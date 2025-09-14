const themeToggleButton = document.getElementById('theme-toggle');
const rootHtml = document.documentElement;

themeToggleButton.addEventListener('click', () => {
  rootHtml.classList.toggle('dark-theme');
});

// submissao de formulario
const formElement = document.querySelector('form');

async function register(event) {
  event.preventDefault();

  const formData = new FormData(formElement);
  const API_BASE_URL = `${window.location.protocol}//${window.location.host}/back/anuncio/AnuncioController.php`;
  const url = API_BASE_URL;

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
      alert("Cadastrado com sucesso!");
      window.location = "/front/listagem_anuncio"
    } else {
      alert("Ocorreu algum erro no cadastro: " + (data.message || ''));
      throw new Error(data.message);
    }
  } catch (error) {
    console.error(`Error submitting form to register - ${error}`);
  }
}

formElement.addEventListener('submit', register);
