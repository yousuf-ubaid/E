<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Attendance_model extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }


    function load_attendance_data(){

        $this->db->select('*');
        $this->db->from('srp_erp_att_get_machine_data');
       // $this->db->where('cloked_in_out_date','2023-06-12');
        $this->db->where('company_id','674');
        $day_data = $this->db->get()->result_array();

        $base_arr = array();

        foreach($day_data as $record){

            $base_arr[$record['hrm_emp_code']][$record['cloked_in_out_date']][] = array('in_out_type' => $record['in_out_type'],'in_out_time' => $record['in_out_time'],'hrm_auto_id' => $record['hrm_auto_id'],'company_id' => $record['company_id']);

        }

        foreach($base_arr as $emp_code => $record_b){

            $data = array();
            $data['hrm_emp_code'] = $emp_code;
        
            
            foreach($record_b as $date => $rec){

                $data['cloked_in_out_date'] = $date;

                foreach($rec as $atten){

                    if($atten['in_out_type'] == 'I'){
                        $data['in_type'] = $atten['in_out_type'];
                        $data['in_time'] = $atten['in_out_time'];
                    }else{
                        $data['out_type'] = $atten['in_out_type'];
                        $data['out_time'] = $atten['in_out_time'];
                    }

                    $data['company_id'] = $atten['company_id'];
                    $data['hrm_auto_id'] = $atten['hrm_auto_id'];

                }

                //insert to data
                $this->db->insert('srp_erp_att_get_machine_data_2', $data);
              
            }
         
        }

    }

    function set_attendance_location(){

        $this->db->select('*');
        $this->db->from('srp_employeesdetails');
       // $this->db->where('cloked_in_out_date','2023-06-12');
        $day_data = $this->db->get()->result_array();

        foreach($day_data as $emp){

            $empMachineID = $emp['empMachineID'];
            $EIdNo = $emp['EIdNo'];
            $ECode = $emp['ECode'];
            $companyID = $emp['Erp_companyID'];
            $data = array();

            if($empMachineID){
                
                $data['floorID'] = 1;
                $data['empID'] = $EIdNo;
                $data['deviceID'] = 1;
                $data['empMachineID'] = $empMachineID;
                $data['companyID'] = $companyID;

                
                //insert to data
                $this->db->insert('srp_erp_empattendancelocation', $data);

            }

           // print_r($emp); exit;

        }


    }

    function load_attendance_data_step(){
        $this->db->select('*');
        $this->db->from('srp_erp_att_get_machine_data');
       // $this->db->where('cloked_in_out_date','2023-06-12');
        $this->db->where('company_id','673');
        $day_data = $this->db->get()->result_array();


        foreach($day_data as $emp_clocked){

            $data = array();
            $empMachineID = $emp_clocked['emp_machine_id'];


            if($empMachineID){
                $data['device_id'] = 1;
                $data['empMachineID'] = $empMachineID;
                $data['attDate'] = $emp_clocked['cloked_in_out_date'];
                $data['attTime'] = $emp_clocked['in_out_time'];
                $data['attDateTime'] = $emp_clocked['cloked_in_out_date'].' '.$data['attTime'];
                $data['companyID'] = $emp_clocked['company_id'];
                $data['uploadType'] = 0;

                 //insert to data
                $this->db->insert('srp_erp_pay_empattendancetemptable', $data);
            }
           
           
        }

    }



}



?>