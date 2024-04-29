<?php

namespace App\Http\Requests\Accounting;

use Pearl\RequestValidate\RequestAbstract;

class AccountingUpdateRequest extends RequestAbstract
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'dateFrom' => 'required',
            'dateTo' => 'required',
            'userId' => 'required',
            'computationType' => 'required|in:1_cutoff,daily,2_cutoffs',
            'projectId' => 'nullable',
            'inputs' => 'required',
            'inputs.backpay' => 'required|numeric',
            'inputs.pot_pay' => 'required|numeric',
            'inputs.sss' => 'required|numeric',
            'inputs.pagibig' => 'required|numeric',
            'inputs.philhealth' => 'required|numeric',
            'inputs.sss_loan' => 'required|numeric',
            'inputs.pagibig_loan' => 'required|numeric',
            'inputs.out_of_office' => 'required|numeric',
            'inputs.cash_advance' => 'required|numeric'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute field is required',
            'numeric' => ':attribute must be a number'
        ];
    }
}
