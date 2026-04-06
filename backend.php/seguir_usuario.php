<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login-page/index.php");
    exit;
}

if (!isset($_GET["id"])) {
    header("Location: ../landingpage/blog.php");
    exit;
}

$seguidor_id = (int) $_SESSION["usuario_id"];
$seguido_id = (int) $_GET["id"];

// impedir seguir a si mesmo
if ($seguidor_id === $seguido_id) {
    header("Location: ../landingpage/perfil.php?id=" . $seguido_id . "&erro=auto_follow");
    exit;
}

// verificar se o usuário que será seguido existe
$sqlUsuario = "SELECT id FROM usuarios WHERE id = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $seguido_id);
$stmtUsuario->execute();
$resUsuario = $stmtUsuario->get_result();

if ($resUsuario->num_rows === 0) {
    header("Location: ../landingpage/blog.php?erro=usuario_nao_encontrado");
    exit;
}

// verificar se já segue
$sqlVerifica = "SELECT id FROM seguidores WHERE seguidor_id = ? AND seguido_id = ?";
$stmtVerifica = $conn->prepare($sqlVerifica);
$stmtVerifica->bind_param("ii", $seguidor_id, $seguido_id);
$stmtVerifica->execute();
$resVerifica = $stmtVerifica->get_result();

if ($resVerifica->num_rows > 0) {
    // deixar de seguir
    $sqlDelete = "DELETE FROM seguidores WHERE seguidor_id = ? AND seguido_id = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("ii", $seguidor_id, $seguido_id);

    if ($stmtDelete->execute()) {
        header("Location: ../landingpage/perfil.php?id=" . $seguido_id . "&sucesso=unfollow");
        exit;
    } else {
        header("Location: ../landingpage/perfil.php?id=" . $seguido_id . "&erro=follow_fail");
        exit;
    }
} else {
    // seguir
    $sqlInsert = "INSERT INTO seguidores (seguidor_id, seguido_id) VALUES (?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("ii", $seguidor_id, $seguido_id);

    if ($stmtInsert->execute()) {
        header("Location: ../landingpage/perfil.php?id=" . $seguido_id . "&sucesso=follow");
        exit;
    } else {
        header("Location: ../landingpage/perfil.php?id=" . $seguido_id . "&erro=follow_fail");
        exit;
    }
}
?>