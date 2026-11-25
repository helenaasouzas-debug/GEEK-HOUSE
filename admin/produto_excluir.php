<?php
// 1. Inclui o arquivo de conexão com o banco de dados ($conn) e as funções de autenticação
require_once "../conexao/cnc.php";
require_once "../conexao/auth.php";

// 2. Trava de segurança: impede que usuários comuns ou não autenticados excluam registros via URL
verificar_admin();

// 3. Captura o ID do produto enviado pela URL (ex: produto_excluir.php?id=3)
// Usamos intval() para garantir que o valor seja tratado estritamente como um número inteiro
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 4. Executa a exclusão apenas se o ID for maior que zero (válido)
if ($id > 0) {
    // 5. Prepared Statement (Consulta Preparada):
    // Prepara a instrução SQL DELETE com a interrogação (?) para evitar SQL Injection
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    
    // Vincula o parâmetro como número inteiro ("i")
    $stmt->bind_param("i", $id);
    
    // Executa o comando de exclusão no MySQL
    $stmt->execute();
}

// 6. Redireciona imediatamente o usuário de volta para a lista de produtos com um aviso na URL
header("Location: produtos_listar.php?msg=excluido");
exit();
?>
