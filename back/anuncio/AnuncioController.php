<?php

require_once __DIR__ . "/../internal/logger/LogService.php";
require_once __DIR__ . "/AnuncioDTO.php";
require_once __DIR__ . "/../foto/FotoDTO.php";

header('Content-Type: application/json; charset=utf-8');

$stringJSON = file_get_contents('php://input');
$plainObject = json_decode($stringJSON);

$action = $plainObject->action ?? "empty";
$service = new AnuncioService();

switch ($action) {
    case 'register':
        $anuncio = new AnuncioDTO(
            marca: $plainObject->marca,
            modelo: $plainObject->modelo,
            ano: $plainObject->ano,
            cor: $plainObject->cor,
            quilometragem: $plainObject->quilometragem,
            descricao: $plainObject->descricao,
            valor: $plainObject->valor,
            dataHora: $plainObject->dataHora,
            estado: $plainObject->estado,
            cidade: $plainObject->cidade,
            idAnunciante: $plainObject->idAnunciante
        );

        // pensar num jeito de pegar o id
        $foto = new FotoDTO(
            nomeArquivoFoto: $plainObject->nomeArquivoFoto
        );

        $result = $service->register($anuncio, $foto);
        echo json_encode($result->success);
        break;

        // case 'checkSession':
        //     $result = $service->checkSession();
        //     echo json_encode($result);
        //     break;

    default:
        LogService::error("unkown action at AnuncianteController - {$action}");
        break;
}
