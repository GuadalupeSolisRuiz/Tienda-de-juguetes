<?php
session_start();

// Permitir acceso solo a Administradores y Editores
$userRol = strtolower($_SESSION['usuario_rol'] ?? '');
if (!isset($_SESSION['usuario_id']) || !in_array($userRol, ['administrador', 'admin', 'editor'])) {
    header('Location: index.php');
    exit;
}

$esAdmin = in_array($userRol, ['administrador', 'admin']);
$esEditor = ($userRol === 'editor');

include 'include/conect.php';

/**
 * Convierte una imagen subida a formato WebP y la guarda en el destino indicado.
 * Requiere la extensión GD de PHP con soporte WebP.
 *
 * @param string $tmp_name   Ruta temporal del archivo subido.
 * @param string $destino    Ruta completa donde se guardará el archivo .webp.
 * @param int    $calidad    Calidad WebP (0-100). Por defecto 82.
 * @param string &$errorMsg  Mensaje de error en caso de fallo.
 * @return bool              true si la conversión y guardado fueron exitosos.
 */
function convertirAWebP(string $tmp_name, string $destino, int $calidad = 82, string &$errorMsg = ''): bool {
    if (!function_exists('imagewebp')) {
        $errorMsg = 'La función imagewebp no está disponible. Verifica que GD tenga soporte WebP.';
        return false;
    }

    $info = @getimagesize($tmp_name);
    if (!$info) {
        $errorMsg = 'No se pudo leer la imagen subida.';
        return false;
    }

    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $img = @imagecreatefromjpeg($tmp_name);
            break;
        case 'image/png':
            $img = @imagecreatefrompng($tmp_name);
            break;
        case 'image/gif':
            $img = @imagecreatefromgif($tmp_name);
            break;
        case 'image/webp':
            $img = @imagecreatefromwebp($tmp_name);
            break;
        default:
            $errorMsg = "Tipo de imagen no soportado: {$mime}. Usa JPG, PNG, GIF o WebP.";
            return false;
    }

    if (!$img) {
        $errorMsg = "No se pudo cargar la imagen (MIME: {$mime}). Archivo corrupto o formato inválido.";
        return false;
    }

    // Preservar transparencia para PNG y GIF
    if ($mime === 'image/png' || $mime === 'image/gif') {
        imagepalettetotruecolor($img);
        imagealphablending($img, false);
        imagesavealpha($img, true);
    }

    // Asegurarse de que el directorio destino exista y tenga permisos
    $dir = dirname($destino);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    if (!is_writable($dir)) {
        imagedestroy($img);
        $errorMsg = "El directorio destino no tiene permisos de escritura: {$dir}";
        return false;
    }

    error_clear_last();
    $resultado = @imagewebp($img, $destino, $calidad);
    imagedestroy($img);

    if (!$resultado) {
        $lastError = error_get_last();
        $errorMsg  = 'imagewebp() falló' . ($lastError ? ': ' . $lastError['message'] : '.');
        return false;
    }

    return true;
}

$mensaje = '';
$tipoMensaje = '';

// --- PROCESAR CREACIÓN DE PRODUCTO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'crear_producto') {
    $nombre      = trim($_POST['nombre_producto'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio      = floatval($_POST['precio'] ?? 0);
    $stock       = intval($_POST['stock'] ?? 0);
    $categoria   = intval($_POST['id_categoria'] ?? 0);

    if ($nombre === '' || $descripcion === '' || $categoria <= 0) {
        $mensaje = 'Todos los campos son obligatorios. Por favor completa la información del producto.';
        $tipoMensaje = 'danger';
    } elseif ($precio <= 0) {
        $mensaje = 'El precio del producto no puede ser un número negativo ni cero. Debe ser mayor a 0.';
        $tipoMensaje = 'danger';
    } elseif ($stock < 0) {
        $mensaje = 'El stock del producto no puede ser un número negativo.';
        $tipoMensaje = 'danger';
    } else {
        // Directorio de subida
        $target_dir = "Juguetes/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $rutas = [];
        $vistas = ['frente', 'izquierda', 'derecha'];

        $erroresWebP = [];
        foreach ($vistas as $vista) {
            if (isset($_FILES["img_$vista"]) && $_FILES["img_$vista"]['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["img_$vista"]['tmp_name'];

                $filename = "prod_" . time() . "_{$vista}_" . rand(100, 999) . ".webp";
                $destino  = $target_dir . $filename;
                $errWebP  = '';

                if (convertirAWebP($tmp_name, $destino, 82, $errWebP)) {
                    $rutas[$vista] = "Juguetes/" . $filename;
                } else {
                    $erroresWebP[] = "Vista '{$vista}': {$errWebP}";
                    $rutas[$vista] = "Juguetes/default.png";
                }
            } else {
                $rutas[$vista] = "Juguetes/default.png";
            }
        }

        $imagenes_json = json_encode($rutas, JSON_UNESCAPED_SLASHES);

        $stmt = $conexion->prepare("INSERT INTO productos (nombre_producto, descripcion, precio, stock, imagen, id_categoria, id_disponible) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $stock, $imagenes_json, $categoria);

        if ($stmt->execute()) {
            if (!empty($erroresWebP)) {
                $mensaje = 'Producto agregado, pero algunas imágenes no se convirtieron a WebP: ' . implode('; ', $erroresWebP);
                $tipoMensaje = 'warning';
            } else {
                $mensaje = 'Producto agregado al catálogo correctamente.';
                $tipoMensaje = 'success';
            }
        } else {
            $mensaje = 'Error al insertar el producto: ' . $stmt->error;
            $tipoMensaje = 'danger';
        }
        $stmt->close();
    }
}

// --- PROCESAR EDICIÓN DE PRODUCTO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar_producto') {
    $idProducto  = (int)($_POST['id_producto'] ?? 0);
    $nombre      = trim($_POST['nombre_producto'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio      = floatval($_POST['precio'] ?? 0);
    $stock       = intval($_POST['stock'] ?? 0);
    $categoria   = intval($_POST['id_categoria'] ?? 0);

    if ($idProducto <= 0 || $nombre === '' || $descripcion === '' || $categoria <= 0) {
        $mensaje = 'Todos los campos son obligatorios al editar un producto.';
        $tipoMensaje = 'danger';
    } elseif ($precio <= 0) {
        $mensaje = 'El precio del producto no puede ser un número negativo ni cero. Debe ser mayor a 0.';
        $tipoMensaje = 'danger';
    } elseif ($stock < 0) {
        $mensaje = 'El stock del producto no puede ser un número negativo.';
        $tipoMensaje = 'danger';
    } else {
        // Obtener imágenes actuales
        $stmtImg = $conexion->prepare("SELECT imagen FROM productos WHERE id_productos = ?");
        $stmtImg->bind_param("i", $idProducto);
        $stmtImg->execute();
        $resImg = $stmtImg->get_result()->fetch_assoc();
        $vistas_actuales = json_decode($resImg['imagen'] ?? '{}', true) ?: [
            "frente" => "Juguetes/default.png",
            "izquierda" => "Juguetes/default.png",
            "derecha" => "Juguetes/default.png"
        ];
        $stmtImg->close();

        $target_dir = "Juguetes/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $vistas_nuevas = $vistas_actuales;
        $vistas = ['frente', 'izquierda', 'derecha'];

        $erroresWebP = [];
        foreach ($vistas as $vista) {
            if (isset($_FILES["edit_img_$vista"]) && $_FILES["edit_img_$vista"]['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["edit_img_$vista"]['tmp_name'];

                $filename = "prod_" . $idProducto . "_{$vista}_" . time() . ".webp";
                $destino  = $target_dir . $filename;
                $errWebP  = '';

                if (convertirAWebP($tmp_name, $destino, 82, $errWebP)) {
                    $vistas_nuevas[$vista] = "Juguetes/" . $filename;
                } else {
                    $erroresWebP[] = "Vista '{$vista}': {$errWebP}";
                }
            }
        }

        $imagenes_json = json_encode($vistas_nuevas, JSON_UNESCAPED_SLASHES);

        $stmt = $conexion->prepare("UPDATE productos SET nombre_producto = ?, descripcion = ?, precio = ?, stock = ?, imagen = ?, id_categoria = ? WHERE id_productos = ?");
        $stmt->bind_param("ssdisii", $nombre, $descripcion, $precio, $stock, $imagenes_json, $categoria, $idProducto);

        if ($stmt->execute()) {
            $mensaje = 'Producto actualizado correctamente.';
            $tipoMensaje = 'success';
        } else {
            $mensaje = 'Error al actualizar el producto: ' . $stmt->error;
            $tipoMensaje = 'danger';
        }
        $stmt->close();
    }
}

// --- PROCESAR ELIMINACIÓN DE PRODUCTO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'eliminar_producto') {
    $idProducto = (int)($_POST['id_producto'] ?? 0);
    if ($idProducto > 0) {
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id_productos = ?");
        $stmt->bind_param("i", $idProducto);
        if ($stmt->execute()) {
            $mensaje = 'Producto eliminado con éxito.';
            $tipoMensaje = 'success';
        } else {
            $mensaje = 'No se pudo eliminar el producto.';
            $tipoMensaje = 'danger';
        }
        $stmt->close();
    }
}

// --- OBTENER PRODUCTOS DEL CATÁLOGO ---
$queryProductos = "SELECT p.*, c.nombre_categoria 
                  FROM productos p 
                  LEFT JOIN categoria c ON p.id_categoria = c.id_categoria 
                  ORDER BY p.id_productos DESC";
$resProductos = $conexion->query($queryProductos);
$productos = $resProductos ? $resProductos->fetch_all(MYSQLI_ASSOC) : [];

// --- OBTENER CATEGORÍAS DISPONIBLES ---
$resCategorias = $conexion->query("SELECT * FROM categoria");
$categorias = $resCategorias ? $resCategorias->fetch_all(MYSQLI_ASSOC) : [];

// --- LÓGICA EXISTENTE DE USUARIOS ---
$rolFiltro = $_GET['rol'] ?? '';
$fechaFiltro = $_GET['fecha'] ?? '';
$hayFiltro = ($rolFiltro !== '' || $fechaFiltro !== '');
$mensajeFiltro = '';
$mensajeResultados = '';
$textoVista = 'Todos los usuarios';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'], $_POST['id_rol'])) {
    $idUsuario = (int)$_POST['id_usuario'];
    $idRol = (int)$_POST['id_rol'];

    if ($idUsuario > 0 && in_array($idRol, [1, 2, 3], true)) {
        // Consultar el rol actual del usuario antes de actualizar
        $stmtCheck = $conexion->prepare('SELECT id_rol FROM usuarios WHERE id_usuario = ?');
        $stmtCheck->bind_param('i', $idUsuario);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result()->fetch_assoc();
        $stmtCheck->close();

        $rolActualId = (int)($resCheck['id_rol'] ?? 0);

        // Restricción: No se permite promover un Cliente (1) a Administrador (3)
        if ($rolActualId === 1 && $idRol === 3) {
            $mensaje = 'No está permitido cambiar a un usuario con rol Cliente directamente a Administrador.';
            $tipoMensaje = 'warning';
        } else {
            $stmt = $conexion->prepare('UPDATE usuarios SET id_rol = ? WHERE id_usuario = ?');
            $stmt->bind_param('ii', $idRol, $idUsuario);
            if ($stmt->execute()) {
                $mensaje = 'Rol actualizado correctamente.';
                $tipoMensaje = 'success';
            } else {
                $mensaje = 'No se pudo actualizar el rol.';
                $tipoMensaje = 'danger';
            }
            $stmt->close();
        }
    }
}

$sql = 'SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.telefono, u.fecha_registro, u.id_rol, r.nombre_rol
        FROM usuarios u
        INNER JOIN rol r ON u.id_rol = r.id_rol WHERE 1=1';
$params = [];
$types = '';

if ($rolFiltro !== '') {
    $sql .= ' AND u.id_rol = ?';
    $params[] = (int)$rolFiltro;
    $types .= 'i';
}
if ($fechaFiltro !== '') {
    $sql .= ' AND u.fecha_registro = ?';
    $params[] = $fechaFiltro;
    $types .= 's';
}

$sql .= ' ORDER BY u.id_usuario ASC';
$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();
$usuarios = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestión de Catálogo y Usuarios - Toys NOVA</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="assets/css/style.css" rel="stylesheet" />
  <style>
    .img-thumb { width: 45px; height: 45px; object-fit: cover; border-radius: 8px; border: 1px solid #dee2e6; }
    .nav-tabs .nav-link.active { font-weight: 700; color: #7c4dff; border-bottom: 3px solid #7c4dff; }
  </style>
</head>
<body>
  <?php include 'views/navbar.php'; ?>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-11">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-body p-4 p-md-5">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
              <div>
                <h2 class="fw-bold mb-1">Panel de Gestión</h2>
                <p class="text-muted mb-0">
                  <?php echo $esAdmin ? 'Administra los productos del catálogo y los usuarios del sistema.' : 'Administra los productos del catálogo de juguetes.'; ?>
                </p>
              </div>
              <a href="index.php" class="btn btn-outline-secondary rounded-3">
                <i class="bi bi-arrow-left"></i> Volver al inicio
              </a>
            </div>

            <?php if ($mensaje !== ''): ?>
              <div class="alert alert-<?php echo htmlspecialchars($tipoMensaje); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <!-- PESTAÑAS DE NAVEGACIÓN -->
            <ul class="nav nav-tabs mb-4" id="gestionTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active fs-5" id="productos-tab" data-bs-toggle="tab" data-bs-target="#productos-pane" type="button" role="tab"><i class="bi bi-box-seam me-2"></i>Catálogo de Productos</button>
              </li>
              <?php if ($esAdmin): ?>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fs-5" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios-pane" type="button" role="tab"><i class="bi bi-people me-2"></i>Gestión de Usuarios</button>
                </li>
              <?php endif; ?>
              <li class="nav-item" role="presentation">
                <button class="nav-link fs-5" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets-pane" type="button" role="tab" onclick="loadAdminAllTickets()"><i class="bi bi-receipt me-2"></i>Gestión de Tickets</button>
              </li>
            </ul>

            <div class="tab-content" id="gestionTabsContent">
              
              <!-- ================= PESTAÑA 1: PRODUCTOS ================= -->
              <div class="tab-pane fade show active" id="productos-pane" role="tabpanel">
                
                <!-- FORMULARIO NUEVO PRODUCTO -->
                <div class="border rounded-4 p-4 bg-light mb-4">
                  <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle-fill me-2 text-primary"></i>Agregar Nuevo Producto al Catálogo</h5>
                  <form action="gestion.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="crear_producto">
                    
                    <div class="row g-3">
                      <div class="col-md-5">
                        <label class="form-label fw-semibold">Nombre del producto</label>
                        <input type="text" name="nombre_producto" class="form-control" placeholder="Ej: Oso de peluche" required>
                      </div>

                      <div class="col-md-3">
                        <label class="form-label fw-semibold">Categoría</label>
                        <select name="id_categoria" class="form-select" required>
                          <?php if(!empty($categorias)): ?>
                            <?php foreach($categorias as $cat): ?>
                              <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nombre_categoria']); ?></option>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <option value="1">Niña</option>
                            <option value="2">Niño</option>
                            <option value="3">Bebé</option>
                          <?php endif; ?>
                        </select>
                      </div>

                      <div class="col-md-2">
                        <label class="form-label fw-semibold">Precio ($)</label>
                        <input type="number" step="0.01" min="0.01" name="precio" class="form-control" placeholder="250.00" required>
                      </div>

                      <div class="col-md-2">
                        <label class="form-label fw-semibold">Stock</label>
                        <input type="number" min="0" name="stock" class="form-control" placeholder="10" required>
                      </div>

                      <div class="col-12">
                        <label class="form-label fw-semibold">Descripción del producto</label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Detalles, dimensiones o material del juguete..." required>
                      </div>

                      <!-- CARGA DE IMÁGENES -->
                      <div class="col-12 mt-3">
                        <label class="form-label fw-bold text-secondary">Vistas de Imagen (para el Slider del Modal)</label>
                        <div class="row g-3">
                          <div class="col-md-4">
                            <label class="form-label small">Vista Frontal</label>
                            <input type="file" name="img_frente" class="form-control form-control-sm" accept="image/*" required>
                          </div>
                          <div class="col-md-4">
                            <label class="form-label small">Vista Izquierda</label>
                            <input type="file" name="img_izquierda" class="form-control form-control-sm" accept="image/*" required>
                          </div>
                          <div class="col-md-4">
                            <label class="form-label small">Vista Derecha</label>
                            <input type="file" name="img_derecha" class="form-control form-control-sm" accept="image/*" required>
                          </div>
                        </div>
                      </div>

                      <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Producto</button>
                      </div>
                    </div>
                  </form>
                </div>

                <!-- TABLA DE PRODUCTOS -->
                <div class="table-responsive">
                  <h5 class="fw-bold mb-3">Productos Existentes</h5>
                  <table class="table align-middle table-hover border">
                    <thead class="table-light">
                      <tr>
                        <th>Vistas (F / I / D)</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th class="text-end">Acción</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($productos)): ?>
                        <?php foreach ($productos as $prod): 
                          $imgs = json_decode($prod['imagen'], true);
                          $frente = $imgs['frente'] ?? 'Juguetes/default.png';
                          $izq    = $imgs['izquierda'] ?? 'Juguetes/default.png';
                          $der    = $imgs['derecha'] ?? 'Juguetes/default.png';
                        ?>
                          <tr>
                            <td>
                              <div class="d-flex gap-1">
                                <img src="<?php echo htmlspecialchars($frente); ?>" class="img-thumb" title="Frontal">
                                <img src="<?php echo htmlspecialchars($izq); ?>" class="img-thumb" title="Izquierda">
                                <img src="<?php echo htmlspecialchars($der); ?>" class="img-thumb" title="Derecha">
                              </div>
                            </td>
                            <td>
                              <div class="fw-bold"><?php echo htmlspecialchars($prod['nombre_producto']); ?></div>
                              <small class="text-muted"><?php echo htmlspecialchars($prod['descripcion']); ?></small>
                            </td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($prod['nombre_categoria'] ?? 'Sin Categoria'); ?></span></td>
                            <td class="fw-bold text-success">$<?php echo number_format($prod['precio'], 2); ?></td>
                            <td><?php echo (int)$prod['stock']; ?> pcs</td>
                            <td class="text-end">
                              <!-- BOTÓN EDITAR -->
                              <button type="button" class="btn btn-sm btn-outline-primary btn-editar-prod me-1"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditarProducto"
                                data-id="<?php echo $prod['id_productos']; ?>"
                                data-nombre="<?php echo htmlspecialchars($prod['nombre_producto'], ENT_QUOTES); ?>"
                                data-descripcion="<?php echo htmlspecialchars($prod['descripcion'], ENT_QUOTES); ?>"
                                data-precio="<?php echo $prod['precio']; ?>"
                                data-stock="<?php echo $prod['stock']; ?>"
                                data-categoria="<?php echo $prod['id_categoria']; ?>"
                                data-vistas='<?php echo htmlspecialchars(json_encode($imgs ?: []), ENT_QUOTES); ?>'>
                                <i class="bi bi-pencil"></i>
                              </button>

                              <!-- BOTÓN ELIMINAR -->
                              <form method="POST" action="gestion.php" onsubmit="return confirm('¿Deseas eliminar este producto?');" style="display:inline;">
                                <input type="hidden" name="action" value="eliminar_producto">
                                <input type="hidden" name="id_producto" value="<?php echo $prod['id_productos']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                  <i class="bi bi-trash"></i>
                                </button>
                              </form>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr><td colspan="6" class="text-center py-3 text-muted">No hay productos registrados en el catálogo.</td></tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

              </div>

              <?php if ($esAdmin): ?>
              <!-- ================= PESTAÑA 2: USUARIOS ================= -->
              <div class="tab-pane fade" id="usuarios-pane" role="tabpanel">
                
                <div class="row g-3 mb-4">
                  <div class="col-12 col-lg-4">
                    <div class="border rounded-4 p-3 bg-light">
                      <h5 class="fw-bold mb-3"><i class="bi bi-person-plus-fill me-2"></i>Crear usuario</h5>
                      <form id="createUserForm" novalidate>
                        <div class="mb-2">
                          <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Nombre" required>
                        </div>
                        <div class="mb-2">
                          <input type="text" name="apellido" class="form-control form-control-sm" placeholder="Apellido" required>
                        </div>
                        <div class="mb-2">
                          <input type="email" name="correo" class="form-control form-control-sm" placeholder="Correo" required>
                        </div>
                        <div class="mb-2">
                          <input type="tel" name="telefono" class="form-control form-control-sm" placeholder="Teléfono">
                        </div>
                        <div class="mb-2">
                          <input type="password" name="contrasena" class="form-control form-control-sm" placeholder="Contraseña" required>
                        </div>
                        <div class="mb-2">
                          <select name="sexo" class="form-select form-select-sm">
                            <option value="">Sexo</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                          </select>
                        </div>
                        <div class="mb-3">
                          <select name="id_rol" class="form-select form-select-sm">
                            <option value="1">Cliente</option>
                            <option value="2">Editor</option>
                            <option value="3">Administrador</option>
                          </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100">Crear usuario</button>
                      </form>
                    </div>
                  </div>

                  <div class="col-12 col-lg-8">
                    <div class="border rounded-4 p-3 bg-light">
                      <h5 class="fw-bold mb-3"><i class="bi bi-funnel-fill me-2"></i>Filtros</h5>
                      <form method="get" id="filterForm" class="row g-2">
                        <div class="col-12 col-md-6">
                          <select name="rol" class="form-select form-select-sm">
                            <option value="">Todos los roles</option>
                            <option value="1" <?php echo $rolFiltro === '1' ? 'selected' : ''; ?>>Cliente</option>
                            <option value="2" <?php echo $rolFiltro === '2' ? 'selected' : ''; ?>>Editor</option>
                            <option value="3" <?php echo $rolFiltro === '3' ? 'selected' : ''; ?>>Administrador</option>
                          </select>
                        </div>
                        <div class="col-12 col-md-6">
                          <input type="date" name="fecha" class="form-control form-control-sm" value="<?php echo htmlspecialchars($fechaFiltro); ?>">
                        </div>
                        <div class="col-12 text-end">
                          <button class="btn btn-sm btn-outline-secondary" type="submit">Aplicar</button>
                          <a class="btn btn-sm btn-outline-dark" href="gestion.php#resultados">Limpiar</a>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div id="resultados" class="table-responsive">
                  <table class="table align-middle" style="min-width: 1050px;">
                    <thead>
                      <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Fecha registro</th>
                        <th style="width: 130px;">Rol actual</th>
                        <th class="text-end" style="width: 340px;">Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                          <td><?php echo (int)$usuario['id_usuario']; ?></td>
                          <td class="fw-semibold"><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></td>
                          <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                          <td><?php echo htmlspecialchars($usuario['telefono'] ?: '—'); ?></td>
                          <td><small class="text-muted"><?php echo htmlspecialchars($usuario['fecha_registro']); ?></small></td>
                          <td style="white-space: nowrap;">
                            <span class="badge text-capitalize bg-primary px-3 py-1.5" style="font-size: 0.82rem;">
                              <?php echo htmlspecialchars($usuario['nombre_rol']); ?>
                            </span>
                          </td>
                          <td class="text-end">
                            <div class="d-flex justify-content-end align-items-center gap-2 flex-nowrap">
                              <button type="button" class="btn btn-sm btn-outline-purple flex-shrink-0" style="border-color:#7C3AED; color:#7C3AED;" onclick="openAdminUserTickets(<?php echo (int)$usuario['id_usuario']; ?>, '<?php echo htmlspecialchars(addslashes($usuario['nombre'] . ' ' . $usuario['apellido'])); ?>', '<?php echo htmlspecialchars(addslashes($usuario['correo'])); ?>')">
                                <i class="bi bi-ticket-perforated-fill me-1"></i> Tickets
                              </button>
                              <form method="post" class="d-flex align-items-center gap-1 flex-shrink-0">
                                <input type="hidden" name="id_usuario" value="<?php echo (int)$usuario['id_usuario']; ?>">
                                <select name="id_rol" class="form-select form-select-sm" style="width: 130px;">
                                  <option value="1" <?php echo (int)$usuario['id_rol'] === 1 ? 'selected' : ''; ?>>Cliente</option>
                                  <option value="2" <?php echo (int)$usuario['id_rol'] === 2 ? 'selected' : ''; ?>>Editor</option>
                                  <?php if ((int)$usuario['id_rol'] !== 1): ?>
                                    <option value="3" <?php echo (int)$usuario['id_rol'] === 3 ? 'selected' : ''; ?>>Administrador</option>
                                  <?php endif; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                              </form>
                              <button type="button" class="btn btn-sm btn-outline-danger delete-user-btn flex-shrink-0" data-user-id="<?php echo (int)$usuario['id_usuario']; ?>" title="Eliminar Usuario">
                                <i class="bi bi-trash"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

              </div>
              <?php endif; ?>

              <!-- ================= PESTAÑA 3: GESTIÓN DE TICKETS ================= -->
              <div class="tab-pane fade" id="tickets-pane" role="tabpanel">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border rounded-4 p-3 bg-light">
                  <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-receipt me-2 text-purple" style="color:#7C3AED;"></i>Todos los Tickets de Compra</h5>
                    <p class="text-muted small mb-0">Visualiza, actualiza el estado o reimprime cualquier ticket del sistema.</p>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-purple" style="border-color:#7C3AED; color:#7C3AED;" onclick="loadAdminAllTickets()">
                      <i class="bi bi-arrow-clockwise me-1"></i> Actualizar Lista
                    </button>
                  </div>
                </div>

                <div id="adminAllTicketsContainer">
                  <div class="text-center py-5">
                    <div class="spinner-border text-purple" role="status" style="color:#7C3AED;"></div>
                    <p class="text-muted mt-2 small">Cargando tickets de compra...</p>
                  </div>
                </div>
              </div>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL EDITAR PRODUCTO -->
  <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-labelledby="modalEditarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-4 border-0">
        <form action="gestion.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="editar_producto">
          <input type="hidden" name="id_producto" id="edit_id_producto">

          <div class="modal-header bg-primary text-white rounded-top-4">
            <h5 class="modal-title fw-bold" id="modalEditarProductoLabel"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4">
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label fw-semibold">Nombre del producto</label>
                <input type="text" name="nombre_producto" id="edit_nombre_producto" class="form-control" required>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-semibold">Categoría</label>
                <select name="id_categoria" id="edit_id_categoria" class="form-select" required>
                  <?php if(!empty($categorias)): ?>
                    <?php foreach($categorias as $cat): ?>
                      <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nombre_categoria']); ?></option>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <option value="1">Niña</option>
                    <option value="2">Niño</option>
                    <option value="3">Bebé</option>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Precio ($)</label>
                <input type="number" step="0.01" min="0.01" name="precio" id="edit_precio" class="form-control" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Stock</label>
                <input type="number" min="0" name="stock" id="edit_stock" class="form-control" required>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Descripción del producto</label>
                <input type="text" name="descripcion" id="edit_descripcion" class="form-control" required>
              </div>

              <!-- VISTAS DE IMÁGENES EN MODAL -->
              <div class="col-12 mt-3">
                <label class="form-label fw-bold text-secondary">Cambiar Imágenes (Opcional)</label>
                <p class="small text-muted mb-2">Si no eliges un archivo nuevo, se mantendrá la imagen actual.</p>
                <div class="row g-3 text-center">
                  <div class="col-md-4">
                    <label class="form-label small fw-semibold">Vista Frontal</label>
                    <img id="edit_prev_frente" src="" class="img-thumb d-block mx-auto mb-2" style="width: 60px; height: 60px;">
                    <input type="file" name="edit_img_frente" class="form-control form-control-sm" accept="image/*">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label small fw-semibold">Vista Izquierda</label>
                    <img id="edit_prev_izquierda" src="" class="img-thumb d-block mx-auto mb-2" style="width: 60px; height: 60px;">
                    <input type="file" name="edit_img_izquierda" class="form-control form-control-sm" accept="image/*">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label small fw-semibold">Vista Derecha</label>
                    <img id="edit_prev_derecha" src="" class="img-thumb d-block mx-auto mb-2" style="width: 60px; height: 60px;">
                    <input type="file" name="edit_img_derecha" class="form-control form-control-sm" accept="image/*">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary fw-bold">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      
      // POBLAR MODAL EDITAR PRODUCTO
      const editButtons = document.querySelectorAll('.btn-editar-prod');
      editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
          const id = this.dataset.id;
          const nombre = this.dataset.nombre;
          const descripcion = this.dataset.descripcion;
          const precio = this.dataset.precio;
          const stock = this.dataset.stock;
          const categoria = this.dataset.categoria;
          const vistas = JSON.parse(this.dataset.vistas || '{}');

          document.getElementById('edit_id_producto').value = id;
          document.getElementById('edit_nombre_producto').value = nombre;
          document.getElementById('edit_descripcion').value = descripcion;
          document.getElementById('edit_precio').value = precio;
          document.getElementById('edit_stock').value = stock;
          document.getElementById('edit_id_categoria').value = categoria;

          document.getElementById('edit_prev_frente').src = vistas.frente || 'Juguetes/default.png';
          document.getElementById('edit_prev_izquierda').src = vistas.izquierda || 'Juguetes/default.png';
          document.getElementById('edit_prev_derecha').src = vistas.derecha || 'Juguetes/default.png';
        });
      });

      // SCRIPT DE USUARIOS EXISTENTE
      const createForm = document.getElementById('createUserForm');
      if (createForm) {
        createForm.addEventListener('submit', async function (event) {
          event.preventDefault();
          const formData = new FormData(createForm);
          formData.append('action', 'create');

          const btn = createForm.querySelector('button[type="submit"]');
          const originalText = btn.innerHTML;
          btn.disabled = true;
          btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando...';

          try {
            const response = await fetch('include/admin_user_management.php', {
              method: 'POST',
              body: formData
            });
            const data = await response.json();
            alert(data.message);
            if (data.success) {
              window.location.reload();
            }
          } catch (error) {
            alert('No se pudo completar la acción.');
          } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
          }
        });
      }

      document.querySelectorAll('.delete-user-btn').forEach(function (btn) {
        btn.addEventListener('click', async function () {
          const userId = this.dataset.userId;
          const confirmed = confirm('¿Seguro que deseas eliminar este usuario?');
          if (!confirmed) return;

          const formData = new FormData();
          formData.append('action', 'delete');
          formData.append('id_usuario', userId);

          try {
            const response = await fetch('include/admin_user_management.php', {
              method: 'POST',
              body: formData
            });
            const data = await response.json();
            alert(data.message);
            if (data.success) {
              window.location.reload();
            }
          } catch (error) {
            alert('No se pudo eliminar el usuario.');
          }
        });
      });
    });
  </script>

  <!-- MODAL GESTIONAR TICKETS DE USUARIO -->
  <div class="modal fade" id="adminUserTicketsModal" tabindex="-1" aria-labelledby="adminUserTicketsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-header text-white rounded-top-4 py-3 px-4" style="background:#7C3AED;">
          <div class="d-flex align-items-center gap-2">
            <div style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;">
              <i class="bi bi-ticket-perforated-fill fs-5"></i>
            </div>
            <div>
              <h5 class="modal-title font-fredoka fw-bold mb-0" id="adminUserTicketsModalLabel">Tickets de Compra del Usuario</h5>
              <small class="text-white-50" id="adminUserTicketsModalSubtitle">Historial de compras</small>
            </div>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4" style="min-height: 250px; max-height: 550px; overflow-y: auto;">
          <div id="adminUserTicketsContainer">
            <div class="text-center py-5">
              <div class="spinner-border text-purple" role="status" style="color:#7C3AED;"></div>
              <p class="text-muted mt-2 small">Cargando tickets...</p>
            </div>
          </div>
        </div>

        <div class="modal-footer border-0 bg-light py-3 px-4 rounded-bottom-4">
          <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    async function openAdminUserTickets(userId, userName, userEmail) {
      const modalEl = document.getElementById('adminUserTicketsModal');
      const titleEl = document.getElementById('adminUserTicketsModalLabel');
      const subEl = document.getElementById('adminUserTicketsModalSubtitle');
      const container = document.getElementById('adminUserTicketsContainer');

      if (titleEl) titleEl.textContent = `Tickets de Compra de ${userName}`;
      if (subEl) subEl.textContent = `Correo: ${userEmail || '—'} | ID Usuario: #${userId}`;
      if (container) {
        container.innerHTML = `
          <div class="text-center py-5">
            <div class="spinner-border text-purple" role="status" style="color:#7C3AED;"></div>
            <p class="text-muted mt-2 small">Cargando tickets de ${userName}...</p>
          </div>`;
      }

      const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
      bsModal.show();

      try {
        const resp = await fetch(`include/obtener_pedidos_admin.php?id_usuario=${userId}`);
        const data = await resp.json();

        if (!data.success || !data.pedidos || data.pedidos.length === 0) {
          container.innerHTML = `
            <div class="text-center py-5">
              <div class="display-3 mb-2">🧾</div>
              <h5 class="fw-bold text-dark mb-1">Sin tickets registrados</h5>
              <p class="text-muted small">Este usuario aún no ha realizado ninguna compra.</p>
            </div>`;
          return;
        }

        renderTicketsList(data.pedidos, container, true);
      } catch (e) {
        console.error('Error al cargar tickets:', e);
        container.innerHTML = `<div class="alert alert-danger">Error al conectar con el servidor.</div>`;
      }
    }

    async function loadAdminAllTickets() {
      const container = document.getElementById('adminAllTicketsContainer');
      if (!container) return;

      container.innerHTML = `
        <div class="text-center py-5">
          <div class="spinner-border text-purple" role="status" style="color:#7C3AED;"></div>
          <p class="text-muted mt-2 small">Cargando lista global de tickets...</p>
        </div>`;

      try {
        const resp = await fetch('include/obtener_pedidos_admin.php');
        const data = await resp.json();

        if (!data.success || !data.pedidos || data.pedidos.length === 0) {
          container.innerHTML = `
            <div class="text-center py-5">
              <div class="display-3 mb-2">🧾</div>
              <h5 class="fw-bold text-dark mb-1">No hay tickets registrados en el sistema</h5>
              <p class="text-muted small">Los tickets aparecerán aquí cuando los clientes realicen compras.</p>
            </div>`;
          return;
        }

        renderTicketsList(data.pedidos, container, false);
      } catch (e) {
        console.error('Error al cargar todos los tickets:', e);
        container.innerHTML = `<div class="alert alert-danger">Error al cargar tickets globales.</div>`;
      }
    }

    function renderTicketsList(pedidos, container, isSingleUser = false) {
      let html = `<div class="d-flex flex-column gap-3">`;

      pedidos.forEach(order => {
        let statusClass = 'bg-success';
        if (order.estado === 'Pendiente') statusClass = 'bg-warning text-dark';
        if (order.estado === 'Cancelado') statusClass = 'bg-danger';

        let itemsHtml = order.items.map(item => `
          <div class="d-flex align-items-center gap-2 py-1">
            <img src="${item.imagen}" alt="${item.nombre}" style="width:32px;height:32px;object-fit:cover;border-radius:6px;" onError="this.src='Juguetes/default.png'">
            <div class="flex-grow-1">
              <span class="fw-semibold text-dark small">${item.nombre}</span>
              <small class="text-muted d-block" style="font-size:0.75rem;">x${item.cantidad} — $${item.precio_unitario.toFixed(2)} c/u</small>
            </div>
            <strong class="small text-dark">$${item.subtotal.toFixed(2)}</strong>
          </div>
        `).join('');

        html += `
          <div class="card border rounded-4 shadow-sm" id="adminOrderCard-${order.id_pedido}">
            <div class="card-header bg-white py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 border-bottom">
              <div class="d-flex align-items-center gap-3">
                <span class="badge px-3 py-2 fs-6 fw-bold" style="background:#7C3AED; color:#fff;">${order.folio}</span>
                <div>
                  <small class="text-muted d-block" style="font-size:0.78rem;"><i class="bi bi-calendar3 me-1"></i>${order.fecha}</small>
                  ${!isSingleUser ? `<strong class="text-dark small"><i class="bi bi-person me-1"></i>${order.cliente_nombre} (${order.cliente_correo})</strong>` : ''}
                </div>
              </div>

              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge ${statusClass} px-3 py-1.5 rounded-pill" id="orderStatusBadge-${order.id_pedido}">${order.estado}</span>
                <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill"><i class="bi bi-credit-card me-1"></i>${order.metodo_pago}</span>
                <strong class="fs-5 ms-2" style="color:#7C3AED;">$${order.total.toFixed(2)}</strong>
              </div>
            </div>

            <div class="card-body p-4">
              <div class="row g-3">
                <div class="col-lg-7">
                  <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-bag-check me-1"></i> Detalle del Pedido</h6>
                  <div class="p-3 bg-light rounded-3 border" style="max-height:180px; overflow-y:auto;">
                    ${itemsHtml}
                  </div>
                </div>

                <div class="col-lg-5 d-flex flex-column justify-content-between">
                  <div>
                    <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-gear me-1"></i> Gestión de Estado</h6>
                    <div class="input-group input-group-sm mb-3">
                      <label class="input-group-text bg-white small">Estado</label>
                      <select class="form-select form-select-sm" id="selectStatus-${order.id_pedido}">
                        <option value="Completado" ${order.estado === 'Completado' ? 'selected' : ''}>Completado</option>
                        <option value="Pendiente" ${order.estado === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
                        <option value="Cancelado" ${order.estado === 'Cancelado' ? 'selected' : ''}>Cancelado</option>
                      </select>
                      <button class="btn text-white btn-sm" style="background:#7C3AED;" onclick="updateOrderStatusAdmin(${order.id_pedido})">
                        Guardar
                      </button>
                    </div>
                  </div>

                  <div class="d-flex align-items-center gap-2 pt-2 border-top">
                    <button type="button" class="btn btn-sm btn-outline-purple flex-grow-1" style="border-color:#7C3AED; color:#7C3AED;" onclick="if(window.viewOrderTicket) window.viewOrderTicket(${order.id_pedido});">
                      <i class="bi bi-printer-fill me-1"></i> Ver / Imprimir Ticket
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteOrderAdmin(${order.id_pedido})" title="Eliminar Ticket">
                      <i class="bi bi-trash3-fill"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>`;
      });

      html += `</div>`;
      container.innerHTML = html;
    }

    async function updateOrderStatusAdmin(orderId) {
      const select = document.getElementById(`selectStatus-${orderId}`);
      if (!select) return;

      const newStatus = select.value;
      try {
        const resp = await fetch('include/actualizar_estado_pedido.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ order_id: orderId, estado: newStatus })
        });
        const data = await resp.json();

        if (data.success) {
          const badge = document.getElementById(`orderStatusBadge-${orderId}`);
          if (badge) {
            badge.textContent = newStatus;
            badge.className = 'badge px-3 py-1.5 rounded-pill ' + 
              (newStatus === 'Completado' ? 'bg-success' : (newStatus === 'Pendiente' ? 'bg-warning text-dark' : 'bg-danger'));
          }
          alert(`✅ ${data.message}`);
        } else {
          alert(`⚠️ ${data.message}`);
        }
      } catch (e) {
        console.error('Error al actualizar estado:', e);
        alert('⚠️ Error al comunicarse con el servidor.');
      }
    }

    async function deleteOrderAdmin(orderId) {
      if (!confirm(`¿Estás seguro de que deseas eliminar el ticket #${orderId}? Esta acción no se puede deshacer.`)) {
        return;
      }

      try {
        const resp = await fetch('include/eliminar_pedido.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ order_id: orderId })
        });
        const data = await resp.json();

        if (data.success) {
          const card = document.getElementById(`adminOrderCard-${orderId}`);
          if (card) card.remove();
          alert(`✅ ${data.message}`);
        } else {
          alert(`⚠️ ${data.message}`);
        }
      } catch (e) {
        console.error('Error al eliminar ticket:', e);
        alert('⚠️ Error de conexión.');
      }
    }
  </script>
</body>
</html>