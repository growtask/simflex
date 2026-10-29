/**
 * Слайдер баннера (.banner[data-banner]): стрелки, свайп, клавиши, автопрокрутка. Без библиотек.
 * Атрибуты корня: data-autoplay (1|0), data-interval (мс).
 * Доступность: неактивные слайды aria-hidden + inert, крайние стрелки disabled + aria-disabled,
 * автопрокрутка стоит при наведении, фокусе внутри баннера, скрытой вкладке и prefers-reduced-motion.
 */
class BannerSlider {
    /** Порог свайпа, px */
    static SWIPE_THRESHOLD = 50;

    /**
     * @param {HTMLElement} root
     */
    constructor(root) {
        this.root = root;
        this.track = root.querySelector('[data-banner-track]');
        this.slides = Array.from(root.querySelectorAll('.banner__slide'));
        this.prev = root.querySelector('[data-banner-prev]');
        this.next = root.querySelector('[data-banner-next]');
        this.steps = root.querySelector('[data-banner-steps]');
        this.viewport = root.querySelector('.banner__viewport');
        this.index = 0;
        this.timer = null;
        this.paused = false;
        this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        this.autoplay = root.dataset.autoplay === '1' && !this.reducedMotion.matches;
        this.interval = Math.max(2000, parseInt(root.dataset.interval, 10) || 6000);

        if (!this.track || this.slides.length < 2) {
            return;
        }

        this.bind();
        this.render();
        this.startAutoplay();
    }

    bind() {
        this.prev?.addEventListener('click', () => this.go(this.index - 1, true));
        this.next?.addEventListener('click', () => this.go(this.index + 1, true));

        // Клавиши: стрелки, Home и End, когда фокус внутри баннера
        this.root.addEventListener('keydown', (e) => {
            if (e.target.closest('input, textarea, select')) {
                return;
            }
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                this.go(this.index - 1, true);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                this.go(this.index + 1, true);
            } else if (e.key === 'Home') {
                e.preventDefault();
                this.go(0, true);
            } else if (e.key === 'End') {
                e.preventDefault();
                this.go(this.slides.length - 1, true);
            }
        });

        this.bindSwipe();

        // Пауза автопрокрутки
        this.root.addEventListener('mouseenter', () => this.pause());
        this.root.addEventListener('mouseleave', () => this.resume());
        this.root.addEventListener('focusin', () => this.pause());
        this.root.addEventListener('focusout', (e) => {
            if (!this.root.contains(e.relatedTarget)) {
                this.resume();
            }
        });
        document.addEventListener('visibilitychange', () => {
            document.hidden ? this.stopAutoplay() : this.startAutoplay();
        });
        this.reducedMotion.addEventListener?.('change', () => {
            this.autoplay = this.root.dataset.autoplay === '1' && !this.reducedMotion.matches;
            this.autoplay ? this.startAutoplay() : this.stopAutoplay();
        });
    }

    bindSwipe() {
        let startX = 0;
        let startY = 0;
        let active = false;

        this.viewport.addEventListener('pointerdown', (e) => {
            // Мышью не листаем: на десктопе есть стрелки, а выделение текста не должно листать
            if (e.pointerType === 'mouse' || e.target.closest('a, button')) {
                return;
            }
            active = true;
            startX = e.clientX;
            startY = e.clientY;
        });

        const finish = (e) => {
            if (!active) {
                return;
            }
            active = false;
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            if (Math.abs(dx) >= BannerSlider.SWIPE_THRESHOLD && Math.abs(dx) > Math.abs(dy)) {
                this.go(this.index + (dx < 0 ? 1 : -1), true);
            }
        };
        this.viewport.addEventListener('pointerup', finish);
        this.viewport.addEventListener('pointercancel', () => {
            active = false;
        });
    }

    /**
     * Переход к слайду
     * @param {number} index
     * @param {boolean} byUser Действие пользователя: автопрокрутка перезапускается с паузой
     */
    go(index, byUser = false) {
        const target = Math.min(Math.max(index, 0), this.slides.length - 1);
        if (target !== this.index) {
            this.index = target;
            this.render();
        }
        if (byUser) {
            this.startAutoplay();
        }
    }

    render() {
        const last = this.slides.length - 1;
        this.track.style.transform = `translateX(${-this.index * 100}%)`;

        this.slides.forEach((slide, i) => {
            const isActive = i === this.index;
            slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            slide.toggleAttribute('inert', !isActive);
        });

        this.setArrow(this.prev, this.index === 0);
        this.setArrow(this.next, this.index === last);

        if (this.steps) {
            this.steps.dataset.active = String(this.index);
            this.steps.querySelectorAll('.step-indicator__item').forEach((item, i) => {
                item.classList.toggle('step-indicator__item_active', i === this.index);
            });
            const caption = this.steps.querySelector('.visually-hidden');
            if (caption) {
                caption.textContent = `${this.index + 1} / ${this.slides.length}`;
            }
        }
    }

    /**
     * Крайняя стрелка отключена и остаётся в порядке чтения (aria-disabled)
     * @param {HTMLElement|null} button
     * @param {boolean} disabled
     */
    setArrow(button, disabled) {
        if (!button) {
            return;
        }
        // Если фокус был на кнопке, которая отключается, переносим его на соседнюю
        if (disabled && document.activeElement === button) {
            (button === this.prev ? this.next : this.prev)?.focus();
        }
        button.disabled = disabled;
        button.setAttribute('aria-disabled', disabled ? 'true' : 'false');
    }

    pause() {
        this.paused = true;
        this.stopAutoplay();
    }

    resume() {
        this.paused = false;
        this.startAutoplay();
    }

    startAutoplay() {
        this.stopAutoplay();
        // Состояние читается живой областью только без автопрокрутки
        this.track.setAttribute('aria-live', this.autoplay ? 'off' : 'polite');
        if (!this.autoplay || this.paused || document.hidden) {
            return;
        }
        this.timer = window.setTimeout(() => {
            // В конце возвращаемся к первому слайду
            this.go(this.index === this.slides.length - 1 ? 0 : this.index + 1);
            this.startAutoplay();
        }, this.interval);
    }

    stopAutoplay() {
        if (this.timer !== null) {
            window.clearTimeout(this.timer);
            this.timer = null;
        }
    }
}

function initBannerSliders() {
    document.querySelectorAll('.banner[data-banner]').forEach((root) => {
        if (!root.__bannerSlider) {
            root.__bannerSlider = new BannerSlider(root);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBannerSliders);
} else {
    initBannerSliders();
}
