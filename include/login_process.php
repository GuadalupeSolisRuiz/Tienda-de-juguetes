<?php
// ── Proceso de Inicio de Sesión ──
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "conect.php";

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

// Recoger y limpiar datos
$correo = trim($_POST['correo'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');

// Validaciones básicas
if (empty($correo) || empty($contrasena)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido.']);
    exit;
}

// Buscar el usuario y obtener su rol
$stmt = $conexion->prepare(
    "SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.contraseña, u.id_rol, r.nombre_rol 
     FROM usuarios u 
     INNER JOIN rol r ON u.id_rol = r.id_rol 
     WHERE u.correo = ?"
);
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico o la contraseña son incorrectos.']);
    $stmt->close();
    $conexion->close();
    exit;
}

// Verificar la contraseña hash con bcrypt
if (password_verify($contrasena, $usuario['contraseña'])) {
    // Guardar información en la sesión
    $_SESSION['usuario_id'] = $usuario['id_usuario'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_apellido'] = $usuario['apellido'];
    $_SESSION['usuario_correo'] = $usuario['correo'];
    $_SESSION['usuario_rol'] = $usuario['nombre_rol'];

    echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso. ¡Bienvenido!']);
} else {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico o la contraseña son incorrectos.']);
}

$stmt->close();
$conexion->close();
?>
