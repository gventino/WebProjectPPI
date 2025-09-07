<?php

require_once __DIR__ . "/DatabaseResponseDTO.php";
require_once __DIR__ . "/../env/EnvService.php";
require_once __DIR__ . "/../logger/LogService.php";

class DatabaseService
{
    public array $env;

    public PDO $pdo;

    private array $options = [
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    public function __construct()
    {
        $db_host = "sql309.infinityfree.com";
        $db_username = "if0_39209482";
        $db_password = "u2craKPDns";
        $db_name = "if0_39209482_ppi";

        $options = [
            PDO::ATTR_EMULATE_PREPARES => false, // desativa a execução emulada de prepared statements
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_username, $db_password, $options);
            return $pdo;
        } 
        catch (Exception $e) {
            exit('Ocorreu uma falha na conexão com o MySQL: ' . $e->getMessage());
        }
    }

    public function prepareExecute(string $query, array $args = []): DatabaseResponseDTO
    {
        try {
            $stmt = $this->pdo->prepare($query);
            return new DatabaseResponseDTO(
                success: $stmt->execute($args),
                stmt: $stmt
            );
        } catch (Throwable $e) {
            LogService::error("unable to prepare and execute query - {$e->getMessage()}");
            throw $e;
        }
    }

    // array com as queries a serem aplicadas na transaction
    // e um array de arrays para os args de cada query
    public function transactionExecute(array $queries, array $args = []): bool
    {
        if (count($queries) != count($args)) {
            throw new Exception("unable to start transaction - queries and args have different sizes");
        }

        try {
            $this->pdo->beginTransaction();
            $qty = count($queries);
            for ($i = 0; $i < $qty; $i++) {
                $stmt = $this->pdo->prepare($queries[$i]);
                $success = $stmt->execute($args[$i]);
                if (!$success) {
                    throw new Exception("Could not execute query:\n$queries[$i]\nWith args:\n$args[$i]");
                }
            }
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            LogService::error("unable to commit transaction - {$e->getMessage()}");
            throw $e;
        }
        return true;
    }
}
