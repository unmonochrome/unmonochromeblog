<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login-page/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conteudo = trim($_POST["conteudo"]);
    $post_id = intval($_POST["post_id"]);
    $usuario_id = $_SESSION["usuario_id"];

    if ($conteudo !== "") {
        $sql = "INSERT INTO comentarios (conteudo, usuario_id, post_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $conteudo, $usuario_id, $post_id);
        $stmt->execute();

        header("Location: ../landingpage/blog.php?sucesso=comentario_criado");
        exit;
    }
}

header("Location: ../landingpage/blog.php?erro=comentario_vazio");
exit;
?>