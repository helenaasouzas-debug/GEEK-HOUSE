<?php
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";

// Garante acesso restrito a administradores
verificar_admin();

include "../includes/header.php";

$sql_vendas = "SELECT v.*, u.nome AS cliente_nome, u.email AS cliente_email 
               FROM vendas v 
               INNER JOIN usuarios u ON v.usuario_id = u.id 
               ORDER BY v.id DESC";
$result_vendas = $conn->query($sql_vendas);
?>

<div class="card-box" style="max-width: 1000px;">
    <h1>📦 Gestão de Pedidos dos Clientes</h1>
    <p style="text-align: center; color: #666; margin-bottom: 25px;">Acompanhe as vendas realizadas na Geek House em tempo real.</p>

    <?php if ($result_vendas && $result_vendas->num_rows > 0): ?>
        <?php while ($venda = $result_vendas->fetch_assoc()): ?>
            <div style="border: 1px solid #d8b4fe; border-radius: 10px; padding: 20px; margin-bottom: 20px; background-color: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f3e8ff; padding-bottom: 12px; margin-bottom: 15px;">
                    <div>
                        <span style="font-size: 18px; font-weight: bold; color: #5b164f;">Pedido #<?= $venda['id'] ?></span>
                        <span style="font-size: 13px; color: #777; margin-left: 10px;">
                            📅 <?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?>
                        </span>
                    </div>
                    <div>
                        <span style="background-color: #f3e8ff; color: #7c3aed; font-weight: bold; padding: 5px 12px; border-radius: 15px; font-size: 13px;">
                            💳 <?= htmlspecialchars($venda['forma_pagamento']) ?>
                        </span>
                    </div>
                </div>

                <p style="font-size: 14px; color: #333; margin-bottom: 15px;">
                    👤 <strong>Cliente:</strong> <?= htmlspecialchars($venda['cliente_nome']) ?> (<em><?= htmlspecialchars($venda['cliente_email']) ?></em>)
                </p>

                <?php
                $stmt_itens = $conn->prepare("SELECT iv.*, p.nome AS produto_nome, p.imagem_url 
                                               FROM itens_venda iv 
                                               INNER JOIN produtos p ON iv.produto_id = p.id 
                                               WHERE iv.venda_id = ?");
                $stmt_itens->bind_param("i", $venda['id']);
                $stmt_itens->execute();
                $result_itens = $stmt_itens->get_result();
                ?>

                <table class="table-custom" style="margin-top: 10px; font-size: 14px;">
                    <thead>
                        <tr style="background-color: #7c3aed;">
                            <th>Produto</th>
                            <th style="width: 100px; text-align: center;">Qtd</th>
                            <th style="width: 130px;">Preço Unit.</th>
                            <th style="width: 130px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $result_itens->fetch_assoc()): 
                            $subtotal = $item['quantidade'] * $item['preco_unitario'];
                        ?>
                            <tr>
                                <td style="display: flex; align-items: center; gap: 10px;">
                                    <img src="../<?= htmlspecialchars($item['imagem_url'] ?: 'img/placeholder.png') ?>" alt="" style="width: 35px; height: 35px; object-fit: cover; border-radius: 5px;">
                                    <strong><?= htmlspecialchars($item['produto_nome']) ?></strong>
                                </td>
                                <td style="text-align: center;"><?= $item['quantidade'] ?>x</td>
                                <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                                <td><strong>R$ <?= number_format($subtotal, 2, ',', '.') ?></strong></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div style="text-align: right; margin-top: 15px; font-size: 16px; color: #5b164f;">
                    Total do Pedido: <strong style="color: #8e245f; font-size: 20px;">R$ <?= number_format($venda['valor_total'], 2, ',', '.') ?></strong>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 40px 0; color: #666;">
            <p style="font-size: 16px;">Nenhum pedido foi realizado ainda.</p>
        </div>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 25px;">
        <a href="index.php" class="btn-voltar">← Voltar para o Painel Admin</a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
