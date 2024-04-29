<?php

namespace App\Http\Requests\Projects;

use Pearl\RequestValidate\RequestAbstract;
use App\Rules\ProjectSchedules;

class ProjectsCreateRequest extends RequestAbstract
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
            'name' => 'required',
            'description' => 'required',
            'location' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'department_id' => 'required',
            'offset' => 'nullable',
            'project_authorities' => 'array',
            'project_members' => 'array',
            'project_schedules' => new ProjectSchedules()
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
            'date' => ':attribute must be a valid date',
            'array' => ':attribute must be a valid array'
        ];
    }
}
