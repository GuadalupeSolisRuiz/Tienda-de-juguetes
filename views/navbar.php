<!-- ── NAVBAR ── -->
<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
  <div class="container-fluid px-4">

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
          <ul class="dropdown-menu shadow-sm border-0">
            <li><a class="dropdown-item" href="#" id="nav-ninas">Niñas</a></li>
            <li><a class="dropdown-item" href="#" id="nav-ninos">Niños</a></li>
            <li><a class="dropdown-item" href="#" id="nav-bebes">Bebés</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="nav-categorias">Categorías</a>
          <ul class="dropdown-menu shadow-sm border-0">
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
      <div class="d-flex align-items-center gap-3 ms-lg-3 mt-2 mt-lg-0">
        <button class="nav-icon-btn" id="btn-search"><i class="bi bi-search"></i></button>
        <button class="nav-icon-btn" id="btn-user" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="bi bi-person"></i></button>
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
        <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        
        <div class="modal-avatar mb-3">
          <i class="bi bi-person-fill text-white fs-2"></i>
        </div>
        <h3 class="modal-title font-fredoka fw-bold mb-1" id="loginModalLabel">Iniciar sesión</h3>
        <p class="text-muted fs-7 mb-0">¡Qué bueno verte de nuevo por aquí! 🐻</p>
      </div>

      <!-- Body Form -->
      <div class="modal-body px-4 py-3">
        <form id="modalLoginForm" novalidate>
          
          <!-- Email -->
          <div class="mb-3">
            <label for="modalLoginEmail" class="form-label">Correo electrónico <span class="required-star">*</span></label>
            <div class="input-icon-wrap">
              <i class="bi bi-envelope field-icon"></i>
              <input type="email" id="modalLoginEmail" class="form-control" placeholder="ejemplo@correo.com" required />
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
              <input type="password" id="modalLoginPassword" class="form-control has-toggle" placeholder="Ingresa tu contraseña" required />
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
        <a href="registro.php" class="btn-secondary-custom w-100 d-flex align-items-center justify-content-center gap-2 py-3" id="btn-modal-register" style="border-radius: 12px; font-weight: 700; text-decoration: none;">
          <i class="bi bi-person-plus-fill"></i>
          Registrarse / Crear Cuenta
        </a>
      </div>

    </div>
  </div>
</div>

<script>
  // Alternar visualización de contraseña del modal
  function toggleModalPw(id, btn) {
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

  // Validación de formulario del modal
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('modalLoginForm');
    if (!form) return;

    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });
</script>

