const themeToggleButton = document.getElementById('theme-toggle');
const rootHtml = document.documentElement;

themeToggleButton.addEventListener('click', () => {
    rootHtml.classList.toggle('dark-theme');
});

const API_BASE_URL = `${window.location.protocol}//${window.location.host}/back/anuncio/AnuncioController.php`;

let currentVehicles = [];
let currentFilters = {
    marca: '',
    modelo: '',
    cidade: '',
    estado: '',
    search: ''
};

document.addEventListener('DOMContentLoaded', async () => {
    await updateHeaderBasedOnSession();
    await loadFilterOptions();
    await loadVehicles();
    setupEventListeners();
    setupResizeListener();
});

// Resize para ajustar o carousel
function setupResizeListener() {
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            if (currentVehicles.length > 0) {
                populateCarousel(currentVehicles);
            }
        }, 250);
    });
}

function setupEventListeners() {
    const buscaInput = document.getElementById('busca');
    const marcaSelect = document.getElementById('marca');
    const modeloSelect = document.getElementById('modelo');
    const cidadeSelect = document.getElementById('cidade');
    const estadoSelect = document.getElementById('estado');

    let searchTimeout;
    buscaInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.search = e.target.value;
            loadVehicles();
        }, 500);
    });

    marcaSelect.addEventListener('change', (e) => {
        currentFilters.marca = e.target.value;
        loadVehicles();
    });

    modeloSelect.addEventListener('change', (e) => {
        currentFilters.modelo = e.target.value;
        loadVehicles();
    });

    cidadeSelect.addEventListener('change', (e) => {
        currentFilters.cidade = e.target.value;
        loadVehicles();
    });

    estadoSelect.addEventListener('change', (e) => {
        currentFilters.estado = e.target.value;
        loadVehicles();
    });
}

async function loadFilterOptions() {
    const fields = ['marca', 'modelo', 'cidade', 'estado'];
    
    for (const field of fields) {
        try {
            const response = await fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'getFilterOptions',
                    field: field
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.obj) {
                    populateSelect(field, data.obj);
                }
            }
        } catch (error) {
            console.error(`Error loading ${field} options:`, error);
        }
    }
}

function populateSelect(field, options) {
    const select = document.getElementById(field);
    if (!select) return;

    const firstOption = select.querySelector('option[disabled]');
    select.innerHTML = '';
    if (firstOption) {
        select.appendChild(firstOption);
    }

    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option;
        optionElement.textContent = option;
        select.appendChild(optionElement);
    });
}

async function loadVehicles() {
    const loadingSpinner = document.getElementById('loadingSpinner');
    const noVehicles = document.getElementById('noVehicles');
    const carouselInner = document.getElementById('carouselInner');

    loadingSpinner.style.display = 'block';
    noVehicles.style.display = 'none';

    try {
        const response = await fetch(API_BASE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'listAll',
                ...currentFilters
            })
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success && data.obj) {
                currentVehicles = data.obj;
                populateCarousel(data.obj);
            } else {
                showNoVehicles();
            }
        } else {
            showNoVehicles();
        }
    } catch (error) {
        console.error('Error loading vehicles:', error);
        showNoVehicles();
    } finally {
        loadingSpinner.style.display = 'none';
    }
}

function populateCarousel(vehicles) {
    const carouselInner = document.getElementById('carouselInner');
    const noVehicles = document.getElementById('noVehicles');
    const carouselControls = document.querySelectorAll('.carousel-control-prev, .carousel-control-next');

    if (vehicles.length === 0) {
        showNoVehicles();
        return;
    }

    carouselInner.innerHTML = '';
    noVehicles.style.display = 'none';
    
    // hide nas setinhas enquanto carregamoss veiculos, prevenir layout shift
    carouselControls.forEach(control => {
        control.style.display = 'block';
    });


    const isMobile = window.innerWidth <= 768;
    const vehiclesPerSlide = isMobile ? 1 : 3;
    const slides = [];
    
    for (let i = 0; i < vehicles.length; i += vehiclesPerSlide) {
        slides.push(vehicles.slice(i, i + vehiclesPerSlide));
    }

    slides.forEach((slideVehicles, slideIndex) => {
        const carouselItem = document.createElement('div');
        carouselItem.className = `carousel-item ${slideIndex === 0 ? 'active' : ''}`;
        
        const row = document.createElement('div');
        row.className = 'row g-3 justify-content-center';

        slideVehicles.forEach(vehicle => {
            const col = document.createElement('div');
            col.className = isMobile ? 'col-12' : 'col-md-4';
            
            const card = createVehicleCard(vehicle);
            col.appendChild(card);
            row.appendChild(col);
        });

        carouselItem.appendChild(row);
        carouselInner.appendChild(carouselItem);
    });
}

function createVehicleCard(vehicle) {
    const card = document.createElement('div');
    card.className = 'card vehicle-card h-100';
    card.style.cursor = 'pointer';
    
    card.addEventListener('click', () => {
        window.location.href = `../detalhes_anuncio/index.html?id=${vehicle.id}`;
    });

    const imageSrc = vehicle.foto ? 
        `${window.location.protocol}//${window.location.host}/back/uploads/${vehicle.foto}` : 
        `${window.location.protocol}//${window.location.host}/front/images/VEICO_PLACEHOLDER.webp`;

    card.innerHTML = `
        <img src="${imageSrc}" class="card-img-top" alt="Foto do veículo" style="height: 200px; object-fit: cover;">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title">${vehicle.marca} - ${vehicle.modelo}</h5>
            <p class="card-text text-muted">${vehicle.ano}</p>
            <p class="card-text fw-bold text-primary">R$ ${parseFloat(vehicle.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</p>
            <p class="card-text"><small class="text-muted">${vehicle.cidade}, ${vehicle.estado}</small></p>
        </div>
    `;

    return card;
}

function showNoVehicles() {
    const noVehicles = document.getElementById('noVehicles');
    const carouselInner = document.getElementById('carouselInner');
    const carouselControls = document.querySelectorAll('.carousel-control-prev, .carousel-control-next');
    
    carouselInner.innerHTML = '';
    noVehicles.style.display = 'block';
    
    // hide nas setinhas enquanto nao carregamoss veiculos
    carouselControls.forEach(control => {
        control.style.display = 'none';
    });
}

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