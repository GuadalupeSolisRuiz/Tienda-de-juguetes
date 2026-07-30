<?php
// include/guardar_producto.php
require_once 'conect.php'; // Usa tu archivo de conexión existente

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre      = $_POST['nombre_producto'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio      = floatval($_POST['precio'] ?? 0);
    $stock       = intval($_POST['stock'] ?? 0);
    $categoria   = intval($_POST['id_categoria'] ?? 1);

    // Directorio de destino para guardar las imágenes
    $target_dir = "../Juguetes/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $rutas = [];
    $vistas = ['frente', 'izquierda', 'derecha'];

    foreach ($vistas as $vista) {
        if (isset($_FILES["img_$vista"]) && $_FILES["img_$vista"]['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES["img_$vista"]['tmp_name'];
            $filename = "prod_" . time() . "_{$vista}.webp";
            $destino = $target_dir . $filename;

            $imagenConvertida = false;

            if (function_exists('imagewebp')) {
                $info = getimagesize($tmp_name);

                if ($info !== false) {
                    switch ($info[2]) {
                        case IMAGETYPE_JPEG:
                            $imagen = imagecreatefromjpeg($tmp_name);
                            break;
                        case IMAGETYPE_PNG:
                            $imagen = imagecreatefrompng($tmp_name);
                            break;
                        case IMAGETYPE_WEBP:
                            $imagen = imagecreatefromwebp($tmp_name);
                            break;
                        default:
                            $imagen = null;
                            break;
                    }

                    if ($imagen !== null) {
                        if (imagewebp($imagen, $destino, 80)) {
                            $imagenConvertida = true;
                        }
                        imagedestroy($imagen);
                    }
                }
            }

            if ($imagenConvertida) {
                $rutas[$vista] = "Juguetes/" . $filename;
            } else {
                $rutas[$vista] = "Juguetes/default.png";
            }
        } else {
            $rutas[$vista] = "Juguetes/default.png";
        }
    }

    // Convertir a formato JSON como lo tenías estructurado
    $imagenes_json = json_encode($rutas, JSON_UNESCAPED_SLASHES);

    // Inserción en la base de datos (id_disponible = 1 por defecto)
    $stmt = $mysqli->prepare("INSERT INTO productos (nombre_producto, descripcion, precio, stock, imagen, id_categoria, id_disponible) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $stock, $imagenes_json, $categoria);

    if ($stmt->execute()) {
        header("Location: ../gestion.php?status=success");
    } else {
        header("Location: ../gestion.php?status=error&msg=" . urlencode($stmt->error));
    }
    $stmt->close();
    exit;
}
?>

