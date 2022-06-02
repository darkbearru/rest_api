<?php

namespace Abramenko\RestApi\Services;

use Abramenko\RestApi\Libraries\DataBase;

class UserService extends Service
{
    public function __construct()
    {
        $this->_db = DataBase::getInstance();
    }
    public function Registration(array $params): bool
    {
        echo 'Registration: <pre>';
        print_r($params);
        echo '</pre>';
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
