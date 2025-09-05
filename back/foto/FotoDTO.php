<?php

class FotoDTO
{
    public function __construct(
        public ?int $idAnuncio,
        public string $nomeArquivoFoto,
    ) {
    }
}
