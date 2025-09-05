<?php

require_once __DIR__ . "/../internal/database/DatabaseService.php";
require_once __DIR__ . "/../foto/FotoDTO.php";

class AnuncioRepository
{
    public DatabaseService $service;

    public function __construct()
    {
        $this->service = new DatabaseService();
    }

    public function register(AnuncioDTO $anuncio, FotoDTO $foto): bool
    {
        $queryAnuncio = <<<SQL
          INSERT INTO anuncio (marca, modelo, ano, cor, quilometragem, descricao, valor, estado, cidade, id_anunciante)
          VALUES (
              :marca,
              :modelo,
              :ano,
              :cor,
              :quilometragem,
              :descricao,
              :valor,
              :estado,
              :cidade,
              :id_anunciante
          );
        SQL;

        $queryFoto = <<<SQL
          INSERT INTO foto (id_anuncio, nome_arq_foto)
          VALUES (
              :id_anuncio,
              :nome_arq_foto
          );
        SQL;

        $queries = [$queryAnuncio, $queryFoto];

        $paramsAnuncio = [
          "marca" => $anuncio->marca,
          "modelo" => $anuncio->modelo,
          "ano" => $anuncio->ano,
          "cor" => $anuncio->cor,
          "quilometragem" => $anuncio->quilometragem,
          "descricao" => $anuncio->descricao,
          "valor" => $anuncio->valor,
          "estado" => $anuncio->estado,
          "cidade" => $anuncio->cidade,
          "id_anunciante" => $anuncio->idAnunciante
        ];

        $paramsFoto = [
          "id_anuncio" => $foto->idAnuncio,
          "nome_arq_foto" => $foto->nomeArquivoFoto,
        ];

        $args = [$paramsAnuncio, $paramsFoto];

        try {
            $response = $this->service->transactionExecute($queries, $args);
            return $response;
        } catch (Throwable $e) {
            LogService::error("unable to register anuncio and photo - {$e->getMessage()}");
        }
        return false;
    }
}
