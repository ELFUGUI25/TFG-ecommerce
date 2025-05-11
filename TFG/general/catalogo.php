<?php
// catalogo.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <link rel="stylesheet" href="catalogo.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>
<body>
    <div class="catalogo-container">
        <h1>Catálogo de Productos</h1>
        <div class="tabs">
            <button class="tab-button active" onclick="mostrarCategoria('fruta')">Fruta y Verdura</button>
            <button class="tab-button" onclick="mostrarCategoria('carne')">Carne</button>
            <button class="tab-button" onclick="mostrarCategoria('bebida')">Bebida</button>
            <button class="tab-button" onclick="mostrarCategoria('congelados')">Congelados</button>
        </div>

        <div id="fruta" class="categoria active">
            <div class="producto">
                <img src="img/manzana.jpg" alt="Manzana">
                <h3>Manzana</h3>
                <p>Precio: 1,50€/kg</p>
                <p>Disponible: 30kg</p>
            </div>
            <div class="producto">
                <img src="img/zanahoria.jpg" alt="Zanahoria">
                <h3>Zanahoria</h3>
                <p>Precio: 0,90€/kg</p>
                <p>Disponible: 45kg</p>
            </div>
            <!-- Agrega más productos de fruta/verdura aquí -->
        </div>

        <div id="carne" class="categoria">
            <div class="producto">
                <img src="img/pollo.jpg" alt="Pollo">
                <h3>Pollo entero</h3>
                <p>Precio: 3,80€/kg</p>
                <p>Disponible: 20kg</p>
            </div>
            <div class="producto">
                <img src="img/ternera.jpg" alt="Ternera">
                <h3>Ternera</h3>
                <p>Precio: 7,20€/kg</p>
                <p>Disponible: 12kg</p>
            </div>
        </div>

        <div id="bebida" class="categoria">
            <div class="producto">
                <img src="img/agua.jpg" alt="Agua">
                <h3>Agua Mineral</h3>
                <p>Precio: 0,50€/u</p>
                <p>Disponible: 100 unidades</p>
            </div>
            <div class="producto">
                <img src="img/zumo.jpg" alt="Zumo">
                <h3>Zumo de naranja</h3>
                <p>Precio: 1,20€/u</p>
                <p>Disponible: 60 unidades</p>
            </div>
        </div>

        <div id="congelados" class="categoria">
            <div class="producto">
                <img src="img/pizza.jpg" alt="Pizza congelada">
                <h3>Pizza Congelada</h3>
                <p>Precio: 2,50€/u</p>
                <p>Disponible: 35 unidades</p>
            </div>
            <div class="producto">
                <img src="img/helado.jpg" alt="Helado">
                <h3>Helado de vainilla</h3>
                <p>Precio: 3,00€/u</p>
                <p>Disponible: 20 unidades</p>
            </div>
        </div>
    </div>

    <script>
        function mostrarCategoria(id) {
            const categorias = document.querySelectorAll('.categoria');
            const botones = document.querySelectorAll('.tab-button');
            categorias.forEach(cat => cat.classList.remove('active'));
            botones.forEach(btn => btn.classList.remove('active'));
            document.getElementById(id).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
