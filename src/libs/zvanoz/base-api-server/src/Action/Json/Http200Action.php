<?php

namespace ZVanoZ\BaseApiServer\Action\Json;

use ZVanoZ\BaseApiServer\{
    Action,
    AppInterface,
    ResponseInterface,
    Response\JsonResponse
};

class Http200Action
    extends Action
{
    public function execute(
        AppInterface $app
    ): JsonResponse
    {
        $result = (new JsonResponse())
            ->setHttpCode(200)
            ->setData([
                'success' => true
            ]);
        return $result;
    }
}