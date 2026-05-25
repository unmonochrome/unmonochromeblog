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

    $id = intval($_POST["id"] ?? 0);
    $titulo = trim($_POST["titulo"] ?? "");
    $conteudo = trim($_POST["conteudo"] ?? "");
    $usuario_id = $_SESSION["usuario_id"];
    $usuario_tipo = $_SESSION["usuario_tipo"];

    if (empty($id) || empty($titulo) || empty($conteudo)) {
        header("Location: ../landingpage/blog.php?erro=campos_vazios");
        exit;
    }

    $sqlVerifica = "SELECT * FROM posts WHERE id = ?";
    $stmtVerifica = $conn->prepare($sqlVerifica);
    $stmtVerifica->bind_param("i", $id);
    $stmtVerifica->execute();
    $resultado = $stmtVerifica->get_result();

    if ($resultado->num_rows === 0) {
        header("Location: ../landingpage/blog.php?erro=post_nao_encontrado");
        exit;
    }

    $post = $resultado->fetch_assoc();

    // Usar === para comparação type-safe
    if ($usuario_tipo !== "admin" && $post["usuario_id"] !== $usuario_id) {
        header("Location: ../landingpage/blog.php?erro=sem_permissao");
        exit;
    }

    $nomeImagem = $post["imagem"];
    $imagemOld = $post["imagem"];

    // Validar imagem se fornecida
    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $uploadResult = UploadValidator::validate($_FILES["imagem"]);
        if (!$uploadResult['valid']) {
            header("Location: ../editar_post.php?id=" . $id . "&erro=" . urlencode($uploadResult['error']));
            exit;
        }
        
        $nomeImagem = $uploadResult['filename'];
        $caminhoDestino = "../landingpage/uploads/" . $nomeImagem;
        
        if (!move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminhoDestino)) {
            header("Location: ../editar_post.php?id=" . $id . "&erro=erro_upload");
            exit;
        }
    }

    // Sanitizar input
    $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
    $conteudo = htmlspecialchars($conteudo, ENT_QUOTES, 'UTF-8');

    $sql = "UPDATE posts SET titulo = ?, conteudo = ?, imagem = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: ../editar_post.php?id=" . $id . "&erro=db_error");
        exit;
    }

    $stmt->bind_param("sssi", $titulo, $conteudo, $nomeImagem, $id);
    
    if ($stmt->execute()) {
        // Deletar imagem antiga se nova foi upada
        if ($nomeImagem !== $imagemOld && $imagemOld && file_exists("../landingpage/uploads/" . $imagemOld)) {
            unlink("../landingpage/uploads/" . $imagemOld);
        }
        
        header("Location: ../landingpage/blog.php?sucesso=post_editado");
        exit;
    } else {
        // Se falhar, deletar arquivo que foi upado
        if ($nomeImagem !== $imagemOld && file_exists("../landingpage/uploads/" . $nomeImagem)) {
            unlink("../landingpage/uploads/" . $nomeImagem);
        }
        header("Location: ../editar_post.php?id=" . $id . "&erro=db_error");
        exit;
    }
}
?>