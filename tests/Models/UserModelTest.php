<?php

namespace Models;

use Abramenko\RestApi\Models\UserModel;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{

    public function testCanCheckIsUserExists()
    {
        $this->assertEquals(
            true,
            UserModel::IsUserExists("a.abramenko@chita.ru"),
            'Проверка существования пользователя успешна'
        );
        $this->assertEquals(
            false,
            UserModel::IsUserExists("a.abramenko@yandex.ru"),
            'Проверка отсутствия пользователя успешна'
        );
    }

    public function testCanCreateNewUserWithEmptyParams()
    {
        $this->assertEquals(
            false,
            UserModel::New("", ""),
            'Проверка создания пользователя с пустыми параметрами'
        );
    }

    public function testCanCreateNewUser()
    {
        $user = UserModel::New("a.abramenko@gmail.com", "superSecret");
        $this->assertIsArray($user, 'Проверка возращается ли массив');
        $this->assertMatchesRegularExpression('/^[1-9]\d+$/', $user['id'], 'Проверка созданного ID пользователя');
        $this->assertEquals("a.abramenko@gmail.com", $user['email'], 'Проверка на совпадение email');

    }

    public function testDeleteUser()
    {
        $this->assertEquals(true, UserModel::DeleteUser("a.abramenko@gmail.com"), 'Проверка удаления по email');
        $user = UserModel::New("a.abramenko@gmail.com", "superSecret");
        $this->assertEquals(true, UserModel::DeleteUser((int)$user['id']), 'Проверка удаления по ID');
    }

    public function testSaveConfirmationCode()
    {
        $code = UserModel::SaveConfirmationCode(1);
        $this->assertIsString($code, 'Проверка создания кода подтверждения');
    }

    public function testCheckConfirmationCode()
    {
        $user = UserModel::New("a.abramenko75@gmail.com", "superSecret");
        $code = UserModel::SaveConfirmationCode($user['id']);
        $this->assertEquals(
            ['id' => $user['id'], 'email' => "a.abramenko75@gmail.com"],
            UserModel::checkConfirmationCode($code),
            'Проверка существования кода подтверждения');
        UserModel::DeleteUser((int)$user['id']);
    }
}
