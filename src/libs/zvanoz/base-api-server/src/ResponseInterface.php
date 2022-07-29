<?php


namespace ZVanoZ\BaseApiServer;


use ZVanoZ\BaseApiServer\Headers;

interface ResponseInterface
{

    function send(): void;
    function getBody(): string;
    function getHeaders(): Headers;
    function setHttpCode(int $httpCode): self;
    function getHttpCode(): int;
}