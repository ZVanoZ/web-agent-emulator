<?php


namespace WebAgentServer;

use ZVanoZ\BaseApiServer\{ActionInterface, AppInterface, RequestParam\NotFoundParam};
use ZVanoZ\BaseApiServer\Action\Json\{Http404Action,
    ApiVersionNotSupportAction,
    Journal\GetListAction as JournalGetListAction,
    Journal\GetItemAction as JournalGetItemAction,
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
        $request = $this->app->getRequest();
        $headers = $request->getHeaders();
        $uri = $this->app->getRequest()->getUri();
        $method = $this->app->getRequest()->getMethod();
        $apiVersion = $headers->get('X-API-VERSION');

        if (!$this->app->isOriginAllow()) {
            return new OriginNotAllowAction();
        }
        /**
         * @var ActionInterface|null $result
         */
        $result = null;
        if ($method === 'OPTIONS') {
            $result = new OptionsAction();
        } else {
            if ($uri === '/') {
                if ($method === 'GET') {
                    $result = new ServerInfoAction();
                }
            } else {
                if (!$this->app->isApiVersionAllow($apiVersion)) {
                    return new ApiVersionNotSupportAction();
                }
                if ($method === 'GET') {
                    if ($uri === '/photo') {
                        $result = new PhotoAction();
                    } else if (preg_match('#/journal((\?{1}.*)|($))#', $uri)) {
                        $param = $request->getParamOrNull('id');

                        if (!is_null($param)
                            && !is_null($param->getValueAsInt())
                        ) {
                            $result = new JournalGetItemAction(
                                $this->app->getDb(),
                                [
                                    'id' => $param->getValueAsInt()
                                ]
                            );
                        } else {
                            $searchParams = [];
                            $param = $request->getParamOrNull('traceId');
                            if (!is_null($param)) {
                                $searchParams['trace_id'] = $param->getValueAsString();
                            }
                            $param = $request->getParamOrNull('timeFrom');
                            if (!is_null($param)) {
                                $searchParams['timeFrom'] = $param->getValueAsInt();
                            }
                            $param = $request->getParamOrNull('timeTo');
                            if (!is_null($param)) {
                                $searchParams['timeTo'] = $param->getValueAsInt();
                            }
                            $result = new JournalGetListAction(
                                $this->app->getDb(),
                                $searchParams
                            );
                        }
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