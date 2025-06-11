<?php
/**
 * index.php
 * 
 * Punto de entrada principal de la aplicación web.
 * Redirige al usuario a la página de bienvenida si ha iniciado sesión,
 * o a la página de inicio de sesión si no lo ha hecho.
 */

session_start();

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION["nombre_usuario"])) {
    // Si el usuario ha iniciado sesión, redirigir a la página de bienvenida
    header("Location: pages/bienvenida.php");
    exit();
} else {
    // Si el usuario no ha iniciado sesión, redirigir a la página de login
    header("Location: pages/login.php");
    exit();
}

?>
