<?php
session_start();
require_once "../includes/conexion.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";
$id_usuario = $_SESSION['id_usuario'];

// Obtener datos actuales del usuario
$sql = "SELECT nombre_usuario, correo FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();
} else {
    $mensaje = "No se encontró el usuario.";
}

// Procesar el formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_nombre = $_POST['nombre_usuario'];
    $nuevo_correo = $_POST['correo'];
    $nueva_contrasena = $_POST['contrasena'];

    // Actualizar perfil con o sin cambio de contraseña
    if (!empty($nueva_contrasena)) {
        $hash_contrasena = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $sql_update = "UPDATE usuarios SET nombre_usuario = ?, correo = ?, contrasena = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssi", $nuevo_nombre, $nuevo_correo, $hash_contrasena, $id_usuario);
    } else {
        $sql_update = "UPDATE usuarios SET nombre_usuario = ?, correo = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssi", $nuevo_nombre, $nuevo_correo, $id_usuario);
    }

    if ($stmt->execute()) {
        $_SESSION['nombre_usuario'] = $nuevo_nombre;
        $_SESSION['correo'] = $nuevo_correo;
        $mensaje = "Perfil actualizado correctamente.";
    } else {
        $mensaje = "Error al actualizar el perfil.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="../css/perfil.css">
</head>
<body>
    <div class="top-bar">
        <h1>Mi Perfil</h1>
        <nav>
            <a href="bienvenida.php" class="nav-btn">Inicio</a>
            <a href="catalogo.php" class="nav-btn">Catálogo</a>
            <a href="carrito.php" class="nav-btn">Mi Carrito</a>
            <a href="mis_pedidos.php" class="nav-btn">Mis Pedidos</a>
            <a href="../includes/logout.php" class="nav-btn">Cerrar sesión</a>
        </nav>
    </div>
    
    <main>
        <form method="POST" class="formulario">
            <label>Nombre de usuario:</label>
            <input type="text" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario['nombre_usuario']); ?>" required>

            <label>Correo:</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>

            <label>Nueva contraseña (opcional):</label>
            <input type="password" name="contrasena" placeholder="Deja en blanco para no cambiarla">

            <button type="submit" class="boton">Guardar cambios</button>
        </form>

        <?php if ($mensaje): ?>
            <p class="mensaje"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <div class="acciones-usuario">
            <a href="bienvenida.php" class="boton-accion">Volver al inicio</a>
            <a href="carrito.php" class="boton-accion boton-carrito">Ver mi carrito</a>
            <a href="mis_pedidos.php" class="boton-accion boton-pedidos">Ver mis pedidos</a>
            <a href="../includes/logout.php" class="boton-accion boton-salir">Cerrar sesión</a>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Tienda Online</p>
    </footer>
</body>
</html>
