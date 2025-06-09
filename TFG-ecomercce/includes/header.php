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
$usuario_logueado = isset($_SESSION["id_usuario"]);
$es_admin = ($usuario_logueado && isset($_SESSION["rol"]) && $_SESSION["rol"] === "admin");

// Determinar si la página actual es parte del panel de administración
$es_pagina_admin = (strpos($_SERVER["REQUEST_URI"], "/admin/") !== false);

// Determinar si la página actual es login.php o registro.php
$nombre_pagina_actual = basename($_SERVER["PHP_SELF"]);
$es_pagina_especial = ($nombre_pagina_actual === "login.php" || $nombre_pagina_actual === "registro.php");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? htmlspecialchars($titulo) : "Mi Tienda Online"; ?></title>
    <link rel="stylesheet" href="../css/common.css">
    <?php if (isset($css_adicional) && !empty($css_adicional)): ?>
        <link rel="stylesheet" href="<?php echo $css_adicional; ?>">
    <?php endif; ?>
</head>
<body>
    <header class="top-bar">
        <h1>Mi Tienda Online</h1>
        <?php
        if ($es_pagina_especial) {
            // No navigation for login.php and registro.php
        } elseif ($es_pagina_admin) {
            // If it's an admin page, only show admin panel button if admin
            if ($es_admin) {
                echo '<nav><a href="panel.php" class="nav-btn">Panel de Administración</a></nav>';
            }
        } else {
            // Regular store pages (not admin, not special)
            echo '<nav>';
            echo '<a href="bienvenida.php" class="nav-btn ' . ((isset($pagina_actual) && $pagina_actual === "bienvenida") ? "activo" : "") . '">Inicio</a>';
            echo '<a href="catalogo.php" class="nav-btn ' . ((isset($pagina_actual) && $pagina_actual === "catalogo") ? "activo" : "") . '">Catálogo</a>';
            echo '<a href="carrito.php" class="nav-btn ' . ((isset($pagina_actual) && $pagina_actual === "carrito") ? "activo" : "") . '">Mi Carrito</a>';
            echo '<a href="perfil.php" class="nav-btn ' . ((isset($pagina_actual) && $pagina_actual === "perfil") ? "activo" : "") . '">Mi Perfil</a>';
            echo '<a href="politicas.php" class="nav-btn ' . ((isset($pagina_actual) && $pagina_actual === "politicas") ? "activo" : "") . '">Políticas</a>';
            if ($es_admin) {
                // If admin, also show admin panel button
                echo '<a href="../pages/admin/panel.php" class="nav-btn">Panel de Administración</a>';
            }
            echo '</nav>';
        }
        ?>
    </header>
