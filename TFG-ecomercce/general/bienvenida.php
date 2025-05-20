<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
require_once 'conexion.php';

// Inicializar variables mensajes
$mensaje_exito = '';
$mensaje_error = '';

// Procesar envío del comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
    $usuario = $_SESSION['nombre_usuario'];
    $comentario = trim($_POST['comentario']);
    
    if ($comentario !== '') {
        $stmt = $conn->prepare("INSERT INTO comentarios (usuario, comentario) VALUES (?, ?)");
        $stmt->bind_param("ss", $usuario, $comentario);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje_exito'] = "Gracias por tu comentario.";
        } else {
            $_SESSION['mensaje_error'] = "Error al guardar tu comentario. Inténtalo de nuevo.";
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje_error'] = "El comentario no puede estar vacío.";
    }

    // Redirigir para evitar reenvío del formulario
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Recuperar mensajes de sesión
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}
if (isset($_SESSION['mensaje_error'])) {
    $mensaje_error = $_SESSION['mensaje_error'];
    unset($_SESSION['mensaje_error']);
}

// Obtener comentarios recientes
$comentarios = [];
$result = $conn->query("SELECT usuario, comentario, fecha FROM comentarios ORDER BY fecha DESC LIMIT 10");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $comentarios[] = $row;
    }
    $result->free();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Usuario - Mi Tienda Online</title>
    <link rel="stylesheet" href="bienvenida.css">
</head>
<body>
    <header class="top-bar">
        <h1>Mi Tienda Online</h1>
        <nav>
            <a href="web.php" class="nav-btn <?php echo basename($_SERVER['PHP_SELF']) == 'web.php' ? 'activo' : ''; ?>">Inicio</a>
            <a href="catalogo.php" class="nav-btn <?php echo basename($_SERVER['PHP_SELF']) == 'catalogo.php' ? 'activo' : ''; ?>">Catálogo</a>
            <a href="perfil.php" class="nav-btn <?php echo basename($_SERVER['PHP_SELF']) == 'perfil.php' ? 'activo' : ''; ?>">Mi Perfil</a>
            <a href="politicas.php" class="nav-btn <?php echo basename($_SERVER['PHP_SELF']) == 'politicas.php' ? 'activo' : ''; ?>">Políticas</a>
        </nav>
    </header>

    <main class="contenido">
        <section class="bienvenida">
            <h2>Hola <strong><?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></strong>, has iniciado sesión correctamente.</h2>
            <p>Bienvenido a tu panel de usuario. Aquí puedes acceder a diferentes opciones de la tienda.</p>
        </section>

        <section class="informacion-principal">
            <h3>¿Quiénes somos?</h3>
            <p>Mi Tienda Online es una plataforma comprometida con ofrecer productos de la más alta calidad, seleccionados cuidadosamente para ti. Nuestra misión es brindar una experiencia de compra segura, cómoda y personalizada.</p>
            <p>Trabajamos con pequeños productores locales y tiendas independientes para apoyar la economía regional y garantizar productos frescos y auténticos.</p>
        </section>

        <section class="informacion-secundaria">
            <div class="lateral lateral-izquierdo">
                <h4>Recomendaciones para ti</h4>
                <ul>
                    <li>Explora nuestras <a href="catalogo.php">ofertas exclusivas</a> y novedades.</li>
                    <li>Visita nuestra sección de <a href="perfil.php">perfil</a> para personalizar tus preferencias.</li>
                    <li>Suscríbete a nuestro boletín para recibir promociones y noticias.</li>
                </ul>

                <h4>Consejos para una compra segura</h4>
                <ul>
                    <li>Revisa las descripciones y opiniones de cada producto.</li>
                    <li>Utiliza métodos de pago seguros y confiables.</li>
                    <li>Consulta nuestra <a href="politicas.php">política de privacidad y devoluciones</a>.</li>
                </ul>
            </div>

            <div class="contenido-central">
                <h3>Explora pequeñas tiendas locales</h3>
                <p>Apoyar a negocios locales es vital para la economía. Aquí tienes algunas URL interesantes para descubrir productos únicos:</p>
                <ul>
                    <li><a href="https://www.tiendaverde.com" target="_blank" rel="noopener">Tienda Verde - Productos ecológicos</a></li>
                    <li><a href="https://www.artesaniaslocales.com" target="_blank" rel="noopener">Artesanías Locales - Manualidades y arte</a></li>
                    <li><a href="https://www.gourmetdelcampo.com" target="_blank" rel="noopener">Gourmet del Campo - Delicias regionales</a></li>
                    <li><a href="https://www.ecohogar.com" target="_blank" rel="noopener">Eco Hogar - Artículos sostenibles para tu casa</a></li>
                </ul>

                <h3>Información útil</h3>
                <p>¿Quieres saber más sobre tendencias en comercio electrónico y cómo aprovechar al máximo tu experiencia de compra? Aquí algunos recursos:</p>
                <ul>
                    <li><a href="https://www.shopify.com/blog" target="_blank" rel="noopener">Shopify Blog</a></li>
                    <li><a href="https://www.bigcommerce.com/blog" target="_blank" rel="noopener">BigCommerce Blog</a></li>
                    <li><a href="https://www.ecommerceceo.com" target="_blank" rel="noopener">Ecommerce CEO</a></li>
                </ul>
            </div>

            <div class="lateral lateral-derecho">
                <h4>Atención al cliente</h4>
                <p>Estamos aquí para ayudarte. Puedes contactarnos a través de los siguientes medios:</p>
                <ul>
                    <li>Email: <a href="mailto:soporte@mitiendaonline.com">soporte@mitiendaonline.com</a></li>
                    <li>Teléfono: +34 912 345 678</li>
                    <li>Horario: Lunes a Viernes, 9:00 - 18:00</li>
                </ul>

                <h4>Redes Sociales</h4>
                <ul>
                    <li><a href="https://facebook.com/mitiendaonline" target="_blank" rel="noopener">Facebook</a></li>
                    <li><a href="https://twitter.com/mitiendaonline" target="_blank" rel="noopener">Twitter</a></li>
                    <li><a href="https://instagram.com/mitiendaonline" target="_blank" rel="noopener">Instagram</a></li>
                </ul>
            </div>
        </section>

        <!-- Sección de comentarios -->
        <section class="comentarios">
            <h3>Comentarios de usuarios</h3>

            <?php if (!empty($mensaje_exito)): ?>
                <p style="color: green;"><?php echo htmlspecialchars($mensaje_exito); ?></p>
            <?php endif; ?>

            <?php if (!empty($mensaje_error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($mensaje_error); ?></p>
            <?php endif; ?>

            <form method="post" action="">
                <label for="comentario">Escribe tu comentario:</label><br>
                <textarea id="comentario" name="comentario" rows="6" maxlength="500" required placeholder="Comparte tu opinión o sugerencia..."></textarea><br>
                <button type="submit">Enviar comentario</button>
            </form>

            <hr>

            <?php if (count($comentarios) === 0): ?>
                <p>No hay comentarios aún.</p>
            <?php else: ?>
                <?php foreach ($comentarios as $c): ?>
                    <div>
                        <p><strong><?php echo htmlspecialchars($c['usuario']); ?></strong> (<?php echo date('d/m/Y H:i', strtotime($c['fecha'])); ?>)</p>
                        <p><?php echo nl2br(htmlspecialchars($c['comentario'])); ?></p>
                    </div>
                    <hr>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Tienda Online - Proyecto TFG</p>
    </footer>
</body>
</html>
