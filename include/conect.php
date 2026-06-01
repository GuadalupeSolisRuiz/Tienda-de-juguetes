<?php
$servidor = "localhost";
$usuario = "root";
$pass = "";
$bd = "tienda";
$alerta = "";

$conexion = new mysqli($servidor, $usuario, $pass, $bd);
if ($conexion->connect_error) {
    die("Error al conectar la base de datos" . $conexion->connect_error);
} else {
    echo "Conexión exitosa a la base de datos";
}
?>