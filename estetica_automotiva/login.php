<?php
// Habilitar a exibição de erros
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

// Iniciar a sessão apenas uma vez
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'php/conexao.php'; // Arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['usuario']; // Usar o campo 'usuario' como email
    $senha = $_POST['senha'];

    // Debug: Exibir email e senha recebidos
    /*echo "<h3>Debug:</h3>";
    echo "Email recebido: $email<br>";
    echo "Senha recebida: $senha<br>";*/

    // Prevenir SQL Injection usando Prepared Statements
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Debug: Exibir resultado da consulta ao banco de dados
    /*echo "<h3>Resultado da Consulta:</h3>";
    echo "<pre>";
    var_dump($user);
    echo "</pre>";*/

    if ($user) {
        // Debug: Verificar se a senha está correta
        /*echo "<h3>Verificação da Senha:</h3>";
        echo "Senha fornecida: '$senha'<br>";
        echo "Senha no banco: '{$user['senha']}'<br>";*/

        if ($senha === $user['senha']) { // Comparação direta de strings
            // Login bem-sucedido
            $_SESSION['usuario'] = $user['nome']; // Armazena o nome do usuário na sessão
            $_SESSION['perfil'] = $user['perfil']; // Armazena o perfil do usuário na sessão
            header('Location: pagina_inicial.php'); // Redireciona para a página inicial
            exit();
        } else {
            // Senha inválida
            $erro = "Usuário ou senha incorretos!";
        }
    } else {
        // Usuário não encontrado
        $erro = "Usuário ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (isset($erro)): ?>
            <p class="erro"><?= $erro ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="usuario">Usuário (Email):</label>
            <input type="text" id="usuario" name="usuario" required>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>