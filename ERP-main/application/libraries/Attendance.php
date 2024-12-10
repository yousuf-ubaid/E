<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Attendance
{
    /**
     * Present type
     *
     * @var string
     */
    const PRESENT_ONTIME = 1;
    const PRESENT_LATE = 2;

    /**
     * Ci controller
     *
     * @var CI_Controller
     */
    private $ci;

    /**
     * Construct
     */
    function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->model('AttendanceReview_model');
        $this->ci->load->model('AttendanceTemp_model');
        $this->ci->load->library('session');
    }

    /**
     * Save clock in
     *
     * @param string $date
     * @return bool
     */
    public function clockIn($date)
    {
        $attendances = $this->ci->AttendanceTemp_model->getPendingAttendance($date);
        if (true === empty($attendances))
        {
            return false;
        }

        foreach($attendances as $attendance){
            $existRecord = $this->ci->AttendanceReview_model->getExistingAttendanceById(
                $attendance['EIdNo'],
                $attendance['attDate']
            );

            if($existRecord && null !== $existRecord['checkIn']){
                continue;
            }

            $attendanceData = [
                'onDuty'         => null,
                'offDuty'        => null,
                'gracePeriod'    => 0,
                'presentTypeID'  => 0,
                'shiftID'        => null,
                'lateHours'      => 0,
                'dayID'          => null,
                'attendanceDate' => $attendance['attDate'],
                'checkIn'        => $attendance['attTime'],
                'empID'          => $attendance['EIdNo'],
                'deviceID'       => $attendance['device_id'],
                'machineID'      => $attendance['machineAutoID'],
                'floorID'        => $attendance['floorID'],
                'companyID'      => $attendance['companyID'],
                'companyCode'    => $attendance['company_code'],
                'checkInDate'    => $attendance['attDate']
            ];

            $shiftDetail = $this->ci->AttendanceTemp_model->getShiftDetails($attendance['attDate'], $attendance['EIdNo']);
            if(false === empty($shiftDetail)){
                $onDutyTime = $shiftDetail['onDutyTime'];
                $offDutyTime = $shiftDetail['offDutyTime'];

                $onDutyDateTime = new DateTime($onDutyTime);
                $offDutyDateTime = new DateTime($offDutyTime);
                $clockInCutoffDateTime = new DateTime($shiftDetail['clockInCutoffTime']);

                $diffDateTime = $onDutyDateTime->diff($clockInCutoffDateTime);

                if ($diffDateTime < $onDutyDateTime || $diffDateTime > $offDutyDateTime) {
                    $this->clockOut($attendance['EIdNo'], $attendance['attDate']);
                    continue;
                }

                $gracePeriod = $shiftDetail['gracePeriod'];

                $attendanceData['onDuty'] = $onDutyTime; 
                $attendanceData['offDuty'] = $offDutyTime; 
                $attendanceData['gracePeriod'] = $gracePeriod; 
                $attendanceData['shiftID'] = $shiftDetail['shiftID']; 
                $attendanceData['presentTypeID'] = $this->getPresentTypeId(
                    $attendance['attTime'],
                    $onDutyTime,
                    $gracePeriod
                );
                $attendanceData['lateHours'] = $this->getLateHours(
                    $attendance['attTime'],
                    $onDutyTime,
                    $gracePeriod
                ); 
                $attendanceData['dayID'] = $shiftDetail['dayID'];
            }

            $this->ci->AttendanceReview_model->insert($attendanceData);
            $this->ci->AttendanceTemp_model->updateAttendance($attendance['autoID']);

        } 
    }

    /**
     * Get present type id
     *
     * @param string $clockIn
     * @param string $onDutyTime
     * @param integer $gracePeriod
     * @return integer
     */
    private function getPresentTypeId(
        $clockIn,
        $onDutyTime,
        $gracePeriod
    )
    {
        $time = new DateTime($onDutyTime);
        $interval = new DateInterval(sprintf('PT%dM', $gracePeriod));
        $time->add($interval);

        $ondutyWithGracePeriod = $time->format('H:i:s');

        return ($clockIn <= $ondutyWithGracePeriod) ? self::PRESENT_ONTIME : self::PRESENT_LATE;

    }

    /**
     * Get late hours
     *
     * @param string $clockIn
     * @param string $onDutyTime
     * @param integer $gracePeriod
     * @return integer
     */
    private function getLateHours(
        $clockIn,
        $onDutyTime,
        $gracePeriod
    )
    {
        $time = new DateTime($onDutyTime);
        $interval = new DateInterval(sprintf('PT%dM', $gracePeriod));
        $time->add($interval);

        $ondutyWithGracePeriod = $time->format('H:i:s');

        $startTime = new DateTime($ondutyWithGracePeriod);
        $endTime = new DateTime($clockIn);

        // Calculate the difference
        $interval = $startTime->diff($endTime);

        // Total minutes difference
        $totalMinutes = ($interval->h * 60) + $interval->i;

        // Convert to hours, including fractional part
        $totalHours = $totalMinutes / 60;

        if($totalHours < 0){
            return $totalHours * -1;
        }

        return 0;

    }

    /**
     * Save clock out
     *
     * @param integer $empId
     * @param string $clockOutDateTime
     * @return bool
     */
    public function clockOut($empId, $clockOutDateTime)
    {
        $clockOutDateTime = new DateTime($clockOutDateTime);
        $clockOutDate = $clockOutDateTime->format('Y-m-d');

        $attendanceData = $this->ci->AttendanceReview_model->getExistingAttendanceById(
            $empId,
            $clockOutDate
        );

        if (true === empty($attendanceData) || null == $attendanceData['checkIn'])
        {
            return false;
        }

        $clockOutTime = $clockOutDateTime->format('H:i:s');

        $attendance = [
            'checkOut'     => $clockOutTime,
            'earlyHours'   => $this->getEarlyHours($clockOutTime, $attendanceData['offDuty']),
            'OTHours'      => $this->getOTHours(
                $attendanceData['onDuty'],
                $attendanceData['offDuty'],
                $attendanceData['checkIn'],
                $clockOutTime
            ),
            'checkOutDate' => $clockOutDate,
        ];

        $calendarData = $this->ci->AttendanceTemp_model->getCalendarDateByAttendanceDate(
            $clockOutDate
        );

        $shiftDetail = $this->ci->AttendanceTemp_model->getShiftDetailsByShiftId(
            $attendanceData['shiftID'],
            $attendanceData['dayID']
        );

        if ($calendarData['holiday_flag'] == 1){
            $attendance['holiday'] = 1;
            $attendance['isHoliday'] = 1;
            $attendance['holidayOTHours'] = $attendance['OTHours'];
        } else if ($shiftDetail['isWeekend'] == 1){
            $attendance['weekend'] = 1;
            $attendance['isWeekEndDay'] = 1;
            $attendance['weekendOTHours'] = $attendance['OTHours'];
        } else {
            $attendance['normalDay'] = 1;
            $attendance['isNormalDay'] = 1;
            $attendance['NDaysOT'] = $attendance['OTHours'];
        }

        $specialOT = $attendance['OTHours'] - $shiftDetail['specialOT'];
        $attendance['specialOThours'] = 0;

        if(
            isset($attendance['isNormalDay'])
            && $specialOT > 0
        ){
            $attendance['specialOThours'] = $specialOT;
        }

        $attendance['isSpecialOT'] = $attendance['specialOThours'] > 0 ? 1 : 0;

        $attendance['realTime'] = round($this->getRealTime(
            $attendanceData['onDuty'],
            $attendanceData['offDuty'],
            $attendanceData['checkIn'],
            $clockOutTime
        ), 2);
        
        $this->ci->AttendanceReview_model->update($attendanceData['ID'], $attendance);

    } 

    /**
     * Get early hours
     *
     * @param string $clockOut
     * @param string $offDutyTime
     * @return integer
     */
    private function getEarlyHours(
        $clockOut,
        $offDutyTime
    )
    { 
        $offDutyTime = new DateTime($offDutyTime);
        $clockOutTime = new DateTime($clockOut);

        // Calculate the difference
        $interval = $offDutyTime->diff($clockOutTime);

        // Total minutes difference
        $totalMinutes = ($interval->h * 60) + $interval->i;

        // Convert to hours, including fractional part
        $totalHours = $totalMinutes / 60;

        if($totalHours > 0){
            return $totalHours;
        }

        return 0;

    }

    /**
     * Get ot real time
     *
     * @param string $onDutyTime
     * @param string $offDutyTime
     * @param string $clockInTime
     * @param string $clockOutTime
     * @return integer
     */
    private function getRealTime(
        $onDutyTime,
        $offDutyTime,
        $clockInTime,
        $clockOutTime
    )
    { 
        list($shiftHours, $officialWorkedHours) = $this->calculateShiftAndWorkedHours(
            $onDutyTime,
$offDutyTime,
$clockInTime,
            $clockOutTime
        );
        return ($officialWorkedHours / $shiftHours) * 1;

    }

    /**
     * Get ot hours
     *
     * @param string $onDutyTime
     * @param string $offDutyTime
     * @param string $clockInTime
     * @param string $clockOutTime
     * @return integer
     */
    private function getOTHours(
        $onDutyTime,
        $offDutyTime,
        $clockInTime,
        $clockOutTime
    )
    { 
        list($shiftHours, $officialWorkedHours) = $this->calculateShiftAndWorkedHours(
            $onDutyTime,
$offDutyTime,
$clockInTime,
            $clockOutTime
        );

        $diffHours = $officialWorkedHours - $shiftHours;

        return $diffHours > 1 ? $diffHours : 0;

    }

    /**
     * Calculate shift and worked hours
     *
     * @param string $onDutyTime
     * @param string $offDutyTime
     * @param string $clockInTime
     * @param string $clockOutTime
     * @return array
     */
    private function calculateShiftAndWorkedHours(
        $onDutyTime,
        $offDutyTime,
        $clockInTime,
        $clockOutTime
    )
    {
        $onDutyTime = new DateTime($onDutyTime);
        $offDutyTime = new DateTime($offDutyTime);
        $clockInTime = new DateTime($clockInTime);
        $clockOutTime = new DateTime($clockOutTime);

        // Calculate the difference
        $interval = $offDutyTime->diff($onDutyTime);

        // Total minutes difference
        $totalMinutes = ($interval->h * 60) + $interval->i;

        // Convert to hours, including fractional part
        $shiftHours = $totalMinutes / 60;

        $officialClockingTime = $clockInTime < $onDutyTime ? $onDutyTime : $clockInTime;

        // Calculate the difference
        $interval = $clockOutTime->diff($officialClockingTime);

        // Total minutes difference
        $totalMinutes = ($interval->h * 60) + $interval->i;

        // Convert to hours, including fractional part
        $officialWorkedHours = $totalMinutes / 60;

        return [$shiftHours, $officialWorkedHours];
    }

    /**
     * Day out process
     *
     * @param string $attendanceDate
     * @param integer $companyId
     * @return void
     */
    public function dayOutProcess($attendanceDate, $companyId)
    {
        $employees = $this->ci->AttendanceTemp_model->getEmployees($attendanceDate, $companyId);

        if (empty($employees)) {
            return;
        }
       
        foreach($employees as $employee){

            if (null !== $employee['autoID']) {
                continue;
            }

            $existRecord = $this->ci->AttendanceReview_model->getExistingAttendanceById(
                $employee['EIdNo'],
                $attendanceDate
            );

            if($existRecord){
                continue;
            }

            $attendanceData = [
                'empID'          => $employee['EIdNo'],
                'deviceID'       => $employee['deviceID'],
                'machineID'      => $employee['empMachineID'],
                'floorID'        => $employee['floorID'],
                'attendanceDate' => $attendanceDate,
            ];

            if($employee['setDefaulttimeYN'] == 1){
                $attendanceData['presentTypeID'] = 1;
                $attendanceData['checkIn'] = $employee['onDutyTime'];
                $attendanceData['checkOut'] = $employee['offDutyTime'];
            }else{
                $attendanceData['presentTypeID'] = $this->determinePresentTypeID($employee, $attendanceDate);
            }

            $this->ci->AttendanceReview_model->insert($attendanceData);

        }
        
    }

    /**
     * Determine present type ID
     *
     * @param array $employee
     * @param string $attendanceDate
     * @return int
     */
    private function determinePresentTypeID($employee, $attendanceDate)
    {
        $calendarData = $this->ci->AttendanceTemp_model->getCalendarDateByAttendanceDate($attendanceDate);

        if ($calendarData['holiday_flag'] == 1) {
            return 8; // Holiday
        }

        if ($employee['isWeekend'] == 1) {
            return 7; // Weekend
        }

        $leaveMaster = $this->ci->AttendanceTemp_model->getLeaveByEmpId($employee['EIdNo'], $attendanceDate);

        if ($leaveMaster) {
            return 5; // Leave
        }

        return 4; // Absent
    }

}
