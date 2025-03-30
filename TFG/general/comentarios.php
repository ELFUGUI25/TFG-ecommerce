<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Agregar un nuevo comentario
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(["error" => "Debes iniciar sesión para comentar"]);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['producto_id'], $input['comentario'], $input['valoracion'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit;
    }
    
    $producto_id = intval($input['producto_id']);
    $usuario_id = $_SESSION['user_id'];
    $comentario = mysqli_real_escape_string($conn, $input['comentario']);
    $valoracion = intval($input['valoracion']);
    
    if ($valoracion < 1 || $valoracion > 5) {
        http_response_code(400);
        echo json_encode(["error" => "La valoración debe estar entre 1 y 5"]);
        exit;
    }
    
    $query = "INSERT INTO comentarios (producto_id, usuario_id, comentario, valoracion, fecha) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iisi", $producto_id, $usuario_id, $comentario, $valoracion);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["mensaje" => "Comentario agregado con éxito"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al agregar comentario"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener comentarios de un producto
    if (!isset($_GET['producto_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit;
    }
    
    $producto_id = intval($_GET['producto_id']);
    $query = "SELECT c.id, u.username, c.comentario, c.valoracion, c.fecha FROM comentarios c
              JOIN usuarios u ON c.usuario_id = u.id
              WHERE c.producto_id = ? ORDER BY c.fecha DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $producto_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $comentarios = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comentarios[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($comentarios);
}
?>
