<?php

namespace App\Http\Requests\Posts;

use Pearl\RequestValidate\RequestAbstract;

class PostsIndexRequest extends RequestAbstract
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
            'dateFrom' => '',
            'dateTo' => '',
            'departmentId' => '',
            'projectId' => '',
            'sortBy' => '',
            'sorting' => '',
            'limit' => ''
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
