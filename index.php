<?php
// Используем строгую типизацию
declare(strict_types=1);

$loader = require __DIR__ . '/vendor/autoload.php';

use Abramenko\RestApi\Application\Application;

$app = new Application();

$app->setDefaultRoute("routingTest");
$app->run();


function routingTest($variables)
{
    echo "Default Route is work<br />";
    echo "<pre>";
    print_r($variables);
    echo "</pre>";
}
