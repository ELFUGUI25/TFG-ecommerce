<?php
/**
 * Editar producto existente
 * 
 * Esta página permite editar un producto existente en la tienda.
 * 
 * @author Proyecto TFG
 * @version 1.0
 */

// Incluir archivos necesarios
require_once '../../includes/config.php';
require_once '../../includes/conexion.php';
require_once '../../includes/utilidades.php';
require_once '../../includes/mensajes.php';
require_once '../../includes/validacion.php';
require_once '../../includes/login_admin.php';
require_once '../../includes/admin_funciones.php';

// Verificar que el usuario sea administrador
requerir_admin('login.php');

$mensaje = "";
$tipo_mensaje = "";
$producto = null;

// Verificar que se ha proporcionado un ID de producto
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $mensaje = "ID de producto no válido.";
    $tipo_mensaje = "error";
} else {
    $id_producto = (int)$_GET['id'];
    $producto = obtener_producto_por_id($conn, $id_producto);
    
    if (!$producto) {
        $mensaje = "El producto solicitado no existe.";
        $tipo_mensaje = "error";
    }
}

// Procesar formulario de nueva categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_categoria_nombre'])) {
    try {
        $nueva_categoria_nombre = trim($_POST['nueva_categoria_nombre'] ?? '');
        $nueva_categoria_descripcion = trim($_POST['nueva_categoria_descripcion'] ?? '');

        if (empty($nueva_categoria_nombre)) {
            throw new Exception("El nombre de la nueva categoría es obligatorio.");
        }

        $resultado_categoria = agregar_categoria($conn, $nueva_categoria_nombre, $nueva_categoria_descripcion);

        if ($resultado_categoria['success']) {
            $mensaje = $resultado_categoria['message'];
            $tipo_mensaje = 'success';
        } else {
            throw new Exception($resultado_categoria['message']);
        }
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = 'error';
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $producto) {
    // Procesar formulario de edición de producto
    try {
        // Validar campos obligatorios
        $nombre = trim($_POST['nombre'] ?? '');
        $precio = trim($_POST['precio'] ?? '');
        $stock = trim($_POST['stock'] ?? '');
        $id_categoria = trim($_POST['id_categoria'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $imagen = trim($_POST['imagen'] ?? '');
        $tallas = trim($_POST['tallas'] ?? '');
        $destacado = isset($_POST['destacado']) ? 1 : 0;
        
        // Validaciones
        if (empty($nombre)) {
            throw new Exception("El nombre del producto es obligatorio.");
        }
        
        if (!is_numeric($precio) || floatval($precio) <= 0) {
            throw new Exception("El precio debe ser un número positivo.");
        }
        
        if (!is_numeric($stock) || intval($stock) < 0) {
            throw new Exception("El stock debe ser un número entero no negativo.");
        }
        
        if (empty($id_categoria) || !is_numeric($id_categoria)) {
            throw new Exception("Debes seleccionar una categoría válida.");
        }
        
        // Preparar datos del producto
        $datos_producto = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => floatval($precio),
            'stock' => intval($stock),
            'imagen' => $imagen,
            'tallas' => $tallas,
            'id_categoria' => intval($id_categoria),
            'destacado' => $destacado
        ];
        
        // Actualizar producto
        $resultado = actualizar_producto($conn, $id_producto, $datos_producto);
        
        if ($resultado['success']) {
            $mensaje = $resultado['message'];
            $tipo_mensaje = 'success';
            
            // Actualizar datos del producto en la vista
            $producto = obtener_producto_por_id($conn, $id_producto);
        } else {
            throw new Exception($resultado['message']);
        }
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = 'error';
    }
}

// Obtener categorías para el selector (se recargan después de añadir una nueva)
$categorias = obtener_categorias($conn);

// Variables para el header
$titulo = "Editar Producto - Panel de Administración";
$css_adicional = "../../css/admin.css";
$pagina_actual = "admin_editar_producto";
?>

<?php include '../../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Mi Tienda Online</title>
    <link rel="stylesheet" href="../../css/admin_editar_producto.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="admin-header-top">
        <div class="admin-header-container">
            <div class="admin-logo">
                <i class="fas fa-cogs"></i>
                <h1>Panel de Administración</h1>
            </div>
            <nav class="admin-nav">
                <a href="panel.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="admin_productos.php" class="nav-link active">
                    <i class="fas fa-box"></i> Productos
                </a>
                <a href="admin_stock.php" class="nav-link">
                    <i class="fas fa-warehouse"></i> Stock
                </a>
                <a href="../logout.php" class="nav-link logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </nav>
        </div>
    </header>

<main class="admin-container">
    <div class="admin-header">
        <h1>Editar Producto</h1>
        <p>Modifica los datos del producto seleccionado.</p>
        
        <div class="admin-actions">
            <a href="admin_productos.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Volver a Productos
            </a>
        </div>
    </div>
    
    <?php if (!empty($mensaje)): ?>
        <?php echo mostrar_mensaje($mensaje, $tipo_mensaje); ?>
    <?php endif; ?>
    
    <?php if ($producto): ?>
        <div class="admin-form-container">
            <form method="POST" action="admin_editar_producto.php?id=<?php echo $id_producto; ?>" class="admin-form">
                <div class="form-group">
                    <label for="nombre">Nombre del producto *</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="precio">Precio (€) *</label>
                        <input type="number" id="precio" name="precio" step="0.01" min="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stock *</label>
                        <input type="number" id="stock" name="stock" min="0" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="imagen">URL de la imagen</label>
                    <input type="text" id="imagen" name="imagen" value="<?php echo htmlspecialchars($producto['imagen']); ?>">
                    <?php if (!empty($producto['imagen'])): ?>
                        <div class="imagen-preview">
                            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Vista previa">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="tallas">Tallas disponibles</label>
                    <input type="text" id="tallas" name="tallas" value="<?php echo htmlspecialchars($producto['tallas']); ?>">
                    <small>Ejemplo: S, M, L, XL</small>
                </div>
                
                <div class="form-group">
                    <label for="id_categoria">Categoría *</label>
                    <select id="id_categoria" name="id_categoria" required>
                        <option value="">Selecciona una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id_categoria']; ?>" <?php echo ($producto['id_categoria'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="destacado" name="destacado" <?php echo ($producto['destacado'] == 1) ? 'checked' : ''; ?>>
                    <label for="destacado">Producto destacado</label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <a href="admin_productos.php" class="btn btn-outline">Cancelar</a>
                </div>
            </form>

            <hr class="form-divider">

            <h2>Añadir Nueva Categoría</h2>
            <form method="POST" action="admin_editar_producto.php?id=<?php echo $id_producto; ?>" class="admin-form">
                <div class="form-group">
                    <label for="nueva_categoria_nombre">Nombre de la Categoría *</label>
                    <input type="text" id="nueva_categoria_nombre" name="nueva_categoria_nombre" required>
                </div>
                <div class="form-group">
                    <label for="nueva_categoria_descripcion">Descripción (Opcional)</label>
                    <textarea id="nueva_categoria_descripcion" name="nueva_categoria_descripcion" rows="2"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-secondary">Crear Categoría</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <footer class="admin-footer">
        <div class="admin-footer-container">
            <div class="footer-left">
                <p>&copy; 2025 Mi Tienda Online - Panel de Administración</p>
                <p>Sistema de gestión empresarial</p>
            </div>
            <div class="footer-right">
                <div class="footer-stats">
                    <span class="stat-item">
                        <i class="fas fa-shield-alt"></i>
                        Sesión segura
                    </span>
                    <span class="stat-item">
                        <i class="fas fa-clock"></i>
                        <?php echo date('H:i'); ?>
                    </span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>


