<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once "conexao.php";
require_once "csrf.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function ensureEmailColumnExists($conn) {
    $result = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'email'");
    if ($result === false) {
        die("Erro no banco de dados: " . $conn->error);
    }

    if ($result->num_rows === 0) {
        $alter = $conn->query("ALTER TABLE usuarios ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '' AFTER usuario");
        if ($alter === false) {
            die("Erro ao atualizar estrutura do banco: " . $conn->error);
        }
    }
}

ensureEmailColumnExists($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar CSRF
    $csrf_token = $_POST["csrf_token"] ?? "";
    if (!verifyCSRFToken($csrf_token)) {
        header("Location: ../login-page/cadastro.php?erro=csrf");
        exit;
    }

    $usuario = trim($_POST["usuario"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = trim($_POST["senha"] ?? "");
    $confirmar_senha = trim($_POST["confirmar_senha"] ?? "");

    if ($usuario === "" || $email === "" || $senha === "" || $confirmar_senha === "") {
        header("Location: ../login-page/cadastro.php?erro=2");
        exit;
    }

    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../login-page/cadastro.php?erro=email_invalido");
        exit;
    }

    // Validar comprimento do username
    if (strlen($usuario) < 3 || strlen($usuario) > 50) {
        header("Location: ../login-page/cadastro.php?erro=usuario_invalido");
        exit;
    }

    // Validar força da senha
    if (strlen($senha) < 6) {
        header("Location: ../login-page/cadastro.php?erro=senha_fraca");
        exit;
    }

    if ($senha !== $confirmar_senha) {
        header("Location: ../login-page/cadastro.php?erro=3");
        exit;
    }

    // Sanitizar username (apenas alphanumericos e underscore)
    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $usuario)) {
        header("Location: ../login-page/cadastro.php?erro=usuario_invalido");
        exit;
    }

    $verifica = "SELECT id FROM usuarios WHERE usuario = ? OR email = ?";
    $stmt = $conn->prepare($verifica);
    if (!$stmt) {
        header("Location: ../login-page/cadastro.php?erro=db_error");
        exit;
    }

    $stmt->bind_param("ss", $usuario, $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $registro = $resultado->fetch_assoc();
        // Verificar qual campo está duplicado
        $check_usuario = "SELECT id FROM usuarios WHERE usuario = ?";
        $stmt_check = $conn->prepare($check_usuario);
        $stmt_check->bind_param("s", $usuario);
        $stmt_check->execute();
        
        if ($stmt_check->get_result()->num_rows > 0) {
            header("Location: ../login-page/cadastro.php?erro=1");
        } else {
            header("Location: ../login-page/cadastro.php?erro=email_duplicado");
        }
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (usuario, email, senha, tipo) VALUES (?, ?, ?, 'usuario')";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: ../login-page/cadastro.php?erro=db_error");
        exit;
    }

    $stmt->bind_param("sss", $usuario, $email, $senhaHash);
    
    if ($stmt->execute()) {
        header("Location: ../login-page/index.php?cadastro=1");
        exit;
    } else {
        header("Location: ../login-page/cadastro.php?erro=db_error");
        exit;
    }
}
?>