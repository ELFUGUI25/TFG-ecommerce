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
    // Obtener todos los usuarios
    $query = "SELECT id, username, email, rol FROM usuarios ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    $usuarios = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $usuarios[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($usuarios);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Editar un usuario
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['id'], $input['username'], $input['email'], $input['rol'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit;
    }

    $id = intval($input['id']);
    $username = mysqli_real_escape_string($conn, $input['username']);
    $email = mysqli_real_escape_string($conn, $input['email']);
    $rol = mysqli_real_escape_string($conn, $input['rol']);

    $query = "UPDATE usuarios SET username = ?, email = ?, rol = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $rol, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["mensaje" => "Usuario actualizado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar el usuario"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Eliminar un usuario
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit;
    }

    $id = intval($input['id']);
    $query = "DELETE FROM usuarios WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["mensaje" => "Usuario eliminado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar el usuario"]);
    }
}
?>
