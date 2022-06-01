<?php

namespace Abramenko\RestApi\Application;

use \Abramenko\RestApi\Controllers\Controller;


interface IApplication
{
    //public function addProvider(Provider $provider): void;
    public function addController(Controller $controller): void;

    public function run(): void;
}
