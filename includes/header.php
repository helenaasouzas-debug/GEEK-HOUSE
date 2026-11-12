<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geek House — Sua Loja Colecionável</title>
    <link rel="stylesheet" href="/geek_house/css/estilo.css">
</head>
<body>

<header class="top-header">
    <div class="header-container">
        <a href="/geek_house/index.php" class="logo">
            ⚡ <span>GEEK HOUSE</span>
        </a>

        <nav class="nav-menu">
            <a href="/geek_house/index.php">🏪 Vitrine</a>
            
            <?php if (isset($_SESSION['usuario'])): ?>
                <?php if ($_SESSION['usuario']['tipo'] === 'admin'): ?>
                    <a href="/geek_house/admin/index.php" class="btn-admin">⚙️ Painel Admin</a>
                    <a href="/geek_house/admin/produtos_listar.php">📋 Gerenciar Produtos</a>
                <?php else: ?>
                    <a href="/geek_house/perfil.php">👤 Meu Perfil</a>
                <?php endif; ?>

                <span class="user-greeting">Olá, <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong>!</span>
                <a href="/geek_house/logout.php" class="btn-logout">🚪 Sair</a>

            <?php else: ?>
                <a href="/geek_house/login.php" class="btn-login">🔑 Entrar / Cadastrar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="main-content">
