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

$id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');
$contrasena2 = trim($_POST['contrasena2'] ?? '');

if (!$id_usuario || !isset($_SESSION['usuario_id']) || (int)$_SESSION['usuario_id'] !== $id_usuario) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para actualizar este perfil.']);
    exit;
}

if (empty($nombre) || empty($apellido) || empty($correo)) {
    echo json_encode(['success' => false, 'message' => 'Nombre, apellido y correo son obligatorios.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido.']);
    exit;
}

if ($contrasena !== '' || $contrasena2 !== '') {
    if ($contrasena !== $contrasena2) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
        exit;
    }

    if (strlen($contrasena) < 8 || !preg_match('/[A-Za-z]/', $contrasena) || !preg_match('/[0-9]/', $contrasena)) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres y contener letras y números.']);
        exit;
    }
}

$stmt = $conexion->prepare('SELECT id_usuario FROM usuarios WHERE correo = ? AND id_usuario != ?');
$stmt->bind_param('si', $correo, $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->fetch_assoc()) {
    echo json_encode(['success' => false, 'message' => 'Este correo ya está registrado por otro usuario.']);
    $stmt->close();
    $conexion->close();
    exit;
}

$stmt->close();

$sql = 'UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, telefono = ?';

if ($contrasena !== '') {
    $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);
    $sql .= ', contraseña = ?';
}

$sql .= ' WHERE id_usuario = ?';

$stmt = $conexion->prepare($sql);

if ($contrasena !== '') {
    $stmt->bind_param('sssssi', $nombre, $apellido, $correo, $telefono, $contrasenaHash, $id_usuario);
} else {
    $stmt->bind_param('ssssi', $nombre, $apellido, $correo, $telefono, $id_usuario);
}

if ($stmt->execute()) {
    $_SESSION['usuario_nombre'] = $nombre;
    $_SESSION['usuario_apellido'] = $apellido;
    $_SESSION['usuario_correo'] = $correo;

    echo json_encode(['success' => true, 'message' => 'Perfil actualizado correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el perfil. Intenta de nuevo.']);
}

$stmt->close();
$conexion->close();
?>
