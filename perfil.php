<?php
// 1. Inclui os arquivos de conexão com o banco ($conn) e as funções de autenticação
require_once "conexao/cnc.php";
require_once "conexao/auth.php";

// 2. Trava de Segurança / Controle de Acesso:
// Garante que apenas usuários devidamente autenticados possam acessar esta página.
// Se o usuário não estiver logado, a função redireciona automaticamente para o login.php.
verificar_login();

$mensagem_sucesso = "";
$mensagem_erro = "";
$usuario_id = $_SESSION['usuario']['id'];

// 3. Processamento do Formulário de Atualização (Método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe e sanitiza os dados enviados pelo usuário
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // 4. Validação dos Campos Obrigatórios
    if (!empty($nome) && !empty($email)) {
        
        // 5. Lógica Condicional de Atualização da Senha:
        // Se o usuário digitou algo no campo de senha, atualizamos Nome, E-mail e Senha.
        if (!empty($senha)) {
            // Prepared Statement (Consulta Preparada) para evitar SQL Injection
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
            // "sssi" define os tipos: s = string (nome), s = string (email), s = string (senha), i = int (id)
            $stmt->bind_param("sssi", $nome, $email, $senha, $usuario_id);
        } else {
            // Se o campo de senha ficou em branco, mantemos a senha antiga e atualizamos apenas Nome e E-mail
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            // "ssi" define os tipos: s = string (nome), s = string (email), i = int (id)
            $stmt->bind_param("ssi", $nome, $email, $usuario_id);
        }

        // 6. Execução da instrução SQL no MySQL
        if ($stmt->execute()) {
            // Atualiza imediatamente os dados da sessão ativa para refletir a mudança no cabeçalho em tempo real
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

// 7. Reutilização de código: inclui o cabeçalho padrão com a barra de navegação
include "includes/header.php";
?>

<div class="card-box" style="max-width: 550px;">
    <h1>👤 Meu Perfil</h1>

    <!-- Exibição de alertas de feedback para o usuário -->
    <?php if (!empty($mensagem_sucesso)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagem_sucesso) ?></div>
    <?php endif; ?>

    <?php if (!empty($mensagem_erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
    <?php endif; ?>

    <!-- Formulário que envia os dados via POST para a própria página (perfil.php) -->
    <form method="POST" action="perfil.php">
        
        <!-- Preenchimento prévio dos inputs usando os dados gravados na sessão ($_SESSION['usuario']) -->
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" class="form-control" required value="<?= htmlspecialchars($_SESSION['usuario']['nome']) ?>">
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($_SESSION['usuario']['email']) ?>">
        </div>

        <!-- Campo de senha opcional -->
        <div class="form-group">
            <label for="senha">Nova Senha (deixe em branco se não quiser alterar)</label>
            <input type="password" id="senha" name="senha" class="form-control" placeholder="••••••••">
        </div>

        <!-- Campo desabilitado (disabled) exibindo o nível de acesso (Admin ou Cliente) -->
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

<?php 
// 8. Reutilização de código: inclui o rodapé padrão da página
include "includes/footer.php"; 
?>
