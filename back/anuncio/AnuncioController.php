<?php

require_once __DIR__ . "/../internal/logger/LogService.php";
require_once __DIR__ . "/AnuncioDTO.php";
require_once __DIR__ . "/AnuncioService.php";
require_once __DIR__ . "/../foto/FotoDTO.php";
require_once __DIR__ . "/../foto/FotoService.php";
require_once __DIR__ . "/../messages/MessageDTO.php";

header('Content-Type: application/json; charset=utf-8');

$anuncioService = new AnuncioService();
$fotoService = new FotoService();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? $_POST['action'] ?? "empty";

function verifyFields(): bool
{
    return isset($_POST['marca'])
        && isset($_POST['modelo'])
        && isset($_POST['ano'])
        && isset($_POST['cor'])
        && isset($_POST['quilometragem'])
        && isset($_POST['descricao'])
        && isset($_POST['valor'])
        && isset($_POST['dataHora'])
        && isset($_POST['estado'])
        && isset($_POST['cidade']);
}

switch ($action) {
    case 'register':
        $savedFileNames = [];
        try {
            if (!verifyFields()) {
                throw new Exception("Houve algum campo faltante no formulario.");
            }

            $savedFileNames = $fotoService->savePhotos($_FILES);

            $anuncio = new AnuncioDTO(
                marca: $_POST['marca'],
                modelo: $_POST['modelo'],
                ano: $_POST['ano'],
                cor: $_POST['cor'],
                quilometragem: $_POST['quilometragem'],
                descricao: $_POST['descricao'],
                valor: $_POST['valor'],
                dataHora: $_POST['dataHora'],
                estado: $_POST['estado'],
                cidade: $_POST['cidade']
            );

            $fotos = [];
            foreach ($savedFileNames as $fileName) {
                $fotos[] = new FotoDTO(nomeArquivoFoto: $fileName);
            }

            $result = $anuncioService->register($anuncio, $fotos);

            if (!$result->success) {
                throw new Exception($result->message);
            }

            echo json_encode($result);

        } catch (Exception $e) {
            LogService::error($e->getMessage());

            if (!empty($savedFileNames)) {
                $fotoService->deletePhotos($savedFileNames);
            }

            echo json_encode(new MessageDTO(message: $e->getMessage(), success: false));
        }
        break;

    case 'listUser':
        $messageAnuncioService = $anuncioService->listUser();
        $anuncios = $messageAnuncioService->obj;

        $mensagemFotoService = $fotoService->getPhotos($anuncios);
        $fotos = $mensagemFotoService->obj;

        if (count($anuncios) != count($fotos)) {
            echo json_encode(
                new MessageDTO(success: false, message: "Algo deu errado, quantidade divergente de fotos e anuncios.")
            );
            return;
        }

        $anunciosCompletos = [];
        foreach ($anuncios as $anuncio) {
            $anuncioArray = (array) $anuncio;
            $anuncioArray['foto'] = $fotos[$anuncio->id] ?? null;
            $anunciosCompletos[] = $anuncioArray;
        }

        echo json_encode(
            new MessageDTO(
                success: true,
                message: "listagem bem sucedida.",
                obj: $anunciosCompletos
            )
        );
        break;

    default:
        LogService::error("unkown action at AnuncioController - {$action}");
        echo json_encode(new MessageDTO(message: "Action desconhecida - {$action}", success: false));
        break;
}
