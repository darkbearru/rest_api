<?php

namespace Abramenko\RestApi\Application;

interface IRequest
{
    private array $_requestVariables = [];

    public function getRequestVariables(): array;
}
