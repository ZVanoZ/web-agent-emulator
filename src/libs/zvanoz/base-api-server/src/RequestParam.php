<?php


namespace ZVanoZ\BaseApiServer;


use Datetime;
use ZVanoZ\BaseApiServer\Headers;

class RequestParam
    implements RequestParamInterface
{
    protected string $name;
    protected ?string $value = null;

    public function __construct(
        string  $name,
        ?string $value = null
    )
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    function getValueAsString(?string $defaultValue = null): ?string
    {
        return $this->value;
    }

    function getValueAsInt(?int $defaultValue = null): ?int
    {
        $result = $defaultValue;
        if ('' !== $this->value) {
            $result = intval($this->value);
        }
        return $result;
    }

    function getValueAsDatetime(?Datetime $defaultValue = null): ?Datetime
    {
        // TODO: Implement getValueAsDatetime() method.
    }
}