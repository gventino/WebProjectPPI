<?php

require_once __DIR__ . "/../internal/logger/LogService.php";
require_once __DIR__ . "/../messages/MessageDTO.php";

class FotoService
{
    private string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../uploads/';
    }

    public function savePhotos(array $files): array
    {
        if (empty($files)) {
            throw new Exception("Nenhuma foto foi enviada.");
        }

        $savedFileNames = [];
        foreach ($files as $inputName => $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                continue;
            }

            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFilename = uniqid('foto_', true) . '.' . $fileExtension;
            $destination = $this->uploadDir . $newFilename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $savedFileNames[] = $newFilename;
            } else {
                $this->deletePhotos($savedFileNames);
                LogService::error("Erro ao mover o arquivo - {$inputName}");
                throw new Exception("Erro ao processar a foto {$inputName}.");
            }
        }

        if (empty($savedFileNames)) {
            throw new Exception("Nenhuma foto válida foi processada.");
        }

        return $savedFileNames;
    }

    public function deletePhotos(array $filenames): void
    {
        foreach ($filenames as $filename) {
            $fileToDelete = $this->uploadDir . $filename;
            if (file_exists($fileToDelete)) {
                unlink($fileToDelete);
            }
        }
    }
}
