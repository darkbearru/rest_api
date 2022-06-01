<?php

namespace Abramenko\RestApi\Application;

interface IRequest
{
    public function getRequestVariables(): array;
}
