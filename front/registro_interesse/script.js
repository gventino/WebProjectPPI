const themeToggleButton = document.getElementById('theme-toggle');
const rootHtml = document.documentElement;

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    
    const anuncioId = urlParams.get('idAnuncio');

    if (anuncioId) {
        const idAnuncioInput = document.getElementById('idAnuncio');
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

// submissao de formulario
const formElement = document.querySelector('form');

async function register(event) {
  event.preventDefault();

  const formData = new FormData(formElement);
  const url = 'http://localhost:8080/back/interesse/InteresseController.php';

  formData.append('action', 'register');
  formData.append('dataHora', new Date().toISOString());

  try {
    const options = {
      method: 'POST',
      body: formData
    };

    const response = await fetch(url, options);

    const data = await response.json();
    if (data.success) {
      alert("Interesse registrado com sucesso!");
    } else {
      alert("Ocorreu algum erro no cadastro: " + (data.message || ''));
      throw new Error(data.message);
    }
  } catch (error) {
    console.error(`Error submitting form to register - ${error}`);
  }
}

formElement.addEventListener('submit', register);
