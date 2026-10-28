<?php

namespace Simflex\Admin\Handlers;

use Simflex\Admin\Core;
use Simflex\Admin\Plugins\Alert\Alert;
use Simflex\Admin\Handler;
use Simflex\Core\Container;
use Simflex\Models\User as UserModel;

/**
 * Учётная запись текущего пользователя: имя, email и пароль. Не табличный раздел
 */
class Account extends Handler
{
    public function content(): string
    {
        $user = Container::getUser();
        if (!$user) {
            return '';
        }

        if (isset($_POST['email'], $_POST['name'])) {
            $set = ['email' => (string)$_POST['email'], 'name' => (string)$_POST['name']];
            if (!empty($_POST['password'])) {
                $set['password'] = md5((string)$_POST['password']);
            }
            $success = UserModel::bulkUpdate($set, ['user_id' => $user->getId()]);
            if ($success) {
                Alert::success('Данные успешно обновлены', Core::path());
            } else {
                Alert::error('Данные не обновлены', Core::path());
            }
        }

        $data = [
            'role_name' => $user->role?->name ?? '',
            'login' => $user->login,
            'name' => $user->name,
            'email' => $user->email,
        ];
        return $this->render(__DIR__ . '/tpl/account.tpl', ['data' => $data]);
    }
}
