<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!-- ── NAVBAR ── -->
<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
  <div class="container-fluid px-4" style="background-color: #ffffff;" data-bs-theme="primary" >

    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php" id="logo">
      <span class="logo-icon">🐻</span>
      <span class="logo-text">
        <span class="logo-top">Tienda de</span>
        <span class="logo-bottom">JUGUETES</span>
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
                <?php
                if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'cliente') {
                  echo "Bienvenido " . htmlspecialchars($_SESSION['usuario_nombre']);
                } else {
                  echo htmlspecialchars($_SESSION['usuario_nombre']) . " (" . htmlspecialchars($_SESSION['usuario_rol']) . ")";
                }
                ?>
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
                <a class="dropdown-item rounded d-flex align-items-center gap-2 py-2 text-danger fw-semibold"
                  href="include/logout.php" style="font-size: 0.9rem;">
                  <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                </a>
              </li>
            </ul>
          </div>

          <!-- Botón directo Cerrar sesión -->
          <a href="include/logout.php" class="btn-logout-nav d-flex align-items-center gap-2"
            title="Cerrar sesión"
            style="padding: 0.35rem 0.9rem; border-radius: 50px; background-color: rgba(220, 38, 38, 0.08); color: #DC2626; font-weight: 600; font-size: 0.85rem; text-decoration: none; border: 1px solid rgba(220, 38, 38, 0.2); transition: all 0.2s ease;"
            onmouseover="this.style.backgroundColor='rgba(220,38,38,0.15)'"
            onmouseout="this.style.backgroundColor='rgba(220,38,38,0.08)'">
            <i class="bi bi-box-arrow-right fs-5"></i>
            <span class="d-none d-md-inline">Cerrar sesión</span>
          </a>

        <?php else: ?>
          <!-- Logged out state -->
          <button class="nav-icon-btn" id="btn-user" data-bs-toggle="modal" data-bs-target="#loginModal"><i
              class="bi bi-person"></i></button>
        <?php endif; ?>

        <button class="nav-icon-btn cart-wrapper" id="btn-cart">
          <i class="bi bi-cart2"></i>
          <span class="cart-count">0</span>
        </button>
      </div>
    </div>

  </div>
</nav>

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