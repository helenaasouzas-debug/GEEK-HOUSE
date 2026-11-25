<?php
// 1. Inclui o arquivo de conexão com o banco de dados ($conn)
require_once "conexao/cnc.php";

$mensagem_erro = "";
$mensagem_sucesso = "";

// 2. Verifica se o formulário foi enviado através do método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3. Recebe e sanitiza os dados enviados pelo usuário
    // trim() remove espaços em branco antes e depois do texto fornecido
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // 4. Validação: verifica se nenhum dos campos obrigatórios ficou em branco
    if (!empty($nome) && !empty($email) && !empty($senha)) {
        
        // 5. Prepared Statement (Consulta Preparada) para verificação de e-mail:
        // Verifica se o e-mail informado já existe na tabela 'usuarios'.
        // O marcador ? com bind_param("s", ...) previne ataques de SQL Injection.
        $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        
        // get_result()->num_rows: se retornar mais de 0 linhas, significa que o e-mail já está em uso
        if ($stmt_check->get_result()->num_rows > 0) {
            $mensagem_erro = "Este e-mail já está cadastrado!";
        } else {
            // 6. Prepared Statement para Inserção (INSERT):
            // Insere o novo registro no banco fixando o perfil 'tipo' como 'cliente' por padrão de segurança.
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'cliente')");
            $stmt->bind_param("sss", $nome, $email, $senha);

            // Executa a gravação no banco de dados MySQL
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

// 7. Reutilização de código: inclui o cabeçalho padrão com a navegação
include "includes/header.php";
?>

<div class="card-box" style="max-width: 450px;">
    <h1>✨ Criar Nova Conta</h1>

    <!-- Exibe mensagens dinâmicas de erro ou sucesso -->
    <?php if (!empty($mensagem_erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
    <?php endif; ?>

    <?php if (!empty($mensagem_sucesso)): ?>
        <!-- Mensagem de sucesso com botão redirecionando para a tela de login -->
        <div class="alert alert-success"><?= htmlspecialchars($mensagem_sucesso) ?></div>
        <div style="text-align: center; margin-top: 15px;">
            <a href="login.php" class="btn-primary" style="text-decoration:none; display:inline-block;">Ir para a Tela de Login</a>
        </div>
    <?php else: ?>
        <!-- Formulário de cadastro enviando dados via POST para o próprio script (cadastrar.php) -->
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

<?php 
// 8. Reutilização de código: inclui o rodapé padrão
include "includes/footer.php"; 
?>
