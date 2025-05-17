<?php

$host = "localhost";
$user = "root";
$password = ""; // En XAMPP por defecto está vacío
$database = "tienda_online"; // Asegúrate de que este nombre coincide con tu base de datos

$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Error de conexión a la base de datos: " . $conn->connect_error);
}
?>
