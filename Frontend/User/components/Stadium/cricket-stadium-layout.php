<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$matchId = $_GET['match_id'] ?? null;
$stadiumId = $_GET['stadium_id'] ?? null;
$sessionId = session_id();
$userId = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cricket Stadium Seating Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="cricket-stadium-layout.css">
</head>
<body>
    <div class="container-fluid">
        <div id="stadium-container">
            <svg id="stadium-svg" viewBox="0 0 1000 700" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="grass-gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#4CAF50;stop-opacity:1" />
                        <stop offset="50%" style="stop-color:#66BB6A;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#4CAF50;stop-opacity:1" />
                    </linearGradient>
                </defs>
                
                <ellipse class="field" cx="500" cy="350" rx="450" ry="300"/>
                
                <ellipse class="field-lines" cx="500" cy="350" rx="450" ry="300"/>
                
                <rect class="pitch" x="450" y="250" width="100" height="200" rx="2"/>
                
                <g class="pitch-lines">
                    <rect x="450" y="250" width="100" height="200" rx="2"/>
                    <line x1="500" y1="250" x2="500" y2="450"/>
                    <line x1="480" y1="250" x2="520" y2="250" stroke-width="3"/>
                    <line x1="485" y1="250" x2="485" y2="240" stroke-width="2"/>
                    <line x1="495" y1="250" x2="495" y2="240" stroke-width="2"/>
                    <line x1="505" y1="250" x2="505" y2="240" stroke-width="2"/>
                    <line x1="515" y1="250" x2="515" y2="240" stroke-width="2"/>
                    <line x1="480" y1="450" x2="520" y2="450" stroke-width="3"/>
                    <line x1="485" y1="450" x2="485" y2="460" stroke-width="2"/>
                    <line x1="495" y1="450" x2="495" y2="460" stroke-width="2"/>
                    <line x1="505" y1="450" x2="505" y2="460" stroke-width="2"/>
                    <line x1="515" y1="450" x2="515" y2="460" stroke-width="2"/>
                    <line x1="440" y1="250" x2="440" y2="270"/>
                    <line x1="560" y1="250" x2="560" y2="270"/>
                    <line x1="440" y1="270" x2="560" y2="270"/>
                    <line x1="440" y1="450" x2="440" y2="430"/>
                    <line x1="560" y1="450" x2="560" y2="430"/>
                    <line x1="440" y1="430" x2="560" y2="430"/>
                    <line x1="430" y1="260" x2="570" y2="260" stroke-dasharray="5,5"/>
                    <line x1="430" y1="440" x2="570" y2="440" stroke-dasharray="5,5"/>
                </g>
            </svg>
        </div>
    </div>
    
    <div class="overlay" id="overlay"></div>
    
    <div id="zoom-view">
        <button class="close-btn" id="close-zoom">&times;</button>
        <h4 id="section-title" class="mb-3">Section Details</h4>
        <svg id="zoom-svg" width="100%" height="200" viewBox="0 0 600 250"></svg>
    </div>
    
    <div class="selection-info" style="display: none;">
        <h5>Selected Seats</h5>
        <div id="selected-seats-list"></div>
        <hr>
        <div class="d-flex justify-content-between">
            <strong>Total:</strong>
            <strong id="total-price">$0</strong>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const matchId = <?php echo json_encode($matchId); ?>;
        const stadiumId = <?php echo json_encode($stadiumId); ?>;
        const sessionId = <?php echo json_encode($sessionId); ?>;
        const userId = <?php echo json_encode($userId); ?>;
        const apiUrl = '../../../../Backend/PHP/seats-back.php';
        
        let sections = {
            north: [],
            south: [],
            east: [],
            west: []
        };
        
        let rowsPerSection = {};
        let seatsPerRow = 10;
        const selectedSeats = new Map(); // Map of seatId -> {holdId, expiresAt, timer}
        const occupiedSeats = new Set(); // Booked seats
        const heldSeats = new Set(); // Temporarily held seats (current user's)
        const otherUsersHeldSeats = new Set(); // Seats held by other users (should show as red)
        
        let ticketPrices = {
            VIP: 150,
            Regular: 75,
            Economy: 35
        };
        
        function getCategoryForRow(section, row) {
            if (row <= 2) return 'VIP';
            if (row <= 4) return 'Regular';
            return 'Economy';
        }
        
        function getPriceForSeat(section, row) {
            const category = getCategoryForRow(section, row);
            return ticketPrices[category] || 50;
        }
        
        let currentCategory = 'VIP';

        const svgElement = document.getElementById('stadium-svg');
        const originalViewBox = svgElement.getAttribute('viewBox').split(' ').map(Number);
        const aspectRatio = originalViewBox[2] / originalViewBox[3];
        const viewBoxState = {
            x: originalViewBox[0],
            y: originalViewBox[1],
            width: originalViewBox[2],
            height: originalViewBox[3]
        };
        const minViewBoxWidth = 350;
        const maxViewBoxWidth = originalViewBox[2] * 1.5;

        const clampPadding = 200;
        const zoomBounds = {
            minX: originalViewBox[0] - clampPadding,
            minY: originalViewBox[1] - clampPadding,
            maxX: originalViewBox[0] + originalViewBox[2] + clampPadding,
            maxY: originalViewBox[1] + originalViewBox[3] + clampPadding
        };

        const fieldBounds = {
            centerX: 500,
            centerY: 350,
            radiusX: 450,
            radiusY: 300,
            get left() { return this.centerX - this.radiusX; },
            get right() { return this.centerX + this.radiusX; },
            get top() { return this.centerY - this.radiusY; },
            get bottom() { return this.centerY + this.radiusY; }
        };

        const pitchBounds = {
            left: 450,
            top: 250,
            width: 100,
            height: 200,
            get right() { return this.left + this.width; },
            get bottom() { return this.top + this.height; }
        };

        const topRowHeight = 18;
        const topRowSpacing = topRowHeight + 10;
        const topRowWidth = 140;
        const topRowGap = 25;
        const sideRowWidth = 20;
        const sideRowHeight = 70;
        const sideRowGap = 12;
        const bottomRowHeight = 18;
        const bottomRowWidth = 80;
        const bottomRowGap = 18;
        
        async function loadStadiumLayout() {
            if (!stadiumId) {
                console.warn('Stadium ID not provided, using default layout');
                loadDefaultLayout();
                return;
            }
            
            try {
                const response = await fetch(`${apiUrl}?action=getStadiumLayout&stadium_id=${stadiumId}&match_id=${matchId || ''}&user_id=${userId || ''}&session_id=${sessionId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    const sectionsData = data.sections || {};
                    const allSections = Object.keys(sectionsData);
                    
                    const sectionsFromBackend = data.allSections || allSections;
                    
                    const normalizeSection = (s) => {
                        const match = String(s).match(/(\d+\s+)?([A-Z]+)/i);
                        return match ? match[2].toUpperCase() : String(s).toUpperCase().trim();
                    };
                    
                    sections.north = sectionsFromBackend.filter(s => {
                        const normalized = normalizeSection(s);
                        return normalized.startsWith('N');
                    });
                    sections.south = sectionsFromBackend.filter(s => {
                        const normalized = normalizeSection(s);
                        return normalized.startsWith('S');
                    });
                    sections.east = sectionsFromBackend.filter(s => {
                        const normalized = normalizeSection(s);
                        return normalized.startsWith('E');
                    });
                    sections.west = sectionsFromBackend.filter(s => {
                        const normalized = normalizeSection(s);
                        return normalized.startsWith('W');
                    });
                    
                    rowsPerSection = {};
                    Object.keys(sectionsData).forEach(section => {
                        const rows = Object.keys(sectionsData[section]).map(r => parseInt(r));
                        rowsPerSection[section] = rows.length > 0 ? Math.max(...rows) : 0;
                    });
                    
                    if (data.rowsPerSection) {
                        Object.keys(data.rowsPerSection).forEach(section => {
                            rowsPerSection[section] = data.rowsPerSection[section];
                        });
                    }
                    
                    const allSeatsPerRow = Object.values(data.seatsPerRow || {});
                    seatsPerRow = allSeatsPerRow.length > 0 ? Math.max(...allSeatsPerRow) : 10;
                    
                    window.stadiumData = {
                        seatsPerRow: data.seatsPerRow || {},
                        sections: sectionsData,
                        rowsPerSection: rowsPerSection
                    };
                    
                    if (data.bookedSeats) {
                        data.bookedSeats.forEach(seatId => occupiedSeats.add(seatId));
                    }
                    
                    if (data.heldSeats && Array.isArray(data.heldSeats)) {
                        data.heldSeats.forEach(seat => {
                            const seatIdFormatted = normalizeSeatId(seat.seat_id_formatted || seat.seat_id);
                            if (!seatIdFormatted) return;
                            const seatUserId = seat.user_id ? String(seat.user_id) : null;
                            const seatSessionId = seat.session_id ? String(seat.session_id) : null;
                            const currentUserId = userId ? String(userId) : null;
                            const currentSessionId = sessionId ? String(sessionId) : null;
                            
                            const isCurrentUser = (seatUserId && currentUserId && seatUserId === currentUserId) || 
                                                 (seatSessionId && currentSessionId && seatSessionId === currentSessionId);
                            
                            if (isCurrentUser) {
                                heldSeats.add(seatIdFormatted);
                                console.log('Loaded current user held seat:', seatIdFormatted, 'user_id:', seatUserId, 'session_id:', seatSessionId);
                            } else {
                                otherUsersHeldSeats.add(seatIdFormatted);
                                console.log('Loaded other user held seat:', seatIdFormatted, 'seat_user_id:', seatUserId, 'current_user_id:', currentUserId, 'seat_session:', seatSessionId, 'current_session:', currentSessionId);
                            }
                        });
                    }
                    
                    drawStadium();
                    updateSelectionInfo();
                } else {
                    console.error('Failed to load stadium layout:', data.message);
                    loadDefaultLayout();
                }
            } catch (error) {
                console.error('Error loading stadium layout:', error);
                loadDefaultLayout();
            }
        }
        
        function loadDefaultLayout() {
            sections = {
                north: ['N1', 'N2', 'N3', 'N4'],
                south: ['S1', 'S2', 'S3', 'S4'],
                east: ['E1', 'E2', 'E3', 'E4', 'E5', 'E6'],
                west: ['W1', 'W2', 'W3', 'W4', 'W5', 'W6']
            };
            
            rowsPerSection = {
                'N1': 5, 'N2': 5, 'N3': 5, 'N4': 5,
                'S1': 5, 'S2': 5, 'S3': 5, 'S4': 5,
                'E1': 4, 'E2': 4, 'E3': 4, 'E4': 4, 'E5': 4, 'E6': 4,
                'W1': 4, 'W2': 4, 'W3': 4, 'W4': 4, 'W5': 4, 'W6': 4
            };
            
            seatsPerRow = 10;
            drawStadium();
            updateSelectionInfo();
        }
        
        function parseSeatId(seatId) {
            const match = seatId.match(/^([A-Z]+\d+)(\d+)-(\d+)$/);
            if (match) {
                return {
                    section: match[1],
                    row: parseInt(match[2]), 
                    seatNumber: parseInt(match[3]) 
                };
            }
            return null;
        }
        
        function normalizeSeatId(seatId) {
            if (!seatId) return null;
            return String(seatId).trim();
        }
        
        function calculateLayoutPositions() {
            const topRowCounts = sections.north.map(section => rowsPerSection[section] || 0);
            const maxTopRows = topRowCounts.length ? Math.max(...topRowCounts) : 0;
            const topClusterHeight = maxTopRows > 0 ? topRowHeight + (maxTopRows - 1) * topRowSpacing : 0;
            const topBaseY = fieldBounds.top - topClusterHeight - 20;
            
            const eastCount = sections.east.length;
            const westCount = sections.west.length;
            const sideSpacingEast = eastCount > 1
                ? (fieldBounds.bottom - fieldBounds.top - sideRowHeight) / (eastCount - 1)
                : 0;
            const eastTrackSpan = eastCount > 0
                ? sideRowHeight + sideSpacingEast * (eastCount - 1)
                : 0;
            const eastStartY = fieldBounds.top + (fieldBounds.bottom - fieldBounds.top - eastTrackSpan) / 2;
            
            const sideSpacingWest = westCount > 1
                ? (fieldBounds.bottom - fieldBounds.top - sideRowHeight) / (westCount - 1)
                : 0;
            const westTrackSpan = westCount > 0
                ? sideRowHeight + sideSpacingWest * (westCount - 1)
                : 0;
            const westStartY = fieldBounds.top + (fieldBounds.bottom - fieldBounds.top - westTrackSpan) / 2;
            
            const baseEastX = fieldBounds.right + 25;
            const baseWestX = fieldBounds.left - 30;
            const bottomBaseY = fieldBounds.bottom + 20;
            
            return {
                topBaseY, eastStartY, westStartY, baseEastX, baseWestX, bottomBaseY,
                sideSpacingEast, sideSpacingWest
            };
        }
        
        function applyViewBox() {
            svgElement.setAttribute('viewBox', `${viewBoxState.x} ${viewBoxState.y} ${viewBoxState.width} ${viewBoxState.height}`);
        }

        function clampViewBox() {
            const maxX = Math.max(zoomBounds.minX, zoomBounds.maxX - viewBoxState.width);
            const maxY = Math.max(zoomBounds.minY, zoomBounds.maxY - viewBoxState.height);

            viewBoxState.x = Math.min(Math.max(viewBoxState.x, zoomBounds.minX), maxX);
            viewBoxState.y = Math.min(Math.max(viewBoxState.y, zoomBounds.minY), maxY);
        }

        function performZoom(centerX, centerY, scaleFactor) {
            const svgRect = svgElement.getBoundingClientRect();
            const pointerX = (centerX - svgRect.left) / svgRect.width;
            const pointerY = (centerY - svgRect.top) / svgRect.height;

            const currentWidth = viewBoxState.width;
            const currentHeight = viewBoxState.height;
            const pointerSVGX = viewBoxState.x + pointerX * currentWidth;
            const pointerSVGY = viewBoxState.y + pointerY * currentHeight;

            let targetWidth = viewBoxState.width * scaleFactor;
            targetWidth = Math.min(Math.max(targetWidth, minViewBoxWidth), maxViewBoxWidth);
            const targetHeight = targetWidth / aspectRatio;

            viewBoxState.width = targetWidth;
            viewBoxState.height = targetHeight;
            viewBoxState.x = pointerSVGX - pointerX * targetWidth;
            viewBoxState.y = pointerSVGY - pointerY * targetHeight;

            clampViewBox();
            applyViewBox();
        }

        svgElement.addEventListener('wheel', (event) => {
            event.preventDefault();
            const zoomOut = event.deltaY > 0;
            const scaleFactor = zoomOut ? 1.1 : 0.9;
            performZoom(event.clientX, event.clientY, scaleFactor);
        }, { passive: false });

        let isPanning = false;
        let activePointerId = null;
        const panState = { startX: 0, startY: 0, viewBoxX: 0, viewBoxY: 0 };

        function startPan(event) {
            if (event.button !== 0) return;
            const panTarget = event.target;
            if (!(panTarget === svgElement || panTarget.classList.contains('field') || panTarget.classList.contains('pitch'))) {
                return;
            }

            isPanning = true;
            activePointerId = event.pointerId;
            svgElement.classList.add('panning');
            svgElement.setPointerCapture(activePointerId);
            panState.startX = event.clientX;
            panState.startY = event.clientY;
            panState.viewBoxX = viewBoxState.x;
            panState.viewBoxY = viewBoxState.y;
        }

        function movePan(event) {
            if (!isPanning || event.pointerId !== activePointerId) return;

            const svgRect = svgElement.getBoundingClientRect();
            const deltaX = (event.clientX - panState.startX) / svgRect.width * viewBoxState.width;
            const deltaY = (event.clientY - panState.startY) / svgRect.height * viewBoxState.height;

            viewBoxState.x = panState.viewBoxX - deltaX;
            viewBoxState.y = panState.viewBoxY - deltaY;

            clampViewBox();
            applyViewBox();
        }

        function endPan(event) {
            if (!isPanning || event.pointerId !== activePointerId) return;

            isPanning = false;
            svgElement.classList.remove('panning');
            if (svgElement.hasPointerCapture(activePointerId)) {
                svgElement.releasePointerCapture(activePointerId);
            }
            activePointerId = null;
        }

        svgElement.addEventListener('pointerdown', startPan);
        svgElement.addEventListener('pointermove', movePan);
        svgElement.addEventListener('pointerup', endPan);
        svgElement.addEventListener('pointerleave', endPan);

        applyViewBox();

        function getColorForCategory(category) {
            switch(category) {
                case 'VIP': return '#FFD700';
                case 'Regular': return '#28a745';
                case 'Economy': return '#0d6efd';
                default: return '#e0e0e0';
            }
        }
        
        // Draw stadium sections
        function drawStadium() {
            const svg = document.getElementById('stadium-svg');
            const positions = calculateLayoutPositions();
            
            // North sections (top)
            const northSectionsTotalWidth = sections.north.length
                ? sections.north.length * topRowWidth +
                  Math.max(0, sections.north.length - 1) * topRowGap
                : 0;
            let xPos = fieldBounds.centerX - northSectionsTotalWidth / 2;
            sections.north.forEach((section, idx) => {
                const maxRow = rowsPerSection[section] || 0;
                for (let i = 0; i < maxRow; i++) {
                    const row = i + 1;
                    const category = getCategoryForRow(section, row);
                    const rowColor = getColorForCategory(category);
                    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    rect.setAttribute('class', 'section-row');
                    rect.setAttribute('x', xPos);
                    rect.setAttribute('y', positions.topBaseY + i * topRowSpacing);
                    rect.setAttribute('width', topRowWidth);
                    rect.setAttribute('height', topRowHeight);
                    rect.setAttribute('fill', rowColor);
                    rect.setAttribute('stroke', '#333');
                    rect.setAttribute('stroke-width', '0.5');
                    rect.setAttribute('data-section', section);
                    rect.setAttribute('data-row', row);
                    rect.setAttribute('data-category', category);
                    
                    const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    text.setAttribute('x', xPos + topRowWidth / 2);
                    text.setAttribute('y', positions.topBaseY + i * topRowSpacing + topRowHeight / 2);
                    text.setAttribute('font-size', '10');
                    text.setAttribute('fill', '#333');
                    text.setAttribute('text-anchor', 'middle');
                    text.setAttribute('dominant-baseline', 'middle');
                    text.textContent = `${section}${row}`;
                    text.style.pointerEvents = 'none';
                    
                    rect.addEventListener('click', () => openZoomView(section, row));
                    
                    svg.appendChild(rect);
                    svg.appendChild(text);
                }
                xPos += topRowWidth + topRowGap;
            });
            
            // East sections (right)
            sections.east.forEach((section, idx) => {
                const maxRow = rowsPerSection[section] || 0;
                const sectionY = positions.eastStartY + idx * positions.sideSpacingEast;
                for (let i = 0; i < maxRow; i++) {
                    const row = i + 1;
                    const category = getCategoryForRow(section, row);
                    const rowColor = getColorForCategory(category);
                    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    rect.setAttribute('class', 'section-row');
                    rect.setAttribute('x', positions.baseEastX + i * (sideRowWidth + sideRowGap));
                    rect.setAttribute('y', sectionY);
                    rect.setAttribute('width', sideRowWidth);
                    rect.setAttribute('height', sideRowHeight);
                    rect.setAttribute('fill', rowColor);
                    rect.setAttribute('stroke', '#333');
                    rect.setAttribute('stroke-width', '0.5');
                    rect.setAttribute('data-section', section);
                    rect.setAttribute('data-row', row);
                    rect.setAttribute('data-category', category);
                    
                    const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    const textX = positions.baseEastX + i * (sideRowWidth + sideRowGap) + sideRowWidth / 2;
                    const textY = sectionY + sideRowHeight / 2;
                    text.setAttribute('x', textX);
                    text.setAttribute('y', textY);
                    text.setAttribute('font-size', '10');
                    text.setAttribute('fill', '#333');
                    text.setAttribute('text-anchor', 'middle');
                    text.setAttribute('dominant-baseline', 'middle');
                    text.setAttribute('dy', '0.35em');
                    text.setAttribute('transform', `rotate(90, ${textX}, ${textY})`);
                    text.textContent = `${section}${row}`;
                    text.style.pointerEvents = 'none';
                    
                    rect.addEventListener('click', () => openZoomView(section, row));
                    
                    svg.appendChild(rect);
                    svg.appendChild(text);
                }
            });
            
            // South sections (bottom)
            sections.south.forEach((section, idx) => {
                const maxRow = rowsPerSection[section] || 0;
                const totalWidth = maxRow * bottomRowWidth + Math.max(0, maxRow - 1) * bottomRowGap;
                let xPos = fieldBounds.centerX - totalWidth / 2;
                for (let i = 0; i < maxRow; i++) {
                    const row = i + 1;
                    const category = getCategoryForRow(section, row);
                    const rowColor = getColorForCategory(category);
                    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    rect.setAttribute('class', 'section-row');
                    rect.setAttribute('x', xPos + i * (bottomRowWidth + bottomRowGap));
                    rect.setAttribute('y', positions.bottomBaseY);
                    rect.setAttribute('width', bottomRowWidth);
                    rect.setAttribute('height', bottomRowHeight);
                    rect.setAttribute('fill', rowColor);
                    rect.setAttribute('stroke', '#333');
                    rect.setAttribute('stroke-width', '0.5');
                    rect.setAttribute('data-section', section);
                    rect.setAttribute('data-row', row);
                    rect.setAttribute('data-category', category);
                    
                    const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    text.setAttribute('x', xPos + i * (bottomRowWidth + bottomRowGap) + bottomRowWidth / 2);
                    text.setAttribute('y', positions.bottomBaseY + bottomRowHeight / 2);
                    text.setAttribute('font-size', '10');
                    text.setAttribute('fill', '#333');
                    text.setAttribute('text-anchor', 'middle');
                    text.setAttribute('dominant-baseline', 'middle');
                    text.textContent = `${section}${row}`;
                    text.style.pointerEvents = 'none';
                    
                    rect.addEventListener('click', () => openZoomView(section, row));
                    
                    svg.appendChild(rect);
                    svg.appendChild(text);
                }
            });
            
            // West sections (left)
            sections.west.forEach((section, idx) => {
                const maxRow = rowsPerSection[section] || 0;
                const sectionY = positions.westStartY + idx * positions.sideSpacingWest;
                for (let i = 0; i < maxRow; i++) {
                    const row = i + 1;
                    const category = getCategoryForRow(section, row);
                    const rowColor = getColorForCategory(category);
                    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    rect.setAttribute('class', 'section-row');
                    rect.setAttribute('x', positions.baseWestX - i * (sideRowWidth + sideRowGap));
                    rect.setAttribute('y', sectionY);
                    rect.setAttribute('width', sideRowWidth);
                    rect.setAttribute('height', sideRowHeight);
                    rect.setAttribute('fill', rowColor);
                    rect.setAttribute('stroke', '#333');
                    rect.setAttribute('stroke-width', '0.5');
                    rect.setAttribute('data-section', section);
                    rect.setAttribute('data-row', row);
                    rect.setAttribute('data-category', category);
                    
                    const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    const textX = positions.baseWestX - i * (sideRowWidth + sideRowGap) + sideRowWidth / 2;
                    const textY = sectionY + sideRowHeight / 2;
                    text.setAttribute('x', textX);
                    text.setAttribute('y', textY);
                    text.setAttribute('font-size', '10');
                    text.setAttribute('fill', '#333');
                    text.setAttribute('text-anchor', 'middle');
                    text.setAttribute('dominant-baseline', 'middle');
                    text.setAttribute('dy', '0.35em');
                    text.setAttribute('transform', `rotate(90, ${textX}, ${textY})`);
                    text.textContent = `${section}${row}`;
                    text.style.pointerEvents = 'none';
                    
                    rect.addEventListener('click', () => openZoomView(section, row));
                    
                    svg.appendChild(rect);
                    svg.appendChild(text);
                }
            });
        }
        
        function openZoomView(section, row) {
            const overlay = document.getElementById('overlay');
            const zoomView = document.getElementById('zoom-view');
            const zoomSvg = document.getElementById('zoom-svg');
            const sectionTitle = document.getElementById('section-title');
            
            sectionTitle.textContent = `Section ${section} - Row ${row}`;
            zoomSvg.innerHTML = '';
            
            const seatSize = 35;
            const spacing = 12;
            const startX = 100;
            const startY = 125;
            
            let actualSeatsInRow = 10; // Default fallback
            const rowKey = String(section).toUpperCase().trim() + String(row);
            
            if (window.stadiumData && window.stadiumData.seatsPerRow && window.stadiumData.seatsPerRow[rowKey]) {
                actualSeatsInRow = window.stadiumData.seatsPerRow[rowKey];
            } else if (window.stadiumData && window.stadiumData.sections) {
                const sectionData = window.stadiumData.sections[section];
                if (sectionData && sectionData[row]) {
                    actualSeatsInRow = sectionData[row].length;
                }
            }
            
            console.log(`Opening zoom view for ${section}${row}, seats in row: ${actualSeatsInRow}`);
            
            for (let i = 0; i < actualSeatsInRow; i++) {
                const seatId = `${section}${row}-${i + 1}`;
                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('class', 'seat');
                circle.setAttribute('cx', startX + i * (seatSize + spacing));
                circle.setAttribute('cy', startY);
                circle.setAttribute('r', seatSize / 2);
                circle.setAttribute('data-seat-id', seatId);
                
                if (occupiedSeats.has(seatId) || otherUsersHeldSeats.has(seatId)) {
                    circle.classList.add('occupied');
                } else if (selectedSeats.has(seatId) || heldSeats.has(seatId)) {
                    circle.classList.add('selected');
                } else {
                    circle.classList.add('available');
                }
                
                circle.addEventListener('click', (e) => toggleSeat(seatId, e.target));
                
                const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                text.setAttribute('x', startX + i * (seatSize + spacing));
                text.setAttribute('y', startY + 6);
                text.setAttribute('text-anchor', 'middle');
                text.setAttribute('font-size', '14');
                text.setAttribute('fill', 'white');
                text.setAttribute('font-weight', 'bold');
                text.textContent = i + 1;
                text.style.pointerEvents = 'none';
                
                zoomSvg.appendChild(circle);
                zoomSvg.appendChild(text);
            }
            
            overlay.classList.add('active');
            zoomView.classList.add('active');
        }
        
        function closeZoomView() {
            document.getElementById('overlay').classList.remove('active');
            document.getElementById('zoom-view').classList.remove('active');
        }
        
        async function selectSeat(seatId) {
            const parsed = parseSeatId(seatId);
            if (!parsed) return false;
            
            const { section, row, seatNumber } = parsed;
            
            try {
                const formData = new FormData();
                formData.append('action', 'selectSeat');
                formData.append('match_id', matchId);
                formData.append('section', section);
                formData.append('row', row);
                formData.append('seat_number', seatNumber);
                formData.append('user_id', userId);
                formData.append('session_id', sessionId);
                
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const expiresAt = new Date(data.expires_at);
                    const now = new Date();
                    const timeLeft = expiresAt - now;
                    
                    selectedSeats.set(seatId, {
                        holdId: data.hold_id,
                        expiresAt: expiresAt,
                        timer: setTimeout(() => releaseSeat(seatId), timeLeft)
                    });
                    
                    heldSeats.add(seatId);
                    return true;
                } else {
                    alert(data.message || 'Failed to select seat');
                    return false;
                }
            } catch (error) {
                console.error('Error selecting seat:', error);
                return false;
            }
        }
        
        async function releaseSeat(seatId) {
            const seatData = selectedSeats.get(seatId);
            if (!seatData) return;
            
            try {
                const formData = new FormData();
                formData.append('action', 'releaseSeat');
                formData.append('hold_id', seatData.holdId);
                formData.append('match_id', matchId);
                formData.append('session_id', sessionId);
                
                await fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                });
            } catch (error) {
                console.error('Error releasing seat:', error);
            }
            
            clearTimeout(seatData.timer);
            selectedSeats.delete(seatId);
            heldSeats.delete(seatId);
            otherUsersHeldSeats.delete(seatId);
            
            const element = document.querySelector(`[data-seat-id="${seatId}"]`);
            if (element && !occupiedSeats.has(seatId) && !otherUsersHeldSeats.has(seatId)) {
                element.classList.remove('selected', 'occupied');
                element.classList.add('available');
            }
            
            updateSelectionInfo();
            
            if (window.parent) {
                const parsed = parseSeatId(seatId);
                if (parsed) {
                    const { section, row, seatNumber } = parsed;
                    const category = getCategoryForRow(section, row);
                    const price = getPriceForSeat(section, row);
                    
                    window.parent.postMessage({
                        type: 'seatSelection',
                        seatId: seatId,
                        section: section,
                        row: row,
                        seatNumber: seatNumber,
                        category: category,
                        price: price,
                        isSelected: false
                    }, '*');
                }
            }
        }
        
        async function toggleSeat(seatId, element) {
            if (occupiedSeats.has(seatId)) {
                return;
            }
            
            if (otherUsersHeldSeats.has(seatId) || occupiedSeats.has(seatId)) {
                alert('This seat is currently unavailable. Please select another seat.');
                return;
            }
            
            const parsed = parseSeatId(seatId);
            if (!parsed) return;
            
            const { section, row, seatNumber } = parsed;
            const category = getCategoryForRow(section, row);
            const price = getPriceForSeat(section, row);
            
            const isSelected = selectedSeats.has(seatId);
            
            if (isSelected) {
                await releaseSeat(seatId);
            } else {
                // Check 5-seat limit
                if (selectedSeats.size >= 5) {
                    alert('You can select a maximum of 5 seats at a time. Please remove a seat or add current selection to cart.');
                    return;
                }
                
                const success = await selectSeat(seatId);
                if (success) {
                    // Update visual immediately
                    element.classList.remove('available', 'occupied');
                    element.classList.add('selected');
                    
                    otherUsersHeldSeats.delete(seatId);
                    
                    // Notify parent
                    if (window.parent) {
                        const seatData = selectedSeats.get(seatId);
                        window.parent.postMessage({
                            type: 'seatSelection',
                            seatId: seatId,
                            section: section,
                            row: row,
                            seatNumber: seatNumber,
                            category: category,
                            price: price,
                            isSelected: true,
                            holdId: seatData ? seatData.holdId : null,
                            expiresAt: seatData ? seatData.expiresAt.toISOString() : null
                        }, '*');
                    }
                    
                    setTimeout(updateSeatStatus, 500);
                }
            }
            
            updateSelectionInfo();
        }
        
        function updateSelectionInfo() {
            const list = document.getElementById('selected-seats-list');
            const total = document.getElementById('total-price');
            
            if (selectedSeats.size === 0) {
                list.innerHTML = '<p class="text-muted">No seats selected</p>';
            } else {
                let totalPrice = 0;
                const seatsList = Array.from(selectedSeats.keys()).map(seatId => {
                    const parsed = parseSeatId(seatId);
                    if (parsed) {
                        const { section, row, seatNumber } = parsed;
                        const category = getCategoryForRow(section, row);
                        const price = getPriceForSeat(section, row);
                        totalPrice += price;
                        return { seatId, section, row, seatNumber, category, price };
                    }
                    return null;
                }).filter(Boolean);
                
                list.innerHTML = '<ul class="list-unstyled mb-0">' +
                    seatsList.map(seat => `<li>${seat.section}${seat.row}-${seat.seatNumber} (${seat.category}) - $${seat.price}</li>`).join('') +
                    '</ul>';
                
                total.textContent = `$${totalPrice}`;
            }
        }
        
        window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'init') {
                if (event.data.prices) {
                    ticketPrices = event.data.prices;
                }
            } else if (event.data && event.data.type === 'categoryChange') {
                currentCategory = event.data.category;
                document.querySelectorAll('.section-row').forEach(row => {
                    row.classList.remove('category-glow');
                });
                document.querySelectorAll(`.section-row[data-category="${currentCategory}"]`).forEach(row => {
                    row.classList.add('category-glow');
                });
                
                Array.from(selectedSeats.keys()).forEach(seatId => {
                    releaseSeat(seatId);
                });
            } else if (event.data && event.data.type === 'clearSelections') {
                selectedSeats.forEach((seatData, seatId) => {
                    heldSeats.add(seatId);
                    if (seatData.timer) {
                        clearTimeout(seatData.timer);
                    }
                });
                selectedSeats.clear();
                updateSelectionInfo();
                drawStadium();
            } else if (event.data && event.data.type === 'markPurchasedSeats') {
                const purchasedSeats = Array.isArray(event.data.seats) ? event.data.seats : [];
                occupiedSeats.clear();
                purchasedSeats.forEach(seatId => occupiedSeats.add(seatId));
                
                Array.from(selectedSeats.keys()).forEach(seatId => {
                    if (occupiedSeats.has(seatId)) {
                        releaseSeat(seatId);
                    }
                });
                
                updateSelectionInfo();
            }
        });
        
        document.getElementById('close-zoom').addEventListener('click', closeZoomView);
        document.getElementById('overlay').addEventListener('click', closeZoomView);
        
        // Initialize
        loadStadiumLayout();
        
        setInterval(async () => {
            try {
                await fetch(`${apiUrl}?action=cleanupExpiredHolds`);
            } catch (error) {
                console.error('Error cleaning up expired holds:', error);
            }
        }, 60000); // Every minute
        
        async function updateSeatStatus() {
            if (!stadiumId || !matchId) {
                console.log('Skipping seat status update - missing stadiumId or matchId');
                return;
            }
            
            try {
                const url = `${apiUrl}?action=getStadiumLayout&stadium_id=${stadiumId}&match_id=${matchId}&user_id=${userId || ''}&session_id=${sessionId}`;
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    console.log('Seat status update received:', {
                        bookedSeats: data.bookedSeats?.length || 0,
                        heldSeats: data.heldSeats?.length || 0,
                        currentUserId: userId,
                        currentSessionId: sessionId
                    });
                    
                    const newBookedSeats = new Set(data.bookedSeats || []);
                    occupiedSeats.clear();
                    newBookedSeats.forEach(seatId => occupiedSeats.add(seatId));
                    
                    const newOtherUsersHeldSeats = new Set();
                    const newCurrentUserHeldSeats = new Set();
                    
                    if (data.heldSeats && Array.isArray(data.heldSeats)) {
                        data.heldSeats.forEach(seat => {
                            const seatIdFormatted = normalizeSeatId(seat.seat_id_formatted || seat.seat_id);
                            if (!seatIdFormatted) return;
                            
                            const seatUserId = seat.user_id ? String(seat.user_id) : null;
                            const seatSessionId = seat.session_id ? String(seat.session_id) : null;
                            const currentUserId = userId ? String(userId) : null;
                            const currentSessionId = sessionId ? String(sessionId) : null;
                            
                            const isCurrentUser = (seatUserId && currentUserId && seatUserId === currentUserId) || 
                                                 (seatSessionId && currentSessionId && seatSessionId === currentSessionId);
                            
                            if (isCurrentUser) {
                                newCurrentUserHeldSeats.add(seatIdFormatted);
                            } else {
                                newOtherUsersHeldSeats.add(seatIdFormatted);
                            }
                        });
                    }
                    
                    const otherUsersChanged = 
                        otherUsersHeldSeats.size !== newOtherUsersHeldSeats.size ||
                        Array.from(otherUsersHeldSeats).some(id => !newOtherUsersHeldSeats.has(id)) ||
                        Array.from(newOtherUsersHeldSeats).some(id => !otherUsersHeldSeats.has(id));
                    
                    if (otherUsersChanged) {
                        console.log('Other users held seats changed:', {
                            old: Array.from(otherUsersHeldSeats),
                            new: Array.from(newOtherUsersHeldSeats)
                        });
                        otherUsersHeldSeats.clear();
                        newOtherUsersHeldSeats.forEach(seatId => otherUsersHeldSeats.add(seatId));
                    }
                    
                    const currentUserHeldSeatsToRemove = new Set();
                    heldSeats.forEach(seatId => {
                        if (!newCurrentUserHeldSeats.has(seatId) && !selectedSeats.has(seatId)) {
                            currentUserHeldSeatsToRemove.add(seatId);
                        }
                    });
                    currentUserHeldSeatsToRemove.forEach(seatId => heldSeats.delete(seatId));
                    newCurrentUserHeldSeats.forEach(seatId => heldSeats.add(seatId));
                    
                    const zoomView = document.getElementById('zoom-view');
                    if (zoomView && zoomView.classList.contains('active')) {
                        const sectionTitle = document.getElementById('section-title');
                        if (sectionTitle) {
                            const match = sectionTitle.textContent.match(/Section\s+([A-Z]+\d+)\s+-\s+Row\s+(\d+)/);
                            if (match) {
                                const section = match[1];
                                const row = parseInt(match[2]);
                                openZoomView(section, row);
                            }
                        }
                    } else {
                        document.querySelectorAll('.seat[data-seat-id]').forEach(element => {
                            const seatId = element.getAttribute('data-seat-id');
                            if (seatId) {
                                element.classList.remove('available', 'selected', 'occupied');
                                
                                if (occupiedSeats.has(seatId) || otherUsersHeldSeats.has(seatId)) {
                                    element.classList.add('occupied');
                                } else if (selectedSeats.has(seatId) || heldSeats.has(seatId)) {
                                    element.classList.add('selected');
                                } else {
                                    element.classList.add('available');
                                }
                            }
                        });
                    }
                }
            } catch (error) {
                console.error('Error updating seat status:', error);
            }
        }
        
        setInterval(updateSeatStatus, 3000);
        
        setTimeout(() => {
            updateSeatStatus();
        }, 2000);
    </script>
</body>
</html>

