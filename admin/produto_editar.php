<?php
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";
verificar_admin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: produtos_listar.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

if (!$produto) {
    header("Location: produtos_listar.php");
    exit();
}

$cat_query = $conn->query("SELECT * FROM categorias ORDER BY nome ASC");
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome              = trim($_POST['nome']);
    $categoria_id      = intval($_POST['categoria_id']);
    $preco             = floatval($_POST['preco']);
    $quantidade_estoque= intval($_POST['quantidade_estoque']);
    $editora_fabricante= trim($_POST['editora_fabricante']);
    $especificacao     = trim($_POST['especificacao']);
    $condicao          = trim($_POST['condicao']);
    $descricao          = trim($_POST['descricao']);
    $imagem_url        = trim($_POST['imagem_url']);

    if (!empty($nome) && $categoria_id > 0 && $preco > 0) {
        $update_stmt = $conn->prepare("UPDATE produtos SET nome = ?, categoria_id = ?, preco = ?, quantidade_estoque = ?, editora_fabricante = ?, especificacao = ?, condicao = ?, descricao = ?, imagem_url = ? WHERE id = ?");
        $update_stmt->bind_param("sidisssssi", $nome, $categoria_id, $preco, $quantidade_estoque, $editora_fabricante, $especificacao, $condicao, $descricao, $imagem_url, $id);
        
        if ($update_stmt->execute()) {
            header("Location: produtos_listar.php?msg=atualizado");
            exit();
        } else {
            $mensagem = "Erro ao atualizar produto: " . $conn->error;
        }
    } else {
        $mensagem = "Preencha os campos obrigatórios!";
    }
}

include "../includes/header.php";
?>

<div class="card-box" style="max-width: 650px;">
    <h1>✏️ Editar Produto #<?= $produto['id'] ?></h1>

    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <form method="POST" action="produto_editar.php?id=<?= $produto['id'] ?>">
        <div class="form-group">
            <label for="nome">Nome do Produto *</label>
            <input type="text" id="nome" name="nome" class="form-control" required value="<?= htmlspecialchars($produto['nome']) ?>">
        </div>

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

<?php include "../includes/footer.php"; ?>
