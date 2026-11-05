<?php

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;
    private array $headers;
    private array $body;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->uri = strtok($uri, '?');

        // Mengambil header dengan aman di mode CLI maupun Web Server
        if (function_exists('getallheaders')) {
            $this->headers = getallheaders() ?: [];
        } else {
            $this->headers = [];
            foreach ($_SERVER as $key => $value) {
                if (str_starts_with($key, 'HTTP_')) {
                    $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                    $this->headers[$header] = $value;
                }
            }
        }

        // Parsing body
        $input = file_get_contents('php://input');
        $this->body = json_decode($input, true) ?? $_POST ?? [];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}