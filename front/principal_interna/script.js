const themeToggleButton = document.getElementById('theme-toggle');
const pageButton = document.getElementById('page-button');
const rootHtml = document.documentElement;

themeToggleButton.addEventListener('click', () => {
    rootHtml.classList.toggle('dark-theme');
});