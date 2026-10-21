<?php

function executar_query($query, $parametros = [], $fetchone = false, $fetchall = false, $return_lastrowid = false) {
    $host = 'localhost';
    $usuario = 'root';
    $senha = '';
    $banco = 'db_streetwear';

    try {
        // Conexão com o banco de dados usando PDO
        $pdo = new PDO("mysql:host={$host};dbname={$banco};charset=utf8mb4", $usuario, $senha);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Prepara e executa a consulta
        $stmt = $pdo->prepare($query);
        if (!empty($parametros)) {
            $stmt->execute($parametros);
        } else {
            $stmt->execute();
        }

        // SELECT
        $firstWord = strtoupper(strtok(trim($query), " \n\r\t"));
        if ($firstWord === 'SELECT') {
            if ($fetchone) return $stmt->fetch();
            if ($fetchall) return $stmt->fetchAll();
            return null;
        }

        // INSERT/UPDATE/DELETE
        if ($return_lastrowid) return $pdo->lastInsertId();
        return true;

    } catch (PDOException $e) {
        // Log básico de erro (não vazar em produção)
        error_log('Erro executar_query: ' . $e->getMessage());
        return null;
    }
}