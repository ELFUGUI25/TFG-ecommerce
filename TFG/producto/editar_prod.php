<?php
session_start();
include 'config.php';

// Verificar si el usuario tiene permisos de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['id'], $input['nombre'], $input['descripcion'], $input['precio'], $input['imagen'])) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

$id = intval($input['id']);
$nombre = trim($input['nombre']);
$descripcion = trim($input['descripcion']);
$precio = floatval($input['precio']);
$imagen = trim($input['imagen']);

$query = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, imagen = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssdsi", $nombre, $descripcion, $precio, $imagen, $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["mensaje" => "Producto actualizado correctamente"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al actualizar el producto"]);
}
?>