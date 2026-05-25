<?php
session_start();
require_once "conexao.php";
require_once "csrf.php";

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

    $conteudo = trim($_POST["conteudo"] ?? "");
    $post_id = intval($_POST["post_id"] ?? 0);
    $usuario_id = $_SESSION["usuario_id"];

    if (empty($conteudo)) {
        header("Location: ../landingpage/blog.php?erro=comentario_vazio");
        exit;
    }

    // Validar comprimento
    if (strlen($conteudo) > 1000) {
        header("Location: ../landingpage/blog.php?erro=comentario_muito_grande");
        exit;
    }

    // Validar se post existe
    $sqlPost = "SELECT id FROM posts WHERE id = ?";
    $stmtPost = $conn->prepare($sqlPost);
    $stmtPost->bind_param("i", $post_id);
    $stmtPost->execute();
    $resPost = $stmtPost->get_result();

    if ($resPost->num_rows === 0) {
        header("Location: ../landingpage/blog.php?erro=post_nao_encontrado");
        exit;
    }

    // Sanitizar conteúdo
    $conteudo = htmlspecialchars($conteudo, ENT_QUOTES, 'UTF-8');

    $sql = "INSERT INTO comentarios (conteudo, usuario_id, post_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: ../landingpage/blog.php?erro=db_error");
        exit;
    }

    $stmt->bind_param("sii", $conteudo, $usuario_id, $post_id);
    
    if ($stmt->execute()) {
        header("Location: ../landingpage/blog.php?sucesso=comentario_criado");
        exit;
    } else {
        header("Location: ../landingpage/blog.php?erro=db_error");
        exit;
    }
}

header("Location: ../landingpage/blog.php?erro=comentario_vazio");
exit;
?>