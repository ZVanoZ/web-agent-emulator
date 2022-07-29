<?php


namespace ZVanoZ\BaseApiServer;

abstract class Router
    implements RouterInterface
{
    protected AppInterface $app;

    public function __construct(
        AppInterface $app
    )
    {
        $this->app = $app;
    }

    abstract public function route(): ActionInterface;
}