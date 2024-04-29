<?php

namespace App\Helpers;

use App\Models\Attendance;
use App\Models\AttendanceHistory;
use App\Models\Overtime;
use App\Models\PagibigContribution;
use App\Models\PhilhealthContribution;
use App\Models\Project;
use App\Models\SssContribution;
use App\Models\Tax;

class PayrollComputation
{
    /**
     * Compute employee attendance by employee ID
     * 
     * @param object $employee
     * @param string $dateFrom
     * @param string $dateTo
     * @param string $computationType
     * @param string|null $projectId
     * @param array $inputs
     * 
     * @return object
     */
    public function computeByEmployeeId(
        object $employee,
        string $dateFrom,
        string $dateTo,
        string $computationType,
        ?string $projectId = null,
        array $inputs = []
    ): object {
        $user = config('user');
        $grossPay = 0;
        $dailyRate = 0;
        $payPerMinute = 0;
        $totalDays = date_diff(date_create($dateFrom), date_create($dateTo))->d + 1;

        for ($i=0; $i < $totalDays; $i++) {
            $date = date('Y-m-d', strtotime($dateFrom . " +$i days"));
            $exist = Attendance::getEmployeeAttendanceByDate($employee['id'], $date);
            if (!$exist) {
                $this->generateAbsent($date, $employee, $user, $projectId);
            }
        }

        if ($computationType == '1_cutoff') {
            $grossPay = $employee->rate + $employee->taxable_allowance;
            $dailyRate = $grossPay/Attendance::DAYS_PER_MONTH;
        } elseif ($computationType == '2_cutoffs') {
            $grossPay = $employee->rate/2 + (
                $employee->taxable_allowance > 0
                    ? $employee->taxable_allowance/2
                    : 0
            );
            $dailyRate = $grossPay/(Attendance::DAYS_PER_MONTH/2);
        } else if ($computationType == 'daily') {
            $grossPay = $employee->rate + $employee->taxable_allowance;
            $dailyRate = $grossPay/Attendance::DAYS_PER_MONTH;
            $totalDays = Attendance::getAttendanceCount($employee['id'], $dateFrom, $dateTo);
            $grossPay = $dailyRate * $totalDays;
            $employee['total_days'] = $totalDays;
        }

        $payPerMinute = $dailyRate/Attendance::HOURS_PER_DAY/60;

        $employmentTypeRates = $employee->employmentTypeId;

        $previousPayroll = AttendanceHistory::where('user_id', $employee['id'])
            ->where(function ($q) use ($dateFrom) {
                $q->where('date_to', date('Y-m-d', strtotime($dateFrom . " -1 days")))
                    ->orWhere('date_to', $dateFrom);
            })
            ->where('computation_type', $computationType)
            ->first();

        $history = AttendanceHistory::where('user_id', $employee['id'])
            ->where('date_from', $dateFrom)
            ->where('date_to', $dateTo)
            ->where('computation_type', $computationType)
            ->first();

        $attendance = Attendance::getEmployeeAttendanceById($employee['id'], $dateFrom, $dateTo);

        foreach ($attendance as $key => $value) {
            $value['approved_ot_hours'] = $value->overtimes->sum('approved_ot_hours');
            $value['approved_holiday_hours'] = $value->overtimes->sum('approved_holiday_hours');
            $value['approved_night_ot_hours'] = $value->overtimes->sum('approved_night_ot_hours');

            $otrate = 1;
            $holidayRate = 1;
            $holidayOtRate = 1;
            $restDayRate = 1;
            $restDayOtRate = 1;
            $nightDiffRate = $employmentTypeRates->night_diff_rate;

            switch ($value->day_type) {
                case 'regular':
                    $otRate = $employmentTypeRates->regular_ot_rate;
                    break;

                case 'legal':
                    $holidayRate = $employmentTypeRates->legal_hol_rate;
                    $holidayOtRate = $employmentTypeRates->legal_hol_ot_rate;
                    break;

                case 'special':
                    $holidayRate = $employmentTypeRates->special_hol_rate;
                    $holidayOtRate = $employmentTypeRates->special_hol_ot_rate;
                    break;
            }

            if ($value->is_restday) {
                $otRate = 1;
                $restDayRate = $employmentTypeRates->restday_rate;
                $restDayOtRate = $employmentTypeRates->restday_ot_rate;
            }

            $otPay = $value['approved_ot_hours'] * 60 * $payPerMinute * $otRate * $holidayOtRate * $restDayOtRate;
            $holidayPay = $value['approved_holiday_hours'] * 60 * $payPerMinute * $holidayRate * $restDayRate;
            $nightOtPay = $value['approved_night_ot_hours'] * 60 * $payPerMinute * $holidayOtRate * $nightDiffRate;
            $nightPay = $value['night_mins'] * $payPerMinute * $nightDiffRate;
            $value['total_ot_pay'] = $otPay + $holidayPay + $nightOtPay + $nightPay;
        }

        $employee['backpay'] = $this->checkNegative($inputs['backpay'] ?? ($history->backpay ?? 0));
        $employee['pot_pay'] = $this->checkNegative($inputs['pot_pay'] ?? ($history->pot_pay ?? 0));
        $employee['sss'] = $this->checkNegative($inputs['sss'] ?? ($history->sss ?? 0));
        $employee['pagibig'] = $this->checkNegative($inputs['pagibig'] ?? ($history->pagibig ?? 0));
        $employee['philhealth'] = $this->checkNegative($inputs['philhealth'] ?? ($history->philhealth ?? 0));
        $employee['sss_loan'] = $this->checkNegative($inputs['sss_loan'] ?? ($history->sss_loan ?? 0));
        $employee['pagibig_loan'] = $this->checkNegative($inputs['pagibig_loan'] ?? ($history->pagibig_loan ?? 0));
        $employee['out_of_office'] = $this->checkNegative($inputs['out_of_office'] ?? ($history->out_of_office ?? 0));
        $employee['cash_advance'] = $this->checkNegative($inputs['cash_advance'] ?? ($history->cash_advance ?? 0));
        $employee['net_out_of_office'] = $this->checkNegative($employee['out_of_office'] * $payPerMinute);

        $employee['daily_rate'] = number_format($dailyRate, 2);

        $ot_pay = $attendance->sum('total_ot_pay');
        $employee['ot_pay'] = number_format($ot_pay, 2);

        $employee['absent_mins'] = $attendance->sum('absent_mins');
        $employee['late_mins'] = $attendance->sum('late_mins');
        $employee['undertime_mins'] = $attendance->sum('undertime_mins');
        $employee['total_absences'] = $attendance->sum('total_absences');

        $net_absences = $this->checkNegative($attendance->sum('total_absences') * $payPerMinute);
        $employee['net_absences'] = number_format($net_absences, 2);

        $grossPay = $grossPay + $ot_pay + $employee['backpay'] + $employee['pot_pay']  - $net_absences;
        $employee['grosspay'] = number_format($grossPay, 2);

        // This is for government mandated benefits
        // SSS is based on gross income for a month. Philhealth and Pagibig is based on basic pay
        $sssContribution = SssContribution::getDeduction($grossPay + ($previousPayroll->grosspay ?? 0));
        $pagibigContribution = PagibigContribution::getDeduction($employee->rate);
        $philhealthContribution = PhilhealthContribution::getDeduction($employee->rate);

        $employee['sss_deduction'] = $sssContribution;
        $employee['pagibig_deduction'] = $pagibigContribution;
        $employee['philhealth_deduction'] = $philhealthContribution;

        $employee['sss_check'] = $employee['sss'] > 0;
        $employee['pagibig_check'] = $employee['pagibig'] > 0;
        $employee['philhealth_check'] = $employee['philhealth'] > 0;

        $taxableIncomeDeductions = $employee['sss'] + 
            $employee['pagibig'] +
            $employee['philhealth'] +
            $employee['sss_loan'] +
            $employee['pagibig_loan'] +
            ($employee['out_of_office'] * $payPerMinute);


        $taxableIncome = $this->checkNegative($grossPay - $taxableIncomeDeductions);
        $tax = Tax::getTax($taxableIncome + ($history->taxable_income ?? 0)); // Monthly
        $employee['tax'] = number_format($tax, 2);

        $totaDeductions = $this->checkNegative(
            $taxableIncomeDeductions +
            $tax + 
            $employee['cash_advance']
        );

        $employee['total_deductions'] = number_format($totaDeductions, 2);
        $net_pay = $this->checkNegative($grossPay - $totaDeductions);
        $employee['net_pay'] = number_format($net_pay, 2);
        $employee['attendance'] = $attendance;

        AttendanceHistory::saveHistory([
            'user_id' => $employee['id'],
            'project_id' => $projectId,
            'rate' => $employee->rate,
            'daily_rate' => $dailyRate,
            'ot_pay' => $ot_pay,
            'total_absences' => $employee['total_absences'],
            'net_absences' => $net_absences,
            'grosspay' => $grossPay,
            'net_pay' => $net_pay,
            'backpay' => $employee['backpay'],
            'cash_advance' => $employee['cash_advance'],
            'ecola' => $employee['ecola'] ?? 0,
            'pot_pay' => $employee['pot_pay'],
            'taxable_allowance' => $employee->taxable_allowance ?? 0,
            'sss' => $employee['sss'],
            'pagibig' => $employee['pagibig'],
            'philhealth' => $employee['philhealth'],
            'sss_loan' => $employee['sss_loan'],
            'pagibig_loan' => $employee['pagibig_loan'],
            'tax' => 0,
            'out_of_office' => $employee['out_of_office'],
            'net_out_of_office' => $employee['net_out_of_office'],
            'total_deductions' => $totaDeductions,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'computation_type' => $computationType,
            'taxable_income' => $taxableIncome
        ]);

        return $employee;
    }

    /**
     * Generate absent if no record for the day
     * 
     * @param string $date
     * @param object $employee
     * @param array $user
     * @param string|null $projectId
     * 
     * @return void
     */
    public function generateAbsent(
        string $date,
        object $employee,
        array $user,
        ?string $projectId = null
    ): void {
        $schedule = $projectId
            ? Project::where('id', $projectId)
                ->first()
                ->project_schedules[0] ?? null
            : $employee->officeSchedule ?? null;

        $restday = 0;
        $absent = 1;
        switch (date('w', strtotime($date))) {
            case 0: // sunday
                $timeIn = $projectId ? $schedule->sunday_in : ($schedule['time_in'] ?? '00:00:00');
                $timeOut = $projectId ? $schedule->sunday_out : ($schedule['time_out'] ?? '23:59:00');
                $restday = 1;
                $absent = 0;
                break;

            case 1: // monday
                $timeIn = $projectId ? $schedule->monday_in : ($schedule['time_in'] ?? '00:00:00');
                $timeOut = $projectId ? $schedule->monday_out : ($schedule['time_out'] ?? '23:59:00');
                break;
                
            case 2: // tuesday
                $timeIn = $projectId ? $schedule->tuesday_in : ($schedule['time_in'] ?? '00:00:00');
                $timeOut = $projectId ? $schedule->tuesday_out : ($schedule['time_out'] ?? '23:59:00');
                break;
                
            case 3: // wednesday
                $timeIn = $projectId ? $schedule->wednesday_in : ($schedule['time_in'] ?? '00:00:00');
                $timeOut = $projectId ? $schedule->wednesday_out : ($schedule['time_out'] ?? '23:59:00');
                break;
                
            case 4: // thursday
                $timeIn = $projectId ? $schedule->thursday_in : ($schedule['time_in'] ?? '00:00:00');
                $timeOut = $projectId ? $schedule->thursday_out : ($schedule['time_out'] ?? '23:59:00');
                break;
                
            case 5: // friday
                $timeIn = $projectId ? $schedule->friday_in : ($schedule['time_in'] ?? '00:00:00');
                $timeOut = $projectId ? $schedule->friday_out : ($schedule['time_out'] ?? '23:59:00');
                break;
                
            case 6: // saturday
                $timeIn = $projectId ? $schedule->saturday_in : ($schedule['time_in'] ?? '00:00:00');
                $timeOut = $projectId ? $schedule->saturday_out : ($schedule['time_out'] ?? '23:59:00');
                // $restday = 1; // TODO: uncomment if the company has no work during satruday
                // $absent = 0; // TODO: uncomment if the company has no work during satruday
                break;
        }

        Attendance::create([
            'user_id' => $employee['id'],
            'project_id' => $projectId ?? null,
            'date' => $date,
            'official_time_in' => $date .' '. $timeIn,
            'official_time_out' => $date .' '. $timeOut,
            'time_in' => null,
            'time_out' => null,
            'is_restday' => $restday,
            'is_absent' => $absent,
            'day_type' => 'regular',
            'created_by' => $user['id']
        ]);
    }

    /**
     * @param float $value
     * 
     * @return $float
     */
    private function checkNegative(float $value): float
    {
        if ($value < 0) return 0;

        return $value;
    }
}
