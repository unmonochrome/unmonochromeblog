<?php
// Sistema de CSRF Protection
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Regenera token a cada hora para segurança
if (!isset($_SESSION['csrf_time'])) {
    $_SESSION['csrf_time'] = time();
} elseif (time() - $_SESSION['csrf_time'] > 3600) {
    unset($_SESSION['csrf_token']);
    $_SESSION['csrf_time'] = time();
}
?>
