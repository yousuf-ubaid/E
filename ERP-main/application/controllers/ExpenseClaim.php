<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ExpenseClaim extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Expense_claim_modal');
        $this->load->helpers('expense_claim');
    }

    function fetch_expanse_claim()
    {
        // date inter change according to company policy
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $claimedByEmpID = current_userID();
        //$supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $supplier_filter = '';
        /*if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }*/
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( expenseClaimDate >= '" . $datefromconvert . " 00:00:00' AND expenseClaimDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . " And claimedByEmpID = " . $claimedByEmpID . $date . $status_filter . "";
        $convertFormat = convert_date_format_sql();
        $company_reporting_currency=$this->common_data['company_data']['company_reporting_currency'];
        $company_reporting_DecimalPlaces=$this->common_data['company_data']['company_reporting_decimal'];
        $this->datatables->select("srp_erp_expenseclaimmaster.expenseClaimMasterAutoID as expenseClaimMasterAutoID,expenseClaimCode,comments,claimedByEmpName,confirmedYN,approvedYN ,DATE_FORMAT(expenseClaimDate,'$convertFormat') AS expenseClaimDate,createdUserID,det.transactionAmount as total_value,det.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,det.companyLocalCurrency as companyLocalCurrency,srp_employeesdetails.payCurrency as empCurrency,addedForPayment,addedToSalary");
        $this->datatables->join('(SELECT SUM(companyLocalAmount) as transactionAmount,expenseClaimMasterAutoID,companyLocalCurrencyDecimalPlaces,companyLocalCurrency FROM srp_erp_expenseclaimdetails GROUP BY expenseClaimMasterAutoID) det', '(det.expenseClaimMasterAutoID = srp_erp_expenseclaimmaster.expenseClaimMasterAutoID)', 'left');
        $this->datatables->join('srp_employeesdetails ', 'srp_erp_expenseclaimmaster.claimedByEmpID = srp_employeesdetails.EIdNo');
        $this->datatables->from('srp_erp_expenseclaimmaster');
        $this->datatables->add_column('Ec_detail', '<b>Claimed By Name : </b> $1 <br> <b>Claimed Date : </b> $2 <br><b>Description : </b> $3', 'claimedByEmpName,expenseClaimDate,comments');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,companyLocalCurrencyDecimalPlaces),companyLocalCurrency');
        $this->datatables->where($where);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"EC",expenseClaimMasterAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_EC(approvedYN,confirmedYN,"EC",expenseClaimMasterAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_EC_action(expenseClaimMasterAutoID,confirmedYN,approvedYN,createdUserID,addedForPayment,addedToSalary)');
        echo $this->datatables->generate();
    }

    function save_expense_claim_header()
    {
        $this->form_validation->set_rules('comments', 'Description', 'trim|required');
        $this->form_validation->set_rules('expenseClaimDate', 'Expense Claim Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('segmentID', 'Segment', 'trim|required');
        $this->form_validation->set_rules('claimedByEmpID', 'Employee', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Expense_claim_modal->save_expense_claim_header());
        }
    }

    function load_expense_claim_header()
    {
        echo json_encode($this->Expense_claim_modal->load_expense_claim_header());
    }

    function fetch_calim_category()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "companyID = " . $companyid . "";
        $this->datatables->select("srp_erp_expenseclaimcategories.expenseClaimCategoriesAutoID as expenseClaimCategoriesAutoID,claimcategoriesDescription,glCode,glCodeDescription");
        $this->datatables->from('srp_erp_expenseclaimcategories');
        $this->datatables->add_column('Ec_detail', ' $1 - $2 ', 'glCode,glCodeDescription');
        $this->datatables->where($where);
        $this->datatables->add_column('edit', '$1', 'load_claim_category_action(expenseClaimCategoriesAutoID)');
        echo $this->datatables->generate();
    }

    function save_expense_claim_category()
    {
        $this->form_validation->set_rules('claimcategoriesDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('glAutoID', 'GL Code', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Expense_claim_modal->save_expense_claim_category());
        }
    }

    function editClaimCategory(){
        echo json_encode($this->Expense_claim_modal->editClaimCategory());
    }


    function save_expense_claim_detail()
    {

        $expenseClaimCategoriesAutoID = $this->input->post('expenseClaimCategoriesAutoID');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');

        foreach ($expenseClaimCategoriesAutoID as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("expenseClaimCategoriesAutoID[{$key}]", 'Claim Category', 'trim|required');
            $this->form_validation->set_rules("description[{$key}]", 'Description', 'trim|required');
            $this->form_validation->set_rules("referenceNo[{$key}]", 'Doc Ref', 'trim|required');
            $this->form_validation->set_rules("transactionCurrencyID[{$key}]", 'Currency', 'trim|required');
            $this->form_validation->set_rules("segmentIDDetail[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("transactionAmount[{$key}]", 'Amount', 'trim|required|greater_than[0]');
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
           // echo"oky";
            echo json_encode($this->Expense_claim_modal->save_expense_claim_detail());
        }
    }

    function fetch_Ec_detail_table()
    {
        echo json_encode($this->Expense_claim_modal->fetch_Ec_detail_table());
    }

    function fetch_expense_claim_detail()
    {
        echo json_encode($this->Expense_claim_modal->fetch_expense_claim_detail());
    }

    function update_expense_claim_detail()
    {
        $this->form_validation->set_rules("expenseClaimCategoriesAutoIDEdit", 'Claim Category', 'trim|required');
        $this->form_validation->set_rules("descriptionEdit", 'Description', 'trim|required');
        $this->form_validation->set_rules("referenceNoEdit", 'Doc Ref', 'trim|required');
        $this->form_validation->set_rules("transactionCurrencyIDEdit", 'Currency', 'trim|required');
        $this->form_validation->set_rules("transactionAmountEdit", 'Amount', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules("segmentIDDetailEdit", 'Segment', 'trim|required|greater_than[0]');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Expense_claim_modal->update_expense_claim_detail());
        }
    }

    function delete_expense_claim_detail()
    {
        echo json_encode($this->Expense_claim_modal->delete_expense_claim_detail());
    }

    function load_expense_claim_conformation(){
        $expenseClaimMasterAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('expenseClaimMasterAutoID') ?? '');
        $data['extra'] = $this->Expense_claim_modal->fetch_template_data($expenseClaimMasterAutoID);

        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/expenseClaim/erp_expense_claim_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function expense_claim_confirmation()
    {
        $expenseClaimMasterAutoID = trim($this->input->post('expenseClaimMasterAutoID') ?? '');

        $this->db->select('claimedByEmpID');
        $this->db->where('expenseClaimMasterAutoID', $expenseClaimMasterAutoID);
        $this->db->from('srp_erp_expenseclaimmaster');
        $empid = $this->db->get()->row_array();
    
        if($empid){
            $this->db->select('managerID');
            $this->db->where('empID', trim($empid['claimedByEmpID'] ?? ''));
            $this->db->where('active', 1);
            $this->db->from('srp_erp_employeemanagers');
            $managerid = $this->db->get()->row_array();
     // Perform the update query
     $this->db->set('selectedYN', 1);
     $this->db->where('expenseClaimMasterAutoID', $expenseClaimMasterAutoID);
     $this->db->update('srp_erp_expenseclaimdetails');

     // Call the expense claim confirmation method and output the result
     echo json_encode($this->Expense_claim_modal->expense_claim_confirmation());
            // if(!empty($managerid)){
            //     // Perform the update query
            //     $this->db->set('selectedYN', 1);
            //     $this->db->where('expenseClaimMasterAutoID', $expenseClaimMasterAutoID);
            //     $this->db->update('srp_erp_expenseclaimdetails');
    
            //     // Call the expense claim confirmation method and output the result
            //     echo json_encode($this->Expense_claim_modal->expense_claim_confirmation());
            // } else {
            //     echo json_encode(array('w', 'Reporting manager not available for this employee'));
            // }
        }
    }
    

    function referback_expense_claim()
    {
        $expenseClaimMasterAutoID = $this->input->post('expenseClaimMasterAutoID');
        $data = array(
            'currentLevelNo' => 1,
            'confirmedYN' => 0,
            'confirmedDate' => null,
            'confirmedByEmpID' => null,
            'confirmedByName' => null,
        );
        $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
        $status= $this->db->update('srp_erp_expenseclaimmaster', $data);

        //remove document approved 
        $this->load->library('Approvals');
        $status = $this->approvals->approve_delete($this->input->post('expenseClaimMasterAutoID'), 'EC');

        if ($status) {
            $expenseClaimMasterAutoID = trim($this->input->post('expenseClaimMasterAutoID') ?? '');
            $this->db->select('*');
            $this->db->where('expenseClaimMasterAutoID', $expenseClaimMasterAutoID);
            $this->db->from('srp_erp_expenseclaimmaster');
            $ec_data = $this->db->get()->row_array();

            /*** Firebase Mobile Notification*/
            $this->db->select('managerID');
            $this->db->where('empID', trim($ec_data['claimedByEmpID'] ?? ''));
            $this->db->where('active', 1);
            $this->db->from('srp_erp_employeemanagers');
            $managerid = $this->db->get()->row_array();

            $token_android = firebaseToken($managerid["managerID"], 'android');
            $token_ios = firebaseToken($managerid["managerID"], 'apple');

            $firebaseBody = $ec_data['claimedByEmpName'] . " has referred back his expense claim.";

            $this->load->library('firebase_notification');
            if(!empty($token_android)) {
                $this->firebase_notification->sendFirebasePushNotification("Expense Claim Approval Referred Back", $firebaseBody, $token_android, 6, $ec_data['expenseClaimCode'], "EC", $expenseClaimMasterAutoID, "android");
            }
            if(!empty($token_ios)) {
                $this->firebase_notification->sendFirebasePushNotification("Expense Claim Approval Referred Back", $firebaseBody, $token_ios, 6, $ec_data['expenseClaimCode'], "EC", $expenseClaimMasterAutoID, "apple");
            }

            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }

    }

    function delete_expense_claim(){
        $status=$this->db->delete('srp_erp_expenseclaimmaster', array('expenseClaimMasterAutoID' => trim($this->input->post('expenseClaimMasterAutoID') ?? '')));
        if($status){
            $this->db->delete('srp_erp_expenseclaimdetails', array('expenseClaimMasterAutoID' => trim($this->input->post('expenseClaimMasterAutoID') ?? '')));
            echo json_encode(array('s', ' Deleted Successfully.', $status));
        }else {
            echo json_encode(array('e', ' Error in Deletion.', $status));
        }
    }

    function fetch_expanse_claim_approval()
    {
        // date inter change according to company policy
        $date_format_policy = date_format_policy();


        $companyid = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $empID=current_userID();
        $convertFormat = convert_date_format_sql();

        $query = $this->db->query("SELECT approvalType FROM srp_erp_documentcodemaster WHERE documentID = 'EC' AND companyID = {$companyid}");
        $approvalType = $query->row_array();

        $company_reporting_currency=$this->common_data['company_data']['company_reporting_currency'];
        $company_reporting_DecimalPlaces=$this->common_data['company_data']['company_reporting_decimal'];
        $this->datatables->select("srp_erp_expenseclaimmaster.expenseClaimMasterAutoID as expenseClaimMasterAutoID,expenseClaimCode,comments,claimedByEmpName,srp_erp_expenseclaimmaster.confirmedYN,srp_erp_expenseclaimmaster.approvedYN ,DATE_FORMAT(expenseClaimDate,'$convertFormat') AS expenseClaimDate,srp_erp_expenseclaimmaster.createdUserID,det.empCurrency as empCurrency,det.transactionAmount as total_value");
        $this->datatables->join('(SELECT SUM(empCurrencyAmount) as transactionAmount,expenseClaimMasterAutoID,empCurrency FROM srp_erp_expenseclaimdetails GROUP BY expenseClaimMasterAutoID) det', '(det.expenseClaimMasterAutoID = srp_erp_expenseclaimmaster.expenseClaimMasterAutoID)', 'left');
      //  $this->datatables->join('srp_erp_employeemanagers ', 'srp_erp_expenseclaimmaster.claimedByEmpID = srp_erp_employeemanagers.empID');
        $this->datatables->from('srp_erp_expenseclaimmaster');
        $this->datatables->join('srp_erp_documentapproved AS approve', 'approve.documentSystemCode = srp_erp_expenseclaimmaster.expenseClaimMasterAutoID AND approve.approvalLevelID = srp_erp_expenseclaimmaster.currentLevelNo');

        $this->datatables->join('(SELECT expenseClaimCategoriesAutoID, expenseClaimMasterAutoID FROM srp_erp_expenseclaimdetails WHERE companyID = \'' . $companyid . '\' GROUP BY expenseClaimMasterAutoID) AS Category', 'srp_erp_expenseclaimmaster.expenseClaimMasterAutoID = Category.expenseClaimMasterAutoID', 'left');


        if($approvalType['approvalType']==5){
            $this->datatables->join('srp_erp_approvalusers AS ap', 'ap.levelNo = srp_erp_expenseclaimmaster.currentLevelNo AND Category.expenseClaimCategoriesAutoID = ap.typeID AND srp_erp_expenseclaimmaster.specificUserYN=ap.specificUser', 'left');
        }
        else{
            $this->datatables->join('srp_erp_approvalusers AS ap', 'ap.levelNo = srp_erp_expenseclaimmaster.currentLevelNo');
        }

        $this->datatables->join('srp_erp_appoval_specific_users AS spec', 'spec.empID = srp_erp_expenseclaimmaster.claimedByEmpID', 'left');
    
        // $this->datatables->join('srp_erp_appoval_specific_users AS asu','ap.approvalUserID = asu.approvalUserID 
        //     AND asu.empID = srp_erp_expenseclaimmaster.claimedByEmpID','left');


        // $this->datatables->join('srp_erp_approvalusers AS ap1','ap1.approvalUserID = asu.approvalUserID 
        //     AND asu.empID = srp_erp_expenseclaimmaster.claimedByEmpID AND ap1.approvalUserID=asu.approvalUserID','left');

        $this->datatables->where('approve.documentID', 'EC');
        $this->datatables->where('ap.documentID', 'EC');
        $this->datatables->where('srp_erp_expenseclaimmaster.companyID', $companyid);
        $this->datatables->where('ap.companyID', $companyid);
        $this->datatables->where('approve.companyID', $companyid);
        $this->datatables->where('srp_erp_expenseclaimmaster.confirmedYN', 1);
        $this->datatables->where('srp_erp_expenseclaimmaster.approvedYN', $approvedYN);
        $this->datatables->where("((srp_erp_expenseclaimmaster.specificUserYN =1 AND (ap.approvalUserID=spec.approvalUserID)) OR (srp_erp_expenseclaimmaster.specificUserYN =0))");
        // $this->datatables->where('asu.approvalUserID = ap.approvalUserID');
        
        $this->datatables->where("(  (
	`ap`.`employeeID` = '{$empID}' 
	OR (
	ap.employeeID = - 1 
	AND srp_erp_expenseclaimmaster.claimedByEmpID IN (
SELECT
	emp_manager.empID 
FROM
	srp_employeesdetails AS emp_detail
	JOIN srp_erp_employeemanagers AS emp_manager ON emp_detail.EIdNo = emp_manager.empID 
	AND `emp_manager`.`active` = 1 
	AND `emp_manager`.`companyID` = '{$companyid}' 
	AND emp_manager.managerID = '{$empID}' 
	) 
	) 
	OR (
	ap.employeeID = - 2 
	AND srp_erp_expenseclaimmaster.claimedByEmpID IN (
SELECT
	emp_detail.EIdNo 
FROM
	srp_employeesdetails AS emp_detail
	JOIN srp_empdepartments AS emp_dep ON emp_detail.EIdNo = emp_dep.EmpID
	JOIN srp_departmentmaster AS srp_dep ON emp_dep.DepartmentMasterID = srp_dep.DepartmentMasterID 
	AND `emp_dep`.`isactive` = 1 
	AND `emp_dep`.`Erp_companyID` = '{$companyid}' 
	AND srp_dep.hod_id = '{$empID}' 
	) 
	) 
	OR (
	ap.employeeID = - 3 
	AND srp_erp_expenseclaimmaster.claimedByEmpID IN (
SELECT
	emp_detail.Eidno 
FROM
	srp_employeesdetails AS emp_detail
	JOIN srp_erp_employeemanagers AS emp_manager ON emp_detail.EIdNo = emp_manager.empID
	JOIN ( SELECT * FROM srp_erp_employeemanagers ) AS top_manager ON top_manager.empID = emp_manager.managerID 
WHERE
	emp_manager.active = 1 
	AND `emp_manager`.`companyID` = '{$companyid}' 
	AND top_manager.managerID = '{$empID}' 
	) 
	) 
	) 
	) GROUP BY srp_erp_expenseclaimmaster.expenseClaimMasterAutoID");
     //   $this->datatables->where('srp_erp_employeemanagers.active', 1);
        $this->datatables->add_column('approved', '$1', 'confirm_aproval_EC(approvedYN,confirmedYN,"EC",expenseClaimMasterAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_EC_approval_action(expenseClaimMasterAutoID,confirmedYN,approvedYN,createdUserID)');
        $this->datatables->add_column('Ec_detail', '<b>Claimed By Name : </b> $1 <br> <b>Claimed Date : </b> $2 <br><b>Description : </b> $3', 'claimedByEmpName,expenseClaimDate,comments');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,'.$company_reporting_DecimalPlaces.'),empCurrency');
       
        echo $this->datatables->generate();
    }

    function save_expense_Claim_approval()
    {
        $this->form_validation->set_rules('ec_status', 'Expense Claim Status', 'trim|required');
        if($this->input->post('ec_status') ==2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }
        $this->form_validation->set_rules('expenseClaimMasterAutoID', 'Expense Claim ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Expense_claim_modal->save_expense_Claim_approval());
        }
    }

    function fetch_approval_user_modal_ec(){
        echo json_encode($this->Expense_claim_modal->fetch_approval_user_modal_ec());
    }

    function deleteClaimCategory(){
        echo json_encode($this->Expense_claim_modal->deleteClaimCategory());
    }

    function fetch_expanse_claim_hrms()
    {
        // date inter change according to company policy
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $claimedByEmpID = current_userID();
        //$supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $supplier_filter = '';
        /*if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }*/
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( expenseClaimDate >= '" . $datefromconvert . " 00:00:00' AND expenseClaimDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $date . $status_filter . "";
        $convertFormat = convert_date_format_sql();
        $company_reporting_currency=$this->common_data['company_data']['company_reporting_currency'];
        $company_reporting_DecimalPlaces=$this->common_data['company_data']['company_reporting_decimal'];
        $this->datatables->select("srp_erp_expenseclaimmaster.expenseClaimMasterAutoID as expenseClaimMasterAutoID,expenseClaimCode,comments,claimedByEmpName,confirmedYN,approvedYN ,DATE_FORMAT(expenseClaimDate,'$convertFormat') AS expenseClaimDate,createdUserID,det.transactionAmount as total_value,det.empCurrencyDecimalPlaces as empCurrencyDecimal,det.empCurrency as empCurrency");
        $this->datatables->join('(SELECT SUM(empCurrencyAmount) as transactionAmount,expenseClaimMasterAutoID,empCurrencyDecimalPlaces,empCurrency FROM srp_erp_expenseclaimdetails GROUP BY expenseClaimMasterAutoID) det', '(det.expenseClaimMasterAutoID = srp_erp_expenseclaimmaster.expenseClaimMasterAutoID)', 'left');
        $this->datatables->from('srp_erp_expenseclaimmaster');
        $this->datatables->add_column('Ec_detail', '<b>Claimed By Name : </b> $1 <br> <b>Claimed Date : </b> $2 <br><b>Description : </b> $3', 'claimedByEmpName,expenseClaimDate,comments');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,empCurrencyDecimal),empCurrency');
        $this->datatables->where($where);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"EC",expenseClaimMasterAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_EC(approvedYN,confirmedYN,"EC",expenseClaimMasterAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_Exp_claim_master_action(expenseClaimMasterAutoID,confirmedYN,approvedYN,createdUserID)');
        echo $this->datatables->generate();
    }

    function checkDetailexsist(){
        echo json_encode($this->Expense_claim_modal->checkDetailexsist());
    }

    function get_user_segemnt(){
        echo json_encode($this->Expense_claim_modal->get_user_segemnt());
    }

    function reverse_expense_claim(){
        $expenseClaimMasterAutoID = $this->input->post('expenseClaimMasterAutoID');
        $this->db->select('*');
        $this->db->from('srp_erp_expenseclaimmaster');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo=srp_erp_expenseclaimmaster.approvedByEmpID', 'LEFT');
        $this->db->where('expenseClaimMasterAutoID', $expenseClaimMasterAutoID);
        $approvedByEmpdetails = $this->db->get()->row_array();

        $bodyData = 'Expense Claim ' . $approvedByEmpdetails['expenseClaimCode'] . ' is reversed by Employee Name '. current_user().'.<br/> ';
        $param["empName"] = $approvedByEmpdetails["Ename2"];
        $param["body"] = $bodyData;

        $mailData = [
            'approvalEmpID' => $approvedByEmpdetails["EIdNo"],
            'documentCode' => $approvedByEmpdetails['expenseClaimCode'],
            'toEmail' => $approvedByEmpdetails["EEmail"],
            'subject' => 'Expense Claim reversed',
            'param' => $param
        ];

        $data = array(
            'confirmedYN' => 0,
            'confirmedDate' => null,
            'confirmedByEmpID' => null,
            'confirmedByName' => null,
            'approvedYN' => 0,
            'approvedDate' => null,
            'approvedByEmpID' => null,
            'approvedByEmpName' => null,
            'approvalComments' => null
        );
        $this->db->where('expenseClaimMasterAutoID', trim($expenseClaimMasterAutoID));
        $status= $this->db->update('srp_erp_expenseclaimmaster', $data);
        if ($status) {
            echo json_encode(array('s', 'Reversed Successfully.', $status));
            send_approvalEmail($mailData);
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }
    }
}
