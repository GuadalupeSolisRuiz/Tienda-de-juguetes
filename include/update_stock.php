<?php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/conect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$action = $_POST['action'] ?? '';
$idProducto = (int)($_POST['id_producto'] ?? $_POST['id'] ?? 0);
$cantidad = (int)($_POST['cantidad'] ?? $_POST['qty'] ?? 1);

if ($idProducto <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de producto inválido.']);
    exit;
}

if ($action === 'decrease') {
    $stmtCheck = $conexion->prepare('SELECT stock FROM productos WHERE id_productos = ?');
    $stmtCheck->bind_param('i', $idProducto);
    $stmtCheck->execute();
    $res = $stmtCheck->get_result()->fetch_assoc();
    $stmtCheck->close();

    if (!$res) {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado.']);
        exit;
    }

    $stockActual = (int)$res['stock'];
    if ($stockActual < $cantidad) {
        echo json_encode(['success' => false, 'message' => 'Stock insuficiente.', 'stock' => $stockActual]);
        exit;
    }

    $stmt = $conexion->prepare('UPDATE productos SET stock = stock - ? WHERE id_productos = ? AND stock >= ?');
    $stmt->bind_param('iii', $cantidad, $idProducto, $cantidad);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $nuevoStock = max(0, $stockActual - $cantidad);
        echo json_encode(['success' => true, 'nuevo_stock' => $nuevoStock]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el stock.', 'stock' => $stockActual]);
    }
    $stmt->close();
    $conexion->close();
    exit;
}

if ($action === 'increase') {
    $stmt = $conexion->prepare('UPDATE productos SET stock = stock + ? WHERE id_productos = ?');
    $stmt->bind_param('ii', $cantidad, $idProducto);
    $stmt->execute();
    
    $stmtCheck = $conexion->prepare('SELECT stock FROM productos WHERE id_productos = ?');
    $stmtCheck->bind_param('i', $idProducto);
    $stmtCheck->execute();
    $res = $stmtCheck->get_result()->fetch_assoc();
    $nuevoStock = (int)($res['stock'] ?? 0);
    $stmtCheck->close();
    
    echo json_encode(['success' => true, 'nuevo_stock' => $nuevoStock]);
    $conexion->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
?>
