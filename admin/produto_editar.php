<?php
// 1. Inclui o arquivo de conexão com o banco de dados ($conn) e o controle de autenticação
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";

// 2. Trava de segurança: garante que apenas administradores autenticados acessem a página de edição
verificar_admin();

// 3. Captura o ID do produto passado pela URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Se o ID for inválido (menor ou igual a zero), redireciona de volta para a lista
if ($id <= 0) {
    header("Location: produtos_listar.php");
    exit();
}

// 4. Prepared Statement ndo o ID capturado.
$stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

if (!$produto) {
    header("Location: produtos_listar.php");
    exit();
}

// 5. Busca as categorias no banco para preencher o campo <select> do formulário
$cat_query = $conn->query("SELECT * FROM categorias ORDER BY nome ASC");
$mensagem = "";

// 6. Processamento do formulário quando o usuário clica em "Atualizar Registro" (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome               = trim($_POST['nome']);
    $categoria_id       = intval($_POST['categoria_id']);
    $preco              = floatval($_POST['preco']);
    $quantidade_estoque = intval($_POST['quantidade_estoque']);
    $editora_fabricante = trim($_POST['editora_fabricante']);
    $especificacao      = trim($_POST['especificacao']);
    $condicao           = trim($_POST['condicao']);
    $descricao           = trim($_POST['descricao']);
    $imagem_url         = trim($_POST['imagem_url']);

    if (!empty($nome) && $categoria_id > 0 && $preco > 0) {
        // 7. Prepared Statement para o comando UPDATE:
        // Atualiza os registros do produto selecionado no banco de dados com base no ID
        $update_stmt = $conn->prepare("UPDATE produtos SET nome = ?, categoria_id = ?, preco = ?, quantidade_estoque = ?, editora_fabricante = ?, especificacao = ?, condicao = ?, descricao = ?, imagem_url = ? WHERE id = ?");
        
        $update_stmt->bind_param("sidisssssi", $nome, $categoria_id, $preco, $quantidade_estoque, $editora_fabricante, $especificacao, $condicao, $descricao, $imagem_url, $id);
        
        // Executa a alteração no MySQL
        if ($update_stmt->execute()) {
            // Se alterado com sucesso, redireciona para a lista avisando a atualização
            header("Location: produtos_listar.php?msg=atualizado");
            exit();
        } else {
            $mensagem = "Erro ao atualizar produto: " . $conn->error;
        }
    } else {
        $mensagem = "Preencha os campos obrigatórios!";
    }
}

// 8. Reutilização de código: carrega o cabeçalho padrão
include "../includes/header.php";
?>

<div class="card-box" style="max-width: 650px;">
    <h1>✏️ Editar Produto #<?= $produto['id'] ?></h1>

    <!-- Exibe mensagens de erro de validação ou de banco de dados -->
    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <!-- Formulário com método POST passando o ID do produto atual na URL de ação -->
    <form method="POST" action="produto_editar.php?id=<?= $produto['id'] ?>">
        
        <!-- Campo Nome preenchido dinamicamente com os dados buscados no MySQL ($produto['nome']) -->
        <div class="form-group">
            <label for="nome">Nome do Produto *</label>
            <input type="text" id="nome" name="nome" class="form-control" required value="<?= htmlspecialchars($produto['nome']) ?>">
        </div>

        <!-- Select de Categorias com verificação para marcar a categoria atual como 'selected' -->
        <div class="form-group">
            <label for="categoria_id">Categoria *</label>
            <select id="categoria_id" name="categoria_id" class="form-control" required>
                <?php while ($cat = $cat_query->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $produto['categoria_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nome']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div style="display:flex; gap:15px;">
            <div class="form-group" style="flex:1;">
                <label for="preco">Preço (R$) *</label>
                <input type="number" step="0.01" id="preco" name="preco" class="form-control" required value="<?= $produto['preco'] ?>">
            </div>

            <div class="form-group" style="flex:1;">
                <label for="quantidade_estoque">Estoque *</label>
                <input type="number" id="quantidade_estoque" name="quantidade_estoque" class="form-control" required value="<?= $produto['quantidade_estoque'] ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="editora_fabricante">Editora / Fabricante</label>
            <input type="text" id="editora_fabricante" name="editora_fabricante" class="form-control" value="<?= htmlspecialchars($produto['editora_fabricante']) ?>">
        </div>

        <div class="form-group">
            <label for="especificacao">Especificação / Tamanho</label>
            <input type="text" id="especificacao" name="especificacao" class="form-control" value="<?= htmlspecialchars($produto['especificacao']) ?>">
        </div>

        <!-- Select de Condição com operador ternário mantendo a opção atual selecionada -->
        <div class="form-group">
            <label for="condicao">Condição</label>
            <select id="condicao" name="condicao" class="form-control">
                <option value="Novo/Lacrado" <?= $produto['condicao'] === 'Novo/Lacrado' ? 'selected' : '' ?>>Novo/Lacrado</option>
                <option value="Usado/Seminovo" <?= $produto['condicao'] === 'Usado/Seminovo' ? 'selected' : '' ?>>Usado/Seminovo</option>
                <option value="Colecionador" <?= $produto['condicao'] === 'Colecionador' ? 'selected' : '' ?>>Colecionador</option>
            </select>
        </div>

        <div class="form-group">
            <label for="imagem_url">Caminho da Imagem</label>
            <input type="text" id="imagem_url" name="imagem_url" class="form-control" value="<?= htmlspecialchars($produto['imagem_url']) ?>">
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control" rows="3"><?= htmlspecialchars($produto['descricao']) ?></textarea>
        </div>

        <button type="submit" class="btn-primary">Atualizar Registro</button>
        <div style="text-align:center;">
            <a href="produtos_listar.php" class="btn-voltar">Cancelar</a>
        </div>
    </form>
</div>

<?php 
// 9. Reutilização de código: carrega o rodapé padrão
include "../includes/footer.php"; 
?>
