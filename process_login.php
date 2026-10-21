<?php
require_once __DIR__ . '/api/db.php';

// Função para processar o login
function processLogin($email, $senha) {
    $query = "SELECT id, nome, email, senha FROM perfil WHERE email = ?";
    $resultado = executar_query($query, [$email], true);

    if ($resultado) {
        // Verifica a senha usando password_verify
        if (isset($resultado['senha']) && password_verify($senha, $resultado['senha'])) {
            // Remover a senha do retorno
            unset($resultado['senha']);
            return ['success' => true, 'message' => 'Login realizado com sucesso!', 'data' => $resultado];
        }
    }

    return ['success' => false, 'message' => 'Email ou senha incorretos.'];
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // aceitar tanto form-data como JSON
    $email = '';
    $senha = '';

    if (!empty($_POST)) {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
    } else {
        // Possível JSON no body
        $input = json_decode(file_get_contents('php://input'), true);
        if (is_array($input)) {
            $email = $input['email'] ?? '';
            $senha = $input['senha'] ?? '';
        }
    }

    $resultado = processLogin($email, $senha);

    // Retorna JSON para o frontend
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit;
}

// Se não for POST, redireciona para a página de login
header('Location: login.html');
exit;
?>