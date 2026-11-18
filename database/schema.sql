-- Gerenciador Logístico - script de criação do banco (MySQL/MariaDB - XAMPP)
CREATE DATABASE IF NOT EXISTS gerenciador_logistico
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gerenciador_logistico;

CREATE TABLE IF NOT EXISTS produtos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  descricao VARCHAR(255) NULL,
  peso_kg DECIMAL(10,3) NOT NULL DEFAULT 0,
  valor DECIMAL(12,2) NOT NULL DEFAULT 0,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS enderecos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  apelido VARCHAR(80) NOT NULL,
  cep VARCHAR(8) NOT NULL,
  logradouro VARCHAR(150) NOT NULL,
  numero VARCHAR(20) NOT NULL,
  complemento VARCHAR(80) NULL,
  bairro VARCHAR(80) NOT NULL,
  cidade VARCHAR(80) NOT NULL,
  uf CHAR(2) NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transportadores (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  documento VARCHAR(14) NOT NULL,
  telefone VARCHAR(15) NULL,
  email VARCHAR(120) NULL,
  veiculo VARCHAR(60) NULL,
  placa VARCHAR(8) NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Um frete tem 1 transportador, 1 origem e 1 destino
CREATE TABLE IF NOT EXISTS fretes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  transportador_id INT UNSIGNED NOT NULL,
  origem_id INT UNSIGNED NOT NULL,
  destino_id INT UNSIGNED NOT NULL,
  data_frete DATE NOT NULL,
  valor_frete DECIMAL(12,2) NOT NULL DEFAULT 0,
  observacoes VARCHAR(255) NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_frete_transp FOREIGN KEY (transportador_id) REFERENCES transportadores(id) ON DELETE RESTRICT,
  CONSTRAINT fk_frete_origem FOREIGN KEY (origem_id) REFERENCES enderecos(id) ON DELETE RESTRICT,
  CONSTRAINT fk_frete_destino FOREIGN KEY (destino_id) REFERENCES enderecos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Relação 1 para N: um frete leva vários produtos
CREATE TABLE IF NOT EXISTS frete_itens (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  frete_id INT UNSIGNED NOT NULL,
  produto_id INT UNSIGNED NOT NULL,
  quantidade INT UNSIGNED NOT NULL DEFAULT 1,
  CONSTRAINT fk_item_frete FOREIGN KEY (frete_id) REFERENCES fretes(id) ON DELETE CASCADE,
  CONSTRAINT fk_item_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT,
  UNIQUE KEY uq_frete_produto (frete_id, produto_id)
) ENGINE=InnoDB;
