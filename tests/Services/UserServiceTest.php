<?php

namespace Services;

use Abramenko\RestApi\Models\TokenModel;
use Abramenko\RestApi\Models\UserModel;
use Abramenko\RestApi\Services\TokenService;
use Abramenko\RestApi\Services\UserService;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    public static string $user_email = 'test@userservice.ru';
    public static string $user_psw = 'Test2Psw';
    public static int $user_id = 0;
    public static string $accessToken = '';

    public function testRegistration()
    {
        $userService = new UserService();
        $result = $userService->Registration([
            'email' => self::$user_email,
            'password' => self::$user_psw
        ]);
        $this->assertNotEmpty($result, 'Registration. Содержимое ответа не пустое');
        $this->assertCount(3, $result, 'Registration. Кол-во полей в ответе');
        $this->assertArrayHasKey('result', $result, 'Registration. Наличие поля «result»');
        $this->assertArrayHasKey('errors', $result, 'Registration. Наличие поля «errors»');
        $this->assertArrayHasKey('data', $result, 'Registration. Наличие поля «data»');
        $this->assertEquals('ok', $result['result'], 'Registration. Статус ответа');
        $this->assertGreaterThanOrEqual(1, count($result['data']), 'Registration. Наличие данных в результате');
        // Теперь проверка конкретного поля user и его значений
        $this->assertArrayHasKey('user', $result['data'], 'Registration. Наличие поля «user» в поле «data»');
        $this->assertEquals(self::$user_email, $result['data']['user']['email'], 'Registration. Проверка совпадения зарегистрированного email');
        $this->assertArrayHasKey('accessToken', $result['data'], 'Registration. Проверка наличия поля с токеном');
        $this->assertGreaterThanOrEqual(70, strlen($result['data']['accessToken']), 'Registration. Проверка длины токена (не меньше 70)');

        self::$accessToken = $result['data']['accessToken'];
        self::$user_id = $result['data']['user']['id'];
    }

    public function testLogin()
    {
        $this->assertNotEmpty(self::$accessToken, 'Login. Данный тест должен быть запущен после теста регистрации или логина. AccessToken пуст');

        $userService = new UserService();
        $result = $userService->Login([
            'email' => self::$user_email,
            'password' => self::$user_psw
        ]);

        $this->assertNotEmpty($result, 'Login. Содержимое ответа не пустое');
        $this->assertCount(3, $result, 'Login. Кол-во полей в ответе');
        $this->assertArrayHasKey('result', $result, 'Login. Наличие поля «result»');
        $this->assertArrayHasKey('errors', $result, 'Login. Наличие поля «errors»');
        $this->assertArrayHasKey('data', $result, 'Login. Наличие поля «data»');
        $this->assertEquals('ok', $result['result'], 'Login. Статус ответа');
        $this->assertGreaterThanOrEqual(1, count($result['data']), 'Login. Наличие данных в результате');
        // Теперь проверка конкретного поля user и его значений
        $this->assertArrayHasKey('user', $result['data'], 'Login. Наличие поля «user» в поле «data»');
        $this->assertEquals(self::$user_email, $result['data']['user']['email'], 'Login. Проверка совпадения зарегистрированного email');
        $this->assertArrayHasKey('accessToken', $result['data'], 'Login. Проверка наличия поля с токеном');
        $this->assertGreaterThanOrEqual(70, strlen($result['data']['accessToken']), 'Login. Проверка длины токена (не меньше 70)');
        // Проверка полученного ID с созданным
        $this->assertEquals(self::$user_id, $result['data']['user']['id'], 'Login. Проверка полученного ID с созданным ранее');
    }


    public function testConfirmation()
    {
        $this->assertNotEmpty(self::$user_id, 'Confirmation. Данный тест должен быть запущен после теста регистрации или логина. UserID пуст');
        $userService = new UserService();
        $link = UserModel::getUserConfirmationLink(self::$user_id);
        $this->assertNotEmpty($link, 'Confirmation. Код подтверждения пуст');
        $result = $userService->Confirmation(['link' => $link]);
        $this->assertNotEmpty($result, 'Confirmation. Содержимое ответа не пустое');
        $this->assertCount(3, $result, 'Confirmation. Кол-во полей в ответе');
        $this->assertArrayHasKey('result', $result, 'Confirmation. Наличие поля «result»');
        $this->assertArrayHasKey('errors', $result, 'Confirmation. Наличие поля «errors»');
        $this->assertArrayHasKey('data', $result, 'Confirmation. Наличие поля «data»');
        $this->assertEquals('ok', $result['result'], 'Confirmation. Статус ответа');
        $this->assertGreaterThanOrEqual(1, count($result['data']), 'Confirmation. Наличие данных в результате');
        // Теперь проверка конкретного поля user и его значений
        $this->assertArrayHasKey('id', $result['data'], 'Confirmation. Наличие поля «id» в поле «data»');
        $this->assertArrayHasKey('email', $result['data'], 'Confirmation. Наличие поля «email» в поле «data»');
        $this->assertEquals(self::$user_email, $result['data']['email'], 'Confirmation. Проверка совпадения зарегистрированного email');
        // Проверка полученного ID с созданным
        $this->assertEquals(self::$user_id, $result['data']['id'], 'Confirmation. Проверка полученного ID с созданным ранее');

    }

    public function testLogout()
    {
        $this->assertNotEmpty(self::$user_id, 'Logout. Данный тест должен быть запущен после теста регистрации или логина. UserID пуст');
        $userService = new UserService();
        $refreshToken = TokenModel::deleteTokenByUserId(self::$user_id);
        $this->assertNotEmpty($refreshToken, 'Logout. RefreshToken пуст. Ошибка в User ID или в предыдущих шагах тестовой регистрации');
        // Задаём $_COOKIE
        $_COOKIE['refreshToken'] = $refreshToken;
        $result = $userService->Logout(null);
        $this->assertNotEmpty($result, 'Logout. Содержимое ответа не пустое');
        $this->assertCount(3, $result, 'Logout. Кол-во полей в ответе');
        $this->assertCount(0, $result['data'], 'Logout. Поле дата пустое');
        UserModel::deleteUser(self::$user_id);
        self::$accessToken = '';
        self::$user_id = 0;
    }

}
