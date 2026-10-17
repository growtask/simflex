<?php

namespace Simflex\Extensions\Content\Model;

use Simflex\Core\Buffer;
use Simflex\Core\ModelBase;

/**
 * @property int $content_id
 * @property int $pid
 * @property bool $active
 * @property string $date
 * @property string $title
 * @property string $alias
 * @property string $path
 * @property string $short
 * @property string $text
 * @property string $params
 * @property string $file
 * @property string $photo
 * @property string|null $template Page template key, see \Simflex\Extensions\Content\ContentTemplates
 * @property int $npp
 */
class ModelContent extends ModelBase
{
    protected static string $table = 'content';
    protected static string $primaryKeyName = 'content_id';

    public function loadFrom(string $path): ?ModelContent
    {
        return Buffer::getOrSet('content.' . $path, function () use ($path) {
            if ($nc = self::findOne(['path' => $path, 'active' => 1])) {
                $nc['params'] = unserialize($nc['params']);
            }

            return $nc ?? null;
        });
    }

    public function loadParent(): ?ModelContent
    {
        $pid = $this['pid'];
        return Buffer::getOrSet('content.parent.' . $pid, function () use ($pid) {
            if ($nc = self::findOne(['content_id' => $this['pid'], 'active' => 1])) {
                $nc['params'] = unserialize($nc['params']);
            }

            return $nc ?? null;
        });
    }

    public function getTable(string $param, string $path = ''): array
    {
        $params = $path ? $this->loadFrom($path)['params'] : $this['params'];
        return json_decode($params[$param] ?? '{"v":[]}', true)['v'];
    }
}
