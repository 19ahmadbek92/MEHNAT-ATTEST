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

// Alpine
window.Alpine = Alpine;
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
