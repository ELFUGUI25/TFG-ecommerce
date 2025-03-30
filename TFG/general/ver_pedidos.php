<?php
session_start();
include 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Debes iniciar sesión para ver tus pedidos"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT p.id AS pedido_id, p.total, p.fecha, dp.producto_id, prod.nombre, dp.cantidad, dp.precio_unitario 
          FROM pedidos p 
          JOIN detalle_pedido dp ON p.id = dp.pedido_id 
          JOIN productos prod ON dp.producto_id = prod.id 
          WHERE p.usuario_id = ? 
          ORDER BY p.fecha DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$pedidos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pedidos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($pedidos);
?>
