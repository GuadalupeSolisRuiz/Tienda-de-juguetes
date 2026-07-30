<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tu Carrito — Tienda de Juguetes</title>
  <?php include "include/conect.php"; ?>

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

  <!-- ── CART PAGE ── -->
  <main class="cart-page">
    <div class="container py-5">

      <!-- Page header -->
      <div class="cart-page-header mb-4">
        <div class="d-flex align-items-center gap-3">
          <div class="cart-page-icon">
            <i class="bi bi-cart3"></i>
          </div>
          <div>
            <h1 class="cart-page-title">Tu Carrito de Compras</h1>
            <p class="cart-page-subtitle mb-0" id="cartPageSubtitle">Cargando...</p>
          </div>
        </div>
        <a href="index.php" class="cart-back-link" id="cartBackLink">
          <i class="bi bi-arrow-left"></i> Seguir comprando
        </a>
      </div>

      <div class="row g-4 align-items-start">

        <!-- ── LEFT: Product list ── -->
        <div class="col-lg-8">

          <!-- Empty state -->
          <div class="cart-page-empty" id="cartPageEmpty" style="display:none">
            <div class="cart-page-empty-icon">🛒</div>
            <h3>Tu carrito está vacío</h3>
            <p>¡Explora nuestro catálogo y agrega los juguetes que más te gusten!</p>
            <a href="index.php" class="btn-primary-custom mt-3 d-inline-flex">
              <i class="bi bi-bag-fill"></i>
              Ver catálogo
            </a>
          </div>

          <!-- Items container -->
          <div id="cartPageItems"></div>

          <!-- Free shipping banner -->
          <div class="cart-free-shipping-banner" id="freeShippingBanner" style="display:none">
            <i class="bi bi-truck-front-fill"></i>
            <span id="freeShippingMsg"></span>
          </div>

        </div>

        <!-- ── RIGHT: Order summary ── -->
        <div class="col-lg-4">
          <div class="cart-summary-box" id="cartSummaryBox" style="display:none">

            <div class="cart-summary-header">
              <i class="bi bi-receipt-cutoff"></i>
              <h3>Resumen del Pedido</h3>
            </div>

            <div class="cart-summary-rows">
              <div class="cart-summary-row">
                <span>Subtotal (<span id="summaryCount">0</span> productos)</span>
                <span id="summarySubtotal">$0.00</span>
              </div>
              <div class="cart-summary-row">
                <div>
                  <div>Envío</div>
                  <small style="font-size:0.72rem;color:var(--text-light);font-weight:600">Calculado sobre envío estándar</small>
                </div>
                <span id="summaryShipping">$80.00</span>
              </div>
              <div class="cart-summary-row" id="discountRow" style="display:none">
                <span>Descuento<span class="cart-coupon-badge" id="appliedCouponBadge"></span></span>
                <div style="text-align:right">
                  <div id="summaryDiscount" style="color:var(--green);font-weight:700">-$0.00</div>
                </div>
              </div>
            </div>

            <!-- Coupon -->
            <div class="cart-coupon-section">
              <div class="cart-coupon-label">
                <i class="bi bi-tag-fill"></i>
                Código de descuento
              </div>
              <div class="cart-coupon-row">
                <input type="text" id="couponInput" class="cart-coupon-input" placeholder="Ingresa tu código" />
                <button class="cart-coupon-btn" id="btnApplyCoupon">Aplicar</button>
              </div>
              <div class="cart-coupon-msg" id="couponMsg"></div>
            </div>

            <hr class="cart-summary-divider" />

            <div class="cart-summary-total-row">
              <span>Total a pagar</span>
              <strong id="summaryTotal">$0.00</strong>
            </div>

            <button class="btn-checkout-page" id="btnCheckoutPage">
              <i class="bi bi-lock-fill"></i>
              Finalizar Compra
            </button>

            <a href="index.php" class="cart-continue-link">
              <i class="bi bi-arrow-left"></i> Seguir comprando
            </a>

            <div class="cart-trust-badges">
              <div class="trust-badge">
                <i class="bi bi-shield-check"></i>
                <div>
                  <strong>Pago seguro</strong>
                  <small>Tus datos protegidos</small>
                </div>
              </div>
              <div class="trust-badge">
                <i class="bi bi-arrow-repeat"></i>
                <div>
                  <strong>Devoluciones fáciles</strong>
                  <small>Hasta 30 días</small>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- Mobile sticky bar -->
  <div class="cart-mobile-bar d-lg-none" id="cartMobileBar" style="display:none">
    <div class="cart-mobile-bar-info">
      <span class="cart-mobile-bar-label">Total</span>
      <strong class="cart-mobile-bar-total" id="cartMobileTotal">$0.00</strong>
    </div>
    <button class="cart-mobile-bar-btn" id="btnMobileCheckout">
      <i class="bi bi-lock-fill"></i>
      Finalizar Compra
    </button>
  </div>

  <!-- ── FOOTER BAR ── -->
  <div class="footer-bar">
    <div class="row row-cols-2 row-cols-lg-4 g-3">
      <div class="col">
        <div class="footer-item">
          <div class="footer-icon purple"><i class="bi bi-truck"></i></div>
          <div class="footer-text">
            <strong>Envíos rápidos</strong>
            <span>A todo el país</span>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="footer-item">
          <div class="footer-icon pink"><i class="bi bi-shield-lock"></i></div>
          <div class="footer-text">
            <strong>Pago seguro</strong>
            <span>Tus datos protegidos</span>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="footer-item">
          <div class="footer-icon green"><i class="bi bi-patch-check"></i></div>
          <div class="footer-text">
            <strong>Productos de calidad</strong>
            <span>Juguetes 100% originales</span>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="footer-item">
          <div class="footer-icon blue"><i class="bi bi-headset"></i></div>
          <div class="footer-text">
            <strong>Atención al cliente</strong>
            <span>Siempre disponibles</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
  <!-- Persistent Cart -->
  <script src="assets/js/cart.js"></script>
  <!-- General App Scripts -->
  <script src="assets/js/script.js"></script>
  <!-- Full-page cart logic -->
  <script src="assets/js/carrito.js"></script>

</body>

</html>
