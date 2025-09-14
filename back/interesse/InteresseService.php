<?php

require_once __DIR__ . '/InteresseRepository.php';
require_once __DIR__ . '/InteresseDTO.php';
require_once __DIR__ . '/../messages/MessageDTO.php';

class InteresseService
{
    public InteresseRepository $repository;

    public function __construct()
    {
        $this->repository = new InteresseRepository();
    }

    public function getInteressesByAnuncioId(int $anuncioId): MessageDTO
    {
        try {
            $interesses = $this->repository->getInteressesByAnuncioId($anuncioId);
            return new MessageDTO(
                success: true,
                obj: $interesses
            );
        } catch (Throwable $e) {
            return new MessageDTO(
                success: false,
                message: "Algo deu errado durante a busca dos interesses - {$e->getMessage()}"
            );
        }
    }

    public function registerInterest(InteresseDTO $interesse): MessageDTO
    {
        try {
            $success = $this->repository->registerInteresse($interesse);
            if (!$success) {
                throw new Exception('Could not register interesse at anuncio.');
            }
            return new MessageDTO(success: true);
        } catch (Throwable $e) {
            return new MessageDTO(
                success: false,
                message: "Algo deu errado durante o registro de interesses - {$e->getMessage()}"
            );
        }
    }
}
