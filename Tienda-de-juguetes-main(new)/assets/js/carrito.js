/**
 * carrito.js — Full-page cart view for carrito.php
 */

const CartPage = {
  appliedCoupon: null,
  SHIPPING_THRESHOLD: 1000,
  SHIPPING_COST: 80,
  COUPONS: {
    'JUGUETE10': { pct: 10, label: '10%' },
    'VERANO20':  { pct: 20, label: '20%' },
    'PROMO15':   { pct: 15, label: '15%' },
  },

  init() {
    this.render();
    this._bindEvents();
  },

  _bindEvents() {
    document.getElementById('btnApplyCoupon')
      ?.addEventListener('click', () => this.applyCoupon());

    document.getElementById('couponInput')
      ?.addEventListener('keypress', e => { if (e.key === 'Enter') this.applyCoupon(); });

    document.getElementById('btnCheckoutPage')
      ?.addEventListener('click', () => this.checkout());

    document.getElementById('btnMobileCheckout')
      ?.addEventListener('click', () => this.checkout());
  },

  // ── RENDER ────────────────────────────────────────────

  render() {
    const tc    = window.toyCart;
    const items = tc.items;
    const qty   = tc.count();

    const subtitleEl = document.getElementById('cartPageSubtitle');
    if (subtitleEl) {
      subtitleEl.textContent = qty === 0
        ? 'No tienes productos en tu carrito'
        : `${qty} artículo${qty !== 1 ? 's' : ''} en tu carrito`;
    }

    const emptyEl    = document.getElementById('cartPageEmpty');
    const itemsEl    = document.getElementById('cartPageItems');
    const summaryBox = document.getElementById('cartSummaryBox');
    const bannerEl   = document.getElementById('freeShippingBanner');
    const mobileBar  = document.getElementById('cartMobileBar');

    if (items.length === 0) {
      emptyEl    && (emptyEl.style.display    = 'flex');
      itemsEl    && (itemsEl.innerHTML        = '');
      summaryBox && (summaryBox.style.display = 'none');
      bannerEl   && (bannerEl.style.display   = 'none');
      mobileBar  && (mobileBar.style.display  = 'none');
      return;
    }

    emptyEl    && (emptyEl.style.display    = 'none');
    summaryBox && (summaryBox.style.display = 'block');
    bannerEl   && (bannerEl.style.display   = 'flex');
    mobileBar  && (mobileBar.style.display  = 'flex');

    if (itemsEl) {
      itemsEl.innerHTML = items.map(item => this._itemHTML(item)).join('');
    }

    this._updateShippingBanner(tc.total());
    this.renderSummary();
  },

  _itemHTML(item) {
    const unit = this._parsePrice(item.price);
    const line = unit * item.qty;
    const fmt  = n => n.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    return `
      <div class="cart-card" data-id="${item.id}">
        <div class="cart-card-img">${item.emoji}</div>
        <div class="cart-card-info">
          ${item.category ? `<span class="cart-card-cat">${item.category}</span>` : ''}
          <h4 class="cart-card-name">${item.name}</h4>
          <span class="cart-card-unit">$${fmt(unit)}</span>
        </div>
        <div class="cart-card-qty-block">
          <span class="cart-card-qty-lbl">Cantidad</span>
          <div class="cart-card-qty-row">
            <button class="cart-card-qty-btn"
              onclick="CartPage.changeQty('${item.id}', -1)"
              aria-label="Disminuir cantidad">—</button>
            <input class="cart-card-qty-input" type="number" value="${item.qty}" min="1"
              onchange="CartPage.setQty('${item.id}', +this.value)"
              aria-label="Cantidad" />
            <button class="cart-card-qty-btn"
              onclick="CartPage.changeQty('${item.id}', 1)"
              aria-label="Aumentar cantidad">+</button>
          </div>
        </div>
        <div class="cart-card-side">
          <button class="cart-card-del"
            onclick="CartPage.removeItem('${item.id}')"
            aria-label="Eliminar ${item.name}">
            <i class="bi bi-trash3"></i>
          </button>
          <span class="cart-card-total" id="lineTotal-${item.id}">$${fmt(line)}</span>
        </div>
      </div>`;
  },

  renderSummary() {
    const tc       = window.toyCart;
    const subtotal = tc.total();
    const shipping = subtotal > 0 && subtotal >= this.SHIPPING_THRESHOLD ? 0 : this.SHIPPING_COST;
    const discount = this.appliedCoupon ? subtotal * (this.appliedCoupon.pct / 100) : 0;
    const total    = Math.max(0, subtotal + shipping - discount);
    const count    = tc.count();
    const fmt      = n => n.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

    set('summaryCount',    count);
    set('summarySubtotal', `$${fmt(subtotal)}`);

    const shipEl = document.getElementById('summaryShipping');
    if (shipEl) {
      if (shipping === 0) {
        shipEl.textContent = '¡Gratis!';
        shipEl.style.color      = 'var(--green)';
        shipEl.style.fontWeight = '800';
      } else {
        shipEl.textContent      = `$${fmt(shipping)}`;
        shipEl.style.color      = '';
        shipEl.style.fontWeight = '';
      }
    }

    const discRow = document.getElementById('discountRow');
    if (discRow) {
      if (discount > 0) {
        discRow.style.display = 'flex';
        set('summaryDiscount', `APLICADO: -$${fmt(discount)}`);
        const badge = document.getElementById('appliedCouponBadge');
        if (badge) {
          badge.textContent = this.appliedCoupon.code;
          badge.style.display = 'inline-block';
        }
      } else {
        discRow.style.display = 'none';
        const badge = document.getElementById('appliedCouponBadge');
        if (badge) badge.style.display = 'none';
      }
    }

    set('summaryTotal', `$${fmt(total)}`);

    // Mobile bar total
    set('cartMobileTotal', `$${fmt(total)}`);
  },

  _updateShippingBanner(subtotal) {
    const banner = document.getElementById('freeShippingBanner');
    const msg    = document.getElementById('freeShippingMsg');
    if (!banner || !msg) return;

    if (subtotal <= 0) { banner.style.display = 'none'; return; }
    banner.style.display = 'flex';

    if (subtotal >= this.SHIPPING_THRESHOLD) {
      banner.classList.add('free');
      msg.textContent = '¡Envío gratis aplicado a tu pedido!';
    } else {
      banner.classList.remove('free');
      const rem = (this.SHIPPING_THRESHOLD - subtotal)
        .toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      msg.textContent = `¡Agrega $${rem} más para obtener envío gratis!`;
    }
  },

  // ── COUPON ────────────────────────────────────────────

  applyCoupon() {
    const input = document.getElementById('couponInput');
    const msg   = document.getElementById('couponMsg');
    if (!input || !msg) return;

    const code   = input.value.trim().toUpperCase();
    if (!code) return;

    const coupon = this.COUPONS[code];

    if (coupon) {
      this.appliedCoupon = { code, pct: coupon.pct };
      input.classList.remove('invalid');
      input.classList.add('valid');
      msg.className = 'cart-coupon-msg valid';
      msg.innerHTML = `<i class="bi bi-check-circle-fill"></i> Código <strong>${code}</strong> aplicado — ${coupon.label} de descuento`;
      this.renderSummary();
    } else {
      this.appliedCoupon = null;
      input.classList.remove('valid');
      input.classList.add('invalid');
      msg.className = 'cart-coupon-msg invalid';
      msg.innerHTML = `<i class="bi bi-x-circle-fill"></i> Código inválido o expirado`;
      setTimeout(() => {
        input.classList.remove('invalid');
        msg.className = 'cart-coupon-msg';
        msg.innerHTML = '';
      }, 3000);
    }
  },

  // ── ITEM ACTIONS ──────────────────────────────────────

  changeQty(id, delta) {
    window.toyCart.updateQty(id, delta);
    this.render();
  },

  setQty(id, qty) {
    if (!qty || qty < 1) { this.render(); return; }
    const item = window.toyCart.items.find(i => i.id === id);
    if (!item) return;
    const diff = qty - item.qty;
    if (diff === 0) return;
    window.toyCart.updateQty(id, diff);
    this.render();
  },

  removeItem(id) {
    const el = document.querySelector(`.cart-card[data-id="${id}"]`);
    if (el) {
      el.classList.add('removing');
      setTimeout(() => {
        window.toyCart.remove(id);
        this.render();
      }, 300);
    } else {
      window.toyCart.remove(id);
      this.render();
    }
  },

  // ── CHECKOUT ──────────────────────────────────────────

  checkout() {
    if (window.toyCart.count() === 0) return;
    if (confirm('¿Confirmas que deseas finalizar tu compra?')) {
      window.toyCart.clear();
      this.render();
      this._showSuccess();
    }
  },

  _showSuccess() {
    const html = `
      <div class="order-success-overlay" id="orderSuccessOverlay">
        <div class="order-success-card">
          <div class="order-success-icon">🎉</div>
          <h2>¡Gracias por tu compra!</h2>
          <p>Tu pedido fue recibido exitosamente.<br>Recibirás un correo de confirmación pronto.</p>
          <a href="index.php" class="btn-primary-custom mt-4 d-inline-flex">
            <i class="bi bi-bag-fill"></i>
            Seguir comprando
          </a>
        </div>
      </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    requestAnimationFrame(() => {
      document.getElementById('orderSuccessOverlay')?.classList.add('visible');
    });
  },

  // ── UTILS ─────────────────────────────────────────────

  _parsePrice(price) {
    return parseFloat(
      String(price).replace(/[$,\s]/g, '').match(/[\d.]+/)?.[0] || 0
    );
  }
};

document.addEventListener('DOMContentLoaded', () => {
  if (window.toyCart) CartPage.init();
});
