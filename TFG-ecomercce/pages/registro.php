<?php
require_once '../otros/conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $nombre_usuario = trim($_POST["nombre_usuario"]);
    $correo = trim($_POST["correo"]);
    $contrasena = trim($_POST["contrasena"]);

    if (!empty($nombre) && !empty($nombre_usuario) && !empty($correo) && !empty($contrasena)) {
        // Verificar si ya existe ese correo
        $check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $check->bind_param("s", $correo);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $mensaje = "⚠️ Ya existe una cuenta con ese correo.";
        } else {
            // Encriptar contraseña y registrar usuario
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, nombre_usuario, correo, contrasena) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $nombre_usuario, $correo, $contrasena_hash);

            if ($stmt->execute()) {
                $mensaje = "✅ Registro exitoso. Ya puedes iniciar sesión.";
            } else {
                $mensaje = "❌ Error al registrar: " . $stmt->error;
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Mi Tienda Online</title>
    <link rel="stylesheet" href="../css/registro.css">
</head>
<body>
    <header>
        <h1>Registro de Usuario</h1>
        <p>Crea tu cuenta para comenzar a comprar y disfrutar de recomendaciones personalizadas.</p>
    </header>

    <main>
        <form action="registro.php" method="POST" class="formulario">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" required>
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit" class="boton">Registrarse</button>
        </form>

        <?php if ($mensaje): ?>
            <p class="mensaje" style="margin-top: 1rem;"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <div class="botones-inferiores">
            <a href="web.php" class="btn-volver">← Volver al inicio</a>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Tienda Online - Proyecto TFG</p>
    </footer>
</body>
</html>
