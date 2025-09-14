<?php

require_once __DIR__ . '/../internal/logger/LogService.php';
require_once __DIR__ . '/../internal/database/DatabaseService.php';

class InteresseRepository
{
    public DatabaseService $service;

    public function __construct()
    {
        $this->service = new DatabaseService();
    }

    public function getInteressesByAnuncioId(int $anuncioId): array
    {
        $query = <<<SQL
              SELECT * FROM interesse
                WHERE id_anuncio = ?; 
            SQL;

        $result = $this->service->prepareExecute($query, [$anuncioId]);
        return $result->stmt->fetchAll();
    }
}
