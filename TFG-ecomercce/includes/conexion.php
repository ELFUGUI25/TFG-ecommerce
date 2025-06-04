<?php
/**
 * Archivo de conexión a la base de datos
 * 
 * Este archivo establece la conexión con la base de datos MySQL y proporciona
 * el objeto de conexión para ser utilizado en toda la aplicación.
 * 
 * @author Proyecto TFG
 * @version 1.0
 */

// Parámetros de conexión a la base de datos
$host = "localhost";
$user = "root";
$password = ""; 
$database = "tienda_online";

/**
 * Establece la conexión a la base de datos utilizando mysqli
 * 
 * @global object $conn Objeto de conexión a la base de datos
 * @throws Exception Si la conexión falla
 */
try {
    $conn = new mysqli($host, $user, $password, $database);

    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . $conn->connect_error);
    }
    
    // Establecer conjunto de caracteres
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // Manejo de errores de conexión
    die("❌ " . $e->getMessage());
}
?>
