<?php

namespace Abramenko\RestApi\Services;

class TokenService extends Service
{
    public function Generate(object $payload): object|bool
    {
        return true;
    }
    public function Save(): bool
    {
        return true;
    }
}
