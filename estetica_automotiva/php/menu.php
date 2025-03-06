<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: /estetica_automotiva/login.php');
    exit();
}
?>

<div class="menu-lateral">
    <h2>Menu</h2>
    <ul>
        <li><a href="/estetica_automotiva/pagina_inicial.php">Inicio</a></li>
        <li><a href="/estetica_automotiva/php/cadastro_fidelidade.php">Cadastrar Cliente Fidelidade</a></li>
        <li><a href="/estetica_automotiva/php/gerenciar_servicos.php">Gerenciar Serviços</a></li>
        <li><a href="/estetica_automotiva/php/agendar_servico.php">Agendar Serviço</a></li>
        <li><a href="/estetica_automotiva/php/gerenciar_agendamentos.php">Gerenciar Agendamentos</a></li>
        <li><a href="/estetica_automotiva/php/gerir_usuarios.php">Gerir Usuarios</a></li>
        <li><a href="/estetica_automotiva/php/relatorios.php">Relatorios</a></li>
        <li><a href="/estetica_automotiva/logout.php">Sair</a></li>
    </ul>
</div>