<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    // may change in other companies
    const HALF_DAY_MINS = 240;
    const DAYS_PER_MONTH = 26;
    const HOURS_PER_DAY = 8;
    //

    const NIGHT_DIFFERENTIAL = [
        'from' => '22:00:00',
        'to' => '06:00:00'        
    ];

    const DECIMAL_PRECISION = 2;

    protected $table = 'attendance';

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    protected $fillable = [
        'user_id',
        'project_id',
        'date',
        'official_time_in',
        'official_time_out',
        'time_in',
        'time_out',
        'is_restday',
        'is_absent',
        'on_leave',
        'on_half_leave',
        'is_half_day',
        'day_type',
        'time_in_image',
        'time_out_image',
        'time_in_latitude',
        'time_in_longitude',
        'time_out_latitude',
        'time_out_longitude',
        'override',
        'override_in_reason',
        'override_out_reason',
        'created_by'
    ];

    protected $casts = [
        'project_id' => 'string',
        'user_id' => 'string',
        'is_absent' => 'integer',
        'on_leave' => 'integer',
        'on_half_leave' => 'integer',
        'is_half_day' => 'integer',
        'override' => 'integer'
    ];

    protected $appends = [
        'absent_mins',
        'undertime_mins',
        'late_mins',
        'night_mins',
        'total_absences'
    ];

    public function overtimes()
    {
        return $this->hasMany(Overtime::class, 'date', 'date');
    }

    public function userId()
    {
        return $this->hasOne(User::class);
    }

    public function getTimeInImage($value)
    {
        if (is_null($value) || empty($value)) {
            return $value;
        }

        return url($value);
    }

    public function getTimeOutImage($value)
    {
        if (is_null($value) || empty($value)) {
            return $value;
        }

        return url($value);
    }

    public function getIsAbsentAttribute($value)
    {
        if ($value == 1) {
            return $value;
        }
        
        if (empty($this->time_in) && empty($this->time_out) && !$this->is_restday) {
            return 1;
        }

        return $value;
    }

    public function getAbsentMinsAttribute(): float
    {
        if ($this->is_restday) {
            return 0;
        } else {
            if ((empty($this->time_in) && empty($this->time_out)) || $this->is_absent) {
                $absentMins = $this->computeTimeDifference($this->official_time_in, $this->official_time_out) - 60; // deduct 60 mins, for breaktime
                return $absentMins < 0 ? 0 : $absentMins;
            }
        }

        return 0;
    }

    public function getUndertimeMinsAttribute(): float
    {
        if ($this->is_restday) {
            return 0;
        } else {
            if (empty($this->time_out)) {
                return self::HALF_DAY_MINS;
            } else {
                return $this->computeTimeDifference($this->official_time_out, $this->time_out);
            }
        }

        return 0;
    }

    public function getLateMinsAttribute(): float
    {
        if ($this->is_restday) {
            return 0;
        } else {
            if (empty($this->time_in)) {
                return self::HALF_DAY_MINS;
            } else {
                return $this->computeTimeDifference($this->time_in, $this->official_time_in);
            }
        }

        return 0;
    }

    public function getNightMinsAttribute(): float
    {
        if ($this->is_absent || (empty($this->time_in) && empty($this->time_out))) return 0;

        $nightDiffSchedule = [
            'from' => $this->date . ' ' . self::NIGHT_DIFFERENTIAL['from'],
            'to' => date('Y-m-d', strtotime($this->date . ' + 1 days')) . ' ' . self::NIGHT_DIFFERENTIAL['to']
        ];

        if (date('Y-m-d', strtotime($this->official_time_out)) > date('Y-m-d', strtotime($this->official_time_in))) {
            if (($this->time_in ?? $this->official_time_in) > $nightDiffSchedule['from']) {
                $nightDiffSchedule['from'] = $this->official_time_in;
            }

            if (($this->time_out ?? $this->official_time_out) < $nightDiffSchedule['to']) {
                $nightDiffSchedule['to'] = $this->official_time_out;
            }

            $nightMins = $this->computeTimeDifference($nightDiffSchedule['to'], $nightDiffSchedule['from']);

            if (empty($this->time_in) && empty($this->time_out)) {
                return 0;
            }

            if (empty($this->time_in) || empty($this->time_out)) {
                return $nightMins - self::HALF_DAY_MINS;
            }
        }

        if (
            $this->official_time_out >= $nightDiffSchedule['from'] &&
            $this->official_time_out <= $nightDiffSchedule['to']
        ) {
            return $this->computeTimeDifference(
                $this->time_out > $this->official_time_out
                    ? $this->official_time_out
                    : $this->time_out, 
                $nightDiffSchedule['from']
            );
        }

        return 0;
    }

    public function getTotalAbsencesAttribute()
    {
        return $this->absent_mins + $this->late_mins + $this->undertime_mins;
    }

    private function computeTimeDifference(string $dt1, string $dt2): float
    {
        $dt1 = date_create($dt1);
        $dt2 = date_create($dt2);

        if ($dt1 < $dt2) return 0;

        $difference = date_diff($dt1, $dt2);
        $difference = ($difference->h * 60) + $difference->i;

        return $difference < 0
            ? 0
            : $difference;
    }

    public static function getEmployeeAttendanceById(
        string $employeeId,
        string $dateFrom,
        string $dateTo
    ) {
        return self::with(['overtimes' => function ($q) use ($employeeId, $dateFrom, $dateTo) {
                $q->where('user_id', $employeeId)
                    ->whereNotNull('approved_by')
                    ->select(
                        'id',
                        'date',
                        'approved_ot_hours',
                        'approved_holiday_hours',
                        'approved_night_ot_hours'
                    );
            }])
            ->where('user_id', $employeeId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'DESC')
            ->get();
    }

    public static function getEmployeeAttendanceByDate(string $employeeId, string $date): ?Attendance
    {
        return self::where('user_id', $employeeId)->where('date', $date)->first();
    }

    public static function getAttendanceCount(string $userId, string $dateFrom, string $dateTo): int
    {
        return Attendance::whereBetween('date', [$dateFrom, $dateTo])
            ->where('user_id', $userId)
            ->where('is_restday', 0)
            ->count();
    }
}
