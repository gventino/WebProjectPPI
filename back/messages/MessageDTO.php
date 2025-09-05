<?php

class MessageDTO
{
    public function __construct(
        public bool $success,
        public ?string $message = "",
        public ?MessageObjInterface $obj = null,
    ) {
    }
}
