<?php
// Conexão e trava de segurança
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";
verificar_admin();

// Captura do ID via GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // 1. Remove dependências na tabela itens_venda
    $stmt_itens = $conn->prepare("DELETE FROM itens_venda WHERE produto_id = ?");
    $stmt_itens->bind_param("i", $id);
    $stmt_itens->execute();

    // 2. Remove o produto da tabela produtos
    $stmt_prod = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt_prod->bind_param("i", $id);
    $stmt_prod->execute();
}

// Redireciona com confirmação
header("Location: produtos_listar.php?msg=excluido");
exit();
?>
