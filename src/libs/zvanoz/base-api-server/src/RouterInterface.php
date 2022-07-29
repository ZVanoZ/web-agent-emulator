<?php


namespace ZVanoZ\BaseApiServer;

interface RouterInterface
{
    public function route(): ActionInterface;
}