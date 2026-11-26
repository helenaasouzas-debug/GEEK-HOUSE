<?php
// 1. Inclui o arquivo de conexão procedimental com o banco de dados ($conexao)
require_once "conexao/conexao.php";

// 2. Reutilização de código: inclui o cabeçalho padrão com o menu e inicialização da sessão
include "includes/header.php";

// 3. Captura e tratamento dos parâmetros de busca e filtro passados pela URL (método GET)
$categoria_filtro = (int) ($_GET['categoria'] ?? 0);
$busca            = trim($_GET['busca'] ?? '');

// 4. Construção Dinâmica da Consulta SQL:
$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        WHERE 1=1";

// Filtro por Categoria: se selecionado, filtra pelo ID da categoria
if ($categoria_filtro > 0) {
    $sql .= " AND p.categoria_id = " . $categoria_filtro;
}

// Filtro por Termo de Busca: se digitado algo no campo de busca, pesquisa pelo nome do produto
if (!empty($busca)) {
    // mysqli_real_escape_string(): escapa caracteres especiais no texto para prevenir SQL Injection
    $busca_escapada = mysqli_real_escape_string($conexao, $busca);
    $sql .= " AND p.nome LIKE '%$busca_escapada%'";
}

// Ordenação alfabética pelo nome do produto
$sql .= " ORDER BY p.nome ASC";
$resultado = mysqli_query($conexao, $sql);

// 5. Busca as categorias cadastradas para renderizar os botões/pills de navegação
$categorias_query = mysqli_query($conexao, "SELECT * FROM categorias ORDER BY nome ASC");
?>

<!-- Exibe aviso de acesso negado caso um usuário comum tente acessar o painel de administrador -->
<?php if (isset($_GET['erro']) && $_GET['erro'] === 'sem_permissao'): ?>
    <div class="alert alert-danger" style="background-color: #fef2f2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
        ⚠️ Acesso negado! Você precisa ser um administrador para acessar essa área.
    </div>
<?php endif; ?>

<h1 class="titulo-pagina" style="text-align: center; color: #5b164f; margin-bottom: 10px;">🏪 Colecionáveis Geek House</h1>
<p style="text-align: center; color: #666; margin-bottom: 25px;">Explore nosso catálogo de mangás, action figures, HQs e cartinhas Pokémon!</p>

<!-- 6. Campo de Busca por Texto (Formulário GET):
     Mantém a categoria ativa salva em um input hidden caso o usuário busque texto dentro de uma categoria específica -->
<div class="card-filtro" style="margin-bottom: 20px;">
    <form method="GET" action="produtos.php" class="form-busca-inline" style="display: flex; gap: 10px; max-width: 600px; margin: 0 auto 20px auto;">
        <input type="text" name="busca" placeholder="Buscar mangá, figure, pokémon..." value="<?= htmlspecialchars($busca) ?>" style="flex: 1; padding: 10px 15px; border: 1px solid #7c226a; border-radius: 8px;">
        
        <?php if ($categoria_filtro > 0): ?>
            <input type="hidden" name="categoria" value="<?= $categoria_filtro ?>">
        <?php endif; ?>
        
        <button type="submit" class="btn-enviar" style="margin: 0; width: auto; padding: 10px 20px; background-color: #7c226a; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Buscar</button>
    </form>
</div>

<!-- 7. Barra de Filtros por Categoria (Botões Dinâmicos):
     Preserva a palavra buscada na URL (via urlencode) ao alternar entre categorias -->
<div class="filter-bar">
    <a href="produtos.php<?= !empty($busca) ? '?busca=' . urlencode($busca) : '' ?>" 
       class="filter-btn <?= $categoria_filtro === 0 ? 'active' : '' ?>">
        Todos os Produtos
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

<!-- 8. Grade de Exibição dos Produtos (Catálogo Estilo Loja Virtual) -->
<div class="products-grid">
    <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
        <?php while ($prod = mysqli_fetch_assoc($resultado)): ?>
            <div class="product-card">
                <div class="product-thumb">
                    <!-- Imagem do produto com foto genérica de fallback se o link for nulo -->
                    <img src="<?= htmlspecialchars($prod['imagem_url'] ?: 'https://via.placeholder.com/300x300?text=Geek+House') ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                    <span class="product-category"><?= htmlspecialchars($prod['categoria_nome']) ?></span>
                </div>
                
                <div class="product-info">
                    <div>
                        <h3 class="product-title"><?= htmlspecialchars($prod['nome']) ?></h3>
                        <p class="product-brand"><?= htmlspecialchars($prod['editora_fabricante'] ?? 'Geek') ?> <?= !empty($prod['especificacao']) ? '• ' . htmlspecialchars($prod['especificacao']) : '' ?></p>
                    </div>
                    
                    <div class="product-bottom">
                        <!-- Formatação do preço em moeda nacional (R$ 0,00) -->
                        <div class="product-price">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                        <p class="product-stock">Estoque: <?= (int)$prod['quantidade_estoque'] ?> un.</p>
                        
                        <!-- Ações do Card: Ver Detalhes Completos ou Adicionar Direto ao Carrinho -->
                        <div class="card-acoes" style="display: flex; gap: 8px; margin-top: 10px;">
                            <a href="detalhe.php?id=<?= $prod['id'] ?>" class="btn-detalhes" style="flex: 1; text-align: center; padding: 8px; background-color: #d8b4e8; color: #42104a; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">Detalhes</a>
                            <a href="carrinho.php?acao=add&id=<?= $prod['id'] ?>" class="btn-comprar" style="flex: 1.2; text-align: center; padding: 8px; background-color: #7c226a; color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">+ Carrinho</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <!-- Bloco de mensagem amigável exibido quando nenhum produto atende aos filtros de busca -->
        <div class="no-products" style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 12px; border: 1px dashed #7c226a; color: #5b164f;">
            <p>Nenhum produto encontrado com os filtros selecionados.</p>
            <a href="produtos.php" class="filter-btn active" style="margin-top: 15px; display: inline-block;">Ver todos os produtos</a>
        </div>
    <?php endif; ?>
</div>

<?php 
// 9. Reutilização de código: inclui o rodapé padrão da página
include "includes/footer.php"; 
?>
