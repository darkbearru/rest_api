<?php

namespace Abramenko\RestApi\Controllers;

/**
 * Controller
 */
class Controller
{
    protected array $_routes = [];
    /**
     * __construct
     *
     * @param string $name
     * @param string $prefix
     * @return void
     */
    public function __construct(protected string $_name = 'Controller', protected string $_route = '/api')
    {
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->_name;
    }

    /**
     * getRoute
     * *Получаем корневой маршрут с которым будет работать контроллер
     *
     * @return string
     */
    public function getRoute(): string
    {
        return $this->_route;
    }

    /**
     * getRoutes
     * *Получаем полный список маршрутов и их обработчиков
     * Массив в формате для пакетного добавления \AltoRouter->addRoutes
     * Пример: ['GET','/users/[i:id]', 'users#update', 'update_user']
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->_routes;
    }

    /**
     * routeGet
     * *Добавляем обработчик метода GET для определённого маршрута
     * Получение данных
     *
     * @param  string $route – /[pathname], где pathname название маршрута 
     *                          без корневого указанного при создании класса
     *                          Детальное описание и возможные варианты http://altorouter.com/usage/mapping-routes.html
     * @param  string|array $action – Наименование функции в строке или сама функция
     * @return void
     */
    protected function routeGet(string $route, string|object $action): void
    {
        $this->addRoute('GET', $route, $action);
    }

    /**
     * routePost
     * *Добавляем обработчик метода POST для определённого маршрута
     * Создание/добавление данных
     * P.S. Детальное описание параметров смотри в GET
     *
     * @param  string $route
     * @param  string $action|array
     * @return void
     */
    protected function routePost(string $route, string|object $action): void
    {
        $this->addRoute('POST', $route, $action);
    }

    /**
     * routePut
     * *Добавляем обработчик метода PUT для определённого маршрута
     * Изменение данных
     * P.S. Детальное описание параметров смотри в GET
     *
     * @param  string $route
     * @param  string $action|array
     * @return void
     */
    protected function routePut(string $route, string|object $action): void
    {
        $this->addRoute('PUT', $route, $action);
    }

    /**
     * routeDelete
     * *Добавляем обработчик метода POST для определённого маршрута
     * Удаление данных
     * P.S. Детальное описание параметров смотри в GET
     *
     * @param  string $route
     * @param  string $action|array
     * @return void
     */
    protected function routeDelete(string $route, string|object $action): void
    {
        $this->addRoute('DELETE', $route, $action);
    }

    /**
     * addRoute
     * *Массив в формате для пакетного добавления \AltoRouter->addRoutes
     * Пример: ['GET','/users/[i:id]', 'users#update', 'update_user']

     * @param  string $method
     * @param  string $route
     * @param  string $action|array
     * @return void
     */
    protected function addRoute(string $method, string $route, string|object $action): void
    {
        $this->_routes[] = [
            $method,
            $this->_route . $route,
            $action
        ];
    }

    protected function responseJSON(array|object $response): void
    {
        header('Content-type: application/json; charset=utf-8');
        header('Last-Modified: ' . date('Y-m-d H:i:s'));
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    protected function responseHTML(array|object|string $response): void
    {
        header('Content-Type: text/html; charset=UTF-8');
        header('Last-Modified: ' . date('Y-m-d H:i:s'));
        if (isset($this->_templateEngine)) {
            $this->showTemplate($response);
            return;
        }
        if (is_object($response) || is_array($response)) {
            $response = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        echo $response;
    }

    /**
     * showTemplate
     * Функция для переопределения при наследовании, 
     * внутри которой можно будет делать вызов любого шаблонизатора
     *
     * @param  mixed $response
     * @return void
     */
    protected function showTemplate(array|object|string $response): void
    {
    }
}
