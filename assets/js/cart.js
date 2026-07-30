/**
 * cart.js — Carrito persistente con notificaciones push
 */

class ToyCart {
  constructor() {
    this.items = JSON.parse(localStorage.getItem('toyStoreCart')) || [];
    this.lastActivityTs = parseInt(localStorage.getItem('toyStoreCartTs')) || 0;
    this.reminderTimeout = null;
    this._init();
  }

  _init() {
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

    const existing = this.items.find(i => i.id === product.id);
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
  }

  async remove(id) {
    const item = this.items.find(i => i.id === id);
    if (item) {
      await this._syncStock(id, 'increase', item.qty);
    }
    this.items = this.items.filter(i => i.id !== id);
    this._save();
    this.render();
    this.updateCount();
    if (this.items.length === 0) {
      clearTimeout(this.reminderTimeout);
      localStorage.removeItem('toyStoreCartTs');
    }
  }

  async updateQty(id, delta) {
    const item = this.items.find(i => i.id === id);
    if (!item) return;

    if (delta > 0) {
      const stockOk = await this._syncStock(id, 'decrease', 1);
      if (!stockOk) {
        alert('⚠️ No hay más unidades disponibles en stock.');
        return;
      }
    } else if (delta < 0) {
      await this._syncStock(id, 'increase', 1);
    }

    item.qty += delta;
    if (item.qty <= 0) {
      this.items = this.items.filter(i => i.id !== id);
      this._save();
      this.render();
      this.updateCount();
    } else {
      this._save();
      this.render();
      this.updateCount();
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
      stockEl.textContent = `Stock disponible: ${nuevoStock} unidades`;
      if (nuevoStock <= 0) {
        stockEl.className = 'badge bg-danger mb-3 py-2 px-3';
        stockEl.textContent = 'Agotado';
      } else {
        stockEl.className = 'badge bg-success-subtle text-success mb-3 py-2 px-3 fw-bold';
      }
    }
  }

  clear() {
    this.items = [];
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
    if (badge) badge.textContent = this.count();
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
    localStorage.setItem('toyStoreCart', JSON.stringify(this.items));
    const now = Date.now();
    localStorage.setItem('toyStoreCartTs', now);
    this.lastActivityTs = now;
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
    window.location.href = 'carrito.php';
  });
});