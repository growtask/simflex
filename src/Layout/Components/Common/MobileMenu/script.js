/*
 * Мобильное меню: role="dialog", ловушка фокуса, Esc, блокировка прокрутки, возврат фокуса.
 * Открывается кнопкой с data-mobile-menu-open="{id}", закрывается кнопкой с data-mobile-menu-close.
 */
(function () {
    var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
    var DESKTOP_QUERY = '(min-width: 812px)';

    function init() {
        var menu = document.querySelector('[data-mobile-menu]');
        if (!menu) {
            return;
        }

        var opener = null;
        var prevOverflow = '';

        function isOpen() {
            return !menu.hidden;
        }

        function getFocusable() {
            return Array.prototype.filter.call(menu.querySelectorAll(FOCUSABLE), function (el) {
                // скрытые подменю и недоступные элементы в порядок фокуса не входят
                return el.offsetParent !== null || el === document.activeElement;
            });
        }

        function setExpandedState(button, value) {
            if (button) {
                button.setAttribute('aria-expanded', value ? 'true' : 'false');
            }
        }

        function open(button) {
            if (isOpen()) {
                return;
            }
            opener = button || null;
            menu.hidden = false;
            // класс включает анимацию появления после снятия hidden
            window.requestAnimationFrame(function () {
                menu.classList.add('mobile-menu--open');
            });
            prevOverflow = document.documentElement.style.overflow;
            document.documentElement.style.overflow = 'hidden';
            document.querySelectorAll('[data-mobile-menu-open]').forEach(function (b) {
                setExpandedState(b, true);
            });
            var close = menu.querySelector('[data-mobile-menu-close]');
            if (close) {
                close.focus();
            }
        }

        function close(restoreFocus) {
            if (!isOpen()) {
                return;
            }
            menu.classList.remove('mobile-menu--open');
            menu.hidden = true;
            document.documentElement.style.overflow = prevOverflow;
            document.querySelectorAll('[data-mobile-menu-open]').forEach(function (b) {
                setExpandedState(b, false);
            });
            if (restoreFocus !== false && opener && document.contains(opener) && opener.offsetParent !== null) {
                opener.focus();
            }
            opener = null;
        }

        document.addEventListener('click', function (e) {
            var openBtn = e.target.closest('[data-mobile-menu-open="' + menu.id + '"]');
            if (openBtn) {
                open(openBtn);
            }
        });

        menu.addEventListener('click', function (e) {
            if (e.target.closest('[data-mobile-menu-close]')) {
                close();
                return;
            }
            var toggle = e.target.closest('[data-mobile-menu-toggle]');
            if (toggle) {
                var sub = document.getElementById(toggle.getAttribute('aria-controls'));
                var expand = toggle.getAttribute('aria-expanded') !== 'true';
                setExpandedState(toggle, expand);
                if (sub) {
                    sub.hidden = !expand;
                }
                return;
            }
            // Кнопка открывает другое окно (data-modal) или ссылка ведёт по странице: меню закрывается без возврата фокуса
            if (e.target.closest('[data-modal], a[href]')) {
                close(false);
            }
        });

        menu.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                close();
                return;
            }
            if (e.key !== 'Tab') {
                return;
            }
            // Ловушка фокуса: Tab и Shift+Tab ходят по кругу внутри окна
            var items = getFocusable();
            if (!items.length) {
                e.preventDefault();
                return;
            }
            var first = items[0];
            var last = items[items.length - 1];
            if (e.shiftKey && (document.activeElement === first || !menu.contains(document.activeElement))) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        });

        // Фокус не должен уходить из окна, пока оно открыто
        document.addEventListener('focusin', function (e) {
            if (isOpen() && !menu.contains(e.target)) {
                var items = getFocusable();
                if (items.length) {
                    items[0].focus();
                }
            }
        });

        // При расширении окна до desktop меню закрывается
        var mq = window.matchMedia(DESKTOP_QUERY);
        var onChange = function (e) {
            if (e.matches) {
                close(false);
            }
        };
        if (mq.addEventListener) {
            mq.addEventListener('change', onChange);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
