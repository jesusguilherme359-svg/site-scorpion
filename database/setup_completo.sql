-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS `db_streetwear`;
USE `db_streetwear`;

-- Desativar verificação de chaves estrangeiras durante a criação
SET FOREIGN_KEY_CHECKS=0;

-- Recriar tabelas (drop se existirem)
DROP TABLE IF EXISTS `carrinho`;
DROP TABLE IF EXISTS `produtos`;
DROP TABLE IF EXISTS `perfil`;

-- Tabela de perfil/usuários
CREATE TABLE `perfil` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `senha` VARCHAR(255) NOT NULL,
    `cpf` VARCHAR(14) NOT NULL UNIQUE,
    `telefone` VARCHAR(15),
    `endereco` VARCHAR(255),
    `cidade` VARCHAR(100),
    `estado` CHAR(2),
    `cep` VARCHAR(9),
    `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `data_atualizacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de produtos
CREATE TABLE `produtos` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `descricao` TEXT,
    `preco` DECIMAL(10,2) NOT NULL,
    `imagem` VARCHAR(255),
    `quantidade_estoque` INT NOT NULL DEFAULT 0,
    `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `data_atualizacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de carrinho
CREATE TABLE `carrinho` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `id_usuario` INT NOT NULL,
    `id_produto` INT NOT NULL,
    `quantidade` INT NOT NULL DEFAULT 1,
    `data_adicao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_usuario`) REFERENCES `perfil`(`id`),
    FOREIGN KEY (`id_produto`) REFERENCES `produtos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reativar verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS=1;

-- Inserir alguns produtos para teste
INSERT INTO `produtos` (`nome`, `descricao`, `preco`, `quantidade_estoque`) VALUES
('Camiseta Scorpion', 'Camiseta preta com logo Scorpion', 99.90, 50),
('Calça Streetwear', 'Calça estilo street com detalhes', 159.90, 30),
('Boné Scorpion Gang', 'Boné preto com bordado', 79.90, 25);