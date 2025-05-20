<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Políticas de Privacidad y Uso - Mi Tienda Online</title>
    <link rel="stylesheet" href="../css/politicas.css" />
</head>
<body>
    <header class="top-bar">
        <h1>Mi Tienda Online</h1>
        <nav>
            <a href="bienvenida.php" class="nav-btn">Inicio</a>
            <a href="catalogo.php" class="nav-btn">Catálogo</a>
            <a href="perfil.php" class="nav-btn">Mi Perfil</a>
            <a href="politicas.php" class="nav-btn activo">Políticas</a>
        </nav>
    </header>

    <main class="contenido">
        <article class="politicas-container">
            <h2>Políticas de Privacidad y Uso</h2>

            <section>
                <h3>1. Introducción</h3>
                <p>
                    En <strong>Mi Tienda Online</strong>, valoramos tu privacidad y nos comprometemos a proteger tus datos personales. 
                    Esta política describe cómo recopilamos, usamos y protegemos la información que nos proporcionas al usar nuestro sitio.
                </p>
            </section>

            <section>
                <h3>2. Datos que recopilamos</h3>
                <ul>
                    <li>Información personal que proporcionas al registrarte o hacer compras (nombre, correo electrónico, dirección, etc.).</li>
                    <li>Datos técnicos sobre tu dispositivo y navegación para mejorar la experiencia.</li>
                    <li>Información para procesar pagos y entregas.</li>
                </ul>
            </section>

            <section>
                <h3>3. Uso de la información</h3>
                <p>
                    Utilizamos tus datos para procesar tus pedidos, mejorar nuestros servicios, personalizar tu experiencia y comunicarnos contigo sobre novedades o promociones. 
                    Nunca vendemos tu información a terceros.
                </p>
            </section>

            <section>
                <h3>4. Seguridad</h3>
                <p>
                    Implementamos medidas técnicas y organizativas para proteger tus datos contra accesos no autorizados, pérdida o alteración. 
                    Sin embargo, ningún método de transmisión por Internet es 100% seguro.
                </p>
            </section>

            <section>
                <h3>5. Tus derechos</h3>
                <p>
                    Puedes acceder, rectificar o eliminar tus datos personales contactándonos directamente. También puedes oponerte al tratamiento de tus datos o solicitar su portabilidad.
                </p>
            </section>

            <section>
                <h3>6. Cookies</h3>
                <p>
                    Usamos cookies para mejorar la funcionalidad del sitio y analizar el tráfico. Puedes configurar tu navegador para rechazarlas o eliminarlas.
                </p>
            </section>

            <section>
                <h3>7. Cambios en esta política</h3>
                <p>
                    Podemos actualizar esta política ocasionalmente. Te notificaremos cualquier cambio significativo mediante avisos visibles en nuestra web.
                </p>
            </section>

            <section>
                <h3>8. Contacto</h3>
                <p>
                    Para dudas o solicitudes sobre tus datos, puedes contactarnos en <a href="mailto:soporte@mitiendaonline.com">soporte@mitiendaonline.com</a>.
                </p>
            </section>
        </article>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Tienda Online - Proyecto TFG</p>
    </footer>
</body>
</html>
