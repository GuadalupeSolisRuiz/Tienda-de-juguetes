/**
 * Tienda de Juguetes - script.js
 * JavaScript general de la aplicación.
 */

// ── MOSTRAR/OCULTAR CONTRASEÑA EN FORMULARIO DE REGISTRO ──
function togglePw(id, btn) {
  const input = document.getElementById(id);
  if (!input) return;
  const icon = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    if (icon) icon.className = 'bi bi-eye-slash';
  } else {
    input.type = 'password';
    if (icon) icon.className = 'bi bi-eye';
  }
}

// ── MOSTRAR/OCULTAR CONTRASEÑA EN EL MODAL DE INICIO DE SESIÓN ──
function toggleModalPw(id, btn) {
  const input = document.getElementById(id);
  if (!input) return;
  const icon = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    if (icon) icon.className = 'bi bi-eye-slash';
  } else {
    input.type = 'password';
    if (icon) icon.className = 'bi bi-eye';
  }
}

// ── CONTROL DE INACTIVIDAD DE SESIÓN ──
// Se dejó sin modal para evitar que aparezca de forma invasiva.
function logoutNow() {
  localStorage.removeItem('toyStoreCart');
  localStorage.removeItem('toyStoreCartTs');
  if (window.toyCart) {
    window.toyCart.items = [];
    window.toyCart.updateCount();
  }
  window.location.href = 'include/logout.php';
}


// ── EVENTOS DOMContentLoaded ──
document.addEventListener('DOMContentLoaded', function () {

  // 1. Desplazamiento suave para enlaces de anclaje (Smooth Scroll)
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href && href !== '#' && href.length > 1) {
        try {
          const target = document.querySelector(href);
          if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        } catch (err) { }
      }
    });
  });

  // 2. Alternar favoritos (Wishlist Toggle)
  document.querySelectorAll('.product-wishlist').forEach(btn => {
    btn.addEventListener('click', function () {
      const icon = this.querySelector('i');
      if (icon) {
        if (icon.classList.contains('bi-heart')) {
          icon.classList.replace('bi-heart', 'bi-heart-fill');
          this.style.color = '#EF4444';
        } else {
          icon.classList.replace('bi-heart-fill', 'bi-heart');
          this.style.color = '';
        }
      }
    });
  });

  // 3. Animación de agregar al carrito
  const cartCount = document.querySelector('.cart-count');
  let count = 0;
  document.querySelectorAll('.btn-add-cart').forEach(btn => {
    btn.addEventListener('click', function () {
      count++;
      if (cartCount) {
        cartCount.textContent = count;
      }
      // Animación rápida de pulso
      this.style.transform = 'scale(1.3)';
      setTimeout(() => { this.style.transform = ''; }, 200);
    });
  });

  // 4. Navegación de vistas en el modal de producto
  const productModalEl = document.getElementById('productModal');
  const modalImage = document.getElementById('modalProductImage');
  const modalTitle = document.getElementById('productModalLabel');
  const modalDescription = document.getElementById('modalProductDescription');
  const modalPrice = document.getElementById('modalProductPrice');
  const modalStock = document.getElementById('modalProductStock');
  const modalArrowLeft = document.getElementById('modalArrowLeft');
  const modalArrowRight = document.getElementById('modalArrowRight');
  const modalDots = document.querySelectorAll('.modal-view-dot');

  // Orden fijo de las vistas (ciclo)
  const VIEW_ORDER = ['frente', 'derecha', 'izquierda'];
  let currentModalViews = {};
  let currentViewIndex = 0;

  // Usar la API de Bootstrap para manejar el modal correctamente
  const bsProductModal = productModalEl ? new bootstrap.Modal(productModalEl) : null;

  function openProductModal() {
    if (bsProductModal) bsProductModal.show();
  }

  function setModalView(index) {
    currentViewIndex = ((index % VIEW_ORDER.length) + VIEW_ORDER.length) % VIEW_ORDER.length;
    const view = VIEW_ORDER[currentViewIndex];
    const imagePath = currentModalViews[view];
    if (imagePath) {
      modalImage.src = imagePath;
      modalImage.alt = `${modalTitle.textContent || 'Producto'} - ${view}`;
    }
    // Actualizar puntos indicadores
    modalDots.forEach(dot => {
      dot.classList.toggle('active', dot.dataset.dot === view);
    });
  }

  document.querySelectorAll('.product-card').forEach(card => {
    const image = card.querySelector('.product-visual');
    if (!image) return;

    let views = {};
    try {
      views = image.dataset.views ? JSON.parse(image.dataset.views) : {};
    } catch (e) {
      views = {};
    }

    card.addEventListener('click', function (event) {
      const target = event.target;
      if (target.closest('.product-wishlist, .btn-add-cart')) return;

      currentModalViews = views;
      currentViewIndex = 0; // Siempre empezar desde "frente"
      modalTitle.textContent = card.dataset.name || 'Producto';
      modalDescription.textContent = card.dataset.description || '';
      modalPrice.textContent = card.dataset.formattedPrice || `$${card.dataset.price || '0'}`;
      const stockNum = parseInt(card.dataset.stock || '0', 10);
      const modalBtnAddToCart = document.getElementById('modalBtnAddToCart');
      if (stockNum <= 0) {
        modalStock.className = 'product-modal-stock badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1';
        modalStock.style.fontSize = '0.78rem';
        modalStock.innerHTML = `<i class="bi bi-x-circle-fill text-danger me-1"></i> Agotado (Sin Stock)`;
        if (modalBtnAddToCart) {
          modalBtnAddToCart.disabled = true;
          modalBtnAddToCart.classList.add('disabled', 'opacity-50');
          modalBtnAddToCart.innerHTML = `<i class="bi bi-slash-circle me-2"></i> Juguete Sin Stock`;
        }
      } else {
        modalStock.className = 'product-modal-stock badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1';
        modalStock.style.fontSize = '0.78rem';
        modalStock.innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i> Stock: ${stockNum} dispon.`;
        if (modalBtnAddToCart) {
          modalBtnAddToCart.disabled = false;
          modalBtnAddToCart.classList.remove('disabled', 'opacity-50');
          modalBtnAddToCart.innerHTML = `<i class="bi bi-cart-plus-fill fs-5 me-2"></i> Agregar al Carrito`;
        }
      }

      const modalCategory = document.getElementById('modalProductCategory');
      if (modalCategory) modalCategory.textContent = card.dataset.categoria || '';

      if (productModalEl) {
        productModalEl.dataset.activeCardId = card.id;
      }

      setModalView(0);
      openProductModal();
    });

    card.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        card.click();
      }
    });
  });

  // Botón "Agregar al Carrito" dentro del Modal de Producto Expandido
  const modalBtnAddToCart = document.getElementById('modalBtnAddToCart');
  if (modalBtnAddToCart) {
    modalBtnAddToCart.addEventListener('click', function () {
      const activeCardId = productModalEl?.dataset?.activeCardId;
      const card = activeCardId ? document.getElementById(activeCardId) : null;
      let product = null;

      if (card) {
        const rawPrice = parseFloat(card.dataset.price || card.dataset.rawPrice || 0);
        const priceText = card.dataset.formattedPrice || card.querySelector('.product-price')?.textContent?.trim() || `$${rawPrice}`;

        product = {
          id: card.dataset.id || (card.id ? card.id.replace('product-', '') : Math.random().toString(36).substr(2, 9)),
          name: card.dataset.name || card.dataset.nombre || card.querySelector('h3')?.textContent?.trim() || 'Juguete',
          price: rawPrice || priceText,
          formattedPrice: priceText,
          category: card.dataset.categoria || card.querySelector('.product-category-tag')?.textContent?.trim() || 'General',
          image: card.dataset.image || card.querySelector('.product-visual')?.src || '',
          emoji: '🧸'
        };
      }

      if (window.toyCart && product) {
        window.toyCart.add(product);
        if (window.isUserLoggedIn) {
          if (bsProductModal) bsProductModal.hide();
          window.toyCart.open();
        }
      }
    });
  }

  // Flecha derecha → avanza en el ciclo
  if (modalArrowRight) {
    modalArrowRight.addEventListener('click', function () {
      setModalView(currentViewIndex + 1);
    });
  }

  // Flecha izquierda → retrocede en el ciclo
  if (modalArrowLeft) {
    modalArrowLeft.addEventListener('click', function () {
      setModalView(currentViewIndex - 1);
    });
  }

  // El cierre del modal lo maneja Bootstrap via data-bs-dismiss="modal"

  // 5. Formulario de Newsletter
  const newsletterForm = document.getElementById('newsletterForm');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const emailInput = document.getElementById('newsletter-email');
      if (emailInput && emailInput.value) {
        const btn = this.querySelector('button');
        if (btn) {
          btn.textContent = '¡Suscrito! ✓';
          btn.style.background = '#10B981';
          emailInput.value = '';
          setTimeout(() => {
            btn.textContent = 'Suscribirme';
            btn.style.background = '';
          }, 3000);
        }
      }
    });
  }

  // 5. Animación de revelado al hacer scroll (Scroll Reveal)
  const categoryCards = document.querySelectorAll('.category-card, .product-card, .why-card');
  if (categoryCards.length > 0) {
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -40px 0px' };
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    categoryCards.forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
      observer.observe(el);
    });
  }

  // 6. Validación y envío asíncrono del formulario de login del modal
  const loginForm = document.getElementById('modalLoginForm');
  if (loginForm) {
    const alertBox = document.getElementById('loginAlert');

    loginForm.addEventListener('submit', async function (event) {
      event.preventDefault();

      if (alertBox) {
        alertBox.style.display = 'none';
        alertBox.textContent = '';
      }

      if (!loginForm.checkValidity()) {
        event.stopPropagation();
        loginForm.classList.add('was-validated');
        return;
      }
      loginForm.classList.add('was-validated');

      const submitBtn = loginForm.querySelector('[type="submit"]');
      const originalHtml = submitBtn ? submitBtn.innerHTML : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Iniciando sesión...';
      }

      try {
        const formData = new FormData(loginForm);
        const response = await fetch('include/login_process.php', {
          method: 'POST',
          body: formData
        });

        if (!response.ok) {
          throw new Error('Error en el servidor');
        }

        const data = await response.json();

        if (data.success) {
          if (alertBox) {
            alertBox.className = 'alert alert-success mb-3';
            alertBox.textContent = '🎉 ' + data.message;
            alertBox.style.display = 'block';
          }
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          if (alertBox) {
            alertBox.className = 'alert alert-danger mb-3';
            alertBox.textContent = '⚠️ ' + data.message;
            alertBox.style.display = 'block';
          }
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
          }
        }
      } catch (err) {
        if (alertBox) {
          alertBox.className = 'alert alert-danger mb-3';
          alertBox.textContent = '⚠️ Error de conexión con el servidor. Intenta de nuevo.';
          alertBox.style.display = 'block';
        }
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalHtml;
        }
      }
    });
  }

  // 7. Flujo de recuperación de contraseña
  const forgotPasswordLink = document.getElementById('forgotPasswordLink');
  const forgotPasswordForm = document.getElementById('forgotPasswordForm');
  const resetPasswordForm = document.getElementById('resetPasswordForm');
  const forgotPasswordAlert = document.getElementById('forgotPasswordAlert');
  const modalLoginForm = document.getElementById('modalLoginForm');

  if (forgotPasswordLink && forgotPasswordForm && resetPasswordForm) {
    forgotPasswordLink.addEventListener('click', function (event) {
      event.preventDefault();
      if (modalLoginForm) modalLoginForm.style.display = 'none';
      forgotPasswordForm.style.display = 'block';
      resetPasswordForm.style.display = 'none';
      forgotPasswordAlert.style.display = 'none';
      forgotPasswordAlert.textContent = '';
      forgotPasswordAlert.className = 'alert mb-3';
    });

    forgotPasswordForm.addEventListener('submit', async function (event) {
      event.preventDefault();
      if (!forgotPasswordForm.checkValidity()) {
        forgotPasswordForm.classList.add('was-validated');
        return;
      }

      const submitBtn = forgotPasswordForm.querySelector('[type="submit"]');
      const originalHtml = submitBtn ? submitBtn.innerHTML : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
      }

      const formData = new FormData(forgotPasswordForm);
      formData.append('action', 'send');

      try {
        const response = await fetch('include/forgot_password.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();

        if (data.success) {
          forgotPasswordAlert.className = 'alert alert-success mb-3';
          forgotPasswordAlert.textContent = data.message;
          forgotPasswordAlert.style.display = 'block';
          forgotPasswordForm.style.display = 'none';
          resetPasswordForm.style.display = 'block';
        } else {
          forgotPasswordAlert.className = 'alert alert-danger mb-3';
          forgotPasswordAlert.textContent = '⚠️ ' + data.message;
          forgotPasswordAlert.style.display = 'block';
        }
      } catch (error) {
        forgotPasswordAlert.className = 'alert alert-danger mb-3';
        forgotPasswordAlert.textContent = '⚠️ Error de conexión con el servidor.';
        forgotPasswordAlert.style.display = 'block';
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalHtml;
        }
      }
    });

    resetPasswordForm.addEventListener('submit', async function (event) {
      event.preventDefault();
      if (!resetPasswordForm.checkValidity()) {
        resetPasswordForm.classList.add('was-validated');
        return;
      }

      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      if (newPassword.length < 8 || !/[A-Za-z]/.test(newPassword) || !/[0-9]/.test(newPassword)) {
        forgotPasswordAlert.className = 'alert alert-danger mb-3';
        forgotPasswordAlert.textContent = '⚠️ La contraseña debe tener al menos 8 caracteres y combinar letras y números.';
        forgotPasswordAlert.style.display = 'block';
        return;
      }
      if (newPassword !== confirmPassword) {
        forgotPasswordAlert.className = 'alert alert-danger mb-3';
        forgotPasswordAlert.textContent = '⚠️ Las contraseñas no coinciden.';
        forgotPasswordAlert.style.display = 'block';
        return;
      }

      const submitBtn = resetPasswordForm.querySelector('[type="submit"]');
      const originalHtml = submitBtn ? submitBtn.innerHTML : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Actualizando...';
      }

      const formData = new FormData(resetPasswordForm);
      formData.append('action', 'reset');

      try {
        const response = await fetch('include/forgot_password.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();

        if (data.success) {
          forgotPasswordAlert.className = 'alert alert-success mb-3';
          forgotPasswordAlert.textContent = data.message;
          forgotPasswordAlert.style.display = 'block';
          resetPasswordForm.reset();
        } else {
          forgotPasswordAlert.className = 'alert alert-danger mb-3';
          forgotPasswordAlert.textContent = '⚠️ ' + data.message;
          forgotPasswordAlert.style.display = 'block';
        }
      } catch (error) {
        forgotPasswordAlert.className = 'alert alert-danger mb-3';
        forgotPasswordAlert.textContent = '⚠️ Error de conexión con el servidor.';
        forgotPasswordAlert.style.display = 'block';
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalHtml;
        }
      }
    });
  }

  // 8. Validación y envío asíncrono del formulario de registro
  const regForm = document.getElementById('registrar');
  if (regForm) {
    let alertBox = document.getElementById('regAlert');
    if (!alertBox) {
      alertBox = document.createElement('div');
      alertBox.id = 'regAlert';
      alertBox.style.display = 'none';
      alertBox.className = 'alert mb-3';
      regForm.parentNode.insertBefore(alertBox, regForm);
    }

    regForm.addEventListener('submit', async function (event) {
      event.preventDefault();

      if (!regForm.checkValidity()) {
        regForm.classList.add('was-validated');
        return;
      }
      regForm.classList.add('was-validated');

      const pw1 = document.getElementById('pw1') ? document.getElementById('pw1').value : '';
      const pw2 = document.getElementById('pw2') ? document.getElementById('pw2').value : '';

      if (pw1.length < 8) {
        showAlert('La contraseña debe tener al menos 8 caracteres.', 'danger');
        return;
      }
      const hasLetter = /[A-Za-z]/.test(pw1);
      const hasNumber = /[0-9]/.test(pw1);
      if (!hasLetter || !hasNumber) {
        showAlert('La contraseña debe contener tanto letras como números.', 'danger');
        return;
      }
      if (pw1 !== pw2) {
        showAlert('Las contraseñas no coinciden.', 'danger');
        return;
      }

      const submitBtn = regForm.querySelector('[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando cuenta...';
      }

      try {
        const formData = new FormData(regForm);
        const response = await fetch('include/registro_process.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          showAlert('🎉 ' + data.message, 'success');
          regForm.reset();
          regForm.classList.remove('was-validated');
          setTimeout(() => { window.location.href = 'index.php'; }, 2500);
        } else {
          showAlert('⚠️ ' + data.message, 'danger');
        }
      } catch (err) {
        showAlert('Error de conexión. Intenta nuevamente.', 'danger');
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="bi bi-person-plus-fill" style="font-size:1.15rem;"></i> Crear cuenta';
        }
      }
    });

    function showAlert(message, type) {
      alertBox.className = 'alert alert-' + type + ' mb-3';
      alertBox.textContent = message;
      alertBox.style.display = 'block';
      alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
      if (type === 'danger') {
        setTimeout(() => { alertBox.style.display = 'none'; }, 5000);
      }
    }
  }

  // 8. Marcar checkbox de términos desde el modal automáticamente
  const btnAceptarTerms = document.getElementById('btnAceptarTerms');
  if (btnAceptarTerms) {
    btnAceptarTerms.addEventListener('click', function () {
      const termsCheckbox = document.getElementById('terms');
      if (termsCheckbox) {
        termsCheckbox.checked = true;
      }
    });
  }

  // 9. Botón de búsqueda en el Navbar
  const btnSearch = document.getElementById('btn-search');
  if (btnSearch) {
    btnSearch.addEventListener('click', function (e) {
      e.preventDefault();
      const term = prompt('¿Qué juguete estás buscando?');
      if (term && term.trim() !== '') {
        window.location.href = 'categoria.php?c=' + encodeURIComponent(term.trim());
      }
    });
  }

  // 10. Botones de "Agregar al carrito"
  document.querySelectorAll('.btn-add-cart').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation(); // Evitar abrir el modal del producto
      const card = this.closest('.product-card');
      if (!card) return;

      const rawPrice = parseFloat(card.dataset.price || card.dataset.rawPrice || 0);
      const priceText = card.dataset.formattedPrice || card.querySelector('.product-price')?.textContent?.trim() || `$${rawPrice}`;

      const product = {
        id: card.dataset.id || (card.id ? card.id.replace('product-', '') : Math.random().toString(36).substr(2, 9)),
        name: card.dataset.name || card.dataset.nombre || card.querySelector('h3')?.textContent?.trim() || 'Juguete',
        price: rawPrice || priceText,
        formattedPrice: priceText,
        category: card.dataset.categoria || card.querySelector('.product-category-tag')?.textContent?.trim() || 'General',
        image: card.dataset.image || card.querySelector('.product-visual')?.src || '',
        emoji: '🧸'
      };

      if (window.toyCart) {
        window.toyCart.add(product);
        if (window.isUserLoggedIn) {
          window.toyCart.open();
        }
      }
    });
  });

  // 11. Control de Navegación Cíclica y Temporizador de 10s del Carrusel
  const track = document.getElementById('productsCarouselTrack');
  const btnPrev = document.getElementById('btnProductsPrev');
  const btnNext = document.getElementById('btnProductsNext');

  if (track && btnPrev && btnNext) {
    const getScrollStep = () => {
      const itemWidth = track.querySelector('.products-carousel-item')?.offsetWidth || 280;
      return itemWidth + 22;
    };

    const nextSlide = () => {
      const maxScrollLeft = track.scrollWidth - track.clientWidth;
      const step = getScrollStep();

      // Si estamos al final o muy cerca del final, hacer loop al inicio
      if (Math.ceil(track.scrollLeft) >= maxScrollLeft - 15) {
        track.scrollTo({ left: 0, behavior: 'smooth' });
      } else {
        track.scrollBy({ left: step * 2, behavior: 'smooth' });
      }
    };

    const prevSlide = () => {
      const step = getScrollStep();

      // Si estamos al inicio o muy cerca del inicio, hacer loop al final
      if (track.scrollLeft <= 15) {
        const maxScrollLeft = track.scrollWidth - track.clientWidth;
        track.scrollTo({ left: maxScrollLeft, behavior: 'smooth' });
      } else {
        track.scrollBy({ left: -(step * 2), behavior: 'smooth' });
      }
    };

    btnNext.addEventListener('click', () => {
      nextSlide();
      resetAutoPlay();
    });

    btnPrev.addEventListener('click', () => {
      prevSlide();
      resetAutoPlay();
    });

    // Auto-avanzar cada 10 segundos (10,000 ms)
    let autoPlayInterval = setInterval(nextSlide, 5000);

    const resetAutoPlay = () => {
      clearInterval(autoPlayInterval);
      autoPlayInterval = setInterval(nextSlide, 5000);
    };

    // Pausar auto-aviso al pasar el mouse sobre el carrusel
    track.addEventListener('mouseenter', () => clearInterval(autoPlayInterval));
    track.addEventListener('mouseleave', () => resetAutoPlay());
  }

  // ── FILTRO Y ORDENAMIENTO POR PRECIO / NOMBRE EN CATEGORÍAS ──
  const sortSelect = document.getElementById('sortPriceSelect');
  const filterInput = document.getElementById('filterNameInput');
  const productsRow = document.getElementById('productsRow');

  if (productsRow) {
    const applyFilterAndSort = () => {
      const sortValue = sortSelect ? sortSelect.value : 'defecto';
      const filterValue = filterInput ? filterInput.value.trim().toLowerCase() : '';
      const noResultsMsg = document.getElementById('noResultsMsg');

      const cols = Array.from(productsRow.querySelectorAll('.product-item-col'));
      if (cols.length === 0) return;

      let visibleCount = 0;

      // 1. Filtrado instantáneo por nombre
      cols.forEach(col => {
        const name = (col.getAttribute('data-name') || '').toLowerCase();
        if (!filterValue || name.includes(filterValue)) {
          col.style.display = '';
          visibleCount++;
        } else {
          col.style.display = 'none';
        }
      });

      // Mostrar u ocultar mensaje si 0 productos coinciden con la búsqueda por nombre
      if (noResultsMsg) {
        if (visibleCount === 0) {
          noResultsMsg.classList.remove('d-none');
        } else {
          noResultsMsg.classList.add('d-none');
        }
      }

      // 2. Ordenamiento por precio, nombre o más recientes
      cols.sort((a, b) => {
        const priceA = parseFloat(a.getAttribute('data-price')) || 0;
        const priceB = parseFloat(b.getAttribute('data-price')) || 0;
        const nameA = (a.getAttribute('data-name') || '').toLowerCase();
        const nameB = (b.getAttribute('data-name') || '').toLowerCase();
        const idA = parseInt(a.getAttribute('data-id')) || 0;
        const idB = parseInt(b.getAttribute('data-id')) || 0;

        if (sortValue === 'precio_asc') {
          return priceA - priceB;
        } else if (sortValue === 'precio_desc') {
          return priceB - priceA;
        } else if (sortValue === 'nombre_asc') {
          return nameA.localeCompare(nameB, 'es', { sensitivity: 'base' });
        } else if (sortValue === 'nombre_desc') {
          return nameB.localeCompare(nameA, 'es', { sensitivity: 'base' });
        } else {
          return idB - idA; // Por defecto: más recientes primero (id mayor primero)
        }
      });

      // 3. Reordenar los elementos en el DOM inmediatamente
      cols.forEach(col => productsRow.appendChild(col));

      // 4. Actualizar la URL de forma silenciosa para sincronizar el estado
      try {
        const url = new URL(window.location.href);
        if (sortValue === 'defecto' || !sortValue) {
          url.searchParams.delete('sort');
        } else {
          url.searchParams.set('sort', sortValue);
        }
        window.history.replaceState(null, '', url.toString());
      } catch (e) {}
    };

    // Registrar eventos para cambio de select y tipeo en el buscador
    if (sortSelect) {
      sortSelect.addEventListener('change', applyFilterAndSort);
    }
    if (filterInput) {
      filterInput.value = ''; // Limpiar buscador al cambiar/cargar categoría
      filterInput.addEventListener('input', applyFilterAndSort);
      filterInput.addEventListener('keyup', applyFilterAndSort);
    }

    // Ejecutar inmediatamente al cargar la página
    applyFilterAndSort();
  }

  // ── CONTACT FORMS SUBMISSION ──
  const mainContactForm = document.getElementById('mainContactForm');
  if (mainContactForm) {
    mainContactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const alertBox = document.getElementById('contactFormAlert');
      if (alertBox) {
        alertBox.className = 'alert alert-success mb-3';
        alertBox.innerHTML = '✅ <strong>¡Mensaje enviado con éxito!</strong> Gracias por contactar a TOYS NOVA, te responderemos a la brevedad.';
        alertBox.style.display = 'block';
      }
      mainContactForm.reset();
    });
  }

  const modalContactForm = document.getElementById('modalContactForm');
  if (modalContactForm) {
    modalContactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const alertBox = document.getElementById('modalContactFormAlert');
      if (alertBox) {
        alertBox.className = 'alert alert-success mb-2';
        alertBox.innerHTML = '✅ <strong>¡Mensaje enviado!</strong> Nos pondremos en contacto contigo pronto.';
        alertBox.style.display = 'block';
      }
      modalContactForm.reset();
    });
  }

});
