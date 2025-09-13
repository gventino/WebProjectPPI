const themeToggleButton = document.getElementById('theme-toggle');
const rootHtml = document.documentElement;

themeToggleButton.addEventListener('click', () => {
    rootHtml.classList.toggle('dark-theme');
});

async function updateHeaderBasedOnSession() {
    const sessionResult = await checkSession();
    
    if (sessionResult.success && sessionResult.user) {
        updateHeaderForLoggedInUser(sessionResult.user);
    } else {
        updateHeaderForLoggedOutUser();
    }
}

function updateHeaderForLoggedInUser() {
    const navLeft = document.querySelector('.nav-left');
    navLeft.innerHTML = `
        <button class="nav-item" onclick="location.href='../principal_interna/index.html'">Início</button>
        <button class="nav-item" onclick="location.href='../listagem_anuncio/index.html'">Meus Anúncios</button>
    `;
    
    const navRight = document.querySelector('.nav-right');
    navRight.innerHTML = `
        <button class="nav-item" onclick="location.href='../registro_anuncio/index.html'">Novo Anúncio</button>
        <button class="nav-item" onclick="location.href='../listagem_interesse/index.html'">Interesses</button>
    `;
    
    const mobileNav = document.querySelector('.mobile-nav');
    mobileNav.innerHTML = `
        <button class="nav-item" onclick="location.href='../principal_interna/index.html'">Início</button>
        <button class="nav-item" onclick="location.href='../listagem_anuncio/index.html'">Meus Anúncios</button>
        <button class="nav-item" onclick="location.href='../registro_anuncio/index.html'">Novo Anúncio</button>
        <button class="nav-item" onclick="location.href='../listagem_interesse/index.html'">Interesses</button>
    `;
    
    const logo = document.getElementById('logo');
    logo.onclick = () => location.href = '../principal_interna/index.html';
}

function updateHeaderForLoggedOutUser() {
    const navLeft = document.querySelector('.nav-left');
    navLeft.innerHTML = `
        <button class="nav-item" onclick="location.href='../cadastro_usuario/index.html'">Cadastre-se</button>
    `;
    
    const navRight = document.querySelector('.nav-right');
    navRight.innerHTML = `
        <button class="nav-item" onclick="location.href='../login/index.html'">Faça login</button>
    `;
    
    const mobileNav = document.querySelector('.mobile-nav');
    mobileNav.innerHTML = `
        <button class="nav-item" onclick="location.href='../cadastro_usuario/index.html'">Cadastre-se</button>
        <button class="nav-item" onclick="location.href='../principal_externa/index.html'">Ache seu veíco</button>
        <button class="nav-item" onclick="location.href='../login/index.html'">Faça login</button>
    `;
    
    const logo = document.getElementById('logo');
    logo.onclick = () => location.href = '../principal_externa/index.html';
}

document.addEventListener('DOMContentLoaded', updateHeaderBasedOnSession);