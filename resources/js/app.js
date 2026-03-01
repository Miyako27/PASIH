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

let pdfJsLoaderPromise = null;

const pdfJsBundles = [
    {
        // Legacy UMD build is the most compatible on mobile browsers.
        script: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js',
        worker: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js',
    },
    {
        script: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js',
        worker: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js',
    },
    {
        script: '/js/pdfjs/pdf.min.js',
        worker: '/js/pdfjs/pdf.worker.min.js',
    },
    {
        script: '/vendor/pdfjs/pdf.min.js',
        worker: '/vendor/pdfjs/pdf.worker.min.js',
    },
];

function loadScript(src) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error(`Failed to load script: ${src}`));
        document.head.appendChild(script);
    });
}

async function ensurePdfJs() {
    if (window.pdfjsLib) {
        return window.pdfjsLib;
    }

    if (!pdfJsLoaderPromise) {
        pdfJsLoaderPromise = (async () => {
            let loaded = false;
            let chosenWorker = '';
            for (const bundle of pdfJsBundles) {
                try {
                    await loadScript(bundle.script);
                    if (window.pdfjsLib && typeof window.pdfjsLib.getDocument === 'function') {
                        loaded = true;
                        chosenWorker = bundle.worker;
                        break;
                    }
                } catch (error) {
                    // Try the next CDN source.
                }
            }

            if (!loaded || !window.pdfjsLib) {
                throw new Error('PDF.js could not be loaded from available sources.');
            }

            window.pdfjsLib.GlobalWorkerOptions.workerSrc = chosenWorker;
            return window.pdfjsLib;
        })();
    }

    return pdfJsLoaderPromise;
}

async function initInlinePdfViewer(root) {
    const url = root.dataset.pdfUrl;
    const displayName = root.dataset.pdfName || 'Dokumen PDF';
    const pagesContainer = root.querySelector('[data-pdf-pages]');
    const scrollContainer = root.querySelector('[data-pdf-scroll]');
    const metaElement = root.querySelector('[data-pdf-meta]');

    if (!url || !pagesContainer || !scrollContainer || !metaElement) {
        return;
    }

    const setStatus = (text) => {
        metaElement.textContent = text;
    };

    const setError = (text) => {
        setStatus('Preview gagal dimuat');
        pagesContainer.innerHTML = `<div class="text-xs text-rose-600">${text}</div>`;
    };

    const setIdleState = () => {
        setStatus('Preview belum dimuat');
        pagesContainer.innerHTML = '<div class="text-xs text-slate-500">Menyiapkan preview PDF...</div>';
    };

    let mode = 'idle';
    let loading = false;
    let pdf = null;
    let firstPageWidth = 1;
    let fitScale = 1;
    let zoom = 1;
    let renderToken = 0;

    const loadButton = root.querySelector('[data-pdf-action="load"]');
    const controlButtons = Array.from(root.querySelectorAll('[data-pdf-control]'));

    const setControlEnabled = (enabled) => {
        controlButtons.forEach((button) => {
            button.disabled = !enabled;
            button.classList.toggle('opacity-50', !enabled);
            button.classList.toggle('cursor-not-allowed', !enabled);
        });
    };

    const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

    const calculateFitScale = () => {
        const availableWidth = Math.max(scrollContainer.clientWidth - 24, 320);
        return clamp(availableWidth / firstPageWidth, 0.4, 2.2);
    };

    const renderAllPages = async () => {
        if (mode !== 'pdfjs' || !pdf) {
            return;
        }

        const token = ++renderToken;
        const scale = fitScale * zoom;
        pagesContainer.innerHTML = '';

        for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber += 1) {
            const page = await pdf.getPage(pageNumber);
            const viewport = page.getViewport({ scale });
            const canvas = document.createElement('canvas');
            canvas.className = 'block rounded bg-white shadow-sm ring-1 ring-slate-200';
            canvas.width = Math.floor(viewport.width);
            canvas.height = Math.floor(viewport.height);
            pagesContainer.appendChild(canvas);

            await page.render({
                canvasContext: canvas.getContext('2d'),
                viewport,
            }).promise;

            if (token !== renderToken) {
                return;
            }
        }
    };

    const fetchPdfBytes = async () => {
        const response = await fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error(`Preview request failed with status ${response.status}`);
        }
        const contentType = (response.headers.get('content-type') || '').toLowerCase();

        if (contentType.includes('application/json')) {
            const payload = await response.json();
            const base64Data = payload?.data || '';
            if (!base64Data) {
                throw new Error('Preview JSON payload is empty.');
            }

            const binary = atob(base64Data);
            const bytes = new Uint8Array(binary.length);
            for (let i = 0; i < binary.length; i += 1) {
                bytes[i] = binary.charCodeAt(i);
            }
            return bytes.buffer;
        }

        return response.arrayBuffer();
    };

    const loadPreview = async () => {
        if (loading) {
            return;
        }

        mode = 'idle';
        pdf = null;
        renderToken += 1;

        loading = true;
        if (loadButton) {
            loadButton.disabled = true;
            loadButton.textContent = 'Memuat...';
        }

        try {
            const pdfjsLib = await ensurePdfJs();

            setStatus('Memuat PDF...');
            const pdfBytes = await fetchPdfBytes();
            const loadingTask = pdfjsLib.getDocument({ data: pdfBytes });
            pdf = await loadingTask.promise;
            mode = 'pdfjs';

            const firstPage = await pdf.getPage(1);
            firstPageWidth = firstPage.getViewport({ scale: 1 }).width || 1;
            fitScale = calculateFitScale();
            zoom = 1;
            renderToken = 0;

            setStatus(`${displayName} | ${pdf.numPages} halaman`);
            setControlEnabled(true);
            await renderAllPages();
        } catch (error) {
            setControlEnabled(false);
            setError('File PDF tidak dapat ditampilkan. Silakan tekan Muat Ulang.');
        } finally {
            loading = false;
            if (loadButton) {
                loadButton.disabled = false;
                loadButton.textContent = 'Muat Ulang';
            }
        }
    };

    const handleAction = async (action) => {
        if (action === 'load') {
            await loadPreview();
            return;
        }

        if (mode !== 'pdfjs') {
            return;
        }

        if (action === 'zoom-in') {
            zoom = clamp(zoom * 1.15, 0.5, 3);
            await renderAllPages();
            return;
        }

        if (action === 'zoom-out') {
            zoom = clamp(zoom / 1.15, 0.5, 3);
            await renderAllPages();
            return;
        }

        if (action === 'fit') {
            fitScale = calculateFitScale();
            zoom = 1;
            await renderAllPages();
        }
    };

    root.querySelectorAll('[data-pdf-action]').forEach((button) => {
        button.addEventListener('click', async () => {
            const action = button.dataset.pdfAction;
            await handleAction(action);
        });
    });

    const resizeObserver = new ResizeObserver(async () => {
        if (mode !== 'pdfjs') {
            return;
        }
        fitScale = calculateFitScale();
        await renderAllPages();
    });
    resizeObserver.observe(scrollContainer);

    setControlEnabled(false);
    setIdleState();
    void loadPreview();

}

function initInlinePdfViewers() {
    const viewers = Array.from(document.querySelectorAll('[data-pdf-viewer]'));
    if (!viewers.length) {
        return;
    }

    viewers.forEach((viewer) => {
        initInlinePdfViewer(viewer);
    });
}

function initSidebarDrawer() {
    const drawer = document.querySelector('[data-sidebar-drawer]');
    const overlay = document.querySelector('[data-sidebar-overlay]');
    const toggles = Array.from(document.querySelectorAll('[data-sidebar-toggle]'));
    const closeButton = document.querySelector('[data-sidebar-close]');
    const sidebarLinks = Array.from(document.querySelectorAll('[data-sidebar-link]'));

    if (!drawer || !overlay || !toggles.length) {
        return;
    }

    const isDesktop = () => window.matchMedia('(min-width: 768px)').matches;

    const closeDrawer = () => {
        drawer.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        toggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'false'));
    };

    const openDrawer = () => {
        if (isDesktop()) {
            return;
        }

        drawer.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        toggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'true'));
    };

    toggles.forEach((toggle) => {
        toggle.setAttribute('aria-expanded', 'false');
        toggle.addEventListener('click', () => {
            const isClosed = drawer.classList.contains('-translate-x-full');
            if (isClosed) {
                openDrawer();
                return;
            }
            closeDrawer();
        });
    });

    overlay.addEventListener('click', closeDrawer);
    closeButton?.addEventListener('click', closeDrawer);
    sidebarLinks.forEach((link) => link.addEventListener('click', closeDrawer));

    window.addEventListener('resize', () => {
        if (isDesktop()) {
            closeDrawer();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeDrawer();
        }
    });
}

function initResponsiveTables() {
    const tables = Array.from(document.querySelectorAll('main table'));
    if (!tables.length) {
        return;
    }

    tables.forEach((table) => {
        const parent = table.parentElement;
        if (!parent) {
            return;
        }

        if (parent.classList.contains('pasih-table-scroll') || parent.dataset.tableScroll === '1') {
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'pasih-table-scroll w-full overflow-x-auto';
        wrapper.dataset.tableScroll = '1';

        table.classList.add('w-full', 'min-w-[640px]');
        parent.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    bindConfirmForms();
    showFlashSuccess();
    initInlinePdfViewers();
    initSidebarDrawer();
    initResponsiveTables();
});
