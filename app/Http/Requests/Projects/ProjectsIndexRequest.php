<?php

namespace App\Http\Requests\Projects;

use Pearl\RequestValidate\RequestAbstract;

class ProjectsIndexRequest extends RequestAbstract
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
            'limit' => '',
            'sort_by' => '',
            'sorting' => '',
            'get' => '',
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
