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
            $ext = pathinfo($_FILES["img_$vista"]['name'], PATHINFO_EXTENSION);
            
            // Generar nombre único para evitar sobreescribir archivos
            $filename = "prod_" . time() . "_{$vista}." . $ext;
            $destino = $target_dir . $filename;

            if (move_uploaded_file($tmp_name, $destino)) {
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

