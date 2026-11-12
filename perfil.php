<?php
require_once "conexao/auth.php";
verificar_login();

include "includes/header.php";
?>

<div class="card-box" style="max-width: 600px;">
    <h1>👤 Meu Perfil</h1>
    <p style="margin-bottom: 15px;"><strong>Nome:</strong> <?= htmlspecialchars($_SESSION['usuario']['nome']) ?></p>
    <p style="margin-bottom: 15px;"><strong>E-mail:</strong> <?= htmlspecialchars($_SESSION['usuario']['email']) ?></p>
    <p style="margin-bottom: 25px;"><strong>Tipo de Conta:</strong> <?= ucfirst($_SESSION['usuario']['tipo']) ?></p>

    <div style="text-align:center;">
        <a href="/geek_house/index.php" class="btn-voltar">Voltar para a Vitrine</a>
    </div>
</div>

<?php include "includes/footer.php"; ?>
