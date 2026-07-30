<?php
// ── Proceso de Cierre de Sesión ──
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body>
<script>
  localStorage.removeItem('toyStoreCart');
  localStorage.removeItem('toyStoreCartTs');
  window.location.replace('../index.php');
</script>
</body>
</html>
