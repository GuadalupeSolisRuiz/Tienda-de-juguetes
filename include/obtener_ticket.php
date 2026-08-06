<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada.']);
    exit();
}

include __DIR__ . '/conect.php';

if (!$conexion) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit();
}

$usuario_id = intval($_SESSION['usuario_id']);
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de pedido no válido.']);
    exit();
}

// Comprobar que el pedido pertenece al usuario o es admin/editor
$userRol = strtolower($_SESSION['usuario_rol'] ?? 'cliente');
$sqlUserCheck = in_array($userRol, ['administrador', 'editor'])
    ? "SELECT p.*, COALESCE(mp.tipo_metodo, 'Efectivo') AS tipo_metodo FROM pedidos p LEFT JOIN metodo_pago mp ON p.id_metodopago = mp.id_metodopago WHERE p.id_pedidos = ?"
    : "SELECT p.*, COALESCE(mp.tipo_metodo, 'Efectivo') AS tipo_metodo FROM pedidos p LEFT JOIN metodo_pago mp ON p.id_metodopago = mp.id_metodopago WHERE p.id_pedidos = ? AND p.id_usuario = ?";

$stmt = $conexion->prepare($sqlUserCheck);
if (in_array($userRol, ['administrador', 'editor'])) {
    $stmt->bind_param("i", $order_id);
} else {
    $stmt->bind_param("ii", $order_id, $usuario_id);
}
$stmt->execute();
$resOrder = $stmt->get_result();

if ($resOrder->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Pedido no encontrado o no tienes permisos para verlo.']);
    exit();
}

$pedido = $resOrder->fetch_assoc();
$stmt->close();

$target_user_id = intval($pedido['id_usuario']);

// Obtener cliente info
$stmtUser = $conexion->prepare("SELECT nombre, apellido, correo, telefono FROM usuarios WHERE id_usuario = ?");
$stmtUser->bind_param("i", $target_user_id);
$stmtUser->execute();
$userData = $stmtUser->get_result()->fetch_assoc();
$stmtUser->close();

$cliente_nombre = trim(($userData['nombre'] ?? '') . ' ' . ($userData['apellido'] ?? ''));
$cliente_correo = $userData['correo'] ?? '';
$cliente_telefono = $userData['telefono'] ?? '';

// Obtener items del pedido
$sqlItems = "SELECT dp.cantidad, dp.precio_unitario, dp.subtotal, prod.nombre_producto
             FROM detalle_pedidos dp
             LEFT JOIN productos prod ON dp.id_producto = prod.id_productos
             WHERE dp.id_pedidos = ?";
$stmtItems = $conexion->prepare($sqlItems);
$stmtItems->bind_param("i", $order_id);
$stmtItems->execute();
$resItems = $stmtItems->get_result();

$items = [];
$subtotal_items = 0;
while ($rowItem = $resItems->fetch_assoc()) {
    $sub = floatval($rowItem['subtotal']);
    $subtotal_items += $sub;
    $items[] = [
        'nombre' => $rowItem['nombre_producto'] ?? 'Juguete',
        'cantidad' => intval($rowItem['cantidad']),
        'precio_unitario' => floatval($rowItem['precio_unitario']),
        'subtotal' => $sub
    ];
}
$stmtItems->close();
$conexion->close();

$total = floatval($pedido['total']);
$shipping = ($subtotal_items >= 1000 || $subtotal_items == 0) ? 0 : 80;
$discount = max(0, ($subtotal_items + $shipping) - $total);

$folio = "TN-" . str_pad($order_id, 6, "0", STR_PAD_LEFT);

$ticketData = [
    'folio' => $folio,
    'id_pedido' => $order_id,
    'fecha' => date('d/m/Y h:i A', strtotime($pedido['fecha'])),
    'cliente_nombre' => $cliente_nombre,
    'cliente_correo' => $cliente_correo,
    'cliente_telefono' => $cliente_telefono,
    'metodo_pago' => ucfirst($pedido['tipo_metodo']),
    'items' => $items,
    'subtotal' => $subtotal_items,
    'envio' => $shipping,
    'descuento' => $discount,
    'total' => $total
];

echo json_encode([
    'success' => true,
    'ticket' => $ticketData
]);
