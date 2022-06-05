<?php
// Используем строгую типизацию
declare(strict_types=1);

$loader = require __DIR__ . '/vendor/autoload.php';

use Abramenko\RestApi\Application\Application;
use Abramenko\RestApi\Controllers\UserController;

$app = new Application();

$app->setDefaultRoute("routingTest");
$app->addController(new UserController());

try {
    if (!$app->run()) {
        echo "No Default Page<br />";
    }
} catch (Throwable$e) {
}


function routingTest($variables): void
{
    echo "Default Route is work<br />";
    echo "<pre>";
    print_r($variables);
    echo "</pre>";
}
