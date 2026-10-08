<?php
session_start();
// Se NÃO estiver logado, redireciona para o login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit;
}

include_once 'config.php';

$usuarioId = $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? null;
$apelido = $_SESSION['apelido'] ?? $_SESSION['usuario_apelido'] ?? 'Usuário';
$novoUsuario = !empty($_SESSION['novo_usuario']);

if ($usuarioId) {
    $stmt = $pdo->prepare("SELECT apelido FROM usuarios WHERE id = ?");
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        if (!empty($usuario['apelido'])) {
            $apelido = $usuario['apelido'];
            $_SESSION['apelido'] = $apelido;
        }

    }
}

$mensagemBoasVindas = $novoUsuario ? 'Seja Bem vindo' : 'Bem vindo(a) de volta';
unset($_SESSION['novo_usuario']);

// Buscar estatísticas
$total_palavras = $pdo->query("SELECT COUNT(*) FROM palavras")->fetchColumn();
$total_lugares = $pdo->query("SELECT COUNT(*) FROM lugares")->fetchColumn();
$total_curiosidades = $pdo->query("SELECT COUNT(*) FROM curiosidades")->fetchColumn();

// Buscar curiosidades para os cards
$curiosidades = $pdo->query("SELECT * FROM curiosidades ORDER BY RAND() LIMIT 3")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>English Explorer - Speak like a native</title>
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
        
        .logout-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        /* Hero Section COM IMAGEM DE FUNDO */
        .hero {
            margin-top: 80px;
            position: relative;
            min-height: 75vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-image: url('ChatGPT Image 27 de mai. de 2026, 09_19_25.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        /* Overlay escuro para destacar o texto */
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(74, 111, 165, 0.85) 0%, rgba(0, 0, 0, 0.7) 100%);
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 24px;
        }
        
        .hero-content h1 {
            font-size: 3rem;
            background: linear-gradient(135deg, #FFFFFF 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 20px;
        }
        
        .hero-content p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.2rem;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        /* Course Grid */
        .course-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin: 50px 0;
        }
        
        .course-card {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        
        .course-card:hover {
            transform: translateY(-10px);
        }
        
        .course-image {
            height: 280px;
            background-size: cover;
            background-position: center;
            transition: transform 0.5s ease;
        }
        
        .course-card:hover .course-image {
            transform: scale(1.05);
        }
        
        .course-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 30px 20px 20px;
            text-align: center;
        }
        
        .course-overlay h3 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .age {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        /* Section Title */
        .section-title {
            text-align: center;
            margin: 60px 0 40px;
        }
        
        .section-title h2 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .section-title p {
            color: var(--text-soft);
        }
        
        /* Resource Cards */
        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }
        
        .resource-card {
            text-align: center;
            padding: 40px 30px;
            background: var(--warm-card);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .resource-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }
        
        .resource-card i {
            font-size: 3.5rem;
            color: var(--primary-light);
            margin-bottom: 20px;
        }
        
        .resource-card h3 {
            color: var(--primary);
            font-size: 1.3rem;
        }
        
        /* Quiz card special style */
        .resource-card.quiz-card {
            background: linear-gradient(135deg, var(--warm-card) 0%, #FFF5E6 100%);
            border: 2px solid var(--accent);
        }
        
        .resource-card.quiz-card i {
            color: var(--accent-dark);
            animation: pulse 2s infinite;
        }

        .resource-card.translate-card {
            background: linear-gradient(135deg, var(--warm-card) 0%, #EAF8F6 100%);
            border: 2px solid var(--primary-light);
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        /* About Section */
        .about-section {
            background: var(--soft-gray);
            padding: 80px 0;
            margin: 60px 0;
        }
        
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }
        
        .about-image {
            height: 400px;
            background-image: url('Banner de English Explorer Viagem e Mundo.png');
            background-size: cover;
            background-position: center;
            border-radius: var(--border-radius);
        }
        
        .about-content h3 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .about-content p {
            color: var(--text-soft);
            line-height: 1.8;
            margin-bottom: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
        }
        
        .stat-item {
            background: var(--warm-card);
            padding: 20px;
            border-radius: var(--border-radius-sm);
            box-shadow: var(--shadow-sm);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent);
        }
        
        .stat-label {
            color: var(--text-soft);
            font-size: 0.9rem;
        }
        
        /* Testimonials */
        .testimonials {
            padding: 60px 0;
            background: var(--warm-bg);
        }
        
        .testimonials h2 {
            text-align: center;
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 40px;
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .testimonial-card {
            background: var(--warm-card);
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .testimonial-text {
            font-style: italic;
            color: var(--text-soft);
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .author-info h4 {
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .author-info p {
            color: var(--text-soft);
            font-size: 0.85rem;
        }
        
        /* Contact Section */
        .contact-section {
            padding: 80px 0;
            background: var(--soft-gray);
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }
        
        .contact-info h3 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .contact-info p {
            color: var(--text-soft);
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .contact-details {
            list-style: none;
        }
        
        .contact-details li {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            color: var(--text-soft);
        }
        
        .contact-details li i {
            width: 40px;
            height: 40px;
            background: var(--warm-card);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: var(--accent);
            font-size: 1.2rem;
        }
        
        .contact-form .form-group {
            margin-bottom: 20px;
        }
        
        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.3s ease;
        }
        
        .contact-form input:focus,
        .contact-form textarea:focus {
            outline: none;
            border-color: var(--accent);
        }
        
        .contact-form textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
        }
        
        /* Footer */
        .footer {
            background: var(--text-dark);
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .footer-col h4 {
            margin-bottom: 20px;
            color: var(--accent);
        }
        
        .footer-col ul {
            list-style: none;
        }
        
        .footer-col ul li {
            margin-bottom: 10px;
        }
        
        .footer-col ul li a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-col ul li a:hover {
            color: var(--accent);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .course-grid,
            .resources-grid,
            .about-grid,
            .contact-grid,
            .footer-grid {
                grid-template-columns: 1fr;
            }
            
            .about-image {
                height: 250px;
            }
            
            .testimonials-grid {
                grid-template-columns: 1fr;
            }
            
            .navbar .container {
                flex-direction: column;
                gap: 15px;
            }
            
            .hero {
                margin-top: 120px;
                background-attachment: scroll;
            }
            
            .resources-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 480px) {
            .resources-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <style>
        /* ===== Ajuste visual colorido para adolescentes e adultos ===== */
        :root {
            --primary: #172033;
            --primary-light: #2f80ed;
            --accent: #0f9f8f;
            --accent-dark: #0b766d;
            --coral: #f9735b;
            --gold: #f7b32b;
            --violet: #7c5cff;
            --green: #2fb344;
            --warm-bg: #f8fafc;
            --warm-card: #ffffff;
            --soft-gray: #eef3f8;
            --text-dark: #172033;
            --text-soft: #5c6675;
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.08);
            --shadow-md: 0 8px 20px rgba(15, 23, 42, 0.08);
            --shadow-lg: 0 18px 38px rgba(15, 23, 42, 0.14);
            --border-radius: 8px;
            --border-radius-sm: 8px;
        }

        body {
            background:
                linear-gradient(135deg, rgba(47, 128, 237, 0.08) 0%, transparent 32%),
                linear-gradient(225deg, rgba(249, 115, 91, 0.08) 0%, transparent 30%),
                #f8fafc;
            color: var(--text-dark);
        }

        h1, h2, h3, .logo {
            font-family: 'Inter', sans-serif;
            letter-spacing: 0;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.94);
            border-bottom: 1px solid rgba(23, 32, 51, 0.08);
            box-shadow: 0 1px 12px rgba(15, 23, 42, 0.08);
            padding: 14px 0;
        }

        .logo {
            color: var(--primary);
            background: none;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .logo::after {
            content: '';
            display: block;
            width: 42px;
            height: 3px;
            margin-top: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--primary-light), var(--accent), var(--gold), var(--coral));
        }

        .welcome-badge,
        .logout-btn,
        .btn-submit {
            border-radius: 8px;
        }

        .welcome-badge {
            background: linear-gradient(135deg, rgba(47, 128, 237, 0.12), rgba(15, 159, 143, 0.14));
            color: var(--primary);
            border: 1px solid rgba(47, 128, 237, 0.22);
            padding: 8px 14px;
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--primary), #243b63);
            box-shadow: none;
            padding: 8px 14px;
        }

        .logout-btn:hover,
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .hero {
            min-height: 68vh;
            text-align: left;
            justify-content: flex-start;
            background-image: url('imagem_biblioteca.png');
            background-position: center;
            background-attachment: scroll;
        }

        .hero::before {
            background:
                linear-gradient(90deg, rgba(15, 23, 42, 0.88) 0%, rgba(23, 32, 51, 0.62) 50%, rgba(15, 159, 143, 0.18) 100%),
                linear-gradient(135deg, rgba(47, 128, 237, 0.28), rgba(249, 115, 91, 0.18) 42%, rgba(247, 179, 43, 0.2));
        }

        .hero-content {
            max-width: 760px;
            margin-left: max(24px, calc((100vw - 1200px) / 2 + 24px));
            padding: 0 24px;
        }

        .hero-content h1 {
            background: none;
            color: #ffffff;
            font-size: clamp(2.2rem, 5vw, 4.25rem);
            font-weight: 800;
            line-height: 1.04;
            margin-bottom: 18px;
        }

        .hero-content h1::after {
            content: '';
            display: block;
            width: min(220px, 45vw);
            height: 5px;
            margin-top: 20px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--gold), var(--coral), var(--accent), var(--primary-light));
        }

        .hero-content p {
            max-width: 620px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.15rem;
        }

        .about-section,
        .contact-section {
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(238, 243, 248, 0.92));
            border-top: 1px solid #e2e6ec;
            border-bottom: 1px solid #e2e6ec;
        }

        .about-image {
            border-radius: 8px;
            box-shadow: var(--shadow-md);
        }

        .about-content h3,
        .contact-info h3,
        .section-title h2,
        .testimonials h2 {
            color: var(--primary);
            font-weight: 800;
        }

        .section-title {
            padding: 0 24px;
        }

        .section-title h2::after,
        .testimonials h2::after {
            content: '';
            display: block;
            width: 88px;
            height: 4px;
            margin: 12px auto 0;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--primary-light), var(--accent), var(--gold), var(--coral));
        }

        .resources-grid {
            max-width: 1200px;
            margin: 46px auto;
            padding: 0 24px;
            gap: 18px;
        }

        .resource-card {
            --card-accent: var(--accent);
            border-radius: 8px;
            border: 1px solid #dfe5ed;
            box-shadow: var(--shadow-sm);
            padding: 30px 22px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.96)),
                linear-gradient(135deg, var(--card-accent), transparent);
        }

        .resource-card:nth-child(1) {
            --card-accent: var(--primary-light);
        }

        .resource-card:nth-child(2) {
            --card-accent: var(--green);
        }

        .resource-card:nth-child(3) {
            --card-accent: var(--violet);
        }

        .resource-card:nth-child(4) {
            --card-accent: var(--coral);
        }

        .resource-card:nth-child(5) {
            --card-accent: var(--teal);
        }

        .resource-card::before {
            background: linear-gradient(90deg, var(--card-accent), var(--gold));
            transform: scaleX(1);
            height: 5px;
        }

        .resource-card:hover {
            transform: translateY(-4px);
            border-color: var(--card-accent);
            box-shadow: var(--shadow-lg);
        }

        .resource-card i {
            color: var(--card-accent);
            font-size: 2.3rem;
            margin-bottom: 16px;
            animation: none;
        }

        .resource-card h3 {
            color: var(--primary);
            font-size: 1.12rem;
            font-weight: 700;
        }

        .resource-card.quiz-card {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.96)),
                linear-gradient(135deg, var(--coral), rgba(247, 179, 43, 0.5));
            border: 1px solid #dfe5ed;
        }

        .resource-card.translate-card {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.96)),
                linear-gradient(135deg, var(--teal), rgba(47, 128, 237, 0.45));
            border: 1px solid #dfe5ed;
        }

        .testimonials {
            background:
                linear-gradient(135deg, rgba(47, 128, 237, 0.1), rgba(15, 159, 143, 0.08) 45%, rgba(249, 115, 91, 0.1)),
                #f8fafc;
        }

        .testimonials-grid {
            gap: 20px;
        }

        .testimonial-card {
            --quote-accent: var(--accent);
            border-radius: 8px;
            border: 1px solid #dfe5ed;
            box-shadow: var(--shadow-sm);
            border-top: 5px solid var(--quote-accent);
        }

        .testimonial-card:nth-child(1) {
            --quote-accent: var(--primary-light);
        }

        .testimonial-card:nth-child(2) {
            --quote-accent: var(--coral);
        }

        .testimonial-card:nth-child(3) {
            --quote-accent: var(--green);
        }

        .testimonial-card:hover {
            transform: translateY(-3px);
        }

        .testimonial-text {
            font-style: normal;
            color: var(--text-dark);
        }

        .author-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-light), var(--accent), var(--gold));
            color: #ffffff;
            font-size: 0.78rem;
            font-weight: 800;
            object-fit: initial;
            flex: 0 0 46px;
        }

        .author-info h4 {
            color: var(--primary);
        }

        .contact-details li i {
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            color: #ffffff;
        }

        .contact-form input,
        .contact-form textarea {
            border-radius: 8px;
            border-color: #dfe5ed;
            background: #fbfcfe;
        }

        .contact-form input:focus,
        .contact-form textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 159, 143, 0.14);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent), var(--primary-light));
            padding: 12px 22px;
        }

        .footer {
            background:
                linear-gradient(135deg, #111827 0%, #172033 55%, #163447 100%);
        }

        .footer-col h4,
        .footer-col ul li a:hover {
            color: #7dd3c7;
        }

        .mascot-container {
            display: none;
        }

        @media (max-width: 768px) {
            .hero {
                margin-top: 126px;
                min-height: 58vh;
            }

            .hero::before {
                background: linear-gradient(90deg, rgba(15, 23, 42, 0.88), rgba(15, 23, 42, 0.58));
            }

            .hero-content {
                margin-left: 0;
            }
        }
    </style>
    <link rel="stylesheet" href="tema-explorer.css">
    <link rel="stylesheet" href="menu-translatemaster.css">
    <style>
        .navbar .container {
            max-width: 1400px;
        }

        .user-greeting {
            align-items: center;
            gap: 12px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .nav-links a,
        .games-menu-button {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 13px;
            border: 0;
            background: transparent;
            color: var(--primary);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 800;
            cursor: pointer;
        }

        .games-menu {
            position: relative;
        }

        .games-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 220px;
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

        .translate-master-dock {
            position: fixed;
            left: 0;
            top: 118px;
            z-index: 950;
            pointer-events: none;
        }

        .tm-toggle-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .tm-dock-shell {
            display: flex;
            align-items: flex-start;
            transform: translateX(calc(-100% + 50px));
            transition: transform 0.24s ease;
            filter: drop-shadow(0 18px 36px rgba(21, 25, 54, 0.16));
            pointer-events: auto;
        }

        .tm-toggle-input:checked ~ .tm-dock-shell {
            transform: translateX(0);
        }

        .tm-toggle-tab {
            width: 50px;
            min-height: 190px;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 0;
            border-left: 0;
            border-radius: 0 24px 24px 0;
            background: linear-gradient(135deg, var(--primary-light), var(--accent), var(--coral));
            color: #ffffff;
            cursor: pointer;
            font-weight: 900;
            box-shadow: 0 18px 36px rgba(41, 87, 255, 0.24);
        }

        .tm-toggle-tab span {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            font-size: 0.76rem;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .tm-invite-section {
            width: 370px;
            max-width: calc(100vw - 66px);
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.72);
            border-left: 0;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            padding: 22px;
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(18px) saturate(1.1);
            position: relative;
            overflow: hidden;
        }

        .tm-invite-section::before {
            content: '';
            position: absolute;
            inset: 0 0 auto;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-light), var(--accent), var(--gold), var(--coral), var(--pink));
        }

        .tm-invite-inner {
            display: grid;
            gap: 16px;
            align-items: start;
            position: relative;
        }

        .tm-invite-copy span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-light);
            font-weight: 800;
            font-size: 0.92rem;
        }

        .tm-invite-copy h2 {
            margin: 8px 0 6px;
            color: var(--text-dark);
            font-size: 1.72rem;
            line-height: 1.18;
        }

        .tm-invite-copy p {
            color: var(--text-soft);
            margin: 0;
            line-height: 1.65;
        }

        .tm-feature-grid {
            display: grid;
            gap: 10px;
        }

        .tm-feature-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(41, 87, 255, 0.09), rgba(0, 184, 169, 0.08));
            color: var(--text-dark);
            font-weight: 800;
        }

        .tm-feature-pill:nth-child(2) {
            background: linear-gradient(135deg, rgba(255, 193, 69, 0.18), rgba(255, 92, 92, 0.10));
        }

        .tm-feature-pill:nth-child(3) {
            background: linear-gradient(135deg, rgba(123, 77, 255, 0.10), rgba(255, 79, 163, 0.10));
        }

        .tm-feature-pill i {
            width: 20px;
            color: var(--primary-light);
            text-align: center;
        }

        .tm-invite-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .tm-primary-link,
        .tm-secondary-link {
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 900;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .tm-primary-link {
            background: linear-gradient(135deg, var(--primary-light), var(--accent), var(--coral));
            color: #ffffff;
            box-shadow: 0 14px 30px rgba(41, 87, 255, 0.20);
        }

        .tm-secondary-link {
            background: #ffffff;
            color: var(--primary-light);
            border: 2px solid rgba(41, 87, 255, 0.18);
        }

        .tm-primary-link:hover,
        .tm-secondary-link:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        @media (max-width: 768px) {
            .user-greeting {
                width: 100%;
                flex-direction: column;
            }

            .nav-links {
                width: 100%;
                justify-content: center;
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

            .translate-master-dock {
                top: 150px;
            }

            .tm-invite-section {
                width: min(320px, calc(100vw - 58px));
                padding: 14px;
            }

            .tm-toggle-tab {
                width: 44px;
                min-height: 166px;
            }

            .tm-invite-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="home-page">
    <!-- Barra de Navegação -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">English Explorer</a>
            <div class="user-greeting">
                <div class="welcome-badge">
                    <i class="fas fa-user"></i>
                    Olá, <?php echo htmlspecialchars($apelido); ?>
                </div>
                <div class="nav-links">
                    <a href="index.php" class="active">Início</a>
                    <a href="dicionario.php">Dicionário</a>
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

    <!-- Hero Section COM IMAGEM DE FUNDO -->
    <section class="hero">
        <div class="hero-content">
            <h1>Inglês para usar no mundo real</h1>
            <p>Pratique vocabulário, expressões e cultura com ferramentas rápidas para estudar no seu ritmo.</p>
        </div>
    </section>

    <div class="translate-master-dock" aria-label="Convite para jogar Translate Master">
        <input type="checkbox" id="translateMasterToggle" class="tm-toggle-input">
        <div class="tm-dock-shell">
            <section class="tm-invite-section">
                <div class="tm-invite-inner">
                    <div class="tm-invite-copy">
                        <span><i class="fas fa-language"></i> <?php echo $mensagemBoasVindas; ?>, <?php echo htmlspecialchars($apelido); ?></span>
                        <h2>Entre no Translate Master</h2>
                        <p>Teste suas traduções em inglês com temas rápidos, dicas inteligentes e correção instantânea.</p>
                    </div>
                    <div class="tm-feature-grid">
                        <div class="tm-feature-pill"><i class="fas fa-layer-group"></i> Fases por dificuldade</div>
                        <div class="tm-feature-pill"><i class="fas fa-wand-magic-sparkles"></i> Correção na hora</div>
                        <div class="tm-feature-pill"><i class="fas fa-lightbulb"></i> Dicas para melhorar</div>
                    </div>
                    <div class="tm-invite-actions">
                        <a href="translatemaster.php" class="tm-primary-link"><i class="fas fa-play"></i> Conhecer</a>
                    </div>
                </div>
            </section>
            <label for="translateMasterToggle" class="tm-toggle-tab" title="Abrir Translate Master">
                <i class="fas fa-language"></i>
                <span>Translate</span>
            </label>
        </div>
    </div>
    
    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-image"></div>
                <div class="about-content">
                    <h3>Aprenda com contexto, não só com tradução</h3>
                    <p>O English Explorer reúne vocabulário, curiosidades culturais, mapas e revisões para ajudar você a entender como o inglês aparece em conversas, estudos, viagens e conteúdos do dia a dia.</p>
                    
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Título da seção -->
    <div class="section-title">
        <h2>Explore nossos recursos</h2>
        <p>Ferramentas diretas para revisar, explorar e fixar inglês com mais autonomia.</p>
    </div>

    <!-- Cards de recursos -->
    <div class="resources-grid">
        <div class="resource-card" onclick="window.location.href='dicionario.php'">
            <i class="fas fa-book-open"></i>
            <h3>Dicionário</h3>
        </div>
        
        <div class="resource-card" onclick="window.location.href='mapa.php'">
            <i class="fas fa-map-marked-alt"></i>
            <h3>Mapa</h3>
        </div>
        
        <div class="resource-card" onclick="window.location.href='flashcards.php'">
            <i class="fas fa-layer-group"></i>
            <h3>Flashcards</h3>
        </div>
        
        <div class="resource-card quiz-card" onclick="window.location.href='quizz.php'">
            <i class="fas fa-question-circle"></i>
            <h3>Quiz</h3>
        </div>

        <div class="resource-card translate-card" onclick="window.location.href='translatemaster.php'">
            <i class="fas fa-language"></i>
            <h3>Translate Master</h3>
        </div>

    </div>
    
    <!-- Depoimentos -->
    <section class="testimonials">
        <div class="container">
            <h2>O que dizem nossos usuários</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-text">"Os flashcards deixaram minha revisão mais prática. Consigo estudar alguns minutos por dia e acompanhar melhor o vocabulário."</div>
                    <div class="testimonial-author">
                          <img src="https://i.kym-cdn.com/entries/icons/facebook/000/045/572/ygona_capa.jpg" alt="Avatar" class="author-avatar">
                        <div class="author-info">
                            <h4>Samira Sato</h4>
                            <p>Estudante</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-text">"Usei o dicionário para entender expressões que aparecem em vídeos e séries. As consultas ficaram bem mais rápidas."</div>
                    <div class="testimonial-author">
                    <img src="https://stickerly.pstatic.net/sticker_pack/WxJ1zyBznrtfS3eSeDWVcw/UE8SFV/15/97171814.png" alt="Avatar" class="author-avatar">
                        <div class="author-info">
                            <h4>Pedro Coxta</h4>
                            <p>Tik-toker</p>

                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-text">"O mapa ajudou minha turma a conectar idioma e cultura. Ficou mais fácil conversar sobre países, sotaques e costumes."</div>
                    <div class="testimonial-author">
                       <img src="https://i.pinimg.com/474x/bb/c3/e3/bbc3e3009e45bfb56761ae07227a5856.jpg?nii=t" alt="Avatar" class="author-avatar">
                        <div class="author-info">
                            <h4>Saori Kido</h4>
                            <p>Professora</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== QUEM SOMOS ===== -->
<section class="page-end">
    <div class="container">
        <div class="end-content">
            <div class="end-intro">
                <div class="end-icon">
                    <i class="fas fa-globe-americas"></i>
                </div>
                <span class="end-kicker">Sobre o English Explorer</span>
                <h2>Quem somos</h2>
                <p>
                    O <strong>English Explorer</strong> é um projeto escolar brasileiro criado para tornar o aprendizado de inglês mais prático, divertido e conectado ao mundo real.
                    Reunimos vocabulário, cultura, mapas, jogos e revisões em um só lugar para ajudar estudantes a aprender no próprio ritmo.
                </p>
                <p class="end-motto">
                    <i class="fas fa-quote-left"></i>
                    Speak like a native, explore like a traveler
                    <i class="fas fa-quote-right"></i>
                </p>
            </div>
            <div class="about-facts">
                <div class="about-fact">
                    <i class="fas fa-location-dot"></i>
                    <span>Origem</span>
                    <strong>Projeto estudantil brasileiro</strong>
                </div>
                <div class="about-fact">
                    <i class="fas fa-calendar-days"></i>
                    <span>Lançamento</span>
                    <strong>2026</strong>
                </div>
                <div class="about-fact">
                    <i class="fas fa-lightbulb"></i>
                    <span>Por que existimos</span>
                    <strong>Nosso propósito é ajudar pessoas que querem aprender ou praticar inglês de forma criativa, divertida e prática.</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* ===== QUEM SOMOS ===== */
    .page-end {
        position: relative;
        overflow: hidden;
        padding: 82px 0;
        background:
            linear-gradient(135deg, rgba(245, 158, 11, 0.13) 0%, rgba(255, 255, 255, 0.96) 38%, rgba(44, 82, 130, 0.12) 100%),
            var(--warm-card);
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        color: var(--text-dark);
    }

    .page-end::before {
        content: '';
        position: absolute;
        inset: 0;
        height: 5px;
        background: linear-gradient(90deg, var(--primary-light), var(--accent), var(--coral), var(--primary-light));
    }

    .page-end::after {
        content: '';
        position: absolute;
        right: 0;
        bottom: 0;
        width: 42%;
        height: 100%;
        background: linear-gradient(135deg, transparent, rgba(245, 158, 11, 0.08));
        pointer-events: none;
    }

    .end-content {
        position: relative;
        z-index: 1;
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(360px, 0.85fr);
        gap: 70px;
        align-items: center;
    }

    .end-intro {
        max-width: 690px;
    }

    .end-icon {
        width: 62px;
        height: 62px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.8rem;
        color: #ffffff;
        background: linear-gradient(135deg, var(--primary-light), var(--accent));
        box-shadow: var(--shadow-md);
        animation: none;
    }

    @keyframes floatIcon {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    .end-kicker {
        display: block;
        margin-bottom: 8px;
        color: var(--accent-dark);
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .end-content h2 {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        color: var(--primary);
        line-height: 1.3;
        margin-bottom: 18px;
    }

    .end-content p {
        color: var(--text-soft);
        font-size: 1.08rem;
        line-height: 1.7;
        margin: 0 0 22px;
    }

    .end-content p strong {
        color: var(--primary);
    }

    .about-facts {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        margin: 0;
    }

    .about-fact {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        min-height: 108px;
        padding: 20px 24px;
        align-items: flex-start;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid #dfe5ed;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: var(--shadow-sm);
    }

    .about-fact:nth-child(1) {
        border-top: 4px solid var(--primary-light);
    }

    .about-fact:nth-child(2) {
        border-top: 4px solid var(--gold);
    }

    .about-fact:nth-child(3) {
        border-top: 4px solid var(--accent);
    }

    .about-fact i {
        margin-bottom: 4px;
        color: var(--accent);
        font-size: 1.35rem;
    }

    .about-fact span {
        color: var(--text-soft);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .about-fact strong {
        color: var(--primary);
        font-size: 0.95rem;
    }

    .end-divider {
        width: 80px;
        height: 4px;
        margin: 24px 0;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--primary-light), var(--accent), var(--gold), var(--coral));
    }

    .end-motto {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary-light) !important;
        letter-spacing: 0;
    }

    .end-motto i {
        color: var(--gold);
        font-size: 0.9rem;
        opacity: 0.7;
    }

    @media (max-width: 600px) {
        .page-end {
            padding: 58px 0 42px;
        }

        .end-content {
            grid-template-columns: 1fr;
            gap: 32px;
        }

        .end-content p {
            font-size: 0.95rem;
        }

        .about-facts {
            grid-template-columns: 1fr;
        }

        .end-motto {
            font-size: 1rem;
        }
    }
</style>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>English Explorer</h4>
                    <ul>
                        <li><a href="#">Sobre nós</a></li>
                        <li><a href="#">Metodologia</a></li>
                        <li><a href="#">Depoimentos</a></li>
                        <li><a href="#">Carreiras</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Recursos</h4>
                    <ul>
                        <li><a href="dicionario.php">Dicionário</a></li>
                        <li><a href="mapa.php">Mapa Interativo</a></li>
                        <li><a href="flashcards.php">Flashcards</a></li>
                        <li><a href="quizz.php">Quiz</a></li>
                        <li><a href="translatemaster.php">TranslateMaster</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Termos de uso</a></li>
                        <li><a href="#">Política de privacidade</a></li>
                        <li><a href="#">Cookies</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 English Explorer. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
<!-- Mascote Flutuante -->
<div class="mascot-container">
    <div class="mascot">
        <div class="mascot-body">
            <img src="mascote.png" alt="Mascote English Explorer" class="mascot-image">
            <div class="mascot-speech">
                <p>Pronto para revisar?<br>Abra os flashcards.</p>
            </div>
        </div>
    </div>
</div>

</script>
</body>
</html>
