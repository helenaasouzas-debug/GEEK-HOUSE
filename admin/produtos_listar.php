<?php
// 1. Inclui a conexão com o banco de dados ($conn) e os controles de autenticação/sessão
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";

// 2. Trava de segurança: impede o acesso de usuários não autorizados ou clientes comuns
verificar_admin();

// 3. Consulta SQL com INNER JOIN:
// Seleciona todos os dados dos produtos (p.*) e o nome da categoria relacionada (c.nome AS categoria_nome).
// O INNER JOIN une 'produtos' e 'categorias' onde p.categoria_id = c.id.
// O 'ORDER BY p.id DESC' exibe os últimos produtos cadastrados no topo da tabela.
$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);

// 4. Reutilização de código: inclui o cabeçalho e menu padrão do sistema
include "../includes/header.php";
?>

<div class="card-box">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1>📋 Gerenciamento de Produtos</h1>
        <a href="produto_cadastrar.php" class="btn-enviar" style="width:auto; text-decoration:none;">+ Novo Produto</a>
    </div>

    <!-- 5. Feedback visual ao usuário:
         Verifica os parâmetros enviados pela URL via GET ($_GET['msg']) 
         e exibe a mensagem de sucesso correspondente a cada ação (cadastro, edição ou exclusão). -->
    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'cadastrado'): ?>
            <div class="alert alert-success">✅ Produto cadastrado com sucesso!</div>
        <?php elseif ($_GET['msg'] === 'atualizado'): ?>
            <div class="alert alert-success">✅ Produto atualizado com sucesso!</div>
        <?php elseif ($_GET['msg'] === 'excluido'): ?>
            <div class="alert alert-success">🗑️ Produto excluído com sucesso!</div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- 6. Tabela de exibição do acervo de produtos -->
    <table class="table-custom">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Produto</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- 7. Verifica se a consulta SQL retornou pelo menos um produto cadastrado -->
            <?php if ($result && $result->num_rows > 0): ?>
                <!-- 8. Laço de repetição (while): percorre cada linha de produto retornada pelo MySQL -->
                <?php while ($prod = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?= $prod['id'] ?></strong></td>
                        <!-- htmlspecialchars(): trata caracteres especiais para prevenir ataques XSS -->
                        <td><?= htmlspecialchars($prod['nome']) ?></td>
                        <td><?= htmlspecialchars($prod['categoria_nome']) ?></td>
                        <!-- number_format(): converte o valor numérico para o padrão de moeda brasileira (R$ 0,00) -->
                        <td>R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                        <td><?= $prod['quantidade_estoque'] ?></td>
                        <td>
                            <!-- Link enviando o ID do produto via parâmetro GET para edição -->
                            <a href="produto_editar.php?id=<?= $prod['id'] ?>" class="btn-edit">Editar</a>
                            
                            <!-- Link de exclusão com caixa de confirmação em JavaScript (confirm) antes de executar -->
                            <a href="produto_excluir.php?id=<?= $prod['id'] ?>" class="btn-danger" onclick="return confirm('Deseja realmente excluir este produto?')">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Mensagem padrão exibida se a tabela no banco estiver vazia -->
                <tr>
                    <td colspan="6" style="text-align:center;">Nenhum produto cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// 9. Reutilização de código: inclui o rodapé padrão do portal
include "../includes/footer.php"; 
?>
