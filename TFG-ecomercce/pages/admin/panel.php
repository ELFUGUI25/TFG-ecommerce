<?php
/**
 * Panel de administración principal
 * 
 * Esta página muestra el panel principal de administración con acceso
 * a todas las funcionalidades de gestión de productos.
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

// Variables para el header
$titulo = "Panel de Administración - Mi Tienda Online";
$css_adicional = "../css/admin.css";
$pagina_actual = "admin_panel";
?>

<?php include '../includes/header.php'; ?>

<main class="admin-container">
    <div class="admin-header">
        <h1>Panel de Administración</h1>
        <p>Bienvenido al panel de administración. Desde aquí puedes gestionar los productos de la tienda.</p>
    </div>

    <div class="admin-menu">
        <a href="admin_productos.php" class="admin-menu-item">
            <i class="fas fa-box"></i>
            <span>Gestionar Productos</span>
        </a>
        <a href="admin_agregar_producto.php" class="admin-menu-item">
            <i class="fas fa-plus-circle"></i>
            <span>Añadir Nuevo Producto</span>
        </a>
        <a href="admin_stock.php" class="admin-menu-item">
            <i class="fas fa-warehouse"></i>
            <span>Gestionar Stock</span>
        </a>
        <a href="bienvenida.php" class="admin-menu-item">
            <i class="fas fa-store"></i>
            <span>Ver Tienda</span>
        </a>
    </div>

    <div class="admin-stats">
        <h2>Resumen</h2>
        
        <?php
        // Obtener estadísticas básicas
        $total_productos = 0;
        $productos_sin_stock = 0;
        
        try {
            $query = "SELECT COUNT(*) as total FROM productos";
            $resultado = $conn->query($query);
            if ($resultado && $fila = $resultado->fetch_assoc()) {
                $total_productos = $fila['total'];
            }
            
            $query = "SELECT COUNT(*) as sin_stock FROM productos WHERE stock = 0";
            $resultado = $conn->query($query);
            if ($resultado && $fila = $resultado->fetch_assoc()) {
                $productos_sin_stock = $fila['sin_stock'];
            }
        } catch (Exception $e) {
            registrar_error("Error al obtener estadísticas: " . $e->getMessage());
        }
        ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_productos; ?></div>
                <div class="stat-label">Productos totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $productos_sin_stock; ?></div>
                <div class="stat-label">Productos sin stock</div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
