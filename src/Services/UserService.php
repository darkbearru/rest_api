<?php

namespace Abramenko\RestApi\Services;

use Abramenko\RestApi\Helpers\FormHelper;
use Abramenko\RestApi\Models\UserModel;

class UserService extends Service
{
    public function Registration(array $params): bool|object
    {
        $params = $this->checkRegistrationForm($params);
        if (!empty($params['error'])) return $this->resultError($params['error']);

        ["email" => $email, "password" => $password] = $params["variables"];

        // Сохранить в базу данных
        if (!($user = UserModel::New($email, $password))) {
            return $this->resultError(["Пользовательс с «{$email}» уже существует"]);
        }

        $tokens = TokenService::Generate((array) $user);

        echo "Token:<pre>";
        print_r($tokens);
        echo "</pre>";

        // Генерим токен
        // Сохраняем RefreshToken в базу
        // и отправляем пользователю письмо для проверки email
        return true;
    }

    public function Login(array $params): bool
    {
        return true;
    }

    public function Logout(?array $params): bool
    {
        return true;
    }

    public static function isLogined(): bool
    {
        return true;
    }

    /**
     * checkRegistrationForm
     * Метод проверки данных регистрационной формы
     *
     * @param  array $params
     * @return array
     */
    protected function checkRegistrationForm(array $params): array
    {
        $variables = $params['variables'];
        if (!empty($params['body'])) {
            $variables = $params['body'];
        }
        $variables['email'] = (!empty($variables['email']) ? $variables['email'] : '');
        $variables['password'] = (!empty($variables['password']) ? $variables['password'] : '');
        $isEmail = FormHelper::isEmail($variables['email']);
        $isPassword = FormHelper::isPassword($variables['password']);

        if (!$isEmail || !$isPassword) {
            $errors = [];
            if (!$isEmail) $errors[] = 'Неверно указан формат EMail';
            if (!$isPassword) $errors[] = 'Неверно указан формат пароля (от 7 до 20 символов с использованием цифр, различного регистра)';
            return ["errors" => $errors];
        }
        return ["variables" => $variables];
    }
}
