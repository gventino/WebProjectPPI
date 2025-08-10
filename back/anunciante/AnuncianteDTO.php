<?php
class AnuncianteDTO
{
    public function __construct(
        public int $id,
        public string $nome,
        public string $cpf,
        public string $email,
        public string $senhaHash,
        public string $telefone
    ) {}
}
