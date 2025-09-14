const themeToggleButton = document.getElementById('theme-toggle');
const rootHtml = document.documentElement;

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

