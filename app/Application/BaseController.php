<?php

namespace App\Application;

use App\Enums\StatusCodeEnum;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Exceptions\CustomException;

class BaseController extends Controller
{
    /**
     * Retorna uma resposta JSON padronizada.
     *
     * @param StatusCodeEnum $statusCodeEnum
     * @param mixed $data
     * @param string|null $error
     * @param string|null $message
     * @return JsonResponse
     */
    protected function responseJson(StatusCodeEnum $statusCodeEnum, $data = null): JsonResponse
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
    public function responseJsonError(CustomException $e): JsonResponse
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
}
