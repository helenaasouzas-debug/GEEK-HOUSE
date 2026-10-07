<?php
define('IN_ADMIN', true);
require_once "../conexao/auth.php";
verificar_admin();

require_once "../conexao/conexao.php";

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $sql  = "DELETE FROM produtos WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: produtos_listar.php?mensagem=Produto+exclu%C3%ADdo+com+sucesso!");
    } else {
        header("Location: produtos_listar.php?erro=Erro+ao+excluir+produto:+possui+vendas+vinculadas.");
    }
} else {
    header("Location: produtos_listar.php?erro=ID+inv%C3%A1lido!");
}
exit;
?>
