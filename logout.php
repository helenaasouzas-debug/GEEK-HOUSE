<?php
// 1. Inicialização segura da Sessão:
// Verifica se a sessão PHP já está ativa; caso contrário, inicia a sessão
// para que possamos ter acesso às variáveis que serão limpas e destruídas.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Limpeza das Variáveis de Sessão na Memória:
// Esvazia totalmente o array superglobal $_SESSION, removendo dados do usuário logado (ex: $_SESSION['usuario'])
$_SESSION = array();

// 3. Destruição do Cookie da Sessão no Navegador do Cliente:
// Se a sessão utilizar cookies (padrão do PHP via PHPSESSID), altera a data de validade 
// do cookie para o passado (time() - 42000) forçando o navegador a deletá-lo.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Encerramento Definitivo da Sessão no Servidor:
// Destrói todos os dados associados à sessão ativa no servidor web
session_destroy();

// 5. Redirecionamento de Segurança:
// Envia o usuário de volta para a tela de login informando via URL (?msg=deslogado) que a sessão foi encerrada com sucesso
header("Location: /geek_house/login.php?msg=deslogado");

// Interrompe imediatamente a execução do script após o redirecionamento
exit();
?>
