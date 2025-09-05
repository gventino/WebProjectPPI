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

        $result = $this->db->prepareExecute($query, $params);
        return $result->success;
    }

    public function getByEmail(string $email): ?AnuncianteDTO
    {
        $query = <<<SQL
      SELECT * FROM `anunciante` WHERE `email` = :email;
    SQL;
        $result = $this->db->prepareExecute($query, ["email" => $email]);

        $row = $result->stmt->fetch();
        if ($row === false) {
            return null;
        }

        return AnuncianteDTO::anuncianteFromArray($row);
    }
}
