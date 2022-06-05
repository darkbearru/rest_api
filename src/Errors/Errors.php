<?php

namespace Abramenko\RestApi\Errors;

use JetBrains\PhpStorm\NoReturn;

class Errors
{
    #[NoReturn] public static function showError(int $code, string $message, string $template = ''): void
    {
        ob_clean();
        if (!empty ($_SERVER)) {
            if (!empty ($_SERVER['SERVER_PROTOCOL'])) {
                header($_SERVER['SERVER_PROTOCOL'] . " {$code} Internal Server Error");
            }
        }
        echo $message;
        //header("location: /\r\n");
        if (!empty ($template)) {
            // TODO: Тут делаем вывод шаблонизатором
        }
        exit;
    }
}