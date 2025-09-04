<?php

class AnuncianteDTO
{
    
    public function __construct(
        public string $nome,
        public string $cpf,
        public string $email,
        public string $senhaHash,
        public string $telefone,
        public ?int $id = null 
    ) {
    }

    public static function anuncianteFromArray(array $array): AnuncianteDTO
    {
        return new self
        (
            id: $array["id"],
            nome: $array["nome"],
            cpf: $array["cpf"],
            email: $array["email"],
            senhaHash: $array["senha_hash"],
            telefone: $array["telefone"]
        );
    }
}
