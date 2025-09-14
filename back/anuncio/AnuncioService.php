<?php

require_once __DIR__ . '/AnuncioRepository.php';
require_once __DIR__ . '/../messages/MessageDTO.php';

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
        if (!isset($_SESSION['user_id'])) {
            return new MessageDTO(success: false, message: 'O user_id esta faltando na sessao');
        }
        $idAnunciante = $_SESSION['user_id'];
        $anuncio->idAnunciante = $idAnunciante;

        $result = $this->repository->register($anuncio, $fotos);

        if (!$result) {
            return new MessageDTO(success: false, message: 'Erro ao registrar o anúncio no banco de dados.');
        }

        return new MessageDTO(success: true, message: 'Anúncio registrado com sucesso!');
    }

    public function listUser(): MessageDTO
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return new MessageDTO(success: false, message: 'O user_id esta faltando na sessao');
        }
        $idAnunciante = $_SESSION['user_id'];
        try {
            $response = $this->repository->listUser($idAnunciante);
            return new MessageDTO(
                success: true,
                obj: $response
            );
        } catch (Throwable $e) {
            return new MessageDTO(success: false, message: 'Erro ao listar anuncios.');
        }
    }

    public function isOwner(int $anuncioId): MessageDTO
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return new MessageDTO(success: false, message: 'O user_id esta faltando na sessao');
        }
        $anuncianteId = $_SESSION['user_id'];

        try {
            $success = $this->repository->isOwner($anuncianteId, $anuncioId);
            if (!$success) {
                return new MessageDTO(
                    success: false,
                    message: 'O usuário não é dono do anuncio que está tentando deletar'
                );
            }
            return new MessageDTO(
                success: true,
            );
        } catch (Throwable $e) {
            return new MessageDTO(
                success: false,
                message: "Erro ao verificar posse do anuncio - {$e->getMessage()}"
            );
        }
    }

    public function delete(int $anuncioId): MessageDTO
    {
        try {
            $success = $this->repository->delete($anuncioId);
            if (!$success) {
                throw Exception("Could not delete anuncio and foto with anuncioId = $anuncioId");
            }

            return new MessageDTO(
                success: true,
                message: 'Anuncio e foto deletados com sucesso'
            );
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();
            return new MessageDTO(
                success: false,
                message: "Erro ao deletar anuncio e foto - $errorMessage"
            );
        }
    }

    public function getById(int $anuncioId): MessageDTO
    {
        try {
            $anuncio = $this->repository->getById($anuncioId);
            if (!$anuncio) {
                return new MessageDTO(
                    success: false,
                    message: 'Anúncio não encontrado'
                );
            }

            return new MessageDTO(
                success: true,
                message: 'Anúncio encontrado com sucesso',
                obj: (array) $anuncio
            );
        } catch (Throwable $e) {
            return new MessageDTO(
                success: false,
                message: 'Erro ao buscar anúncio - ' . $e->getMessage()
            );
        }
    }
}
