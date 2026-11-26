<?php
// Cadastro de novo produto no banco de dados
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";
verificar_admin();

$mensagem_sucesso = "";
$mensagem_erro = "";

// Consulta categorias disponíveis
$categorias_res = $conn->query("SELECT * FROM categorias ORDER BY nome ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome              = trim($_POST['nome']);
    $categoria_id      = intval($_POST['categoria_id']);
    $preco             = floatval(str_replace(',', '.', $_POST['preco']));
    $quantidade_estoque= intval($_POST['quantidade_estoque']);
    $editora_fabricante= trim($_POST['editora_fabricante']);
    $imagem_url        = trim($_POST['imagem_url']);

    if (!empty($nome) && $categoria_id > 0 && $preco > 0) {
        $stmt = $conn->prepare("INSERT INTO produtos (categoria_id, nome, preco, quantidade_estoque, editora_fabricante, imagem_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdiss", $categoria_id, $nome, $preco, $quantidade_estoque, $editora_fabricante, $imagem_url);

        if ($stmt->execute()) {
            $mensagem_sucesso = "Produto cadastrado com sucesso!";
        } else {
            $mensagem_erro = "Erro ao cadastrar: " . $conn->error;
        }
    } else {
        $mensagem_erro = "Preencha os campos obrigatórios!";
    }
}

include "../includes/header.php";
?>

<!-- Formulário de cadastro de produto -->
<div class="card-box" style="max-width: 600px;">
    <h1>➕ Cadastrar Novo Item Geek</h1>

    <?php if (!empty($mensagem_sucesso)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagem_sucesso) ?></div>
    <?php endif; ?>

    <?php if (!empty($mensagem_erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="produto_cadastrar.php">
        <div class="form-group">
            <label for="nome">Nome do Produto *</label>
            <input type="text" id="nome" name="nome" class="form-control" required placeholder="Ex: Figure Luffy Gear 5">
        </div>

        <div class="form-group">
            <label for="categoria_id">Categoria *</label>
            <select id="categoria_id" name="categoria_id" class="form-control" required>
                <option value="">Selecione a categoria...</option>
                <?php while ($cat = $categorias_res->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label for="preco">Preço (R$) *</label>
                <input type="text" id="preco" name="preco" class="form-control" required placeholder="250,00">
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="quantidade_estoque">Estoque Inicial *</label>
                <input type="number" id="quantidade_estoque" name="quantidade_estoque" class="form-control" required value="10">
            </div>
        </div>

        <div class="form-group">
            <label for="editora_fabricante">Editora / Fabricante</label>
            <input type="text" id="editora_fabricante" name="editora_fabricante" class="form-control" placeholder="Ex: Bandai / Panini">
        </div>

        <div class="form-group">
            <label for="imagem_url">URL da Imagem</label>
            <input type="text" id="imagem_url" name="imagem_url" class="form-control" placeholder="img/produto.png">
        </div>

        <button type="submit" class="btn-enviar">Cadastrar Produto</button>

        <div style="text-align: center;">
            <a href="produtos_listar.php" class="btn-voltar">Voltar para a Lista</a>
        </div>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
