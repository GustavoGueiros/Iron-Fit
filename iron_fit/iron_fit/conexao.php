<?php

$conn = new mysqli("sql306.infinityfree.com", "if0_42679061", "3szvIJV67iEvDX", "if0_42679061_academia"); 

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
date_default_timezone_set("America/Sao_Paulo");