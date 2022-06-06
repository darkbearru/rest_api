<?php

namespace Abramenko\RestApi\Libraries;

/**
 * Database
 * Singleton PDO
 */
class DataBase
{
    private static ?\PDO $_instance = null;

    public static function getInstance(): \PDO
    {
        if (is_null(self::$_instance)) {
            $database = 'mysql:dbname=db;host=127.0.0.1';
            $user = 'dbuser';
            $password = 'dbpass';
            self::$_instance = new \PDO($database, $user, $password);
        }
        return self::$_instance;
    }
}
