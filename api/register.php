<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

// Prevenir cache
header('Cache-Control: no-cache, must-revalidate');

// Permitir requisições do seu domínio
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Pegar dados do POST
    $data = json_decode(file_get_contents("php://input"));
    
    // Validar dados recebidos
    if (!isset($data->email) || !isset($data->senha) || 
        !isset($data->nome) || !isset($data->cpf) || 
        !isset($data->cep) || !isset($data->dataNascimento)) {
        throw new Exception("Dados incompletos");
    }

    $db = Database::getConnection();

    // Verificar se o usuário já existe
    $query = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "success" => false,
            "message" => "Email já cadastrado"
        ]);
        exit;
    }

    // Hash da senha
    $senha_hash = password_hash($data->senha, PASSWORD_DEFAULT);

    // Inserir novo usuário
    $query = "INSERT INTO usuarios (nome, email, senha, cpf, cep, data_nascimento) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->execute([
        $data->nome,
        $data->email,
        $senha_hash,
        $data->cpf,
        $data->cep,
        $data->dataNascimento
    ]);

    // Pegar o ID do usuário inserido
    $userId = $db->lastInsertId();

    // Buscar dados do usuário para retornar
    $query = "SELECT id, nome, email, cpf, cep, data_nascimento 
              FROM usuarios WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "message" => "Usuário cadastrado com sucesso",
        "data" => $usuario
    ]);

} catch(Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>