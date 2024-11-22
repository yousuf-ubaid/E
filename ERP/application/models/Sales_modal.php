<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function save_sales_commision_header()
    {
        $sate = ' Save';
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $date = $this->input->post('asOfDate');
        $asOfDate = input_format_date($date, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        if ($financeyearperiodYN == 1) {
            $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($asOfDate);
            if (empty($financeYearDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {
                $FYBegin = $financeYearDetails['beginingDate'];
                $FYEnd = $financeYearDetails['endingDate'];
                $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails = get_financial_period_date_wise($asOfDate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));

        $data['asOfDate'] = $asOfDate;
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionCurrency'] = trim($currency_code[0] ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        $data['Description'] = trim($this->input->post('narration') ?? '');
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('salesCommisionID') ?? '')) {
            $sate = ' Update';
            $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID') ?? ''));
            $this->db->update('srp_erp_salescommisionmaster', $data);
            $last_id = trim($this->input->post('salesCommisionID') ?? '');
            $this->db->trans_complete();
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['salesCommisionCode'] = $this->sequence->sequence_generator('SC');
            $data['salesCommisionCode'] = 0;
            $this->db->insert('srp_erp_salescommisionmaster', $data);
            $last_id = $this->db->insert_id();
        }

        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID') ?? ''));
        $this->db->select('salesPersonID');
        $existsalesperson = $this->db->get('srp_erp_salescommisionperson')->result_array();
        $existsalesperson = array_map(function ($value) {
            return $value['salesPersonID'];
        }, $existsalesperson);
        //$existsalesperson = array_values($existsalesperson[0]);

        $chkspfordelete = array_diff($existsalesperson, $this->input->post('salesPersonID')); // check sales person for delete
        if ($chkspfordelete) {
            $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID') ?? ''));
            $this->db->where_in('salesPersonID', $chkspfordelete);
            $this->db->delete('srp_erp_salescommisionperson');

            $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID') ?? ''));
            $this->db->where_in('salesPersonID', $chkspfordelete);
            $this->db->delete('srp_erp_salescommisiondetail');
        }

        $chkspforinsert = array_diff($this->input->post('salesPersonID'), $existsalesperson); // check sales person for insert

        if ($chkspforinsert) {
            $this->db->select('*');
            $this->db->where_in('salesPersonID', $chkspforinsert);
            $sales_person = $this->db->get('srp_erp_salespersonmaster')->result_array();
            $sales_person_arr = array();
            for ($i = 0; $i < count($sales_person); $i++) {
                $sales_person_arr[$i]['salesCommisionID'] = $last_id;
                $sales_person_arr[$i]['salesPersonID'] = $sales_person[$i]['salesPersonID'];
                $sales_person_arr[$i]['salesPersonCurrencyID'] = $sales_person[$i]['salesPersonCurrencyID'];
                $sales_person_arr[$i]['salesPersonCurrency'] = $sales_person[$i]['salesPersonCurrency'];
                $party_currency = currency_conversionID($data['transactionCurrencyID'], $sales_person[$i]['salesPersonCurrencyID']);
                $sales_person_arr[$i]['salesPersonCurrencyExchangeRate'] = $party_currency['conversion'];
                $sales_person_arr[$i]['salesPersonCurrencyDecimalPlaces'] = $sales_person[$i]['salesPersonCurrencyDecimalPlaces'];
                $sales_person_arr[$i]['liabilityAutoID'] = $sales_person[$i]['receivableAutoID'];
                $sales_person_arr[$i]['expenseAutoID'] = $sales_person[$i]['expanseAutoID'];
                $sales_person_arr[$i]['liabilitySystemGLCode'] = $sales_person[$i]['receivableSystemGLCode'];
                $sales_person_arr[$i]['liabilityGLAccount'] = $sales_person[$i]['receivableGLAccount'];
                $sales_person_arr[$i]['liabilityDescription'] = $sales_person[$i]['receivableDescription'];
                $sales_person_arr[$i]['liabilityType'] = $sales_person[$i]['receivableType'];
                $sales_person_arr[$i]['expenseSystemGLCode'] = $sales_person[$i]['expanseSystemGLCode'];
                $sales_person_arr[$i]['expenseGLAccount'] = $sales_person[$i]['expanseGLAccount'];
                $sales_person_arr[$i]['expenseDescription'] = $sales_person[$i]['expanseDescription'];
                $sales_person_arr[$i]['expenseType'] = $sales_person[$i]['expanseType'];
                $sales_person_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            }
            $this->db->insert_batch('srp_erp_salescommisionperson', $sales_person_arr);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('status' => 0, 'type' => 'e', 'message' => 'Sales commision : ' . $sate . ' Failed.');
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'type' => 's', 'message' => 'Sales commision : ' . $sate . ' Successfully.', 'last_id' => $last_id);
        }
    }

    function laad_sales_commision_header()
    {
        $convertFormat = convert_date_format_sql();
        $data['person'] = '';
        $this->db->select('*,DATE_FORMAT(asOfDate,\'' . $convertFormat . '\') AS asOfDate');
        $this->db->from('srp_erp_salescommisionmaster');
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID') ?? ''));
        $data['header'] = $this->db->get()->row_array();
        $this->db->select('salesPersonID');
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID') ?? ''));
        $person = $this->db->get('srp_erp_salescommisionperson')->result_array();
        $data['person'] = array_column($person, 'salesPersonID');
        return $data;
    }

    function fetch_template_data($salesCommisionID)
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*,DATE_FORMAT(asOfDate,\'' . $convertFormat . '\') AS asOfDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN 
CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->from('srp_erp_salescommisionmaster');
        $this->db->where('salesCommisionID', $salesCommisionID);
        $this->db->where('companyID', $companyID);
        $data['master'] = $this->db->get()->row_array();

        /* $this->db->select('*');
         $this->db->where('salesCommisionID',$salesCommisionID);
         $this->db->from('srp_erp_salescommisionperson');
         $this->db->join('srp_erp_salespersonmaster','srp_erp_salespersonmaster.salesPersonID=srp_erp_salescommisionperson.salesPersonID');
         $data['sales_person'] = $this->db->get()->result_array();

         $this->db->select('srp_erp_salescommisiondetail.*,invoiceCode,DATE_FORMAT(invoiceDate,\''.$convertFormat.'\') AS invoiceDate,invoiceNarration,customerName, `companyLocalCurrencyDecimalPlaces`,companyLocalAmount,companyLocalCurrency');
         $this->db->where('salesCommisionID',$salesCommisionID);
         $this->db->from('srp_erp_salescommisiondetail');
         $this->db->join('srp_erp_customerinvoicemaster','srp_erp_customerinvoicemaster.invoiceAutoID=srp_erp_salescommisiondetail.invoiceAutoID');
         $data['sales_detail'] = $this->db->get()->result_array();*/

        $invoice = $this->db->query("SELECT invoiceAutoID,invoiceCode,DATE_FORMAT(invoiceDate,'" . $convertFormat . "') AS invoiceDate,invoiceNarration,customerName, `companyLocalCurrencyDecimalPlaces`,companyLocalAmount,companyLocalCurrency,srp_erp_salescommisionperson.*,srp_erp_salespersonmaster.*
FROM srp_erp_salescommisionperson 
INNER JOIN srp_erp_salespersonmaster ON srp_erp_salespersonmaster.salesPersonID=srp_erp_salescommisionperson.salesPersonID
LEFT JOIN (SELECT srp_erp_customerinvoicemaster.*,srp_erp_salescommisiondetail.salesCommisionID FROM srp_erp_salescommisiondetail LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_salescommisiondetail.invoiceAutoID WHERE srp_erp_salescommisiondetail.companyID = $companyID AND salesCommisionID = $salesCommisionID) as srp_erp_salescommisiondetail ON srp_erp_salescommisiondetail.salesCommisionID = srp_erp_salescommisionperson.salesCommisionID AND srp_erp_salescommisiondetail.salesPersonID = srp_erp_salescommisionperson.salesPersonID WHERE srp_erp_salescommisionperson.salesCommisionID = $salesCommisionID GROUP BY
	srp_erp_salescommisionperson.`salesPersonID`,
	srp_erp_salescommisiondetail.`invoiceAutoID`")->result_array();
        //echo $this->db->last_query();

        $this->db->select('srp_erp_salescommisionperson.salesPersonID,DATE_FORMAT(datefrom,\'' . $convertFormat . '\') AS datefrom,DATE_FORMAT(dateTo,\'' . $convertFormat . '\') AS dateTo,fromTargetAmount,toTargetAmount,srp_erp_salespersontarget.percentage');
        $this->db->where('salesCommisionID', $salesCommisionID);
        $this->db->from('srp_erp_salespersontarget');
        $this->db->join('srp_erp_salescommisionperson', 'srp_erp_salescommisionperson.salesPersonID=srp_erp_salespersontarget.salesPersonID');
        $sales_target = $this->db->get()->result_array();

        $value = array();
        $valueSales = array();
        if (!empty($invoice)) {
            foreach ($invoice as $val) {
                if ($val["invoiceAutoID"] == NULL) {
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['salesperson'] = array('SalesPersonCode' => $val["SalesPersonCode"], 'SalesPersonName' => $val["SalesPersonName"], 'salesPersonID' => $val["salesPersonID"], 'adjustment' => $val["adjustment"], 'percentage' => $val["percentage"], 'salesPersonImage' => $val["salesPersonImage"], 'SecondaryCode' => $val["SecondaryCode"], 'contactNumber' => $val["contactNumber"], 'SalesPersonEmail' => $val["SalesPersonEmail"], 'wareHouseDescription' => $val["wareHouseDescription"], 'SalesPersonAddress' => $val["SalesPersonAddress"], 'salesPersonCurrency' => $val["salesPersonCurrency"], 'salesPersonTargetType' => $val["salesPersonTargetType"], 'salesPersonTarget' => $val["salesPersonTarget"], 'salesPersonCurrencyDecimalPlaces' => $val["salesPersonCurrencyDecimalPlaces"], 'salesPersonCurrency' => $val["salesPersonCurrency"]);
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['invoice'] = array();
                } else {
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['salesperson'] = $val["invoiceAutoID"] == NULL ? array() : array('SalesPersonCode' => $val["SalesPersonCode"], 'SalesPersonName' => $val["SalesPersonName"], 'salesPersonID' => $val["salesPersonID"], 'adjustment' => $val["adjustment"], 'percentage' => $val["percentage"], 'salesPersonImage' => $val["salesPersonImage"], 'SecondaryCode' => $val["SecondaryCode"], 'contactNumber' => $val["contactNumber"], 'SalesPersonEmail' => $val["SalesPersonEmail"], 'wareHouseDescription' => $val["wareHouseDescription"], 'SalesPersonAddress' => $val["SalesPersonAddress"], 'salesPersonCurrency' => $val["salesPersonCurrency"], 'salesPersonTargetType' => $val["salesPersonTargetType"], 'salesPersonTarget' => $val["salesPersonTarget"], 'salesPersonCurrencyDecimalPlaces' => $val["salesPersonCurrencyDecimalPlaces"], 'salesPersonCurrency' => $val["salesPersonCurrency"]);
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['invoice'][] = $val["invoiceAutoID"] == NULL ? array() : $val;
                }
            }
        }

        foreach ($value as $val) {
            foreach ($sales_target as $val2) {
                if ($val['salesperson']["salesPersonID"] == $val2['salesPersonID']) {
                    $value[$val['salesperson']["SalesPersonCode"] . '-' . $val['salesperson']['SalesPersonName']]['salestarget'][] = $val2;
                }
            }
        }

        if (!empty($sales_target)) {
            foreach ($sales_target as $val2) {
                $valueSales[$val2["salesPersonID"]][] = array('percentage' => $val2["percentage"], 'fromTargetAmount' => $val2["fromTargetAmount"], 'toTargetAmount' => $val2["toTargetAmount"]);
            }
        }

        $data['invoice'] = $value;
        $data['sales_target'] = $valueSales;

        return $data;

    }

    function fetch_detail_header_lock()
    {
        $this->db->select('salesCommisionID');
        $this->db->from('srp_erp_salescommisiondetail');
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID') ?? ''));
        return $this->db->get()->row_array();
    }

    function fetch_inv_detail($salesCommisionID)
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*');
        $this->db->from('srp_erp_salescommisionmaster');
        $this->db->where('salesCommisionID', $salesCommisionID);
        $this->db->where('companyID', $companyID);
        $data['header'] = $this->db->get()->row_array();

        $this->db->select('srp_erp_customerinvoicemaster.salesPersonID,srp_erp_salespersontarget.*,DATE_FORMAT(datefrom,\'' . $convertFormat . '\') AS datefrom,DATE_FORMAT(dateTo,\'' . $convertFormat . '\') AS dateTo');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->group_by("targetID");
        $this->db->where('srp_erp_customerinvoicemaster.salesPersonID !=', null);
        $this->db->where('srp_erp_customerinvoicemaster.salesPersonID !=', '');
        $this->db->where('approvedYN', '1');
        $this->db->join('srp_erp_salespersonmaster', 'srp_erp_salespersonmaster.salesPersonID=srp_erp_customerinvoicemaster.salesPersonID');
        $this->db->join('srp_erp_salespersontarget', 'srp_erp_salespersontarget.salesPersonID=srp_erp_customerinvoicemaster.salesPersonID');
        $sales_target = $this->db->get()->result_array();

        $invoice = $this->db->query("SELECT srp_erp_salescommisionperson.adjustment,if(srp_erp_salescommisiondetail.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID,1,0) as checked,srp_erp_salescommisionperson.`salesPersonID`,srp_erp_customerinvoicemaster.`invoiceAutoID`, `srp_erp_customerinvoicemaster`.`invoiceCode`, `srp_erp_customerinvoicemaster`.`invoiceDate`, `srp_erp_customerinvoicemaster`.`invoiceNarration`, `srp_erp_customerinvoicemaster`.`customerName`, `srp_erp_customerinvoicemaster`.`companyLocalCurrencyDecimalPlaces`, `srp_erp_customerinvoicemaster`.`companyLocalAmount`, `srp_erp_customerinvoicemaster`.`companyLocalCurrency`,srp_erp_salespersonmaster.SalesPersonCode,SalesPersonName,srp_erp_salescommisionperson.percentage 
FROM srp_erp_salescommisionperson 
INNER JOIN srp_erp_salespersonmaster ON srp_erp_salespersonmaster.salesPersonID=srp_erp_salescommisionperson.salesPersonID  
LEFT JOIN (SELECT * FROM srp_erp_customerinvoicemaster WHERE NOT EXISTS (SELECT * FROM srp_erp_salescommisiondetail WHERE srp_erp_salescommisiondetail.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID AND salesCommisionID != $salesCommisionID)  AND `invoiceDate` <= '" . $data['header']['asOfDate'] . "' AND  srp_erp_customerinvoicemaster.`salesPersonID` IS NOT NULL AND srp_erp_customerinvoicemaster.`salesPersonID` != '' AND srp_erp_customerinvoicemaster.`approvedYN` = '1' AND srp_erp_customerinvoicemaster.`companyID` = '{$companyID}') as srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.salesPersonID = srp_erp_salescommisionperson.salesPersonID
LEFT JOIN (SELECT * FROM srp_erp_salescommisiondetail WHERE companyID = $companyID AND salesCommisionID = $salesCommisionID) as srp_erp_salescommisiondetail ON srp_erp_salescommisiondetail.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE srp_erp_salescommisionperson.salesCommisionID = $salesCommisionID GROUP BY
	srp_erp_salescommisionperson.`salesPersonID`,
	srp_erp_customerinvoicemaster.`invoiceAutoID`")->result_array();
        //echo $this->db->last_query();
        $value = array();
        $valueSales = array();
        if (!empty($invoice)) {
            foreach ($invoice as $val) {
                if ($val["invoiceAutoID"] == NULL) {
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['salesperson'] = array('SalesPersonCode' => $val["SalesPersonCode"], 'SalesPersonName' => $val["SalesPersonName"], 'salesPersonID' => $val["salesPersonID"], 'adjustment' => $val["adjustment"], 'percentage' => $val["percentage"]);
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['invoice'] = array();
                } else {
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['salesperson'] = $val["invoiceAutoID"] == NULL ? array() : array('SalesPersonCode' => $val["SalesPersonCode"], 'SalesPersonName' => $val["SalesPersonName"], 'salesPersonID' => $val["salesPersonID"], 'adjustment' => $val["adjustment"], 'percentage' => $val["percentage"]);
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['invoice'][] = $val["invoiceAutoID"] == NULL ? array() : $val;
                }
            }
        }

        foreach ($value as $val) {
            foreach ($sales_target as $val2) {
                if ($val['salesperson']["salesPersonID"] == $val2['salesPersonID']) {
                    $value[$val['salesperson']["SalesPersonCode"] . '-' . $val['salesperson']['SalesPersonName']]['salestarget'][] = $val2;
                }
            }
        }

        if (!empty($sales_target)) {
            foreach ($sales_target as $val2) {
                $valueSales[$val2["salesPersonID"]][] = array('percentage' => $val2["percentage"], 'fromTargetAmount' => $val2["fromTargetAmount"], 'toTargetAmount' => $val2["toTargetAmount"]);
            }
        }

        $data['invoice'] = $value;
        $data['sales_target'] = $valueSales;
        return $data;
    }

    function sales_commission_detail()
    {
        $this->db->trans_start();
        $companyID = $this->common_data['company_data']['company_id'];
        $salesCommisionID = trim($this->input->post('salesCommisionID') ?? '');
        /*$this->db->select('*');
        $this->db->from('srp_erp_salescommisionmaster');
        $this->db->where('salesCommisionID',$salesCommisionID);
        $this->db->where('companyID',$companyID);
        $header = $this->db->get()->row_array();*/

        $sales_person_arr = array();
        $invoices = $this->input->post('isActive');
        if (!empty($invoices)) {
            foreach ($invoices as $key => $invoice) {
                $invoices_arr = explode('|', $invoices[$key]);
                $data[$key]['salesCommisionID'] = $salesCommisionID;
                $data[$key]['salesPersonID'] = trim($invoices_arr[1] ?? '');
                $data[$key]['invoiceAutoID'] = trim($invoices_arr[0] ?? '');
                $data[$key]['transactionAmount'] = trim($invoices_arr[2] ?? '');
                $data[$key]['companyID'] = $companyID;
                array_push($sales_person_arr, $data[$key]['salesPersonID']);
            }

            $this->db->delete('srp_erp_salescommisiondetail', array('salesCommisionID' => $salesCommisionID));
            if (!empty($data)) {
                $this->db->insert_batch('srp_erp_salescommisiondetail', $data);
            }
        } else {
            $this->db->delete('srp_erp_salescommisiondetail', array('salesCommisionID' => $salesCommisionID));
        }

        $sales_commission_detail_arr = array();
        $this->db->select('commisionSalesPersonID,salesPersonID');
        /* $this->db->where_in('salesPersonID', $sales_person_arr);*/
        $this->db->where('salesCommisionID', $salesCommisionID);
        $this->db->from('srp_erp_salescommisionperson');
        $sales_person_arr = $this->db->get()->result_array();
        for ($i = 0; $i < count($sales_person_arr); $i++) {
            $sales_commission_detail_arr[$i]['salesPersonID'] = $sales_person_arr[$i]['salesPersonID'];
            $sales_commission_detail_arr[$i]['commisionSalesPersonID'] = $sales_person_arr[$i]['commisionSalesPersonID'];
            $sales_commission_detail_arr[$i]['adjustment'] = $this->input->post('adjustment_' . $sales_person_arr[$i]['salesPersonID']);
            $sales_commission_detail_arr[$i]['description'] = $this->input->post('description_' . $sales_person_arr[$i]['salesPersonID']);
            $sales_commission_detail_arr[$i]['invoiceTotal'] = $this->input->post('invoice_total_' . $sales_person_arr[$i]['salesPersonID']);
            $sales_commission_detail_arr[$i]['percentage'] = $this->input->post('percentage_' . $sales_person_arr[$i]['salesPersonID']);
            $sales_commission_detail_arr[$i]['netCommision'] = ((($sales_commission_detail_arr[$i]['invoiceTotal'] / 100) * $sales_commission_detail_arr[$i]['percentage']) + $sales_commission_detail_arr[$i]['adjustment']);
        }

        if (!empty($sales_commission_detail_arr)) {
            $this->db->update_batch('srp_erp_salescommisionperson', $sales_commission_detail_arr, 'commisionSalesPersonID');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('status' => 0, 'type' => 'e', 'message' => 'Sales commision Failed.');
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'type' => 's', 'message' => 'Sales commision created Successfully.', 'last_id' => $salesCommisionID);
        }
    }

    function save_sales_target()
    {
        $this->db->trans_start();
        /*$date_format_policy = date_format_policy();
        $date_f = $this->input->post('datefrom');
        $datefrom = input_format_date($date_f,$date_format_policy);
        $date_t = $this->input->post('dateTo');
        $dateto = input_format_date($date_t,$date_format_policy);*/
        $fromTargetAmount = trim($this->input->post('fromTargetAmount') ?? '');
        $toTargetAmount = trim($this->input->post('toTargetAmount') ?? '');
        $salesPersonID = trim($this->input->post('salesPersonID') ?? '');

        $data['salesPersonID'] = trim($this->input->post('salesPersonID') ?? '');
        /* $data['datefrom'] = $datefrom;
         $data['dateTo'] = $dateto;*/
        $data['currencyID'] = trim($this->input->post('currencyID') ?? '');
        $data['fromTargetAmount'] = $fromTargetAmount;
        $data['toTargetAmount'] = $toTargetAmount;
        $data['percentage'] = trim($this->input->post('percentage') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        if (trim($this->input->post('targetID') ?? '')) {

            $sale_target2 = $this->db->query("SELECT
   max(toTargetAmount) as toTargetAmount,MAX(targetID) as targetID
FROM
   `srp_erp_salespersontarget`
WHERE
   `salesPersonID` = $salesPersonID")->row_array();
            if (!empty($sale_target2)) {
                if (($sale_target2["targetID"] != trim($this->input->post('targetID') ?? '')) && ($toTargetAmount > $sale_target2["toTargetAmount"]))
                    return array('status' => 0, 'type' => 'w', 'message' => 'Invalid sales target range.');
            }

            $sale_target2 = $this->db->query("SELECT
   salesPersonID
FROM
   `srp_erp_salespersontarget`
WHERE
   `salesPersonID` = $salesPersonID
AND `targetID` != " . $this->input->post('targetID') . "
AND (($fromTargetAmount BETWEEN fromTargetAmount
AND toTargetAmount)
or ($toTargetAmount BETWEEN fromTargetAmount
AND toTargetAmount))")->row_array();
            if (!empty($sale_target2)) {
                return array('status' => 0, 'type' => 'w', 'message' => 'Sales target already exists for selected Range.');
            }

            $this->db->where('targetID', trim($this->input->post('targetID') ?? ''));
            $this->db->update('srp_erp_salespersontarget', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'message' => 'Sales Target Record Update Failed.');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'message' => 'Sales Target Record Updated Successfully.', 'last_id' => trim($this->input->post('targetID') ?? ''));
            }
        } else {
            $sale_target2 = $this->db->query("SELECT
   salesPersonID
FROM
   `srp_erp_salespersontarget`
WHERE
   `salesPersonID` = $salesPersonID
AND (($fromTargetAmount BETWEEN fromTargetAmount
AND toTargetAmount)
or ($toTargetAmount BETWEEN fromTargetAmount
AND toTargetAmount))")->row_array();
            if (!empty($sale_target2)) {
                return array('status' => 0, 'type' => 'w', 'message' => 'Sales target already exists for selected Range.');
            }

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_salespersontarget', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'message' => 'Sales Target Record Save Failed.');

            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'message' => 'Sales Target Record Saved Successfully.', 'last_id' => $last_id);
            }
        }
    }

    function delete_sc()
    {
        /*$salesCommisionID = trim($this->input->post('salesCommisionID') ?? '');
        $this->db->delete('srp_erp_salescommisiondetail', array('salesCommisionID' => $salesCommisionID));
        $this->db->delete('srp_erp_salescommisionperson', array('salesCommisionID' => $salesCommisionID));
        $this->db->delete('srp_erp_salescommisionmaster', array('salesCommisionID' => $salesCommisionID));*/
        $this->db->select('*');
        $this->db->from('srp_erp_salescommisiondetail');
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID') ?? ''));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            return array('status' => 1, 'type' => 'e', 'message' => 'please delete all detail records before delete this document.');
        } else {
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID') ?? ''));
            $this->db->update('srp_erp_salescommisionmaster', $data);
            return array('status' => 1, 'type' => 's', 'message' => 'Sales commision Deleted Successfully.');
        }


    }

    function save_sales_person()
    {
        if (!trim($this->input->post('salesPersonID') ?? '') and trim($this->input->post('EIdNo') ?? '')) {
            $this->db->select('salesPersonID,SalesPersonName,SalesPersonCode');
            $this->db->from('srp_erp_salespersonmaster');
            $this->db->where('EIdNo', trim($this->input->post('EIdNo') ?? ''));
            //$this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('status' => 0, 'type' => 'w', 'message' => 'Sales Person : ' . $order_detail['SalesPersonCode'] . ' ' . $order_detail['SalesPersonName'] . '  already exists.', 'last_id' => 1);
            }
        }
        $this->db->trans_start();
        $isactive = 0;
        if (!empty($this->input->post('isActive'))) {
            $isactive = 1;
        }
        $sate = ' Save';
        $segment = explode('|', trim($this->input->post('segmentID') ?? ''));
        $delivery_location = explode('|', trim($this->input->post('delivery_location') ?? ''));
        $liability = fetch_gl_account_desc(trim($this->input->post('receivableAutoID') ?? ''));
        $expanse = fetch_gl_account_desc(trim($this->input->post('expanseAutoID') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $data['SalesPersonName'] = trim($this->input->post('SalesPersonName') ?? '');
        $data['EIdNo'] = trim($this->input->post('EIdNo') ?? '');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['SecondaryCode'] = trim($this->input->post('SecondaryCode') ?? '');
        $data['SalesPersonEmail'] = trim($this->input->post('SalesPersonEmail') ?? '');
        $data['contactNumber'] = trim($this->input->post('contactNumber') ?? '');
        $data['wareHouseAutoID'] = trim($this->input->post('wareHouseAutoID') ?? '');
        $data['SalesPersonAddress'] = trim($this->input->post('SalesPersonAddress') ?? '');
        $data['wareHouseCode'] = trim($delivery_location[0] ?? '');
        $data['wareHouseLocation'] = trim($delivery_location[1] ?? '');
        $data['wareHouseDescription'] = trim($delivery_location[2] ?? '');
        $data['salesPersonTargetType'] = trim($this->input->post('salesPersonTargetType') ?? '');
        $data['salesPersonTarget'] = trim($this->input->post('salesPersonTarget') ?? '');
        $data['receivableAutoID'] = $liability['GLAutoID'];
        $data['receivableSystemGLCode'] = $liability['systemAccountCode'];
        $data['receivableGLAccount'] = $liability['GLSecondaryCode'];
        $data['receivableDescription'] = $liability['GLDescription'];
        $data['receivableType'] = $liability['subCategory'];
        $data['expanseAutoID'] = $expanse['GLAutoID'];
        $data['expanseSystemGLCode'] = $expanse['systemAccountCode'];
        $data['expanseGLAccount'] = $expanse['GLSecondaryCode'];
        $data['expanseDescription'] = $expanse['GLDescription'];
        $data['expanseType'] = $expanse['subCategory'];
        $data['isActive'] = $isactive;
        $data['salesPersonCurrencyID'] = trim($this->input->post('salesPersonCurrencyID') ?? '');
        $data['salesPersonCurrency'] = $currency_code[0];
        $data['salesPersonCurrencyDecimalPlaces'] = fetch_currency_desimal($data['salesPersonCurrency']);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('salesPersonID') ?? '')) {
            $this->db->where('salesPersonID', trim($this->input->post('salesPersonID') ?? ''));
            $this->db->update('srp_erp_salespersonmaster', $data);
            $sate = ' Update';
            $last_id = trim($this->input->post('salesPersonID') ?? '');
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['SalesPersonCode'] = $this->sequence->sequence_generator('REP');
            $data['salesPersonImage'] = 'images/users/default.gif';
            $this->db->insert('srp_erp_salespersonmaster', $data);
            $last_id = $this->db->insert_id();
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('status' => 0, 'type' => 'e', 'message' => 'Sales person  ' . $data['SalesPersonName'] . $sate . ' Update Failed.', 'last_id' => $last_id, 'salesPersonCurrency' => $data['salesPersonCurrency']);
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'type' => 's', 'message' => 'Sales person  ' . $data['SalesPersonName'] . $sate . ' Updated Successfully.', 'last_id' => $last_id, 'salesPersonCurrency' => $data['salesPersonCurrency']);
        }
    }

    function sc_confirmation()
    {
        $salesCommisionID = trim($this->input->post('salesCommisionID') ?? '');
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $companyID = current_companyID();
        $currentuser = current_userID();
        $locationemployee = $this->common_data['emplanglocationid'];

        $this->db->select('salesCommisionID');
        $this->db->from('srp_erp_salescommisiondetail');
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID') ?? ''));
        $result = $this->db->get()->result_array();

        if ($result) {
            $system_id = trim($this->input->post('salesCommisionID') ?? '');
            $this->db->select('salesCommisionCode,companyFinanceYearID,DATE_FORMAT(asOfDate, "%Y") as invYear,DATE_FORMAT(asOfDate, "%m") as invMonth');
            $this->db->where('salesCommisionID', $system_id);
            $this->db->from('srp_erp_salescommisionmaster');
            $master_dt = $this->db->get()->row_array();
            $this->load->library('sequence');
            $lenth = strlen($master_dt['salesCommisionCode']);
            if ($lenth == 1) {
                if ($locationwisecodegenerate == 1) {
                    $this->db->select('locationID');
                    $this->db->where('Erp_companyID', $companyID);
                    $this->db->where('EIdNo', $currentuser);
                    $this->db->from('srp_employeesdetails');
                    $location = $this->db->get()->row_array();
                    if ((empty($location)) || ($location == ' ')) {
                        return array('status' => 0, 'type' => 'e', 'message' => 'Location is not assigned for current employee');
                    } else {
                        if ($locationemployee != '') {
                            $salesCommisionCode = $this->sequence->sequence_generator_location('SC', $master_dt['companyFinanceYearID'], $locationemployee, $master_dt['invYear'], $master_dt['invMonth']);
                        } else {
                            return array('status' => 0, 'type' => 'e', 'message' => 'Location is not assigned for current employee');
                        }
                    }
                } else {
                    $salesCommisionCode = $this->sequence->sequence_generator_fin('SC', $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                }

                $validate_code = validate_code_duplication($salesCommisionCode, 'salesCommisionCode', $system_id, 'salesCommisionID', 'srp_erp_salescommisionmaster');
                if (!empty($validate_code)) {
                    return array('status' => 0, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                }

                $invcod = array(
                    'salesCommisionCode' => $salesCommisionCode
                );
                $this->db->where('salesCommisionID', $system_id);
                $this->db->update('srp_erp_salescommisionmaster', $invcod);
            } else {
                $validate_code = validate_code_duplication($master_dt['salesCommisionCode'], 'salesCommisionCode', $system_id, 'salesCommisionID', 'srp_erp_salescommisionmaster');
                if (!empty($validate_code)) {
                    return array('status' => 0, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                }
            }

            $this->load->library('Approvals');
            $this->db->select('salesCommisionID, salesCommisionCode,asOfDate');
            $this->db->where('salesCommisionID', $salesCommisionID);
            $this->db->from('srp_erp_salescommisionmaster');
            $sc_data = $this->db->get()->row_array();

            $autoApproval = get_document_auto_approval('SC');

            if ($autoApproval == 0) {
                $approvals_status = $this->approvals->auto_approve($sc_data['salesCommisionID'], 'srp_erp_salescommisionmaster', 'salesCommisionID', 'SC', $sc_data['salesCommisionCode'], $sc_data['asOfDate']);
            } elseif ($autoApproval == 1) {
                $approvals_status = $this->approvals->CreateApproval('SC', $sc_data['salesCommisionID'], $sc_data['salesCommisionCode'], 'Sales commision', 'srp_erp_salescommisionmaster', 'salesCommisionID', 0, $sc_data['asOfDate']);
            } else {
                return array('status' => 0, 'type' => 'e', 'message' => 'Approval levels are not set for this document');
            }

            if ($approvals_status == 1) {
                $autoApproval = get_document_auto_approval('SC');

                if ($autoApproval == 0) {
                    $result = $this->save_sc_approval(0, $sc_data['salesCommisionID'], 1, 'Auto Approved');
                    if ($result) {
                        return true;
                    }
                } else {
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']
                    );
                    $this->db->where('salesCommisionID', $salesCommisionID);
                    $this->db->update('srp_erp_salescommisionmaster', $data);
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return array('status' => 0, 'type' => 'e', 'message' => 'There are no records to confirm this document!');
        }
    }

    function save_sc_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('salesCommisionID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['salesCommisionID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'SC');
        }

        if ($approvals_status == 1) {
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_SC($system_code, 'SC');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['salesCommisionID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['salesCommisionCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['asOfDate'];
                $generalledger_arr[$i]['documentType'] = '';
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['asOfDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['asOfDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['Description'];
                $generalledger_arr[$i]['chequeNumber'] = $double_entry['master_data']['referenceNo'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
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
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($amount / $double_entry['gl_detail'][$i]['partyExchangeRate']), $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces']);
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

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Sales Commission Approval Successfully.');
            return true;
        }
    }

    function re_open_salescommishion()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID') ?? ''));
        $this->db->update('srp_erp_salescommisionmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function get_sales_order_report()
    {
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $search = $this->input->post('search');
        $documentTypes = $this->input->post('documentTypes');
        $doctype = '';
        $segmentID = $this->input->post('segmentID');
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        $sales = "";
        $salesperson = $this->input->post('salesperson');
        $statusID = $this->input->post('statusID');
        $status_filter = '';

        $salesepersoncount = is_array($this->input->post('salesperson')) ? sizeof($this->input->post('salesperson')) : 0;
        $segmentIDcount = is_array($this->input->post('segmentID')) ? sizeof($this->input->post('segmentID')) : 0;
        $salespersoncount = $this->db->query("SELECT COUNT(salesPersonID)  as salespersoncount FROM `srp_erp_salespersonmaster` WHERE `companyID` = " . current_companyID() . " ")->row_array();

        $segmentcount = $this->db->query("SELECT COUNT(segmentID) as segmentcount FROM `srp_erp_segment` WHERE `status` = 1  AND `companyID` = " . current_companyID() . " ")->row_array();
        $companyID = current_companyID();

        $salespersonex = '';
        if ($salespersoncount['salespersoncount'] == $salesepersoncount) {

            $salespersonex = '';
        } else {
            if (!empty($salesperson)) {
                $salesPe = implode(',', $salesperson);
                $salespersonex = 'AND srp_erp_contractmaster.salesPersonID IN (' . $salesPe . ')  ';
            }
        }

        $segment = '';
        if ($segmentcount['segmentcount'] == $segmentIDcount) {

            $segment = '';
        } else {

            if (!empty($segmentID)) {
                $seg = implode(',', $segmentID);
                $segment = 'AND srp_erp_contractmaster.segmentID IN (' . $seg . ')  ';
            }
        }
        if (!empty($statusID)) {
            $stat = implode(',', $statusID);
            $status_filter = 'AND contautoid.statusso IN (' . $stat . ')  ';
        }


        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 00:00:00')";
        }
        if ($search) {
            $search = " AND (contractCode LIKE '%" . $search . "%' OR srp_erp_customermaster.customerName LIKE '%" . $search . "%'  OR invoiceAmount LIKE '%" . $search . "%' OR receiptvAmount LIKE '%" . $search . "%' OR transactionAmount LIKE '%" . $search . "%' OR srp_erp_salespersonmaster.SalesPersonName LIKE '%" . $search . "%' OR referenceNo LIKE '%" . $search . "%')";
        } else {
            $search = "";
        }

        if ($documentTypes != 'All') {
            $doctype = " AND srp_erp_contractmaster.documentID = '$documentTypes' ";
        } else {
            $doctype = "";
        }


        $qry = "SELECT statusso as dostatus,DATE_FORMAT(contractDate,'" . $convertFormat . "') as documentDate,DATE_FORMAT( contractExpDate, '%d-%m-%Y' ) AS contractExpDate,
                    IFNULL(srp_erp_contractmaster.referenceNo,'-') as referenceNo, (srp_erp_contractmaster.transactionAmount + IFNULL(taxdetail.taxAmount, 0)) as transactionAmount,
                    (IFNULL(a.invoiceAmount,0) + IFNULL(b.doAmount,0)) as invoiceAmount,
                    (IFNULL(a.nonTaxAmount,0) + IFNULL(b.nonTaxAmountDO,0)) as nonTaxAmount,
                    ((IFNULL( a.receiptvAmount, 0 ) +IFNULL( b.receiptAmountDO, 0 )) + (IFNULL( a.creditNoteAmount, 0 )+IFNULL( b.creditNoteAmountDO, 0 ))+ (IFNULL( a.receiptMatchAmount, 0 ) + IFNULL( b.receiptMatchAmountDO, 0 ))) AS receiptAmount,
                    transactionCurrency, transactionCurrencyDecimalPlaces, srp_erp_customermaster.customerName, contractCode, 
                    srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.documentID,
                    IFNULL(srp_erp_salespersonmaster.SalesPersonName,'-') as SalesPersonName,	
                    IFNULL(segment.segmentCode,'-')	 as segmentCodemaster FROM srp_erp_contractmaster 
                    LEFT JOIN srp_erp_segment segment on segment.segmentID = srp_erp_contractmaster.segmentID 
                    LEFT JOIN srp_erp_salespersonmaster on srp_erp_salespersonmaster.salesPersonID = srp_erp_contractmaster.salesPersonID  
                    LEFT JOIN ( SELECT SUM(ab.invoiceAmount) AS invoiceAmount, SUM(ab.nonTaxAmount) AS nonTaxAmount, SUM(ab.receiptAmount) AS receiptvAmount,
                    SUM(ab.creditNoteAmount) AS creditNoteAmount,SUM(ab.receiptMatchAmount) AS receiptMatchAmount, ab.contractAutoID 
                    FROM ( SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS invoiceAmount, 
                    SUM(civ.transactionAmount) AS nonTaxAmount, SUM(crv.transactionAmount) AS receiptAmount, 
                    SUM(IFNULL(crn.transactionAmount,0)) AS creditNoteAmount,SUM(IFNULL(rvd.transactionAmount,0)) AS receiptMatchAmount,
                    tax.taxPercentage, civ.contractAutoID, civ.invoiceAutoID FROM srp_erp_customerinvoicemaster 
                    LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID 
                    FROM srp_erp_customerinvoicedetails 
                    WHERE companyID = " . current_companyID() . " GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID 
                    LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID 
                    FROM srp_erp_customerinvoicetaxdetails WHERE companyID = " . current_companyID() . " GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID 
                    LEFT JOIN ( SELECT SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS transactionAmount, invoiceAutoID 
                    FROM srp_erp_customerreceiptdetail 
                    LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                    WHERE srp_erp_customerreceiptdetail.companyID = " . current_companyID() . " AND approvedYN = 1 GROUP BY invoiceAutoID ) crv ON crv.invoiceAutoID = civ.invoiceAutoID   
                LEFT JOIN (SELECT SUM( srp_erp_creditnotedetail.transactionAmount) AS transactionAmount,invoiceAutoID FROM srp_erp_creditnotedetail 
                LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID 
                WHERE srp_erp_creditnotedetail.companyID = " . current_companyID() . " AND approvedYN = 1 
                GROUP BY invoiceAutoID ) crn ON crn.invoiceAutoID = civ.invoiceAutoID  
                LEFT JOIN (SELECT SUM(srp_erp_rvadvancematchdetails.transactionAmount) AS transactionAmount,InvoiceAutoID 
                FROM srp_erp_rvadvancematchdetails LEFT JOIN srp_erp_rvadvancematch ON srp_erp_rvadvancematch.matchID = srp_erp_rvadvancematchdetails.matchID 
                WHERE srp_erp_rvadvancematchdetails.companyID = " . current_companyID() . " AND confirmedYN = 1 GROUP BY InvoiceAutoID) rvd ON rvd.invoiceAutoID = civ.invoiceAutoID 
                WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND civ.contractAutoID IS NOT NULL GROUP BY civ.contractAutoID, civ.invoiceAutoID ) ab 
                GROUP BY ab.contractAutoID ) a ON a.contractAutoID = srp_erp_contractmaster.contractAutoID
                    
         	LEFT JOIN ( 
            SELECT 

                SUM( ab.doAmount ) AS doAmount,
                SUM( ab.nonTaxAmountDO ) AS nonTaxAmountDO,
                SUM( ab.receiptAmountDO ) AS receiptAmountDO,
                SUM( ab.creditNoteAmountDO ) AS creditNoteAmountDO,
                SUM( ab.receiptMatchAmountDO ) AS receiptMatchAmountDO,
                ab.contractAutoID ,
                ab.DOMasterID 


            FROM 
            (
            SELECT 
                SUM( Dorder.transactionAmount ) AS doAmount,
                SUM( Dorder.transactionAmount ) AS nonTaxAmountDO,
                SUM( crv.transactionAmount ) AS receiptAmountDO,
                SUM(
                IFNULL( crn.transactionAmount, 0 )) AS creditNoteAmountDO,
                SUM(
                IFNULL( rvd.transactionAmount, 0 )) AS receiptMatchAmountDO,
                dotax.taxPercentage,
                Dorder.contractAutoID,
                civ.DOMasterID 
                    FROM
                        srp_erp_deliveryorder
                        LEFT JOIN ( SELECT SUM( deliveredTransactionAmount ) AS transactionAmount, contractAutoID, DOAutoID FROM srp_erp_deliveryorderdetails WHERE companyID = $companyID GROUP BY contractAutoID, DOAutoID ) Dorder ON srp_erp_deliveryorder.doautoID = Dorder.doautoID
                        LEFT JOIN ( SELECT SUM( IFNULL( taxPercentage, 0 )) AS taxPercentage, DOAutoID FROM srp_erp_deliveryordertaxdetails WHERE companyID = $companyID GROUP BY DOAutoID ) dotax ON dotax.DOAutoID = Dorder.doautoID
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, DOMasterID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID = $companyID GROUP BY DOMasterID ) civ ON srp_erp_deliveryorder.DOAutoID = civ.DOMasterID
                        LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID
                        LEFT JOIN (
                        SELECT
                            SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS transactionAmount,
                            invoiceAutoID 
                        FROM
                            srp_erp_customerreceiptdetail
                            LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                        WHERE
                            srp_erp_customerreceiptdetail.companyID = $companyID 
                            AND approvedYN = 1 
                        GROUP BY
                            invoiceAutoID 
                        ) crv ON crv.invoiceAutoID = civ.invoiceAutoID
                        LEFT JOIN (
                        SELECT
                            SUM( srp_erp_creditnotedetail.transactionAmount ) AS transactionAmount,
                            invoiceAutoID 
                        FROM
                            srp_erp_creditnotedetail
                            LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID 
                        WHERE
                            srp_erp_creditnotedetail.companyID = $companyID 
                            AND approvedYN = 1 
                        GROUP BY
                            invoiceAutoID 
                        ) crn ON crn.invoiceAutoID = civ.invoiceAutoID
                        LEFT JOIN (
                        SELECT
                            SUM( srp_erp_rvadvancematchdetails.transactionAmount ) AS transactionAmount,
                            InvoiceAutoID 
                        FROM
                            srp_erp_rvadvancematchdetails
                            LEFT JOIN srp_erp_rvadvancematch ON srp_erp_rvadvancematch.matchID = srp_erp_rvadvancematchdetails.matchID 
                        WHERE
                            srp_erp_rvadvancematchdetails.companyID = $companyID 
                            AND confirmedYN = 1 
                        GROUP BY
                            InvoiceAutoID 
                        ) rvd ON rvd.invoiceAutoID = civ.invoiceAutoID 
                    WHERE
                        srp_erp_deliveryorder.approvedYN = 1 
                        AND Dorder.contractAutoID IS NOT NULL 
                    GROUP BY
                        Dorder.contractAutoID,
                        civ.domasterID 
                    ) ab 
                GROUP BY
                    ab.contractAutoID 
                ) b ON b.contractAutoID = srp_erp_contractmaster.contractAutoID
                        
            LEFT JOIN (SELECT
                IF(BALANCE = 0,'1',IF(BALANCE = requestedQty,'2','3')) as statusso,
                contractAutoID
                FROM
                    (
                    SELECT
                        srp_erp_contractdetails.contractAutoID,
                        srp_erp_contractdetails.contractDetailsAutoID,
                        srp_erp_contractdetails.requestedQty,
                        ifnull( cinqty, 0 ) AS cinvqty,ifnull(doqty,0) as doQty,
                        (
                            srp_erp_contractdetails.requestedQty -(
                                ifnull( cinqty, 0 ) + ifnull( doqty, 0 )
                            )) AS BALANCE
                    FROM
                        srp_erp_contractdetails
                        LEFT JOIN ( SELECT SUM( requestedQty ) AS cinqty, contractDetailsAutoID  FROM srp_erp_customerinvoicedetails WHERE companyID = " . current_companyID() . " 
                        GROUP BY contractDetailsAutoID ) cutomerinvoicedetail ON cutomerinvoicedetail.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID
                        LEFT JOIN ( SELECT SUM( deliveredQty ) AS doqty, contractDetailsAutoID FROM srp_erp_deliveryorderdetails WHERE companyID = " . current_companyID() . "
                    GROUP BY contractDetailsAutoID ) dodetail ON dodetail.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID
                    ) T1
                GROUP BY
                    contractAutoID) contautoid on contautoid.contractAutoID = srp_erp_contractmaster.contractAutoID 
                    LEFT JOIN (
                        SELECT ROUND(SUM( taxAmount ),2) AS taxAmount, contractDetailsAutoID,contractAutoID FROM srp_erp_contractdetails WHERE companyID = '$companyID'
                        GROUP BY contractAutoID 
                    ) taxdetail ON taxdetail.contractAutoID = srp_erp_contractmaster.contractAutoID
            
            LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_contractmaster.companyID = " . current_companyID() . " AND srp_erp_contractmaster.customerID IN (" . join(',', $customerID) . ") $segment $doctype AND approvedYN = 1  $date $salespersonex $search $status_filter/*AND (srp_erp_contractmaster.transactionAmount - IFNULL(a.invoiceAmount, 0)) > 0*/ GROUP BY srp_erp_contractmaster.contractAutoID ORDER BY transactionCurrency,contractDate";
       
            $output = $this->db->query($qry)->result_array();

            return $output;
    }

    function get_group_sales_order_report()
    {
        // $company = $this->get_group_company();
        $companies = getallsubGroupCompanies();
        $masterGroupID = getParentgroupMasterID();
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $search = $this->input->post('search');
        if ($search) {
            $search = " AND contractCode LIKE '%" . $search . "%'";
        } else {
            $search = "";
        }
        $qry = "SELECT DATE_FORMAT(contractDate,'" . $convertFormat . "') as documentDate, srp_erp_contractmaster.transactionAmount, a.invoiceAmount, a.nonTaxAmount, a.receiptAmount, transactionCurrency, transactionCurrencyDecimalPlaces, cust.groupCustomerName as customerName, contractCode, srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.documentID, IFNULL( segment.segmentCode, '-' ) AS segmentCodemaster
         FROM srp_erp_contractmaster 
         LEFT JOIN srp_erp_segment segment ON segment.segmentID = srp_erp_contractmaster.segmentID
         LEFT JOIN ( SELECT SUM(ab.invoiceAmount) AS invoiceAmount, SUM(ab.nonTaxAmount) AS nonTaxAmount, SUM(ab.receiptAmount) AS receiptAmount, ab.contractAutoID 
         FROM ( SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS invoiceAmount, SUM(civ.transactionAmount) AS nonTaxAmount, SUM(crv.transactionAmount) AS receiptAmount, tax.taxPercentage, contractAutoID, civ.invoiceAutoID FROM srp_erp_customerinvoicemaster 
         LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID IN (" . join(',', $companies) . ") GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID
         LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID IN (" . join(',', $companies) . ") GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID 
         LEFT JOIN ( SELECT SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS transactionAmount, invoiceAutoID FROM srp_erp_customerreceiptdetail 
         LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
         WHERE srp_erp_customerreceiptdetail.companyID IN (" . join(',', $companies) . ") AND approvedYN = 1 GROUP BY invoiceAutoID ) crv ON crv.invoiceAutoID = civ.invoiceAutoID 
         WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID IS NOT NULL GROUP BY contractAutoID, civ.invoiceAutoID ) ab GROUP BY ab.contractAutoID ) a ON a.contractAutoID = srp_erp_contractmaster.contractAutoID 
         INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . $masterGroupID . " AND groupCustomerAutoID IN (" . join(',', $customerID) . ")) cust ON cust.customerMasterID = customerID 
         WHERE srp_erp_contractmaster.companyID IN (" . join(',', $companies) . ") AND srp_erp_contractmaster.documentID = 'SO' AND approvedYN = 1 $search GROUP BY srp_erp_contractmaster.contractAutoID ORDER BY transactionCurrency,contractDate";

        $output = false;
        if ($companies) {
            $output = $this->db->query($qry)->result_array();
        }

        return $output;
    }

    function get_sales_order_drilldown_report()
    {
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $contractAutoID = $this->input->post('autoID');
        $search = $this->input->post('search');
        $qry = "";
        if ($this->input->post('type') == 1) { //get invoice amount
            $qry = "SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS transactionAmount, DATE_FORMAT(invoiceDate,'" . $convertFormat . "') as documentDate, civ.contractAutoID, srp_erp_customermaster.customerName, invoiceCode AS documentCode,
            transactionCurrency, transactionCurrencyDecimalPlaces,srp_erp_customerinvoicemaster.invoiceAutoID as autoID,documentID 
            FROM srp_erp_customerinvoicemaster 
            LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID 
            FROM srp_erp_customerinvoicedetails WHERE companyID = " . current_companyID() . " GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID 
            FROM srp_erp_customerinvoicetaxdetails WHERE companyID = " . current_companyID() . " GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID 
            LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_customerinvoicemaster.approvedYN = 1 
            AND civ.contractAutoID = $contractAutoID 
            AND srp_erp_customerinvoicemaster.customerID IN (" . join(',', $customerID) . ") 
            GROUP BY civ.contractAutoID, civ.invoiceAutoID
            UNION ALL 
                SELECT
                (
                    SUM( civ.transactionAmount ) 
                ) AS transactionAmount,
                DATE_FORMAT( DODate, '%d-%m-%Y' ) AS documentDate,
                civ.contractAutoID,
                srp_erp_customermaster.customerName,
                DOCode AS documentCode,
                transactionCurrency,
                transactionCurrencyDecimalPlaces,
                srp_erp_deliveryorder.DOAutoID AS autoID,
                documentID 
            FROM
                srp_erp_deliveryorder
                LEFT JOIN ( SELECT SUM( deliveredTransactionAmount ) AS transactionAmount, contractAutoID, DOAutoID FROM srp_erp_deliveryorderdetails WHERE companyID = " . current_companyID() . " GROUP BY contractAutoID, DOAutoID ) civ ON srp_erp_deliveryorder.DOAutoID = civ.DOAutoID
                
                LEFT JOIN ( SELECT SUM( IFNULL( taxPercentage, 0 )) AS taxPercentage, DOAutoID FROM srp_erp_deliveryordertaxdetails WHERE companyID = " . current_companyID() . " GROUP BY DOAutoID ) tax ON tax.DOAutoID = civ.DOAutoID
                LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID 
            WHERE
                srp_erp_deliveryorder.approvedYN = 1 
                AND civ.contractAutoID = $contractAutoID 
                AND srp_erp_deliveryorder.customerID IN  (" . join(',', $customerID) . ")
            GROUP BY
                civ.contractAutoID,
                civ.DOAutoID";
        } else { // get receipt amount
            $invoice = "SELECT srp_erp_customerinvoicedetails.invoiceAutoID FROM srp_erp_customerinvoicedetails LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE approvedYN = 1 AND srp_erp_customerinvoicemaster.customerID IN (" . join(',', $customerID) . ") AND srp_erp_customerinvoicedetails.companyID = " . current_companyID() . " AND srp_erp_customerinvoicedetails.contractAutoID = $contractAutoID GROUP BY srp_erp_customerinvoicedetails.invoiceAutoID";
            $output = $this->db->query($invoice)->result_array();
            $invoiceAutoID = array_column($output, 'invoiceAutoID');
            if ($invoiceAutoID) {
                //$qry = "SELECT DATE_FORMAT(RVdate,'" . $convertFormat . "') as documentDate,SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount,srp_erp_customermaster.customerName,RVcode as documentCode,transactionCurrency,transactionCurrencyDecimalPlaces,srp_erp_customerreceiptdetail.receiptVoucherAutoId as autoID,documentID FROM srp_erp_customerreceiptdetail LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_customerreceiptdetail.companyID = " . current_companyID() . " AND approvedYN = 1 AND srp_erp_customerreceiptmaster.customerID IN (" . join(',', $customerID) . ") AND srp_erp_customerreceiptdetail.invoiceAutoID IN (" . join(',', $invoiceAutoID) . ") GROUP BY srp_erp_customerreceiptdetail.receiptVoucherAutoId";
                $qry = "SELECT * FROM( SELECT DATE_FORMAT(RVdate,'" . $convertFormat . "') as documentDate,SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount,srp_erp_customermaster.customerName,RVcode as documentCode,transactionCurrency,transactionCurrencyDecimalPlaces,srp_erp_customerreceiptdetail.receiptVoucherAutoId as autoID,documentID FROM srp_erp_customerreceiptdetail LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_customerreceiptdetail.companyID = " . current_companyID() . " AND approvedYN = 1 AND srp_erp_customerreceiptmaster.customerID IN (" . join(',', $customerID) . ") AND srp_erp_customerreceiptdetail.invoiceAutoID IN (" . join(',', $invoiceAutoID) . ") GROUP BY srp_erp_customerreceiptdetail.receiptVoucherAutoId  UNION ALL  SELECT DATE_FORMAT(creditNoteDate,'" . $convertFormat . "') as documentDate,SUM(srp_erp_creditnotedetail.transactionAmount) as transactionAmount,srp_erp_customermaster.customerName,creditNoteCode as documentCode,transactionCurrency,transactionCurrencyDecimalPlaces,srp_erp_creditnotedetail.creditNoteMasterAutoID as autoID,documentID FROM srp_erp_creditnotedetail LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_creditnotemaster.customerID WHERE srp_erp_creditnotedetail.companyID = " . current_companyID() . " AND approvedYN = 1 AND srp_erp_creditnotemaster.customerID IN (" . join(',', $customerID) . ") AND srp_erp_creditnotedetail.invoiceAutoID IN (" . join(',', $invoiceAutoID) . ") GROUP BY srp_erp_creditnotedetail.creditNoteMasterAutoID       UNION ALL  SELECT DATE_FORMAT(matchDate,'" . $convertFormat . "') as documentDate,SUM(srp_erp_rvadvancematchdetails.transactionAmount) as transactionAmount,srp_erp_customermaster.customerName,matchSystemCode as documentCode,srp_erp_rvadvancematch.transactionCurrency,srp_erp_rvadvancematch.transactionCurrencyDecimalPlaces,srp_erp_rvadvancematchdetails.matchID as autoID,documentID FROM srp_erp_rvadvancematchdetails LEFT JOIN srp_erp_rvadvancematch ON srp_erp_rvadvancematch.matchID = srp_erp_rvadvancematchdetails.matchID LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_rvadvancematch.customerID WHERE srp_erp_rvadvancematchdetails.companyID = " . current_companyID() . " AND confirmedYN = 1 AND srp_erp_rvadvancematch.customerID IN (" . join(',', $customerID) . ") AND srp_erp_rvadvancematchdetails.InvoiceAutoID IN (" . join(',', $invoiceAutoID) . ") GROUP BY srp_erp_rvadvancematch.matchID ) as aa";
            } else {
                return array();
            }
        }
        $output = $this->db->query($qry)->result_array();

        return $output;
    }

    function get_group_sales_order_drilldown_report()
    {
        $company = $this->get_group_company();
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $contractAutoID = $this->input->post('autoID');
        $search = $this->input->post('search');
        $qry = "";
        if ($this->input->post('type') == 1) { //get invoice amount
            $qry = "SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS transactionAmount, DATE_FORMAT(invoiceDate,'" . $convertFormat . "') as documentDate, contractAutoID, cust.groupCustomerName as customerName, invoiceCode AS documentCode, transactionCurrency, transactionCurrencyDecimalPlaces,srp_erp_customerinvoicemaster.invoiceAutoID as autoID,documentID FROM srp_erp_customerinvoicemaster 
            LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID IN (" . join(',', $company) . ") GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID 
            LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID IN (" . join(',', $company) . ") GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID 
            INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID IN (" . join(',', $customerID) . ")) cust ON cust.customerMasterID = customerID 
            WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID = $contractAutoID GROUP BY contractAutoID, civ.invoiceAutoID";
        } else { // get receipt amount
            $invoice = "SELECT srp_erp_customerinvoicedetails.invoiceAutoID FROM srp_erp_customerinvoicedetails 
LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID IN (" . join(',', $customerID) . ")) cust ON cust.customerMasterID = customerID 
WHERE approvedYN = 1 AND srp_erp_customerinvoicedetails.companyID IN (" . join(',', $company) . ") AND srp_erp_customerinvoicedetails.contractAutoID = $contractAutoID GROUP BY srp_erp_customerinvoicedetails.invoiceAutoID";
            $output = $this->db->query($invoice)->result_array();
            $invoiceAutoID = array_column($output, 'invoiceAutoID');
            if ($invoiceAutoID) {
                $qry = "SELECT DATE_FORMAT(RVdate,'" . $convertFormat . "') as documentDate,SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount,cust.groupCustomerName as customerName,RVcode as documentCode,transactionCurrency,transactionCurrencyDecimalPlaces,srp_erp_customerreceiptdetail.receiptVoucherAutoId as autoID,documentID FROM srp_erp_customerreceiptdetail 
                LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID IN (" . join(',', $customerID) . ")) cust ON cust.customerMasterID = customerID 
                WHERE srp_erp_customerreceiptdetail.companyID IN (" . join(',', $company) . ") AND approvedYN = 1 AND srp_erp_customerreceiptdetail.invoiceAutoID IN (" . join(',', $invoiceAutoID) . ") GROUP BY srp_erp_customerreceiptdetail.receiptVoucherAutoId";
            } else {
                return array();
            }
        }
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'SC');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();
    }

    function get_customer_invoice_report()
    {
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $segmentID = $this->input->post('segmentID');
        $search = $this->input->post('search');
        $currency = $this->input->post('currency');

        $companyid = current_companyID();
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        $date1 = "";
        $date2 = "";
        $date3 = "";
        $search1 = "";
        $search2 = "";
        $taxAmount = "";

        if ($currency == 1) {
            $sumCurrency = 'SUM(total_value / transactionExchangeRate) AS total_value';
            $taxAmount = '(taxAmount / transactionExchangeRate) AS taxAmount';
        } else if ($currency == 2) {
            $sumCurrency = 'SUM(total_value / companyLocalExchangeRate) AS total_value';
            $taxAmount = '(taxAmount / companyLocalExchangeRate) AS taxAmount';
        } else {
            $sumCurrency = 'SUM(total_value / companyReportingExchangeRate) AS total_value';
            $taxAmount = '(taxAmount / companyReportingExchangeRate) AS taxAmount';
        }

        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentdate >= '" . $datefromconvert . " 00:00:00' AND documentdate <= '" . $datetoconvert . " 00:00:00')";
            // $date1 .= " AND ( DODate >= '" . $datefromconvert . " 00:00:00' AND DODate <= '" . $datetoconvert . " 00:00:00' ) ";
            // $date2 .= " AND ( RVdate >= '" . $datefromconvert . " 00:00:00' AND RVdate <= '" . $datetoconvert . " 00:00:00' ) ";
            // $date3 .= " AND ( returnDate >= '" . $datefromconvert . " 00:00:00' AND returnDate <= '" . $datetoconvert . " 00:00:00' ) ";
        }

        if ($search) {
            $search = " AND (documentSystemCode LIKE '%" . $search . "%')";
            // $search1 = " AND (domaster.DOCode LIKE '%" . $search . "%' OR `srp_erp_customermaster`.`customerName` LIKE '%" . $search . "%')";
            // $search2 = " AND (RVcode LIKE '%" . $search . "%' OR `cusmaster`.`customerName` LIKE '%" . $search . "%')";
        } else {
            $search = "";
            $search1 = "";
            $search2 = "";
        }
        //$qry = "SELECT DATE_FORMAT(contractDate,'" . $convertFormat . "') as documentDate, srp_erp_contractmaster.transactionAmount, a.invoiceAmount, a.nonTaxAmount, a.receiptAmount, transactionCurrency, transactionCurrencyDecimalPlaces, srp_erp_customermaster.customerName, contractCode, srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.documentID FROM srp_erp_contractmaster LEFT JOIN ( SELECT SUM(ab.invoiceAmount) AS invoiceAmount, SUM(ab.nonTaxAmount) AS nonTaxAmount, SUM(ab.receiptAmount) AS receiptAmount, ab.contractAutoID FROM ( SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS invoiceAmount, SUM(civ.transactionAmount) AS nonTaxAmount, SUM(crv.transactionAmount) AS receiptAmount, tax.taxPercentage, contractAutoID, civ.invoiceAutoID FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID = ".current_companyID()." GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID = ".current_companyID()." GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS transactionAmount, invoiceAutoID FROM srp_erp_customerreceiptdetail LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId WHERE srp_erp_customerreceiptdetail.companyID = ".current_companyID()." AND approvedYN = 1 GROUP BY invoiceAutoID ) crv ON crv.invoiceAutoID = civ.invoiceAutoID WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID IS NOT NULL GROUP BY contractAutoID, civ.invoiceAutoID ) ab GROUP BY ab.contractAutoID ) a ON a.contractAutoID = srp_erp_contractmaster.contractAutoID LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_contractmaster.companyID = ".current_companyID()." AND srp_erp_contractmaster.customerID IN (".join(',',$customerID).") AND srp_erp_contractmaster.documentID = 'SO' AND approvedYN = 1 $search GROUP BY srp_erp_contractmaster.contractAutoID ORDER BY transactionCurrency";
        $qry = "SELECT
                    IFNULL(receipttransactionAmount, 0) as receipttransactionAmount,
                    IFNULL(rvmatchtransactionAmount, 0) as rvmatchtransactionAmount,
                    IFNULL(credittransactionAmount, 0) as credittransactionAmount,
                    IF(CustomerID = - 1, 'From POS Customers', IF ( CustomerID = - 2, 'From Direct Receipt voucher', Customer.CustomerName )) AS customermastername,
                    revensummary.*,
                    rvmatch.*,
                    creditnote.*,
                    receiptdet.*	
	            FROM (SELECT customerAutoID, customerName FROM srp_erp_customermaster WHERE companyID = {$companyid} UNION SELECT 0 AS customerAutoID, ' Sundry' AS customerName FROM srp_erp_customermaster WHERE companyID = {$companyid} ) 		Customer
                LEFT JOIN (
                    SELECT
                        $sumCurrency,
                        $taxAmount,
                        transactionCurrencyDecimalPlaces,
                        transactionExchangeRate,
                        companyLocalCurrencyDecimalPlaces,
                        companyLocalExchangeRate,
                        companyReportingCurrencyDecimalPlaces,
                        companyReportingExchangeRate,
                        transactionCurrency,
                        companyLocalCurrency,
                        companyReportingCurrency,
                        documentDate,
                        documentMasterAutoID,
                        documentCode,
                        documentSystemCode,
                        0 as returnAmount,
                        IF(customerID = 0 && documentCode = 'POS',- 1, IF(customerID = 0 && documentCode = 'RV',- 2, customerID )) AS customerID,
                        segmentID,
                        companyID,
                        segmentCode
                    FROM
                        (
                            SELECT
                                transactionCurrencyDecimalPlaces,
                                transactionExchangeRate,
                                companyLocalCurrencyDecimalPlaces,
                                companyLocalExchangeRate,
                                companyReportingCurrencyDecimalPlaces,
                                companyReportingExchangeRate,
                                transactionCurrency,
                                companyLocalCurrency,
                                companyReportingCurrency,
                                0 as returnAmount,
                                IF(partyAutoID = 0 && documentCode = 'POS',- 1, IF(partyAutoID = 0 && documentCode = 'RV',- 2, partyAutoID )) AS customerID,
                                IFNULL( transactionAmount, 0 )*- 1 AS total_value,
                                partyAutoID,
                                srp_erp_generalledger.segmentID,
                                srp_erp_segment.segmentCode,
                                srp_erp_generalledger.companyID,
                                documentCode,
                                documentDate,
                                documentMasterAutoID,
                                documentSystemCode,
                                (IFNULL(cinvTaxAmount, 0) + IFNULL(doTaxAmount, 0) + IFNULL(posTaxAmount, 0) + IFNULL(slrTaxAmount, 0) + IFNULL(rvTaxAmount, 0) + IFNULL(cnTaxAmount, 0)) as taxAmount
                            FROM
                                srp_erp_generalledger 
                            LEFT JOIN srp_erp_segment ON srp_erp_generalledger.segmentID = srp_erp_segment.segmentID
                            LEFT JOIN (SELECT creditNoteMasterAutoID, SUM( IFNULL( taxAmount, 0 ) ) AS cnTaxAmount FROM srp_erp_creditnotedetail WHERE companyID = {$companyid} AND GLType = 'PLI' GROUP BY creditNoteMasterAutoID)cnTax ON cnTax.creditNoteMasterAutoID = srp_erp_generalledger.documentMasterAutoID AND srp_erp_generalledger.documentCode = 'CN'
                            LEFT JOIN (SELECT receiptVoucherAutoId, SUM( IFNULL( taxAmount, 0 ) ) AS rvTaxAmount FROM srp_erp_customerreceiptdetail WHERE companyID = {$companyid} AND ( type = 'Item' OR ( type = 'GL' AND GLType = 'PLI' ) ) GROUP BY receiptVoucherAutoId)rvTax ON rvTax.receiptVoucherAutoId = srp_erp_generalledger.documentMasterAutoID AND srp_erp_generalledger.documentCode = 'RV'
                            LEFT JOIN (SELECT invoiceAutoID, SUM(IFNULL(taxAmount, 0)) as cinvTaxAmount FROM srp_erp_customerinvoicedetails WHERE companyID = {$companyid} AND revenueGLType = 'PLI' GROUP BY invoiceAutoID)cinvTax ON cinvTax.invoiceAutoID = srp_erp_generalledger.documentMasterAutoID AND srp_erp_generalledger.documentCode = 'CINV'
                            LEFT JOIN (SELECT DOAutoID, SUM(IFNULL(taxAmount, 0)) as doTaxAmount FROM srp_erp_deliveryorderdetails WHERE companyID = {$companyid} GROUP BY DOAutoID) doTax ON doTax.DOAutoID = srp_erp_generalledger.documentMasterAutoID AND srp_erp_generalledger.documentCode = 'DO'
                            LEFT JOIN ( SELECT invoiceID, SUM(IFNULL(taxAmount, 0)) as posTaxAmount FROM srp_erp_pos_invoicedetail WHERE companyID = {$companyid} AND revenueGLAutoID IS NOT NULL GROUP BY invoiceID)posTax ON posTax.invoiceID = srp_erp_generalledger.documentMasterAutoID AND srp_erp_generalledger.documentCode = 'POS'
                            LEFT JOIN (SELECT salesReturnAutoID, SUM(IFNULL(taxAmount, 0)) as slrTaxAmount FROM srp_erp_salesreturndetails WHERE companyID = {$companyid} GROUP BY salesReturnAutoID) slrTax ON slrTax.salesReturnAutoID = srp_erp_generalledger.documentMasterAutoID AND srp_erp_generalledger.documentCode = 'SLR'
                            WHERE
                                srp_erp_generalledger.companyID = {$companyid} 
                                AND GLType = 'PLI' 
                                AND documentCode IN ( 'CINV', 'DO', 'POS', 'RV', 'SLR', 'CN' ) 
                                {$date} {$search}
                        ) t1 
                        GROUP BY
                            documentMasterAutoID,
                            documentCode
                ) revensummary ON revensummary.customerID = IF(Customer.customerAutoID = 0 && documentCode = 'POS',- 1,IF(Customer.customerAutoID = 0 && documentCode = 'RV',- 2, Customer.customerAutoID )) 
		        LEFT JOIN (
                    SELECT
                        SUM( srp_erp_rvadvancematchdetails.transactionAmount ) AS rvmatchtransactionAmount,
                        SUM( srp_erp_rvadvancematchdetails.companyLocalAmount ) AS rvmatchcompanyLocalAmount,
                        SUM( srp_erp_rvadvancematchdetails.companyReportingAmount ) AS rvmatchcompanyReportingAmount,
                        invoiceAutoID 
                    FROM
                        srp_erp_rvadvancematchdetails
                        LEFT JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematch`.`matchID` = `srp_erp_rvadvancematchdetails`.`matchID` 
                    WHERE
                        srp_erp_rvadvancematch.confirmedYN = 1 
                    GROUP BY
                        invoiceAutoID 
                ) rvmatch ON ( `rvmatch`.`invoiceAutoID` = revensummary.documentMasterAutoID AND revensummary.documentCode = 'CINV' )
                LEFT JOIN (
                    SELECT
                        SUM( srp_erp_creditnotedetail.transactionAmount ) AS credittransactionAmount,
                        SUM( srp_erp_creditnotedetail.companyLocalAmount ) AS creditcompanyLocalAmount,
                        SUM( srp_erp_creditnotedetail.companyReportingAmount ) AS creditcompanyReportingAmount,
                        invoiceAutoID 
                    FROM
                        srp_erp_creditnotedetail
                        LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID 
                    WHERE
                        srp_erp_creditnotemaster.approvedYN = 1 
                        AND srp_erp_creditnotedetail.companyID = {$companyid} 
                    GROUP BY
                        invoiceAutoID 
                ) creditnote ON ( `creditnote`.`invoiceAutoID` = revensummary.documentMasterAutoID AND revensummary.documentCode = 'CINV' )
                LEFT JOIN (
                    SELECT
                        SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS receipttransactionAmount,
                        SUM( srp_erp_customerreceiptdetail.companyLocalAmount ) AS receiptcompanyLocalAmount,
                        SUM( srp_erp_customerreceiptdetail.companyReportingAmount ) AS receiptcompanyReportingAmount,
                        invoiceAutoID
                    FROM
                        srp_erp_customerreceiptdetail
                        LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                    WHERE
                        srp_erp_customerreceiptmaster.approvedYN = 1 
                        AND type = 'Invoice' 
                        AND srp_erp_customerreceiptdetail.companyID = {$companyid} 
                    GROUP BY
                        invoiceAutoID 
                ) receiptdet ON ( `receiptdet`.`invoiceAutoID` = revensummary.documentMasterAutoID AND revensummary.documentCode = 'CINV' )
		        WHERE
                    companyID = {$companyid} 
                    AND customerID IN (" . join(',', $customerID) . ") 
                    AND segmentID IN (" . join(',', $segmentID) . ") 
                group by 
                    documentMasterAutoID,documentCode
                ORDER BY 
                    documentDate ";

        $output = $this->db->query($qry)->result_array();
        return $output;
    }


    function get_group_customer_invoice_report()
    {
        $company = $this->get_group_company();

        $companies = getallsubGroupCompanies();
        $masterGroupID = getParentgroupMasterID();

        $convertFormat = convert_date_format_sql();
        $segmentID = $this->input->post('segmentID');
        $customerID = $this->input->post('customerID');
        $search = $this->input->post('search');

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        $date1 = "";
        $date2 = "";
        $date3 = "";
        $search1 = "";
        $search2 = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
            $date1 .= " AND ( DODate >= '" . $datefromconvert . " 00:00:00' AND DODate <= '" . $datetoconvert . " 00:00:00' ) ";
            $date2 .= " AND ( RVdate >= '" . $datefromconvert . " 00:00:00' AND RVdate <= '" . $datetoconvert . " 00:00:00' ) ";
            $date3 .= " AND ( returnDate >= '" . $datefromconvert . " 00:00:00' AND returnDate <= '" . $datetoconvert . " 00:00:00' ) ";
        }

        if ($search) {
            //$search = " AND invoiceCode LIKE '%" . $search . "%'";
            $search = " AND (invoiceCode LIKE '%" . $search . "%' OR `srp_erp_customermaster`.`customerName` LIKE '%" . $search . "%')";
            $search1 = " AND (domaster.DOCode LIKE '%" . $search . "%' OR `srp_erp_customermaster`.`customerName` LIKE '%" . $search . "%')";
            $search2 = " AND (RVcode LIKE '%" . $search . "%' OR `cusmaster`.`customerName` LIKE '%" . $search . "%')";
        } else {
            $search = "";
            $search1 = "";
            $search2 = "";
        }
        /*     $qry = "SELECT
         `srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
         `srp_erp_customerinvoicemaster`.`documentID` AS `documentID`,
         `invoiceCode`,
         `invoiceNarration`,
         `cust`.`groupCustomerName` AS `customermastername`,
         `transactionCurrencyDecimalPlaces`,
         `transactionCurrency`,
         `transactionExchangeRate`,
         `companyLocalCurrency`,
         `companyLocalCurrencyDecimalPlaces`,
         companyLocalExchangeRate,
         `companyReportingCurrency`,
         `companyReportingExchangeRate`,
         `companyReportingCurrencyDecimalPlaces`,
         `confirmedYN`,
         `approvedYN`,
         seg.segmentCode as segid,
         `srp_erp_customerinvoicemaster`.`createdUserID` AS `createdUser`,
         DATE_FORMAT(invoiceDate,'" . $convertFormat . "') AS invoiceDate,
         DATE_FORMAT(invoiceDueDate,'" . $convertFormat . "') AS invoiceDueDate,
         `invoiceType`,
         (
             (
                 (
                     IFNULL(addondet.taxPercentage, 0) / 100
                 ) * (
                     (
                         IFNULL(det.transactionAmount, 0) - (
                             IFNULL(det.detailtaxamount, 0)
                         )
                     )
                 )
             ) + IFNULL(det.transactionAmount, 0)
         ) AS total_value,
         IFNULL(detreturn.totalValue, 0) as returnAmount,
         IFNULL(creditnote.credittransactionAmount, 0) as credittransactionAmount,
         IFNULL(creditnote.creditcompanyLocalAmount, 0) as creditcompanyLocalAmount,
         IFNULL(creditnote.creditcompanyReportingAmount, 0) as creditcompanyReportingAmount,
         IFNULL( rvmatch.rvmatchtransactionAmount, 0 ) AS rvmatchtransactionAmount,
         IFNULL( rvmatch.rvmatchcompanyLocalAmount, 0 ) AS rvmatchcompanyLocalAmount,
         IFNULL( rvmatch.rvmatchcompanyReportingAmount, 0 ) AS rvmatchcompanyReportingAmount,
         IFNULL(receiptdet.receipttransactionAmount, 0) as receipttransactionAmount,
         IFNULL(receiptdet.receiptcompanyLocalAmount, 0) as receiptcompanyLocalAmount,
         IFNULL(receiptdet.receiptcompanyReportingAmount, 0) as receiptcompanyReportingAmount
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
             SUM(totalValue) AS totalValue,
             invoiceAutoID
         FROM
             srp_erp_salesreturndetails
             LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
         WHERE
             srp_erp_salesreturnmaster.approvedYN=1
         GROUP BY
             invoiceAutoID
     ) detreturn ON (
         `detreturn`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
     )

     LEFT JOIN (
         SELECT
             SUM( srp_erp_rvadvancematchdetails.transactionAmount ) AS rvmatchtransactionAmount,
             SUM( srp_erp_rvadvancematchdetails.companyLocalAmount ) AS rvmatchcompanyLocalAmount,
             SUM( srp_erp_rvadvancematchdetails.companyReportingAmount ) AS rvmatchcompanyReportingAmount,
             invoiceAutoID
         FROM
             srp_erp_rvadvancematchdetails
             LEFT JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematch`.`matchID` = `srp_erp_rvadvancematchdetails`.`matchID`
         WHERE
             srp_erp_rvadvancematch.confirmedYN = 1
         GROUP BY
             invoiceAutoID
         ) rvmatch ON ( `rvmatch`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID )

     LEFT JOIN (
         SELECT
             SUM(srp_erp_creditnotedetail.transactionAmount) AS credittransactionAmount,
             SUM(srp_erp_creditnotedetail.companyLocalAmount) AS creditcompanyLocalAmount,
             SUM(srp_erp_creditnotedetail.companyReportingAmount) AS creditcompanyReportingAmount,
             invoiceAutoID
         FROM
             srp_erp_creditnotedetail
             LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
         WHERE
             srp_erp_creditnotemaster.approvedYN=1
             AND srp_erp_creditnotedetail.companyID IN (" . join(',', $companies) . ")
         GROUP BY
             invoiceAutoID
     ) creditnote ON (
         `creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
     )

     LEFT JOIN (
         SELECT
             SUM(srp_erp_customerreceiptdetail.transactionAmount) AS receipttransactionAmount,
             SUM(srp_erp_customerreceiptdetail.companyLocalAmount) AS receiptcompanyLocalAmount,
             SUM(srp_erp_customerreceiptdetail.companyReportingAmount) AS receiptcompanyReportingAmount,
             invoiceAutoID
         FROM
             srp_erp_customerreceiptdetail
             LEFT JOIN srp_erp_customerreceiptmaster  ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
         WHERE
         srp_erp_customerreceiptmaster.approvedYN = 1
         AND	type = 'Invoice'
             AND srp_erp_customerreceiptdetail.companyID IN (" . join(',', $companies) . ")
         GROUP BY
             invoiceAutoID
     ) receiptdet ON (
         `receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
     )
     INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . $masterGroupID . " AND groupCustomerAutoID IN (" . join(',', $customerID) . ")) cust ON cust.customerMasterID = `srp_erp_customerinvoicemaster`.`customerID`
     INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(" . join(',', $segment) . ")) seg ON srp_erp_customerinvoicemaster.segmentID = seg.segmentID
     WHERE
         srp_erp_customerinvoicemaster.companyID IN (" . join(',', $companies) . ") AND approvedYN=1 $date $search  ORDER BY srp_erp_customerinvoicemaster.invoiceDate ASC";*/

        $qry = "Select * from (SELECT
    `srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
    `srp_erp_customerinvoicemaster`.`documentID` AS `documentID`,
    `invoiceCode`,
    `invoiceNarration`,
    srp_erp_groupcustomermaster.groupCustomerName AS `customermastername`,
    `transactionCurrencyDecimalPlaces`,
    `transactionCurrency`,
    `transactionExchangeRate`,
    `companyLocalCurrency`,
    `companyLocalCurrencyDecimalPlaces`,
    companyLocalExchangeRate,
    `companyReportingCurrency`,
    `companyReportingExchangeRate`,
    `companyReportingCurrencyDecimalPlaces`,
   
    `confirmedYN`,
    `approvedYN`,
    srp_erp_customerinvoicemaster.segmentCode as segid,
    `srp_erp_customerinvoicemaster`.`createdUserID` AS `createdUser`,
    DATE_FORMAT(invoiceDate,'" . $convertFormat . "') AS invoiceDate,
    DATE_FORMAT(invoiceDueDate,'" . $convertFormat . "') AS invoiceDueDate,
    `invoiceType`,
    ((
        IFNULL(addondet.taxPercentage, 0) / 100
    ) * (
        IFNULL((det.transactionAmount-retensionTransactionAmount), 0) - IFNULL(det.detailtaxamount, 0) - (
            (
                IFNULL(
                    gendiscount.discountPercentage,
                    0
                ) / 100
            ) * IFNULL((det.transactionAmount-retensionTransactionAmount), 0)
        ) + IFNULL(
            genexchargistax.transactionAmount,
            0
        )
    ) + IFNULL((det.transactionAmount-retensionTransactionAmount), 0) - (
        (
            IFNULL(
                gendiscount.discountPercentage,
                0
            ) / 100
        ) * IFNULL((det.transactionAmount-retensionTransactionAmount), 0)
    ) + IFNULL(
        genexcharg.transactionAmount,
        0
    ))-IFNULL(det.detailtaxamount, 0) AS total_value,
    0 as returnAmount,
    IFNULL(creditnote.credittransactionAmount, 0) as credittransactionAmount,
    IFNULL( rvmatch.rvmatchtransactionAmount, 0) AS rvmatchtransactionAmount,
    IFNULL( rvmatch.rvmatchcompanyLocalAmount, 0) AS rvmatchcompanyLocalAmount,
    IFNULL( rvmatch.rvmatchcompanyReportingAmount, 0) AS rvmatchcompanyReportingAmount,
    IFNULL(creditnote.creditcompanyLocalAmount, 0) as creditcompanyLocalAmount,
    IFNULL(creditnote.creditcompanyReportingAmount, 0) as creditcompanyReportingAmount,
    IFNULL(receiptdet.receipttransactionAmount, 0) as receipttransactionAmount,
    IFNULL(receiptdet.receiptcompanyLocalAmount, 0) as receiptcompanyLocalAmount,
    IFNULL(receiptdet.receiptcompanyReportingAmount, 0) as receiptcompanyReportingAmount
FROM
    `srp_erp_customerinvoicemaster`
LEFT JOIN (
    SELECT
        SUM(transactionAmount) AS transactionAmount,
        sum(totalafterTax) AS detailtaxamount,
        invoiceAutoID
    FROM
        srp_erp_customerinvoicedetails
    Where
       revenueGLType = 'PLI'      
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

    LEFT JOIN (
    SELECT
        SUM(srp_erp_rvadvancematchdetails.transactionAmount) AS rvmatchtransactionAmount,
    SUM(srp_erp_rvadvancematchdetails.companyLocalAmount) AS rvmatchcompanyLocalAmount,
    SUM(srp_erp_rvadvancematchdetails.companyReportingAmount) AS rvmatchcompanyReportingAmount,
    invoiceAutoID 
FROM
    srp_erp_rvadvancematchdetails
    LEFT JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematch`.`matchID` = `srp_erp_rvadvancematchdetails`.`matchID` 
WHERE
    srp_erp_rvadvancematch.confirmedYN = 1 
GROUP BY
    invoiceAutoID
    ) rvmatch ON ( `rvmatch`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID )
LEFT JOIN (
    SELECT
        SUM(srp_erp_creditnotedetail.transactionAmount) AS credittransactionAmount,
        SUM(srp_erp_creditnotedetail.companyLocalAmount) AS creditcompanyLocalAmount,
        SUM(srp_erp_creditnotedetail.companyReportingAmount) AS creditcompanyReportingAmount,
        invoiceAutoID
    FROM
        srp_erp_creditnotedetail
        LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
    WHERE
        srp_erp_creditnotemaster.approvedYN=1
        AND srp_erp_creditnotedetail.companyID IN (" . join(',', $companies) . ")
    GROUP BY
        invoiceAutoID
) creditnote ON (
    `creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)

LEFT JOIN (
    SELECT
        SUM(srp_erp_customerreceiptdetail.transactionAmount) AS receipttransactionAmount,
        SUM(srp_erp_customerreceiptdetail.companyLocalAmount) AS receiptcompanyLocalAmount,
        SUM(srp_erp_customerreceiptdetail.companyReportingAmount) AS receiptcompanyReportingAmount,
        invoiceAutoID,
        srp_erp_customerreceiptdetail.segmentID as segment
    FROM
        srp_erp_customerreceiptdetail
        LEFT JOIN srp_erp_customerreceiptmaster  ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
    WHERE
    srp_erp_customerreceiptmaster.approvedYN = 1
    
    AND type = 'Invoice'
        AND srp_erp_customerreceiptdetail.companyID IN (" . join(',', $companies) . ")
    GROUP BY
        invoiceAutoID
) receiptdet ON (
    `receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
    LEFT JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomerdetails.customerMasterID = `srp_erp_customerinvoicemaster`.`customerID`
    LEFT JOIN srp_erp_groupcustomermaster ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    LEFT JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegmentdetails.segmentID = srp_erp_customerinvoicemaster.segmentID
    LEFT JOIN srp_erp_groupsegment ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID
WHERE
    srp_erp_customerinvoicemaster.companyID IN (" . join(',', $companies) . ") AND approvedYN=1   $date $search 
    AND srp_erp_groupcustomermaster.groupCustomerAutoID IN (" . join(',', $customerID) . ") AND srp_erp_groupsegment.segmentID IN (" . join(',', $segmentID) . ")  
     HAVING
total_value > 0
    UNION ALL 
        SELECT
    domaster.DOAutoID AS `invoiceAutoID`,
    `domaster`.`documentID` AS `documentID`,
    domaster.DOCode AS invoiceCode,
    domaster.narration AS invoiceNarration,
    `srp_erp_groupcustomermaster`.`groupCustomerName` AS `customermastername`,
        
 `transactionCurrencyDecimalPlaces` ,
     `transactionCurrency`  ,
     `transactionExchangeRate` , 
     `companyLocalCurrency` , 
     `companyLocalCurrencyDecimalPlaces` ,
     companyLocalExchangeRate ,
     `companyReportingCurrency` ,
     `companyReportingExchangeRate` ,
     `companyReportingCurrencyDecimalPlaces` ,
    
    `confirmedYN`,
    `approvedYN`,
    domaster.segmentCode AS segid,
    `domaster`.`createdUserID` AS `createdUser`,
    DATE_FORMAT( DODate, '%d-%m-%Y' ) AS invoiceDate,
        DATE_FORMAT( domaster.invoiceDueDate, '%d-%m-%Y' ) AS invoiceDueDate,
                DOType as invoiceType,
        det.transactionAmount as total_value,
0  AS returnAmount,
0 as credittransactionAmount,
0 as rvmatchtransactionAmount,
0 as rvmatchcompanyLocalAmount,
0 as  rvmatchcompanyReportingAmount,
0 as  creditcompanyLocalAmount,
0 as  creditcompanyReportingAmount,
0 as receipttransactionAmount,
0 as receiptcompanyLocalAmount,
0 as receiptcompanyReportingAmount

FROM
    srp_erp_deliveryorder domaster
    LEFT JOIN (
    SELECT
        SUM( srp_erp_deliveryorderdetails.deliveredTransactionAmount) AS transactionAmount,
        sum( totalafterTax ) AS detailtaxamount,
        srp_erp_deliveryorderdetails.DOAutoID 
    FROM
        srp_erp_deliveryorderdetails
        LEFT JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
        WHERE 
        revenueGLType = 'PLI'
    GROUP BY
        srp_erp_deliveryorderdetails.DOAutoID 
    ) det ON ( `det`.`DOAutoID` = domaster.doautoID )
    LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, DOAutoID FROM srp_erp_deliveryordertaxdetails GROUP BY DOAutoID ) addondet ON ( `addondet`.`DOAutoID` = domaster.DOAutoID )
    LEFT JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomerdetails.customerMasterID = `domaster`.`customerID`
    LEFT JOIN srp_erp_groupcustomermaster ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    LEFT JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegmentdetails.segmentID = domaster.segmentID
    LEFT JOIN srp_erp_groupsegment ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID
WHERE
    domaster.companyID IN (" . join(',', $companies) . ") 
    AND domaster.approvedYN = 1 
    $date1 $search1
    AND srp_erp_groupcustomermaster.groupCustomerAutoID  IN (" . join(',', $customerID) . ")
    AND srp_erp_groupsegment.segmentID IN (" . join(',', $segmentID) . ") 
     HAVING
total_value > 0
    UNION ALL 
    SELECT
    srp_erp_pos_invoice.invoiceID AS `invoiceAutoID`,
    documentCode AS `documentID`,
    documentSystemCode AS invoiceCode,
    ' ' AS invoiceNarration,
    IFNULL( cusmaster.customerName, 'Sundry' ) AS customermastername,
    transactionCurrencyDecimalPlaces,
    transactionCurrency,
    transactionExchangeRate,
    `companyLocalCurrency`,
    `companyLocalCurrencyDecimalPlaces`,
    companyLocalExchangeRate,
    `companyReportingCurrency`,
    `companyReportingExchangeRate`,
    `companyReportingCurrencyDecimalPlaces`,
    1 AS confirmedYN,
    1 AS approvedYN,
    segmentCode AS segid,
    `srp_erp_pos_invoice`.`createdUserID` AS `createdUser`,
    DATE_FORMAT( invoiceDate, '%d-%m-%Y' ) AS invoiceDate,
    ' ' AS invoiceDueDate,
    ' ' AS invoiceType,
    transactionamount AS total_value,
    0 AS returnAmount,
    0 AS credittransactionAmount,
    0 AS rvmatchtransactionAmount,
    0 AS rvmatchcompanyLocalAmount,
    0 AS rvmatchcompanyReportingAmount,
    0 AS creditcompanyLocalAmount,
    0 AS creditcompanyReportingAmount,
    0 AS receipttransactionAmount,
    0 AS receiptcompanyLocalAmount,
    0 AS receiptcompanyReportingAmount 
FROM
    srp_erp_pos_invoice
    LEFT JOIN ( 
    SELECT IFNULL( SUM( transactionAmount ), 0 ) AS transactionamount,srp_erp_pos_invoice.invoiceID,IFNULL(groupCustomerAutoID,0) as customerID
    FROM srp_erp_pos_invoicedetail
    LEFT JOIN srp_erp_pos_invoice on srp_erp_pos_invoicedetail.invoiceID = srp_erp_pos_invoice.invoiceID
    LEFT JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomerdetails.customerMasterID = srp_erp_pos_invoice.customerID
    LEFT JOIN srp_erp_groupcustomermaster ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    LEFT JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegmentdetails.segmentID = srp_erp_pos_invoice.segmentID
    LEFT JOIN srp_erp_groupsegment ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID
    WHERE
    srp_erp_pos_invoicedetail.companyID IN (" . join(',', $companies) . ")
    AND revenueGLType = 'PLI'
    GROUP BY srp_erp_pos_invoice.invoiceID ) posinvoicedet ON posinvoicedet.invoiceID = srp_erp_pos_invoice.invoiceID
          LEFT JOIN (SELECT
						*
					FROM
						(
							SELECT 
    groupCustomerMasterID as  customerAutoID,    
    groupCustomerName as customerName 
    FROM
    srp_erp_groupcustomerdetails
    LEFT JOIN srp_erp_groupcustomermaster on srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    WHERE
    srp_erp_groupcustomerdetails.companyID IN (" . join(',', $companies) . ")
    UNION
SELECT
    0 AS customerAutoID,
    'Sundry' AS customerName
    FROM
    srp_erp_groupcustomerdetails
    LEFT JOIN srp_erp_groupcustomermaster on srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    WHERE
     srp_erp_groupcustomerdetails.companyID IN (" . join(',', $companies) . ")
						) t1
					GROUP BY
						CustomerAutoID)cusmaster ON cusmaster.customerAutoID = posinvoicedet.customerID
     
WHERE
    srp_erp_pos_invoice.companyID IN (" . join(',', $companies) . ")
    AND srp_erp_pos_invoice.isVoid != 1 
    $date $search

    AND posinvoicedet.customerID IN (" . join(',', $customerID) . ")
    AND segmentID IN(" . join(',', $segmentID) . ")
 HAVING
total_value > 0
     
     UNION ALL 
    
    SELECT
    srp_erp_customerreceiptmaster.receiptVoucherAutoId AS `invoiceAutoID`,
    documentID AS `documentID`,
    RVcode AS invoiceCode,
    RVNarration AS invoiceNarration,
    IFNULL( cusmaster.customerName, 'Sundry' ) AS customermastername,
    transactionCurrencyDecimalPlaces,
    transactionCurrency,
    transactionExchangeRate,
    `companyLocalCurrency`,
    `companyLocalCurrencyDecimalPlaces`,
    companyLocalExchangeRate,
    `companyReportingCurrency`,
    `companyReportingExchangeRate`,
    `companyReportingCurrencyDecimalPlaces`,
    confirmedYN,
    approvedYN,
    srp_erp_groupsegment.segmentCode AS segid,
    `srp_erp_customerreceiptmaster`.`createdUserID` AS `createdUser`,
    DATE_FORMAT( RVdate, '%d-%m-%Y' ) AS invoiceDate,
    ' ' AS invoiceDueDate,
    RVType AS invoiceType,
    IFNULL( cusdetail.transactionamount, 0 ) AS total_value,
    0 AS returnAmount,
    0 AS credittransactionAmount,
    0 AS rvmatchtransactionAmount,
    0 AS rvmatchcompanyLocalAmount,
    0 AS rvmatchcompanyReportingAmount,
    0 AS creditcompanyLocalAmount,
    0 AS creditcompanyReportingAmount,
    0 AS receipttransactionAmount,
    0 AS receiptcompanyLocalAmount,
    0 AS receiptcompanyReportingAmount 
FROM
    srp_erp_customerreceiptmaster
    LEFT JOIN ( SELECT SUM(( transactionamount )) AS transactionamount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE companyID IN (" . join(',', $companies) . ") AND GLType = 'PLI' AND type IN ( 'GL', 'Item' ) GROUP BY receiptVoucherAutoId ) cusdetail ON cusdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
    	LEFT JOIN (
					SELECT
						*
					FROM
						(
							SELECT 
    groupCustomerMasterID as  customerAutoID,    
    groupCustomerName as customerName 
    FROM
    srp_erp_groupcustomerdetails
    LEFT JOIN srp_erp_groupcustomermaster on srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    WHERE
    srp_erp_groupcustomerdetails. companyID IN (" . join(',', $companies) . ")
    UNION
SELECT
    0 AS customerAutoID,
    'Sundry' AS customerName
    FROM
    srp_erp_groupcustomerdetails
    LEFT JOIN srp_erp_groupcustomermaster on srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    WHERE
     srp_erp_groupcustomerdetails. companyID IN (" . join(',', $companies) . ")
						) t1
					GROUP BY
						CustomerAutoID
				)  cusmaster ON cusmaster.customerAutoID = srp_erp_customerreceiptmaster.customerID
    LEFT JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegmentdetails.segmentID = srp_erp_customerreceiptmaster.segmentID
    LEFT JOIN srp_erp_groupsegment ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID
WHERE
    srp_erp_customerreceiptmaster.companyID IN (" . join(',', $companies) . ") $date2 $search2
    AND approvedYN = 1 
    AND cusmaster.customerAutoID IN (" . join(',', $customerID) . ")
    AND srp_erp_groupsegment.segmentID IN(" . join(',', $segmentID) . ")
     HAVING
total_value > 0
    UNION ALL 
    SELECT
    `srp_erp_salesreturndetails`.`salesReturnAutoID` AS `invoiceAutoID`,
    `srp_erp_salesreturnmaster`.`documentID` AS `documentID`,
    salesReturnCode AS `invoiceCode`,
    `comment` AS `invoiceNarration`,
    	(cusmaster.customerName) AS customermastername,
    transactionCurrencyDecimalPlaces,
    transactionCurrency,
    transactionExchangeRate,
    `companyLocalCurrency`,
    `companyLocalCurrencyDecimalPlaces`,
    companyLocalExchangeRate,
    `companyReportingCurrency`,
    `companyReportingExchangeRate`,
    `companyReportingCurrencyDecimalPlaces`,
    confirmedYN,
    approvedYN,
    srp_erp_salesreturndetails.segmentCode AS segid,
    `srp_erp_salesreturnmaster`.`createdUserID` AS `createdUser`,
    DATE_FORMAT( returnDate, '%d-%m-%Y' ) AS invoiceDate,
    ' ' AS invoiceDueDate,
    ' ' AS invoiceType,
    0 as    total_value,
    SUM( IFNULL(totalValue,0) )*-1 AS returnAmount ,
        0 AS credittransactionAmount,
        0 AS rvmatchtransactionAmount,
        0 AS rvmatchcompanyLocalAmount,
        0 AS rvmatchcompanyReportingAmount,
        0 AS creditcompanyLocalAmount,
        0 AS creditcompanyReportingAmount,
        0 AS receipttransactionAmount,
        0 AS receiptcompanyLocalAmount,
        0 AS receiptcompanyReportingAmount 
FROM
    srp_erp_salesreturndetails
    LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
    LEFT JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomerdetails.customerMasterID = srp_erp_salesreturnmaster.customerID
    LEFT JOIN srp_erp_groupcustomermaster ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    LEFT JOIN (SELECT * FROM(SELECT 
    groupCustomerMasterID as  customerAutoID,    
    groupCustomerName as customerName 
    FROM
    srp_erp_groupcustomerdetails
    LEFT JOIN srp_erp_groupcustomermaster on srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    WHERE
    srp_erp_groupcustomerdetails.companyID IN (" . join(',', $companies) . ")
    UNION
SELECT
    0 AS customerAutoID,
    'Sundry' AS customerName
    FROM
    srp_erp_groupcustomerdetails
    LEFT JOIN srp_erp_groupcustomermaster on srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    WHERE
     srp_erp_groupcustomerdetails. companyID IN (" . join(',', $companies) . ") 
					GROUP BY
						CustomerAutoID) t1
				)  cusmaster ON cusmaster.customerAutoID = srp_erp_groupcustomermaster.groupCustomerAutoID
    LEFT JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegmentdetails.segmentID = srp_erp_salesreturndetails.segmentID
    LEFT JOIN srp_erp_groupsegment ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID
WHERE
    srp_erp_salesreturnmaster.approvedYN = 1 
    AND srp_erp_salesreturndetails.revenueGLType = 'PLI' 
    $date3
    AND srp_erp_salesreturnmaster.companyID IN (" . join(',', $companies) . ")
    AND cusmaster.customerAutoID  IN (" . join(',', $customerID) . ")
    AND srp_erp_groupsegment.segmentID IN (" . join(',', $segmentID) . ")
GROUP BY
    srp_erp_salesreturnmaster.salesReturnAutoID) t1";

        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_sales_order_return_drilldown_report()
    {
        $invoiceID = $this->input->post('invoiceAutoID');
        $qry = "SELECT
	sd.salesReturnDetailsID,
	slsm.salesReturnAutoID,
	sd.totalValue,
	slsm.transactionExchangeRate,
	slsm.companyLocalExchangeRate,
	slsm.companyReportingExchangeRate,
	slsm.transactionCurrencyDecimalPlaces,
	slsm.companyLocalCurrencyDecimalPlaces,
	slsm.companyReportingCurrencyDecimalPlaces,
	slsm.transactionCurrency,
	slsm.companyLocalCurrency,
	slsm.companyReportingCurrency,
	slsm.salesReturnCode,
	slsm.returnDate
FROM
	srp_erp_salesreturndetails sd
LEFT JOIN srp_erp_salesreturnmaster slsm ON `slsm`.`salesReturnAutoID` = `sd`.`salesReturnAutoID`
WHERE
	slsm.approvedYN = 1
AND sd.invoiceAutoID = $invoiceID
AND slsm.companyID = " . current_companyID() . " ";
        $output = $this->db->query($qry)->result_array();
        return $output;

    }

    function get_group_sales_order_return_drilldown_report()
    {
        $company = $this->get_group_company();
        $invoiceID = $this->input->post('invoiceAutoID');
        $qry = "SELECT
	sd.salesReturnDetailsID,
	slsm.salesReturnAutoID,
	sd.totalValue,
	slsm.transactionExchangeRate,
	slsm.companyLocalExchangeRate,
	slsm.companyReportingExchangeRate,
	slsm.transactionCurrencyDecimalPlaces,
	slsm.companyLocalCurrencyDecimalPlaces,
	slsm.companyReportingCurrencyDecimalPlaces,
	slsm.transactionCurrency,
	slsm.companyLocalCurrency,
	slsm.companyReportingCurrency,
	slsm.salesReturnCode,
	slsm.returnDate
FROM
	srp_erp_salesreturndetails sd
LEFT JOIN srp_erp_salesreturnmaster slsm ON `slsm`.`salesReturnAutoID` = `sd`.`salesReturnAutoID`
WHERE
	slsm.approvedYN = 1
AND sd.invoiceAutoID = $invoiceID
AND slsm.companyID IN (" . join(',', $company) . ")";
        $output = $this->db->query($qry)->result_array();
        return $output;

    }

    function get_sales_order_credit_drilldown_report()
    {
        $invoiceID = $this->input->post('invoiceAutoID');
        $qry = "SELECT
	rm.receiptVoucherAutoId as masterID,
	rm.RVcode as documentCode,
	rm.documentID as docID,
	rm.RVdate as documentDate,
	rd.transactionAmount as transactionAmount,
	rd.companyLocalAmount as companyLocalAmount,
	rd.companyReportingAmount as companyReportingAmount,
	rm.transactionCurrency as transactionCurrency,
	rm.companyLocalCurrency as companyLocalCurrency,
	rm.companyReportingCurrency as companyReportingCurrency,
	rm.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
	rm.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
	rm.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces

FROM
	srp_erp_customerreceiptdetail rd
LEFT JOIN srp_erp_customerreceiptmaster rm ON rm.receiptVoucherAutoId = rd.receiptVoucherAutoId
WHERE
	rm.approvedYN = 1
AND rd.invoiceAutoID = $invoiceID
AND rd.type = 'Invoice'
AND rm.companyID = " . current_companyID() . "

UNION

SELECT
	srp_erp_rvadvancematch.matchID AS masterID,
	srp_erp_rvadvancematch.matchSystemCode AS documentCode,
	srp_erp_rvadvancematch.documentID AS docID,
	srp_erp_rvadvancematch.matchDate AS documentDate,
	srp_erp_rvadvancematchdetails.transactionAmount AS transactionAmount,
	srp_erp_rvadvancematchdetails.companyLocalAmount AS companyLocalAmount,
	srp_erp_rvadvancematchdetails.companyReportingAmount AS companyReportingAmount,
	srp_erp_rvadvancematch.transactionCurrency AS transactionCurrency,
	srp_erp_rvadvancematch.companyLocalCurrency AS companyLocalCurrency,
	srp_erp_rvadvancematch.companyReportingCurrency AS companyReportingCurrency,
	srp_erp_rvadvancematch.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
	srp_erp_rvadvancematch.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces,
	srp_erp_rvadvancematch.companyReportingCurrencyDecimalPlaces AS companyReportingCurrencyDecimalPlaces 

FROM
	srp_erp_rvadvancematchdetails 
LEFT JOIN srp_erp_rvadvancematch ON srp_erp_rvadvancematchdetails.matchID = srp_erp_rvadvancematch.matchID
WHERE
	srp_erp_rvadvancematch.confirmedYN = 1
AND srp_erp_rvadvancematchdetails.invoiceAutoID = $invoiceID
AND srp_erp_rvadvancematch.companyID = " . current_companyID() . "

UNION

SELECT
	cm.creditNoteMasterAutoID as masterID,
	cm.creditNoteCode as documentCode,
	cm.documentID as docID,
	cm.creditNoteDate as documentDate,
	cd.transactionAmount as transactionAmount,
	cd.companyLocalAmount as companyLocalAmount,
	cd.companyReportingAmount as companyReportingAmount,
	cm.transactionCurrency as transactionCurrency,
	cm.companyLocalCurrency as companyLocalCurrency,
	cm.companyReportingCurrency as companyReportingCurrency,
	cm.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
	cm.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
	cm.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces

FROM
	srp_erp_creditnotedetail cd
LEFT JOIN srp_erp_creditnotemaster cm ON cm.creditNoteMasterAutoID = cd.creditNoteMasterAutoID
WHERE
	cm.approvedYN = 1
AND cd.invoiceAutoID = $invoiceID
AND cm.companyID = " . current_companyID() . " ";
        $output = $this->db->query($qry)->result_array();
        return $output;

    }


    function get_group_sales_order_credit_drilldown_report()
    {
        $company = $this->get_group_company();
        $invoiceID = $this->input->post('invoiceAutoID');
        $qry = "SELECT
	rm.receiptVoucherAutoId as masterID,
	rm.RVcode as documentCode,
	rm.documentID as docID,
	rm.RVdate as documentDate,
	rd.transactionAmount as transactionAmount,
	rd.companyLocalAmount as companyLocalAmount,
	rd.companyReportingAmount as companyReportingAmount,
	rm.transactionCurrency as transactionCurrency,
	rm.companyLocalCurrency as companyLocalCurrency,
	rm.companyReportingCurrency as companyReportingCurrency,
	rm.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
	rm.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
	rm.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces

FROM
	srp_erp_customerreceiptdetail rd
LEFT JOIN srp_erp_customerreceiptmaster rm ON rm.receiptVoucherAutoId = rd.receiptVoucherAutoId
WHERE
	rm.approvedYN = 1
AND rd.invoiceAutoID = $invoiceID
AND rd.type = 'Invoice'
AND rm.companyID IN (" . join(',', $company) . ")

UNION

SELECT
	cm.creditNoteMasterAutoID as masterID,
	cm.creditNoteCode as documentCode,
	cm.documentID as docID,
	cm.creditNoteDate as documentDate,
	cd.transactionAmount as transactionAmount,
	cd.companyLocalAmount as companyLocalAmount,
	cd.companyReportingAmount as companyReportingAmount,
	cm.transactionCurrency as transactionCurrency,
	cm.companyLocalCurrency as companyLocalCurrency,
	cm.companyReportingCurrency as companyReportingCurrency,
	cm.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
	cm.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
	cm.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces

FROM
	srp_erp_creditnotedetail cd
LEFT JOIN srp_erp_creditnotemaster cm ON cm.creditNoteMasterAutoID = cd.creditNoteMasterAutoID
WHERE
	cm.approvedYN = 1
AND cd.invoiceAutoID = $invoiceID
AND cm.companyID IN (" . join(',', $company) . ")";
        $output = $this->db->query($qry)->result_array();
        return $output;

    }


    function get_get_revenue_summery_report($datearr)
    {
        $customerID = $this->input->post('customerID');
        $search = $this->input->post('search');
        $currency = $this->input->post('currency');
        $segment = $this->input->post('segmentID');
        $sumamount = '';
        $sumamountDo = '';
        $currencytype = '';
        $sumamountDo_s = '';
        $date_join = '';
        $date_arr = [];
        $companyID = current_companyID();
        if ($currency == 2) {
            foreach ($datearr as $key => $val) {
                /*  $monthexploe = explode('-',$key);
                  array_push($date_arr,$key);
                  $sumamountDo_s .= "SUM(doamount$monthexploe[1]) as doamount$monthexploe[1],";
                  $sumamountDo .= "	IF( DATE_FORMAT( DODate, '%Y-%m' ) = '$key', ( ( (srp_erp_deliveryorderdetails.deliveredTransactionAmount/companyLocalExchangeRate )) ), 0 ) AS doamount$monthexploe[1], ";
                  $sumamount .= "IF( invoiceDate = '$key',SUM(total_value / companyLocalExchangeRate),0) + IFNULL(doamount.doamount$monthexploe[1],0) AS '$val',";*/
                $sumamount .= " SUM(IF(documentDate='$key',total_value/companyLocalExchangeRate,0)) as '$val' ,";
                $currencytype .= 'companyLocalExchangeRate';
            }
        } else {
            foreach ($datearr as $key => $val) {
                /*$monthexploe = explode('-',$key);
                array_push($date_arr,$key);
                $sumamountDo_s .= "SUM(doamount$monthexploe[1]) as doamount$monthexploe[1],";
                $sumamountDo .= "IF( DATE_FORMAT( DODate, '%Y-%m' ) = '$key', ( ( (srp_erp_deliveryorderdetails.deliveredTransactionAmount/companyReportingExchangeRate ))), 0 ) AS doamount$monthexploe[1], ";
                $sumamount .= "	IF( invoiceDate = '$key',SUM(total_value / companyReportingExchangeRate),0) + IFNULL(doamount.doamount$monthexploe[1],0) AS '$val',";*/
                $sumamount .= " SUM(IF(documentDate='$key',total_value/companyReportingExchangeRate,0)) as '$val' ,";
                $currencytype = 'companyReportingExchangeRate';
            }
        }
        $date_join = "('" . join("' , '", $date_arr) . "')";

        if ($search) {
            $search = " AND documentSystemCode LIKE '%" . $search . "%'";
        } else {
            $search = "";
        }
        if ($segment) {
            //$segme = " AND segmentID = $segment";
            $segme = " AND srp_erp_customerinvoicemaster.segmentID IN (" . join(',', $segment) . ")";
        } else {
            $segm = "";
        }

        /*Old Query*/

        /*$segme='';
        if($segment){
            //$segme = " AND segmentID = $segment";
            $segme = " AND srp_erp_customerinvoicemaster.segmentID IN (".join(',',$segment).")";
        }else{
            $segm = "";
        }*/
        /*$qry = "SELECT
    $sumamount
	customermastername,
	transactionCurrencyDecimalPlaces,
	transactionExchangeRate,
	companyLocalCurrencyDecimalPlaces,
	companyLocalExchangeRate,
	companyReportingCurrencyDecimalPlaces,
	companyReportingExchangeRate,
	customerID
FROM
	(
		SELECT
			customerID,
			`srp_erp_customermaster`.`customerName` AS `customermastername`,
			`transactionCurrencyDecimalPlaces`,
			`transactionCurrency`,
			`transactionExchangeRate`,
			`companyLocalCurrency`,
			`companyLocalCurrencyDecimalPlaces`,
			companyLocalExchangeRate,
			`companyReportingCurrency`,
			`companyReportingExchangeRate`,
			`companyReportingCurrencyDecimalPlaces`,
			DATE_FORMAT(invoiceDate, '%Y-%m') AS invoiceDate,
			(
		IFNULL(addondet.taxPercentage, 0) / 100
	) * (
		IFNULL((det.transactionAmount-retensionTransactionAmount), 0) - IFNULL(det.detailtaxamount, 0) - (
			(
				IFNULL(
					gendiscount.discountPercentage,
					0
				) / 100
			) * IFNULL((det.transactionAmount-retensionTransactionAmount), 0)
		) + IFNULL(
			genexchargistax.transactionAmount,
			0
		)
	) + IFNULL((det.transactionAmount-retensionTransactionAmount), 0) - (
		(
			IFNULL(
				gendiscount.discountPercentage,
				0
			) / 100
		) * IFNULL((det.transactionAmount-retensionTransactionAmount), 0)
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
		LEFT JOIN (
			SELECT
				SUM(totalValue) AS totalValue,
				invoiceAutoID
			FROM
				srp_erp_salesreturndetails
			LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
			WHERE
				srp_erp_salesreturnmaster.approvedYN = 1
			GROUP BY
				invoiceAutoID
		) detreturn ON (
			`detreturn`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
		)
		LEFT JOIN (
		SELECT
                SUM(srp_erp_rvadvancematchdetails.transactionAmount) AS rvmatchtransactionAmount,
                SUM(srp_erp_rvadvancematchdetails.companyLocalAmount) AS rvmatchcompanyLocalAmount,
                SUM(srp_erp_rvadvancematchdetails.companyReportingAmount) AS rvmatchcompanyReportingAmount,
                invoiceAutoID 
            FROM
                srp_erp_rvadvancematchdetails
                LEFT JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematch`.`matchID` = `srp_erp_rvadvancematchdetails`.`matchID` 
            WHERE
                srp_erp_rvadvancematch.confirmedYN = 1 
            GROUP BY
                invoiceAutoID
		) rvmatch ON ( `rvmatch`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID )
		LEFT JOIN (
			SELECT
				SUM(
					srp_erp_creditnotedetail.transactionAmount
				) AS credittransactionAmount,
				SUM(
					srp_erp_creditnotedetail.companyLocalAmount
				) AS creditcompanyLocalAmount,
				SUM(
					srp_erp_creditnotedetail.companyReportingAmount
				) AS creditcompanyReportingAmount,
				invoiceAutoID
			FROM
				srp_erp_creditnotedetail
			LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
			WHERE
				srp_erp_creditnotemaster.approvedYN = 1
			AND srp_erp_creditnotedetail.companyID = " . current_companyID() . "
			GROUP BY
				invoiceAutoID
		) creditnote ON (
			`creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
		)
		LEFT JOIN (
			SELECT
				SUM(
					srp_erp_customerreceiptdetail.transactionAmount
				) AS receipttransactionAmount,
				SUM(
					srp_erp_customerreceiptdetail.companyLocalAmount
				) AS receiptcompanyLocalAmount,
				SUM(
					srp_erp_customerreceiptdetail.companyReportingAmount
				) AS receiptcompanyReportingAmount,
				invoiceAutoID
			FROM
				srp_erp_customerreceiptdetail
			LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
			WHERE
				srp_erp_customerreceiptmaster.approvedYN = 1
			AND type = 'Invoice'
			AND srp_erp_customerreceiptdetail.companyID = " . current_companyID() . "
			GROUP BY
				invoiceAutoID
		) receiptdet ON (
			`receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
		)
		LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_customerinvoicemaster`.`customerID`
		WHERE
			srp_erp_customerinvoicemaster.companyID = " . current_companyID() . "
		AND approvedYN = 1
		AND customerID IN (" . join(',', $customerID) . ")
		AND srp_erp_customerinvoicemaster.segmentID IN (" . join(',', $segment) . ")
	) a
	
	LEFT JOIN (SELECT

$sumamountDo_s
customerID as customerIDDo
FROM
srp_erp_deliveryorder domaster

	LEFT JOIN (	SELECT
customerID as customerIDtest, 
	$sumamountDo
	domaster.DOAutoID ,
	DODate
FROM
	srp_erp_deliveryorderdetails 
	LEFT JOIN srp_erp_deliveryorder domaster on  srp_erp_deliveryorderdetails.DOAutoID = domaster.DOAutoID
		) dodetail on domaster.DOAutoID = dodetail.DOAutoID
GROUP BY
	domaster.customerID
	) doamount ON doamount.customerIDDo = a.customerID 
GROUP BY
	customerID"; */

        $qry = "SELECT
IF( CustomerID = -1 ,'From POS Customers',IF( CustomerID = -2 ,'From Direct Receipt voucher', Customer.CustomerName))  AS customermastername,
revensummary.*
FROM 
(SELECT customerAutoID, customerName FROM srp_erp_customermaster WHERE companyID = $companyID UNION SELECT 0 AS customerAutoID, ' Sundry' AS customerName FROM srp_erp_customermaster WHERE companyID = $companyID ) Customer

LEFT JOIN (SELECT
    $sumamount
    transactionCurrencyDecimalPlaces,
    transactionExchangeRate,
    companyLocalCurrencyDecimalPlaces,
    companyLocalExchangeRate,
    companyReportingCurrencyDecimalPlaces,
    companyReportingExchangeRate,
    IF( customerID = 0 && documentCode='POS',-1,IF( customerID = 0 && documentCode='RV',-2,customerID)) AS customerID,
    segmentID,
    companyID,
    documentCode	
FROM
    (
    SELECT
        transactionCurrencyDecimalPlaces,
        transactionExchangeRate,
        companyLocalCurrencyDecimalPlaces,
        companyLocalExchangeRate,
        companyReportingCurrencyDecimalPlaces,
        companyReportingExchangeRate,
        DATE_FORMAT( documentDate, '%Y-%m' ) AS documentDate,
        IF( partyAutoID = 0 && documentCode='POS',-1,IF( partyAutoID = 0 && documentCode='RV',-2,partyAutoID)) AS customerID,
        SUM(
        IFNULL( transactionAmount, 0 ))*- 1 AS total_value ,
        partyAutoID,
        segmentID,companyID,
       documentCode
    FROM
    srp_erp_generalledger
    WHERE
        companyID = $companyID 
        AND GLType = 'PLI' 
        AND documentCode IN ( 'CINV', 'DO', 'POS', 'RV', 'SLR', 'CN' ) 
    GROUP BY
        documentMasterAutoID,
        documentCode 
    ) t1 
GROUP BY
    IF(partyAutoID = 0 && documentCode='POS',-1,IF( partyAutoID = 0 && documentCode='RV',-2,partyAutoID)) 
    ) revensummary on revensummary.customerID = IF( Customer.customerAutoID = 0 && documentCode='POS',-1,IF( Customer.customerAutoID = 0 && documentCode='RV',-2,
    Customer.customerAutoID)) 
    Where 
      companyID = $companyID  
      AND customerID IN  (" . join(',', $customerID) . ") 
      AND segmentID IN (" . join(',', $segment) . ") 
    GROUP BY 
    customerID
      ";


        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_group_revenue_summery_report($datearr)
    {
        $company = $this->get_group_company();
        $companies = getallsubGroupCompanies();
        $masterGroupID = getParentgroupMasterID();
        $customerID = $this->input->post('customerID');
        $segment = $this->input->post('segmentID');
        $search = $this->input->post('search');
        $currency = $this->input->post('currency');
        $sumamount = '';
        if ($currency == 2) {
            foreach ($datearr as $key => $val) {
                // $sumamount .= " SUM(IF(invoiceDate='$key',total_value/companyLocalExchangeRate,0)) as '$val' ,";
                $sumamount .= " SUM(IF(documentDate='$key',total_value/companyLocalExchangeRate,0)) as '$val' ,";
                $currencytype .= 'companyLocalExchangeRate';
            }
        } else {
            foreach ($datearr as $key => $val) {
                //  $sumamount .= " SUM(IF(invoiceDate='$key',total_value/companyReportingExchangeRate,0)) as '$val' ,";
                $sumamount .= " SUM(IF(documentDate='$key',total_value/companyReportingExchangeRate,0)) as '$val' ,";
                $currencytype = 'companyReportingExchangeRate';
            }
        }

        if ($search) {
            $search = " AND invoiceCode LIKE '%" . $search . "%'";
        } else {
            $search = "";
        }
        /*     $qry = "SELECT
         $sumamount
         customermastername,
         transactionCurrencyDecimalPlaces,
         transactionExchangeRate,
         companyLocalCurrencyDecimalPlaces,
         companyLocalExchangeRate,
         companyReportingCurrencyDecimalPlaces,
         companyReportingExchangeRate,
         segid,
         customerID
     FROM
         (
             SELECT
                  cust.groupCustomerAutoID as customerID,
                 `cust`.`groupCustomerName` AS `customermastername`,
                 `transactionCurrencyDecimalPlaces`,
                 `transactionCurrency`,
                 `transactionExchangeRate`,
                 `companyLocalCurrency`,
                 `companyLocalCurrencyDecimalPlaces`,
                 companyLocalExchangeRate,
                 `companyReportingCurrency`,
                 `companyReportingExchangeRate`,
                 `companyReportingCurrencyDecimalPlaces`,
                 seg.segmentCode as segid,
                 DATE_FORMAT(invoiceDate, '%Y-%m') AS invoiceDate,
                 (
                     (
                         (
                             IFNULL(addondet.taxPercentage, 0) / 100
                         ) * (
                             (
                                 IFNULL(det.transactionAmount, 0) - (
                                     IFNULL(det.detailtaxamount, 0)
                                 )
                             )
                         )
                     ) + IFNULL(det.transactionAmount, 0) - IFNULL(detreturn.totalValue, 0)
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
                     SUM(totalValue) AS totalValue,
                     invoiceAutoID
                 FROM
                     srp_erp_salesreturndetails
                 LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
                 WHERE
                     srp_erp_salesreturnmaster.approvedYN = 1
                 GROUP BY
                     invoiceAutoID
             ) detreturn ON (
                 `detreturn`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
             )
             LEFT JOIN (
                 SELECT
                     SUM(
                         srp_erp_creditnotedetail.transactionAmount
                     ) AS credittransactionAmount,
                     SUM(
                         srp_erp_creditnotedetail.companyLocalAmount
                     ) AS creditcompanyLocalAmount,
                     SUM(
                         srp_erp_creditnotedetail.companyReportingAmount
                     ) AS creditcompanyReportingAmount,
                     invoiceAutoID
                 FROM
                     srp_erp_creditnotedetail
                 LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
                 WHERE
                     srp_erp_creditnotemaster.approvedYN = 1
                 AND srp_erp_creditnotedetail.companyID IN (" . join(',', $company) . ")
                 GROUP BY
                     invoiceAutoID
             ) creditnote ON (
                 `creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
             )
             LEFT JOIN (
                 SELECT
                     SUM(
                         srp_erp_customerreceiptdetail.transactionAmount
                     ) AS receipttransactionAmount,
                     SUM(
                         srp_erp_customerreceiptdetail.companyLocalAmount
                     ) AS receiptcompanyLocalAmount,
                     SUM(
                         srp_erp_customerreceiptdetail.companyReportingAmount
                     ) AS receiptcompanyReportingAmount,
                     invoiceAutoID
                 FROM
                     srp_erp_customerreceiptdetail
                 LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
                 WHERE
                     srp_erp_customerreceiptmaster.approvedYN = 1
                 AND type = 'Invoice'
                 AND srp_erp_customerreceiptdetail.companyID IN (" . join(',', $company) . ")
                 GROUP BY
                     invoiceAutoID
             ) receiptdet ON (
                 `receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
             )
             INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID IN (" . join(',', $customerID) . ")) cust ON cust.customerMasterID = `srp_erp_customerinvoicemaster`.`customerID`
             INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(" . join(',', $segment) . ")) seg ON srp_erp_customerinvoicemaster.segmentID = seg.segmentID
             WHERE
                 srp_erp_customerinvoicemaster.companyID IN (" . join(',', $company) . ")
             AND approvedYN = 1
         ) a
     GROUP BY
         customerID";*/

        /*$qry = "SELECT
	customermastername,
	$sumamount
	transactionCurrencyDecimalPlaces,
	transactionExchangeRate,
	companyLocalCurrencyDecimalPlaces,
	companyLocalExchangeRate,
	companyReportingCurrencyDecimalPlaces,
	companyReportingExchangeRate,
	customerID 
FROM
	(
	SELECT
		transactionCurrencyDecimalPlaces,
		transactionExchangeRate,
		companyLocalCurrencyDecimalPlaces,
		companyLocalExchangeRate,
		companyReportingCurrencyDecimalPlaces,
		companyReportingExchangeRate,
		DATE_FORMAT( documentDate, '%Y-%m' ) AS documentDate,
		partyAutoID AS customerID,
		`cusmaster`.`customerName` AS `customermastername`,
		SUM(
		IFNULL( transactionAmount, 0 ))*- 1 AS total_value 
	FROM
		`srp_erp_generalledger`
		LEFT JOIN ( SELECT customerAutoID, customerName FROM srp_erp_customermaster WHERE companyID = $companyID UNION  SELECT 0 AS customerAutoID, ' Other' AS customerName FROM srp_erp_customermaster WHERE companyID = $companyID ) cusmaster ON cusmaster.customerAutoID = srp_erp_generalledger.partyAutoID 
	WHERE
		companyID = $companyID 
		AND GLType = 'PLI' 
		AND documentCode IN ( 'CINV', 'DO', 'POS', 'RV', 'SR', 'CN' ) 
		AND partyType = 'CUS' 
		AND partyAutoID IN 	 (" . join(',', $customerID) . ") 
	    AND segmentID IN (" . join(',', $segment) . ")
	GROUP BY
		documentMasterAutoID,
		documentCode 
	) t1 
GROUP BY
	customerID";*/

        $qry = "SELECT 
Customer.CustomerName as customermastername,
revensummary.*
FROM 
(SELECT 
    groupCustomerMasterID as  customerAutoID,    
    groupCustomerName as customerName 
    FROM
    srp_erp_groupcustomerdetails
    LEFT JOIN srp_erp_groupcustomermaster on srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    WHERE
    srp_erp_groupcustomerdetails. companyID IN (" . join(',', $companies) . ")
    UNION
SELECT
    0 AS customerAutoID,
    'Sundry' AS customerName
    FROM
    srp_erp_groupcustomerdetails
    LEFT JOIN srp_erp_groupcustomermaster on srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    WHERE
     srp_erp_groupcustomerdetails. companyID IN (" . join(',', $companies) . ")) Customer

LEFT JOIN (SELECT
	$sumamount
	transactionCurrencyDecimalPlaces,
	transactionExchangeRate,
	companyLocalCurrencyDecimalPlaces,
	companyLocalExchangeRate,
	companyReportingCurrencyDecimalPlaces,
	companyReportingExchangeRate,
	IFNULL(customerID,0) as  customerID,
	segmentID,
	companyID
FROM
	(
	SELECT
		transactionCurrencyDecimalPlaces,
		transactionExchangeRate,
		companyLocalCurrencyDecimalPlaces,
		companyLocalExchangeRate,
		companyReportingCurrencyDecimalPlaces,
		companyReportingExchangeRate,
		DATE_FORMAT( documentDate, '%Y-%m' ) AS documentDate,
		srp_erp_groupcustomermaster.groupCustomerAutoID AS customerID,
		SUM(
		IFNULL( transactionAmount, 0 ))*- 1 AS total_value ,
       srp_erp_groupcustomermaster.groupCustomerAutoID AS partyAutoID,
        srp_erp_groupsegmentdetails.segmentID,
        srp_erp_generalledger.companyID
	FROM
	srp_erp_generalledger
	LEFT JOIN srp_erp_groupcustomerdetails on srp_erp_groupcustomerdetails.customerMasterID  = srp_erp_generalledger.partyAutoID
    LEFT JOIN srp_erp_groupcustomermaster on srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
    LEFT JOIN srp_erp_groupsegmentdetails on srp_erp_groupsegmentdetails.segmentID = srp_erp_generalledger.segmentID
    LEFT JOIN srp_erp_groupsegment on srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID
	WHERE
		srp_erp_generalledger.companyID IN (" . join(',', $companies) . ")
		AND GLType = 'PLI' 
		AND documentCode IN ( 'CINV', 'DO', 'POS', 'RV', 'SLR', 'CN' ) 
		
	
	GROUP BY
		documentMasterAutoID,
		documentCode 
	) t1 
GROUP BY
	partyAutoID) revensummary on revensummary.customerID = Customer.customerAutoID
	Where 
	  companyID IN (" . join(',', $companies) . ")
	  ANd customerID IN  (" . join(',', $customerID) . ") 
	  AND segmentID IN (" . join(',', $segment) . ") 
    GROUP BY 
	customerID
	  ";

        $output = $this->db->query($qry)->result_array();
        return $output;
    }


    function get_revanue_details_drilldown_report()
    {
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $datefrm = $this->input->post('date');
        $datefromconvert = $datefrm . '-01';
        $datetoconvert = $datefrm . '-31';
        $segmentID = $this->input->post('segmentID');
        $companyid = current_companyID();
        $date = "";
        $date1 = "";
        $date2 = "";
        $date3 = "";
        $search1 = "";
        $search2 = "";
        $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
        $date1 .= " AND ( DODate >= '" . $datefromconvert . " 00:00:00' AND DODate <= '" . $datetoconvert . " 00:00:00' ) ";
        $date2 .= " AND ( RVdate >= '" . $datefromconvert . " 00:00:00' AND RVdate <= '" . $datetoconvert . " 00:00:00' ) ";
        $date3 .= " AND ( returnDate >= '" . $datefromconvert . " 00:00:00' AND returnDate <= '" . $datetoconvert . " 00:00:00' ) ";
        $search = "";
        //$qry = "SELECT DATE_FORMAT(contractDate,'" . $convertFormat . "') as documentDate, srp_erp_contractmaster.transactionAmount, a.invoiceAmount, a.nonTaxAmount, a.receiptAmount, transactionCurrency, transactionCurrencyDecimalPlaces, srp_erp_customermaster.customerName, contractCode, srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.documentID FROM srp_erp_contractmaster LEFT JOIN ( SELECT SUM(ab.invoiceAmount) AS invoiceAmount, SUM(ab.nonTaxAmount) AS nonTaxAmount, SUM(ab.receiptAmount) AS receiptAmount, ab.contractAutoID FROM ( SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS invoiceAmount, SUM(civ.transactionAmount) AS nonTaxAmount, SUM(crv.transactionAmount) AS receiptAmount, tax.taxPercentage, contractAutoID, civ.invoiceAutoID FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID = ".current_companyID()." GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID = ".current_companyID()." GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS transactionAmount, invoiceAutoID FROM srp_erp_customerreceiptdetail LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId WHERE srp_erp_customerreceiptdetail.companyID = ".current_companyID()." AND approvedYN = 1 GROUP BY invoiceAutoID ) crv ON crv.invoiceAutoID = civ.invoiceAutoID WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID IS NOT NULL GROUP BY contractAutoID, civ.invoiceAutoID ) ab GROUP BY ab.contractAutoID ) a ON a.contractAutoID = srp_erp_contractmaster.contractAutoID LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_contractmaster.companyID = ".current_companyID()." AND srp_erp_contractmaster.customerID IN (".join(',',$customerID).") AND srp_erp_contractmaster.documentID = 'SO' AND approvedYN = 1 $search GROUP BY srp_erp_contractmaster.contractAutoID ORDER BY transactionCurrency";
        $qry = "Select * from (SELECT
	`srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
	`srp_erp_customerinvoicemaster`.`documentID` AS `documentID`,
	`invoiceCode`,
	`invoiceNarration`,
	`srp_erp_customermaster`.`customerName` AS `customermastername`,
	`transactionCurrencyDecimalPlaces`,
	`transactionCurrency`,
	`transactionExchangeRate`,
	`companyLocalCurrency`,
	`companyLocalCurrencyDecimalPlaces`,
	companyLocalExchangeRate,
	`companyReportingCurrency`,
	`companyReportingExchangeRate`,
	`companyReportingCurrencyDecimalPlaces`,
   
	`confirmedYN`,
	`approvedYN`,
	srp_erp_customerinvoicemaster.segmentCode as segid,
	`srp_erp_customerinvoicemaster`.`createdUserID` AS `createdUser`,
	DATE_FORMAT(invoiceDate,'" . $convertFormat . "') AS invoiceDate,
	DATE_FORMAT(invoiceDueDate,'" . $convertFormat . "') AS invoiceDueDate,
	`invoiceType`,
	((
		IFNULL(addondet.taxPercentage, 0) / 100
	) * (
		IFNULL((det.transactionAmount-retensionTransactionAmount), 0) - IFNULL(det.detailtaxamount, 0) - (
			(
				IFNULL(
					gendiscount.discountPercentage,
					0
				) / 100
			) * IFNULL((det.transactionAmount-retensionTransactionAmount), 0)
		) + IFNULL(
			genexchargistax.transactionAmount,
			0
		)
	) + IFNULL((det.transactionAmount-retensionTransactionAmount), 0) - (
		(
			IFNULL(
				gendiscount.discountPercentage,
				0
			) / 100
		) * IFNULL((det.transactionAmount-retensionTransactionAmount), 0)
	) + IFNULL(
		genexcharg.transactionAmount,
		0
	)) - IFNULL( det.detailtaxamount, 0 )  AS total_value,
	0 as returnAmount,
	IFNULL(creditnote.credittransactionAmount, 0) as credittransactionAmount,
	IFNULL( rvmatch.rvmatchtransactionAmount, 0) AS rvmatchtransactionAmount,
	IFNULL( rvmatch.rvmatchcompanyLocalAmount, 0) AS rvmatchcompanyLocalAmount,
	IFNULL( rvmatch.rvmatchcompanyReportingAmount, 0) AS rvmatchcompanyReportingAmount,
	IFNULL(creditnote.creditcompanyLocalAmount, 0) as creditcompanyLocalAmount,
	IFNULL(creditnote.creditcompanyReportingAmount, 0) as creditcompanyReportingAmount,
	IFNULL(receiptdet.receipttransactionAmount, 0) as receipttransactionAmount,
	IFNULL(receiptdet.receiptcompanyLocalAmount, 0) as receiptcompanyLocalAmount,
	IFNULL(receiptdet.receiptcompanyReportingAmount, 0) as receiptcompanyReportingAmount
FROM
	`srp_erp_customerinvoicemaster`
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		sum(totalafterTax) AS detailtaxamount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicedetails
	Where
	   revenueGLType = 'PLI'	  
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

	LEFT JOIN (
	SELECT
		SUM(srp_erp_rvadvancematchdetails.transactionAmount) AS rvmatchtransactionAmount,
	SUM(srp_erp_rvadvancematchdetails.companyLocalAmount) AS rvmatchcompanyLocalAmount,
	SUM(srp_erp_rvadvancematchdetails.companyReportingAmount) AS rvmatchcompanyReportingAmount,
	invoiceAutoID 
FROM
	srp_erp_rvadvancematchdetails
	LEFT JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematch`.`matchID` = `srp_erp_rvadvancematchdetails`.`matchID` 
WHERE
	srp_erp_rvadvancematch.confirmedYN = 1 
GROUP BY
	invoiceAutoID
	) rvmatch ON ( `rvmatch`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID )
LEFT JOIN (
	SELECT
		SUM(srp_erp_creditnotedetail.transactionAmount) AS credittransactionAmount,
		SUM(srp_erp_creditnotedetail.companyLocalAmount) AS creditcompanyLocalAmount,
		SUM(srp_erp_creditnotedetail.companyReportingAmount) AS creditcompanyReportingAmount,
		invoiceAutoID
	FROM
		srp_erp_creditnotedetail
		LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
	WHERE
	    srp_erp_creditnotemaster.approvedYN=1
		AND srp_erp_creditnotedetail.companyID=" . current_companyID() . "
		AND srp_erp_creditnotedetail.GLType = 'PLI'
	GROUP BY
		invoiceAutoID
) creditnote ON (
	`creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)

LEFT JOIN (
	SELECT
		SUM(srp_erp_customerreceiptdetail.transactionAmount) AS receipttransactionAmount,
		SUM(srp_erp_customerreceiptdetail.companyLocalAmount) AS receiptcompanyLocalAmount,
		SUM(srp_erp_customerreceiptdetail.companyReportingAmount) AS receiptcompanyReportingAmount,
		invoiceAutoID,
		srp_erp_customerreceiptdetail.segmentID as segment
	FROM
		srp_erp_customerreceiptdetail
		LEFT JOIN srp_erp_customerreceiptmaster  ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
	WHERE
	srp_erp_customerreceiptmaster.approvedYN = 1
	AND srp_erp_customerreceiptdetail.GLType = 'PLI'
	AND	type = 'Invoice'
		AND srp_erp_customerreceiptdetail.companyID=" . current_companyID() . "
	GROUP BY
		invoiceAutoID,
		segment
) receiptdet ON (
	`receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_customerinvoicemaster`.`customerID`
WHERE
    srp_erp_customerinvoicemaster.companyID = " . current_companyID() . " AND approvedYN=1   $date $search AND customerID = $customerID AND srp_erp_customerinvoicemaster.segmentID IN (" . join(',', $segmentID) . ")  
     HAVING
total_value > 0
    UNION ALL 
    	SELECT
	domaster.DOAutoID AS `invoiceAutoID`,
	`domaster`.`documentID` AS `documentID`,
	domaster.DOCode AS invoiceCode,
	domaster.narration AS invoiceNarration,
		`srp_erp_customermaster`.`customerName` AS `customermastername`,
		
 `transactionCurrencyDecimalPlaces` ,
	 `transactionCurrency`  ,
	 `transactionExchangeRate` , 
	 `companyLocalCurrency` , 
	 `companyLocalCurrencyDecimalPlaces` ,
	 companyLocalExchangeRate ,
	 `companyReportingCurrency` ,
	 `companyReportingExchangeRate` ,
	 `companyReportingCurrencyDecimalPlaces` ,
	
	`confirmedYN`,
	`approvedYN`,
	domaster.segmentCode AS segid,
	`domaster`.`createdUserID` AS `createdUser`,
	DATE_FORMAT( DODate, '%d-%m-%Y' ) AS invoiceDate,
		DATE_FORMAT( domaster.invoiceDueDate, '%d-%m-%Y' ) AS invoiceDueDate,
				DOType as invoiceType,
		det.transactionAmount as total_value,
0  AS returnAmount,
0 as credittransactionAmount,
0 as rvmatchtransactionAmount,
0 as rvmatchcompanyLocalAmount,
0 as  rvmatchcompanyReportingAmount,
0 as  creditcompanyLocalAmount,
0 as  creditcompanyReportingAmount,
0 as receipttransactionAmount,
0 as receiptcompanyLocalAmount,
0 as receiptcompanyReportingAmount

FROM

	srp_erp_deliveryorder domaster

	LEFT JOIN (
	SELECT
		SUM( srp_erp_deliveryorderdetails.deliveredTransactionAmount) AS transactionAmount,
		sum( totalafterTax ) AS detailtaxamount,
		srp_erp_deliveryorderdetails.DOAutoID 
	FROM
		srp_erp_deliveryorderdetails
		LEFT JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
		WHERE 
		revenueGLType = 'PLI'
	GROUP BY
		srp_erp_deliveryorderdetails.DOAutoID 
	) det ON ( `det`.`DOAutoID` = domaster.doautoID )
	-- LEFT JOIN ( SELECT SUM( IFNULL(totalValue,0) ) AS totalreturnamount, DOAutoID FROM `srp_erp_salesreturndetails` WHERE companyID = $companyid AND revenueGLType = 'PLI' GROUP BY DOAutoID ) salesrtn ON salesrtn.DOAutoID = domaster.DOAutoID 
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, DOAutoID FROM srp_erp_deliveryordertaxdetails GROUP BY DOAutoID ) addondet ON ( `addondet`.`DOAutoID` = domaster.DOAutoID )
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `domaster`.`customerID` 
WHERE
	domaster.companyID = $companyid 
	AND domaster.approvedYN = 1 
    $date1 $search1
	AND  customerID = $customerID
	AND domaster.segmentID IN (" . join(',', $segmentID) . ") 
	 HAVING
total_value > 0
	UNION ALL 
	SELECT
	srp_erp_pos_invoice.invoiceID AS `invoiceAutoID`,
	documentCode AS `documentID`,
	documentSystemCode AS invoiceCode,
	' ' AS invoiceNarration,
    IFNULL( cusmaster.customerName, 'From POS Customers' ) AS customermastername,
	transactionCurrencyDecimalPlaces,
	transactionCurrency,
	transactionExchangeRate,
	`companyLocalCurrency`,
	`companyLocalCurrencyDecimalPlaces`,
	companyLocalExchangeRate,
	`companyReportingCurrency`,
	`companyReportingExchangeRate`,
	`companyReportingCurrencyDecimalPlaces`,
	1 AS confirmedYN,
	1 AS approvedYN,
	segmentCode AS segid,
	`srp_erp_pos_invoice`.`createdUserID` AS `createdUser`,
	DATE_FORMAT( invoiceDate, '%d-%m-%Y' ) AS invoiceDate,
	' ' AS invoiceDueDate,
	' ' AS invoiceType,
	transactionamount AS total_value,
    0 AS returnAmount,
	0 AS credittransactionAmount,
	0 AS rvmatchtransactionAmount,
	0 AS rvmatchcompanyLocalAmount,
	0 AS rvmatchcompanyReportingAmount,
	0 AS creditcompanyLocalAmount,
	0 AS creditcompanyReportingAmount,
	0 AS receipttransactionAmount,
	0 AS receiptcompanyLocalAmount,
	0 AS receiptcompanyReportingAmount 
FROM
	srp_erp_pos_invoice
    LEFT JOIN ( 
    SELECT IFNULL( SUM( transactionAmount ), 0 ) AS transactionamount,srp_erp_pos_invoice.invoiceID,IF(customerID = 0, -1,IFNULL( customerID, - 1 ))  AS customerID FROM srp_erp_pos_invoicedetail
    LEFT JOIN srp_erp_pos_invoice on srp_erp_pos_invoicedetail.invoiceID = srp_erp_pos_invoice.invoiceID
    WHERE
    srp_erp_pos_invoicedetail.companyID = $companyid 
    AND revenueGLType = 'PLI'
    GROUP BY srp_erp_pos_invoice.invoiceID ) posinvoicedet ON posinvoicedet.invoiceID = srp_erp_pos_invoice.invoiceID

	LEFT JOIN 
(SELECT  * FROM(Select
customerAutoID,
customerName
from 
srp_erp_customermaster 
where
companyID = $companyid
UNION ALL 
Select
-1  as customerAutoID,
'From POS Customers'  as customerName
from 
srp_erp_customermaster
where
companyID = $companyid) t1  GROUP BY 
CustomerAutoID)  cusmaster ON cusmaster.customerAutoID = posinvoicedet.customerID
	
WHERE
	srp_erp_pos_invoice.companyID = $companyid 
	AND srp_erp_pos_invoice.isVoid != 1 
	$date $search

    AND posinvoicedet.customerID = $customerID
	AND segmentID IN(" . join(',', $segmentID) . ")
 HAVING
total_value > 0
	 
	 UNION ALL 
	
	SELECT
	srp_erp_customerreceiptmaster.receiptVoucherAutoId AS `invoiceAutoID`,
	documentID AS `documentID`,
	RVcode AS invoiceCode,
	RVNarration AS invoiceNarration,
    IFNULL( cusmaster.customerName, 'From Direct Receipt voucher' ) AS customermastername,
	transactionCurrencyDecimalPlaces,
	transactionCurrency,
	transactionExchangeRate,
	`companyLocalCurrency`,
	`companyLocalCurrencyDecimalPlaces`,
	companyLocalExchangeRate,
	`companyReportingCurrency`,
	`companyReportingExchangeRate`,
	`companyReportingCurrencyDecimalPlaces`,
	confirmedYN,
	approvedYN,
	segmentCode AS segid,
	`srp_erp_customerreceiptmaster`.`createdUserID` AS `createdUser`,
	DATE_FORMAT( RVdate, '%d-%m-%Y' ) AS invoiceDate,
	' ' AS invoiceDueDate,
	RVType AS invoiceType,
	IFNULL( cusdetail.transactionamount, 0 ) AS total_value,
	0 AS returnAmount,
	0 AS credittransactionAmount,
	0 AS rvmatchtransactionAmount,
	0 AS rvmatchcompanyLocalAmount,
	0 AS rvmatchcompanyReportingAmount,
	0 AS creditcompanyLocalAmount,
	0 AS creditcompanyReportingAmount,
	0 AS receipttransactionAmount,
	0 AS receiptcompanyLocalAmount,
	0 AS receiptcompanyReportingAmount 
FROM
	srp_erp_customerreceiptmaster
	LEFT JOIN ( SELECT SUM(( transactionamount )) AS transactionamount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE companyID = $companyid AND GLType = 'PLI' AND type IN ( 'GL', 'Item' ) GROUP BY receiptVoucherAutoId ) cusdetail ON cusdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
	LEFT JOIN (
	SELECT
		* 
	FROM
		(SELECT customerAutoID, customerName FROM srp_erp_customermaster WHERE companyID = $companyid UNION ALL SELECT -2 AS customerAutoID, 'From Direct Receipt voucher'  AS customerName FROM srp_erp_customermaster WHERE companyID = $companyid ) t1 
	GROUP BY
		CustomerAutoID)cusmaster ON cusmaster.customerAutoID = IF(srp_erp_customerreceiptmaster.customerID=0,-2, srp_erp_customerreceiptmaster.customerID) 
WHERE
	companyID = $companyid $date2 $search2
	AND approvedYN = 1 
	AND cusmaster.customerAutoID = $customerID
    AND segmentID IN(" . join(',', $segmentID) . ")
     HAVING
total_value > 0
    UNION ALL 
    SELECT
	`srp_erp_salesreturndetails`.`salesReturnAutoID` AS `invoiceAutoID`,
	`srp_erp_salesreturnmaster`.`documentID` AS `documentID`,
	salesReturnCode AS `invoiceCode`,
	`comment` AS `invoiceNarration`,
	( customer.customerName ) AS customermastername,
	transactionCurrencyDecimalPlaces,
	transactionCurrency,
	transactionExchangeRate,
	`companyLocalCurrency`,
	`companyLocalCurrencyDecimalPlaces`,
	companyLocalExchangeRate,
	`companyReportingCurrency`,
	`companyReportingExchangeRate`,
	`companyReportingCurrencyDecimalPlaces`,
	confirmedYN,
	approvedYN,
	segmentCode AS segid,
	`srp_erp_salesreturnmaster`.`createdUserID` AS `createdUser`,
	DATE_FORMAT( returnDate, '%d-%m-%Y' ) AS invoiceDate,
	' ' AS invoiceDueDate,
	' ' AS invoiceType,
	0 as 	total_value,
	SUM( IFNULL(totalValue,0) )*-1 AS returnAmount ,
		0 AS credittransactionAmount,
		0 AS rvmatchtransactionAmount,
		0 AS rvmatchcompanyLocalAmount,
		0 AS rvmatchcompanyReportingAmount,
		0 AS creditcompanyLocalAmount,
		0 AS creditcompanyReportingAmount,
		0 AS receipttransactionAmount,
		0 AS receiptcompanyLocalAmount,
		0 AS receiptcompanyReportingAmount 
FROM
	srp_erp_salesreturndetails
	LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
	LEFT JOIN ( SELECT customerAutoID, customerName FROM srp_erp_customermaster WHERE companyID = $companyid ) customer ON customer.CustomerAutoID = srp_erp_salesreturnmaster.customerID 
WHERE
	srp_erp_salesreturnmaster.approvedYN = 1 
	AND srp_erp_salesreturndetails.revenueGLType = 'PLI' 
	$date3
	AND srp_erp_salesreturnmaster.companyID = $companyid 
	AND customerID = $customerID
	AND segmentID IN (" . join(',', $segmentID) . ")
GROUP BY
	srp_erp_salesreturnmaster.salesReturnAutoID) t1";

        $output = $this->db->query($qry)->result_array();
        /*   echo '<pre>'; echo  $this->db->last_query();
           exit;*/
        return $output;
    }


    function get_group_revanue_details_drilldown_report()
    {
        $company = $this->get_group_company();
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $datefrm = $this->input->post('date');
        $datefromconvert = $datefrm . '-01';
        $datetoconvert = $datefrm . '-31';
        $segment = $this->input->post('segmentID');

        $date = "";
        $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";

        $search = "";
        //$qry = "SELECT DATE_FORMAT(contractDate,'" . $convertFormat . "') as documentDate, srp_erp_contractmaster.transactionAmount, a.invoiceAmount, a.nonTaxAmount, a.receiptAmount, transactionCurrency, transactionCurrencyDecimalPlaces, srp_erp_customermaster.customerName, contractCode, srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.documentID FROM srp_erp_contractmaster LEFT JOIN ( SELECT SUM(ab.invoiceAmount) AS invoiceAmount, SUM(ab.nonTaxAmount) AS nonTaxAmount, SUM(ab.receiptAmount) AS receiptAmount, ab.contractAutoID FROM ( SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS invoiceAmount, SUM(civ.transactionAmount) AS nonTaxAmount, SUM(crv.transactionAmount) AS receiptAmount, tax.taxPercentage, contractAutoID, civ.invoiceAutoID FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID = ".current_companyID()." GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID = ".current_companyID()." GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS transactionAmount, invoiceAutoID FROM srp_erp_customerreceiptdetail LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId WHERE srp_erp_customerreceiptdetail.companyID = ".current_companyID()." AND approvedYN = 1 GROUP BY invoiceAutoID ) crv ON crv.invoiceAutoID = civ.invoiceAutoID WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID IS NOT NULL GROUP BY contractAutoID, civ.invoiceAutoID ) ab GROUP BY ab.contractAutoID ) a ON a.contractAutoID = srp_erp_contractmaster.contractAutoID LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_contractmaster.companyID = ".current_companyID()." AND srp_erp_contractmaster.customerID IN (".join(',',$customerID).") AND srp_erp_contractmaster.documentID = 'SO' AND approvedYN = 1 $search GROUP BY srp_erp_contractmaster.contractAutoID ORDER BY transactionCurrency";
        $qry = "SELECT
	`srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
	`srp_erp_customerinvoicemaster`.`documentID` AS `documentID`,
	`invoiceCode`,
	`invoiceNarration`,
	`cust`.`groupCustomerName` AS `customermastername`,
	`transactionCurrencyDecimalPlaces`,
	`transactionCurrency`,
	`transactionExchangeRate`,
	`companyLocalCurrency`,
	`companyLocalCurrencyDecimalPlaces`,
	companyLocalExchangeRate,
	`companyReportingCurrency`,
	`companyReportingExchangeRate`,
	`companyReportingCurrencyDecimalPlaces`,
	`confirmedYN`,
	`approvedYN`,
	seg.segmentCode as segid,
	`srp_erp_customerinvoicemaster`.`createdUserID` AS `createdUser`,
	DATE_FORMAT(invoiceDate,'" . $convertFormat . "') AS invoiceDate,
	DATE_FORMAT(invoiceDueDate,'" . $convertFormat . "') AS invoiceDueDate,
	`invoiceType`,
	((
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * (
				(
					IFNULL(det.transactionAmount, 0) - (
						IFNULL(det.detailtaxamount, 0)
					)
				)
			)
		) + IFNULL(det.transactionAmount, 0)
	))- IFNULL(det.detailtaxamount,0) AS total_value,
	IFNULL(detreturn.totalValue, 0) as returnAmount,
	IFNULL(creditnote.credittransactionAmount, 0) as credittransactionAmount,
	IFNULL(creditnote.creditcompanyLocalAmount, 0) as creditcompanyLocalAmount,
	IFNULL(creditnote.creditcompanyReportingAmount, 0) as creditcompanyReportingAmount,
	IFNULL(receiptdet.receipttransactionAmount, 0) as receipttransactionAmount,
	IFNULL(receiptdet.receiptcompanyLocalAmount, 0) as receiptcompanyLocalAmount,
	IFNULL(receiptdet.receiptcompanyReportingAmount, 0) as receiptcompanyReportingAmount
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
		SUM(totalValue) AS totalValue,
		invoiceAutoID
	FROM
		srp_erp_salesreturndetails
		LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
	WHERE
		srp_erp_salesreturnmaster.approvedYN=1
	GROUP BY
		invoiceAutoID
) detreturn ON (
	`detreturn`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(srp_erp_creditnotedetail.transactionAmount) AS credittransactionAmount,
		SUM(srp_erp_creditnotedetail.companyLocalAmount) AS creditcompanyLocalAmount,
		SUM(srp_erp_creditnotedetail.companyReportingAmount) AS creditcompanyReportingAmount,
		invoiceAutoID
	FROM
		srp_erp_creditnotedetail
		LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
	WHERE
	    srp_erp_creditnotemaster.approvedYN=1
		AND srp_erp_creditnotedetail.companyID IN (" . join(',', $company) . ")
	GROUP BY
		invoiceAutoID
) creditnote ON (
	`creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)

LEFT JOIN (
	SELECT
		SUM(srp_erp_customerreceiptdetail.transactionAmount) AS receipttransactionAmount,
		SUM(srp_erp_customerreceiptdetail.companyLocalAmount) AS receiptcompanyLocalAmount,
		SUM(srp_erp_customerreceiptdetail.companyReportingAmount) AS receiptcompanyReportingAmount,
		invoiceAutoID
	FROM
		srp_erp_customerreceiptdetail
		LEFT JOIN srp_erp_customerreceiptmaster  ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
	WHERE
	srp_erp_customerreceiptmaster.approvedYN = 1
	AND	type = 'Invoice'
		AND srp_erp_customerreceiptdetail.companyID IN (" . join(',', $company) . ")
	GROUP BY
		invoiceAutoID
) receiptdet ON (
	`receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID = $customerID) cust ON cust.customerMasterID = `srp_erp_customerinvoicemaster`.`customerID`
INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(" . join(',', $segment) . ")) seg ON srp_erp_customerinvoicemaster.segmentID = seg.segmentID
WHERE
    srp_erp_customerinvoicemaster.companyID IN (" . join(',', $company) . ") AND approvedYN=1   $date $search ORDER BY srp_erp_customerinvoicemaster.invoiceDate ASC";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }


    function get_group_company()
    {
        $this->db->select("companyID");
        $this->db->from('srp_erp_companygroupdetails');
        $this->db->where('companyGroupID', current_companyID());
        $company = $this->db->get()->result_array();
        return array_column($company, 'companyID');
    }

    /*    function get_sales_person_performance_report()
        {
            $convertFormat = convert_date_format_sql();
            $salesperson = $this->input->post('salesperson');
            $date_format_policy = date_format_policy();
            $datefrom = $this->input->post('datefrom');
            $datefromconvert = input_format_date($datefrom, $date_format_policy);
            $dateto = $this->input->post('dateto');
            $datetoconvert = input_format_date($dateto, $date_format_policy);
            $date = "";
            $datecontract = "";
            $companyid = current_companyID();
            if (!empty($datefrom) && !empty($dateto)) {
                $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
            }
            if (!empty($datefrom) && !empty($dateto)) {
                $datecontract .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 00:00:00')";
            }

            /* $qry = "Select * from (SELECT
                             IF(invoicemaster.salesPersonID!=0, contract.salesPersonID, invoicemaster.salesPersonID) as salesPersonID,
                             contract.contractAutoID,
                             invoicemaster.invoiceAutoID,
                             IFNULL( sum( contract.contractamount ), 0 ) contractvalue,
                             sum( invoicedetail.transactionAmount ) / invoicemaster.companyLocalExchangeRate AS invoicelocalmamount,
                             sum( invoicedetail.transactionAmount ) / invoicemaster.companyReportingExchangeRate AS invoicereportingamount,
                             sum( invoicedetail.transactionAmount ) / invoicemaster.transactionExchangeRate AS invoicetransactionamount,
                             sum( contract.contractamount ) / contractmaster.transactionExchangeRate AS contractmastertransactionamount,
                             sum( contract.contractamount ) / contractmaster.companyReportingExchangeRate AS contractmasterreportingexchange,
                             sum( contract.contractamount ) / contractmaster.companyLocalExchangeRate AS contractmasterlocalexchange,
                             invoicemaster.transactionCurrencyDecimalPlaces AS invoicetransactionCurrencyDecimalPlaces,
                             invoicemaster.companyLocalCurrencyDecimalPlaces AS invoicecompanyLocalCurrencyDecimalPlaces,
                             invoicemaster.companyReportingCurrencyDecimalPlaces AS invoicecompanyReportingCurrencyDecimalPlaces,
                             contractmaster.transactionCurrencyDecimalPlaces AS contracttransactionCurrencyDecimalPlaces,
                             contractmaster.companyLocalCurrencyDecimalPlaces AS contractLocalCurrencyDecimalPlaces,
                             contractmaster.companyReportingCurrencyDecimalPlaces AS contractReportingCurrencyDecimalPlaces,
                             salesperson.SalesPersonName AS salesPersonName,
                             invoicemaster.transactionCurrency,
                             invoicemaster.companyLocalCurrency,
                             invoicemaster.companyReportingCurrency,
                             1 AS docTye
                         FROM
                             srp_erp_customerinvoicedetails invoicedetail
                             LEFT JOIN srp_erp_customerinvoicemaster invoicemaster ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID
                             JOIN srp_erp_salespersonmaster salesperson ON salesperson.salesPersonID = invoicemaster.salesPersonID
                             JOIN ( SELECT conatractmaster.contractAutoID,
                             conatractmaster.salesPersonID,
                             sum( srp_erp_contractdetails.transactionAmount ) AS contractamount  FROM srp_erp_contractdetails
                                 LEFT JOIN srp_erp_contractmaster conatractmaster ON conatractmaster.contractAutoID = srp_erp_contractdetails.contractAutoID


                             GROUP BY contractAutoID ) contract ON contract.contractAutoID = invoicedetail.contractAutoID
                             LEFT JOIN srp_erp_contractmaster contractmaster ON contractmaster.contractAutoID = contract.contractAutoID
                         WHERE
                             invoicemaster.companyID = $companyid
                             AND invoicemaster.approvedYN = 1
                             AND contractmaster.approvedYN = 1
                             $date
                             $datecontract
                             AND invoicemaster.salesPersonID IN (" . join(',', $salesperson) . ")

                         GROUP BY
                             invoicemaster.salesPersonID UNION
                         SELECT
                             contractmaster.salesPersonID,
                             contractmaster.contractAutoID,
                             \"0\" AS invoiceAutoID,
                             IFNULL( sum( contractdetail.transactionAmount ), 0 ) AS contractvalue,
                             \"0\" AS invoicelocalmamount,
                             \"0\" AS invoicereportingamount,
                             \"0\" AS invoicetransactionamount,
                             sum( contractdetail.transactionAmount ) / contractmaster.transactionExchangeRate AS contractmastertransactionamount,
                             sum( contractdetail.transactionAmount ) / contractmaster.companyReportingExchangeRate AS contractmasterreportingexchange,
                             sum( contractdetail.transactionAmount ) / contractmaster.companyLocalExchangeRate AS contractmasterlocalexchange,
                             \"0\" AS invoicetransactionCurrencyDecimalPlaces,
                             \"0\" AS invoicecompanyLocalCurrencyDecimalPlaces,
                             \"0\" AS invoicecompanyReportingCurrencyDecimalPlaces,
                             contractmaster.transactionCurrencyDecimalPlaces AS contracttransactionExchangeRate,
                             contractmaster.companyLocalCurrencyDecimalPlaces AS contractLocalCurrencyDecimalPlaces,
                             contractmaster.companyReportingCurrencyDecimalPlaces AS contractReportingCurrencyDecimalPlaces,
                             salesperson.SalesPersonName AS salesPersonName,
                             contractmaster.transactionCurrency,
                             contractmaster.companyLocalCurrency,
                             contractmaster.companyReportingCurrency,
                             1 AS docTye
                         FROM
                             srp_erp_contractdetails contractdetail
                             LEFT JOIN srp_erp_contractmaster contractmaster ON contractmaster.contractAutoID = contractdetail.contractAutoID
                             JOIN srp_erp_salespersonmaster salesperson ON salesperson.salesPersonID = contractmaster.salesPersonID
                         WHERE
                             contractmaster.companyID = $companyid
                             AND contractmaster.approvedYN = 1
                             $datecontract
                             AND contractmaster.salesPersonID IN (" . join(',', $salesperson) . ")
                             AND contractmaster.salesPersonID IS NOT NULL
                             AND contractmaster.contractAutoID NOT IN (
                         SELECT
                             ifnull( contractAutoID, 0 )
                         FROM
                             srp_erp_customerinvoicedetails invoicedetail
                             JOIN srp_erp_customerinvoicemaster invoicemaster ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID
                         WHERE
                             invoicemaster.companyID = $companyid
                         GROUP BY
                             invoicedetail.contractAutoID

                             )
                         GROUP BY
                             contractmaster.salesPersonID
                             UNION
                         SELECT
                             srp_erp_customerinvoicemaster.salesPersonID,
                             \"0\" AS contractAutoID,
                             srp_erp_customerinvoicemaster.invoiceAutoID,
                             \"0\" AS contractvalue,
                             sum( srp_erp_customerinvoicedetails.transactionAmount ) / srp_erp_customerinvoicemaster.companyLocalExchangeRate AS invoicelocalmamount,
                             sum( srp_erp_customerinvoicedetails.transactionAmount ) / srp_erp_customerinvoicemaster.companyReportingExchangeRate AS invoicereportingamount,
                             sum( srp_erp_customerinvoicedetails.transactionAmount ) / srp_erp_customerinvoicemaster.transactionExchangeRate AS invoicetransactionamount,
                             0 AS contractmastertransactionamount,
                             0 AS contractmasterreportingexchange,
                             0 AS contractmasterlocalexchange,
                             srp_erp_customerinvoicemaster.transactionCurrencyDecimalPlaces AS invoicetransactionCurrencyDecimalPlaces,
                             srp_erp_customerinvoicemaster.companyLocalCurrencyDecimalPlaces AS invoicecompanyLocalCurrencyDecimalPlaces,
                             srp_erp_customerinvoicemaster.companyReportingCurrencyDecimalPlaces AS invoicecompanyReportingCurrencyDecimalPlaces,
                             0 AS contracttransactionCurrencyDecimalPlaces,
                             0 AS contractLocalCurrencyDecimalPlaces,
                             0 AS contractReportingCurrencyDecimalPlaces,
                             salesperson.SalesPersonName AS salesPersonName,
                             srp_erp_customerinvoicemaster.transactionCurrency,
                             srp_erp_customerinvoicemaster.companyLocalCurrency,
                             srp_erp_customerinvoicemaster.companyReportingCurrency,
                             2 AS docTye
                         FROM
                             `srp_erp_customerinvoicemaster`
                             LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
                             JOIN srp_erp_salespersonmaster salesperson ON salesperson.salesPersonID = srp_erp_customerinvoicemaster.salesPersonID
                         WHERE
                             srp_erp_customerinvoicemaster.companyID =  $companyid
                             AND invoiceType = 'Direct'
                             AND srp_erp_customerinvoicemaster.approvedYN = 1
                             $date
                             AND srp_erp_customerinvoicemaster.salesPersonID IN (" . join(',', $salesperson) . ")
                             AND srp_erp_customerinvoicemaster.salesPersonID IS NOT NULL GROUP BY 	srp_erp_customerinvoicemaster.salesPersonID



                             ) t1
                             GROUP BY
                         t1.salesPersonID,t1.docTye";*/

    /* $qry = "SELECT * From(SELECT
 conatractmaster.salesPersonID AS salesPersonID,
 conatractmaster.contractAutoID,
 IFNULL( invoicedetailmaster.invoiceAutoID, 0 ) AS invoiceAutoID,
 IFNULL( sum( srp_erp_contractdetails.transactionAmount ), 0 ) contractvalue,
 IFNULL( sum( invoicedetailmaster.transactionAmount /invoicedetailmaster.companyLocalExchangeRate ) , 0 ) AS invoicelocalmamount,
 IFNULL( sum( invoicedetailmaster.transactionAmount / invoicedetailmaster.companyReportingExchangeRate ) , 0 ) AS invoicereportingamount,
 IFNULL( sum( invoicedetailmaster.transactionAmount / invoicedetailmaster.transactionExchangeRate) , 0 ) AS invoicetransactionamount,
 IFNULL( sum( srp_erp_contractdetails.transactionAmount / conatractmaster.transactionExchangeRate ) , 0 ) AS contractmastertransactionamount,
 sum( srp_erp_contractdetails.transactionAmount/ conatractmaster.companyReportingExchangeRate )  AS contractmasterreportingexchange,
 sum( srp_erp_contractdetails.transactionAmount/ conatractmaster.companyLocalExchangeRate )  AS contractmasterlocalexchange,
 IFNULL( invoicedetailmaster.transactionCurrencyDecimalPlaces, conatractmaster.transactionCurrencyDecimalPlaces) AS invoicetransactionCurrencyDecimalPlaces,
 IFNULL( invoicedetailmaster.companyLocalCurrencyDecimalPlaces, conatractmaster.companyLocalCurrencyDecimalPlaces) AS invoicecompanyLocalCurrencyDecimalPlaces,
 IFNULL( invoicedetailmaster.companyReportingCurrencyDecimalPlaces, conatractmaster.companyReportingCurrencyDecimalPlaces) AS invoicecompanyReportingCurrencyDecimalPlaces,
 conatractmaster.transactionCurrencyDecimalPlaces AS contracttransactionCurrencyDecimalPlaces,
 conatractmaster.companyLocalCurrencyDecimalPlaces AS contractLocalCurrencyDecimalPlaces,
 conatractmaster.companyReportingCurrencyDecimalPlaces AS contractReportingCurrencyDecimalPlaces,
 salespersoncontract.SalesPersonName AS salesPersonName,
 IFNULL( invoicedetailmaster.transactionCurrency, conatractmaster.transactionCurrency) AS transactionCurrency,
 IFNULL( invoicedetailmaster.companyLocalCurrency, conatractmaster.companyLocalCurrency) AS companyLocalCurrency,
 IFNULL( invoicedetailmaster.companyReportingCurrency, conatractmaster.companyReportingCurrency) AS companyReportingCurrency,
 1 AS docTye
FROM
 srp_erp_contractdetails
 LEFT JOIN srp_erp_contractmaster conatractmaster ON conatractmaster.contractAutoID = srp_erp_contractdetails.contractAutoID
 LEFT JOIN (
SELECT
 invoicedetail.transactionAmount,
 invoicedetail.contractDetailsAutoID,
 invoicemaster.companyLocalExchangeRate,
 invoicemaster.companyReportingExchangeRate,
 invoicemaster.transactionExchangeRate,
 invoicemaster.invoiceAutoID,
 invoicemaster.transactionCurrencyDecimalPlaces,
 invoicemaster.companyLocalCurrencyDecimalPlaces,
 invoicemaster.companyReportingCurrencyDecimalPlaces,
 invoicemaster.transactionCurrency,
 invoicemaster.companyLocalCurrency,
 invoicemaster.companyReportingCurrency
FROM
 srp_erp_customerinvoicedetails invoicedetail
 LEFT JOIN srp_erp_customerinvoicemaster invoicemaster ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID
WHERE
 invoicedetail.companyID = $companyid
 AND invoicemaster.approvedYN = 1
 $date
 ) invoicedetailmaster ON invoicedetailmaster.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID
 JOIN srp_erp_salespersonmaster salespersoncontract ON salespersoncontract.salesPersonID = conatractmaster.salesPersonID
WHERE
 srp_erp_contractdetails.companyID = $companyid
 AND conatractmaster.approvedYN = 1
 $datecontract
 AND conatractmaster.salesPersonID IN (" . join(',', $salesperson) . ")
 AND ( ( conatractmaster.salesPersonID IS NOT NULL ) OR ( conatractmaster.salesPersonID = 0 ) )
GROUP BY
 conatractmaster.salesPersonID
 UNION
 SELECT
 srp_erp_customerinvoicemaster.salesPersonID,
 \"0\" AS contractAutoID,
 srp_erp_customerinvoicemaster.invoiceAutoID,
 \"0\" AS contractvalue,
 sum( srp_erp_customerinvoicedetails.transactionAmount ) / srp_erp_customerinvoicemaster.companyLocalExchangeRate AS invoicelocalmamount,
 sum( srp_erp_customerinvoicedetails.transactionAmount ) / srp_erp_customerinvoicemaster.companyReportingExchangeRate AS invoicereportingamount,
 sum( srp_erp_customerinvoicedetails.transactionAmount ) / srp_erp_customerinvoicemaster.transactionExchangeRate AS invoicetransactionamount,
 0 AS contractmastertransactionamount,
 0 AS contractmasterreportingexchange,
 0 AS contractmasterlocalexchange,
 srp_erp_customerinvoicemaster.transactionCurrencyDecimalPlaces AS invoicetransactionCurrencyDecimalPlaces,
 srp_erp_customerinvoicemaster.companyLocalCurrencyDecimalPlaces AS invoicecompanyLocalCurrencyDecimalPlaces,
 srp_erp_customerinvoicemaster.companyReportingCurrencyDecimalPlaces AS invoicecompanyReportingCurrencyDecimalPlaces,
 0 AS contracttransactionCurrencyDecimalPlaces,
 0 AS contractLocalCurrencyDecimalPlaces,
 0 AS contractReportingCurrencyDecimalPlaces,
 salesperson.SalesPersonName AS salesPersonName,
 srp_erp_customerinvoicemaster.transactionCurrency,
 srp_erp_customerinvoicemaster.companyLocalCurrency,
 srp_erp_customerinvoicemaster.companyReportingCurrency,
 2 AS docTye
FROM
 `srp_erp_customerinvoicemaster`
 LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
 JOIN srp_erp_salespersonmaster salesperson ON salesperson.salesPersonID = srp_erp_customerinvoicemaster.salesPersonID
WHERE
 srp_erp_customerinvoicemaster.companyID = $companyid
 AND srp_erp_customerinvoicemaster.approvedYN = 1
 $date
 AND srp_erp_customerinvoicemaster.salesPersonID IN (" . join(',', $salesperson) . ")
 AND srp_erp_customerinvoicemaster.salesPersonID IS NOT NULL
AND  ((srp_erp_customerinvoicedetails.contractAutoID is null )or(srp_erp_customerinvoicedetails.contractAutoID NOT IN (SELECT conatractmaster.contractAutoID from srp_erp_contractmaster conatractmaster
where companyID = $companyid  AND ((conatractmaster.salesPersonID IS NOT NULL) or (conatractmaster.salesPersonID = 0)) )))
GROUP BY
 srp_erp_customerinvoicemaster.salesPersonID) t1
 GROUP BY
 t1.salesPersonID,t1.docTye";


     $output = $this->db->query($qry)->result_array();
     return $output;
 }*/

    function get_sales_person_performance_report()
    {
        $convertFormat = convert_date_format_sql();
        $salesperson = $this->input->post('salesperson');
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        $datecontract = "";
        $datedo = "";
        $companyid = current_companyID();
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
        }
        if (!empty($datefrom) && !empty($dateto)) {
            $datecontract .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 00:00:00')";
        }

        if (!empty($datefrom) && !empty($dateto)) {
            $datedo .= " AND ( DODate >= '" . $datefromconvert . " 00:00:00' AND DODate <= '" . $datetoconvert . " 00:00:00')";
        }


        $qry = "SELECT * From(
		
		select t3.salesPersonID as salesPersonID,
		t3.contractAutoID as contractAutoID,
		t3.invoiceAutoID as invoiceAutoID,
		t3.DOAutoID as DOAutoID,
		t3.contractvalue as contractvalue,
		t3.invoicelocalmamount as invoicelocalmamount,
		t3.invoicereportingamount as invoicereportingamount,
		t3.invoicetransactionamount as invoicetransactionamount,

		contractMastermain.contractmastertransactionamount as contractmastertransactionamount,
		contractMastermain.contractmasterreportingexchange as contractmasterreportingexchange,
		contractMastermain.contractmasterlocalexchange as contractmasterlocalexchange,
		t3.invoicetransactionCurrencyDecimalPlaces as invoicetransactionCurrencyDecimalPlaces,
		t3.invoicecompanyLocalCurrencyDecimalPlaces as invoicecompanyLocalCurrencyDecimalPlaces,
		t3.invoicecompanyReportingCurrencyDecimalPlaces as invoicecompanyReportingCurrencyDecimalPlaces ,
		t3.contracttransactionCurrencyDecimalPlaces as contracttransactionCurrencyDecimalPlaces,
		t3.contractLocalCurrencyDecimalPlaces as contractLocalCurrencyDecimalPlaces,
		t3.contractReportingCurrencyDecimalPlaces as contractReportingCurrencyDecimalPlaces,
		t3.salesPersonName as salesPersonName,
		t3.transactionCurrency as transactionCurrency,
		t3.companyLocalCurrency as companyLocalCurrency,
		t3.companyReportingCurrency as companyReportingCurrency,
		t3.docTye as docTye,
		t3.dolocalmamount as dolocalmamount,
		t3.doreportingamount as doreportingamount,
		t3.dotransactionamount as dotransactionamount 

		 from(
		SELECT
	conatractmaster.salesPersonID AS salesPersonID,
	conatractmaster.contractAutoID,
	IFNULL( invoicedetailmaster.invoiceAutoID, 0 ) AS invoiceAutoID,
	IFNULL( dodetailmaster.DOAutoID, 0 ) as DOAutoID,
	IFNULL( sum( srp_erp_contractdetails.transactionAmount ), 0 ) contractvalue,
	IFNULL( sum( invoicedetailmaster.transactionAmount /invoicedetailmaster.companyLocalExchangeRate ) , 0 ) AS invoicelocalmamount,
	IFNULL( sum( invoicedetailmaster.transactionAmount / invoicedetailmaster.companyReportingExchangeRate ) , 0 ) AS invoicereportingamount,
	IFNULL( sum( invoicedetailmaster.transactionAmount / invoicedetailmaster.transactionExchangeRate) , 0 ) AS invoicetransactionamount,
		
	
	
	IFNULL( invoicedetailmaster.transactionCurrencyDecimalPlaces, conatractmaster.transactionCurrencyDecimalPlaces) AS invoicetransactionCurrencyDecimalPlaces,
	IFNULL( invoicedetailmaster.companyLocalCurrencyDecimalPlaces, conatractmaster.companyLocalCurrencyDecimalPlaces) AS invoicecompanyLocalCurrencyDecimalPlaces,
	IFNULL( invoicedetailmaster.companyReportingCurrencyDecimalPlaces, conatractmaster.companyReportingCurrencyDecimalPlaces) AS invoicecompanyReportingCurrencyDecimalPlaces,
	conatractmaster.transactionCurrencyDecimalPlaces AS contracttransactionCurrencyDecimalPlaces,
	conatractmaster.companyLocalCurrencyDecimalPlaces AS contractLocalCurrencyDecimalPlaces,
	conatractmaster.companyReportingCurrencyDecimalPlaces AS contractReportingCurrencyDecimalPlaces,
	salespersoncontract.SalesPersonName AS salesPersonName,
	IFNULL( invoicedetailmaster.transactionCurrency, conatractmaster.transactionCurrency) AS transactionCurrency,
	IFNULL( invoicedetailmaster.companyLocalCurrency, conatractmaster.companyLocalCurrency) AS companyLocalCurrency,
	IFNULL( invoicedetailmaster.companyReportingCurrency, conatractmaster.companyReportingCurrency) AS companyReportingCurrency,
	1 AS docTye ,
	IFNULL( sum( dodetailmaster.transactionAmount / dodetailmaster.companyLocalExchangeRate ), 0 ) AS dolocalmamount,
			IFNULL( sum( dodetailmaster.transactionAmount / dodetailmaster.companyReportingExchangeRate ), 0 ) AS doreportingamount,
			IFNULL( sum( dodetailmaster.transactionAmount / dodetailmaster.transactionExchangeRate ), 0 ) AS dotransactionamount
FROM
	srp_erp_contractdetails
	LEFT JOIN srp_erp_contractmaster conatractmaster ON conatractmaster.contractAutoID = srp_erp_contractdetails.contractAutoID
		
	
	LEFT JOIN (
SELECT
	invoicedetail.transactionAmount,
	invoicedetail.contractDetailsAutoID,
	invoicemaster.companyLocalExchangeRate,
	invoicemaster.companyReportingExchangeRate,
	invoicemaster.transactionExchangeRate,
	invoicemaster.invoiceAutoID,
	invoicemaster.transactionCurrencyDecimalPlaces,
	invoicemaster.companyLocalCurrencyDecimalPlaces,
	invoicemaster.companyReportingCurrencyDecimalPlaces,
	invoicemaster.transactionCurrency,
	invoicemaster.companyLocalCurrency,
	invoicemaster.companyReportingCurrency 
FROM
	srp_erp_customerinvoicedetails invoicedetail
	LEFT JOIN srp_erp_customerinvoicemaster invoicemaster ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID 
WHERE
	invoicedetail.companyID = $companyid 
	AND invoicemaster.approvedYN = 1 
	$date
	) invoicedetailmaster ON invoicedetailmaster.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID


	LEFT JOIN (
				SELECT
					dodetail.transactionAmount,
					dodetail.contractDetailsAutoID,
					domaster.companyLocalExchangeRate,
					domaster.companyReportingExchangeRate,
					domaster.transactionExchangeRate,
					domaster.DOAutoID,
					domaster.transactionCurrencyDecimalPlaces,
					domaster.companyLocalCurrencyDecimalPlaces,
					domaster.companyReportingCurrencyDecimalPlaces,
					domaster.transactionCurrency,
					domaster.companyLocalCurrency,
					domaster.companyReportingCurrency 
				FROM
					srp_erp_deliveryorderdetails dodetail
					LEFT JOIN srp_erp_deliveryorder domaster ON domaster.DOAutoID = dodetail.DOAutoID
				WHERE
					dodetail.companyID = $companyid 
					AND domaster.approvedYN = 1 
					$datedo
			) dodetailmaster ON dodetailmaster.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID

	JOIN srp_erp_salespersonmaster salespersoncontract ON salespersoncontract.salesPersonID = conatractmaster.salesPersonID 
WHERE
	srp_erp_contractdetails.companyID = $companyid 
	AND conatractmaster.approvedYN = 1 
	$datecontract
    AND conatractmaster.salesPersonID IN (" . join(',', $salesperson) . ") 
	AND ( ( conatractmaster.salesPersonID IS NOT NULL ) OR ( conatractmaster.salesPersonID = 0 ) ) 
	AND documentID not in ( 'QUT')
GROUP BY
	conatractmaster.salesPersonID

	)t3
		LEFT JOIN 
 (
		SELECT
			salesPersonID,
			sum( srp_erp_contractdetails.transactionAmount ) AS contractmastertransactionamount,
			sum( srp_erp_contractdetails.companyReportingAmount ) AS contractmasterreportingexchange,
			sum( srp_erp_contractdetails.companyLocalAmount ) AS contractmasterlocalexchange 
		FROM
			srp_erp_contractdetails 
			left join srp_erp_contractmaster on srp_erp_contractdetails.contractAutoID=srp_erp_contractmaster.contractAutoID
			where documentID!='QUT'
		GROUP BY
			srp_erp_contractmaster.salesPersonID 
			
		) contractMastermain ON t3.salesPersonID=contractMastermain.salesPersonID

	UNION ALL
	SELECT 
t2.salesPersonID as salesPersonID,
t2.contractAutoID as contractAutoID,
t2.invoiceAutoID as invoiceAutoID,
t2.DOAutoID as DOAutoID,
t2.contractvalue as contractvalue,
SUM(t2.invoicelocalmamount) as invoicelocalmamount,
SUM(t2.invoicereportingamount) as invoicereportingamount,
SUM(t2.invoicetransactionamount) as invoicetransactionamount,

t2.contractmastertransactionamount as  contractmastertransactionamount,
t2.contractmasterreportingexchange as contractmasterreportingexchange,
t2.contractmasterlocalexchange as contractmasterlocalexchange,

t2.invoicetransactionCurrencyDecimalPlaces as invoicetransactionCurrencyDecimalPlaces,
t2.invoicecompanyLocalCurrencyDecimalPlaces as invoicecompanyLocalCurrencyDecimalPlaces ,
t2.invoicecompanyReportingCurrencyDecimalPlaces as invoicecompanyReportingCurrencyDecimalPlaces , 

t2.contracttransactionCurrencyDecimalPlaces,
t2.contractLocalCurrencyDecimalPlaces,
t2.contractReportingCurrencyDecimalPlaces,

t2.SalesPersonName AS salesPersonName,

			t2.transactionCurrency,
			t2.companyLocalCurrency,
			t2.companyReportingCurrency,
			'2' AS docTye,
			SUM(t2.dolocalmamount) as dolocalmamount,
			SUM(t2.doreportingamount) as doreportingamount,
			SUM(t2.dotransactionamount) as dotransactionamount
FROM (	
		SELECT
		srp_erp_customerinvoicemaster.salesPersonID,
		\"0\" AS contractAutoID,
		srp_erp_customerinvoicemaster.invoiceAutoID,
		\"0\" as DOAutoID,
		\"0\" AS contractvalue,
		sum( srp_erp_customerinvoicedetails.transactionAmount ) / srp_erp_customerinvoicemaster.companyLocalExchangeRate AS invoicelocalmamount,
		sum( srp_erp_customerinvoicedetails.transactionAmount ) / srp_erp_customerinvoicemaster.companyReportingExchangeRate AS invoicereportingamount,
		sum( srp_erp_customerinvoicedetails.transactionAmount ) / srp_erp_customerinvoicemaster.transactionExchangeRate AS invoicetransactionamount,
		0 AS contractmastertransactionamount,
		0 AS contractmasterreportingexchange,
		0 AS contractmasterlocalexchange,
		srp_erp_customerinvoicemaster.transactionCurrencyDecimalPlaces AS invoicetransactionCurrencyDecimalPlaces,
		srp_erp_customerinvoicemaster.companyLocalCurrencyDecimalPlaces AS invoicecompanyLocalCurrencyDecimalPlaces,
		srp_erp_customerinvoicemaster.companyReportingCurrencyDecimalPlaces AS invoicecompanyReportingCurrencyDecimalPlaces,
		0 AS contracttransactionCurrencyDecimalPlaces,
		0 AS contractLocalCurrencyDecimalPlaces,
		0 AS contractReportingCurrencyDecimalPlaces,
		salesperson.SalesPersonName AS salesPersonName,
		srp_erp_customerinvoicemaster.transactionCurrency,
		srp_erp_customerinvoicemaster.companyLocalCurrency,
		srp_erp_customerinvoicemaster.companyReportingCurrency,
		2 AS docTye ,
		0 AS dolocalmamount,
					0 AS doreportingamount,
					0 AS dotransactionamount
				
	FROM
		`srp_erp_customerinvoicemaster`
		LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
		LEFT join srp_erp_contractmaster on srp_erp_contractmaster.contractAutoID=srp_erp_customerinvoicedetails.contractAutoID
		JOIN srp_erp_salespersonmaster salesperson ON salesperson.salesPersonID = srp_erp_customerinvoicemaster.salesPersonID 
	WHERE
		srp_erp_customerinvoicemaster.companyID = $companyid 
		AND invoiceType!='DeliveryOrder'
		AND srp_erp_customerinvoicemaster.approvedYN = 1 
		$date
		AND srp_erp_customerinvoicemaster.salesPersonID IN (" . join(',', $salesperson) . ") 
		
		AND srp_erp_customerinvoicemaster.salesPersonID IS NOT NULL 
		AND (
				( (srp_erp_customerinvoicedetails.contractAutoID IS NULL) or ( srp_erp_customerinvoicemaster.invoiceType='Quotation')  )
				OR ( srp_erp_customerinvoicedetails.contractAutoID NOT IN (
								SELECT
									conatractmaster.contractAutoID 
								FROM
									srp_erp_contractmaster conatractmaster 
								WHERE
									companyID = $companyid   and  conatractmaster.documentID !='QUT' 
									AND ( ( conatractmaster.salesPersonID IS NOT NULL ) OR ( conatractmaster.salesPersonID = 0 ) ) 
								) 
							) 
		) 
		GROUP BY
		srp_erp_customerinvoicemaster.salesPersonID

		UNION ALL
		
		SELECT
				srp_erp_deliveryorder.salesPersonID,
				\"0\" AS contractAutoID,
				\"0\" AS invoiceAutoID,
				srp_erp_deliveryorder.DOAutoID,
				\"0\" AS contractvalue,
				
					0 AS invoicelocalmamount,
					0 AS invoicereportingamount,
					0 AS invoicetransactionamount,
				
				0 AS contractmastertransactionamount,
				0 AS contractmasterreportingexchange,
				0 AS contractmasterlocalexchange,
				srp_erp_deliveryorder.transactionCurrencyDecimalPlaces AS invoicetransactionCurrencyDecimalPlaces,
				srp_erp_deliveryorder.companyLocalCurrencyDecimalPlaces AS invoicecompanyLocalCurrencyDecimalPlaces,
				srp_erp_deliveryorder.companyReportingCurrencyDecimalPlaces AS invoicecompanyReportingCurrencyDecimalPlaces,
				0 AS contracttransactionCurrencyDecimalPlaces,
				0 AS contractLocalCurrencyDecimalPlaces,
				0 AS contractReportingCurrencyDecimalPlaces,
				salespersondo.SalesPersonName AS salesPersonName,
				srp_erp_deliveryorder.transactionCurrency,
				srp_erp_deliveryorder.companyLocalCurrency,
				srp_erp_deliveryorder.companyReportingCurrency,
				2 AS docTye ,
				sum( srp_erp_deliveryorderdetails.transactionAmount ) / srp_erp_deliveryorder.companyLocalExchangeRate AS dolocalmamount,
				sum( srp_erp_deliveryorderdetails.transactionAmount ) / srp_erp_deliveryorder.companyReportingExchangeRate AS doreportingamount,
				sum( srp_erp_deliveryorderdetails.transactionAmount ) / srp_erp_deliveryorder.transactionExchangeRate AS dotransactionamount
				
				
			FROM
				`srp_erp_deliveryorder`
				LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID
				LEFT join srp_erp_contractmaster on srp_erp_contractmaster.contractAutoID=srp_erp_deliveryorderdetails.contractAutoID
				JOIN srp_erp_salespersonmaster salespersondo ON salespersondo.salesPersonID = srp_erp_deliveryorder.salesPersonID 
			WHERE
				srp_erp_deliveryorder.companyID = $companyid 
				AND srp_erp_deliveryorder.approvedYN = 1 
				$datedo
				AND srp_erp_deliveryorder.salesPersonID IN (" . join(',', $salesperson) . ") 
				AND srp_erp_deliveryorder.salesPersonID IS NOT NULL 
				AND (
						( (srp_erp_deliveryorderdetails.contractAutoID IS NULL) or ( srp_erp_deliveryorder.DOType='Quotation')  )
						OR (
							srp_erp_deliveryorderdetails.contractAutoID NOT IN (
									SELECT
										conatractmaster.contractAutoID 
									FROM
										srp_erp_contractmaster conatractmaster 
									WHERE
										companyID = $companyid  and  conatractmaster.documentID !='QUT' 
										AND ( ( conatractmaster.salesPersonID IS NOT NULL ) OR ( conatractmaster.salesPersonID = 0 ) ) 
										) 
								) 
					) 
			GROUP BY
				srp_erp_deliveryorder.salesPersonID )t2
				GROUP BY
				t2.salesPersonID
		
	) t1 
	GROUP BY
	t1.salesPersonID,t1.docTye";


        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_sales_preformance_dd()
    {
        $salesPersonID = $this->input->post('salesPersonID');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        $datecontract = "";
        $datedo = "";
        $companyid = current_companyID();
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
        }
        if (!empty($datefrom) && !empty($dateto)) {
            $datecontract .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 00:00:00')";
        }
        if (!empty($datefrom) && !empty($dateto)) {
            $datedo .= " AND ( DODate >= '" . $datefromconvert . " 00:00:00' AND DODate <= '" . $datetoconvert . " 00:00:00')";
        }
        /*  $qry = "SELECT
                            invoicemaster.salesPersonID,
                            contract.contractAutoID,
                            invoicemaster.customerName as customerName,
                            invoicemaster.invoiceCode as docsyscodeinvoice,
                            invoicemaster.documentID AS invoicedocid,
                            contractmaster.contractCode as docsyscodecontract,
                            contractmaster.documentID AS contractdocid,
                            DATE_FORMAT(invoicemaster.invoiceDate,'" . $convertFormat . "') AS docdateinvoice,
                            DATE_FORMAT(contractmaster.contractDate,'" . $convertFormat . "') AS docdatecontract,
                            invoicemaster.invoiceAutoID,
                            IFNULL(contract.contractamount,0) contractvalue,
                            sum( invoicedetail.transactionAmount ) / invoicemaster.companyLocalExchangeRate AS invoicelocalmamount,
                            sum( invoicedetail.transactionAmount ) / invoicemaster.companyReportingExchangeRate AS invoicereportingamount,
                            sum( invoicedetail.transactionAmount ) / invoicemaster.transactionExchangeRate AS invoicetransactionamount,
                            contract.contractamount  / contractmaster.transactionExchangeRate AS contractmastertransactionamount,
                            contract.contractamount  / contractmaster.companyReportingExchangeRate AS contractmasterreportingexchange,
                            contract.contractamount / contractmaster.companyLocalExchangeRate AS contractmasterlocalexchange,
                            invoicemaster.transactionCurrencyDecimalPlaces AS invoicetransactionCurrencyDecimalPlaces,
                            invoicemaster.companyLocalCurrencyDecimalPlaces AS invoicecompanyLocalCurrencyDecimalPlaces,
                            invoicemaster.companyReportingCurrencyDecimalPlaces AS invoicecompanyReportingCurrencyDecimalPlaces,
                            contractmaster.transactionCurrencyDecimalPlaces AS contracttransactionCurrencyDecimalPlaces,
                            contractmaster.companyLocalCurrencyDecimalPlaces AS contractLocalCurrencyDecimalPlaces,
                            contractmaster.companyReportingCurrencyDecimalPlaces AS contractReportingCurrencyDecimalPlaces,
                            salesperson.SalesPersonName AS salesPersonName,
                            invoicemaster.transactionCurrency,
                            invoicemaster.companyLocalCurrency,
                            invoicemaster.companyReportingCurrency
                        FROM
                            srp_erp_customerinvoicedetails invoicedetail
                            LEFT JOIN srp_erp_customerinvoicemaster invoicemaster ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID
                            JOIN srp_erp_salespersonmaster salesperson ON salesperson.salesPersonID = invoicemaster.salesPersonID
                            JOIN ( SELECT contractAutoID, sum( transactionAmount ) AS contractamount FROM srp_erp_contractdetails GROUP BY contractAutoID ) contract ON contract.contractAutoID = invoicedetail.contractAutoID
                            LEFT JOIN srp_erp_contractmaster contractmaster ON contractmaster.contractAutoID = contract.contractAutoID
                        WHERE
                            invoicemaster.companyID = $companyid
                            AND invoicemaster.approvedYN = 1
                            AND contractmaster.approvedYN = 1
                            AND invoicemaster.salesPersonID = $salesPersonID
                            $date
                            $datecontract
                            AND invoicemaster.salesPersonID IS NOT NULL
                        GROUP BY
                            invoicedetail.contractAutoID,invoicedetail.invoiceAutoID UNION
                            SELECT
                            contractmaster.salesPersonID,
                            contractmaster.contractAutoID,
                            contractmaster.customerName as customerName,
                            \" - \" as docsyscodeinvoice,
                            \" - \" as invoicedocid,
                            contractmaster.contractCode as docsyscodecontract,
                            contractmaster.documentID as documentID,
                            \" - \"  as docdateinvoice,
                            DATE_FORMAT(contractmaster.contractDate,'" . $convertFormat . "') AS docdatecontract,
                            \"0\" AS invoiceAutoID,
                            IFNULL( sum( contractdetail.transactionAmount ), 0 ) AS contractvalue,
                            \"0\" AS invoicelocalmamount,
                            \"0\" AS invoicereportingamount,
                            \"0\" AS invoicetransactionamount,
                            sum( contractdetail.transactionAmount ) / contractmaster.transactionExchangeRate AS contractmastertransactionamount,
                            sum( contractdetail.transactionAmount ) / contractmaster.companyReportingExchangeRate AS contractmasterreportingexchange,
                            sum( contractdetail.transactionAmount ) / contractmaster.companyLocalExchangeRate AS contractmasterlocalexchange,
                            \"0\" AS invoicetransactionCurrencyDecimalPlaces,
                            \"0\" AS invoicecompanyLocalCurrencyDecimalPlaces,
                            \"0\" AS invoicecompanyReportingCurrencyDecimalPlaces,
                            contractmaster.transactionCurrencyDecimalPlaces AS contracttransactionExchangeRate,
                            contractmaster.companyLocalCurrencyDecimalPlaces AS contractLocalCurrencyDecimalPlaces,
                            contractmaster.companyReportingCurrencyDecimalPlaces AS contractReportingCurrencyDecimalPlaces,
                            salesperson.SalesPersonName AS salesPersonName,
                            contractmaster.transactionCurrency,
                            contractmaster.companyLocalCurrency,
                            contractmaster.companyReportingCurrency
                        FROM
                            srp_erp_contractdetails contractdetail
                            LEFT JOIN srp_erp_contractmaster contractmaster ON contractmaster.contractAutoID = contractdetail.contractAutoID
                            JOIN srp_erp_salespersonmaster salesperson ON salesperson.salesPersonID = contractmaster.salesPersonID
                        WHERE
                            contractmaster.companyID = $companyid
                            AND contractmaster.approvedYN = 1
                            AND contractmaster.salesPersonID = $salesPersonID
                            $datecontract
                            AND contractmaster.salesPersonID IS NOT NULL
                            AND contractmaster.contractAutoID NOT IN (
                        SELECT
                            ifnull( contractAutoID, 0 )
                        FROM
                            srp_erp_customerinvoicedetails invoicedetail
                            JOIN srp_erp_customerinvoicemaster invoicemaster ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID
                        WHERE
                            invoicemaster.companyID = $companyid
                        GROUP BY
                            contractdetail.contractAutoID
                            ) GROUP BY
                            contractdetail.contractAutoID";*/
        $qry = "SELECT
	invoicedetailmaster.salesPersonID,
	conatractmaster.contractAutoID,
	srp_erp_contractdetails.contractDetailsAutoID,
	conatractmaster.customerName AS customerName,
	invoicedetailmaster.invoiceCode AS docsyscodeinvoice,
	invoicedetailmaster.documentID AS invoicedocid,
	conatractmaster.contractCode AS docsyscodecontract,
	conatractmaster.documentID AS contractdocid,
	DATE_FORMAT( invoicedetailmaster.invoiceDate, '%d-%m-%Y' ) AS docdateinvoice,
	DATE_FORMAT( conatractmaster.contractDate, '%d-%m-%Y' ) AS docdatecontract,
	invoicedetailmaster.invoiceAutoID,
	SUM(IFNULL( conatractmaster.transactionAmount, 0 )) contractvalue,
	SUM(( invoicedetailmaster.transactionAmount ) / invoicedetailmaster.companyLocalExchangeRate) AS invoicelocalmamount,
	SUM(( invoicedetailmaster.transactionAmount ) / invoicedetailmaster.companyReportingExchangeRate) AS invoicereportingamount,
	SUM(( invoicedetailmaster.transactionAmount ) / invoicedetailmaster.transactionExchangeRate) AS invoicetransactionamount,
	(conatractmaster.transactionAmount / conatractmaster.transactionExchangeRate) AS contractmastertransactionamount,
	(conatractmaster.transactionAmount / conatractmaster.companyReportingExchangeRate) AS contractmasterreportingexchange,
	(conatractmaster.transactionAmount / conatractmaster.companyLocalExchangeRate) AS contractmasterlocalexchange,
	conatractmaster.transactionCurrencyDecimalPlaces AS invoicetransactionCurrencyDecimalPlaces,
	conatractmaster.companyLocalCurrencyDecimalPlaces AS invoicecompanyLocalCurrencyDecimalPlaces,
	conatractmaster.companyReportingCurrencyDecimalPlaces AS invoicecompanyReportingCurrencyDecimalPlaces,
	conatractmaster.transactionCurrencyDecimalPlaces AS contracttransactionCurrencyDecimalPlaces,
	conatractmaster.companyLocalCurrencyDecimalPlaces AS contractLocalCurrencyDecimalPlaces,
	conatractmaster.companyReportingCurrencyDecimalPlaces AS contractReportingCurrencyDecimalPlaces,
	salespersoncontract.SalesPersonName AS salesPersonName,
	conatractmaster.transactionCurrency,
	conatractmaster.companyLocalCurrency,
	conatractmaster.companyReportingCurrency 
FROM
	srp_erp_contractdetails
	LEFT JOIN srp_erp_contractmaster conatractmaster ON conatractmaster.contractAutoID = srp_erp_contractdetails.contractAutoID
	LEFT JOIN (
SELECT
	invoicedetail.transactionAmount,
	invoicedetail.contractDetailsAutoID,
	invoicemaster.companyLocalExchangeRate,
	invoicemaster.companyReportingExchangeRate,
	invoicemaster.transactionExchangeRate,
	invoicemaster.invoiceAutoID,
	invoicemaster.transactionCurrencyDecimalPlaces,
	invoicemaster.companyLocalCurrencyDecimalPlaces,
	invoicemaster.companyReportingCurrencyDecimalPlaces,
	invoicemaster.transactionCurrency,
	invoicemaster.companyLocalCurrency,
	invoicemaster.companyReportingCurrency,
	invoicemaster.salesPersonID,
	invoicemaster.customerName,
	invoicemaster.invoiceCode,
	invoicemaster.documentID,
	invoicemaster.invoiceDate
FROM
	srp_erp_customerinvoicedetails invoicedetail
	LEFT JOIN srp_erp_customerinvoicemaster invoicemaster ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID 
WHERE
	invoicedetail.companyID = $companyid 
	AND invoicemaster.approvedYN = 1 
    $date
	
	) invoicedetailmaster ON invoicedetailmaster.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID
	
	JOIN srp_erp_salespersonmaster salespersoncontract ON salespersoncontract.salesPersonID = conatractmaster.salesPersonID 
WHERE
	srp_erp_contractdetails.companyID = $companyid 
	AND conatractmaster.approvedYN = 1 

	$datecontract
	AND conatractmaster.salesPersonID = $salesPersonID
	AND ( ( conatractmaster.salesPersonID IS NOT NULL ) OR ( conatractmaster.salesPersonID = 0 ) ) 
	AND conatractmaster.documentID not in ( 'QUT')
	GROUP BY
	    contractAutoID

		UNION ALL


		SELECT
	conatractmaster.salesPersonID,
	conatractmaster.contractAutoID,
	srp_erp_contractdetails.contractDetailsAutoID,
	conatractmaster.customerName AS customerName,
	dodetailmaster.DOCode AS docsyscodeinvoice,
	dodetailmaster.documentID AS invoicedocid,
	conatractmaster.contractCode AS docsyscodecontract,
	conatractmaster.documentID AS contractdocid,
	DATE_FORMAT( dodetailmaster.DODate, '%d-%m-%Y' ) AS docdateinvoice,
	DATE_FORMAT( conatractmaster.contractDate, '%d-%m-%Y' ) AS docdatecontract,
	dodetailmaster.DOAutoID,
	SUM(IFNULL( conatractmaster.transactionAmount, 0 )) contractvalue,
	SUM(( dodetailmaster.transactionAmount ) / dodetailmaster.companyLocalExchangeRate) AS invoicelocalmamount,
	SUM(( dodetailmaster.transactionAmount ) / dodetailmaster.companyReportingExchangeRate) AS invoicereportingamount,
	SUM(( dodetailmaster.transactionAmount ) / dodetailmaster.transactionExchangeRate) AS invoicetransactionamount,
	(conatractmaster.transactionAmount / conatractmaster.transactionExchangeRate) AS contractmastertransactionamount,
	(conatractmaster.transactionAmount / conatractmaster.companyReportingExchangeRate) AS contractmasterreportingexchange,
	(conatractmaster.transactionAmount / conatractmaster.companyLocalExchangeRate) AS contractmasterlocalexchange,
	conatractmaster.transactionCurrencyDecimalPlaces AS invoicetransactionCurrencyDecimalPlaces,
	conatractmaster.companyLocalCurrencyDecimalPlaces AS invoicecompanyLocalCurrencyDecimalPlaces,
	conatractmaster.companyReportingCurrencyDecimalPlaces AS invoicecompanyReportingCurrencyDecimalPlaces,
	conatractmaster.transactionCurrencyDecimalPlaces AS contracttransactionCurrencyDecimalPlaces,
	conatractmaster.companyLocalCurrencyDecimalPlaces AS contractLocalCurrencyDecimalPlaces,
	conatractmaster.companyReportingCurrencyDecimalPlaces AS contractReportingCurrencyDecimalPlaces,
	salespersoncontract.SalesPersonName AS salesPersonName,
	conatractmaster.transactionCurrency,
	conatractmaster.companyLocalCurrency,
	conatractmaster.companyReportingCurrency 
FROM
	srp_erp_contractdetails
	LEFT JOIN srp_erp_contractmaster conatractmaster ON conatractmaster.contractAutoID = srp_erp_contractdetails.contractAutoID
	LEFT JOIN (
SELECT
	dodetail.transactionAmount,
	dodetail.contractDetailsAutoID,
	domaster.companyLocalExchangeRate,
	domaster.companyReportingExchangeRate,
	domaster.transactionExchangeRate,
	domaster.DOAutoID,
	domaster.companyLocalCurrencyDecimalPlaces,
	domaster.companyReportingCurrencyDecimalPlaces,
	domaster.transactionCurrency,
	domaster.companyLocalCurrency,
	domaster.companyReportingCurrency,
	domaster.salesPersonID,
	domaster.customerName,
	domaster.DOCode,
	domaster.documentID,
	domaster.DODate
FROM
	srp_erp_deliveryorderdetails dodetail
	LEFT JOIN srp_erp_deliveryorder domaster ON domaster.DOAutoID = dodetail.DOAutoID 
WHERE
dodetail.companyID = $companyid 
	AND domaster.approvedYN = 1 
    $datedo
	) dodetailmaster ON dodetailmaster.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID
	
	
	JOIN srp_erp_salespersonmaster salespersoncontract ON salespersoncontract.salesPersonID = conatractmaster.salesPersonID 
WHERE
	srp_erp_contractdetails.companyID = $companyid 
	AND conatractmaster.approvedYN = 1 

	$datecontract
	AND conatractmaster.salesPersonID = $salesPersonID
	AND ( ( conatractmaster.salesPersonID IS NOT NULL ) OR ( conatractmaster.salesPersonID = 0 ) ) 
	AND conatractmaster.documentID not in ( 'QUT')
	GROUP BY
	    contractAutoID";

        $output = $this->db->query($qry)->result_array();
        return $output;

    }

    function get_itemwise_sales_report($datearr)
    {
        $location = $this->input->post("location");
        $financeyear = $this->input->post("financeyear");
        $items = $this->input->post("itemTo");
        $groupBy = $this->input->post("groupBy");

        $i = 1;
        $itmesOR = '( ';
        if (!empty($items)) {
            foreach ($items as $item_val) {
                if ($i != 1) {
                    $itmesOR .= ' OR ';
                }
                $itmesOR .= " ledger.itemAutoID = '" . $item_val . "' "; /*generate the query according to selectd items*/
                $i++;
            }
        }
        $itmesOR .= ' ) ';

        $sumamount = '';
        foreach ($datearr as $key => $val) {
            $sumamount .= " SUM(IF(documentDate='$key',total_value, 0)) as '$val' ,";
        }

        /*To generate item wise item category wise item sub category wise sales rep wise customer category wise area wise Report*/
        $select = '';
        $group = '';
        $join = '';
        if ($groupBy == 'item') {
            $select = "itemAutoID as groupID, CONCAT(ledger.itemSystemCode, ' | ',ledger.itemDescription) as ";
        } else if ($groupBy == 'category') {
            $select = "mainCategoryID as groupID, srp_erp_itemmaster.mainCategory as ";
            $join = "LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID";
        } else if ($groupBy == 'subCategory') {
            $select = "srp_erp_itemmaster.subcategoryID as groupID, srp_erp_itemcategory.description as ";
            $join = "LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID
                     LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subcategoryID";
        } else if ($groupBy == 'subSubCategory') {
            $select = "srp_erp_itemmaster.subSubCategoryID as groupID, srp_erp_itemcategory.description as ";
            $join = "LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID
                     LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subSubCategoryID";
        } else if ($groupBy == 'cusArea') {
            $select = "srp_erp_customermaster.locationID as groupID, srp_erp_buyback_locations.description as ";
            $join = "LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = ledger.documentAutoID
                     LEFT JOIN srp_erp_customermaster ON srp_erp_customerinvoicemaster.customerID = srp_erp_customermaster.customerAutoID
                     LEFT JOIN srp_erp_buyback_locations ON srp_erp_buyback_locations.locationID = srp_erp_customermaster.locationID";
        } else if ($groupBy == 'cusCategory') {
            $select = "srp_erp_customermaster.partyCategoryID as groupID, srp_erp_partycategories.categoryDescription as ";
            $join = "LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = ledger.documentAutoID
                     LEFT JOIN srp_erp_customermaster ON srp_erp_customerinvoicemaster.customerID = srp_erp_customermaster.customerAutoID
                     LEFT JOIN srp_erp_partycategories ON srp_erp_partycategories.partyCategoryID = srp_erp_customermaster.partyCategoryID";
        } else if ($groupBy == 'salesRep') {
            $select = "srp_erp_customerinvoicemaster.salesPersonID as groupID, CONCAT(srp_erp_customerinvoicemaster.SalesPersonCode, ' | ', srp_erp_salespersonmaster.SalesPersonName ) as ";
            $join = "LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = ledger.documentAutoID
                     LEFT JOIN srp_erp_salespersonmaster ON srp_erp_salespersonmaster.salesPersonID = srp_erp_customerinvoicemaster.salesPersonID";
        }

        $result = $this->db->query("SELECT
$sumamount
 groupID,
  description,
  defaultUOM
  FROM
  (
  SELECT 
      ledger.itemAutoID, ledger.defaultUOM, $select description,
      DATE_FORMAT(ledger.documentDate, '%Y-%m') AS documentDate,
      (ledger.transactionQTY/ledger.convertionRate) as total_value
      
      FROM srp_erp_itemledger ledger
      $join      
      WHERE  $itmesOR AND ledger.companyFinanceYearID = $financeyear
             AND ledger.companyID = " . $this->common_data['company_data']['company_id'] . " AND ledger.wareHouseAutoID IN (" . join(',', $location) . ")
             AND (ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV')
  )tble
  GROUP BY groupID")->result_array();

        return $result;
    }

    function get_itemwise_sales_drilldown_report()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $location = $this->input->post("location");
        $groupBy = $this->input->post("groupBy");
        $datefrm = $this->input->post('date');
        $datefromconvert = $datefrm . '-01';
        $datetoconvert = $datefrm . '-31';

        $date = "";
        $date .= " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 00:00:00')";

        $select = '';
        $join = '';
        if ($groupBy == 'item') {
            $select = " AND ledger.itemAutoID = $itemAutoID";
        } else if ($groupBy == 'category') {
            $select = " AND mainCategoryID = $itemAutoID";
            $join = "LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID";
        } else if ($groupBy == 'subCategory') {
            $select = " AND srp_erp_itemmaster.subcategoryID = $itemAutoID";
            $join = "LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID
                     LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subcategoryID";
        } else if ($groupBy == 'subSubCategory') {
            $select = " AND srp_erp_itemmaster.subSubCategoryID = $itemAutoID";
            $join = "LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID
                     LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subSubCategoryID";
        } else if ($groupBy == 'cusArea') {
            $select = " AND srp_erp_customermaster.locationID = $itemAutoID";
            $join = "LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = ledger.documentAutoID
                     LEFT JOIN srp_erp_customermaster ON srp_erp_customerinvoicemaster.customerID = srp_erp_customermaster.customerAutoID
                     LEFT JOIN srp_erp_buyback_locations ON srp_erp_buyback_locations.locationID = srp_erp_customermaster.locationID";
        } else if ($groupBy == 'cusCategory') {
            $select = " AND srp_erp_customermaster.partyCategoryID = $itemAutoID";
            $join = "LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = ledger.documentAutoID
                     LEFT JOIN srp_erp_customermaster ON srp_erp_customerinvoicemaster.customerID = srp_erp_customermaster.customerAutoID
                     LEFT JOIN srp_erp_partycategories ON srp_erp_partycategories.partyCategoryID = srp_erp_customermaster.partyCategoryID";
        } else if ($groupBy == 'salesRep') {
            $select = " AND srp_erp_customerinvoicemaster.salesPersonID = $itemAutoID";
            $join = "LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = ledger.documentAutoID";
        }

        $result = $this->db->query("SELECT
  ledger.documentSystemCode,
  ledger.documentAutoID,
  ledger.documentDate,
  ledger.transactionQTY,
  ledger.itemAutoID,
  ledger.itemSystemCode,
  ledger.itemDescription,
  ledger.documentID,
  ledger.wareHouseDescription,
  ledger.wareHouseLocation,
  ledger.defaultUOM
      FROM srp_erp_itemledger ledger $join
      WHERE 
             ledger.companyID = " . $this->common_data['company_data']['company_id'] . " AND ledger.wareHouseAutoID IN (" . join(',', $location) . ")
             AND (ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV') $date $select ORDER BY itemLedgerAutoID ASC ")->result_array();

        return $result;
    }

    function get_sales_preformance_dd_so()
    {
        $salesPersonID = $this->input->post('salesPersonID');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        $datecontract = "";
        $datedo = "";
        $companyid = current_companyID();
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
        }
        if (!empty($datefrom) && !empty($dateto)) {
            $datecontract .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 00:00:00')";
        }
        if (!empty($datefrom) && !empty($dateto)) {
            $datedo .= " AND ( DODate >= '" . $datefromconvert . " 00:00:00' AND DODate <= '" . $datetoconvert . " 00:00:00')";
        }

        $qry = " SELECT
	invoicemaster.salesPersonID,
	\"\" AS contractAutoID,
	invoicemaster.customerID,
	invoicemaster.customerName AS customerName,
	invoicemaster.invoiceCode AS docsyscodeinvoice,
	invoicemaster.documentID AS invoicedocid,
	\"\" AS docsyscodecontract,
	\"\" AS contractdocid,
	DATE_FORMAT( invoicemaster.invoiceDate, '%d-%m-%Y' ) AS docdateinvoice,
	\"\" AS docdatecontract,
	invoicemaster.invoiceAutoID,
	\"\" AS contractvalue,
	SUM(( invoicedetail.transactionAmount ) / invoicemaster.companyLocalExchangeRate) AS invoicelocalmamount,
	SUM(( invoicedetail.transactionAmount ) / invoicemaster.companyReportingExchangeRate) AS invoicereportingamount,
	SUM(( invoicedetail.transactionAmount ) / invoicemaster.transactionExchangeRate) AS invoicetransactionamount,
	\" \" AS contractmastertransactionamount,
	\" \" AS contractmasterreportingexchange,
	\" \" AS contractmasterlocalexchange,
	invoicemaster.transactionCurrencyDecimalPlaces AS invoicetransactionCurrencyDecimalPlaces,
	invoicemaster.companyLocalCurrencyDecimalPlaces AS invoicecompanyLocalCurrencyDecimalPlaces,
	invoicemaster.companyReportingCurrencyDecimalPlaces AS invoicecompanyReportingCurrencyDecimalPlaces,
	\"\" AS contracttransactionCurrencyDecimalPlaces,
	\" \" AS contractLocalCurrencyDecimalPlaces,
	\"\" AS contractReportingCurrencyDecimalPlaces,
	salesperson.SalesPersonName AS salesPersonName,
	invoicemaster.transactionCurrency,
	invoicemaster.companyLocalCurrency,
	invoicemaster.companyReportingCurrency 
FROM
	srp_erp_customerinvoicemaster invoicemaster
	LEFT JOIN srp_erp_customerinvoicedetails invoicedetail ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID
	LEFT join srp_erp_contractmaster on srp_erp_contractmaster.contractAutoID=invoicedetail.contractAutoID
	
	JOIN srp_erp_salespersonmaster salesperson ON salesperson.salesPersonID = invoicemaster.salesPersonID 
WHERE
	invoicemaster.companyID = $companyid 
	AND invoicemaster.approvedYN = 1 
	$date
	AND invoicemaster.salesPersonID = $salesPersonID
	AND invoicemaster.salesPersonID IS NOT NULL 
	AND (
		-- 	( invoicedetail.contractAutoID IS NULL ) 
		-- 	OR (
		-- 	invoicedetail.contractAutoID NOT IN (
		-- SELECT
		-- 	conatractmaster.contractAutoID 
		-- FROM
		-- 	srp_erp_contractmaster conatractmaster 
		-- WHERE
		-- 	companyID = $companyid 
		-- 	AND ( ( conatractmaster.salesPersonID IS NOT NULL ) OR ( conatractmaster.salesPersonID = 0 ) ) 
		-- 	) 
		-- 	) 
		( (invoicedetail.contractAutoID IS NULL) or ( invoicemaster.invoiceType='Quotation')  )
				OR ( invoicedetail.contractAutoID NOT IN (
								SELECT
									conatractmaster.contractAutoID 
								FROM
									srp_erp_contractmaster conatractmaster 
								WHERE
									companyID = $companyid   and  conatractmaster.documentID !='QUT' 
									AND ( ( conatractmaster.salesPersonID IS NOT NULL ) OR ( conatractmaster.salesPersonID = 0 ) ) 
								) 
							) 
	) 
	GROUP BY invoicemaster.invoiceAutoID

	UNION ALL

	SELECT
	domaster.salesPersonID,
	\"\" AS contractAutoID,
	domaster.customerID,
	domaster.customerName AS customerName,
	domaster.DOCode AS docsyscodeinvoice,
	domaster.documentID AS invoicedocid,
	\"\" AS docsyscodecontract,
	\"\" AS contractdocid,
	DATE_FORMAT( domaster.DODate, '%d-%m-%Y' ) AS docdateinvoice,
	\"\" AS docdatecontract,
	domaster.DOAutoID,
	\"\" AS contractvalue,
	SUM(( dodetail.transactionAmount ) / domaster.companyLocalExchangeRate) AS invoicelocalmamount,
	SUM(( dodetail.transactionAmount ) / domaster.companyReportingExchangeRate) AS invoicereportingamount,
	SUM(( dodetail.transactionAmount ) / domaster.transactionExchangeRate) AS invoicetransactionamount,
	\" \" AS contractmastertransactionamount,
	\" \" AS contractmasterreportingexchange,
	\" \" AS contractmasterlocalexchange,
	domaster.transactionCurrencyDecimalPlaces AS invoicetransactionCurrencyDecimalPlaces,
	domaster.companyLocalCurrencyDecimalPlaces AS invoicecompanyLocalCurrencyDecimalPlaces,
	domaster.companyReportingCurrencyDecimalPlaces AS invoicecompanyReportingCurrencyDecimalPlaces,
	\"\" AS contracttransactionCurrencyDecimalPlaces,
	\" \" AS contractLocalCurrencyDecimalPlaces,
	\"\" AS contractReportingCurrencyDecimalPlaces,
	salesperson.SalesPersonName AS salesPersonName,
	domaster.transactionCurrency,
	domaster.companyLocalCurrency,
	domaster.companyReportingCurrency 
FROM
	srp_erp_deliveryorder domaster
	LEFT JOIN srp_erp_deliveryorderdetails dodetail ON domaster.DOAutoID = dodetail.DOAutoID
	
	JOIN srp_erp_salespersonmaster salesperson ON salesperson.salesPersonID = domaster.salesPersonID 
WHERE
	domaster.companyID = $companyid 
	AND domaster.approvedYN = 1 
	$datedo
	AND domaster.salesPersonID = $salesPersonID
	AND domaster.salesPersonID IS NOT NULL 
	AND (
	-- 	( dodetail.contractAutoID IS NULL ) 
	-- 	OR (
	-- 	dodetail.contractAutoID NOT IN (
	-- 		SELECT
	-- 			conatractmaster.contractAutoID 
	-- 		FROM
	-- 			srp_erp_contractmaster conatractmaster 
	-- 		WHERE
	-- 			companyID = $companyid 
	-- 			AND ( ( conatractmaster.salesPersonID IS NOT NULL ) OR ( conatractmaster.salesPersonID = 0 ) ) 
	-- 	) 
	--    ) 
	( (dodetail.contractAutoID IS NULL) or ( domaster.DOType='Quotation')  )
						OR (
							dodetail.contractAutoID NOT IN (
									SELECT
										conatractmaster.contractAutoID 
									FROM
										srp_erp_contractmaster conatractmaster 
									WHERE
										companyID = $companyid  and  conatractmaster.documentID !='QUT' 
										AND ( ( conatractmaster.salesPersonID IS NOT NULL ) OR ( conatractmaster.salesPersonID = 0 ) ) 
										) 
								) 
	) 
	GROUP BY domaster.DOAutoID

";
        $output = $this->db->query($qry)->result_array();
//        echo $this->db->last_query();exit;
        return $output;
    }

    function get_erp_weeklySalesIncome_category()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $datafilter2 = $this->input->post("datafilter2");

        $mainCategoryID = $this->input->post("mainCategoryID");
        $subcategoryID = $this->input->post("subcategoryID");
        $itemAutoID = $this->input->post("itemAutoID");
        $cusCategory = $this->input->post("cusCategory");
        $customerID = $this->input->post("customerID");
        $area = $this->input->post("area");
        $categoryarr = "";

        if ($datafilter2 == 1) {
            /*Item Wise Header*/
            $categoryarr = $this->db->query("SELECT DISTINCT
	IFNULL( item.itemDescription, 'Others' ) AS description,
	IFNULL( item.ItemAutoID, '' ) AS groupID 
FROM
	srp_erp_itemmaster item
WHERE
	item.companyID =  {$companyID}
	AND item.mainCategoryID IN (" . join(',', $mainCategoryID) . ") AND item.subcategoryID IN (" . join(',', $subcategoryID) . ") AND item.itemAutoID IN (" . join(',', $itemAutoID) . ")
	ORDER BY item.ItemAutoID ASC ")->result_array();

        } else if ($datafilter2 == 2) {
            /*Item Category Wise Header*/
            $categoryarr = $this->db->query("SELECT DISTINCT
	IFNULL( category.description, 'Others' ) AS description,
	IFNULL( item.subcategoryID, '' ) AS groupID 
FROM
	srp_erp_itemmaster item LEFT JOIN srp_erp_itemcategory category ON category.itemCategoryID = item.subcategoryID
WHERE
	item.companyID = {$companyID} 
	AND item.mainCategoryID IN (" . join(',', $mainCategoryID) . ") AND item.subcategoryID IN (" . join(',', $subcategoryID) . ")
	ORDER BY item.subcategoryID ASC")->result_array();

        } else if ($datafilter2 == 3) {
            /*Area Wise Header*/
            $categoryarr = $this->db->query("SELECT DISTINCT
	locations.description AS description,
	customer.subLocationID AS groupID 
FROM
	srp_erp_customermaster customer
	LEFT JOIN srp_erp_buyback_locations locations ON locations.locationID = customer.subLocationID 
WHERE
	customer.companyID = {$companyID} 
	AND customer.locationID IN (" . join(',', $area) . ") AND customer.subLocationID IS NOT NULL")->result_array();

        } else if ($datafilter2 == 4) {
            /*Customer Wise Header*/
            $categoryarr = $this->db->query("SELECT DISTINCT
	IFNULL( customer.customerName , 'Others' ) AS description,
	IFNULL( customer.customerAutoID, '' ) AS groupID 
FROM
	srp_erp_customermaster customer
WHERE
	customer.companyID = {$companyID} AND customer.partyCategoryID IN (" . join(',', $cusCategory) . ")
	AND customer.customerAutoID IN (" . join(',', $customerID) . ") ")->result_array();

        } else if ($datafilter2 == 5) {
            /*Customer Category Wise Header*/
            $categoryarr = $this->db->query("SELECT DISTINCT
	IFNULL( srp_erp_partycategories.categoryDescription, 'Others' ) AS description,
	IFNULL( customer.partyCategoryID, '' ) AS groupID 
FROM
	srp_erp_customermaster customer LEFT JOIN srp_erp_partycategories ON customer.partyCategoryID = srp_erp_partycategories.partyCategoryID
WHERE
	customer.companyID = {$companyID} 
	AND customer.partyCategoryID IN (" . join(',', $cusCategory) . ")
	AND customer.customerAutoID IN (" . join(',', $customerID) . ") ")->result_array();

        }
//        echo '<pre>'; print_r($categoryarr); echo '</pre>';
        return $categoryarr;
    }

    function get_erp_weeklySalesIncome_report()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $dataFilter = $this->input->post("dataFilter");
        $dataFilter2 = $this->input->post("datafilter2");
        $location = $this->input->post("location");
        $cusCategory = $this->input->post("cusCategory");
        $customerID = $this->input->post("customerID");
        $area = $this->input->post("area");
        $mainCategoryID = $this->input->post("mainCategoryID");
        $subcategoryID = $this->input->post("subcategoryID");
        $itemAutoID = $this->input->post("itemAutoID");

        $financeyear = $this->input->post("financeyear");
        $companyFinancePeriodID = $this->input->post("financeyear_period");
        $financePeriodFilt = '';
        $resultdet = '';
        if (!empty($companyFinancePeriodID)) {
            $this->db->select('dateFrom,dateTo');
            $this->db->where('companyFinanceYearID', $financeyear);
            $this->db->where('companyFinancePeriodID', $companyFinancePeriodID);
            $this->db->from('srp_erp_companyfinanceperiod');
            $financePeriod = $this->db->get()->row_array();
            $financePeriodFilt = " AND DATE(ledger.documentDate) BETWEEN '" . $financePeriod['dateFrom'] . "' AND '" . $financePeriod['dateTo'] . "' ";

        }
        $this->db->select('weekID');
        $this->db->where('companyID', $companyID);
        $this->db->where('companyFinanceYearID', $financeyear);
        $this->db->from('srp_erp_financeyearweek');
        $financeyearweekDet = $this->db->get()->result_array();

        if (empty($financeyearweekDet)) {
            $this->db->select('beginingDate,endingDate');
            $this->db->where('companyFinanceYearID', $financeyear);
            $this->db->from('srp_erp_companyfinanceyear');
            $financeyeardtl = $this->db->get()->row_array();

            $beginingDate = $financeyeardtl['beginingDate'];
            $endingDate = $financeyeardtl['endingDate'];
            $firstWeek = date("Y-m-d", strtotime("first monday " . $beginingDate));
            $start = (new DateTime($firstWeek));
            $end = (new DateTime($endingDate));
            $interval = DateInterval::createFromDateString('1 week');
            $period = new DatePeriod($start, $interval, $end);
            $dateinsert = [];
            $key = 0;
            foreach ($period as $dt) {
                $dat = $dt->format("Y-m-d");
                $enddate = strtotime("+6 day", strtotime($dat));
                $dateinsert[$key]['dateFrom'] = $dat;
                $dateinsert[$key]['dateTo'] = date("Y-m-d", $enddate);
                $dateinsert[$key]['description'] = $dt->format("M d") . " to " . date("M d", $enddate);
                $dateinsert[$key]['companyID'] = $this->common_data['company_data']['company_id'];
                $dateinsert[$key]['companyFinanceYearID'] = $financeyear;
                $dateinsert[$key]['timestamp'] = format_date_mysql_datetime();
                $key++;
            }
            $this->db->insert_batch('srp_erp_financeyearweek', $dateinsert);
        }

        if ($dataFilter == 1) {
            if ($dataFilter2 == 1) {
                $resultdet = $this->db->query("SELECT
	groupID,
	SUM(total_value) as total_value,
	weekID,
	weekvalue, 
	transactionCurrencyDecimalPlaces
FROM
	(
SELECT
    (IFNULL(ledger.salesPrice, 1) * ledger.transactionQTY * -1) AS total_value, IFNULL(srp_erp_itemmaster.itemAutoID, 0) AS groupID, srp_erp_itemmaster.itemDescription AS description,
	ledger.transactionCurrencyDecimalPlaces,
	srp_erp_financeyearweek.dateFrom, srp_erp_financeyearweek.weekID, srp_erp_financeyearweek.description as weekvalue
FROM
	srp_erp_itemledger ledger
	INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID
	INNER JOIN srp_erp_financeyearweek On ledger.documentDate BETWEEN srp_erp_financeyearweek.dateFrom AND srp_erp_financeyearweek.dateTo And srp_erp_financeyearweek.companyID = {$companyID}
WHERE
	ledger.companyFinanceYearID = $financeyear 
	AND ledger.companyID = {$companyID}
	AND srp_erp_itemmaster.mainCategoryID IN (" . join(',', $mainCategoryID) . ")
	AND srp_erp_itemmaster.subcategoryID IN (" . join(',', $subcategoryID) . ")
	AND srp_erp_itemmaster.itemAutoID IN (" . join(',', $itemAutoID) . ")
	AND ledger.wareHouseAutoID IN (" . join(',', $location) . ") 
	AND ( ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV' ) $financePeriodFilt
	) tble 
GROUP BY
	groupID,weekvalue
	ORDER BY dateFrom, groupID ASc")->result_array();
                return $resultdet;
                exit;
            } else if ($dataFilter2 == 2) {
                $resultdet = $this->db->query("SELECT
	groupID,
	SUM(total_value) as total_value,
	weekID,
	weekvalue, 
	transactionCurrencyDecimalPlaces
FROM
	(
SELECT
    (IFNULL(ledger.salesPrice, 1) * ledger.transactionQTY * -1) AS total_value, IFNULL(srp_erp_itemmaster.subcategoryID, 0) AS groupID, srp_erp_itemcategory.description AS description,
	ledger.transactionCurrencyDecimalPlaces,
	srp_erp_financeyearweek.dateFrom, srp_erp_financeyearweek.weekID, srp_erp_financeyearweek.description as weekvalue
FROM
	srp_erp_itemledger ledger
	INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID
	LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subcategoryID 
	INNER JOIN srp_erp_financeyearweek On ledger.documentDate BETWEEN srp_erp_financeyearweek.dateFrom AND srp_erp_financeyearweek.dateTo And srp_erp_financeyearweek.companyID = {$companyID}
WHERE
	ledger.companyFinanceYearID = $financeyear 
	AND ledger.companyID = {$companyID}
	AND srp_erp_itemmaster.mainCategoryID IN (" . join(',', $mainCategoryID) . ")
	AND srp_erp_itemmaster.subcategoryID IN (" . join(',', $subcategoryID) . ")
	AND srp_erp_itemmaster.itemAutoID IN (" . join(',', $itemAutoID) . ")
	AND ledger.wareHouseAutoID IN (" . join(',', $location) . ") 
	AND ( ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV' ) $financePeriodFilt
	) tble 
GROUP BY
	groupID,weekvalue
	ORDER BY dateFrom, groupID ASc")->result_array();
                return $resultdet;
                exit;
            } else if ($dataFilter2 == 3) {
                $resultdet = $this->db->query("SELECT
	tbl.* 
FROM
	(
	SELECT
		SUM( total_value ) AS total_value,
		groupID,
		weekID,
		weekvalue,
		transactionCurrencyDecimalPlaces
	FROM
		(
		SELECT
			( IFNULL( ledger.salesPrice, 1 ) * ledger.transactionQTY * - 1 ) AS total_value,
			IFNULL( srp_erp_customermaster.subLocationID, 0 ) AS groupID,
			ledger.transactionCurrencyDecimalPlaces,
			srp_erp_financeyearweek.dateFrom,
			srp_erp_financeyearweek.weekID,
			srp_erp_financeyearweek.description AS weekvalue 
		FROM
			srp_erp_itemledger ledger
			LEFT JOIN (
			SELECT
				invoiceAutoID AS documentAutoID,
				customerID,
				documentID 
			FROM
				`srp_erp_customerinvoicemaster` UNION ALL
			SELECT
				receiptVoucherAutoId AS documentAutoID,
				customerID,
				documentID 
			FROM
				srp_erp_customerreceiptmaster UNION ALL
			SELECT
				invoiceID AS documentAutoID,
				customerID,
				documentCode AS documentID 
			FROM
				srp_erp_pos_invoice 
			) customerTbl ON customerTbl.documentID = ledger.documentCode 
			AND customerTbl.documentAutoID = ledger.documentAutoID
			LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = customerTbl.customerID
			INNER JOIN srp_erp_financeyearweek ON ledger.documentDate BETWEEN srp_erp_financeyearweek.dateFrom 
			AND srp_erp_financeyearweek.dateTo 
			AND srp_erp_financeyearweek.companyID = {$companyID} 
		WHERE
			ledger.companyFinanceYearID = {$financeyear} 
			AND ledger.companyID = {$companyID} 
			AND ledger.wareHouseAutoID IN (" . join(',', $location) . ") 
			AND ( ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV' ) $financePeriodFilt
		) tble 
	GROUP BY
		groupID,
		weekvalue 
	ORDER BY
		dateFrom ASC 
	) tbl
	LEFT JOIN srp_erp_customermaster customermaster ON customermaster.subLocationID  = tbl.groupID AND (	customermaster.locationID IN (" . join(',', $area) . ") 
	OR customermaster.locationID = 0 OR customermaster.locationID IS NULL)")->result_array();
                return $resultdet;
                exit;
            }

        } else if ($dataFilter == 2) {
            if ($dataFilter2 == 1) {
                $resultdet = $this->db->query("SELECT
	(SUM(total_value)/ SUM(Qty)) as total_value,
	groupID,
	weekID,
	weekvalue, 
	transactionCurrencyDecimalPlaces
FROM
	(
SELECT
 (IFNULL(ledger.salesPrice, 1) * ledger.transactionQTY * -1) AS total_value, (ledger.transactionQTY * -1) AS Qty, IFNULL(srp_erp_itemmaster.itemAutoID, 0) AS groupID, srp_erp_itemmaster.itemDescription AS description,
	ledger.transactionCurrencyDecimalPlaces,
	srp_erp_financeyearweek.dateFrom, srp_erp_financeyearweek.weekID, srp_erp_financeyearweek.description as weekvalue
FROM
	srp_erp_itemledger ledger
	INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID
	INNER JOIN srp_erp_financeyearweek On ledger.documentDate BETWEEN srp_erp_financeyearweek.dateFrom AND srp_erp_financeyearweek.dateTo And srp_erp_financeyearweek.companyID = {$companyID}
WHERE
	ledger.companyFinanceYearID = {$financeyear} 
	AND ledger.companyID = {$companyID}
	AND srp_erp_itemmaster.mainCategoryID IN (" . join(',', $mainCategoryID) . ")
	AND srp_erp_itemmaster.subcategoryID IN (" . join(',', $subcategoryID) . ")
	AND srp_erp_itemmaster.itemAutoID IN (" . join(',', $itemAutoID) . ")
	AND ledger.wareHouseAutoID IN (" . join(',', $location) . ") 
	AND ( ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV' ) {$financePeriodFilt}
) tble 
GROUP BY
	groupID,weekvalue
	ORDER BY dateFrom ASc")->result_array();
                return $resultdet;
                exit;
            } else if ($dataFilter2 == 2) {
                $resultdet = $this->db->query("SELECT
	(SUM(total_value)/ SUM(Qty)) as total_value,
	groupID,
	weekID,
	weekvalue, 
	transactionCurrencyDecimalPlaces
FROM
	(
SELECT
 (IFNULL(ledger.salesPrice, 1) * ledger.transactionQTY * -1) AS total_value, (ledger.transactionQTY * -1) AS Qty, IFNULL(srp_erp_itemmaster.subcategoryID, 0) AS groupID, srp_erp_itemcategory.description AS description,
	ledger.transactionCurrencyDecimalPlaces,
	srp_erp_financeyearweek.dateFrom, srp_erp_financeyearweek.weekID, srp_erp_financeyearweek.description as weekvalue
FROM
	srp_erp_itemledger ledger
	INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID
	LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subcategoryID 
	INNER JOIN srp_erp_financeyearweek On ledger.documentDate BETWEEN srp_erp_financeyearweek.dateFrom AND srp_erp_financeyearweek.dateTo And srp_erp_financeyearweek.companyID = {$companyID}
WHERE
	ledger.companyFinanceYearID = {$financeyear} 
	AND ledger.companyID = {$companyID}
	AND srp_erp_itemmaster.mainCategoryID IN (" . join(',', $mainCategoryID) . ")
	AND srp_erp_itemmaster.subcategoryID IN (" . join(',', $subcategoryID) . ")
	AND srp_erp_itemmaster.itemAutoID IN (" . join(',', $itemAutoID) . ")
	AND ledger.wareHouseAutoID IN (" . join(',', $location) . ") 
	AND ( ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV' ) {$financePeriodFilt}
) tble 
GROUP BY
	groupID,weekvalue
	ORDER BY dateFrom ASc")->result_array();
                return $resultdet;
                exit;
            }
        } else if ($dataFilter == 3) {
            if ($dataFilter2 == 1) {
                $resultdet = $this->db->query("SELECT
	SUM(total_value) as total_value,
	groupID,
	weekID,
	weekvalue, 
	transactionCurrencyDecimalPlaces
FROM
	(
SELECT
  (ledger.transactionQTY * -1) AS total_value, IFNULL(srp_erp_itemmaster.itemAutoID, 0) AS groupID, srp_erp_itemmaster.itemDescription AS description,
	ledger.transactionCurrencyDecimalPlaces,
	srp_erp_financeyearweek.dateFrom, srp_erp_financeyearweek.weekID, srp_erp_financeyearweek.description as weekvalue
FROM
	srp_erp_itemledger ledger
	INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID
	INNER JOIN srp_erp_financeyearweek On ledger.documentDate BETWEEN srp_erp_financeyearweek.dateFrom AND srp_erp_financeyearweek.dateTo And srp_erp_financeyearweek.companyID = {$companyID}
WHERE
	ledger.companyFinanceYearID = {$financeyear} 
	AND ledger.companyID = {$companyID}
	AND srp_erp_itemmaster.mainCategoryID IN (" . join(',', $mainCategoryID) . ")
	AND srp_erp_itemmaster.subcategoryID IN (" . join(',', $subcategoryID) . ")
	AND srp_erp_itemmaster.itemAutoID IN (" . join(',', $itemAutoID) . ")
	AND ledger.wareHouseAutoID IN (" . join(',', $location) . ") 
	AND ( ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV' )
	$financePeriodFilt 
	) tble 
GROUP BY
	groupID,weekvalue
	ORDER BY dateFrom ASc")->result_array();
                return $resultdet;
                exit;
            } else if ($dataFilter2 == 2) {
                $resultdet = $this->db->query("SELECT
	SUM(total_value) as total_value,
	groupID,
	weekID,
	weekvalue, 
	transactionCurrencyDecimalPlaces
FROM
	(
SELECT
  (ledger.transactionQTY * -1) AS total_value, IFNULL(srp_erp_itemmaster.subcategoryID, 0) AS groupID, srp_erp_itemcategory.description AS description,
	ledger.transactionCurrencyDecimalPlaces,
	srp_erp_financeyearweek.dateFrom, srp_erp_financeyearweek.weekID, srp_erp_financeyearweek.description as weekvalue
FROM
	srp_erp_itemledger ledger
	INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = ledger.itemAutoID
	LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subcategoryID
	INNER JOIN srp_erp_financeyearweek On ledger.documentDate BETWEEN srp_erp_financeyearweek.dateFrom AND srp_erp_financeyearweek.dateTo And srp_erp_financeyearweek.companyID = {$companyID}
WHERE
	ledger.companyFinanceYearID = {$financeyear} 
	AND ledger.companyID = {$companyID}
	AND srp_erp_itemmaster.mainCategoryID IN (" . join(',', $mainCategoryID) . ")
	AND srp_erp_itemmaster.subcategoryID IN (" . join(',', $subcategoryID) . ")
	AND srp_erp_itemmaster.itemAutoID IN (" . join(',', $itemAutoID) . ")
	AND ledger.wareHouseAutoID IN (" . join(',', $location) . ") 
	AND ( ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV' )
	$financePeriodFilt 
	) tble 
GROUP BY
	groupID,weekvalue
	ORDER BY dateFrom ASc")->result_array();
                return $resultdet;
                exit;
            } else if ($dataFilter2 == 3) {
                $resultdet = $this->db->query("SELECT
	* 
FROM
	(
	SELECT
		SUM( total_value ) AS total_value,
		groupID,
		weekID,
		weekvalue,
		transactionCurrencyDecimalPlaces 
	FROM
		(
		SELECT
			( ledger.transactionQTY * - 1 ) AS total_value,
			IFNULL( srp_erp_customermaster.subLocationID, 0 ) AS groupID,
			ledger.transactionCurrencyDecimalPlaces,
			srp_erp_financeyearweek.dateFrom,
			srp_erp_financeyearweek.weekID,
			srp_erp_financeyearweek.description AS weekvalue 
		FROM
			srp_erp_itemledger ledger
			LEFT JOIN (
			SELECT
				invoiceAutoID AS documentAutoID,
				customerID,
				documentID 
			FROM
				`srp_erp_customerinvoicemaster` UNION ALL
			SELECT
				receiptVoucherAutoId AS documentAutoID,
				customerID,
				documentID 
			FROM
				srp_erp_customerreceiptmaster UNION ALL
			SELECT
				invoiceID AS documentAutoID,
				customerID,
				documentCode AS documentID 
			FROM
				srp_erp_pos_invoice 
			) customerTbl ON customerTbl.documentID = ledger.documentCode 
			AND customerTbl.documentAutoID = ledger.documentAutoID
			LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = customerTbl.customerID
			INNER JOIN srp_erp_financeyearweek ON ledger.documentDate BETWEEN srp_erp_financeyearweek.dateFrom 
			AND srp_erp_financeyearweek.dateTo 
			AND srp_erp_financeyearweek.companyID = " . $this->common_data['company_data']['company_id'] . " 
		WHERE
			ledger.companyFinanceYearID = {$financeyear} 
			AND ledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
		
			AND ledger.wareHouseAutoID IN ( " . join(',', $location) . " ) 
			AND ( ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV' ) {$financePeriodFilt} 
		) tble 
	GROUP BY
		groupID,
		weekvalue 
	ORDER BY
		dateFrom ASC 
	) tbl
	LEFT JOIN srp_erp_customermaster customermaster ON customermaster.subLocationID  = tbl.groupID AND (customermaster.locationID IN (" . join(',', $area) . ") 
	OR customermaster.locationID = 0 OR customermaster.locationID IS NULL)")->result_array();
                return $resultdet;
                exit;
            } else if ($dataFilter2 == 4) {
                $resultdet = $this->db->query("SELECT
	* 
FROM
	(
	SELECT
		SUM( total_value ) AS total_value,
		groupID,
		weekID,
		weekvalue,
		transactionCurrencyDecimalPlaces 
	FROM
		(
		SELECT
			( ledger.transactionQTY * - 1 ) AS total_value,
			customerTbl.groupID AS groupID,
			ledger.transactionCurrencyDecimalPlaces,
			srp_erp_financeyearweek.dateFrom,
			srp_erp_financeyearweek.weekID,
			srp_erp_financeyearweek.description AS weekvalue 
		FROM
			srp_erp_itemledger ledger
			LEFT JOIN (
			SELECT
				invoiceAutoID AS documentAutoID,
				customerID AS groupID,
				documentID 
			FROM
				`srp_erp_customerinvoicemaster` UNION ALL
			SELECT
				receiptVoucherAutoId AS documentAutoID,
				customerID AS groupID,
				documentID 
			FROM
				srp_erp_customerreceiptmaster UNION ALL
			SELECT
				invoiceID AS documentAutoID,
				customerID AS groupID,
				documentCode AS documentID 
			FROM
				srp_erp_pos_invoice 
			) customerTbl ON customerTbl.documentID = ledger.documentCode 
			AND customerTbl.documentAutoID = ledger.documentAutoID
			INNER JOIN srp_erp_financeyearweek ON ledger.documentDate BETWEEN srp_erp_financeyearweek.dateFrom 
			AND srp_erp_financeyearweek.dateTo 
			AND srp_erp_financeyearweek.companyID = " . $this->common_data['company_data']['company_id'] . " 
		WHERE
			ledger.companyFinanceYearID = {$financeyear} 
			AND customerTbl.groupID IN ( " . join(',', $customerID) . " ) 
			AND ledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
			AND ledger.wareHouseAutoID IN ( " . join(',', $location) . " ) 
			AND ( ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV' ) 
			{$financePeriodFilt} 
		) tble 
	GROUP BY
		groupID,
		weekvalue 
	ORDER BY
		dateFrom ASC 
	) tbl
	LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = tbl.groupID 
WHERE
	customermaster.partyCategoryID IN ( " . join(',', $cusCategory) . " )")->result_array();
                return $resultdet;
                exit;
            } else if ($dataFilter2 == 5) {
                $resultdet = $this->db->query("SELECT * FROM(SELECT
	SUM(total_value) as total_value,
	groupID,
	weekID,
	weekvalue, 
	transactionCurrencyDecimalPlaces
FROM
	(
SELECT
  (ledger.transactionQTY * -1) AS total_value,	IFNULL(srp_erp_customermaster.partyCategoryID, 0) AS groupID,
	ledger.transactionCurrencyDecimalPlaces,
	srp_erp_financeyearweek.dateFrom, srp_erp_financeyearweek.weekID, srp_erp_financeyearweek.description as weekvalue
FROM
	srp_erp_itemledger ledger
	
	LEFT JOIN (SELECT
	invoiceAutoID AS documentAutoID,
	customerID AS customerID,
	documentID 
FROM
	`srp_erp_customerinvoicemaster` UNION ALL
SELECT
	receiptVoucherAutoId AS documentAutoID,
	customerID AS customerID,
	documentID 
FROM
	srp_erp_customerreceiptmaster UNION ALL
SELECT
	invoiceID AS documentAutoID,
	customerID AS customerID,
	documentCode as documentID 
FROM
	srp_erp_pos_invoice) customerTbl ON customerTbl.documentID = ledger.documentCode AND customerTbl.documentAutoID = ledger.documentAutoID 
	LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = customerTbl.customerID
	INNER JOIN srp_erp_financeyearweek On ledger.documentDate BETWEEN srp_erp_financeyearweek.dateFrom AND srp_erp_financeyearweek.dateTo And srp_erp_financeyearweek.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	ledger.companyFinanceYearID = $financeyear 
	AND srp_erp_customermaster.partyCategoryID IN (" . join(',', $cusCategory) . ") 
	AND customerTbl.customerID IN (" . join(',', $customerID) . ")
	AND ledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
	AND ledger.wareHouseAutoID IN (" . join(',', $location) . ") 
	AND ( ledger.documentCode = 'CINV' OR ledger.documentCode = 'POS' OR ledger.documentCode = 'RV' )
	$financePeriodFilt 
	) tble 
GROUP BY
	groupID,weekvalue
	ORDER BY dateFrom ASc) tbl")->result_array();
                return $resultdet;
                exit;
            }
        }
    }

    function get_erp_weeklySalesIncome_drilldown_report()
    {
        $location = $this->input->post("location");
        $weekID = $this->input->post("weekID");
        $itemCategoryID = $this->input->post("itemCategoryID");
        $companyID = $this->common_data['company_data']['company_id'];
        $result = '';
        if ($weekID) {
            if ($itemCategoryID == 0) {
                $itemCategoryID = "0 OR srp_erp_itemmaster.subCategoryID IS NULL ";
            }

            $result = $this->db->query("SELECT srp_erp_itemledger.documentID, srp_erp_itemledger.documentAutoID, srp_erp_itemledger.segmentCode,
	 CONCAT(srp_erp_itemledger.itemSystemCode, ' | ', srp_erp_itemledger.itemDescription) as item, srp_erp_itemledger.documentSystemCode,srp_erp_itemledger.documentDate,
	   (IFNULL(srp_erp_itemledger.salesPrice, 1) * srp_erp_itemledger.transactionQTY * -1) as total_value,srp_erp_itemledger.transactionCurrencyDecimalPlaces
FROM
	srp_erp_itemledger 
	INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID
	LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subCategoryID
	INNER JOIN srp_erp_financeyearweek ON srp_erp_financeyearweek.weekID = {$weekID} 
	AND srp_erp_itemledger.documentDate BETWEEN srp_erp_financeyearweek.dateFrom 
	AND srp_erp_financeyearweek.dateTo 
WHERE
	srp_erp_itemledger.companyID = {$companyID} 
	AND srp_erp_itemledger.wareHouseAutoID IN (" . join(',', $location) . ") 
	AND ( srp_erp_itemledger.documentCode = 'CINV' OR srp_erp_itemledger.documentCode = 'POS' OR srp_erp_itemledger.documentCode = 'RV')
	AND (srp_erp_itemmaster.subCategoryID = {$itemCategoryID})
")->result_array();
        }
        return $result;
    }

    function get_sales_order_details_report()
    {
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];
        $customerID = $this->input->post('customerID');
        $viewZeroBalace = $this->input->post('viewZeroBalace');
        $documentTypes = $this->input->post('documentTypes');
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $documentCodes = $this->input->post('documentCodes');
        $statusID = $this->input->post('statusID');
        $status_filter = '';
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 00:00:00')";
        } else if (empty($dateto)) {
            $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00')";
        }

        $having = "";
        if (isset($viewZeroBalace)) {
            $having = "HAVING b.balanceQty > 0";
        }

        if ($documentTypes != 'All') {
            $doctype = " AND srp_erp_contractmaster.documentID = '$documentTypes' ";
        } else {
            $doctype = "";
        }
        if (!empty($documentCodes)) {
            $documentCodes = implode(',', $documentCodes);
            $documentCodeDrop = 'AND srp_erp_contractmaster.contractAutoID IN (' . $documentCodes . ')';
        } else {
            $documentCodeDrop = "";
        }
        if (!empty($statusID)) {
            $stat = implode(',', $statusID);
            $status_filter = 'WHERE  status IN (' . $stat . ')  ';
        }
        $where = $date . " AND customerID IN (" . join(',', $customerID) . ") " . $doctype . $documentCodeDrop;
        /*IFNULL(delivered.deliveredQty,0) + IFNULL(invoiced.requestedQty,0) AS receivedQty,
	    srp_erp_contractdetails.requestedQty - (IFNULL(delivered.deliveredQty,0) + IFNULL(invoiced.requestedQty,0)) AS balanceQty,*/
        $result = $this->db->query("SELECT * FROM(SELECT a.*,IF(receivedQty = 0,'2',IF((Qty - receivedQty) <= 0,'1','3')) as status 
		FROM (SELECT
    customerSystemCode, customerName,
	srp_erp_contractmaster.contractAutoID,
	srp_erp_contractdetails.contractDetailsAutoID,
	contractCode,
	contractDate,
	referenceNo,
	srp_erp_contractdetails.itemAutoID,
	seconeryItemCode,
	srp_erp_contractdetails.itemDescription,
	unitOfMeasure,
	srp_erp_contractdetails.requestedQty AS Qty,
    TRIM(TRAILING 0 FROM(ROUND( ifnull( delivered.deliveredQty, 0 ), 3 )))+TRIM(TRAILING 0 FROM(ROUND( ifnull( invoiced.requestedQty, 0 ), 3 ))) AS receivedQty,
    TRIM(TRAILING 0 FROM(ROUND( ifnull( srp_erp_contractdetails.requestedQty, 0 ), 3 ))) - (TRIM(TRAILING 0 FROM(ROUND( ifnull( delivered.deliveredQty, 0 ), 3 )))+TRIM(TRAILING 0 FROM(ROUND( ifnull( invoiced.requestedQty, 0 ), 3 )))) AS balanceQty,
	unittransactionAmount,
	srp_erp_contractdetails.transactionAmount,
	srp_erp_contractmaster.transactionCurrencyDecimalPlaces ,
	srp_erp_contractmaster.documentID
FROM
	srp_erp_contractdetails
	LEFT JOIN srp_erp_contractmaster ON srp_erp_contractdetails.contractAutoID = srp_erp_contractmaster.contractAutoID
	LEFT JOIN srp_erp_itemmaster ON srp_erp_contractdetails.itemAutoID = srp_erp_itemmaster.itemAutoID
	LEFT JOIN ( SELECT contractAutoID, contractDetailsAutoID, SUM(deliveredQty) AS deliveredQty FROM `srp_erp_deliveryorderdetails` GROUP BY contractAutoID, contractDetailsAutoID ) delivered ON delivered.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID
	LEFT JOIN ( SELECT contractAutoID, contractDetailsAutoID, SUM(requestedQty) AS requestedQty FROM `srp_erp_customerinvoicedetails` GROUP BY contractAutoID, contractDetailsAutoID ) invoiced ON invoiced.contractDetailsAutoID = srp_erp_contractdetails.contractDetailsAutoID 
WHERE
	 approvedYN = 1 
	AND srp_erp_contractdetails.companyID = {$companyID} {$where}
)a )b  $status_filter  {$having}")->result_array();
        return $result;
    }

    function load_sales_person_performance_detail_report($filterItem, $filterWarehouse, $filterDateFrom, $filterDateTo, $filterSalesperson)
    {
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];
        $mainCategoryID = $this->input->post('detail_mainCategoryID');
        $subcategoryID = $this->input->post('detail_subcategoryID');
        $subsubcategoryID = $this->input->post('detail_subsubcategoryID');
        $items = $this->input->post('detail_items');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $currency = $this->input->post('detail_currency');
        $salespersons = $this->input->post('detail_salesperson');
        $datefrom = $this->input->post('detail_datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('detail_dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $detail_type = $this->input->post('detail_type');

        $filter = '';
        $date = "";
        /* if(!empty($items))
        {
            $item  = implode(',',$items);
            $item_filter = 'AND   contractdetails.itemAutoID IN ('.$item.')  ';
        }
        if(!empty($wareHouseAutoID))
        {
            $wareHouse  = implode(',',$wareHouseAutoID);
            $warehouse_filter = 'AND   conatractmaster.warehouseAutoID IN ('.$wareHouse.')  ';
        } */
        /* if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 00:00:00')";
        } else if(empty($dateto)) {
            $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00')";
        } */
        /* if(!empty($salespersons))
        {
            $salesperson  = implode(',',$salespersons);
            $salesperson_filter = 'AND   conatractmaster.salesPersonID IN ('.$salesperson.')  ';
        } */
        //$currency = $this->input->post('detail_currency');

        //$where_tmp[]="";
        if ($filterWarehouse != null) {
            $where_tmp[] = "  wareHouseAutoID IN(" . $filterWarehouse . ") ";
        }

        if ($filterItem != null) {
            $where_tmp[] = " itemAutoID IN(" . $filterItem . ") ";
        }

        if ($filterSalesperson != null) {
            $where_tmp[] = " salesPersonID IN(" . $filterSalesperson . ") ";
        }
        //$where_tmp[] = " ( srp_erp_itemledger.documentDate  BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' )";


        if ($currency == '3') {
            $currencyExchange = 'companyReportingExchangeRate';
            $currencyDecimal = 'companyReportingCurrencyDecimalPlaces';
        } else {
            $currencyExchange = 'companyLocalExchangeRate';
            $currencyDecimal = 'companyLocalCurrencyDecimalPlaces';
        }

        $where_tmp[] = ' qty > 0';
        $filter = join('AND', $where_tmp);
        $where = 'WHERE ' . $filter;


        if ($detail_type == 1) {
            $result = $this->db->query(" SELECT 
				t1.salesPersonID AS salesPersonID,
				t1.SalesPersonName AS salesPersonName,
				t1.warehouseAutoID AS warehouseAutoID,
				t1.itemAutoID  AS itemAutoID,
				itemSystemCode  AS itemSystemCode,
				t1.seconeryItemCode AS seconeryItemCode,
				t1.itemDescription  AS itemDescription,
				sum(t1.qty) as qty,
				sum(t1.contractvalue) as   contractvalue,
				sum(t1.amount) AS amount,
				t1.currencyDecimalPlaces AS currencyDecimalPlaces,
				t1.UnitOfMeasure AS UnitOfMeasure,
				t1.transactionCurrencyDecimalPlaces,
				'1' AS type

			 FROM (
				SELECT
					conatractmaster.salesPersonID AS salesPersonID,
					salespersoncontract.SalesPersonName AS salesPersonName,
					conatractmaster.warehouseAutoID as warehouseAutoID,
					IFNULL(srp_erp_itemmaster.itemAutoID,contractdetails.itemAutoID) AS itemAutoID,
					IFNULL(srp_erp_itemmaster.itemSystemCode,contractdetails.itemSystemCode) AS itemSystemCode,
					IFNULL(srp_erp_itemmaster.seconeryItemCode, ' - ') AS seconeryItemCode,
					IFNULL(srp_erp_itemmaster.itemDescription,contractdetails.itemDescription) AS itemDescription,
					IFNULL( ( contractdetails.requestedQty ), 0 ) qty,
					IFNULL( ( contractdetails.transactionAmount ), 0 ) contractvalue,
					IFNULL( ( contractdetails.transactionAmount / conatractmaster.$currencyExchange ) , 0 ) AS amount,
					
					conatractmaster.$currencyDecimal AS currencyDecimalPlaces,
					contractdetails.UnitOfMeasure AS UnitOfMeasure ,
					conatractmaster.transactionCurrencyDecimalPlaces
				FROM
					srp_erp_contractdetails contractdetails
					LEFT JOIN srp_erp_contractmaster conatractmaster ON conatractmaster.contractAutoID = contractdetails.contractAutoID
					LEFT JOIN srp_erp_itemmaster ON contractdetails.itemAutoID = srp_erp_itemmaster.itemAutoID
					JOIN srp_erp_salespersonmaster salespersoncontract ON salespersoncontract.salesPersonID = conatractmaster.salesPersonID 
					WHERE
						conatractmaster.approvedYN = 1 
						AND contractdetails.companyID = {$companyID} 
						AND contractdetails.itemAutoID is not null
						AND ( contractDate >= '{$filterDateFrom}' AND contractDate <= '{$filterDateTo}' ) 

						
					 ) t1
					$where
					GROUP BY 
					itemAutoID , salesPersonID
					
			")->result_array();
        } else {
            $result = $this->db->query("SELECT 
				t1.salesPersonID AS salesPersonID,
				t1.SalesPersonName AS salesPersonName,
				t1.warehouseAutoID AS warehouseAutoID,
				t1.itemAutoID AS itemAutoID,
				t1.itemSystemCode AS itemSystemCode,
				t1.seconeryItemCode AS seconeryItemCode,
				t1.itemDescription AS itemDescription,
				sum(t1.qty) as qty,
				sum(t1.contractvalue) as   contractvalue,
				sum(t1.amount) AS amount,
				t1.currencyDecimalPlaces AS currencyDecimalPlaces,
				t1.UnitOfMeasure AS UnitOfMeasure,
				t1.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
				'2' AS type
			 FROM (
				SELECT
					invoicemaster.salesPersonID AS salesPersonID,
					salespersoncontract.SalesPersonName AS salesPersonName,
					invoicedetail.warehouseAutoID as warehouseAutoID,

					IFNULL(srp_erp_itemmaster.itemAutoID,invoicedetail.itemAutoID) AS itemAutoID,
					IFNULL(srp_erp_itemmaster.itemSystemCode,invoicedetail.itemSystemCode) AS itemSystemCode,
					IFNULL(srp_erp_itemmaster.seconeryItemCode,' - ') AS seconeryItemCode,
					IFNULL(srp_erp_itemmaster.itemDescription,invoicedetail.itemDescription) AS itemDescription,
					IFNULL( ( invoicedetail.requestedQty ), 0 ) qty,
					IFNULL( ( invoicedetail.transactionAmount ), 0 ) contractvalue,
					IFNULL( ( invoicedetail.transactionAmount / invoicemaster.$currencyExchange ) , 0 ) AS amount,
					
					invoicemaster.$currencyDecimal AS currencyDecimalPlaces,
					invoicedetail.UnitOfMeasure AS UnitOfMeasure ,
					invoicemaster.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces
				FROM
					srp_erp_customerinvoicedetails invoicedetail
					LEFT JOIN srp_erp_customerinvoicemaster invoicemaster ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID 
					LEFT JOIN srp_erp_itemmaster ON invoicedetail.itemAutoID = srp_erp_itemmaster.itemAutoID
					JOIN srp_erp_salespersonmaster salespersoncontract ON salespersoncontract.salesPersonID = invoicemaster.salesPersonID 
					WHERE
					invoicemaster.approvedYN = 1 
						AND invoicedetail.companyID = {$companyID} 
						AND invoicedetail.itemAutoID is not null
						AND ( invoiceDate >= '{$filterDateFrom}' AND invoiceDate <= '{$filterDateTo}' ) 
					
				UNION ALL
				
				SELECT
					domaster.salesPersonID AS salesPersonID,
					salespersoncontract.SalesPersonName AS salesPersonName,
					dodetail.warehouseAutoID as warehouseAutoID,

					IFNULL(srp_erp_itemmaster.itemAutoID,dodetail.itemAutoID) AS itemAutoID,
					IFNULL(srp_erp_itemmaster.itemSystemCode,dodetail.itemSystemCode) AS itemSystemCode,
					IFNULL(srp_erp_itemmaster.seconeryItemCode,' - ') AS seconeryItemCode,
					IFNULL(srp_erp_itemmaster.itemDescription,dodetail.itemDescription) AS itemDescription,
					IFNULL( ( dodetail.requestedQty ), 0 ) qty,
					IFNULL( ( dodetail.transactionAmount ), 0 ) contractvalue,
					IFNULL( ( dodetail.transactionAmount / domaster.$currencyExchange ) , 0 ) AS amount,
					
					domaster.$currencyDecimal AS currencyDecimalPlaces,
					dodetail.UnitOfMeasure AS UnitOfMeasure ,
					domaster.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces
					
				FROM
				srp_erp_deliveryorderdetails dodetail
				LEFT JOIN srp_erp_deliveryorder domaster ON domaster.DOAutoID = dodetail.DOAutoID 
				LEFT JOIN srp_erp_itemmaster ON dodetail.itemAutoID = srp_erp_itemmaster.itemAutoID
				JOIN srp_erp_salespersonmaster salespersoncontract ON salespersoncontract.salesPersonID = domaster.salesPersonID 
				WHERE
					domaster.approvedYN = 1 
					AND dodetail.companyID = {$companyID} 
					AND dodetail.itemAutoID is not null
					AND ( DODate >= '{$filterDateFrom}' AND DODate <= '{$filterDateTo}' ) 
				 ) t1

			$where 
			GROUP BY
			itemAutoID,
			salesPersonID 	
			")->result_array();
        }

        return $result;
    }

    function get_itemwise_salesperson_preformance_dd_cnt_so()
    {
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $datecontract = "";
        $companyID = current_companyID();
        $item = $this->input->post('item');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $currency = $this->input->post('detail_currency');
        $salesPersonID = $this->input->post('salesPersonID');
        $datefrom = $this->input->post('detail_datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('detail_dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $detail_type = $this->input->post('detail_type');
        $filter = '';
        if (!empty($datefrom) && !empty($dateto)) {
            $filter .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 00:00:00')";
        }

        if (!empty($wareHouseAutoID)) {
            $wareHouse = implode(',', $wareHouseAutoID);
            $filter .= 'AND   conatractmaster.warehouseAutoID IN (' . $wareHouse . ')  ';
        }
        //  if (!empty($datefrom) && !empty($dateto)) {
        //     $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 00:00:00')";
        // } else if(empty($dateto)) {
        //     $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' )";
        // } 


        if ($currency == '3') {
            $currencyExchange = 'companyReportingExchangeRate';
            $currencyDecimal = 'companyReportingCurrencyDecimalPlaces';
        } else {
            $currencyExchange = 'companyLocalExchangeRate';
            $currencyDecimal = 'companyLocalCurrencyDecimalPlaces';
        }
        $qry = " 
		SELECT
		conatractmaster.salesPersonID AS salesPersonID,
		salespersoncontract.SalesPersonName AS salesPersonName,
		conatractmaster.warehouseAutoID as warehouseAutoID,
		IFNULL(srp_erp_itemmaster.itemAutoID,contractdetails.itemAutoID) AS itemAutoID,
		IFNULL(srp_erp_itemmaster.itemSystemCode,contractdetails.itemSystemCode) AS itemSystemCode,
		IFNULL(srp_erp_itemmaster.seconeryItemCode, ' - ') AS seconeryItemCode,
		IFNULL(srp_erp_itemmaster.itemDescription,contractdetails.itemDescription) AS itemDescription,
		IFNULL( ( contractdetails.requestedQty ), 0 ) qty,
		IFNULL( ( contractdetails.transactionAmount ), 0 ) contractvalue,
		IFNULL( ( contractdetails.transactionAmount / conatractmaster.$currencyExchange ) , 0 ) AS amount,
		conatractmaster.contractCode as docsyscode,
		conatractmaster.contractDate as docDate,
		conatractmaster.documentID as documentID,
		conatractmaster.contractAutoID as masterID,
		conatractmaster.$currencyDecimal AS currencyDecimalPlaces,
		contractdetails.UnitOfMeasure AS UnitOfMeasure ,
		conatractmaster.transactionCurrencyDecimalPlaces
	FROM
		srp_erp_contractdetails contractdetails
		LEFT JOIN srp_erp_contractmaster conatractmaster ON conatractmaster.contractAutoID = contractdetails.contractAutoID
		LEFT JOIN srp_erp_itemmaster ON contractdetails.itemAutoID = srp_erp_itemmaster.itemAutoID
		JOIN srp_erp_salespersonmaster salespersoncontract ON salespersoncontract.salesPersonID = conatractmaster.salesPersonID 
		WHERE
			conatractmaster.approvedYN = 1 
			AND contractdetails.companyID = {$companyID} 
			AND contractdetails.itemAutoID is not null
			AND contractdetails.itemAutoID = {$item}
			AND conatractmaster.salesPersonID = {$salesPersonID}
			$filter
		";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_itemwise_salesperson_preformance_dd_cinv_do()
    {
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $datecontract = "";
        $companyID = current_companyID();
        $item = $this->input->post('item');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $currency = $this->input->post('detail_currency');
        $salesPersonID = $this->input->post('salesPersonID');
        $datefrom = $this->input->post('detail_datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('detail_dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $detail_type = $this->input->post('detail_type');
        $filter = '';
        $invoicedate = '';
        $dodate = '';
        $invwareHouse = '';
        $dowareHouse = '';
        if (!empty($datefrom) && !empty($dateto)) {
            $invoicedate .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
        }
        if (!empty($datefrom) && !empty($dateto)) {
            $dodate .= " AND ( DODate >= '" . $datefromconvert . " 00:00:00' AND DODate <= '" . $datetoconvert . " 00:00:00')";
        }

        if (!empty($wareHouseAutoID)) {
            $wareHouse = implode(',', $wareHouseAutoID);
            $invwareHouse .= 'AND   invoicedetail.warehouseAutoID IN (' . $wareHouse . ')  ';
            $dowareHouse .= 'AND   dodetail.warehouseAutoID IN (' . $wareHouse . ')  ';
        }
        //  if (!empty($datefrom) && !empty($dateto)) {
        //     $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 00:00:00')";
        // } else if(empty($dateto)) {
        //     $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' )";
        // } 


        if ($currency == '3') {
            $currencyExchange = 'companyReportingExchangeRate';
            $currencyDecimal = 'companyReportingCurrencyDecimalPlaces';
        } else {
            $currencyExchange = 'companyLocalExchangeRate';
            $currencyDecimal = 'companyLocalCurrencyDecimalPlaces';
        }
        $qry = " 
		SELECT
		invoicemaster.salesPersonID AS salesPersonID,
		salespersoninv.SalesPersonName AS salesPersonName,
		invoicemaster.warehouseAutoID as warehouseAutoID,
		IFNULL(srp_erp_itemmaster.itemAutoID,invoicedetail.itemAutoID) AS itemAutoID,
		IFNULL(srp_erp_itemmaster.itemSystemCode,invoicedetail.itemSystemCode) AS itemSystemCode,
		IFNULL(srp_erp_itemmaster.seconeryItemCode, ' - ') AS seconeryItemCode,
		IFNULL(srp_erp_itemmaster.itemDescription,invoicedetail.itemDescription) AS itemDescription,
		IFNULL( ( invoicedetail.requestedQty ), 0 ) qty,
		IFNULL( ( invoicedetail.transactionAmount / invoicemaster.$currencyExchange ) , 0 ) AS amount,
		invoicemaster.invoiceCode as docsyscode,
		invoicemaster.invoiceDate as docDate,
		invoicemaster.documentID as documentID,
		invoicemaster.invoiceAutoID as masterID,
		invoicemaster.$currencyDecimal AS currencyDecimalPlaces,
		invoicedetail.UnitOfMeasure AS UnitOfMeasure ,
		invoicemaster.transactionCurrencyDecimalPlaces
	FROM
		srp_erp_customerinvoicedetails invoicedetail
		LEFT JOIN srp_erp_customerinvoicemaster invoicemaster ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID
		LEFT JOIN srp_erp_itemmaster ON invoicedetail.itemAutoID = srp_erp_itemmaster.itemAutoID
		JOIN srp_erp_salespersonmaster salespersoninv ON salespersoninv.salesPersonID = invoicemaster.salesPersonID 
		WHERE
		invoicemaster.approvedYN = 1 
			AND invoicedetail.companyID = {$companyID} 
			AND invoicedetail.itemAutoID is not null
			AND invoicedetail.itemAutoID = {$item}
			AND invoicemaster.salesPersonID = {$salesPersonID}
			$invoicedate
			$invwareHouse

		UNION ALL
		
		SELECT
		domaster.salesPersonID AS salesPersonID,
		salespersondo.SalesPersonName AS salesPersonName,
		dodetail.warehouseAutoID as warehouseAutoID,
		IFNULL(srp_erp_itemmaster.itemAutoID,dodetail.itemAutoID) AS itemAutoID,
		IFNULL(srp_erp_itemmaster.itemSystemCode,dodetail.itemSystemCode) AS itemSystemCode,
		IFNULL(srp_erp_itemmaster.seconeryItemCode, ' - ') AS seconeryItemCode,
		IFNULL(srp_erp_itemmaster.itemDescription,dodetail.itemDescription) AS itemDescription,
		IFNULL( ( dodetail.requestedQty ), 0 ) qty,
		IFNULL( ( dodetail.transactionAmount / domaster.$currencyExchange ) , 0 ) AS amount,
		domaster.DOCode as docsyscode,
		domaster.DODate as docDate,
		domaster.documentID as documentID,
		domaster.DOAutoID as masterID,
		domaster.$currencyDecimal AS currencyDecimalPlaces,
		dodetail.UnitOfMeasure AS UnitOfMeasure ,
		domaster.transactionCurrencyDecimalPlaces
	FROM
		srp_erp_deliveryorderdetails dodetail
		LEFT JOIN srp_erp_deliveryorder domaster ON domaster.DOAutoID = dodetail.DOAutoID
		LEFT JOIN srp_erp_itemmaster ON dodetail.itemAutoID = srp_erp_itemmaster.itemAutoID
		JOIN srp_erp_salespersonmaster salespersondo ON salespersondo.salesPersonID = domaster.salesPersonID 
		WHERE
		domaster.approvedYN = 1 
			AND dodetail.companyID = {$companyID} 
			AND dodetail.itemAutoID is not null
			AND dodetail.itemAutoID = {$item}
			AND domaster.salesPersonID = {$salesPersonID}
			$dodate
			$dowareHouse
		";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_sales_summary_report()
    {
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $currencyType = $this->input->post('currencyType');

        $date_format_policy = date_format_policy();

        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyID = current_companyID();

        if($currencyType=='local_currency'){
            $exchangeRate = 'companyLocalExchangeRate';
            $retensionAmount = 'retensionLocalAmount';
            $trasactionAmount = '(srp_erp_customerinvoicemaster.transactionAmount/srp_erp_customerinvoicemaster.companyLocalExchangeRate)';
        }else if($currencyType=='reporting_currency'){
            $exchangeRate = 'companyReportingExchangeRate';
            $retensionAmount = 'retensionReportingAmount';
            $trasactionAmount = '(srp_erp_customerinvoicemaster.transactionAmount/srp_erp_customerinvoicemaster.companyReportingExchangeRate)';
        }

        $qry = "SELECT
  srp_erp_customermaster.customerAutoID,
    srp_erp_customermaster.customerSystemCode,
    srp_erp_customermaster.secondaryCode,
    srp_erp_customermaster.customerName,
    srp_erp_partycategories.categoryDescription,
    ifnull(srp_erp_customermaster.rebatePercentage,0) as rebatePercentage,
    ifnull(previousSalesDet.totalSales,0) As previous12monthsales,
    srp_erp_customermaster.customerCreditPeriod AS credintMonths,
    srp_erp_customermaster.customerCreditLimit AS CreditAmount,
    ifnull(outstanding,0) as outstanding,
    ifnull(outStandingmorethanCreditMonth,0) as outStandingmorethanCreditMonth
FROM
    srp_erp_customermaster
        
    -- for previous12monthsales
    LEFT JOIN( 
        SELECT a.customerID, ifnull(sum(a.previousSales) ,0)AS totalSales FROM (
            SELECT 
                srp_erp_customerinvoicemaster.customerID as customerID,
                IFNULL(SUM(srp_erp_customerinvoicedetails.transactionAmount / srp_erp_customerinvoicemaster.$exchangeRate ) ,0)as previousSales
            FROM
                `srp_erp_customerinvoicemaster`
            LEFT JOIN 	srp_erp_customerinvoicedetails ON  srp_erp_customerinvoicedetails.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID` AND srp_erp_customerinvoicemaster.approvedYN = 1
            WHERE
                `srp_erp_customerinvoicedetails`.`companyID` = $companyID AND  
                invoiceDate is not null
                AND invoiceDate BETWEEN  (DATE_SUB('$datetoconvert' , INTERVAL 12 MONTH) ) AND '$datetoconvert' 
                GROUP BY
                    customerID
        UNION 
			
			SELECT
			
			  srp_erp_customerreceiptmaster.customerID as customerID,
				IFNULL( SUM( srp_erp_customerreceiptdetail.transactionAmount / srp_erp_customerreceiptdetail.companyLocalExchangeRate ), 0 ) AS previousSales
				
			FROM
				srp_erp_customerreceiptmaster
				LEFT JOIN `srp_erp_customerreceiptdetail` ON `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` = `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` 
				AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 
			WHERE
				`srp_erp_customerreceiptdetail`.`companyID` = $companyID AND
				srp_erp_customerreceiptmaster.RVDate IS NOT NULL 
				AND srp_erp_customerreceiptmaster.RVDate BETWEEN ( DATE_SUB( '$datetoconvert', INTERVAL 12 MONTH ) ) 	AND '$datetoconvert' 
				AND type != 'Advance' 
				AND type != 'Invoice' 
				
			GROUP BY
				customerID    
        ) a 
        GROUP BY
            customerID
    )previousSalesDet ON previousSalesDet.customerID = srp_erp_customermaster.customerAutoID        
    -- end previous12monthsales   
        
    LEFT JOIN srp_erp_partycategories ON srp_erp_customermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID
    -- currentoutstanding
    LEFT JOIN ( SELECT partyAutoID, sum( transactionAmount / $exchangeRate ) AS outstanding FROM srp_erp_generalledger 
        WHERE subLedgerType = 3 and documentDate<='$datetoconvert' GROUP BY partyAutoID ) outstanding ON outstanding.partyAutoID = srp_erp_customermaster.customerAutoID 
    
    
    -- Start of Outstanding greater than credit month
    
LEFT JOIN (SELECT
`srp_erp_customerinvoicemaster`.`customerID` AS customerID,
    (
    SUM( $trasactionAmount - ( IFNULL( $retensionAmount, 0 ) ) - IFNULL( srp_erp_customerinvoicemaster.rebateAmount, 0 ) ) - (
    IFNULL( SUM( pvd.transactionAmount ), 0 ) + IFNULL( SUM( cnd.transactionAmount ), 0 ) + IFNULL( SUM( ca.transactionAmount ), 0 ) 
    ) 
    ) AS outStandingmorethanCreditMonth
  
FROM
    `srp_erp_customerinvoicemaster`
    LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customerinvoicemaster`.`customerID` = `srp_erp_customermaster`.`customerAutoID` 
    AND `srp_erp_customermaster`.`companyID` = $companyID
    LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_customerinvoicemaster.documentID 
    AND srp_erp_documentcodemaster.companyID = $companyID
    LEFT JOIN (
SELECT
    IFNULL( SUM( srp_erp_customerreceiptdetail.transactionAmount ), 0 ) AS transactionAmount,
    srp_erp_customerreceiptdetail.invoiceAutoID,
    srp_erp_customerreceiptdetail.receiptVoucherAutoID 
FROM
    srp_erp_customerreceiptdetail
    INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` 
    AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 
WHERE
    `srp_erp_customerreceiptdetail`.`companyID` = $companyID 
    AND srp_erp_customerreceiptmaster.RVDate <= '$datetoconvert' 
GROUP BY
    srp_erp_customerreceiptdetail.invoiceAutoID 
    ) pvd ON ( pvd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID` )
    LEFT JOIN (
SELECT
    SUM( srp_erp_creditnotedetail.transactionAmount ) AS transactionAmount,
    invoiceAutoID,
    srp_erp_creditnotedetail.creditNoteMasterAutoID 
FROM
    srp_erp_creditnotedetail
    INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` 
    AND `srp_erp_creditnotemaster`.`approvedYN` = 1 
WHERE
    `srp_erp_creditnotedetail`.`companyID` = $companyID 
    AND srp_erp_creditnotemaster.creditNoteDate <= '$datetoconvert' 
GROUP BY
    srp_erp_creditnotedetail.invoiceAutoID 
    ) cnd ON ( cnd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID` )
    LEFT JOIN (
SELECT
    SUM( srp_erp_rvadvancematchdetails.transactionAmount ) AS transactionAmount,
    srp_erp_rvadvancematchdetails.InvoiceAutoID,
    srp_erp_rvadvancematchdetails.receiptVoucherAutoID 
FROM
    srp_erp_rvadvancematchdetails
    INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` 
    AND `srp_erp_rvadvancematch`.`confirmedYN` = 1 
WHERE
    `srp_erp_rvadvancematchdetails`.`companyID` = $companyID 
GROUP BY
    srp_erp_rvadvancematchdetails.InvoiceAutoID 
    ) ca ON ( ca.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID` )
    LEFT JOIN srp_erp_chartofaccounts ON srp_erp_customerinvoicemaster.customerReceivableAutoID = srp_erp_chartofaccounts.GLAutoID 
    AND `srp_erp_chartofaccounts`.`companyID` = $companyID
    LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_customerinvoicemaster.companyReportingCurrencyID )
    LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_customerinvoicemaster.companyLocalCurrencyID )
    LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) TC ON ( TC.currencyID = srp_erp_customerinvoicemaster.transactionCurrencyID ) 
WHERE
-- srp_erp_customermaster.customerAutoID in ( '1,3,10,14,99')  AND 
    `srp_erp_customerinvoicemaster`.`companyID` = $companyID 
    AND srp_erp_customerinvoicemaster.invoiceDate <= '$datetoconvert' 
    AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1 
    AND (srp_erp_customermaster.customerCreditPeriod<(TIMESTAMPDIFF( Month, srp_erp_customerinvoicemaster.`invoiceDueDate`,'$datetoconvert')))
GROUP BY
    -- `srp_erp_customerinvoicemaster`.`invoiceAutoID` 
    srp_erp_customermaster.customerAutoID
HAVING
    ( outStandingmorethanCreditMonth != - 0 AND outStandingmorethanCreditMonth != 0 ) 
    
)   moreCreditmonth on moreCreditmonth.customerID=srp_erp_customermaster.customerAutoID
-- End of Out Standing more than credit month
WHERE
    srp_erp_customermaster.customerAutoID IN ( ".join(',',$customerID)." )";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }


    function get_back_to_back_revenue_report()
{
    //$company = $this->get_group_company();
    //$companies = getallsubGroupCompanies();
    $companyID = current_companyID();
    
    //$masterGroupID = getParentgroupMasterID();

    $convertFormat = convert_date_format_sql();
    $customerID = $this->input->post('customerID');
    $supplierAutoID = $this->input->post('supplier');
    $search = $this->input->post('search');

    $date_format_policy = date_format_policy();
    $datefrom = $this->input->post('datefrom');
    $datefromconvert = input_format_date($datefrom, $date_format_policy);
    $dateto = $this->input->post('dateto');
    $datetoconvert = input_format_date($dateto, $date_format_policy);
    $date = "";

    // Date filtering
    if (!empty($datefrom) && !empty($dateto)) {
        $date = " AND (invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:59')";
    }

    // Search filtering
    if ($search) {
        $search = " AND (invoiceCode LIKE '%" . $this->db->escape_like_str($search) . "%' OR srp_erp_customermaster.customerName LIKE '%" . $this->db->escape_like_str($search) . "%')";
    } else {
        $search = "";
    }

    // Sanitize customer and supplier IDs
    if (!empty($customerID)) {
        $customerID = array_map('intval', $customerID);
        $customerIDList = implode(',', $customerID);
    } else {
        $customerIDList = 'NULL'; // to avoid SQL syntax error
    }

    if (!empty($supplierAutoID)) {
        $supplierAutoID = array_map('intval', $supplierAutoID);
        $supplierAutoIDList = implode(',', $supplierAutoID);
    } else {
        $supplierAutoIDList = 'NULL';
    }
    
    $qry = "
        SELECT
            srp_erp_company.company_code,
            srp_erp_company.company_name,
            srp_erp_customerinvoicemaster.invoiceCode,
            srp_erp_customerinvoicemaster.referenceNo,
            srp_erp_customerinvoicemaster.invoiceDate,
            srp_erp_deliveryorderdetails.revenueGLAutoID,
            srp_erp_deliveryorderdetails.revenueGLCode,
            srp_erp_deliveryorderdetails.revenueSystemGLCode,
            srp_erp_deliveryorderdetails.revenueGLDescription,
            srp_erp_contractmaster.customerOrderID,
            srp_erp_suppliermaster.supplierAutoID,
            srp_erp_suppliermaster.supplierSystemCode,
            srp_erp_suppliermaster.secondaryCode,
            srp_erp_suppliermaster.supplierName,
            srp_erp_customermaster.customerAutoID,
            srp_erp_customermaster.customerSystemCode,
            srp_erp_customermaster.secondaryCode AS customersecondaryCode,
            srp_erp_customermaster.customerName,
            srp_erp_customerinvoicemaster.transactionCurrency,
            srp_erp_customerinvoicedetails.transactionAmount,
            srp_erp_customerinvoicemaster.companyLocalCurrency,
            (
                srp_erp_customerinvoicedetails.companyLocalAmount + ROUND(srp_erp_customerinvoicedetails.taxAmount / srp_erp_customerinvoicemaster.companyLocalExchangeRate, 2)
            ) AS companyLocalAmount,
            srp_erp_customerinvoicemaster.createdDateTime,
            srp_erp_customerinvoicemaster.companyFinanceYear,
            srp_erp_customerinvoicemaster.createdUserName 
        FROM
            srp_erp_customerinvoicedetails
            JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicedetails.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID
            JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_customerinvoicemaster.companyID
            JOIN srp_erp_chartofaccounts ON srp_erp_customerinvoicedetails.revenueGLAutoID = srp_erp_chartofaccounts.GLAutoID
            JOIN srp_erp_deliveryorderdetails ON srp_erp_customerinvoicedetails.DOMasterID = srp_erp_deliveryorderdetails.DOAutoID
            JOIN srp_erp_contractmaster ON srp_erp_deliveryorderdetails.contractAutoID = srp_erp_contractmaster.contractAutoID
            JOIN srp_erp_srm_customerordermaster ON srp_erp_contractmaster.customerOrderID = srp_erp_srm_customerordermaster.customerOrderID
            JOIN srp_erp_suppliermaster ON srp_erp_srm_customerordermaster.supplierID = srp_erp_suppliermaster.supplierAutoID
            JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID 
        WHERE
            srp_erp_customerinvoicemaster.companyID = {$companyID}
            AND srp_erp_contractmaster.isBackToBack = 1
            AND srp_erp_customerinvoicemaster.approvedYN = 1
            {$search}
            {$date}
            AND srp_erp_customermaster.customerAutoID IN ({$customerIDList})
            AND srp_erp_suppliermaster.supplierAutoID IN ({$supplierAutoIDList})
    ";

    $output = $this->db->query($qry)->result_array();
    return $output;
}

}