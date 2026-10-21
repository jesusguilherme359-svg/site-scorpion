<?php
require_once 'db.php';

function gerarCodigoRastreio() {
    // Gera um código no formato SS-XXXXXX-YY (SS = Scorpion Street)
    $codigo = 'SS-';
    $codigo .= strtoupper(substr(uniqid(), -6)); // 6 caracteres aleatórios
    $codigo .= '-' . strtoupper(substr(md5(time()), 0, 2)); // 2 caracteres de checksum
    return $codigo;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? 0;
    $itens = isset($_POST['itens']) ? json_decode($_POST['itens'], true) : [];
    
    if (!$id_usuario || empty($itens)) {
        echo json_encode(['success' => false, 'message' => 'Dados insuficientes']);
        exit;
    }

    try {
        // Inicia transação
        $pdo = new PDO("mysql:host=localhost;dbname=db_streetwear;charset=utf8mb4", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();

        // Cria o pedido
        $codigo_rastreio = gerarCodigoRastreio();
        $stmt = $pdo->prepare("INSERT INTO pedidos (id_usuario, codigo_rastreio) VALUES (?, ?)");
        $stmt->execute([$id_usuario, $codigo_rastreio]);
        $id_pedido = $pdo->lastInsertId();

        // Insere os itens do pedido
        $stmt = $pdo->prepare("INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
        
        foreach ($itens as $item) {
            // Busca o preço atual do produto
            $stmt_preco = $pdo->prepare("SELECT preco FROM produtos WHERE id = ?");
            $stmt_preco->execute([$item['id_produto']]);
            $produto = $stmt_preco->fetch(PDO::FETCH_ASSOC);
            
            if (!$produto) {
                throw new Exception("Produto não encontrado: " . $item['id_produto']);
            }

            // Insere o item com o preço atual
            $stmt->execute([
                $id_pedido,
                $item['id_produto'],
                $item['quantidade'],
                $produto['preco']
            ]);

            // Atualiza o estoque
            $pdo->prepare("UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?")
                ->execute([$item['quantidade'], $item['id_produto']]);
        }

        // Limpa o carrinho
        $pdo->prepare("DELETE FROM carrinho WHERE id_usuario = ?")
            ->execute([$id_usuario]);

        // Confirma a transação
        $pdo->commit();

        echo json_encode([
            'success' => true,
            'id_pedido' => $id_pedido,
            'codigo_rastreio' => $codigo_rastreio,
            'message' => 'Pedido registrado com sucesso!'
        ]);

    } catch (Exception $e) {
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao registrar pedido: ' . $e->getMessage()
        ]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método inválido']);
exit;