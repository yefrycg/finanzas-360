import Toastify from 'toastify-js';

const ensureToastContainer = () => {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = `
            position: fixed;
            top: 80px;
            right: 16px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 380px;
        `;
        document.body.appendChild(container);
    }
    return container;
};

const createToast = (options) => {
    ensureToastContainer();

    const type = options.type || 'success';
    const message = options.message || options.text || '';

    const icons = {
        success: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`,
        error: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
        warning: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
        info: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`,
    };

    const colors = {
        success: { bg: '#022c22', border: '#10b981', text: '#d1fae5' },
        error: { bg: '#450a0a', border: '#ef4444', text: '#fef2f2' },
        warning: { bg: '#451a03', border: '#f59e0b', text: '#fef3c7' },
        info: { bg: '#1e3a8a', border: '#3b82f6', text: '#dbeafe' },
    };

    const color = colors[type] || colors.success;
    const icon = icons[type] || icons.success;

    const toastEl = document.createElement('div');
    toastEl.style.cssText = `
        background: ${color.bg};
        color: ${color.text};
        padding: 12px 16px;
        border-radius: 10px;
        border-left: 3px solid ${color.border};
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 12px;
        font-family: 'Instrument Sans', system-ui, -apple-system, sans-serif;
        font-size: 14px;
        line-height: 1.4;
        animation: slideIn 0.3s ease-out;
        width: 100%;
    `;

    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    `;
    document.head.appendChild(style);

    toastEl.innerHTML = `
        <span style="flex-shrink: 0; display: flex; color: ${color.border};">${icon}</span>
        <span style="flex: 1; color: ${color.text};">${message}</span>
    `;

    ensureToastContainer().appendChild(toastEl);

    setTimeout(() => {
        toastEl.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => toastEl.remove(), 300);
    }, 4000);

    const styleOut = document.createElement('style');
    styleOut.textContent = `
        @keyframes slideOut {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(20px); }
        }
    `;
    document.head.appendChild(styleOut);
};

window.showToast = (options) => {
    if (typeof options === 'string') {
        options = { message: options };
    }
    createToast(options);
};

window.showSuccess = (message) => createToast({ type: 'success', message });
window.showError = (message) => createToast({ type: 'error', message, duration: 5000 });
window.showWarning = (message) => createToast({ type: 'warning', message });
window.showInfo = (message) => createToast({ type: 'info', message });

window.showConfirm = (options) => {
    const {
        title = '¿Confirmar acción?',
        message = 'Esta acción no se puede deshacer.',
        onConfirm = () => {},
        onCancel = () => {},
        confirmText = 'Confirmar',
        cancelText = 'Cancelar',
    } = options;

    const overlay = document.createElement('div');
    overlay.id = 'confirm-overlay';
    overlay.style.cssText = `
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeIn 0.2s ease-out;
    `;

    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    overlay.innerHTML = `
        <div style="
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-width: 360px;
            width: 90%;
            animation: scaleIn 0.3s ease-out;
            font-family: 'Instrument Sans', system-ui, -apple-system, sans-serif;
        ">
            <style>
                @keyframes scaleIn {
                    from { opacity: 0; transform: scale(0.95); }
                    to { opacity: 1; transform: scale(1); }
                }
            </style>
            <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" style="flex-shrink: 0;">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <div style="flex: 1;">
                    <h3 style="font-weight: 600; font-size: 16px; margin: 0 0 4px 0;">${title}</h3>
                    <p style="font-size: 14px; opacity: 0.8; margin: 0;">${message}</p>
                </div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button id="toast-confirm-cancel" style="
                    padding: 8px 16px;
                    background: transparent;
                    border: 1px solid rgba(255, 255, 255, 0.3);
                    border-radius: 8px;
                    color: white;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: all 0.2s;
                ">${cancelText}</button>
                <button id="toast-confirm-ok" style="
                    padding: 8px 16px;
                    background: #ef4444;
                    border: none;
                    border-radius: 8px;
                    color: white;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                ">${confirmText}</button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    overlay.querySelector('#toast-confirm-cancel').addEventListener('click', () => {
        overlay.remove();
        onCancel();
    });

    overlay.querySelector('#toast-confirm-ok').addEventListener('click', () => {
        overlay.remove();
        onConfirm();
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            overlay.remove();
            onCancel();
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    // Check for hidden elements (from Laravel session)
    const successHidden = document.querySelector('#toast-success-message[data-message]');
    const errorHidden = document.querySelector('#toast-error-message[data-message]');

    if (successHidden && successHidden.dataset.message) {
        showSuccess(successHidden.dataset.message);
    }

    if (errorHidden && errorHidden.dataset.message) {
        showError(errorHidden.dataset.message);
    }
});