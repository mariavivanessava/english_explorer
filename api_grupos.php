<?php
require_once 'config.php';

header('Content-Type: application/json');

$acao = $_GET['acao'] ?? '';

if ($acao === 'listar') {
    // Listar todos os grupos do usuário
    $stmt = $pdo->prepare("
        SELECT g.*, 
               COUNT(gf.flashcard_id) as total_flashcards,
               MAX(fp.data_criacao) as ultima_atividade
        FROM grupos g
        LEFT JOIN grupos_flashcards gf ON g.id = gf.grupo_id
        LEFT JOIN flashcards_personalizados fp ON gf.flashcard_id = fp.id
        WHERE g.usuario_id = ?
        GROUP BY g.id
        ORDER BY g.data_criacao DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['sucesso' => true, 'grupos' => $grupos]);
    
} elseif ($acao === 'criar') {
    // Criar novo grupo
    $dados = json_decode(file_get_contents('php://input'), true);
    $nome = trim($dados['nome'] ?? '');
    $descricao = trim($dados['descricao'] ?? '');
    $flashcards_ids = $dados['flashcards_ids'] ?? [];
    
    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nome do grupo é obrigatório']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Criar o grupo
        $stmt = $pdo->prepare("INSERT INTO grupos (usuario_id, nome, descricao) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['usuario_id'], $nome, $descricao]);
        $grupo_id = $pdo->lastInsertId();
        
        // Adicionar flashcards ao grupo
        if (!empty($flashcards_ids)) {
            $stmt = $pdo->prepare("INSERT INTO grupos_flashcards (grupo_id, flashcard_id) VALUES (?, ?)");
            foreach ($flashcards_ids as $flashcard_id) {
                // Verificar se o flashcard pertence ao usuário
                $check = $pdo->prepare("SELECT id FROM flashcards_personalizados WHERE id = ? AND usuario_id = ?");
                $check->execute([$flashcard_id, $_SESSION['usuario_id']]);
                if ($check->fetch()) {
                    $stmt->execute([$grupo_id, $flashcard_id]);
                }
            }
        }
        
        $pdo->commit();
        echo json_encode(['sucesso' => true, 'grupo_id' => $grupo_id, 'mensagem' => "Grupo '$nome' criado com sucesso!"]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao criar grupo: ' . $e->getMessage()]);
    }
    
} elseif ($acao === 'carregar') {
    // Carregar flashcards de um grupo específico
    $grupo_id = $_GET['id'] ?? 0;
    
    $stmt = $pdo->prepare("
        SELECT fp.id, fp.palavra, fp.traducao, fp.tema
        FROM flashcards_personalizados fp
        JOIN grupos_flashcards gf ON fp.id = gf.flashcard_id
        WHERE gf.grupo_id = ? AND fp.usuario_id = ?
        ORDER BY fp.data_criacao
    ");
    $stmt->execute([$grupo_id, $_SESSION['usuario_id']]);
    $flashcards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['sucesso' => true, 'flashcards' => $flashcards]);
    
} elseif ($acao === 'deletar') {
    // Deletar um grupo (não deleta os flashcards)
    $dados = json_decode(file_get_contents('php://input'), true);
    $grupo_id = $dados['id'] ?? 0;
    
    // Verificar se o grupo pertence ao usuário
    $stmt = $pdo->prepare("SELECT id FROM grupos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$grupo_id, $_SESSION['usuario_id']]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Grupo não encontrado']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Remover relações
        $stmt = $pdo->prepare("DELETE FROM grupos_flashcards WHERE grupo_id = ?");
        $stmt->execute([$grupo_id]);
        
        // Remover grupo
        $stmt = $pdo->prepare("DELETE FROM grupos WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$grupo_id, $_SESSION['usuario_id']]);
        
        $pdo->commit();
        echo json_encode(['sucesso' => true, 'mensagem' => 'Grupo excluído com sucesso!']);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir grupo']);
    }
    
} elseif ($acao === 'adicionar_flashcard') {
    // Adicionar um flashcard existente a um grupo
    $dados = json_decode(file_get_contents('php://input'), true);
    $grupo_id = $dados['grupo_id'] ?? 0;
    $flashcard_id = $dados['flashcard_id'] ?? 0;
    
    // Verificar se o grupo pertence ao usuário
    $stmt = $pdo->prepare("SELECT id FROM grupos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$grupo_id, $_SESSION['usuario_id']]);
    if (!$stmt->fetch()) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Grupo não encontrado']);
        exit;
    }
    
    // Verificar se o flashcard pertence ao usuário
    $stmt = $pdo->prepare("SELECT id FROM flashcards_personalizados WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$flashcard_id, $_SESSION['usuario_id']]);
    if (!$stmt->fetch()) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Flashcard não encontrado']);
        exit;
    }
    
    // Verificar se já existe
    $stmt = $pdo->prepare("SELECT id FROM grupos_flashcards WHERE grupo_id = ? AND flashcard_id = ?");
    $stmt->execute([$grupo_id, $flashcard_id]);
    if ($stmt->fetch()) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Flashcard já está neste grupo']);
        exit;
    }
    
    $stmt = $pdo->prepare("INSERT INTO grupos_flashcards (grupo_id, flashcard_id) VALUES (?, ?)");
    $stmt->execute([$grupo_id, $flashcard_id]);
    
    echo json_encode(['sucesso' => true, 'mensagem' => 'Flashcard adicionado ao grupo!']);
}
?>