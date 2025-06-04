<?php
/**
 * Header común para todas las páginas
 * 
 * Este archivo contiene el encabezado común que se utiliza en todas las páginas
 * de la aplicación, incluyendo la barra de navegación y los elementos comunes del head.
 * 
 * @param string $titulo Título de la página
 * @param string $css_adicional Ruta al archivo CSS adicional específico de la página
 * @param string $pagina_actual Nombre de la página actual para resaltar en la navegación
 */

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay un usuario logueado
$usuario_logueado = isset($_SESSION['id_usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? htmlspecialchars($titulo) : 'Mi Tienda Online'; ?></title>
    <link rel="stylesheet" href="../css/common.css">
    <?php if (isset($css_adicional) && !empty($css_adicional)): ?>
        <link rel="stylesheet" href="<?php echo $css_adicional; ?>">
    <?php endif; ?>
</head>
<body>
    <header class="top-bar">
        <h1>Mi Tienda Online</h1>
        <?php if ($usuario_logueado): ?>
        <nav>
            <a href="bienvenida.php" class="nav-btn <?php echo (isset($pagina_actual) && $pagina_actual === 'bienvenida') ? 'activo' : ''; ?>">Inicio</a>
            <a href="catalogo.php" class="nav-btn <?php echo (isset($pagina_actual) && $pagina_actual === 'catalogo') ? 'activo' : ''; ?>">Catálogo</a>
            <a href="carrito.php" class="nav-btn <?php echo (isset($pagina_actual) && $pagina_actual === 'carrito') ? 'activo' : ''; ?>">Mi Carrito</a>
            <a href="perfil.php" class="nav-btn <?php echo (isset($pagina_actual) && $pagina_actual === 'perfil') ? 'activo' : ''; ?>">Mi Perfil</a>
            <a href="politicas.php" class="nav-btn <?php echo (isset($pagina_actual) && $pagina_actual === 'politicas') ? 'activo' : ''; ?>">Políticas</a>
        </nav>
        <?php endif; ?>
    </header>
