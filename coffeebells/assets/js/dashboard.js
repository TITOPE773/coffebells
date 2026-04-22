// ============================================================
// DASHBOARD.JS — Lógica del panel administrativo
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ── SIDEBAR TOGGLE (MÓVIL) ────────────────────────────
    const sidebar = document.getElementById('dashSidebar');
    const toggle  = document.getElementById('sidebarToggle');

    toggle?.addEventListener('click', function () {
        sidebar?.classList.toggle('open');
    });

    // Cerrar sidebar al hacer clic fuera en móvil
    document.addEventListener('click', function (e) {
        if (window.innerWidth < 992 &&
            sidebar && !sidebar.contains(e.target) &&
            toggle && !toggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });

    // ── AUTO-DISMISS FLASH MESSAGES ───────────────────────
    setTimeout(function () {
        document.querySelectorAll('.alert-auto-dismiss').forEach(el => {
            el.style.transition = 'opacity .5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 4000);

    // ── CONFIRM DELETES ───────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm || '¿Confirmas esta acción?')) {
                e.preventDefault();
            }
        });
    });

    // ── TOOLTIP BOOTSTRAP ─────────────────────────────────
    const tooltipEls = document.querySelectorAll('[title]');
    tooltipEls.forEach(el => new bootstrap.Tooltip(el, { trigger: 'hover' }));

    // ── GLOBAL TOAST ─────────────────────────────────────
    window.showDashToast = function (msg, type = 'success') {
        const toast = document.createElement('div');
        toast.innerHTML = `
            <div style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;
                        background:${type==='success'?'#3e2723':'#dc3545'};color:#fff;
                        padding:.85rem 1.5rem;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.2);
                        font-size:.9rem;font-weight:600;max-width:320px;
                        animation:fadeInUp .3s ease both;">
                ${msg}
            </div>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    };

    // ── HIGHLIGHT SIDEBAR LINK ACTIVO ─────────────────────
    const currentFile = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-link').forEach(link => {
        const href = link.getAttribute('href') || '';
        if (href.startsWith(currentFile) || href === currentFile) {
            link.classList.add('active');
        }
    });

    // ── CONFIRMAR ANTES DE SALIR CON FORMULARIO SUCIO ─────
    let formDirty = false;
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('input', () => { formDirty = true; });
        form.addEventListener('submit', () => { formDirty = false; });
    });
    window.addEventListener('beforeunload', function (e) {
        if (formDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});