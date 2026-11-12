<?php
require_once "conexao/cnc.php";

$mensagem_erro = "";
$mensagem_sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (!empty($nome) && !empty($email) && !empty($senha)) {
        // Verifica se o e-mail já existe no banco
        $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        
        if ($stmt_check->get_result()->num_rows > 0) {
            $mensagem_erro = "Este e-mail já está cadastrado!";
        } else {
            // Insere o novo usuário como 'cliente'
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'cliente')");
            $stmt->bind_param("sss", $nome, $email, $senha);

            if ($stmt->execute()) {
                $mensagem_sucesso = "Conta criada com sucesso! Faça login para continuar.";
            } else {
                $mensagem_erro = "Erro ao criar conta: " . $conn->error;
            }
        }
    } else {
        $mensagem_erro = "Preencha todos os campos!";
    }
}

include "includes/header.php";
?>

<div class="card-box" style="max-width: 450px;">
    <h1>✨ Criar Nova Conta</h1>

    <?php if (!empty($mensagem_erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
    <?php endif; ?>

    <?php if (!empty($mensagem_sucesso)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagem_sucesso) ?></div>
        <div style="text-align: center; margin-top: 15px;">
            <a href="login.php" class="btn-primary" style="text-decoration:none; display:inline-block;">Ir para a Tela de Login</a>
        </div>
    <?php else: ?>
        <form method="POST" action="cadastrar.php">
            <div class="form-group">
                <label for="nome">Nome Completo *</label>
                <input type="text" id="nome" name="nome" class="form-control" required placeholder="Seu nome">
            </div>

            <div class="form-group">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="seu@email.com">
            </div>

            <div class="form-group">
                <label for="senha">Senha *</label>
                <input type="password" id="senha" name="senha" class="form-control" required placeholder="Sua senha">
            </div>

            <button type="submit" class="btn-enviar">Cadastrar Conta</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 14px;">
            Já tem uma conta? <a href="login.php" style="color: #7c3aed; font-weight: bold;">Faça Login aqui</a>.
        </p>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
