<?php
$mysqli = new mysqli("localhost", "root", "", "tienda_virtual");
if ($mysqli->connect_errno) { echo "DB_ERROR:" . $mysqli->connect_error; exit(1); }
$mysqli->set_charset("utf8mb4");
$mysqli->query("INSERT IGNORE INTO disponibilidad (id_disponible, tipo_disp) VALUES (1, 'Disponible')");
$mysqli->query("INSERT IGNORE INTO categoria (id_categoria, nombre_categoria) VALUES (1, 'Peluches')");
$nombre = "Oso de peluche";
$descripcion = "Peluche ultra suave de 60cm. Perfecto para abrazar.";
$precio = 459.00;
$stock = 12;
$imagenes_json = '{"frente":"Juguetes/osof.png","izquierda":"Juguetes/osoi.png","derecha":"Juguetes/osod.png"}';
$stmt = $mysqli->prepare("INSERT INTO productos (nombre_producto, descripcion, precio, stock, imagen, id_categoria, id_disponible) VALUES (?, ?, ?, ?, ?, 1, 1)");
$stmt->bind_param("ssdis", $nombre, $descripcion, $precio, $stock, $imagenes_json);
if ($stmt->execute()) {
    echo "INSERT_OK:" . $stmt->insert_id;
} else {
    echo "INSERT_ERR:" . $stmt->error;
}
?>
