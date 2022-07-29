<?php

namespace ZVanoZ\BaseApiServer\Action\Json;

class Http404Action
    extends Http4xxAction
{
    protected string $translateMessageKey = 'http-404';
}