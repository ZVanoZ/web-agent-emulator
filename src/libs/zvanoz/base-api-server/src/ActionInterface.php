<?php


namespace ZVanoZ\BaseApiServer;


use ZVanoZ\BaseApiServer\Headers;

interface ActionInterface
{
    public function execute(
        AppInterface     $app
    ): ResponseInterface;
}