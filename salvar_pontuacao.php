<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuario nao autenticado.']);
    exit;
}

include_once 'config.php';

$usuarioId = $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? null;

if (!$usuarioId) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuario nao encontrado.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
$jogo = $dados['jogo'] ?? '';
$pontuacao = filter_var($dados['pontuacao'] ?? null, FILTER_VALIDATE_INT);

if ($jogo !== 'translate_master' || $pontuacao === false || $pontuacao < 0) {
    http_response_code(422);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Pontuacao invalida.']);
    exit;
}

$pontuacao = min($pontuacao, 100);

$stmt = $pdo->prepare("
    UPDATE usuarios
    SET pontuacao_translate_master = GREATEST(COALESCE(pontuacao_translate_master, 0), ?)
    WHERE id = ?
");
$stmt->execute([$pontuacao, $usuarioId]);

$stmt = $pdo->prepare("SELECT pontuacao, pontuacao_translate_master FROM usuarios WHERE id = ?");
$stmt->execute([$usuarioId]);
$usuario = $stmt->fetch();

$pontuacaoQuizz = (int)($usuario['pontuacao'] ?? 0);
$pontuacaoTranslate = (int)($usuario['pontuacao_translate_master'] ?? 0);

echo json_encode([
    'sucesso' => true,
    'pontuacao_quizz' => $pontuacaoQuizz,
    'pontuacao_translate_master' => $pontuacaoTranslate,
    'pontuacao_total' => $pontuacaoQuizz + $pontuacaoTranslate
]);
