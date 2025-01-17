<?php

namespace App\Services;

use App\Enums\StatusCodeEnum;
use Illuminate\Http\JsonResponse;
use App\Exceptions\CustomException;

class ResponseService
{
    /**
     * Retorna uma resposta JSON padronizada.
     *
     * @param StatusCodeEnum $statusCodeEnum
     * @param mixed $data
     * @return JsonResponse
     */
    public static function responseJson(StatusCodeEnum $statusCodeEnum, $data = null): JsonResponse
    {
        $response = [
            'status' => $statusCodeEnum->message(),
            'data' => $data,
        ];

        return response()->json($response, $statusCodeEnum->value);
    }

    /**
     * Retorna uma resposta JSON padronizada para erros.
     *
     * @param CustomException $e
     * @return JsonResponse
     */
    public static function responseJsonError(CustomException $e): JsonResponse
    {
        $statusCodeEnum = method_exists($e, 'getStatusCodeEnum')
            ? $e->getStatusCodeEnum()
            : StatusCodeEnum::INTERNAL_SERVER_ERROR;

        return response()->json(
            [
                'status' => $statusCodeEnum->message(),
                'error' => $statusCodeEnum->name,
                'message' => $e->getMessage() ?? 'Erro inesperado.',
            ],
            $statusCodeEnum->value
        );
    }

    /**
     * Retorna uma resposta de objeto padronizada para erros.
     *
     * @param CustomException $e
     * @return object
     */
    public static function responseError(CustomException $e): object
    {
        $statusCodeEnum = method_exists($e, 'getStatusCodeEnum')
            ? $e->getStatusCodeEnum()
            : StatusCodeEnum::INTERNAL_SERVER_ERROR;

        return (object) [
            [
                'status' => $statusCodeEnum->message(),
                'error' => $statusCodeEnum->name,
                'message' => $e->getMessage() ?? 'Erro inesperado.',
            ],
            $statusCodeEnum->value
        ];
    }

    /**
     * Retorna uma resposta padronizada.
     *
     * @param StatusCodeEnum $statusCodeEnum
     * @param mixed $data
     * @return object
     */
    public static function response(StatusCodeEnum $statusCodeEnum, $data = null): object
    {
        $response = (object) [
            'status' => $statusCodeEnum->message(),
            'data' => '',
        ];

        $response->data = $data;

        return $response;
    }
}
