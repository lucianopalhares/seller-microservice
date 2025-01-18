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

    public function __construct()
    {
        try {
            $hosts = config('elasticsearch.hosts');

            if (empty($hosts)) {
                throw new Exception('servidor do Elasticsearch inválido.');
            }

            $this->client = ClientBuilder::create()->setHosts($hosts)->build();

            if (!$this->client->ping()) {
                throw new Exception('Falha na conexão com o Elasticsearch.');
            }

        } catch (NoNodeAvailableException $e) {
            throw new Exception('Elasticsearch não está acessível: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Erro ao tentar conectar ao Elasticsearch: ' . $e->getMessage());
        }
    }

    /**
     * Cria o índice 'sales' no Elasticsearch
     */
    public function createSalesIndex(): void
    {
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
    public function saveToSalesIndex(object $data): bool
    {
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

            return true;
        } catch (TransportException $e) {
            Log::channel('seller_microservice')->error("Erro de transporte ao salvar venda no Elasticsearch: " . $e->getMessage());
            return false;
        } catch (NoNodeAvailableException $e) {
            Log::channel('seller_microservice')->error("Não foi possível conectar ao Elasticsearch para salvar a venda: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            Log::channel('seller_microservice')->error("Erro desconhecido ao salvar venda no Elasticsearch: " . $e->getMessage());
            return false;
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
