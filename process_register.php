<?php
require_once __DIR__ . '/api/db.php';

function cadastrarUsuario($nome, $email, $senha, $cpf, $telefone, $endereco, $cidade, $estado, $cep) {
    // Verifica se o email já existe
    $checkEmail = executar_query("SELECT id FROM perfil WHERE email = ?", [$email], true);
    if ($checkEmail) {
        return ['success' => false, 'message' => 'Este email já está cadastrado.'];
    }

    // Verifica se o CPF já existe
    $checkCpf = executar_query("SELECT id FROM perfil WHERE cpf = ?", [$cpf], true);
    if ($checkCpf) {
        return ['success' => false, 'message' => 'Este CPF já está cadastrado.'];
    }

    // Hash da senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere o novo usuário
    $query = "INSERT INTO perfil (nome, email, senha, cpf, telefone, endereco, cidade, estado, cep, data_cadastro) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    try {
        executar_query($query, [
            $nome, $email, $senhaHash, $cpf, $telefone, $endereco, $cidade, $estado, $cep
        ]);
        return ['success' => true, 'message' => 'Cadastro realizado com sucesso!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Erro ao cadastrar: ' . $e->getMessage()];
    }
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome' => $_POST['nome'] ?? '',
        'email' => $_POST['email'] ?? '',
        'senha' => $_POST['senha'] ?? '',
        'cpf' => $_POST['cpf'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'endereco' => $_POST['endereco'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? '',
        'cep' => $_POST['cep'] ?? ''
    ];

    // Validações básicas
    if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
        $resultado = ['success' => false, 'message' => 'Por favor, preencha todos os campos obrigatórios.'];
    } else {
        $resultado = cadastrarUsuario(
            $dados['nome'], $dados['email'], $dados['senha'],
            $dados['cpf'], $dados['telefone'], $dados['endereco'],
            $dados['cidade'], $dados['estado'], $dados['cep']
        );
    }

    // Retorna JSON para o frontend
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit;
}

// Se não for POST, redireciona para a página de cadastro
header('Location: cadastro.html');
exit;
?>