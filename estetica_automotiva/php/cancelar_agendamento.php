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
    $id = $_POST['id'];
    $motivo = $_POST['motivo'];

    // Atualizar o status e o motivo do cancelamento
    $stmt = $pdo->prepare('UPDATE agendamentos SET status = "cancelado", motivo_cancelamento = ? WHERE id = ?');
    if ($stmt->execute([$motivo, $id])) {
        // Exibir mensagem de sucesso
        echo "<p class='sucesso'>Agendamento cancelado com sucesso!</p>";

        // Redirecionar para a página de gerenciar agendamentos após 2 segundos
        header("Refresh: 2; URL=gerenciar_agendamentos.php");
        exit();
    } else {
        echo "<p class='erro'>Erro ao cancelar o agendamento.</p>";
    }
}
?>