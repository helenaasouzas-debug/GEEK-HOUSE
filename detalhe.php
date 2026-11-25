<?php
// 1. Inclui o arquivo de conexão procedimental com o banco de dados ($conexao)
require_once "conexao/conexao.php";

// 2. Captura e valida o ID recebido pela URL (ex: detalhe.php?id=5)
// O operador (int) converte o parâmetro para número inteiro por segurança
$id = (int) ($_GET['id'] ?? 0);

// 3. Prepared Statement (Consulta Preparada Procedimental):
// Busca os detalhes do produto relacionando com a tabela categorias via INNER JOIN.
// O uso do marcador '?' impede ataques de Injeção de SQL (SQL Injection).
$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.id = ? LIMIT 1";

$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $id); // "i" especifica que o parâmetro é um inteiro
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$produto = mysqli_fetch_assoc($resultado);

// 4. Trava e Redirecionamento de Segurança:
// Se o produto não existir no banco de dados ou o ID for inválido, redireciona para a vitrine
if (!$produto) {
    header("Location: produtos.php");
    exit;
}

// 5. Reutilização de código: inclui o cabeçalho padrão com o menu de navegação
include "includes/header.php";
?>

<div class="card-detalhe-produto">
    <!-- Imagem do produto com fallback (imagem genérica caso o produto não tenha imagem salva) -->
    <div class="detalhe-imagem">
        <img src="<?= htmlspecialchars($produto['imagem_url'] ?: 'https://via.placeholder.com/400x400?text=Geek+House') ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
    </div>

    <div class="detalhe-info">
        <!-- Categoria e Nome Sanitizados com htmlspecialchars() contra ataques XSS -->
        <span class="categoria-tag"><?= htmlspecialchars($produto['categoria_nome']) ?></span>
        <h1><?= htmlspecialchars($produto['nome']) ?></h1>
        
        <!-- Formatação da moeda para o padrão brasileiro (R$ 0,00) -->
        <p class="preco-detalhe">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
        
        <!-- Lista com os detalhes técnicos e a quantidade em estoque -->
        <ul class="lista-especificacoes">
            <li><strong>Editora / Fabricante:</strong> <?= htmlspecialchars($produto['editora_fabricante'] ?: 'Não informado') ?></li>
            <li><strong>Especificação:</strong> <?= htmlspecialchars($produto['especificacao'] ?: 'N/A') ?></li>
            <li><strong>Condição:</strong> <?= htmlspecialchars($produto['condicao']) ?></li>
            <li><strong>Estoque Disponível:</strong> <?= $produto['quantidade_estoque'] ?> unidades</li>
        </ul>

        <!-- nl2br() converte as quebras de linha digitadas na descrição em marcas <br> no HTML -->
        <p class="descricao"><?= nl2br(htmlspecialchars($produto['descricao'] ?: 'Sem descrição informada.')) ?></p>

        <!-- 6. Validação de Estoque Dinâmica:
             Exibe o botão de compra apenas se o estoque for maior que zero -->
        <?php if ($produto['quantidade_estoque'] > 0): ?>
            <a href="carrinho.php?acao=add&id=<?= $produto['id'] ?>" class="btn-enviar" style="display:inline-block; text-align:center; text-decoration:none;">Adicionar ao Carrinho</a>
        <?php else: ?>
            <p class="erro">Produto Indisponível no Momento</p>
        <?php endif; ?>
        
        <br><br>
        <a href="produtos.php" class="btn-voltar">← Voltar para o Catálogo</a>
    </div>
</div>

<?php 
// 7. Reutilização de código: inclui o rodapé padrão da página
include "includes/footer.php"; 
?>
