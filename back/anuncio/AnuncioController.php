<?php

require_once __DIR__ . "/../internal/logger/LogService.php";
require_once __DIR__ . "/AnuncioDTO.php";
require_once __DIR__ . "/AnuncioService.php";
require_once __DIR__ . "/../foto/FotoDTO.php";
require_once __DIR__ . "/../messages/MessageDTO.php";

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? "empty";
$service = new AnuncioService();

function verifyFields(): bool
{
    $expr = isset($_POST['marca'])
      && isset($_POST['modelo'])
      && isset($_POST['ano'])
      && isset($_POST['cor'])
      && isset($_POST['quilometragem'])
      && isset($_POST['descricao'])
      && isset($_POST['valor'])
      && isset($_POST['dataHora'])
      && isset($_POST['estado'])
      && isset($_POST['cidade']);
    return $expr;
}

switch ($action) {
    case 'register':
        $upload_dir = __DIR__ . '/../uploads/';
        $foto_paths = [];

        foreach ($_FILES as $input_name => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_filename = uniqid('foto_', true) . '.' . $file_extension;
                $destination = $upload_dir . $new_filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $foto_paths[$input_name] = $new_filename;
                } else {
                    LogService::error("Error moving the file - {$input_name}");
                    echo json_encode(new MessageDTO(message: "Erro ao processar a foto {$input_name}.", success: false));
                    exit;
                }
            }
        }

        if (empty($foto_paths)) {
            echo json_encode(new MessageDTO(message: "Nenhuma foto foi enviada.", success: false));
            exit;
        }

        if (!verifyFields()) {
            echo json_encode(new MessageDTO(message: "Houve algum campo faltante no formulario.", success: false));
            exit;
        }

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
        foreach ($foto_paths as $foto_path) {
            $fotos[] = new FotoDTO(
                nomeArquivoFoto: $foto_path
            );
        }

        $result = $service->register($anuncio, $fotos);
        if (!$result->success) {
            foreach ($foto_paths as $filename) {
                $file_to_delete = $upload_dir . $filename;
                if (file_exists($file_to_delete)) {
                    unlink($file_to_delete);
                }
            }
        }

        echo json_encode($result);
        break;

    default:
        LogService::error("unkown action at AnuncioController - {$action}");
        echo json_encode(new MessageDTO(message: "Action desconhecida - {$action}", success: false));
        break;
}
