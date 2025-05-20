<?php
/**
 * Archivo de conexión a la base de datos simplificado
 */

$host = "localhost";
$user = "root";
$password = ""; 
$database = "tienda_online";

$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Error de conexión a la base de datos: " . $conn->connect_error);
}
?>
