<?php


namespace ZVanoZ\BaseApiServer;


use PhpParser\Node\Scalar\String_;
use ZVanoZ\BaseApiServer\Headers;
use Datetime;

interface RequestParamInterface
{
    function getName(): string;

    function getValueAsString(?string $defaultValue = null): ?string;

    function getValueAsInt(?int $defaultValue = null): ?int;

    function getValueAsDatetime(?Datetime $defaultValue = null): ?Datetime;
}