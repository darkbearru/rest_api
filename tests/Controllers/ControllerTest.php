<?php

use PHPUnit\Framework\TestCase;
use \Abramenko\RestApi\Controllers\Controller;

class ControllerTest extends TestCase
{
    public function testCanCreateController(): void
    {
        $controller = new Controller();
        $this->assertEquals(
            'Controller',
            (string)$controller
        );
    }
    public function testCanCreateControllerWithCustomNameParameter(): void
    {
        $controller = new Controller("User");
        $this->assertEquals(
            'User',
            (string)$controller
        );
    }
    public function testCanCreateControllerWithCustomRouteParameter(): void
    {
        $controller = new Controller("User", "/api/users");
        $this->assertEquals(
            '/api/users',
            $controller->getRoute()
        );
    }
    public function testCanAddRouteOnGetMethod(): void
    {
        $controller = new Controller("User", "/api/users");
        $controller->routeGet("/list", "UserService#listUsers");
        $this->assertEquals(
            [["GET", "/api/users/list", "UserService#listUsers", "userservice_listusers"]],
            $controller->getRoutes()
        );
    }
}
