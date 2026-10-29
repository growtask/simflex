<?php

namespace Simflex\Core\Layout\Components\Common\Banner;

use Simflex\Core\Layout\LayoutBase;

/**
 * Баннер-слайдер первого экрана (Figma: Promo/Page/main): слайды с картинкой, заголовком, описанием и кнопкой,
 * стрелки и индикатор шагов. Листание, свайп, клавиатура и автопрокрутка - в script.js.
 * Один слайд выводится без стрелок и индикатора. Первая картинка грузится сразу, остальные лениво.
 *
 * Классы-связи с Figma: .banner (main), __slide, __img, __content, __title (title_h1), __text (desc),
 * __button (button), __arrows (buttons-slide), __steps (steps).
 */
class Layout extends LayoutBase
{
    /**
     * @param array<int,array{title?:string,text?:string,image?:string,alt?:string,buttonText?:string,buttonLink?:string}> $slides
     *        Слайды: заголовок, описание, адрес картинки, alt картинки (по умолчанию пусто: картинка декоративная),
     *        текст и адрес кнопки (кнопка выводится, если заданы оба). Слайд без заголовка и картинки пропускается
     * @param string $className Дополнительные CSS-классы
     * @param string $label Доступное имя карусели
     * @param bool $autoplay Автопрокрутка (останавливается при наведении, фокусе и prefers-reduced-motion)
     * @param int $interval Пауза автопрокрутки, мс
     * @param int $headingLevel Уровень заголовка слайда (1 для первого экрана главной)
     * @param string $prevLabel Имя стрелки «назад»
     * @param string $nextLabel Имя стрелки «вперёд»
     * @param string $slideLabel Шаблон подписи слайда: %1$d - номер, %2$d - всего
     * @param array<string,mixed> $attributes Дополнительные HTML-атрибуты корня
     */
    public static function drawBanner(
        array       $slides = [],
        string      $className = '',
        string      $label = 'Слайдер',
        bool        $autoplay = true,
        int         $interval = 6000,
        int         $headingLevel = 1,
        string      $prevLabel = 'Назад',
        string      $nextLabel = 'Вперёд',
        string      $slideLabel = '%1$d из %2$d',
        BannerTheme $theme = BannerTheme::Light,
        array       $attributes = []
    ): string {
        $slides = array_values(array_filter(
            $slides,
            static fn($slide) => is_array($slide) && (($slide['title'] ?? '') !== '' || ($slide['image'] ?? '') !== '')
        ));
        if (!$slides) {
            return '';
        }

        return static::draw(
            compact(
                'slides',
                'className',
                'label',
                'autoplay',
                'interval',
                'prevLabel',
                'nextLabel',
                'slideLabel',
                'attributes'
            ) + [
                'headingLevel' => min(6, max(1, $headingLevel)),
                'theme' => $theme->value,
            ]
        );
    }
}
