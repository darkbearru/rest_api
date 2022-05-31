<?php

namespace Abramenko\RestApi;

use \Abramenko\RestApi\Provider;
use \Abramenko\RestApi\Controller;


interface IApplication
{
    private array $_controllers = [];
    private \AltoRouter $_router;
    private string $_defaultRoute = '';

    //public function addProvider(Provider $provider): void;
    public function addController(Controller $controller): void;

    public function run(): void;
}
