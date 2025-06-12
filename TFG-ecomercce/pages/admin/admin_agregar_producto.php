<?php
/**
 * Añadir nuevo producto
 * 
 * Esta página permite añadir un nuevo producto a la tienda.
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
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar formulario de nuevo producto
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
        
        // Agregar producto
        $resultado = agregar_producto($conn, $datos_producto);
        
        if ($resultado['success']) {
            $mensaje = $resultado['message'];
            $tipo_mensaje = 'success';
            
            // Limpiar formulario después de éxito
            $nombre = $descripcion = $imagen = $tallas = '';
            $precio = $stock = $id_categoria = '';
            $destacado = 0;
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
$titulo = "Añadir Producto - Panel de Administración";
$css_adicional = "../../css/admin.css";
$pagina_actual = "admin_agregar_producto";
?>

<?php include '../../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda Online</title>
    <link rel="stylesheet" href="../../css/admin_agregar_producto.css">
</head>

<main class="admin-container">
    <div class="admin-header">
        <h1>Añadir Nuevo Producto</h1>
        <p>Completa el formulario para añadir un nuevo producto a la tienda.</p>
        
        <div class="admin-actions">
            <a href="admin_productos.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Volver a Productos
            </a>
        </div>
    </div>
    
    <?php if (!empty($mensaje)): ?>
        <?php echo mostrar_mensaje($mensaje, $tipo_mensaje); ?>
    <?php endif; ?>
    
    <div class="admin-form-container">
        <form method="POST" action="admin_agregar_producto.php" class="admin-form">
            <div class="form-group">
                <label for="nombre">Nombre del producto *</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"><?php echo isset($descripcion) ? htmlspecialchars($descripcion) : ''; ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="precio">Precio (€) *</label>
                    <input type="number" id="precio" name="precio" step="0.01" min="0.01" value="<?php echo isset($precio) ? htmlspecialchars($precio) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="stock">Stock *</label>
                    <input type="number" id="stock" name="stock" min="0" value="<?php echo isset($stock) ? htmlspecialchars($stock) : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="imagen">URL de la imagen</label>
                <input type="text" id="imagen" name="imagen" value="<?php echo isset($imagen) ? htmlspecialchars($imagen) : ''; ?>">
                <small>Introduce la URL completa de la imagen del producto</small>
            </div>
            
            <div class="form-group">
                <label for="tallas">Tallas disponibles</label>
                <input type="text" id="tallas" name="tallas" value="<?php echo isset($tallas) ? htmlspecialchars($tallas) : ''; ?>">
                <small>Ejemplo: S, M, L, XL</small>
            </div>
            
            <div class="form-group">
                <label for="id_categoria">Categoría *</label>
                <select id="id_categoria" name="id_categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id_categoria']; ?>" <?php echo (isset($id_categoria) && $id_categoria == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="destacado" name="destacado" <?php echo (isset($destacado) && $destacado) ? 'checked' : ''; ?>>
                <label for="destacado">Producto destacado</label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Producto</button>
                <button type="reset" class="btn btn-outline">Limpiar Formulario</button>
            </div>
        </form>

        <hr class="form-divider">

        <h2>Añadir Nueva Categoría</h2>
        <form method="POST" action="admin_agregar_producto.php" class="admin-form">
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
</main>

<?php include '../../includes/footer.php'; ?>


