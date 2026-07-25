/* ==========================================================================
   ADD BAG PAGE – JavaScript
   Cart data lives in MySQL now (see config.php / cart_*.php). This file
   talks to those endpoints instead of localStorage. Product catalogue for
   "Recently Viewed" comes from window.COCO_PRODUCTS, injected by add-bag.php.
   ========================================================================== */
document.addEventListener('DOMContentLoaded', () => {

  const VIEWED_KEY = 'coco_recently_viewed';
  const PRODUCTS = window.COCO_PRODUCTS || [];

  /* ---------- Helpers ---------- */
  const fmt = n => '₹' + Number(n).toLocaleString('en-IN');
  const qs  = s => document.querySelector(s);
  const qsa = s => document.querySelectorAll(s);

  function postForm(url, params) {
    const body = new URLSearchParams(params);
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString(),
    }).then(res => res.json());
  }

  /* ---------- Loading Spinner ---------- */
  const overlay = qs('#loading-overlay');
  if (overlay) {
    setTimeout(() => overlay.classList.add('hidden'), 600);
  }

  /* ---------- Header Scroll ---------- */
  const header = qs('.ab-header');
  window.addEventListener('scroll', () => {
    if (header) header.classList.toggle('scrolled', window.scrollY > 40);
  });

  /* ---------- Mobile Nav Toggle ---------- */
  const menuToggle = qs('#ab-menu-toggle');
  const nav = qs('#ab-nav');
  if (menuToggle && nav) {
    menuToggle.addEventListener('click', () => nav.classList.toggle('active'));
    qsa('.ab-nav-link').forEach(l => l.addEventListener('click', () => nav.classList.remove('active')));
  }

  /* ---------- Apply totals returned from the server to the summary card ---------- */
  function applyTotals(totals) {
    const el = id => qs('#' + id);
    if (!totals) return;
    if (el('summary-subtotal'))     el('summary-subtotal').textContent     = fmt(totals.subtotal);
    if (el('summary-discount'))     el('summary-discount').textContent     = '-' + fmt(totals.discount);
    if (el('summary-gst'))          el('summary-gst').textContent          = fmt(totals.gst);
    if (el('summary-shipping'))     el('summary-shipping').textContent     = totals.shipping === 0 ? 'FREE' : fmt(totals.shipping);
    if (el('summary-grand-total'))  el('summary-grand-total').textContent  = fmt(totals.grand_total);
    if (el('checkout-grand-total')) el('checkout-grand-total').textContent = fmt(totals.grand_total);

    const discountRow = qs('.ab-discount-row');
    if (discountRow) discountRow.style.display = totals.discount > 0 ? 'flex' : 'none';
  }

  /* ---------- Cart Interactions (Delegated), talk to PHP/MySQL then refresh the page ---------- */
  document.addEventListener('click', e => {
    const t = e.target;

    if (t.classList.contains('ab-qty-minus') || t.classList.contains('ab-qty-plus')) {
      const cartId = t.dataset.cartId;
      const action = t.classList.contains('ab-qty-minus') ? 'decrease' : 'increase';
      postForm('cart_update.php', { cart_id: cartId, action }).then(data => {
        if (data.success) {
          const row = document.querySelector('.ab-cart-item[data-cart-id="' + cartId + '"]');
          if (row) {
            const qtyEl = row.querySelector('.ab-qty-value');
            const totalEl = row.querySelector('.ab-item-total');
            const priceEl = row.querySelector('.ab-item-price');
            if (qtyEl) qtyEl.textContent = data.quantity;
            if (totalEl && priceEl) {
              const price = parseInt(priceEl.textContent.replace(/[^\d]/g, ''), 10);
              totalEl.textContent = fmt(price * data.quantity);
            }
          }
          applyTotals(data.totals);
          const badge = qs('#cart-count');
          if (badge) badge.textContent = data.cart_count;
        }
      });
    }

    if (t.classList.contains('ab-remove-btn')) {
      const cartId = t.dataset.cartId;
      postForm('cart_remove.php', { cart_id: cartId }).then(data => {
        if (data.success) {
          const row = document.querySelector('.ab-cart-item[data-cart-id="' + cartId + '"]');
          if (row) row.remove();
          applyTotals(data.totals);
          const badge = qs('#cart-count');
          if (badge) badge.textContent = data.cart_count;
          if (!document.querySelector('.ab-cart-item')) {
            const emptyDiv = qs('#empty-cart');
            const summaryCol = qs('#summary-col');
            if (emptyDiv) emptyDiv.style.display = 'block';
            if (summaryCol) summaryCol.style.display = 'none';
          }
        }
      });
    }

    if (t.classList.contains('ab-wishlist-btn')) {
      t.textContent = '♥ Saved'; t.style.color = '#E74C3C'; t.style.borderColor = '#E74C3C';
    }
  });

  /* ---------- Coupon System ---------- */
  const couponInput = qs('#coupon-input');
  const couponMsg   = qs('#coupon-msg');
  const applyBtn    = qs('#apply-coupon-btn');

  if (applyBtn) {
    applyBtn.addEventListener('click', () => {
      const code = couponInput.value.trim().toUpperCase();
      if (!code) return;
      postForm('cart_coupon.php', { code }).then(data => {
        if (data.success) {
          couponMsg.textContent = 'Coupon "' + data.code + '" applied! ' + data.percent + '% off';
          couponMsg.className = 'ab-coupon-msg success';
          applyTotals(data.totals);
        } else {
          couponMsg.textContent = data.message || 'Invalid Coupon Code';
          couponMsg.className = 'ab-coupon-msg error';
        }
      });
    });
  }

  // Coupon tag click
  qsa('.ab-coupon-tag').forEach(tag => {
    tag.addEventListener('click', () => {
      if (couponInput) couponInput.value = tag.dataset.code;
      if (applyBtn) applyBtn.click();
    });
  });

  /* ---------- Checkout Toggle ---------- */
  const cartSection     = qs('#cart-section');
  const checkoutSection = qs('#checkout-section');
  const triggerBtn      = qs('#checkout-trigger-btn');
  const backBtn         = qs('#back-to-cart-btn');

  if (triggerBtn) {
    triggerBtn.addEventListener('click', () => {
      if (!document.querySelector('.ab-cart-item')) return;
      cartSection.style.display     = 'none';
      checkoutSection.style.display = 'block';
      window.scrollTo({ top: 0, behavior: 'smooth' });
      renderCheckoutItems();
    });
  }
  if (backBtn) {
    backBtn.addEventListener('click', () => {
      checkoutSection.style.display = 'none';
      cartSection.style.display     = 'block';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // Build the checkout summary list straight from the already-rendered cart DOM
  function renderCheckoutItems() {
    const list = qs('#checkout-items-list');
    if (!list) return;
    list.innerHTML = '';
    qsa('.ab-cart-item').forEach(row => {
      const name  = row.querySelector('.ab-item-name')?.textContent || '';
      const img   = row.querySelector('.ab-item-img')?.getAttribute('src') || '';
      const qty   = row.querySelector('.ab-qty-value')?.textContent || '1';
      const total = row.querySelector('.ab-item-total')?.textContent || '₹0';
      const d = document.createElement('div');
      d.className = 'ab-checkout-item';
      d.innerHTML =
        '<img class="ab-checkout-item-img" src="' + img + '" alt="' + name + '">' +
        '<div class="ab-checkout-item-info"><span class="ab-checkout-item-name">' + name + '</span><span class="ab-checkout-item-qty">Qty: ' + qty + '</span></div>' +
        '<span class="ab-checkout-item-price">' + total + '</span>';
      list.appendChild(d);
    });
  }

  /* ---------- Payment Option Toggle ---------- */
  const paymentOptions = qsa('.ab-payment-option');
  const cardForm = qs('#card-form');
  paymentOptions.forEach(opt => {
    opt.addEventListener('click', () => {
      paymentOptions.forEach(o => o.classList.remove('selected'));
      opt.classList.add('selected');
      const val = opt.querySelector('input').value;
      if (cardForm) cardForm.style.display = (val === 'credit-card' || val === 'debit-card') ? 'flex' : 'none';
    });
  });

  /* ---------- Card Number Formatting ---------- */
  const cardNum = qs('#card-number');
  if (cardNum) {
    cardNum.addEventListener('input', () => {
      let v = cardNum.value.replace(/\D/g, '').substring(0, 16);
      cardNum.value = v.replace(/(.{4})/g, ' ').trim();
    });
  }
  const expiryEl = qs('#card-expiry');
  if (expiryEl) {
    expiryEl.addEventListener('input', () => {
      let v = expiryEl.value.replace(/\D/g, '').substring(0, 4);
      if (v.length >= 3) v = v.substring(0,2) + '/' + v.substring(2);
      expiryEl.value = v;
    });
  }

  /* ---------- Form Validation & Submission -> checkout.php (saves order in MySQL) ---------- */
  const form = qs('#checkout-form');
  if (form) {
    form.addEventListener('submit', e => {
      e.preventDefault();
      let valid = true;
      const fieldIds = { name:'ch-name', email:'ch-email', mobile:'ch-mobile', country:'ch-country', state:'ch-state', city:'ch-city', address:'ch-address', pin:'ch-pin' };
      Object.values(fieldIds).forEach(id => {
        const inp = qs('#' + id);
        if (inp && !inp.value.trim()) { inp.classList.add('error'); valid = false; }
        else if (inp) inp.classList.remove('error');
      });

      const paymentVal = qs('input[name="payment"]:checked');
      if (paymentVal && (paymentVal.value === 'credit-card' || paymentVal.value === 'debit-card')) {
        ['card-holder','card-number','card-expiry','card-cvv'].forEach(id => {
          const inp = qs('#' + id);
          if (inp && !inp.value.trim()) { inp.classList.add('error'); valid = false; }
          else if (inp) inp.classList.remove('error');
        });
      }

      if (!valid) return;

      const btn = qs('#complete-order-btn');
      btn.textContent = 'Processing...';
      btn.disabled = true;

      const payload = {};
      Object.entries(fieldIds).forEach(([key, id]) => { payload[key] = qs('#' + id).value.trim(); });
      payload.payment_method = paymentVal ? paymentVal.value : 'cod';

      postForm('checkout.php', payload).then(data => {
        if (data.success) {
          const modal = qs('#success-modal');
          qs('#order-number').textContent = data.order_number;
          modal.style.display = 'flex';
          const badge = qs('#cart-count');
          if (badge) badge.textContent = '0';
        } else {
          btn.textContent = 'Complete Order';
          btn.disabled = false;
          alert(data.message || 'Something went wrong. Please try again.');
        }
      });
    });
  }

  /* ---------- Add to Bag from Recommended / Recently Viewed ---------- */
  document.addEventListener('click', e => {
    if (e.target.classList.contains('ab-add-to-bag')) {
      const id = e.target.dataset.id;
      postForm('cart_add.php', { product_id: id, quantity: 1 }).then(data => {
        if (data.success) {
          const badge = qs('#cart-count');
          if (badge) badge.textContent = data.cart_count;
          const orig = e.target.textContent;
          e.target.textContent = '✓ Added';
          e.target.style.background = '#27AE60';
          setTimeout(() => { e.target.textContent = orig; e.target.style.background = ''; window.location.reload(); }, 900);
        }
      });
    }
  });

  /* ---------- Recently Viewed (localStorage keeps track of ids you've opened) ---------- */
  function renderRecentlyViewed() {
    const grid = qs('#recently-viewed-grid');
    if (!grid) return;
    let viewed = [];
    try { viewed = JSON.parse(localStorage.getItem(VIEWED_KEY)) || []; } catch {}
    if (viewed.length === 0) viewed = PRODUCTS.slice(0, 4).map(p => p.id);
    grid.innerHTML = '';
    viewed.slice(0, 4).forEach(pid => {
      const p = PRODUCTS.find(x => x.id === pid);
      if (!p) return;
      const card = document.createElement('div');
      card.className = 'ab-product-card';
      card.innerHTML =
        '<img class="ab-product-card-img" src="' + p.image + '" alt="' + p.name + '">' +
        '<div class="ab-product-card-body">' +
          '<div class="ab-product-card-name">' + p.name + '</div>' +
          '<div class="ab-product-card-price">' + fmt(p.price) + '</div>' +
          '<div class="ab-product-card-rating">' + '★'.repeat(Math.floor(p.rating)) + ' ' + p.rating + '</div>' +
          '<div class="ab-product-card-actions">' +
            '<button class="ab-btn-primary ab-add-to-bag" data-id="' + p.id + '">Add Bag</button>' +
            '<button class="ab-wishlist-btn">♡</button>' +
          '</div>' +
        '</div>';
      grid.appendChild(card);
    });
  }

  renderRecentlyViewed();
});
