<?php
session_start();
require_once '../includes/conexion.php';
require_once '../includes/carrito_funciones.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$mensaje = "";
$tipo_mensaje = "";

// Procesar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
        
        if ($accion === 'actualizar' && isset($_POST['id_producto']) && isset($_POST['cantidad'])) {
            $id_producto = $_POST['id_producto'];
            $cantidad = (int)$_POST['cantidad'];
            
            $resultado = actualizarCantidadCarrito($conn, $id_usuario, $id_producto, $cantidad);
            $mensaje = $resultado['message'];
            $tipo_mensaje = $resultado['success'] ? 'success' : 'error';
        } 
        elseif ($accion === 'eliminar' && isset($_POST['id_producto'])) {
            $id_producto = $_POST['id_producto'];
            
            $resultado = eliminarDelCarrito($conn, $id_usuario, $id_producto);
            $mensaje = $resultado['message'];
            $tipo_mensaje = $resultado['success'] ? 'success' : 'error';
        }
        elseif ($accion === 'vaciar') {
            $resultado = vaciarCarrito($conn, $id_usuario);
            $mensaje = $resultado['message'];
            $tipo_mensaje = $resultado['success'] ? 'success' : 'error';
        }
    }
}

// Obtener contenido del carrito
$carrito = obtenerContenidoCarrito($conn, $id_usuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Carrito - Mi Tienda Online</title>
    <link rel="stylesheet" href="../css/carrito.css">
</head>
<body>
    <header class="top-bar">
        <h1>Mi Tienda Online</h1>
        <nav>
            <a href="bienvenida.php" class="nav-btn">Inicio</a>
            <a href="catalogo.php" class="nav-btn">Catálogo</a>
            <a href="carrito.php" class="nav-btn activo">Mi Carrito</a>
            <a href="perfil.php" class="nav-btn">Mi Perfil</a>
            <a href="politicas.php" class="nav-btn">Políticas</a>
        </nav>
    </header>

    <main class="carrito-container">
        <h2>Mi Carrito de Compras</h2>
        
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <?php if (count($carrito['items']) > 0): ?>
            <div class="carrito-resumen">
                <p>Tienes <strong><?php echo count($carrito['items']); ?></strong> productos en tu carrito</p>
                <p>Total: <strong>€<?php echo number_format($carrito['total'], 2); ?></strong></p>
            </div>
            
            <div class="carrito-acciones">
                <form method="POST" action="carrito.php">
                    <input type="hidden" name="accion" value="vaciar">
                    <button type="submit" class="btn-vaciar">Vaciar Carrito</button>
                </form>
                <a href="finalizar_compra.php" class="btn-comprar">Finalizar Compra</a>
            </div>
            
            <div class="carrito-items">
                <?php foreach ($carrito['items'] as $item): ?>
                    <div class="carrito-item">
                        <div class="item-imagen">
                            <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                        </div>
                        
                        <div class="item-detalles">
                            <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                            <p class="item-precio">€<?php echo number_format($item['precio_unitario'], 2); ?> por unidad</p>
                            <p class="item-stock">Disponible: <?php echo $item['stock']; ?> unidades</p>
                        </div>
                        
                        <div class="item-cantidad">
                            <form method="POST" action="carrito.php" class="form-cantidad">
                                <input type="hidden" name="accion" value="actualizar">
                                <input type="hidden" name="id_producto" value="<?php echo $item['id_producto']; ?>">
                                <label for="cantidad-<?php echo $item['id_producto']; ?>">Cantidad:</label>
                                <input type="number" id="cantidad-<?php echo $item['id_producto']; ?>" name="cantidad" 
                                       value="<?php echo $item['cantidad']; ?>" min="1" max="<?php echo $item['stock']; ?>" 
                                       class="input-cantidad">
                                <button type="submit" class="btn-actualizar">Actualizar</button>
                            </form>
                        </div>
                        
                        <div class="item-subtotal">
                            <p>Subtotal:</p>
                            <p class="precio-subtotal">€<?php echo number_format($item['subtotal'], 2); ?></p>
                        </div>
                        
                        <div class="item-acciones">
                            <form method="POST" action="carrito.php">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id_producto" value="<?php echo $item['id_producto']; ?>">
                                <button type="submit" class="btn-eliminar">Eliminar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="carrito-total">
                <h3>Resumen del Pedido</h3>
                <div class="total-linea">
                    <span>Subtotal:</span>
                    <span>€<?php echo number_format($carrito['total'], 2); ?></span>
                </div>
                <div class="total-linea">
                    <span>Envío:</span>
                    <span>Gratis</span>
                </div>
                <div class="total-linea total-final">
                    <span>Total:</span>
                    <span>€<?php echo number_format($carrito['total'], 2); ?></span>
                </div>
                <a href="finalizar_compra.php" class="btn-comprar btn-grande">Finalizar Compra</a>
            </div>
        <?php else: ?>
            <div class="carrito-vacio">
                <p>Tu carrito está vacío</p>
                <a href="catalogo.php" class="btn-seguir-comprando">Seguir Comprando</a>
            </div>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Tienda Online - Proyecto TFG</p>
    </footer>
</body>
</html>
