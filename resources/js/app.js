import './bootstrap';

import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';

// Register all Chart.js components once globally
Chart.register(...registerables);
window.Chart = Chart;

// Default chart styling: match design system
Chart.defaults.font.family = "'DM Sans', 'Figtree', system-ui, sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.color = '#78756e';
Chart.defaults.borderColor = '#eae7e0';

// Alpine - register components BEFORE Alpine.start()
window.Alpine = Alpine;

/**
 * Notification bell dropdown.
 * Polls /notifications/recent and surfaces the latest 8 audit-log entries
 * relevant to the current user.
 */
window.notifBell = function () {
    return {
        open: false,
        loading: false,
        items: [],
        loadedAt: 0,

        init() {
            // Initial fetch shortly after mount so the bell dot reflects state.
            setTimeout(() => this.fetchItems(), 800);
            // Re-fetch every 90s while the page is open.
            this._timer = setInterval(() => this.fetchItems(), 90_000);
        },

        async fetchItems() {
            this.loading = true;
            try {
                const r = await fetch('/notifications/recent', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (r.ok) {
                    const data = await r.json();
                    this.items = data.items || [];
                    this.loadedAt = Date.now();
                }
            } catch (_) { /* network errors are silent */ }
            this.loading = false;
        },

        toggle() {
            this.open = !this.open;
            if (this.open && Date.now() - this.loadedAt > 30_000) {
                this.fetchItems();
            }
        },
    };
};

Alpine.start();

/**
 * Toast helper — used by the auth shell layout.
 * Usage: window.showNotif('Saqlandi','success')
 */
window.showNotif = function (msg, type = 'info') {
    const stack = document.getElementById('notif-stack');
    if (!stack) return;
    const t = document.createElement('div');
    t.className = 'notif-toast ' + (type || '');
    const icon = ({ success: '✓', error: '!', warn: '!' }[type] || 'i');
    t.innerHTML = `<span style="font-weight:700;width:18px;text-align:center;">${icon}</span><span>${msg}</span>`;
    stack.appendChild(t);
    setTimeout(() => {
        t.style.opacity = '0';
        t.style.transform = 'translateX(16px)';
        t.style.transition = 'all .3s ease';
        setTimeout(() => t.remove(), 320);
    }, 3500);
};

// Mobile sidebar auto-close on nav click
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sidebar .nav-item').forEach((i) => {
        i.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                document.querySelector('.sidebar')?.classList.remove('open');
                document.querySelector('.sidebar-overlay')?.classList.remove('open');
            }
        });
    });

    // Topbar search: Ctrl/Cmd+K to focus
    const search = document.getElementById('topbar-search-input');
    if (search) {
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                search.focus();
            }
        });
    }
});
