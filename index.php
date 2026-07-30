<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description"
    content="Tienda de Juguetes — Descubre los mejores juguetes para niños y niñas. Peluches, educativos, electrónicos y más con envío a todo el país." />
  <title>Tienda de Juguetes — Inicio</title>
  <?php include "include/conect.php"; ?>
  <?php
  $productos = [];
  if ($conexion) {
    $nombre_base = "Oso de peluche";
    $descripcion_base = "Peluche ultra suave de 60cm. Perfecto para abrazar.";
    $precio_base = 459.00;
    $stock_base = 12;
    $imagenes_json = json_encode([
      "frente" => "Juguetes/osof.png",
      "izquierda" => "Juguetes/osoi.png",
      "derecha" => "Juguetes/osod.png"
    ], JSON_UNESCAPED_SLASHES);

    if ($check_stmt = $conexion->prepare("SELECT id_productos FROM productos WHERE nombre_producto = ?")) {
      $check_stmt->bind_param("s", $nombre_base);
      $check_stmt->execute();
      $check_result = $check_stmt->get_result();
      if ($check_result->num_rows === 0) {
        if ($insert_stmt = $conexion->prepare("INSERT INTO productos (nombre_producto, descripcion, precio, stock, imagen, id_categoria, id_disponible) VALUES (?, ?, ?, ?, ?, NULL, 1)")) {
          $insert_stmt->bind_param("ssdis", $nombre_base, $descripcion_base, $precio_base, $stock_base, $imagenes_json);
          $insert_stmt->execute();
          $insert_stmt->close();
        }
      }
      $check_stmt->close();
    }

    if (
      $select_stmt = $conexion->prepare("
      SELECT p.id_productos, p.nombre_producto, p.descripcion, p.precio, p.stock, p.imagen,
             COALESCE(c.nombre_categoria, 'Sin categoría') AS nombre_categoria
      FROM productos p
      LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
      ORDER BY p.id_productos DESC
    ")
    ) {
      $select_stmt->execute();
      $select_result = $select_stmt->get_result();
      while ($row = $select_result->fetch_assoc()) {
        $vistas = json_decode($row["imagen"], true);
        if (!is_array($vistas)) {
          $vistas = ["frente" => $row["imagen"], "izquierda" => $row["imagen"], "derecha" => $row["imagen"]];
        }

        $productos[] = [
          "id" => (int) $row["id_productos"],
          "nombre" => $row["nombre_producto"],
          "descripcion" => $row["descripcion"],
          "precio" => (float) $row["precio"],
          "stock" => (int) $row["stock"],
          "categoria" => $row["nombre_categoria"],
          "vistas" => $vistas
        ];
      }
      $select_stmt->close();
    }

    // ── Imagen aleatoria por categoría para la sección "Explora por categoría" ──
    $imgsCat = ['nina' => null, 'nino' => null, 'bebe' => null];
    $countsCat = ['nina' => 0, 'nino' => 0, 'bebe' => 0];
    $mapCat = ['Niña' => 'nina', 'Niño' => 'nino', 'Bebé' => 'bebe'];
    foreach ($mapCat as $nombre_db => $slug) {
      $cq = $conexion->prepare("
        SELECT p.imagen, COUNT(*) OVER() AS total
        FROM productos p
        INNER JOIN categoria c ON p.id_categoria = c.id_categoria
        WHERE c.nombre_categoria = ? AND p.id_disponible = 1
        ORDER BY RAND() LIMIT 1
      ");
      $cq->bind_param('s', $nombre_db);
      $cq->execute();
      $crow = $cq->get_result()->fetch_assoc();
      $cq->close();
      if ($crow) {
        $v = json_decode($crow['imagen'], true);
        $imgsCat[$slug] = is_array($v) ? ($v['frente'] ?? null) : $crow['imagen'];
      }
      // count
      $ccount = $conexion->prepare("SELECT COUNT(*) AS n FROM productos p INNER JOIN categoria c ON p.id_categoria=c.id_categoria WHERE c.nombre_categoria=? AND p.id_disponible=1");
      $ccount->bind_param('s', $nombre_db);
      $ccount->execute();
      $crow2 = $ccount->get_result()->fetch_assoc();
      $ccount->close();
      $countsCat[$slug] = (int) ($crow2['n'] ?? 0);
    }
  }
  ?>

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


  <!-- ── PROMO TICKER ── -->
  <div class="promo-ticker">
    <div class="ticker-track">
      <span class="ticker-item"><i class="bi bi-gift-fill"></i> ¡Envío GRATIS en compras mayores a $599!</span>
      <span class="ticker-item"><i class="bi bi-stars"></i> Nuevas colecciones de verano disponibles</span>
      <span class="ticker-item"><i class="bi bi-percent"></i> Hasta 40% de descuento en juguetes seleccionados</span>
      <span class="ticker-item"><i class="bi bi-heart-fill"></i> +2,000 clientes felices nos respaldan</span>
      <span class="ticker-item"><i class="bi bi-shield-check"></i> Compra 100% segura y garantizada</span>
      <!-- duplicate for seamless loop -->
      <span class="ticker-item"><i class="bi bi-gift-fill"></i> ¡Envío GRATIS en compras mayores a $599!</span>
      <span class="ticker-item"><i class="bi bi-stars"></i> Nuevas colecciones de verano disponibles</span>
      <span class="ticker-item"><i class="bi bi-percent"></i> Hasta 40% de descuento en juguetes seleccionados</span>
      <span class="ticker-item"><i class="bi bi-heart-fill"></i> +2,000 clientes felices nos respaldan</span>
      <span class="ticker-item"><i class="bi bi-shield-check"></i> Compra 100% segura y garantizada</span>
    </div>
  </div>


  <!-- ── FEATURED PRODUCTS (PROMOTED TO TOP) ── -->
  <section class="products-section" id="productos" style="padding: 40px 0 50px; background: #ffffff;">
    <div class="container">
      <div class="section-header mb-4">
        <span class="section-badge yellow"><i class="bi bi-star-fill"></i> Destacados</span>
        <h2>Juguetes más populares</h2>
        <p>Los favoritos de nuestros clientes. ¡No te quedes sin el tuyo!</p>
      </div>

      <div class="row g-4">
        <?php if (!empty($productos)): ?>
          <?php foreach ($productos as $producto): ?>
            <div class="col-6 col-lg-3">
              <div class="product-card" id="product-<?= (int) $producto['id'] ?>" data-id="<?= (int) $producto['id'] ?>"
                data-name="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                data-description="<?= htmlspecialchars($producto['descripcion'], ENT_QUOTES, 'UTF-8') ?>"
                data-price="<?= (float) $producto['precio'] ?>" data-raw-price="<?= (float) $producto['precio'] ?>"
                data-formatted-price="$<?= number_format($producto['precio'], 0, ',', '.') ?>"
                data-stock="<?= (int) $producto['stock'] ?>"
                data-categoria="<?= htmlspecialchars($producto['categoria'], ENT_QUOTES, 'UTF-8') ?>"
                data-image="<?= htmlspecialchars($producto['vistas']['frente'] ?? 'Juguetes/osof.png', ENT_QUOTES, 'UTF-8') ?>"
                data-views='<?= htmlspecialchars(json_encode($producto['vistas'], JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>'
                role="button" tabindex="0">
                <div class="product-image">
                  <span class="product-badge new">Nuevo</span>
                  <button class="product-wishlist" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
                  <img
                    src="<?= htmlspecialchars($producto['vistas']['frente'] ?? 'Juguetes/osof.png', ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>" class="product-visual"
                    data-name="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                    data-views='<?= htmlspecialchars(json_encode($producto['vistas'], JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>'>
                  <div class="product-card-hint">Toca para ver más</div>
                </div>
                <div class="product-info">
                  <span class="product-category-tag"><?= htmlspecialchars($producto['categoria']) ?></span>
                  <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
                  <div class="product-footer mt-auto">
                    <span class="product-price">$<?= number_format($producto['precio'], 0, ',', '.') ?></span>
                    <button class="btn-add-cart" aria-label="Agregar al carrito"><i class="bi bi-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="alert alert-info">No hay productos registrados en la base de datos todavía.</div>
          </div>
        <?php endif; ?>
      </div>

      <!-- View all button -->
      <div class="text-center mt-4">
        <a href="categoria.php?c=todos" class="btn-secondary-custom" id="btn-view-all">
          Ver todos los productos
          <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>


  <!-- ── HERO ── -->
  <section class="hero">
    <div class="decorations">
      <div class="deco-star">⭐</div>
      <div class="deco-star2">🌟</div>
      <div class="deco-star3">⭐</div>
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
      <div class="deco-dots">
        <span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span>
      </div>
    </div>

    <div class="container">
      <div class="row align-items-center hero-content">
        <div class="col-lg-6">
          <div class="hero-badge">
            <span class="dot"></span>
            Temporada de verano 2026
          </div>

          <h1>Descubre un mundo<br>de <span class="highlight">diversión</span><br>para tus pequeños</h1>

          <p class="hero-description">
            Encuentra los juguetes más divertidos, educativos y seguros para todas las edades. Calidad garantizada y
            precios increíbles.
          </p>

          <div class="hero-actions">
            <a href="#productos" class="btn-primary-custom" id="btn-explore">
              <i class="bi bi-bag-fill"></i>
              Explorar juguetes
            </a>
            <a href="#categorias" class="btn-secondary-custom" id="btn-categories">
              <i class="bi bi-grid-fill"></i>
              Ver categorías
            </a>
          </div>

          <div class="hero-stats">
            <div class="hero-stat">
              <div class="number">2,500+</div>
              <div class="label">Productos</div>
            </div>
            <div class="hero-stat">
              <div class="number">150+</div>
              <div class="label">Marcas</div>
            </div>
            <div class="hero-stat">
              <div class="number">4.9 ⭐</div>
              <div class="label">Satisfacción</div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="hero-toys">
            <span class="toy-side1">🎮</span>
            <span class="toy-main">🧸</span>
            <span class="toy-side2">🎨</span>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ── CATEGORIES ── -->
  <section class="categories-section" id="categorias">
    <div class="container">
      <div class="section-header">
        <span class="section-badge purple"><i class="bi bi-grid-fill"></i> Categorías</span>
        <h2>Explora por categoría</h2>
        <p>Encuentra el juguete perfecto navegando por nuestras categorías principales.</p>
      </div>

      <div class="row g-4 justify-content-center">
        <?php
        $cats = [
          'nina' => ['label' => 'Niñas', 'emoji' => '\uD83D\uDC67', 'color' => '#EC4899', 'bg' => 'bg-pink', 'id' => 'cat-ninas'],
          'nino' => ['label' => 'Niños', 'emoji' => '\uD83D\uDC66', 'color' => '#3B82F6', 'bg' => 'bg-blue', 'id' => 'cat-ninos'],
          'bebe' => ['label' => 'Bebés', 'emoji' => '\uD83C\uDF7C', 'color' => '#10B981', 'bg' => 'bg-green', 'id' => 'cat-bebes'],
        ];
        foreach ($cats as $slug => $cat):
          $img = $imgsCat[$slug] ?? null;
          $count = $countsCat[$slug] ?? 0;
          ?>
          <div class="col-6 col-md-4 col-lg-3">
            <a href="categoria.php?c=<?= $slug ?>" class="category-card" id="<?= $cat['id'] ?>"
              style="overflow:hidden; padding:0;">
              <div
                style="height:160px; overflow:hidden; border-radius:18px 18px 0 0; background:#f3f4f6; display:flex; align-items:center; justify-content:center; position:relative;">
                <?php if ($img): ?>
                  <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($cat['label']) ?>"
                    style="width:100%; height:100%; object-fit:cover; transition:transform .35s ease;"
                    class="cat-thumb-img">
                  <div
                    style="position:absolute;inset:0;background:linear-gradient(to top,<?= $cat['color'] ?>44 0%,transparent 60%);">
                  </div>
                <?php else: ?>
                  <span style="font-size:3.5rem;"><?= json_decode('"' . $cat['emoji'] . '"') ?></span>
                <?php endif; ?>
              </div>
              <div style="padding:16px 20px 20px;">
                <h3 style="font-family:'Fredoka One',cursive; font-size:1.25rem; color:var(--text); margin-bottom:4px;">
                  <?= htmlspecialchars($cat['label']) ?>
                </h3>
                <p style="font-size:.82rem; color:var(--text-light); margin:0;">
                  <?= $count > 0 ? "+{$count} producto" . ($count !== 1 ? 's' : '') : '' ?>
                </p>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <style>
    .category-card:hover .cat-thumb-img {
      transform: scale(1.07);
    }
  </style>


  <!-- ── PROMO BANNER ──
  <section class="promo-banner">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-7 promo-content">
          <h2>🎉 ¡Ofertas de temporada!</h2>
          <p>Aprovecha descuentos de hasta el 40% en juguetes seleccionados. Promoción válida hasta agotar existencias.
          </p>
          <a href="#" class="btn-promo" id="btn-promo-offers">
            <i class="bi bi-bag-check-fill"></i>
            Ver ofertas
          </a>
        </div>
        <div class="col-lg-5 d-none d-lg-block">
          <div class="promo-toys">
            <span>🎁</span>
            <span>🎉</span>
            <span>🎈</span>
          </div>
        </div>
      </div>
    </div>
  </section> -->


  <!-- ── WHY US ── -->
  <section class="why-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge green"><i class="bi bi-check-circle-fill"></i> ¿Por qué elegirnos?</span>
        <h2>Tu compra en las mejores manos</h2>
        <p>Nos preocupamos por ofrecerte la mejor experiencia de compra.</p>
      </div>

      <div class="row g-4">
        <div class="col-6 col-lg-3">
          <div class="why-card">
            <div class="why-icon purple"><i class="bi bi-truck"></i></div>
            <h3>Envío rápido</h3>
            <p>Envíos a todo el país en 2-5 días hábiles. Gratis en compras mayores a $599.</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="why-card">
            <div class="why-icon pink"><i class="bi bi-shield-check"></i></div>
            <h3>Compra segura</h3>
            <p>Tus datos siempre protegidos con encriptación de última generación.</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="why-card">
            <div class="why-icon green"><i class="bi bi-arrow-repeat"></i></div>
            <h3>Devoluciones fáciles</h3>
            <p>30 días para devolver tu producto si no estás satisfecho. Sin preguntas.</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="why-card">
            <div class="why-icon blue"><i class="bi bi-headset"></i></div>
            <h3>Soporte 24/7</h3>
            <p>Nuestro equipo está disponible para ayudarte en todo momento.</p>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ── NEWSLETTER ── -->
  <section class="newsletter-section">
    <div class="container">
      <div class="newsletter-box">
        <div class="newsletter-content">
          <h2>📬 ¡No te pierdas nada!</h2>
          <p>Suscríbete a nuestro boletín y recibe ofertas exclusivas, novedades y un 10% de descuento en tu primera
            compra.</p>
          <form class="newsletter-form" id="newsletterForm">
            <input type="email" placeholder="Tu correo electrónico" id="newsletter-email" required />
            <button type="submit" id="btn-subscribe">Suscribirme</button>
          </form>
        </div>
      </div>
    </div>
  </section>


  <!-- ── FOOTER BAR (identical to registro.php) ── -->
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


  <!-- ── MAIN FOOTER ── -->
  <footer class="main-footer">
    <div class="container">
      <div class="row g-4">

        <!-- Brand -->
        <div class="col-lg-3 col-md-6">
          <a class="d-flex align-items-center gap-2 text-decoration-none mb-3 footer-brand" href="index.php">
            <span class="logo-icon">🐻</span>
            <span class="logo-text">
              <span class="logo-top">TOYS</span>
              <span class="logo-bottom">NOVA</span>
            </span>
          </a>
          <p style="font-size: 0.82rem; color: #9CA3AF; line-height: 1.6;">
            Tu destino favorito para encontrar los mejores juguetes. Diversión garantizada para toda la familia.
          </p>
          <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
          </div>
        </div>

        <!-- Links -->
        <div class="col-lg-2 col-md-6 col-6">
          <h5>Tienda</h5>
          <ul>
            <li><a href="#">Novedades</a></li>
            <li><a href="#">Más vendidos</a></li>
            <li><a href="#">Ofertas</a></li>
            <li><a href="#">Categorías</a></li>
            <li><a href="#">Marcas</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-6 col-6">
          <h5>Ayuda</h5>
          <ul>
            <li><a href="#">Preguntas frecuentes</a></li>
            <li><a href="#">Envíos</a></li>
            <li><a href="#">Devoluciones</a></li>
            <li><a href="#">Rastrear pedido</a></li>
            <li><a href="#">Contacto</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-6 col-6">
          <h5>Empresa</h5>
          <ul>
            <li><a href="#">Sobre nosotros</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Trabaja con nosotros</a></li>
            <li><a href="#">Prensa</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 col-6">
          <h5>Contacto</h5>
          <ul>
            <li><a href="mailto:hola@toysnova.com"><i class="bi bi-envelope me-2"></i>hola@toysnova.com</a></li>
            <li><a href="tel:+521234567890"><i class="bi bi-telephone me-2"></i>(123) 456-7890</a></li>
            <li><a href="#"><i class="bi bi-geo-alt me-2"></i>Ciudad de México, MX</a></li>
          </ul>
        </div>

      </div>

      <div class="footer-bottom">
        <p>© 2026 Tienda de Juguetes. Todos los derechos reservados.</p>
        <!-- <div class="footer-payment">
          <span>Visa</span>
          <span>Mastercard</span>
          <span>PayPal</span>
          <span>OXXO</span>
        </div>
      </div>
    </div>
  </footer> -->
        <!-- se comento el metodo de pago -->


        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content product-modal-content">
              <button type="button" class="btn-close product-modal-close" data-bs-dismiss="modal"
                aria-label="Cerrar"></button>
              <div class="row g-0">
                <div class="col-lg-6">
                  <div class="product-modal-image-wrap">
                    <img id="modalProductImage" src="" alt="" class="product-modal-image">
                    <div class="modal-nav-arrows" role="group" aria-label="Cambiar vista del juguete">
                      <button class="modal-arrow modal-arrow-left" id="modalArrowLeft" aria-label="Vista anterior"><i
                          class="bi bi-chevron-left"></i></button>
                      <button class="modal-arrow modal-arrow-right" id="modalArrowRight" aria-label="Vista siguiente"><i
                          class="bi bi-chevron-right"></i></button>
                    </div>
                    <div class="modal-view-dots" aria-hidden="true">
                      <span class="modal-view-dot" data-dot="izquierda"></span>
                      <span class="modal-view-dot active" data-dot="frente"></span>
                      <span class="modal-view-dot" data-dot="derecha"></span>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="product-modal-body">
                    <span class="product-category-tag" id="modalProductCategory"></span>
                    <h3 id="productModalLabel"></h3>
                    <p id="modalProductDescription"></p>
                    <div class="product-modal-meta">
                      <div class="product-modal-price" id="modalProductPrice"></div>
                      <div class="product-modal-stock" id="modalProductStock"></div>
                    </div>
                    <button type="button" class="btn-primary-custom w-100 justify-content-center mt-3 py-3"
                      id="modalBtnAddToCart" style="border-radius: 14px; font-size: 1rem; font-weight: 800;">
                      <i class="bi bi-cart-plus-fill fs-5 me-2"></i>
                      Agregar al Carrito
                    </button>
                    <p class="product-modal-help mt-3 mb-0">Haz clic en las flechas para cambiar entre las vistas del
                      producto.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
        <!-- General App Scripts -->
        <script src="assets/js/cart.js"></script>
        <script src="assets/js/script.js"></script>
</body>

</html>