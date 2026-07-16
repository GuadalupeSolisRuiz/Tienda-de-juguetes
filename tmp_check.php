<?php
$mysqli = new mysqli("localhost", "root", "", "tienda_virtual");
$mysqli->set_charset("utf8mb4");
$stmt = $mysqli->prepare("SELECT nombre_producto, descripcion, precio, imagen FROM productos WHERE nombre_producto = ?");
$name = "Oso de peluche";
$stmt->bind_param("s", $name);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
echo json_encode($row, JSON_UNESCAPED_UNICODE);
?>
