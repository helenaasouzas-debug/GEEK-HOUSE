<?php
require_once "../conexao/auth.php";
verificar_admin();

include "../includes/header.php";
?>

<div class="card-box" style="max-width: 650px;">
    <h1>⚙️ Painel Administrativo</h1>
    <p style="text-align:center; margin-bottom:25px; font-size: 16px;">
        Bem-vindo, <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong>!
    </p>
    
    <!-- BLOCO COM TODOS OS BOTÕES ADMINISTRATIVOS ALINHADOS -->
    <div style="display:flex; flex-direction:column; gap:15px; margin-bottom:25px;">
        <a href="produto_cadastrar.php" class="btn-enviar" style="text-decoration:none; text-align:center;">
            + Cadastrar Novo Item Geek
        </a>
        <a href="produtos_listar.php" class="btn-primary" style="text-decoration:none; text-align:center;">
            📋 Gerenciar Produtos (Editar / Excluir)
        </a>
        <a href="pedidos_listar.php" class="btn-primary" style="text-decoration:none; text-align:center; background: linear-gradient(135deg, #8e245f, #5b164f);">
            📦 Ver Todos os Pedidos dos Clientes
        </a>
    </div>

    <!-- BOTÃO DE VOLTAR PARA A VITRINE NO RODAPÉ DO CARD -->
    <div style="text-align:center;">
        <a href="../index.php" class="btn-voltar">🏪 Ver Vitrine da Loja</a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
