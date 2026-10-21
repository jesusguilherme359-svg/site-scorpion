<?php
// Configurações do banco
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'db_streetwear';

try {
    // Tentar conectar ao MySQL
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão ao MySQL estabelecida<br>";
    
    // Ler o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/database/setup_completo.sql');
    
    if ($sql === false) {
        throw new Exception("Erro ao ler arquivo setup_completo.sql");
    }
    
    // Executar os comandos SQL
    $pdo->exec($sql);
    echo "✅ Banco de dados e tabelas criados com sucesso<br>";
    
    // Verificar se as tabelas foram criadas
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    
    // Testar tabela perfil
    $stmt = $pdo->query("DESCRIBE perfil");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ Tabela perfil OK: " . implode(", ", $cols) . "<br>";
    
    // Testar tabela produtos
    $stmt = $pdo->query("SELECT * FROM produtos");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Tabela produtos OK (" . count($produtos) . " produtos encontrados)<br>";
    
    echo "<br>🎉 Setup completo! O banco está pronto para uso.";
    
} catch (PDOException $e) {
    die("❌ Erro: " . $e->getMessage());
}
?>