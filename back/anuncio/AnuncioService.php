<?php

require_once __DIR__ . "/AnuncioRepository.php";
require_once __DIR__ . "/../messages/MessageDTO.php";

class AnuncioService
{
    public AnuncioRepository $repository;

    public function __construct()
    {
        $this->repository = new AnuncioRepository();
    }

    public function register(AnuncioDTO $anuncio, array $fotos): MessageDTO
    {
        session_start();
        if (!isset($_SESSION["user_id"])) {
            return new MessageDTO(success: false, message: "O user_id esta faltando na sessao");
        }
        $idAnunciante = $_SESSION["user_id"];
        $anuncio->idAnunciante = $idAnunciante;

        $result = $this->repository->register($anuncio, $fotos);

        if (!$result) {
            return new MessageDTO(success: false, message: "Erro ao registrar o anúncio no banco de dados.");
        }

        return new MessageDTO(success: true, message: "Anúncio registrado com sucesso!");
    }

    public function listUser(): MessageDTO
    {
        session_start();
        if (!isset($_SESSION["user_id"])) {
            return new MessageDTO(success: false, message: "O user_id esta faltando na sessao");
        }
        $idAnunciante = $_SESSION["user_id"];
        try {
            $response = $this->repository->listUser($idAnunciante);
            return new MessageDTO(
                success: true,
                obj: $response
            );
        } catch (Throwable $e) {
            return new MessageDTO(success: false, message: "Erro ao listar anuncios.");
        }
    }
}
