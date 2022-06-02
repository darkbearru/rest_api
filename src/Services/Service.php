<?php

namespace Abramenko\RestApi\Services;

use PDO;
use Abramenko\RestApi\Libs\DB;

abstract class Service
{
    private PDO $_db;

    public function __construct()
    {
        $this->_db = DB::getInstance();
    }
}
