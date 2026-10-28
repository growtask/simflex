<?php

namespace Simflex\Media;

/**
 * Загруженное изображение: public/uf/images/<path>/<размер>/<имя>.<расширение>.
 *
 * save() кладёт исходник в raw/ и уменьшенные копии в каталоги размеров $sizes: source (до 2000x10000)
 * и preview (90x90, миниатюры админки) есть всегда. SVG не масштабируется - во все размеры копируется исходник
 */
class Image extends File
{
    /** Расширения по типу GD (IMAGETYPE_*) */
    protected const array TYPES = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_GIF => 'gif',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];

    public string $pathBase = 'images';

    /**
     * @var array<string, string> Каталог размера => "ШxВ" (вписать) или "ШxВx1" (обрезать по центру до ШxВ)
     */
    public array $sizes = [];

    /** Изображение GD; для SVG - исходный XML */
    protected \GdImage|string|null $img = null;

    /** Исходный файл: копируется в raw/ */
    protected string $source = '';

    /**
     * @param string $path Подкаталог внутри images
     * @param array<string, string> $sizes Каталог размера => "ШxВ" или "ШxВx1"
     */
    public function __construct(string $path = '', array $sizes = [])
    {
        parent::__construct($path);
        $this->sizes = $sizes + ['preview' => '90x90'];
        $this->sizes['source'] = '2000x10000';
    }

    /**
     * Читает загруженное изображение $_FILES[$field]: JPEG, PNG, GIF, WebP, SVG (тип - по содержимому файла)
     * @return bool false - файла нет или это не изображение
     */
    public function loadPost(string $field): bool
    {
        $tmp = (string)($_FILES[$field]['tmp_name'] ?? '');
        if (!is_uploaded_file($tmp)) {
            return false;
        }

        return $this->load($tmp, keepName: false);
    }

    /**
     * Читает изображение из файла
     * @param bool $keepName Взять имя у файла, иначе - сгенерированное
     */
    public function load(string $filename, bool $keepName = false): bool
    {
        $this->img = null;
        $this->type = '';

        if (mime_content_type($filename) === 'image/svg+xml') {
            $this->img = (string)file_get_contents($filename);
            $this->type = 'svg';
        } else {
            $type = static::TYPES[@getimagesize($filename)[2] ?? 0] ?? '';
            $img = match ($type) {
                'jpg' => imagecreatefromjpeg($filename),
                'gif' => imagecreatefromgif($filename),
                'png' => imagecreatefrompng($filename),
                'webp' => imagecreatefromwebp($filename),
                default => false,
            };
            if (!$img) {
                return false;
            }
            imagealphablending($img, false);
            imagesavealpha($img, true);
            $this->img = $img;
            $this->type = $type;
        }

        $this->source = $filename;
        if ($keepName) {
            $this->name = pathinfo($filename, PATHINFO_FILENAME);
        }

        return true;
    }

    /**
     * Сохраняет исходник в raw/ и копии во все размеры
     */
    public function save(): void
    {
        if ($this->img === null) {
            return;
        }

        copy($this->source, $this->getFilePath('raw'));
        foreach ($this->sizes as $dir => $size) {
            if (is_string($this->img)) {
                file_put_contents($this->getFilePath($dir), $this->img);
            } else {
                $this->write($this->getResized($size), $this->getFilePath($dir));
            }
        }
    }

    /**
     * Удаляет изображение из всех размеров и raw/
     * @return bool false - какой-то из файлов удалить не удалось
     */
    public function delete(string $name): bool
    {
        $success = true;
        foreach (['', ...array_keys($this->sizes), 'raw'] as $dir) {
            $file = $this->path . ($dir !== '' ? $dir . '/' : '') . $name;
            if (is_file($file)) {
                $success = unlink($file) && $success;
            }
        }

        return $success;
    }

    /**
     * Меняет размер по высоте, сохраняя пропорции
     */
    public function resizeToHeight(int $height): void
    {
        $this->resize((int)round(imagesx($this->img) * $height / imagesy($this->img)), $height);
    }

    /**
     * Меняет размер по ширине, сохраняя пропорции
     */
    public function resizeToWidth(int $width): void
    {
        $this->resize($width, (int)round(imagesy($this->img) * $width / imagesx($this->img)));
    }

    /**
     * Масштабирует, сохраняя пропорции
     * @param int $percent Процент от исходного размера: 50 - вдвое меньше
     */
    public function scale(int $percent): void
    {
        $this->resize(
            (int)round(imagesx($this->img) * $percent / 100),
            (int)round(imagesy($this->img) * $percent / 100)
        );
    }

    public function resize(int $width, int $height): void
    {
        $img = $this->createCanvas($width, $height);
        imagecopyresampled($img, $this->img, 0, 0, 0, 0, $width, $height, imagesx($this->img), imagesy($this->img));
        $this->img = $img;
    }

    /**
     * Сохраняет изображение в файл
     * @param string|null $type jpg, png, gif, webp; null - тип загруженного изображения
     * @param int $quality Качество JPEG и WebP, 0-100
     * @param int|null $permissions Права на файл (*nix)
     */
    public function saveAs(string $filename, ?string $type = null, int $quality = 100, ?int $permissions = null): void
    {
        $this->write($this->img, $filename, $type ?? $this->type, $quality);
        if ($permissions !== null) {
            chmod($filename, $permissions);
        }
    }

    /**
     * Копия под размер "ШxВ" (вписать, не увеличивая) или "ШxВx1" (обрезать по центру)
     */
    protected function getResized(string $size): \GdImage
    {
        [$w, $h, $crop] = array_map('intval', explode('x', $size)) + [0, 0, 0];
        $width = imagesx($this->img);
        $height = imagesy($this->img);

        if ($crop === 1) {
            // наибольшая область исходника с пропорциями ШxВ
            $sw = min($width, (int)round($height * $w / $h));
            $sh = min($height, (int)round($width * $h / $w));
            $img = $this->createCanvas($w, $h);
            imagecopyresampled($img, $this->img, 0, 0, intdiv($width - $sw, 2), 0, $w, $h, $sw, $sh);
            return $img;
        }

        if ($width <= $w && $height <= $h) {
            return $this->img;
        }
        $ratio = min($w / $width, $h / $height);
        $nw = max(1, (int)round($width * $ratio));
        $nh = max(1, (int)round($height * $ratio));
        $img = $this->createCanvas($nw, $nh);
        imagecopyresampled($img, $this->img, 0, 0, 0, 0, $nw, $nh, $width, $height);

        return $img;
    }

    protected function createCanvas(int $width, int $height): \GdImage
    {
        $img = imagecreatetruecolor($width, $height);
        imagealphablending($img, false);
        imagesavealpha($img, true);

        return $img;
    }

    /**
     * Путь к файлу изображения в каталоге размера, каталог создаётся при необходимости
     */
    protected function getFilePath(string $dir): string
    {
        return static::makeDir($this->path . $dir) . $this->getName();
    }

    protected function write(\GdImage $img, string $file, ?string $type = null, int $quality = 100): bool
    {
        return match ($type ?? $this->type) {
            'jpg' => imagejpeg($img, $file, $quality),
            'gif' => imagegif($img, $file),
            'png' => imagepng($img, $file),
            'webp' => imagewebp($img, $file, $quality),
            default => false,
        };
    }
}
