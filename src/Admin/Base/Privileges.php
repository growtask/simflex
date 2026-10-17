<?php

namespace Simflex\Admin\Base;

use Simflex\Admin\Base;
use Simflex\Core\Privilege;

/**
 * Admin section of the privileges. They are declared in code (\Simflex\Core\Privilege and the app's subclass):
 * the section lists them and switches their activity, kept in the optional user_privilege table.
 * Only two actions: the list and the switch, the generic table actions (form, save, delete...) aren't available
 */
class Privileges extends Base
{
    public function content(): string
    {
        return match ($_GET['action'] ?? '') {
            '' => $this->show(),
            'switch' => $this->switchActive(),
            default => (string)\Simflex\Admin\Core::siteParam('error404'),
        };
    }

    public function show(): string
    {
        $rows = [];
        foreach (Privilege::all() as $name => $params) {
            $rows[] = [
                'name' => $name,
                'title' => $params->title,
                'active' => Privilege::isActive($name),
                'switchable' => $params->switchable,
            ];
        }

        return $this->render(__DIR__ . '/tpl/privileges.tpl', ['rows' => $rows]);
    }

    /**
     * Sets the activity of the privilege: POST name, active (1/0). Returns JSON with the activity after the change
     */
    protected function switchActive(): string
    {
        $name = (string)($_POST['name'] ?? '');
        $ok = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && Privilege::setActive($name, !empty($_POST['active']));
        if (!$ok) {
            http_response_code(400);
        }

        return (string)json_encode(['ok' => $ok, 'active' => Privilege::isActive($name)]);
    }
}
