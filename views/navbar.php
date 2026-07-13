<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$usuario_actual = null;
$mostrarBienvenidaReactivacion = false;
if (isset($_SESSION['usuario_id'])) {
  include __DIR__ . '/../include/conect.php';

  $stmt = $conexion->prepare('SELECT nombre, apellido, correo, telefono FROM usuarios WHERE id_usuario = ?');
  $stmt->bind_param('i', $_SESSION['usuario_id']);
  $stmt->execute();
  $resultado = $stmt->get_result();
  $usuario_actual = $resultado->fetch_assoc();
  $stmt->close();
  $conexion->close();
}

$isIndexPage = basename($_SERVER['PHP_SELF']) === 'index.php';
if ($isIndexPage && isset($_SESSION['mostrar_bienvenida_reactivacion']) && $_SESSION['mostrar_bienvenida_reactivacion']) {
  $mostrarBienvenidaReactivacion = true;
  unset($_SESSION['mostrar_bienvenida_reactivacion']);
}
?>
<!-- ── NAVBAR ── -->
<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
  <div class="container-fluid px-4" style="background-color: #ffffff;" data-bs-theme="primary" >

    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php" id="logo">
      <span class="logo-icon">🐻</span>
      <span class="logo-text">
        <span class="logo-top">TOYS</span>
        <span class="logo-bottom">NOVA</span>
      </span>
    </a>

    <!-- Toggler (mobile) -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav links -->
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto gap-lg-3">
        <li class="nav-item"><a class="nav-link" href="index.php" id="nav-inicio">Inicio</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="nav-juguetes">Juguetes</a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
            <li><a class="dropdown-item" href="#" id="nav-ninas">Niñas</a></li>
            <li><a class="dropdown-item" href="#" id="nav-ninos">Niños</a></li>
            <li><a class="dropdown-item" href="#" id="nav-bebes">Bebés</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="nav-categorias">Categorías</a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
            <li><a class="dropdown-item" href="#" id="nav-educativos">Educativos</a></li>
            <li><a class="dropdown-item" href="#" id="nav-electronicos">Electrónicos</a></li>
            <li><a class="dropdown-item" href="#" id="nav-peluches">Peluches</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="#" id="nav-ofertas">Ofertas</a></li>
        <li class="nav-item"><a class="nav-link" href="#" id="nav-nosotros">Nosotros</a></li>
        <li class="nav-item"><a class="nav-link" href="#" id="nav-contacto">Contacto</a></li>
        <?php if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_rol']) && strtolower($_SESSION['usuario_rol']) === 'administrador'): ?>
          <li class="nav-item">
            <a class="nav-link fw-semibold nav-gestion-pill" href="gestion.php" id="nav-gestion"
              style="border: none; background: transparent; padding: 0.4rem 0.8rem; border-radius: 50px; background-color: rgba(124, 58, 237, 0.17); color: #7C3AED; transition: all 0.2s ease;">
              Gestión
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Icons -->
      <div class="d-flex align-items-center gap-3 ms-lg-3 mt-2 mb-2 mt-lg-0">
        <button class="nav-icon-btn" id="btn-search"><i class="bi bi-search"></i></button>

        <?php if (isset($_SESSION['usuario_id'])): ?>
          <!-- Logged in user dropdown -->
          <div class="dropdown">
            <button class="nav-icon-btn dropdown-toggle d-flex align-items-center gap-2" id="userDropdown"
              data-bs-toggle="dropdown" aria-expanded="false"
              style="border: none; background: transparent; padding: 0.25rem 0.75rem; border-radius: 50px; background-color: rgba(124, 58, 237, 0.08); color: #7C3AED; transition: all 0.2s ease;">
              <i class="bi bi-person-fill fs-5"></i>
              <span class="d-none d-md-inline fw-semibold" style="font-size: 0.9rem;">
                <a href="#" class="text-decoration-none text-dark"
                  onclick="event.preventDefault(); event.stopPropagation(); const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('profileModal')); modal.show();"
                  style="color: inherit;">
                  <?php
                  if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'cliente') {
                    echo "Bienvenido " . htmlspecialchars($_SESSION['usuario_nombre']);
                  } else {
                    echo htmlspecialchars($_SESSION['usuario_nombre']) . " (" . htmlspecialchars($_SESSION['usuario_rol']) . ")";
                  }
                  ?>
                </a>
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" aria-labelledby="userDropdown"
              style="border-radius: 12px; min-width: 200px; background-color: #fff; z-index: 1050;">
              <li class="px-3 py-2 border-bottom mb-2">
                <p class="mb-0 fw-bold text-dark text-capitalize" style="font-size: 0.9rem;">
                  <?php echo htmlspecialchars($_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido']); ?>
                </p>
                <p class="mb-0 text-muted small text-truncate" style="font-size: 0.75rem;">
                  <?php echo htmlspecialchars($_SESSION['usuario_correo']); ?>
                </p>
                <span class="badge mt-1 text-capitalize"
                  style="font-size: 0.65rem; background-color: var(--purple); color: #fff;"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></span>
              </li>
              <li>
                <a class="dropdown-item rounded d-flex align-items-center gap-2 py-2" href="#"
                  data-bs-toggle="modal" data-bs-target="#profileModal"
                  onclick="event.preventDefault(); event.stopPropagation(); const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('profileModal')); modal.show();"
                  style="font-size: 0.9rem;">
                  <i class="bi bi-person-gear"></i> Editar perfil
                </a>
              </li>
              <li>
                <a class="dropdown-item rounded d-flex align-items-center gap-2 py-2 text-danger fw-semibold"
                  href="include/logout.php" style="font-size: 0.9rem;">
                  <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                </a>
              </li>
            </ul>
          </div>
        <?php else: ?>
          <!-- Logged out state -->
          <button class="nav-icon-btn" id="btn-user" data-bs-toggle="modal" data-bs-target="#loginModal"><i
              class="bi bi-person"></i></button>
        <?php endif; ?>

        <?php if (isset($_SESSION['usuario_id'])): ?>
          <a class="nav-link d-flex align-items-center gap-2 text-danger fw-semibold" href="include/logout.php"
            style="font-size: 0.9rem; padding: 0.4rem 0.7rem; border-radius: 999px; background-color: rgba(220, 53, 69, 0.08);">
            <i class="bi bi-box-arrow-right"></i>
            <span class="d-none d-lg-inline">Cerrar sesión</span>
          </a>
        <?php endif; ?>

        <button class="nav-icon-btn cart-wrapper" id="btn-cart">
          <i class="bi bi-cart2"></i>
          <span class="cart-count">0</span>
        </button>
      </div>
    </div>

  </div>
</nav>

<?php if ($mostrarBienvenidaReactivacion): ?>
<div class="modal fade" id="welcomeBackModal" tabindex="-1" aria-labelledby="welcomeBackModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: linear-gradient(135deg, #fff8f0 0%, #fff 100%);">
      <div class="modal-body text-center px-4 py-5">
        <div class="mb-3" style="font-size: 4rem;">🐻</div>
        <h3 class="fw-bold mb-2" id="welcomeBackModalLabel" style="font-family: 'Fredoka One', cursive; color: #7C3AED;">¡Te extrañamos!</h3>
        <p class="mb-0 text-muted" style="font-size: 1.05rem;">Es bueno verte de vuelta.</p>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ── MODAL INICIAR SESIÓN / REGISTRO ── -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0">

      <!-- Header -->
      <div class="modal-header border-0 pb-0 d-flex flex-column align-items-center text-center position-relative pt-4">
        <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"
          aria-label="Cerrar"></button>

        <div class="modal-avatar mb-3">
          <i class="bi bi-person-fill text-white fs-2"></i>
        </div>
        <h3 class="modal-title font-fredoka fw-bold mb-1" id="loginModalLabel">Iniciar sesión</h3>
        <p class="text-muted fs-7 mb-0">¡Qué bueno verte de nuevo por aquí! 🐻</p>
      </div>

      <!-- Body Form -->
      <div class="modal-body px-4 py-3">
        <form id="modalLoginForm" novalidate>
          <!-- Alerta de inicio de sesión -->
          <div id="loginAlert" class="alert alert-danger mb-3"
            style="display: none; font-size: 0.85rem; border-radius: 8px;"></div>

          <!-- Email -->
          <div class="mb-3">
            <label for="modalLoginEmail" class="form-label">Correo electrónico <span
                class="required-star">*</span></label>
            <div class="input-icon-wrap">
              <i class="bi bi-envelope field-icon"></i>
              <input type="email" id="modalLoginEmail" name="correo" class="form-control"
                placeholder="ejemplo@correo.com" required />
              <div class="invalid-feedback">
                Por favor ingresa un correo electrónico válido.
              </div>
            </div>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label for="modalLoginPassword" class="form-label">Contraseña <span class="required-star">*</span></label>
            <div class="input-icon-wrap">
              <i class="bi bi-lock field-icon"></i>
              <input type="password" id="modalLoginPassword" name="contrasena" class="form-control has-toggle"
                placeholder="Ingresa tu contraseña" required />
              <button class="toggle-pw" type="button" onclick="toggleModalPw('modalLoginPassword', this)">
                <i class="bi bi-eye"></i>
              </button>
              <div class="invalid-feedback">
                Por favor ingresa tu contraseña.
              </div>
            </div>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn-register w-100 mt-2 mb-3">
            <i class="bi bi-box-arrow-in-right fs-5 me-2"></i>
            Iniciar sesión
          </button>
        </form>

        <div class="text-center mb-3">
          <button type="button" class="btn btn-link p-0 text-decoration-none" id="forgotPasswordLink">
            ¿Olvidaste tu contraseña?
          </button>
        </div>

        <div id="forgotPasswordAlert" class="alert mb-3" style="display: none; font-size: 0.85rem; border-radius: 8px;"></div>

        <form id="forgotPasswordForm" style="display: none;" novalidate>
          <div class="mb-3">
            <label for="forgotEmail" class="form-label">Correo electrónico <span class="required-star">*</span></label>
            <input type="email" id="forgotEmail" name="correo" class="form-control" placeholder="ejemplo@correo.com" required />
          </div>
          <button type="submit" class="btn btn-outline-primary w-100 mb-3">
            <i class="bi bi-send me-2"></i>Enviar código
          </button>
        </form>

        <form id="resetPasswordForm" style="display: none;" novalidate>
          <div class="mb-3">
            <label for="resetCode" class="form-label">Código de verificación <span class="required-star">*</span></label>
            <input type="text" id="resetCode" name="codigo" class="form-control" placeholder="123456" maxlength="6" required />
          </div>
          <div class="mb-3">
            <label for="newPassword" class="form-label">Nueva contraseña <span class="required-star">*</span></label>
            <input type="password" id="newPassword" name="nueva_contrasena" class="form-control" placeholder="Mínimo 8 caracteres" required />
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirmar contraseña <span class="required-star">*</span></label>
            <input type="password" id="confirmPassword" name="confirmar_contrasena" class="form-control" placeholder="Repite la contraseña" required />
          </div>
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-shield-lock me-2"></i>Actualizar contraseña
          </button>
        </form>

        <!-- Divider -->
        <div class="d-flex align-items-center my-3">
          <hr class="flex-grow-1 border-gray-300">
          <span class="mx-3 text-muted fs-7">¿No tienes cuenta?</span>
          <hr class="flex-grow-1 border-gray-300">
        </div>

        <!-- Register Redirect Link -->
        <a href="registro.php"
          class="btn-secondary-custom w-100 d-flex align-items-center justify-content-center gap-2 py-3"
          id="btn-modal-register" style="border-radius: 12px; font-weight: 700; text-decoration: none;">
          <i class="bi bi-person-plus-fill"></i>
          Registrarse / Crear Cuenta
        </a>
      </div>

    </div>
  </div>
</div>


<?php if (isset($_SESSION['usuario_id'])): ?>
  <!-- ── MODAL DE PERFIL ── -->
  <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background-color: #fff;">
        <div class="modal-header border-0 pb-0 d-flex flex-column align-items-center text-center pt-4">
          <div class="modal-avatar mb-3"
            style="background-color: var(--purple); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);">
            <i class="bi bi-person-gear text-white"></i>
          </div>
          <h4 class="modal-title font-fredoka fw-bold text-dark mb-1" id="profileModalLabel">Editar perfil</h4>
          <p class="text-muted small mb-0 px-3">Actualiza tus datos de registro desde aquí.</p>
        </div>
        <div class="modal-body px-4 py-3">
          <div id="profileAlert" class="alert alert-danger mb-3" style="display: none; font-size: 0.9rem; border-radius: 8px;"></div>
          <form id="profileForm" novalidate>
            <input type="hidden" name="id_usuario" value="<?php echo intval($_SESSION['usuario_id']); ?>">

            <div class="row g-3 mb-3">
              <div class="col-12 col-sm-6">
                <label class="form-label">Nombre <span class="required-star">*</span></label>
                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario_actual['nombre'] ?? ''); ?>" required>
              </div>
              <div class="col-12 col-sm-6">
                <label class="form-label">Apellido <span class="required-star">*</span></label>
                <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($usuario_actual['apellido'] ?? ''); ?>" required>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Correo electrónico <span class="required-star">*</span></label>
              <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($usuario_actual['correo'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Teléfono</label>
              <input type="tel" name="telefono" class="form-control" value="<?php echo htmlspecialchars($usuario_actual['telefono'] ?? ''); ?>" maxlength="70">
            </div>

            <div class="row g-3 mb-3">
              <div class="col-12 col-sm-6">
                <label class="form-label">Nueva contraseña <small class="text-muted">(opcional)</small></label>
                <input type="password" id="profilePassword" name="contrasena" class="form-control has-toggle" placeholder="Deja vacío para mantenerla">
              </div>
              <div class="col-12 col-sm-6">
                <label class="form-label">Confirmar contraseña</label>
                <input type="password" id="profilePasswordConfirm" name="contrasena2" class="form-control has-toggle" placeholder="Repite la nueva contraseña">
              </div>
            </div>

            <button type="submit" class="btn-register w-100 mt-2">
              <i class="bi bi-save me-2"></i> Guardar cambios
            </button>
          </form>

          <hr class="my-4">

          <div class="border border-danger-subtle rounded-4 p-3 bg-light">
            <h6 class="fw-bold text-danger mb-2">
              <i class="bi bi-person-x-fill me-2"></i>Desactivar cuenta
            </h6>
            <p class="small text-muted mb-3">
              Esta acción desactivará tu cuenta. Si tienes pedidos pendientes, no podrás continuar.
            </p>
            <form id="deactivateAccountForm" novalidate>
              <input type="hidden" name="id_usuario" value="<?php echo intval($_SESSION['usuario_id']); ?>">
              <div class="mb-3">
                <label class="form-label small">Escribe <strong>DESACTIVAR</strong> para confirmar</label>
                <input type="text" name="confirmacion" class="form-control" placeholder="DESACTIVAR" required>
              </div>
              <button type="submit" class="btn btn-outline-danger w-100">
                <i class="bi bi-shield-exclamation me-2"></i> Desactivar mi cuenta
              </button>
            </form>

            <div class="mt-3 pt-3 border-top border-danger-subtle">
              <h6 class="fw-bold text-danger mb-2">
                <i class="bi bi-trash3-fill me-2"></i>Eliminar cuenta
              </h6>
              <p class="small text-muted mb-3">
                Esta acción eliminará permanentemente tu cuenta y tus datos. Requiere confirmar con tu contraseña.
              </p>
              <form id="deleteAccountForm" novalidate>
                <input type="hidden" name="id_usuario" value="<?php echo intval($_SESSION['usuario_id']); ?>">
                <div class="mb-3">
                  <label class="form-label small">Contraseña actual</label>
                  <input type="password" name="contrasena" class="form-control" placeholder="Ingresa tu contraseña" required>
                </div>
                <button type="submit" class="btn btn-danger w-100">
                  <i class="bi bi-trash3 me-2"></i> Eliminar cuenta
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── MODAL DE INACTIVIDAD ── -->
  <div class="modal fade" id="inactivityModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="inactivityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background-color: #fff;">
        <div class="modal-header border-0 pb-0 d-flex flex-column align-items-center text-center pt-4">
          <div class="modal-avatar mb-3"
            style="background-color: var(--yellow); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);">
            ⏳
          </div>
          <h4 class="modal-title font-fredoka fw-bold text-dark mb-1" id="inactivityModalLabel">¿Sigues ahí?</h4>
          <p class="text-muted small mb-0 px-3">Tu sesión está a punto de expirar debido a la inactividad.</p>
        </div>
        <div class="modal-body text-center px-4 py-3">
          <div class="mb-3">
            <p class="fw-semibold text-secondary mb-1" style="font-size: 0.95rem;">Cerrando sesión en:</p>
            <span id="inactivityCountdown" class="display-6 fw-bold text-danger font-fredoka">10</span>
            <span class="text-danger fw-bold fs-5">segundos</span>
          </div>
          <!-- Progress bar countdown -->
          <div class="progress mb-2" style="height: 6px; border-radius: 3px; background-color: #F3F4F6;">
            <div id="inactivityProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
              role="progressbar" style="width: 100%; transition: width 10s linear;"></div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex gap-2 justify-content-center">
          <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-semibold"
            style="border-radius: 12px; font-size: 0.9rem;" onclick="logoutNow()">Cerrar sesión</button>
          <button type="button" class="btn text-white px-4 py-2 fw-bold"
            style="border-radius: 12px; background-color: var(--purple); border: none; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);"
            onclick="keepSessionAlive()">Sí, seguir jugando 🧸</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const welcomeBackModal = document.getElementById('welcomeBackModal');
  if (welcomeBackModal) {
    const modal = bootstrap.Modal.getOrCreateInstance(welcomeBackModal);
    modal.show();
  }

  const profileForm = document.getElementById('profileForm');
  const deactivateForm = document.getElementById('deactivateAccountForm');
  const deleteForm = document.getElementById('deleteAccountForm');
  if (!profileForm) return;

  const alertBox = document.getElementById('profileAlert');

  profileForm.addEventListener('submit', async function (event) {
    event.preventDefault();

    if (alertBox) {
      alertBox.style.display = 'none';
      alertBox.textContent = '';
      alertBox.className = 'alert mb-3';
    }

    if (!profileForm.checkValidity()) {
      profileForm.classList.add('was-validated');
      return;
    }

    const password = document.getElementById('profilePassword')?.value || '';
    const passwordConfirm = document.getElementById('profilePasswordConfirm')?.value || '';

    if (password || passwordConfirm) {
      if (password.length < 8 || !/[A-Za-z]/.test(password) || !/[0-9]/.test(password)) {
        showAlert('La contraseña debe tener al menos 8 caracteres y contener letras y números.', 'danger');
        return;
      }
      if (password !== passwordConfirm) {
        showAlert('Las contraseñas no coinciden.', 'danger');
        return;
      }
    }

    const submitBtn = profileForm.querySelector('[type="submit"]');
    const originalHtml = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
    }

    try {
      const formData = new FormData(profileForm);
      const response = await fetch('include/update_profile.php', {
        method: 'POST',
        body: formData
      });

      const responseText = await response.text();
      let data = {};
      try {
        data = responseText ? JSON.parse(responseText) : {};
      } catch (error) {
        data = { success: false, message: responseText || 'No se pudo procesar la respuesta del servidor.' };
      }

      if (data.success) {
        showAlert('✅ ' + data.message, 'success');
        setTimeout(() => window.location.reload(), 1200);
      } else {
        showAlert('⚠️ ' + data.message, 'danger');
      }
    } catch (error) {
      showAlert('⚠️ Error de conexión con el servidor.', 'danger');
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHtml;
      }
    }
  });

  if (deactivateForm) {
    deactivateForm.addEventListener('submit', async function (event) {
      event.preventDefault();

      if (alertBox) {
        alertBox.style.display = 'none';
        alertBox.textContent = '';
        alertBox.className = 'alert mb-3';
      }

      const confirmation = deactivateForm.querySelector('[name="confirmacion"]')?.value?.trim() || '';
      if (confirmation.toLowerCase() !== 'desactivar') {
        showAlert('Escribe exactamente DESACTIVAR para confirmar.', 'danger');
        return;
      }

      const submitBtn = deactivateForm.querySelector('[type="submit"]');
      const originalHtml = submitBtn ? submitBtn.innerHTML : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
      }

      try {
        const formData = new FormData(deactivateForm);
        const response = await fetch('include/deactivate_account.php', {
          method: 'POST',
          body: formData
        });

        const responseText = await response.text();
        let data = {};
        try {
          data = responseText ? JSON.parse(responseText) : {};
        } catch (error) {
          data = { success: false, message: responseText || 'No se pudo procesar la respuesta del servidor.' };
        }

        if (data.success) {
          showAlert('✅ ' + data.message, 'success');
          setTimeout(() => window.location.href = 'index.php', 1500);
        } else {
          showAlert('⚠️ ' + data.message, 'danger');
        }
      } catch (error) {
        showAlert('⚠️ Error de conexión con el servidor.', 'danger');
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalHtml;
        }
      }
    });
  }

  if (deleteForm) {
    deleteForm.addEventListener('submit', async function (event) {
      event.preventDefault();

      if (alertBox) {
        alertBox.style.display = 'none';
        alertBox.textContent = '';
        alertBox.className = 'alert mb-3';
      }

      const submitBtn = deleteForm.querySelector('[type="submit"]');
      const originalHtml = submitBtn ? submitBtn.innerHTML : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...';
      }

      try {
        const formData = new FormData(deleteForm);
        const response = await fetch('include/delete_account.php', {
          method: 'POST',
          body: formData
        });

        const responseText = await response.text();
        let data = {};
        try {
          data = responseText ? JSON.parse(responseText) : {};
        } catch (error) {
          data = { success: false, message: responseText || 'No se pudo procesar la respuesta del servidor.' };
        }

        if (data.success) {
          showAlert('✅ ' + data.message, 'success');
          setTimeout(() => window.location.href = 'index.php', 1500);
        } else {
          showAlert('⚠️ ' + data.message, 'danger');
        }
      } catch (error) {
        showAlert('⚠️ Error de conexión con el servidor.', 'danger');
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalHtml;
        }
      }
    });
  }

  function showAlert(message, type) {
    if (!alertBox) return;
    alertBox.className = 'alert alert-' + type + ' mb-3';
    alertBox.textContent = message;
    alertBox.style.display = 'block';
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
});
</script>