<?php
session_start();
require_once '../includes/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener todos los pedidos del usuario
$query = "SELECT id_pedido, fecha_pedido, estado, total FROM pedidos 
          WHERE id_usuario = ? 
          ORDER BY fecha_pedido DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

// Obtener pedidos pendientes (no entregados ni cancelados)
$query_pendientes = "SELECT id_pedido, fecha_pedido, estado, total FROM pedidos 
                    WHERE id_usuario = ? AND estado NOT IN ('entregado', 'cancelado') 
                    ORDER BY fecha_pedido DESC";
$stmt_pendientes = $conn->prepare($query_pendientes);
$stmt_pendientes->bind_param("i", $id_usuario);
$stmt_pendientes->execute();
$resultado_pendientes = $stmt_pendientes->get_result();

// Obtener pedidos completados (entregados o cancelados)
$query_completados = "SELECT id_pedido, fecha_pedido, estado, total FROM pedidos 
                     WHERE id_usuario = ? AND estado IN ('entregado', 'cancelado') 
                     ORDER BY fecha_pedido DESC";
$stmt_completados = $conn->prepare($query_completados);
$stmt_completados->bind_param("i", $id_usuario);
$stmt_completados->execute();
$resultado_completados = $stmt_completados->get_result();

// Obtener detalles de un pedido específico si se solicita
$detalles_pedido = null;
$items_pedido = [];

if (isset($_GET['id_pedido'])) {
    $id_pedido = (int)$_GET['id_pedido'];
    
    // Verificar que el pedido pertenezca al usuario
    $query_verificar = "SELECT * FROM pedidos WHERE id_pedido = ? AND id_usuario = ?";
    $stmt_verificar = $conn->prepare($query_verificar);
    $stmt_verificar->bind_param("ii", $id_pedido, $id_usuario);
    $stmt_verificar->execute();
    $resultado_verificar = $stmt_verificar->get_result();
    
    if ($resultado_verificar->num_rows > 0) {
        $detalles_pedido = $resultado_verificar->fetch_assoc();
        
        // Obtener items del pedido
        $query_items = "SELECT pi.*, p.nombre, p.imagen 
                       FROM pedido_items pi 
                       JOIN productos p ON pi.id_producto = p.id_producto 
                       WHERE pi.id_pedido = ?";
        $stmt_items = $conn->prepare($query_items);
        $stmt_items->bind_param("i", $id_pedido);
        $stmt_items->execute();
        $resultado_items = $stmt_items->get_result();
        
        while ($item = $resultado_items->fetch_assoc()) {
            $items_pedido[] = $item;
        }
    } else {
        // Redirigir si el pedido no pertenece al usuario
        header("Location: mis_pedidos.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Pedidos - Mi Tienda Online</title>
    <link rel="stylesheet" href="../css/mis_pedidos.css">
</head>
<body>
    <header class="top-bar">
        <h1>Mi Tienda Online</h1>
        <nav>
            <a href="bienvenida.php" class="nav-btn">Inicio</a>
            <a href="catalogo.php" class="nav-btn">Catálogo</a>
            <a href="carrito.php" class="nav-btn">Mi Carrito</a>
            <a href="mis_pedidos.php" class="nav-btn activo">Mis Pedidos</a>
            <a href="perfil.php" class="nav-btn">Mi Perfil</a>
            <a href="politicas.php" class="nav-btn">Políticas</a>
        </nav>
    </header>

    <main class="pedidos-container">
        <?php if ($detalles_pedido): ?>
            <!-- Vista de detalles de un pedido específico -->
            <div class="pedido-detalle">
                <div class="detalle-header">
                    <h2>Detalles del Pedido #<?php echo $detalles_pedido['id_pedido']; ?></h2>
                    <a href="mis_pedidos.php" class="btn-volver">← Volver a Mis Pedidos</a>
                </div>
                
                <div class="detalle-info">
                    <div class="info-basica">
                        <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($detalles_pedido['fecha_pedido'])); ?></p>
                        <p><strong>Estado:</strong> <span class="estado estado-<?php echo $detalles_pedido['estado']; ?>"><?php echo ucfirst($detalles_pedido['estado']); ?></span></p>
                        <p><strong>Total:</strong> €<?php echo number_format($detalles_pedido['total'], 2); ?></p>
                        <p><strong>Método de Pago:</strong> <?php echo isset($detalles_pedido['metodo_pago']) ? ucfirst($detalles_pedido['metodo_pago']) : 'No especificado'; ?></p>
                    </div>
                    
                    <div class="info-envio">
                        <h3>Dirección de Envío</h3>
                        <p><?php echo nl2br(htmlspecialchars($detalles_pedido['direccion_envio'])); ?></p>
                    </div>
                </div>
                
                <div class="detalle-productos">
                    <h3>Productos</h3>
                    <?php if (count($items_pedido) > 0): ?>
                        <div class="productos-lista">
                            <?php foreach ($items_pedido as $item): ?>
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
                        
                        <div class="detalle-total">
                            <div class="total-linea">
                                <span>Subtotal:</span>
                                <span>€<?php echo number_format($detalles_pedido['total'], 2); ?></span>
                            </div>
                            <div class="total-linea">
                                <span>Envío:</span>
                                <span>Gratis</span>
                            </div>
                            <div class="total-linea total-final">
                                <span>Total:</span>
                                <span>€<?php echo number_format($detalles_pedido['total'], 2); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="no-items">No hay productos en este pedido.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Vista de lista de pedidos -->
            <h2>Mis Pedidos</h2>
            
            <div class="pedidos-tabs">
                <button class="tab-btn active" data-tab="pendientes">Pedidos Pendientes</button>
                <button class="tab-btn" data-tab="completados">Pedidos Completados</button>
                <button class="tab-btn" data-tab="todos">Todos los Pedidos</button>
            </div>
            
            <div class="tab-content active" id="pendientes">
                <h3>Pedidos Pendientes</h3>
                <?php if ($resultado_pendientes->num_rows > 0): ?>
                    <div class="pedidos-lista">
                        <?php while ($pedido = $resultado_pendientes->fetch_assoc()): ?>
                            <div class="pedido-card">
                                <div class="pedido-header">
                                    <h4>Pedido #<?php echo $pedido['id_pedido']; ?></h4>
                                    <span class="estado estado-<?php echo $pedido['estado']; ?>"><?php echo ucfirst($pedido['estado']); ?></span>
                                </div>
                                <div class="pedido-info">
                                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                                    <p><strong>Total:</strong> €<?php echo number_format($pedido['total'], 2); ?></p>
                                </div>
                                <div class="pedido-acciones">
                                    <a href="mis_pedidos.php?id_pedido=<?php echo $pedido['id_pedido']; ?>" class="btn-ver-detalles">Ver Detalles</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="no-pedidos">No tienes pedidos pendientes.</p>
                <?php endif; ?>
            </div>
            
            <div class="tab-content" id="completados">
                <h3>Pedidos Completados</h3>
                <?php if ($resultado_completados->num_rows > 0): ?>
                    <div class="pedidos-lista">
                        <?php while ($pedido = $resultado_completados->fetch_assoc()): ?>
                            <div class="pedido-card">
                                <div class="pedido-header">
                                    <h4>Pedido #<?php echo $pedido['id_pedido']; ?></h4>
                                    <span class="estado estado-<?php echo $pedido['estado']; ?>"><?php echo ucfirst($pedido['estado']); ?></span>
                                </div>
                                <div class="pedido-info">
                                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                                    <p><strong>Total:</strong> €<?php echo number_format($pedido['total'], 2); ?></p>
                                </div>
                                <div class="pedido-acciones">
                                    <a href="mis_pedidos.php?id_pedido=<?php echo $pedido['id_pedido']; ?>" class="btn-ver-detalles">Ver Detalles</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="no-pedidos">No tienes pedidos completados.</p>
                <?php endif; ?>
            </div>
            
            <div class="tab-content" id="todos">
                <h3>Todos los Pedidos</h3>
                <?php if ($resultado->num_rows > 0): ?>
                    <div class="pedidos-lista">
                        <?php while ($pedido = $resultado->fetch_assoc()): ?>
                            <div class="pedido-card">
                                <div class="pedido-header">
                                    <h4>Pedido #<?php echo $pedido['id_pedido']; ?></h4>
                                    <span class="estado estado-<?php echo $pedido['estado']; ?>"><?php echo ucfirst($pedido['estado']); ?></span>
                                </div>
                                <div class="pedido-info">
                                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                                    <p><strong>Total:</strong> €<?php echo number_format($pedido['total'], 2); ?></p>
                                </div>
                                <div class="pedido-acciones">
                                    <a href="mis_pedidos.php?id_pedido=<?php echo $pedido['id_pedido']; ?>" class="btn-ver-detalles">Ver Detalles</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="no-pedidos">No has realizado ningún pedido todavía.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Tienda Online - Proyecto TFG</p>
    </footer>
    
    <script>
        // Script para manejar las pestañas
        document.addEventListener('DOMContentLoaded', function() {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remover clase active de todos los botones
                    tabBtns.forEach(b => b.classList.remove('active'));
                    // Añadir clase active al botón clickeado
                    this.classList.add('active');
                    
                    // Ocultar todos los contenidos
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Mostrar el contenido correspondiente
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>
