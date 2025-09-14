<?php

require_once __DIR__ . '/../internal/logger/LogService.php';
require_once __DIR__ . '/InteresseDTO.php';
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

    public function registerInteresse(InteresseDTO $interesse): bool
    {
        $query = <<<SQL
                INSERT INTO interesse (nome, telefone, mensagem, data_hora, id_anuncio)
                    VALUES (:nome, :telefone, :mensagem, :data_hora, :id_anuncio);
            SQL;

        $dateTime = new DateTime($interesse->dataHora);

        $mysqlDateTime = $dateTime->format('Y-m-d H:i:s');

        $params = [
            'nome' => $interesse->nome,
            'telefone' => $interesse->telefone,
            'mensagem' => $interesse->mensagem,
            'data_hora' => $mysqlDateTime,
            'id_anuncio' => $interesse->idAnuncio
        ];

        $result = $this->service->prepareExecute($query, $params);
        return $result->success;
    }
}
