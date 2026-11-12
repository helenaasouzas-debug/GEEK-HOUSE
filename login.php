<?php
require_once "conexao/cnc.php";
require_once "conexao/auth.php";

$mensagem_erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (!empty($email) && !empty($senha)) {
        $stmt = $conn->prepare("SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            
            // Comparação simples de senha para ambiente acadêmico
            if ($senha === $usuario['senha']) {
                $_SESSION['usuario'] = [
                    'id'    => $usuario['id'],
                    'nome'  => $usuario['nome'],
                    'email' => $usuario['email'],
                    'tipo'  => $usuario['tipo']
                ];

                if ($usuario['tipo'] === 'admin') {
                    header("Location: /geek_house/admin/index.php");
                } else {
                    header("Location: /geek_house/perfil.php");
                }
                exit();
            } else {
                $mensagem_erro = "Senha incorreta!";
            }
        } else {
            $mensagem_erro = "E-mail não cadastrado!";
        }
    } else {
        $mensagem_erro = "Preencha todos os campos!";
    }
}

include "includes/header.php";
?>

<div class="card-box" style="max-width: 420px;">
    <h1>🔑 Login</h1>

    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'acesso_negado'): ?>
        <div class="alert alert-danger">⚠️ Faça login para acessar a área administrativa.</div>
    <?php endif; ?>

    <?php if (!empty($mensagem_erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="admin@geekhouse.com">
        </div>

        <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" class="form-control" required placeholder="12345">
        </div>

        <button type="submit" class="btn-primary">Entrar no Sistema</button>
    </form>
</div>

<?php include "includes/footer.php"; ?>
include "includes/header.php";
?>

<div class="card-box">
    <h1>Geek House - Login</h1>

    <?php if (!empty($erro)): ?>
        <p class="erro"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'acesso_negado'): ?>
        <p class="erro">Você precisa estar logado para acessar essa área!</p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="campo">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="seuemail@exemplo.com" required>
        </div>

        <div class="campo">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
        </div>

        <button type="submit" class="btn-enviar">Entrar</button>
        <a href="index.php" class="btn-voltar">Voltar para a Loja</a>
    </form>
</div>

<?php include "includes/footer.php"; ?>
