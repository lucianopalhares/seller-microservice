<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

/**
 * Serviço para integração com RabbitMQ.
 * Gerencia a conexão, publicação e consumo de mensagens no RabbitMQ.
 */
class RabbitMQService
{
    /**
     * @var AMQPStreamConnection|null Conexão com o RabbitMQ.
     */
    private $connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel|null Canal para comunicação com o RabbitMQ.
     */
    private $channel;

    private $error = false;

    /**
     * Construtor da classe RabbitMQService.
     * Configura a conexão e o canal com RabbitMQ utilizando os dados do arquivo .env.
     *
     * @throws Exception Caso a conexão com RabbitMQ falhe.
     */
    public function __construct()
    {
        try {
            $host = Config::get('services.rabbitmq.host');
            $port = Config::get('services.rabbitmq.port');
            $user = Config::get('services.rabbitmq.user');
            $password = Config::get('services.rabbitmq.password');

            $this->connection = new AMQPStreamConnection($host, $port, $user, $password);

            $this->channel = $this->connection->channel();

            $this->error = false;
        } catch (Exception $e) {
            $this->error = true;
            Log::channel('seller_microservice')->error("Falha na conexão com o RabbitMQ: " . $e->getMessage());
        }
    }

    /**
     * Declara a exchange, fila e a vincula, sem causar erro se já existirem.
     *
     * @param string $exchange Nome da exchange.
     * @param string $queue Nome da fila.
     * @param string $routingKey Routing key para a fila.
     */
    public function declareExchangeQueueBind($exchange, $queue, $routingKey)
    {
        try {
            // Declare a exchange
            $this->channel->exchange_declare($exchange, 'direct', false, true, false);
            Log::info("Exchange '{$exchange}' declarada com sucesso.");

            // Declare a fila
            $this->channel->queue_declare($queue, false, true, false, false);
            Log::info("Fila '{$queue}' declarada com sucesso.");

            // Bind a fila à exchange com a routing key
            $this->channel->queue_bind($queue, $exchange, $routingKey);
            Log::info("Fila '{$queue}' vinculada à exchange '{$exchange}' com a chave de roteamento '{$routingKey}'.");

        } catch (Exception $e) {
            Log::error("Erro ao declarar a exchange, fila ou bind", [
                'error' => $e->getMessage(),
            ]);
        }
    }


    /**
     * Publica uma mensagem em uma exchange com uma routing key específica.
     *
     * @param string $queue Nome da queue.
     * @param string $routingKey Routing key para a mensagem.
     * @param string $message Conteúdo da mensagem.
     *
     * @throws Exception Caso a publicação da mensagem falhe.
     */
    public function publishMessage(string $queue, string $message): void
    {
        if ($this->error === true) return;

        try {
            $msg = new AMQPMessage($message);
            $this->channel->basic_publish($msg, '', $queue);

            $this->error = false;
        } catch (Exception $e) {
            $this->error = true;
            Log::channel('seller_microservice')->error("Falha ao publicar a mensagem no RabbitMQ: " . $e->getMessage());
        }
    }

    /**
     * Consome mensagens de uma fila específica e executa um callback para processar cada mensagem.
     *
     * @param string $queue Nome da fila.
     * @param callable $callback Função de callback que será executada ao receber uma mensagem.
     *
     * @throws Exception Caso ocorra um erro ao consumir mensagens.
     */
    public function consumeMessage(string $queue, callable $callback): void
    {
        if ($this->error === true) return;

        try {





            $this->channel->basic_consume($queue, '', false, true, false, false, $callback);

            $this->channel->consume();

            $this->error = false;
        } catch (Exception $e) {
            $this->error = true;
            Log::channel('seller_microservice')->error("Falha ao consumir a mensagem no RabbitMQ: " . $e->getMessage());
        }
    }

    /**
     * Fecha a conexão e o canal com o RabbitMQ.
     */
    public function closeConnection(): void
    {
        if ($this->channel) {
            $this->channel->close();
        }
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
