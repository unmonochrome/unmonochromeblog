<?php
// Mostrar erros para depuração do servidor
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Carrega variáveis de ambiente de um arquivo .env simples
$env_file = __DIR__ . "/../.env";
$defaultLocal = [
    'DB_HOST' => '127.0.0.1',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'DB_NAME' => 'unmonochrome',
];
$defaultRemote = [
    'DB_HOST' => 'sql104.infinityfree.com',
    'DB_USER' => 'if0_41588733',
    'DB_PASS' => 'YORRAjudeu123',
    'DB_NAME' => 'if0_41588733_unmonochrome',
];
$env = [];

if (file_exists($env_file) && is_readable($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = preg_replace('/^\xEF\xBB\xBF/', '', $line);
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || $line[0] === ';') {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);
            if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
                $value = substr($value, 1, -1);
            }

            $env[$key] = $value;
        }
    } else {
        error_log('[conexao.php] .env existe mas não pôde ser lido.');
    }
}

$host = $env['DB_HOST'] ?? getenv('DB_HOST');
$usuario = $env['DB_USER'] ?? getenv('DB_USER');
$senha = $env['DB_PASS'] ?? getenv('DB_PASS');
$banco = $env['DB_NAME'] ?? getenv('DB_NAME');

if (!$host || !$usuario || !$banco) {
    if (file_exists($env_file)) {
        error_log('[conexao.php] .env presente mas com valores incompletos. Usando fallback remoto conhecido.');
        $host = $host ?: $defaultRemote['DB_HOST'];
        $usuario = $usuario ?: $defaultRemote['DB_USER'];
        $senha = $senha ?: $defaultRemote['DB_PASS'];
        $banco = $banco ?: $defaultRemote['DB_NAME'];
    } else {
        error_log('[conexao.php] .env ausente. Usando fallback XAMPP local.');
        $host = $host ?: $defaultLocal['DB_HOST'];
        $usuario = $usuario ?: $defaultLocal['DB_USER'];
        $senha = $senha ?: $defaultLocal['DB_PASS'];
        $banco = $banco ?: $defaultLocal['DB_NAME'];
    }
}

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

function ensureColumnExists($conn, $table, $column, $definition) {
    // SHOW COLUMNS não aceita marcador de posição para a cláusula LIKE no MariaDB.
    $tableEscaped = $conn->real_escape_string($table);
    $columnEscaped = $conn->real_escape_string($column);
    $sql = "SHOW COLUMNS FROM `$tableEscaped` LIKE '$columnEscaped'";

    $res = $conn->query($sql);
    if ($res === false) {
        die('Erro ao executar SHOW COLUMNS: ' . $conn->error);
    }
    if ($res->num_rows === 0) {
        $alter = $conn->query("ALTER TABLE `$tableEscaped` ADD COLUMN `$columnEscaped` $definition");
        if ($alter === false) {
            die('Erro ao alterar tabela: ' . $conn->error);
        }
    }
    return true;
}

ensureColumnExists($conn, 'usuarios', 'email', "VARCHAR(255) NOT NULL DEFAULT '' AFTER usuario");
ensureColumnExists($conn, 'usuarios', 'tipo', "VARCHAR(20) NOT NULL DEFAULT 'usuario' AFTER senha");

$conn->set_charset("utf8");
?>