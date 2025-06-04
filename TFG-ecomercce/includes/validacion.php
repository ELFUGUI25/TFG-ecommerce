<?php
/**
 * Funciones de validación para formularios
 * 
 * Este archivo contiene funciones para validar datos de entrada en formularios
 * y proporcionar mensajes de error consistentes.
 */

/**
 * Valida que un campo no esté vacío
 * 
 * @param string $valor Valor a validar
 * @param string $nombre_campo Nombre del campo para el mensaje de error
 * @return array ['valido' => bool, 'mensaje' => string]
 */
function validar_campo_requerido($valor, $nombre_campo) {
    $valor = trim($valor);
    if (empty($valor)) {
        return [
            'valido' => false,
            'mensaje' => "El campo {$nombre_campo} es obligatorio."
        ];
    }
    return ['valido' => true, 'mensaje' => ''];
}

/**
 * Valida un correo electrónico
 * 
 * @param string $correo Correo a validar
 * @return array ['valido' => bool, 'mensaje' => string]
 */
function validar_correo($correo) {
    $correo = trim($correo);
    
    // Primero verificar que no esté vacío
    $validacion = validar_campo_requerido($correo, 'correo electrónico');
    if (!$validacion['valido']) {
        return $validacion;
    }
    
    // Validar formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        return [
            'valido' => false,
            'mensaje' => 'El formato del correo electrónico no es válido.'
        ];
    }
    
    return ['valido' => true, 'mensaje' => ''];
}

/**
 * Valida una contraseña
 * 
 * @param string $contrasena Contraseña a validar
 * @param int $min_longitud Longitud mínima requerida
 * @return array ['valido' => bool, 'mensaje' => string]
 */
function validar_contrasena($contrasena, $min_longitud = 6) {
    // Verificar que no esté vacía
    $validacion = validar_campo_requerido($contrasena, 'contraseña');
    if (!$validacion['valido']) {
        return $validacion;
    }
    
    // Verificar longitud mínima
    if (strlen($contrasena) < $min_longitud) {
        return [
            'valido' => false,
            'mensaje' => "La contraseña debe tener al menos {$min_longitud} caracteres."
        ];
    }
    
    return ['valido' => true, 'mensaje' => ''];
}

/**
 * Valida un número entero positivo
 * 
 * @param mixed $valor Valor a validar
 * @param string $nombre_campo Nombre del campo para el mensaje de error
 * @return array ['valido' => bool, 'mensaje' => string]
 */
function validar_entero_positivo($valor, $nombre_campo) {
    // Verificar que sea un número entero positivo
    if (!is_numeric($valor) || intval($valor) != $valor || $valor <= 0) {
        return [
            'valido' => false,
            'mensaje' => "El campo {$nombre_campo} debe ser un número entero positivo."
        ];
    }
    
    return ['valido' => true, 'mensaje' => ''];
}

/**
 * Valida un número decimal positivo
 * 
 * @param mixed $valor Valor a validar
 * @param string $nombre_campo Nombre del campo para el mensaje de error
 * @return array ['valido' => bool, 'mensaje' => string]
 */
function validar_decimal_positivo($valor, $nombre_campo) {
    // Verificar que sea un número decimal positivo
    if (!is_numeric($valor) || floatval($valor) <= 0) {
        return [
            'valido' => false,
            'mensaje' => "El campo {$nombre_campo} debe ser un número positivo."
        ];
    }
    
    return ['valido' => true, 'mensaje' => ''];
}
