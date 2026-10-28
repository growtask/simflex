<?php

namespace Simflex\Admin\Handlers;

use Simflex\Admin\Handler;
use Simflex\Core\Privilege as PrivilegeRegistry;
use Simflex\Core\SiteSettings;

/**
 * Привилегии объявлены в коде (\Simflex\Core\Privilege и подкласс приложения): раздел выводит их список
 * и переключает их активность, которая хранится в необязательной таблице user_privilege. Не табличный раздел:
 * только список и переключатель
 */
class Privilege extends Handler
{
    public function content(): string
    {
        return match ($_GET['action'] ?? '') {
            '' => $this->show(),
            'switch' => $this->switchActive(),
            default => (string)SiteSettings::get('error404'),
        };
    }

    public function show(): string
    {
        $rows = [];
        foreach (PrivilegeRegistry::getAll() as $name => $params) {
            $rows[] = [
                'name' => $name,
                'title' => $params->title,
                'active' => PrivilegeRegistry::isActive($name),
                'switchable' => $params->switchable,
            ];
        }

        return $this->render(__DIR__ . '/tpl/privileges.tpl', ['rows' => $rows]);
    }

    /**
     * Задаёт активность привилегии: POST name, active (1/0). Возвращает JSON с активностью после изменения
     */
    protected function switchActive(): string
    {
        $name = (string)($_POST['name'] ?? '');
        $ok = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && PrivilegeRegistry::setActive($name, !empty($_POST['active']));
        if (!$ok) {
            http_response_code(400);
        }

        return (string)json_encode(['ok' => $ok, 'active' => PrivilegeRegistry::isActive($name)]);
    }
}
