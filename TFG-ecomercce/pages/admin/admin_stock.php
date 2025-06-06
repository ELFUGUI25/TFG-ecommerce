<?php
/**
 * Gestión de stock de productos
 * 
 * Esta página permite actualizar rápidamente el stock de los productos.
 * 
 * @author Proyecto TFG
 * @version 1.0
 */

// Incluir archivos necesarios
require_once '../includes/config.php';
require_once '../includes/conexion.php';
require_once '../includes/utilidades.php';
require_once '../includes/mensajes.php';
require_once '../includes/validacion.php';
require_once '../includes/login_admin.php';
require_once '../includes/admin_funciones.php';

// Verificar que el usuario sea administrador
requerir_admin('login.php');

$mensaje = "";
$tipo_mensaje = "";

// Procesar actualización de stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_stock') {
    try {
        $id_producto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
        $nuevo_stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
        
        if ($id_producto <= 0) {
            throw new Exception("ID de producto no válido.");
        }
        
        if ($nuevo_stock < 0) {
            throw new Exception("El stock no puede ser negativo.");
        }
        
        $resultado = actualizar_stock_producto($conn, $id_producto, $nuevo_stock);
        
        if ($resultado['success']) {
            $mensaje = $resultado['message'];
            $tipo_mensaje = 'success';
        } else {
            throw new Exception($resultado['message']);
        }
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = 'error';
    }
}

// Obtener todos los productos
$productos = obtener_productos($conn);

// Variables para el header
$titulo = "Gestionar Stock - Panel de Administración";
$css_adicional = "../css/admin.css";
$pagina_actual = "admin_stock";
?>

<?php include '../includes/header.php'; ?>

<main class="admin-container">
    <div class="admin-header">
        <h1>Gestionar Stock</h1>
        <p>Actualiza rápidamente el stock de los productos de la tienda.</p>
        
        <div class="admin-actions">
            <a href="admin_productos.php" class="btn btn-secondary">
                <i class="fas fa-box"></i> Ver Todos los Productos
            </a>
            <a href="panel.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
        </div>
    </div>
    
    <?php if (!empty($mensaje)): ?>
        <?php echo mostrar_mensaje($mensaje, $tipo_mensaje); ?>
    <?php endif; ?>
    
    <?php if (empty($productos)): ?>
        <div class="mensaje info">
            No hay productos disponibles. ¡Añade algunos!
        </div>
    <?php else: ?>
        <div class="stock-tabla">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Stock Actual</th>
                        <th>Nuevo Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo $producto['id_producto']; ?></td>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td class="<?php echo $producto['stock'] <= 0 ? 'stock-agotado' : ''; ?>">
                                <?php echo $producto['stock']; ?>
                            </td>
                            <td>
                                <form method="POST" action="admin_stock.php" class="form-stock">
                                    <input type="hidden" name="accion" value="actualizar_stock">
                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                                    <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" min="0" class="input-stock">
                                    <button type="submit" class="btn-accion btn-actualizar" title="Actualizar">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <a href="admin_editar_producto.php?id=<?php echo $producto['id_producto']; ?>" class="btn-accion btn-editar" title="Editar producto completo">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
