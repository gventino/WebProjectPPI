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
            LogService::error("unable to execute query prepare and execute query - {$e->getMessage()}");
            throw $e;
        }
    }
}
