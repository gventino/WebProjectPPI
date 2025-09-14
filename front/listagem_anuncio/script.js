const themeToggle = document.getElementById('theme-toggle');
const body = document.body;

themeToggle.addEventListener('click', () => {
  body.classList.toggle('dark-theme');
});

const API_BASE_URL = `${window.location.protocol}//${window.location.host}/back/anuncio/AnuncioController.php`;

document.addEventListener('DOMContentLoaded', () => {
  const announcementsContainer = document.querySelector('.announcements-container');

  async function carregarAnuncios() {
    announcementsContainer.innerHTML = '<p>Carregando seus anúncios...</p>';

    try {
      const url = API_BASE_URL;
      const options = {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          action: 'listUser'
        })
      };

      const response = await fetch(url, options);
      const data = await response.json();

      if (!data.success || !Array.isArray(data.obj)) {
        announcementsContainer.innerHTML = `<p>Erro ao carregar anúncios: ${data.message || 'Resposta inválida do servidor.'}</p>`;
        return;
      }

      if (data.obj.length === 0) {
        announcementsContainer.innerHTML = '<p>Você ainda não tem nenhum anúncio cadastrado.</p>';
        return;
      }

      announcementsContainer.innerHTML = '';
      data.obj.forEach(anuncio => {
        const card = document.createElement('div');
        card.className = 'announcement-card';
        card.innerHTML = `
                    <div class="announcement-image">
                        <img src="/back/uploads/${anuncio.foto}" alt="Foto do veículo ${anuncio.marca} ${anuncio.modelo}">
                    </div>
                    <div class="announcement-info">
                        <h3>${anuncio.marca} ${anuncio.modelo}</h3>
                        <p class="model">${anuncio.descricao}</p>
                        <p class="year">${anuncio.ano}</p>
                        <div class="announcement-actions">
                            <button class="action-btn view-btn" onclick="viewDetails(${anuncio.id})">Ver Detalhes</button>
                            <button class="action-btn interests-btn" onclick="viewInterests(${anuncio.id})">Ver Interesses</button>
                            <button class="action-btn delete-btn" onclick="excluirAnuncio(${anuncio.id})">Excluir</button>
                        </div>
                    </div>
                `;
        announcementsContainer.appendChild(card);
      });
    } catch (error) {
      console.error('Falha na requisição:', error);
      announcementsContainer.innerHTML = '<p>Ocorreu um erro de conexão. Por favor, verifique sua internet e tente novamente.</p>';
    }
  }

  carregarAnuncios();
});

async function excluirAnuncio(anuncioId) {
  if (confirm('Tem certeza que deseja excluir este anúncio?')) {
    const options = {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ anuncioId: anuncioId, action: 'delete' })
    };
    let response = await fetch(API_BASE_URL, options);
    console.log(await response.json());
    window.location.reload();
  }
}

function viewDetails(announcementId) {
  location.href = `../detalhes_anuncio/index.html?id=${announcementId}`;
}

function viewInterests(announcementId) {
  location.href = `../listagem_interesse/index.html?id=${announcementId}`;
}

