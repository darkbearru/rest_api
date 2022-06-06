<?php

namespace Abramenko\RestApi\Helpers;

class FormHelper
{
    public static function isEmail(
        string $email,
        string $mask = '/^[a-z\d]([-._a-z\d]+)@([a-z\d][-_a-z\d]+\.)+(com|ru|[a-z]{2,5})$/uis'
    ): bool
    {
        return preg_match($mask, $email);
    }

    public static function isPassword(
        string $password,
        string $mask = '/^[-_.a-zA-Z\d\@\#\$\%]{7,20}$/us'
    ): bool
    {
        return preg_match($mask, $password);
    }

    public static function checkFormData(array $params, ...$fields): array
    {
        foreach ($fields as $field) {
            $params[$field] = (!empty($params[$field]) ? trim($params[$field]) : '');
        }
        return $params;
    }
}
