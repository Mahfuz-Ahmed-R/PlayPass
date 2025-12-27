const API_CART = '../../../../Backend/PHP/cart-back.php';
const API_USER = '../../../../Backend/PHP/user-back.php';
const API_INIT = '../../../../Backend/PHP/sslcommerz-initiate.php';

function updateAuthButton() {
  const userId = localStorage.getItem('user_id');
  const signInBtn = document.getElementById('signInBtn');
  const accountBtn = document.getElementById('accountBtn');
  if (userId && userId !== 'null' && userId !== 'undefined') {
    if (signInBtn) signInBtn.style.display = 'none';
    if (accountBtn) accountBtn.style.display = 'block';
  } else {
    if (signInBtn) signInBtn.style.display = 'block';
    if (accountBtn) accountBtn.style.display = 'none';
  }
}

async function fetchUser(userId) {
  if (!userId) return null;
  try {
    const res = await fetch(`${API_USER}?action=getUser&user_id=${encodeURIComponent(userId)}`);
    if (!res.ok) throw new Error('Network');
    const data = await res.json();
    return data.success ? data.user : null;
  } catch (e) {
    console.error('fetchUser error', e);
    return null;
  }
}

async function fetchCart(userId) {
  try {
    const res = await fetch(`${API_CART}?action=getCart&user_id=${encodeURIComponent(userId)}`);
    if (!res.ok) throw new Error('Network');
    const data = await res.json();
    return data.success ? data.cart : [];
  } catch (e) {
    console.error('fetchCart error', e);
    return JSON.parse(localStorage.getItem('cart') || '[]');
  }
}

function formatCurrency(val) {
  return '৳' + Number(val).toFixed(2);
}

function renderCartSummary(cart) {
  const totalQty = cart.reduce((s, it) => s + (it.quantity || 0), 0);
  const totalAmount = cart.reduce((s, it) => s + (it.total || 0), 0);
  const first = cart[0] || null;

  document.getElementById('ticketBadge').textContent = `${totalQty} Ticket${totalQty!==1? 's':''}`;
  document.getElementById('ticketTitle').textContent = first ? first.eventTitle : 'Your tickets';
  document.getElementById('ticketPrice').textContent = first ? `৳ ${first.total && first.quantity ? (first.total/first.quantity).toFixed(2) : '0'} per ticket` : '৳ 0 per ticket';
  document.getElementById('subTotal').textContent = formatCurrency(totalAmount);
  document.getElementById('totalAmount').textContent = formatCurrency(totalAmount);

  const cartBody = document.getElementById('cartBody');
  if (!cartBody) return;
  if (cart.length === 0) {
    cartBody.innerHTML = '<p class="text-center py-4">Your cart is empty.</p>';
    return;
  }

  let html = '';
  cart.forEach(item => {
    html += `<div class="cart-item mb-3 p-2 border rounded">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <strong>${item.eventTitle}</strong>
          <div class="text-muted">${item.eventLocation || ''} | ${item.eventDate || ''} ${item.eventTime||''}</div>
        </div>
        <div class="text-end">
          <div>Qty: ${item.quantity || 0}</div>
          <div>Amount: ${formatCurrency(item.total || 0)}</div>
        </div>
      </div>
      <div class="mt-2">Seats: ${item.seats.map(s=> s.seatId).join(', ')}</div>
    </div>`;
  });
  cartBody.innerHTML = html;
}

async function removeHold(holdId, userId) {
  try {
    const form = new FormData();
    form.append('action', 'removeFromCart');
    form.append('hold_id', holdId);
    form.append('user_id', userId || '');
    const res = await fetch(API_CART, { method: 'POST', body: form });
    return await res.json();
  } catch (e) {
    console.error('removeHold error', e);
    return { success: false };
  }
}

async function deleteAll(cart, userId) {
  if (!confirm('Remove all items from cart?')) return;
  for (const item of cart) {
    if (!item.seats) continue;
    for (const seat of item.seats) {
      if (seat.holdId) {
        await removeHold(seat.holdId, userId);
      }
    }
  }
  if (window.cartFunctions && typeof window.cartFunctions.updateCartCount === 'function') {
    window.cartFunctions.updateCartCount();
  }
  await loadAndRender();
}

async function initiatePayment(payload) {
  try {
    const res = await fetch(API_INIT, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    return await res.json();
  } catch (e) {
    console.error('initiatePayment error', e);
    return { success: false, message: 'Network error' };
  }
}

async function loadAndRender() {
  updateAuthButton();
  const userId = localStorage.getItem('user_id');
  const [user, cart] = await Promise.all([fetchUser(userId), fetchCart(userId)]);

  if (user) {
    document.getElementById('attendeeName').textContent = user.name || '--';
    const emailEl = document.getElementById('attendeeEmail');
    emailEl.textContent = user.email || '--';
    emailEl.href = 'mailto:' + (user.email || '');
    document.getElementById('attendeePhone').textContent = user.phone || '--';
  }

  renderCartSummary(cart || []);

  const delAll = document.getElementById('deleteAllBtn');
  if (delAll) {
    delAll.onclick = () => deleteAll(cart || [], userId);
  }

  const proceed = document.getElementById('proceedBtn');
  if (proceed) {
    proceed.onclick = async () => {
      const termsChecked = document.getElementById('terms').checked;
      if (!termsChecked) return alert('Please agree to the Terms & Conditions');
      if (!cart || cart.length === 0) return alert('Your cart is empty.');

      const payload = { user_id: userId, cart };
      const resp = await initiatePayment(payload);
      if (resp.success && resp.redirect_url) {
        window.location.href = resp.redirect_url;
      } else if (resp.success) {
        alert('Payment initiated. ' + (resp.message || ''));
        localStorage.removeItem('cart');
        if (window.cartFunctions && typeof window.cartFunctions.updateCartCount === 'function') {
          window.cartFunctions.updateCartCount();
        }
        await loadAndRender();
      } else {
        alert('Payment error: ' + (resp.message || 'Unable to initiate payment'));
      }
    };
  }
}

document.addEventListener('DOMContentLoaded', loadAndRender);

function includeHTML(id, file) {
  return fetch(file).then(r => r.text()).then(html => { const el = document.getElementById(id); if (el) el.innerHTML = html; }).catch(() => {});
}

includeHTML('navbar', '../../components/Navbar/navbar.html').then(() => {
  if (!document.querySelector('script[src*="components/Cart/cart.js"]') && !window.cartFunctions) {
    const script = document.createElement('script');
    script.src = '../../components/Cart/cart.js';
    script.async = true;
    document.body.appendChild(script);
  }
});
includeHTML('responsive_navbar', '../../components/Responsive_Navbar/responsive_navbar.html');
includeHTML('footer', '../../components/Footer/footer.html');

function loadCSS(file) { const link = document.createElement('link'); link.rel = 'stylesheet'; link.href = file; document.head.appendChild(link); }
loadCSS('../../components/Navbar/navbar.css');
loadCSS('../../components/Responsive_Navbar/responsive_navbar.css');
loadCSS('../../components/Footer/footer.css');
