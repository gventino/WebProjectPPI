<?php

require_once __DIR__ . "/../internal/logger/LogService.php";
require_once __DIR__ . "/../internal/database/DatabaseService.php";

class FotoRepository
{
    public DatabaseService $service;

    public function __construct()
    {
        $this->service = new DatabaseService();
    }

    // traz a primeira foto de cada anuncio
    public function getPhotos(array $anuncios): array
    {
        $query = <<<SQL
        SELECT * FROM foto
          WHERE id_anuncio = :id_anuncio
          LIMIT 1; 
        SQL;

        $photos = [];
        foreach ($anuncios as $anuncio) {
            $params = [
              "id_anuncio" => $anuncio->id
            ];

            $result = $this->service->prepareExecute($query, $params);
            $row = $result->stmt->fetch();
            if ($row !== false && isset($row["nome_arq_foto"])) {
                $photos[] = $row["nome_arq_foto"];
            } else {
                $photos[] = null;
            }
        }
        return $photos;
    }
}
