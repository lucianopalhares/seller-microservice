<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElasticsearchService;
use Illuminate\Support\Facades\Log;

/**
 * Classe para criar um índice no Elasticsearch.
 *
 * Esta classe define um comando Artisan para criar o índice de vendas no Elasticsearch,
 * configurando as definições e mapeamentos necessários para o armazenamento dos dados.
 */
class CreateElasticsearchIndex extends Command
{
    /**
     * O nome e a assinatura do comando Artisan.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:create-index';

    /**
     * A descrição do comando Artisan.
     *
     * @var string
     */
    protected $description = 'Criar index para o Elasticsearch de vendas';

    /**
     * Lógica principal do comando.
     *
     * Este método cria um índice no Elasticsearch chamado "sales" com as configurações
     * e mapeamentos especificados. Se houver um erro durante a criação, ele será capturado
     * e exibido no console.
     *
     * @return void
     */
    public function handle()
    {
        $elasticsearchService = new ElasticsearchService();
        $elasticsearchService->createSalesIndex();
    }
}
