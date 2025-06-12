<?php
/**
 * Funciones para la administración de productos
 * 
 * Este archivo contiene todas las funciones necesarias para gestionar productos
 * desde el panel de administración.
 * 
 * @author Proyecto TFG
 * @version 1.0
 */

/**
 * Obtiene todos los productos de la base de datos
 * 
 * @param object $conn Conexión a la base de datos
 * @return array Lista de productos
 */
function obtener_productos($conn) {
    try {
        $query = "SELECT id_producto, nombre, descripcion, precio, stock, imagen, tallas, id_categoria, destacado 
                 FROM productos ORDER BY nombre ASC";
        $resultado = $conn->query($query);
        
        $productos = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($producto = $resultado->fetch_assoc()) {
                $productos[] = $producto;
            }
        }
        
        return $productos;
    } catch (Exception $e) {
        registrar_error("Error al obtener productos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene un producto por su ID
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_producto ID del producto
 * @return array|null Datos del producto o null si no existe
 */
function obtener_producto_por_id($conn, $id_producto) {
    try {
        $query = "SELECT id_producto, nombre, descripcion, precio, stock, imagen, tallas, id_categoria, destacado 
                 FROM productos WHERE id_producto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado && $resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        
        return null;
    } catch (Exception $e) {
        registrar_error("Error al obtener producto por ID: " . $e->getMessage());
        return null;
    }
}

/**
 * Añade un nuevo producto a la base de datos
 * 
 * @param object $conn Conexión a la base de datos
 * @param array $datos_producto Datos del producto a añadir
 * @return array Resultado de la operación con claves 'success' y 'message'
 */
function agregar_producto($conn, $datos_producto) {
    try {
        // Validar datos obligatorios
        $campos_requeridos = ['nombre', 'precio', 'stock', 'id_categoria'];
        foreach ($campos_requeridos as $campo) {
            if (!isset($datos_producto[$campo]) || empty($datos_producto[$campo])) {
                return [
                    'success' => false,
                    'message' => "El campo {$campo} es obligatorio."
                ];
            }
        }
        
        // Validar que precio y stock sean números positivos
        if (!is_numeric($datos_producto['precio']) || floatval($datos_producto['precio']) <= 0) {
            return [
                'success' => false,
                'message' => "El precio debe ser un número positivo."
            ];
        }
        
        if (!is_numeric($datos_producto['stock']) || intval($datos_producto['stock']) < 0) {
            return [
                'success' => false,
                'message' => "El stock debe ser un número entero no negativo."
            ];
        }
        
        // Preparar valores por defecto para campos opcionales
        $descripcion = isset($datos_producto['descripcion']) ? $datos_producto['descripcion'] : '';
        $imagen = isset($datos_producto['imagen']) ? $datos_producto['imagen'] : '';
        $tallas = isset($datos_producto['tallas']) ? $datos_producto['tallas'] : '';
        $destacado = isset($datos_producto['destacado']) ? (int)$datos_producto['destacado'] : 0;
        
        // Insertar producto
        $query = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen, tallas, id_categoria, destacado) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssdiisii", 
            $datos_producto['nombre'], 
            $descripcion, 
            $datos_producto['precio'], 
            $datos_producto['stock'], 
            $imagen, 
            $tallas, 
            $datos_producto['id_categoria'], 
            $destacado
        );
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => "Producto añadido correctamente.",
                'id_producto' => $conn->insert_id
            ];
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        registrar_error("Error al agregar producto: " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Error al agregar el producto: " . $e->getMessage()
        ];
    }
}

/**
 * Actualiza un producto existente en la base de datos
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_producto ID del producto a actualizar
 * @param array $datos_producto Nuevos datos del producto
 * @return array Resultado de la operación con claves 'success' y 'message'
 */
function actualizar_producto($conn, $id_producto, $datos_producto) {
    try {
        // Verificar que el producto existe
        $producto_actual = obtener_producto_por_id($conn, $id_producto);
        if (!$producto_actual) {
            return [
                'success' => false,
                'message' => "El producto no existe."
            ];
        }
        
        // Validar datos obligatorios
        $campos_requeridos = ['nombre', 'precio', 'stock', 'id_categoria'];
        foreach ($campos_requeridos as $campo) {
            if (!isset($datos_producto[$campo]) || empty($datos_producto[$campo])) {
                return [
                    'success' => false,
                    'message' => "El campo {$campo} es obligatorio."
                ];
            }
        }
        
        // Validar que precio y stock sean números positivos
        if (!is_numeric($datos_producto['precio']) || floatval($datos_producto['precio']) <= 0) {
            return [
                'success' => false,
                'message' => "El precio debe ser un número positivo."
            ];
        }
        
        if (!is_numeric($datos_producto['stock']) || intval($datos_producto['stock']) < 0) {
            return [
                'success' => false,
                'message' => "El stock debe ser un número entero no negativo."
            ];
        }
        
        // Preparar valores para campos opcionales
        $descripcion = isset($datos_producto['descripcion']) ? $datos_producto['descripcion'] : $producto_actual['descripcion'];
        $imagen = isset($datos_producto['imagen']) ? $datos_producto['imagen'] : $producto_actual['imagen'];
        $tallas = isset($datos_producto['tallas']) ? $datos_producto['tallas'] : $producto_actual['tallas'];
        $destacado = isset($datos_producto['destacado']) ? (int)$datos_producto['destacado'] : $producto_actual['destacado'];
        
        // Actualizar producto
        $query = "UPDATE productos SET 
                 nombre = ?, 
                 descripcion = ?, 
                 precio = ?, 
                 stock = ?, 
                 imagen = ?, 
                 tallas = ?, 
                 id_categoria = ?, 
                 destacado = ? 
                 WHERE id_producto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssdiisiii", 
            $datos_producto['nombre'], 
            $descripcion, 
            $datos_producto['precio'], 
            $datos_producto['stock'], 
            $imagen, 
            $tallas, 
            $datos_producto['id_categoria'], 
            $destacado,
            $id_producto
        );
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => "Producto actualizado correctamente."
            ];
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        registrar_error("Error al actualizar producto: " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Error al actualizar el producto: " . $e->getMessage()
        ];
    }
}

/**
 * Actualiza solo el stock de un producto
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_producto ID del producto
 * @param int $nuevo_stock Nuevo valor de stock
 * @return array Resultado de la operación con claves 'success' y 'message'
 */
function actualizar_stock_producto($conn, $id_producto, $nuevo_stock) {
    try {
        // Verificar que el producto existe
        $producto_actual = obtener_producto_por_id($conn, $id_producto);
        if (!$producto_actual) {
            return [
                'success' => false,
                'message' => "El producto no existe."
            ];
        }
        
        // Validar que el stock sea un número no negativo
        if (!is_numeric($nuevo_stock) || intval($nuevo_stock) < 0) {
            return [
                'success' => false,
                'message' => "El stock debe ser un número entero no negativo."
            ];
        }
        
        // Actualizar stock
        $query = "UPDATE productos SET stock = ? WHERE id_producto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $nuevo_stock, $id_producto);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => "Stock actualizado correctamente."
            ];
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        registrar_error("Error al actualizar stock: " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Error al actualizar el stock: " . $e->getMessage()
        ];
    }
}

/**
 * Elimina un producto de la base de datos
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_producto ID del producto a eliminar
 * @return array Resultado de la operación con claves 'success' y 'message'
 */
function eliminar_producto($conn, $id_producto) {
    try {
        // Verificar que el producto existe
        $producto = obtener_producto_por_id($conn, $id_producto);
        if (!$producto) {
            return [
                'success' => false,
                'message' => "El producto no existe."
            ];
        }
        
        // Iniciar transacción para asegurar integridad
        $conn->begin_transaction();
        
        try {
            // Eliminar referencias en carrito_items
            $query = "DELETE FROM carrito_items WHERE id_producto = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();
            
            // Eliminar referencias en pedido_items (opcional, depende de la lógica de negocio)
            // En este caso, no eliminamos de pedido_items para mantener el historial
            
            // Eliminar el producto
            $query = "DELETE FROM productos WHERE id_producto = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();
            
            // Confirmar transacción
            $conn->commit();
            
            return [
                'success' => true,
                'message' => "Producto eliminado correctamente."
            ];
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conn->rollback();
            throw $e;
        }
    } catch (Exception $e) {
        registrar_error("Error al eliminar producto: " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Error al eliminar el producto: " . $e->getMessage()
        ];
    }
}

/**
 * Obtiene todas las categorías de productos
 * 
 * @param object $conn Conexión a la base de datos
 * @return array Lista de categorías
 */
function obtener_categorias($conn) {
    try {
        $query = "SELECT id_categoria, nombre, descripcion FROM categorias ORDER BY nombre ASC";
        $resultado = $conn->query($query);
        
        $categorias = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($categoria = $resultado->fetch_assoc()) {
                $categorias[] = $categoria;
            }
        }
        
        return $categorias;
    } catch (Exception $e) {
        registrar_error("Error al obtener categorías: " . $e->getMessage());
        return [];
    }
}


/**
 * Añade una nueva categoría a la base de datos
 * 
 * @param object $conn Conexión a la base de datos
 * @param string $nombre Nombre de la categoría
 * @param string $descripcion Descripción de la categoría (opcional)
 * @return array Resultado de la operación con claves 'success', 'message' y 'id_categoria'
 */
function agregar_categoria($conn, $nombre, $descripcion = '') {
    try {
        if (empty($nombre)) {
            return [
                'success' => false,
                'message' => "El nombre de la categoría es obligatorio."
            ];
        }

        // Verificar si la categoría ya existe
        $query_check = "SELECT COUNT(*) FROM categorias WHERE nombre = ?";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bind_param("s", $nombre);
        $stmt_check->execute();
        $count = 0; // Inicializar la variable para evitar el error 'Undefined variable'
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            return [
                'success' => false,
                'message' => "La categoría '" . htmlspecialchars($nombre) . "' ya existe."
            ];
        }

        $query = "INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $nombre, $descripcion);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => "Categoría '" . htmlspecialchars($nombre) . "' añadida correctamente.",
                'id_categoria' => $conn->insert_id
            ];
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        registrar_error("Error al agregar categoría: " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Error al agregar la categoría: " . $e->getMessage()
        ];
    }
}


