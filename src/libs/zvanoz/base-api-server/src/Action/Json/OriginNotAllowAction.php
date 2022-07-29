<?php

namespace ZVanoZ\BaseApiServer\Action\Json;

use ZVanoZ\BaseApiServer\{
    AppInterface,
    Response\JsonResponse
};

class OriginNotAllowAction
    extends \ZVanoZ\BaseApiServer\Action\Json\Http403Action
{
    public function execute(
        AppInterface $app
    ): JsonResponse
    {
        $result = new JsonResponse([
            'success' => false,
            'message' => $app
                ->getTranslateHandler()
                ->translate('Origin not allowed'),
        ], 403);
        return $result;
    }
}