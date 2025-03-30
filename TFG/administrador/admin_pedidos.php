<?php
session_start();
include 'config.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener todos los pedidos
    $query = "SELECT p.id, u.username, p.total, p.fecha, p.estado 
              FROM pedidos p 
              JOIN usuarios u ON p.usuario_id = u.id 
              ORDER BY p.fecha DESC";
    $result = mysqli_query($conn, $query);
    $pedidos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pedidos[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($pedidos);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar estado del pedido
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['pedido_id']) || !isset($input['estado'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit;
    }

    $pedido_id = intval($input['pedido_id']);
    $estado = mysqli_real_escape_string($conn, $input['estado']);
    $query = "UPDATE pedidos SET estado = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $estado, $pedido_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["mensaje" => "Estado del pedido actualizado"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar el estado"]);
    }
}
?>
