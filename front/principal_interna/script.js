const themeToggleButton = document.getElementById('theme-toggle');
const pageButton = document.getElementById('page-button');
const rootHtml = document.documentElement;

document.addEventListener('DOMContentLoaded', gatekeeper);

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

document.querySelector('#Logoff').addEventListener('click', async function() {
    
    try {
        const API_BASE_URL = `${window.location.protocol}//${window.location.host}/back/anunciante/AnuncianteController.php`;
        const response = await fetch(API_BASE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'logout',
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Logout feito!');
            window.location.href = '../principal_externa/index.html';
        } else {
            alert('Erro: ' + result.message);
        }
    } catch (error) {
        alert('Erro ao fazer logout');
    }
});
