<?php

namespace ZVanoZ\BaseApiServer\Monolog\Context;

class JournalContext
    extends \ZVanoZ\BaseApiServer\Monolog\Context
{
    public array|string $message='';

    public function toArray(): array
    {
        $result = parent::toArray();
        $result['className'] = self::class;
        $data = &$result['data'];
        $data = array_merge($data, [
            'message' => $this->message
        ]);
        return $result;
    }

    public function applyArray(array &$data): void
    {
        parent::applyArray($data);
        if (array_key_exists('message', $data)) {
            $this->message = $data['message'];
        }
    }
}