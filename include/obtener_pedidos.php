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

$sql = "SELECT p.id_pedidos, p.fecha, p.total, 
               COALESCE(mp.tipo_metodo, 'Efectivo') AS metodo_pago, 
               COALESCE(e.tipo_estado, 'Completado') AS estado
        FROM pedidos p
        LEFT JOIN metodo_pago mp ON p.id_metodopago = mp.id_metodopago
        LEFT JOIN estado e ON p.id_estado = e.id_estado
        WHERE p.id_usuario = ?
        ORDER BY p.id_pedidos DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();

$pedidos = [];

while ($row = $res->fetch_assoc()) {
    $id_pedido = intval($row['id_pedidos']);

    // Obtener ítems de este pedido
    $sqlItems = "SELECT dp.cantidad, dp.precio_unitario, dp.subtotal, prod.nombre_producto, prod.imagen
                 FROM detalle_pedidos dp
                 LEFT JOIN productos prod ON dp.id_producto = prod.id_productos
                 WHERE dp.id_pedidos = ?";
    $stmtItems = $conexion->prepare($sqlItems);
    $stmtItems->bind_param("i", $id_pedido);
    $stmtItems->execute();
    $resItems = $stmtItems->get_result();

    $items = [];
    while ($item = $resItems->fetch_assoc()) {
        $imagenPath = 'assets/img/placeholder.png';
        if (!empty($item['imagen'])) {
            $imgJson = json_decode($item['imagen'], true);
            if (is_array($imgJson) && isset($imgJson['frente'])) {
                $imagenPath = $imgJson['frente'];
            } else if (is_string($item['imagen'])) {
                $imagenPath = $item['imagen'];
            }
        }

        $items[] = [
            'nombre' => $item['nombre_producto'] ?? 'Juguete',
            'cantidad' => intval($item['cantidad']),
            'precio_unitario' => floatval($item['precio_unitario']),
            'subtotal' => floatval($item['subtotal']),
            'imagen' => $imagenPath
        ];
    }
    $stmtItems->close();

    $folio = "TN-" . str_pad($id_pedido, 6, "0", STR_PAD_LEFT);

    $pedidos[] = [
        'id_pedido' => $id_pedido,
        'folio' => $folio,
        'fecha' => date('d/m/Y h:i A', strtotime($row['fecha'])),
        'total' => floatval($row['total']),
        'metodo_pago' => ucfirst($row['metodo_pago']),
        'estado' => ucfirst($row['estado']),
        'items' => $items
    ];
}

$stmt->close();
$conexion->close();

echo json_encode([
    'success' => true,
    'pedidos' => $pedidos
]);
