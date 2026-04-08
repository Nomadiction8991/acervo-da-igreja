import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const THEME_KEY = 'acervo-igreja-theme';
const THEME_COLORS = {
    light: '#f5efe6',
    dark: '#11171c',
};

function applyTheme(theme) {
    document.documentElement.dataset.theme = theme;
    localStorage.setItem(THEME_KEY, theme);

    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', THEME_COLORS[theme]);

    document.querySelectorAll('[data-theme-label]').forEach((label) => {
        label.textContent = theme === 'dark' ? 'Usar tema claro' : 'Usar tema escuro';
    });

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-label', theme === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro');
        button.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const initialTheme = localStorage.getItem(THEME_KEY)
        ?? document.documentElement.dataset.theme
        ?? 'light';

    applyTheme(initialTheme);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';

            applyTheme(nextTheme);
        });
    });
});
