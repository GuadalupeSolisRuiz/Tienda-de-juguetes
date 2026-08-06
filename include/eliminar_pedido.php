<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
ob_start();

$userRol = strtolower($_SESSION['usuario_rol'] ?? '');
if (!isset($_SESSION['usuario_id']) || !in_array($userRol, ['administrador', 'admin'])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Acceso restringido a administradores.']);
    exit();
}

include __DIR__ . '/conect.php';

if (!$conexion) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$order_id = isset($input['order_id']) ? intval($input['order_id']) : 0;

if ($order_id <= 0) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'ID de pedido no válido.']);
    exit();
}

$conexion->begin_transaction();

try {
    // 1. Eliminar pagos asociados
    $stmt1 = $conexion->prepare("DELETE FROM pagos WHERE id_pedidos = ?");
    $stmt1->bind_param("i", $order_id);
    $stmt1->execute();
    $stmt1->close();

    // 2. Eliminar detalle de pedidos
    $stmt2 = $conexion->prepare("DELETE FROM detalle_pedidos WHERE id_pedidos = ?");
    $stmt2->bind_param("i", $order_id);
    $stmt2->execute();
    $stmt2->close();

    // 3. Eliminar el pedido
    $stmt3 = $conexion->prepare("DELETE FROM pedidos WHERE id_pedidos = ?");
    $stmt3->bind_param("i", $order_id);
    $stmt3->execute();
    $stmt3->close();

    $conexion->commit();

    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => "El ticket #{$order_id} ha sido eliminado correctamente."
    ]);
} catch (Exception $e) {
    $conexion->rollback();
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar el pedido: ' . $e->getMessage()
    ]);
}

$conexion->close();
exit();
