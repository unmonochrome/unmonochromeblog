<?php
$host = "sql104.infinityfree.com";
$usuario = "if0_41588733";
$senha = "YORRAjudeu123";
$banco = "if0_41588733_unmonochrome"; // IMPORTANTE: trocar pelo nome real do banco

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>