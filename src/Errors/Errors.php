<?php

namespace Abramenko\RestApi\Errors;

use JetBrains\PhpStorm\NoReturn;

class Errors
{
    public static array $errorMessages = [
        3 => 'Multiple Choice',
        4 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        405 => 'Not Found',
        406 => 'Method Not Allowed',
        5 => 'Internal Server Error'
    ];

    #[NoReturn] public static function showError(int $code, string $message, string $template = ''): void
    {
        self::errorHeader($code);
        echo $message;
        //header("location: /\r\n");
        if (!empty ($template)) {
            // TODO: Тут делаем вывод шаблонизатором
        }
        exit;
    }

    public static function errorHeader(int $code): void
    {
        ob_clean();
        if (!empty ($_SERVER)) {
            if (!empty ($_SERVER['SERVER_PROTOCOL'])) {
                header($_SERVER['SERVER_PROTOCOL'] . " {$code} " . self::getErrorMessage($code));
            }
        }
    }

    public static function getErrorMessage(int $code): string
    {
        if (!empty(self::$errorMessages[$code])) return self::$errorMessages[$code];
        $code = (int)($code / 100);
        if (!empty(self::$errorMessages[$code])) return self::$errorMessages[$code];
        return "Unrecognized error";
    }
}