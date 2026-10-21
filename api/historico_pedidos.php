<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? 0;
    
    if (!$id_usuario) {
        echo json_encode(['success' => false, 'message' => 'ID do usuário não fornecido']);
        exit;
    }

    // Busca todos os pedidos do usuário com produtos e valores
    $query = "SELECT p.id, p.data_pedido, p.status, p.codigo_rastreio,
                     ip.quantidade, pr.nome as produto_nome, pr.preco,
                     (ip.quantidade * pr.preco) as valor_total
              FROM pedidos p
              JOIN itens_pedido ip ON p.id = ip.id_pedido
              JOIN produtos pr ON ip.id_produto = pr.id
              WHERE p.id_usuario = ?
              ORDER BY p.data_pedido DESC";
    
    $pedidos = executar_query($query, [$id_usuario], false, true);

    if ($pedidos) {
        // Agrupa os itens por pedido
        $historico = [];
        foreach ($pedidos as $pedido) {
            $id_pedido = $pedido['id'];
            if (!isset($historico[$id_pedido])) {
                $historico[$id_pedido] = [
                    'id' => $id_pedido,
                    'data' => $pedido['data_pedido'],
                    'status' => $pedido['status'],
                    'codigo_rastreio' => $pedido['codigo_rastreio'],
                    'itens' => [],
                    'valor_total' => 0
                ];
            }
            
            $historico[$id_pedido]['itens'][] = [
                'produto' => $pedido['produto_nome'],
                'quantidade' => $pedido['quantidade'],
                'preco_unitario' => $pedido['preco'],
                'subtotal' => $pedido['valor_total']
            ];
            
            $historico[$id_pedido]['valor_total'] += $pedido['valor_total'];
        }

        echo json_encode([
            'success' => true,
            'historico' => array_values($historico)
        ]);
    } else {
        echo json_encode(['success' => true, 'historico' => []]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método inválido']);
exit;