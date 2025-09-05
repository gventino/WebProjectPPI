<?php

class DatabaseResponseDTO
{
    public function __construct(
        public bool $success,
        public PDOStatement $stmt,
    ) {
    }
}
