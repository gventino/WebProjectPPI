<?php

require_once __DIR__ . '/../internal/logger/LogService.php';
require_once __DIR__ . '/AnuncianteService.php';
require_once __DIR__ . '/AnuncianteDTO.php';
require_once __DIR__ . '/../messages/MessageDTO.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$service = new AnuncianteService();

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'empty';
    switch ($action) {
        case 'checkSession':
            $result = $service->checkSession();
            http_response_code($result->success ? 200 : 401);
            echo json_encode($result);
            break;
        default:
            LogService::error("unkown action at AnuncianteController (GET) - {$action}");
            http_response_code(404);
            echo json_encode(new MessageDTO(success: false, message: "Action desconhecida - {$action}"));
            break;
    }
    exit;
}

$stringJSON = file_get_contents('php://input');
$plainObject = json_decode($stringJSON);

$action = $plainObject->action ?? 'empty';
switch ($action) {
    case 'register':
        $anunciante = new AnuncianteDTO(
            nome: htmlspecialchars($plainObject->nome),
            cpf: htmlspecialchars($plainObject->cpf),
            email: htmlspecialchars($plainObject->email),
            senhaHash: $plainObject->senha,
            telefone: htmlspecialchars($plainObject->telefone),
            id: null
        );
        $result = $service->register($anunciante);
        http_response_code($result->success ? 201 : 400);
        echo json_encode($result);
        break;

    case 'login':
        $result = $service->login($plainObject->email, $plainObject->senha);
        http_response_code($result->success ? 200 : 401);
        echo json_encode($result);
        break;

    case 'logout':
        $result = $service->logout();
        http_response_code($result->success ? 200 : 400);
        echo json_encode($result);
        break;

    default:
        LogService::error("unkown action at AnuncianteController - {$action}");
        http_response_code(404);
        echo json_encode(new MessageDTO(success: false, message: "Action desconhecida - {$action}"));
        break;
}
