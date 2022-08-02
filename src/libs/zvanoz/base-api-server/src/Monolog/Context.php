<?php

namespace ZVanoZ\BaseApiServer\Monolog;

use ArrayAccess;

class Context
    implements ArrayAccess
{
    public string $traceId='';

    public function offsetExists(mixed $offset): bool
    {
        $result = isset($this->{$offset});
        return $result;
    }

    public function offsetGet(mixed $offset): mixed
    {
        $result = $this->{$offset};
        return $result;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('Unsupported operation');
    }

    public function toArray(): array
    {
        $result = [
            'className' => self::class,
            'data' => [
                'traceId' => $this->traceId
            ]
        ];
        return $result;
    }

    public function applyArray(array &$data): void
    {
        if (array_key_exists('traceId', $data)) {
            $this->traceId = $data['traceId'];
        }
    }

    public static function createFromArray(
        array $contextArray
    ): ?Context
    {
        if (!array_key_exists('className', $contextArray)) {
            return null;
        }
        $className = $contextArray['className'];
        $result = new $className();
        if ($result instanceof Context) {
            if (array_key_exists('data', $contextArray)) {
                $data = &$contextArray['data'];
                if (is_array($data)) {
                    $result->applyArray($data);
                }
            }
        }
        return $result;
    }

    public function __toString(): string
    {
        $result = json_encode($this->toArray(), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        return $result;
    }
}