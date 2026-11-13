CREATE DATABASE IF NOT EXISTS loja_geek DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE loja_geek;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS categorias;
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS produtos;
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria_id INT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    quantidade_estoque INT NOT NULL DEFAULT 0,
    editora_fabricante VARCHAR(100) NULL,
    especificacao VARCHAR(100) NULL,
    condicao VARCHAR(50) DEFAULT 'Novo/Lacrado',
    descricao TEXT NULL,
    imagem_url VARCHAR(255) NULL DEFAULT 'img/placeholder.png',
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS vendas;
CREATE TABLE vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_venda DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    valor_total DECIMAL(10,2) NOT NULL,
    forma_pagamento VARCHAR(50) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS itens_venda;
CREATE TABLE itens_venda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venda_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO categorias (id, nome) VALUES
(1, 'Mangás'),
(2, 'HQs & Quadrinhos'),
(3, 'Action Figures'),
(4, 'Camisetas'),
(5, 'Canecas'),
(6, 'Colecionáveis Premium'),
(7, 'Posteres & Quadros'),
(8, 'Jogos de Tabuleiro'),
(9, 'Card Games'),
(10, 'Chaveiros'),
(11, 'Pelúcias'),
(12, 'Acessórios Geek'),
(13, 'Estátuas'),
(14, 'Funko Pop'),
(15, 'Light Novels');

INSERT INTO usuarios (id, nome, email, senha, tipo) VALUES
(1, 'Administrador Geek', 'admin@geekhouse.com', '12345', 'admin'),
(2, 'Cliente Geek', 'cliente@geekhouse.com', '12345', 'cliente'),
(3, 'Peter Parker', 'peter@dailybugle.com', '12345', 'cliente'),
(4, 'Bruce Wayne', 'bruce@wayne.com', '12345', 'cliente'),
(5, 'Tony Stark', 'tony@stark.com', '12345', 'cliente'),
(6, 'Clark Kent', 'clark@dailyplanet.com', '12345', 'cliente'),
(7, 'Diana Prince', 'diana@amazon.com', '12345', 'cliente'),
(8, 'Barry Allen', 'barry@centralcitypd.com', '12345', 'cliente');

INSERT INTO produtos (id, nome, categoria_id, preco, quantidade_estoque, editora_fabricante, especificacao, condicao, descricao, imagem_url) VALUES
(1, 'Mangá One Piece Volume 01', 1, 34.90, 15, 'Panini Comics', 'Vol. 01', 'Novo/Lacrado', 'Primeiro volume da épica jornada de Luffy', 'img/mangas/one_piece_vol_01.jpg'),
(2, 'Mangá One Piece Volume 02', 1, 34.90, 12, 'Panini Comics', 'Vol. 02', 'Novo/Lacrado', 'Continuação das aventuras de One Piece', 'img/mangas/one_piece_vol_02.jpg'),
(3, 'HQ Homem-Aranha Volume 01', 2, 49.90, 10, 'Panini Comics', 'Edição Especial', 'Novo/Lacrado', 'Aventuras clássicas do Homem-Aranha', 'img/hqs/spiderman_vol_01.jpg'),
(4, 'HQ Batman Volume 01', 2, 54.90, 8, 'Panini Comics', 'Edição de Luxo', 'Novo/Lacrado', 'História marcante do Cavaleiro das Trevas', 'img/hqs/batman_vol_01.jpg'),
(5, 'Figure Roronoa Zoro 15cm', 3, 250.00, 5, 'Bandai Tamashii', 'Escala 15cm', 'Novo/Lacrado', 'Action figure rica em detalhes do Zoro', 'img/figures/zoro_15cm.jpg'),
(6, 'Figure Monkey D. Luffy 15cm', 3, 250.00, 5, 'Bandai Tamashii', 'Escala 15cm', 'Novo/Lacrado', 'Action figure oficial do Luffy', 'img/figures/luffy_15cm.jpg'),
(7, 'Camiseta Gojo Satoru (M)', 4, 79.90, 12, 'Piticas', 'Tamanho M', 'Novo/Lacrado', 'Camiseta 100% algodão Jujutsu Kaisen', 'img/camisetas/gojo_m.jpg'),
(8, 'Camiseta Ryomen Sukuna (G)', 4, 79.90, 10, 'Piticas', 'Tamanho G', 'Novo/Lacrado', 'Camiseta estilizada do Rei das Maldições', 'img/camisetas/sukuna_g.jpg'),
(9, 'Caneca Marvel Comics', 5, 45.00, 20, 'Canecas & Cia', '350ml Porcelana', 'Novo/Lacrado', 'Caneca temática dos Vingadores', 'img/canecas/marvel.jpg'),
(10, 'Caneca Attack on Titan', 5, 49.90, 15, 'Canecas & Cia', '350ml Porcelana', 'Novo/Lacrado', 'Caneca com o brasão da Tropa de Exploração', 'img/canecas/aot.jpg'),
(11, 'Pokémon TCG Booster Pack', 9, 12.00, 50, 'Copag', 'Edição Estelar', 'Novo/Lacrado', 'Pacote de cartas colecionáveis Pokémon', 'img/cards/pokemon_booster.jpg'),
(12, 'Funko Pop! Naruto Uzumaki', 14, 130.00, 7, 'Funko Import', 'Pop! Animation', 'Novo/Lacrado', 'Boneco colecionável de vinil do Naruto', 'img/funko/naruto.jpg');

INSERT INTO vendas (id, usuario_id, data_venda, valor_total, forma_pagamento) VALUES
(1, 4, '2026-10-01 10:30:00', 284.90, 'Cartão de Crédito'),
(2, 5, '2026-10-02 11:15:00', 179.70, 'PIX'),
(3, 6, '2026-10-03 14:20:00', 340.00, 'Cartão de Crédito'),
(4, 7, '2026-10-04 16:45:00', 99.90, 'Boleto Bancário');

INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES
(1, 1, 1, 34.90),
(1, 5, 1, 250.00),
(2, 3, 2, 49.90),
(2, 7, 1, 79.90),
(3, 6, 1, 250.00),
(3, 9, 2, 45.00),
(4, 4, 1, 54.90),
(4, 10, 1, 45.00);
