<?php
// Conexão com o banco de dados MySQL
$host = "localhost";
$user = "root";
$pass = "";
$db   = "loja_geek";

$conn = new mysqli($host, $user, $pass, $db);

// Verificação de erro de conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
