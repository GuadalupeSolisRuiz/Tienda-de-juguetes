<?php
ob_start();
error_reporting(0);
ini_set('display_errors', '0');
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para realizar un pedido.']);
    exit();
}

include __DIR__ . '/conect.php';

if (!$conexion) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . $conexion_error]);
    exit();
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input || empty($input['items']) || !is_array($input['items'])) {
    echo json_encode(['success' => false, 'message' => 'El carrito está vacío o los datos son inválidos.']);
    exit();
}

$usuario_id = intval($_SESSION['usuario_id']);
$metodo_pago_tipo = isset($input['metodo_pago']) && in_array(strtolower($input['metodo_pago']), ['tarjeta', 'efectivo'])
    ? strtolower($input['metodo_pago'])
    : 'efectivo';

$monto_efectivo = isset($input['monto_efectivo']) ? floatval($input['monto_efectivo']) : 0;
$cambio_efectivo = isset($input['cambio_efectivo']) ? floatval($input['cambio_efectivo']) : 0;

$items = $input['items'];
$subtotal_cart = floatval($input['subtotal'] ?? 0);
$shipping_cost = floatval($input['envio'] ?? 0);
$discount_amount = floatval($input['descuento'] ?? 0);
$total_compra = floatval($input['total'] ?? 0);
$codigo_cupon = trim($input['cupon'] ?? '');

// 1. Asegurar estructura de tablas en la BD si no existen o faltan columnas
$checkColumn = $conexion->query("SHOW COLUMNS FROM detalle_pedidos LIKE 'id_pedidos'");
if ($checkColumn && $checkColumn->num_rows == 0) {
    @$conexion->query("ALTER TABLE detalle_pedidos ADD COLUMN id_pedidos INT NOT NULL AFTER id_detallepedido");
}

// 2. Asegurar que existan registros en metodo_pago
$checkMetodo = $conexion->query("SELECT id_metodopago, tipo_metodo FROM metodo_pago");
$metodos_map = [];
if ($checkMetodo) {
    while ($row = $checkMetodo->fetch_assoc()) {
        $metodos_map[strtolower($row['tipo_metodo'])] = intval($row['id_metodopago']);
    }
}

if (!isset($metodos_map['efectivo'])) {
    $conexion->query("INSERT INTO metodo_pago (id_metodopago, tipo_metodo) VALUES (1, 'Efectivo')");
    $metodos_map['efectivo'] = 1;
}
if (!isset($metodos_map['tarjeta'])) {
    $conexion->query("INSERT INTO metodo_pago (id_metodopago, tipo_metodo) VALUES (2, 'Tarjeta')");
    $metodos_map['tarjeta'] = 2;
}

$id_metodopago = $metodos_map[$metodo_pago_tipo] ?? 1;

// 3. Asegurar registros en tabla estado
$checkEstado = $conexion->query("SELECT id_estado, tipo_estado FROM estado");
$estados_map = [];
if ($checkEstado) {
    while ($row = $checkEstado->fetch_assoc()) {
        $estados_map[strtolower($row['tipo_estado'])] = intval($row['id_estado']);
    }
}
if (!isset($estados_map['completado'])) {
    $conexion->query("INSERT INTO estado (id_estado, tipo_estado) VALUES (1, 'Completado')");
    $estados_map['completado'] = 1;
}
$id_estado = $estados_map['completado'] ?? 1;

// 4. Iniciar Transacción SQL
$conexion->begin_transaction();

try {
    // Insertar en tabla `pedidos`
    $fecha_actual = date('Y-m-d H:i:s');
    $stmtPedido = $conexion->prepare("INSERT INTO pedidos (id_usuario, fecha, total, id_metodopago, id_estado) VALUES (?, ?, ?, ?, ?)");
    $stmtPedido->bind_param("isdii", $usuario_id, $fecha_actual, $total_compra, $id_metodopago, $id_estado);
    
    if (!$stmtPedido->execute()) {
        throw new Exception("Error al guardar el pedido: " . $stmtPedido->error);
    }
    
    $id_pedido = $stmtPedido->insert_id;
    $stmtPedido->close();

    // Insertar en tabla `detalle_pedidos` y procesar productos
    $stmtDetalle = $conexion->prepare("INSERT INTO detalle_pedidos (id_pedidos, cantidad, precio_unitario, subtotal, id_producto) VALUES (?, ?, ?, ?, ?)");
    
    $ticket_items = [];
    
    foreach ($items as $item) {
        $id_prod = intval($item['id']);
        $qty = intval($item['qty']);
        
        // Parse precio
        $price = 0;
        if (is_numeric($item['price'])) {
            $price = floatval($item['price']);
        } else {
            $cleanPrice = preg_replace('/[^\d.]/', '', str_replace(',', '.', $item['price']));
            $price = floatval($cleanPrice);
        }
        
        $subtotal_linea = $price * $qty;
        
        $stmtDetalle->bind_param("iiddd", $id_pedido, $qty, $price, $subtotal_linea, $id_prod);
        if (!$stmtDetalle->execute()) {
            throw new Exception("Error al guardar el detalle del pedido: " . $stmtDetalle->error);
        }

        $ticket_items[] = [
            'id' => $id_prod,
            'nombre' => $item['name'] ?? 'Producto',
            'cantidad' => $qty,
            'precio_unitario' => $price,
            'subtotal' => $subtotal_linea
        ];
    }
    $stmtDetalle->close();

    // Insertar en tabla `pagos`
    $stmtPago = $conexion->prepare("INSERT INTO pagos (monto, id_pedidos, id_metodopago, id_estado) VALUES (?, ?, ?, ?)");
    $stmtPago->bind_param("diii", $total_compra, $id_pedido, $id_metodopago, $id_estado);
    $stmtPago->execute();
    $stmtPago->close();

    // Confirmar transacción
    $conexion->commit();

    // Obtener datos del cliente para el ticket
    $stmtUser = $conexion->prepare("SELECT nombre, apellido, correo, telefono FROM usuarios WHERE id_usuario = ?");
    $stmtUser->bind_param("i", $usuario_id);
    $stmtUser->execute();
    $userData = $stmtUser->get_result()->fetch_assoc();
    $stmtUser->close();

    $cliente_nombre = trim(($userData['nombre'] ?? '') . ' ' . ($userData['apellido'] ?? ''));
    $cliente_correo = $userData['correo'] ?? '';
    $cliente_telefono = $userData['telefono'] ?? '';

    $folio = "TN-" . str_pad($id_pedido, 6, "0", STR_PAD_LEFT);

    $ticketData = [
        'folio' => $folio,
        'id_pedido' => $id_pedido,
        'fecha' => date('d/m/Y h:i A', strtotime($fecha_actual)),
        'cliente_nombre' => $cliente_nombre,
        'cliente_correo' => $cliente_correo,
        'cliente_telefono' => $cliente_telefono,
        'metodo_pago' => ucfirst($metodo_pago_tipo),
        'monto_efectivo' => $monto_efectivo,
        'cambio_efectivo' => $cambio_efectivo,
        'items' => $ticket_items,
        'subtotal' => $subtotal_cart,
        'envio' => $shipping_cost,
        'descuento' => $discount_amount,
        'cupon' => $codigo_cupon,
        'total' => $total_compra
    ];

    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => '¡Compra realizada con éxito!',
        'order_id' => $id_pedido,
        'ticket' => $ticketData
    ]);

} catch (Exception $e) {
    $conexion->rollback();
    ob_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conexion->close();
