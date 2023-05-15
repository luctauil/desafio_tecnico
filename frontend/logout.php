<?php
session_start();

function logout() {
    // Destruir todas as variáveis de sessão
    session_unset();

    // Destruir a sessão
    session_destroy();

    // Redirecionar para a tela de login ou outra página de sua escolha
    header('Location: login.php');
    exit();
}

logout();
?>
