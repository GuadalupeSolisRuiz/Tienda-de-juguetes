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
        <button class="nav-icon-btn" id="btn-user"><i class="bi bi-person"></i></button>
        <button class="nav-icon-btn cart-wrapper" id="btn-cart">
          <i class="bi bi-cart2"></i>
          <span class="cart-count">0</span>
        </button>
      </div>
    </div>

  </div>
</nav>
