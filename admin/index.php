<?php
define('IN_ADMIN', true);
require_once "../conexao/auth.php";
verificar_admin(); // Trava de segurança

include "../includes/header.php";
?>

<div class="card-box" style="max-width: 650px;">
    <h1>Painel Administrativo</h1>
    <p style="text-align:center; margin-bottom:20px;">Bem-vindo, <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong>!</p>
    
    <div style="display:flex; flex-direction:column; gap:15px; margin-bottom:25px;">
        <a href="produto_cadastrar.php" class="btn-enviar" style="text-decoration:none;">+ Cadastrar Novo Item Geek</a>
        <a href="produtos_listar.php" class="btn-primary" style="text-decoration:none;">📋 Gerenciar Produtos (Editar / Excluir)</a>
    </div>

    <div style="text-align:center;">
        <a href="../perfil.php" class="btn-voltar">Ver Perfil</a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
