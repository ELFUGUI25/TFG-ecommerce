<?php
// Aquí puedes agregar conexión a base de datos más adelante si deseas cargar los productos desde ahí.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Catálogo de Productos</title>
    <link rel="stylesheet" href="estilo.css" />
</head>
<body>
    <header>
        <div class="logo">Tienda Online</div>
        <nav>
            <ul>
                <li><a href="web.html">Inicio</a></li>
                <li><a href="catalogo.php">Catálogo</a></li>
                <li><a href="registro.php">Registro</a></li>
                <li><a href="login.php">Iniciar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="catalogo">
        <h1>Nuestro Catálogo</h1>
        <div class="productos">
            <div class="producto">
                <img src="img/producto1.jpg" alt="Producto 1" />
                <h2>Auriculares Bluetooth</h2>
                <p>29.99€</p>
                <button class="btn">Agregar al carrito</button>
            </div>
            <div class="producto">
                <img src="img/producto2.jpg" alt="Producto 2" />
                <h2>Sudadera Urbana</h2>
                <p>39.99€</p>
                <button class="btn">Agregar al carrito</button>
            </div>
            <div class="producto">
                <img src="img/producto3.jpg" alt="Producto 3" />
                <h2>Pack Cocina Pro</h2>
                <p>59.99€</p>
                <button class="btn">Agregar al carrito</button>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Tienda Online. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
