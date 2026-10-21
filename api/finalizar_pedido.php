<?php
require_once __DIR__ . '/db.php';

// Recebe dados do pedido via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = $_POST['id_usuario'] ?? 0;
    $itens = isset($_POST['itens']) ? json_decode($_POST['itens'], true) : [];
    if (!$idUsuario || empty($itens)) {
        echo json_encode(['success' => false, 'message' => 'Dados insuficientes.']);
        exit;
    }

    // Cria pedido
    $dataPedido = date('Y-m-d H:i:s');
    executar_query(
        "INSERT INTO pedidos (id_usuario, data_pedido, status) VALUES (?, ?, ?)",
        [$idUsuario, $dataPedido, 'processando']
    );
    // Recupera id do pedido
    $pedido = executar_query("SELECT id FROM pedidos WHERE id_usuario = ? ORDER BY id DESC LIMIT 1", [$idUsuario], true);
    $idPedido = $pedido ? $pedido['id'] : 0;

    // Insere itens do pedido, criando produto se necessário
    foreach ($itens as $item) {
        $idProduto = $item['id_produto'] ?? 0;
        $nomeProduto = $item['nome'] ?? '';
        $precoProduto = $item['preco'] ?? 0;
        $imagemProduto = $item['imagem'] ?? '';
        $quantidade = $item['quantidade'] ?? 1;

        // Se id_produto não existe ou é zero, tenta buscar pelo nome
        if (!$idProduto && $nomeProduto) {
            $produto = executar_query("SELECT id FROM produtos WHERE nome = ?", [$nomeProduto], true);
            if ($produto && isset($produto['id'])) {
                $idProduto = $produto['id'];
            } else {
                // Cria produto
                $idProduto = executar_query(
                    "INSERT INTO produtos (nome, preco, imagem, quantidade_estoque) VALUES (?, ?, ?, ?)",
                    [$nomeProduto, $precoProduto, $imagemProduto, 100], // estoque inicial padrão
                    false, false, true
                );
            }
        }

        // Insere item do pedido
        executar_query(
            "INSERT INTO itens_pedido (id_pedido, id_produto, quantidade) VALUES (?, ?, ?)",
            [$idPedido, $idProduto, $quantidade]
        );
        // Atualiza estoque
        executar_query(
            "UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?",
            [$quantidade, $idProduto]
        );
    }

    // Limpa carrinho do usuário
    executar_query("DELETE FROM carrinho WHERE id_usuario = ?", [$idUsuario]);

    echo json_encode(['success' => true, 'message' => 'Pedido registrado com sucesso!']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método inválido.']);
exit;
