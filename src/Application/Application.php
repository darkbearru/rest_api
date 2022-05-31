<?php

namespace Abramenko\RestApi;


/**
 * Application
 */
class Application implements IApplication
{
    private array $_controllers = [];
    private \AltoRouter $_router;
    private string $_defaultRoute = '';


    public function __construct()
    {
        $this->_router = new \AltoRouter();
    }

    /**
     * addController
     *
     * @param  mixed $controller
     * @return void
     */
    public function addController(Controller $controller): void
    {
        $this->_controllers[(string) $controller] = $controller;
    }

    /**
     * run
     *
     * @return void
     */
    public function run(): void
    {
        $this->setupRoutes();
        if (!$this->matchRoutes() && !empty($this->_defaultRoute)) {
            $this->defaultRoute();
        }
    }


    /**
     * setupRoutes
     * В зависимости от добавленных контролеров настраиваем маршрутизацию
     *
     * @return void
     */
    protected function setupRoutes(): void
    {
        if (empty($this->_controllers)) return;

        foreach ($this->_controllers as $name => $controller) {
            $this->_router->addRoutes($controller->getRoutes());
        }
    }

    protected function matchRoutes(): bool
    {
        $match = $this->_router;
        return true;
    }

    protected function defaultRoute(): void
    {
        $this->_defaultRoute = '';
    }
}
