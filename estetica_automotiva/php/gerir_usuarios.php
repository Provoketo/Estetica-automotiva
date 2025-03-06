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

// Adicionar novo usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adicionar_usuario'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha']; // Senha sem criptografia
    $perfil = $_POST['perfil'];

    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)');
    if ($stmt->execute([$nome, $email, $senha, $perfil])) {
        echo "<p class='sucesso'>Usuário adicionado com sucesso!</p>";
    } else {
        echo "<p class='erro'>Erro ao adicionar usuário.</p>";
    }
}

// Editar usuário existente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_usuario'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $perfil = $_POST['perfil'];
    $senha = !empty($_POST['senha']) ? $_POST['senha'] : null; // Senha sem criptografia, se fornecida

    // Atualiza nome, email, perfil e senha (se fornecida)
    if ($senha) {
        $stmt = $pdo->prepare('UPDATE usuarios SET nome = ?, email = ?, perfil = ?, senha = ? WHERE id = ?');
        $stmt->execute([$nome, $email, $perfil, $senha, $id]);
    } else {
        $stmt = $pdo->prepare('UPDATE usuarios SET nome = ?, email = ?, perfil = ? WHERE id = ?');
        $stmt->execute([$nome, $email, $perfil, $id]);
    }

    if ($stmt->rowCount() > 0) {
        echo "<p class='sucesso'>Usuário atualizado com sucesso!</p>";
    } else {
        echo "<p class='erro'>Erro ao atualizar usuário.</p>";
    }
}

// Buscar todos os usuários
$stmt = $pdo->query('SELECT * FROM usuarios');
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerir Usuários</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Arquivo CSS padrão -->
    <style>
        /* Estilos gerais */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
        }

        /* Estilos do menu lateral */
        .menu-lateral {
            width: 220px;
            background-color: #333;
            color: white;
            height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .menu-lateral h2 {
            margin-top: 0;
            font-size: 1.5em;
            border-bottom: 1px solid #444;
            padding-bottom: 10px;
        }

        .menu-lateral ul {
            list-style: none;
            padding: 0;
        }

        .menu-lateral ul li {
            margin: 15px 0;
        }

        .menu-lateral ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.1em;
            transition: color 0.3s;
        }

        .menu-lateral ul li a:hover {
            color: #007bff;
        }

        /* Estilos do conteúdo principal */
        .conteudo-principal {
            flex: 1;
            padding: 20px;
            background-color: white;
            margin-left: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Estilos específicos para esta página */
        .editable {
            display: none;
        }

        .editing .editable {
            display: inline;
        }

        .editing .view {
            display: none;
        }

        .erro {
            color: #ff0000;
            font-weight: bold;
        }

        .sucesso {
            color: #008000;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .modal-conteudo {
            max-width: 400px;
            margin: 0 auto;
        }

        .fechar {
            float: right;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
        }

        .fechar:hover {
            color: #ff0000;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Menu Lateral -->
        <div class="menu-lateral">
            <h2>Menu</h2>
            <ul>
                <li><a href="/estetica_automotiva/pagina_inicial.php">Inicio</a></li>
                <li><a href="cadastro_fidelidade.php">Cadastrar Cliente Fidelidade</a></li>
                <li><a href="/estetica_automotiva/php/gerenciar_servicos.php">Gerenciar Serviços</a></li>
                <li><a href="/estetica_automotiva/php/agendar_servico.php">Agendar Serviço</a></li>
                <li><a href="/estetica_automotiva/php/gerenciar_agendamentos.php">Gerenciar Agendamentos</a></li>
                <li><a href="/estetica_automotiva/php/gerir_usuarios.php">Gerir Usuarios</a></li>
                <li><a href="/estetica_automotiva/php/relatorios.php">Relatorios</a></li>
                <li><a href="/estetica_automotiva/logout.php">Sair</a></li>

            </ul>
        </div>

        <!-- Conteúdo Principal -->
        <div class="conteudo-principal">
            <h1>Gerir Usuários</h1>

            <!-- Formulário para adicionar novo usuário -->
            <h2>Adicionar Novo Usuário</h2>
            <form method="POST">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>

                <label for="perfil">Perfil:</label>
                <select id="perfil" name="perfil" required>
                    <option value="admin">Admin</option>
                    <option value="usuario">Usuário</option>
                </select>

                <button type="submit" name="adicionar_usuario">Adicionar Usuário</button>
            </form>

            <!-- Tabela de usuários existentes -->
            <h2>Usuários Cadastrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr id="usuario-<?= $usuario['id'] ?>">
                            <td><?= $usuario['id'] ?></td>
                            <td>
                                <span class="view"><?= $usuario['nome'] ?></span>
                                <input type="text" class="editable" name="nome" value="<?= $usuario['nome'] ?>">
                            </td>
                            <td>
                                <span class="view"><?= $usuario['email'] ?></span>
                                <input type="email" class="editable" name="email" value="<?= $usuario['email'] ?>">
                            </td>
                            <td>
                                <span class="view"><?= ucfirst($usuario['perfil']) ?></span>
                                <select class="editable" name="perfil">
                                    <option value="admin" <?= $usuario['perfil'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="usuario" <?= $usuario['perfil'] == 'usuario' ? 'selected' : '' ?>>Usuário
                                    </option>
                                </select>
                            </td>
                            <td>
                                <button
                                    onclick="abrirModalEdicao(<?= $usuario['id'] ?>, '<?= $usuario['nome'] ?>', '<?= $usuario['email'] ?>', '<?= $usuario['perfil'] ?>')">Editar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div id="modalEdicao" class="modal">
        <div class="modal-conteudo">
            <span class="fechar" onclick="fecharModalEdicao()">&times;</span>
            <h2>Editar Usuário</h2>
            <form method="POST">
                <input type="hidden" id="edit_id" name="id">
                <label for="edit_nome">Nome:</label>
                <input type="text" id="edit_nome" name="nome" required>

                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="email" required>

                <label for="edit_perfil">Perfil:</label>
                <select id="edit_perfil" name="perfil" required>
                    <option value="admin">Admin</option>
                    <option value="usuario">Usuário</option>
                </select>

                <label for="edit_senha">Nova Senha (deixe em branco para manter a atual):</label>
                <input type="password" id="edit_senha" name="senha">

                <button type="submit" name="editar_usuario">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <script>
        // Função para abrir o modal de edição
        function abrirModalEdicao(id, nome, email, perfil) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nome').value = nome;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_perfil').value = perfil;
            document.getElementById('modalEdicao').style.display = 'block';
        }

        // Função para fechar o modal de edição
        function fecharModalEdicao() {
            document.getElementById('modalEdicao').style.display = 'none';
        }
    </script>
</body>

</html>