<?php
session_start();
require_once "conexao.php";
require_once "csrf.php";
require_once "upload_validator.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login-page/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar CSRF
    $csrf_token = $_POST["csrf_token"] ?? "";
    if (!verifyCSRFToken($csrf_token)) {
        header("Location: ../landingpage/blog.php?erro=csrf");
        exit;
    }

    $titulo = trim($_POST["titulo"] ?? "");
    $conteudo = trim($_POST["conteudo"] ?? "");
    $usuario_id = $_SESSION["usuario_id"];
    $nomeImagem = null;

    // Validar campos obrigatórios
    if (empty($titulo) || empty($conteudo)) {
        header("Location: ../landingpage/blog.php?erro=post_vazio");
        exit;
    }

    // Limitar tamanho de texto
    if (strlen($titulo) > 200 || strlen($conteudo) > 5000) {
        header("Location: ../landingpage/blog.php?erro=texto_muito_grande");
        exit;
    }

    // Validar imagem se fornecida
    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $uploadResult = UploadValidator::validate($_FILES["imagem"]);
        if (!$uploadResult['valid']) {
            header("Location: ../landingpage/blog.php?erro=" . urlencode($uploadResult['error']));
            exit;
        }
        
        $nomeImagem = $uploadResult['filename'];
        $caminhoDestino = "../landingpage/uploads/" . $nomeImagem;
        
        if (!move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminhoDestino)) {
            header("Location: ../landingpage/blog.php?erro=erro_upload");
            exit;
        }
    }

    // Sanitizar input para prevenir XSS
    $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
    $conteudo = htmlspecialchars($conteudo, ENT_QUOTES, 'UTF-8');

    $sql = "INSERT INTO posts (titulo, conteudo, usuario_id, imagem) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: ../landingpage/blog.php?erro=db_error");
        exit;
    }

    $stmt->bind_param("ssis", $titulo, $conteudo, $usuario_id, $nomeImagem);
    if (!$stmt->execute()) {
        header("Location: ../landingpage/blog.php?erro=db_error");
        exit;
    }

    header("Location: ../landingpage/blog.php?sucesso=post_criado");
    exit;
}
?>