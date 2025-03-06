<?php
// Iniciar a sessão
session_start();

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
header('Location: ../estetica_automotiva/login.php');
exit();
?>