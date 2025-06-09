<?php
/**
 * Funciones para la autenticación de administradores
 * 
 * Este archivo contiene las funciones necesarias para la autenticación
 * y gestión de sesiones de administradores.
 * 
 * @author Proyecto TFG
 * @version 1.1
 */

/**
 * Verifica si el usuario tiene rol de administrador en la base de datos
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario a verificar
 * @return bool True si el usuario tiene rol de administrador, false en caso contrario
 */
function verificar_rol_admin($conn, $id_usuario) {
    // Consulta preparada para verificar el rol del usuario
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id_usuario = ?");
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();
        return ($fila["rol"] === "admin");
    }
    
    return false;
}

/**
 * Verifica si las credenciales corresponden a un administrador
 * 
 * @param string $usuario Nombre de usuario
 * @param string $contrasena Contraseña
 * @param object $conn Conexión a la base de datos
 * @return array|bool Array con datos del usuario si es admin, false en caso contrario
 */
function verificar_admin($usuario, $contrasena, $conn = null) {
    // Si no se proporciona conexión, usar las credenciales fijas como fallback
    if ($conn === null) {
        // Credenciales fijas para el administrador (mantener por compatibilidad)
        $admin_usuario = 'adminasir';
        $admin_contrasena = 'admin';
        
        return ($usuario === $admin_usuario && $contrasena === $admin_contrasena);
    }
    
    // Verificar en la base de datos
    $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario, rol FROM usuarios WHERE (correo = ? OR nombre_usuario = ?) AND rol = 'admin'");
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("ss", $usuario, $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $usuario_data = $resultado->fetch_assoc();
        $stmt->close();
        
        // Ahora verificar la contraseña
        $stmt = $conn->prepare("SELECT contrasena FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $usuario_data['id_usuario']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        
        if (password_verify($contrasena, $fila["contrasena"])) {
            return $usuario_data;
        }
    }
    
    return false;
}

/**
 * Establece la sesión de administrador
 * 
 * @param string $nombre_usuario Nombre del usuario administrador
 * @param int $id_usuario ID del usuario administrador
 * @return void
 */
function establecer_sesion_admin($nombre_usuario = 'Administrador', $id_usuario = 0) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['es_admin'] = true;
    $_SESSION['nombre_usuario'] = $nombre_usuario;
    $_SESSION['id_usuario'] = $id_usuario;
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
