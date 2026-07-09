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
$contrasena = trim($_POST['contrasena'] ?? '');

if (!$id_usuario || !isset($_SESSION['usuario_id']) || (int)$_SESSION['usuario_id'] !== $id_usuario) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para realizar esta acción.']);
    exit;
}

if ($contrasena === '') {
    echo json_encode(['success' => false, 'message' => 'Ingresa tu contraseña para confirmar la eliminación.']);
    exit;
}

$stmt = $conexion->prepare('SELECT contraseña FROM usuarios WHERE id_usuario = ?');
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

if (!$usuario || !password_verify($contrasena, $usuario['contraseña'])) {
    echo json_encode(['success' => false, 'message' => 'La contraseña es incorrecta.']);
    $conexion->close();
    exit;
}

$stmt = $conexion->prepare('SELECT COUNT(*) AS total FROM pedidos WHERE id_usuario = ?');
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$pedido = $resultado->fetch_assoc();
$stmt->close();

if ((int)$pedido['total'] > 0) {
    echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu cuenta porque tienes pedidos pendientes o asociados.']);
    $conexion->close();
    exit;
}

$stmt = $conexion->prepare('DELETE FROM usuarios WHERE id_usuario = ?');
$stmt->bind_param('i', $id_usuario);

if ($stmt->execute()) {
    session_unset();
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Tu cuenta ha sido eliminada correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo eliminar la cuenta. Intenta de nuevo.']);
}

$stmt->close();
$conexion->close();
?>