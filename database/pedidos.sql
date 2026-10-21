-- Tabela de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('confirmado', 'separacao', 'transporte', 'entregue') NOT NULL DEFAULT 'confirmado',
    codigo_rastreio VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de itens do pedido
CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id),
    FOREIGN KEY (id_produto) REFERENCES produtos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices para melhor performance
CREATE INDEX idx_pedidos_usuario ON pedidos(id_usuario);
CREATE INDEX idx_pedidos_rastreio ON pedidos(codigo_rastreio);
CREATE INDEX idx_itens_pedido ON itens_pedido(id_pedido);