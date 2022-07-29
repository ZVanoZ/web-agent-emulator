<?php

namespace ZVanoZ\BaseApiServer\Action\Json;

use ZVanoZ\BaseApiServer\AppInterface;
use ZVanoZ\BaseApiServer\Response\JsonResponse;

class OptionsAction
    extends \ZVanoZ\BaseApiServer\Action\Json
{
    public function execute(
        AppInterface $app
    ): JsonResponse
    {
        $result = new JsonResponse([
            'success' => false,
            'message' => $app->getTranslateHandler()
                ->translate('Origin is allowed'),
        ], 200);
        return $result;
    }
}