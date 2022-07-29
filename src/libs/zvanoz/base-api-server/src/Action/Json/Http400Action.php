<?php

namespace ZVanoZ\BaseApiServer\Action\Json;

class Http400Action
    extends Http4xxAction
{
    protected string $translateMessageKey = 'http-400';
}