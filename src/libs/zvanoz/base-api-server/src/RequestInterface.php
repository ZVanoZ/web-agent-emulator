<?php


namespace ZVanoZ\BaseApiServer;


use ZVanoZ\BaseApiServer\Headers;

interface RequestInterface
{
    function getHeaders():Headers;
    function getMethod(): string;
    function getUri(): string;
    function getOrigin(): ?string;
}