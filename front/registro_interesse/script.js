const themeToggleButton = document.getElementById('theme-toggle');
const rootHtml = document.documentElement;

document.addEventListener('DOMContentLoaded', async () => {
    await updateHeaderBasedOnSession();
});

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    
    const anuncioId = urlParams.get('anuncioId');

    if (anuncioId) {
        const idAnuncioInput = document.getElementById('anuncioId');
        if (idAnuncioInput) {
            idAnuncioInput.value = anuncioId;
        }
    } else {
        console.error("ID do anúncio não encontrado na URL.");
        alert("Erro: Não foi possível identificar o anúncio. Por favor, volte e tente novamente.");
        document.querySelector('form button[type="submit"]').disabled = true;
    }
});

themeToggleButton.addEventListener('click', () => {
    rootHtml.classList.toggle('dark-theme');
});

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const anuncioId = urlParams.get('anuncioId');

    if (anuncioId) {
        const idAnuncioInput = document.querySelector('input[name="anuncioId"]');
        if (idAnuncioInput) {
            idAnuncioInput.value = anuncioId;
        }
    } else {
        console.error("ID do anúncio não encontrado na URL.");
        alert("Erro: Não foi possível identificar o anúncio. Por favor, volte e tente novamente.");
        const submitButton = document.querySelector('form button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }
    }
});


// Submissão de formulário com JSON
const formElement = document.querySelector('form');

async function register(event) {
    event.preventDefault();

    const form = event.target;
    const data = {
        nome: form.nome.value,
        telefone: form.telefone.value,
        mensagem: form.mensagem.value,
        anuncioId: form.anuncioId.value,
        action: 'register',
        dataHora: new Date().toISOString()
    };

    //const url = '/../../back/interesse/InteresseController.php';
    const API_BASE_URL = `${window.location.protocol}//${window.location.host}/back/interesse/InteresseController.php`;
    const url = API_BASE_URL;

    try {
        const options = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        };

        const response = await fetch(url, options);
        const result = await response.json();

        if (result.success) {
            alert("Interesse registrado com sucesso!");
        } else {
            alert("Ocorreu algum erro no cadastro: " + (result.message || ''));
            throw new Error(result.message);
        }
    } catch (error) {
        console.error(`Error submitting form to register - ${error}`);
        alert("Falha ao conectar com o servidor. Tente novamente mais tarde.");
    }
}

formElement.addEventListener('submit', register);

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
        <button class="nav-item" onclick="location.href='../listagem_anuncio/index.html'">Meus Anúncios</button>
        <button class="nav-item" onclick="location.href='../registro_anuncio/index.html'">Novo Anúncio</button>
    `;
    
    const navRight = document.querySelector('.nav-right');
    navRight.innerHTML = `
        <button class="nav-item" onclick="location.href='../principal_interna/index.html'">Portal Usuário</button>
        <button class="nav-item" onclick="location.href='../principal_externa/index.html'">Ache um Veíco</button>
    `;
    
    const mobileNav = document.querySelector('.mobile-nav');
    mobileNav.innerHTML = `
        <button class="nav-item" onclick="location.href='../principal_interna/index.html'">Portal Usuário</button>
        <button class="nav-item" onclick="location.href='../principal_externa/index.html'">Ache um Veíco</button>
        <button class="mobile-nav-item" onclick="location.href='../listagem_anuncio/index.html'">Meus Anúncios</button>
        <button class="mobile-nav-item" onclick="location.href='../registro_anuncio/index.html'">Novo Anúncio</button>
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
