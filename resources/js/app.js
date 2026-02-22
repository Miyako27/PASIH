import './bootstrap';

const iconMarkup = {
    success: `
        <div class="pasih-alert-icon pasih-alert-icon-success">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M20 6L9 17l-5-5"></path>
            </svg>
        </div>
    `,
    question: `
        <div class="pasih-alert-icon pasih-alert-icon-question">
            <span>?</span>
        </div>
    `,
    danger: `
        <div class="pasih-alert-icon pasih-alert-icon-danger">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18"></path>
            </svg>
        </div>
    `,
};

function getSuccessTitle(message) {
    const text = (message || '').toLowerCase();

    if (text.includes('hapus')) {
        return 'Data Berhasil Dihapus !';
    }

    if (text.includes('diambil')) {
        return 'Penugasan Berhasil Diambil !';
    }

    if (
        text.includes('ditambah') ||
        text.includes('diperbarui') ||
        text.includes('dibuat') ||
        text.includes('disimpan')
    ) {
        return 'Data Berhasil Disimpan !';
    }

    return message || 'Data Berhasil Disimpan !';
}

function createAlertModal({
    icon = 'question',
    title = '',
    withActions = false,
    confirmText = 'Ya',
    cancelText = 'Tidak',
    autoCloseMs = null,
}) {
    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.className = 'pasih-alert-overlay';

        const card = document.createElement('div');
        card.className = 'pasih-alert-card';
        card.innerHTML = `
            ${iconMarkup[icon] || ''}
            <p class="pasih-alert-title"></p>
            ${
                withActions
                    ? `<div class="pasih-alert-actions">
                        <button type="button" class="pasih-alert-btn pasih-alert-btn-yes">${confirmText}</button>
                        <button type="button" class="pasih-alert-btn pasih-alert-btn-no">${cancelText}</button>
                    </div>`
                    : ''
            }
        `;
        const titleElement = card.querySelector('.pasih-alert-title');
        if (titleElement) {
            titleElement.textContent = title;
        }

        overlay.appendChild(card);
        document.body.appendChild(overlay);

        const close = (value) => {
            if (!overlay.isConnected) {
                return;
            }
            overlay.remove();
            resolve(value);
        };

        if (withActions) {
            const yesBtn = card.querySelector('.pasih-alert-btn-yes');
            const noBtn = card.querySelector('.pasih-alert-btn-no');

            yesBtn?.addEventListener('click', () => close(true));
            noBtn?.addEventListener('click', () => close(false));
            overlay.addEventListener('click', (event) => {
                if (event.target === overlay) {
                    close(false);
                }
            });
            return;
        }

        if (autoCloseMs) {
            window.setTimeout(() => close(true), autoCloseMs);
        }
    });
}

function bindConfirmForms() {
    const forms = document.querySelectorAll('form[data-confirm-type]');

    forms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const confirmType = form.dataset.confirmType;
            const title = form.dataset.confirmMessage || '';
            const icon = confirmType === 'delete' ? 'danger' : 'question';

            const accepted = await createAlertModal({
                icon,
                title,
                withActions: true,
            });

            if (accepted) {
                form.submit();
            }
        });
    });
}

function showFlashSuccess() {
    const flashMessage = document.body.dataset.flashSuccess;

    if (!flashMessage) {
        return;
    }

    createAlertModal({
        icon: 'success',
        title: getSuccessTitle(flashMessage),
        autoCloseMs: 1500,
    });
}

document.addEventListener('DOMContentLoaded', () => {
    bindConfirmForms();
    showFlashSuccess();
});
