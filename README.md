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

http://localhost:8089

### Como usar

#### crie um usuário pela api

```
POST http://localhost:8000/api/register

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}

a conta do usuário sera criada
e um token de acesso sera gerado
```

#### utilize o token para as proximas etapas

#### crie um vendedor

```
use o token no BeaterToken do authorization

POST http://localhost:8000/api/sellers

{
    "name": "fabio",
    "email": "fabio@test.com"
}
```

#### crie uma venda

```
use o token no BeaterToken do authorization

POST http://localhost:8000/api/sales

{
    "seller_id": 1,
    "sale_value": 40.99
}
```

#### listar os vendedores

```
use o token no BeaterToken do authorization

GET http://localhost:8000/api/sellers
```

#### listar as vendas de um vendedor

```
use o token no BeaterToken do authorization

GET http://localhost:8000/api/sales/1
```

#### se o token expirar, logar novamente

```
use o token no BeaterToken do authorization

POST http://localhost:8000/api/login

{
    "name": "fabio",
    "email": "fabio@test.com"
}

um novo token sera gerado
```

### Acesso

#### url da aplicação de microserviços

http://localhost:8000/api/sales-all

#### servidor de recebimento de email (relatorio de vendas)

http://localhost:8026/

#### url kibana (logs do elasticsearch)

http://localhost:5603/app/management/data/index_management/indices

#### url rabitMq (filas)

http://localhost:15672/#/queues

- usuario = user
- senha = password

### mysql

- usuario = root
- senha = root
- servidor = localhost
- porta = 3337
- banco = seller-tray

### License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
