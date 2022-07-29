<?php


namespace ZVanoZ\BaseApiServer;

abstract class Response
    implements ResponseInterface
{

    protected int $httpCode = 200;
    protected Headers $headers;

    public function __construct()
    {
        $this->headers = new Headers();
    }

    public function send(): void
    {
        $httpCode = $this->getHttpCode();
        $headers = $this->headers->getListRef();
        $body = $this->getBody();

        http_response_code($httpCode);
        foreach ($headers as $headerName => $headerVal) {
            $tmp = $headerName . ':' . $headerVal;
            header($tmp);
        }
        echo $body;
    }

    abstract public function getBody(): string;

    /**
     * @return Headers
     */
    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    /**
     * @param int $httpCode
     * @return Response
     */
    public function setHttpCode(int $httpCode): Response
    {
        $this->httpCode = $httpCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}