<?php

class InteresseDTO
{
    public function __construct(
        public string $nome,
        public string $telefone,
        public string $mensagem,
        public string $dataHora,
        public int $idAnuncio,
        public ?int $id = null
    ) {}
}
