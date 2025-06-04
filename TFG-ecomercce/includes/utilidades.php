<?php
/**
 * Funciones de utilidad general para la aplicación
 * 
 * Este archivo contiene funciones auxiliares que pueden ser utilizadas
 * en diferentes partes de la aplicación.
 * 
 * @author Proyecto TFG
 * @version 1.0
 */

/**
 * Redirecciona a una URL específica
 * 
 * @param string $url URL a la que redireccionar
 * @param bool $permanente Indica si la redirección es permanente (301) o temporal (302)
 * @return void
 */
function redireccionar($url, $permanente = false) {
    if ($permanente) {
        header('HTTP/1.1 301 Moved Permanently');
    }
    header('Location: ' . $url);
    exit();
}

/**
 * Limpia y sanitiza una cadena de texto para prevenir XSS
 * 
 * @param string $texto Texto a sanitizar
 * @return string Texto sanitizado
 */
function sanitizar_texto($texto) {
    return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
}

/**
 * Genera un token CSRF para proteger formularios
 * 
 * @return string Token CSRF generado
 */
function generar_csrf_token() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verifica si un token CSRF es válido
 * 
 * @param string $token Token a verificar
 * @return bool True si el token es válido, false en caso contrario
 */
function verificar_csrf_token($token) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    
    return true;
}

/**
 * Registra un error en el archivo de log
 * 
 * @param string $mensaje Mensaje de error
 * @param string $tipo Tipo de error (ERROR, WARNING, INFO)
 * @return void
 */
function registrar_error($mensaje, $tipo = 'ERROR') {
    $fecha = date('Y-m-d H:i:s');
    $log_mensaje = "[$fecha] [$tipo] $mensaje" . PHP_EOL;
    
    // Asegurarse de que el directorio de logs existe
    $log_dir = __DIR__ . '/../logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/app_errors.log';
    error_log($log_mensaje, 3, $log_file);
}

/**
 * Formatea un precio para mostrar
 * 
 * @param float $precio Precio a formatear
 * @param string $simbolo Símbolo de moneda
 * @return string Precio formateado
 */
function formatear_precio($precio, $simbolo = '€') {
    return $simbolo . number_format($precio, 2, ',', '.');
}

/**
 * Verifica si el usuario tiene una sesión activa
 * 
 * @return bool True si el usuario está logueado, false en caso contrario
 */
function usuario_logueado() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['id_usuario']);
}

/**
 * Verifica si una petición es AJAX
 * 
 * @return bool True si es una petición AJAX, false en caso contrario
 */
function es_peticion_ajax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>
