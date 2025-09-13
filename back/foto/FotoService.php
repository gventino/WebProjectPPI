<?php

require_once __DIR__ . '/../internal/logger/LogService.php';
require_once __DIR__ . '/../messages/MessageDTO.php';
require_once __DIR__ . '/FotoRepository.php';

class FotoService
{
    private string $uploadDir;
    private FotoRepository $repository;

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../uploads/';
        $this->repository = new FotoRepository();
    }

    public function savePhotos(array $files): array
    {
        if (empty($files)) {
            throw new Exception('Nenhuma foto foi enviada.');
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
            throw new Exception('Nenhuma foto válida foi processada.');
        }

        return $savedFileNames;
    }

    public function getPhotosByAnuncioId(int $anuncioId): array
    {
        try {
            $filenames = $this->repository->getFotosByAnuncioId($anuncioId);
            return $filenames;
        } catch (Throwable $e) {
            return [];
        }
    }

    public function deletePhotos(array $filenames): bool
    {
        try {
            foreach ($filenames as $filename) {
                $fileToDelete = $this->uploadDir . $filename;
                if (file_exists($fileToDelete)) {
                    unlink($fileToDelete);
                }
            }
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function getPhotos(array $anuncios): MessageDTO
    {
        try {
            $photos = $this->repository->getPhotos($anuncios);
            return new MessageDTO(success: true, obj: $photos);
        } catch (Throwable $e) {
            return new MessageDTO(success: false, message: 'Erro ao resgatar fotos dos anuncios.');
        }
    }

    public function getAllPhotosByAnuncioId(int $anuncioId): MessageDTO
    {
        try {
            $photos = $this->repository->getFotosByAnuncioId($anuncioId);
            return new MessageDTO(success: true, obj: $photos);
        } catch (Throwable $e) {
            return new MessageDTO(success: false, message: 'Erro ao resgatar fotos do anúncio.');
        }
    }
}
