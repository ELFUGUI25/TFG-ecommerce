<?php
/**
 * Página de inicio de sesión
 * 
 * Esta página permite a los usuarios iniciar sesión en la aplicación
 * utilizando su correo electrónico o nombre de usuario y contraseña.
 * También permite el acceso al panel de administración con credenciales especiales.
 * 
 * @author Proyecto TFG
 * @version 1.1
 */

// Incluir archivos necesarios
require_once '../includes/config.php';
require_once '../includes/conexion.php';
require_once '../includes/utilidades.php';
require_once '../includes/mensajes.php';
require_once '../includes/validacion.php';
require_once '../includes/login_admin.php';

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirigir si ya está logueado
if (usuario_logueado()) {
    redireccionar('bienvenida.php');
}

$mensaje = "";
$tipo_mensaje = "info";

// Procesar formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $usuario = trim($_POST["usuario"]);
        $contrasena = trim($_POST["contrasena"]);

        // Validar campos
        if (empty($usuario)) {
            throw new Exception("Por favor, introduce tu correo o nombre de usuario.");
        }
        
        if (empty($contrasena)) {
            throw new Exception("Por favor, introduce tu contraseña.");
        }
        
        // Verificar si son credenciales de administrador
        if (verificar_admin($usuario, $contrasena)) {
            // Establecer sesión de administrador
            establecer_sesion_admin();
            
            // Redirigir al panel de administración
            redireccionar("admin/panel.php");
            exit();
        }

        // Si no es admin, verificar credenciales normales
        $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario, correo, contrasena FROM usuarios WHERE correo = ? OR nombre_usuario = ?");
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        
        $stmt->bind_param("ss", $usuario, $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            
            // Verificar contraseña
            if (password_verify($contrasena, $fila["contrasena"])) {
                // Iniciar sesión
                $_SESSION["correo"] = $fila["correo"];
                $_SESSION["id_usuario"] = $fila["id_usuario"];
                $_SESSION["nombre_usuario"] = $fila["nombre_usuario"];
                $_SESSION["es_admin"] = false; // Usuario normal
                
                // Regenerar ID de sesión para prevenir session fixation
                session_regenerate_id(true);
                
                redireccionar("bienvenida.php");
            } else {
                throw new Exception("El usuario o la contraseña son incorrectos.");
            }
        } else {
            throw new Exception("El usuario o la contraseña son incorrectos.");
        }
        $stmt->close();
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = "error";
        registrar_error("Error en login: " . $e->getMessage());
    }
}

// Variables para el header
$titulo = "Iniciar Sesión - Mi Tienda Online";
$css_adicional = "../css/login.css";
?>

<?php include '../includes/header.php'; ?>

<main>
    <div class="container">
        <h1>Iniciar Sesión</h1>
        <p>Accede con tu nombre de usuario o correo electrónico.</p>

        <?php if (!empty($mensaje)): ?>
            <?php echo mostrar_mensaje($mensaje, $tipo_mensaje); ?>
        <?php endif; ?>

        <form action="login.php" method="POST" class="formulario">
            <input type="text" name="usuario" placeholder="Correo o Nombre de Usuario" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit" class="boton">Entrar</button>
        </form>
        
        <p><a href="web.php" style="color:rgb(247, 184, 135)">← Volver al inicio</a></p>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
