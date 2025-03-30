<?php
session_start();
include 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Debes iniciar sesión para realizar una compra"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT c.producto_id, p.precio, c.cantidad, (p.precio * c.cantidad) AS total 
          FROM carrito c 
          JOIN productos p ON c.producto_id = p.id 
          WHERE c.usuario_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$productos = [];
$total_pagar = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $productos[] = $row;
    $total_pagar += $row['total'];
}

if (empty($productos)) {
    http_response_code(400);
    echo json_encode(["error" => "No tienes productos en el carrito"]);
    exit;
}

// Insertar pedido
$query = "INSERT INTO pedidos (usuario_id, total, fecha) VALUES (?, ?, NOW())";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "id", $usuario_id, $total_pagar);
mysqli_stmt_execute($stmt);
$pedido_id = mysqli_insert_id($conn);

// Insertar detalles del pedido
foreach ($productos as $producto) {
    $query = "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iiid", $pedido_id, $producto['producto_id'], $producto['cantidad'], $producto['precio']);
    mysqli_stmt_execute($stmt);
}

// Vaciar carrito
$query = "DELETE FROM carrito WHERE usuario_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);

echo json_encode(["mensaje" => "Pedido realizado con éxito", "pedido_id" => $pedido_id]);
?>