<?php

require_once __DIR__ . "/../internal/database/DatabaseService.php";

class AnuncianteRepository
{
    public DatabaseService $db;

    public function __construct()
    {
        $this->db = new DatabaseService();
    }

    public function register(AnuncianteDTO $anunciante): bool
    {
        $query = <<<SQL
          INSERT INTO `anunciante` (`nome`, `cpf`, `email`, `senha_hash`, `telefone`)
          VALUES
          (:nome, :cpf, :email, :senha_hash, :telefone);
        SQL;

        $params = [
          "nome" => $anunciante->nome,
          "cpf" => $anunciante->cpf,
          "email" => $anunciante->email,
          "senha_hash" => $anunciante->senhaHash,
          "telefone" => $anunciante->telefone
        ];

        try {
            $result = $this->db->prepareExecute($query, $params);
            return $result->success;
        } catch (PDOException $e) {
            $message = $e->getMessage();
            $sqlState = $e->getCode();
            if ($sqlState === '23000') //23000 - Integrity constraint violation
             { 
              if (str_contains($message, 'cpf')) {
                    throw new Exception('CPF ja cadastrado.');
                }
                if (str_contains($message, 'email')) {
                    throw new Exception('E-mail ja cadastrado.');
                }
                throw new Exception('Dados ja cadastrados.');
            }
            throw $e;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function getByEmail(string $email): ?AnuncianteDTO
    {
        $query = <<<SQL
          SELECT * FROM `anunciante` WHERE `email` = :email;
        SQL;

        try {
            $result = $this->db->prepareExecute($query, ["email" => $email]);
            $row = $result->stmt->fetch();
            if (!$row) {
                return null;
            }
            return AnuncianteDTO::anuncianteFromArray($row);
        } catch (Throwable $e) {
            LogService::error("could not get anunciante by email - {$e->getMessage()}");
        }
        return null;
    }
}
