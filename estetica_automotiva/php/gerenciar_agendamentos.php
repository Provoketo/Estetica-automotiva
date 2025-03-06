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

// Função para marcar um agendamento como concluído
if (isset($_GET['concluir'])) {
    $id = $_GET['concluir'];
    $stmt = $pdo->prepare('UPDATE agendamentos SET status = "concluido" WHERE id = ?');
    if ($stmt->execute([$id])) {
        echo "<p class='sucesso'>Agendamento marcado como concluído!</p>";
    } else {
        echo "<p class='erro'>Erro ao atualizar o status do agendamento.</p>";
    }
}

// Verificar se um filtro de status foi aplicado
$filtro_status = isset($_GET['status']) ? $_GET['status'] : 'todos';

// Consulta SQL baseada no filtro de status
if ($filtro_status == 'todos') {
    $stmt = $pdo->query('SELECT * FROM agendamentos');
} else {
    $stmt = $pdo->prepare('SELECT * FROM agendamentos WHERE status = ?');
    $stmt->execute([$filtro_status]);
}
$agendamentos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Agendamentos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/menu.php'; ?> <!-- Inclui o menu lateral -->
        <div class="conteudo-principal">
            <h1>Gerenciar Agendamentos</h1>

            <!-- Filtro de Status -->
            <form method="GET" action="">
                <label for="status">Filtrar por Status:</label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="todos" <?= $filtro_status == 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="pendente" <?= $filtro_status == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="concluido" <?= $filtro_status == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                    <option value="cancelado" <?= $filtro_status == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Cliente</th>
                        <th>Serviço</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Forma de Pagamento</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td><?= $agendamento['id'] ?></td>
                            <td><?= $agendamento['nome_cliente'] ?></td>
                            <td><?= $agendamento['servico'] ?></td>
                            <td><?= date('d/m/Y', strtotime($agendamento['data'])) ?></td>
                            <td><?= $agendamento['hora'] ?></td>
                            <td><?= $agendamento['forma_pagamento'] ?></td>
                            <td>R$ <?= number_format($agendamento['valor'], 2, ',', '.') ?></td>
                            <td><?= ucfirst($agendamento['status']) ?></td>
                            <td>
                                <a href="imprimir_comprovante.php?id=<?= $agendamento['id'] ?>">Imprimir Comprovante</a>
                                <?php if ($agendamento['status'] == 'pendente'): ?>
                                    <a href="?concluir=<?= $agendamento['id'] ?>" onclick="return confirm('Deseja marcar este agendamento como concluído?')">Concluir</a>
                                <?php endif; ?>
                                <?php if ($agendamento['status'] == 'pendente'): ?>
                                    <form method="POST" action="cancelar_agendamento.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $agendamento['id'] ?>">
                                        <select name="motivo" required>
                                            <option value="Cliente cancelou">Cliente cancelou</option>
                                            <option value="Cancelamento feito pela loja">Cancelamento feito pela loja</option>
                                            <option value="Reagendado">Reagendado</option>
                                        </select>
                                        <button type="submit">Cancelar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>