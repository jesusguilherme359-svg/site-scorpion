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
    
    if (!isset($data->email) || !isset($data->senha)) {
        throw new Exception("Email e senha são obrigatórios");
    }

    $db = Database::getConnection();

    // Buscar usuário
    $query = "SELECT id, nome, email, cpf, cep, data_nascimento, senha 
              FROM usuarios WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->email]);
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || !password_verify($data->senha, $usuario['senha'])) {
        echo json_encode([
            "success" => false,
            "message" => "Email ou senha incorretos"
        ]);
        exit;
    }

    // Remover a senha do retorno
    unset($usuario['senha']);

    echo json_encode([
        "success" => true,
        "message" => "Login realizado com sucesso",
        "data" => $usuario
    ]);

} catch(Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>