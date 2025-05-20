<?php
// Archivo simplificado de la página principal
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda Online</title>
    <link rel="stylesheet" href="../css/web.css">
</head>
<body>
    <header>
        <h1>Mi Tienda Online</h1>
        <p>¡Tu nueva plataforma ecomercce de compras inteligentes y sin complicaciones!</p>
    </header>

    <main>
        <section class="presentacion">
            <h2>¿Qué es esta plataforma?</h2>
            <p>
                Esta tienda online es mucho más que un simple catálogo. Aquí encontrarás:
            </p>
            <ul>
                <li>🛒 Productos organizados por categorías.</li>
                <li>📦 Gestión de stock e inventario.</li>
                <li>🤖 Recomendaciones personalizadas según tus gustos y compras.</li>
                <li>🔒 Compras seguras con pasarelas como PayPal </li>
                <li>📈 Panel de administración para gestionar todo en segundos.</li>
            </ul>
            <p>
                Esta plataforma está pensada para ti. ¡Únete y descubre una nueva forma de comprar online!
            </p>
        </section>

        <section class="acciones">
           <a href="registro.php" class="boton">Registrarse</a>
           <a href="login.php" class="boton">Iniciar sesión</a>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Tienda Online - Proyecto TFG | Desarrollado con HTML, CSS y PHP</p>
    </footer>
</body>
</html>
