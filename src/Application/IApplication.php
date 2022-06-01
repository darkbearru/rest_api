<?php

namespace Abramenko\RestApi;

use \Abramenko\RestApi\Controller;


interface IApplication
{
    private array $_controllers = [];
    private \AltoRouter $_router;
    private string | array $_defaultRoute = '';

    //public function addProvider(Provider $provider): void;
    public function addController(Controller $controller): void;

    public function run(): void;
}
