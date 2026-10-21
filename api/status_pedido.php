<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_rastreio = $_POST['codigo_rastreio'] ?? '';
    
    if (!$codigo_rastreio) {
        echo json_encode(['success' => false, 'message' => 'Código de rastreio não fornecido']);
        exit;
    }

    // Busca o status atual do pedido
    $query = "SELECT status FROM pedidos WHERE codigo_rastreio = ?";
    $pedido = executar_query($query, [$codigo_rastreio], true);

    if ($pedido) {
        // Converte o status em número (0 = confirmado, 1 = em separação, 2 = em transporte, 3 = entregue)
        $status_atual = match($pedido['status']) {
            'confirmado' => 0,
            'separacao' => 1,
            'transporte' => 2,
            'entregue' => 3,
            default => 0
        };

        echo json_encode([
            'success' => true,
            'status' => $status_atual
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método inválido']);
exit;