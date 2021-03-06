<?php
namespace Controllers;

use PHPUnit\Framework\TestCase;
use \Abramenko\RestApi\Controllers\Controller;

class ControllerTest extends TestCase
{
    public function testCanCreateAController(): void
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
            [["GET", "/api/users/list", "UserService#listUsers"]],
            $controller->getRoutes()
        );
    }

    public function testCanAddRouteOnPostMethod(): void
    {
        $controller = new Controller("User", "/api/users");
        $controller->routePost("/list", "UserService#listUsers");
        $this->assertEquals(
            [["POST", "/api/users/list", "UserService#listUsers"]],
            $controller->getRoutes()
        );
    }

    public function testCanAddRouteOnPutMethod(): void
    {
        $controller = new Controller("User", "/api/users");
        $controller->routePut("/list", "UserService#listUsers");
        $this->assertEquals(
            [["PUT", "/api/users/list", "UserService#listUsers"]],
            $controller->getRoutes()
        );
    }

    public function testCanAddRouteOnDeleteMethod(): void
    {
        $controller = new Controller("User", "/api/users");
        $controller->routeDelete("/list", "UserService#listUsers");
        $this->assertEquals(
            [["DELETE", "/api/users/list", "UserService#listUsers"]],
            $controller->getRoutes()
        );
    }

    public function testCanAddMultipleRoutes(): void
    {
        $controller = new Controller("User", "/api/users");
        $controller->routeGet("/profile", "UserService#userProfile");
        $controller->routePost("/login", "UserService#userLogin");
        $controller->routePost("/logout", "UserService#userLogout");
        $controller->routePost("/registration", "UserService#userRegistration");
        $this->assertEquals(
            [
                ["GET", "/api/users/profile", "UserService#userProfile"],
                ["POST", "/api/users/login", "UserService#userLogin"],
                ["POST", "/api/users/logout", "UserService#userLogout"],
                ["POST", "/api/users/registration", "UserService#userRegistration"],
            ],
            $controller->getRoutes()
        );
    }
}
