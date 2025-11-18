<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "codgotemp";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>