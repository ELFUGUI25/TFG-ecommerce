<?php
include 'config.php';

$query = "SELECT id, nombre, descripcion, precio, imagen FROM productos";
$result = mysqli_query($conn, $query);

$productos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $productos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($productos);
?>