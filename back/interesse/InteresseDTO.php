<?php

class InteresseDTO
{
    public function __construct(
        public int $id,
        public string $nome,
        public string $telefone,
        public string $mensagem,
        public string $dataHora,
        public int $idAnuncio,
    ) {}
}
