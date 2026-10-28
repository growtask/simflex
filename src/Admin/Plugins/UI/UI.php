<?php

namespace Simflex\Core\Admin\Plugins\UI;

use Simflex\Core\Admin\Page;

class UI
{
    public static function datePicker(): void
    {
        static::js('/theme/ui/datepicker/bootstrap-datepicker.js', 5);
        static::js('/theme/ui/datepicker/bootstrap-datepicker.ru.js', 5);
        static::css('/theme/ui/datepicker/datepicker.css', 5);
    }

    public static function timePicker(): void
    {
        static::js('/theme/ui/timepicker/bootstrap-timepicker.min.js', 5);
        static::css('/theme/ui/timepicker/bootstrap-timepicker.min.css', 5);
    }

    public static function dateTimePicker(): void
    {
        static::js('/theme/ui/datetimepicker/bootstrap-datetimepicker.js', 5);
        static::js('/theme/ui/datetimepicker/bootstrap-datetimepicker.ru.js', 5);
        static::css('/theme/ui/datetimepicker/datetimepicker.css', 5);
    }

    public static function fileInput(): void
    {
        static::js('/theme/ui/fileinput/bootstrap-fileinput.js', 5);
        static::css('/theme/ui/fileinput/bootstrap-fileinput.css', 5);
    }

    protected static function js(string $file, int $idx): void
    {
        Page::get()->js(Page::webVendorPath() . $file, $idx);
    }

    protected static function css(string $file, int $idx): void
    {
        Page::get()->css(Page::webVendorPath() . $file, $idx);
    }
}
