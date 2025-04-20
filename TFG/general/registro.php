<?php
include 'conexion.php';

$errores = "";
$exito = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $contrasena = $_POST["contrasena"];
    $confirmar = $_POST["confirmar"];

    // Validaciones básicas
    if (empty($nombre) || empty($correo) || empty($contrasena) || empty($confirmar)) {
        $errores = "Por favor, completa todos los campos.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores = "Correo no válido.";
    } elseif ($contrasena !== $confirmar) {
        $errores = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el correo ya existe
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errores = "Este correo ya está registrado.";
        } else {
            // Encriptar contraseña
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Insertar usuario
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $correo, $hash);

            if ($stmt->execute()) {
                $exito = "✅ Registro exitoso. Ya puedes iniciar sesión.";
            } else {
                $errores = "Error al registrar. Intenta de nuevo.";
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="registro.css">
</head>
<body>
    <div class="form-container">
        <h2>Registro de Usuario</h2>

        <?php if ($errores): ?>
            <div class="error"><?= $errores ?></div>
        <?php elseif ($exito): ?>
            <div class="exito"><?= $exito ?></div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <input type="password" name="confirmar" placeholder="Confirmar contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
    </div>
</body>
</html>
