<?php
require_once "conexao/auth.php";
verificar_login(); // Bloqueia acesso não autenticado

$usuario = $_SESSION['usuario'];

include "includes/header.php";
?>

<div class="card-box">
    <h1>Meu Perfil</h1>
    <div style="line-height:2; text-align:left; margin-bottom:20px;">
        <p><strong>Nome:</strong> <?= htmlspecialchars($usuario['nome']) ?></p>
        <p><strong>E-mail:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
        <p><strong>Tipo de Conta:</strong> <span class="badge-admin"><?= strtoupper(htmlspecialchars($usuario['tipo'])) ?></span></p>
    </div>

    <hr style="border-color:#7c226a; margin-bottom:20px;">

    <?php if ($usuario['tipo'] === 'admin'): ?>
        <a href="admin/index.php" class="btn-enviar" style="display:block; text-align:center; text-decoration:none; margin-bottom:10px;">Painel de Administração</a>
    <?php endif; ?>

    <a href="logout.php" class="btn-voltar">Sair / Logout</a>
</div>

<?php include "includes/footer.php"; ?>
