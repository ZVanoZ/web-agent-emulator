<?php

namespace ZVanoZ\BaseApiServer\Action\Json;

use ZVanoZ\BaseApiServer\AppInterface;
use ZVanoZ\BaseApiServer\Response\JsonResponse;

class OptionsAction
    extends Http200Action
{
    public function execute(
        AppInterface $app
    ): JsonResponse
    {
        $result = parent::execute($app);
        $result->setItem(
            'message',
            $app->getTranslateHandler()->translate('Origin is allowed')
        );
        return $result;
    }
}