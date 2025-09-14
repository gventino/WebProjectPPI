const themeToggleButton = document.getElementById('theme-toggle');
const rootHtml = document.documentElement;

themeToggleButton.addEventListener('click', () => {
  rootHtml.classList.toggle('dark-theme');
});

// register call
const formElement = document.querySelector('form');

async function register(event) {
  event.preventDefault();

  const formData = new FormData(formElement);
  const API_BASE_URL = `${window.location.protocol}//${window.location.host}/back/anunciante/AnuncianteController.php`;
  const url = API_BASE_URL;

  try {
    const formObject = Object.fromEntries(formData);
    formObject.action = "register";
    const json = JSON.stringify(formObject);
    const options = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: json
    };
    const response = await fetch(url, options);

    const data = await response.json();
    if (response.ok && data.success) {
      alert("Cadastrado com sucesso!");
    } else {
      const msg = data && data.message ? data.message : "Ocorreu algum erro no cadastro!";
      alert(msg);
      throw new Error(msg);
    }
  } catch (error) {
    console.error(`Error submiting form to register - ${error}`)
  }
}

formElement.addEventListener('submit', register);
