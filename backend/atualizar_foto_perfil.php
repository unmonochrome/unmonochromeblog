<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login-page/index.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

if (!isset($_FILES["foto_perfil"]) || $_FILES["foto_perfil"]["error"] !== 0) {
    header("Location: ../landingpage/perfil.php?erro=upload_invalido");
    exit;
}

$arquivo = $_FILES["foto_perfil"];
$extensao = strtolower(pathinfo($arquivo["name"], PATHINFO_EXTENSION));
$extensoesPermitidas = ["jpg", "jpeg", "png", "webp", "gif"];

if (!in_array($extensao, $extensoesPermitidas)) {
    header("Location: ../landingpage/perfil.php?erro=formato_invalido");
    exit;
}

$nomeImagem = uniqid("perfil_", true) . "." . $extensao;
$caminhoDestino = "../landingpage/profile_pics/" . $nomeImagem;

if (!move_uploaded_file($arquivo["tmp_name"], $caminhoDestino)) {
    header("Location: ../landingpage/perfil.php?erro=erro_upload");
    exit;
}

$sql = "UPDATE usuarios SET foto_perfil = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $nomeImagem, $usuario_id);

if ($stmt->execute()) {
    header("Location: ../landingpage/perfil.php?sucesso=foto_atualizada");
    exit;
}


header("Location: ../landingpage/perfil.php?erro=erro_salvar");
exit;
?>