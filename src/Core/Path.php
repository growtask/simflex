<?php

namespace Simflex\Core;

class Path
{
    /**
     * Публикует статику каталога или файла в SF_PUBLIC_PATH (см. Assets::publish) и возвращает её url
     * @param string $dir for example /var/www/html/Layout/Cards/Product1
     * @return string for example /cache/assets/1a2b3c4d5e6f7a8b
     */
    public static function dirToHref(string $dir): string
    {
        return Assets::publish($dir);
    }
}