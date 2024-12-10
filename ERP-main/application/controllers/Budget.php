<?php

class Budget extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('Budget_model');
        $this->load->model('dashboard_model');
        $this->load->helpers('budget_helper');

    }

    function save_budget_header()
    {
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');

        $this->form_validation->set_rules('segment_gl', 'Segement', 'trim|required');
        $this->form_validation->set_rules('description', 'Narration', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $segment_gl = explode('|', $this->input->post('segment_gl'));
            $financeyear=$this->input->post('financeyear');
            $validate = $this->db->query("SELECT * FROM srp_erp_budgetmaster WHERE companyFinanceYearID ={$financeyear} AND segmentID={$segment_gl[0]}  ")->row_array();
            /*if (!empty($validate)) {
                $this->session->set_flashdata('e', 'Budget already created for the selected financial year and segment');
                echo json_encode(false);
                exit;
            }*/

            $doc = get_document_code('BD');

           $period = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
          /*  $financeperiod = $this->input->post('financeyear_period');
            $period=fetchFinancePeriod($financeperiod);*/
            $date_format_policy = date_format_policy();
            $FYBegin = input_format_date($period[0], $date_format_policy);
            $FYEnd = input_format_date($period[1], $date_format_policy);

            $data['FYBegin'] = $FYBegin;
            $data['FYEnd'] = $FYEnd;

            $this->load->library('sequence');
            $date_format_policy = date_format_policy();
            $documentDate = $this->input->post('documentDate');
            $date = input_format_date($documentDate,$date_format_policy);
            $data['documentSystemCode'] = $this->sequence->sequence_generator($doc['prefix']);
            $data['narration'] = $this->input->post('description');
            $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
            $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
            $data['documentDate'] = $date;
            $data['companyID'] = current_companyID();
            $data['companyCode'] = current_companyCode();
            $data['segmentID'] = $segment_gl[0];
            $data['segmentCode'] = $segment_gl[1];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];

            $data['transactionCurrency']                    =$this->common_data['company_data']['company_reporting_currency'];
            $data['transactionCurrencyID']                    =$this->common_data['company_data']['company_reporting_currencyID'];
              $data['transactionExchangeRate']                = 1;
              $data['transactionCurrencyDecimalPlaces']       = fetch_currency_desimal($data['transactionCurrency']);
             $data['companyLocalCurrency']                   = $this->common_data['company_data']['company_default_currency'];
             $data['companyLocalCurrencyID']                   = $this->common_data['company_data']['company_default_currencyID'];
            $default_currency      = currency_conversion($data['transactionCurrency'],$data['companyLocalCurrency']);
            $data['companyLocalExchangeRate']               = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces']      = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrency']               = $this->common_data['company_data']['company_reporting_currency'];
            $data['companyReportingCurrencyID']               = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency    = currency_conversion($data['transactionCurrency'],$data['companyReportingCurrency']);
            $data['companyReportingExchangeRate']           = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces']  = $reporting_currency['DecimalPlaces'];

          /*  $companyFinanceYear = explode(' - ', $this->input->post('companyFinanceYear'));
            $fromMonth = $companyFinanceYear[0];
            $toMonth = $companyFinanceYear[1];*/
            $_POST['companyFinanceYearID'] = $data['companyFinanceYearID'];

            $insert = $this->db->insert('srp_erp_budgetmaster', $data);
            $last_id = $this->db->insert_id();
            $companyID=current_companyID();


            $financialperiod = $this->Budget_model->fetch_finance_year_period_budget();
       /*     $result=$this->db->query("SELECT d.accountCategoryTypeID AS accountCategoryID, d.CategoryTypeDescription AS accountCategoryDesc, dd.GLAutoID AS masterGLAutoID, dd.GLDescription AS masterAccount, m.GLAutoID AS GLAutoID, m.GLDescription AS GLDescription FROM srp_erp_chartofaccounts AS m LEFT JOIN srp_erp_accountcategorytypes AS d ON d.accountCategoryTypeID = m.accountCategoryTypeID LEFT JOIN ( SELECT GLDescription, GLAutoID FROM srp_erp_chartofaccounts WHERE srp_erp_chartofaccounts.masterCategory = 'PL' ) dd ON ( dd.GLAutoID = m.masterAutoID ) WHERE m.masterCategory = 'PL' AND d.accountCategoryTypeID IN (10 , 11, 12) order by accountCategoryID,masterAccount ")->result_array();   */
            $result=$this->db->query("SELECT d.accountCategoryTypeID AS accountCategoryID, d.CategoryTypeDescription AS accountCategoryDesc, dd.GLAutoID AS masterGLAutoID, dd.GLDescription AS masterAccount, m.GLAutoID AS GLAutoID, m.GLDescription AS GLDescription, m.companyID FROM srp_erp_chartofaccounts AS m LEFT JOIN srp_erp_accountcategorytypes AS d ON d.accountCategoryTypeID = m.accountCategoryTypeID LEFT JOIN (SELECT GLDescription, GLAutoID FROM srp_erp_chartofaccounts WHERE srp_erp_chartofaccounts.masterCategory = 'PL') dd ON (dd.GLAutoID = m.masterAutoID) WHERE m.masterCategory = 'PL'  AND m.companyID={$companyID} ORDER BY accountCategoryID , masterAccount")->result_array();
            $detail = array();
            $x = 0;
            if($result){
                foreach($result as $value){
                    if ($financialperiod) {

                        foreach ($financialperiod as $period) {
                            $datefrom = explode('-', $period['dateFrom']);
                            $year = $datefrom[2];
                            $month = $datefrom[1];
                            $detail[$x]['companyID'] = $data['companyID'];
                            $detail[$x]['companyCode'] = $data['companyCode'];
                            $detail[$x]['segmentID'] = $data['segmentID'];
                            $detail[$x]['segmentCode'] = $data['segmentCode'];
                            $detail[$x]['companyFinancePeriodID'] = $period['companyFinanceYearID'];

                            $date_format_policy = date_format_policy();
                            $dtFrm = $period['dateFrom'];
                            $dateFrom = input_format_date($dtFrm,$date_format_policy);
                            $dtto = $period['dateTo'];
                            $dateTo = input_format_date($dtto,$date_format_policy);
                            $detail[$x]['FYPeriodDateFrom'] = $dateFrom;
                            $detail[$x]['FYPeriodDateTo'] = $dateTo;
                            $detail[$x]['budgetAutoID'] = $last_id;
                            $detail[$x]['accountCategoryID'] = $value['accountCategoryID'];
                            $detail[$x]['accountCategoryDesc'] = $value['accountCategoryDesc'];
                            $detail[$x]['masterGLAutoID'] = $value['masterGLAutoID'];
                            $detail[$x]['masterAccount'] = $value['masterAccount'];
                            $detail[$x]['GLAutoID'] = $value['GLAutoID'];
                            $detail[$x]['GLDescription'] = $value['GLDescription'];
                            $detail[$x]['budgetMonth'] = $month;
                            $detail[$x]['budgetYear'] = $year;

                            $detail[$x]['transactionCurrency']                    = trim($this->input->post('transactionCurrency') ?? '');
                            $detail[$x]['transactionCurrencyID']                    = $this->common_data['company_data']['company_reporting_currencyID'];
                            $detail[$x]['transactionExchangeRate']                = 1;
                            $detail[$x]['transactionCurrencyDecimalPlaces']       = fetch_currency_desimal($detail[$x]['transactionCurrency']);
                            $detail[$x]['companyLocalCurrency']                   = $this->common_data['company_data']['company_default_currency'];
                            $detail[$x]['companyLocalCurrencyID']                   = $this->common_data['company_data']['company_default_currencyID'];
                            $default_currency      = currency_conversion($detail[$x]['transactionCurrency'],$detail[$x]['companyLocalCurrency']);
                            $detail[$x]['companyLocalExchangeRate']               = $default_currency['conversion'];
                            $detail[$x]['companyLocalCurrencyDecimalPlaces']      = $default_currency['DecimalPlaces'];
                            $detail[$x]['companyReportingCurrency']               = $this->common_data['company_data']['company_reporting_currency'];
                            $detail[$x]['companyReportingCurrencyID']               = $this->common_data['company_data']['company_reporting_currencyID'];
                            $reporting_currency    = currency_conversion($detail[$x]['transactionCurrency'],$detail[$x]['companyReportingCurrency']);
                            $detail[$x]['companyReportingExchangeRate']           = $reporting_currency['conversion'];
                            $detail[$x]['companyReportingCurrencyDecimalPlaces']  = $reporting_currency['DecimalPlaces'];
                            $detail[$x]['createdUserGroup'] = $data['createdUserGroup'];
                            $detail[$x]['createdPCID'] = $data['createdPCID'];
                            $detail[$x]['createdUserID'] = $data['createdUserID'];
                            $detail[$x]['createdDateTime'] = $data['createdDateTime'];
                            $detail[$x]['createdUserName'] = $data['createdUserName'];


                            $x++;

                        }
                    }
                }
            }

            $detail_insert = $this->db->insert_batch('srp_erp_budgetdetail', $detail);
            if($detail_insert) {
                $this->session->set_flashdata('s', 'Records Inserted Successfully');
                echo json_encode(TRUE);
            }else{
                $this->session->set_flashdata('e', 'Failed. Please Contact IT Team');
                echo json_encode(FALES);
            }

        }
    }

    function fetch_budget_entry(){

        $this->datatables->select("budgetAutoID,documentSystemCode as documentSystemCode,narration as narration,documentDate as documentDate ,companyFinanceYearID ,companyFinanceYear as companyFinanceYear, FYBegin, FYEnd, transactionCurrency as transactionCurrency, confirmedYN,srp_erp_segment.description as description,srp_erp_budgetmaster.confirmedYN,srp_erp_budgetmaster.approvedYN as approvedYN",false);
        $this->datatables->from('srp_erp_budgetmaster');
        $this->datatables->join('srp_erp_segment','srp_erp_budgetmaster.segmentID=srp_erp_segment.segmentID AND srp_erp_budgetmaster.companyID=srp_erp_segment.companyID','left');
        $this->datatables->where('srp_erp_budgetmaster.companyID', current_companyID());
        $this->datatables->where('srp_erp_budgetmaster.budgetType', 1);
        $this->datatables->add_column('edit', ' $1 ', 'load_budget_action(budgetAutoID,confirmedYN,approvedYN)');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"BD",budgetAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"BD",budgetAutoID)');
        echo $this->datatables->generate();
    }

    function get_budget_detail_data(){
        $budgetAutoID=$this->input->post('budgetAutoID');
        $viewtype=$this->input->post('viewtype');
        $master=$this->db->query("select * from srp_erp_budgetmaster  LEFT JOIN
    `srp_erp_segment` ON srp_erp_segment.segmentID = srp_erp_budgetmaster.segmentID
        AND srp_erp_budgetmaster.companyID = srp_erp_segment.companyID where budgetAutoID = {$budgetAutoID} ")->row_array();

      $allmonth =   $this->db->query("SELECT
            CONCAT(DATE_FORMAT(dateFrom, '%b'),'-',DATE_FORMAT(dateFrom, '%Y')) as MonthName,
        TRIM(LEADING '0' FROM DATE_FORMAT(dateFrom, '%m'))  as month,
            DATE_FORMAT(dateFrom, '%Y') as budgetyear
        FROM
            srp_erp_companyfinanceperiod 
        WHERE
            companyID = " . $this->common_data['company_data']['company_id'] . "  
            AND companyFinanceYearID = {$master['companyFinanceYearID']}")->result_array();


      $select = "";
      if(!empty($allmonth)){
          foreach ($allmonth as $m){
              $select .= "SUM( IF ( budgetMonth = {$m['month']}, transactionAmount, 0 ) ) as month_{$m['month']},";
          }
      }

                $detail=$this->db->query("SELECT  $select ca2.GLDescription as subCategory,budgetDetailAutoID, accountCategoryID, accountCategoryDesc,
        masterGLAutoID, srp_erp_budgetdetail.masterAccount,srp_erp_budgetdetail.GLAutoID,srp_erp_budgetdetail.GLDescription, budgetMonth,
        budgetYear,IF (srp_erp_chartofaccounts.subCategory = 'PLE','EXPENSE',IF (srp_erp_chartofaccounts.subCategory = 'PLI','INCOME','ND')) AS mainCategory
        FROM srp_erp_budgetdetail INNER JOIN srp_erp_chartofaccounts ON srp_erp_budgetdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . " LEFT JOIN ( SELECT GLDescription, GLAutoID FROM srp_erp_chartofaccounts WHERE srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . " ) ca2 ON ( ca2.GLAutoID = srp_erp_chartofaccounts.masterAutoID ) WHERE budgetAutoID = {$budgetAutoID} GROUP BY GLAutoID order BY accountCategoryID,masterGLAutoID asc")->result_array();


      /*  $detail=$this->db->query("SELECT ca2.GLDescription as subCategory,budgetDetailAutoID, accountCategoryID, accountCategoryDesc,
 masterGLAutoID, srp_erp_budgetdetail.masterAccount,srp_erp_budgetdetail.GLAutoID,srp_erp_budgetdetail.GLDescription, budgetMonth, 
 budgetYear,IF (srp_erp_chartofaccounts.subCategory = 'PLE','EXPENSE',IF (srp_erp_chartofaccounts.subCategory = 'PLI','INCOME','ND')) AS mainCategory,
 SUM(IF(budgetMonth = 1, transactionAmount, 0)) AS myJan, SUM(IF(budgetMonth = 2, transactionAmount, 0)) AS myFeb, SUM(IF(budgetMonth = 3, transactionAmount, 0)) AS myMar, SUM(IF(budgetMonth = 4, transactionAmount, 0)) AS myApr, SUM(IF(budgetMonth = 5, transactionAmount, 0)) AS myMay, SUM(IF(budgetMonth = 6, transactionAmount, 0)) AS myJun, SUM(IF(budgetMonth = 7, transactionAmount, 0)) AS myJul, SUM(IF(budgetMonth = 8, transactionAmount, 0)) AS myAug, SUM(IF(budgetMonth = 9, transactionAmount, 0)) AS mySep, SUM(IF(budgetMonth = 10, transactionAmount, 0)) AS myOct, SUM(IF(budgetMonth = 11, transactionAmount, 0)) AS myNov, SUM(IF(budgetMonth = 12, transactionAmount, 0)) AS myDec FROM srp_erp_budgetdetail INNER JOIN srp_erp_chartofaccounts ON srp_erp_budgetdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . " LEFT JOIN ( SELECT GLDescription, GLAutoID FROM srp_erp_chartofaccounts WHERE srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . " ) ca2 ON ( ca2.GLAutoID = srp_erp_chartofaccounts.masterAutoID ) WHERE budgetAutoID = {$budgetAutoID} GROUP BY GLAutoID order BY accountCategoryID,masterGLAutoID asc")->result_array();*/

        $data['detail']=$detail;
        $data['months_all']=$allmonth;
        $master=$this->db->query("select * from srp_erp_budgetmaster  LEFT JOIN
    `srp_erp_segment` ON srp_erp_segment.segmentID = srp_erp_budgetmaster.segmentID
        AND srp_erp_budgetmaster.companyID = srp_erp_segment.companyID where budgetAutoID = {$budgetAutoID} ")->row_array();
        $_POST['companyFinanceYearID'] = $master['companyFinanceYearID'];
        //$financialperiodactive = $this->dashboard_model->fetch_finance_year_period();
        $financialperiodactive = $this->Budget_model->fetch_finance_year_period_budget();
        $financial_from_to = get_financial_from_to($_POST['companyFinanceYearID']);
        $financialperiod = get_month_list_from_date($financial_from_to["beginingDate"], $financial_from_to["endingDate"], "Y-m", "1 month");
        $data['financialperiod']=$financialperiod;
        $data['activeFP']=$financialperiodactive;
        $data['master']=$master;

        // $viewtype = 'approval';
        if($viewtype=='view'){
            echo   $html = $this->load->view('system/budget/erp_budget_detail_view_disable', $data, true);
        }else if($viewtype=='approval'){
            echo   $html = $this->load->view('system/budget/erp_budget_detail_view_disable_approval', $data, true);
        } else{
            echo   $html = $this->load->view('system/budget/erp_budget_detail_view', $data, true);
        }

    }

    function update_budget_row(){
        $glAutoID = $this->input->post('glAutoID');
        $budgetyear = $this->input->post('budgetyear');
        $budgetmonth = $this->input->post('budgetmonth');
        $amount = $this->input->post('amount');
        $type = $this->input->post('type');
        $budgetAutoID = $this->input->post('budgetAutoID');
        $master=$this->Budget_model->get_budget_master_header($budgetAutoID);
        $budgetpolicy = getPolicyValues('BFOR', 'All');
        
        if($budgetpolicy == 1){
            $amount = $amount * -1;
        }elseif($budgetpolicy == 2){
            if($type == 'EXPENSE'){
                $amount = $amount * -1;
            }
        }

        $data['transactionAmount']=$amount;
        $data['companyLocalAmount']         = ($amount/$master['companyLocalExchangeRate']);
        $data['companyReportingAmount']     = ($amount/$master['companyReportingExchangeRate']);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['modifiedUserName'] = $this->common_data['current_user'];



        $this->db->update('srp_erp_budgetdetail', $data, array('GLAutoID' => $glAutoID,'budgetYear'=>$budgetyear,'budgetMonth'=>$budgetmonth,'budgetAutoID'=>$budgetAutoID));
        echo json_encode(TRUE);
    }
    function update_apply_all_row(){
        $myArray= $this->input->post('myArray');
        $budgetAutoID=$this->input->post('budgetAutoID');
        $master=$this->Budget_model->get_budget_master_header($budgetAutoID);
        if($myArray){
            foreach ($myArray as $value){
                $data['transactionAmount']=$value['amount'];
                $data['companyLocalAmount']         = ($value['amount']/$master['companyLocalExchangeRate']);
                $data['companyReportingAmount']     = ($value['amount']/$master['companyReportingExchangeRate']);
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['modifiedUserName'] = $this->common_data['current_user'];

                $this->db->update('srp_erp_budgetdetail', $data, array('GLAutoID' => $value['GLAutoID'],'budgetYear'=>$value['budgetYear'],'budgetMonth'=>$value['budgetMonth'],'budgetAutoID'=>$budgetAutoID));
            }
        }

        echo json_encode(TRUE);
    }

    function get_budget_footer_total(){
        $budgetAutoID=$this->input->post('budgetAutoID');

        $data['master'] =$this->db->query("select * from srp_erp_budgetmaster  LEFT JOIN
    `srp_erp_segment` ON srp_erp_segment.segmentID = srp_erp_budgetmaster.segmentID
        AND srp_erp_budgetmaster.companyID = srp_erp_segment.companyID where budgetAutoID = {$budgetAutoID} ")->row_array();

        $data['allmonth']  =  $this->db->query("SELECT
            CONCAT(DATE_FORMAT(dateFrom, '%b'),'-',DATE_FORMAT(dateFrom, '%Y')) as MonthName,
        TRIM(LEADING '0' FROM DATE_FORMAT(dateFrom, '%m'))  as month,
            DATE_FORMAT(dateFrom, '%Y') as budgetyear
        FROM
            srp_erp_companyfinanceperiod 
        WHERE
            companyID = " . $this->common_data['company_data']['company_id'] . "  
            AND companyFinanceYearID = ".$data['master']['companyFinanceYearID']." ")->result_array();


        $select = "";

        $budgetpolicy = getPolicyValues('BFOR', 'All');

        $make_minus = 1;
        if($budgetpolicy == 1){
            $make_minus = $make_minus * -1;
        }elseif($budgetpolicy == 2){
            $make_minus = "IF(srp_erp_chartofaccounts.subCategory = 'PLE',-1,1)";
        }              
                 

        if(!empty( $data['allmonth'])){
            foreach ( $data['allmonth'] as $m){
                $select .= "SUM( IF ( budgetMonth = {$m['month']}, transactionAmount * $make_minus, 0 ) ) as month_{$m['month']},";
            }
        }
     

        $data['detail']=$this->db->query("SELECT
            $select

                IF
                ( srp_erp_chartofaccounts.subCategory = 'PLE', 'EXPENSE', IF ( srp_erp_chartofaccounts.subCategory = 'PLI', 'INCOME', 'ND' ) ) AS mainCategory
                
            FROM
                srp_erp_budgetdetail 
                INNER JOIN srp_erp_chartofaccounts ON srp_erp_budgetdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
            WHERE
            srp_erp_chartofaccounts.masterCategory = 'PL' AND 
                budgetAutoID = {$budgetAutoID} 
                GROUP BY srp_erp_chartofaccounts.subCategory")->result_array();

        echo json_encode($data);
    }

    function budget_confirmation(){
        echo json_encode($this->Budget_model->budget_confirmation());
    }
    function referback_budjet()
    {
        $budgetAutoID = trim($this->input->post('budgetAutoID') ?? '');

        $this->db->select('approvedYN,documentSystemCode');
        $this->db->where('budgetAutoID', trim($budgetAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_budgetmaster');
        $approved_bdt = $this->db->get()->row_array();
        if (!empty($approved_bdt)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_bdt['documentSystemCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($budgetAutoID, 'BD');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }
    }

    function load_missing_gl_tobudget(){
        $budgetAutoID=$this->input->post('budgetAutoID');
        $companyID=current_companyID();
        $budmastr=$this->db->query("SELECT * FROM srp_erp_budgetmaster WHERE budgetAutoID = $budgetAutoID")->row_array();

       // $financialperiod = $this->Budget_model->fetch_finance_year_period_budget();
        $financialperiod = $this->Budget_model->fetch_finance_year_period_budget_load_missing($budmastr['companyFinanceYearID']);

        $result=$this->db->query("SELECT d.accountCategoryTypeID AS accountCategoryID, d.CategoryTypeDescription AS accountCategoryDesc, dd.GLAutoID AS masterGLAutoID, dd.GLDescription AS masterAccount, m.GLAutoID AS GLAutoID, m.GLDescription AS GLDescription, m.companyID FROM srp_erp_chartofaccounts AS m LEFT JOIN srp_erp_accountcategorytypes AS d ON d.accountCategoryTypeID = m.accountCategoryTypeID LEFT JOIN (SELECT GLDescription, GLAutoID FROM srp_erp_chartofaccounts WHERE srp_erp_chartofaccounts.masterCategory = 'PL') dd ON (dd.GLAutoID = m.masterAutoID) WHERE m.masterCategory = 'PL'  AND m.companyID={$companyID} AND m.GLAutoID Not IN(SELECT  srp_erp_budgetdetail.GLAutoID FROM srp_erp_budgetdetail WHERE budgetAutoID = $budgetAutoID GROUP BY GLAutoID) ORDER BY accountCategoryID , masterAccount")->result_array();
        $detail = array();
        $x = 0;
        if($result){
            foreach($result as $value){
                if ($financialperiod) {

                    foreach ($financialperiod as $period) {
                        $datefrom = explode('-', $period['dateFrom']);
                        $year = $datefrom[2];
                        $month = $datefrom[1];
                        $detail[$x]['companyID'] = $budmastr['companyID'];
                        $detail[$x]['companyCode'] = $budmastr['companyCode'];
                        $detail[$x]['segmentID'] = $budmastr['segmentID'];
                        $detail[$x]['segmentCode'] = $budmastr['segmentCode'];
                        $detail[$x]['companyFinancePeriodID'] = $period['companyFinanceYearID'];

                        $date_format_policy = date_format_policy();
                        $dtFrm = $period['dateFrom'];
                        $dateFrom = input_format_date($dtFrm,$date_format_policy);
                        $dtto = $period['dateTo'];
                        $dateTo = input_format_date($dtto,$date_format_policy);
                        $detail[$x]['FYPeriodDateFrom'] = $dateFrom;
                        $detail[$x]['FYPeriodDateTo'] = $dateTo;
                        $detail[$x]['budgetAutoID'] = $budgetAutoID;
                        $detail[$x]['accountCategoryID'] = $value['accountCategoryID'];
                        $detail[$x]['accountCategoryDesc'] = $value['accountCategoryDesc'];
                        $detail[$x]['masterGLAutoID'] = $value['masterGLAutoID'];
                        $detail[$x]['masterAccount'] = $value['masterAccount'];
                        $detail[$x]['GLAutoID'] = $value['GLAutoID'];
                        $detail[$x]['GLDescription'] = $value['GLDescription'];
                        $detail[$x]['budgetMonth'] = $month;
                        $detail[$x]['budgetYear'] = $year;

                        $detail[$x]['transactionCurrency']                    = trim($this->input->post('transactionCurrency') ?? '');
                        $detail[$x]['transactionCurrencyID']                    = $this->common_data['company_data']['company_reporting_currencyID'];
                        $detail[$x]['transactionExchangeRate']                = 1;
                        $detail[$x]['transactionCurrencyDecimalPlaces']       = fetch_currency_desimal($detail[$x]['transactionCurrency']);
                        $detail[$x]['companyLocalCurrency']                   = $this->common_data['company_data']['company_default_currency'];
                        $detail[$x]['companyLocalCurrencyID']                   = $this->common_data['company_data']['company_default_currencyID'];
                        $default_currency      = currency_conversion($detail[$x]['transactionCurrency'],$detail[$x]['companyLocalCurrency']);
                        $detail[$x]['companyLocalExchangeRate']               = $default_currency['conversion'];
                        $detail[$x]['companyLocalCurrencyDecimalPlaces']      = $default_currency['DecimalPlaces'];
                        $detail[$x]['companyReportingCurrency']               = $this->common_data['company_data']['company_reporting_currency'];
                        $detail[$x]['companyReportingCurrencyID']               = $this->common_data['company_data']['company_reporting_currencyID'];
                        $reporting_currency    = currency_conversion($detail[$x]['transactionCurrency'],$detail[$x]['companyReportingCurrency']);
                        $detail[$x]['companyReportingExchangeRate']           = $reporting_currency['conversion'];
                        $detail[$x]['companyReportingCurrencyDecimalPlaces']  = $reporting_currency['DecimalPlaces'];
                        $detail[$x]['createdUserGroup'] = $budmastr['createdUserGroup'];
                        $detail[$x]['createdPCID'] = $budmastr['createdPCID'];
                        $detail[$x]['createdUserID'] = $budmastr['createdUserID'];
                        $detail[$x]['createdDateTime'] = $budmastr['createdDateTime'];
                        $detail[$x]['createdUserName'] = $budmastr['createdUserName'];


                        $x++;

                    }
                }
            }
            $detail_insert = $this->db->insert_batch('srp_erp_budgetdetail', $detail);
            if($detail_insert) {
                echo json_encode(array('s', 'Records Inserted Successfully'));
            }else{
                echo json_encode(array('e', 'Failed. Please Contact IT Team'));
            }
        }else{
            echo json_encode(array('s', 'Records Inserted Successfully'));
        }

    }

    function fetch_budget_approval(){
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $currentuserid = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select("budgetAutoID,srp_erp_budgetmaster.documentSystemCode as documentSystemCode,narration,srp_erp_budgetmaster.documentDate as documentDate ,companyFinanceYearID ,companyFinanceYear as companyFinanceYear, FYBegin, FYEnd, transactionCurrency,srp_erp_budgetmaster.approvedYN as approvedYN,confirmedYN,srp_erp_segment.description as description,srp_erp_budgetmaster.confirmedYN,srp_erp_budgetmaster.createdUserID as createdUserID,srp_erp_documentapproved.approvalLevelID as approvalLevelID");
            $this->datatables->from('srp_erp_budgetmaster');
            $this->datatables->join('srp_erp_segment','srp_erp_budgetmaster.segmentID=srp_erp_segment.segmentID AND srp_erp_budgetmaster.companyID=srp_erp_segment.companyID','left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_budgetmaster.budgetAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_budgetmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_budgetmaster.currentLevelNo');
            $this->datatables->where('srp_erp_budgetmaster.companyID', current_companyID());
            $this->datatables->where('srp_erp_budgetmaster.budgetType', 1);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_documentapproved.documentID', 'BD');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'BD');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "BD", budgetAutoID)');
            $this->datatables->add_column('edit', '$1', 'load_BD_approval_action(budgetAutoID,confirmedYN,approvedYN,createdUserID,approvalLevelID)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select("budgetAutoID,srp_erp_budgetmaster.documentSystemCode as documentSystemCode,narration,srp_erp_budgetmaster.documentDate as documentDate ,companyFinanceYearID ,companyFinanceYear as companyFinanceYear, FYBegin, FYEnd, transactionCurrency,srp_erp_budgetmaster.approvedYN as approvedYN,confirmedYN,srp_erp_segment.description as description,srp_erp_budgetmaster.confirmedYN,srp_erp_budgetmaster.createdUserID as createdUserID,srp_erp_documentapproved.approvalLevelID as approvalLevelID");
            $this->datatables->from('srp_erp_budgetmaster');
            $this->datatables->join('srp_erp_segment','srp_erp_budgetmaster.segmentID=srp_erp_segment.segmentID AND srp_erp_budgetmaster.companyID=srp_erp_segment.companyID','left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_budgetmaster.budgetAutoID');

            $this->datatables->where('srp_erp_budgetmaster.companyID', current_companyID());
            $this->datatables->where('srp_erp_budgetmaster.budgetType', 1);
            $this->datatables->where('srp_erp_documentapproved.documentID', 'BD');
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
            $this->datatables->group_by('srp_erp_budgetmaster.budgetAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');

            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "BD", budgetAutoID)');
            $this->datatables->add_column('edit', '$1', 'load_BD_approval_action(budgetAutoID,confirmedYN,approvedYN,createdUserID,approvalLevelID)');
            echo $this->datatables->generate();
        }

    }

    function save_budget_approval()
    {
        $system_code = trim($this->input->post('budgetAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'BD', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('budgetAutoID');
                $this->db->where('budgetAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_budgetmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Budget_model->save_budget_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('budgetAutoID');
            $this->db->where('budgetAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_budgetmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'BD', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Budget_model->save_budget_approval());
                    }
                }
            }
        }
    }

}
