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

    protected function isApiVersionAllow(): bool
    {
        $request = $this->app->getRequest();
        $headers = $request->getHeaders();
        $apiVersion = $headers->get('X-API-VERSION');
        if (empty($apiVersion)) {
            return false;
        }
        $apiVersion = intval($apiVersion);
        $allowApiVersions = $this->app->getAllowApiVersions();
        if (!in_array($apiVersion, $allowApiVersions)) {
            return false;
        }
        return true;
    }

}