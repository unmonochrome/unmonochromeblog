<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login-page/index.php");
    exit;
}

if (!isset($_GET["post_id"])) {
    header("Location: ../landingpage/blog.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];
$post_id = intval($_GET["post_id"]);

// verifica se já curtiu
$sqlVerifica = "SELECT id FROM curtidas WHERE usuario_id = ? AND post_id = ?";
$stmtVerifica = $conn->prepare($sqlVerifica);
$stmtVerifica->bind_param("ii", $usuario_id, $post_id);
$stmtVerifica->execute();
$resultado = $stmtVerifica->get_result();

if ($resultado->num_rows > 0) {
    // descurtir
    $sqlDelete = "DELETE FROM curtidas WHERE usuario_id = ? AND post_id = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("ii", $usuario_id, $post_id);
    $stmtDelete->execute();
} else {
    // curtir
    $sqlInsert = "INSERT INTO curtidas (usuario_id, post_id) VALUES (?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("ii", $usuario_id, $post_id);
    $stmtInsert->execute();
}

// volta pro blog
header("Location: ../landingpage/blog.php");
exit;
?>