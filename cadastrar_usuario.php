<?php
require_once __DIR__ . '/api/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Receber dados do formulário
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $cep = $_POST['cep'] ?? '';
    
    // Validações básicas
    $erros = [];
    if (empty($nome)) $erros[] = "Nome é obrigatório";
    if (empty($email)) $erros[] = "Email é obrigatório";
    if (empty($senha)) $erros[] = "Senha é obrigatória";
    if (empty($cpf)) $erros[] = "CPF é obrigatório";
    
    if (empty($erros)) {
        try {
            // Verificar se email já existe
            $check = executar_query(
                "SELECT id FROM perfil WHERE email = ?",
                [$email],
                true
            );
            
            if ($check) {
                $response = [
                    'success' => false,
                    'message' => 'Este email já está cadastrado'
                ];
            } else {
                // Hash da senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
                // Inserir usuário
                $query = "INSERT INTO perfil (nome, email, senha, cpf, telefone, endereco, cidade, estado, cep) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                executar_query($query, [
                    $nome, $email, $senha_hash, $cpf, $telefone, 
                    $endereco, $cidade, $estado, $cep
                ]);
                
                $response = [
                    'success' => true,
                    'message' => 'Cadastro realizado com sucesso!'
                ];
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Erro ao cadastrar: ' . $e->getMessage()
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Erros de validação: ' . implode(', ', $erros)
        ];
    }
    
    // Retornar resposta como JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>