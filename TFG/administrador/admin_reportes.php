<?php
session_start();
include 'config.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado"]);
    exit;
}

$reportes = [];

// Obtener total de usuarios
$query = "SELECT COUNT(id) AS total_usuarios FROM usuarios";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$reportes['total_usuarios'] = $row['total_usuarios'];

// Obtener total de productos
$query = "SELECT COUNT(id) AS total_productos FROM productos";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$reportes['total_productos'] = $row['total_productos'];

// Obtener total de ventas
$query = "SELECT COUNT(id) AS total_ventas, SUM(total) AS ingresos_totales FROM pedidos";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$reportes['total_ventas'] = $row['total_ventas'];
$reportes['ingresos_totales'] = $row['ingresos_totales'] ? $row['ingresos_totales'] : 0;

// Obtener productos más vendidos
$query = "SELECT p.nombre, SUM(dp.cantidad) AS cantidad_vendida 
          FROM detalle_pedidos dp
          JOIN productos p ON dp.producto_id = p.id
          GROUP BY dp.producto_id
          ORDER BY cantidad_vendida DESC
          LIMIT 5";
$result = mysqli_query($conn, $query);
$productos_mas_vendidos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $productos_mas_vendidos[] = $row;
}
$reportes['productos_mas_vendidos'] = $productos_mas_vendidos;

header('Content-Type: application/json');
echo json_encode($reportes);
?>
