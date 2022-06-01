<?php
// Используем строгую типизацию
declare(strict_types=1);

$loader = require __DIR__ . '/vendor/autoload.php';

use Abramenko\RestApi\Test\Test;
use Abramenko\RestApi\Application;

Test::Go();

//$app = new Application();

echo "11<br />";
