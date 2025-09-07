const themeToggle = document.getElementById('theme-toggle');
const body = document.body;

themeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-theme');
});

document.addEventListener('DOMContentLoaded', () => {
    const announcementsContainer = document.querySelector('.announcements-container');

    async function carregarAnuncios() {
        announcementsContainer.innerHTML = '<p>Carregando seus anúncios...</p>';

        try {
            const url = 'http://localhost:8080/back/anuncio/AnuncioController.php';
            const options = {
              method: 'POST',
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
            data.obj.forEach(pair => {
                const card = document.createElement('div');
                card.className = 'announcement-card';
                card.innerHTML = `
                    <div class="announcement-image">
                        <img src="/back/uploads/${pair.foto}" alt="Foto do veículo ${pair.anuncio.marca} ${pair.anuncio.modelo}">
                    </div>
                    <div class="announcement-info">
                        <h3>${pair.anuncio.marca} ${pair.anuncio.modelo}</h3>
                        <p class="model">${pair.anuncio.descricao}</p>
                        <p class="year">${pair.anuncio.ano}</p>
                        <div class="announcement-actions">
                            <button class="action-btn view-btn" onclick="viewDetails(${pair.anuncio.id})">Ver Detalhes</button>
                            <button class="action-btn interests-btn" onclick="viewInterests(${pair.anuncio.id})">Ver Interesses</button>
                            <button class="action-btn delete-btn" onclick="excluirAnuncio(${pair.anuncio.id})">Excluir</button>
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

function excluirAnuncio(announcementId) {
    if (confirm('Tem certeza que deseja excluir este anúncio?')) {
        console.log(`Excluindo anúncio com ID: ${announcementId}`);
        // Exemplo: fetch(`api/excluir-anuncio.php?id=${announcementId}`, { method: 'DELETE' })
        // .then(...)
        // Após excluir, você pode recarregar a lista ou remover o card da tela.
    }
}

function viewDetails(announcementId) {
    // depois vamos passar o id do anuncio para a pagina de detalhes e interesses
    // location.href = `../detalhes_anuncio/index.html?id=${announcementId}`;
    location.href = `../detalhes_anuncio/index.html`;
}

function viewInterests(announcementId) {
    location.href = `../listagem_interesse/index.html`;
}

