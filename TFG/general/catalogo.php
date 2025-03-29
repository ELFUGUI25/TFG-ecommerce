<?php
session_start();
include 'config.php';

// Obtener todos los productos del catálogo
$query = "SELECT * FROM productos";
$result = mysqli_query($conn, $query);
$productos = [];

while ($row = mysqli_fetch_assoc($result)) {
    $productos[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre'],
        'descripcion' => $row['descripcion'],
        'precio' => $row['precio'],
        'imagen' => $row['imagen'],
        'categoria' => $row['categoria']
    ];
}

// Devolver datos en formato JSON
header('Content-Type: application/json');
echo json_encode($productos);
?>
