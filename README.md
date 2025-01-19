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

#### instale as dependencias necessarias

```
composer install
```

#### crie o indice de vendas para o elasticsearch

```
php artisan elasticsearch:create-index
```

### entre no rabitMQ

```
docker exec -it seller_tray_rabbitmq bash
```

### crie a fila para envio de email das vendas

```
rabbitmqadmin -u user -p password declare queue name=email_sales_queue durable=true
```

### crie a exchange

```
rabbitmqadmin -u user -p password declare exchange name=email_sales_events type=direct durable=true
```

### crie um binding entre a fila e a exchange

```
rabbitmqadmin -u user -p password declare binding source=email_sales_events destination=email_sales_queue destination_type=queue routing_key=email_sales_binding
```

### public uma mensagem de teste na fila

```
rabbitmqadmin -u user -p password publish exchange=email_sales_events routing_key=email_sales_binding payload="Hello World"
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
