<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 5/19/2016
 * Time: 4:20 PM
 */
class Loan_model extends ERP_Model{

    function saveLoanCategory($data){
        $this->db->insert('srp_erp_pay_loan_category', $data);

        if( $this->db->affected_rows() > 0 ){
            return array('s', 'Loan Category Saved');
        }else{
            return array('e', 'Error in Loan Category Saving Process');
        }
    }

    function updateLoanCategory($data, $loanID){
        $this->db->where('loanID', $loanID)
                 ->update('srp_erp_pay_loan_category', $data);

        if( $this->db->affected_rows() > 0 ){
            return array('s', 'Loan Category Updated');
        }else{
            return array('e', 'Error in Loan Category Updating Process');
        }
    }

    function isAlreadyExistLoanCategory($descpt){
        $where = "description='".$descpt."' AND companyID=".$this->common_data['company_data']['company_id'];

        $query = $this->db->select('loanID')
                            ->from('srp_erp_pay_loan_category')
                            ->where($where)
                            ->get();

        return $query->result_array();

    }

    function fetchLoanCategory(){
        $query = $this->db->select('loanID, description, isInterestBased, interestPercentage, isSalaryAdvance', false)
                         ->from('srp_erp_pay_loan_category')
                         ->where('companyID', $this->common_data['company_data']['company_id'])
                         ->get();

        return $query->result();
    }

    function delete_loanCat(){
        $catID = $this->input->post('catID');
        $this->form_validation->set_rules('catID', 'Cat ID', 'trim|required');

        if($this->form_validation->run()==FALSE){
            return array('e', validation_errors()) ;
        }
        else {
            $query = $this->db->select('cat.loanID, cat.description AS des')
                              ->from('srp_erp_pay_loan_category AS cat')
                              ->join('srp_erp_pay_emploan AS loan', 'loan.loanCatID = cat.loanID')
                              ->where('cat.loanID', $catID)->get();
            $isInUse = $query->row_array();

            if( empty($isInUse) ){
                $this->db->where('loanID', $catID)->delete('srp_erp_pay_loan_category');

                if ($this->db->affected_rows() > 0) {
                    return array('s', 'Deleted successfully');
                } else {
                    return array('e', 'Error in delete process.');
                }
            }
            else{
                return array('e', '[ '.$isInUse['des'].' ] is in use, You can not delete this');
            }
        }
    }

    function loanApprovalLevel(){
        $query =  $this->db->select('levelNo, documentID, employeeID')
                            ->from('srp_erp_approvalusers')
                            ->where('companyID', $this->common_data['company_data']['company_id'])
                            ->where('documentID', 'LO')
                            ->get();
        return $query->result();
    }

    function createLoan($data){

        //Get last Loan no
        $query = $this->db->select('loanSerialNo')
                           ->from('srp_erp_pay_emploan')
                           ->where('companyID', $this->common_data['company_data']['company_id'])
                           ->order_by('ID', 'desc')
                           ->get();
        $lastLoanArray = $query->row_array();
        $lastLoanNo    = $lastLoanArray['loanSerialNo'] ;
        $lastLoanNo    = ( $lastLoanNo == null  )? 1 : $lastLoanArray['loanSerialNo'] + 1;

        //Generate loan Code
        $this->load->library('sequence');
        $loanCode             = $this->sequence->sequence_generator('LO', $lastLoanNo);
        $data['loanCode']     =  $loanCode;
        $data['loanSerialNo'] =  $lastLoanNo;

        //transaction , company, reporting currency conversions
        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $com_currencyDPlace = $this->common_data['company_data']['company_default_decimal'];
        $com_repCurrency = $this->common_data['company_data']['company_reporting_currency'];
        $com_repCurDPlace = $this->common_data['company_data']['company_reporting_decimal'];

        $empCurrencyCode    = $this->input->post('payCurrencyCode');
        $empCurrencyDPlace  = $this->input->post('payCurrencyDPlace');


        $localCon = currency_conversion($empCurrencyCode, $com_currency, $data['amount']);
        $reportCon = currency_conversion($empCurrencyCode, $com_repCurrency, $data['amount']);
        $localAmount = ( $localCon['conversion'] > 0)? round(($data['amount'] / $localCon['conversion']), $com_currencyDPlace) : round($data['amount'], $com_currencyDPlace);
        $reportAmount = ( $reportCon['conversion'] > 0)? round(($data['amount'] / $reportCon['conversion']), $com_repCurDPlace) : round($data['amount'], $com_repCurDPlace);


        $data['transactionCurrencyID'] = $localCon['trCurrencyID'];
        $data['transactionCurrency'] = $empCurrencyCode;
        $data['transactionER'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = $empCurrencyDPlace;
        $data['transactionAmount'] = $data['amount'];

        $data['companyLocalCurrencyID'] = $localCon['currencyID'];
        $data['companyLocalCurrency'] = $com_currency;
        $data['companyLocalER'] = $localCon['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $com_currencyDPlace;
        $data['companyLocalAmount'] = $localAmount;

        $data['companyReportingCurrencyID'] = $reportCon['currencyID'];
        $data['companyReportingCurrency'] = $com_repCurrency;
        $data['companyReportingAmount'] = $reportAmount;
        $data['companyReportingER'] = $reportCon['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $com_repCurDPlace;



        $this->db->trans_start();
        $this->db->insert('srp_erp_pay_emploan', $data);
        $loanID = $this->db->insert_id();



        //create Loan schedule
        $n = 0;
        $noOfInst      = $data['numberOfInstallment'];
        $intPer        = $data['interestPer'];
        $amountPerInst = $data['amount'] / $noOfInst;
        $amountPerInst += ($intPer > 0)? $amountPerInst * ($intPer * 0.01): 0;
        $deductionDate = $data['deductionStartingDate'];
        $deductionMonthFirstDate = date('Y-m-01', strtotime($deductionDate));
        $data_Schedule = array();

        $localIntAmount = ( $localCon['conversion'] > 0)? round(($amountPerInst / $localCon['conversion']), 3) : round($amountPerInst, 3);
        $reportIntAmount = ( $reportCon['conversion'] > 0)? round(($amountPerInst / $reportCon['conversion']), 3) : round($amountPerInst, 3);

        while( $n < $noOfInst ){
            $data_Schedule[$n]['empID']                = $data['empID'];
            $data_Schedule[$n]['loanID']               = $loanID;
            $data_Schedule[$n]['loanCode']             = $loanCode;
            $data_Schedule[$n]['amountPerInstallment'] = $amountPerInst;
            if($n == 0){
                $data_Schedule[$n]['scheduleDate']         = $deductionDate;
            }else{
                $data_Schedule[$n]['scheduleDate']   = date('Y-m-t', strtotime("+".$n." months", strtotime($deductionMonthFirstDate)));
            }
            
            $data_Schedule[$n]['installmentNo']        = $n + 1;
            $data_Schedule[$n]['skipedInstallmentID']  = 0;

            $data_Schedule[$n]['transactionCurrencyID'] = $localCon['trCurrencyID'];
            $data_Schedule[$n]['transactionCurrency'] = $empCurrencyCode;
            $data_Schedule[$n]['transactionER'] = 1;
            $data_Schedule[$n]['transactionCurrencyDecimalPlaces'] = $empCurrencyDPlace;
            $data_Schedule[$n]['transactionAmount'] = $amountPerInst;

            $data_Schedule[$n]['companyLocalCurrencyID'] = $localCon['currencyID'];
            $data_Schedule[$n]['companyLocalCurrency'] = $com_currency;
            $data_Schedule[$n]['companyLocalER'] = $localCon['conversion'];
            $data_Schedule[$n]['companyLocalCurrencyDecimalPlaces'] = $com_currencyDPlace;
            $data_Schedule[$n]['companyLocalAmount'] = $localIntAmount;

            $data_Schedule[$n]['companyReportingCurrencyID'] = $reportCon['currencyID'];
            $data_Schedule[$n]['companyReportingCurrency'] = $com_repCurrency;
            $data_Schedule[$n]['companyReportingAmount'] = $reportIntAmount;
            $data_Schedule[$n]['companyReportingER'] = $reportCon['conversion'];
            $data_Schedule[$n]['companyReportingCurrencyDecimalPlaces'] = $com_repCurDPlace;


            $data_Schedule[$n]['companyID']            = $this->common_data['company_data']['company_id'];
            $data_Schedule[$n]['companyCode']          = $this->common_data['company_data']['company_code'];
            $data_Schedule[$n]['segmentID']            = 1;
            $data_Schedule[$n]['segmentCode']          = 1;
            $data_Schedule[$n]['createdPCID']          = $this->common_data['current_pc'];
            $data_Schedule[$n]['createdUserID']        = $this->common_data['current_userID'];
            $data_Schedule[$n]['createdUserGroup']     = $this->common_data['user_group'];
            $data_Schedule[$n]['createdUserName']      = $this->common_data['current_user'];
            $data_Schedule[$n]['createdDateTime']      = $this->common_data['current_date'];
            $n++;
        }

        $this->db->insert_batch('srp_erp_pay_emploan_schedule', $data_Schedule);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in Loan creating Process');
        } else {
            $this->db->trans_commit();
            return array('s','Loan created Successfully', $loanID, $loanCode);
        }


    }

    function load_emp_loanDet($loanID){

        $convertFormat=convert_date_format_sql();
        $company_id = current_companyID();
        $con = "IFNULL(Ename2, '')";
        $query = $this->db->select('EIdNo, CONCAT('.$con.') AS "Employee", ECode, loanCode, amount, payCurrency, payCurrencyID, salaryAdvanceRequestID, adv.documentCode AS ad_document_code,
                             adv.request_amount, DecimalPlaces,DATE_FORMAT(loanDate,\''.$convertFormat.'\') AS loanDate, ID, loanCatID, loanDescription, 
                             interestPer, numberOfInstallment, DATE_FORMAT(deductionStartingDate,\''.$convertFormat.'\') AS  deductionStartingDate, DesDescription, loan_cat.description, 
                             confirmedYN, confirmedByName, approvedYN,approvedDate, approvedbyEmpName')
                           ->from('srp_erp_pay_emploan AS loan')
                           ->join('srp_employeesdetails AS emp', 'emp.EIdNo = loan.empID' )
                           ->join('srp_erp_pay_loan_category AS loan_cat', 'loan_cat.loanID = loan.loanCatID' )
                           ->join('srp_designation AS empDes', 'empDes.DesignationID = emp.EmpDesignationId' )
                           ->join('srp_erp_currencymaster AS cu_master', 'cu_master.CurrencyCode = emp.payCurrency ', 'left')
                           ->join('(SELECT masterID, documentCode, FORMAT(request_amount, trDPlace) AS request_amount FROM srp_erp_pay_salaryadvancerequest 
                                    WHERE companyID = '.$company_id.' )
                                   AS adv', 'adv.masterID = loan.salaryAdvanceRequestID ', 'left')
                           ->where('loan.companyID', $company_id)
                           ->where('loan.ID', $loanID)
                           ->get();

        return $query->row();
    }

    function load_emp_loanDetReq($loanID){

        $convertFormat=convert_date_format_sql();
        $company_id = current_companyID();
        $query = $this->db->query("SELECT
                loan.loanDate,
                loan.loanCode AS docCode,
                empDetails.EmpSecondaryCode,
                empDetails.Ename2 AS employeename,
                empDetails.EDOJ AS empdateofjoin,
                empDetails.EPassportNO AS EPassportNO,
                loan.amount,
                loan.loanDescription,
                designationMaster.DesDescription,
                departmentmaster.DepartmentDes,
                loan.transactionCurrency,
                loan.numberOfInstallment,
                loan.amount / loan.numberOfInstallment AS monthlyinstallment,
                loan.deductionStartingDate,
                DATE_ADD( loan.deductionStartingDate, INTERVAL loan.numberOfInstallment MONTH ) AS loanEndDate 
            FROM
                srp_erp_pay_emploan loan
                LEFT JOIN srp_employeesdetails empDetails ON loan.empID = empDetails.EIdNo
                LEFT JOIN srp_employeedesignation empdesignation ON empDetails.EIdNo = empdesignation.EmpID 
                AND isMajor = 1
                LEFT JOIN srp_designation designationMaster ON empdesignation.designationID = designationMaster.DesignationID
                LEFT JOIN srp_empdepartments empdepartment ON empdepartment.EmpID = empDetails.EIdNo 
                AND empdepartment.isPrimary = 1
                LEFT JOIN srp_departmentmaster departmentmaster ON departmentmaster.departmentmasterID = empdepartment.empdepartmentID 
            WHERE
                loan.ID =$loanID");

        return $query->row();
    }

    function updateLoan($data, $loanID, $loanCode){

        //transaction , company, reporting currency conversions
        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $com_currencyDPlace = $this->common_data['company_data']['company_default_decimal'];
        $com_repCurrency = $this->common_data['company_data']['company_reporting_currency'];
        $com_repCurDPlace = $this->common_data['company_data']['company_reporting_decimal'];

        $empCurrencyCode    = $this->input->post('payCurrencyCode');
        $empCurrencyDPlace  = $this->input->post('payCurrencyDPlace');


        $localCon = currency_conversion($empCurrencyCode, $com_currency, $data['amount']);
        $reportCon = currency_conversion($empCurrencyCode, $com_repCurrency, $data['amount']);
        $localAmount = ( $localCon['conversion'] > 0)? round(($data['amount'] / $localCon['conversion']), $com_currencyDPlace) : round($data['amount'], $com_currencyDPlace);
        $reportAmount = ( $reportCon['conversion'] > 0)? round(($data['amount'] / $reportCon['conversion']), $com_repCurDPlace) : round($data['amount'], $com_repCurDPlace);


        $data['transactionCurrencyID'] = $localCon['trCurrencyID'];
        $data['transactionCurrency'] = $empCurrencyCode;
        $data['transactionER'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = $empCurrencyDPlace;
        $data['transactionAmount'] = $data['amount'];

        $data['companyLocalCurrencyID'] = $localCon['currencyID'];
        $data['companyLocalCurrency'] = $com_currency;
        $data['companyLocalER'] = $localCon['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $com_currencyDPlace;
        $data['companyLocalAmount'] = $localAmount;

        $data['companyReportingCurrencyID'] = $reportCon['currencyID'];
        $data['companyReportingCurrency'] = $com_repCurrency;
        $data['companyReportingAmount'] = $reportAmount;
        $data['companyReportingER'] = $reportCon['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $com_repCurDPlace;

        $this->db->trans_start();

        $this->db->where('ID', $loanID);
        $this->db->update('srp_erp_pay_emploan', $data);
        $this->db->delete('srp_erp_pay_emploan_schedule', array('loanID' => $loanID));

        //create Loan schedule
        $n = 0;
        $noOfInst      = $data['numberOfInstallment'];
        $intPer        = $data['interestPer'];
        $amountPerInst = $data['amount'] / $noOfInst;
        $amountPerInst += ($intPer > 0)? $amountPerInst * ($intPer * 0.01): 0;
        $deductionDate = $data['deductionStartingDate'];
        $deductionMonthFirstDate = date('Y-m-01', strtotime($deductionDate));
        $data_Schedule = array();


        $localIntAmount = ( $localCon['conversion'] > 0)? round(($amountPerInst / $localCon['conversion']), 3) : round($amountPerInst, 3);
        $reportIntAmount = ( $reportCon['conversion'] > 0)? round(($amountPerInst / $reportCon['conversion']), 3) : round($amountPerInst, 3);

        while( $n < $noOfInst ){
            $data_Schedule[$n]['empID']                = $data['empID'];
            $data_Schedule[$n]['loanID']               = $loanID;
            $data_Schedule[$n]['loanCode']             = $loanCode;
            $data_Schedule[$n]['amountPerInstallment'] = $amountPerInst;

            if($n == 0){
                $data_Schedule[$n]['scheduleDate']         = $deductionDate;
            }else{
                $data_Schedule[$n]['scheduleDate']   = date('Y-m-t', strtotime("+".$n." months", strtotime($deductionMonthFirstDate)));
            }

            $data_Schedule[$n]['installmentNo']        = $n + 1;
            $data_Schedule[$n]['skipedInstallmentID']  = 0;

            $data_Schedule[$n]['transactionCurrencyID'] = $localCon['trCurrencyID'];
            $data_Schedule[$n]['transactionCurrency'] = $empCurrencyCode;
            $data_Schedule[$n]['transactionER'] = 1;
            $data_Schedule[$n]['transactionCurrencyDecimalPlaces'] = $empCurrencyDPlace;
            $data_Schedule[$n]['transactionAmount'] = $amountPerInst;

            $data_Schedule[$n]['companyLocalCurrencyID'] = $localCon['currencyID'];
            $data_Schedule[$n]['companyLocalCurrency'] = $com_currency;
            $data_Schedule[$n]['companyLocalER'] = $localCon['conversion'];
            $data_Schedule[$n]['companyLocalCurrencyDecimalPlaces'] = $com_currencyDPlace;
            $data_Schedule[$n]['companyLocalAmount'] = $localIntAmount;

            $data_Schedule[$n]['companyReportingCurrencyID'] = $reportCon['currencyID'];
            $data_Schedule[$n]['companyReportingCurrency'] = $com_repCurrency;
            $data_Schedule[$n]['companyReportingAmount'] = $reportIntAmount;
            $data_Schedule[$n]['companyReportingER'] = $reportCon['conversion'];
            $data_Schedule[$n]['companyReportingCurrencyDecimalPlaces'] = $com_repCurDPlace;

            $data_Schedule[$n]['companyID']            = $this->common_data['company_data']['company_id'];
            $data_Schedule[$n]['companyCode']          = $this->common_data['company_data']['company_code'];
            $data_Schedule[$n]['segmentID']            = 1;
            $data_Schedule[$n]['segmentCode']          = 1;
            $data_Schedule[$n]['createdPCID']          = $this->common_data['current_pc'];
            $data_Schedule[$n]['createdUserID']        = $this->common_data['current_userID'];
            $data_Schedule[$n]['createdUserGroup']     = $this->common_data['user_group'];
            $data_Schedule[$n]['createdUserName']      = $this->common_data['current_user'];
            $data_Schedule[$n]['createdDateTime']      = $this->common_data['current_date'];
            $n++;
        }

        $this->db->insert_batch('srp_erp_pay_emploan_schedule', $data_Schedule);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in Loan creating Process');
        } else {
            $this->db->trans_commit();
            return array('s','Loan Updated Successfully', $loanID, $loanCode, );
        }


    }

    function emp_loan_delete($delID){
        $this->db->trans_start();

        //delete loan Schedule
        $this->db->delete('srp_erp_pay_emploan_schedule', array('loanID' => $delID));


        //delete loan approvals
        //$this->db->delete('srp_erp_documentapproved', array('documentSystemCode'=> $delID));

        //delete loan details
        $this->db->delete('srp_erp_pay_emploan', array('ID' => $delID));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in Loan deleting Process');
        } else {
            $this->db->trans_commit();
            return array('s','Loan deleted Success');
        }

    }

    function loan_conformation($loanID){
        $this->db->select('loanCode,loanDate')->where('ID', $loanID)->from('srp_erp_pay_emploan');
        $lo_data = $this->db->get()->row_array();

        $documentDate = $lo_data['loanDate'];
        $documentCode = $lo_data['loanCode'];
        $table = 'srp_erp_pay_emploan';
        $primaryColumn = 'ID';

        $validate_code = validate_code_duplication($documentCode, 'loanCode', $loanID,'ID', 'srp_erp_pay_emploan');
        if(!empty($validate_code)) {
            $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
            return array(false, 'error');
        }

        $this->load->library('Approvals');
        $isAutoApproval = get_document_auto_approval('LO');
        if($isAutoApproval == 0){ // If auto approval
            $this->db->trans_start();
            $this->approvals->auto_approve($loanID, $table, $primaryColumn, 'LO', $documentCode, $documentDate);
            $this->db->trans_complete();
            if ($this->db->trans_status() === true) {
                $this->db->trans_commit();
                return ['s', 'Approved successfully'];
            } else {
                $this->db->trans_rollback();
                return ['e', 'Error in approval process'];
            }
        }

        $approvals_status = $this->approvals->CreateApproval('LO', $loanID, $documentCode, 'Loan Approval', $table, $primaryColumn, 0, $documentDate);
        if ($approvals_status == 3) {
            return array('w', 'There are no users exists to perform Loan approval for this company.');
        }
        elseif ($approvals_status == 1) {
            return array('s', 'Create Approval : ' . $lo_data['loanCode']);
        }
        else{
            return array('e', 'Error in Loan Conformation ['.$lo_data['loanCode'].']', $approvals_status);
        }
    }

    function isConformed($loanID){
        $query = $this->db->select('confirmedYN, deductionStartingDate')
                          ->from('srp_erp_pay_emploan')
                          ->where('ID', $loanID);

        return $query->get()->row_array();
    }


    function getLastScheduleDate(){
        $loanID = $this->input->post('loanID');

        $schedule = $this->db->select('MAX(scheduleDate) AS sDate')
                             ->from('srp_erp_pay_emploan AS loan')
                             ->join('srp_erp_pay_emploan_schedule AS schedule', 'schedule.loanID = loan.ID')
                             ->where('loan.ID', $loanID)->get(); //->order_by('schedule.ID', 'DESC')->limit(1)
        $lastSchedule = $schedule->row_array();

        /*$this->db->select('scheduleDate')
                 ->from('srp_erp_pay_emploan AS loan')
                 ->join('srp_erp_pay_emploan_schedule AS schedule', 'schedule.loanID = loan.ID')
                 ->where('loan.ID', $loanID)
                 ->where('isSetteled', 0)
                 ->where('skipedInstallmentID', 0)
                 ->order_by('schedule.ID', 'ASC');
        $newDateQuery = $this->db->get()->row_array();
        $minNewDate = $newDateQuery['scheduleDate'];*/
        $minNewDate = '';
        return array('s', $lastSchedule['sDate'], $minNewDate);
    }

    function skipLoanSchedule(){
        $date_format_policy = date_format_policy();
        $loanID = $this->input->post('hiddenLoanID_skip');
        $loanCode = $this->input->post('hiddenLoanCode_skip');
        $skipID = $this->input->post('skipID');
        $skipDate = $this->input->post('skipDate');

        $skpProeDate = $this->input->post('skipProcessDate');
        $skipProcessDate = input_format_date($skpProeDate,$date_format_policy);
        $skipDescription = $this->input->post('skipDescription');
        
        $isAmount = $this->input->post('isAmount');
        $amount = $this->input->post('amount');
        $resheduledAmount = $this->input->post('resheduleAmount');
        $remainingAmount = $this->input->post('remainingAmount');
        $resheduleType=0;

        $this->form_validation->set_rules('hiddenLoanID_skip', 'Loan ID', 'trim|required');
        $this->form_validation->set_rules('skipID[]', 'Skip ID', 'trim|required');
        $this->form_validation->set_rules('skipDate[]', 'Skip Dates', 'trim|required');
        $this->form_validation->set_rules('skipProcessDate', 'Process Date', 'trim|required|date');
        $this->form_validation->set_rules('skipDescription', 'Description', 'trim|required');
        if($isAmount == 'amount'){
            $this->form_validation->set_rules('resheduleAmount[]', 'Reshedule Amount', 'trim|required');
            $resheduleType=1;
        }
        if($this->form_validation->run()==FALSE){
            return array('e', validation_errors()) ;
        }
        else {

            $errorInstallmentNo = ''; $separator = '';
            foreach($skipID as $skip){
                $this->db->select('isSetteled, skipedInstallmentID, installmentNo')->from('srp_erp_pay_emploan_schedule')->where('ID', $skip);
                $isSettled = $this->db->get()->row_array();

                if( $isSettled['isSetteled'] == 1 || $isSettled['skipedInstallmentID'] != 0 ){
                    $errorInstallmentNo .= $separator.' '.$isSettled['installmentNo'];
                    $separator = ' | ';
                }
            }


            if( $errorInstallmentNo == '' ){
                $createdPCID = $this->common_data['current_pc'];
                $createdUserID  = $this->common_data['current_userID'];
                $createdUserGroup = $this->common_data['user_group'];
                $createdUserName = $this->common_data['current_user'];
                $createdDateTime = $this->common_data['current_date'];
                $j = 0;

                $this->db->trans_start();
                if($isAmount != 'amount'){
                    foreach($skipDate as $scheduleDate) {
                        $scheduleDate= input_format_date($scheduleDate,$date_format_policy);
                        $thisSkipID = $skipID[$j];
                        $companyID = $this->common_data['company_data']['company_id'];
                        $this->db->query("INSERT INTO srp_erp_pay_emploan_schedule ( empID, loanID, loanCode, amountPerInstallment,resheduleType, scheduleDate, installmentNo, interestPer,
                                          isSetteled, skipedInstallmentID, transactionCurrency, transactionER, transactionAmount, transactionCurrencyDecimalPlaces,
                                          companyLocalCurrency, companyLocalER, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingER,
                                          companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID, companyCode, segmentID, segmentCode, createdUserGroup, createdPCID,
                                          createdUserID, createdDateTime, createdUserName)
                                          SELECT empID, loanID, loanCode, amountPerInstallment,0,'".$scheduleDate."', installmentNo, interestPer, isSetteled, 0, transactionCurrency,
                                          transactionER, transactionAmount, transactionCurrencyDecimalPlaces,companyLocalCurrency, companyLocalER, companyLocalAmount,
                                          companyLocalCurrencyDecimalPlaces, companyReportingCurrency,companyReportingER,companyReportingAmount, companyReportingCurrencyDecimalPlaces,
                                          companyID, companyCode,segmentID, segmentCode, '".$createdUserGroup."', '".$createdPCID."', {$createdUserID}, '".$createdDateTime."' , '".$createdUserName."'
                                          FROM srp_erp_pay_emploan_schedule WHERE ID = {$thisSkipID} AND companyID = {$companyID}");
    
                        $last_skipID = $this->db->insert_id();
    
                        $updateDet = array(
                            'resheduleType' => 0,
                            'skippedDate'        => $skipProcessDate,
                            'skippedDescription' => $skipDescription,
                            'skipedInstallmentID'=> $last_skipID,
                            'modifiedPCID'       => $createdPCID,
                            'modifiedUserID'     => $createdUserID,
                            'modifiedUserName'   => $createdUserName,
                            'modifiedDateTime'   => $createdDateTime
                        );
    
                        $this->db->where('ID', $thisSkipID );
                        $this->db->update('srp_erp_pay_emploan_schedule',$updateDet);
    
                        $j++;
                    }
                }else{
                    foreach($resheduledAmount as $rAmount) {
                        $thisAmountPerInstallment = $amount[$j];
                        if($rAmount != $thisAmountPerInstallment) {
                            
                            $scheduleDate= input_format_date($skipDate[$j],$date_format_policy);
                            $thisSkipID = $skipID[$j];
                            $thisAmountPerInstallment = $amount[$j];
                            $thisRemainingAmount= $remainingAmount[$j];
                            $companyID = $this->common_data['company_data']['company_id'];
                            $this->db->query("INSERT INTO srp_erp_pay_emploan_schedule ( empID, loanID, loanCode, amountPerInstallment, resheduleType,scheduleDate, installmentNo, interestPer,
                                            isSetteled, skipedInstallmentID,resheduledInstallmentID,resheduledDate,skippedDescription,amountBeforeReshedule, transactionCurrency, transactionER, transactionAmount, transactionCurrencyDecimalPlaces,
                                            companyLocalCurrency, companyLocalER, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingER,
                                            companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID, companyCode, segmentID, segmentCode, createdUserGroup, createdPCID,
                                            createdUserID, createdDateTime, createdUserName,transactionCurrencyID,companyLocalcurrencyID,companyReportingCurrencyID)
                                            SELECT empID, loanID, loanCode, '".$thisRemainingAmount."', 1,'".$scheduleDate."', installmentNo, interestPer, isSetteled,0, '".$thisSkipID."', '".$skipProcessDate."','".$skipDescription."', '".$thisAmountPerInstallment."',transactionCurrency,
                                            transactionER, '".$thisRemainingAmount."', transactionCurrencyDecimalPlaces,
                                            companyLocalCurrency, companyLocalER,IF(companyLocalER > 0,(ROUND(($thisRemainingAmount/companyLocalER),companyLocalCurrencyDecimalPlaces)),(ROUND($thisRemainingAmount,companyLocalCurrencyDecimalPlaces))) as companyLocalAmount,
                                            companyLocalCurrencyDecimalPlaces, 
                                            companyReportingCurrency,companyReportingER,IF(companyReportingER > 0,( ROUND(($thisRemainingAmount/companyReportingER),companyReportingCurrencyDecimalPlaces)),(ROUND($thisRemainingAmount,companyReportingCurrencyDecimalPlaces))) as companyReportingAmount, companyReportingCurrencyDecimalPlaces,
                                            companyID, companyCode,segmentID, segmentCode, '".$createdUserGroup."', '".$createdPCID."', {$createdUserID}, '".$createdDateTime."' , '".$createdUserName."',transactionCurrencyID,companyLocalcurrencyID,companyReportingCurrencyID
                                            FROM srp_erp_pay_emploan_schedule WHERE ID = {$thisSkipID} AND companyID = {$companyID}");
        
                            $last_skipID = $this->db->insert_id();
                            
                            $this->db->select('companyLocalER,companyLocalCurrencyDecimalPlaces,companyReportingER,companyReportingCurrencyDecimalPlaces')->from('srp_erp_pay_emploan_schedule')->where('ID', $thisSkipID);
                            $currecydet = $this->db->get()->row_array();
                            $localAmount = ( $currecydet['companyLocalER'] > 0)? round(($rAmount/$currecydet['companyLocalER']), $currecydet['companyLocalCurrencyDecimalPlaces']) : round($rAmount, $currecydet['companyLocalCurrencyDecimalPlaces']);
                            $reportingAmount = ( $currecydet['companyReportingER'] > 0)? round(($rAmount/$currecydet['companyReportingER']), $currecydet['companyReportingCurrencyDecimalPlaces']) : round($rAmount, $currecydet['companyReportingCurrencyDecimalPlaces']);

                            $updateDet = array(
                                'resheduleType' => 1,
                                'amountPerInstallment'  => $rAmount,
                                'transactionAmount'     => $rAmount,
                                'companyLocalAmount'     => $localAmount,
                                'companyReportingAmount'     => $reportingAmount,

                                //'resheduledDate'           => $skipProcessDate,
                                //'skippedDescription'    => $skipDescription,
                                // 'resheduledInstallmentID'   => $last_skipID,
                                //'amountBeforeReshedule'  => $thisAmountPerInstallment,
                                'modifiedPCID'          => $createdPCID,
                                'modifiedUserID'        => $createdUserID,
                                'modifiedUserName'      => $createdUserName,
                                'modifiedDateTime'      => $createdDateTime
                            );
        
                            $this->db->where('ID', $thisSkipID );
                            $this->db->update('srp_erp_pay_emploan_schedule',$updateDet);
                        }
                        $j++;
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Failed to Update');
                } else {
                    $this->db->trans_commit();
                    return array('s', '[ '.$loanCode.'] schedule skipped successfully');
                }
            }
            else{
                return array('e',  $errorInstallmentNo .' this/these installment are already settled/skipped  ');
            }

        }
    }

    function isPreviousLevelsApproved(){
        $loanID = $this->input->post('loanID');
        $docID = $this->input->post('docID');
        $appLevel =  $this->input->post('appLevel') - 1;

        // 'documentApprovedID <' => $docID,
        $where = array(
            'documentID' => 'LO',
            'documentSystemCode' => $loanID,
            'approvalLevelID' => $appLevel
        );

        $query = $this->db->select('approvedYN')->from('srp_erp_documentapproved')->where($where)->get();
        $isApproved = $query->row_array();

        if( !empty($isApproved) ){
            return true;
        }else{
            return false;
        }
    }

    function empLoanView_data($empID){
        $empCurrentLoans =  $this->db->query("SELECT ID, loanCode, deductionStartingDate AS dDate, loanDescription, transactionAmount,
                                              transactionCurrencyDecimalPlaces AS dPlace, transactionCurrency, confirmedYN, approvedYN,
                                              isClosed
                                              FROM srp_erp_pay_emploan WHERE empID={$empID} ")->result_array(); /*AND isClosed != 1
                                              AND approvedYN = 1 */

        $current = array();
        $i = 0;
        foreach($empCurrentLoans as $empCurLoan){
            $loanID = $empCurLoan['ID'];
            $loanMore =  $this->db->query("SELECT  schedule.transactionAmount AS intAmount, schedule.transactionCurrencyDecimalPlaces AS dPlace,
                                           (SELECT count(ID) FROM srp_erp_pay_emploan_schedule WHERE loanID={$loanID} AND isSetteled = 1) AS settled,
                                           (SELECT count(ID) FROM srp_erp_pay_emploan_schedule WHERE loanID={$loanID} AND isSetteled = 0 AND skipedInstallmentID = 0) AS pending,
                                           (SELECT count(ID) FROM srp_erp_pay_emploan_schedule WHERE loanID={$loanID} AND isSetteled = 0 AND skipedInstallmentID != 0) AS skipped
                                           FROM srp_erp_pay_emploan AS loan JOIN srp_erp_pay_emploan_schedule  AS schedule
                                           ON schedule.loanID = loan.ID
                                           WHERE loan.ID={$loanID} GROUP BY schedule.loanID")->row_array();
            $current[$i]['header'] = $empCurLoan;
            $current[$i]['installment'] = $loanMore;

            $i++;
        }

        return array(
            'empCurrentLoans' => $current
        );
    }

    function empLoan_installmentDetails(){
        $loanID = $this->input->post('loanID');
        $dType = $this->input->post('dType');

        if( $dType == 'settled' ){
            return $this->db->query("SELECT scheduleDate, installmentNo FROM srp_erp_pay_emploan_schedule
                                     WHERE loanID={$loanID} AND isSetteled=1 ORDER BY scheduleDate")->result_array();
        }
        elseif( $dType == 'pending' ){
            return $this->db->query("SELECT scheduleDate, installmentNo FROM srp_erp_pay_emploan_schedule
                                     WHERE loanID={$loanID} AND isSetteled=0 AND skipedInstallmentID=0 ORDER BY scheduleDate")->result_array();
        }
        elseif( $dType == 'skipped' ){
            return $this->db->query("SELECT scheduleDate, installmentNo, skippedDescription FROM srp_erp_pay_emploan_schedule
                                     WHERE loanID={$loanID} AND skipedInstallmentID!=0 ORDER BY scheduleDate")->result_array();
        }
    }

    function empLoanSchedule_print($id){
        $installmentAmount = $this->db->query("SELECT transactionAmount  FROM srp_erp_pay_emploan_schedule
                                               WHERE loanID={$id} GROUP BY loanID")->row_array();

        $schedule  = $this->db->query("SELECT scheduleDate, installmentNo, isSetteled, skippedDate, skipedInstallmentID
                                       FROM srp_erp_pay_emploan_schedule WHERE loanID={$id}")->result_array();

        $installment =  $this->db->query("SELECT  schedule.transactionAmount AS intAmount, schedule.transactionCurrencyDecimalPlaces AS dPlace,
                                        (SELECT count(ID) FROM srp_erp_pay_emploan_schedule WHERE loanID={$id} AND isSetteled = 1) AS settled,
                                        (SELECT count(ID) FROM srp_erp_pay_emploan_schedule WHERE loanID={$id} AND isSetteled = 0 AND skipedInstallmentID = 0) AS pending,
                                        (SELECT count(ID) FROM srp_erp_pay_emploan_schedule WHERE loanID={$id} AND isSetteled = 0 AND skipedInstallmentID != 0) AS skipped
                                        FROM srp_erp_pay_emploan AS loan JOIN srp_erp_pay_emploan_schedule  AS schedule
                                        ON schedule.loanID = loan.ID
                                        WHERE loan.ID={$id} GROUP BY schedule.loanID")->row_array();

        return array(
            'intAmount' => $installmentAmount,
            'schedule' => $schedule,
            'installment' => $installment
        );
    }

}