<?php
require_once 'conexion.php';

$query = "SELECT id_producto, nombre, descripcion, precio, stock, imagen, tallas FROM productos ORDER BY nombre ASC";
$resultado = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <header class="top-bar">
    <h1>Mi Tienda Online</h1>
    <nav>
        <a href="bienvenida.php" class="nav-btn <?php echo basename($_SERVER['PHP_SELF']) == 'bienvenida.php' ? 'activo' : ''; ?>">Inicio</a>
        <a href="catalogo.php" class="nav-btn <?php echo basename($_SERVER['PHP_SELF']) == 'catalogo.php' ? 'activo' : ''; ?>">Catálogo</a>
        <a href="perfil.php" class="nav-btn <?php echo basename($_SERVER['PHP_SELF']) == 'perfil.php' ? 'activo' : ''; ?>">Mi Perfil</a>
        <a href="politicas.php" class="nav-btn <?php echo basename($_SERVER['PHP_SELF']) == 'politicas.php' ? 'activo' : ''; ?>">Políticas</a>
    </nav>
</header>
    <link rel="stylesheet" href="catalogo.css">
</head>
<body>

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
</body>
</html>
