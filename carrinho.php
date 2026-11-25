<?php
// 1. Inclui o arquivo de conexão com o banco de dados ($conn)
require_once "conexao/cnc.php";

// 2. Inicialização da Sessão:
// Verifica se a sessão do usuário já está ativa; se não estiver, inicia a sessão.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Inicialização da Estrutura do Carrinho:
// Se a variável de sessão $_SESSION['carrinho'] ainda não existir, cria um array vazio.
// O carrinho armazena os produtos no formato: [id_do_produto => quantidade]
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = array();
}

// Captura a ação a ser realizada (add, remove, update, limpar) e o ID do produto via GET
$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
$id   = isset($_GET['id']) ? intval($_GET['id']) : 0;


// LÓGICA DO CARRINHO (AÇÕES DO USUÁRIO)

// AÇÃO 1: Adicionar Item ao Carrinho
if ($acao === 'add' && $id > 0) {
    // Se o produto já estiver no carrinho, incrementa a quantidade em +1
    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id] += 1;
    } else {
        // Se for a primeira vez que o item é adicionado, define a quantidade como 1
        $_SESSION['carrinho'][$id] = 1;
    }
    // Redireciona para atualizar a tela e evitar reenviar a ação ao recarregar a página
    header("Location: carrinho.php");
    exit();
}

// AÇÃO 2: Remover Item do Carrinho
if ($acao === 'remove' && $id > 0) {
    // A função unset() remove a chave correspondente ao produto da sessão
    unset($_SESSION['carrinho'][$id]);
    header("Location: carrinho.php");
    exit();
}

// AÇÃO 3: Atualizar Quantidades em Lote (via Formulário POST)
if ($acao === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['qtd']) && is_array($_POST['qtd'])) {
        foreach ($_POST['qtd'] as $prod_id => $quantidade) {
            $prod_id = intval($prod_id);
            $quantidade = intval($quantidade);
            if ($quantidade > 0) {
                // Atualiza a quantidade do produto com o valor digitado no input
                $_SESSION['carrinho'][$prod_id] = $quantidade;
            } else {
                // Se o usuário definir a quantidade como 0 ou menos, remove o item
                unset($_SESSION['carrinho'][$prod_id]);
            }
        }
    }
    header("Location: carrinho.php");
    exit();
}

// AÇÃO 4: Limpar Todo o Carrinho
if ($acao === 'limpar') {
    $_SESSION['carrinho'] = array();
    header("Location: carrinho.php");
    exit();
}

// 4. Reutilização de código: inclui o cabeçalho padrão com a navegação
include "includes/header.php";

$produtos_carrinho = array();
$valor_total = 0;

// 5. Busca dos Dados dos Produtos no Banco:
// Se houver produtos no carrinho da sessão, extrai as chaves (IDs) e busca as informações atualizadas no MySQL
if (!empty($_SESSION['carrinho'])) {
    // implode() junta os IDs do array em uma string separada por vírgula (ex: "1,3,5")
    $ids = implode(',', array_keys($_SESSION['carrinho']));
    
    // Consulta SQL usando a cláusula IN() para trazer apenas os produtos presentes no carrinho
    $sql = "SELECT * FROM produtos WHERE id IN ($ids)";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $produtos_carrinho[] = $row;
        }
    }
}
?>

<div class="card-box" style="max-width: 900px;">
    <h1>🛒 Seu Carrinho de Compras</h1>

    <!-- 6. Exibição Condicional: verifica se o carrinho está vazio -->
    <?php if (empty($produtos_carrinho)): ?>
        <div style="text-align: center; padding: 40px 0;">
            <p style="font-size: 18px; color: #666; margin-bottom: 20px;">Seu carrinho está vazio no momento.</p>
            <a href="index.php" class="btn-primary" style="text-decoration:none; display:inline-block; width:auto; padding: 10px 25px;">Escolher Produtos</a>
        </div>
    <?php else: ?>
        <!-- Form para permitir atualizar as quantidades de múltiplos produtos de uma só vez -->
        <form method="POST" action="carrinho.php?acao=update">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço Unitário</th>
                        <th style="width: 130px;">Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- 7. Percorre os produtos resgatados do banco de dados -->
                    <?php foreach ($produtos_carrinho as $prod): 
                        // Recupera a quantidade selecionada da sessão para o produto atual
                        $qtd = $_SESSION['carrinho'][$prod['id']];
                        
                        // Calcula o subtotal do produto (Preço * Quantidade)
                        $subtotal = $prod['preco'] * $qtd;
                        
                        // Acumula o valor no total geral da compra
                        $valor_total += $subtotal;
                    ?>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 12px;">
                                <img src="<?= htmlspecialchars($prod['imagem_url'] ?: 'img/placeholder.png') ?>" alt="" style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px;">
                                <strong><?= htmlspecialchars($prod['nome']) ?></strong>
                            </td>
                            <td>R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                            <td>
                                <!-- Campo numérico que envia um array de quantidades no formato qtd[id_produto] -->
                                <input type="number" name="qtd[<?= $prod['id'] ?>]" value="<?= $qtd ?>" min="1" max="<?= $prod['quantidade_estoque'] ?>" class="form-control" style="width: 70px; text-align: center; padding: 5px;">
                            </td>
                            <td><strong>R$ <?= number_format($subtotal, 2, ',', '.') ?></strong></td>
                            <td>
                                <!-- Link para remover um produto individual passando ?acao=remove&id=X -->
                                <a href="carrinho.php?acao=remove&id=<?= $prod['id'] ?>" class="btn-danger" style="padding: 4px 8px; font-size: 12px;">✖ Remover</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Barra inferior do carrinho com botão de atualização de quantidades e o Total da Compra -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 15px; background-color: #f3e8ff; border-radius: 8px;">
                <button type="submit" class="btn-voltar" style="margin: 0; cursor: pointer;">🔄 Atualizar Quantidades</button>
                <div style="font-size: 20px; color: #5b164f;">
                    Total da Compra: <strong style="color: #8e245f; font-size: 24px;">R$ <?= number_format($valor_total, 2, ',', '.') ?></strong>
                </div>
            </div>

            <!-- Botões de navegação: continuar comprando ou ir para o checkout/finalização -->
            <div style="display: flex; justify-content: space-between; gap: 15px; margin-top: 25px;">
                <a href="index.php" class="btn-voltar" style="text-align: center;">← Continuar Comprando</a>
                <a href="finalizar.php" class="btn-enviar" style="text-decoration: none; text-align: center; max-width: 300px;">Continuar para Checkout ➔</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php 
// 8. Reutilização de código: inclui o rodapé padrão da página
include "includes/footer.php"; 
?>
