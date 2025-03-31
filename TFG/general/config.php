<?php

// Configuración de la conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$password = ""; // En XAMPP, la contraseña por defecto es vacía
$base_datos = "ecommerce";

// Crear conexión
$conn = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar el juego de caracteres a UTF-8
$conn->set_charset("utf8");

?>
