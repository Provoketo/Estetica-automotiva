<?php
// Habilitar a exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar a sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

include __DIR__ . '/conexao.php'; // Arquivo de conexão com o banco de dados

// Receber o nome do serviço via GET
if (isset($_GET['servico'])) {
    $servico = $_GET['servico'];
    echo "Serviço recebido: " . $servico . "<br>"; // Depuração

    // Buscar o valor do serviço no banco de dados
    $stmt = $pdo->prepare('SELECT valor FROM servicos WHERE servico = ?');
    $stmt->execute([$servico]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        echo "Valor encontrado: " . $resultado['valor'] . "<br>"; // Depuração
        echo json_encode(['valor' => $resultado['valor']]);
    } else {
        echo "Nenhum valor encontrado para o serviço.<br>"; // Depuração
        echo json_encode(['valor' => '']);
    }
} else {
    echo "Nenhum serviço recebido.<br>"; // Depuração
    echo json_encode(['valor' => '']);
}
?>