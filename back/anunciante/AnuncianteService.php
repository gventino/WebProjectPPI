<?php

/*
 * essa classe implementa os metodos da classe anunciante,
 * ela n tem atributos, somente métodos,
 * os atributos devem estar no dto, que deve ter somente atributos.
 *
 * isso se dá em razao da implementacao do php, alem de que
 * garante forte desacoplamento entre as partes,
 * favorece mudanças e afins.
 */
class AnuncianteService
{
    public function __construct(
        public AnuncianteDTO $anunciante,
    ) {}
}
