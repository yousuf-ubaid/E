<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Profile_model');
        $this->load->helpers('profile_helper');
        $this->load->helper('employee');
        $this->load->model('Employee_model');
    }

    public function empProfile()
    {
        $company_id = current_companyID();
        $isNeedApproval = getPolicyValues('EPD', 'All');
        $data['pendingData'] = [];
        $empID = current_userID();
        $data['empID'] = $empID;
        $data['empArray'] = $this->Profile_model->get_employees_detail($data['empID']);
        $empImage = $data['empArray']['employees']['EmpImage'];

        $this->load->library('s3');
        if(!empty($empImage)){
            $empImage = $this->s3->createPresignedRequest($empImage, '1 hour');
        }
        else{
            $empImage = ($data['empArray']['employees']['Gender'] == 1)? 'male' : 'female';
            $empImage = $this->s3->createPresignedRequest("images/users/{$empImage}.png", '1 hour');
        }

        $data['empArray']['employees']['EmpImage'] = $empImage;

        if( $isNeedApproval == 1){
            $data['pendingData'] = get_pendingEmpApprovalData($data['empID']);
        }

        $data['grade'] = $this->db->query("SELECT gradeDescription  FROM srp_employeesdetails ed
                                           JOIN srp_erp_employeegrade grd ON ed.gradeID = grd.gradeID 
                                           WHERE ed.EIdNo={$empID}")->row('gradeDescription');

        $data['reportingsegment'] =  $this->db->query(" SELECT srp_erp_reporting_structure_details.detail_description as strdescription
        FROM srp_erp_employee_reporting_structure 
        JOIN srp_erp_reporting_structure_details ON srp_erp_reporting_structure_details.id = srp_erp_employee_reporting_structure.reportingStructureDetailID
        join srp_erp_reporting_structure_master on srp_erp_reporting_structure_master.id = srp_erp_reporting_structure_details.structureMasterID
        WHERE  srp_erp_employee_reporting_structure.empID ={$empID} and srp_erp_reporting_structure_master.systemTypeID = 1 ")->row('strdescription');

        $data['reportingdeivition'] =  $this->db->query(" SELECT srp_erp_reporting_structure_details.detail_description as strdescription
        FROM srp_erp_employee_reporting_structure 
        JOIN srp_erp_reporting_structure_details ON srp_erp_reporting_structure_details.id = srp_erp_employee_reporting_structure.reportingStructureDetailID
        join srp_erp_reporting_structure_master on srp_erp_reporting_structure_master.id = srp_erp_reporting_structure_details.structureMasterID
        WHERE  srp_erp_employee_reporting_structure.empID ={$empID} and srp_erp_reporting_structure_master.systemTypeID = 2 ")->row('strdescription');


        $data['reportingsubsegment'] =  $this->db->query(" SELECT srp_erp_reporting_structure_details.detail_description as strdescription
        FROM srp_erp_employee_reporting_structure 
        JOIN srp_erp_reporting_structure_details ON srp_erp_reporting_structure_details.id = srp_erp_employee_reporting_structure.reportingStructureDetailID
        join srp_erp_reporting_structure_master on srp_erp_reporting_structure_master.id = srp_erp_reporting_structure_details.structureMasterID
        WHERE  srp_erp_employee_reporting_structure.empID ={$empID} and srp_erp_reporting_structure_master.systemTypeID = 3 ")->row('strdescription');


        $data['yearMonth']['payrollYear'] = date('Y');
        $data['yearMonth']['payrollMonth'] = date('m');
        $data['leaveDetails'] = $this->Employee_model->get_emp_leaveDet_paySheetPrint($data['empID'], $data['yearMonth']);

        //check the assign template for employee create
        $page = $this->db->query("SELECT createPageLink FROM srp_erp_templatemaster
                              LEFT JOIN srp_erp_templates ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID
                              WHERE srp_erp_templates.FormCatID=89 AND companyID={$company_id}
                              ORDER BY srp_erp_templatemaster.FormCatID")->row('createPageLink');

        $data['is_tibian'] = ($page == 'system/hrm/employee_create_tibian')? 'Y': 'N';


        $this->load->view('system/profile/ajax/load_profile', $data);
    }

    function fetch_pendingEmpDataApproval(){
        $empID = $this->input->post('empID');
        $template = $this->uri->segment(3);
        $companyID = current_companyID();


        $familyDataChanges = $this->db->query("SELECT changeTB.*, familyDet.`name` changeName FROM srp_erp_employeefamilydatachanges AS changeTB
                                               JOIN srp_erp_family_details AS familyDet ON familyDet.empfamilydetailsID = changeTB.empfamilydetailsID
                                               WHERE companyID={$companyID} AND changeTB.empID={$empID} AND familyDet.empID={$empID}
                                               AND changeTB.approvedYN!=1 GROUP BY id")->result_array();

        $familyDataChanges = array_group_by($familyDataChanges, 'empfamilydetailsID');

        $newbank=$this->Employee_model->fetch_bank_details($empID);
        $familyDataNew = $this->Employee_model->fetch_family_details($empID, 0);
        $data['empID'] = $empID;
        $data['empArray'] = $this->Profile_model->get_employees_detail($empID);
        $data['pendingData'] = get_pendingEmpApprovalData($empID);
        $data['reporting'] = get_pendingEmpApprovalReportingData($empID);
        $data['department'] = get_pendingEmpApprovaldepartmentData($empID);
        $data['bankprimary'] = get_pendingbankprimaryData($empID);
        $data['bankdetail'] = get_pendingbankdetail($empID);
        $data['familyData_changes'] = $familyDataChanges;
        $data['bankdetailtext']= $newbank;
        $data['familyData_new'] = $familyDataNew;
        $data['is_tibian'] = ($template == 'tibian')? 'Y': 'N';

        $this->load->view('system/profile/ajax/pending_data', $data);
    }

    function approve_pendingEmpData(){
        $upDateNameWithInitial = $this->input->post('upDateNameWithInitial');
        $familyData = $this->input->post('familyData');
        $addFamilyData = $this->input->post('addFamilyData');
        $upDateColumn = $this->input->post('upDateColumn');
        $columnVal = $this->input->post('columnVal');
        $empID = $this->input->post('empID');
        $relatedColumnID = $this->input->post('relatedColumnID');
        $is_tibian = ($this->uri->segment(3) == 'tibian')? 'Y': 'N';
        $companyID = current_companyID();
        $reportColumn=$this->input->post('reportColumn');
        $departmentColumn=$this->input->post('departmentColumn');
        $bankColumn=$this->input->post('bankColumn');
        $addBankDetail = $this->input->post('addBankDetail');

        if(empty($upDateColumn) && empty($upDateNameWithInitial) && empty($familyData) && empty($addFamilyData)&& empty($reportColumn) && empty($departmentColumn) && empty($bankColumn) && empty($addBankDetail)){
            die( json_encode(['e', 'There is no data to update.']) );
        }

        $updateData = [
            'approvedYN' => 1,
            'approvedDate' => current_date(),
            'approvedbyEmpID' => current_userID()
        ];


        $this->db->trans_start();

        /**** Personal Data changes ***/
        if( !empty($upDateColumn)){

            $data = [];
            foreach($upDateColumn as $key=>$row){
                $data[$row] = $columnVal[$row];
                $updateData['columnVal'] = $columnVal[$row];
                $this->db->where( ['columnName'=>$row, 'empID' =>$empID, 'companyID'=>$companyID])->update('srp_erp_employeedatachanges', $updateData);
            }

                $this->db->where(['EIdNo' => $empID]) ->update('srp_employeesdetails', $data);

        }

        if( !empty($upDateNameWithInitial)){ //This will not work on Tibian template
            $initial = $this->input->post('initial');
            $initial_changed = $this->input->post('initial_changed');
            $eName4 = $this->input->post('Ename4');
            $eName4_changed = $this->input->post('Ename4_changed');

            $data['initial'] = $initial;
            $data['Ename4'] = $eName4;
            $data['Ename2'] = $initial . ' ' . $eName4;

            $this->db->where(['EIdNo' => $empID]) ->update('srp_employeesdetails', $data);

            if($initial_changed == 1){
                $this->db->where( ['columnName'=>'initial', 'empID' =>$empID, 'companyID'=>$companyID])->update('srp_erp_employeedatachanges', $updateData);
            }

            if($eName4_changed == 1){
                $this->db->where( ['columnName'=>'Ename4', 'empID' =>$empID, 'companyID'=>$companyID])->update('srp_erp_employeedatachanges', $updateData);
            }
        }

        if($is_tibian == 'Y'){
            if(in_array('Ename1', $upDateColumn) || in_array('EFamilyName', $upDateColumn)){
                $emp_full_name_data = $this->db->get_where('srp_employeesdetails', ['EIdNo' => $empID])->row_array();
                $full_name = $emp_full_name_data['Ename1'].' '.$emp_full_name_data['EFamilyName'];

                $this->db->where(['EIdNo' => $empID]) ->update('srp_employeesdetails', ['Ename2' => $full_name]);

            }

            if(in_array('Enameother1', $upDateColumn) || in_array('EFamilyNameOther', $upDateColumn)){
                $emp_full_name_data = $this->db->get_where('srp_employeesdetails', ['EIdNo' => $empID])->row_array();
                $full_name = $emp_full_name_data['Enameother1'].' '.$emp_full_name_data['EFamilyNameOther'];

                $this->db->where(['EIdNo' => $empID]) ->update('srp_employeesdetails', ['Enameother2' => $full_name]);

            }
        }

        /**** Family Data changes ***/
        if(!empty($familyData)) {
            foreach ($familyData as $familyID => $familyRow) {

                $familyDataUpdate = [];
                $familyDataUpdate['modifiedUser'] = current_employee();
                $familyDataUpdate['modifiedPc'] = current_pc();

                foreach ($familyRow as $rowName => $rowVal) {
                    $familyDataUpdate[$rowName] = $rowVal;

                    $this->db->where(['columnName' => $rowName, 'empID' => $empID, 'companyID' => $companyID])->update('srp_erp_employeefamilydatachanges', $updateData);
                }

                $this->db->where(['empfamilydetailsID' => $familyID, 'empID' => $empID])->update('srp_erp_family_details', $familyDataUpdate);

            }
        }


        /**** Family Data add ***/
        if(!empty($addFamilyData)){
            foreach($addFamilyData as $appEmpKey => $appEmp){
                $familyDataUpdate = [];
                $familyDataUpdate['approvedYN'] = 1;
                $familyDataUpdate['modifiedUser'] = current_employee();
                $familyDataUpdate['modifiedPc'] = current_pc();

                $this->db->where(['empID'=>$empID, 'empfamilydetailsID'=>$appEmp])->update('srp_erp_family_details', $familyDataUpdate);
            }
        }

        /**** Report Manger Data changes ***/
        if( !empty($reportColumn)){

            $data = [];
            foreach($reportColumn as $key=>$row){
                $data[$row] = $columnVal[$row];
                $updateData['columnVal'] = $columnVal[$row];
                $this->db->where( ['columnName'=>$row, 'empID' =>$empID, 'companyID'=>$companyID])->update('srp_erp_employeedatachanges', $updateData);
            }


            $this->db->where(['employeeManagersID' => $relatedColumnID]) ->update('srp_erp_employeemanagers', $data);           
        }

        /****Primary Bank account change ***/
        if( !empty($bankColumn)){

            $data = [];
            foreach($bankColumn as $key=>$row){
                $data[$row] = $columnVal[$row];
                $updateData['columnVal'] = $columnVal[$row];
                $this->db->where( ['columnName'=>$row, 'empID' =>$empID, 'companyID'=>$companyID])->update('srp_erp_employeedatachanges', $updateData);
            }

            $primary['isPrimary'] = 0;
            $this->db->where('employeeNo', $empID);
            $this->db->update('srp_erp_pay_salaryaccounts', $primary);

            $primary['isPrimary'] = 1;
            $this->db->where('id', $relatedColumnID);
            $this->db->where('employeeNo', $empID);
            $this->db->update('srp_erp_pay_salaryaccounts', $primary);       
        }

        
        /****Bank account Detail change ***/
        if(!empty($addBankDetail)) {

            // var_dump($addBankDetail);
            $this->db->select('*');
            $this->db->from('srp_erp_employeedatachanges');
            $this->db->where('empID',$empID);
            $this->db->where('companyID',$companyID);
            $this->db->where('approvedYN',0);
            $this->db->where('relatedColumnID',$addBankDetail[0]);

            $bankdetailupdate=$this->db->get()->result_array();

            foreach ($bankdetailupdate as $row) {
                $this->db->where('id', $row['id']);
                $this->db->update('srp_erp_employeedatachanges', $updateData);
            }
            
            foreach ($bankdetailupdate as $row) {
                if (!isset($data)) {
                    $data = array();
                }
                $data[$row['columnName']] = $row['columnVal'];
                $data['companyID'] = $row['companyID'];
            }
  
           
            $this->db->where('companyID', current_companyID())->where('id',  $bankdetailupdate[0]['relatedColumnID'])->where('employeeNo', $empID)->update('srp_erp_pay_salaryaccounts', $data); 
        }

         /**** Department Data changes ***/
        if( !empty($departmentColumn)){

            $data = [];
            foreach($departmentColumn as $key=>$row){
                $data[$row] = $columnVal[$row];
                $updateData['columnVal'] = $columnVal[$row];
                $this->db->where( ['columnName'=>$row, 'empID' =>$empID, 'companyID'=>$companyID])->update('srp_erp_employeedatachanges', $updateData);
            }

            $this->db->query("UPDATE srp_empdepartments SET isPrimary=0 WHERE EmpID={$empID} AND Erp_companyID={$companyID}");
            $this->db->query("UPDATE srp_empdepartments SET isPrimary=1 WHERE EmpID={$empID} AND EmpDepartmentID={$relatedColumnID} AND Erp_companyID={$companyID}");      
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode( ['s', 'Updated successfully.']);
        } else {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error In update process']);
        }

    }

    function update_empDetail()
    {
        echo json_encode($this->Profile_model->update_empDetail());
    }

    function change_password()
    {
        $this->form_validation->set_rules('currentPassword', 'Current Password', 'trim|required');
        $this->form_validation->set_rules('newPassword', 'New Password', 'trim|required');
        $this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'trim|required|matches[newPassword]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            return $this->Profile_model->change_password();
        }
    }

    function fetch_family_details()
    {
        $empID = current_userID();
        $fm_data = $this->Employee_model->fetch_family_details($empID);

        if(!empty($fm_data)){
            $no_img = $this->s3->createPresignedRequest('images/no_image.jpg', '1 hour');
            foreach($fm_data as $key=>$row){
                $imgPath = trim($row['image'] ?? '');

                if( $imgPath == '' ){
                    $imgPath = $no_img;
                }
                else{
                    $imgPath = $this->s3->createPresignedRequest($imgPath, '1 hour');
                }
                $fm_data[$key]['image'] = $imgPath;
            }
        }
        $data['empArray'] = $fm_data;
        $this->load->view('system/hrm/ajax/ajax-employee_profile_load_family_info_profile', $data);
    }

    public function load_empDocumentProfileView()
    {
        $data['empID'] = current_userID();
        $data['isFromProfile'] = 'Y';
        //$this->load->view('system/hrm/ajax/load_empDocumentProfileView', $data);
        $this->load->view('system/hrm/ajax/load_empDocumentView', $data);
    }

    function fetch_my_employee_list()
    {
        /*$empID = current_userID();
        $data['empArray'] = $this->Employee_model->fetch_my_employee_list($empID);*/
        $this->load->library('s3');
        $data['male_img'] = $this->s3->createPresignedRequest('images/users/male.png', '1 hour');
        $data['female_img'] = $this->s3->createPresignedRequest('images/users/female.png', '1 hour');
        $this->load->view('system/hrm/ajax/ajax-my_employees_list', $data);
    }

    function fetch_bank_details()
    {
        $id = current_userID();
        $data['empID'] = current_userID();
        $data['empDetail'] = $this->db->query("select ECode,Ename2 from srp_employeesdetails where EIdNo={$id} ")->row_array();
        $data['accountDetails'] = $this->Employee_model->loadEmpBankAccount($id);
        $data['accountDetails_nonPayroll'] = $this->Employee_model->loadEmpNonBankAccount($id);
        $this->load->view('system/hrm/ajax/load_empBankView', $data);
    }

    function ajax_update_familydetails()
    {
        /*$result = $this->Employee_model->xeditable_update('srp_erp_family_details', 'empfamilydetailsID');
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'updated'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'updated Fail'));
        }*/
        echo json_encode($this->Profile_model->update_familydetails());
    }
}
