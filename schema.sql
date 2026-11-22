-- Biblo - schema idempotente (pode ser importado várias vezes).
CREATE DATABASE IF NOT EXISTS bd_saul CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bd_saul;

CREATE TABLE IF NOT EXISTS usuario (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(120) NOT NULL,
  cargo VARCHAR(60) NOT NULL DEFAULT 'Bibliotecário',
  telefone VARCHAR(30) NOT NULL DEFAULT '',
  email VARCHAR(150) NOT NULL,
  login VARCHAR(60) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuario_login (login),
  UNIQUE KEY uq_usuario_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Para bancos antigos: senha precisa comportar o hash do password_hash().
ALTER TABLE usuario MODIFY senha VARCHAR(255) NOT NULL;

CREATE TABLE IF NOT EXISTS estudante (
  idestudante INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(120) NOT NULL,
  turma VARCHAR(40) NOT NULL DEFAULT '',
  ensino VARCHAR(40) NOT NULL DEFAULT '',
  telefone VARCHAR(30) NOT NULL DEFAULT '',
  condicao VARCHAR(10) NOT NULL DEFAULT 'Ativo',
  PRIMARY KEY (idestudante)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS livro (
  idlivro INT UNSIGNED NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(200) NOT NULL,
  autor VARCHAR(150) NOT NULL DEFAULT '',
  editora VARCHAR(120) NOT NULL DEFAULT '',
  materia VARCHAR(80) NOT NULL DEFAULT '',
  isbn VARCHAR(20) NOT NULL DEFAULT '',
  quantidade INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (idlivro),
  KEY idx_livro_titulo (titulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS emprestimo (
  idemprestimo INT UNSIGNED NOT NULL AUTO_INCREMENT,
  idlivro INT UNSIGNED NOT NULL,
  idestudante INT UNSIGNED NOT NULL,
  idusuario INT UNSIGNED NULL,
  data_emprestimo DATE NOT NULL,
  data_prevista DATE NOT NULL,
  data_devolucao DATE NULL,
  PRIMARY KEY (idemprestimo),
  KEY idx_emp_livro (idlivro),
  KEY idx_emp_estudante (idestudante),
  KEY idx_emp_devolucao (data_devolucao),
  CONSTRAINT fk_emp_livro FOREIGN KEY (idlivro) REFERENCES livro (idlivro),
  CONSTRAINT fk_emp_estudante FOREIGN KEY (idestudante) REFERENCES estudante (idestudante)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
