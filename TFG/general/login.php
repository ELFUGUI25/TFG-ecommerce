<?php
session_start();
include 'conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST["correo"]);
    $contrasena = $_POST["contrasena"];

    if (empty($correo) || empty($contrasena)) {
        $mensaje = "Por favor, completa todos los campos.";
    } else {
        $stmt = $conexion->prepare("SELECT id, contrasena FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hash);
            $stmt->fetch();

            if (password_verify($contrasena, $hash)) {
                $_SESSION["usuario_id"] = $id;
                header("Location: web.html");
                exit;
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "Correo no registrado.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?php echo $mensaje; ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
        <p class="registro-link">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
