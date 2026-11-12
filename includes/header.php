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
    <title>Geek House — Sua Loja de Colecionáveis</title>
    
    <!-- CAMINHO ABSOLUTO DO CSS (Funciona na raiz e na pasta /admin) -->
    <link rel="stylesheet" href="/geek_house/css/estilo.css">
</head>
<body>

<header class="top-header">
    <div class="header-container">
        <!-- LOGO DA LOJA -->
        <a href="/geek_house/index.php" class="logo">
            ⚡ <span>GEEK HOUSE</span>
        </a>

        <!-- MENU DINÂMICO DE NAVEGAÇÃO -->
        <nav class="nav-menu">
            <a href="/geek_house/index.php">🏪 Vitrine</a>
            
            <?php if (isset($_SESSION['usuario'])): ?>
                <!-- Se o usuário estiver LOGADO -->
                <?php if ($_SESSION['usuario']['tipo'] === 'admin'): ?>
                    <!-- Links exclusivos para ADMINISTRADOR -->
                    <a href="/geek_house/admin/index.php" class="btn-admin">⚙️ Painel Admin</a>
                    <a href="/geek_house/admin/produtos_listar.php">📋 Gerenciar Produtos</a>
                <?php else: ?>
                    <!-- Link de perfil para CLIENTE -->
                    <a href="/geek_house/perfil.php">👤 Meu Perfil</a>
                <?php endif; ?>

                <span class="user-greeting">Olá, <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong>!</span>
                <a href="/geek_house/logout.php" class="btn-logout">🚪 Sair</a>

            <?php else: ?>
                <!-- Se o usuário NÃO estiver logado -->
                <a href="/geek_house/login.php" class="btn-login">🔑 Entrar / Cadastrar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="main-content">
