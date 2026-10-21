<?php
// Configuração da conexão
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'loja_scorpion'; // banco original

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Pegar todas as tabelas
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "-- Estrutura e dados do banco $db\n";
    echo "-- Gerado em " . date('Y-m-d H:i:s') . "\n\n";
    
    // Para cada tabela
    foreach ($tables as $table) {
        // Pegar estrutura CREATE TABLE
        $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $createTable = $row['Create Table'] . ";\n\n";
        
        // Substituir nome do banco se necessário
        $createTable = str_replace("`$db`.", "", $createTable);
        
        echo $createTable;
        
        // Pegar dados da tabela
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            $columns = array_keys($rows[0]);
            echo "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES\n";
            
            $values = [];
            foreach ($rows as $i => $row) {
                $rowValues = [];
                foreach ($row as $value) {
                    $rowValues[] = $value === null ? 'NULL' : $pdo->quote($value);
                }
                $values[] = "(" . implode(", ", $rowValues) . ")";
            }
            echo implode(",\n", $values) . ";\n\n";
        }
    }
    
    echo "\n-- Exportação concluída\n";
    
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage() . "\n");
}
?>