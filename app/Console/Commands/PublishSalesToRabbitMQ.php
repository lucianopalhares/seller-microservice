<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Application\Sales\Services\SaleService;
use App\Services\RabbitMQService;

class PublishSalesToRabbitMQ extends Command
{
    protected $signature = 'sales:publish';
    protected $description = 'Publica os registros de vendas no RabbitMQ';

    protected $rabbitMQService;

    /**
     * Construtor da classe.
     *
     * @param RabbitMQService $rabbitMQService
     */
    public function __construct(RabbitMQService $rabbitMQService)
    {
        parent::__construct();
        $this->rabbitMQService = $rabbitMQService;
    }

    /**
     * Lógica principal do comando.
     *
     * @param SaleService $saleService
     */
    public function handle(SaleService $saleService)
    {
        try {
            // Buscar todas as vendas usando o serviço
            $saleService->fetchAllSales();

            $sales = $saleService->getSales();

            if (empty($sales)) {
                $this->warn('Nenhuma venda encontrada para publicar.');
                return;
            }

            $sales = [];

            foreach ($sales as $sale) {
                try {
                    print_r( get_object_vars($sale));
                    $sales[] = get_object_vars($sale);
                } catch (\Exception $e) {
                    // Registrar erro específico ao publicar a mensagem
                    Log::error('Erro ao publicar venda no RabbitMQ', [
                        'sale_id' => $sale['id'] ?? 'Desconhecido',
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $message = json_encode($sales);

            $this->rabbitMQService->publishMessage(env('RABBITMQ_QUEUE'), $message);

            $this->info('Registros de vendas publicados no RabbitMQ com sucesso.');

        } catch (\Exception $e) {
            // Registrar erro genérico durante a execução do comando
            Log::error('Erro durante a publicação das vendas no RabbitMQ', [
                'error' => $e->getMessage(),
            ]);

            $this->error('Falha ao publicar registros de vendas no RabbitMQ. Verifique os logs para mais detalhes.');
        }
    }
}
