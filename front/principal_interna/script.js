const themeToggleButton = document.getElementById('theme-toggle');
const pageButton = document.getElementById('page-button');
const rootHtml = document.documentElement;

themeToggleButton.addEventListener('click', () => {
    rootHtml.classList.toggle('dark-theme');
});

(async function loadWelcome() {
    try {
        const response = await fetch('../../back/anunciante/AnuncianteController.php?action=checkSession', {
            method: 'GET'
        });
        const result = await response.json();
        const nameElem = document.getElementById('welcome-name');
        if (response.ok && result.success && result.obj && result.obj.name) {
            nameElem.textContent = `Olá, ${result.obj.name}!`;
        } else {
            nameElem.textContent = '';
        }
    } catch (_) {
        const nameElem = document.getElementById('welcome-name');
        nameElem.textContent = '';
    }
})();