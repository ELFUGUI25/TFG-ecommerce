<?php
/**
 * Gestión de productos - Listado y eliminación
 * 
 * Esta página muestra todos los productos y permite eliminarlos.
 * 
 * @author Proyecto TFG
 * @version 1.0
 */

// Incluir archivos necesarios
require_once '../../includes/config.php';
require_once '../../includes/conexion.php';
require_once '../../includes/utilidades.php';
require_once '../../includes/mensajes.php';
require_once '../../includes/login_admin.php';
require_once '../../includes/admin_funciones.php';

// Verificar que el usuario sea administrador
requerir_admin('login.php');

$mensaje = "";
$tipo_mensaje = "";

// Procesar eliminación de producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    if (isset($_POST['id_producto'])) {
        $id_producto = (int)$_POST['id_producto'];
        
        $resultado = eliminar_producto($conn, $id_producto);
        $mensaje = $resultado['message'];
        $tipo_mensaje = $resultado['success'] ? 'success' : 'error';
    }
}

// Obtener todos los productos
$productos = obtener_productos($conn);

// Variables para el header
$titulo = "Gestionar Productos - Panel de Administración";
$css_adicional = "../../css/admin.css";
$pagina_actual = "admin_productos";
?>

<?php include '../../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda Online</title>
    <link rel="stylesheet" href="../../css/admin_productos.css">
</head>

<main class="admin-container">
    <div class="admin-header">
        <h1>Gestionar Productos</h1>
        <p>Desde aquí puedes ver, editar y eliminar los productos de la tienda.</p>
        
        <div class="admin-actions">
            <a href="admin_agregar_producto.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Añadir Nuevo Producto
            </a>
            <a href="admin_stock.php" class="btn btn-secondary">
                <i class="fas fa-warehouse"></i> Gestionar Stock
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
        <div class="productos-tabla">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo $producto['id_producto']; ?></td>
                            <td>
                                <?php if (!empty($producto['imagen'])): ?>
                                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="producto-miniatura">
                                <?php else: ?>
                                    <span class="no-imagen">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo formatear_precio($producto['precio']); ?></td>
                            <td class="<?php echo $producto['stock'] <= 0 ? 'stock-agotado' : ''; ?>">
                                <?php echo $producto['stock']; ?>
                            </td>
                            <td><?php echo $producto['id_categoria']; ?></td>
                            <td class="acciones">
                                <a href="admin_editar_producto.php?id=<?php echo $producto['id_producto']; ?>" class="btn-accion btn-editar" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="admin_productos.php" class="form-eliminar" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                                    <button type="submit" class="btn-accion btn-eliminar" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php include '../../includes/footer.php'; ?>
