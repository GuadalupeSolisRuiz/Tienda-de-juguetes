<?php
// include/eliminar_producto.php
require_once 'conect.php';

if (isset($_GET['id'])) {
    $id_producto = intval($_GET['id']);

    $stmt = $mysqli->prepare("DELETE FROM productos WHERE id_productos = ?");
    $stmt->bind_param("i", $id_producto);

    if ($stmt->execute()) {
        header("Location: ../gestion.php?status=deleted");
    } else {
        header("Location: ../gestion.php?status=error");
    }
    $stmt->close();
}
exit;
?>