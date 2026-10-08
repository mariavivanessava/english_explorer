<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit;
}

$apelido = isset($_SESSION['apelido']) ? $_SESSION['apelido'] : 'Usuário';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>English Explorer - Word Builder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4A6FA5;
            --primary-light: #6B8CBE;
            --accent: #E8B86B;
            --accent-dark: #D4A259;
            --coral: #F27667;
            --teal: #31A89B;
            --pink: #D96FA5;
            --gold: #F4BB4A;
            --warm-bg: #FFF9F0;
            --warm-card: #FFFFFF;
            --soft-gray: #F5F0E6;
            --text-dark: #2C3E4E;
            --text-soft: #5D6E7E;
            --success: #2E8B57;
            --danger: #C94D5A;
            --shadow-sm: 0 4px 12px rgba(0,0,0,.05);
            --shadow-md: 0 8px 24px rgba(0,0,0,.08);
            --radius: 24px;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            color: var(--text-dark);
            background:
                radial-gradient(circle at 10% 15%, rgba(232,184,107,.18), transparent 24%),
                radial-gradient(circle at 90% 28%, rgba(242,118,103,.14), transparent 25%),
                linear-gradient(145deg, #fffaf0 0%, #f8f1e7 48%, #eef7f5 100%);
            font-family: Inter, sans-serif;
            line-height: 1.6;
        }
        button, select { font: inherit; }
        button { cursor: pointer; }
        .navbar {
            position: fixed;
            inset: 0 0 auto;
            z-index: 1000;
            padding: 12px 0;
            background: rgba(255,249,240,.96);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(10px);
        }
        .navbar .container { max-width: 1200px; margin: auto; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .logo { color: var(--primary); font: 700 1.55rem Poppins, sans-serif; text-decoration: none; }
        .user-greeting, .nav-links { display: flex; align-items: center; gap: 14px; }
        .welcome-badge { display: flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 40px; background: var(--primary); color: white; font-weight: 600; }
        .nav-links a { color: var(--text-soft); text-decoration: none; font-weight: 600; padding: 8px 10px; border-radius: 8px; }
        .nav-links a:hover, .nav-links a.active { color: var(--primary); background: rgba(74,111,165,.1); }
        .logout-btn { padding: 9px 17px; border: 0; border-radius: 40px; color: white; background: linear-gradient(135deg, var(--primary), var(--coral)); text-decoration: none; font-weight: 600; }
        .hero { padding: 112px 24px 38px; text-align: center; }
        .hero h1 { margin: 0 0 8px; color: transparent; background: linear-gradient(90deg, var(--primary), var(--coral), var(--accent-dark)); -webkit-background-clip: text; background-clip: text; font: 700 2.4rem Poppins, sans-serif; }
        .hero p { margin: 0; color: var(--text-soft); font-size: 1.05rem; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        .game-shell { margin-bottom: 70px; }
        .menu-screen, .game-screen, .result-screen { display: none; padding: 28px; border: 1px solid rgba(74,111,165,.14); border-radius: var(--radius); background: linear-gradient(145deg, rgba(255,255,255,.98), rgba(255,250,240,.98)); box-shadow: var(--shadow-md); }
        .active { display: block !important; }
        .menu-grid { display: grid; grid-template-columns: 1.1fr .9fr; gap: 24px; }
        .menu-card { padding: 26px; border: 1px solid rgba(74,111,165,.1); border-radius: 20px; background: linear-gradient(135deg, rgba(255,255,255,.95), rgba(245,240,230,.9)); box-shadow: inset 0 1px 0 white; }
        .menu-card h2 { margin: 0 0 10px; color: var(--primary); font: 700 1.5rem Poppins, sans-serif; }
        .menu-card p { margin: 0 0 20px; color: var(--text-soft); }
        .mode-options { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .mode-card { position: relative; padding: 18px; border: 2px solid transparent; border-radius: 16px; background: white; text-align: left; color: var(--text-dark); box-shadow: var(--shadow-sm); transition: .2s ease; }
        .mode-card::after { content: ''; position: absolute; inset: 0; border-radius: inherit; pointer-events: none; background: linear-gradient(135deg, rgba(74,111,165,.08), rgba(232,184,107,.08)); opacity: 0; transition: .2s ease; }
        .mode-card:hover, .mode-card.selected { border-color: var(--accent); transform: translateY(-2px); }
        .mode-card:hover::after, .mode-card.selected::after { opacity: 1; }
        .mode-card strong { display: block; margin-bottom: 4px; }
        .mode-card small { color: var(--text-soft); }
        .button-row { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 22px; }
        .primary-btn, .secondary-btn, .ghost-btn { border: 0; border-radius: 12px; padding: 12px 20px; font-weight: 700; transition: .2s ease; }
        .primary-btn { color: white; background: linear-gradient(135deg, var(--primary), var(--primary-light), var(--teal)); box-shadow: 0 6px 16px rgba(74,111,165,.25); }
        .primary-btn:hover { transform: translateY(-2px); box-shadow: 0 9px 22px rgba(74,111,165,.32); }
        .secondary-btn { color: var(--primary); background: linear-gradient(135deg, #eef4fb, #fff1d9); border: 1px solid rgba(74,111,165,.16); }
        .ghost-btn { color: var(--text-soft); background: rgba(255,255,255,.75); border: 1px solid #d7dce3; }
        .stats-row { display: flex; flex-wrap: wrap; gap: 10px; margin: 20px 0; }
        .stat { min-width: 110px; padding: 10px 14px; border: 1px solid rgba(74,111,165,.12); border-radius: 12px; background: linear-gradient(135deg, white, #fff2d7); box-shadow: var(--shadow-sm); }
        .stat:nth-child(2) { background: linear-gradient(135deg, white, #fce9e8); }
        .stat:nth-child(3) { background: linear-gradient(135deg, white, #e5f6f3); }
        .stat:nth-child(4) { background: linear-gradient(135deg, white, #f4e9f3); }
        .stat span { display: block; color: var(--text-soft); font-size: .8rem; }
        .stat strong { color: var(--primary); font-size: 1.2rem; }
        .level-picker { display: flex; flex-wrap: wrap; gap: 9px; margin-top: 15px; }
        .level-btn { min-width: 58px; padding: 8px 12px; border: 1px solid #d8dee7; border-radius: 10px; color: var(--text-soft); background: white; box-shadow: var(--shadow-sm); }
        .level-btn.active { color: white; border-color: var(--teal); background: linear-gradient(135deg, var(--primary), var(--teal)); }
        .game-top { display: flex; justify-content: space-between; align-items: center; gap: 14px; margin-bottom: 20px; }
        .game-title h2 { margin: 0; color: var(--primary); font: 700 1.6rem Poppins, sans-serif; }
        .game-title p { margin: 4px 0 0; color: var(--text-soft); }
        .timer { min-width: 120px; padding: 10px 16px; border-radius: 14px; color: white; background: linear-gradient(135deg, var(--coral), var(--accent)); text-align: center; font-size: 1.15rem; font-weight: 700; box-shadow: 0 6px 18px rgba(242,118,103,.25); }
        .timer.warning { background: linear-gradient(135deg, var(--danger), var(--coral)); animation: pulse 1s infinite; }
        .progress-track { height: 9px; margin-bottom: 20px; overflow: hidden; border-radius: 20px; background: #e9e2d6; box-shadow: inset 0 1px 3px rgba(0,0,0,.08); }
        .progress-bar { width: 0; height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--primary), var(--teal), var(--accent), var(--coral)); transition: width .3s ease; }
        .game-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }
        .round-card, .word-bank { padding: 22px; border: 1px solid rgba(74,111,165,.1); border-radius: 18px; background: linear-gradient(145deg, #fff, #f6f1e9); box-shadow: var(--shadow-sm); }
        .round-card { background: linear-gradient(145deg, #fff, #fff2e3); }
        .word-bank { background: linear-gradient(145deg, #fff, #eaf7f4); }
        .round-card h3, .word-bank h3 { margin: 0 0 6px; color: var(--primary); font: 700 1.05rem Poppins, sans-serif; }
        .round-card p { margin: 0 0 18px; color: var(--text-soft); }
        .sentence { display: flex; min-height: 92px; align-items: center; justify-content: center; flex-wrap: wrap; gap: 9px; padding: 18px; border: 2px dashed rgba(74,111,165,.3); border-radius: 16px; background: linear-gradient(135deg, white, #fff9ec); text-align: center; }
        .sentence .empty { color: #9aa6b3; font-style: italic; }
        .selected-word { padding: 8px 14px; border-radius: 10px; color: white; background: linear-gradient(135deg, var(--primary), var(--teal)); font-weight: 600; box-shadow: 0 4px 10px rgba(74,111,165,.2); animation: appear .2s ease; }
        .word-bank { min-height: 280px; }
        .word-list { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
        .word-btn { padding: 10px 15px; border: 1px solid rgba(74,111,165,.2); border-radius: 11px; color: var(--text-dark); background: linear-gradient(135deg, white, #f5f8fc); font-weight: 600; box-shadow: 0 3px 7px rgba(0,0,0,.05); transition: .18s ease; }
        .word-btn:hover:not(:disabled) { color: white; border-color: var(--coral); background: linear-gradient(135deg, var(--coral), var(--accent)); transform: translateY(-2px); }
        .word-btn:disabled { opacity: .4; cursor: not-allowed; }
        .feedback { min-height: 28px; margin-top: 16px; padding: 9px 12px; border-radius: 10px; color: var(--text-soft); background: white; text-align: center; font-weight: 600; }
        .feedback.correct { color: var(--success); background: linear-gradient(90deg, #e9f8ef, #e5f5f2); }
        .feedback.wrong { color: var(--danger); background: linear-gradient(90deg, #fff0f2, #fff4e6); }
        .duel-board { display: none; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 18px; }
        .duel-player { padding: 16px; border: 1px solid rgba(74,111,165,.12); border-radius: 14px; background: linear-gradient(135deg, #fff, #f9eef0); text-align: center; box-shadow: var(--shadow-sm); transition: .2s ease; }
        .duel-player:nth-child(2) { background: linear-gradient(135deg, #fff, #e9f7f4); }
        .duel-player.active { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(232,184,107,.16), var(--shadow-sm); transform: translateY(-2px); }
        .duel-player strong { display: block; color: var(--primary); }
        .duel-player span { color: var(--text-soft); font-size: .9rem; }
        .result-screen { text-align: center; background: linear-gradient(145deg, rgba(255,255,255,.98), rgba(255,242,220,.98)); }
        .result-icon { display: grid; place-items: center; width: 78px; height: 78px; margin: 0 auto 12px; border-radius: 50%; color: white; background: linear-gradient(135deg, var(--primary), var(--coral), var(--gold)); box-shadow: 0 10px 28px rgba(242,118,103,.25); font-size: 2.2rem; }
        .result-screen h2 { margin: 0; font: 700 2rem Poppins, sans-serif; color: var(--primary); }
        .result-screen p { color: var(--text-soft); }
        .score-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin: 24px 0; }
        .score-box { padding: 16px; border-radius: 14px; background: linear-gradient(135deg, #eef4fb, #fff2d7); box-shadow: var(--shadow-sm); }
        .score-box:nth-child(2) { background: linear-gradient(135deg, #e7f7f4, #eef4fb); }
        .score-box:nth-child(3) { background: linear-gradient(135deg, #fbeef5, #fff2d7); }
        .score-box span { display: block; color: var(--text-soft); font-size: .85rem; }
        .score-box strong { color: var(--primary); font-size: 1.6rem; }
        .footer { padding: 32px 24px; color: white; background: linear-gradient(90deg, var(--text-dark), #344d6a, var(--primary)); text-align: center; }
        @keyframes appear { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: none; } }
        @keyframes pulse { 50% { transform: scale(1.03); } }
        @media (max-width: 820px) {
            .navbar .container { align-items: flex-start; flex-direction: column; }
            .nav-links { flex-wrap: wrap; }
            .menu-grid, .game-grid { grid-template-columns: 1fr; }
            .hero { padding-top: 170px; }
            .score-grid, .duel-board { grid-template-columns: 1fr; }
        }
        @media (max-width: 520px) {
            .container { padding: 0 14px; }
            .menu-screen, .game-screen, .result-screen { padding: 18px; }
            .mode-options { grid-template-columns: 1fr; }
            .game-top { align-items: flex-start; flex-direction: column; }
            .timer { width: 100%; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">English Explorer</a>
            <div class="user-greeting">
                <div class="welcome-badge"><i class="fas fa-user"></i> Olá, <?= htmlspecialchars($apelido) ?></div>
                <div class="nav-links">
                    <a href="index.php">Início</a>
                    <a href="dicionario.php">Dicionário</a>
                    <a href="flashcards.php">Flashcards</a>
                    <a href="mapa.php">Mapa</a>
                    <a href="translatemaster.php">TranslateMaster</a>
                </div>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <h1>Word Builder</h1>
        <p>Monte frases em inglês, one word at a time.</p>
    </section>

    <main class="container game-shell">
        <section id="menuScreen" class="menu-screen active">
            <div class="menu-grid">
                <div class="menu-card">
                    <h2>Como jogar</h2>
                    <p>As palavras serão mostradas fora de ordem. Clique na sequência correta para formar a frase.</p>
                    <div class="mode-options">
                        <button type="button" class="mode-card selected" data-mode="solo"><i class="fas fa-user"></i><strong>Solo</strong><small>Treine no seu ritmo</small></button>
                        <button type="button" class="mode-card" data-mode="duelo"><i class="fas fa-users"></i><strong>Duelo</strong><small>Dois jogadores competem</small></button>
                    </div>
                    <div class="level-picker" id="levelPicker"></div>
                </div>
                <div class="menu-card">
                    <h2>Regras</h2>
                    <ul>
                        <li>Frases de 3 a 4 palavras no nível 1.</li>
                        <li>Os níveis aumentam a complexidade.</li>
                        <li>Errores reduz a velocidade e o tempo.</li>
                        <li>Precisão e tempo aceleram a pontuação.</li>
                    </ul>
                    <div class="button-row">
                        <button id="startButton" class="primary-btn"><i class="fas fa-play"></i> Começar</button>
                        <button id="howButton" class="secondary-btn"><i class="fas fa-question-circle"></i> Mais informações</button>
                    </div>
                </div>
            </div>
        </section>

        <section id="gameScreen" class="game-screen">
            <div class="game-top">
                <div class="game-title">
                    <h2 id="gameTitle">Nível 1</h2>
                    <p id="gameSubtitle">Monte a frase correta</p>
                </div>
                <div class="timer" id="timer">⏱ 60s</div>
            </div>
            <div class="stats-row">
                <div class="stat"><span>Pontos</span><strong id="score">0</strong></div>
                <div class="stat"><span>Erros</span><strong id="errors">0</strong></div>
                <div class="stat"><span>Precisão</span><strong id="precision">100%</strong></div>
                <div class="stat"><span>Frase</span><strong id="round">1/5</strong></div>
            </div>
            <div class="progress-track"><div class="progress-bar" id="progressBar"></div></div>
            <div class="game-grid">
                <div class="round-card">
                    <h3>Sua frase</h3>
                    <p>Clique nas palavras na ordem correta.</p>
                    <div class="sentence" id="sentence"><span class="empty">A frase aparecerá aqui...</span></div>
                    <div class="feedback" id="feedback">Acompanhe o tempo e a precisão.</div>
                    <div class="button-row">
                        <button id="resetButton" class="ghost-btn"><i class="fas fa-undo"></i> Recomeçar frase</button>
                        <button id="hintButton" class="secondary-btn"><i class="fas fa-lightbulb"></i> Dica</button>
                    </div>
                </div>
                <div class="word-bank">
                    <h3>Palavras disponíveis</h3>
                    <p>Use as palavras para montar a frase.</p>
                    <div class="word-list" id="wordList"></div>
                </div>
            </div>
            <div class="duel-board" id="duelBoard">
                <div class="duel-player active" id="playerOne"><strong>Jogador 1</strong><span>Pontos: 0</span></div>
                <div class="duel-player" id="playerTwo"><strong>Jogador 2</strong><span>Pontos: 0</span></div>
            </div>
        </section>

        <section id="resultScreen" class="result-screen">
            <div class="result-icon"><i class="fas fa-trophy"></i></div>
            <h2 id="resultTitle">Jogo concluído!</h2>
            <p id="resultMessage">Você terminou todas as frases.</p>
            <div class="score-grid">
                <div class="score-box"><span>Pontos finais</span><strong id="finalScore">0</strong></div>
                <div class="score-box"><span>Precisão</span><strong id="finalPrecision">100%</strong></div>
                <div class="score-box"><span>Tempo médio</span><strong id="finalTime">0s</strong></div>
            </div>
            <div class="button-row" style="justify-content:center">
                <button id="playAgainButton" class="primary-btn"><i class="fas fa-rotate"></i> Jogar novamente</button>
                <button id="menuButton" class="secondary-btn"><i class="fas fa-home"></i> Voltar ao menu</button>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 English Explorer - Speak like a native</p>
        </div>
    </footer>

    <script>
        const levels = [
            { name: 'Nível 1', description: 'Frases de 3 a 4 palavras', time: 60, phrases: [
                { sentence: 'I am learning English', hint: 'Eu estou aprendendo inglês.' },
                { sentence: 'The cat is happy', hint: 'O gato está feliz.' },
                { sentence: 'We play football daily', hint: 'Jogamos futebol todos os dias.' },
                { sentence: 'My friend lives here', hint: 'Meu amigo mora aqui.' },
                { sentence: 'Please open the door', hint: 'Abra a porta, por favor.' }
            ]},
            { name: 'Nível 2', description: 'Frases de 4 a 5 palavras', time: 55, phrases: [
                { sentence: 'I want a new computer', hint: 'Eu quero um novo computador.' },
                { sentence: 'She works in a small office', hint: 'Ela trabalha em um pequeno escritório.' },
                { sentence: 'We study English every night', hint: 'Estudamos inglês todas as noites.' },
                { sentence: 'The book is on the table', hint: 'O livro está sobre a mesa.' },
                { sentence: 'My brother plays the guitar', hint: 'Meu irmão toca violão.' }
            ]},
            { name: 'Nível 3', description: 'Frases de 5 a 6 palavras', time: 50, phrases: [
                { sentence: 'I am going to the market tomorrow', hint: 'Vou ao mercado amanhã.' },
                { sentence: 'The children are playing outside', hint: 'As crianças estão brincando fora.' },
                { sentence: 'We need to improve our pronunciation', hint: 'Precisamos melhorar nossa pronúncia.' },
                { sentence: 'The teacher explains the lesson clearly', hint: 'O professor explica a aula com clareza.' },
                { sentence: 'I have lived here for three years', hint: 'Moro aqui há três anos.' }
            ]},
            { name: 'Nível 4', description: 'Frases mais complexas', time: 45, phrases: [
                { sentence: 'I have been studying for two hours', hint: 'Estou estudando há duas horas.' },
                { sentence: 'The weather is very cold today', hint: 'O tempo está muito frio hoje.' },
                { sentence: 'We should visit the museum next week', hint: 'Devemos visitar o museu na próxima semana.' },
                { sentence: 'She is interested in learning history', hint: 'Ela gosta de aprender história.' },
                { sentence: 'I cannot remember where I placed my keys', hint: 'Não consigo lembrar onde deixei minhas chaves.' }
            ]},
            { name: 'Nível 5', description: 'Frases avançadas elongadas', time: 40, phrases: [
                { sentence: 'I have finished reading the article completely', hint: 'Terminei de ler o artigo completamente.' },
                { sentence: 'The beautiful garden is beside the library', hint: 'O jardim bonito fica ao lado da biblioteca.' },
                { sentence: 'We must be careful when speaking with strangers', hint: 'Temos ter cuidado ao falar com desconhecidos.' },
                { sentence: 'I am looking forward to meeting you tomorrow', hint: 'Estou ansioso para te encontrar amanhã.' },
                { sentence: 'The most important thing is to stay calm', hint: 'O mais importante é ficar calmo.' }
            ]}
        ];

        const state = {
            level: 0,
            mode: 'solo',
            currentPhrase: 0,
            totalPhrases: 5,
            words: [],
            selected: [],
            score: 0,
            errors: 0,
            elapsed: 0,
            timerId: null,
            timeLeft: 0,
            precision: 100,
            duelScores: { playerOne: 0, playerTwo: 0 },
            activePlayer: 'playerOne',
            hintVisible: false
        };

        const elements = {
            menu: document.getElementById('menuScreen'), game: document.getElementById('gameScreen'), result: document.getElementById('resultScreen'),
            levelPicker: document.getElementById('levelPicker'), wordList: document.getElementById('wordList'), sentence: document.getElementById('sentence'),
            feedback: document.getElementById('feedback'), timer: document.getElementById('timer'), score: document.getElementById('score'),
            errors: document.getElementById('errors'), precision: document.getElementById('precision'), round: document.getElementById('round'),
            progressBar: document.getElementById('progressBar'), gameTitle: document.getElementById('gameTitle'), gameSubtitle: document.getElementById('gameSubtitle'),
            duelBoard: document.getElementById('duelBoard'), playerOne: document.getElementById('playerOne'), playerTwo: document.getElementById('playerTwo')
        };

        function renderLevels() {
            elements.levelPicker.innerHTML = '';
            levels.forEach((level, index) => {
                const button = document.createElement('button');
                button.className = `level-btn${index === state.level ? ' active' : ''}`;
                button.textContent = `L${index + 1}`;
                button.title = level.description;
                button.addEventListener('click', () => {
                    state.level = index;
                    renderLevels();
                });
                elements.levelPicker.appendChild(button);
            });
        }

        function chooseMode(mode) {
            state.mode = mode;
            document.querySelectorAll('.mode-card').forEach(card => card.classList.toggle('selected', card.dataset.mode === mode));
            elements.duelBoard.style.display = state.mode === 'duelo' ? 'grid' : 'none';
        }

        function shuffle(items) {
            const copy = [...items];
            for (let i = copy.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [copy[i], copy[j]] = [copy[j], copy[i]];
            }
            return copy;
        }

        function startGame() {
            clearInterval(state.timerId);
            state.currentPhrase = 0;
            state.totalPhrases = levels[state.level].phrases.length;
            state.score = 0;
            state.errors = 0;
            state.elapsed = 0;
            state.precision = 100;
            state.duelScores = { playerOne: 0, playerTwo: 0 };
            state.selected = [];
            state.timeLeft = levels[state.level].time;
            state.activePlayer = 'playerOne';
            elements.menu.classList.remove('active');
            elements.result.classList.remove('active');
            elements.game.classList.add('active');
            elements.gameTitle.textContent = levels[state.level].name;
            elements.gameSubtitle.textContent = levels[state.level].description;
            elements.playerOne.classList.add('active');
            elements.playerTwo.classList.remove('active');
            loadPhrase();
        }

        function loadPhrase() {
            const phraseData = levels[state.level].phrases[state.currentPhrase];
            state.words = phraseData.sentence.split(' ');
            state.selected = [];
            state.timeLeft = levels[state.level].time;
            state.hintVisible = false;
            elements.sentence.innerHTML = '<span class="empty">Selecione as palavras...</span>';
            elements.feedback.className = 'feedback';
            elements.feedback.textContent = 'Acompanhe o tempo e a precisão.';
            renderWords();
            updateHud();
            startTimer();
        }

        function renderWords() {
            elements.wordList.innerHTML = '';
            shuffle(state.words).forEach(word => {
                const button = document.createElement('button');
                button.className = 'word-btn';
                button.textContent = word;
                button.addEventListener('click', () => selectWord(word));
                elements.wordList.appendChild(button);
            });
        }

        function selectWord(word) {
            if (state.timeLeft <= 0) return;
            const expected = state.words[state.selected.length];
            if (word === expected) {
                state.selected.push(word);
                elements.sentence.innerHTML = '';
                state.selected.forEach(selectedWord => {
                    const span = document.createElement('span');
                    span.className = 'selected-word';
                    span.textContent = selectedWord;
                    elements.sentence.appendChild(span);
                });
                elements.feedback.className = 'feedback correct';
                elements.feedback.textContent = state.selected.length === state.words.length ? 'Frase concluída!' : 'Palavra correta.';
                if (state.selected.length === state.words.length) {
                    finishPhrase(true);
                }
            } else {
                state.errors++;
                state.precision = Math.max(0, Math.round((state.precision * 100 - 100) / 100));
                state.precision = Math.max(0, state.precision - Math.round(100 / Math.max(1, state.words.length)));
                state.timeLeft = Math.max(0, state.timeLeft - 4);
                elements.feedback.className = 'feedback wrong';
                elements.feedback.textContent = 'Erro! -4s. Tente novamente.';
                if (state.selected.length > 0) state.selected = [];
                elements.sentence.innerHTML = '<span class="empty">A frase foi reiniciada.</span>';
                updateHud();
            }
        }

        function finishPhrase(success) {
            clearInterval(state.timerId);
            if (!success) return;
            const remaining = Math.max(0, state.timeLeft);
            const speedBonus = Math.round(remaining * 3);
            const accuracyBonus = Math.round((state.precision / 100) * 100);
            const earned = Math.max(50, 100 + speedBonus + accuracyBonus);
            state.score += earned;
            if (state.mode === 'duelo') {
                state.duelScores[state.activePlayer] += earned;
                const nextPlayer = state.activePlayer === 'playerOne' ? 'playerTwo' : 'playerOne';
                state.activePlayer = nextPlayer;
                elements.playerOne.classList.toggle('active', state.activePlayer === 'playerOne');
                elements.playerTwo.classList.toggle('active', state.activePlayer === 'playerTwo');
                updateDuelHud();
            }
            state.currentPhrase++;
            if (state.currentPhrase >= state.totalPhrases) {
                finishGame();
            } else {
                elements.feedback.className = 'feedback correct';
                elements.feedback.textContent = `+${earned} pontos! ${state.mode === 'duelo' ? `O ${state.activePlayer === 'playerOne' ? 'Jogador 1' : 'Jogador 2'} está jogando agora.` : ''}`;
                setTimeout(loadNextPhrase, 900);
            }
        }

        function loadNextPhrase() {
            loadPhrase();
        }

        function startTimer() {
            clearInterval(state.timerId);
            state.timerId = setInterval(() => {
                if (state.timeLeft <= 0) {
                    clearInterval(state.timerId);
                    elements.feedback.className = 'feedback wrong';
                    elements.feedback.textContent = 'Tempo esgotado. A frase foi interrompida.';
                    setTimeout(() => {
                        if (state.currentPhrase < state.totalPhrases) loadPhrase();
                    }, 900);
                    return;
                }
                state.timeLeft--;
                updateTimer();
            }, 1000);
        }

        function updateTimer() {
            elements.timer.textContent = `⏱ ${state.timeLeft}s`;
            elements.timer.classList.toggle('warning', state.timeLeft <= 10);
        }

        function updateHud() {
            const totalAttempts = Math.max(1, state.currentPhrase + state.errors + 1);
            const accuracy = Math.max(0, Math.round((1 - state.errors / totalAttempts) * 100));
            state.precision = Math.min(100, accuracy);
            elements.score.textContent = state.score;
            elements.errors.textContent = state.errors;
            elements.precision.textContent = `${state.precision}%`;
            elements.round.textContent = `${Math.min(state.currentPhrase + 1, state.totalPhrases)}/${state.totalPhrases}`;
            const progress = (state.currentPhrase / state.totalPhrases) * 100;
            elements.progressBar.style.width = `${progress}%`;
            updateTimer();
        }

        function updateDuelHud() {
            elements.playerOne.querySelector('span').textContent = `Pontos: ${state.duelScores.playerOne}`;
            elements.playerTwo.querySelector('span').textContent = `Pontos: ${state.duelScores.playerTwo}`;
        }

        function resetPhrase() {
            state.selected = [];
            state.words = levels[state.level].phrases[state.currentPhrase].sentence.split(' ');
            elements.sentence.innerHTML = '<span class="empty">A frase foi reiniciada.</span>';
            elements.feedback.className = 'feedback';
            elements.feedback.textContent = 'Frase reiniciada. Tente novamente.';
            renderWords();
        }

        function showHint() {
            const phraseData = levels[state.level].phrases[state.currentPhrase];
            elements.feedback.className = 'feedback';
            elements.feedback.textContent = phraseData.hint;
        }

        function finishGame() {
            clearInterval(state.timerId);
            const totalMinutes = state.elapsed / 60;
            const finalPrecision = Math.max(0, Math.round((1 - state.errors / Math.max(1, state.currentPhrase + state.errors)) * 100));
            document.getElementById('finalScore').textContent = state.score;
            document.getElementById('finalPrecision').textContent = `${finalPrecision}%`;
            document.getElementById('finalTime').textContent = `${Math.round(totalMinutes)}s`;
            document.getElementById('resultTitle').textContent = state.mode === 'duelo' ? `${state.duelScores.playerOne > state.duelScores.playerTwo ? 'Jogador 1' : 'Jogador 2'} venceu!` : 'Jogo concluído!';
            document.getElementById('resultMessage').textContent = state.mode === 'duelo' ? 'Dois jogadores competiram e o resultado foi registrado.' : 'Você montou todas as frases. Continue para melhorar sua pontuação.';
            elements.game.classList.remove('active');
            elements.result.classList.add('active');
        }

        document.querySelectorAll('.mode-card').forEach(card => card.addEventListener('click', () => chooseMode(card.dataset.mode)));
        document.getElementById('startButton').addEventListener('click', startGame);
        document.getElementById('howButton').addEventListener('click', () => {
            document.getElementById('feedback').textContent = 'As palavras aparecem fora de ordem. Clique na ordem correta; erros custam 4 segundos.';
        });
        document.getElementById('resetButton').addEventListener('click', resetPhrase);
        document.getElementById('hintButton').addEventListener('click', showHint);
        document.getElementById('playAgainButton').addEventListener('click', startGame);
        document.getElementById('menuButton').addEventListener('click', () => {
            clearInterval(state.timerId);
            elements.result.classList.remove('active');
            elements.menu.classList.add('active');
        });
        renderLevels();
    </script>
</body>
</html>
