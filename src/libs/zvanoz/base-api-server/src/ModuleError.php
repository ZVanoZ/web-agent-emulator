<?php

namespace ZVanoZ\BaseApiServer;

use RuntimeException;

class ModuleError extends RuntimeException
{
    protected $context = [];

    public function &getContext()
    {
        return $this->context;
    }

    public function addContext($name, $value)
    {
        $this->context[$name] = $value;
        return $this;
    }

    public function __toString(): string
    {
        $result = parent::__toString();
        $result .= 'context: ' . json_encode($this->context);
        return $result;
    }
}