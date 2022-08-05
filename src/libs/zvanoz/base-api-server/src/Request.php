<?php


namespace ZVanoZ\BaseApiServer;


use ZVanoZ\BaseApiServer\Headers;
use ZVanoZ\BaseApiServer\RequestParam\NotFoundParam;

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

    function getParam(string $name): RequestParamInterface
    {
        $result = null;
        if (array_key_exists($name, $_GET)) {
            $result = new RequestParam($name, $_GET[$name]);
        } elseif (array_key_exists($name, $_REQUEST)){
            $result = new RequestParam($name, $_REQUEST[$name]);
        } else{
            $result = new NotFoundParam($name);
        }
        return $result;
    }

    function getParamOrNull(string $name): ?RequestParamInterface
    {
        $result = $this->getParam($name);
        if ($result instanceof NotFoundParam) {
            return null;
        }
        return $result;
    }
}