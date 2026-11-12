<?php
// conexao/auth.php - Gerenciamento de Sessões e Trava de Segurança
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verificar_login() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: /geek_house/login.php?erro=acesso_negado");
        exit();
    }
}

function verificar_admin() {
    verificar_login();
    if ($_SESSION['usuario']['tipo'] !== 'admin') {
        header("Location: /geek_house/index.php?erro=sem_permissao");
        exit();
    }
}
?>
