<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tienda de Juguetes - Registro</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap"
    rel="stylesheet" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet" />
  <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body>

  <?php include "views/navbar.php"; ?>


  <!-- ── HERO ── -->
  <section class="hero-section">

    <!-- LEFT -->
    <div class="hero-left">
      <div class="decorations">
        <div class="deco-star">⭐</div>
        <svg class="deco-cloud1" viewBox="0 0 120 70" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="20" y="25" width="80" height="35" rx="17.5" fill="#BAE6FD" />
          <circle cx="45" cy="30" r="20" fill="#BAE6FD" />
          <circle cx="75" cy="25" r="22" fill="#BAE6FD" />
          <circle cx="95" cy="35" r="15" fill="#BAE6FD" />
        </svg>
        <svg class="deco-cloud2" viewBox="0 0 100 60" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="15" y="22" width="70" height="30" rx="15" fill="#BAE6FD" />
          <circle cx="35" cy="27" r="18" fill="#BAE6FD" />
          <circle cx="62" cy="22" r="19" fill="#BAE6FD" />
          <circle cx="80" cy="30" r="12" fill="#BAE6FD" />
        </svg>
        <div class="deco-star-pink">⭐</div>
      </div>

      <div class="hero-copy">
        <h1>¡Crea tu cuenta<br>y comienza<br>a jugar!</h1>
        <p>Regístrate para descubrir los mejores juguetes, ofertas exclusivas y una experiencia personalizada.</p>
      </div>

      <div class="hero-image">
        <div class="toys-container">
          <span class="toy-bear">🧸</span>
          <span class="toy-ball">🏀</span>
        </div>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="hero-right">
      <div class="form-card">
        <form id="registrar" novalidate>

          <!-- Header -->
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="form-avatar">
              <i class="bi bi-person"></i>
            </div>
            <div>
              <h2 class="mb-0">Registro de usuario</h2>
              <p class="subtitle mb-0">Completa el formulario para crear tu cuenta.</p>
            </div>
          </div>

          <hr class="mb-4">

          <!-- Name Row -->
          <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
              <label class="form-label">Nombre completo <span class="required-star">*</span></label>
              <div class="input-icon-wrap">
                <i class="bi bi-person field-icon"></i>
                <input type="text" id="nombreCompleto" name="nombre" class="form-control"
                  placeholder="Ingresa tu nombre completo" required />
                <div class="invalid-feedback">
                  Por favor ingresa tu nombre completo.
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <label class="form-label">Apellido completo <span class="required-star">*</span></label>
              <div class="input-icon-wrap">
                <i class="bi bi-person field-icon"></i>
                <input type="text" name="apellido" class="form-control" placeholder="Ingresa tu apellido completo"
                  required />
                <div class="invalid-feedback">
                  Por favor ingresa tu apellido completo.
                </div>
              </div>
            </div>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label class="form-label">Correo electrónico <span class="required-star">*</span></label>
            <div class="input-icon-wrap">
              <i class="bi bi-envelope field-icon"></i>
              <input type="email" id="correoReg" name="correo" class="form-control" placeholder="ejemplo@correo.com"
                required />
              <div class="invalid-feedback">
                Por favor ingresa un correo electrónico válido.
              </div>
            </div>
          </div>

          <!-- Password Row -->
          <div class="row g-3">
            <div class="col-12 col-sm-6">
              <label class="form-label">Contraseña <span class="required-star">*</span></label>
              <div class="input-icon-wrap">
                <i class="bi bi-lock field-icon"></i>
                <input type="password" id="pw1" name="contrasena" class="form-control has-toggle"
                  placeholder="Crea una contraseña" required />
                <button class="toggle-pw" type="button" onclick="togglePw('pw1', this)">
                  <i class="bi bi-eye"></i>
                </button>
                <div class="invalid-feedback">
                  Por favor ingresa una contraseña.
                </div>
              </div>
              <p class="hint-text">Mínimo 8 caracteres, incluye letras y números.</p>
            </div>
            <div class="col-12 col-sm-6">
              <label class="form-label">Confirmar contraseña <span class="required-star">*</span></label>
              <div class="input-icon-wrap">
                <i class="bi bi-lock field-icon"></i>
                <input type="password" id="pw2" name="contrasena2" class="form-control has-toggle"
                  placeholder="Repite tu contraseña" required />
                <button class="toggle-pw" type="button" onclick="togglePw('pw2', this)">
                  <i class="bi bi-eye"></i>
                </button>
                <div class="invalid-feedback">
                  Por favor confirma tu contraseña.
                </div>
              </div>
            </div>
          </div>

          <!-- Teléfono -->
          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <div class="input-icon-wrap">
              <i class="bi bi-telephone field-icon"></i>
              <input type="tel" name="telefono" class="form-control" placeholder="ej. 5512345678" maxlength="70" />
            </div>
          </div>

          <!-- Date & Gender Row -->
          <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
              <label class="form-label">Fecha de nacimiento</label>
              <div class="input-icon-wrap">
                <i class="bi bi-calendar3 field-icon"></i>
                <input type="date" id="dob" class="form-control" onchange="this.classList.add('filled')" />
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <label class="form-label">Género</label>
              <div class="input-icon-wrap">
                <i class="bi bi-gender-ambiguous field-icon"></i>
                <select id="genero" class="form-select placeholder-mode"
                  onchange="this.classList.remove('placeholder-mode')">
                  <option value="" disabled selected>Selecciona una opción</option>
                  <option value="masculino">Masculino</option>
                  <option value="femenino">Femenino</option>
                  <option value="otro">Otro</option>
                  <option value="prefiero-no-decir">Prefiero no decir</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Terms -->
          <div class="form-check d-flex align-items-center gap-2 mb-4 mt-2">
            <input class="form-check-input" type="checkbox" id="terms" />
            <label class="form-check-label" for="terms">
              Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Términos y Condiciones</a> y la
              <a href="#">Política de Privacidad</a>.
            </label>
          </div>

          <!-- Submit -->
          <button type="submit" name="registrar" class="btn-register">
            <i class="bi bi-person-plus-fill" style="font-size:1.15rem;"></i>
            Crear cuenta
          </button>
        </form>

        <!-- Login link -->
        <p class="login-link">¿Ya tienes una cuenta? <a href="#" data-bs-toggle="modal"
            data-bs-target="#loginModal">Inicia sesión</a></p>
      </div>
    </div>
  </section>


  <!-- ── FOOTER BAR ── -->
  <div class="footer-bar">
    <div class="row row-cols-2 row-cols-lg-4 g-3">

      <div class="col">
        <div class="footer-item">
          <div class="footer-icon purple">
            <i class="bi bi-truck"></i>
          </div>
          <div class="footer-text">
            <strong>Envíos a todo el país</strong>
            <span>Rápidos y seguros</span>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="footer-item">
          <div class="footer-icon pink">
            <i class="bi bi-tags-fill"></i>
          </div>
          <div class="footer-text">
            <strong>Las mejores marcas</strong>
            <span>Calidad garantizada</span>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="footer-item">
          <div class="footer-icon green">
            <i class="bi bi-shield-check"></i>
          </div>
          <div class="footer-text">
            <strong>Compra segura</strong>
            <span>Tus datos protegidos</span>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="footer-item">
          <div class="footer-icon blue">
            <i class="bi bi-headset"></i>
          </div>
          <div class="footer-text">
            <strong>Atención al cliente</strong>
            <span>Estamos para ayudarte</span>
          </div>
        </div>
      </div>

    </div>
  </div>


  <!-- ── MODAL: Términos y Condiciones ── -->
  <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">

        <!-- Header -->
        <div class="modal-header text-white" style="background: linear-gradient(135deg, #7237b5, #7237b5);">
          <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center rounded-circle"
              style="width:42px; height:42px; background:rgba(255,255,255,0.25);">
              <i class="bi bi-file-earmark-text fs-5"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-0" id="termsModalLabel">Términos y Condiciones</h5>
              <small style="opacity:0.85;">Tienda Virtual · Versión vigente</small>
            </div>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Body -->
        <div class="modal-body px-4 py-3">

          <div class="mb-4">
            <h6 class="fw-bold" style="color:#7237b5;">
              <i class="bi bi-1-circle-fill me-2"></i>Uso de la plataforma
            </h6>
            <p class="text-muted small">La tienda virtual tiene como finalidad permitir la consulta y compra de
              productos tecnológicos mediante una plataforma web. El usuario se compromete a utilizar el sistema de
              manera responsable y únicamente para las funciones permitidas dentro de la plataforma.</p>
          </div>
          <hr>

          <div class="mb-4">
            <h6 class="fw-bold" style="color:#7237b5;">
              <i class="bi bi-2-circle-fill me-2"></i>Registro de usuarios
            </h6>
            <p class="text-muted small">Para realizar compras dentro del sistema será necesario crear una cuenta
              proporcionando información válida y actualizada. El usuario será responsable de mantener la
              confidencialidad de su contraseña y de la actividad realizada desde su cuenta.</p>
          </div>
          <hr>

          <div class="mb-4">
            <h6 class="fw-bold" style="color:#7237b5;">
              <i class="bi bi-3-circle-fill me-2"></i>Productos y disponibilidad
            </h6>
            <p class="text-muted small">Los productos mostrados dentro de la tienda estarán sujetos a disponibilidad. La
              información relacionada con precios, imágenes y descripciones podrá actualizarse en cualquier momento
              dentro del sistema.</p>
          </div>
          <hr>

          <div class="mb-4">
            <h6 class="fw-bold" style="color:#7237b5;">
              <i class="bi bi-4-circle-fill me-2"></i>Compras y pagos
            </h6>
            <p class="text-muted small">Las compras realizadas dentro de la plataforma deberán ser confirmadas mediante
              el proceso de pago correspondiente. El sistema registrará la información relacionada con pedidos, pagos y
              estado de compra para mantener el control de las operaciones.</p>
          </div>
          <hr>

          <div class="mb-4">
            <h6 class="fw-bold" style="color:#7237b5;">
              <i class="bi bi-5-circle-fill me-2"></i>Seguridad de la información
            </h6>
            <p class="text-muted small">El sistema implementará medidas de seguridad para proteger la información de los
              usuarios, incluyendo validación de datos y protección de credenciales. Los datos almacenados serán
              utilizados únicamente para el funcionamiento de la tienda virtual.</p>
          </div>
          <hr>

          <div class="mb-4">
            <h6 class="fw-bold" style="color:#7237b5;">
              <i class="bi bi-6-circle-fill me-2"></i>Restricciones de uso
            </h6>
            <p class="text-muted small">Queda prohibido el uso indebido de la plataforma, incluyendo intentos de acceso
              no autorizado, alteración de información o actividades que afecten el funcionamiento del sistema.</p>
          </div>
          <hr>

          <div class="mb-4">
            <h6 class="fw-bold" style="color:#7237b5;">
              <i class="bi bi-7-circle-fill me-2"></i>Modificaciones del sistema
            </h6>
            <p class="text-muted small">La administración podrá realizar actualizaciones, modificaciones o mantenimiento
              de la plataforma cuando sea necesario.</p>
          </div>
          <hr>

          <div class="mb-2">
            <h6 class="fw-bold" style="color:#7237b5;">
              <i class="bi bi-8-circle-fill me-2"></i>Aceptación de términos
            </h6>
            <p class="text-muted small">Al utilizar la tienda virtual, el usuario acepta los presentes términos y
              condiciones de uso del sistema.</p>
          </div>

        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-sm text-white fw-bold" id="btnAceptarTerms" data-bs-dismiss="modal"
            style="background:#7237b5; border:none;">
            <i class="bi bi-check2-circle me-1"></i>Acepto los términos
          </button>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

  <script>
    // ── Mostrar/ocultar contraseña ──
    function togglePw(id, btn) {
      const input = document.getElementById(id);
      const icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
      } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
      }
    }

    // ── Envío del formulario con fetch (POST → JSON) ──
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('registrar');
      if (!form) return;

      // Crear contenedor de alerta si no existe
      let alertBox = document.getElementById('regAlert');
      if (!alertBox) {
        alertBox = document.createElement('div');
        alertBox.id = 'regAlert';
        alertBox.style.display = 'none';
        alertBox.className = 'alert mb-3';
        form.parentNode.insertBefore(alertBox, form);
      }

      form.addEventListener('submit', async function (event) {
        event.preventDefault();

        // Validación nativa del navegador
        if (!form.checkValidity()) {
          form.classList.add('was-validated');
          return;
        }
        form.classList.add('was-validated');

        // Verificar que las contraseñas coincidan en cliente
        const pw1 = document.getElementById('pw1').value;
        const pw2 = document.getElementById('pw2').value;
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

        // Deshabilitar botón durante la petición
        const submitBtn = form.querySelector('[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando cuenta...';

        try {
          const formData = new FormData(form);
          const response = await fetch('include/registro_process.php', {
            method: 'POST',
            body: formData
          });

          const data = await response.json();

          if (data.success) {
            showAlert('🎉 ' + data.message, 'success');
            form.reset();
            form.classList.remove('was-validated');
            // Redirigir al inicio luego de 2.5 segundos
            setTimeout(() => { window.location.href = 'index.php'; }, 2500);
          } else {
            showAlert('⚠️ ' + data.message, 'danger');
          }
        } catch (err) {
          showAlert('Error de conexión. Intenta nuevamente.', 'danger');
        } finally {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="bi bi-person-plus-fill" style="font-size:1.15rem;"></i> Crear cuenta';
        }
      });

      function showAlert(message, type) {
        alertBox.className = 'alert alert-' + type + ' mb-3';
        alertBox.textContent = message;
        alertBox.style.display = 'block';
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Ocultar automáticamente los errores después de 5s
        if (type === 'danger') {
          setTimeout(() => { alertBox.style.display = 'none'; }, 5000);
        }
      }
    });

    // ── Al aceptar términos desde el modal, marcar el checkbox automáticamente ──
    document.getElementById('btnAceptarTerms').addEventListener('click', function () {
      document.getElementById('terms').checked = true;
    });
  </script>
</body>

</html>