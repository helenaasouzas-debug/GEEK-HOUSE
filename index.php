<?php
require_once "conexao/cnc.php";
include "includes/header.php";

$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>

<?php if (isset($_GET['erro']) && $_GET['erro'] === 'sem_permissao'): ?>
    <div class="alert alert-danger">⚠️ Acesso negado! Você precisa ser um administrador para acessar essa área.</div>
<?php endif; ?>

<h1 style="color: #5b164f; text-align:center; margin-bottom: 10px;">🏪 Colecionáveis Geek House</h1>
<p style="text-align:center; color: #666; margin-bottom: 25px;">Explore nosso catálogo de mangás, action figures, HQs e vestuário geek!</p>

<div class="products-grid">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($prod = $result->fetch_assoc()): ?>
            <div class="product-card">
                <div>
                    <img src="<?= htmlspecialchars($prod['imagem_url'] ?: 'img/placeholder.png') ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                    <span class="product-category"><?= htmlspecialchars($prod['categoria_nome']) ?></span>
                    <h3 class="product-title"><?= htmlspecialchars($prod['nome']) ?></h3>
                    <p style="font-size: 13px; color: #666; margin-bottom: 10px;"><?= htmlspecialchars($prod['editora_fabricante'] ?: 'Geek') ?></p>
                </div>
                <div>
                    <div class="product-price">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                    <p style="font-size: 12px; color: #888; margin-bottom: 10px;">Estoque: <?= $prod['quantidade_estoque'] ?> un.</p>
                    
                    <a href="carrinho.php?acao=add&id=<?= $prod['id'] ?>" class="btn-primary" style="text-decoration:none; display:block; text-align:center;">
                        🛒 Adicionar ao Carrinho
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; grid-column: 1/-1;">Nenhum produto cadastrado no momento.</p>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
