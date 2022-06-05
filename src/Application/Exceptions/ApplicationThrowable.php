<?php

namespace Abramenko\RestApi\Application\Exceptions;


use Abramenko\RestApi\Errors\Errors;
use JetBrains\PhpStorm\NoReturn;
use Throwable;

/**
 * ApplicationThrowable
 * Если случилось свосем что-то жесткое
 * пишем в лог, на экран выводим 501
 */
class ApplicationThrowable extends \Exception
{
    #[NoReturn] public function __construct(mixed $message = "", int $code = 0, ?Throwable $previous = null)
    {
        echo "ApplicationThrowable call";
        if (is_object($message)) $message = (string)$message;
        // TODO: Тут вставляем логгер
        // Отображаем 500-ю ошибку
        Errors::showError(500, $message);
        //parent::__construct($message, $code, $previous);
    }
}
