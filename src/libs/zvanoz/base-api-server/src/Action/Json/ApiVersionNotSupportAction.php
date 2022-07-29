<?php

namespace ZVanoZ\BaseApiServer\Action\Json;

use ZVanoZ\BaseApiServer\{
    AppInterface,
    Response\JsonResponse
};

class ApiVersionNotSupportAction
    extends Http400Action
{
    public function execute(
        AppInterface $app
    ): JsonResponse
    {
        $allowApiVersions = $app->getAllowApiVersions();
        $result = parent::execute($app)
            ->setItem('allowVersions', $allowApiVersions);
        return $result;
    }
}