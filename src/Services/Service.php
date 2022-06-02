<?php

namespace Abramenko\RestApi\Services;


abstract class Service
{

    protected function resultOk(?object $data): object
    {
        return (object)[
            "result" => "ok",
            "errors" => false,
            "data" => $data
        ];
    }

    protected function resultError(string|array $errors = []): object
    {
        return (object)[
            "result" => "error",
            "errors" => $errors
        ];
    }
}
