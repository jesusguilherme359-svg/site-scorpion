<?php
require_once __DIR__ . '/config/database.php';

$conn = Database::getConnection();
if ($conn) {
    echo "✅ Conexão com o banco de dados estabelecida com sucesso!<br>";
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tabelas encontradas: <pre>" . print_r($tables, true) . "</pre>";
} else {
    echo "❌ Falha ao conectar ao banco de dados.";
}
?>
