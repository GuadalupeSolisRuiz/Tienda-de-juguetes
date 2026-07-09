<?php
$servidor = "localhost";
$usuario = "root";
$pass = "";
$bd = "tienda_virtual";
$alerta = "";
$conexion_error = null;

$conexion = new mysqli($servidor, $usuario, $pass, $bd);
if ($conexion->connect_error) {
    $conexion_error = $conexion->connect_error;
    $conexion = null;
}
?>