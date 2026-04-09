(function () {
    if (window.AppPopup) {
        return;
    }

    const STYLE_ID = 'app-popup-style';
    const HOST_ID = 'app-popup-host';
    const TOAST_HOST_ID = 'app-toast-host';

    function ensureStyles() {
        if (document.getElementById(STYLE_ID)) return;

        const style = document.createElement('style');
        style.id = STYLE_ID;
        style.textContent = `
            @keyframes app-popup-overlay-in {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes app-popup-card-in {
                from { opacity: 0; transform: translateY(10px) scale(0.97); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
            @keyframes app-toast-in {
                from { opacity: 0; transform: translate(-50%, -16px) scale(0.96); }
                to { opacity: 1; transform: translate(-50%, 0) scale(1); }
            }
            @keyframes app-toast-out {
                from { opacity: 1; transform: translate(-50%, 0) scale(1); }
                to { opacity: 0; transform: translate(-50%, -10px) scale(0.98); }
            }
            .app-popup-overlay {
                position: fixed;
                inset: 0;
                z-index: 3000;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(9, 17, 12, 0.45);
                padding: 16px;
                animation: app-popup-overlay-in 0.18s ease-out;
            }
            .app-popup-card {
                width: min(520px, 100%);
                border-radius: 14px;
                overflow: hidden;
                border: 1px solid #dae5d6;
                background: #fff;
                box-shadow: 0 20px 44px rgba(13, 26, 18, 0.25);
                font-family: 'Poppins', sans-serif;
                animation: app-popup-card-in 0.2s ease-out;
            }
            .app-popup-header {
                padding: 14px 16px;
                color: #fff;
                font-weight: 700;
                font-size: 14px;
                letter-spacing: 0.2px;
            }
            .app-popup-header.info {
                background: linear-gradient(135deg, #2e7d1e, #3f9b2d);
            }
            .app-popup-header.warn {
                background: linear-gradient(135deg, #a54322, #c95c34);
            }
            .app-popup-body {
                padding: 16px;
                color: #1d2921;
                font-size: 13px;
                line-height: 1.55;
                white-space: pre-line;
            }
            .app-popup-input-wrap {
                padding: 0 16px;
            }
            .app-popup-input {
                width: 100%;
                border: 1px solid #cfdbca;
                border-radius: 10px;
                min-height: 40px;
                padding: 8px 10px;
                outline: none;
                font-size: 13px;
                font-family: 'Poppins', sans-serif;
                transition: box-shadow 0.14s ease, border-color 0.14s ease;
            }
            .app-popup-input:focus {
                border-color: #2e7d1e;
                box-shadow: 0 0 0 3px rgba(46, 125, 30, 0.12);
            }
            .app-popup-actions {
                display: flex;
                justify-content: flex-end;
                gap: 8px;
                padding: 16px;
            }
            .app-popup-btn {
                border: none;
                border-radius: 10px;
                min-width: 84px;
                padding: 8px 14px;
                font-size: 12px;
                font-weight: 700;
                cursor: pointer;
                font-family: 'Poppins', sans-serif;
                transition: transform 0.12s ease, box-shadow 0.12s ease, filter 0.12s ease;
            }
            .app-popup-btn:hover {
                transform: translateY(-1px);
                filter: brightness(1.02);
            }
            .app-popup-btn.cancel {
                background: #ecf2ea;
                color: #2e3b32;
            }
            .app-popup-btn.ok {
                background: #2e7d1e;
                color: #fff;
            }
            .app-toast-host {
                position: fixed;
                top: 16px;
                left: 50%;
                z-index: 3400;
                pointer-events: none;
            }
            .app-toast {
                min-width: 260px;
                max-width: min(720px, calc(100vw - 28px));
                border-radius: 12px;
                border: 1px solid rgba(255, 255, 255, 0.35);
                box-shadow: 0 16px 30px rgba(6, 20, 10, 0.26);
                color: #fff;
                font-family: 'Poppins', sans-serif;
                font-size: 13px;
                line-height: 1.45;
                padding: 10px 14px;
                margin-bottom: 10px;
                white-space: pre-line;
                transform: translate(-50%, 0);
                animation: app-toast-in 0.2s ease-out forwards;
            }
            .app-toast.info,
            .app-toast.success {
                background: linear-gradient(135deg, #2e7d1e, #3f9b2d);
            }
            .app-toast.warn,
            .app-toast.error {
                background: linear-gradient(135deg, #a54322, #c95c34);
            }
            .app-toast.fade-out {
                animation: app-toast-out 0.18s ease-in forwards;
            }
        `;

        document.head.appendChild(style);
    }

    function ensureHost() {
        let host = document.getElementById(HOST_ID);
        if (host) return host;

        host = document.createElement('div');
        host.id = HOST_ID;
        document.body.appendChild(host);
        return host;
    }

    function ensureToastHost() {
        let host = document.getElementById(TOAST_HOST_ID);
        if (host) return host;

        host = document.createElement('div');
        host.id = TOAST_HOST_ID;
        host.className = 'app-toast-host';
        document.body.appendChild(host);
        return host;
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    let toastQueue = [];
    let isToastShowing = false;
    const toastCache = {};

    function processToastQueue() {
        if (isToastShowing || toastQueue.length === 0) return;

        isToastShowing = true;
        const { message, type, duration } = toastQueue.shift();
        ensureStyles();
        const host = ensureToastHost();

        const toast = document.createElement('div');
        toast.className = `app-toast ${type || 'info'}`;
        toast.textContent = String(message || '');
        host.appendChild(toast);

        const stay = Math.max(600, Number(duration || 2200));

        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => {
                toast.remove();
                isToastShowing = false;
                processToastQueue();
            }, 190);
        }, stay);
    }

    function showToast(message, type, duration) {
        const cacheKey = `${type}|${message}`;
        const now = Date.now();

        if (toastCache[cacheKey] && now - toastCache[cacheKey] < 500) {
            return Promise.resolve(true);
        }

        toastCache[cacheKey] = now;
        toastQueue.push({ message, type: type || 'info', duration: duration || 2200 });
        processToastQueue();

        return Promise.resolve(true);
    }

    function openModal(options) {
        ensureStyles();
        const host = ensureHost();

        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.className = 'app-popup-overlay';

            const headerType = options.type === 'warn' ? 'warn' : 'info';
            const title = escapeHtml(options.title || 'KABSCHOLAR');
            const message = escapeHtml(options.message || '');

            const wantsInput = options.mode === 'prompt';
            const wantsCancel = options.mode === 'confirm' || options.mode === 'prompt';

            overlay.innerHTML = `
                <div class="app-popup-card" role="dialog" aria-modal="true" aria-label="${title}" tabindex="-1">
                    <div class="app-popup-header ${headerType}">${title}</div>
                    <div class="app-popup-body">${message}</div>
                    ${wantsInput ? '<div class="app-popup-input-wrap"><input class="app-popup-input" type="text" /></div>' : ''}
                    <div class="app-popup-actions">
                        ${wantsCancel ? '<button type="button" class="app-popup-btn cancel">Cancel</button>' : ''}
                        <button type="button" class="app-popup-btn ok">OK</button>
                    </div>
                </div>
            `;

            host.appendChild(overlay);

            const card = overlay.querySelector('.app-popup-card');
            const okBtn = overlay.querySelector('.app-popup-btn.ok');
            const cancelBtn = overlay.querySelector('.app-popup-btn.cancel');
            const input = overlay.querySelector('.app-popup-input');

            if (input) {
                input.value = options.defaultValue || '';
                input.placeholder = options.placeholder || '';
                setTimeout(() => input.focus(), 20);
            } else if (okBtn) {
                setTimeout(() => okBtn.focus(), 20);
            } else if (card) {
                setTimeout(() => card.focus(), 20);
            }

            function close(result) {
                overlay.remove();
                resolve(result);
            }

            if (okBtn) {
                okBtn.addEventListener('click', () => {
                    if (!input) {
                        close(true);
                        return;
                    }

                    const value = input.value || '';
                    if (options.required && value.trim() === '') {
                        input.focus();
                        input.style.borderColor = '#c64035';
                        return;
                    }

                    close(value);
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => close(options.mode === 'prompt' ? null : false));
            }

            overlay.addEventListener('click', (event) => {
                if (event.target === overlay) {
                    close(options.mode === 'prompt' ? null : false);
                }
            });

            overlay.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    close(options.mode === 'prompt' ? null : false);
                }
                if (event.key === 'Enter' && okBtn) {
                    okBtn.click();
                }
            });
        });
    }

    window.AppPopup = {
        toast(message, type, duration) {
            return showToast(message, type || 'info', duration || 2200);
        },
        alert(message) {
            return showToast(message, 'info', 2200);
        },
        confirm(message, title) {
            return openModal({
                mode: 'confirm',
                message,
                title: title || 'Please Confirm',
                type: 'warn'
            });
        },
        prompt(message, title, placeholder, defaultValue, required) {
            return openModal({
                mode: 'prompt',
                message,
                title: title || 'Input Required',
                placeholder: placeholder || '',
                defaultValue: defaultValue || '',
                required: !!required,
                type: 'warn'
            });
        }
    };

    const nativeAlert = window.alert.bind(window);
    window.alert = function (message) {
        if (!document.body || !window.AppPopup) {
            nativeAlert(message);
            return;
        }

        window.AppPopup.toast(message, 'info', 2200);
    };
})();
