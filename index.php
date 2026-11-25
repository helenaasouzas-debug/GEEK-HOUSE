<?php
// 1. Inclui a conexão com o banco de dados MySQL ($conn)
require_once "conexao/cnc.php";

// 2. Reutilização de código: inclui o cabeçalho padrão (carrega a sessão, o menu e a contagem do carrinho)
include "includes/header.php";

// 3. Consulta SQL com INNER JOIN:
// Seleciona todos os produtos (p.*) e traz o nome da categoria associada (c.nome AS categoria_nome)
// unindo a tabela 'produtos' com a tabela 'categorias' onde p.categoria_id = c.id.
// O 'ORDER BY p.id DESC' garante que os produtos lançados mais recentemente apareçam primeiro na vitrine.
$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>

<!-- 4. Tratamento de Erro de Permissão via GET:
     Se um usuário tentar acessar uma página restrita (como o painel admin) sem autorização, 
     o sistema o redireciona para cá com ?erro=sem_permissao e este alerta é exibido. -->
<?php if (isset($_GET['erro']) && $_GET['erro'] === 'sem_permissao'): ?>
    <div class="alert alert-danger">⚠️ Acesso negado! Você precisa ser um administrador para acessar essa área.</div>
<?php endif; ?>

<h1 style="color: #5b164f; text-align:center; margin-bottom: 10px;">🏪 Colecionáveis Geek House</h1>
<p style="text-align:center; color: #666; margin-bottom: 25px;">Explore nosso catálogo de mangás, action figures, HQs e vestuário geek!</p>

<!-- Container principal da grade de produtos da loja -->
<div class="products-grid">
    <!-- 5. Verifica se a consulta retornou produtos cadastrados -->
    <?php if ($result && $result->num_rows > 0): ?>
        <!-- 6. Estrutura de repetição (while): percorre cada produto e monta o seu card visual na vitrine -->
        <?php while ($prod = $result->fetch_assoc()): ?>
            <div class="product-card">
                <div>
                    <!-- Imagem do produto com fallback caso o caminho esteja vazio -->
                    <img src="<?= htmlspecialchars($prod['imagem_url'] ?: 'img/placeholder.png') ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                    
                    <!-- Categoria e Nome do produto sanitizados com htmlspecialchars() para evitar XSS -->
                    <span class="product-category"><?= htmlspecialchars($prod['categoria_nome']) ?></span>
                    <h3 class="product-title"><?= htmlspecialchars($prod['nome']) ?></h3>
                    <p style="font-size: 13px; color: #666; margin-bottom: 10px;"><?= htmlspecialchars($prod['editora_fabricante'] ?: 'Geek') ?></p>
                </div>
                <div>
                    <!-- Preço formatado no padrão de moeda brasileira (R$ 0,00) -->
                    <div class="product-price">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                    <p style="font-size: 12px; color: #888; margin-bottom: 10px;">Estoque: <?= $prod['quantidade_estoque'] ?> un.</p>
                    
                    <!-- Link de ação enviando a instrução ?acao=add&id=X para o carrinho.php -->
                    <a href="carrinho.php?acao=add&id=<?= $prod['id'] ?>" class="btn-primary" style="text-decoration:none; display:block; text-align:center;">
                        🛒 Adicionar ao Carrinho
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <!-- Mensagem de fallback caso não existam itens cadastrados na base de dados -->
        <p style="text-align:center; grid-column: 1/-1;">Nenhum produto cadastrado no momento.</p>
    <?php endif; ?>
</div>

<?php 
// 7. Reutilização de código: inclui o rodapé padrão da página
include "includes/footer.php"; 
?>
