/* Закреплённая копия шапки: показывается, когда основная шапка ушла за верх окна */
(function () {
    function init() {
        var header = document.querySelector('header.header');
        var fixed = document.querySelector('[data-header-fixed]');
        if (!header || !fixed) {
            return;
        }

        var visible = false;
        var ticking = false;

        function setVisible(value) {
            if (value === visible) {
                return;
            }
            visible = value;
            fixed.classList.toggle('header--fixed-visible', value);
            // Скрытая копия не должна попадать в порядок фокуса и дерево доступности
            fixed.toggleAttribute('inert', !value);
            fixed.setAttribute('aria-hidden', value ? 'false' : 'true');
        }

        function update() {
            ticking = false;
            setVisible(header.getBoundingClientRect().bottom < 0);
        }

        function onScroll() {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(update);
            }
        }

        window.addEventListener('scroll', onScroll, {passive: true});
        window.addEventListener('resize', onScroll);
        update();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
