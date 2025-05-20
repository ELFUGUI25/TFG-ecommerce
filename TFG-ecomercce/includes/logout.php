<?php
session_start();         // Inicia la sesión si aún no está iniciada
session_unset();         // Elimina todas las variables de sesión
session_destroy();       // Destruye la sesión
header("Location: ../pages/login.php"); // Redirige al login
exit();
