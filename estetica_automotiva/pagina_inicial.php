<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página Inicial</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'php/menu.php'; ?> <!-- Inclui o menu lateral -->
        <div class="conteudo-principal">
            <h1>Bem-vindo, <?= $_SESSION['usuario'] ?>!</h1>
            <p>Selecione uma opção no menu lateral para começar.</p>
        </div>
    </div>
</body>
</html>