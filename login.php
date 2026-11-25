<?php
// 1. Inclui os arquivos de conexão com o banco ($conn) e o controle de autenticação/sessão
require_once "conexao/cnc.php";
require_once "conexao/auth.php";

$mensagem_erro = "";

// 2. Verifica se o formulário foi enviado via método POST ao clicar em "Entrar"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3. Recebe e limpa os dados digitados nos campos de e-mail e senha
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // 4. Validação: garante que ambos os campos foram preenchidos
    if (!empty($email) && !empty($senha)) {
        
        // 5. Prepared Statement (Consulta Preparada):
        // Busca o usuário no MySQL através do e-mail digitado.
        // O uso de '?' com bind_param("s", ...) previne ataques de SQL Injection.
        $stmt = $conn->prepare("SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // 6. Verifica se encontrou exatamente 1 usuário cadastrado com esse e-mail
        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            
            // 7. Autenticação de Senha:
            // Compara a senha informada no formulário com a senha registrada no banco de dados.
            if ($senha === $usuario['senha']) {
                
                // 8. Criação da Sessão de Autenticação:
                // Grava o array $_SESSION['usuario'] com os dados do usuário autenticado.
                // Isso permite que o sistema reconheça quem está logado em todas as páginas do portal.
                $_SESSION['usuario'] = [
                    'id'    => $usuario['id'],
                    'nome'  => $usuario['nome'],
                    'email' => $usuario['email'],
                    'tipo'  => $usuario['tipo']
                ];

                // 9. Redirecionamento baseado no perfil de acesso (admin vs cliente):
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

// 10. Reutilização de código: inclui o cabeçalho padrão da página
include "includes/header.php";
?>

<div class="card-box" style="max-width: 420px;">
    <h1>🔑 Login</h1>

    <!-- Exibe alerta de acesso negado caso o usuário tente acessar uma área restrita sem logar -->
    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'acesso_negado'): ?>
        <div class="alert alert-danger">⚠️ Faça login para acessar a área administrativa.</div>
    <?php endif; ?>

    <!-- Exibe mensagens de erro de validação (e-mail não encontrado, senha incorreta, etc.) -->
    <?php if (!empty($mensagem_erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
    <?php endif; ?>

    <!-- Formulário de login enviando dados via POST para a própria página -->
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

<?php 
// 11. Reutilização de código: inclui o rodapé padrão da página
include "includes/footer.php"; 
?>
