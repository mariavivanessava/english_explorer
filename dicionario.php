<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit;
}

// PEGA O APELIDO DA SESSÃO
$apelido = isset($_SESSION['apelido']) ? $_SESSION['apelido'] : 'Usuário';

// Se não tiver apelido na sessão, busca no banco
if ($apelido == 'Usuário' && isset($_SESSION['id_usuario'])) {
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

// Arquivo para armazenar as palavras
$arquivoPalavras = 'palavras.json';

// Função para carregar palavras do arquivo JSON
function carregarPalavras() {
    global $arquivoPalavras;
    if (file_exists($arquivoPalavras)) {
        $json = file_get_contents($arquivoPalavras);
        return json_decode($json, true);
    }
    return [];
}

// Função para salvar palavras no arquivo JSON
function salvarPalavras($palavras) {
    global $arquivoPalavras;
    $json = json_encode($palavras, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($arquivoPalavras, $json);
}

// Dicionário inicial com palavras e gírias (mais de 140 itens)
$dicionarioInicial = [
    // Palavras comuns em inglês
    [
        'categoria' => 'palavra',
        'termo' => 'Hello',
        'significado' => 'Olá - Saudação comum em inglês',
        'exemplo' => 'Hello, how are you?'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Goodbye',
        'significado' => 'Adeus - Despedida',
        'exemplo' => 'Goodbye, see you tomorrow!'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Friend',
        'significado' => 'Amigo - Pessoa com quem temos afinidade',
        'exemplo' => 'She is my best friend.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Beautiful',
        'significado' => 'Bonito(a) - Algo agradável aos olhos',
        'exemplo' => 'The sunset is beautiful.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Delicious',
        'significado' => 'Delicioso - Comida saborosa',
        'exemplo' => 'This cake is delicious!'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Happy',
        'significado' => 'Feliz - Estado de alegria',
        'exemplo' => 'I am happy today!'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Sad',
        'significado' => 'Triste - Estado de tristeza',
        'exemplo' => 'The movie made me sad.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Fast',
        'significado' => 'Rápido - Algo com alta velocidade',
        'exemplo' => 'The car is very fast.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Slow',
        'significado' => 'Devagar - Algo com baixa velocidade',
        'exemplo' => 'The turtle is slow.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Big',
        'significado' => 'Grande - Algo de tamanho grande',
        'exemplo' => 'That is a big house.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Small',
        'significado' => 'Pequeno - Algo de tamanho reduzido',
        'exemplo' => 'I have a small dog.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'New',
        'significado' => 'Novo - Algo recente',
        'exemplo' => 'I bought a new phone.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Old',
        'significado' => 'Velho - Algo antigo',
        'exemplo' => 'This is an old building.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Good',
        'significado' => 'Bom - Algo positivo',
        'exemplo' => 'This is a good idea.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Bad',
        'significado' => 'Ruim - Algo negativo',
        'exemplo' => 'That was a bad decision.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Easy',
        'significado' => 'Fácil - Algo simples de fazer',
        'exemplo' => 'The test was easy.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Hard',
        'significado' => 'Difícil - Algo complicado',
        'exemplo' => 'Math is hard for some people.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Work',
        'significado' => 'Trabalho - Atividade profissional',
        'exemplo' => 'I go to work every day.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Study',
        'significado' => 'Estudar - Aprender algo',
        'exemplo' => 'I need to study for the exam.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Family',
        'significado' => 'Família - Grupo de parentes',
        'exemplo' => 'I love my family.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'House',
        'significado' => 'Casa - Lugar onde se mora',
        'exemplo' => 'They have a beautiful house.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Car',
        'significado' => 'Carro - Veículo automotor',
        'exemplo' => 'My car is red.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Food',
        'significado' => 'Comida - Alimento',
        'exemplo' => 'I love Italian food.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Water',
        'significado' => 'Água - Líquido essencial',
        'exemplo' => 'Drink plenty of water.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Time',
        'significado' => 'Tempo - Duração ou momento',
        'exemplo' => 'What time is it?'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Day',
        'significado' => 'Dia - Período de 24 horas',
        'exemplo' => 'Have a nice day!'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Night',
        'significado' => 'Noite - Período escuro do dia',
        'exemplo' => 'I sleep at night.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Love',
        'significado' => 'Amor - Sentimento profundo',
        'exemplo' => 'I love you.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Dream',
        'significado' => 'Sonho - Imaginação durante o sono ou objetivo',
        'exemplo' => 'Follow your dreams.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Music',
        'significado' => 'Música - Arte dos sons',
        'exemplo' => 'I listen to music every day.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Movie',
        'significado' => 'Filme - Obra cinematográfica',
        'exemplo' => 'Let\'s watch a movie.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Book',
        'significado' => 'Livro - Conjunto de páginas escritas',
        'exemplo' => 'I am reading a good book.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'School',
        'significado' => 'Escola - Instituição de ensino',
        'exemplo' => 'The school is nearby.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Teacher',
        'significado' => 'Professor - Quem ensina',
        'exemplo' => 'The teacher is very kind.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Student',
        'significado' => 'Estudante - Quem aprende',
        'exemplo' => 'She is a good student.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'City',
        'significado' => 'Cidade - Grande centro urbano',
        'exemplo' => 'New York is a big city.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Country',
        'significado' => 'País - Nação',
        'exemplo' => 'Brazil is my country.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Morning',
        'significado' => 'Manhã - Período do dia',
        'exemplo' => 'Good morning!'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Afternoon',
        'significado' => 'Tarde - Período após o meio-dia',
        'exemplo' => 'Good afternoon!'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Evening',
        'significado' => 'Noite (início) - Período do fim da tarde',
        'exemplo' => 'Good evening!'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Week',
        'significado' => 'Semana - Período de 7 dias',
        'exemplo' => 'See you next week!'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Month',
        'significado' => 'Mês - Período de 30 dias',
        'exemplo' => 'My birthday is this month.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Year',
        'significado' => 'Ano - Período de 365 dias',
        'exemplo' => 'Happy New Year!'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Hot',
        'significado' => 'Quente - Temperatura elevada',
        'exemplo' => 'The coffee is hot.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Cold',
        'significado' => 'Frio - Temperatura baixa',
        'exemplo' => 'It is very cold today.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Rain',
        'significado' => 'Chuva - Precipitação',
        'exemplo' => 'I love the rain.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Sun',
        'significado' => 'Sol - Estrela central do sistema solar',
        'exemplo' => 'The sun is shining.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Moon',
        'significado' => 'Lua - Satélite natural da Terra',
        'exemplo' => 'The moon is full tonight.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Star',
        'significado' => 'Estrela - Astro luminoso',
        'exemplo' => 'Look at the stars!'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Tree',
        'significado' => 'Árvore - Planta de grande porte',
        'exemplo' => 'There is a big tree in the garden.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Flower',
        'significado' => 'Flor - Estrutura reprodutiva das plantas',
        'exemplo' => 'The flowers are beautiful.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Dog',
        'significado' => 'Cachorro - Animal de estimação',
        'exemplo' => 'My dog is very friendly.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Cat',
        'significado' => 'Gato - Animal de estimação felino',
        'exemplo' => 'The cat is sleeping.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Bird',
        'significado' => 'Pássaro - Animal que voa',
        'exemplo' => 'I can hear the birds singing.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Fish',
        'significado' => 'Peixe - Animal aquático',
        'exemplo' => 'I caught a big fish!'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Bed',
        'significado' => 'Cama - Móvel para dormir',
        'exemplo' => 'I am going to bed now.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Door',
        'significado' => 'Porta - Entrada de um ambiente',
        'exemplo' => 'Please close the door.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Window',
        'significado' => 'Janela - Abertura na parede',
        'exemplo' => 'Open the window, please.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Money',
        'significado' => 'Dinheiro - Meio de troca',
        'exemplo' => 'I need some money.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Job',
        'significado' => 'Emprego - Trabalho remunerado',
        'exemplo' => 'I have a new job.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Health',
        'significado' => 'Saúde - Bem-estar físico e mental',
        'exemplo' => 'Health is very important.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Help',
        'significado' => 'Ajuda - Auxílio, socorro',
        'exemplo' => 'Can you help me?'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Travel',
        'significado' => 'Viajar - Deslocar-se entre lugares',
        'exemplo' => 'I want to travel the world.'
    ],
    [
        'categoria' => 'palavra',
        'termo' => 'Weather',
        'significado' => 'Clima - Condições atmosféricas',
        'exemplo' => 'How is the weather today?'
    ],
    
    // Gírias e expressões em inglês (originais)
    [
        'categoria' => 'giria',
        'termo' => 'Cool',
        'significado' => 'Legal, maneiro - Expressão de aprovação',
        'exemplo' => 'That\'s a cool shirt!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Awesome',
        'significado' => 'Incrível, fantástico - Algo muito bom',
        'exemplo' => 'The concert was awesome!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'What\'s up?',
        'significado' => 'E aí? Como vai? - Saudação informal',
        'exemplo' => 'Hey, what\'s up?'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'No way!',
        'significado' => 'Não acredito! - Expressão de surpresa',
        'exemplo' => 'You won the lottery? No way!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Hang out',
        'significado' => 'Sair, passar tempo com alguém',
        'exemplo' => 'Let\'s hang out this weekend.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'OMG',
        'significado' => 'Oh My God - Ai meu Deus! Expressão de surpresa',
        'exemplo' => 'OMG! I can\'t believe it!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'LOL',
        'significado' => 'Laughing Out Loud - Rindo muito',
        'exemplo' => 'That meme is so funny, LOL!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'BTW',
        'significado' => 'By The Way - A propósito',
        'exemplo' => 'BTW, did you see the game?'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'IDK',
        'significado' => 'I Don\'t Know - Eu não sei',
        'exemplo' => 'IDK what to do.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Gonna',
        'significado' => 'Going to - Vou (forma informal)',
        'exemplo' => 'I\'m gonna go now.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Wanna',
        'significado' => 'Want to - Querer (forma informal)',
        'exemplo' => 'Do you wanna come?'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Gotta',
        'significado' => 'Got to - Tenho que (forma informal)',
        'exemplo' => 'I gotta go now.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Kinda',
        'significado' => 'Kind of - Tipo, meio que',
        'exemplo' => 'I\'m kinda tired.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Sorta',
        'significado' => 'Sort of - Mais ou menos',
        'exemplo' => 'It\'s sorta complicated.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Yeah',
        'significado' => 'Sim - Forma informal de yes',
        'exemplo' => 'Yeah, I agree.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Nope',
        'significado' => 'Não - Forma informal de no',
        'exemplo' => 'Nope, not today.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Yep',
        'significado' => 'Sim - Forma informal de yes',
        'exemplo' => 'Yep, that\'s right.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Chill out',
        'significado' => 'Relaxar, ficar tranquilo',
        'exemplo' => 'Just chill out, everything is fine.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Awesome sauce',
        'significado' => 'Algo extremamente legal',
        'exemplo' => 'That movie was awesome sauce!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Beat',
        'significado' => 'Exausto, muito cansado',
        'exemplo' => 'I worked all day, I\'m beat.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Binge watch',
        'significado' => 'Maratonar uma série',
        'exemplo' => 'I binge watched the entire season.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Crush',
        'significado' => 'Paixão, interesse amoroso',
        'exemplo' => 'She is my crush.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Epic fail',
        'significado' => 'Fracasso épico, falha total',
        'exemplo' => 'My attempt to cook was an epic fail.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Facepalm',
        'significado' => 'Gesto de frustração com a mão no rosto',
        'exemplo' => 'When he said that, I did a facepalm.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Ghosting',
        'significado' => 'Sumir, parar de responder mensagens',
        'exemplo' => 'He stopped talking to me, total ghosting.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'GOAT',
        'significado' => 'Greatest Of All Time - Melhor de todos os tempos',
        'exemplo' => 'Michael Jordan is the GOAT.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Hater',
        'significado' => 'Pessoa que critica negativamente',
        'exemplo' => 'Don\'t listen to the haters.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'It\'s lit',
        'significado' => 'Está animado, muito legal',
        'exemplo' => 'The party was lit!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Netflix and chill',
        'significado' => 'Frase usada para convidar alguém para assistir Netflix (geralmente com segundas intenções)',
        'exemplo' => 'Do you want to Netflix and chill?'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'On point',
        'significado' => 'Perfeito, impecável',
        'exemplo' => 'Your outfit is on point!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Salty',
        'significado' => 'Amargurado, irritado',
        'exemplo' => 'Why are you so salty today?'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Savage',
        'significado' => 'Alguém sem filtros, que fala o que pensa',
        'exemplo' => 'That response was savage!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Shade',
        'significado' => 'Desrespeito disfarçado, indireta',
        'exemplo' => 'She was throwing shade at him.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Squad',
        'significado' => 'Grupo de amigos próximos',
        'exemplo' => 'Going out with my squad tonight.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Throwback',
        'significado' => 'Recordação, nostalgia',
        'exemplo' => 'Throwback to our vacation last year.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'YOLO',
        'significado' => 'You Only Live Once - Você só vive uma vez',
        'exemplo' => 'Let\'s go skydiving, YOLO!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Catch up',
        'significado' => 'Colocar a conversa em dia',
        'exemplo' => 'Let\'s catch up over coffee.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Freak out',
        'significado' => 'Surtar, ficar muito nervoso',
        'exemplo' => 'Don\'t freak out, it\'s not a big deal.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Give a hand',
        'significado' => 'Dar uma mão, ajudar',
        'exemplo' => 'Can you give me a hand?'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Hit the road',
        'significado' => 'Pegar a estrada, sair',
        'exemplo' => 'We should hit the road early.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Keep in touch',
        'significado' => 'Manter contato',
        'exemplo' => 'Let\'s keep in touch after graduation.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Piece of cake',
        'significado' => 'Moleza, algo muito fácil',
        'exemplo' => 'The test was a piece of cake.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Pull an all-nighter',
        'significado' => 'Virar a noite estudando ou trabalhando',
        'exemplo' => 'I had to pull an all-nighter for the exam.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Sit tight',
        'significado' => 'Aguardar pacientemente',
        'exemplo' => 'Just sit tight, help is coming.'
    ],
    
    // =============================================
    // NOVAS GÍRIAS ENGRAÇADAS E ÚTEIS (70+ novas)
    // =============================================
    
    // Gírias engraçadas e divertidas
    [
        'categoria' => 'giria',
        'termo' => 'FOMO',
        'significado' => 'Fear Of Missing Out - Medo de ficar de fora de algo legal',
        'exemplo' => 'I have serious FOMO about that party.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'JOMO',
        'significado' => 'Joy Of Missing Out - Alegria de ficar em casa de boa',
        'exemplo' => 'JOMO is real, I love staying home.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Hangry',
        'significado' => 'Faminto + irritado (Hungry + Angry)',
        'exemplo' => 'Give me food, I\'m getting hangry!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Snack',
        'significado' => 'Alguém muito atraente (gíria: "gato/gata")',
        'exemplo' => 'Did you see him? He\'s a snack!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Thirsty',
        'significado' => 'Desesperado por atenção romântica',
        'exemplo' => 'He comments on every photo, so thirsty!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Clap back',
        'significado' => 'Responder de forma afiada a uma crítica',
        'exemplo' => 'Her clap back was legendary!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Tea',
        'significado' => 'Fofoca quente, novidade',
        'exemplo' => 'Spill the tea! What happened?'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Slay',
        'significado' => 'Arrasar, fazer algo de forma incrível',
        'exemplo' => 'You slay in that dress, girl!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Low-key',
        'significado' => 'Discretamente, na surdina',
        'exemplo' => 'I low-key love watching cartoons.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'High-key',
        'significado' => 'Abertamente, sem vergonha',
        'exemplo' => 'I high-key need a vacation.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Flex',
        'significado' => 'Se exibir, ostentar',
        'exemplo' => 'Stop flexing your new car!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Cringe',
        'significado' => 'Vergonha alheia, algo constrangedor',
        'exemplo' => 'His dad dancing is so cringe.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Sus',
        'significado' => 'Suspicious - Suspeito, estranho',
        'exemplo' => 'That guy is acting sus.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Cap',
        'significado' => 'Mentira (No cap = sem mentira)',
        'exemplo' => 'He said he\'s rich? That\'s cap!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Simp',
        'significado' => 'Alguém que faz de tudo por atenção romântica',
        'exemplo' => 'He bought her a car? What a simp!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Stan',
        'significado' => 'Fã obsessivo de alguém',
        'exemplo' => 'I stan this singer so hard!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Vibe check',
        'significado' => 'Testar o clima/energia do ambiente',
        'exemplo' => 'Vibe check! How is everyone feeling?'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Main character energy',
        'significado' => 'Agir como se fosse o protagonista',
        'exemplo' => 'She has main character energy!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Living rent-free',
        'significado' => 'Ocupar sua mente sem pagar aluguel',
        'exemplo' => 'That song lives rent-free in my head.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Big yikes',
        'significado' => 'Situação muito constrangedora',
        'exemplo' => 'He called her by the wrong name... big yikes!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'I\'m dead',
        'significado' => 'Estou morrendo de rir (não literalmente)',
        'exemplo' => 'That joke was so funny, I\'m dead!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Drip',
        'significado' => 'Estilo, roupa maneira',
        'exemplo' => 'Check out my new drip!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Glow up',
        'significado' => 'Transformação positiva na aparência',
        'exemplo' => 'She had a major glow up this year.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Receipts',
        'significado' => 'Provas, evidências (em discussões)',
        'exemplo' => 'I have the receipts to prove it!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Wig snatched',
        'significado' => 'Chocado, impressionado (positivamente)',
        'exemplo' => 'Her performance snatched my wig!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Periodt',
        'significado' => 'Ponto final (ênfase na afirmação)',
        'exemplo' => 'She is the best singer, periodt!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Bet',
        'significado' => 'Tá certo, combinado, pode apostar',
        'exemplo' => 'You coming tonight? Bet!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'No cap',
        'significado' => 'Sem mentira, sério mesmo',
        'exemplo' => 'This is the best pizza ever, no cap!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Touch grass',
        'significado' => 'Sair um pouco da internet (ir tomar ar)',
        'exemplo' => 'You\'ve been online all day, go touch grass.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Rizz',
        'significado' => 'Charme, capacidade de paquerar',
        'exemplo' => 'He has that rizz, no wonder girls like him.'
    ],
    
    // Gírias úteis para conversação
    [
        'categoria' => 'giria',
        'termo' => 'AFK',
        'significado' => 'Away From Keyboard - Longe do teclado',
        'exemplo' => 'BRB, I\'ll be AFK for a few minutes.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'BRB',
        'significado' => 'Be Right Back - Volto já',
        'exemplo' => 'BRB, gotta grab some water.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'TBH',
        'significado' => 'To Be Honest - Pra ser honesto',
        'exemplo' => 'TBH, I didn\'t like the movie.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'IMO',
        'significado' => 'In My Opinion - Na minha opinião',
        'exemplo' => 'IMO, this is the best option.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'IRL',
        'significado' => 'In Real Life - Na vida real',
        'exemplo' => 'We finally met IRL!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'DM',
        'significado' => 'Direct Message - Mensagem direta',
        'exemplo' => 'Slide into my DMs!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'TL;DR',
        'significado' => 'Too Long; Didn\'t Read - Resumo de texto longo',
        'exemplo' => 'Can you give me the TL;DR?'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'ASAP',
        'significado' => 'As Soon As Possible - O mais rápido possível',
        'exemplo' => 'I need this report ASAP!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'FYI',
        'significado' => 'For Your Information - Para sua informação',
        'exemplo' => 'FYI, the meeting was moved to 3pm.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'NVM',
        'significado' => 'Never Mind - Deixa pra lá',
        'exemplo' => 'NVM, I found the answer.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'TMI',
        'significado' => 'Too Much Information - Informação demais',
        'exemplo' => 'I didn\'t need to know that... TMI!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'NSFW',
        'significado' => 'Not Safe For Work - Conteúdo inapropriado',
        'exemplo' => 'This video is NSFW, watch later.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'AMA',
        'significado' => 'Ask Me Anything - Me pergunte qualquer coisa',
        'exemplo' => 'I\'m doing an AMA on Reddit tonight.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'ELI5',
        'significado' => 'Explain Like I\'m 5 - Explique de forma simples',
        'exemplo' => 'Can you ELI5 this concept?'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'MFW',
        'significado' => 'My Face When - Minha cara quando',
        'exemplo' => 'MFW I saw the surprise party!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'FTW',
        'significado' => 'For The Win - Para a vitória (algo muito bom)',
        'exemplo' => 'Pizza for dinner FTW!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'BFF',
        'significado' => 'Best Friends Forever - Melhores amigos para sempre',
        'exemplo' => 'She is my BFF since kindergarten.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'FOMO',
        'significado' => 'Fear Of Missing Out - Medo de ficar de fora',
        'exemplo' => 'I can\'t miss this, serious FOMO!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'GOATed',
        'significado' => 'Algo ou alguém que é o melhor (derivado de GOAT)',
        'exemplo' => 'That game is absolutely GOATed.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'POV',
        'significado' => 'Point Of View - Ponto de vista',
        'exemplo' => 'POV: You\'re about to eat the best cake ever.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Bussin\'',
        'significado' => 'Extremamente bom (especialmente comida)',
        'exemplo' => 'This burger is bussin\', no cap!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Based',
        'significado' => 'Alguém que fala verdades corajosas',
        'exemplo' => 'That opinion is so based, I agree!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Mid',
        'significado' => 'Mediano, mais ou menos, nada especial',
        'exemplo' => 'The movie was kinda mid, honestly.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Gucci',
        'significado' => 'Tudo bem, tudo certo (como a marca de luxo)',
        'exemplo' => 'Everything is Gucci, don\'t worry!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Slaps',
        'significado' => 'Muito bom (especialmente música)',
        'exemplo' => 'This new song slaps so hard!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Woke',
        'significado' => 'Consciente socialmente, ligado em questões sociais',
        'exemplo' => 'Stay woke and question everything.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Living my best life',
        'significado' => 'Viver a melhor versão da sua vida',
        'exemplo' => 'Traveling and eating good food, living my best life!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Boujee',
        'significado' => 'Luxuoso, chique (derivado de bourgeois)',
        'exemplo' => 'That restaurant is so boujee!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Fire',
        'significado' => 'Incrível, fantástico',
        'exemplo' => 'Your new shoes are straight fire!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Ghost',
        'significado' => 'Desaparecer, sumir sem avisar',
        'exemplo' => 'He ghosted me after the first date.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Ship',
        'significado' => 'Torcer por um casal (relationship)',
        'exemplo' => 'I totally ship those two characters!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Mood',
        'significado' => 'Algo com que você se identifica totalmente',
        'exemplo' => 'That picture of the tired cat is such a mood.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Cancelado',
        'significado' => 'Ser boicotado socialmente por fazer algo errado',
        'exemplo' => 'That actor got canceled last year.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Bop',
        'significado' => 'Música muito boa e dançante',
        'exemplo' => 'This song is a total bop!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Understood the assignment',
        'significado' => 'Entendeu perfeitamente o que era pra fazer',
        'exemplo' => 'She really understood the assignment with this outfit!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Spill',
        'significado' => 'Contar fofoca, revelar segredos',
        'exemplo' => 'Come on, spill! I want to know everything.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'It\'s giving',
        'significado' => 'Passa a vibe de...',
        'exemplo' => 'This outfit is giving 90s pop star.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Press F',
        'significado' => 'Mostrar respeito/prestar condolências (origem de jogo)',
        'exemplo' => 'He failed the test? Press F to pay respects.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'F in the chat',
        'significado' => 'Mesmo que Press F - preste condolências',
        'exemplo' => 'Phone battery died... F in the chat.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'CEO of',
        'significado' => 'Ser o melhor em algo, o CEO de algo',
        'exemplo' => 'She is the CEO of being late.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Pick-me',
        'significado' => 'Pessoa que se esforça para ser escolhida/validada',
        'exemplo' => 'She\'s such a pick-me, always putting others down.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Karen',
        'significado' => 'Mulher rude e exigente (estereótipo)',
        'exemplo' => 'The customer was being a total Karen.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Let him cook',
        'significado' => 'Deixe ele pensar/fazer o que está planejando',
        'exemplo' => 'Wait, let him cook, I think he has a plan.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Ratio',
        'significado' => 'Quando um reply tem mais likes que o post original',
        'exemplo' => 'Dude got ratio\'d so hard on Twitter.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Cope',
        'significado' => 'Lidar com algo difícil (usado ironicamente)',
        'exemplo' => 'You lost the game? Cope harder.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Seethe',
        'significado' => 'Ficar fervendo de raiva',
        'exemplo' => 'He was seething after losing the debate.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Rent-free',
        'significado' => 'Algo ocupa sua mente sem parar',
        'exemplo' => 'That embarrassing moment lives rent-free in my head.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Caught in 4K',
        'significado' => 'Pego no flagra com provas',
        'exemplo' => 'He denied it but was caught in 4K.'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Ate',
        'significado' => 'Arrasou, fez algo incrível',
        'exemplo' => 'She ate that performance and left no crumbs!'
    ],
    [
        'categoria' => 'giria',
        'termo' => 'Left no crumbs',
        'significado' => 'Fez algo perfeitamente sem deixar nada',
        'exemplo' => 'Her presentation ate and left no crumbs!'
    ]
];

// Carregar palavras existentes ou criar novo arquivo com dicionário inicial
$palavras = carregarPalavras();

// FORÇAR ATUALIZAÇÃO: se o arquivo tiver menos de 140 palavras, recria com o dicionário completo
if (empty($palavras) || count($palavras) < 140) {
    $palavras = $dicionarioInicial;
    salvarPalavras($palavras);
    $mensagem = 'Dicionário atualizado com ' . count($palavras) . ' palavras!';
}
// Processar adição de nova palavra
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $categoria = $_POST['categoria'] ?? 'palavra';
    $termo = trim($_POST['termo'] ?? '');
    $significado = trim($_POST['significado'] ?? '');
    $exemplo = trim($_POST['exemplo'] ?? '');
    
    if (empty($termo) || empty($significado)) {
        $erro = 'Por favor, preencha o termo e o significado!';
    } else {
        // Verificar se a palavra já existe
        $existe = false;
        foreach ($palavras as $p) {
            if (strtolower($p['termo']) === strtolower($termo)) {
                $existe = true;
                break;
            }
        }
        
        if ($existe) {
            $erro = 'Esta palavra/gíria já existe no dicionário!';
        } else {
            $novaPalavra = [
                'categoria' => $categoria,
                'termo' => $termo,
                'significado' => $significado,
                'exemplo' => $exemplo
            ];
            
            $palavras[] = $novaPalavra;
            salvarPalavras($palavras);
            $mensagem = 'Palavra adicionada com sucesso!';
        }
    }
}

// Processar exclusão de palavra
if (isset($_GET['excluir'])) {
    $index = (int)$_GET['excluir'];
    if (isset($palavras[$index])) {
        array_splice($palavras, $index, 1);
        salvarPalavras($palavras);
        $mensagem = 'Palavra removida com sucesso!';
    }
    header('Location: dicionario.php');
    exit;
}

function normalizarLimite($limite) {
    if ($limite === 'todas') {
        return 'todas';
    }

    $limite = (int)$limite;
    return in_array($limite, [20, 40, 60], true) ? $limite : 20;
}

function prepararPalavrasFiltradas($palavras, $categoriaFiltro, $busca) {
    $busca = trim($busca);
    $palavrasFiltradas = [];

    foreach ($palavras as $indice => $palavra) {
        if ($categoriaFiltro !== 'todas' && $palavra['categoria'] !== $categoriaFiltro) {
            continue;
        }

        if ($busca !== '' && stripos($palavra['termo'], $busca) === false) {
            continue;
        }

        $palavra['indice_original'] = $indice;
        $palavrasFiltradas[] = $palavra;
    }

    $buscaNormalizada = mb_strtolower(trim($busca));
    $letraBusca = $buscaNormalizada !== '' ? mb_substr($buscaNormalizada, 0, 1) : '';

    usort($palavrasFiltradas, function($a, $b) use ($letraBusca) {
        $termoA = mb_strtolower($a['termo']);
        $termoB = mb_strtolower($b['termo']);

        if ($letraBusca !== '') {
            $aComecaComLetra = mb_substr($termoA, 0, 1) === $letraBusca;
            $bComecaComLetra = mb_substr($termoB, 0, 1) === $letraBusca;

            if ($aComecaComLetra !== $bComecaComLetra) {
                return $aComecaComLetra ? -1 : 1;
            }
        }

        return strcasecmp($a['termo'], $b['termo']);
    });

    return $palavrasFiltradas;
}

function renderizarPalavras($palavrasExibidas, $categoriaFiltro, $busca, $limiteExibicao) {
    ob_start();

    if (empty($palavrasExibidas)): ?>
        <div class="no-results">
            <i class="fas fa-frown"></i><br>
            Nenhuma palavra encontrada. Que tal adicionar uma nova?
        </div>
    <?php else: ?>
        <?php foreach ($palavrasExibidas as $palavra): ?>
            <div class="word-card word-card-<?php echo htmlspecialchars($palavra['categoria']); ?>">
                <span class="word-category category-<?php echo htmlspecialchars($palavra['categoria']); ?>">
                    <?php echo $palavra['categoria'] === 'palavra' ? '📖 Palavra' : '💬 G&iacute;ria'; ?>
                </span>
                <div class="word-term"><?php echo htmlspecialchars($palavra['termo']); ?></div>
                <div class="word-meaning">
                    <strong>Significado:</strong> <?php echo htmlspecialchars($palavra['significado']); ?>
                </div>
                <?php if (!empty($palavra['exemplo'])): ?>
                    <div class="word-example">
                        <strong>Exemplo:</strong> "<?php echo htmlspecialchars($palavra['exemplo']); ?>"
                    </div>
                <?php endif; ?>
                <a href="?excluir=<?php echo (int)$palavra['indice_original']; ?>&categoria=<?php echo urlencode($categoriaFiltro); ?>&busca=<?php echo urlencode($busca); ?>&limite=<?php echo urlencode($limiteExibicao); ?>" 
                   class="delete-btn" 
                   onclick="return confirm('Tem certeza que deseja excluir esta palavra?')">
                    <i class="fas fa-trash-alt"></i> Excluir
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif;

    return ob_get_clean();
}

// Filtrar por categoria, busca e limite de exibicao
$categoriaFiltro = $_GET['categoria'] ?? 'todas';
if (!in_array($categoriaFiltro, ['todas', 'palavra', 'giria'], true)) {
    $categoriaFiltro = 'todas';
}

$busca = $_GET['busca'] ?? '';
$limiteExibicao = normalizarLimite($_GET['limite'] ?? 20);

$palavrasFiltradas = prepararPalavrasFiltradas($palavras, $categoriaFiltro, $busca);
$totalEncontradas = count($palavrasFiltradas);
$palavrasExibidas = $limiteExibicao === 'todas'
    ? $palavrasFiltradas
    : array_slice($palavrasFiltradas, 0, $limiteExibicao);

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'html' => renderizarPalavras($palavrasExibidas, $categoriaFiltro, $busca, $limiteExibicao),
        'total' => $totalEncontradas,
        'exibindo' => count($palavrasExibidas)
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>English Explorer - Dicionário</title>
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
            background: linear-gradient(135deg, #f8fbff 0%, #fff7ed 42%, #eef7ff 100%);
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
            max-width: 1400px;
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
            color: #4A6FA5;
            font-size: 1.9rem;
        }
        
        /* Container principal - removendo padding lateral e centralização */
        .main-container {
            max-width: 100%;
            margin: 0;
            padding: 32px 40px 40px;
        }
        
        /* Para o container de conteúdo */
        .content-wrapper {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }
        
        /* Cards - agora sem largura máxima fixa */
        .content-card {
            background: var(--warm-card);
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
            width: 100%;
        }
        
        .content-card h2 {
            color: var(--primary);
            margin-bottom: 25px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Formulário */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        /* Mensagens */
        .message {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease;
        }
        
        .success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .error {
            background: linear-gradient(135deg, #f85b5b, #f5c6cb);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Filtros */
        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filter-btn {
            background: var(--soft-gray);
            color: var(--text-soft);
            padding: 8px 24px;
            border-radius: 40px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
        }
        
        .filter-btn:hover:not(.active) {
            background: #e0e0e0;
            transform: translateY(-2px);
        }
        
        .search-box {
            flex: 1;
            min-width: 250px;
        }
        
        .search-box input {
            width: 100%;
            padding: 8px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 40px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--accent);
        }

        .limit-box {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-soft);
            font-weight: 500;
        }

        .limit-box select {
            padding: 8px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 40px;
            background: white;
            color: var(--text-dark);
            font-size: 1rem;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .limit-box select:focus {
            outline: none;
            border-color: var(--accent);
        }
        
        /* Stats */
        .stats {
            background: var(--soft-gray);
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            color: var(--text-soft);
            font-weight: 500;
        }
        
        /* Grid de palavras - 4 por linha em telas grandes, 3 em médias, 2 em tablets, 1 em celular */
        .dictionary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            width: 100%;
        }
        
        .word-card {
            background: var(--warm-card);
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: var(--border-radius-sm);
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: var(--shadow-sm);
            height: 100%;
            min-height: 340px;
            display: flex;
            flex-direction: column;
        }
        
        .word-card:hover {
            transform: none;
            box-shadow: var(--shadow-sm);
        }

        .word-category {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem !important;
        }
        
        .category-palavra {
            background: linear-gradient(135deg, #e3f2fd, #bbdef5);
            color: #1976d2;
        }
        
        .category-giria {
            background: linear-gradient(135deg, #f7deb5, #ffe0b5);
            color: #f57c00;
        }
        
        .word-term {
            font-size: 1.3rem !important;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 12px;
            padding-right: 80px;
            line-height: 1.3;
            min-height: 58px;
            display: flex;
            align-items: center;
        }

        .word-meaning {
            font-size: 0.95rem !important;
            margin-bottom: 12px;
            line-height: 1.5;
            flex: 1;
            min-height: 104px;
        }
        
        .word-meaning strong {
            color: var(--text-dark);
        }
        
        .word-example {
            background: var(--soft-gray);
            padding: 12px;
            border-radius: 10px;
            font-style: italic;
            color: var(--text-soft);
            font-size: 0.85rem !important;
            margin-top: 12px;
            line-height: 1.4;
            min-height: 76px;
            display: flex;
            align-items: center;
        }
        
        .delete-btn {
            display: inline-block;
            margin-top: 15px;
            background: #db9a44ff;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .delete-btn:hover {
            background: #ec832cff;
            transform: scale(1.05);
        }
        
        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px;
            color: var(--text-soft);
            font-size: 1.1rem;
        }
        
        .no-results i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--accent);
        }
        
        /* Responsividade */
        @media (max-width: 1400px) {
            .dictionary-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 1024px) {
            .dictionary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .navbar .container {
                flex-direction: column;
                gap: 15px;
            }
            
            .hero {
                margin-top: 140px;
            }
            
            .dictionary-grid {
                grid-template-columns: 1fr;
            }
            
            .filters {
                flex-direction: column;
            }
            
            .filter-btn {
                width: 100%;
                text-align: center;
            }
            
            .main-container {
                padding: 0 20px;
            }
        }
        
        /* Footer */
        .footer {
            background: var(--text-dark);
            color: white;
            padding: 60px 0 30px;
            margin-top: 60px;
        }
        
        .footer .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .footer .container {
                padding: 0 20px;
            }
        }
    </style>
    <link rel="stylesheet" href="tema-explorer.css">
    <style>
        body.dictionary-page .navbar .container {
            max-width: 1400px;
        }

        body.dictionary-page .nav-links {
            align-items: center;
        }

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
            width: 100%;
            justify-content: flex-start;
            border-radius: 8px !important;
            background: transparent !important;
        }

        .dictionary-workspace {
            display: block;
            width: 100%;
            padding-top: 16px;
            padding-bottom: 32px;
        }

        body.dictionary-page .content-card {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
            padding: 0 !important;
        }

        body.dictionary-page .content-card::before {
            display: none !important;
        }

        .dictionary-toolbar {
            display: flex;
            justify-content: flex-end;
            margin: 0 0 18px;
        }

        .add-word-trigger {
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, #f4b765 0%, #e99952 45%, #d97a4f 100%);
            color: #fff;
            padding: 10px 18px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 22px rgba(233, 153, 82, 0.22);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-left: auto;
            margin-top: 2px;
            align-self: flex-end;
            white-space: nowrap;
        }

        .add-word-trigger:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(91, 120, 169, 0.28);
        }

        .add-word-modal {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            z-index: 1200;
        }

        .add-word-modal.open {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .add-word-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(18, 25, 35, 0.62);
            backdrop-filter: blur(2px);
        }

        .add-word-modal-panel {
            position: relative;
            width: min(500px, 100%);
            max-height: 90vh;
            overflow: auto;
            z-index: 1;
        }

        .add-word-modal-panel .content-card {
            background: linear-gradient(135deg, rgba(248, 250, 253, 0.97), rgba(255, 247, 238, 0.96)) !important;
            border: 1px solid rgba(74, 111, 165, 0.12) !important;
        }

        .modal-close {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 2;
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            color: var(--text-dark);
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }

        .add-word-card {
            position: relative;
            padding: 0;
            background: linear-gradient(135deg, rgba(245, 249, 255, 0.97), rgba(255, 248, 240, 0.98)) !important;
            border: 1px solid rgba(74, 111, 165, 0.12) !important;
            border-radius: 18px !important;
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.14) !important;
            margin: 0;
            overflow: hidden;
        }

        .add-word-card-header {
            background: linear-gradient(135deg, #4A6FA5 0%, #6F9ED9 45%, #F2B868 100%);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font-weight: 700;
            box-shadow: inset 0 -1px 0 rgba(255,255,255,0.2);
        }

        .add-word-card-header h2 {
            color: #fff !important;
        }

        .add-word-card form {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(74, 111, 165, 0.12);
            border-radius: 0 0 16px 16px;
            padding: 18px 16px 10px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.7);
            margin: 0;
        }

        .add-word-card h2 {
            margin: 0;
            font-size: 1.18rem;
            color: #fff;
        }

        .add-word-card .form-group {
            margin-bottom: 14px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.32);
            border: 1px solid rgba(74, 111, 165, 0.08);
        }

        .add-word-card .form-group label {
            margin-bottom: 6px;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .add-word-card .form-group input,
        .add-word-card .form-group select,
        .add-word-card .form-group textarea {
            padding: 10px 12px;
            font-size: 0.92rem;
            border: 1px solid rgba(74, 111, 165, 0.18);
            background: rgba(249, 251, 255, 0.95);
            border-radius: 10px;
            box-shadow: inset 0 1px 2px rgba(74, 111, 165, 0.04);
        }

        .add-word-card .form-group textarea {
            min-height: 76px;
        }

        .add-word-card .btn-submit {
            width: 100%;
            justify-content: center;
            padding: 11px 16px;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #4A6FA5 0%, #6B8CBE 100%) !important;
            box-shadow: 0 10px 18px rgba(74, 111, 165, 0.2);
        }

        .dictionary-panel {
            min-width: 0;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .dictionary-panel .filters {
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
            display: flex;
            flex-wrap: nowrap;
        }

        .dictionary-panel .filter-btn {
            padding: 8px 16px;
            font-size: 0.9rem;
            font-weight: 700;
            background: linear-gradient(135deg, rgba(74, 111, 165, 0.08), rgba(232, 184, 107, 0.12));
            border: 1px solid rgba(116, 141, 174, 0.08);
            white-space: nowrap;
        }

        .dictionary-panel .filter-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: #fff;
            box-shadow: 0 10px 18px rgba(74, 111, 165, 0.14);
        }

        .search-box {
            flex: 1;
            min-width: 180px;
            max-width: 340px;
            margin-left: 0;
        }

        .search-box input {
            width: 100%;
            height: 38px;
            font-size: 0.92rem;
            padding: 8px 16px;
            border-radius: 999px;
        }

        .dictionary-panel .limit-box {
            margin-left: 0;
            margin-right: 8px;
            white-space: nowrap;
        }

        .dictionary-panel .stats {
            margin-bottom: 18px;
        }

        body.dictionary-page .dictionary-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 260px));
            gap: 18px;
            justify-content: flex-start;
        }

        body.dictionary-page .word-card {
            --word-accent: #243047;
            --local-accent: #e6edf5 !important;
            border: 1px solid rgba(230, 237, 245, 0.7) !important;
            border-top: 1px solid rgba(230, 237, 245, 0.7) !important;
            background: rgba(255, 255, 255, 0.72) !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            width: 100%;
            max-width: 260px;
        }

        body.dictionary-page .word-card:hover {
            box-shadow: 0 12px 22px rgba(15, 23, 42, 0.08) !important;
            transform: translateY(-3px);
        }

        body.dictionary-page .word-card.word-card-palavra:hover {
            background: rgba(230, 240, 255, 0.9) !important;
        }

        body.dictionary-page .word-card.word-card-giria:hover {
            background: rgba(255, 240, 225, 0.92) !important;
        }

        body.dictionary-page .word-card-giria {
            --word-accent: #243047;
        }

        body.dictionary-page .word-term {
            color: var(--word-accent) !important;
        }

        body.dictionary-page .word-meaning {
            color: #5b6575 !important;
        }

        body.dictionary-page .word-example {
            background: #f6f8fb !important;
            color: #596274 !important;
            border: 1px solid #edf1f6;
        }

       body.dictionary-page .word-category.category-palavra,
body.dictionary-page .word-category.category-giria {
    /* background: #f1f4f8 !important; */
    /* color: #657084 !important; */
    border: 1px solid #e0e7ef;
    font-weight: 700;

        }

        body.dictionary-page .delete-btn {
            background: #eef2f7 !important;
            color: #4b5565 !important;
            box-shadow: none !important;
        }

        body.dictionary-page .delete-btn:hover {
            background: #e2e8f0 !important;
            color: #263244 !important;
        }

        @media (max-width: 1050px) {
            .dictionary-workspace {
                grid-template-columns: 1fr;
            }

            .add-word-card {
                position: static;
            }
        }

        @media (max-width: 768px) {
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
</head>
<body class="dictionary-page">
    <!-- Navbar igual ao index e mapa -->
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
                    <a href="dicionario.php" class="active">Dicionário</a>
                    <a href="flashcards.php">Flashcards</a>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container" style="max-width: 1400px; margin: 0 auto;">
            <h1>Dicionário</h1>
        </div>
    </section>

    <div class="main-container">
        <?php if ($mensagem): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <div class="dictionary-workspace">
            <div class="content-card dictionary-panel">
                <div class="filters">
                    <a href="?categoria=todas&busca=<?php echo urlencode($busca); ?>&limite=<?php echo urlencode($limiteExibicao); ?>" data-categoria="todas" class="filter-btn <?php echo $categoriaFiltro === 'todas' ? 'active' : ''; ?>">
                        📚 Todas
                    </a>
                    <a href="?categoria=palavra&busca=<?php echo urlencode($busca); ?>&limite=<?php echo urlencode($limiteExibicao); ?>" data-categoria="palavra" class="filter-btn <?php echo $categoriaFiltro === 'palavra' ? 'active' : ''; ?>">
                        📖 Palavras
                    </a>
                    <a href="?categoria=giria&busca=<?php echo urlencode($busca); ?>&limite=<?php echo urlencode($limiteExibicao); ?>" data-categoria="giria" class="filter-btn <?php echo $categoriaFiltro === 'giria' ? 'active' : ''; ?>">
                        💬 Gírias
                    </a>
                    <div class="search-box">
                        <form method="GET" action="dicionario.php" id="searchForm">
                            <input type="hidden" name="categoria" id="categoriaFiltro" value="<?php echo htmlspecialchars($categoriaFiltro); ?>">
                            <input type="hidden" name="limite" id="limiteFiltroHidden" value="<?php echo htmlspecialchars($limiteExibicao); ?>">
                            <input type="text" name="busca" placeholder="🔍 Buscar palavra ou gíria..." 
                                   value="<?php echo htmlspecialchars($busca); ?>" id="buscaInput">
                        </form>
                    </div>
                    <div class="limit-box">
                        <label for="limiteFiltro">Mostrar:</label>
                        <select id="limiteFiltro">
                            <option value="20" <?php echo $limiteExibicao === 20 ? 'selected' : ''; ?>>20</option>
                            <option value="40" <?php echo $limiteExibicao === 40 ? 'selected' : ''; ?>>40</option>
                            <option value="60" <?php echo $limiteExibicao === 60 ? 'selected' : ''; ?>>60</option>
                            <option value="todas" <?php echo $limiteExibicao === 'todas' ? 'selected' : ''; ?>>Todas</option>
                        </select>
                    </div>
                    <button type="button" class="add-word-trigger" id="openAddWordModal">
                        <i class="fas fa-plus-circle"></i> Adicionar palavra
                    </button>
                </div>

            

                <div class="dictionary-grid" id="dictionaryGrid">
                    <?php echo renderizarPalavras($palavrasExibidas, $categoriaFiltro, $busca, $limiteExibicao); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="add-word-modal" id="addWordModal" aria-hidden="true">
        <div class="add-word-modal-backdrop" data-close-modal="true"></div>
        <div class="add-word-modal-panel">
            <button type="button" class="modal-close" aria-label="Fechar" data-close-modal="true">
                <i class="fas fa-times"></i>
            </button>

            <div class="content-card add-word-card">
                <div class="add-word-card-header">
                    <i class="fas fa-plus-circle" style="color: #fff;"></i>
                    <h2>Nova palavra</h2>
                </div>
                <form method="POST" action="dicionario.php">
                    <div class="form-group">
                        <label>Categoria:</label>
                        <select name="categoria" required>
                            <option value="palavra">📖 Palavra</option>
                            <option value="giria">💬 Gíria</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Termo:</label>
                        <input type="text" name="termo" required placeholder="Ex: Awesome">
                    </div>

                    <div class="form-group">
                        <label>Significado:</label>
                        <textarea name="significado" required placeholder="Ex: Incrível, fantástico"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Exemplo opcional:</label>
                        <textarea name="exemplo" placeholder="Ex: That's awesome!"></textarea>
                    </div>

                    <button type="submit" name="adicionar" class="btn-submit">
                        <i class="fas fa-save"></i> Adicionar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 English Explorer - Speak like a native</p>
        </div>
    </footer>

    <script>
        const searchForm = document.getElementById('searchForm');
        const buscaInput = document.getElementById('buscaInput');
        const categoriaFiltro = document.getElementById('categoriaFiltro');
        const limiteFiltro = document.getElementById('limiteFiltro');
        const limiteFiltroHidden = document.getElementById('limiteFiltroHidden');
        const dictionaryGrid = document.getElementById('dictionaryGrid');
        const dictionaryStats = document.getElementById('dictionaryStats');
        const filterButtons = document.querySelectorAll('.filter-btn[data-categoria]');
        const addWordModal = document.getElementById('addWordModal');
        const openAddWordModal = document.getElementById('openAddWordModal');
        const closeModalButtons = document.querySelectorAll('[data-close-modal]');
        let searchTimer;

        function abrirModalAdicionar() {
            if (!addWordModal) return;
            addWordModal.classList.add('open');
            addWordModal.setAttribute('aria-hidden', 'false');
        }

        function fecharModalAdicionar() {
            if (!addWordModal) return;
            addWordModal.classList.remove('open');
            addWordModal.setAttribute('aria-hidden', 'true');
        }

        if (openAddWordModal) {
            openAddWordModal.addEventListener('click', abrirModalAdicionar);
        }

        closeModalButtons.forEach((button) => {
            button.addEventListener('click', fecharModalAdicionar);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                fecharModalAdicionar();
            }
        });

        function montarParametros(incluirAjax = true) {
            const params = new URLSearchParams();
            params.set('categoria', categoriaFiltro.value);
            params.set('limite', limiteFiltro.value);

            if (buscaInput.value.trim() !== '') {
                params.set('busca', buscaInput.value.trim());
            }

            if (incluirAjax) {
                params.set('ajax', '1');
            }

            return params;
        }

        function atualizarLinks() {
            filterButtons.forEach((button) => {
                const params = montarParametros(false);
                params.set('categoria', button.dataset.categoria);
                button.href = `?${params.toString()}`;
                button.classList.toggle('active', button.dataset.categoria === categoriaFiltro.value);
            });
        }

        async function atualizarDicionario() {
            limiteFiltroHidden.value = limiteFiltro.value;
            atualizarLinks();

            try {
                const response = await fetch(`dicionario.php?${montarParametros(true).toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erro ao buscar palavras');
                }

                const data = await response.json();
                dictionaryGrid.innerHTML = data.html;
                dictionaryStats.innerHTML = `<i class="fas fa-chart-bar"></i> Exibindo ${data.exibindo} de ${data.total} palavras/g&iacute;rias encontradas`;
                window.history.replaceState({}, '', `?${montarParametros(false).toString()}`);
            } catch (error) {
                searchForm.submit();
            }
        }

        filterButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                categoriaFiltro.value = button.dataset.categoria;
                atualizarDicionario();
            });
        });

        buscaInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                atualizarDicionario();
            }, 2000);
        });

        limiteFiltro.addEventListener('change', atualizarDicionario);

        searchForm.addEventListener('submit', (event) => {
            event.preventDefault();
            atualizarDicionario();
        });
    </script>

</body>
</html>
