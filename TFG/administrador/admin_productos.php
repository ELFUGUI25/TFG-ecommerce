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
    // Obtener todos los productos
    $query = "SELECT * FROM productos ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    $productos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $productos[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($productos);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Agregar un nuevo producto
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['nombre'], $input['precio'], $input['descripcion'], $input['imagen'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit;
    }

    $nombre = mysqli_real_escape_string($conn, $input['nombre']);
    $precio = floatval($input['precio']);
    $descripcion = mysqli_real_escape_string($conn, $input['descripcion']);
    $imagen = mysqli_real_escape_string($conn, $input['imagen']);

    $query = "INSERT INTO productos (nombre, precio, descripcion, imagen) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sdss", $nombre, $precio, $descripcion, $imagen);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["mensaje" => "Producto agregado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al agregar el producto"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Editar un producto existente
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['id'], $input['nombre'], $input['precio'], $input['descripcion'], $input['imagen'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit;
    }

    $id = intval($input['id']);
    $nombre = mysqli_real_escape_string($conn, $input['nombre']);
    $precio = floatval($input['precio']);
    $descripcion = mysqli_real_escape_string($conn, $input['descripcion']);
    $imagen = mysqli_real_escape_string($conn, $input['imagen']);

    $query = "UPDATE productos SET nombre = ?, precio = ?, descripcion = ?, imagen = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sdssi", $nombre, $precio, $descripcion, $imagen, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["mensaje" => "Producto actualizado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar el producto"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Eliminar un producto
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit;
    }

    $id = intval($input['id']);
    $query = "DELETE FROM productos WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["mensaje" => "Producto eliminado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar el producto"]);
    }
}
?>
