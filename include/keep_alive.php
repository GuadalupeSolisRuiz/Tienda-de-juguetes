<?php
// ── Mantener Viva la Sesión PHP ──
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (isset($_SESSION['usuario_id'])) {
    // Actualizar el tiempo de última actividad
    $_SESSION['ultima_actividad'] = time();
    echo json_encode(['success' => true, 'message' => 'Sesión activa y refrescada.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No hay una sesión activa.']);
}
?>
