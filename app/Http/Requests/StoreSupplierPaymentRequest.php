<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierPaymentRequest extends FormRequest
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
        $payload = $this->input('data');

        if (is_array($payload)) {
            $this->merge([
                'amount' => $payload['newpaid'] ?? $this->input('amount'),
                'supplyer_id' => $payload['supplyerID'] ?? $this->input('supplyer_id'),
                'payment_method_id' => $payload['paymentMethod'] ?? $this->input('payment_method_id'),
                'box_id' => $payload['ID'] ?? $this->input('box_id'),
                'remarks' => $payload['remarks'] ?? $this->input('remarks'),
                'previous_paid' => $payload['paid'] ?? $this->input('previous_paid'),
                'total' => $payload['total'] ?? $this->input('total'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:1',
            'supplyer_id' => 'required|integer|exists:supplyers,id',
            'payment_method_id' => 'nullable|integer|exists:paymentMethods,ID',
            'box_id' => 'required|integer|exists:purchase,boxID',
            'remarks' => 'nullable|string|max:255',
            'previous_paid' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
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
            $amount = $this->input('amount');
            $previousPaid = $this->input('previous_paid', 0);
            $total = $this->input('total');

            if ($amount !== null && $total !== null && is_numeric($amount) && is_numeric($total)) {
                $currentTotal = (float) $amount + (float) $previousPaid;

                if ($currentTotal - (float) $total > 0.00001) {
                    $validator->errors()->add('amount', 'The payment exceeds the outstanding balance.');
                }
            }
        });
    }
}
