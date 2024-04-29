<?php

namespace App\Http\Requests\Users;

use Pearl\RequestValidate\RequestAbstract;

class UsersCreateRequest extends RequestAbstract
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
            'employee_id' => 'nullable',
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'extension' => 'nullable',
            'email' => 'required|email',
            'password' => 'nullable',
            'birthday' => 'nullable|date',
            'position' => 'nullable|string|max:100',
            'image' => 'nullable',
            'mobile' => 'nullable|string|min:9|max:15',
            'tin' => 'nullable|string|max:25',
            'sss_number' => 'nullable|string|max:25',
            'pagibig_number' => 'nullable|string|max:25',
            'philhealth_number' => 'nullable|string|max:25',
            'rate' => 'nullable',
            'taxable_allowance' => 'nullable',
            'employment_type_id' => 'required',
            'department_id' => 'required',
            'civil_status_id' => 'required',
            'role_id' => 'required',
            'address' => 'nullable',
            'province_id' => 'required',
            'city_id' => 'required',
            'zipcode_id' => 'required',
            'monday_in' => 'nullable',
            'monday_out' => 'nullable',
            'tuesday_in' => 'nullable',
            'tuesday_out' => 'nullable',
            'wednesday_in' => 'nullable',
            'wednesday_out' => 'nullable',
            'thursday_in' => 'nullable',
            'thursday_out' => 'nullable',
            'friday_in' => 'nullable',
            'friday_out' => 'nullable',
            'saturday_in' => 'nullable',
            'saturday_out' => 'nullable',
            'sunday_in' => 'nullable',
            'sunday_out' => 'nullable',
            'office_schedule' => 'required|boolean',
            'supervisor' => 'nullable|string|max:36'
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
            'email' => ':attribute must be a valid email address'
        ];
    }
}
