<?php
// Salva preferências de acessibilidade na sessão do usuário
session_start();
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
if (!$raw) {
    echo json_encode(['success' => false, 'error' => 'no_input']);
    exit;
}

$data = json_decode($raw, true);
if (!is_array($data)) {
    echo json_encode(['success' => false, 'error' => 'invalid_json']);
    exit;
}

$allowed = ['large','contrast','motion','spacing'];
$prefs = [];
foreach ($allowed as $k) {
    $prefs[$k] = !empty($data[$k]) ? true : false;
}

// Store in session
$_SESSION['a11y_prefs'] = $prefs;

echo json_encode(['success' => true]);
exit;

?>
