<?php
session_start();

// Verifica se o usuário está logado e é um administrador
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
} elseif ($_SESSION['perfil'] != 'admin') {
    header('Location: acesso_negado.php');
    exit();
}

include __DIR__ . '/conexao.php'; // Arquivo de conexão com o banco de dados

// Processamento do formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar'])) {
    // Coleta e sanitiza os dados do formulário
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $placa = trim($_POST['placa']);
    $modelo = trim($_POST['modelo']);
    $cor = trim($_POST['cor']);

    // Validação básica dos campos
    if (empty($nome) || empty($email) || empty($telefone) || empty($placa) || empty($modelo) || empty($cor)) {
        echo "<p class='erro'>Todos os campos são obrigatórios.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p class='erro'>Email inválido.</p>";
    } else {
        // Verifica se o email ou a placa já estão cadastrados
        $stmt = $pdo->prepare('SELECT id FROM clientes_fidelidade WHERE email = ? OR placa = ?');
        $stmt->execute([$email, $placa]);
        if ($stmt->fetch()) {
            echo "<p class='erro'>Email ou placa já cadastrados.</p>";
        } else {
            // Insere os dados no banco de dados
            $stmt = $pdo->prepare('INSERT INTO clientes_fidelidade (nome, email, telefone, placa, modelo, cor) VALUES (?, ?, ?, ?, ?, ?)');
            if ($stmt->execute([$nome, $email, $telefone, $placa, $modelo, $cor])) {
                // Redireciona para a página de clientes fidelidade após o cadastro
                header('Location: cadastro_fidelidade.php');
                exit();
            } else {
                echo "<p class='erro'>Erro ao cadastrar cliente fidelidade.</p>";
            }
        }
    }
}

// Processamento da pesquisa
$termo_pesquisa = '';
$clientes = [];
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['pesquisa'])) {
    $termo_pesquisa = trim($_GET['pesquisa']);
    if (!empty($termo_pesquisa)) {
        // Pesquisa por nome, telefone, placa ou modelo
        $stmt = $pdo->prepare('SELECT * FROM clientes_fidelidade WHERE nome LIKE ? OR telefone LIKE ? OR placa LIKE ? OR modelo LIKE ?');
        $stmt->execute(["%$termo_pesquisa%", "%$termo_pesquisa%", "%$termo_pesquisa%", "%$termo_pesquisa%"]);
        $clientes = $stmt->fetchAll();
    } else {
        // Se o campo de pesquisa estiver vazio, lista todos os clientes
        $stmt = $pdo->query('SELECT * FROM clientes_fidelidade');
        $clientes = $stmt->fetchAll();
    }
} else {
    // Lista todos os clientes ao carregar a página
    $stmt = $pdo->query('SELECT * FROM clientes_fidelidade');
    $clientes = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Clientes Fidelidade</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos globais */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .menu-lateral {
            width: 250px;
            background-color: #333;
            color: #fff;
            padding: 20px;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .menu-lateral ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .menu-lateral ul li {
            margin-bottom: 15px;
        }
        .menu-lateral ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            display: block;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .menu-lateral ul li a:hover {
            background-color: #444;
        }
        .container {
            flex: 1;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
        }
        .erro {
            color: #d9534f;
            background-color: #f2dede;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        .sucesso {
            color: #5cb85c;
            background-color: #dff0d8;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        form {
            margin-bottom: 30px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #5cb85c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .pesquisa {
            margin-bottom: 20px;
        }
        .pesquisa input {
            width: calc(100% - 100px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .pesquisa button {
            width: 90px;
            margin-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/menu.php'; ?> <!-- Inclui o menu lateral -->
    <div class="container">
        <div class="conteudo-principal">
            <h1>Cadastro de Clientes Fidelidade</h1>

            <!-- Formulário de cadastro -->
            <form method="POST">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="telefone">Telefone:</label>
                <input type="tel" id="telefone" name="telefone" required>

                <label for="placa">Placa do Carro:</label>
                <input type="text" id="placa" name="placa" required>

                <label for="modelo">Modelo do Carro:</label>
                <input type="text" id="modelo" name="modelo" required>

                <label for="cor">Cor do Carro:</label>
                <input type="text" id="cor" name="cor" required>

                <button type="submit" name="cadastrar">Cadastrar</button>
            </form>

            <!-- Campo de pesquisa -->
            <div class="pesquisa">
                <form method="GET">
                    <input type="text" name="pesquisa" placeholder="Pesquisar por nome, telefone, placa ou modelo" value="<?= htmlspecialchars($termo_pesquisa) ?>">
                    <button type="submit">Buscar</button>
                </form>
            </div>

            <!-- Listagem de clientes -->
            <h2>Clientes Cadastrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Placa</th>
                        <th>Modelo</th>
                        <th>Cor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clientes)): ?>
                        <tr>
                            <td colspan="6">Nenhum cliente encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?= htmlspecialchars($cliente['nome']) ?></td>
                                <td><?= htmlspecialchars($cliente['email']) ?></td>
                                <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                                <td><?= htmlspecialchars($cliente['placa']) ?></td>
                                <td><?= htmlspecialchars($cliente['modelo']) ?></td>
                                <td><?= htmlspecialchars($cliente['cor']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>