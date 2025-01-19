<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RabbitMQService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Application\Sales\Services\SaleService;
use Illuminate\Support\Facades\Config;
use App\Mail\SendEmailSales;

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
            $exchange = Config::get('services.rabbitmq.exchange');
            $queue = Config::get('services.rabbitmq.queue');
            $bind = Config::get('services.rabbitmq.bind');

            $this->rabbitMQService->declareExchangeQueueBind($exchange, $queue, $bind);

            $this->rabbitMQService->consumeMessage($queue, function ($message) {
                $sales = json_decode($message->body, true);

                if (!$sales) {
                    Log::channel('seller_microservice')->error('Mensagem inválida recebida do RabbitMQ', ['message' => $message->body] );
                    return;
                }

                try {
                    $email = Config::get('services.rabbitmq.email_report_sales');
                    Mail::to($email)->send(new SendEmailSales($sales));

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
