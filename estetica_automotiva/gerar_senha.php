<?php
$senha = '123456'; // Senha que você deseja criptografar
$senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);
echo "Senha criptografada: " . $senha_criptografada;
?>