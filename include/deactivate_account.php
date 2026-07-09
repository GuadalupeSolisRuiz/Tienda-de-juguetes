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

if (!isset($conexion) || $conexion === null) {
    echo json_encode(['success' => false, 'message' => 'No se pudo conectar a la base de datos.']);
    exit;
}

$id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
$confirmacion = trim($_POST['confirmacion'] ?? '');

if (!$id_usuario || !isset($_SESSION['usuario_id']) || (int)$_SESSION['usuario_id'] !== $id_usuario) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para realizar esta acción.']);
    exit;
}

if (strtolower($confirmacion) !== 'desactivar') {
    echo json_encode(['success' => false, 'message' => 'Escribe la palabra DESACTIVAR para confirmar.']);
    exit;
}

$stmt = $conexion->prepare('SELECT COUNT(*) AS total FROM pedidos WHERE id_usuario = ?');
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$pedido = $resultado->fetch_assoc();
$stmt->close();

if ((int)$pedido['total'] > 0) {
    echo json_encode(['success' => false, 'message' => 'No puedes desactivar tu cuenta porque tienes pedidos pendientes o asociados.']);
    $conexion->close();
    exit;
}

$rolStmt = $conexion->prepare("SELECT id_rol FROM rol WHERE LOWER(nombre_rol) = 'inactivo' LIMIT 1");
if ($rolStmt) {
    $rolStmt->execute();
    $rolResultado = $rolStmt->get_result();
    $rolData = $rolResultado->fetch_assoc();
    $rolStmt->close();

    if (!$rolData) {
        $insertRol = $conexion->prepare("INSERT INTO rol (nombre_rol) VALUES ('inactivo')");
        if ($insertRol) {
            $insertRol->execute();
            $rolId = $conexion->insert_id;
            $insertRol->close();
        } else {
            $rolId = 0;
        }
    } else {
        $rolId = (int)$rolData['id_rol'];
    }
} else {
    $rolId = 0;
}

if ($rolId <= 0) {
    echo json_encode(['success' => false, 'message' => 'No se pudo preparar el rol de cuenta inactiva.']);
    $conexion->close();
    exit;
}

$stmt = $conexion->prepare('UPDATE usuarios SET id_rol = ? WHERE id_usuario = ?');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'No se pudo procesar la solicitud.']);
    $conexion->close();
    exit;
}

$stmt->bind_param('ii', $rolId, $id_usuario);

if ($stmt->execute()) {
    session_unset();
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Tu cuenta ha sido desactivada correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo desactivar la cuenta. Intenta de nuevo.']);
}

$stmt->close();
$conexion->close();
?>
