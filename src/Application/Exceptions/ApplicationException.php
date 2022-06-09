<?php

namespace Abramenko\RestApi\Application\Exceptions;

use JetBrains\PhpStorm\NoReturn;
use Throwable;

class ApplicationException extends \Exception
{
    #[NoReturn] public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        echo "Message: {$message}, Code: {$code}";
        parent::__construct($message, $code, $previous);
    }
}

