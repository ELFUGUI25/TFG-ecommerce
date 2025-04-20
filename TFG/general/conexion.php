<?php
$host = "localhost";
$usuario = "root";
$contrasena = ""; // o tu contraseña si tiene
$base_de_datos = "";

$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>
