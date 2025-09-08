const themeToggleButton = document.getElementById('theme-toggle');
const pageButton = document.getElementById('page-button');
const rootHtml = document.documentElement;

themeToggleButton.addEventListener('click', () => {
    rootHtml.classList.toggle('dark-theme');
});

(async function loadWelcome() {
    const sessionResult = await checkSession();
    const nameElem = document.getElementById('welcome-name');
    
    if (sessionResult.success && sessionResult.user && sessionResult.user.name) {
        nameElem.textContent = `Olá, ${sessionResult.user.name}!`;
    } else {
        nameElem.textContent = '';
    }
})();