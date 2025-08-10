-- Cria o banco de dados se ele não existir, especificando o conjunto de caracteres para suportar acentos
CREATE DATABASE IF NOT EXISTS veico CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usa o banco de dados recém-criado
USE veico;

-- Tabela de Anunciantes
-- Deve ser criada primeiro, pois 'Anuncio' depende dela.
CREATE TABLE IF NOT EXISTS Anunciante (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE, -- Formato XXX.XXX.XXX-XX
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    telefone VARCHAR(20)
);

-- Tabela de Anúncios
-- Depende de 'Anunciante'.
CREATE TABLE IF NOT EXISTS Anuncio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    ano INT NOT NULL,
    cor VARCHAR(50),
    quilometragem INT,
    descricao TEXT,
    valor DECIMAL(10, 2) NOT NULL, -- Ex: 99999999.99
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(100),
    cidade VARCHAR(100),
    id_anunciante INT NOT NULL,
    CONSTRAINT fk_anuncio_anunciante
        FOREIGN KEY (id_anunciante) REFERENCES Anunciante(id)
        ON DELETE CASCADE -- Se o anunciante for deletado, seus anúncios também serão.
);

-- Tabela de Fotos do Anúncio
-- Depende de 'Anuncio'.
CREATE TABLE IF NOT EXISTS Foto (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdAnuncio INT NOT NULL,
    NomeArqFoto VARCHAR(255) NOT NULL,
    -- Uma chave primária composta também seria uma opção, mas um ID autoincremento é mais simples.
    -- PRIMARY KEY (IdAnuncio, NomeArqFoto),
    CONSTRAINT fk_foto_anuncio
        FOREIGN KEY (id_anuncio) REFERENCES Anuncio(id)
        ON DELETE CASCADE -- Se o anúncio for deletado, suas fotos também serão.
);

-- Tabela de Pessoas Interessadas no Anúncio
-- Depende de 'Anuncio'.
CREATE TABLE IF NOT EXISTS Interesse (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    mensagem TEXT NOT NULL,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_anuncio INT NOT NULL,
    CONSTRAINT fk_interesse_anuncio
        FOREIGN KEY (id_anuncio) REFERENCES Anuncio(id)
        ON DELETE CASCADE -- Se o anúncio for deletado, o registro de interesse também será.
);
