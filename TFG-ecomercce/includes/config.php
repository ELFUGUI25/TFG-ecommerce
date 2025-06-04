<?php
/**
 * Configuración global de la aplicación
 * 
 * Este archivo contiene constantes y configuraciones globales
 * que se utilizan en toda la aplicación.
 * 
 * @author Proyecto TFG
 * @version 1.0
 */

// Configuración de la aplicación
define('APP_NAME', 'Mi Tienda Online');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/tienda_online');

// Configuración de rutas
define('BASE_PATH', dirname(__DIR__));
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('CSS_PATH', BASE_PATH . '/css');
define('JS_PATH', BASE_PATH . '/js');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('LOGS_PATH', BASE_PATH . '/logs');

// Configuración de errores
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', LOGS_PATH . '/php_errors.log');

// Asegurarse de que exista el directorio de logs
if (!is_dir(LOGS_PATH)) {
    mkdir(LOGS_PATH, 0755, true);
}

// Zona horaria
date_default_timezone_set('Europe/Madrid');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS

// Función para cargar automáticamente los archivos de includes
function cargar_includes($archivo) {
    $archivos_base = [
        'conexion.php',
        'utilidades.php',
        'mensajes.php',
        'validacion.php',
        'carrito_funciones.php'
    ];
    
    if (in_array($archivo, $archivos_base)) {
        require_once INCLUDES_PATH . '/' . $archivo;
    }
}

// Registrar función de autocarga
spl_autoload_register('cargar_includes');
?>
