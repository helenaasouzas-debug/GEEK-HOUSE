<<?php
// Consulta e listagem de produtos para gestão
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";
verificar_admin();

include "../includes/header.php";

$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>

<!-- Tabela de gerenciamento de produtos -->
<div class="card-box" style="max-width: 1000px;">
    <h1>📋 Gerenciamento de Produtos</h1>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'excluido'): ?>
        <div class="alert alert-success">Produto excluído com sucesso!</div>
    <?php endif; ?>

    <div style="margin-bottom: 20px; text-align: right;">
        <a href="produto_cadastrar.php" class="btn-primary" style="text-decoration:none; display:inline-block; width:auto; padding: 10px 20px;">
            + Cadastrar Novo Produto
        </a>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagem</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($prod = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $prod['id'] ?></td>
                        <td>
                            <img src="../<?= htmlspecialchars($prod['imagem_url'] ?: 'img/placeholder.png') ?>" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td><strong><?= htmlspecialchars($prod['nome']) ?></strong></td>
                        <td><?= htmlspecialchars($prod['categoria_nome']) ?></td>
                        <td>R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                        <td><?= $prod['quantidade_estoque'] ?> un.</td>
                        <td>
                            <a href="produto_editar.php?id=<?= $prod['id'] ?>" class="btn-edit">✏️ Editar</a>
                            <a href="produto_excluir.php?id=<?= $prod['id'] ?>" class="btn-danger" onclick="return confirm('Deseja realmente excluir este produto?');">🗑️ Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Nenhum produto cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php" class="btn-voltar">← Voltar para o Painel Admin</a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
