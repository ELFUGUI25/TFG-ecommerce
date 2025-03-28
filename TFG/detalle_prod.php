<?php
session_start();
include 'config.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID de producto no especificado"]);
    exit;
}

$id = intval($_GET['id']);
$query = "SELECT * FROM productos WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($result);

if (!$producto) {
    http_response_code(404);
    echo json_encode(["error" => "Producto no encontrado"]);
    exit;
}

header('Content-Type: application/json');
echo json_encode($producto);
?>
