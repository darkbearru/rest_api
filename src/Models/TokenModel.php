<?php

namespace Abramenko\RestApi\Models;

use Abramenko\RestApi\Libraries\DataBase;

class TokenModel
{
    public static function Save(int $userID, string $refreshToken): bool
    {
        $db = DataBase::getInstance();

        $statement = $db->prepare(
            "REPLACE INTO tokens (`user_id`, `refresh_token`) VALUES (:user_id, :token)"
        );
        $statement->execute([
            ':user_id'  => $userID,
            ':token'    => $refreshToken
        ]);

        return true;
    }
}
