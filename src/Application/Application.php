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
    private bool $_redirectToRoot = true;

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
        // Выполняем все необходимые предварительные установки
        $this->setup();
        // Выполняем поиск маршрута и если найден выходим
        if ($this->matchRoutes()) return;

        // Если необходимо то в случае несовпадения с корневым путём
        // редиректим в корень
        if ($this->_redirectToRoot) $this->checkRootRedirect();

        // Если есть вывод по умолчанию, то делаем его
        if (!empty($this->_defaultRoute)) {
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

    protected function checkRootRedirect(): void
    {
        if (empty($_SERVER['REQUEST_URI'])) return;
        if ($_SERVER['REQUEST_URI'] !== '/') {
            header("HTTP/1.1 301 Moved Permanently");
            header("location: /\r\n");
            exit;
        }
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
     * fileGetContents
     * Оболочка для теста работы функции получения данных с потока
     *
     * @param  mixed $input
     * @return string
     */
    protected function fileGetContents($input = false): string
    {
        if (!empty($input)) return $input;
        return file_get_contents('php://input');
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
        $data = $this->FileGetContents();
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
     * @throws
     */
    protected function setupRoutes(): void
    {
        if (empty($this->_controllers)) return;

        $routes = [];
        foreach ($this->_controllers as $name => $controller) {
            $routes += $controller->getRoutes();
        }
        $this->_router->addRoutes($routes);
    }

    /**
     * matchRoutes
     * Проверяем есть ли соответствующий запросу маршрут
     *
     * @return bool
     */
    protected function matchRoutes(): bool
    {
        // Выполняем поиск роутингом подходящего маршрута
        $match = $this->_router->match();
        if (is_array($match) && is_callable($match['target'])) {
            $this->_requestVariables["variables"] += $match['params'];
            call_user_func_array(
                $match['target'],
                [$this->_requestVariables]
            );
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
            call_user_func_array($this->_defaultRoute, [$this->_requestVariables]);
        } catch (\Exception $e) {
        }
    }
}
