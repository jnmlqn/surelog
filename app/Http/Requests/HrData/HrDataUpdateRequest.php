<?php

namespace App\Http\Requests\HrData;

use Pearl\RequestValidate\RequestAbstract;

class HrDataUpdateRequest extends RequestAbstract
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
            'civil_statuses' => 'required',
            'departments' => 'required',
            'employment_types' => 'required',
            'sss_contributions' => 'required',
            'pagibig_contributions' => 'required',
            'philhealth_contributions' => 'required',
            'taxes' => 'required'
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
            'required' => ':attribute are required',
        ];
    }
}
