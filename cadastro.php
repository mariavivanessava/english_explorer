<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_completo = $_POST['nome_completo'];
    $apelido = $_POST['apelido'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    
    // Verificar se email já existe
    $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->execute([$email]);
    
    if ($check->rowCount() > 0) {
        $erro = "Email já cadastrado!";
    } else {
        // Inserir novo usuário com TODOS os campos
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome_completo, apelido, email, senha) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$nome_completo, $apelido, $email, $senha])) {
            // Pega o ID do usuário recém-criado
            $id_usuario = $pdo->lastInsertId();
            
            // Armazena na sessão
            $_SESSION['logado'] = true;
            $_SESSION['id_usuario'] = $id_usuario;
            $_SESSION['apelido'] = $apelido; // Armazena o apelido direto na sessão
            $_SESSION['novo_usuario'] = true;
            
            // Redireciona para a página principal
            header('Location: index.php');
            exit;
        } else {
            $erro = "Erro ao cadastrar. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - English Explorer</title>
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="tema-explorer.css">
</head>
<body>
    <div class="cadastro-container">
        <h2>Cadastro</h2>
        <?php if (isset($erro)): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Nome Completo:</label>
                <input type="text" name="nome_completo" required>
            </div>
            <div class="form-group">
                <label>Apelido:</label>
                <input type="text" name="apelido" required placeholder="Como você quer ser chamado?">
                <small>Ex: João, Maria, Pedrinho, etc.</small>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="senha" required>
            </div>
            <button type="submit">Cadastrar</button>
        </form>
        <p>Já tem conta? <a href="login.php">Faça login</a></p>
    </div>
</body>
</html>
