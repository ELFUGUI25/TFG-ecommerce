<?php

$host = "localhost";
$usuario = "root";
$contrasena = "";
$basededatos = "tienda_online";

$conexion = new mysqli($host, $usuario, $contrasena, $basededatos);

if ($conexion->connect_error) {
    die("❌ Conexión fallida: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

?>
