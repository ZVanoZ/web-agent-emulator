<?php


namespace WebAgentServer;

use ZVanoZ\BaseApiServer\{
    ActionInterface,
    AppInterface,
};
use ZVanoZ\BaseApiServer\Action\Json\{Http404Action,
    ApiVersionNotSupportAction,
    OriginNotAllowAction,
    OptionsAction,
    ServerInfoAction
};
use WebAgentServer\Action\{
    PhotoAction
};

class Router
    extends \ZVanoZ\BaseApiServer\Router
{
    /**
     * @var App $app
     */
    protected App|AppInterface $app;

    /**
     * @return ActionInterface
     */
    public function route(): ActionInterface
    {
        $uri = $this->app->getRequest()->getUri();
        $method = $this->app->getRequest()->getMethod();

        if (!$this->app->isOriginAllow()) {
            return new OriginNotAllowAction();
        }
        /**
         * @var ActionInterface|null $result
         */
        $result = null;
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $result = new OptionsAction();
        } else {
            if ($method === 'GET') {
                if ($uri === '/') {
                    $result = new ServerInfoAction();
                }
            } else {
                if (!$this->isApiVersionAllow()) {
                    return new ApiVersionNotSupportAction();
                }
                if ($method === 'GET') {
                    if ($uri === '/photo') {
                        $result = new PhotoAction();
                    }
                }
            }
        }
        if (!$result instanceof ActionInterface) {
            $result = new Http404Action();
        }
        return $result;
    }
}