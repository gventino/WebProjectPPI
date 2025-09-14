<?php
require_once __DIR__ . '/InteresseDTO.php';
require_once __DIR__ . '/InteresseService.php';
require_once __DIR__ . '/../messages/MessageDTO.php';

header('Content-Type: application/json; charset=utf-8');

$interesseService = new InteresseService();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? $_POST['action'] ?? 'empty';

switch ($action) {
    case 'register':
        $anuncioId = $_POST['anuncioId'] ?? '';
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

        $interesse = new InteresseDTO(
            nome: htmlspecialchars($_POST['nome']),
            telefone: htmlspecialchars($_POST['telefone']),
            mensagem: htmlspecialchars($_POST['mensagem']),
            dataHora: htmlspecialchars($_POST['dataHora']),
            idAnuncio: htmlspecialchars($_POST['idAnuncio'])
        );

        $mensagemInteresseService = $interesseService->registerInterest($interesse);
        if (!$mensagemInteresseService->success) {
            http_response_code(500);
            echo json_encode($mensagemInteresseService);
            break;
        }

        http_response_code(200);
        echo json_encode($mensagemInteresseService);
        break;

    default:
        LogService::error("unkown action at AnuncioController - {$action}");
        http_response_code(404);
        echo json_encode(new MessageDTO(message: "Action desconhecida - {$action}", success: false));
        break;
}
