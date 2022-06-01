<?php

namespace Abramenko\RestApi;

interface IRequest
{
    private array $_requestVariables = [];

    public function getRequestVariables(): array;
}
