<?php
/**
 * Funciones para el manejo del carrito de compras
 * 
 * Este archivo contiene todas las funciones necesarias para gestionar el carrito de compras,
 * incluyendo agregar, actualizar, eliminar productos y procesar la compra.
 * 
 * @author Proyecto TFG
 * @version 1.1
 */

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Obtiene el ID del carrito del usuario actual o crea uno nuevo si no existe
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario actual
 * @return int ID del carrito del usuario
 * @throws Exception Si hay un error en la consulta
 */
function obtenerCarritoUsuario($conn, $id_usuario) {
    try {
        // Verificar si el usuario ya tiene un carrito activo
        $query = "SELECT id_carrito FROM carrito WHERE id_usuario = ? ORDER BY fecha_creacion DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            return $fila['id_carrito'];
        } else {
            // Crear un nuevo carrito para el usuario
            $query = "INSERT INTO carrito (id_usuario) VALUES (?)";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al crear nuevo carrito: " . $conn->error);
            }
            
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            return $conn->insert_id;
        }
    } catch (Exception $e) {
        // Registrar el error y devolver un valor por defecto
        error_log("Error en obtenerCarritoUsuario: " . $e->getMessage());
        return 0; // Valor que indicará error
    }
}

/**
 * Agrega un producto al carrito verificando stock disponible
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario actual
 * @param int $id_producto ID del producto a agregar
 * @param int $cantidad Cantidad del producto a agregar (por defecto 1)
 * @return array Resultado de la operación con claves 'success' y 'message'
 */
function agregarAlCarrito($conn, $id_usuario, $id_producto, $cantidad = 1) {
    try {
        // Validar parámetros de entrada
        if (!is_numeric($id_usuario) || $id_usuario <= 0) {
            return ["success" => false, "message" => "ID de usuario inválido"];
        }
        
        if (!is_numeric($id_producto) || $id_producto <= 0) {
            return ["success" => false, "message" => "ID de producto inválido"];
        }
        
        if (!is_numeric($cantidad) || $cantidad <= 0) {
            return ["success" => false, "message" => "Cantidad inválida"];
        }
        
        // Verificar stock disponible
        $query = "SELECT stock, precio FROM productos WHERE id_producto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            return ["success" => false, "message" => "Producto no encontrado"];
        }
        
        $producto = $resultado->fetch_assoc();
        
        if ($producto['stock'] < $cantidad) {
            return ["success" => false, "message" => "Stock insuficiente. Solo quedan " . $producto['stock'] . " unidades disponibles."];
        }
        
        // Obtener o crear carrito
        $id_carrito = obtenerCarritoUsuario($conn, $id_usuario);
        
        if ($id_carrito === 0) {
            return ["success" => false, "message" => "Error al obtener el carrito del usuario"];
        }
        
        // Verificar si el producto ya está en el carrito
        $query = "SELECT id_item, cantidad FROM carrito_items WHERE id_carrito = ? AND id_producto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $id_carrito, $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            // Actualizar cantidad si ya existe
            $item = $resultado->fetch_assoc();
            $nueva_cantidad = $item['cantidad'] + $cantidad;
            
            // Verificar que la nueva cantidad no exceda el stock
            if ($nueva_cantidad > $producto['stock']) {
                return ["success" => false, "message" => "No puedes agregar más unidades. Stock insuficiente."];
            }
            
            $query = "UPDATE carrito_items SET cantidad = ? WHERE id_item = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $nueva_cantidad, $item['id_item']);
            $stmt->execute();
        } else {
            // Agregar nuevo item al carrito
            $query = "INSERT INTO carrito_items (id_carrito, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiid", $id_carrito, $id_producto, $cantidad, $producto['precio']);
            $stmt->execute();
        }
        
        return ["success" => true, "message" => "Producto agregado al carrito correctamente"];
    } catch (Exception $e) {
        error_log("Error en agregarAlCarrito: " . $e->getMessage());
        return ["success" => false, "message" => "Error al agregar el producto al carrito"];
    }
}

/**
 * Actualiza la cantidad de un producto en el carrito
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario actual
 * @param int $id_producto ID del producto a actualizar
 * @param int $cantidad Nueva cantidad del producto
 * @return array Resultado de la operación con claves 'success' y 'message'
 */
function actualizarCantidadCarrito($conn, $id_usuario, $id_producto, $cantidad) {
    try {
        // Si la cantidad es 0 o negativa, eliminar el producto
        if ($cantidad <= 0) {
            return eliminarDelCarrito($conn, $id_usuario, $id_producto);
        }
        
        // Verificar stock disponible
        $query = "SELECT stock FROM productos WHERE id_producto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            return ["success" => false, "message" => "Producto no encontrado"];
        }
        
        $producto = $resultado->fetch_assoc();
        
        if ($producto['stock'] < $cantidad) {
            return ["success" => false, "message" => "Stock insuficiente. Solo quedan " . $producto['stock'] . " unidades disponibles."];
        }
        
        // Obtener carrito
        $id_carrito = obtenerCarritoUsuario($conn, $id_usuario);
        
        if ($id_carrito === 0) {
            return ["success" => false, "message" => "Error al obtener el carrito del usuario"];
        }
        
        // Actualizar cantidad
        $query = "UPDATE carrito_items SET cantidad = ? WHERE id_carrito = ? AND id_producto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $cantidad, $id_carrito, $id_producto);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            return ["success" => true, "message" => "Cantidad actualizada correctamente"];
        } else {
            return ["success" => false, "message" => "No se pudo actualizar la cantidad"];
        }
    } catch (Exception $e) {
        error_log("Error en actualizarCantidadCarrito: " . $e->getMessage());
        return ["success" => false, "message" => "Error al actualizar la cantidad del producto"];
    }
}

/**
 * Elimina un producto del carrito
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario actual
 * @param int $id_producto ID del producto a eliminar
 * @return array Resultado de la operación con claves 'success' y 'message'
 */
function eliminarDelCarrito($conn, $id_usuario, $id_producto) {
    try {
        // Obtener carrito
        $id_carrito = obtenerCarritoUsuario($conn, $id_usuario);
        
        if ($id_carrito === 0) {
            return ["success" => false, "message" => "Error al obtener el carrito del usuario"];
        }
        
        // Eliminar producto del carrito
        $query = "DELETE FROM carrito_items WHERE id_carrito = ? AND id_producto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $id_carrito, $id_producto);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            return ["success" => true, "message" => "Producto eliminado del carrito"];
        } else {
            return ["success" => false, "message" => "No se pudo eliminar el producto del carrito"];
        }
    } catch (Exception $e) {
        error_log("Error en eliminarDelCarrito: " . $e->getMessage());
        return ["success" => false, "message" => "Error al eliminar el producto del carrito"];
    }
}

/**
 * Obtiene el contenido completo del carrito con información detallada de productos
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario actual
 * @return array Contenido del carrito con items, total y número de items
 */
function obtenerContenidoCarrito($conn, $id_usuario) {
    try {
        // Obtener carrito
        $id_carrito = obtenerCarritoUsuario($conn, $id_usuario);
        
        if ($id_carrito === 0) {
            return ["items" => [], "total" => 0, "num_items" => 0];
        }
        
        // Obtener items del carrito con información de productos
        $query = "SELECT ci.id_item, ci.id_producto, ci.cantidad, ci.precio_unitario, 
                  p.nombre, p.imagen, p.stock
                  FROM carrito_items ci
                  JOIN productos p ON ci.id_producto = p.id_producto
                  WHERE ci.id_carrito = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_carrito);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $items = [];
        $total = 0;
        
        while ($item = $resultado->fetch_assoc()) {
            $subtotal = $item['cantidad'] * $item['precio_unitario'];
            $total += $subtotal;
            
            $item['subtotal'] = $subtotal;
            $items[] = $item;
        }
        
        return [
            "items" => $items,
            "total" => $total,
            "num_items" => count($items)
        ];
    } catch (Exception $e) {
        error_log("Error en obtenerContenidoCarrito: " . $e->getMessage());
        return ["items" => [], "total" => 0, "num_items" => 0];
    }
}

/**
 * Vacía completamente el carrito de compras
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario actual
 * @return array Resultado de la operación con claves 'success' y 'message'
 */
function vaciarCarrito($conn, $id_usuario) {
    try {
        // Obtener carrito
        $id_carrito = obtenerCarritoUsuario($conn, $id_usuario);
        
        if ($id_carrito === 0) {
            return ["success" => false, "message" => "Error al obtener el carrito del usuario"];
        }
        
        // Eliminar todos los items del carrito
        $query = "DELETE FROM carrito_items WHERE id_carrito = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_carrito);
        $stmt->execute();
        
        return ["success" => true, "message" => "Carrito vaciado correctamente"];
    } catch (Exception $e) {
        error_log("Error en vaciarCarrito: " . $e->getMessage());
        return ["success" => false, "message" => "Error al vaciar el carrito"];
    }
}

/**
 * Procesa la compra y crea un pedido con transacción para garantizar integridad
 * 
 * @param object $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario actual
 * @param string $direccion_envio Dirección de envío del pedido
 * @param string $metodo_pago Método de pago seleccionado
 * @return array Resultado de la operación con claves 'success', 'message' y 'id_pedido'
 */
function procesarCompra($conn, $id_usuario, $direccion_envio, $metodo_pago) {
    try {
        // Validar parámetros
        if (empty($direccion_envio)) {
            return ["success" => false, "message" => "La dirección de envío es obligatoria"];
        }
        
        if (empty($metodo_pago)) {
            return ["success" => false, "message" => "El método de pago es obligatorio"];
        }
        
        // Obtener contenido del carrito
        $carrito = obtenerContenidoCarrito($conn, $id_usuario);
        
        if (count($carrito['items']) === 0) {
            return ["success" => false, "message" => "El carrito está vacío"];
        }
        
        // Verificar stock antes de procesar
        foreach ($carrito['items'] as $item) {
            if ($item['cantidad'] > $item['stock']) {
                return [
                    "success" => false, 
                    "message" => "Stock insuficiente para el producto: " . $item['nombre']
                ];
            }
        }
        
        // Iniciar transacción
        $conn->begin_transaction();
        
        try {
            // Crear pedido
            $query = "INSERT INTO pedidos (id_usuario, total, direccion_envio, metodo_pago) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("idss", $id_usuario, $carrito['total'], $direccion_envio, $metodo_pago);
            $stmt->execute();
            
            $id_pedido = $conn->insert_id;
            
            // Agregar items al pedido
            foreach ($carrito['items'] as $item) {
                $query = "INSERT INTO pedido_items (id_pedido, id_producto, cantidad, precio_unitario) 
                          VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("iiid", $id_pedido, $item['id_producto'], $item['cantidad'], $item['precio_unitario']);
                $stmt->execute();
                
                // Actualizar stock
                $query = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ii", $item['cantidad'], $item['id_producto']);
                $stmt->execute();
            }
            
            // Vaciar carrito
            vaciarCarrito($conn, $id_usuario);
            
            // Confirmar transacción
            $conn->commit();
            
            return [
                "success" => true, 
                "message" => "Compra procesada correctamente", 
                "id_pedido" => $id_pedido
            ];
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conn->rollback();
            throw $e; // Relanzar para el manejo externo
        }
    } catch (Exception $e) {
        error_log("Error en procesarCompra: " . $e->getMessage());
        return ["success" => false, "message" => "Error al procesar la compra: " . $e->getMessage()];
    }
}
