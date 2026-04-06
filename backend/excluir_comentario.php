<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login-page/index.php");
    exit;
}

$id = intval($_GET["id"]);
$usuario_id = $_SESSION["usuario_id"];
$usuario_tipo = $_SESSION["usuario_tipo"];

if ($usuario_tipo === "admin") {
    $sql = "DELETE FROM comentarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
} else {
    $sql = "DELETE FROM comentarios WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
}

header("Location: ../landingpage/blog.php");
exit;
?>