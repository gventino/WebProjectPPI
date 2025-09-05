<?php

require_once __DIR__ . "/AnuncianteRepository.php";
require_once __DIR__ . "/../messages/MessageDTO.php";

class AnuncianteService
{
    public AnuncianteRepository $repository;

    public function __construct()
    {
        $this->repository = new AnuncianteRepository();
    }

    public function register(AnuncianteDTO $anunciante): MessageDTO
    {

        $anunciante->senhaHash = password_hash($anunciante->senhaHash, PASSWORD_DEFAULT);
        $result = $this->repository->register($anunciante);

        return new MessageDTO(success: $result);
    }

    public function login(string $email, string $senha): MessageDTO
    {
        $anunciante = $this->repository->getByEmail($email);
        if ($anunciante == null) {
            return new MessageDTO(
                success: false,
                message: "Anunciante nao encontrado"
            );
        }

        if (!password_verify($senha, $anunciante->senhaHash)) {
            return new MessageDTO(
                success: false,
                message: "Senha incorreta"
            );
        }

        session_start();
        $_SESSION["user_id"] = $anunciante->id;
        $_SESSION["user_name"] = $anunciante->nome;
        $_SESSION["user_email"] = $anunciante->email;
        $_SESSION["user_cpf"] = $anunciante->cpf;
        $_SESSION["user_telefone"] = $anunciante->telefone;
        $_SESSION["logged_in"] = true;

        return new MessageDTO(
            success: true,
            message: "Login realizado com sucesso",
            obj: $anunciante,
        );
    }

    public function logout(): array
    {
        // fazer aqueles negocio de destroy e redirect pra algum lugar
        session_start();
        session_unset();
        session_destroy();

        return new MessageDTO(
            success: true,
            message: "Sessao destruida"
        );
    }

    public function checkSession(): array
    {
        session_start();
        $loggedIn = $_SESSION["logged_in"] ?? false;
        return new MessageDTO(success: true);
    }

}

