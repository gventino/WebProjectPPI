<?php

require_once __DIR__ . '/InteresseRepository.php';
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
                message: 'Algo deu errado durante a busca dos interesses. Erro: ' . $e->getMessage()
            );
        }
    }
}
