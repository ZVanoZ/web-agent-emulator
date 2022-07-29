<?php

namespace ZVanoZ\BaseApiServer\Action\Json;

class Http403Action
    extends Http4xxAction
{
    protected string $translateMessageKey = 'http-403';
}