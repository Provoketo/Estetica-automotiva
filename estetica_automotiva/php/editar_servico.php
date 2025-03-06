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

// Buscar o serviço pelo ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM servicos WHERE id = ?');
    $stmt->execute([$id]);
    $servico = $stmt->fetch();

    if (!$servico) {
        echo "<p class='erro'>Serviço não encontrado.</p>";
        exit();
    }
} else {
    echo "<p class='erro'>ID do serviço não fornecido.</p>";
    exit();
}

// Atualizar o serviço
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servico_nome = $_POST['servico'];
    $valor = $_POST['valor'];

    $stmt = $pdo->prepare('UPDATE servicos SET servico = ?, valor = ? WHERE id = ?');
    if ($stmt->execute([$servico_nome, $valor, $id])) {
        // Redirecionar para a página de gerenciar serviços após a atualização
        header('Location: gerenciar_servicos.php');
        exit(); // Certifique-se de sair para evitar execução adicional do script
    } else {
        echo "<p class='erro'>Erro ao atualizar o serviço.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Serviço</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/menu.php'; ?> <!-- Inclui o menu lateral -->
        <div class="conteudo-principal">
            <h1>Editar Serviço</h1>

            <form method="POST">
                <label for="servico">Serviço:</label>
                <input type="text" id="servico" name="servico" value="<?= $servico['servico'] ?>" required>

                <label for="valor">Valor:</label>
                <input type="number" id="valor" name="valor" step="0.01" value="<?= $servico['valor'] ?>" required>

                <button type="submit">Salvar</button>
            </form>
        </div>
    </div>
</body>
</html>