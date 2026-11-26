<?php
// Edição de dados do produto no MySQL
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";
verificar_admin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mensagem_sucesso = "";
$mensagem_erro = "";

// Busca dados atuais do produto
$stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$prod = $stmt->get_result()->fetch_assoc();

if (!$prod) {
    header("Location: produtos_listar.php");
    exit();
}

$categorias_res = $conn->query("SELECT * FROM categorias ORDER BY nome ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome              = trim($_POST['nome']);
    $categoria_id      = intval($_POST['categoria_id']);
    $preco             = floatval(str_replace(',', '.', $_POST['preco']));
    $quantidade_estoque= intval($_POST['quantidade_estoque']);
    $editora_fabricante= trim($_POST['editora_fabricante']);
    $imagem_url        = trim($_POST['imagem_url']);

    if (!empty($nome) && $categoria_id > 0 && $preco > 0) {
        $stmt_up = $conn->prepare("UPDATE produtos SET categoria_id = ?, nome = ?, preco = ?, quantidade_estoque = ?, editora_fabricante = ?, imagem_url = ? WHERE id = ?");
        $stmt_up->bind_param("isdissi", $categoria_id, $nome, $preco, $quantidade_estoque, $editora_fabricante, $imagem_url, $id);

        if ($stmt_up->execute()) {
            $mensagem_sucesso = "Produto atualizado com sucesso!";
            // Atualiza vetor em memória
            $prod['nome'] = $nome;
            $prod['categoria_id'] = $categoria_id;
            $prod['preco'] = $preco;
            $prod['quantidade_estoque'] = $quantidade_estoque;
            $prod['editora_fabricante'] = $editora_fabricante;
            $prod['imagem_url'] = $imagem_url;
        } else {
            $mensagem_erro = "Erro ao atualizar: " . $conn->error;
        }
    }
}

include "../includes/header.php";
?>

<!-- Formulário de edição de produto -->
<div class="card-box" style="max-width: 600px;">
    <h1>✏️ Editar Produto #<?= $prod['id'] ?></h1>

    <?php if (!empty($mensagem_sucesso)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagem_sucesso) ?></div>
    <?php endif; ?>

    <?php if (!empty($mensagem_erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="produto_editar.php?id=<?= $id ?>">
        <div class="form-group">
            <label for="nome">Nome do Produto</label>
            <input type="text" id="nome" name="nome" class="form-control" required value="<?= htmlspecialchars($prod['nome']) ?>">
        </div>

        <div class="form-group">
            <label for="categoria_id">Categoria</label>
            <select id="categoria_id" name="categoria_id" class="form-control" required>
                <?php while ($cat = $categorias_res->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $prod['categoria_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nome']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label for="preco">Preço (R$)</label>
                <input type="text" id="preco" name="preco" class="form-control" required value="<?= number_format($prod['preco'], 2, ',', '.') ?>">
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="quantidade_estoque">Estoque</label>
                <input type="number" id="quantidade_estoque" name="quantidade_estoque" class="form-control" required value="<?= $prod['quantidade_estoque'] ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="editora_fabricante">Editora / Fabricante</label>
            <input type="text" id="editora_fabricante" name="editora_fabricante" class="form-control" value="<?= htmlspecialchars($prod['editora_fabricante']) ?>">
        </div>

        <div class="form-group">
            <label for="imagem_url">URL da Imagem</label>
            <input type="text" id="imagem_url" name="imagem_url" class="form-control" value="<?= htmlspecialchars($prod['imagem_url']) ?>">
        </div>

        <button type="submit" class="btn-primary">Salvar Alterações</button>

        <div style="text-align: center;">
            <a href="produtos_listar.php" class="btn-voltar">Voltar para a Lista</a>
        </div>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
