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
     * IsUserExists
     * Проверяем на существование пользователя с таким email-ом
     *
     * @param string $email
     * @param array $tokenData
     * @return bool
     */
    public static function IsUserExists(string $email, array $tokenData = []): bool
    {
        $db = DataBase::getInstance();

        $where = 'email=:email';
        $params = [
            ':email' => $email
        ];
        if (empty ($email) && !empty($tokenData)) {
            $where = '';
            $params = [];
            if (!empty($tokenData['user'])) {
                foreach ($tokenData['user'] as $key => $value) {
                    $where .= ($where != '' ? " and " : "") . "$key=:$key";
                    $params [':' . $key] = $value;
                }
            }
        }

        $statement = $db->prepare(
            "SELECT id FROM Users WHERE $where"
        );
        $statement->execute($params);
        $rows = $statement->fetch(PDO::FETCH_ASSOC);

        if (empty($rows)) return false;
        return true;
    }

    /**
     * SaveConfirmationLink
     * Сохраняем ссылку для подтверждения пользователя
     *
     * @param int $userID
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
            ':user_id' => $userID,
            ':link' => $hash
        ]);

        // Удаляем старые записи по данным для подтверждения
        self::deleteOldLinks();

        return $hash;
    }

    protected static function deleteOldLinks(): void
    {
        $db = DataBase::getInstance();

        $statement = $db->prepare(
            "DELETE FROM users_links WHERE created_at < DATE_ADD(now(), INTERVAL -1 WEEK)"
        );
        $statement->execute();
    }

    public static function getUserConfirmationLink(int $id): string
    {
        $db = DataBase::getInstance();
        $statement = $db->prepare(
            "SELECT link FROM users_links WHERE user_id=:id"
        );
        $statement->execute([
            ':id' => $id
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return (!empty ($result) ? $result['link'] : '');
    }

    /**
     * checkConfirmationCode
     * Проверка кода подтверждения
     *
     * @param string $link
     * @return array|bool
     */
    public static function checkConfirmationCode(string $link): array|bool
    {
        $db = DataBase::getInstance();

        $statement = $db->prepare(
            "SELECT users.id, users.email FROM users left join users_links on users.id=users_links.user_id  WHERE link=:link"
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
            ':user_id' => $rows['id']
        ]);
        return (array)$rows;
    }

    /**
     * DeleteUser
     * Удаление пользователя
     *
     * @param string|int $user User ID или User Email
     * @return bool
     */
    public static function deleteUser(string|int $user): bool
    {
        $db = DataBase::getInstance();

        $statement = $db->prepare(
            "DELETE FROM Users WHERE " . (is_int($user) ? "id" : "email") . "=:user"
        );
        return $statement->execute([':user' => $user]);

    }

    /**
     * checkLoginInfo
     * Проверка информации для авторизации
     *
     * @param string $email
     * @param string $password
     * @return bool|int
     */
    public static function checkLoginInfo(string $email, string $password): bool|int
    {
        $db = DataBase::getInstance();
        $password = md5($password);
        $statement = $db->prepare(
            "SELECT id FROM Users WHERE email=:email and password=:password"
        );
        $statement->execute([
            ':email' => $email,
            ':password' => $password
        ]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return empty($row['id']) ? false : $row['id'];
    }
}
