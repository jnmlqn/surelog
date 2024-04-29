<?php

namespace App\Http\Requests\Accounting;

use Pearl\RequestValidate\RequestAbstract;

class AccountingBulkUpdateRequest extends RequestAbstract
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
            'computationType' => 'required|in:1_cutoff,daily,2_cutoffs',
            'projectId' => 'nullable',
            'employees' => 'required|array',
            'employees.*.userId' => 'required',
            'employees.*.inputs' => 'required',
            'employees.*.inputs.backpay' => 'required|numeric',
            'employees.*.inputs.pot_pay' => 'required|numeric',
            'employees.*.inputs.sss' => 'required|numeric',
            'employees.*.inputs.pagibig' => 'required|numeric',
            'employees.*.inputs.philhealth' => 'required|numeric',
            'employees.*.inputs.sss_loan' => 'required|numeric',
            'employees.*.inputs.pagibig_loan' => 'required|numeric',
            'employees.*.inputs.out_of_office' => 'required|numeric',
            'employees.*.inputs.cash_advance' => 'required|numeric'
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
