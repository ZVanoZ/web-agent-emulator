<?php

namespace ZVanoZ\BaseApiServer\Action\Json;

use ZVanoZ\BaseApiServer\{
    AppInterface,
    Response\JsonResponse
};

class ServerInfoAction
    extends \ZVanoZ\BaseApiServer\Action\Json\Http200Action
{
    public function execute(
        AppInterface $app
    ): JsonResponse
    {
        $result = parent::execute($app);
        $appName = $app->getAppName();
        $appVersion = $app->getAppVersion();
        $allowApiVersions = $app->getAllowApiVersions();
        $result->setItems([
            'appName' => $appName,
            'appVersion' => $appVersion,
            'allowApiVersions' => $allowApiVersions
        ]);
        $result->setItems([
            'test1/node1/node2' => 'test1/value'
        ]);
        $result->setItems([
            'test2/node1/node2' => 'test2/value'
        ], '/');

        return $result;
    }
}