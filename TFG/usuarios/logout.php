<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    session_unset();
    session_destroy();
    echo json_encode(["mensaje" => "Sesión cerrada exitosamente"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "No hay sesión activa"]);
}
?>