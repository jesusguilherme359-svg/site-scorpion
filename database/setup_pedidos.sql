-- Tabelas para pedidos e itens do pedido
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    data_pedido DATETIME NOT NULL,
    status VARCHAR(32) DEFAULT 'processando',
    FOREIGN KEY (id_usuario) REFERENCES perfil(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id),
    FOREIGN KEY (id_produto) REFERENCES produtos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
