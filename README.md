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

### Tecnologias usadas:

* Framework Laravel 11 (php 8.4) 
* API de Microserviço com as melhores práticas de design patterns
* Autenticação JWT
* Fila com RabitMQ (para envio de emails das vendas)
* Observabilidade com Sentry (monitoramento de erros)
* Cache com Redis (busca de vendas)
* Banco de dados Mysql
* Logs com Elasticsearch e Kibana (de todas as vendas criadas)
* Documentação da API

### Como funciona:

* Serviço automaticamente agendado para diariamente meia-noite pegar todas vendas do dia e publicar na fila do RabitMQ
* RabitMQ consome a fila de vendas escutando o recebimento destas vendas que foram publicadas, e realiza envio de email como relatório de vendas
* cada venda criada é registrada no Elasticsearch para consulta de logs
* cache de vendas:
    - ao buscar as vendas de um vendedor, é buscado do banco de dados mysql e são armazenadas no cache com o Redis
    - a proxima consulta ira pegar as vendas não mais do banco, mas do Redis
    - mais uma consulta é feita no banco de dados mysql, mas buscando vendas a partir da ultima venda registrada anteriormente no redis
    - as vendas retornadas do banco é mesclada com as vendas ja existente no Redis
    - aliviando o banco de dados de consultas repetidas
    - o cache de vendas tem duração de 1 hora

### Instalação

#### instale a aplicação

```
docker-compose up -d
```

#### entre na aplicação laravel

```
docker exec -it seller_tray_app bash
```

#### instale as dependencias necessarias

```
composer install
```

#### crie o arquivo de configuração

```
cp .env.example .env
```

#### crie a chave da aplicação

```
php artisan key:generate
```

#### crie as tabelas do banco de dados

```
php artisan migrate
```

#### crie o indice de vendas para o elasticsearch

```
php artisan elasticsearch:create-index
```

#### saia do container

```
exit
```

#### reinicie a aplicação

```
docker-compose restart
```

#### adicione permissão

```
sudo chmod 777 -R storage bootstrap
```

#### aguarde ate 30 segundos enquanto a aplicação inicia

### Documentação da api

http://localhost:8000/docs/api

### Exemplo de Uso

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

### Observabilidade - monitorar erros (sentry)

#### opção 1 = acesse a seguinte conta sentry pre-configurada

- acesse o site da sentry:

https://sentry.io/welcome/

- digite o login e senha:

usuario:
```
xebok35047@fundapk.com
```
senha:
```
dK54865*hh
```

- acesse a seguinte url da sentry para acompanhar os erros:

https://test-dby.sentry.io/issues/?referrer=sidebar

#### opção 2 = crie uma conta na sentry

- acesse o site da sentry, se registre e faça login

https://sentry.io/welcome/

- obtenha a chave de autenticação

- insira no seu .env da aplicação a chave de autenticação da sentry

```
SENTRY_LARAVEL_DSN=<sua_chave>
```

- reinicie a aplicacao

```
docker-compose restart
```

- acesse a aba de erros no painel da sentry (issues)

https://sentry.io/welcome/

### Fila (RabitMQ)

#### url

http://localhost:15672/#/queues

- usuario = user
- senha = password

### Logs

#### buscar todas vendas registradas (elasticsearch)

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

### FRONT para consumir a api

#### siga as instrucoes do seguinte repositorio do front, para consumir a api

https://github.com/lucianopalhares/seller-nuxt

### License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
