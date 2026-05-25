<?php
require_once 'conexao.php';
require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login-page/validar_codigo.php');
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($csrf_token)) {
    header('Location: ../login-page/validar_codigo.php?erro=csrf');
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$codigo = trim($_POST['codigo'] ?? '');
$nova_senha = trim($_POST['nova_senha'] ?? '');
$confirmar_senha = trim($_POST['confirmar_senha'] ?? '');

if ($usuario === '' || $codigo === '' || $nova_senha === '' || $confirmar_senha === '') {
    header('Location: ../login-page/validar_codigo.php?erro=campos_vazios');
    exit;
}

if ($nova_senha !== $confirmar_senha) {
    header('Location: ../login-page/validar_codigo.php?erro=senhas_nao_coincidem');
    exit;
}

if (strlen($nova_senha) < 6) {
    header('Location: ../login-page/validar_codigo.php?erro=senha_fraca');
    exit;
}

// Localiza usuário
// aceitar usuario ou email
$stmt = $conn->prepare('SELECT id FROM usuarios WHERE usuario = ? OR email = ? LIMIT 1');
if (!$stmt) {
    header('Location: ../login-page/validar_codigo.php?erro=db_error');
    exit;
}
$stmt->bind_param('ss', $usuario, $usuario);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header('Location: ../login-page/validar_codigo.php?erro=codigo_invalido');
    exit;
}

$user = $res->fetch_assoc();
$user_id = $user['id'];

// Busca token válido
$now = date('Y-m-d H:i:s');
$stmt2 = $conn->prepare('SELECT id, token_hash, expires_at, attempts FROM password_resets WHERE user_id = ? ORDER BY created_at DESC LIMIT 1');
if (!$stmt2) {
    header('Location: ../login-page/validar_codigo.php?erro=db_error');
    exit;
}
$stmt2->bind_param('i', $user_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($res2->num_rows === 0) {
    header('Location: ../login-page/validar_codigo.php?erro=codigo_invalido');
    exit;
}

$row = $res2->fetch_assoc();
$reset_id = $row['id'];
$token_hash = $row['token_hash'];
$expires_at = $row['expires_at'];
$attempts = (int)$row['attempts'];

if ($attempts >= 5) {
    header('Location: ../login-page/validar_codigo.php?erro=muitas_tentativas');
    exit;
}

if ($expires_at < $now) {
    header('Location: ../login-page/validar_codigo.php?erro=codigo_invalido');
    exit;
}

// Verifica código
if (!password_verify((string)$codigo, $token_hash)) {
    // incrementa tentativas
    $upd = $conn->prepare('UPDATE password_resets SET attempts = attempts + 1 WHERE id = ?');
    if ($upd) {
        $upd->bind_param('i', $reset_id);
        $upd->execute();
    }
    header('Location: ../login-page/validar_codigo.php?erro=codigo_invalido');
    exit;
}

// Código válido — atualiza senha
$novaHash = password_hash($nova_senha, PASSWORD_DEFAULT);
$updUser = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
if (!$updUser) {
    header('Location: ../login-page/validar_codigo.php?erro=db_error');
    exit;
}
$updUser->bind_param('si', $novaHash, $user_id);
if (!$updUser->execute()) {
    header('Location: ../login-page/validar_codigo.php?erro=db_error');
    exit;
}

// Remove os tokens antigos do usuário
$del = $conn->prepare('DELETE FROM password_resets WHERE user_id = ?');
if ($del) {
    $del->bind_param('i', $user_id);
    $del->execute();
}

header('Location: ../login-page/index.php?sucesso_recuperacao=1');
exit;

?>
