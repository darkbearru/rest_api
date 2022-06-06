<?php

namespace Abramenko\RestApi\Services;

use Abramenko\RestApi\Helpers\FormHelper;
use Abramenko\RestApi\Models\{TokenModel, UserModel};

class UserService extends Service
{
    /**
     * @return bool
     */
    public static function Authorized(): bool
    {

        return true;
    }

    /**
     * @param array $params
     * @return object|array
     */
    public function Registration(array $params): object|array
    {
        $params = $this->checkRegistrationForm($params);
        if (!empty($params['error'])) return $this->resultError($params['error']);

        ["email" => $email, "password" => $password] = $params["variables"];

        // Сохранить в базу данных
        if (!($user = UserModel::New($email, $password))) {
            return $this->resultError(["Пользовательс с «{$email}» уже существует"]);
        }

        $code = UserModel::SaveConfirmationCode($user['id']);
        MailService::Send($user['email'], "<p>Необходимо <a href=\"http://localhost:8050/api/users/confirmation/$code\">подтвердить email</a></p>");

        $tokens = TokenService::Generate((array)$user);
        TokenModel::Save($user['id'], $tokens['refresh']);

        setcookie(
            "refreshToken",
            $tokens["refresh"],
            [
                "httponly" => true,
                "expires" => time() + TokenService::REFRESH_TOKEN_LIFETIME * 60 * 60 * 24
            ]
        );
        return [
            "result" => true,
            "user" => $user,
            "accessToken" => $tokens["access"]
        ];
    }

    /**
     * checkRegistrationForm
     * Метод проверки данных регистрационной формы
     *
     * @param array $params
     * @return array
     */
    protected function checkRegistrationForm(array $params): array
    {
        $params = FormHelper::checkFormData($params, 'email', 'password');
        $isEmail = FormHelper::isEmail($params['email']);
        $isPassword = FormHelper::isPassword($params['password']);

        if (!$isEmail || !$isPassword) {
            $errors = [];
            if (!$isEmail) $errors[] = 'Неверно указан формат EMail';
            if (!$isPassword) $errors[] = 'Неверно указан формат пароля (от 7 до 20 символов с использованием цифр, различного регистра)';
            return ["errors" => $errors];
        }
        return ["variables" => $params];
    }

    public function Confirmation(array $params): array|object
    {
        if (empty($params)) return $this->resultError(["Данные не преданы или не распознаны"]);

        if (empty($params['link'])) return $this->resultError(["Код подтверждения не указан"]);

        $link = $params['link'];
        $result = UserModel::checkConfirmationCode($link);
        if (!$result) return $this->resultError(["Неверный код подтверждения"]);
        return [
            "result" => true
        ];
    }

    /**
     * @param array $params
     * @return bool
     */
    public function Login(array $params): bool
    {
        return true;
    }

    /**
     * @param array|null $params
     * @return bool
     */
    public function Logout(?array $params): bool
    {
        return true;
    }
}
