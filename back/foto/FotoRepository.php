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
        $idsAnuncios = array_map(fn($anuncio) => $anuncio->id, $anuncios);

        $inClause = implode(',', array_fill(0, count($idsAnuncios), '?'));

        $query = <<<SQL
          SELECT
              f.id_anuncio,
              f.nome_arq_foto
          FROM
              foto f
          INNER JOIN (
              SELECT
                  id_anuncio,
                  MIN(id) AS primeira_foto_id
              FROM
                  foto
              WHERE
                  id_anuncio IN ($inClause)
              GROUP BY
                  id_anuncio
          ) AS primeiras_fotos ON f.id = primeiras_fotos.primeira_foto_id;
        SQL;
        $result = $this->service->prepareExecute($query, $idsAnuncios);

        $photosMap = [];
        $rows = $result->stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $idAnuncio = $row['id_anuncio'];
            $photosMap[$idAnuncio] = $row['nome_arq_foto'];
        }

        return $photosMap;
  }

  public function getFotosByAnuncioId(int $anuncioId): array 
  {
      $query = <<<SQL
        SELECT nome_arq_foto 
          FROM foto
          WHERE id_anuncio = ?;
      SQL;

      $response = $this->service->prepareExecute($query, [$anuncioId]);
      if (!$response->success) {
        throw new Exception("Could not get fotos for anuncioId = $anuncioId");
      }
      $fotos = $response->stmt->fetchAll();
      return array_map(fn($foto) => $foto['nome_arq_foto'], $fotos);
  }
}
