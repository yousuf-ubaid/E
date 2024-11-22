<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class for attendance temp
 */
class AttendanceTemp_model extends ERP_Model
{
   
    /**
     * Get pending attendance
     *
     * @param string $date
     * @return array
     */
    public function getPendingAttendance($date)
    {
        $this->db->select(
            '*,srp_employeesdetails.EIdNo,srp_erp_empattendancelocation.floorID,srp_erp_company.company_code'
        );
        $this->db->from('srp_erp_pay_empattendancetemptable');
        $this->db->join('srp_erp_empattendancelocation', 'srp_erp_pay_empattendancetemptable.device_id = srp_erp_empattendancelocation.deviceID AND srp_erp_pay_empattendancetemptable.empMachineID = srp_erp_empattendancelocation.empMachineID');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_empattendancelocation.empID');
        $this->db->join('srp_erp_company', 'srp_erp_pay_empattendancetemptable.companyID = srp_erp_company.company_id', 'LEFT');
        $this->db->where('srp_erp_pay_empattendancetemptable.attDate', $date);
        $this->db->where('srp_erp_pay_empattendancetemptable.isUpdated', 0);
        $this->db->where('srp_employeesdetails.empConfirmedYN', 1);
        $this->db->where('srp_employeesdetails.isDischarged', 0);
        $this->db->order_by('srp_erp_pay_empattendancetemptable.attDateTime ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Get shift details
     *
     * @return array
     */
    public function getShiftDetails($attendanceDate, $empID)
    {
        $this->db->select(
            'srp_erp_pay_shiftdetails.onDutyTime,
            srp_erp_pay_shiftdetails.offDutyTime,
            srp_erp_pay_shiftemployees.shiftID,
            srp_erp_pay_shiftdetails.dayID,
            srp_erp_pay_shiftdetails.clockInCutoffTime,
            srp_erp_pay_shiftdetails.clockOutCutoffTime,
            srp_erp_pay_shiftdetails.gracePeriod'
        );
        $this->db->from('srp_erp_pay_shiftemployees');
        $this->db->join('srp_erp_pay_shiftdetails', 'srp_erp_pay_shiftemployees.shiftID = srp_erp_pay_shiftdetails.shiftID');
        $this->db->where('srp_erp_pay_shiftemployees.empID', $empID);
        $this->db->where("'{$attendanceDate}' BETWEEN startDate AND endDate");
        $this->db->where('srp_erp_pay_shiftdetails.dayID', date('N', strtotime($attendanceDate)));
        return $this->db->get()->row_array();
    }

    /**
     * Get shift details by shift id
     *
     * @param integer $shiftID
     * @param integer $dayID
     * @return array
     */
    public function getShiftDetailsByShiftId($shiftID, $dayID)
    {
        $this->db->select(
            '*'
        );
        $this->db->from('srp_erp_pay_shiftdetails');
        $this->db->where('srp_erp_pay_shiftdetails.shiftID', $shiftID);
        $this->db->where('srp_erp_pay_shiftdetails.dayID', $dayID);
        return $this->db->get()->row_array();
    }

    /**
     * Get employees
     *
     * @param string $date
     * @param integer $companyId
     * @return array
     */
    public function getEmployees($attendanceDate, $companyId)
    {
        $this->db->select(
            'srp_erp_pay_shiftdetails.onDutyTime,
            srp_erp_pay_shiftdetails.offDutyTime,
            srp_employeesdetails.EIdNo,
            srp_erp_pay_empattendancetemptable.autoID,
            srp_erp_empattendancelocation.deviceID,
            srp_erp_empattendancelocation.empMachineID,
            srp_erp_pay_shiftdetails.isWeekend,
            srp_erp_empattendancelocation.floorID,
            srp_employeesdetails.setDefaulttimeYN'
        );
        $this->db->from('srp_employeesdetails');
        $this->db->join('srp_erp_empattendancelocation', 'srp_employeesdetails.EIdNo=srp_erp_empattendancelocation.empID', 'LEFT');
        $this->db->join('srp_erp_pay_shiftemployees', 'srp_employeesdetails.EIdNo=srp_erp_pay_shiftemployees.empID', 'LEFT');
        $this->db->join('srp_erp_pay_shiftdetails', 'srp_erp_pay_shiftemployees.shiftID = srp_erp_pay_shiftdetails.shiftID', 'LEFT');
        $this->db->join('srp_erp_pay_empattendancetemptable', 'srp_erp_pay_empattendancetemptable.device_id = srp_erp_empattendancelocation.deviceID AND srp_erp_pay_empattendancetemptable.empMachineID = srp_erp_empattendancelocation.empMachineID AND srp_erp_pay_empattendancetemptable.attDate = \'' . $attendanceDate . '\'', 'LEFT');
        $this->db->where('srp_employeesdetails.isCheckin', 1);
        $this->db->where("'{$attendanceDate}' BETWEEN srp_erp_pay_shiftemployees.startDate AND srp_erp_pay_shiftemployees.endDate");
        $this->db->where('srp_erp_pay_shiftdetails.dayID', date('N', strtotime($attendanceDate)));
        $this->db->where('srp_employeesdetails.empConfirmedYN', 1);
        $this->db->where('srp_employeesdetails.isDischarged', 0);
        $this->db->where('srp_employeesdetails.Erp_companyID', $companyId);
        return $this->db->get()->result_array();
    }


    /**
     * Get calendar date by attendance date
     *
     * @param string $date
     * @return array
     */
    public function getCalendarDateByAttendanceDate($date)
    {
        $this->db->select(
            '*'
        );
        $this->db->from('srp_erp_calender');
        $this->db->where('srp_erp_calender.fulldate', $date);
        return $this->db->get()->row_array();
    }
    
    /**
     * Get leave for emp id
     *
     * @param integer $empId
     * @param string $date
     * @return array
     */
    public function getLeaveByEmpId($empId, $date)
    {
        $this->db->select(
            '*'
        );
        $this->db->from('srp_erp_leavemaster');
        $this->db->where('empID', $empId);
        $this->db->where('approvedYN', 1);
        $this->db->where("'{$date}' BETWEEN startDate AND endDate");
        return $this->db->get()->row_array();
    } 

    /**
     * Update attendance in update to the review table
     *
     * @param integer $id
     * @return void
     */
    public function updateAttendance($id)
    {
        $this->db->update('srp_erp_pay_empattendancetemptable', ['isUpdated' => 1], ['autoID' => $id]);
    } 

}