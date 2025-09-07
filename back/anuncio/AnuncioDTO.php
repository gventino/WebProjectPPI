<?php

class AnuncioDTO
{
    public function __construct(
        public string $marca,
        public string $modelo,
        public int $ano,
        public string $cor,
        public int $quilometragem,
        public string $descricao,
        public float $valor,
        public string $dataHora,
        public string $estado,
        public string $cidade,
        public ?int $idAnunciante = null,
        public ?int $id = null,
    ) {
    }
}
