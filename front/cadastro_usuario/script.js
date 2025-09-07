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
  const url = '/../../back/anunciante/AnuncianteController.php';

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

    const data = await response.text();
    if (data === "true") {
      alert("Cadastrado com sucesso!");
    } else {
      alert("Ocorreu algum erro no cadastro!");
      throw new Error(data);
    }
  } catch (error) {
    console.error(`Error submiting form to register - ${error}`)
  }
}

formElement.addEventListener('submit', register);
