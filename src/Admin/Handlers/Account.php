<?php

namespace Simflex\Core\Admin\Handlers;

use Simflex\Core\Admin\Page;
use Simflex\Core\Extensions\Alert\Alert;
use Simflex\Core\Admin\Handler;
use Simflex\Core\System\Container;
use Simflex\Core\Models\User as UserModel;

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

        if ($this->request->post('email') !== null && $this->request->post('name') !== null) {
            $set = ['email' => (string)$this->request->post('email'), 'name' => (string)$this->request->post('name')];
            if (!empty($this->request->post('password'))) {
                $set['password'] = md5((string)$this->request->post('password'));
            }
            $success = UserModel::bulkUpdate($set, ['user_id' => $user->getId()]);
            if ($success) {
                Alert::success('Данные успешно обновлены', Page::get()->getPath());
            } else {
                Alert::error('Данные не обновлены', Page::get()->getPath());
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
