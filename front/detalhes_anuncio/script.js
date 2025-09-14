const themeToggle = document.getElementById('theme-toggle');
const body = document.body;

document.addEventListener('DOMContentLoaded', gatekeeper);

themeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-theme');
});

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const anuncioId = urlParams.get('id');
    
    if (!anuncioId) {
        document.querySelector('main').innerHTML = '<h1>Erro: ID do anúncio não encontrado</h1>';
        return;
    }
    
    carregarDetalhesAnuncio(anuncioId);
});

async function carregarDetalhesAnuncio(anuncioId) {
    try {
        const url = '/../../back/anuncio/AnuncioController.php';
        const options = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'getById',
                anuncioId: parseInt(anuncioId)
            })
        };

        const response = await fetch(url, options);
        const data = await response.json();

        if (!data.success) {
            document.querySelector('main').innerHTML = `<h1>Erro: ${data.message}</h1>`;
            return;
        }

        const anuncio = data.obj;
        atualizarPaginaComDados(anuncio);
        
    } catch (error) {
        console.error('Erro ao carregar detalhes do anúncio:', error);
        document.querySelector('main').innerHTML = '<h1>Erro ao carregar detalhes do anúncio</h1>';
    }
}

function atualizarPaginaComDados(anuncio) {
    // TITLE SECTION
    document.querySelector('h2').textContent = `${anuncio.marca} ${anuncio.modelo} ${anuncio.ano}`;
    // PHOTO SECTION
    const mainPhoto = document.getElementById('main-photo');
    const thumbnailContainer = document.querySelector('.thumbnail-container');
    

    if (anuncio.fotos && anuncio.fotos.length > 0) {
        mainPhoto.src = `/back/uploads/${anuncio.fotos[0]}`;
        mainPhoto.alt = `Foto do veículo ${anuncio.marca} ${anuncio.modelo}`;
        
        thumbnailContainer.innerHTML = '';
        anuncio.fotos.forEach((foto, index) => {
            const img = document.createElement('img');
            img.src = `/back/uploads/${foto}`;
            img.alt = `Foto ${index + 1}`;
            img.className = `thumbnail ${index === 0 ? 'active' : ''}`;
            img.onclick = () => changeMainPhoto(img);
            thumbnailContainer.appendChild(img);
        });
    }
    
    // INFO SECTION
    const infoSections = document.querySelectorAll('.info-section');
    
    const basicInfo = infoSections[0];
    updateInfoItem(basicInfo, 'Marca:', anuncio.marca);
    updateInfoItem(basicInfo, 'Modelo:', anuncio.modelo);
    updateInfoItem(basicInfo, 'Ano:', anuncio.ano);
    updateInfoItem(basicInfo, 'Cor:', anuncio.cor);
    updateInfoItem(basicInfo, 'Quilometragem:', `${anuncio.quilometragem.toLocaleString()} km`);
    
    // DESCRIPTION SECTION
    document.querySelector('.description-content p').textContent = anuncio.descricao;
    
    // PRICE SECTION
    document.querySelector('.price-value').textContent = `R$ ${anuncio.valor.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
    
    // CONTACT SECTION
    const contactInfo = document.querySelector('.contact-info');
    updateContactItem(contactInfo, 'Vendedor:', anuncio.anuncianteNome || 'Não informado');
    updateContactItem(contactInfo, 'Telefone:', anuncio.anuncianteTelefone || 'Não informado');
    updateContactItem(contactInfo, 'Email:', anuncio.anuncianteEmail || 'Não informado');
    updateContactItem(contactInfo, 'Localização:', `${anuncio.cidade}, ${anuncio.estado}`);
    
    // INTEREST BUTTON
    const interestButton = document.querySelector('.nav-btn');
    interestButton.onclick = () => {
        location.href = `../registro_interesse/index.html?anuncioId=${anuncio.id}`;
    };
}

function updateInfoItem(section, label, value) {
    const items = section.querySelectorAll('.info-item');
    for (let item of items) {
        const labelSpan = item.querySelector('.label');
        if (labelSpan && labelSpan.textContent === label) {
            item.querySelector('.value').textContent = value;
            break;
        }
    }
}

function updateContactItem(section, label, value) {
    const items = section.querySelectorAll('.contact-item');
    for (let item of items) {
        const labelSpan = item.querySelector('.label');
        if (labelSpan && labelSpan.textContent === label) {
            const valueSpan = item.querySelector('.value');
            if (label === 'Telefone:' && value !== 'Não informado') {
                valueSpan.innerHTML = `<a href="tel:${value}">${value}</a>`;
            } else if (label === 'Email:' && value !== 'Não informado') {
                valueSpan.innerHTML = `<a href="mailto:${value}">${value}</a>`;
            } else {
                valueSpan.textContent = value;
            }
            break;
        }
    }
}

// CARROSSEL
function changeMainPhoto(thumbnail) {
    const mainPhoto = document.getElementById('main-photo');
    mainPhoto.src = thumbnail.src;
    
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    
    thumbnail.classList.add('active');
}