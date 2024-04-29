<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;

class ProjectAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startDate = date('Y-m-d', strtotime('2022-02-26'));
        $endDate = date('Y-m-d', strtotime('2022-03-10'));

        for ($i=0; $i < 100; $i++) { 
            $newDate = date('Y-m-d', strtotime($startDate . "+$i day"));
            $dates[] = $newDate;

            if ($newDate == $endDate) {
                foreach ($dates as $key => $value) {
                    Attendance::create([
                        'user_id' => '51b4ed3c-0264-47c0-a803-4ace91649e8f',
                        'project_id' => null,
                        'date' => $value,
                        'official_time_in' => "$value 07:00",
                        'official_time_out' => "$value 13:00",
                        'time_in' => "$value 07:00",
                        'time_out' => "$value 13:00",
                        'is_absent' => 0,
                        'on_leave' => 0,
                        'on_half_leave' => 0,
                        'is_half_day' => 0,
                        'day_type' => 'regular',
                        'time_in_image' => null,
                        'time_out_image' => null,
                        'time_in_latitude' => null,
                        'time_in_longitude' => null,
                        'time_out_latitude' => null,
                        'time_out_longitude' => null,
                        'override' => 0,
                        'override_in_reason' => null,
                        'override_out_reason' => null,
                        'created_by' => '51b4ed3c-0264-47c0-a803-4ace91649e8f',
                    ]);
                }
            }
        }        
    }
}
