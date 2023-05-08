<?php

namespace Simflex\Admin\Modules\Account;

use Simflex\Admin\Plugins\Alert\Alert;;
use Simflex\Core\DB;
use Simflex\Core\ModuleBase;
use Simflex\Core\User;

class Account extends ModuleBase
{

    public function content()
    {

        if (count($data = DB::escape($_POST))) {
            $set = array("email = '{$data['email']}', name = '{$data['name']}'");
            if ($data['password']) {
                $password = md5($data['password']);
                $set[] = "password = '$password'";
            }
            $q = "UPDATE user SET " . implode(', ', $set) . " WHERE user_id = " . User::$id;
            $success = DB::query($q);
            if ($success) {
                Alert::success('Данные успешно обновлены', './');
            } else {
                Alert::error('Данные не обновлены', './');
            }
        }

        $data = User::info();
        include 'tpl/index.tpl';
    }

}
