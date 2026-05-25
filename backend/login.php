<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();
require_once "conexao.php";
require_once "csrf.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar CSRF token
    $csrf_token = $_POST["csrf_token"] ?? "";
    if (!verifyCSRFToken($csrf_token)) {
        header("Location: ../login-page/index.php?erro=csrf");
        exit;
    }

    $usuario = trim($_POST["usuario"] ?? "");
    $senha = trim($_POST["senha"] ?? "");

    if (empty($usuario) || empty($senha)) {
        header("Location: ../login-page/index.php?erro=campos_vazios");
        exit;
    }

    // Permitir login por usuario OU email
    $sql = "SELECT * FROM usuarios WHERE usuario = ? OR email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: ../login-page/index.php?erro=db_error");
        exit;
    }
    
    $stmt->bind_param("ss", $usuario, $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $user = $resultado->fetch_assoc();

        if (password_verify($senha, $user["senha"])) {
            // IMPORTANTE: Regenerar session ID para evitar session fixation
            session_regenerate_id(true);
            
            $_SESSION["usuario_id"] = $user["id"];
            $_SESSION["usuario_nome"] = $user["usuario"];
            $_SESSION["usuario_tipo"] = $user["tipo"];

            header("Location: ../landingpage/blog.php");
            exit;
        }
    }

    header("Location: ../login-page/index.php?erro=1");
    exit;
}
?>