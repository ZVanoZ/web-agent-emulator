<?php


namespace ZVanoZ\BaseApiServer;

use Psr\Log\LoggerInterface;

interface AppInterface
{
    public function getAppName(): string;

    public function getAppVersion(): string;

    public function getAllowApiVersions(): array;

    public function getTraceId(): ?string;

    public function getTranslateHandler(): ?TranslateHandlerInterface;

    public function getRequest(): ?RequestInterface;

    public function getLogger(): ?LoggerInterface;

    function setOptions(Options $options): self;

    function run(?Options $options = null): void;

    function getXhrHeaders(): Headers;

    /**
     * @param {string|int|null} $apiVersion
     * @return bool
     */
    public function isApiVersionAllow($apiVersion): bool;

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