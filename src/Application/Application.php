<?php

namespace Abramenko\RestApi\Application;

use Abramenko\RestApi\Controllers\Controller;

use AltoRouter;
use Abramenko\RestApi\Application\Exceptions\{ApplicationThrowable, ApplicationException};
use Exception;
use Throwable;

/**
 * Application
 */
class Application
{
    private array $_controllers = [];
    private AltoRouter $_router;
    private string|object|array $_defaultRoute = '';
    private array $_requestVariables = [];
    private bool $_redirectToRoot = true;

    public function __construct()
    {
        $this->_router = new AltoRouter();
    }

    /**
     * addController
     *
     * @param mixed $controller
     * @return void
     */
    public function addController(Controller $controller): void
    {
        $this->_controllers[(string)$controller] = $controller;
    }

    /**
     * run
     *
     * @return bool
     * @throws ApplicationException
     * @throws ApplicationThrowable
     */
    public function run(): bool
    {
        try {
            // Выполняем все необходимые предварительные установки
            $this->setup();
            // Выполняем поиск маршрута и если найден выходим
            if ($this->matchRoutes()) return true;

            // Если необходимо то в случае несовпадения с корневым путём
            // редиректим в корень
            if ($this->_redirectToRoot) $this->checkRootRedirect();

            // Если есть вывод по умолчанию, то делаем его
            if (!empty($this->_defaultRoute)) {
                return $this->defaultRoute();
            }
        } catch (Exception $e) {
            throw new ApplicationException($e);
        } catch (Throwable $e) {
            throw new ApplicationThrowable($e);
        }
        return false;
    }

    /**
     * setup
     * Запуск всех необходимых установочных процедур
     *
     * @return void
     * @throws ApplicationException
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
     * @throws ApplicationException
     */
    protected function setupRequestVariables(): void
    {
        $variables = (!empty($_REQUEST) ? $_REQUEST : []);
        $body = $this->getRequestBody();
        $this->_requestVariables = [...(array)$variables, ...(array)$body];
    }

    /**
     * getRequestBody
     * Получаем тело запроса, и сразу пробуем его преобразовать в json
     * Если не получается возвращаем просто строку
     *
     * @return string|array
     * @throws ApplicationException
     */
    protected function getRequestBody(): string|array
    {
        $data = $this->FileGetContents();
        if (empty($data)) return '';

        try {
            $data = json_decode($data, true);
        } catch (Exception $e) {
            throw new ApplicationException($e);
        }
        return $data;
    }

    /**
     * fileGetContents
     * Оболочка для теста работы функции получения данных с потока
     *
     * @param mixed $input // Используется только для тестов
     * @return string
     */
    protected function fileGetContents(mixed $input = false): string
    {
        if (!empty($input)) return $input;
        return file_get_contents('php://input');
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
        foreach ($this->_controllers as $controller) {
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
            $this->_requestVariables += $match['params'];
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

    protected function checkRootRedirect(): void
    {
        if (empty($_SERVER['REQUEST_URI'])) return;
        if ($_SERVER['REQUEST_URI'] !== '/') {
            header("HTTP/1.1 301 Moved Permanently");
            header("location: /\r\n");
            exit;
        }
    }

    protected function defaultRoute(): bool
    {
        // Выполняем только если указан маршрут «по умолчанию»
        if (empty($this->_defaultRoute)) return false;
        if (is_string($this->_defaultRoute)) {
            if (!function_exists($this->_defaultRoute)) return false;
        }

        call_user_func_array($this->_defaultRoute, [$this->_requestVariables]);

        return true;
    }

    public function setDefaultRoute(array|object|string $route): void
    {
        $this->_defaultRoute = $route;
    }

    public function getRequestVariables(): array
    {
        return $this->_requestVariables;
    }
}
