<?php
require_once "conexao/cnc.php";
require_once "conexao/auth.php";
verificar_login();

$mensagem_sucesso = "";
$mensagem_erro = "";
$usuario_id = $_SESSION['usuario']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (!empty($nome) && !empty($email)) {
        // Se preencheu a nova senha, atualiza a senha também
        if (!empty($senha)) {
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nome, $email, $senha, $usuario_id);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $email, $usuario_id);
        }

        if ($stmt->execute()) {
            $_SESSION['usuario']['nome']  = $nome;
            $_SESSION['usuario']['email'] = $email;
            $mensagem_sucesso = "Perfil atualizado com sucesso!";
        } else {
            $mensagem_erro = "Erro ao atualizar perfil: " . $conn->error;
        }
    } else {
        $mensagem_erro = "Nome e E-mail são obrigatórios!";
    }
}

include "includes/header.php";
?>

<div class="card-box" style="max-width: 550px;">
    <h1>👤 Meu Perfil</h1>

    <?php if (!empty($mensagem_sucesso)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagem_sucesso) ?></div>
    <?php endif; ?>

    <?php if (!empty($mensagem_erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="perfil.php">
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" class="form-control" required value="<?= htmlspecialchars($_SESSION['usuario']['nome']) ?>">
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($_SESSION['usuario']['email']) ?>">
        </div>

        <div class="form-group">
            <label for="senha">Nova Senha (deixe em branco se não quiser alterar)</label>
            <input type="password" id="senha" name="senha" class="form-control" placeholder="••••••••">
        </div>

        <div class="form-group">
            <label>Tipo de Conta</label>
            <input type="text" class="form-control" value="<?= ucfirst($_SESSION['usuario']['tipo']) ?>" disabled style="background-color: #f3f3f3;">
        </div>

        <button type="submit" class="btn-primary">Salvar Alterações</button>

        <div style="text-align: center;">
            <a href="index.php" class="btn-voltar">Voltar para a Vitrine</a>
        </div>
    </form>
</div>

<?php include "includes/footer.php"; ?>
