<?php

namespace Abramenko\RestApi\Controllers;

use Abramenko\RestApi\Controllers\Controller;
use Abramenko\RestApi\Services\UserService;

class UserController extends Controller
{
    private UserService $_userService;

    public function __construct(protected string $_name = 'Users', protected string $_route = '/api/users')
    {
        parent::__construct($_name, $_route);

        $this->_userService = new UserService();
        $this->setupRoutes();
    }

    /**
     * setupRoutes
     * Устанавливаем маршруты для пользователя
     *
     * @return void
     */
    protected function setupRoutes()
    {
        $this->routePost("/registration", function ($params) {
            $this->_userService->Registration($params);
        });
    }
}
