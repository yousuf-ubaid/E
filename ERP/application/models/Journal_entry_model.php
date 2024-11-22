<?php

class Journal_entry_model extends ERP_Model
{

    function save_journal_entry_header()
    {
        $this->db->trans_start();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $date_format_policy = date_format_policy();
        $Jdates = $this->input->post('JVdate');
        $JVdate = input_format_date($Jdates, $date_format_policy);
        $JVType = $this->input->post('JVType');
        $JVMasterAutoId = $this->input->post('JVMasterAutoId');
        $company_type =  $this->session->userdata("companyType");       /**SMSD */
        $group_id = $this->session->userdata("companyID");             /**SMSD */

    
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        if($financeyearperiodYN==1) {
            $companyFinancePeriod = trim($this->input->post('companyFinancePeriod') ?? '');
            $period = explode(' - ', trim($companyFinancePeriod));
            $PeriodBegin = input_format_date($period[0], $date_format_policy);
            $PeriodEnd = input_format_date($period[1], $date_format_policy);

            $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        }else{

            if($company_type != 2){    /**SMSD */   /* add to exit the error("Finance period not found for the selected document date") - need to discuss */
                
                $financeYearDetails=get_financial_year($JVdate);
                if(empty($financeYearDetails)){
                    $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                    return array('status' => false);
                    exit;
                }else{
                    $FYBegin=$financeYearDetails['beginingDate'];
                    $FYEnd=$financeYearDetails['endingDate'];
                    $_POST['companyFinanceYear'] = $FYBegin.' - '.$FYEnd;
                    $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
                }
                $financePeriodDetails=get_financial_period_date_wise($JVdate);

                if(empty($financePeriodDetails)){
                    $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                    return array('status' => false);
                    exit;
                }else{
                    $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
                    $PeriodBegin = $financePeriodDetails['dateFrom'];
                    $PeriodEnd = $financePeriodDetails['dateTo'];
                }
            }         /**SMSD */
        }

        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));

        //Gratutity validations
        if($JVType == 'Gratuity' && empty($JVMasterAutoId)){

            $error_response = check_jv_valid_period($this->input->post('financeyear_period'));

            if($error_response){
                $this->session->set_flashdata($error_response[0], $error_response[1]);
                return $error_response;
            }
        }
        // Gratutity validations end

        if($JVType == 'SalaryProvision' && empty($JVMasterAutoId)){

            $error_response = check_leave_salary_provision_setup();

            if($error_response){
                $this->session->set_flashdata($error_response[0], $error_response[1]);
                return $error_response;
            }

            $error_response = check_jv_valid_period($this->input->post('financeyear_period'),'SalaryProvision');

            if($error_response){
                $this->session->set_flashdata($error_response[0], $error_response[1]);
                return $error_response;
            }
        }
       
        $data['documentID'] = 'JV';
        $data['JVType'] = trim($this->input->post('JVType') ?? '');
        $data['JVdate'] = trim($JVdate);
        $JVNarration = ($this->input->post('JVNarration'));
        $data['JVNarration'] = str_replace('<br />', PHP_EOL, $JVNarration);
        //$data['JVNarration'] = trim_desc($this->input->post('JVNarration'));
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        $data['FYPeriodDateFrom'] = trim($PeriodBegin);
        $data['FYPeriodDateTo'] = trim($PeriodEnd);
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionCurrency'] = trim($currency_code[0] ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];

        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

        if($company_type == 2){   /**SMSD */                                                              /*new*/
            $data['groupId'] = $group_id;/**SMSD */ //$this->common_data['company_data']['company_id'];   /*new*/
        }else{ /**SMSD */                                                                                 /*new*/
            $data['companyID'] = $this->common_data['company_data']['company_id'];              /*new*/
        }   /**SMSD */                                                                                    /*new*/
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        if (trim($this->input->post('JVMasterAutoId') ?? '')) {

            $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
            $this->db->update('srp_erp_jvmaster', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Journal Entry : (' . $data['JVType'] . ' ) ' . $data['JVNarration'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Journal Entry : (' . $data['JVType'] . ' ) ' . $data['JVNarration'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('JVMasterAutoId'));
            }
        } else {
            //$this->load->library('sequence');
            //$data['companyID'] = $this->common_data['company_data']['company_id'];       /**SMSD */
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['JVcode'] = 0;
            $this->db->insert('srp_erp_jvmaster', $data);
            $last_id = $this->db->insert_id();

            if($last_id && $JVType == 'Gratuity'){
                $this->set_Gratuity_Provision($last_id);
            }

            if($last_id && $JVType == 'SalaryProvision'){
                $this->set_salary_provision_details($last_id);
            }
           

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Journal Entry : (' . $data['JVType'] . ' ) ' . $data['JVNarration'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Journal Entry : (' . $data['JVType'] . ' ) ' . $data['JVNarration'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function set_Gratuity_Provision($jvAutoID){

  
        $companyID = current_companyID();
        //get jv master details
        $this->db->where('JVMasterAutoId', $jvAutoID);
        $master_record = $this->db->from('srp_erp_jvmaster')->get()->row_array();

        //get the last ran gratuvity records
        // add approve 1
        $this->db->where('JVType','Gratuity');
        $this->db->where('JVMasterAutoId !=',$jvAutoID);
        $this->db->order_by('JVMasterAutoId','DESC');
        $last_ran_record = $this->db->from('srp_erp_jvmaster')->get()->row_array();

        if($last_ran_record){

            $lastJVMasterAutoId = $last_ran_record['JVMasterAutoId'];
            
            //without reversal entries
            $lastJVMasterDetails = get_jv_master_record_details($lastJVMasterAutoId);

            // reverse last records
            foreach($lastJVMasterDetails as  $key => $details_reverse){

                $base_arr = $details_reverse;
                unset($details_reverse['JVDetailAutoID']);
                unset($details_reverse['JVMasterAutoId']);

                $details_reverse['debitAmount'] = $base_arr['creditAmount'];
                $details_reverse['debitCompanyLocalAmount'] = $base_arr['creditCompanyLocalAmount'];
                $details_reverse['debitCompanyReportingAmount'] = $base_arr['creditCompanyReportingAmount'];
 
                $details_reverse['creditAmount'] = $base_arr['debitAmount'];
                $details_reverse['creditCompanyLocalAmount'] = $base_arr['debitCompanyLocalAmount'];
                $details_reverse['creditCompanyReportingAmount'] = $base_arr['debitCompanyReportingAmount'];

                $details_reverse['isReversal'] = 1;

                $details_reverse['JVMasterAutoId'] = $jvAutoID;

                $res = $this->db->insert('srp_erp_jvdetail', $details_reverse);

            }

            // add new records

            // get gratuvity
            $gratuity_arr = array();

            $this->db->select('gratuityID');
            $this->db->where('companyID',$companyID);
            $master_records_gr = $this->db->from('srp_erp_pay_gratuitymaster')->get()->result_array();

            foreach($master_records_gr as $gratuity){
                $gratuity_arr[] = $gratuity['gratuityID'];
            }

            $_POST['as_of_date'] = $last_ran_record['JVdate'];
            $_POST['gratuityID'] = $gratuity_arr;
            $grat_record = $this->get_salary_gratuvity_records();

            //update provision tables
            $this->set_provision_record_as_of_today($grat_record,$jvAutoID);

            //Group and hit to detail
            $pulled_data_expense = $this->pull_expense_provision_gls_amount($jvAutoID,'expense');
            $pulled_data_provision = $this->pull_expense_provision_gls_amount($jvAutoID,'provision');


            foreach($pulled_data_expense as $expense_records){
                
                $chartofaccount = $expense_records['grExpenseGL'];

                $gldata = fetch_gl_account_desc($chartofaccount);

               
                $data = array();

                $data['JVMasterAutoId'] = $jvAutoID;
                $data['type'] = 'GL';
                $data['gl_type'] = 'Dr';
                $data['GLAutoID'] = $chartofaccount;
                $data['systemGLCode'] = $gldata['systemAccountCode'];
                $data['GLCode'] = $gldata['GLSecondaryCode'];
                $data['GLDescription'] = $gldata['GLDescription'];
                $data['GLType'] = $gldata['subCategory'];
                $data['description'] = $gldata['GLDescription'];

                $data['debitAmount'] = $expense_records['amount'];
                $data['debitCompanyLocalAmount'] = $expense_records['amount'];
                $data['debitCompanyReportingAmount'] = $expense_records['amount'];
 
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];

                $res = $this->db->insert('srp_erp_jvdetail', $data);

            }

            foreach($pulled_data_provision as $provision_records){
                
                $chartofaccount = $provision_records['grProvisionGL'];

                $gldata = fetch_gl_account_desc($chartofaccount);

                if(empty($gldata)){
                    continue;
                }
                
                $data = array();

                $data['JVMasterAutoId'] = $jvAutoID;
                $data['type'] = 'GL';
                $data['gl_type'] = 'Dr';
                $data['GLAutoID'] = $chartofaccount;
                $data['systemGLCode'] = $gldata['systemAccountCode'];
                $data['GLCode'] = $gldata['GLSecondaryCode'];
                $data['GLDescription'] = $gldata['GLDescription'];
                $data['GLType'] = $gldata['subCategory'];
                $data['description'] = $gldata['GLDescription'];

                $data['creditAmount'] = $provision_records['amount'];
                $data['creditCompanyLocalAmount'] = $provision_records['amount'];
                $data['creditCompanyReportingAmount'] = $provision_records['amount'];
 
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];

                $res = $this->db->insert('srp_erp_jvdetail', $data);

            }

        }

        return TRUE;

    }

    function get_salary_gratuvity_records(){

        $this->load->helper('employee');
        $requestType = $this->uri->segment(3);
        $companyID = current_companyID();
        $as_of_date_str = $as_of_date = $this->input->post('as_of_date');
        $date_format_policy = date_format_policy();
        $as_of_date = input_format_date($as_of_date, $date_format_policy);
        $firstDate = date('Y-m-01', strtotime($as_of_date));
        $convertFormat = convert_date_format_sql();
        $gratuity_arr = $this->input->post('gratuityID');
        $gratuity_list = join(',', $gratuity_arr);

        $gratuityMaster = $this->db->query("SELECT t1.gratuityID, gratuityDescription, formulaString, provisionGL,expenseGL                                 
                            FROM srp_erp_pay_gratuitymaster t1 JOIN srp_erp_pay_gratuityformula t2 ON t2.autoID = t1.gratuityID 
                            AND t1.gratuityID IN ({$gratuity_list}) AND t2.masterType='GRATUITY' ")->result_array();

        $gr_data = []; $currency = [];
        foreach ($gratuityMaster as $gr_mas_data) {
            $dPlace = 3;
            $gratuityID = $gr_mas_data['gratuityID'];
            $gratuitySlabData = $this->db->query("SELECT t1.id, slabTitle, formulaString, startYear, endYear FROM srp_erp_pay_gratuityslab t1                                    
                                    JOIN srp_erp_pay_gratuityformula t3 ON t3.autoID = t1.id AND t3.masterType='GRATUITY-SLAB'
                                    WHERE t1.gratuityMasterID='{$gratuityID}'")->result_array();

            $slab_wise = [];
            if (!empty($gratuitySlabData)) {
                $slabStr = 'round((CASE';
                foreach ($gratuitySlabData as $slabKey => $slabData) {
                    $gr_data[$gratuityID]['slab_det'][$slabData['id']] = $slabData['slabTitle'];
                    $endYear = $slabData['endYear'];
                    $result_slab = formulaBuilder_to_sql_simple_conversion($slabData['formulaString']);
                    $formula_slab = $result_slab['formulaDecode'];

                    $slabStr .= ' WHEN (totalWorkingDays/365) <= ' . $endYear . ' THEN ' . $formula_slab;
                    $slab_wise[$slabData['id']] = $endYear - 0.001;
                }
                $slabStr .= ' ELSE 0 END), ' . $dPlace . ') AS gratuityAmount';

                $result = formulaBuilder_to_sql_simple_conversion($gr_mas_data['formulaString']);

                $formula = $result['formulaDecode'];
                $salCat = $result['select_str'];
                $salCat2 = $result['select_str2'];
                $whereInClause = $result['whereInClause'];

                $details = $this->db->query("SELECT empID, totalWork, totFixPayment, joinDate, designation, payCurrencyID, ECode, Ename2, gratuityID, {$slabStr} FROM (
                                            SELECT empID, totalWorkingDays, CONCAT(age,'Y ',days,'D') AS totalWork, joinDate, designation, gratuityID,
                                            payCurrencyID, ECode, Ename2, {$formula} AS totFixPayment
                                            FROM (
                                                SELECT empID, DATE_FORMAT(EDOJ,'{$convertFormat}') AS joinDate, DesDescription AS designation, 
                                                payCurrencyID, ECode, Ename2, empTB.gratuityID,
                                                IF (
                                                    isDischarged = 0 OR dischargedDate >= '{$as_of_date}', DATEDIFF( '{$as_of_date}', EDOJ ),
                                                    IF ( finalSettlementDoneYN = 0, DATEDIFF( dischargedDate, EDOJ ), 0 )
                                                ) AS totalWorkingDays,
                                                IF (
                                                    isDischarged = 0 OR dischargedDate >= '{$as_of_date}', TIMESTAMPDIFF( YEAR, empTB.EDOJ, '{$as_of_date}' ),
                                                    TIMESTAMPDIFF( YEAR, empTB.EDOJ, dischargedDate )
                                                ) AS age,
                                                IF (
                                                    isDischarged = 0 OR dischargedDate >= '{$as_of_date}',
                                                    IF(
                                                        TIMESTAMPDIFF( YEAR, empTB.EDOJ, '{$as_of_date}' ) = 0, DATEDIFF( '{$as_of_date}', empTB.EDOJ ),
                                                        FLOOR( TIMESTAMPDIFF( DAY, empTB.EDOJ, '{$as_of_date}' ) % 365 )
                                                    ),  
                                                    IF(
                                                        TIMESTAMPDIFF( YEAR, empTB.EDOJ, dischargedDate ) = 0, DATEDIFF( dischargedDate, empTB.EDOJ ),
                                                        FLOOR( TIMESTAMPDIFF( DAY, empTB.EDOJ, dischargedDate ) % 365 )
                                                    )
                                                ) AS days, {$salCat2}                                                                    
                                                FROM srp_employeesdetails AS empTB 
                                                LEFT JOIN (
                                                    SELECT DesignationID, DesDescription FROM srp_designation WHERE Erp_companyID = {$companyID} AND isDeleted = 0
                                                ) AS des_tb ON des_tb.DesignationID = empTB.EmpDesignationId 
                                                JOIN (
                                                    SELECT employeeNo AS empID, {$salCat} FROM srp_erp_pay_salarydeclartion salDec
                                                    JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = salDec.employeeNo
                                                    WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} 
                                                    AND effectiveDate <= '{$as_of_date}' AND amount IS NOT NULL 
                                                    AND salaryCategoryID IN ({$whereInClause}) GROUP BY empID, salaryCategoryID
                                                )  salDec ON salDec.empID = empTB.EIdNo
                                                WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} AND 
                                                ( isDischarged = 0 OR ( dischargedDate >= '{$firstDate}' ) OR (isDischarged = 1 AND empTB.finalSettlementDoneYN = 0 ) ) 
                                                GROUP BY empTB.EIdNo
                                            ) empSalary GROUP BY empSalary.empID ORDER BY ECode
                                      ) t1")->result_array();

                $slab_wise_amount = [];
                foreach ($slab_wise as $slab_id => $end) {

                    $slabStr = 'round((CASE';
                    foreach ($gratuitySlabData as $slabKey => $slabData) {
                        $endYear = $slabData['endYear'];
                        $result_slab = formulaBuilder_to_sql_simple_conversion($slabData['formulaString']);
                        $formula_slab = $result_slab['formulaDecode'];

                        if (($endYear - 0.001) != $end) {
                            $formula_slab = 0;
                        }
                        $slabStr .= ' WHEN (totalWorkingDays/365) <= ' . $endYear . ' THEN ' . $formula_slab;

                    }
                    $slabStr .= ' ELSE 0 END), ' . $dPlace . ') AS gratuityAmount';

                    $this_data = $this->db->query("SELECT empID, {$slabStr} FROM (
                                            SELECT empID, IF((totalWorkingDays/365) > {$end}, (365*{$end}), totalWorkingDays) AS totalWorkingDays,   
                                            {$formula} AS totFixPayment  FROM (
                                                SELECT empID,  
                                                IF (
                                                    isDischarged = 0 OR dischargedDate >= '{$as_of_date}', DATEDIFF( '{$as_of_date}', EDOJ ),
                                                    IF ( finalSettlementDoneYN = 0, DATEDIFF( dischargedDate, EDOJ ), 0 )
                                                ) AS totalWorkingDays,
                                                {$salCat2}                                                                    
                                                FROM srp_employeesdetails empTB 
                                                JOIN (
                                                    SELECT employeeNo AS empID, {$salCat} FROM srp_erp_pay_salarydeclartion salDec
                                                    JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = salDec.employeeNo
                                                    WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} 
                                                    AND effectiveDate <= '{$as_of_date}' AND amount IS NOT NULL 
                                                    AND salaryCategoryID IN ({$whereInClause}) GROUP BY empID, salaryCategoryID
                                                )  salDec ON salDec.empID = empTB.EIdNo
                                                WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} AND
                                                ( isDischarged = 0 OR ( dischargedDate >= '{$firstDate}' ) OR (isDischarged = 1 AND empTB.finalSettlementDoneYN = 0 ) )
                                                GROUP BY empTB.EIdNo
                                            ) empSalary GROUP BY empSalary.empID
                                      ) t1")->result_array();

                    $slab_wise_amount[$slab_id] = array_group_by($this_data, 'empID');
                }
            }

            foreach ($details as $key => $row) {
                $emp_id = $row['empID'];
                if(!in_array($row['payCurrencyID'], $currency)){
                    $currency[] = $row['payCurrencyID'];
                }
                $sum = 0;
                foreach ($gratuitySlabData as $slabKey => $slabData) {
                    $slab_id = $slabData['id'];
                    $amount = $slab_wise_amount[$slab_id][$emp_id][0]['gratuityAmount'];
                    $amount = ($amount > 0) ? $amount - $sum : 0;
                    $details[$key]['slab'][$slab_id] = $amount;

                    $sum += $amount;
                }
            }

            $gr_data[$gratuityID]['details'] = $details;
        }

        $loc_cur = $this->common_data['company_data']['company_default_currencyID'];
        $rpt_cur = $this->common_data['company_data']['company_reporting_currencyID'];


        $currency_det = [];
        foreach ($currency as $item){
            $reportCon = currency_conversionID($item, $rpt_cur, 0);
            $currency_det[$item]['rpt'] = $reportCon;

            $localCon = currency_conversionID($item, $loc_cur, 0);
            $currency_det[$item]['loc'] = $localCon;
        }


        $gratuityMaster = array_group_by($gratuityMaster, 'gratuityID');

        $data['as_of_date_str'] = $as_of_date_str;
        $data['currency_det'] = $currency_det;
        $data['gratuityMaster'] = $gratuityMaster;
        $data['gr_data'] = $gr_data;
        $data['loc_curr'] = $this->common_data['company_data']['company_default_currency'];
        $data['rpt_curr'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['rpt_dPlace'] = $this->db->get_where('srp_erp_currencymaster', ['currencyID' => $rpt_cur])->row('DecimalPlaces');
        $data['loc_dPlace'] = $this->db->get_where('srp_erp_currencymaster', ['currencyID' => $loc_cur])->row('DecimalPlaces');

        return $data;
    }

    function set_provision_record_as_of_today($records,$jvAutoID){

        $gr_data = $records['gr_data'];
        $gr_masters = $records['gratuityMaster'];
        $gr_currency = $records['currency_det'];
        $companyID = current_companyID();

        foreach($gr_data as $gr_emp_details){

            $gr_emp = $gr_emp_details['details'];

            foreach($gr_emp as $emp_detail){

                $this->db->where('JVmasterID',$jvAutoID);
                $this->db->where('empID',$emp_detail['empID']);
                $provision = $this->db->from('srp_erp_jv_provision_detail')->get()->row_array();

                if($provision){
                    continue;
                }

                $data = array();

                $data['JVmasterID'] = $jvAutoID;
                $data['gratuityID'] = $emp_detail['gratuityID'];
                $data['empID'] = $emp_detail['empID'];
                $data['grProvisionGL'] = $gr_masters[$emp_detail['gratuityID']][0]['provisionGL'];
                $data['grExpenseGL'] = $gr_masters[$emp_detail['gratuityID']][0]['expenseGL'];
                $data['amount'] = $emp_detail['gratuityAmount'];
                $data['companyID'] = $companyID;

                $res = $this->db->insert('srp_erp_jv_provision_detail',$data);
                
            }

        }



    }

    function pull_expense_provision_gls_amount($jvAutoID,$type){

        $companyID = current_companyID();

        if($type == 'provision'){
           $data = $this->db->query("SELECT grProvisionGL,SUM(amount) as amount
                FROM `srp_erp_jv_provision_detail`
                WHERE companyID = {$companyID}
                AND JVmasterID = {$jvAutoID}
                GROUP BY grProvisionGL")->result_array();

        }else{
            $data = $this->db->query("SELECT grExpenseGL,SUM(amount) as amount
                FROM `srp_erp_jv_provision_detail`
                WHERE companyID = {$companyID}
                AND JVmasterID = {$jvAutoID}
                GROUP BY grExpenseGL")->result_array();
        }

        return $data;

    }

    function save_gl_detail()
    {
        $projectExist = project_is_exist();
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('JVMasterAutoId', $this->input->post('JVMasterAutoId'));
        $master = $this->db->get('srp_erp_jvmaster')->row_array();

        $gl_codes = $this->input->post('gl_code');
        $gl_code_des = $this->input->post('gl_code_des');
        /*$gl_types = $this->input->post('gl_type');*/
        $debitAmount = $this->input->post('debitAmount');
        $creditAmount = $this->input->post('creditAmount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');
        $projectID = $this->input->post('projectID');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        
        $company_IDs = $this->input->post('companyID');              /**SMSD */
        $companyType = $this->session->userdata("companyType");      /**SMSD */

        foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $segment_gls[$key]);
            
            if($companyType == 2){/**SMSD */
                foreach($company_IDs as $key => $companyID){/**SMSD */
                    $gldata = fetch_gl_account_desc($gl_codes[$key],$company_IDs[$key]);/**SMSD */
                }/**SMSD */
            }else{/**SMSD */
                $gldata = fetch_gl_account_desc($gl_codes[$key]);
            } /**SMSD */

            if ($gldata['masterCategory'] == 'PL') {
                $data[$key]['segmentID'] = trim($segment[0] ?? '');
                $data[$key]['segmentCode'] = trim($segment[1] ?? '');
            } else {
                /*   $data[$key]['segmentID'] = trim($segment[0] ?? '');
                   $data[$key]['segmentCode'] = trim($segment[1] ?? '');*/
                $data[$key]['segmentID'] = null;
                $data[$key]['segmentCode'] = null;
            }

            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data[$key]['projectID'] = $projectID[$key];
                $data[$key]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data[$key]['project_categoryID'] = $project_categoryID[$key];
                $data[$key]['project_subCategoryID'] = $project_subCategoryID[$key];
            }

            $gl_des = explode('|', $gl_code_des[$key]);
            $data[$key]['JVMasterAutoId'] = trim($this->input->post('JVMasterAutoId') ?? '');
            $data[$key]['GLAutoID'] = $gl_codes[$key] ?? '';
            $data[$key]['systemGLCode'] = trim($gl_des[0] ?? '');
            $data[$key]['GLCode'] = trim($gl_des[1] ?? '');
            $data[$key]['GLDescription'] = trim($gl_des[2] ?? '');
            $data[$key]['GLType'] = trim($gl_des[3] ?? '');
            $data[$key]['projectID'] = $projectID[$key] ?? '';

            if ($creditAmount[$key] > 0) {
                $data[$key]['gl_type'] = 'Cr';
            } else {
                $data[$key]['gl_type'] = 'Dr';
            }

            if ($data[$key]['gl_type'] == 'Cr') {
                $data[$key]['creditAmount'] = round($creditAmount[$key], $master['transactionCurrencyDecimalPlaces']);
                $creditCompanyLocalAmount = (float)$data[$key]['creditAmount'] / $master['companyLocalExchangeRate'];
                $data[$key]['creditCompanyLocalAmount'] = round($creditCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $creditCompanyReportingAmount = (float)$data[$key]['creditAmount'] / $master['companyReportingExchangeRate'];
                $data[$key]['creditCompanyReportingAmount'] = round($creditCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);

                //updating the value as 0
                $data[$key]['debitAmount'] = 0;
                $data[$key]['debitCompanyLocalAmount'] = 0;
                $data[$key]['debitCompanyReportingAmount'] = 0;

                if($gldata['isBank']==1){
                    $data[$key]['isBank'] = 1;
                    $data[$key]['bankCurrencyID'] = $gldata['bankCurrencyID'];
                    $data[$key]['bankCurrency'] = $gldata['bankCurrencyCode'];
                    $bank_currency = currency_conversionID($master['transactionCurrencyID'], $gldata['bankCurrencyID']);
                    $data[$key]['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                    $data[$key]['bankCurrencyAmount'] = $data[$key]['creditAmount'] / $bank_currency['conversion'];
                }else{
                    $data[$key]['isBank'] = 0;
                    $data[$key]['bankCurrencyID'] = null;
                    $data[$key]['bankCurrency'] = null;
                    $data[$key]['bankCurrencyExchangeRate'] = null;
                    $data[$key]['bankCurrencyAmount'] = null;
                }
            } else {


                $data[$key]['debitAmount'] = round($debitAmount[$key], $master['transactionCurrencyDecimalPlaces']);
                $debitCompanyLocalAmount = (float)$data[$key]['debitAmount'] / $master['companyLocalExchangeRate'];
                $data[$key]['debitCompanyLocalAmount'] = round($debitCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $debitCompanyReportingAmount = (float)$data[$key]['debitAmount'] / $master['companyReportingExchangeRate'];
                $data[$key]['debitCompanyReportingAmount'] = round($debitCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);

                //updating the value as 0
                $data[$key]['creditAmount'] = 0;
                $data[$key]['creditCompanyLocalAmount'] = 0;
                $data[$key]['creditCompanyReportingAmount'] = 0;

                if($gldata['isBank']==1){
                    $data[$key]['isBank'] = 1;
                    $data[$key]['bankCurrencyID'] = $gldata['bankCurrencyID'];
                    $data[$key]['bankCurrency'] = $gldata['bankCurrencyCode'];
                    $bank_currency = currency_conversionID($master['transactionCurrencyID'], $gldata['bankCurrencyID']);
                    $data[$key]['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                    $data[$key]['bankCurrencyAmount'] = $data[$key]['debitAmount'] / $bank_currency['conversion'];
                }else{
                    $data[$key]['isBank'] = 0;
                    $data[$key]['bankCurrencyID'] = null;
                    $data[$key]['bankCurrency'] = null;
                    $data[$key]['bankCurrencyExchangeRate'] = null;
                    $data[$key]['bankCurrencyAmount'] = null;
                }
            }
            $data[$key]['description'] = $descriptions[$key];
            $data[$key]['type'] = 'GL';

            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];    /*new*/
            if($companyType == 2){        /**SMSD */                                                      /*new*/
                $data[$key]['companyID'] = $company_IDs[$key];   /**SMSD */                               /*new*/
            }else{  /**SMSD */                                                                            /*new*/
                $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];    /*new*/
            }    /**SMSD */                                                                               /*new*/
            
        }

        $this->db->insert_batch('srp_erp_jvdetail', $data);
        /*$last_id = $this->db->insert_id();*/
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'GL Description : Saved Failed ');
        } else {
            $this->db->trans_commit();
            return array('s', 'GL Description :  Saved Successfully.');
        }


        if (trim($this->input->post('JVDetailAutoID') ?? '')) {
            /*$this->db->where('JVDetailAutoID', trim($this->input->post('JVDetailAutoID') ?? ''));
            $this->db->update('srp_erp_jvdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GL Description : ' . $data['GLDescription'] . ' Update Failed ');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'GL Description : ' . $data['GLDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('JVDetailAutoID'));
            }*/
        } else {

            //insert
        }
    }

    function set_salary_provision_details($jvAutoID){
        
    
        $companyID = current_companyID();
        //get jv master details
        $this->db->where('JVMasterAutoId', $jvAutoID);
        $master_record = $this->db->from('srp_erp_jvmaster')->get()->row_array();

        $jv_date = new DateTime($master_record['JVdate']);

        // add approve 1
        $this->db->where('JVType','SalaryProvision');
        $this->db->where('JVMasterAutoId !=',$jvAutoID);
        $this->db->order_by('JVMasterAutoId','DESC');
        $last_ran_record = $this->db->from('srp_erp_jvmaster')->get()->row_array();

        $this->db->select('EIdNo,ECode,DateAssumed,EDOJ');
        $this->db->where('isPayrollEmployee',1);
        $this->db->where('isSystemAdmin !=',1);
        $this->db->where('Erp_companyID',$companyID);
        $emp_records = $this->db->from('srp_employeesdetails')->get()->result_array();

        //get proviosn amounts earliyer
        if($last_ran_record){

            $lastJVMasterAutoId = $last_ran_record['JVMasterAutoId'];
            
            //without reversal entries
            $lastJVMasterDetails = get_jv_master_record_details($lastJVMasterAutoId);

            // reverse last records
            foreach($lastJVMasterDetails as  $key => $details_reverse){

                $base_arr = $details_reverse;
                unset($details_reverse['JVDetailAutoID']);
                unset($details_reverse['JVMasterAutoId']);

                $details_reverse['debitAmount'] = $base_arr['creditAmount'];
                $details_reverse['debitCompanyLocalAmount'] = $base_arr['creditCompanyLocalAmount'];
                $details_reverse['debitCompanyReportingAmount'] = $base_arr['creditCompanyReportingAmount'];
 
                $details_reverse['creditAmount'] = $base_arr['debitAmount'];
                $details_reverse['creditCompanyLocalAmount'] = $base_arr['debitCompanyLocalAmount'];
                $details_reverse['creditCompanyReportingAmount'] = $base_arr['debitCompanyReportingAmount'];

                $details_reverse['isReversal'] = 1;

                $details_reverse['JVMasterAutoId'] = $jvAutoID;

                $res = $this->db->insert('srp_erp_jvdetail', $details_reverse);

            }


        }
        
        $total_amount = 0;

        // echo '<pre>';
    

        foreach($emp_records as $emp_details){

            $emp_id = $emp_details['EIdNo'];
            $date_assumed = $emp_details['DateAssumed'];
            $doj = $emp_details['EDOJ'];

            $date_assumed = new DateTime($date_assumed);
            $date_doj = new DateTime($doj);

            $interval = $jv_date->diff($date_assumed);
            // $emp_date_dif_days = $interval->format('%days');
            $emp_date_dif_days = $interval->days;

            $emp_date_dif = ((int)$emp_date_dif_days / 30);

            //callculate doj
            $interval_year_doj =  $jv_date->diff($date_doj);
            $worked_years = $interval_year_doj->y;

            if($worked_years < 1){
                $worked_years = 1;
            }


            //month calcualtion
            $emp_salary_details = $this->db->query("
                SELECT
                    employeeNo,
                    salarydeclartion.salaryCategoryID,
                    SUM(transactionAmount) AS transactionAmount,
                    SUM(amount) AS amount,
                    provision_de.salaryProvisionMonths,
                    provision_de.eligibleAfterMonths,
                    provision_de.GlAutoID,
                    provision_de.expenseGlAutoID,
                    IFNULL(openning.openning_balance,0) as openning_balance
                FROM
                    `srp_erp_pay_salarydeclartion` AS salarydeclartion
                INNER JOIN (SELECT * FROM srp_erp_leave_salary_provision where companyID = {$companyID} ) AS provision ON salarydeclartion.salaryCategoryID = provision.salarycategoryID 
                LEFT JOIN (SELECT salaryProvisionMonths,eligibleAfterMonths,GlAutoID,expenseGlAutoID FROM srp_erp_leave_salary_provision where isProvision = 1) AS provision_de ON 1 = 1
                LEFT JOIN (SELECT empID, openning_balance FROM srp_erp_doc_open_balance where companyID = {$companyID}) AS openning ON salarydeclartion.employeeNo = openning.empID
                WHERE
                    salarydeclartion.employeeNo = {$emp_id}
                    AND salarydeclartion.companyID = {$companyID}
                GROUP BY
                    salarydeclartion.salaryCategoryID
            
            ")->result_array();

            if(empty($emp_salary_details)){
                continue;
            }

        

            foreach($emp_salary_details as $salary_provision){

                $eligibleAfterMonths = $salary_provision['eligibleAfterMonths'];
                $salaryProvisionMonths = $salary_provision['salaryProvisionMonths'];

                //Payment voucher transactions
                $pv_details = $this->db->query("SELECT SUM(amount) as amount
                    FROM `srp_erp_jv_provision_detail`
                    WHERE empID = {$salary_provision['employeeNo']} and companyID = {$companyID} and provisionDocType = 'PV' and isReversal != 1
                    GROUP BY empID")->row_array();

                if($emp_date_dif > $eligibleAfterMonths){

                    $data = array();

                    $reduce = isset($pv_details['amount']) ? $pv_details['amount'] : 0;
                    $opening_balance = $salary_provision['openning_balance'];

                    $amount = (($salary_provision['transactionAmount'] * $salaryProvisionMonths) - $opening_balance ) + $reduce;
                    $total_amount += ($amount* $worked_years);

                    $data['JVmasterID'] = $jvAutoID;
                    $data['grExpenseGL'] = $salary_provision['expenseGlAutoID'];
                    $data['empID'] = $salary_provision['employeeNo'];
                    $data['amount'] = $amount * $worked_years;
                    $data['companyID'] = $companyID;
                    $data['provisionDocType'] = 'JV';

                    $this->db->insert('srp_erp_jv_provision_detail',$data);
                }
                
            }

        }

        // hit provision amount
        $this->db->select('*');
        $this->db->from('srp_erp_leave_salary_provision as jv');
        $this->db->where('jv.isProvision',1);
        $this->db->where('jv.companyID',$companyID);
        $provision_record = $this->db->get()->row_array();

        if($provision_record){

            $data_pr = array();

            $data_pr['JVmasterID'] = $jvAutoID;
            $data_pr['grProvisionGL'] = $provision_record['GlAutoID'];
            $data_pr['empID'] = null;
            $data_pr['provisionDocType'] = 'JV';
            $data_pr['amount'] = $total_amount;
            $data_pr['companyID'] = $companyID;

            $this->db->insert('srp_erp_jv_provision_detail',$data_pr);

        }

        //update ise reversal
        $data_reverse = array();
        $data_reverse['isReversal'] = 1;

        $this->db->where('JVMasterID !=',$jvAutoID)->where('gratuityID IS NULL',null)->where('companyID',$companyID)->update('srp_erp_jv_provision_detail',$data_reverse);
        $this->db->where('provisionDocType','PV')->where('companyID',$companyID)->update('srp_erp_jv_provision_detail',$data_reverse);

        $pulled_data_expense = $this->pull_expense_provision_gls_amount($jvAutoID,'expense');
        $pulled_data_provision = $this->pull_expense_provision_gls_amount($jvAutoID,'provision');

        // print_r($pulled_data_expense); exit;

        foreach($pulled_data_expense as $expense_records){
            
            $chartofaccount = $expense_records['grExpenseGL'];

            $gldata = fetch_gl_account_desc($chartofaccount);
            
            $data = array();

            if(empty($gldata)){
                continue;
            }
            

            $data['JVMasterAutoId'] = $jvAutoID;
            $data['type'] = 'GL';
            $data['gl_type'] = 'Dr';
            $data['GLAutoID'] = $chartofaccount;
            $data['systemGLCode'] = $gldata['systemAccountCode'];
            $data['GLCode'] = $gldata['GLSecondaryCode'];
            $data['GLDescription'] = $gldata['GLDescription'];
            $data['GLType'] = $gldata['subCategory'];
            $data['description'] = $gldata['GLDescription'];

            $data['debitAmount'] = $expense_records['amount'];
            $data['debitCompanyLocalAmount'] = $expense_records['amount'];
            $data['debitCompanyReportingAmount'] = $expense_records['amount'];

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];

            $res = $this->db->insert('srp_erp_jvdetail', $data);

        }

        foreach($pulled_data_provision as $provision_records){
            
            $chartofaccount = $provision_records['grProvisionGL'];

            $gldata = fetch_gl_account_desc($chartofaccount);
            
            $data = array();

            if(empty($gldata)){
                continue;
            }

            $data['JVMasterAutoId'] = $jvAutoID;
            $data['type'] = 'GL';
            $data['gl_type'] = 'Cr';
            $data['GLAutoID'] = $chartofaccount;
            $data['systemGLCode'] = $gldata['systemAccountCode'];
            $data['GLCode'] = $gldata['GLSecondaryCode'];
            $data['GLDescription'] = $gldata['GLDescription'];
            $data['GLType'] = $gldata['subCategory'];
            $data['description'] = $gldata['GLDescription'];

            $data['creditAmount'] = $provision_records['amount'];
            $data['creditCompanyLocalAmount'] = $provision_records['amount'];
            $data['creditCompanyReportingAmount'] = $provision_records['amount'];

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];

            $res = $this->db->insert('srp_erp_jvdetail', $data);

        }

        return true;

    }


    function update_gl_detail()
    {
        $projectExist = project_is_exist();
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('JVMasterAutoId', $this->input->post('JVMasterAutoId'));
        $master = $this->db->get('srp_erp_jvmaster')->row_array();
        $segment = explode('|', trim($this->input->post('edit_segment_gl') ?? ''));
        $gl = $this->input->post('gl_code_des');
        $creditAmount = $this->input->post('editcreditAmount');
        $projectID = $this->input->post('projectID');
        $debitAmount = $this->input->post('editdebitAmount');

        $company_ID = trim($this->input->post('edit_companyID') ?? '');          /**SMSD */
        $companyType = $this->session->userdata("companyType");            /**SMSD */

        if($companyType == 2){/**SMSD */
                $gldata = fetch_gl_account_desc(trim($this->input->post('edit_gl_code') ?? ''),$company_ID);/**SMSD */
        }else{/**SMSD */
            $gldata = fetch_gl_account_desc(trim($this->input->post('edit_gl_code') ?? ''));/**SMSD */
        } /**SMSD */

        // $gldata = fetch_gl_account_desc($this->input->post('edit_gl_code'));
        if ($gldata['masterCategory'] == 'PL') {
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');

        }

        $gl_code = explode('|', trim($gl));
        $data['JVMasterAutoId'] = trim($this->input->post('JVMasterAutoId') ?? '');
        $data['GLAutoID'] = trim($this->input->post('edit_gl_code') ?? '');
        $data['systemGLCode'] = trim($gl_code[0] ?? '');
        $data['GLCode'] = trim($gl_code[1] ?? '');
        $data['GLDescription'] = trim($gl_code[2] ?? '');
        $data['GLType'] = trim($gl_code[3] ?? '');

        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');

        }
        if ($creditAmount > 0) {
            $data['gl_type'] = 'Cr';
        } else {
            $data['gl_type'] = 'Dr';
        }

        if ($data['gl_type'] == 'Cr') {
            $data['creditAmount'] = round(trim($this->input->post('editcreditAmount') ?? ''), $master['transactionCurrencyDecimalPlaces']);
            $creditCompanyLocalAmount = $data['creditAmount'] / $master['companyLocalExchangeRate'];
            $data['creditCompanyLocalAmount'] = round($creditCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $creditCompanyReportingAmount = $data['creditAmount'] / $master['companyReportingExchangeRate'];
            $data['creditCompanyReportingAmount'] = round($creditCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);

            //updating the value as 0
            $data['debitAmount'] = 0;
            $data['debitCompanyLocalAmount'] = 0;
            $data['debitCompanyReportingAmount'] = 0;

            if($gldata['isBank']==1){
                $data['isBank'] = 1;
                $data['bankCurrencyID'] = $gldata['bankCurrencyID'];
                $data['bankCurrency'] = $gldata['bankCurrencyCode'];
                $bank_currency = currency_conversionID($master['transactionCurrencyID'], $gldata['bankCurrencyID']);
                $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                $data['bankCurrencyAmount'] = $data['creditAmount'] / $bank_currency['conversion'];
            }

        } else {
            $data['debitAmount'] = round(trim($this->input->post('editdebitAmount') ?? ''), $master['transactionCurrencyDecimalPlaces']);
            $debitCompanyLocalAmount = $data['debitAmount'] / $master['companyLocalExchangeRate'];
            $data['debitCompanyLocalAmount'] = round($debitCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $debitCompanyReportingAmount = $data['debitAmount'] / $master['companyReportingExchangeRate'];
            $data['debitCompanyReportingAmount'] = round($debitCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);

            //updating the value as 0
            $data['creditAmount'] = 0;
            $data['creditCompanyLocalAmount'] = 0;
            $data['creditCompanyReportingAmount'] = 0;

            if($gldata['isBank']==1){
                $data['isBank'] = 1;
                $data['bankCurrencyID'] = $gldata['bankCurrencyID'];
                $data['bankCurrency'] = $gldata['bankCurrencyCode'];
                $bank_currency = currency_conversionID($master['transactionCurrencyID'], $gldata['bankCurrencyID']);
                $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                $data['bankCurrencyAmount'] = $data['debitAmount'] / $bank_currency['conversion'];
            }
        }
        $data['description'] = trim($this->input->post('editdescription') ?? '');
        $data['type'] = 'GL';

        if($companyType == 2){       /**SMSD */                                                                  
            $data['companyID'] = $company_ID; /* $this->common_data['company_data']['company_id'];*/   
        }  /**SMSD */                                                                                    
        else{/**SMSD */
            $data['companyID'] = $this->common_data['company_data']['company_id'];/**SMSD */
        }/**SMSD */

        if (trim($this->input->post('JVDetailAutoID') ?? '')) {
            $this->db->where('JVDetailAutoID', trim($this->input->post('JVDetailAutoID') ?? ''));
            $this->db->update('srp_erp_jvdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GL Description : ' . $data['GLDescription'] . ' Update Failed ');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'GL Description : ' . $data['GLDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('JVDetailAutoID'));
            }
        } else {
        }
    }

    function getJournalEntry($JVMasterAutoId)
    {
        $this->db->select('*');
        $this->db->where('JVMasterAutoId', $JVMasterAutoId);
        $this->db->from('srp_erp_jvmaster');
        return $this->db->get()->row_array();
    }

    function getJournalEntryDetail($JVMasterDetailId)
    {
        $this->db->select('*');
        $this->db->where('JVDetailAutoID', $JVMasterDetailId);
        $this->db->from('srp_erp_jvdetail');
        return $this->db->get()->row_array();
    }

    function fetch_Journal_entry_template_data($JVMasterAutoId)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,DATE_FORMAT(JVdate,\'' . $convertFormat . '\') AS JVdate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('JVMasterAutoId', $JVMasterAutoId);
        $this->db->from('srp_erp_jvmaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        $this->db->select('*');
        $this->db->where('JVMasterAutoId', $JVMasterAutoId);
        $this->db->from('srp_erp_jvdetail');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_journal_entry_detail()
    {
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('JVMasterAutoId', $this->input->post('JVMasterAutoId'));
        $this->db->from('srp_erp_jvmaster');
        $data['currency'] = $this->db->get()->row_array();
        $this->db->select('srp_erp_jvdetail.rjvSystemCode, srp_erp_jvdetail.systemGLCode,srp_erp_jvdetail.GLCode,srp_erp_jvdetail.GLDescription,srp_erp_jvdetail.description,srp_erp_jvdetail.segmentCode,srp_erp_jvdetail.debitAmount,srp_erp_jvdetail.creditAmount,srp_erp_jvdetail.JVDetailAutoID,srp_erp_company.company_code,srp_erp_company.company_name,srp_erp_jvdetail.JVMasterAutoId,srp_erp_jvdetail.activityCodeID');/**SMSD */
        $this->db->where('srp_erp_jvdetail.JVMasterAutoId', $this->input->post('JVMasterAutoId'));
        $this->db->join('srp_erp_company','srp_erp_company.company_id = srp_erp_jvdetail.companyID');/**SMSD */
        $this->db->from('srp_erp_jvdetail');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function load_journal_entry_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(JVdate,\'' . $convertFormat . '\') AS JVdate');
        $this->db->where('JVMasterAutoId', $this->input->post('JVMasterAutoId'));
        $this->db->from('srp_erp_jvmaster');
        return $this->db->get()->row_array();
    }

    function delete_Journal_entry_detail()
    {
        $this->db->where('JVDetailAutoID', $this->input->post('JVDetailAutoID'));
        $this->db->delete('srp_erp_jvdetail');
        $this->session->set_flashdata('s', 'Journal entry : deleted Successfully.');
        return true;
    }

    function delete_Journal_entry()
    {
        /*$this->db->where('JVMasterAutoId', $this->input->post('JVMasterAutoId'));
        $this->db->delete('srp_erp_jvmaster');

        $this->db->where('JVMasterAutoId', $this->input->post('JVMasterAutoId'));
        $this->db->delete('srp_erp_jvdetail');
        $this->session->set_flashdata('s', 'Journal entry : deleted Successfully.');
        return true;*/
        $this->db->select('*');
        $this->db->from('srp_erp_jvdetail');
        $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
        $datas = $this->db->get()->row_array();

        $this->db->select('JVcode');
        $this->db->from('srp_erp_jvmaster');
        $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
        $master = $this->db->get()->row_array();

        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            if($master['JVcode']=="0"){
                $this->db->where('JVMasterAutoId', $this->input->post('JVMasterAutoId'));
                $results = $this->db->delete('srp_erp_jvmaster');
                if ($results) {
                    $this->db->where('JVMasterAutoId', $this->input->post('JVMasterAutoId'));
                    $this->db->delete('srp_erp_jvdetail');
                    $this->session->set_flashdata('s', 'Deleted Successfully');
                    return true;
                }
            }else{
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
                $this->db->update('srp_erp_jvmaster', $data);
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }

        }
    }

    function journal_entry_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $this->db->select('documentID, JVcode,DATE_FORMAT(JVdate, "%Y") as invYear,DATE_FORMAT(JVdate, "%m") as invMonth,companyFinanceYearID');
        $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
        $this->db->from('srp_erp_jvmaster');
        $master_dt = $this->db->get()->row_array();

        $companyID = current_companyID();
        $currentuser  = current_userID();
        $locationemp = $this->common_data['emplanglocationid'];

        $this->db->select('*');
        $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
        $detl = $this->db->get('srp_erp_jvdetail')->result_array();
        if(empty($detl)){
            $this->session->set_flashdata('w', 'JV Detail can not be empty');
            return false;
        }

        $this->load->library('sequence');
        if($master_dt['JVcode'] == "0"){
            if($locationwisecodegenerate == 1) {
                $this->db->select('locationID');
                $this->db->where('EIdNo', $currentuser);
                $this->db->where('Erp_companyID', $companyID);
                $this->db->from('srp_employeesdetails');
                $location = $this->db->get()->row_array();
                if ((empty($location)) || ($location =='')) {
                    $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                    return false;
                }else {
                    if($locationemp!='') {
                        $jvcd = $this->sequence->sequence_generator_location($master_dt['documentID'],$master_dt['companyFinanceYearID'],$locationemp,$master_dt['invYear'],$master_dt['invMonth']);
                    } else {
                        $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                        return false;
                    }
                }
            }else {
                $jvcd = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
            }
            $validate_code = validate_code_duplication($jvcd, 'JVcode', trim($this->input->post('JVMasterAutoId') ?? ''),'JVMasterAutoId', 'srp_erp_jvmaster');
            if(!empty($validate_code)) {
                $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                return false;
            }
            $jvcd = array(
                'JVcode' => $jvcd
            );
            $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
            $this->db->update('srp_erp_jvmaster', $jvcd);
        } else {
            $validate_code = validate_code_duplication($master_dt['JVcode'], 'JVcode', trim($this->input->post('JVMasterAutoId') ?? ''),'JVMasterAutoId', 'srp_erp_jvmaster');
            if(!empty($validate_code)) {
                $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                return false;
            }
        }

        $this->load->library('Approvals');
        $this->db->select('documentID,JVMasterAutoId, JVcode,DATE_FORMAT(JVdate, "%Y") as invYear,DATE_FORMAT(JVdate, "%m") as invMonth,companyFinanceYearID,JVdate');
        $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
        $this->db->from('srp_erp_jvmaster');
        $app_data = $this->db->get()->row_array();

        $this->db->select_sum('debitAmount');
        $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
        $amount = $this->db->get('srp_erp_jvdetail')->row_array();

        $autoApproval= get_document_auto_approval('JV');
        if($autoApproval==0){
            $approvals_status = $this->approvals->auto_approve($app_data['JVMasterAutoId'], 'srp_erp_jvmaster','JVMasterAutoId', 'JV',$app_data['JVcode'],$app_data['JVdate']);
        }elseif($autoApproval==1){
            $approvals_status = $this->approvals->CreateApproval('JV', $app_data['JVMasterAutoId'], $app_data['JVcode'], 'Journal Entry', 'srp_erp_jvmaster', 'JVMasterAutoId',0,$app_data['JVdate']);
        }else{
            $this->session->set_flashdata('e', 'Approval levels are not set for this document');
            return false;
        }

        $this->load->library('costAllocation');
        foreach($detl as $value)
        {
            if (null !== $value['activityCodeID'])
            {
                $costAllocation = [
                    'documentId'     => $master_dt['documentID'],
                    'masterId'       => $value['JVMasterAutoId'],
                    'detailId'       => $value['JVDetailAutoID'],
                    'amount'         => false === empty($value['debitAmount']) ? $value['debitAmount'] : $value['creditAmount'],
                    'activityCodeID' => $value['activityCodeID'],
                ];
                $output = $this->costallocation->saveDocumentCostAllocation($costAllocation);
                if(false === $output)
                {
                    return false;
                }
            }
        }

        if ($approvals_status==1) {
            $autoApproval= get_document_auto_approval('JV');
            if($autoApproval==0) {
                $result = $this->save_jv_approval(0, $app_data['JVMasterAutoId'], 1, 'Auto Approved');
                if($result){
                    $this->session->set_flashdata('s', 'Approvals Created Successfully.');
                    return true;
                }
            }else{
                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user'],
                    'transactionAmount' => $amount['debitAmount']
                );

                $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
                $this->db->update('srp_erp_jvmaster', $data);
                $this->session->set_flashdata('s', 'Approvals Created Successfully.');
                return true;
            }
        }else if($approvals_status==3){
            /*$this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
            return true;*/
        } else {
            $this->session->set_flashdata('e', 'Document confirmation failed.');
            return false;
        }
    }

    function save_jv_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();

        $this->load->library('Approvals');
        if($autoappLevel==1) {
            $system_code = trim($this->input->post('JVMasterAutoId') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        }else{
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['JVMasterAutoId']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }
        $companyID = current_companyID();
        
        $this->db->select('JVdate, JVType');
        $this->db->from('srp_erp_jvmaster');
        $this->db->where('JVMasterAutoId', $system_code);
        $query = $this->db->get();
        $result = $query->row_array(); 
        
        $jv = $result['JVdate'];  
        
        if ($result['JVType'] == 'Accrual JV') {  

            $date = new DateTime($jv);
            $date->modify('first day of next month');
            $JVdate = $date->format('Y-m-d');

            $financePeriodDetails=get_financial_period_date_wise($JVdate);
           
            if(empty($financePeriodDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{
                // $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
                // $PeriodBegin = $financePeriodDetails['dateFrom'];
                // $PeriodEnd = $financePeriodDetails['dateTo'];
                // var_dump('hi');

                $JVDetails = $this->db->query('SELECT
                srp_erp_jvdetail.*,srp_erp_chartofaccounts.bankCurrencyID,srp_erp_chartofaccounts.bankCurrencyCode,srp_erp_chartofaccounts.bankCurrencyDecimalPlaces,srp_erp_chartofaccounts.isBank,srp_erp_chartofaccounts.bankName
                FROM
                    srp_erp_jvdetail
                 LEFT JOIN srp_erp_chartofaccounts ON srp_erp_jvdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID
                 WHERE
                    JVMasterAutoId = '.$system_code.'
                    AND srp_erp_jvdetail.companyID= '.$companyID.'  ')->result_array();
                if($autoappLevel==0){
                    $approvals_status=1;
                }else{
                    $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'JV');
                }
        
                if ($approvals_status == 1) {
                    $this->load->model('Double_entry_model');
                    $double_entry = $this->Double_entry_model->fetch_double_entry_journal_entry_data($system_code, 'JV');
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['JVMasterAutoId'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['JVcode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['JVdate'];
                        $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['JVType'];
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['JVdate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['JVdate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['gl_detail'][$i]['description'];
                        $generalledger_arr[$i]['chequeNumber'] = null;
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                        $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                        $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                        $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                        $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                        $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                        $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['partyContractID'] = '';
                        $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                        $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                        $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                        $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                        $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                        $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                        $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                        $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                        $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                        if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                            $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                        }
                        $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = $double_entry['gl_detail'][$i]['projectID'];
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                        $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                        $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                        $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                        $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                        $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                        $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                        $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                    }
        
                    if (!empty($generalledger_arr)) {
                        $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                    }
        
        
                    foreach($JVDetails as $val){
                        if($val['isBank']==1){
                            if($val['gl_type']=='Cr'){
                                $transactionType=2;
                                $transactionAmount=$val['creditAmount'];
                            }else{
                                $transactionType=1;
                                $transactionAmount=$val['debitAmount'];
                            }
                            $bankledger['documentDate']=$double_entry['master_data']['JVdate'];
                            $bankledger['transactionType']=$transactionType;
                            $bankledger['transactionCurrencyID']=$double_entry['master_data']['transactionCurrencyID'];
                            $bankledger['transactionCurrency']=$double_entry['master_data']['transactionCurrency'];
                            $bankledger['transactionExchangeRate']=$double_entry['master_data']['transactionExchangeRate'];
                            $bankledger['transactionAmount']=$transactionAmount;
                            $bankledger['transactionCurrencyDecimalPlaces']=$double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                            $bankledger['bankCurrencyID']=$val['bankCurrencyID'];
                            $bankledger['bankCurrency']=$val['bankCurrencyCode'];
                            $bankledger['bankCurrencyExchangeRate']=$val['bankCurrencyExchangeRate'];
                            $bankledger['bankCurrencyAmount']=$val['bankCurrencyAmount'];
                            $bankledger['bankCurrencyDecimalPlaces']=$val['bankCurrencyDecimalPlaces'];
                            $bankledger['memo']=$val['description'];
                            $bankledger['bankName']=$val['bankName'];
                            $bankledger['bankGLAutoID']=$val['GLAutoID'];
                            $bankledger['bankSystemAccountCode']=$val['systemGLCode'];
                            $bankledger['bankGLSecondaryCode']=$val['GLCode'];
                            $bankledger['documentMasterAutoID']=$val['JVMasterAutoId'];
                            $bankledger['documentType']='JV';
                            $bankledger['documentSystemCode']=$double_entry['master_data']['JVcode'];
                            $bankledger['createdPCID']=$this->common_data['current_pc'];
                            $bankledger['companyID']=$val['companyID'];
                            $bankledger['companyCode']=$val['companyCode'];
                            $bankledger['segmentID']=$val['segmentID'];
                            $bankledger['segmentCode']=$val['segmentCode'];
                            $bankledger['createdUserID']=current_userID();
                            $bankledger['createdDateTime']=current_date();
                            $bankledger['createdUserName']=current_user();
                            $this->db->insert('srp_erp_bankledger', $bankledger);
        
                        }
                    }
                }
                
                $this->db->select('*');
                $this->db->from('srp_erp_jvmaster');
                $this->db->where('JVMasterAutoId', $system_code);
                $master_data = $this->db->get()->row_array(); 
                
                $this->db->select('*');
                $this->db->from('srp_erp_jvdetail');
                $this->db->where('JVMasterAutoId', $system_code);
                $detail_data = $this->db->get()->result_array();

                unset($master_data['JVMasterAutoId']); 
                unset($master_data['JVdate']); 
                unset($master_data['JVType']); 
                $master_data['JVType'] = 'Reversal';
                $master_data['JVdate'] = $JVdate;
                $this->db->insert('srp_erp_jvmaster', $master_data);
                $new_master_id = $this->db->insert_id(); 

                foreach ($detail_data as &$detail) {
                    unset($detail['JVDetailAutoID']); 
                    $detail['JVMasterAutoId'] = $new_master_id;
                    $this->db->insert('srp_erp_jvdetail', $detail);
                }


                $this->load->library('Approvals');
                $this->db->select('documentID,JVMasterAutoId, JVcode,DATE_FORMAT(JVdate, "%Y") as invYear,DATE_FORMAT(JVdate, "%m") as invMonth,companyFinanceYearID,JVdate');
                $this->db->where('JVMasterAutoId', $new_master_id  );
                $this->db->from('srp_erp_jvmaster');
                $app_data = $this->db->get()->row_array();
        
                $this->db->select_sum('debitAmount');
                $this->db->where('JVMasterAutoId',  $new_master_id );
                $amount = $this->db->get('srp_erp_jvdetail')->row_array();
        
                $approvals_status = $this->approvals->auto_approve($app_data['JVMasterAutoId'], 'srp_erp_jvmaster','JVMasterAutoId', 'JV',$app_data['JVcode'],$app_data['JVdate']);
                $this->db->select('*');
                $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
                $detl = $this->db->get('srp_erp_jvdetail')->result_array();
        
                $this->load->library('costAllocation');
                foreach($detl as $value)
                {
                    if (null !== $value['activityCodeID'])
                    {
                        $costAllocation = [
                            'documentId'     => $master_dt['documentID'],
                            'masterId'       => $value['JVMasterAutoId'],
                            'detailId'       => $value['JVDetailAutoID'],
                            'amount'         => false === empty($value['debitAmount']) ? $value['debitAmount'] : $value['creditAmount'],
                            'activityCodeID' => $value['activityCodeID'],
                        ];
                        $output = $this->costallocation->saveDocumentCostAllocation($costAllocation);
                        if(false === $output)
                        {
                            return false;
                        }
                    }
                }

                if ($approvals_status==1) {
                    $autoApproval= get_document_auto_approval('JV');
                    if($autoApproval==0) {
                        $result = $this->save_jv_approval(0, $app_data['JVMasterAutoId'], 1, 'Auto Approved');
                        if($result){
                            $this->session->set_flashdata('s', 'Approvals Created Successfully.');
                            return true;
                        }
                    }else{
                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user'],
                            'transactionAmount' => $amount['debitAmount']
                        );
        
                        $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
                        $this->db->update('srp_erp_jvmaster', $data);
                        $this->session->set_flashdata('s', 'Approvals Created Successfully.');
                        return true;
                    }
                }
                 else {
                    $this->session->set_flashdata('e', 'Document confirmation failed.');
                    return false;
                }
                
            }
        }
        else{
            $JVDetails = $this->db->query('SELECT
            srp_erp_jvdetail.*,srp_erp_chartofaccounts.bankCurrencyID,srp_erp_chartofaccounts.bankCurrencyCode,srp_erp_chartofaccounts.bankCurrencyDecimalPlaces,srp_erp_chartofaccounts.isBank,srp_erp_chartofaccounts.bankName
         FROM
            srp_erp_jvdetail
         LEFT JOIN srp_erp_chartofaccounts ON srp_erp_jvdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID
         WHERE
            JVMasterAutoId = '.$system_code.'
            AND srp_erp_jvdetail.companyID= '.$companyID.'  ')->result_array();
                if($autoappLevel==0){
                    $approvals_status=1;
                }else{
                    $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'JV');
                }
        
                if ($approvals_status == 1) {
                    $this->load->model('Double_entry_model');
                    $double_entry = $this->Double_entry_model->fetch_double_entry_journal_entry_data($system_code, 'JV');
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['JVMasterAutoId'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['JVcode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['JVdate'];
                        $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['JVType'];
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['JVdate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['JVdate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['gl_detail'][$i]['description'];
                        $generalledger_arr[$i]['chequeNumber'] = null;
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                        $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                        $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                        $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                        $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                        $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                        $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['partyContractID'] = '';
                        $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                        $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                        $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                        $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                        $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                        $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                        $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                        $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                        $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                        $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                        if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                            $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                        }
                        $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = $double_entry['gl_detail'][$i]['projectID'];
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                        $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                        $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                        $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                        $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                        $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                        $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                        $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                    }
        
                    if (!empty($generalledger_arr)) {
                        $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                    }
        
        
                    foreach($JVDetails as $val){
                        if($val['isBank']==1){
                            if($val['gl_type']=='Cr'){
                                $transactionType=2;
                                $transactionAmount=$val['creditAmount'];
                            }else{
                                $transactionType=1;
                                $transactionAmount=$val['debitAmount'];
                            }
                            $bankledger['documentDate']=$double_entry['master_data']['JVdate'];
                            $bankledger['transactionType']=$transactionType;
                            $bankledger['transactionCurrencyID']=$double_entry['master_data']['transactionCurrencyID'];
                            $bankledger['transactionCurrency']=$double_entry['master_data']['transactionCurrency'];
                            $bankledger['transactionExchangeRate']=$double_entry['master_data']['transactionExchangeRate'];
                            $bankledger['transactionAmount']=$transactionAmount;
                            $bankledger['transactionCurrencyDecimalPlaces']=$double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                            $bankledger['bankCurrencyID']=$val['bankCurrencyID'];
                            $bankledger['bankCurrency']=$val['bankCurrencyCode'];
                            $bankledger['bankCurrencyExchangeRate']=$val['bankCurrencyExchangeRate'];
                            $bankledger['bankCurrencyAmount']=$val['bankCurrencyAmount'];
                            $bankledger['bankCurrencyDecimalPlaces']=$val['bankCurrencyDecimalPlaces'];
                            $bankledger['memo']=$val['description'];
                            $bankledger['bankName']=$val['bankName'];
                            $bankledger['bankGLAutoID']=$val['GLAutoID'];
                            $bankledger['bankSystemAccountCode']=$val['systemGLCode'];
                            $bankledger['bankGLSecondaryCode']=$val['GLCode'];
                            $bankledger['documentMasterAutoID']=$val['JVMasterAutoId'];
                            $bankledger['documentType']='JV';
                            $bankledger['documentSystemCode']=$double_entry['master_data']['JVcode'];
                            $bankledger['createdPCID']=$this->common_data['current_pc'];
                            $bankledger['companyID']=$val['companyID'];
                            $bankledger['companyCode']=$val['companyCode'];
                            $bankledger['segmentID']=$val['segmentID'];
                            $bankledger['segmentCode']=$val['segmentCode'];
                            $bankledger['createdUserID']=current_userID();
                            $bankledger['createdDateTime']=current_date();
                            $bankledger['createdUserName']=current_user();
                            $this->db->insert('srp_erp_bankledger', $bankledger);
        
                        }
                    }
                }
        }
       

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Journal entry Approval Successfully.');
            return true;
        }
    }

    function re_open_journal_entry()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('JVMasterAutoId', trim($this->input->post('JVMasterAutoId') ?? ''));
        $this->db->update('srp_erp_jvmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function get_recurringjv_details()
    {
        $this->db->select('*');
        $this->db->where('RJVMasterAutoId', $this->input->post('RJVMasterAutoId'));
        $this->db->from('srp_erp_recurringjvdetail');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function add_recarring_details()
    {
        $jvMasterID = $this->input->post('jvMasterID');
        $RJVDetailAutoID = $this->input->post('JVDetailAutoID');
        if(!empty($RJVDetailAutoID)){
            if ($jvMasterID) {
                foreach ($RJVDetailAutoID as $val) {
                    $this->db->select('srp_erp_recurringjvdetail.*,srp_erp_recurringjvmaster.RJVcode');
                    $this->db->where('RJVDetailAutoID', $val);
                    $this->db->join('srp_erp_recurringjvmaster', 'srp_erp_recurringjvdetail.RJVMasterAutoId = srp_erp_recurringjvmaster.RJVMasterAutoId');
                    $this->db->from('srp_erp_recurringjvdetail');
                    $result = $this->db->get()->row_array();

                    $data['JVMasterAutoId'] = $jvMasterID;
                    $data['rjvSystemCode'] = $result['RJVcode'];
                    $data['projectID'] = $result['projectID'];
                    $data['projectExchangeRate'] = $result['projectExchangeRate'];
                    $data['recurringjvMasterAutoId'] = $result['RJVMasterAutoId'];
                    $data['recurringjvDetailAutoID'] = $result['RJVDetailAutoID'];
                    $data['type'] = $result['type'];
                    $data['segmentID'] = $result['segmentID'];
                    $data['segmentCode'] = $result['segmentCode'];
                    $data['gl_type'] = $result['gl_type'];
                    $data['GLAutoID'] = $result['GLAutoID'];
                    $data['systemGLCode'] = $result['systemGLCode'];
                    $data['GLCode'] = $result['GLCode'];
                    $data['GLDescription'] = $result['GLDescription'];
                    $data['GLType'] = $result['GLType'];
                    $data['description'] = $result['description'];
                    $data['debitAmount'] = $result['debitAmount'];
                    $data['debitCompanyLocalAmount'] = $result['debitCompanyLocalAmount'];
                    $data['debitCompanyReportingAmount'] = $result['debitCompanyReportingAmount'];
                    $data['creditAmount'] = $result['creditAmount'];
                    $data['creditCompanyLocalAmount'] = $result['creditCompanyLocalAmount'];
                    $data['creditCompanyReportingAmount'] = $result['creditCompanyReportingAmount'];
                    $data['companyID'] = $result['companyID'];
                    $data['companyCode'] = $result['companyCode'];
                    $results = $this->db->insert('srp_erp_jvdetail', $data);
                }
                if ($results) {
                    /*$rjvMasterIds = $this->input->post('rjvMasterIds');
                    $str = "$rjvMasterIds";
                    $masterid = explode(",", $str);
                    foreach ($masterid as $valu) {
                        $this->db->select('*');
                        $this->db->where('documentID', 'RJV');
                        $this->db->where('documentSystemCode', $valu);
                        $this->db->from('srp_erp_documentattachments');
                        $rjvAttachments = $this->db->get()->row_array();
                    }*/
                    return array('s', 'Detail saved successfully');
                } else {
                    return array('e', 'error in saving details');
                }
            }else{
                return array('e', 'JV Master Id Required');
            }
        }else{
            return array('e', 'Select Recurring JV');
        }

    }

    function delete_Journal_entry_recurring_detail()
    {
        $this->db->where('JVMasterAutoId', $this->input->post('JVMasterAutoId'));
        $this->db->where('recurringjvMasterAutoId', $this->input->post('recurringjvMasterAutoId'));
        $this->db->delete('srp_erp_jvdetail');
        $this->session->set_flashdata('s', 'Journal entry : deleted Successfully.');
        return true;
    }
    function fetch_signaturelevel_journal_voucher()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'JV');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();


    }

    function get_jv_provision_employees(){
        
        $jvMasterID = $this->input->post('jvMasterID');

        $this->db->select('provision.*,emp.ECode,emp.Ename1');
        $this->db->from('srp_erp_jv_provision_detail as provision');
        $this->db->join('srp_employeesdetails as emp','provision.empID = emp.EIdNo','left');
        $this->db->where('companyID', current_companyID());
        $this->db->where('JVmasterID', $jvMasterID);
        $this->db->where('provision.empID IS NOT NULL', null);
       
        return $this->db->get()->result_array();

        
    }
}