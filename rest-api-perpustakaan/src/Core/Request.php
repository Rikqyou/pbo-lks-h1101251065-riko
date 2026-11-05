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
        $this->method  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri     = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $this->headers = getallheaders() ?: [];

        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);

        if (is_array($json)) {
            $this->body = array_merge($_REQUEST, $json);
        } else {
            $this->body = $_REQUEST;
        }
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

    public function getHeader(string $name): ?string
    {
        $nameLower = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $nameLower) {
                return $value;
            }
        }
        return null;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}
