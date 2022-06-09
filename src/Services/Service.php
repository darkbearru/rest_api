<?php

namespace Abramenko\RestApi\Services;


use Abramenko\RestApi\Errors\Errors;

abstract class Service
{

    protected function resultOk(?array $data): array
    {
        return [
            "result" => "ok",
            "errors" => false,
            "data" => $data
        ];
    }

    protected function resultError(string|array $errors = [], int $errorCode = 200): array
    {
        if ($errorCode > 200) Errors::errorHeader($errorCode);
        return [
            "result" => "error",
            "errors" => $errors
        ];
    }
}
