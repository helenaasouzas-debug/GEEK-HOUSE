<?php
// 1. Inclui o arquivo com as funções de controle de sessão e segurança
require_once "../conexao/auth.php";

// 2. Trava de segurança da Área Administrativa:
// Esta função verifica se existe um usuário logado e se o perfil dele é 'admin'.
// Se alguém tentar acessar digitando o link direto sem permissão, é redirecionado imediatamente.
verificar_admin();

// 3. Inclui o cabeçalho padrão com o menu de navegação da loja (Reutilização de código)
include "../includes/header.php";
?>

<div class="card-box" style="max-width: 650px;">
    <h1>⚙️ Painel Administrativo</h1>
    
    <!-- Exibe mensagem personalizada resgatando o nome do administrador direto da SESSÃO ($_SESSION). -->
    <!-- A função htmlspecialchars() é usada por segurança para evitar ataques do tipo XSS. -->
    <p style="text-align:center; margin-bottom:25px; font-size: 16px;">
        Bem-vindo, <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong>!
    </p>
    
    <!-- Menu principal de atalhos para as operações de cadastro, gerenciamento (CRUD) e pedidos -->
    <div style="display:flex; flex-direction:column; gap:15px; margin-bottom:25px;">
        <a href="produto_cadastrar.php" class="btn-primary" style="text-decoration:none; text-align:center;">
            ➕ Cadastrar Novo Item Geek
        </a>
        <a href="produtos_listar.php" class="btn-primary" style="text-decoration:none; text-align:center;">
            📋 Gerenciar Produtos (Editar / Excluir)
        </a>
        <a href="pedidos_listar.php" class="btn-primary" style="text-decoration:none; text-align:center;">
            📦 Ver Todos os Pedidos dos Clientes
        </a>
    </div>

    <!-- Botão de navegação para retornar à área pública da loja -->
    <div style="text-align:center;">
        <a href="../index.php" class="btn-voltar">🏪 Ver Vitrine da Loja</a>
    </div>
</div>

<?php 
// 4. Inclui o rodapé padrão da página (Reutilização de código)
include "../includes/footer.php"; 
?>
        <a href="../index.php" class="btn-voltar">🏪 Ver Vitrine da Loja</a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
