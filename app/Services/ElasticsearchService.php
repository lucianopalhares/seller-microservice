<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Transport\Exception\TransportException;
use Elastic\Transport\Exception\NoNodeAvailableException;
use Exception;
use Illuminate\Support\Facades\Log;

class ElasticsearchService
{
    protected $client;
    private $error = false;

    public function __construct()
    {
        try {
            $hosts = config('elasticsearch.hosts');

            if (empty($hosts)) {
                $this->error = true;
                Log::channel('seller_microservice')->error('servidor do Elasticsearch inválido.');
            }

            $this->client = ClientBuilder::create()->setHosts($hosts)->build();

            if (!$this->client->ping()) {
                $this->error = true;
                Log::channel('seller_microservice')->error('Falha na conexão com o Elasticsearch.');
            }

            $this->error = false;
        } catch (NoNodeAvailableException $e) {
            $this->error = true;
            Log::channel('seller_microservice')->error('Elasticsearch não está acessível: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->error = true;
            Log::channel('seller_microservice')->error('Erro ao tentar conectar ao Elasticsearch: ' . $e->getMessage());
        }
    }

    /**
     * Cria o índice 'sales' no Elasticsearch
     */
    public function createSalesIndex(): void
    {
        if ($this->error === true) return;

        try {
            $params = [
                'index' => 'sales',
                'body'  => [
                    'settings' => [
                        'number_of_shards' => 1,
                    ],
                    'mappings' => [
                        'properties' => [
                            'name' => ['type' => 'text'],
                            'email' => ['type' => 'text'],
                            'value' => ['type' => 'text'],
                            'comission' => ['type' => 'text'],
                            'date' => ['type' => 'text'],
                        ]
                    ]
                ]
            ];

            $this->client->indices()->create($params);

            Log::channel('seller_microservice')->info('index de venda criado com sucesso no elasticsearc!');

        } catch (TransportException $e) {
            Log::channel('seller_microservice')->error('Erro de transporte ao salvar indice de vendas no Elasticsearch: ' . $e->getMessage());
        } catch (NoNodeAvailableException $e) {
            Log::channel('seller_microservice')->error('Não foi possível conectar ao Elasticsearch para criar indice de vendas: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::channel('seller_microservice')->error('Erro desconhecido ao criar o índice de vendas no Elasticsearch: ' . $e->getMessage());
        }
    }

    /**
     * Salva um documento no índice 'sales'
     *
     * @param object $data
     * @return mixed
     */
    public function saveToSalesIndex(object $data): void
    {
        if ($this->error === true) return;

        try {
            $params = [
                'index' => 'sales',
                'id'    => $data->id,
                'body'  => [
                    'name' => $data->seller->name,
                    'email' => $data->seller->email,
                    'value' => $data->sale_value,
                    'commission' => $data->sale_commission,
                    'date' => \Carbon\Carbon::parse($data->created_at)->format('d/m/Y')
                ]
            ];

            $this->client->index($params);

            Log::channel('seller_microservice')->info('venda no elasticsearc salvo com sucesso');
        } catch (TransportException $e) {
            Log::channel('seller_microservice')->error("Erro de transporte ao salvar venda no Elasticsearch: " . $e->getMessage());
        } catch (NoNodeAvailableException $e) {
            Log::channel('seller_microservice')->error("Não foi possível conectar ao Elasticsearch para salvar a venda: " . $e->getMessage());
        } catch (Exception $e) {
            Log::channel('seller_microservice')->error("Erro desconhecido ao salvar venda no Elasticsearch: " . $e->getMessage());
        }
    }

    /**
     * Verifica a conexão com o Elasticsearch
     */
    public function ping(): mixed
    {
        try {
            return $this->client->ping();
        } catch (NoNodeAvailableException $e) {
            return 'Não foi possível conectar ao Elasticsearch: ' . $e->getMessage();
        }
    }

    public function fetchAllSales(): array
    {
        if ($this->error === true) return [];

        try {

            $params = [
                'scroll' => '30s',
                'size'   => 50,
                'index' => 'sales',
                'body'   => [
                    'query' => [
                        'match_all' => new \stdClass()
                    ]
                ]
            ];

            $response = $this->client->search($params);

            return $response['hits']['hits'];
        } catch (TransportException $e) {
            Log::channel('seller_microservice')->error("Erro de transporte para pegar vendas: " . $e->getMessage());
            return [];
        } catch (NoNodeAvailableException $e) {
            Log::channel('seller_microservice')->error("Não foi possível conectar ao Elasticsearch para buscar as vendas: " . $e->getMessage());
            return [];
        } catch (Exception $e) {
            Log::channel('seller_microservice')->error("Erro desconhecido ao pegar vendas no elasticsearch: " . $e->getMessage());
            return [];
        }
    }
}
