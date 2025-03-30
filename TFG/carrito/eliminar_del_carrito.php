<?php
session_start();
include 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Debes iniciar sesión para modificar tu carrito"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['producto_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Falta el ID del producto"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$producto_id = intval($input['producto_id']);

$query = "DELETE FROM carrito WHERE usuario_id = ? AND producto_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $usuario_id, $producto_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["mensaje" => "Producto eliminado del carrito"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al eliminar el producto del carrito"]);
}
?>
