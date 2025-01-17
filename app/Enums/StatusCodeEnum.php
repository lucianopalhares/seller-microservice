<?php

namespace App\Enums;

/**
 * Enum que define tipos de código de status e mensagens padrão.
 */
enum StatusCodeEnum: int
{
    case OK = 200;
    case CREATED = 201;
    case NO_CONTENT = 204;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case INTERNAL_SERVER_ERROR = 500;
    case GONE = 410;

    /**
     * Obtém a mensagem padrão para o tipo de status.
     *
     * @return string
     */
    public function message(): string
    {
        return match ($this) {
            self::OK => 'Requisição bem-sucedida.',
            self::CREATED => 'Criado com sucesso.',
            self::NO_CONTENT => 'Sem conteúdo.',
            self::BAD_REQUEST => 'Requisição inválida.',
            self::UNAUTHORIZED => 'Não autorizado.',
            self::FORBIDDEN => 'Proibido.',
            self::NOT_FOUND => 'Recurso não encontrado.',
            self::INTERNAL_SERVER_ERROR => 'Erro interno no servidor.',
            self::GONE => 'Recurso removido permanentemente.',
        };
    }
}
