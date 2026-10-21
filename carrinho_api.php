<?php
require_once __DIR__ . '/api/db.php';

class Carrinho {
    // Adicionar item ao carrinho
    public static function adicionarItem($idUsuario, $idProduto, $quantidade = 1) {
        // Verifica se o produto existe
        $produto = executar_query("SELECT * FROM produtos WHERE id = ?", [$idProduto], true);
        if (!$produto) {
            return ['success' => false, 'message' => 'Produto não encontrado.'];
        }

        // Verifica se há estoque suficiente
        if ($produto['quantidade_estoque'] < $quantidade) {
            return ['success' => false, 'message' => 'Quantidade indisponível no estoque.'];
        }

        // Verifica se o item já está no carrinho
        $itemExistente = executar_query(
            "SELECT * FROM carrinho WHERE id_usuario = ? AND id_produto = ?",
            [$idUsuario, $idProduto],
            true
        );

        if ($itemExistente) {
            // Atualiza a quantidade
            $novaQuantidade = $itemExistente['quantidade'] + $quantidade;
            executar_query(
                "UPDATE carrinho SET quantidade = ? WHERE id = ?",
                [$novaQuantidade, $itemExistente['id']]
            );
        } else {
            // Adiciona novo item
            executar_query(
                "INSERT INTO carrinho (id_usuario, id_produto, quantidade) VALUES (?, ?, ?)",
                [$idUsuario, $idProduto, $quantidade]
            );
        }

        return ['success' => true, 'message' => 'Produto adicionado ao carrinho.'];
    }

    // Remover item do carrinho
    public static function removerItem($idUsuario, $idProduto) {
        executar_query(
            "DELETE FROM carrinho WHERE id_usuario = ? AND id_produto = ?",
            [$idUsuario, $idProduto]
        );
        return ['success' => true, 'message' => 'Produto removido do carrinho.'];
    }

    // Atualizar quantidade
    public static function atualizarQuantidade($idUsuario, $idProduto, $quantidade) {
        if ($quantidade <= 0) {
            return self::removerItem($idUsuario, $idProduto);
        }

        // Verifica estoque
        $produto = executar_query("SELECT quantidade_estoque FROM produtos WHERE id = ?", [$idProduto], true);
        if ($produto['quantidade_estoque'] < $quantidade) {
            return ['success' => false, 'message' => 'Quantidade indisponível no estoque.'];
        }

        executar_query(
            "UPDATE carrinho SET quantidade = ? WHERE id_usuario = ? AND id_produto = ?",
            [$quantidade, $idUsuario, $idProduto]
        );
        return ['success' => true, 'message' => 'Quantidade atualizada.'];
    }

    // Listar itens do carrinho
    public static function listarItens($idUsuario) {
        return executar_query(
            "SELECT c.*, p.nome, p.preco, p.imagem, (p.preco * c.quantidade) as subtotal 
             FROM carrinho c 
             JOIN produtos p ON c.id_produto = p.id 
             WHERE c.id_usuario = ?",
            [$idUsuario],
            false,
            true
        );
    }

    // Calcular total do carrinho
    public static function calcularTotal($idUsuario) {
        $result = executar_query(
            "SELECT SUM(p.preco * c.quantidade) as total 
             FROM carrinho c 
             JOIN produtos p ON c.id_produto = p.id 
             WHERE c.id_usuario = ?",
            [$idUsuario],
            true
        );
        return $result['total'] ?? 0;
    }

    // Limpar carrinho
    public static function limparCarrinho($idUsuario) {
        executar_query("DELETE FROM carrinho WHERE id_usuario = ?", [$idUsuario]);
        return ['success' => true, 'message' => 'Carrinho esvaziado.'];
    }
}

// Processar requisições AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $idUsuario = $_POST['id_usuario'] ?? 0;
    $idProduto = $_POST['id_produto'] ?? 0;
    $quantidade = $_POST['quantidade'] ?? 1;

    $resultado = ['success' => false, 'message' => 'Ação inválida'];

    switch ($acao) {
        case 'adicionar':
            $resultado = Carrinho::adicionarItem($idUsuario, $idProduto, $quantidade);
            break;
        case 'remover':
            $resultado = Carrinho::removerItem($idUsuario, $idProduto);
            break;
        case 'atualizar':
            $resultado = Carrinho::atualizarQuantidade($idUsuario, $idProduto, $quantidade);
            break;
        case 'listar':
            $resultado = ['success' => true, 'items' => Carrinho::listarItens($idUsuario)];
            break;
        case 'total':
            $resultado = ['success' => true, 'total' => Carrinho::calcularTotal($idUsuario)];
            break;
        case 'limpar':
            $resultado = Carrinho::limparCarrinho($idUsuario);
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit;
}
?>