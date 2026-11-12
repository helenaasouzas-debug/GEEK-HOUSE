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
    
    <!-- FORÇA O NAVEGADOR A CARREGAR O CSS NOVO SEMPRE -->
    <link rel="stylesheet" href="/geek_house/css/estilo.css?v=<?= time() ?>">
</head>
<body>

<header class="top-header">
    <div class="header-container">
        <!-- LOGO DA KUROMI (COM BUSCA AUTOMÁTICA DE IMAGEM) -->
        <a href="/geek_house/index.php" class="logo" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
            <img src="/geek_house/img/logo.png" 
                 onerror="this.onerror=null; this.src='/geek_house/img/geek_house_kuromi_logo.png';" 
                 alt="Geek House Logo" 
                 style="height: 48px; width: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #e9d5ff; background: #fff;">
            <span>GEEK HOUSE</span>
        </a>

        <!-- MENU COM TODOS OS BOTÕES ROXINHOS -->
        <nav class="nav-menu">
            <a href="/geek_house/index.php" class="btn-nav">🏪 Vitrine</a>
            
            <?php if (isset($_SESSION['usuario'])): ?>
                <?php if ($_SESSION['usuario']['tipo'] === 'admin'): ?>
                    <a href="/geek_house/admin/index.php" class="btn-nav">⚙️ Painel Admin</a>
                    <a href="/geek_house/admin/produtos_listar.php" class="btn-nav">📋 Gerenciar Produtos</a>
                <?php else: ?>
                    <a href="/geek_house/perfil.php" class="btn-nav">👤 Meu Perfil</a>
                <?php endif; ?>

                <span class="user-greeting">Olá, <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong>!</span>
                <a href="/geek_house/logout.php" class="btn-logout">🚪 Sair</a>

            <?php else: ?>
                <a href="/geek_house/login.php" class="btn-nav">🔑 Entrar / Cadastrar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="main-content">
