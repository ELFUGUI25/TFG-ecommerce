<?php
require_once '../otros/conexion.php';

// Consulta simplificada para obtener productos
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
            <a href="perfil.php" class="nav-btn">Mi Perfil</a>
            <a href="politicas.php" class="nav-btn">Políticas</a>
        </nav>
    </header>

    <main class="catalogo">
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
