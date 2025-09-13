<?php

class LogService
{
    public static function error(string $str): void
    {
        error_log('[ERROR] ' . $str);
    }
}
