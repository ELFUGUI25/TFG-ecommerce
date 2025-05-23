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

// Verificar si se recibió un ID de pedido
if (!isset($_GET['id_pedido'])) {
    header("Location: carrito.php");
    exit();
}

$id_pedido = (int)$_GET['id_pedido'];

// Obtener información del pedido
$query = "SELECT p.*, u.nombre, u.apellidos, u.correo 
          FROM pedidos p 
          JOIN usuarios u ON p.id_usuario = u.id_usuario 
          WHERE p.id_pedido = ? AND p.id_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id_pedido, $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: carrito.php");
    exit();
}

$pedido = $resultado->fetch_assoc();

// Obtener items del pedido
$query = "SELECT pi.*, p.nombre, p.imagen 
          FROM pedido_items pi 
          JOIN productos p ON pi.id_producto = p.id_producto 
          WHERE pi.id_pedido = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$resultado_items = $stmt->get_result();
$items = [];

while ($item = $resultado_items->fetch_assoc()) {
    $items[] = $item;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Compra - Mi Tienda Online</title>
    <link rel="stylesheet" href="../css/confirmacion_compra.css">
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

    <main class="confirmacion-container">
        <div class="confirmacion-header">
            <h2>¡Pedido Confirmado!</h2>
            <p class="mensaje-exito">Tu pedido ha sido procesado correctamente.</p>
        </div>
        
        <div class="confirmacion-detalles">
            <div class="detalles-pedido">
                <h3>Detalles del Pedido</h3>
                <p><strong>Número de Pedido:</strong> #<?php echo $id_pedido; ?></p>
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                <p><strong>Estado:</strong> <span class="estado-pedido"><?php echo ucfirst($pedido['estado']); ?></span></p>
                <p><strong>Método de Pago:</strong> <?php echo ucfirst($pedido['metodo_pago']); ?></p>
                <p><strong>Total:</strong> €<?php echo number_format($pedido['total'], 2); ?></p>
            </div>
            
            <div class="detalles-envio">
                <h3>Datos de Envío</h3>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellidos']); ?></p>
                <p><strong>Dirección:</strong> <?php echo nl2br(htmlspecialchars($pedido['direccion_envio'])); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido['correo']); ?></p>
            </div>
        </div>
        
        <div class="confirmacion-productos">
            <h3>Productos Adquiridos</h3>
            <div class="productos-lista">
                <?php foreach ($items as $item): ?>
                    <div class="producto-item">
                        <div class="producto-imagen">
                            <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                        </div>
                        <div class="producto-info">
                            <h4><?php echo htmlspecialchars($item['nombre']); ?></h4>
                            <p>Cantidad: <?php echo $item['cantidad']; ?></p>
                            <p>Precio unitario: €<?php echo number_format($item['precio_unitario'], 2); ?></p>
                        </div>
                        <div class="producto-total">
                            €<?php echo number_format($item['cantidad'] * $item['precio_unitario'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="resumen-total">
                <div class="total-linea">
                    <span>Subtotal:</span>
                    <span>€<?php echo number_format($pedido['total'], 2); ?></span>
                </div>
                <div class="total-linea">
                    <span>Envío:</span>
                    <span>Gratis</span>
                </div>
                <div class="total-linea total-final">
                    <span>Total:</span>
                    <span>€<?php echo number_format($pedido['total'], 2); ?></span>
                </div>
            </div>
        </div>
        
        <div class="confirmacion-acciones">
            <p>Recibirás un correo electrónico con los detalles de tu pedido.</p>
            <div class="botones">
                <a href="catalogo.php" class="btn-seguir-comprando">Seguir Comprando</a>
                <a href="perfil.php" class="btn-ver-pedidos">Ver Mis Pedidos</a>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Tienda Online - Proyecto TFG</p>
    </footer>
</body>
</html>
