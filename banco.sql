-- Criação do Banco de Dados
CREATE DATABASE IF NOT EXISTS loja_geek;
USE loja_geek;

-- Tabela de Usuários (obrigatória para Login e Sessão)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'cliente') DEFAULT 'cliente'
);

-- Tabela 1 do Negócio: Categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

INSERT INTO categorias (nome) VALUES ('Mangás'), ('Figures'), ('Cartas Pokémon')
ON DUPLICATE KEY UPDATE nome=VALUES(nome);

-- Tabela 2 do Negócio: Produtos (com campos específicos geek)
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    categoria_id INT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    quantidade_estoque INT NOT NULL DEFAULT 0,
    editora_fabricante VARCHAR(100),
    especificacao VARCHAR(150),
    condicao ENUM('Novo/Lacrado', 'Seminovo', 'Raro/Colecionável') DEFAULT 'Novo/Lacrado',
    descricao TEXT,
    imagem_url VARCHAR(255),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

-- Tabela 3 do Negócio: Vendas
CREATE TABLE IF NOT EXISTS vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_venda DATETIME DEFAULT CURRENT_TIMESTAMP,
    valor_total DECIMAL(10,2) NOT NULL,
    forma_pagamento VARCHAR(50) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela 4 do Negócio: Itens da Venda
CREATE TABLE IF NOT EXISTS itens_venda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venda_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- Inserção de usuários de teste
INSERT INTO usuarios (nome, email, senha, tipo) VALUES 
('Administrador', 'admin@geekhouse.com', '12345', 'admin'),
('Cliente Exemplo', 'cliente@geekhouse.com', '12345', 'cliente')
ON DUPLICATE KEY UPDATE nome=VALUES(nome);

-- Inserção de produtos de teste
INSERT INTO produtos (nome, categoria_id, preco, quantidade_estoque, editora_fabricante, especificacao, condicao, descricao, imagem_url) VALUES
('Mangá Chainsaw Man Vol. 1', 1, 34.90, 15, 'Panini', 'Vol. 1 - Tatsuki Fujimoto', 'Novo/Lacrado', 'Denji é um jovem que vive como caçador de demônios para quitar as dívidas do pai falecido.', 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=400'),
('Figure Roronoa Zoro - One Piece', 2, 289.90, 5, 'Bandai / Banpresto', 'Escala 1/8 - 20cm', 'Novo/Lacrado', 'Action figure detalhada do espadachim Roronoa Zoro em pose de combate.', 'https://images.unsplash.com/photo-1563089145-599997674d42?w=400'),
('Carta Charizard Holo Rara', 3, 150.00, 2, 'Copag / Pokémon TCG', 'Rara Holofoil #004', 'Raro/Colecionável', 'Carta colecionável clássica do Charizard em perfeito estado de conservação.', 'https://images.unsplash.com/photo-1613771404784-3a5686aa2be3?w=400');
