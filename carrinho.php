<?php
require_once "conexao/cnc.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = array();
}

$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
$id   = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($acao === 'add' && $id > 0) {
    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id] += 1;
    } else {
        $_SESSION['carrinho'][$id] = 1;
    }
    header("Location: carrinho.php");
    exit();
}

if ($acao === 'remove' && $id > 0) {
    unset($_SESSION['carrinho'][$id]);
    header("Location: carrinho.php");
    exit();
}

if ($acao === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['qtd']) && is_array($_POST['qtd'])) {
        foreach ($_POST['qtd'] as $prod_id => $quantidade) {
            $prod_id = intval($prod_id);
            $quantidade = intval($quantidade);
            if ($quantidade > 0) {
                $_SESSION['carrinho'][$prod_id] = $quantidade;
            } else {
                unset($_SESSION['carrinho'][$prod_id]);
            }
        }
    }
    header("Location: carrinho.php");
    exit();
}

if ($acao === 'limpar') {
    $_SESSION['carrinho'] = array();
    header("Location: carrinho.php");
    exit();
}

include "includes/header.php";

$produtos_carrinho = array();
$valor_total = 0;

if (!empty($_SESSION['carrinho'])) {
    $ids = implode(',', array_keys($_SESSION['carrinho']));
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

    <?php if (empty($produtos_carrinho)): ?>
        <div style="text-align: center; padding: 40px 0;">
            <p style="font-size: 18px; color: #666; margin-bottom: 20px;">Seu carrinho está vazio no momento.</p>
            <a href="index.php" class="btn-primary" style="text-decoration:none; display:inline-block; width:auto; padding: 10px 25px;">Escolher Produtos</a>
        </div>
    <?php else: ?>
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
                    <?php foreach ($produtos_carrinho as $prod): 
                        $qtd = $_SESSION['carrinho'][$prod['id']];
                        $subtotal = $prod['preco'] * $qtd;
                        $valor_total += $subtotal;
                    ?>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 12px;">
                                <img src="<?= htmlspecialchars($prod['imagem_url'] ?: 'img/placeholder.png') ?>" alt="" style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px;">
                                <strong><?= htmlspecialchars($prod['nome']) ?></strong>
                            </td>
                            <td>R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                            <td>
                                <input type="number" name="qtd[<?= $prod['id'] ?>]" value="<?= $qtd ?>" min="1" max="<?= $prod['quantidade_estoque'] ?>" class="form-control" style="width: 70px; text-align: center; padding: 5px;">
                            </td>
                            <td><strong>R$ <?= number_format($subtotal, 2, ',', '.') ?></strong></td>
                            <td>
                                <a href="carrinho.php?acao=remove&id=<?= $prod['id'] ?>" class="btn-danger" style="padding: 4px 8px; font-size: 12px;">✖ Remover</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 15px; background-color: #f3e8ff; border-radius: 8px;">
                <button type="submit" class="btn-voltar" style="margin: 0; cursor: pointer;">🔄 Atualizar Quantidades</button>
                <div style="font-size: 20px; color: #5b164f;">
                    Total da Compra: <strong style="color: #8e245f; font-size: 24px;">R$ <?= number_format($valor_total, 2, ',', '.') ?></strong>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; gap: 15px; margin-top: 25px;">
                <a href="index.php" class="btn-voltar" style="text-align: center;">← Continuar Comprando</a>
                <a href="finalizar.php" class="btn-enviar" style="text-decoration: none; text-align: center; max-width: 300px;">Continuar para Checkout ➔</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
