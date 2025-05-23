<?php
/**
 * Funciones para el manejo del carrito de compras
 */

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Obtiene el ID del carrito del usuario actual o crea uno nuevo
 */
function obtenerCarritoUsuario($conn, $id_usuario) {
    // Verificar si el usuario ya tiene un carrito activo
    $query = "SELECT id_carrito FROM carrito WHERE id_usuario = ? ORDER BY fecha_creacion DESC LIMIT 1";
    $stmt = $conn->prepare($query);
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
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $conn->insert_id;
    }
}

/**
 * Agrega un producto al carrito
 */
function agregarAlCarrito($conn, $id_usuario, $id_producto, $cantidad = 1) {
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
}

/**
 * Actualiza la cantidad de un producto en el carrito
 */
function actualizarCantidadCarrito($conn, $id_usuario, $id_producto, $cantidad) {
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
}

/**
 * Elimina un producto del carrito
 */
function eliminarDelCarrito($conn, $id_usuario, $id_producto) {
    // Obtener carrito
    $id_carrito = obtenerCarritoUsuario($conn, $id_usuario);
    
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
}

/**
 * Obtiene el contenido del carrito
 */
function obtenerContenidoCarrito($conn, $id_usuario) {
    // Obtener carrito
    $id_carrito = obtenerCarritoUsuario($conn, $id_usuario);
    
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
}

/**
 * Vacía el carrito de compras
 */
function vaciarCarrito($conn, $id_usuario) {
    // Obtener carrito
    $id_carrito = obtenerCarritoUsuario($conn, $id_usuario);
    
    // Eliminar todos los items del carrito
    $query = "DELETE FROM carrito_items WHERE id_carrito = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_carrito);
    $stmt->execute();
    
    return ["success" => true, "message" => "Carrito vaciado correctamente"];
}

/**
 * Procesa la compra y crea un pedido
 */
function procesarCompra($conn, $id_usuario, $direccion_envio, $metodo_pago) {
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
        return ["success" => false, "message" => "Error al procesar la compra: " . $e->getMessage()];
    }
}
