<?php
// 1. Incluimos la conexión
include '../conexion.php'; // Ajusta la ruta si es distinta

// 2. Comprobamos si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 3. Recogemos y limpiamos los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar = $_POST['confirmar_contrasena'] ?? '';

    // 4. Validamos que no estén vacíos
    if (empty($nombre) || empty($correo) || empty($contrasena) || empty($confirmar_contrasena)) {
        echo "<script>alert('Por favor, rellena todos los campos.'); window.history.back();</script>";
        exit;
    }

    // 5. Validar que las contraseñas coincidan
    if ($contrasena !== $confirmar_contrasena) {
        echo "<script>alert('Las contraseñas no coinciden.'); window.history.back();</script>";
        exit;
    }

    // 6. Encriptamos la contraseña
    $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

    // 7. Insertamos en la base de datos
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $nombre, $correo, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Registro exitoso. ¡Bienvenido!'); window.location.href='../web.html';</script>";
        } else {
            echo "<script>alert('Error al registrar. Intenta más tarde.'); window.history.back();</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error de preparación en la base de datos.'); window.history.back();</script>";
    }

    $conexion->close();
} else {
    echo "<script>alert('Acceso no permitido.'); window.location.href='../web.html';</script>";
}
?>
