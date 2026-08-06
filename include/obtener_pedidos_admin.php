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

$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;

$whereClause = "WHERE 1=1";
$params = [];
$types = "";

if ($id_usuario > 0) {
    $whereClause .= " AND p.id_usuario = ?";
    $params[] = $id_usuario;
    $types .= "i";
}

$sqlOrders = "
    SELECT p.id_pedidos, p.fecha, p.total,
           COALESCE(e.tipo_estado, 'Completado') AS estado,
           p.id_usuario, u.nombre, u.apellido, u.correo, u.telefono,
           COALESCE(mp.tipo_metodo, 'Efectivo') AS tipo_metodo
    FROM pedidos p
    INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
    LEFT JOIN metodo_pago mp ON p.id_metodopago = mp.id_metodopago
    LEFT JOIN estado e ON p.id_estado = e.id_estado
    {$whereClause}
    ORDER BY p.id_pedidos DESC
";

$stmt = $conexion->prepare($sqlOrders);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resOrders = $stmt->get_result();

$pedidos = [];
while ($row = $resOrders->fetch_assoc()) {
    $order_id = intval($row['id_pedidos']);

    // Obtener detalle de items
    $sqlItems = "
        SELECT dp.cantidad, dp.precio_unitario, dp.subtotal,
               prod.nombre_producto, prod.imagen
        FROM detalle_pedidos dp
        LEFT JOIN productos prod ON dp.id_producto = prod.id_productos
        WHERE dp.id_pedidos = ?
    ";
    $stmtItems = $conexion->prepare($sqlItems);
    $stmtItems->bind_param("i", $order_id);
    $stmtItems->execute();
    $resItems = $stmtItems->get_result();

    $items = [];
    while ($rowItem = $resItems->fetch_assoc()) {
        $vistas = json_decode($rowItem['imagen'] ?? '{}', true);
        $thumb = is_array($vistas) ? ($vistas['frente'] ?? reset($vistas)) : $rowItem['imagen'];

        $items[] = [
            'nombre' => $rowItem['nombre_producto'] ?? 'Juguete',
            'cantidad' => intval($rowItem['cantidad']),
            'precio_unitario' => floatval($rowItem['precio_unitario']),
            'subtotal' => floatval($rowItem['subtotal']),
            'imagen' => $thumb ?: 'Juguetes/default.png'
        ];
    }
    $stmtItems->close();

    $pedidos[] = [
        'id_pedido' => $order_id,
        'folio' => "TN-" . str_pad($order_id, 6, "0", STR_PAD_LEFT),
        'fecha' => date('d/m/Y h:i A', strtotime($row['fecha'])),
        'total' => floatval($row['total']),
        'estado' => ucfirst($row['estado']),
        'metodo_pago' => ucfirst($row['tipo_metodo']),
        'id_usuario' => intval($row['id_usuario']),
        'cliente_nombre' => trim($row['nombre'] . ' ' . $row['apellido']),
        'cliente_correo' => $row['correo'],
        'cliente_telefono' => $row['telefono'] ?: '—',
        'items' => $items
    ];
}
$stmt->close();
$conexion->close();

ob_clean();
echo json_encode([
    'success' => true,
    'total_pedidos' => count($pedidos),
    'pedidos' => $pedidos
]);
exit();
