<?php

namespace Simflex\Core;

class Page
{
    protected static string $menu = '';
    protected static string $content = '';
    protected static array $positions = [];
    protected static array $modules = [];
    protected static array $css = [];
    protected static array $cssCheck = [];
    protected static array $js = [];
    protected static array $jsCheck = [];
    public static string $seoTitle = '';
    protected static string $seoDescription = '';
    protected static string $seoKeywords = '';
    protected static string $seoMetatags = '';
    protected static bool $seoIs = false;
    protected static string $metaRaw = '';
    public static bool $showAside = false;

    public static array $override = [
        'description' => '',
        'keywords' => '',
        'title' => '',
        'uses_meta' => false,
    ];

    public static function init(): void
    {
        $com = Core::getComponent();

        $q = "
            SELECT t1.item_id, t1.module_id, t1.menu_id, t1.posname, t1.name, t2.class, t1.params, t2.postexec
            FROM module_item t1
            JOIN module t2 ON t2.module_id=t1.module_id and t2.type = 'site'
            WHERE t1.active=1
            ORDER BY t1.npp, t1.item_id
        ";
        $rows = DB::assoc($q);
        $curmenu = Core::menuCurItem();
        foreach ($rows as $row) {
            if (!$row['postexec'] && (empty($row['menu_id']) || (int)$row['menu_id'] == $curmenu['menu_id'])) {
                $module = new $row['class']($row);
                self::$positions[$row['posname']][] = $module->execute();
            }
        }

        self::$content = $com->execute();
        if (strpos(self::$content, '{') !== false) {
            foreach (self::$positions as $posname => $mcontent) {
                self::$content = str_replace('{position_' . $posname . '}', implode("\n", $mcontent), self::$content);
            }
        }

        foreach ($rows as $row) {
            if ($row['postexec'] && (empty($row['menu_id']) || (int)$row['menu_id'] == $curmenu['menu_id'])) {
                $module = new $row['class']($row);
                self::$positions[$row['posname']][] = $module->execute();
            }
        }
    }

    public static function content(): void
    {
        echo self::$content;
    }

    /**
     * @param string $delimiter (optional = '') разделитель, например: '<hr/>'
     */
    public static function position(string $posname, string $delimiter = ''): void
    {
        if ($posname && !empty(self::$positions[$posname])) {
            $i = 0;

            $counter = count(self::$positions[$posname]);
            foreach (self::$positions[$posname] as $mod) {
                echo $mod;
                if ($counter-- > 1) {
                    echo $delimiter;
                }
            }
        }
    }

    public static function module($modname): false
    {
        return false;
    }

    public static function moduleById(int $module_id): void
    {
        $q = 'SELECT t1.item_id, t1.module_id, t1.menu_id, t1.posname, t1.name, t2.class, t1.params
        FROM module_item t1
        JOIN module t2 ON t2.module_id=t1.module_id
        WHERE t1.active=1 AND t1.item_id=' . $module_id;
        if ($row = DB::result($q)) {
            $module = new $row['class']($row);
            echo $module->execute();
        }
    }

    /**
     * @param string $file Web path to asset file
     * @param int $idx Priority from highest 0 to lowest 100
     * @param string|null $type css/js - optional, to specify directly what is this asset
     * @throws \Exception
     */
    public static function addAsset(string $file, int $idx = 100, ?string $type = null): void
    {
        if ('css' === $type || strpos($file, '.css')) {
            static::css($file, $idx);
            return;
        }
        if ('js' === $type || strpos($file, '.js')) {
            static::js($file, $idx);
            return;
        }
        throw new \Exception("Unknown asset type (css or js?) for $file");
    }

    public static function css(string $file, int $idx = 100): void
    {
        if (SF_LOCATION == SF_LOCATION_ADMIN) {
            \Simflex\Admin\Page::css($file, $idx);
            return;
        }
        if (empty(self::$css[$idx][md5($file)])) {
            if (isset(self::$cssCheck[md5($file)])) {
                unset(self::$css[self::$cssCheck[md5($file)]][md5($file)]);
                unset(self::$cssCheck[md5($file)]);
            }
            self::$cssCheck[md5($file)] = $idx;
            self::$css[$idx][md5($file)] = $file;
        }
    }

    /**
     * @param string $file Путь к подключаемому скрипту
     * @param int $idx [optional = 100] Приоритет, 0 - самый высокий, 100 - самый низкий
     */
    public static function js(string $file, int $idx = 100): void
    {
        if (SF_LOCATION == SF_LOCATION_ADMIN) {
            \Simflex\Admin\Page::js($file, $idx);
            return;
        }
        if (empty(self::$js[$idx][md5($file)])) {
            if (isset(self::$jsCheck[md5($file)])) {
                unset(self::$js[self::$jsCheck[md5($file)]][md5($file)]);
                unset(self::$jsCheck[md5($file)]);
            }
            self::$jsCheck[md5($file)] = $idx;
            self::$js[$idx][md5($file)] = $file;
        }
    }

    /**
     * Добавляет JS код в блок подключения скриптов
     * @param string $js inline js code
     */
    public static function jsInline(string $js, int $idx = 100): bool
    {
        if (SF_LOCATION == SF_LOCATION_ADMIN) {
            return false;
        }
        self::$js[$idx][md5($js)] = ['type' => 'inline', 'value' => $js];
        return true;
    }

    public static function addMetaRaw(string $str): void
    {
        self::$metaRaw .= "$str\n";
    }

    public static function title(): string
    {
        return str_replace(
            '<br/>',
            '',
            htmlspecialchars(self::$seoTitle)
        ) . (self::$seoTitle ? ' | ' : '') . Core::siteParam('site_name');
    }

    public static function meta(): void
    {
        $title = self::$override['title'] ?: self::$seoTitle;
        $sname = (!self::$override['uses_meta'] ? ' | ' : '') . (!self::$override['uses_meta'] ? Core::siteParam(
            'site_name'
        ) : '');
        echo '<title>', str_replace('<br/>', '', htmlspecialchars($title)), $sname, '</title>', "\r\n";
        echo self::$seoMetatags ? self::$seoMetatags . "\r\n" : '';

        self::metaCSS();

        echo self::$metaRaw;

        echo '<meta charset="utf-8">', "\r\n";

        if (Container::getConfig()->devMode) {
            Container::get('debugbar')->renderHead();
        }
    }

    public static function metaCSS(): void
    {
        $std = Core::siteParam('static_domain');
        $sub = Core::siteParam('static_domain_sub') ? 'css.' : '';
        $v = (int)Core::siteParam('static_version');
        $hasCache = (bool)Core::siteParam('static_cache');
        if ($hasCache && $cacheStr = Core::siteParam('static_cache_css')) {
            $cache = array_filter(array_map('trim', explode("\n", $cacheStr)));
            if ($hasCache = (bool)count($cache)) {
                $cache = array_flip($cache);
                self::$css[1][] = "/cache/css/$v.min.css";
            }
        }
        ksort(self::$css);
        foreach (self::$css as $css_arr) {
            foreach ($css_arr as $css) {
                if ($hasCache && isset($cache[trim($css)])) {
                    continue;
                }
                if ($std && strpos($css, 'http') === false) {
                    $css = "http://$sub$std$css";
                }
                echo '<link type="text/css" rel="preload" as="style" href="', $css, $v ? '?v=' . $v : '', '" />', "\r\n";
                echo '<link type="text/css" rel="stylesheet" href="', $css, $v ? '?v=' . $v : '', '" />', "\r\n";
            }
        }
    }

    public static function metaJS(): void
    {
        $std = Core::siteParam('static_domain');
        $sub = Core::siteParam('static_domain_sub') ? 'js.' : '';
        $v = env('JS_VER');
        $hasCache = (bool)Core::siteParam('static_cache');
        if ($hasCache && $cacheStr = Core::siteParam('static_cache_js')) {
            $cache = array_filter(array_map('trim', explode("\n", $cacheStr)));
            if ($hasCache = (bool)count($cache)) {
                $cache = array_flip($cache);
                self::$js[1][] = "/cache/js/$v.min.js";
            }
        }
        ksort(self::$js);
        foreach (self::$js as $js_arr) {
            foreach ($js_arr as $js) {
                if (is_array($js) && 'inline' === (string)@$js['type']) {
                    echo '<script type="text/javascript" defer>', $js['value'], '</script>', "\r\n";
                    continue;
                }
                if ($hasCache && isset($cache[trim($js)])) {
                    continue;
                }
                if ($std && strpos($js, 'http') === false) {
                    $js = "http://$sub$std$js";
                }
                echo '<script type="text/javascript" defer src="', $js, $v && strpos(
                    $js,
                    'http'
                ) === false ? '?v=' . $v : '', '"></script>', "\r\n";
            }
        }

        if (Container::getConfig()->devMode) {
            Container::get('debugbar')->render();
        }
    }

    public static function seo(
        string $title,
        ?string $description = '',
        ?string $keywords = '',
        bool $final = false,
        ?string $metatags = ''
    ): void {
        if (!self::$seoIs) {
            self::$seoTitle = $title;
            self::$seoDescription = $description ? $description : self::$seoDescription;
            self::$seoKeywords = $keywords ? $keywords : self::$seoKeywords;
            self::$seoMetatags = $metatags ? $metatags : self::$seoMetatags;
            self::$seoIs = $final;
        }
    }
}
