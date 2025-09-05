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
        try {
            $this->env = EnvService::loadEnv();
        } catch (Throwable $e) {
            LogService::error("unable to load env vars at DatabaseService - {$e}");
            throw $e;
        }

        try {
            $uri = "mysql:host={$this->env["DB_HOST"]}; dbname={$this->env["MYSQL_DATABASE"]}; charset=utf8mb4";
            $this->pdo = new PDO($uri, $this->env["MYSQL_USER"], $this->env["MYSQL_PASSWORD"], $this->options);
        } catch (Throwable $e) {
            LogService::error("unable to connect to Database - {$e->getMessage()}");
            throw $e;
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
            for ($i = 0; $i <= $qty; $i++) {
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
