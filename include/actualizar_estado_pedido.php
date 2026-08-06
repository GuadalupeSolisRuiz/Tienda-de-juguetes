<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
ob_start();

$userRol = strtolower($_SESSION['usuario_rol'] ?? '');
if (!isset($_SESSION['usuario_id']) || !in_array($userRol, ['administrador', 'admin', 'editor'])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
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
$nuevo_estado = isset($input['estado']) ? trim($input['estado']) : '';

$estadosValidos = ['Completado', 'Pendiente', 'Cancelado'];
if ($order_id <= 0 || !in_array($nuevo_estado, $estadosValidos)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Parámetros no válidos.']);
    exit();
}

// Asegurar que existan los registros en la tabla estado
@$conexion->query("INSERT IGNORE INTO estado (id_estado, tipo_estado) VALUES (1, 'Completado'), (2, 'Pendiente'), (3, 'Cancelado')");

$resEst = $conexion->query("SELECT id_estado, tipo_estado FROM estado");
$estados_map = [];
if ($resEst) {
    while ($rowEst = $resEst->fetch_assoc()) {
        $estados_map[strtolower($rowEst['tipo_estado'])] = intval($rowEst['id_estado']);
    }
}

$id_estado = $estados_map[strtolower($nuevo_estado)] ?? 1;

// Actualizar id_estado en pedidos
$stmt = $conexion->prepare("UPDATE pedidos SET id_estado = ? WHERE id_pedidos = ?");
$stmt->bind_param("ii", $id_estado, $order_id);

if ($stmt->execute()) {
    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => "Estado del pedido #{$order_id} actualizado a {$nuevo_estado}.",
        'order_id' => $order_id,
        'nuevo_estado' => $nuevo_estado
    ]);
} else {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar el estado: ' . $stmt->error
    ]);
}

$stmt->close();
$conexion->close();
exit();
