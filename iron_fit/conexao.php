<?php
// Abre a conexão MySQL e garante as tabelas necessárias ao sistema de treinos.


// Configuração local usada pelo Laragon.
 $conn = new mysqli("localhost", "root", "", "academia");



if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
date_default_timezone_set("America/Sao_Paulo");

// Atualiza instalações antigas para aceitar o papel de funcionário.
$conn->query("ALTER TABLE usuarios MODIFY tipo ENUM('admin', 'funcionario', 'cliente') NOT NULL DEFAULT 'cliente'");
$conn->query("CREATE TABLE IF NOT EXISTS treinos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    funcionario_id INT DEFAULT NULL,
    dia_semana ENUM('segunda','terca','quarta','quinta','sexta','sabado','domingo') NOT NULL,
    observacoes TEXT DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (cliente_id), INDEX (funcionario_id)
)");
$conn->query("CREATE TABLE IF NOT EXISTS exercicios_treino (
    id INT AUTO_INCREMENT PRIMARY KEY,
    treino_id INT NOT NULL,
    nome VARCHAR(120) NOT NULL,
    series INT NOT NULL DEFAULT 3,
    repeticoes VARCHAR(30) NOT NULL DEFAULT '10',
    carga VARCHAR(30) DEFAULT NULL,
    descanso VARCHAR(30) DEFAULT NULL,
    observacoes VARCHAR(255) DEFAULT NULL,
    INDEX (treino_id)
)");
$hashDemo = '$2y$10$XXSH0.NpqrZcm31YBFwqDOucfQSPAB2WjP0sL0vAUzAEswSlqmjPm';
$stmtDemo = $conn->prepare("INSERT IGNORE INTO usuarios (nome, email, senha, tipo, cpf, telefone) VALUES ('Funcionário Demo', 'funcionario@ironfit.com', ?, 'funcionario', NULL, '(11) 97777-0000')");
$stmtDemo->bind_param('s', $hashDemo);
$stmtDemo->execute();
$stmtDemo = $conn->prepare('UPDATE usuarios SET senha = ? WHERE email IN (\'admin@ironfit.com\', \'cliente@ironfit.com\', \'funcionario@ironfit.com\')');
$stmtDemo->bind_param('s', $hashDemo);
$stmtDemo->execute();
