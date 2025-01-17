<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
    /**
     * Autorizando o request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Valida os parametros da venda
     */
    public function rules(): array
    {
        return [
            'seller_id' => 'required|exists:sellers,id',
            'sale_value' => 'required|numeric|min:0.01',
        ];
    }

    /**
     * Retorna os erros de validação
     */
    public function messages(): array
    {
        return [
            'seller_id.required' => 'O vendedor é obrigatório.',
            'seller_id.exists' => 'O vendedor informado não existe.',
            'sale_value.required' => 'O valor da venda é obrigatório.',
            'sale_value.numeric' => 'O valor da venda deve ser numérico.',
            'sale_value.min' => 'O valor da venda deve ser maior que zero.',
        ];
    }
}
