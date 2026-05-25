<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../landingpage/usuarios_admin.php');
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($csrf_token)) {
    header('Location: ../landingpage/usuarios_admin.php?erro=csrf');
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login-page/index.php');
    exit;
}

$current_id = (int)$_SESSION['usuario_id'];

// verifica se usuário atual é admin
$stmt = $conn->prepare('SELECT tipo FROM usuarios WHERE id = ? LIMIT 1');
if (!$stmt) {
    header('Location: ../landingpage/usuarios_admin.php?erro=db_error');
    exit;
}
$stmt->bind_param('i', $current_id);
$stmt->execute();
$r = $stmt->get_result();
if ($r->num_rows === 0) {
    header('Location: ../login-page/index.php');
    exit;
}
$me = $r->fetch_assoc();
if ($me['tipo'] !== 'admin') {
    header('Location: ../landingpage/usuarios_admin.php?erro=forbidden');
    exit;
}

$target_id = (int)($_POST['user_id'] ?? 0);
if ($target_id <= 0) {
    header('Location: ../landingpage/usuarios_admin.php?erro=invalid');
    exit;
}

$upd = $conn->prepare('UPDATE usuarios SET tipo = ? WHERE id = ?');
if (!$upd) {
    header('Location: ../landingpage/usuarios_admin.php?erro=db_error');
    exit;
}
$role = 'admin';
$upd->bind_param('si', $role, $target_id);
if ($upd->execute()) {
    header('Location: ../landingpage/usuarios_admin.php?sucesso=1');
    exit;
} else {
    header('Location: ../landingpage/usuarios_admin.php?erro=db_error');
    exit;
}

?>
