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

// Função para calcular o total arrecadado
function calcularTotalArrecadado($pdo) {
    $stmt = $pdo->query('SELECT SUM(valor) as total FROM agendamentos WHERE status = "concluido"');
    return $stmt->fetch()['total'];
}

// Função para obter serviços por período
function obterServicosPorPeriodo($pdo, $periodo, $data_inicio = null, $data_fim = null) {
    switch ($periodo) {
        case 'dia':
            $sql = "SELECT * FROM agendamentos WHERE DATE(data) = CURDATE() AND status = 'concluido'";
            break;
        case 'semana':
            $sql = "SELECT * FROM agendamentos WHERE YEARWEEK(data) = YEARWEEK(CURDATE()) AND status = 'concluido'";
            break;
        case 'mes':
            $sql = "SELECT * FROM agendamentos WHERE MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE()) AND status = 'concluido'";
            break;
        case 'ano':
            $sql = "SELECT * FROM agendamentos WHERE YEAR(data) = YEAR(CURDATE()) AND status = 'concluido'";
            break;
        case 'personalizado':
            if ($data_inicio && $data_fim) {
                $sql = "SELECT * FROM agendamentos WHERE data BETWEEN '$data_inicio' AND '$data_fim' AND status = 'concluido'";
            } else {
                $sql = "SELECT * FROM agendamentos WHERE status = 'concluido'";
            }
            break;
        default:
            $sql = "SELECT * FROM agendamentos WHERE status = 'concluido'";
    }
    return $pdo->query($sql)->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatórios</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/menu.php'; ?> <!-- Inclui o menu lateral -->
        <div class="conteudo-principal">
            <h1>Relatórios</h1>

            <!-- Total Arrecadado -->
            <h2>Total Arrecadado: R$ <?= number_format(calcularTotalArrecadado($pdo), 2, ',', '.') ?></h2>

            <!-- Relatórios por Período -->
            <h2>Serviços por Período</h2>
            <form method="GET">
                <label for="periodo">Selecione o período:</label>
                <select id="periodo" name="periodo">
                    <option value="dia">Dia</option>
                    <option value="semana">Semana</option>
                    <option value="mes">Mês</option>
                    <option value="ano">Ano</option>
                    <option value="personalizado">Personalizado</option>
                </select>
                <div id="periodo-personalizado" style="display: none;">
                    <label for="data_inicio">Data Início:</label>
                    <input type="text" id="data_inicio" name="data_inicio" placeholder="Selecione a data de início">

                    <label for="data_fim">Data Fim:</label>
                    <input type="text" id="data_fim" name="data_fim" placeholder="Selecione a data de fim">
                </div>
                <button type="submit">Filtrar</button>
            </form>

            <?php
            // Verifica se o parâmetro 'periodo' foi passado via GET
            $periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'dia';
            $data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : null;
            $data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : null;

            $servicos = obterServicosPorPeriodo($pdo, $periodo, $data_inicio, $data_fim);
            ?>

            <h3>Serviços no Período Selecionado</h3>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servicos as $servico): ?>
                        <tr>
                            <td><?= $servico['id'] ?></td>
                            <td><?= $servico['nome_cliente'] ?></td>
                            <td><?= $servico['servico'] ?></td>
                            <td><?= date('d/m/Y', strtotime($servico['data'])) ?></td>
                            <td><?= $servico['hora'] ?></td>
                            <td><?= $servico['forma_pagamento'] ?></td>
                            <td>R$ <?= number_format($servico['valor'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Inicializa o Flatpickr para os campos de data
        flatpickr("#data_inicio", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                // Define a data mínima para o campo de data_fim
                document.getElementById("data_fim")._flatpickr.set("minDate", dateStr);
            }
        });

        flatpickr("#data_fim", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
        });

        // Mostrar/ocultar campos de data personalizada
        document.getElementById('periodo').addEventListener('change', function() {
            var periodoPersonalizado = document.getElementById('periodo-personalizado');
            if (this.value === 'personalizado') {
                periodoPersonalizado.style.display = 'block';
            } else {
                periodoPersonalizado.style.display = 'none';
            }
        });
    </script>
</body>
</html>