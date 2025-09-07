<?php

class FotoDTO
{
    public function __construct(
        public string $nomeArquivoFoto,
        public ?int $idAnuncio = null,
    ) {
    }
}
