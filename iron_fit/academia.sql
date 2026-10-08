-- Schema completo do Iron Fit: usuários, cadastros, planos e treinos.
-- A execução recria o banco para uma instalação limpa de desenvolvimento.
-- Active: 1788121831503@@127.0.0.1@3306@academia
DROP DATABASE IF EXISTS academia;

CREATE DATABASE IF NOT EXISTS academia;

USE academia;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  tipo ENUM('admin', 'funcionario', 'cliente') NOT NULL DEFAULT 'cliente',
  cpf VARCHAR(14) DEFAULT NULL,
  telefone VARCHAR(15) DEFAULT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE alunos (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  data_nascimento DATE DEFAULT NULL,
  data_matricula DATE DEFAULT NULL,
  cpf VARCHAR(14) NOT NULL,
  email VARCHAR(100) DEFAULT NULL,
  telefone VARCHAR(15) DEFAULT NULL,
  endereco VARCHAR(150) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (cpf)
);

CREATE TABLE planos (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(50) NOT NULL,
  descricao VARCHAR(255) DEFAULT NULL,
  valor DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (nome)
);

CREATE TABLE funcionarios (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  cpf VARCHAR(14) DEFAULT NULL,
  data_nascimento DATE DEFAULT NULL,
  data_admissao DATE DEFAULT NULL,
  cargo VARCHAR(50) DEFAULT NULL,
  email VARCHAR(100) DEFAULT NULL,
  telefone VARCHAR(15) DEFAULT NULL,
  endereco VARCHAR(150) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (cpf)
);

CREATE TABLE contatos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  assunto VARCHAR(100) NOT NULL,
  mensagem TEXT NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE treinos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  funcionario_id INT DEFAULT NULL,
  dia_semana ENUM('segunda','terca','quarta','quinta','sexta','sabado','domingo') NOT NULL,
  observacoes TEXT DEFAULT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (cliente_id), INDEX (funcionario_id)
);

CREATE TABLE exercicios_treino (
  id INT AUTO_INCREMENT PRIMARY KEY,
  treino_id INT NOT NULL,
  nome VARCHAR(120) NOT NULL,
  series INT NOT NULL DEFAULT 3,
  repeticoes VARCHAR(30) NOT NULL DEFAULT '10',
  carga VARCHAR(30) DEFAULT NULL,
  descanso VARCHAR(30) DEFAULT NULL,
  observacoes VARCHAR(255) DEFAULT NULL,
  INDEX (treino_id)
);

INSERT INTO usuarios (nome, email, senha, tipo, cpf, telefone) VALUES
('Administrador', 'admin@ironfit.com', '$2y$10$XXSH0.NpqrZcm31YBFwqDOucfQSPAB2WjP0sL0vAUzAEswSlqmjPm', 'admin', '000.000.000-00', '(11) 99999-0000'),
('Cliente Demo', 'cliente@ironfit.com', '$2y$10$XXSH0.NpqrZcm31YBFwqDOucfQSPAB2WjP0sL0vAUzAEswSlqmjPm', 'cliente', '111.111.111-00', '(11) 98888-1111'),
('Funcionário Demo', 'funcionario@ironfit.com', '$2y$10$XXSH0.NpqrZcm31YBFwqDOucfQSPAB2WjP0sL0vAUzAEswSlqmjPm', 'funcionario', NULL, '(11) 97777-0000');

INSERT INTO alunos (nome, data_nascimento, data_matricula, cpf, email, telefone, endereco) VALUES
('João Silva', '1990-05-15', '2023-01-10', '123.456.789-00', 'joao.silva@email.com', '(11) 99999-9999', 'Rua Exemplo, 123'),
('Maria Souza', '1991-06-18', '2024-01-12', '987.654.321-00', 'maria.souza@email.com', '(11) 98888-2222', 'Avenida Brasil, 456'),
('Pedro Lima', '1988-08-22', '2025-02-18', '456.789.123-00', 'pedro.lima@email.com', '(11) 97777-3333', 'Rua das Flores, 789');

INSERT INTO planos (nome, descricao, valor) VALUES
('Mensal', 'Acesso completo por 30 dias com acompanhamento básico', 119.90),
('Trimestral', 'Plano com desconto para treinos por 3 meses', 299.90),
('Anual', 'Acesso ilimitado com atendimento prioritário', 999.90);

INSERT INTO funcionarios (nome, cpf, data_nascimento, data_admissao, cargo, email, telefone, endereco) VALUES
('Ana Souza', '111.222.333-44', '1985-11-10', '2020-03-15', 'Personal Trainer', 'ana@ironfit.com', '(11) 98765-4321', 'Avenida Norte, 150'),
('Carlos Mendes', '222.333.444-55', '1990-07-16', '2022-06-20', 'Recepcionista', 'carlos@ironfit.com', '(11) 97654-3210', 'Rua Central, 90'),
('Beatriz Rocha', '333.444.555-66', '1992-09-09', '2023-01-08', 'Instrutora de Yoga', 'beatriz@ironfit.com', '(11) 96543-2109', 'Alameda Oeste, 222');