<style>
    .delete-confirm-overlay { position: fixed; inset: 0; z-index: 130; display: flex; align-items: center; justify-content: center; padding: 24px; background: rgba(15, 23, 42, .32); backdrop-filter: blur(2px); opacity: 0; visibility: hidden; transition: opacity .2s ease, visibility .2s ease; }
    .delete-confirm-overlay.is-open { opacity: 1; visibility: visible; }
    .delete-confirm-card { width: min(100%, 360px); padding: 18px 18px 16px; border: 1px solid rgba(148, 163, 184, .35); border-radius: 14px; background: rgba(255, 255, 255, .98); box-shadow: 0 24px 60px rgba(15, 39, 71, .18); }
    .delete-confirm-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
    .delete-confirm-icon { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; font-size: 1.1rem; }
    .delete-confirm-title { margin: 0; font-size: 1.05rem; font-weight: 800; }
    .delete-confirm-close { border: 0; background: transparent; color: #64748b; font-size: 1.2rem; cursor: pointer; }
    .delete-confirm-message { margin: 0 0 16px; color: var(--text-soft); font-size: .83rem; line-height: 1.5; }
    .delete-confirm-actions { display: flex; justify-content: flex-end; gap: 8px; }
    .delete-confirm-cancel, .delete-confirm-ok { padding: 9px 14px; border: 0; border-radius: 8px; font: inherit; font-size: .8rem; font-weight: 700; cursor: pointer; }
    .delete-confirm-cancel { background: #eef2f7; color: #334155; }
    .delete-confirm-ok { background: #dc2626; color: #ffffff; box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25); transition: background-color .15s ease, transform .15s ease; }
    .delete-confirm-ok:hover { background: #b91c1c; transform: translateY(-1px); }
</style>
<div class="delete-confirm-overlay" id="delete-confirm-modal" aria-hidden="true">
    <div class="delete-confirm-card" role="dialog" aria-modal="true" aria-labelledby="delete-confirm-title">
        <div class="delete-confirm-header">
            <div class="delete-confirm-icon"><i class="bi bi-trash3-fill"></i></div>
            <h2 id="delete-confirm-title" class="delete-confirm-title">Hapus data?</h2>
            <button type="button" class="delete-confirm-close" aria-label="Tutup" data-dismiss-delete-confirm>&times;</button>
        </div>
        <p class="delete-confirm-message">Data yang dihapus tidak dapat dikembalikan. Apakah Anda yakin ingin melanjutkan?</p>
        <div class="delete-confirm-actions">
            <button type="button" class="delete-confirm-cancel" data-dismiss-delete-confirm>Batal</button>
            <button type="button" class="delete-confirm-ok">Hapus</button>
        </div>
    </div>
</div>
<script>
    (() => {
        const modal = document.getElementById('delete-confirm-modal');
        if (!modal) return;

        const contentScroller = document.querySelector('.content');
        let pendingForm = null;
        let confirmedForm = null;
        let returnFocusTo = null;
        let originalBodyOverflow = '';
        let originalContentOverflow = '';

        document.body.append(modal);

        const setOpen = (isOpen) => {
            modal.classList.toggle('is-open', isOpen);
            modal.setAttribute('aria-hidden', String(!isOpen));
            if (isOpen) {
                originalBodyOverflow = document.body.style.overflow;
                originalContentOverflow = contentScroller?.style.overflow ?? '';
                document.body.style.overflow = 'hidden';
                if (contentScroller) contentScroller.style.overflow = 'hidden';
                modal.querySelector('.delete-confirm-cancel')?.focus();
                return;
            }

            document.body.style.overflow = originalBodyOverflow;
            if (contentScroller) contentScroller.style.overflow = originalContentOverflow;
            returnFocusTo?.focus();
        };

        const openForForm = (form, trigger) => {
            pendingForm = form;
            returnFocusTo = trigger;
            modal.querySelector('.delete-confirm-title').textContent = form.dataset.confirmTitle || 'Hapus data?';
            modal.querySelector('.delete-confirm-message').textContent = form.dataset.confirmMessage || 'Data yang dihapus tidak dapat dikembalikan. Apakah Anda yakin ingin melanjutkan?';
            setOpen(true);
        };

        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('.delete-form .delete-btn, .sector-delete-form .node-delete');
            if (!trigger) return;
            event.preventDefault();
            openForForm(trigger.closest('form'), trigger);
        });

        document.addEventListener('submit', (event) => {
            const form = event.target.closest('.delete-form, .sector-delete-form');
            if (!form) return;
            if (form === confirmedForm) {
                confirmedForm = null;
                return;
            }
            event.preventDefault();
            openForForm(form, form.querySelector('.delete-btn, .node-delete'));
        }, true);

        modal.querySelectorAll('[data-dismiss-delete-confirm]').forEach((button) => {
            button.addEventListener('click', () => {
                setOpen(false);
                pendingForm = null;
            });
        });

        modal.querySelector('.delete-confirm-ok')?.addEventListener('click', () => {
            const form = pendingForm;
            pendingForm = null;
            setOpen(false);
            if (form) {
                confirmedForm = form;
                form.requestSubmit();
            }
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                setOpen(false);
                pendingForm = null;
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                setOpen(false);
                pendingForm = null;
            }
        });
    })();
</script>