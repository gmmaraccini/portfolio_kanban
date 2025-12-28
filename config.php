<?php
// config.php
$host = 'localhost';
$db   = 'kanban_db';
$user = 'root'; // Seu usuário
$pass = '';     // Sua senha

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Configura para lançar erros caso o SQL falhe
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>