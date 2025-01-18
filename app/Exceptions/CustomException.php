<?php

namespace App\Exceptions;

use App\Enums\StatusCodeEnum;
use Exception;

/**
 * Classe base para exceções personalizadas com suporte a tipos de status.
 */
class CustomException extends Exception
{
    /**
     * O tipo de status associado à exceção.
     *
     * @var StatusCodeEnum
     */
    private StatusCodeEnum $StatusCodeEnum;

    /**
     * Construtor da exceção personalizada.
     *
     * @param StatusCodeEnum $StatusCodeEnum O tipo de status associado.
     * @param string|null $message Mensagem opcional (pode ser nula).
     */
    public function __construct(StatusCodeEnum $StatusCodeEnum, string $message = null)
    {
        $this->StatusCodeEnum = $StatusCodeEnum;
        $messageToUse = $message ?? $StatusCodeEnum->message();

        parent::__construct($messageToUse, $StatusCodeEnum->value);
    }

    /**
     * Obtém o tipo de status associado à exceção.
     *
     * @return StatusCodeEnum
     */
    public function getStatusCodeEnum(): StatusCodeEnum
    {
        return $this->StatusCodeEnum;
    }

    /**
     * Método para pegar a mensagem original do erro (caso você queira).
     * Descomente esta linha para acessar a mensagem original.
     *
     * @return string
     */
    public function getOriginalMessage(): string
    {
        // Descomente a linha abaixo se quiser pegar a mensagem original do erro
        // return $this->getMessage();

        return $this->getStatusCodeEnum()->message();
    }
}

