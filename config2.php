<?php
// Configuração do Banco de Dados
$host = 'localhost';
$dbname = 'english_explorer';
$username = 'root';  // Altere para seu usuário MySQL
$password = '';      // Altere para sua senha MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>