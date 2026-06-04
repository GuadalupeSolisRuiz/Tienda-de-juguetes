<?php
// ── Proceso de Cierre de Sesión ──
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vaciar todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la cookie de sesión por completo
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Redireccionar al inicio
header("Location: ../index.php");
exit;
?>
