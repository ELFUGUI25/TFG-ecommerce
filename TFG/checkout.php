<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    http_response_code(400);
    echo json_encode(["error" => "El carrito está vacío"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    http_response_code(401);
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit;
}

// Crear un nuevo pedido
$query = "INSERT INTO pedidos (usuario_id, fecha) VALUES (?, NOW())";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$pedido_id = mysqli_insert_id($conn);

// Insertar los productos del carrito en los detalles del pedido
foreach ($_SESSION['carrito'] as $producto) {
    $query = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iiid", $pedido_id, $producto['id'], $producto['cantidad'], $producto['precio']);
    mysqli_stmt_execute($stmt);
}

// Vaciar el carrito después de la compra
$_SESSION['carrito'] = [];

echo json_encode(["mensaje" => "Pedido realizado con éxito", "pedido_id" => $pedido_id]);
?>
