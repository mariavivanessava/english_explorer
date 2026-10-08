<?php
// ==========================================
// INÍCIO - Processamento PHP
// ==========================================
require_once 'config.php';  // Já inclui session_start() e conexão PDO

$mensagem = '';
$erro = '';
$modo = $_GET['modo'] ?? 'login';

// Processar logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Processar cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $apelido = trim($_POST['apelido'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    // Validações
    if (empty($nome)) {
        $erro = 'Por favor, informe seu nome completo.';
    } elseif (empty($email)) {
        $erro = 'Por favor, informe seu e-mail.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Por favor, informe um e-mail válido.';
    } elseif (empty($apelido)) {
        $erro = 'Por favor, escolha um apelido.';
    } elseif (empty($senha)) {
        $erro = 'Por favor, crie uma senha.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem.';
    } else {
        try {
            // Verificar se email já existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $erro = 'Este e-mail já está cadastrado.';
            } else {
                // Verificar se apelido já existe
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE apelido = ?");
                $stmt->execute([$apelido]);
                
                if ($stmt->fetch()) {
                    $erro = 'Este apelido já está em uso.';
                } else {
                    // Inserir novo usuário
                    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nome_completo, email, apelido, senha) VALUES (?, ?, ?, ?)");
                    
                    if ($stmt->execute([$nome, $email, $apelido, $senha_hash])) {
                        $_SESSION['cadastro_recente_email'] = $email;
                        $mensagem = 'Cadastro realizado com sucesso! Faça login para continuar.';
                        $modo = 'login';
                    } else {
                        $erro = 'Erro ao cadastrar. Tente novamente.';
                    }
                }
            }
        } catch (PDOException $e) {
            $erro = 'Erro no banco de dados: ' . $e->getMessage();
        }
    }
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entrar'])) {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $erro = 'Por favor, preencha e-mail e senha.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome_completo'];
                $_SESSION['usuario_apelido'] = $usuario['apelido'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['logado'] = true;
                $_SESSION['apelido'] = $usuario['apelido'];
                if (isset($_SESSION['cadastro_recente_email']) && $_SESSION['cadastro_recente_email'] === $usuario['email']) {
                    $_SESSION['novo_usuario'] = true;
                    unset($_SESSION['cadastro_recente_email']);
                }
                
                header('Location: index.php');
                exit;
            } else {
                $erro = 'E-mail ou senha incorretos.';
            }
        } catch (PDOException $e) {
            $erro = 'Erro no banco de dados: ' . $e->getMessage();
        }
    }
}
// ==========================================
// FIM - Processamento PHP
// ==========================================
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>English Explorer - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #37598bff;
            --primary-light: #6B8CBE;
            --accent: #d49561ff;
            --accent-dark: #D4A259;
            --warm-bg: #FFF9F0;
            --warm-card: #FFFFFF;
            --soft-gray: #F5F0E6;
            --text-dark: #2C3E4E;
            --text-soft: #5D6E7E;
            --shadow-sm: 0 4px 12px rgba(0,0,0,0.05);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.08);
            --border-radius: 24px;
            --border-radius-sm: 16px;
            --success: #27ae60;
            --error: #e74c3c;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-image: linear-gradient(
                rgba(255, 249, 240, 0.85),
                rgba(255, 249, 240, 0.40)
            ), url('https://tse3.mm.bing.net/th/id/OIP.xOWIE5eOwnuIh2bm326xTAHaDr?w=768&h=381&rs=1&pid=ImgDetMain&o=7&rm=3');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        h1, h2, h3, .logo {
            font-family: 'Poppins', sans-serif;
        }

        .login-container {
            max-width: 500px;
            width: 100%;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 700;
        }

        .logo span {
            background: linear-gradient(135deg, var(--accent) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .logo p {
            color: var(--text-soft);
            margin-top: 10px;
            font-size: 1rem;
        }

        .login-card {
            background: var(--warm-card);
            border-radius: var(--border-radius);
            padding: 40px;
            box-shadow: var(--shadow-md);
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            background: var(--soft-gray);
            padding: 5px;
            border-radius: 60px;
        }

        .tab-btn {
            flex: 1;
            background: transparent;
            border: none;
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            color: var(--text-soft);
            transition: all 0.3s;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
        }

        .tab-btn i {
            margin-right: 8px;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .tab-btn:hover:not(.active) {
            background: rgba(74,111,165,0.1);
            color: var(--primary);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .form-group label i {
            margin-right: 8px;
            color: var(--primary);
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #E8E0D5;
            border-radius: var(--border-radius-sm);
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
            background: var(--warm-bg);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(232, 184, 107, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            font-family: 'Inter', sans-serif;
        }

        .btn-submit i {
            margin-right: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .message {
            padding: 12px 15px;
            border-radius: var(--border-radius-sm);
            margin-bottom: 20px;
            animation: slideDown 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .message i {
            font-size: 1.1rem;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid var(--success);
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--error);
        }

        .help-text {
            font-size: 0.75rem;
            color: var(--text-soft);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .help-text i {
            font-size: 0.7rem;
            color: var(--accent);
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 25px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .logo h1 {
                font-size: 2rem;
            }
        }
    </style>
    <link rel="stylesheet" href="tema-explorer.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>English <span>Explorer</span></h1>
           
        </div>

        <div class="login-card">
            <div class="tabs">
                <button class="tab-btn <?php echo $modo === 'login' ? 'active' : ''; ?>" onclick="switchTab('login')">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
                <button class="tab-btn <?php echo $modo === 'cadastro' ? 'active' : ''; ?>" onclick="switchTab('cadastro')">
                    <i class="fas fa-user-plus"></i> Cadastrar
                </button>
            </div>

            <?php if ($mensagem): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $mensagem; ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($erro): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $erro; ?></span>
                </div>
            <?php endif; ?>

            <div id="login-tab" class="tab-content <?php echo $modo === 'login' ? 'active' : ''; ?>">
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> E-mail</label>
                        <input type="email" name="email" required placeholder="seu@email.com"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Senha</label>
                        <input type="password" name="senha" required placeholder="Digite sua senha">
                    </div>
                    
                    <button type="submit" name="entrar" class="btn-submit">
                        <i class="fas fa-arrow-right"></i> Entrar
                    </button>
                </form>
            </div>

            <div id="cadastro-tab" class="tab-content <?php echo $modo === 'cadastro' ? 'active' : ''; ?>">
                <form method="POST" action="login.php?modo=cadastro" id="formCadastro">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nome Completo</label>
                        <input type="text" name="nome" required placeholder="Digite seu nome completo"
                               value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> E-mail</label>
                        <input type="email" name="email" required placeholder="Deve conter @ e domínio"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> Apelido</label>
                        <input type="text" name="apelido" required placeholder="Como quer ser chamado?"
                               value="<?php echo htmlspecialchars($_POST['apelido'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-key"></i> Senha</label>
                        <input type="password" name="senha" id="senha" required placeholder=" A senha deve ter pelo menos 6 caracteres">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-check-circle"></i> Confirmar Senha</label>
                        <input type="password" name="confirmar_senha" id="confirmar_senha" required placeholder="Confirme sua senha">
                    </div>
                    
                    <button type="submit" name="cadastrar" class="btn-submit">
                        <i class="fas fa-user-plus"></i> Criar Conta
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            const url = new URL(window.location.href);
            url.searchParams.set('modo', tab);
            window.history.pushState({}, '', url);
            
            document.getElementById('login-tab').classList.remove('active');
            document.getElementById('cadastro-tab').classList.remove('active');
            document.getElementById(`${tab}-tab`).classList.add('active');
            
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            if (tab === 'login') {
                document.querySelectorAll('.tab-btn')[0].classList.add('active');
            } else {
                document.querySelectorAll('.tab-btn')[1].classList.add('active');
            }
        }
        
        const senhaInput = document.getElementById('senha');
        const confirmarInput = document.getElementById('confirmar_senha');
        
        if (senhaInput && confirmarInput) {
            function validarSenha() {
                if (confirmarInput.value && senhaInput.value !== confirmarInput.value) {
                    confirmarInput.setCustomValidity('As senhas não coincidem');
                } else {
                    confirmarInput.setCustomValidity('');
                }
                
                if (senhaInput.value && senhaInput.value.length < 6) {
                    senhaInput.setCustomValidity('A senha deve ter pelo menos 6 caracteres');
                } else {
                    senhaInput.setCustomValidity('');
                }
            }
            
            senhaInput.addEventListener('input', validarSenha);
            confirmarInput.addEventListener('input', validarSenha);
        }
    </script>
</body>
</html>
