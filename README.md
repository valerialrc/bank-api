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
    php artisan migrate --seed
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

Para gerar um relatório de cobertura de código, use:

  ```bash
    XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html coverage
  ```

Abra o arquivo com o relatório de cobertura:

 ```bash
    xdg-open coverage/index.html
  ```

## Rotas da API

- GET /api/accounts/{account}/balance: Consulta o saldo da conta.
- GET /api/accounts/{account}/balance?currency={currency}: Consulta o saldo da conta em uma moeda específica.
- POST /api/accounts/{account}/deposit: Realiza um depósito na conta.
- POST /api/accounts/{account}/withdraw: Realiza um saque na conta.
