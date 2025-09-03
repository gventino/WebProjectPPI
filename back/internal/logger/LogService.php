<?php

class LogService
{
    public static function error(string $str): void
    {
        $now = date('H:i:s');
        echo "{$now} - " . "ERROR: " . $str . PHP_EOL;
    }
}
