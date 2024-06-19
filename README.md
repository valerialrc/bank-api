# Projeto API Banco

Este projeto é uma API RESTful desenvolvida em Laravel para simular operações bancárias em diferentes moedas.

## Requisitos

- PHP >= 8.0
- Composer
- MySQL

## Configuração

1. **Clonar o repositório:**

  ```bash
    git clone git@github.com:valerialrc/bank-api.git
    cd bank-api
  ```

2. **Instalar as dependências do Composer:**

  ```bash
    composer install
  ```
  
3. **Configurar o ambiente:**

- Copie o arquivo .env.example para .env e configure as variáveis de ambiente necessárias, como conexão com o banco de dados.

4. **Gerar a chave da aplicação:**

  ```bash
    php artisan key:generate
  ```

5. **Executar as migrações do banco de dados:**

  ```bash
    php artisan migrate
  ```

Iniciar o servidor local:

  ```bash
    php artisan serve
  ```
Acesse a API em http://localhost:8000.

## Comandos Úteis
Executar os testes:

  ```bash
    php artisan test
  ```
