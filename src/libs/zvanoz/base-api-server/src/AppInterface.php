<?php


namespace ZVanoZ\BaseApiServer;


use ZVanoZ\BaseApiServer\Headers;

interface AppInterface
{
    public function getAppName(): string;

    public function getAppVersion(): string;

    public function getAllowApiVersions(): array;

    public function getTranslateHandler(): TranslateHandlerInterface;

    public function getRequest(): RequestInterface;

    function setOptions(Options $options): self;

    function run(
        ?Options $options = null
    ): void;

    function init(): void;

    function getXhrHeaders(): Headers;

    function checkApiVersion(): bool;

    /**
     * Метод предназначен для переопроеделения потомками класса
     * @return bool
     */
    function isOriginAllow(): bool;

    function exceptionHandler(\Throwable $e);

    function errorHandler(
        $errno,
        $errstr,
        $errfile = NULL,
        $errline = 0,
        $errcontext = []
    );

    function shutdownFunction();
}