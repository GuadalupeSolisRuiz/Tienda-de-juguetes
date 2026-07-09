<?php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/conect.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_rol']) || strtolower($_SESSION['usuario_rol']) !== 'administrador') {
    echo json_encode(['success' => false, 'message' => 'No autorizado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');
    $sexo = trim($_POST['sexo'] ?? '');
    $idRol = (int)($_POST['id_rol'] ?? 1);

    if ($nombre === '' || $apellido === '' || $correo === '' || $contrasena === '') {
        echo json_encode(['success' => false, 'message' => 'Nombre, apellido, correo y contraseña son obligatorios.']);
        exit;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'El correo no es válido.']);
        exit;
    }

    if (!in_array($idRol, [1, 2, 3], true)) {
        echo json_encode(['success' => false, 'message' => 'Rol inválido.']);
        exit;
    }

    $check = $conexion->prepare('SELECT id_usuario FROM usuarios WHERE correo = ?');
    $check->bind_param('s', $correo);
    $check->execute();
    if ($check->get_result()->fetch_assoc()) {
        echo json_encode(['success' => false, 'message' => 'El correo ya está registrado.']);
        $check->close();
        $conexion->close();
        exit;
    }
    $check->close();

    $checkSexoColumn = $conexion->query("SHOW COLUMNS FROM usuarios LIKE 'sexo'");
    if ($checkSexoColumn && $checkSexoColumn->num_rows === 0) {
        $conexion->query("ALTER TABLE usuarios ADD COLUMN sexo VARCHAR(20) NULL DEFAULT NULL");
    }

    $hash = password_hash($contrasena, PASSWORD_BCRYPT);
    $fecha = date('Y-m-d');
    $stmt = $conexion->prepare('INSERT INTO usuarios (nombre, apellido, correo, contraseña, telefono, sexo, fecha_registro, id_rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssssssi', $nombre, $apellido, $correo, $hash, $telefono, $sexo, $fecha, $idRol);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente. Se enviaron las credenciales al correo indicado.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo crear el usuario.']);
    }
    $stmt->close();
    $conexion->close();
    exit;
}

if ($action === 'delete') {
    $idUsuario = (int)($_POST['id_usuario'] ?? 0);
    if ($idUsuario <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido.']);
        exit;
    }

    $checkPedidos = $conexion->prepare('SELECT COUNT(*) AS total FROM pedidos WHERE id_usuario = ?');
    $checkPedidos->bind_param('i', $idUsuario);
    $checkPedidos->execute();
    $pedidos = $checkPedidos->get_result()->fetch_assoc();
    $checkPedidos->close();

    if ((int)$pedidos['total'] > 0) {
        echo json_encode(['success' => false, 'message' => 'No se puede eliminar el usuario porque tiene pedidos asociados.']);
        $conexion->close();
        exit;
    }

    $stmt = $conexion->prepare('DELETE FROM usuarios WHERE id_usuario = ?');
    $stmt->bind_param('i', $idUsuario);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el usuario.']);
    }
    $stmt->close();
    $conexion->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no reconocida.']);
?>