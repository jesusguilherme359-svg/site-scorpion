<?php
require_once __DIR__ . '/../api/db.php';

try {
    // Lê o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/pedidos.sql');
    
    // Divide em queries individuais
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    // Executa cada query
    foreach ($queries as $query) {
        if (!empty($query)) {
            executar_query($query);
        }
    }
    
    echo "Tabelas criadas com sucesso!\n";
    
} catch (Exception $e) {
    echo "Erro ao criar tabelas: " . $e->getMessage() . "\n";
}