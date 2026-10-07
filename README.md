# 👾 Geek House - Sistema Web e-Commerce (Projeto Final)

Sistema de e-Commerce e Controle de Acesso desenvolvido em **PHP**, **MySQL**, **HTML5** e **CSS3** para o Trabalho Final da disciplina de Linguagem de Programação II.

---

## 💜 Funcionalidades do Sistema

- **Portal Público (Loja Geek):**
  - Vitrine de produtos em destaque (Mangás, Action Figures e Cartas Pokémon).
  - Catálogo completo com busca por nome e filtro dinâmico por categoria.
  - Página de detalhes do produto com especificações técnicas.
  - Carrinho de compras dinâmico gerenciado via Sessão (`$_SESSION['carrinho']`).
  - Checkout/Finalização de pedido com gravação de vendas no MySQL e atualização automática de estoque.
  - Página institucional "Sobre Nós".

- **Sistema de Usuários & Segurança:**
  - Login com autenticação no MySQL e hash de sessão.
  - Perfis diferenciados: `admin` (Administrador) e `cliente` (Cliente comum).
  - Controle rigoroso de acesso e trava de URLs restritas (`conexao/auth.php`).
  - Logout seguro com destruição completa de cookies de sessão.

- **Painel Administrativo (CRUD Completo de Produtos):**
  - **C**reate: Cadastro de novos produtos com campos específicos geek.
  - **R**ead: Listagem detalhada em tabela responsiva com `JOIN` de categorias.
  - **U**pdate: Formulário de edição preenchido dinamicamente.
  - **D**elete: Exclusão com confirmação e integridade referencial.

---

## 🚀 Como Executar o Projeto

1. Clone este repositório ou copie a pasta para a raiz do seu servidor local (ex: `htdocs/geek_house` no XAMPP).
2. Abra o phpMyAdmin e crie o banco de dados `loja_geek`.
3. Importe o arquivo `banco.sql`.
4. Acesse no navegador: `http://localhost/geek_house/`

### 🔑 Credenciais para Teste:
- **Administrador:** `admin@geekhouse.com` / **Senha:** `12345`
- **Cliente:** `cliente@geekhouse.com` / **Senha:** `12345`
