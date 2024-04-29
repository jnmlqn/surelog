<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceHistory extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'user_id',
        'project_id',
        'rate',
        'daily_rate',
        'ot_pay',
        'total_absences',
        'net_absences',
        'grosspay',
        'net_pay',
        'backpay',
        'cash_advance',
        'ecola',
        'pot_pay',
        'taxable_allowance',
        'sss',
        'pagibig',
        'philhealth',
        'sss_loan',
        'pagibig_loan',
        'tax',
        'out_of_office',
        'net_out_of_office',
        'total_deductions',
        'date_from',
        'date_to',
        'computation_type',
        'taxable_income'
    ];

    protected $casts = [
        'user_id' => 'string',
        'project_id' => 'string'
    ];

    public static function saveHistory(array $data): void
    {
        AttendanceHistory::updateOrCreate(
            [
                'date_from' => $data['date_from'],
                'date_to' => $data['date_to'],
                'user_id' => $data['user_id'],
                'computation_type' => $data['computation_type']
            ],
            $data
        );
    }
}
