<?php
// 1. Inicialização segura da Sessão:
// Verifica se a sessão PHP já foi iniciada; se não foi, chama session_start().
// Isso impede erros de "session already started" ao incluir este arquivo em diferentes páginas.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Cálculo dinâmico de itens no Carrinho de Compras:
// Inicializa o contador zerado e, caso exista a variável de sessão $_SESSION['carrinho'],
// utiliza a função array_sum() para somar todas as quantidades salvas no array.
$total_itens_carrinho = 0;
if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
    $total_itens_carrinho = array_sum($_SESSION['carrinho']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geek House — Sua Loja Colecionável</title>
    <!-- Carrega a folha de estilo CSS com parâmetro time() para evitar cache no navegador -->
    <link rel="stylesheet" href="/geek_house/css/estilo.css?v=<?= time() ?>">
</head>
<body>

<header class="top-header">
    <div class="header-container">
        <!-- LOGO E IDENTIDADE DA LOJA -->
        <a href="/geek_house/sobre.php" class="logo" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
            <!-- Tratamento de erro na imagem da logo com fallback (onerror) em JavaScript -->
            <img src="/geek_house/img/logo.png" 
                 onerror="this.onerror=null; this.src='/geek_house/img/geek_house_kuromi_logo.png';" 
                 alt="Geek House Logo" 
                 style="height: 48px; width: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #e9d5ff; background: #fff;">
            <span>GEEK HOUSE</span>
        </a>

        <!-- MENU DE NAVEGAÇÃO DINÂMICO -->
        <nav class="nav-menu">
            <a href="/geek_house/sobre.php" class="btn-nav">ℹ️ Sobre</a>
            <a href="/geek_house/index.php" class="btn-nav">🏪 Vitrine</a>
            
            <!-- 3. Exibição condicional do Carrinho:
                 Esconde o botão do carrinho caso o usuário logado seja um administrador. -->
            <?php if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin'): ?>
                <a href="/geek_house/carrinho.php" class="btn-carrinho">
                    🛒 Carrinho <strong>(<?= $total_itens_carrinho ?>)</strong>
                </a>
            <?php endif; ?>
            
            <!-- 4. Menu condicional de Usuário Autenticado vs Visitante: -->
            <?php if (isset($_SESSION['usuario'])): ?>
                <!-- Se o usuário estiver logado -->
                <a href="/geek_house/perfil.php" class="btn-nav">👤 Meu Perfil</a>

                <!-- Se o usuário logado for do tipo 'admin', exibe o link do Painel Administrativo -->
                <?php if ($_SESSION['usuario']['tipo'] === 'admin'): ?>
                    <a href="/geek_house/admin/index.php" class="btn-nav">⚙️ Painel Admin</a>
                <?php endif; ?>

                <!-- Exibe mensagem de boas-vindas com nome sanitizado (htmlspecialchars) -->
                <span class="user-greeting">Olá, <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong>!</span>
                <a href="/geek_house/logout.php" class="btn-logout">🚪 Sair</a>

            <?php else: ?>
                <!-- Se for um visitante não logado, exibe os botões de Entrar e Cadastrar -->
                <a href="/geek_house/login.php" class="btn-nav">🔑 Entrar</a>
                <a href="/geek_house/cadastrar.php" class="btn-nav" style="background-color: #5b164f !important;">✨ Cadastre-se</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- Abertura da tag principal da página (o conteúdo específico de cada tela entra aqui) -->
<main class="main-content">
