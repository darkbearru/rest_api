<?php

namespace Abramenko\RestApi\Services;

class UserService extends Service
{
    public function Registration(array $params): bool
    {
        return true;
    }

    public function Login(array $params): bool
    {
        return true;
    }

    public function Logout(?array $params): bool
    {
        return true;
    }
}
