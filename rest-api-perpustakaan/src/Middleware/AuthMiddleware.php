<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class AuthMiddleware
{
    private string $validToken;

    public function __construct()
    {
        $this->validToken = $_ENV['API_KEY'] ?? 'secret-token-123';
    }

    public function handle(Request $request): void
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
            Response::json([
                'success' => false,
                'message' => 'Akses ditolak: Token Authorization tidak ditemukan'
            ], 401);
        }

        $token = substr($authHeader, 7);

        if ($token !== $this->validToken) {
            Response::json([
                'success' => false,
                'message' => 'Akses ditolak: Token tidak valid'
            ], 403);
        }
    }
}
