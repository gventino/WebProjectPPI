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
}
