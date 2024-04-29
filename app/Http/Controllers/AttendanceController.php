<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\AttendanceCreateRequest;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Overtime;
use App\Traits\ApiResponser;
use App\Traits\AuditTrail;
use Illuminate\Http\Response;

class AttendanceController extends Controller
{
    use ApiResponser;
    
    use AuditTrail;

    /**
     * @param App\Http\Requests\Attendance\AttendanceCreateRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function create(AttendanceCreateRequest $request): Response
    {
        $data = $request->validated();

        $date = $data['date'];
        $user_id = $data['user_id'];
        $project_id = $data ['project_id'];
        $location = $data['location'];
        $attendance = $data['attendance'];
        $leaves = $data['leaves'];
        $overtimes = $data['overtimes'];
        $dar = $data['dar'];

        $attendance_log = AttendanceLog::updateOrCreate(
            [
                'date' => $date,
                'project_id' => $project_id,
                'user_id' => $user_id,
            ],
            [
                'date' => $date,
                'project_id' => $project_id,
                'user_id' => $user_id,
                'locations' => $location,
                'dar' => $dar,
            ]
        );

        foreach($attendance as $att) {
            $att['date'] = $date;
            $att['official_time_in'] = "$date {$att['official_time_in']}";

            if ($att['official_time_in'] > $att['official_time_out']) { // if night shift
                $tomorrowDate = date('Y-m-d', strtotime($date . ' + 1 days'));
                $att['official_time_out'] = "$tomorrowDate {$att['official_time_out']}";
            } else {
                $att['official_time_out'] = "$date {$att['official_time_out']}";
            }

            Attendance::updateOrCreate(
                [
                    'date' => $att['date'],
                    'user_id' => $att['user_id'],
                    'project_id' => $att['project_id'],
                    'created_by' => $att['created_by']
                ],
                $att
            );
        }

        foreach($leaves as $leave) {
            $leave['date'] = $date;
            $leave['official_time_in'] = "$date {$leave['official_time_in']}";
            $leave['official_time_out'] = "$date {$leave['official_time_out']}";

            Attendance::updateOrCreate(
                [
                    'date' => $leave['date'],
                    'user_id' => $leave['user_id'],
                    'project_id' => $leave['project_id'],
                    'created_by' => $leave['created_by']
                ],
                $leave
            );
        }

        foreach($overtimes as $overtime) {
            $overtime['date'] = $date;
            
            Overtime::updateOrCreate(
                [
                    'date' => $overtime['date'],
                    'user_id' => $overtime['user_id'],
                    'project_id' => $overtime['project_id'],
                    'created_by' => $overtime['created_by']
                ],
                $overtime
            );
        }

        $this->saveLogs(
            'Attendance',
            $data,
            'Uploaded attendance data'
        );

        return $this->apiResponse(
            'Attendance data were successfully uploaded',
            null,
            201
        );
    }
}
