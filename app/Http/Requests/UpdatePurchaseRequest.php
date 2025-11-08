<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdatePurchaseRequest extends StorePurchaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'products' => ['required', 'array', 'min:1'],
            'products.*.product' => ['required', 'string', 'max:255'],
            'products.*.price' => ['required', 'numeric', 'min:0'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.color' => ['nullable', 'string', 'max:255'],
            'products.*.size' => ['nullable', 'string', 'max:255'],
            'products.*.Brand' => ['required', 'integer', Rule::exists('products_brand', 'ID')],
            'products.*.style' => ['required', 'integer', Rule::exists('productstyles', 'id')],
            'supplier_id' => ['nullable', 'integer', Rule::exists('supplyers', 'id')],
            'box_id' => ['required', 'integer', Rule::exists('purchase', 'boxID')],
        ];
    }
}
