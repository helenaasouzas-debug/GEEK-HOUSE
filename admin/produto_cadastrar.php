<?php
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";
verificar_admin();

$mensagem = "";

$cat_query = $conn->query("SELECT * FROM categorias ORDER BY nome ASC");

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
        $stmt = $conn->prepare("INSERT INTO produtos (nome, categoria_id, preco, quantidade_estoque, editora_fabricante, especificacao, condicao, descricao, imagem_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sidisssss", $nome, $categoria_id, $preco, $quantidade_estoque, $editora_fabricante, $especificacao, $condicao, $descricao, $imagem_url);
        
        if ($stmt->execute()) {
            header("Location: produtos_listar.php?msg=cadastrado");
            exit();
        } else {
            $mensagem = "Erro ao cadastrar produto: " . $conn->error;
        }
    } else {
        $mensagem = "Preencha os campos obrigatórios!";
    }
}

include "../includes/header.php";
?>

<div class="card-box" style="max-width: 650px;">
    <h1>+ Cadastrar Produto Geek</h1>

    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <form method="POST" action="produto_cadastrar.php">
        <div class="form-group">
            <label for="nome">Nome do Produto *</label>
            <input type="text" id="nome" name="nome" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="categoria_id">Categoria *</label>
            <select id="categoria_id" name="categoria_id" class="form-control" required>
                <option value="">Selecione uma Categoria...</option>
                <?php while ($cat = $cat_query->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div style="display:flex; gap:15px;">
            <div class="form-group" style="flex:1;">
                <label for="preco">Preço (R$) *</label>
                <input type="number" step="0.01" id="preco" name="preco" class="form-control" required placeholder="0.00">
            </div>

            <div class="form-group" style="flex:1;">
                <label for="quantidade_estoque">Estoque *</label>
                <input type="number" id="quantidade_estoque" name="quantidade_estoque" class="form-control" required value="10">
            </div>
        </div>

        <div class="form-group">
            <label for="editora_fabricante">Editora / Fabricante</label>
            <input type="text" id="editora_fabricante" name="editora_fabricante" class="form-control" placeholder="Panini, Bandai, Piticas, etc.">
        </div>

        <div class="form-group">
            <label for="especificacao">Especificação / Tamanho</label>
            <input type="text" id="especificacao" name="especificacao" class="form-control" placeholder="Vol. 01, Escala 15cm, Tamanho M...">
        </div>

        <div class="form-group">
            <label for="condicao">Condição</label>
            <select id="condicao" name="condicao" class="form-control">
                <option value="Novo/Lacrado">Novo/Lacrado</option>
                <option value="Usado/Seminovo">Usado/Seminovo</option>
                <option value="Colecionador">Colecionador</option>
            </select>
        </div>

        <div class="form-group">
            <label for="imagem_url">Caminho da Imagem</label>
            <input type="text" id="imagem_url" name="imagem_url" class="form-control" value="img/placeholder.png">
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn-enviar">Salvar Produto</button>
        <div style="text-align:center;">
            <a href="produtos_listar.php" class="btn-voltar">Cancelar</a>
        </div>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
