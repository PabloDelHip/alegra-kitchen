<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKitchenOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambiar a lógica de permisos si es necesario
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer'  => 'La cantidad debe ser un número entero.',
            'quantity.min'      => 'Debe solicitar al menos un platillo.',
        ];
    }
}
