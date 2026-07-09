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

$action = $_POST['action'] ?? '';

if ($action === 'send') {
    $correo = strtolower(trim($_POST['correo'] ?? ''));

    if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Ingresa un correo válido.']);
        exit;
    }

    $stmt = $conexion->prepare('SELECT id_usuario FROM usuarios WHERE correo = ?');
    $stmt->bind_param('s', $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();

    if (!$usuario) {
        echo json_encode(['success' => false, 'message' => 'No existe una cuenta con ese correo.']);
        $conexion->close();
        exit;
    }

    $codigo = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['reset_email'] = $correo;
    $_SESSION['reset_code'] = $codigo;

    echo json_encode([
        'success' => true,
        'message' => 'Se envió un código al correo registrado. Código: ' . $codigo,
        'code' => $codigo
    ]);
    $conexion->close();
    exit;
}

if ($action === 'reset') {
    $codigoIngresado = trim($_POST['codigo'] ?? '');
    $nuevaContrasena = trim($_POST['nueva_contrasena'] ?? '');
    $confirmarContrasena = trim($_POST['confirmar_contrasena'] ?? '');
    $correo = $_SESSION['reset_email'] ?? '';
    $codigoEsperado = $_SESSION['reset_code'] ?? '';

    if ($correo === '' || $codigoEsperado === '') {
        echo json_encode(['success' => false, 'message' => 'No hay una recuperación activa. Solicita un nuevo código.']);
        exit;
    }

    if (!hash_equals((string)$codigoEsperado, (string)$codigoIngresado)) {
        echo json_encode(['success' => false, 'message' => 'El código ingresado no coincide.']);
        exit;
    }

    if ($nuevaContrasena === '' || $confirmarContrasena === '') {
        echo json_encode(['success' => false, 'message' => 'Ingresa y confirma la nueva contraseña.']);
        exit;
    }

    if (strlen($nuevaContrasena) < 8 || !preg_match('/[A-Za-z]/', $nuevaContrasena) || !preg_match('/[0-9]/', $nuevaContrasena)) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres y combinar letras y números.']);
        exit;
    }

    if ($nuevaContrasena !== $confirmarContrasena) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
        exit;
    }

    $hash = password_hash($nuevaContrasena, PASSWORD_BCRYPT);
    $stmt = $conexion->prepare('UPDATE usuarios SET contraseña = ? WHERE correo = ?');
    $stmt->bind_param('ss', $hash, $correo);

    if ($stmt->execute()) {
        unset($_SESSION['reset_email'], $_SESSION['reset_code']);
        echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo actualizar la contraseña.']);
    }

    $stmt->close();
    $conexion->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no reconocida.']);
?>
