<?php
// Habilitar a exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar a sessão apenas uma vez
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php'); // Redireciona para o login se não estiver logado
    exit();
}

include __DIR__ . '/conexao.php'; // Arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servico = $_POST['servico'];
    $valor = $_POST['valor'];
    $prazo_execucao = $_POST['prazo_execucao'];

    // Inserir o serviço no banco de dados
    $stmt = $pdo->prepare('INSERT INTO servicos (servico, valor, prazo_execucao) VALUES (?, ?, ?)');
    if ($stmt->execute([$servico, $valor, $prazo_execucao])) {
        echo "<p class='sucesso'>Serviço cadastrado com sucesso!</p>";
    } else {
        echo "<p class='erro'>Erro ao cadastrar o serviço.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Serviço</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/menu.php'; ?> <!-- Inclui o menu lateral -->
        <div class="conteudo-principal">
            <h1>Cadastrar Serviço</h1>
            <form method="POST">
                <label for="servico">Serviço:</label>
                <input type="text" id="servico" name="servico" required>
                <label for="valor">Valor:</label>
                <input type="number" id="valor" name="valor" step="0.01" required>
                <label for="prazo_execucao">Prazo de Execução:</label>
                <input type="date" id="prazo_execucao" name="prazo_execucao" required>
                <button type="submit">Cadastrar</button>
            </form>
        </div>
    </div>
</body>
</html>