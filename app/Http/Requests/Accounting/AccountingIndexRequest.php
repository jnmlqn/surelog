<?php

namespace App\Http\Requests\Accounting;

use Pearl\RequestValidate\RequestAbstract;

class AccountingIndexRequest extends RequestAbstract
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
            'departmentId' => 'nullable',
            'computationType' => 'required|in:1_cutoff,daily,2_cutoffs',
            'projectId' => 'nullable',
            'sortBy' => 'nullable',
            'sorting' => 'nullable',
            'employeeId' => 'nullable'
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
            'required' => ':attribute field is required'
        ];
    }
}
