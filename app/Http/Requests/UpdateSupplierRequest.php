<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
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
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $balance = $this->input('balance');
        $paid = $this->input('paid');

        $this->merge([
            'adress' => $this->input('adress', $this->input('address')),
            'balance' => ($balance === '') ? null : $balance,
            'paid' => ($paid === '') ? null : $paid,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ID' => 'required|integer|exists:supplyers,id',
            'cat' => 'nullable|integer|in:-1,0,1',
            'name' => 'required|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:191',
            'adress' => 'nullable|string|max:255',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $balance = $this->input('balance');
            $paid = $this->input('paid');

            if ($balance !== null && $paid !== null && is_numeric($balance) && is_numeric($paid)) {
                if ((float) $paid > (float) $balance) {
                    $validator->errors()->add('paid', 'The paid amount may not be greater than the balance.');
                }
            }
        });
    }
}
