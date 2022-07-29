<?php


namespace WebAgentServer;

use ZVanoZ\BaseApiServer\{ActionInterface, Request, RequestInterface, Response, RouterInterface};
use ZVanoZ\BaseApiServer\Headers;

class App
    extends \ZVanoZ\BaseApiServer\App
{
    protected array $allowedApiVersions = [1, 2, 3];

    protected function createRouter(): void
    {
        $this->router = new Router($this);
    }

    protected function createTranslateHandler(): void
    {
        $this->translateHandler = new TranslateHandler();
    }

    public function getXhrHeaders(): Headers
    {
        $result = parent::getXhrHeaders();
        $origin = $this->getRequest()->getOrigin();
        $result->set('Access-Control-Allow-Origin', $origin);
        return $result;
    }

    public function isOriginAllow(): bool
    {
        // @TODO: add check origin here
        return true;
    }
}