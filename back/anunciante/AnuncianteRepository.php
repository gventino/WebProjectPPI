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
        (:nome, :cpf, :email, :senhaHash, :telefone);
    SQL;

    $result = $this->db->prepareExecute($query, (array) $anunciante);
    return $result["success"];
  }
}
