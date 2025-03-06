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

// Buscar serviços cadastrados com seus valores
$stmt = $pdo->query('SELECT servico, valor FROM servicos');
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_cliente = $_POST['nome_cliente'];
    $servico = $_POST['servico'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $valor = $_POST['valor'];

    // Inserir o agendamento no banco de dados
    $stmt = $pdo->prepare('INSERT INTO agendamentos (nome_cliente, servico, data, hora, forma_pagamento, valor) VALUES (?, ?, ?, ?, ?, ?)');
    if ($stmt->execute([$nome_cliente, $servico, $data, $hora, $forma_pagamento, $valor])) {
        echo "<p class='sucesso'>Agendamento realizado com sucesso!</p>";
    } else {
        echo "<p class='erro'>Erro ao agendar o serviço.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Agendar Serviço</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="container">
        <?php include __DIR__ . '/menu.php'; ?> <!-- Inclui o menu lateral -->
        <div class="conteudo-principal">
            <h1>Agendar Serviço</h1>
            <form method="POST">
                <label for="nome_cliente">Nome do Cliente:</label>
                <input type="text" id="nome_cliente" name="nome_cliente" required>

                <label for="servico">Serviço:</label>
                <select id="servico" name="servico" required>
                    <option value="">Selecione um serviço</option>
                    <?php foreach ($servicos as $servico): ?>
                        <option value="<?= $servico['servico'] ?>" data-valor="<?= $servico['valor'] ?>"><?= $servico['servico'] ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="data">Data:</label>
                <input type="date" id="data" name="data" required>

                <label for="hora">Hora:</label>
                <input type="time" id="hora" name="hora" min="09:00" max="18:00" required>

                <label for="forma_pagamento">Forma de Pagamento:</label>
                <select id="forma_pagamento" name="forma_pagamento" required>
                    <option value="Credito">Crédito</option>
                    <option value="Debito">Débito</option>
                    <option value="Pix">Pix</option>
                    <option value="Dinheiro">Dinheiro</option>
                </select>

                <label for="valor">Valor:</label>
                <input type="number" id="valor" name="valor" step="0.01" required> <!-- Removido o atributo readonly -->

                <button type="submit">Agendar</button>
            </form>
        </div>
    </div>

    <script>
        // Adicionar um evento de mudança ao campo de seleção de serviço
        document.getElementById('servico').addEventListener('change', function () {
            var servicoSelecionado = this.options[this.selectedIndex]; // Opção selecionada
            var valorServico = servicoSelecionado.getAttribute('data-valor'); // Valor do serviço

            // Preencher o campo de valor
            document.getElementById('valor').value = valorServico || '';
        });
    </script>
</body>

</html>