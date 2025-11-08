<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $products = $this->input('products');

        if (is_array($products)) {
            return;
        }

        $payload = $this->input('data1');

        if (! is_array($payload) || empty($payload)) {
            return;
        }

        $meta = Arr::last($payload);
        $hasMeta = is_array($meta) && (array_key_exists('boxID', $meta) || array_key_exists('supplyer', $meta));
        $productRows = $hasMeta ? array_slice($payload, 0, -1) : $payload;

        $this->merge([
            'products' => $productRows,
            'supplier_id' => $this->input('supplier_id', $meta['supplyer'] ?? null),
            'box_id' => $this->input('box_id', $meta['boxID'] ?? null),
        ]);
    }

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
            'supplier_id' => ['required', 'integer', Rule::exists('supplyers', 'id')],
            'box_id' => ['required', 'integer', Rule::unique('purchase', 'boxID')],
        ];
    }

    /**
     * Provide custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'products.*.Brand.exists' => 'The selected brand is invalid.',
            'products.*.style.exists' => 'The selected style is invalid.',
        ];
    }
}
