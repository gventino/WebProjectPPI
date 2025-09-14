<?php

require_once __DIR__ . '/../internal/logger/LogService.php';
require_once __DIR__ . '/AnuncioDTO.php';
require_once __DIR__ . '/AnuncioService.php';
require_once __DIR__ . '/../foto/FotoDTO.php';
require_once __DIR__ . '/../foto/FotoService.php';
require_once __DIR__ . '/../interesse/InteresseService.php';
require_once __DIR__ . '/../messages/MessageDTO.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');

$anuncioService = new AnuncioService();
$fotoService = new FotoService();
$interesseService = new InteresseService();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? $_POST['action'] ?? 'empty';

function verifyFields(): bool
{
    return isset($_POST['marca']) &&
        isset($_POST['modelo']) &&
        isset($_POST['ano']) &&
        isset($_POST['cor']) &&
        isset($_POST['quilometragem']) &&
        isset($_POST['descricao']) &&
        isset($_POST['valor']) &&
        isset($_POST['dataHora']) &&
        isset($_POST['estado']) &&
        isset($_POST['cidade']);
}

switch ($action) {
    case 'register':
        $savedFileNames = [];
        try {
            if (!verifyFields()) {
                throw new Exception('Houve algum campo faltante no formulario.');
            }

            $savedFileNames = $fotoService->savePhotos($_FILES);

            $anuncio = new AnuncioDTO(
                marca: htmlspecialchars($_POST['marca']),
                modelo: htmlspecialchars($_POST['modelo']),
                ano: $_POST['ano'],
                cor: htmlspecialchars($_POST['cor']),
                quilometragem: $_POST['quilometragem'],
                descricao: htmlspecialchars($_POST['descricao']),
                valor: $_POST['valor'],
                dataHora: $_POST['dataHora'],
                estado: htmlspecialchars($_POST['estado']),
                cidade: htmlspecialchars($_POST['cidade'])
            );

            $fotos = [];
            foreach ($savedFileNames as $fileName) {
                $fotos[] = new FotoDTO(nomeArquivoFoto: $fileName);
            }

            $result = $anuncioService->register($anuncio, $fotos);

            if (!$result->success) {
                throw new Exception($result->message);
            }

            http_response_code(201);
            echo json_encode($result);
        } catch (Exception $e) {
            LogService::error($e->getMessage());

            if (!empty($savedFileNames)) {
                $fotoService->deletePhotos($savedFileNames);
            }

            http_response_code(400);
            echo json_encode(new MessageDTO(message: $e->getMessage(), success: false));
        }
        break;

    case 'listUser':
        $messageAnuncioService = $anuncioService->listUser();
        if (!$messageAnuncioService->success) {
            http_response_code(401);
            echo json_encode($messageAnuncioService);
            break;
        }

        $anuncios = $messageAnuncioService->obj;

        $mensagemFotoService = $fotoService->getPhotos($anuncios);
        $fotos = $mensagemFotoService->obj;

        if (count($anuncios) != count($fotos)) {
            http_response_code(500);
            echo json_encode(
                new MessageDTO(success: false, message: 'Algo deu errado, quantidade divergente de fotos e anuncios.')
            );
            return;
        }

        $anunciosCompletos = [];
        foreach ($anuncios as $anuncio) {
            $anuncioArray = (array) $anuncio;
            $anuncioArray['foto'] = $fotos[$anuncio->id] ?? null;
            $anuncioArray['id'] = $anuncio->id;
            $anunciosCompletos[] = $anuncioArray;
        }

        http_response_code(200);
        echo json_encode(
            new MessageDTO(
                success: true,
                message: 'listagem bem sucedida.',
                obj: $anunciosCompletos
            )
        );
        break;

    case 'delete':
        $anuncioId = $input['anuncioId'] ?? '';
        if ($anuncioId == '') {
            http_response_code(500);
            echo json_encode(
                new MessageDTO(
                    success: false,
                    message: 'id de anuncio não incluso no payload',
                )
            );
            break;
        }

        $isOwnerMessage = $anuncioService->isOwner($anuncioId);
        if (!$isOwnerMessage->success) {
            http_response_code(401);
            echo json_encode($isOwnerMessage);
            break;
        }

        $filepaths = $fotoService->getPhotosByAnuncioId($anuncioId);
        if (count($filepaths) == 0) {
            http_response_code(500);
            echo json_encode(
                new MessageDTO(
                    success: false,
                    message: 'O servidor falhou em encontrar o path das fotos no db.'
                )
            );
        }

        $mensagemService = $anuncioService->delete($anuncioId);
        if (!$mensagemService->success) {
            http_response_code(500);
            echo json_encode($mensagemService);
            break;
        }

        $fotosSuccess = $fotoService->deletePhotos($filepaths);
        if (!$fotosSuccess) {
            http_response_code(500);
            echo json_encode(
                new MessageDTO(
                    success: false,
                    message: 'O servidor não conseguiu deletar os arquivos das fotos.'
                )
            );
            break;
        }

        http_response_code(200);
        echo json_encode($mensagemService);
        break;

    case 'getById':
        $anuncioId = $input['anuncioId'] ?? null;
        if (!$anuncioId) {
            http_response_code(400);
            echo json_encode(new MessageDTO(
                success: false,
                message: 'ID do anúncio é obrigatório'
            ));
            break;
        }

        $messageAnuncioService = $anuncioService->getById($anuncioId);
        if (!$messageAnuncioService->success) {
            http_response_code(404);
            echo json_encode($messageAnuncioService);
            break;
        }

        $anuncio = $messageAnuncioService->obj;

        $mensagemFotoService = $fotoService->getAllPhotosByAnuncioId($anuncio['id']);
        $fotos = $mensagemFotoService->obj;

        $anuncioArray = $anuncio;
        $anuncioArray['fotos'] = $fotos ?? [];

        http_response_code(200);
        echo json_encode(new MessageDTO(
            success: true,
            message: 'Anúncio encontrado com sucesso',
            obj: $anuncioArray
        ));
        break;

    case 'interest':
        $anuncioId = $input['anuncioId'] ?? '';
        if ($anuncioId == '') {
            http_response_code(500);
            echo json_encode(
                new MessageDTO(
                    success: false,
                    message: 'id de anuncio não incluso no payload',
                )
            );
            break;
        }

        $messageAnuncioService = $anuncioService->getById($anuncioId);
        if (!$messageAnuncioService->success) {
            http_response_code(404);
            echo json_encode($messageAnuncioService);
            break;
        }

        $anuncio = $messageAnuncioService->obj;

        $mensagemFotoService = $fotoService->getAllPhotosByAnuncioId($anuncio['id']);
        $fotos = $mensagemFotoService->obj;

        $mensagemInteresseService = $interesseService->getInteressesByAnuncioId($anuncioId);
        if (!$mensagemInteresseService->success) {
            http_response_code(404);
            echo json_encode($mensagemInteresseService);
            break;
        }
        $interesses = $mensagemInteresseService->obj;

        $response = [
            'anuncio' => $anuncio,
            'fotos' => $fotos,
            'interesses' => $interesses
        ];
        http_response_code(200);
        echo json_encode($response);
        break;

    case 'listAll':
        $filters = [
            'marca' => $input['marca'] ?? '',
            'modelo' => $input['modelo'] ?? '',
            'cidade' => $input['cidade'] ?? '',
            'estado' => $input['estado'] ?? '',
            'search' => $input['search'] ?? ''
        ];

        $messageAnuncioService = $anuncioService->listAll($filters);
        if (!$messageAnuncioService->success) {
            http_response_code(500);
            echo json_encode($messageAnuncioService);
            break;
        }

        $anuncios = $messageAnuncioService->obj;

        $anunciosCompletos = [];
        foreach ($anuncios as $anuncio) {
            $anuncioArray = (array) $anuncio;
            
            $mensagemFotoService = $fotoService->getPhotos([$anuncio]);
            $fotos = $mensagemFotoService->obj;
            $anuncioArray['foto'] = $fotos[$anuncio->id] ?? null;
            $anuncioArray['id'] = $anuncio->id;
            $anunciosCompletos[] = $anuncioArray;
        }

        http_response_code(200);
        echo json_encode(
            new MessageDTO(
                success: true,
                message: 'Listagem de anúncios realizada com sucesso',
                obj: $anunciosCompletos
            )
        );
        break;

    case 'getFilterOptions':
        $field = $input['field'] ?? '';
        if (empty($field)) {
            http_response_code(400);
            echo json_encode(new MessageDTO(
                success: false,
                message: 'Campo é obrigatório'
            ));
            break;
        }

        $messageAnuncioService = $anuncioService->getDistinctValues($field);
        if (!$messageAnuncioService->success) {
            http_response_code(500);
            echo json_encode($messageAnuncioService);
            break;
        }

        http_response_code(200);
        echo json_encode($messageAnuncioService);
        break;

    default:
        LogService::error("unkown action at AnuncioController - {$action}");
        http_response_code(404);
        echo json_encode(new MessageDTO(message: "Action desconhecida - {$action}", success: false));
        break;
}
