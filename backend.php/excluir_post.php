<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login-page/index.php");
    exit;
}

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: ../landingpage/blog.php?erro=id_invalido");
    exit;
}

$id = intval($_GET["id"]);
$usuario_id = $_SESSION["usuario_id"];
$usuario_tipo = $_SESSION["usuario_tipo"] ?? "usuario";

if ($usuario_tipo === "admin") {
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro no prepare: " . $conn->error);
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        die("Erro ao executar: " . $stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        header("Location: ../landingpage/blog.php?sucesso=post_excluido");
    } else {
        header("Location: ../landingpage/blog.php?erro=post_nao_encontrado");
    }
    exit;

} else {
    $sql = "DELETE FROM posts WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro no prepare: " . $conn->error);
    }

    $stmt->bind_param("ii", $id, $usuario_id);

    if (!$stmt->execute()) {
        die("Erro ao executar: " . $stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        header("Location: ../landingpage/blog.php?sucesso=post_excluido");
    } else {
        header("Location: ../landingpage/blog.php?erro=sem_permissao");
    }
    exit;
}
?>