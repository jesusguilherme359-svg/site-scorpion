<?php
// testdb.php
// Script simples para testar as conexões do projeto.
// Coloque este arquivo na raiz do projeto e acesse via browser: http://localhost/TESTE%20SITE%20TCC%202.0/testdb.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Teste de Conexão ao Banco</h1>";

// 1) Teste usando a classe Database em config/database.php
if (file_exists(__DIR__ . '/config/database.php')) {
    require_once __DIR__ . '/config/database.php';
    echo "<h2>Database::getConnection()</h2>";
    try {
        $pdo = Database::getConnection();
        if ($pdo) {
            echo "Conexão via Database::getConnection() OK.<br>";
            $db = $pdo->query("SELECT DATABASE()")->fetchColumn();
            echo "Banco atual: <strong>" . htmlspecialchars($db) . "</strong><br>";
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            if ($tables) {
                echo "Tabelas: " . htmlspecialchars(implode(', ', $tables)) . "<br>";
            } else {
                echo "Nenhuma tabela encontrada ou erro ao listar tabelas.<br>";
            }
        } else {
            echo "Falha ao obter PDO via Database::getConnection().<br>";
        }
    } catch (Exception $e) {
        echo "Erro: " . htmlspecialchars($e->getMessage()) . "<br>";
    }
} else {
    echo "Arquivo config/database.php não encontrado.\n";
}

// 2) Teste usando executar_query() em api/db.php
if (file_exists(__DIR__ . '/api/db.php')) {
    require_once __DIR__ . '/api/db.php';
    echo "<h2>executar_query() (api/db.php)</h2>";
    try {
        $res = executar_query('SELECT DATABASE()', [], true, false);
        if ($res) {
            // quando executar_query retorna fetch(), ele vem como array associativo
            $val = array_values($res)[0] ?? null;
            echo "executar_query retornou: <strong>" . htmlspecialchars($val) . "</strong><br>";
        } else {
            echo "executar_query retornou null/false. Verifique mensagens de erro acima.\n";
        }
    } catch (Exception $e) {
        echo "Erro: " . htmlspecialchars($e->getMessage()) . "<br>";
    }
} else {
    echo "Arquivo api/db.php não encontrado.\n";
}

echo "<hr><p>Se receber mensagens de 'Access denied' ou 'Connection refused', verifique se o MySQL está rodando no XAMPP e se as credenciais em <code>config/database.php</code> e <code>api/db.php</code> estão corretas.</p>";

?>
