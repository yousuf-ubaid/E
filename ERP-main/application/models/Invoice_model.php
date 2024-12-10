<?php

class Invoice_model extends ERP_Model
{

    function save_invoice_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $retensionEnabled = getPolicyValues('RETO', 'All');

        $interCompanyID=null;
        $invDueDate = $this->input->post('invoiceDueDate');
        $invoiceDueDate = input_format_date($invDueDate, $date_format_policy);
        $invDate = $this->input->post('invoiceDate');
        $invoiceDate = input_format_date($invDate, $date_format_policy);
        $customerDate = $this->input->post('customerInvoiceDate');
        $customerInvoiceDate = input_format_date($customerDate, $date_format_policy);
        $ackDate = $this->input->post('acknowledgeDate');
        $acknowledgeDate = input_format_date($ackDate, $date_format_policy);
        $sullyD = $this->input->post('supplyDate');
        $supplyDate = input_format_date($sullyD, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $customerID = $this->input->post('customerID');
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $invoicetype = $this->input->post('invoiceType');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $projectID =  $this->input->post('projectID');
        $contractAutoID = $this->input->post('contractMaster');
        $retentionPercentage = $this->input->post('retentionPercentage');
        $retensionGL = $this->input->post('retensionGL');

        $isintercompany = $this->input->post('interCompanyCheck');
        if($isintercompany==true){

            $this->db->select('interCompanyID');
            $this->db->where('customerAutoID', $this->input->post('customerID'));
            $this->db->from('srp_erp_customermaster');
            $interCompanyQuery = $this->db->get();
            $interCompanyID = $interCompanyQuery->row()->interCompanyID;

            $this->db->select('GLAutoID');
            $this->db->where('companyID', $interCompanyID);
            $this->db->where('accountDefaultType', 4);
            $this->db->from('srp_erp_chartofaccounts');
            $query = $this->db->get();
           
            if($query->num_rows() == 0){
                $this->session->set_flashdata('e', ' Inter Company chart of account is not created for this company');
                return;
            }
        }

        if($invoiceAutoID) {
            $projectID_detail = $this->db->query("SELECT projectID FROM srp_erp_customerinvoicemaster where 
                                      invoiceAutoID = $invoiceAutoID")->row_array();
            $detailexist = $this->db->query("SELECT invoiceDetailsAutoID FROM`srp_erp_customerinvoicedetails` where invoiceAutoID = $invoiceAutoID AND type = 'Project'")->result_array();
        }
        if(($invoicetype =='Project')&&(!empty($invoiceAutoID))&&($projectID!=$projectID_detail['projectID']&&$projectID_detail['projectID']!=''))
        {

            if((!empty($detailexist)&&($detailexist!='')))
            {
                $this->session->set_flashdata('e', 'Please delete all the records and change the project');
                return array('status' => false);
                exit;
            }
        }

        $rebate = getPolicyValues('CRP', 'All');
        if($rebate == 1) {
            $rebateDet = $this->db->query("SELECT rebatePercentage, rebateGLAutoID FROM `srp_erp_customermaster` WHERE customerAutoID = {$customerID}")->row_array();
            if(!empty($rebate)) {
                $data['rebateGLAutoID'] = $rebateDet['rebateGLAutoID'];
                $data['rebatePercentage'] = $rebateDet['rebatePercentage'];
            }
        } else {
            $data['rebateGLAutoID'] = null;
            $data['rebatePercentage'] = null;
        }

        if($financeyearperiodYN==1) {
            $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));

            $FYBegin = input_format_date($financeyr[0], $date_format_policy);
            $FYEnd = input_format_date($financeyr[1], $date_format_policy);
        }else{
            $financeYearDetails=get_financial_year($invoiceDate);
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
            $financePeriodDetails=get_financial_period_date_wise($invoiceDate);

            if(empty($financePeriodDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID') ?? ''));
        //$location = explode('|', trim($this->input->post('location_dec') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        if ($this->input->post('RVbankCode')) {
            $bank_detail = fetch_gl_account_desc(trim($this->input->post('RVbankCode') ?? ''));
            $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
            $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
            $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
            $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
            $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
            $data['invoicebank'] = $bank_detail['bankName'];
            $data['invoicebankBranch'] = $bank_detail['bankBranch'];
            $data['invoicebankSwiftCode'] = $bank_detail['bankSwiftCode'];
            $data['invoicebankAccount'] = $bank_detail['bankAccountNumber'];
            $data['invoicebankType'] = $bank_detail['subCategory'];
        }
        $data['interCompanyID'] =$interCompanyID;
        $data['documentID'] = 'CINV';
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        $data['projectID'] = $projectID;
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['invoiceDate'] = trim($invoiceDate);
        $data['customerInvoiceDate'] = trim($customerInvoiceDate);
        $data['invoiceDueDate'] = trim($invoiceDueDate);
        
        if($retensionEnabled == 1){
            $data['retentionPercentage'] = trim($retentionPercentage);
            $data['retensionGL'] =   trim($retensionGL);
            $data['isRetensionYN'] = trim($this->input->post('isRetensionYN') ?? '');
            $data['retensionDocumentType'] = trim($this->input->post('retensionDocumentType') ?? '');
            $data['retensionInvoiceID'] = trim($this->input->post('retensionInvoiceID') ?? '');
        }
       

        $acknowledgementDateYN = getPolicyValues('SAD', 'All');
        if(!empty($acknowledgementDateYN) && $acknowledgementDateYN == 1) {
            $data['acknowledgementDate'] = trim($acknowledgeDate);
        } else {
            $data['acknowledgementDate'] = trim($invoiceDate);
        }

        $isGroupBasedTax = getPolicyValues('GBT', 'All');
        if(!empty($isGroupBasedTax) && $isGroupBasedTax == 1) {
            $data['supplyDate'] = trim($supplyDate);
        } else {
            $data['supplyDate'] = trim($invoiceDate);
        }

        $invoiceNarration = ($this->input->post('invoiceNarration'));
        $data['invoiceNarration'] = str_replace('<br />', PHP_EOL, $invoiceNarration);
        //$data['invoiceNarration'] = trim_desc($this->input->post('invoiceNarration'));

        $crTypes = explode('<table', $this->input->post('invoiceNote'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['invoiceNote'] = $notes;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['salesPersonID'] = trim($this->input->post('salesPersonID') ?? '');
        if ($data['salesPersonID']) {
            $code = explode(' | ', trim($this->input->post('salesPerson') ?? ''));
            $data['SalesPersonCode'] = trim($code[0] ?? '');
        }
        // $data['wareHouseCode'] = trim($location[0] ?? '');
        // $data['wareHouseLocation'] = trim($location[1] ?? '');
        // $data['wareHouseDescription'] = trim($location[2] ?? '');
        $data['invoiceType'] = trim($this->input->post('invoiceType') ?? '');
        if($this->input->post('invoiceType')=='Operation'){
            $data['isOpYN'] =1;
        }
        $data['seNumber'] = trim($this->input->post('se_number') ?? '');
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $data['isPrintDN'] = trim($this->input->post('isPrintDN') ?? '');
        $data['customerID'] = $customer_arr['customerAutoID'];
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
        $data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
        $data['customerTelephone'] = $customer_arr['customerTelephone'];
        $data['customerFax'] = $customer_arr['customerFax'];
        $data['customerEmail'] = $customer_arr['customerEmail'];
        $data['customerReceivableAutoID'] = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription'] = $customer_arr['receivableDescription'];
        $data['customerReceivableType'] = $customer_arr['receivableType'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
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
        $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];


        if($contractAutoID){
            $data['contractAutoID'] = $contractAutoID;

            //update customer details according to the contract selected
            $fss_policy = getPolicyValues('FSSP', 'All');

            if($fss_policy == '1'){

                $contract_details = get_contractmaster_details($contractAutoID);

                if($contract_details){
                    $data['customerAddress'] = $contract_details['customerAddress'];
                    $data['customerTelephone'] = $contract_details['customerTelephone'];
                    $data['customerEmail'] = $contract_details['customerEmail'];
                    $data['customerWebURL'] = $contract_details['customerWebURL'];
                }

            }


        }

   

        if (trim($this->input->post('invoiceAutoID') ?? '')) {
            $masterID = $this->input->post('invoiceAutoID');
            $taxAdded = $this->db->query("SELECT InvoiceAutoID FROM srp_erp_customerinvoicedetails WHERE invoiceAutoID = $masterID 
                                        UNION
                                          SELECT InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE invoiceAutoID = $masterID")->row_array();
            if (empty($taxAdded)) {
                $isGroupBasedTax = getPolicyValues('GBT', 'All');
                if($isGroupBasedTax && $isGroupBasedTax == 1) {
                    $data['isGroupBasedTax'] = 1;
                }
            }

            if($invoicetype =='DeliveryOrder'){
                update_do_item_wise_policy_value( $invoiceAutoID);
            }

              /*Segment Update To Items in detail tbl start*/
              $comapnyid = current_companyID();
              $invID = $this->input->post('invoiceAutoID');
              $master_rec = $this->db->query("SELECT
                                              segmentID,
                                              invoiceAutoID 
                                              FROM 
                                              `srp_erp_customerinvoicemaster`
                                               where
                                               companyID = $comapnyid 
                                               AND invoiceAutoID = $invID")->row_array();

              if(!empty($master_rec)&&($master_rec!='')){
                  if($master_rec['segmentID']!=trim($segment[0] ?? '')){
                     $this->db->query("UPDATE
                                        srp_erp_customerinvoicedetails 
                                       SET 
                                        srp_erp_customerinvoicedetails.segmentID =  $segment[0] ,
                                        srp_erp_customerinvoicedetails.segmentCode = '{$segment[1]}'
                                       where 
                                        type = 'Item'
                                        AND srp_erp_customerinvoicedetails.invoiceAutoID = $invID");
                  }
              }

              /*Segment Update To Items in detail tbl end*/


            if($projectID_detail['projectID']!=$projectID)
            {
                $invoiceExist = $this->db->query("SELECT invoiceAutoID,invoiceCode,IF(referenceNo = '','-',referenceNo) as referenceNo FROM
                                                 `srp_erp_customerinvoicemaster` WHERE approvedYN = 0 AND projectID = $projectID")->row_array();
                if((!empty($invoiceExist))&&($invoicetype == 'Project'))
                {
                    $this->session->set_flashdata('e', 'There is an unapproved invoice exist for selected project');
                    return array('status' => false);
                    exit;
                }
            }

            // echo '<pre>';
            // print_r($data); exit;

            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            $this->db->update('srp_erp_customerinvoicemaster', $data);

            

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Invoice Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                
                if(($invoicetype == 'Project'))
                {
                    $cutomerinvoiceexist = $this->db->query("SELECT srp_erp_customerinvoicedetails.invoiceDetailsAutoID FROM `srp_erp_customerinvoicedetails` 
                        LEFT JOIN srp_erp_customerinvoicemaster invoicemaster on invoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
                        where type = 'Project' AND invoicemaster.InvoiceAutoID = $invoiceAutoID  AND invoicemaster.projectID = $projectID")->row_array();

                    if(($invoicetype == 'Project')&&((empty($cutomerinvoiceexist['invoiceDetailsAutoID']))||($cutomerinvoiceexist['invoiceDetailsAutoID'] == '')))
                    {

                        $this->save_project_detail($invoiceAutoID,$projectID);
                    }
                }

                // update_warehouse_items();
                // update_item_master();
                $this->session->set_flashdata('s', 'Invoice Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('invoiceAutoID'));
            }
        } else {

            $data['isDOItemWisePolicy']=0;
            if($invoicetype =='DeliveryOrder') {
                $DOItemWiseYN = getPolicyValues('DOIW', 'All');
                if($DOItemWiseYN && $DOItemWiseYN == 1) {
                    $data['isDOItemWisePolicy'] = 1;
                }
            }
            $isGroupBasedTax = getPolicyValues('GBT', 'All');
            if($isGroupBasedTax && $isGroupBasedTax == 1) {
                $data['isGroupBasedTax'] = 1;
            }

            //$this->load->library('sequence');
            if($invoicetype == 'Project')
            {
                $invoiceExist = $this->db->query("SELECT invoiceAutoID,invoiceCode,IF(referenceNo = '','-',referenceNo) as referenceNo FROM
                                             `srp_erp_customerinvoicemaster` WHERE  approvedYN = 0 AND projectID = $projectID")->row_array();
                if((!empty($invoiceExist))&&($invoicetype == 'Project'))
                {
                    $this->session->set_flashdata('e', 'There is an unapproved invoice exist for selected project');
                    return array('status' => false);
                    exit;
                }
            }

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['invoiceCode'] = 0;
            //if ($data['isPrintDN']==1) {
            $data['deliveryNoteSystemCode'] = $this->sequence->sequence_generator('DLN');
            //}

            $this->db->insert('srp_erp_customerinvoicemaster', $data);
            $last_id = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Invoice   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                if(($invoicetype == 'Project'))
                {
                    $this->save_project_detail($last_id,$projectID);
                }
                $this->session->set_flashdata('s', 'Invoice Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_recurring(){
        $this->db->trans_start();
        $date_format_policy = date_format_policy();

        $ccemail = $this->input->post('ccemail');
        $email = $this->input->post('email');

        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $isRecurring = 1;
        $frequency_days = $this->input->post('frequency_days');
        $start_date = $this->input->post('start_date');
        $recurring_StartDate = input_format_date($start_date, $date_format_policy);
        $end_date = $this->input->post('end_date');
        $recurring_EndDate = input_format_date($end_date, $date_format_policy);
        $next_invoice_date = $this->input->post('next_invoice_date');
        $recurring_InvoiceDate = input_format_date($next_invoice_date, $date_format_policy);

        

            if(isset($ccemail)){
                $type = 2;
                $data_m['toEmailAddress'] = $ccemail;
                $data_m['type'] = $type;
                $data_m['documentAutoID'] = $invoiceAutoID;
                $data_m['isRecurring'] = $isRecurring;
                $data_m['documentID'] = 'CINV';
                $data_m['companyID'] = $this->common_data['company_data']['company_id'];
                $data_m['sentByEmpID'] = $this->common_data['current_userID'];
                $data_m['sentDateTime'] = $this->common_data['current_date'];
    
                $this->db->select('autoID');
                $this->db->from('srp_erp_documentemailhistory');
                $this->db->where('documentAutoID', $invoiceAutoID);
                $this->db->where('type', $type);
                $isexist = $this->db->get()->row('autoID');
    
                if(isset($isexist)){
                    $this->db->where('autoID', $isexist);
                    $this->db->update('srp_erp_documentemailhistory', $data_m);
                }else{
                    $this->db->insert('srp_erp_documentemailhistory', $data_m);
                } 
            }

            if(!empty($email)){
                $type = 1;
                $data_m['toEmailAddress'] = $email;
                $data_m['type'] = $type;
                $data_m['documentAutoID'] = $invoiceAutoID;
                $data_m['isRecurring'] = $isRecurring;
                $data_m['documentID'] = 'CINV';
                $data_m['companyID'] = $this->common_data['company_data']['company_id'];
                $data_m['sentByEmpID'] = $this->common_data['current_userID'];
                $data_m['sentDateTime'] = $this->common_data['current_date'];

                $this->db->select('autoID');
                $this->db->from('srp_erp_documentemailhistory');
                $this->db->where('documentAutoID', $invoiceAutoID);
                $this->db->where('type', $type);
                $isexist = $this->db->get()->row('autoID');

                if(isset($isexist)){
                    $this->db->where('autoID', $isexist);
                    $this->db->update('srp_erp_documentemailhistory', $data_m);
                }else{
                    $this->db->insert('srp_erp_documentemailhistory', $data_m);
                } 
            }
  

        $data['isRecurring'] = $isRecurring;
        $data['nexInvoiceDate'] = $next_invoice_date;
        $data['frequencyDays'] = $frequency_days;
        $data['policyStartDate'] = $recurring_StartDate;
        $data['policyEndDate'] = $recurring_EndDate;

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->update('srp_erp_customerinvoicemaster', $data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Recurring fields updat failed');
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
           
            $this->session->set_flashdata('s', 'Recurring fields has been updated successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }

    }

    function fetch_customer_data($customerID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customerID);
        return $this->db->get()->row_array();
    }

    function delete_item_direct()
    {
        $id = $this->input->post('invoiceDetailsAutoID');

        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicedetails');
        $this->db->where('invoiceDetailsAutoID', $id);
        $rTmp = $this->db->get()->row_array();

        //print_r($rTmp);exit;

        //update batch table


        /** Update Contract Table*/
        if(!empty($rTmp['contractDetailsAutoID'])) {
            $balance = $this->db->query("SELECT
                            srp_erp_contractdetails.contractAutoID,
                            srp_erp_contractdetails.contractDetailsAutoID,
                            srp_erp_contractdetails.itemAutoID,
                            srp_erp_contractdetails.requestedQty AS requestedQtyTot,
                            ifnull( cinv.requestedQtyINV, 0 ) AS requestedQtyINV,
                            ifnull( deliveryorder.requestedQtyDO, 0 ) AS requestedQtyDO,
                            TRIM(
                                TRAILING '.' 
                            FROM
                                TRIM(
                                    TRAILING 0 
                                FROM
                                    (
                                    ROUND( ifnull( srp_erp_contractdetails.requestedQty, 0 ), 2 ))) - TRIM(
                                    TRAILING 0 
                                FROM
                                    (
                                    ROUND( ifnull( cinv.requestedQtyINV, 0 ) + ifnull( deliveryorder.requestedQtyDO, 0 ), 2 )))) AS balance 
                        FROM
                            srp_erp_contractdetails
                            LEFT JOIN (
                            SELECT
                                contractAutoID,
                                contractDetailsAutoID,
                                itemAutoID,
                                IFNULL( SUM( requestedQty ), 0 ) AS requestedQtyINV 
                            FROM
                                srp_erp_customerinvoicedetails 
                            GROUP BY
                                contractDetailsAutoID 
                            ) cinv ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `cinv`.`contractDetailsAutoID`
                            LEFT JOIN (
                            SELECT
                                contractAutoID,
                                contractDetailsAutoID,
                                itemAutoID,
                                IFNULL( SUM( deliveredQty ), 0 ) AS requestedQtyDO 
                            FROM
                                srp_erp_deliveryorderdetails 
                            GROUP BY
                                contractDetailsAutoID 
                            ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
                        WHERE
                         srp_erp_contractdetails.contractDetailsAutoID = {$rTmp['contractDetailsAutoID']} ")->row_array();

            $balanceQty = $rTmp['requestedQty'] + $balance['balance'];

            if ($balanceQty <= 0) {
                $cont_data['invoicedYN'] = 1;
                $this->db->where('contractDetailsAutoID', $rTmp['contractDetailsAutoID']);
                $this->db->update('srp_erp_contractdetails', $cont_data);
            } else {
                $cont_data['invoicedYN'] = 0;
                $this->db->where('contractDetailsAutoID', $rTmp['contractDetailsAutoID']);
                $this->db->update('srp_erp_contractdetails', $cont_data);
            }
        }
        /** End Of Update Contract Table*/

        /** Update DO Table*/
        if(!empty($rTmp['DODetailsID'])) {
            $balance_do = $this->db->query("SELECT
                            srp_erp_deliveryorderdetails.DOAutoID,
                            srp_erp_deliveryorderdetails.DODetailsAutoID,
                            srp_erp_deliveryorderdetails.itemAutoID,
                            srp_erp_deliveryorderdetails.requestedQty AS requestedQtyTot,
                            ifnull( cinv.requestedQtyINV, 0 ) AS requestedQtyINV,
                            TRIM( TRAILING '.' FROM TRIM(TRAILING 0 FROM ( ROUND( ifnull( srp_erp_deliveryorderdetails.requestedQty, 0 ), 2 ))) - 
                            TRIM(TRAILING 0  FROM ( ROUND( ifnull( cinv.requestedQtyINV, 0 )))))  AS balance 
                        FROM
                        srp_erp_deliveryorderdetails
                            LEFT JOIN (
                            SELECT
                                DOMasterID,
                                DODetailsID,
                                itemAutoID,
                                IFNULL( SUM( requestedQty ), 0 ) AS requestedQtyINV 
                            FROM
                                srp_erp_customerinvoicedetails 
                            GROUP BY
                                DODetailsID 
                            ) cinv ON `srp_erp_deliveryorderdetails`.`DODetailsAutoID` = `cinv`.`DODetailsID`
                            
                        WHERE
                        srp_erp_deliveryorderdetails.DODetailsAutoID = {$rTmp['DODetailsID']} ")->row_array();

            $balanceQty_do = $rTmp['requestedQty'] + $balance_do['balance'];

            if ($balanceQty_do <= 0) {
                $do_data['invoicedYN'] = 1;
                $this->db->where('DODetailsAutoID', $rTmp['DODetailsID']);
                $this->db->update('srp_erp_deliveryorderdetails', $do_data);
            } else {
                $do_data['invoicedYN'] = 0;
                $this->db->where('DODetailsAutoID', $rTmp['DODetailsID']);
                $this->db->update('srp_erp_deliveryorderdetails', $do_data);
            }
        }
        /** End Of Update DO Table*/

        /** update sub item master */

        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $rTmp['invoiceAutoID']);
        $this->db->where('soldDocumentDetailID', $rTmp['invoiceDetailsAutoID']);
        $this->db->where('soldDocumentID', 'CINV');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);

        /** end update sub item master */

        $this->db->select('SUM(IFNULL(transactionAmount, 0)) as transactionAmount');
        $this->db->from('srp_erp_rvadvancematchdetails');
        $this->db->where('invoiceDetailsAutoID', $id);
        $this->db->where('invoiceAutoID', $rTmp['invoiceAutoID']);
        $advanceMatched = $this->db->get()->row('transactionAmount');
        if($advanceMatched) {
            $this->db->delete('srp_erp_rvadvancematchdetails', array('invoiceDetailsAutoID' => $id, 'invoiceAutoID' => $rTmp['invoiceAutoID']));
            $this->db->query("UPDATE
                                    srp_erp_customerinvoicemaster
                                SET
                                    advanceMatchedTotal = (advanceMatchedTotal - {$advanceMatched}),
                                    receiptInvoiceYN = 0
                                WHERE
                                    invoiceAutoID = {$rTmp['invoiceAutoID']}");
        }

        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster', $rTmp['invoiceAutoID'],'CINV', 'invoiceAutoID');
        if($isGroupByTax == 1){ 
            $this->db->delete('srp_erp_taxledger', array('documentID' => 'CINV','documentMasterAutoID' => $rTmp['invoiceAutoID'],'documentDetailAutoID' => $id));
        }
        $this->db->where('invoiceDetailsAutoID', $id);
        $results = $this->db->delete('srp_erp_customerinvoicedetails');

        //$res = update_customerinvoicemaster_reference($rTmp['invoiceAutoID']);

        /** Added By : (SME-2299)*/
        $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$rTmp['invoiceAutoID']}")->row_array();
        if(!empty($rebate)) {
            $this->calculate_rebate_amount($rTmp['invoiceAutoID']);
        }
        /** End (SME-2299)*/


        if ($results) {
            $this->session->set_flashdata('s', 'Invoice Detail Deleted Successfully');
            return true;
        }
    }

    function save_invoice_item_detail()
    {
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',trim($this->input->post('invoiceAutoID') ?? ''),'CINV', 'invoiceAutoID');
        $projectExist = project_is_exist();
        $retensionEnabled = getPolicyValues('RETO', 'All');

        $invoiceDetailsAutoID = $this->input->post('invoiceDetailsAutoID');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $item_text = $this->input->post('item_text');
        $wareHouse = $this->input->post('wareHouse');
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $projectID = $this->input->post('projectID');
        $quantityRequested = $this->input->post('quantityRequested');
        $item_taxPercentage = $this->input->post('item_taxPercentage');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $promotionID = $this->input->post('promotionID');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');
        $SUOMQty = $this->input->post('SUOMQty');
        $SUOMIDhn = $this->input->post('SUOMIDhn');

        $noOfItems = $this->input->post('noOfItems');
        $grossQty = $this->input->post('grossQty');
        $noOfUnits = $this->input->post('noOfUnits');
        $deduction = $this->input->post('deduction');

        $this->db->trans_start();
        $this->db->select('transactionCurrency,retentionPercentage, transactionExchangeRate, companyLocalCurrency,companyLocalCurrencyID, companyReportingCurrency, companyReportingCurrencyID, companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $tax_master = array();
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm= $this->db->get()->row_array();

            //    if (!trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
            //        if($serviceitm['mainCategory']=="Inventory") {
            //            $this->db->select('invoiceAutoID,,itemDescription,itemSystemCode');
            //            $this->db->from('srp_erp_customerinvoicedetails');
            //            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            //            $this->db->where('itemAutoID', $itemAutoID);
            //            $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
            //            $order_detail = $this->db->get()->row_array();
            //            if (!empty($order_detail)) {
            //                return array('w', 'Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            //            }
            //        }
            //    }

            $wareHouse_location = explode('|', $wareHouse[$key]);
            $item_arr = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);
            $project_categoryID = $this->input->post('project_categoryID');
            $project_subCategoryID = $this->input->post('project_subCategoryID');

            $data['invoiceAutoID'] = trim($invoiceAutoID);
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_arr['itemSystemCode'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];
            }

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){

                    $batch_number2 = $this->input->post("batch_number[{$key}]");
                    $arraydata2 = implode(",",$batch_number2);
                    $data['batchNumber'] = $arraydata2;
            }

            $data['itemDescription'] = $item_arr['itemDescription'];
            $data['SUOMQty'] = $SUOMQty[$key];
            $data['SUOMID'] = $SUOMIDhn[$key];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = $quantityRequested[$key];
            $data['promotionID'] = $promotionID[$key];
            $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = $discount_amount[$key];
            $amountafterdiscount = $estimatedAmount[$key] - $data['discountAmount'];
            $data['unittransactionAmount'] = round($estimatedAmount[$key], $master['transactionCurrencyDecimalPlaces']);
            $data['taxPercentage'] = $item_taxPercentage[$key];
            $taxAmount = ($data['taxPercentage'] / 100) * $amountafterdiscount;
            $data['taxAmount'] = round($taxAmount, $master['transactionCurrencyDecimalPlaces']);
            $totalAfterTax = $data['taxAmount'] * $data['requestedQty'];
            $data['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
            $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested[$key];
            $data['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $data['comment'] = $comment[$key];
            $data['remarks'] = $remarks[$key];
            $data['type'] = 'Item';
            $item_data = fetch_item_data($data['itemAutoID']);
            if($serviceitm['mainCategory']=="Service") {
                $data['wareHouseAutoID'] = 0;
                $data['wareHouseCode'] = null;
                $data['wareHouseLocation'] = null;
                $data['wareHouseDescription'] = null;
            }else{
                $data['wareHouseAutoID'] = $wareHouseAutoID[$key];
                $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
                $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
                $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
            }
            $data['segmentID'] = $master['segmentID'];
            $data['segmentCode'] = $master['segmentCode'];
            $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
            $data['expenseGLCode'] = $item_data['costGLCode'];
            $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['expenseGLDescription'] = $item_data['costDescription'];
            $data['expenseGLType'] = $item_data['costType'];
            $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
            $data['revenueGLCode'] = $item_data['revanueGLCode'];
            $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['revenueGLDescription'] = $item_data['revanueDescription'];
            $data['revenueGLType'] = $item_data['revanueType'];
            $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
            $data['assetGLCode'] = $item_data['assteGLCode'];
            $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['assetGLDescription'] = $item_data['assteDescription'];
            $data['assetGLType'] = $item_data['assteType'];
            $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['itemCategory'] = $item_data['mainCategory'];

            if($retensionEnabled == 1){
                $data['retensionPercentage'] = $master['retentionPercentage'];
                $data['retensionValue'] = round((($data['transactionAmount'] * $master['retentionPercentage']) / 100),2);
            }

            $data['noOfItems'] = $noOfItems[$key];
            $data['grossQty'] = $grossQty[$key];
            $data['noOfUnits'] = $noOfUnits[$key];
            $data['deduction'] = $deduction[$key];

            if (isset($item_text[$key])) {
                if($isGroupByTax == 1) {
                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$item_text[$key]);
                    $tax_master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

                    $dataTax['invoiceAutoID'] = trim($invoiceAutoID);
                    $dataTax['taxFormulaMasterID'] = $item_text[$key];
                    $dataTax['taxDescription'] = $tax_master['Description'];
                    $dataTax['transactionCurrencyID'] = $master['transactionCurrencyID'];
                    $dataTax['transactionCurrency'] = $master['transactionCurrency'];
                    $dataTax['transactionExchangeRate'] = $master['transactionExchangeRate'];
                    $dataTax['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                    $dataTax['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                    $dataTax['companyLocalCurrency'] = $master['companyLocalCurrency'];
                    $dataTax['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                    $dataTax['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                    $dataTax['companyReportingCurrency'] = $master['companyReportingCurrency'];
                    $dataTax['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];

                    $tot_amount = $estimatedAmount[$key] * $quantityRequested[$key];

                } else {
                    $this->db->select('*');
                    $this->db->where('taxMasterAutoID', $item_text[$key]);
                    $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();  
                    
                    $this->db->select('srp_erp_taxmaster.*,srp_erp_chartofaccounts.GLAutoID as liabilityAutoID,srp_erp_chartofaccounts.systemAccountCode as liabilitySystemGLCode,srp_erp_chartofaccounts.GLSecondaryCode as liabilityGLAccount,srp_erp_chartofaccounts.GLDescription as liabilityDescription,srp_erp_chartofaccounts.CategoryTypeDescription as liabilityType,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.DecimalPlaces');
                    $this->db->where('taxMasterAutoID', $item_text[$key]);
                    $this->db->from('srp_erp_taxmaster');
                    $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.supplierGLAutoID');
                    $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_taxmaster.supplierCurrencyID');
                    $tax_master = $this->db->get()->row_array();

                    if (!empty($tax_master)) {
                        $data['taxMasterAutoID'] = $tax_master['taxMasterAutoID'];
                        $data['taxDescription'] = $tax_master['taxDescription'];
                        $data['taxShortCode'] = $tax_master['taxShortCode'];
                        $data['taxSupplierAutoID'] = $tax_master['supplierAutoID'];
                        $data['taxSupplierSystemCode'] = $tax_master['supplierSystemCode'];
                        $data['taxSupplierName'] = $tax_master['supplierName'];
                        $data['taxSupplierCurrencyID'] = $tax_master['supplierCurrencyID'];
                        $data['taxSupplierCurrency'] = $tax_master['CurrencyCode'];
                        $data['taxSupplierCurrencyDecimalPlaces'] = $tax_master['DecimalPlaces'];
                        $data['taxSupplierliabilityAutoID'] = $tax_master['liabilityAutoID'];
                        $data['taxSupplierliabilitySystemGLCode'] = $tax_master['liabilitySystemGLCode'];
                        $data['taxSupplierliabilityGLAccount'] = $tax_master['liabilityGLAccount'];
                        $data['taxSupplierliabilityDescription'] = $tax_master['liabilityDescription'];
                        $data['taxSupplierliabilityType'] = $tax_master['liabilityType'];
                        $supplierCurrency = currency_conversion($master['transactionCurrency'], $data['taxSupplierCurrency']);
                        $data['taxSupplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                        $data['taxSupplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                        $data['taxSupplierCurrencyAmount'] = ($data['transactionAmount'] / $data['taxSupplierCurrencyExchangeRate']);
                    } else {
                        $data['taxSupplierCurrencyExchangeRate'] = 1;
                        $data['taxSupplierCurrencyDecimalPlaces'] = 2;
                        $data['taxSupplierCurrencyAmount'] = 0;
                    }
                }      
            }

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];


            if ($invoiceDetailsAutoID) {
                /*$this->db->where('invoiceDetailsAutoID', trim($invoiceDetailsAutoID));
                $this->db->update('srp_erp_customerinvoicedetails', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('invoiceDetailsAutoID'));
                }*/
            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerinvoicedetails', $data);
                $last_id = $this->db->insert_id();

                if($isGroupByTax == 1) {
                    $discountAmount = $data['discountAmount'] * $quantityRequested[$key];
                    if(!empty($item_text[$key])){
                        tax_calculation_vat('srp_erp_customerinvoicetaxdetails',$dataTax,$item_text[$key],'invoiceAutoID',trim($invoiceAutoID),$tot_amount,'CINV',$last_id,$discountAmount,1);
                    }
                }
                if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                    $this->db->select('itemAutoID');
                    $this->db->where('itemAutoID', $itemAutoID);
                    $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                    if (empty($warehouseitems)) {
                        $data_arr = array(
                            'wareHouseAutoID' => $data['wareHouseAutoID'],
                            'wareHouseLocation' => $data['wareHouseLocation'],
                            'wareHouseDescription' => $data['wareHouseDescription'],
                            'itemAutoID' => $data['itemAutoID'],
                            'itemSystemCode' => $data['itemSystemCode'],
                            'barCodeNo' => $item_data['barcode'],
                            'salesPrice' => $item_data['companyLocalSellingPrice'],
                            'ActiveYN' => $item_data['isActive'],
                            'itemDescription' => $data['itemDescription'],
                            'unitOfMeasureID' => $data['defaultUOMID'],
                            'unitOfMeasure' => $data['defaultUOM'],
                            'currentStock' => 0,
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'companyCode' => $this->common_data['company_data']['company_code'],
                        );
                        $this->db->insert('srp_erp_warehouseitems', $data_arr);
                    }
                }
            }
        }
        /** Added By : (SME-2299)*/
        $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoiceAutoID}")->row_array();
        if(!empty($rebate['rebatePercentage'])) {
            $this->calculate_rebate_amount($invoiceAutoID);
        }
        /** End (SME-2299)*/

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Invoice Detail : Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Invoice Detail : Saved Successfully.');
        }
    }

    function update_invoice_item_detail()
    {
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',trim($this->input->post('invoiceAutoID') ?? ''),'CINV', 'invoiceAutoID');
        $invoiceDetailsAutoID = $this->input->post('invoiceDetailsAutoID');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $itemAutoID = $this->input->post('itemAutoID');
        $item_text = $this->input->post('item_text');
        $wareHouse = $this->input->post('wareHouse');
        $projectID = $this->input->post('projectID');
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $item_taxPercentage = $this->input->post('item_taxPercentage');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $promotionID = $this->input->post('promotionID');
        $discount_amount = $this->input->post('discount_amount');
        $discount = $this->input->post('discount');
        $projectExist = project_is_exist();

        $this->db->select('mainCategory');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $itemAutoID);
        $serviceitm= $this->db->get()->row_array();

        $this->db->trans_start();
        $this->db->select('transactionExchangeRate, companyLocalCurrencyID, companyLocalCurrency, companyReportingCurrencyID, companyReportingCurrency, companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID,invoiceType,isDOItemWisePolicy');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $tax_master = array();
//        if($serviceitm['mainCategory']=="Inventory") {
//            if (!empty($this->input->post('invoiceDetailsAutoID'))) {
//                $this->db->select('invoiceAutoID,,itemDescription,itemSystemCode');
//                $this->db->from('srp_erp_customerinvoicedetails');
//                $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
//                $this->db->where('itemAutoID', $itemAutoID);
//                $this->db->where('invoiceDetailsAutoID !=', $invoiceDetailsAutoID);
//                $order_detail = $this->db->get()->row_array();
//                if (!empty($order_detail)) {
//                    return array('w', 'Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
//                }
//            }
//        }

        $wareHouse_location = explode('|', $wareHouse);
        $item_arr = fetch_item_data($itemAutoID);
        $uomEx = explode('|', $uom);

        $data['invoiceAutoID'] = trim($invoiceAutoID);
        $data['itemAutoID'] = $itemAutoID;
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['projectID'] = $projectID;
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');
        }
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['SUOMQty'] = $this->input->post('SUOMQty');
        $data['SUOMID'] = $this->input->post('SUOMIDhn');
        $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
        $data['unitOfMeasureID'] = $UnitOfMeasureID;
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = $quantityRequested;
        $data['promotionID'] = $promotionID;
        $data['discountPercentage'] = $discount;
        $data['discountAmount'] = $discount_amount;
        $amountafterdiscount = $estimatedAmount - $discount_amount;
        $data['unittransactionAmount'] = round($estimatedAmount, $master['transactionCurrencyDecimalPlaces']);
        $data['taxPercentage'] = $item_taxPercentage;
        $taxAmount = ($data['taxPercentage'] / 100) * $amountafterdiscount;
        $data['taxAmount'] = round($taxAmount, $master['transactionCurrencyDecimalPlaces']);
        $totalAfterTax = $data['taxAmount'] * $data['requestedQty'];
        $data['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
        $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested;
        $data['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
        $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
        $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
        $data['comment'] = $comment;
        $data['remarks'] = $remarks;
        //$data['type'] = 'Item';
        if($master['invoiceType'] == 'DeliveryOrder' && $master['isDOItemWisePolicy'] == 1 ){
            $data['type'] = 'DO';
        }else{
            $data['type'] = 'Item';
        }
        $item_data = fetch_item_data($data['itemAutoID']);
        if($serviceitm['mainCategory']=="Service") {
            $data['wareHouseAutoID'] = 0;
            $data['wareHouseCode'] = null;
            $data['wareHouseLocation'] = null;
            $data['wareHouseDescription'] = null;
        }else{
            $data['wareHouseAutoID'] = $wareHouseAutoID;
            $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
            $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
            $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
        }
        $data['segmentID'] = $master['segmentID'];
        $data['segmentCode'] = $master['segmentCode'];
        $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
        $data['expenseGLCode'] = $item_data['costGLCode'];
        $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
        $data['expenseGLDescription'] = $item_data['costDescription'];
        $data['expenseGLType'] = $item_data['costType'];
        $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
        $data['revenueGLCode'] = $item_data['revanueGLCode'];
        $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
        $data['revenueGLDescription'] = $item_data['revanueDescription'];
        $data['revenueGLType'] = $item_data['revanueType'];
        $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
        $data['assetGLCode'] = $item_data['assteGLCode'];
        $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
        $data['assetGLDescription'] = $item_data['assteDescription'];
        $data['assetGLType'] = $item_data['assteType'];
        $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
        $data['itemCategory'] = $item_data['mainCategory'];

        if (isset($item_text)) {
            if($isGroupByTax == 1) {
                $this->db->select('*');
                $this->db->where('taxCalculationformulaID',$item_text);
                $tax_master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

                $dataTax['invoiceAutoID'] = trim($invoiceAutoID);
                $dataTax['taxFormulaMasterID'] = $item_text;
                $dataTax['taxDescription'] = $tax_master['Description'];
                $dataTax['transactionCurrencyID'] = $master['transactionCurrencyID'];
                $dataTax['transactionCurrency'] = $master['transactionCurrency'];
                $dataTax['transactionExchangeRate'] = $master['transactionExchangeRate'];
                $dataTax['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                $dataTax['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                $dataTax['companyLocalCurrency'] = $master['companyLocalCurrency'];
                $dataTax['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $dataTax['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                $dataTax['companyReportingCurrency'] = $master['companyReportingCurrency'];
                $dataTax['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];

                $tot_amount = $estimatedAmount * $quantityRequested;
            } else {
                $this->db->select('*');
                $this->db->where('taxMasterAutoID', $item_text);
                $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();
    
                $this->db->select('*');
                $this->db->where('supplierSystemCode', $tax_master['supplierSystemCode']);
                $Supplier_master = $this->db->get('srp_erp_suppliermaster')->row_array();
         
                if (!empty($tax_master)) {
                    $data['taxMasterAutoID'] = $tax_master['taxMasterAutoID'];
                    $data['taxDescription'] = $tax_master['taxDescription'];
                    $data['taxShortCode'] = $tax_master['taxShortCode'];
                    $data['taxSupplierAutoID'] = $tax_master['supplierAutoID'];
                    $data['taxSupplierSystemCode'] = $tax_master['supplierSystemCode'];
                    $data['taxSupplierName'] = $tax_master['supplierName'];
                    $data['taxSupplierCurrencyID'] = $tax_master['supplierCurrencyID'];
                    $data['taxSupplierCurrency'] = $tax_master['supplierCurrency'];
                    $data['taxSupplierCurrencyDecimalPlaces'] = $tax_master['supplierCurrencyDecimalPlaces'];
                    $data['taxSupplierliabilityAutoID'] = $tax_master['supplierGLAutoID'];
                    $data['taxSupplierliabilitySystemGLCode'] = $tax_master['supplierGLSystemGLCode'];
                    $data['taxSupplierliabilityGLAccount'] = $tax_master['supplierGLAccount'];
                    $data['taxSupplierliabilityDescription'] = $tax_master['supplierGLDescription'];
                    $data['taxSupplierliabilityType'] = $tax_master['supplierGLType'];
                    $supplierCurrency = currency_conversion($master['transactionCurrency'], $data['taxSupplierCurrency']);
                    $data['taxSupplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                    $data['taxSupplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                    $data['taxSupplierCurrencyAmount'] = ($data['transactionAmount'] / $data['taxSupplierCurrencyExchangeRate']);
                } else {
                    $data['taxSupplierCurrencyExchangeRate'] = 1;
                    $data['taxSupplierCurrencyDecimalPlaces'] = 2;
                    $data['taxSupplierCurrencyAmount'] = 0;
                }
            }
        }

        $data['noOfItems'] = $this->input->post('noOfItems');
        $data['grossQty'] = $this->input->post('grossQty');
        $data['noOfUnits'] = $this->input->post('noOfUnits');
        $data['deduction'] = $this->input->post('deduction');

        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy==1){
            $batch_number1 = $this->input->post('batch_number');
            $arraydata1 = implode(',',$batch_number1);
            $data['batchNumber'] = $arraydata1;

        }

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $data['wareHouseAutoID'],
                    'wareHouseLocation' => $data['wareHouseLocation'],
                    'wareHouseDescription' => $data['wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'barCodeNo' => $item_data['barcode'],
                    'salesPrice' => $item_data['companyLocalSellingPrice'],
                    'ActiveYN' => $item_data['isActive'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }
        }

        if ($invoiceDetailsAutoID) {
            $contractID = $this->db->query("SELECT contractDetailsAutoID FROM srp_erp_customerinvoicedetails WHERE invoiceDetailsAutoID = {$invoiceDetailsAutoID}")->row_array();
            $compID = $this->common_data['company_data']['company_id'];
            if(isset($contractID['contractDetailsAutoID'])){
                $contractedTotal = $this->db->query("SELECT (IFNULL(deliveredQty, 0) + IFNULL(invoiced.requestedQty, 0)) AS totalDeliveredQty, srp_erp_contractdetails.requestedQty 
                    FROM srp_erp_contractdetails
                        LEFT JOIN ( SELECT SUM( deliveredQty ) AS deliveredQty, contractDetailsAutoID FROM srp_erp_deliveryorderdetails GROUP BY contractDetailsAutoID ) delivered ON delivered.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID
                        LEFT JOIN ( SELECT SUM( requestedQty ) AS requestedQty, contractDetailsAutoID FROM srp_erp_customerinvoicedetails WHERE invoiceDetailsAutoID != {$invoiceDetailsAutoID} GROUP BY contractDetailsAutoID ) invoiced ON invoiced.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID 
                    WHERE companyID = {$compID} AND srp_erp_contractdetails.contractDetailsAutoID = {$contractID['contractDetailsAutoID']}")->row_array();

                $deliveredTot = $contractedTotal['totalDeliveredQty'] + $quantityRequested;
                if ($deliveredTot >= $contractedTotal['requestedQty'])
                {
                    $cont_data['invoicedYN'] = 1;
                    $this->db->where('contractDetailsAutoID', $contractID['contractDetailsAutoID']);
                    $this->db->update('srp_erp_contractdetails', $cont_data);
                } else {
                    $cont_data['invoicedYN'] = 0;
                    $this->db->where('contractDetailsAutoID', $contractID['contractDetailsAutoID']);
                    $this->db->update('srp_erp_contractdetails', $cont_data);
                }
            }

            if($master['invoiceType'] == 'DeliveryOrder' && $master['isDOItemWisePolicy'] == 1 ){

                $doID = $this->db->query("SELECT DODetailsID FROM srp_erp_customerinvoicedetails WHERE invoiceDetailsAutoID = {$invoiceDetailsAutoID}")->row_array();
                if(isset($doID['DODetailsID'])){
                    $doTotal = $this->db->query("SELECT (IFNULL(deliveredQty, 0) + IFNULL(invoiced.requestedQty, 0)) AS totalDeliveredQty, srp_erp_deliveryorderdetails.requestedQty 
                        FROM srp_erp_deliveryorderdetails
                            LEFT JOIN ( SELECT SUM( requestedQty ) AS requestedQty, DODetailsID FROM srp_erp_customerinvoicedetails WHERE invoiceDetailsAutoID != {$invoiceDetailsAutoID} 
                            GROUP BY DODetailsID ) invoiced ON invoiced.DODetailsID = srp_erp_deliveryorderdetails.DODetailsAutoID 
                        WHERE companyID = {$compID} AND srp_erp_deliveryorderdetails.DODetailsAutoID = {$doID['DODetailsID']}")->row_array();

                    $deliveredTot = $doTotal['totalDeliveredQty'] + $quantityRequested;
                    if ($deliveredTot >= $doTotal['requestedQty'])
                    {
                        $cont_data['invoicedYN'] = 1;
                        $this->db->where('DODetailsAutoID', $doID['DODetailsID']);
                        $this->db->update('srp_erp_deliveryorderdetails', $cont_data);
                    } else {
                        $cont_data['invoicedYN'] = 0;
                        $this->db->where('DODetailsAutoID', $doID['DODetailsID']);
                        $this->db->update('srp_erp_deliveryorderdetails', $cont_data);
                    }
                }
            }

            $this->db->where('invoiceDetailsAutoID', trim($invoiceDetailsAutoID));
            $this->db->update('srp_erp_customerinvoicedetails', $data);
            if($isGroupByTax == 1 && isset($item_text) && !empty($item_text)) {
                $discountAmount = $discount_amount * $quantityRequested;
                if(!empty($item_text)){
                    tax_calculation_vat('srp_erp_customerinvoicetaxdetails',$dataTax,$item_text,'invoiceAutoID',trim($invoiceAutoID),$tot_amount,'CINV',$invoiceDetailsAutoID,$discountAmount,1);
                }
            } else if($isGroupByTax == 1 && empty($item_text)) {
                fetchExistsDetailTBL('CINV', trim($invoiceAutoID),trim($invoiceDetailsAutoID),null, 0,$data['transactionAmount']);
            }
            
            /** Added By : (SME-2299)*/
            $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoiceAutoID}")->row_array();
            if(!empty($rebate['rebatePercentage'])) {
                $this->calculate_rebate_amount($invoiceAutoID);
            }
            /** End (SME-2299)*/

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
            }
        }
    }

    function fetch_invoice_template_data($invoiceAutoID)
    {
          
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 

        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $companyID = current_companyID();
        $this->db->select('srp_erp_customerinvoicemaster.*, DATE_FORMAT(srp_erp_customerinvoicemaster.createdDateTime,\'' . $convertFormat . '\') AS createdDateTime, IFNULL(srp_erp_customermaster.vatIdNo, 0) as vatIdNo, srp_erp_customermaster.sVatNumber, srp_erp_segment.description as segDescription, DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDate,\'' . $convertFormat . '\') AS invoiceDate ,DATE_FORMAT(srp_erp_customerinvoicemaster.supplyDate,\'' . $convertFormat . '\') AS supplyDate, DATE_FORMAT(srp_erp_customerinvoicemaster.acknowledgementDate,\'' . $convertFormat . '\') AS acknowledgementDate , DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate, DATE_FORMAT(srp_erp_customerinvoicemaster.customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate, DATE_FORMAT(srp_erp_customerinvoicemaster.approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate, CASE WHEN srp_erp_customerinvoicemaster.confirmedYN = 2 || srp_erp_customerinvoicemaster.confirmedYN = 3   THEN " - " WHEN srp_erp_customerinvoicemaster.confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(srp_erp_customerinvoicemaster.confirmedbyName),srp_erp_customerinvoicemaster.confirmedbyName,\'-\'), IF(LENGTH(DATE_FORMAT( srp_erp_customerinvoicemaster.confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )), DATE_FORMAT( srp_erp_customerinvoicemaster.confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn, srp_erp_salespersonmaster.SalesPersonName as SalesPersonName,srp_designation.DesDescription as DesDescription,logisticBLNo,logisticContainerNo, company_name AS accountName, textIdentificationNo, IFNULL(taxCardNo, " - ") as taxCardNo,srp_erp_company.companyVatNumber,srp_erp_company.companySVatNumber, srp_erp_company.vatRegisterYN,isDOItemWisePolicy');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->join('srp_erp_salespersonmaster', 'srp_erp_salespersonmaster.salesPersonID = srp_erp_customerinvoicemaster.salesPersonID','LEFT');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_salespersonmaster.EIdNo','LEFT');
        $this->db->join('srp_designation', 'srp_designation.DesignationID = srp_employeesdetails.EmpDesignationId AND srp_designation.isDeleted=0 AND srp_designation.Erp_companyID=  \'' . $companyID . '\'','LEFT');
        $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_customerinvoicemaster.segmentID', 'Left');
        $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_erp_customerinvoicemaster.companyID', 'Left');
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'Left');
        $this->db->from('srp_erp_customerinvoicemaster');

        $data['master'] = $this->db->get()->row_array();

        $data['master']['retentionInvoiceCode']='';
        if($data['master']){
            if($data['master']['retensionInvoiceID'] <> ''){
                /*Retention*/
                $this->db->select('invoiceCode');
                $this->db->where('invoiceAutoID', $data['master']['retensionInvoiceID']);
                $this->db->from('srp_erp_customerinvoicemaster');
                $retention = $this->db->get()->row_array();
                $data['master']['retentionInvoiceCode']=$retention['invoiceCode'];
            }

            //check bank gl details
            if($data['master']['bankGLAutoID']){
               $data['master']['bankChartOfAccount'] = fetch_gl_account_desc($data['master']['bankGLAutoID']);
            }

        }
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax,customerCountry,vatIdNo');
        $this->db->where('customerAutoID', $data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();

        $this->db->select('wareHouseLocation');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('wareHouseAutoID !=','');
        $this->db->from('srp_erp_customerinvoicedetails');
        $data['warehousearea'] = $this->db->get()->row_array();

        $str = '';
        if($data['master']['invoiceType'] == 'Manufacturing'){
            $str = ', CONCAT(srp_erp_mfq_itemmaster.itemSystemCode," - ",srp_erp_mfq_itemmaster.itemDescription) as mfq_item_Description';
        }
        $this->db->select('IFNULL(taxLedgerDetails.amount,0) as amount,IFNULL(taxLedgerDetails.taxPercentage,0)  as taxpercentageLedger,warehouse.wareHouseDescription as warehouse,srp_erp_customerinvoicedetails.*,srp_erp_itemmaster.partNo,srp_erp_itemmaster.seconeryItemCode AS itemSecondaryCode,
                srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,srp_erp_customerinvoicedetails.serviceFromDate,srp_erp_customerinvoicedetails.serviceToDate,srp_erp_itemmaster.barcode,srp_erp_unit_of_measure.UnitShortCode as secuom,contractmaster.documentID,'.$item_code_alias.''.$str);
        $this->db->where('srp_erp_customerinvoicedetails.invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'Item');        
        $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerinvoicedetails.SUOMID','left');
        $this->db->join('srp_erp_warehousemaster warehouse ', ' warehouse.wareHouseAutoID = srp_erp_customerinvoicedetails.wareHouseAutoID','left');
        $this->db->join('srp_erp_contractmaster contractmaster ', 'contractmaster.contractAutoID = srp_erp_customerinvoicedetails.contractAutoID','left');
        $this->db->join('(SELECT
                              amount,
                              srp_erp_taxledger.taxPercentage,
                              documentDetailAutoID
                              FROM
                              srp_erp_taxledger 
                              LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                              WHERE documentID = \'CINV\' 
                              AND (taxCategory = 1 AND isVat = "1" ||  taxCategory = 2)
                              GROUP BY documentID,documentDetailAutoID)  taxLedgerDetails','taxLedgerDetails.documentDetailAutoID = srp_erp_customerinvoicedetails.invoiceDetailsAutoID','left');

        $this->db->from('srp_erp_customerinvoicedetails');

        if($data['master']['invoiceType'] == 'Manufacturing'){
            $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.mfqItemID = srp_erp_customerinvoicedetails.mfqItemAutoID');
        }
        

        $data['item_detail'] = $this->db->get()->result_array();

        if($data['master']['invoiceType'] == 'Job'){
            $this->db->select('srp_erp_customerinvoicedetails.*,SUM(srp_erp_customerinvoicedetails.taxAmount) as taxAmount,SUM(srp_erp_customerinvoicedetails.requestedQty) as requestedQty,
            srp_erp_customerinvoicemaster.*,SUM(srp_erp_customerinvoicedetails.transactionAmount) as transactionAmount,srp_erp_customerinvoicedetails.itemSystemCode as itemSecondaryCode,srp_erp_customerinvoicedetails.wareHouseDescription as warehouse');
            $this->db->from('srp_erp_customerinvoicedetails');
            $this->db->where('srp_erp_customerinvoicedetails.invoiceAutoID', $invoiceAutoID);
            $this->db->where('type', 'Item');        
            $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID');
            $this->db->group_by('srp_erp_customerinvoicedetails.itemSystemCode');
            $data['item_detail'] = $this->db->get()->result_array();

            // echo '<pre>';
            // print_r($data['item_detail']); exit;
        }


        $data['item_detail_tax'] = array_sum(array_column($data['item_detail'],'totalAfterTax'));

        $data['item_detail_count'] = $this->db->query("SELECT
                    COUNT(  invoiceDetailsAutoID) as doccount
                    FROM
                        `srp_erp_customerinvoicedetails`
                        JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_customerinvoicedetails`.`itemAutoID`
                        LEFT JOIN `srp_erp_unit_of_measure` ON `srp_erp_unit_of_measure`.`UnitID` = `srp_erp_customerinvoicedetails`.`SUOMID`
                        LEFT JOIN `srp_erp_warehousemaster` `warehouse` ON `warehouse`.`wareHouseAutoID` = `srp_erp_customerinvoicedetails`.`wareHouseAutoID`
                        LEFT JOIN `srp_erp_contractmaster` `contractmaster` ON `contractmaster`.`contractAutoID` = `srp_erp_customerinvoicedetails`.`contractAutoID` 
                    WHERE
                        `invoiceAutoID` = $invoiceAutoID 
                        AND `type` = 'Item'")->row('doccount');

        $data['delivery_order']='';
        $data['delivery_order_itemwise']='';
        if($data['master']['isDOItemWisePolicy'] == 1){

            $this->db->select('domaster.DODate,IFNULL(taxLedgerDetails.amount,0) as amount,IFNULL(taxLedgerDetails.taxPercentage,0)  as taxpercentageLedger,
                warehouse.wareHouseDescription as warehouse, srp_erp_customerinvoicedetails.*,srp_erp_itemmaster.partNo,srp_erp_itemmaster.seconeryItemCode AS itemSecondaryCode,
                srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,srp_erp_unit_of_measure.UnitShortCode as secuom,domaster.documentID,DOCode,'.$item_code_alias.'');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->where('type', 'DO');
            $this->db->where('DODetailsID is not null');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerinvoicedetails.SUOMID','left');
            $this->db->join('srp_erp_warehousemaster warehouse ', ' warehouse.wareHouseAutoID = srp_erp_customerinvoicedetails.wareHouseAutoID','left');
            $this->db->join('srp_erp_deliveryorder domaster ', 'domaster.DOAutoID = srp_erp_customerinvoicedetails.DOMasterID','left');
            $this->db->join('(SELECT
                                amount,
                                srp_erp_taxledger.taxPercentage,
                                documentDetailAutoID
                                FROM
                                srp_erp_taxledger 
                                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                WHERE documentID = \'CINV\' 
                                AND taxCategory = 2 
                                GROUP BY documentID,documentDetailAutoID)  taxLedgerDetails','taxLedgerDetails.documentDetailAutoID = srp_erp_customerinvoicedetails.invoiceDetailsAutoID','left');
            $this->db->from('srp_erp_customerinvoicedetails');
            $data['delivery_order_itemwise'] = $this->db->get()->result_array();
            $data['delivery_order_DS_item'] = $this->db->query("SELECT
                            DOMasterID,
                            DATE_FORMAT( DODate, '%d-%m-%Y' ) AS DODate,
                            DOCode,
                            CONCAT(item.seconeryItemCode, ' | ', cus.itemDescription ) AS itemDesc,
                            FORMAT( cus.requestedQty, 3 ) AS requestedQtyformatted,
                            ( cus.unittransactionAmount - cus.discountAmount ) AS unittransactionAmount,
                            cus.transactionAmount,
                            cus.unitOfMeasure,
                            cus.unitOfMeasureID, 
                            IFNULL( amount, 0 ) AS amount,
                            IFNULL( taxLedgerDetails.taxPercentage, 0 ) AS taxpercentageLedger,
                            IFNULL( cus.taxAmount, 0 ) AS taxAmount
                            FROM
                            srp_erp_customerinvoicedetails cus
                            LEFT JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = cus.DOMasterID
                            LEFT JOIN `srp_erp_itemmaster` `item` ON `cus`.`itemAutoID` = `item`.`ItemAutoID`
                            LEFT JOIN (
                            SELECT
                                amount,
                                srp_erp_taxledger.taxPercentage,
                                documentDetailAutoID 
                            FROM
                                srp_erp_taxledger
                                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                            WHERE
                                documentID = 'DO' 
                                AND taxCategory = 2 
                            GROUP BY
                                documentID,
                                documentDetailAutoID 
                            ) taxLedgerDetails ON taxLedgerDetails.documentDetailAutoID = cus.DODetailsID 
                            WHERE
                            invoiceAutoID = {$invoiceAutoID} 
                            AND cus.`type` = 'DO'")->result_array();
                            

        }else{
            $data['delivery_order'] = $this->db->query("SELECT
                    `DOMasterID`,
                    cus.transactionAmount as transactionAmount, cus.totalAfterTax,
                    DATE_FORMAT(del_ord.DODate, '%d-%m-%Y' ) AS DODate,
                    `del_ord`.`DOCode`,
                    del_ord.referenceNo,
                    `del_ord`.`transactionAmount` AS `do_tr_amount`,
                    `due_amount`,
                    `balance_amount`,
                    IFNULL( sum(amount), 0 ) AS amount,
                    IFNULL( sum(srp_erp_deliveryorderdetails.taxAmount), 0 ) AS taxAmount
                FROM
                    `srp_erp_customerinvoicedetails` `cus`
                    JOIN `srp_erp_deliveryorder` `del_ord` ON `del_ord`.`DOAutoID` = `cus`.`DOMasterID`
                    JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorderdetails.DOAutoID = del_ord.DOAutoID
                    LEFT JOIN (
                    SELECT
                        amount,
                        srp_erp_taxledger.taxPercentage,
                        documentDetailAutoID 
                    FROM
                        srp_erp_taxledger
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                    WHERE
                        documentID = 'DO' 
                        AND taxCategory = 2 
                    GROUP BY
                        documentID,
                        documentDetailAutoID 
                    ) taxLedgerDetails ON taxLedgerDetails.documentDetailAutoID = srp_erp_deliveryorderdetails.DODetailsAutoID
                    
                WHERE
                    `invoiceAutoID` = '$invoiceAutoID' 
                    AND `cus`.`type` = 'DO' 
                    AND `DODetailsID` IS NULL 
                    GROUP BY
                        DOMasterID
                    ")->result_array();
                    }

                    $data['delivery_order_NH'] =$this->db->query("SELECT
                    DOMasterID,
                    `del_ord`.`DOCode`,
                    DATE_FORMAT( del_ord.DODate, '%d-%m-%Y' ) AS DODate,
                    del_ord.referenceNo,
                    `del_ord`.`transactionAmount` AS `do_tr_amount`,
                    sum(cus.transactionAmount) as due_amount,
                    sum(cus.balance_amount) as balance_amount,
                    sum(cus.transactionAmount) as transactionAmount,
                    IFNULL( sum( amount ), 0 ) AS amount,
                    sum(cus.taxAmount) AS taxAmount
                from 
                srp_erp_customerinvoicedetails `cus`
                JOIN `srp_erp_deliveryorder` `del_ord` ON `del_ord`.`DOAutoID` = `cus`.`DOMasterID`
                LEFT JOIN (
                    SELECT
                        amount,
                        documentDetailAutoID 
                    FROM
                        srp_erp_taxledger
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                    WHERE
                        documentID = 'DO' 
                        AND taxCategory = 2 
                    GROUP BY
                        documentDetailAutoID 
                    ) taxLedgerDetails ON taxLedgerDetails.documentDetailAutoID = cus.DODetailsID 
                    
                where
                invoiceAutoID = $invoiceAutoID
                AND `cus`.`type` = 'DO' 
        ")->result_array();
    
        if(!empty($data['delivery_order'])){
            foreach($data['delivery_order'] as $key => $value){
                $data['delivery_order'][$key]['items'] = $this->fetch_delivery_customerinvoice_items($value['DOMasterID']);
            }
        }
       

//        $convertFormat = convert_date_format_sql();
//        $this->db->select('cus.*, DOMasterID,DATE_FORMAT(DODate,\''.  $convertFormat .'\') AS DODate,DOCode,referenceNo,del_ord.transactionAmount AS do_tr_amount,due_amount,balance_amount');
//        $this->db->where('invoiceAutoID', $invoiceAutoID);
//        $this->db->where('type', 'DO');
//        $this->db->from('srp_erp_customerinvoicedetails cus');
//        $this->db->join('srp_erp_deliveryorder del_ord', 'del_ord.DOAutoID = cus.DOMasterID');
//        $data['delivery_order'] = $this->db->get()->result_array();
    
        $data['delivery_order_DS'] = $this->db->query("SELECT
                `DOMasterID`,
                IFNULL(amount, 0) as amount, 
                IFNULL(taxLedgerDetails.taxPercentage,0)  as taxpercentageLedger,
                IFNULL(srp_erp_deliveryorderdetails.taxAmount, 0) as taxAmount,
                DATE_FORMAT( DODate, '%d-%m-%Y' ) AS DODate,
                `DOCode`,
                CONCAT( item.seconeryItemCode, ' | ', item.itemDescription ) AS itemDesc,
                FORMAT( srp_erp_deliveryorderdetails.requestedQty, 3 ) AS requestedQtyformatted,
                (srp_erp_deliveryorderdetails.unittransactionAmount - srp_erp_deliveryorderdetails.discountAmount) as unittransactionAmount,
                srp_erp_deliveryorderdetails.transactionAmount,
                srp_erp_deliveryorderdetails.unitOfMeasure ,
                srp_erp_deliveryorderdetails.unitOfMeasureID
            FROM
                srp_erp_deliveryorderdetails
                JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID
                JOIN `srp_erp_customerinvoicedetails` `cus` ON `srp_erp_deliveryorderdetails`.`DOAutoID` = `cus`.`DOMasterID` 
                LEFT JOIN `srp_erp_itemmaster` `item` ON `srp_erp_deliveryorderdetails`.`itemAutoID` = `item`.`ItemAutoID` 
                LEFT JOIN (
                    SELECT
                        amount,
                        srp_erp_taxledger.taxPercentage,
                        documentDetailAutoID 
                    FROM
                        srp_erp_taxledger
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                    WHERE
                        documentID = 'DO' 
                        AND taxCategory = 2 
                    GROUP BY
                        documentID,
                        documentDetailAutoID 
                ) taxLedgerDetails ON taxLedgerDetails.documentDetailAutoID = srp_erp_deliveryorderdetails.DODetailsAutoID 
            WHERE
                `invoiceAutoID` = {$invoiceAutoID} 
                AND cus.`type` = 'DO'")->result_array();

        $data['delivery_order_ds_count'] = $this->db->query("SELECT COUNT(`DOCode`)  as doccount FROM srp_erp_deliveryorderdetails JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID
                                                                 JOIN `srp_erp_customerinvoicedetails` `cus` ON `srp_erp_deliveryorderdetails`.`DOAutoID` = `cus`.`DOMasterID`
                                                                 LEFT JOIN `srp_erp_itemmaster` `item` ON `srp_erp_deliveryorderdetails`.`itemAutoID` = `item`.`ItemAutoID` 
                                                                 WHERE
                                                                `invoiceAutoID` = {$invoiceAutoID}  
                                                                AND cus.`type` = 'DO'")->row('doccount');


        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['extracharge'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['discount'] = $this->db->get('srp_erp_customerinvoicediscountdetails')->result_array();

       /* $this->db->select('*,CONCAT(revenueGLCode,\' | \',revenueGLDescription,\' | \',revenueGLType) as manufacturinggldes');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_customerinvoicedetails');*/
    // $data['gl_detail'] = $this->db->get()->result_array();

        $data['gl_detail'] = $this->db->query("SELECT
                                                   srp_erp_customerinvoicedetails.*,
                                                   CONCAT( revenueGLCode, ' | ', `revenueGLDescription`, ' | ', revenueGLType ) AS manufacturinggldes ,
                                                   IFNULL( taxLedgerDetails.amount, 0 ) AS amount,
                                                   IFNULL( taxLedgerDetails.taxPercentage, 0 ) AS taxpercentageLedger
                                                   FROM
                                                  `srp_erp_customerinvoicedetails` 
                                                   LEFT JOIN (
                                                               SELECT
                                                               amount,
                                                               srp_erp_taxledger.taxPercentage,
                                                               documentDetailAutoID 
                                                               FROM
                                                               srp_erp_taxledger
                                                               LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                               WHERE
                                                               documentID = 'CINV' 
                                                               AND (srp_erp_taxmaster.taxCategory = 2 OR (srp_erp_taxmaster.taxCategory = 1 AND srp_erp_taxmaster.isVat = 1))
                                                               GROUP BY
                                                               documentID,
                                                               documentDetailAutoID) taxLedgerDetails ON `taxLedgerDetails`.`documentDetailAutoID` = `srp_erp_customerinvoicedetails`.`invoiceDetailsAutoID` 
                                                   WHERE
                                                  `invoiceAutoID` = '{$invoiceAutoID}' 
                                                   AND `type` = 'GL'")->result_array();
       

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'OP');
        $this->db->from('srp_erp_customerinvoicedetails');
        $data['op_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['tax'] = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();

        $this->db->select('srp_erp_customerinvoicedetails.contractAutoID,
        srp_erp_contractmaster.referenceNo AS referenceNo');
        $this->db->from('srp_erp_customerinvoicedetails');
        $this->db->join('srp_erp_contractmaster', 'srp_erp_contractmaster.contractAutoID = srp_erp_customerinvoicedetails.contractAutoID', 'LEFT');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->group_by("srp_erp_contractmaster.contractAutoID");
        $data['referenceNo'] = $this->db->get()->result_array();

        $data['invoiceproject'] = $this->db->query("SELECT detailid.itemDescription,IF(isVariation = 1,variationAmount,totalTransCurrency) as totalTransCurrency,invoicemaster.transactionCurrencyDecimalPlaces,
            srp_erp_customerinvoicedetails.invoiceDetailsAutoID,detailID,
            unitRateTransactionCurrency,
            IFNULL(srp_erp_customerinvoicedetails.transactionAmount,0) as transactionAmount,
            IFNULL(srp_erp_customerinvoicedetails.boqClaimPercentage,0) as boqClaimPercentage,
            isVariation,
            header.retensionPercentage,
            header.headerID,
            header.projectID,
            srp_erp_customerinvoicedetails.invoiceAutoID,
            srp_erp_customerinvoicedetails.invoiceDetailsAutoID,
            srp_erp_customerinvoicedetails.boqDetailID,
              IFNULL( boqPreviousClaimPercentage,0) as boqPreviousClaimPercentage,
           IFNULL( boqTotalClaimPercentage,0) as boqTotalClaimPercentage
             FROM `srp_erp_customerinvoicedetails` LEFT JOIN srp_erp_boq_details detailid on detailid.detailID =srp_erp_customerinvoicedetails.boqDetailID
            LEFT JOIN srp_erp_customerinvoicemaster invoicemaster on invoicemaster.InvoiceAutoID =srp_erp_customerinvoicedetails.InvoiceAutoID
            LEFT JOIN srp_erp_boq_header header on header.headerID = detailid.headerID
             where  Type = 'Project' AND srp_erp_customerinvoicedetails.invoiceAutoID = {$invoiceAutoID}
            ORDER BY
            isVariation asc")->result_array();

        $data['po_numberEST'] = array();
        if(!empty($data['master']['mfqInvoiceAutoID'])){
            $mfqInvoiceAutoID = $data['master']['mfqInvoiceAutoID'];
            $data['po_numberEST'] = $this->db->query("SELECT poNumber, documentCode 
                        FROM srp_erp_mfq_estimatemaster
                            JOIN srp_erp_mfq_job ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID
                            JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID 
                            JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.deliveryNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID
                        WHERE invoiceAutoID =  {$mfqInvoiceAutoID} AND srp_erp_mfq_estimatemaster.companyID = {$companyID}")->result_array();

            $data['mfqInvoiceCode'] = $this->db->query("SELECT invoiceCode FROM srp_erp_mfq_customerinvoicemaster WHERE invoiceAutoID = {$mfqInvoiceAutoID} AND companyID = {$companyID}")->row('invoiceCode');
        
            $linked_subJobs = $this->db->query("SELECT documentCode FROM srp_erp_mfq_job 
                                JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID 
                                JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.deliveryNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID 
                                WHERE invoiceAutoID = {$mfqInvoiceAutoID}")->result_array();
            $data['linkedSubJobs'] = join(', ', array_column($linked_subJobs, 'documentCode'));
        }

        /* Added */
        $data['approvallevels'] = '';
        if($data['master']){
            if($data['master']['confirmedYN'] == 1 && $data['master']['approvedYN'] == 0){
                $this->db->select('ECode, Ename2, documentApprovedID, approvalLevelID, approvedEmpID, ApprovedDate');
                $this->db->from('srp_erp_documentapproved');
                $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_documentapproved.approvedEmpID');
                $this->db->where('documentSystemCode', $invoiceAutoID);
                $this->db->where('documentID', 'CINV');
                $this->db->where('approvedYN', 1);
                $this->db->order_by('approvalLevelID', 'ASC');
                $data['approvallevels'] = $this->db->get()->result_array();
            }
        }

        return $data;
    }

    function conversionRateUOM($umo, $default_umo)
    {
        $this->db->select('UnitID');
        $this->db->where('UnitShortCode', $default_umo);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $masterUnitID = $this->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $this->db->select('UnitID');
        $this->db->where('UnitShortCode', $umo);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $subUnitID = $this->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $this->db->select('conversion');
        $this->db->from('srp_erp_unitsconversion');
        $this->db->where('masterUnitID', $masterUnitID);
        $this->db->where('subUnitID', $subUnitID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get()->row('conversion');
    }

    function load_invoice_header()
    {
        update_group_based_tax('srp_erp_customerinvoicemaster', 'invoiceAutoID', $this->input->post('invoiceAutoID'), 'srp_erp_customerinvoicetaxdetails', null, 'CINV');
        
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,cus.interCompayYN as intercompanyyn,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(acknowledgementDate,\'' . $convertFormat . '\') AS acknowledgementDate,DATE_FORMAT(customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(policyStartDate,\'' . $convertFormat . '\') AS policyStartDate,DATE_FORMAT(policyEndDate,\'' . $convertFormat . '\') AS policyEndDate,IF(invoiceType=\'Direct\',\'DirectItem\',invoiceType) as documentdrop');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $this->db->join('srp_erp_customermaster as cus', 'cus.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        return $this->db->get('srp_erp_customerinvoicemaster')->row_array();
    }

    function fetch_invoice_direct_details()
    {
        $convertFormat = convert_date_format_sql();
       
        
        $this->db->select('transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,invoiceType,ifnull(retentionPercentage,0) as retentionPercentage');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $master_record = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
        $data['currency'] = $master_record;

        if($master_record['invoiceType'] == 'Job'){
            $this->db->select('srp_erp_customerinvoicedetails.*,IFNULL(remarks,"") as remarks,SUM(srp_erp_customerinvoicedetails.requestedQty) as requestedQty,SUM(srp_erp_customerinvoicedetails.transactionAmount) as transactionAmount,srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.partNo,srp_erp_itemmaster.seconeryItemCode AS itemSecondaryCode,srp_erp_suppliermaster.supplierName as supplierName, 
            DOMasterID,DATE_FORMAT(DODate,\''.  $convertFormat .'\') AS DODate,DOCode,referenceNo,del_ord.deliveredTransactionAmount AS do_tr_amount,due_amount,balance_amount,srp_erp_unit_of_measure.UnitShortCode as secuom
            ,SUM(srp_erp_customerinvoicedetails.taxAmount) as taxAmount');
        }else{
            $this->db->select('srp_erp_customerinvoicedetails.*,IFNULL(remarks,"") as remarks, srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.partNo,srp_erp_itemmaster.seconeryItemCode AS itemSecondaryCode,srp_erp_suppliermaster.supplierName as supplierName, 
            DOMasterID,DATE_FORMAT(DODate,\''.  $convertFormat .'\') AS DODate,DOCode,referenceNo,del_ord.deliveredTransactionAmount AS do_tr_amount,due_amount,balance_amount,srp_erp_unit_of_measure.UnitShortCode as secuom');
        }
        
       
        $this->db->from('srp_erp_customerinvoicedetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID', 'left');
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_customerinvoicedetails.supplierAutoID', 'left');
        $this->db->join('srp_erp_deliveryorder del_ord', 'del_ord.DOAutoID = srp_erp_customerinvoicedetails.DOMasterID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerinvoicedetails.SUOMID','left');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));

        if($master_record['invoiceType'] == 'Job'){
            $this->db->group_by('itemSystemCode');
        }

        $data['detail'] = $this->db->get()->result_array();

        $data['invoiceItems'] = array();

        foreach($data['detail'] as $key => $item_details){

            $data['detail'][$key]['items'] = $this->fetch_delivery_customerinvoice_items($item_details['DOMasterID']);

        }


        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicediscountdetails');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $data['discount_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $data['extraChargeDetail'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->result_array();

        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicetaxdetails');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $data['tax_detail'] = $this->db->get()->result_array();
        $taxamount = array_sum(array_column($data['detail'],'taxAmount'));
        $data['Istaxexist'] = ($taxamount>0?1:0);
        return $data;
    }

    function fetch_detail()
    {
       
        $data = array();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $this->db->select('*,'.$item_code_alias.' ');
        $this->db->from('srp_erp_customerinvoicedetails');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select("del_ord.transactionAmount,totalAfterTax");
        $this->db->from("srp_erp_customerinvoicedetails cus");
        $this->db->join("srp_erp_deliveryorder del_ord",'del_ord.DOAutoID = cus.DOMasterID','left');
        $this->db->where("invoiceAutoID",trim($this->input->post('invoiceAutoID') ?? ''));
        $this->db->where("type","DO");
        $data['delivery_order'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $data['tax'] = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();
        return $data;
    }

    function save_direct_invoice_detail()
    {
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',trim($this->input->post('invoiceAutoID') ?? ''),'CINV','invoiceAutoID');
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,retentionPercentage,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $projectExist = project_is_exist();
        $gl_text_type = $this->input->post('gl_text_type');
        $segment_gls = $this->input->post('segment_gl');
        $gl_code_des = $this->input->post('gl_code_des');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $gl_code = $this->input->post('gl_code');
        $projectID = $this->input->post('projectID');
        $amount = $this->input->post('amount');
        $description = $this->input->post('description');
        $discountPercentage = $this->input->post('discountPercentage');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');

        $retensionEnabled = getPolicyValues('RETO', 'All');

        foreach ($segment_gls as $key => $segment_gl) {
            $segment = explode('|', $segment_gl);
            $gl_code_de = explode(' | ', $gl_code_des[$key]);
            $data['invoiceAutoID'] = trim($invoiceAutoID);
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];
            }
            $data['revenueGLAutoID'] = $gl_code[$key];
            $data['revenueSystemGLCode'] = trim($gl_code_de[0] ?? '');
            $data['revenueGLCode'] = trim($gl_code_de[1] ?? '');
            $data['revenueGLDescription'] = trim($gl_code_de[2] ?? '');
            $data['revenueGLType'] = trim($gl_code_de[3] ?? '');
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            $data['discountPercentage'] = trim($discountPercentage[$key]);
            $data['discountAmount'] = trim(($amount[$key]*$discountPercentage[$key])/100);
            $data['transactionAmount'] = round($amount[$key]-$data['discountAmount'], $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            if(isset($description[$key]) && $description[$key] != ''){
               // $data['description'] = $data['revenueGLDescription'].' - '.trim($description[$key]);
                $data['description'] = trim($description[$key]);
            }else{
                $data['description'] = $data['revenueGLDescription'];
            }
           
            $data['type'] = 'GL';

            if($retensionEnabled == 1){
                $data['retensionPercentage'] = $master['retentionPercentage'];
                $data['retensionValue'] = round((($data['transactionAmount'] * $master['retentionPercentage']) / 100),2);
            }

            if (trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                /*$this->db->where('invoiceDetailsAutoID', trim($this->input->post('invoiceDetailsAutoID') ?? ''));
                $this->db->update('srp_erp_customerinvoicedetails', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('invoiceDetailsAutoID'));
                }*/
            } else {
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

            }

            $this->db->insert('srp_erp_customerinvoicedetails', $data);
            $last_id = $this->db->insert_id();
          
            if($isGroupByTax == 1){ 
                if(!empty($gl_text_type[$key])){
                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$gl_text_type[$key]);
                    $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
            
                    $this->db->trans_start();
                    $this->db->select('transactionCurrency, transactionExchangeRate, companyLocalCurrency,companyLocalCurrencyID, companyReportingCurrency, companyReportingCurrencyID, companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
                    $this->db->where('invoiceAutoID', $invoiceAutoID);
                    $inv_master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

                    $dataTax['invoiceAutoID'] = trim($this->input->post('invoiceAutoID') ?? '');
                    $dataTax['taxFormulaMasterID'] = $gl_text_type[$key];
                    $dataTax['taxDescription'] = $master['Description'];
                    $dataTax['transactionCurrencyID'] = $inv_master['transactionCurrencyID'];
                    $dataTax['transactionCurrency'] = $inv_master['transactionCurrency'];
                    $dataTax['transactionExchangeRate'] = $inv_master['transactionExchangeRate'];
                    $dataTax['transactionCurrencyDecimalPlaces'] = $inv_master['transactionCurrencyDecimalPlaces'];
                    $dataTax['companyLocalCurrencyID'] = $inv_master['companyLocalCurrencyID'];
                    $dataTax['companyLocalCurrency'] = $inv_master['companyLocalCurrency'];
                    $dataTax['companyLocalExchangeRate'] = $inv_master['companyLocalExchangeRate'];
                    $dataTax['companyReportingCurrencyID'] = $inv_master['companyReportingCurrencyID'];
                    $dataTax['companyReportingCurrency'] = $inv_master['companyReportingCurrency'];
                    $dataTax['companyReportingExchangeRate'] = $inv_master['companyReportingExchangeRate'];

                    tax_calculation_vat('srp_erp_customerinvoicetaxdetails',$dataTax,$gl_text_type[$key],'invoiceAutoID',trim($invoiceAutoID),$amount[$key],'CINV',$last_id,$data['discountAmount'],1);
                }             
            }
        }

        // $this->db->insert_batch('srp_erp_customerinvoicedetails', $data);

        /** Added By : (SME-2299)*/
        $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoiceAutoID}")->row_array();
        if(!empty($rebate['rebatePercentage'])) {
            $this->calculate_rebate_amount($invoiceAutoID);
        }
        /** End (SME-2299)*/

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Invoice Detail : Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);

        } else {
            $this->session->set_flashdata('s', 'Invoice Detail Saved Successfully');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function update_income_invoice_detail()
    {
        $gl_text_type = $this->input->post('gl_text_type');
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',trim($this->input->post('invoiceAutoID') ?? ''),'CINV','invoiceAutoID');

        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $segment_gl = $this->input->post('segment_gl');
        $gl_code_des = $this->input->post('gl_code_des');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $projectID = $this->input->post('projectID');
        $gl_code = $this->input->post('gl_code');
        $amount = $this->input->post('amount');
        $description = $this->input->post('description');
        $discountPercentage = $this->input->post('discountPercentage');
        $projectExist = project_is_exist();

        $segment = explode('|', $segment_gl);
        $gl_code_de = explode(' | ', $gl_code_des);
        $data['invoiceAutoID'] = trim($invoiceAutoID);
        $data['revenueGLAutoID'] = $gl_code;
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');
        }
        $data['revenueSystemGLCode'] = trim($gl_code_de[0] ?? '');
        $data['revenueGLCode'] = trim($gl_code_de[1] ?? '');
        $data['revenueGLDescription'] = trim($gl_code_de[2] ?? '');
        $data['revenueGLType'] = trim($gl_code_de[3] ?? '');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['transactionAmount'] = round($amount, $master['transactionCurrencyDecimalPlaces']);

        $data['discountPercentage'] = trim($discountPercentage);
        $data['discountAmount'] = trim(($amount*$discountPercentage)/100);
        $data['transactionAmount'] = round($amount-$data['discountAmount'], $master['transactionCurrencyDecimalPlaces']);

        $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
        $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
        $data['description'] = trim($description);
        $data['type'] = 'GL';
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
            $this->db->where('invoiceDetailsAutoID', trim($this->input->post('invoiceDetailsAutoID') ?? ''));
            $this->db->update('srp_erp_customerinvoicedetails', $data);

            if($isGroupByTax == 1 && !empty($gl_text_type)) {
                $this->db->select('*');
                $this->db->where('taxCalculationformulaID',$gl_text_type);
                $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
        
                $this->db->trans_start();
                $this->db->select('transactionCurrency, transactionExchangeRate, companyLocalCurrency,companyLocalCurrencyID, companyReportingCurrency, companyReportingCurrencyID, companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
                $this->db->where('invoiceAutoID', $invoiceAutoID);
                $inv_master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

                $dataTax['invoiceAutoID'] = trim($invoiceAutoID);
                $dataTax['taxFormulaMasterID'] = $gl_text_type;
                $dataTax['taxDescription'] = $master['Description'];
                $dataTax['transactionCurrencyID'] = $inv_master['transactionCurrencyID'];
                $dataTax['transactionCurrency'] = $inv_master['transactionCurrency'];
                $dataTax['transactionExchangeRate'] = $inv_master['transactionExchangeRate'];
                $dataTax['transactionCurrencyDecimalPlaces'] = $inv_master['transactionCurrencyDecimalPlaces'];
                $dataTax['companyLocalCurrencyID'] = $inv_master['companyLocalCurrencyID'];
                $dataTax['companyLocalCurrency'] = $inv_master['companyLocalCurrency'];
                $dataTax['companyLocalExchangeRate'] = $inv_master['companyLocalExchangeRate'];
                $dataTax['companyReportingCurrencyID'] = $inv_master['companyReportingCurrencyID'];
                $dataTax['companyReportingCurrency'] = $inv_master['companyReportingCurrency'];
                $dataTax['companyReportingExchangeRate'] = $inv_master['companyReportingExchangeRate'];

                tax_calculation_vat('srp_erp_customerinvoicetaxdetails',$dataTax,$gl_text_type,'invoiceAutoID',trim($invoiceAutoID),$amount,'CINV',trim($this->input->post('invoiceDetailsAutoID') ?? ''),$data['discountAmount'],1);
            }
            
            if (empty($gl_text_type)){
                fetchExistsDetailTBL('CINV', trim($invoiceAutoID),trim($this->input->post('invoiceDetailsAutoID') ?? ''),'srp_erp_customerinvoicetaxdetails',1, $data['transactionAmount']);
            }

            /** Added By : (SME-2299)*/
            $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoiceAutoID}")->row_array();
            if(!empty($rebate['rebatePercentage'])) {
                $this->calculate_rebate_amount($invoiceAutoID);
            }
            /** End (SME-2299)*/

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Updated Successfully.');
            }
        }
    }

    function fetch_customer_invoice_detail()
    {
        $invoiceDetailsAutoID = trim($this->input->post('invoiceDetailsAutoID') ?? '');
        $this->db->select('srp_erp_customerinvoicedetails.*,srp_erp_customerinvoicemaster.invoiceType,srp_erp_itemmaster.currentStock,srp_erp_itemmaster.mainCategory,srp_erp_unit_of_measure.UnitShortCode as secuom,srp_erp_unit_of_measure.UnitDes as secuomdec, (IFNULL(contractBalance.balance,0) + srp_erp_customerinvoicedetails.requestedQty) AS balanceQty,srp_erp_itemmaster.seconeryItemCode, IFNULL(taxAmount, 0) as taxAmount');
        $this->db->where('invoiceDetailsAutoID', trim($this->input->post('invoiceDetailsAutoID') ?? ''));
        $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicedetails.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_customerinvoicedetails.itemAutoID = srp_erp_itemmaster.itemAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerinvoicedetails.SUOMID','left');
        $this->db->join('(SELECT srp_erp_contractdetails.contractDetailsAutoID, TRIM(TRAILING '.' FROM TRIM(TRAILING 0 FROM(ROUND( ifnull( srp_erp_contractdetails.requestedQty, 0 ), 2 ))) - TRIM(TRAILING 0 FROM(ROUND( ifnull( cinv.requestedQtyINV, 0 ) + ifnull( deliveryorder.requestedQtyDO, 0 ), 2 )))) AS balance 
                                        FROM srp_erp_contractdetails
                                        LEFT JOIN (SELECT contractAutoID, contractDetailsAutoID, itemAutoID, IFNULL( SUM( requestedQty ), 0 ) AS requestedQtyINV FROM srp_erp_customerinvoicedetails WHERE invoiceDetailsAutoID  GROUP BY contractDetailsAutoID) cinv ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `cinv`.`contractDetailsAutoID`
                                        LEFT JOIN (SELECT contractAutoID, contractDetailsAutoID, itemAutoID, IFNULL( SUM( deliveredQty ), 0 ) AS requestedQtyDO FROM srp_erp_deliveryorderdetails GROUP BY contractDetailsAutoID ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
                                ) contractBalance', 'contractBalance.contractDetailsAutoID = srp_erp_customerinvoicedetails.contractDetailsAutoID','left');
        $this->db->from('srp_erp_customerinvoicedetails');
        return $this->db->get()->row_array();
    }

    function invoice_confirmation($invoiceAutoID = null)
    {
        if(empty($invoiceAutoID)) {
            $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        }

        $this->db->trans_start();
        $total_amount = 0;
        $tax_total = 0;
        $t_arr = array();
        $companyID = current_companyID();
        $currentuser  = current_userID();
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $itemBatchPolicy = getPolicyValues('IB', 'All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        $retensionEnabled = getPolicyValues('RETO', 'All');

        $this->db->select('invoiceDetailsAutoID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->from('srp_erp_customerinvoicedetails');
        $results = $this->db->get()->result_array();
        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        } else {
            $this->db->select('invoiceAutoID');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_customerinvoicemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                return array('w', 'Document already confirmed');
            }
            else {
                $customerInvoiceDetail = $this->db->query("SELECT
                    GROUP_CONCAT( itemAutoID ) AS itemAutoID 
                    FROM
                    srp_erp_customerinvoicedetails 
                    WHERE
                    companyID = $companyID
                    AND invoiceAutoID = $invoiceAutoID")->row('itemAutoID');

                if(!empty($customerInvoiceDetail)){ 
                        $wacTransactionAmountValidation  = fetch_itemledger_transactionAmount_validation("$customerInvoiceDetail");
                        if(!empty($wacTransactionAmountValidation)){ 
                            return array('e','Below items are with negative wac amount',$wacTransactionAmountValidation);
                            exit();
                        }
                } 

                $this->load->library('Approvals');
                $this->db->select('documentID,invoiceCode,DATE_FORMAT(invoiceDate, "%Y") as invYear,DATE_FORMAT(invoiceDate, "%m") as invMonth,companyFinanceYearID,invoiceType,isRetensionYN');
                $this->db->where('invoiceAutoID', $invoiceAutoID);
                $this->db->from('srp_erp_customerinvoicemaster');
                $master_dt = $this->db->get()->row_array();

                $this->load->library('sequence');
                $lenth=strlen($master_dt['invoiceCode']);

                if($lenth == 1){
                    if($locationwisecodegenerate == 1)
                    {
                        $this->db->select('locationID');
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location ==' ')) {
                            return array('w' ,'Location is not assigned for current employee');
                        }else
                        {
                            if($locationemployee!='')
                            {
                                $codegerator = $this->sequence->sequence_generator_location($master_dt['documentID'],$master_dt['companyFinanceYearID'], $locationemployee,$master_dt['invYear'],$master_dt['invMonth']);
                            }else
                            {
                                return array('w' ,'Location is not assigned for current employee');
                            }
                        }
                    }
                    else
                    {
                        $codegerator = $this->sequence->sequence_generator_fin($master_dt['documentID'],$master_dt['companyFinanceYearID'],$master_dt['invYear'],$master_dt['invMonth']);
                    }

                    $validate_code = validate_code_duplication($codegerator, 'invoiceCode', $invoiceAutoID,'invoiceAutoID', 'srp_erp_customerinvoicemaster');
                    if(!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }

                    $invcod = array(
                        'invoiceCode' => $codegerator,
                    );
                    $this->db->where('invoiceAutoID', $invoiceAutoID);
                    $this->db->update('srp_erp_customerinvoicemaster', $invcod);
                    
                } else {
                    $validate_code = validate_code_duplication($master_dt['invoiceCode'], 'invoiceCode', $invoiceAutoID,'invoiceAutoID', 'srp_erp_customerinvoicemaster');
                    if(!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                }

                $this->db->select('invoiceAutoID, invoiceCode,segmentID,documentID,transactionCurrency, transactionExchangeRate, companyLocalExchangeRate, companyReportingExchangeRate,customerCurrencyExchangeRate,DATE_FORMAT(invoiceDate, "%Y") as invYear,DATE_FORMAT(invoiceDate, "%m") as invMonth,companyFinanceYearID,invoiceDate ');
                $this->db->where('invoiceAutoID', $invoiceAutoID);
                $this->db->from('srp_erp_customerinvoicemaster');
                $master_data = $this->db->get()->row_array();

                //$sql = "SELECT (srp_erp_customerinvoicedetails.requestedQty / srp_erp_customerinvoicedetails.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,(srp_erp_warehouseitems.currentStock-(srp_erp_customerinvoicedetails.requestedQty / srp_erp_customerinvoicedetails.conversionRateUOM)) as stock ,srp_erp_warehouseitems.itemAutoID,srp_erp_customerinvoicedetails.wareHouseAutoID FROM srp_erp_customerinvoicedetails INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID AND srp_erp_customerinvoicedetails.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where invoiceAutoID = '{$this->input->post('invoiceAutoID')}' AND (itemCategory != 'Service' AND itemCategory != 'Non Inventory')   Having stock < 0";
                /* $sql = "SELECT
                        SUM(
                            srp_erp_customerinvoicedetails.requestedQty / srp_erp_customerinvoicedetails.conversionRateUOM
                        ) AS qty,
                        ware_house.currentStock,
                        (IFNULL(ware_house.currentStock,0)- IFNULL( SUM(srp_erp_customerinvoicedetails.requestedQty / srp_erp_customerinvoicedetails.conversionRateUOM),0)) AS stock,
                        ware_house.itemAutoID,
                        srp_erp_customerinvoicedetails.wareHouseAutoID
                    FROM
                        srp_erp_customerinvoicedetails
                    LEFT JOIN ( 
                        SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID FROM srp_erp_itemledger WHERE companyID ={$companyID} GROUP BY wareHouseAutoID, itemAutoID 
                        ) AS ware_house ON ware_house.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID AND ware_house.wareHouseAutoID = srp_erp_customerinvoicedetails.wareHouseAutoID
                    JOIN `srp_erp_itemmaster` ON `srp_erp_customerinvoicedetails`.`itemAutoID` = `srp_erp_itemmaster`.`itemAutoID`
                    WHERE
                        invoiceAutoID = '{$invoiceAutoID}'
                    AND (
                        mainCategory != 'Service'
                        AND mainCategory != 'Non Inventory'
                    )
                    GROUP BY itemAutoID
                    HAVING
                        stock < 0"; */

                $sql="SELECT
                        SUM( srp_erp_customerinvoicedetails.requestedQty / srp_erp_customerinvoicedetails.conversionRateUOM ) AS qty,
                        ware_house.currentStock,
                        (
                        IFNULL( ware_house.currentStock, 0 )- ((IFNULL(pq.stock,0))+IFNULL( SUM( srp_erp_customerinvoicedetails.requestedQty / srp_erp_customerinvoicedetails.conversionRateUOM ), 0 ))) AS stock,
                        ware_house.itemAutoID,
                        srp_erp_customerinvoicedetails.wareHouseAutoID 
                    FROM
                        srp_erp_customerinvoicedetails
                        LEFT JOIN ( SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID FROM srp_erp_itemledger WHERE companyID = {$companyID} GROUP BY wareHouseAutoID, itemAutoID ) AS ware_house ON ware_house.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID 
                        AND ware_house.wareHouseAutoID = srp_erp_customerinvoicedetails.wareHouseAutoID
                        JOIN `srp_erp_itemmaster` ON `srp_erp_customerinvoicedetails`.`itemAutoID` = `srp_erp_itemmaster`.`itemAutoID` 
                        LEFT JOIN (
                        SELECT
                            SUM( stock ) AS stock,
                            t1.ItemAutoID,
                            wareHouseAutoID 
                        FROM
                            (
                            SELECT
                                IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
                                itemAutoID,
                                srp_erp_stockadjustmentmaster.wareHouseAutoID AS wareHouseAutoID 
                            FROM
                                srp_erp_stockadjustmentmaster
                                LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
                            WHERE
                                companyID = {$companyID} 
                                AND approvedYN != 1 
                                AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
                                itemAutoID,
                                srp_erp_stockcountingmaster.wareHouseAutoID AS wareHouseAutoID 
                            FROM
                                srp_erp_stockcountingmaster
                                LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
                            WHERE
                                companyID = {$companyID} 
                                AND approvedYN != 1 
                                AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock,
                                itemAutoID,
                                srp_erp_itemissuemaster.wareHouseAutoID AS wareHouseAutoID 
                            FROM
                                srp_erp_itemissuemaster
                                LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
                            WHERE
                                srp_erp_itemissuemaster.companyID = {$companyID} 
                                AND approvedYN != 1 
                                AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                ( requestedQty / conversionRateUOM ) AS stock,
                                itemAutoID,
                                srp_erp_customerreceiptdetail.wareHouseAutoID AS wareHouseAutoID 
                            FROM
                                srp_erp_customerreceiptmaster
                                LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                            WHERE
                                srp_erp_customerreceiptmaster.companyID = {$companyID} 
                                AND approvedYN != 1 
                                AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                ( requestedQty / conversionRateUOM ) AS stock,
                                itemAutoID,
                                srp_erp_customerinvoicedetails.wareHouseAutoID AS wareHouseAutoID 
                            FROM
                                srp_erp_customerinvoicemaster
                                LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
                            WHERE
                                srp_erp_customerinvoicemaster.companyID = {$companyID} 
                                AND srp_erp_customerinvoicedetails.invoiceAutoID != '{$invoiceAutoID}'
                                AND approvedYN != 1 
                                AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                ( deliveredQty / conversionRateUOM ) AS stock,
                                itemAutoID,
                                srp_erp_deliveryorderdetails.wareHouseAutoID AS wareHouseAutoID 
                            FROM
                                srp_erp_deliveryorder
                                LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
                            WHERE
                                srp_erp_deliveryorder.companyID = {$companyID} 
                                AND approvedYN != 1 
                                AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                ( transfer_QTY / conversionRateUOM ) AS stock,
                                itemAutoID,
                                srp_erp_stocktransfermaster.from_wareHouseAutoID AS from_wareHouseAutoID 
                            FROM
                                srp_erp_stocktransfermaster
                                LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
                            WHERE
                                srp_erp_stocktransfermaster.companyID = {$companyID} 
                                AND approvedYN != 1 
                                AND itemCategory = 'Inventory' 
                            ) t1 
                        GROUP BY
                            t1.wareHouseAutoID,
                            t1.ItemAutoID 
                        ) AS pq ON pq.ItemAutoID = srp_erp_customerinvoicedetails.itemAutoID 
                        AND pq.wareHouseAutoID = srp_erp_customerinvoicedetails.wareHouseAutoID 
                    
                    WHERE
                        invoiceAutoID = '{$invoiceAutoID}' AND type != 'DO'
                        AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' ) 
                    GROUP BY
                        srp_erp_customerinvoicedetails.itemAutoID 
                    HAVING
                        stock < 0 ";

                $item_low_qty = $this->db->query($sql)->result_array();
                if (!empty($item_low_qty)) {
                    return array('e', 'Some Item quantities are not sufficient to confirm this transaction.',$item_low_qty);
                }

                $autoApproval= get_document_auto_approval('CINV');

                if($master_dt['isRetensionYN'] != 1){
                    if($autoApproval==0){
                        $approvals_status = $this->approvals->auto_approve($master_data['invoiceAutoID'], 'srp_erp_customerinvoicemaster','invoiceAutoID', 'CINV',$master_data['invoiceCode'],$master_data['invoiceDate']);
                    }elseif($autoApproval==1){
                        $approvals_status = $this->approvals->CreateApproval($master_data['documentID'], $master_data['invoiceAutoID'], $master_data['invoiceCode'], 'Invoice', 'srp_erp_customerinvoicemaster', 'invoiceAutoID',0,$master_data['invoiceDate'],$master_data['segmentID']);
                    }else{
                        return array('e', 'Approval levels are not set for this document');
                        exit;
                    }
                }else{
                    $approvals_status = 0;
                }
              
                if ($approvals_status == 1) {
                    /** item Master Sub check */
                    $validate = $this->validate_itemMasterSub($invoiceAutoID);

                    /** end of item master sub */
                    if ($validate) {
                        $this->db->select_sum('transactionAmount');
                        $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                        $transaction_total_amount = $this->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');

                      
                        $this->db->select_sum('totalAfterTax');
                        $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                        $item_tax = $this->db->get('srp_erp_customerinvoicedetails')->row('totalAfterTax');
                        $total_amount = ($transaction_total_amount - $item_tax);
                        $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate ,companyLocalExchangeRate ,taxPercentage');
                        $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                        $tax_arr = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();
                        for ($x = 0; $x < count($tax_arr); $x++) {
                            $tax_total_amount = (($tax_arr[$x]['taxPercentage'] / 100) * $total_amount);
                            $t_arr[$x]['taxDetailAutoID'] = $tax_arr[$x]['taxDetailAutoID'];
                            $t_arr[$x]['transactionAmount'] = $tax_total_amount;
                            $t_arr[$x]['supplierCurrencyAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['supplierCurrencyExchangeRate']);
                            $t_arr[$x]['companyLocalAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyLocalExchangeRate']);
                            $t_arr[$x]['companyReportingAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyReportingExchangeRate']);
                            $tax_total = $t_arr[$x]['transactionAmount'];
                        }
                        /*updating transaction amount using the query used in the master data table*/
                        $companyID=current_companyID();
                        $invautoid=$invoiceAutoID;
                        $r1 = "SELECT
                                        `srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
                                        `srp_erp_customerinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
                                        `srp_erp_customerinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
                                        `srp_erp_customerinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
                                        `srp_erp_customerinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
                                        `srp_erp_customerinvoicemaster`.`customerCurrencyExchangeRate` AS `customerCurrencyExchangeRate`,
                                        `srp_erp_customerinvoicemaster`.`customerCurrencyDecimalPlaces` AS `customerCurrencyDecimalPlaces`,
                                        `srp_erp_customerinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,

                                        (
                                            IFNULL(addondet.taxPercentage, 0) / 100
                                        ) * (
                                            IFNULL(det.transactionAmount, 0) - IFNULL(det.detailtaxamount, 0) - (
                                                (
                                                    IFNULL(
                                                        gendiscount.discountPercentage,
                                                        0
                                                    ) / 100
                                                ) * IFNULL(det.transactionAmount, 0)
                                            ) + IFNULL(
                                                genexchargistax.transactionAmount,
                                                0
                                            )
                                        ) + IFNULL(det.transactionAmount, 0) - (
                                            (
                                                IFNULL(
                                                    gendiscount.discountPercentage,
                                                    0
                                                ) / 100
                                            ) * IFNULL(det.transactionAmount, 0)
                                        ) + IFNULL(
                                            genexcharg.transactionAmount,
                                            0
                                        ) AS total_value

                                    FROM
                                        `srp_erp_customerinvoicemaster`
                                    LEFT JOIN (
                                        SELECT
                                            SUM(transactionAmount) AS transactionAmount,
                                            sum(totalafterTax) AS detailtaxamount,
                                            invoiceAutoID
                                        FROM
                                            srp_erp_customerinvoicedetails
                                        GROUP BY
                                            invoiceAutoID
                                    ) det ON (
                                        `det`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
                                    )
                                    LEFT JOIN (
                                        SELECT
                                            SUM(taxPercentage) AS taxPercentage,
                                            InvoiceAutoID
                                        FROM
                                            srp_erp_customerinvoicetaxdetails
                                        GROUP BY
                                            InvoiceAutoID
                                    ) addondet ON (
                                        `addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
                                    )
                                    LEFT JOIN (
                                        SELECT
                                            SUM(discountPercentage) AS discountPercentage,
                                            invoiceAutoID
                                        FROM
                                            srp_erp_customerinvoicediscountdetails
                                        GROUP BY
                                            invoiceAutoID
                                    ) gendiscount ON (
                                        `gendiscount`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
                                    )
                                    LEFT JOIN (
                                        SELECT
                                            SUM(transactionAmount) AS transactionAmount,
                                            invoiceAutoID
                                        FROM
                                            srp_erp_customerinvoiceextrachargedetails
                                        WHERE
                                            isTaxApplicable = 1
                                        GROUP BY
                                            invoiceAutoID
                                    ) genexchargistax ON (
                                        `genexchargistax`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
                                    )
                                    LEFT JOIN (
                                        SELECT
                                            SUM(transactionAmount) AS transactionAmount,
                                            invoiceAutoID
                                        FROM
                                            srp_erp_customerinvoiceextrachargedetails
                                        GROUP BY
                                            invoiceAutoID
                                    ) genexcharg ON (
                                        `genexcharg`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
                                    )
                                    WHERE
                                        `companyID` = $companyID
                                    and srp_erp_customerinvoicemaster.invoiceAutoID= $invautoid ";
                        $totalValue = $this->db->query($r1)->row_array();

                        $retentionPercentage = 0;
                        $retensionTransactionAmount = '';
                        $retensionLocalAmount = '';
                        $retensionReportingAmount = '';
                        if($master_dt['invoiceType'] == 'Project')
                        {
                            $retentiondetail = $this->db->query("SELECT retensionPercentage, (SUM(srp_erp_customerinvoicedetails.transactionAmount) *(retensionPercentage/100))as Invoice_amount FROM
                                                                        `srp_erp_customerinvoicedetails` LEFT JOIN srp_erp_customerinvoicemaster invoicemater on srp_erp_customerinvoicedetails.invoiceAutoID = invoicemater.invoiceAutoID
                                                                        LEFT JOIN srp_erp_boq_header boqheader on boqheader.projectID = invoicemater.projectID where  invoicemater.invoiceAutoID = $invautoid  AND type ='Project'")->row_array();

                            $retentionPercentage =  $retentiondetail['retensionPercentage'];
                            $retensionTransactionAmount = (round($retentiondetail['Invoice_amount'],$totalValue['transactionCurrencyDecimalPlaces']));
                            $retensionLocalAmount =(round($retentiondetail['Invoice_amount'] / $totalValue['companyLocalExchangeRate'],$totalValue['companyLocalCurrencyDecimalPlaces']));
                            $retensionReportingAmount = (round($retentiondetail['Invoice_amount'] / $totalValue['companyLocalExchangeRate'],$totalValue['companyLocalCurrencyDecimalPlaces']));

                        }else if($master_dt['invoiceType'] == 'Operation')
                        {
                            $this->db->select('retentionPercentage,retensionTransactionAmount,retensionLocalAmount,retensionReportingAmount');
                            $this->db->where('invoiceAutoID', $invoiceAutoID);
                            $this->db->from('srp_erp_customerinvoicemaster');
                            $retntn_data = $this->db->get()->row_array();

                            if(!empty($retntn_data)){
                                $retentionPercentage =  $retntn_data['retentionPercentage'];
                                $retensionTransactionAmount = $retntn_data['retensionTransactionAmount'];
                                $retensionLocalAmount =$retntn_data['retensionLocalAmount'];
                                $retensionReportingAmount = $retntn_data['retensionReportingAmount'];
                            }
                        }

                        if($retensionEnabled == 1){
                            $this->db->select_sum('retensionValue');
                            $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                            $retensionTransactionAmount = $this->db->get('srp_erp_customerinvoicedetails')->row('retensionValue');
                            $retensionLocalAmount =  $retensionTransactionAmount;
                            $retensionReportingAmount =  $retensionTransactionAmount;
                        }
                        

                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user'],
                            'transactionAmount' => (round($totalValue['total_value'],$totalValue['transactionCurrencyDecimalPlaces'])),
                            'companyLocalAmount' => (round($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'],$totalValue['companyLocalCurrencyDecimalPlaces'])),
                            'companyReportingAmount' => (round($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'],$totalValue['companyReportingCurrencyDecimalPlaces'])),
                            'customerCurrencyAmount' => (round($totalValue['total_value'] / $totalValue['customerCurrencyExchangeRate'],$totalValue['customerCurrencyDecimalPlaces'])),
                            'retentionPercentage' =>$retentionPercentage,
                            'retensionTransactionAmount' =>  $retensionTransactionAmount,
                            'retensionLocalAmount' =>  $retensionLocalAmount,
                            'retensionReportingAmount' => $retensionReportingAmount,
                            'totalRetension' => $retensionTransactionAmount,
                        );

                        $this->db->where('invoiceAutoID', $invoiceAutoID);
                        $this->db->update('srp_erp_customerinvoicemaster', $data);
                        if (!empty($t_arr)) {
                            $this->db->update_batch('srp_erp_customerinvoicetaxdetails', $t_arr, 'taxDetailAutoID');
                        }
                        
                        if($wacRecalculationEnableYN == 0 && $master_dt['invoiceType'] != 'Manufacturing'){ 
                            reupdate_companylocalwac('srp_erp_customerinvoicedetails',$invoiceAutoID,'invoiceAutoID','companyLocalWacAmount');
                        }
                    } else {
                        return array('e', 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                        /*return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');*//*return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');*/
                        exit;
                    }
                } elseif($approvals_status == 3){
                    return array('w', 'There are no users exist to perform approval for this document.');
                    exit;
                } elseif($approvals_status == 4){
                    return array('e', 'There are no user group assigned to this confirmed document Segment.');
                    exit;
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                //$this->session->set_flashdata('e', 'Supplier Invoice Detail : ' . $data['GLDescription']. '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                /* return array('error' => 0, 'message' => 'Supplier Invoice Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());*/
                return array('e', 'Supplier Invoice Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
                //return array('status' => false);
            } else {
                $autoApproval= get_document_auto_approval('CINV');

                $updatedBatchNumberArray=[];

                if($itemBatchPolicy==1){

                    $this->db->select('*');
                    $this->db->where('invoiceAutoID', $invoiceAutoID);
                    $this->db->from('srp_erp_customerinvoicedetails');
                    $invoice_results = $this->db->get()->result_array();

                    $updatedBatchNumberArray=update_item_batch_number_details($invoice_results);

                }

                if($autoApproval==0) {
                    $result = $this->save_invoice_approval(0, $master_data['invoiceAutoID'], 1, 'Auto Approved',0,$updatedBatchNumberArray);
                    if($result){

                        $this->db->trans_commit();
                        return array('s', 'Document confirmed successfully');
                    }
                }else{

                    $this->db->trans_commit();
                    return array('s', 'Document confirmed successfully');
                }
            }
        }
    }

    function validate_itemMasterSub($itemAutoID)
    {
        $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_customerinvoicemaster cinv
                    LEFT JOIN srp_erp_customerinvoicedetails cinvDetail ON cinv.invoiceAutoID = cinvDetail.invoiceAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = cinvDetail.invoiceDetailsAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = cinvDetail.itemAutoID
                    WHERE
                        cinv.invoiceAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
        $r1 = $this->db->query($query1)->row_array();

        $query2 = "SELECT
                        SUM(cinvDetail.requestedQty) AS totalQty
                    FROM
                        srp_erp_customerinvoicemaster cinv
                    LEFT JOIN srp_erp_customerinvoicedetails cinvDetail ON cinv.invoiceAutoID = cinvDetail.invoiceAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = cinvDetail.itemAutoID
                    WHERE
                        cinv.invoiceAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";


        $r2 = $this->db->query($query2)->row_array();


        if (empty($r1) && empty($r2)) {
            $validate = true;
        } else if (empty($r1) || $r1['countAll'] == 0) {
            $validate = true;
        } else {
            if ($r1['countAll'] == $r2['totalQty']) {
                $validate = true;
            } else {
                $validate = false;
            }
        }
        return $validate;

    }

    function fetch_customer_con($master,$ref_no = null,$segment = null)
    {
        $customerID = $master['customerID'];
        $currencyID = $master['transactionCurrencyID'];
        $invType = $master['invoiceType'];

        $openContract = getPolicyValues('OCE', 'All');
        $where = '';
        if($openContract != 1) {
            $where = " AND srp_erp_contractdetails.invoicedYN = 0 ";
        }

        if($ref_no){
            $where .= " AND srp_erp_contractmaster.referenceNo = '$ref_no' ";
        }

        if($segment){

            $segment_arr = explode('|',$segment);

            if(isset($segment_arr[0])){
                $where .= " AND srp_erp_contractmaster.segmentID = '$segment_arr[0]' ";
            }
           
        }

        $currency = '';
        if($currencyID){
            $currency .= "AND transactionCurrencyID = '{$currencyID}'"; 
        }

        //$invoiceDate    = format_date($master['invoiceDate']);
        //$contractExp    = $master['contractExpDate'];
        $data = $this->db->query("SELECT srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.contractCode,srp_erp_contractmaster.segmentCode, srp_erp_contractmaster.referenceNo, srp_erp_contractmaster.contractDate,
                                                    srp_erp_contractdetails.requestedQty - IFNULL(SUM( recTB.requestedQty ) ,0) AS Total 
                                  FROM srp_erp_contractdetails 
                                  INNER JOIN srp_erp_contractmaster ON srp_erp_contractdetails.contractAutoID = srp_erp_contractmaster.contractAutoID 
                                  LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicedetails.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID 
                                    LEFT JOIN (
                                            SELECT
                                                contractDetailsAutoID,
                                                requestedQty 
                                            FROM
                                                srp_erp_customerinvoicedetails 
                                                GROUP BY contractAutoID
                                             UNION ALL
                                            SELECT
                                                contractDetailsAutoID,
                                                deliveredQty AS requestedQty 
                                            FROM
                                                srp_erp_deliveryorderdetails 
                                                GROUP BY contractAutoID
                                            ) recTB ON recTB.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID  
                                  WHERE 
                                          `customerID` = '{$customerID}' 
                                          AND `contractType` = '{$invType}' 
                                          {$currency}
                                          AND `confirmedYN` = 1 
                                          AND `closedYN` = 0
                                          AND `approvedYN` = 1
                                          {$where}
                                  GROUP BY srp_erp_contractmaster.contractCode")->result_array();
        //AND '{$invoiceDate}' BETWEEN contractDate AND contractExpDate

        return $data;
    }

    function fetch_contract_job($contractAutoID){

        $data = $this->db->query(
            "SELECT * 
            FROM srp_erp_jobsmaster
            WHERE srp_erp_jobsmaster.contract_po_id = '{$contractAutoID}' AND `confirmed` = 1"
        )->result_array();


        return $data;

    }

    function get_job_item_details(){
        
        $job_id = trim($this->input->post('job_id') ?? '');

        $this->db->where('job_id',$job_id);
        $data = $this->db->from('srp_erp_job_itemdetail')->get()->result_array();

        return $data;
    }

    function fetch_con_detail_table()
    {
        $companyID = current_companyID();
        $contract_id = trim($this->input->post('contractAutoID') ?? '');
        $openContract = getPolicyValues('OCE', 'All');
        $where = '';

        if($openContract != 1) {
            $where = " AND conDet.invoicedYN = 0 ";
        }
        
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'items.itemSystemCode';
        $item_code_alias = 'items.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'items.seconeryItemCode as itemSystemCode';
        }
        
        $data['detail'] = $this->db->query("SELECT conDet.*,cmaster.isBackToBack,IFNULL(taxAmount, 0)/conDet.requestedQty as taxAmount, ROUND((IFNULL( conDet.requestedQty, 0) - IFNULL( SUM( recTB.requestedQty ), 0)),4 )  AS balance,TRIM(TRAILING 0 FROM((ROUND((SUM( recTB.requestedQty )),4)))) AS receivedQty, items.seconeryItemCode AS itemSecondaryCode,items.partNo, items.mainCategory as invmaincat,IFNULL(srp_erp_taxcalculationformulamaster.Description,'-') as Description,IFNULL(conDet.taxCalculationformulaID,0) as taxCalculationformulaID,$item_code_alias
                            FROM srp_erp_contractdetails conDet
                            LEFT JOIN srp_erp_contractmaster cmaster ON cmaster.contractAutoID = conDet.contractAutoID
                            LEFT JOIN srp_erp_itemmaster items ON items.itemAutoID = conDet.itemAutoID
                            LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID  = conDet.taxCalculationformulaID
                            LEFT JOIN (
                                SELECT contractDetailsAutoID, requestedQty  FROM srp_erp_customerinvoicedetails WHERE contractAutoID = {$contract_id}
                                UNION ALL
                                SELECT contractDetailsAutoID, deliveredQty as requestedQty FROM srp_erp_deliveryorderdetails WHERE contractAutoID = {$contract_id}
                            ) recTB ON recTB.contractDetailsAutoID = conDet.contractDetailsAutoID
                            WHERE conDet.contractAutoID = '{$contract_id}' {$where}
                            GROUP BY contractDetailsAutoID")->result_array();


        $this->db->select("wareHouseCode,wareHouseDescription,companyCode,wareHouseAutoID,wareHouseLocation");
        $this->db->from('srp_erp_warehousemaster');
        $this->db->where('companyID', $companyID);
        $this->db->where('isActive', 1);
        $data['ware_house'] = $this->db->get()->result_array();

        $data['tax_master'] = all_tax_drop(1, 0);
        return $data;
    }

    function fetch_billing_detail(){
        
        $billing_id = trim($this->input->post('billing_id') ?? '');
        $companyID = current_companyID();

        $data['detail'] = $this->db->query("
            SELECT *
            FROM srp_erp_job_billing_detail as billing_detail
            LEFT JOIN srp_erp_contractdetails as contract_details ON billing_detail.price_id = contract_details.contractDetailsAutoID
            WHERE billing_detail.billing_header = '$billing_id' AND billing_detail.companyID = '$companyID'
        ")->result_array();

        return $data;

    }   

    function add_job_based_items(){
        $item_id = $this->input->post('item_id');
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $billing_id = trim($this->input->post('billing_id') ?? '');

        if(count($item_id) == 0){
            $this->session->set_flashdata('e', 'Nothing to Pull to the Document');
            return false;
        }

        foreach($item_id as $item){

            //get item detail record
            $this->db->where('id',$item);
            $job_item_record = $this->db->from('srp_erp_job_itemdetail')->get()->row_array();

            //get job master detail
            $this->db->where('id',$job_item_record['job_id']);
            $job_master_record = $this->db->from('srp_erp_jobsmaster')->get()->row_array();

            //get contract details
            $this->db->where('contractAutoID',$job_master_record['contract_po_id']);
            $job_contract_record = $this->db->from('srp_erp_contractmaster')->get()->row_array();

            //check pull customer invoices
            $this->db->where('srp_erp_customerinvoicedetails.job_detail_id',$item);
            $this->db->from('srp_erp_customerinvoicedetails as cd');
            $this->db->join('srp_erp_customerinvoicemaster as ci','cd.invoiceAutoID = ci.invoiceAutoID','left');
            $job_already_pulled = $this->db->from('srp_erp_customerinvoicedetails')->get()->row_array();

           

            if($job_item_record){

                $this->db->where('itemAutoID',$job_item_record['itemAutoID']);
                $item_detail = $this->db->from('srp_erp_itemmaster')->get()->row_array();

                if($job_already_pulled){
                    $this->session->set_flashdata('w', $item_detail['itemSystemCode']." already pulled to the invoice ".$job_already_pulled['invoiceCode']);
                    continue;
                }
                
                $data = array();

                $data['invoiceAutoID'] = $invoiceAutoID;
                $data['type'] = 'Item';
                $data['itemSystemCode'] = $item_detail['itemSystemCode'];
                $data['itemAutoID'] = $item_detail['itemAutoID'];
                $data['itemDescription'] = $item_detail['itemDescription'];
                $data['itemCategory'] = $item_detail['mainCategory'];
                $data['expenseGLAutoID'] = $item_detail['costGLAutoID'];
                $data['expenseGLCode'] = $item_detail['costGLCode'];
                $data['expenseSystemGLCode'] = $item_detail['costSystemGLCode'];
                $data['expenseGLDescription'] = $item_detail['costDescription'];
                $data['expenseGLType'] = $item_detail['costType'];
                $data['revenueGLAutoID'] = $item_detail['revanueGLAutoID'];
                $data['revenueGLCode'] = $item_detail['revanueGLCode'];
                $data['revenueSystemGLCode'] = $item_detail['revanueSystemGLCode'];
                $data['revenueGLDescription'] = $item_detail['revanueDescription'];
                $data['revenueGLType'] = $item_detail['revanueType'];
                $data['assetGLAutoID'] = $item_detail['assteGLAutoID'];
                $data['assetGLCode'] = $item_detail['assteGLCode'];
                $data['assetSystemGLCode'] = $item_detail['assteSystemGLCode'];
                $data['assetGLDescription'] = $item_detail['assteDescription'];
                $data['assetGLType'] = $item_detail['assteType'];
                $data['companyLocalWacAmount'] = $item_detail['companyLocalWacAmount'];

                $data['unitOfMeasure'] = $job_item_record['uomCode'];
                $data['unitOfMeasureID'] = $job_item_record['uomID'];
                $data['defaultUOM'] = $item_detail['defaultUnitOfMeasure'];
                $data['defaultUOMID'] = $item_detail['defaultUnitOfMeasureID'];
                $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
                $data['requestedQty'] = $job_item_record['qty'];

                $data['discountAmount'] = round($job_item_record['discount'], 2) ;
                $amountafterdiscount = $job_item_record['value'];
                $data['unittransactionAmount'] = round($amountafterdiscount , 2);
                $data['taxPercentage'] = 0;
                $taxAmount = ($data['taxPercentage'] / 100) * $amountafterdiscount;
                $data['taxAmount'] = round($taxAmount, 2);
                $totalAfterTax = $data['taxAmount'] * $data['requestedQty'];
                $data['totalAfterTax'] = round($totalAfterTax, 2);
                $transactionAmount = $job_item_record['transactionAmount'];
                $data['transactionAmount'] = round($transactionAmount, 2);
                $companyLocalAmount = $data['transactionAmount'] / 1;
                $data['companyLocalAmount'] = round($companyLocalAmount, 2);
                $companyReportingAmount = $data['transactionAmount'] / 1;
                $data['companyReportingAmount'] = round($companyReportingAmount, 2);
                $customerAmount = $data['transactionAmount'] / 1;
                $data['customerAmount'] = round($customerAmount,2);
                $data['comment'] = $job_item_record['comment'];

                $data['segmentID'] = $job_contract_record['segmentID'];
                $data['segmentCode'] = $job_contract_record['segmentCode'];

                $data['job_id'] = $job_master_record['id'];
                $data['job_code'] = $job_master_record['job_code'];
                $data['job_detail_id'] = $item;

                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

                $this->db->insert('srp_erp_customerinvoicedetails', $data);
                $last_id = $this->db->insert_id();

                $this->session->set_flashdata('s', $item_detail['itemSystemCode']."Added to the invoice");

            }

        }

        $this->session->set_flashdata('s', "Completed the process Successfully");
        return True;

    }

    function add_job_based_billing_items(){

        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $billing_id = trim($this->input->post('billing_id') ?? '');
        $company_id = $this->common_data['company_data']['company_id'];

        $billing_details = $this->db->query("
            SELECT billing_detail.*,contract_details.*,billing_detail.id as detail_id,contract_master.segmentID,contract_master.segmentCode,job.job_code
            FROM srp_erp_job_billing_detail as billing_detail
            LEFT JOIN srp_erp_contractdetails as contract_details ON billing_detail.price_id = contract_details.contractDetailsAutoID
            LEFT JOIN srp_erp_contractmaster as contract_master ON contract_details.contractAutoID = contract_master.contractAutoID
            LEFT JOIN srp_erp_jobsmaster as job ON billing_detail.job_id = job.id
            WHERE billing_detail.billing_header = '$billing_id' AND billing_detail.companyID = '$company_id'
        ")->result_array();

        $this->db->trans_start();

        foreach($billing_details as $details){

                $data = array();

                $data['invoiceAutoID'] = $invoiceAutoID;
                $data['type'] = 'Item';
                $data['itemSystemCode'] = $details['itemReferenceNo'];
                $data['wareHouseLocation'] = '';
                $data['remarks'] = '';
                // $data['itemSecondaryCode'] = '';
                

                // $data['itemAutoID'] = $item_detail['itemAutoID'];
                $data['itemDescription'] = $details['typeItemName'];
                $data['itemCategory'] = $details['mainCategoryID'];

                $costGL_details = fetch_gl_account_desc($details['costGLAutoID']);

                $data['expenseGLAutoID'] = $details['costGLAutoID'];
                $data['expenseGLCode'] = $costGL_details['GLSecondaryCode'];
                $data['expenseSystemGLCode'] = $costGL_details['systemAccountCode'];
                $data['expenseGLDescription'] = $costGL_details['GLDescription'];
                $data['expenseGLType'] = $costGL_details['subCategory'];

                $revenueGL_details = fetch_gl_account_desc($details['revanueGLAutoID']);

                $data['revenueGLAutoID'] = $details['revanueGLAutoID'];
                $data['revenueGLCode'] = $revenueGL_details['GLSecondaryCode'];
                $data['revenueSystemGLCode'] = $revenueGL_details['systemAccountCode'];
                $data['revenueGLDescription'] = $revenueGL_details['GLDescription'];
                $data['revenueGLType'] = $revenueGL_details['subCategory'];
                
                // $data['assetGLAutoID'] = $item_detail['assteGLAutoID'];
                // $data['assetGLCode'] = $item_detail['assteGLCode'];
                // $data['assetSystemGLCode'] = $item_detail['assteSystemGLCode'];
                // $data['assetGLDescription'] = $item_detail['assteDescription'];
                // $data['assetGLType'] = $item_detail['assteType'];
                // $data['companyLocalWacAmount'] = $item_detail['companyLocalWacAmount'];

                $data['unitOfMeasure'] = $details['unitOfMeasure'];
                $data['unitOfMeasureID'] = $details['unitOfMeasureID'];
                $data['defaultUOM'] = $details['defaultUOM'];
                $data['defaultUOMID'] = $details['defaultUOMID'];
                $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
                $data['requestedQty'] = $details['qty'];

                $data['discountAmount'] = round($details['discountTotal'], 2) ;
                $amountafterdiscount = $details['transactionAmount'];
                $data['unittransactionAmount'] = round($amountafterdiscount , 2);
                $data['taxPercentage'] = 0;
              //  $taxAmount = ($data['taxPercentage'] / 100) * $amountafterdiscount;
                $data['taxAmount'] = round($details['taxAmount'], 2);
                $totalAfterTax = $data['taxAmount'] * $data['requestedQty'];
                $data['totalAfterTax'] = round($totalAfterTax, 2);
                $transactionAmount = $details['transactionAmount'];
                $data['transactionAmount'] = round($transactionAmount, 2);
                $companyLocalAmount = $details['transactionAmount'] / 1;
                $data['companyLocalAmount'] = round($companyLocalAmount, 2);
                $companyReportingAmount = $details['transactionAmount'] / 1;
                $data['companyReportingAmount'] = round($companyReportingAmount, 2);
                $customerAmount = $details['transactionAmount'] / 1;
                $data['customerAmount'] = round($customerAmount,2);
                $data['comment'] = $details['comment'];

                $data['segmentID'] = $details['segmentID'];
                $data['segmentCode'] = $details['segmentCode'];

                $data['job_id'] = $details['job_id'];
                $data['job_code'] = $details['job_code'];
                $data['job_detail_id'] = $details['detail_id'];

                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

                $this->db->insert('srp_erp_customerinvoicedetails', $data);
                $last_id = $this->db->insert_id();

                //update billing records
                $billing_arr = array();
                $billing_arr['invoice_id'] = $invoiceAutoID;

                $this->db->where('id',$details['billing_header'])->update('srp_erp_job_billing', $billing_arr);
                $this->db->where('id',$details['detail_id'])->update('srp_erp_job_billing_detail', $billing_arr);

                $this->session->set_flashdata('s', $details['itemSystemCode']."Added to the invoice");


        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('s', "Invoice Details Save Failed ' . $this->db->_error_message()");
            return True;
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', "Completed the process Successfully");
            return True;
        }
    

    }

    function save_con_base_items()
    {
        $this->db->trans_start();
        $items_arr = array();

        $this->db->select('srp_erp_contractdetails.*,sum(srp_erp_customerinvoicedetails.requestedQty) AS receivedQty,srp_erp_contractmaster.contractCode,srp_erp_contractmaster.referenceNo');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where_in('srp_erp_contractdetails.contractDetailsAutoID', $this->input->post('DetailsID'));
        $this->db->join('srp_erp_contractmaster', 'srp_erp_contractmaster.contractAutoID = srp_erp_contractdetails.contractAutoID');
        $this->db->join('srp_erp_customerinvoicedetails', 'srp_erp_customerinvoicedetails.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID', 'left');
        $this->db->group_by("contractDetailsAutoID");
        $query = $this->db->get()->result_array();

        

        $this->db->select('COUNT(contractAutoID) as documentCount');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->join('srp_erp_customerreceiptmaster', 'srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId');
        $this->db->where_in('contractAutoID', array_column($query, 'contractAutoID'));
        $this->db->where('srp_erp_customerreceiptdetail.companyID', current_companyID());
        $this->db->where('approvedYN !=', 1);
        $documentCount = $this->db->get()->row('documentCount');
        if($documentCount > 0) {
            return array('e', 'Approve all Advances for this Quotation and try again!');
        }

        $this->db->select('invoiceDate, invoiceCode,referenceNo,customerID,customerSystemCode,customerName,customerCurrency,customerCurrencyID,customerCurrencyDecimalPlaces,transactionCurrencyID, companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,segmentID,segmentCode,transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $master = $this->db->get()->row_array();

        $qty = $this->input->post('qty');
        $amount = $this->input->post('amount');
        $discount = $this->input->post('discount');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $whrehouse = $this->input->post('whrehouse');
        $tex_id = $this->input->post('tex_id');
        $tex_percntage = $this->input->post('tex_percntage');
        $remarks = $this->input->post('remarks');
        $taxCalculationFormulaID = $this->input->post('taxCalculationFormulaID');
        $contract_reference = array();

        for ($i = 0; $i < count($query); $i++) {
            $discount_percentage = ($discount[$i] / $amount[$i])*100;
            $this->db->select('contractAutoID');
            $this->db->from('srp_erp_customerinvoicedetails');
            $this->db->where('contractAutoID', $query[$i]['contractAutoID']);
            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
            $order_detail = $this->db->get()->result_array();
            $item_data = fetch_item_data($query[$i]['itemAutoID']);
            $wareHouse_arr = explode('|', $whrehouse[$i]);

            if (isset($tex_id[$i])) {
                /*$this->db->select('*');
                $this->db->where('taxMasterAutoID', $tex_id[$i]);
                $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();*/

                /*$this->db->select('*');
                $this->db->where('supplierSystemCode', $tax_master['supplierSystemCode']);
                $Supplier_master = $this->db->get('srp_erp_suppliermaster')->row_array();*/

                $this->db->select('srp_erp_taxmaster.*,srp_erp_chartofaccounts.GLAutoID as liabilityAutoID,srp_erp_chartofaccounts.systemAccountCode as liabilitySystemGLCode,srp_erp_chartofaccounts.GLSecondaryCode as liabilityGLAccount,srp_erp_chartofaccounts.GLDescription as liabilityDescription,srp_erp_chartofaccounts.CategoryTypeDescription as liabilityType,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.DecimalPlaces');
                $this->db->where('taxMasterAutoID', $tex_id[$i]);
                $this->db->from('srp_erp_taxmaster');
                $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.supplierGLAutoID');
                $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_taxmaster.supplierCurrencyID');
                $tax_master = $this->db->get()->row_array();
            }
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
            $serviceitm= $this->db->get()->row_array();

            if (!empty($order_detail) && $serviceitm['mainCategory']=="Inventory") {
                $this->session->set_flashdata('w', 'Invoice Detail : ' . trim($this->input->post('itemCode') ?? '') . '  already exists.');
                $this->db->trans_rollback();
                return array('w', 'Invoice Detail : ' . trim($this->input->post('itemCode') ?? '') . '  already exists.');
            }
            else {
                $data[$i]['type'] = 'Item';
                $data[$i]['contractAutoID'] = $query[$i]['contractAutoID'];
                $data[$i]['taxCalculationFormulaID'] = $taxCalculationFormulaID[$i];
                $data[$i]['contractCode'] = $query[$i]['contractCode'];
                $data[$i]['contractDetailsAutoID'] = $query[$i]['contractDetailsAutoID'];
                $data[$i]['invoiceAutoID'] = trim($this->input->post('invoiceAutoID') ?? '');
                $data[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
                $data[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                $data[$i]['itemDescription'] = $query[$i]['itemDescription'];
                $data[$i]['defaultUOM'] = $query[$i]['defaultUOM'];
                $data[$i]['defaultUOMID'] = $query[$i]['defaultUOMID'];
                $data[$i]['unitOfMeasure'] = $query[$i]['unitOfMeasure'];
                $data[$i]['unitOfMeasureID'] = $query[$i]['unitOfMeasureID'];
                $data[$i]['conversionRateUOM'] = $query[$i]['conversionRateUOM'];
                $data[$i]['contractQty'] = $query[$i]['requestedQty'];
                $data[$i]['contractAmount'] = $query[$i]['unittransactionAmount'];
                $data[$i]['comment'] = $query[$i]['comment'];
                $data[$i]['requestedQty'] = $qty[$i];
                $data[$i]['unittransactionAmount'] = $amount[$i];
                $data[$i]['discountAmount'] = $discount[$i];
                $data[$i]['discountPercentage'] = $discount_percentage;
                $data[$i]['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
                $data[$i]['itemCategory'] = trim($item_data['mainCategory'] ?? '');
                $data[$i]['segmentID'] = $master['segmentID'];
                $data[$i]['segmentCode'] = $master['segmentCode'];
                $data[$i]['expenseGLAutoID'] = $item_data['costGLAutoID'];
                $data[$i]['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
                $data[$i]['expenseGLCode'] = $item_data['costGLCode'];
                $data[$i]['expenseGLDescription'] = $item_data['costDescription'];
                $data[$i]['expenseGLType'] = $item_data['costType'];
                $data[$i]['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
                $data[$i]['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
                $data[$i]['revenueGLCode'] = $item_data['revanueGLCode'];
                $data[$i]['revenueGLDescription'] = $item_data['revanueDescription'];
                $data[$i]['revenueGLType'] = $item_data['revanueType'];
                $data[$i]['assetGLAutoID'] = $item_data['assteGLAutoID'];
                $data[$i]['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data[$i]['assetGLCode'] = $item_data['assteGLCode'];
                $data[$i]['assetGLDescription'] = $item_data['assteDescription'];
                $data[$i]['assetGLType'] = $item_data['assteType'];
                $data[$i]['comment'] = $query[$i]['comment'];
                $data[$i]['remarks'] = $remarks[$i];
                $data[$i]['wareHouseAutoID'] = $wareHouseAutoID[$i];//$master['wareHouseAutoID'];
                $data[$i]['wareHouseCode'] = $wareHouse_arr[0]; //$master['wareHouseCode'];
                $data[$i]['wareHouseLocation'] = $wareHouse_arr[1]; //$master['wareHouseLocation'];
                //$data[$i]['wareHouseDescription'] = $wareHouse_arr[2]; //$master['wareHouseDescription'];
                $data[$i]['taxPercentage'] = $tex_percntage[$i];
                $tax_amount = ($data[$i]['taxPercentage'] / 100) * ($data[$i]['unittransactionAmount'] - $data[$i]['discountAmount']);
                $data[$i]['taxAmount'] =round($tax_amount, $master['transactionCurrencyDecimalPlaces']);
                $totalAfterTax  = ($data[$i]['taxAmount'] * $data[$i]['requestedQty']);
                $data[$i]['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
                $transactionAmount = ($data[$i]['requestedQty'] * ($data[$i]['unittransactionAmount'] - $discount[$i] )) + $data[$i]['totalAfterTax'];
                $data[$i]['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                $companyLocalAmount = $data[$i]['transactionAmount'] / $master['companyLocalExchangeRate'];
                $data[$i]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $companyReportingAmount = $data[$i]['transactionAmount'] / $master['companyReportingExchangeRate'];
                $data[$i]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $customerAmount = $data[$i]['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                $data[$i]['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                $total_amount = ($data[$i]['requestedQty'] * ($data[$i]['unittransactionAmount'] - $data[$i]['discountAmount']));
                if (!empty($tax_master)) {
                    $data[$i]['taxMasterAutoID'] = $tax_master['taxMasterAutoID'];
                    $data[$i]['taxDescription'] = $tax_master['taxDescription'];
                    $data[$i]['taxShortCode'] = $tax_master['taxShortCode'];
                    $data[$i]['taxSupplierAutoID'] = $tax_master['supplierAutoID'];
                    $data[$i]['taxSupplierSystemCode'] = $tax_master['supplierSystemCode'];
                    $data[$i]['taxSupplierName'] = $tax_master['supplierName'];
                    $data[$i]['taxSupplierCurrencyID'] = $tax_master['supplierCurrencyID'];
                    $data[$i]['taxSupplierCurrency'] = $tax_master['CurrencyCode'];
                    $data[$i]['taxSupplierCurrencyDecimalPlaces'] = $tax_master['DecimalPlaces'];
                    $data[$i]['taxSupplierliabilityAutoID'] = $tax_master['liabilityAutoID'];
                    $data[$i]['taxSupplierliabilitySystemGLCode'] = $tax_master['liabilitySystemGLCode'];
                    $data[$i]['taxSupplierliabilityGLAccount'] = $tax_master['liabilityGLAccount'];
                    $data[$i]['taxSupplierliabilityDescription'] = $tax_master['liabilityDescription'];
                    $data[$i]['taxSupplierliabilityType'] = $tax_master['liabilityType'];
                    $supplierCurrency = currency_conversion($master['transactionCurrency'], $data[$i]['taxSupplierCurrency']);
                    $data[$i]['taxSupplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                    $data[$i]['taxSupplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                    $data[$i]['taxSupplierCurrencyAmount'] = ($data[$i]['transactionAmount'] / $data[$i]['taxSupplierCurrencyExchangeRate']);
                } else {
                    $data[$i]['taxMasterAutoID'] = null;
                    $data[$i]['taxDescription'] = null;
                    $data[$i]['taxShortCode'] = null;
                    $data[$i]['taxSupplierAutoID'] = null;
                    $data[$i]['taxSupplierSystemCode'] = null;
                    $data[$i]['taxSupplierName'] = null;
                    $data[$i]['taxSupplierCurrencyID'] = null;
                    $data[$i]['taxSupplierCurrency'] = null;
                    $data[$i]['taxSupplierCurrencyDecimalPlaces'] = null;
                    $data[$i]['taxSupplierliabilityAutoID'] = null;
                    $data[$i]['taxSupplierliabilitySystemGLCode'] = null;
                    $data[$i]['taxSupplierliabilityGLAccount'] = null;
                    $data[$i]['taxSupplierliabilityDescription'] = null;
                    $data[$i]['taxSupplierliabilityType'] = null;

                    $data[$i]['taxSupplierCurrencyExchangeRate'] = 1;
                    $data[$i]['taxSupplierCurrencyDecimalPlaces'] = 2;
                    $data[$i]['taxSupplierCurrencyAmount'] = 0;
                }
                $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
                $data[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                $data[$i]['createdUserName'] = $this->common_data['current_user'];
                $data[$i]['createdDateTime'] = $this->common_data['current_date'];

                // $con_data[$i]['contractDetailsAutoID']  = $query[$i]['contractDetailsAutoID'];
                // $con_data[$i]['invoicedYN']         = 0;
                // if ($query[$i]['requestedQty'] <= (floatval($qty[$i])+floatval($query[$i]['receivedQty']))) {
                //     $con_data[$i]['invoicedYN']         = 1;
                // }

                if(!empty($query[$i]['contractDetailsAutoID']))
                {
                    $compID = $this->common_data['company_data']['company_id'];
                    $contractedTotal = $this->db->query("SELECT (IFNULL(deliveredQty, 0) + IFNULL(invoiced.requestedQty, 0)) AS totalDeliveredQty, srp_erp_contractdetails.requestedQty 
                    FROM srp_erp_contractdetails
                        LEFT JOIN ( SELECT SUM( deliveredQty ) AS deliveredQty, contractDetailsAutoID FROM srp_erp_deliveryorderdetails GROUP BY contractDetailsAutoID ) delivered ON delivered.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID
                        LEFT JOIN ( SELECT SUM( requestedQty ) AS requestedQty, contractDetailsAutoID FROM srp_erp_customerinvoicedetails GROUP BY contractDetailsAutoID ) invoiced ON invoiced.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID 
                    WHERE companyID = {$compID} AND srp_erp_contractdetails.contractDetailsAutoID = {$query[$i]['contractDetailsAutoID']}")->row_array();

                    $deliveredTot = $contractedTotal['totalDeliveredQty'] + $qty[$i];
                    if ($deliveredTot >= $contractedTotal['requestedQty'])
                    {
                        $cont_data['invoicedYN'] = 1;
                        $this->db->where('contractDetailsAutoID', $query[$i]['contractDetailsAutoID']);
                        $this->db->update('srp_erp_contractdetails', $cont_data);
                    }
                    
                }
            }
        }


        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_customerinvoicedetails', $data);


            $res = update_customerinvoicemaster_reference(trim($this->input->post('invoiceAutoID') ?? ''));


            $companyID = current_companyID();
            $invoiceAutoID =  trim($this->input->post('invoiceAutoID') ?? '');

            $CINVTax = $this->db->query("SELECT
                                            customerID,
                                            customerCountryID,
                                            vatEligible,
                                            srp_erp_customerinvoicedetails.taxCalculationFormulaID,
                                            srp_erp_customerinvoicedetails.invoiceAutoID,
                                            invoiceDetailsAutoID,
                                            srp_erp_customerinvoicedetails.transactionAmount,
                                            (requestedQty * unittransactionAmount) AS totalAmount,
                                            IFNULL((requestedQty * discountAmount),0) as discountAmount,
                                            srp_erp_customerinvoicedetails.contractAutoID, contractDetailsAutoID, invoiceDetailsAutoID, 
                                            IFNULL(taxAmount,0) as taxAmount
                                        from 
                                            srp_erp_customerinvoicedetails
                                        JOIN srp_erp_customerinvoicemaster  ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
                                        LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID
                                        where 
                                            srp_erp_customerinvoicedetails.companyID = {$companyID} 
                                            AND srp_erp_customerinvoicedetails.invoiceAutoID  = {$invoiceAutoID}")->result_array();

            if(existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',trim($this->input->post('invoiceAutoID') ?? ''),'CINV','invoiceAutoID')== 1){
                if(!empty($CINVTax)){
                    foreach($CINVTax as $val){
                        if($val['taxCalculationFormulaID']!=0){
                            tax_calculation_vat(null,null,$val['taxCalculationFormulaID'],'invoiceAutoID',trim($this->input->post('invoiceAutoID') ?? ''),$val['totalAmount'],'CINV',$val['invoiceDetailsAutoID'],$val['discountAmount'],1);

                              /** Advance Matching */
                            $matchedAdvance = $this->db->query("SELECT isAdvance FROM srp_erp_taxledger WHERE documentDetailAutoID = {$val['invoiceDetailsAutoID']} AND documentID = 'CINV' AND isAdvance = 1")->row('isAdvance');
                            if(empty($matchedAdvance)) {
                                $advances = $this->db->query("SELECT
                                                                srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID,
                                                                srp_erp_customerreceiptmaster.receiptVoucherAutoId,
                                                                contractAutoID,
                                                                srp_erp_customerreceiptdetail.transactionAmount,
                                                                taxAmount,
                                                                RVcode,
                                                                RVdate,
                                                                IFNULL(matchedAmount, 0) AS matchedAmount,
                                                                (srp_erp_customerreceiptdetail.transactionAmount - IFNULL(matchedAmount, 0)) AS balanceAmount
                                                            FROM
                                                                srp_erp_customerreceiptdetail
                                                            JOIN srp_erp_customerreceiptmaster On srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
                                                            LEFT JOIN (SELECT SUM(transactionAmount) as matchedAmount, receiptVoucherDetailAutoID FROM srp_erp_rvadvancematchdetails GROUP BY receiptVoucherDetailAutoID) rvm ON rvm.receiptVoucherDetailAutoID = srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID
                                                            WHERE
                                                                contractAutoID = {$val['contractAutoID']}
                                                                AND type = 'Advance'
                                                                HAVING srp_erp_customerreceiptdetail.transactionAmount > IFNULL(matchedAmount, 0)")->result_array();

                                if(!empty($advances)) {
                                    $matchID = $this->db->query("SELECT matchID FROM srp_erp_rvadvancematch WHERE matchinvoiceAutoID = {$invoiceAutoID}")->row('matchID');
                                    if(empty($matchID)) {
                                        $data_matchMaster['documentID'] = 'RVM';
                                        $data_matchMaster['matchinvoiceAutoID'] = $invoiceAutoID;
                                        $data_matchMaster['matchDate'] = current_date();
                                        $data_matchMaster['Narration'] = '';
                                        $data_matchMaster['refNo'] = $master['invoiceCode'];
                                        $data_matchMaster['customerID'] = $master['customerID'];
                                        $data_matchMaster['customerSystemCode'] = $master['customerSystemCode'];
                                        $data_matchMaster['customerName'] = $master['customerName'];
                                        $data_matchMaster['customerCurrency'] = $master['customerCurrency'];
                                        $data_matchMaster['customerCurrencyID'] = $master['customerCurrencyID'];
                                        $data_matchMaster['customerCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                                        $data_matchMaster['transactionCurrencyID'] = $master['transactionCurrencyID'];
                                        $data_matchMaster['transactionCurrency'] = $master['transactionCurrency'];
                                        $data_matchMaster['transactionExchangeRate'] = 1;
                                        $data_matchMaster['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_matchMaster['transactionCurrencyID']);
                                        $data_matchMaster['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                                        $data_matchMaster['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                                        $default_currency = currency_conversionID($data_matchMaster['transactionCurrencyID'], $data_matchMaster['companyLocalCurrencyID']);
                                        $data_matchMaster['companyLocalExchangeRate'] = $default_currency['conversion'];
                                        $data_matchMaster['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                                        $data_matchMaster['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                                        $data_matchMaster['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                                        $reporting_currency = currency_conversionID($data_matchMaster['transactionCurrencyID'], $data_matchMaster['companyReportingCurrencyID']);
                                        $data_matchMaster['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                                        $data_matchMaster['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                                        $customer_currency = currency_conversionID($data_matchMaster['transactionCurrencyID'], $data_matchMaster['customerCurrencyID']);
                                        $data_matchMaster['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
                                        $data_matchMaster['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
                                        $data_matchMaster['companyCode'] = $this->common_data['company_data']['company_code'];
                                        $data_matchMaster['companyID'] = $this->common_data['company_data']['company_id'];
                                        $data_matchMaster['createdUserGroup'] = $this->common_data['user_group'];
                                        $data_matchMaster['createdPCID'] = $this->common_data['current_pc'];
                                        $data_matchMaster['createdUserID'] = $this->common_data['current_userID'];
                                        $data_matchMaster['createdUserName'] = $this->common_data['current_user'];
                                        $data_matchMaster['createdDateTime'] = $this->common_data['current_date'];
                                        $this->load->library('sequence');
                                        $data_matchMaster['matchSystemCode'] = $this->sequence->sequence_generator($data_matchMaster['documentID']);
                                        $data_matchMaster['confirmedYN'] = 1;
                                        $data_matchMaster['confirmedDate'] = $this->common_data['current_date'];
                                        $data_matchMaster['confirmedByEmpID'] = $this->common_data['current_userID'];
                                        $data_matchMaster['confirmedByName'] = $this->common_data['current_user'];
                                        $this->db->insert('srp_erp_rvadvancematch', $data_matchMaster);
                                        $matchID = $this->db->insert_id();
                                    }

                                    $balance_amount = $this->db->query("SELECT transactionAmount FROM srp_erp_customerinvoicedetails WHERE invoiceDetailsAutoID = {$val['invoiceDetailsAutoID']}")->row('transactionAmount');
                                    foreach ($advances as $advance) {
                                        $CINV_ledgerEntries = $this->db->query("SELECT
                                                                            *
                                                                        FROM
                                                                            srp_erp_taxledger
                                                                            JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                                                        WHERE
                                                                            taxCategory = 2
                                                                            AND documentID = 'CINV'
                                                                            AND documentDetailAutoID = {$val['invoiceDetailsAutoID']}")->row_array();

                                        if($balance_amount > 0)
                                        {
                                            $RV_ledgerEntries = $this->db->query("SELECT
                                                                                            *
                                                                                        FROM
                                                                                            srp_erp_taxledger
                                                                                            JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                                                                        WHERE
                                                                                            taxCategory = 2
                                                                                            AND documentID = 'RV'
                                                                                            AND documentDetailAutoID = {$advance['receiptVoucherDetailAutoID']}")->row_array();

                                            if($balance_amount > $advance['balanceAmount'])
                                            {
                                                /** entry for invoiceable_amount*/
                                                $dataleg['documentID'] = 'CINV';
                                                $dataleg['documentMasterAutoID'] = trim($this->input->post('invoiceAutoID') ?? '');
                                                $dataleg['documentDetailAutoID'] = $val['invoiceDetailsAutoID'];
                                                $dataleg['taxDetailAutoID'] = null;
                                                $dataleg['taxPercentage'] = 0;
                                                $dataleg['ismanuallychanged'] = 0;
                                                $dataleg['isAdvance'] = 1;
                                                $dataleg['isClaimable'] = $RV_ledgerEntries['isClaimable'];
                                                $dataleg['taxFormulaMasterID'] = $RV_ledgerEntries['taxFormulaMasterID'];
                                                $dataleg['taxFormulaDetailID'] = $RV_ledgerEntries['taxFormulaDetailID'];
                                                $dataleg['taxMasterID'] = $RV_ledgerEntries['taxMasterID'];
                                                $dataleg['amount'] = ($RV_ledgerEntries['amount'] / $advance['transactionAmount']) * $advance['balanceAmount'];
                                                $dataleg['formula'] = $RV_ledgerEntries['formula'];
                                                $dataleg['taxGlAutoID'] = $RV_ledgerEntries['transferGLAutoID'];
                                                $dataleg['transferGLAutoID'] = $RV_ledgerEntries['taxGlAutoID'];
                                                $dataleg['countryID'] = $val['customerCountryID'];
                                                $dataleg['partyVATEligibleYN'] = $val['vatEligible'];
                                                $dataleg['partyID'] = $val['customerID'];
                                                $dataleg['locationID'] = null;
                                                $dataleg['locationType'] = null;
                                                $dataleg['companyCode'] = $this->common_data['company_data']['company_code'];
                                                $dataleg['companyID'] = $this->common_data['company_data']['company_id'];
                                                $dataleg['createdUserGroup'] = $this->common_data['user_group'];
                                                $dataleg['createdPCID'] = $this->common_data['current_pc'];
                                                $dataleg['createdUserID'] = $this->common_data['current_userID'];
                                                $dataleg['createdUserName'] = $this->common_data['current_user'];
                                                $dataleg['createdDateTime'] = $this->common_data['current_date'];
                                                $this->db->insert('srp_erp_taxledger', $dataleg);

                                                $this->db->query("UPDATE 
                                                                            srp_erp_taxledger 
                                                                        SET
                                                                            amount = (amount - {$dataleg['amount']})
                                                                        WHERE 
                                                                            documentID = 'CINV'
                                                                            AND documentDetailAutoID = {$val['invoiceDetailsAutoID']}
                                                                            AND taxLedgerAutoID = {$CINV_ledgerEntries['taxLedgerAutoID']}");

                                                if($matchID) {
                                                    $match_data['matchID'] = $matchID;
                                                    $match_data['receiptVoucherAutoId'] = $advance['receiptVoucherAutoId'];
                                                    $match_data['receiptVoucherDetailAutoID'] = $advance['receiptVoucherDetailAutoID'];
                                                    $match_data['RVcode'] = $advance['RVcode'];
                                                    $match_data['RVdate'] = $advance['RVdate'];
                                                    $match_data['invoiceAutoID'] = trim($invoiceAutoID);
                                                    $match_data['invoiceDetailsAutoID'] = trim($val['invoiceDetailsAutoID'] ?? '');
                                                    $match_data['invoiceCode'] = trim($master['invoiceCode'] ?? '');
                                                    $match_data['invoiceDate'] = trim($master['invoiceDate'] ?? '');
                                                    $match_data['transactionAmount'] = $advance['balanceAmount'];
                                                    $match_data['transactionExchangeRate'] = 1;
                                                    $match_data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                                    $match_data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                                    $match_data['customerCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                                                    $match_data['companyLocalAmount'] = ($match_data['transactionAmount'] / $master['companyLocalExchangeRate']);
                                                    $match_data['companyReportingAmount'] = ($match_data['transactionAmount'] / $master['companyReportingExchangeRate']);
                                                    $match_data['customerCurrencyAmount'] = ($match_data['transactionAmount'] / $master['customerCurrencyExchangeRate']);
                                                    $match_data['modifiedPCID'] = $this->common_data['current_pc'];
                                                    $match_data['modifiedUserID'] = $this->common_data['current_userID'];
                                                    $match_data['modifiedUserName'] = $this->common_data['current_user'];
                                                    $match_data['modifiedDateTime'] = $this->common_data['current_date'];
                                                    $match_data['companyID'] = $this->common_data['company_data']['company_id'];
                                                    $match_data['companyCode'] = $this->common_data['company_data']['company_code'];
                                                    $match_data['createdUserGroup'] = $this->common_data['user_group'];
                                                    $match_data['createdPCID'] = $this->common_data['current_pc'];
                                                    $match_data['createdUserID'] = $this->common_data['current_userID'];
                                                    $match_data['createdUserName'] = $this->common_data['current_user'];
                                                    $match_data['createdDateTime'] = $this->common_data['current_date'];
                                                    $this->db->insert('srp_erp_rvadvancematchdetails', $match_data);

                                                    $this->load->model('Receipt_voucher_model');
                                                    $invoice_data = $this->Receipt_voucher_model->fetch_invoice($invoiceAutoID);
                                                    $amo['advanceMatchedTotal']         = $invoice_data['advanceMatchedTotal'] + $match_data['transactionAmount'];
                                                    $balanceAmount                      = $invoice_data['transactionAmount'] - ($invoice_data['creditNoteTotalAmount'] + $invoice_data['receiptTotalAmount'] + $invoice_data['advanceMatchedTotal'] + $match_data['transactionAmount']);
                                                    if ($balanceAmount <= 0) {
                                                        $amo['receiptInvoiceYN'] = 1;
                                                    }
                                                    $this->db->where('invoiceAutoID', $invoiceAutoID);
                                                    $this->db->update('srp_erp_customerinvoicemaster', $amo);
                                                }
                                            } else {
                                                /** entry for balance_amount*/
                                                $dataleg['documentID'] = 'CINV';
                                                $dataleg['documentMasterAutoID'] = trim($this->input->post('invoiceAutoID') ?? '');
                                                $dataleg['documentDetailAutoID'] = $val['invoiceDetailsAutoID'];
                                                $dataleg['taxDetailAutoID'] = null;
                                                $dataleg['taxPercentage'] = 0;
                                                $dataleg['ismanuallychanged'] = 0;
                                                $dataleg['isAdvance'] = 1;
                                                $dataleg['isClaimable'] = $RV_ledgerEntries['isClaimable'];
                                                $dataleg['taxFormulaMasterID'] = $RV_ledgerEntries['taxFormulaMasterID'];
                                                $dataleg['taxFormulaDetailID'] = $RV_ledgerEntries['taxFormulaDetailID'];
                                                $dataleg['taxMasterID'] = $RV_ledgerEntries['taxMasterID'];
                                                $dataleg['amount'] = ($RV_ledgerEntries['amount'] / $advance['transactionAmount']) * $balance_amount;
                                                $dataleg['formula'] = $RV_ledgerEntries['formula'];
                                                $dataleg['taxGlAutoID'] = $RV_ledgerEntries['transferGLAutoID'];
                                                $dataleg['transferGLAutoID'] = $RV_ledgerEntries['taxGlAutoID'];
                                                $dataleg['countryID'] = $val['customerCountryID'];
                                                $dataleg['partyVATEligibleYN'] = $val['vatEligible'];
                                                $dataleg['partyID'] = $val['customerID'];
                                                $dataleg['locationID'] = null;
                                                $dataleg['locationType'] = null;
                                                $dataleg['companyCode'] = $this->common_data['company_data']['company_code'];
                                                $dataleg['companyID'] = $this->common_data['company_data']['company_id'];
                                                $dataleg['createdUserGroup'] = $this->common_data['user_group'];
                                                $dataleg['createdPCID'] = $this->common_data['current_pc'];
                                                $dataleg['createdUserID'] = $this->common_data['current_userID'];
                                                $dataleg['createdUserName'] = $this->common_data['current_user'];
                                                $dataleg['createdDateTime'] = $this->common_data['current_date'];
                                                $this->db->insert('srp_erp_taxledger', $dataleg);

                                                $this->db->query("UPDATE
                                                                            srp_erp_taxledger
                                                                        SET
                                                                            amount = (amount - {$dataleg['amount']})
                                                                        WHERE
                                                                            documentID = 'CINV'
                                                                            AND documentDetailAutoID = {$val['invoiceDetailsAutoID']}
                                                                            AND taxLedgerAutoID = {$CINV_ledgerEntries['taxLedgerAutoID']}");

                                                if($matchID) {
                                                    $match_data['matchID'] = $matchID;
                                                    $match_data['receiptVoucherAutoId'] = $advance['receiptVoucherAutoId'];
                                                    $match_data['receiptVoucherDetailAutoID'] = $advance['receiptVoucherDetailAutoID'];
                                                    $match_data['RVcode'] = $advance['RVcode'];
                                                    $match_data['RVdate'] = $advance['RVdate'];
                                                    $match_data['invoiceAutoID'] = trim($invoiceAutoID);
                                                    $match_data['invoiceDetailsAutoID'] = trim($val['invoiceDetailsAutoID'] ?? '');
                                                    $match_data['invoiceCode'] = trim($master['invoiceCode'] ?? '');
                                                    $match_data['invoiceDate'] = trim($master['invoiceDate'] ?? '');
                                                    $match_data['transactionAmount'] = $balance_amount;
                                                    $match_data['transactionExchangeRate'] = 1;
                                                    $match_data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                                    $match_data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                                    $match_data['customerCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                                                    $match_data['companyLocalAmount'] = ($match_data['transactionAmount'] / $master['companyLocalExchangeRate']);
                                                    $match_data['companyReportingAmount'] = ($match_data['transactionAmount'] / $master['companyReportingExchangeRate']);
                                                    $match_data['customerCurrencyAmount'] = ($match_data['transactionAmount'] / $master['customerCurrencyExchangeRate']);
                                                    $match_data['modifiedPCID'] = $this->common_data['current_pc'];
                                                    $match_data['modifiedUserID'] = $this->common_data['current_userID'];
                                                    $match_data['modifiedUserName'] = $this->common_data['current_user'];
                                                    $match_data['modifiedDateTime'] = $this->common_data['current_date'];
                                                    $match_data['companyID'] = $this->common_data['company_data']['company_id'];
                                                    $match_data['companyCode'] = $this->common_data['company_data']['company_code'];
                                                    $match_data['createdUserGroup'] = $this->common_data['user_group'];
                                                    $match_data['createdPCID'] = $this->common_data['current_pc'];
                                                    $match_data['createdUserID'] = $this->common_data['current_userID'];
                                                    $match_data['createdUserName'] = $this->common_data['current_user'];
                                                    $match_data['createdDateTime'] = $this->common_data['current_date'];
                                                    $this->db->insert('srp_erp_rvadvancematchdetails', $match_data);

                                                    $this->load->model('Receipt_voucher_model');
                                                    $invoice_data = $this->Receipt_voucher_model->fetch_invoice($invoiceAutoID);
                                                    $amo['advanceMatchedTotal']         = $invoice_data['advanceMatchedTotal'] + $match_data['transactionAmount'];
                                                    $balanceAmount                      = $invoice_data['transactionAmount'] - ($invoice_data['creditNoteTotalAmount'] + $invoice_data['receiptTotalAmount'] + $invoice_data['advanceMatchedTotal'] + $match_data['transactionAmount']);
                                                    if ($balanceAmount <= 0) {
                                                        $amo['receiptInvoiceYN'] = 1;
                                                    }
                                                    $this->db->where('invoiceAutoID', $invoiceAutoID);
                                                    $this->db->update('srp_erp_customerinvoicemaster', $amo);
                                                }
                                            }
                                            $balance_amount = $balance_amount - $advance['balanceAmount'];
                                        }
                                    }
                                }
                            }
                            /** End of Advance Matching */
                        }
                    }
                }
            }

            //$this->db->update_batch('srp_erp_contractdetails', $con_data, 'contractDetailsAutoID');
            /** Added By : (SME-2299)*/
            $invoice_id = $this->input->post('invoiceAutoID');
            $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoice_id}")->row_array();
            if(!empty($rebate)) {
                $this->calculate_rebate_amount($invoice_id);
            }
            /** End (SME-2299)*/

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Details Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice ' . count($query) . ' Item Details Saved Successfully.');
            }
        }
        else {
            return array('e', 'There is no data to process');
        }
    }

    function save_invoice_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0,$isRetentionYN=0,$updatedBatchNumberArray=[])
    {
        $batchNumberPolicy = getPolicyValues('IB', 'All');

        $this->load->library('Approvals');
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        if($autoappLevel==1){
            $system_id = trim($this->input->post('invoiceAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        }else{
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['invoiceAutoID']=$system_id;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }
        $companyID = current_companyID();
        /* $sql = "SELECT srp_erp_itemmaster.itemSystemCode, srp_erp_itemmaster.itemDescription, 
                SUM( cus_inv.requestedQty / cus_inv.conversionRateUOM ) AS qty,IFNULL(ware_house.currentStock,0) as currentStock ,IFNULL( ware_house.currentStock,0) as availableStock,
                ( IFNULL(ware_house.currentStock,0)  - IFNULL( SUM( cus_inv.requestedQty / cus_inv.conversionRateUOM ),0) ) AS stock,
                ware_house.itemAutoID, cus_inv.wareHouseAutoID
                FROM srp_erp_customerinvoicedetails AS cus_inv
                LEFT JOIN ( 
                    SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID FROM srp_erp_itemledger WHERE companyID ={$companyID} GROUP BY wareHouseAutoID, itemAutoID 
                ) AS ware_house ON ware_house.itemAutoID = cus_inv.itemAutoID AND ware_house.wareHouseAutoID = cus_inv.wareHouseAutoID
                JOIN srp_erp_itemmaster ON cus_inv.itemAutoID = srp_erp_itemmaster.itemAutoID
                WHERE invoiceAutoID = '{$system_id}'
                AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' )
                GROUP BY itemAutoID
                HAVING stock < 0"; */

        $sql = "SELECT srp_erp_itemmaster.itemSystemCode, srp_erp_itemmaster.itemDescription, 
                SUM( cus_inv.requestedQty / cus_inv.conversionRateUOM ) AS qty,IFNULL(ware_house.currentStock,0) as currentStock ,
                IFNULL( ware_house.currentStock,0) as availableStock,
                ( IFNULL( ware_house.currentStock, 0 ) - ((IFNULL(pq.stock,0))+(IFNULL( SUM( cus_inv.requestedQty / cus_inv.conversionRateUOM ),0)))) AS stock,
                ware_house.itemAutoID, cus_inv.wareHouseAutoID
                FROM srp_erp_customerinvoicedetails AS cus_inv
                LEFT JOIN ( 
                    SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID FROM srp_erp_itemledger WHERE companyID ={$companyID} GROUP BY wareHouseAutoID, itemAutoID 
                ) AS ware_house ON ware_house.itemAutoID = cus_inv.itemAutoID AND ware_house.wareHouseAutoID = cus_inv.wareHouseAutoID
                JOIN srp_erp_itemmaster ON cus_inv.itemAutoID = srp_erp_itemmaster.itemAutoID
                LEFT JOIN (
                    SELECT
                        SUM( stock ) AS stock, t1.ItemAutoID, wareHouseAutoID 
                    FROM
                        (
                        SELECT
                            IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock, itemAutoID, srp_erp_stockadjustmentmaster.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_stockadjustmentmaster
                            LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
                        WHERE
                            companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock, itemAutoID, srp_erp_stockcountingmaster.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_stockcountingmaster
                            LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
                        WHERE
                            companyID = {$companyID}  AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock, itemAutoID,  srp_erp_itemissuemaster.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_itemissuemaster
                            LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
                        WHERE
                            srp_erp_itemissuemaster.companyID = {$companyID}  AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( requestedQty / conversionRateUOM ) AS stock, itemAutoID, srp_erp_customerreceiptdetail.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_customerreceiptmaster
                            LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                        WHERE
                            srp_erp_customerreceiptmaster.companyID = {$companyID}  AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( requestedQty / conversionRateUOM ) AS stock, itemAutoID, srp_erp_customerinvoicedetails.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_customerinvoicemaster
                            LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
                        WHERE
                            srp_erp_customerinvoicemaster.companyID = {$companyID} AND srp_erp_customerinvoicedetails.invoiceAutoID != '{$system_id}' 
                            AND approvedYN != 1  AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( deliveredQty / conversionRateUOM ) AS stock, itemAutoID, srp_erp_deliveryorderdetails.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_deliveryorder
                            LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
                        WHERE
                            srp_erp_deliveryorder.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( transfer_QTY / conversionRateUOM ) AS stock,
                            itemAutoID,
                            srp_erp_stocktransfermaster.from_wareHouseAutoID AS from_wareHouseAutoID 
                        FROM
                            srp_erp_stocktransfermaster
                            LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
                        WHERE
                            srp_erp_stocktransfermaster.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' 
                        ) t1 
                    GROUP BY
                        t1.wareHouseAutoID,
                        t1.ItemAutoID 
                    ) AS pq ON pq.ItemAutoID = cus_inv.itemAutoID 
                    AND pq.wareHouseAutoID = cus_inv.wareHouseAutoID 
                WHERE invoiceAutoID = '{$system_id}'  AND type != 'DO'
                AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' )
                GROUP BY itemAutoID
                HAVING stock < 0";
        $items_arr = $this->db->query($sql)->result_array();

        
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $system_id);
        $this->db->from('srp_erp_customerinvoicemaster');
        $master = $this->db->get()->row_array();

        if($wacRecalculationEnableYN == 0 && $master['invoiceType'] != 'Manufacturing'){ 
            reupdate_companylocalwac('srp_erp_customerinvoicedetails',$system_id,'invoiceAutoID','companyLocalWacAmount');
        }

        if($status!=1){
            $items_arr='';
        }
        if (!$items_arr) {
            if($autoappLevel==0){
                $approvals_status=1;
            }else{
                $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'CINV');
            }
            if ($approvals_status == 1 && $isRetentionYN==0) {
                $this->db->select('*');
                $this->db->where('invoiceAutoID', $system_id);
                $this->db->from('srp_erp_customerinvoicedetails');
                $invoice_detail = $this->db->get()->result_array();

                if($master['retentionPercentage']>0){
                    $this->create_retention_invoice($system_id);
                }

                if($master["invoiceType"] != "Manufacturing") {
                    if($master["invoiceType"] != "Insurance") {
                        for ($a = 0; $a < count($invoice_detail); $a++) {
                            if ($invoice_detail[$a]['type'] == 'Item') {
                                $item = fetch_item_data($invoice_detail[$a]['itemAutoID']);
                                if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory' or $item['mainCategory'] =='Service') {
                                    $itemAutoID = $invoice_detail[$a]['itemAutoID'];
                                    $qty = $invoice_detail[$a]['requestedQty'] / $invoice_detail[$a]['conversionRateUOM'];
                                    $wareHouseAutoID = $invoice_detail[$a]['wareHouseAutoID'];
                                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");

                                    $item_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                    $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                                    $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                                    $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                                    if (!empty($item_arr)) {
                                        $this->db->where('itemAutoID', trim($invoice_detail[$a]['itemAutoID']));
                                        $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                                    }
                                    $itemledger_arr[$a]['documentID'] = $master['documentID'];
                                    $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                                    $itemledger_arr[$a]['documentAutoID'] = $master['invoiceAutoID'];
                                    $itemledger_arr[$a]['documentSystemCode'] = $master['invoiceCode'];
                                    $itemledger_arr[$a]['documentDate'] = $master['invoiceDate'];
                                    $itemledger_arr[$a]['referenceNumber'] = $master['referenceNo'];
                                    $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                                    $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                                    $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                                    $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                                    $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                                    $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                                    $itemledger_arr[$a]['wareHouseAutoID'] = $invoice_detail[$a]['wareHouseAutoID'];
                                    $itemledger_arr[$a]['wareHouseCode'] = $invoice_detail[$a]['wareHouseCode'];
                                    $itemledger_arr[$a]['wareHouseLocation'] = $invoice_detail[$a]['wareHouseLocation'];
                                    $itemledger_arr[$a]['wareHouseDescription'] = $invoice_detail[$a]['wareHouseDescription'];
                                    $itemledger_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                    $itemledger_arr[$a]['itemSystemCode'] = $invoice_detail[$a]['itemSystemCode'];
                                    $itemledger_arr[$a]['itemDescription'] = $invoice_detail[$a]['itemDescription'];
                                    $itemledger_arr[$a]['SUOMID'] = $invoice_detail[$a]['SUOMID'];
                                    $itemledger_arr[$a]['SUOMQty'] = $invoice_detail[$a]['SUOMQty'];
                                    $itemledger_arr[$a]['defaultUOMID'] = $invoice_detail[$a]['defaultUOMID'];
                                    $itemledger_arr[$a]['defaultUOM'] = $invoice_detail[$a]['defaultUOM'];
                                    $itemledger_arr[$a]['transactionUOMID'] = $invoice_detail[$a]['unitOfMeasureID'];
                                    $itemledger_arr[$a]['transactionUOM'] = $invoice_detail[$a]['unitOfMeasure'];
                                    $itemledger_arr[$a]['transactionQTY'] = ($invoice_detail[$a]['requestedQty'] * -1);
                                    $itemledger_arr[$a]['convertionRate'] = $invoice_detail[$a]['conversionRateUOM'];
                                    $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                                    $itemledger_arr[$a]['PLGLAutoID'] = $item['costGLAutoID'];
                                    $itemledger_arr[$a]['PLSystemGLCode'] = $item['costSystemGLCode'];
                                    $itemledger_arr[$a]['PLGLCode'] = $item['costGLCode'];
                                    $itemledger_arr[$a]['PLDescription'] = $item['costDescription'];
                                    $itemledger_arr[$a]['PLType'] = $item['costType'];
                                    $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                                    $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                                    $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                                    $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                                    $itemledger_arr[$a]['BLType'] = $item['assteType'];
                                    $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                                    $ex_rate_wac = (1 / $master['companyLocalExchangeRate']);
                                    $itemledger_arr[$a]['transactionAmount'] = round((($invoice_detail[$a]['companyLocalWacAmount'] / $ex_rate_wac) * ($itemledger_arr[$a]['transactionQTY'] / $invoice_detail[$a]['conversionRateUOM'])), $itemledger_arr[$a]['transactionCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['salesPrice'] = (($invoice_detail[$a]['transactionAmount'] / ($itemledger_arr[$a]['transactionQTY'] / $invoice_detail[$a]['conversionRateUOM'])) * -1);
                                    $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                                    $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                                    $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                                    $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                                    $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                                    $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                    $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                                    $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                                    $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                                    $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                    $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                                    $itemledger_arr[$a]['partyCurrencyID'] = $master['customerCurrencyID'];
                                    $itemledger_arr[$a]['partyCurrency'] = $master['customerCurrency'];
                                    $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                                    $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                                    $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                                    $itemledger_arr[$a]['confirmedByName'] = $master['confirmedByName'];
                                    $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDate'];
                                    $itemledger_arr[$a]['approvedYN'] = $master['approvedYN'];
                                    $itemledger_arr[$a]['approvedDate'] = $master['approvedDate'];
                                    $itemledger_arr[$a]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                                    $itemledger_arr[$a]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                                    $itemledger_arr[$a]['segmentID'] = $master['segmentID'];
                                    $itemledger_arr[$a]['segmentCode'] = $master['segmentCode'];
                                    $itemledger_arr[$a]['companyID'] = $master['companyID'];
                                    $itemledger_arr[$a]['companyCode'] = $master['companyCode'];
                                    $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                                    $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                                    $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                                    $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                                    $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                                    $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                                    $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                                    $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                                    $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];
                                }
                            }
                        }
                        if (!empty($itemledger_arr)) {
                            $itemledger_arr = array_values($itemledger_arr);
                            //$this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                            if($batchNumberPolicy==1){

                                foreach($itemledger_arr  as $key1=>$ledger){
            
                                    if(count($updatedBatchNumberArray)>0){
                                        foreach($updatedBatchNumberArray as $bKey=>$batch){
                                            if($ledger['itemAutoID']==$batch['itemAutoID'] && $ledger['wareHouseAutoID']==$batch['wareHouseAutoID']){
                                                $itemledger_arr[]=[
                                                    'documentID'=>$ledger['documentID'],
                                                    'documentCode'=>$ledger['documentCode'],
                                                    'documentAutoID'=>$ledger['documentAutoID'],
                                                    'documentSystemCode'=>$ledger['documentSystemCode'],
                                                    'documentDate'=>$ledger['documentDate'],
                                                    'referenceNumber'=>$ledger['referenceNumber'],
                                                    'companyFinanceYearID'=>$ledger['companyFinanceYearID'],
                                                    'companyFinanceYear'=>$ledger['companyFinanceYear'],
                                                    'FYBegin'=>$ledger['FYBegin'],
                                                    'FYEnd'=>$ledger['FYEnd'],
                                                    'FYPeriodDateFrom'=>$ledger['FYPeriodDateFrom'],
                                                    'FYPeriodDateTo'=>$ledger['FYPeriodDateTo'],
                                                    'wareHouseAutoID'=>$ledger['wareHouseAutoID'],
                                                    'wareHouseCode'=>$ledger['wareHouseCode'],
                                                    'wareHouseLocation'=>$ledger['wareHouseLocation'],
                                                    'wareHouseDescription'=>$ledger['wareHouseDescription'],
                                                    'itemAutoID'=>$ledger['itemAutoID'],
                                                    'itemSystemCode'=>$ledger['itemSystemCode'],
                                                    'itemDescription'=>$ledger['itemDescription'],
                                                    'SUOMID'=>$ledger['SUOMID'],
                                                    'SUOMQty'=>$ledger['SUOMQty'],
                                                    'defaultUOMID'=>$ledger['defaultUOMID'],
                                                    'defaultUOM'=>$ledger['defaultUOM'],
                                                    'transactionUOM'=>$ledger['transactionUOM'],
                                                    'transactionUOMID'=>$ledger['transactionUOMID'],
                                                    'transactionQTY'=>$batch['qty'],
                                                    'batchNumber'=>$batch['batchNumber'],
                                                    'convertionRate'=>$ledger['convertionRate'],
                                                    'currentStock'=>$ledger['currentStock'],
                                                    'PLGLAutoID'=>$ledger['PLGLAutoID'],
                                                    'PLSystemGLCode'=>$ledger['PLSystemGLCode'],
                                                    'PLGLCode'=>$ledger['PLGLCode'],
                                                    'PLDescription'=>$ledger['PLDescription'],
                                                    'PLType'=>$ledger['PLType'],
                                                    'BLGLAutoID'=>$ledger['BLGLAutoID'],
                                                    'BLSystemGLCode'=>$ledger['BLSystemGLCode'],
                                                    'BLGLCode'=>$ledger['BLGLCode'],
                                                    'BLDescription'=>$ledger['BLDescription'],
                                                    'BLType'=>$ledger['BLType'],
                                                    'transactionAmount'=>$ledger['transactionAmount'],
                                                    'transactionCurrencyID'=>$ledger['transactionCurrencyID'],
                                                    'transactionCurrency'=>$ledger['transactionCurrency'],
                                                    'transactionExchangeRate'=>$ledger['transactionExchangeRate'],
                                                    'transactionCurrencyDecimalPlaces'=>$ledger['transactionCurrencyDecimalPlaces'],
                                                    'companyLocalCurrencyID'=>$ledger['companyLocalCurrencyID'],
                                                    'companyLocalCurrency'=>$ledger['companyLocalCurrency'],
                                                    'companyLocalExchangeRate'=>$ledger['companyLocalExchangeRate'],
                                                    'companyLocalCurrencyDecimalPlaces'=>$ledger['companyLocalCurrencyDecimalPlaces'],
                                                    'companyLocalAmount'=>$ledger['companyLocalAmount'],
                                                    'companyLocalWacAmount'=>$ledger['companyLocalWacAmount'],
                                                    'companyReportingCurrencyID'=>$ledger['companyReportingCurrencyID'],
                                                    'companyReportingCurrency'=>$ledger['companyReportingCurrency'],
                                                    'companyReportingExchangeRate'=>$ledger['companyReportingExchangeRate'],
                                                    'companyReportingCurrencyDecimalPlaces'=>$ledger['companyReportingCurrencyDecimalPlaces'],
                                                    'companyReportingAmount'=>$ledger['companyReportingAmount'],
                                                    'companyReportingWacAmount'=>$ledger['companyReportingWacAmount'],
                                                    'partyCurrencyID'=>$ledger['partyCurrencyID'],
                                                    'partyCurrency'=>$ledger['partyCurrency'],
                                                    'partyCurrencyExchangeRate'=>$ledger['partyCurrencyExchangeRate'],
                                                    'partyCurrencyDecimalPlaces'=>$ledger['partyCurrencyDecimalPlaces'],
                                                    'partyCurrencyAmount'=>$ledger['partyCurrencyAmount'],
                                                    'confirmedYN'=>$ledger['confirmedYN'],
                                                    'confirmedByEmpID'=>$ledger['confirmedByEmpID'],
                                                    'confirmedByName'=>$ledger['confirmedByName'],
                                                    'confirmedDate'=>$ledger['confirmedDate'],
                                                    'approvedYN'=>$ledger['approvedYN'],
                                                    'approvedDate'=>$ledger['approvedDate'],
                                                    'approvedbyEmpID'=>$ledger['approvedbyEmpID'],
                                                    'approvedbyEmpName'=>$ledger['approvedbyEmpName'],
                                                    'segmentID'=>$ledger['segmentID'],
                                                    'segmentCode'=>$ledger['segmentCode'],
                                                    'companyID'=>$ledger['companyID'],
                                                    'companyCode'=>$ledger['companyCode'],
                                                    'createdUserGroup'=>$ledger['createdUserGroup'],
                                                    'createdPCID'=>$ledger['createdPCID'],
                                                    'createdUserID'=>$ledger['createdUserID'],
                                                    'createdDateTime'=>$ledger['createdDateTime'],
                                                    'createdUserName'=>$ledger['createdUserName'],
                                                    'modifiedPCID'=>$ledger['modifiedPCID'],
                                                    'modifiedUserID'=>$ledger['modifiedUserID'],
                                                    'modifiedDateTime'=>$ledger['modifiedDateTime'],
                                                    'modifiedUserName'=>$ledger['modifiedUserName'],
            
                                                   
                                                ];
                                            }
                    
                                        }
                                    }
            
                                    unset($itemledger_arr[$key1]);
            
                                }
            
                                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                
                            }else{
                                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                            }
                        }
                    }
                    $this->load->model('Double_entry_model');
                    if($master["invoiceType"] != "Insurance") {
                        $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data($system_id, 'CINV');
                    }else{
                        $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data_insurance($system_id, 'CINV');
                    }

                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {

                        $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                        if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                            $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                        }

                        // if amount empty no need to add ledger records
                        if($amount == 0){
                            continue;
                        }

                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['acknowledgementDate'] = $master['acknowledgementDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
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
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                        $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                
                        $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                        $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
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

                }else{
                    for ($a = 0; $a < count($invoice_detail); $a++) {
                        if ($invoice_detail[$a]['type'] == 'Item') {
                            $item = fetch_item_data($invoice_detail[$a]['itemAutoID']);
                            if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                                $itemAutoID = $invoice_detail[$a]['itemAutoID'];
                                $qty = $invoice_detail[$a]['requestedQty'] / $invoice_detail[$a]['conversionRateUOM'];
                                $wareHouseAutoID = $invoice_detail[$a]['wareHouseAutoID'];

                                $item_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                                $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                                $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);

                            }
                        }
                    }

                    $this->load->model('Double_entry_model');
                    $double_entry = $this->Double_entry_model->fetch_double_entry_mfq_customer_invoice_data($system_id, 'CINV');
                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['acknowledgementDate'] = $master['acknowledgementDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
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
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
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
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
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

                }

                $this->db->select_sum('transactionAmount');
                $this->db->where('invoiceAutoID', $system_id);
                $total = $this->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');

                $data['approvedYN'] = $status;
                $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                $data['approvedbyEmpName'] = $this->common_data['current_user'];
                $data['approvedDate'] = $this->common_data['current_date'];

                $this->db->where('invoiceAutoID', $system_id);
                $this->db->update('srp_erp_customerinvoicemaster', $data);
                //$this->session->set_flashdata('s', 'Invoice Approval Successfully.');

                if($master["invoiceType"] == "Insurance") {
                    $sumsup = "SELECT (sum(transactionAmount)-sum(marginAmount)) as transactionAmount,
                                srp_erp_customerinvoicedetails.supplierAutoID as supplierAutoID,
                                srp_erp_customerinvoicedetails.segmentID as segmentID,
                                srp_erp_customerinvoicedetails.segmentCode as segmentCode,
                                srp_erp_suppliermaster.supplierName as supplierName,
                                srp_erp_suppliermaster.supplierSystemCode as supplierSystemCode,
                                srp_erp_suppliermaster.supplierAddress1 as supplierAddress,
                                srp_erp_suppliermaster.supplierTelephone as supplierTelephone,
                                srp_erp_suppliermaster.supplierFax as supplierFax,
                                srp_erp_suppliermaster.liabilityAutoID as liabilityAutoID,
                                srp_erp_suppliermaster.liabilitySystemGLCode as liabilitySystemGLCode,
                                srp_erp_suppliermaster.liabilityGLAccount as liabilityGLAccount,
                                srp_erp_suppliermaster.liabilityDescription as liabilityDescription,
                                srp_erp_suppliermaster.liabilityType as liabilityType,
                                srp_erp_suppliermaster.supplierCurrencyID as supplierCurrencyID,
                                srp_erp_suppliermaster.supplierCurrency as supplierCurrency,
                                srp_erp_suppliermaster.supplierCurrencyDecimalPlaces as supplierCurrencyDecimalPlaces
                            FROM
                                `srp_erp_customerinvoicedetails`
                            LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_customerinvoicedetails.supplierAutoID
                            WHERE
                                `invoiceAutoID` = $system_id
                            GROUP BY
                              supplierAutoID";
                    $sumsupdetail = $this->db->query($sumsup)->result_array();
                    $this->load->library('sequence');
                    $invdate=explode("-",$master['invoiceDate']);

                    foreach($sumsupdetail as $val){
                        $datasup['documentID'] = 'BSI';
                        $datasup['invoiceType'] = 'Standard';
                        $datasup['companyFinanceYearID'] = $master['companyFinanceYearID'];
                        $datasup['companyFinanceYear'] = $master['companyFinanceYear'];
                        $datasup['warehouseAutoID'] = $master['wareHouseAutoID'];
                        $datasup['isSytemGenerated'] = 1;
                        $datasup['documentOrigin'] = 'CINV';
                        $datasup['documentOriginAutoID'] = $system_id;
                        $datasup['FYBegin'] = $master['FYBegin'];
                        $datasup['FYEnd'] = $master['FYEnd'];
                        $datasup['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                        $datasup['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                        $datasup['companyFinancePeriodID'] = $master['companyFinancePeriodID'];
                        $datasup['bookingInvCode'] = $this->sequence->sequence_generator_fin('BSI',$master['companyFinanceYearID'],$invdate[0],$invdate[1]);
                        $datasup['bookingDate'] = $master['invoiceDate'];
                        $datasup['invoiceDate'] = $master['invoiceDate'];
                        $datasup['invoiceDueDate'] = $master['invoiceDueDate'];
                        $datasup['comments'] = 'From custome invoice '.$master['invoiceCode'];
                        $datasup['RefNo'] = $master['invoiceCode'];
                        $datasup['supplierID'] = $val['supplierAutoID'];
                        $datasup['supplierCode'] = $val['supplierSystemCode'];
                        $datasup['supplierName'] = $val['supplierName'];
                        $datasup['supplierAddress'] = $val['supplierAddress'];
                        $datasup['supplierTelephone'] = $val['supplierTelephone'];
                        $datasup['supplierFax'] = $val['supplierFax'];
                        $datasup['supplierliabilityAutoID'] = $val['liabilityAutoID'];
                        $datasup['supplierliabilitySystemGLCode'] = $val['liabilitySystemGLCode'];
                        $datasup['supplierliabilityGLAccount'] = $val['liabilityGLAccount'];
                        $datasup['supplierliabilityDescription'] = $val['liabilityDescription'];
                        $datasup['supplierliabilityType'] = $val['liabilityType'];
                        $datasup['supplierInvoiceDate'] = $master['invoiceDate'];
                        $datasup['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $datasup['transactionCurrency'] = $master['transactionCurrency'];
                        $datasup['transactionExchangeRate'] = $master['transactionExchangeRate'];
                        $datasup['transactionAmount'] = $val['transactionAmount'];
                        $datasup['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $datasup['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $datasup['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $datasup['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $datasup['companyLocalAmount'] = $val['transactionAmount']/$master['companyLocalExchangeRate'];
                        $datasup['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                        $datasup['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $datasup['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $datasup['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                        $datasup['companyReportingAmount'] = $val['transactionAmount']/$master['companyReportingExchangeRate'];
                        $datasup['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                        $datasup['supplierCurrencyID'] = $val['supplierCurrencyID'];
                        $datasup['supplierCurrency'] = $val['supplierCurrency'];
                        $datasup['segmentID'] = $val['segmentID'];
                        $datasup['segmentCode'] = $val['segmentCode'];
                        $datasup['companyID'] = current_companyID();
                        $datasup['companyCode'] = current_companyCode();
                        $supplier_currency = currency_conversionID($master['transactionCurrencyID'], $val['supplierCurrencyID']);
                        $datasup['supplierCurrencyExchangeRate'] = $supplier_currency['conversion'];
                        $datasup['supplierCurrencyAmount'] = $val['transactionAmount']/$supplier_currency['conversion'];
                        $datasup['supplierCurrencyDecimalPlaces'] = $val['supplierCurrencyDecimalPlaces'];
                        $datasup['confirmedYN'] = 1;
                        $datasup['confirmedByEmpID'] = current_userID();
                        $datasup['confirmedByName'] = current_user();
                        $datasup['confirmedDate'] = $this->common_data['current_date'];
                        $datasup['createdUserGroup'] = $this->common_data['user_group'];
                        $datasup['createdPCID'] = $this->common_data['current_pc'];
                        $datasup['createdUserID'] = $this->common_data['current_userID'];
                        $datasup['createdDateTime'] = $this->common_data['current_date'];
                        $datasup['createdUserName'] = $this->common_data['current_user'];

                        $supresult=$this->db->insert('srp_erp_paysupplierinvoicemaster', $datasup);
                        $last_idsup = $this->db->insert_id();
                        if($supresult){
                            $supid=$val['supplierAutoID'];
                            $supd = "SELECT * FROM `srp_erp_customerinvoicedetails` WHERE `invoiceAutoID` = $system_id AND `supplierAutoID` = $supid";
                            $supdetail = $this->db->query($supd)->result_array();

                            foreach($supdetail as $detl){
                                $datasupd['InvoiceAutoID'] = $last_idsup;
                                $datasupd['segmentID'] = $detl['segmentID'];
                                $datasupd['segmentCode'] = $detl['segmentCode'];
                                $datasupd['description'] = $detl['description'];
                                $datasupd['GLCode'] = "-";
                                $datasupd['transactionAmount'] = round($detl['transactionAmount']-$detl['marginAmount'],$master['transactionCurrencyDecimalPlaces']);
                                $datasupd['transactionExchangeRate'] = $master['transactionExchangeRate'];
                                $datasupd['companyLocalAmount'] = round($datasupd['transactionAmount']/$master['companyLocalExchangeRate'], $master['companyLocalCurrencyDecimalPlaces']);
                                $datasupd['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                $datasupd['companyReportingAmount'] = round($datasupd['transactionAmount']/$master['companyReportingExchangeRate'], $master['companyReportingCurrencyDecimalPlaces']);
                                $datasupd['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                $datasupd['supplierAmount'] = round($datasupd['transactionAmount']/$datasup['supplierCurrencyExchangeRate'], $datasup['supplierCurrencyDecimalPlaces']);
                                $datasupd['supplierCurrencyExchangeRate'] = $datasup['supplierCurrencyExchangeRate'];
                                $datasupd['companyCode'] = $this->common_data['company_data']['company_code'];
                                $datasupd['companyID'] = $this->common_data['company_data']['company_id'];
                                $datasupd['createdUserGroup'] = $this->common_data['user_group'];
                                $datasupd['createdPCID'] = $this->common_data['current_pc'];
                                $datasupd['createdUserID'] = $this->common_data['current_userID'];
                                $datasupd['createdUserName'] = $this->common_data['current_user'];
                                $datasupd['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_paysupplierinvoicedetail', $datasupd);
                            }
                            $this->load->library('Approvals');
                            $approvals_status_sup = $this->approvals->auto_approve($last_idsup, 'srp_erp_paysupplierinvoicemaster','InvoiceAutoID', 'BSI',$master['invoiceDate'],$master['invoiceDate']);
                            if($approvals_status_sup==1){
                                $this->load->model('Payable_modal');
                                $this->Payable_modal->save_supplier_invoice_approval(0, $last_idsup, 1, 'Auto Approved');
                            }
                        }
                    }
                }
            }else{
                if($isRetentionYN==1)
                {
                    $this->load->model('Double_entry_model');
                    $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data_opr($system_id, 'CINV');

                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['acknowledgementDate'] = $master['acknowledgementDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
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
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
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
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
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
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                // return array('e', 'Invoice Approval Failed.', 1);
            } else {
                $this->db->trans_commit();
                if($master['invoiceType']='Project')
                {
                    $this->updateRVMconfirmstatus($system_id);
                }
                // return array('s', 'Invoice Approval Successfull.', 1);
            }           

            $this->db->trans_start();
            $this->db->select('interCompanyID');
            $this->db->from('srp_erp_customerinvoicemaster');
            $this->db->where('invoiceAutoID',$system_id);
            $interCompany=$this->db->get();
            $inteCompanyID = $interCompany->row()->interCompanyID;

            if ($inteCompanyID!=0) {
               $this->db->select('*');
               $this->db->from('srp_erp_customerinvoicemaster');
               $this->db->where('invoiceAutoID',$system_id);
               $query = $this->db->get();
               $customerDetail = $query->row_array();

               $this->db->select('*');
               $this->db->from('srp_erp_suppliermaster');
               $this->db->where('companyID',$customerDetail['interCompanyID']);
               $this->db->where('interCompanyID',$customerDetail['companyID']);
               $query = $this->db->get(); 
               $supplierDetail = $query->row_array(); 

               $this->db->select('*');
               $this->db->from('srp_erp_company');
               $this->db->where('company_id',$customerDetail['interCompanyID']);
               $query=$this->db->get();
               $companyDetail=$query->row_array();

               if(!$supplierDetail){
                return array('e', 'Supplier not found');
               }
               else{

                $supplyData['supplierID']=$supplierDetail['supplierAutoID'];
                $supplyData['supplierCode']=$supplierDetail['supplierSystemCode'];
                $supplyData['supplierName']=$supplierDetail['supplierName'];
                $supplyData['supplierAddress']=$supplierDetail['supplierAddress1'];
                $supplyData['supplierTelephone']=$supplierDetail['supplierTelephone'];
                $supplyData['supplierFax']=$supplierDetail['supplierFax'];
                $supplyData['supplierliabilityAutoID']=$supplierDetail['liabilityAutoID'];
                $supplyData['supplierliabilitySystemGLCode']=$supplierDetail['liabilitySystemGLCode'];
                $supplyData['supplierliabilityGLAccount']=$supplierDetail['liabilityGLAccount'];
                $supplyData['supplierliabilityDescription']=$supplierDetail['liabilityDescription'];
                $supplyData['supplierliabilityType']=$supplierDetail['liabilityType'];
                $supplyData['supplierCurrencyID']=$supplierDetail['supplierCurrencyID'];
                $supplyData['supplierCurrency']=$supplierDetail['supplierCurrency'];
                $supplyData['supplierCurrencyDecimalPlaces']=$supplierDetail['supplierCurrencyDecimalPlaces'];

                $supplyData['bookingDate']=$customerDetail['acknowledgementDate'];
                $supplyData['supplierInvoiceDate']=$customerDetail['customerInvoiceDate'];
                $supplyData['invoiceDate']=$customerDetail['invoiceDate'];
                $supplyData['invoiceDueDate']=$customerDetail['invoiceDueDate'];
                $supplyData['invoiceType']='StandardExpense';
                $supplyData['documentID']='BSI';
                $supplyData['relatedDocumentID']='CINV';
                $supplyData['relatedDocumentMasterID']=$system_id;
                $supplyData['transactionCurrencyID']=$customerDetail['transactionCurrencyID'];
                $supplyData['transactionCurrency']=$customerDetail['transactionCurrency'];
                $supplyData['transactionExchangeRate']=$customerDetail['transactionExchangeRate'];
                $supplyData['transactionAmount']=$customerDetail['transactionAmount'];
                $supplyData['transactionCurrencyDecimalPlaces']=$customerDetail['transactionCurrencyDecimalPlaces'];
                $supplyData['segmentID']=$customerDetail['segmentID'];
                $supplyData['segmentCode']=$customerDetail['segmentCode'];
                $supplyData['currentLevelNo']=$customerDetail['currentLevelNo'];
                $supplyData['interCompanyID']=$customerDetail['companyID'];
                $supplyData['companyFinanceYearID']=$customerDetail['companyFinanceYearID'];
                $supplyData['companyFinanceYear']=$customerDetail['companyFinanceYear'];
                $supplyData['FYBegin']=$customerDetail['FYBegin'];
                $supplyData['FYEnd']=$customerDetail['FYEnd'];
                $supplyData['FYPeriodDateFrom']=$customerDetail['FYPeriodDateFrom'];
                $supplyData['FYPeriodDateTo']=$customerDetail['FYPeriodDateTo'];
                $supplyData['companyFinancePeriodID']=$customerDetail['companyFinancePeriodID'];
                $supplyData['retensionGL']=$customerDetail['retensionGL'];
                $supplyData['companyID'] = $customerDetail['interCompanyID'];
                $supplyData['createdUserGroup'] = $this->common_data['user_group'];
                $supplyData['createdPCID'] = $this->common_data['current_pc'];
                $supplyData['createdUserID'] = $this->common_data['current_userID'];
                $supplyData['createdUserName'] = $this->common_data['current_user'];
                $supplyData['createdDateTime'] = $this->common_data['current_date'];
                $supplyData['timestamp'] = $this->common_data['current_date'];


                $supplyData['companyCode'] = $companyDetail['company_code'];
                $supplyData['companyLocalCurrencyID'] = $companyDetail['company_default_currencyID'];
                $supplyData['companyLocalCurrency'] = $companyDetail['company_default_currency'];
                // $supplyData['companyLocalExchangeRate'] = $companyDetail['company_default_currencyID'];
                // $supplyData['companyLocalAmount'] = $companyDetail['company_default_currencyID'];
                $supplyData['companyLocalCurrencyDecimalPlaces'] = $companyDetail['company_default_decimal'];
                $supplyData['companyReportingCurrencyID'] = $companyDetail['company_reporting_currencyID'];
                $supplyData['companyReportingCurrency'] = $companyDetail['company_reporting_currency'];
                // $supplyData['companyReportingExchangeRate'] = $companyDetail['company_default_currencyID'];
                // $supplyData['companyReportingAmount'] = $companyDetail['company_default_currencyID'];
                $supplyData['companyReportingCurrencyDecimalPlaces'] = $companyDetail['company_reporting_decimal'];


                $this->db->insert('srp_erp_paysupplierinvoicemaster', $supplyData);
                $last_id = $this->db->insert_id();

                $this->db->select('*');
                $this->db->from('srp_erp_customerinvoicedetails');
                $this->db->where('invoiceAutoID',$system_id);
                $query = $this->db->get(); 
                $customerInvoiceDetail = $query->row_array(); 


                $supplyDetails['InvoiceAutoID']=$last_id;
                $supplyDetails['type']=$customerInvoiceDetail['type'];
                $supplyDetails['discountAmount']=$customerInvoiceDetail['discountAmount'];
                $supplyDetails['description']=$customerInvoiceDetail['description'];
                $supplyDetails['discountPercentage']=$customerInvoiceDetail['discountPercentage'];
                $supplyDetails['transactionAmount']=$customerInvoiceDetail['transactionAmount'];
                $supplyDetails['segmentID']=$customerInvoiceDetail['segmentID'];
                $supplyDetails['segmentCode']=$customerInvoiceDetail['segmentCode'];
                $supplyDetails['companyID']=$customerDetail['interCompanyID'];

                
                $supplyDetails['GLAutoID']=$customerInvoiceDetail['revenueGLAutoID'];
                $supplyDetails['GLCode']=$customerInvoiceDetail['revenueGLCode'];
                $supplyDetails['systemGLCode']=$customerInvoiceDetail['revenueSystemGLCode'];
                $supplyDetails['GLDescription']=$customerDetail['revenueGLDescription'];
                $supplyDetails['GLType']=$customerDetail['revenueGLType'];

                $supplyDetails['createdUserGroup'] = $this->common_data['user_group'];
                $supplyDetails['createdPCID'] = $this->common_data['current_pc'];
                $supplyDetails['createdUserID'] = $this->common_data['current_userID'];
                $supplyDetails['createdUserName'] = $this->common_data['current_user'];
                $supplyDetails['createdDateTime'] = $this->common_data['current_date'];
                $supplyDetails['taxCalculationformulaID']=$customerInvoiceDetail['taxCalculationformulaID'];
                
                $supplyDetails['companyCode'] = $companyDetail['company_code'];


                $this->db->insert('srp_erp_paysupplierinvoicedetail', $supplyDetails);
              

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Supply Invoice failed', 1);
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Supply Invoice created Successfull.', 1);
                }           

               }

            } 

            return array('s', 'Invoice approved Successfull.', 1);

        } else {
            return array('e', 'Some Item quantities are not sufficient to approve this transaction.', $items_arr);
        }
    }

    function delete_customerInvoice_attachement()
    {
        $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($this->input->post('attachmentID') ?? '')));
        return true;
    }

    function delete_invoice_master()
    {
        /* $this->db->delete('srp_erp_customerinvoicemaster', array('invoiceAutoID' => trim($this->input->post('invoiceAutoID') ?? '')));
         $this->db->delete('srp_erp_customerinvoicedetails', array('invoiceAutoID' => trim($this->input->post('invoiceAutoID') ?? '')));
         $this->db->delete('srp_erp_customerinvoicetaxdetails', array('invoiceAutoID' => trim($this->input->post('invoiceAutoID') ?? '')));*/
        
        $this->db->select('invoiceCode, invoiceType');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $master = $this->db->get()->row_array();

        if($master['invoiceType'] == 'Manufacturing') {
            $this->session->set_flashdata('w', 'This document is System Generated.');
            return true;
        } else {
            $this->db->select('*');
            $this->db->from('srp_erp_customerinvoicedetails');
            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            $datas = $this->db->get()->row_array();
            if ($datas) {
                $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
                return true;
            } else {
                $lenth=strlen($master['invoiceCode']);
                if($lenth > 1){
                    $data = array(
                        'isDeleted' => 1,
                        'deletedEmpID' => current_userID(),
                        'deletedDate' => current_date(),
                    );
                    $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
                    $this->db->update('srp_erp_customerinvoicemaster', $data);
                    return true;
    
                }else{
                    $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
                    $results = $this->db->delete('srp_erp_customerinvoicemaster');
                    if ($results) {
                        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
                        $this->db->delete('srp_erp_customerinvoicedetails');
                        $this->session->set_flashdata('s', 'Deleted Successfully');
                        return true;
                    }
                }
            }
        }
    }

    function load_subItem_notSold_QueryGen($itemAutoID, $detailID, $warehouseID)
    {
        $query = "SELECT * FROM srp_erp_itemmaster_sub iSub 
                                    WHERE iSub.itemAutoID = '" . $itemAutoID . "' AND  iSub.wareHouseAutoID ='" . $warehouseID . "'
                                    AND ( (( ISNULL(iSub.isSold) OR iSub.isSold = '' OR iSub.isSold = 0 ) ) OR (iSub.soldDocumentDetailID='" . $detailID . "' ) ) ;";

        return $query;
    }

    function load_subItem_notSold($detailID, $documentID, $warehouseID)
    {
        $subItemArray = array();

        switch ($documentID) {
            case "CINV":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_customerinvoicedetails WHERE invoiceDetailsAutoID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();
                    $subItemArray = $result;
                }
                break;

            case "RV":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_customerreceiptdetail WHERE receiptVoucherDetailAutoID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();
                    $subItemArray = $result;
                }
                break;

            case "SR":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_stockreturndetails WHERE stockReturnDetailsID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();
                    $subItemArray = $result;
                }
                break;

            case "MI":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_itemissuedetails WHERE itemIssueDetailID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();
                    $subItemArray = $result;
                }

                break;

            case "ST":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_stocktransferdetails WHERE stockTransferDetailsID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();

                    $subItemArray = $result;
                }
                break;

            case "SA":
                $item = $this->db->query(" SELECT itemAutoID FROM srp_erp_stockadjustmentdetails WHERE stockAdjustmentDetailsAutoID = '" . $detailID . "' ")->row_array();
                if (isset($item['itemAutoID']) && !empty($item['itemAutoID'])) {
                    $query = $this->load_subItem_notSold_QueryGen($item['itemAutoID'], $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();

                    $subItemArray = $result;
                }
                break;

            case "DO":
                $itemAutoID = $this->db->query(" SELECT itemAutoID FROM srp_erp_deliveryorderdetails WHERE DODetailsAutoID = '{$detailID}' ")->row('itemAutoID');
                if (!empty($itemAutoID)) {
                    $query = $this->load_subItem_notSold_QueryGen($itemAutoID, $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();
                    $subItemArray = $result;
                }
                break;

            case "JOB":
                $itemAutoID = $this->db->query("SELECT srp_erp_mfq_itemmaster.itemAutoID AS itemAutoID FROM srp_erp_mfq_jc_materialconsumption LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_jc_materialconsumption.mfqItemID WHERE jcMaterialConsumptionID = '{$detailID}'")->row('itemAutoID');
                if(!empty($itemAutoID)){
                    $query = $this->load_subItem_notSold_QueryGen($itemAutoID, $detailID, $warehouseID);
                    $result = $this->db->query($query)->result_array();
                    $subItemArray = $result;
                }
                break;

            default:
                echo $documentID . ' Error: Code not configured!<br/>';
                echo 'File: ' . __FILE__ . '<br/>';
                echo 'Line No: ' . __LINE__ . '<br><br>';
        }

        return $subItemArray;
    }

    function get_invoiceDetail($id)
    {
        $this->db->select('srp_erp_customerinvoicedetails.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_customerinvoicedetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID', 'left');
        $this->db->where('invoiceDetailsAutoID', $id);
        $r = $this->db->get()->row_array();
        return $r;
    }

    function get_receiptVoucherDetail($id)
    {
        $this->db->select('srp_erp_customerreceiptdetail.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID', 'left');
        $this->db->where('srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID', $id);
        $r = $this->db->get()->row_array();
        return $r;
    }

    function get_stockReturnDetail($id)
    {
        $this->db->select('srp_erp_stockreturndetails.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_stockreturndetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stockreturndetails.itemAutoID', 'left');
        $this->db->where('srp_erp_stockreturndetails.stockReturnDetailsID', $id);
        $r = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $r;
    }

    function get_materialIssueDetail($id)
    {
        $this->db->select('srp_erp_itemissuedetails.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_itemissuedetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_itemissuedetails.itemAutoID', 'left');
        $this->db->where('srp_erp_itemissuedetails.itemIssueDetailID', $id);
        $r = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $r;
    }

    function get_stockTransferDetail($id)
    {
        $this->db->select('srp_erp_stocktransferdetails.*,srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.secondaryUOMID,srp_erp_unit_of_measure.UnitDes as secUOMDes');
        $this->db->from('srp_erp_stocktransferdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stocktransferdetails.itemAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_itemmaster.secondaryUOMID', 'left');
        $this->db->where('srp_erp_stocktransferdetails.stockTransferDetailsID', $id);
        $r = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $r;
    }

    function get_stockAdjustmentDetail($id)
    {
        $this->db->select('srp_erp_stockadjustmentdetails.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_stockadjustmentdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stockadjustmentdetails.itemAutoID', 'left');
        $this->db->where('srp_erp_stockadjustmentdetails.stockAdjustmentDetailsAutoID', $id);
        $r = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $r;
    }

    function load_invoice_header_id($id)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate');
        $this->db->where('invoiceAutoID', $id);
        return $this->db->get('srp_erp_customerinvoicemaster')->row_array();
    }

    function load_customer_invoice_deatails()
    {
        $invoiceDetailsAutoID = $this->input->post('invoiceDetailsAutoID');

       // $invoiceDueDate = input_format_date($invDueDate, $date_format_policy);

        $convertFormat = convert_date_format_sql();
        $this->db->select('DATE_FORMAT(serviceFromDate,\'' . $convertFormat . '\') AS serviceFromDate,DATE_FORMAT(serviceToDate,\'' . $convertFormat . '\') AS serviceToDate');
        $this->db->where('invoiceDetailsAutoID', $invoiceDetailsAutoID);
        return $this->db->get('srp_erp_customerinvoicedetails')->row_array();
    }

    function save_subItemList()
    {
        $subItems = $this->input->post('subItemCode[]');
        $soldDocumentID = $this->input->post('soldDocumentID');
        $soldDocumentAutoID = $this->input->post('soldDocumentAutoID');
        $soldDocumentDetailID = $this->input->post('soldDocumentDetailID');

        $currentUser = current_pc();
        $modifiedUserID = current_userID();
        $modifiedDatetime = format_date_mysql_datetime();
        if (!empty($subItems)) {
            $i = 0;
            foreach ($subItems as $subItem) {
                $data[$i]['subItemAutoID'] = $subItem;
                $data[$i]['soldDocumentID'] = $soldDocumentID;
                $data[$i]['isSold'] = 1;
                $data[$i]['soldDocumentAutoID'] = $soldDocumentAutoID;
                $data[$i]['soldDocumentDetailID'] = $soldDocumentDetailID;
                $data[$i]['modifiedPCID'] = $currentUser;
                $data[$i]['modifiedUserID'] = $modifiedUserID;
                $data[$i]['modifiedDatetime'] = $modifiedDatetime;
                $i++;
            }


            if (!empty($data)) {

                $dataTmp['isSold'] = null;
                $dataTmp['soldDocumentAutoID'] = null;
                $dataTmp['soldDocumentDetailID'] = null;
                $dataTmp['soldDocumentID'] = null;
                $dataTmp['modifiedPCID'] = $currentUser;
                $dataTmp['modifiedUserID'] = $modifiedUserID;
                $dataTmp['modifiedDatetime'] = $modifiedDatetime;

                $this->db->where('soldDocumentAutoID', $soldDocumentAutoID);
                $this->db->where('soldDocumentDetailID', $soldDocumentDetailID);
                $this->db->update('srp_erp_itemmaster_sub', $dataTmp);


                $this->db->update_batch('srp_erp_itemmaster_sub', $data, 'subItemAutoID');
            }

            if($soldDocumentID == 'ST') {
                $attrinutes = fetch_company_assigned_attributes();
                if(in_array('QtyPrimaryUOM', array_column($attrinutes, 'columnName')))
                {
                    $id = implode(',', $subItems);
                    $this->db->select('SUM(IFNULL(QtyPrimaryUOM, 0)) AS Qty');
                    $this->db->where('subItemAutoID IN (' . $id . ')');
                    $qty = $this->db->get('srp_erp_itemmaster_sub')->row('Qty');
                    if(empty($qty)) {
                        $qty = 0;
                    }

                    $result = $this->db->query("UPDATE srp_erp_stocktransferdetails SET 
                                                        transfer_QTY = {$qty},
                                                        totalValue = currentlWacAmount * {$qty}
                                                    WHERE 
                                                        stockTransferDetailsID= {$soldDocumentDetailID}
                                                        AND stockTransferAutoID= {$soldDocumentAutoID}");

                    $data_st['transfer_QTY'] = $qty;
                    $this->db->where('stockTransferDetailsID', $soldDocumentDetailID);
                    $this->db->where('stockTransferAutoID', $soldDocumentAutoID);
                    $this->db->update('srp_erp_stocktransferdetails', $data_st);
                }
            }
            return array('error' => 0, 'message' => 'Record/s updated successfully');

        } else {
            return array('error' => 1, 'message' => 'Please select sub items!');
        }

    }

    function re_open_invoice()
    {
        $this->db->select('invoiceCode, invoiceType');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $master = $this->db->get()->row_array();

        if($master['invoiceType'] == 'Manufacturing') {
            $this->session->set_flashdata('w', 'This document is System Generated.');
            return true;
        } else {
            $data = array(
                'isDeleted' => 0,
            );
            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            $this->db->update('srp_erp_customerinvoicemaster', $data);
            $this->session->set_flashdata('s', 'Re Opened Successfully.');
            return true;
        }
    }

    function customerinvoiceGLUpdate()
    {
        $gl = fetch_gl_account_desc($this->input->post('PLGLAutoID'));

        $BLGLAutoID = $this->input->post('BLGLAutoID');

        $data = array(
            'expenseGLAutoID' => $this->input->post('PLGLAutoID'),
            'expenseSystemGLCode' => $gl['systemAccountCode'],
            'expenseGLCode' => $gl['GLSecondaryCode'],
            'expenseGLDescription' => $gl['GLDescription'],
            'expenseGLType' => $gl['subCategory'],

        );
        if (isset($BLGLAutoID)) {
            $bl = fetch_gl_account_desc($this->input->post('BLGLAutoID'));
            $data = array_merge($data, array(
                'revenueGLAutoID' => $this->input->post('BLGLAutoID'),
                'revenueGLCode' => $bl['systemAccountCode'],
                'revenueSystemGLCode' => $bl['GLSecondaryCode'],
                'revenueGLDescription' => $bl['GLSecondaryCode']));
            /*'revenueGLType'=>'',*/


        }


        if ($this->input->post('applyAll') == 1) {
            $this->db->where('invoiceAutoID', trim($this->input->post('masterID') ?? ''));
        } else {
            $this->db->where('invoiceDetailsAutoID', trim($this->input->post('detailID') ?? ''));
        }
        $this->db->update('srp_erp_customerinvoicedetails ', $data);
        return array('s', 'GL Account Successfully Changed');
    }


    function fetch_customer_invoice_all_detail_edit()
    {
        $this->db->select('srp_erp_customerinvoicedetails.*,srp_erp_customerinvoicemaster.invoiceType,srp_erp_itemmaster.currentStock as currentStock,srp_erp_itemmaster.mainCategory,srp_erp_itemmaster.seconeryItemCode');
        $this->db->where('srp_erp_customerinvoicedetails.invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $this->db->where('srp_erp_customerinvoicedetails.type', 'Item');
        $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicedetails.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_customerinvoicedetails.itemAutoID = srp_erp_itemmaster.itemAutoID', 'left');
        $this->db->from('srp_erp_customerinvoicedetails');
        return $this->db->get()->result_array();
    }

    function updateCustomerInvoice_edit_all_Item()
    {
        $projectExist = project_is_exist();
        $invoiceDetailsAutoID = $this->input->post('invoiceDetailsAutoID');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $item_text = $this->input->post('item_text');
        $wareHouse = $this->input->post('wareHouse');
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $projectID = $this->input->post('projectID');
        $quantityRequested = $this->input->post('quantityRequested');
        $item_taxPercentage = $this->input->post('item_taxPercentage');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');

        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $tax_master = array();
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm= $this->db->get()->row_array();

//            if (!trim($invoiceDetailsAutoID[$key])) {
//                $this->db->select('invoiceAutoID,,itemDescription,itemSystemCode');
//                $this->db->from('srp_erp_customerinvoicedetails');
//                $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
//                $this->db->where('itemAutoID', $itemAutoID);
//                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
//                $order_detail = $this->db->get()->row_array();
//                if($serviceitm['mainCategory']=="Inventory") {
//                    if (!empty($order_detail)) {
//                        return array('w', 'Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
//                    }
//                }
//            }else{
//                $this->db->select('invoiceAutoID,,itemDescription,itemSystemCode');
//                $this->db->from('srp_erp_customerinvoicedetails');
//                $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
//                $this->db->where('itemAutoID', $itemAutoID);
//                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
//                $this->db->where('invoiceDetailsAutoID !=', $invoiceDetailsAutoID[$key]);
//                $order_detail = $this->db->get()->row_array();
//                if($serviceitm['mainCategory']=="Inventory") {
//                    if (!empty($order_detail)) {
//                        return array('w', 'Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
//                    }
//                }
//            }

            if (isset($item_text[$key])) {
                $this->db->select('*');
                $this->db->where('taxMasterAutoID', $item_text[$key]);
                $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();

                $this->db->select('*');
                $this->db->where('supplierSystemCode', $tax_master['supplierSystemCode']);
                $Supplier_master = $this->db->get('srp_erp_suppliermaster')->row_array();

                $this->db->select('srp_erp_taxmaster.*,srp_erp_chartofaccounts.GLAutoID as liabilityAutoID,srp_erp_chartofaccounts.systemAccountCode as liabilitySystemGLCode,srp_erp_chartofaccounts.GLSecondaryCode as liabilityGLAccount,srp_erp_chartofaccounts.GLDescription as liabilityDescription,srp_erp_chartofaccounts.CategoryTypeDescription as liabilityType,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.DecimalPlaces');
                $this->db->where('taxMasterAutoID', $item_text[$key]);
                $this->db->from('srp_erp_taxmaster');
                $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.supplierGLAutoID');
                $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_taxmaster.supplierCurrencyID');
                $tax_master = $this->db->get()->row_array();
            }

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){

                $batch_number2 = $this->input->post('batch_number['.$key.']');
                $arraydata2 = implode(',',$batch_number2);
                $data['batchNumber'] = $arraydata2;
                
            }

            $wareHouse_location = explode('|', $wareHouse[$key]);
            $item_arr = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);

            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm= $this->db->get()->row_array();

            $data['invoiceAutoID'] = trim($invoiceAutoID);
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_arr['itemSystemCode'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['itemDescription'] = $item_arr['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = $quantityRequested[$key];
            $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = $discount_amount[$key];
            $amountafterdiscount = $estimatedAmount[$key] - $data['discountAmount'];
            $data['unittransactionAmount'] = round($estimatedAmount[$key], $master['transactionCurrencyDecimalPlaces']);
            $data['taxPercentage'] = $item_taxPercentage[$key];
            $taxAmount = ($data['taxPercentage'] / 100) * $amountafterdiscount;
            $data['taxAmount'] = round($taxAmount, $master['transactionCurrencyDecimalPlaces']);
            $totalAfterTax = $data['taxAmount'] * $data['requestedQty'];
            $data['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
            $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested[$key];
            $data['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $data['comment'] = $comment[$key];
            $data['remarks'] = $remarks[$key];
            $data['type'] = 'Item';
            $item_data = fetch_item_data($data['itemAutoID']);
            if($serviceitm['mainCategory']=="Service") {
                $data['wareHouseAutoID'] = null;
                $data['wareHouseCode'] = null;
                $data['wareHouseLocation'] = null;
                $data['wareHouseDescription'] = null;
            }else{
                $data['wareHouseAutoID'] = $wareHouseAutoID[$key];
                $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
                $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
                $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
            }
            $data['segmentID'] = $master['segmentID'];
            $data['segmentCode'] = $master['segmentCode'];
            $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
            $data['expenseGLCode'] = $item_data['costGLCode'];
            $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['expenseGLDescription'] = $item_data['costDescription'];
            $data['expenseGLType'] = $item_data['costType'];
            $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
            $data['revenueGLCode'] = $item_data['revanueGLCode'];
            $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['revenueGLDescription'] = $item_data['revanueDescription'];
            $data['revenueGLType'] = $item_data['revanueType'];
            $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
            $data['assetGLCode'] = $item_data['assteGLCode'];
            $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['assetGLDescription'] = $item_data['assteDescription'];
            $data['assetGLType'] = $item_data['assteType'];
            $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['itemCategory'] = $item_data['mainCategory'];

            if (!empty($tax_master)) {
                $data['taxMasterAutoID'] = $tax_master['taxMasterAutoID'];
                $data['taxDescription'] = $tax_master['taxDescription'];
                $data['taxShortCode'] = $tax_master['taxShortCode'];
                $data['taxSupplierAutoID'] = $tax_master['supplierAutoID'];
                $data['taxSupplierSystemCode'] = $tax_master['supplierSystemCode'];
                $data['taxSupplierName'] = $tax_master['supplierName'];
                $data['taxSupplierCurrencyID'] = $tax_master['supplierCurrencyID'];
                $data['taxSupplierCurrency'] = $tax_master['CurrencyCode'];
                $data['taxSupplierCurrencyDecimalPlaces'] = $tax_master['DecimalPlaces'];
                $data['taxSupplierliabilityAutoID'] = $tax_master['liabilityAutoID'];
                $data['taxSupplierliabilitySystemGLCode'] = $tax_master['liabilitySystemGLCode'];
                $data['taxSupplierliabilityGLAccount'] = $tax_master['liabilityGLAccount'];
                $data['taxSupplierliabilityDescription'] = $tax_master['liabilityDescription'];
                $data['taxSupplierliabilityType'] = $tax_master['liabilityType'];
                $supplierCurrency = currency_conversion($master['transactionCurrency'], $data['taxSupplierCurrency']);
                $data['taxSupplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                $data['taxSupplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                $data['taxSupplierCurrencyAmount'] = ($data['transactionAmount'] / $data['taxSupplierCurrencyExchangeRate']);
            } else {
                $data['taxSupplierCurrencyExchangeRate'] = 1;
                $data['taxSupplierCurrencyDecimalPlaces'] = 2;
                $data['taxSupplierCurrencyAmount'] = 0;
            }

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];


            if (trim($invoiceDetailsAutoID[$key])) {
                $this->db->where('invoiceDetailsAutoID', trim($invoiceDetailsAutoID[$key]));
                $this->db->update('srp_erp_customerinvoicedetails', $data);
                $this->db->trans_complete();
            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerinvoicedetails', $data);

                if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                    $this->db->select('itemAutoID');
                    $this->db->where('itemAutoID', $itemAutoID);
                    $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                    if (empty($warehouseitems)) {
                        $data_arr = array(
                            'wareHouseAutoID' => $data['wareHouseAutoID'],
                            'wareHouseLocation' => $data['wareHouseLocation'],
                            'wareHouseDescription' => $data['wareHouseDescription'],
                            'itemAutoID' => $data['itemAutoID'],
                            'itemSystemCode' => $data['itemSystemCode'],
                            'barCodeNo' => $item_data['barcode'],
                            'salesPrice' => $item_data['companyLocalSellingPrice'],
                            'ActiveYN' => $item_data['isActive'],
                            'itemDescription' => $data['itemDescription'],
                            'unitOfMeasureID' => $data['defaultUOMID'],
                            'unitOfMeasure' => $data['defaultUOM'],
                            'currentStock' => 0,
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'companyCode' => $this->common_data['company_data']['company_code'],
                        );
                        $this->db->insert('srp_erp_warehouseitems', $data_arr);
                    }
                }
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Invoice Detail : Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Invoice Detail : Saved Successfully.');
        }
    }
    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'CINV');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }
    function invoiceloademail()
    {
        $invoiceautoid = $this->input->post('invoiceAutoID');
        $this->db->select('srp_erp_customerinvoicemaster.*,srp_erp_customermaster.customerEmail as customerEmail');
        $this->db->where('invoiceAutoID', $invoiceautoid);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $this->db->from('srp_erp_customerinvoicemaster ');
        return $this->db->get()->row_array();
    }
    function send_invoice_email()
    {
        $invoiceautoid = trim($this->input->post('invoiceid') ?? '');
        $invoiceemail = trim($this->input->post('email') ?? '');
        $attachmentID = $this->input->post('attachmentID');
        $documentid = 'CINV';
        $attachmentID_join = '';
        if(!empty($attachmentID))
        {
            $attachmentID_join =   join(', ', $attachmentID);
        }

        if(empty($invoiceemail) || $invoiceemail =='')
        {
            return array('e', 'email address is required.');
            exit();
        }

        $ccEmail = trim($this->input->post('ccemail') ?? '');
        $this->db->select('srp_erp_customerinvoicemaster.*,srp_erp_customermaster.customerEmail as customerEmail,srp_erp_customermaster.customerName as customerName');
        $this->db->where('invoiceAutoID', $invoiceautoid);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $this->db->from('srp_erp_customerinvoicemaster ');
        $results = $this->db->get()->row_array();

        if (!empty($results)) {
            if ($results['customerEmail'] == '') {
                $data_master['customerEmail'] = $invoiceemail;
                $this->db->where('customerAutoID', $results['customerID']);
                $this->db->update('srp_erp_customermaster', $data_master);
            }
        }
        $this->db->select('customerEmail,customerName');
        $this->db->where('customerAutoID', $results['customerID']);
        $this->db->from('srp_erp_customermaster ');
        $customerMaster = $this->db->get()->row_array();

        $this->load->library('NumberToWords');
        $data['extra'] = $this->Invoice_model->fetch_invoice_template_data($invoiceautoid);
        $data['approval'] = $this->input->post('approval');
        $data['printHeaderFooterYN'] = 1;
        $data['signature'] = $this->Invoice_model->fetch_signaturelevel();
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $data['printHeaderFooterYN']=1;
        $data['emailView'] = 1; // to get the html view otherwise it will set two headers
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $data['group_based_tax'] = existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',$invoiceautoid,'CINV','invoiceAutoID');
        $VatTax = $this->db->query("SELECT
                                        COUNT(taxCategory) as taxcat
                                    FROM
                                        `srp_erp_taxledger`
                                    LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                    WHERE
                                        documentID = 'CINV'
                                        ANd srp_erp_taxledger.companyID = {$companyID}
                                        AND documentMasterAutoID = {$invoiceautoid}")->row('taxcat');
        $data['is_tax_invoice'] = 0;
        if($data['group_based_tax']==1 && $VatTax > 0 ){
            $data['is_tax_invoice'] = 1;
        }
        $data['invoiceType'] = $results['invoiceType'];
        $data['date_of_supply'] = $this->db->query("SELECT
                                        DATE_FORMAT(IFNULL(MAX(srp_erp_deliveryorder.DODate) ,(srp_erp_customerinvoicemaster.invoiceDate) ),'$convertFormat') as supplierDate
                                    FROM
                                        `srp_erp_customerinvoicedetails`
                                    LEFT JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_customerinvoicedetails.DOMasterID
                                    LEFT JOIN srp_erp_customerinvoicemaster ON  srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
                                    WHERE
                                        srp_erp_customerinvoicedetails.companyID = {$companyID}
                                        AND srp_erp_customerinvoicedetails.invoiceAutoID = {$invoiceautoid}
        ")->row('supplierDate');
        $printlink = print_template_pdf('CINV','system/invoices/erp_invoice_print');
        $html = $this->load->view($printlink, $data, true);



        $this->load->library('pdf');
        $path = UPLOAD_PATH.base_url().'/uploads/invoice/'. $invoiceautoid .$results["documentID"] . current_userID() . ".pdf";
        $this->pdf->save_pdf($html, 'A4', 1, $path);

        if (!empty($customerMaster)) {
            if ($customerMaster['customerEmail'] != '') {
                $param = array();
                $param["empName"] = 'Sir/Madam';
                $param["body"] = 'we are pleased to submit our invoice as follows.<br/>
                                          <table border="0px">
                                          </table>';
                $mailData = [
                    'approvalEmpID' => '',
                    'documentCode' => '',
                    'toEmail' => $invoiceemail ,
                    'ccEmail' => $ccEmail ,
                    'subject' => ' Customer Invoice for '.$customerMaster['customerName'],
                    'param' => $param
                ];
                send_customerinvoice_emailCc($mailData, 1,$path,$documentid, $attachmentID_join);                
                return array('s', 'Email Send Successfully 123.',$invoiceemail,$invoiceautoid);
            } else {
                return array('e', 'Please enter an Email ID.');
            } 
        }
    }

    function fetch_invoice_template_data_temp($invoiceAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $companyID=current_companyID();
        $this->db->select('*,DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDate,\'' . $convertFormat . '\') AS invoiceDate ,DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(srp_erp_customerinvoicemaster.customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(srp_erp_customerinvoicemaster.approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->from('srp_erp_customerinvoicemaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax');
        $this->db->where('customerAutoID', $data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();


        $this->db->select('srp_erp_customerinvoicedetails.*, srp_erp_itemmaster.seconeryItemCode AS itemSecondaryCode,contractmaster.documentID,    FORMAT( requestedQty,2) as requestedQtyformatted,'.$item_code_alias.'');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_customerinvoicedetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID');
        $this->db->join('srp_erp_contractmaster contractmaster', 'contractmaster.contractAutoID = srp_erp_customerinvoicedetails.contractAutoID','left');
        $data['item_detail'] = $this->db->get()->result_array();
       
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_customerinvoicedetails');
        $data['gl_detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['tax'] = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();

        $convertFormat = convert_date_format_sql();
        $this->db->select('cus.*, DOMasterID,DATE_FORMAT(DODate,\''.  $convertFormat .'\') AS DODate,DOCode,referenceNo,del_ord.transactionAmount AS do_tr_amount,due_amount,balance_amount');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'DO');
        $this->db->from('srp_erp_customerinvoicedetails cus');
        $this->db->join('srp_erp_deliveryorder del_ord', 'del_ord.DOAutoID = cus.DOMasterID');
        $data['delivery_order'] = $this->db->get()->result_array();

        $data['taxledger'] = $this->db->query("SELECT
                tax.taxDescription,tax.taxShortCode,srp_erp_taxledger.taxMasterID,SUM(srp_erp_taxledger.amount)as amount
            FROM
                `srp_erp_taxledger`
            LEFT JOIN srp_erp_taxmaster tax on srp_erp_taxledger.taxMasterID=tax.taxMasterAutoID
            WHERE
                documentMasterAutoID = $invoiceAutoID
            AND documentID = 'CINV'
            AND srp_erp_taxledger.companyID = $companyID
            GROUP BY srp_erp_taxledger.taxMasterID ")->result_array();

      

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['extracharge'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['discount'] = $this->db->get('srp_erp_customerinvoicediscountdetails')->result_array();

        return $data;
    }

    function load_default_note(){
        $docid = trim($this->input->post('docid') ?? '');
        $this->db->select('description');
        $this->db->where('documentID', $docid);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('isDefault', 1);
        $data = $this->db->get('srp_erp_termsandconditions')->row_array();
        return $data;
    }

    function open_all_notes(){
        $docid = trim($this->input->post('docid') ?? '');
        $this->db->select('autoID,description');
        $this->db->where('documentID', $docid);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get('srp_erp_termsandconditions')->result_array();
        return $data;
    }

    function load_notes(){
        $autoID = trim($this->input->post('allnotedesc') ?? '');
        $this->db->select('description');
        $this->db->where('autoID', $autoID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get('srp_erp_termsandconditions')->row_array();
        return $data;
    }
    function saveinsurancetype()
    {
        $companyid = current_companyID();
        $insurancetype = $this->input->post('insurancetype');
        $insurancetypeID = $this->input->post('insurancetypeId');
        $GLAutoID = $this->input->post('gl_code');
        $marginPercentage = $this->input->post('marginPercentage');

        $data['insuranceType'] = $insurancetype;
        $data['GLAutoID'] = $GLAutoID;
        $data['marginPercentage'] = $marginPercentage;


        if(!empty($insurancetypeID))
        {
            $q = "SELECT insuranceType FROM srp_erp_invoiceinsurancetypes WHERE insuranceType = '{$insurancetype }' AND companyID = $companyid AND insuranceTypeID != $insurancetypeID" ;
            $result = $this->db->query($q)->row_array();
            if ($result) {
                return array('e', 'Insurance Type Already Exist');
            }else
            {
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $this->db->where('insuranceTypeID', $insurancetypeID);
                $this->db->update('srp_erp_invoiceinsurancetypes', $data);
            }


        }else
        {
            $q = "SELECT insuranceType FROM srp_erp_invoiceinsurancetypes WHERE insuranceType =  '{$insurancetype }' AND companyID = $companyid";
            $result = $this->db->query($q)->row_array();
            if ($result) {
                return array('e', 'Insurance Type Already Exist');
            }else
            {
                $data['companyID'] = $companyid;
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_invoiceinsurancetypes', $data);
                $last_id = $this->db->insert_id();
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Insurance Type Added Failed.');
        } else {
            $this->db->trans_commit();
            return array('s', 'Insurance Type Added Successfully.');
        }
    }
    function getinsurancetype()
    {
        $insurancetypeid = $this->input->post('insuranceTypeID');
        $comapnyid = current_companyID();
        $data = $this->db->query("select * from srp_erp_invoiceinsurancetypes where companyID = $comapnyid And insuranceTypeID = $insurancetypeid")->row_array();
        return $data;
    }
    function deleteinsurancetype()
    {
        $comapnyid = current_companyID();
        $insurancetypeid = $this->input->post('insuranceTypeID');
        $insuranceexist = $this->db->query("select invoiceAutoID from srp_erp_customerinvoicemaster where companyID = $comapnyid AND insuranceTypeID = $insurancetypeid")->row_array();
        if(!empty($insuranceexist))
        {
            return array('e', 'Insurance Type Already selected for a invoice.');
        }else
        {
            $this->db->delete('srp_erp_invoiceinsurancetypes', array('insuranceTypeID' => $insurancetypeid));
            return array('s', 'Insurance Type Deleted Successfully.');
        }


    }
    function save_invoice_header_insurance()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $invDueDate = $this->input->post('invoiceDueDate');
        $invoiceDueDate = input_format_date($invDueDate, $date_format_policy);
        $invDate = $this->input->post('invoiceDate');
        $invoiceDate = input_format_date($invDate, $date_format_policy);
        $customerDate = $this->input->post('customerInvoiceDate');
        $customerInvoiceDate = input_format_date($customerDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $customerID = $this->input->post('customerID');
        $rebate = getPolicyValues('CRP', 'All');
        if($rebate == 1) {
            $rebateDet = $this->db->query("SELECT rebatePercentage, rebateGLAutoID FROM `srp_erp_customermaster` WHERE customerAutoID = {$customerID}")->row_array();
            $data['rebateGLAutoID'] = $rebateDet['rebateGLAutoID'];
            $data['rebatePercentage'] = $rebateDet['rebatePercentage'];
        } else {
            $data['rebateGLAutoID'] = null;
            $data['rebatePercentage'] = null;
        }

        if($financeyearperiodYN==1) {
            $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));

            $FYBegin = input_format_date($financeyr[0], $date_format_policy);
            $FYEnd = input_format_date($financeyr[1], $date_format_policy);
        }else{
            $financeYearDetails=get_financial_year($invoiceDate);
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
            $financePeriodDetails=get_financial_period_date_wise($invoiceDate);

            if(empty($financePeriodDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }

        if($this->input->post('invoiceType')=='Insurance'){
            $date_format_policy = date_format_policy();
            $policyStDt = $this->input->post('policyStartDate');
            $policyStartDate = input_format_date($policyStDt, $date_format_policy);

            $policyendDt = $this->input->post('policyEndDate');
            $policyEndDate = input_format_date($policyendDt, $date_format_policy);
        }else{
            $policyStartDate=null;
            $policyEndDate=null;
        }

        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID') ?? ''));
        //$location = explode('|', trim($this->input->post('location_dec') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        if ($this->input->post('RVbankCode')) {
            $bank_detail = fetch_gl_account_desc(trim($this->input->post('RVbankCode') ?? ''));
            $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
            $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
            $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
            $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
            $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
            $data['invoicebank'] = $bank_detail['bankName'];
            $data['invoicebankBranch'] = $bank_detail['bankBranch'];
            $data['invoicebankSwiftCode'] = $bank_detail['bankSwiftCode'];
            $data['invoicebankAccount'] = $bank_detail['bankAccountNumber'];
            $data['invoicebankType'] = $bank_detail['subCategory'];
        }
        $data['documentID'] = 'CINV';
        $data['insuranceTypeID'] = $this->input->post('insurancetypeid');
        $data['insuranceSubTypeID'] = $this->input->post('insuranceSubTypeID');
        $data['policyStartDate'] = $policyStartDate;
        $data['policyEndDate'] = $policyEndDate;
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['invoiceDate'] = trim($invoiceDate);
        $data['customerInvoiceDate'] = trim($customerInvoiceDate);
        $data['invoiceDueDate'] = trim($invoiceDueDate);
        $data['invoiceNarration'] = trim_desc($this->input->post('invoiceNarration'));

        $crTypes = explode('<table', $this->input->post('invoiceNote'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['invoiceNote'] = trim($notes);
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['salesPersonID'] = trim($this->input->post('salesPersonID') ?? '');
        if ($data['salesPersonID']) {
            $code = explode(' | ', trim($this->input->post('salesPerson') ?? ''));
            $data['SalesPersonCode'] = trim($code[0] ?? '');
        }
        // $data['wareHouseCode'] = trim($location[0] ?? '');
        // $data['wareHouseLocation'] = trim($location[1] ?? '');
        // $data['wareHouseDescription'] = trim($location[2] ?? '');
        $data['invoiceType'] = trim($this->input->post('invoiceType') ?? '');
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $data['isPrintDN'] = trim($this->input->post('isPrintDN') ?? '');
        $data['customerID'] = $customer_arr['customerAutoID'];
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
        $data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
        $data['customerTelephone'] = $customer_arr['customerTelephone'];
        $data['customerFax'] = $customer_arr['customerFax'];
        $data['customerEmail'] = $customer_arr['customerEmail'];
        $data['customerReceivableAutoID'] = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription'] = $customer_arr['receivableDescription'];
        $data['customerReceivableType'] = $customer_arr['receivableType'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
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
        $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];

        if (trim($this->input->post('invoiceAutoID') ?? '')) {
            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            $this->db->update('srp_erp_customerinvoicemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Invoice Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                $this->session->set_flashdata('s', 'Invoice Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('invoiceAutoID'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['invoiceCode'] = 0;
            //if ($data['isPrintDN']==1) {
            $data['deliveryNoteSystemCode'] = $this->sequence->sequence_generator('DLN');
            //}

            $this->db->insert('srp_erp_customerinvoicemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Invoice   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                $this->session->set_flashdata('s', 'Invoice Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }
    function fetch_invoice_template_data_temp_insurance($invoiceAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $companyID=current_companyID();
        $this->db->select('srp_erp_customerinvoicemaster.*,DATE_FORMAT(srp_erp_customerinvoicemaster.createdDateTime,\'' . $convertFormat . '\') AS createdDateTime ,srp_erp_segment.description as segDescription,insurancetype.insuranceType as insurance,DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDate,\'' . $convertFormat . '\') AS invoiceDate ,DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(srp_erp_customerinvoicemaster.customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(srp_erp_customerinvoicemaster.approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN srp_erp_customerinvoicemaster.confirmedYN = 2 || srp_erp_customerinvoicemaster.confirmedYN = 3   THEN " - " WHEN srp_erp_customerinvoicemaster.confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn,srp_erp_salespersonmaster.SalesPersonName as SalesPersonName, textIdentificationNo, IFNULL(taxCardNo, " - ") as taxCardNo');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->join('srp_erp_invoiceinsurancetypes insurancetype','insurancetype.insuranceTypeID = srp_erp_customerinvoicemaster.insuranceTypeID','left');
        $this->db->join('srp_erp_salespersonmaster','srp_erp_salespersonmaster.salesPersonID = srp_erp_customerinvoicemaster.salesPersonID','left');
        $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_customerinvoicemaster.segmentID', 'Left');
        $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_erp_customerinvoicemaster.companyID', 'Left');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax');
        $this->db->where('customerAutoID', $data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();


        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_customerinvoicedetails');
        $data['item_detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_customerinvoicedetails');
        $data['gl_detail'] = $this->db->get()->result_array();

        $this->db->select('srp_erp_customerinvoicedetails.*,srp_erp_suppliermaster.supplierName as supplierName');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'insurance');
        $this->db->join('srp_erp_suppliermaster','srp_erp_suppliermaster.supplierAutoID = srp_erp_customerinvoicedetails.supplierAutoID','left');
        $this->db->from('srp_erp_customerinvoicedetails');
        $data['insurance_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['tax'] = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();

        $data['taxledger'] = $this->db->query("SELECT
    tax.taxDescription,tax.taxShortCode,srp_erp_taxledger.taxMasterID,SUM(srp_erp_taxledger.amount)as amount
FROM
    `srp_erp_taxledger`
LEFT JOIN srp_erp_taxmaster tax on srp_erp_taxledger.taxMasterID=tax.taxMasterAutoID
WHERE
    documentMasterAutoID = $invoiceAutoID
AND documentID = 'CINV'
AND srp_erp_taxledger.companyID = $companyID

GROUP BY srp_erp_taxledger.taxMasterID ")->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['extracharge'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['discount'] = $this->db->get('srp_erp_customerinvoicediscountdetails')->result_array();

        /* Added */
        $data['approvallevels'] = '';
        if($data['master']){
            if($data['master']['confirmedYN'] == 1 && $data['master']['approvedYN'] == 0){
                $this->db->select('ECode, Ename2, documentApprovedID, approvalLevelID, approvedEmpID, ApprovedDate');
                $this->db->from('srp_erp_documentapproved');
                $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_documentapproved.approvedEmpID');
                $this->db->where('documentSystemCode', $invoiceAutoID);
                $this->db->where('documentID', 'CINV');
                $this->db->where('approvedYN', 1);
                $this->db->order_by('approvalLevelID', 'ASC');
                $data['approvallevels'] = $this->db->get()->result_array();
            }
        }

        return $data;
    }


    function save_direct_invoice_detail_margin()
    {
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $projectExist = project_is_exist();
        $segment_gls = $this->input->post('segment_gl');
        $gl_code_des = $this->input->post('gl_code_des');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $gl_code = $this->input->post('gl_code');
        $projectID = $this->input->post('projectID');
        $amount = $this->input->post('amount');
        $marginPercentage = $this->input->post('marginPercentage');
        $marginAmount = $this->input->post('marginAmount');
        //$transactionAmount = $this->input->post('transactionAmount');
        $description = $this->input->post('description');

        foreach ($segment_gls as $key => $segment_gl) {
            $segment = explode('|', $segment_gl);
            $gl_code_de = explode(' | ', $gl_code_des[$key]);
            $data[$key]['invoiceAutoID'] = trim($invoiceAutoID);
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data[$key]['projectID'] = $projectID[$key];
                $data[$key]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$key]['revenueGLAutoID'] = $gl_code[$key];
            $data[$key]['revenueSystemGLCode'] = trim($gl_code_de[0] ?? '');
            $data[$key]['revenueGLCode'] = trim($gl_code_de[1] ?? '');
            $data[$key]['revenueGLDescription'] = trim($gl_code_de[2] ?? '');
            $data[$key]['revenueGLType'] = trim($gl_code_de[3] ?? '');
            $data[$key]['segmentID'] = trim($segment[0] ?? '');
            $data[$key]['segmentCode'] = trim($segment[1] ?? '');
            $data[$key]['transactionAmount'] = round($amount[$key]+$marginAmount[$key], $master['transactionCurrencyDecimalPlaces']);
            $data[$key]['marginPercentage'] = $marginPercentage[$key];
            $data[$key]['marginAmount'] = round($marginAmount[$key], $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = (float)$data[$key]['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $marginLocalAmount = (float)$data[$key]['marginAmount'] / $master['companyLocalExchangeRate'];
            $data[$key]['marginLocalAmount'] = round($marginLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = (float)$data[$key]['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $marginReportingAmount = (float)$data[$key]['marginAmount'] / $master['companyReportingExchangeRate'];
            $data[$key]['marginReportingAmount'] = round($marginReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = (float)$data[$key]['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data[$key]['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $marginCustomerAmount = (float)$data[$key]['marginAmount'] / $master['customerCurrencyExchangeRate'];
            $data[$key]['marginCustomerAmount'] = round($marginCustomerAmount, $master['customerCurrencyDecimalPlaces']);
            $data[$key]['description'] = trim($description[$key]);
            $data[$key]['type'] = 'GL';
            $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$key]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$key]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
                /*$this->db->where('invoiceDetailsAutoID', trim($this->input->post('invoiceDetailsAutoID') ?? ''));
                $this->db->update('srp_erp_customerinvoicedetails', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('invoiceDetailsAutoID'));
                }*/
            } else {
                $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$key]['createdPCID'] = $this->common_data['current_pc'];
                $data[$key]['createdUserID'] = $this->common_data['current_userID'];
                $data[$key]['createdUserName'] = $this->common_data['current_user'];
                $data[$key]['createdDateTime'] = $this->common_data['current_date'];

            }
        }

        $this->db->insert_batch('srp_erp_customerinvoicedetails', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Invoice Detail : Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);

        } else {
            $this->session->set_flashdata('s', 'Invoice Detail Saved Successfully');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function update_income_invoice_detail_margin()
    {
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $segment_gl = $this->input->post('segment_gl');
        $gl_code_des = $this->input->post('gl_code_des');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $projectID = $this->input->post('projectID');
        $gl_code = $this->input->post('gl_code');
        $amount = $this->input->post('amount');
        $marginPercentage = $this->input->post('marginPercentage');
        $marginAmount = $this->input->post('marginAmount');
        $description = $this->input->post('description');
        $projectExist = project_is_exist();

        $segment = explode('|', $segment_gl);
        $gl_code_de = explode(' | ', $gl_code_des);
        $data['invoiceAutoID'] = trim($invoiceAutoID);
        $data['revenueGLAutoID'] = $gl_code;
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['revenueSystemGLCode'] = trim($gl_code_de[0] ?? '');
        $data['revenueGLCode'] = trim($gl_code_de[1] ?? '');
        $data['revenueGLDescription'] = trim($gl_code_de[2] ?? '');
        $data['revenueGLType'] = trim($gl_code_de[3] ?? '');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['marginPercentage'] = $marginPercentage;
        $data['transactionAmount'] = round($amount+$marginAmount, $master['transactionCurrencyDecimalPlaces']);
        $data['marginAmount'] = round($marginAmount, $master['transactionCurrencyDecimalPlaces']);

        $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $marginLocalAmount = $data['marginAmount'] / $master['companyLocalExchangeRate'];
        $data['marginLocalAmount'] = round($marginLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $marginReportingAmount = $data['marginAmount'] / $master['companyReportingExchangeRate'];
        $data['marginReportingAmount'] = round($marginReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
        $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
        $marginCustomerAmount = $data['marginAmount'] / $master['customerCurrencyExchangeRate'];
        $data['marginCustomerAmount'] = round($marginCustomerAmount, $master['customerCurrencyDecimalPlaces']);

        $data['description'] = trim($description);
        $data['type'] = 'GL';
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
            $this->db->where('invoiceDetailsAutoID', trim($this->input->post('invoiceDetailsAutoID') ?? ''));
            $this->db->update('srp_erp_customerinvoicedetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Detail : ' . $data['revenueSystemGLCode'] . ' ' . $data['revenueGLDescription'] . ' Updated Successfully.');
            }
        }
    }

    function fetch_invoice_template_data_temp_margin($invoiceAutoID)
    {

        $convertFormat = convert_date_format_sql();
        $companyID=current_companyID();
        $this->db->select('*,DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDate,\'' . $convertFormat . '\') AS invoiceDate ,DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(srp_erp_customerinvoicemaster.customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate,DATE_FORMAT(srp_erp_customerinvoicemaster.approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->from('srp_erp_customerinvoicemaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax');
        $this->db->where('customerAutoID', $data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();


        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_customerinvoicedetails');
        $data['item_detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_customerinvoicedetails');
        $data['gl_detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['tax'] = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();

        $data['taxledger'] = $this->db->query("SELECT
    tax.taxDescription,tax.taxShortCode,srp_erp_taxledger.taxMasterID,SUM(srp_erp_taxledger.amount)as amount
FROM
    `srp_erp_taxledger`
LEFT JOIN srp_erp_taxmaster tax on srp_erp_taxledger.taxMasterID=tax.taxMasterAutoID
WHERE
    documentMasterAutoID = $invoiceAutoID
AND documentID = 'CINV'
AND srp_erp_taxledger.companyID = $companyID

GROUP BY srp_erp_taxledger.taxMasterID ")->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['extracharge'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['discount'] = $this->db->get('srp_erp_customerinvoicediscountdetails')->result_array();

        return $data;
    }

    function save_insurance_invoice_detail_margin()
    {
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID,srp_erp_invoiceinsurancetypes.GLAutoID as marginGLAutoID');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $this->db->join('srp_erp_invoiceinsurancetypes', 'srp_erp_invoiceinsurancetypes.insuranceTypeID = srp_erp_customerinvoicemaster.insuranceTypeID', 'left');
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $projectExist = project_is_exist();
        $segment_gls = $this->input->post('segment_gl');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $supplierAutoID = $this->input->post('supplierAutoID');
        $projectID = $this->input->post('projectID');
        $amount = $this->input->post('amount');
        $marginPercentage = $this->input->post('marginPercentage');
        $marginAmount = $this->input->post('marginAmount');
        $description = $this->input->post('description');

        foreach ($segment_gls as $key => $segment_gl) {
            $segment = explode('|', $segment_gl);

            $this->db->select('*');
            $this->db->where('supplierAutoID', $supplierAutoID[$key]);
            $supplierdetail = $this->db->get('srp_erp_suppliermaster')->row_array();

            $data[$key]['invoiceAutoID'] = trim($invoiceAutoID);
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data[$key]['projectID'] = $projectID[$key];
                $data[$key]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$key]['supplierAutoID'] = $supplierAutoID[$key];
            $data[$key]['revenueGLAutoID'] = $supplierdetail['liabilityAutoID'];
            $data[$key]['revenueSystemGLCode'] = $supplierdetail['liabilitySystemGLCode'];
            $data[$key]['revenueGLCode'] =  $supplierdetail['liabilityGLAccount'];
            $data[$key]['revenueGLDescription'] = $supplierdetail['liabilityDescription'];
            $data[$key]['revenueGLType'] = $supplierdetail['liabilityType'];
            $data[$key]['segmentID'] = trim($segment[0] ?? '');
            $data[$key]['segmentCode'] = trim($segment[1] ?? '');
            $data[$key]['transactionAmount'] = round($amount[$key], $master['transactionCurrencyDecimalPlaces']);
            $data[$key]['marginPercentage'] = $marginPercentage[$key];
            $data[$key]['marginAmount'] = round($marginAmount[$key], $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = (float)$data[$key]['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $marginLocalAmount = (float)$data[$key]['marginAmount'] / $master['companyLocalExchangeRate'];
            $data[$key]['marginLocalAmount'] = round($marginLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = (float)$data[$key]['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $marginReportingAmount = (float)$data[$key]['marginAmount'] / $master['companyReportingExchangeRate'];
            $data[$key]['marginReportingAmount'] = round($marginReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = (float)$data[$key]['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data[$key]['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $marginCustomerAmount = (float)$data[$key]['marginAmount'] / $master['customerCurrencyExchangeRate'];
            $data[$key]['marginCustomerAmount'] = round($marginCustomerAmount, $master['customerCurrencyDecimalPlaces']);
            $data[$key]['marginGLAutoID'] = $master['marginGLAutoID'];
            $data[$key]['description'] = trim($description[$key]);
            $data[$key]['type'] = 'insurance';
            $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$key]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$key]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($this->input->post('invoiceDetailsAutoID') ?? '')) {

            } else {
                $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$key]['createdPCID'] = $this->common_data['current_pc'];
                $data[$key]['createdUserID'] = $this->common_data['current_userID'];
                $data[$key]['createdUserName'] = $this->common_data['current_user'];
                $data[$key]['createdDateTime'] = $this->common_data['current_date'];

            }
        }

        $this->db->insert_batch('srp_erp_customerinvoicedetails', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Invoice Detail : Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);

        } else {
            $this->session->set_flashdata('s', 'Invoice Detail Saved Successfully');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function save_customer_invoice_details_service_date()
    {
        $this->db->trans_start();
    
     
        $invoiceDetailsAutoID = $this->input->post('invoiceDetailsAutoIDDate');
        $date_format_policy = date_format_policy();
       

        $invoiceType = $this->input->post('invoiceType');
        if($invoiceType=='DirectIncome' || $invoiceType=='DirectItem'){

            $serviceToDate = $this->input->post('serviceToDate');
            if($serviceToDate){
                $serviceToDatef = input_format_date($serviceToDate, $date_format_policy);
                $data['serviceToDate']=trim($serviceToDatef); 
            } else{                
                $data['serviceToDate']="0000-00-00";
            }
        }
                    
        $serviceFromDate = $this->input->post('serviceFromDate');
        $serviceFromDatef = input_format_date($serviceFromDate, $date_format_policy);
        $data['serviceFromDate']=trim($serviceFromDatef);
       
      
        $this->db->where('invoiceDetailsAutoID', $invoiceDetailsAutoID);
        $this->db->update('srp_erp_customerinvoicedetails', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Update failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Successfully Saved');
        }
    }

    function update_income_invoice_detail_insurance()
    {
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $segment_gl = $this->input->post('segment_gl');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $projectID = $this->input->post('projectID');
        $supplierAutoID = $this->input->post('supplierAutoID');
        $amount = $this->input->post('amount');
        $marginPercentage = $this->input->post('marginPercentage');
        $marginAmount = $this->input->post('marginAmount');
        $description = $this->input->post('description');
        $projectExist = project_is_exist();

        $this->db->select('*');
        $this->db->where('supplierAutoID', $supplierAutoID);
        $supplierdetail = $this->db->get('srp_erp_suppliermaster')->row_array();

        $segment = explode('|', $segment_gl);
        $data['invoiceAutoID'] = trim($invoiceAutoID);
        $data['revenueGLAutoID'] = $supplierdetail['liabilityAutoID'];
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['revenueSystemGLCode'] = $supplierdetail['liabilitySystemGLCode'];
        $data['revenueGLCode'] =  $supplierdetail['liabilityGLAccount'];
        $data['revenueGLDescription'] = $supplierdetail['liabilityDescription'];
        $data['revenueGLType'] = $supplierdetail['liabilityType'];
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['marginPercentage'] = $marginPercentage;
        $data['transactionAmount'] = round($amount, $master['transactionCurrencyDecimalPlaces']);
        $data['marginAmount'] = round($marginAmount, $master['transactionCurrencyDecimalPlaces']);

        $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $marginLocalAmount = $data['marginAmount'] / $master['companyLocalExchangeRate'];
        $data['marginLocalAmount'] = round($marginLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $marginReportingAmount = $data['marginAmount'] / $master['companyReportingExchangeRate'];
        $data['marginReportingAmount'] = round($marginReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
        $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
        $marginCustomerAmount = $data['marginAmount'] / $master['customerCurrencyExchangeRate'];
        $data['marginCustomerAmount'] = round($marginCustomerAmount, $master['customerCurrencyDecimalPlaces']);

        $data['description'] = trim($description);
        $data['type'] = 'insurance';
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
            $this->db->where('invoiceDetailsAutoID', trim($this->input->post('invoiceDetailsAutoID') ?? ''));
            $this->db->update('srp_erp_customerinvoicedetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Detail Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Detail Updated Successfully.');
            }
        }
    }

    function delivery_detail($customerID){
        $companyID = current_companyID();
        $data = $this->db->query("SELECT ord.DOAutoID, DOCode, DODate, referenceNo, deliveredTransactionAmount AS transactionAmount, transactionCurrencyDecimalPlaces,
                          (IFNULL(paid_amount,0) + IFNULL(return_amount,0)) AS invoiced_amount
                          FROM srp_erp_deliveryorder ord
                          LEFT JOIN (
                              SELECT DOMasterID, SUM(det.transactionAmount) paid_amount FROM srp_erp_customerinvoicedetails det
                              JOIN srp_erp_customerinvoicemaster mas ON mas.invoiceAutoID = det.invoiceAutoID                              
                              WHERE mas.companyID = {$companyID} AND customerID = {$customerID} GROUP BY DOMasterID
                          ) paidDet ON paidDet.DOMasterID = ord.DOAutoID
                          LEFT JOIN(
                              SELECT returnDet.DOAutoID, SUM(returnDet.totalValue) return_amount
                              FROM srp_erp_salesreturnmaster AS returnMas
                              JOIN srp_erp_salesreturndetails AS returnDet ON returnMas.salesReturnAutoID = returnDet.salesReturnAutoID
                              WHERE returnMas.companyID = {$companyID} AND customerID = {$customerID}  
                              AND returnDet.invoiceAutoID IS NULL GROUP BY returnDet.DOAutoID
                          ) AS return_tb ON return_tb.DOAutoID = ord.DOAutoID
                          WHERE companyID = {$companyID} AND approvedYN = 1 AND customerID = {$customerID}
                          HAVING transactionAmount > invoiced_amount")->result_array();
        //echo '<pre>'.$this->db->last_query().'</pre>';
        return $data;
    }


    function save_inv_discount_detail(){
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $this->db->where('discountMasterAutoID', $this->input->post('discountExtraChargeID'));
        $tax_detail = $this->db->get('srp_erp_customerinvoicediscountdetails')->row_array();
        if (!empty($tax_detail)) {
            $this->session->set_flashdata('w', 'Discount Detail added already ! ');
            return array('status' => true, 'last_id' => null);
        }
        $this->db->select('*');
        $this->db->where('discountExtraChargeID', $this->input->post('discountExtraChargeID'));
        $master = $this->db->get('srp_erp_discountextracharges')->row_array();

        $this->db->select('segmentCode,segmentID,customerCurrencyDecimalPlaces,customerCurrencyExchangeRate,customerCurrencyID,customerCurrency,transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID,companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces,companyReportingCurrency,companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $inv_master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $data['invoiceAutoID']                   = trim($this->input->post('InvoiceAutoID') ?? '');
        $data['discountMasterAutoID']            = $master['discountExtraChargeID'];
        $data['discountDescription']             = $master['Description'];
        $data['isChargeToExpense']               = $master['isChargeToExpense'];
        $data['discountPercentage']              = trim($this->input->post('discountPercentage') ?? '');
        $data['transactionAmount']               = trim($this->input->post('discount_amount') ?? '');
        $data['transactionCurrencyID']           = $inv_master['transactionCurrencyID'];
        $data['transactionCurrency']             = $inv_master['transactionCurrency'];
        $data['transactionExchangeRate']         = $inv_master['transactionExchangeRate'];
        $data['transactionCurrencyDecimalPlaces']= $inv_master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID']          = $inv_master['companyLocalCurrencyID'];
        $data['companyLocalCurrency']            = $inv_master['companyLocalCurrency'];
        $data['companyLocalExchangeRate']        = $inv_master['companyLocalExchangeRate'];
        $data['companyReportingCurrencyID']      = $inv_master['companyReportingCurrencyID'];
        $data['companyReportingCurrency']        = $inv_master['companyReportingCurrency'];
        $data['companyReportingExchangeRate']    = $inv_master['companyReportingExchangeRate'];
        $data['customerCurrencyID']              = $inv_master['customerCurrencyID'];
        $data['customerCurrency']                = $inv_master['customerCurrency'];
        $data['customerCurrencyExchangeRate']    = $inv_master['customerCurrencyExchangeRate'];
        $data['customerCurrencyDecimalPlaces']   = $inv_master['customerCurrencyDecimalPlaces'];
        $data['segmentID']                       = $inv_master['segmentID'];
        $data['segmentCode']                     = $inv_master['segmentCode'];
        $data['customerCurrencyAmount']          =  round(($data['transactionAmount']/$data['customerCurrencyExchangeRate']), $data['customerCurrencyDecimalPlaces']);
        $data['companyLocalAmount']              =  $data['transactionAmount']/$data['companyLocalExchangeRate'];
        $data['companyReportingAmount']          =  $data['transactionAmount']/$data['companyReportingExchangeRate'];
        if(!empty($master['glCode'])){
            $data['GLAutoID']                        = $master['glCode'];
            $gl = fetch_gl_account_desc($master['glCode']);
            $data['systemGLCode']                    = $gl['systemAccountCode'];
            $data['GLCode']                          = $gl['GLSecondaryCode'];
            $data['GLDescription']                   = $gl['GLDescription'];
            $data['GLType']                          = $gl['subCategory'];
        }

        $current_userID = current_userID();
        $current_pc = current_pc();
        $current_user = current_user();
        $current_date = current_date();

        $data['modifiedPCID']                    = $current_pc;
        $data['modifiedUserID']                  = $current_userID;
        $data['modifiedUserName']                = $current_user;
        $data['modifiedDateTime']                = $current_date;
        $data['companyCode']        = current_companyCode();
        $data['companyID']          = current_companyID();
        $data['createdUserGroup']   = current_user_group();
        $data['createdPCID']        = $current_pc;
        $data['createdUserID']      = $current_userID;
        $data['createdUserName']    = $current_user;
        $data['createdDateTime']    = $current_date;
        $this->db->insert('srp_erp_customerinvoicediscountdetails', $data);
        $last_id = $this->db->insert_id();

        /** Added By : (SME-2299)*/
        $invoiceAutoID = $this->input->post('InvoiceAutoID');
        $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoiceAutoID}")->row_array();
        if(!empty($rebate['rebatePercentage'])) {
            $this->calculate_rebate_amount($invoiceAutoID);
        }
        /** End (SME-2299)*/
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Discount Detail Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Discount Detail Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }


    function save_inv_extra_detail(){
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $this->db->where('extraChargeMasterAutoID', $this->input->post('discountExtraChargeIDExtra'));
        $tax_detail = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->row_array();
        if (!empty($tax_detail)) {
            $this->session->set_flashdata('w', 'Extra Charges added already ! ');
            return array('status' => true, 'last_id' => Null);
        }
        $this->db->select('*');
        $this->db->where('discountExtraChargeID', $this->input->post('discountExtraChargeIDExtra'));
        $master = $this->db->get('srp_erp_discountextracharges')->row_array();

        $this->db->select('segmentCode,segmentID,customerCurrencyDecimalPlaces,customerCurrencyExchangeRate,customerCurrencyID,customerCurrency,transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID,companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces,companyReportingCurrency,companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $inv_master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $data['invoiceAutoID']                   = trim($this->input->post('InvoiceAutoID') ?? '');
        $data['extraChargeMasterAutoID']         = $master['discountExtraChargeID'];
        $data['extraChargeDescription']          = $master['Description'];
        $data['isTaxApplicable']                 = $master['isTaxApplicable'];
        $data['transactionAmount']               = trim($this->input->post('extra_amount') ?? '');
        $data['transactionCurrencyID']           = $inv_master['transactionCurrencyID'];
        $data['transactionCurrency']             = $inv_master['transactionCurrency'];
        $data['transactionExchangeRate']         = $inv_master['transactionExchangeRate'];
        $data['transactionCurrencyDecimalPlaces']= $inv_master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID']          = $inv_master['companyLocalCurrencyID'];
        $data['companyLocalCurrency']            = $inv_master['companyLocalCurrency'];
        $data['companyLocalExchangeRate']        = $inv_master['companyLocalExchangeRate'];
        $data['companyReportingCurrencyID']      = $inv_master['companyReportingCurrencyID'];
        $data['companyReportingCurrency']        = $inv_master['companyReportingCurrency'];
        $data['companyReportingExchangeRate']    = $inv_master['companyReportingExchangeRate'];
        $data['customerCurrencyID']              = $inv_master['customerCurrencyID'];
        $data['customerCurrency']                = $inv_master['customerCurrency'];
        $data['customerCurrencyExchangeRate']    = $inv_master['customerCurrencyExchangeRate'];
        $data['customerCurrencyDecimalPlaces']   = $inv_master['customerCurrencyDecimalPlaces'];
        $data['segmentID']                       = $inv_master['segmentID'];
        $data['segmentCode']                     = $inv_master['segmentCode'];
        $data['customerCurrencyAmount']          =  round(($data['transactionAmount']/$data['customerCurrencyExchangeRate']), $data['customerCurrencyDecimalPlaces']);
        $data['companyLocalAmount']              =  $data['transactionAmount']/$data['companyLocalExchangeRate'];
        $data['companyReportingAmount']          =  $data['transactionAmount']/$data['companyReportingExchangeRate'];
        $data['GLAutoID']                        = $master['glCode'];
        $gl = fetch_gl_account_desc($master['glCode']);
        $data['systemGLCode']                    = $gl['systemAccountCode'];
        $data['GLCode']                          = $gl['GLSecondaryCode'];
        $data['GLDescription']                   = $gl['GLDescription'];
        $data['GLType']                          = $gl['subCategory'];

        $current_userID = current_userID();
        $current_pc = current_pc();
        $current_user = current_user();
        $current_date = current_date();

        $data['modifiedPCID']                    = $current_pc;
        $data['modifiedUserID']                  = $current_userID;
        $data['modifiedUserName']                = $current_user;
        $data['modifiedDateTime']                = $current_date;
        $data['companyCode']                     = current_companyCode();
        $data['companyID']                       = current_companyID();
        $data['createdUserGroup']                = current_user_group();
        $data['createdPCID']                     = $current_pc;
        $data['createdUserID']                   = $current_userID;
        $data['createdUserName']                 = $current_user;
        $data['createdDateTime']                 = $current_date;
        $this->db->insert('srp_erp_customerinvoiceextrachargedetails', $data);
        $last_id = $this->db->insert_id();

        /** Added By : (SME-2299)*/
        $invoiceAutoID = $this->input->post('InvoiceAutoID');
        $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoiceAutoID}")->row_array();
        if(!empty($rebate['rebatePercentage'])) {
            $this->calculate_rebate_amount($invoiceAutoID);
        }
        /** End (SME-2299)*/

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Extra Charge Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Extra Charge Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function delete_discount_gen(){
        $discountDetailID = trim($this->input->post('discountDetailID') ?? '');
        $id = $this->db->query("SELECT invoiceAutoID FROM srp_erp_customerinvoicediscountdetails WHERE discountDetailID = {$discountDetailID} AND isChargeToExpense = 0")->row_array();

        $this->db->delete('srp_erp_customerinvoicediscountdetails',array('discountDetailID' => trim($this->input->post('discountDetailID') ?? '')));

        if(!empty($id)) {
            /** Added By : (SME-2299)*/
            $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$id['invoiceAutoID']}")->row_array();
            if(!empty($rebate['rebatePercentage'])) {
                $this->calculate_rebate_amount($id['invoiceAutoID']);
            }
            /** End (SME-2299)*/
        }

        return true;
    }

    function delete_extra_gen(){
        $extraChargeDetailID = trim($this->input->post('extraChargeDetailID') ?? '');
        $id = $this->db->query("SELECT invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE extraChargeDetailID = {$extraChargeDetailID}")->row_array();

        $this->db->delete('srp_erp_customerinvoiceextrachargedetails',array('extraChargeDetailID' => trim($this->input->post('extraChargeDetailID') ?? '')));

        if(!empty($id)) {
            /** Added By : (SME-2299)*/
            $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$id['invoiceAutoID']}")->row_array();
            if(!empty($rebate['rebatePercentage'])) {
                $this->calculate_rebate_amount($id['invoiceAutoID']);
            }
            /** End (SME-2299)*/
        }

        return true;
    }
    function fetch_customer_details_by_id()
    {
        $CustomerAutoID = trim($this->input->post('customerAutoID') ?? '');
        $contractAutoID = trim($this->input->post('contractAutoID') ?? '');
        $companyID = current_companyID();
        $base_arr = array();

        $this->db->select('customerAutoID as cusAuto,customerName,customerTelephone,customerEmail,customerAddress1,customerUrl,customerCreditPeriod');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $CustomerAutoID);
        $base_arr =  $this->db->get()->row_array();

        if($contractAutoID){
            $this->db->where('contractAutoID',$contractAutoID);
            $contract_details = $this->db->where('companyID',$companyID)->from('srp_erp_contractmaster')->get()->row_array();


            $customerID = $contract_details['customerID'];

            if($customerID == $CustomerAutoID){
                $base_arr['customerTelephone'] = ($contract_details['customerTelephone'] != '') ? $contract_details['customerTelephone'] : $base_arr['customerTelephone'];
                $base_arr['customerAddress1'] = ($contract_details['customerAddress'] != '') ? $contract_details['customerAddress'] : $base_arr['customerAddress1'];
                $base_arr['customerEmail'] = ($contract_details['customerEmail'] != '') ? $contract_details['customerEmail'] : $base_arr['customerEmail'];
                $base_arr['customerUrl'] = ($contract_details['customerWebURL'] != '') ? $contract_details['customerWebURL'] : $base_arr['customerUrl'];
                $base_arr['contactPersonName'] = $contract_details['contactPersonName'];
                $base_arr['contactPersonNumber'] = $contract_details['contactPersonNumber'];
            }
            $base_arr['paymentTerms'] = $contract_details['paymentTerms'];
            
        }

        return $base_arr;
        
    }

    function fetch_customer_details_currency()
    {
        $this->db->select('customerCurrencyID,customerCreditPeriod');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', trim($this->input->post('customerAutoID') ?? ''));
        $data['currency'] = $this->db->get()->row_array();

        $this->db->select('customerAutoID as cusAuto,customerName,customerTelephone');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID',trim($this->input->post('customerAutoID') ?? ''));
        $data['detail'] = $this->db->get()->row_array();
        return $data;
    }


    function savesubinsurancetype()
    {
        $companyid = current_companyID();
        $insuranceType = $this->input->post('insuranceType');
        $insuranceTypeID = $this->input->post('insuranceTypeID');
        $masterTypeID = $this->input->post('masterTypeID');
        $marginPercentage = $this->input->post('marginPercentage');
        $noofMonths = $this->input->post('noofMonths');

        $q = "SELECT GLAutoID FROM srp_erp_invoiceinsurancetypes WHERE insuranceTypeID = $masterTypeID" ;
        $master = $this->db->query($q)->row_array();

        $data['insuranceType'] = $insuranceType;
        $data['GLAutoID'] = $master['GLAutoID'];
        $data['marginPercentage'] = $marginPercentage;
        $data['masterTypeID'] = $masterTypeID;
        $data['noofMonths'] = $noofMonths;

        if(!empty($insuranceTypeID))
        {
            $qm = "SELECT insuranceType FROM srp_erp_invoiceinsurancetypes WHERE insuranceType = '{$insuranceType }' AND companyID = $companyid AND masterTypeID = $masterTypeID AND insuranceTypeID != $insuranceTypeID" ;
            $master = $this->db->query($qm)->row_array();
            if(!empty($master)){
                return array('e', 'Sub Insurance Type Already Exist');
            }else
            {
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $this->db->where('insuranceTypeID', $insuranceTypeID);
                $this->db->update('srp_erp_invoiceinsurancetypes', $data);
            }
        }else
        {
            $qm = "SELECT insuranceType FROM srp_erp_invoiceinsurancetypes WHERE insuranceType = '{$insuranceType }' AND companyID = $companyid AND masterTypeID = $masterTypeID" ;
            $master = $this->db->query($qm)->row_array();
            if(!empty($master)){
                return array('e', 'Sub Insurance Type Already Exist');
            }else
            {
                $data['companyID'] = $companyid;
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_invoiceinsurancetypes', $data);
                $last_id = $this->db->insert_id();
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sub Insurance Type Added Failed.');
        } else {
            $this->db->trans_commit();
            return array('s', 'Sub Insurance Type Added Successfully.');
        }
    }

    function load_sub_type()
    {
        $this->db->select('insuranceTypeID,insuranceType');
        $this->db->where('masterTypeID', $this->input->post('insuranceTypeID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_invoiceinsurancetypes');
        return $subtype = $this->db->get()->result_array();
    }

    function get_sub_type_months()
    {
        $this->db->select('noofMonths');
        $this->db->where('insuranceTypeID', $this->input->post('insuranceTypeID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_invoiceinsurancetypes');
        $noofMonths = $this->db->get()->row_array();
        if (!empty($noofMonths)) {

            $date_format_policy = date_format_policy();
            $policyStartDate = $this->input->post('policyStartDate');
            $currDate = input_format_date($policyStartDate, $date_format_policy);

            $months = $noofMonths['noofMonths'];
            $convertFormat = convert_date_format_sql();
            $effectiveDate = date('Y-m-d', strtotime("+$months months", strtotime($currDate)));
            $convertedDate = convert_date_format($effectiveDate);

            return $convertedDate;
        } else {
            return 0;
        }
    }
    function fetch_customer_details()
    {
        $CustomerAutoID = trim($this->input->post('customer') ?? '');
        $this->db->select('customerTelephone,customerCurrencyID');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $CustomerAutoID);
        return $this->db->get()->row_array();
    }

    function open_receipt_voucher_modal(){
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['master']=$this->db->get()->row_array();

        $this->db->select("GLAutoID");
        $this->db->from('srp_erp_chartofaccounts');
        $this->db->where('isDefaultlBank', 1);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data['GL']=$this->db->get()->row_array();

        $customerID = $data['master']['customerID'];
        $RVdate = current_date();
        $currencyID = $data['master']['transactionCurrencyID'];

        $dataw = $this->db->query("SELECT srp_erp_customerinvoicemaster.invoiceAutoID, invoiceCode, receiptTotalAmount, advanceMatchedTotal, creditNoteTotalAmount, referenceNo, (( ( ( cid.transactionAmount - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL(cid.transactionAmount, 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) AS transactionAmount, invoiceDate, slr.returnsalesvalue as salesreturnvalue FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT invoiceAutoID, IFNULL(SUM(transactionAmount), 0) AS transactionAmount, IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID ) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID ) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue FROM srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE confirmedYN = 1 AND approvedYN = 1 AND receiptInvoiceYN = 0 AND `customerID` = '{$customerID}' AND `transactionCurrencyID` = '{$currencyID}' AND invoiceDate <= '{$RVdate}' AND srp_erp_customerinvoicemaster.invoiceAutoID = $invoiceAutoID ")->row_array();
        $data['balance'] = number_format($dataw['transactionAmount'] - ($dataw['receiptTotalAmount'] + $dataw['creditNoteTotalAmount'] + $dataw['advanceMatchedTotal'] + $dataw['salesreturnvalue']),$data['master']['transactionCurrencyDecimalPlaces']);

        return $data;
    }




    function save_receiptvoucher_from_CINV_header()
    {
        $invoiceAutoID=$this->input->post('invoiceAutoID');
        $date_format_policy = date_format_policy();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $RVdates = $this->input->post('RVdate');
        $RVdate = input_format_date($RVdates, $date_format_policy);
        $RVcheqDate = $this->input->post('RVchequeDate');
        $RVchequeDate = input_format_date($RVcheqDate, $date_format_policy);
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $this->db->select('invoiceDate');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $invdate=$this->db->get()->row_array();

        if($RVdate>=$invdate['invoiceDate']){
            if ($financeyearperiodYN == 1) {
                $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
                $FYBegin = input_format_date($financeyr[0], $date_format_policy);
                $FYEnd = input_format_date($financeyr[1], $date_format_policy);
            } else {
                $financeYearDetails = get_financial_year($RVdate);
                if (empty($financeYearDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $FYBegin = $financeYearDetails['beginingDate'];
                    $FYEnd = $financeYearDetails['endingDate'];
                    $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                    $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
                }
                $financePeriodDetails = get_financial_period_date_wise($RVdate);

                if (empty($financePeriodDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
                }
            }
            $this->db->select("segmentCode");
            $this->db->from('srp_erp_segment');
            $this->db->where('segmentID', $this->input->post('segment'));
            $segment = $this->db->get()->row_array();

            $currency_code = fetch_currency_code($this->input->post('transactionCurrencyID'));


            $bank_detail = fetch_gl_account_desc(trim($this->input->post('RVbankCode') ?? ''));
            $data['documentID'] = 'RV';
            $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
            $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
            $data['FYBegin'] = trim($FYBegin);
            $data['FYEnd'] = trim($FYEnd);
            $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');

            $data['RVdate'] = trim($RVdate);
            $data['RVNarration'] = trim_desc($this->input->post('RVNarration'));
            $data['segmentID'] = trim($this->input->post('segment') ?? '');
            $data['segmentCode'] = trim($segment['segmentCode'] ?? '');
            $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
            $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
            $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
            $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
            $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
            $data['RVbank'] = $bank_detail['bankName'];
            $data['RVbankBranch'] = $bank_detail['bankBranch'];
            $data['RVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
            $data['RVbankAccount'] = $bank_detail['bankAccountNumber'];
            $data['RVbankType'] = $bank_detail['subCategory'];
            $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
            $data['RVchequeNo'] = trim($this->input->post('RVchequeNo') ?? '');
            if ($bank_detail['isCash'] == 0) {
                $data['RVchequeDate'] = trim($RVchequeDate);
            } else {
                $data['RVchequeDate'] = null;
            }
            $data['RvType'] = trim($this->input->post('vouchertype') ?? '');
            $data['referanceNo'] = trim_desc($this->input->post('referenceno'));
            $data['RVbankCode'] = trim($this->input->post('RVbankCode') ?? '');

            $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID') ?? ''));
            $data['customerID'] = $customer_arr['customerAutoID'];
            $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
            $data['customerName'] = $customer_arr['customerName'];
            $data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
            $data['customerTelephone'] = $customer_arr['customerTelephone'];
            $data['customerFax'] = $customer_arr['customerFax'];
            $data['customerEmail'] = $customer_arr['customerEmail'];
            $data['customerreceivableAutoID'] = $customer_arr['receivableAutoID'];
            $data['customerreceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
            $data['customerreceivableGLAccount'] = $customer_arr['receivableGLAccount'];
            $data['customerreceivableDescription'] = $customer_arr['receivableDescription'];
            $data['customerreceivableType'] = $customer_arr['receivableType'];
            $data['customerCurrency'] = $customer_arr['customerCurrency'];
            $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
            $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];

            $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
            $data['transactionCurrency'] = trim($currency_code);
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
            $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
            $data['customerExchangeRate'] = $customer_currency['conversion'];
            $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
            $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
            $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
            $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['RVcode'] = 0;

            $result=$this->db->insert('srp_erp_customerreceiptmaster', $data);
            $last_id = $this->db->insert_id();
            if ($result) {
                // update_warehouse_items();
                // update_item_master();

                $currencyID= $this->input->post('transactionCurrencyID');
                $customerID= $this->input->post('customerID');

                $this->db->select('transactionAmount');
                $this->db->from('srp_erp_customerinvoicemaster');
                $this->db->where('invoiceAutoID', $invoiceAutoID);
                $invAmount=$this->db->get()->row_array();

                $data = $this->db->query("SELECT srp_erp_customerinvoicemaster.invoiceAutoID, invoiceCode, receiptTotalAmount, advanceMatchedTotal, creditNoteTotalAmount, referenceNo, (( ( ( cid.transactionAmount - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL(cid.transactionAmount, 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) AS transactionAmount, invoiceDate, slr.returnsalesvalue as salesreturnvalue FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT invoiceAutoID, IFNULL(SUM(transactionAmount), 0) AS transactionAmount, IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID ) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID ) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue FROM srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE confirmedYN = 1 AND approvedYN = 1 AND receiptInvoiceYN = 0 AND `customerID` = '{$customerID}' AND `transactionCurrencyID` = '{$currencyID}' AND invoiceDate <= '{$RVdate}' AND srp_erp_customerinvoicemaster.invoiceAutoID = $invoiceAutoID ")->row_array();
                $balance = $data['transactionAmount'] - ($data['receiptTotalAmount'] + $data['creditNoteTotalAmount'] + $data['advanceMatchedTotal'] + $data['salesreturnvalue']);

                if ($balance > 0) {
                    // echo $invAmount['transactionAmount'] .' | '.$balance;exit;
                    $receiptVoucherAutoID =  $last_id;
                    $settlementAmount =  0;
                    $this->db->select('customerReceivableAutoID,slr.returnsalesvalue as returnsalesvalue,companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,srp_erp_customerinvoicemaster.invoiceAutoID,invoiceCode,referenceNo,invoiceDate,invoiceNarration,(( ( ( cid.transactionAmount - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL(cid.transactionAmount, 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) AS transactionAmount,receiptTotalAmount,advanceMatchedTotal,creditNoteTotalAmount,customerReceivableSystemGLCode,customerReceivableGLAccount,customerReceivableDescription,customerReceivableType,segmentID,segmentCode,transactionCurrencyDecimalPlaces');
                    $this->db->from('srp_erp_customerinvoicemaster');
                    $this->db->join('(SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount,IFNULL(SUM(totalAfterTax ),0) as totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) cid', 'srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID', 'left');

                    $this->db->join('(SELECT
    invoiceAutoID,
    IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue
    from
    srp_erp_salesreturndetails slaesdetail
    GROUP BY invoiceAutoID) slr', 'srp_erp_customerinvoicemaster.invoiceAutoID = slr.invoiceAutoID', 'left');

                    $this->db->join('(SELECT
    SUM(discountPercentage) AS discountPercentage,
        invoiceAutoID
    from
    srp_erp_customerinvoicediscountdetails
    GROUP BY invoiceAutoID) gendiscount', 'srp_erp_customerinvoicemaster.invoiceAutoID = gendiscount.invoiceAutoID', 'left');


                    $this->db->join('(SELECT
    SUM(transactionAmount) AS transactionAmount,
        invoiceAutoID
    from
    srp_erp_customerinvoiceextrachargedetails
    WHERE
        isTaxApplicable = 1
    GROUP BY invoiceAutoID) genexchargistax', 'srp_erp_customerinvoicemaster.invoiceAutoID = genexchargistax.invoiceAutoID', 'left');


                    $this->db->join('(SELECT
    SUM(transactionAmount) AS transactionAmount,
        invoiceAutoID
    from
    srp_erp_customerinvoiceextrachargedetails
    GROUP BY invoiceAutoID) genexcharg', 'srp_erp_customerinvoicemaster.invoiceAutoID = genexcharg.invoiceAutoID', 'left');



                    $this->db->join('(SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID) tax', 'tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID', 'left');
                    $this->db->where_in('srp_erp_customerinvoicemaster.invoiceAutoID', $this->input->post('invoiceAutoID'));
                    $master_recode = $this->db->get()->result_array();
                    $amount = $balance;
                    for ($i = 0; $i < count($master_recode); $i++) {
                        $dataD[$i]['receiptVoucherAutoId'] = $last_id;
                        $dataD[$i]['invoiceAutoID'] = $master_recode[$i]['invoiceAutoID'];
                        $dataD[$i]['type'] = 'Invoice';
                        $dataD[$i]['invoiceCode'] = $master_recode[$i]['invoiceCode'];
                        $dataD[$i]['referenceNo'] = $master_recode[$i]['referenceNo'];
                        $dataD[$i]['invoiceDate'] = $master_recode[$i]['invoiceDate'];
                        $dataD[$i]['GLAutoID'] = $master_recode[$i]['customerReceivableAutoID'];
                        $dataD[$i]['systemGLCode'] = $master_recode[$i]['customerReceivableSystemGLCode'];
                        $dataD[$i]['GLCode'] = $master_recode[$i]['customerReceivableGLAccount'];
                        $dataD[$i]['GLDescription'] = $master_recode[$i]['customerReceivableDescription'];
                        $dataD[$i]['GLType'] = $master_recode[$i]['customerReceivableType'];
                        $dataD[$i]['description'] = $master_recode[$i]['invoiceNarration'];
                        $dataD[$i]['Invoice_amount'] = $master_recode[$i]['transactionAmount'];
                        $dataD[$i]['segmentID'] = $master_recode[$i]['segmentID'];
                        $dataD[$i]['segmentCode'] = $master_recode[$i]['segmentCode'];
                        $dataD[$i]['due_amount'] = ($master_recode[$i]['transactionAmount'] - ($master_recode[$i]['receiptTotalAmount'] + $master_recode[$i]['advanceMatchedTotal'] + $master_recode[$i]['creditNoteTotalAmount'] + $master_recode[$i]['returnsalesvalue']));
                        $dataD[$i]['balance_amount'] = ($dataD[$i]['due_amount'] - round($amount, $master_recode[$i]['transactionCurrencyDecimalPlaces']));
                        $dataD[$i]['transactionAmount'] = round($amount, $master_recode[$i]['transactionCurrencyDecimalPlaces']);
                        $dataD[$i]['companyLocalAmount'] = ($dataD[$i]['transactionAmount'] / $master_recode[$i]['companyLocalExchangeRate']);
                        $dataD[$i]['companyLocalExchangeRate'] = $master_recode[$i]['companyLocalExchangeRate'];
                        $dataD[$i]['companyReportingAmount'] = ($dataD[$i]['transactionAmount'] / $master_recode[$i]['companyReportingExchangeRate']);
                        $dataD[$i]['companyReportingExchangeRate'] = $master_recode[$i]['companyReportingExchangeRate'];
                        $dataD[$i]['customerAmount'] = ($dataD[$i]['transactionAmount'] / $master_recode[$i]['customerCurrencyExchangeRate']);
                        $dataD[$i]['customerCurrencyExchangeRate'] = $master_recode[$i]['customerCurrencyExchangeRate'];
                        $dataD[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataD[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataD[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                        $dataD[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                        $dataD[$i]['modifiedUserName'] = $this->common_data['current_user'];
                        $dataD[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                        $dataD[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $dataD[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $dataD[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $dataD[$i]['createdUserName'] = $this->common_data['current_user'];
                        $dataD[$i]['createdDateTime'] = $this->common_data['current_date'];

                        $grv_m[$i]['invoiceAutoID'] = $invoiceAutoID;
                        $grv_m[$i]['receiptTotalAmount'] = ($master_recode[$i]['receiptTotalAmount'] + $amount);
                        $grv_m[$i]['receiptInvoiceYN'] = 0;
                        if ($dataD[$i]['balance_amount'] <= 0) {
                            $grv_m[$i]['receiptInvoiceYN'] = 1;
                        }
                    }
                    $data_up_settlement['settlementTotal'] =$settlementAmount;
                    $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoID);
                    $this->db->update('srp_erp_customerreceiptmaster', $data_up_settlement);

                    if (!empty($dataD)) {
                        $this->db->update_batch('srp_erp_customerinvoicemaster', $grv_m, 'invoiceAutoID');
                        $this->db->insert_batch('srp_erp_customerreceiptdetail', $dataD);
                        return array('s', 'Receipt Voucher Saved Successfully.',$last_id);
                    } else {
                        $this->db->delete('srp_erp_customerreceiptmaster',array('receiptVoucherAutoId' => trim($last_id)));
                        return array('e', 'Receipt voucher not Created');
                    }

                }else{
                    $this->db->delete('srp_erp_customerreceiptmaster',array('receiptVoucherAutoId' => trim($last_id)));
                    return array('e', 'Balance amount should be greater than zero');
                }
            } else {
                return array('e', 'Receipt Voucher   Saved Failed ');
            }
        }else{
            return array('e', 'Receipt voucher date should be greater than or equal to invoice date');
        }
    }

    function invoice_detail_modal_operation(){
        $invoiceAutoID=$this->input->post('invoiceAutoID');
        $companyID=$this->common_data['company_data']['company_id'];
        $master = $this->db->query("SELECT * FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID= $invoiceAutoID")->row_array();
        $clientID=$master['customerID'];
        $ContCurrencyID=$master['transactionCurrencyID'];

        $detail = $this->db->query("SELECT
    ticketmaster.comments,
    contractRefNo,
    ticketmaster.createdDateTime,
    ticketNo,
    ticketmaster.ticketidAtuto,
    IFNULL( product.TotalCharges, 0 ) AS productValue,
    IFNULL( service.TotalCharges, 0 ) AS serviceValue 
FROM
    ticketmaster
    INNER JOIN srp_erp_customermaster ON ticketmaster.clientID = srp_erp_customermaster.customerAutoID
    INNER JOIN contractmaster ON ticketmaster.contractRefNo = contractmaster.ContractNumber
    LEFT JOIN ( SELECT ticketidAtuto, SUM( TotalCharges ) AS TotalCharges FROM product_service_details WHERE companyID = $companyID AND typeId = 1 GROUP BY ticketidAtuto ) product ON product.ticketidAtuto = ticketmaster.ticketidAtuto
    LEFT JOIN ( SELECT ticketidAtuto, SUM( TotalCharges ) AS TotalCharges FROM product_service_details WHERE companyID = $companyID AND typeId = 2 GROUP BY ticketidAtuto ) service ON service.ticketidAtuto = ticketmaster.ticketidAtuto 
WHERE
    ticketmaster.companyID = '$companyID' 
    AND ticketmaster.approvedYN = 1 
    AND ticketmaster.clientID = $clientID 
    AND contractmaster.ContCurrencyID = $ContCurrencyID 
    AND selectedBillingYN =0")->result_array();

        return $detail;
    }

    function saveopDetails(){
        $ticketidAtuto=$this->input->post('ticketidAtuto');
        $invoiceAutoID=$this->input->post('invoiceAutoID');
        $companyID=$this->common_data['company_data']['company_id'];
        if(empty($ticketidAtuto)){
            return array('e', 'Please select a ticket');
            exit;
        }

        $ticket = $this->db->query("SELECT * FROM srp_erp_customerinvoicedetails WHERE ticketNo=$ticketidAtuto")->row_array();
        if (!empty($ticket)) {
            return array('e', 'Ticket Already exist');
            exit;
        }

        $master = $this->db->query("SELECT * FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID  = $invoiceAutoID")->row_array();
        $datas = $this->db->query("SELECT op_calloff_master.description AS callOffDescription, ticketmaster.ticketidAtuto AS tik, ticketmaster.calloffID, ticketmaster.percentageCompletion, contractdetails.ItemDescrip, t.*, units.* FROM ( SELECT 'service' AS type, TicketproductID AS ID, addedDate, ticketidAtuto, companyID, CustomerID, contractUID, contractDetailID, Description, ItemNum, OurReferance, clientReference, Unit, RateCurrency, UnitRate, sum( Qty ) AS Qty, sum( TotalCharges ) AS TotalCharges, proProposalId, proProposalRevId, proCategoryID, ticketClientSerial, GLCode AS glCode, sum( discount ) AS discount, comments FROM `product_service_details` WHERE companyID = $companyID AND ticketidAtuto = $ticketidAtuto AND typeId = 2 GROUP BY glCode, UnitRate, Unit, contractDetailID UNION ALL SELECT 'product' AS type, TicketproductID AS ID, addedDate, ticketidAtuto, companyID, CustomerID, contractUID, contractDetailID, Description, ItemNum, OurReferance, clientReference, Unit, RateCurrency, UnitRate, sum( Qty ) AS Qty, sum( TotalCharges ) AS TotalCharges, proProposalId, proProposalRevId, proCategoryID, ticketClientSerial, GLCode AS glCode, sum( discount ) AS discount, comments FROM `product_service_details` WHERE companyID = $companyID AND ticketidAtuto = $ticketidAtuto AND typeId = 1 GROUP BY glCode, UnitRate, Unit, contractDetailID ) t INNER JOIN srp_erp_unit_of_measure units ON t.Unit = units.UnitID INNER JOIN contractdetails ON t.contractDetailID = contractdetails.ContractDetailID LEFT JOIN ticketmaster ON ticketmaster.ticketidAtuto = t.ticketidAtuto LEFT JOIN op_calloff_master ON op_calloff_master.calloffID = ticketmaster.calloffID ORDER BY type, ID DESC")->result_array();

        $x = 0;
        if (!empty($datas)) {
            foreach ($datas as $item) {

                $callOff['previousPercentage'] = 0;
                $callOff['totalvalue'] = 0;

                if ($item['calloffID'] > 0 && $item['calloffID'] != '') {

                    $callOff = $this->db->query("SELECT ticketidAtuto,sum(percentageCompletion) as previousPercentage , sum(estimatedProductValue)+sum(estimatedServiceValue) as  totalvalue FROM ticketmaster WHERE calloffID ={$item['calloffID']}  AND selectedBillingYN =1 AND companyID='{$companyID}'")->row_array();
                }

                $GL = fetch_gl_account_desc($item['glCode']);
                $data[$x]['invoiceAutoID'] = $invoiceAutoID;
                $data[$x]['itemDescription'] = $item['ItemDescrip'];
                $data[$x]['type'] = "OP";

                $data[$x]['revenueGLAutoID'] = $GL['GLAutoID'];
                $data[$x]['revenueGLCode'] = $GL['GLSecondaryCode'];
                $data[$x]['revenueSystemGLCode'] = $GL['systemAccountCode'];
                $data[$x]['revenueGLDescription'] = $GL ['GLDescription'];
                $data[$x]['revenueGLType'] = $GL ['subCategory'];


                $data[$x]['defaultUOMID'] = $item['Unit'];
                $data[$x]['defaultUOM'] = $item['UnitShortCode'];
                $data[$x]['unitOfMeasureID'] = $item['Unit'];
                $data[$x]['unitOfMeasure'] = $item['UnitShortCode'];
                $data[$x]['conversionRateUOM'] = 1;
                $data[$x]['contractQty'] = $item['Qty'];
                $data[$x]['contractAmount'] = $item['UnitRate'];
                $data[$x]['requestedQty'] = 0;


                $data[$x]['comment'] = $item['comments'];
                $data[$x]['description'] = $item['Description'];

                $transactionAmount = $item['TotalCharges'];

                $data[$x]['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
                $companyLocalAmount = $data[$x]['transactionAmount'] / $master['companyLocalExchangeRate'];
                $data[$x]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $companyReportingAmount = $data[$x]['companyLocalAmount'] / $master['companyReportingExchangeRate'];
                $data[$x]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $customerAmount = $data[$x]['companyReportingAmount'] / $master['customerCurrencyExchangeRate'];
                $data[$x]['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                $data[$x]['segmentID'] = $master['segmentID'];
                $data[$x]['segmentCode'] = $master['segmentCode'];
                $data[$x]['companyID'] = $master['companyID'];
                $data[$x]['companyCode'] = $master['companyCode'];
                $data[$x]['createdPCID'] = $this->common_data['current_pc'];;
                $data[$x]['createdUserID'] = $this->common_data['current_userID'];
                $data[$x]['createdDateTime'] = $this->common_data['current_date'];
                $data[$x]['createdUserName'] = $this->common_data['current_user'];

                $data[$x]['timestamp'] = $this->common_data['current_date'];;
                $data[$x]['contractDetailType'] = $item['type'];
                $data[$x]['contractDetailID'] = $item['contractDetailID'];
                $data[$x]['ticketNo'] = $item['tik'];
                $data[$x]['currentCertified'] = $item['percentageCompletion'];
                $data[$x]['callOffID'] = $item['calloffID'];
                $data[$x]['callOffDescription'] = $item['callOffDescription'];
                $data[$x]['discountAmount'] = $item['discount'];


                $insert = $this->db->insert('srp_erp_customerinvoicedetails', $data[$x]);


                $x++;
            }
            $this->db->query("UPDATE ticketmaster SET selectedBillingYN=1 WHERE companyID='{$companyID}' AND ticketidAtuto=$ticketidAtuto");

            return array('s', 'Successfully inserted');
            exit;
        }
    }

    function saveRetentionAmnt(){
        $invoiceAutoID=$this->input->post('invoiceAutoID');
        $retentionPercentage=$this->input->post('retentionPercentage');
        $trans_amount=$this->input->post('trans_amount');
        if($retentionPercentage>100){
            return array('e', 'Retention Percentage canot be greater than 100 percent');
            exit;
        }else{

            if($trans_amount>0){
                $master = $this->db->query("SELECT companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces,companyReportingExchangeRate,companyReportingCurrencyDecimalPlaces FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID  = $invoiceAutoID")->row_array();

                $total=$trans_amount;
                $retamnt=$total*($retentionPercentage/100);

                $companyLocalret = $retamnt / $master['companyLocalExchangeRate'];
                $companyLocalret = round($companyLocalret, $master['companyLocalCurrencyDecimalPlaces']);

                $companyReportingret = $retamnt / $master['companyReportingExchangeRate'];
                $companyReportingret = round($companyReportingret, $master['companyReportingCurrencyDecimalPlaces']);

                $data['retentionPercentage'] = $retentionPercentage;
                $data['retensionTransactionAmount'] = $retamnt;
                $data['retensionLocalAmount'] = $companyLocalret;
                $data['retensionReportingAmount'] = $companyReportingret;
                $this->db->where('invoiceAutoID', $invoiceAutoID);
                $result=$this->db->update('srp_erp_customerinvoicemaster', $data);

                if($result){
                    return array('s', 'Successfully updated');
                }
            }else{
                return array('s', 'Successfully updated');
            }

        }
    }

    function create_retention_invoice($invoiceAutoID){
        $master = $this->db->query("SELECT * FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID  = $invoiceAutoID")->row_array();


        $data['documentID'] = 'CINV';
        $data['invoiceType'] = 'Operation';
        $data['isSytemGenerated'] = 1;
        $data['invoiceDate'] = $master['invoiceDate'];
        $data['invoiceDueDate'] = $master['invoiceDueDate'];
        $data['invoiceCode'] = $master['invoiceCode'].'/R';
        $data['referenceNo'] = $master['referenceNo'];
        $data['invoiceNarration'] = $master['invoiceNarration'];
        $data['bankGLAutoID'] = $master['bankGLAutoID'];
        $data['bankSystemAccountCode'] = $master['bankSystemAccountCode'];
        $data['bankGLSecondaryCode'] = $master['bankGLSecondaryCode'];
        $data['bankCurrencyID'] = $master['bankCurrencyID'];
        $data['bankCurrency'] = $master['bankCurrency'];
        $data['invoicebank'] = $master['invoicebank'];
        $data['invoicebankBranch'] = $master['invoicebankBranch'];
        $data['invoicebankSwiftCode'] = $master['invoicebankSwiftCode'];
        $data['invoicebankAccount'] = $master['invoicebankAccount'];
        $data['invoicebankType'] = $master['invoicebankType'];
        $data['companyFinanceYearID'] = $master['companyFinanceYearID'];
        $data['companyFinanceYear'] = $master['companyFinanceYear'];
        $data['FYBegin'] = $master['FYBegin'];
        $data['FYEnd'] = $master['FYEnd'];
        $data['companyFinancePeriodID'] = $master['companyFinancePeriodID'];
        $data['customerID'] = $master['customerID'];
        $data['customerSystemCode'] = $master['customerSystemCode'];
        $data['customerName'] = $master['customerName'];
        $data['customerAddress'] = $master['customerAddress'];
        $data['customerTelephone'] = $master['customerTelephone'];
        $data['customerFax'] = $master['customerFax'];
        $data['customerEmail'] = $master['customerEmail'];
        $data['customerReceivableAutoID'] = $master['customerReceivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $master['customerReceivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $master['customerReceivableGLAccount'];
        $data['customerReceivableDescription'] = $master['customerReceivableDescription'];
        $data['customerReceivableType'] = $master['customerReceivableType'];
        $data['isPrintDN'] = $master['isPrintDN'];
        $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
        $data['transactionCurrency'] = $master['transactionCurrency'];
        $data['transactionExchangeRate'] = $master['transactionExchangeRate'];
        $data['transactionAmount'] = $master['retensionTransactionAmount'];
        $data['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $master['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = $master['retensionLocalAmount'];
        $data['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
        $data['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $master['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = $master['retensionReportingAmount'];
        $data['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
        $data['customerCurrencyID'] = $master['customerCurrencyID'];
        $data['customerCurrency'] = $master['customerCurrency'];
        $data['customerCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
        $data['customerCurrencyAmount'] = 0;
        $data['customerCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
        $data['confirmedYN'] = 1;
        $data['confirmedByEmpID'] = $this->common_data['current_userID'];
        $data['confirmedByName'] = $this->common_data['current_user'];
        $data['confirmedDate'] = $this->common_data['current_date'];
        $data['segmentID'] = $master['segmentID'];
        $data['segmentCode'] = $master['segmentCode'];
        $data['companyID'] = $master['companyID'];
        $data['companyCode'] = $master['companyCode'];
        $data['createdPCID'] = $this->common_data['current_pc'];;
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['isOpYN'] = 1;
        $data['retensionInvoiceID'] = $invoiceAutoID;

        $insert = $this->db->insert('srp_erp_customerinvoicemaster', $data);
        $last_idR = $this->db->insert_id();
        if($insert){
            $dataD['invoiceAutoID'] = $last_idR;
            $dataD['type'] = 'OP';
            $dataD['contractQty'] = 1;
            $dataD['contractAmount'] = $data['transactionAmount'];
            $dataD['description'] = 'Retention Balance';
            $dataD['transactionAmount'] = $data['transactionAmount'];
            $dataD['companyLocalAmount'] = $data['companyLocalAmount'];
            $dataD['companyReportingAmount'] = $data['companyReportingAmount'];
            $dataD['customerAmount'] = $data['transactionAmount'];
            $dataD['segmentID'] = $data['segmentID'];
            $dataD['segmentCode'] = $data['segmentCode'];
            $dataD['companyID'] = $master['companyID'];
            $dataD['companyCode'] = $master['companyCode'];
            $dataD['createdPCID'] = $this->common_data['current_pc'];;
            $dataD['createdUserID'] = $this->common_data['current_userID'];
            $dataD['createdDateTime'] = $this->common_data['current_date'];
            $dataD['createdUserName'] = $this->common_data['current_user'];

            $insertD = $this->db->insert('srp_erp_customerinvoicedetails', $dataD);
            if($insertD){
                $this->load->library('Approvals');
                $approvals_status_cinv = $this->approvals->auto_approve($last_idR, 'srp_erp_customerinvoicemaster','invoiceAutoID', 'CINV',$data['invoiceCode'],$master['invoiceDate']);
                if($approvals_status_cinv==1){
                    $this->save_invoice_approval(0,$last_idR,1,'Auto Approved',1);
                }


            }
        }

        return true;
    }


    function delete_item_direct_op()
    {
        $id = $this->input->post('invoiceDetailsAutoID');

        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicedetails');
        $this->db->where('invoiceDetailsAutoID', $id);
        $rTmp = $this->db->get()->row_array();


        /** update sub item master */

        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $rTmp['invoiceAutoID']);
        $this->db->where('soldDocumentDetailID', $rTmp['invoiceDetailsAutoID']);
        $this->db->where('soldDocumentID', 'CINV');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);

        /** end update sub item master */

        $this->db->where('invoiceDetailsAutoID', $id);
        $results = $this->db->delete('srp_erp_customerinvoicedetails');
        if ($results) {
            $companyID=$this->common_data['company_data']['company_id'];
            $ticketidAtuto=$rTmp['ticketNo'];
            $this->db->query("UPDATE ticketmaster SET selectedBillingYN=0 WHERE companyID='{$companyID}' AND ticketidAtuto=$ticketidAtuto");
            $this->session->set_flashdata('s', 'Invoice Detail Deleted Successfully');
            return true;
        }
    }


    function delete_retention_amout(){
        $invoiceAutoID=$this->input->post('invoiceAutoID');
        $data['retentionPercentage'] = 0;
        $data['retensionTransactionAmount'] = 0;
        $data['retensionLocalAmount'] = 0;
        $data['retensionReportingAmount'] = 0;
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $result=$this->db->update('srp_erp_customerinvoicemaster', $data);

        if($result){
            return array('s', 'Successfully updated');
        }
    }

    function fetch_converted_price_qty_invoice()
    {
        $companyID=$this->common_data['company_data']['company_id'];
        $ContractAutoID = $this->input->post('id');
        $itemAutoID = $this->input->post('itemAutoID');
        $uomID = $this->input->post('uomID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $details = array();

        $data = $this->db->query("SELECT defaultUnitOfMeasureID, companyLocalSellingPrice,companyLocalWacAmount FROM srp_erp_itemmaster WHERE companyID = {$companyID} AND itemAutoID = {$itemAutoID}")->row_array();
        $conversion = conversionRateUOM_id($uomID, $data['defaultUnitOfMeasureID']);
        $details['conversionRate'] = $conversion;
        if(empty($conversion) || $conversion == 0) {
            $conversion = 1;
        }

        $this->load->model('Receipt_voucher_model');
        $pulled_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty($itemAutoID,$wareHouseAutoID);


        if(!empty($wareHouseAutoID)) {
            $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM `srp_erp_itemledger` where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
            $details['qty'] = $stock['currentStock'] * $conversion;
            $details['Unapproved_stock'] = $pulled_stock['Unapproved_stock'] * $conversion;
            $details['qty_pulleddoc'] =  $details['qty'] - ($pulled_stock['Unapproved_stock'] * $conversion);


        } else {
            $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM `srp_erp_itemledger` where itemAutoID="' . $itemAutoID . '" ')->row_array();
            $details['qty'] = $stock['currentStock'] * $conversion;
            $details['Unapproved_stock'] = $pulled_stock['Unapproved_stock'] * $conversion;
            $details['qty_pulleddoc'] =   $details['qty'] - ($pulled_stock['Unapproved_stock'] * $conversion);
        }

        $this->load->model('Item_model');
        $price = $this->Item_model->fetch_sales_price_customerWise($data['companyLocalSellingPrice']);
        $details['price'] = $price['amount'] / $conversion;

        $this->db->select('transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('contractAutoID', $ContractAutoID);
        $result = $this->db->get('srp_erp_contractmaster')->row_array();
        $localCurrency = currency_conversion($result['companyLocalCurrency'], $result['transactionCurrency']);
        $details['localwacamount'] = round( (($data['companyLocalWacAmount'] / $localCurrency['conversion'])/$conversion),$result['transactionCurrencyDecimalPlaces']);


        return $details;
    }

    function calculate_rebate_amount($invoiceAutoID)
    {
        $companyID = current_companyID();
        $master = $this->db->query("SELECT SUM(srp_erp_customerinvoicedetails.transactionAmount - IFNULL(taxAmount, 0)) as transactionAmount, srp_erp_customerinvoicemaster.rebatePercentage
                FROM srp_erp_customerinvoicedetails 
                JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
                WHERE srp_erp_customerinvoicedetails.invoiceAutoID = {$invoiceAutoID} AND srp_erp_customerinvoicedetails.companyID = {$companyID}")->row_array();

        $discount = $this->db->query("SELECT SUM(discountPercentage) AS discountPercentage FROM srp_erp_customerinvoicediscountdetails WHERE invoiceAutoID = {$invoiceAutoID} AND isChargeToExpense = 0 AND companyID = {$companyID}")->row_array();
        $extraCharge = $this->db->query("SELECT SUM(transactionAmount) AS extracharge FROM srp_erp_customerinvoiceextrachargedetails WHERE invoiceAutoID = {$invoiceAutoID} AND companyID = {$companyID}")->row_array();
        $totalDiscount = 0;
        if(!empty($discount)) {
            $totalDiscount += $master['transactionAmount'] * ($discount['discountPercentage'] / 100);
        }
        if(!empty($extraCharge)) {
            $totalDiscount = $totalDiscount - $extraCharge['extracharge'];
        }
        $totalAmount = $master['transactionAmount'] - $totalDiscount;

        $rebateTotal = $totalAmount * ($master['rebatePercentage'] / 100);

        $data['rebateAmount'] = $rebateTotal;

        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->update('srp_erp_customerinvoicemaster', $data);
    }
    function save_project_detail($invoiceAutoID,$projectID)
    {
        $companyid = current_companyID();
        $this->db->query("INSERT INTO srp_erp_customerinvoicedetails ( InvoiceAutoID,type,boqDetailID,isVariation,revenueGLAutoID,revenueGLCode,revenueSystemGLCode,revenueGLDescription,revenueGLType,segmentID,segmentCode,projectID,project_categoryID,project_subCategoryID)
                          select $invoiceAutoID,'Project',srp_erp_boq_details.detailID,1,srp_erp_chartofaccounts.GLAutoID,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode, 
                          srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory,srp_erp_boq_header.segementID,srp_erp_segment.segmentCode,srp_erp_boq_header.projectID,srp_erp_boq_details.categoryID,srp_erp_boq_details.subCategoryID
                          FROM srp_erp_boq_header 
                          LEFT JOIN srp_erp_boq_details ON srp_erp_boq_details.headerID = srp_erp_boq_header.headerID 
                          LEFT JOIN srp_erp_boq_category on srp_erp_boq_category.categoryID = srp_erp_boq_details.categoryID
                          LEFT JOIN srp_erp_segment ON srp_erp_boq_header.segementID = srp_erp_segment.segmentID
                          LEFT JOIN srp_erp_chartofaccounts on srp_erp_chartofaccounts.GLAutoID= srp_erp_boq_category.GLAutoID where 
                          srp_erp_boq_header.companyID = $companyid
                          AND srp_erp_boq_header.projectID = $projectID
                          AND variationAmount > 0");

        $this->db->query("INSERT INTO srp_erp_customerinvoicedetails (InvoiceAutoID,type,boqDetailID,isVariation,revenueGLAutoID,revenueGLCode,revenueSystemGLCode,revenueGLDescription,revenueGLType,segmentID,segmentCode,projectID,project_categoryID,project_subCategoryID)
                          select $invoiceAutoID,'Project',srp_erp_boq_details.detailID,0,srp_erp_chartofaccounts.GLAutoID,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode, 
                          srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory,srp_erp_boq_header.segementID,srp_erp_segment.segmentCode,srp_erp_boq_header.projectID,srp_erp_boq_details.categoryID,srp_erp_boq_details.subCategoryID
                          from srp_erp_boq_header LEFT JOIN srp_erp_boq_details ON srp_erp_boq_details.headerID = srp_erp_boq_header.headerID 
                          LEFT JOIN srp_erp_boq_category on srp_erp_boq_category.categoryID = srp_erp_boq_details.categoryID
                          LEFT JOIN srp_erp_chartofaccounts on srp_erp_chartofaccounts.GLAutoID= srp_erp_boq_category.GLAutoID
                          LEFT JOIN srp_erp_segment ON srp_erp_boq_header.segementID = srp_erp_segment.segmentID
                          where srp_erp_boq_header.companyID = $companyid AND srp_erp_boq_header.projectID = $projectID AND detailID is not null");

        $invoicedetail = $this->db->query("SELECT invoiceDetailsAutoID, boqDetailID,isVariation, IFNULL(srp_erp_customerinvoicedetails.transactionAmount,0) as transactionAmount FROM `srp_erp_customerinvoicedetails` where InvoiceAutoID = $invoiceAutoID AND type = 'Project'")->result_array();
        
        foreach ($invoicedetail as $val)
        {
            $firstinvoiceautoid=$this->db->query("select invoiceAutoID from srp_erp_customerinvoicemaster where  projectID = $projectID ORDER BY invoiceAutoID ASC LIMIT 1")->row('invoiceAutoID');
            $claimpercentage = 0;
            if($invoiceAutoID == $firstinvoiceautoid)
            {
                $claimpercentage = 0;
            }else
            {
                $prevclaimedpercentage = $this->db->query("select SUM(boqClaimPercentage) as  previousclaimed from srp_erp_customerinvoicedetails where boqDetailID = '{$val['boqDetailID']}' AND invoiceDetailsAutoID !='{$val['invoiceDetailsAutoID']}' AND isVariation = '{$val['isVariation']}'
                                                    GROUP BY boqDetailID")->row('previousclaimed');

                if(!empty($prevclaimedpercentage) || ($prevclaimedpercentage!=''))
                    {
                        $claimpercentage = $prevclaimedpercentage;
                    }
            }
            $this->db->trans_start();
            $data['boqPreviousClaimPercentage'] = $claimpercentage;
            $data['boqPreviousClaimedAmount'] = $val['transactionAmount'];
            $this->db->where('invoiceDetailsAutoID', $val['invoiceDetailsAutoID']);
            $this->db->update('srp_erp_customerinvoicedetails', $data);
            $this->db->trans_complete();
        }
    }
    function updateRVMconfirmstatus($invoiceAutoID)
    {
        $rvmdetail = $this->db->query("SELECT matchID FROM `srp_erp_rvadvancematch` WHERE matchinvoiceAutoID = $invoiceAutoID")->row_array();
        $invoicedetail=  $this->db->query("SELECT invoiceCode FROM `srp_erp_customerinvoicemaster` WHERE invoiceAutoID = $invoiceAutoID ")->row_array();
        if(!empty($rvmdetail['matchID']))
        {
            $data = array(
                'confirmedYN' => 1,
                'Narration' => 'Receipt Voucher Auto Generated ('.$invoicedetail['invoiceCode'].')',
                'confirmedDate' => current_date(),
                'confirmedByEmpID' => current_userID(),
                'confirmedByName' => current_user()
            );

            $this->db->where('matchID', $rvmdetail['matchID']);
            $confirmation = $this->db->update('srp_erp_rvadvancematch', $data);

        }
    }

    function getWareHouseItemQty_bulk()
    {
        $contractDetID = $this->input->post('contractDetID');
        $warehouseAutoID = $this->input->post('warehouseAutoID');

        $contractAutoID = $this->db->query("SELECT contractAutoID FROM srp_erp_contractdetails WHERE contractDetailsAutoID = {$contractDetID}")->row('contractAutoID');
        $result = $this->db->query("SELECT contractDetailsAutoID, conDet.itemAutoID, IFNULL(SUM(transactionQTY / convertionRate), 0) * conDet.conversionRateUOM AS currentStock FROM srp_erp_contractdetails conDet JOIN srp_erp_itemledger items ON items.itemAutoID = conDet.itemAutoID AND wareHouseAutoID = {$warehouseAutoID} WHERE contractAutoID = {$contractAutoID} GROUP BY contractDetailsAutoID")->result_array();

        return $result;

    }

    function save_invoice_approval_cs($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0,$isRetentionYN=0)
    {

        $this->load->library('Approvals');
        if($autoappLevel==1){
            $system_id = trim($this->input->post('invoiceAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        }else {
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['invoiceAutoID']=$system_id;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }
        $companyID = current_companyID();
        $sql = "SELECT srp_erp_itemmaster.itemSystemCode, srp_erp_itemmaster.itemDescription, 
                SUM( cus_inv.requestedQty / cus_inv.conversionRateUOM ) AS qty,IFNULL(ware_house.currentStock,0) as currentStock ,IFNULL( ware_house.currentStock,0) as availableStock,
                ( IFNULL(ware_house.currentStock,0)  - IFNULL( SUM( cus_inv.requestedQty / cus_inv.conversionRateUOM ),0) ) AS stock,
                ware_house.itemAutoID, cus_inv.wareHouseAutoID
                FROM srp_erp_customerinvoicedetails AS cus_inv
                LEFT JOIN ( 
                    SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID FROM srp_erp_itemledger WHERE companyID ={$companyID} GROUP BY wareHouseAutoID, itemAutoID 
                ) AS ware_house ON ware_house.itemAutoID = cus_inv.itemAutoID AND ware_house.wareHouseAutoID = cus_inv.wareHouseAutoID
                JOIN srp_erp_itemmaster ON cus_inv.itemAutoID = srp_erp_itemmaster.itemAutoID
                WHERE invoiceAutoID = '{$system_id}'
                AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' )
                GROUP BY itemAutoID
                HAVING stock < 0";


        $items_arr = $this->db->query($sql)->result_array();
      
        if($status!=1){
            $items_arr='';
        }
        if (!$items_arr) {
            if($autoappLevel == 0){
                $approvals_status = 1;
            }else{
                $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'CINV');
            }
            //print_r($approvals_status);exit();
            if ($approvals_status == 1 && $isRetentionYN==0) {
                $this->db->select('*');
                $this->db->where('invoiceAutoID', $system_id);
                $this->db->from('srp_erp_customerinvoicemaster');
                $master = $this->db->get()->row_array();
                $this->db->select('*');
                $this->db->where('invoiceAutoID', $system_id);
                $this->db->from('srp_erp_customerinvoicedetails');
                $invoice_detail = $this->db->get()->result_array();

                if($master['retentionPercentage']>0){
                    $this->create_retention_invoice($system_id);
                }

                if($master["invoiceType"] != "Manufacturing") {
                    if($master["invoiceType"] != "Insurance") {
                        for ($a = 0; $a < count($invoice_detail); $a++) {
                            if ($invoice_detail[$a]['type'] == 'Item' || $invoice_detail[$a]['type'] == 'Commission') {
                                $item = fetch_item_data($invoice_detail[$a]['itemAutoID']);
                                if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory' or $item['mainCategory'] =='Service') {
                                    $itemAutoID = $invoice_detail[$a]['itemAutoID'];
                                    $qty = $invoice_detail[$a]['requestedQty'] / $invoice_detail[$a]['conversionRateUOM'];
                                    $wareHouseAutoID = $invoice_detail[$a]['wareHouseAutoID'];
                                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");

                                    $item_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                    $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                                    $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                                    $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                                    if (!empty($item_arr)) {
                                        $this->db->where('itemAutoID', trim($invoice_detail[$a]['itemAutoID']));
                                        $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                                    }
                                    $itemledger_arr[$a]['documentID'] = $master['documentID'];
                                    $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                                    $itemledger_arr[$a]['documentAutoID'] = $master['invoiceAutoID'];
                                    $itemledger_arr[$a]['documentSystemCode'] = $master['invoiceCode'];
                                    $itemledger_arr[$a]['documentDate'] = $master['invoiceDate'];
                                    $itemledger_arr[$a]['referenceNumber'] = $master['referenceNo'];
                                    $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                                    $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                                    $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                                    $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                                    $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                                    $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                                    $itemledger_arr[$a]['wareHouseAutoID'] = $invoice_detail[$a]['wareHouseAutoID'];
                                    $itemledger_arr[$a]['wareHouseCode'] = $invoice_detail[$a]['wareHouseCode'];
                                    $itemledger_arr[$a]['wareHouseLocation'] = $invoice_detail[$a]['wareHouseLocation'];
                                    $itemledger_arr[$a]['wareHouseDescription'] = $invoice_detail[$a]['wareHouseDescription'];
                                    $itemledger_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                    $itemledger_arr[$a]['itemSystemCode'] = $invoice_detail[$a]['itemSystemCode'];
                                    $itemledger_arr[$a]['itemDescription'] = $invoice_detail[$a]['itemDescription'];
                                    $itemledger_arr[$a]['SUOMID'] = $invoice_detail[$a]['SUOMID'];
                                    $itemledger_arr[$a]['SUOMQty'] = $invoice_detail[$a]['SUOMQty'];
                                    $itemledger_arr[$a]['defaultUOMID'] = $invoice_detail[$a]['defaultUOMID'];
                                    $itemledger_arr[$a]['defaultUOM'] = $invoice_detail[$a]['defaultUOM'];
                                    $itemledger_arr[$a]['transactionUOMID'] = $invoice_detail[$a]['unitOfMeasureID'];
                                    $itemledger_arr[$a]['transactionUOM'] = $invoice_detail[$a]['unitOfMeasure'];
                                    $itemledger_arr[$a]['transactionQTY'] = ($invoice_detail[$a]['requestedQty'] * -1);
                                    $itemledger_arr[$a]['convertionRate'] = $invoice_detail[$a]['conversionRateUOM'];
                                    $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                                    $itemledger_arr[$a]['PLGLAutoID'] = $item['costGLAutoID'];
                                    $itemledger_arr[$a]['PLSystemGLCode'] = $item['costSystemGLCode'];
                                    $itemledger_arr[$a]['PLGLCode'] = $item['costGLCode'];
                                    $itemledger_arr[$a]['PLDescription'] = $item['costDescription'];
                                    $itemledger_arr[$a]['PLType'] = $item['costType'];
                                    $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                                    $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                                    $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                                    $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                                    $itemledger_arr[$a]['BLType'] = $item['assteType'];
                                    $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                                    $ex_rate_wac = (1 / $master['companyLocalExchangeRate']);
                                    $itemledger_arr[$a]['transactionAmount'] = round((($invoice_detail[$a]['companyLocalWacAmount'] / $ex_rate_wac) * ($itemledger_arr[$a]['transactionQTY'] / $invoice_detail[$a]['conversionRateUOM'])), $itemledger_arr[$a]['transactionCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['salesPrice'] = (($invoice_detail[$a]['transactionAmount'] / ($itemledger_arr[$a]['transactionQTY'] / $invoice_detail[$a]['conversionRateUOM'])) * -1);
                                    $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                                    $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                                    $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                                    $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                                    $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                                    $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                    $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                                    $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                                    $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                                    $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                    $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                                    $itemledger_arr[$a]['partyCurrencyID'] = $master['customerCurrencyID'];
                                    $itemledger_arr[$a]['partyCurrency'] = $master['customerCurrency'];
                                    $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                                    $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                                    $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                                    $itemledger_arr[$a]['confirmedByName'] = $master['confirmedByName'];
                                    $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDate'];
                                    $itemledger_arr[$a]['approvedYN'] = $master['approvedYN'];
                                    $itemledger_arr[$a]['approvedDate'] = $master['approvedDate'];
                                    $itemledger_arr[$a]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                                    $itemledger_arr[$a]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                                    $itemledger_arr[$a]['segmentID'] = $master['segmentID'];
                                    $itemledger_arr[$a]['segmentCode'] = $master['segmentCode'];
                                    $itemledger_arr[$a]['companyID'] = $master['companyID'];
                                    $itemledger_arr[$a]['companyCode'] = $master['companyCode'];
                                    $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                                    $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                                    $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                                    $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                                    $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                                    $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                                    $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                                    $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                                    $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];
                                }
                            }
                        }
                        if (!empty($itemledger_arr)) {
                            $itemledger_arr = array_values($itemledger_arr);
                            $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                        }
                    }
                    $this->load->model('Double_entry_model');
                    if($master["invoiceType"] != "Insurance") {
                        $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data($system_id, 'CINV');
                    }else{
                        $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data_insurance($system_id, 'CINV');
                    }

                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
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
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
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
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                        $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
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

                }else{
                    for ($a = 0; $a < count($invoice_detail); $a++) {
                        if ($invoice_detail[$a]['type'] == 'Item' || $invoice_detail[$a]['type'] == 'Commission') {
                            $item = fetch_item_data($invoice_detail[$a]['itemAutoID']);
                            if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                                $itemAutoID = $invoice_detail[$a]['itemAutoID'];
                                $qty = $invoice_detail[$a]['requestedQty'] / $invoice_detail[$a]['conversionRateUOM'];
                                $wareHouseAutoID = $invoice_detail[$a]['wareHouseAutoID'];

                                $item_arr[$a]['itemAutoID'] = $invoice_detail[$a]['itemAutoID'];
                                $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                                $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                                $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($item['companyReportingWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), wacDecimalPlaces);

                            }
                        }
                    }

                    $this->load->model('Double_entry_model');
                    $double_entry = $this->Double_entry_model->fetch_double_entry_mfq_customer_invoice_data($system_id, 'CINV');
                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
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
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
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
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
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

                }

                $this->db->select_sum('transactionAmount');
                $this->db->where('invoiceAutoID', $system_id);
                $total = $this->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');

                $data['approvedYN'] = $status;
                $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                $data['approvedbyEmpName'] = $this->common_data['current_user'];
                $data['approvedDate'] = $this->common_data['current_date'];

                $this->db->where('invoiceAutoID', $system_id);
                $this->db->update('srp_erp_customerinvoicemaster', $data);
                //$this->session->set_flashdata('s', 'Invoice Approval Successfully.');
                if($master["invoiceType"] == "Commission"){

                    $invoiceCommissionDet = "SELECT
                        invdet.invoiceDetailsAutoID as invoiceDetailID,
                        invdet.invoiceAutoID as invoiceAutoID,
                        invdet.type as type,
                        invdet.salesPersonID as salesPersonID,
                        invdet.designationID as DesignationID,
                        invdet.itemAutoID as itemAutoID,
                         ch.reportingEmployeeID as reportingEmployeeID,
                        ch.reportingDesignationID as reportingDesignationID,
                        srp_empdepartments.DepartmentMasterID as DepartmentMasterID,
                        schemedetail.commisionAmount as commisionAmount,
                        schemedetail.schemeMasterID as schemeID,
                        schemedetail.schemeDetailID as schemeDetailID,
                        invdet.requestedQty as requestedQty,
                        IFNULL(schemedetail.commisionAmount *   invdet.requestedQty,0) AS totalcommisionAmount,
                         ch.commissionHierarchyID as commissionHierarchyID
                        FROM
                            srp_erp_customerinvoicedetails invdet
                        LEFT JOIN srp_erp_customerinvoicemaster on invdet.invoiceAutoID=srp_erp_customerinvoicemaster.invoiceAutoID
                        JOIN srp_erp_commission_hierachy ch on invdet.salesPersonID = ch.employeeID AND ch.designationID = invdet.designationID AND ch.isDeleted = 0 
                        LEFT JOIN srp_empdepartments on invdet.salesPersonID = srp_empdepartments.EmpID and isPrimary = 1
                        JOIN (
                            SELECT
                                srp_erp_commisionschemedetails.schemeDetailID as schemeDetailID,
                                srp_erp_commisionschemedetails.schemeMasterID as schemeMasterID,
                                srp_erp_commisionschemedetails.itemAutoID as itemAutoID,
                                srp_erp_commisionschemedetails.designationID as designationID,
                                srp_erp_commisionschemedetails.commisionAmount as commisionAmount,
                                srp_erp_commisionscheme.departmentID as departmentID
                            FROM
                                srp_erp_commisionschemedetails
                                JOIN srp_erp_commisionscheme on srp_erp_commisionschemedetails.schemeMasterID = srp_erp_commisionscheme.schemeID
                                WHERE isActive=1
                        ) schemedetail ON schemedetail.itemAutoID = invdet.itemAutoID
                            AND  invdet.designationID=schemedetail.designationID 
                            AND srp_empdepartments.DepartmentMasterID=schemedetail.departmentID
                        WHERE srp_erp_customerinvoicemaster.invoiceAutoID={$system_id}";
                    $invoiceCommissionDetail = $this->db->query($invoiceCommissionDet)->result_array();
                    if($invoiceCommissionDetail){
                        $datacom['invoiceID'] = $system_id;
                        $this->load->library('sequence');
                        $datacom['documentSystemCode'] = $this->sequence->sequence_generator('IC');;
                        $datacom['companyID'] = current_companyID();
                        $datacom['createdUserGroup'] = $this->common_data['user_group'];
                        $datacom['createdPCID'] = $this->common_data['current_pc'];
                        $datacom['createdUserID'] = $this->common_data['current_userID'];
                        $datacom['createdDateTime'] = $this->common_data['current_date'];
                        $datacom['createdUserName'] = $this->common_data['current_user'];

                        $comresult=$this->db->insert('srp_erp_invoice_commision', $datacom);
                        $last_idcom = $this->db->insert_id();

                        if($comresult){
                            foreach($invoiceCommissionDetail as $detl){
                                $datacomd['commissionAutoID'] = $last_idcom;
                                $datacomd['invoiceDetailID'] = $detl['invoiceDetailID'];
                                $datacomd['salesPersonEmpID'] = $detl['salesPersonID'];
                                $datacomd['designationID'] = $detl['DesignationID'];
                                $datacomd['empID'] = $detl['reportingEmployeeID'];
                                $datacomd['commissionAmount'] = $detl['totalcommisionAmount'];
                                $datacomd['FixedCommissionAmount'] = $detl['totalcommisionAmount'];
                                $datacomd['schemeID'] = $detl['schemeID'];
                                $datacomd['schmeDetailID'] = $detl['schemeDetailID'];
                                $datacomd['companyID'] = $this->common_data['company_data']['company_id'];
                                $datacomd['createdUserGroup'] = $this->common_data['user_group'];
                                $datacomd['createdPCID'] = $this->common_data['current_pc'];
                                $datacomd['createdUserID'] = $this->common_data['current_userID'];
                                $datacomd['createdUserName'] = $this->common_data['current_user'];
                                $datacomd['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_invoice_commission_detail', $datacomd);
                                if(!empty($detl['reportingEmployeeID'])){
                                    $treedetails = $this->add_commission_details_tree($detl['reportingEmployeeID'],$detl['reportingDesignationID'],$detl['schemeID'],$detl['requestedQty'],$detl['itemAutoID'],$detl['invoiceDetailID'], $last_idcom,$detl['commissionHierarchyID']);
                                    if (!empty($treedetails)) {
                                        $this->db->insert_batch('srp_erp_invoice_commission_detail', $treedetails);
                                    }
                                }

                            }
                        }
                    }



                  }
                if($master["invoiceType"] == "Insurance") {
                    $sumsup = "SELECT (sum(transactionAmount)-sum(marginAmount)) as transactionAmount,
                    srp_erp_customerinvoicedetails.supplierAutoID as supplierAutoID,
                    srp_erp_customerinvoicedetails.segmentID as segmentID,
                    srp_erp_customerinvoicedetails.segmentCode as segmentCode,
                    srp_erp_suppliermaster.supplierName as supplierName,
                    srp_erp_suppliermaster.supplierSystemCode as supplierSystemCode,
                    srp_erp_suppliermaster.supplierAddress1 as supplierAddress,
                    srp_erp_suppliermaster.supplierTelephone as supplierTelephone,
                    srp_erp_suppliermaster.supplierFax as supplierFax,
                    srp_erp_suppliermaster.liabilityAutoID as liabilityAutoID,
                    srp_erp_suppliermaster.liabilitySystemGLCode as liabilitySystemGLCode,
                    srp_erp_suppliermaster.liabilityGLAccount as liabilityGLAccount,
                    srp_erp_suppliermaster.liabilityDescription as liabilityDescription,
                    srp_erp_suppliermaster.liabilityType as liabilityType,
                    srp_erp_suppliermaster.supplierCurrencyID as supplierCurrencyID,
                    srp_erp_suppliermaster.supplierCurrency as supplierCurrency,
                    srp_erp_suppliermaster.supplierCurrencyDecimalPlaces as supplierCurrencyDecimalPlaces
                    FROM
                        `srp_erp_customerinvoicedetails`
                    Left JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_customerinvoicedetails.supplierAutoID
                    WHERE
                        `invoiceAutoID` = $system_id
                        GROUP BY
                            supplierAutoID";
                    $sumsupdetail = $this->db->query($sumsup)->result_array();
                    $this->load->library('sequence');
                    $invdate=explode("-",$master['invoiceDate']);

                    foreach($sumsupdetail as $val){
                        $datasup['documentID'] = 'BSI';
                        $datasup['invoiceType'] = 'Standard';
                        $datasup['companyFinanceYearID'] = $master['companyFinanceYearID'];
                        $datasup['companyFinanceYear'] = $master['companyFinanceYear'];
                        $datasup['warehouseAutoID'] = $master['wareHouseAutoID'];
                        $datasup['isSytemGenerated'] = 1;
                        $datasup['documentOrigin'] = 'CINV';
                        $datasup['documentOriginAutoID'] = $system_id;
                        $datasup['FYBegin'] = $master['FYBegin'];
                        $datasup['FYEnd'] = $master['FYEnd'];
                        $datasup['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                        $datasup['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                        $datasup['companyFinancePeriodID'] = $master['companyFinancePeriodID'];
                        $datasup['bookingInvCode'] = $this->sequence->sequence_generator_fin('BSI',$master['companyFinanceYearID'],$invdate[0],$invdate[1]);
                        $datasup['bookingDate'] = $master['invoiceDate'];
                        $datasup['invoiceDate'] = $master['invoiceDate'];
                        $datasup['invoiceDueDate'] = $master['invoiceDueDate'];
                        $datasup['comments'] = 'From custome invoice '.$master['invoiceCode'];
                        $datasup['RefNo'] = $master['invoiceCode'];
                        $datasup['supplierID'] = $val['supplierAutoID'];
                        $datasup['supplierCode'] = $val['supplierSystemCode'];
                        $datasup['supplierName'] = $val['supplierName'];
                        $datasup['supplierAddress'] = $val['supplierAddress'];
                        $datasup['supplierTelephone'] = $val['supplierTelephone'];
                        $datasup['supplierFax'] = $val['supplierFax'];
                        $datasup['supplierliabilityAutoID'] = $val['liabilityAutoID'];
                        $datasup['supplierliabilitySystemGLCode'] = $val['liabilitySystemGLCode'];
                        $datasup['supplierliabilityGLAccount'] = $val['liabilityGLAccount'];
                        $datasup['supplierliabilityDescription'] = $val['liabilityDescription'];
                        $datasup['supplierliabilityType'] = $val['liabilityType'];
                        $datasup['supplierInvoiceDate'] = $master['invoiceDate'];
                        $datasup['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $datasup['transactionCurrency'] = $master['transactionCurrency'];
                        $datasup['transactionExchangeRate'] = $master['transactionExchangeRate'];
                        $datasup['transactionAmount'] = $val['transactionAmount'];
                        $datasup['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $datasup['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $datasup['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $datasup['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $datasup['companyLocalAmount'] = $val['transactionAmount']/$master['companyLocalExchangeRate'];
                        $datasup['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                        $datasup['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $datasup['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $datasup['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                        $datasup['companyReportingAmount'] = $val['transactionAmount']/$master['companyReportingExchangeRate'];
                        $datasup['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                        $datasup['supplierCurreinvoicencyID'] = $val['supplierCurrencyID'];
                        $datasup['supplierCurrency'] = $val['supplierCurrency'];
                        $datasup['segmentID'] = $val['segmentID'];
                        $datasup['segmentCode'] = $val['segmentCode'];
                        $datasup['companyID'] = current_companyID();
                        $datasup['companyCode'] = current_companyCode();
                        $supplier_currency = currency_conversionID($master['transactionCurrencyID'], $val['supplierCurrencyID']);
                        $datasup['supplierCurrencyExchangeRate'] = $supplier_currency['conversion'];
                        $datasup['supplierCurrencyAmount'] = $val['transactionAmount']/$supplier_currency['conversion'];
                        $datasup['supplierCurrencyDecimalPlaces'] = $val['supplierCurrencyDecimalPlaces'];
                        $datasup['confirmedYN'] = 1;
                        $datasup['confirmedByEmpID'] = current_userID();
                        $datasup['confirmedByName'] = current_user();
                        $datasup['confirmedDate'] = $this->common_data['current_date'];
                        $datasup['createdUserGroup'] = $this->common_data['user_group'];
                        $datasup['createdPCID'] = $this->common_data['current_pc'];
                        $datasup['createdUserID'] = $this->common_data['current_userID'];
                        $datasup['createdDateTime'] = $this->common_data['current_date'];
                        $datasup['createdUserName'] = $this->common_data['current_user'];

                        $supresult=$this->db->insert('srp_erp_paysupplierinvoicemaster', $datasup);
                        $last_idsup = $this->db->insert_id();
                        if($supresult){
                            $supid=$val['supplierAutoID'];
                            $supd = "SELECT * FROM `srp_erp_customerinvoicedetails` WHERE `invoiceAutoID` = $system_id AND `supplierAutoID` = $supid";
                            $supdetail = $this->db->query($supd)->result_array();

                            foreach($supdetail as $detl){
                                $datasupd['InvoiceAutoID'] = $last_idsup;
                                $datasupd['segmentID'] = $detl['segmentID'];
                                $datasupd['segmentCode'] = $detl['segmentCode'];
                                $datasupd['description'] = $detl['description'];
                                $datasupd['GLCode'] = "-";
                                $datasupd['transactionAmount'] = round($detl['transactionAmount']-$detl['marginAmount'],$master['transactionCurrencyDecimalPlaces']);
                                $datasupd['transactionExchangeRate'] = $master['transactionExchangeRate'];
                                $datasupd['companyLocalAmount'] = round($datasupd['transactionAmount']/$master['companyLocalExchangeRate'], $master['companyLocalCurrencyDecimalPlaces']);
                                $datasupd['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                $datasupd['companyReportingAmount'] = round($datasupd['transactionAmount']/$master['companyReportingExchangeRate'], $master['companyReportingCurrencyDecimalPlaces']);
                                $datasupd['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                $datasupd['supplierAmount'] = round($datasupd['transactionAmount']/$datasup['supplierCurrencyExchangeRate'], $datasup['supplierCurrencyDecimalPlaces']);
                                $datasupd['supplierCurrencyExchangeRate'] = $datasup['supplierCurrencyExchangeRate'];
                                $datasupd['companyCode'] = $this->common_data['company_data']['company_code'];
                                $datasupd['companyID'] = $this->common_data['company_data']['company_id'];
                                $datasupd['createdUserGroup'] = $this->common_data['user_group'];
                                $datasupd['createdPCID'] = $this->common_data['current_pc'];
                                $datasupd['createdUserID'] = $this->common_data['current_userID'];
                                $datasupd['createdUserName'] = $this->common_data['current_user'];
                                $datasupd['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_paysupplierinvoicedetail', $datasupd);
                            }
                            $this->load->library('Approvals');
                            $approvals_status_sup = $this->approvals->auto_approve($last_idsup, 'srp_erp_paysupplierinvoicemaster','InvoiceAutoID', 'BSI',$master['invoiceDate'],$master['invoiceDate']);
                            if($approvals_status_sup==1){
                                $this->load->model('Payable_modal');
                                $this->Payable_modal->save_supplier_invoice_approval(0, $last_idsup, 1, 'Auto Approved');
                            }
                        }
                    }
                }
            }else{
                if($isRetentionYN==1)
                {
                    $this->load->model('Double_entry_model');
                    $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data_opr($system_id, 'CINV');

                    //echo '<pre>';print_r($double_entry['gl_detail']); echo '</pre>';
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentType'] = '';
                        $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                        $generalledger_arr[$i]['chequeNumber'] = '';
                        $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
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
                        $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                        $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
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
                        $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                        $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                        $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                        $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                        $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                        $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                        $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                        $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                        $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                        $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                        $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                        $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
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
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Approval Failed.', 1);
            } else {
               // $this->db->trans_rollback();
               $this->db->trans_commit();
                if($master['invoiceType']='Project')
                {
                    $this->updateRVMconfirmstatus($system_id);
                }
                return array('s', 'Invoice Approval Successfull.', 1);
            }
        } else {
            return array('e', 'Some Item quantities are not sufficient to approve this transaction.', $items_arr);
        }
    }
    function add_commission_details_tree($empID,$designationID,$schemeID,$requestedQty,$itemAutoID,$invoiceDetailID,$commissionAutoID,$commissionHierarchyID){
        $emp_manager_array[]='';
        $companyID = current_companyID();
        $this->db->select('employeeID,reportingEmployeeID');
        $this->db->from('srp_erp_commission_hierachy');
        $this->db->where('employeeID',$empID);
        $this->db->where('designationID', $designationID );
        $this->db->where('isDeleted', 0 );
        $this->db->where('companyID', $companyID );
        $this->db->where('commissionHierarchyID !=', $commissionHierarchyID );
        $details = $this->db->get()->row_array();

        if ($details){
            $x = 0;
            /*
            if ($details['employeeID'] ==  $details['reportingEmployeeID']){*/

                /*$this->db->select(' ch.employeeID as employeeID,
                        ch.reportingEmployeeID as reportingEmployeeID,
                        ch.designationID as DesignationID, 
                        ch.reportingDesignationID as reportingDesignationID,
                        srp_erp_commisionschemedetails.schemeDetailID as schemeDetailID,
                        ifnull( srp_erp_commisionschemedetails.commisionAmount , 0 ) AS commisionAmount, 
                        ifnull( srp_erp_commisionschemedetails.commisionAmount * '.$requestedQty.', 0 ) AS totalcommisionAmount ');
                $this->db->from('srp_erp_commission_hierachy ch');
                $this->db->join('srp_erp_commisionschemedetails', 'srp_erp_commisionschemedetails.designationID = ch.designationID 
                    AND srp_erp_commisionschemedetails.schemeMasterID = '.$schemeID.'  AND srp_erp_commisionschemedetails.itemAutoID = '.$itemAutoID.'','left');
                $this->db->where('ch.employeeID',$empID);
                $this->db->where('ch.designationID', $designationID );
                $this->db->where('ch.isDeleted', 0 );
                $this->db->where('ch.companyID', $companyID );
                $details = $this->db->get()->row_array();

                $emp_manager_array[$x]['invoiceDetailID']=$invoiceDetailID;
                $emp_manager_array[$x]['commissionAutoID']=$commissionAutoID;
                $emp_manager_array[$x]['salesPersonEmpID']=$details['employeeID'];
                $emp_manager_array[$x]['designationID']=$details['DesignationID'];
                $emp_manager_array[$x]['empID']=$details['reportingEmployeeID'];
                $emp_manager_array[$x]['commissionAmount']=$details['totalcommisionAmount'];
                $emp_manager_array[$x]['schemeID']=$schemeID;
                $emp_manager_array[$x]['schmeDetailID']=$details['schemeDetailID'];
                $emp_manager_array[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                $emp_manager_array[$x]['createdUserGroup'] = $this->common_data['user_group'];
                $emp_manager_array[$x]['createdPCID'] = $this->common_data['current_pc'];
                $emp_manager_array[$x]['createdUserID'] = $this->common_data['current_userID'];
                $emp_manager_array[$x]['createdUserName'] = $this->common_data['current_user'];
                $emp_manager_array[$x]['createdDateTime'] = $this->common_data['current_date'];*/

//            }else{

                while( $empID !=  '' ){

                    $this->db->select('ch.employeeID as employeeID,
                        ch.designationID as DesignationID,
                        ch.reportingEmployeeID as reportingEmployeeID, 
                        ch.reportingDesignationID as reportingDesignationID,
                        srp_erp_commisionschemedetails.schemeDetailID as schemeDetailID,
                        ifnull( srp_erp_commisionschemedetails.commisionAmount , 0 ) AS commisionAmount, 
                        ifnull( srp_erp_commisionschemedetails.commisionAmount * '.$requestedQty.', 0 ) AS totalcommisionAmount ');
                    $this->db->from('srp_erp_commission_hierachy ch');
                    $this->db->join('srp_erp_commisionschemedetails', 'srp_erp_commisionschemedetails.designationID = ch.designationID 
                    AND srp_erp_commisionschemedetails.schemeMasterID = '.$schemeID.'  AND srp_erp_commisionschemedetails.itemAutoID = '.$itemAutoID.'','left');
                    $this->db->where('ch.employeeID',$empID);
                    $this->db->where('ch.designationID', $designationID );
                    $this->db->where('ch.isDeleted', 0 );
                    $this->db->where('ch.companyID', $companyID );
                    $details = $this->db->get()->row_array();

                    if($details){
                        $emp_manager_array[$x]['invoiceDetailID']=$invoiceDetailID;
                        $emp_manager_array[$x]['commissionAutoID']=$commissionAutoID;
                        $emp_manager_array[$x]['salesPersonEmpID']=$details['employeeID'];
                        $emp_manager_array[$x]['designationID']=$details['DesignationID'];
                        $emp_manager_array[$x]['empID']=$details['reportingEmployeeID'];
                        $emp_manager_array[$x]['commissionAmount']=$details['totalcommisionAmount'];
                        $emp_manager_array[$x]['FixedCommissionAmount'] = $details['totalcommisionAmount'];
                        $emp_manager_array[$x]['schemeID']=$schemeID;
                        $emp_manager_array[$x]['schmeDetailID']=$details['schemeDetailID'];
                        $emp_manager_array[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                        $emp_manager_array[$x]['createdUserGroup'] = $this->common_data['user_group'];
                        $emp_manager_array[$x]['createdPCID'] = $this->common_data['current_pc'];
                        $emp_manager_array[$x]['createdUserID'] = $this->common_data['current_userID'];
                        $emp_manager_array[$x]['createdUserName'] = $this->common_data['current_user'];
                        $emp_manager_array[$x]['createdDateTime'] = $this->common_data['current_date'];
                        $empID = $details['reportingEmployeeID'];
                        $designationID = $details['reportingDesignationID'];
                        $x++;

                    }else{
                        break;
                    }
                }
           // }
        }else{ $emp_manager_array=''; }
        return $emp_manager_array;
    }

    function save_invoice_item_detail_commission()
    {
        $projectExist = project_is_exist();
        $invoiceDetailsAutoID = $this->input->post('invoiceDetailsAutoID');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $item_text = $this->input->post('item_text');
        $wareHouse = $this->input->post('wareHouse');
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $projectID = $this->input->post('projectID');
        $quantityRequested = $this->input->post('quantityRequested');
        $item_taxPercentage = $this->input->post('item_taxPercentage');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');
        $SUOMQty = $this->input->post('SUOMQty');
        $SUOMIDhn = $this->input->post('SUOMIDhn');

        $noOfItems = $this->input->post('noOfItems');
        $grossQty = $this->input->post('grossQty');
        $noOfUnits = $this->input->post('noOfUnits');
        $deduction = $this->input->post('deduction');
        $invoiceType = $this->input->post('invoiceType');
        $salesPersonID = $this->input->post('salesPersonID');
        $designationID = $this->input->post('designationID');

        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $tax_master = array();
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm= $this->db->get()->row_array();

//            if (!trim($this->input->post('invoiceDetailsAutoID') ?? '')) {
//                if($serviceitm['mainCategory']=="Inventory") {
//                    $this->db->select('invoiceAutoID,,itemDescription,itemSystemCode');
//                    $this->db->from('srp_erp_customerinvoicedetails');
//                    $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
//                    $this->db->where('itemAutoID', $itemAutoID);
//                    $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
//                    $order_detail = $this->db->get()->row_array();
//                    if (!empty($order_detail)) {
//                        return array('w', 'Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
//                    }
//                }
//            }

            if (isset($item_text[$key])) {
                $this->db->select('*');
                $this->db->where('taxMasterAutoID', $item_text[$key]);
                $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();

                $this->db->select('*');
                $this->db->where('supplierSystemCode', $tax_master['supplierSystemCode']);
                $Supplier_master = $this->db->get('srp_erp_suppliermaster')->row_array();

                $this->db->select('srp_erp_taxmaster.*,srp_erp_chartofaccounts.GLAutoID as liabilityAutoID,srp_erp_chartofaccounts.systemAccountCode as liabilitySystemGLCode,srp_erp_chartofaccounts.GLSecondaryCode as liabilityGLAccount,srp_erp_chartofaccounts.GLDescription as liabilityDescription,srp_erp_chartofaccounts.CategoryTypeDescription as liabilityType,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.DecimalPlaces');
                $this->db->where('taxMasterAutoID', $item_text[$key]);
                $this->db->from('srp_erp_taxmaster');
                $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.supplierGLAutoID');
                $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_taxmaster.supplierCurrencyID');
                $tax_master = $this->db->get()->row_array();
            }

            $wareHouse_location = explode('|', $wareHouse[$key]);
            $item_arr = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);
            $project_categoryID = $this->input->post('project_categoryID');
            $project_subCategoryID = $this->input->post('project_subCategoryID');

            $data['invoiceAutoID'] = trim($invoiceAutoID);
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_arr['itemSystemCode'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];
            }
            $data['itemDescription'] = $item_arr['itemDescription'];

            $data['SUOMQty'] = $SUOMQty[$key];
            $data['SUOMID'] = $SUOMIDhn[$key];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = $quantityRequested[$key];
            $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = $discount_amount[$key];
            $amountafterdiscount = $estimatedAmount[$key] - $data['discountAmount'];
            $data['unittransactionAmount'] = round($estimatedAmount[$key], $master['transactionCurrencyDecimalPlaces']);
            $data['taxPercentage'] = $item_taxPercentage[$key];
            $taxAmount = ($data['taxPercentage'] / 100) * $amountafterdiscount;
            $data['taxAmount'] = round($taxAmount, $master['transactionCurrencyDecimalPlaces']);
            $totalAfterTax = $data['taxAmount'] * $data['requestedQty'];
            $data['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
            $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested[$key];
            /* if($invoiceType == "Commission"){
                $transactionAmount = $data['unittransactionAmount'] * $quantityRequested[$key];
            } */
            $data['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $data['comment'] = $comment[$key];
            $data['remarks'] = $remarks[$key];
            $data['type'] = 'Item';
            if($invoiceType == "Commission"){
                $data['type'] = 'Commission';
            }
            $item_data = fetch_item_data($data['itemAutoID']);
            if($serviceitm['mainCategory']=="Service") {
                $data['wareHouseAutoID'] = 0;
                $data['wareHouseCode'] = null;
                $data['wareHouseLocation'] = null;
                $data['wareHouseDescription'] = null;
            }else{
                $data['wareHouseAutoID'] = $wareHouseAutoID[$key];
                $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
                $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
                $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
            }
            $data['segmentID'] = $master['segmentID'];
            $data['segmentCode'] = $master['segmentCode'];
            $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
            $data['expenseGLCode'] = $item_data['costGLCode'];
            $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['expenseGLDescription'] = $item_data['costDescription'];
            $data['expenseGLType'] = $item_data['costType'];
            $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
            $data['revenueGLCode'] = $item_data['revanueGLCode'];
            $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['revenueGLDescription'] = $item_data['revanueDescription'];
            $data['revenueGLType'] = $item_data['revanueType'];
            $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
            $data['assetGLCode'] = $item_data['assteGLCode'];
            $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['assetGLDescription'] = $item_data['assteDescription'];
            $data['assetGLType'] = $item_data['assteType'];
            $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['itemCategory'] = $item_data['mainCategory'];

            $data['noOfItems'] = $noOfItems[$key];
            $data['grossQty'] = $grossQty[$key];
            $data['noOfUnits'] = $noOfUnits[$key];
            $data['deduction'] = $deduction[$key];
            if($invoiceType == "Commission"){
                $data['salesPersonID'] = $salesPersonID[$key];
                $data['designationID'] = $designationID[$key];
            }


            if (!empty($tax_master)) {
                $data['taxMasterAutoID'] = $tax_master['taxMasterAutoID'];
                $data['taxDescription'] = $tax_master['taxDescription'];
                $data['taxShortCode'] = $tax_master['taxShortCode'];
                $data['taxSupplierAutoID'] = $tax_master['supplierAutoID'];
                $data['taxSupplierSystemCode'] = $tax_master['supplierSystemCode'];
                $data['taxSupplierName'] = $tax_master['supplierName'];
                $data['taxSupplierCurrencyID'] = $tax_master['supplierCurrencyID'];
                $data['taxSupplierCurrency'] = $tax_master['CurrencyCode'];
                $data['taxSupplierCurrencyDecimalPlaces'] = $tax_master['DecimalPlaces'];
                $data['taxSupplierliabilityAutoID'] = $tax_master['liabilityAutoID'];
                $data['taxSupplierliabilitySystemGLCode'] = $tax_master['liabilitySystemGLCode'];
                $data['taxSupplierliabilityGLAccount'] = $tax_master['liabilityGLAccount'];
                $data['taxSupplierliabilityDescription'] = $tax_master['liabilityDescription'];
                $data['taxSupplierliabilityType'] = $tax_master['liabilityType'];
                $supplierCurrency = currency_conversion($master['transactionCurrency'], $data['taxSupplierCurrency']);
                $data['taxSupplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                $data['taxSupplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                $data['taxSupplierCurrencyAmount'] = ($data['transactionAmount'] / $data['taxSupplierCurrencyExchangeRate']);
            } else {
                $data['taxSupplierCurrencyExchangeRate'] = 1;
                $data['taxSupplierCurrencyDecimalPlaces'] = 2;
                $data['taxSupplierCurrencyAmount'] = 0;
            }

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];


            if ($invoiceDetailsAutoID) {
                /*$this->db->where('invoiceDetailsAutoID', trim($invoiceDetailsAutoID));
                $this->db->update('srp_erp_customerinvoicedetails', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('invoiceDetailsAutoID'));
                }*/
            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerinvoicedetails', $data);
                $last_id = $this->db->insert_id();

                if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                    $this->db->select('itemAutoID');
                    $this->db->where('itemAutoID', $itemAutoID);
                    $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                    if (empty($warehouseitems)) {
                        $data_arr = array(
                            'wareHouseAutoID' => $data['wareHouseAutoID'],
                            'wareHouseLocation' => $data['wareHouseLocation'],
                            'wareHouseDescription' => $data['wareHouseDescription'],
                            'itemAutoID' => $data['itemAutoID'],
                            'itemSystemCode' => $data['itemSystemCode'],
                            'barCodeNo' => $item_data['barcode'],
                            'salesPrice' => $item_data['companyLocalSellingPrice'],
                            'ActiveYN' => $item_data['isActive'],
                            'itemDescription' => $data['itemDescription'],
                            'unitOfMeasureID' => $data['defaultUOMID'],
                            'unitOfMeasure' => $data['defaultUOM'],
                            'currentStock' => 0,
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'companyCode' => $this->common_data['company_data']['company_code'],
                        );
                        $this->db->insert('srp_erp_warehouseitems', $data_arr);
                    }
                }
            }
        }
        /** Added By : (SME-2299)*/
        $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoiceAutoID}")->row_array();
        if(!empty($rebate['rebatePercentage'])) {
            $this->calculate_rebate_amount($invoiceAutoID);
        }
        /** End (SME-2299)*/

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Invoice Detail : Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Invoice Detail : Saved Successfully.');
        }
    }

    function update_invoice_item_detail_commission()
    {
        $invoiceDetailsAutoID = $this->input->post('invoiceDetailsAutoID');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $itemAutoID = $this->input->post('itemAutoID');
        $item_text = $this->input->post('item_text');
        $wareHouse = $this->input->post('wareHouse');
        $projectID = $this->input->post('projectID');
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $item_taxPercentage = $this->input->post('item_taxPercentage');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $discount_amount = $this->input->post('discount_amount');
        $discount = $this->input->post('discount');
        $projectExist = project_is_exist();
        $invoiceType = $this->input->post('invoiceType');
        $salesPersonID = $this->input->post('salesPersonID');
        $designationID = $this->input->post('designationID');

        $this->db->select('mainCategory');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $itemAutoID);
        $serviceitm= $this->db->get()->row_array();

        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $tax_master = array();
//        if($serviceitm['mainCategory']=="Inventory") {
//            if (!empty($this->input->post('invoiceDetailsAutoID'))) {
//                $this->db->select('invoiceAutoID,,itemDescription,itemSystemCode');
//                $this->db->from('srp_erp_customerinvoicedetails');
//                $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
//                $this->db->where('itemAutoID', $itemAutoID);
//                $this->db->where('invoiceDetailsAutoID !=', $invoiceDetailsAutoID);
//                $order_detail = $this->db->get()->row_array();
//                if (!empty($order_detail)) {
//                    return array('w', 'Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
//                }
//            }
//        }
        if (isset($item_text)) {
            $this->db->select('*');
            $this->db->where('taxMasterAutoID', $item_text);
            $tax_master = $this->db->get('srp_erp_taxmaster')->row_array();

            $this->db->select('*');
            $this->db->where('supplierSystemCode', $tax_master['supplierSystemCode']);
            $Supplier_master = $this->db->get('srp_erp_suppliermaster')->row_array();
        }

        $wareHouse_location = explode('|', $wareHouse);
        $item_arr = fetch_item_data($itemAutoID);
        $uomEx = explode('|', $uom);

        $data['invoiceAutoID'] = trim($invoiceAutoID);
        $data['itemAutoID'] = $itemAutoID;
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['projectID'] = $projectID;
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');
        }
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['SUOMQty'] = $this->input->post('SUOMQty');
        $data['SUOMID'] = $this->input->post('SUOMIDhn');
        $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
        $data['unitOfMeasureID'] = $UnitOfMeasureID;
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = $quantityRequested;
        $data['discountPercentage'] = $discount;
        $data['discountAmount'] = $discount_amount;
        $amountafterdiscount = $estimatedAmount - $discount_amount;
        $data['unittransactionAmount'] = round($estimatedAmount, $master['transactionCurrencyDecimalPlaces']);
        $data['taxPercentage'] = $item_taxPercentage;
        $taxAmount = ($data['taxPercentage'] / 100) * $amountafterdiscount;
        $data['taxAmount'] = round($taxAmount, $master['transactionCurrencyDecimalPlaces']);
        $totalAfterTax = $data['taxAmount'] * $data['requestedQty'];
        $data['totalAfterTax'] = round($totalAfterTax, $master['transactionCurrencyDecimalPlaces']);
        $transactionAmount = ($data['taxAmount'] + $amountafterdiscount) * $quantityRequested;
        $data['transactionAmount'] = round($transactionAmount, $master['transactionCurrencyDecimalPlaces']);
        $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
        $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
        $data['comment'] = $comment;
        $data['remarks'] = $remarks;
        $data['type'] = 'Item';
        if($invoiceType == "Commission"){
            $data['type'] = 'Commission';
            $data['salesPersonID'] = $salesPersonID;
            $data['designationID'] = $designationID;
        }
        $item_data = fetch_item_data($data['itemAutoID']);
        if($serviceitm['mainCategory']=="Service") {
            $data['wareHouseAutoID'] = 0;
            $data['wareHouseCode'] = null;
            $data['wareHouseLocation'] = null;
            $data['wareHouseDescription'] = null;
        }else{
            $data['wareHouseAutoID'] = $wareHouseAutoID;
            $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
            $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
            $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
        }
        $data['segmentID'] = $master['segmentID'];
        $data['segmentCode'] = $master['segmentCode'];
        $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
        $data['expenseGLCode'] = $item_data['costGLCode'];
        $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
        $data['expenseGLDescription'] = $item_data['costDescription'];
        $data['expenseGLType'] = $item_data['costType'];
        $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
        $data['revenueGLCode'] = $item_data['revanueGLCode'];
        $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
        $data['revenueGLDescription'] = $item_data['revanueDescription'];
        $data['revenueGLType'] = $item_data['revanueType'];
        $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
        $data['assetGLCode'] = $item_data['assteGLCode'];
        $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
        $data['assetGLDescription'] = $item_data['assteDescription'];
        $data['assetGLType'] = $item_data['assteType'];
        $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
        $data['itemCategory'] = $item_data['mainCategory'];

        if (!empty($tax_master)) {
            $data['taxMasterAutoID'] = $tax_master['taxMasterAutoID'];
            $data['taxDescription'] = $tax_master['taxDescription'];
            $data['taxShortCode'] = $tax_master['taxShortCode'];
            $data['taxSupplierAutoID'] = $tax_master['supplierAutoID'];
            $data['taxSupplierSystemCode'] = $tax_master['supplierSystemCode'];
            $data['taxSupplierName'] = $tax_master['supplierName'];
            $data['taxSupplierCurrencyID'] = $tax_master['supplierCurrencyID'];
            $data['taxSupplierCurrency'] = $tax_master['supplierCurrency'];
            $data['taxSupplierCurrencyDecimalPlaces'] = $tax_master['supplierCurrencyDecimalPlaces'];
            $data['taxSupplierliabilityAutoID'] = $tax_master['supplierGLAutoID'];
            $data['taxSupplierliabilitySystemGLCode'] = $tax_master['supplierGLSystemGLCode'];
            $data['taxSupplierliabilityGLAccount'] = $tax_master['supplierGLAccount'];
            $data['taxSupplierliabilityDescription'] = $tax_master['supplierGLDescription'];
            $data['taxSupplierliabilityType'] = $tax_master['supplierGLType'];
            $supplierCurrency = currency_conversion($master['transactionCurrency'], $data['taxSupplierCurrency']);
            $data['taxSupplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
            $data['taxSupplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
            $data['taxSupplierCurrencyAmount'] = ($data['transactionAmount'] / $data['taxSupplierCurrencyExchangeRate']);
        } else {
            $data['taxSupplierCurrencyExchangeRate'] = 1;
            $data['taxSupplierCurrencyDecimalPlaces'] = 2;
            $data['taxSupplierCurrencyAmount'] = 0;
        }

        $data['noOfItems'] = $this->input->post('noOfItems');
        $data['grossQty'] = $this->input->post('grossQty');
        $data['noOfUnits'] = $this->input->post('noOfUnits');
        $data['deduction'] = $this->input->post('deduction');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $data['wareHouseAutoID'],
                    'wareHouseLocation' => $data['wareHouseLocation'],
                    'wareHouseDescription' => $data['wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'barCodeNo' => $item_data['barcode'],
                    'salesPrice' => $item_data['companyLocalSellingPrice'],
                    'ActiveYN' => $item_data['isActive'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }
        }

        if ($invoiceDetailsAutoID) {
            $contractID = $this->db->query("SELECT contractDetailsAutoID FROM srp_erp_customerinvoicedetails WHERE invoiceDetailsAutoID = {$invoiceDetailsAutoID}")->row_array();
            $compID = $this->common_data['company_data']['company_id'];
            if(isset($contractID['contractDetailsAutoID'])){
                $contractedTotal = $this->db->query("SELECT (IFNULL(deliveredQty, 0) + IFNULL(invoiced.requestedQty, 0)) AS totalDeliveredQty, srp_erp_contractdetails.requestedQty 
                    FROM srp_erp_contractdetails
                        LEFT JOIN ( SELECT SUM( deliveredQty ) AS deliveredQty, contractDetailsAutoID FROM srp_erp_deliveryorderdetails GROUP BY contractDetailsAutoID ) delivered ON delivered.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID
                        LEFT JOIN ( SELECT SUM( requestedQty ) AS requestedQty, contractDetailsAutoID FROM srp_erp_customerinvoicedetails WHERE invoiceDetailsAutoID != {$invoiceDetailsAutoID} GROUP BY contractDetailsAutoID ) invoiced ON invoiced.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID 
                    WHERE companyID = {$compID} AND srp_erp_contractdetails.contractDetailsAutoID = {$contractID['contractDetailsAutoID']}")->row_array();

                $deliveredTot = $contractedTotal['totalDeliveredQty'] + $quantityRequested;
                if ($deliveredTot >= $contractedTotal['requestedQty'])
                {
                    $cont_data['invoicedYN'] = 1;
                    $this->db->where('contractDetailsAutoID', $contractID['contractDetailsAutoID']);
                    $this->db->update('srp_erp_contractdetails', $cont_data);
                } else {
                    $cont_data['invoicedYN'] = 0;
                    $this->db->where('contractDetailsAutoID', $contractID['contractDetailsAutoID']);
                    $this->db->update('srp_erp_contractdetails', $cont_data);
                }
            }

            $this->db->where('invoiceDetailsAutoID', trim($invoiceDetailsAutoID));
            $this->db->update('srp_erp_customerinvoicedetails', $data);

            /** Added By : (SME-2299)*/
            $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoiceAutoID}")->row_array();
            if(!empty($rebate['rebatePercentage'])) {
                $this->calculate_rebate_amount($invoiceAutoID);
            }
            /** End (SME-2299)*/

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
            }
        }
    }

    function fetch_invoice_direct_details_commission()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->db->select('transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,invoiceType,ifnull(retentionPercentage,0) as retentionPercentage');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $master_record = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
        $data['currency'] = $master_record;

     
        $this->db->select('srp_erp_customerinvoicedetails.*,srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.partNo,srp_erp_itemmaster.seconeryItemCode AS itemSecondaryCode,srp_erp_suppliermaster.supplierName as supplierName, 
        DOMasterID,DATE_FORMAT(DODate,\''.  $convertFormat .'\') AS DODate,DOCode,referenceNo,del_ord.deliveredTransactionAmount AS do_tr_amount,due_amount,balance_amount,srp_erp_unit_of_measure.UnitShortCode as secuom,IFNULL(srp_employeesdetails.Ename1 , " - ") AS SalesPersonName,
        IFNULL(srp_employeesdetails.EmpSecondaryCode, " - ") AS SalesPersonCode,srp_designation.DesDescription as DesDescription');
        $this->db->from('srp_erp_customerinvoicedetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID', 'left');
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_customerinvoicedetails.supplierAutoID', 'left');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_customerinvoicedetails.salesPersonID', 'left');
        $this->db->join('srp_erp_deliveryorder del_ord', 'del_ord.DOAutoID = srp_erp_customerinvoicedetails.DOMasterID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerinvoicedetails.SUOMID','left');
        $this->db->join('srp_designation', '`srp_designation`.`DesignationID` = `srp_erp_customerinvoicedetails`.`designationID` 
            AND `srp_designation`.`isDeleted` = 0  AND `srp_designation`.`Erp_companyID` = \''.   $companyID  .'\'','left');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));

      
        $data['detail'] = $this->db->get()->result_array();


        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicediscountdetails');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $data['discount_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
        $data['extraChargeDetail'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->result_array();

        $this->db->select('*');
        $this->db->from('srp_erp_customerinvoicetaxdetails');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $data['tax_detail'] = $this->db->get()->result_array();
        $taxamount = array_sum(array_column($data['detail'],'taxAmount'));
        $data['Istaxexist'] = ($taxamount>0?1:0);
        return $data;
    }

    function fetch_customer_invoice_detail_commission()
    {
        $invoiceDetailsAutoID = trim($this->input->post('invoiceDetailsAutoID') ?? '');
        $this->db->select('srp_erp_customerinvoicedetails.*,srp_erp_customerinvoicemaster.invoiceType,srp_erp_itemmaster.currentStock,srp_erp_itemmaster.mainCategory,srp_erp_unit_of_measure.UnitShortCode as secuom,srp_erp_unit_of_measure.UnitDes as secuomdec, (IFNULL(contractBalance.balance,0) + srp_erp_customerinvoicedetails.requestedQty) AS balanceQty,srp_erp_itemmaster.seconeryItemCode as seconeryItemCode');
        $this->db->where('invoiceDetailsAutoID', trim($this->input->post('invoiceDetailsAutoID') ?? ''));
        $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicedetails.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID', 'left');
        $this->db->join('srp_employeesdetails', 'srp_erp_customerinvoicedetails.salesPersonID = srp_employeesdetails.EIdNo', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_customerinvoicedetails.itemAutoID = srp_erp_itemmaster.itemAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerinvoicedetails.SUOMID','left');
        $this->db->join('(SELECT srp_erp_contractdetails.contractDetailsAutoID, TRIM(TRAILING '.' FROM TRIM(TRAILING 0 FROM(ROUND( ifnull( srp_erp_contractdetails.requestedQty, 0 ), 2 ))) - TRIM(TRAILING 0 FROM(ROUND( ifnull( cinv.requestedQtyINV, 0 ) + ifnull( deliveryorder.requestedQtyDO, 0 ), 2 )))) AS balance 
                                        FROM srp_erp_contractdetails
                                        LEFT JOIN (SELECT contractAutoID, contractDetailsAutoID, itemAutoID, IFNULL( SUM( requestedQty ), 0 ) AS requestedQtyINV FROM srp_erp_customerinvoicedetails WHERE invoiceDetailsAutoID  GROUP BY contractDetailsAutoID) cinv ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `cinv`.`contractDetailsAutoID`
                                        LEFT JOIN (SELECT contractAutoID, contractDetailsAutoID, itemAutoID, IFNULL( SUM( deliveredQty ), 0 ) AS requestedQtyDO FROM srp_erp_deliveryorderdetails GROUP BY contractDetailsAutoID ) deliveryorder ON `srp_erp_contractdetails`.`contractDetailsAutoID` = `deliveryorder`.`contractDetailsAutoID` 
                                ) contractBalance', 'contractBalance.contractDetailsAutoID = srp_erp_customerinvoicedetails.contractDetailsAutoID','left');
        $this->db->from('srp_erp_customerinvoicedetails');
        return $this->db->get()->row_array();
    }

    function fetch_invoice_template_data_commission($invoiceAutoID)
    {

        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $this->db->select('srp_erp_customerinvoicemaster.*,srp_erp_segment.description as segDescription, DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDate,\'' . $convertFormat . '\') AS invoiceDate , DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate, DATE_FORMAT(srp_erp_customerinvoicemaster.customerInvoiceDate,\'' . $convertFormat . '\') AS customerInvoiceDate, DATE_FORMAT(srp_erp_customerinvoicemaster.approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate, CASE WHEN srp_erp_customerinvoicemaster.confirmedYN = 2 || srp_erp_customerinvoicemaster.confirmedYN = 3   THEN " - " WHEN srp_erp_customerinvoicemaster.confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(srp_erp_customerinvoicemaster.confirmedbyName),srp_erp_customerinvoicemaster.confirmedbyName,\'-\'), IF(LENGTH(DATE_FORMAT( srp_erp_customerinvoicemaster.confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )), DATE_FORMAT( srp_erp_customerinvoicemaster.confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn, srp_erp_salespersonmaster.SalesPersonName as SalesPersonName,srp_designation.DesDescription as DesDescription,logisticBLNo,logisticContainerNo, company_name AS accountName, textIdentificationNo, IFNULL(taxCardNo, " - ") as taxCardNo');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->join('srp_erp_salespersonmaster', 'srp_erp_salespersonmaster.salesPersonID = srp_erp_customerinvoicemaster.salesPersonID','LEFT');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_salespersonmaster.EIdNo','LEFT');
        $this->db->join('srp_designation', 'srp_designation.DesignationID = srp_employeesdetails.EmpDesignationId AND srp_designation.isDeleted=0 AND srp_designation.Erp_companyID=  \'' . $companyID . '\' ','LEFT');
        $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_customerinvoicemaster.segmentID', 'Left');
        $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_erp_customerinvoicemaster.companyID', 'Left');
        $this->db->from('srp_erp_customerinvoicemaster');
        $data['master'] = $this->db->get()->row_array();

        $data['master']['retentionInvoiceCode']='';
        if($data['master']){
            if($data['master']['retensionInvoiceID'] <> ''){

                /*Retention*/
                $this->db->select('invoiceCode');
                $this->db->where('invoiceAutoID', $data['master']['retensionInvoiceID']);
                $this->db->from('srp_erp_customerinvoicemaster');
                $retention = $this->db->get()->row_array();

                $data['master']['retentionInvoiceCode']=$retention['invoiceCode'];

                /**/

            }
        }

        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);



        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax,customerCountry');
        $this->db->where('customerAutoID', $data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();

        $this->db->select('wareHouseLocation');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('wareHouseAutoID !=','');
        $this->db->from('srp_erp_customerinvoicedetails');
        $data['warehousearea'] = $this->db->get()->row_array();

        $str = '';
        if($data['master']['invoiceType'] == 'Manufacturing'){
            $str = ', CONCAT(srp_erp_mfq_itemmaster.itemSystemCode," - ",srp_erp_mfq_itemmaster.itemDescription) as mfq_item_Description';
        }
        $this->db->select('warehouse.wareHouseDescription as warehouse,srp_erp_customerinvoicedetails.*,srp_erp_itemmaster.partNo,srp_erp_itemmaster.seconeryItemCode AS itemSecondaryCode,
                srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,srp_erp_unit_of_measure.UnitShortCode as secuom,contractmaster.documentID'.$str);
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'Item');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerinvoicedetails.SUOMID','left');
        $this->db->join('srp_erp_warehousemaster warehouse ', ' warehouse.wareHouseAutoID = srp_erp_customerinvoicedetails.wareHouseAutoID','left');
        $this->db->join('srp_erp_contractmaster contractmaster ', 'contractmaster.contractAutoID = srp_erp_customerinvoicedetails.contractAutoID','left');
        $this->db->from('srp_erp_customerinvoicedetails');

        if($data['master']['invoiceType'] == 'Manufacturing'){
            $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.mfqItemID = srp_erp_customerinvoicedetails.mfqItemAutoID');
        }

        $data['item_detail'] = $this->db->get()->result_array();

        $data['item_detail_tax'] = array_sum(array_column($data['item_detail'],'totalAfterTax'));

        $data['item_detail_count'] = $this->db->query("SELECT
            COUNT(  invoiceDetailsAutoID) as doccount
            FROM
                `srp_erp_customerinvoicedetails`
                JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_customerinvoicedetails`.`itemAutoID`
                LEFT JOIN `srp_erp_unit_of_measure` ON `srp_erp_unit_of_measure`.`UnitID` = `srp_erp_customerinvoicedetails`.`SUOMID`
                LEFT JOIN `srp_erp_warehousemaster` `warehouse` ON `warehouse`.`wareHouseAutoID` = `srp_erp_customerinvoicedetails`.`wareHouseAutoID`
                LEFT JOIN `srp_erp_contractmaster` `contractmaster` ON `contractmaster`.`contractAutoID` = `srp_erp_customerinvoicedetails`.`contractAutoID` 
            WHERE
                `invoiceAutoID` = $invoiceAutoID 
                AND `type` = 'Item'")->row('doccount');

        $convertFormat = convert_date_format_sql();
        $this->db->select('cus.*, DOMasterID,DATE_FORMAT(DODate,\''.  $convertFormat .'\') AS DODate,DOCode,referenceNo,del_ord.transactionAmount AS do_tr_amount,due_amount,balance_amount');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'DO');
        $this->db->from('srp_erp_customerinvoicedetails cus');
        $this->db->join('srp_erp_deliveryorder del_ord', 'del_ord.DOAutoID = cus.DOMasterID');
        $data['delivery_order'] = $this->db->get()->result_array();

        $data['delivery_order_DS'] = $this->db->query("SELECT
            `DOMasterID`,
            DATE_FORMAT( DODate, '%d-%m-%Y' ) AS DODate,
            `DOCode`,
            CONCAT( item.seconeryItemCode, ' | ', item.itemDescription ) AS itemDesc,
            FORMAT( srp_erp_deliveryorderdetails.requestedQty, 3 ) AS requestedQtyformatted,
            srp_erp_deliveryorderdetails.unittransactionAmount,
            TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((IFNULL(  srp_erp_deliveryorderdetails.unittransactionAmount , 0 )), 5)))))) AS unittransactionAmount,
            srp_erp_deliveryorderdetails.transactionAmount,
            srp_erp_deliveryorderdetails.unitOfMeasure ,
            srp_erp_deliveryorderdetails.unitOfMeasureID
        FROM
            srp_erp_deliveryorderdetails
            JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID
            JOIN `srp_erp_customerinvoicedetails` `cus` ON `srp_erp_deliveryorderdetails`.`DOAutoID` = `cus`.`DOMasterID` 
            LEFT JOIN `srp_erp_itemmaster` `item` ON `srp_erp_deliveryorderdetails`.`itemAutoID` = `item`.`ItemAutoID` 
        WHERE
            `invoiceAutoID` = {$invoiceAutoID} 
            AND cus.`type` = 'DO'")->result_array();

        $data['delivery_order_ds_count'] = $this->db->query("SELECT COUNT(`DOCode`)  as doccount FROM srp_erp_deliveryorderdetails JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID
                                                                 JOIN `srp_erp_customerinvoicedetails` `cus` ON `srp_erp_deliveryorderdetails`.`DOAutoID` = `cus`.`DOMasterID`
                                                                 LEFT JOIN `srp_erp_itemmaster` `item` ON `srp_erp_deliveryorderdetails`.`itemAutoID` = `item`.`ItemAutoID` 
                                                                 WHERE
                                                                `invoiceAutoID` = {$invoiceAutoID}  
                                                                AND cus.`type` = 'DO'")->row('doccount');

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['extracharge'] = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['discount'] = $this->db->get('srp_erp_customerinvoicediscountdetails')->result_array();

        $this->db->select('*,   CONCAT(revenueGLCode,\' | \',revenueGLDescription,\' | \',revenueGLType) as manufacturinggldes');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_customerinvoicedetails');
        $data['gl_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('type', 'OP');
        $this->db->from('srp_erp_customerinvoicedetails');
        $data['op_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $data['tax'] = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();

        $this->db->select('srp_erp_customerinvoicedetails.contractAutoID,
        srp_erp_contractmaster.referenceNo AS referenceNo');
        $this->db->from('srp_erp_customerinvoicedetails');
        $this->db->join('srp_erp_contractmaster', 'srp_erp_contractmaster.contractAutoID = srp_erp_customerinvoicedetails.contractAutoID', 'LEFT');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->group_by("srp_erp_contractmaster.contractAutoID");
        $data['referenceNo'] = $this->db->get()->result_array();

        $data['invoiceproject'] = $this->db->query("SELECT detailid.itemDescription,IF(isVariation = 1,variationAmount,totalTransCurrency) as totalTransCurrency,invoicemaster.transactionCurrencyDecimalPlaces,
            srp_erp_customerinvoicedetails.invoiceDetailsAutoID,detailID,
            unitRateTransactionCurrency,
            IFNULL(srp_erp_customerinvoicedetails.transactionAmount,0) as transactionAmount,
            IFNULL(srp_erp_customerinvoicedetails.boqClaimPercentage,0) as boqClaimPercentage,
            isVariation,
            header.retensionPercentage,
            header.headerID,
            header.projectID,
            srp_erp_customerinvoicedetails.invoiceAutoID,
            srp_erp_customerinvoicedetails.invoiceDetailsAutoID,
            srp_erp_customerinvoicedetails.boqDetailID,
              IFNULL( boqPreviousClaimPercentage,0) as boqPreviousClaimPercentage,
           IFNULL( boqTotalClaimPercentage,0) as boqTotalClaimPercentage
             FROM `srp_erp_customerinvoicedetails` LEFT JOIN srp_erp_boq_details detailid on detailid.detailID =srp_erp_customerinvoicedetails.boqDetailID
            LEFT JOIN srp_erp_customerinvoicemaster invoicemaster on invoicemaster.InvoiceAutoID =srp_erp_customerinvoicedetails.InvoiceAutoID
            LEFT JOIN srp_erp_boq_header header on header.headerID = detailid.headerID
             where  Type = 'Project' AND srp_erp_customerinvoicedetails.invoiceAutoID = {$invoiceAutoID}
            ORDER BY
            isVariation asc")->result_array();

        $data['po_numberEST'] = array();
        if(!empty($data['master']['mfqInvoiceAutoID'])){
            $mfqInvoiceAutoID = $data['master']['mfqInvoiceAutoID'];
            $data['po_numberEST'] = $this->db->query("SELECT poNumber, documentCode 
                        FROM srp_erp_mfq_estimatemaster
                            JOIN srp_erp_mfq_job ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID
                            JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID 
                            JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.deliveryNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID
                        WHERE invoiceAutoID =  {$mfqInvoiceAutoID} AND srp_erp_mfq_estimatemaster.companyID = {$companyID}")->result_array();
        }

        if($data['master']['invoiceType'] == 'Commission'){
            $this->db->select('warehouse.wareHouseDescription as warehouse,srp_erp_customerinvoicedetails.*,srp_erp_itemmaster.partNo,srp_erp_itemmaster.seconeryItemCode AS itemSecondaryCode,
            srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,srp_erp_unit_of_measure.UnitShortCode as secuom,contractmaster.documentID,srp_employeesdetails.EmpSecondaryCode AS SalesPersonCode,srp_employeesdetails.Ename1 AS SalesPersonName,desig.DesDescription as DesDescription');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->where('type', 'Commission');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerinvoicedetails.SUOMID','left');
            $this->db->join('srp_erp_warehousemaster warehouse ', ' warehouse.wareHouseAutoID = srp_erp_customerinvoicedetails.wareHouseAutoID','left');
            $this->db->join('srp_erp_contractmaster contractmaster ', 'contractmaster.contractAutoID = srp_erp_customerinvoicedetails.contractAutoID','left');
            $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_customerinvoicedetails.salesPersonID', 'left');
            $this->datatables->join('srp_designation desig', 'desig.DesignationID = srp_erp_customerinvoicedetails.designationID AND desig.isDeleted=0 AND desig.Erp_companyID =\'' . $companyID . '\'', 'left');
            $this->db->from('srp_erp_customerinvoicedetails');
            $data['commission_detail'] = $this->db->get()->result_array();
        }

        return $data;
    }

    function fetch_ic_data($commissionAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('srp_erp_invoice_commision.*, srp_erp_customerinvoicemaster.invoiceCode,CASE WHEN srp_erp_invoice_commision.confirmedYN = 2 || srp_erp_invoice_commision.confirmedYN = 3   THEN " - " WHEN srp_erp_invoice_commision.confirmedYN = 1 THEN 
        CONCAT_WS(\' on \',IF(LENGTH(srp_erp_invoice_commision.confirmedByName),srp_erp_invoice_commision.confirmedByName,\'-\'),IF(LENGTH(DATE_FORMAT( srp_erp_invoice_commision.confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),
        DATE_FORMAT( srp_erp_invoice_commision.confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn,srp_erp_invoice_commision.approvedbyEmpName as approvedbyEmpName,
        DATE_FORMAT( srp_erp_invoice_commision.approvedDate, \'' . $convertFormat . ' %h:%i:%s\' ) as approvedDate,
        DATE_FORMAT( srp_erp_customerinvoicemaster.invoiceDate, \'' . $convertFormat . ' \' ) as invoiceDate') ;
        $this->db->from('srp_erp_invoice_commision');
        $this->db->join('srp_employeesdetails emp','emp.EIdNo=srp_erp_invoice_commision.confirmedByEmpID','left');
        $this->db->join('srp_erp_customerinvoicemaster','srp_erp_invoice_commision.invoiceID=srp_erp_customerinvoicemaster.invoiceAutoID','left');
        $this->db->where('srp_erp_invoice_commision.commissionAutoID', $commissionAutoID);
        $this->db->where('srp_erp_invoice_commision.companyID', $companyID);
        $data['master'] = $this->db->get()->row_array();

        $this->db->select(' icd.commissionDetailID as commissionDetailID,
            `det`.`itemAutoID` AS `itemAutoID`,
            `det`.`seconeryItemCode`,
            `det`.`itemDescription`,
            `det`.`partNo`,
            `det`.`comments`,
            `det`.`salesPersonName` as salesPersonName,
            `det`.`salesPersonSecondaryCode` as salesPersonSecondaryCode,
            `emp`.`EmpSecondaryCode` AS `empoyeeSecondarycode`,
            `emp`.`Ename2` AS `employeeName`,
          icd.designationID,
          desig.DesDescription as DesDescription,
            `det`.`requestedQty` AS requestedQty,
            `cs`.`commisionAmount` AS `UnitcommisionAmount` ,
            icd.commissionAmount AS commissionAmount');
        $this->db->from('srp_erp_invoice_commission_detail icd ');
        $this->db->join('srp_erp_commisionschemedetails cs','icd.schmeDetailID=cs.schemeDetailID','left');
        //$this->db->join('srp_employeesdetails emp','emp.EIdNo=srp_erp_invoice_commission_detail.salesPersonEmpID','left');
        $this->db->join('srp_employeesdetails emp','emp.EIdNo=icd.salesPersonEmpID','left');
        $this->db->join('srp_designation desig','desig.DesignationID=icd.designationID','left');
        $this->db->join('(SELECT invoiceDetailsAutoID,
            srp_erp_customerinvoicedetails.itemAutoID,
            srp_erp_itemmaster.seconeryItemCode as seconeryItemCode ,
            srp_erp_itemmaster.itemDescription as itemDescription,
            srp_erp_itemmaster.partNo as partNo, 
            srp_erp_itemmaster.comments as comments,
            srp_employeesdetails.Ename2 as salesPersonName,
            srp_employeesdetails.EmpSecondaryCode as salesPersonSecondaryCode,
            srp_erp_customerinvoicedetails.requestedQty as requestedQty
            from  srp_erp_customerinvoicedetails 
            LEFT JOIN srp_erp_itemmaster on srp_erp_itemmaster.itemAutoID=srp_erp_customerinvoicedetails.itemAutoID AND srp_erp_itemmaster.companyID = '.$companyID.' 
            LEFT JOIN `srp_employeesdetails`  ON `srp_employeesdetails`.`EIdNo` = `srp_erp_customerinvoicedetails`.`salesPersonID` )det' 
            ,' icd.invoiceDetailID=det.invoiceDetailsAutoID','left');
        $this->db->where('icd.commissionAutoID', $commissionAutoID);
        $this->db->where('icd.companyID', $companyID);
        $data['details'] = $this->db->get()->result_array();

        return $data;
    }



    function invoice_commission_confirmation(){
        $commissionAutoID = trim($this->input->post('commissionAutoID') ?? '');

        $this->db->select('srp_erp_invoice_commision.*');
        $this->db->from('srp_erp_invoice_commision');
        $this->db->where('commissionAutoID', $commissionAutoID);
        $result = $this->db->get()->row_array();

        if (empty($result)) {
            die( json_encode(array('w', 'There are no records to confirm this document!')));
        }
        if ($result['approvedYN']==1) {
            die( json_encode(array('w', 'This document already <b>approved</b>.')));
        }
        if ($result['confirmedYN']==1) {
            die( json_encode(array('w', 'This document already <b>Confirmed</b>.')));
        }

        $this->load->library('Approvals');
        $this->db->trans_start();
        $autoApproval= get_document_auto_approval('IC');

        if($autoApproval==0){
            $approvals_status = $this->approvals->auto_approve($result['commissionAutoID'], 'srp_erp_invoice_commision','commissionAutoID', 'IC',$result['documentSystemCode'], $result['createdDateTime']);
        }elseif($autoApproval==1){
            $approvals_status = $this->approvals->CreateApproval('IC', $result['commissionAutoID'], $result['documentSystemCode'], 'Invoice Commission', 'srp_erp_invoice_commision', 'commissionAutoID', 0, $result['createdDateTime']);
        }else{
            die( json_encode(array('e', 'Approval levels are not set for this document')));
        }

        if($approvals_status == 3){
            die( json_encode(['w', 'There are no users exist to perform approval for this document.']) );
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            die( json_encode(['e', 'Invoice Commission : ' . $result['documentSystemCode'] . ' confirmation failed ' . $this->db->_error_message()]) );
        } else {
            $this->db->trans_commit();
            die( json_encode(['s', 'Invoice Commission : ' . $result['documentSystemCode'] . ' confirmed successfully']) );
        }
    }

    function update_acknowledgementDate_CINV()
    {
        $pK = explode('|',trim($this->input->post('pk') ?? ''));
        $invoiceAutoID =  $pK[0];
        $acknowledgementDate = $this->input->post('value');
        $companyID = current_companyID();
       
        $date_format_policy = date_format_policy();
        $format_acknowledgementDate = null;
        if (isset($acknowledgementDate) && !empty($acknowledgementDate)) {
            $format_acknowledgementDate = input_format_date($acknowledgementDate, $date_format_policy);
        }
        $data['acknowledgementDate'] = $format_acknowledgementDate;
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->update('srp_erp_customerinvoicemaster', $data);

        $this->db->select('approvedYN');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('companyID', $companyID);
        $approvedYN = $this->db->get()->row('approvedYN');   
        if($approvedYN == 1) {
            $data['acknowledgementDate'] = $format_acknowledgementDate;
            $this->db->where('documentCode', 'CINV');
            $this->db->where('documentMasterAutoID', $invoiceAutoID);
            $this->db->update('srp_erp_generalledger', $data);
        }    

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function update_preliminaryPrint_status_update()
    {
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $checked = $this->input->post('checked');
        if($invoiceAutoID) {
            $data['isPreliminaryPrinted'] = $checked;
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->update('srp_erp_customerinvoicemaster', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function fetch_invoice_excel() 
    {
        $companyID = current_companyID();
        
        // date filter
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('IncidateDateFrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('IncidateDateTo');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
        }

        // customer filter
        $customer = $this->input->post('customerCode');
        $customer_filter = '';
        if (!empty($customer)) {
            $whereIN = "( " . join(" , ", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }

        // status Filter
        $status = $this->input->post('status');
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else if ($status == 5) {
                $status_filter = " AND ((isPreliminaryPrinted = 1 AND approvedYN != 1))";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }

        // search filter
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( invoiceCode Like '%$search%' ESCAPE '!') OR ( invoiceType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%')  OR (invoiceNarration Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (invoiceDate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%') OR (referenceNo Like '%$sSearch%')) ";
        }

        $where = "srp_erp_customerinvoicemaster.isDeleted != 1 AND srp_erp_customerinvoicemaster.companyID = " . $companyID . $customer_filter . $date . $status_filter . $searches."";

        $this->db->select("srp_erp_customerinvoicemaster.invoiceAutoID AS invoiceAutoID,
                            invoiceCode,
                            invoiceNarration,
                            srp_erp_customermaster.customerName AS customermastername,
                            transactionCurrencyDecimalPlaces,
                            transactionCurrency AS transactionCurrency,
                            IF(confirmedYN = 1, 'Confirmed', 'Not Confirmed') as confirmed,
                            IF(approvedYN = 1, 'Approved', 'Not Approved') as approved,
                            IF(isPreliminaryPrinted = 1, 'Submitted', 'Not Submitted') as printed,
                            DATE_FORMAT( invoiceDate, '%d-%m-%Y' ) AS invoiceDate,
                            DATE_FORMAT( acknowledgementDate, '%d-%m-%Y' ) AS acknowledgementDate,
                            DATE_FORMAT( invoiceDueDate, '%d-%m-%Y' ) AS invoiceDueDate,
                            CASE
                                    
                                    WHEN invoiceType = 'DeliveryOrder' THEN
                                    'Delivery Order' 
                                    WHEN invoiceType = 'DirectItem' THEN
                                    'Direct Item' 
                                    WHEN invoiceType = 'DirectIncome' THEN
                                    'Direct Income' 
                                    WHEN invoiceType = 'Quotation' THEN
                                    'Quotation Based' 
                                    WHEN invoiceType = 'Contract' THEN
                                    'Contract Based' 
                                    WHEN invoiceType = 'Sales Order' THEN
                                    'Sales Order Based' 
                                    WHEN invoiceType = 'Direct' THEN
                                    'Direct Item' ELSE invoiceType 
                                END AS invoiceType,
                                ( IFNULL( addondet.taxPercentage, 0 )/ 100 )*(
                                    IFNULL( det.transactionAmount, 0 )- IFNULL( det.detailtaxamount, 0 )-((
                                            IFNULL( gendiscount.discountPercentage, 0 )/ 100 
                                            )* IFNULL( det.transactionAmount, 0 ))+ IFNULL( genexchargistax.transactionAmount, 0 ))+ IFNULL( det.transactionAmount, 0 )-((
                                        IFNULL( gendiscount.discountPercentage, 0 )/ 100 
                                    )* IFNULL( det.transactionAmount, 0 ))+ IFNULL( genexcharg.transactionAmount, 0 ) - IFNULL( retensionTransactionAmount, 0 ) - IFNULL( rebateAmount, 0 ) AS total_value,
                                ROUND((
                                        IFNULL( addondet.taxPercentage, 0 )/ 100 
                                        )*(
                                        IFNULL( det.transactionAmount, 0 )- IFNULL( det.detailtaxamount, 0 )-((
                                                IFNULL( gendiscount.discountPercentage, 0 )/ 100 
                                                )* IFNULL( det.transactionAmount, 0 ))+ IFNULL( genexchargistax.transactionAmount, 0 ))+ IFNULL( det.transactionAmount, 0 )-((
                                            IFNULL( gendiscount.discountPercentage, 0 )/ 100 
                                        )* IFNULL( det.transactionAmount, 0 ))+ IFNULL( genexcharg.transactionAmount, 0 ) - IFNULL( retensionTransactionAmount, 0 ) - IFNULL( rebateAmount, 0 ),
                                    2 
                                ) AS total_value_search,
                                isDeleted,
                                referenceNo");
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->join('(SELECT SUM(transactionAmount) AS transactionAmount, sum(totalafterTax) AS detailtaxamount, invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) det', 'det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID', 'LEFT');
        $this->db->join('(SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID) gendiscount', 'gendiscount.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID', 'LEFT');
        $this->db->join('(SELECT SUM(taxPercentage) AS taxPercentage, InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID) addondet', 'addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID', 'LEFT');
        $this->db->join('(SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID) genexchargistax', 'genexchargistax.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID', 'LEFT');
        $this->db->join('(SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID) genexcharg', 'genexcharg.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID', 'LEFT');
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'LEFT');
        $this->db->where($where);
        $result = $this->db->get()->result_array();
        $a = 1;
        $data = array();
        foreach ($result as $row)
        {
            $data[] = array(
                'Num' => $a,
                'invoiceCode' => $row['invoiceCode'],
                'documentDate' => $row['invoiceDate'],
                'dueDate' => $row['invoiceDueDate'],
                'acknowledgementDate' => $row['acknowledgementDate'],
                'customerName' => $row['customermastername'],
                'type' => $row['invoiceType'],
                'referenceNumber' => $row['referenceNo'],
                'comment' => $row['invoiceNarration'],
                'currency' => $row['transactionCurrency'],
                'amount' => $row['total_value'],
                'confirmed' => $row['confirmed'],
                'approved' => $row['approved'],
                'preliminaryPrinted' => $row['printed'],
                'decimalPlace' => $row['transactionCurrencyDecimalPlaces']
            );
            $a++;
        }
        return $data;
    }

    function save_invoice_header_commission()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $invDueDate = $this->input->post('invoiceDueDate');
        $invoiceDueDate = input_format_date($invDueDate, $date_format_policy);
        $invDate = $this->input->post('invoiceDate');
        $invoiceDate = input_format_date($invDate, $date_format_policy);
        $customerDate = $this->input->post('customerInvoiceDate');
        $customerInvoiceDate = input_format_date($customerDate, $date_format_policy);
       /*  $ackDate = $this->input->post('acknowledgeDate');
        $acknowledgeDate = input_format_date($ackDate, $date_format_policy); */
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $customerID = $this->input->post('customerID');
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $invoicetype = $this->input->post('invoiceType');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $projectID =  $this->input->post('projectID');
        if($invoiceAutoID) {
            $projectID_detail = $this->db->query("SELECT projectID FROM srp_erp_customerinvoicemaster where 
                                      invoiceAutoID = $invoiceAutoID")->row_array();
            $detailexist = $this->db->query("SELECT invoiceDetailsAutoID FROM`srp_erp_customerinvoicedetails` where invoiceAutoID = $invoiceAutoID AND type = 'Project'")->result_array();
        }
        if(($invoicetype =='Project')&&(!empty($invoiceAutoID))&&($projectID!=$projectID_detail['projectID']&&$projectID_detail['projectID']!=''))
        {

            if((!empty($detailexist)&&($detailexist!='')))
            {
                $this->session->set_flashdata('e', 'Please delete all the records and change the project');
                return array('status' => false);
                exit;
            }
        }

        $rebate = getPolicyValues('CRP', 'All');
        if($rebate == 1) {
            $rebateDet = $this->db->query("SELECT rebatePercentage, rebateGLAutoID FROM `srp_erp_customermaster` WHERE customerAutoID = {$customerID}")->row_array();
            if(!empty($rebate)) {
                $data['rebateGLAutoID'] = $rebateDet['rebateGLAutoID'];
                $data['rebatePercentage'] = $rebateDet['rebatePercentage'];
            }
        } else {
            $data['rebateGLAutoID'] = null;
            $data['rebatePercentage'] = null;
        }

        if($financeyearperiodYN==1) {
            $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));

            $FYBegin = input_format_date($financeyr[0], $date_format_policy);
            $FYEnd = input_format_date($financeyr[1], $date_format_policy);
        }else{
            $financeYearDetails=get_financial_year($invoiceDate);
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
            $financePeriodDetails=get_financial_period_date_wise($invoiceDate);

            if(empty($financePeriodDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID') ?? ''));
        //$location = explode('|', trim($this->input->post('location_dec') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        if ($this->input->post('RVbankCode')) {
            $bank_detail = fetch_gl_account_desc(trim($this->input->post('RVbankCode') ?? ''));
            $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
            $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
            $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
            $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
            $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
            $data['invoicebank'] = $bank_detail['bankName'];
            $data['invoicebankBranch'] = $bank_detail['bankBranch'];
            $data['invoicebankSwiftCode'] = $bank_detail['bankSwiftCode'];
            $data['invoicebankAccount'] = $bank_detail['bankAccountNumber'];
            $data['invoicebankType'] = $bank_detail['subCategory'];
        }
        $data['documentID'] = 'CINV';
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        $data['projectID'] = $projectID;
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['invoiceDate'] = trim($invoiceDate);
        $data['customerInvoiceDate'] = trim($customerInvoiceDate);
        $data['invoiceDueDate'] = trim($invoiceDueDate);

        /* $acknowledgementDateYN = getPolicyValues('SAD', 'All');
        if(!empty($acknowledgementDateYN) && $acknowledgementDateYN == 1) {
            $data['acknowledgementDate'] = trim($acknowledgeDate);
        } else {
            $data['acknowledgementDate'] = trim($invoiceDate);
        } */

        $invoiceNarration = ($this->input->post('invoiceNarration'));
        $data['invoiceNarration'] = str_replace('<br />', PHP_EOL, $invoiceNarration);
        //$data['invoiceNarration'] = trim_desc($this->input->post('invoiceNarration'));
        
        $crTypes = explode('<table', $this->input->post('invoiceNote'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['invoiceNote'] = $notes;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['salesPersonID'] = trim($this->input->post('salesPersonID') ?? '');
        if ($data['salesPersonID']) {
            $code = explode(' | ', trim($this->input->post('salesPerson') ?? ''));
            $data['SalesPersonCode'] = trim($code[0] ?? '');
        }
        // $data['wareHouseCode'] = trim($location[0] ?? '');
        // $data['wareHouseLocation'] = trim($location[1] ?? '');
        // $data['wareHouseDescription'] = trim($location[2] ?? '');
        $data['invoiceType'] = trim($this->input->post('invoiceType') ?? '');
        if($this->input->post('invoiceType')=='Operation'){
            $data['isOpYN'] =1;
        }
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $data['isPrintDN'] = trim($this->input->post('isPrintDN') ?? '');
        $data['customerID'] = $customer_arr['customerAutoID'];
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
        $data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
        $data['customerTelephone'] = $customer_arr['customerTelephone'];
        $data['customerFax'] = $customer_arr['customerFax'];
        $data['customerEmail'] = $customer_arr['customerEmail'];
        $data['customerReceivableAutoID'] = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription'] = $customer_arr['receivableDescription'];
        $data['customerReceivableType'] = $customer_arr['receivableType'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
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
        $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];

        if (trim($this->input->post('invoiceAutoID') ?? '')) {

              /*Segment Update To Items in detail tbl start*/
              $comapnyid = current_companyID();
              $invID = $this->input->post('invoiceAutoID');
              $master_rec = $this->db->query("SELECT
                                              segmentID,
                                              invoiceAutoID 
                                              FROM 
                                              `srp_erp_customerinvoicemaster`
                                               where
                                               companyID = $comapnyid 
                                               AND invoiceAutoID = $invID")->row_array();

              if(!empty($master_rec)&&($master_rec!='')){
                  if($master_rec['segmentID']!=trim($segment[0] ?? '')){
                     $this->db->query("UPDATE
                                        srp_erp_customerinvoicedetails 
                                       SET 
                                        srp_erp_customerinvoicedetails.segmentID =  $segment[0] ,
                                        srp_erp_customerinvoicedetails.segmentCode = '{$segment[1]}'
                                       where 
                                        type = 'Item'
                                        AND srp_erp_customerinvoicedetails.invoiceAutoID = $invID");
                  }
              }

              /*Segment Update To Items in detail tbl end*/






            if($projectID_detail['projectID']!=$projectID)
            {
                $invoiceExist = $this->db->query("SELECT invoiceAutoID,invoiceCode,IF(referenceNo = '','-',referenceNo) as referenceNo FROM
                                                 `srp_erp_customerinvoicemaster` WHERE approvedYN = 0 AND projectID = $projectID")->row_array();
                if((!empty($invoiceExist))&&($invoicetype == 'Project'))
                {
                    $this->session->set_flashdata('e', 'There is an unapproved invoice exist for selected project');
                    return array('status' => false);
                    exit;
                }
            }
            $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
            $this->db->update('srp_erp_customerinvoicemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Invoice Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                if(($invoicetype == 'Project'))
                {
                    $cutomerinvoiceexist = $this->db->query("SELECT srp_erp_customerinvoicedetails.invoiceDetailsAutoID FROM `srp_erp_customerinvoicedetails` 
                LEFT JOIN srp_erp_customerinvoicemaster invoicemaster on invoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
                where type = 'Project' AND invoicemaster.InvoiceAutoID = $invoiceAutoID  AND invoicemaster.projectID = $projectID")->row_array();

                    if(($invoicetype == 'Project')&&((empty($cutomerinvoiceexist['invoiceDetailsAutoID']))||($cutomerinvoiceexist['invoiceDetailsAutoID'] == '')))
                    {

                        $this->save_project_detail($invoiceAutoID,$projectID);
                    }
                }

                // update_warehouse_items();
                // update_item_master();
                $this->session->set_flashdata('s', 'Invoice Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('invoiceAutoID'));
            }
        } else {
            //$this->load->library('sequence');
            if($invoicetype == 'Project')
            {
                $invoiceExist = $this->db->query("SELECT invoiceAutoID,invoiceCode,IF(referenceNo = '','-',referenceNo) as referenceNo FROM
                                             `srp_erp_customerinvoicemaster` WHERE  approvedYN = 0 AND projectID = $projectID")->row_array();
                if((!empty($invoiceExist))&&($invoicetype == 'Project'))
                {
                    $this->session->set_flashdata('e', 'There is an unapproved invoice exist for selected project');
                    return array('status' => false);
                    exit;
                }

            }

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['invoiceCode'] = 0;
            //if ($data['isPrintDN']==1) {
            $data['deliveryNoteSystemCode'] = $this->sequence->sequence_generator('DLN');
            //}

            $this->db->insert('srp_erp_customerinvoicemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Invoice   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                if(($invoicetype == 'Project'))
                {
                    $this->save_project_detail($last_id,$projectID);
                }
                $this->session->set_flashdata('s', 'Invoice Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }
 
    function fetch_discount_setup_percentage()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $warehouseAutoID = $this->input->post('warehouseAutoID');
        $date = $this->input->post('date');
        $date_format_policy = date_format_policy();
        $invoiceDate = input_format_date($date, $date_format_policy);
        $compID = current_companyID();
        if(!empty($itemAutoID) && !empty($warehouseAutoID)) {
            $discount = $this->db->select("customerID AS promotionID, IF(applyToAllItem = 1, commissionPercentage, discountPercentage) discountPercentage")
            ->from("srp_erp_pos_customers")
            ->join("srp_erp_pos_promotionwarehouses", "srp_erp_pos_promotionwarehouses.promotionID = srp_erp_pos_customers.customerID")
            ->join("srp_erp_pos_promotionapplicableitems", "srp_erp_pos_promotionapplicableitems.promotionID = srp_erp_pos_customers.customerID 
                                                            AND srp_erp_pos_promotionapplicableitems.companyID = {$compID} 
                                                            AND srp_erp_pos_promotionapplicableitems.itemAutoID = {$itemAutoID} 
                                                            AND srp_erp_pos_promotionapplicableitems.isActive = 1", "LEFT")
            ->where("'{$invoiceDate}' BETWEEN dateFrom AND dateTo")
            ->where("srp_erp_pos_customers.companyID", $compID)
            ->where("srp_erp_pos_promotionwarehouses.companyID", $compID)
            ->where("srp_erp_pos_promotionwarehouses.isActive", 1)
            ->where("srp_erp_pos_promotionwarehouses.wareHouseID", $warehouseAutoID)
            ->where("posType", 2)
            ->where("srp_erp_pos_customers.isActive", 1)
            ->get()->row_array();

            if($discount['discountPercentage'] != null && $discount['discountPercentage'] > 0) {
                return $discount;
            } else {
                return null;
            }
        }
    }
    
    function fetch_line_tax_and_vat()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $data['isGroupByTax'] =  existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',trim($this->input->post('invoiceAutoID') ?? ''),'CINV','invoiceAutoID');
        if($data['isGroupByTax'] == 1){ 
            $data['dropdown'] = fetch_line_wise_itemTaxFormulaID($itemAutoID,'taxMasterAutoID','taxDescription', 1);
            $selected_itemTax =   array_column($data['dropdown'], 'assignedItemTaxFormula');
            $data['selected_itemTax'] =   $selected_itemTax[0];
        }else { 
            $this->db->SELECT("taxMasterAutoID,taxDescription,taxShortCode,taxPercentage");
            $this->db->FROM('srp_erp_taxmaster');
            $this->db->where('taxType', 1);
            $this->db->where('isActive', 1);
            $this->db->where('isApplicableforTotal', 0);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $data = $this->db->get()->result_array();
        }
        return $data;
    }

    function load_line_tax_amount()
    {
        $amnt=0;
        $applicableAmnt=$this->input->post('applicableAmnt');
        $taxCalculationformulaID=$this->input->post('taxtype');
        $disount = trim($this->input->post('discount') ?? '');
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',trim($this->input->post('invoiceAutoID') ?? ''),'CINV', 'invoiceAutoID');
        if($isGroupByTax == 1){
            //$amnt = fetch_line_wise_itemTaxcalculation($taxCalculationformulaID,$applicableAmnt,$disount, 'CINV', trim($this->input->post('invoiceAutoID') ?? ''));
            $return = fetch_line_wise_itemTaxcalculation($taxCalculationformulaID,$applicableAmnt,$disount, 'CINV', trim($this->input->post('invoiceAutoID') ?? ''));
            if($return['error'] == 1) {
                $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
                $amnt = 0;
            } else {
                $amnt = $return['amount'];
            }
        }
        return $amnt;
    }

    function update_supply_date()
    {
        $pK = explode('|',trim($this->input->post('pk') ?? ''));
        $invoiceAutoID =  $pK[0];
        $supply_date = $this->input->post('value');
        $companyID = current_companyID();

        $date_format_policy = date_format_policy();
        $format_supply_date = null;
        if (isset($supply_date) && !empty($supply_date)) {
            $format_supply_date = input_format_date($supply_date, $date_format_policy);
        }
        $data['supplyDate'] = $format_supply_date;
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->update('srp_erp_customerinvoicemaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }


    function fetch_converted_price_qty_invoice_new()
    {
        $companyID=$this->common_data['company_data']['company_id'];
        $ContractAutoID = $this->input->post('id');
        $itemAutoID = $this->input->post('itemAutoID');
        $uomID = $this->input->post('uomID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $DetailID = $this->input->post('detailID');
        $documentcode=trim($this->input->post('documentcode') ?? '');
        $details = array();
        $data = $this->db->query("SELECT defaultUnitOfMeasureID, companyLocalSellingPrice,companyLocalWacAmount FROM srp_erp_itemmaster WHERE companyID = {$companyID} AND itemAutoID = {$itemAutoID}")->row_array();
        $conversion = conversionRateUOM_id($uomID, $data['defaultUnitOfMeasureID']);
        $details['conversionRate'] = $conversion;
        if(empty($conversion) || $conversion == 0) {
            $conversion = 1;
        }
        $this->load->model('Receipt_voucher_model');
        $pulled_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty_new($itemAutoID,$wareHouseAutoID,$documentcode,$ContractAutoID,$DetailID);
        if(!empty($wareHouseAutoID)) {
            $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM `srp_erp_itemledger` where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
            $details['qty'] = $stock['currentStock'] * $conversion;
            $details['Unapproved_stock'] = $pulled_stock['Unapproved_stock'] * $conversion;
            $details['qty_pulleddoc'] =  $details['qty'] - ($pulled_stock['Unapproved_stock'] * $conversion);
        } else {
            $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM `srp_erp_itemledger` where itemAutoID="' . $itemAutoID . '" ')->row_array();
            $details['qty'] = $stock['currentStock'] * $conversion;
            $details['Unapproved_stock'] = $pulled_stock['Unapproved_stock'] * $conversion;
            $details['qty_pulleddoc'] =   $details['qty'] - ($pulled_stock['Unapproved_stock'] * $conversion);
        }
        $this->load->model('Item_model');
        $price = $this->Item_model->fetch_sales_price_customerWise($data['companyLocalSellingPrice']);
        $details['price'] = $price['amount'] / $conversion;
        $this->db->select('transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('contractAutoID', $ContractAutoID);
        $result = $this->db->get('srp_erp_contractmaster')->row_array();
        $localCurrency = currency_conversion($result['companyLocalCurrency'], $result['transactionCurrency']);
        $details['localwacamount'] = round( (($data['companyLocalWacAmount'] / $localCurrency['conversion'])/$conversion),$result['transactionCurrencyDecimalPlaces']);
        return $details;
    }

    function fetch_do_detail_table()
    {
        $companyID = current_companyID();
        $DOAutoID = trim($this->input->post('DOAutoID') ?? '');
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $retentionPercentage = 0;
        //$openContract = getPolicyValues('OCE', 'All');
        $where = '';
        // if($openContract != 1) {
        //     $where = " AND conDet.invoicedYN = 0 ";
        // }
        $secondaryCode = getPolicyValues('SSC', 'All');
        $item_code = 'items.itemSystemCode';
        $item_code_alias = 'items.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'items.seconeryItemCode as itemSystemCode';
        }

        if($invoiceAutoID){
            $this->db->where('invoiceAutoID',$invoiceAutoID);
            $retentionPercentage = $this->db->from('srp_erp_customerinvoicemaster')->get()->row('retentionPercentage');
        }


        $data['detail'] = $this->db->query("SELECT
                                                doDet.*,
                                                IFNULL( taxAmount, 0 ) /*/ doDet.requestedQty*/ AS taxAmount,
                                                items.mainCategory AS invmaincat,
                                                items.seconeryItemCode AS itemSystemCode ,
                                                IFNULL( srp_erp_taxcalculationformulamaster.Description, '-' ) AS Description,
                                                IFNULL( doDet.taxCalculationformulaID, 0 ) AS taxCalculationformulaID, 
                                                IFNULL(invoicedAmount, 0) as invoicedAmount,
                                                {$retentionPercentage} as retentionPercentage,
                                                $item_code_alias 
                                        FROM
                                            srp_erp_deliveryorderdetails doDet
                                            LEFT JOIN srp_erp_itemmaster items ON items.itemAutoID = doDet.itemAutoID
                                            LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = doDet.taxCalculationformulaID
                                            LEFT JOIN ( 
                                                SELECT SUM(transactionAmount) as invoicedAmount, DODetailsID FROM srp_erp_customerinvoicedetails WHERE DOMasterID = $DOAutoID AND DODetailsID is not null 
                                            ) recTB ON recTB.DODetailsID = doDet.DODetailsAutoID
                                        WHERE
                                            doDet.DOAutoID = '{$DOAutoID}' 
                                        GROUP BY
                                            DODetailsAutoID")->result_array();



        $this->db->select("wareHouseCode,wareHouseDescription,companyCode,wareHouseAutoID,wareHouseLocation");
        $this->db->from('srp_erp_warehousemaster');
        $this->db->where('companyID', $companyID);
        $this->db->where('isActive', 1);
        $data['ware_house'] = $this->db->get()->result_array();
        $data['tax_master'] = all_tax_formula_drop_groupByTax(1);//all_tax_drop(1, 0);

        // print_r($data['tax_master']); exit;

        return $data;
    }

    function save_do_base_items()
    {
        $this->db->trans_start();
        $retensionEnabled = getPolicyValues('RETO', 'All');

        
        $this->db->select('srp_erp_deliveryorderdetails.*,sum(srp_erp_deliveryorderdetails.requestedQty) AS receivedQty,srp_erp_deliveryorder.DOCode');
        $this->db->from('srp_erp_deliveryorderdetails');
        $this->db->where_in('srp_erp_deliveryorderdetails.DODetailsAutoID', $this->input->post('DetailsID'));
        $this->db->join('srp_erp_deliveryorder', 'srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID');
        $this->db->join('srp_erp_customerinvoicedetails', 'srp_erp_customerinvoicedetails.DODetailsID = srp_erp_deliveryorderdetails.DODetailsAutoID', 'left');
        $this->db->group_by("DODetailsAutoID");
        $query = $this->db->get()->result_array();
    
        $this->db->select('invoiceDate, invoiceCode,customerID,customerSystemCode,customerName,customerCurrency,customerCurrencyID,customerCurrencyDecimalPlaces,transactionCurrencyID, companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,segmentID,segmentCode,transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,retentionPercentage');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->where('invoiceAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $master = $this->db->get()->row_array();
        
        $amount = $this->input->post('amount');
        $remarks = $this->input->post('remarks');
        $taxCalculationFormulaID = $this->input->post('taxCalculationFormulaID');
        $tex_id = $this->input->post('tex_id');
        $tex_amount = $this->input->post('tex_amount');

      

        for ($i = 0; $i < count($query); $i++) {
            $item_data = fetch_item_data($query[$i]['itemAutoID']);
            $taxCalculationFormulaID[$i] = (isset($taxCalculationFormulaID[$i]) && $taxCalculationFormulaID[$i] > 0) ? $taxCalculationFormulaID[$i] :  $tex_id[$i];
            $query[$i]['taxAmount'] = ($tex_amount[$i]);
           
           // $amount[$i] = $amount[$i] + $tex_amount[$i];
            $query[$i]['deliveredQty'] = $amount[$i] / ($query[$i]['deliveredTransactionAmount'] / $query[$i]['deliveredQty']);

    

            $data[$i]['type'] = 'DO';
            $data[$i]['DOMasterID'] = $query[$i]['DOAutoID'];
            $data[$i]['taxCalculationFormulaID'] = $taxCalculationFormulaID[$i];
            $data[$i]['DODetailsID'] = $query[$i]['DODetailsAutoID'];
            $data[$i]['invoiceAutoID'] = trim($this->input->post('invoiceAutoID') ?? '');
            $data[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
            $data[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
            $data[$i]['itemDescription'] = $query[$i]['itemDescription'];
            $data[$i]['defaultUOM'] = $query[$i]['defaultUOM'];
            $data[$i]['defaultUOMID'] = $query[$i]['defaultUOMID'];
            $data[$i]['unitOfMeasure'] = $query[$i]['unitOfMeasure'];
            $data[$i]['unitOfMeasureID'] = $query[$i]['unitOfMeasureID'];
            $data[$i]['conversionRateUOM'] = $query[$i]['conversionRateUOM'];
            $data[$i]['comment'] = $query[$i]['comment'];
            $data[$i]['requestedQty'] = $query[$i]['deliveredQty'];
           // $data[$i]['unittransactionAmount'] = ($query[$i]['deliveredTransactionAmount']) / $query[$i]['deliveredQty'];
            
            $data[$i]['unittransactionAmount'] = $query[$i]['unittransactionAmount']; //($query[$i]['deliveredTransactionAmount'] / $query[$i]['deliveredQty']);

            $query[$i]['deliveredTransactionAmount'] = $query[$i]['deliveredTransactionAmount'];
            
            $data[$i]['discountAmount'] = 0;
            $data[$i]['discountPercentage'] = 0;
            $data[$i]['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
            $data[$i]['itemCategory'] = trim($item_data['mainCategory'] ?? '');
            $data[$i]['segmentID'] = $master['segmentID'];
            $data[$i]['segmentCode'] = $master['segmentCode'];

            $companyID = current_companyID();
            $un_billed_gl = $this->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory FROM srp_erp_chartofaccounts
                                          WHERE GLAutoID = (
                                              SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'UBI' AND companyID = {$companyID}
                                          ) AND companyID= {$companyID}")->row_array();

            $data[$i]['revenueGLAutoID'] = $un_billed_gl['GLAutoID'];
            $data[$i]['revenueSystemGLCode'] = $un_billed_gl['systemAccountCode'];
            $data[$i]['revenueGLCode'] = $un_billed_gl['GLSecondaryCode'];
            $data[$i]['revenueGLDescription'] = $un_billed_gl['GLDescription'];
            $data[$i]['revenueGLType'] = $un_billed_gl['subCategory'];
            
            $invoicedAmount = $this->db->query("SELECT IFNULL(SUM(transactionAmount), 0) as invoicedAmount, DODetailsID FROM srp_erp_customerinvoicedetails WHERE DOMasterID = {$query[$i]['DOAutoID']} AND DODetailsID = {$query[$i]['DODetailsAutoID']}")->row('invoicedAmount');
            $data[$i]['due_amount'] = round(($query[$i]['deliveredTransactionAmount'] - $invoicedAmount), $master['transactionCurrencyDecimalPlaces']);
            $data[$i]['balance_amount'] = round(($data[$i]['due_amount']-$amount[$i]), $master['transactionCurrencyDecimalPlaces']);
            
            $data[$i]['taxAmount'] =round($query[$i]['taxAmount'], $master['transactionCurrencyDecimalPlaces']);
            $data[$i]['transactionAmount'] = round($amount[$i], $master['transactionCurrencyDecimalPlaces']);
            
            if($retensionEnabled == 1){
                $data[$i]['retensionPercentage'] = $master['retentionPercentage'];
                $data[$i]['retensionValue'] = round((($data[$i]['transactionAmount'] * $master['retentionPercentage']) / 100),2);
            }
                    
            $companyLocalAmount = $data[$i]['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data[$i]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $companyReportingAmount = $data[$i]['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data[$i]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $customerAmount = $data[$i]['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data[$i]['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $data[$i]['comment'] = $query[$i]['comment'];
            $data[$i]['remarks'] = $remarks[$i];
            $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$i]['modifiedDateTime'] = $this->common_data['current_date'];
            $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$i]['createdPCID'] = $this->common_data['current_pc'];
            $data[$i]['createdUserID'] = $this->common_data['current_userID'];
            $data[$i]['createdUserName'] = $this->common_data['current_user'];
            $data[$i]['createdDateTime'] = $this->common_data['current_date'];

        
            if(!empty($query[$i]['DODetailsAutoID']))
            {
                if ($data[$i]['due_amount']-$amount[$i])
                {
                    $do_data['invoicedYN'] = 1;
                    $this->db->where('DODetailsAutoID', $query[$i]['DODetailsAutoID']);
                    $this->db->update('srp_erp_deliveryorderdetails', $do_data);
                }
            }
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_customerinvoicedetails', $data);

            $companyID = current_companyID();
            $invoiceAutoID =  trim($this->input->post('invoiceAutoID') ?? '');

            $CINVTax = $this->db->query("SELECT
                                            customerID,
                                            customerCountryID,
                                            vatEligible,
                                            srp_erp_customerinvoicedetails.taxCalculationFormulaID,
                                            srp_erp_customerinvoicedetails.invoiceAutoID,
                                            invoiceDetailsAutoID,
                                            (srp_erp_customerinvoicedetails.transactionAmount - IFNULL(srp_erp_customerinvoicedetails.retensionValue,0)) as transactionAmount,
                                            ((requestedQty * unittransactionAmount) - IFNULL(srp_erp_customerinvoicedetails.retensionValue,0)) AS totalAmount,
                                            IFNULL((requestedQty * discountAmount),0) as discountAmount,
                                            srp_erp_customerinvoicedetails.contractAutoID, srp_erp_customerinvoicedetails.contractDetailsAutoID, invoiceDetailsAutoID, 
                                            IFNULL(taxAmount,0) as taxAmount,
                                            srp_erp_customerinvoicemaster.invoiceType,
                                            IFNULL(srp_erp_customerinvoicedetails.retensionValue,0) as retensionValue
                                        from 
                                            srp_erp_customerinvoicedetails
                                        JOIN srp_erp_customerinvoicemaster   ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
                                        LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID
                                        where 
                                            srp_erp_customerinvoicedetails.companyID = {$companyID} 
                                            AND srp_erp_customerinvoicedetails.invoiceAutoID  = {$invoiceAutoID}")->result_array();
            
      
            if(existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',trim($this->input->post('invoiceAutoID') ?? ''),'CINV','invoiceAutoID')== 1){
                if(!empty($CINVTax)){
                    foreach($CINVTax as $val){

                        $this->db->select('*');
                        $this->db->where('taxCalculationformulaID',$val['taxCalculationFormulaID']);
                        $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

                        $val['taxDescription'] = $master['Description'];

                        if($val['taxCalculationFormulaID'] != 0){
                           tax_calculation_vat('srp_erp_customerinvoicetaxdetails',$val,$val['taxCalculationFormulaID'],'invoiceAutoID',trim($this->input->post('invoiceAutoID') ?? ''),$val['totalAmount'],'CINV',$val['invoiceDetailsAutoID'],$val['discountAmount'],1,0,null,$val['retensionValue']);
                        }
                    }
                }
            }

            $details = $this->db->query("SELECT * FROM srp_erp_customerinvoicedetails WHERE invoiceAutoID = $invoiceAutoID")->result_array();

            foreach ($details as $det) {

                //reverse update retension amounts

                $dataExist = $this->db->query("SELECT COUNT(taxLedgerAutoID) as taxledgerID 
                                                FROM srp_erp_taxledger 
                                                WHERE documentID = 'CINV' AND companyID = {$companyID} AND documentDetailAutoID =  {$det['invoiceDetailsAutoID']}"
                                            )->row('taxledgerID');
    
                if($dataExist == 0) {
                    $ledgerDet = $this->db->query("SELECT
                                        IF(srp_erp_taxmaster.taxCategory = 2, (SELECT vatRegisterYN FROM `srp_erp_company` WHERE company_id = {$companyID}), srp_erp_taxmaster.isClaimable) AS isClaimable,
                                        customerCountryID,
                                        vatEligible,
                                        customerID,
                                        srp_erp_taxledger.*, outputVatGLAccountAutoID,
                                        IF(taxCategory = 2 ,outputVatTransferGLAccountAutoID,taxGlAutoID) as outputVatTransferGLAccountAutoID, deliveredTransactionAmount 
                                    FROM
                                        srp_erp_taxledger
                                        JOIN ( 
                                            SELECT srp_erp_deliveryorderdetails.deliveredTransactionAmount, srp_erp_deliveryorderdetails.DOAutoID, DODetailsAutoID, customerID
                                            FROM srp_erp_deliveryorderdetails 
                                            JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
                                        ) mastertbl ON mastertbl.DOAutoID = srp_erp_taxledger.documentMasterAutoID 
                                        AND srp_erp_taxledger.documentID = 'DO' AND srp_erp_taxledger.documentDetailAutoID = mastertbl.DODetailsAutoID
                                        JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = mastertbl.customerID
                                        JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                                        JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                        WHERE srp_erp_taxmaster.taxCategory = 2 AND DOAutoID = {$det['DOMasterID']} AND documentDetailAutoID = {$det['DODetailsID']}")->result_array();
        
                    if(!empty($ledgerDet)) {
                        foreach ($ledgerDet as $val) {
                            $dataleg['documentID'] = 'CINV';
                            $dataleg['documentMasterAutoID'] = $invoiceAutoID;
                            $dataleg['documentDetailAutoID'] = $det['invoiceDetailsAutoID'];
                            $dataleg['taxDetailAutoID'] = null;
                            $dataleg['taxPercentage'] = 0;
                            $dataleg['ismanuallychanged'] = 0;
                            $dataleg['isClaimable'] = $val['isClaimable'];
                            $dataleg['taxFormulaMasterID'] = $val['taxFormulaMasterID'];
                            $dataleg['taxFormulaDetailID'] = $val['taxFormulaDetailID'];
                            $dataleg['taxMasterID'] = $val['taxMasterID'];
                            $dataleg['amount'] = ($val['amount'] / $val['deliveredTransactionAmount']) * $det['transactionAmount'];
                            $dataleg['formula'] = $val['formula'];
                            $dataleg['taxGlAutoID'] = $val['outputVatGLAccountAutoID'];
                            $dataleg['transferGLAutoID'] = $val['outputVatTransferGLAccountAutoID'];
                            $dataleg['countryID'] = $val['customerCountryID'];
                            $dataleg['partyVATEligibleYN'] = $val['vatEligible'];
                            $dataleg['partyID'] = $val['customerID'];
                            $dataleg['locationID'] = null;
                            $dataleg['locationType'] = null;
                            $dataleg['companyCode'] = $this->common_data['company_data']['company_code'];
                            $dataleg['companyID'] = $this->common_data['company_data']['company_id'];
                            $dataleg['createdUserGroup'] = $this->common_data['user_group'];
                            $dataleg['createdPCID'] = $this->common_data['current_pc'];
                            $dataleg['createdUserID'] = $this->common_data['current_userID'];
                            $dataleg['createdUserName'] = $this->common_data['current_user'];
                            $dataleg['createdDateTime'] = $this->common_data['current_date'];
        
                            $ledgerEntry = $this->db->insert('srp_erp_taxledger', $dataleg);
                        }
                    }
                }
            }
          
            /** Added By : (SME-2299)*/
            $invoice_id = $this->input->post('invoiceAutoID');
            $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoice_id}")->row_array();
            if(!empty($rebate)) {
                $this->calculate_rebate_amount($invoice_id);
            }
            /** End (SME-2299)*/

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Details Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice ' . count($query) . ' Item Details Saved Successfully.');
            }
        } else {
            return array('e', 'There is no data to process');
        }
    }

    function load_customer_dropdown()
    {
        $search = $this->input->post('search_value');
        $selected_customers = $this->input->post('selected_customers');
        $data_arr = [];
        $where = '';
        if(!empty($selected_customers)) {
            $where = ' OR customerAutoID IN (' . join(',', $selected_customers) . ')';
        }

        if(!empty($search)) {
            $this->db->select("customerAutoID,customerName,customerSystemCode");
            $this->db->from('srp_erp_customermaster');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('deletedYN', 0);
            $this->db->where('isActive', 1);
            $this->db->where("(customerName LIKE '%$search%' OR customerSystemCode LIKE '%$search%') $where");
            $customer = $this->db->get()->result_array();
        }

        if (!empty($customer)) {
            foreach ($customer as $row) {
                $data_arr[trim($row['customerAutoID'] ?? '')] = trim($row['customerSystemCode'] . ' | ' . $row['customerName']);
            }
        }
        
        return form_dropdown('customerCode[]', $data_arr, '', 'class="form-control" id="customer_array" multiple="multiple" onchange="Otable.draw()"');
    }
    function get_invoice_template_details($id){

        $this->db->select("*");
        $this->db->from('srp_erp_invoicetemplatemaster');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('invoiceTemplateMasterID', $id);
        $this->db->where('status', 1);
        $invoiceDetailsTemp = $this->db->get()->result_array();
        return $invoiceDetailsTemp;

    }

    function fetch_contract_details(){

        $contractAutoID = $this->input->post('contractAutoID');

        $this->db->select("*");
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('contractAutoID', $contractAutoID);
        $contract_detail = $this->db->get()->row_array();

        return $contract_detail;

    }

    function fetch_delivery_order_details($auto_id){

        $this->db->select("*");
        $this->db->from('srp_erp_deliveryorderdetails');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('DOAutoID', $auto_id);
        $do_order_details = $this->db->get()->result_array();

        return $do_order_details;
        
    }

    function fetch_delivery_customerinvoice_items($auto_id){

        $this->db->select("*");
        $this->db->from('srp_erp_customerinvoicedoitems');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('DOAutoID', $auto_id);
        $do_order_details = $this->db->get()->result_array();

        return $do_order_details;
        
    }

    function add_do_detail_item_invoice(){

        $autoID = $this->input->post('autoID');
        $selected = $this->input->post('selected');
        $DODetailsAutoID = $this->input->post('DODetailsAutoID');

        //Pull do deatails
        $this->db->select("*");
        $this->db->from('srp_erp_deliveryorderdetails');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('DODetailsAutoID', $DODetailsAutoID);
        $do_order_detail = $this->db->get()->row_array();

        $data = array();

        if($selected == 'true'){
            
            $data = $do_order_detail;

            //check exists
            $ex_record = $this->db->where('DODetailsAutoID',$DODetailsAutoID)->from('srp_erp_customerinvoicedoitems')->get()->row_array();
            
            if(empty($ex_record)){
                $this->db->insert('srp_erp_customerinvoicedoitems',$data);
            }
            

        }else{    
            $this->db->where('DODetailsAutoID',$DODetailsAutoID)->delete('srp_erp_customerinvoicedoitems');
        }

        //get the total of
        $this->db->select("IFNULL(SUM(transactionAmount),0) as amount");
        $this->db->from('srp_erp_customerinvoicedoitems');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('DOAutoID', $autoID);
        $total = $this->db->get()->row_array();

        return $total['amount'];
        
    }

    function get_retension_details(){
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $documentID = $this->input->post('documentID');

        if($documentID == 'SUP'){
            //srp_erp_customerinvoicemaster
            $this->db->select("totalRetension");
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $total = $this->db->get()->row_array();

            //invoiced amount
            $total_payed = $this->db->query("
              SELECT SUM(transactionAmount) as transactionAmount
              FROM srp_erp_paysupplierinvoicemaster
              WHERE isRetensionYN = 1 AND retensionInvoiceID = {$invoiceAutoID}
            ")->row_array();

        }else{
                //srp_erp_customerinvoicemaster
            $this->db->select("totalRetension");
            $this->db->from('srp_erp_customerinvoicemaster');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $total = $this->db->get()->row_array();

             //invoiced amount
            $total_payed = $this->db->query("
                    SELECT SUM(transactionAmount) as transactionAmount
                    FROM srp_erp_customerinvoicemaster
                    WHERE isRetensionYN = 1 AND retensionInvoiceID = {$invoiceAutoID} AND retensionDocumentType = '{$documentID}'
                ")->row_array();

        }
    

       

        $balanced_amount = $total['totalRetension'] - $total_payed['transactionAmount'];

        $base_arr = array('retensionAmount' => $total['totalRetension'],'balance' => $balanced_amount);

        return $base_arr;

    }

    function create_retension_invoice_cinv(){

        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $documentID = $this->input->post('documentID');
        $amountset = $this->input->post('amount');

        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $invoiceDetails = $this->db->get()->row_array();


        //get currency details
        $currencyDetails = $this->db->where('currencyID',$invoiceDetails['transactionCurrencyID'])->from('srp_erp_currencymaster')->get()->row_array();

        $_POST['invoiceType'] = 'DirectIncome';
        $_POST['segment'] = $invoiceDetails['segmentID'].'|'.$invoiceDetails['segmentCode'];
        $_POST['invoiceDate'] = $invoiceDetails['invoiceDate'];
        $_POST['customerInvoiceDate'] = $invoiceDetails['invoiceDate'];
        $_POST['invoiceDueDate'] = date('d-m-Y',strtotime('+1 months',strtotime($invoiceDetails['invoiceDate'])));
        $_POST['transactionCurrencyID'] = $invoiceDetails['transactionCurrencyID'];
        $_POST['financeyear'] = $invoiceDetails['companyFinanceYearID'];
        $_POST['financeyear_period'] = $invoiceDetails['companyFinancePeriodID'];
        $_POST['companyFinanceYear'] = $invoiceDetails['companyFinanceYear'];
        $_POST['companyFinanceYearID'] = $invoiceDetails['companyFinanceYearID'];
        $_POST['isRetensionYN'] = 1;
        $_POST['retensionDocumentType'] = 'CINV';
        $_POST['retensionInvoiceID'] = $invoiceAutoID;
        $_POST['isSytemGenerated'] = 1;
        $_POST['invoiceAutoID'] = '';
        $_POST['currency_code'] = $currencyDetails['CurrencyCode'].' | '.$currencyDetails['CurrencyName'];
        $_POST['customerID'] = $invoiceDetails['customerID'];

        // echo '<pre>'; print_r($invoiceDetails); exit;

        $header = $this->save_invoice_header();

        if($header){

            $last_id = $header['last_id'];

            $gl_code = array();
            $segment_gl = array();
            $amount = array();
            $Netamount = array();
            $gl_code_des = array();
            $description = array();

            $retensionGLdetails = $this->db->where('GLAutoID',$invoiceDetails['retensionGL'])->from('srp_erp_chartofaccounts')->get()->row_array();

            $gl_code[] = $invoiceDetails['retensionGL'];
            $segment_gl[] = $invoiceDetails['segmentID'].'|'.$invoiceDetails['segmentCode'];
            $amount[] = $amountset;
            $Netamount[] = $amount;
            $gl_code_des[] = $retensionGLdetails['systemAccountCode'].' | '.$retensionGLdetails['GLSecondaryCode'].' | '.$retensionGLdetails['GLDescription'].' | '.$retensionGLdetails['subCategory'];
            $description[] = '';

            $_POST['invoiceAutoID'] = $last_id;
            $_POST['gl_code'] = $gl_code;
            $_POST['segment_gl'] = $segment_gl;
            $_POST['amount'] = $amount;
            $_POST['Netamount'] = $Netamount;
            $_POST['gl_code_des'] = $gl_code_des;
            $_POST['description'] = $description;

            $res = $this->save_direct_invoice_detail();

        }

        $this->invoice_confirmation();

        return TRUE;

    }

}