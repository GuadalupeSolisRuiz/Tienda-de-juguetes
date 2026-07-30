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

  add(product) {
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

  remove(id) {
    this.items = this.items.filter(i => i.id !== id);
    this._save();
    this.render();
    this.updateCount();
    if (this.items.length === 0) {
      clearTimeout(this.reminderTimeout);
      localStorage.removeItem('toyStoreCartTs');
    }
  }

  updateQty(id, delta) {
    const item = this.items.find(i => i.id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
      this.remove(id);
    } else {
      this._save();
      this.render();
      this.updateCount();
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
      const n = parseFloat(String(i.price).replace(/[$,\s]/g, '').match(/[\d.]+/)?.[0] || 0);
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
      body.innerHTML = this.items.map(item => `
        <div class="cart-item" data-id="${item.id}">
          <div class="cart-item-emoji">${item.emoji}</div>
          <div class="cart-item-info">
            <h4>${item.name}</h4>
            <span class="cart-item-price">${item.price}</span>
          </div>
          <div class="cart-item-controls">
            <button class="qty-btn" onclick="window.toyCart.updateQty('${item.id}',-1)" aria-label="Quitar uno"><i class="bi bi-dash"></i></button>
            <span class="qty-val">${item.qty}</span>
            <button class="qty-btn" onclick="window.toyCart.updateQty('${item.id}',1)" aria-label="Agregar uno"><i class="bi bi-plus"></i></button>
          </div>
          <button class="cart-item-remove" onclick="window.toyCart.remove('${item.id}')" aria-label="Eliminar"><i class="bi bi-trash3"></i></button>
        </div>`).join('');
    }

    const totalEl = document.getElementById('cartTotal');
    if (totalEl) totalEl.textContent = `$${this.total().toLocaleString('es-MX')}`;

    const footer = document.getElementById('cartFooter');
    if (footer) footer.style.display = this.items.length > 0 ? 'block' : 'none';
  }

  open() {
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