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
<script>
  window.isUserLoggedIn = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>;
  window.currentUserId = <?= isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0 ?>;
</script>
<!-- ── NAVBAR ── -->
<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
  <div class="container-fluid px-4" style="background-color: #ffffff;">

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
        <li class="nav-item">
          <a class="nav-link" href="categoria.php?c=todos" id="nav-categorias">Categorías</a>
        </li>
        <li class="nav-item"><a class="nav-link" href="index.php#ofertas" id="nav-ofertas"><span class="badge bg-danger text-white me-1" style="font-size:0.65rem; vertical-align:middle;">HOT</span> Ofertas</a></li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#nosotros" id="nav-nosotros"
            onclick="if(!window.location.pathname.endsWith('index.php') && window.location.pathname !== '/' && !window.location.pathname.endsWith('Tienda-de-juguetes/')) { event.preventDefault(); if(window.closeAllModals) window.closeAllModals(); setTimeout(() => { const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('nosotrosModal')); modal.show(); }, 150); }">Nosotros</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#contacto" id="nav-contacto"
            onclick="if(!window.location.pathname.endsWith('index.php') && window.location.pathname !== '/' && !window.location.pathname.endsWith('Tienda-de-juguetes/')) { event.preventDefault(); if(window.closeAllModals) window.closeAllModals(); setTimeout(() => { const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('contactoModal')); modal.show(); }, 150); }">Contacto</a>
        </li>
        <?php 
        $userRolNavbar = strtolower($_SESSION['usuario_rol'] ?? '');
        if (isset($_SESSION['usuario_id']) && in_array($userRolNavbar, ['administrador', 'editor'])): 
        ?>
          <li class="nav-item">
            <a class="nav-link fw-semibold nav-gestion-pill" href="gestion.php" id="nav-gestion"
              style="border: none; background: transparent; padding: 0.4rem 0.8rem; border-radius: 50px; background-color: rgba(124, 58, 237, 0.17); color: #7C3AED; transition: all 0.2s ease;">
              Gestión
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Icons / User Section -->
      <div class="d-flex align-items-center gap-3 ms-lg-3 mt-2 mb-2 mt-lg-0">
        <button class="nav-icon-btn" id="btn-search"><i class="bi bi-search"></i></button>

        <?php if (isset($_SESSION['usuario_id'])): ?>
          <!-- Logged in user dropdown -->
          <div class="dropdown user-dropdown-hover">
            <button class="nav-icon-btn dropdown-toggle d-flex align-items-center gap-2" id="userDropdown"
              data-bs-toggle="dropdown" aria-expanded="false"
              style="border: none; background: transparent; padding: 0.25rem 0.75rem; border-radius: 50px; background-color: rgba(124, 58, 237, 0.08); color: #7C3AED; transition: all 0.2s ease;">
              <i class="bi bi-person-fill fs-5"></i>
              <span class="d-none d-md-inline fw-semibold" style="font-size: 0.9rem;">
                <span class="text-decoration-none text-dark" style="color: inherit;">
                  <?php
                  if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'cliente') {
                    echo "Bienvenido " . htmlspecialchars($_SESSION['usuario_nombre'] ?? '');
                  } else {
                    echo htmlspecialchars($_SESSION['usuario_nombre'] ?? '') . " (" . htmlspecialchars($_SESSION['usuario_rol'] ?? '') . ")";
                  }
                  ?>
                </span>
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" aria-labelledby="userDropdown"
              style="border-radius: 12px; min-width: 220px; background-color: #fff; z-index: 1050;">
              <li class="px-3 py-2 border-bottom mb-2">
                <p class="mb-0 fw-bold text-dark text-capitalize" style="font-size: 0.9rem;">
                  <?php echo htmlspecialchars(($_SESSION['usuario_nombre'] ?? '') . ' ' . ($_SESSION['usuario_apellido'] ?? '')); ?>
                </p>
                <p class="mb-0 text-muted small text-truncate" style="font-size: 0.75rem;">
                  <?php echo htmlspecialchars($_SESSION['usuario_correo'] ?? ''); ?>
                </p>
                <span class="badge mt-1 text-capitalize"
                  style="font-size: 0.65rem; background-color: var(--purple); color: #fff;"><?php echo htmlspecialchars($_SESSION['usuario_rol'] ?? ''); ?></span>
              </li>
              <li>
                <a class="dropdown-item rounded d-flex align-items-center gap-2 py-2" href="#"
                  onclick="event.preventDefault(); if(window.closeAllModals) window.closeAllModals(); setTimeout(() => { const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('profileModal')); modal.show(); }, 150);"
                  style="font-size: 0.9rem;">
                  <i class="bi bi-person-gear"></i> Editar perfil
                </a>
              </li>
              <li>
                <a class="dropdown-item rounded d-flex align-items-center gap-2 py-2" href="#"
                  onclick="event.preventDefault(); if(window.loadUserOrders) window.loadUserOrders();"
                  style="font-size: 0.9rem;">
                  <i class="bi bi-bag-check-fill text-purple"></i> Mis Pedidos
                </a>
              </li>
              <li>
                <a class="dropdown-item rounded d-flex align-items-center gap-2 py-2 text-danger fw-semibold"
                  href="include/logout.php" onclick="logoutNow(); return false;" style="font-size: 0.9rem;">
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
            onclick="logoutNow(); return false;"
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
      <div class="modal-content border-0 shadow-lg"
        style="border-radius: 20px; background: linear-gradient(135deg, #fff8f0 0%, #fff 100%);">
        <div class="modal-body text-center px-4 py-5">
          <div class="mb-3" style="font-size: 4rem;">🐻</div>
          <h3 class="fw-bold mb-2" id="welcomeBackModalLabel"
            style="font-family: 'Fredoka One', cursive; color: #7C3AED;">¡Te extrañamos!</h3>
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
          <div class="mb-2">
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

          <!-- Olvidé mi contraseña link -->
          <div class="d-flex justify-content-end mb-3">
            <a href="#" id="forgotPasswordLink" class="small text-purple text-decoration-none fw-semibold">
              ¿Olvidaste tu contraseña?
            </a>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn-register w-100 mt-1 mb-3">
            <i class="bi bi-box-arrow-in-right fs-5 me-2"></i>
            Iniciar sesión
          </button>
        </form>

        <!-- Alerta de recuperación -->
        <div id="forgotPasswordAlert" class="alert mb-3" style="display: none; font-size: 0.85rem; border-radius: 8px;"></div>

        <!-- Formulario de Recuperación (envío de código) -->
        <form id="forgotPasswordForm" style="display: none;" novalidate>
          <div class="mb-3">
            <label for="forgotEmail" class="form-label">Correo electrónico registrado <span class="required-star">*</span></label>
            <div class="input-icon-wrap">
              <i class="bi bi-envelope field-icon"></i>
              <input type="email" id="forgotEmail" name="correo" class="form-control" placeholder="ejemplo@correo.com" required />
            </div>
          </div>
          <button type="submit" class="btn-primary-custom w-100 py-2.5 mb-2">Enviar código de recuperación</button>
          <button type="button" class="btn btn-link w-100 text-muted small text-decoration-none" onclick="document.getElementById('forgotPasswordForm').style.display='none'; document.getElementById('modalLoginForm').style.display='block';">Volver al inicio de sesión</button>
        </form>

        <!-- Formulario de Restablecimiento (código + nueva contraseña) -->
        <form id="resetPasswordForm" style="display: none;" novalidate>
          <div class="mb-3">
            <label for="resetToken" class="form-label">Código de recuperación <span class="required-star">*</span></label>
            <div class="input-icon-wrap">
              <i class="bi bi-key field-icon"></i>
              <input type="text" id="resetToken" name="codigo" class="form-control" placeholder="Ingresa el código enviado" required />
            </div>
          </div>
          <div class="mb-3">
            <label for="newPassword" class="form-label">Nueva contraseña <span class="required-star">*</span></label>
            <div class="input-icon-wrap">
              <i class="bi bi-lock field-icon"></i>
              <input type="password" id="newPassword" name="nueva_contrasena" class="form-control has-toggle" placeholder="Nueva contraseña" required />
              <button class="toggle-pw" type="button" onclick="toggleModalPw('newPassword', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirmar contraseña <span class="required-star">*</span></label>
            <div class="input-icon-wrap">
              <i class="bi bi-lock field-icon"></i>
              <input type="password" id="confirmPassword" name="confirmar_contrasena" class="form-control has-toggle" placeholder="Repite la contraseña" required />
              <button class="toggle-pw" type="button" onclick="toggleModalPw('confirmPassword', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
          <button type="submit" class="btn-primary-custom w-100 py-2.5 mb-2">Guardar nueva contraseña</button>
          <button type="button" class="btn btn-link w-100 text-muted small text-decoration-none" onclick="document.getElementById('resetPasswordForm').style.display='none'; document.getElementById('modalLoginForm').style.display='block';">Volver al inicio de sesión</button>
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

<!-- ── CART DRAWER ── -->
<div class="cart-drawer" id="cartDrawer" role="dialog" aria-label="Carrito de compras">
  <div class="cart-drawer-header">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-cart3 fs-5" style="color:var(--purple)"></i>
      <h3>Tu Carrito</h3>
    </div>
    <button class="cart-drawer-close" id="cartDrawerClose" aria-label="Cerrar carrito">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <div class="cart-drawer-body" id="cartItems">
    <div class="cart-empty">
      <div class="cart-empty-icon">🛒</div>
      <p>Tu carrito está vacío</p>
      <small>¡Agrega tus juguetes favoritos!</small>
    </div>
  </div>

  <div class="cart-drawer-footer" id="cartFooter" style="display:none">
    <div class="cart-total-row">
      <span>Total estimado</span>
      <strong id="cartTotal">$0</strong>
    </div>
    <button class="btn-checkout-cart" id="btnCheckout">
      <i class="bi bi-bag-check-fill"></i>
      Proceder al pago
    </button>
    <button class="btn-clear-cart" id="btnClearCart">
      <i class="bi bi-trash3"></i>
      Vaciar carrito
    </button>
  </div>
</div>
<div class="cart-overlay" id="cartOverlay"></div>

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
          <div id="profileAlert" class="alert alert-danger mb-3"
            style="display: none; font-size: 0.9rem; border-radius: 8px;"></div>
          <form id="profileForm" novalidate>
            <input type="hidden" name="id_usuario" value="<?php echo intval($_SESSION['usuario_id']); ?>">

            <div class="row g-3 mb-3">
              <div class="col-12 col-sm-6">
                <label class="form-label">Nombre <span class="required-star">*</span></label>
                <input type="text" name="nombre" class="form-control"
                  value="<?php echo htmlspecialchars($usuario_actual['nombre'] ?? ''); ?>" required>
              </div>
              <div class="col-12 col-sm-6">
                <label class="form-label">Apellido <span class="required-star">*</span></label>
                <input type="text" name="apellido" class="form-control"
                  value="<?php echo htmlspecialchars($usuario_actual['apellido'] ?? ''); ?>" required>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Correo electrónico <span class="required-star">*</span></label>
              <input type="email" name="correo" class="form-control"
                value="<?php echo htmlspecialchars($usuario_actual['correo'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Teléfono</label>
              <input type="tel" name="telefono" class="form-control"
                value="<?php echo htmlspecialchars($usuario_actual['telefono'] ?? ''); ?>" maxlength="70">
            </div>

            <div class="row g-3 mb-3">
              <div class="col-12 col-sm-6">
                <label class="form-label">Nueva contraseña <small class="text-muted">(opcional)</small></label>
                <input type="password" id="profilePassword" name="contrasena" class="form-control has-toggle"
                  placeholder="Deja vacío para mantenerla">
              </div>
              <div class="col-12 col-sm-6">
                <label class="form-label">Confirmar contraseña</label>
                <input type="password" id="profilePasswordConfirm" name="contrasena2" class="form-control has-toggle"
                  placeholder="Repite la nueva contraseña">
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
                  <input type="password" name="contrasena" class="form-control" placeholder="Ingresa tu contraseña"
                    required>
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
<?php endif; ?>

<!-- ── MODAL SELECCIÓN MÉTODO DE PAGO ── -->
<div class="modal fade" id="checkoutPaymentModal" tabindex="-1" aria-labelledby="checkoutPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-0 bg-light-purple py-3 px-4" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
        <div class="d-flex align-items-center gap-2">
          <div style="width:40px;height:40px;border-radius:50%;background:#7C3AED;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
            <i class="bi bi-credit-card-fill"></i>
          </div>
          <div>
            <h5 class="modal-title font-fredoka fw-bold mb-0" id="checkoutPaymentModalLabel" style="color: #7C3AED;">Finalizar Compra</h5>
            <small class="text-muted">Selecciona tu método de pago para generar tu ticket</small>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-4">
          <!-- Sidebar con Resumen -->
          <div class="col-md-5 order-md-2">
            <div class="p-3 bg-light rounded-4 border">
              <h6 class="fw-bold mb-3 d-flex align-items-center gap-2 text-purple">
                <i class="bi bi-receipt"></i> Resumen de Compra
              </h6>
              <div id="checkoutItemsPreview" class="mb-3" style="max-height: 180px; overflow-y: auto; font-size: 0.85rem;"></div>
              <hr>
              <div class="d-flex justify-content-between small mb-1">
                <span>Subtotal:</span>
                <span id="checkoutSubtotalDisplay">$0.00</span>
              </div>
              <div class="d-flex justify-content-between small mb-1">
                <span>Envío:</span>
                <span id="checkoutShippingDisplay">$80.00</span>
              </div>
              <div class="d-flex justify-content-between small mb-2 text-success" id="checkoutDiscountRow" style="display:none;">
                <span>Descuento:</span>
                <span id="checkoutDiscountDisplay">-$0.00</span>
              </div>
              <hr>
              <div class="d-flex justify-content-between fw-bold fs-5 text-dark">
                <span>Total:</span>
                <span id="checkoutTotalDisplay" style="color: #7C3AED;">$0.00</span>
              </div>
            </div>
          </div>

          <!-- Opciones de Pago -->
          <div class="col-md-7 order-md-1">
            <h6 class="fw-bold mb-3">Método de Pago <span class="text-danger">*</span></h6>
            
            <div class="payment-methods-grid d-flex gap-3 mb-4">
              <!-- Efectivo Option -->
              <label class="payment-method-card flex-fill p-3 border rounded-3 text-center cursor-pointer active" id="payMethodEfectivoLabel" style="cursor:pointer; transition: all 0.2s ease;">
                <input type="radio" name="paymentMethod" value="efectivo" checked class="d-none" id="payMethodEfectivo">
                <div class="fs-2 mb-1">💵</div>
                <div class="fw-bold">Efectivo</div>
                <small class="text-muted d-block" style="font-size:0.75rem;">Pago al recibir / Sucursal</small>
              </label>

              <!-- Tarjeta Option -->
              <label class="payment-method-card flex-fill p-3 border rounded-3 text-center cursor-pointer" id="payMethodTarjetaLabel" style="cursor:pointer; transition: all 0.2s ease;">
                <input type="radio" name="paymentMethod" value="tarjeta" class="d-none" id="payMethodTarjeta">
                <div class="fs-2 mb-1">💳</div>
                <div class="fw-bold">Tarjeta</div>
                <small class="text-muted d-block" style="font-size:0.75rem;">Crédito o Débito</small>
              </label>
            </div>

            <!-- Efectivo Details -->
            <div id="cashFields" class="payment-details-box p-3 bg-light rounded-3 border mb-3">
              <h6 class="fw-bold text-dark mb-2" style="font-size:0.9rem;"><i class="bi bi-cash-stack text-success me-1"></i> Pago en Efectivo</h6>
              <div class="mb-2">
                <label for="cashAmountInput" class="form-label small fw-semibold mb-1">¿Con cuánto vas a pagar?</label>
                <div class="input-group input-group-sm">
                  <span class="input-group-text">$</span>
                  <input type="number" id="cashAmountInput" class="form-control" placeholder="Ingresa el monto" step="0.50" min="0">
                </div>
              </div>
              <div id="changeCalculatorAlert" class="alert alert-info py-2 px-3 mb-0 small" style="display:none; font-size:0.85rem;">
                <i class="bi bi-info-circle-fill me-1"></i> Cambio estimado: <strong id="cashChangeDisplay">$0.00</strong>
              </div>
            </div>

            <!-- Tarjeta Details -->
            <div id="cardFields" class="payment-details-box p-3 bg-light rounded-3 border mb-3" style="display:none;">
              <h6 class="fw-bold text-dark mb-3" style="font-size:0.9rem;"><i class="bi bi-credit-card-2-front-fill text-purple me-1"></i> Datos de la Tarjeta</h6>
              <div class="mb-2">
                <label for="cardHolder" class="form-label small mb-1">Nombre del titular</label>
                <input type="text" id="cardHolder" class="form-control form-control-sm" placeholder="Ej. Alex Gonzalez">
              </div>
              <div class="mb-2">
                <label for="cardNumber" class="form-label small mb-1">Número de tarjeta</label>
                <div class="input-group input-group-sm">
                  <input type="text" id="cardNumber" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                  <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                </div>
              </div>
              <div class="row g-2">
                <div class="col-6">
                  <label for="cardExpiry" class="form-label small mb-1">Vencimiento</label>
                  <input type="text" id="cardExpiry" class="form-control form-control-sm" placeholder="MM/YY" maxlength="5">
                </div>
                <div class="col-6">
                  <label for="cardCvv" class="form-label small mb-1">CVV</label>
                  <input type="password" id="cardCvv" class="form-control form-control-sm" placeholder="123" maxlength="4">
                </div>
              </div>
            </div>

            <div id="checkoutErrorAlert" class="alert alert-danger py-2 px-3 small mb-3" style="display:none;"></div>

            <button type="button" class="btn-primary-custom w-100 py-3 font-fredoka fw-bold text-white d-flex align-items-center justify-content-center gap-2" id="btnConfirmCheckoutOrder">
              <i class="bi bi-check-circle-fill fs-5"></i> Confirmar Compra y Generar Ticket
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── MODAL TICKET DE COMPRA ── -->
<div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      <div class="modal-header border-0 bg-dark text-white py-3 px-4 d-print-none">
        <h5 class="modal-title font-fredoka fw-bold text-white mb-0" id="ticketModalLabel">
          <i class="bi bi-receipt me-2 text-warning"></i> Ticket de Compra
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4 bg-light">
        
        <!-- Recibo imprimible -->
        <div class="ticket-receipt-card bg-white p-4 rounded-4 shadow-sm border position-relative" id="printableTicketArea">
          <!-- Cabecera Tienda -->
          <div class="text-center mb-3 border-bottom pb-3">
            <div class="fs-1 mb-1">🐻</div>
            <h4 class="font-fredoka fw-bold mb-0 text-purple" style="letter-spacing:1px; color:#7C3AED;">TOYS NOVA</h4>
            <p class="text-muted small mb-0">Tienda de Juguetes & Fantasías</p>
            <small class="text-secondary d-block" style="font-size:0.75rem;">RFC: TNO-2026-8899X • Tel: (55) 7737-4656</small>
          </div>

          <!-- Folio y Fecha -->
          <div class="d-flex justify-content-between align-items-center small mb-2 bg-light p-2 rounded border">
            <div>
              <span class="text-muted d-block" style="font-size:0.7rem;">FOLIO DE COMPRA:</span>
              <strong id="ticketFolio" class="font-monospace text-purple fs-6" style="color:#7C3AED;">TN-000000</strong>
            </div>
            <div class="text-end">
              <span class="text-muted d-block" style="font-size:0.7rem;">FECHA & HORA:</span>
              <strong id="ticketFecha" class="small text-dark">00/00/0000 00:00</strong>
            </div>
          </div>

          <!-- Datos Cliente -->
          <div class="mb-3 p-2 border rounded small bg-light">
            <div style="font-size:0.75rem;" class="text-muted">CLIENTE:</div>
            <strong id="ticketClienteNombre" class="text-dark d-block">--</strong>
            <small id="ticketClienteCorreo" class="text-muted d-block">--</small>
          </div>

          <!-- Lista de Productos -->
          <div class="table-responsive mb-3">
            <table class="table table-sm align-middle small mb-0">
              <thead class="table-light">
                <tr style="font-size:0.75rem;">
                  <th>Cant.</th>
                  <th>Producto</th>
                  <th class="text-end">P.Unit</th>
                  <th class="text-end">Importe</th>
                </tr>
              </thead>
              <tbody id="ticketItemsBody" style="font-size:0.82rem;">
              </tbody>
            </table>
          </div>

          <!-- Desglose de Totales -->
          <div class="border-top pt-2 mb-3">
            <div class="d-flex justify-content-between small mb-1">
              <span>Subtotal:</span>
              <span id="ticketSubtotal">$0.00</span>
            </div>
            <div class="d-flex justify-content-between small mb-1">
              <span>Envío:</span>
              <span id="ticketEnvio">$0.00</span>
            </div>
            <div class="d-flex justify-content-between small mb-1 text-success" id="ticketDiscountRow" style="display:none;">
              <span>Descuento:</span>
              <span id="ticketDescuento">-$0.00</span>
            </div>
            <div class="d-flex justify-content-between fw-bold fs-5 text-dark border-top pt-2 mt-1">
              <span>TOTAL PAGADO:</span>
              <strong id="ticketTotal" style="color:#7C3AED;">$0.00</strong>
            </div>
          </div>

          <!-- Info Método de Pago -->
          <div class="p-2 border rounded text-center small mb-3 bg-light">
            <span class="text-muted" style="font-size:0.75rem;">MÉTODO DE PAGO:</span>
            <strong id="ticketMetodoPago" class="d-block text-uppercase text-dark">EFECTIVO</strong>
            <div id="ticketCashDetails" style="display:none;" class="mt-1 small text-muted">
              Monto recibido: <span id="ticketCashPaid">$0.00</span> | Cambio: <span id="ticketCashChange">$0.00</span>
            </div>
          </div>

          <!-- Pie del ticket -->
          <div class="text-center text-muted border-top pt-3" style="font-size:0.72rem;">
            <p class="mb-1 fw-bold">¡GRACIAS POR TU COMPRA! 🎉</p>
            <p class="mb-0">Conserva este ticket digital para cualquier aclaración o garantía.</p>
          </div>
        </div>

      </div>

      <div class="modal-footer border-0 bg-light d-flex justify-content-between d-print-none py-3 px-4">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Cerrar
        </button>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-primary-custom btn-sm text-white fw-bold px-3" onclick="window.printTicket()">
            <i class="bi bi-printer-fill me-1"></i> Imprimir Ticket
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── MODAL MIS PEDIDOS ── -->
<div class="modal fade" id="ordersModal" tabindex="-1" aria-labelledby="ordersModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-0 bg-light-purple py-3 px-4" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
        <div class="d-flex align-items-center gap-2">
          <div style="width:40px;height:40px;border-radius:50%;background:#7C3AED;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
            <i class="bi bi-bag-check-fill"></i>
          </div>
          <div>
            <h5 class="modal-title font-fredoka fw-bold mb-0" id="ordersModalLabel" style="color: #7C3AED;">Mis Pedidos</h5>
            <small class="text-muted">Historial de compras realizadas en tu carrito</small>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4" style="min-height: 250px; max-height: 500px; overflow-y: auto;">
        <div id="userOrdersContainer">
          <div class="text-center py-5">
            <div class="spinner-border text-purple" role="status"></div>
            <p class="text-muted mt-2 small">Cargando tus pedidos...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── MODAL NOSOTROS (SOBRE LA EMPRESA) ── -->
<div class="modal fade" id="nosotrosModal" tabindex="-1" aria-labelledby="nosotrosModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-0 bg-light-purple py-3 px-4" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
        <div class="d-flex align-items-center gap-2">
          <div style="width:40px;height:40px;border-radius:50%;background:#7C3AED;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
            <i class="bi bi-info-circle-fill"></i>
          </div>
          <div>
            <h5 class="modal-title font-fredoka fw-bold mb-0" id="nosotrosModalLabel" style="color: #7C3AED;">Sobre TOYS NOVA</h5>
            <small class="text-muted">Conoce nuestra historia, misión y valores</small>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4">
        <div class="text-center mb-4">
          <div class="display-3 mb-2">🐻✨</div>
          <h4 class="font-fredoka fw-bold text-dark">¡Hola! Somos TOYS NOVA</h4>
          <p class="text-muted small mx-auto" style="max-width:550px;">
            Tu tienda de confianza especializada en juguetes interactivos, peluches, didácticos y de entretenimiento para todas las edades.
          </p>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="p-3 bg-light rounded-4 text-center border h-100">
              <div class="fs-2 text-purple mb-2">🎯</div>
              <h6 class="fw-bold text-dark">Nuestra Misión</h6>
              <p class="text-muted small mb-0">Fomentar el desarrollo infantil mediante juguetes educativos y seguros que inspiran curiosidad y alegría.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 bg-light rounded-4 text-center border h-100">
              <div class="fs-2 text-pink mb-2">🌟</div>
              <h6 class="fw-bold text-dark">Nuestra Visión</h6>
              <p class="text-muted small mb-0">Ser la tienda de juguetes líder en el país, reconocida por nuestra calidad, atención rápida y variedad.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 bg-light rounded-4 text-center border h-100">
              <div class="fs-2 text-warning mb-2">💡</div>
              <h6 class="fw-bold text-dark">Nuestros Valores</h6>
              <p class="text-muted small mb-0">Seguridad ante todo, innovación continua, compromiso con la infancia y excelencia en servicio.</p>
            </div>
          </div>
        </div>

        <div class="p-3 rounded-4 border bg-purple-subtle d-flex align-items-center gap-3" style="background:rgba(124,58,237,0.06);">
          <div class="fs-2 text-purple" style="color:#7C3AED;"><i class="bi bi-shop"></i></div>
          <div>
            <strong class="d-block text-dark small">¿Tienes preguntas o deseas hacernos un pedido corporativo?</strong>
            <small class="text-muted">Escríbenos a <strong>hola@toysnova.com</strong> o llámanos al <strong>(55) 5555-5555</strong>.</small>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light py-3 px-4">
        <button type="button" class="btn btn-purple text-white font-fredoka fw-bold px-4" style="background:#7C3AED; border-radius:12px;" data-bs-dismiss="modal">
          ¡Entendido!
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ── MODAL CONTACTO (CONTACTO RÁPIDO) ── -->
<div class="modal fade" id="contactoModal" tabindex="-1" aria-labelledby="contactoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-0 bg-light-purple py-3 px-4" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
        <div class="d-flex align-items-center gap-2">
          <div style="width:40px;height:40px;border-radius:50%;background:#7C3AED;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
            <i class="bi bi-headset"></i>
          </div>
          <div>
            <h5 class="modal-title font-fredoka fw-bold mb-0" id="contactoModalLabel" style="color: #7C3AED;">Contacto & Atención al Cliente</h5>
            <small class="text-muted">Estamos aquí para resolver tus dudas</small>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-4">
          <div class="col-md-5">
            <div class="p-3 bg-light rounded-4 border h-100">
              <h6 class="fw-bold text-dark mb-3"><i class="bi bi-telephone-fill me-1" style="color:#7C3AED;"></i> Canales Directos</h6>
              <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-3">
                <li class="d-flex align-items-center gap-2">
                  <i class="bi bi-whatsapp text-success fs-5"></i>
                  <div>
                    <strong class="d-block text-dark">WhatsApp Ventas</strong>
                    <span>(55) 5555-5555</span>
                  </div>
                </li>
                <li class="d-flex align-items-center gap-2">
                  <i class="bi bi-envelope-fill fs-5" style="color:#7C3AED;"></i>
                  <div>
                    <strong class="d-block text-dark">Correo Electrónico</strong>
                    <span>hola@toysnova.com</span>
                  </div>
                </li>
                <li class="d-flex align-items-center gap-2">
                  <i class="bi bi-clock-fill text-warning fs-5"></i>
                  <div>
                    <strong class="d-block text-dark">Horarios de Atención</strong>
                    <span>Lun - Sáb: 9:00 AM - 8:00 PM</span>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-md-7">
            <div id="modalContactFormAlert" class="alert mb-2" style="display:none; border-radius:10px;"></div>
            <form id="modalContactForm">
              <div class="mb-2">
                <label class="form-label small fw-semibold mb-1">Nombre</label>
                <input type="text" class="form-control form-control-sm" name="nombre" placeholder="Tu nombre" required>
              </div>
              <div class="mb-2">
                <label class="form-label small fw-semibold mb-1">Correo electrónico</label>
                <input type="email" class="form-control form-control-sm" name="correo" placeholder="ejemplo@correo.com" required>
              </div>
              <div class="mb-2">
                <label class="form-label small fw-semibold mb-1">Mensaje</label>
                <textarea class="form-control form-control-sm" name="mensaje" rows="3" placeholder="Escribe tu mensaje..." required></textarea>
              </div>
              <button type="submit" class="btn btn-purple w-100 text-white font-fredoka fw-bold mt-2" style="background:#7C3AED; border-radius:10px;">
                <i class="bi bi-send me-1"></i> Enviar Mensaje
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
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