<p align="center"><img src="https://cdn-icons-png.flaticon.com/512/2544/2544336.png" width="400" alt="Seller API"></p>

## Seller API (microserviço)

### Descrição

API para gestão de vendas, vendedores e cálculo de comissão das vendas (a comissão é de 8.5% sobre o valor da venda)

- Criar vendedor
- Listar todos os vendedores
- Lançar nova venda
- Listar todas as vendas de um vendedor

### Relatório via email

Ao final de cada dia é enviado um email com um relatório com a soma de todas as vendas efetuadas no dia.

### Instalação

#### entre na aplicação laravel

```
docker exec -it seller_tray_app bash
```

#### crie a chave da aplicação

```
php artisan key:generate
```

#### instale as dependencias necessarias

```
composer install
```

#### crie as tabelas do banco de dados

```
php artisan migrate
```

#### crie o indice de vendas para o elasticsearch

```
php artisan elasticsearch:create-index
```

### documentação da api de microserviços

http://localhost:8000/docs/api

### Como usar

#### crie um usuário pela api

metodo: POST
url:
```
http://localhost:8000/api/register
```
body:
```
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

a conta do usuário sera criada
e um token de acesso sera gerado

#### utilize o token para as proximas etapas

#### crie um vendedor

metodo: POST
authorization: BearerToken (use o token gerado)
url:
```
http://localhost:8000/api/sellers
```
body:
```
{
    "name": "fabio",
    "email": "fabio@test.com"
}
```

#### crie uma venda

metodo: POST
authorization: BearerToken (use o token gerado)
url:
```
http://localhost:8000/api/sales
```
body:
```
{
    "seller_id": 1,
    "sale_value": 40.99
}
```

#### listar os vendedores

metodo: GET
authorization: BearerToken (use o token gerado)
url:
```
http://localhost:8000/api/sellers
```

#### listar as vendas de um vendedor

metodo: GET
authorization: BearerToken (use o token gerado)
url (troque o 1 pelo id do vendedor):
```
http://localhost:8000/api/sales/1
```

#### se o token expirar, logar novamente

um novo token sera gerado para usar novamente

metodo: POST
authorization: BearerToken (use o token gerado)
url:
```
http://localhost:8000/api/login
```
body:
```
{
    "name": "fabio",
    "email": "fabio@test.com"
}
```

### Receber email com Relatório de vendas

#### acesse esta url para acompanhar os emails recebidos com o relatório de vendas:

http://localhost:8026

#### horário do recebimento do email

- foi configurado para recebimento das vendas do dia todos os dias à meia-noite
- deve haver vendas fechadas no dia para o email ser recebido

#### receber email imediatamente

- execute o seguinte comando:

```
docker exec -it seller_tray_app bash -c "php artisan sales:publish"
```

- voce deve receber o email imediatamente

#### alterar o horario de recebimento de email (a cada 10 segundos)

- entre no seguinte arquivo e siga as instruções contidas nele, descomentando uma linha especifica:

```
routes/console.php
```

- descomente o trecho:

```
//Schedule::command('sales:publish')->everyTenSeconds();
```

- reinicie o container:

```
docker-compose restart
```

- voce deve receber o email em ate 30 segundos

### Observabilidade (sentry)

#### crie uma conta ou faça login da url da sentry

https://sentry.io/welcome/

#### altere a chave de autenticação no sentry

- no .env insira sua chave do sentry

```
SENTRY_LARAVEL_DSN=<sua_chave>
```

#### acesse o painel do sentry para monitorar os erros

https://sentry.io/welcome/

### Fila (RabitMQ)

#### url

http://localhost:15672/#/queues

- usuario = user
- senha = password

### Logs

#### log de todas as vendas (elasticsearch)

http://localhost:8000/api/sales-elastic

#### url kibana (logs do elasticsearch)

http://localhost:5603/app/management/data/index_management/indices

### Servidor de Email

#### servidor de recebimento de email (relatorio de vendas)

http://localhost:8026/

### Banco de dados (mysql)

#### acesso

- usuario = root
- senha = root
- servidor = localhost
- porta = 3337
- banco = seller-tray

### License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
