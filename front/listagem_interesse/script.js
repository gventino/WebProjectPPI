const themeToggle = document.getElementById('theme-toggle');
const body = document.body;

themeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-theme');
});

document.addEventListener('DOMContentLoaded', () => {
    const announcementTitle = document.querySelector('main h2');
    const summaryContainer = document.querySelector('.announcement-summary .summary-card');
    const interestsContainer = document.querySelector('.interests-container');
    const viewAdButton = document.querySelector('.navigation-buttons .nav-btn');

    async function carregarDetalhesEInteresses() {
        const urlParams = new URLSearchParams(window.location.search);
        const anuncioId = urlParams.get('id');

        if (!anuncioId) {
            document.querySelector('main').innerHTML = '<h1>Erro</h1><p>ID do anúncio não fornecido na URL.</p>';
            return;
        }

        interestsContainer.innerHTML = '<h3>LISTA DE INTERESSES</h3><p>Carregando interesses...</p>';
        
        try {
            const url = '/../../back/anuncio/AnuncioController.php';
            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'interest',
                    anuncioId: anuncioId
                })
            };

            const response = await fetch(url, options);
            const data = await response.json();

            if (!data.anuncio || !Array.isArray(data.fotos) || !Array.isArray(data.interesses)) {
                document.querySelector('main').innerHTML = `<h1>Erro</h1><p>Não foi possível carregar os dados: ${data.message || 'Resposta inválida do servidor.'}</p>`;
                return;
            }
            
            const anuncio = data.anuncio;
            const fotos = data.fotos;
            const interesses = data.interesses;

            preencherResumoAnuncio(anuncio, fotos, interesses.length);
            renderizarInteresses(interesses);
            
            viewAdButton.onclick = () => location.href = `../detalhes_anuncio/index.html?id=${anuncio.id}`;

        } catch (error) {
            console.error('Falha na requisição:', error);
            document.querySelector('main').innerHTML = '<h1>Erro de Conexão</h1><p>Ocorreu um erro ao buscar os dados. Por favor, verifique sua conexão e tente novamente.</p>';
        }
    }

    function preencherResumoAnuncio(anuncio, fotos, totalInteresses) {
        const precoFormatado = parseFloat(anuncio.valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });

        const kmFormatada = parseInt(anuncio.quilometragem).toLocaleString('pt-BR');

        announcementTitle.textContent = `${anuncio.marca.toUpperCase()} ${anuncio.modelo.toUpperCase()} ${anuncio.ano}`;

        if (fotos && fotos.length > 0) {
            summaryContainer.querySelector('.summary-image img').src = `/back/uploads/${fotos[0]}`;
        }
        
        summaryContainer.querySelector('.summary-image img').alt = `Foto do ${anuncio.marca} ${anuncio.modelo}`;
        summaryContainer.querySelector('.summary-info h4').textContent = `${anuncio.marca} ${anuncio.modelo}`;
        summaryContainer.querySelector('.summary-info .summary-details').textContent = `${anuncio.cor} • ${kmFormatada} km • ${precoFormatado}`;
        summaryContainer.querySelector('.summary-info .interests-count').textContent = `Total de interesses: ${totalInteresses}`;
    }

    function renderizarInteresses(interesses) {
        interestsContainer.innerHTML = '<h3>LISTA DE INTERESSES</h3>';

        if (interesses.length === 0) {
          interestsContainer.innerHTML += '<p style="text-align: center; color: red;">Ainda não há nenhum interesse neste anúncio.</p>';
          return;
        }

        interesses.forEach((interesse, index) => {
            const dataFormatada = new Date(interesse.data_interesse).toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const card = document.createElement('div');
            card.className = 'interest-card';
            card.innerHTML = `
                <div class="interest-header">
                    <div class="interest-number">#${index + 1}</div>
                    <div class="interest-date">${dataFormatada}</div>
                </div>
                <div class="interest-content">
                    <div class="contact-info">
                        <div class="contact-item">
                            <span class="label">Nome:</span>
                            <span class="value">${interesse.nome}</span>
                        </div>
                        <div class="contact-item">
                            <span class="label">Telefone:</span>
                            <span class="value">${interesse.telefone}</span>
                        </div>
                    </div>
                    <div class="message-section">
                        <h4>Mensagem:</h4>
                        <div class="message-content">
                            <p>${interesse.mensagem}</p>
                        </div>
                    </div>
                </div>
                <div class="interest-actions">
                    <button class="action-btn contact-btn">Entrar em Contato</button>
                    <button class="action-btn mark-btn">Marcar como Contatado</button>
                </div>
            `;
            interestsContainer.appendChild(card);
        });
    }

    carregarDetalhesEInteresses();
});
