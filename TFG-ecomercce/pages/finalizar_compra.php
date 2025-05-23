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

// Obtener contenido del carrito
$carrito = obtenerContenidoCarrito($conn, $id_usuario);

// Verificar si hay productos en el carrito
if (count($carrito['items']) === 0) {
    header("Location: carrito.php");
    exit();
}

// Procesar el formulario de finalización de compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $direccion_envio = trim($_POST['direccion']);
    $metodo_pago = trim($_POST['metodo_pago']);
    
    if (empty($direccion_envio)) {
        $mensaje = "Por favor, ingresa una dirección de envío válida.";
        $tipo_mensaje = "error";
    } else {
        // Procesar la compra
        $resultado = procesarCompra($conn, $id_usuario, $direccion_envio, $metodo_pago);
        
        if ($resultado['success']) {
            // Redirigir a la página de confirmación
            header("Location: confirmacion_compra.php?id_pedido=" . $resultado['id_pedido']);
            exit();
        } else {
            $mensaje = $resultado['message'];
            $tipo_mensaje = "error";
        }
    }
}

// Obtener información del usuario para prellenar el formulario
$query = "SELECT nombre, apellidos, direccion, telefono FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra - Mi Tienda Online</title>
    <link rel="stylesheet" href="../css/finalizar_compra.css">
</head>
<body>
    <header class="top-bar">
        <h1>Mi Tienda Online</h1>
        <nav>
            <a href="bienvenida.php" class="nav-btn">Inicio</a>
            <a href="catalogo.php" class="nav-btn">Catálogo</a>
            <a href="carrito.php" class="nav-btn">Mi Carrito</a>
            <a href="perfil.php" class="nav-btn">Mi Perfil</a>
            <a href="politicas.php" class="nav-btn">Políticas</a>
        </nav>
    </header>

    <main class="finalizar-compra-container">
        <h2>Finalizar Compra</h2>
        
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <div class="finalizar-compra-grid">
            <div class="datos-envio">
                <h3>Datos de Envío</h3>
                <form method="POST" action="finalizar_compra.php" class="form-finalizar">
                    <div class="form-grupo">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo isset($usuario['nombre']) ? htmlspecialchars($usuario['nombre']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="apellidos">Apellidos:</label>
                        <input type="text" id="apellidos" name="apellidos" value="<?php echo isset($usuario['apellidos']) ? htmlspecialchars($usuario['apellidos']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="direccion">Dirección de Envío:</label>
                        <textarea id="direccion" name="direccion" required><?php echo isset($usuario['direccion']) ? htmlspecialchars($usuario['direccion']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="telefono">Teléfono de Contacto:</label>
                        <input type="tel" id="telefono" name="telefono" value="<?php echo isset($usuario['telefono']) ? htmlspecialchars($usuario['telefono']) : ''; ?>" required>
                    </div>
                    
                    <h3>Método de Pago</h3>
                    <div class="form-grupo metodos-pago">
                        <div class="metodo-pago">
                            <input type="radio" id="tarjeta" name="metodo_pago" value="tarjeta" checked>
                            <label for="tarjeta">Tarjeta de Crédito/Débito</label>
                        </div>
                        
                        <div class="metodo-pago">
                            <input type="radio" id="paypal" name="metodo_pago" value="paypal">
                            <label for="paypal">PayPal</label>
                        </div>
                        
                        <div class="metodo-pago">
                            <input type="radio" id="transferencia" name="metodo_pago" value="transferencia">
                            <label for="transferencia">Transferencia Bancaria</label>
                        </div>
                    </div>
                    
                    <div class="form-acciones">
                        <a href="carrito.php" class="btn-volver">Volver al Carrito</a>
                        <button type="submit" class="btn-confirmar">Confirmar Pedido</button>
                    </div>
                </form>
            </div>
            
            <div class="resumen-pedido">
                <h3>Resumen del Pedido</h3>
                
                <div class="resumen-items">
                    <?php foreach ($carrito['items'] as $item): ?>
                        <div class="resumen-item">
                            <div class="item-info">
                                <span class="item-cantidad"><?php echo $item['cantidad']; ?>x</span>
                                <span class="item-nombre"><?php echo htmlspecialchars($item['nombre']); ?></span>
                            </div>
                            <span class="item-precio">€<?php echo number_format($item['subtotal'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="resumen-total">
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
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Tienda Online - Proyecto TFG</p>
    </footer>
</body>
</html>
