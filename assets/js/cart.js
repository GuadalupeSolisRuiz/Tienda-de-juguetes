/**
 * cart.js — Carrito persistente con notificaciones push
 */

class ToyCart {
  constructor() {
    this.items = [];
    this.lastActivityTs = 0;
    this.reminderTimeout = null;
    this._init();
  }

  _getStorageKey() {
    if (window.isUserLoggedIn && window.currentUserId) {
      return `toyStoreCart_user_${window.currentUserId}`;
    }
    return 'toyStoreCart_guest';
  }

  _getTimestampKey() {
    if (window.isUserLoggedIn && window.currentUserId) {
      return `toyStoreCartTs_user_${window.currentUserId}`;
    }
    return 'toyStoreCartTs_guest';
  }

  _load() {
    if (!window.isUserLoggedIn) {
      this.items = [];
      this.lastActivityTs = 0;
    } else {
      const key = this._getStorageKey();
      const tsKey = this._getTimestampKey();

      let saved = localStorage.getItem(key);
      if (!saved && window.currentUserId) {
        saved = localStorage.getItem('toyStoreCart');
      }

      try {
        this.items = saved ? JSON.parse(saved) : [];
      } catch (e) {
        this.items = [];
      }

      this.lastActivityTs = parseInt(localStorage.getItem(tsKey) || localStorage.getItem('toyStoreCartTs') || 0, 10);
    }
  }

  _init() {
    this._load();
    this.updateCount();
    this.render();
    this._checkAbandonedCart();
    this._registerServiceWorker();
  }

  // ── CRUD ──────────────────────────────────────────

  async add(product) {
    // 1. Verificar si hay una sesión iniciada
    if (!window.isUserLoggedIn) {
      // Si el modal de detalles del producto está abierto, cerrarlo
      const productModalEl = document.getElementById('productModal');
      if (productModalEl && window.bootstrap) {
        const bsProductModal = window.bootstrap.Modal.getInstance(productModalEl);
        if (bsProductModal) bsProductModal.hide();
      }

      // Abrir el modal de inicio de sesión
      const loginModalEl = document.getElementById('loginModal');
      if (loginModalEl && window.bootstrap) {
        const bsLoginModal = window.bootstrap.Modal.getOrCreateInstance(loginModalEl);
        bsLoginModal.show();

        const loginAlert = document.getElementById('loginAlert');
        if (loginAlert) {
          loginAlert.className = 'alert alert-warning mb-3';
          loginAlert.innerHTML = '🔒 <strong>Inicia sesión</strong> para agregar productos a tu carrito de compras.';
          loginAlert.style.display = 'block';
        }
      }
      return;
    }

    const productId = product.id;
    const stockOk = await this._syncStock(productId, 'decrease', 1);

    if (!stockOk) {
      alert('⚠️ Lo sentimos, este producto ya no tiene más unidades disponibles.');
      return;
    }

    const existing = this.items.find(i => String(i.id) === String(product.id));
    if (existing) {
      existing.qty++;
    } else {
      this.items.push({ ...product, qty: 1 });
    }
    this._save();
    this.render();
    this.updateCount();
    this._scheduleReminder();
    this._notify(`🎁 ¡${product.name} agregado!`, `Tienes ${this.count()} producto(s) en tu carrito.`);
    
    // Abrir automáticamente el sidebar del carrito para respuesta visual inmediata
    this.open();
    if (window.CartPage && typeof window.CartPage.render === 'function') {
      window.CartPage.render();
    }
  }

  async remove(id) {
    const item = this.items.find(i => String(i.id) === String(id));
    if (item) {
      await this._syncStock(item.id, 'increase', item.qty);
    }
    this.items = this.items.filter(i => String(i.id) !== String(id));
    this._save();
    this.render();
    this.updateCount();
    if (window.CartPage && typeof window.CartPage.render === 'function') {
      window.CartPage.render();
    }
    if (this.items.length === 0) {
      clearTimeout(this.reminderTimeout);
      localStorage.removeItem('toyStoreCartTs');
    }
  }

  async updateQty(id, delta) {
    const item = this.items.find(i => String(i.id) === String(id));
    if (!item) return;

    if (delta > 0) {
      const stockOk = await this._syncStock(item.id, 'decrease', 1);
      if (!stockOk) {
        alert('⚠️ No hay más unidades disponibles en stock.');
        return;
      }
    } else if (delta < 0) {
      await this._syncStock(item.id, 'increase', 1);
    }

    item.qty += delta;
    if (item.qty <= 0) {
      this.items = this.items.filter(i => String(i.id) !== String(id));
    }

    this._save();
    this.render();
    this.updateCount();

    if (window.CartPage && typeof window.CartPage.render === 'function') {
      window.CartPage.render();
    }
  }

  async _syncStock(productId, action, cantidad = 1) {
    try {
      const formData = new FormData();
      formData.append('action', action);
      formData.append('id_producto', productId);
      formData.append('cantidad', cantidad);

      const resp = await fetch('include/update_stock.php', {
        method: 'POST',
        body: formData
      });
      const data = await resp.json();

      if (data.success && typeof data.nuevo_stock !== 'undefined') {
        this._updateDomStock(productId, data.nuevo_stock);
        return true;
      } else {
        return false;
      }
    } catch (e) {
      console.warn('Error al sincronizar el stock:', e);
      return true; // fallback
    }
  }

  _updateDomStock(productId, nuevoStock) {
    // 1. Actualizar atributo data-stock en las tarjetas del catálogo
    const cards = document.querySelectorAll(`.product-card[data-id="${productId}"]`);
    cards.forEach(card => {
      card.dataset.stock = nuevoStock;
    });

    // 2. Si el modal de detalles del producto está activo, actualizar la etiqueta
    const stockEl = document.getElementById('modalProductStock');
    if (stockEl) {
      if (parseInt(nuevoStock) <= 0) {
        stockEl.className = 'product-modal-stock badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1';
        stockEl.style.fontSize = '0.78rem';
        stockEl.innerHTML = `<i class="bi bi-x-circle-fill text-danger me-1"></i> Agotado (Sin Stock)`;
        const modalBtnAddToCart = document.getElementById('modalBtnAddToCart');
        if (modalBtnAddToCart) {
          modalBtnAddToCart.disabled = true;
          modalBtnAddToCart.classList.add('disabled', 'opacity-50');
          modalBtnAddToCart.innerHTML = `<i class="bi bi-slash-circle me-2"></i> Juguete Sin Stock`;
        }
      } else {
        stockEl.className = 'product-modal-stock badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1';
        stockEl.style.fontSize = '0.78rem';
        stockEl.innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i> Stock: ${nuevoStock} dispon.`;
        const modalBtnAddToCart = document.getElementById('modalBtnAddToCart');
        if (modalBtnAddToCart) {
          modalBtnAddToCart.disabled = false;
          modalBtnAddToCart.classList.remove('disabled', 'opacity-50');
          modalBtnAddToCart.innerHTML = `<i class="bi bi-cart-plus-fill fs-5 me-2"></i> Agregar al Carrito`;
        }
      }
    }
  }

  clear() {
    this.items = [];
    if (window.isUserLoggedIn && window.currentUserId) {
      localStorage.removeItem(this._getStorageKey());
      localStorage.removeItem(this._getTimestampKey());
    }
    localStorage.removeItem('toyStoreCart');
    localStorage.removeItem('toyStoreCartTs');
    clearTimeout(this.reminderTimeout);
    this.render();
    this.updateCount();
  }

  // ── COMPUTED ──────────────────────────────────────

  count() {
    return this.items.reduce((sum, i) => sum + i.qty, 0);
  }

  total() {
    return this.items.reduce((sum, i) => {
      let n = 0;
      if (typeof i.price === 'number') {
        n = i.price;
      } else {
        let str = String(i.price).trim().replace(/[$\s]/g, '');
        if (/^\d{1,3}(\.\d{3})+$/.test(str)) {
          str = str.replace(/\./g, '');
        } else if (str.includes('.') && str.includes(',')) {
          str = str.indexOf('.') < str.indexOf(',') ? str.replace(/\./g, '').replace(',', '.') : str.replace(/,/g, '');
        } else if (str.includes(',')) {
          str = str.replace(',', '.');
        }
        n = parseFloat(str) || 0;
      }
      return sum + n * i.qty;
    }, 0);
  }

  // ── UI ────────────────────────────────────────────

  updateCount() {
    const badge = document.querySelector('.cart-count');
    if (badge) {
      if (!window.isUserLoggedIn) {
        badge.textContent = '0';
        badge.style.display = 'none';
      } else {
        const totalItems = this.count();
        badge.textContent = totalItems;
        if (totalItems > 0) {
          badge.style.display = 'flex';
        } else {
          badge.style.display = 'none';
        }
      }
    }
  }

  render() {
    const body = document.getElementById('cartItems');
    if (!body) return;

    if (this.items.length === 0) {
      body.innerHTML = `
        <div class="cart-empty">
          <div class="cart-empty-icon">🛒</div>
          <p>Tu carrito está vacío</p>
          <small>¡Agrega tus juguetes favoritos!</small>
        </div>`;
    } else {
      body.innerHTML = this.items.map(item => {
        const itemPrice = typeof item.price === 'number' ? item.price : (parseFloat(String(item.price).replace(/[$,\s]/g, '')) || 0);
        const displayPrice = item.formattedPrice || `$${itemPrice.toLocaleString('es-MX')}`;
        const mediaHtml = item.image 
          ? `<img src="${item.image}" alt="${item.name}" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">` 
          : (item.emoji || '🧸');

        return `
        <div class="cart-item" data-id="${item.id}">
          <div class="cart-item-emoji" style="overflow:hidden;display:flex;align-items:center;justify-content:center;">${mediaHtml}</div>
          <div class="cart-item-info">
            <h4 style="font-size:0.9rem;font-weight:700;margin-bottom:2px;">${item.name}</h4>
            <span class="cart-item-price" style="font-weight:800;color:var(--purple);">${displayPrice}</span>
          </div>
          <div class="cart-item-controls">
            <button class="qty-btn" onclick="window.toyCart.updateQty('${item.id}',-1)" aria-label="Quitar uno"><i class="bi bi-dash"></i></button>
            <span class="qty-val">${item.qty}</span>
            <button class="qty-btn" onclick="window.toyCart.updateQty('${item.id}',1)" aria-label="Agregar uno"><i class="bi bi-plus"></i></button>
          </div>
          <button class="cart-item-remove" onclick="window.toyCart.remove('${item.id}')" aria-label="Eliminar"><i class="bi bi-trash3"></i></button>
        </div>`;
      }).join('');
    }

    const totalEl = document.getElementById('cartTotal');
    if (totalEl) totalEl.textContent = `$${this.total().toLocaleString('es-MX')}`;

    const footer = document.getElementById('cartFooter');
    if (footer) footer.style.display = this.items.length > 0 ? 'block' : 'none';
  }

  open() {
    if (!window.isUserLoggedIn) {
      this.close();
      const loginModalEl = document.getElementById('loginModal');
      if (loginModalEl && window.bootstrap) {
        const bsLoginModal = window.bootstrap.Modal.getOrCreateInstance(loginModalEl);
        bsLoginModal.show();

        const loginAlert = document.getElementById('loginAlert');
        if (loginAlert) {
          loginAlert.className = 'alert alert-warning mb-3';
          loginAlert.innerHTML = '🔒 <strong>Inicia sesión</strong> para acceder a tu carrito de compras.';
          loginAlert.style.display = 'block';
        }
      }
      return;
    }
    document.getElementById('cartDrawer')?.classList.add('open');
    document.getElementById('cartOverlay')?.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  close() {
    document.getElementById('cartDrawer')?.classList.remove('open');
    document.getElementById('cartOverlay')?.classList.remove('open');
    document.body.style.overflow = '';
  }

  // ── PERSISTENCE ───────────────────────────────────

  _save() {
    if (window.isUserLoggedIn && window.currentUserId) {
      const key = this._getStorageKey();
      const tsKey = this._getTimestampKey();
      const now = Date.now();
      localStorage.setItem(key, JSON.stringify(this.items));
      localStorage.setItem(tsKey, now);
      localStorage.setItem('toyStoreCart', JSON.stringify(this.items));
      this.lastActivityTs = now;
    }
  }

  // ── NOTIFICATIONS ─────────────────────────────────

  async _requestPermission() {
    if (!('Notification' in window)) return false;
    if (Notification.permission === 'granted') return true;
    if (Notification.permission === 'denied') return false;
    try {
      return (await Notification.requestPermission()) === 'granted';
    } catch {
      return false;
    }
  }

  async _notify(title, body) {
    if (!(await this._requestPermission())) return;
    try {
      const reg = 'serviceWorker' in navigator ? await navigator.serviceWorker.ready : null;
      if (reg) {
        reg.showNotification(title, {
          body,
          icon: 'assets/img/notif-icon.png',
          tag: 'toy-cart',
          renotify: true,
          data: { url: window.location.href }
        });
      } else {
        new Notification(title, { body });
      }
    } catch {
      try { new Notification(title, { body }); } catch { /* no-op */ }
    }
  }

  _scheduleReminder(delayMs = 5 * 60 * 1000) {
    clearTimeout(this.reminderTimeout);
    if (this.items.length === 0) return;
    this.reminderTimeout = setTimeout(async () => {
      if (this.items.length > 0) {
        await this._notify(
          '⏰ ¡No olvides tu carrito!',
          `Tienes ${this.count()} juguete(s) esperándote. ¡Completa tu compra!`
        );
        this._scheduleReminder();
      }
    }, delayMs);
  }

  _checkAbandonedCart() {
    if (this.items.length === 0 || !this.lastActivityTs) return;
    const elapsed = Date.now() - this.lastActivityTs;
    const fiveMin = 5 * 60 * 1000;
    if (elapsed >= fiveMin) {
      // Cart was abandoned — notify shortly after page load
      setTimeout(() => {
        this._notify(
          '🛒 ¡Tu carrito te está esperando!',
          `Aún tienes ${this.count()} juguete(s) guardados. ¡Vuelve y completa tu compra!`
        );
      }, 3000);
    } else {
      this._scheduleReminder(fiveMin - elapsed);
    }
  }

  async _registerServiceWorker() {
    if (!('serviceWorker' in navigator)) return;
    try {
      await navigator.serviceWorker.register('sw.js');
    } catch { /* no-op in environments without SW support */ }
  }
}

// ── Bootstrap ──────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
  window.toyCart = new ToyCart();

  document.getElementById('btn-cart')?.addEventListener('click', () => window.toyCart.open());
  document.getElementById('cartDrawerClose')?.addEventListener('click', () => window.toyCart.close());
  document.getElementById('cartOverlay')?.addEventListener('click', () => window.toyCart.close());
  document.getElementById('btnClearCart')?.addEventListener('click', () => {
    if (window.toyCart.count() === 0) return;
    if (confirm('¿Deseas vaciar el carrito?')) window.toyCart.clear();
  });
  document.getElementById('btnCheckout')?.addEventListener('click', () => {
    window.openCheckoutModal();
  });
});

// ── CHECKOUT & ORDERS HELPERS ──

window.closeAllModals = function () {
  const modalIds = ['checkoutPaymentModal', 'ticketModal', 'ordersModal', 'profileModal', 'loginModal', 'welcomeBackModal'];
  modalIds.forEach(id => {
    const el = document.getElementById(id);
    if (el && window.bootstrap) {
      const modal = window.bootstrap.Modal.getInstance(el);
      if (modal) {
        modal.hide();
      }
    }
  });

  if (window.toyCart && typeof window.toyCart.close === 'function') {
    window.toyCart.close();
  }

  setTimeout(() => {
    const anyOpen = document.querySelectorAll('.modal.show');
    if (anyOpen.length === 0) {
      document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
      document.body.classList.remove('modal-open');
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    }
  }, 200);
};

window.addOfferToCart = function (id, name, priceOriginal, offerPrice, image, category) {
  if (!window.isUserLoggedIn) {
    window.closeAllModals();
    setTimeout(() => {
      const loginModalEl = document.getElementById('loginModal');
      if (loginModalEl && window.bootstrap) {
        const bsLoginModal = window.bootstrap.Modal.getOrCreateInstance(loginModalEl);
        bsLoginModal.show();
      }
    }, 150);
    return;
  }

  if (window.toyCart) {
    const offerDiscount = priceOriginal - offerPrice;
    window.toyCart.add({
      id: id,
      name: `${name} (🔥 Oferta)`,
      price: offerPrice,
      originalPrice: priceOriginal,
      offerDiscount: offerDiscount,
      image: image,
      category: category
    });
  }
};

window.printTicket = function () {
  const printableArea = document.getElementById('printableTicketArea');
  if (!printableArea) {
    window.print();
    return;
  }

  const printWin = window.open('', '_blank', 'width=450,height=650');
  if (printWin) {
    printWin.document.write(`
      <!DOCTYPE html>
      <html lang="es">
      <head>
        <meta charset="UTF-8">
        <title>Ticket de Compra - Toys Nova</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <style>
          @page { size: auto; margin: 4mm; }
          body { font-family: system-ui, -apple-system, sans-serif; background: #fff; margin: 0; padding: 10px; }
          .ticket-receipt-card { border: none !important; box-shadow: none !important; max-width: 400px; margin: 0 auto; }
        </style>
      </head>
      <body>
        ${printableArea.outerHTML}
        <script>
          window.onload = function() {
            window.print();
            setTimeout(function() { window.close(); }, 500);
          };
        </script>
      </body>
      </html>
    `);
    printWin.document.close();
  } else {
    window.print();
  }
};

window.openCheckoutModal = function (appliedCoupon = null) {
  if (!window.isUserLoggedIn) {
    window.closeAllModals();
    setTimeout(() => {
      const loginModalEl = document.getElementById('loginModal');
      if (loginModalEl && window.bootstrap) {
        const bsLoginModal = window.bootstrap.Modal.getOrCreateInstance(loginModalEl);
        bsLoginModal.show();
      }
    }, 150);
    return;
  }

  const items = window.toyCart ? window.toyCart.items : [];
  if (!items || items.length === 0) {
    alert('Tu carrito está vacío.');
    return;
  }

  const subtotal = window.toyCart.total();
  const shipping = subtotal >= 1000 ? 0 : 80;
  const discount = appliedCoupon ? subtotal * (appliedCoupon.pct / 100) : 0;
  const total = Math.max(0, subtotal + shipping - discount);

  const fmt = n => n.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

  // Preview items
  const itemsPreviewEl = document.getElementById('checkoutItemsPreview');
  if (itemsPreviewEl) {
    itemsPreviewEl.innerHTML = items.map(i => {
      const unit = typeof i.price === 'number' ? i.price : parseFloat(String(i.price).replace(/[$,\s]/g, '')) || 0;
      return `<div class="d-flex justify-content-between align-items-center mb-1">
        <span class="text-truncate" style="max-width:180px;"><strong>${i.qty}x</strong> ${i.name}</span>
        <span class="fw-bold">$${fmt(unit * i.qty)}</span>
      </div>`;
    }).join('');
  }

  const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
  setEl('checkoutSubtotalDisplay', `$${fmt(subtotal)}`);
  setEl('checkoutShippingDisplay', shipping === 0 ? '¡Gratis!' : `$${fmt(shipping)}`);
  
  const discRow = document.getElementById('checkoutDiscountRow');
  if (discRow) {
    if (discount > 0) {
      discRow.style.display = 'flex';
      setEl('checkoutDiscountDisplay', `-$${fmt(discount)}`);
    } else {
      discRow.style.display = 'none';
    }
  }
  setEl('checkoutTotalDisplay', `$${fmt(total)}`);

  // Reset payment option to Cash
  const radEfectivo = document.getElementById('payMethodEfectivo');
  if (radEfectivo) radEfectivo.checked = true;
  
  const cashFields = document.getElementById('cashFields');
  const cardFields = document.getElementById('cardFields');
  if (cashFields) cashFields.style.display = 'block';
  if (cardFields) cardFields.style.display = 'none';
  document.getElementById('payMethodEfectivoLabel')?.classList.add('active');
  document.getElementById('payMethodTarjetaLabel')?.classList.remove('active');

  const cashInput = document.getElementById('cashAmountInput');
  if (cashInput) cashInput.value = '';
  const changeAlert = document.getElementById('changeCalculatorAlert');
  if (changeAlert) changeAlert.style.display = 'none';
  const errAlert = document.getElementById('checkoutErrorAlert');
  if (errAlert) errAlert.style.display = 'none';

  // Close previous modals & Open Checkout Payment Modal
  window.closeAllModals();
  setTimeout(() => {
    const modalEl = document.getElementById('checkoutPaymentModal');
    if (modalEl && window.bootstrap) {
      const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();
    }
  }, 150);
};

// Payment Method Switcher Listeners
document.addEventListener('DOMContentLoaded', () => {
  const radEfectivo = document.getElementById('payMethodEfectivo');
  const radTarjeta = document.getElementById('payMethodTarjeta');
  const cashLabel = document.getElementById('payMethodEfectivoLabel');
  const cardLabel = document.getElementById('payMethodTarjetaLabel');
  const cashFields = document.getElementById('cashFields');
  const cardFields = document.getElementById('cardFields');

  if (cashLabel && cardLabel) {
    cashLabel.addEventListener('click', () => {
      if (radEfectivo) radEfectivo.checked = true;
      cashLabel.classList.add('active');
      cardLabel.classList.remove('active');
      if (cashFields) cashFields.style.display = 'block';
      if (cardFields) cardFields.style.display = 'none';
    });

    cardLabel.addEventListener('click', () => {
      if (radTarjeta) radTarjeta.checked = true;
      cardLabel.classList.add('active');
      cashLabel.classList.remove('active');
      if (cardFields) cardFields.style.display = 'block';
      if (cashFields) cashFields.style.display = 'none';
    });
  }

  // Cash change calculator listener
  const cashInput = document.getElementById('cashAmountInput');
  const changeAlert = document.getElementById('changeCalculatorAlert');
  const changeDisplay = document.getElementById('cashChangeDisplay');

  if (cashInput) {
    cashInput.addEventListener('input', () => {
      const val = parseFloat(cashInput.value) || 0;
      const subtotal = window.toyCart ? window.toyCart.total() : 0;
      const shipping = subtotal >= 1000 ? 0 : 80;
      const discount = window.CartPage && window.CartPage.appliedCoupon ? subtotal * (window.CartPage.appliedCoupon.pct / 100) : 0;
      const total = Math.max(0, subtotal + shipping - discount);

      if (val > 0) {
        const change = val - total;
        if (changeAlert) changeAlert.style.display = 'block';
        if (change >= 0) {
          if (changeAlert) changeAlert.className = 'alert alert-info py-2 px-3 mb-0 small';
          if (changeDisplay) changeDisplay.textContent = `$${change.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        } else {
          if (changeAlert) changeAlert.className = 'alert alert-warning py-2 px-3 mb-0 small';
          if (changeDisplay) changeDisplay.textContent = `Faltan $${Math.abs(change).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }
      } else {
        if (changeAlert) changeAlert.style.display = 'none';
      }
    });
  }

  // Card Number Formatting
  const cardNumInput = document.getElementById('cardNumber');
  if (cardNumInput) {
    cardNumInput.addEventListener('input', (e) => {
      let v = e.target.value.replace(/\D/g, '').substring(0, 16);
      let parts = [];
      for (let i = 0; i < v.length; i += 4) {
        parts.push(v.substring(i, i + 4));
      }
      e.target.value = parts.join(' ');
    });
  }

  // Card Expiry Formatting
  const cardExpInput = document.getElementById('cardExpiry');
  if (cardExpInput) {
    cardExpInput.addEventListener('input', (e) => {
      let v = e.target.value.replace(/\D/g, '').substring(0, 4);
      if (v.length >= 2) {
        e.target.value = v.substring(0, 2) + '/' + v.substring(2);
      } else {
        e.target.value = v;
      }
    });
  }

  // Confirm Order Submit Button
  const btnConfirm = document.getElementById('btnConfirmCheckoutOrder');
  if (btnConfirm) {
    btnConfirm.addEventListener('click', async () => {
      const errAlert = document.getElementById('checkoutErrorAlert');
      if (errAlert) errAlert.style.display = 'none';

      const method = document.querySelector('input[name="paymentMethod"]:checked')?.value || 'efectivo';
      let cashPaid = 0;
      let cashChange = 0;

      const subtotal = window.toyCart ? window.toyCart.total() : 0;
      const shipping = subtotal >= 1000 ? 0 : 80;
      const coupon = window.CartPage ? window.CartPage.appliedCoupon : null;
      const discount = coupon ? subtotal * (coupon.pct / 100) : 0;
      const total = Math.max(0, subtotal + shipping - discount);

      if (method === 'efectivo') {
        const cashVal = parseFloat(document.getElementById('cashAmountInput')?.value) || 0;
        if (cashVal > 0 && cashVal < total) {
          if (errAlert) {
            errAlert.textContent = `El monto en efectivo ($${cashVal.toFixed(2)}) es menor al total del pedido ($${total.toFixed(2)}).`;
            errAlert.style.display = 'block';
          }
          return;
        }
        if (cashVal > 0) {
          cashPaid = cashVal;
          cashChange = cashVal - total;
        }
      } else if (method === 'tarjeta') {
        const holder = document.getElementById('cardHolder')?.value?.trim();
        const num = document.getElementById('cardNumber')?.value?.replace(/\s/g, '');
        const exp = document.getElementById('cardExpiry')?.value?.trim();
        const cvv = document.getElementById('cardCvv')?.value?.trim();

        if (!holder || !num || !exp || !cvv) {
          if (errAlert) {
            errAlert.textContent = 'Por favor completa todos los campos de la tarjeta.';
            errAlert.style.display = 'block';
          }
          return;
        }
        if (num.length < 15) {
          if (errAlert) {
            errAlert.textContent = 'Ingresa un número de tarjeta válido.';
            errAlert.style.display = 'block';
          }
          return;
        }
      }

      btnConfirm.disabled = true;
      btnConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando pedido...';

      try {
        const payload = {
          items: window.toyCart ? window.toyCart.items : [],
          subtotal: subtotal,
          envio: shipping,
          descuento: discount,
          total: total,
          cupon: coupon ? coupon.code : '',
          metodo_pago: method,
          monto_efectivo: cashPaid,
          cambio_efectivo: cashChange
        };

        const resp = await fetch('include/procesar_pedido.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });

        const respText = await resp.text();
        let data = {};
        try {
          data = JSON.parse(respText);
        } catch (e) {
          data = { success: false, message: respText || 'Respuesta inválida del servidor.' };
        }

        if (data.success && data.ticket) {
          // Clear Cart
          if (window.toyCart) window.toyCart.clear();
          if (window.CartPage && window.CartPage.render) window.CartPage.render();

          // Close All Modals & Open Ticket Modal
          window.closeAllModals();
          setTimeout(() => {
            window.renderTicket(data.ticket);
            const ticketModalEl = document.getElementById('ticketModal');
            if (ticketModalEl && window.bootstrap) {
              const bsTicket = window.bootstrap.Modal.getOrCreateInstance(ticketModalEl);
              bsTicket.show();
            }
          }, 150);
        } else {
          if (errAlert) {
            errAlert.textContent = data.message || 'Ocurrió un error al procesar el pedido.';
            errAlert.style.display = 'block';
          }
        }
      } catch (err) {
        if (errAlert) {
          errAlert.textContent = 'Error de conexión al procesar el pedido.';
          errAlert.style.display = 'block';
        }
      } finally {
        btnConfirm.disabled = false;
        btnConfirm.innerHTML = '<i class="bi bi-check-circle-fill fs-5"></i> Confirmar Compra y Generar Ticket';
      }
    });
  }
});

// Render ticket object into #ticketModal
window.renderTicket = function (ticket) {
  const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
  const fmt = n => parseFloat(n || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

  setEl('ticketFolio', ticket.folio || 'TN-000000');
  setEl('ticketFecha', ticket.fecha || '');
  setEl('ticketClienteNombre', ticket.cliente_nombre || 'Cliente');
  setEl('ticketClienteCorreo', ticket.cliente_correo || '');

  const itemsBody = document.getElementById('ticketItemsBody');
  if (itemsBody && Array.isArray(ticket.items)) {
    itemsBody.innerHTML = ticket.items.map(i => `
      <tr>
        <td class="fw-bold">${i.cantidad}x</td>
        <td>${i.nombre}</td>
        <td class="text-end">$${fmt(i.precio_unitario)}</td>
        <td class="text-end fw-bold">$${fmt(i.subtotal)}</td>
      </tr>
    `).join('');
  }

  setEl('ticketSubtotal', `$${fmt(ticket.subtotal)}`);
  setEl('ticketEnvio', ticket.envio === 0 ? '¡Gratis!' : `$${fmt(ticket.envio)}`);
  
  const discRow = document.getElementById('ticketDiscountRow');
  if (discRow) {
    if (ticket.descuento > 0) {
      discRow.style.display = 'flex';
      setEl('ticketDescuento', `-$${fmt(ticket.descuento)}`);
    } else {
      discRow.style.display = 'none';
    }
  }
  setEl('ticketTotal', `$${fmt(ticket.total)}`);
  setEl('ticketMetodoPago', ticket.metodo_pago || 'EFECTIVO');

  const cashDetails = document.getElementById('ticketCashDetails');
  if (cashDetails) {
    if (ticket.metodo_pago && ticket.metodo_pago.toLowerCase() === 'efectivo' && ticket.monto_efectivo > 0) {
      cashDetails.style.display = 'block';
      setEl('ticketCashPaid', `$${fmt(ticket.monto_efectivo)}`);
      setEl('ticketCashChange', `$${fmt(ticket.cambio_efectivo)}`);
    } else {
      cashDetails.style.display = 'none';
    }
  }
};

// Load User Orders History
window.loadUserOrders = async function () {
  window.closeAllModals();
  
  setTimeout(async () => {
    const ordersModalEl = document.getElementById('ordersModal');
    if (ordersModalEl && window.bootstrap) {
      const bsOrders = window.bootstrap.Modal.getOrCreateInstance(ordersModalEl);
      bsOrders.show();
    }

    const container = document.getElementById('userOrdersContainer');
    if (!container) return;

    container.innerHTML = `<div class="text-center py-4">
      <div class="spinner-border text-purple" role="status"></div>
      <p class="text-muted mt-2 small">Cargando tus pedidos...</p>
    </div>`;

    try {
      const resp = await fetch('include/obtener_pedidos.php');
      const respText = await resp.text();
      let data = {};
      try {
        data = JSON.parse(respText);
      } catch (e) {
        data = { success: false, message: 'Respuesta no válida del servidor.' };
      }

      if (data.success && Array.isArray(data.pedidos)) {
        if (data.pedidos.length === 0) {
          container.innerHTML = `<div class="text-center py-5">
            <div style="font-size:3rem;" class="mb-2">🛍️</div>
            <h5 class="fw-bold">Aún no has realizado pedidos</h5>
            <p class="text-muted small">Agrega juguetes a tu carrito y finaliza tu compra para verlos aquí.</p>
          </div>`;
          return;
        }

        const fmt = n => parseFloat(n || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        container.innerHTML = data.pedidos.map(p => {
          const thumbs = (p.items || []).slice(0, 4).map(item => `
            <img src="${item.imagen}" title="${item.nombre} x${item.cantidad}" class="order-thumb-item" alt="${item.nombre}">
          `).join('');

          const extraCount = (p.items || []).length > 4 ? `<span class="badge bg-light text-dark border align-self-center">+${p.items.length - 4} más</span>` : '';

          return `<div class="order-history-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-2 mb-3 gap-2">
              <div>
                <strong class="font-monospace text-purple fs-6" style="color:#7C3AED;">${p.folio}</strong>
                <small class="text-muted ms-2">${p.fecha}</small>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2 py-1" style="font-size:0.75rem; background:rgba(124,58,237,0.1); color:#7C3AED;">
                  <i class="bi bi-wallet2 me-1"></i> ${p.metodo_pago}
                </span>
                ${(() => {
                  const est = String(p.estado || '').toLowerCase();
                  if (est === 'pendiente') {
                    return `<span class="badge px-2 py-1 fw-bold" style="font-size:0.75rem; background:rgba(245, 158, 11, 0.15); color:#B45309; border:1px solid rgba(245, 158, 11, 0.35);">
                      <i class="bi bi-clock-history me-1"></i> ${p.estado}
                    </span>`;
                  } else if (est === 'cancelado') {
                    return `<span class="badge px-2 py-1 fw-bold" style="font-size:0.75rem; background:rgba(239, 68, 68, 0.15); color:#B91C1C; border:1px solid rgba(239, 68, 68, 0.35);">
                      <i class="bi bi-x-circle-fill me-1"></i> ${p.estado}
                    </span>`;
                  } else {
                    return `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:0.75rem;">
                      <i class="bi bi-check-circle-fill me-1"></i> ${p.estado}
                    </span>`;
                  }
                })()}
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
              <div class="d-flex align-items-center gap-2">
                ${thumbs}
                ${extraCount}
              </div>
              <div class="text-end">
                <span class="text-muted d-block" style="font-size:0.75rem;">TOTAL COMPRA</span>
                <strong class="fs-5 text-dark" style="color:#7C3AED;">$${fmt(p.total)}</strong>
                <button class="btn btn-outline-purple btn-sm d-block mt-2 font-fredoka fw-bold px-3 ms-auto"
                  style="border-color:#7C3AED; color:#7C3AED;"
                  onclick="window.viewOrderTicket(${p.id_pedido})">
                  <i class="bi bi-receipt me-1"></i> Ver Ticket
                </button>
              </div>
            </div>
          </div>`;
        }).join('');
      } else {
        container.innerHTML = `<div class="alert alert-warning mb-0">${data.message || 'No se pudieron cargar los pedidos.'}</div>`;
      }
    } catch (err) {
      container.innerHTML = `<div class="alert alert-danger mb-0">Error de conexión al cargar los pedidos.</div>`;
    }
  }, 150);
};

// View Order Ticket by Order ID
window.viewOrderTicket = async function (orderId) {
  try {
    const resp = await fetch(`include/obtener_ticket.php?order_id=${orderId}`);
    const respText = await resp.text();
    let data = {};
    try {
      data = JSON.parse(respText);
    } catch (e) {
      data = { success: false, message: 'Respuesta no válida del servidor.' };
    }

    if (data.success && data.ticket) {
      window.closeAllModals();
      setTimeout(() => {
        window.renderTicket(data.ticket);
        const ticketModalEl = document.getElementById('ticketModal');
        if (ticketModalEl && window.bootstrap) {
          const bsTicket = window.bootstrap.Modal.getOrCreateInstance(ticketModalEl);
          bsTicket.show();
        }
      }, 150);
    } else {
      alert(data.message || 'No se pudo obtener el ticket.');
    }
  } catch (err) {
    alert('Error al conectar con el servidor.');
  }
};