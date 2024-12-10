<?php

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');
class MigrationDocument extends ERP_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->helpers('buyback_helper');
        $this->load->helpers('insurancetype_helper');
        $this->load->helper('migration_document_helper');
        $this->load->model('Migration_document_model');
        $this->load->library('s3');
        $this->load->helper('employee_helper');

        ini_set('max_execution_time', 360);
        ini_set('memory_limit', '2048M');

    }

    function fetch_for_col_excel_migration_document()
    {
        $date_format_policy = date_format_policy();
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];

        $documentID = trim($this->input->post('documentID') ?? '');

        $details = $this->db->query("SELECT * FROM srp_erp_migration_config WHERE documentID='" . $documentID . "' AND companyID = {$companyID}")->result_array();

        $data = array();

        $data[0]='#';

        $a = 1;
        foreach ($details as $row)
        {
            
            $data[] = $row['tempColoumnName'];
            $a++;
        }

        return $data;

    }

    function export_excel_migration_document()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Migration Document');
        $this->load->database();
        $data = $this->fetch_for_col_excel_migration_document();

        $header = $data;

        $this->excel->getActiveSheet()->fromArray($header, null, 'A1');

        $filename = 'Migration Details.csv'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function migration_master_excelUpload()
    {
        $docID = $this->input->post('docID');
        $isdocTypeID = $this->input->post('isdocTypeID');
        $companyID = $this->common_data['company_data']['company_id'];
       // $this->db->trans_start();
        if (isset($_FILES['excelUpload_file']['size']) && $_FILES['excelUpload_file']['size'] > 0) {
            $type = explode(".", $_FILES['excelUpload_file']['name']);
          //  print_r($type);exit;
            if (strtolower(end($type)) != 'csv') {
                die(json_encode(['e', 'File type is not csv - ', $type]));
            }
            $i = 0;
            $x = 0;
            $n = 0;
            $filename = $_FILES["excelUpload_file"]["tmp_name"];
            $file = fopen($filename, "r");
            $filed = fopen($filename, "r");
            $dataExcel = [];
            $dataExcel2 = [];
            $dataExcel3 = [];

            //check csv header match or not
            $document_arr = $this->db->query("SELECT * FROM srp_erp_migration_config WHERE documentID='" . $docID . "' AND companyID = {$companyID} AND isExcel=1 order by sortOrder asc")->result_array();

            $required_header = array();
            $upload_file_header=array();
            $required_header[0]='LineID';
            $required_header[1]='headerReferenceID';

            foreach ($document_arr as $row)
            {
                $required_header[] = $row['tempColoumnName'];
            }


            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                if($i==0){
                    $upload_file_header=$getData;
                }
                if ($i > 0) {
                 
                    if (!empty($getData[0])) {
                        $dataExcel[$i]['id'] = $getData[0];
                    }

                }
                $i++;
            }
            fclose($file);

            if($required_header===$upload_file_header){

                $data['documentID']=$docID;
                $data['createdUserID']=current_userID();
                $data['createdDateTime']=current_date();
                $data['companyID']=$companyID;
                $data['status']=0;
                $data['noOfRecords']=count($dataExcel);

                if(count($dataExcel)>0){
                    $data['isMigration']=1;
                }else{
                    $data['isMigration']=0;
                }

                if($isdocTypeID==1){
                    $docTypeID = $this->input->post('docTypeID');
                    $fetch_doc_type = $this->db->query("SELECT * FROM srp_erp_migration_config WHERE documentID='" . $docID . "' AND documentType='" . $docTypeID . "' AND companyID = {$companyID} AND isExcel=1")->row_array();
                    $data['documentType']=$docTypeID;
                    $data['documentTypeName']=$fetch_doc_type['documentTypeName'];
                }

                $this->db->insert('srp_erp_migration_header', $data);
                $last_id = $this->db->insert_id();
                
                $header=[];

                while (($getData1 = fgetcsv($filed, 10000, ",")) !== FALSE) {
                
                    if($x==0){
                        $header=$getData1;
                    }
                    
                    if ($x > 0) {
                        $lineId=0;
                        foreach($header as $key=>$val){
                        
                            
                            if($key==0 || $key==1){
                               // $lineId=$val;
                            }else{
                                $details = $this->db->query("SELECT * FROM srp_erp_migration_config WHERE documentID='" . $docID . "' AND companyID = {$companyID} AND 	tempColoumnName='".$val."' AND isExcel=1 order by sortOrder asc")->row_array();

                                $dataExcel2['migrationHeaderMasterID'] =  $last_id;
                                $dataExcel2['migrationConfMasterID'] = $details['migrationConfigID'];
                                $dataExcel2['value'] = $getData1[$key];
                                $dataExcel2['excelLineID'] = $getData1[0];
                                $dataExcel2['headerReferenceID'] = $getData1[1];
    
                                $dataExcel3[]=$dataExcel2;
                            }

                        }

                    }
                    $x++;
                }

                fclose($filed);


                if (!empty($dataExcel3)) {
                    $result = $this->db->insert_batch('srp_erp_migration_header_details', $dataExcel3);
                
                    if ($result) {
                       // $this->session->set_flashdata('s', 'Successfully Updated');
                      //  echo json_encode(array('status' => true,'header' => $required_header));
                       
                        echo json_encode(['s', 'Successfully Updated']);
                    } else {
                        echo json_encode(['e', 'Upload Failed']);
                    }
                } else {
                    echo json_encode(['e', 'No records in the uploaded file']);
                }
            }else{
                echo json_encode(['e', 'Required file format incorrect']);
            }
        } else {
            echo json_encode(['e', 'No Files Attached']);
        }
    }


    function downloadExcel()
    {

        $companyID = current_companyID();

        $documentID = trim($this->input->post('documentID') ?? '');
        $isdocumentType = trim($this->input->post('isdocumentType') ?? '');
        

        if($isdocumentType==1){
            $documentType = trim($this->input->post('documentType') ?? '');
            $details = $this->db->query("SELECT * FROM srp_erp_migration_config WHERE documentID='" . $documentID . "' AND documentType='" . $documentType . "' AND companyID = {$companyID} AND isExcel=1 order by sortOrder asc")->result_array();
        }else{
            $details = $this->db->query("SELECT * FROM srp_erp_migration_config WHERE documentID='" . $documentID . "' AND companyID = {$companyID} AND isExcel=1 order by sortOrder asc")->result_array();
        }

        $csv_data = [];
        $csv_data[0]='LineID';
        $csv_data[1]='headerReferenceID';

        if (!empty($details)) {
            foreach ($details as $row) {
                $csv_data[] = $row['tempColoumnName'];
            }
        }

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=Migration.csv");


        $output = fopen("php://output", "w");
     
        fputcsv($output, $csv_data);

        // foreach ($csv_data as $row) {
        //     fputcsv($output, $row);
        // }
        fclose($output);
    }


    public function fetch_migration_submission()
    {
        
        $this->datatables->select('migrationHeaderMasterID, documentType, documentTypeName, documentID,	createdDateTime,noOfRecords,status,isValidated')
            ->from('srp_erp_migration_header AS t1')
            ->add_column('doc_type', '$1', 'add_doc_type(documentType,documentTypeName)')
            ->add_column('edit', '$1', 'action_migration_header(migrationHeaderMasterID,isValidated,documentID)')
            ->add_column('status', '$1', 'status_migration_header(isValidated)')
            ->where('t1.companyID', current_companyID())
            ->where('t1.isMigration',1);

        echo $this->datatables->generate();
    }

    public function fetch_excel_upload_migration_details(){

        $migrationHeaderMasterID = $this->input->post('migrationHeaderMasterID');
        $companyID = current_companyID();

        $this->db->select('*');
        $this->db->from('srp_erp_migration_header');
        $this->db->where('migrationHeaderMasterID', trim($migrationHeaderMasterID));
        $master = $this->db->get()->row_array();

        if($master){

            //genarate Table header
            $migtation_config_details = $this->db->query("SELECT * FROM srp_erp_migration_config WHERE documentID='" . $master['documentID'] . "' AND companyID = {$companyID} AND isExcel=1 order by sortOrder asc")->result_array();

            $header_data = [];
            $header_data[0]='LineID';
            $header_data[1]='headerReferenceID';
    
            if (!empty($migtation_config_details)) {
                foreach ($migtation_config_details as $row) {
                    $header_data[] = $row['tempColoumnName'];
                }
            }
            $data['master_data']=$master;
            $data['table_header']=$header_data;

            $csv_data=[];

            $migration_header_details=$this->db->query("SELECT detTB.excelLineID,detTB.headerReferenceID
                                        FROM  srp_erp_migration_header_details detTB
                                        WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID}
                                        GROUP BY detTB.excelLineID" )->result_array();


            if (!empty($migration_header_details)) {
                foreach ($migration_header_details as $key=>$row) {

                  
                    $migration_header_details1=$this->db->query("SELECT detTB.value,detTB.excelLineID ,detTB.headerReferenceID,hTB.documentID,hTB.noOfRecords,confTB.tempColoumnName,confTB.sortOrder
                    FROM  srp_erp_migration_header_details detTB
                    JOIN srp_erp_migration_header hTB ON hTB.migrationHeaderMasterID=detTB.migrationHeaderMasterID
                    JOIN srp_erp_migration_config confTB ON confTB.migrationConfigID=detTB.migrationConfMasterID
                    WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID} AND
                    detTB.excelLineID ={$row['excelLineID']} order by confTB.sortOrder asc" )->result_array();

                 //  print_r($migration_header_details1);exit;

                    if($migration_header_details1){

                        $line =[];
                        $line[0]=$row['excelLineID'];
                        $line[1]=$row['headerReferenceID'];

                        foreach($migration_header_details1 as $key1=>$val){
                            $line[]=$val['value'];
                           
                        }
                        $csv_data[$key+1]=$line;
                    }

                    
                }

               
            }
            $data['table_body']=$csv_data;
           // print_r($migration_header_details);exit;



        }

        $this->load->view('system/migration/ajax/maigration_table_edit', $data);

    }



    public function post_validated_excel_data(){
      
        echo json_encode($this->Migration_document_model->post_validated_excel_data());

    }

    public function post_validated_edit_em_excel_data(){
        $tempEmpAutoID = $this->input->post('id');

        $EmpSecondaryCode = $this->input->post('EmpSecondaryCode');
        $emp_title = $this->input->post('emp_title');
        $shortName = $this->input->post('shortName');
        $initial = $this->input->post('initial');
        $fullName = $this->input->post('fullName');
        $Ename3 = $this->input->post('Ename3');
        $emp_gender = $this->input->post('emp_gender');

        $Nationality = $this->input->post('Nationality');
        $religion = $this->input->post('religion');
        $MaritialStatus = $this->input->post('MaritialStatus');
        $empDob = $this->input->post('empDob');
        $BloodGroup = $this->input->post('BloodGroup');
        $emp_email = $this->input->post('emp_email');
        $NIC = $this->input->post('NIC');
        $confirmDate = $this->input->post('confirmDate');

        $ep_address1 = $this->input->post('ep_address1');
        $ep_address2 = $this->input->post('ep_address2');
        $ep_address3 = $this->input->post('ep_address3');
        $ep_address4 = $this->input->post('ep_address4');
        $personalEmail = $this->input->post('personalEmail');
        $emp_mobile = $this->input->post('emp_mobile');

        $empDoj = $this->input->post('empDoj');
        $dateAssumed = $this->input->post('dateAssumed');
        $employeeConType = $this->input->post('employeeConType');
        $empCurrency = $this->input->post('empCurrency');
        $empSegment = $this->input->post('empSegment');
        $probationPeriod = $this->input->post('probationPeriod');
        $isPayrollEmployee = $this->input->post('isPayrollEmployee');
        $pass_portNo = $this->input->post('pass_portNo');
        $passPort_expiryDate = $this->input->post('passPort_expiryDate');
        $visa_expiryDate = $this->input->post('visa_expiryDate');
        $airport_destination = $this->input->post('airport_destination');

        $designationID = $this->input->post('designationID');
        $startDate = $this->input->post('startDate');

        $items = $this->input->post('items');
        $managerID = $this->input->post('managerID');
        
        $accHolder = $this->input->post('accHolder');
        $bank_id = $this->input->post('bank_id');
        $branch_id = $this->input->post('branch_id');
        $accountNo = $this->input->post('accountNo');
        $salPerc = $this->input->post('salPerc');
        $payrollType = $this->input->post('payrollType');

        $leaveGroupID = $this->input->post('leaveGroupID');


        $emp_data_arr=[];

        
       // print_r($tempEmpAutoID);exit;

        foreach ($tempEmpAutoID as $key => $search) {
            $data_arr=[];
            $details_arr=[];
            $details_arr_contact=[];
            $details_arr_employment=[];
            $details_arr_designation=[];
            $details_arr_department=[];
            $details_arr_bank=[];
            $details_arr_ref_mg=[];
            $details_arr_leave_group=[];

            $data_arr['EmpSecondaryCode']=$EmpSecondaryCode[$key];
            $data_arr['emp_title']=$emp_title[$key];
            $data_arr['shortName']=$shortName[$key];
            $data_arr['Ename4']=$shortName[$key];
            $data_arr['initial']=$initial[$key];
            $data_arr['fullName']=$fullName[$key];
            $data_arr['Ename3']=$Ename3[$key];
            $data_arr['emp_gender']=$emp_gender[$key];
            $data_arr['Nationality']=$Nationality[$key];
            $data_arr['religion']=$religion[$key];
            $data_arr['MaritialStatus']=$MaritialStatus[$key];
            $data_arr['empDob']=$empDob[$key];
            $data_arr['BloodGroup']=$BloodGroup[$key];

            $data_arr['emp_email']=$emp_email[$key];
            $data_arr['NIC']=$NIC[$key];
            $data_arr['confirmDate']=$confirmDate[$key];
            $data_arr['header_post_url']='Employee/new_employee';

            //update employe contact details
            $details_arr_contact['details_url']='Employee/contactDetails_update';
            $details_arr_contact['ep_address1']=$ep_address1[$key];
            $details_arr_contact['ep_address2']=$ep_address2[$key];
            $details_arr_contact['ep_address3']=$ep_address3[$key];
            $details_arr_contact['ep_address4']=$ep_address4[$key];
            $details_arr_contact['ec_address4']=$ep_address4[$key];
            $details_arr_contact['personalEmail']=$personalEmail[$key];
            $details_arr_contact['emp_mobile']=$emp_mobile[$key];

            //update employment details
            $details_arr_employment['details_url']='Employee/save_employmentData_envoy';
            $details_arr_employment['empDoj']=$empDoj[$key];
            $details_arr_employment['dateAssumed']=$dateAssumed[$key];
            $details_arr_employment['employeeConType']=$employeeConType[$key];
            $details_arr_employment['empCurrency']=$empCurrency[$key];
            $details_arr_employment['empSegment']=$empSegment[$key];
            $details_arr_employment['probationPeriod']=$probationPeriod[$key];
            $details_arr_employment['isPayrollEmployee']=$isPayrollEmployee[$key];
            $details_arr_employment['pass_portNo']=$pass_portNo[$key];
            $details_arr_employment['passPort_expiryDate']=$passPort_expiryDate[$key];
            $details_arr_employment['visa_expiryDate']=$visa_expiryDate[$key];
            $details_arr_employment['airport_destination']=$airport_destination[$key];

            //set emp designation details
            $details_arr_designation['details_url']='Employee/save_empDesignations';
            $details_arr_designation['designationID']=$designationID[$key];
            $details_arr_designation['startDate']=$startDate[$key];
            $details_arr_designation['isMajor']=1;

            //set emp departments
            $details_arr_department['details_url']='Employee/save_empDepartments';
            $details_arr_department['items']=$items[$key];

            //set emp bank details
            $details_arr_bank['details_url']='Employee/save_empBankAccounts';
            $details_arr_bank['bank_id']=$bank_id[$key];
            $details_arr_bank['branch_id']=$branch_id[$key];
            $details_arr_bank['accHolder']=$accHolder[$key];
            $details_arr_bank['accountNo']=$accountNo[$key];
            $details_arr_bank['salPerc']=$salPerc[$key];
            $details_arr_bank['payrollType']=$payrollType[$key];

            // emp reporting manager
            $details_arr_ref_mg['details_url']='Employee/save_reportingManager';
            $details_arr_ref_mg['managerID']=$managerID[$key];

            //emp leave group
            $details_arr_bank['details_url']='Employee/save_reportingManager';
            $details_arr_bank['managerID']=$managerID[$key];

            //emp leave group
            $details_arr_leave_group['details_url']='Employee/save_attendanceData';
            $details_arr_leave_group['leaveGroupID']=$leaveGroupID[$key];


            $details_arr[]=$details_arr_contact;
            $details_arr[]=$details_arr_employment;
            $details_arr[]=$details_arr_designation;
            $details_arr[]=$details_arr_department;
            $details_arr[]=$details_arr_bank;
            $details_arr[]=$details_arr_ref_mg;
            $details_arr[]=$details_arr_leave_group;

            $data_arr['emp_details']=$details_arr;
            $emp_data_arr[]=$data_arr;
        }

       
        echo json_encode($emp_data_arr);
       // print_r($emp_data_arr);exit;
    }

    public function fetch_excel_upload_migration_details_edit(){

        $migrationHeaderMasterID = trim($this->input->post('migrationHeaderMasterID') ?? '');
        $companyID = current_companyID();
      
        $details=$this->Migration_document_model->fetch_excel_upload_migration_details_edit();
        $date_format_policy = date_format_policy();

        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_employeesdetails_migration_temp.migrationHeaderMasterID = " . $migrationHeaderMasterID .  "";
        $this->datatables->select('EIdNo,excelLineID, serialNo, ECode,accHolder,accountNo,salPerc,bank_id,branch_id,payrollType, EmpSecondaryCode,managerID,startDate, EmpTitleId, manPowerNo, ssoNo, EmpDesignationId, Ename1, Ename2, AirportDestinationID, Ename3, Ename4, empSecondName, EFamilyName, initial, EmpShortCode, Enameother1, Enameother2, Enameother3, Enameother4, empSecondNameOther, EFamilyNameOther, empSignature, EmpImage, EthumbnailImage, Gender, payee_emp_type, EpAddress1, EpAddress2, EpAddress3, EpAddress4, countryID, ZipCode, EpTelephone, EpFax, EpMobile, EcAddress1, EcAddress2, EcAddress3,EcAddress4, EcPOBox, EcPC, EcArea, EcTel, EcExtension, EcFax, EcMobile, EEmail, personalEmail, EDOB, EDOJ, NIC, insuranceNo, EPassportNO, EPassportExpiryDate, EVisaExpiryDate, Nid, Rid, AirportDestination, travelFrequencyID, commissionSchemeID, medicalInfo,SchMasterId, branchID, userType, isSystemUserYN, UserName, Password, isDeleted, HouseID, HouseCatID, HPID, isPayrollEmployee, payCurrencyID, payCurrency, isLeft, DateLeft, LeftComment, BloodGroup, DateAssumed, probationPeriod, isDischarged, dischargedByEmpID, EmployeeConType, dischargedDate, lastWorkingDate, gratuityCalculationDate, dischargedComment, finalSettlementDoneYN,MaritialStatus, Nationality, isLoginAttempt, isChangePassword, CreatedUserName, CreatedDate, CreatedPC, ModifiedUserName, Timestamp, ModifiedPC, isActive, NoOfLoginAttempt, languageID, locationID, sponsorID, mobileCreditLimit, segmentID, Erp_companyID, floorID, deviceID, empMachineID, leaveGroupID, isMobileCheckIn, isCheckin, token, overTimeGroup, familyStatusID, gratuityID, isSystemAdmin, isHRAdmin, contractStartDate, contractEndDate, contractRefNo, empConfirmDate, empConfirmedYN, rejoinDate,previousEmpID, gradeID, pos_userGroupMasterID, pos_userGroupMasterID_gpos, pos_barCode, isLocalPosSyncEnable,isLocalPosSalesRptEnable, tibianType, LocalPOSUserType, last_login, visaPartyType, visaPartyID, migrationHeaderMasterID')
            ->where($where)
            ->from('srp_employeesdetails_migration_temp');
        $this->datatables->add_column('id', '<input type="hidden" value="$1" class="form-control id" name="id[]">', 'EIdNo');
        $this->datatables->add_column('EmpSecondaryCode', '<input type="text" value="$1" class="form-control itemAutoIDhn" name="EmpSecondaryCode[]">', 'EmpSecondaryCode');
        $this->datatables->add_column('emp_title', '$1', 'make_emp_title_dropDown(EmpTitleId)');
        $this->datatables->add_column('shortName', '<input type="text" value="$1" class="form-control shortName" name="shortName[]">', 'EmpShortCode');

        $this->datatables->add_column('initial', '<input type="text" value="$1" class="form-control initial" name="initial[]">', 'initial');
        $this->datatables->add_column('fullName', '<input type="text" value="$1" class="form-control fullName" name="fullName[]">', 'Ename1');
        $this->datatables->add_column('Ename3', '<input type="text" value="$1" class="form-control Ename3" name="Ename3[]">', 'Ename3');
        //$this->datatables->add_column('emp_gender', '<input type="radio" value="$1" class="form-control gender" name="emp_gender[]">', 'Gender');
        $this->datatables->add_column('emp_gender', '$1', 'make_gender_dropDown(Gender)');
        $this->datatables->add_column('Nationality', '$1', 'make_Nationality_dropDown(Nid)');
        $this->datatables->add_column('religion', '$1', 'make_religion_dropDown(Rid)');
        $this->datatables->add_column('MaritialStatus', '$1', 'make_MaritialStatus_dropDown(MaritialStatus)');
        $this->datatables->add_column('empDob', '$1', 'make_date_dropDown(EDOB,empDob[])');
        $this->datatables->add_column('BloodGroup', '$1', 'make_BloodGroup_dropDown(BloodGroup)');

        $this->datatables->add_column('emp_email', '<input type="text" value="$1" class="form-control emp_email" name="emp_email[]">', 'EEmail');
        $this->datatables->add_column('NIC', '<input type="text" value="$1" class="form-control NIC" name="NIC[]">', 'NIC');
        $this->datatables->add_column('confirmDate', '<input type="text" value="$1" class="form-control confirmDate" name="confirmDate[]">', 'empConfirmDate');

        $this->datatables->add_column('ep_address1', '<input type="text" value="$1" class="form-control ep_address1" name="ep_address1[]">', 'EpAddress1');
        $this->datatables->add_column('ep_address2', '<input type="text" value="$1" class="form-control ep_address2" name="ep_address2[]">', 'EpAddress2');
        $this->datatables->add_column('ep_address3', '<input type="text" value="$1" class="form-control ep_address3" name="ep_address3[]">', 'EpAddress3');

        $this->datatables->add_column('ep_address4', '$1', 'make_EpAddress4_dropDown(EpAddress4)');

        $this->datatables->add_column('personalEmail', '<input type="text" value="$1" class="form-control personalEmail" name="personalEmail[]">', 'personalEmail');
        $this->datatables->add_column('emp_mobile', '<input type="text" value="$1" class="form-control emp_mobile" name="emp_mobile[]">', 'EcMobile');

        $this->datatables->add_column('empDoj', '$1', 'make_date_dropDown(EDOJ,empDoj[])');
        $this->datatables->add_column('dateAssumed', '$1', 'make_date_dropDown(DateAssumed,dateAssumed[])');

        $this->datatables->add_column('employeeConType', '$1', 'make_employeeConType_dropDown(EmployeeConType)');
        $this->datatables->add_column('empCurrency', '$1', 'make_empCurrency_dropDown(payCurrencyID)');
        $this->datatables->add_column('empSegment', '$1', 'make_empSegment_dropDown(segmentID)');
        $this->datatables->add_column('probationPeriod', '$1', 'make_date_dropDown(probationPeriod,probationPeriod[])');
       // $this->datatables->add_column('', '<input type="radio" value="$1" class="form-control isPayrollEmployee" name="isPayrollEmployee[]">', 'isPayrollEmployee');
       $this->datatables->add_column('managerID', '$1', 'make_manager_dropDown(managerID)');
        $this->datatables->add_column('isPayrollEmployee', '$1', 'make_isPayrollEmployee_dropDown(isPayrollEmployee)');
        $this->datatables->add_column('pass_portNo', '<input type="text" value="$1" class="form-control pass_portNo" name="pass_portNo[]">', 'EPassportNO');

        $this->datatables->add_column('passPort_expiryDate', '$1', 'make_date_dropDown(EPassportExpiryDate,passPort_expiryDate[])');
        $this->datatables->add_column('visa_expiryDate', '$1', 'make_date_dropDown(EVisaExpiryDate,visa_expiryDate[])');
        $this->datatables->add_column('airport_destination', '<input type="text" value="$1" class="form-control airport_destination" name="airport_destination[]">', 'AirportDestination');
        $this->datatables->add_column('designationID', '$1', 'make_designationID_dropDown(designationID)');

        $this->datatables->add_column('startDate', '$1', 'make_date_dropDown(startDate,startDate[])');
   
        $this->datatables->add_column('items', '$1', 'make_department_dropDown(departmentID)');
        
        $this->datatables->add_column('accHolder', '<input type="text" value="$1" class="form-control accHolder" name="accHolder[]">', 'accHolder');

        $this->datatables->add_column('bank', '$1', 'make_bank_dropDown(bank_id,EIdNo)');
        $this->datatables->add_column('branch', '$1', 'make_bank_branch_dropDown(branch_id,bank_id,EIdNo)');

        $this->datatables->add_column('accountNo', '<input type="text" value="$1" class="form-control accountNo" name="accountNo[]">', 'accountNo');
        $this->datatables->add_column('salPerc', '<input type="text" value="$1" class="form-control salPerc" name="salPerc[]">', 'salPerc');
       // $this->datatables->add_column('payrollType', '<input type="text" value="$1" class="form-control payrollType" name="payrollType[]">', 'payrollType');
        $this->datatables->add_column('payrollType', '$1', 'make_payroll_dropDown(payrollType)');
        $this->datatables->add_column('leaveGroupID', '$1', 'make_leaveGroupID_dropDown(leaveGroupID)');
        //print_r($details);exit;
        echo $this->datatables->generate();

    }

    public function fetch_header_migration_details(){
      
        echo json_encode($this->Migration_document_model->fetch_header_migration_details());

    }
    

    public function fetchbankBranches()
    {
        echo json_encode($this->Migration_document_model->fetchbankBranches());
    }

    public function validated_excel_data(){
      
        echo json_encode($this->Migration_document_model->validated_excel_data());

    }

    public function fetch_document_type(){
      
        echo json_encode($this->Migration_document_model->fetch_document_type());

    }

    public function delete_migration_recode(){
      
        echo json_encode($this->Migration_document_model->delete_migration_recode());

    }

    function fetch_view_validated_errors(){
        $this->datatables->select('autoID,excelLineID,tempColoumnName,Errormessage')
            ->where('migrationHeaderMasterID', $this->input->post('migrationHeaderMasterID'))
            ->from('srp_erp_migration_validation_results');
            echo $this->datatables->generate();
    }

}