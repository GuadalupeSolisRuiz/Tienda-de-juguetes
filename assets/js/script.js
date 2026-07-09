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
  window.location.href = 'include/logout.php';
}


// ── EVENTOS DOMContentLoaded ──
document.addEventListener('DOMContentLoaded', function () {

  // 1. Desplazamiento suave para enlaces de anclaje (Smooth Scroll)
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
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

  // 4. Formulario de Newsletter
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

  // 7. Validación y envío asíncrono del formulario de registro
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


});
