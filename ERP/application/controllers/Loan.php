<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** ================================
 * -- File Name : Loan.php
 * -- Project Name : Gs_SME
 * -- Module Name : Employee Loans
 * -- Create date : 19 - May 2016
 * -- Description :
 */
class Loan extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Loan_model');
        $this->load->helper('loan_helper');
    }

    public function saveLoanCategory()
    {
        $this->form_validation->set_rules('description', 'Loan Description', 'trim|required');
        $this->form_validation->set_rules('intType', 'Loan Type', 'trim|required');
        $this->form_validation->set_rules('glCode', 'GL Code', 'trim|required');

        if ($this->input->post('intType') == 1) {
            $this->form_validation->set_rules('percentage', 'Interest Percentage', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $descpt = $this->input->post('description');
        $intTyp = $this->input->post('intType');
        $per = $this->input->post('percentage');
        $glCode = $this->input->post('glCode');
        $is_salary_advance = $this->input->post('is_salary_advance');
        $is_salary_advance = ($is_salary_advance == 1)? $is_salary_advance: 0;
        $isExist = $this->Loan_model->isAlreadyExistLoanCategory($descpt);

        if ($isExist == true) {
            die( json_encode(['e', 'This Loan Category is already exist.']));
        }

        if($is_salary_advance == 1){
            //Is salary advanced request type category already exists
            $usageCount = $this->db->query("SELECT COUNT(loanID) AS usageCount FROM srp_erp_pay_loan_category
                                    WHERE isSalaryAdvance = 1")->row('usageCount');

            if($usageCount > 0){
                die( json_encode(['e', 'Already <b>salary advance</b> type loan category is exist.']));
            }
        }

        $data = array(
            'description' => $descpt,
            'isInterestBased' => $intTyp,
            'interestPercentage' => $per,
            'GLCode' => $glCode,
            'isSalaryAdvance' => $is_salary_advance,
            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'segmentID' => 1,
            'segmentCode' => 1,
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdUserGroup' => $this->common_data['user_group'],
            'createdDateTime' => $this->common_data['current_date']
        );

        echo json_encode($this->Loan_model->saveLoanCategory($data));

    }

    public function loadLoanCat()
    {
        $this->datatables->select('loanID, description, isInterestBased, interestPercentage, IFNULL(GLCode,0) AS GLCode, GLSecondaryCode, GLDescription,
            isSalaryAdvance, IF(isSalaryAdvance=1, \'YES\', \'NO\') AS isSalaryAdvance_str', false)
            ->from('srp_erp_pay_loan_category t1')
            ->join('srp_erp_chartofaccounts t2', 't2.GLAutoID=t1.GLCode', 'left')
            ->edit_column('isInterestBased', '$1', 'convertIntType(isInterestBased)')
            ->edit_column('per', '$1', 'intPercentage(isInterestBased ,interestPercentage)')
            ->add_column('edit', '$1', 'createLoanEditView(loanID, description, isInterestBased, interestPercentage, GLCode, isSalaryAdvance)')
            ->where('t1.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    public function updateLoanCategory()
    {
        $this->form_validation->set_rules('description', 'Loan Description', 'trim|required');
        $this->form_validation->set_rules('intType', 'Loan Type', 'trim|required');
        $this->form_validation->set_rules('glCode', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('editID', 'Loan ID', 'trim|required');

        if ($this->input->post('intType') == 1) {
            $this->form_validation->set_rules('percentage', 'Interest Percentage', 'trim|required');
        }


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $descpt = trim($this->input->post('description') ?? '');
            $intTyp = $this->input->post('intType');
            $per = trim($this->input->post('percentage') ?? '');
            $glCode = $this->input->post('glCode');
            $loanID = $this->input->post('editID');
            $is_salary_advance = $this->input->post('is_salary_advance');
            $is_salary_advance = ($is_salary_advance == 1)? $is_salary_advance: 0;
            $companyID = current_companyID();

            if($is_salary_advance == 0){
                //Is used for salary advanced request loan document
                $usageCount = $this->db->query("SELECT COUNT(salaryAdvanceRequestID) AS usageCount FROM srp_erp_pay_emploan 
                                    WHERE loanCatID = {$loanID} AND salaryAdvanceRequestID <> 0")->row('usageCount');

                if($usageCount > 0){
                    die( json_encode(['e', 'Salary advance based loans processed with this loan category,<br/>You can not remove the <b>Salary Advance</b> type.']));
                }
            }
            else{
                //Is salary advanced request type category already exists
                $usageCount = $this->db->query("SELECT COUNT(loanID) AS usageCount FROM srp_erp_pay_loan_category
                                    WHERE isSalaryAdvance = 1 AND loanID <> {$loanID} AND companyID = {$companyID}")->row('usageCount');

                if($usageCount > 0){
                    die( json_encode(['e', 'Already <b>salary advance</b> type loan category is exist.']));
                }
            }



            $data = array(
                'description' => $descpt,
                'isInterestBased' => $intTyp,
                'interestPercentage' => $per,
                'isSalaryAdvance' => $is_salary_advance,
                'GLCode' => $glCode,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => $this->common_data['current_date']
            );


            $isExist = $this->Loan_model->isAlreadyExistLoanCategory($descpt);
            $numOfResult = sizeof($isExist);


            if ($isExist == true) {
                if ($isExist[0]['loanID'] == $loanID && $numOfResult == 1) {
                    echo json_encode($this->Loan_model->updateLoanCategory($data, $loanID));
                } else {
                    echo json_encode(array('e', 'This Loan Category is already exist.'));
                }
            } else {
                echo json_encode($this->Loan_model->updateLoanCategory($data, $loanID));
            }
        }
    }

    public function delete_loanCat()
    {
        // echo json_encode(array('e', 'The delete process is still in developing'));
        echo json_encode($this->Loan_model->delete_loanCat());
    }

    public function fetchLoanCategory()
    {
        echo json_encode($this->Loan_model->fetchLoanCategory());
    }

    public function loanDateValidate($deductionDate)
    {
        $date_format_policy = date_format_policy();
        $loanDate = $this->input->post('loanDate');
        $deductionDate = input_format_date($deductionDate, $date_format_policy);
        $loanDate = input_format_date($loanDate, $date_format_policy);
        if ($deductionDate < $loanDate) {
            $this->form_validation->set_message('loanDateValidate', 'Loan Deduction Start Date should be greater than Loan Date');
            return false;
        }
    }

    function load_advance_salary_drop(){
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $empID = $this->input->post('empID');

        //un pulled salary advance request
        $requests = $this->db->query("SELECT masterID, documentCode, request_amount, trDPlace
                            FROM srp_erp_pay_salaryadvancerequest rsq_tb
                            WHERE empID = {$empID} AND approvedYN = 1 AND NOT EXISTS (
                               SELECT salaryAdvanceRequestID FROM srp_erp_pay_emploan loanTB
                               WHERE loanTB.salaryAdvanceRequestID = rsq_tb.masterID
                            )")->result_array();

        $str = '<option value="" data-amount="0">Select request</option>';
        foreach ($requests as $row){
            $amount = number_format($row['request_amount'], $row['trDPlace']);
            $str .= '<option value="'.$row['masterID'].'" data-amount="'.$row['request_amount'].'">'.$row['documentCode'].' | '.$amount.'</option>';
        }

        echo json_encode(array('s', 'drop_list' => $str ));
    }

    public function createLoan()
    {
        $date_format_policy = date_format_policy();
        $loanID = $this->input->post('hiddenLoanID');
        $loanCode = $this->input->post('hiddenLoanCode');
        $empID = $this->input->post('empID');
        $lnDt = $this->input->post('loanDate');
        $loanDate = input_format_date($lnDt, $date_format_policy);
        $loanType = $this->input->post('loanType');
        $intPer = $this->input->post('intPer');
        $salary_advanceID = $this->input->post('salary_advanceID');
        $loanAmount = $this->input->post('loanAmount');
        $noOfInstallment = $this->input->post('noOfInstallment');
        $dedutnDte = $this->input->post('deductionDate');
        $deductionDate = input_format_date($dedutnDte, $date_format_policy);
        $loanDescription = $this->input->post('loanDescription');

        if ($loanID) {
            $loan_data = $this->db->query("SELECT salaryAdvanceRequestID, loanCatID FROM srp_erp_pay_emploan WHERE ID = {$loanID}")->row_array();
            $salary_advanceID = $loan_data['salaryAdvanceRequestID'];
            $loanType = $loan_data['loanCatID'];
            $_POST['salary_advanceID'] = $salary_advanceID;
            $_POST['loanType'] = $loanType;
        }

        //echo '<pre>'; print_r($_POST); echo '</pre>';        die();

        $this->form_validation->set_rules('empID', 'Employee Name', 'trim|required|numeric');
        $this->form_validation->set_rules('loanType', 'Loan Type', 'trim|required|numeric');
        $this->form_validation->set_rules('payCurrencyCode', 'Employee Pay Currency', 'trim|required');
        $this->form_validation->set_rules('payCurrencyDPlace', 'Employee pay currency decimal places', 'trim|required|numeric');
        $this->form_validation->set_rules('loanDate', 'Loan Date', 'trim|required|date');
        if(empty($salary_advanceID)){
            $this->form_validation->set_rules('loanAmount', 'Loan Amount', 'trim|required|numeric');
        }
        $this->form_validation->set_rules('noOfInstallment', 'No of Installment', 'trim|required|numeric');
        $this->form_validation->set_rules('deductionDate', 'Loan Deduction Start Date', 'trim|required|date');
        //$this->form_validation->set_rules('deductionDate', 'Loan Deduction Start Date', 'trim|required|date|callback_loanDateValidate');
        //$this->form_validation->set_rules('loanDescription', 'Loan Description', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            /*Employee status*/
            $isDischarged = $this->db->get_where('srp_employeesdetails', ['EIdNo'=>$empID])->row('isDischarged');
            if($isDischarged == 1){
               // die( json_encode(['e', 'This employee already discharged']) );
            }

            if ($deductionDate < $loanDate) {
                die( json_encode(['e', 'Loan Deduction Start Date should be greater than Loan Date']) );
            }
            
            $this->load->helper('template_paySheet_helper');
            $lastPayrollProcessed = lastPayrollProcessedForEmp($empID);

            if($lastPayrollProcessed >= $deductionDate){
                echo json_encode(['e', 'Loan deduction start date should be greater than <br/>[ '.date('Y-F', strtotime($lastPayrollProcessed)).' ]']);
                exit;
            }

            $dateTime = current_date();

            if(!empty($salary_advanceID)){
                //Check already salary advance document pulled
                $this->db->select('loanCode')->from('srp_erp_pay_emploan')->where('salaryAdvanceRequestID', $salary_advanceID);
                if($loanID){ // if edit
                    $this->db->where('ID <> '.$loanID);
                }
                $pulled_data = $this->db->get()->row('loanCode');

                if(!empty($pulled_data)){
                    die( json_encode(['e', 'This Salary advance request document already pulled in [ '.$pulled_data.' ]']) );
                }

                //Get salary advance request data
                $advance_data = $this->db->query("SELECT masterID, documentCode, request_amount, trDPlace
                                  FROM srp_erp_pay_salaryadvancerequest
                                  WHERE empID = {$empID} AND approvedYN = 1 AND masterID={$salary_advanceID} ")->row_array();

                if(empty($advance_data)){
                    die( json_encode(['e', 'Salary Advance request document not found']) );
                }
                $loanAmount = $advance_data['request_amount'];
            }
            else{
                $salary_advanceID = 0;
            }

            $data = array(
                'empID' => $empID,
                'salaryAdvanceRequestID' => $salary_advanceID,
                'loanCatID' => $loanType,
                'loanDate' => $loanDate,
                'loanDescription' => $loanDescription,
                'amount' => $loanAmount,
                'interestPer' => $intPer,
                'numberOfInstallment' => $noOfInstallment,
                'currentApprovalLevel' => 1,
                'deductionStartingDate' => $deductionDate,
                'isClosed' => 0,
                'confirmedYN' => 0,
                'approvedYN' => 0,
                'segmentID' => 1,
                'segmentCode' => 1,
            );

            if (!$loanID) {
                $data['companyID'] = current_companyID();
                $data['companyCode'] = current_companyCode();
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdUserName'] = current_user();
                $data['createdUserGroup'] = current_user_group();
                $data['createdDateTime'] = $dateTime;
                $data['timestamp'] = $dateTime;
                echo json_encode($this->Loan_model->createLoan($data));
            }
            else {

                $document_status = document_status('LO', $loanID);
                if($document_status['error'] == 1){
                    die( json_encode(['e', $document_status['message']]) );
                }


                $data['modifiedPCID'] = current_pc();
                $data['modifiedUserID'] = current_userID();
                $data['modifiedUserName'] = current_user();
                $data['modifiedDateTime'] = $dateTime;
                $data['timestamp'] = $dateTime;
                echo json_encode($this->Loan_model->updateLoan($data, $loanID, $loanCode));
            }

        }
    }

    public function fetch_employee_loan()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        //$con = "IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')";
        $con = " IFNULL(Ename2, '')";
//userID, ECode,
        $this->datatables->select('CONCAT(' . $con . ') AS "Employee", loanCode, amount,loanDate, description, ID, confirmedYN, approvedYN', false)
            ->from('srp_erp_pay_emploan AS loan')
            ->join('srp_employeesdetails AS emp', 'emp.EIdNo = loan.empID')
            ->join('srp_erp_pay_loan_category AS loan_cat', 'loan_cat.loanID = loan.loanCatID')
            ->edit_column('amount', '$1', 'convertLoanAmount(amount)')
            ->add_column('confirm', '$1', 'confirm_approval(confirmedYN)')
            //->add_column('approved', '$1', 'loanConformStatus(approvedYN)')
            ->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"LO",ID)')
            ->add_column('action', '$1', 'load_Employee_load_action(ID, confirmedYN, approvedYN, loanCode)')
            ->edit_column('loanDate', '<span >$1 </span>', 'convert_date_format(loanDate)')
            ->where('loan.companyID', $companyID);
        echo $this->datatables->generate();

    }

    public function load_emp_loanDet()
    {
        $loanID = $this->input->post('loanID');
        echo json_encode($this->Loan_model->load_emp_loanDet($loanID));
    }

    public function load_empLoanSchedule()
    {
        $convertFormat = convert_date_format_sql();
        $loanID = $this->input->get('loanID');

        $this->datatables->select('DATE_FORMAT(scheduleDate,\'' . $convertFormat . '\') AS scheduleDate1, scheduleDate as sh, amountPerInstallment as amount, sch.ID AS scheduleID, sch.transactionCurrencyDecimalPlaces AS dPlace, isSetteled, skipedInstallmentID, installmentNo, skippedDescription', false)
            ->from('srp_erp_pay_emploan_schedule AS sch')
            ->join('srp_erp_pay_emploan AS loan', 'loan.ID = sch.loanID')
            ->edit_column('amount', '$1', 'convertLoanAmount(amount, dPlace)')
            ->add_column('status', '$1', 'loanStatus(isSetteled, skipedInstallmentID, skippedDescription)')
            ->add_column('action', '$1', 'loanSkip(scheduleID, isSetteled, skipedInstallmentID, sh, installmentNo,amount)')
            ->where('loan.companyID', $this->common_data['company_data']['company_id'])
            ->where('loan.ID', $loanID);
        echo $this->datatables->generate();

    }

    public function load_LoanSchedule()
    {
        $convertFormat = convert_date_format_sql();
        $loanID = $this->input->post('loanID');

        $query = $this->db->query("
        SELECT 
            DATE_FORMAT(scheduleDate, '{$convertFormat}') AS scheduleDate1,scheduleDate AS sh,amountPerInstallment AS amount,sch.ID AS scheduleID,sch.transactionCurrencyDecimalPlaces AS dPlace, isSetteled,skipedInstallmentID,installmentNo,skippedDescription FROM srp_erp_pay_emploan_schedule AS sch
        INNER JOIN 
            srp_erp_pay_emploan AS loan ON loan.ID = sch.loanID
        WHERE 
            loan.companyID = {$this->common_data['company_data']['company_id']}
            AND loan.ID = {$loanID}");

     $result_array = $query->result_array();
     echo json_encode($result_array);
    }

    public function emp_loan_delete()
    {
        $delID = $this->input->post('delID');

        echo json_encode($this->Loan_model->emp_loan_delete($delID));
    }

    public function getLastScheduleDate()
    {
        echo json_encode($this->Loan_model->getLastScheduleDate());
    }

    public function skipLoanSchedule()
    {
        $loanID = $this->input->post('hiddenLoanID_skip');
        $this->form_validation->set_rules('hiddenLoanID_skip', 'Loan ID', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $this->db->select('isClosed, loanCode')->from('srp_erp_pay_emploan')->where('ID', $loanID);
            $isClosed = $this->db->get()->row_array();

            if ($isClosed['isClosed'] == 1) {
                echo json_encode(array('e', '[ ' . $isClosed['loanCode'] . ' ] is already closed'));
            } else {
                echo json_encode($this->Loan_model->skipLoanSchedule());
            }
        }
    }

    public function loan_conformation()
    {

        $loanID = trim($this->input->post('loanID') ?? '');
        $isConformed = $this->Loan_model->isConformed($loanID);

        $this->load->helper('template_paySheet_helper');
        $isPayrollProcessed = isPayrollProcessed($isConformed['deductionStartingDate']);


        if ($isPayrollProcessed['status'] == 'N') {

            if ($isConformed['confirmedYN'] == 1) {
                echo json_encode(array('e', 'This Loan is already Conformed'));
            } else {
                echo json_encode($this->Loan_model->loan_conformation($loanID));
            }
        } else {
            $greaterThanDate = date('Y - F', strtotime($isPayrollProcessed['year'] . '-' . $isPayrollProcessed['month'] . '-01'));
            echo json_encode(array('e', 'Loan deduction start date should be greater than <p> [ ' . $greaterThanDate . ' ] '));
        }
    }

    function fetch_loan_conformation()
    {
        //$userCode   = $this->common_data["current_userCode"];
        $convertFormat = convert_date_format_sql();
        $userID = current_userID();
        //$con        = "IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')";
        $con = "IFNULL(Ename2, '')";
        $status = trim($this->input->post('approvedYN') ?? '');
        $companyid  = current_companyID();

      if($status == 0)
      {
          $where = array(
              'approve.documentID' => 'LO',
              'ap.documentID' => 'LO',
              'ap.employeeID' => $userID,
              'approve.approvedYN' => $status,
          );

          $this->datatables->select('e_loan.ID AS loanID, CONCAT(' . $con . ') AS empName, loanCode, currentApprovalLevel, loanDescription, confirmedYN, approve.approvedYN AS appYN, documentApprovedID, approvalLevelID,DATE_FORMAT(loanDate,\'' . $convertFormat . '\') AS loanDate')
              ->from('srp_erp_pay_emploan AS e_loan')
              ->join('srp_erp_documentapproved AS approve', 'approve.documentSystemCode = e_loan.ID AND approve.approvalLevelID = e_loan.currentLevelNo')
              ->join('srp_employeesdetails AS emp', 'emp.EIdNo = e_loan.empID')
              ->join('srp_erp_approvalusers AS ap', 'ap.levelNo = e_loan.currentLevelNo')
              ->where($where)
              ->where('e_loan.companyID', current_companyID())
              ->where('ap.companyID', current_companyID())
              ->add_column('level', "<center>Level $1</center>", 'approvalLevelID')
              ->add_column('approved', '$1', 'document_approval_drilldown(appYN, LO, loanID)')
              ->add_column('edit', '$1', 'loan_action_approval(loanID, documentApprovedID, approvalLevelID, loanCode, appYN)')
              ->add_column('loanCode', '<a onclick=\'load_emp_loanDet("$2","$3","$4"); \'>$1</a>', 'loanCode, loanID,documentApprovedID,approvalLevelID');
          echo $this->datatables->generate();
      }else
      {
          $where = array(
              'approve.documentID' => 'LO',
              'e_loan.companyID' => $companyid,
              'approve.approvedEmpID' => $userID,
          );

          $this->datatables->select('e_loan.ID AS loanID, CONCAT(' . $con . ') AS empName, loanCode, currentApprovalLevel, loanDescription, confirmedYN, approve.approvedYN AS appYN, documentApprovedID, approvalLevelID,DATE_FORMAT(loanDate,\'' . $convertFormat . '\') AS loanDate')
              ->from('srp_erp_pay_emploan AS e_loan')
              ->join('srp_erp_documentapproved AS approve', 'approve.documentSystemCode = e_loan.ID ')
              ->join('srp_employeesdetails AS emp', 'emp.EIdNo = e_loan.empID')
              ->where($where)
              ->group_by('e_loan.ID')
              ->group_by('approve.approvalLevelID')
              ->add_column('level', "<center>Level $1</center>", 'approvalLevelID')
              ->add_column('approved', '$1', 'document_approval_drilldown(appYN, LO, loanID)')
              ->add_column('edit', '$1', 'loan_action_approval(loanID, documentApprovedID, approvalLevelID, loanCode, appYN)')
              ->add_column('loanCode', '<a onclick=\'load_emp_loanDet("$2","$3","$4"); \'>$1</a>', 'loanCode, loanID,documentApprovedID,approvalLevelID');
          echo $this->datatables->generate();
      }


        /*$this->datatables->select('e_loan.ID, CONCAT('.$con.') AS empName, loanCode, currentApprovalLevel, loanDescription, confirmedYN, approve.approvedYN, documentApprovedID, approvalLevelID')
            ->from('srp_erp_pay_emploan1 AS e_loan')
            ->join('srp_erp_documentapproved AS approve', 'approve.documentSystemCode = e_loan.ID')
            ->join('srp_employeesdetails AS emp', 'emp.EIdNo = e_loan.empID' )
            ->where('approve.documentID', 'LO')
            ->where('approve.approvedEmpID',  $this->common_data["current_userCode"])
            ->where('approve.approvedYN', $status)
            ->where('e_loan.currentApprovalLevel', $userLevel )
            ->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID')
            ->add_column('approved', '$1', 'loan_conform(approvedYN)')
            ->add_column('edit', '$1', 'loan_action_approval(e_loan.ID , approvalLevelID, approve.approvedYN, documentApprovedID)');
        echo $this->datatables->generate('json', 'ISO-8859-1');*/
    }


    public function loanApproval()
    {
        $loanID = $this->input->post('hiddenLoanID');
        $level_id = $this->input->post('level');
        $status = $this->input->post('status');
        $comments = $this->input->post('comments');

        $this->form_validation->set_rules('hiddenLoanID', 'Loan ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_rules('level', 'Level', 'trim|required');
        if($this->input->post('status') ==2){
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $loanDet = $this->Loan_model->load_emp_loanDet($loanID);

            if ($status == 1) {
                $this->load->helper('template_paySheet_helper');
                $deductionStartingDate = $loanDet->deductionStartingDate;
                $date_format_policy = date_format_policy();
                $deductionStartingDate = input_format_date($deductionStartingDate, $date_format_policy);

                $isPayrollProcessed = isPayrollProcessed($deductionStartingDate);

                if ($isPayrollProcessed['status'] == 'Y') {

                    $greaterThanDate = date('Y - F', strtotime($isPayrollProcessed['year'] . '-' . $isPayrollProcessed['month'] . '-01'));
                    echo json_encode(array('e', 'You cannot approve this document.</br>
                                                 Loan deduction start date should be greater than last payroll processed date [ ' . $greaterThanDate . ' ]</br>
                                                 Please Reschedule the deduction dates.'));
                    exit;
                }
            }


            $this->load->library('Approvals');
            $approvals_status = $this->approvals->approve_document($loanID, $level_id, $status, $comments, 'LO');
            if ($approvals_status == 1 || $approvals_status == 2) {
                echo json_encode(array('s', 'Loan [ ' . $loanDet->loanCode . ' ] Approved'));
            } else if ($approvals_status == 3) {
                echo json_encode(array('s', '[ ' . $loanDet->loanCode . ' ] Approvals  Reject Process Successfully done.'));
            } else if ($approvals_status == 5) {
                echo json_encode(array('w', '[ ' . $loanDet->loanCode . ' ] Previous Level Approval Not Finished.'));
            } else {
                echo json_encode(array('e', 'Error in Loan Approvals Of  [ ' . $loanDet->loanCode . ' ] ', $approvals_status));
            }

        }
    }

    public function isPreviousLevelsApproved()
    {
        echo json_encode($this->Loan_model->isPreviousLevelsApproved());
    }

    public function load_empLoanView()
    {
        $empID = $this->input->post('empID');
        $data['empID'] = $empID;
        $data['loanData'] = $this->Loan_model->empLoanView_data($empID);

        /*echo '<pre>'; print_r($data); echo'</pre>';die();*/

        $this->load->view('system/hrm/load_empLoanView', $data);
    }

    public function empLoan_installmentDetails()
    {
        echo json_encode($this->Loan_model->empLoan_installmentDetails());
    }

    public function loan_print()
    {
        $id = $this->uri->segment(3);

        $printData = $this->Loan_model->empLoanSchedule_print($id);
        $data['masterData'] = $this->Loan_model->load_emp_loanDet($id);
        $data['intAmount'] = $printData['intAmount'];
        $data['schedule'] = $printData['schedule'];
        $data['installment'] = $printData['installment'];

        /*echo '<pre>'; print_r($data['installment'] ); echo '</pre>';die();*/

        if (empty($data['masterData'])) {
            show_404();
            die();
        }

        $isApproved = $data['masterData']->approvedYN;

        $html = $this->load->view('system/hrm/print/loan_print', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', $isApproved);


    }

    public function loan_request_print()
    {
        $id = $this->uri->segment(3);

        $printData = $this->Loan_model->empLoanSchedule_print($id);
        $data['masterData'] = $this->Loan_model->load_emp_loanDet($id);
        $data['masterDataReq'] = $this->Loan_model->load_emp_loanDetReq($id);
        $data['intAmount'] = $printData['intAmount'];
        $data['schedule'] = $printData['schedule'];
        $data['installment'] = $printData['installment'];

        //$data['extra'] = $this->Payable_modal->fetch_debit_note_template_data($debitNoteMasterAutoID);

        //var_dump($data['masterDataReq']);

        /*echo '<pre>'; print_r($data['installment'] ); echo '</pre>';die();*/

        if (empty($data['masterData'])) {
            show_404();
            die();
        }

        $isApproved = $data['masterData']->approvedYN;
        ob_clean(); // cleaning the buffer before Output()
        $html = $this->load->view('system/hrm/print/loan_request_print', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', $isApproved);


    }


    public function loan_referBack()
    {
        $loanID = $this->input->post('id');
        $loanDet = $this->Loan_model->load_emp_loanDet($loanID);

        if ($loanDet->approvedYN == 1) {
            echo json_encode(array('e', $loanDet->loanCode . ' is already Approved'));
        } else {
            $this->load->library('Approvals');
            $status = $this->approvals->approve_delete($loanID, 'LO');
            if ($status == 1) {
                echo json_encode(array('s', $loanDet->loanCode . ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', $loanDet->loanCode . ' Error in refer back.', $status));
            }

        }
    }

    /* Function added */
    function get_loan_report()
    { 
        $requestType = $this->uri->segment(3);
        $companyID = current_companyID();
        $employee = $this->input->post('empID');
        $loanType = $this->input->post('loanType');
        $filter = '';
 
        if (!empty(array_filter($employee))) {
            $commaList = implode(', ', $employee);
            $filter .= "AND srp_erp_pay_emploan.empID IN($commaList) ";
        }

        if (!empty(array_filter($loanType))) {
            $commaList = implode(', ', $loanType);
            $filter .= "AND srp_erp_pay_emploan.loanCatID IN($commaList) ";
        }

        $data['details'] = $this->db->query(" 
            SELECT
                srp_erp_pay_emploan.ID AS emploanID,
                ECode,
                Ename2,
                loanCode,
                srp_erp_pay_loan_category.description AS loanType,
                IFNULL(amount, 0) AS loanAmount,
                interestPer,
                loanDate,
                numberOfInstallment,
                deductionStartingDate,
                deductionStartingDate + INTERVAL numberOfInstallment MONTH AS expectedEndDate,
                ifnull(
                    ifnull(amount, 0) / ifnull(numberOfInstallment, 0),
                    0
                ) AS installmentAmount,
                ifnull(payroll.settltedAmount, 0) AS settltedAmount,
                (
                    IFNULL(payroll.settltedAmount, 0) / IFNULL(amount, 0)
                ) * 100 AS settledAmountPercentage,
                (
                    IFNULL(amount, 0) - IFNULL(payroll.settltedAmount, 0)
                ) AS balance,
                srp_erp_pay_emploan.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                documentID
            FROM
                srp_erp_pay_emploan
            LEFT JOIN srp_employeesdetails ON EIdNo = srp_erp_pay_emploan.empID
            LEFT JOIN srp_erp_pay_loan_category ON srp_erp_pay_emploan.loanCatID = srp_erp_pay_loan_category.loanID
            LEFT JOIN (
                SELECT
                    SUM(
                        srp_erp_payrolldetail.transactionAmount
                    ) *- 1 AS settltedAmount,
                    detailTBID,
                    srp_erp_pay_emploan_schedule.loanID AS loanID
                FROM
                    srp_erp_payrolldetail
                JOIN srp_erp_payrollmaster ON srp_erp_payrolldetail.payrollMasterID = srp_erp_payrollmaster.payrollMasterID
                JOIN srp_erp_pay_emploan_schedule ON srp_erp_payrolldetail.detailTBID = srp_erp_pay_emploan_schedule.ID
              JOIN srp_erp_pay_emploan on srp_erp_pay_emploan.ID=srp_erp_pay_emploan_schedule.loanID
                WHERE
                    srp_erp_payrollmaster.companyID = {$companyID}
                AND srp_erp_payrollmaster.approvedYN = 1
                AND fromTB = 'LO'
                GROUP BY
                    srp_erp_pay_emploan.ID
            ) payroll ON payroll.loanID = srp_erp_pay_emploan.ID
            WHERE srp_erp_pay_emploan.companyID = {$companyID} AND approvedYN = 1 
         $filter
             ")->result_array();

        if ($requestType == 'pdf') {
            $html = $this->load->view('system/hrm/report/ajax/load-employee-loan-report_pdf.php', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        } else {
            echo  $html = $this->load->view('system/hrm/report/ajax/load-employee-loan-report.php', $data, true);
        }
    }


    function get_drilldown_details(){
        
        $companyID = current_companyID();
        $emploanID = $this->input->post('emploanID');
        
        $data['numberOfSettledInstallment'] = $this->db->query(" SELECT COUNT(isSetteled) AS numberOfSettledInstallment FROM srp_erp_pay_emploan_schedule WHERE companyID={$companyID } AND loanID = {$emploanID} AND isSetteled != 0 ")->row('numberOfSettledInstallment');
        
        $data['numberOfSkippedInstallment'] = $this->db->query(" SELECT COUNT(skipedInstallmentID) AS numberOfSkippedInstallment FROM srp_erp_pay_emploan_schedule WHERE companyID={$companyID } AND loanID = {$emploanID} AND skipedInstallmentID != 0  ")->row('numberOfSkippedInstallment');
      
        $data['header']= $this->db->query(" 
            SELECT
                srp_erp_pay_emploan.ID AS emploanID,
                ECode,
                Ename2,
                IFNULL( amount, 0 ) AS loanAmount,
                numberOfInstallment,
                ifnull( ifnull( amount, 0 ) / ifnull( numberOfInstallment, 0 ), 0 ) AS installmentAmount,
                ifnull( settledAmount, 0 ) AS settledAmount,
                ifnull(( ifnull( amount, 0 ) - ifnull( settledAmount, 0 )), 0 ) AS pendingAmount,
                transactionCurrency,
                srp_erp_pay_emploan.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces 
            FROM
                srp_erp_pay_emploan
                LEFT JOIN srp_employeesdetails ON EIdNo = srp_erp_pay_emploan.empID
                LEFT JOIN (
                SELECT
                    SUM( srp_erp_payrolldetail.transactionAmount )*- 1 AS settledAmount,
                    detailTBID,
                    srp_erp_pay_emploan_schedule.loanID AS loanID 
                FROM
                    srp_erp_payrolldetail
                    JOIN srp_erp_payrollmaster ON srp_erp_payrolldetail.payrollMasterID = srp_erp_payrollmaster.payrollMasterID
                    JOIN srp_erp_pay_emploan_schedule ON srp_erp_payrolldetail.detailTBID = srp_erp_pay_emploan_schedule.ID 
                    JOIN srp_erp_pay_emploan on srp_erp_pay_emploan.ID=srp_erp_pay_emploan_schedule.loanID
                WHERE
                    srp_erp_payrollmaster.companyID = {$companyID} 
                    AND srp_erp_payrollmaster.approvedYN = 1 
                    AND fromTB = 'LO' 
                GROUP BY
                    srp_erp_pay_emploan.ID
                ) payroll ON payroll.loanID = srp_erp_pay_emploan.ID 
            WHERE
                srp_erp_pay_emploan.companyID = {$companyID} 
                AND srp_erp_pay_emploan.ID = {$emploanID}
                        ")->row_array();

            $data['details']= $this->db->query(" 
                SELECT srp_erp_pay_emploan_schedule.*,payroll.narration AS remark
                FROM srp_erp_pay_emploan_schedule 
                LEFT JOIN (
                SELECT
                    detailTBID,
                    srp_erp_payrollmaster.narration
                    FROM
                    srp_erp_payrolldetail
                    join srp_erp_payrollmaster on srp_erp_payrolldetail.payrollMasterID=srp_erp_payrollmaster.payrollMasterID
                    WHERE
                        srp_erp_payrollmaster.companyID = {$companyID }  and srp_erp_payrollmaster.approvedYN=1
                    AND fromTB = 'LO'
                    GROUP BY
                        detailTBID
                    ) payroll ON payroll.detailTBID = srp_erp_pay_emploan_schedule.ID   
                    WHERE srp_erp_pay_emploan_schedule.companyID = {$companyID } AND srp_erp_pay_emploan_schedule.loanID = {$emploanID}
             ")->result_array();

        $html = $this->load->view('system/hrm/report/ajax/load-employee-loan_drilldown-html.php', $data, true);
        echo $html ;
    }
    /* End  Function */
}