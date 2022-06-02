<?php

namespace Abramenko\RestApi\Models;

use Abramenko\RestApi\Libraries\DataBase;

class UserModel
{
    /**
     * New
     *
     * @param  string $email
     * @param  string $password
     * @return bool
     */
    public static function New(string $email, string $password): bool|array
    {
        $db = DataBase::getInstance();

        if (self::IsUserExists($email)) return false;

        $statement = $db->prepare(
            "INSERT INTO users (`email`,`password`, `created_at`, `changed_at`) VALUES (:email, :password, now(), now())"
        );
        $statement->execute([
            ':email'    => $email,
            ':password' => md5($password)
        ]);
        $id = $db->lastInsertId();
        $user = [
            "id" => $id,
            "email" => $email
        ];

        return $user;
    }

    /**
     * IsUserExists
     * Проверяем на существование пользователя с таким email-ом
     *
     * @param  string $email
     * @return bool
     */
    public static function IsUserExists(string $email): bool
    {
        $db = DataBase::getInstance();

        $statement = $db->prepare(
            "SELECT id FROM Users WHERE email=:email"
        );
        $statement->execute([':email' => $email]);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($rows)) return false;
        return true;
    }
}
