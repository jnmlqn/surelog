<?php

namespace App\Http\Requests\Attendance;

use Pearl\RequestValidate\RequestAbstract;

class AttendanceCreateRequest extends RequestAbstract
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
            'user_id' => 'required',
            'project_id' => 'nullable',
            'date' => 'required',

            'location' => 'required',
            'location.date' => 'required',
            'location.project_id' => 'nullable',
            'location.image' => 'required',
            'location.latitude' => 'required',
            'location.longitude' => 'required',
            'location.created_by' => 'required',

            'attendance' => 'nullable',
            'attendance.*.user_id' => 'required',
            'attendance.*.project_id' => 'nullable',
            'attendance.*.official_time_in' => 'required',
            'attendance.*.official_time_out' => 'required',
            'attendance.*.time_in' => 'required',
            'attendance.*.time_out' => 'nullable',
            'attendance.*.is_absent' => 'required',
            'attendance.*.on_leave' => 'required',
            'attendance.*.on_half_leave' => 'required',
            'attendance.*.is_half_day' => 'required',
            'attendance.*.day_type' => 'required',
            'attendance.*.time_in_image' => 'nullable',
            'attendance.*.time_out_image' => 'nullable',
            'attendance.*.time_in_latitude' => 'nullable',
            'attendance.*.time_in_longitude' => 'nullable',
            'attendance.*.time_out_latitude' => 'nullable',
            'attendance.*.time_out_longitude' => 'nullable',
            'attendance.*.override' => 'required',
            'attendance.*.override_in_reason' => 'nullable',
            'attendance.*.override_out_reason' => 'nullable',
            'attendance.*.created_by' => 'required',

            'leaves' => 'nullable',
            'leaves.*.user_id' => 'required',
            'leaves.*.project_id' => 'nullable',
            'leaves.*.official_time_in' => 'required',
            'leaves.*.official_time_out' => 'required',
            'leaves.*.time_in' => 'nullable',
            'leaves.*.time_out' => 'nullable',
            'leaves.*.is_absent' => 'required',
            'leaves.*.on_leave' => 'required',
            'leaves.*.on_half_leave' => 'required',
            'leaves.*.is_half_day' => 'required',
            'leaves.*.day_type' => 'required',
            'leaves.*.time_in_image' => 'nullable',
            'leaves.*.time_out_image' => 'nullable',
            'leaves.*.time_in_latitude' => 'nullable',
            'leaves.*.time_in_longitude' => 'nullable',
            'leaves.*.time_out_latitude' => 'nullable',
            'leaves.*.time_out_longitude' => 'nullable',
            'leaves.*.override' => 'required',
            'leaves.*.override_in_reason' => 'nullable',
            'leaves.*.override_out_reason' => 'nullable',
            'leaves.*.created_by' => 'required',

            'overtimes' => 'nullable',
            'overtimes.*.user_id' => 'required',
            'overtimes.*.name' => 'required',
            'overtimes.*.project_id' => 'nullable',
            'overtimes.*.time_in' => 'required',
            'overtimes.*.time_out' => 'nullable',
            'overtimes.*.created_by' => 'required',

            'dar' => 'nullable',
            'dar.weather_condition' => 'nullable',
            'dar.technical_issues' => 'nullable',
            'dar.equipment_issues' => 'nullable',
            'dar.remarks' => 'nullable',
            'dar.requests' => 'nullable',
            'dar.images' => 'nullable',
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
