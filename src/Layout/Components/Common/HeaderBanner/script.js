/* Строка над шапкой: закрытие запоминается в localStorage (с try/catch: хранилище может быть недоступно) */
(function () {
    var STORAGE_KEY = 'simflex:header-banner';

    function read() {
        try {
            return localStorage.getItem(STORAGE_KEY);
        } catch (e) {
            return null;
        }
    }

    function write(value) {
        try {
            localStorage.setItem(STORAGE_KEY, value);
        } catch (e) {
            // без запоминания строка появится снова при следующей загрузке
        }
    }

    function init(banner) {
        var key = banner.getAttribute('data-header-banner');
        if (read() === key) {
            banner.hidden = true;
            return;
        }
        var close = banner.querySelector('[data-header-banner-close]');
        if (!close) {
            return;
        }
        close.addEventListener('click', function () {
            banner.hidden = true;
            write(key);
            document.dispatchEvent(new CustomEvent('header-banner:close'));
        });
    }

    function initAll() {
        document.querySelectorAll('[data-header-banner]').forEach(init);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
