<?php
require_once "conexao/cnc.php";
require_once "conexao/auth.php";

verificar_login();

$mensagem_sucesso = false;
$pedido_id = 0;

if (empty($_SESSION['carrinho']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$produtos_compra = array();
$valor_total = 0;

if (!empty($_SESSION['carrinho'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['carrinho'])));
    if (!empty($ids)) {
        $result = $conn->query("SELECT * FROM produtos WHERE id IN ($ids)");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $produtos_compra[$row['id']] = $row;
                $qtd = $_SESSION['carrinho'][$row['id']];
                $valor_total += ($row['preco'] * $qtd);
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($produtos_compra)) {
    $rua             = trim($_POST['rua'] ?? '');
    $numero          = trim($_POST['numero'] ?? '');
    $bairro          = trim($_POST['bairro'] ?? '');
    $cidade          = trim($_POST['cidade'] ?? '');
    $estado          = trim($_POST['estado'] ?? '');
    $cep             = trim($_POST['cep'] ?? '');
    $forma_pagamento = trim($_POST['forma_pagamento'] ?? '');
    $usuario_id      = $_SESSION['usuario']['id'];

    if (!empty($rua) && !empty($cidade) && !empty($forma_pagamento)) {
        
        $stmt_venda = $conn->prepare("INSERT INTO vendas (usuario_id, valor_total, forma_pagamento, data_venda) VALUES (?, ?, ?, NOW())");
        $stmt_venda->bind_param("ids", $usuario_id, $valor_total, $forma_pagamento);
        
        if ($stmt_venda->execute()) {
            $pedido_id = $conn->insert_id;

            $stmt_item = $conn->prepare("INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
            $stmt_estoque = $conn->prepare("UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?");

            foreach ($_SESSION['carrinho'] as $prod_id => $qtd) {
                if (isset($produtos_compra[$prod_id])) {
                    $prod = $produtos_compra[$prod_id];
                    $preco_unit = $prod['preco'];
                    
                    $stmt_item->bind_param("iiid", $pedido_id, $prod_id, $qtd, $preco_unit);
                    $stmt_item->execute();

                    $stmt_estoque->bind_param("ii", $qtd, $prod_id);
                    $stmt_estoque->execute();
                }
            }

            $_SESSION['carrinho'] = array();
            $mensagem_sucesso = true;
        }
    }
}

include "includes/header.php";
?>

<div class="card-box" style="max-width: 750px;">
    <?php if ($mensagem_sucesso): ?>
        <div style="text-align: center; padding: 20px;">
            <h1 style="color: #166534; margin-bottom: 15px;">🎉 Pedido #<?= $pedido_id ?> Realizado com Sucesso!</h1>
            <p style="font-size: 16px; color: #444; margin-bottom: 20px;">
                Obrigado por comprar na <strong>Geek House</strong>, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?>!
            </p>
            <div class="alert alert-success" style="display:inline-block; text-align:left; width: 100%;">
                <p><strong>Forma de Pagamento:</strong> <?= htmlspecialchars($_POST['forma_pagamento'] ?? '') ?></p>
                <p><strong>Total Pago:</strong> R$ <?= number_format($valor_total, 2, ',', '.') ?></p>
                <p><strong>Endereço de Entrega:</strong> <?= htmlspecialchars($_POST['rua'] ?? '') ?>, nº <?= htmlspecialchars($_POST['numero'] ?? '') ?> - <?= htmlspecialchars($_POST['cidade'] ?? '') ?>/<?= htmlspecialchars($_POST['estado'] ?? '') ?></p>
            </div>
            <br><br>
            <a href="index.php" class="btn-primary" style="text-decoration:none; display:inline-block; width:auto; padding: 12px 30px;">Voltar para a Loja</a>
        </div>
    <?php else: ?>
        <h1>💳 Finalizar Pedido</h1>
        
        <div style="background-color: #faf5ff; padding: 15px; border-radius: 8px; border: 1px solid #d8b4fe; margin-bottom: 25px;">
            <h3 style="color: #5b164f; margin-bottom: 10px;">Resumo dos Itens</h3>
            <?php foreach ($produtos_compra as $prod_id => $prod): 
                $qtd = $_SESSION['carrinho'][$prod_id] ?? 0;
            ?>
                <div style="display:flex; justify-content:space-between; font-size:14px; margin-bottom: 5px;">
                    <span><?= $qtd ?>x <?= htmlspecialchars($prod['nome']) ?></span>
                    <strong>R$ <?= number_format($prod['preco'] * $qtd, 2, ',', '.') ?></strong>
                </div>
            <?php endforeach; ?>
            <hr style="border:0; border-top: 1px solid #d8b4fe; margin: 10px 0;">
            <div style="display:flex; justify-content:space-between; font-size:18px; color: #5b164f;">
                <span>Total a Pagar:</span>
                <strong>R$ <?= number_format($valor_total, 2, ',', '.') ?></strong>
            </div>
        </div>

        <form method="POST" action="finalizar.php">
            <h3 style="color: #5b164f; margin-bottom: 15px;">📍 Endereço de Entrega</h3>
            
            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 3;">
                    <label for="rua">Rua / Avenida *</label>
                    <input type="text" id="rua" name="rua" class="form-control" required placeholder="Ex: Rua dos Bobos">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="numero">Número *</label>
                    <input type="text" id="numero" name="numero" class="form-control" required placeholder="123">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" name="bairro" class="form-control" placeholder="Centro">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="cidade">Cidade *</label>
                    <input type="text" id="cidade" name="cidade" class="form-control" required placeholder="São Paulo">
                </div>
                <div class="form-group" style="width: 80px;">
                    <label for="estado">UF *</label>
                    <input type="text" id="estado" name="estado" class="form-control" required maxlength="2" placeholder="SP">
                </div>
            </div>

            <div class="form-group">
                <label for="cep">CEP *</label>
                <input type="text" id="cep" name="cep" class="form-control" required placeholder="00000-000">
            </div>

            <h3 style="color: #5b164f; margin-top: 25px; margin-bottom: 15px;">💰 Forma de Pagamento</h3>
            
            <div class="form-group">
                <select name="forma_pagamento" id="forma_pagamento" class="form-control" required>
                    <option value="">Selecione como deseja pagar...</option>
                    <option value="Pix">⚡ Pix (Aprovação Instantânea)</option>
                    <option value="Cartão de Crédito">💳 Cartão de Crédito</option>
                    <option value="Boleto Bancário">📄 Boleto Bancário</option>
                </select>
            </div>

            <button type="submit" class="btn-enviar" style="margin-top: 15px;">Confirmar e Pagar R$ <?= number_format($valor_total, 2, ',', '.') ?></button>
            <div style="text-align: center;">
                <a href="carrinho.php" class="btn-voltar">Voltar para o Carrinho</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
