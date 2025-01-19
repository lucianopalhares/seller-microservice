<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Application\Sales\Services\SaleService;
use App\Services\RabbitMQService;
use App\Http\Resources\SaleResource;
use Illuminate\Support\Facades\Config;

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
            $saleService->getSalesOfTheDay();
            $items = $saleService->getSales();

            if (empty($items)) {
                $this->warn('Nenhuma venda encontrada para publicar.');
                return;
            }

            $sales = [];

            foreach ($items as $item) {
                try {
                    $sale = new SaleResource($item);
                    $array = $sale->toArray(request());
                    $sales[] = $array;
                } catch (\Exception $e) {
                    Log::error('Erro ao publicar venda no RabbitMQ', [
                        'sale_id' => $sale['id'] ?? 'Desconhecido',
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $message = json_encode($sales);

            $queue = Config::get('services.rabbitmq.queue');

            $this->rabbitMQService->publishMessage($queue, $message);

            $this->info('Registros de vendas publicados no RabbitMQ com sucesso.');

        } catch (\Exception $e) {
            Log::error('Erro durante a publicação das vendas no RabbitMQ', [
                'error' => $e->getMessage(),
            ]);

            $this->error('Falha ao publicar registros de vendas no RabbitMQ. Verifique os logs para mais detalhes.');
        }
    }
}
