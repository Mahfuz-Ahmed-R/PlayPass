<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Get match_id and stadium_id from URL or parent
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
    <title>Stadium Seating Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="stadium-layout.css">
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
                
                <!-- Football Pitch -->
                <rect class="pitch" x="300" y="100" width="400" height="500" rx="5" fill="url(#grass-gradient)"/>
                
                <!-- Pitch markings -->
                <g class="pitch-lines">
                    <rect x="300" y="100" width="400" height="500" rx="5"/>
                    <line x1="500" y1="100" x2="500" y2="600"/>
                    <circle cx="500" cy="350" r="50"/>
                    <circle cx="500" cy="350" r="2"/>
                    
                    <!-- Top goal area -->
                    <rect x="400" y="100" width="200" height="60"/>
                    <rect x="450" y="100" width="100" height="30"/>
                    <path d="M 450 160 Q 500 180 550 160"/>
                    
                    <!-- Bottom goal area -->
                    <rect x="400" y="540" width="200" height="60"/>
                    <rect x="450" y="570" width="100" height="30"/>
                    <path d="M 450 540 Q 500 520 550 540"/>
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
        <h5>Selected Seats <span id="seat-count" class="badge bg-secondary">0/5</span></h5>
        <div id="selected-seats-list"></div>
        <hr>
        <div class="d-flex justify-content-between">
            <strong>Total:</strong>
            <strong id="total-price">$0</strong>
        </div>
        <p class="text-muted small mt-2 mb-0">Seats will be locked for 3 minutes after adding to cart</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Configuration from PHP
        const matchId = <?php echo json_encode($matchId); ?>;
        const stadiumId = <?php echo json_encode($stadiumId); ?>;
        const sessionId = <?php echo json_encode($sessionId); ?>;
        // Get user_id from localStorage (as per user requirement)
        const userId = localStorage.getItem('user_id') || <?php echo json_encode($userId); ?>;
        const apiUrl = '../../../../Backend/PHP/seats-back.php';
        
        // Stadium configuration - will be loaded from database
        let sections = {
            top: [],
            right: [],
            bottom: [],
            left: []
        };
        
        let rowsPerSection = {};
        let seatsPerRow = 10;
        const selectedSeats = new Map(); // Map of seatId -> {holdId, expiresAt, timer}
        const occupiedSeats = new Set(); // Booked seats (red)
        const heldSeats = new Set(); // Current user's held seats (yellow)
        const otherUsersHeldSeats = new Set(); // Other users' held seats (red/occupied)
        
        // Category pricing (will be updated from parent)
        let ticketPrices = {};
        
        // Category assignment to rows (VIP = rows 1-2, Regular = rows 3-4, Economy = rows 5+)
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

        const pitchBounds = {
            left: 300,
            top: 100,
            width: 400,
            height: 500,
            get right() {
                return this.left + this.width;
            },
            get bottom() {
                return this.top + this.height;
            }
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
        
        // Load stadium layout from database
        async function loadStadiumLayout() {
            console.log('Loading stadium layout with:', {
                matchId: matchId,
                stadiumId: stadiumId,
                apiUrl: apiUrl
            });
            
            if (!stadiumId) {
                console.warn('Stadium ID not provided, using default layout');
                loadDefaultLayout();
                return;
            }
            
            if (!matchId) {
                console.warn('Match ID not provided. Seats will not be filtered by match.');
            }
            
            try {
                const response = await fetch(`${apiUrl}?action=getStadiumLayout&stadium_id=${stadiumId}&match_id=${matchId || ''}&user_id=${userId || ''}&session_id=${sessionId || ''}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    // Process sections data
                    ticketPrices = data.prices;
                    const sectionsData = data.sections || {};
                    const allSections = Object.keys(sectionsData);
                    
                    // Use allSections from backend response if available, otherwise extract from sections object
                    const sectionsFromBackend = data.allSections || allSections;
                    
                    // Organize sections by position - use all sections from database
                    const normalizeSection = (s) => {
                        const match = String(s).match(/(\d+\s+)?([A-Z]+)/i);
                        return match ? match[2].toUpperCase() : String(s).toUpperCase().trim();
                    };
                    
                    // Get all normalized sections
                    const normalizedSections = sectionsFromBackend.map(s => ({
                        original: s,
                        normalized: normalizeSection(s)
                    }));
                    
                    // Organize by position
                    sections.right = normalizedSections
                        .filter(s => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'].includes(s.normalized))
                        .map(s => s.original);
                    sections.bottom = normalizedSections
                        .filter(s => ['N'].includes(s.normalized))
                        .map(s => s.original);
                    sections.left = normalizedSections
                        .filter(s => ['V', 'U', 'T', 'S', 'R', 'Q', 'P', 'O'].includes(s.normalized))
                        .map(s => s.original);
                    
                    // Top sections: First two from left + Last two from right
                    const leftFirstTwo = sections.left.slice(0, Math.min(2, sections.left.length));
                    const rightLastTwo = sections.right.slice(-Math.min(2, sections.right.length));
                    sections.top = [...leftFirstTwo, ...rightLastTwo];
                    
                    // rowsPerSection from backend contains max row number per section
                    // Convert to the format needed: section -> number of rows
                    rowsPerSection = {};
                    Object.keys(sectionsData).forEach(section => {
                        // Get the actual max row number from the sections data
                        const rows = Object.keys(sectionsData[section]).map(r => parseInt(r));
                        rowsPerSection[section] = rows.length > 0 ? Math.max(...rows) : 0;
                    });
                    
                    // Override with backend data if available (it has the correct max row numbers)
                    if (data.rowsPerSection) {
                        Object.keys(data.rowsPerSection).forEach(section => {
                            rowsPerSection[section] = data.rowsPerSection[section];
                        });
                    }
                    
                    // Get max seats per row - use the actual count from database
                    const allSeatsPerRow = Object.values(data.seatsPerRow || {});
                    seatsPerRow = allSeatsPerRow.length > 0 ? Math.max(...allSeatsPerRow) : 10;
                    
                    // Store the full seatsPerRow data for use in openZoomView
                    window.stadiumData = {
                        seatsPerRow: data.seatsPerRow || {},
                        sections: sectionsData,
                        rowsPerSection: rowsPerSection
                    };
                    
                    // Note: bookedSeats are now included in heldSeats (so they stay yellow)
                    // We don't add them to occupiedSeats since they should remain yellow until match_seat.status = 'available'
                    // If you need to show booked seats as red (occupied) for admin purposes, you can add them here
                    // For now, keeping them out of occupiedSeats so they stay yellow via heldSeats
                    
                    // Mark held seats - separate current user's seats (yellow) from other users' seats (red)
                    if (data.heldSeats && Array.isArray(data.heldSeats)) {
                        console.log('Loading held seats from database:', data.heldSeats);
                        heldSeats.clear();
                        otherUsersHeldSeats.clear();
                        
                        data.heldSeats.forEach(seat => {
                            // Use seat_id_formatted if available, otherwise construct from seat_id
                            const seatIdFormatted = seat.seat_id_formatted || seat.seat_id;
                            if (seatIdFormatted) {
                                // Normalize the seat ID format to ensure consistency
                                const normalizedSeatId = String(seatIdFormatted).trim();
                                
                                // Check if this seat belongs to current user
                                const seatUserId = seat.user_id ? parseInt(seat.user_id) : null;
                                const seatSessionId = seat.session_id || null;
                                
                                // Determine if this is the current user's seat
                                // Priority: user_id match is more reliable than session_id match
                                // Only consider it the current user's seat if:
                                // 1. Both userId and seatUserId are available AND they match, OR
                                // 2. userId is not available AND sessionId matches seatSessionId
                                let isCurrentUserSeat = false;
                                
                                if (userId && seatUserId) {
                                    // Both user IDs available - check if they match
                                    isCurrentUserSeat = parseInt(userId) === seatUserId;
                                } else if (!userId && sessionId && seatSessionId) {
                                    // No user ID available, fall back to session ID comparison
                                    isCurrentUserSeat = sessionId === seatSessionId;
                                }
                                
                                if (isCurrentUserSeat) {
                                    // Current user's seat - show as yellow (held)
                                    heldSeats.add(normalizedSeatId);
                                    console.log('Added current user held seat (yellow):', normalizedSeatId, 'userId:', userId, 'seatUserId:', seatUserId);
                                } else {
                                    // Other user's seat - show as red (occupied)
                                    otherUsersHeldSeats.add(normalizedSeatId);
                                    occupiedSeats.add(normalizedSeatId);
                                    console.log('Added other user held seat (red):', normalizedSeatId, 'userId:', userId, 'seatUserId:', seatUserId);
                                }
                            } else {
                                console.warn('Held seat missing seat_id_formatted:', seat);
                            }
                        });
                        console.log('Total held seats - Current user (yellow):', heldSeats.size, 'Other users (red):', otherUsersHeldSeats.size);
                    } else {
                        console.log('No held seats data received or data is not an array:', data.heldSeats);
                    }
                    
                    drawStadium();
                    updateSelectionInfo();
                } else {
                    console.error('Failed to load stadium layout:', data.message);
                    // Fallback to default configuration
                    loadDefaultLayout();
                }
            } catch (error) {
                console.error('Error loading stadium layout:', error);
                // Fallback to default configuration
                loadDefaultLayout();
            }
        }
        
        // Fallback to default layout if database fails
        function loadDefaultLayout() {
            sections = {
                top: ['X', 'Y', 'Z'],
                right: ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'],
                bottom: ['N'],
                left: ['V', 'U', 'T', 'S', 'R', 'Q', 'P', 'O']
            };
            
            rowsPerSection = {
                'X': 6, 'Y': 6, 'Z': 6,
                'A': 3, 'B': 3, 'C': 3, 'D': 3, 'E': 3, 'F': 3, 'G': 3, 'H': 3, 'I': 3, 'J': 3, 'K': 3,
                'N': 5,
                'V': 4, 'U': 4, 'T': 4, 'S': 4, 'R': 4, 'Q': 4, 'P': 4, 'O': 4
            };
            
            seatsPerRow = 10;
            drawStadium();
            updateSelectionInfo();
        }
        
        // Parse seat ID for football format: SectionRow-SeatNumber (e.g., "A1-5")
        function parseSeatId(seatId) {
            const match = seatId.match(/^([A-Z]+)(\d+)-(\d+)$/);
            if (match) {
                return {
                    section: match[1],
                    row: parseInt(match[2]),
                    seatNumber: parseInt(match[3])
                };
            }
            return null;
        }
        
        // Calculate layout positions
        function calculateLayoutPositions() {
            const topRowCounts = sections.top.map(section => rowsPerSection[section] || 0);
            const maxTopRows = topRowCounts.length ? Math.max(...topRowCounts) : 0;
            const topClusterHeight = maxTopRows > 0 ? topRowHeight + (maxTopRows - 1) * topRowSpacing : 0;
            const topBaseY = pitchBounds.top - topClusterHeight - 20;
            
            // Exclude sections moved to top: first 2 from left, last 2 from right
            const rightCount = Math.max(0, sections.right.length - 2);
            const leftCount = Math.max(0, sections.left.length - 2);
            const sideSpacingRight = rightCount > 1
                ? (pitchBounds.height - sideRowHeight) / (rightCount - 1)
                : 0;
            const rightTrackSpan = rightCount > 0
                ? sideRowHeight + sideSpacingRight * (rightCount - 1)
                : 0;
            const rightStartY = pitchBounds.top + (pitchBounds.height - rightTrackSpan) / 2;
            
            const sideSpacingLeft = leftCount > 1
                ? (pitchBounds.height - sideRowHeight) / (leftCount - 1)
                : 0;
            const leftTrackSpan = leftCount > 0
                ? sideRowHeight + sideSpacingLeft * (leftCount - 1)
                : 0;
            const leftStartY = pitchBounds.top + (pitchBounds.height - leftTrackSpan) / 2;
            
            const baseRightX = pitchBounds.right + 25;
            const baseLeftX = pitchBounds.left - 30;
            const bottomBaseY = pitchBounds.bottom + 20;
            
            return {
                topBaseY, rightStartY, leftStartY, baseRightX, baseLeftX, bottomBaseY,
                sideSpacingRight, sideSpacingLeft
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
            if (!(panTarget === svgElement || panTarget.classList.contains('pitch'))) {
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
            
            // Top sections
            const topSectionsTotalWidth = sections.top.length
                ? sections.top.length * topRowWidth +
                  Math.max(0, sections.top.length - 1) * topRowGap
                : 0;
            let xPos = pitchBounds.left + (pitchBounds.width - topSectionsTotalWidth) / 2;
            sections.top.forEach((section, idx) => {
                const maxRow = rowsPerSection[section] || 0;
                if (maxRow === 0) return;
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
            
            // Right sections (excluding last two which are now in top)
            const rightSectionsForSide = sections.right.length > 2 ? sections.right.slice(0, -2) : [];
            rightSectionsForSide.forEach((section, idx) => {
                const maxRow = rowsPerSection[section] || 0;
                const sectionY = positions.rightStartY + idx * positions.sideSpacingRight;
                for (let i = 0; i < maxRow; i++) {
                    const row = i + 1;
                    const category = getCategoryForRow(section, row);
                    const rowColor = getColorForCategory(category);
                    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    rect.setAttribute('class', 'section-row');
                    rect.setAttribute('x', positions.baseRightX + i * (sideRowWidth + sideRowGap));
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
                    const textX = positions.baseRightX + i * (sideRowWidth + sideRowGap) + sideRowWidth / 2;
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
            
            // Bottom sections
            sections.bottom.forEach((section, idx) => {
                const maxRow = rowsPerSection[section] || 0;
                const totalWidth = maxRow * bottomRowWidth + Math.max(0, maxRow - 1) * bottomRowGap;
                let xPos = pitchBounds.left + (pitchBounds.width - totalWidth) / 2;
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
            
            // Left sections (excluding first two which are now in top)
            const leftSectionsForSide = sections.left.length > 2 ? sections.left.slice(2) : [];
            leftSectionsForSide.forEach((section, idx) => {
                const maxRow = rowsPerSection[section] || 0;
                const sectionY = positions.leftStartY + idx * positions.sideSpacingLeft;
                for (let i = 0; i < maxRow; i++) {
                    const row = i + 1;
                    const category = getCategoryForRow(section, row);
                    const rowColor = getColorForCategory(category);
                    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    rect.setAttribute('class', 'section-row');
                    rect.setAttribute('x', positions.baseLeftX - i * (sideRowWidth + sideRowGap));
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
                    const textX = positions.baseLeftX - i * (sideRowWidth + sideRowGap) + sideRowWidth / 2;
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
        
        async function openZoomView(section, row) {
            const overlay = document.getElementById('overlay');
            const zoomView = document.getElementById('zoom-view');
            const zoomSvg = document.getElementById('zoom-svg');
            const sectionTitle = document.getElementById('section-title');
            
            // Store current zoom view info for refresh
            currentZoomSection = section;
            currentZoomRow = row;
            
            sectionTitle.textContent = `Section ${section} - Row ${row}`;
            zoomSvg.innerHTML = '';
            
            // Refresh held seats from backend before drawing
            if (matchId && stadiumId) {
                try {
                    const response = await fetch(`${apiUrl}?action=getStadiumLayout&stadium_id=${stadiumId}&match_id=${matchId}&user_id=${userId || ''}&session_id=${sessionId || ''}`);
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success && data.heldSeats && Array.isArray(data.heldSeats)) {
                            // Update held seats Sets with latest data from backend
                            heldSeats.clear();
                            otherUsersHeldSeats.clear();
                            data.heldSeats.forEach(seat => {
                                const seatIdFormatted = seat.seat_id_formatted || seat.seat_id;
                                if (seatIdFormatted) {
                                    // Normalize seat ID
                                    const normalizedSeatId = String(seatIdFormatted).trim();
                                    
                                    // Check if this seat belongs to current user
                                    const seatUserId = seat.user_id ? parseInt(seat.user_id) : null;
                                    const seatSessionId = seat.session_id || null;
                                    const isCurrentUserSeat = (userId && seatUserId && parseInt(userId) === seatUserId) ||
                                                              (sessionId && seatSessionId && sessionId === seatSessionId);
                                    
                                    if (isCurrentUserSeat) {
                                        heldSeats.add(normalizedSeatId);
                                    } else {
                                        otherUsersHeldSeats.add(normalizedSeatId);
                                        occupiedSeats.add(normalizedSeatId);
                                    }
                                }
                            });
                            console.log('Refreshed held seats for zoom view - Current user (yellow):', heldSeats.size, 'Other users (red):', otherUsersHeldSeats.size);
                        }
                    }
                } catch (error) {
                    console.error('Error refreshing held seats:', error);
                }
            }
            
            // Refresh seat states before drawing (in case selections changed)
            console.log('Opening zoom view for:', section, row, 'Selected seats:', Array.from(selectedSeats.keys()), 'Held seats:', Array.from(heldSeats));
            
            // Get actual number of seats for this specific row from database data
            let actualSeatsInRow = 10; // Default fallback
            const rowKey = String(section).toUpperCase().trim() + String(row);
            
            // Try to get from seatsPerRow
            if (window.stadiumData && window.stadiumData.seatsPerRow && window.stadiumData.seatsPerRow[rowKey]) {
                actualSeatsInRow = window.stadiumData.seatsPerRow[rowKey];
            } else if (window.stadiumData && window.stadiumData.sections) {
                // Fallback: Count seats from sectionsData
                const sectionData = window.stadiumData.sections[section];
                if (sectionData && sectionData[row]) {
                    actualSeatsInRow = sectionData[row].length;
                }
            }
            
            const seatSize = 35;
            const spacing = 12;
            const startX = 100;
            const startY = 125;
            
            for (let i = 0; i < actualSeatsInRow; i++) {
                const seatId = `${section}${row}-${i + 1}`;
                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('class', 'seat');
                circle.setAttribute('cx', startX + i * (seatSize + spacing));
                circle.setAttribute('cy', startY);
                circle.setAttribute('r', seatSize / 2);
                circle.setAttribute('data-seat-id', seatId);
                
                // Normalize seat ID for comparison
                const normalizedSeatId = String(seatId).trim();
                
                // Check seat status and apply appropriate class
                // Current user's held seats = YELLOW, Other users' held seats = RED
                if (occupiedSeats.has(normalizedSeatId) || otherUsersHeldSeats.has(normalizedSeatId)) {
                    // Other users' held seats or booked seats = RED (occupied)
                    circle.classList.add('occupied');
                    circle.classList.remove('available', 'selected');
                    if (otherUsersHeldSeats.has(normalizedSeatId)) {
                        circle.setAttribute('title', 'Seat is held by another user');
                    }
                } else if (selectedSeats.has(normalizedSeatId) || heldSeats.has(normalizedSeatId)) {
                    // Current user's held or selected seats = YELLOW (selected class)
                    circle.classList.add('selected');
                    circle.classList.remove('available', 'occupied');
                    if (heldSeats.has(normalizedSeatId) && !selectedSeats.has(normalizedSeatId)) {
                        circle.setAttribute('title', 'Seat is locked (in your cart, reserved, or purchased)');
                    }
                } else {
                    // Available seats = GREEN (available class) - only when match_seat.status = 'available'
                    circle.classList.add('available');
                    circle.classList.remove('selected', 'occupied');
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
            currentZoomSection = null;
            currentZoomRow = null;
        }
        
        // Permanently hold seat (called when Add to Cart is clicked)
        async function permanentlyHoldSeat(seatId) {
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
                
                console.log('Permanently hold seat response:', data);
                
                if (data.success) {
                    const expiresAt = new Date(data.expires_at);
                    const now = new Date();
                    const timeLeft = Math.max(0, expiresAt - now);
                    
                    // Update seat selection with hold information
                    const seatData = selectedSeats.get(seatId);
                    if (seatData) {
                        seatData.holdId = data.hold_id;
                        seatData.expiresAt = expiresAt;
                        seatData.timer = timeLeft > 0 ? setTimeout(() => releaseSeat(seatId), timeLeft) : null;
                    } else {
                        // If seat not in selectedSeats, add it
                        selectedSeats.set(seatId, {
                            holdId: data.hold_id,
                            expiresAt: expiresAt,
                            timer: timeLeft > 0 ? setTimeout(() => releaseSeat(seatId), timeLeft) : null
                        });
                    }
                    
                    // Normalize seat ID and add to held seats
                    const normalizedSeatId = String(seatId).trim();
                    heldSeats.add(normalizedSeatId);
                    
                    // Update all seat elements with this seat ID to show YELLOW (selected class)
                    document.querySelectorAll(`[data-seat-id="${seatId}"], [data-seat-id="${normalizedSeatId}"]`).forEach(el => {
                        if (!occupiedSeats.has(normalizedSeatId)) {
                            el.classList.remove('available', 'occupied');
                            el.classList.add('selected'); // Yellow color
                        }
                    });
                    
                    console.log('Seat permanently held successfully:', seatId, 'Hold ID:', data.hold_id);
                    return true;
                } else {
                    // Show detailed error message (including match_id mismatch errors)
                    const errorMsg = data.message || 'Failed to hold seat';
                    console.error('Seat hold failed:', data);
                    if (data.debug) {
                        console.error('Debug info:', data.debug);
                    }
                    // Error message will be shown via alert and returned to parent
                    return { success: false, message: errorMsg };
                }
            } catch (error) {
                console.error('Error holding seat:', error);
                alert('Network error: ' + error.message);
                return false;
            }
        }
        
        // Release seat (only for permanently held seats)
        async function releaseSeat(seatId) {
            const seatData = selectedSeats.get(seatId);
            if (!seatData || !seatData.holdId) {
                // Not permanently held, just remove from selection
                const normalizedSeatId = String(seatId).trim();
                selectedSeats.delete(seatId);
                selectedSeats.delete(normalizedSeatId);
                updateSelectionInfo();
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('action', 'releaseSeat');
                formData.append('hold_id', seatData.holdId);
                formData.append('match_id', matchId);
                formData.append('session_id', sessionId);
                
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    // Show alert if seat isn't released (e.g., match_id mismatch)
                    const errorMsg = data.message || 'Failed to release seat';
                    alert(errorMsg);
                    console.error('Seat release failed:', data);
                    return;
                }
                
                // Remove from selectedSeats and heldSeats
                const normalizedSeatId = String(seatId).trim();
                selectedSeats.delete(seatId);
                selectedSeats.delete(normalizedSeatId);
                heldSeats.delete(seatId);
                heldSeats.delete(normalizedSeatId);
                
                // Update UI
                document.querySelectorAll(`[data-seat-id="${seatId}"], [data-seat-id="${normalizedSeatId}"]`).forEach(el => {
                    if (!occupiedSeats.has(normalizedSeatId) && !occupiedSeats.has(seatId)) {
                        el.classList.remove('selected', 'occupied');
                        el.classList.add('available'); // Green color
                    }
                });
                
                updateSelectionInfo();
            } catch (error) {
                console.error('Error releasing seat:', error);
                alert('Network error while releasing seat. Please try again.');
                return;
            }
            
            if (seatData.timer) {
                clearTimeout(seatData.timer);
            }
            const normalizedSeatId = String(seatId).trim();
            selectedSeats.delete(seatId);
            selectedSeats.delete(normalizedSeatId);
            heldSeats.delete(seatId);
            heldSeats.delete(normalizedSeatId);
            
            // Update all seat elements with this seat ID to show GREEN (available class)
            document.querySelectorAll(`[data-seat-id="${seatId}"], [data-seat-id="${normalizedSeatId}"]`).forEach(el => {
                if (!occupiedSeats.has(normalizedSeatId) && !occupiedSeats.has(seatId)) {
                    el.classList.remove('selected', 'occupied');
                    el.classList.add('available'); // Green color
                }
            });
            
            updateSelectionInfo();
            
            // Notify parent
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
            const normalizedSeatId = String(seatId).trim();
            
            if (occupiedSeats.has(normalizedSeatId) || occupiedSeats.has(seatId)) {
                alert('This seat is already booked');
                return;
            }
            
            // Prevent selecting seats held by other users (they show as red/occupied)
            if ((otherUsersHeldSeats.has(normalizedSeatId) || otherUsersHeldSeats.has(seatId)) ||
                (occupiedSeats.has(normalizedSeatId) || occupiedSeats.has(seatId))) {
                alert('This seat is currently held by another user or is already booked.');
                return;
            }
            
            const parsed = parseSeatId(seatId);
            if (!parsed) return;
            
            const { section, row, seatNumber } = parsed;
            const category = getCategoryForRow(section, row);
            const price = getPriceForSeat(section, row);
            
            const isSelected = selectedSeats.has(normalizedSeatId) || selectedSeats.has(seatId);
            
            if (isSelected) {
                // Deselect seat (temporary selection only - no database call if not permanently held)
                const seatData = selectedSeats.get(seatId) || selectedSeats.get(normalizedSeatId);
                if (seatData && seatData.holdId) {
                    // If seat is permanently held, release it
                    await releaseSeat(seatId);
                } else {
                    // Just remove from temporary selection
                    selectedSeats.delete(seatId);
                    selectedSeats.delete(normalizedSeatId);
                    
                    // Update visual state
                    document.querySelectorAll(`[data-seat-id="${seatId}"], [data-seat-id="${normalizedSeatId}"]`).forEach(el => {
                        if (!occupiedSeats.has(normalizedSeatId) && !occupiedSeats.has(seatId) && !heldSeats.has(normalizedSeatId) && !heldSeats.has(seatId)) {
                            el.classList.remove('selected', 'occupied');
                            el.classList.add('available'); // Green color
                        }
                    });
                    
                    // Notify parent
                    if (window.parent) {
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
            } else {
                // Check 5-seat limit
                if (selectedSeats.size >= 5) {
                    alert('You can select a maximum of 5 seats at a time. Please remove a seat or add current selection to cart.');
                    return;
                }
                
                // Temporary selection only (no database call - will be held when Add to Cart is clicked)
                selectedSeats.set(seatId, {
                    holdId: null, // Will be set when Add to Cart is clicked
                    expiresAt: null,
                    timer: null
                });
                
                // Update visual state
                if (element) {
                    element.classList.remove('available', 'occupied');
                    element.classList.add('selected'); // Yellow color
                } else {
                    const seatElement = document.querySelector(`[data-seat-id="${seatId}"], [data-seat-id="${normalizedSeatId}"]`);
                    if (seatElement) {
                        seatElement.classList.remove('available', 'occupied');
                        seatElement.classList.add('selected'); // Yellow color
                    }
                }
                
                // Update all seat elements with this seat ID (in case zoom view is open)
                document.querySelectorAll(`[data-seat-id="${seatId}"], [data-seat-id="${normalizedSeatId}"]`).forEach(el => {
                    if (!occupiedSeats.has(normalizedSeatId) && !occupiedSeats.has(seatId)) {
                        el.classList.remove('available', 'occupied');
                        el.classList.add('selected'); // Yellow color
                    }
                });
                
                // Notify parent
                if (window.parent) {
                    window.parent.postMessage({
                        type: 'seatSelection',
                        seatId: seatId,
                        section: section,
                        row: row,
                        seatNumber: seatNumber,
                        category: category,
                        price: price,
                        isSelected: true,
                        holdId: null, // Not permanently held yet
                        expiresAt: null
                    }, '*');
                }
            }
            
            updateSelectionInfo();
        }
        
        function updateSelectionInfo() {
            const selectionInfo = document.querySelector('.selection-info');
            const list = document.getElementById('selected-seats-list');
            const total = document.getElementById('total-price');
            const seatCount = document.getElementById('seat-count');
            
            // Show/hide selection info panel
            if (selectedSeats.size === 0) {
                selectionInfo.style.display = 'none';
                list.innerHTML = '<p class="text-muted">No seats selected</p>';
            } else {
                selectionInfo.style.display = 'block';
                
                // Update seat count badge
                seatCount.textContent = `${selectedSeats.size}/5`;
                if (selectedSeats.size >= 5) {
                    seatCount.className = 'badge bg-warning';
                } else {
                    seatCount.className = 'badge bg-secondary';
                }
                
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
                
                total.textContent = `$${totalPrice.toFixed(2)}`;
            }
        }
        
        // Listen for messages from parent window
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
                
                // Release all selected seats when category changes
                Array.from(selectedSeats.keys()).forEach(seatId => {
                    releaseSeat(seatId);
                });
            } else if (event.data && event.data.type === 'holdSeat') {
                // Permanently hold a seat (called when Add to Cart is clicked)
                const seatId = event.data.seatId;
                permanentlyHoldSeat(seatId).then(result => {
                    // Get hold information from selectedSeats
                    const seatData = selectedSeats.get(seatId);
                    const success = result === true || (result && result.success === true);
                    const errorMessage = (result && result.message) || null;
                    
                    // Notify parent of result
                    if (window.parent) {
                        window.parent.postMessage({
                            type: 'holdSeatResult',
                            seatId: seatId,
                            success: success,
                            message: errorMessage,
                            holdId: seatData ? seatData.holdId : null,
                            expiresAt: seatData && seatData.expiresAt ? seatData.expiresAt.toISOString() : null
                        }, '*');
                    }
                });
            } else if (event.data && event.data.type === 'clearSelections') {
                // Clear UI selections but KEEP holds active in database (for 3-minute lock)
                // Move seats from selectedSeats to heldSeats so they remain visually locked
                selectedSeats.forEach((seatData, seatId) => {
                    // Keep the seat in heldSeats so it remains visually locked
                    const normalizedSeatId = String(seatId).trim();
                    heldSeats.add(normalizedSeatId);
                    // Clear any timers since the seat is now in cart
                    if (seatData.timer) {
                        clearTimeout(seatData.timer);
                    }
                });
                // Clear the selectedSeats map but keep heldSeats for visual indication
                selectedSeats.clear();
                updateSelectionInfo();
                // Redraw to show held seats as locked
                drawStadium();
            } else if (event.data && event.data.type === 'markPurchasedSeats') {
                // After purchase, seats should remain yellow (held) until match_seat.status changes to 'available'
                // Don't mark them as occupied - they will be shown as held (yellow) via the heldSeats from backend
                const purchasedSeats = Array.isArray(event.data.seats) ? event.data.seats : [];
                
                // Add purchased seats to heldSeats so they remain yellow
                purchasedSeats.forEach(seatId => {
                    const normalizedSeatId = String(seatId).trim();
                    heldSeats.add(normalizedSeatId);
                });
                
                // Remove from selectedSeats but keep in heldSeats for visual indication
                Array.from(selectedSeats.keys()).forEach(seatId => {
                    const normalizedSeatId = String(seatId).trim();
                    if (purchasedSeats.includes(seatId) || purchasedSeats.includes(normalizedSeatId)) {
                        // Keep the seat in heldSeats so it remains visually locked (yellow)
                        heldSeats.add(normalizedSeatId);
                        heldSeats.add(seatId);
                        // Clear any timers since the seat is now purchased
                        const seatData = selectedSeats.get(seatId) || selectedSeats.get(normalizedSeatId);
                        if (seatData && seatData.timer) {
                            clearTimeout(seatData.timer);
                        }
                        selectedSeats.delete(seatId);
                        selectedSeats.delete(normalizedSeatId);
                    }
                });
                
                // Update UI to show purchased seats as yellow (held)
                purchasedSeats.forEach(seatId => {
                    const normalizedSeatId = String(seatId).trim();
                    document.querySelectorAll(`[data-seat-id="${seatId}"], [data-seat-id="${normalizedSeatId}"]`).forEach(el => {
                        el.classList.remove('available', 'occupied');
                        el.classList.add('selected'); // Yellow color
                    });
                });
                
                updateSelectionInfo();
                
                // Refresh held seats from backend to ensure consistency
                refreshHeldSeats();
            }
        });
        
        document.getElementById('close-zoom').addEventListener('click', closeZoomView);
        document.getElementById('overlay').addEventListener('click', closeZoomView);
        
        // Initialize
        loadStadiumLayout();
        
        // Track current zoom view section/row to refresh if needed
        let currentZoomSection = null;
        let currentZoomRow = null;
        
        // Refresh held seats periodically to ensure they stay current
        async function refreshHeldSeats() {
            if (!matchId || !stadiumId) return;
            
            try {
                const response = await fetch(`${apiUrl}?action=getStadiumLayout&stadium_id=${stadiumId}&match_id=${matchId}&user_id=${userId || ''}&session_id=${sessionId || ''}`);
                if (!response.ok) return;
                
                const data = await response.json();
                if (data.success && data.heldSeats && Array.isArray(data.heldSeats)) {
                    // Clear current held seats and reload from database
                    const currentHeldSeats = new Set(heldSeats);
                    const currentOtherUsersHeldSeats = new Set(otherUsersHeldSeats);
                    heldSeats.clear();
                    otherUsersHeldSeats.clear();
                    
                    data.heldSeats.forEach(seat => {
                        const seatIdFormatted = seat.seat_id_formatted || seat.seat_id;
                        if (seatIdFormatted) {
                            // Normalize seat ID to ensure consistent matching
                            const normalizedSeatId = String(seatIdFormatted).trim();
                            
                            // Check if this seat belongs to current user
                            const seatUserId = seat.user_id ? parseInt(seat.user_id) : null;
                            const seatSessionId = seat.session_id || null;
                            const isCurrentUserSeat = (userId && seatUserId && parseInt(userId) === seatUserId) ||
                                                      (sessionId && seatSessionId && sessionId === seatSessionId);
                            
                            if (isCurrentUserSeat) {
                                heldSeats.add(normalizedSeatId);
                            } else {
                                otherUsersHeldSeats.add(normalizedSeatId);
                                occupiedSeats.add(normalizedSeatId);
                            }
                        }
                    });
                    
                    // If held seats changed, update UI
                    const heldSeatsChanged = heldSeats.size !== currentHeldSeats.size || 
                        Array.from(heldSeats).some(id => !currentHeldSeats.has(id)) ||
                        Array.from(currentHeldSeats).some(id => !heldSeats.has(id)) ||
                        otherUsersHeldSeats.size !== currentOtherUsersHeldSeats.size ||
                        Array.from(otherUsersHeldSeats).some(id => !currentOtherUsersHeldSeats.has(id)) ||
                        Array.from(currentOtherUsersHeldSeats).some(id => !otherUsersHeldSeats.has(id));
                    
                    if (heldSeatsChanged) {
                        console.log('Held seats updated. New count:', heldSeats.size, 'Held seats:', Array.from(heldSeats));
                        
                        // Redraw stadium overview
                        drawStadium();
                        
                        // If zoom view is open, refresh it to show updated held seats
                        if (currentZoomSection && currentZoomRow) {
                            const zoomView = document.getElementById('zoom-view');
                            if (zoomView && zoomView.classList.contains('active')) {
                                // Close and reopen zoom view to refresh
                                closeZoomView();
                                setTimeout(() => {
                                    openZoomView(currentZoomSection, currentZoomRow);
                                }, 100);
                            }
                        }
                        
                        // Update all seat elements in the DOM
                        document.querySelectorAll('[data-seat-id]').forEach(el => {
                            const seatId = el.getAttribute('data-seat-id');
                            if (!seatId) return;
                            
                            // Remove all status classes first
                            el.classList.remove('available', 'selected', 'occupied');
                            
                            // Apply correct class based on current status
                            // Current user's held seats = YELLOW, Other users' held seats = RED
                            const normalizedSeatId = String(seatId).trim();
                            if (occupiedSeats.has(seatId) || occupiedSeats.has(normalizedSeatId) ||
                                otherUsersHeldSeats.has(seatId) || otherUsersHeldSeats.has(normalizedSeatId)) {
                                // Other users' held seats or booked seats = RED (occupied)
                                el.classList.add('occupied');
                            } else if (selectedSeats.has(seatId) || selectedSeats.has(normalizedSeatId) || 
                                      heldSeats.has(seatId) || heldSeats.has(normalizedSeatId)) {
                                // Current user's held or selected seats = YELLOW
                                el.classList.add('selected'); // Yellow color
                            } else {
                                // Available seats = GREEN - only when match_seat.status = 'available'
                                el.classList.add('available'); // Green color
                            }
                        });
                    }
                }
            } catch (error) {
                console.error('Error refreshing held seats:', error);
            }
        }
        
        // Cleanup expired holds and refresh held seats periodically
        setInterval(async () => {
            try {
                // First cleanup expired holds in database
                await fetch(`${apiUrl}?action=cleanupExpiredHolds`);
                // Then refresh held seats to reflect changes
                await refreshHeldSeats();
                
                // Also cleanup expired cart items from localStorage
                if (window.cartFunctions && typeof window.cartFunctions.cleanupExpiredCartItems === 'function') {
                    const cartChanged = window.cartFunctions.cleanupExpiredCartItems();
                    if (cartChanged) {
                        // Update cart count if items were removed
                        if (window.cartFunctions.updateCartCount) {
                            window.cartFunctions.updateCartCount();
                        }
                    }
                } else {
                    // Fallback: cleanup localStorage cart directly
                    try {
                        const localCart = JSON.parse(localStorage.getItem("cart") || "[]");
                        if (localCart && localCart.length > 0) {
                            const now = Date.now();
                            const BOOKING_TIMEOUT = 3 * 60 * 1000; // 3 minutes
                            const validCart = localCart.filter(item => {
                                if (item.seats && item.seats.some(seat => {
                                    if (seat.expiresAt) {
                                        return new Date(seat.expiresAt).getTime() > now;
                                    }
                                    return item.addedAt && (item.addedAt + BOOKING_TIMEOUT) > now;
                                })) {
                                    return true;
                                }
                                return item.addedAt && (item.addedAt + BOOKING_TIMEOUT) > now;
                            });
                            if (validCart.length !== localCart.length) {
                                localStorage.setItem("cart", JSON.stringify(validCart));
                                // Update cart count if available
                                if (window.cartFunctions && window.cartFunctions.updateCartCount) {
                                    window.cartFunctions.updateCartCount();
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Error cleaning up cart:', e);
                    }
                }
            } catch (error) {
                console.error('Error cleaning up expired holds:', error);
            }
        }, 10000); // Every 10 seconds - frequent enough to catch expired holds quickly
    </script>
</body>
</html>

