<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use \Abramenko\Experiments\Email;

$email = \Abramenko\Experiments\Email::fromString("a.abramenko@chita.ru");

echo "<pre>";
print_r((string) $email);
echo "</pre>";
