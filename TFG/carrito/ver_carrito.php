<?php
session_start();
include 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Debes iniciar sesión para ver tu carrito"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT c.id, p.nombre, p.precio, c.cantidad, (p.precio * c.cantidad) AS total 
          FROM carrito c 
          JOIN productos p ON c.producto_id = p.id 
          WHERE c.usuario_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$carrito = [];
while ($row = mysqli_fetch_assoc($result)) {
    $carrito[] = $row;
}

header('Content-Type: application/json');
echo json_encode($carrito);
?>
