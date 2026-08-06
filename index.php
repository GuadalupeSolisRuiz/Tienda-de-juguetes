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

    // ── Estadísticas reales desde la Base de Datos ──
    $realTotalProdCount = count($productos);

    $resUsersCount = $conexion->query("SELECT COUNT(*) AS total FROM usuarios");
    $realTotalUsersCount = $resUsersCount ? (int)($resUsersCount->fetch_assoc()['total'] ?? 0) : 0;

    $resOrdersCount = $conexion->query("SELECT COUNT(*) AS total FROM pedidos");
    $realTotalOrdersCount = $resOrdersCount ? (int)($resOrdersCount->fetch_assoc()['total'] ?? 0) : 0;

    $resCatCount = $conexion->query("SELECT COUNT(*) AS total FROM categoria");
    $realTotalCatsCount = $resCatCount ? (int)($resCatCount->fetch_assoc()['total'] ?? 0) : 0;

    // ── Imagen aleatoria y conteo por categoría para la sección "Explora por categoría" ──
    $mapCat = [
      'nino'         => ['db' => 'Niños',       'keywords' => ['Niño', 'Robot', 'Auto', 'Cohete', 'Soldado', 'Avión', 'Pista', 'Carreras']],
      'nina'         => ['db' => 'Niñas',       'keywords' => ['Niña', 'Muñeca', 'Princesa', 'Pony', 'Rosa', 'Cocina', 'Casa', 'Castillo']],
      'bebe'         => ['db' => 'Bebés',       'keywords' => ['Bebé', 'Sonajero', 'Pulpo', 'Oso', 'Gimnasio', 'Mecedora', 'Xilófono']],
      'peluches'     => ['db' => 'Peluches',    'keywords' => ['Peluche', 'Oso', 'León', 'Dinosaurio', 'Conejo', 'Perro']],
      'educativos'   => ['db' => 'Educativos',  'keywords' => ['Educativo', 'Didáctico', 'Gimnasio', 'Sonajero', 'Xilófono', 'Cocina', 'Pulpo']],
      'vehiculos'    => ['db' => 'Vehículos',   'keywords' => ['Vehículo', 'Auto', 'Carro', 'Moto', 'Avión', 'Pista', 'Carreras']],
      'electronicos' => ['db' => 'Electrónicos','keywords' => ['Electrónico', 'Robot', 'Control', 'Volante', 'Interactivo']],
      'munecas'      => ['db' => 'Muñecas',     'keywords' => ['Muñeca', 'Princesa', 'Castillo', 'Casa', 'Soldado', 'Pony']],
      'exterior'     => ['db' => 'Exterior',    'keywords' => ['Exterior', 'Bicicleta', 'Cohete', 'Mecedora', 'Pistola']]
    ];

    $imgsCat = [];
    $countsCat = [];

    foreach ($mapCat as $slug => $catInfo) {
      $nombre_db = $catInfo['db'];
      $keywords  = $catInfo['keywords'] ?? $catInfo['kw'] ?? [];

      // 1. Intentar buscar productos por categoría directa (id_categoria)
      $cq = $conexion->prepare("
        SELECT p.imagen
        FROM productos p
        INNER JOIN categoria c ON p.id_categoria = c.id_categoria
        WHERE c.nombre_categoria = ? AND p.id_disponible = 1
        ORDER BY RAND() LIMIT 1
      ");
      $cq->bind_param('s', $nombre_db);
      $cq->execute();
      $crow = $cq->get_result()->fetch_assoc();
      $cq->close();

      $ccount = $conexion->prepare("SELECT COUNT(*) AS n FROM productos p INNER JOIN categoria c ON p.id_categoria=c.id_categoria WHERE c.nombre_categoria=? AND p.id_disponible=1");
      $ccount->bind_param('s', $nombre_db);
      $ccount->execute();
      $crow2 = $ccount->get_result()->fetch_assoc();
      $ccount->close();
      $totalCount = (int) ($crow2['n'] ?? 0);

      // 2. Si no hay productos con la categoría exacta en FK, buscar por palabras clave
      if ($totalCount === 0 && !empty($keywords)) {
        $whereClauses = [];
        $params = [];
        $types = "";
        foreach ($keywords as $kw) {
          $whereClauses[] = "p.nombre_producto LIKE ? OR p.descripcion LIKE ?";
          $params[] = "%" . $kw . "%";
          $params[] = "%" . $kw . "%";
          $types .= "ss";
        }
        $whereSql = implode(" OR ", $whereClauses);

        // Contar coincidencias
        $kwCountStmt = $conexion->prepare("SELECT COUNT(DISTINCT p.id_productos) AS n FROM productos p WHERE p.id_disponible = 1 AND ({$whereSql})");
        if ($kwCountStmt) {
          $kwCountStmt->bind_param($types, ...$params);
          $kwCountStmt->execute();
          $resKwCount = $kwCountStmt->get_result()->fetch_assoc();
          $totalCount = (int) ($resKwCount['n'] ?? 0);
          $kwCountStmt->close();
        }

        // Obtener imagen aleatoria por palabras clave
        $kwImgStmt = $conexion->prepare("SELECT p.imagen FROM productos p WHERE p.id_disponible = 1 AND ({$whereSql}) ORDER BY RAND() LIMIT 1");
        if ($kwImgStmt) {
          $kwImgStmt->bind_param($types, ...$params);
          $kwImgStmt->execute();
          $crow = $kwImgStmt->get_result()->fetch_assoc();
          $kwImgStmt->close();
        }
      }

      // 3. Fallback final si no se encontró imagen especifica
      if (!$crow) {
        $fallStmt = $conexion->prepare("SELECT p.imagen FROM productos p WHERE p.id_disponible = 1 ORDER BY RAND() LIMIT 1");
        if ($fallStmt) {
          $fallStmt->execute();
          $crow = $fallStmt->get_result()->fetch_assoc();
          $fallStmt->close();
        }
      }

      $countsCat[$slug] = $totalCount;
      if ($crow) {
        $v = json_decode($crow['imagen'], true);
        $imgsCat[$slug] = is_array($v) ? ($v['frente'] ?? null) : $crow['imagen'];
      } else {
        $imgsCat[$slug] = null;
      }
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
      <span class="ticker-item"><i class="bi bi-stars"></i> +<?= $realTotalProdCount ?? 0 ?> juguetes listos en catálogo</span>
      <span class="ticker-item"><i class="bi bi-percent"></i> Descuentos especiales y ofertas relámpago</span>
      <span class="ticker-item"><i class="bi bi-heart-fill"></i> +<?= max(1, $realTotalUsersCount ?? 0) ?> usuarios registrados</span>
      <span class="ticker-item"><i class="bi bi-shield-check"></i> Compra 100% segura y garantizada</span>
      <!-- duplicate for seamless loop -->
      <span class="ticker-item"><i class="bi bi-gift-fill"></i> ¡Envío GRATIS en compras mayores a $599!</span>
      <span class="ticker-item"><i class="bi bi-stars"></i> +<?= $realTotalProdCount ?? 0 ?> juguetes listos en catálogo</span>
      <span class="ticker-item"><i class="bi bi-percent"></i> Descuentos especiales y ofertas relámpago</span>
      <span class="ticker-item"><i class="bi bi-heart-fill"></i> +<?= max(1, $realTotalUsersCount ?? 0) ?> usuarios registrados</span>
      <span class="ticker-item"><i class="bi bi-shield-check"></i> Compra 100% segura y garantizada</span>
    </div>
  </div>


  <!-- ── FEATURED PRODUCTS CAROUSEL ── -->
  <section class="products-section" id="productos" style="padding: 40px 0 50px; background: #ffffff;">
    <div class="container">
      <!-- Section Header (Centered) -->
      <div class="section-header text-center mb-4">
        <span class="section-badge yellow"><i class="bi bi-star-fill"></i> Destacados</span>
        <h2 class="mb-1">Juguetes más populares</h2>
        <p class="mb-0">Los favoritos de nuestros clientes. ¡Explora nuestro catálogo en movimiento!</p>
      </div>

      <!-- Wrapper con Flechas Flotantes a los Lados -->
      <div class="products-carousel-wrapper position-relative">
        <button class="carousel-side-btn prev-btn" id="btnProductsPrev" aria-label="Anterior producto">
          <i class="bi bi-chevron-left"></i>
        </button>

        <!-- Carrusel de Productos -->
        <div class="products-carousel-track" id="productsCarouselTrack">
          <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $producto): ?>
              <div class="products-carousel-item">
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
                    <?php if ((int)$producto['stock'] <= 0): ?>
                      <span class="product-badge out-of-stock" style="background:#EF4444; color:#ffffff; font-weight:800; z-index:5;">Sin Stock</span>
                    <?php else: ?>
                      <span class="product-badge new">Nuevo</span>
                    <?php endif; ?>
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
                      <?php if ((int)$producto['stock'] <= 0): ?>
                        <button class="btn-add-cart disabled" style="background:#6B7280; opacity:0.6; cursor:not-allowed;" title="Producto sin stock" disabled><i class="bi bi-slash-circle"></i></button>
                      <?php else: ?>
                        <button class="btn-add-cart" aria-label="Agregar al carrito"><i class="bi bi-plus"></i></button>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="w-100">
              <div class="alert alert-info">No hay productos registrados en la base de datos todavía.</div>
            </div>
          <?php endif; ?>
        </div>

        <button class="carousel-side-btn next-btn" id="btnProductsNext" aria-label="Siguiente producto">
          <i class="bi bi-chevron-right"></i>
        </button>
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

  <!-- ── SECCIÓN OFERTAS ESPECIALES ── -->
  <section class="offers-section py-5" id="ofertas" style="background: linear-gradient(135deg, #FFF5F5 0%, #FEF3C7 50%, #FFF 100%); position: relative; border-top: 1px solid rgba(239, 68, 68, 0.1); border-bottom: 1px solid rgba(239, 68, 68, 0.1);">
    <div class="container py-3">
      
      <!-- Section Header -->
      <div class="section-header text-center mb-5">
        <span class="section-badge red mb-2" style="background:#FEE2E2; color:#EF4444; font-weight:800;">
          <i class="bi bi-fire me-1"></i> Ofertas Relámpago — Tiempo Limitado
        </span>
        <h2 class="font-fredoka fw-bold text-dark display-6 mb-2">Descuentos Especiales del Mes</h2>
        <p class="text-muted mx-auto" style="max-width:560px;">
          ¡Aprovecha precios rebajados en juguetes seleccionados! El descuento de cada oferta se aplica directamente al agregarlo a tu carrito y se reflejará en tu <strong>Ticket de Compra</strong>.
        </p>
      </div>

      <!-- Grid of Offer Cards -->
      <div class="row g-4 justify-content-center">
        
        <!-- Offer Item 1: Control juega y aprende (id 13) -->
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm h-100 position-relative offer-product-card" style="border-radius:20px; overflow:hidden; transition:transform 0.25s ease, box-shadow 0.25s ease;">
            <div class="position-absolute top-0 end-0 m-3 z-3">
              <span class="badge bg-danger fs-6 fw-bold px-3 py-2 shadow-sm" style="border-radius:30px;">-25% OFF</span>
            </div>
            <div class="p-4 text-center bg-white" style="height:220px; display:flex; align-items:center; justify-content:center;">
              <img src="Juguetes/prod_1785395782_frente_639.webp" alt="Control juega y aprende" style="max-height:170px; object-fit:contain;" onError="this.src='assets/img/placeholder.png'">
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between bg-light-subtle border-top">
              <div>
                <span class="badge bg-purple-subtle text-purple mb-2" style="background:rgba(124,58,237,0.1); color:#7C3AED;">Electrónicos</span>
                <h5 class="fw-bold text-dark mb-2" style="font-size:1.05rem;">Control juega y aprende</h5>
                <p class="text-muted small mb-3 text-truncate-2" style="font-size:0.82rem;">Divertido control interactivo con botones, colores y sonidos estimularles.</p>
              </div>
              <div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                  <del class="text-muted small">$450.00</del>
                  <strong class="fs-4 fw-extrabold text-purple" style="color:#7C3AED;">$337.50</strong>
                  <small class="badge bg-success-subtle text-success ms-auto" style="font-size:0.7rem;">Ahorras $112.50</small>
                </div>
                <button class="btn-primary-custom w-100 py-2.5 justify-content-center fw-bold" style="border-radius:12px; font-size:0.9rem;"
                  onclick="window.addOfferToCart(13, 'Control juega y aprende', 450.00, 337.50, 'Juguetes/prod_1785395782_frente_639.webp', 'Electrónicos')">
                  <i class="bi bi-bag-plus-fill me-1"></i> Agregar Oferta
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Offer Item 2: Oso de peluche (id 8) -->
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm h-100 position-relative offer-product-card" style="border-radius:20px; overflow:hidden; transition:transform 0.25s ease, box-shadow 0.25s ease;">
            <div class="position-absolute top-0 end-0 m-3 z-3">
              <span class="badge bg-danger fs-6 fw-bold px-3 py-2 shadow-sm" style="border-radius:30px;">-20% OFF</span>
            </div>
            <div class="p-4 text-center bg-white" style="height:220px; display:flex; align-items:center; justify-content:center;">
              <img src="Juguetes/prod_8_frente_1785395438.webp" alt="Oso de peluche" style="max-height:170px; object-fit:contain;" onError="this.src='assets/img/placeholder.png'">
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between bg-light-subtle border-top">
              <div>
                <span class="badge bg-purple-subtle text-purple mb-2" style="background:rgba(124,58,237,0.1); color:#7C3AED;">Peluches</span>
                <h5 class="fw-bold text-dark mb-2" style="font-size:1.05rem;">Oso de peluche</h5>
                <p class="text-muted small mb-3 text-truncate-2" style="font-size:0.82rem;">Adorable oso suave diseñado para brindar ternura y compañía.</p>
              </div>
              <div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                  <del class="text-muted small">$250.00</del>
                  <strong class="fs-4 fw-extrabold text-purple" style="color:#7C3AED;">$200.00</strong>
                  <small class="badge bg-success-subtle text-success ms-auto" style="font-size:0.7rem;">Ahorras $50.00</small>
                </div>
                <button class="btn-primary-custom w-100 py-2.5 justify-content-center fw-bold" style="border-radius:12px; font-size:0.9rem;"
                  onclick="window.addOfferToCart(8, 'Oso de peluche', 250.00, 200.00, 'Juguetes/prod_8_frente_1785395438.webp', 'Peluches')">
                  <i class="bi bi-bag-plus-fill me-1"></i> Agregar Oferta
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Offer Item 3: Auto de carreras RC (id 16) -->
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm h-100 position-relative offer-product-card" style="border-radius:20px; overflow:hidden; transition:transform 0.25s ease, box-shadow 0.25s ease;">
            <div class="position-absolute top-0 end-0 m-3 z-3">
              <span class="badge bg-danger fs-6 fw-bold px-3 py-2 shadow-sm" style="border-radius:30px;">-20% OFF</span>
            </div>
            <div class="p-4 text-center bg-white" style="height:220px; display:flex; align-items:center; justify-content:center;">
              <img src="Juguetes/prod_1785403943_frente_115.webp" alt="Auto de carreras RC" style="max-height:170px; object-fit:contain;" onError="this.src='assets/img/placeholder.png'">
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between bg-light-subtle border-top">
              <div>
                <span class="badge bg-purple-subtle text-purple mb-2" style="background:rgba(124,58,237,0.1); color:#7C3AED;">Vehículos</span>
                <h5 class="fw-bold text-dark mb-2" style="font-size:1.05rem;">Auto de carreras RC</h5>
                <p class="text-muted small mb-3 text-truncate-2" style="font-size:0.82rem;">Auto a control remoto de alta velocidad con luces LED y alcance 20m.</p>
              </div>
              <div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                  <del class="text-muted small">$399.00</del>
                  <strong class="fs-4 fw-extrabold text-purple" style="color:#7C3AED;">$319.20</strong>
                  <small class="badge bg-success-subtle text-success ms-auto" style="font-size:0.7rem;">Ahorras $79.80</small>
                </div>
                <button class="btn-primary-custom w-100 py-2.5 justify-content-center fw-bold" style="border-radius:12px; font-size:0.9rem;"
                  onclick="window.addOfferToCart(16, 'Auto de carreras RC', 399.00, 319.20, 'Juguetes/prod_1785403943_frente_115.webp', 'Vehículos')">
                  <i class="bi bi-bag-plus-fill me-1"></i> Agregar Oferta
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Offer Item 4: Gimnasio musical para bebé (id 25) -->
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm h-100 position-relative offer-product-card" style="border-radius:20px; overflow:hidden; transition:transform 0.25s ease, box-shadow 0.25s ease;">
            <div class="position-absolute top-0 end-0 m-3 z-3">
              <span class="badge bg-danger fs-6 fw-bold px-3 py-2 shadow-sm" style="border-radius:30px;">-30% OFF</span>
            </div>
            <div class="p-4 text-center bg-white" style="height:220px; display:flex; align-items:center; justify-content:center;">
              <img src="Juguetes/prod_1785404989_frente_612.webp" alt="Gimnasio musical para bebé" style="max-height:170px; object-fit:contain;" onError="this.src='assets/img/placeholder.png'">
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between bg-light-subtle border-top">
              <div>
                <span class="badge bg-purple-subtle text-purple mb-2" style="background:rgba(124,58,237,0.1); color:#7C3AED;">Educativos</span>
                <h5 class="fw-bold text-dark mb-2" style="font-size:1.05rem;">Gimnasio musical</h5>
                <p class="text-muted small mb-3 text-truncate-2" style="font-size:0.82rem;">Tapete acolchado con piano interactivo y juguetes colgantes.</p>
              </div>
              <div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                  <del class="text-muted small">$699.00</del>
                  <strong class="fs-4 fw-extrabold text-purple" style="color:#7C3AED;">$489.30</strong>
                  <small class="badge bg-success-subtle text-success ms-auto" style="font-size:0.7rem;">Ahorras $209.70</small>
                </div>
                <button class="btn-primary-custom w-100 py-2.5 justify-content-center fw-bold" style="border-radius:12px; font-size:0.9rem;"
                  onclick="window.addOfferToCart(25, 'Gimnasio musical para bebé', 699.00, 489.30, 'Juguetes/prod_1785404989_frente_612.webp', 'Educativos')">
                  <i class="bi bi-bag-plus-fill me-1"></i> Agregar Oferta
                </button>
              </div>
            </div>
          </div>
        </div>

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
          'nino' => ['label' => 'Niños', 'emoji' => '\uD83D\uDC66', 'color' => '#3B82F6', 'bg' => 'bg-blue', 'id' => 'cat-ninos'],
          'nina' => ['label' => 'Niñas', 'emoji' => '\uD83D\uDC67', 'color' => '#EC4899', 'bg' => 'bg-pink', 'id' => 'cat-ninas'],
          'bebe' => ['label' => 'Bebés', 'emoji' => '\uD83C\uDF7C', 'color' => '#10B981', 'bg' => 'bg-green', 'id' => 'cat-bebes'],
          'peluches' => ['label' => 'Peluches', 'emoji' => '\uD83E\uDDF8', 'color' => '#F59E0B', 'bg' => 'bg-amber', 'id' => 'cat-peluches'],
          'educativos' => ['label' => 'Educativos', 'emoji' => '\uD83E\uDDE9', 'color' => '#8B5CF6', 'bg' => 'bg-purple', 'id' => 'cat-educativos'],
          'vehiculos' => ['label' => 'Vehículos', 'emoji' => '\uD83C\uDFCE', 'color' => '#EF4444', 'bg' => 'bg-red', 'id' => 'cat-vehiculos'],
          'electronicos' => ['label' => 'Electrónicos', 'emoji' => '\uD83C\uDFAE', 'color' => '#6366F1', 'bg' => 'bg-indigo', 'id' => 'cat-electronicos'],
          'munecas' => ['label' => 'Muñecas', 'emoji' => '\uD83E\uDE86', 'color' => '#D946EF', 'bg' => 'bg-fuchsia', 'id' => 'cat-munecas'],
          'exterior' => ['label' => 'Exterior', 'emoji' => '\uD83D\uDEB2', 'color' => '#14B8A6', 'bg' => 'bg-teal', 'id' => 'cat-exterior'],
        ];
        foreach ($cats as $slug => $cat):
          $img = $imgsCat[$slug] ?? null;
          $count = $countsCat[$slug] ?? 0;
          ?>
          <div class="col-6 col-md-4 col-lg-3">
            <a href="categoria.php?c=<?= $slug ?>" class="category-card" id="<?= $cat['id'] ?>"
              style="overflow:hidden; padding:0; border-radius: 20px; transition: all 0.3s ease; display: block; text-decoration: none; background: #ffffff;">
              <div
                style="height:175px; overflow:hidden; border-radius:18px 18px 0 0; background: linear-gradient(135deg, <?= $cat['color'] ?>15 0%, #f9fafb 100%); display:flex; align-items:center; justify-content:center; position:relative; padding:14px;">
                <?php if ($img): ?>
                  <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($cat['label']) ?>"
                    style="max-width:100%; max-height:100%; object-fit:contain; filter: drop-shadow(0 6px 10px rgba(0,0,0,0.09)); transition:transform .35s ease;"
                    class="cat-thumb-img">
                  <div
                    style="position:absolute;inset:0;background:linear-gradient(to top,<?= $cat['color'] ?>20 0%,transparent 60%);pointer-events:none;">
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
                  <?= $count > 0 ? "+{$count} producto" . ($count !== 1 ? 's' : '') : 'Explorar categoría' ?>
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
            <h3>Atención al Cliente</h3>
            <p>Atención personalizada de Lunes a Sábado de 9:00 AM a 8:00 PM.</p>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ── SECCIÓN SOBRE NOSOTROS ── -->
  <section class="about-section py-5" id="nosotros" style="background: linear-gradient(180deg, #FFFFFF 0%, #FDF8F0 100%); position: relative;">
    <div class="container py-4">
      <div class="row align-items-center g-5">
        <!-- Left: Image / Visual Banner -->
        <div class="col-lg-6">
          <div class="about-visual-card p-4 rounded-5 text-center shadow-lg position-relative border" style="background: linear-gradient(135deg, #EDE9FE 0%, #FCE7F3 100%); border-color: rgba(124, 58, 237, 0.15) !important; border-radius: 24px;">
            <div class="about-badge bg-white text-purple fw-bold px-3 py-1 rounded-pill shadow-sm d-inline-block mb-3" style="color:#7C3AED;">
              ✨ Conoce TOYS NOVA
            </div>
            <div class="display-1 mb-3">🐻🚀</div>
            <h3 class="font-fredoka fw-bold text-dark mb-2">Más que una tienda, una fábrica de sonrisas</h3>
            <p class="text-muted small mb-4">
              Nos dedicamos a seleccionar los juguetes más didácticos, seguros y divertidos para acompañar el crecimiento de tus pequeños en cada etapa.
            </p>
            <div class="row g-3 text-start">
              <div class="col-6">
                <div class="bg-white p-3 rounded-4 border shadow-sm" style="border-radius:16px;">
                  <div class="fs-4 text-purple mb-1" style="color:#7C3AED;">🎁 <?= $realTotalProdCount ?? 0 ?> Juguetes</div>
                  <small class="fw-bold text-dark d-block">En Catálogo Real</small>
                  <small class="text-muted" style="font-size:0.75rem;"><?= $realTotalCatsCount ?? 0 ?> Categorías activas</small>
                </div>
              </div>
              <div class="col-6">
                <div class="bg-white p-3 rounded-4 border shadow-sm" style="border-radius:16px;">
                  <div class="fs-4 text-success mb-1">📦 <?= $realTotalOrdersCount ?? 0 ?> Pedidos</div>
                  <small class="fw-bold text-dark d-block">Registrados</small>
                  <small class="text-muted" style="font-size:0.75rem;"><?= $realTotalUsersCount ?? 0 ?> Usuarios activos</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Description & Pillars -->
        <div class="col-lg-6">
          <span class="section-badge purple mb-2"><i class="bi bi-heart-fill me-1"></i> Nuestra Pasión</span>
          <h2 class="font-fredoka fw-bold text-dark display-6 mb-3">Creamos aventuras inolvidables para cada niño</h2>
          <p class="text-muted leading-relaxed mb-4">
            En <strong>TOYS NOVA</strong> creemos firmemente en el poder del juego como la herramienta fundamental para el aprendizaje, la creatividad y el desarrollo afectivo. Seleccionamos cuidadosamente cada peluche, vehículo, muñeca y juego educativo asegurando los más altos estándares de calidad.
          </p>

          <div class="d-flex flex-column gap-3">
            <div class="d-flex align-items-start gap-3 p-3 rounded-4 bg-white border shadow-sm" style="border-radius:16px;">
              <div class="about-icon-pill p-3 rounded-circle" style="background:rgba(124,58,237,0.1); color:#7C3AED;">
                <i class="bi bi-shield-check fs-4"></i>
              </div>
              <div>
                <h6 class="fw-bold text-dark mb-1">Juguetes 100% Seguros y Certificados</h6>
                <p class="text-muted small mb-0">Materiales no tóxicos, bordes suaves y pruebas de durabilidad garantizadas.</p>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 p-3 rounded-4 bg-white border shadow-sm" style="border-radius:16px;">
              <div class="about-icon-pill p-3 rounded-circle" style="background:rgba(236,72,153,0.1); color:#EC4899;">
                <i class="bi bi-lightbulb-fill fs-4"></i>
              </div>
              <div>
                <h6 class="fw-bold text-dark mb-1">Estimulación Didáctica y Creativa</h6>
                <p class="text-muted small mb-0">Juguetes diseñados para potenciar la motricidad, imaginación y lógica desde los primeros meses.</p>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 p-3 rounded-4 bg-white border shadow-sm" style="border-radius:16px;">
              <div class="about-icon-pill p-3 rounded-circle" style="background:rgba(16,185,129,0.1); color:#10B981;">
                <i class="bi bi-truck-front-fill fs-4"></i>
              </div>
              <div>
                <h6 class="fw-bold text-dark mb-1">Envío Rápido y Garantía de Entrega</h6>
                <p class="text-muted small mb-0">Entregas puntuales y atención personalizada para que la sorpresa llegue justo a tiempo.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── SECCIÓN CONTACTO ── -->
  <section class="contact-section py-5" id="contacto" style="background: #ffffff; position: relative;">
    <div class="container py-4">
      <div class="section-header text-center mb-5">
        <span class="section-badge blue mb-2"><i class="bi bi-envelope-heart-fill me-1"></i> ¡Estamos para ayudarte!</span>
        <h2 class="font-fredoka fw-bold text-dark display-6">Ponte en contacto con nosotros</h2>
        <p class="text-muted">¿Tienes alguna duda sobre tus pedidos o necesitas asesoría para elegir el juguete ideal?</p>
      </div>

      <div class="row g-4 align-items-stretch">
        <!-- Info cards -->
        <div class="col-lg-5">
          <div class="p-4 rounded-5 border h-100 shadow-sm d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, #F3F4F6 0%, #EDE9FE 100%); border-radius:24px;">
            <div>
              <h4 class="font-fredoka fw-bold mb-4 text-purple" style="color:#7C3AED;">Información de Contacto</h4>
              
              <div class="d-flex align-items-center gap-3 mb-4 bg-white p-3 rounded-4 border shadow-sm" style="border-radius:16px;">
                <div class="p-3 rounded-circle" style="background:rgba(124,58,237,0.1); color:#7C3AED;">
                  <i class="bi bi-geo-alt-fill fs-4"></i>
                </div>
                <div>
                  <strong class="d-block text-dark small">Ubicación Principal</strong>
                  <small class="text-muted">Av. Universidad #1200, Col. del Valle, CDMX</small>
                </div>
              </div>

              <div class="d-flex align-items-center gap-3 mb-4 bg-white p-3 rounded-4 border shadow-sm" style="border-radius:16px;">
                <div class="p-3 rounded-circle" style="background:rgba(236,72,153,0.1); color:#EC4899;">
                  <i class="bi bi-telephone-fill fs-4"></i>
                </div>
                <div>
                  <strong class="d-block text-dark small">Teléfono & WhatsApp</strong>
                  <small class="text-muted">(55) 5555-5555</small>
                </div>
              </div>

              <div class="d-flex align-items-center gap-3 mb-4 bg-white p-3 rounded-4 border shadow-sm" style="border-radius:16px;">
                <div class="p-3 rounded-circle" style="background:rgba(16,185,129,0.1); color:#10B981;">
                  <i class="bi bi-envelope-at-fill fs-4"></i>
                </div>
                <div>
                  <strong class="d-block text-dark small">Correo Electrónico</strong>
                  <small class="text-muted">hola@toysnova.com</small>
                </div>
              </div>

              <div class="d-flex align-items-center gap-3 bg-white p-3 rounded-4 border shadow-sm" style="border-radius:16px;">
                <div class="p-3 rounded-circle" style="background:rgba(245,158,11,0.1); color:#F59E0B;">
                  <i class="bi bi-clock-fill fs-4"></i>
                </div>
                <div>
                  <strong class="d-block text-dark small">Horarios de Atención</strong>
                  <small class="text-muted">Lunes a Sábado: 9:00 AM - 8:00 PM</small>
                </div>
              </div>
            </div>

            <div class="mt-4 text-center border-top pt-3">
              <small class="text-muted"><i class="bi bi-shield-check text-purple me-1"></i> Garantía de Respuesta en Menos de 24 Horas</small>
            </div>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-7">
          <div class="p-4 p-md-5 rounded-5 border bg-white shadow-sm h-100" style="border-radius:24px;">
            <h4 class="font-fredoka fw-bold text-dark mb-2">Envíanos un mensaje</h4>
            <p class="text-muted small mb-4">Déjanos tus datos y te responderemos en menos de 24 horas.</p>

            <div id="contactFormAlert" class="alert mb-3" style="display:none; border-radius:12px;"></div>

            <form id="mainContactForm">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="contactName" class="form-label small fw-bold">Nombre completo <span class="text-danger">*</span></label>
                  <input type="text" id="contactName" name="nombre" class="form-control" placeholder="Ej. Ana Martínez" required>
                </div>
                <div class="col-md-6">
                  <label for="contactEmail" class="form-label small fw-bold">Correo electrónico <span class="text-danger">*</span></label>
                  <input type="email" id="contactEmail" name="correo" class="form-control" placeholder="ana@ejemplo.com" required>
                </div>
                <div class="col-md-6">
                  <label for="contactPhone" class="form-label small fw-bold">Teléfono (opcional)</label>
                  <input type="tel" id="contactPhone" name="telefono" class="form-control" placeholder="(55) 0000-0000">
                </div>
                <div class="col-md-6">
                  <label for="contactSubject" class="form-label small fw-bold">Asunto <span class="text-danger">*</span></label>
                  <select id="contactSubject" name="asunto" class="form-select" required>
                    <option value="" selected disabled>Selecciona una opción</option>
                    <option value="Consulta de Pedido">Consulta de Pedido</option>
                    <option value="Información de Productos">Información de Productos</option>
                    <option value="Devoluciones o Garantía">Devoluciones o Garantía</option>
                    <option value="Ventas Corporativas">Ventas Corporativas</option>
                    <option value="Otro">Otro</option>
                  </select>
                </div>
                <div class="col-12">
                  <label for="contactMessage" class="form-label small fw-bold">Mensaje <span class="text-danger">*</span></label>
                  <textarea id="contactMessage" name="mensaje" class="form-control" rows="4" placeholder="Escribe tu consulta aquí..." required></textarea>
                </div>
                <div class="col-12 mt-4">
                  <button type="submit" class="btn-primary-custom w-100 py-3 font-fredoka fw-bold text-white d-flex align-items-center justify-content-center gap-2" style="border-radius:14px;">
                    <i class="bi bi-send-fill"></i> Enviar Mensaje
                  </button>
                </div>
              </div>
            </form>
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
        <div class="col-lg-4 col-md-6">
          <a class="d-flex align-items-center gap-2 text-decoration-none mb-3 footer-brand" href="index.php">
            <span class="logo-icon">🐻</span>
            <span class="logo-text">
              <span class="logo-top">TOYS</span>
              <span class="logo-bottom">NOVA</span>
            </span>
          </a>
          <p style="font-size: 0.82rem; color: #9CA3AF; line-height: 1.6;" class="mb-0">
            Tu tienda favorita para encontrar los mejores juguetes. Diversión, calidad y sonrisas garantizadas para toda la familia.
          </p>
        </div>

        <!-- Links Tienda -->
        <div class="col-lg-3 col-md-6 col-6">
          <h5>Explorar Tienda</h5>
          <ul>
            <li><a href="index.php#productos"><i class="bi bi-chevron-right small me-1"></i> Ver Catálogo</a></li>
            <li><a href="index.php#ofertas"><i class="bi bi-chevron-right small me-1"></i> Ofertas Especiales</a></li>
            <li><a href="index.php#categorias"><i class="bi bi-chevron-right small me-1"></i> Categorías</a></li>
            <li><a href="categoria.php?c=todos"><i class="bi bi-chevron-right small me-1"></i> Todos los Productos</a></li>
          </ul>
        </div>

        <!-- Links Empresa y Ayuda -->
        <div class="col-lg-2 col-md-6 col-6">
          <h5>Empresa & Ayuda</h5>
          <ul>
            <li><a href="#nosotros" data-bs-toggle="modal" data-bs-target="#nosotrosModal"><i class="bi bi-chevron-right small me-1"></i> Sobre Nosotros</a></li>
            <li><a href="#contacto" data-bs-toggle="modal" data-bs-target="#contactoModal"><i class="bi bi-chevron-right small me-1"></i> Contacto</a></li>
            <li><a href="javascript:void(0)" onclick="window.loadUserOrders()"><i class="bi bi-chevron-right small me-1"></i> Mis Pedidos</a></li>
          </ul>
        </div>

        <!-- Datos de Contacto Directo -->
        <div class="col-lg-3 col-md-6">
          <h5>Contacto Directo</h5>
          <ul>
            <li><a href="mailto:soporte@toysnova.com"><i class="bi bi-envelope me-2" style="color:#7C3AED;"></i>soporte@toysnova.com</a></li>
            <li><a href="tel:5555555555"><i class="bi bi-telephone me-2" style="color:#7C3AED;"></i>(55) 5555-5555</a></li>
            <li><a href="#contacto" data-bs-toggle="modal" data-bs-target="#contactoModal"><i class="bi bi-geo-alt me-2" style="color:#7C3AED;"></i>Av. Universidad #1200, CDMX</a></li>
          </ul>
        </div>

      </div>

      <div class="footer-bottom mt-4 pt-3 border-top border-secondary border-opacity-25 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <p class="mb-0 text-muted" style="font-size: 0.8rem;">
          © <?php echo date('Y'); ?> <strong>TOYS NOVA</strong>. Todos los derechos reservados.
        </p>
        <small class="text-muted" style="font-size: 0.78rem;">
          <i class="bi bi-shield-lock-fill text-success me-1"></i> Compra 100% Segura con Encriptación SSL
        </small>
      </div>

    </div>
  </footer>
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