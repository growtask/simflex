/**
 * Модальные окна: открытие по data-modal="<id>", ловушка фокуса, Esc, закрытие по подложке и кнопке,
 * блокировка прокрутки страницы, inert для фона, возврат фокуса на кнопку, открывшую окно.
 * Публичный API: window.SimflexModal.open(id, opener?), close(id?), isOpen(id?).
 * События на корне окна: modal:open, modal:close.
 */
(() => {
    if (window.SimflexModal) {
        return;
    }

    const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
    const CLOSE_DELAY = 250;

    let active = null;
    let opener = null;
    let inerted = [];
    let closeTimer = null;

    const find = id => document.getElementById(id);

    const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const focusables = root => Array.from(root.querySelectorAll(FOCUSABLE))
        .filter(el => !el.closest('[hidden]') && el.getClientRects().length > 0);

    /** Фон за окном недоступен для чтения и ввода */
    const lockPage = modal => {
        document.documentElement.classList.add('modal-open');
        inerted = Array.from(document.body.children).filter(el => el !== modal && !el.hasAttribute('inert') && el.tagName !== 'SCRIPT');
        inerted.forEach(el => el.setAttribute('inert', ''));
    };

    const unlockPage = () => {
        document.documentElement.classList.remove('modal-open');
        inerted.forEach(el => el.removeAttribute('inert'));
        inerted = [];
    };

    const open = (id, trigger = null) => {
        const modal = find(id);
        if (!modal || active === modal) {
            return;
        }
        if (active) {
            close();
        }
        clearTimeout(closeTimer);

        // окно лежит в body, чтобы inert фона его не затрагивал
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        opener = trigger || document.activeElement;
        active = modal;
        modal.hidden = false;
        lockPage(modal);
        // принудительный reflow, чтобы переход запустился
        void modal.offsetWidth;
        modal.classList.add('is-open');
        modal.querySelector('.modal__dialog').focus({preventScroll: true});
        modal.dispatchEvent(new CustomEvent('modal:open', {bubbles: true}));
    };

    const close = id => {
        const modal = id ? find(id) : active;
        if (!modal || modal !== active) {
            return;
        }

        active = null;
        modal.classList.remove('is-open');
        unlockPage();

        const finish = () => {
            if (!active) {
                modal.hidden = true;
            }
        };
        if (prefersReducedMotion()) {
            finish();
        } else {
            closeTimer = setTimeout(finish, CLOSE_DELAY);
        }

        const back = opener;
        opener = null;
        if (back && document.contains(back) && typeof back.focus === 'function') {
            back.focus({preventScroll: true});
        }
        modal.dispatchEvent(new CustomEvent('modal:close', {bubbles: true}));
    };

    document.addEventListener('click', event => {
        const trigger = event.target.closest('[data-modal]');
        if (trigger) {
            event.preventDefault();
            open(trigger.getAttribute('data-modal'), trigger);
            return;
        }

        if (!active) {
            return;
        }
        if (event.target.closest('[data-modal-close]') && active.contains(event.target)) {
            event.preventDefault();
            close();
            return;
        }
        if (event.target.hasAttribute('data-modal-overlay') && active.hasAttribute('data-modal-overlay-close')) {
            close();
        }
    });

    document.addEventListener('keydown', event => {
        if (!active) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            close();
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        // ловушка фокуса: Tab ходит по кругу внутри окна
        const dialog = active.querySelector('.modal__dialog');
        const items = focusables(dialog);
        if (!items.length) {
            event.preventDefault();
            dialog.focus();
            return;
        }
        const first = items[0];
        const last = items[items.length - 1];
        const current = document.activeElement;
        if (event.shiftKey && (current === first || current === dialog)) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && current === last) {
            event.preventDefault();
            first.focus();
        } else if (!dialog.contains(current)) {
            event.preventDefault();
            first.focus();
        }
    });

    window.SimflexModal = {
        open,
        close,
        isOpen: id => (id ? active === find(id) : active !== null),
    };
})();
