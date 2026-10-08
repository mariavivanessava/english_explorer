<?php
// ==========================================
// CONFIGURAÇÃO DO BANCO DE DADOS
// ==========================================
$host = 'localhost';
$dbname = 'englishexplorer';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// ==========================================
// INICIAR SESSÃO (ADICIONADO AQUI)
// ==========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// CRIAR TABELAS AUTOMATICAMENTE SE NÃO EXISTIREM
// ==========================================
try {
    // Tabela palavras (dicionário)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS palavras (
            id INT AUTO_INCREMENT PRIMARY KEY,
            palavra VARCHAR(100) NOT NULL,
            traducao VARCHAR(100) NOT NULL,
            tema VARCHAR(100),
            data_adicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabela flashcards personalizados
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS flashcards_personalizados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            palavra VARCHAR(100) NOT NULL,
            traducao VARCHAR(100) NOT NULL,
            tema VARCHAR(100) DEFAULT 'Personalizado',
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabela usuarios
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome_completo VARCHAR(100) NOT NULL,
            apelido VARCHAR(50) UNIQUE,
            email VARCHAR(150) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            pontuacao INT DEFAULT 0,
            pontuacao_translate_master INT DEFAULT 0,
            nivel INT DEFAULT 1,
            data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabela grupos
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS grupos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    // Tabela grupos_flashcards
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS grupos_flashcards (
            id INT AUTO_INCREMENT PRIMARY KEY,
            grupo_id INT NOT NULL,
            flashcard_id INT NOT NULL,
            data_adicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE,
            FOREIGN KEY (flashcard_id) REFERENCES flashcards_personalizados(id) ON DELETE CASCADE,
            UNIQUE KEY unique_grupo_flashcard (grupo_id, flashcard_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    // Inserir palavras iniciais se a tabela estiver vazia
    $stmt = $pdo->query("SELECT COUNT(*) FROM palavras");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("
            INSERT INTO palavras (palavra, traducao, tema) VALUES
            ('Hello', 'Olá', 'Saudações'),
            ('Good morning', 'Bom dia', 'Saudações'),
            ('Good afternoon', 'Boa tarde', 'Saudações'),
            ('Good evening', 'Boa noite', 'Saudações'),
            ('Good night', 'Boa noite (ao dormir)', 'Saudações'),
            ('Hi', 'Oi', 'Saudações'),
            ('Hey', 'Ei', 'Saudações'),
            ('How are you?', 'Como você está?', 'Saudações'),
            ('I''m fine', 'Estou bem', 'Saudações'),
            ('Thanks', 'Obrigado', 'Saudações'),
            ('Thank you', 'Muito obrigado', 'Saudações'),
            ('You''re welcome', 'De nada', 'Saudações'),
            ('Sorry', 'Desculpe', 'Saudações'),
            ('Excuse me', 'Com licença', 'Saudações'),
            ('See you later', 'Até mais', 'Saudações'),
            ('Dog', 'Cachorro', 'Animais'),
            ('Cat', 'Gato', 'Animais'),
            ('Bird', 'Pássaro', 'Animais'),
            ('Fish', 'Peixe', 'Animais'),
            ('Lion', 'Leão', 'Animais'),
            ('Tiger', 'Tigre', 'Animais'),
            ('Elephant', 'Elefante', 'Animais'),
            ('Giraffe', 'Girafa', 'Animais'),
            ('Monkey', 'Macaco', 'Animais'),
            ('Snake', 'Cobra', 'Animais'),
            ('Horse', 'Cavalo', 'Animais'),
            ('Cow', 'Vaca', 'Animais'),
            ('Pig', 'Porco', 'Animais'),
            ('Duck', 'Pato', 'Animais'),
            ('Rabbit', 'Coelho', 'Animais'),
            ('Red', 'Vermelho', 'Cores'),
            ('Blue', 'Azul', 'Cores'),
            ('Green', 'Verde', 'Cores'),
            ('Yellow', 'Amarelo', 'Cores'),
            ('Orange', 'Laranja', 'Cores'),
            ('Purple', 'Roxo', 'Cores'),
            ('Pink', 'Rosa', 'Cores'),
            ('Brown', 'Marrom', 'Cores'),
            ('Black', 'Preto', 'Cores'),
            ('White', 'Branco', 'Cores'),
            ('Gray', 'Cinza', 'Cores'),
            ('Gold', 'Dourado', 'Cores'),
            ('Silver', 'Prata', 'Cores'),
            ('Beige', 'Bege', 'Cores'),
            ('Navy', 'Azul marinho', 'Cores'),
            ('Apple', 'Maçã', 'Comidas'),
            ('Banana', 'Banana', 'Comidas'),
            ('Orange fruit', 'Laranja', 'Comidas'),
            ('Pizza', 'Pizza', 'Comidas'),
            ('Hamburger', 'Hambúrguer', 'Comidas'),
            ('Rice', 'Arroz', 'Comidas'),
            ('Beans', 'Feijão', 'Comidas'),
            ('Chicken', 'Frango', 'Comidas'),
            ('Beef', 'Carne bovina', 'Comidas'),
            ('Fish', 'Peixe', 'Comidas'),
            ('Bread', 'Pão', 'Comidas'),
            ('Cheese', 'Queijo', 'Comidas'),
            ('Egg', 'Ovo', 'Comidas'),
            ('Milk', 'Leite', 'Comidas'),
            ('Water', 'Água', 'Comidas'),
            ('Mother', 'Mãe', 'Família'),
            ('Father', 'Pai', 'Família'),
            ('Brother', 'Irmão', 'Família'),
            ('Sister', 'Irmã', 'Família'),
            ('Grandmother', 'Avó', 'Família'),
            ('Grandfather', 'Avô', 'Família'),
            ('Uncle', 'Tio', 'Família'),
            ('Aunt', 'Tia', 'Família'),
            ('Cousin', 'Primo(a)', 'Família'),
            ('Son', 'Filho', 'Família'),
            ('Daughter', 'Filha', 'Família'),
            ('Husband', 'Marido', 'Família'),
            ('Wife', 'Esposa', 'Família'),
            ('Baby', 'Bebê', 'Família'),
            ('Parents', 'Pais', 'Família'),
            ('Happy', 'Feliz', 'Adjetivos'),
            ('Sad', 'Triste', 'Adjetivos'),
            ('Big', 'Grande', 'Adjetivos'),
            ('Small', 'Pequeno', 'Adjetivos'),
            ('Fast', 'Rápido', 'Adjetivos'),
            ('Slow', 'Devagar', 'Adjetivos'),
            ('Hot', 'Quente', 'Adjetivos'),
            ('Cold', 'Frio', 'Adjetivos'),
            ('New', 'Novo', 'Adjetivos'),
            ('Old', 'Velho', 'Adjetivos'),
            ('Good', 'Bom', 'Adjetivos'),
            ('Bad', 'Ruim', 'Adjetivos'),
            ('Beautiful', 'Bonito', 'Adjetivos'),
            ('Ugly', 'Feio', 'Adjetivos'),
            ('Strong', 'Forte', 'Adjetivos')
        ");
    }
    
} catch(PDOException $e) {
    error_log("Erro ao criar tabelas: " . $e->getMessage());
}

// ==========================================
// CORRIGIR: Ajustar tabela usuarios se for antiga
// ==========================================
try {
    // Verificar se a coluna 'nome' existe (tabela antiga)
    $stmt = $pdo->query("SHOW COLUMNS FROM usuarios LIKE 'nome'");
    if ($stmt->fetch()) {
        // Tabela antiga, precisa ajustar
        $pdo->exec("ALTER TABLE usuarios CHANGE nome nome_completo VARCHAR(100) NOT NULL");
        $pdo->exec("ALTER TABLE usuarios CHANGE senha_hash senha VARCHAR(255) NOT NULL");
    }
    
    // Adicionar colunas que podem faltar
    $colunas = $pdo->query("SHOW COLUMNS FROM usuarios")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('pontuacao', $colunas)) {
        $pdo->exec("ALTER TABLE usuarios ADD COLUMN pontuacao INT DEFAULT 0");
    }
    if (!in_array('pontuacao_translate_master', $colunas)) {
        $pdo->exec("ALTER TABLE usuarios ADD COLUMN pontuacao_translate_master INT DEFAULT 0");
    }
    if (!in_array('nivel', $colunas)) {
        $pdo->exec("ALTER TABLE usuarios ADD COLUMN nivel INT DEFAULT 1");
    }
} catch(PDOException $e) {
    error_log("Erro ao ajustar tabela usuarios: " . $e->getMessage());
}

// ==========================================
// USUÁRIO PADRÃO PARA TESTE (apenas se não existir nenhum)
// ==========================================
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
    if ($stmt->fetchColumn() == 0) {
        $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome_completo, apelido, email, senha) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Aluno Teste', 'Explorer', 'aluno@teste.com', $senha_hash]);
        
        $_SESSION['usuario_id'] = $pdo->lastInsertId();
        $_SESSION['apelido'] = 'Explorer';
        $_SESSION['usuario_nome'] = 'Aluno Teste';
        $_SESSION['logado'] = true;
    }
} catch(PDOException $e) {
    error_log("Erro ao criar usuário padrão: " . $e->getMessage());
}
?>
