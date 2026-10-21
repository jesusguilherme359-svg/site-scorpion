<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? 0;
    
    if (!$id_usuario) {
        echo json_encode(['success' => false, 'message' => 'ID do usuário não fornecido']);
        exit;
    }

    $query = "SELECT nome, cpf, cep, endereco, data_nascimento FROM usuarios WHERE id = ?";
    $usuario = executar_query($query, [$id_usuario], true);

    if ($usuario) {
        echo json_encode([
            'success' => true,
            'nome' => $usuario['nome'],
            'cpf' => $usuario['cpf'],
            'cep' => $usuario['cep'],
            'endereco' => $usuario['endereco'],
            'data_nascimento' => $usuario['data_nascimento']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método inválido']);
exit;