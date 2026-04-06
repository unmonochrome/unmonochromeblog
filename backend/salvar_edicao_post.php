<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login-page/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST["id"]);
    $titulo = trim($_POST["titulo"]);
    $conteudo = trim($_POST["conteudo"]);
    $usuario_id = $_SESSION["usuario_id"];
    $usuario_tipo = $_SESSION["usuario_tipo"];

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

    if ($usuario_tipo !== "admin" && $post["usuario_id"] != $usuario_id) {
        header("Location: ../landingpage/blog.php?erro=sem_permissao");
        exit;
    }

    $nomeImagem = $post["imagem"];

    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] === 0) {
        $extensao = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
        $nomeImagem = uniqid() . "." . $extensao;
        $caminhoDestino = "../landingpage/uploads/" . $nomeImagem;
        move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminhoDestino);
    }

    $sql = "UPDATE posts SET titulo = ?, conteudo = ?, imagem = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $titulo, $conteudo, $nomeImagem, $id);
    $stmt->execute();

    header("Location: ../landingpage/blog.php?sucesso=post_editado");
    exit;
}
?>