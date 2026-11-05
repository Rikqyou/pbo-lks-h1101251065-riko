<?php

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;
    private array $body;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->uri = strtok($uri, '?');
        
        $input = file_get_contents('php://input');
        $this->body = json_decode($input, true) ?? $_POST;
    }

    public function getMethod(): string { return $this->method; }
    public function getUri(): string { return $this->uri; }
    public function getBody(): array { return $this->body; }
}
