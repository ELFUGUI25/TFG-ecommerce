<?php
/**
 * Sistema de mensajes para la aplicación
 * 
 * Este archivo contiene funciones para mostrar mensajes de éxito, error, advertencia e información
 * de manera consistente en toda la aplicación.
 * 
 * @param string $mensaje Texto del mensaje a mostrar
 * @param string $tipo Tipo de mensaje: 'success', 'error', 'warning', 'info'
 * @return string HTML formateado para mostrar el mensaje
 */

/**
 * Muestra un mensaje con formato según su tipo
 * 
 * @param string $mensaje Texto del mensaje
 * @param string $tipo Tipo de mensaje ('success', 'error', 'warning', 'info')
 * @return string HTML del mensaje formateado
 */
function mostrar_mensaje($mensaje, $tipo = 'info') {
    if (empty($mensaje)) {
        return '';
    }
    
    // Iconos para cada tipo de mensaje
    $iconos = [
        'success' => '✅',
        'error' => '❌',
        'warning' => '⚠️',
        'info' => 'ℹ️'
    ];
    
    $icono = isset($iconos[$tipo]) ? $iconos[$tipo] : $iconos['info'];
    
    return '<div class="mensaje ' . htmlspecialchars($tipo) . '">' . $icono . ' ' . htmlspecialchars($mensaje) . '</div>';
}

/**
 * Muestra un mensaje de éxito
 * 
 * @param string $mensaje Texto del mensaje
 * @return string HTML del mensaje de éxito
 */
function mensaje_exito($mensaje) {
    return mostrar_mensaje($mensaje, 'success');
}

/**
 * Muestra un mensaje de error
 * 
 * @param string $mensaje Texto del mensaje
 * @return string HTML del mensaje de error
 */
function mensaje_error($mensaje) {
    return mostrar_mensaje($mensaje, 'error');
}

/**
 * Muestra un mensaje de advertencia
 * 
 * @param string $mensaje Texto del mensaje
 * @return string HTML del mensaje de advertencia
 */
function mensaje_advertencia($mensaje) {
    return mostrar_mensaje($mensaje, 'warning');
}

/**
 * Muestra un mensaje informativo
 * 
 * @param string $mensaje Texto del mensaje
 * @return string HTML del mensaje informativo
 */
function mensaje_info($mensaje) {
    return mostrar_mensaje($mensaje, 'info');
}
