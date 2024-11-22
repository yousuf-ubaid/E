<?php

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api_attendance extends REST_Controller
{
    private $company_info;
    private $company_id = 0;

    function __construct()
    {
        parent::__construct();
        $this->load->library('Attendance');
        $this->auth();
    }

    protected function auth()
    {
        if (isset($_SERVER['HTTP_SME_API_KEY'])) {
            $result = $this->db->select('*')
                ->from('keys')
                ->where('key', $_SERVER['HTTP_SME_API_KEY'])
                ->get()->row_array();
            if (!empty($result)) {
                $tmpCompanyInfo = $this->db->select('*')
                    ->from('srp_erp_company')
                    ->where('company_id', $result['company_id'])
                    ->get()->row_array();
                $this->company_info = $tmpCompanyInfo;
                if (!empty($this->company_info)) {
                    $this->company_id = $this->company_info['company_id'];
                    $this->setDb();
                    return true;
                } else {
                    echo $this->response(array('type' => 'error', 'error_code' => 500, 'error_message' => 'Company ID not found'), 500);
                }
            }
        }
    }

    protected function setDb()
    {
        if (!empty($this->company_info)) {
            $config['hostname'] = trim($this->encryption->decrypt($this->company_info["host"]));
            $config['username'] = trim($this->encryption->decrypt($this->company_info["db_username"]));
            $config['password'] = trim($this->encryption->decrypt($this->company_info["db_password"]));
            $config['database'] = trim($this->encryption->decrypt($this->company_info["db_name"]));
            $config['dbdriver'] = 'mysqli';
            $config['db_debug'] = (ENVIRONMENT !== 'production');
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = TRUE;
            $this->load->database($config, FALSE, TRUE);
        }
    }

    public function mapping_details_get(){

        $device_id = $_SERVER['HTTP_DEVICE_ID'];
        $mapping_det = $this->db->where('device_id', $device_id)->get('attendance_mapping')->row_array();
        $last_id = $this->db->where('device_id', $device_id)->order_by('autoID', 'DESC')->limit(1)->get('srp_erp_pay_empattendancetemptable')->row('machineAutoID');

        return $this->response( ['mapping_det' => $mapping_det, 'last_id' => $last_id], 200);
    }

    public function mapping_details_oman_oil_get(){

        $device_id = $_SERVER['HTTP_DEVICE_ID'];
        $last_id = $this->db->where('deviceID', $device_id)->order_by('logID', 'DESC')->limit(1)
                        ->get('srp_erp_attendance_oman_oil')->row('logID');

        return $this->response( ['last_id' => $last_id], 200);
    }

    public function sync_attendance_post(){
        $att_data = json_decode( file_get_contents('php://input') );
        $device_id = $_SERVER['HTTP_DEVICE_ID'];
  
        try {
            if(!empty($att_data)){
                $date_time = date('Y-m-d H:i:s');
    
                $int_data = [];
                $not_added_arr = [];

                foreach ($att_data as $row){
        
                    $attDateTime = $row->attDateTime;
                    $empMachineID = $row->empMachineID;
                    $attDate = date('Y-m-d', strtotime($attDateTime));
                    $attTime = date('H:i:s', strtotime($attDateTime));

                    //get company id of the user
                    $get_employee_record =  $this->db->query("
                            SELECT location.companyID as Erp_companyID,location.empMachineID
                            FROM srp_employeesdetails as emp
                            LEFT JOIN srp_erp_empattendancelocation as location ON emp.EidNo = location.empID
                            WHERE location.empMachineID = '{$empMachineID}'
                    ")->row_array();

                    if($get_employee_record){

                        $ex_record = $this->db->where('attDate', $attDate)->where('empMachineID',$row->empMachineID)->where('companyID',$get_employee_record['Erp_companyID'])
                            ->get('srp_erp_pay_empattendancetemptable')->row_array();
        
                        if($ex_record){
                            $row->attType = 'O';
                        }

                        $int_data[] = [
                            'device_id' => $device_id,
                            'machineAutoID' => $row->machineAutoID,
                            'empMachineID' => $row->empMachineID,
                            'deviceAutoId' => $row->deviceAutoId,
                            'attDate' => $attDate,
                            'attTime' => $attTime,
                            'attType' => $row->attType,
                            'attDateTime' => $attDateTime,
                            'companyID' => $get_employee_record['Erp_companyID'],
                            'createdUserID' => 0,
                            'uploadType' => 1,
                            'timestamp' => $date_time
                        ];

                    }else{
                        $not_added_arr[] = $empMachineID;
                    }
                   
                }
    
                $this->db->insert_batch('srp_erp_pay_empattendancetemptable', $int_data);
            }

            if(count($not_added_arr) > 0){
                $ids = join(',',$not_added_arr);
                $data = array('status'=>'error','message'=>"Machine IDs $ids not exists in the system.");
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
       
            }else{
            
                $data = array('status'=>'sucess','message'=>'Attendance synchronize successfully done.');
                return $this->set_response($data, REST_Controller::HTTP_OK);
            }
    
           

        } catch (\Throwable $th) {
            $data = array('status'=>'error',"message'=>'Something went wrong - {$th}");
            return $this->set_response($data, REST_Controller::HTTP_OK);
        }
       
       // return $this->response('Attendance synchronize successfully done.');
    }

    public function sync_attendance_processed_post(){
        $att_data = json_decode( file_get_contents('php://input') );
   
        try {
            if(!empty($att_data)){
                $date_time = date('Y-m-d H:i:s');
    
                $int_data = [];
                $not_added_arr = [];

                foreach ($att_data as $row){

                    $data = array();
                    
                    // parameter list //
                    $attendanceDate = $row->attendanceDate;
                    $empMachineID = $row->empMachineID;
                    $onDutyTime = $row->onDutyTime;
                    $offDutyTime = $row->offDutyTime;
                    $gracePeriod = $row->gracePeriod;
                    $presentTypeID = $row->presentTypeID;
                    $checkInDate = $row->checkInDate;
                    $checkIn = $row->checkInTime;
                    $checkOutDate = $row->checkOutDate;
                    $checkOut = $row->checkOutTime;
                    $lateHours = $row->lateHours;
                    $earlyHours = $row->earlyHours;
                    $OTHours = $row->OTHours;
                    $normalDay = $row->normalDay;
                    $weekendDay = $row->weekendDay;
                    $holidayDay = $row->holidayDay;
                    $NDaysOT = $row->normalDayOTMins;
                    $weekendOTHours = $row->weekendDayOTMins;
                    $holidayOTHours = $row->holidayDayOTMins;

                    // parameter list end
                  

                    //get company id of the user
                    $get_employee_record =  $this->db->query("
                            SELECT location.companyID as Erp_companyID,location.empID,location.floorID,location.deviceID,location.empMachineID
                            FROM srp_employeesdetails as emp
                            LEFT JOIN srp_erp_empattendancelocation as location ON emp.EidNo = location.empID
                            WHERE location.empMachineID = '{$empMachineID}'
                    ")->row_array();

                    if($get_employee_record){

                        $ex_record = $this->db->where('attendanceDate', $attendanceDate)->where('empID',$get_employee_record['empID'])->where('companyID',$get_employee_record['Erp_companyID'])
                            ->get('srp_erp_pay_empattendancereview')->row_array();

                        $data = [
                            'deviceID' => $get_employee_record['deviceID'],
                            'empID' => $get_employee_record['empID'],
                            'machineID' => $get_employee_record['empMachineID'],
                            'floorID' => $get_employee_record['floorID'],
                            'clockoutFloorID' => $get_employee_record['floorID'],
                            'attendanceDate' => $attendanceDate,
                            'onDuty' => $onDutyTime,
                            'gracePeriod' => $gracePeriod,
                            'offDuty' => $offDutyTime,
                            'presentTypeID' => $presentTypeID,
                            'checkInDate' => $checkInDate,
                            'checkIn' => $checkIn,
                            'checkOutDate' => $checkOutDate,
                            'checkOut' => $checkOut,
                            'lateHours' => $lateHours,
                            'earlyHours' => $earlyHours,
                            'OTHours' => $OTHours,
                            'normalDay' => $normalDay,
                            'weekend' => $weekendDay,
                            'holiday' => $holidayDay,
                            'NDaysOT' => $NDaysOT,
                            'weekendOTHours' => $weekendOTHours,
                            'holidayOTHours' => $holidayOTHours,
                            'companyID' => $get_employee_record['Erp_companyID'],
                        ];

                        if($ex_record){
                            $this->db->where('ID',$ex_record['ID'])->update('srp_erp_pay_empattendancereview', $data);
                        }else{
                            $this->db->insert('srp_erp_pay_empattendancereview', $data);
                        }

                    }else{
                        $not_added_arr[] = $empMachineID;
                    }
                   
                }
    
            }

            if(count($not_added_arr) > 0){
                $ids = join(',',$not_added_arr);
                $data = array('status'=>'error','message'=>"Machine IDs $ids not exists in the system.");
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
       
            }else{
            
                $data = array('status'=>'sucess','message'=>'Attendance synchronize successfully done.');
                return $this->set_response($data, REST_Controller::HTTP_OK);
            }
    
           

        } catch (\Throwable $th) {
            $data = array('status'=>'error',"message'=>'Something went wrong - {$th}");
            return $this->set_response($data, REST_Controller::HTTP_OK);
        }
       
       // return $this->response('Attendance synchronize successfully done.');
    }

    public function sync_attendance_oman_oil_post(){
        $att_data = json_decode( file_get_contents('php://input') );
        $device_id = $_SERVER['HTTP_DEVICE_ID'];

        if(!empty($att_data)){
            $date_time = date('Y-m-d H:i:s');

            $int_data = [];
            foreach ($att_data as $row){

                $int_data[] = [
                    'logID' => $row->AttendanceLogId,
                    'attendanceDate' => $row->AttendanceDate,
                    'deviceID' => $device_id,
                    'empMachineID' => $row->EmployeeId,
                    'inTime' => $row->InTime,
                    'outTime' => $row->OutTime,
                    'punchRecord' => $row->PunchRecords,
                    'totalDurationOrg' => $row->TotalDuration,
                    'totalDuration' => $row->TotalDuration,
                    'overTime' => $row->OverTime,
                    'statusCode' => $row->StatusCode,
                    'missedInPunch' => $row->MissedInPunch,
                    'missedOutPunch' => $row->MissedOutPunch,
                    'locationId' => null,
                    'companyID' => $this->company_id,
                    'pullingTime' => $date_time,
                    'timestamp' => $date_time
                ];
            }
            $this->db->insert_batch('srp_erp_attendance_oman_oil', $int_data);

            /*** Update location ID ***/
            $this->db->query("UPDATE srp_erp_attendance_oman_oil AS mas_tb
                        JOIN (
                            SELECT logID, empTB.floorID FROM srp_erp_attendance_oman_oil AS t1
                            JOIN srp_employeesdetails AS empTB ON empTB.empMachineID = t1.empMachineID
                            WHERE t1.locationID IS NULL LIMIT 2000
                        ) AS empTB ON mas_tb.logID = empTB.logID
                        SET mas_tb.locationID = IFNULL(empTB.floorID, 0)");

            $this->db->query("UPDATE srp_erp_attendance_oman_oil SET locationID = 0 WHERE locationID IS NULL LIMIT 2000");
        }

        //return $this->response(['s', 'att_data' => $att_data]);
        return $this->response('Attendance synchronize successfully done.');
    }

    /**
     * Save clock in
     *
     * @return string
     */
    public function clock_in_post(){
        $attData = json_decode( file_get_contents('php://input'));

        if(isset($attData->date) && !empty($attData->date)){
            $this->attendance->clockIn($attData->date);
        }
        
        return $this->response('Attendance clock in is done');
    }

    /**
     * Save clock out
     *
     * @return string
     */
    public function clock_out_post(){
        $this->load->helper('employee_helper');

        $attData = json_decode( file_get_contents('php://input'));

        if(!isset($attData->clockOutDate) && empty($attData->clockOutDate)){
            return $this->response('Clock out date is required');
        }

        if(!isset($attData->empId) && empty($attData->empId)){
            return $this->response('Emp id is required');
        }

        if(!validateDate($attData->clockOutDate)){
            return $this->response('Invalid date');
        }
        
        $this->attendance->clockOut($attData->empId, $attData->clockOutDate);
        
        return $this->response('Attendance clock out is done');
    }

    /**
     * Save clock out
     *
     * @return string
     */
    public function dayoutprocess_post(){
        $this->load->helper('employee_helper');

        $attData = json_decode( file_get_contents('php://input'));
        
        $this->attendance->dayOutProcess($attData->attendanceDate, $attData->companyId);
        
        return $this->response('Attendance clock out is done');
    }

}