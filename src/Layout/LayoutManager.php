<?php

namespace Simflex\Layout;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use Simflex\Core\Assets;
use Simflex\Core\Container;
use Simflex\Core\PageAssets;
use Simflex\Debug\Profiler;
use tubalmartin\CssMin\Minifier;

/**
 * Менеджер макетов: собирает стили и скрипты компонентов Layout, отрисованных на странице.
 *
 * Глобальные стили - assetsGlobal ядра; сайт переопределяет их в Layout/assetsGlobal: одноимённый файл сайта
 * (например, styles/tokens/_variables.scss) импортируется вместо файла ядра, style.scss сайта заменяет корневой
 * style.scss ядра, script.js сайта подключается после script.js ядра.
 */
class LayoutManager
{
    /**
     * @var LayoutBase[] Все используемые макеты
     */
    protected static array $usedLayouts = [];

    /**
     * @var array Все используемые стили
     */
    protected static array $usedStyles = [];

    /**
     * @var array Все используемые скрипты
     */
    public static array $usedScripts = [];

    /**
     * @var bool Если true, отрисованные макеты собирают поля своих схем, см. ./sf layout/generate
     */
    public static bool $shouldGenerateFields = false;

    /**
     * @var array<string, list<array{name: string, label: string, class: string, default: string, params: array}>>
     *      Поля, собранные из схем макетов: название блока => поля
     */
    public static array $generatedFields = [];

    /**
     * Подключает макет
     * @param string $class Класс макета
     */
    public static function useLayout(string $class): void
    {
        static::$usedLayouts[$class] = $class;
    }

    /**
     * Подключает стиль
     * @param string $path Путь к css/scss
     */
    public static function useStyle(string $path): void
    {
        static::$usedStyles[] = $path;
    }

    /**
     * Подключает JS-скрипт
     * @param string $path Путь к js
     */
    public static function useScript(string $path): void
    {
        static::$usedScripts[] = $path;
    }

    /**
     * Инициализирует систему макетов
     * @return string Путь к сгенерированному CSS-файлу
     * @throws SassException
     */
    public static function init(): string
    {
        // у админки свои стили и скрипты
        if (SF_LOCATION === SF_LOCATION_ADMIN) {
            return '';
        }

        Profiler::traceStart(__CLASS__, __FUNCTION__);
        foreach (static::globalDirs() as $dir) {
            if (is_file($dir . '/script.js')) {
                PageAssets::js(Assets::publish($dir . '/script.js'));
            }
        }
        foreach (static::$usedLayouts as $class) {
            $class::initAssets();
        }

        static::$usedStyles = array_unique(static::$usedStyles);
        $config = Container::getConfig();

        // путь к кэшу.
        if (!$config->devMode) {
            $cachePath = '';
            foreach (static::$usedStyles as $style) {
                $cachePath .= md5($style);
            }

            $cachePath = '/cache/css/' . md5($cachePath) . '.css';
        } else {
            $cachePath = '/cache/css/style.css';
        }

        // файлы сайта находятся раньше файлов ядра
        $imports = array_reverse(static::globalDirs());

        // проверяем, есть ли файл кэша.
        $compiler = new Compiler();
        if ($config->devMode || !is_file(SF_PUBLIC_PATH . $cachePath)) {
            // неявно добавляем корневой стиль.
            $implicit = '';
            foreach ($imports as $dir) {
                if (is_file($dir . '/style.scss')) {
                    $implicit = file_get_contents($dir . '/style.scss');
                    break;
                }
            }

            // кэшируем стили.
            $styleString = '';

            $compileString = '';
            if ($implicit) {
                $compileString .= $implicit;
            }

            foreach (static::$usedStyles as $style) {
                if (!is_file($style)) {
                    continue;
                }

                $cssData = file_get_contents($style);
                if (str_ends_with($style, '.scss')) {
                    $imports[] = dirname($style);
                    $compileString .= $cssData;
                } else {
                    $styleString .= $cssData;
                }
            }

            // компилируем то, что нужно
            $compiler->setImportPaths($imports);
            $compileString = $compiler->compileString($compileString)->getCss();
            $styleString = $compileString . $styleString;

            // минифицируем итоговый css.
            if (!$config->devMode) {
                $min = new Minifier();
                $styleString = $min->run($styleString);
            }

            // записываем новый стиль.
            if (!is_dir(dirname(SF_PUBLIC_PATH . $cachePath))) {
                mkdir(dirname(SF_PUBLIC_PATH . $cachePath), 0777, true);
            }
            file_put_contents(SF_PUBLIC_PATH . $cachePath, $styleString);
        }

        // используем стиль из кэша.
        PageAssets::css($cachePath . ($config->devMode ? ('?v=' . md5(microtime())) : ''));
        Profiler::traceEnd(__CLASS__, __FUNCTION__);
        return $cachePath;
    }

    /**
     * @return list<string> Директории глобальных стилей и скриптов: ядра и, если есть, сайта
     */
    protected static function globalDirs(): array
    {
        $dirs = [__DIR__ . '/assetsGlobal'];
        if (is_dir(SF_ROOT_PATH . '/Layout/assetsGlobal')) {
            $dirs[] = SF_ROOT_PATH . '/Layout/assetsGlobal';
        }

        return $dirs;
    }
}
