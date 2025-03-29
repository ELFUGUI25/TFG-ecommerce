<?php
session_start();
include 'config.php';

// Obtener productos destacados
$query = "SELECT * FROM productos ORDER BY RAND() LIMIT 4";
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

// Devolver datos en formato JSON
header('Content-Type: application/json');
echo json_encode($productos);
?>