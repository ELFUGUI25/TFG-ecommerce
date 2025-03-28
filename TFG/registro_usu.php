<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['nombre']) || !isset($input['email']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

$nombre = trim($input['nombre']);
$email = trim($input['email']);
$password = password_hash($input['password'], PASSWORD_DEFAULT);

$query = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "sss", $nombre, $email, $password);
if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["mensaje" => "Usuario registrado con éxito"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al registrar el usuario"]);
}
?>
