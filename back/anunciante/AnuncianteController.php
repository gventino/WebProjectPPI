<?php

require_once __DIR__ . "/../internal/logger/LogService.php";
require_once __DIR__ . "/AnuncianteService.php";
require_once __DIR__ . "/AnuncianteDTO.php";

header('Content-Type: application/json; charset=utf-8');

$stringJSON = file_get_contents('php://input');
$plainObject = json_decode($stringJSON);

$action = $plainObject->action ?? "empty";
$service = new AnuncianteService();
switch ($action) {
  case 'register':
    $anunciante = new AnuncianteDTO(
      nome: $plainObject->nome,
      cpf: $plainObject->cpf,
      email: $plainObject->email,
      senhaHash: $plainObject->senha,
      telefone: $plainObject->telefone
    );
    $result = $service->register($anunciante);
    echo json_encode($result);
    break;
  
  default:
     LogService::error("unkown action at AnuncianteController - {$action}");
     break;
}
