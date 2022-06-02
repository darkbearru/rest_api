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
     * SaveConfirmationLink
     * Сохраняем ссылку для подтверждения пользователя
     *
     * @param  int $userID
     * @return string
     */
    public static function SaveConfirmationCode(int $userID): string
    {
        $hash = md5($userID . '-' . strtotime('now'));

        $db = DataBase::getInstance();
        // Сохраняем данные
        $statement = $db->prepare(
            "REPLACE INTO users_links (`user_id`, `link`, `created_at`) VALUES (:user_id, :link, now())"
        );
        $statement->execute([
            ':user_id'  => $userID,
            ':link'    => $hash
        ]);
        // Удаляем старые записи по данным для подтверждения
        $statement = $db->prepare(
            "DELETE FROM users_links WHERE created_at < DATE_ADD(now(), INTERVAL -1 WEEK)"
        );
        $statement->execute();

        return $hash;
    }

    public static function checkConfirmationCode(string $link): bool
    {
        $db = DataBase::getInstance();
        $statement = $db->prepare(
            "SELECT id FROM users left join users_link  WHERE link=:link"
        );
        $statement->execute([':link' => $link]);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($rows)) return false;

        echo '<pre>';
        print_r($rows);
        echo '</pre>';
        $statement = $db->prepare(
            "UPDATE users SET is_activated=1 WHERE id=:id"
        );
        $statement->execute([':id' => $rows['id']]);

        // Удаляем старые записи по данным для подтверждения
        $statement = $db->prepare(
            "DELETE FROM users_links WHERE user_id=:user_id"
        );
        $statement->execute([
            ':user_id'  => $rows['id']
        ]);
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
