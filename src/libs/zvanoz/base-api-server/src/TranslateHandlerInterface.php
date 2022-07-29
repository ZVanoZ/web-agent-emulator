<?php


namespace ZVanoZ\BaseApiServer;


interface TranslateHandlerInterface
{
    function getTargetLang(): string;

    function getAllowLangs(): array;

    function translate(
        string $value
    ): string;

    function translateByArrayRef(
        string $value,
        array  &$translatesByLang
    ): string;

    function translateByArrayCopy(
        string $value,
        array  $translatesByLang
    ): string;
}