-- 🔹 Banco de dados Biblioteca
CREATE DATABASE IF NOT EXISTS biblioteca;
USE biblioteca;

-- 🔹 Tabela de usuários (para login)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- senha criptografada com password_hash
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 🔹 Tabela de livros (CRUD principal)
CREATE TABLE IF NOT EXISTS livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(150) NOT NULL,
    ano_publicacao YEAR NOT NULL,
    editora VARCHAR(100),
    disponivel BOOLEAN DEFAULT TRUE,    -- disponível para empréstimo
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- Índice para buscas rápidas por título e autor
