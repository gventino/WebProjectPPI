<?php

require_once __DIR__ . "/AnuncianteRepository.php";

class AnuncianteService
{
    public AnuncianteRepository $repository;

    public function __construct()
    {
      $this->repository = new AnuncianteRepository();
    }
    
    public function register(AnuncianteDTO $anunciante): bool
    {
  
      $anunciante->senhaHash = password_hash($anunciante->senhaHash, PASSWORD_DEFAULT);
      return $this->repository->register($anunciante);  
    }

    public function login(string $email, string $senha): array //talvez seja melhor retornar um objeto com Mensagem, AnuncianteDTO e Sucesso em vez de um array. Deixo isso pros backenders ai
    {
      $anunciante = $this->repository->getByEmail($email);
      if ($anunciante == null) {
        return [
          "success" => false,
          "message" => "Anunciante nao encontrado"
        ];
      }

      if (!password_verify($senha, $anunciante->senhaHash)) {
        return [
          "success" => false,
          "message" => "Senha incorreta"
        ];
      }

      session_start();
      $_SESSION["user_id"] = $anunciante->id;
      $_SESSION["user_name"] = $anunciante->nome;
      $_SESSION["user_email"] = $anunciante->email;
      $_SESSION["user_cpf"] = $anunciante->cpf;
      $_SESSION["user_telefone"] = $anunciante->telefone;
      $_SESSION["logged_in"] = true;
      
      return [
        "success" => true,
        "message" => "Login realizado com sucesso",
        "anunciante" => $anunciante
      ];
    }
    
    public function logout(): array
    {
      // fazer aqueles negocio de destroy e redirect pra algum lugar
      session_start();
      session_destroy();
      // tinha mais um session alguma coisa
      return [
        "success" => true,
        "message" => "Sessao destruida"
      ];
    }

    public function checkSession(): array
    {
      session_start();
      $loggedIn = $_SESSION["logged_in"] ?? false;
      return [
        "success" => $loggedIn,
      ];
    }

}