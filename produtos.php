<?php
require_once "conexao/conexao.php";
include "includes/header.php";

$categoria_filtro = (int) ($_GET['categoria'] ?? 0);
$busca            = trim($_GET['busca'] ?? '');

// Monta a consulta de produtos aplicando os filtros ativos
$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        WHERE 1=1";

if ($categoria_filtro > 0) {
    $sql .= " AND p.categoria_id = " . $categoria_filtro;
}

if (!empty($busca)) {
    $busca_escapada = mysqli_real_escape_string($conexao, $busca);
    $sql .= " AND p.nome LIKE '%$busca_escapada%'";
}

$sql .= " ORDER BY p.nome ASC";
$resultado = mysqli_query($conexao, $sql);

// Busca as categorias cadastradas no banco para os botões de filtro
$categorias_query = mysqli_query($conexao, "SELECT * FROM categorias ORDER BY nome ASC");
?>

<?php if (isset($_GET['erro']) && $_GET['erro'] === 'sem_permissao'): ?>
    <div class="alert alert-danger" style="background-color: #fef2f2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
        ⚠️ Acesso negado! Você precisa ser um administrador para acessar essa área.
    </div>
<?php endif; ?>

<h1 class="titulo-pagina" style="text-align: center; color: #5b164f; margin-bottom: 10px;">🏪 Colecionáveis Geek House</h1>
<p style="text-align: center; color: #666; margin-bottom: 25px;">Explore nosso catálogo de mangás, action figures, HQs e cartinhas Pokémon!</p>

<!-- Campo de Busca por Texto -->
<div class="card-filtro" style="margin-bottom: 20px;">
    <form method="GET" action="produtos.php" class="form-busca-inline" style="display: flex; gap: 10px; max-width: 600px; margin: 0 auto 20px auto;">
        <input type="text" name="busca" placeholder="Buscar mangá, figure, pokémon..." value="<?= htmlspecialchars($busca) ?>" style="flex: 1; padding: 10px 15px; border: 1px solid #7c226a; border-radius: 8px;">
        <?php if ($categoria_filtro > 0): ?>
            <input type="hidden" name="categoria" value="<?= $categoria_filtro ?>">
        <?php endif; ?>
        <button type="submit" class="btn-enviar" style="margin: 0; width: auto; padding: 10px 20px; background-color: #7c226a; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Buscar</button>
    </form>
</div>

<!-- Barra de Filtros por Categoria (Pills/Botões) -->
<div class="filter-bar">
    <a href="produtos.php<?= !empty($busca) ? '?busca=' . urlencode($busca) : '' ?>" 
       class="filter-btn <?= $categoria_filtro === 0 ? 'active' : '' ?>">
        ✨ Todos os Produtos
    </a>
    <?php if ($categorias_query && mysqli_num_rows($categorias_query) > 0): ?>
        <?php while ($cat = mysqli_fetch_assoc($categorias_query)): 
            $url_cat = "produtos.php?categoria=" . $cat['id'];
            if (!empty($busca)) {
                $url_cat .= "&busca=" . urlencode($busca);
            }
        ?>
            <a href="<?= $url_cat ?>" 
               class="filter-btn <?= $categoria_filtro == $cat['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['nome']) ?>
            </a>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<!-- Grade de Produtos no Estilo Google Shopping -->
<div class="products-grid">
    <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
        <?php while ($prod = mysqli_fetch_assoc($resultado)): ?>
            <div class="product-card">
                <div class="product-thumb">
                    <img src="<?= htmlspecialchars($prod['imagem_url'] ?: 'https://via.placeholder.com/300x300?text=Geek+House') ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                    <span class="product-category"><?= htmlspecialchars($prod['categoria_nome']) ?></span>
                </div>
                
                <div class="product-info">
                    <div>
                        <h3 class="product-title"><?= htmlspecialchars($prod['nome']) ?></h3>
                        <p class="product-brand"><?= htmlspecialchars($prod['editora_fabricante'] ?? 'Geek') ?> <?= !empty($prod['especificacao']) ? '• ' . htmlspecialchars($prod['especificacao']) : '' ?></p>
                    </div>
                    
                    <div class="product-bottom">
                        <div class="product-price">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                        <p class="product-stock">Estoque: <?= (int)$prod['quantidade_estoque'] ?> un.</p>
                        
                        <div class="card-acoes" style="display: flex; gap: 8px; margin-top: 10px;">
                            <a href="detalhe.php?id=<?= $prod['id'] ?>" class="btn-detalhes" style="flex: 1; text-align: center; padding: 8px; background-color: #d8b4e8; color: #42104a; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">Detalhes</a>
                            <a href="carrinho.php?acao=add&id=<?= $prod['id'] ?>" class="btn-comprar" style="flex: 1.2; text-align: center; padding: 8px; background-color: #7c226a; color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">+ Carrinho</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-products" style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 12px; border: 1px dashed #7c226a; color: #5b164f;">
            <p>Nenhum produto encontrado com os filtros selecionados.</p>
            <a href="produtos.php" class="filter-btn active" style="margin-top: 15px; display: inline-block;">Ver todos os produtos</a>
        </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
