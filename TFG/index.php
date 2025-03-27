<?php
session_start();
include 'config.php'; // Archivo para la conexión a la base de datos

$query = "SELECT * FROM productos";
$result = mysqli_query($conn, $query);
$productos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $productos[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre'],
        'precio' => $row['precio'],
        'imagen' => $row['imagen']
    ];
}

header('Content-Type: application/json');
echo json_encode($productos);
?>