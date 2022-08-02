<?php

namespace ZVanoZ\BaseApiServer\Monolog\Context;

class ErrorContext
    extends \ZVanoZ\BaseApiServer\Monolog\Context
{
    public ?string $errCode=null;
    public ?string $errFile=null;
    public ?int $errLine=null;
    //public int $errLevel;
    public ?string $errMessage=null;
    public ?array $context=null;

    public function toArray(): array
    {
        $result = parent::toArray();
        $result['className'] = self::class;
        $data = &$result['data'];
        $data = array_merge($data, [
            'errCode' => $this->errCode,
            'errFile' => $this->errFile,
            'errLine' => $this->errLine,
            'errMessage' => $this->errMessage,
            'context' => $this->context,
        ]);
        return $result;
    }

    public function applyArray(array &$data): void
    {
        parent::applyArray($data);
        foreach (['errCode', 'errFile', 'errLine', 'errMessage', 'context'] as $fieldName){
            if (array_key_exists($fieldName, $data)) {
                $this->offsetSet($fieldName, $data[$fieldName]);
            }
        }
    }
}