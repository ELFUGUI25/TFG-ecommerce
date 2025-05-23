<?php
require_once '../includes/conexion.php';
require_once '../includes/carrito_funciones.php';

// Verificar si el usuario está logueado
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$mensaje = "";
$tipo_mensaje = "";

// Procesar acción de agregar al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $id_producto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
    
    if ($id_producto > 0) {
        $resultado = agregarAlCarrito($conn, $id_usuario, $id_producto, $cantidad);
        $mensaje = $resultado['message'];
        $tipo_mensaje = $resultado['success'] ? 'success' : 'error';
    }
}

// Consulta para obtener productos
$query = "SELECT id_producto, nombre, descripcion, precio, stock, imagen, tallas FROM productos ORDER BY nombre ASC";
$resultado = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - Mi Tienda Online</title>
    <link rel="stylesheet" href="../css/catalogo.css">
</head>
<body>
    <header class="top-bar">
        <h1>Mi Tienda Online</h1>
        <nav>
            <a href="bienvenida.php" class="nav-btn">Inicio</a>
            <a href="catalogo.php" class="nav-btn activo">Catálogo</a>
            <a href="carrito.php" class="nav-btn">Mi Carrito</a>
            <a href="perfil.php" class="nav-btn">Mi Perfil</a>
            <a href="politicas.php" class="nav-btn">Políticas</a>
        </nav>
    </header>

    <main class="catalogo">
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <div class="productos-grid">
                <?php while ($producto = $resultado->fetch_assoc()): ?>
                    <article class="producto-card">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="producto-imagen">
                        <h2 class="producto-nombre"><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                        <p class="producto-precio">€<?php echo number_format($producto['precio'], 2); ?></p>
                        <p class="producto-descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <p class="producto-tallas"><strong>Tallas disponibles:</strong> 
                            <?php echo !empty($producto['tallas']) ? htmlspecialchars($producto['tallas']) : 'No disponibles'; ?>
                        </p>
                        <p class="producto-cantidad"><?php echo $producto['stock'] > 0 ? "Disponible: " . $producto['stock'] : "Agotado"; ?></p>
                        
                        <?php if ($producto['stock'] > 0): ?>
                            <form method="POST" action="catalogo.php" class="form-agregar">
                                <input type="hidden" name="accion" value="agregar">
                                <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                                <div class="cantidad-container">
                                    <label for="cantidad-<?php echo $producto['id_producto']; ?>">Cantidad:</label>
                                    <input type="number" id="cantidad-<?php echo $producto['id_producto']; ?>" name="cantidad" 
                                           value="1" min="1" max="<?php echo $producto['stock']; ?>" class="input-cantidad">
                                </div>
                                <button type="submit" class="btn-agregar">Agregar al Carrito</button>
                            </form>
                        <?php else: ?>
                            <button class="btn-agotado" disabled>Agotado</button>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No hay productos disponibles en este momento.</p>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Tienda Online - Proyecto TFG</p>
    </footer>
</body>
</html>
