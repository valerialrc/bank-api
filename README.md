# Projeto API Banco

Este projeto é uma API RESTful desenvolvida em Laravel para simular operações bancárias em diferentes moedas.
Para fazer a conversão de moedas dentro das operações, foi utilizado o endpoint de boletim de Fechamento PTAX da API do Banco Central.

## Requisitos

- PHP >= 8.0
- Laravel
- Composer
- MySQL
- Swagger

## Configuração no Linux

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

- Copie o arquivo .env.example para .env e para .env.testing

    ```bash
    cp .env.example .env
    ```

    ```bash
    cp .env.example .env.testing
    ```

- Configure as variáveis necessárias em ambos arquivos
  - Sugestões:
    ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=bank
    DB_USERNAME=laravel_user
    DB_PASSWORD=
    ```

    ```bash
    APP_ENV=testing
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=bank_api_test
    DB_USERNAME=laravel_user
    DB_PASSWORD=
    ```

    - Obs: Será necessário conceder permissão de acesso ao usuário configurado.

4. **Executar as migrações do banco de dados:**

    ```bash
    php artisan migrate --seed
    ```

5. Iniciar o servidor local:

    ```bash
    php artisan serve
    ```
6. Acesse a API em http://localhost:8000.

## Documentação da API com Swagger
Este projeto utiliza Swagger para a documentação da API. Swagger fornece uma interface amigável para interagir com a API e visualizar os endpoints disponíveis.

Depois de configurar e executar o projeto, você pode acessar o Swagger UI no seguinte URL:

    http://localhost:8000/api/documentation
  

Obs:
1. Certifique-se de estar usando um ID de conta válido para fazer as consultas. Para isso, após executar os seeders, execute o banco de dados:
    ```bash
    mysql -u laravel_user -p bank
    ```
    Realize a consulta e escolha um ID:
      ```bash
      SELECT * FROM accounts;
      ```

2. As moedas válidas são apenas as disponibilizadas pela API do Banco Central:

      AUD, CAD, CHF, DKK, EUR, GBP, JPY, NOK, SEK, USD

## Comandos Úteis
1. Executar os testes:

    ```bash
    php artisan test
    ```

2. Para gerar um relatório de cobertura de código, use:

    ```bash
    XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html coverage
    ```

3. Abra o arquivo com o relatório de cobertura:

    ```bash
    xdg-open coverage/index.html
    ```

## Rotas da API

- GET /api/accounts/{account}/balance: Consulta o saldo da conta.
- GET /api/accounts/{account}/balance?currency={currency}: Consulta o saldo da conta em uma moeda específica.
- POST /api/accounts/{account}/deposit: Realiza um depósito na conta.
- POST /api/accounts/{account}/withdraw: Realiza um saque na conta.
