<?php

namespace Simflex\Admin\Modules\Account;

use Simflex\Admin\Plugins\Alert\Alert;
use Simflex\Core\Container;
use Simflex\Core\DB;
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
            $set = ['email = ?', 'name = ?'];
            $params = [(string)$_POST['email'], (string)$_POST['name']];
            if (!empty($_POST['password'])) {
                $set[] = 'password = ?';
                $params[] = md5((string)$_POST['password']);
            }
            $params[] = $user->getId();
            $q = "UPDATE user SET " . implode(', ', $set) . " WHERE user_id = ?";
            $success = DB::query($q, $params);
            if ($success) {
                Alert::success('Данные успешно обновлены', './');
            } else {
                Alert::error('Данные не обновлены', './');
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
