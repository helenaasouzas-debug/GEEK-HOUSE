<?php
// Conexão e cabeçalho
require_once "conexao/cnc.php";
include "includes/header.php";

// Consulta de produtos e categorias
$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>

<!-- Alerta de acesso negado -->
<?php if (isset($_GET['erro']) && $_GET['erro'] === 'sem_permissao'): ?>
    <div class="alert alert-danger">⚠️ Acesso negado! Área restrita a administradores.</div>
<?php endif; ?>

<!-- Título principal -->
<div class="vitrine-header">
    <h1>Vitrine Geek House</h1>
    <p>Confira nossos lançamentos de mangás, figures, HQs e colecionáveis!</p>
</div>

<!-- Grid de produtos no estilo Google Shopping -->
<div class="products-grid">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($prod = $result->fetch_assoc()): ?>
            <div class="product-card">
                
                <!-- Imagem do produto -->
                <div class="product-image-box">
                    <img src="<?= htmlspecialchars($prod['imagem_url'] ?: 'img/placeholder.png') ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                </div>

                <!-- Detalhes do produto -->
                <div class="product-details">
                    <span class="product-badge"><?= htmlspecialchars($prod['categoria_nome']) ?></span>
                    <h3 class="product-name"><?= htmlspecialchars($prod['nome']) ?></h3>
                    <p class="product-maker"><?= htmlspecialchars($prod['editora_fabricante'] ?: 'Geek House') ?></p>
                    <div class="product-price">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                    <p class="product-stock">Disponível: <?= $prod['quantidade_estoque'] ?> un.</p>
                </div>

                <!-- Botão de adicionar ao carrinho -->
                <div class="product-action">
                    <a href="carrinho.php?acao=add&id=<?= $prod['id'] ?>" class="btn-primary">
                        🛒 Adicionar ao Carrinho
                    </a>
                </div>

            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="grid-column: 1/-1; text-align: center; color: #666;">Nenhum produto cadastrado no momento.</p>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
