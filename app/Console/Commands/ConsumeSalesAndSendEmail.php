<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RabbitMQService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Application\Sales\Services\SaleService;

/**
 * Comando para consumir mensagens da fila RabbitMQ e enviar e-mails formatados.
 */
class ConsumeSalesAndSendEmail extends Command
{
    /**
     * O nome e assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'sales:consume';

    /**
     * A descrição do comando.
     *
     * @var string
     */
    protected $description = 'Consome mensagens de vendas do RabbitMQ e envia e-mails formatados';

    /**
     * Serviço de integração com RabbitMQ.
     *
     * @var RabbitMQService
     */
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
     * Manipula a execução do comando.
     *
     * @param SaleService $saleService
     */
    public function handle(SaleService $saleService)
    {
        try {

                       // Declarar a exchange, fila e bind no RabbitMQ, sem causar erro se já existirem
            $this->rabbitMQService->declareExchangeQueueBind(env('RABBITMQ_EXCHANGE'), env('RABBITMQ_QUEUE'), env('RABBITMQ_BIND'));

            $this->rabbitMQService->consumeMessage(env('RABBITMQ_QUEUE'), function ($message) {
                $sales = json_decode($message->body, true);

                if (!$sales) {
                    Log::channel('seller_microservice')->error('Mensagem inválida recebida do RabbitMQ', ['message' => $message->body] );
                    return;
                }

                try {

                    print_r($sales);
                    /*
                    Mail::send('emails.sales', ['sale' => $sale], function ($email) use ($sale) {
                        $email->to('email@example.com')
                              ->subject('Registro de Venda #' . $sale['id']);
                    });

                    DB::table('sales')
                        ->where('id', $sale['id'])
                        ->update(['notified' => 1]);
                        */

                } catch (\Exception $e) {
                    Log::channel('seller_microservice')->error('Erro ao consumir vendas do rabitMQ', ['message' => $e->getMessage()] );
                }
            });

            Log::channel('seller_microservice')->info('Lista de vendas consumida do rabitMQ');
        } catch (\Exception $e) {
            Log::error('Erro ao consumir mensagens do RabbitMQ', [
                'error' => $e->getMessage(),
            ]);
            $this->error('Falha ao consumir mensagens. Verifique os logs para mais detalhes.');
        }
    }
}
