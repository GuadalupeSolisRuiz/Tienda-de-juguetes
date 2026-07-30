<?php
$servidor = "localhost";
$usuario = "root";
$pass = "";
$bd = "tienda_virtual";
$alerta = "";

$conexion = new mysqli($servidor, $usuario, $pass, $bd);
if ($conexion->connect_error) {
    if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Error de conexión con la base de datos.']);
    } else {
        echo "Error al conectar la base de datos: " . $conexion->connect_error;
    }
    exit;
}
$conexion->set_charset('utf8mb4');
?>