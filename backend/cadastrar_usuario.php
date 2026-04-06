<?php
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $senha = trim($_POST["senha"] ?? "");
    $confirmar_senha = trim($_POST["confirmar_senha"] ?? "");

    if ($usuario === "" || $senha === "" || $confirmar_senha === "") {
        header("Location: ../login-page/cadastro.php?erro=2");
        exit;
    }

    if ($senha !== $confirmar_senha) {
        header("Location: ../login-page/cadastro.php?erro=3");
        exit;
    }

    $verifica = "SELECT id FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($verifica);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        header("Location: ../login-page/cadastro.php?erro=1");
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (usuario, senha, tipo) VALUES (?, ?, 'usuario')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $senhaHash);
    $stmt->execute();

    header("Location: ../login-page/index.php?cadastro=1");
    exit;
}
?>