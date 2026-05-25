<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';

require_once 'conexao.php';
require_once 'csrf.php';

// ======================================
// VERIFICA POST
// ======================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login-page/recuperar_senha.php');
    exit;
}

// ======================================
// CSRF
// ======================================

$csrf = $_POST['csrf_token'] ?? '';

if (!verifyCSRFToken($csrf)) {

    header('Location: ../login-page/recuperar_senha.php?erro=csrf');
    exit;
}

// ======================================
// CAMPOS
// ======================================

$usuario = trim($_POST['usuario'] ?? '');
$email = trim($_POST['email'] ?? '');

if (
    empty($usuario) ||
    empty($email)
) {

    header('Location: ../login-page/recuperar_senha.php?erro=campos_vazios');
    exit;
}

// ======================================
// BUSCA USUÁRIO
// ======================================

$stmt = $conn->prepare("
    SELECT id
    FROM usuarios
    WHERE usuario = ?
    AND email = ?
    LIMIT 1
");

$stmt->bind_param(
    'ss',
    $usuario,
    $email
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    header('Location: ../login-page/recuperar_senha.php?erro=email_invalido');
    exit;
}

$user = $result->fetch_assoc();

$user_id = $user['id'];

// ======================================
// TABELA RESET
// ======================================

$conn->query("
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

// ======================================
// GERA CÓDIGO
// ======================================

$codigo = random_int(100000, 999999);

$hash = password_hash(
    $codigo,
    PASSWORD_DEFAULT
);

$expires = date(
    'Y-m-d H:i:s',
    time() + 900
);

// ======================================
// SALVA
// ======================================

$insert = $conn->prepare("
INSERT INTO password_resets
(user_id, token_hash, expires_at)
VALUES (?, ?, ?)
");

$insert->bind_param(
    'iss',
    $user_id,
    $hash,
    $expires
);

$insert->execute();

// ======================================
// ENVIA EMAIL
// ======================================

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username = 'unmonochrome@gmail.com';

    // SENHA APP GOOGLE
    $mail->Password = 'moml hdpk duub nmwq';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    $mail->Port = 465;

    $mail->CharSet = 'UTF-8';

    $mail->setFrom(
        'unmonochrome@gmail.com',
        'UNMONOCHROME'
    );

    $mail->addAddress($email);

    $mail->isHTML(true);

    $mail->Subject = 'Código de recuperação';

    $mail->Body = "
        <h2>Recuperação de senha</h2>

        <p>Seu código é:</p>

        <h1>$codigo</h1>

        <p>Expira em 15 minutos.</p>
    ";

    $mail->AltBody = "
        Seu código é: $codigo
    ";

    $mail->send();

    $_SESSION['reset_email'] = $email;

    header('Location: ../login-page/validar_codigo.php?sucesso=1');
    exit;

} catch (Exception $e) {

    echo "
    <h1>ERRO AO ENVIAR EMAIL</h1>

    <pre>
    {$mail->ErrorInfo}
    </pre>
    ";

}
?>