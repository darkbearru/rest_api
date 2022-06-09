<?php

namespace Models;

use Abramenko\RestApi\Models\TokenModel;
use Abramenko\RestApi\Models\UserModel;
use Abramenko\RestApi\Services\TokenService;
use PHPUnit\Framework\TestCase;

class TokenModelTest extends TestCase
{

    public static string $refreshToken = '';
    public static int $userID = 0;

    public function testCanSaveNewToken()
    {
        $user = UserModel::New('superuser@super.ru', 'superpassword');
        $token = self::createToken($user);
        self::$userID = $user['id'];
        $result = TokenModel::Save(self::$userID, $token);
        $this->assertEquals(true, $result, 'Проверка создания нового токена');
    }

    public static function createToken(array $payload = ['test' => 1]): string
    {
        if (empty (self::$refreshToken) && $payload) {
            $tokens = TokenService::Generate($payload);
            self::$refreshToken = $tokens['refresh'];
        }
        return self::$refreshToken;
    }

    public function testDoesTokenValidationWork()
    {
        $token = self::createToken();
        $result = TokenModel::validateToken($token);
        $email = 'not@work.ru';
        if (!empty ($result)) {
            list ('email' => $email) = $result;
        }

        $this->assertEquals('superuser@super.ru', $email, 'Проверка валидации Токена');
    }

    public function testIsRefreshTokenExistsWork()
    {
        $this->assertEquals(
            true,
            TokenModel::refreshTokenExists(self::$userID),
            'Проверка существования RefreshToken у пользователя'
        );
    }

    public function testDoesTokenDeleteWork()
    {
        $token = self::createToken();
        $this->assertEquals(true, TokenModel::deleteToken($token));
        UserModel::DeleteUser(self::$userID);
        self::$refreshToken = '';
        self::$userID = 0;
    }
}
