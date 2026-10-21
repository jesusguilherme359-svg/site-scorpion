<?php
require_once __DIR__ . '/db.php';

// Retorna o produto que mais corresponde ao nome (LIKE)
$nome = '';
$createIfMissing = false;
$precoRecebido = null;
$imagemRecebida = null;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $nome = $_GET['nome'] ?? '';
    $createIfMissing = isset($_GET['create_if_missing']) && ($_GET['create_if_missing'] === '1' || $_GET['create_if_missing'] === 'true');
    $precoRecebido = $_GET['preco'] ?? null;
    $imagemRecebida = $_GET['imagem'] ?? null;
} else {
    $nome = $_POST['nome'] ?? '';
    $createIfMissing = isset($_POST['create_if_missing']) && ($_POST['create_if_missing'] === '1' || $_POST['create_if_missing'] === 'true');
    $precoRecebido = $_POST['preco'] ?? null;
    $imagemRecebida = $_POST['imagem'] ?? null;
}

header('Content-Type: application/json');
if (empty($nome)) {
    echo json_encode(['success' => false, 'message' => 'Nome vazio']);
    exit;
}

// Busca produto pelo nome (LIKE)
$like = "%" . $nome . "%";
$produto = executar_query("SELECT id, nome, preco, imagem FROM produtos WHERE nome LIKE ? LIMIT 1", [$like], true);
if ($produto) {
    echo json_encode(['success' => true, 'id' => $produto['id'], 'nome' => $produto['nome'], 'preco' => $produto['preco'], 'imagem' => $produto['imagem']]);
} else {
    // Se solicitado, cria o produto automaticamente com dados mínimos
    if ($createIfMissing && !empty($nome)) {
        // Normalizar preco
        $preco = null;
        if (!empty($precoRecebido)) {
            $preco = str_replace(',', '.', preg_replace('/[^0-9,\.]/', '', $precoRecebido));
        }
        $imagem = $imagemRecebida ?? null;
        // Inserir com estoque padrão 10
        $params = [$nome, $preco ?? 0.00, $imagem, 10];
        executar_query("INSERT INTO produtos (nome, descricao, preco, imagem, quantidade_estoque) VALUES (?, '', ?, ?, ?)", $params);
        // Recupera o produto criado
        $created = executar_query("SELECT id, nome, preco, imagem FROM produtos WHERE nome = ? ORDER BY id DESC LIMIT 1", [$nome], true);
        if ($created) {
            echo json_encode(['success' => true, 'id' => $created['id'], 'nome' => $created['nome'], 'preco' => $created['preco'], 'imagem' => $created['imagem'], 'created' => true]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
}
exit;
