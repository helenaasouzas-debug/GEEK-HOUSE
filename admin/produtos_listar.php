<?php
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";
verificar_admin();

$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);

include "../includes/header.php";
?>

<div class="card-box">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1>📋 Gerenciamento de Produtos</h1>
        <a href="produto_cadastrar.php" class="btn-enviar" style="width:auto; text-decoration:none;">+ Novo Produto</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'cadastrado'): ?>
            <div class="alert alert-success">✅ Produto cadastrado com sucesso!</div>
        <?php elseif ($_GET['msg'] === 'atualizado'): ?>
            <div class="alert alert-success">✅ Produto atualizado com sucesso!</div>
        <?php elseif ($_GET['msg'] === 'excluido'): ?>
            <div class="alert alert-success">🗑️ Produto excluído com sucesso!</div>
        <?php endif; ?>
    <?php endif; ?>

    <table class="table-custom">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Produto</th>
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
                        <td><strong>#<?= $prod['id'] ?></strong></td>
                        <td><?= htmlspecialchars($prod['nome']) ?></td>
                        <td><?= htmlspecialchars($prod['categoria_nome']) ?></td>
                        <td>R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                        <td><?= $prod['quantidade_estoque'] ?></td>
                        <td>
                            <a href="produto_editar.php?id=<?= $prod['id'] ?>" class="btn-edit">Editar</a>
                            <a href="produto_excluir.php?id=<?= $prod['id'] ?>" class="btn-danger" onclick="return confirm('Deseja realmente excluir este produto?')">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">Nenhum produto cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "../includes/footer.php"; ?>
