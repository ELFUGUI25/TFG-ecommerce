<?php
session_start();
require_once '../includes/conexion.php';

$mensaje = "";

// Procesar formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"]);
    $contrasena = trim($_POST["contrasena"]);

    if (!empty($usuario) && !empty($contrasena)) {
        // Consulta preparada para evitar inyección SQL
        $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario, correo, contrasena FROM usuarios WHERE correo = ? OR nombre_usuario = ?");
        $stmt->bind_param("ss", $usuario, $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            
            // Verificar contraseña
            if (password_verify($contrasena, $fila["contrasena"])) {
                // Iniciar sesión
                $_SESSION["correo"] = $fila["correo"];
                $_SESSION["id_usuario"] = $fila["id_usuario"];
                $_SESSION["nombre_usuario"] = $fila["nombre_usuario"];
                header("Location: bienvenida.php");
                exit();
            } else {
                $mensaje = "❌ El usuario o la contraseña son incorrectos.";
            }
        } else {
            $mensaje = "❌ El usuario o la contraseña son incorrectos.";
        }
        $stmt->close();
    } else {
        $mensaje = "⚠️ Por favor, completa todos los campos.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Mi Tienda Online</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <header>
        <h1>Iniciar Sesión</h1>
        <p>Accede con tu nombre de usuario o correo electrónico.</p>
    </header>

    <main>
        <form action="login.php" method="POST" class="formulario">
            <input type="text" name="usuario" placeholder="Correo o Nombre de Usuario" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit" class="boton">Entrar</button>
        </form>
        <?php if ($mensaje): ?>
            <p style="margin-top: 1rem;"><?php echo $mensaje; ?></p>
        <?php endif; ?>
        <p><a href="web.php" style="color:rgb(247, 184, 135)">← Volver al inicio</a></p>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Tienda Online - Proyecto TFG</p>
    </footer>
</body>
</html>
