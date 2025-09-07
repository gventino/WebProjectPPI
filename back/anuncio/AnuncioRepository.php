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

    public function register(AnuncioDTO $anuncio, array $fotos): bool
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


        $pdo = $this->service->pdo;
        try {
            $pdo->beginTransaction();

            // insere o anuncio
            $stmtAnuncio = $pdo->prepare($queryAnuncio);
            $successAnuncio = $stmtAnuncio->execute($paramsAnuncio);
            if (!$successAnuncio) {
                throw new Exception("could not register anuncio");
            }

            // insere todos as fotos do anuncio
            $idAnuncio = $pdo->lastInsertId();
            foreach ($fotos as $foto) {
                $stmtFoto = $pdo->prepare($queryFoto);
                $paramsFoto = [
                  "nome_arq_foto" => $foto->nomeArquivoFoto,
                  "id_anuncio" => $idAnuncio
                ];
                $successFoto = $stmtFoto->execute($paramsFoto);
                if (!$successFoto) {
                    throw new Exception("could not register foto at anuncio register flow");
                }
            }
            $pdo->commit();

            return true;
        } catch (Throwable $e) {
            LogService::error("unable to register anuncio and photo - {$e->getMessage()}");
            $pdo->rollBack();
        }
        return false;
    }
}
