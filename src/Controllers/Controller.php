<?php

namespace Abramenko\RestApi;

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
     * routeGet
     * *Добавляем обработчик метода GET для определённого маршрута
     * Получение данных
     *
     * @param  string $route – /[pathname], где pathname название маршрута 
     *                          без корневого указанного при создании класса
     *                          Детальное описание и возможные варианты http://altorouter.com/usage/mapping-routes.html
     * @param  string $action – в формате "ClassName#Method"
     * @return void
     */
    public function routeGet(string $route, string $action): void
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
     * @param  string $action
     * @return void
     */
    public function routePost(string $route, string $action): void
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
     * @param  string $action
     * @return void
     */
    public function routePut(string $route, string $action): void
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
     * @param  string $action
     * @return void
     */
    public function routeDelete(string $route, string $action): void
    {
        $this->addRoute('DELETE', $route, $action);
    }

    /**
     * addRoute
     * *Массив в формате для пакетного добавления \AltoRouter->addRoutes
     * Пример: ['GET','/users/[i:id]', 'users#update', 'update_user']

     * @param  string $method
     * @param  string $route
     * @param  string $action
     * @return void
     */
    protected function addRoute(string $method, string $route, string $action): void
    {
        $this->_routes[] = [
            $method,
            $this->_route . $route,
            $action,
            str_replace('#', '_', strtolower($action))
        ];
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
}
