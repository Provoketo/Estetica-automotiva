<?php
include 'conexao.php';

$id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM agendamentos WHERE id = ?');
$stmt->execute([$id]);
$agendamento = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Comprovante de Serviço</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Comprovante de Serviço</h1>
    <p>Nome do Cliente: <?= $agendamento['nome_cliente'] ?></p>
    <p>Serviço: <?= $agendamento['servico'] ?></p>
    <p>Data: <?= $agendamento['data'] ?></p>
    <p>Hora: <?= $agendamento['hora'] ?></p>
    <p>Forma de Pagamento: <?= $agendamento['forma_pagamento'] ?></p>
    <p>Valor: R$ <?= number_format($agendamento['valor'], 2, ',', '.') ?></p>
    <button onclick="window.print()">Imprimir Comprovante</button>
</body>
</html>