<?php
session_start();
require_once 'conexao.php';
require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../landingpage/perfil.php');
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($csrf_token)) {
    header('Location: ../landingpage/perfil.php?erro=csrf');
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login-page/index.php');
    exit;
}

$user_id = (int)$_SESSION['usuario_id'];
$email = trim($_POST['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../landingpage/perfil.php?erro=email_invalido');
    exit;
}

// Verifica se email já existe em outro usuário
$stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ? AND id != ?');
if (!$stmt) {
    header('Location: ../landingpage/perfil.php?erro=db_error');
    exit;
}
$stmt->bind_param('si', $email, $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    header('Location: ../landingpage/perfil.php?erro=email_duplicado');
    exit;
}

$upd = $conn->prepare('UPDATE usuarios SET email = ? WHERE id = ?');
if (!$upd) {
    header('Location: ../landingpage/perfil.php?erro=db_error');
    exit;
}
$upd->bind_param('si', $email, $user_id);
if ($upd->execute()) {
    header('Location: ../landingpage/perfil.php?sucesso_email=1');
    exit;
} else {
    header('Location: ../landingpage/perfil.php?erro=db_error');
    exit;
}

?>
