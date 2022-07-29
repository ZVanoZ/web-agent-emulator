<?php


namespace ZVanoZ\BaseApiServer;


use ZVanoZ\BaseApiServer\Headers;

class Request
    implements RequestInterface
{
    protected Headers $headers;

    public function __construct()
    {
        $this->headers = new Headers();
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    public function getMethod(): string
    {
        $result = $_SERVER['REQUEST_METHOD'];
        return $result;
    }

    public function getUri(): string
    {
        $result = $_SERVER['REQUEST_URI'];
        $this->normalizeUri($result);
        return $result;
    }

    protected function normalizeUri(&$uri): void
    {
        $uri = strtolower($uri);
    }

    public function getOrigin(): ?string
    {
        $result = null;
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $result = $_SERVER['HTTP_ORIGIN'];
        }
        return $result;
    }
}