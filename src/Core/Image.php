<?php

namespace Simflex\Core;

class Image extends File
{
    public string $pathBase = 'images';
    protected string $uploadedFilePath;

    /**
     * @var array<string, string> subdir => "WxH" или "WxHx1" (с обрезкой)
     */
    public array $sizes = [];

    /**
     * @var \GdImage|string|null GD-изображение; для SVG - исходный XML
     */
    protected \GdImage|string|false|null $img = null;

    /**
     * @param string $path
     * @param array $sizes subdir => "WxH"
     */
    public function __construct($path, $sizes = [])
    {
        parent::__construct($path);
        $this->sizes = is_array($sizes) ? $sizes : [];
        $this->sizes['source'] = '2000x10000';
        // preview/ используется админкой (FieldImage) для миниатюр, поэтому создается всегда
        if (!isset($this->sizes['preview'])) {
            $this->sizes['preview'] = '90x90';
        }
    }

    public function loadPost(string $fileName): void
    {
        if (is_uploaded_file($this->uploadedFilePath = $_FILES[$fileName]['tmp_name'])) {
            switch ($_FILES[$fileName]['type']) {
                case 'image/gif':
                    $this->type = 'gif';
                    $this->img = imagecreatefromgif($_FILES[$fileName]['tmp_name']);
                    break;
                case 'image/jpeg':
                    $this->type = 'jpg';
                    $this->img = imagecreatefromjpeg($_FILES[$fileName]['tmp_name']);
                    break;
                case 'image/pjpeg':
                    $this->type = 'jpg';
                    $this->img = imagecreatefromjpeg($_FILES[$fileName]['tmp_name']);
                    break;
                case 'image/webp':
                    $this->type = 'webp';
                    $this->img = imagecreatefromwebp($_FILES[$fileName]['tmp_name']);
                    break;
                case 'image/png':
                    $this->type = 'png';
                    $this->img = imagecreatefrompng($_FILES[$fileName]['tmp_name']);
                    imageAlphaBlending($this->img, false);
                    imageSaveAlpha($this->img, true);
                    break;
                case 'image/svg+xml':
                    $this->type = 'svg';
                    $this->img = file_get_contents($_FILES[$fileName]['tmp_name']);
                    break;
            }
        }
    }

    public function convertTypeToStr(): void
    {
        if ($this->type == IMAGETYPE_JPEG) {
            $this->type = 'jpg';
        } elseif ($this->type == IMAGETYPE_GIF) {
            $this->type = 'gif';
        } elseif ($this->type == IMAGETYPE_PNG) {
            $this->type = 'png';
        } elseif ($this->type == IMAGETYPE_WEBP) {
            $this->type = 'webp';
        } elseif ($this->type == IMAGETYPE_SVG) {
            $this->type = 'svg';
        }
    }

    public function getTypeStr(): string|false
    {
        if ((string)$this->type === (string)(int)$this->type) {
            $type = '';
            if ($this->type == IMAGETYPE_JPEG) {
                $type = 'jpg';
            } elseif ($this->type == IMAGETYPE_GIF) {
                $type = 'gif';
            } elseif ($this->type == IMAGETYPE_PNG) {
                $type = 'png';
            }
            return $type;
        }
        return $this->type;
    }

    public function save(): void
    {
        if (is_string($this->img)) {
            // SVG - векторное изображение, масштабирование не требуется: кладем исходник во все размеры
            copy($this->uploadedFilePath, $this->outputMkDirReturnFilePath('raw'));
            foreach ($this->sizes as $subdir => $_) {
                file_put_contents($this->outputMkDirReturnFilePath($subdir), $this->img);
            }
            return;
        }
        if ($this->img) {
            copy($this->uploadedFilePath, $this->outputMkDirReturnFilePath('raw'));
            $img = null;
            $width = imagesx($this->img);
            $height = imagesy($this->img);
            foreach ($this->sizes as $subdir => $size) {
                $s = explode('x', $size);
                if (count($s) == 3 && $s[2] == '1') {
                    $w = (int)$s[0];
                    $h = (int)$s[1];
                    $kh = $h / $w;
                    $sw = $width;
                    $sh = round($width * $kh);
                    if ($sh > $height) {
                        $sh = $height;
                        $sw = round($height / $kh);
                    }
                    $img = imagecreatetruecolor($w, $h);
                    imageAlphaBlending($img, false);
                    imageSaveAlpha($img, true);
                    if ($width > $height) {
                        imagecopyresampled($img, $this->img, 0, 0, round(($width - $sw) / 2), 0, $w, $h, $sw, $sh);
                    } else {
                        imagecopyresampled($img, $this->img, 0, 0, 0, 0, $w, $h, $sw, $sh);
                    }
                } else {
                    $w = (int)$s[0];
                    $h = (int)$s[1];
                    $nw = min($w, $width);
                    $nh = $nw * $height / $width;
                    if ($nh > $h) {
                        $nh = $h;
                        $nw = $nh / $height * $width;
                    }
                    if ($width > $w || $height > $h) {
                        $img = imagecreatetruecolor($nw, $nh);
                        imageAlphaBlending($img, false);
                        imageSaveAlpha($img, true);
                        imagecopyresampled($img, $this->img, 0, 0, 0, 0, (int)$nw, (int)$nh, $width, $height);
                    } else {
                        $img = $this->img;
                    }
                }
                $this->output($img, $subdir);
            }
        }
    }

    protected function outputMkDirReturnFilePath($subdir): string
    {
        $typeStr = $this->getTypeStr($this->type);
        $name = $this->name . '.' . $typeStr;
        $folder = '';
        if ($subdir) {
            $folder = $subdir . '/';
            if (!is_dir($this->path . $folder)) {
                if (!mkdir($this->path . $folder)) {
                    die('<b>Fatal Error!</b> Can not create folder ' . $this->path . $folder);
                }
            }
        }
        return $this->path . $folder . $name;
    }

    private function output($img, $subdir): ?bool
    {
        $typeStr = $this->getTypeStr($this->type);
        $file = $this->outputMkDirReturnFilePath($subdir);
        switch ($typeStr) {
            case 'gif':
                return imagegif($img, $file);
            case 'jpg':
                return imagejpeg($img, $file, 100);
            case 'png':
                return imagepng($img, $file);
            case 'webp':
                return imagewebp($img, $file);
        }
        return null;
    }

    public function delete($name): bool|int
    {
        parent::delete($name);
        $success = true;
        foreach ($this->sizes + ['raw' => ''] as $subdir => $_) {
            $folder = '';
            if ($subdir) {
                $folder = $subdir . '/';
            }
            if (is_file($f = $this->path . $folder . $name)) {
                $success &= @unlink($f);
            }
        }
        return $success;
    }

    /**
     * Загружает изображение в свой буфер
     */
    public function load(string $filename, $leaveName = false): void
    {

        $image_info = getimagesize($filename);
        $this->type = $image_info[2];
        if ($this->type == IMAGETYPE_JPEG) {

            $this->img = imagecreatefromjpeg($filename);
        } elseif ($this->type == IMAGETYPE_GIF) {

            $this->img = imagecreatefromgif($filename);
        } elseif ($this->type == IMAGETYPE_PNG) {

            $this->img = imagecreatefrompng($filename);
        }
        // исходник для raw/ в save()
        $this->uploadedFilePath = $filename;

        if ($leaveName) {
            $this->name = pathinfo($filename, PATHINFO_FILENAME);
        }
    }

    protected function getWidth(): int
    {
        return imagesx($this->img);
    }

    protected function getHeight(): int
    {
        return imagesy($this->img);
    }

    /**
     * Изменяет размер изображения, подгоняет по высоте, сохраняя пропорции
     * @param int $height
     */
    public function resizeToHeight($height): void
    {

        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * Изменяет размер изображения, подгоняет по ширине, сохраняя пропорции
     * @param int $width
     */
    public function resizeToWidth($width): void
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * Изменяет размер изображения, сохраняя пропорции
     * @param int $scale Пропорция в процентах. Пример: $scale = 50, изображение уменьшится в 2 раза
     */
    public function scale($scale): void
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }

    /**
     * Изменяет размер изображения
     * @param int $width
     * @param int $height
     */
    public function resize($width, $height): void
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->img, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->img = $new_image;
    }

    /**
     *
     * @param string $filename
     * @param int|false $image_type (optional) IMAGETYPE_JPEG, IMAGETYPE_GIF, ...
     * @param int (optional) $compression Процент сжатия качества
     * @param int (optional) $permissions Только для *nix систем
     */
    public function saveAs($filename, $image_type = false, $compression = 100, $permissions = null): void
    {

        if ($image_type) {
            $this->type = $image_type;
        }
        if ($this->type == IMAGETYPE_JPEG) {
            imagejpeg($this->img, $filename, $compression);
        } elseif ($this->type == IMAGETYPE_GIF) {
            imagegif($this->img, $filename);
        } elseif ($this->type == IMAGETYPE_PNG) {
            imagepng($this->img, $filename);
        }
        if ($permissions != null) {
            chmod($filename, $permissions);
        }
    }
}
