<?php
/**
 * Funciones para la autenticación de administradores
 * 
 * Este archivo contiene las funciones necesarias para la autenticación
 * y gestión de sesiones de administradores.
 * 
 * @author Proyecto TFG
 * @version 1.0
 */

/**
 * Verifica si las credenciales corresponden a un administrador
 * 
 * @param string $usuario Nombre de usuario
 * @param string $contrasena Contraseña
 * @return bool True si las credenciales son de administrador, false en caso contrario
 */
function verificar_admin($usuario, $contrasena) {
    // Credenciales fijas para el administrador
    $admin_usuario = 'adminasir';
    $admin_contrasena = 'admin';
    
    return ($usuario === $admin_usuario && $contrasena === $admin_contrasena);
}

/**
 * Establece la sesión de administrador
 * 
 * @return void
 */
function establecer_sesion_admin() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['es_admin'] = true;
    $_SESSION['nombre_usuario'] = 'Administrador';
    $_SESSION['id_usuario'] = 0; // ID especial para administrador
}

/**
 * Verifica si el usuario actual tiene permisos de administrador
 * 
 * @return bool True si el usuario es administrador, false en caso contrario
 */
function es_administrador() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;
}

/**
 * Protege una página para que solo sea accesible por administradores
 * Si el usuario no es administrador, redirige a la página de login
 * 
 * @param string $redirect_url URL a la que redirigir si no es administrador
 * @return void
 */
function requerir_admin($redirect_url = 'login.php') {
    if (!es_administrador()) {
        header("Location: $redirect_url");
        exit();
    }
}
?>
