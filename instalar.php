<?php
require_once 'config.php';

try {
    // Criar banco de dados se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS english_explorer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE english_explorer");
    
    // Criar tabelas
    $sql = "
    -- Tabela de Usuários
    CREATE TABLE IF NOT EXISTS `usuarios` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nome_completo` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `apelido` VARCHAR(50) NOT NULL UNIQUE,
        `senha` VARCHAR(255) NOT NULL,
        `pontuacao` INT DEFAULT 0,
        `pontuacao_translate_master` INT DEFAULT 0,
        `nivel` INT DEFAULT 1,
        `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Tabela de Flashcards Personalizados
    CREATE TABLE IF NOT EXISTS `flashcards_personalizados` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `usuario_id` INT NOT NULL,
        `palavra` VARCHAR(100) NOT NULL,
        `traducao` VARCHAR(100) NOT NULL,
        `tema` VARCHAR(50) DEFAULT 'Personalizado',
        `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Tabela de Grupos
    CREATE TABLE IF NOT EXISTS `grupos` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `usuario_id` INT NOT NULL,
        `nome` VARCHAR(100) NOT NULL,
        `descricao` TEXT,
        `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Tabela de Relacionamento Grupos <-> Flashcards
    CREATE TABLE IF NOT EXISTS `grupos_flashcards` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `grupo_id` INT NOT NULL,
        `flashcard_id` INT NOT NULL,
        `data_adicao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`grupo_id`) REFERENCES `grupos`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`flashcard_id`) REFERENCES `flashcards_personalizados`(`id`) ON DELETE CASCADE,
        UNIQUE KEY `unique_grupo_flashcard` (`grupo_id`, `flashcard_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    $pdo->exec($sql);
    
    echo "<h1>✅ Banco de dados instalado com sucesso!</h1>";
    echo "<p>Tabelas criadas: usuarios, flashcards_personalizados, grupos, grupos_flashcards</p>";
    echo "<p><a href='login.php'>Ir para o Login</a></p>";
    
} catch(PDOException $e) {
    die("<h1>❌ Erro na instalação:</h1><p>" . $e->getMessage() . "</p>");
}
?>
