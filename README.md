# Sistema de Agendamento de Serviços

## Índice
1. Visão Geral
2. Requisitos do Sistema
3. Instalação e Configuração
4. Estrutura do Projeto
5. Funcionalidades
6. Banco de Dados
7. Tecnologias Utilizadas
8. Fluxo de Trabalho
9. Testes
10. Contribuição
11. Licença

---

## 1. Visão Geral
O sistema de agendamento de serviços é uma aplicação web desenvolvida para gerenciar agendamentos de serviços, como cortes de cabelo, manicure, pedicure, entre outros. Ele permite que os usuários agendem serviços, selecionem horários e formas de pagamento, enquanto os administradores podem gerenciar serviços e visualizar agendamentos.

## 2. Requisitos do Sistema
### Requisitos Funcionais
#### Usuários:
- Agendar serviços.
- Selecionar data, hora e forma de pagamento.
- Visualizar o valor do serviço automaticamente.

#### Administradores:
- Gerenciar serviços (adicionar, editar, excluir).
- Visualizar todos os agendamentos.

### Requisitos Não Funcionais
- Interface responsiva e amigável.
- Segurança básica (proteção contra SQL injection, uso de sessões para autenticação).
- Compatibilidade com navegadores modernos (Chrome, Firefox, Edge).

## 3. Instalação e Configuração
### Pré-requisitos
- Servidor web (Apache, Nginx).
- PHP 7.4 ou superior. (utilizei a versão 5.6.31 e funcionou bem)
- Banco de dados MySQL ou MariaDB.
- Composer (para gerenciamento de dependências, se necessário).

### Passos para Instalação
1. Clone o repositório:
   ```bash
   git clone https://github.com/seu-usuario/sistema-agendamento.git
   cd sistema-agendamento
   ```
2. Configure o banco de dados:
   - Crie um banco de dados MySQL.
   - Importe o arquivo SQL (`database.sql`) para criar as tabelas necessárias.
3. Configure o arquivo `conexao.php`:
   ```php
   $host = 'localhost';
   $dbname = 'nome_do_banco';
   $user = 'usuario';
   $pass = 'senha';
   ```
4. Inicie o servidor web:
   ```bash
   php -S localhost:8000
   ```
5. Acesse o sistema no navegador: `http://localhost:8000`.

## 4. Estrutura do Projeto
```
sistema-agendamento/
├── css/
│   └── style.css              # Estilos CSS
├── js/
│   └── script.js              # Scripts JavaScript
├── php/
│   ├── conexao.php            # Configuração do banco de dados
│   ├── buscar_valor_servico.php # Endpoint para buscar valor do serviço
│   └── menu.php               # Menu lateral
├── index.php                  # Página inicial
├── agendar.php                # Página de agendamento
├── login.php                  # Página de login
├── database.sql               # Script SQL para criar o banco de dados
└── README.md                  # Documentação do projeto
```

## 5. Funcionalidades
### Agendamento de Serviços
- O usuário seleciona um serviço, data, hora e forma de pagamento.
- O valor do serviço é preenchido automaticamente ao selecionar o serviço.

### Gerenciamento de Serviços (Admin)
- Adicionar, editar e excluir serviços.
- Visualizar todos os agendamentos.

### Autenticação
- Login e logout de usuários.
- Restrição de acesso a páginas administrativas.

## 6. Banco de Dados
### Tabelas
**usuarios:**
- `id`: Chave primária.
- `nome`: Nome do usuário.
- `email`: E-mail do usuário.
- `senha`: Senha do usuário (armazenada em texto plano, não recomendado para produção).
- `perfil`: Perfil do usuário (admin ou usuario).

**servicos:**
- `id`: Chave primária.
- `servico`: Nome do serviço.
- `valor`: Valor do serviço.

**agendamentos:**
- `id`: Chave primária.
- `nome_cliente`: Nome do cliente.
- `servico`: Serviço agendado.
- `data`: Data do agendamento.
- `hora`: Hora do agendamento.
- `forma_pagamento`: Forma de pagamento.
- `valor`: Valor do serviço.

## 7. Tecnologias Utilizadas
**Front-end:**
- HTML5, CSS3, JavaScript.

**Back-end:**
- PHP (linguagem de programação).
- MySQL (banco de dados).

**Ferramentas:**
- Git (controle de versão).
- Composer (gerenciamento de dependências, se necessário).

## 8. Fluxo de Trabalho
### Login:
- O usuário faz login no sistema.
- Se for administrador, tem acesso ao painel de gerenciamento.

### Agendamento:
- O usuário seleciona um serviço, data, hora e forma de pagamento.
- O valor é preenchido automaticamente.

### Gerenciamento:
- O administrador pode adicionar, editar ou excluir serviços.
- Visualiza todos os agendamentos.

## 9. Testes
### Testes Manuais
**Agendamento:**
- Verifique se o valor do serviço é preenchido automaticamente.
- Teste o envio do formulário de agendamento.

**Login:**
- Teste o login com credenciais válidas e inválidas.

**Gerenciamento:**
- Adicione, edite e exclua serviços.
- Verifique se os agendamentos são exibidos corretamente.

### Testes Automatizados (Opcional)
- Utilize ferramentas como PHPUnit para testes automatizados no back-end.

## 10. Contribuição
1. Faça um fork do projeto.
2. Crie uma branch para sua feature/correção:
   ```bash
   git checkout -b minha-feature
   ```
3. Commit suas alterações:
   ```bash
   git commit -m "Adiciona nova funcionalidade"
   ```
4. Envie para o repositório remoto:
   ```bash
   git push origin minha-feature
   ```
5. Abra um pull request no GitHub.

## 11. Licença
Este projeto está licenciado sob a MIT License.

