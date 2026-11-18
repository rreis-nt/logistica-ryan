# 🚚 Gerenciador Logístico

Aplicação web desenvolvida para a **Atividade Prática Orientada (APO)** da disciplina **Fundamentos de Programação para Internet**.

O sistema permite cadastrar **produtos**, **endereços** (origem e destino) e **transportadores** e, a partir deles, **gerar fretes**. Um mesmo transportador pode levar **vários produtos** no mesmo frete (relação 1 para N). Também é possível **baixar o relatório dos fretes de um dia** em PDF ou CSV.

## ✨ Funcionalidades

- CRUD completo (cadastrar, listar, editar e excluir) de **produtos**, **endereços** e **transportadores**.
- **Geração de frete**: escolhe transportador, origem, destino, data, valor e **vários produtos** (campos adicionados/removidos dinamicamente com JavaScript, com peso total e valor da carga calculados na hora).
- Tela de **detalhe do frete** e filtro de fretes por data.
- **Relatório de fretes por dia** com download em **PDF** (gerado em PHP puro) e **CSV** (compatível com Excel).
- Menu de navegação em todas as telas e interface responsiva com **Bootstrap 5**.
- Máscaras de CEP, CPF/CNPJ, telefone e placa; preenchimento automático do endereço pelo CEP (ViaCEP, opcional).
- Validações no servidor, proteção contra **SQL Injection** (PDO com prepared statements), **XSS** (escape de saída) e **CSRF** (token nos formulários).

## 🛠️ Tecnologias

| Camada | Tecnologia |
| --- | --- |
| Backend | PHP 8 (PDO) |
| Banco de dados | MySQL / MariaDB (XAMPP) |
| Frontend | HTML5, CSS3, JavaScript, Bootstrap 5 + Bootstrap Icons |
| Versionamento | Git e GitHub |

## ▶️ Como executar (XAMPP)

1. Instale o [XAMPP](https://www.apachefriends.org/) e inicie **Apache** e **MySQL** no painel de controle.
2. Copie a pasta do projeto para `C:\xampp\htdocs\gerenciador-logistico` (Linux/Mac: `/opt/lampp/htdocs/` ou `/Applications/XAMPP/htdocs/`).
3. Abra o **phpMyAdmin** (`http://localhost/phpmyadmin`), vá em **Importar** e selecione `database/schema.sql`.
   - (Opcional) importe também `database/dados_exemplo.sql` para ter dados de teste.
4. Se o seu MySQL tiver usuário/senha diferentes do padrão do XAMPP (`root` sem senha), ajuste `config/database.php`.
5. Acesse **http://localhost/gerenciador-logistico/**.

> O sistema precisa de pelo menos **1 produto, 2 endereços e 1 transportador** cadastrados para gerar um frete.

## 🗂️ Estrutura do projeto

```
gerenciador-logistico/
├── index.php                 # Painel inicial
├── config/database.php       # Conexão PDO com o MySQL
├── includes/
│   ├── functions.php         # Helpers, layout (menu), consultas de frete
│   └── pdf.php               # Gerador de PDF em PHP puro
├── produtos/                 # index.php, form.php, excluir.php
├── enderecos/                # index.php, form.php, excluir.php
├── transportadores/          # index.php, form.php, excluir.php
├── fretes/                   # index.php, form.php, ver.php, excluir.php
├── relatorios/               # index.php, download.php (PDF/CSV)
├── assets/
│   ├── css/style.css
│   └── js/                   # app.js (máscaras/CEP), frete.js (campos dinâmicos)
├── database/                 # schema.sql e dados_exemplo.sql
└── docs/                     # Esboço das telas (PDF) e script que o gerou
```

## 🧩 Modelo de dados

```
transportadores 1 ──< fretes >── 1 enderecos (origem)
                         │  └──── 1 enderecos (destino)
                         └──< frete_itens >── 1 produtos
```

- `fretes`: transportador, origem, destino, data, valor do frete e observações.
- `frete_itens`: liga o frete aos produtos (frete **1 → N** produtos) com a quantidade.

## 🔀 Versionamento

O projeto foi desenvolvido com Git, com commits a cada etapa (estrutura, banco, cadastros, fretes, relatórios e documentação).

## 👤 Autor

Projeto acadêmico — Fundamentos de Programação para Internet (UNIPAR EAD).
