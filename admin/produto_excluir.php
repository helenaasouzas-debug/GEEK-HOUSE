<?php
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";
verificar_admin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: produtos_listar.php?msg=excluido");
exit();
?>
