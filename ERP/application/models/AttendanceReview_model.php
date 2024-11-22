<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class for document attendance review
 */
class AttendanceReview_model extends ERP_Model
{
    /**
     * Get existing record for current date and employee id
     *
     * @return array
     */
    public function getExistingAttendanceById($empId, $date)
    {
        $this->db->select(
            '*'
        );
        $this->db->from('srp_erp_pay_empattendancereview');
        $this->db->where('srp_erp_pay_empattendancereview.empID', $empId);
        $this->db->where('srp_erp_pay_empattendancereview.attendanceDate', $date);
        return $this->db->get()->row_array();
    }

    /**
     * Insert record to attendance review table
     *
     * @param array $data
     * @return void
     */
    public function insert($data)
    {
        $this->db->insert('srp_erp_pay_empattendancereview', $data);
    } 

    /**
     * Update record to attendance review table
     *
     * @param integer $id
     * @param array $data
     * @return void
     */
    public function update($id, $data)
    {
        $this->db->where('ID', $id);
        $this->db->update('srp_erp_pay_empattendancereview', $data);

    } 

}