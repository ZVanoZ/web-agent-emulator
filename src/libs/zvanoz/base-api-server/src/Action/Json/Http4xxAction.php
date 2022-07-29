<?php

namespace ZVanoZ\BaseApiServer\Action\Json;

use ZVanoZ\BaseApiServer\{
    Action,
    AppInterface,
    ResponseInterface,
    Response\JsonResponse
};

class Http4xxAction
    extends Action
{
    protected string $translateMessageKey = 'http-400';

    public function execute(
        AppInterface $app
    ): JsonResponse
    {
        $translateMessageKey = $this->getTranslateMessageKey();
        $message = $app->getTranslateHandler()->translate($translateMessageKey);
        $result = (new JsonResponse())
            ->setHttpCode(404)
            ->setData([
                'success' => false,
                'message' => $message
            ]);
        return $result;
    }

    /**
     * @param string $translateMessageKey
     * @return Http4xxAction
     */
    public function setTranslateMessageKey(string $translateMessageKey): Http4xxAction
    {
        $this->translateMessageKey = $translateMessageKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getTranslateMessageKey(): string
    {
        return $this->translateMessageKey;
    }
}