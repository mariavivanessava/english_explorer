<?php
session_start();
require_once 'config.php';

// Buscar temas disponíveis
$stmt = $pdo->query("SELECT DISTINCT tema FROM palavras WHERE tema IS NOT NULL AND tema != '' ORDER BY tema");
$temas = $stmt->fetchAll();

// PEGA O APELIDO DO USUÁRIO (DEPOIS de carregar o config.php)
$apelido = 'Explorer'; // valor padrão

if (isset($_SESSION['apelido']) && !empty($_SESSION['apelido'])) {
    $apelido = $_SESSION['apelido'];
} elseif (isset($_SESSION['id_usuario'])) {
    // Buscar no banco se não estiver na sessão
    $stmt = $pdo->prepare("SELECT apelido FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['id_usuario']]);
    $usuario = $stmt->fetch();
    if ($usuario && !empty($usuario['apelido'])) {
        $apelido = $usuario['apelido'];
        $_SESSION['apelido'] = $apelido;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flashcards - English Explorer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #4A6FA5;
            --primary-light: #6B8CBE;
            --accent: #E8B86B;
            --accent-dark: #D4A259;
            --warm-bg: #FFF9F0;
            --warm-card: #FFFFFF;
            --soft-gray: #F5F0E6;
            --text-dark: #2C3E4E;
            --text-soft: #5D6E7E;
            --shadow-sm: 0 4px 12px rgba(0,0,0,0.05);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 36px rgba(0,0,0,0.1);
            --border-radius: 24px;
            --border-radius-sm: 16px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--warm-bg);
            color: var(--text-dark);
            line-height: 1.6;
        }

        h1, h2, h3, .logo {
            font-family: 'Poppins', sans-serif;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 249, 240, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-sm);
            z-index: 1000;
            padding: 12px 0;
        }
        
        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        .logo {
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-decoration: none;
        }
        
        .user-greeting {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .welcome-badge {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 500;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--text-soft);
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 8px 12px;
            border-radius: 8px;
        }
        
        .nav-links a:hover {
            color: var(--accent);
        }
        
        .nav-links a.active,
        .nav-links a.active:hover,
        .games-dropdown a.active,
        .games-dropdown a.active:hover {
            border: none !important;
            box-shadow: none !important;
            background: linear-gradient(135deg, var(--primary-light), var(--accent), var(--coral)) !important;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            -webkit-text-fill-color: transparent;
        }
        
        .logout-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white !important;
            padding: 8px 20px !important;
            border-radius: 40px !important;
            transition: all 0.3s ease !important;
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            color: white !important;
        }

        /* ===== MENU DE JOGOS (mesmo do dicionário) ===== */
        .games-menu {
            position: relative;
        }

        .games-menu-button {
            min-height: 34px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: var(--primary);
            font-size: 0.84rem;
            font-weight: 750;
            line-height: 1;
            cursor: pointer;
        }

        .games-menu-button:hover {
            color: var(--accent);
        }

        .games-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 190px;
            padding: 8px;
            border-radius: 12px;
            border: none !important;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
            z-index: 1002;
        }

        .games-menu:hover .games-dropdown,
        .games-menu:focus-within .games-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .games-dropdown a {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            width: 100% !important;
            justify-content: flex-start !important;
            border-radius: 8px !important;
            background: transparent !important;
            padding: 10px 14px !important;
            color: var(--text-dark) !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            transition: background 0.2s ease !important;
        }

        .games-dropdown a:hover {
            background: var(--soft-gray) !important;
            color: var(--primary) !important;
        }

        .games-dropdown a i {
            width: 20px;
            text-align: center;
            color: var(--accent);
        }

        /* Hero Section */
        .hero {
            margin-top: 80px;
            padding: 40px 24px 60px;
            text-align: center;
            background: linear-gradient(135deg, rgba(74, 111, 165, 0.05) 0%, rgba(232, 184, 107, 0.08) 100%);
        }
        
        .hero h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 15px;
        }
        
        .hero p {
            color: var(--text-soft);
            font-size: 1.1rem;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Abas de navegação */
        .tabs {
            display: flex;
            gap: 10px;
            margin: 30px 0;
            justify-content: center;
            flex-wrap: wrap;
        }

        .tab-btn {
            background: var(--warm-card);
            border: 2px solid var(--accent);
            padding: 12px 24px;
            border-radius: 40px;
            color: var(--text-dark);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .tab-btn.active {
            background: var(--accent);
            color: white;
            transform: translateY(-2px);
        }

        .tab-btn:hover {
            background: var(--accent);
            color: white;
            transform: translateY(-2px);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Container dos flashcards */
        .flashcard-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 450px;
            margin: 30px 0;
        }

        .flashcard {
            width: 550px;
            height: 380px;
            perspective: 1000px;
            cursor: pointer;
        }

        .flashcard-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.6s;
            transform-style: preserve-3d;
            border-radius: 20px;
        }

        .flashcard.flipped .flashcard-inner {
            transform: rotateY(180deg);
        }

        .flashcard-front, .flashcard-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px;
            box-shadow: var(--shadow-md);
        }

        .flashcard-front {
            background: white;
            color: var(--text-dark);
            transform: rotateY(0deg);
            border: 3px solid var(--accent);
        }

        .flashcard-back {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            transform: rotateY(180deg);
        }

        .flashcard-front h3, .flashcard-back h3 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .flashcard-front .hint {
            position: absolute;
            bottom: 25px;
            font-size: 0.9rem;
            color: var(--text-soft);
        }

        .flashcard-back .hint {
            position: absolute;
            bottom: 25px;
            font-size: 0.9rem;
            opacity: 0.7;
        }

        .navigation {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .nav-btn {
            background: var(--warm-card);
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--primary);
            font-weight: bold;
            box-shadow: var(--shadow-sm);
        }

        .nav-btn:hover {
            background: var(--accent);
            color: white;
            transform: scale(1.05);
        }

        .theme-selector {
            text-align: center;
            margin: 20px 0;
        }

        .theme-selector label {
            color: var(--text-dark);
            font-size: 1.1rem;
            font-weight: bold;
        }

        .theme-selector select {
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 8px;
            border: 2px solid var(--accent);
            margin-left: 10px;
            cursor: pointer;
            background: white;
            color: var(--text-dark);
        }

        .create-form {
            background: var(--warm-card);
            padding: 35px;
            border-radius: var(--border-radius);
            max-width: 650px;
            margin: 0 auto;
            box-shadow: var(--shadow-md);
        }

        .create-form h3 {
            margin-bottom: 10px;
            color: var(--primary);
            text-align: center;
            font-size: 1.5rem;
        }
        
        .create-form .subtitle {
            text-align: center;
            color: var(--text-soft);
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--text-dark);
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--accent);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .custom-list {
            background: var(--warm-card);
            border-radius: var(--border-radius);
            padding: 25px;
            margin-top: 30px;
            box-shadow: var(--shadow-md);
        }

        .custom-list h3 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .custom-card-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            transition: all 0.3s;
        }

        .custom-card-item:hover {
            background: var(--soft-gray);
        }

        .delete-btn {
            background: #20295c;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .delete-btn:hover {
            background: #20295c;
            transform: scale(1.05);
        }

        .counter {
            text-align: center;
            margin-top: 20px;
            color: var(--text-dark);
            font-size: 1rem;
            font-weight: bold;
        }

        .empty-message {
            text-align: center;
            color: var(--text-soft);
            padding: 40px;
            font-size: 1.1rem;
        }

        /* ESTILOS DOS GRUPOS */
        .grupos-section {
            margin-top: 30px;
        }

        .grupos-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .grupos-header h3 {
            color: var(--primary);
            font-size: 1.5rem;
            margin: 0;
        }

        .grupo-card {
            background: linear-gradient(135deg, rgba(74, 111, 165, 0.1), rgba(232, 184, 107, 0.1));
            border-radius: var(--border-radius-sm);
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .grupo-card:hover {
            border-color: var(--accent);
            transform: translateX(5px);
            box-shadow: var(--shadow-md);
        }

        .grupo-card h4 {
            color: var(--primary);
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.2rem;
        }

        .grupo-card p {
            color: var(--text-soft);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .grupo-stats {
            display: flex;
            gap: 15px;
            margin-top: 8px;
            flex-wrap: wrap;
        }

        .grupo-stats span {
            font-size: 0.85rem;
            color: var(--accent);
        }

        .grupo-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .jogar-grupo-btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
            font-weight: 500;
        }

        .jogar-grupo-btn:hover {
            background: var(--accent-dark);
            transform: scale(1.05);
        }

        .btn-criar-grupo {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 40px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-criar-grupo:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .grupo-atual-indicador {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
        }

        .preview-grupo {
            margin-top: 20px;
            background: #f5f0e6;
            padding: 20px;
            border-radius: 12px;
            border: 2px dashed var(--accent);
        }
        
        .preview-grupo h4 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .preview-item {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
            font-size: 0.95rem;
        }
        
        .preview-item:last-child {
            border-bottom: none;
        }
        
        .preview-item strong {
            color: var(--primary);
        }

        .footer {
            background: var(--text-dark);
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
            text-align: center;
        }

        /* Indicador de grupo ativo na aba personalizados */
        .grupo-ativo-info {
            text-align: center;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .navbar .container {
                flex-direction: column;
                gap: 15px;
            }
            
            .hero {
                margin-top: 140px;
            }
            
            .hero h1 {
                font-size: 1.8rem;
            }
            
            .flashcard {
                width: 95%;
                height: 320px;
            }
            
            .flashcard-front h3, .flashcard-back h3 {
                font-size: 1.5rem;
            }
            
            .tab-btn {
                padding: 8px 16px;
                font-size: 0.9rem;
            }

            .games-menu {
                position: static;
            }

            .games-dropdown {
                left: 50%;
                right: auto;
                transform: translate(-50%, -6px);
            }

            .games-menu:hover .games-dropdown,
            .games-menu:focus-within .games-dropdown {
                transform: translate(-50%, 0);
            }
        }
    </style>
    <link rel="stylesheet" href="tema-explorer.css">
    <link rel="stylesheet" href="menu-translatemaster.css">
</head>
<body>
    <!-- ===== NAVBAR COM MENU DE JOGOS ===== -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">English Explorer</a>
            <div class="user-greeting">
                <div class="welcome-badge">
                    <i class="fas fa-user"></i>
                    Olá, <?php echo htmlspecialchars($apelido); ?>
                </div>
                <div class="nav-links">
                    <a href="index.php">Início</a>
                    <a href="dicionario.php">Dicionário</a>
                    <a href="flashcards.php" class="active">Flashcards</a>
                    <a href="mapa.php">Mapa</a>
                    <div class="games-menu">
                        <button type="button" class="games-menu-button">
                            <i class="fas fa-gamepad"></i> Jogos <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="games-dropdown">
                            <a href="quizz.php"><i class="fas fa-question-circle"></i> Quizz</a>
                            <a href="translatemaster.php"><i class="fas fa-language"></i> TranslateMaster</a>
                        </div>
                    </div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>
    
    <section class="hero">
        <div class="container">
            <h1>Flashcards para treinar de verdade</h1>
        </div>
    </section>
    
    <div class="container">
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('biblioteca')" data-tab="biblioteca">📚 Biblioteca de Temas</button>
            <button class="tab-btn" onclick="switchTab('personalizados')" data-tab="personalizados">✏️ Meus Flashcards</button>
            <button class="tab-btn" onclick="switchTab('criar')" data-tab="criar">➕ Criar Grupo</button>
        </div>

        <!-- Aba: Biblioteca de Temas -->
        <div id="biblioteca" class="tab-content active">
            <div class="theme-selector">
                <label>🎯 Escolha um tema para estudar:</label>
                <select id="temaSelect" onchange="carregarFlashcards(); salvarEstado()">
                    <?php foreach($temas as $tema): ?>
                        <option value="<?= htmlspecialchars($tema['tema']) ?>"><?= htmlspecialchars($tema['tema']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="bibliotecaFlashcardContainer" class="flashcard-container">
                <div class="flashcard" onclick="toggleFlip()">
                    <div class="flashcard-inner">
                        <div class="flashcard-front">
                            <h3 id="palavra">Carregando...</h3>
                            <p class="hint">✨ Clique para ver a tradução ✨</p>
                        </div>
                        <div class="flashcard-back">
                            <h3 id="traducao">Carregando...</h3>
                            <p class="hint">🔄 Clique para voltar 🔄</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="navigation">
                <button class="nav-btn" onclick="flashcardAnterior()">◀ Anterior</button>
                <button class="nav-btn" onclick="proximoFlashcard()">Próximo ▶</button>
            </div>
            <div class="counter" id="contador"></div>
        </div>

        <!-- Aba: Meus Flashcards Personalizados -->
        <div id="personalizados" class="tab-content">
            <!-- Indicador de grupo ativo -->
            <div id="grupoAtualIndicador" class="grupo-ativo-info"></div>

            <div id="personalizadoFlashcardContainer" class="flashcard-container">
                <div class="flashcard" onclick="toggleFlipPersonalizado()">
                    <div class="flashcard-inner">
                        <div class="flashcard-front">
                            <h3 id="personalizadoPalavra">Carregando...</h3>
                            <p class="hint">✨ Clique para ver a tradução ✨</p>
                        </div>
                        <div class="flashcard-back">
                            <h3 id="personalizadoTraducao">Carregando...</h3>
                            <p class="hint">🔄 Clique para voltar 🔄</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="navigation">
                <button class="nav-btn" onclick="personalizadoAnterior()">◀ Anterior</button>
                <button class="nav-btn" onclick="personalizadoProximo()">Próximo ▶</button>
            </div>
            <div class="counter" id="personalizadoContador"></div>

            <div class="grupos-section">
                <div class="grupos-header">
                    <h3>📁 Meus Grupos</h3>
                </div>
                <div id="listaGrupos"></div>
            </div>
        </div>

        <!-- Aba: Criar Grupo (APENAS GRUPO, sem opção individual) -->
        <div id="criar" class="tab-content">
            <div class="create-form">
                <h3>📚 Criar Grupo de Flashcards</h3>

                
                <form id="formCriarGrupo">
                    <div class="form-group">
                        <label>🏷️ Nome do Grupo:</label>
                        <input type="text" id="tema_grupo" required placeholder="Ex: Verbos, Animais, Cores" style="background: #f0f0ff; font-weight: bold;">
                    </div>
                    <div class="form-group">
                        <label>📚 Palavras:</label>
                        <textarea id="lista_palavras" rows="8" placeholder="Exemplo:
Hello = Olá
Good morning = Bom dia
Dog = Cachorro"></textarea>
                        <small style="color: #666; display: block; margin-top: 5px;">
                            💡 Dica: Use o formato <strong>palavra = tradução</strong> (uma por linha)
                        </small>
                    </div>
                    <button type="submit" class="btn-submit">🚀 Criar Grupo de Flashcards</button>
                </form>
                
                <div id="previewGrupo" class="preview-grupo" style="display: none;">
                    <h4>📋 Prévia do grupo:</h4>
                    <div id="previewLista"></div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 English Explorer - Speak like a native</p>
        </div>
    </footer>

    <script>
        // ==========================================
        // VARIÁVEIS GLOBAIS
        // ==========================================
        let flashcardsAtuais = [];
        let indiceAtual = 0;
        let flashcardsPersonalizados = [];
        
        let grupos = [];
        let grupoAtual = null;
        let flashcardsDoGrupo = [];
        let indiceGrupoAtual = 0;

        // ==========================================
        // FUNÇÕES PARA SALVAR/RESTAURAR ESTADO
        // ==========================================
        
        function salvarEstado() {
            // Salva a aba ativa
            const abaAtiva = document.querySelector('.tab-content.active');
            if (abaAtiva) {
                localStorage.setItem('flashcards_aba', abaAtiva.id);
            }
            
            // Salva o tema selecionado
            const temaSelect = document.getElementById('temaSelect');
            if (temaSelect) {
                localStorage.setItem('flashcards_tema', temaSelect.value);
            }
            
            // Salva o índice atual da biblioteca
            localStorage.setItem('flashcards_indice', indiceAtual);
            
            // Salva o grupo ativo
            if (grupoAtual) {
                localStorage.setItem('flashcards_grupo_ativo', grupoAtual);
            } else {
                localStorage.removeItem('flashcards_grupo_ativo');
            }
            
            // Salva o índice do grupo
            localStorage.setItem('flashcards_indice_grupo', indiceGrupoAtual);
        }

        function restaurarEstado() {
            // Restaura a aba
            const abaSalva = localStorage.getItem('flashcards_aba');
            if (abaSalva) {
                // Remove active de todas
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active');
                });
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Ativa a aba salva
                const tabAtiva = document.getElementById(abaSalva);
                if (tabAtiva) {
                    tabAtiva.classList.add('active');
                    const btnCorrespondente = document.querySelector(`.tab-btn[data-tab="${abaSalva}"]`);
                    if (btnCorrespondente) {
                        btnCorrespondente.classList.add('active');
                    }
                }
            }
            
            // Restaura o tema
            const temaSalvo = localStorage.getItem('flashcards_tema');
            if (temaSalvo) {
                const temaSelect = document.getElementById('temaSelect');
                if (temaSelect) {
                    temaSelect.value = temaSalvo;
                }
            }
            
            // Restaura o índice da biblioteca
            const indiceSalvo = localStorage.getItem('flashcards_indice');
            if (indiceSalvo !== null) {
                indiceAtual = parseInt(indiceSalvo);
            }
            
            // Restaura o grupo ativo
            const grupoSalvo = localStorage.getItem('flashcards_grupo_ativo');
            if (grupoSalvo) {
                grupoAtual = parseInt(grupoSalvo);
            }
            
            // Restaura o índice do grupo
            const indiceGrupoSalvo = localStorage.getItem('flashcards_indice_grupo');
            if (indiceGrupoSalvo !== null) {
                indiceGrupoAtual = parseInt(indiceGrupoSalvo);
            }
        }

        // ==========================================
        // FUNÇÕES DE ABAS E NAVEGAÇÃO
        // ==========================================
        
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
            
            if (tabId === 'biblioteca') {
                carregarFlashcards();
            } else if (tabId === 'personalizados') {
                carregarFlashcardsPersonalizados();
                carregarGrupos();
            }
            
            salvarEstado();
        }

        // ==========================================
        // FUNÇÕES DA BIBLIOTECA (TEMAS)
        // ==========================================
        
        function carregarFlashcards() {
            const tema = document.getElementById('temaSelect').value;
            fetch(`api_flashcards.php?acao=listar&tema=${encodeURIComponent(tema)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso && data.flashcards.length > 0) {
                        flashcardsAtuais = data.flashcards;
                        // Garante que o índice não ultrapasse o tamanho
                        if (indiceAtual >= flashcardsAtuais.length) {
                            indiceAtual = 0;
                        }
                        atualizarFlashcard();
                        document.getElementById('contador').innerHTML = `📊 Card ${indiceAtual + 1} de ${flashcardsAtuais.length}`;
                    } else {
                        document.getElementById('palavra').innerText = 'Nenhum flashcard encontrado';
                        document.getElementById('traducao').innerText = 'Tente outro tema';
                        document.getElementById('contador').innerHTML = '📊 0 flashcards';
                    }
                    salvarEstado();
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
        }

        function atualizarFlashcard() {
            if (flashcardsAtuais.length > 0) {
                document.getElementById('palavra').innerText = flashcardsAtuais[indiceAtual].palavra;
                document.getElementById('traducao').innerText = flashcardsAtuais[indiceAtual].traducao;
                const flashcard = document.querySelector('#biblioteca .flashcard');
                if (flashcard && flashcard.classList.contains('flipped')) {
                    flashcard.classList.remove('flipped');
                }
            }
        }

        function toggleFlip() {
            const flashcard = document.querySelector('#biblioteca .flashcard');
            if (flashcard) {
                flashcard.classList.toggle('flipped');
            }
        }

        function proximoFlashcard() {
            if (indiceAtual < flashcardsAtuais.length - 1) {
                indiceAtual++;
                atualizarFlashcard();
                document.getElementById('contador').innerHTML = `📊 Card ${indiceAtual + 1} de ${flashcardsAtuais.length}`;
                salvarEstado();
            }
        }

        function flashcardAnterior() {
            if (indiceAtual > 0) {
                indiceAtual--;
                atualizarFlashcard();
                document.getElementById('contador').innerHTML = `📊 Card ${indiceAtual + 1} de ${flashcardsAtuais.length}`;
                salvarEstado();
            }
        }

        // ==========================================
        // FUNÇÕES PARA FLASHCARDS PERSONALIZADOS
        // ==========================================
        
        function carregarFlashcardsPersonalizados() {
            fetch('api_flashcards.php?acao=personalizados')
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        flashcardsPersonalizados = data.flashcards;
                        
                        // Se tem um grupo ativo, carrega ele
                        if (grupoAtual) {
                            carregarGrupoEspecifico(grupoAtual, false);
                        } else {
                            flashcardsDoGrupo = [...flashcardsPersonalizados];
                            if (indiceGrupoAtual >= flashcardsDoGrupo.length) {
                                indiceGrupoAtual = 0;
                            }
                            atualizarFlashcardPersonalizado();
                            document.getElementById('personalizadoContador').innerHTML = `📊 Card ${indiceGrupoAtual + 1} de ${flashcardsDoGrupo.length}`;
                        }
                    }
                });
        }

        function carregarGrupoEspecifico(grupoId, salvar = true) {
            // Cada nova abertura de grupo inicia pelo primeiro flashcard,
            // independentemente do índice anterior salvo no localStorage.
            indiceGrupoAtual = 0;

            fetch(`api_grupos.php?acao=carregar&id=${grupoId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso && data.flashcards.length > 0) {
                        flashcardsDoGrupo = data.flashcards;
                        grupoAtual = grupoId;
                        
                        atualizarFlashcardPersonalizado();
                        
                        const grupo = grupos.find(g => g.id == grupoId);
                        if (grupo) {
                            document.getElementById('personalizadoContador').innerHTML = `🎮 Grupo: ${escapeHtml(grupo.nome)} | ${indiceGrupoAtual + 1} de ${flashcardsDoGrupo.length}`;
                            const indicador = document.getElementById('grupoAtualIndicador');
                            if (indicador) {
                                indicador.innerHTML = `<span class="grupo-atual-indicador">📁 Estudando: ${escapeHtml(grupo.nome)}</span>`;
                            }
                        }
                        
                        const flashcard = document.querySelector('#personalizados .flashcard');
                        if (flashcard && flashcard.classList.contains('flipped')) {
                            flashcard.classList.remove('flipped');
                        }
                        
                        if (salvar) salvarEstado();
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar grupo específico:', error);
                });
        }

        function atualizarFlashcardPersonalizado() {
            if (flashcardsDoGrupo.length > 0 && indiceGrupoAtual < flashcardsDoGrupo.length) {
                document.getElementById('personalizadoPalavra').innerText = flashcardsDoGrupo[indiceGrupoAtual].palavra;
                document.getElementById('personalizadoTraducao').innerText = flashcardsDoGrupo[indiceGrupoAtual].traducao;
                const flashcard = document.querySelector('#personalizados .flashcard');
                if (flashcard && flashcard.classList.contains('flipped')) {
                    flashcard.classList.remove('flipped');
                }
            }
        }

        function toggleFlipPersonalizado() {
            const flashcard = document.querySelector('#personalizados .flashcard');
            if (flashcard) {
                flashcard.classList.toggle('flipped');
            }
        }

        function personalizadoProximo() {
            if (flashcardsDoGrupo.length > 0 && indiceGrupoAtual < flashcardsDoGrupo.length - 1) {
                indiceGrupoAtual++;
                atualizarFlashcardPersonalizado();
                atualizarContadorPersonalizado();
                salvarEstado();
            }
        }

        function personalizadoAnterior() {
            if (flashcardsDoGrupo.length > 0 && indiceGrupoAtual > 0) {
                indiceGrupoAtual--;
                atualizarFlashcardPersonalizado();
                atualizarContadorPersonalizado();
                salvarEstado();
            }
        }

        function atualizarContadorPersonalizado() {
            if (grupoAtual) {
                const grupo = grupos.find(g => g.id == grupoAtual);
                if (grupo) {
                    document.getElementById('personalizadoContador').innerHTML = `🎮 Grupo: ${escapeHtml(grupo.nome)} | ${indiceGrupoAtual + 1} de ${flashcardsDoGrupo.length}`;
                }
            } else {
                document.getElementById('personalizadoContador').innerHTML = `📊 Card ${indiceGrupoAtual + 1} de ${flashcardsDoGrupo.length}`;
            }
        }

        // ==========================================
        // FUNÇÕES PARA GRUPOS
        // ==========================================
        
        function carregarGrupos() {
            fetch('api_grupos.php?acao=listar')
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        grupos = data.grupos;
                        renderizarListaGrupos();
                        
                        // Se tem grupo ativo, verifica se ainda existe
                        if (grupoAtual) {
                            const existe = grupos.some(g => g.id == grupoAtual);
                            if (!existe) {
                                grupoAtual = null;
                                flashcardsDoGrupo = [...flashcardsPersonalizados];
                                indiceGrupoAtual = 0;
                                atualizarFlashcardPersonalizado();
                                document.getElementById('personalizadoContador').innerHTML = `📊 Card 1 de ${flashcardsDoGrupo.length}`;
                                document.getElementById('grupoAtualIndicador').innerHTML = '';
                                salvarEstado();
                            } else {
                                // Recarrega o grupo para pegar dados atualizados
                                carregarGrupoEspecifico(grupoAtual, false);
                            }
                        }
                    }
                })
                .catch(error => console.error('Erro ao carregar grupos:', error));
        }

        function renderizarListaGrupos() {
            const listaDiv = document.getElementById('listaGrupos');
            if (!listaDiv) return;
            
            if (grupos.length === 0) {
                listaDiv.innerHTML = '<div class="empty-message">📭 Nenhum grupo criado ainda. Crie seu primeiro grupo na aba "Criar Grupo"!</div>';
                return;
            }
            
            listaDiv.innerHTML = '';
            grupos.forEach(grupo => {
                const isAtivo = (grupoAtual == grupo.id);
                const div = document.createElement('div');
                div.className = 'grupo-card';
                if (isAtivo) {
                    div.style.borderColor = 'var(--accent)';
                }
                div.innerHTML = `
                    <h4>
                        📁 ${escapeHtml(grupo.nome)} ${isAtivo ? '✅' : ''}
                        <button class="jogar-grupo-btn" onclick="jogarGrupo(${grupo.id})">🎮 Jogar</button>
                    </h4>
                    <div class="grupo-stats">
                        <span>📊 ${grupo.total_flashcards} flashcards</span>
                        <span>📅 ${new Date(grupo.data_criacao).toLocaleDateString()}</span>
                    </div>
                    <div class="grupo-actions">
                        <button onclick="deletarGrupo(${grupo.id})" class="delete-btn">🗑️ Excluir Grupo</button>
                    </div>
                `;
                listaDiv.appendChild(div);
            });
        }

        function jogarGrupo(grupoId) {
            carregarGrupoEspecifico(grupoId, true);
        }

        function deletarGrupo(grupoId) {
            if (confirm('Tem certeza que deseja excluir este grupo? Os flashcards NÃO serão deletados.')) {
                fetch('api_grupos.php?acao=deletar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: grupoId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        alert('✅ Grupo excluído!');
                        if (grupoAtual === grupoId) {
                            grupoAtual = null;
                            flashcardsDoGrupo = [...flashcardsPersonalizados];
                            indiceGrupoAtual = 0;
                            atualizarFlashcardPersonalizado();
                            document.getElementById('personalizadoContador').innerHTML = `📊 Card 1 de ${flashcardsDoGrupo.length}`;
                            document.getElementById('grupoAtualIndicador').innerHTML = '';
                            salvarEstado();
                        }
                        carregarGrupos();
                        carregarFlashcardsPersonalizados();
                    }
                });
            }
        }

        // ==========================================
        // FUNÇÕES PARA CRIAR GRUPO DE FLASHCARDS
        // ==========================================
        
        function processarFormatoIgual(texto) {
            const linhas = texto.split('\n');
            const palavras = [];
            
            for (let linha of linhas) {
                linha = linha.trim();
                if (linha === '') continue;
                
                if (linha.includes('=')) {
                    const partes = linha.split('=');
                    const palavra = partes[0].trim();
                    const traducao = partes[1].trim();
                    if (palavra && traducao) {
                        palavras.push({ palavra, traducao });
                    }
                }
            }
            return palavras;
        }

        function atualizarPreview() {
            const textoIgual = document.getElementById('lista_palavras')?.value || '';
            const palavras = processarFormatoIgual(textoIgual);
            
            const previewDiv = document.getElementById('previewGrupo');
            const previewLista = document.getElementById('previewLista');
            
            if (previewDiv && previewLista) {
                if (palavras.length > 0) {
                    previewDiv.style.display = 'block';
                    previewLista.innerHTML = palavras.map(p => 
                        `<div class="preview-item">
                            <strong>${escapeHtml(p.palavra)}</strong> → ${escapeHtml(p.traducao)}
                        </div>`
                    ).join('');
                } else {
                    previewDiv.style.display = 'none';
                }
            }
        }

        function criarGrupoViaFormulario(formData) {
            const tema = formData.tema;
            const palavras = formData.palavras;
            
            const submitBtn = document.querySelector('#formCriarGrupo .btn-submit');
            const textoOriginal = submitBtn.textContent;
            submitBtn.textContent = `⏳ Criando grupo "${tema}"...`;
            submitBtn.disabled = true;
            
            fetch('api_flashcards.php?acao=criar_grupo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ palavras, tema })
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    alert(`🎉 Grupo "${tema}" criado com sucesso!\n${data.adicionados} flashcards adicionados.`);
                    
                    document.getElementById('lista_palavras').value = '';
                    document.getElementById('tema_grupo').value = '';
                    document.getElementById('previewGrupo').style.display = 'none';
                    
                    carregarFlashcardsPersonalizados();
                    carregarGrupos();
                    
                    setTimeout(() => {
                        switchTab('personalizados');
                    }, 500);
                } else {
                    alert('❌ Erro: ' + (data.mensagem || 'Tente novamente'));
                }
            })
            .catch(error => {
                alert('❌ Erro de conexão');
                console.error('Erro:', error);
            })
            .finally(() => {
                submitBtn.textContent = textoOriginal;
                submitBtn.disabled = false;
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ==========================================
        // INICIALIZAÇÃO
        // ==========================================
        
        document.addEventListener('DOMContentLoaded', function() {
            // Restaura o estado salvo
            restaurarEstado();
            
            // Carrega os dados
            carregarFlashcards();
            carregarFlashcardsPersonalizados();
            carregarGrupos();
            
            // Form Grupo
            document.getElementById('formCriarGrupo')?.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const tema = document.getElementById('tema_grupo').value.trim();
                const textoIgual = document.getElementById('lista_palavras').value;
                const palavras = processarFormatoIgual(textoIgual);
                
                if (palavras.length === 0) {
                    alert('Digite pelo menos uma palavra!');
                    return;
                }
                
                if (!tema) {
                    alert('⚠️ Informe um nome para o grupo!');
                    return;
                }
                
                criarGrupoViaFormulario({ tema, palavras });
            });
            
            // Preview em tempo real
            document.getElementById('lista_palavras')?.addEventListener('input', atualizarPreview);
            
            // Salva estado quando a página for fechada/recarregada
            window.addEventListener('beforeunload', function() {
                salvarEstado();
            });
        });
    </script>
</body>
</html>