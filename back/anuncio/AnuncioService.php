<?php

require_once __DIR__ . "/AnuncioRepository.php";
require_once __DIR__ . "/../messages/MessageDTO.php";

class AnuncioService
{
    public AnuncioRepository $repository;

    public function __construct(
    ) {
        $this->repository = new AnuncioRepository();
    }

    public function register(AnuncioDTO $anuncio, FotoDTO $foto): MessageDTO
    {
        $result = $this->repository->register($anuncio, $foto);
        return new MessageDTO(success: $result);
    }
}
