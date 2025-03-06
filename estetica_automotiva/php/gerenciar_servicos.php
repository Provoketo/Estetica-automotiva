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

// Adicionar novo serviço
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adicionar'])) {
    $servico = $_POST['servico'];
    $valor = $_POST['valor'];

    $stmt = $pdo->prepare('INSERT INTO servicos (servico, valor) VALUES (?, ?)');
    if ($stmt->execute([$servico, $valor])) {
        echo "<p class='sucesso'>Serviço adicionado com sucesso!</p>";
    } else {
        echo "<p class='erro'>Erro ao adicionar o serviço.</p>";
    }
}

// Editar serviço existente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $servico = $_POST['servico'];
    $valor = $_POST['valor'];

    $stmt = $pdo->prepare('UPDATE servicos SET servico = ?, valor = ? WHERE id = ?');
    if ($stmt->execute([$servico, $valor, $id])) {
        echo "<p class='sucesso'>Serviço atualizado com sucesso!</p>";
    } else {
        echo "<p class='erro'>Erro ao atualizar o serviço.</p>";
    }
}

// Excluir serviço
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];

    $stmt = $pdo->prepare('DELETE FROM servicos WHERE id = ?');
    if ($stmt->execute([$id])) {
        echo "<p class='sucesso'>Serviço excluído com sucesso!</p>";
    } else {
        echo "<p class='erro'>Erro ao excluir o serviço.</p>";
    }
}

// Buscar todos os serviços cadastrados
$stmt = $pdo->query('SELECT * FROM servicos');
$servicos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Serviços</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/menu.php'; ?> <!-- Inclui o menu lateral -->
        <div class="conteudo-principal">
            <h1>Gerenciar Serviços</h1>

            <!-- Formulário para adicionar novo serviço -->
            <h2>Adicionar Novo Serviço</h2>
            <form method="POST">
                <label for="servico">Serviço:</label>
                <input type="text" id="servico" name="servico" required>

                <label for="valor">Valor:</label>
                <input type="number" id="valor" name="valor" step="0.01" required>

                <button type="submit" name="adicionar">Adicionar</button>
            </form>

            <!-- Lista de serviços cadastrados -->
            <h2>Serviços Cadastrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Serviço</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servicos as $servico): ?>
                        <tr>
                            <td><?= $servico['id'] ?></td>
                            <td><?= $servico['servico'] ?></td>
                            <td>R$ <?= number_format($servico['valor'], 2, ',', '.') ?></td>
                            <td>
                                <a href="editar_servico.php?id=<?= $servico['id'] ?>">Editar</a>
                                <a href="?excluir=<?= $servico['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este serviço?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>