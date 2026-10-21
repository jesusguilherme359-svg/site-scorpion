-- Criação e uso do banco de dados
CREATE DATABASE IF NOT EXISTS `db_streetwear`;
USE `db_streetwear`;

-- Estrutura e dados do banco original
-- Gerado em 2025-10-24 23:04:12

-- Desabilitar verificação de chaves estrangeiras durante a importação
SET FOREIGN_KEY_CHECKS=0;

-- Apagar tabelas existentes se necessário
DROP TABLE IF EXISTS `carrinho`;
DROP TABLE IF EXISTS `perfil`;
DROP TABLE IF EXISTS `produtos`;
DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `carrinho` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT 1,
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_produto` (`id_produto`),
  CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `perfil` (`id`),
  CONSTRAINT `carrinho_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `perfil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `quantidade_estoque` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Reabilitar verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS=1;

-- Fim da importação