<?php
session_start();
require_once "conexao.php";
require_once "csrf.php";
require_once "upload_validator.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login-page/index.php");
    exit;
}

// Verificar CSRF
$csrf_token = $_POST["csrf_token"] ?? "";
if (!verifyCSRFToken($csrf_token)) {
    header("Location: ../landingpage/perfil.php?erro=csrf");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

if (!isset($_FILES["foto_perfil"]) || $_FILES["foto_perfil"]["error"] !== UPLOAD_ERR_OK) {
    header("Location: ../landingpage/perfil.php?erro=upload_invalido");
    exit;
}

// Validar upload
$uploadResult = UploadValidator::validate($_FILES["foto_perfil"]);
if (!$uploadResult['valid']) {
    header("Location: ../landingpage/perfil.php?erro=" . urlencode($uploadResult['error']));
    exit;
}

$nomeImagem = $uploadResult['filename'];
$caminhoDestino = "../landingpage/profile_pics/" . $nomeImagem;

if (!move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $caminhoDestino)) {
    header("Location: ../landingpage/perfil.php?erro=erro_upload");
    exit;
}

// Buscar foto antiga para deletar
$sqlOld = "SELECT foto_perfil FROM usuarios WHERE id = ?";
$stmtOld = $conn->prepare($sqlOld);
$stmtOld->bind_param("i", $usuario_id);
$stmtOld->execute();
$resOld = $stmtOld->get_result();
$userOld = $resOld->fetch_assoc();

// Atualizar no banco
$sql = "UPDATE usuarios SET foto_perfil = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header("Location: ../landingpage/perfil.php?erro=db_error");
    exit;
}

$stmt->bind_param("si", $nomeImagem, $usuario_id);

if ($stmt->execute()) {
    // Deletar foto antiga se existia
    if ($userOld['foto_perfil'] && file_exists("../landingpage/profile_pics/" . $userOld['foto_perfil'])) {
        unlink("../landingpage/profile_pics/" . $userOld['foto_perfil']);
    }
    
    header("Location: ../landingpage/perfil.php?sucesso=foto_atualizada");
    exit;
} else {
    // Se falhar, deletar arquivo que foi upado
    if (file_exists($caminhoDestino)) {
        unlink($caminhoDestino);
    }
    header("Location: ../landingpage/perfil.php?erro=erro_salvar");
    exit;
}
?>