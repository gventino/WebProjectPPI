const themeToggleButton = document.getElementById('theme-toggle');
const rootHtml = document.documentElement;

document.addEventListener('DOMContentLoaded', innkeeper);

themeToggleButton.addEventListener('click', () => {
    rootHtml.classList.toggle('dark-theme');
});

document.querySelector('button[type="button"]').addEventListener('click', async function() {
    const email = document.querySelector('input[name="email"]').value;
    const password = document.querySelector('input[name="password"]').value;
    
    if (!email || !password) {
        alert('Preencha email e senha');
        return;
    }
    
    try {
        const response = await fetch('../../back/anunciante/AnuncianteController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'login',
                email: email,
                senha: password
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Login feito!');
            window.location.href = '../principal_interna/index.html';
        } else {
            alert('Erro: ' + result.message);
        }
    } catch (error) {
        alert('Erro ao fazer login');
    }
});
