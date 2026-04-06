<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login-page/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST["titulo"]);
    $conteudo = trim($_POST["conteudo"]);
    $usuario_id = $_SESSION["usuario_id"];
    $nomeImagem = null;

    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] === 0) {
        $extensao = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
        $nomeImagem = uniqid() . "." . $extensao;
        $caminhoDestino = "../landingpage/uploads/" . $nomeImagem;

        move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminhoDestino);
    }

    if ($titulo !== "" && $conteudo !== "") {
        $sql = "INSERT INTO posts (titulo, conteudo, usuario_id, imagem) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $titulo, $conteudo, $usuario_id, $nomeImagem);
        $stmt->execute();

        header("Location: ../landingpage/blog.php?sucesso=post_criado");
        exit;
    }

    header("Location: ../landingpage/blog.php?erro=post_vazio");
    exit;
}
?>