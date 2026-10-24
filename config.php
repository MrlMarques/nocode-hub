<?php
// config.php

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'nocode_hub');
define('DB_USER', 'root'); // Mude depois no seu PC
define('DB_PASS', '');     // Mude depois no seu PC

// Chave da API do Google Gemini (Não coloque a real no GitHub!)
define('GEMINI_API_KEY', 'SUA_CHAVE_AQUI_DEPOIS');

// Inicializa a conexão PDO
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Iniciar sessão para o Admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função para verificar se o admin tá logado
function checkAdminLogin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}
?>
