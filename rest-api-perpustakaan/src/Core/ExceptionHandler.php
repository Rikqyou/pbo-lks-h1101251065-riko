<?php

namespace App\Core;

use Throwable;

class ExceptionHandler
{
    public static function handle(Throwable $e): void
    {
        $code = $e->getCode();
        $statusCode = (is_numeric($code) && $code >= 400 && $code < 600) ? (int)$code : 500;

        Response::json([
            'success' => false,
            'message' => 'Terjadi kesalahan pada server',
            'error'   => $e->getMessage()
        ], $statusCode);
    }
}
