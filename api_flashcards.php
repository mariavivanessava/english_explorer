<?php
require_once 'config.php';

header('Content-Type: application/json');

$acao = $_GET['acao'] ?? '';

if ($acao === 'listar') {
    $tema = $_GET['tema'] ?? '';
    
    // Buscar flashcards do tema selecionado
    $stmt = $pdo->prepare("SELECT id, palavra, traducao FROM palavras WHERE tema = ? ORDER BY id");
    $stmt->execute([$tema]);
    $flashcards = $stmt->fetchAll();
    
    echo json_encode(['sucesso' => true, 'flashcards' => $flashcards]);
    
} elseif ($acao === 'temas') {
    // Buscar todos os temas disponíveis
    $stmt = $pdo->query("SELECT DISTINCT tema FROM palavras WHERE tema IS NOT NULL AND tema != '' ORDER BY tema");
    $temas = $stmt->fetchAll();
    
    echo json_encode(['sucesso' => true, 'temas' => $temas]);
    
} elseif ($acao === 'personalizados') {
    // Buscar flashcards personalizados do usuário
    $stmt = $pdo->prepare("SELECT id, palavra, traducao, tema FROM flashcards_personalizados WHERE usuario_id = ? ORDER BY data_criacao DESC");
    $stmt->execute([$_SESSION['usuario_id']]);
    $flashcards = $stmt->fetchAll();
    
    echo json_encode(['sucesso' => true, 'flashcards' => $flashcards]);
    
} elseif ($acao === 'criar') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $palavra = trim($dados['palavra'] ?? '');
    $traducao = trim($dados['traducao'] ?? '');
    $tema = trim($dados['tema'] ?? 'Personalizado');
    
    if (empty($palavra) || empty($traducao)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Palavra e tradução são obrigatórios']);
        exit;
    }
    
    $stmt = $pdo->prepare("INSERT INTO flashcards_personalizados (usuario_id, palavra, traducao, tema) VALUES (?, ?, ?, ?)");
    $sucesso = $stmt->execute([$_SESSION['usuario_id'], $palavra, $traducao, $tema]);
    
    echo json_encode(['sucesso' => $sucesso]);
    
} elseif ($acao === 'criar_grupo') {
    // Criar múltiplos flashcards E já agrupá-los automaticamente
    $dados = json_decode(file_get_contents('php://input'), true);
    $palavras = $dados['palavras'] ?? [];
    $tema = trim($dados['tema'] ?? 'Personalizado');
    
    if (empty($palavras)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhuma palavra para adicionar']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // 1. Criar o grupo automaticamente
        $stmt = $pdo->prepare("INSERT INTO grupos (usuario_id, nome, descricao) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['usuario_id'], $tema, "Flashcards do tema: $tema"]);
        $grupo_id = $pdo->lastInsertId();
        
        // 2. Criar cada flashcard e já adicionar ao grupo
        $sucessos = 0;
        $erros = [];
        
        $stmtFlashcard = $pdo->prepare("INSERT INTO flashcards_personalizados (usuario_id, palavra, traducao, tema) VALUES (?, ?, ?, ?)");
        $stmtGrupoFlashcard = $pdo->prepare("INSERT INTO grupos_flashcards (grupo_id, flashcard_id) VALUES (?, ?)");
        
        foreach ($palavras as $item) {
            $palavra = trim($item['palavra'] ?? '');
            $traducao = trim($item['traducao'] ?? '');
            
            if (!empty($palavra) && !empty($traducao)) {
                // Criar o flashcard
                $stmtFlashcard->execute([$_SESSION['usuario_id'], $palavra, $traducao, $tema]);
                $flashcard_id = $pdo->lastInsertId();
                
                // Adicionar ao grupo
                $stmtGrupoFlashcard->execute([$grupo_id, $flashcard_id]);
                
                $sucessos++;
            } else {
                $erros[] = "Palavra inválida: '$palavra'";
            }
        }
        
        $pdo->commit();
        
        echo json_encode([
            'sucesso' => $sucessos > 0,
            'adicionados' => $sucessos,
            'total' => count($palavras),
            'erros' => $erros,
            'grupo_id' => $grupo_id,
            'grupo_nome' => $tema,
            'mensagem' => "✅ Grupo '$tema' criado com $sucessos flashcards!"
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
    }
    
} elseif ($acao === 'deletar') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $id = $dados['id'] ?? 0;
    
    $stmt = $pdo->prepare("DELETE FROM flashcards_personalizados WHERE id = ? AND usuario_id = ?");
    $sucesso = $stmt->execute([$id, $_SESSION['usuario_id']]);
    
    echo json_encode(['sucesso' => $sucesso]);
    
} elseif ($acao === 'deletar_todos') {
    // Deletar todos os flashcards personalizados de um tema
    $dados = json_decode(file_get_contents('php://input'), true);
    $tema = trim($dados['tema'] ?? '');
    
    if (empty($tema)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Tema não especificado']);
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM flashcards_personalizados WHERE usuario_id = ? AND tema = ?");
    $sucesso = $stmt->execute([$_SESSION['usuario_id'], $tema]);
    
    echo json_encode(['sucesso' => $sucesso, 'deletados' => $stmt->rowCount()]);
}
?>