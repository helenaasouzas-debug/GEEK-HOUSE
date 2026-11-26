<?php
// Autenticação de usuário
require_once "conexao/cnc.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensagem_erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (!empty($email) && !empty($senha)) {
        // Consulta credenciais no MySQL
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = ? LIMIT 1");
        $stmt->bind_param("ss", $email, $senha);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            $_SESSION['usuario'] = [
                'id'    => $user['id'],
                'nome'  => $user['nome'],
                'email' => $user['email'],
                'tipo'  => $user['tipo']
            ];

            // Redirecionamento de acordo com o perfil
            if ($user['tipo'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $mensagem_erro = "E-mail ou senha incorretos!";
        }
    } else {
        $mensagem_erro = "Preencha todos os campos!";
    }
}

include "includes/header.php";
?>

<!-- Formulário de login -->
<div class="card-box" style="max-width: 450px;">
    <h1>🔑 Entrar na Geek House</h1>

    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'acesso_negado'): ?>
        <div class="alert alert-danger">⚠️ Você precisa estar logado para acessar a página solicitada.</div>
    <?php endif; ?>

    <?php if (!empty($mensagem_erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="seu@email.com">
        </div>

        <div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" class="form-control" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn-enviar">Acessar Conta</button>
    </form>

    <p style="text-align: center; margin-top: 20px; font-size: 14px;">
        Não tem uma conta? <a href="cadastrar.php" style="color: #7c3aed; font-weight: bold;">Cadastre-se aqui</a>.
    </p>
</div>

<?php include "includes/footer.php"; ?>
