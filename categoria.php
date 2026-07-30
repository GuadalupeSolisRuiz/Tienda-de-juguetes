<?php
session_start();
include 'include/conect.php';

// ── Configuración de categorías predefinidas y slugs ──
$categoriasConfig = [
    'todos' => [
        'label'       => 'Todos los Juguetes',
        'emoji'       => '🎁',
        'color'       => '#7C3AED',
        'bg'          => '#F5F3FF',
        'desc'        => 'Explora nuestro catálogo completo de juguetes divertidos y educativos para todas las edades.',
        'keywords'    => []
    ],
    'nina' => [
        'label'       => 'Niñas',
        'emoji'       => '👧',
        'color'       => '#EC4899',
        'bg'          => '#FDF2F8',
        'desc'        => 'Descubre la mejor selección de juguetes, muñecas y juegos especiales para niñas.',
        'keywords'    => ['Niña', 'Niñas', 'Muñeca', 'Princesa']
    ],
    'nino' => [
        'label'       => 'Niños',
        'emoji'       => '👦',
        'color'       => '#3B82F6',
        'bg'          => '#EFF6FF',
        'desc'        => 'Encuentra autos, figuras de acción, superhéroes y aventuras para niños.',
        'keywords'    => ['Niño', 'Niños', 'Auto', 'Vehículo', 'Héroe']
    ],
    'bebe' => [
        'label'       => 'Bebés',
        'emoji'       => '🍼',
        'color'       => '#10B981',
        'bg'          => '#ECFDF5',
        'desc'        => 'Juguetes suaves, sonajeros y estimulación temprana totalmente seguros para bebés.',
        'keywords'    => ['Bebé', 'Bebés', 'Bebe', 'Sonajero', 'Estimulación', 'Cuna']
    ],
    'educativos' => [
        'label'       => 'Educativos',
        'emoji'       => '🧩',
        'color'       => '#8B5CF6',
        'bg'          => '#F5F3FF',
        'desc'        => 'Juegos didácticos, rompecabezas y juguetes de aprendizaje para agilizar la mente.',
        'keywords'    => ['Educativo', 'Educativos', 'Didáctico', 'Puzzle', 'Rompecabezas', 'Bloques']
    ],
    'electronicos' => [
        'label'       => 'Electrónicos',
        'emoji'       => '🎮',
        'color'       => '#6366F1',
        'bg'          => '#EEF2FF',
        'desc'        => 'Robots, controles remotos, konsolas y juguetes interactivos con sonidos y luces.',
        'keywords'    => ['Electrónico', 'Electrónicos', 'Robot', 'Control', 'Batería', 'Digital', 'Controlador']
    ],
    'peluches' => [
        'label'       => 'Peluches',
        'emoji'       => '🧸',
        'color'       => '#F59E0B',
        'bg'          => '#FEF3C7',
        'desc'        => 'Peluches ultra suaves de todos los tamaños, ideales para abrazar y acompañar.',
        'keywords'    => ['Peluche', 'Peluches', 'Oso', 'Suave', 'Almohada']
    ]
];

$slug = strtolower(trim($_GET['c'] ?? $_GET['cat'] ?? 'todos'));
if (!array_key_exists($slug, $categoriasConfig)) {
    // Si no coincide con un slug predefinido, crear una configuración dinámica
    $rawCategoryName = ucfirst(htmlspecialchars($slug));
    $cfg = [
        'label'    => $rawCategoryName,
        'emoji'    => '⭐',
        'color'    => '#7C3AED',
        'bg'       => '#F5F3FF',
        'desc'     => "Explora los juguetes pertenecientes a la categoría {$rawCategoryName}.",
        'keywords' => [$slug]
    ];
} else {
    $cfg = $categoriasConfig[$slug];
}

// ── Consulta SQL para obtener los productos ──
$productos = [];
if ($conexion) {
    if ($slug === 'todos' || empty($slug)) {
        // Traer todos los productos disponibles
        $sql = "
            SELECT p.id_productos, p.nombre_producto, p.descripcion, p.precio, p.stock, p.imagen,
                   COALESCE(c.nombre_categoria, 'General') AS nombre_categoria
            FROM productos p
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
            WHERE p.id_disponible = 1
            ORDER BY p.id_productos DESC
        ";
        $stmt = $conexion->prepare($sql);
    } else {
        // Filtrar por ID de categoría, nombre de categoría en BD, o palabras clave en producto
        $conditions = ["c.nombre_categoria LIKE ?"];
        $params = ["%" . $cfg['label'] . "%"];
        $types = "s";

        foreach ($cfg['keywords'] as $kw) {
            $conditions[] = "c.nombre_categoria LIKE ?";
            $conditions[] = "p.nombre_producto LIKE ?";
            $conditions[] = "p.descripcion LIKE ?";
            $params[] = "%" . $kw . "%";
            $params[] = "%" . $kw . "%";
            $params[] = "%" . $kw . "%";
            $types .= "sss";
        }

        $whereClause = implode(" OR ", $conditions);
        $sql = "
            SELECT DISTINCT p.id_productos, p.nombre_producto, p.descripcion, p.precio, p.stock, p.imagen,
                   COALESCE(c.nombre_categoria, ?) AS nombre_categoria
            FROM productos p
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
            WHERE p.id_disponible = 1 AND ({$whereClause})
            ORDER BY p.id_productos DESC
        ";
        
        $stmt = $conexion->prepare($sql);
        $bindParams = array_merge([$cfg['label']], $params);
        $bindTypes = "s" . $types;
        $stmt->bind_param($bindTypes, ...$bindParams);
    }

    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $vistas = json_decode($row['imagen'], true);
            if (!is_array($vistas)) {
                $vistas = ['frente' => $row['imagen'], 'izquierda' => $row['imagen'], 'derecha' => $row['imagen']];
            }
            $productos[] = [
                'id'          => (int)$row['id_productos'],
                'nombre'      => $row['nombre_producto'],
                'descripcion' => $row['descripcion'],
                'precio'      => (float)$row['precio'],
                'stock'       => (int)$row['stock'],
                'categoria'   => $row['nombre_categoria'],
                'vistas'      => $vistas,
            ];
        }
        $stmt->close();
    }

    // Si no hubo coincidencias específicas pero el catálogo tiene productos, buscar como fallback
    if (empty($productos) && $slug !== 'todos') {
        $qFallback = $conexion->prepare("
            SELECT p.id_productos, p.nombre_producto, p.descripcion, p.precio, p.stock, p.imagen,
                   COALESCE(c.nombre_categoria, 'General') AS nombre_categoria
            FROM productos p
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
            WHERE p.id_disponible = 1
            ORDER BY p.id_productos DESC
        ");
        if ($qFallback) {
            $qFallback->execute();
            $resF = $qFallback->get_result();
            while ($row = $resF->fetch_assoc()) {
                $vistas = json_decode($row['imagen'], true);
                if (!is_array($vistas)) {
                    $vistas = ['frente' => $row['imagen'], 'izquierda' => $row['imagen'], 'derecha' => $row['imagen']];
                }
                $productos[] = [
                    'id'          => (int)$row['id_productos'],
                    'nombre'      => $row['nombre_producto'],
                    'descripcion' => $row['descripcion'],
                    'precio'      => (float)$row['precio'],
                    'stock'       => (int)$row['stock'],
                    'categoria'   => $row['nombre_categoria'],
                    'vistas'      => $vistas,
                ];
            }
            $qFallback->close();
            $mostrandoFallback = true;
        }
    }
    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="Categoría <?= htmlspecialchars($cfg['label']) ?> — Tienda de Juguetes Toys NOVA."/>
  <title><?= htmlspecialchars($cfg['label']) ?> — Toys NOVA</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="assets/css/style.css" rel="stylesheet"/>
  <style>
    /* ── HERO CATEGORÍA COMPACTO ── */
    .cat-hero {
      background: linear-gradient(135deg, <?= $cfg['bg'] ?> 0%, #ffffff 100%);
      padding: 16px 0 12px;
      text-align: center;
      position: relative;
      overflow: hidden;
      border-bottom: 1px solid var(--border);
    }
    .cat-hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse at 50% 30%, <?= $cfg['color'] ?>18 0%, transparent 70%);
      pointer-events: none;
    }
    .cat-hero-emoji {
      font-size: 1.8rem;
      line-height: 1;
      display: inline-block;
      vertical-align: middle;
      margin-right: 6px;
      filter: drop-shadow(0 3px 6px <?= $cfg['color'] ?>33);
    }
    .cat-hero h1 {
      font-family: 'Fredoka One', cursive;
      font-size: 1.4rem;
      color: var(--text);
      display: inline-block;
      vertical-align: middle;
      margin-bottom: 0;
    }
    .cat-hero h1 span { color: <?= $cfg['color'] ?>; }
    .cat-hero p {
      font-size: 0.82rem;
      color: var(--text-light);
      max-width: 580px;
      margin: 4px auto 0;
    }
    .cat-hero-pill {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 10px;
      border-radius: 50px;
      font-size: 0.68rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .05em;
      margin-bottom: 4px;
    }

    /* ── BARRA NAVEGACIÓN RÁPIDA DE CATEGORÍAS (PILLS) ── */
    .category-pills-bar {
      background: #ffffff;
      padding: 8px 0;
      border-bottom: 1px solid var(--border);
      box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .pills-scroll {
      display: flex;
      align-items: center;
      gap: 8px;
      overflow-x: auto;
      padding-bottom: 2px;
      scrollbar-width: thin;
    }
    .pill-item {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: 50px;
      font-weight: 700;
      font-size: 0.82rem;
      text-decoration: none;
      color: var(--text-light);
      background: #F3F4F6;
      border: 1px solid transparent;
      white-space: nowrap;
      transition: all 0.2s ease;
    }
    .pill-item:hover {
      background: #EDE9FE;
      color: #7C3AED;
      transform: translateY(-1px);
    }
    .pill-item.active {
      background: <?= $cfg['color'] ?>;
      color: #ffffff !important;
      box-shadow: 0 4px 12px <?= $cfg['color'] ?>40;
    }

    /* ── PRODUCTS SECTION ── */
    .cat-products-section {
      padding: 20px 0 60px;
      background: var(--bg);
      min-height: 50vh;
    }
    .empty-cat {
      text-align: center;
      padding: 70px 20px;
    }
    .empty-cat .empty-icon { font-size: 4.5rem; margin-bottom: 14px; }
    .empty-cat h3 { font-family:'Fredoka One',cursive; font-size:1.8rem; color:var(--text); margin-bottom:8px; }
    .empty-cat p  { color:var(--text-light); margin-bottom:24px; }
  </style>
</head>
<body>
  <?php include 'views/navbar.php'; ?>

  <!-- ── HERO COMPACTO ── -->
  <section class="cat-hero">
    <div class="container position-relative">
      <div class="cat-hero-pill" style="background:<?= $cfg['color'] ?>18; color:<?= $cfg['color'] ?>;">
        <i class="bi bi-grid-fill me-1"></i> Categoría
      </div>
      <div>
        <span class="cat-hero-emoji"><?= $cfg['emoji'] ?></span>
        <h1>Catálogo: <span><?= htmlspecialchars($cfg['label']) ?></span></h1>
      </div>
      <p><?= htmlspecialchars($cfg['desc']) ?></p>
    </div>
  </section>

  <!-- ── NAVEGACIÓN RÁPIDA DE CATEGORÍAS ── -->
  <div class="category-pills-bar">
    <div class="container">
      <div class="pills-scroll">
        <?php foreach ($categoriasConfig as $cKey => $cItem): ?>
          <a href="categoria.php?c=<?= $cKey ?>" class="pill-item <?= $slug === $cKey ? 'active' : '' ?>">
            <span><?= $cItem['emoji'] ?></span>
            <span><?= htmlspecialchars($cItem['label']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- ── PRODUCTOS ── -->
  <section class="cat-products-section">
    <div class="container">
      <?php if (isset($mostrandoFallback) && $mostrandoFallback): ?>
        <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3">
          <i class="bi bi-info-circle-fill fs-3 text-info"></i>
          <div>
            <strong>Catálogo General:</strong> No encontramos productos asignados específicamente a <em>"<?= htmlspecialchars($cfg['label']) ?>"</em>, pero aquí tienes todos los juguetes disponibles en nuestra tienda.
          </div>
        </div>
      <?php endif; ?>

      <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 style="font-family:'Fredoka One',cursive; font-size:1.5rem; color:var(--text); margin:0;">
          <?= count($productos) ?> Juguete<?= count($productos) !== 1 ? 's' : '' ?> Disponible<?= count($productos) !== 1 ? 's' : '' ?>
        </h2>
        <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill shadow-sm">
          Filtro: <?= htmlspecialchars($cfg['label']) ?>
        </span>
      </div>

      <?php if (!empty($productos)): ?>
        <div class="row g-4">
          <?php foreach ($productos as $producto): ?>
            <div class="col-6 col-md-4 col-lg-3">
              <div class="product-card"
                id="product-<?= (int)$producto['id'] ?>"
                data-id="<?= (int)$producto['id'] ?>"
                data-name="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                data-description="<?= htmlspecialchars($producto['descripcion'], ENT_QUOTES, 'UTF-8') ?>"
                data-price="<?= (float)$producto['precio'] ?>"
                data-raw-price="<?= (float)$producto['precio'] ?>"
                data-formatted-price="$<?= number_format($producto['precio'], 0, ',', '.') ?>"
                data-stock="<?= (int)$producto['stock'] ?>"
                data-categoria="<?= htmlspecialchars($producto['categoria'], ENT_QUOTES, 'UTF-8') ?>"
                data-image="<?= htmlspecialchars($producto['vistas']['frente'] ?? 'Juguetes/default.png', ENT_QUOTES, 'UTF-8') ?>"
                data-views='<?= htmlspecialchars(json_encode($producto['vistas'], JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>'
                role="button" tabindex="0">
                <div class="product-image">
                  <span class="product-badge new">Nuevo</span>
                  <button class="product-wishlist" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
                  <img
                    src="<?= htmlspecialchars($producto['vistas']['frente'] ?? 'Juguetes/default.png', ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                    class="product-visual"
                    data-name="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                    data-views='<?= htmlspecialchars(json_encode($producto['vistas'], JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>'>
                  <div class="product-card-hint">Toca para ver detalles</div>
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
        </div>

      <?php else: ?>
        <div class="empty-cat">
          <div class="empty-icon"><?= $cfg['emoji'] ?></div>
          <h3>Sin productos en el catálogo</h3>
          <p>No se encontraron productos registrados en este momento.<br>¡Vuelve pronto para ver nuestras novedades!</p>
          <a href="index.php" class="btn-primary-custom">
            <i class="bi bi-house-fill me-1"></i> Ir al Inicio
          </a>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ── FOOTER BAR ── -->
  <div class="footer-bar">
    <div class="row row-cols-2 row-cols-lg-4 g-3">
      <div class="col"><div class="footer-item"><div class="footer-icon purple"><i class="bi bi-truck"></i></div><div class="footer-text"><strong>Envíos a todo el país</strong><span>Rápidos y seguros</span></div></div></div>
      <div class="col"><div class="footer-item"><div class="footer-icon pink"><i class="bi bi-tags-fill"></i></div><div class="footer-text"><strong>Las mejores marcas</strong><span>Calidad garantizada</span></div></div></div>
      <div class="col"><div class="footer-item"><div class="footer-icon green"><i class="bi bi-shield-check"></i></div><div class="footer-text"><strong>Compra segura</strong><span>Tus datos protegidos</span></div></div></div>
      <div class="col"><div class="footer-item"><div class="footer-icon blue"><i class="bi bi-headset"></i></div><div class="footer-text"><strong>Atención al cliente</strong><span>Estamos para ayudarte</span></div></div></div>
    </div>
  </div>

  <!-- ── MODAL PRODUCTO ── -->
  <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content product-modal-content">
        <button type="button" class="btn-close product-modal-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        <div class="row g-0">
          <div class="col-lg-6">
            <div class="product-modal-image-wrap">
              <img id="modalProductImage" src="" alt="" class="product-modal-image">
              <div class="modal-nav-arrows" role="group" aria-label="Cambiar vista">
                <button class="modal-arrow modal-arrow-left" id="modalArrowLeft" aria-label="Vista anterior"><i class="bi bi-chevron-left"></i></button>
                <button class="modal-arrow modal-arrow-right" id="modalArrowRight" aria-label="Vista siguiente"><i class="bi bi-chevron-right"></i></button>
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
              <button type="button" class="btn-primary-custom w-100 justify-content-center mt-3 py-3" id="modalBtnAddToCart" style="border-radius: 14px; font-size: 1rem; font-weight: 800;">
                <i class="bi bi-cart-plus-fill fs-5 me-2"></i>
                Agregar al Carrito
              </button>
              <p class="product-modal-help mt-3 mb-0">Haz clic en las flechas para cambiar entre las vistas del producto.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/cart.js"></script>
  <script src="assets/js/script.js"></script>
</body>
</html>
