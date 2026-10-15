<?php

namespace Simflex\Admin\Modules\Account;

use Simflex\Admin\Plugins\Alert\Alert;
use Simflex\Core\Container;
use Simflex\Core\Models\User as UserModel;
use Simflex\Core\ModuleBase;

class Account extends ModuleBase
{
    public function content(): void
    {
        $user = Container::getUser();
        if (!$user) {
            return;
        }

        if (isset($_POST['email'], $_POST['name'])) {
            $set = ['email' => (string)$_POST['email'], 'name' => (string)$_POST['name']];
            if (!empty($_POST['password'])) {
                $set['password'] = md5((string)$_POST['password']);
            }
            $success = UserModel::bulkUpdate($set, ['user_id' => $user->getId()]);
            if ($success) {
                Alert::success('Данные успешно обновлены', \Simflex\Admin\Core::path());
            } else {
                Alert::error('Данные не обновлены', \Simflex\Admin\Core::path());
            }
        }

        $data = [
            'role_name' => $user->role?->name ?? '',
            'login' => $user->login,
            'name' => $user->name,
            'email' => $user->email,
        ];
        include 'tpl/index.tpl';
    }
}
