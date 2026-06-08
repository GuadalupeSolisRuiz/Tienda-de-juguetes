<?php
// ── Proceso de Registro de Usuario ──
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . "/conect.php";

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

// Recoger y limpiar datos del formulario
$nombre    = trim($_POST['nombre']    ?? '');
$apellido  = trim($_POST['apellido']  ?? '');
$correo    = trim($_POST['correo']    ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');
$contrasena2 = trim($_POST['contrasena2'] ?? '');
$telefono  = trim($_POST['telefono']  ?? '');

// ── Validaciones básicas ──
if (empty($nombre) || empty($apellido) || empty($correo) || empty($contrasena) || empty($contrasena2)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben completarse.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido.']);
    exit;
}

if ($contrasena !== $contrasena2) {
    echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
    exit;
}

if (strlen($contrasena) < 8) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres.']);
    exit;
}

if (!preg_match('/[A-Za-z]/', $contrasena) || !preg_match('/[0-9]/', $contrasena)) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe contener tanto letras como números.']);
    exit;
}

// ── Hashear la contraseña de forma segura ──
$contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);

// ── Verificar si el correo ya existe ──
$stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->fetch_assoc()) {
    echo json_encode(['success' => false, 'message' => 'Este correo ya está registrado. Prueba iniciar sesión.']);
    $stmt->close();
    exit;
}
$stmt->close();

// ── Insertar en la base de datos ──
// id_rol = 1 (cliente), fecha_registro = hoy
$fecha_registro = date('Y-m-d');
$id_rol = 1;

$stmt = $conexion->prepare(
    "INSERT INTO usuarios (nombre, apellido, correo, contraseña, telefono, fecha_registro, id_rol)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssssssi", $nombre, $apellido, $correo, $contrasenaHash, $telefono, $fecha_registro, $id_rol);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '¡Cuenta creada exitosamente! Ahora puedes iniciar sesión.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar los datos. Intenta de nuevo.']);
}

$stmt->close();
$conexion->close();
?>
