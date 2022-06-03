<?php

namespace Abramenko\RestApi\Models;

use PDO;
use Abramenko\RestApi\Libraries\DataBase;

class UserModel
{
    /**
     * New
     * Создание нового пользователя
     *
     * @param string $email
     * @param string $password
     * @return bool|array
     */
    public static function New(string $email, string $password): bool|array
    {
        $email = trim($email);
        $password = trim($password);
        if (empty($email) || empty($password)) return false;

        $db = DataBase::getInstance();

        if (self::IsUserExists($email)) return false;

        $statement = $db->prepare(
            "INSERT INTO users (`email`,`password`, `created_at`, `changed_at`) VALUES (:email, :password, now(), now())"
        );
        $statement->execute([
            ':email' => $email,
            ':password' => md5($password)
        ]);
        $id = $db->lastInsertId();
        return [
            "id" => $id,
            "email" => $email
        ];
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
            "SELECT users.id FROM users left join users_links on users.id=users_links.user_id  WHERE link=:link"
        );
        $statement->execute([':link' => $link]);
        $rows = $statement->fetch(PDO::FETCH_ASSOC);

        if (empty($rows)) return false;

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
        return true;
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
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) return false;
        return true;
    }

    /**
     * DeleteUser
     * Удаление пользователя
     *
     * @param string|int $user User ID или User Email
     * @return bool
     */
    public static function DeleteUser(string|int $user): bool
    {
        $db = DataBase::getInstance();

        $statement = $db->prepare(
            "DELETE FROM Users WHERE " . (is_int($user) ? "id" : "email") . "=:user"
        );
        return $statement->execute([':user' => $user]);

    }
}
