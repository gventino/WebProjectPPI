<?php

require_once __DIR__ . "/../internal/logger/LogService.php";
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

    public function listUser(int $idAnunciante): array
    {
        $query = <<<SQL
          SELECT * FROM anuncio
            WHERE id_anunciante = :id_anunciante;
        SQL;

        $params = [
          "id_anunciante" => $idAnunciante
        ];

        $result = $this->service->prepareExecute($query, $params);
        if (!$result->success) {
            return [];
        }

        $response = [];
        while ($row = $result->stmt->fetch()) {
            $response[] = new AnuncioDTO(
                id: $row["id"],
                marca: $row["marca"],
                modelo: $row["modelo"],
                ano: $row["ano"],
                cor: $row["cor"],
                quilometragem: $row["quilometragem"],
                descricao: $row["descricao"],
                valor: $row["valor"],
                dataHora: $row["data_hora"],
                estado: $row["estado"],
                cidade: $row["cidade"]
            );
        }
        return $response;
    }

    public function delete(int $anuncioId): bool
    {
        // foto tem on delete cascade, ent se apagar o anuncio o db apaga a foto automatico!
        $query = <<<SQL
          DELETE FROM anuncio
            WHERE id = ?;
        SQL;
        
        try {
          $response = $this->service->prepareExecute($query, [$anuncioId]);
          if (!$response->success){
            throw new Exception("Fail to delete anuncio and foto with anuncioId = $anuncioId");
          }
          return true;

        } catch(Throwable $e) {
          throw $e;
        }
    }

    public function isOwner(int $anuncianteId, int $anuncioId): bool {
      $query = <<<SQL
        SELECT * FROM anuncio
          WHERE id = :id_anuncio
          AND id_anunciante = :id_anunciante;
      SQL;

      $params = [
        "id_anuncio" => $anuncioId,
        "id_anunciante" => $anuncianteId
      ];

      try {
        $response = $this->service->prepareExecute($query, $params);
        if (!$response->success){
            throw new Exception("Could not verify ownership for anuncio $anuncioId and anunciante $anuncianteId");
        }
        return count($response->stmt->fetchAll()) > 0;
      } catch(Throwable $e) {
        throw $e;
      }
    }

    public function getById(int $anuncioId): ?AnuncioDTO {
        $query = <<<SQL
          SELECT a.*, an.nome as anunciante_nome, an.telefone as anunciante_telefone, an.email as anunciante_email
          FROM anuncio a
          JOIN anunciante an ON a.id_anunciante = an.id
          WHERE a.id = :id_anuncio;
        SQL;

        $params = [
          "id_anuncio" => $anuncioId
        ];

        try {
            $response = $this->service->prepareExecute($query, $params);
            if (!$response->success) {
                throw new Exception("Could not get anuncio with id = $anuncioId");
            }

            $row = $response->stmt->fetch();
            if (!$row) {
                return null;
            }

            return new AnuncioDTO(
                id: $row["id"],
                marca: $row["marca"],
                modelo: $row["modelo"],
                ano: $row["ano"],
                cor: $row["cor"],
                quilometragem: $row["quilometragem"],
                descricao: $row["descricao"],
                valor: $row["valor"],
                dataHora: $row["data_hora"],
                estado: $row["estado"],
                cidade: $row["cidade"],
                idAnunciante: $row["id_anunciante"],
                anuncianteNome: $row["anunciante_nome"],
                anuncianteTelefone: $row["anunciante_telefone"],
                anuncianteEmail: $row["anunciante_email"]
            );
        } catch(Throwable $e) {
            throw $e;
        }
    }

}
