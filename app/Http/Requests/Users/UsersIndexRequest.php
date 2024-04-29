<?php

namespace App\Http\Requests\Users;

use Pearl\RequestValidate\RequestAbstract;

class UsersIndexRequest extends RequestAbstract
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
            'keyword' => '',
            'department_id' => '',
            'role_id' => '',
            'limit' => '',
            'sort_by' => '',
            'sorting' => ''
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

        ];
    }
}
