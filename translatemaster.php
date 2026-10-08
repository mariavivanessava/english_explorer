<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit;
}

$apelido = isset($_SESSION['apelido']) ? $_SESSION['apelido'] : 'Explorer';

if ($apelido === 'Explorer' && isset($_SESSION['id_usuario'])) {
    include 'config.php';
    $id_usuario = $_SESSION['id_usuario'];
    $stmt = $pdo->prepare("SELECT apelido FROM usuarios WHERE id = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch();

    if ($usuario && !empty($usuario['apelido'])) {
        $apelido = $usuario['apelido'];
        $_SESSION['apelido'] = $apelido;
    }
}

$apelidoSeguro = htmlspecialchars($apelido, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TranslateMaster - English Explorer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="tema-explorer.css?v=<?php echo filemtime('tema-explorer.css'); ?>">
    <link rel="stylesheet" href="jogos/translatemaster.css?v=<?php echo filemtime('jogos/translatemaster.css'); ?>">
    <link rel="stylesheet" href="menu-translatemaster.css?v=<?php echo filemtime('menu-translatemaster.css'); ?>">
</head>
<body class="translate-master-page">
    <!-- ===== NAVBAR ===== -->
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
                    <a href="flashcards.php">Flashcards</a>
                    <a href="mapa.php">Mapa</a>
                    <div class="games-menu">
                        <button type="button" class="games-menu-button">
                            <i class="fas fa-gamepad"></i> Jogos <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="games-dropdown">
                            <a href="quizz.php"><i class="fas fa-question-circle"></i> Quizz</a>
                            <a href="translatemaster.php" class="active"><i class="fas fa-language"></i> TranslateMaster</a>
                        </div>
                    </div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <main class="translate-page" id="translateGame" data-player="<?php echo $apelidoSeguro; ?>">
        <!-- ===== HERO ===== -->
        <section class="game-hero">
            <div class="hero-copy">
                <h1>TranslateMaster</h1>
                <p>Traduza textos para o inglês</p>
            </div>
        </section>

        <!-- ===== LEVEL TABS ===== -->
        <section class="level-tabs" aria-label="Fases do TranslateMaster">
            <button type="button" class="level-tab active" data-level="facil">
                <i class="fas fa-seedling"></i>
                <span>Fácil</span>
            </button>
            <button type="button" class="level-tab" data-level="medio">
                <i class="fas fa-mountain"></i>
                <span>Médio</span>
            </button>
            <button type="button" class="level-tab" data-level="dificil">
                <i class="fas fa-crown"></i>
                <span>Difícil</span>
            </button>
        </section>

        <!-- ===== ARENA ===== -->
        <section class="translate-arena">
            <div class="translate-layout">
                <aside class="challenge-list" aria-label="Textos disponíveis">
                    <div class="panel-heading">
                        <span>Temas</span>
                        <h2>Escolha seu texto</h2>
                    </div>
                    <div class="challenge-options" id="challengeOptions"></div>
                </aside>

                <section class="workbench">
                    <div class="prompt-card">
                        <div class="prompt-meta">
                            <span id="difficultyBadge">Fácil</span>
                            <strong id="selectedTheme">Rotina</strong>
                        </div>
                        <p id="portugueseText"></p>
                    </div>

                    <label class="translation-label" for="translationInput">
                        Sua tradução em inglês
                    </label>
                    <textarea id="translationInput" spellcheck="false" placeholder="Digite sua tradução aqui..."></textarea>

                    <div class="actions-row">
                        <button type="button" class="secondary-button" id="exampleBtn">
                            <i class="fas fa-lightbulb"></i> Ver dica
                        </button>
                        <button type="button" class="secondary-button" id="clearBtn">
                            <i class="fas fa-eraser"></i> Limpar
                        </button>
                        <button type="button" class="primary-button" id="checkBtn">
                            <i class="fas fa-paper-plane"></i> Corrigir
                        </button>
                    </div>

                    <div class="hint-box" id="hintBox" aria-live="polite"></div>
                </section>
            </div>

            <!-- ===== RESULTS ===== -->
            <div class="results-panel" id="resultsPanel" aria-live="polite">
                <div class="correction-card">
                    <div class="panel-heading">
                        <span>Correção</span>
                        <h2>Seu texto analisado</h2>
                    </div>
                    <div class="marked-text" id="markedText"></div>
                    <div class="legend">
                        <span><i class="legend-dot ok"></i> Bom uso</span>
                        <span><i class="legend-dot close"></i> Forma possível</span>
                        <span><i class="legend-dot issue"></i> Possível erro</span>
                    </div>
                </div>

                <div class="feedback-card">
                    <div class="panel-heading">
                        <span>Detalhes</span>
                        <h2>Pontos para revisar</h2>
                    </div>
                    <ul id="feedbackList"></ul>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 English Explorer - Speak like a native</p>
        </div>
    </footer>

    <script src="jogos/translatemaster.js"></script>
</body>
</html>
