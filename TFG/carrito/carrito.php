<?php
session_start();
include 'config.php';

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Devolver el contenido del carrito
    header('Content-Type: application/json');
    echo json_encode($_SESSION['carrito']);
} elseif ($method === 'POST') {
    // Agregar un producto al carrito
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['id']) || !isset($input['cantidad'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit;
    }
    
    $id = intval($input['id']);
    $cantidad = intval($input['cantidad']);
    
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
    
    // Agregar al carrito
    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id]['cantidad'] += $cantidad;
    } else {
        $_SESSION['carrito'][$id] = [
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'cantidad' => $cantidad
        ];
    }
    
    echo json_encode(["mensaje" => "Producto agregado al carrito"]);
} elseif ($method === 'DELETE') {
    // Vaciar el carrito
    $_SESSION['carrito'] = [];
    echo json_encode(["mensaje" => "Carrito vaciado"]);
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
?>
