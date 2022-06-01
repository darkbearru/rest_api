<?php

namespace Abramenko\RestApi\Application;

use Abramenko\RestApi\Controllers\Controller;

/**
 * Application
 */
class Application implements IApplication, IRequest
{
    private array $_controllers = [];
    private \AltoRouter $_router;
    private string | array  $_defaultRoute = '';
    private array $_requestVariables = [];

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
        $this->setup();
        if (!$this->matchRoutes() && !empty($this->_defaultRoute)) {
            $this->defaultRoute();
        }
    }

    public function setDefaultRoute(array|string $route): void
    {
        $this->_defaultRoute = $route;
    }


    public function getRequestVariables(): array
    {
        return $this->_requestVariables;
    }


    /**
     * setup
     * Запуск всех необходимых установочных процедур
     *
     * @return void
     */
    protected function setup(): void
    {
        $this->setupRequestVariables();
        $this->setupRoutes();
    }


    /**
     * setupVariables
     * Получаем все возможные элементы окружения
     *
     * @return void
     */
    protected function setupRequestVariables(): void
    {
        $this->_requestVariables = [
            "variables"  => (!empty($_REQUEST) ? $_REQUEST : []),
            "body"       => $this->getRequestBody()
        ];
    }

    /**
     * getRequestBody
     * Получаем тело запроса, и сразу пробуем его преобразовать в json
     * Если не получается возвращаем просто строку
     * 
     * @return string|array
     */
    protected function getRequestBody(): string|array
    {
        $data = file_get_contents('php://input');
        if (empty($data)) return '';

        try {
            $data = json_decode($data, true);
        } catch (\Exception $e) {
            throw $e;
        }
        return $data;
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
        // Выполняем поиск роутингом подходящего маршрута
        $match = $this->_router->match();

        if (is_array($match) && is_callable($match['target'])) {
            call_user_func_array($match['target'], $match['params']);
            return true;
        }

        // Муршрут не найден
        //header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
        return false;
    }

    protected function defaultRoute(): void
    {
        // Выполняем только если указан маршрут «по умолчанию»
        if (empty($this->_defaultRoute)) return;
        try {
            call_user_func_array($this->_defaultRoute, $this->_requestVariables);
        } catch (\Exception $e) {
        }
    }
}
