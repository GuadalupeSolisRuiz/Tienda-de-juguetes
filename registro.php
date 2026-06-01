<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tienda de Juguetes - Registro</title>
  <?php include "include/conect.php"; ?>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap"
    rel="stylesheet" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet" />

  <style>
    :root {
      --purple: #7C3AED;
      --purple-light: #8B5CF6;
      --purple-dark: #6D28D9;
      --yellow: #F59E0B;
      --pink: #EC4899;
      --blue: #3B82F6;
      --green: #10B981;
      --bg: #FDF8F0;
      --text: #1F2937;
      --text-light: #6B7280;
      --border: #E5E7EB;
      --red: #EF4444;
    }

    body {
      font-family: 'Nunito', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
    }

    /* ── NAVBAR ── */
    .navbar {
      background: #fff !important;
      border-bottom: 1px solid var(--border);
      height: 64px;
      font-family: 'Nunito', sans-serif;
    }

    .logo-icon {
      font-size: 2rem;
    }

    .logo-text {
      display: flex;
      flex-direction: column;
      line-height: 1;
    }

    .logo-top {
      font-weight: 700;
      font-size: 0.65rem;
      color: var(--text);
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    .logo-bottom {
      font-family: 'Fredoka One', cursive;
      font-size: 1.4rem;
      background: linear-gradient(90deg, #EF4444, #F59E0B, #10B981, #3B82F6, #8B5CF6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .navbar-nav .nav-link {
      font-weight: 600;
      font-size: 0.9rem;
      color: var(--text) !important;
      transition: color 0.2s;
    }

    .navbar-nav .nav-link:hover {
      color: var(--purple) !important;
    }

    .nav-icon-btn {
      background: none;
      border: none;
      padding: 0;
      color: var(--text);
      transition: color 0.2s;
      cursor: pointer;
      line-height: 1;
    }

    .nav-icon-btn:hover {
      color: var(--purple);
    }

    .nav-icon-btn i {
      font-size: 1.3rem;
    }

    .cart-wrapper {
      position: relative;
      display: inline-flex;
    }

    .cart-count {
      position: absolute;
      top: -6px;
      right: -6px;
      background: var(--red);
      color: #fff;
      font-size: 0.58rem;
      font-weight: 800;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* ── HERO ── */
    .hero-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      min-height: calc(100vh - 64px - 96px);
    }

    /* LEFT */
    .hero-left {
      background: linear-gradient(135deg, #FEF9EC 0%, #FFF3E0 50%, #FEF0F8 100%);
      padding: 60px 50px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      overflow: hidden;
    }

    .decorations {
      position: absolute;
      inset: 0;
      pointer-events: none;
    }

    .deco-star {
      position: absolute;
      top: 60px;
      left: 60px;
      font-size: 2.5rem;
      animation: float 3s ease-in-out infinite;
    }

    .deco-cloud1 {
      position: absolute;
      top: 50px;
      right: 60px;
      width: 100px;
      animation: float 4s ease-in-out infinite 0.5s;
    }

    .deco-cloud2 {
      position: absolute;
      top: 55%;
      left: 30px;
      width: 80px;
      animation: float 3.5s ease-in-out infinite 1s;
    }

    .deco-star-pink {
      position: absolute;
      bottom: 260px;
      right: 50px;
      font-size: 2rem;
      animation: float 3.2s ease-in-out infinite 0.8s;
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-10px);
      }
    }

    .hero-copy {
      margin-top: 80px;
      z-index: 1;
    }

    .hero-copy h1 {
      font-family: 'Fredoka One', cursive;
      font-size: 2.8rem;
      color: var(--text);
      line-height: 1.15;
      margin-bottom: 16px;
    }

    .hero-copy p {
      font-size: 1rem;
      color: var(--text-light);
      line-height: 1.6;
      max-width: 320px;
    }

    .hero-image {
      z-index: 1;
      position: relative;
    }

    .hero-image::before {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 360px;
      height: 200px;
      background: radial-gradient(ellipse at bottom, #FDDFA0 0%, transparent 70%);
      border-radius: 50% 50% 0 0 / 100% 100% 0 0;
    }

    .toys-container {
      display: flex;
      align-items: flex-end;
      justify-content: center;
      height: 280px;
      position: relative;
      z-index: 1;
    }

    .toy-bear {
      font-size: 9rem;
      filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.15));
      animation: float 4s ease-in-out infinite;
    }

    .toy-ball {
      font-size: 5rem;
      margin-left: -20px;
      filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.15));
      animation: float 3.5s ease-in-out infinite 0.5s;
    }

    /* RIGHT */
    .hero-right {
      background: #fff;
      padding: 40px 50px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-card {
      width: 100%;
      max-width: 600px;
    }

    .form-avatar {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      background: linear-gradient(135deg, #C4B5FD, #8B5CF6);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .form-avatar i {
      font-size: 1.5rem;
      color: #fff;
    }

    .form-card h2 {
      font-family: 'Fredoka One', cursive;
      font-size: 1.8rem;
      color: var(--text);
      margin-bottom: 2px;
    }

    .form-card .subtitle {
      font-size: 0.85rem;
      color: var(--text-light);
    }

    /* Inputs */
    .input-icon-wrap {
      position: relative;
    }

    .input-icon-wrap .field-icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #9CA3AF;
      font-size: 1rem;
      pointer-events: none;
      z-index: 5;
    }

    .input-icon-wrap .form-control,
    .input-icon-wrap .form-select {
      padding-left: 38px;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      font-family: 'Nunito', sans-serif;
      font-size: 0.88rem;
      color: var(--text);
      background: #fff;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .input-icon-wrap .form-control::placeholder {
      color: #9CA3AF;
    }

    .input-icon-wrap .form-control:focus,
    .input-icon-wrap .form-select:focus {
      border-color: var(--purple);
      box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
    }

    .input-icon-wrap .toggle-pw {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      padding: 0;
      color: #9CA3AF;
      cursor: pointer;
      font-size: 1.1rem;
      line-height: 1;
      z-index: 5;
      transition: color 0.2s;
    }

    .input-icon-wrap .toggle-pw:hover {
      color: var(--purple);
    }

    .input-icon-wrap .form-control.has-toggle {
      padding-right: 38px;
    }

    /* Date input placeholder color */
    input[type="date"]:not(.filled) {
      color: #9CA3AF;
    }

    input[type="date"].filled {
      color: var(--text);
    }

    /* Select placeholder */
    .form-select.placeholder-mode {
      color: #9CA3AF;
    }

    .form-select:not(.placeholder-mode) {
      color: var(--text);
    }

    .form-label {
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 6px;
    }

    .required-star {
      color: var(--red);
    }

    .hint-text {
      font-size: 0.75rem;
      color: #9CA3AF;
      margin-top: 4px;
    }

    /* Terms */
    .form-check-input {
      width: 18px;
      height: 18px;
      border: 2px solid var(--border);
      border-radius: 4px;
      accent-color: var(--purple);
      cursor: pointer;
      margin-top: 0;
    }

    .form-check-label {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text);
      cursor: pointer;
    }

    .form-check-label a {
      color: var(--purple);
      font-weight: 700;
      text-decoration: none;
    }

    .form-check-label a:hover {
      text-decoration: underline;
    }

    /* Submit button */
    .btn-register {
      width: 100%;
      padding: 14px;
      background: var(--purple);
      color: #fff;
      border: none;
      border-radius: 10px;
      font-family: 'Nunito', sans-serif;
      font-size: 1rem;
      font-weight: 800;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35);
    }

    .btn-register:hover {
      background: var(--purple-dark);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(124, 58, 237, 0.45);
      color: #fff;
    }

    .btn-register:active {
      transform: translateY(0);
    }

    .login-link {
      text-align: center;
      margin-top: 20px;
      font-size: 0.88rem;
      color: var(--text-light);
    }

    .login-link a {
      color: var(--purple);
      font-weight: 700;
      text-decoration: none;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    /* ── FOOTER BAR ── */
    .footer-bar {
      background: #F3F4F6;
      border-top: 1px solid var(--border);
      padding: 24px 60px;
    }

    .footer-item {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .footer-icon {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .footer-icon i {
      font-size: 1.25rem;
    }

    .footer-icon.purple {
      background: #EDE9FE;
      color: #7C3AED;
    }

    .footer-icon.pink {
      background: #FCE7F3;
      color: #EC4899;
    }

    .footer-icon.green {
      background: #D1FAE5;
      color: #059669;
    }

    .footer-icon.blue {
      background: #DBEAFE;
      color: #2563EB;
    }

    .footer-text strong {
      display: block;
      font-size: 0.9rem;
      font-weight: 800;
      color: var(--text);
    }

    .footer-text span {
      font-size: 0.78rem;
      color: var(--text-light);
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 991.98px) {
      .hero-section {
        grid-template-columns: 1fr;
      }

      .hero-left {
        display: none;
      }

      .hero-right {
        padding: 32px 20px;
      }

      .footer-bar {
        padding: 20px 16px;
      }
    }

    @media (max-width: 575.98px) {
      .footer-item .footer-text strong {
        font-size: 0.78rem;
      }
    }
  </style>
</head>

<body>

  <!-- ── NAVBAR ── -->
  <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container-fluid px-4">

      <!-- Logo -->
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
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
          <li class="nav-item"><a class="nav-link" href="#">Inicio</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Juguetes</a>
            <ul class="dropdown-menu shadow-sm border-0">
              <li><a class="dropdown-item" href="#">Niñas</a></li>
              <li><a class="dropdown-item" href="#">Niños</a></li>
              <li><a class="dropdown-item" href="#">Bebés</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Categorías</a>
            <ul class="dropdown-menu shadow-sm border-0">
              <li><a class="dropdown-item" href="#">Educativos</a></li>
              <li><a class="dropdown-item" href="#">Electrónicos</a></li>
              <li><a class="dropdown-item" href="#">Peluches</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="#">Ofertas</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Nosotros</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
        </ul>

        <!-- Icons -->
        <div class="d-flex align-items-center gap-3 ms-lg-3 mt-2 mt-lg-0">
          <button class="nav-icon-btn"><i class="bi bi-search"></i></button>
          <button class="nav-icon-btn"><i class="bi bi-person"></i></button>
          <button class="nav-icon-btn cart-wrapper">
            <i class="bi bi-cart2"></i>
            <span class="cart-count">0</span>
          </button>
        </div>
      </div>

    </div>
  </nav>


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
        <form id="registrationForm" novalidate>

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
                <input type="text" id="nombreCompleto" class="form-control" placeholder="Ingresa tu nombre completo"
                  required />
                <div class="invalid-feedback">
                  Por favor ingresa tu nombre completo.
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <label class="form-label">Apellido completo <span class="required-star">*</span></label>
              <div class="input-icon-wrap">
                <i class="bi bi-person field-icon"></i>
                <input type="text" class="form-control" placeholder="Ingresa tu apellido completo" required />
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
              <input type="email" class="form-control" placeholder="ejemplo@correo.com" required />
              <div class="invalid-feedback">
                Por favor ingresa un correo electrónico válido.
              </div>
            </div>
          </div>

          <!-- Password Row -->
          <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
              <label class="form-label">Contraseña <span class="required-star">*</span></label>
              <div class="input-icon-wrap">
                <i class="bi bi-lock field-icon"></i>
                <input type="password" id="pw1" class="form-control has-toggle" placeholder="Crea una contraseña"
                  required />
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
                <input type="password" id="pw2" class="form-control has-toggle" placeholder="Repite tu contraseña"
                  required />
                <button class="toggle-pw" type="button" onclick="togglePw('pw2', this)">
                  <i class="bi bi-eye"></i>
                </button>
                <div class="invalid-feedback">
                  Por favor confirma tu contraseña.
                </div>
              </div>
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
              Acepto los <a href="#">Términos y Condiciones</a> y la <a href="#">Política de Privacidad</a>.
            </label>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn-register">
            <i class="bi bi-person-plus-fill" style="font-size:1.15rem;"></i>
            Crear cuenta
          </button>
        </form>

        <!-- Login link -->
        <p class="login-link">¿Ya tienes una cuenta? <a href="#">Inicia sesión</a></p>
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


  <!-- Bootstrap 5 JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

  <script>
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

    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('registrationForm');
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
</body>

</html>