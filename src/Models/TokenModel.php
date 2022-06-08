<?php

namespace Abramenko\RestApi\Models;

use Abramenko\RestApi\Libraries\DataBase;
use PDO;

class TokenModel
{
    public static function Save(int $userID, string $refreshToken): bool
    {
        $db = DataBase::getInstance();

        $statement = $db->prepare(
            "REPLACE INTO tokens (`user_id`, `refresh_token`) VALUES (:user_id, :token)"
        );
        $statement->execute([
            ':user_id' => $userID,
            ':token' => $refreshToken
        ]);

        return empty((int)$statement->errorCode());
    }

    public static function validateToken($refreshToken): bool|array|object
    {
        $db = DataBase::getInstance();

        $statement = $db->prepare(
            "SELECT users.* FROM tokens INNER JOIN users on tokens.user_id = users.id WHERE `refresh_token`=:token"
        );
        $statement->execute([
            ':token' => $refreshToken
        ]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function deleteToken($refreshToken): bool|array|object
    {
        $db = DataBase::getInstance();

        $statement = $db->prepare(
            "DELETE FROM tokens WHERE `refresh_token`=:token"
        );
        $statement->execute([
            ':token' => $refreshToken
        ]);
        return empty((int)$statement->errorCode());
    }

    public static function deleteTokenByUserId(int $id): bool
    {
        $db = DataBase::getInstance();

        $statement = $db->prepare(
            "DELETE FROM tokens WHERE `user_id`=:id"
        );
        $statement->execute([
            ':id' => $id
        ]);
        return true;
    }

    public static function refreshTokenExists(int $id): bool
    {
        $db = DataBase::getInstance();

        $statement = $db->prepare(
            "SELECT id FROM tokens WHERE `user_id`=:id"
        );
        $statement->execute([
            ':id' => $id
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return !empty($result);
    }
}
