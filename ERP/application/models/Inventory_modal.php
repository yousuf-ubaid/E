<?php

class Inventory_modal extends ERP_Model
{
    function save_material_issue_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $isuDate = $this->input->post('issueDate');
        $issueDate = input_format_date($isuDate, $date_format_policy);
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $location = explode('|', trim($this->input->post('location_dec') ?? ''));
        $requestedLocation = explode('|', trim($this->input->post('requested_location_dec') ?? ''));
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $jobID = $this->input->post('jobID');
        $jobNumber = $this->input->post('jobNumber');
        $check= $this->input->post('reserved');
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        if ($financeyearperiodYN == 1) {
            $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($issueDate);
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
            $financePeriodDetails = get_financial_period_date_wise($issueDate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }

        if($check==1){
            $data['reservedYN'] = $check;
        }else{
            $data['reservedYN'] = 0;
        }
        $data['documentID'] = 'MI';
        $data['issueType'] = trim($this->input->post('issueType') ?? '');
        $data['itemType'] = trim($this->input->post('itemType') ?? '');
        $data['issueDate'] = trim($issueDate);
        $data['issueRefNo'] = trim($this->input->post('issueRefNo') ?? '');
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        //$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        //$data['FYPeriodDateTo'] = trim($period[1] ?? '');
        $data['wareHouseAutoID'] = trim($this->input->post('location') ?? '');
        $data['wareHouseCode'] = trim($location[0] ?? '');
        $data['wareHouseLocation'] = trim($location[1] ?? '');
        $data['wareHouseDescription'] = trim($location[2] ?? '');
        $data['jobID'] = trim($jobID);
        $data['jobNo'] = trim($jobNumber);
        if ($data['issueType'] == 'Material Request') {
            $data['requestedWareHouseAutoID'] = trim($this->input->post('requested_location') ?? '');
            $data['requestedWareHouseCode'] = trim($requestedLocation[0] ?? '');
            $data['requestedWareHouseLocation'] = trim($requestedLocation[1] ?? '');
            $data['requestedWareHouseDescription'] = trim($requestedLocation[2] ?? '');
        }
//        $data['jobNo'] = trim($this->input->post('jobNo') ?? '');
        /* $data['employeeCode'] = trim($Requested[0] ?? '');
         $data['employeeName'] = trim($Requested[1] ?? '');*/
        /*$data['employeeCode'] = trim($Requested[0] ?? '');*/
        if ($data['issueType'] == 'Direct Issue') {
            if ($this->input->post('employeeID')) {
                $Requested = explode('|', trim($this->input->post('requested') ?? ''));
                $data['employeeName'] = trim($Requested[1] ?? '');
                $data['employeeCode'] = trim($Requested[0] ?? '');
                $data['employeeID'] = trim($this->input->post('employeeID') ?? '');
            } else {
                $data['employeeName'] = trim($this->input->post('employeeName') ?? '');
                $data['employeeCode'] = NULL;
                $data['employeeID'] = NULL;
            }
        }
        $data['requestedDate'] = '';
        $narration = ($this->input->post('narration'));
        $data['comment'] = str_replace('<br />', PHP_EOL, $narration);
       // $data['comment'] = trim($this->input->post('narration') ?? '');
        if ($data['issueType'] == 'Material Request') {
            $data['segmentID'] = '';
            $data['segmentCode'] = '';
        } else {
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
        }

        //System generated
        $data['isSystemGenerated'] = ($this->input->post('itemIssueAutoID')) ? $this->input->post('itemIssueAutoID') : 0;


        if (trim($this->input->post('itemIssueAutoID') ?? '')) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
            $this->db->update('srp_erp_itemissuemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Material Issue :  Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Material Issue :  Updated Successfully.', $this->input->post('issueType'));
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('itemIssueAutoID'), 'issueType' => $this->input->post('issueType'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['companyLocalCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['itemIssueCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_itemissuemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Material Issue :   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Material Issue :  Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id, 'issueType' => $this->input->post('issueType'));
            }
        }
    }

    function fetch_batch_details_byId(){
		$this->db->select('srp_erp_inventory_itembatch.*,srp_erp_itemmaster.defaultUnitOfMeasure');
        $this->db->from('srp_erp_inventory_itembatch');
        $this->db->join('srp_erp_itemmaster','srp_erp_inventory_itembatch.itemMasterID = srp_erp_itemmaster.itemAutoID','left');
        $this->db->where('srp_erp_inventory_itembatch.companyId', $this->common_data['company_data']['company_id']);
        $this->db->where('srp_erp_inventory_itembatch.itemMasterID', $this->input->post('itemId'));
        $this->db->where('srp_erp_inventory_itembatch.wareHouseAutoID', $this->input->post('wareHouseAutoID'));
        $this->db->where('srp_erp_inventory_itembatch.qtr !=', 0);
		return $this->db->get()->result_array();
    }

    function save_stock_transfer_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $trfrDate = $this->input->post('tranferDate');
        $tranferDate = input_format_date($trfrDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $form_location = explode('|', trim($this->input->post('form_location_dec') ?? ''));
        $to_location = explode('|', trim($this->input->post('to_location_dec') ?? ''));

        $jobID = $this->input->post('jobID');
        $jobNumber = $this->input->post('jobNumber');
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        if ($financeyearperiodYN == 1) {
            $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($tranferDate);
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
            $financePeriodDetails = get_financial_period_date_wise($tranferDate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
        $data['documentID'] = 'ST';
        $data['transferType'] = trim($this->input->post('transferType') ?? '');
        $data['itemType'] = trim($this->input->post('itemType') ?? '');
        $data['tranferDate'] = trim($tranferDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $narration = ($this->input->post('narration'));
        $data['comment'] = str_replace('<br />', PHP_EOL, $narration);

        //$data['comment'] = trim($this->input->post('narration') ?? '');
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['from_wareHouseAutoID'] = trim($this->input->post('form_location') ?? '');
        $data['form_wareHouseCode'] = trim($form_location[0] ?? '');
        $data['form_wareHouseLocation'] = trim($form_location[1] ?? '');
        $data['form_wareHouseDescription'] = trim($form_location[2] ?? '');
        $data['to_wareHouseAutoID'] = trim($this->input->post('to_location') ?? '');
        $data['to_wareHouseCode'] = trim($to_location[0] ?? '');
        $data['to_wareHouseLocation'] = trim($to_location[1] ?? '');
        $data['to_wareHouseDescription'] = trim($to_location[2] ?? '');
        $data['jobID'] = trim($jobID);
        $data['jobNumber'] = '';
        if(!empty($jobID) || $jobID != 0){
            $data['jobNumber'] = trim($jobNumber);
        }
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('stockTransferAutoID') ?? '')) {
            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
            $this->db->update('srp_erp_stocktransfermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Transfer : ' . $data['form_wareHouseDescription'] . ' - ' . $data['to_wareHouseDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                $this->session->set_flashdata('s', 'Stock Transfer : ' . $data['form_wareHouseDescription'] . ' - ' . $data['to_wareHouseDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('stockTransferAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['companyLocalCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['stockTransferCode'] = 0;
            $this->db->insert('srp_erp_stocktransfermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Transfer : ' . $data['form_wareHouseDescription'] . ' - ' . $data['to_wareHouseDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                // update_warehouse_items();
                // update_item_master();
                $this->session->set_flashdata('s', 'Stock Transfer : ' . $data['form_wareHouseDescription'] . ' - ' . $data['to_wareHouseDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_adjustment_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $stkAdjmntDate = $this->input->post('stockAdjustmentDate');
        $stockAdjustmentDate = input_format_date($stkAdjmntDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $location = explode('|', trim($this->input->post('location_dec') ?? ''));
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        if ($financeyearperiodYN == 1) {
            $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($stockAdjustmentDate);
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
            $financePeriodDetails = get_financial_period_date_wise($stockAdjustmentDate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
        $data['documentID'] = 'SA';
        $data['stockAdjustmentDate'] = trim($stockAdjustmentDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $narration = ($this->input->post('narration'));
        $data['comment'] = str_replace('<br />', PHP_EOL, $narration);
        //$data['comment'] = trim($this->input->post('narration') ?? '');
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['stockAdjustmentType'] = trim($this->input->post('adjustmentType') ?? '');
        $data['adjustmentType'] = trim($this->input->post('adjsType') ?? '');
        $data['wareHouseAutoID'] = trim($this->input->post('location') ?? '');
        $data['wareHouseCode'] = trim($location[0] ?? '');
        $data['wareHouseLocation'] = trim($location[1] ?? '');
        $data['wareHouseDescription'] = trim($location[2] ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('stockAdjustmentAutoID') ?? '')) {
            $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID') ?? ''));
            $this->db->update('srp_erp_stockadjustmentmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Adjustment : ' . $data['wareHouseDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Stock Adjustment : ' . $data['wareHouseDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('stockAdjustmentAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['companyLocalCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['stockAdjustmentCode'] = $this->sequence->sequence_generator($data['documentID']);
            $data['stockAdjustmentCode'] = 0;
            $this->db->insert('srp_erp_stockadjustmentmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Adjustment : ' . $data['wareHouseDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Stock Adjustment : ' . $data['wareHouseDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_return_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $rtrnDate = $this->input->post('returnDate');
        $returnDate = input_format_date($rtrnDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');

        $location = explode('|', trim($this->input->post('location_dec') ?? ''));
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        if ($financeyearperiodYN == 1) {
            $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($returnDate);
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
            $financePeriodDetails = get_financial_period_date_wise($returnDate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplierID') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $data['documentID'] = 'SR';
        $data['returnDate'] = trim($returnDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        //$data['comment'] = trim($this->input->post('narration') ?? '');
        $narration = ($this->input->post('narration'));
        $data['comment'] = str_replace('<br />', PHP_EOL, $narration);

        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['supplierID'] = trim($this->input->post('supplierID') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        //$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        //$data['FYPeriodDateTo'] = trim($period[1] ?? '');
        $data['wareHouseAutoID'] = trim($this->input->post('location') ?? '');
        $data['wareHouseCode'] = trim($location[0] ?? '');
        $data['wareHouseLocation'] = trim($location[1] ?? '');
        $data['wareHouseDescription'] = trim($location[2] ?? '');
        $data['supplierSystemCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierEmail'] = $supplier_arr['supplierEmail'];
        $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data['supplierliabilityType'] = $supplier_arr['liabilityType'];

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

        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('stockReturnAutoID') ?? '')) {
            $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
            $this->db->update('srp_erp_stockreturnmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Return : ' . $data['wareHouseDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Purchase Return : ' . $data['wareHouseDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('stockReturnAutoID'));
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
            //$data['stockReturnCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_stockreturnmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Return : ' . $data['wareHouseDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Purchase Return : ' . $data['wareHouseDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_material_issue_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate');
        $this->db->where('itemIssueAutoID', $this->input->post('itemIssueAutoID'));
        return $this->db->get('srp_erp_itemissuemaster')->row_array();
    }

    function laad_stock_transfer_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS tranferDate, srp_erp_mfq_warehousemaster.mfqWarehouseAutoID AS mfqWarehouseAutoID ');
        $this->db->join('srp_erp_mfq_warehousemaster', 'srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_stocktransfermaster.to_wareHouseAutoID', 'LEFT');
        $this->db->where('stockTransferAutoID', $this->input->post('stockTransferAutoID'));
        return $this->db->get('srp_erp_stocktransfermaster')->row_array();
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function laad_stock_adjustment_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(stockAdjustmentDate,\'' . $convertFormat . '\') AS stockAdjustmentDate');
        $this->db->where('stockAdjustmentAutoID', $this->input->post('stockAdjustmentAutoID'));
        return $this->db->get('srp_erp_stockadjustmentmaster')->row_array();
    }

    function load_stock_return_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS returnDate');
        $this->db->where('stockReturnAutoID', $this->input->post('stockReturnAutoID'));
        return $this->db->get('srp_erp_stockreturnmaster')->row_array();
    }

    function fetch_stockTransfer_detail_table()
    {
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $this->db->select('MRCode, srp_erp_stocktransferdetails.*,srp_erp_stocktransfermaster.from_wareHouseAutoID,srp_erp_stocktransfermaster.to_wareHouseAutoID,srp_erp_activity_code_main.activity_code as activityCodeName,srp_erp_itemmaster.isSubitemExist,srp_erp_stocktransfermaster.transferType as transferType,srp_erp_stocktransfermaster.from_wareHouseAutoID,srp_erp_itemmaster.subItemapplicableon,srp_erp_unit_of_measure.UnitShortCode as secuom,'.$item_code_alias.'');
        $this->db->where('srp_erp_stocktransferdetails.stockTransferAutoID', $this->input->post('stockTransferAutoID'));
        $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_stocktransferdetails.activityCodeID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stocktransferdetails.itemAutoID', 'left');
        $this->db->join('srp_erp_materialrequest mr', 'mr.mrAutoID = srp_erp_stocktransferdetails.mrAutoID', 'LEFT');
        $this->db->join('srp_erp_stocktransfermaster', 'srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_stocktransferdetails.SUOMID', 'left');
        $data = $this->db->get('srp_erp_stocktransferdetails')->result_array();

        if(count($data)>0){
            foreach($data as $key=>$val){

                $this->db->select('srp_erp_warehousebinlocation.Description');
                $this->db->from('srp_erp_itembinlocation');
                $this->db->join('srp_erp_warehousebinlocation', 'srp_erp_warehousebinlocation.binLocationID = srp_erp_itembinlocation.binLocationID','left');
                $this->db->where('srp_erp_itembinlocation.itemAutoID', $val['itemAutoID']);
                $this->db->where('srp_erp_itembinlocation.warehouseAutoID', $val['from_wareHouseAutoID']);
                $data_bin = $this->db->get()->row_array();

                if($data_bin){
                    $data[$key]['binlocation'] = $data_bin['Description'];
                }else{
                    $data[$key]['binlocation'] = 'Not assigned';
                }

            }
        }

        return $data;
    }

    function fetch_stock_adjustment_detail()
    {
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $this->db->select('PLGLAutoID,BLGLAutoID,srp_erp_stockadjustmentdetails.itemSystemCode,srp_erp_stockadjustmentdetails.batchNumber,srp_erp_stockadjustmentdetails.batchExpireDate, srp_erp_stockadjustmentdetails.itemDescription,srp_erp_stockadjustmentdetails.unitOfMeasure,srp_erp_stockadjustmentdetails.previousWareHouseStock,srp_erp_stockadjustmentdetails.previousWac,srp_erp_stockadjustmentmaster.companyLocalCurrencyDecimalPlaces,srp_erp_stockadjustmentdetails.currentWareHouseStock,srp_erp_stockadjustmentdetails.currentWac,srp_erp_stockadjustmentdetails.adjustmentWareHouseStock,srp_erp_stockadjustmentdetails.adjustmentWac,srp_erp_stockadjustmentdetails.totalValue,srp_erp_stockadjustmentdetails.stockAdjustmentDetailsAutoID,srp_erp_stockadjustmentdetails.previousStock, srp_erp_stockadjustmentdetails.currentStock, srp_erp_stockadjustmentmaster.wareHouseAutoID, srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.currentStock as itemcurrentStock,srp_erp_stockadjustmentdetails.noOfItems,srp_erp_stockadjustmentdetails.grossQty,srp_erp_stockadjustmentdetails.noOfUnits,srp_erp_stockadjustmentdetails.deduction,'.$item_code_alias.'');
        $this->db->where('srp_erp_stockadjustmentdetails.stockAdjustmentAutoID', $this->input->post('stockAdjustmentAutoID'));
        $this->db->join('srp_erp_stockadjustmentmaster', 'srp_erp_stockadjustmentdetails.stockAdjustmentAutoID = srp_erp_stockadjustmentmaster.stockAdjustmentAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID= srp_erp_stockadjustmentdetails.itemAutoID', 'left');
        return $this->db->get('srp_erp_stockadjustmentdetails')->result_array();
    }

    function fetch_template_data($itemIssueAutoID)
    {
        /*$convertFormat = convert_date_format_sql();
        $this->db->select('*,,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->from('srp_erp_itemissuemaster');
        $data['master'] = $this->db->get()->row_array();
        $this->db->select('srp_erp_itemissuedetails.*,srp_erp_materialrequest.MRCode');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->join('srp_erp_materialrequest', 'srp_erp_materialrequest.mrAutoID = srp_erp_itemissuedetails.mrAutoID', 'left');
        $this->db->from('srp_erp_itemissuedetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;*/

        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate,(DATE_FORMAT(FYBegin,\'' . $convertFormat . '\')) AS FYbegining,(DATE_FORMAT(FYEnd,\'' . $convertFormat . '\')) AS FYend,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->from('srp_erp_itemissuemaster');
        $data['master'] = $this->db->get()->row_array();
       
        $this->db->select('srp_erp_itemissuedetails.*, FORMAT(qtyIssued, 2) AS qtyIssuedFormated, srp_erp_activity_code_main.activity_code as activityCodeName, srp_erp_materialrequest.MRCode,srp_erp_chartofaccounts.systemAccountCode as systemAccountCode,srp_erp_chartofaccounts.GLDescription as costglname,srp_erp_chartofaccounts.GLDescription as GLDescription,'.$item_code_alias.'');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_itemissuedetails.activityCodeID', 'left');
        $this->db->join('srp_erp_materialrequest', 'srp_erp_materialrequest.mrAutoID = srp_erp_itemissuedetails.mrAutoID', 'left');
        $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_itemissuedetails.PLGLAutoID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_itemissuedetails.itemAutoID', 'left');
        $this->db->from('srp_erp_itemissuedetails');
        $data['detail'] = $this->db->get()->result_array();
        $data['jobClosed'] = 0;
        if($data['master']['jobID']) {
            $data['jobClosed'] = $this->db->query("SELECT closedYN FROM srp_erp_mfq_job WHERE workProcessID = {$data['master']['jobID']}")->row('closedYN');
        }

        return $data;
    }

    function fetch_template_stock_transfer($stockTransferAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
       
       
        $this->db->select('*, DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS tranferDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN 
CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn,from_wareHouseAutoID');
        $this->db->where('stockTransferAutoID', $stockTransferAutoID);
        $this->db->from('srp_erp_stocktransfermaster');
        $data['master'] = $this->db->get()->row_array();
       
       
       
        $this->db->select('*,MRCode,srp_erp_itemmaster.subItemapplicableon,srp_erp_activity_code_main.activity_code as activityCodeName,srp_erp_itemmaster.isSubitemExist,srp_erp_unit_of_measure.UnitShortCode as secuom,'.$item_code_alias.'');
        $this->db->where('stockTransferAutoID', $stockTransferAutoID);
        $this->db->from('srp_erp_stocktransferdetails');
        $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_stocktransferdetails.activityCodeID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_stocktransferdetails.SUOMID', 'LEFT');
        $this->db->join('srp_erp_materialrequest mr', 'mr.mrAutoID = srp_erp_stocktransferdetails.mrAutoID', 'LEFT');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stocktransferdetails.itemAutoID', 'LEFT');
        $data['detail'] = $this->db->get()->result_array();
        $data['jobClosed'] = 0;
        if($data['master']['jobID']) {
            $data['jobClosed'] = $this->db->query("SELECT closedYN FROM srp_erp_mfq_job WHERE workProcessID = {$data['master']['jobID']}")->row('closedYN');
        }

        if(count($data['detail'])>0){
            foreach($data['detail'] as $key=>$val){
                $this->db->select('srp_erp_warehousebinlocation.Description');
                $this->db->from('srp_erp_itembinlocation');
                $this->db->join('srp_erp_warehousebinlocation', 'srp_erp_warehousebinlocation.binLocationID = srp_erp_itembinlocation.binLocationID','left');
                $this->db->where('srp_erp_itembinlocation.itemAutoID', $val['itemAutoID']);
                $this->db->where('srp_erp_itembinlocation.warehouseAutoID', $data['master']['from_wareHouseAutoID']);
                $data_bin = $this->db->get()->row_array();

                if($data_bin){
                    $data['detail'][$key]['binlocation'] = $data_bin['Description'];
                }else{
                    $data['detail'][$key]['binlocation'] = 'Not assigned';
                }
            }
        }
        return $data;
    }

    function fetch_template_stock_return_data($stockReturnAutoID)
    {
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS returnDate,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('stockReturnAutoID', $stockReturnAutoID);
        $this->db->from('srp_erp_stockreturnmaster');
        $data['master'] = $this->db->get()->row_array();

        $this->db->select('srp_erp_stockreturndetails.grvAutoID,stockReturnDetailsID, stockReturnAutoID, type, srp_erp_itemmaster.itemAutoID,
                        concat(IFNULL(srp_erp_stockreturndetails.documentSystemCode,""),
                        IF(documentSystemCode IS NULL,""," - "),'.$item_code.') as itemSystemCode, srp_erp_itemmaster.itemDescription, 
                        srp_erp_stockreturndetails.itemFinanceCategory, srp_erp_stockreturndetails.itemFinanceCategorySub, 
                        srp_erp_stockreturndetails.financeCategory, itemCategory, unitOfMeasureID, unitOfMeasure, defaultUOMID, 
                        defaultUOM conversionRateUOM, return_Qty, ROUND(return_Qty, 2) AS return_QtyFormated, 
                        received_Qty, srp_erp_stockreturndetails.currentStock, 
                        currentWareHouseStock, 
                        IFNULL(srp_erp_taxcalculationformulamaster.Description, " ") as taxDescription,
                        taxAmount,
                        currentlWacAmount as currentlWacAmount, 
                        totalValue as totalValue, 
                        PLGLAutoID, PLSystemGLCode, PLGLCode, PLDescription, PLType, BLGLAutoID, BLSystemGLCode, BLGLCode, BLDescription, BLType, srp_erp_stockreturndetails.comments ');
        $this->db->where('stockReturnAutoID', $stockReturnAutoID);
        $this->db->from('srp_erp_stockreturndetails');
        $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_stockreturndetails.grvAutoID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stockreturndetails.itemAutoID', 'left');
        $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_stockreturndetails.taxCalculationformulaID', 'left');
        $data['detail'] = $this->db->get()->result_array();
        //var_dump($data['detail']);
        return $data;
    }

    function fetch_template_stock_adjustment($stockAdjustmentAutoID)
    {
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,DATE_FORMAT(stockAdjustmentDate,\'' . $convertFormat . '\') AS stockAdjustmentDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN 
CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('stockAdjustmentAutoID', $stockAdjustmentAutoID);
        $this->db->from('srp_erp_stockadjustmentmaster');
        $data['master'] = $this->db->get()->row_array();
       
        $this->db->select('*,srp_erp_stockadjustmentdetails.currentWareHouseStock as currentWareHouseStockadjustment,srp_erp_stockadjustmentdetails.currentStock as currentStockadjustment,'.$item_code_alias.'');
        $this->db->where('stockAdjustmentAutoID', $stockAdjustmentAutoID);
        $this->db->from('srp_erp_stockadjustmentdetails');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_stockadjustmentdetails.itemAutoID','left');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function save_material_detail()
    {
        $projectExist = project_is_exist();
        $companyID = current_companyID();
        
        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        if($this->input->post('activityCode')){
            $activityCodeID = $this->input->post('activityCode');
        }

        $this->db->select('cca.GLAutoID as GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory');
        $this->db->from('srp_erp_companycontrolaccounts cca');
        $this->db->join('srp_erp_chartofaccounts ca', 'cca.GLAutoID = ca.GLAutoID', 'LEFT');
        $this->db->where('controlAccountType', 'GIT');
        $this->db->where('cca.companyID', $this->common_data['company_data']['company_id']);
        $materialRequestGlDetail = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $masterRecord = $this->db->get()->row_array();

        $mfqWarehouseAutoID = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster WHERE warehouseAutoID = {$masterRecord['wareHouseAutoID']} AND companyID = {$companyID}")->row('mfqWarehouseAutoID');

        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        if (!empty($this->input->post('itemIssueDetailID'))) {
            $this->db->select('itemIssueAutoID,,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_itemissuedetails');
            $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
            if ($masterRecord['issueType'] == 'Material Request') {
                $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
            }
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->where('itemIssueDetailID !=', trim($this->input->post('itemIssueDetailID') ?? ''));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }

        if ($masterRecord['issueType'] == 'Material Request') {
            if (!empty($this->input->post('itemIssueDetailID'))) {
                $this->db->select('mrAutoID,,qtyRequested');
                $this->db->from('srp_erp_itemissuedetails');
                $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
                $this->db->where('itemIssueDetailID', trim($this->input->post('itemIssueDetailID') ?? ''));
                $req_QTY = $this->db->get()->row_array();

                if (!empty($req_QTY)) {
                    $mrid = $req_QTY['mrAutoID'];
                    $itmid = $this->input->post('itemAutoID');
                    $itmDid = $this->input->post('itemIssueDetailID');
                    $issuedQTY = $this->db->query("SELECT
	SUM(qtyIssued) as qtyIssued
FROM
	srp_erp_itemissuedetails

WHERE
	srp_erp_itemissuedetails.mrAutoID = $mrid
AND srp_erp_itemissuedetails.itemAutoID = $itmid
AND srp_erp_itemissuedetails.companyID = $companyID
AND srp_erp_itemissuedetails.itemIssueDetailID!= $itmDid
")->row_array();
                    $qtyrequested = $req_QTY['qtyRequested'] - $issuedQTY['qtyIssued'];
                    if (!empty($req_QTY['mrAutoID']) && $req_QTY['mrAutoID'] > 0 && $qtyrequested < $this->input->post('quantityRequested')) {
                        return array('w', 'Qty cannot be grater than balance qty');
                    }
                }

            }
        }

        $this->db->trans_start();
        $segment = explode('|', trim($this->input->post('a_segment') ?? ''));
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
        $projectID = trim($this->input->post('projectID') ?? '');
        $data['itemIssueAutoID'] = trim($this->input->post('itemIssueAutoID') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');
        }

        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy==1){
            $batch_number1 = $this->input->post('batch_number');
            $arraydata1 = implode(',',$batch_number1);
            $data['batchNumber'] = $arraydata1;

        }

        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['qtyIssued'] = trim($this->input->post('quantityRequested') ?? '');
        if($advanceCostCapturing == 1){
            $data['activityCodeID'] = $activityCodeID;
        }
        $data['comments'] = trim($this->input->post('comment') ?? '');
        $data['remarks'] = trim($this->input->post('remarks') ?? '');
        $data['currentWareHouseStock'] = trim($this->input->post('currentWareHouseStockQty') ?? '');
        if ($masterRecord['issueType'] == 'Material Request') {
            $data['segmentID'] = '';
            $data['segmentCode'] = '';
        } else {
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
        }
        $data['itemFinanceCategory'] = $item_data['subcategoryID'];
        $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
        $data['financeCategory'] = $item_data['financeCategory'];
        $data['itemCategory'] = $item_data['mainCategory'];
        $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
        $data['currentStock'] = $item_data['currentStock'];
        if ($masterRecord['issueType'] == 'Material Request') {

            $data['PLGLAutoID'] = $materialRequestGlDetail['GLAutoID'];
            $data['PLSystemGLCode'] = $materialRequestGlDetail['systemAccountCode'];
            $data['PLGLCode'] = $materialRequestGlDetail['GLSecondaryCode'];
            $data['PLDescription'] = $materialRequestGlDetail['GLDescription'];
            $data['PLType'] = $materialRequestGlDetail['subCategory'];

            if($mfqWarehouseAutoID) {
                $wipGLDesc=fetch_gl_account_desc($this->common_data['controlaccounts']['WIP']);
                $data['BLGLAutoID'] = $this->common_data['controlaccounts']['WIP'];
                $data['BLSystemGLCode'] = $wipGLDesc['systemAccountCode'];
                $data['BLGLCode'] = $wipGLDesc['GLSecondaryCode'];
                $data['BLDescription'] = $wipGLDesc['GLDescription'];
                $data['BLType'] = $wipGLDesc['subCategory'];
            } else {
                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            }
        } else {
            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                if($mfqWarehouseAutoID) {
                    $wipGLDesc=fetch_gl_account_desc($this->common_data['controlaccounts']['WIP']);
                    $data['BLGLAutoID'] = $this->common_data['controlaccounts']['WIP'];
                    $data['BLSystemGLCode'] = $wipGLDesc['systemAccountCode'];
                    $data['BLGLCode'] = $wipGLDesc['GLSecondaryCode'];
                    $data['BLDescription'] = $wipGLDesc['GLDescription'];
                    $data['BLType'] = $wipGLDesc['subCategory'];
                } else {
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                }
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
        }
        $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyIssued'] / $data['conversionRateUOM']));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('itemIssueDetailID') ?? '')) {
            $this->db->where('itemIssueDetailID', trim($this->input->post('itemIssueDetailID') ?? ''));
            $this->db->update('srp_erp_itemissuedetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Item Issue Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Item Issue Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_itemissuedetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Item Issue Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Item Issue Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_material_detail_multiple()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $itemIssueDetailID = $this->input->post('itemIssueDetailID');
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $projectID = $this->input->post('projectID');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $quantityRequested = $this->input->post('quantityRequested');
        $a_segment = $this->input->post('a_segment');
        if($this->input->post('activityCode')){
            $activityCodeID = $this->input->post('activityCode');
        }
        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        $comment = $this->input->post('comment');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $isSystemGenerated = $this->input->post('isSystemGenerated');
        $this->db->trans_start();
        $companyID = current_companyID();
        $issueMaster = $this->db->query("SELECT * FROM srp_erp_itemissuemaster WHERE itemIssueAutoID = {$itemIssueAutoID}")->row_array();
        $mfqWarehouseAutoID = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster WHERE warehouseAutoID = {$issueMaster['wareHouseAutoID']} AND companyID = {$companyID}")->row('mfqWarehouseAutoID');

       

        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$itemIssueDetailID) {
                $this->db->select('itemIssueAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_itemissuedetails');
                $this->db->where('itemIssueAutoID', $itemIssueAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

            $data['itemIssueAutoID'] = trim($this->input->post('itemIssueAutoID') ?? '');
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];
            }

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if( $itemBatchPolicy==1 && $isSystemGenerated != 1){

                    $batch_number2 = $this->input->post("batch_number[{$key}]");
                    $arraydata2 = implode(",",$batch_number2);
                    $data['batchNumber'] = $arraydata2;
            }

            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['qtyIssued'] = $quantityRequested[$key];
            $data['comments'] = $comment[$key];
            $data['remarks'] = '';
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            if($advanceCostCapturing == 1){
                $data['activityCodeID'] = $activityCodeID[$key];
            }
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

               // if($mfqWarehouseAutoID) {
                   // $wipGLDesc=fetch_gl_account_desc($this->common_data['controlaccounts']['WIP']);
                    //$data['BLGLAutoID'] = $this->common_data['controlaccounts']['WIP'];
                    //$data['BLSystemGLCode'] = $wipGLDesc['systemAccountCode'];
                    //$data['BLGLCode'] = $wipGLDesc['GLSecondaryCode'];
                    //$data['BLDescription'] = $wipGLDesc['GLDescription'];
                    //$data['BLType'] = $wipGLDesc['subCategory'];
               // } else {
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                //}
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyIssued'] / $data['conversionRateUOM']));
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_itemissuedetails', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item Issue Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Issue Detail :  Saved Successfully.');

        }

    }

    function save_return_item_detail()
    {
        if (!trim($this->input->post('stockReturnDetailsID') ?? '')) {
            $this->db->select('stockReturnAutoID');
            $this->db->from('srp_erp_stockreturndetails');
            $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $order_detail = $this->db->get()->result_array();

            if (!empty($order_detail)) {
                $this->session->set_flashdata('w', 'Item Issue Detail : ' . trim($this->input->post('itemCode') ?? '') . '  already exists.');
                return array('status' => false);
            }
        }
        $this->db->trans_start();
        $segment = explode('|', trim($this->input->post('a_segment') ?? ''));
        $data['stockReturnAutoID'] = trim($this->input->post('stockReturnAutoID') ?? '');
        $data['itemSystemCode'] = trim($this->input->post('itemSystemCode') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        $data['itemDescription'] = trim($this->input->post('itemDescription') ?? '');
        $data['unitOfMeasure'] = trim($this->input->post('unitOfMeasure') ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID') ?? '');
        $data['defaultUOMID'] = trim($this->input->post('defaultUOMID') ?? '');
        $data['defaultUOM'] = trim($this->input->post('defaultUOM') ?? '');
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['return_Qty'] = trim($this->input->post('return_Qty') ?? '');
        $data['comments'] = trim($this->input->post('comment') ?? '');
        $data['type'] = 'Item';
        $data['currentStock'] = trim($this->input->post('currentStock') ?? '');
        $data['currentWareHouseStock'] = trim($this->input->post('currentWareHouseStockQty') ?? '');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');

        if (trim($this->input->post('stockReturnDetailsID') ?? '')) {
            $this->db->where('stockReturnDetailsID', trim($this->input->post('stockReturnDetailsID') ?? ''));
            $this->db->update('srp_erp_stockreturndetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Return Item Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Return Item Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
                return array('status' => true, 'last_id' => $this->input->post('itemIssueDetailID'));
            }
        } else {
            $item_data = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];
            //$data['currentWareHouseStock']  = $item_data['companyLocalWacAmount'];
            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
            $data['totalValue'] = ($data['currentlWacAmount'] * $data['return_Qty']);

            $this->db->insert('srp_erp_stockreturndetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Return Item Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Return Item Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_adjustment_detail()
    {
        if (!trim($this->input->post('stockAdjustmentDetailsAutoID') ?? '')) {
            $this->db->select('stockAdjustmentDetailsAutoID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_stockadjustmentdetails');
            $this->db->where('stockAdjustmentDetailsAutoID', trim($this->input->post('stockAdjustmentDetailsAutoID') ?? ''));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $order_detail = $this->db->get()->result_array();
            if (!empty($order_detail)) {
                return array('w', 'Stock Adjustment Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->trans_start();
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $projectID = trim($this->input->post('projectID') ?? '');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $this->db->select('wareHouseAutoID,adjustmentType');
        $this->db->where('stockAdjustmentAutoID', $this->input->post('stockAdjustmentAutoID'));
        $stockadjustmentMaster = $this->db->get('srp_erp_stockadjustmentmaster')->row_array();
        $segment = explode('|', trim($this->input->post('a_segment') ?? ''));
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
        $this->db->select('currentStock');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $prevItemMasterTotal = $this->db->get()->row_array();
        $previousWareHouseStock = $this->db->query("SELECT currentStock FROM srp_erp_warehouseitems WHERE wareHouseAutoID='{$stockadjustmentMaster['wareHouseAutoID']}' and itemAutoID='{$this->input->post('itemAutoID')}'")->row_array(); //get warehouse stock of the item by location
        $data['stockAdjustmentAutoID'] = trim($this->input->post('stockAdjustmentAutoID') ?? '');
        $data['itemSystemCode'] = trim($this->input->post('itemSystemCode') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $project_categoryID;
            $data['project_subCategoryID'] = $project_subCategoryID;
        }
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['itemFinanceCategory'] = $item_data['subcategoryID'];
        $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
        $data['financeCategory'] = $item_data['financeCategory'];
        $data['itemCategory'] = $item_data['mainCategory'];
        if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
            $data['PLGLAutoID'] = $item_data['stockAdjustmentGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['stockAdjustmentSystemGLCode'];
            $data['PLGLCode'] = $item_data['stockAdjustmentGLCode'];
            $data['PLDescription'] = $item_data['stockAdjustmentDescription'];
            $data['PLType'] = $item_data['stockAdjustmentType'];
            $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
            $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['BLGLCode'] = $item_data['assteGLCode'];
            $data['BLDescription'] = $item_data['assteDescription'];
            $data['BLType'] = $item_data['assteType'];
        } elseif ($data['financeCategory'] == 2) {
            $data['PLGLAutoID'] = $item_data['costGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['PLGLCode'] = $item_data['costGLCode'];
            $data['PLDescription'] = $item_data['costDescription'];
            $data['PLType'] = $item_data['costType'];
            $data['BLGLAutoID'] = '';
            $data['BLSystemGLCode'] = '';
            $data['BLGLCode'] = '';
            $data['BLDescription'] = '';
            $data['BLType'] = '';
        }
        $data['previousStock'] = trim($prevItemMasterTotal['currentStock'] ?? '');
        $data['previousWac'] = trim($this->input->post('currentWac') ?? '');
        $data['previousWareHouseStock'] = $previousWareHouseStock["currentStock"];
        $data['currentWac'] = trim($this->input->post('adjustment_wac') ?? '');
        $data['batchNumber'] = trim($this->input->post('batchNumber') ?? '');
        $data['batchExpireDate'] = trim($this->input->post('expireDate') ?? '');
        $data['batchCurrentQty'] = trim($this->input->post('batch_adjustment_Stock') ?? '');
        $data['currentWareHouseStock'] = trim($this->input->post('adjustment_Stock') ?? '');
        $data['adjustmentWac'] = (trim($this->input->post('adjustment_wac') ?? '') - trim($this->input->post('currentWac') ?? ''));
        $data['adjustmentWareHouseStock'] = (trim($this->input->post('adjustment_Stock') ?? '') - trim($this->input->post('currentWareHouseStock') ?? ''));
        //$data['adjustmentStock'] = (trim($this->input->post('adjustment_Stock') ?? '') - trim($this->input->post('currentWareHouseStock') ?? ''));
        if ($stockadjustmentMaster['adjustmentType'] == 1) {
            $data['adjustmentStock'] = 0;
        } else {
            $data['adjustmentStock'] = (trim($this->input->post('adjustment_Stock') ?? '') - trim($this->input->post('currentWareHouseStock') ?? ''));
        }
        //$data['currentStock'] = $data['adjustmentStock']+$data['previousStock'];
        $data['currentStock'] = $prevItemMasterTotal['currentStock'] + $data['adjustmentStock'];
        $previousTotal = ($data['previousStock'] * $data['previousWac']);
        $newTotal = ($data['currentStock'] * $data['currentWac']);
        $data['totalValue'] = ($data['currentStock'] * $data['currentWac']) - ($data['previousStock'] * $data['previousWac']);
        $data['comments'] = trim($this->input->post('comments') ?? '');

        if (trim($this->input->post('stockAdjustmentDetailsAutoID') ?? '')) {
            $this->db->where('stockAdjustmentDetailsAutoID', trim($this->input->post('stockAdjustmentDetailsAutoID') ?? ''));
            $this->db->update('srp_erp_stockadjustmentdetails', $data);

            /** item master Sub codes*/
            $detailsAutoID = $this->input->post('stockAdjustmentDetailsAutoID');

            /* 1---- delete all entries in the update process - item master sub temp */
            $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentDetailID' => $detailsAutoID, 'receivedDocumentID' => 'SA'));
            /* 2----  update all selected sub item list */
            if ($item_data['isSubitemExist'] == 1) {

                if ($data['previousStock'] < $data['currentStock']) {
                    /* Add Stock */
                    $qty = $data['adjustmentStock'];
                    $last_id = $detailsAutoID;
                    $documentAutoID = $data['stockAdjustmentAutoID'];

                    $subData['uom'] = $data['unitOfMeasure'];
                    $subData['uomID'] = $data['unitOfMeasureID'];
                    $subData['grv_detailID'] = $last_id;
                    $subData['warehouseAutoID'] = $stockadjustmentMaster['wareHouseAutoID'];
                    $this->add_sub_itemMaster_tmpTbl($qty, $data['itemAutoID'], $documentAutoID, $last_id, 'SA', $item_data['itemSystemCode'], $subData);
                }


            }

            /* 3---- update all selected values */

            $setData['isSold'] = null;
            $setData['soldDocumentID'] = null;
            $setData['soldDocumentAutoID'] = null;
            $setData['soldDocumentDetailID'] = null;

            $ware['soldDocumentID'] = 'SA';
            $ware['soldDocumentDetailID'] = $detailsAutoID;

            $this->db->update('srp_erp_itemmaster_sub', $setData, $ware);


            /** end item master Sub codes*/


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
            }
        } else {
            /* We are not using this method : there is a bulk insert method used to add the item..  */
            $this->db->insert('srp_erp_stockadjustmentdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_adjustment_detail_multiple()
    {
        $stockAdjustmentDetailsAutoID = $this->input->post('stockAdjustmentDetailsAutoID');
        $stockAdjustmentAutoID = $this->input->post('stockAdjustmentAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $batchNumber = $this->input->post('batchNumber');
        $expireDate=$this->input->post('expireDate');
        $batch_adjustment_Stock=$this->input->post('batch_adjustment_Stock');
        $currentWareHouseStock = $this->input->post('currentWareHouseStock');
        $currentWac = $this->input->post('currentWac');
        $projectID = $this->input->post('projectID');
        $adjustment_Stock = $this->input->post('adjustment_Stock');
        $adjustment_wac = $this->input->post('adjustment_wac');
        $a_segment = $this->input->post('a_segment');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $defaultUOMid = $this->input->post('defaultUOMid');
        $defaultUOM = $this->input->post('defaultUOM');

        $projectExist = project_is_exist();
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];

        $this->db->select('wareHouseAutoID, wareHouseCode, wareHouseLocation, wareHouseDescription,adjustmentType');
        $this->db->where('stockAdjustmentAutoID', $this->input->post('stockAdjustmentAutoID'));
        $stockadjustmentMaster = $this->db->get('srp_erp_stockadjustmentmaster')->row_array();


        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$stockAdjustmentDetailsAutoID) {
                $this->db->select('stockAdjustmentDetailsAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stockadjustmentdetails');
                $this->db->where('stockAdjustmentAutoID', $stockAdjustmentAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Adjustment Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

            $previousWareHouseStock = $this->db->query("SELECT currentStock FROM srp_erp_warehouseitems WHERE wareHouseAutoID='{$stockadjustmentMaster['wareHouseAutoID']}' and itemAutoID='{$itemAutoID}'")->row_array(); //get warehouse stock of the item by location

            $this->db->select('currentStock,companyLocalWacAmount,(currentStock * companyLocalWacAmount) as prevItemMasterTotal');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $prevItemMasterTotal = $this->db->get()->row_array();

            $data['stockAdjustmentAutoID'] = $stockAdjustmentAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];

            }

            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);

            // if($UnitOfMeasureID[$key] != $item_data['defaultUnitOfMeasureID']){
            //     $currentWareHouseStock[$key] = $currentWareHouseStock[$key] * $data['conversionRateUOM'];
            //     $adjustment_Stock[$key] = $adjustment_Stock[$key] * $data['conversionRateUOM'];
            //     $batch_adjustment_Stock[$key] = $batch_adjustment_Stock[$key] * $data['conversionRateUOM'];
            //     $uom[$key] = $item_data['defaultUnitOfMeasure'].' | '.$item_data['defaultUnitOfMeasure'];
            // }

            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['batchNumber'] = $batchNumber[$key] ?? 0;
            $data['batchExpireDate'] = $expireDate[$key] ?? '';
            $data['batchCurrentQty'] = $batch_adjustment_Stock[$key];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {

                $data['PLGLAutoID'] = $item_data['stockAdjustmentGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['stockAdjustmentSystemGLCode'];
                $data['PLGLCode'] = $item_data['stockAdjustmentGLCode'];
                $data['PLDescription'] = $item_data['stockAdjustmentDescription'];
                $data['PLType'] = $item_data['stockAdjustmentType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];

            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
            //$data['previousStock'] = isset($prevItemMasterTotal['currentStock']) && !empty($prevItemMasterTotal['currentStock']) ? $prevItemMasterTotal['currentStock'] : 0;
            $data['previousStock'] = $prevItemMasterTotal['currentStock'];
            $data['previousWac'] = $currentWac[$key];
            $data['previousWareHouseStock'] = isset($previousWareHouseStock["currentStock"]) && !empty($previousWareHouseStock["currentStock"]) ? $previousWareHouseStock["currentStock"] : 0;
            $data['currentWac'] = $adjustment_wac[$key];

            if ($stockadjustmentMaster['adjustmentType'] == 1) {
                $data['currentWareHouseStock'] = isset($previousWareHouseStock["currentStock"]) && !empty($previousWareHouseStock["currentStock"]) ? $previousWareHouseStock["currentStock"] : 0;
            } else {
                $data['currentWareHouseStock'] = $adjustment_Stock[$key];
            }
            $data['adjustmentWac'] = ($adjustment_wac[$key] - $currentWac[$key]);
            if ($stockadjustmentMaster['adjustmentType'] == 1) {
                $data['adjustmentWareHouseStock'] = 0;
            } else {
                $data['adjustmentWareHouseStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            }
            //$data['adjustmentWareHouseStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            if ($stockadjustmentMaster['adjustmentType'] == 1) {
                $data['adjustmentStock'] = 0;
            } else {
                $data['adjustmentStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            }
            //$data['adjustmentStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            $data['currentStock'] = $prevItemMasterTotal['currentStock'] + $data['adjustmentStock'];

            //print_r($data);

            $previousTotal = ($data['previousStock'] * $data['previousWac']);
            $newTotal = ($data['currentStock'] * $data['currentWac']);


            /*$prevItemMasterStock = $prevItemMasterTotal["currentStock"];
            $prevItemMasterWac = $prevItemMasterTotal["companyLocalWacAmount"];
            $prevItemMasterTotal = $prevItemMasterTotal["prevItemMasterTotal"];
            $total = (($data['adjustmentStock'] + $prevItemMasterStock) * $data['currentWac']) - $prevItemMasterTotal;*/

            $prevItemMasterStock = $currentWareHouseStock[$key];
            $prevItemMasterWac = $currentWac[$key];
            $adjustmentStock = $adjustment_Stock[$key];
            $adjustmentWac = $adjustment_wac[$key];
            $total = (($adjustmentStock * $adjustmentWac) - ($prevItemMasterStock * $prevItemMasterWac));

            //$data['totalValue'] = ($newTotal - $previousTotal);
            $data['totalValue'] = ($data['currentStock'] * $data['currentWac']) - ($data['previousStock'] * $data['previousWac']);
            $data['comments'] = '';

            $this->db->insert('srp_erp_stockadjustmentdetails', $data);
            $last_id = $this->db->insert_id();

            if ($item_data['mainCategory'] == 'Inventory' || $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $data['itemAutoID']);
                $this->db->where('wareHouseAutoID', $stockadjustmentMaster['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                if (empty($warehouseitems)) {
                    if ($data['previousWareHouseStock'] == 0) {
                        $data_arr = array(
                            'wareHouseAutoID' => $stockadjustmentMaster['wareHouseAutoID'],
                            'wareHouseLocation' => $stockadjustmentMaster['wareHouseLocation'],
                            'wareHouseDescription' => $stockadjustmentMaster['wareHouseDescription'],
                            'itemAutoID' => $data['itemAutoID'],
                            'barCodeNo' => $item_data['barcode'],
                            'salesPrice' => $item_data['companyLocalSellingPrice'],
                            'ActiveYN' => $item_data['isActive'],
                            'itemSystemCode' => $data['itemSystemCode'],
                            'itemDescription' => $data['itemDescription'],
                            'unitOfMeasureID' => $data['unitOfMeasureID'],
                            'unitOfMeasure' => $data['unitOfMeasure'],
                            'currentStock' => 0,
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'companyCode' => $this->common_data['company_data']['company_code'],
                        );

                        $this->db->insert('srp_erp_warehouseitems', $data_arr);
                    }

                }
            }


            /*sub item master config : multiple add scanario */
            $adjustedStock = $data['adjustmentStock'];

            if ($item_data['isSubitemExist'] == 1) {

                if ($data['previousStock'] < $data['currentStock']) {
                    /* Add Stock */
                    $qty = $adjustedStock;

                    $subData['uom'] = $data['unitOfMeasure'];
                    $subData['uomID'] = $data['unitOfMeasureID'];
                    $subData['grv_detailID'] = $last_id;
                    $subData['warehouseAutoID'] = $stockadjustmentMaster['wareHouseAutoID'];
                    $this->add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $stockAdjustmentAutoID, $last_id, 'SA', $item_data['itemSystemCode'], $subData);
                }

            }

            /*end of sub item master config */

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Stock Adjustment Details : Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 's', 'Stock Adjustment Details : Saved Successfully.');
        }
    }

    function save_stock_transfer_detail()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $projectExist = project_is_exist();
        if (!empty($this->input->post('stockTransferDetailsID'))) {
            $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_stocktransferdetails');
            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->where('stockTransferDetailsID !=', trim($this->input->post('stockTransferDetailsID') ?? ''));
            $order_detail = $this->db->get()->row_array();

            if (!empty($order_detail)) {
                return array('w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->trans_start();
        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        if($this->input->post('activityCode')){
            $activityCodeID = $this->input->post('activityCode');
        }
        $segment = explode('|', trim($this->input->post('a_segment') ?? ''));
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
        $projectID = trim($this->input->post('projectID') ?? '');
        $data['stockTransferAutoID'] = trim($this->input->post('stockTransferAutoID') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        $data['projectID'] = trim($this->input->post('projectID') ?? '');
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $project_categoryID;
            $data['project_subCategoryID'] = $project_subCategoryID;

        }

        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy==1){
            $batch_number1 = $this->input->post('batch_number');
            $arraydata1 = implode(',',$batch_number1);
            $data['batchNumber'] = $arraydata1;

        }

        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['SUOMQty'] = $this->input->post('SUOMQty');
        $data['SUOMID'] = $this->input->post('SUOMIDhn');
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['transfer_QTY'] = trim($this->input->post('transfer_QTY') ?? '');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        if($advanceCostCapturing == 1){
            $data['activityCodeID'] = $activityCodeID;
        }
        $data['currentWareHouseStock'] = trim($this->input->post('currentWareHouseStockQty') ?? '');
        // $data['modifiedPCID']            = $this->common_data['current_pc'];
        // $data['modifiedUserID']          = $this->common_data['current_userID'];
        // $data['modifiedUserName']        = $this->common_data['current_user'];
        // $data['modifiedDateTime']        = $this->common_data['current_date'];

        $this->db->select('to_wareHouseAutoID,to_wareHouseLocation,to_wareHouseDescription');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
        $master = $this->db->get()->row_array();

        $this->db->select('itemAutoID');
        $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

        if (empty($warehouseitems)) {
            $data_arr = array(
                'wareHouseAutoID' => $master['to_wareHouseAutoID'],
                'wareHouseLocation' => $master['to_wareHouseLocation'],
                'wareHouseDescription' => $master['to_wareHouseDescription'],
                'itemAutoID' => $data['itemAutoID'],
                'barCodeNo' => $item_data['barcode'],
                'salesPrice' => $item_data['companyLocalSellingPrice'],
                'ActiveYN' => $item_data['isActive'],
                'itemSystemCode' => $data['itemSystemCode'],
                'itemDescription' => $data['itemDescription'],
                'unitOfMeasureID' => $data['defaultUOMID'],
                'unitOfMeasure' => $data['defaultUOM'],
                'currentStock' => 0,
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
            );
            $this->db->insert('srp_erp_warehouseitems', $data_arr);
        }

        if (trim($this->input->post('stockTransferDetailsID') ?? '')) {
            $this->db->where('stockTransferDetailsID', trim($this->input->post('stockTransferDetailsID') ?? ''));
            $this->db->update('srp_erp_stocktransferdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
            }
        } else {
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];
            $data['totalValue'] = ($data['currentlWacAmount'] * $data['transfer_QTY']);

            $this->db->insert('srp_erp_stocktransferdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_transfer_detail_multiple()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $stockTransferDetailsID = $this->input->post('stockTransferDetailsID');
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $transfer_QTY = $this->input->post('transfer_QTY');
        $projectID = $this->input->post('projectID');
        $a_segment = $this->input->post('a_segment');
        $SUOMIDhn = $this->input->post('SUOMIDhn');
        $SUOMQty = $this->input->post('SUOMQty');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        if($this->input->post('activityCode')){
            $activityCodeID = $this->input->post('activityCode');
        }
        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            if (!$stockTransferDetailsID) {
                $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('stockTransferAutoID', $stockTransferAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();

                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);
            $data['stockTransferAutoID'] = $stockTransferAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
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

            $data['SUOMID'] = $SUOMIDhn[$key];
            $data['SUOMQty'] = $SUOMQty[$key];
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['transfer_QTY'] = $transfer_QTY[$key];
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            if($advanceCostCapturing == 1){
                $data['activityCodeID'] = $activityCodeID[$key];
            }
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];

            $this->db->select('to_wareHouseAutoID,to_wareHouseLocation,to_wareHouseDescription,from_wareHouseAutoID');
            $this->db->from('srp_erp_stocktransfermaster');
            $this->db->where('stockTransferAutoID', $stockTransferAutoID);
            $master = $this->db->get()->row_array();

            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['from_wareHouseAutoID']);
            $fromWarehouseGl = $this->db->get()->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $toWarehouseGl = $this->db->get()->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $master['to_wareHouseAutoID'],
                    'wareHouseLocation' => $master['to_wareHouseLocation'],
                    'wareHouseDescription' => $master['to_wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'barCodeNo' => $item_data['barcode'],
                    'salesPrice' => $item_data['companyLocalSellingPrice'],
                    'ActiveYN' => $item_data['isActive'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }

            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            if ($fromWarehouseGl['warehouseType'] == 2) {
                $data['fromWarehouseType'] = 2;
                $data['fromWarehouseWIPGLAutoID'] = $fromWarehouseGl['WIPGLAutoID'];
            }

            if ($toWarehouseGl['warehouseType'] == 2) {
                $data['toWarehouseType'] = 2;
                $data['toWarehouseWIPGLAutoID'] = $toWarehouseGl['WIPGLAutoID'];
            }

            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];
            $data['totalValue'] = ($data['currentlWacAmount'] * $data['transfer_QTY']);
            $this->db->insert('srp_erp_stocktransferdetails', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Stock Transfer Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 's', 'Stock Transfer Detail :  Saved Successfully.');
        }

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

    function fetch_item_detail($id)
    {
        $this->db->select('*');
        $this->db->where('itemAutoID', $id);
        $this->db->from('srp_erp_itemmaster');
        return $this->db->get()->row_array();
    }

    function fetch_material_item_detail()
    {
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        $data['detail'] = $this->db->query("SELECT
	srp_erp_itemissuedetails.*, srp_erp_itemmaster.isSubitemExist, srp_erp_activity_code_main.activity_code as activityCodeName, 
	srp_erp_itemissuemaster.wareHouseAutoID,
srp_erp_warehouseitems.currentStock as stock,srp_erp_materialrequest.MRCode as MRCode,
srp_erp_chartofaccounts.GLDescription as costglname,$item_code_alias,srp_erp_itemmaster.itemSystemCode as itemSystemCodeeditall,srp_erp_itemmaster.seconeryItemCode as seconeryItemCodeedditall
FROM
	srp_erp_itemissuedetails
LEFT JOIN srp_erp_activity_code_main ON srp_erp_activity_code_main.id = srp_erp_itemissuedetails.activityCodeID
LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemissuedetails.itemAutoID
LEFT JOIN srp_erp_itemissuemaster ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID
LEFT JOIN srp_erp_materialrequest ON srp_erp_materialrequest.mrAutoID = srp_erp_itemissuedetails.mrAutoID
LEFT JOIN srp_erp_chartofaccounts on srp_erp_chartofaccounts.GLAutoID = srp_erp_itemissuedetails.PLGLAutoID
JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_itemissuedetails.itemAutoID
AND srp_erp_itemissuemaster.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID
WHERE
	srp_erp_itemissuedetails.itemIssueAutoID = '$itemIssueAutoID' ")->result_array();

    
        $this->db->select('issueType');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->from('srp_erp_itemissuemaster');
        $data['issueType'] = $this->db->get()->row('issueType');   

        return $data;
    }

    function fetch_return_direct_details()
    {
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        
            $item_code = 'srp_erp_itemmaster.itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'srp_erp_itemmaster.seconeryItemCode';
        }
        $this->db->select('concat(IFNULL(srp_erp_stockreturndetails.documentSystemCode,""),IF(documentSystemCode IS NULL,""," - "),'.$item_code.') as itemSystemCode,
                            srp_erp_stockreturndetails.itemDescription,srp_erp_stockreturndetails.unitOfMeasure,
                            srp_erp_stockreturnmaster.transactionCurrencyDecimalPlaces,
                            (srp_erp_stockreturndetails.currentlWacAmount + (IFNULL(srp_erp_stockreturndetails.taxAmount, 0)/srp_erp_stockreturndetails.return_Qty)) as currentlWacAmount,
                            (srp_erp_stockreturndetails.currentlWacAmount) as currentlWacAmountTaxGroupEnable,
                            ROUND(srp_erp_stockreturndetails.return_Qty, 2) AS return_Qty, 
                            srp_erp_stockreturndetails.totalValue + IFNULL(srp_erp_stockreturndetails.taxAmount, 0) AS totalValue,
                            srp_erp_stockreturndetails.totalValue  AS totalValueTaxGroupEnable,
                            srp_erp_stockreturndetails.stockReturnDetailsID,srp_erp_stockreturndetails.type, 
                            srp_erp_itemmaster.isSubitemExist,srp_erp_stockreturnmaster.wareHouseAutoID,
                            IFNULL(taxAmount,0) as taxAmount,
                            IFNULL(srp_erp_taxcalculationformulamaster.Description, " ") AS Description,
                            srp_erp_stockreturndetails.grvDetailAutoID,
                            srp_erp_stockreturndetails.grvAutoID,
                            srp_erp_stockreturndetails.stockReturnDetailsID
                            ');
        $this->db->from('srp_erp_stockreturndetails');
        $this->db->where('srp_erp_stockreturndetails.stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
        $this->db->join('srp_erp_stockreturnmaster', 'srp_erp_stockreturndetails.stockReturnAutoID = srp_erp_stockreturnmaster.stockReturnAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stockreturndetails.itemAutoID', 'left');
        $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_stockreturndetails.taxCalculationformulaID', 'left');
        //$this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_stockreturndetails.grvAutoID', 'left');
        $data['detail'] = $this->db->get()->result_array();

        /*grvPrimaryCode,*/
        return $data;
    }

    function load_material_item_detail()
    {
        $itemIssueDetailID = $this->input->post('itemIssueDetailID');
        $result = $this->db->query("SELECT
	srp_erp_itemissuedetails.*, srp_erp_warehouseitems.currentStock AS Stock,srp_erp_itemmaster.seconeryItemCode,srp_erp_itemmaster.mainCategory
FROM
	srp_erp_itemissuedetails
JOIN srp_erp_itemissuemaster ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID
JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_itemissuedetails.itemAutoID
AND srp_erp_itemissuemaster.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID
LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemissuedetails.itemAutoID
WHERE
	itemIssueDetailID = '$itemIssueDetailID'")->row_array();
        return $result;
    }

    function load_stock_transfer_item_detail()
    {
        $this->db->select('st.stockTransferDetailsID,st.batchNumber,activityCodeID,st.bucketWeightID,w.currentStock as wareHouseStock,st.itemAutoID,st.itemDescription,st.itemSystemCode, st.defaultUOMID,st.unitOfMeasureID,st.transfer_QTY,st.segmentID,st.segmentCode,st.projectID,st.noOfItems,st.grossQty,st.noOfUnits,st.deduction,srp_erp_unit_of_measure.UnitShortCode as secuom,srp_erp_unit_of_measure.UnitDes as secuomdec,st.SUOMQty,st.SUOMID,st.project_categoryID as project_categoryID,st.project_subCategoryID as project_subCategoryID,srp_erp_itemmaster.seconeryItemCode,st.conversionRateUOM as conversionRateUOM,srp_erp_itemmaster.mainCategory as mainCategory');
        $this->db->from('srp_erp_stocktransferdetails st');
        $this->db->join('srp_erp_warehouseitems w', 'w.itemAutoID = st.itemAutoID');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = st.SUOMID', 'left');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = st.itemAutoID','Left');
        $this->db->where('wareHouseAutoID', trim($this->input->post('location') ?? ''));
        $this->db->where('stockTransferDetailsID', trim($this->input->post('stockTransferDetailsID') ?? ''));
        return $this->db->get()->row_array();
    }

    function delete_material_item()
    {
        $id = $this->input->post('itemIssueDetailID');

        $this->db->select('*');
        $this->db->from('srp_erp_itemissuedetails');
        $this->db->where('itemIssueDetailID', $id);
        $detail_arr = $this->db->get()->row_array();

        /** update sub item master */

        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $detail_arr['itemIssueAutoID']);
        $this->db->where('soldDocumentDetailID', $detail_arr['itemIssueDetailID']);
        $this->db->where('soldDocumentID', 'MI');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);


        /** end update sub item master */
        $this->db->where('itemIssueDetailID', $id);
        $result = $this->db->delete('srp_erp_itemissuedetails');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }

    function load_adjustment_item_detail()
    {
        $this->db->select('sa.stockAdjustmentDetailsAutoID,sa.unitOfMeasureID,sa.conversionRateUOM,sa.batchNumber,sa.batchExpireDate,sa.batchCurrentQty,sa.bucketWeightID,sa.itemDescription,sa.itemSystemCode,w.currentStock as wareHouseStock,sa.currentWac as currentWacstock,im.companyLocalWacAmount as LocalWacAmount,sa.defaultUOMID,sa.segmentID,sa.segmentCode,sa.currentStock,sa.currentWac,sa.itemAutoID,sa.projectID,sa.adjustmentStock as adjustmentStock,sa.previousWareHouseStock as previousWareHouseStock,im.currentStock as itemcurrentStock,sa.noOfItems,sa.grossQty,sa.noOfUnits,sa.deduction,sa.project_subCategoryID as project_subCategoryID,sa.project_categoryID as project_categoryID,im.seconeryItemCode');
        $this->db->from('srp_erp_stockadjustmentdetails sa');
        $this->db->join('srp_erp_warehouseitems w', 'w.itemAutoID = sa.itemAutoID');
        $this->db->join('srp_erp_itemmaster im', 'im.itemAutoID = sa.itemAutoID');
        $this->db->where('stockAdjustmentDetailsAutoID', trim($this->input->post('stockAdjustmentDetailsAutoID') ?? ''));
        $this->db->where('wareHouseAutoID', trim($this->input->post('location') ?? ''));
        return $this->db->get()->row_array();
    }

    function delete_adjustment_item()
    {
        $id = $this->input->post('stockAdjustmentDetailsAutoID');
        $this->db->where('stockAdjustmentDetailsAutoID', $id);
        $result = $this->db->delete('srp_erp_stockadjustmentdetails');
        $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentDetailID' => $id, 'receivedDocumentID' => 'SA'));

        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }

    function delete_material_issue_header()
    {
        /*$this->db->where('itemIssueAutoID', $this->input->post('itemIssueAutoID'));
        $result = $this->db->delete('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', $this->input->post('itemIssueAutoID'));
        $result = $this->db->delete('srp_erp_itemissuedetails');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }*/

        /*  $this->db->select('*');
          $this->db->from('srp_erp_itemissuedetails');
          $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
          $datas = $this->db->get()->row_array();
          if ($datas) {
              $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
              return true;
          } else {
              $data = array(
                  'isDeleted' => 1,
                  'deletedEmpID' => current_userID(),
                  'deletedDate' => current_date(),
              );*/

        $masterID = trim($this->input->post('itemIssueAutoID') ?? '');
        $this->db->select('*');
        $this->db->from('srp_erp_itemissuedetails');
        $this->db->where('itemIssueAutoID', $masterID);
        $datas = $this->db->get()->row_array();

        if (!empty($datas)) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {

            $documentCode = $this->db->get_where('srp_erp_itemissuemaster', ['itemIssueAutoID' => $masterID])->row('itemIssueCode');

            $this->db->trans_start();

            $length = strlen($documentCode);
            if ($length > 1) {
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('itemIssueAutoID', $masterID);
                $this->db->update('srp_erp_itemissuemaster', $data);

            } else {
                $this->db->where('itemIssueAutoID', $masterID)->delete('srp_erp_itemissuedetails');
                $this->db->where('itemIssueAutoID', $masterID)->delete('srp_erp_itemissuemaster');
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            } else {
                $this->session->set_flashdata('e', 'Error in delete process.');

                return false;
            }
        }
    }

    function material_item_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $companyID = current_companyID();
        $currentuser = current_userID();
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');
        $autoApproveDoc = $this->input->post('autoApproveDoc');

        $this->db->select('reservedYN');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $this->db->from('srp_erp_itemissuemaster');
        $isReserved = $this->db->get()->row_array();

        if($isReserved['reservedYN']==1){
            return array('error' => 2, 'message' => 'This document is reserved. To confirm, make it unreserved.');
        }

        $this->db->select('itemIssueAutoID');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $this->db->from('srp_erp_itemissuedetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('itemIssueAutoID');
            $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_itemissuemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->db->select('itemIssueCode,documentID,DATE_FORMAT(issueDate, "%Y") as invYear,DATE_FORMAT(issueDate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
                $this->db->from('srp_erp_itemissuemaster');
                $master_dt = $this->db->get()->row_array();

                $this->load->library('sequence');
                if ($master_dt['itemIssueCode'] == "0" || empty($master_dt['itemIssueCode'])) {
                    if ($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location == '')) {
                            return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                        } else {
                            if ($locationemployee != '') {
                                $codegeratormi = $this->sequence->sequence_generator_location($master_dt['documentID'], $master_dt['companyFinanceYearID'], $locationemployee, $master_dt['invYear'], $master_dt['invMonth']);
                            } else {
                                return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                            }
                        }
                    } else {
                        $codegeratormi = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }

                    $validate_code = validate_code_duplication($codegeratormi, 'itemIssueCode', trim($this->input->post('itemIssueAutoID') ?? ''),'itemIssueAutoID', 'srp_erp_itemissuemaster');
                    if(!empty($validate_code)) {
                        return array('error' => 1, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                    }

                    $pvCd = array(
                        'itemIssueCode' => $codegeratormi
                    );
                    $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
                    $this->db->update('srp_erp_itemissuemaster', $pvCd);
                } else {
                    $validate_code = validate_code_duplication($master_dt['itemIssueCode'], 'itemIssueCode', trim($this->input->post('itemIssueAutoID') ?? ''),'itemIssueAutoID', 'srp_erp_itemissuemaster');
                    if(!empty($validate_code)) {
                        return array('error' => 1, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                }

                $this->load->library('Approvals');
                $this->db->select('itemIssueAutoID, itemIssueCode,issueDate');
                $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
                $this->db->from('srp_erp_itemissuemaster');
                $app_data = $this->db->get()->row_array();

                //  $sql = "SELECT(srp_erp_itemissuedetails.qtyIssued / srp_erp_itemissuedetails.conversionRateUOM) AS qty,srp_erp_warehouseitems.itemAutoID,srp_erp_warehouseitems.currentStock,(srp_erp_warehouseitems.currentStock - (srp_erp_itemissuedetails.qtyIssued / srp_erp_itemissuedetails.conversionRateUOM)) AS stock FROM srp_erp_itemissuedetails INNER JOIN srp_erp_itemissuemaster  ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_itemissuedetails.itemAutoID AND srp_erp_itemissuemaster.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where srp_erp_itemissuedetails.itemIssueAutoID = '{$this->input->post('itemIssueAutoID')}' Having stock < 0";
                /* $sql = "SELECT 	
                        TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(( ( srp_erp_itemissuedetails.qtyIssued / srp_erp_itemissuedetails.conversionRateUOM )), 2 )))))) AS qty,srp_erp_warehouseitems.itemAutoID,
                        TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((srp_erp_warehouseitems.currentStock), 2 )))))) AS currentStock,
                        TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((srp_erp_warehouseitems.currentStock - ( srp_erp_itemissuedetails.qtyIssued / srp_erp_itemissuedetails.conversionRateUOM ))), 2 )))))) AS stock
                        FROM srp_erp_itemissuedetails INNER JOIN srp_erp_itemissuemaster  ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_itemissuedetails.itemAutoID AND srp_erp_itemissuemaster.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where srp_erp_itemissuedetails.itemIssueAutoID = '{$this->input->post('itemIssueAutoID')}' Having stock < 0"; */
                
                $sql = "SELECT 	
                    TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(( ( srp_erp_itemissuedetails.qtyIssued / srp_erp_itemissuedetails.conversionRateUOM )), 2 )))))) AS qty,srp_erp_itemissuedetails.itemAutoID,
                    TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((ware_house.currentStock), 2 )))))) AS currentStock,pq.stock as parkQty, 
                    TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((ware_house.currentStock - ((IFNULL(pq.stock,0) ) +( srp_erp_itemissuedetails.qtyIssued / srp_erp_itemissuedetails.conversionRateUOM )))), 2 )))))) AS stock
                    FROM srp_erp_itemissuedetails 
                    INNER JOIN srp_erp_itemissuemaster  ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
                    LEFT JOIN ( SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID 
                    FROM srp_erp_itemledger WHERE companyID = {$companyID} GROUP BY wareHouseAutoID, itemAutoID ) 
                    AS ware_house ON ware_house.itemAutoID = srp_erp_itemissuedetails.itemAutoID AND srp_erp_itemissuemaster.wareHouseAutoID = ware_house.wareHouseAutoID
                    LEFT JOIN (
                        SELECT
                            SUM( stock ) AS stock,t1.ItemAutoID ,wareHouseAutoID
                        FROM
                            (
                            SELECT
                                IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
                                itemAutoID ,
                                srp_erp_stockadjustmentmaster.wareHouseAutoID as wareHouseAutoID
                            FROM
                                srp_erp_stockadjustmentmaster
                                LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
                            WHERE
                                companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
                                itemAutoID ,
                                srp_erp_stockcountingmaster.wareHouseAutoID AS wareHouseAutoID
                            FROM
                                srp_erp_stockcountingmaster
                                LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
                            WHERE
                                companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock,
                                itemAutoID ,
                                srp_erp_itemissuemaster.wareHouseAutoID AS wareHouseAutoID
                            FROM
                                srp_erp_itemissuemaster
                                LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
                            WHERE
                                srp_erp_itemissuemaster.companyID = {$companyID} AND srp_erp_itemissuemaster.itemIssueAutoID != '{$itemIssueAutoID}' AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                ( requestedQty / conversionRateUOM ) AS stock,
                                itemAutoID,
                                srp_erp_customerreceiptdetail.wareHouseAutoID AS wareHouseAutoID
                            FROM
                                srp_erp_customerreceiptmaster
                                LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                            WHERE
                                srp_erp_customerreceiptmaster.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                ( requestedQty / conversionRateUOM ) AS stock,
                                itemAutoID ,
                                srp_erp_customerinvoicedetails.wareHouseAutoID AS wareHouseAutoID
                            FROM
                                srp_erp_customerinvoicemaster
                                LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
                            WHERE
                                srp_erp_customerinvoicemaster.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                ( deliveredQty / conversionRateUOM ) AS stock,
                                itemAutoID ,
                                srp_erp_deliveryorderdetails.wareHouseAutoID AS wareHouseAutoID 
                            FROM
                                srp_erp_deliveryorder
                                LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
                            WHERE
                                srp_erp_deliveryorder.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                            SELECT
                                ( transfer_QTY / conversionRateUOM ) AS stock,
                                itemAutoID ,
                                srp_erp_stocktransfermaster.from_wareHouseAutoID  AS from_wareHouseAutoID
                            
                            FROM
                                srp_erp_stocktransfermaster
                                LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
                            WHERE
                                srp_erp_stocktransfermaster.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' 
                            ) t1 
                        GROUP BY
                            t1.wareHouseAutoID,t1.ItemAutoID
                    ) as pq ON pq.ItemAutoID = srp_erp_itemissuedetails.itemAutoID  AND pq.wareHouseAutoID = srp_erp_itemissuemaster.wareHouseAutoID
                    
                    where srp_erp_itemissuedetails.itemIssueAutoID = '{$itemIssueAutoID}' Having stock < 0";
                

                $item_low_qty = $this->db->query($sql)->result_array();
                if (!empty($item_low_qty)) {
                    /*$this->session->set_flashdata('w', 'Some Item quantities are not sufficient to confirm this transaction');
                    return array('status' => false, 'data' => 'Some Item quantities are not sufficient to confirm this transaction');*/
                    return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction!', 'itemAutoID' => $item_low_qty);
                }

                /** item Master Sub check */
                $documentDetailID = trim($this->input->post('itemIssueAutoID') ?? '');
                $validate = $this->validate_itemMasterSub($documentDetailID, 'MI');
                /** end of item master sub */

                if ($validate) {
                    $autoApproval = get_document_auto_approval('MI');
                    if ($autoApproval == 0 || $autoApproveDoc == 1) {
                        $approvals_status = $this->approvals->auto_approve($app_data['itemIssueAutoID'], 'srp_erp_itemissuemaster', 'itemIssueAutoID', 'MI', $app_data['itemIssueCode'], $app_data['issueDate']);
                    } elseif ($autoApproval == 1) {
                        $approvals_status = $this->approvals->CreateApproval('MI', $app_data['itemIssueAutoID'], $app_data['itemIssueCode'], 'Material issue', 'srp_erp_itemissuemaster', 'itemIssueAutoID', 0, $app_data['issueDate']);
                    } else {
                        return array('error' => 1, 'message' => 'Approval levels are not set for this document');
                    }

                    if ($approvals_status == 1) {
                        $autoApproval = get_document_auto_approval('MI');
                        $batchNumberPolicy = getPolicyValues('IB', 'All');
                        $updatedBatchNumberArray=[];

                        if($batchNumberPolicy==1){

                            $this->db->select('srp_erp_itemissuedetails.qtyIssued AS requestedQty,srp_erp_itemissuedetails.conversionRateUOM,srp_erp_itemissuedetails.batchNumber,srp_erp_itemissuedetails.itemAutoID,srp_erp_itemissuemaster.wareHouseAutoID');
                            $this->db->from('srp_erp_itemissuedetails');
                            $this->db->where('srp_erp_itemissuedetails.itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
                            $this->db->join('srp_erp_itemissuemaster', 'srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID');
                            $material_results = $this->db->get()->result_array();

                            $updatedBatchNumberArray=update_item_batch_number_details($material_results);

                        }

                        if ($autoApproval == 0) {
                            $result = $this->save_material_issue_approval(0, $app_data['itemIssueAutoID'], 1, 'Auto Approved',$updatedBatchNumberArray);
                            if ($result) {
                                $this->db->trans_commit();
                                return array('error' => 0, 'message' => 'Document Confirmed Successfully.');
                            }
                        } else {
                            $data = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user']
                            );
                            $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
                            $this->db->update('srp_erp_itemissuemaster', $data);

                            $wacRecalculationEnableYN = getPolicyValues('WACR','All');
                            if($wacRecalculationEnableYN == 0){ 
                                reupdate_companylocalwac('srp_erp_itemissuedetails',trim($this->input->post('itemIssueAutoID') ?? ''),'itemIssueAutoID','currentlWacAmount');
                                $itemissueAutoId = trim($this->input->post('itemIssueAutoID') ?? '');
                                
                                $this->db->query("UPDATE srp_erp_itemissuedetails JOIN(
                                    SELECT 
                                    currentlWacAmount*(qtyIssued/conversionRateUOM) as totalvalrecal,
                                    itemIssueDetailID
                                    FROM 
                                    srp_erp_itemissuedetails
                                    where 
                                    companyID = $companyID
                                    AND itemIssueAutoID  = $itemissueAutoId)wactotal ON  wactotal.itemIssueDetailID = srp_erp_itemissuedetails.itemIssueDetailID 
                                    SET srp_erp_itemissuedetails.totalValue = wactotal.totalvalrecal");
                            }
                           

                            
                            return array('error' => 0, 'message' => 'Document Confirmed Successfully.');
                        }
                    } else if ($approvals_status == 3) {
                        return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document');
                    } else {
                        return array('error' => 1, 'message' => 'Document confirmation failed!');
                    }
                } else {
                    return array('error' => 1, 'message' => 'Please complete sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                }
            }
        }
    }

    function stock_transfer_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        $companyID = current_companyID();
        $currentuser = current_userID();
        $employeelocation = $this->common_data['emplanglocationid'];
        $stockTransferAutoID=trim($this->input->post('stockTransferAutoID') ?? '');
        $this->db->select('stockTransferAutoID');
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
        $this->db->from('srp_erp_stocktransferdetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('stockTransferAutoID');
            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_stocktransfermaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->load->library('Approvals');
                $this->db->select('stockTransferAutoID, stockTransferCode,tranferDate');
                $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
                $this->db->from('srp_erp_stocktransfermaster');
                $app_data = $this->db->get()->row_array();
                //echo $app_data;

                //$sql = "SELECT(ssrp_erp_stocktransferdetails.transfer_QTY / srp_erp_stocktransferdetails.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,srp_erp_warehouseitems.itemAutoID,(srp_erp_warehouseitems.currentStock - (srp_erp_stocktransferdetails.transfer_QTY / srp_erp_stocktransferdetails.conversionRateUOM)) AS stock FROM srp_erp_stocktransferdetails INNER JOIN srp_erp_stocktransfermaster  ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_stocktransferdetails.itemAutoID AND srp_erp_stocktransfermaster.from_wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where srp_erp_stocktransferdetails.stockTransferAutoID = '{$this->input->post('stockTransferAutoID')}' Having stock < 0";
                $sql = "SELECT
                            srp_erp_stocktransferdetails.itemAutoID,
                            TRIM(	TRAILING '.' FROM(TRIM(TRAILING 0 FROM((ROUND(( ware_house.currentStock ), 2 )))))) AS currentStock,
                            TRIM(	TRAILING '.' FROM(TRIM(TRAILING 0 FROM((ROUND(( ( srp_erp_stocktransferdetails.transfer_QTY / srp_erp_stocktransferdetails.conversionRateUOM )), 2 )))))) AS qty,
                            TRIM(	TRAILING '.' FROM(TRIM(TRAILING 0 FROM	((ROUND((( ware_house.currentStock - ( (IFNULL(pq.stock,0))+(srp_erp_stocktransferdetails.transfer_QTY / srp_erp_stocktransferdetails.conversionRateUOM )))), 2 )))))) AS stock
            
                        FROM
                            srp_erp_stocktransferdetails
                            INNER JOIN srp_erp_stocktransfermaster ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID
                            -- INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_stocktransferdetails.itemAutoID 
                            -- AND srp_erp_stocktransfermaster.from_wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID 
                            LEFT JOIN ( SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID FROM srp_erp_itemledger WHERE companyID = {$companyID} GROUP BY wareHouseAutoID, itemAutoID ) AS ware_house ON ware_house.itemAutoID = srp_erp_stocktransferdetails.itemAutoID 	AND srp_erp_stocktransfermaster.from_wareHouseAutoID = ware_house.wareHouseAutoID
                            LEFT JOIN (
                            SELECT
                                SUM( stock ) AS stock, t1.ItemAutoID, wareHouseAutoID 
                            FROM
                                (
                                SELECT
                                    IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock, itemAutoID,  srp_erp_stockadjustmentmaster.wareHouseAutoID AS wareHouseAutoID 
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
                                    companyID = {$companyID}   AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock, itemAutoID, srp_erp_itemissuemaster.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_itemissuemaster
                                    LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
                                WHERE
                                    srp_erp_itemissuemaster.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( requestedQty / conversionRateUOM ) AS stock, itemAutoID, srp_erp_customerreceiptdetail.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_customerreceiptmaster
                                    LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                                WHERE
                                    srp_erp_customerreceiptmaster.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( requestedQty / conversionRateUOM ) AS stock, itemAutoID, srp_erp_customerinvoicedetails.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_customerinvoicemaster
                                    LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
                                WHERE
                                    srp_erp_customerinvoicemaster.companyID = {$companyID} AND approvedYN != 1  AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( deliveredQty / conversionRateUOM ) AS stock, itemAutoID, srp_erp_deliveryorderdetails.wareHouseAutoID AS wareHouseAutoID 
                                FROM
                                    srp_erp_deliveryorder
                                    LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
                                WHERE
                                    srp_erp_deliveryorder.companyID = {$companyID}  AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                                SELECT
                                    ( transfer_QTY / conversionRateUOM ) AS stock, itemAutoID, srp_erp_stocktransfermaster.from_wareHouseAutoID AS from_wareHouseAutoID 
                                FROM
                                    srp_erp_stocktransfermaster
                                    LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
                                WHERE
                                    srp_erp_stocktransfermaster.companyID = {$companyID}  
                                    AND srp_erp_stocktransfermaster.stockTransferAutoID !='{$stockTransferAutoID}'
                                    AND approvedYN != 1 
                                    AND itemCategory = 'Inventory' 
                                ) t1 
                            GROUP BY
                                t1.wareHouseAutoID,
                                t1.ItemAutoID 
                            ) AS pq ON pq.ItemAutoID = srp_erp_stocktransferdetails.itemAutoID 
                            AND pq.wareHouseAutoID = srp_erp_stocktransfermaster.from_wareHouseAutoID 
                        WHERE
                            srp_erp_stocktransferdetails.stockTransferAutoID = '{$stockTransferAutoID}' 
                        HAVING
                            stock < 0";
                //echo $sql;
                
                $item_low_qty = $this->db->query($sql)->result_array();
                if (!empty($item_low_qty)) {
                    /*$this->session->set_flashdata('w', 'Some Item quantities are not sufficient to confirm this transaction');
                    return array('status' => false, 'data' => 'Some Item quantities are not sufficient to confirm this transaction');*/
                    return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction.', 'itemAutoID' => $item_low_qty);
                }

                /** item Master Sub check */
                $documentDetailID = trim($this->input->post('stockTransferAutoID') ?? '');
                $validate = $this->validate_itemMasterSub($documentDetailID, 'ST');
                /** end of item master sub */

                if ($validate) {
                    $this->db->select('documentID, stockTransferCode,DATE_FORMAT(tranferDate, "%Y") as invYear,DATE_FORMAT(tranferDate, "%m") as invMonth,companyFinanceYearID,tranferDate');
                    $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
                    $this->db->from('srp_erp_stocktransfermaster');
                    $master_dt = $this->db->get()->row_array();
                    $this->load->library('sequence');
                    $stockCode = $master_dt['stockTransferCode'];
                    if ($master_dt['stockTransferCode'] == "0") {
                        if ($locationwisecodegenerate == 1) {

                            $this->db->select('locationID');
                            $this->db->where('EIdNo', $currentuser);
                            $this->db->where('Erp_companyID', $companyID);
                            $this->db->from('srp_employeesdetails');
                            $location = $this->db->get()->row_array();
                            if ((empty($location)) || ($location == '')) {
                                return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                            } else {
                                if ($employeelocation != '') {
                                    $codegeratorstocktransfer = $this->sequence->sequence_generator_location($master_dt['documentID'], $master_dt['companyFinanceYearID'], $employeelocation, $master_dt['invYear'], $master_dt['invMonth']);
                                } else {
                                    return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                                }
                            }
                        } else {
                            $codegeratorstocktransfer = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                        }

                        $validate_code = validate_code_duplication($codegeratorstocktransfer, 'stockTransferCode', trim($this->input->post('stockTransferAutoID') ?? ''),'stockTransferAutoID', 'srp_erp_stocktransfermaster');
                        if(!empty($validate_code)) {
                            return array('error' => 2, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                        }

                        $pvCd = array(
                            'stockTransferCode' => $codegeratorstocktransfer
                        );
                        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
                        $this->db->update('srp_erp_stocktransfermaster', $pvCd);

                        $stockCode = $pvCd['stockTransferCode'];
                    } else {
                        $validate_code = validate_code_duplication($master_dt['stockTransferCode'], 'stockTransferCode', trim($this->input->post('stockTransferAutoID') ?? ''),'stockTransferAutoID', 'srp_erp_stocktransfermaster');
                        if(!empty($validate_code)) {
                            return array('error' => 2, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                        }
                    }

                    $autoApproval = get_document_auto_approval('ST');
                    if ($autoApproval == 0) {
                        $approvals_status = $this->approvals->auto_approve($app_data['stockTransferAutoID'], 'srp_erp_stocktransfermaster', 'stockTransferAutoID', 'ST', $stockCode, $app_data['tranferDate']);
                    } elseif ($autoApproval == 1) {
                        $approvals_status = $this->approvals->CreateApproval('ST', $app_data['stockTransferAutoID'], $stockCode, 'Stock Transfer', 'srp_erp_stocktransfermaster', 'stockTransferAutoID', 0, $app_data['tranferDate']);
                    } else {
                        return array('error' => 1, 'message' => 'Approval levels are not set for this document');
                    }

                    if ($approvals_status == 1) {
                        $autoApproval = get_document_auto_approval('ST');

                        $batchNumberPolicy = getPolicyValues('IB', 'All');
                        $updatedBatchNumberArray=[];

                        if($batchNumberPolicy==1){

                            $this->db->select('srp_erp_stocktransferdetails.transfer_QTY AS requestedQty,srp_erp_stocktransferdetails.conversionRateUOM,srp_erp_stocktransferdetails.batchNumber,srp_erp_stocktransferdetails.itemAutoID,srp_erp_stocktransfermaster.from_wareHouseAutoID,srp_erp_stocktransfermaster.to_wareHouseAutoID');
                            $this->db->from('srp_erp_stocktransferdetails');
                            $this->db->where('srp_erp_stocktransferdetails.stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
                            $this->db->join('srp_erp_stocktransfermaster', 'srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID');
                            $material_results = $this->db->get()->result_array();                          

                            $updatedBatchNumberArray=$this->update_item_batch_number_details_stock_transfer($material_results);

                            if(count($updatedBatchNumberArray)>0){
                                $this->update_stock_tranfer_batch($updatedBatchNumberArray);
                            }

                        }

                        if ($autoApproval == 0) {
                            $result = $this->save_stock_transfer_approval(0, $app_data['stockTransferAutoID'], 1, 'Auto Approved',$updatedBatchNumberArray);
                           
                            if ($result) {
                                return array('error' => 0, 'message' => 'Document Confirmed Successfully.');
                            }
                        } else {
                            $data = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user']
                            );
                            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
                            $this->db->update('srp_erp_stocktransfermaster', $data);
                            if($wacRecalculationEnableYN == 0){
                            reupdate_companylocalwac('srp_erp_stocktransferdetails',trim($this->input->post('stockTransferAutoID') ?? ''),'stockTransferAutoID','currentlWacAmount','ST');
                            $stocktransferId = trim($this->input->post('stockTransferAutoID') ?? '');
                            $this->db->query("UPDATE srp_erp_stocktransferdetails JOIN(
                                SELECT 
                                currentlWacAmount*(transfer_QTY/conversionRateUOM) as totalvalrecal,
                                stockTransferDetailsID
                                FROM 
                                srp_erp_stocktransferdetails
                                where 
                               stockTransferAutoID  = $stocktransferId)wactotal ON  wactotal.stockTransferDetailsID = srp_erp_stocktransferdetails.stockTransferDetailsID 
                                SET srp_erp_stocktransferdetails.totalValue = wactotal.totalvalrecal");

                            }
                          
                            return array('error' => 0, 'message' => 'Stock Transfer Approval Successfully.');
                        }
                    } else if ($approvals_status == 3) {
                        return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document');
                    } else {
                        return array('error' => 1, 'message' => 'Document confirmation failed');
                    }
                } else {
                    return array('error' => 1, 'message' => 'Please complete sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                }
            }
        }
    }

    function update_stock_tranfer_batch($array){
        foreach($array AS $val){

                $this->db->select("*");
                $this->db->where('itemMasterID',  $val['itemAutoID']);
                $this->db->where('batchNumber', $val['batchNumber']);
                $this->db->where('wareHouseAutoID', $val['to_wareHouseAutoID']);
                $this->db->where('companyId',$this->common_data['company_data']['company_id']);
                $batchitems = $this->db->get('srp_erp_inventory_itembatch')->row_array();

                if (empty($batchitems)) {
                    $CI =& get_instance();
                    $CI->db->select("*");
                    $CI->db->from('srp_erp_inventory_itembatch');
                    $results_data_batch = count($CI->db->get()->result_array());
        
                    $data_batch['batchNumber']=$val['batchNumber'];
                    $data_batch['batchCode']="BIL".(100001+$results_data_batch);
                    $data_batch['qtr']=$val['qty'];
                    $data_batch['itemMasterID']=$val['itemAutoID'];
                    $data_batch['companyId']=$this->common_data['company_data']['company_id'];
                    $data_batch['createdUserID']=$this->common_data['current_userID'];
                    $data_batch['createdDateTime']=$this->common_data['current_date'];
                    $data_batch['wareHouseAutoID']=  $val['to_wareHouseAutoID'];
                    $data_batch['status']=3;
        
                    $this->db->insert('srp_erp_inventory_itembatch', $data_batch);

                }else{
                    $data_batch['qtr'] = $batchitems['qtr']+$val['qty'];
                    $data_batch['batchExpireDate'] = $batchitems['batchExpireDate'];
                    
                    $this->db->where('itemMasterID',  $val['itemAutoID']);
                    $this->db->where('batchNumber', $val['batchNumber']);
                    $this->db->where('wareHouseAutoID', $val['to_wareHouseAutoID']);
                    $this->db->where('companyId',$this->common_data['company_data']['company_id']);
                    $this->db->update('srp_erp_inventory_itembatch', $data_batch);

                }
        
        }
    }

    function update_item_batch_number_details_stock_transfer($invoiceArray)
    {
        $CI =& get_instance();

        $resultArray=array();

        if(count($invoiceArray)>0){

            foreach($invoiceArray as $invoice){

                $batch_number2= explode(",",$invoice['batchNumber']);

                $requestedQtyWithUOM=$invoice['requestedQty']/$invoice['conversionRateUOM'];

                if(count($batch_number2)>0){
                    $balanceRequestQtr=0;

                    foreach($batch_number2 AS $key1 => $val){
                        $CI->db->select('*');
                        $CI->db->where('itemMasterID',  $invoice['itemAutoID']);
                        $CI->db->where('wareHouseAutoID',  $invoice['from_wareHouseAutoID']);
                        $CI->db->where('batchNumber', $val);
                        $CI->db->where('companyId',$CI->common_data['company_data']['company_id']);
                        $batchitems = $CI->db->get('srp_erp_inventory_itembatch')->row_array();

                        if( $batchitems){

                            if($batchitems['qtr']>0){
                                
                                if($balanceRequestQtr===0){
                                    if($requestedQtyWithUOM <= $batchitems['qtr']){
                                        $data_batch['qtr'] = $batchitems['qtr']-$requestedQtyWithUOM;
                                        $CI =& get_instance();
                                        $CI->db->where('itemMasterID',  $invoice['itemAutoID']);
                                        $CI->db->where('wareHouseAutoID',  $invoice['from_wareHouseAutoID']);
                                        $CI->db->where('batchNumber', $val);
                                        $CI->db->where('companyId',$CI->common_data['company_data']['company_id']);
                                        $CI->db->update('srp_erp_inventory_itembatch', $data_batch);

                                        $resultArray[]=['batchNumber'=>$val,'qty'=>$requestedQtyWithUOM,'itemAutoID'=>$invoice['itemAutoID'],'from_wareHouseAutoID'=>$invoice['from_wareHouseAutoID'],'to_wareHouseAutoID'=>$invoice['to_wareHouseAutoID']];

                                        break;
                                    }else{

                                        $data_batch['qtr'] = 0;
                                        $CI->db->where('itemMasterID',  $invoice['itemAutoID']);
                                        $CI->db->where('wareHouseAutoID',  $invoice['from_wareHouseAutoID']);
                                        $CI->db->where('batchNumber', $val);
                                        $CI->db->where('companyId',$CI->common_data['company_data']['company_id']);
                                        $CI->db->update('srp_erp_inventory_itembatch', $data_batch);
                                        $resultArray[]=['batchNumber'=>$val,'qty'=>$batchitems['qtr'],'itemAutoID'=>$invoice['itemAutoID'],'from_wareHouseAutoID'=>$invoice['from_wareHouseAutoID'],'to_wareHouseAutoID'=>$invoice['to_wareHouseAutoID']];
                                        $balanceRequestQtr=$requestedQtyWithUOM-$batchitems['qtr'];

                                    }
                                }else{

                                    if($balanceRequestQtr <= $batchitems['qtr']){
                                        
                                        $data_batch['qtr'] = $batchitems['qtr']-$balanceRequestQtr;
                                        $CI->db->where('itemMasterID',  $invoice['itemAutoID']);
                                        $CI->db->where('wareHouseAutoID',  $invoice['from_wareHouseAutoID']);
                                        $CI->db->where('batchNumber', $val);
                                        $CI->db->where('companyId',$CI->common_data['company_data']['company_id']);
                                        $CI->db->update('srp_erp_inventory_itembatch', $data_batch);
                                        $resultArray[]=['batchNumber'=>$val,'qty'=>$balanceRequestQtr,'itemAutoID'=>$invoice['itemAutoID'],'from_wareHouseAutoID'=>$invoice['from_wareHouseAutoID'],'to_wareHouseAutoID'=>$invoice['to_wareHouseAutoID']];
                                        break;
                                    }else{

                                        $data_batch['qtr'] = 0;
                                        $CI->db->where('itemMasterID',  $invoice['itemAutoID']);
                                        $CI->db->where('wareHouseAutoID',  $invoice['from_wareHouseAutoID']);
                                        $CI->db->where('batchNumber', $val);
                                        $CI->db->where('companyId',$CI->common_data['company_data']['company_id']);
                                        $CI->db->update('srp_erp_inventory_itembatch', $data_batch);
                                        $resultArray[]=['batchNumber'=>$val,'qty'=>$batchitems['qtr'],'itemAutoID'=>$invoice['itemAutoID'],'from_wareHouseAutoID'=>$invoice['from_wareHouseAutoID'],'to_wareHouseAutoID'=>$invoice['to_wareHouseAutoID']];
                                        $balanceRequestQtr=$balanceRequestQtr-$batchitems['qtr'];

                                    }

                                }
                            }


                        }
                    }
                }
            }
        }

        return $resultArray;
       
    }

    function stock_adjustment_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $locationemp = $this->common_data['emplanglocationid'];
        $companyID = current_companyID();
        $currentuser = current_userID();
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        $itemBatchPolicy = getPolicyValues('IB', 'All');
        $stockAdjustmentAutoID = trim($this->input->post('stockAdjustmentAutoID') ?? '');
        $stockType = $this->db->query("SELECT adjustmentType FROM `srp_erp_stockadjustmentmaster` where stockAdjustmentAutoID = $stockAdjustmentAutoID")->row('adjustmentType');

        $this->db->select('stockAdjustmentAutoID');
        $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID') ?? ''));
        $this->db->from('srp_erp_stockadjustmentdetails');
        $results = $this->db->get()->row_array();

        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!.');
        } else {

            if($itemBatchPolicy == 1){
                $this->db->select('*');
                $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID') ?? ''));
                $this->db->from('srp_erp_stockadjustmentdetails');
                $stockadjustmentdetails_results = $this->db->get()->result_array();
            }

            $this->db->select('stockAdjustmentAutoID');
            $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_stockadjustmentmaster');
            $Confirmed = $this->db->get()->row_array();

            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $id = trim($this->input->post('stockAdjustmentAutoID') ?? '');

                if($wacRecalculationEnableYN == 0 && $stockType!=1){
                    $stockAdjustDetail = $this->db->query("select
                        GROUP_CONCAT(itemAutoID) as itemAutoID
                        from 
                        srp_erp_stockadjustmentdetails
                        where 
                        stockAdjustmentAutoID = $id")->row("itemAutoID");

                    if(!empty($stockAdjustDetail)){ 
                        
                            $wacTransactionAmountValidation  = fetch_itemledger_transactionAmount_validation("$stockAdjustDetail");
                            if(!empty($wacTransactionAmountValidation)){ 
                                return array('error' => 4, 'message' => $wacTransactionAmountValidation);
                                exit();
                            }

                    }
                }
                    
                //$isProductReference_completed = $this->isProductReference_completed_document_SA($id);
                $stockValidation = $this->minus_qty_validation($id);
                if (empty($stockValidation)) {
                    $isProductReference_completed = isMandatory_completed_document($id, 'SA');
                    if ($isProductReference_completed == 0) {
                        /** item Master Sub check : sub item already added items check box are ch */
                        $validate = $this->validate_itemMasterSub($id, 'SA');
                        /** validation skipped until they found this. we have to do the both side of check in the validate_itemMasterSub method and have to change the query */
                        if ($validate) {
                            $system_id = trim($this->input->post('stockAdjustmentAutoID') ?? '');
                            $this->db->select('stockAdjustmentCode,companyFinanceYearID,DATE_FORMAT(stockAdjustmentDate, "%Y") as invYear,DATE_FORMAT(stockAdjustmentDate, "%m") as invMonth,adjustmentType');
                            $this->db->where('stockAdjustmentAutoID', $system_id);
                            $this->db->from('srp_erp_stockadjustmentmaster');
                            $master_dt = $this->db->get()->row_array();
                            $this->load->library('sequence');
                            $lenth = strlen($master_dt['stockAdjustmentCode']);
                            if ($lenth == 1) {
                                if ($locationwisecodegenerate == 1) {
                                    $this->db->select('locationID');
                                    $this->db->where('EIdNo', $currentuser);
                                    $this->db->where('Erp_companyID', $companyID);
                                    $this->db->from('srp_employeesdetails');
                                    $location = $this->db->get()->row_array();
                                    if ((empty($location)) || ($location == '')) {
                                        return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                                    } else {
                                        if ($locationemp != '') {
                                            $stockAdjustmentCode = $this->sequence->sequence_generator_location('SA', $master_dt['companyFinanceYearID'], $locationemp, $master_dt['invYear'], $master_dt['invMonth']);
                                        } else {
                                            return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                                        }
                                    }
                                } else {
                                    $stockAdjustmentCode = $this->sequence->sequence_generator_fin('SA', $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                                }

                                $validate_code = validate_code_duplication($stockAdjustmentCode, 'stockAdjustmentCode', $system_id,'stockAdjustmentAutoID', 'srp_erp_stockadjustmentmaster');
                                if(!empty($validate_code)) {
                                    return array('error' => 2, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                                }

                                $invcod = array(
                                    'stockAdjustmentCode' => $stockAdjustmentCode
                                );
                                $this->db->where('stockAdjustmentAutoID', $system_id);
                                $this->db->update('srp_erp_stockadjustmentmaster', $invcod);
                            } else {
                                $validate_code = validate_code_duplication($master_dt['stockAdjustmentCode'], 'stockAdjustmentCode', $system_id,'stockAdjustmentAutoID', 'srp_erp_stockadjustmentmaster');
                                if(!empty($validate_code)) {
                                    return array('error' => 2, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                                }
                            }

                            $this->load->library('Approvals');
                            $this->db->select('stockAdjustmentAutoID, stockAdjustmentCode,stockAdjustmentDate');
                            $this->db->where('stockAdjustmentAutoID', $id);
                            $this->db->from('srp_erp_stockadjustmentmaster');
                            $app_data = $this->db->get()->row_array();

                            $autoApproval = get_document_auto_approval('SA');
                            if ($autoApproval == 0) {
                                $approvals_status = $this->approvals->auto_approve($app_data['stockAdjustmentAutoID'], 'srp_erp_stockadjustmentmaster', 'stockAdjustmentAutoID', 'SA', $app_data['stockAdjustmentCode'], $app_data['stockAdjustmentDate']);
                            } elseif ($autoApproval == 1) {
                                $approvals_status = $this->approvals->CreateApproval('SA', $app_data['stockAdjustmentAutoID'], $app_data['stockAdjustmentCode'], 'Stock Adjustment', 'srp_erp_stockadjustmentmaster', 'stockAdjustmentAutoID', 0, $app_data['stockAdjustmentDate']);
                            } else {
                                return array('error' => 1, 'message' => 'Approval levels are not set for this document');
                            }
                            if ($approvals_status == 1) {
                                $autoApproval = get_document_auto_approval('SA');
                                if ($autoApproval == 0) {
                                    $result = $this->save_stock_adjustment_approval(0, $app_data['stockAdjustmentAutoID'], 1, 'Auto Approved');
                                    if ($result) {
                                        if( $itemBatchPolicy==1){
                                            $this->hit_item_batch($stockadjustmentdetails_results);
                                        }
                                        return array('error' => 0, 'message' => 'Document confirmed successfully');
                                    }
                                } else {
                                    $data = array(
                                        'confirmedYN' => 1,
                                        'confirmedDate' => $this->common_data['current_date'],
                                        'confirmedByEmpID' => $this->common_data['current_userID'],
                                        'confirmedByName' => $this->common_data['current_user']
                                    );
                                    $this->db->where('stockAdjustmentAutoID', $id);
                                    $this->db->update('srp_erp_stockadjustmentmaster', $data);
                                  
                                    if($wacRecalculationEnableYN == 0){ 
                                        if($master_dt['adjustmentType']==0){
                                            reupdate_companylocalwac('srp_erp_stockadjustmentdetails',$id,'stockAdjustmentAutoID','currentWac','SA','previousWac');
                                            $this->db->query("UPDATE srp_erp_stockadjustmentdetails JOIN(
                                                SELECT 
                                                currentWac * ( adjustmentStock / conversionRateUOM ) AS totalvalrecal,
                                                stockAdjustmentDetailsAutoID
                                                FROM 
                                                srp_erp_stockadjustmentdetails
                                                where 
                                                stockAdjustmentAutoID  = $id)wactotal ON  wactotal.stockAdjustmentDetailsAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentDetailsAutoID 
                                                SET srp_erp_stockadjustmentdetails.totalValue = wactotal.totalvalrecal");
                                       
                                       
                                        }
                                    }

                                    if( $itemBatchPolicy==1){
                                        $this->hit_item_batch($stockadjustmentdetails_results);
                                    }
                                    return array('error' => 0, 'message' => 'Document confirmed successfully');
                                }

                            } else if ($approvals_status == 3) {
                                return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document');
                            } else {
                                return array('error' => 1, 'message' => 'Document confirmation failed');
                            }
                        } else {
                            return array('error' => 1, 'message' => 'Please complete sub item configurations<br/> Please add sub item/s before confirm this document.');
                        }
                    } else {
                        return array('error' => 1, 'message' => 'Please complete you sub item configuration, fill all the mandatory fields!.');
                    }
                } else {
                    return array('error' => 1, 'message' => 'Balance Qty cannot be less than 0', 'stock' => $stockValidation);
                }
            }
        }
    }

    function hit_item_batch($array){

        foreach($array as $val){

                $this->db->select('*');
                $this->db->where('stockAdjustmentAutoID', $val['stockAdjustmentAutoID']);
                $this->db->from('srp_erp_stockadjustmentmaster');
                $master_dt = $this->db->get()->row_array();

                $this->db->select("*");
                $this->db->where('itemMasterID',  $val['itemAutoID']);
                $this->db->where('batchNumber', $val['batchNumber']);
                $this->db->where('wareHouseAutoID', $master_dt['wareHouseAutoID']);
                $this->db->where('companyId',$this->common_data['company_data']['company_id']);
                $batchitems = $this->db->get('srp_erp_inventory_itembatch')->row_array();


                $adjusted_value = $val['adjustmentStock'];

                if (empty($batchitems)) {
                    $CI =& get_instance();
                    $CI->db->select("*");
                    $CI->db->from('srp_erp_inventory_itembatch');
                    $results_data_batch = count($CI->db->get()->result_array());
        
                    $data_batch['batchNumber']=$val['batchNumber'];
                    $data_batch['batchCode']="ST_BIL".( 100001+ $results_data_batch);
                    $data_batch['qtr']= ($val['adjustmentStock'] / $val['conversionRateUOM']);
                    $data_batch['batchExpireDate']=$val['batchExpireDate'];
                    $data_batch['itemMasterID']=$val['itemAutoID'];
                    $data_batch['companyId']=$this->common_data['company_data']['company_id'];
                    $data_batch['createdUserID']=$this->common_data['current_userID'];
                    $data_batch['createdDateTime']=$this->common_data['current_date'];
                    $data_batch['grvDetailID']= $val['stockAdjustmentDetailsAutoID'];
                    $data_batch['wareHouseAutoID']=  $master_dt['wareHouseAutoID'];
                    $data_batch['status'] = 2;
        
                    $this->db->insert('srp_erp_inventory_itembatch', $data_batch);

                }else{
                    $data_batch['qtr'] = $batchitems['qtr'] + ($val['adjustmentStock'] / $val['conversionRateUOM']);

                    $this->db->where('itemMasterID',  $val['itemAutoID']);
                    $this->db->where('batchNumber', $val['batchNumber']);
                    $this->db->where('wareHouseAutoID', $master_dt['wareHouseAutoID']);
                    $this->db->where('companyId',$this->common_data['company_data']['company_id']);
                    $this->db->update('srp_erp_inventory_itembatch', $data_batch);

                }

                //Hit for srp_erp_warehouseitems
                //Generally for POS adding
                //$res = $this->update_warehouse_items_quantity($master_dt['wareHouseAutoID'],$adjusted_value,$val['itemAutoID']);

        }
    }

    function update_warehouse_items_quantity($wareHouseAutoID,$adjusment,$itemAutoID){

        $this->db->select('');
        $this->db->where('wareHouseAutoID', $wareHouseAutoID);
        $this->db->where('itemAutoID', $itemAutoID);
        $this->db->from('srp_erp_warehouseitems');
        $ex_quentity = $this->db->get()->row_array();

        $data = array();

        if($ex_quentity){
            //Item is already in the table
            $ex_count = $ex_quentity['currentStock'];
            $adjusted_stock = $ex_count + $adjusment;

            $data['currentStock'] = $adjusted_stock;

            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->update('srp_erp_warehouseitems',$data);

        }else {
            // Item not in the table continue
        }


    }

    function minus_qty_validation($id)
    {
        $validateStock = array();
        $companyID = current_companyID();
        $details = $this->db->query("SELECT 
                            stockAdjustmentDetailsAutoID, itemAutoID, unitOfMeasureID, unitOfMeasure, previousStock, previousWareHouseStock, currentStock,currentWareHouseStock, 
                            TRIM(TRAILING '.' FROM(TRIM(TRAILING 0 FROM((ROUND(( IFNULL( adjustmentStock,0) ), 4 )))))) as adjustmentStock, currentWac, totalValue, warehouseAutoID, itemSystemCode, itemDescription
                            FROM srp_erp_stockadjustmentdetails 
                            JOIN srp_erp_stockadjustmentmaster ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID
                            WHERE srp_erp_stockadjustmentmaster.adjustmentType = 0 AND srp_erp_stockadjustmentdetails.stockAdjustmentAutoID = {$id} AND companyID = {$companyID} AND adjustmentStock < 0")->result_array();
        if (!empty($details)) {
            foreach ($details AS $stock) {
                $warehouseItemCurrent = $this->db->query("SELECT TRIM(TRAILING '.' FROM(TRIM(TRAILING 0 FROM((ROUND(( IFNULL( SUM(transactionQTY/convertionRate),0) ), 4 )))))) AS currentStock 
                FROM srp_erp_itemledger 
                WHERE companyID = {$companyID} AND wareHouseAutoID = {$stock['warehouseAutoID']} AND itemAutoID = {$stock['itemAutoID']}")->row_array();
    
                $remainingStock = $warehouseItemCurrent['currentStock'] + $stock['adjustmentStock'];
              
                if ($remainingStock < 0) {
                    $stock['ledgerItems'] = $warehouseItemCurrent['currentStock'];
                    array_push($validateStock, $stock);
                }
            }
        }
       
        return $validateStock;
    }

    function isProductReference_completed_document_SA($id)
    {
        $result = $this->db->query("SELECT
                        count(itemMaster.subItemAutoID) AS countTotal
                    FROM
                        srp_erp_stockadjustmentmaster stockMaster
                    LEFT JOIN srp_erp_stockadjustmentdetails stockAdjustment ON stockAdjustment.stockAdjustmentAutoID = stockMaster.stockAdjustmentAutoID
                    LEFT JOIN srp_erp_itemmaster_subtemp itemMaster ON itemMaster.receivedDocumentDetailID = stockAdjustment.stockAdjustmentDetailsAutoID
                    LEFT JOIN srp_erp_itemmaster im ON im.itemAutoID = itemMaster.itemAutoID
                    WHERE
                        stockMaster.stockAdjustmentAutoID = '" . $id . "'
                    AND ( ISNULL( itemMaster.productReferenceNo )
                        OR itemMaster.productReferenceNo = ''
                    )
                    AND im.isSubitemExist = 1")->row_array();

        return $result['countTotal'];

    }

    function stock_return_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $companyID = current_companyID();
        $currentuser = current_userID();
        $stockReturnAutoID = trim($this->input->post('stockReturnAutoID') ?? '');
        $this->db->select('stockReturnAutoID');
        $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
        $this->db->from('srp_erp_stockreturndetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('stockReturnAutoID');
            $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_stockreturnmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->db->select('documentID,stockReturnCode,companyFinanceYearID,DATE_FORMAT(returnDate, "%Y") as invYear,DATE_FORMAT(returnDate, "%m") as invMonth');
                $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
                $this->db->from('srp_erp_stockreturnmaster');
                $master_dt = $this->db->get()->row_array();

                $stockReturnID = trim($this->input->post('stockReturnAutoID') ?? '');
               
                $stockReturnDetail = $this->db->query("select
                                                       GROUP_CONCAT(itemAutoID) as itemAutoID
                                                       from 
                                                       srp_erp_stockreturndetails
                                                       where 
                                                       stockReturnAutoID = $stockReturnID")->row("itemAutoID");
                
                if(!empty($stockReturnDetail)){ 

                $wacTransactionAmountValidation  = fetch_itemledger_transactionAmount_validation("$stockReturnDetail");
                if(!empty($wacTransactionAmountValidation)){ 
                return array('error' => 4, 'message' => $wacTransactionAmountValidation);
                exit();
                }

            }





                $this->load->library('sequence');
                if ($master_dt['stockReturnCode'] == "0" || empty($master_dt['stockReturnCode'])) {
                    if ($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location == '')) {
                            return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                        } else {
                            if ($locationemployee != '') {
                                $codegeratorpr = $this->sequence->sequence_generator_location($master_dt['documentID'], $master_dt['companyFinanceYearID'], $locationemployee, $master_dt['invYear'], $master_dt['invMonth']);
                            } else {
                                return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                            }
                        }
                    } else {
                        $codegeratorpr = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }

                    $validate_code = validate_code_duplication($codegeratorpr, 'stockReturnCode', $stockReturnAutoID,'stockReturnAutoID', 'srp_erp_stockreturnmaster');
                    if(!empty($validate_code)) {
                        return array('error' => 1, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                    }

                    $pvCd = array(
                        'stockReturnCode' => $codegeratorpr
                    );
                    $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
                    $this->db->update('srp_erp_stockreturnmaster', $pvCd);
                } else {
                    $validate_code = validate_code_duplication($master_dt['stockReturnCode'], 'stockReturnCode', $stockReturnAutoID,'stockReturnAutoID', 'srp_erp_stockreturnmaster');
                    if(!empty($validate_code)) {
                        return array('error' => 1, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                }

                $this->load->library('Approvals');
                $this->db->select('stockReturnAutoID, stockReturnCode,returnDate');
                $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
                $this->db->from('srp_erp_stockreturnmaster');
                $app_data = $this->db->get()->row_array();

                /** item Master Sub check */


                $documentDetailID = trim($this->input->post('stockReturnAutoID') ?? '');
                $validate = $this->validate_itemMasterSub($documentDetailID, 'SR');

                /** end of item master sub */

                if ($validate) {
                    // $sql = "SELECT (srp_erp_stockreturndetails.return_Qty / srp_erp_stockreturndetails.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,(srp_erp_warehouseitems.currentStock - (srp_erp_stockreturndetails.return_Qty / srp_erp_stockreturndetails.conversionRateUOM)) AS stock FROM srp_erp_stockreturndetails INNER JOIN srp_erp_stockreturnmaster  ON srp_erp_stockreturnmaster.stockReturnAutoID = srp_erp_stockreturndetails.stockReturnAutoID INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_stockreturndetails.itemAutoID AND srp_erp_stockreturnmaster.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where srp_erp_stockreturndetails.stockReturnAutoID = '{$this->input->post('stockReturnAutoID')}' Having stock < 0";
                    $sql = "SELECT TRIM(TRAILING '.' FROM(TRIM(TRAILING 0 FROM((ROUND(( ( srp_erp_stockreturndetails.return_Qty / srp_erp_stockreturndetails.conversionRateUOM ) ), 2 )))))) AS qty,
	TRIM(TRAILING '.' FROM(TRIM(TRAILING 0 FROM((ROUND(( srp_erp_warehouseitems.currentStock ), 2 )))))) AS qty,
	TRIM(TRAILING '.' FROM( TRIM(TRAILING 0 FROM((ROUND(( ( srp_erp_warehouseitems.currentStock - ( srp_erp_stockreturndetails.return_Qty / srp_erp_stockreturndetails.conversionRateUOM )) ), 2 )))))) AS stock  
	FROM srp_erp_stockreturndetails INNER JOIN srp_erp_stockreturnmaster  ON srp_erp_stockreturnmaster.stockReturnAutoID = srp_erp_stockreturndetails.stockReturnAutoID INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_stockreturndetails.itemAutoID AND srp_erp_stockreturnmaster.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where srp_erp_stockreturndetails.stockReturnAutoID = '{$this->input->post('stockReturnAutoID')}' Having stock < 0";

                    $item_low_qty = $this->db->query($sql)->result_array();


                    if (!empty($item_low_qty)) {
                        //$this->session->set_flashdata('w', 'Some Item quantities are not sufficient to confirm this transaction');
                        //return array('status' => false, 'data' => 'Some Item quantities are not sufficient to confirm this transaction');
                        return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction');
                    }
                } else {
                    return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');

                }


                $autoApproval = get_document_auto_approval('SR');

                if ($autoApproval == 0) {
                    $approvals_status = $this->approvals->auto_approve($app_data['stockReturnAutoID'], 'srp_erp_stockreturnmaster', 'stockReturnAutoID', 'SR', $app_data['stockReturnCode'], $app_data['returnDate']);
                } elseif ($autoApproval == 1) {
                    $approvals_status = $this->approvals->CreateApproval('SR', $app_data['stockReturnAutoID'], $app_data['stockReturnCode'], 'Stock Return', 'srp_erp_stockreturnmaster', 'stockReturnAutoID', 0, $app_data['returnDate']);
                } else {
                    return array('error' => 1, 'message' => 'Approval levels are not set for this document');
                    exit;
                }

                if ($approvals_status == 1) {


                    $autoApproval = get_document_auto_approval('SR');

                    if ($autoApproval == 0) {
                        $result = $this->save_stock_return_approval(0, $app_data['stockReturnAutoID'], 1, 'Auto Approved');
                        if ($result) {
                            return array('error' => 0, 'message' => 'document successfully confirmed');
                        }
                    } else {
                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user']
                        );

                        $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
                        $this->db->update('srp_erp_stockreturnmaster', $data);
                       // reupdate_companylocalwac('srp_erp_stockreturndetails',trim($this->input->post('stockReturnAutoID') ?? ''),'stockReturnAutoID','currentlWacAmount');
                        return array('error' => 0, 'message' => 'document successfully confirmed');
                    }


                } else if ($approvals_status == 3) {
                    return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document');
                } else {
                    return array('error' => 1, 'message' => 'Document confirmation failed!');
                }


            }

        }
        //return array('status' => true);
    }

    function fetch_warehouse_item()
    {
        $this->db->select('wareHouseAutoID,wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $query = $this->db->get()->row_array();

        $this->db->select('srp_erp_warehouseitems.currentStock,companyLocalWacAmount,wareHouseDescription,wareHouseLocation,srp_erp_itemmaster.mainCategory as mainCategory');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->where('wareHouseAutoID', $query['wareHouseAutoID']);
        $this->db->where('srp_erp_warehouseitems.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        $this->db->where('srp_erp_warehouseitems.companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get()->row_array();

        $wareHouseAutoID = $query['wareHouseAutoID'];
        $itemAutoID = $this->input->post('itemAutoID');

        $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM srp_erp_itemledger where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
        if (!empty($stock)) {
            $currentStock = $stock['currentStock'];
        } else {
            $currentStock = 0;
        }
        $this->load->model('Receipt_voucher_model');
        $pulled_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty($itemAutoID,$wareHouseAutoID, 'MI', trim($this->input->post('itemIssueAutoID') ?? ''));

        if (!empty($data)) {
            return array('status' => true, 'currentStock' => $currentStock, 'WacAmount' => $data['companyLocalWacAmount'],'pulledstock'=>($currentStock - $pulled_stock['Unapproved_stock']),'mainCategory'=>$data['mainCategory'],'parkQty'=>($pulled_stock['Unapproved_stock']));
        } else {
            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse " . $query["wareHouseDescription"] . " ( " . $query["wareHouseLocation"] . " )");
            return array('status' => false);
        }
    }
    function fetch_warehouse_item_new()
    {
        $documentcode=trim($this->input->post('documentcode') ?? '');
        $itemIssueAutoID=trim($this->input->post('itemIssueAutoID') ?? '');
        $itemIssueDetailID=trim($this->input->post('itemIssueDetailID') ?? '');

        $this->db->select('wareHouseAutoID,wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $query = $this->db->get()->row_array();

        $this->db->select('srp_erp_warehouseitems.currentStock,companyLocalWacAmount,wareHouseDescription,wareHouseLocation,srp_erp_itemmaster.mainCategory as mainCategory');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->where('wareHouseAutoID', $query['wareHouseAutoID']);
        $this->db->where('srp_erp_warehouseitems.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        $this->db->where('srp_erp_warehouseitems.companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get()->row_array();

        $wareHouseAutoID = $query['wareHouseAutoID'];
        $itemAutoID = $this->input->post('itemAutoID');

        $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM srp_erp_itemledger where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
        if (!empty($stock)) {
            $currentStock = $stock['currentStock'];
        } else {
            $currentStock = 0;
        }
        $this->load->model('Receipt_voucher_model');
        //$pulled_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty($itemAutoID,$wareHouseAutoID, 'MI', trim($this->input->post('itemIssueAutoID') ?? ''));
        $pulled_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty_new($itemAutoID,$wareHouseAutoID, $documentcode,$itemIssueAutoID,  $itemIssueDetailID);

        if (!empty($data)) {
            return array('status' => true, 'currentStock' => $currentStock, 'WacAmount' => $data['companyLocalWacAmount'],'pulledstock'=>($currentStock - $pulled_stock['Unapproved_stock']),'mainCategory'=>$data['mainCategory'],'parkQty'=>($pulled_stock['Unapproved_stock']));
        } else {
            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse " . $query["wareHouseDescription"] . " ( " . $query["wareHouseLocation"] . " )");
            return array('status' => false);
        }
    }

    function fetch_st_warehouse_item()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $this->db->select('from_wareHouseAutoID,form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
        $query = $this->db->get()->row_array();

        $this->db->select('companyLocalWacAmount,srp_erp_itemmaster.mainCategory as mainCategory');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', trim($itemAutoID));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $currentWac = $this->db->get()->row_array();

        $wareHouseAutoID = $query['from_wareHouseAutoID'];
        $stock = $this->db->query('SELECT IFNULL(SUM(transactionQTY/convertionRate), 0) as currentStock FROM srp_erp_itemledger where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
        $this->load->model('Receipt_voucher_model');
        $pulled_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty($itemAutoID,$wareHouseAutoID, 'ST', trim($this->input->post('stockTransferAutoID') ?? ''));
        if (!empty($stock)) {
            return array('error' => 0, 'message' => '', 'status' => true, 'currentStock' => $stock['currentStock'], 'WacAmount' => $currentWac['companyLocalWacAmount'],'pulledstock'=>($stock['currentStock'] - $pulled_stock['Unapproved_stock']),'mainCategory'=>$currentWac['mainCategory'],'parkQty'=>($pulled_stock['Unapproved_stock']));
        } else {
            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse " . $query['form_wareHouseDescription'] . " ( " . $query['form_wareHouseLocation'] . " )");
            return array('status' => false, 'error' => 2, 'message' => "Item doesn't exists in the selected warehouse " . $query['form_wareHouseDescription'] . " ( " . $query['form_wareHouseLocation'] . " )");
        }
    }
    function fetch_st_warehouse_item_new()
    {
        $documentcode=trim($this->input->post('documentcode') ?? '');
        $stockTransferAutoID=trim($this->input->post('stockTransferAutoID') ?? '');
        $stockTransferDetailsID=trim($this->input->post('stockTransferDetailsID') ?? '');
        $itemAutoID = $this->input->post('itemAutoID');
        $this->db->select('from_wareHouseAutoID,form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
        $query = $this->db->get()->row_array();
        $this->db->select('companyLocalWacAmount,srp_erp_itemmaster.mainCategory as mainCategory');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', trim($itemAutoID));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $currentWac = $this->db->get()->row_array();
        $wareHouseAutoID = $query['from_wareHouseAutoID'];
        $stock = $this->db->query('SELECT IFNULL(SUM(transactionQTY/convertionRate), 0) as currentStock FROM srp_erp_itemledger where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
        $this->load->model('Receipt_voucher_model');
        $pulled_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty($itemAutoID,$wareHouseAutoID,$documentcode,$stockTransferAutoID,$stockTransferDetailsID);
        if (!empty($stock)) {
            return array('error' => 0, 'message' => '', 'status' => true, 'currentStock' => $stock['currentStock'], 'WacAmount' => $currentWac['companyLocalWacAmount'],'pulledstock'=>($stock['currentStock'] - $pulled_stock['Unapproved_stock']),'mainCategory'=>$currentWac['mainCategory'],'parkQty'=>($pulled_stock['Unapproved_stock']));
        } else {
            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse " . $query['form_wareHouseDescription'] . " ( " . $query['form_wareHouseLocation'] . " )");
            return array('status' => false, 'error' => 2, 'message' => "Item doesn't exists in the selected warehouse " . $query['form_wareHouseDescription'] . " ( " . $query['form_wareHouseLocation'] . " )");
        }
    }
    function fetch_warehouse_item_adjustment()
    {
        $this->db->select('wareHouseAutoID,wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->db->from('srp_erp_stockadjustmentmaster');
        $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID') ?? ''));
        $query = $this->db->get()->row_array();

        /* $this->db->select('currentStock');
         $this->db->from('srp_erp_warehouseitems');
         $this->db->where('wareHouseAutoID', $query['wareHouseAutoID']);
         $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
         $this->db->where('companyID', $this->common_data['company_data']['company_id']);
         $currentStock = $this->db->get()->row('currentStock');*/

        $this->db->select('companyLocalWacAmount');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $currentWac = $this->db->get()->row('companyLocalWacAmount');

        $wareHouseAutoID = $query['wareHouseAutoID'];
        $itemAutoID = $this->input->post('itemAutoID');

        $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM srp_erp_itemledger where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
        if (!empty($stock)) {
            $currentStock = $stock['currentStock'];
        } else {
            $currentStock = 0;
        }

        if (!empty($currentStock)) {
            return array('status' => true, 'currentStock' => $currentStock, 'currentWac' => $currentWac);
        } else {
            return array('status' => true, 'currentStock' => 0, 'currentWac' => $currentWac);
            //$this->session->set_flashdata('w', 'The item you entered is not exists in this warehouse ' . $query['wareHouseDescription'] . ' ( ' . $query['wareHouseLocation'] . ' ) . you can not issue this item from this warehouse.');
            return array('status' => false);
        }
    }


    function save_material_issue_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0,$updatedBatchNumberArray=[])
    {
        $this->db->trans_start();
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        $batchNumberPolicy = getPolicyValues('IB', 'All');
        $this->load->library('Approvals');
        $companyID = current_companyID();
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('itemIssueAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['itemIssueAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }


        $this->db->select('wareHouseAutoID, jobID');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', $system_code);
        $frmWareHouse = $this->db->get()->row_array();
        $warehouse =$frmWareHouse['wareHouseAutoID'];
        /* $this->db->select('TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM (ROUND((srp_erp_warehouseitems.currentStock-srp_erp_itemissuedetails.qtyIssued), 2 ))))) as stockDiff,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.itemDescription,srp_erp_warehouseitems.currentStock as availableStock');
        $this->db->from('srp_erp_itemissuedetails');
        $this->db->where('srp_erp_itemissuedetails.itemIssueAutoID', $system_code);
        $this->db->where('srp_erp_warehouseitems.companyID', current_companyID());
        $this->db->where('srp_erp_warehouseitems.wareHouseAutoID', $frmWareHouse['wareHouseAutoID']);
        $this->db->join('srp_erp_warehouseitems', 'srp_erp_itemissuedetails.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemissuedetails.itemAutoID = srp_erp_itemmaster.itemAutoID');
        $this->db->having('stockDiff < 0'); */

        $sql = "SELECT  
                `srp_erp_itemmaster`.`itemSystemCode`, `srp_erp_itemmaster`.`itemDescription`,
                TRIM(	TRAILING '.' FROM(	TRIM(TRAILING 0 FROM((ROUND(( ware_house.currentStock ), 2 )))))) AS availableStock, pq.stock AS parkQty,
                TRIM(	TRAILING '.' FROM(	TRIM(TRAILING 0 FROM((ROUND(( ( srp_erp_itemissuedetails.qtyIssued / srp_erp_itemissuedetails.conversionRateUOM )), 2 )))))) AS qty,
                TRIM(	TRAILING '.' FROM(	TRIM(TRAILING 0 FROM((ROUND((( ware_house.currentStock - (( IFNULL( pq.stock, 0 ) ) +( srp_erp_itemissuedetails.qtyIssued / srp_erp_itemissuedetails.conversionRateUOM )))), 2 )))))) AS stockDiff
            FROM
            `srp_erp_itemissuedetails`
            INNER JOIN srp_erp_itemissuemaster ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID
            JOIN `srp_erp_itemmaster` ON `srp_erp_itemissuedetails`.`itemAutoID` = `srp_erp_itemmaster`.`itemAutoID` 
            LEFT JOIN ( SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID FROM srp_erp_itemledger WHERE companyID = {$companyID} GROUP BY wareHouseAutoID, itemAutoID ) 	AS ware_house ON ware_house.itemAutoID = srp_erp_itemissuedetails.itemAutoID 	AND srp_erp_itemissuemaster.wareHouseAutoID = ware_house.wareHouseAutoID
            LEFT JOIN (
            SELECT  SUM( stock ) AS stock, t1.ItemAutoID, wareHouseAutoID 
                FROM
                    (
                    SELECT
                        IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock, itemAutoID, srp_erp_stockadjustmentmaster.wareHouseAutoID AS wareHouseAutoID 
                    FROM
                        srp_erp_stockadjustmentmaster
                        LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
                    WHERE
                        companyID = {$companyID} 	AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                    SELECT
                        IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,	itemAutoID,	srp_erp_stockcountingmaster.wareHouseAutoID AS wareHouseAutoID 
                    FROM
                        srp_erp_stockcountingmaster
                        LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
                    WHERE
                        companyID =  {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                    SELECT
                        IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock,	itemAutoID,		srp_erp_itemissuemaster.wareHouseAutoID AS wareHouseAutoID 
                    FROM
                        srp_erp_itemissuemaster
                        LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
                    WHERE
                        srp_erp_itemissuemaster.companyID =  {$companyID} AND srp_erp_itemissuemaster.itemIssueAutoID != '{$system_code}'  AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                    SELECT
                        ( requestedQty / conversionRateUOM ) AS stock,	itemAutoID,	srp_erp_customerreceiptdetail.wareHouseAutoID AS wareHouseAutoID 
                    FROM
                        srp_erp_customerreceiptmaster
                        LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                    WHERE
                        srp_erp_customerreceiptmaster.companyID =  {$companyID} 	AND approvedYN != 1 	AND itemCategory = 'Inventory' UNION ALL
                    SELECT
                        ( requestedQty / conversionRateUOM ) AS stock,	itemAutoID,	srp_erp_customerinvoicedetails.wareHouseAutoID AS wareHouseAutoID 
                    FROM
                        srp_erp_customerinvoicemaster
                        LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
                    WHERE
                        srp_erp_customerinvoicemaster.companyID =  {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                    SELECT
                        ( deliveredQty / conversionRateUOM ) AS stock,	itemAutoID,	srp_erp_deliveryorderdetails.wareHouseAutoID AS wareHouseAutoID 
                    FROM
                        srp_erp_deliveryorder
                        LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
                    WHERE
                        srp_erp_deliveryorder.companyID =  {$companyID} 	AND approvedYN != 1 	AND itemCategory = 'Inventory' UNION ALL
                    SELECT
                        ( transfer_QTY / conversionRateUOM ) AS stock,itemAutoID,	srp_erp_stocktransfermaster.from_wareHouseAutoID AS from_wareHouseAutoID 
                    FROM
                        srp_erp_stocktransfermaster
                        LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
                    WHERE
                        srp_erp_stocktransfermaster.companyID =  {$companyID} AND approvedYN != 1 	AND itemCategory = 'Inventory' 
                ) t1 
                GROUP BY
                    t1.wareHouseAutoID,
                    t1.ItemAutoID 
                ) AS pq ON pq.ItemAutoID = srp_erp_itemissuedetails.itemAutoID AND pq.wareHouseAutoID = srp_erp_itemissuemaster.wareHouseAutoID 
            WHERE
                `srp_erp_itemissuedetails`.`itemIssueAutoID` = '{$system_code}' 
                AND `srp_erp_itemissuedetails`.`companyID` = '{$companyID}' 
                AND `srp_erp_itemissuemaster`.`wareHouseAutoID` = '{$warehouse}' 
            HAVING
                `stockDiff` <0";
                

        $items_arr = $this->db->query($sql)->result_array();
        //$items_arr = $this->db->get()->result_array();

        if($wacRecalculationEnableYN == 0){ 
        reupdate_companylocalwac('srp_erp_itemissuedetails',$system_code,'itemIssueAutoID','currentlWacAmount');
        $this->db->query("UPDATE srp_erp_itemissuedetails JOIN(
            SELECT 
            currentlWacAmount*(qtyIssued/conversionRateUOM) as totalvalrecal,
            itemIssueDetailID
            FROM 
            srp_erp_itemissuedetails
            where 
            companyID = $companyID
            AND itemIssueAutoID  = $system_code)wactotal ON  wactotal.itemIssueDetailID = srp_erp_itemissuedetails.itemIssueDetailID 
            SET srp_erp_itemissuedetails.totalValue = wactotal.totalvalrecal");
        }


        if ($status != 1) {
            $items_arr = [];
        }
        if (!$items_arr) {
            if ($autoappLevel == 0) {
                $approvals_status = 1;
            } else {
                $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'MI');
            }
            if ($approvals_status == 1) {
                $this->db->select('*,COALESCE(SUM(srp_erp_itemissuedetails.qtyIssued),0) AS qtyUpdatedIssued,COALESCE(SUM(srp_erp_itemissuedetails.totalValue),0) AS UpdatedTotalValue');
                $this->db->from('srp_erp_itemissuedetails');
                $this->db->where('srp_erp_itemissuedetails.itemIssueAutoID', $system_code);
                $this->db->join('srp_erp_itemissuemaster', 'srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID');
                $this->db->group_by('srp_erp_itemissuedetails.itemAutoID');
                $details_arr = $this->db->get()->result_array();

                $item_arr = array();
                $itemledger_arr = array();
                $transaction_loc_tot = 0;
                $company_rpt_tot = 0;
                $supplier_cr_tot = 0;
                $company_loc_tot = 0;
                for ($i = 0; $i < count($details_arr); $i++) {
                    if ($details_arr[$i]['itemCategory'] == 'Inventory' or $details_arr[$i]['itemCategory'] == 'Non Inventory' or $details_arr[$i]['itemCategory'] =='Service') {
                        $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                        $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                        $item_arr[$i]['currentStock'] = ($item['currentStock'] - ($details_arr[$i]['qtyUpdatedIssued'] / $details_arr[$i]['conversionRateUOM']));
                        $item_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                        $item_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                        $qty = ($details_arr[$i]['qtyUpdatedIssued'] / $details_arr[$i]['conversionRateUOM']);
                        $itemSystemCode = $details_arr[$i]['itemAutoID'];
                        $location = $details_arr[$i]['wareHouseLocation'];
                        $wareHouseAutoID = $details_arr[$i]['wareHouseAutoID'];
                        $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");

                        $itemledger_arr[$i]['documentID'] = $details_arr[$i]['documentID'];
                        $itemledger_arr[$i]['documentCode'] = $details_arr[$i]['documentID'];
                        $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['itemIssueAutoID'];
                        $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['itemIssueCode'];
                        $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['issueDate'];
                        $itemledger_arr[$i]['referenceNumber'] = $details_arr[$i]['issueRefNo'];
                        $itemledger_arr[$i]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                        $itemledger_arr[$i]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                        $itemledger_arr[$i]['FYBegin'] = $details_arr[$i]['FYBegin'];
                        $itemledger_arr[$i]['FYEnd'] = $details_arr[$i]['FYEnd'];
                        $itemledger_arr[$i]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                        $itemledger_arr[$i]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                        $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['wareHouseAutoID'];
                        $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                        $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                        $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                        $itemledger_arr[$i]['projectID'] = $details_arr[$i]['projectID'];
                        $itemledger_arr[$i]['projectExchangeRate'] = $details_arr[$i]['projectExchangeRate'];
                        $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                        $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                        $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                        $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                        $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                        $itemledger_arr[$i]['transactionQTY'] = ($details_arr[$i]['qtyUpdatedIssued'] * -1);
                        $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                        $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                        $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                        $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                        $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                        $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                        $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                        $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                        $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                        $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                        $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                        $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                        $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                        $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];
                        $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                        $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                        $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                        $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                        $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];
                        $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                        $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                        $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                        $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr[$i]['transactionAmount'] = (round($details_arr[$i]['UpdatedTotalValue'], $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                        $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                        $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                        $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyLocalExchangeRate']), $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$i]['companyLocalWacAmount'] = round($details_arr[$i]['currentlWacAmount'], $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                        $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                        $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                        $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                        $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$i]['companyReportingWacAmount'] = round(($itemledger_arr[$i]['companyLocalWacAmount'] / $itemledger_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        $itemledger_arr[$i]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                        $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                        $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                        $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                        $itemledger_arr[$i]['approvedYN'] = $details_arr[$i]['approvedYN'];
                        $itemledger_arr[$i]['approvedDate'] = $details_arr[$i]['approvedDate'];
                        $itemledger_arr[$i]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                        $itemledger_arr[$i]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                        $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                        $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];
                        $itemledger_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                        $itemledger_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                        $itemledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $itemledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $itemledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $itemledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                        $itemledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                        $itemledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                        $itemledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                        $itemledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                        $itemledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                    }
                }

                if (!empty($item_arr)) {
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
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

                $this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_material_issue_data($system_code, 'MI');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['itemIssueAutoID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['itemIssueCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['issueDate'];
                    $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['issueType'];
                    $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['issueDate'];
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['issueDate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                    $generalledger_arr[$i]['chequeNumber'] = '';
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['partyContractID'] = '';
                    $generalledger_arr[$i]['partyType'] = 'EMP';
                    $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['employeeID'];
                    $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['employeeCode'];
                    $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['employeeName'];
                    $generalledger_arr[$i]['partyCurrencyID'] = '';//$double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr[$i]['partyCurrency'] = '';//$double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = '';//$double_entry['master_data']['supplierCurrencyExchangeRate'];
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = '';//$double_entry['master_data']['supplierCurrencyDecimalPlaces'];
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

                //$this->session->set_flashdata('s', 'Material Issue Approval Successfully.');
            }
            /*else {
                $this->session->set_flashdata('s', 'Material Issue Approval : Level ' . $level_id . ' Successfully.');
            }*/

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Material Issue Approval Failed.', 1);
            } else {
                $this->db->trans_commit();
                return array('s', 'Material Issue Approved Successfully.', 1);
            }
        } else {
            return array('e', 'Item quantities are insufficient.', $items_arr);
        }
    }

    function save_stock_adjustment_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('stockAdjustmentAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['stockAdjustmentAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        $companyID = current_companyID();
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        $stockadjustmentType = $this->db->query("SELECT adjustmentType FROM srp_erp_stockadjustmentmaster where companyID = $companyID AND stockAdjustmentAutoID = $system_code")->row('adjustmentType');
       
        if($stockadjustmentType == 0 && $wacRecalculationEnableYN == 0){
            reupdate_companylocalwac('srp_erp_stockadjustmentdetails',$system_code,'stockAdjustmentAutoID','currentWac','SA','previousWac');
            $this->db->query("UPDATE srp_erp_stockadjustmentdetails JOIN(
                SELECT 
                currentWac *( adjustmentStock / conversionRateUOM ) AS totalvalrecal,
                stockAdjustmentDetailsAutoID
                FROM 
                srp_erp_stockadjustmentdetails
                where 
                stockAdjustmentAutoID  = $system_code)wactotal ON  wactotal.stockAdjustmentDetailsAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentDetailsAutoID 
                SET srp_erp_stockadjustmentdetails.totalValue = wactotal.totalvalrecal");
        
        
        }

        
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            if ($status == 1) {
               $stockValidation = $this->minus_qty_validation($system_code);
                if (empty($stockValidation)) {
                    $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'SA');
                } else {
                    return array('error' => 'e', 'message' => 'Balance Qty cannot be less than 0', 'stock' => $stockValidation);
                }
            } else {
                $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'SA');
            }
        }

        if ($approvals_status == 1) {
            $this->db->select('*,srp_erp_stockadjustmentdetails.segmentID as segID,srp_erp_stockadjustmentdetails.segmentCode as segCode');
            $this->db->from('srp_erp_stockadjustmentdetails');
            $this->db->where('srp_erp_stockadjustmentdetails.stockAdjustmentAutoID', $system_code);
            $this->db->join('srp_erp_stockadjustmentmaster',
                'srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID');
            $details_arr = $this->db->get()->result_array();

            $item_arr = array();
            $itemledger_arr = array();
            $transaction_loc_tot = 0;
            $company_rpt_tot = 0;
            $supplier_cr_tot = 0;
            $company_loc_tot = 0;
            for ($i = 0; $i < count($details_arr); $i++) {
                $this->db->select('currentStock');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemAutoID', $details_arr[$i]['itemAutoID']);
                $prevItemMasterTotal = $this->db->get()->row_array();

                $item = fetch_item_data($details_arr[$i]['itemAutoID']);

                $itemledgerCurrentStock = fetch_itemledger_currentstock($details_arr[$i]['itemAutoID']);
                $itemledgerTransactionAmountLocalWac = fetch_itemledger_transactionAmount($details_arr[$i]['itemAutoID'], 'companyLocalExchangeRate');
                $itemledgerTransactionAmountReportingWac = fetch_itemledger_transactionAmount($details_arr[$i]['itemAutoID'],'companyReportingExchangeRate');



                $qty = ($details_arr[$i]['adjustmentStock'] / $details_arr[$i]['conversionRateUOM']);
                $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];

                if ($details_arr[$i]['adjustmentType'] == 0) {
                   // $item_arr[$i]['currentStock'] = ($item['currentStock'] + $qty);
                    $item_arr[$i]['currentStock'] = ($itemledgerCurrentStock + $qty);
                } else {
                    $item_arr[$i]['currentStock'] = $prevItemMasterTotal['currentStock'];
                }

                $item_arr[$i]['companyLocalWacAmount'] = round(($details_arr[$i]['currentWac'] / $details_arr[$i]['companyLocalExchangeRate']),
                                                                $details_arr[$i]['companyLocalCurrencyDecimalPlaces']);

                $item_arr[$i]['companyReportingWacAmount'] = round(($details_arr[$i]['currentWac'] / $details_arr[$i]['companyReportingExchangeRate']),
                                                                    $details_arr[$i]['companyReportingCurrencyDecimalPlaces']);


                $itemSystemCode = $details_arr[$i]['itemAutoID'];
                $location = $details_arr[$i]['wareHouseLocation'];
                $wareHouseAutoID = $details_arr[$i]['wareHouseAutoID'];

                if ($details_arr[$i]['adjustmentType'] == 0) {
                    $warehouseItemQty = ($details_arr[$i]['adjustmentWareHouseStock'] / $details_arr[$i]['conversionRateUOM']);
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = currentStock + {$warehouseItemQty}  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");
                }

                $itemledger_arr[$i]['documentID'] = $details_arr[$i]['documentID'];
                $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['stockAdjustmentAutoID'];
                $itemledger_arr[$i]['documentCode'] = $details_arr[$i]['documentID'];
                $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['stockAdjustmentCode'];
                $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['stockAdjustmentDate'];
                $itemledger_arr[$i]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                $itemledger_arr[$i]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                $itemledger_arr[$i]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                $itemledger_arr[$i]['FYBegin'] = $details_arr[$i]['FYBegin'];
                $itemledger_arr[$i]['FYEnd'] = $details_arr[$i]['FYEnd'];
                $itemledger_arr[$i]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                $itemledger_arr[$i]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['wareHouseAutoID'];
                $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                $itemledger_arr[$i]['transactionQTY'] = $details_arr[$i]['adjustmentStock'];
                $itemledger_arr[$i]['batchNumber'] = $details_arr[$i]['batchNumber'];
                $itemledger_arr[$i]['batchExpireDate'] = $details_arr[$i]['batchExpireDate'];
                $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];
                $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];
                $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['transactionAmount'] = (round($details_arr[$i]['totalValue'], $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyLocalExchangeRate']),
                    $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyReportingExchangeRate']),
                    $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];

                $itemledger_arr[$i]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                $itemledger_arr[$i]['approvedYN'] = $details_arr[$i]['approvedYN'];
                $itemledger_arr[$i]['approvedDate'] = $details_arr[$i]['approvedDate'];
                $itemledger_arr[$i]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                $itemledger_arr[$i]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                /*$itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];*/
                $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segID'];
                $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segCode'];
                $itemledger_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $itemledger_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $itemledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $itemledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                $itemledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                $itemledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                $itemledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                $itemledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $itemledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $itemledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $itemledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }

            if (!empty($item_arr)) {
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }
            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_sa_data($system_code, 'SA');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['stockAdjustmentAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['stockAdjustmentCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['stockAdjustmentDate'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['stockAdjustmentDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['stockAdjustmentDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                // $generalledger_arr[$i]['partyType']                                 = 'SUP';
                // $generalledger_arr[$i]['partyAutoID']                               = $double_entry['master_data']['supplierID'];
                // $generalledger_arr[$i]['partySystemCode']                           = $double_entry['master_data']['supplierSystemCode'];
                // $generalledger_arr[$i]['partyName']                                 = $double_entry['master_data']['supplierName'];
                // $generalledger_arr[$i]['partyCurrency']                             = $double_entry['master_data']['supplierCurrency'];
                // $generalledger_arr[$i]['partyExchangeRate']                         = $double_entry['master_data']['supplierCurrencyExchangeRate'];
                // $generalledger_arr[$i]['partyCurrencyDecimalPlaces']                = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
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
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']),
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']*$generalledger_arr[$i]['partyExchangeRate']),4);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : NULL;
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

            $maxLevel = $this->approvals->maxlevel('SA');

            $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? TRUE : FALSE;
            /** update sub item master : shafry */
            if ($isFinalLevel) {
                $masterID = $this->input->post('stockAdjustmentAutoID');
                $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                if (!empty($result)) {
                    $i = 0;
                    foreach ($result as $item) {
                        unset($result[$i]['subItemAutoID']);
                        $i++;
                    }
                    $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    $this->db->delete('srp_erp_itemmaster_subtemp',
                        array('receivedDocumentAutoID' => $masterID, 'receivedDocumentID' => 'SA'));

                }
            }
            $itemAutoIDarry = array();
            $ajststkarry = 0;
            foreach ($details_arr as $value) {
                array_push($itemAutoIDarry, $value['itemAutoID']);
                $ajststkarry += ($value['adjustmentStock']/$value['conversionRateUOM']);
            }
            
            $companyID = current_companyID();
            $this->db->select('*');
            $this->db->from('srp_erp_stockadjustmentmaster');
            $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID') ?? ''));
            $master = $this->db->get()->row_array();
            if ($master['adjustmentType'] == 0 && $ajststkarry > 0) {
                $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID= '" . $master ['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID = 0;

                if (!empty($exceededitems_master)) {
                    $this->load->library('sequence');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['stockAdjustmentDate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['stockAdjustmentAutoID'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['stockAdjustmentCode'];
                    $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $exceededmatch['transactionCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['transactionCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['transactionExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                    $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                    $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                    $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                    $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                    $exceededmatch['FYBegin'] = $master ['FYBegin'];
                    $exceededmatch['FYEnd'] = $master ['FYEnd'];
                    $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                    $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                    $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];
                    $exceededmatch['createdUserGroup'] = $this->common_data['user_group'];
                    $exceededmatch['createdPCID'] = $this->common_data['current_pc'];
                    $exceededmatch['createdUserID'] = $this->common_data['current_userID'];
                    $exceededmatch['createdUserName'] = $this->common_data['current_user'];
                    $exceededmatch['createdDateTime'] = $this->common_data['current_date'];
                    $exceededmatch['documentSystemCode'] = $this->sequence->sequence_generator($exceededmatch['documentID']);
                    $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                    $exceededMatchID = $this->db->insert_id();
                }


                foreach ($details_arr as $itemid) {
                    $receivedQty = $itemid['adjustmentStock'];
                    $receivedQtyConverted = $itemid['adjustmentStock'] / $itemid['conversionRateUOM'];
                    $companyID = current_companyID();
                    $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $master ['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                    $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                    $sumqty = array_column($exceededitems, 'balanceQty');
                    $sumqty = array_sum($sumqty);
                    if (!empty($exceededitems)) {
                        foreach ($exceededitems as $exceededItemAutoID) {
                            if ($receivedQtyConverted > 0) {
                                $balanceQty = $exceededItemAutoID['balanceQty'];
                                $updatedQty = $exceededItemAutoID['updatedQty'];
                                $balanceQtyConverted = $exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM'];
                                $updatedQtyConverted = $exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM'];

                                if ($receivedQtyConverted > $balanceQtyConverted) {
                                    $qty = $receivedQty - $balanceQty;
                                    $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                    $receivedQty = $qty;
                                    $receivedQtyConverted = $qtyconverted;
                                    $exeed['balanceQty'] = 0;
                                    //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                    $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                    $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetail['warehouseAutoID'] = $master['wareHouseAutoID'];
                                    $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetail['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                    $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                    $exceededmatchdetail['itemCost'] = $exceededItemAutoID['unitCost'];
                                    $exceededmatchdetail['totalValue'] = $balanceQtyConverted * $exceededmatchdetail['itemCost'];
                                    $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetail['createdUserGroup'] = $this->common_data['user_group'];
                                    $exceededmatchdetail['createdPCID'] = $this->common_data['current_pc'];
                                    $exceededmatchdetail['createdUserID'] = $this->common_data['current_userID'];
                                    $exceededmatchdetail['createdUserName'] = $this->common_data['current_user'];
                                    $exceededmatchdetail['createdDateTime'] = $this->common_data['current_date'];

                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                                } else {
                                    $exeed['balanceQty'] = $balanceQtyConverted - $receivedQtyConverted;
                                    $exeed['updatedQty'] = $updatedQtyConverted + $receivedQtyConverted;
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetails['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                    $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetails['warehouseAutoID'] = $master['wareHouseAutoID'];
                                    $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetails['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                    $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                    $exceededmatchdetails['itemCost'] = $exceededItemAutoID['unitCost'];
                                    $exceededmatchdetails['totalValue'] = $receivedQtyConverted * $exceededmatchdetails['itemCost'];
                                    $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetails['createdUserGroup'] = $this->common_data['user_group'];
                                    $exceededmatchdetails['createdPCID'] = $this->common_data['current_pc'];
                                    $exceededmatchdetails['createdUserID'] = $this->common_data['current_userID'];
                                    $exceededmatchdetails['createdUserName'] = $this->common_data['current_user'];
                                    $exceededmatchdetails['createdDateTime'] = $this->common_data['current_date'];
                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                    $receivedQty = $receivedQty - $exeed['updatedQty'];
                                    $receivedQtyConverted = $receivedQtyConverted - ($updatedQtyConverted + $receivedQtyConverted);
                                }
                            }
                        }
                    }
                }
                if (!empty($exceededitems_master)) {
                    exceed_double_entry($exceededMatchID);
                }
            }


            $this->session->set_flashdata('s', 'Stock adjustment Approval Successfully.');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return TRUE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    function save_stock_transfer_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0,$updatedBatchNumberArray=[])
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        $batchNumberPolicy = getPolicyValues('IB', 'All');
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('stockTransferAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['stockTransferAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $maxLevel = $this->approvals->maxlevel('ST');

        $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;

        $this->db->select('from_wareHouseAutoID');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', $system_code);
        $frmWareHouse = $this->db->get()->row_array();
        $warehouse = $frmWareHouse['from_wareHouseAutoID'];
        $this->db->select('*');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', $system_code);
        $master = $this->db->get()->row_array();

        /* $this->db->select('(srp_erp_warehouseitems.currentStock-srp_erp_stocktransferdetails.transfer_QTY) as stockDiff,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.itemDescription,srp_erp_warehouseitems.currentStock as availableStock');
        $this->db->from('srp_erp_stocktransferdetails');
        $this->db->where('srp_erp_stocktransferdetails.stockTransferAutoID', $system_code);
        $this->db->where('srp_erp_warehouseitems.companyID', current_companyID());
        $this->db->where('srp_erp_warehouseitems.wareHouseAutoID', $frmWareHouse['from_wareHouseAutoID']);
        $this->db->join('srp_erp_warehouseitems', 'srp_erp_stocktransferdetails.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_stocktransferdetails.itemAutoID = srp_erp_itemmaster.itemAutoID');
        $this->db->having('stockDiff < 0');
        $items_arr = $this->db->get()->result_array(); */
        
        $sql = "SELECT	`srp_erp_itemmaster`.`itemSystemCode`,`srp_erp_itemmaster`.`itemDescription`,
                    TRIM(	TRAILING '.' 	FROM(TRIM(TRAILING 0 FROM((ROUND((( ware_house.currentStock - ( ( IFNULL( pq.stock, 0 ))+( srp_erp_stocktransferdetails.transfer_QTY / srp_erp_stocktransferdetails.conversionRateUOM )))), 2 )))))) AS stockDiff ,
                    TRIM(	TRAILING '.' 	FROM(TRIM(	TRAILING 0 FROM((ROUND(( ware_house.currentStock ), 2 )))))) AS availableStock
                FROM
                    `srp_erp_stocktransferdetails`
                JOIN `srp_erp_itemmaster` ON `srp_erp_stocktransferdetails`.`itemAutoID` = `srp_erp_itemmaster`.`itemAutoID` 
                INNER JOIN srp_erp_stocktransfermaster ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID
                LEFT JOIN ( SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID FROM srp_erp_itemledger WHERE companyID = {$companyID} GROUP BY wareHouseAutoID, itemAutoID ) AS ware_house ON ware_house.itemAutoID = srp_erp_stocktransferdetails.itemAutoID AND srp_erp_stocktransfermaster.from_wareHouseAutoID = ware_house.wareHouseAutoID
                LEFT JOIN (
                    SELECT
                        SUM( stock ) AS stock,	t1.ItemAutoID,		wareHouseAutoID 
                    FROM
                        (
                        SELECT
                            IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,	itemAutoID,		srp_erp_stockadjustmentmaster.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_stockadjustmentmaster
                            LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
                        WHERE
                            companyID = {$companyID} 	AND approvedYN != 1 	AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,	itemAutoID,	srp_erp_stockcountingmaster.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_stockcountingmaster
                            LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
                        WHERE
                            companyID = {$companyID} 	AND approvedYN != 1 	AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock,	itemAutoID,	srp_erp_itemissuemaster.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_itemissuemaster
                            LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
                        WHERE
                            srp_erp_itemissuemaster.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( requestedQty / conversionRateUOM ) AS stock,	itemAutoID,		srp_erp_customerreceiptdetail.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_customerreceiptmaster
                            LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                        WHERE
                            srp_erp_customerreceiptmaster.companyID = {$companyID} AND approvedYN != 1 	AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( requestedQty / conversionRateUOM ) AS stock,	itemAutoID,		srp_erp_customerinvoicedetails.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_customerinvoicemaster
                            LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
                        WHERE
                            srp_erp_customerinvoicemaster.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( deliveredQty / conversionRateUOM ) AS stock,	itemAutoID, 	srp_erp_deliveryorderdetails.wareHouseAutoID AS wareHouseAutoID 
                        FROM
                            srp_erp_deliveryorder
                            LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
                        WHERE
                            srp_erp_deliveryorder.companyID = {$companyID} AND approvedYN != 1 AND itemCategory = 'Inventory' UNION ALL
                        SELECT
                            ( transfer_QTY / conversionRateUOM ) AS stock,	itemAutoID,	srp_erp_stocktransfermaster.from_wareHouseAutoID AS from_wareHouseAutoID 
                        FROM
                            srp_erp_stocktransfermaster
                            LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
                        WHERE
                            srp_erp_stocktransfermaster.companyID = {$companyID} AND srp_erp_stocktransfermaster.stockTransferAutoID != '{$system_code}' AND approvedYN != 1 
                            AND itemCategory = 'Inventory' 
                        ) t1 
                    GROUP BY
                        t1.wareHouseAutoID,
                        t1.ItemAutoID 
                    ) AS pq ON pq.ItemAutoID = srp_erp_stocktransferdetails.itemAutoID
                    AND pq.wareHouseAutoID = srp_erp_stocktransfermaster.from_wareHouseAutoID AND pq.wareHouseAutoID = srp_erp_stocktransfermaster.from_wareHouseAutoID 
                WHERE
                    `srp_erp_stocktransferdetails`.`stockTransferAutoID` = '{$system_code}' 
                    AND `srp_erp_stocktransfermaster`.`companyID` = '{$companyID}' 
                    AND `srp_erp_stocktransfermaster`.`from_wareHouseAutoID` = '{$warehouse}' 
                HAVING
                    `stockDiff` < 0";
        $items_arr = $this->db->query($sql)->result_array();

        if($wacRecalculationEnableYN == 0){
            reupdate_companylocalwac('srp_erp_stocktransferdetails',$system_code,'stockTransferAutoID','currentlWacAmount', 'ST');

            $this->db->query("UPDATE srp_erp_stocktransferdetails JOIN(
                SELECT 
                currentlWacAmount*(transfer_QTY/conversionRateUOM) as totalvalrecal,
                stockTransferDetailsID
                FROM 
                srp_erp_stocktransferdetails
                where 
                stockTransferAutoID  = $system_code)wactotal ON  wactotal.stockTransferDetailsID = srp_erp_stocktransferdetails.stockTransferDetailsID 
                SET srp_erp_stocktransferdetails.totalValue = wactotal.totalvalrecal");
        }

        if ($status != 1) {
            $items_arr = [];
        }
        if (!$items_arr) {
            if ($autoappLevel == 0) {
                $approvals_status = 1;
            } else {
                $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'ST');
            }

            if ($approvals_status == 1) {
                $this->db->select('*');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('srp_erp_stocktransferdetails.stockTransferAutoID', $system_code);
                $this->db->join('srp_erp_stocktransfermaster', 'srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID');
                $details_arr = $this->db->get()->result_array();

                $item_arr = array();
                $itemledger_arr = array();
                $transaction_loc_tot = 0;
                $company_rpt_tot = 0;
                $supplier_cr_tot = 0;
                $company_loc_tot = 0;
                $x = 0;
                for ($i = 0; $i < count($details_arr); $i++) {
                    $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                    $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = $item['currentStock'];
                    $item_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $item_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $qty = ($details_arr[$i]['transfer_QTY'] / $details_arr[$i]['conversionRateUOM']);
                    $itemSystemCode = $details_arr[$i]['itemAutoID'];
                    $location = $details_arr[$i]['from_wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty}) WHERE wareHouseAutoID='{$location}' and itemAutoID='{$itemSystemCode}'");
                    $location = $details_arr[$i]['to_wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$location}' and itemAutoID='{$itemSystemCode}'");

                    $itemledger_arr[$x]['documentID'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$x]['documentCode'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$x]['documentAutoID'] = $details_arr[$i]['stockTransferAutoID'];
                    $itemledger_arr[$x]['documentSystemCode'] = $details_arr[$i]['stockTransferCode'];
                    $itemledger_arr[$x]['documentDate'] = $details_arr[$i]['tranferDate'];
                    $itemledger_arr[$x]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                    $itemledger_arr[$x]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                    $itemledger_arr[$x]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                    $itemledger_arr[$x]['FYBegin'] = $details_arr[$i]['FYBegin'];
                    $itemledger_arr[$x]['FYEnd'] = $details_arr[$i]['FYEnd'];
                    $itemledger_arr[$x]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                    $itemledger_arr[$x]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                    $itemledger_arr[$x]['wareHouseAutoID'] = $details_arr[$i]['from_wareHouseAutoID'];
                    $itemledger_arr[$x]['wareHouseCode'] = $details_arr[$i]['form_wareHouseCode'];
                    $itemledger_arr[$x]['wareHouseLocation'] = $details_arr[$i]['form_wareHouseLocation'];
                    $itemledger_arr[$x]['wareHouseDescription'] = $details_arr[$i]['form_wareHouseLocation'];
                    $itemledger_arr[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$x]['transactionQTY'] = ($qty * -1);
                    $itemledger_arr[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$x]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$x]['SUOMID'] = $details_arr[$i]['SUOMID'];
                    $itemledger_arr[$x]['SUOMQty'] = $details_arr[$i]['SUOMQty'];
                    $itemledger_arr[$x]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                    $itemledger_arr[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$x]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                    $itemledger_arr[$x]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                    $itemledger_arr[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$x]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                    $itemledger_arr[$x]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr[$x]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr[$x]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr[$x]['PLType'] = $details_arr[$i]['PLType'];
                    $itemledger_arr[$x]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                    $itemledger_arr[$x]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr[$x]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr[$x]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr[$x]['BLType'] = $details_arr[$i]['BLType'];
                    $itemledger_arr[$x]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$x]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$x]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$x]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['transactionAmount'] = (round($details_arr[$i]['totalValue'], $itemledger_arr[$x]['transactionCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$x]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$x]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$x]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$x]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['companyLocalAmount'] = (round(($details_arr[$i]['totalValue'] / $itemledger_arr[$x]['companyLocalExchangeRate']), $itemledger_arr[$x]['companyLocalCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$x]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $itemledger_arr[$x]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                    $itemledger_arr[$x]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                    $itemledger_arr[$x]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                    $itemledger_arr[$x]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['companyReportingAmount'] = (round(($details_arr[$i]['totalValue'] / $itemledger_arr[$x]['companyReportingExchangeRate']), $itemledger_arr[$x]['companyReportingCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$x]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $itemledger_arr[$x]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                    $itemledger_arr[$x]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr[$x]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                    $itemledger_arr[$x]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr[$x]['approvedYN'] = $details_arr[$i]['approvedYN'];
                    $itemledger_arr[$x]['approvedDate'] = $details_arr[$i]['approvedDate'];
                    $itemledger_arr[$x]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                    $itemledger_arr[$x]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                    $itemledger_arr[$x]['segmentID'] = $details_arr[$i]['segmentID'];
                    $itemledger_arr[$x]['segmentCode'] = $details_arr[$i]['segmentCode'];
                    $itemledger_arr[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                    $itemledger_arr[$x]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $itemledger_arr[$x]['createdUserGroup'] = $this->common_data['user_group'];
                    $itemledger_arr[$x]['createdPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr[$x]['createdUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr[$x]['createdDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr[$x]['createdUserName'] = $this->common_data['current_user'];
                    $itemledger_arr[$x]['modifiedPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr[$x]['modifiedUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr[$x]['modifiedDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr[$x]['modifiedUserName'] = $this->common_data['current_user'];


                    $itemledger_arr_to[$x]['documentID'] = $details_arr[$i]['documentID'];
                    $itemledger_arr_to[$x]['documentCode'] = $details_arr[$i]['documentID'];
                    $itemledger_arr_to[$x]['documentAutoID'] = $details_arr[$i]['stockTransferAutoID'];
                    $itemledger_arr_to[$x]['documentSystemCode'] = $details_arr[$i]['stockTransferCode'];
                    $itemledger_arr_to[$x]['documentDate'] = $details_arr[$i]['tranferDate'];
                    $itemledger_arr_to[$x]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                    $itemledger_arr_to[$x]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                    $itemledger_arr_to[$x]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                    $itemledger_arr_to[$x]['FYBegin'] = $details_arr[$i]['FYBegin'];
                    $itemledger_arr_to[$x]['FYEnd'] = $details_arr[$i]['FYEnd'];
                    $itemledger_arr_to[$x]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                    $itemledger_arr_to[$x]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                    $itemledger_arr_to[$x]['wareHouseAutoID'] = $details_arr[$i]['to_wareHouseAutoID'];
                    $itemledger_arr_to[$x]['wareHouseCode'] = $details_arr[$i]['to_wareHouseCode'];
                    $itemledger_arr_to[$x]['wareHouseLocation'] = $details_arr[$i]['to_wareHouseLocation'];
                    $itemledger_arr_to[$x]['wareHouseDescription'] = $details_arr[$i]['to_wareHouseLocation'];
                    $itemledger_arr_to[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr_to[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr_to[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr_to[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr_to[$x]['transactionQTY'] = $qty;
                    $itemledger_arr_to[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr_to[$x]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr_to[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr_to[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr_to[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr_to[$x]['SUOMID'] = $details_arr[$i]['SUOMID'];
                    $itemledger_arr_to[$x]['SUOMQty'] = $details_arr[$i]['SUOMQty'];
                    $itemledger_arr_to[$x]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                    $itemledger_arr_to[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr_to[$x]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                    $itemledger_arr_to[$x]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                    $itemledger_arr_to[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr_to[$x]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                    $itemledger_arr_to[$x]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr_to[$x]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr_to[$x]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr_to[$x]['PLType'] = $details_arr[$i]['PLType'];
                    $itemledger_arr_to[$x]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                    $itemledger_arr_to[$x]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr_to[$x]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr_to[$x]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr_to[$x]['BLType'] = $details_arr[$i]['BLType'];
                    $itemledger_arr_to[$x]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr_to[$x]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr_to[$x]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr_to[$x]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr_to[$x]['transactionAmount'] = round($details_arr[$i]['totalValue'], $itemledger_arr_to[$x]['transactionCurrencyDecimalPlaces']);
                    $itemledger_arr_to[$x]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr_to[$x]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr_to[$x]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr_to[$x]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr_to[$x]['companyLocalAmount'] = round(($details_arr[$i]['totalValue'] / $itemledger_arr_to[$x]['companyLocalExchangeRate']), $itemledger_arr_to[$x]['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr_to[$x]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $itemledger_arr_to[$x]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                    $itemledger_arr_to[$x]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                    $itemledger_arr_to[$x]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                    $itemledger_arr_to[$x]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr_to[$x]['companyReportingAmount'] = round(($details_arr[$i]['totalValue'] / $itemledger_arr_to[$x]['companyReportingExchangeRate']), $itemledger_arr_to[$x]['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr_to[$x]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $itemledger_arr_to[$x]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                    $itemledger_arr_to[$x]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr_to[$x]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                    $itemledger_arr_to[$x]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr_to[$x]['approvedYN'] = $details_arr[$i]['approvedYN'];
                    $itemledger_arr_to[$x]['approvedDate'] = $details_arr[$i]['approvedDate'];
                    $itemledger_arr_to[$x]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                    $itemledger_arr_to[$x]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                    $itemledger_arr_to[$x]['segmentID'] = $details_arr[$i]['segmentID'];
                    $itemledger_arr_to[$x]['segmentCode'] = $details_arr[$i]['segmentCode'];
                    $itemledger_arr_to[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                    $itemledger_arr_to[$x]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $itemledger_arr_to[$x]['createdUserGroup'] = $this->common_data['user_group'];
                    $itemledger_arr_to[$x]['createdPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr_to[$x]['createdUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr_to[$x]['createdDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr_to[$x]['createdUserName'] = $this->common_data['current_user'];
                    $itemledger_arr_to[$x]['modifiedPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr_to[$x]['modifiedUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr_to[$x]['modifiedDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr_to[$x]['modifiedUserName'] = $this->common_data['current_user'];
                    $x++;
                }

                if (!empty($item_arr)) {
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }
                if (!empty($itemledger_arr)) {
                    $itemledger_arr = array_values($itemledger_arr);
                    //$this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                    if($batchNumberPolicy==1){

                        foreach($itemledger_arr  as $key1=>$ledger){
    
                            if(count($updatedBatchNumberArray)>0){
                                foreach($updatedBatchNumberArray as $bKey=>$batch){
                                    if($ledger['itemAutoID']==$batch['itemAutoID'] && $ledger['wareHouseAutoID']==$batch['from_wareHouseAutoID']){
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
                                            'transactionQTY'=>($batch['qty']*-1),
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
                if (!empty($itemledger_arr_to)) {
                    $itemledger_arr_to = array_values($itemledger_arr_to);
                    //$this->db->insert_batch('srp_erp_itemledger', $itemledger_arr_to);

                    if($batchNumberPolicy==1){

                        foreach($itemledger_arr_to  as $key1=>$ledger){
    
                            if(count($updatedBatchNumberArray)>0){
                                foreach($updatedBatchNumberArray as $bKey=>$batch){
                                    if($ledger['itemAutoID']==$batch['itemAutoID'] && $ledger['wareHouseAutoID']==$batch['to_wareHouseAutoID']){
                                        $itemledger_arr_to[]=[
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
    
                            unset($itemledger_arr_to[$key1]);
    
                        }
    
                        $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr_to);
        
                    }else{
                        $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr_to);
                    }
                }


                $this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_stock_transfer_data($system_code, 'ST');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['stockTransferAutoID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['stockTransferCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['tranferDate'];
                    $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['itemType'];
                    $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['tranferDate'];
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['tranferDate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                    $generalledger_arr[$i]['chequeNumber'] = '';
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
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
                    $generalledger_arr[$i]['partyType'] = '';//'EMP';
                    $generalledger_arr[$i]['partyAutoID'] = '';//$double_entry['master_data']['employeeID'];
                    $generalledger_arr[$i]['partySystemCode'] = '';//$double_entry['master_data']['employeeCode'];
                    $generalledger_arr[$i]['partyName'] = '';//$double_entry['master_data']['employeeName'];
                    $generalledger_arr[$i]['partyCurrency'] = '';//$double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = '';//$double_entry['master_data']['supplierCurrencyExchangeRate'];
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = '';//$double_entry['master_data']['supplierCurrencyDecimalPlaces'];
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
                    $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                    $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                    $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
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

                /** update Manufacturing Job Qty Based on Job Number */
                if($master['jobID']) {
                    $this->updateJobQty($master['jobID'], $system_code, 'ST');
                }
                /**End Of update Manufacturing Job Qty Based on Job Number */

                /** update sub item master sub : shafry */
                if ($isFinalLevel) {
                    $masterID = $this->input->post('stockTransferAutoID');


                    $masterData = $this->db->query("SELECT  * FROM srp_erp_stocktransfermaster WHERE stockTransferAutoID = '" . $masterID . "'")->row_array();

                    $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_sub WHERE soldDocumentID = 'ST' AND isSold='1' AND soldDocumentAutoID = '" . $masterID . "'")->result_array();

                    $is_mfqUsage_exist = $this->db->query("SELECT * FROM srp_erp_mfq_jc_usage where linkedDocumentID = 'ST' and linkedDocumentAutoID =  '{$masterID}'")->row_array();

                    if(!empty($result) && empty($is_mfqUsage_exist)){
                        $i = 0;
                        foreach ($result as $item) {
                            $result[$i]['receivedDocumentID'] = 'ST';
                            $result[$i]['receivedDocumentAutoID'] = $item['soldDocumentAutoID'];
                            $result[$i]['receivedDocumentDetailID'] = $item['soldDocumentDetailID'];
                            $result[$i]['isSold'] = 0;
                            $result[$i]['soldDocumentID'] = null;
                            $result[$i]['soldDocumentDetailID'] = null;
                            $result[$i]['soldDocumentAutoID'] = null;

                            $result[$i]['wareHouseAutoID'] = $masterData['to_wareHouseAutoID'];

                            unset($result[$i]['subItemAutoID']);
                            $i++;
                        }


                        $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    }
                    if (!empty($result) && !empty($is_mfqUsage_exist)) {
                        $i = 0;
                        foreach ($result as $item) {
                            $result[$i]['receivedDocumentID'] = 'ST';
                            $result[$i]['receivedDocumentAutoID'] = $item['soldDocumentAutoID'];
                            $result[$i]['receivedDocumentDetailID'] = $item['soldDocumentDetailID'];
                            $result[$i]['isSold'] = 1;
                            $result[$i]['soldDocumentID'] = 'JOB';
                            $result[$i]['soldDocumentDetailID'] = $is_mfqUsage_exist['jobDetailID'];
                            $result[$i]['soldDocumentAutoID'] = $is_mfqUsage_exist['jobID'];

                            $result[$i]['wareHouseAutoID'] = $masterData['to_wareHouseAutoID'];

                            unset($result[$i]['subItemAutoID']);
                            $i++;
                        }


                        $this->db->insert_batch('srp_erp_itemmaster_sub', $result);

                    }
                }
                $itemAutoIDarry = array();
                foreach ($details_arr as $value) {
                    array_push($itemAutoIDarry, $value['itemAutoID']);
                }
                $companyID = current_companyID();
                $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID= '" . $master ['to_wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID = 0;
                if (!empty($exceededitems_master)) {
                    $this->load->library('sequence');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['tranferDate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['stockTransferAutoID'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['stockTransferCode'];
                    $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $exceededmatch['transactionCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['transactionCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['transactionExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                    $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                    $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                    $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                    $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                    $exceededmatch['FYBegin'] = $master ['FYBegin'];
                    $exceededmatch['FYEnd'] = $master ['FYEnd'];
                    $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                    $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                    $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];
                    $exceededmatch['createdUserGroup'] = $this->common_data['user_group'];
                    $exceededmatch['createdPCID'] = $this->common_data['current_pc'];
                    $exceededmatch['createdUserID'] = $this->common_data['current_userID'];
                    $exceededmatch['createdUserName'] = $this->common_data['current_user'];
                    $exceededmatch['createdDateTime'] = $this->common_data['current_date'];
                    $exceededmatch['documentSystemCode'] = $this->sequence->sequence_generator($exceededmatch['documentID']);
                    $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                    $exceededMatchID = $this->db->insert_id();
                }

                foreach ($details_arr as $itemid) {
                    $receivedQty = $itemid['transfer_QTY'];
                    $receivedQtyConverted = $itemid['transfer_QTY'] / $itemid['conversionRateUOM'];
                    $companyID = current_companyID();
                    $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $master ['to_wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                    $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                    $sumqty = array_column($exceededitems, 'balanceQty');
                    $sumqty = array_sum($sumqty);
                    if (!empty($exceededitems)) {
                        foreach ($exceededitems as $exceededItemAutoID) {
                            if ($receivedQty > 0) {
                                $balanceQty = $exceededItemAutoID['balanceQty'];
                                $updatedQty = $exceededItemAutoID['updatedQty'];
                                $balanceQtyConverted = $exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM'];
                                $updatedQtyConverted = $exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM'];
                                if ($receivedQtyConverted > $balanceQtyConverted) {
                                    $qty = $receivedQty - $balanceQty;
                                    $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                    $receivedQty = $qty;
                                    $receivedQtyConverted = $qtyconverted;
                                    $exeed['balanceQty'] = 0;
                                    //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                    $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                    $exceededmatchdetail['warehouseAutoID'] = $master['to_wareHouseAutoID'];
                                    $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetail['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                    $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                    $exceededmatchdetail['itemCost'] = $exceededItemAutoID['unitCost'];
                                    $exceededmatchdetail['totalValue'] = $balanceQtyConverted * $exceededmatchdetail['itemCost'];
                                    $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetail['createdUserGroup'] = $this->common_data['user_group'];
                                    $exceededmatchdetail['createdPCID'] = $this->common_data['current_pc'];
                                    $exceededmatchdetail['createdUserID'] = $this->common_data['current_userID'];
                                    $exceededmatchdetail['createdUserName'] = $this->common_data['current_user'];
                                    $exceededmatchdetail['createdDateTime'] = $this->common_data['current_date'];

                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                                } else {
                                    $exeed['balanceQty'] = $balanceQtyConverted - $receivedQtyConverted;
                                    $exeed['updatedQty'] = $updatedQtyConverted + $receivedQtyConverted;
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetails['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                    $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetails['warehouseAutoID'] = $master['to_wareHouseAutoID'];
                                    $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetails['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                    $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                    $exceededmatchdetails['itemCost'] = $exceededItemAutoID['unitCost'];
                                    $exceededmatchdetails['totalValue'] = $receivedQtyConverted * $exceededmatchdetails['itemCost'];
                                    $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetails['createdUserGroup'] = $this->common_data['user_group'];
                                    $exceededmatchdetails['createdPCID'] = $this->common_data['current_pc'];
                                    $exceededmatchdetails['createdUserID'] = $this->common_data['current_userID'];
                                    $exceededmatchdetails['createdUserName'] = $this->common_data['current_user'];
                                    $exceededmatchdetails['createdDateTime'] = $this->common_data['current_date'];
                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                    $receivedQty = $receivedQty - $exeed['updatedQty'];
                                    $receivedQtyConverted = $receivedQtyConverted - ($updatedQtyConverted + $receivedQtyConverted);
                                }
                            }
                        }
                    }
                }
                if (!empty($exceededitems_master)) {
                    exceed_double_entry($exceededMatchID);
                }
                //$this->session->set_flashdata('s', 'Stock Transfer Approval Successfully.');
            } /*else {
            $this->session->set_flashdata('s', 'Stock Transfer Approval : Level ' . $level_id . ' Successfully.');
        }*/

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Stock Transfer Approval Failed.', 1);
            } else {
                $this->db->trans_commit();
                return array('s', 'Stock Transfer Approval Successfully.', 1);
            }
        } else {
            return array('e', 'Item quantities are insufficient.', $items_arr);
        }
    }

    function save_stock_return_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals');
        $this->load->library('wac');

        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('stockReturnAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['stockReturnAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'SR');
        }
        reupdate_companylocalwac('srp_erp_stockreturndetails',$system_code,'stockReturnAutoID','currentlWacAmount');
        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->from('srp_erp_stockreturndetails');
            $this->db->where('srp_erp_stockreturndetails.stockReturnAutoID', $system_code);
            $this->db->join('srp_erp_stockreturnmaster', 'srp_erp_stockreturnmaster.stockReturnAutoID = srp_erp_stockreturndetails.stockReturnAutoID');
            $details_arr = $this->db->get()->result_array();

            $item_arr = array();
            $itemledger_arr = array();
            $transaction_loc_tot = 0;
            $company_rpt_tot = 0;
            $supplier_cr_tot = 0;
            $company_loc_tot = 0;
            for ($i = 0; $i < count($details_arr); $i++) {
                if ($details_arr[$i]['itemCategory'] == 'Inventory' or $details_arr[$i]['itemCategory'] == 'Non Inventory' or $details_arr[$i]['itemCategory'] =='Service') {
                    $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                    $qty = ($details_arr[$i]['return_Qty'] / $details_arr[$i]['conversionRateUOM']);
                    $wacAmount = $this->wac->wac_calculation_amounts($details_arr[$i]['itemAutoID'], $details_arr[$i]['unitOfMeasure'], ($details_arr[$i]['return_Qty'] * -1), $details_arr[$i]['transactionCurrency'], $details_arr[$i]['currentlWacAmount']); //get Local and reporitng Amount
                    /*$item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = ($item['currentStock'] - $qty);
                    $item_arr[$i]['companyLocalWacAmount'] = $wacAmount["companyLocalWacAmount"];
                    $item_arr[$i]['companyReportingWacAmount'] = $wacAmount["companyReportingWacAmount"];*/

                    $itemSystemCode = $details_arr[$i]['itemAutoID'];

                    $itemledgerCurrentStock = fetch_itemledger_currentstock($details_arr[$i]['itemAutoID']);
                    $itemledgerTransactionAmountLocalWac = fetch_itemledger_transactionAmount($details_arr[$i]['itemAutoID'], 'companyLocalExchangeRate');
                    $itemledgerTransactionAmountReportingWac = fetch_itemledger_transactionAmount($details_arr[$i]['itemAutoID'],'companyReportingExchangeRate');


                    $location = $details_arr[$i]['wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$location}' and itemAutoID='{$itemSystemCode}'");
                    $itemledger_arr[$i]['documentID'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$i]['documentCode'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['stockReturnAutoID'];
                    $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['stockReturnCode'];
                    $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['returnDate'];
                    $itemledger_arr[$i]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                    $itemledger_arr[$i]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                    $itemledger_arr[$i]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                    $itemledger_arr[$i]['FYBegin'] = $details_arr[$i]['FYBegin'];
                    $itemledger_arr[$i]['FYEnd'] = $details_arr[$i]['FYEnd'];
                    $itemledger_arr[$i]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                    $itemledger_arr[$i]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                    $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['wareHouseAutoID'];
                    $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                    $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                    $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                    $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$i]['transactionQTY'] = ($details_arr[$i]['return_Qty'] * -1);
                    $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$i]['currentStock'] = ($itemledgerCurrentStock - $qty);
                    $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                    $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                    $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                    $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                    $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];
                    $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                    $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];
                    $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['transactionCurrencyID'];
                    $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['transactionCurrency'];
                    $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['transactionExchangeRate'];
                    $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['transactionCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['transactionAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['transactionExchangeRate']), $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyLocalExchangeRate']), $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                    $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                    $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                    $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                    $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];
                    $itemledger_arr[$i]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                    $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                    $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr[$i]['approvedYN'] = $details_arr[$i]['approvedYN'];
                    $itemledger_arr[$i]['approvedDate'] = $details_arr[$i]['approvedDate'];
                    $itemledger_arr[$i]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                    $itemledger_arr[$i]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];

                    $itemledger_arr[$i]['partyCurrencyID'] = $details_arr[$i]['supplierCurrencyID'];
                    $itemledger_arr[$i]['partyCurrency'] = $details_arr[$i]['supplierCurrency'];
                    $itemledger_arr[$i]['partyCurrencyExchangeRate'] = $details_arr[$i]['supplierCurrencyExchangeRate'];
                    $itemledger_arr[$i]['partyCurrencyAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['supplierCurrencyExchangeRate']), $details_arr[$i]['supplierCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['partyCurrencyDecimalPlaces'] = $details_arr[$i]['supplierCurrencyDecimalPlaces'];

                    $itemledger_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                    $itemledger_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $itemledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $itemledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                    $itemledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                }
            }

            /*if (!empty($item_arr)) {
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }*/

            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            //$data['approvedYN']             = $status;
            //$data['approvedbyEmpID']        = $this->common_data['current_userID'];
            //$data['approvedbyEmpName']      = $this->common_data['current_user'];
            //$data['approvedDate']           = $this->common_data['current_date'];
            //$data['companyLocalAmount']     = $company_loc_tot;
            //$data['companyReportingAmount'] = $company_rpt_tot;
            //$data['supplierCurrencyAmount'] = $supplier_cr_tot;
            //$data['transactionAmount']      = $transaction_loc_tot;

            //$this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
            //$this->db->update('srp_erp_stockreturnmaster', $data);
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_stock_return_data($system_code, 'SR');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['stockReturnAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['stockReturnCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['returnDate'];
                $generalledger_arr[$i]['documentType'] = 'Return';
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['returnDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['returnDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = 'SUP';
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['supplierID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['supplierSystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['supplierName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['master_data']['supplierCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['master_data']['supplierCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['master_data']['supplierCurrencyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
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
            $this->session->set_flashdata('s', 'Purchase Return Approved Successfully.');
        } /*else {
            $this->session->set_flashdata('s', 'Purchase Return Approval : Level ' . $level_id . ' Successfully.');
        }*/

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function delete_material_Issue_attachement()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            return false;
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function fetch_item_for_grv()
    {
        //made changes to query - by mubashir (condition receivedQty > 0,grvDate less than stock returndate )

        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $this->db->select('transactionCurrency,returnDate');
        $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
        $this->db->from('srp_erp_stockreturnmaster');
        $currency = $this->db->get()->row_array();

        $secondaryCode = getPolicyValues('SSC', 'All'); 
       
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        /* $this->db->select('grvDetailsID,srp_erp_grvmaster.grvAutoID,grvPrimaryCode,grvDate,srp_erp_grvdetails.itemAutoID,srp_erp_grvdetails.itemSystemCode, srp_erp_grvdetails.itemDescription, (srp_erp_grvdetails.receivedQty-SUM(IFNULL(return_Qty,0))) as receivedQty, srp_erp_grvdetails.receivedAmount,transactionCurrencyDecimalPlaces,srp_erp_grvmaster.transactionCurrency,srp_erp_grvdetails.unitOfMeasure');
         $this->db->from('srp_erp_grvmaster');
         $this->db->join('srp_erp_grvdetails', 'srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID');
         $this->db->join('srp_erp_stockreturndetails', 'srp_erp_grvmaster.grvAutoID = srp_erp_stockreturndetails.grvAutoID AND srp_erp_stockreturndetails.itemAutoID = ' . $itemAutoID . '', "LEFT");
         $this->db->where('srp_erp_grvdetails.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
         $this->db->where('srp_erp_grvmaster.supplierID', trim($this->input->post('supplierID') ?? ''));
         $this->db->where('srp_erp_grvmaster.wareHouseLocation', trim($this->input->post('wareHouseLocation') ?? ''));
         $this->db->where('srp_erp_grvmaster.transactionCurrency', $currency["transactionCurrency"]);
         $this->db->where('srp_erp_grvmaster.grvDate <=', $currency["returnDate"]);
         $this->db->where('srp_erp_grvmaster.approvedYN', 1);
         $this->db->group_by('srp_erp_grvmaster.grvAutoID');
         $this->db->having('receivedQty >', 0);
          $this->db->get()->result_array();*/

        $itemAutoID = $this->input->post('itemAutoID');
        $supplierID = $this->input->post('supplierID');
        $wareHouseLocation = $this->input->post('wareHouseLocation');
        $transactionCurrency = $currency["transactionCurrency"];
        $returnDate = $currency["returnDate"];


        $items = $this->db->query("SELECT
                        *,$item_code
                    FROM
                        (
                            SELECT
                                grvDetailsID,
                                'GRV' as type,
                                srp_erp_grvmaster.grvAutoID,
                                grvPrimaryCode,
                                grvDate,
                                srp_erp_grvdetails.itemAutoID,
                                srp_erp_grvdetails.itemSystemCode,
                                srp_erp_grvdetails.itemDescription,
                                (TRIM( ROUND( (srp_erp_grvdetails.receivedQty - SUM(IFNULL(return_Qty, 0))), 4 ) ) + 0 ) AS receivedQty,
                                /*TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((srp_erp_grvdetails.receivedQty - SUM(IFNULL(return_Qty, 0)))), 2)))))) AS receivedQty,*/
                                srp_erp_grvdetails.receivedAmount + (IFNULL(srp_erp_grvdetails.taxAmount, 0) / srp_erp_grvdetails.receivedQty) AS receivedAmount,
                                transactionCurrencyDecimalPlaces,
                                srp_erp_grvmaster.transactionCurrency,
                                srp_erp_grvdetails.unitOfMeasure
                            FROM
                                srp_erp_grvmaster
                            JOIN srp_erp_grvdetails ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID
                            LEFT JOIN srp_erp_stockreturndetails ON srp_erp_grvmaster.grvAutoID = srp_erp_stockreturndetails.grvAutoID
                            AND srp_erp_stockreturndetails.itemAutoID = $itemAutoID
                            AND srp_erp_stockreturndetails.type = 'GRV'
                            WHERE
                                srp_erp_grvdetails.itemAutoID = $itemAutoID
                            AND srp_erp_grvmaster.supplierID = $supplierID
                            AND srp_erp_grvmaster.wareHouseLocation = '$wareHouseLocation'
                            AND srp_erp_grvmaster.transactionCurrency = '$transactionCurrency'
                            AND srp_erp_grvmaster.grvDate <= '$returnDate'
                            AND srp_erp_grvmaster.approvedYN = 1
                            GROUP BY
                                srp_erp_grvmaster.grvAutoID
                            HAVING
                                receivedQty > 0
                            UNION ALL
                                SELECT
                                    InvoiceDetailAutoID AS grvDetailsID,
                                    'BSI' as type,
                                    srp_erp_paysupplierinvoicemaster.InvoiceAutoID,
                                    bookingInvCode AS grvPrimaryCode,
                                    bookingDate AS grvDate,
                                    srp_erp_paysupplierinvoicedetail.itemAutoID,
                                    srp_erp_paysupplierinvoicedetail.itemSystemCode,
                                    srp_erp_paysupplierinvoicedetail.itemDescription,
                                    (TRIM( ROUND( (srp_erp_paysupplierinvoicedetail.requestedQty - SUM( IFNULL( return_Qty, 0 ))), 4 ) ) + 0 ) AS receivedQty,
                                    /*TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((srp_erp_paysupplierinvoicedetail.requestedQty - SUM( IFNULL( return_Qty, 0 ))), 2)))))) AS receivedQty,*/
                                    /* srp_erp_paysupplierinvoicedetail.unittransactionAmount AS receivedAmount, */
                                    srp_erp_paysupplierinvoicedetail.unittransactionAmount + ((IFNULL(srp_erp_paysupplierinvoicedetail.taxAmount, 0) / srp_erp_paysupplierinvoicedetail.requestedQty)) AS receivedAmount,
                                    srp_erp_paysupplierinvoicemaster.transactionCurrencyDecimalPlaces,
                                    srp_erp_paysupplierinvoicemaster.transactionCurrency,
                                    srp_erp_paysupplierinvoicedetail.unitOfMeasure
                                FROM
                                    srp_erp_paysupplierinvoicemaster
                                JOIN srp_erp_paysupplierinvoicedetail ON srp_erp_paysupplierinvoicedetail.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                                LEFT JOIN srp_erp_stockreturndetails ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_stockreturndetails.grvAutoID
                                AND srp_erp_stockreturndetails.itemAutoID = $itemAutoID
                                AND srp_erp_stockreturndetails.type = 'Item'
                                WHERE
                                    srp_erp_paysupplierinvoicedetail.itemAutoID = $itemAutoID
                                AND srp_erp_paysupplierinvoicemaster.supplierID = $supplierID
                                AND srp_erp_paysupplierinvoicedetail.wareHouseLocation = '$wareHouseLocation'
                                AND srp_erp_paysupplierinvoicemaster.transactionCurrency = '$transactionCurrency'
                                AND srp_erp_paysupplierinvoicemaster.bookingDate <= '$returnDate'
                                AND srp_erp_paysupplierinvoicemaster.approvedYN = 1
                                GROUP BY
                                    srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                                HAVING
                                    receivedQty > 0
                        ) AS results
                        LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = results.ItemAutoID
            ")->result_array();
        return $items;
        // echo  $this->db->last_query();
    }

    function save_grv_base_items()
    {
        $this->db->trans_start();
        $items_arr = array();
        /*$this->db->select('srp_erp_grvdetails.grvAutoID,itemAutoID,itemSystemCode,itemDescription,defaultUOM,unitOfMeasure,conversionRateUOM,receivedQty,itemFinanceCategory,itemFinanceCategorySub,PLGLAutoID,PLSystemGLCode,PLGLCode,PLDescription,PLType,BLGLAutoID,BLSystemGLCode,BLGLCode,BLDescription,BLType,segmentID,segmentCode,receivedAmount ,financeCategory,itemCategory,itemFinanceCategory,defaultUOMID,unitOfMeasureID');
        $this->db->from('srp_erp_grvdetails');
        $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID');
        $this->db->where_in('grvDetailsID', $this->input->post('grvDetailsID'));
        $grv_data = $this->db->get()->result_array();*/

        $qty = $this->input->post('qty');
        $grvDetailsID = $this->input->post('grvDetailsID');
        $types = $this->input->post('types');
        for ($i = 0; $i < count($grvDetailsID); $i++) {
            if ($types[$i] == 'BSI') {
                $this->db->select('srp_erp_paysupplierinvoicedetail.invoiceDetailAutoID as grvDetailAutoID,srp_erp_paysupplierinvoicedetail.InvoiceAutoID as grvAutoID,itemAutoID,itemSystemCode,itemDescription,defaultUOM,unitOfMeasure,conversionRateUOMID as conversionRateUOM,requestedQty as receivedQty,srp_erp_paysupplierinvoicemaster.segmentID,srp_erp_paysupplierinvoicemaster.segmentCode,srp_erp_paysupplierinvoicedetail.transactionAmount as receivedAmount,defaultUOMID,unitOfMeasureID,bookingInvCode,unittransactionAmount,srp_erp_paysupplierinvoicedetail.companyLocalExchangeRate as locexrate');
                $this->db->from('srp_erp_paysupplierinvoicedetail');
                $this->db->join('srp_erp_paysupplierinvoicemaster', 'srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID');
                $this->db->where_in('InvoiceDetailAutoID', $grvDetailsID[$i]);
                $bsi_data = $this->db->get()->row_array();

                $itemdt = fetch_item_data($bsi_data['itemAutoID']);

                $data[$i]['type'] = $types[$i];
                $data[$i]['stockReturnAutoID'] = trim($this->input->post('stockReturnAutoID') ?? '');
                $data[$i]['grvAutoID'] = $bsi_data['grvAutoID'];
                $data[$i]['grvDetailAutoID'] = $bsi_data['grvDetailAutoID'];
                $data[$i]['itemAutoID'] = $bsi_data['itemAutoID'];
                $data[$i]['itemSystemCode'] = $bsi_data['itemSystemCode'];
                $data[$i]['itemDescription'] = $bsi_data['itemDescription'];
                $data[$i]['defaultUOMID'] = $bsi_data['defaultUOMID'];
                $data[$i]['defaultUOM'] = $bsi_data['defaultUOM'];
                $data[$i]['unitOfMeasureID'] = $bsi_data['unitOfMeasureID'];
                $data[$i]['unitOfMeasure'] = $bsi_data['unitOfMeasure'];
                $data[$i]['conversionRateUOM'] = $bsi_data['conversionRateUOM'];
                $data[$i]['return_Qty'] = $qty[$i];
                $data[$i]['received_Qty'] = $bsi_data['receivedQty'];
                $data[$i]['documentSystemCode'] = $bsi_data['bookingInvCode'];

                $data[$i]['itemFinanceCategory'] = $itemdt['subcategoryID'];
                $data[$i]['itemFinanceCategorySub'] = $itemdt['subSubCategoryID'];

                $data[$i]['PLGLAutoID'] = $itemdt['costGLAutoID'];
                $data[$i]['PLSystemGLCode'] = $itemdt['costSystemGLCode'];
                $data[$i]['PLGLCode'] = $itemdt['costGLCode'];
                $data[$i]['PLDescription'] = $itemdt['costDescription'];
                $data[$i]['PLType'] = $itemdt['costType'];
                $data[$i]['BLGLAutoID'] = $itemdt['assteGLAutoID'];
                $data[$i]['BLSystemGLCode'] = $itemdt['assteSystemGLCode'];
                $data[$i]['BLGLCode'] = $itemdt['assteGLCode'];
                $data[$i]['BLDescription'] = $itemdt['assteDescription'];
                $data[$i]['BLType'] = $itemdt['assteType'];
                $data[$i]['segmentID'] = $bsi_data['segmentID'];
                $data[$i]['segmentCode'] = $bsi_data['segmentCode'];
                $data[$i]['currentlWacAmount'] = $bsi_data['unittransactionAmount'];
                $data[$i]['financeCategory'] = $itemdt['financeCategory'];
                $data[$i]['itemCategory'] = $itemdt['mainCategory'];
                //$data[$i]['currentStock']           = $grv_data[$i]['currentStock'];
                //$data[$i]['currentWareHouseStock']  = $grv_data[$i]['currentStock'];
                $data[$i]['totalValue'] = ($data[$i]['currentlWacAmount'] * $data[$i]['return_Qty']);
            } else {
                $this->db->select('srp_erp_grvdetails.grvDetailsID as grvDetailAutoID,srp_erp_grvdetails.grvAutoID,itemAutoID,itemSystemCode,itemDescription,defaultUOM,unitOfMeasure,conversionRateUOM,receivedQty,itemFinanceCategory,itemFinanceCategorySub,PLGLAutoID,PLSystemGLCode,PLGLCode,PLDescription,PLType,BLGLAutoID,BLSystemGLCode,BLGLCode,BLDescription,BLType,segmentID,segmentCode,receivedAmount ,financeCategory,itemCategory,itemFinanceCategory,defaultUOMID,unitOfMeasureID,grvPrimaryCode');
                $this->db->from('srp_erp_grvdetails');
                $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID');
                $this->db->where_in('grvDetailsID', $grvDetailsID[$i]);
                $grv_data = $this->db->get()->row_array();

                $data[$i]['type'] = $types[$i];
                $data[$i]['stockReturnAutoID'] = trim($this->input->post('stockReturnAutoID') ?? '');
                $data[$i]['grvAutoID'] = $grv_data['grvAutoID'];
                $data[$i]['grvDetailAutoID'] = $grv_data['grvDetailAutoID'];
                $data[$i]['itemAutoID'] = $grv_data['itemAutoID'];
                $data[$i]['itemSystemCode'] = $grv_data['itemSystemCode'];
                $data[$i]['itemDescription'] = $grv_data['itemDescription'];
                $data[$i]['defaultUOMID'] = $grv_data['defaultUOMID'];
                $data[$i]['defaultUOM'] = $grv_data['defaultUOM'];
                $data[$i]['unitOfMeasureID'] = $grv_data['unitOfMeasureID'];
                $data[$i]['unitOfMeasure'] = $grv_data['unitOfMeasure'];
                $data[$i]['conversionRateUOM'] = $grv_data['conversionRateUOM'];
                $data[$i]['return_Qty'] = $qty[$i];
                $data[$i]['received_Qty'] = $grv_data['receivedQty'];
                $data[$i]['documentSystemCode'] = $grv_data['grvPrimaryCode'];
                $data[$i]['itemFinanceCategory'] = $grv_data['itemFinanceCategory'];
                $data[$i]['itemFinanceCategorySub'] = $grv_data['itemFinanceCategorySub'];
                $data[$i]['PLGLAutoID'] = $grv_data['PLGLAutoID'];
                $data[$i]['PLSystemGLCode'] = $grv_data['PLSystemGLCode'];
                $data[$i]['PLGLCode'] = $grv_data['PLGLCode'];
                $data[$i]['PLDescription'] = $grv_data['PLDescription'];
                $data[$i]['PLType'] = $grv_data['PLType'];
                $data[$i]['BLGLAutoID'] = $grv_data['BLGLAutoID'];
                $data[$i]['BLSystemGLCode'] = $grv_data['BLSystemGLCode'];
                $data[$i]['BLGLCode'] = $grv_data['BLGLCode'];
                $data[$i]['BLDescription'] = $grv_data['BLDescription'];
                $data[$i]['BLType'] = $grv_data['BLType'];
                $data[$i]['segmentID'] = $grv_data['segmentID'];
                $data[$i]['segmentCode'] = $grv_data['segmentCode'];
                $data[$i]['currentlWacAmount'] = $grv_data['receivedAmount'];
                $data[$i]['financeCategory'] = $grv_data['financeCategory'];
                $data[$i]['itemCategory'] = $grv_data['itemCategory'];
                //$data[$i]['currentStock']           = $grv_data[$i]['currentStock'];
                //$data[$i]['currentWareHouseStock']  = $grv_data[$i]['currentStock'];
                $data[$i]['totalValue'] = ($data[$i]['currentlWacAmount'] * $data[$i]['return_Qty']);
            }
        }
        //echo '<pre>';print_r($data); echo '</pre>'; die();
        $this->db->insert_batch('srp_erp_stockreturndetails', $data);

        /** Added (SME-2992)*/
        $stockReturnAutoID = trim($this->input->post('stockReturnAutoID') ?? '');
        $details = $this->db->query("SELECT * FROM srp_erp_stockreturndetails WHERE stockReturnAutoID = $stockReturnAutoID")->result_array();

        $companyID = current_companyID();
        foreach ($details as $det) {
            $dataExist = $this->db->query("SELECT COUNT(taxLedgerAutoID) as taxledgerID 
                                            FROM srp_erp_taxledger 
                                            WHERE documentID = 'PR' AND companyID = {$companyID} AND documentDetailAutoID =  {$det['stockReturnDetailsID']}"
                                        )->row('taxledgerID');

            if($dataExist == 0) {
                if($det['type'] == 'GRV') {
                    $ledgerDet = $this->db->query("SELECT
                                                            IF(srp_erp_taxmaster.taxCategory = 2, (SELECT vatRegisterYN FROM `srp_erp_company` WHERE company_id = {$companyID}), srp_erp_taxmaster.isClaimable) AS isClaimable,
                                                            supplierCountryID,
                                                            supplierID,
                                                            vatEligible,
                                                            supplierLocationID,
                                                            srp_erp_location.locationType,
                                                            srp_erp_taxledger.*,
                                                            CASE 
                                                                WHEN taxCategory = 2 THEN inputVatGLAccountAutoID
                                                                ELSE taxGlAutoID 
                                                            END AS inputVatGLAccountAutoID,
                                                            transactionAmount 
                                                        FROM
                                                            srp_erp_taxledger
                                                        JOIN (
                                                            SELECT
                                                                SUM( receivedAmount * receivedQty ) AS transactionAmount,
                                                                srp_erp_grvdetails.grvAutoID,
                                                                srp_erp_grvdetails.grvDetailsID,
                                                                supplierID 
                                                            FROM
                                                                srp_erp_grvdetails
                                                                JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID 
                                                            GROUP BY
                                                                grvAutoID,grvDetailsID
                                                        ) mastertbl ON mastertbl.grvDetailsID = srp_erp_taxledger.documentDetailAutoID AND srp_erp_taxledger.documentID = 'GRV'
                                                        LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = mastertbl.supplierID
                                                        LEFT JOIN srp_erp_location ON srp_erp_suppliermaster.supplierLocationID = srp_erp_location.locationID
                                                        JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                                                        JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID 
                                                        WHERE
                                                            documentMasterAutoID = {$det['grvAutoID']}
                                                            AND documentDetailAutoID =  {$det['grvDetailAutoID']}
                                                            ")->result_array();
                
                } else {
                    $ledgerDet = $this->db->query("SELECT
                                                        IF(srp_erp_taxmaster.taxCategory = 2, (SELECT vatRegisterYN FROM `srp_erp_company` WHERE company_id = {$companyID}), srp_erp_taxmaster.isClaimable) AS isClaimable,
                                                        supplierCountryID,
                                                        supplierID,
                                                        vatEligible,
                                                        supplierLocationID,
                                                        srp_erp_location.locationType,
                                                        srp_erp_taxledger.*,
                                                        CASE 
                                                            WHEN taxCategory = 2 THEN inputVatGLAccountAutoID
                                                            ELSE taxGlAutoID 
                                                        END AS inputVatGLAccountAutoID,
                                                        transactionAmount 
                                                    FROM
                                                        srp_erp_taxledger
                                                        JOIN (
                                                            SELECT
                                                                SUM( srp_erp_paysupplierinvoicedetail.transactionAmount ) AS transactionAmount,
                                                                srp_erp_paysupplierinvoicedetail.invoiceAutoID,
                                                                srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID,
                                                                supplierID 
                                                            FROM
                                                                srp_erp_paysupplierinvoicedetail
                                                                JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicedetail.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID 
                                                            GROUP BY
                                                                invoiceAutoID,InvoiceDetailAutoID
                                                        ) mastertbl ON mastertbl.InvoiceDetailAutoID = srp_erp_taxledger.documentDetailAutoID AND srp_erp_taxledger.documentID = 'BSI'
                                                        LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = mastertbl.supplierID
	                                                    LEFT JOIN srp_erp_location ON srp_erp_suppliermaster.supplierLocationID = srp_erp_location.locationID
                                                        JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                                                        JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID 
                                                    WHERE
                                                       documentMasterAutoID = {$det['grvAutoID']}
                                                        AND documentDetailAutoID =  {$det['grvDetailAutoID']}")->result_array();
                }
    
                if(!empty($ledgerDet)) {
                    $taxAmount = 0;
                    foreach ($ledgerDet as $val) {
                        $dataleg['documentID'] = 'PR';
                        $dataleg['documentMasterAutoID'] = $stockReturnAutoID;
                        $dataleg['documentDetailAutoID'] = $det['stockReturnDetailsID'];
                        $dataleg['taxDetailAutoID'] = null;
                        $dataleg['taxPercentage'] = $val['taxPercentage'];
                        $dataleg['ismanuallychanged'] = 0;
                        $dataleg['isClaimable'] = $val['isClaimable'];
                        $dataleg['taxFormulaMasterID'] = $val['taxFormulaMasterID'];
                        $dataleg['taxFormulaDetailID'] = $val['taxFormulaDetailID'];
                        $dataleg['taxMasterID'] = $val['taxMasterID'];
                        $dataleg['amount'] = ($val['amount'] / $val['transactionAmount']) * $det['totalValue'];
                        $dataleg['formula'] = $val['formula'];
                        $dataleg['taxGlAutoID'] = $val['inputVatGLAccountAutoID'];
                        $dataleg['transferGLAutoID'] = null;
                        $dataleg['countryID'] = $val['supplierCountryID'];
                        $dataleg['partyVATEligibleYN'] = $val['vatEligible'];
                        $dataleg['partyID'] = $val['supplierID'];
                        $dataleg['locationID'] = $val['supplierLocationID'];
                        $dataleg['locationType'] = $val['locationType'];
                        $dataleg['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataleg['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataleg['createdUserGroup'] = $this->common_data['user_group'];
                        $dataleg['createdPCID'] = $this->common_data['current_pc'];
                        $dataleg['createdUserID'] = $this->common_data['current_userID'];
                        $dataleg['createdUserName'] = $this->common_data['current_user'];
                        $dataleg['createdDateTime'] = $this->common_data['current_date'];
                        $ledgerEntry = $this->db->insert('srp_erp_taxledger', $dataleg);

                        $taxAmount += ($val['amount'] / $val['transactionAmount']) * $det['totalValue'];
                    }
                    $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                    $data_detailTBL['taxAmount'] = $taxAmount;
                    $this->db->where('stockReturnDetailsID', $det['stockReturnDetailsID']);
                    $this->db->update('srp_erp_stockreturndetails', $data_detailTBL);
                }
            }
        }
        /** End (SME-2992)*/


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Purchase Return : Details Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Purchase Return : ' . count($grvDetailsID) . ' Item Details Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function delete_stockTransfer_attachement()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            return false;
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function delete_return_detail()
    {
        /** update sub item master */
        $id = $this->input->post('stockReturnDetailsID');

        $this->db->select('*');
        $this->db->from('srp_erp_stockreturndetails');
        $this->db->where('stockReturnDetailsID', $id);
        $rTmp = $this->db->get()->row_array();


        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $rTmp['stockReturnAutoID']);
        $this->db->where('soldDocumentDetailID', $rTmp['stockReturnDetailsID']);
        $this->db->where('soldDocumentID', 'SR');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);
        /** end update sub item master */

        $this->db->delete('srp_erp_taxledger', array('documentID' => 'PR','documentMasterAutoID' => $rTmp['stockReturnAutoID'],'documentDetailAutoID' => $rTmp['stockReturnDetailsID']));

        $this->db->delete('srp_erp_stockreturndetails', array('stockReturnDetailsID' => trim($this->input->post('stockReturnDetailsID') ?? '')));
        return true;
    }

    function fetch_inv_item_stock_adjustment()
    {
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['query'] . "%";
        $type = (empty($_GET['t'])) ? 'Inventory' : $_GET['t'];
        $data = $this->db->query('SELECT * FROM (
                                        SELECT 
                                            mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode, partNo as partNo,companyLocalWacAmount,
                                            assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock, barcode,companyLocalSellingPrice,
                                            CONCAT( IFNULL(itemDescription,"empty"), " - ", IFNULL(itemSystemCode,"empty"), " - ", IFNULL(partNo,"empty")  , " - ", IFNULL(seconeryItemCode,"empty")) AS "Match",
                                            CONCAT(itemDescription, " (" ,itemSystemCode,")") as itemDesc,srp_erp_itemmaster.secondaryUOMID as secondaryUOMID,mainCategory 
                                        FROM srp_erp_itemmaster 
                                        WHERE 
                                            companyCode = "' . $companyCode . '" 
                                            AND isActive="1" 
                                            AND mainCategory = "' . $type . '" 
                                            AND masterApprovedYN = "1" 
                                    ) a 
                                    WHERE (
                                        a.itemSystemCode LIKE "' . $search_string . '" 
                                        OR partNo LIKE "' . $search_string . '"
                                        OR a.itemDescription LIKE "' . $search_string . '" 
                                        OR a.seconeryItemCode LIKE "' . $search_string . '" 
                                        OR a.itemDesc LIKE "' . $search_string . '" 
                                        OR a.barcode LIKE "' . $search_string . '"
                                        
                                    ) LIMIT 20')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'itemAutoID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'secondaryUOMID' => $val['secondaryUOMID'],'mainCategory'=>$val['mainCategory']);
            }

        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_inv_item()
    {
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['q'] . "%";
        $type = (empty($_GET['t'])) ? 'Inventory' : $_GET['t'];
        return $this->db->query('SELECT mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT(itemDescription, " (" ,itemSystemCode,")") AS "Match" FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '") AND companyCode = "' . $companyCode . '" AND isActive="1" AND mainCategory = "' . $type . '"')->result_array();
    }

    function delete_purchase_return()
    {
        $masterID = trim($this->input->post('stockReturnID') ?? '');
        /*$this->db->delete('srp_erp_stockreturnmaster', array('stockReturnAutoID' => trim($this->input->post('stockReturnID') ?? '')));
        $this->db->delete('srp_erp_stockreturndetails', array('stockReturnAutoID' => trim($this->input->post('stockReturnID') ?? '')));*/
        $this->db->select('*');
        $this->db->from('srp_erp_stockreturndetails');
        $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnID') ?? ''));
        $datas = $this->db->get()->row_array();

        /*$this->db->select('stockReturnCode');
        $this->db->from('srp_erp_stockreturnmaster');
        $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnID') ?? ''));
        $master = $this->db->get()->row_array();*/
        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {

            /* Added */
            $documentCode = $this->db->get_where('srp_erp_stockreturnmaster', ['stockReturnAutoID'=> $masterID])->row('stockReturnCode');
            $this->db->trans_start();

            $length = strlen($documentCode);
            if($length > 1){
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('stockReturnAutoID', $masterID );
                $this->db->update('srp_erp_stockreturnmaster', $data);
            }
            else{
                $this->db->where('stockReturnAutoID', $masterID)->delete('srp_erp_stockreturndetails');
                $this->db->where('stockReturnAutoID', $masterID)->delete('srp_erp_stockreturnmaster');
            }

            $this->db->trans_complete();
            if($this->db->trans_status() == true){
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }else{
                $this->session->set_flashdata('e', 'Error in delete process.');

                return false;
            }
            /* End */

            /*if ($master['stockReturnCode'] == 0) {
                $this->db->delete('srp_erp_stockreturnmaster', array('stockReturnAutoID' => trim($this->input->post('stockReturnID') ?? '')));
                $this->db->delete('srp_erp_stockreturndetails', array('stockReturnAutoID' => trim($this->input->post('stockReturnID') ?? '')));
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            } else {
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnID') ?? ''));
                $this->db->update('srp_erp_stockreturnmaster', $data);
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }*/
        }
    }

    function delete_stock_adjustment()
    {
        $id = trim($this->input->post('stock_auto_id') ?? '');

        //$this->db->delete('srp_erp_stockadjustmentmaster', array('stockAdjustmentAutoID' => $id));
       /* $this->db->delete('srp_erp_stockadjustmentdetails', array('stockAdjustmentAutoID' => $id));


        $data = array(
            'isDeleted' => 1,
            'deletedEmpID' => current_userID(),
            'deletedDate' => current_date(),
        );
        $this->db->where('stockAdjustmentAutoID', trim($id));
        $this->db->update('srp_erp_stockadjustmentmaster', $data);
        $this->session->set_flashdata('s', 'Deleted Successfully.');
        return true;*/

        $this->db->select('*');
        $this->db->from('srp_erp_stockadjustmentdetails');
        $this->db->where('stockAdjustmentAutoID', $id);
        $datas = $this->db->get()->row_array();
        if (!empty($datas)) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {

            /** Delete sub item list */
            /* 1---- delete all entries in the update process - item master sub temp */
            $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $id, 'receivedDocumentID' => 'SA'));

            /*2-- reset all marked values */
            $setData['isSold'] = null;
            $setData['soldDocumentID'] = null;
            $setData['soldDocumentAutoID'] = null;
            $setData['soldDocumentDetailID'] = null;
            $ware['soldDocumentID'] = 'SA';
            $ware['soldDocumentAutoID'] = $id;
            $this->db->update('srp_erp_itemmaster_sub', $setData, $ware);
            /** End Delete sub item list */



            $documentCode = $this->db->get_where('srp_erp_stockadjustmentmaster', ['stockAdjustmentAutoID'=> $id])->row('stockAdjustmentCode');
            $this->db->trans_start();

            $length = strlen($documentCode);
            if($length > 1){

                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('stockAdjustmentAutoID', trim($id));
                $this->db->update('srp_erp_stockadjustmentmaster', $data);
            }else{
                $this->db->where('stockAdjustmentAutoID', $id)->delete('srp_erp_stockadjustmentdetails');
                $this->db->where('stockAdjustmentAutoID', $id)->delete('srp_erp_stockadjustmentmaster');
            }
            $this->db->trans_complete();
            if($this->db->trans_status() == true){
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }else{
                $this->session->set_flashdata('e', 'Error in delete process.');

                return false;
            }

        }
    }


    function delete_purchaseReturn_attachement()
    {

        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            return false;
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }


    function delete_stockAdjustment_attachement()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            return false;
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function delete_stockTransfer_details()
    {
        $detail_id = trim($this->input->post('stockReturnDetailsID') ?? '');
        $this->db->query("UPDATE srp_erp_itemmaster_sub AS updt_subItem INNER JOIN (
            SELECT subItemAutoID,isSold,soldDocumentID,soldDocumentAutoID,soldDocumentDetailID 
            FROM srp_erp_itemmaster_sub 
            WHERE soldDocumentID = 'ST'  AND soldDocumentDetailID = {$detail_id}) as sel_subItem
            SET updt_subItem.isSold = NULL, updt_subItem.soldDocumentID = NULL, updt_subItem.soldDocumentAutoID = NULL, updt_subItem.soldDocumentDetailID = NULL
          WHERE updt_subItem.soldDocumentID = 'ST' AND updt_subItem.soldDocumentDetailID = {$detail_id}");

        $this->db->delete('srp_erp_stocktransferdetails', array('stockTransferDetailsID' => trim($this->input->post('stockReturnDetailsID') ?? '')));
        return true;
    }

    function delete_stocktransfer_master()
    {
        /*$this->db->delete('srp_erp_stocktransfermaster', array('stockTransferAutoID' => trim($this->input->post('stockTransferAutoID') ?? '')));
        $this->db->delete('srp_erp_stocktransferdetails', array('stockTransferAutoID' => trim($this->input->post('stockTransferAutoID') ?? '')));*/
        /*    $this->db->select('*');
            $this->db->from('srp_erp_stocktransferdetails');
            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
            $datas = $this->db->get()->row_array();
            if ($datas) {
                $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
                return true;
            } else {
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );*/
        $masterID = trim($this->input->post('stockTransferAutoID') ?? '');
        $this->db->select('*');
        $this->db->from('srp_erp_stocktransferdetails');
        $this->db->where('stockTransferAutoID', $masterID);
        $datas = $this->db->get()->row_array();

        if (!empty($datas)) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {

            $documentCode = $this->db->get_where('srp_erp_stocktransfermaster', ['stockTransferAutoID'=> $masterID])->row('stockTransferCode');

            $this->db->trans_start();

            $length = strlen($documentCode);
            if($length > 1){
                $data = array(
                    'jobID' => null,
                    'jobNumber' => null,
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('stockTransferAutoID',$masterID );
                $this->db->update('srp_erp_stocktransfermaster', $data);
            }else{
                $this->db->where('stockTransferAutoID', $masterID)->delete('srp_erp_stocktransferdetails');
                $this->db->where('stockTransferAutoID', $masterID)->delete('srp_erp_stocktransfermaster');
            }
            $this->db->trans_complete();
            if($this->db->trans_status() == true){
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }else{
                $this->session->set_flashdata('e', 'Error in delete process.');

                return false;
            }
        }

    }

    function validate_itemMasterSub($itemAutoID, $documentID)
    {

        switch ($documentID) {
            case "SR":
                $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_stockreturnmaster masterTbl
                    LEFT JOIN srp_erp_stockreturndetails detailTbl ON masterTbl.stockReturnAutoID = detailTbl.stockReturnAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.stockReturnDetailsID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockReturnAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
                $query2 = "SELECT
                        SUM(detailTbl.return_Qty) AS totalQty
                    FROM
                        srp_erp_stockreturnmaster masterTbl
                    LEFT JOIN srp_erp_stockreturndetails detailTbl ON masterTbl.stockReturnAutoID = detailTbl.stockReturnAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockReturnAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";
                break;

            case "MI":
                $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_itemissuemaster masterTbl
                    LEFT JOIN srp_erp_itemissuedetails detailTbl ON masterTbl.itemIssueAutoID = detailTbl.itemIssueAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.itemIssueDetailID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.itemIssueAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
                $query2 = "SELECT
                        SUM(detailTbl.qtyIssued) AS totalQty
                    FROM
                        srp_erp_itemissuemaster masterTbl
                    LEFT JOIN srp_erp_itemissuedetails detailTbl ON masterTbl.itemIssueAutoID = detailTbl.itemIssueAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.itemIssueAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";
                break;

            case "ST":
                $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_stocktransfermaster masterTbl
                    LEFT JOIN srp_erp_stocktransferdetails detailTbl ON masterTbl.stockTransferAutoID = detailTbl.stockTransferAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.stockTransferDetailsID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockTransferAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
                $query2 = "SELECT
                        SUM(IF(itemmaster.subItemapplicableon = 2,detailTbl.SUOMQty,detailTbl.transfer_QTY)) AS totalQty
                    FROM
                        srp_erp_stocktransfermaster masterTbl
                    LEFT JOIN srp_erp_stocktransferdetails detailTbl ON masterTbl.stockTransferAutoID = detailTbl.stockTransferAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockTransferAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";
                break;

            case "SA":
                $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_stockadjustmentmaster masterTbl
                    LEFT JOIN srp_erp_stockadjustmentdetails detailTbl ON masterTbl.stockAdjustmentAutoID = detailTbl.stockAdjustmentAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.stockAdjustmentDetailsAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockAdjustmentAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 AND detailTbl.previousStock > detailTbl.currentStock ";
                $query2 = "SELECT
                        SUM(abs(detailTbl.adjustmentStock)) AS totalQty
                    FROM
                        srp_erp_stockadjustmentmaster masterTbl
                    LEFT JOIN srp_erp_stockadjustmentdetails detailTbl ON masterTbl.stockAdjustmentAutoID = detailTbl.stockAdjustmentAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockAdjustmentAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 AND detailTbl.previousStock > detailTbl.currentStock";
                break;

            case "STB":
                $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_stocktransfermaster_bulk masterTbl
                    LEFT JOIN srp_erp_stocktransferdetails_bulk detailTbl ON masterTbl.stockTransferAutoID = detailTbl.stockTransferAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.stockTransferDetailsID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockTransferAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
                $query2 = "SELECT
                        SUM(detailTbl.transfer_QTY) AS totalQty
                    FROM
                        srp_erp_stocktransfermaster_bulk masterTbl
                    LEFT JOIN srp_erp_stocktransferdetails_bulk detailTbl ON masterTbl.stockTransferAutoID = detailTbl.stockTransferAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockTransferAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";
                break;
            default:
                echo $documentID . ' Error: Code not configured!<br/>';
                echo 'File: ' . __FILE__ . '<br/>';
                echo 'Line No: ' . __LINE__ . '<br><br>';
                exit;
        }

        $r1 = $this->db->query($query1)->row_array();
        //echo $this->db->last_query();

        $r2 = $this->db->query($query2)->row_array();
        //echo $this->db->last_query();

        //exit;

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


    function add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $masterID, $detailID, $code = 'GRV', $itemCode = null, $data = array())
    {

        $uom = isset($data['uom']) && !empty($data['uom']) ? $data['uom'] : null;
        $uomID = isset($data['uomID']) && !empty($data['uomID']) ? $data['uomID'] : null;
        $grv_detailID = isset($data['grv_detailID']) && !empty($data['grv_detailID']) ? $data['grv_detailID'] : null;
        $warehouseAutoID = isset($data['warehouseAutoID']) && !empty($data['warehouseAutoID']) ? $data['warehouseAutoID'] : null;
        $data_subItemMaster = array();
        if ($qty > 0) {
            $x = 0;
            for ($i = 1; $i <= $qty; $i++) {
                $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/SA/' . $grv_detailID . '/' . $i;
                $data_subItemMaster[$x]['uom'] = $uom;
                $data_subItemMaster[$x]['wareHouseAutoID'] = $warehouseAutoID;
                $data_subItemMaster[$x]['uomID'] = $uomID;
                $data_subItemMaster[$x]['receivedDocumentID'] = $code;
                $data_subItemMaster[$x]['receivedDocumentAutoID'] = $masterID;
                $data_subItemMaster[$x]['receivedDocumentDetailID'] = $detailID;
                $data_subItemMaster[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                $data_subItemMaster[$x]['createdUserGroup'] = $this->common_data['user_group'];
                $data_subItemMaster[$x]['createdPCID'] = $this->common_data['current_pc'];
                $data_subItemMaster[$x]['createdUserID'] = $this->common_data['current_userID'];
                $data_subItemMaster[$x]['createdDateTime'] = $this->common_data['current_date'];
                $x++;
            }
        }

        if (!empty($data_subItemMaster)) {
            /** bulk insert to item master sub */
            $this->batch_insert_srp_erp_itemmaster_subtemp($data_subItemMaster);
        }
    }

    function batch_insert_srp_erp_itemmaster_subtemp($data)
    {
        $this->db->insert_batch('srp_erp_itemmaster_subtemp', $data);
    }


    function save_sales_return_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $rtrnDate = $this->input->post('returnDate');
        $returnDate = input_format_date($rtrnDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');

        /*Finance Period */
        if ($financeyearperiodYN == 1) {
            $financeyear_period = $this->input->post('financeyear_period');
            $this->db->select('*');
            $this->db->from('srp_erp_companyfinanceperiod');
            $this->db->where('companyFinancePeriodID', $financeyear_period);
            $companyFinancePeriod = $this->db->get()->row_array();

            $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($returnDate);
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
            $financePeriodDetails = get_financial_period_date_wise($returnDate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {
                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
                $companyFinancePeriod['dateFrom'] = $financePeriodDetails['dateFrom'];
                $companyFinancePeriod['dateTo'] = $financePeriodDetails['dateTo'];
            }
        }

        $location = explode('|', trim($this->input->post('location_dec') ?? ''));
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));

        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $data['documentID'] = 'SLR';
        $data['returnDate'] = trim($returnDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $comment = ($this->input->post('narration'));
        $data['comment'] = str_replace('<br />', PHP_EOL, $comment);
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['customerID'] = trim($this->input->post('customerID') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        $data['FYPeriodDateFrom'] = $companyFinancePeriod['dateFrom'];
        $data['FYPeriodDateTo'] = $companyFinancePeriod['dateTo'];
        $data['wareHouseAutoID'] = trim($this->input->post('location') ?? '');
        $data['wareHouseCode'] = trim($location[0] ?? '');
        $data['wareHouseLocation'] = trim($location[1] ?? '');
        $data['wareHouseDescription'] = trim($location[2] ?? '');
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
        $data['isGroupBasedTax'] = getPolicyValues('GBT', 'All');

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

        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $customerCurrency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate'] = $customerCurrency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customerCurrency['DecimalPlaces'];

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        if (trim($this->input->post('salesReturnAutoID') ?? '')) {
            $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID') ?? ''));
            $this->db->update('srp_erp_salesreturnmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Sales Return : ' . $data['wareHouseDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Sales Return : ' . $data['wareHouseDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('salesReturnAutoID'));
            }
        } else {
            $this->load->library('sequence');

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['salesReturnCode'] = $this->sequence->sequence_generator($data['documentID']);
            $data['salesReturnCode'] = 0;
            $this->db->insert('srp_erp_salesreturnmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Sales Return : ' . $data['wareHouseDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Sales Return : ' . $data['wareHouseDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_customer_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function fetch_template_sales_return_data($salesReturnAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime, DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS returnDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN 
                CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('salesReturnAutoID', $salesReturnAutoID);
        $this->db->from('srp_erp_salesreturnmaster');
        $data['master'] = $this->db->get()->row_array();
        $data['customer'] = $this->db->get_where('srp_erp_customermaster', ['customerAutoID' => $data['master']['customerID']])->row_array();

        $secondaryCode = getPolicyValues('SSC', 'All');
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        $this->db->select("srp_erp_salesreturndetails.*, IFNULL(srp_erp_salesreturndetails.rebateAmount, 0) as returnRebateAmount, IFNULL(inv_mas.invoiceCode, ord_mas.DOCode) mas_code, IFNULL(taxPer, 0) as taxPer, IFNULL(discountPer, 0) as discountPer, IFNULL(inv_mas.isGroupBasedTax, ord_mas.isGroupBasedTax) isGroupBasedTax, IFNULL(srp_erp_salesreturndetails.taxAmount, 0) AS taxAmount, $item_code_alias");
        $this->db->from('srp_erp_salesreturndetails');
        $this->db->where('salesReturnAutoID', $salesReturnAutoID);
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_salesreturndetails.itemAutoID', 'left');
        $this->db->join('srp_erp_customerinvoicemaster AS inv_mas', 'srp_erp_salesreturndetails.invoiceAutoID = inv_mas.invoiceAutoID', 'left');
        $this->db->join('srp_erp_deliveryorder AS ord_mas', 'srp_erp_salesreturndetails.DOAutoID = ord_mas.DOAutoID', 'left');
        $this->db->join('(SELECT SUM(taxPercentage) as taxPer, salesReturnDetailsID FROM srp_erp_salesreturntaxdetails GROUP BY salesReturnDetailsID)taxTable', 'taxTable.salesReturnDetailsID = srp_erp_salesreturndetails.salesReturnDetailsID', 'left');
        $this->db->join('(SELECT SUM(discountPercentage) AS discountPer, salesReturnDetailsID FROM srp_erp_salesreturndiscountdetails GROUP BY salesReturnDetailsID) discountTable', 'discountTable.salesReturnDetailsID = srp_erp_salesreturndetails.salesReturnDetailsID', 'left');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_template_sales_return_buyback_data($salesReturnAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS returnDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN 
CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('salesReturnAutoID', $salesReturnAutoID);
        $this->db->from('srp_erp_salesreturnmaster');
        $data['master'] = $this->db->get()->row_array();
        $data['customer'] = $this->db->get_where('srp_erp_customermaster', ['customerAutoID' => $data['master']['customerID']])->row_array();
        
        $secondaryCode = getPolicyValues('SSC', 'All');
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        $this->db->select("inv_det.totalAfterTax, inv_det.taxAmount, inv_det.requestedQty as invRequestedQty, srp_erp_salesreturndetails.*, IFNULL(inv_mas.invoiceCode, ord_mas.DOCode) mas_code, $item_code_alias");
        $this->db->from('srp_erp_salesreturndetails');
        $this->db->where('salesReturnAutoID', $salesReturnAutoID);
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_salesreturndetails.itemAutoID', 'left');
        $this->db->join('srp_erp_customerinvoicemaster AS inv_mas', 'srp_erp_salesreturndetails.invoiceAutoID = inv_mas.invoiceAutoID', 'left');
        $this->db->join('srp_erp_customerinvoicedetails AS inv_det', 'inv_det.invoiceAutoID = inv_mas.invoiceAutoID AND inv_det.InvoiceDetailsAutoID = srp_erp_salesreturndetails.invoiceDetailID', 'left');
        $this->db->join('srp_erp_deliveryorder AS ord_mas', 'srp_erp_salesreturndetails.DOAutoID = ord_mas.DOAutoID', 'left');
        $data['detail'] = $this->db->get()->result_array();

        return $data;
    }

    function load_sales_return_header()
    {
        update_group_based_tax('srp_erp_salesreturnmaster', 'salesReturnAutoID', $this->input->post('salesReturnAutoID'), null, null, 'SLR');

        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS returnDate');
        $this->db->where('salesReturnAutoID', $this->input->post('salesReturnAutoID'));
        return $this->db->get('srp_erp_salesreturnmaster')->row_array();
    }

    function fetch_sales_return_details()
    {
        $secondaryCode = getPolicyValues('SSC', 'All');
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

         /* inv_det.taxAmount as taxAmount,
            ((((detailTbl.totalValue / 100) * (100 - IFNULL(discountPer, 0)))/100) * IFNULL(taxPer, 0)) as taxAmount, */
        $this->db->select(" case when inv_mas.isGroupBasedTax = 1 THEN detailTbl.taxAmount
                                when ord_mas.isGroupBasedTax = 1 then detailTbl.taxAmount
                                ELSE ((((detailTbl.totalValue / 100) * (100 - IFNULL( discountPer, 0 )))/ 100) * IFNULL( taxPer, 0 )) 
                            END as taxAmount,
            ((detailTbl.salesPrice / 100) * (100 - IFNULL(discountPer, 0))) as salesPrice,
            ((((detailTbl.totalValue / 100) * (100 - IFNULL(discountPer, 0)))/100) * (100 + IFNULL(taxPer, 0)) + IFNULL(detailTbl.taxAmount,0)) as totalFinal,
            inv_det.requestedQty as invRequestedQty, inv_det.totalAfterTax as totalAfterTax,IFNULL(detailTbl.noOfItems,0) as noOfItems,IFNULL(detailTbl.grossQty,0) as grossQty,IFNULL(detailTbl.noOfUnits,0) as noOfUnits,
            IFNULL(detailTbl.deduction,0)as deduction,detailTbl.itemDescription,detailTbl.unitOfMeasure,srp_erp_salesreturnmaster.transactionCurrencyDecimalPlaces,detailTbl.currentWacAmount,
            detailTbl.return_Qty,detailTbl.totalValue,detailTbl.salesReturnDetailsID,srp_erp_itemmaster.isSubitemExist,srp_erp_salesreturnmaster.wareHouseAutoID, 
            IFNULL(inv_mas.invoiceCode, ord_mas.DOCode) mas_code, $item_code_alias");
        $this->db->from('srp_erp_salesreturndetails detailTbl');
        $this->db->where('detailTbl.salesReturnAutoID', trim($this->input->post('salesReturnAutoID') ?? ''));
        $this->db->join('srp_erp_salesreturnmaster', 'detailTbl.salesReturnAutoID = srp_erp_salesreturnmaster.salesReturnAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = detailTbl.itemAutoID', 'left');
        $this->db->join('srp_erp_customerinvoicemaster AS inv_mas', 'detailTbl.invoiceAutoID = inv_mas.invoiceAutoID', 'left');
        $this->db->join('(SELECT SUM(taxPercentage) as taxPer, salesReturnDetailsID FROM srp_erp_salesreturntaxdetails GROUP BY salesReturnDetailsID)taxtble', 'taxtble.salesReturnDetailsID = detailTbl.salesReturnDetailsID', 'left');
        $this->db->join('(SELECT SUM(discountPercentage) AS discountPer, salesReturnDetailsID FROM srp_erp_salesreturndiscountdetails GROUP BY salesReturnDetailsID) discountTable', 'discountTable.salesReturnDetailsID = detailTbl.salesReturnDetailsID', 'left');
        $this->db->join('srp_erp_customerinvoicedetails AS inv_det', 'inv_det.invoiceAutoID = inv_mas.invoiceAutoID AND inv_det.InvoiceDetailsAutoID = detailTbl.invoiceDetailID', 'left');
        $this->db->join('srp_erp_deliveryorder AS ord_mas', 'detailTbl.DOAutoID = ord_mas.DOAutoID', 'left');
        $data['detail'] = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function fetch_item_for_sales_return()
    {
        //made changes to query - by mubashir (condition receivedQty > 0,grvDate less than stock returndate )
        $this->db->select('transactionCurrency,returnDate,wareHouseAutoID');
        $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID') ?? ''));
        $this->db->from('srp_erp_salesreturnmaster');
        $currency = $this->db->get()->row_array();

        /*$this->db->select('invoiceDetailsAutoID,mainTable.invoiceAutoID,invoiceCode,invoiceDate,detailTbl.itemAutoID,detailTbl.itemSystemCode, detailTbl.itemDescription, (detailTbl.requestedQty-SUM(IFNULL(srp_erp_salesreturndetails.return_Qty,0))) as requestedQty, (detailTbl.unittransactionAmount-detailTbl.discountAmount) as transactionAmount,transactionCurrencyDecimalPlaces,mainTable.transactionCurrency,detailTbl.unitOfMeasure');
        $this->db->from('srp_erp_customerinvoicemaster mainTable');
        $this->db->join('srp_erp_customerinvoicedetails detailTbl', 'detailTbl.invoiceAutoID = mainTable.invoiceAutoID');
        $this->db->join('srp_erp_salesreturndetails', 'mainTable.invoiceAutoID = srp_erp_salesreturndetails.invoiceAutoID AND detailTbl.itemAutoID = srp_erp_salesreturndetails.itemAutoID', "LEFT");
        $this->db->where('detailTbl.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        $this->db->where('mainTable.customerID', trim($this->input->post('customerID') ?? ''));
        //$this->db->where('mainTable.wareHouseLocation', trim($this->input->post('wareHouseLocation') ?? ''));
        $this->db->where('mainTable.transactionCurrency', $currency["transactionCurrency"]);
        $this->db->where('mainTable.invoiceDate <=', $currency["returnDate"]);
        $this->db->where('mainTable.approvedYN', 1);
        $this->db->where('detailTbl.wareHouseAutoID', $currency['wareHouseAutoID']);
        $this->db->group_by('mainTable.invoiceAutoID');
        $this->db->having('requestedQty >', 0);
        $r = $this->db->get()->result_array();*/

        $item_id = trim($this->input->post('itemAutoID') ?? '');
        $customer_id = trim($this->input->post('customerID') ?? '');
        $searchByItemInvice = trim($this->input->post('searchByItemInvice') ?? '');
        $invoice_code = trim($this->input->post('invoice_code') ?? '');

        $ware_house = $currency['wareHouseAutoID'];
        $return_date = $currency['returnDate'];
        $currency = $currency['transactionCurrency'];
        $company_id = current_companyID();

        if($searchByItemInvice == 1){
            
                $r = $this->db->query("SELECT mainTable.documentID, cs.customerAutoID , cs.customerName, cs.customerSystemCode,
                invoiceDetailsAutoID, mainTable.invoiceAutoID, invoiceCode, invoiceDate, detailTbl.itemAutoID, detailTbl.itemSystemCode, detailTbl.itemDescription,
                (TRIM( ROUND( detailTbl.requestedQty, 4 ) ) + 0 ) - (TRIM( ROUND( SUM( IFNULL( srp_erp_salesreturndetails.return_Qty, 0 ) ), 4 ) ) + 0 ) AS requestedQty,
                -- TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((detailTbl.requestedQty - SUM(IFNULL( srp_erp_salesreturndetails.return_Qty, 0 ))))))) AS requestedQty,
                transactionCurrencyDecimalPlaces, 
                mainTable.transactionCurrency, detailTbl.unitOfMeasure,isGroupBasedTax,
                CASE 
                    WHEN isGroupBasedTax = 1 THEN (( detailTbl.unittransactionAmount - detailTbl.discountAmount )/ 100) * (100 - IFNULL(masterDiscountPercentage, 0)) + (IFNULL(detailTbl.taxAmount, 0)/detailTbl.requestedQty)
                    ELSE (( detailTbl.unittransactionAmount - detailTbl.discountAmount )/ 100) * (100 - IFNULL(masterDiscountPercentage, 0))
                END AS transactionAmount
            FROM srp_erp_customerinvoicemaster mainTable
            JOIN srp_erp_customerinvoicedetails detailTbl ON detailTbl.invoiceAutoID = mainTable.invoiceAutoID
            JOIN srp_erp_customermaster cs on cs.customerAutoID = mainTable.customerID
            LEFT JOIN srp_erp_salesreturndetails ON detailTbl.invoiceDetailsAutoID = srp_erp_salesreturndetails.invoiceDetailID
            -- LEFT JOIN srp_erp_salesreturndetails ON mainTable.invoiceAutoID = srp_erp_salesreturndetails.invoiceAutoID AND detailTbl.itemAutoID = srp_erp_salesreturndetails.itemAutoID
            LEFT JOIN (SELECT SUM(discountPercentage) as masterDiscountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID)discountPercentage ON discountPercentage.invoiceAutoID = mainTable.invoiceAutoID
            WHERE detailTbl.itemAutoID = '{$item_id}' AND mainTable.customerID = {$customer_id}
                AND mainTable.transactionCurrency = '{$currency}' AND mainTable.invoiceDate <= '{$return_date}'
                AND mainTable.approvedYN = 1 
                AND detailTbl.wareHouseAutoID = '{$ware_house}' AND mainTable.companyID = {$company_id}
            GROUP BY detailTbl.invoiceDetailsAutoID
            HAVING requestedQty >0
            
            UNION ALL
            
            SELECT ord_mas.documentID, cs.customerAutoID , cs.customerName, cs.customerSystemCode,
            ord_det.DODetailsAutoID, ord_mas.DOAutoID, DOCode, DODate, ord_det.itemAutoID, ord_det.itemSystemCode, ord_det.itemDescription, 
                (TRIM( ROUND( ord_det.requestedQty - SUM( IFNULL( ret_det.return_Qty, 0 )), 4)) + 0) AS requestedQty,
                -- TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM (ROUND(ord_det.requestedQty - SUM( IFNULL( ret_det.return_Qty, 0 )) , 2 ))))) AS requestedQty,
                transactionCurrencyDecimalPlaces, 
                ord_mas.transactionCurrency, ord_det.unitOfMeasure,isGroupBasedTax,
                CASE 
                    WHEN isGroupBasedTax = 1 THEN ((ord_det.unittransactionAmount-ord_det.discountAmount) + (IFNULL(ord_det.taxAmount, 0)/ord_det.requestedQty)) 
                    ELSE (ord_det.unittransactionAmount-ord_det.discountAmount)
                END AS transactionAmount
            FROM srp_erp_deliveryorder AS ord_mas
            JOIN srp_erp_deliveryorderdetails AS ord_det ON ord_mas.DOAutoID = ord_det.DOAutoID
            JOIN srp_erp_customermaster AS cs on cs.customerAutoID = ord_mas.customerID
            -- LEFT JOIN srp_erp_salesreturndetails AS ret_det ON ord_mas.DOAutoID = ret_det.DOAutoID  AND ord_det.itemAutoID = ret_det.itemAutoID
            LEFT JOIN srp_erp_salesreturndetails AS ret_det ON ord_det.DODetailsAutoID = ret_det.DODetailsAutoID 
            WHERE ord_det.itemAutoID = '{$item_id}' AND ord_mas.customerID = {$customer_id}
            AND ord_mas.transactionCurrency = '{$currency}' AND ord_mas.DODate <= '{$return_date}'
            AND ord_mas.approvedYN = 1 
            AND ord_det.wareHouseAutoID = '{$ware_house}' AND ord_mas.companyID = {$company_id} 
            /* AND Welcome@321
            
            NOT EXISTS (
                SELECT ord_mas.DOAutoID FROM srp_erp_customerinvoicedetails cus_inv WHERE cus_inv.companyID = {$company_id} 
                AND cus_inv.DOMasterID = ord_mas.DOAutoID
            )*/
            GROUP BY ord_det.DODetailsAutoID
            HAVING requestedQty >0")->result_array();

        }else if($searchByItemInvice == 2){

            $r = $this->db->query("SELECT mainTable.documentID, cs.customerAutoID , cs.customerName, cs.customerSystemCode,
                    invoiceDetailsAutoID, mainTable.invoiceAutoID, invoiceCode, invoiceDate, detailTbl.itemAutoID, detailTbl.itemSystemCode, detailTbl.itemDescription,
                    (TRIM( ROUND( detailTbl.requestedQty, 4 ) ) + 0 ) - (TRIM( ROUND( SUM( IFNULL( srp_erp_salesreturndetails.return_Qty, 0 ) ), 4 ) ) + 0 ) AS requestedQty,
                    -- TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((detailTbl.requestedQty - SUM(IFNULL( srp_erp_salesreturndetails.return_Qty, 0 ))))))) AS requestedQty,
                    transactionCurrencyDecimalPlaces, 
                    mainTable.transactionCurrency, detailTbl.unitOfMeasure,isGroupBasedTax,
                    CASE 
                        WHEN isGroupBasedTax = 1 THEN (( detailTbl.unittransactionAmount - detailTbl.discountAmount )/ 100) * (100 - IFNULL(masterDiscountPercentage, 0)) + (IFNULL(detailTbl.taxAmount, 0)/detailTbl.requestedQty)
                        ELSE (( detailTbl.unittransactionAmount - detailTbl.discountAmount )/ 100) * (100 - IFNULL(masterDiscountPercentage, 0))
                    END AS transactionAmount
                FROM srp_erp_customerinvoicemaster mainTable
                JOIN srp_erp_customerinvoicedetails detailTbl ON detailTbl.invoiceAutoID = mainTable.invoiceAutoID
                JOIN srp_erp_customermaster cs on cs.customerAutoID = mainTable.customerID
                LEFT JOIN srp_erp_salesreturndetails ON detailTbl.invoiceDetailsAutoID = srp_erp_salesreturndetails.invoiceDetailID
                -- LEFT JOIN srp_erp_salesreturndetails ON mainTable.invoiceAutoID = srp_erp_salesreturndetails.invoiceAutoID AND detailTbl.itemAutoID = srp_erp_salesreturndetails.itemAutoID
                LEFT JOIN (SELECT SUM(discountPercentage) as masterDiscountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID)discountPercentage ON discountPercentage.invoiceAutoID = mainTable.invoiceAutoID
                WHERE mainTable.invoiceCode = '{$invoice_code}'
                GROUP BY detailTbl.invoiceDetailsAutoID HAVING requestedQty > 0 ")->result_array();
      
                
            
                

        }
       

        //echo $this->db->last_query();
        return $r;
    }

    function save_sales_return_detail_items()
    {
        $invoiceAutoID = $this->input->post('invoiceDetailsAutoID');
        $doc_id = $this->input->post('doc_id');
        $salesReturnAutoID = $this->input->post('salesReturnAutoID');
        $companyID = current_companyID();
        $currentTime = format_date_mysql_datetime();
        $inv_arr = []; //invoice
        $order_arr = []; //Delivery order
        foreach ($invoiceAutoID as $key => $row) {
            if ($doc_id[$key] == 'DO') {
                $order_arr[] = $row;
            } else {
                $inv_arr[] = $row;
            }
        }

        /*$invoiceIDs = join(',', $invoiceAutoID);

        $this->db->select('detailTbl.wareHouseAutoID,detailTbl.invoiceAutoID,itemAutoID,itemSystemCode,itemDescription,defaultUOM,unitOfMeasure,conversionRateUOM,requestedQty,
        expenseGLAutoID, expenseSystemGLCode, expenseGLCode, expenseGLDescription, expenseGLType, revenueGLAutoID, revenueGLCode, revenueSystemGLCode , revenueGLDescription ,
        revenueGLType, assetGLAutoID,  assetGLCode,  assetSystemGLCode,  assetGLDescription, assetGLType, detailTbl.segmentID, detailTbl.segmentCode, detailTbl.transactionAmount,
        itemCategory, defaultUOMID, unitOfMeasureID, detailTbl.itemCategory, (detailTbl.unittransactionAmount-detailTbl.discountAmount) as unittransactionAmount, detailTbl.companyLocalWacAmount');
        $this->db->from('srp_erp_customerinvoicedetails as detailTbl');
        $this->db->join('srp_erp_customerinvoicemaster as masterTbl', 'masterTbl.invoiceAutoID = detailTbl.invoiceAutoID');
        $this->db->where('detailTbl.invoiceDetailsAutoID IN (' . $invoiceIDs . ')');
        $itemDetailList = $this->db->get()->result_array();*/

        $str = "";
        if (!empty($order_arr)) {
            $order_list = join(',', $order_arr);

            $str = "SELECT 0 as rebatePercentage, null as rebateGLAutoID, masterTbl.DOCode AS mas_doc_code, detailTbl.DODetailsAutoID AS det_id, masterTbl.documentID, detailTbl.DODetailsAutoID AS detail_line, detailTbl.wareHouseAutoID, detailTbl.DOAutoID AS masID, itemAutoID, itemSystemCode, itemDescription, defaultUOM, unitOfMeasure, conversionRateUOM, 
                    requestedQty, expenseGLAutoID, expenseSystemGLCode, expenseGLCode, expenseGLDescription, expenseGLType, revenueGLAutoID, revenueGLCode, revenueSystemGLCode, revenueGLDescription, 
                    revenueGLType, assetGLAutoID, assetGLCode, assetSystemGLCode, assetGLDescription, assetGLType, detailTbl.segmentID, detailTbl.segmentCode, detailTbl.transactionAmount,
                    itemCategory, defaultUOMID, unitOfMeasureID, detailTbl.itemCategory, (detailTbl.unittransactionAmount-detailTbl.discountAmount) AS unittransactionAmount, detailTbl.companyLocalWacAmount,
                    isGroupBasedTax, IFNULL(taxAmount, 0)/requestedQty as taxAmount, detailTbl.taxCalculationformulaID
                    FROM srp_erp_deliveryorderdetails AS detailTbl
                    JOIN srp_erp_deliveryorder AS masterTbl ON masterTbl.DOAutoID = detailTbl.DOAutoID
                    WHERE detailTbl.DODetailsAutoID IN ({$order_list})";
        }

        if (!empty($inv_arr)) {
            $invoiceIDs = join(',', $inv_arr);
            $str .= ($str == '') ? "" : " UNION ALL ";

            $str .= "SELECT rebatePercentage, 
                            rebateGLAutoID, 
                            masterTbl.invoiceCode AS mas_doc_code,
                            detailTbl.invoiceDetailsAutoID AS det_id, 
                            masterTbl.documentID,  
                            detailTbl.invoiceDetailsAutoID AS detail_line, 
                            detailTbl.wareHouseAutoID, detailTbl.invoiceAutoID AS masID, 
                            itemAutoID, itemSystemCode, itemDescription, defaultUOM, unitOfMeasure, conversionRateUOM, requestedQty, 
                            expenseGLAutoID, expenseSystemGLCode, expenseGLCode, expenseGLDescription, expenseGLType, 
                            revenueGLAutoID, revenueGLCode, revenueSystemGLCode, revenueGLDescription, revenueGLType, 
                            assetGLAutoID, assetGLCode, assetSystemGLCode, assetGLDescription, assetGLType,
                            detailTbl.segmentID, detailTbl.segmentCode, detailTbl.transactionAmount, 
                            itemCategory, defaultUOMID, unitOfMeasureID, detailTbl.itemCategory, 
                            /* (((detailTbl.unittransactionAmount - IFNULL( detailTbl.discountAmount, 0 ))/100) * (100 - IFNULL(masterDiscountPercentage, 0))) AS unittransactionAmount,*/
                            (detailTbl.unittransactionAmount-IFNULL(detailTbl.discountAmount, 0)) AS unittransactionAmount,
                            detailTbl.companyLocalWacAmount,
                            isGroupBasedTax, IFNULL(taxAmount, 0)/requestedQty as taxAmount, detailTbl.taxCalculationformulaID
                    FROM srp_erp_customerinvoicedetails AS detailTbl
                    JOIN srp_erp_customerinvoicemaster AS masterTbl ON masterTbl.invoiceAutoID = detailTbl.invoiceAutoID
                    LEFT JOIN (SELECT SUM(discountPercentage) as masterDiscountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID)discountPercentage ON discountPercentage.invoiceAutoID = detailTbl.invoiceAutoID
                    WHERE detailTbl.invoiceDetailsAutoID IN ({$invoiceIDs})";
        }

        if ($str == '') {
            return ['e', 'No data found for proceed'];
        }

        $items_arr = $this->db->query($str)->result_array();
        ///echo '<pre>'.$this->db->last_query().'</pre>';       die();
        if (empty($items_arr)) {
            return ['e', 'No data found for proceed'];
        }

        $i = 0;
        $qty = $this->input->post('qty');

        $this->db->trans_start();

        foreach ($items_arr as $item) {
            $itemAutoID = $item['itemAutoID'];
            $wareHouseAutoID = $item['wareHouseAutoID'];
            $key_qty = $item['documentID'] . '_' . $item['detail_line'];

            if (!array_key_exists($key_qty, $qty)) {
                return ['e', 'Return qty is not found on document : ' . $item['mas_doc_code'], $key_qty];
            }
            $return_Qty = $qty[$key_qty];

            /** item Master */
            $this->db->select('*');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $itemMaster = $this->db->get()->row_array();

            /** warehouse item Master */
            $this->db->select('*');
            $this->db->from('srp_erp_warehouseitems');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
            $itemWarehouseMaster = $this->db->get()->row_array();

            $data['salesReturnAutoID'] = $salesReturnAutoID;
            if ($item['documentID'] == 'CINV') {
                $data['invoiceAutoID'] = $item['masID'];
                $data['invoiceDetailID'] = $item['det_id'];
                $data['DOAutoID'] = null;
                $data['DODetailsAutoID'] = null;

                if(!empty($item['rebatePercentage'])) {
                    $data['rebatePercentage'] = $item['rebatePercentage'];
                    $data['rebateGLAutoID'] = $item['rebateGLAutoID'];
                    $data['rebateAmount'] = ($return_Qty * ($item['unittransactionAmount'] + $item['taxAmount'])) * ($item['rebatePercentage']/100);
                }
            } else {
                $data['invoiceAutoID'] = null;
                $data['DOAutoID'] = $item['masID'];
                $data['invoiceDetailID'] = null;
                $data['DODetailsAutoID'] = $item['det_id'];
            }
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item['itemSystemCode'];
            $data['itemDescription'] = $item['itemDescription'];
            $data['itemCategory'] = $item['itemCategory'];
            $data['unitOfMeasureID'] = $item['unitOfMeasureID'];
            $data['unitOfMeasure'] = $item['unitOfMeasure'];
            $data['defaultUOMID'] = $item['defaultUOMID'];
            $data['defaultUOM'] = $item['defaultUOM'];
            $data['conversionRateUOM'] = $item['conversionRateUOM'];
            $data['return_Qty'] = $return_Qty;
            $data['issued_Qty'] = $item['requestedQty'];
            $data['currentStock'] = $itemMaster['currentStock'];
            $data['currentWareHouseStock'] = $itemWarehouseMaster['currentStock'];
            $data['currentWacAmount'] = $item['companyLocalWacAmount'];
            $data['salesPrice'] = $item['unittransactionAmount'];
            if($item['isGroupBasedTax'] == 1) {
                $data['taxAmount'] = $item['taxAmount'];
                $data['taxCalculationformulaID'] = $item['taxCalculationformulaID'];
            }
            $data['totalValue'] = $return_Qty * $item['unittransactionAmount'];
            $data['segmentID'] = $item['segmentID'];
            $data['segmentCode'] = $item['segmentCode'];
            $data['expenseGLAutoID'] = $item['expenseGLAutoID'];
            $data['expenseSystemGLCode'] = $item['expenseSystemGLCode'];
            $data['expenseGLCode'] = $item['expenseGLCode'];
            $data['expenseGLDescription'] = $item['expenseGLDescription'];
            $data['expenseGLType'] = $item['expenseGLType'];
            $data['revenueGLAutoID'] = $item['revenueGLAutoID'];
            $data['revenueGLCode'] = $item['revenueGLCode'];
            $data['revenueSystemGLCode'] = $item['revenueSystemGLCode'];
            $data['revenueGLDescription'] = $item['revenueGLDescription'];
            $data['revenueGLType'] = $item['revenueGLType'];
            $data['assetGLAutoID'] = $item['assetGLAutoID'];
            $data['assetGLCode'] = $item['assetGLCode'];
            $data['assetSystemGLCode'] = $item['assetSystemGLCode'];
            $data['assetGLDescription'] = $item['assetGLDescription'];
            $data['assetGLType'] = $item['assetGLType'];
            $data['comments'] = '';
            $data['companyID'] = $companyID;
            $data['timestamp'] = $currentTime;

            $this->db->insert('srp_erp_salesreturndetails', $data);
            $last_id = $this->db->insert_id();
            
            /** Added : (SME-2990)*/
            $dataExist = $this->db->query("SELECT COUNT(taxLedgerAutoID) as taxledgerID FROM srp_erp_taxledger WHERE documentID = 'SLR' AND companyID = {$companyID} AND documentDetailAutoID = {$last_id}")->row('taxledgerID');

            if($dataExist == 0) {
                if($item['documentID'] == 'CINV') {
                    $ledgerDet = $this->db->query("SELECT
                                    IF(srp_erp_taxmaster.taxCategory = 2, (SELECT vatRegisterYN FROM `srp_erp_company` WHERE company_id = {$companyID}), srp_erp_taxmaster.isClaimable) AS isClaimable,
                                    customerCountryID,
                                    vatEligible,
                                    customerID,
                                    srp_erp_taxledger.*,
                                    IF(taxCategory = 2 ,outputVatGLAccountAutoID,taxGlAutoID) as outputVatGLAccountAutoID,
                                    outputVatTransferGLAccountAutoID,
                                    transactionAmount 
                                FROM
                                    srp_erp_taxledger
                                    JOIN (
                                        SELECT
                                            SUM( srp_erp_customerinvoicedetails.transactionAmount ) AS transactionAmount,
                                            srp_erp_customerinvoicedetails.invoiceAutoID,
                                            customerID 
                                        FROM
                                            srp_erp_customerinvoicedetails
                                            JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicedetails.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                                        GROUP BY
                                            invoiceAutoID
                                    ) mastertbl ON mastertbl.invoiceAutoID = srp_erp_taxledger.documentMasterAutoID AND srp_erp_taxledger.documentID = 'CINV'
                                    LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = mastertbl.customerID
                                    JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                                    JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID 
                                WHERE
                                    invoiceAutoID = {$item['masID']}")->result_array();
                } else {
                    $ledgerDet = $this->db->query("SELECT
                                    IF(srp_erp_taxmaster.taxCategory = 2, (SELECT vatRegisterYN FROM `srp_erp_company` WHERE company_id = {$companyID}), srp_erp_taxmaster.isClaimable) AS isClaimable,
                                    customerCountryID,
                                    vatEligible,
                                    customerID,
                                    srp_erp_taxledger.*, 
                                    IF(taxCategory = 2 ,outputVatGLAccountAutoID,taxGlAutoID) as outputVatGLAccountAutoID,
                                    outputVatTransferGLAccountAutoID, 
                                    deliveredTransactionAmount AS transactionAmount
                                FROM
                                    srp_erp_taxledger
                                    JOIN (SELECT deliveredTransactionAmount, DOAutoID, customerID FROM srp_erp_deliveryorder) mastertbl ON mastertbl.DOAutoID = srp_erp_taxledger.documentMasterAutoID AND srp_erp_taxledger.documentID = 'DO'
                                    LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = mastertbl.customerID
                                    JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                                    JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                    WHERE
                                        DOAutoID = {$item['masID']}")->result_array();
                }

                    if(!empty($ledgerDet)) {
                        $taxAmount = 0;
                        foreach ($ledgerDet as $val) {
                            $dataleg['documentID'] = 'SLR';
                            $dataleg['documentMasterAutoID'] = $salesReturnAutoID;
                            $dataleg['documentDetailAutoID'] = $last_id;
                            $dataleg['taxDetailAutoID'] = null;
                            $dataleg['taxPercentage'] = 0;
                            $dataleg['ismanuallychanged'] = 0;
                            $dataleg['isClaimable'] = $val['isClaimable'];
                            $dataleg['taxFormulaMasterID'] = $val['taxFormulaMasterID'];
                            $dataleg['taxFormulaDetailID'] = $val['taxFormulaDetailID'];
                            $dataleg['taxMasterID'] = $val['taxMasterID'];
                            $taxCalculateAmount = ($data['salesPrice'] + $item['taxAmount']) * $return_Qty;
                            $dataleg['amount'] = ($val['amount'] / $val['transactionAmount']) * $taxCalculateAmount;
                            $dataleg['formula'] = $val['formula'];
                            $dataleg['taxGlAutoID'] = $val['outputVatGLAccountAutoID'];
                            $dataleg['transferGLAutoID'] = null;
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

                            $taxAmount += ($val['amount'] / $val['transactionAmount']) * $taxCalculateAmount;
                        }

                        $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                        $data_detailTBL['taxAmount'] = $taxAmount;
                        $this->db->where('salesReturnDetailsID', $last_id);
                        $this->db->update('srp_erp_salesreturndetails', $data_detailTBL);
                    }
                }
                /** End (SME-2988)*/

            $taxData = array();
            if ($item['documentID'] == 'CINV') {
                $discountDetails = $this->db->query("SELECT * FROM srp_erp_customerinvoicediscountdetails WHERE invoiceAutoID = {$item['masID']}")->result_array();
                if(!empty($discountDetails)) {
                    $k = 0;
                    foreach ($discountDetails as $discount) {
                        $discount_data[$k]['salesReturnAutoID'] = $salesReturnAutoID;
                        $discount_data[$k]['salesReturnDetailsID'] = $last_id;
                        
                        $discount_data[$k]['invoiceAutoID'] = $discount['invoiceAutoID'];
                        $discount_data[$k]['referenceNo'] = $discount['referenceNo'];
                        $discount_data[$k]['discountMasterAutoID'] = $discount['discountMasterAutoID'];
                        $discount_data[$k]['isChargeToExpense'] = $discount['isChargeToExpense'];
                        $discount_data[$k]['discountDescription'] = $discount['discountDescription'];
                        $discount_data[$k]['discountPercentage'] = $discount['discountPercentage'];
                        $discount_data[$k]['transactionCurrencyID'] = $discount['transactionCurrencyID'];
                        $discount_data[$k]['transactionCurrency'] = $discount['transactionCurrency'];
                        $discount_data[$k]['transactionExchangeRate'] = $discount['transactionExchangeRate'];
                        $discount_data[$k]['transactionCurrencyDecimalPlaces'] = $discount['transactionCurrencyDecimalPlaces'];
                        $discount_data[$k]['transactionAmount'] = $discount['transactionAmount'];
                        $discount_data[$k]['customerCurrencyID'] = $discount['customerCurrencyID'];
                        $discount_data[$k]['customerCurrency'] = $discount['customerCurrency'];
                        $discount_data[$k]['customerCurrencyExchangeRate'] = $discount['customerCurrencyExchangeRate'];
                        $discount_data[$k]['customerCurrencyAmount'] = $discount['customerCurrencyAmount'];
                        $discount_data[$k]['customerCurrencyDecimalPlaces'] = $discount['customerCurrencyDecimalPlaces'];
                        $discount_data[$k]['companyLocalCurrencyID'] = $discount['companyLocalCurrencyID'];
                        $discount_data[$k]['companyLocalCurrency'] = $discount['companyLocalCurrency'];
                        $discount_data[$k]['companyLocalExchangeRate'] = $discount['companyLocalExchangeRate'];
                        $discount_data[$k]['companyLocalAmount'] = $discount['companyLocalAmount'];
                        $discount_data[$k]['companyReportingCurrencyID'] = $discount['companyReportingCurrencyID'];
                        $discount_data[$k]['companyReportingCurrency'] = $discount['companyReportingCurrency'];
                        $discount_data[$k]['companyReportingExchangeRate'] = $discount['companyReportingExchangeRate'];
                        $discount_data[$k]['companyReportingAmount'] = $discount['companyReportingAmount'];
                        $discount_data[$k]['GLAutoID'] = $discount['GLAutoID'];
                        $discount_data[$k]['systemGLCode'] = $discount['systemGLCode'];
                        $discount_data[$k]['GLCode'] = $discount['GLCode'];
                        $discount_data[$k]['GLDescription'] = $discount['GLDescription'];
                        $discount_data[$k]['GLType'] = $discount['GLType'];
                        $discount_data[$k]['segmentID'] = $discount['segmentID'];
                        $discount_data[$k]['segmentCode'] = $discount['segmentCode'];
                        $discount_data[$k]['companyID'] = $discount['companyID'];
                        $discount_data[$k]['companyCode'] = $discount['companyCode'];
                        $discount_data[$k]['createdUserGroup'] = $discount['createdUserGroup'];
                        $discount_data[$k]['createdPCID'] = $discount['createdPCID'];
                        $discount_data[$k]['createdUserID'] = $discount['createdUserID'];
                        $discount_data[$k]['createdDateTime'] = $discount['createdDateTime'];
                        $discount_data[$k]['createdUserName'] = $discount['createdUserName'];
                        $k++;
                    }
                    $this->db->insert_batch('srp_erp_salesreturndiscountdetails', $discount_data);
                }

                $taxData = $this->db->query("SELECT * FROM srp_erp_customerinvoicetaxdetails WHERE invoiceAutoID = {$item['masID']}")->result_array();
            } else {
                $taxData = $this->db->query("SELECT *, DOAutoID as invoiceAutoID FROM srp_erp_deliveryordertaxdetails WHERE DOAutoID = {$item['masID']}")->result_array();
            }
            if(!empty($taxData)) {
                $j = 0;
                foreach ($taxData as $tax) {
                    $tax_data[$j]['salesReturnAutoID'] = $salesReturnAutoID;
                    $tax_data[$j]['salesReturnDetailsID'] = $last_id;

                    $tax_data[$j]['invoiceAutoID'] = $tax['invoiceAutoID'];
                    $tax_data[$j]['referenceNo'] = $tax['referenceNo'];
                    $tax_data[$j]['taxMasterAutoID'] = $tax['taxMasterAutoID'];
                    $tax_data[$j]['taxDescription'] = $tax['taxDescription'];
                    $tax_data[$j]['taxShortCode'] = $tax['taxShortCode'];
                    $tax_data[$j]['taxPercentage'] = $tax['taxPercentage'];
                    $tax_data[$j]['supplierAutoID'] = $tax['supplierAutoID'];
                    $tax_data[$j]['supplierSystemCode'] = $tax['supplierSystemCode'];
                    $tax_data[$j]['supplierName'] = $tax['supplierName'];
                    $tax_data[$j]['supplierAutoID'] = $tax['supplierAutoID'];
                    $tax_data[$j]['transactionCurrencyID'] = $tax['transactionCurrencyID'];
                    $tax_data[$j]['transactionCurrency'] = $tax['transactionCurrency'];
                    $tax_data[$j]['transactionExchangeRate'] = $tax['transactionExchangeRate'];
                    $tax_data[$j]['transactionCurrencyDecimalPlaces'] = $tax['transactionCurrencyDecimalPlaces'];
                    $tax_data[$j]['transactionAmount'] = $tax['transactionAmount'];
                    $tax_data[$j]['supplierCurrencyID'] = $tax['supplierCurrencyID'];
                    $tax_data[$j]['supplierCurrency'] = $tax['supplierCurrency'];
                    $tax_data[$j]['supplierCurrencyExchangeRate'] = $tax['supplierCurrencyExchangeRate'];
                    $tax_data[$j]['supplierCurrencyAmount'] = $tax['supplierCurrencyAmount'];
                    $tax_data[$j]['supplierCurrencyDecimalPlaces'] = $tax['supplierCurrencyDecimalPlaces'];
                    $tax_data[$j]['companyLocalCurrencyID'] = $tax['companyLocalCurrencyID'];
                    $tax_data[$j]['companyLocalCurrency'] = $tax['companyLocalCurrency'];
                    $tax_data[$j]['companyLocalExchangeRate'] = $tax['companyLocalExchangeRate'];
                    $tax_data[$j]['companyLocalAmount'] = $tax['companyLocalAmount'];
                    $tax_data[$j]['companyReportingCurrencyID'] = $tax['companyReportingCurrencyID'];
                    $tax_data[$j]['companyReportingCurrency'] = $tax['companyReportingCurrency'];
                    $tax_data[$j]['companyReportingExchangeRate'] = $tax['companyReportingExchangeRate'];
                    $tax_data[$j]['companyReportingAmount'] = $tax['companyReportingAmount'];
                    $tax_data[$j]['GLAutoID'] = $tax['GLAutoID'];
                    $tax_data[$j]['systemGLCode'] = $tax['systemGLCode'];
                    $tax_data[$j]['GLCode'] = $tax['GLCode'];
                    $tax_data[$j]['GLDescription'] = $tax['GLDescription'];
                    $tax_data[$j]['GLType'] = $tax['GLType'];
                    $tax_data[$j]['segmentID'] = $tax['segmentID'];
                    $tax_data[$j]['segmentCode'] = $tax['segmentCode'];
                    $tax_data[$j]['companyID'] = $tax['companyID'];
                    $tax_data[$j]['companyCode'] = $tax['companyCode'];
                    $tax_data[$j]['createdUserGroup'] = $tax['createdUserGroup'];
                    $tax_data[$j]['createdPCID'] = $tax['createdPCID'];
                    $tax_data[$j]['createdUserID'] = $tax['createdUserID'];
                    $tax_data[$j]['createdDateTime'] = $tax['createdDateTime'];
                    $tax_data[$j]['createdUserName'] = $tax['createdUserName'];
                    $j++;
                }
                $this->db->insert_batch('srp_erp_salesreturntaxdetails', $tax_data);
            }    
            $i++;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Good Received note : Details Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Details Saved Successfully.');
        }
    }

    function delete_sales_return_detail()
    {
        $id = $this->input->post('salesReturnDetailsID');
        /** update sub item master */
        /*$this->db->select('*');
        $this->db->from('srp_erp_stockreturndetails');
        $this->db->where('stockReturnDetailsID', $id);
        $rTmp = $this->db->get()->row_array();


        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $rTmp['stockReturnAutoID']);
        $this->db->where('soldDocumentDetailID', $rTmp['stockReturnDetailsID']);
        $this->db->where('soldDocumentID', 'SR');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);*/
        /** end update sub item master */

        $salesReturnAutoID = $this->db->query("SELECT salesReturnAutoID FROM srp_erp_salesreturndetails WHERE salesReturnDetailsID = $id")->row('salesReturnAutoID');
        $this->db->delete('srp_erp_taxledger', array('documentID' => 'SLR','documentMasterAutoID' => $salesReturnAutoID,'documentDetailAutoID' => trim($id)));
       
        $this->db->delete('srp_erp_salesreturndetails', array('salesReturnDetailsID' => $id));
        $this->db->delete('srp_erp_salesreturntaxdetails', array('salesReturnDetailsID' => $id));
        return true;
    }

    function sales_return_confirmation()
    {
        $companyID = current_companyID();
        $currentuser = current_userID();
        $this->db->select('salesReturnDetailsID');
        $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID') ?? ''));
        $this->db->from('srp_erp_salesreturndetails');
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('salesReturnAutoID');
            $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_salesreturnmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $masterID = trim($this->input->post('salesReturnAutoID') ?? '');


/* 
                $salesReturnDetail = $this->db->query("select
                GROUP_CONCAT(itemAutoID) as itemAutoID
                from 
                srp_erp_salesreturndetails
                where 
                companyID = $companyID 
                AND salesReturnAutoID = $masterID")->row("itemAutoID");

                if(!empty($salesReturnDetail)){ 
                    
                    $wacTransactionAmountValidation  = fetch_itemledger_transactionAmount_validation("$salesReturnDetail");
                    if(!empty($wacTransactionAmountValidation)){ 
                    return array('error' => 4, 'message' => $wacTransactionAmountValidation);
                    exit();
                    }

                } */





                $this->db->select('salesReturnCode,companyFinanceYearID,DATE_FORMAT(returnDate, "%Y") as invYear,DATE_FORMAT(returnDate, "%m") as invMonth');
                $this->db->where('salesReturnAutoID', $masterID);
                $this->db->from('srp_erp_salesreturnmaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');
                $lenth = strlen($master_dt['salesReturnCode']);

                if ($lenth == 1) {
                    if ($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location == '')) {
                            return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                        } else {
                            if ($locationemployee != '') {
                                $codegerator = $this->sequence->sequence_generator_location('SLR', $master_dt['companyFinanceYearID'], $locationemployee, $master_dt['invYear'], $master_dt['invMonth']);
                            } else {
                                return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                            }
                        }
                    } else {
                        $codegerator = $this->sequence->sequence_generator_fin('SLR', $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }

                    $validate_code = validate_code_duplication($codegerator, 'salesReturnCode', $masterID,'salesReturnAutoID', 'srp_erp_salesreturnmaster');
                    if(!empty($validate_code)) {
                        return array('error' => 1, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                    }

                    $invcod = array(
                        'salesReturnCode' => $codegerator,
                    );
                    $this->db->where('salesReturnAutoID', $masterID);
                    $this->db->update('srp_erp_salesreturnmaster', $invcod);
                } else {
                    $validate_code = validate_code_duplication($master_dt['salesReturnCode'], 'salesReturnCode', $masterID,'salesReturnAutoID', 'srp_erp_salesreturnmaster');
                    if(!empty($validate_code)) {
                        return array('error' => 1, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                }

                $this->load->library('Approvals');
                $this->db->select('salesReturnAutoID, salesReturnCode,returnDate');
                $this->db->where('salesReturnAutoID', $masterID);
                $this->db->from('srp_erp_salesreturnmaster');
                $app_data = $this->db->get()->row_array();

                /** item Master Sub check */
                /*$documentDetailID = trim($this->input->post('stockReturnAutoID') ?? '');
                $validate = $this->validate_itemMasterSub($documentDetailID, 'SLR');*/
                /** end of item master sub */

                /*if ($validate) {*/


                /*} else {
                    return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                }*/
                $autoApproval = get_document_auto_approval('SLR');
                if ($autoApproval == 0) {
                    $approvals_status = $this->approvals->auto_approve($app_data['salesReturnAutoID'], 'srp_erp_salesreturnmaster', 'salesReturnAutoID', 'SLR', $app_data['salesReturnCode'], $app_data['returnDate']);
                } elseif ($autoApproval == 1) {
                    $approvals_status = $this->approvals->CreateApproval('SLR', $app_data['salesReturnAutoID'], $app_data['salesReturnCode'], 'Sales Return', 'srp_erp_salesreturnmaster', 'salesReturnAutoID', 0, $app_data['returnDate']);
                } else {
                    return array('error' => 1, 'message' => 'Approval levels are not set for this document');
                }

                if ($approvals_status == 1) {
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']
                    );

                    $this->db->where('salesReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
                    $this->db->update('srp_erp_salesreturnmaster', $data);

                    $autoApproval = get_document_auto_approval('SLR');

                    if ($autoApproval == 0) {
                        $result = $this->save_sales_return_approval(0, $app_data['salesReturnAutoID'], 1, 'Auto Approved');
                        if ($result) {
                            return array('error' => 0, 'message' => 'document successfully confirmed');
                        }
                    } else {
                        return array('error' => 0, 'message' => 'document successfully confirmed');
                    }


                } else {
                    return array('error' => 1, 'message' => 'Approval setting are not configured!, please contact your system team.');
                }
            }
        }
        //return array('status' => true);
    }

    function delete_sales_return()
    {
        $masterID = trim($this->input->post('salesReturnAutoID') ?? '');
        /* $this->db->delete('srp_erp_salesreturnmaster', array('salesReturnAutoID' => trim($this->input->post('salesReturnAutoID') ?? '')));
         $this->db->delete('srp_erp_salesreturndetails', array('salesReturnAutoID' => trim($this->input->post('salesReturnAutoID') ?? '')));*/
        $this->db->select('*');
        $this->db->from('srp_erp_salesreturndetails');
        $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID') ?? ''));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            /*$data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID') ?? ''));
            $this->db->update('srp_erp_salesreturnmaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;*/

            /* Added*/
            $documentCode = $this->db->get_where('srp_erp_salesreturnmaster', ['salesReturnAutoID'=> $masterID])->row('salesReturnCode');
            $this->db->trans_start();

            $length = strlen($documentCode);
            if($length > 1){
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID') ?? ''));
                $this->db->update('srp_erp_salesreturnmaster', $data);
            }
            else{
                $this->db->where('salesReturnAutoID', $masterID)->delete('srp_erp_salesreturndetails');
                $this->db->where('salesReturnAutoID', $masterID)->delete('srp_erp_salesreturnmaster');
            }

            $this->db->trans_complete();
            if($this->db->trans_status() == true){
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }else{
                $this->session->set_flashdata('e', 'Error in delete process.');

                return false;
            }
            /* End */
        }
    }


    /**
     * @param $oldStock : item master stock
     * @param $WACAmount : item master WAC  old
     * @param $qty : item master Qty
     * @param $cost : sales return unit cost
     * @param int $decimal : decimal point
     * @return float
     */
    function calculateNewWAC_salesReturn($oldStock, $WACAmount, $qty, $cost, $decimal = 2)
    {
        $newStock = $oldStock + $qty;
        $newWACAmount = round(((($oldStock * $WACAmount) + ($cost * $qty)) / $newStock), $decimal);
        return $newWACAmount;
    }

    function save_sales_return_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_id = trim($this->input->post('salesReturnAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['salesReturnAutoID'] = $system_id;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'SLR');
        }

        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->where('salesReturnAutoID', $system_id);
            $this->db->from('srp_erp_salesreturnmaster');
            $master = $this->db->get()->row_array();

            /* $this->db->select('*');
             $this->db->where('salesReturnAutoID', $system_id);
             $this->db->from('srp_erp_salesreturndetails');*/
            $qry = "SELECT *,SUM(return_Qty) as return_Qty FROM srp_erp_salesreturndetails WHERE salesReturnAutoID = $system_id GROUP BY itemAutoID,unitOfMeasureID";
            $detailTbl = $this->db->query($qry)->result_array();


            $this->db->trans_start();
            /**setup data for item master & item ledger */
            $i = 0;
            foreach ($detailTbl as $invDetail) {

                $itemAutoID = $invDetail['itemAutoID'];
                $decimal = $master['companyLocalCurrencyDecimalPlaces'];
                $item = fetch_item_data($itemAutoID);

                $wareHouseAutoID = $master['wareHouseAutoID'];
                $qty = $invDetail['return_Qty'] / $invDetail['conversionRateUOM'];
                $newStock = $item['currentStock'] + $qty;

                $this->db->select('*');
                $this->db->from('srp_erp_warehouseitems');
                $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
                $this->db->where('itemAutoID', $invDetail['itemAutoID']);
                $warehouseItem = $this->db->get()->row_array();
                $newStock_warehouse = $warehouseItem['currentStock'] + $qty;

                /** update warehouse stock */
                //$this->db->query("UPDATE srp_erp_warehouseitems SET currentStock =  '{$newStock}'  WHERE wareHouseAutoID='{$wareHouseAutoID}' AND itemAutoID='{$itemAutoID}'");

                /** WAC Calculation  */
//                $companyLocalWacAmount = $this->calculateNewWAC_salesReturn($item['currentStock'], $item['companyLocalWacAmount'], $invDetail['return_Qty'], $invDetail['salesPrice'], $decimal);
//                $companyReportingWacAmount = $this->calculateNewWAC_salesReturn($item['currentStock'], $item['companyReportingWacAmount'], $invDetail['return_Qty'], $invDetail['salesPrice'], $decimal);

                $companyLocalWacAmount = $this->calculateNewWAC_salesReturn($item['currentStock'], $item['companyLocalWacAmount'], $invDetail['return_Qty'], $invDetail['currentWacAmount'], wacDecimalPlaces);
                $reportingCurrentWAC = $invDetail['currentWacAmount'] / $master['companyReportingExchangeRate'];
                $companyReportingWacAmount = $this->calculateNewWAC_salesReturn($item['currentStock'], $item['companyReportingWacAmount'], $invDetail['return_Qty'], $reportingCurrentWAC, wacDecimalPlaces);

                /** warehouse item update data */
                $warehouseItemData[$i]['warehouseItemsAutoID'] = $warehouseItem['warehouseItemsAutoID'];
                $warehouseItemData[$i]['currentStock'] = $newStock_warehouse;

                /** Item master update data */
                $itemMaster[$i]['itemAutoID'] = $itemAutoID;
                $itemMaster[$i]['currentStock'] = $newStock;
                $itemMaster[$i]['companyLocalWacAmount'] = $companyLocalWacAmount;
                $itemMaster[$i]['companyReportingWacAmount'] = $companyReportingWacAmount;

                /** setup Item Ledger Data  */
                $itemLedgerData[$i]['documentID'] = $master['documentID'];
                $itemLedgerData[$i]['documentCode'] = $master['documentID'];
                $itemLedgerData[$i]['documentAutoID'] = $master['salesReturnAutoID'];
                $itemLedgerData[$i]['documentSystemCode'] = $master['salesReturnCode'];
                $itemLedgerData[$i]['documentDate'] = $master['returnDate'];
                $itemLedgerData[$i]['referenceNumber'] = $master['referenceNo'];
                $itemLedgerData[$i]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                $itemLedgerData[$i]['companyFinanceYear'] = $master['companyFinanceYear'];
                $itemLedgerData[$i]['FYBegin'] = $master['FYBegin'];
                $itemLedgerData[$i]['FYEnd'] = $master['FYEnd'];
                $itemLedgerData[$i]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                $itemLedgerData[$i]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                $itemLedgerData[$i]['wareHouseAutoID'] = $master['wareHouseAutoID'];
                $itemLedgerData[$i]['wareHouseCode'] = $master['wareHouseCode'];
                $itemLedgerData[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                $itemLedgerData[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                $itemLedgerData[$i]['itemAutoID'] = $itemAutoID;
                $itemLedgerData[$i]['itemSystemCode'] = $invDetail['itemSystemCode'];
                $itemLedgerData[$i]['itemDescription'] = $invDetail['itemDescription'];
                $itemLedgerData[$i]['defaultUOMID'] = $invDetail['defaultUOMID'];
                $itemLedgerData[$i]['defaultUOM'] = $invDetail['defaultUOM'];
                $itemLedgerData[$i]['transactionUOMID'] = $invDetail['unitOfMeasureID'];
                $itemLedgerData[$i]['transactionUOM'] = $invDetail['unitOfMeasure'];
                $itemLedgerData[$i]['transactionQTY'] = $invDetail['return_Qty'];
                $itemLedgerData[$i]['convertionRate'] = $invDetail['conversionRateUOM'];
                $itemLedgerData[$i]['currentStock'] = $newStock;
                $itemLedgerData[$i]['PLGLAutoID'] = $item['costGLAutoID'];
                $itemLedgerData[$i]['PLSystemGLCode'] = $item['costSystemGLCode'];
                $itemLedgerData[$i]['PLGLCode'] = $item['costGLCode'];
                $itemLedgerData[$i]['PLDescription'] = $item['costDescription'];
                $itemLedgerData[$i]['PLType'] = $item['costType'];
                $itemLedgerData[$i]['BLGLAutoID'] = $item['assteGLAutoID'];
                $itemLedgerData[$i]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                $itemLedgerData[$i]['BLGLCode'] = $item['assteGLCode'];
                $itemLedgerData[$i]['BLDescription'] = $item['assteDescription'];
                $itemLedgerData[$i]['BLType'] = $item['assteType'];
                $itemLedgerData[$i]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                $ex_rate_wac = (1 / $master['companyLocalExchangeRate']);
                $itemLedgerData[$i]['transactionAmount'] = round((($invDetail['currentWacAmount'] / $ex_rate_wac) * ($itemLedgerData[$i]['transactionQTY'] / $invDetail['conversionRateUOM'])), $itemLedgerData[$i]['transactionCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['salesPrice'] = $invDetail["salesPrice"];
                $itemLedgerData[$i]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                $itemLedgerData[$i]['transactionCurrency'] = $master['transactionCurrency'];
                $itemLedgerData[$i]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                $itemLedgerData[$i]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                $itemLedgerData[$i]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                $itemLedgerData[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $itemLedgerData[$i]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['companyLocalAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['companyLocalExchangeRate']), $itemLedgerData[$i]['companyLocalCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                $itemLedgerData[$i]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                $itemLedgerData[$i]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                $itemLedgerData[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $itemLedgerData[$i]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['companyReportingAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['companyReportingExchangeRate']), $itemLedgerData[$i]['companyReportingCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                $itemLedgerData[$i]['partyCurrencyID'] = $master['customerCurrencyID'];
                $itemLedgerData[$i]['partyCurrency'] = $master['customerCurrency'];
                $itemLedgerData[$i]['partyCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $itemLedgerData[$i]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['partyCurrencyAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['partyCurrencyExchangeRate']), $itemLedgerData[$i]['partyCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['confirmedYN'] = $master['confirmedYN'];
                $itemLedgerData[$i]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                $itemLedgerData[$i]['confirmedByName'] = $master['confirmedByName'];
                $itemLedgerData[$i]['confirmedDate'] = $master['confirmedDate'];
                $itemLedgerData[$i]['approvedYN'] = $master['approvedYN'];
                $itemLedgerData[$i]['approvedDate'] = $master['approvedDate'];
                $itemLedgerData[$i]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                $itemLedgerData[$i]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                $itemLedgerData[$i]['segmentID'] = $invDetail['segmentID'];
                $itemLedgerData[$i]['segmentCode'] = $invDetail['segmentCode'];
                $itemLedgerData[$i]['companyID'] = $master['companyID'];
                $itemLedgerData[$i]['companyCode'] = $master['companyCode'];
                $itemLedgerData[$i]['createdUserGroup'] = $master['createdUserGroup'];
                $itemLedgerData[$i]['createdPCID'] = $master['createdPCID'];
                $itemLedgerData[$i]['createdUserID'] = $master['createdUserID'];
                $itemLedgerData[$i]['createdDateTime'] = $master['createdDateTime'];
                $itemLedgerData[$i]['createdUserName'] = $master['createdUserName'];
                $i++;
            }


            /** updating Item master new stock */
            if (!empty($itemMaster)) {
                $this->db->update_batch('srp_erp_itemmaster', $itemMaster, 'itemAutoID');
            }

            /** updating warehouse Item new stock */
            if (!empty($warehouseItemData)) {
                $this->db->update_batch('srp_erp_warehouseitems', $warehouseItemData, 'warehouseItemsAutoID');
            }

            /** updating Item Ledger */
            if (!empty($itemLedgerData)) {
                $this->db->insert_batch('srp_erp_itemledger', $itemLedgerData);
            }


            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_sales_return_data($system_id, 'SLR');


            /**setup data for general Ledger  */
            $i = 0;

            foreach ($double_entry['GLEntries'] as $doubleEntry) {
                $generalLedgerData[$i]['documentMasterAutoID'] = $master['salesReturnAutoID'];
                $generalLedgerData[$i]['documentCode'] = $master['documentID'];
                $generalLedgerData[$i]['documentSystemCode'] = $master['salesReturnCode'];
                $generalLedgerData[$i]['documentDate'] = $master['returnDate'];
                $generalLedgerData[$i]['documentType'] = '';
                $generalLedgerData[$i]['documentYear'] = date("Y", strtotime($master['returnDate']));;
                $generalLedgerData[$i]['documentMonth'] = date("m", strtotime($master['returnDate']));
                $generalLedgerData[$i]['documentNarration'] = $master['comment'];
                $generalLedgerData[$i]['chequeNumber'] = '';
                $generalLedgerData[$i]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                $generalLedgerData[$i]['transactionCurrency'] = $master['transactionCurrency'];
                $generalLedgerData[$i]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                $generalLedgerData[$i]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                $generalLedgerData[$i]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                $generalLedgerData[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $generalLedgerData[$i]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                $generalLedgerData[$i]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                $generalLedgerData[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $generalLedgerData[$i]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['partyContractID'] = '';
                $generalLedgerData[$i]['partyType'] = 'CUS';
                $generalLedgerData[$i]['partyAutoID'] = $master['customerID'];
                $generalLedgerData[$i]['partySystemCode'] = $master['customerSystemCode'];
                $generalLedgerData[$i]['partyName'] = $master['customerName'];
                $generalLedgerData[$i]['partyCurrencyID'] = $master['customerCurrencyID'];
                $generalLedgerData[$i]['partyCurrency'] = $master['customerCurrency'];
                $generalLedgerData[$i]['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $generalLedgerData[$i]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                $generalLedgerData[$i]['confirmedByName'] = $master['confirmedByName'];
                $generalLedgerData[$i]['confirmedDate'] = $master['confirmedDate'];
                $generalLedgerData[$i]['approvedDate'] = $master['approvedDate'];
                $generalLedgerData[$i]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                $generalLedgerData[$i]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                $generalLedgerData[$i]['companyID'] = $master['companyID'];
                $generalLedgerData[$i]['companyCode'] = $master['companyCode'];
                $amount = $doubleEntry['debit'];
                if ($doubleEntry['amountType'] == 'cr') {
                    $amount = ($doubleEntry['credit'] * -1);
                }

                $transactionAmount = $doubleEntry['transactionAmount'];

                $generalLedgerData[$i]['transactionAmount'] = round($transactionAmount, $doubleEntry['transactionDecimal']);
                $generalLedgerData[$i]['companyLocalAmount'] = round(($transactionAmount / $generalLedgerData[$i]['companyLocalExchangeRate']), $generalLedgerData[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['companyReportingAmount'] = round(($transactionAmount / $generalLedgerData[$i]['companyReportingExchangeRate']), $generalLedgerData[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['partyCurrencyAmount'] = round(($transactionAmount / $generalLedgerData[$i]['partyExchangeRate']), $generalLedgerData[$i]['partyCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['amount_type'] = $doubleEntry['amountType'];
                $generalLedgerData[$i]['documentDetailAutoID'] = $doubleEntry['auto_id'];
                $generalLedgerData[$i]['GLAutoID'] = $doubleEntry['GLAutoID'];
                $generalLedgerData[$i]['systemGLCode'] = $doubleEntry['SystemGLCode'];
                $generalLedgerData[$i]['GLCode'] = $doubleEntry['GLSecondaryCode'];
                $generalLedgerData[$i]['GLDescription'] = $doubleEntry['GLDescription'];
                $generalLedgerData[$i]['GLType'] = $doubleEntry['GLType'];
                $generalLedgerData[$i]['segmentID'] = $doubleEntry['segmentID'];
                $generalLedgerData[$i]['segmentCode'] = $doubleEntry['segmentCode'];
                $generalLedgerData[$i]['subLedgerType'] = $doubleEntry['subLedgerType'];
                $generalLedgerData[$i]['subLedgerDesc'] = $doubleEntry['subLedgerDesc'];
                $generalLedgerData[$i]['isAddon'] = 0;
                $generalLedgerData[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $generalLedgerData[$i]['createdPCID'] = $this->common_data['current_pc'];
                $generalLedgerData[$i]['createdUserID'] = $this->common_data['current_userID'];
                $generalLedgerData[$i]['createdDateTime'] = $this->common_data['current_date'];
                $generalLedgerData[$i]['createdUserName'] = $this->common_data['current_user'];
                $i++;
            }


            if (!empty($generalLedgerData)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalLedgerData);
            }


            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];

            $this->db->where('salesReturnAutoID', $system_id);
            $this->db->update('srp_erp_salesreturnmaster', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            //return true;
            //$this->session->set_flashdata('s', 'Document approved successfully.');
            return array('error' => 1, 'An error has occurred!');
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 'Document approved successfully.');
            //return true;
        }
    }

    function save_sales_return_approval_buyback($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_id = trim($this->input->post('salesReturnAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['salesReturnAutoID'] = $system_id;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'SLR');
        }

        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->where('salesReturnAutoID', $system_id);
            $this->db->from('srp_erp_salesreturnmaster');
            $master = $this->db->get()->row_array();

            /* $this->db->select('*');
             $this->db->where('salesReturnAutoID', $system_id);
             $this->db->from('srp_erp_salesreturndetails');*/
            $qry = "SELECT *,SUM(return_Qty) as return_Qty FROM srp_erp_salesreturndetails WHERE salesReturnAutoID = $system_id GROUP BY itemAutoID,unitOfMeasureID";
            $detailTbl = $this->db->query($qry)->result_array();


            $this->db->trans_start();
            /**setup data for item master & item ledger */
            $i = 0;
            foreach ($detailTbl as $invDetail) {

                $itemAutoID = $invDetail['itemAutoID'];
                $decimal = $master['companyLocalCurrencyDecimalPlaces'];
                $item = fetch_item_data($itemAutoID);

                $wareHouseAutoID = $master['wareHouseAutoID'];
                $qty = $invDetail['return_Qty'] / $invDetail['conversionRateUOM'];
                $newStock = $item['currentStock'] + $qty;

                $this->db->select('*');
                $this->db->from('srp_erp_warehouseitems');
                $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
                $this->db->where('itemAutoID', $invDetail['itemAutoID']);
                $warehouseItem = $this->db->get()->row_array();
                $newStock_warehouse = $warehouseItem['currentStock'] + $qty;

                /** update warehouse stock */
                //$this->db->query("UPDATE srp_erp_warehouseitems SET currentStock =  '{$newStock}'  WHERE wareHouseAutoID='{$wareHouseAutoID}' AND itemAutoID='{$itemAutoID}'");

                /** WAC Calculation  */
                $companyLocalWacAmount = $this->calculateNewWAC_salesReturn($item['currentStock'], $item['companyLocalWacAmount'], $invDetail['return_Qty'], $invDetail['salesPrice'], $decimal);
                $companyReportingWacAmount = $this->calculateNewWAC_salesReturn($item['currentStock'], $item['companyReportingWacAmount'], $invDetail['return_Qty'], $invDetail['salesPrice'], $decimal);

                /** warehouse item update data */
                $warehouseItemData[$i]['warehouseItemsAutoID'] = $warehouseItem['warehouseItemsAutoID'];
                $warehouseItemData[$i]['currentStock'] = $newStock_warehouse;

                /** Item master update data */
                $itemMaster[$i]['itemAutoID'] = $itemAutoID;
                $itemMaster[$i]['currentStock'] = $newStock;
                $itemMaster[$i]['companyLocalWacAmount'] = $companyLocalWacAmount;
                $itemMaster[$i]['companyReportingWacAmount'] = $companyReportingWacAmount;

                /** setup Item Ledger Data  */
                $itemLedgerData[$i]['documentID'] = $master['documentID'];
                $itemLedgerData[$i]['documentCode'] = $master['documentID'];
                $itemLedgerData[$i]['documentAutoID'] = $master['salesReturnAutoID'];
                $itemLedgerData[$i]['documentSystemCode'] = $master['salesReturnCode'];
                $itemLedgerData[$i]['documentDate'] = $master['returnDate'];
                $itemLedgerData[$i]['referenceNumber'] = $master['referenceNo'];
                $itemLedgerData[$i]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                $itemLedgerData[$i]['companyFinanceYear'] = $master['companyFinanceYear'];
                $itemLedgerData[$i]['FYBegin'] = $master['FYBegin'];
                $itemLedgerData[$i]['FYEnd'] = $master['FYEnd'];
                $itemLedgerData[$i]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                $itemLedgerData[$i]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                $itemLedgerData[$i]['wareHouseAutoID'] = $master['wareHouseAutoID'];
                $itemLedgerData[$i]['wareHouseCode'] = $master['wareHouseCode'];
                $itemLedgerData[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                $itemLedgerData[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                $itemLedgerData[$i]['itemAutoID'] = $itemAutoID;
                $itemLedgerData[$i]['itemSystemCode'] = $invDetail['itemSystemCode'];
                $itemLedgerData[$i]['itemDescription'] = $invDetail['itemDescription'];
                $itemLedgerData[$i]['defaultUOMID'] = $invDetail['defaultUOMID'];
                $itemLedgerData[$i]['defaultUOM'] = $invDetail['defaultUOM'];
                $itemLedgerData[$i]['transactionUOMID'] = $invDetail['unitOfMeasureID'];
                $itemLedgerData[$i]['transactionUOM'] = $invDetail['unitOfMeasure'];
                $itemLedgerData[$i]['transactionQTY'] = $invDetail['return_Qty'];
                $itemLedgerData[$i]['convertionRate'] = $invDetail['conversionRateUOM'];
                $itemLedgerData[$i]['currentStock'] = $newStock;
                $itemLedgerData[$i]['PLGLAutoID'] = $item['costGLAutoID'];
                $itemLedgerData[$i]['PLSystemGLCode'] = $item['costSystemGLCode'];
                $itemLedgerData[$i]['PLGLCode'] = $item['costGLCode'];
                $itemLedgerData[$i]['PLDescription'] = $item['costDescription'];
                $itemLedgerData[$i]['PLType'] = $item['costType'];
                $itemLedgerData[$i]['BLGLAutoID'] = $item['assteGLAutoID'];
                $itemLedgerData[$i]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                $itemLedgerData[$i]['BLGLCode'] = $item['assteGLCode'];
                $itemLedgerData[$i]['BLDescription'] = $item['assteDescription'];
                $itemLedgerData[$i]['BLType'] = $item['assteType'];
                $itemLedgerData[$i]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                $ex_rate_wac = (1 / $master['companyLocalExchangeRate']);
                $itemLedgerData[$i]['transactionAmount'] = round((($invDetail['currentWacAmount'] / $ex_rate_wac) * ($itemLedgerData[$i]['transactionQTY'] / $invDetail['conversionRateUOM'])), $itemLedgerData[$i]['transactionCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['salesPrice'] = $invDetail["salesPrice"];
                $itemLedgerData[$i]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                $itemLedgerData[$i]['transactionCurrency'] = $master['transactionCurrency'];
                $itemLedgerData[$i]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                $itemLedgerData[$i]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                $itemLedgerData[$i]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                $itemLedgerData[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $itemLedgerData[$i]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['companyLocalAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['companyLocalExchangeRate']), $itemLedgerData[$i]['companyLocalCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                $itemLedgerData[$i]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                $itemLedgerData[$i]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                $itemLedgerData[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $itemLedgerData[$i]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['companyReportingAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['companyReportingExchangeRate']), $itemLedgerData[$i]['companyReportingCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                $itemLedgerData[$i]['partyCurrencyID'] = $master['customerCurrencyID'];
                $itemLedgerData[$i]['partyCurrency'] = $master['customerCurrency'];
                $itemLedgerData[$i]['partyCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $itemLedgerData[$i]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['partyCurrencyAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['partyCurrencyExchangeRate']), $itemLedgerData[$i]['partyCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['confirmedYN'] = $master['confirmedYN'];
                $itemLedgerData[$i]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                $itemLedgerData[$i]['confirmedByName'] = $master['confirmedByName'];
                $itemLedgerData[$i]['confirmedDate'] = $master['confirmedDate'];
                $itemLedgerData[$i]['approvedYN'] = $master['approvedYN'];
                $itemLedgerData[$i]['approvedDate'] = $master['approvedDate'];
                $itemLedgerData[$i]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                $itemLedgerData[$i]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                $itemLedgerData[$i]['segmentID'] = $invDetail['segmentID'];
                $itemLedgerData[$i]['segmentCode'] = $invDetail['segmentCode'];
                $itemLedgerData[$i]['companyID'] = $master['companyID'];
                $itemLedgerData[$i]['companyCode'] = $master['companyCode'];
                $itemLedgerData[$i]['createdUserGroup'] = $master['createdUserGroup'];
                $itemLedgerData[$i]['createdPCID'] = $master['createdPCID'];
                $itemLedgerData[$i]['createdUserID'] = $master['createdUserID'];
                $itemLedgerData[$i]['createdDateTime'] = $master['createdDateTime'];
                $itemLedgerData[$i]['createdUserName'] = $master['createdUserName'];
                $i++;
            }


            /** updating Item master new stock */
            if (!empty($itemMaster)) {
                $this->db->update_batch('srp_erp_itemmaster', $itemMaster, 'itemAutoID');
            }

            /** updating warehouse Item new stock */
            if (!empty($warehouseItemData)) {
                $this->db->update_batch('srp_erp_warehouseitems', $warehouseItemData, 'warehouseItemsAutoID');
            }

            /** updating Item Ledger */
            if (!empty($itemLedgerData)) {
                $this->db->insert_batch('srp_erp_itemledger', $itemLedgerData);
            }


            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_sales_return_buyback_data($system_id, 'SLR');


            /**setup data for general Ledger  */
            $i = 0;

            foreach ($double_entry['GLEntries'] as $doubleEntry) {
                $generalLedgerData[$i]['documentMasterAutoID'] = $master['salesReturnAutoID'];
                $generalLedgerData[$i]['documentCode'] = $master['documentID'];
                $generalLedgerData[$i]['documentSystemCode'] = $master['salesReturnCode'];
                $generalLedgerData[$i]['documentDate'] = $master['returnDate'];
                $generalLedgerData[$i]['documentType'] = '';
                $generalLedgerData[$i]['documentYear'] = date("Y", strtotime($master['returnDate']));;
                $generalLedgerData[$i]['documentMonth'] = date("m", strtotime($master['returnDate']));
                $generalLedgerData[$i]['documentNarration'] = $master['comment'];
                $generalLedgerData[$i]['chequeNumber'] = '';
                $generalLedgerData[$i]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                $generalLedgerData[$i]['transactionCurrency'] = $master['transactionCurrency'];
                $generalLedgerData[$i]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                $generalLedgerData[$i]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                $generalLedgerData[$i]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                $generalLedgerData[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $generalLedgerData[$i]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                $generalLedgerData[$i]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                $generalLedgerData[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $generalLedgerData[$i]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['partyContractID'] = '';
                $generalLedgerData[$i]['partyType'] = 'CUS';
                $generalLedgerData[$i]['partyAutoID'] = $master['customerID'];
                $generalLedgerData[$i]['partySystemCode'] = $master['customerSystemCode'];
                $generalLedgerData[$i]['partyName'] = $master['customerName'];
                $generalLedgerData[$i]['partyCurrencyID'] = $master['customerCurrencyID'];
                $generalLedgerData[$i]['partyCurrency'] = $master['customerCurrency'];
                $generalLedgerData[$i]['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $generalLedgerData[$i]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                $generalLedgerData[$i]['confirmedByName'] = $master['confirmedByName'];
                $generalLedgerData[$i]['confirmedDate'] = $master['confirmedDate'];
                $generalLedgerData[$i]['approvedDate'] = $master['approvedDate'];
                $generalLedgerData[$i]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                $generalLedgerData[$i]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                $generalLedgerData[$i]['companyID'] = $master['companyID'];
                $generalLedgerData[$i]['companyCode'] = $master['companyCode'];
                $amount = $doubleEntry['debit'];
                if ($doubleEntry['amountType'] == 'cr') {
                    $amount = ($doubleEntry['credit'] * -1);
                }

                $transactionAmount = $doubleEntry['transactionAmount'];

                $generalLedgerData[$i]['transactionAmount'] = round($transactionAmount, $doubleEntry['transactionDecimal']);
                $generalLedgerData[$i]['companyLocalAmount'] = round(($transactionAmount / $generalLedgerData[$i]['companyLocalExchangeRate']), $generalLedgerData[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['companyReportingAmount'] = round(($transactionAmount / $generalLedgerData[$i]['companyReportingExchangeRate']), $generalLedgerData[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['partyCurrencyAmount'] = round(($transactionAmount / $generalLedgerData[$i]['partyExchangeRate']), $generalLedgerData[$i]['partyCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['amount_type'] = $doubleEntry['amountType'];
                $generalLedgerData[$i]['documentDetailAutoID'] = $doubleEntry['auto_id'];
                $generalLedgerData[$i]['GLAutoID'] = $doubleEntry['GLAutoID'];
                $generalLedgerData[$i]['systemGLCode'] = $doubleEntry['SystemGLCode'];
                $generalLedgerData[$i]['GLCode'] = $doubleEntry['GLSecondaryCode'];
                $generalLedgerData[$i]['GLDescription'] = $doubleEntry['GLDescription'];
                $generalLedgerData[$i]['GLType'] = $doubleEntry['GLType'];
                $generalLedgerData[$i]['segmentID'] = $doubleEntry['segmentID'];
                $generalLedgerData[$i]['segmentCode'] = $doubleEntry['segmentCode'];
                $generalLedgerData[$i]['subLedgerType'] = $doubleEntry['subLedgerType'];
                $generalLedgerData[$i]['subLedgerDesc'] = $doubleEntry['subLedgerDesc'];
                $generalLedgerData[$i]['isAddon'] = 0;
                $generalLedgerData[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $generalLedgerData[$i]['createdPCID'] = $this->common_data['current_pc'];
                $generalLedgerData[$i]['createdUserID'] = $this->common_data['current_userID'];
                $generalLedgerData[$i]['createdDateTime'] = $this->common_data['current_date'];
                $generalLedgerData[$i]['createdUserName'] = $this->common_data['current_user'];
                $i++;
            }


            if (!empty($generalLedgerData)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalLedgerData);
            }


            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];

            $this->db->where('salesReturnAutoID', $system_id);
            $this->db->update('srp_erp_salesreturnmaster', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            //return true;
            //$this->session->set_flashdata('s', 'Document approved successfully.');
            return array('error' => 1, 'An error has occurred!');
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 'Document approved successfully.');
            //return true;
        }
    }

    function re_open_inventory()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID') ?? ''));
        $this->db->update('srp_erp_salesreturnmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_stock_return()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID') ?? ''));
        $this->db->update('srp_erp_stockreturnmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_material_issue()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $this->db->update('srp_erp_itemissuemaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_stock_transfer()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
        $this->db->update('srp_erp_stocktransfermaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_stock_adjestment()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID') ?? ''));
        $this->db->update('srp_erp_stockadjustmentmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function stockadjustmentAccountUpdate()
    {

        $gl = fetch_gl_account_desc($this->input->post('PLGLAutoID'));

        $BLGLAutoID = $this->input->post('BLGLAutoID');

        $data = array(
            'PLGLAutoID' => $this->input->post('PLGLAutoID'),
            'PLSystemGLCode' => $gl['systemAccountCode'],
            'PLGLCode' => $gl['GLSecondaryCode'],
            'PLDescription' => $gl['GLDescription'],
            'PLType' => $gl['subCategory'],
        );
        if (isset($BLGLAutoID)) {
            $bl = fetch_gl_account_desc($this->input->post('BLGLAutoID'));
            $data = array_merge($data, array('BLGLAutoID' => $this->input->post('BLGLAutoID'),
                'BLSystemGLCode' => $bl['systemAccountCode'],
                'BLGLCode' => $bl['GLSecondaryCode'],
                'BLDescription' => $bl['GLDescription']));
        }
        if ($this->input->post('applyAll') == 1) {
            $this->db->where('stockAdjustmentAutoID', trim($this->input->post('masterID') ?? ''));
        } else {
            $this->db->where('stockAdjustmentDetailsAutoID', trim($this->input->post('detailID') ?? ''));
        }

        $this->db->update('srp_erp_stockadjustmentdetails', $data);
        return array('s', 'GL Account Successfully Changed');

    }

    function materialAccountUpdate()
    {
        $gl = fetch_gl_account_desc($this->input->post('PLGLAutoID'));

        $BLGLAutoID = $this->input->post('BLGLAutoID');

        $data = array(
            'PLGLAutoID' => $this->input->post('PLGLAutoID'),
            'PLSystemGLCode' => $gl['systemAccountCode'],
            'PLGLCode' => $gl['GLSecondaryCode'],
            'PLDescription' => $gl['GLDescription'],
            'PLType' => $gl['subCategory'],
        );
        if (isset($BLGLAutoID)) {
            $bl = fetch_gl_account_desc($this->input->post('BLGLAutoID'));
            $data = array_merge($data, array('BLGLAutoID' => $this->input->post('BLGLAutoID'),
                'BLSystemGLCode' => $bl['systemAccountCode'],
                'BLGLCode' => $bl['GLSecondaryCode'],
                'BLDescription' => $bl['GLDescription']));
        }


        if ($this->input->post('applyAll') == 1) {
            $this->db->where('itemIssueAutoID', trim($this->input->post('masterID') ?? ''));
        } else {
            $this->db->where('itemIssueDetailID', trim($this->input->post('detailID') ?? ''));
        }
        $this->db->update('srp_erp_itemissuedetails', $data);
        return array('s', 'GL Account Successfully Changed');
    }


    function fetch_stockTransfer_all_detail_edit()
    {
        $this->db->select('st.stockTransferDetailsID,st.activityCodeID,w.currentStock as wareHouseStock,st.itemAutoID,st.itemDescription,st.itemSystemCode, st.defaultUOMID,st.unitOfMeasureID,st.transfer_QTY,st.segmentID,st.segmentCode,st.projectID,st.noOfItems,st.grossQty,st.noOfUnits,st.deduction,srp_erp_itemmaster.seconeryItemCode');
        $this->db->from('srp_erp_stocktransferdetails st');
        $this->db->join('srp_erp_warehouseitems w', 'w.itemAutoID = st.itemAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = st.itemAutoID','Left');
        $this->db->where('wareHouseAutoID', trim($this->input->post('location') ?? ''));
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
        $data['details'] = $this->db->get()->result_array();

        $this->db->select('srp_erp_unit_of_measure.UnitID,UnitShortCode,UnitDes,conversion,masterUnitID');
        $this->db->from('srp_erp_unitsconversion');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_unitsconversion.subUnitID');
        $this->db->where('srp_erp_unitsconversion.companyID', $this->common_data['company_data']['company_id']);
        $data['alluom'] = $this->db->get()->result_array();

        return $data;

    }

    function save_stock_transfer_detail_edit_all_multiple()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $stockTransferDetailsID = $this->input->post('stockTransferDetailsID');
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $transfer_QTY = $this->input->post('transfer_QTY');
        $projectID = $this->input->post('projectID');
        $a_segment = $this->input->post('a_segment');

        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            if (!$stockTransferDetailsID[$key]) {
                $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('stockTransferAutoID', $stockTransferAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();

                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            } else {
                $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('stockTransferAutoID', $stockTransferAutoID);
                $this->db->where('stockTransferDetailsID !=', $stockTransferDetailsID[$key]);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();

                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);
            $data['stockTransferAutoID'] = $stockTransferAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){

                $batch_number2 = $this->input->post('batch_number['.$key.']');
                $arraydata2 = implode(',',$batch_number2);
                $data['batchNumber'] = $arraydata2;
                
            }
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['transfer_QTY'] = $transfer_QTY[$key];
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];

            $this->db->select('to_wareHouseAutoID,to_wareHouseLocation,to_wareHouseDescription,from_wareHouseAutoID');
            $this->db->from('srp_erp_stocktransfermaster');
            $this->db->where('stockTransferAutoID', $stockTransferAutoID);
            $master = $this->db->get()->row_array();

            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['from_wareHouseAutoID']);
            $fromWarehouseGl = $this->db->get()->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $toWarehouseGl = $this->db->get()->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $master['to_wareHouseAutoID'],
                    'wareHouseLocation' => $master['to_wareHouseLocation'],
                    'wareHouseDescription' => $master['to_wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'barCodeNo' => $item_data['barcode'],
                    'salesPrice' => $item_data['companyLocalSellingPrice'],
                    'ActiveYN' => $item_data['isActive'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }


            if ($fromWarehouseGl['warehouseType'] == 2) {
                $data['fromWarehouseType'] = 2;
                $data['fromWarehouseWIPGLAutoID'] = $fromWarehouseGl['WIPGLAutoID'];
            }

            if ($toWarehouseGl['warehouseType'] == 2) {
                $data['toWarehouseType'] = 2;
                $data['toWarehouseWIPGLAutoID'] = $toWarehouseGl['WIPGLAutoID'];
            }

            if (trim($stockTransferDetailsID[$key])) {
                $this->db->where('stockTransferDetailsID', trim($stockTransferDetailsID[$key]));
                $this->db->update('srp_erp_stocktransferdetails', $data);
                $this->db->trans_complete();
            } else {
                $data['itemFinanceCategory'] = $item_data['subcategoryID'];
                $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
                $data['financeCategory'] = $item_data['financeCategory'];
                $data['itemCategory'] = $item_data['mainCategory'];
                if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data['PLGLCode'] = $item_data['costGLCode'];
                    $data['PLDescription'] = $item_data['costDescription'];
                    $data['PLType'] = $item_data['costType'];
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                } elseif ($data['financeCategory'] == 2) {
                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data['PLGLCode'] = $item_data['costGLCode'];
                    $data['PLDescription'] = $item_data['costDescription'];
                    $data['PLType'] = $item_data['costType'];
                    $data['BLGLAutoID'] = '';
                    $data['BLSystemGLCode'] = '';
                    $data['BLGLCode'] = '';
                    $data['BLDescription'] = '';
                    $data['BLType'] = '';
                }
                $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
                $data['currentStock'] = $item_data['currentStock'];
                $data['totalValue'] = ($data['currentlWacAmount'] * $data['transfer_QTY']);

                $this->db->insert('srp_erp_stocktransferdetails', $data);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Stock Transfer Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 's', 'Stock Transfer Detail :  Saved Successfully.');
        }

    }


    function save_material_detail_multiple_edit()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $itemIssueDetailID = $this->input->post('itemIssueDetailID');
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $projectID = $this->input->post('projectID');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $quantityRequested = $this->input->post('quantityRequested');
        $a_segment = $this->input->post('a_segment');
        $comment = $this->input->post('comment');

        $this->db->trans_start();

        $companyID = current_companyID();
        $issueMaster = $this->db->query("SELECT * FROM srp_erp_itemissuemaster WHERE itemIssueAutoID = {$itemIssueAutoID}")->row_array();
        $mfqWarehouseAutoID = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster WHERE warehouseAutoID = {$issueMaster['wareHouseAutoID']} AND companyID = {$companyID}")->row('mfqWarehouseAutoID');

        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$itemIssueDetailID[$key]) {
                $this->db->select('itemIssueAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_itemissuedetails');
                $this->db->where('itemIssueAutoID', $itemIssueAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            } else {
                $this->db->select('itemIssueAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_itemissuedetails');
                $this->db->where('itemIssueAutoID', $itemIssueAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('itemIssueDetailID !=', $itemIssueDetailID[$key]);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

            $data['itemIssueAutoID'] = trim($this->input->post('itemIssueAutoID') ?? '');
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){

                $batch_number2 = $this->input->post('batch_number['.$key.']');
                $arraydata2 = implode(',',$batch_number2);
                $data['batchNumber'] = $arraydata2;
                
            }

            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['qtyIssued'] = $quantityRequested[$key];
            $data['comments'] = $comment[$key];
            $data['remarks'] = '';
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                if($mfqWarehouseAutoID) {
                    $wipGLDesc=fetch_gl_account_desc($this->common_data['controlaccounts']['WIP']);
                    $data['BLGLAutoID'] = $this->common_data['controlaccounts']['WIP'];
                    $data['BLSystemGLCode'] = $wipGLDesc['systemAccountCode'];
                    $data['BLGLCode'] = $wipGLDesc['GLSecondaryCode'];
                    $data['BLDescription'] = $wipGLDesc['GLDescription'];
                    $data['BLType'] = $wipGLDesc['subCategory'];
                } else {
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                }
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyIssued'] / $data['conversionRateUOM']));
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($itemIssueDetailID[$key])) {
                $this->db->where('itemIssueDetailID', trim($itemIssueDetailID[$key]));
                $this->db->update('srp_erp_itemissuedetails', $data);
            } else {
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_itemissuedetails', $data);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item Issue Detail :  Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Issue Detail :  Updated Successfully.');

        }

    }


    function save_material_request_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $isuDate = $this->input->post('requestedDate');
        $issueDate = input_format_date($isuDate, $date_format_policy);
        //$segment = explode('|', trim($this->input->post('segment') ?? ''));
        $location = explode('|', trim($this->input->post('location_dec') ?? ''));

        $data['documentID'] = 'MR';
        $data['itemType'] = trim($this->input->post('itemType') ?? '');
        $data['requestedDate'] = trim($issueDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $data['wareHouseAutoID'] = trim($this->input->post('location') ?? '');
        $data['wareHouseCode'] = trim($location[0] ?? '');
        $data['wareHouseLocation'] = trim($location[1] ?? '');
        $data['wareHouseDescription'] = trim($location[2] ?? '');
        $data['jobNo'] = trim($this->input->post('jobNo') ?? '');

        if ($this->input->post('employeeID')) {
            $Requested = explode('|', trim($this->input->post('requested') ?? ''));
            $data['employeeName'] = trim($Requested[1] ?? '');
            $data['employeeCode'] = trim($Requested[0] ?? '');
            $data['employeeID'] = trim($this->input->post('employeeID') ?? '');
        } else {
            $data['employeeName'] = trim($this->input->post('employeeName') ?? '');
            $data['employeeCode'] = NULL;
            $data['employeeID'] = NULL;
        }
        $narration = ($this->input->post('narration'));
        $data['comment'] = str_replace('<br />', PHP_EOL, $narration);
        //$data['comment'] = trim($this->input->post('narration') ?? '');
        //$data['segmentID'] = trim($segment[0] ?? '');
        //$data['segmentCode'] = trim($segment[1] ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('mrAutoID') ?? '')) {
            $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
            $this->db->update('srp_erp_materialrequest', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Material Request : ' . $data['employeeName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Material Request : ' . $data['employeeName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('mrAutoID'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['companyLocalCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['MRCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_materialrequest', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Material Request : ' . $data['employeeName'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Material Request : ' . $data['employeeName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_inventory_catalogue_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $isuDate = $this->input->post('requestedDate');
        $issueDate = input_format_date($isuDate, $date_format_policy);

        $fromDate = input_format_date($this->input->post('fromDate'), $date_format_policy);
        $toDate = input_format_date($this->input->post('toDate'), $date_format_policy);
        $supplierID = $this->input->post('supplierID');

        $supplierDetails = fetch_supplier_data($supplierID);

        $data['documentID'] = 'MIC';
        $data['requestedDate'] = trim($issueDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $narration = ($this->input->post('narration'));
        $data['comment'] = str_replace('<br />', PHP_EOL, $narration);
        $data['supplierID'] = $supplierID;
        $data['supplierName'] = $supplierDetails['supplierName'];
        $data['supplierCode'] = $supplierDetails['supplierSystemCode'];
        $data['transactionCurrencyID'] = $this->input->post('transactionCurrencyID');
        $data['fromDate'] = $fromDate;
        $data['toDate'] = $toDate;

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('mrAutoID') ?? '')) {
            $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
            $this->db->update('srp_erp_inventorycataloguemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Invemtory Catalogue Request :  Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Invemtory Catalogue Request : Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('mrAutoID'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['companyLocalCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['MRCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_inventorycataloguemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Invemtory Catalogue Request :  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Invemtory Catalogue Request :  Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_material_request_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(requestedDate,\'' . $convertFormat . '\') AS requestedDate');
        $this->db->where('mrAutoID', $this->input->post('mrAutoID'));
        return $this->db->get('srp_erp_materialrequest')->row_array();
    }

    function fetch_material_request_detail()
    {
       
            $mrAutoID = $this->input->post('mrAutoID');
            $secondaryCode = getPolicyValues('SSC', 'All'); 
            $item_code = 'srp_erp_itemmaster.itemSystemCode';
            $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
            if($secondaryCode  == 1){ 
                $item_code = 'seconeryItemCode';
                $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
            }


                $data['detail'] = $this->db->query("SELECT
            srp_erp_materialrequestdetails.*, srp_erp_itemmaster.isSubitemExist,
            srp_erp_materialrequest.wareHouseAutoID,
        srp_erp_warehouseitems.currentStock AS stock,srp_erp_materialrequestdetails.currentWareHouseStock as CurrentStockAddTime,$item_code_alias,srp_erp_itemmaster.itemSystemCode as itemSystemCodeeditall,srp_erp_itemmaster.seconeryItemCode as seconeryItemCodeedditall
        FROM
            srp_erp_materialrequestdetails
        LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_materialrequestdetails.itemAutoID
        LEFT JOIN srp_erp_materialrequest ON srp_erp_materialrequest.mrAutoID = srp_erp_materialrequestdetails.mrAutoID
        JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_materialrequestdetails.itemAutoID
        AND srp_erp_materialrequest.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID
        WHERE
            srp_erp_materialrequestdetails.mrAutoID = '$mrAutoID' ")->result_array();
                return $data;
    }

    function fetch_inventory_catalogue_details(){

        $mrAutoID = $this->input->post('mrAutoID');
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
   
   
        $data['detail'] = $this->db->query("SELECT
            srp_erp_inventorycataloguedetails.*, srp_erp_itemmaster.isSubitemExist,$item_code_alias,srp_erp_itemmaster.itemSystemCode as itemSystemCodeeditall,srp_erp_itemmaster.seconeryItemCode as seconeryItemCodeedditall
            FROM
            srp_erp_inventorycataloguedetails
            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_inventorycataloguedetails.itemAutoID
            LEFT JOIN srp_erp_materialrequest ON srp_erp_materialrequest.mrAutoID = srp_erp_inventorycataloguedetails.mrAutoID
            WHERE
            srp_erp_inventorycataloguedetails.mrAutoID = '$mrAutoID' ")->result_array();
        return $data;

    }


    function save_material_request_detail_multiple()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $mrDetailID = $this->input->post('mrDetailID');
        $mrAutoID = $this->input->post('mrAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $projectID = $this->input->post('projectID');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $quantityRequested = $this->input->post('quantityRequested');
        //$a_segment = $this->input->post('a_segment');
        $comment = $this->input->post('comment');

        $this->db->select('*');
        $this->db->from('srp_erp_materialrequest');
        $this->db->where('mrAutoID', $mrAutoID);
        $masterRecord = $this->db->get()->row_array();

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$mrDetailID) {
                $this->db->select('mrAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_materialrequestdetails');
                $this->db->where('mrAutoID', $mrAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Request Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            //$segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

            $data['mrAutoID'] = trim($this->input->post('mrAutoID') ?? '');
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['qtyRequested'] = $quantityRequested[$key];
            $data['comments'] = $comment[$key];
            $data['remarks'] = '';
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];
            //$data['segmentID'] = $masterRecord['segmentID'];
            //$data['segmentCode'] = $masterRecord['segmentCode'];
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyRequested'] / $data['conversionRateUOM']));
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_materialrequestdetails', $data);

            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $masterRecord['wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $masterRecord['wareHouseAutoID'],
                    'wareHouseLocation' => $masterRecord['wareHouseLocation'],
                    'wareHouseDescription' => $masterRecord['wareHouseDescription'],
                    'itemAutoID' => $itemAutoID,
                    'barCodeNo' => $item_data['barcode'],
                    'salesPrice' => $item_data['companyLocalSellingPrice'],
                    'ActiveYN' => $item_data['isActive'],
                    'itemSystemCode' => $item_data['itemSystemCode'],
                    'itemDescription' => $item_data['itemDescription'],
                    'unitOfMeasureID' => $UnitOfMeasureID[$key],
                    'unitOfMeasure' => trim($uomEx[0] ?? ''),
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item Request Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Request Detail :  Saved Successfully.');

        }

    }

    function fetch_warehouse_item_material_request()
    {
        $this->db->select('wareHouseAutoID,wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->db->from('srp_erp_materialrequest');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
        $query = $this->db->get()->row_array();

        $this->db->select('srp_erp_warehouseitems.currentStock,companyLocalWacAmount,wareHouseDescription,wareHouseLocation');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->where('wareHouseAutoID', $query['wareHouseAutoID']);
        $this->db->where('srp_erp_warehouseitems.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        $this->db->where('srp_erp_warehouseitems.companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get()->row_array();
        if (!empty($data)) {
            return array('status' => true, 'currentStock' => $data['currentStock'], 'WacAmount' => $data['companyLocalWacAmount']);
        } else {
            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse " . $query["wareHouseDescription"] . " ( " . $query["wareHouseLocation"] . " )");
            return array('status' => false);
        }
    }

    function load_material_request_detail()
    {
        $mrDetailID = $this->input->post('mrDetailID');
        $result = $this->db->query("SELECT
	srp_erp_materialrequestdetails.*, srp_erp_warehouseitems.currentStock AS Stock,srp_erp_itemmaster.seconeryItemCode as seconeryItemCode
FROM
	srp_erp_materialrequestdetails
JOIN srp_erp_materialrequest ON srp_erp_materialrequest.mrAutoID = srp_erp_materialrequestdetails.mrAutoID
JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_materialrequestdetails.itemAutoID
AND srp_erp_materialrequest.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID
LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_materialrequestdetails.itemAutoID
WHERE
	mrDetailID = '$mrDetailID'")->row_array();
        return $result;
    }

    function save_material_request_detail()
    {
        $projectExist = project_is_exist();
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        if (!empty($this->input->post('mrDetailID'))) {
            $this->db->select('mrAutoID,,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_materialrequestdetails');
            $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->where('mrDetailID !=', trim($this->input->post('mrDetailID') ?? ''));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->select('*');
        $this->db->from('srp_erp_materialrequest');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
        $masterRecord = $this->db->get()->row_array();
        $this->db->trans_start();
        //$segment = explode('|', trim($this->input->post('a_segment') ?? ''));
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
        $projectID = trim($this->input->post('projectID') ?? '');
        $data['mrAutoID'] = trim($this->input->post('mrAutoID') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['qtyRequested'] = trim($this->input->post('quantityRequested') ?? '');
        $data['comments'] = trim($this->input->post('comment') ?? '');
        $data['remarks'] = trim($this->input->post('remarks') ?? '');
        $data['currentWareHouseStock'] = trim($this->input->post('currentWareHouseStockQty') ?? '');
        $data['segmentID'] = $masterRecord['segmentID'];
        $data['segmentCode'] = $masterRecord['segmentCode'];
        $data['itemFinanceCategory'] = $item_data['subcategoryID'];
        $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
        $data['financeCategory'] = $item_data['financeCategory'];
        $data['itemCategory'] = $item_data['mainCategory'];
        $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
        $data['currentStock'] = $item_data['currentStock'];
        if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
            $data['PLGLAutoID'] = $item_data['costGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['PLGLCode'] = $item_data['costGLCode'];
            $data['PLDescription'] = $item_data['costDescription'];
            $data['PLType'] = $item_data['costType'];

            $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
            $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['BLGLCode'] = $item_data['assteGLCode'];
            $data['BLDescription'] = $item_data['assteDescription'];
            $data['BLType'] = $item_data['assteType'];
        } elseif ($data['financeCategory'] == 2) {
            $data['PLGLAutoID'] = $item_data['costGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['PLGLCode'] = $item_data['costGLCode'];
            $data['PLDescription'] = $item_data['costDescription'];
            $data['PLType'] = $item_data['costType'];

            $data['BLGLAutoID'] = '';
            $data['BLSystemGLCode'] = '';
            $data['BLGLCode'] = '';
            $data['BLDescription'] = '';
            $data['BLType'] = '';
        }
        $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyRequested'] / $data['conversionRateUOM']));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('mrDetailID') ?? '')) {
            $this->db->where('mrDetailID', trim($this->input->post('mrDetailID') ?? ''));
            $this->db->update('srp_erp_materialrequestdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Item Request Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Item Request Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_materialrequestdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Item Request Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Item Request Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }


    function fetch_template_data_MR($mrAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        
        
        $this->db->select('*,DATE_FORMAT(requestedDate,\'' . $convertFormat . '\') AS requestedDate,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN 
        CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('mrAutoID', $mrAutoID);
        $this->db->from('srp_erp_materialrequest');
        $data['master'] = $this->db->get()->row_array();
        
        
        $this->db->select('*,srp_erp_materialrequestdetails.comments as comments,'.$item_code_alias.'');
        $this->db->where('mrAutoID', $mrAutoID);
        $this->db->from('srp_erp_materialrequestdetails');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_materialrequestdetails.itemAutoID','left');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_material_request_item()
    {
        $id = $this->input->post('mrDetailID');

        $this->db->select('*');
        $this->db->from('srp_erp_materialrequestdetails');
        $this->db->where('mrDetailID', $id);
        $detail_arr = $this->db->get()->row_array();

        /** update sub item master */

        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $detail_arr['mrAutoID']);
        $this->db->where('soldDocumentDetailID', $detail_arr['mrDetailID']);
        $this->db->where('soldDocumentID', 'MR');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);


        /** end update sub item master */
        $this->db->where('mrDetailID', $id);
        $result = $this->db->delete('srp_erp_materialrequestdetails');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }


    function delete_material_request_header()
    {
        $masterID = trim($this->input->post('mrAutoID') ?? '');
        $this->db->select('*');
        $this->db->from('srp_erp_materialrequestdetails');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
        $datas = $this->db->get()->row_array();

        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before deleting this document.');
            return true;
        } else {
            /* Added */
            $documentCode = $this->db->get_where('srp_erp_materialrequest', ['mrAutoID'=> $masterID])->row('MRCode');
            $this->db->trans_start();

            $length = strlen($documentCode);
            if($length > 1){
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
                $this->db->update('srp_erp_materialrequest', $data);
            }
            else{
                $this->db->where('mrAutoID', $masterID)->delete('srp_erp_materialrequestdetails');
                $this->db->where('mrAutoID', $masterID)->delete('srp_erp_materialrequest');
            }

            $this->db->trans_complete();
            if($this->db->trans_status() == true){
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }else{
                $this->session->set_flashdata('e', 'Error in delete process.');

                return false;
            }
            /* End */
        }

       /* $data = array(
            'isDeleted' => 1,
            'deletedEmpID' => current_userID(),
            'deletedDate' => current_date(),
        );
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
        $this->db->update('srp_erp_materialrequest', $data);
        $this->session->set_flashdata('s', 'Deleted Successfully.');
        return true;*/


    }

    function re_open_material_request()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
        $this->db->update('srp_erp_materialrequest', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }


    function material_request_item_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $companyID = current_companyID();
        $currentuser = current_userID();
        $this->db->select('mrAutoID');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
        $this->db->from('srp_erp_materialrequestdetails');
        $result = $this->db->get()->row_array();
        if (empty($result)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return false;
        } else {
            $this->db->select('mrAutoID');
            $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_materialrequest');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->db->select('MRCode,documentID,DATE_FORMAT(requestedDate, "%Y") as invYear,DATE_FORMAT(requestedDate, "%m") as invMonth,requestedDate');
                $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
                $this->db->from('srp_erp_materialrequest');
                $master_dt = $this->db->get()->row_array();

                $docDate = $master_dt['requestedDate'];
                $Comp = current_companyID();
                $companyFinanceYearID = $this->db->query("SELECT
                        period.companyFinanceYearID as companyFinanceYearID
                    FROM
                        srp_erp_companyfinanceperiod period
                    WHERE
                        period.companyID = $Comp
                    AND '$docDate' BETWEEN period.dateFrom
                    AND period.dateTo
                    AND period.isActive = 1")->row_array();

                if (empty($companyFinanceYearID['companyFinanceYearID'])) {
                    $companyFinanceYearID['companyFinanceYearID'] = NULL;
                }

                $this->load->library('sequence');
                if ($master_dt['MRCode'] == "0" || empty($master_dt['MRCode'])) {
                    if ($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location == '')) {
                            $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                            return false;
                        } else {
                            if ($locationemployee != '') {
                                $mrcode = $this->sequence->sequence_generator_location($master_dt['documentID'], $companyFinanceYearID['companyFinanceYearID'], $locationemployee, $master_dt['invYear'], $master_dt['invMonth']);
                            } else {
                                $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                                return false;
                            }
                        }
                    } else {
                        $mrcode = $this->sequence->sequence_generator_fin($master_dt['documentID'], $companyFinanceYearID['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }

                    $validate_code = validate_code_duplication($mrcode, 'MRCode', trim($this->input->post('mrAutoID') ?? ''),'mrAutoID', 'srp_erp_materialrequest');
                    if(!empty($validate_code)) {
                        $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                        return false;
                    }

                    
                    $pvCd = array(
                        'MRCode' => $mrcode
                    );
                    $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
                    $this->db->update('srp_erp_materialrequest', $pvCd);
                } else {
                    $validate_code = validate_code_duplication($master_dt['MRCode'], 'MRCode', trim($this->input->post('mrAutoID') ?? ''),'mrAutoID', 'srp_erp_materialrequest');
                    if(!empty($validate_code)) {
                        $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                        return false;
                    }
                }

                $this->load->library('Approvals');
                $this->db->select('mrAutoID, MRCode,requestedDate');
                $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
                $this->db->from('srp_erp_materialrequest');
                $app_data = $this->db->get()->row_array();

                $autoApproval = get_document_auto_approval('MR');
                if ($autoApproval == 0) {
                    $approvals_status = $this->approvals->auto_approve($app_data['mrAutoID'], 'srp_erp_materialrequest', 'mrAutoID', 'MR', $app_data['MRCode'], $app_data['requestedDate']);
                } elseif ($autoApproval == 1) {
                    $approvals_status = $this->approvals->CreateApproval('MR', $app_data['mrAutoID'], $app_data['MRCode'], 'Material Request', 'srp_erp_materialrequest', 'mrAutoID', 0, $app_data['requestedDate']);
                } else {
                    $this->session->set_flashdata('e', 'Approval levels are not set for this document ');
                    return false;
                    exit;
                }

                if ($approvals_status == 1) {
                    $autoApproval = get_document_auto_approval('MR');
                    if ($autoApproval == 0) {
                        $result = $this->save_material_request_approval(0, $app_data['mrAutoID'], 1, 'Auto Approved');
                        if ($result) {
                            $this->session->set_flashdata('s', 'Approvals Created Successfully ');
                            return true;
                        }
                    } else {
                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user']
                        );
                        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
                        $this->db->update('srp_erp_materialrequest', $data);
                        $this->session->set_flashdata('s', 'Approvals Created Successfully ');
                        return true;
                    }
                } else {
                    return false;
                }
            }
        }

    }


    function save_material_request_detail_multiple_edit()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $mrDetailID = $this->input->post('mrDetailID');
        $mrAutoID = $this->input->post('mrAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $projectID = $this->input->post('projectID');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $quantityRequested = $this->input->post('quantityRequested');
        $a_segment = $this->input->post('a_segment');
        $comment = $this->input->post('comment');

        $this->db->select('*');
        $this->db->from('srp_erp_materialrequest');
        $this->db->where('mrAutoID', $mrAutoID);
        $masterRecord = $this->db->get()->row_array();

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$mrDetailID[$key]) {
                $this->db->select('mrAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_materialrequestdetails');
                $this->db->where('mrAutoID', $mrAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Request Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            } else {
                $this->db->select('mrAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_materialrequestdetails');
                $this->db->where('mrAutoID', $mrAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('mrDetailID !=', $mrDetailID[$key]);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Request Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key] ?? '');
            $uomEx = explode('|', $uom[$key] ??  '');
            $item_data = fetch_item_data($itemAutoID);

            $data['mrAutoID'] = trim($this->input->post('mrAutoID') ?? '');
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['qtyRequested'] = $quantityRequested[$key];
            $data['comments'] = $comment[$key];
            $data['remarks'] = '';
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];
            $data['segmentID'] = $masterRecord['segmentID'];
            $data['segmentCode'] = $masterRecord['segmentCode'];
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyRequested'] / $data['conversionRateUOM']));
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($mrDetailID[$key])) {
                $this->db->where('mrDetailID', trim($mrDetailID[$key]));
                $this->db->update('srp_erp_materialrequestdetails', $data);
            } else {
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_materialrequestdetails', $data);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item Request Detail :  Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Request Detail :  Updated Successfully.');

        }

    }

    function save_material_request_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        //$this->db->trans_start();
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('mrAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['mrAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'MR');
        }

        if ($approvals_status) {
            if ($status == 1) {
                return array('s', 'Approved Successfully.', 1);
            } else {
                return array('s', 'Rejected Successfully.', 1);
            }

        } else {
            return array('e', 'Approval Failed.', 1);
        }

    }

    function fetch_MR_code()
    {
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');

        $this->db->select('issueDate,itemType,wareHouseAutoID,requestedWareHouseAutoID,segmentID');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', trim($itemIssueAutoID));
        $result = $this->db->get()->row_array();

        $issueDate = $result['issueDate'];
        $itemType = $result['itemType'];
        $requestedWareHouseAutoID = $result['requestedWareHouseAutoID'];
        $companyID = current_companyID();

        $data = $this->db->query("SELECT
	mrm.mrAutoID,
	MRCode,
	requestedDate,
	employeeName,
/*IFNULL(SUM(mrqdetail.qtyRequested),0) as qtyRequested,*/
	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((IFNULL( SUM( mrqdetail.qtyRequested ), 0 )), 2)))))) AS qtyRequested,
	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((IFNULL( SUM( mrqdetail.mrQty ), 0 )), 2 )))))) AS mrQty,
	TRIM(TRAILING '.' FROM(TRIM(TRAILING 0 FROM ((ROUND(( IFNULL( SUM( mrqdetail.stQty ), 0 )), 2 )))))) AS stQty,
	TRIM(TRAILING '.' FROM(TRIM(TRAILING 0 FROM ((ROUND((IFNULL( SUM( mrqdetail.qtyRequested ), 0 ) - (IFNULL( SUM( mrqdetail.stQty ), 0 ) + IFNULL( SUM( mrqdetail.mrQty ), 0 ))), 2 )))))) AS usageQty
/*IFNULL(SUM(mrqdetail.mrQty),0) as mrQty*/
FROM
	srp_erp_materialrequest mrm
LEFT JOIN (
	SELECT
		mrd.mrDetailID,
		mrd.mrAutoID,
		mrd.qtyRequested,
		sum(srp_erp_itemissuedetails.qtyIssued) AS mrQty,
		sum( transfer_QTY ) AS stQty 
	FROM
	srp_erp_materialrequestdetails mrd
	LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuedetails.mrDetailID= mrd.mrDetailID
	LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransferdetails.mrDetailID = mrd.mrDetailID
	GROUP BY
		mrDetailID
) mrqdetail ON mrqdetail.mrAutoID = mrm.mrAutoID
WHERE
	mrm.requestedDate <= '$issueDate'
AND mrm.itemType = '$itemType'
AND mrm.wareHouseAutoID = '$requestedWareHouseAutoID'
AND mrm.companyID = '$companyID'
AND mrm.approvedYN = 1
GROUP BY
		mrm.mrAutoID")->result_array();
        return $data;

        /*$this->db->select('mrAutoID,MRCode,requestedDate,employeeName');
        $this->db->from('srp_erp_materialrequest');
        $this->db->where('requestedDate <=', trim($result['issueDate'] ?? ''));
        $this->db->where('itemType', trim($result['itemType'] ?? ''));
        $this->db->where('wareHouseAutoID', trim($result['requestedWareHouseAutoID'] ?? ''));
        $this->db->where('companyID', trim(current_companyID()));
        $this->db->where('approvedYN', 1);
        return $this->db->get()->result_array();*/
    }

    function fetch_mr_detail_table()
    {
        $itemIssueAutoID = trim($this->input->post('itemIssueAutoID') ?? '');

        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        $this->db->select('*');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
        $this->db->where('companyID', trim(current_companyID()));
        $this->db->from('srp_erp_materialrequest');
        $master = $this->db->get()->row_array();

        $this->db->select('issueDate,itemType,wareHouseAutoID,requestedWareHouseAutoID');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $issueMaster = $this->db->get()->row_array();

        $issueMasterWarehouseID = $issueMaster['wareHouseAutoID'];
        $warehouseID = $master['wareHouseAutoID'];
        $mrAutoID = $this->input->post('mrAutoID');
        $companyID = current_companyID();
        /*$this->db->select('srp_erp_materialrequestdetails.*,srp_erp_itemissuedetails.qtyIssued as qtyIssued,srp_erp_warehouseitems.currentStock as stock');
        $this->db->where('srp_erp_materialrequestdetails.mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
        $this->db->where('srp_erp_materialrequestdetails.companyID', trim(current_companyID()));
        $this->db->from('srp_erp_materialrequestdetails');
        $this->db->join('srp_erp_itemissuedetails', 'srp_erp_itemissuedetails.mrDetailID = srp_erp_materialrequestdetails.mrDetailID AND srp_erp_itemissuedetails.mrAutoID = srp_erp_materialrequestdetails.mrAutoID', 'left');
        $this->db->join('srp_erp_warehouseitems', 'srp_erp_warehouseitems.itemAutoID = srp_erp_materialrequestdetails.itemAutoID AND srp_erp_warehouseitems.wareHouseAutoID = '.$warehouseID.'', 'left');
        $data['detail'] = $this->db->get()->result_array();*/

        $data['detail'] = $this->db->query("SELECT
            srp_erp_materialrequestdetails.*,$item_code_alias,/*det.qtyIssued AS qtyIssued,
            IFNULL(itmlg.currentStock,0) AS stock,detMaterialIssue.miQtyIssued AS miQtyIssued*/
        TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((IFNULL( det.qtyIssued, 0 ) + IFNULL( STdet.transfer_QTY, 0 )), 2 )))))) AS qtyIssued,
        TRIM(TRAILING '.' FROM (TRIM((ROUND((IFNULL( itmlg.currentStock, 0 )), 2 ))))) AS stock,
        TRIM(TRAILING '.' FROM (TRIM((ROUND(( detMaterialIssue.miQtyIssued ), 2 ))))) AS miQtyIssued,
        TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(( IFNULL(qtyRequested , 0) - (IFNULL(qtyIssued, 0) + IFNULL( STdet.transfer_QTY, 0 )) ), 2 )))))) AS balanceQTY
        FROM
            srp_erp_materialrequestdetails
            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_materialrequestdetails.itemAutoID
        LEFT JOIN (
            SELECT
            COALESCE(SUM(qtyIssued),0) as qtyIssued,mrDetailID,mrAutoID
            FROM
                srp_erp_itemissuedetails
            GROUP BY
                mrDetailID
        ) AS det ON det.mrDetailID = srp_erp_materialrequestdetails.mrDetailID
        AND det.mrAutoID = srp_erp_materialrequestdetails.mrAutoID 
        LEFT JOIN (
            SELECT
            COALESCE(SUM(transfer_QTY),0) as transfer_QTY,mrDetailID,mrAutoID
            FROM
                srp_erp_stocktransferdetails
            GROUP BY
                mrDetailID
        ) AS STdet ON STdet.mrDetailID = srp_erp_materialrequestdetails.mrDetailID
        AND STdet.mrAutoID = srp_erp_materialrequestdetails.mrAutoID 
        LEFT JOIN (
            SELECT
                COALESCE(SUM(qtyIssued),0) AS miQtyIssued,
                mrDetailID,
                mrAutoID,
                itemAutoID
            FROM
                srp_erp_itemissuedetails
            WHERE
                itemIssueAutoID = {$itemIssueAutoID}
            GROUP BY
                itemAutoID
        ) AS detMaterialIssue ON detMaterialIssue.itemAutoID = srp_erp_materialrequestdetails.itemAutoID
        LEFT JOIN (
            SELECT
                SUM(
                    transactionQTY / convertionRate
                ) AS currentStock,itemAutoID
            FROM
                srp_erp_itemledger
            WHERE
                wareHouseAutoID = $issueMasterWarehouseID
        GROUP BY
                itemAutoID
        ) itmlg ON itmlg.itemAutoID = srp_erp_materialrequestdetails.itemAutoID
        LEFT JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_materialrequestdetails.itemAutoID
        AND srp_erp_warehouseitems.wareHouseAutoID = $issueMasterWarehouseID
        WHERE
            srp_erp_materialrequestdetails.mrAutoID = $mrAutoID
        AND srp_erp_materialrequestdetails.companyID = $companyID
        GROUP BY srp_erp_materialrequestdetails.mrDetailID")->result_array();
        // echo $this->db->last_query();
        return $data;
    }

    function save_mr_base_items()
    {
        $qty = $this->input->post('qty');
        $mrDetailID = $this->input->post('mrDetailID');
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');
        $this->db->trans_start();

        $this->db->select('*');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $issueMaster = $this->db->get()->row_array();

        $companyID = current_companyID();
        $mfqWarehouseAutoID = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster WHERE warehouseAutoID = {$issueMaster['wareHouseAutoID']} AND companyID = {$companyID}")->row('mfqWarehouseAutoID');

        $this->db->select('cca.GLAutoID as GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory');
        $this->db->from('srp_erp_companycontrolaccounts cca');
        $this->db->join('srp_erp_chartofaccounts ca', 'cca.GLAutoID = ca.GLAutoID', 'LEFT');
        $this->db->where('controlAccountType', 'GIT');
        $this->db->where('cca.companyID', $this->common_data['company_data']['company_id']);
        $materialRequestGlDetail = $this->db->get()->row_array();

        foreach ($mrDetailID as $key => $mrDetailID) {

            if ($qty[$key] != 0) {
                $this->db->select('srp_erp_materialrequestdetails.*');
                $this->db->from('srp_erp_materialrequestdetails');
                $this->db->where('mrDetailID', $mrDetailID);
                $itemDetail = $this->db->get()->row_array();

                if ($itemIssueAutoID) {
                    $this->db->select('itemIssueAutoID,,itemDescription,itemSystemCode');
                    $this->db->from('srp_erp_itemissuedetails');
                    $this->db->where('itemIssueAutoID', $itemIssueAutoID);
                    $this->db->where('mrAutoID', $itemDetail['mrAutoID']);
                    $this->db->where('itemAutoID', $itemDetail['itemAutoID']);
                    $order_detail = $this->db->get()->row_array();
                    if (!empty($order_detail)) {
                        $this->session->set_flashdata('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                        return array('status' => false);
                        //return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                    }
                }

                $item_data = fetch_item_data($itemDetail['itemAutoID']);
                $data['itemIssueAutoID'] = trim($this->input->post('itemIssueAutoID') ?? '');
                $data['mrAutoID'] = trim($itemDetail['mrAutoID'] ?? '');
                $data['mrDetailID'] = trim($mrDetailID);
                $data['itemAutoID'] = $itemDetail['itemAutoID'];
                $data['itemSystemCode'] = $item_data['itemSystemCode'];
                $data['itemDescription'] = $item_data['itemDescription'];
                $data['unitOfMeasure'] = trim($itemDetail['unitOfMeasure'] ?? '');
                $data['unitOfMeasureID'] = $itemDetail['unitOfMeasureID'];
                $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
                $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
                $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
                $data['qtyRequested'] = $itemDetail['qtyRequested'];
                $data['qtyIssued'] = $qty[$key];
                $data['comments'] = $itemDetail['comments'];;
                $data['remarks'] = '';
                $data['currentWareHouseStock'] = $itemDetail['currentWareHouseStock'];
                if ($issueMaster['issueType'] != 'Material Request') {
                    $data['segmentID'] = trim($itemDetail['segmentID'] ?? '');
                    $data['segmentCode'] = trim($itemDetail['segmentCode'] ?? '');
                }
                $data['itemFinanceCategory'] = $item_data['subcategoryID'];
                $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
                $data['financeCategory'] = $item_data['financeCategory'];
                $data['itemCategory'] = $item_data['mainCategory'];
                $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
                $data['currentStock'] = $item_data['currentStock'];

                /*                if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                                    $data['PLGLCode'] = $item_data['costGLCode'];
                                    $data['PLDescription'] = $item_data['costDescription'];
                                    $data['PLType'] = $item_data['costType'];

                                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                                    $data['BLGLCode'] = $item_data['assteGLCode'];
                                    $data['BLDescription'] = $item_data['assteDescription'];
                                    $data['BLType'] = $item_data['assteType'];
                                } elseif ($data['financeCategory'] == 2) {
                                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                                    $data['PLGLCode'] = $item_data['costGLCode'];
                                    $data['PLDescription'] = $item_data['costDescription'];
                                    $data['PLType'] = $item_data['costType'];

                                    $data['BLGLAutoID'] = '';
                                    $data['BLSystemGLCode'] = '';
                                    $data['BLGLCode'] = '';
                                    $data['BLDescription'] = '';
                                    $data['BLType'] = '';
                                }*/

                $data['PLGLAutoID'] = $materialRequestGlDetail['GLAutoID'];
                $data['PLSystemGLCode'] = $materialRequestGlDetail['systemAccountCode'];
                $data['PLGLCode'] = $materialRequestGlDetail['GLSecondaryCode'];
                $data['PLDescription'] = $materialRequestGlDetail['GLDescription'];
                $data['PLType'] = $materialRequestGlDetail['subCategory'];

                if($mfqWarehouseAutoID) {
                    $wipGLDesc=fetch_gl_account_desc($this->common_data['controlaccounts']['WIP']);

                    $data['BLGLAutoID'] = $this->common_data['controlaccounts']['WIP'];
                    $data['BLSystemGLCode'] = $wipGLDesc['systemAccountCode'];
                    $data['BLGLCode'] = $wipGLDesc['GLSecondaryCode'];
                    $data['BLDescription'] = $wipGLDesc['GLDescription'];
                    $data['BLType'] = $wipGLDesc['subCategory'];
                } else {
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                }

                $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyIssued'] / $data['conversionRateUOM']));
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_itemissuedetails', $data);
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Material Request : Details Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Material Requestt : Item Details Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }

    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'SLR');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function fetch_signaturelevel_purchasereturn()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'SR');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function fetch_signaturelevel_material_issue()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'MI');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function fetch_signaturelevel_stock_transfer()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'ST');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function fetch_signaturelevel_stock_adjustment()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'SA');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function fetch_signaturelevel_material_request()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'MR');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function delete_stock_adjustment_buyback()
    {
        $id = trim($this->input->post('stock_auto_id') ?? '');

        /** Delete sub item list */
        /* 1---- delete all entries in the update process - item master sub temp */
        $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $id, 'receivedDocumentID' => 'SA'));

        /*2-- reset all marked values */
        $setData['isSold'] = null;
        $setData['soldDocumentID'] = null;
        $setData['soldDocumentAutoID'] = null;
        $setData['soldDocumentDetailID'] = null;
        $ware['soldDocumentID'] = 'SA';
        $ware['soldDocumentAutoID'] = $id;
        $this->db->update('srp_erp_itemmaster_sub', $setData, $ware);
        /** End Delete sub item list */

        //$this->db->delete('srp_erp_stockadjustmentmaster', array('stockAdjustmentAutoID' => $id));
        $this->db->delete('srp_erp_stockadjustmentdetails', array('stockAdjustmentAutoID' => $id));


        $data = array(
            'isDeleted' => 1,
            'deletedEmpID' => current_userID(),
            'deletedDate' => current_date(),
        );
        $this->db->where('stockAdjustmentAutoID', trim($id));
        $this->db->update('srp_erp_stockadjustmentmaster', $data);
        $this->session->set_flashdata('s', 'Deleted Successfully.');
        return true;
    }

    function re_open_stock_adjestment_buyback()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID') ?? ''));
        $this->db->update('srp_erp_stockadjustmentmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function save_stock_adjustment_detail_multiple_buyback()
    {
        $stockAdjustmentDetailsAutoID = $this->input->post('stockAdjustmentDetailsAutoID');
        $stockAdjustmentAutoID = $this->input->post('stockAdjustmentAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $currentWareHouseStock = $this->input->post('currentWareHouseStock');
        $currentWac = $this->input->post('currentWac');
        $projectID = $this->input->post('projectID');
        $adjustment_Stock = $this->input->post('adjustment_Stock');
        $adjustment_wac = $this->input->post('adjustment_wac');
        $a_segment = $this->input->post('a_segment');
        $noofitems = $this->input->post('noOfItems');
        $grossqty = $this->input->post('grossQty');
        $noofunits = $this->input->post('noOfUnits');
        $deduction = $this->input->post('deduction');
        $deductionvalue = $this->input->post('deductionvalue');

        $projectExist = project_is_exist();
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];

        $this->db->select('wareHouseAutoID, wareHouseCode, wareHouseLocation, wareHouseDescription,adjustmentType');
        $this->db->where('stockAdjustmentAutoID', $this->input->post('stockAdjustmentAutoID'));
        $stockadjustmentMaster = $this->db->get('srp_erp_stockadjustmentmaster')->row_array();


        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$stockAdjustmentDetailsAutoID) {
                $this->db->select('stockAdjustmentDetailsAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stockadjustmentdetails');
                $this->db->where('stockAdjustmentAutoID', $stockAdjustmentAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Adjustment Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

            $previousWareHouseStock = $this->db->query("SELECT currentStock FROM srp_erp_warehouseitems WHERE wareHouseAutoID='{$stockadjustmentMaster['wareHouseAutoID']}' and itemAutoID='{$itemAutoID}'")->row_array(); //get warehouse stock of the item by location

            $this->db->select('currentStock,companyLocalWacAmount,(currentStock * companyLocalWacAmount) as prevItemMasterTotal');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $prevItemMasterTotal = $this->db->get()->row_array();

            $data['stockAdjustmentAutoID'] = $stockAdjustmentAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['noOfItems'] = $noofitems[$key];
            $data['grossQty'] = $grossqty[$key];
            $data['noOfUnits'] = $noofunits[$key];
            $data['deduction'] = $deductionvalue[$key];
            $data['bucketWeightID'] = $deduction[$key];
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {

                $data['PLGLAutoID'] = $item_data['stockAdjustmentGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['stockAdjustmentSystemGLCode'];
                $data['PLGLCode'] = $item_data['stockAdjustmentGLCode'];
                $data['PLDescription'] = $item_data['stockAdjustmentDescription'];
                $data['PLType'] = $item_data['stockAdjustmentType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];

            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
            //$data['previousStock'] = isset($prevItemMasterTotal['currentStock']) && !empty($prevItemMasterTotal['currentStock']) ? $prevItemMasterTotal['currentStock'] : 0;
            $data['previousStock'] = $prevItemMasterTotal['currentStock'];
            $data['previousWac'] = $currentWac[$key];
            $data['previousWareHouseStock'] = isset($previousWareHouseStock["currentStock"]) && !empty($previousWareHouseStock["currentStock"]) ? $previousWareHouseStock["currentStock"] : 0;
            $data['currentWac'] = $adjustment_wac[$key];

            if ($stockadjustmentMaster['adjustmentType'] == 1) {
                $data['currentWareHouseStock'] = isset($previousWareHouseStock["currentStock"]) && !empty($previousWareHouseStock["currentStock"]) ? $previousWareHouseStock["currentStock"] : 0;
            } else {
                $data['currentWareHouseStock'] = $adjustment_Stock[$key];
            }
            $data['adjustmentWac'] = ($adjustment_wac[$key] - $currentWac[$key]);
            if ($stockadjustmentMaster['adjustmentType'] == 1) {
                $data['adjustmentWareHouseStock'] = 0;
            } else {
                $data['adjustmentWareHouseStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            }
            //$data['adjustmentWareHouseStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            if ($stockadjustmentMaster['adjustmentType'] == 1) {
                $data['adjustmentStock'] = 0;
            } else {
                $data['adjustmentStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            }
            //$data['adjustmentStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            $data['currentStock'] = $prevItemMasterTotal['currentStock'] + $data['adjustmentStock'];

            //print_r($data);

            $previousTotal = ($data['previousStock'] * $data['previousWac']);
            $newTotal = ($data['currentStock'] * $data['currentWac']);


            /*$prevItemMasterStock = $prevItemMasterTotal["currentStock"];
            $prevItemMasterWac = $prevItemMasterTotal["companyLocalWacAmount"];
            $prevItemMasterTotal = $prevItemMasterTotal["prevItemMasterTotal"];
            $total = (($data['adjustmentStock'] + $prevItemMasterStock) * $data['currentWac']) - $prevItemMasterTotal;*/

            $prevItemMasterStock = $currentWareHouseStock[$key];
            $prevItemMasterWac = $currentWac[$key];
            $adjustmentStock = $adjustment_Stock[$key];
            $adjustmentWac = $adjustment_wac[$key];
            $total = (($adjustmentStock * $adjustmentWac) - ($prevItemMasterStock * $prevItemMasterWac));

            //$data['totalValue'] = ($newTotal - $previousTotal);
            $data['totalValue'] = ($data['currentStock'] * $data['currentWac']) - ($data['previousStock'] * $data['previousWac']);
            $data['comments'] = '';

            $this->db->insert('srp_erp_stockadjustmentdetails', $data);
            $last_id = $this->db->insert_id();

            if ($item_data['mainCategory'] == 'Inventory' || $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $data['itemAutoID']);
                $this->db->where('wareHouseAutoID', $stockadjustmentMaster['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                if (empty($warehouseitems)) {
                    if ($data['previousWareHouseStock'] == 0) {
                        $data_arr = array(
                            'wareHouseAutoID' => $stockadjustmentMaster['wareHouseAutoID'],
                            'wareHouseLocation' => $stockadjustmentMaster['wareHouseLocation'],
                            'wareHouseDescription' => $stockadjustmentMaster['wareHouseDescription'],
                            'itemAutoID' => $data['itemAutoID'],
                            'barCodeNo' => $item_data['barcode'],
                            'salesPrice' => $item_data['companyLocalSellingPrice'],
                            'ActiveYN' => $item_data['isActive'],
                            'itemSystemCode' => $data['itemSystemCode'],
                            'itemDescription' => $data['itemDescription'],
                            'unitOfMeasureID' => $data['unitOfMeasureID'],
                            'unitOfMeasure' => $data['unitOfMeasure'],
                            'currentStock' => 0,
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'companyCode' => $this->common_data['company_data']['company_code'],
                        );

                        $this->db->insert('srp_erp_warehouseitems', $data_arr);
                    }

                }
            }


            /*sub item master config : multiple add scanario */
            $adjustedStock = $data['adjustmentStock'];

            if ($item_data['isSubitemExist'] == 1) {

                if ($data['previousStock'] < $data['currentStock']) {
                    /* Add Stock */
                    $qty = $adjustedStock;

                    $subData['uom'] = $data['unitOfMeasure'];
                    $subData['uomID'] = $data['unitOfMeasureID'];
                    $subData['grv_detailID'] = $last_id;
                    $subData['warehouseAutoID'] = $stockadjustmentMaster['wareHouseAutoID'];
                    $this->add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $stockAdjustmentAutoID, $last_id, 'SA', $item_data['itemSystemCode'], $subData);
                }

            }

            /*end of sub item master config */

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Stock Adjustment Details : Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 's', 'Stock Adjustment Details : Saved Successfully.');
        }
    }

    function stock_adjustment_confirmation_buyback()
    {
        $this->db->select('stockAdjustmentAutoID');
        $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID') ?? ''));
        $this->db->from('srp_erp_stockadjustmentdetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!.');
        } else {
            $this->db->select('stockAdjustmentAutoID');
            $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_stockadjustmentmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $id = trim($this->input->post('stockAdjustmentAutoID') ?? '');
                //$isProductReference_completed = $this->isProductReference_completed_document_SA($id);
                $isProductReference_completed = isMandatory_completed_document($id, 'SA');
                if ($isProductReference_completed == 0) {


                    /** item Master Sub check : sub item already added items check box are ch */

                    $validate = $this->validate_itemMasterSub($id, 'SA');

                    /** validation skipped until they found this. we have to do the both side of check in the validate_itemMasterSub method and have to change the query */

                    if ($validate) {

                        $this->load->library('Approvals');
                        $this->db->select('stockAdjustmentAutoID, stockAdjustmentCode');
                        $this->db->where('stockAdjustmentAutoID', $id);
                        $this->db->from('srp_erp_stockadjustmentmaster');
                        $app_data = $this->db->get()->row_array();
                        $approvals_status = $this->approvals->CreateApproval('SA', $app_data['stockAdjustmentAutoID'], $app_data['stockAdjustmentCode'], 'Stock Adjustment', 'srp_erp_stockadjustmentmaster', 'stockAdjustmentAutoID');
                        if ($approvals_status == 1) {
                            $data = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user']
                            );

                            $this->db->where('stockAdjustmentAutoID', $id);
                            $this->db->update('srp_erp_stockadjustmentmaster', $data);
                        } else if ($approvals_status == 3) {
                            return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document');

                        } else {
                            return array('error' => 1, 'message' => 'Document confirmation failed');

                        }
                        //return array('status' => true);
                        return array('error' => 0, 'message' => 'Document confirmed successfully');
                    } else {
                        return array('error' => 1, 'message' => 'Please complete sub item configurations<br/> Please add sub item/s before confirm this document.');
                    }


                } else {
                    return array('error' => 1, 'message' => 'Please complete you sub item configuration, fill all the mandatory fields!.');
                }

            }

        }
    }

    function save_stock_adjustment_detail_buyback()
    {
        if (!trim($this->input->post('stockAdjustmentDetailsAutoID') ?? '')) {
            $this->db->select('stockAdjustmentDetailsAutoID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_stockadjustmentdetails');
            $this->db->where('stockAdjustmentDetailsAutoID', trim($this->input->post('stockAdjustmentDetailsAutoID') ?? ''));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $order_detail = $this->db->get()->result_array();
            if (!empty($order_detail)) {
                return array('w', 'Stock Adjustment Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->trans_start();
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $projectID = trim($this->input->post('projectID') ?? '');

        $this->db->select('wareHouseAutoID');
        $this->db->where('stockAdjustmentAutoID', $this->input->post('stockAdjustmentAutoID'));
        $stockadjustmentMaster = $this->db->get('srp_erp_stockadjustmentmaster')->row_array();
        $segment = explode('|', trim($this->input->post('a_segment') ?? ''));
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
        $this->db->select('currentStock');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $prevItemMasterTotal = $this->db->get()->row_array();
        $previousWareHouseStock = $this->db->query("SELECT currentStock FROM srp_erp_warehouseitems WHERE wareHouseAutoID='{$stockadjustmentMaster['wareHouseAutoID']}' and itemAutoID='{$this->input->post('itemAutoID')}'")->row_array(); //get warehouse stock of the item by location
        $data['stockAdjustmentAutoID'] = trim($this->input->post('stockAdjustmentAutoID') ?? '');
        $data['itemSystemCode'] = trim($this->input->post('itemSystemCode') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['itemFinanceCategory'] = $item_data['subcategoryID'];
        $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
        $data['financeCategory'] = $item_data['financeCategory'];
        $data['itemCategory'] = $item_data['mainCategory'];
        if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
            $data['PLGLAutoID'] = $item_data['stockAdjustmentGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['stockAdjustmentSystemGLCode'];
            $data['PLGLCode'] = $item_data['stockAdjustmentGLCode'];
            $data['PLDescription'] = $item_data['stockAdjustmentDescription'];
            $data['PLType'] = $item_data['stockAdjustmentType'];
            $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
            $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['BLGLCode'] = $item_data['assteGLCode'];
            $data['BLDescription'] = $item_data['assteDescription'];
            $data['BLType'] = $item_data['assteType'];
        } elseif ($data['financeCategory'] == 2) {
            $data['PLGLAutoID'] = $item_data['costGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['PLGLCode'] = $item_data['costGLCode'];
            $data['PLDescription'] = $item_data['costDescription'];
            $data['PLType'] = $item_data['costType'];
            $data['BLGLAutoID'] = '';
            $data['BLSystemGLCode'] = '';
            $data['BLGLCode'] = '';
            $data['BLDescription'] = '';
            $data['BLType'] = '';
        }
        $data['previousStock'] = trim($prevItemMasterTotal['currentStock'] ?? '');
        $data['previousWac'] = trim($this->input->post('currentWac') ?? '');
        $data['previousWareHouseStock'] = $previousWareHouseStock["currentStock"];
        $data['currentWac'] = trim($this->input->post('adjustment_wac') ?? '');
        $data['currentWareHouseStock'] = trim($this->input->post('adjustment_Stock') ?? '');
        $data['adjustmentWac'] = (trim($this->input->post('adjustment_wac') ?? '') - trim($this->input->post('currentWac') ?? ''));
        $data['adjustmentWareHouseStock'] = (trim($this->input->post('adjustment_Stock') ?? '') - trim($this->input->post('currentWareHouseStock') ?? ''));
        $data['adjustmentStock'] = (trim($this->input->post('adjustment_Stock') ?? '') - trim($this->input->post('currentWareHouseStock') ?? ''));
        $data['currentStock'] = $data['adjustmentStock'] + $data['previousStock'];
        $previousTotal = ($data['previousStock'] * $data['previousWac']);
        $newTotal = ($data['currentStock'] * $data['currentWac']);
        $data['totalValue'] = ($data['currentStock'] * $data['currentWac']) - ($data['previousStock'] * $data['previousWac']);
        $data['comments'] = trim($this->input->post('comments') ?? '');

        $data['noOfItems'] = trim($this->input->post('noOfItems') ?? '');
        $data['grossQty'] = trim($this->input->post('grossQty') ?? '');
        $data['noOfUnits'] = trim($this->input->post('noOfUnits') ?? '');
        $data['deduction'] = trim($this->input->post('deductionvalue') ?? '');
        $data['bucketWeightID'] = trim($this->input->post('deductionedit') ?? '');

        if (trim($this->input->post('stockAdjustmentDetailsAutoID') ?? '')) {

            $this->db->where('stockAdjustmentDetailsAutoID', trim($this->input->post('stockAdjustmentDetailsAutoID') ?? ''));
            $this->db->update('srp_erp_stockadjustmentdetails', $data);

            /** item master Sub codes*/
            $detailsAutoID = $this->input->post('stockAdjustmentDetailsAutoID');

            /* 1---- delete all entries in the update process - item master sub temp */
            $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentDetailID' => $detailsAutoID, 'receivedDocumentID' => 'SA'));
            /* 2----  update all selected sub item list */
            if ($item_data['isSubitemExist'] == 1) {

                if ($data['previousStock'] < $data['currentStock']) {
                    /* Add Stock */
                    $qty = $data['adjustmentStock'];
                    $last_id = $detailsAutoID;
                    $documentAutoID = $data['stockAdjustmentAutoID'];

                    $subData['uom'] = $data['unitOfMeasure'];
                    $subData['uomID'] = $data['unitOfMeasureID'];
                    $subData['grv_detailID'] = $last_id;
                    $subData['warehouseAutoID'] = $stockadjustmentMaster['wareHouseAutoID'];
                    $this->add_sub_itemMaster_tmpTbl($qty, $data['itemAutoID'], $documentAutoID, $last_id, 'SA', $item_data['itemSystemCode'], $subData);
                }


            }

            /* 3---- update all selected values */

            $setData['isSold'] = null;
            $setData['soldDocumentID'] = null;
            $setData['soldDocumentAutoID'] = null;
            $setData['soldDocumentDetailID'] = null;

            $ware['soldDocumentID'] = 'SA';
            $ware['soldDocumentDetailID'] = $detailsAutoID;

            $this->db->update('srp_erp_itemmaster_sub', $setData, $ware);


            /** end item master Sub codes*/


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
            }
        } else {
            /* We are not using this method : there is a bulk insert method used to add the item..  */
            $this->db->insert('srp_erp_stockadjustmentdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_transfer_detail_multiple_buyback()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $stockTransferDetailsID = $this->input->post('stockTransferDetailsID');
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $transfer_QTY = $this->input->post('transfer_QTY');
        $projectID = $this->input->post('projectID');
        $a_segment = $this->input->post('a_segment');

        $noofitems = $this->input->post('noOfItems');
        $grossqty = $this->input->post('grossQty');
        $noofunits = $this->input->post('noOfUnits');
        $deduction = $this->input->post('deduction');
        $deductionvalue = $this->input->post('deductionvalue');


        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            /*         if (!$stockTransferDetailsID) {
                         $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
                         $this->db->from('srp_erp_stocktransferdetails');
                         $this->db->where('stockTransferAutoID', $stockTransferAutoID);
                         $this->db->where('itemAutoID', $itemAutoID);
                         $order_detail = $this->db->get()->row_array();

                         if (!empty($order_detail)) {
                             return array('error' => 1, 'w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                         }

                     }*/


            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);
            $data['stockTransferAutoID'] = $stockTransferAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];

            $data['noOfItems'] = $noofitems[$key];
            $data['grossQty'] = $grossqty[$key];
            $data['noOfUnits'] = $noofunits[$key];
            $data['deduction'] = $deductionvalue[$key];
            $data['bucketWeightID'] = $deduction[$key];

            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['transfer_QTY'] = $transfer_QTY[$key];
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];

            $this->db->select('to_wareHouseAutoID,to_wareHouseLocation,to_wareHouseDescription,from_wareHouseAutoID');
            $this->db->from('srp_erp_stocktransfermaster');
            $this->db->where('stockTransferAutoID', $stockTransferAutoID);
            $master = $this->db->get()->row_array();

            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['from_wareHouseAutoID']);
            $fromWarehouseGl = $this->db->get()->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $toWarehouseGl = $this->db->get()->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $master['to_wareHouseAutoID'],
                    'wareHouseLocation' => $master['to_wareHouseLocation'],
                    'wareHouseDescription' => $master['to_wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'barCodeNo' => $item_data['barcode'],
                    'salesPrice' => $item_data['companyLocalSellingPrice'],
                    'ActiveYN' => $item_data['isActive'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }

            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            if ($fromWarehouseGl['warehouseType'] == 2) {
                $data['fromWarehouseType'] = 2;
                $data['fromWarehouseWIPGLAutoID'] = $fromWarehouseGl['WIPGLAutoID'];
            }

            if ($toWarehouseGl['warehouseType'] == 2) {
                $data['toWarehouseType'] = 2;
                $data['toWarehouseWIPGLAutoID'] = $toWarehouseGl['WIPGLAutoID'];
            }

            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];
            $data['totalValue'] = ($data['currentlWacAmount'] * $data['transfer_QTY']);
            $this->db->insert('srp_erp_stocktransferdetails', $data);
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Stock Transfer Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 's', 'Stock Transfer Detail :  Saved Successfully.');
        }

    }

    function save_stock_transfer_detail_buyback()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        /*    if (!empty($this->input->post('stockTransferDetailsID'))) {
                $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
                $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
                $this->db->where('stockTransferDetailsID !=', trim($this->input->post('stockTransferDetailsID') ?? ''));
                $order_detail = $this->db->get()->row_array();

                if (!empty($order_detail)) {
                    return array('w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }*/
        $this->db->trans_start();
        $segment = explode('|', trim($this->input->post('a_segment') ?? ''));
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
        $projectID = trim($this->input->post('projectID') ?? '');
        $data['stockTransferAutoID'] = trim($this->input->post('stockTransferAutoID') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        $data['projectID'] = trim($this->input->post('projectID') ?? '');
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);

        $data['noOfItems'] = trim($this->input->post('noOfItems') ?? '');
        $data['grossQty'] = trim($this->input->post('grossQty') ?? '');
        $data['noOfUnits'] = trim($this->input->post('noOfUnits') ?? '');
        $data['deduction'] = trim($this->input->post('deductionvalue') ?? '');
        $data['bucketWeightID'] = trim($this->input->post('deductionedit') ?? '');

        $data['transfer_QTY'] = trim($this->input->post('transfer_QTY') ?? '');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['currentWareHouseStock'] = trim($this->input->post('currentWareHouseStockQty') ?? '');
        // $data['modifiedPCID']            = $this->common_data['current_pc'];
        // $data['modifiedUserID']          = $this->common_data['current_userID'];
        // $data['modifiedUserName']        = $this->common_data['current_user'];
        // $data['modifiedDateTime']        = $this->common_data['current_date'];

        $this->db->select('to_wareHouseAutoID,to_wareHouseLocation,to_wareHouseDescription');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
        $master = $this->db->get()->row_array();

        $this->db->select('itemAutoID');
        $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

        if (empty($warehouseitems)) {
            $data_arr = array(
                'wareHouseAutoID' => $master['to_wareHouseAutoID'],
                'wareHouseLocation' => $master['to_wareHouseLocation'],
                'wareHouseDescription' => $master['to_wareHouseDescription'],
                'itemAutoID' => $data['itemAutoID'],
                'barCodeNo' => $item_data['barcode'],
                'salesPrice' => $item_data['companyLocalSellingPrice'],
                'ActiveYN' => $item_data['isActive'],
                'itemSystemCode' => $data['itemSystemCode'],
                'itemDescription' => $data['itemDescription'],
                'unitOfMeasureID' => $data['defaultUOMID'],
                'unitOfMeasure' => $data['defaultUOM'],
                'currentStock' => 0,
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
            );
            $this->db->insert('srp_erp_warehouseitems', $data_arr);
        }

        if (trim($this->input->post('stockTransferDetailsID') ?? '')) {
            $this->db->where('stockTransferDetailsID', trim($this->input->post('stockTransferDetailsID') ?? ''));
            $this->db->update('srp_erp_stocktransferdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
            }
        } else {
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];
            $data['totalValue'] = ($data['currentlWacAmount'] * $data['transfer_QTY']);

            $this->db->insert('srp_erp_stocktransferdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_transfer_detail_edit_all_multiple_buyback()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $stockTransferDetailsID = $this->input->post('stockTransferDetailsID');
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $transfer_QTY = $this->input->post('transfer_QTY');
        $projectID = $this->input->post('projectID');
        $a_segment = $this->input->post('a_segment');

        $noOfItems = $this->input->post('noOfItems');
        $grossQty = $this->input->post('grossQty');
        $noOfUnits = $this->input->post('noOfUnits');
        $deduction = $this->input->post('deduction');

        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            if (!$stockTransferDetailsID[$key]) {
                $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('stockTransferAutoID', $stockTransferAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();

                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            } else {
                $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('stockTransferAutoID', $stockTransferAutoID);
                $this->db->where('stockTransferDetailsID !=', $stockTransferDetailsID[$key]);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();

                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);
            $data['stockTransferAutoID'] = $stockTransferAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];

            $data['noOfItems'] = $noOfItems[$key];
            $data['grossQty'] = $grossQty[$key];
            $data['noOfUnits'] = $noOfUnits[$key];
            $data['deduction'] = $deduction[$key];

            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['transfer_QTY'] = $transfer_QTY[$key];
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];

            $this->db->select('to_wareHouseAutoID,to_wareHouseLocation,to_wareHouseDescription,from_wareHouseAutoID');
            $this->db->from('srp_erp_stocktransfermaster');
            $this->db->where('stockTransferAutoID', $stockTransferAutoID);
            $master = $this->db->get()->row_array();

            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['from_wareHouseAutoID']);
            $fromWarehouseGl = $this->db->get()->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $toWarehouseGl = $this->db->get()->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $master['to_wareHouseAutoID'],
                    'wareHouseLocation' => $master['to_wareHouseLocation'],
                    'wareHouseDescription' => $master['to_wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'barCodeNo' => $item_data['barcode'],
                    'salesPrice' => $item_data['companyLocalSellingPrice'],
                    'ActiveYN' => $item_data['isActive'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }


            if ($fromWarehouseGl['warehouseType'] == 2) {
                $data['fromWarehouseType'] = 2;
                $data['fromWarehouseWIPGLAutoID'] = $fromWarehouseGl['WIPGLAutoID'];
            }

            if ($toWarehouseGl['warehouseType'] == 2) {
                $data['toWarehouseType'] = 2;
                $data['toWarehouseWIPGLAutoID'] = $toWarehouseGl['WIPGLAutoID'];
            }

            if (trim($stockTransferDetailsID[$key])) {
                $this->db->where('stockTransferDetailsID', trim($stockTransferDetailsID[$key]));
                $this->db->update('srp_erp_stocktransferdetails', $data);
                $this->db->trans_complete();
            } else {
                $data['itemFinanceCategory'] = $item_data['subcategoryID'];
                $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
                $data['financeCategory'] = $item_data['financeCategory'];
                $data['itemCategory'] = $item_data['mainCategory'];
                if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data['PLGLCode'] = $item_data['costGLCode'];
                    $data['PLDescription'] = $item_data['costDescription'];
                    $data['PLType'] = $item_data['costType'];
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                } elseif ($data['financeCategory'] == 2) {
                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data['PLGLCode'] = $item_data['costGLCode'];
                    $data['PLDescription'] = $item_data['costDescription'];
                    $data['PLType'] = $item_data['costType'];
                    $data['BLGLAutoID'] = '';
                    $data['BLSystemGLCode'] = '';
                    $data['BLGLCode'] = '';
                    $data['BLDescription'] = '';
                    $data['BLType'] = '';
                }
                $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
                $data['currentStock'] = $item_data['currentStock'];
                $data['totalValue'] = ($data['currentlWacAmount'] * $data['transfer_QTY']);

                $this->db->insert('srp_erp_stocktransferdetails', $data);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Stock Transfer Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 's', 'Stock Transfer Detail :  Saved Successfully.');
        }

    }

    function fetch_stock_return_detail_buyback()
    {
        $companyid = current_companyID();
        $this->db->select('detailTbl.totalAfterTax,detailTbl.requestedQty, srp_erp_salesreturndetails.*');
        $this->db->where('srp_erp_salesreturndetails.companyID', $companyid);
        $this->db->where('salesReturnDetailsID', trim($this->input->post('salesreturnid') ?? ''));
        $this->db->join('srp_erp_customerinvoicedetails detailTbl', 'detailTbl.invoiceAutoID = srp_erp_salesreturndetails.invoiceAutoID');
        $data = $this->db->get('srp_erp_salesreturndetails')->row_array();
        return $data;
    }

    function save_stock_return_detail_buyback()
    {
        $stockreturndetailid = $this->input->post('stockreturndetailid');
        $noOfItems = $this->input->post('noOfItems');
        $salesprice = $this->input->post('salesprice');
        $grossQty = $this->input->post('grossQty');
        $noOfUnits = $this->input->post('noOfUnits');
        $deductionedit = $this->input->post('deductionedit');
        $adjustment_Stock = $this->input->post('adjustment_Stock');
        $deductionvalue = $this->input->post('deductionvalue');
        $returnqty = $this->input->post('returnqty');


        $data['noOfItems'] = $noOfItems;
        $data['grossQty'] = $grossQty;
        $data['noOfUnits'] = $noOfUnits;
        $data['deduction'] = $deductionvalue;
        $data['return_Qty'] = $returnqty;
        $data['bucketWeightID'] = $deductionedit;
        $data['totalValue'] = $adjustment_Stock;

        $this->db->where('salesReturnDetailsID', $stockreturndetailid);
        $this->db->update('srp_erp_salesreturndetails', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Stock Return Detail Saved failed', $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Stock Return Detail Updated Successfully.');
        }

    }

    function save_sales_return_detail_items_buyback()
    {
        $invoiceAutoID = $this->input->post('invoiceDetailsAutoID');
        $salesReturnAutoID = $this->input->post('salesReturnAutoID');
        $companyID = current_companyID();
        $currentTime = format_date_mysql_datetime();

        $noofitems = $this->input->post('noofitems');
        $grossqty = $this->input->post('grossqty');
        $buckets = $this->input->post('buckets');
        $bucketweightID = $this->input->post('bucketweightID');
        $bucketweight = $this->input->post('bucketweight');
        $invoiceDetailsAutoID_edit = $this->input->post('invoiceDetailsAutoID');
        $invoiceIDs = join(',', $invoiceAutoID);
        $this->db->trans_start();
        $items_arr = array();
        $this->db->select('detailTbl.wareHouseAutoID,detailTbl.invoiceAutoID,itemAutoID,itemSystemCode,itemDescription,defaultUOM,unitOfMeasure,conversionRateUOM,requestedQty, expenseGLAutoID, expenseSystemGLCode, expenseGLCode, expenseGLDescription, expenseGLType, revenueGLAutoID, revenueGLCode, revenueSystemGLCode , revenueGLDescription , revenueGLType, assetGLAutoID,  assetGLCode,  assetSystemGLCode,  assetGLDescription, assetGLType, detailTbl.segmentID, detailTbl.segmentCode, detailTbl.transactionAmount, itemCategory, defaultUOMID, unitOfMeasureID, detailTbl.itemCategory, (detailTbl.unittransactionAmount- IFNULL(detailTbl.discountAmount, 0)) as unittransactionAmount, detailTbl.companyLocalWacAmount');
        $this->db->from('srp_erp_customerinvoicedetails as detailTbl');
        $this->db->join('srp_erp_customerinvoicemaster as masterTbl', 'masterTbl.invoiceAutoID = detailTbl.invoiceAutoID');
        $this->db->where('detailTbl.invoiceDetailsAutoID IN (' . $invoiceIDs . ')');
        $itemDetailList = $this->db->get()->result_array();

        //echo $this->db->last_query();


        $i = 0;
        $qty = $this->input->post('qty');


        foreach ($itemDetailList as $item) {

            $itemAutoID = $item['itemAutoID'];
            $wareHouseAutoID = $item['wareHouseAutoID'];
            $return_Qty = $qty[$i];

            /** item Master */
            $this->db->select('*');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $itemMaster = $this->db->get()->row_array();

            /** warehouse item Master */
            $this->db->select('*');
            $this->db->from('srp_erp_warehouseitems');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
            $itemWarehouseMaster = $this->db->get()->row_array();

            $data[$i]['salesReturnAutoID'] = $salesReturnAutoID;
            $data[$i]['invoiceAutoID'] = $item['invoiceAutoID'];
            $data[$i]['invoiceDetailID'] = $invoiceDetailsAutoID_edit[$i];
            $data[$i]['itemAutoID'] = $itemAutoID;
            $data[$i]['itemSystemCode'] = $item['itemSystemCode'];
            $data[$i]['itemDescription'] = $item['itemDescription'];
            $data[$i]['itemCategory'] = $item['itemCategory'];
            $data[$i]['unitOfMeasureID'] = $item['unitOfMeasureID'];
            $data[$i]['unitOfMeasure'] = $item['unitOfMeasure'];
            $data[$i]['defaultUOMID'] = $item['defaultUOMID'];
            $data[$i]['defaultUOM'] = $item['defaultUOM'];
            $data[$i]['conversionRateUOM'] = $item['conversionRateUOM'];
            $data[$i]['return_Qty'] = $return_Qty;


            $data[$i]['noOfItems'] = $noofitems[$i];
            $data[$i]['grossQty'] = $grossqty[$i];
            $data[$i]['noOfUnits'] = $buckets[$i];
            $data[$i]['deduction'] = $bucketweight[$i];
            $data[$i]['bucketWeightID'] = $bucketweightID[$i];

            $data[$i]['issued_Qty'] = $item['requestedQty'];
            $data[$i]['currentStock'] = $itemMaster['currentStock'];
            $data[$i]['currentWareHouseStock'] = $itemWarehouseMaster['currentStock'];
            $data[$i]['currentWacAmount'] = $item['companyLocalWacAmount'];
            $data[$i]['salesPrice'] = $item['unittransactionAmount'];
            $data[$i]['totalValue'] = $return_Qty * $item['unittransactionAmount'];
            $data[$i]['segmentID'] = $item['segmentID'];
            $data[$i]['segmentCode'] = $item['segmentCode'];
            $data[$i]['expenseGLAutoID'] = $item['expenseGLAutoID'];
            $data[$i]['expenseSystemGLCode'] = $item['expenseSystemGLCode'];
            $data[$i]['expenseGLCode'] = $item['expenseGLCode'];
            $data[$i]['expenseGLDescription'] = $item['expenseGLDescription'];
            $data[$i]['expenseGLType'] = $item['expenseGLType'];
            $data[$i]['revenueGLAutoID'] = $item['revenueGLAutoID'];
            $data[$i]['revenueGLCode'] = $item['revenueGLCode'];
            $data[$i]['revenueSystemGLCode'] = $item['revenueSystemGLCode'];
            $data[$i]['revenueGLDescription'] = $item['revenueGLDescription'];
            $data[$i]['revenueGLType'] = $item['revenueGLType'];
            $data[$i]['assetGLAutoID'] = $item['assetGLAutoID'];
            $data[$i]['assetGLCode'] = $item['assetGLCode'];
            $data[$i]['assetSystemGLCode'] = $item['assetSystemGLCode'];
            $data[$i]['assetGLDescription'] = $item['assetGLDescription'];
            $data[$i]['assetGLType'] = $item['assetGLType'];
            $data[$i]['comments'] = '';
            $data[$i]['companyID'] = $companyID;
            $data[$i]['timestamp'] = $currentTime;
            $i++;
        }


        $this->db->insert_batch('srp_erp_salesreturndetails', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Good Received note : Details Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Item Details Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function fetch_item_for_sales_return_buyback()
    {
        $companyID = current_companyID();
        //made changes to query - by mubashir (condition receivedQty > 0,grvDate less than stock returndate )
        $this->db->select('transactionCurrency,returnDate,wareHouseAutoID');
        $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID') ?? ''));
        $this->db->from('srp_erp_salesreturnmaster');
        $currency = $this->db->get()->row_array();

        $this->db->select('detailTbl.totalAfterTax,detailTbl.requestedQty as invoiceQty,detailTbl.unittransactionAmount,detailTbl.discountAmount,invoiceDetailsAutoID,mainTable.invoiceAutoID,invoiceCode,invoiceDate,detailTbl.itemAutoID,detailTbl.itemSystemCode, detailTbl.itemDescription, (detailTbl.requestedQty-SUM(IFNULL(srp_erp_salesreturndetails.return_Qty,0))) as requestedQty, (detailTbl.unittransactionAmount-detailTbl.discountAmount) as transactionAmount,transactionCurrencyDecimalPlaces,mainTable.transactionCurrency,detailTbl.unitOfMeasure');
        $this->db->from('srp_erp_customerinvoicemaster mainTable');
        $this->db->join('srp_erp_customerinvoicedetails detailTbl', 'detailTbl.invoiceAutoID = mainTable.invoiceAutoID');
        $this->db->join('srp_erp_salesreturndetails', 'mainTable.invoiceAutoID = srp_erp_salesreturndetails.invoiceAutoID AND detailTbl.itemAutoID = srp_erp_salesreturndetails.itemAutoID', "LEFT");
        $this->db->where('detailTbl.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        $this->db->where('mainTable.customerID', trim($this->input->post('customerID') ?? ''));
        //$this->db->where('mainTable.wareHouseLocation', trim($this->input->post('wareHouseLocation') ?? ''));
        $this->db->where('mainTable.transactionCurrency', $currency["transactionCurrency"]);
        $this->db->where('mainTable.invoiceDate <=', $currency["returnDate"]);
        $this->db->where('mainTable.approvedYN', 1);
//        $this->db->where('detailTbl.wareHouseAutoID', $currency['wareHouseAutoID']);
        $this->db->group_by('detailTbl.invoiceDetailsAutoID');
        $this->db->having('requestedQty >', 0);

        $r['data'] = $this->db->get()->result_array();
        $this->db->SELECT("weightAutoID,bucketWeight");
        $this->db->FROM('srp_erp_buyback_bucketweight');
        $this->db->WHERE('companyID', $companyID);
        $r['bucketweightdrop'] = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $r;
    }

    function check_item_not_approved_in_document()
    {
        $stockcountingID = $this->input->post('stockCountingAutoID');
        $documentID = $this->input->post('documentcode');
        $RVAutoID = $this->input->post('receiptVoucherAutoId');
        $CINVAutoID = $this->input->post('invoiceAutoID');
        $companyID = current_companyID();
        $where_warehouse = '';
        if($stockcountingID)
        {
        $stockcountingdet = $this->db->query("SELECT stockCountingType,wareHouseAutoID FROM srp_erp_stockcountingmaster WHERE companyID = $companyID AND stockCountingAutoID = $stockcountingID AND adjustmentType=0")->row_array();
        if(!empty($stockcountingdet))
        {
            $where_warehouse.='AND (wareHouseAutoID =  '.$stockcountingdet['wareHouseAutoID'].' OR adjustmentType=1)';
        }

        }
        $documentAutoID_filter_rv = '';
        $documentAutoID_filter_cinv = '';
        if($documentID == 'RV'){
            $documentAutoID_filter_rv = ' 	AND srp_erp_customerreceiptmaster.receiptVoucherAutoId !='.$RVAutoID.' ';
        }
        if($documentID == 'CINV'){ 
            $documentAutoID_filter_cinv = ' 	AND srp_erp_customerinvoicemaster.invoiceAutoID!='.$CINVAutoID.' ';
        }

        $companyID = current_companyID();
        $itemAutoID = $this->input->post('itemAutoID');
        $data['usedDocs'] = $this->db->query("SELECT
		a.*,
	    srp_erp_warehousemaster.wareHouseLocation as warehouse,
	    srp_erp_itemmaster.defaultUnitOfMeasure as Uom 
FROM
	(
SELECT
	documentID,
	srp_erp_stockadjustmentmaster.stockAdjustmentAutoID AS documentAutoID,
	stockAdjustmentCode AS documentCode,
	itemAutoID,
	itemDescription,
	referenceNo,
	stockAdjustmentDate as documentDate,
    srp_erp_stockadjustmentmaster.wareHouseAutoID as wareHouseID, 
    IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
    srp_erp_stockadjustmentdetails.defaultUOMID as  UOMID
FROM
	srp_erp_stockadjustmentmaster
	LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
WHERE
	companyID = $companyID 
	AND itemAutoID = $itemAutoID 
	$where_warehouse
	AND approvedYN != 1 
	
	UNION ALL
	
SELECT
	documentID,
	srp_erp_stockcountingmaster.stockCountingAutoID AS documentAutoID,
	stockCountingCode AS documentCode,
	itemAutoID,
	itemDescription,
	referenceNo,
	stockCountingDate as documentDate,
    srp_erp_stockcountingmaster.wareHouseAutoID as wareHouseID, 
    IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
    srp_erp_stockcountingdetails.defaultUOMID as  UOMID
FROM
	srp_erp_stockcountingmaster
	LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
WHERE
	companyID = $companyID 
	AND itemAutoID = $itemAutoID 
	$where_warehouse
	AND approvedYN != 1 
	
	UNION ALL
	
SELECT
	documentID,
	srp_erp_itemissuemaster.itemIssueAutoID AS documentAutoID,
	itemIssueCode AS documentCode,
	itemAutoID,
	itemDescription,
	issueRefNo as  referenceNo,
	issueDate as documentDate,
    srp_erp_itemissuemaster.wareHouseAutoID as wareHouseID, 
    IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock,
    srp_erp_itemissuedetails.defaultUOMID as  UOMID
FROM
	srp_erp_itemissuemaster
	LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
WHERE
	srp_erp_itemissuemaster.companyID = $companyID 
	AND itemAutoID = $itemAutoID 
	AND approvedYN != 1 
	
	UNION ALL
	
SELECT
	documentID,
	srp_erp_customerreceiptmaster.receiptVoucherAutoId AS documentAutoID,
	RVcode AS documentCode,
	itemAutoID,
	itemDescription,
	referanceNo,
	 RVdate as documentDate,
     srp_erp_customerreceiptdetail.wareHouseAutoID as wareHouseID, 
    ( requestedQty / conversionRateUOM ) AS stock,
    srp_erp_customerreceiptdetail.defaultUOMID as  UOMID
FROM
	srp_erp_customerreceiptmaster
	LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
WHERE
	srp_erp_customerreceiptmaster.companyID = $companyID 
	AND itemAutoID = $itemAutoID 
    $documentAutoID_filter_rv
	AND approvedYN != 1 
    GROUP BY
    documentAutoID,wareHouseID
	UNION ALL
	
SELECT
	documentID,
	srp_erp_customerinvoicemaster.invoiceAutoID AS documentAutoID,
	invoiceCode AS documentCode,
	itemAutoID,
	itemDescription,
	 referenceNo,
	 invoiceDate as documentDate,
     srp_erp_customerinvoicedetails.wareHouseAutoID as wareHouseID, 
    ( requestedQty / conversionRateUOM ) AS stock,
    srp_erp_customerinvoicedetails.defaultUOMID as  UOMID
FROM
	srp_erp_customerinvoicemaster
	LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
WHERE
	srp_erp_customerinvoicemaster.companyID = $companyID 
	AND itemAutoID = $itemAutoID 
    $documentAutoID_filter_cinv
	AND approvedYN != 1 
	GROUP BY
    srp_erp_customerinvoicemaster.invoiceAutoID,wareHouseID
	UNION ALL
	
SELECT
	documentID,
	srp_erp_deliveryorder.DOAutoID AS documentAutoID,
	DOCode AS documentCode,
	itemAutoID,
	itemDescription,
	referenceNo,
	 deliveredDate as documentDate,
     srp_erp_deliveryorderdetails.wareHouseAutoID as wareHouseID, 
    ( deliveredQty / conversionRateUOM ) AS stock,
    srp_erp_deliveryorderdetails.defaultUOMID as  UOMID
FROM
	srp_erp_deliveryorder
	LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
WHERE
	srp_erp_deliveryorder.companyID = $companyID 
	AND itemAutoID = $itemAutoID 
	AND approvedYN != 1 
	
	UNION ALL
	
SELECT
	documentID,
	srp_erp_stocktransfermaster.stockTransferAutoID AS documentAutoID,
	stockTransferCode AS documentCode,
	itemAutoID,
	itemDescription,
	referenceNo,
	 tranferDate as documentDate,
     srp_erp_stocktransfermaster.from_wareHouseAutoID  as wareHouseID, 
    ( transfer_QTY / conversionRateUOM ) AS stock,
    srp_erp_stocktransferdetails.defaultUOMID as  UOMID
FROM
	srp_erp_stocktransfermaster
	LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
WHERE
	srp_erp_stocktransfermaster.companyID = $companyID 
	AND itemAutoID = $itemAutoID 
	AND approvedYN != 1 
    ) a
    LEFT JOIN srp_erp_warehousemaster ON srp_erp_warehousemaster.wareHouseAutoID = a.wareHouseID
	LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = a.ItemAutoID
    WHERE 
	srp_erp_itemmaster.mainCategory = 'Inventory'
    ")->result_array();

        return $data;
    }
    function check_item_not_approved_in_document_new()
    {
        $stockcountingID = $this->input->post('stockCountingAutoID');
        $documentID = $this->input->post('documentcode');
        $RVAutoID = $this->input->post('receiptVoucherAutoId');
        $CINVAutoID = $this->input->post('invoiceAutoID');

        $MIDetAutoID = $this->input->post('itemIssueDetailID');
        $stockTransferDetID = $this->input->post('stockTransferDetailsID');
        $DODetID = $this->input->post('DODetailsAutoID');
        $CINVDetAutoID = $this->input->post('invoiceDetailsAutoID');

        $companyID = current_companyID();
        $where_warehouse = '';
        if($stockcountingID)
        {
            $stockcountingdet = $this->db->query("SELECT stockCountingType,wareHouseAutoID FROM srp_erp_stockcountingmaster WHERE companyID = $companyID AND stockCountingAutoID = $stockcountingID AND adjustmentType=0")->row_array();
            if(!empty($stockcountingdet))
            {
                $where_warehouse.='AND (wareHouseAutoID =  '.$stockcountingdet['wareHouseAutoID'].' OR adjustmentType=1)';
            }

        }
        $documentAutoID_filter_rv = '';
        $documentAutoID_filter_cinv = '';
        $documentAutoID_filter_mi ='';
        $documentAutoID_filter_st ='';
        $documentAutoID_filter_do ='';

        if($documentID == 'RV'){
            $documentAutoID_filter_rv = ' 	AND srp_erp_customerreceiptmaster.receiptVoucherAutoId !='.$RVAutoID.' ';
        }
        if($documentID == 'CINV'){
            $documentAutoID_filter_cinv = ' AND srp_erp_customerinvoicedetails.invoiceDetailsAutoID !='.$CINVDetAutoID.' ';
        }
        if($documentID == 'MI'){
            $documentAutoID_filter_mi = ' 	AND srp_erp_itemissuedetails.itemIssueDetailID !='.$MIDetAutoID.' ';
        }
        if($documentID == 'ST'){
            $documentAutoID_filter_st = ' 	AND srp_erp_stocktransferdetails.stockTransferDetailsID !='.$stockTransferDetID.' ';
        }
        if($documentID == 'DO'){
            $documentAutoID_filter_do = ' 	AND `srp_erp_deliveryorderdetails`.DODetailsAutoID !='.$DODetID.' ';
        }
        $companyID = current_companyID();
        $itemAutoID = $this->input->post('itemAutoID');
        $data['usedDocs'] = $this->db->query("SELECT
                a.*,
                srp_erp_warehousemaster.wareHouseLocation as warehouse,
                srp_erp_itemmaster.defaultUnitOfMeasure as Uom 
        FROM
            (
        SELECT
            documentID,
            srp_erp_stockadjustmentmaster.stockAdjustmentAutoID AS documentAutoID,
            stockAdjustmentCode AS documentCode,
            itemAutoID,
            itemDescription,
            referenceNo,
            stockAdjustmentDate as documentDate,
            srp_erp_stockadjustmentmaster.wareHouseAutoID as wareHouseID, 
            IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
            srp_erp_stockadjustmentdetails.defaultUOMID as  UOMID
        FROM
            srp_erp_stockadjustmentmaster
            LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
        WHERE
            companyID = $companyID 
            AND itemAutoID = $itemAutoID 
            $where_warehouse
            AND approvedYN != 1 
            
            UNION ALL
            
        SELECT
            documentID,
            srp_erp_stockcountingmaster.stockCountingAutoID AS documentAutoID,
            stockCountingCode AS documentCode,
            itemAutoID,
            itemDescription,
            referenceNo,
            stockCountingDate as documentDate,
            srp_erp_stockcountingmaster.wareHouseAutoID as wareHouseID, 
            IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
            srp_erp_stockcountingdetails.defaultUOMID as  UOMID
        FROM
            srp_erp_stockcountingmaster
            LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
        WHERE
            companyID = $companyID 
            AND itemAutoID = $itemAutoID 
            $where_warehouse
            AND approvedYN != 1 
            
            UNION ALL
            
        SELECT
            documentID,
            srp_erp_itemissuemaster.itemIssueAutoID AS documentAutoID,
            itemIssueCode AS documentCode,
            itemAutoID,
            itemDescription,
            issueRefNo as  referenceNo,
            issueDate as documentDate,
            srp_erp_itemissuemaster.wareHouseAutoID as wareHouseID, 
            IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock,
            srp_erp_itemissuedetails.defaultUOMID as  UOMID
        FROM
            srp_erp_itemissuemaster
            LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
        WHERE
            srp_erp_itemissuemaster.companyID = $companyID 
            $documentAutoID_filter_mi
            AND itemAutoID = $itemAutoID 
            AND approvedYN != 1 
            
            UNION ALL
            
        SELECT
            documentID,
            srp_erp_customerreceiptmaster.receiptVoucherAutoId AS documentAutoID,
            RVcode AS documentCode,
            itemAutoID,
            itemDescription,
            referanceNo,
             RVdate as documentDate,
             srp_erp_customerreceiptdetail.wareHouseAutoID as wareHouseID, 
            SUM( requestedQty / conversionRateUOM ) AS stock,
            srp_erp_customerreceiptdetail.defaultUOMID as  UOMID
        FROM
            srp_erp_customerreceiptmaster
            LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
        WHERE
            srp_erp_customerreceiptmaster.companyID = $companyID 
            AND itemAutoID = $itemAutoID 
            $documentAutoID_filter_rv
            AND approvedYN != 1 
            GROUP BY
            documentAutoID,wareHouseID
            UNION ALL
            
        SELECT
            documentID,
            srp_erp_customerinvoicemaster.invoiceAutoID AS documentAutoID,
            invoiceCode AS documentCode,
            itemAutoID,
            itemDescription,
             referenceNo,
             invoiceDate as documentDate,
             srp_erp_customerinvoicedetails.wareHouseAutoID as wareHouseID, 
            SUM( requestedQty / conversionRateUOM ) AS stock,
            srp_erp_customerinvoicedetails.defaultUOMID as  UOMID
        FROM
            srp_erp_customerinvoicemaster
            LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
        WHERE
            srp_erp_customerinvoicemaster.companyID = $companyID 
            AND itemAutoID = $itemAutoID 
            $documentAutoID_filter_cinv
            AND approvedYN != 1 
            GROUP BY
            srp_erp_customerinvoicemaster.invoiceAutoID,wareHouseID
            UNION ALL
            
        SELECT
            documentID,
            srp_erp_deliveryorder.DOAutoID AS documentAutoID,
            DOCode AS documentCode,
            itemAutoID,
            itemDescription,
            referenceNo,
             deliveredDate as documentDate,
             srp_erp_deliveryorderdetails.wareHouseAutoID as wareHouseID, 
            ( deliveredQty / conversionRateUOM ) AS stock,
            srp_erp_deliveryorderdetails.defaultUOMID as  UOMID
        FROM
            srp_erp_deliveryorder
            LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
        WHERE
            srp_erp_deliveryorder.companyID = $companyID 
            $documentAutoID_filter_do
            AND itemAutoID = $itemAutoID 
            AND approvedYN != 1 
            
            UNION ALL
            
        SELECT
            documentID,
            srp_erp_stocktransfermaster.stockTransferAutoID AS documentAutoID,
            stockTransferCode AS documentCode,
            itemAutoID,
            itemDescription,
            referenceNo,
             tranferDate as documentDate,
             srp_erp_stocktransfermaster.from_wareHouseAutoID  as wareHouseID, 
            ( transfer_QTY / conversionRateUOM ) AS stock,
            srp_erp_stocktransferdetails.defaultUOMID as  UOMID
        FROM
            srp_erp_stocktransfermaster
            LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
        WHERE
            srp_erp_stocktransfermaster.companyID = $companyID 
            $documentAutoID_filter_st
            AND itemAutoID = $itemAutoID 
            AND approvedYN != 1 
            ) a
            LEFT JOIN srp_erp_warehousemaster ON srp_erp_warehousemaster.wareHouseAutoID = a.wareHouseID
            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = a.ItemAutoID
            WHERE 
            srp_erp_itemmaster.mainCategory = 'Inventory'
            ")->result_array();

        return $data;
    }
    function check_item_not_approved_document_wise()
    {
        $companyID = current_companyID();
        $itemAutoID = $this->input->post('itemAutoID');
        $data['usedDocs'] = $this->db->query("SELECT
	* 
FROM
	(
SELECT
	documentID,
	srp_erp_stockadjustmentmaster.stockAdjustmentAutoID AS documentAutoID,
	stockAdjustmentCode AS documentCode,
	itemAutoID,
	itemDescription,
	referenceNo,
	stockAdjustmentDate as documentDate 
FROM
	srp_erp_stockadjustmentmaster
	LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
WHERE
	companyID = $companyID 
	AND itemAutoID = $itemAutoID 
	AND approvedYN != 1 
	
	UNION ALL
	
SELECT
	documentID,
	srp_erp_stockcountingmaster.stockCountingAutoID AS documentAutoID,
	stockCountingCode AS documentCode,
	itemAutoID,
	itemDescription,
	referenceNo,
	stockCountingDate as documentDate 
FROM
	srp_erp_stockcountingmaster
	LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
WHERE
	companyID = $companyID 
	AND itemAutoID = $itemAutoID 
	AND approvedYN != 1 
	) a")->result_array();

        return $data;
    }

    function load_item_received_history($currency, $items = null, $suppliers = null)
    {
        $companyID = current_companyID();
        $documentTypes = $this->input->post('documentID');
        $suppliers = $this->input->post('supplier');
        $supFilter = '';
        if ($suppliers != null) {
            $sup =  "'" . implode("', '", $suppliers) . "'";
            $supFilter = "AND supplierID IN($sup)";
        }
        $column_filter = $this->input->post('columSelectionDrop');
        $feildsra = array();
        $feilds1 = "";

        if (isset($column_filter)) {
            foreach ($column_filter as $val) {
                if ($val == "barcode" || $val == "partNo" ) {
                    $feildsra[]= 'srp_erp_itemmaster.' . $val;
                }
            }
            $feilds1 = join(',', $feildsra);
            if (!empty($feilds1)){
                $feilds1 = $feilds1. ",";
            }
        }
        $currencyExchange = 'companyLocalExchangeRate';
        if ($currency == 'Reporting') {
            $currencyExchange = 'companyReportingExchangeRate';
        }
        $doctypeGRV = "";
        $doctypeBSI = "";
        $doctypePV = "";
        $doctype_filter = "";
        if (!empty($documentTypes)) {

            $doctype_filter = "'" . implode("', '", $documentTypes) . "'";
            $doctypeGRV = " AND srp_erp_grvmaster.documentID IN ($doctype_filter)";
            $doctypeBSI = " AND paysuppliermaster.documentID IN ($doctype_filter)";
            $doctypePV = " AND paymentmaster.documentID IN ($doctype_filter)";
        }
        $itemFilter = '';
        if ($items != null) {
            $itemFilter = "AND srp_erp_itemmaster.itemAutoID IN(" . $items . ")";
        }
        $supplierFilter = '';
        if ($suppliers != null) {
            $sup =  "'" . implode("', '", $suppliers) . "'";
            $supplierFilter = "AND supplierAutoID IN($sup)";
        }

        $data['item_received_details'] = $this->db->query("
    SELECT
        $feilds1
    	srp_erp_grvmaster.grvAutoID AS documentprimaryID,
        srp_erp_grvmaster.grvPrimaryCode  AS documentSystemCode,
        srp_erp_grvmaster.documentID AS DocumentCode,
        srp_erp_grvdetails.ItemAutoID AS itemAutoID, 
        DATE_FORMAT(srp_erp_grvmaster.grvDate, '%d-%m-%Y') AS date,
        srp_erp_grvdetails.purchaseOrderCode AS poNo,
        srp_erp_grvdetails.itemSystemCode  AS itemCode,
        srp_erp_itemmaster.mainCategory AS itemCategory,
        srp_erp_itemmaster.itemName AS itemName,
        srp_erp_grvdetails.itemDescription AS description,
      TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((receivedQty / conversionRateUOM  )), 4 )))))) AS qty,
        srp_erp_grvdetails.receivedAmount / srp_erp_grvmaster.$currencyExchange as rate,
        srp_erp_grvdetails.receivedTotalAmount / srp_erp_grvmaster.$currencyExchange AS gross,
        srp_erp_grvmaster.$currencyExchange as currenctexchange,
         0 AS discount,
       srp_erp_grvmaster.supplierSystemCode AS supplierCode,
       srp_erp_grvmaster.supplierName AS supplierName,
       srp_erp_itemmaster.seconeryItemCode
       
    FROM 
        srp_erp_grvdetails 
        INNER JOIN  srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID=srp_erp_grvmaster.grvAutoID 
        LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID= srp_erp_grvdetails.ItemAutoID
    WHERE
            srp_erp_grvdetails.companyID=$companyID AND
            srp_erp_grvmaster.approvedYN=1 
            $supFilter
            AND srp_erp_grvdetails.itemAutoID IS NOT NULL 		
            $doctypeGRV $itemFilter
    UNION ALL
    SELECT
    $feilds1
	paysuppliermaster.InvoiceAutoID AS documentprimaryID,
	paysuppliermaster.bookingInvCode AS documentSystemCode,
	paysuppliermaster.documentID AS DocumentCode,
	srp_erp_paysupplierinvoicedetail.itemAutoID AS itemAutoID,
	DATE_FORMAT( paysuppliermaster.bookingDate, '%d-%m-%Y' ) AS date,
	srp_erp_paysupplierinvoicedetail.purchaseOrderCode AS poNo,
	srp_erp_paysupplierinvoicedetail.itemSystemCode AS itemCode,
	srp_erp_itemmaster.mainCategory AS itemCategory,
	srp_erp_itemmaster.itemName AS itemName,
	srp_erp_paysupplierinvoicedetail.itemDescription AS description,
	requestedQty / conversionRateUOMID AS qty,
	srp_erp_paysupplierinvoicedetail.unittransactionAmount / paysuppliermaster.companyLocalExchangeRate AS rate,
	srp_erp_paysupplierinvoicedetail.transactionAmount / paysuppliermaster.companyLocalExchangeRate AS gross,
	paysuppliermaster.companyLocalExchangeRate AS currenctexchange,
	(((( srp_erp_paysupplierinvoicedetail.transactionAmount )* IFNULL( generalDiscountPercentage, 0 ))/ 100 )+ IFNULL( generalDiscountAmount, 0 ) ) AS discount,
	Supplier.supplierAutoID,
	Supplier.supplierName,
	  srp_erp_itemmaster.seconeryItemCode
FROM
(SELECT supplierAutoID, supplierName FROM srp_erp_suppliermaster WHERE companyID = {$companyID} UNION  SELECT 0 AS supplierAutoID, 'Sundry' AS supplierName FROM srp_erp_suppliermaster WHERE companyID = {$companyID} GROUP BY supplierAutoID) Supplier 

LEFT JOIN (SELECT *,IFNULL(supplierID,0) as supplierIDsun FROM srp_erp_paysupplierinvoicemaster) paysuppliermaster ON paysuppliermaster.supplierIDsun = Supplier.supplierAutoID
LEFT JOIN srp_erp_paysupplierinvoicedetail on srp_erp_paysupplierinvoicedetail.InvoiceAutoID = paysuppliermaster.InvoiceAutoID
	LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.ItemAutoID 
WHERE
	srp_erp_paysupplierinvoicedetail.companyID = $companyID 
	AND paysuppliermaster.approvedYN = 1 
	AND srp_erp_paysupplierinvoicedetail.itemAutoID IS NOT NULL 
	$doctypeBSI
	$supplierFilter
	 $itemFilter
    UNION ALL
    SELECT 
    $feilds1
	paymentmaster.payVoucherAutoId AS documentprimaryID,
	paymentmaster.PVcode AS documentSystemCode,
	paymentmaster.documentID AS DocumentCode,
	srp_erp_paymentvoucherdetail.itemAutoID AS itemAutoID,
	DATE_FORMAT( paymentmaster.PVdate, '%d-%m-%Y' ) AS date,
	srp_erp_paymentvoucherdetail.POCode AS poNo,
	srp_erp_paymentvoucherdetail.itemSystemCode AS itemCode,
	srp_erp_itemmaster.mainCategory AS itemCategory,
	srp_erp_itemmaster.itemName AS itemName,
	srp_erp_paymentvoucherdetail.itemDescription AS description,
	requestedQty / conversionRateUOM AS qty,
	srp_erp_paymentvoucherdetail.unittransactionAmount / srp_erp_paymentvoucherdetail.companyLocalExchangeRate AS rate,
	srp_erp_paymentvoucherdetail.transactionAmount / srp_erp_paymentvoucherdetail.companyLocalExchangeRate AS gross,
	srp_erp_paymentvoucherdetail.companyLocalExchangeRate AS currenctexchange,
	srp_erp_paymentvoucherdetail.discountAmount AS discount,
	Supplier.supplierAutoID,
	Supplier.supplierName,
	  srp_erp_itemmaster.seconeryItemCode
FROM
	(SELECT supplierAutoID, supplierName FROM srp_erp_suppliermaster WHERE companyID = {$companyID} UNION  SELECT 0 AS supplierAutoID, 'Sundry' AS supplierName FROM srp_erp_suppliermaster WHERE companyID = {$companyID} GROUP BY supplierAutoID) Supplier 
	
LEFT JOIN (SELECT *,IFNULL(partyID,0) as supplierID FROM srp_erp_paymentvouchermaster where companyID ={$companyID}) paymentmaster ON paymentmaster.supplierID = Supplier.supplierAutoID
LEFT JOIN srp_erp_paymentvoucherdetail on 	srp_erp_paymentvoucherdetail.payVoucherAutoId = paymentmaster.payVoucherAutoId
LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.ItemAutoID 

WHERE
	srp_erp_paymentvoucherdetail.companyID = $companyID 
	AND paymentmaster.approvedYN = 1 
    AND srp_erp_paymentvoucherdetail.itemAutoID IS NOT NULL 
	AND srp_erp_itemmaster.mainCategory = 'Inventory'
	$doctypePV 
	$supplierFilter
	$itemFilter ")->result_array();

        return $data;
    }

    function close_material_request()
    {
        $this->db->trans_start();
        $system_code = trim($this->input->post('mrAutoID') ?? '');
        $date_format_policy = date_format_policy();
        $docdate = $this->input->post('closedDate');
        $closeddate = input_format_date($docdate, $date_format_policy);

        $data['closedYN'] = 1;
        $data['closedDate'] = $closeddate;
        $data['closedBy'] = $this->common_data['current_user'];
        $data['closedReason'] = trim($this->input->post('comments') ?? '');
        $data['approvedYN'] = 5;
        $data['approvedbyEmpID'] = $this->common_data['current_userID'];
        $data['approvedbyEmpName'] = $this->common_data['current_user'];
        $data['approvedDate'] = $this->common_data['current_date'];

        $this->db->where('mrAutoID', trim($system_code));
        $this->db->update('srp_erp_materialrequest', $data);
        $this->session->set_flashdata('s', 'Document Closed Successfully.');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function fetch_details_foc_report()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $item = $this->input->post('items');
        $documentID = $this->input->post('documentID');
        $currency = $this->input->post('currency');
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $column_filter = $this->input->post('columSelectionDrop');
        $feildsra = array();
        $feilds1 = "";
        if (isset($column_filter)) {
            foreach ($column_filter as $val) {
                if ($val == "barcode" || $val == "partNo" ) {
                    $feildsra[]= 'srp_erp_itemmaster.' . $val;
                }
            }
            $feilds1 = join(',', $feildsra);
            if (!empty($feilds1)){
                $feilds1 = $feilds1. ",";
            }
        }
            $fields = "";
        if ($currency == 'transaction') {
            $fields .= 'srp_erp_itemledger.transactionCurrency, srp_erp_itemledger.transactionCurrencyDecimalPlaces, srp_erp_itemledger.transactionAmount, ';
        } else if ($currency == 'companyLocal') {
            $fields .= 'srp_erp_itemledger.companyLocalCurrency, srp_erp_itemledger.companyLocalCurrencyDecimalPlaces, srp_erp_itemledger.companyLocalAmount, ';
        } else if ($currency == 'companyReporting') {
            $fields .= 'srp_erp_itemledger.companyReportingCurrency, srp_erp_itemledger.companyReportingCurrencyDecimalPlaces, srp_erp_itemledger.companyReportingAmount, ';
        }

        $date = " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 00:00:00')";

        $sql = $this->db->query("SELECT $feilds1 $fields
	srp_erp_itemmaster.itemAutoID,
	srp_erp_itemledger.documentID,
	documentAutoID,
	documentDate,
	srp_erp_itemmaster.itemSystemCode,
	srp_erp_itemmaster.seconeryItemCode,
	srp_erp_itemmaster.itemDescription,
	documentSystemCode,
	transactionUOM,
	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((transactionQTY)), 4 )))))) AS transactionQTY
FROM
	srp_erp_itemledger 
	JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID
WHERE
	srp_erp_itemledger.companyID = {$companyID} 
	AND srp_erp_itemledger.salesPrice = 0 
	AND srp_erp_itemmaster.itemAutoID IN (" . join(',', $item) . ") 
	AND srp_erp_itemledger.documentID IN (" . join(',', $documentID) . ") {$date}")->result_array();

//        echo '<pre>'; print_r($sql);
//        echo $this->db->last_query();
        return $sql;
    }

    function update_stock_minus_qty()
    {
        $companyID = current_companyID();
        $stockAdjustmentDetailsAutoID = $this->input->post('stockAdjustmentDetailsAutoID');
        $stock = $this->input->post('stock');
        $currentWarehouseItem = $this->input->post('currentWarehouseItem');

        foreach ($stockAdjustmentDetailsAutoID as $key => $id) {
            $details = $this->db->query("SELECT * FROM srp_erp_stockadjustmentdetails WHERE stockAdjustmentDetailsAutoID = {$id}")->row_array();

            $ItemCurrent = $this->db->query("SELECT IFNULL( SUM(transactionQTY/convertionRate),0) AS currentStock 
                FROM srp_erp_itemledger WHERE companyID = {$companyID} AND itemAutoID = {$details['itemAutoID']}")->row('currentStock');

            $data['updatedPreviousStock'] = $details['previousStock'];
            $data['updatedPreviousWareHouseStock'] = $details['previousWareHouseStock'];
            $data['updatedCurrentStock'] = $details['currentStock'];
            $data['previousStock'] = $ItemCurrent; // global Qty
            $data['previousWareHouseStock'] = $currentWarehouseItem[$key];
            $data['adjustmentStock'] = $stock[$key] - $currentWarehouseItem[$key];
            $data['currentStock'] = $ItemCurrent + $data['adjustmentStock'];

            $data['currentWareHouseStock'] = $stock[$key];

            $data['adjustmentWareHouseStock'] = $stock[$key] - $currentWarehouseItem[$key];
            $data['totalValue'] = $data['adjustmentStock'] * $details['currentWac'];
            $data['totalValue'] = ($data['currentStock'] * $details['currentWac']) - ($data['previousStock'] * $details['previousWac']);

            $this->db->where('stockAdjustmentDetailsAutoID', $id);
            $this->db->update('srp_erp_stockadjustmentdetails', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Stock Adjustment Details Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Stock Adjustment Detail Updated Successfully.');
        }
    }

    function load_sales_movement_analysis_report()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $item = $this->input->post('item');
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        //currency config
        $currency = $this->input->post('currency');
        $currencyExchange = 'companyLocalExchangeRate';
        if ($currency == 'Reporting') {
            $currencyExchange = 'companyReportingExchangeRate';
        }

        //location filter
        $location = $this->input->post('location');
        if(!empty($location)){
            $location_string = implode(",",$location);
            $warehouse_filter = "AND srp_erp_itemledger.wareHouseAutoID IN ($location_string)";
        }else{
            $warehouse_filter = "AND srp_erp_itemledger.wareHouseAutoID=0";
        }

        $result = $this->db->query("SELECT
    ifnull( customerID, 0 ) as customerID,
    ifnull(customerName,'Sundry') AS customerName,
    sum(
        (
            transactionQTY / convertionRate
        )
    ) *- 1 AS transctionQty,
      ifnull(
        (sum(
            srp_erp_itemmaster.companyLocalWacAmount
        ))/srp_erp_itemledger.$currencyExchange,
        0
    )  AS basicRate,
    ifnull(
        (sum(
            (
                transactionQTY / convertionRate
            ) * salesPrice
        ))/srp_erp_itemledger.$currencyExchange,
        0
    ) *- 1 AS salesAmount,
    (
        ((
            ifnull(
                sum(
                    (
                        transactionQTY / convertionRate
                    ) * salesPrice
                ),
                0
            ) *- 1
        ) / (
            sum(
                (
                    transactionQTY / convertionRate
                )
            ) *- 1
        ))/srp_erp_itemledger.$currencyExchange
    ) AS avgprice
FROM
    srp_erp_itemledger
LEFT JOIN (
    SELECT
        salestranstable.*, srp_erp_customermaster.customerAutoID,
        srp_erp_customermaster.customerName
    FROM
        (
            SELECT
                invoiceAutoID,
                documentID,
                customerID
            FROM
                srp_erp_customerinvoicemaster
            WHERE
                approvedYN = 1
            AND companyID = $companyID
            UNION
                SELECT
                    receiptVoucherAutoId AS invoiceAutoID,
                    documentID,
                    customerID
                FROM
                    srp_erp_customerreceiptmaster
                WHERE
                    companyID = $companyID
                AND approvedYN = 1
                UNION
                    SELECT
                        invoiceID,
                        documentCode,
                        customerID
                    FROM
                        srp_erp_pos_invoice
                    WHERE
                        companyID = $companyID
                    AND isVoid != 1
                    AND isCreditSales != 1
                    UNION
                        SELECT
                            salesReturnAutoID as invoiceID,
              documentID,
              customerID
                        FROM
                            srp_erp_salesreturnmaster
             where companyID=$companyID and approvedYN=1
           UNION
            select 
                salesReturnID as invoiceID,
                documentCode as documentID,
                customerID 
            from srp_erp_pos_salesreturn
             where companyID=$companyID
        ) salestranstable
    LEFT JOIN srp_erp_customermaster ON salestranstable.customerID = srp_erp_customermaster.customerAutoID
) salestransmastertable ON srp_erp_itemledger.documentAutoID = salestransmastertable.invoiceautoID
AND srp_erp_itemledger.documentID = salestransmastertable.documentID
JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID=srp_erp_itemledger.itemAutoID
WHERE
    srp_erp_itemledger.itemAutoID = $item
AND (
    documentDate BETWEEN '$datefromconvert'
    AND '$datetoconvert'
)
AND srp_erp_itemledger.documentID IN ('RV', 'CINV', 'POS')
$warehouse_filter
GROUP BY
    ifnull( customerID, 0 )")->result_array();

        //var_dump($this->db->last_query());exit;
        return $result;
    }

    function get_item_wac_amount($item_id,$currency){
        //currency config
        if ($currency == 'Reporting') {
            $wac_amount_column = 'companyReportingWacAmount';
            $company_currency = 'companyReportingCurrency';
        }else{
            $wac_amount_column = 'companyLocalWacAmount';
            $company_currency = 'companyLocalCurrency';
        }
        $row = $this->db->query("select $wac_amount_column,$company_currency from srp_erp_itemmaster where itemAutoID=$item_id")->row();
        return $row->$wac_amount_column.' '.$row->$company_currency;
    }

    function load_purchase_movement_analysis_report()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $item = $this->input->post('item');
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        //currency config
        $currency = $this->input->post('currency');
        $currencyExchange = 'companyLocalExchangeRate';
        if ($currency == 'Reporting') {
            $currencyExchange = 'companyReportingExchangeRate';
        }

        //location filter
        $location = $this->input->post('location');
        if(!empty($location)){
            $location_string = implode(",",$location);
            $warehouse_filter = "AND srp_erp_itemledger.wareHouseAutoID IN ($location_string)";
        }else{
            $warehouse_filter = "AND srp_erp_itemledger.wareHouseAutoID=0";
        }

//        $date = " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 00:00:00')";
//        $grvDate = " AND ( grvDate >= '" . $datefromconvert . " 00:00:00' AND grvDate <= '" . $datetoconvert . " 00:00:00')";
//        $bookingDate = " AND ( bookingDate >= '" . $datefromconvert . " 00:00:00' AND bookingDate <= '" . $datetoconvert . " 00:00:00')";
//        $itemFilter = " srp_erp_itemmaster.itemAutoID IN (" . join(',', $items) . ")";

        $result = $this->db->query("SELECT
    supplierautoID,
    supplierName,
    sum(
        (
            transactionQTY / convertionRate
        )
    ) AS transctionQty,
        ifnull(
        (sum(
            srp_erp_itemmaster.companyLocalWacAmount
        ))/srp_erp_itemledger.$currencyExchange,
        0
    )  AS basicRate,
    ifnull(
        (sum(
            transactionAmount
        ))/srp_erp_itemledger.$currencyExchange,
        0
    )  AS purchaseamount,
    (
         (sum(
            transactionAmount
        )/ (
            sum(
                (
                    transactionQTY / convertionRate
                )
            ) 
        ))/srp_erp_itemledger.$currencyExchange
    ) AS avgprice
FROM
    srp_erp_itemledger
JOIN (
    SELECT
        purchasetranstable.*, srp_erp_suppliermaster.supplierAutoID,
        srp_erp_suppliermaster.supplierName
    FROM
        (
            SELECT
                InvoiceAutoID,
                documentID,
                supplierID
            FROM
                srp_erp_paysupplierinvoicemaster
            WHERE
                approvedYN = 1
            AND companyID = $companyID
            UNION
                SELECT
                    payVoucherAutoId AS invoiceAutoID,
                    documentID,
                    partyID as supplierID
                FROM
                    srp_erp_paymentvouchermaster
                WHERE
                    companyID = $companyID
                AND approvedYN = 1
                UNION 
                                Select 
                                grvAutoID as invoiceAutoID,
                documentID,
                supplierID 
                                from 
                                srp_erp_grvmaster 
                        where companyID=$companyID and approvedYN=1
                    UNION
                        SELECT
                            stockReturnAutoID as invoiceID,
              documentID,
              supplierID
                        FROM
                            srp_erp_stockreturnmaster 
             where companyID=$companyID and approvedYN=1
           UNION
            select 
                salesReturnID as invoiceID,
                documentCode as documentID,
                customerID 
            from srp_erp_pos_salesreturn
             where companyID=$companyID
        ) purchasetranstable
    JOIN srp_erp_suppliermaster ON purchasetranstable.supplierID = srp_erp_suppliermaster.supplierAutoID
) purchasetransmastertable ON srp_erp_itemledger.documentAutoID = purchasetransmastertable.invoiceautoID
AND srp_erp_itemledger.documentID = purchasetransmastertable.documentID
JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID=srp_erp_itemledger.itemAutoID
WHERE
    srp_erp_itemledger.itemAutoID = $item
AND (
    documentDate BETWEEN '$datefromconvert'
    AND '$datetoconvert'
)
AND srp_erp_itemledger.documentID IN ('BSI', 'PV', 'GRV','PR')
$warehouse_filter
GROUP BY
    supplierID")->result_array();

//        echo '<pre>'; print_r($sql);
        return $result;
    }

    function get_currency(){
        $currency = $this->input->post('currency');
        $currencyName = 'company_default_currency';
        if ($currency == 'Reporting') {
            $currencyName = 'company_reporting_currency';
        }
        $com_id = current_companyID();
        return $currency = $this->db->query("select $currencyName from srp_erp_company where company_id=$com_id")->row()->$currencyName;
    }

    function load_transfers_movement_analysis_report()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $item = $this->input->post('item');
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        //currency config
        $currency = $this->input->post('currency');
        $currencyExchange = 'companyLocalExchangeRate';
        if ($currency == 'Reporting') {
            $currencyExchange = 'companyReportingExchangeRate';
        }

        //location filter
        $location = $this->input->post('location');
        if(!empty($location)){
            $location_string = implode(",",$location);
            $warehouse_filter = "AND srp_erp_itemledger.wareHouseAutoID IN ($location_string)";
        }else{
            $warehouse_filter = "AND srp_erp_itemledger.wareHouseAutoID=0";
        }

//        $date = " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 00:00:00')";
//        $grvDate = " AND ( grvDate >= '" . $datefromconvert . " 00:00:00' AND grvDate <= '" . $datetoconvert . " 00:00:00')";
//        $bookingDate = " AND ( bookingDate >= '" . $datefromconvert . " 00:00:00' AND bookingDate <= '" . $datetoconvert . " 00:00:00')";
//        $itemFilter = " srp_erp_itemmaster.itemAutoID IN (" . join(',', $items) . ")";

        $result = $this->db->query("SELECT
   srp_erp_itemledger.documentID,
    sum(
        (
            transactionQTY / convertionRate
        )
    ) AS transctionQty,
    ifnull(
        (sum(
            srp_erp_itemmaster.companyLocalWacAmount
        ))/srp_erp_itemledger.$currencyExchange,
        0
    )  AS basicRate,
    ifnull(
        (sum(
            transactionAmount
        ))/srp_erp_itemledger.$currencyExchange,
        0
    )  AS purchaseamount,
    (
         (sum(
            transactionAmount
        )/ (
            sum(
                (
                    transactionQTY / convertionRate
                )
            )
        ))/srp_erp_itemledger.$currencyExchange
    ) AS avgprice
FROM
    srp_erp_itemledger
JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID=srp_erp_itemledger.itemAutoID
WHERE
    srp_erp_itemledger.itemAutoID = $item
AND (
    documentDate BETWEEN '$datefromconvert'
    AND '$datetoconvert'
)
AND srp_erp_itemledger.documentID IN ('SA', 'SCNT', 'MI')
$warehouse_filter
GROUP BY
    srp_erp_itemledger.documentID")->result_array();

       //var_dump($this->db->last_query());exit;
        return $result;
    }

    function movement_analysis_item_details(){
        $item_id = $this->input->post("item",true);
        $result = $this->db->query("select * from srp_erp_itemmaster 
join srp_erp_unit_of_measure on srp_erp_unit_of_measure.UnitID=srp_erp_itemmaster.defaultUnitOfMeasureID
where itemAutoID=$item_id ")->row();
        return $result;
    }

    function fetch_details_item_movement_report()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $items = $this->input->post('items');
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $column_filter = $this->input->post('columSelectionDrop');
        $feildsra = array();
        $feilds1 = "";
        if (isset($column_filter)) {
            foreach ($column_filter as $val) {
                if ($val == "barcode" || $val == "partNo" ) {
                    $feildsra[]= 'srp_erp_itemmaster.' . $val;
                }
            }
            $feilds1 = join(',', $feildsra);
            if (!empty($feilds1)){
                $feilds1 = $feilds1. ",";
            }
        }

        $warehouseID = $this->input->post('warehouseID');
        if(!empty($warehouseID)){

            $warehouse_list = implode(',',$warehouseID);
            $warehouse_filter = "AND srp_erp_itemledger.wareHouseAutoID IN ({$warehouse_list})";
            
        }else{
            $warehouse_filter = '';
        }
        

        $date = " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 00:00:00')";
        $grvDate = " AND ( grvDate >= '" . $datefromconvert . " 00:00:00' AND grvDate <= '" . $datetoconvert . " 00:00:00')";
        $bookingDate = " AND ( bookingDate >= '" . $datefromconvert . " 00:00:00' AND bookingDate <= '" . $datetoconvert . " 00:00:00')";
        $itemFilter = " srp_erp_itemmaster.itemAutoID IN (" . join(',', $items) . ")";
        
        $sql = $this->db->query("SELECT
                $feilds1
                srp_erp_itemmaster.itemAutoID,
                itemSystemCode,
                seconeryItemCode,
                itemDescription,
                defaultUnitOfMeasure,
                TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((openingBalance.currentStock)), 4 )))))) AS openingBalance,
                TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((itempurchaseGRV.currentStock)), 4 )))))) AS GRV,
                TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((itempurchaseBSI.currentStock)), 4 )))))) AS BSI,
                TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((itempurchasePV.currentStock)), 4 )))))) AS PV,
                TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((itempurchaseSALES.currentStock)), 4 )))))) AS SALES,
                TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((itempurchaseSLR.currentStock)), 4 )))))) AS SLR,
                TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((adjustment.currentStock)), 4 )))))) AS adjustmentStock
                  
 
            
    FROM srp_erp_itemmaster
	LEFT JOIN ( SELECT itemAutoID, IFNULL( SUM( transactionQTY / convertionRate ), 0 ) AS currentStock FROM srp_erp_itemledger WHERE documentDate < '{$datefromconvert}' {$warehouse_filter} GROUP BY itemAutoID ) openingBalance ON openingBalance.ItemAutoID = srp_erp_itemmaster.itemAutoID
	LEFT JOIN (
	    SELECT itemAutoID, IFNULL( SUM( transactionQTY / convertionRate ), 0 ) AS currentStock FROM srp_erp_itemledger WHERE documentID = 'PV' {$date} {$warehouse_filter} GROUP BY itemAutoID 
	) itempurchasePV ON itempurchasePV.ItemAutoID = srp_erp_itemmaster.itemAutoID
	LEFT JOIN (
        SELECT srp_erp_itemledger.itemAutoID, IFNULL( SUM( transactionQTY / convertionRate ), 0 ) AS currentStock 
        FROM srp_erp_itemledger
            LEFT JOIN (
                SELECT IFNULL( SUM( receivedQty / conversionRateUOM ), 0 ) AS POQty, itemAutoID 
                FROM srp_erp_grvdetails
                    JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID 
                WHERE approvedYN = 1 AND srp_erp_grvdetails.companyID = {$companyID} {$grvDate} AND purchaseOrderMastertID > 0
                GROUP BY itemAutoID 
            ) grvDet ON grvDet.ItemAutoID = srp_erp_itemledger.itemAutoID 
        WHERE documentID = 'GRV' {$date} {$warehouse_filter}
        GROUP BY srp_erp_itemledger.itemAutoID 
	) itempurchaseGRV ON itempurchaseGRV.ItemAutoID = srp_erp_itemmaster.itemAutoID
	LEFT JOIN (
        SELECT srp_erp_itemledger.itemAutoID, IFNULL( SUM( transactionQTY / convertionRate ), 0 ) AS currentStock 
        FROM srp_erp_itemledger
            LEFT JOIN (
                SELECT IFNULL( SUM( requestedQty / conversionRateUOMID ), 0 ) AS POQty, itemAutoID 
                FROM srp_erp_paysupplierinvoicedetail
                    JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID 
                WHERE approvedYN = 1 AND srp_erp_paysupplierinvoicedetail.companyID = {$companyID} {$bookingDate} AND purchaseOrderMastertID > 0 
                GROUP BY itemAutoID 
            ) bsiQty ON bsiQty.ItemAutoID = srp_erp_itemledger.itemAutoID 
        WHERE documentID = 'BSI' {$date} {$warehouse_filter}
        GROUP BY srp_erp_itemledger.itemAutoID 
	) itempurchaseBSI ON itempurchaseBSI.ItemAutoID = srp_erp_itemmaster.itemAutoID
	LEFT JOIN (
	        SELECT itemAutoID, IFNULL( SUM( transactionQTY / convertionRate ), 0 ) AS currentStock FROM srp_erp_itemledger WHERE documentID IN ( 'SLR', 'RET' ) {$date} {$warehouse_filter} GROUP BY itemAutoID 
	) itempurchaseSLR ON itempurchaseSLR.ItemAutoID = srp_erp_itemmaster.itemAutoID
	LEFT JOIN (
	        SELECT itemAutoID, IFNULL( SUM( transactionQTY / convertionRate ), 0 ) AS currentStock FROM srp_erp_itemledger WHERE documentID IN ( 'CINV', 'RV', 'POS' ) {$date} {$warehouse_filter} GROUP BY itemAutoID 
	) itempurchaseSALES ON itempurchaseSALES.ItemAutoID = srp_erp_itemmaster.itemAutoID
	LEFT JOIN (
	        SELECT itemAutoID, IFNULL( SUM( transactionQTY / convertionRate ), 0 ) AS currentStock FROM srp_erp_itemledger WHERE documentID IN ( 'SA', 'SCNT') {$date} {$warehouse_filter} GROUP BY itemAutoID 
	) adjustment ON adjustment.ItemAutoID = srp_erp_itemmaster.itemAutoID 
	WHERE {$itemFilter}
GROUP BY srp_erp_itemmaster.itemAutoID
	")->result_array();

       //echo '<pre>'; print_r($this->db->last_query());exit;
        return $sql;
    }

    function check_mfq_warehouse() 
    {
        $companyID = current_companyID();
        $warehouseAutoID = $this->input->post('warehouseAutoID');
        if(!empty($warehouseAutoID)){
            $mfqWarehouseAutoID = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster WHERE warehouseAutoID = {$warehouseAutoID} AND companyID = {$companyID}")->row('mfqWarehouseAutoID');     
        }
        $data['status'] = 0;
        $data['mfqWarehouseAutoID'] = '';
        if($mfqWarehouseAutoID) {

            $this->db->select('workProcessID, documentCode');
            $this->db->from('srp_erp_mfq_job job');
            //$this->db->join('srp_erp_mfq_estimatedetail estd', 'estd.estimateDetailID = job.estimateDetailID');
           // $this->db->join('srp_erp_mfq_estimatemaster estm', 'estd.estimateMasterID = estm.estimateMasterID');
            $this->db->where('job.companyID', $companyID);
            $this->db->where('job.closedYN != 1');
            $this->db->where('job.isSaved', 1);
            $this->db->where('job.mfqWarehouseAutoID', $mfqWarehouseAutoID);
            $result = $this->db->get()->result_array();
            $data_arr = array('' => 'Select Job Number');
            if (isset($result)) {
                foreach ($result as $row) {
                    $data_arr[trim($row['workProcessID'] ?? '')] = trim($row['documentCode'] ?? '');
                }
            }
            $data['dropdown'] = form_dropdown('jobID', $data_arr, '', 'class="form-control select2" id="jobID"');
            $data['status'] = 1;
            $data['mfqWarehouseAutoID'] = $mfqWarehouseAutoID;

            return $data;
        }else{

            $this->db->where('wareHouseAutoID',$warehouseAutoID);
            $details = $this->db->from('srp_erp_warehousemaster')->get()->row_array();

            if($details && $details['warehouseType'] == 4){
                $data['status'] = 3;
                $data['consegmentAutoID'] = $warehouseAutoID;
            }

        }



        return $data;
    }

    function updateJobQty($jobID, $system_code, $documentID)
    {
        $this->db->trans_start();
        $itemIDs = array();
        $LanguagePolicy = getPolicyValues('LNG', 'All');

        $this->db->where('jobID', $jobID);
        $this->db->where('status', 0);
        $this->db->order_by('workProcessFlowID', 'asc');
        $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');

        if(empty($templateDetailID)) {
            $this->db->where('jobID', $jobID);
            $this->db->order_by('workProcessFlowID', 'desc');
            $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');
        }
        $this->db->select("DISTINCT(srp_erp_mfq_itemmaster.itemAutoID) AS itemAutoID,srp_erp_mfq_jc_materialconsumption.workProcessID,srp_erp_mfq_jc_materialconsumption.materialCost AS materialCost,jcMaterialConsumptionID,CONCAT(itemSystemCode,' - ',itemDescription) as itemDescription,IFNULL(wh.currentStock,0) as currentStock,srp_erp_mfq_jc_materialconsumption.qtyUsed,usageQty,srp_erp_mfq_jc_materialconsumption.jobCardID,srp_erp_mfq_jc_materialconsumption.mfqItemID as typeMasterAutoID");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $jobID);
        $this->db->where('srp_erp_mfq_itemmaster.itemAutoID IS NOT NULL');
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_warehousemaster', "srp_erp_mfq_warehousemaster.mfqWarehouseAutoID = srp_erp_mfq_job.mfqWarehouseAutoID", 'inner');
        $this->db->join('srp_erp_mfq_jc_materialconsumption', "srp_erp_mfq_jc_materialconsumption.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_materialconsumption.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_itemmaster', "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_jc_materialconsumption.mfqItemID", 'inner');
        $this->db->join('(SELECT SUM(currentStock) as currentStock,wareHouseAutoID,itemAutoID FROM srp_erp_warehouseitems GROUP BY wareHouseAutoID,itemAutoID) wh', "wh.wareHouseAutoID = srp_erp_mfq_warehousemaster.warehouseAutoID AND srp_erp_mfq_itemmaster.itemAutoID = wh.itemAutoID", 'left');
        $data = $this->db->get()->result_array();

        $updateQty = array();
        if($data){
            foreach ($data AS $item) {
                $updateQty = '';
                $updateQtyUsed = null;
                $itemID = $item['itemAutoID'];
                if($documentID == 'ST') {
                    $updateQty = $this->db->query("SELECT transfer_QTY as Qty, (totalValue/srp_erp_stocktransfermaster.companyLocalExchangeRate) AS totalValue FROM srp_erp_stocktransferdetails 
                    JOIN srp_erp_stocktransfermaster ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID
                                WHERE approvedYN = 1 AND srp_erp_stocktransferdetails.stockTransferAutoID = {$system_code} AND itemAutoID = {$itemID}")->row_array();
                } else if ($documentID == 'GRV') {
                    $updateQty = $this->db->query("SELECT SUM(receivedQty) as Qty, (fullTotalAmount/srp_erp_grvmaster.companyLocalExchangeRate) AS totalValue FROM srp_erp_grvdetails 
                                JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID 
                                WHERE approvedYN = 1  AND srp_erp_grvmaster.grvAutoID = {$system_code} AND itemAutoID = {$itemID} GROUP BY itemAutoID")->row_array();
                } else if ($documentID == 'MRN') {
                    $updateQty = $this->db->query("SELECT SUM(qtyReceived) as Qty, (totalValue/srp_erp_materialreceiptmaster.companyLocalExchangeRate) AS totalValue FROM srp_erp_materialreceiptdetails JOIN srp_erp_materialreceiptmaster ON srp_erp_materialreceiptmaster.mrnAutoID = srp_erp_materialreceiptdetails.mrnAutoID WHERE approvedYN = 1  AND srp_erp_materialreceiptmaster.mrnAutoID = {$system_code} AND itemAutoID = {$itemID} GROUP BY itemAutoID")->row_array();
                }

                if($updateQty) {
                    $qtyUsage = $updateQty['Qty'];
                    $this->db->set('jobID', $jobID);
                    $this->db->set('jobDetailID', $item['jcMaterialConsumptionID']);
                    $this->db->set('jobCardID', $item['jobCardID']);
                    $this->db->set('typeMasterAutoID', $item['typeMasterAutoID']);
                    $this->db->set('linkedDocumentID', $documentID);
                    $this->db->set('linkedDocumentAutoID', $system_code);
                    $this->db->set('usageAmount', $qtyUsage);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 1);
                    $this->db->insert('srp_erp_mfq_jc_usage');

                    $this->db->where('jobID', $jobID);
                    $this->db->where('typeID', 1);
                    $this->db->where('jobDetailID', $item['jcMaterialConsumptionID']);
                    $this->db->SELECT('SUM(usageAmount) as usageAmount');
                    $this->db->FROM('srp_erp_mfq_jc_usage');
                    $updateQtyUsed = $this->db->get()->row('usageAmount');

                    if($updateQtyUsed) {
                        $jcMaterialConsumptionID = $item['jcMaterialConsumptionID'];
                        $costrecalculate = $this->db->query("SELECT materialCost FROM srp_erp_mfq_jc_materialconsumption WHERE jcMaterialConsumptionID = {$jcMaterialConsumptionID}")->row('materialCost');
                        // $materialCost = $costrecalculate + $updateQty['totalValue'];
                        $materialCost = $updateQty['totalValue'];
                        $result = $this->db->query("UPDATE srp_erp_mfq_jc_materialconsumption SET 
                                usageQty = {$updateQtyUsed},
                                materialCost = {$materialCost},
                                unitCost = ({$materialCost} / {$updateQtyUsed}),
                                materialCharge = ({$materialCost})+(({$materialCost})*(markUp/100))
                            WHERE jcMaterialConsumptionID= {$jcMaterialConsumptionID}");
                    }
                }
            }
            $itemIDs = array_column($data, 'itemAutoID');
        }

        $createItem = array();
        $where = "";
        if(!empty($itemIDs)) 
        {
            $where = " AND det.itemAutoID NOT IN (" . join(',', $itemIDs) . ")";
        }
        if($documentID == 'ST') {
            $createItem = $this->db->query("SELECT transfer_QTY as Qty, det.itemAutoID, mfqItemID, (totalValue/srp_erp_stocktransfermaster.companyLocalExchangeRate) AS totalValue
                                        FROM srp_erp_stocktransferdetails det
                                        LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = det.itemAutoID
                                        JOIN srp_erp_stocktransfermaster ON srp_erp_stocktransfermaster.stockTransferAutoID = det.stockTransferAutoID
                                        WHERE approvedYN = 1 AND det.stockTransferAutoID = {$system_code} {$where} GROUP BY itemAutoID")->result_array();
        } else if ($documentID == 'GRV') {
            // $createItem = $this->db->query("SELECT SUM(receivedQty) as Qty, det.itemAutoID, mfqItemID, (fullTotalAmount/srp_erp_grvmaster.companyLocalExchangeRate) AS totalValue FROM srp_erp_grvdetails det 
            //                      JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = det.grvAutoID 
            //                     LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = det.itemAutoID 
            //                     WHERE approvedYN = 1 AND det.grvAutoID = {$system_code} {$where} GROUP BY det.itemAutoID")->result_array();
             $createItem = null;
        } else if ($documentID == 'MRN') {
            $createItem = $this->db->query("SELECT SUM(qtyReceived) as Qty, det.itemAutoID, mfqItemID, (totalValue/srp_erp_materialreceiptmaster.companyLocalExchangeRate) AS totalValue FROM srp_erp_materialreceiptdetails det
            JOIN srp_erp_materialreceiptmaster ON srp_erp_materialreceiptmaster.mrnAutoID = det.mrnAutoID  
                        LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = det.itemAutoID 
                        WHERE approvedYN = 1 AND det.mrnAutoID = {$system_code} {$where} GROUP BY det.itemAutoID")->result_array();
        }

        if(!empty($createItem))
        {
            $this->db->where('jobID', $jobID);
            $this->db->where('status', 1);
            $this->db->order_by('workProcessFlowID', 'desc');
            $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');

            if(empty($templateDetailID)) {
                $this->db->where('jobID', $jobID);
                $this->db->where('status', 0);
                $this->db->order_by('workProcessFlowID', 'asc');
                $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');
            }

            $this->db->select("jobcardID AS jobCardID");
            $this->db->from("srp_erp_mfq_jobcardmaster");
            $this->db->where('templateDetailID', $templateDetailID);
            $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $jobID);
            $jobCardID = $this->db->get()->row('jobCardID');

            if($jobCardID) {
                foreach($createItem as $val) {
                    if(empty($val['mfqItemID']))
                    {
                        $itemAutoID = $val['itemAutoID'];
                        $result = $this->db->query('INSERT INTO srp_erp_mfq_itemmaster (
                                            itemAutoID, categoryType, itemSystemCode, secondaryItemCode, itemImage,
                                            itemName, itemDescription, mainCategoryID,mainCategory,subcategoryID, subSubCategoryID, 
                                            itemUrl, barcode, financeCategory, partNo,
                                            defaultUnitOfMeasureID, defaultUnitOfMeasure, currentStock, reorderPoint,
                                            maximunQty, minimumQty, revenueGLAutoID, revenueSystemGLCode, revenueGLCode,
                                            revenueDescription, revenueType, costGLAutoID, costSystemGLCode, costGLCode,
                                            costDescription, costType, assetGLAutoID, assetSystemGLCode, assetGLCode, assetDescription,
                                            assetType, faCostGLAutoID, faACCDEPGLAutoID, faDEPGLAutoID, faDISPOGLAutoID,
                                            isActive, comments, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate,
                                            companyLocalSellingPrice, companyLocalWacAmount, companyLocalCurrencyDecimalPlaces,
                                            companyReportingCurrencyID, companyReportingCurrency, companyID, companyCode
                                        ) SELECT
                                        
                                        itemAutoID, IF ( mainCategory = "Inventory" OR mainCategoryID = "Non Inventory", 1,
                                        IF ( mainCategory = "Service", 2, NULL ) ) AS categoryType,
                                        itemSystemCode, seconeryItemCode, itemImage, itemName,
                                        itemDescription, mainCategoryID,mainCategory,subcategoryID, subSubCategoryID, 
                                        itemUrl, barcode, financeCategory,
                                        partNo, defaultUnitOfMeasureID, defaultUnitOfMeasure,
                                        currentStock, reorderPoint, maximunQty, minimumQty,
                                        revanueGLAutoID, revanueSystemGLCode, revanueGLCode,
                                        revanueDescription, revanueType, costGLAutoID, costSystemGLCode, costGLCode,
                                        costDescription, costType, assteGLAutoID, assteSystemGLCode,
                                        assteGLCode, assteDescription, assteType, faCostGLAutoID, faACCDEPGLAutoID,
                                        faDEPGLAutoID, faDISPOGLAutoID, isActive, comments, companyLocalCurrencyID,
                                        companyLocalCurrency, companyLocalExchangeRate, companyLocalSellingPrice,
                                        companyLocalWacAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID,
                                        companyReportingCurrency, companyID, companyCode
                                        FROM
                                    srp_erp_itemmaster WHERE companyID = ' . $this->common_data['company_data']['company_id'] . ' AND itemAutoID = ' . $itemAutoID);
                        
                        $val['mfqItemID'] = $this->db->insert_id();
    
                    }

                    $mfqItemID = $val['mfqItemID'];
                    $materialAdded = $this->db->query("SELECT jcMaterialConsumptionID, usageQty, unitCost, markUp FROM srp_erp_mfq_jc_materialconsumption WHERE workProcessID = {$jobID} AND jobCardID = {$jobCardID} AND mfqItemID = {$mfqItemID}")->row_array();

                    if($materialAdded) {
                        $jcMaterialID = $materialAdded['jcMaterialConsumptionID'];
                        $jcMaterialUpdate['usageQty'] = $materialAdded['usageQty'] + $val['Qty'];
                        $jcMaterialUpdate['materialCost'] = ($materialAdded['usageQty'] + $val['Qty']) * $materialAdded['unitCost'];
                        $jcMaterialUpdate['materialCharge'] = (($materialAdded['usageQty'] + $val['Qty']) * $materialAdded['unitCost']) + ((($materialAdded['usageQty'] + $val['Qty']) * $materialAdded['unitCost']) * ($materialAdded['markUp']/100));
                        $this->db->where('workProcessID', $jobID);
                        $this->db->where('jobCardID', $jobCardID);
                        $this->db->where('mfqItemID', $mfqItemID);
                        $this->db->where('jcMaterialConsumptionID', $jcMaterialID);
                        $this->db->update('srp_erp_mfq_jc_materialconsumption', $jcMaterialUpdate);
        
                    } else {
                        $this->db->set('mfqItemID', $val['mfqItemID']);
                        $this->db->set('qtyUsed', $val['Qty']);
                        $this->db->set('usageQty', $val['Qty']);
                        $this->db->set('unitCost', ($val['totalValue'] / $val['Qty']));
                        $this->db->set('materialCost', $val['totalValue']);
                        $this->db->set('markUp', 0);
                        $this->db->set('materialCharge', $val['totalValue']);
                        $this->db->set('jobCardID', $jobCardID);    
                        $this->db->set('workProcessID', $jobID);
                        $this->db->set('companyID', current_companyID());
                        $this->db->set('transactionCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                        $this->db->set('transactionCurrency', $this->common_data['company_data']['company_default_currency']);
                        $this->db->set('transactionExchangeRate', 1);
                        $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->common_data['company_data']['company_default_currencyID']));
                        $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                        $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
                        $default_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_default_currencyID']);
                        $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
                        $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);

                        $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
                        $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
                        $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                        $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
                        $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);

                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('createdUserID', current_userID());
                        $this->db->set('createdUserName', current_user());
                        $this->db->set('createdDateTime', current_date(true));
                        $this->db->insert('srp_erp_mfq_jc_materialconsumption');
                        $jcMaterialID = $this->db->insert_id();
                    }
    
                    /* $this->db->set('mfqItemID', $val['mfqItemID']);
                    $this->db->set('qtyUsed', $val['Qty']);
                    $this->db->set('usageQty', $val['Qty']);
                    $this->db->set('unitCost', ($val['totalValue'] / $val['Qty']));
                    $this->db->set('materialCost', $val['totalValue']);
                    $this->db->set('markUp', 0);
                    $this->db->set('materialCharge', $val['totalValue']);
                    $this->db->set('jobCardID', $jobCardID);    
                    $this->db->set('workProcessID', $jobID);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('transactionCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                    $this->db->set('transactionCurrency', $this->common_data['company_data']['company_default_currency']);
                    $this->db->set('transactionExchangeRate', 1);
                    $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->common_data['company_data']['company_default_currencyID']));
                    $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                    $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
                    $default_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_default_currencyID']);
                    $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
                    $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);
    
                    $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
                    $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
                    $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                    $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
                    $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);
    
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->insert('srp_erp_mfq_jc_materialconsumption');
                    $jcMaterialID = $this->db->insert_id(); */
    
                    $this->db->set('jobID', $jobID);
                    $this->db->set('jobDetailID', $jcMaterialID);
                    $this->db->set('jobCardID', $jobCardID);
                    $this->db->set('typeMasterAutoID', $val['mfqItemID']);
                    $this->db->set('linkedDocumentID', $documentID);
                    $this->db->set('linkedDocumentAutoID', $system_code);
                    $this->db->set('usageAmount', $val['Qty']);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 1);
                    $this->db->insert('srp_erp_mfq_jc_usage');
                }
            }
        }

        //update thired party
        if($LanguagePolicy != 'FlowServ'){
            $grvDetails = $this->db->where('grvAutoID',$system_code)->where('companyID',current_companyID())->from('srp_erp_grvdetails')->get()->result_array();

            foreach($grvDetails as $grvDet){
                
                if($LanguagePolicy != 'FlowServe'){
                    $srvupdate = $this->db->query("SELECT jc.jcOverHeadID,jc.totalValue FROM srp_erp_mfq_jc_overhead as jc LEFT JOIN srp_erp_mfq_overhead as ov ON jc.overHeadID = ov.overHeadID WHERE jc.workProcessID = '{$jobID}' AND ov.erpItemAutoID = '{$grvDet['itemAutoID']}'")->result_array();
    
                    if($srvupdate){
                        $data = array();
                        $data['totalValue'] = $grvDet['receivedTotalAmount'] + $srvupdate['totalValue'];
         
                        $this->db->where('jcOverHeadID',$srvupdate['jcOverHeadID'])->update('srp_erp_mfq_jc_overhead',$data);
                    }
                }else{
                    $srvupdate = $this->db->query("SELECT jc.jcOverHeadID,jc.totalValue FROM srp_erp_mfq_jc_overhead as jc LEFT JOIN srp_erp_mfq_overhead as ov ON jc.overHeadID = ov.overHeadID WHERE jc.workProcessID = '{$jobID}' AND ov.typeID = 2 AND ov.erpItemAutoID = '{$grvDet['itemAutoID']}'")->result_array();
    
                    if($srvupdate){
                        $data = array();
                        $data['totalValue'] = $grvDet['receivedTotalAmount'] + $srvupdate['totalValue'];
         
                        $this->db->where('jcOverHeadID',$srvupdate['jcOverHeadID'])->update('srp_erp_mfq_jc_overhead',$data);
                    }
                }
               
            }
        }

    }

    function fetch_MR_code_ST()
    {
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');

        $this->db->select('tranferDate, itemType, to_wareHouseAutoID, from_wareHouseAutoID');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', trim($stockTransferAutoID));
        $result = $this->db->get()->row_array();

        $issueDate = $result['tranferDate'];
        $itemType = $result['itemType'];
        $requestedWareHouseAutoID = $result['from_wareHouseAutoID'];
        $companyID = current_companyID();

        $data = $this->db->query("SELECT
	mrm.mrAutoID,
	MRCode,
	requestedDate,
	employeeName,
/*IFNULL(SUM(mrqdetail.qtyRequested),0) as qtyRequested,*/
	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((IFNULL( SUM( mrqdetail.qtyRequested ), 0 )), 2)))))) AS qtyRequested,
	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((IFNULL( SUM( mrqdetail.mrQty ), 0 )), 2 )))))) AS mrQty,
	TRIM(TRAILING '.' FROM(TRIM(TRAILING 0 FROM ((ROUND(( IFNULL( SUM( stdetail.stQty ), 0 )), 2 )))))) AS stQty,
    TRIM(TRAILING '.' FROM(TRIM(TRAILING 0 FROM ((ROUND((IFNULL( SUM( mrqdetail.qtyRequested ), 0 ) - (IFNULL( SUM( stdetail.stQty ), 0 ) + IFNULL( SUM( mrqdetail.mrQty ), 0 ))), 2 )))))) AS usageQty

/*IFNULL(SUM(mrqdetail.mrQty),0) as mrQty*/
FROM
	srp_erp_materialrequest mrm
LEFT JOIN (
	SELECT
		mrd.mrDetailID,
		mrd.mrAutoID,
		mrd.qtyRequested,
		sum(
			srp_erp_itemissuedetails.qtyIssued
		) AS mrQty
	FROM
	srp_erp_materialrequestdetails mrd
	LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuedetails.mrDetailID= mrd.mrDetailID
	GROUP BY
		mrDetailID
) mrqdetail ON mrqdetail.mrAutoID = mrm.mrAutoID
	LEFT JOIN (
	SELECT
		mrd.mrDetailID,
		mrd.mrAutoID,
		sum( transfer_QTY ) AS stQty 
	FROM
		srp_erp_materialrequestdetails mrd
		LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransferdetails.mrDetailID = mrd.mrDetailID 
	GROUP BY
		mrDetailID 
	) stdetail ON stdetail.mrAutoID = mrm.mrAutoID 
WHERE
	mrm.requestedDate <= '$issueDate'
AND mrm.itemType = '$itemType'
AND mrm.wareHouseAutoID = '$requestedWareHouseAutoID'
AND mrm.companyID = '$companyID'
AND mrm.approvedYN = 1
GROUP BY
		mrm.mrAutoID")->result_array();

        return $data;
    }

    function fetch_mr_detail_table_ST()
    {
        $stockTransferAutoID = trim($this->input->post('stockTransferAutoID') ?? '');

        $this->db->select('*');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
        $this->db->where('companyID', trim(current_companyID()));
        $this->db->from('srp_erp_materialrequest');
        $master = $this->db->get()->row_array();

        $this->db->select('tranferDate, itemType, to_wareHouseAutoID, from_wareHouseAutoID');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', $stockTransferAutoID);
        $transferMaster = $this->db->get()->row_array();

        $transferMasterWarehouseID = $transferMaster['from_wareHouseAutoID'];
        $mrAutoID = $this->input->post('mrAutoID');
        $companyID = current_companyID();

        $data['detail'] = $this->db->query("SELECT
	srp_erp_materialrequestdetails.*,
        TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((IFNULL( det.qtyIssued, 0 ) + IFNULL( STdet.transfer_QTY, 0 )), 2 )))))) AS qtyIssued,
        TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((IFNULL( itmlg.currentStock, 0 )), 2 )))))) AS stock,
        TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(( detMaterialIssue.miQtyIssued ), 2 )))))) AS miQtyIssued,
        TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(( IFNULL(qtyRequested , 0) - (IFNULL(qtyIssued, 0) + IFNULL( STdet.transfer_QTY, 0 )) ), 2 )))))) AS balanceQTY
FROM
	srp_erp_materialrequestdetails
LEFT JOIN (
    SELECT
       COALESCE(SUM(qtyIssued),0) as qtyIssued,mrDetailID,mrAutoID
    FROM
        srp_erp_itemissuedetails
    GROUP BY
        mrDetailID
) AS det ON det.mrDetailID = srp_erp_materialrequestdetails.mrDetailID
AND det.mrAutoID = srp_erp_materialrequestdetails.mrAutoID 
LEFT JOIN (
    SELECT
       COALESCE(SUM(transfer_QTY),0) as transfer_QTY,mrDetailID,mrAutoID
    FROM
        srp_erp_stocktransferdetails
    GROUP BY
        mrDetailID
) AS STdet ON STdet.mrDetailID = srp_erp_materialrequestdetails.mrDetailID
AND STdet.mrAutoID = srp_erp_materialrequestdetails.mrAutoID 
LEFT JOIN (
	SELECT
		COALESCE(SUM(transfer_QTY),0) AS miQtyIssued,
		mrDetailID,
		mrAutoID,
		itemAutoID
	FROM
		srp_erp_stocktransferdetails
	WHERE
		stockTransferAutoID = {$stockTransferAutoID}
	GROUP BY
		itemAutoID
) AS detMaterialIssue ON detMaterialIssue.itemAutoID = srp_erp_materialrequestdetails.itemAutoID
LEFT JOIN (
	SELECT
		SUM(
			transactionQTY / convertionRate
		) AS currentStock,itemAutoID
	FROM
		srp_erp_itemledger
	WHERE
		wareHouseAutoID = $transferMasterWarehouseID
GROUP BY
		itemAutoID
) itmlg ON itmlg.itemAutoID = srp_erp_materialrequestdetails.itemAutoID
LEFT JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_materialrequestdetails.itemAutoID
AND srp_erp_warehouseitems.wareHouseAutoID = $transferMasterWarehouseID
WHERE
	srp_erp_materialrequestdetails.mrAutoID = $mrAutoID
AND srp_erp_materialrequestdetails.companyID = $companyID
GROUP BY srp_erp_materialrequestdetails.mrDetailID")->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function save_mr_base_ST_items()
    {
        $qty = $this->input->post('qty');
        $mrDetailID = $this->input->post('mrDetailID');
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');
        $this->db->trans_start();

        $this->db->select('*');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', $stockTransferAutoID);
        $transferMaster = $this->db->get()->row_array();

        $companyID = current_companyID();
        $mfqWarehouseAutoID = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster WHERE warehouseAutoID = {$transferMaster['from_wareHouseAutoID']} AND companyID = {$companyID}")->row('mfqWarehouseAutoID');

        $this->db->select('cca.GLAutoID as GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory');
        $this->db->from('srp_erp_companycontrolaccounts cca');
        $this->db->join('srp_erp_chartofaccounts ca', 'cca.GLAutoID = ca.GLAutoID', 'LEFT');
        $this->db->where('controlAccountType', 'GIT');
        $this->db->where('cca.companyID', $companyID);
        $materialRequestGlDetail = $this->db->get()->row_array();

        foreach ($mrDetailID as $key => $mrDetailID) {

            if ($qty[$key] != 0) {
                $this->db->select('srp_erp_materialrequestdetails.*');
                $this->db->from('srp_erp_materialrequestdetails');
                $this->db->where('mrDetailID', $mrDetailID);
                $itemDetail = $this->db->get()->row_array();

                if ($stockTransferAutoID) {
                    $this->db->select('stockTransferAutoID,,itemDescription,itemSystemCode');
                    $this->db->from('srp_erp_stocktransferdetails');
                    $this->db->where('stockTransferAutoID', $stockTransferAutoID);
                    $this->db->where('mrAutoID', $itemDetail['mrAutoID']);
                    $this->db->where('itemAutoID', $itemDetail['itemAutoID']);
                    $order_detail = $this->db->get()->row_array();
                    if (!empty($order_detail)) {
                        $this->session->set_flashdata('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                        return array('status' => false);
                        //return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                    }
                }

                $item_data = fetch_item_data($itemDetail['itemAutoID']);
                $data['stockTransferAutoID'] = trim($this->input->post('stockTransferAutoID') ?? '');
                $data['mrAutoID'] = trim($itemDetail['mrAutoID'] ?? '');
                $data['mrDetailID'] = trim($mrDetailID);
                $data['itemAutoID'] = $itemDetail['itemAutoID'];
                $data['itemSystemCode'] = $item_data['itemSystemCode'];
                $data['itemDescription'] = $item_data['itemDescription'];
                $data['itemCategory'] = $item_data['mainCategory'];
                $data['itemFinanceCategory'] = $item_data['subcategoryID'];
                $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
                $data['financeCategory'] = $item_data['financeCategory'];
                $data['PLGLAutoID'] = $materialRequestGlDetail['GLAutoID'];
                $data['PLSystemGLCode'] = $materialRequestGlDetail['systemAccountCode'];
                $data['PLGLCode'] = $materialRequestGlDetail['GLSecondaryCode'];
                $data['PLDescription'] = $materialRequestGlDetail['GLDescription'];
                $data['PLType'] = $materialRequestGlDetail['subCategory'];
                if($mfqWarehouseAutoID) {
                    $wipGLDesc=fetch_gl_account_desc($this->common_data['controlaccounts']['WIP']);

                    $data['BLGLAutoID'] = $this->common_data['controlaccounts']['WIP'];
                    $data['BLSystemGLCode'] = $wipGLDesc['systemAccountCode'];
                    $data['BLGLCode'] = $wipGLDesc['GLSecondaryCode'];
                    $data['BLDescription'] = $wipGLDesc['GLDescription'];
                    $data['BLType'] = $wipGLDesc['subCategory'];
                } else {
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                }

                $data['unitOfMeasure'] = trim($itemDetail['unitOfMeasure'] ?? '');
                $data['unitOfMeasureID'] = $itemDetail['unitOfMeasureID'];
                $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
                $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
                $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
                $data['transfer_QTY'] = $qty[$key];
                $data['qtyRequested'] = $itemDetail['qtyRequested'];
                $data['currentWareHouseStock'] = $itemDetail['currentWareHouseStock'];
                if ($transferMaster['transferType'] != 'materialRequest') {
                    $data['segmentID'] = trim($itemDetail['segmentID'] ?? '');
                    $data['segmentCode'] = trim($itemDetail['segmentCode'] ?? '');
                }
                $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
                $data['currentStock'] = $item_data['currentStock'];
                $data['totalValue'] = ($data['currentlWacAmount'] * ($data['transfer_QTY'] / $data['conversionRateUOM']));
                $data['comments'] = $itemDetail['comments'];
                $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
                $this->db->from('srp_erp_warehousemaster');
                $this->db->where('wareHouseAutoID', $transferMaster['from_wareHouseAutoID']);
                $fromWarehouseGl = $this->db->get()->row_array();

                $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
                $this->db->from('srp_erp_warehousemaster');
                $this->db->where('wareHouseAutoID', $transferMaster['to_wareHouseAutoID']);
                $toWarehouseGl = $this->db->get()->row_array();

                if ($fromWarehouseGl['warehouseType'] == 2) {
                    $data['fromWarehouseType'] = 2;
                    $data['fromWarehouseWIPGLAutoID'] = $fromWarehouseGl['WIPGLAutoID'];
                }

                if ($toWarehouseGl['warehouseType'] == 2) {
                    $data['toWarehouseType'] = 2;
                    $data['toWarehouseWIPGLAutoID'] = $toWarehouseGl['WIPGLAutoID'];
                }
                $this->db->insert('srp_erp_stocktransferdetails', $data);
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Material Request : Details Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Material Request : Item Details Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function save_bulk_transfer_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $trfrDate = $this->input->post('tranferDate');
        $tranferDate = input_format_date($trfrDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $form_location = explode('|', trim($this->input->post('form_location_dec') ?? ''));

        if ($financeyearperiodYN == 1) {
            $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($tranferDate);
            if (empty($financeYearDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
            } else {
                $FYBegin = $financeYearDetails['beginingDate'];
                $FYEnd = $financeYearDetails['endingDate'];
                $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails = get_financial_period_date_wise($tranferDate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
            } else {
                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
        $data['documentID'] = 'STB';
        $data['transferType'] = 'standard';
        $data['itemType'] = trim($this->input->post('itemType') ?? '');
        $data['receiptType'] = trim($this->input->post('receiptType') ?? '');
        $data['tranferDate'] = trim($tranferDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo') ?? '');
        $narration = ($this->input->post('narration'));
        $data['comment'] = str_replace('<br />', PHP_EOL, $narration);
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');

        $data['from_wareHouseAutoID'] = trim($this->input->post('form_location') ?? '');
        $data['form_wareHouseCode'] = trim($form_location[0] ?? '');
        $data['form_wareHouseLocation'] = trim($form_location[1] ?? '');
        $data['form_wareHouseDescription'] = trim($form_location[2] ?? '');

        $toWarehouseIDs = $this->input->post('to_location');
        $toWarehouseID = implode(',', $toWarehouseIDs);
        $data['to_wareHouseAutoID'] = $toWarehouseID;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');

        if (trim($this->input->post('stockTransferAutoID') ?? '')) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
            $this->db->update('srp_erp_stocktransfermaster_bulk', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Bulk Transfer : Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Bulk Transfer : Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('stockTransferAutoID'));
            }
        } else {
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['companyLocalCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['stockTransferCode'] = 0;
            $this->db->insert('srp_erp_stocktransfermaster_bulk', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Bulk Transfer : Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Bulk Transfer : Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_bulk_transfer_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS transferDate ');
        $this->db->where('stockTransferAutoID', $this->input->post('stockTransferAutoID'));
        $data = $this->db->get('srp_erp_stocktransfermaster_bulk')->row_array();

        $data['toWarehouse'] = explode(',', $data['to_wareHouseAutoID']);
        return $data;
    }

    function fetch_bulkTransfer_details()
    {
        $itemSearch = $this->input->post('itemSearch');
        $companyID = current_companyID();
        $this->db->select('to_wareHouseAutoID, from_wareHouseAutoID');
        $this->db->where('companyID', $companyID);
        $this->db->where('stockTransferAutoID', $this->input->post('stockTransferAutoID'));
        $data['warehouse'] = $this->db->get('srp_erp_stocktransfermaster_bulk')->row_array();
        $fromWarehouse = $data['warehouse']['from_wareHouseAutoID'];
        $data['toWarehouse'] = explode(',', $data['warehouse']['to_wareHouseAutoID']);

        $this->db->select('srp_erp_stocktransferdetails_bulk.*, IFNULL(ledgerCurrentStock, 0) AS ledgerCurrentStock');
        $this->db->where('stockTransferAutoID', $this->input->post('stockTransferAutoID'));
        if(!empty($itemSearch)) {
            $this->db->where("(itemSystemCode LIKE '%{$itemSearch}%' OR itemDescription LIKE '%{$itemSearch}%' OR unitOfMeasure LIKE '%{$itemSearch}%')");
        }
        $this->db->join("(SELECT SUM(transactionQTY / convertionRate) AS ledgerCurrentStock, itemAutoID
FROM srp_erp_itemledger WHERE wareHouseAutoID = {$fromWarehouse} GROUP BY itemAutoID, wareHouseAutoID)warehouse", 'warehouse.itemAutoID = srp_erp_stocktransferdetails_bulk.itemAutoID', 'left');
        $this->db->group_by('itemAutoID');
        $items = $this->db->get('srp_erp_stocktransferdetails_bulk')->result_array();

        $result = array();
        foreach ($items AS $item) {
            $this->db->select('*');
            $this->db->where('stockTransferAutoID', $this->input->post('stockTransferAutoID'));
            if(!empty($itemSearch)) {
                $this->db->where("(itemSystemCode LIKE '%{$itemSearch}%' OR itemDescription LIKE '%{$itemSearch}%' OR unitOfMeasure LIKE '%{$itemSearch}%')");
            }
            $this->db->where('itemAutoID', $item['itemAutoID']);
            $details = $this->db->get('srp_erp_stocktransferdetails_bulk')->result_array();

            $result[$item['itemAutoID']]['stockTransferAutoID'] = $item['stockTransferAutoID'];
            $result[$item['itemAutoID']]['itemAutoID'] = $item['itemAutoID'];
            $result[$item['itemAutoID']]['itemSystemCode'] = $item['itemSystemCode'];
            $result[$item['itemAutoID']]['itemDescription'] = $item['itemDescription'];
            $result[$item['itemAutoID']]['currentWareHouseStock'] = $item['ledgerCurrentStock'];
            $result[$item['itemAutoID']]['unitOfMeasureID'] = $item['unitOfMeasureID'];
            $result[$item['itemAutoID']]['unitOfMeasure'] = $item['unitOfMeasure'];

            foreach ($details as $det) {
                $result[$item['itemAutoID']][$det['towarehouseAutoID']] = $det['towarehouseAutoID'];
                $result[$item['itemAutoID']][$det['towarehouseAutoID'] . '_qty'] = $det['transfer_QTY'];
                $result[$item['itemAutoID']][$det['towarehouseAutoID'] . '_transferDetailID'] = $det['stockTransferDetailsID'];
            }
        }
        $data['details'] = $result;

        return $data;
    }

    function add_item_bulk_transfer()
    {
        $companyID = current_companyID();
        $selectedItemsSync = $this->input->post('selectedItemsSync');
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');
        $this->db->select('*');
        $this->db->where('companyID', $companyID);
        $this->db->where('stockTransferAutoID', trim($stockTransferAutoID));
        $master = $this->db->get('srp_erp_stocktransfermaster_bulk')->row_array();
        $warehouse = explode(',', $master['to_wareHouseAutoID']);
        $this->db->trans_start();
        foreach ($selectedItemsSync AS $itemAutoID)
        {
            $item_data = fetch_item_data($itemAutoID);
            foreach ($warehouse AS $warehouseAutoID)
            {
                $data['stockTransferAutoID'] = $stockTransferAutoID;
                $data['itemAutoID'] = $itemAutoID;
                $data['itemSystemCode'] = $item_data['itemSystemCode'];
                $data['itemDescription'] = $item_data['itemDescription'];
                $data['unitOfMeasureID'] = $item_data['defaultUnitOfMeasureID'];
                $data['unitOfMeasure'] = $item_data['defaultUnitOfMeasure'];
                $data['SUOMID'] = '';
                $data['SUOMQty'] = '';
                $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
                $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
                $data['conversionRateUOM'] = 1;
                $data['transfer_QTY'] = 0;
                $data['segmentID'] = trim($master['segmentID'] ?? '');
                $data['segmentCode'] = trim($master['segmentCode'] ?? '');
                $data['towarehouseAutoID'] = $warehouseAutoID;
                $stock = $this->db->query("SELECT IFNULL(SUM(transactionQTY/convertionRate), 0) as currentStock FROM srp_erp_itemledger where wareHouseAutoID={$master["from_wareHouseAutoID"]} AND itemAutoID={$itemAutoID} ")->row('currentStock');
                $data['currentWareHouseStock'] = $stock;

                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $warehouseAutoID);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
                $this->db->from('srp_erp_warehousemaster');
                $this->db->where('wareHouseAutoID', $master['from_wareHouseAutoID']);
                $fromWarehouseGl = $this->db->get()->row_array();

                $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID, wareHouseDescription, wareHouseLocation');
                $this->db->from('srp_erp_warehousemaster');
                $this->db->where('wareHouseAutoID', $warehouseAutoID);
                $toWarehouseGl = $this->db->get()->row_array();

                if (empty($warehouseitems)) {
                    $data_arr = array(
                        'wareHouseAutoID' => $warehouseAutoID,
                        'wareHouseLocation' => $toWarehouseGl['wareHouseLocation'],
                        'wareHouseDescription' => $toWarehouseGl['wareHouseDescription'],
                        'itemAutoID' => $data['itemAutoID'],
                        'barCodeNo' => $item_data['barcode'],
                        'salesPrice' => $item_data['companyLocalSellingPrice'],
                        'ActiveYN' => $item_data['isActive'],
                        'itemSystemCode' => $data['itemSystemCode'],
                        'itemDescription' => $data['itemDescription'],
                        'unitOfMeasureID' => $data['defaultUOMID'],
                        'unitOfMeasure' => $data['defaultUOM'],
                        'currentStock' => 0,
                        'companyID' => $companyID,
                        'companyCode' => $this->common_data['company_data']['company_code'],
                    );
                    $this->db->insert('srp_erp_warehouseitems', $data_arr);
                }

                $data['itemFinanceCategory'] = $item_data['subcategoryID'];
                $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
                $data['financeCategory'] = $item_data['financeCategory'];
                $data['itemCategory'] = $item_data['mainCategory'];

                if($master['receiptType'] == 1) {
                    $this->db->select('cca.GLAutoID as GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory');
                    $this->db->from('srp_erp_companycontrolaccounts cca');
                    $this->db->join('srp_erp_chartofaccounts ca', 'cca.GLAutoID = ca.GLAutoID', 'LEFT');
                    $this->db->where('controlAccountType', 'GIT');
                    $this->db->where('cca.companyID', $this->common_data['company_data']['company_id']);
                    $gitGL = $this->db->get()->row_array();

                    $data['PLGLAutoID'] = $gitGL['GLAutoID'];
                    $data['PLSystemGLCode'] = $gitGL['systemAccountCode'];
                    $data['PLGLCode'] = $gitGL['GLSecondaryCode'];
                    $data['PLDescription'] = $gitGL['GLDescription'];
                    $data['PLType'] = $gitGL['subCategory'];

                } else {
                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data['PLGLCode'] = $item_data['costGLCode'];
                    $data['PLDescription'] = $item_data['costDescription'];
                    $data['PLType'] = $item_data['costType'];
                }

                if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                } elseif ($data['financeCategory'] == 2) {
                    $data['BLGLAutoID'] = '';
                    $data['BLSystemGLCode'] = '';
                    $data['BLGLCode'] = '';
                    $data['BLDescription'] = '';
                    $data['BLType'] = '';
                }
                if ($fromWarehouseGl['warehouseType'] == 2) {
                    $data['fromWarehouseType'] = 2;
                    $data['fromWarehouseWIPGLAutoID'] = $fromWarehouseGl['WIPGLAutoID'];
                }
                if ($toWarehouseGl['warehouseType'] == 2) {
                    $data['toWarehouseType'] = 2;
                    $data['toWarehouseWIPGLAutoID'] = $toWarehouseGl['WIPGLAutoID'];
                }
                $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
                $data['currentStock'] = $item_data['currentStock'];
                $data['totalValue'] = ($data['currentlWacAmount'] * $data['transfer_QTY']);
                $data['timestamp'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_stocktransferdetails_bulk', $data);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 'e', 'message' => 'Bulk Transfer Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 's', 'message' => 'Bulk Transfer Detail : Saved Successfully.');
        }
    }

    function delete_bulk_transfer_detail()
    {
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');
        $itemAutoID = $this->input->post('itemAutoID');
        $this->db->trans_start();
        $this->db->delete('srp_erp_stocktransferdetails_bulk', array('stockTransferAutoID' => $stockTransferAutoID, 'itemAutoID' => $itemAutoID));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in transfer detail delete process');
        } else {
            $this->db->trans_commit();
            return array('s', 'Bulk Transfer detail deleted successfully');
        }
    }

    function update_bulk_transfer_qty()
    {
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');
        $itemAutoID = $this->input->post('itemAutoID');
        $stockTransferDetailAutoID = $this->input->post('stockTransferDetailAutoID');
        $transferQty = $this->input->post('transferQty');

        $this->db->select('currentlWacAmount');
        $this->db->where('stockTransferDetailsID', $stockTransferDetailAutoID);
        $master = $this->db->get('srp_erp_stocktransferdetails_bulk')->row_array();

        $data['transfer_Qty'] = $transferQty;
        if($transferQty > 0) {
            $data['totalValue'] = ($master['currentlWacAmount'] * $transferQty);
        } else {
            $data['totalValue'] = 0;
        }

        $this->db->where('stockTransferAutoID', $this->input->post('stockTransferAutoID'));
        $this->db->where('stockTransferDetailsID', $stockTransferDetailAutoID);
        $this->db->update('srp_erp_stocktransferdetails_bulk', $data);

        $det = $this->db->query("SELECT IFNULL(SUM(transfer_QTY), 0) as Qty FROM srp_erp_stocktransferdetails_bulk WHERE stockTransferAutoID = {$stockTransferAutoID} AND itemAutoID = {$itemAutoID}")->row('Qty');
        return $det;
    }

    function fetch_signatureLevel_bulk_transfer()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'STB');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function bulk_transfer_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $companyID = current_companyID();
        $currentuser = current_userID();
        $employeelocation = $this->common_data['emplanglocationid'];
        $this->db->select('stockTransferAutoID');
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
        $this->db->from('srp_erp_stocktransferdetails_bulk');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('stockTransferAutoID');
            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_stocktransfermaster_bulk');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed!');
                return false;
            } else {
                $this->db->select('stockTransferAutoID, stockTransferCode,tranferDate');
                $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
                $this->db->from('srp_erp_stocktransfermaster_bulk');
                $app_data = $this->db->get()->row_array();

                $sql = "SELECT (srp_erp_stocktransferdetails_bulk.transfer_QTY / srp_erp_stocktransferdetails_bulk.conversionRateUOM) AS qty, itemSystemCode, itemDescription, warehouse, SUM( srp_erp_stocktransferdetails_bulk.transfer_QTY / srp_erp_stocktransferdetails_bulk.conversionRateUOM ) AS updatedStock, warehouseItem.currentStock, warehouseItem.itemAutoID,(warehouseItem.currentStock - ( SUM( srp_erp_stocktransferdetails_bulk.transfer_QTY / srp_erp_stocktransferdetails_bulk.conversionRateUOM ) )) AS stock 
                            FROM srp_erp_stocktransferdetails_bulk
                            INNER JOIN srp_erp_stocktransfermaster_bulk ON srp_erp_stocktransfermaster_bulk.stockTransferAutoID = srp_erp_stocktransferdetails_bulk.stockTransferAutoID
                            LEFT JOIN ( 
                                SELECT SUM( transactionQTY / convertionRate ) AS currentStock, itemAutoID, wareHouseAutoID, CONCAT(wareHouseDescription, ' (', wareHouseCode, ')') AS warehouse FROM srp_erp_itemledger GROUP BY itemAutoID,wareHouseAutoID 
                            ) warehouseItem ON warehouseItem.itemAutoID = srp_erp_stocktransferdetails_bulk.itemAutoID AND warehouseItem.wareHouseAutoID = srp_erp_stocktransfermaster_bulk.from_wareHouseAutoID 
                            WHERE srp_erp_stocktransferdetails_bulk.stockTransferAutoID = '{$this->input->post('stockTransferAutoID')}' 
                            GROUP BY itemAutoID HAVING stock < 0";
                $item_low_qty = $this->db->query($sql)->result_array();
                if (!empty($item_low_qty)) {
                    return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction.', 'itemDetails' => $item_low_qty);
                }

                /** item Master Sub check */
                $documentDetailID = trim($this->input->post('stockTransferAutoID') ?? '');
                $validate = $this->validate_itemMasterSub($documentDetailID, 'STB');
                /** end of item master sub */

                if ($validate) {
                    $this->load->library('Approvals');
                    $this->db->select('documentID, stockTransferCode,DATE_FORMAT(tranferDate, "%Y") as invYear,DATE_FORMAT(tranferDate, "%m") as invMonth,companyFinanceYearID,tranferDate');
                    $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
                    $this->db->from('srp_erp_stocktransfermaster_bulk');
                    $master_dt = $this->db->get()->row_array();

                    $this->load->library('sequence');
                    $stockCode = $master_dt['stockTransferCode'];
                    if ($master_dt['stockTransferCode'] == "0") {
                        if ($locationwisecodegenerate == 1) {
                            $this->db->select('locationID');
                            $this->db->where('EIdNo', $currentuser);
                            $this->db->where('Erp_companyID', $companyID);
                            $this->db->from('srp_employeesdetails');
                            $location = $this->db->get()->row_array();
                            if ((empty($location)) || ($location == '')) {
                                return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                            } else {
                                if ($employeelocation != '') {
                                    $codegeratorstocktransfer = $this->sequence->sequence_generator_location($master_dt['documentID'], $master_dt['companyFinanceYearID'], $employeelocation, $master_dt['invYear'], $master_dt['invMonth']);
                                } else {
                                    return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                                }
                            }
                        } else {
                            $codegeratorstocktransfer = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                        }

                        $validate_code = validate_code_duplication($codegeratorstocktransfer, 'stockTransferCode', trim($this->input->post('stockTransferAutoID') ?? ''),'stockTransferAutoID', 'srp_erp_stocktransfermaster_bulk');
                        if(!empty($validate_code)) {
                            return array('error' => 2, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                        }

                        $pvCd = array(
                            'stockTransferCode' => $codegeratorstocktransfer
                        );
                        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
                        $this->db->update('srp_erp_stocktransfermaster_bulk', $pvCd);

                        $stockCode = $pvCd['stockTransferCode'];
                    } else {
                        $validate_code = validate_code_duplication($master_dt['stockTransferCode'], 'stockTransferCode', trim($this->input->post('stockTransferAutoID') ?? ''),'stockTransferAutoID', 'srp_erp_stocktransfermaster_bulk');
                        if(!empty($validate_code)) {
                            return array('error' => 2, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                        }
                    }

                    $autoApproval = get_document_auto_approval('STB');
                    if ($autoApproval == 0) {
                        $approvals_status = $this->approvals->auto_approve($app_data['stockTransferAutoID'], 'srp_erp_stocktransfermaster', 'stockTransferAutoID', 'ST', $stockCode, $app_data['tranferDate']);
                    } elseif ($autoApproval == 1) {
                        $approvals_status = $this->approvals->CreateApproval('STB', $app_data['stockTransferAutoID'], $stockCode, 'Stock Transfer', 'srp_erp_stocktransfermaster_bulk', 'stockTransferAutoID', 0, $app_data['tranferDate']);
                    } else {
                        return array('error' => 1, 'message' => 'Approval levels are not set for this document');
                    }

                    if ($approvals_status == 1) {
                        $autoApproval = get_document_auto_approval('STB');
                        if ($autoApproval == 0) {
                            $result = $this->save_bulk_transfer_approval(0, $app_data['stockTransferAutoID'], 1, 'Auto Approved');
                            if ($result) {
                                return array('error' => 0, 'message' => 'Document Confirmed Successfully.');
                            }
                        } else {
                            $data = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user']
                            );
                            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
                            $this->db->update('srp_erp_stocktransfermaster_bulk', $data);
                            return array('error' => 0, 'message' => 'Document Confirmed Successfully.');
                        }
                    } else if ($approvals_status == 3) {
                        return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document');
                    } else {
                        return array('error' => 1, 'message' => 'Document confirmation failed');
                    }
                } else {
                    return array('error' => 1, 'message' => 'Please complete sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                }
            }
        }
    }

    function delete_bulk_transfer_master()
    {
        $masterID = trim($this->input->post('stockTransferAutoID') ?? '');
        $this->db->select('*');
        $this->db->from('srp_erp_stocktransferdetails_bulk');
        $this->db->where('stockTransferAutoID', $masterID);
        $datas = $this->db->get()->row_array();
        if (!empty($datas)) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            $documentCode = $this->db->get_where('srp_erp_stocktransfermaster_bulk', ['stockTransferAutoID'=> $masterID])->row('stockTransferCode');
            $this->db->trans_start();
            $length = strlen($documentCode);
            if($length > 1){
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('stockTransferAutoID',$masterID );
                $this->db->update('srp_erp_stocktransfermaster_bulk', $data);
            }else{
                $this->db->where('stockTransferAutoID', $masterID)->delete('srp_erp_stocktransferdetails_bulk');
                $this->db->where('stockTransferAutoID', $masterID)->delete('srp_erp_stocktransfermaster_bulk');
            }
            $this->db->trans_complete();
            if($this->db->trans_status() == true){
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }else{
                $this->session->set_flashdata('e', 'Error in delete process.');
                return false;
            }
        }
    }

    function re_open_bulk_transfer()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID') ?? ''));
        $this->db->update('srp_erp_stocktransfermaster_bulk', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function save_bulk_transfer_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $companyID = current_companyID();
        $this->db->trans_start();
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('stockTransferAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['stockTransferAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $maxLevel = $this->approvals->maxlevel('STB');
        $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;
        $this->db->select('from_wareHouseAutoID,receiptType');
        $this->db->from('srp_erp_stocktransfermaster_bulk');
        $this->db->where('stockTransferAutoID', $system_code);
        $frmWareHouse = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_stocktransfermaster_bulk');
        $this->db->where('stockTransferAutoID', $system_code);
        $master = $this->db->get()->row_array();

        /*$this->db->select('(srp_erp_warehouseitems.currentStock-SUM(srp_erp_stocktransferdetails_bulk.transfer_QTY)) as stockDiff,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.itemDescription,srp_erp_warehouseitems.currentStock as availableStock');
        $this->db->from('srp_erp_stocktransferdetails_bulk');
        $this->db->where('srp_erp_stocktransferdetails_bulk.stockTransferAutoID', $system_code);
        $this->db->where('srp_erp_warehouseitems.companyID', current_companyID());
        $this->db->where('srp_erp_warehouseitems.wareHouseAutoID', $frmWareHouse['from_wareHouseAutoID']);
        $this->db->join('srp_erp_warehouseitems', 'srp_erp_stocktransferdetails_bulk.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_stocktransferdetails_bulk.itemAutoID = srp_erp_itemmaster.itemAutoID');
        $this->db->group_by('srp_erp_stocktransferdetails_bulk.itemAutoID');
        $this->db->having('stockDiff < 0');*/
        $items_arr = $this->db->query("SELECT (srp_erp_stocktransferdetails_bulk.transfer_QTY / srp_erp_stocktransferdetails_bulk.conversionRateUOM) AS qty, itemSystemCode, itemDescription, warehouse, SUM( srp_erp_stocktransferdetails_bulk.transfer_QTY / srp_erp_stocktransferdetails_bulk.conversionRateUOM ) AS updatedStock, warehouseItem.currentStock, warehouseItem.itemAutoID,(warehouseItem.currentStock - ( SUM( srp_erp_stocktransferdetails_bulk.transfer_QTY / srp_erp_stocktransferdetails_bulk.conversionRateUOM ) )) AS stock 
                            FROM srp_erp_stocktransferdetails_bulk
                            INNER JOIN srp_erp_stocktransfermaster_bulk ON srp_erp_stocktransfermaster_bulk.stockTransferAutoID = srp_erp_stocktransferdetails_bulk.stockTransferAutoID
                            LEFT JOIN ( 
                                SELECT SUM( transactionQTY / convertionRate ) AS currentStock, itemAutoID, wareHouseAutoID, CONCAT(wareHouseDescription, ' (', wareHouseCode, ')') AS warehouse FROM srp_erp_itemledger GROUP BY itemAutoID,wareHouseAutoID 
                            ) warehouseItem ON warehouseItem.itemAutoID = srp_erp_stocktransferdetails_bulk.itemAutoID AND warehouseItem.wareHouseAutoID = srp_erp_stocktransfermaster_bulk.from_wareHouseAutoID 
                            WHERE srp_erp_stocktransferdetails_bulk.stockTransferAutoID = $system_code 
                            GROUP BY itemAutoID HAVING stock < 0")->result_array();

        if ($status != 1) {
            $items_arr = [];
        }
        if (!$items_arr) {
            if ($autoappLevel == 0) {
                $approvals_status = 1;
            } else {
                $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'STB');
            }

            if ($approvals_status == 1) {
                $this->db->select('*, SUM(transfer_QTY) AS transfer_QTY, SUM(totalValue) AS totalValue');
                $this->db->from('srp_erp_stocktransferdetails_bulk');
                $this->db->where('srp_erp_stocktransferdetails_bulk.stockTransferAutoID', $system_code);
                $this->db->join('srp_erp_stocktransfermaster_bulk', 'srp_erp_stocktransfermaster_bulk.stockTransferAutoID = srp_erp_stocktransferdetails_bulk.stockTransferAutoID');
                $this->db->group_by('srp_erp_stocktransferdetails_bulk.itemAutoID');
                $details_from_arr = $this->db->get()->result_array();

                $itemledger_arr = array();
                $x = 0;
                for ($i = 0; $i < count($details_from_arr); $i++) {
                    $item = fetch_item_data($details_from_arr[$i]['itemAutoID']);
                    $qty = ($details_from_arr[$i]['transfer_QTY'] / $details_from_arr[$i]['conversionRateUOM']);
                    $itemSystemCode = $details_from_arr[$i]['itemAutoID'];
                    $location = $details_from_arr[$i]['from_wareHouseAutoID'];
                    if($qty > 0) {
                        $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty}) WHERE wareHouseAutoID='{$location}' and itemAutoID='{$itemSystemCode}'");

                        $itemledger_arr[$x]['documentID'] = $details_from_arr[$i]['documentID'];
                        $itemledger_arr[$x]['documentCode'] = $details_from_arr[$i]['documentID'];
                        $itemledger_arr[$x]['documentAutoID'] = $details_from_arr[$i]['stockTransferAutoID'];
                        $itemledger_arr[$x]['documentSystemCode'] = $details_from_arr[$i]['stockTransferCode'];
                        $itemledger_arr[$x]['documentDate'] = $details_from_arr[$i]['tranferDate'];
                        $itemledger_arr[$x]['referenceNumber'] = $details_from_arr[$i]['referenceNo'];
                        $itemledger_arr[$x]['companyFinanceYearID'] = $details_from_arr[$i]['companyFinanceYearID'];
                        $itemledger_arr[$x]['companyFinanceYear'] = $details_from_arr[$i]['companyFinanceYear'];
                        $itemledger_arr[$x]['FYBegin'] = $details_from_arr[$i]['FYBegin'];
                        $itemledger_arr[$x]['FYEnd'] = $details_from_arr[$i]['FYEnd'];
                        $itemledger_arr[$x]['FYPeriodDateFrom'] = $details_from_arr[$i]['FYPeriodDateFrom'];
                        $itemledger_arr[$x]['FYPeriodDateTo'] = $details_from_arr[$i]['FYPeriodDateTo'];
                        $itemledger_arr[$x]['wareHouseAutoID'] = $details_from_arr[$i]['from_wareHouseAutoID'];
                        $itemledger_arr[$x]['wareHouseCode'] = $details_from_arr[$i]['form_wareHouseCode'];
                        $itemledger_arr[$x]['wareHouseLocation'] = $details_from_arr[$i]['form_wareHouseLocation'];
                        $itemledger_arr[$x]['wareHouseDescription'] = $details_from_arr[$i]['form_wareHouseLocation'];
                        $itemledger_arr[$x]['itemAutoID'] = $details_from_arr[$i]['itemAutoID'];
                        $itemledger_arr[$x]['itemSystemCode'] = $details_from_arr[$i]['itemSystemCode'];
                        $itemledger_arr[$x]['itemDescription'] = $details_from_arr[$i]['itemDescription'];
                        $itemledger_arr[$x]['transactionUOM'] = $details_from_arr[$i]['unitOfMeasure'];
                        $itemledger_arr[$x]['transactionQTY'] = ($qty * -1);
                        $itemledger_arr[$x]['convertionRate'] = $details_from_arr[$i]['conversionRateUOM'];
                        $itemledger_arr[$x]['currentStock'] = $item['currentStock'];
                        $itemledger_arr[$x]['itemAutoID'] = $details_from_arr[$i]['itemAutoID'];
                        $itemledger_arr[$x]['itemSystemCode'] = $details_from_arr[$i]['itemSystemCode'];
                        $itemledger_arr[$x]['itemDescription'] = $details_from_arr[$i]['itemDescription'];
                        $itemledger_arr[$x]['SUOMID'] = $details_from_arr[$i]['SUOMID'];
                        $itemledger_arr[$x]['SUOMQty'] = $details_from_arr[$i]['SUOMQty'];
                        $itemledger_arr[$x]['defaultUOM'] = $details_from_arr[$i]['defaultUOM'];
                        $itemledger_arr[$x]['transactionUOM'] = $details_from_arr[$i]['unitOfMeasure'];
                        $itemledger_arr[$x]['defaultUOMID'] = $details_from_arr[$i]['defaultUOMID'];
                        $itemledger_arr[$x]['transactionUOMID'] = $details_from_arr[$i]['unitOfMeasureID'];
                        $itemledger_arr[$x]['convertionRate'] = $details_from_arr[$i]['conversionRateUOM'];
                        $itemledger_arr[$x]['PLGLAutoID'] = $details_from_arr[$i]['PLGLAutoID'];
                        $itemledger_arr[$x]['PLSystemGLCode'] = $details_from_arr[$i]['PLSystemGLCode'];
                        $itemledger_arr[$x]['PLGLCode'] = $details_from_arr[$i]['PLGLCode'];
                        $itemledger_arr[$x]['PLDescription'] = $details_from_arr[$i]['PLDescription'];
                        $itemledger_arr[$x]['PLType'] = $details_from_arr[$i]['PLType'];
                        $itemledger_arr[$x]['BLGLAutoID'] = $details_from_arr[$i]['BLGLAutoID'];
                        $itemledger_arr[$x]['BLSystemGLCode'] = $details_from_arr[$i]['BLSystemGLCode'];
                        $itemledger_arr[$x]['BLGLCode'] = $details_from_arr[$i]['BLGLCode'];
                        $itemledger_arr[$x]['BLDescription'] = $details_from_arr[$i]['BLDescription'];
                        $itemledger_arr[$x]['BLType'] = $details_from_arr[$i]['BLType'];
                        $itemledger_arr[$x]['transactionCurrencyID'] = $details_from_arr[$i]['companyLocalCurrencyID'];
                        $itemledger_arr[$x]['transactionCurrency'] = $details_from_arr[$i]['companyLocalCurrency'];
                        $itemledger_arr[$x]['transactionExchangeRate'] = $details_from_arr[$i]['companyLocalExchangeRate'];
                        $itemledger_arr[$x]['transactionCurrencyDecimalPlaces'] = $details_from_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr[$x]['transactionAmount'] = (round($details_from_arr[$i]['totalValue'], $itemledger_arr[$x]['transactionCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$x]['companyLocalCurrencyID'] = $details_from_arr[$i]['companyLocalCurrencyID'];
                        $itemledger_arr[$x]['companyLocalCurrency'] = $details_from_arr[$i]['companyLocalCurrency'];
                        $itemledger_arr[$x]['companyLocalExchangeRate'] = $details_from_arr[$i]['companyLocalExchangeRate'];
                        $itemledger_arr[$x]['companyLocalCurrencyDecimalPlaces'] = $details_from_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr[$x]['companyLocalAmount'] = (round(($details_from_arr[$i]['totalValue'] / $itemledger_arr[$x]['companyLocalExchangeRate']), $itemledger_arr[$x]['companyLocalCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$x]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                        $itemledger_arr[$x]['companyReportingCurrencyID'] = $details_from_arr[$i]['companyReportingCurrencyID'];
                        $itemledger_arr[$x]['companyReportingCurrency'] = $details_from_arr[$i]['companyReportingCurrency'];
                        $itemledger_arr[$x]['companyReportingExchangeRate'] = $details_from_arr[$i]['companyReportingExchangeRate'];
                        $itemledger_arr[$x]['companyReportingCurrencyDecimalPlaces'] = $details_from_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                        $itemledger_arr[$x]['companyReportingAmount'] = (round(($details_from_arr[$i]['totalValue'] / $itemledger_arr[$x]['companyReportingExchangeRate']), $itemledger_arr[$x]['companyReportingCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$x]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                        $itemledger_arr[$x]['confirmedYN'] = $details_from_arr[$i]['confirmedYN'];
                        $itemledger_arr[$x]['confirmedByEmpID'] = $details_from_arr[$i]['confirmedByEmpID'];
                        $itemledger_arr[$x]['confirmedByName'] = $details_from_arr[$i]['confirmedByName'];
                        $itemledger_arr[$x]['confirmedDate'] = $details_from_arr[$i]['confirmedDate'];
                        $itemledger_arr[$x]['approvedYN'] = $details_from_arr[$i]['approvedYN'];
                        $itemledger_arr[$x]['approvedDate'] = $details_from_arr[$i]['approvedDate'];
                        $itemledger_arr[$x]['approvedbyEmpID'] = $details_from_arr[$i]['approvedbyEmpID'];
                        $itemledger_arr[$x]['approvedbyEmpName'] = $details_from_arr[$i]['approvedbyEmpName'];
                        $itemledger_arr[$x]['segmentID'] = $details_from_arr[$i]['segmentID'];
                        $itemledger_arr[$x]['segmentCode'] = $details_from_arr[$i]['segmentCode'];
                        $itemledger_arr[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                        $itemledger_arr[$x]['companyCode'] = $this->common_data['company_data']['company_code'];
                        $itemledger_arr[$x]['createdUserGroup'] = $this->common_data['user_group'];
                        $itemledger_arr[$x]['createdPCID'] = $this->common_data['current_pc'];
                        $itemledger_arr[$x]['createdUserID'] = $this->common_data['current_userID'];
                        $itemledger_arr[$x]['createdDateTime'] = $this->common_data['current_date'];
                        $itemledger_arr[$x]['createdUserName'] = $this->common_data['current_user'];
                        $itemledger_arr[$x]['modifiedPCID'] = $this->common_data['current_pc'];
                        $itemledger_arr[$x]['modifiedUserID'] = $this->common_data['current_userID'];
                        $itemledger_arr[$x]['modifiedDateTime'] = $this->common_data['current_date'];
                        $itemledger_arr[$x]['modifiedUserName'] = $this->common_data['current_user'];
                        $x++;
                    }
                }

                $this->db->select('*');
                $this->db->from('srp_erp_stocktransferdetails_bulk');
                $this->db->where('srp_erp_stocktransferdetails_bulk.stockTransferAutoID', $system_code);
                $this->db->where('srp_erp_stocktransferdetails_bulk.transfer_QTY > 0');
                $this->db->join('srp_erp_stocktransfermaster_bulk', 'srp_erp_stocktransfermaster_bulk.stockTransferAutoID = srp_erp_stocktransferdetails_bulk.stockTransferAutoID');
                $this->db->join('srp_erp_warehousemaster', 'srp_erp_warehousemaster.wareHouseAutoID = srp_erp_stocktransferdetails_bulk.towarehouseAutoID','LEFT');
                $details_arr = $this->db->get()->result_array();

                $item_arr = array();
                $itemledger_arr_to = array();
                $x = 0;
                if($frmWareHouse['receiptType']==2)
                {
                for ($i = 0; $i < count($details_arr); $i++) {
                    $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                    $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = $item['currentStock'];
                    $item_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $item_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $qty = ($details_arr[$i]['transfer_QTY'] / $details_arr[$i]['conversionRateUOM']);
                    $itemSystemCode = $details_arr[$i]['itemAutoID'];
                    $location = $details_arr[$i]['towarehouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$location}' and itemAutoID='{$itemSystemCode}'");

                    $itemledger_arr_to[$x]['documentID'] = $details_arr[$i]['documentID'];
                    $itemledger_arr_to[$x]['documentCode'] = $details_arr[$i]['documentID'];
                    $itemledger_arr_to[$x]['documentAutoID'] = $details_arr[$i]['stockTransferAutoID'];
                    $itemledger_arr_to[$x]['documentSystemCode'] = $details_arr[$i]['stockTransferCode'];
                    $itemledger_arr_to[$x]['documentDate'] = $details_arr[$i]['tranferDate'];
                    $itemledger_arr_to[$x]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                    $itemledger_arr_to[$x]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                    $itemledger_arr_to[$x]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                    $itemledger_arr_to[$x]['FYBegin'] = $details_arr[$i]['FYBegin'];
                    $itemledger_arr_to[$x]['FYEnd'] = $details_arr[$i]['FYEnd'];
                    $itemledger_arr_to[$x]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                    $itemledger_arr_to[$x]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                    $itemledger_arr_to[$x]['wareHouseAutoID'] = $details_arr[$i]['towarehouseAutoID'];
                    $itemledger_arr_to[$x]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                    $itemledger_arr_to[$x]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                    $itemledger_arr_to[$x]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                    $itemledger_arr_to[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr_to[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr_to[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr_to[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr_to[$x]['transactionQTY'] = $qty;
                    $itemledger_arr_to[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr_to[$x]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr_to[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr_to[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr_to[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr_to[$x]['SUOMID'] = $details_arr[$i]['SUOMID'];
                    $itemledger_arr_to[$x]['SUOMQty'] = $details_arr[$i]['SUOMQty'];
                    $itemledger_arr_to[$x]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                    $itemledger_arr_to[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr_to[$x]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                    $itemledger_arr_to[$x]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                    $itemledger_arr_to[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr_to[$x]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                    $itemledger_arr_to[$x]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr_to[$x]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr_to[$x]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr_to[$x]['PLType'] = $details_arr[$i]['PLType'];
                    $itemledger_arr_to[$x]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                    $itemledger_arr_to[$x]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr_to[$x]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr_to[$x]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr_to[$x]['BLType'] = $details_arr[$i]['BLType'];
                    $itemledger_arr_to[$x]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr_to[$x]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr_to[$x]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr_to[$x]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr_to[$x]['transactionAmount'] = round($details_arr[$i]['totalValue'], $itemledger_arr_to[$x]['transactionCurrencyDecimalPlaces']);
                    $itemledger_arr_to[$x]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr_to[$x]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr_to[$x]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr_to[$x]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr_to[$x]['companyLocalAmount'] = round(($details_arr[$i]['totalValue'] / $itemledger_arr_to[$x]['companyLocalExchangeRate']), $itemledger_arr_to[$x]['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr_to[$x]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $itemledger_arr_to[$x]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                    $itemledger_arr_to[$x]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                    $itemledger_arr_to[$x]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                    $itemledger_arr_to[$x]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr_to[$x]['companyReportingAmount'] = round(($details_arr[$i]['totalValue'] / $itemledger_arr_to[$x]['companyReportingExchangeRate']), $itemledger_arr_to[$x]['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr_to[$x]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $itemledger_arr_to[$x]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                    $itemledger_arr_to[$x]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr_to[$x]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                    $itemledger_arr_to[$x]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr_to[$x]['approvedYN'] = $details_arr[$i]['approvedYN'];
                    $itemledger_arr_to[$x]['approvedDate'] = $details_arr[$i]['approvedDate'];
                    $itemledger_arr_to[$x]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                    $itemledger_arr_to[$x]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                    $itemledger_arr_to[$x]['segmentID'] = $details_arr[$i]['segmentID'];
                    $itemledger_arr_to[$x]['segmentCode'] = $details_arr[$i]['segmentCode'];
                    $itemledger_arr_to[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                    $itemledger_arr_to[$x]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $itemledger_arr_to[$x]['createdUserGroup'] = $this->common_data['user_group'];
                    $itemledger_arr_to[$x]['createdPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr_to[$x]['createdUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr_to[$x]['createdDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr_to[$x]['createdUserName'] = $this->common_data['current_user'];
                    $itemledger_arr_to[$x]['modifiedPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr_to[$x]['modifiedUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr_to[$x]['modifiedDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr_to[$x]['modifiedUserName'] = $this->common_data['current_user'];
                    $x++;
                }
                    if (!empty($itemledger_arr_to)) {
                        $itemledger_arr_to = array_values($itemledger_arr_to);
                        $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr_to);
                    }
                }

                if (!empty($item_arr)) {
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }
                if (!empty($itemledger_arr)) {
                    $itemledger_arr = array_values($itemledger_arr);
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }


                $this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_bulk_transfer_data($system_code, 'STB');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['stockTransferAutoID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['stockTransferCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['tranferDate'];
                    $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['itemType'];
                    $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['tranferDate'];
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['tranferDate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                    $generalledger_arr[$i]['chequeNumber'] = '';
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
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
                    $generalledger_arr[$i]['partyType'] = '';//'EMP';
                    $generalledger_arr[$i]['partyAutoID'] = '';//$double_entry['master_data']['employeeID'];
                    $generalledger_arr[$i]['partySystemCode'] = '';//$double_entry['master_data']['employeeCode'];
                    $generalledger_arr[$i]['partyName'] = '';//$double_entry['master_data']['employeeName'];
                    $generalledger_arr[$i]['partyCurrency'] = '';//$double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = '';//$double_entry['master_data']['supplierCurrencyExchangeRate'];
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = '';//$double_entry['master_data']['supplierCurrencyDecimalPlaces'];
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
                    $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                    $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                    $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
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

                /** update sub item master sub : shafry */
                $masterID = $this->input->post('stockTransferAutoID');
                $masterData = $this->db->query("SELECT towarehouseAutoID FROM srp_erp_stocktransferdetails_bulk WHERE stockTransferAutoID = '" . $masterID . "' GROUP BY towarehouseAutoID")->result_array();
                $warehouses = array_column($masterData, 'towarehouseAutoID');
                if ($isFinalLevel) {
                    $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_sub WHERE soldDocumentID = 'STB' AND isSold='1' AND soldDocumentAutoID = '" . $masterID . "'")->result_array();

                    if (!empty($result)) {
                        $i = 0;
                        foreach ($result as $item) {
                            foreach ($masterData AS $mas) {
                                $result[$i]['receivedDocumentID'] = 'STB';
                                $result[$i]['receivedDocumentAutoID'] = $item['soldDocumentAutoID'];
                                $result[$i]['receivedDocumentDetailID'] = $item['soldDocumentDetailID'];
                                $result[$i]['isSold'] = null;
                                $result[$i]['soldDocumentID'] = null;
                                $result[$i]['soldDocumentDetailID'] = null;
                                $result[$i]['soldDocumentAutoID'] = null;
                                $result[$i]['wareHouseAutoID'] = $mas['towarehouseAutoID'];
                                $i++;
                            }
                            unset($result[$i]['subItemAutoID']);
                        }
                        $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    }
                }
                $itemAutoIDarry = array();
                foreach ($details_from_arr as $value) {
                    array_push($itemAutoIDarry, $value['itemAutoID']);
                }

                /** Create Material Receipt Note */
                if($master['receiptType'] == 1) {
                    $this->db->select('cca.GLAutoID as GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory');
                    $this->db->from('srp_erp_companycontrolaccounts cca');
                    $this->db->join('srp_erp_chartofaccounts ca', 'cca.GLAutoID = ca.GLAutoID', 'LEFT');
                    $this->db->where('controlAccountType', 'GIT');
                    $this->db->where('cca.companyID', $this->common_data['company_data']['company_id']);
                    $materialRequestGlDetail = $this->db->get()->row_array();

                    $this->db->select('towarehouseAutoID, SUM(transfer_QTY) as Qty');
                    $this->db->from('srp_erp_stocktransferdetails_bulk');
                    $this->db->where('stockTransferAutoID', $system_code);
                    $this->db->group_by('towarehouseAutoID');
                    $towarehouseAutoID = $this->db->get()->result_array();

                    foreach ($towarehouseAutoID AS $warehouseDet)
                    {
                        if($warehouseDet['Qty'] > 0) {
                            $mrnData['documentID'] = 'MRN';
                            $mrnData['receiptType'] = trim('Material Request');
                            $mrnData['itemType'] = trim('Inventory');
                            $mrnData['receivedDate'] = current_date(false);
                            $mrnData['RefNo'] = $master['stockTransferCode'];

                            $financeYearDetails=get_financial_year($mrnData['receivedDate']);
                            $mrnData['companyFinanceYearID'] = $financeYearDetails['companyFinanceYearID'];
                            $mrnData['companyFinanceYear'] = $financeYearDetails['beginingDate'] . ' - ' . $financeYearDetails['endingDate'];
                            $mrnData['FYBegin'] = $financeYearDetails['beginingDate'];
                            $mrnData['FYEnd'] = $financeYearDetails['endingDate'];
                            $financePeriodDetails=get_financial_period_date_wise($mrnData['receivedDate']);
                            $mrnData['companyFinancePeriodID'] = $financePeriodDetails['companyFinancePeriodID'];

                            $location = load_warehouses($warehouseDet['towarehouseAutoID']);
                            $mrnData['wareHouseAutoID'] = trim($warehouseDet['towarehouseAutoID'] ?? '');
                            $mrnData['wareHouseCode'] = trim($location['wareHouseCode'] ?? '');
                            $mrnData['wareHouseLocation'] = trim($location['wareHouseDescription'] ?? '');
                            $mrnData['wareHouseDescription'] = trim($location['wareHouseLocation'] ?? '');

                            $mrnData['employeeName'] = current_user();
                            $mrnData['employeeCode'] = current_userCode();
                            $mrnData['employeeID'] = current_userID();
                            $mrnData['requestedDate'] = '';
                            $mrnData['segmentID'] = '';
                            $mrnData['segmentCode'] = '';
                            $mrnData['comment'] = $master['comment'];

                            $mrnData['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $mrnData['companyLocalCurrency'] = $master['companyLocalCurrency'];
                            $mrnData['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $mrnData['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                            $mrnData['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $mrnData['companyReportingCurrency'] = $master['companyReportingCurrency'];
                            $mrnData['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $mrnData['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];

                            $mrnData['companyCode'] = $this->common_data['company_data']['company_code'];
                            $mrnData['companyID'] = $companyID;
                            $mrnData['createdUserGroup'] = $this->common_data['user_group'];
                            $mrnData['createdPCID'] = $this->common_data['current_pc'];
                            $mrnData['createdUserID'] = $this->common_data['current_userID'];
                            $mrnData['createdUserName'] = $this->common_data['current_user'];
                            $mrnData['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_materialreceiptmaster', $mrnData);
                            $last_id = $this->db->insert_id();

                            $this->db->select('*');
                            $this->db->from('srp_erp_stocktransferdetails_bulk');
                            $this->db->where('stockTransferAutoID', $system_code);
                            $this->db->where('towarehouseAutoID', $warehouseDet['towarehouseAutoID']);
                            $bulkDetails = $this->db->get()->result_array();

                            $MRNmasterDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                FROM srp_erp_materialreceiptmaster 
                                INNER JOIN srp_erp_warehousemaster ON srp_erp_warehousemaster.wareHouseAutoID = srp_erp_materialreceiptmaster.wareHouseAutoID
                                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
                                WHERE srp_erp_materialreceiptmaster.companyID = $companyID AND mrnAutoID = $last_id")->row_array();
                            $mfqWarehouseAutoID = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster WHERE warehouseAutoID = {$mrnData['wareHouseAutoID']} AND companyID = {$companyID}")->row('mfqWarehouseAutoID');

                            foreach ($bulkDetails as $bulk) {
                                if($bulk['transfer_QTY'] > 0) {
                                    $item_data = fetch_item_data($bulk['itemAutoID']);
                                    $mrnDetData['mrnAutoID'] = trim($last_id);
                                    $mrnDetData['stockTransferAutoID'] = trim($bulk['stockTransferAutoID'] ?? '');
                                    $mrnDetData['stockTransferDetailsID'] = trim($bulk['stockTransferDetailsID'] ?? '');
                                    $mrnDetData['itemAutoID'] = $bulk['itemAutoID'];
                                    $mrnDetData['itemSystemCode'] = $item_data['itemSystemCode'];
                                    $mrnDetData['itemDescription'] = $item_data['itemDescription'];
                                    $mrnDetData['unitOfMeasure'] = trim($bulk['unitOfMeasure'] ?? '');
                                    $mrnDetData['unitOfMeasureID'] = $bulk['unitOfMeasureID'];
                                    $mrnDetData['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
                                    $mrnDetData['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
                                    $mrnDetData['conversionRateUOM'] = conversionRateUOM_id($mrnDetData['unitOfMeasureID'], $mrnDetData['defaultUOMID']);
                                    $mrnDetData['qtyRequested'] = $bulk['transfer_QTY'];
                                    $mrnDetData['qtyReceived'] = $bulk['transfer_QTY'];
                                    $mrnDetData['comments'] = $bulk['comments'];;
                                    $mrnDetData['remarks'] = '';
                                    $mrnDetData['currentWareHouseStock'] = $bulk['currentWareHouseStock'];
                                    $mrnDetData['segmentID'] = trim($bulk['segmentID'] ?? '');
                                    $mrnDetData['segmentCode'] = trim($bulk['segmentCode'] ?? '');
                                    $mrnDetData['itemFinanceCategory'] = $item_data['subcategoryID'];
                                    $mrnDetData['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
                                    $mrnDetData['financeCategory'] = $item_data['financeCategory'];
                                    $mrnDetData['itemCategory'] = $item_data['mainCategory'];
                                    $mrnDetData['currentlWacAmount'] = $bulk['currentlWacAmount'];
                                    $mrnDetData['unitCost'] = $bulk['currentlWacAmount'];
                                    $mrnDetData['currentStock'] = $item_data['currentStock'];
                                    $mrnDetData['mrMasterID'] = '';
                                    $mrnDetData['mrDetailID'] = '';

                                    $mrnDetData['PLGLAutoID'] = $materialRequestGlDetail['GLAutoID'];
                                    $mrnDetData['PLSystemGLCode'] = $materialRequestGlDetail['systemAccountCode'];
                                    $mrnDetData['PLGLCode'] = $materialRequestGlDetail['GLSecondaryCode'];
                                    $mrnDetData['PLDescription'] = $materialRequestGlDetail['GLDescription'];
                                    $mrnDetData['PLType'] = $materialRequestGlDetail['subCategory'];
                                    if(!empty($MRNmasterDetails['WIPGLAutoID'])) {
                                        $mrnDetData['BLGLAutoID'] = $MRNmasterDetails['WIPGLAutoID'];
                                        $mrnDetData['BLSystemGLCode'] = $MRNmasterDetails['systemAccountCode'];
                                        $mrnDetData['BLGLCode'] = $MRNmasterDetails['GLSecondaryCode'];
                                        $mrnDetData['BLDescription'] = $MRNmasterDetails['GLDescription'];
                                        $mrnDetData['BLType'] = $MRNmasterDetails['subCategory'];
                                    } else if($mfqWarehouseAutoID) {
                                        $wipGLDesc=fetch_gl_account_desc($this->common_data['controlaccounts']['WIP']);
                                        $mrnDetData['BLGLAutoID'] = $this->common_data['controlaccounts']['WIP'];
                                        $mrnDetData['BLSystemGLCode'] = $wipGLDesc['systemAccountCode'];
                                        $mrnDetData['BLGLCode'] = $wipGLDesc['GLSecondaryCode'];
                                        $mrnDetData['BLDescription'] = $wipGLDesc['GLDescription'];
                                        $mrnDetData['BLType'] = $wipGLDesc['subCategory'];
                                    } else {
                                        $mrnDetData['BLGLAutoID'] = $item_data['assteGLAutoID'];
                                        $mrnDetData['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                                        $mrnDetData['BLGLCode'] = $item_data['assteGLCode'];
                                        $mrnDetData['BLDescription'] = $item_data['assteDescription'];
                                        $mrnDetData['BLType'] = $item_data['assteType'];
                                    }
                                    $mrnDetData['totalValue'] = ($mrnDetData['currentlWacAmount'] * $mrnDetData['qtyReceived']);

                                    $mrnDetData['companyCode'] = $this->common_data['company_data']['company_code'];
                                    $mrnDetData['companyID'] = $this->common_data['company_data']['company_id'];
                                    $mrnDetData['createdUserGroup'] = $this->common_data['user_group'];
                                    $mrnDetData['createdPCID'] = $this->common_data['current_pc'];
                                    $mrnDetData['createdUserID'] = $this->common_data['current_userID'];
                                    $mrnDetData['createdUserName'] = $this->common_data['current_user'];
                                    $mrnDetData['createdDateTime'] = $this->common_data['current_date'];
                                    $this->db->insert('srp_erp_materialreceiptdetails', $mrnDetData);
                                }
                            }
                        }
                    }
                }
                /** End of Create Material Receipt Note */

                $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID IN (" . join(',', $warehouses) . ") AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID = 0;
                if (!empty($exceededitems_master)) {
                    $this->load->library('sequence');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['tranferDate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['stockTransferAutoID'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['stockTransferCode'];
                    $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $exceededmatch['transactionCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['transactionCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['transactionExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                    $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                    $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                    $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                    $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                    $exceededmatch['FYBegin'] = $master ['FYBegin'];
                    $exceededmatch['FYEnd'] = $master ['FYEnd'];
                    $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                    $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                    $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];
                    $exceededmatch['createdUserGroup'] = $this->common_data['user_group'];
                    $exceededmatch['createdPCID'] = $this->common_data['current_pc'];
                    $exceededmatch['createdUserID'] = $this->common_data['current_userID'];
                    $exceededmatch['createdUserName'] = $this->common_data['current_user'];
                    $exceededmatch['createdDateTime'] = $this->common_data['current_date'];
                    $exceededmatch['documentSystemCode'] = $this->sequence->sequence_generator($exceededmatch['documentID']);
                    $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                    $exceededMatchID = $this->db->insert_id();
                }

                foreach ($details_arr as $itemid) {
                    $receivedQty = $itemid['transfer_QTY'];
                    $receivedQtyConverted = $itemid['transfer_QTY'] / $itemid['conversionRateUOM'];
                    $companyID = current_companyID();
                    $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $itemid['towarehouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                    $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                    $sumqty = array_column($exceededitems, 'balanceQty');
                    $sumqty = array_sum($sumqty);
                    if (!empty($exceededitems)) {
                        foreach ($exceededitems as $exceededItemAutoID) {
                            if ($receivedQty > 0) {
                                $balanceQty = $exceededItemAutoID['balanceQty'];
                                $updatedQty = $exceededItemAutoID['updatedQty'];
                                $balanceQtyConverted = $exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM'];
                                $updatedQtyConverted = $exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM'];
                                if ($receivedQtyConverted > $balanceQtyConverted) {
                                    $qty = $receivedQty - $balanceQty;
                                    $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                    $receivedQty = $qty;
                                    $receivedQtyConverted = $qtyconverted;
                                    $exeed['balanceQty'] = 0;
                                    //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                    $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                    $exceededmatchdetail['warehouseAutoID'] = $itemid['towarehouseAutoID'];
                                    $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetail['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                    $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                    $exceededmatchdetail['itemCost'] = $exceededItemAutoID['unitCost'];
                                    $exceededmatchdetail['totalValue'] = $balanceQtyConverted * $exceededmatchdetail['itemCost'];
                                    $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetail['createdUserGroup'] = $this->common_data['user_group'];
                                    $exceededmatchdetail['createdPCID'] = $this->common_data['current_pc'];
                                    $exceededmatchdetail['createdUserID'] = $this->common_data['current_userID'];
                                    $exceededmatchdetail['createdUserName'] = $this->common_data['current_user'];
                                    $exceededmatchdetail['createdDateTime'] = $this->common_data['current_date'];

                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                                } else {
                                    $exeed['balanceQty'] = $balanceQtyConverted - $receivedQtyConverted;
                                    $exeed['updatedQty'] = $updatedQtyConverted + $receivedQtyConverted;
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetails['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                    $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetails['warehouseAutoID'] = $itemid['towarehouseAutoID'];
                                    $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetails['exceededGLAutoID'] = $exceededItemAutoID['exceededGLAutoID'];
                                    $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                    $exceededmatchdetails['itemCost'] = $exceededItemAutoID['unitCost'];
                                    $exceededmatchdetails['totalValue'] = $receivedQtyConverted * $exceededmatchdetails['itemCost'];
                                    $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetails['createdUserGroup'] = $this->common_data['user_group'];
                                    $exceededmatchdetails['createdPCID'] = $this->common_data['current_pc'];
                                    $exceededmatchdetails['createdUserID'] = $this->common_data['current_userID'];
                                    $exceededmatchdetails['createdUserName'] = $this->common_data['current_user'];
                                    $exceededmatchdetails['createdDateTime'] = $this->common_data['current_date'];
                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                    $receivedQty = $receivedQty - $exeed['updatedQty'];
                                    $receivedQtyConverted = $receivedQtyConverted - ($updatedQtyConverted + $receivedQtyConverted);
                                }
                            }
                        }
                    }
                }
                if (!empty($exceededitems_master)) {
                    exceed_double_entry($exceededMatchID);
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Bulk Transfer Approval Failed.', 1);
            } else {
                $this->db->trans_commit();
                return array('s', 'Bulk Transfer Approval Successfully.', 1);
            }
        } else {
            return array('e', 'Item quantities are insufficient.', $items_arr);
        }
    }

    function delete_all_bulk_transfer_detail()
    {
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');
        $this->db->trans_start();
        $this->db->delete('srp_erp_stocktransferdetails_bulk', array('stockTransferAutoID' => $stockTransferAutoID));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in transfer detail delete process');
        } else {
            $this->db->trans_commit();
            return array('s', 'Bulk Transfer detail deleted successfully');
        }
    }


    function check_item_not_approved_in_document_bywarehouse()
    {
     
        $documentID = $this->input->post('documentcode');
        $DocumentAutoID = $this->input->post('DocumentAutoID');
        $warehouseAutoID = $this->input->post('warehouseAutoID');
        $DocumentDetAutoID = $this->input->post('DocumentDetAutoID');
        $companyID = current_companyID();
    
        $documentAutoID_filter_rv = '';
        $documentAutoID_filter_cinv = '';
        $documentAutoID_filter_mi = '';
        $documentAutoID_filter_st = '';

        if($documentID == 'RV'){ 
            //$documentAutoID_filter_rv = ' AND srp_erp_customerreceiptmaster.receiptVoucherAutoId !='.$DocumentAutoID.' ';
            $documentAutoID_filter_rv = ' AND srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID !='.$DocumentDetAutoID.' ';

        }
        if($documentID == 'CINV'){ 
            $documentAutoID_filter_cinv = ' AND srp_erp_customerinvoicedetails.invoiceDetailsAutoID!='.$DocumentDetAutoID.' ';
        }
        if($documentID == 'MI'){ 
            //$documentAutoID_filter_mi = ' AND srp_erp_itemissuemaster.itemIssueAutoID!='.$DocumentAutoID.' ';
            $documentAutoID_filter_mi = ' AND srp_erp_itemissuedetails.itemIssueDetailID !='.$DocumentDetAutoID.' ';

        }
        if($documentID == 'ST'){
            $documentAutoID_filter_st = ' AND srp_erp_stocktransferdetails.stockTransferDetailsID !='.$DocumentDetAutoID.' ';
        }

        $companyID = current_companyID();
        $itemAutoID = $this->input->post('itemAutoID');
        $data['usedDocs'] = $this->db->query("SELECT
		a.*,
	    srp_erp_warehousemaster.wareHouseLocation as warehouse,
	    srp_erp_itemmaster.defaultUnitOfMeasure as Uom 
FROM
	(
SELECT
	documentID,
	srp_erp_stockadjustmentmaster.stockAdjustmentAutoID AS documentAutoID,
	stockAdjustmentCode AS documentCode,
	itemAutoID,
	itemDescription,
	referenceNo,
	stockAdjustmentDate as documentDate,
    srp_erp_stockadjustmentmaster.wareHouseAutoID as wareHouseID, 
    IFNULL(( adjustmentStock /  IFNULL( conversionRateUOM,1)  ), 0 ) AS stock,
    srp_erp_stockadjustmentdetails.defaultUOMID as  UOMID
FROM
	srp_erp_stockadjustmentmaster
	LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
WHERE
	companyID = $companyID 
	AND itemAutoID = $itemAutoID 
    AND srp_erp_stockadjustmentmaster.wareHouseAutoID = $warehouseAutoID
	AND approvedYN != 1 
	
	UNION ALL
	
SELECT
	documentID,
	srp_erp_stockcountingmaster.stockCountingAutoID AS documentAutoID,
	stockCountingCode AS documentCode,
	itemAutoID,
	itemDescription,
	referenceNo,
	stockCountingDate as documentDate,
    srp_erp_stockcountingmaster.wareHouseAutoID as wareHouseID, 
    IFNULL(( adjustmentStock /  IFNULL( conversionRateUOM,1)  ), 0 ) AS stock,
    srp_erp_stockcountingdetails.defaultUOMID as  UOMID
FROM
	srp_erp_stockcountingmaster
	LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
WHERE
	companyID = $companyID 
	AND itemAutoID = $itemAutoID 
	AND srp_erp_stockcountingmaster.wareHouseAutoID = $warehouseAutoID
	AND approvedYN != 1 
	
	UNION ALL
	
SELECT
	documentID,
	srp_erp_itemissuemaster.itemIssueAutoID AS documentAutoID,
	itemIssueCode AS documentCode,
	itemAutoID,
	itemDescription,
	issueRefNo as  referenceNo,
	issueDate as documentDate,
    srp_erp_itemissuemaster.wareHouseAutoID as wareHouseID, 
    IFNULL(( qtyIssued /  IFNULL( conversionRateUOM,1)  ), 0 ) AS stock,
    srp_erp_itemissuedetails.defaultUOMID as  UOMID
FROM
	srp_erp_itemissuemaster
	LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
WHERE
	srp_erp_itemissuemaster.companyID = $companyID 
    $documentAutoID_filter_mi
	AND itemAutoID = $itemAutoID 
    AND srp_erp_itemissuemaster.wareHouseAutoID = $warehouseAutoID
	AND approvedYN != 1 
	
	UNION ALL
	
SELECT
	documentID,
	srp_erp_customerreceiptmaster.receiptVoucherAutoId AS documentAutoID,
	RVcode AS documentCode,
	itemAutoID,
	itemDescription,
	referanceNo,
	 RVdate as documentDate,
     srp_erp_customerreceiptdetail.wareHouseAutoID as wareHouseID, 
    SUM( requestedQty /  IFNULL( conversionRateUOM,1)  ) AS stock,
    srp_erp_customerreceiptdetail.defaultUOMID as  UOMID
FROM
	srp_erp_customerreceiptmaster
	LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
WHERE
	srp_erp_customerreceiptmaster.companyID = $companyID 
	AND itemAutoID = $itemAutoID 
    $documentAutoID_filter_rv
    AND srp_erp_customerreceiptdetail.wareHouseAutoID = $warehouseAutoID
	AND approvedYN != 1 
    GROUP BY
    documentAutoID,wareHouseID
	UNION ALL
	
SELECT
	documentID,
	srp_erp_customerinvoicemaster.invoiceAutoID AS documentAutoID,
	invoiceCode AS documentCode,
	itemAutoID,
	itemDescription,
	 referenceNo,
	 invoiceDate as documentDate,
     srp_erp_customerinvoicedetails.wareHouseAutoID as wareHouseID, 
    SUM( requestedQty /  IFNULL( conversionRateUOM,1)  ) AS stock,
    srp_erp_customerinvoicedetails.defaultUOMID as  UOMID
FROM
	srp_erp_customerinvoicemaster
	LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
WHERE
	srp_erp_customerinvoicemaster.companyID = $companyID 
	AND itemAutoID = $itemAutoID 
    $documentAutoID_filter_cinv
    AND srp_erp_customerinvoicedetails.wareHouseAutoID = $warehouseAutoID
	AND approvedYN != 1 
    
	GROUP BY
    srp_erp_customerinvoicemaster.invoiceAutoID,wareHouseID
	UNION ALL
	
SELECT
	documentID,
	srp_erp_deliveryorder.DOAutoID AS documentAutoID,
	DOCode AS documentCode,
	itemAutoID,
	itemDescription,
	referenceNo,
	 DODate as documentDate,
     srp_erp_deliveryorderdetails.wareHouseAutoID as wareHouseID, 
    ( deliveredQty /  IFNULL( conversionRateUOM,1)  ) AS stock,
    srp_erp_deliveryorderdetails.defaultUOMID as  UOMID
FROM
	srp_erp_deliveryorder
	LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
WHERE
	srp_erp_deliveryorder.companyID = $companyID 
	AND itemAutoID = $itemAutoID 
    AND srp_erp_deliveryorderdetails.wareHouseAutoID = $warehouseAutoID
	AND approvedYN != 1 
	
	UNION ALL
	
SELECT
	documentID,
	srp_erp_stocktransfermaster.stockTransferAutoID AS documentAutoID,
	stockTransferCode AS documentCode,
	itemAutoID,
	itemDescription,
	referenceNo,
	 tranferDate as documentDate,
     srp_erp_stocktransfermaster.from_wareHouseAutoID  as wareHouseID, 
    ( transfer_QTY /  IFNULL( conversionRateUOM,1)  ) AS stock,
    srp_erp_stocktransferdetails.defaultUOMID as  UOMID
FROM
	srp_erp_stocktransfermaster
	LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
WHERE
	srp_erp_stocktransfermaster.companyID = $companyID 
	$documentAutoID_filter_st
	AND itemAutoID = $itemAutoID 
    AND srp_erp_stocktransfermaster.from_wareHouseAutoID = $warehouseAutoID
	AND approvedYN != 1 
    ) a
    LEFT JOIN srp_erp_warehousemaster ON srp_erp_warehousemaster.wareHouseAutoID = a.wareHouseID
	LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = a.ItemAutoID
    ")->result_array();

        return $data;
    }

    function fetch_converted_price_qty_invoice()
    { 
        $companyID=$this->common_data['company_data']['company_id'];
        $itemIssueAutoID = $this->input->post('id');
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
            $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM srp_erp_itemledger where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
            $details['qty'] = $stock['currentStock'] * $conversion;
            $details['qty_pulleddoc'] =  $details['qty'] - ($pulled_stock['Unapproved_stock'] * $conversion);
            $details['parkQty'] =  $pulled_stock['Unapproved_stock'] * $conversion;


        } else {
            $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM srp_erp_itemledger where itemAutoID="' . $itemAutoID . '" ')->row_array();
            $details['qty'] = $stock['currentStock'] * $conversion;
            $details['qty_pulleddoc'] =   $details['qty'] - ($pulled_stock['Unapproved_stock'] * $conversion);
            $details['parkQty'] =  $pulled_stock['Unapproved_stock'] * $conversion;

        }
        
        return $details;
    }

    function fetch_converted_price_qty_invoice_new()
    {
        $companyID=$this->common_data['company_data']['company_id'];
        $itemIssueAutoID = $this->input->post('id');
        $itemAutoID = $this->input->post('itemAutoID');
        $uomID = $this->input->post('uomID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $itemIssueDetailID = $this->input->post('detailID');
        $documentcode=trim($this->input->post('documentcode') ?? '');

        $details = array();

        $data = $this->db->query("SELECT defaultUnitOfMeasureID, companyLocalSellingPrice,companyLocalWacAmount FROM srp_erp_itemmaster WHERE companyID = {$companyID} AND itemAutoID = {$itemAutoID}")->row_array();
        $conversion = conversionRateUOM_id($uomID, $data['defaultUnitOfMeasureID']);
        $details['conversionRate'] = $conversion;
        if(empty($conversion) || $conversion == 0) {
            $conversion = 1;
        }
        $this->load->model('Receipt_voucher_model');
        $pulled_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty_new($itemAutoID,$wareHouseAutoID,$documentcode,$itemIssueAutoID, $itemIssueDetailID);

        if(!empty($wareHouseAutoID)) {
            $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM srp_erp_itemledger where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
            $details['qty'] = $stock['currentStock'] * $conversion;
            $details['qty_pulleddoc'] =  $details['qty'] - ($pulled_stock['Unapproved_stock'] * $conversion);
            $details['parkQty'] =  $pulled_stock['Unapproved_stock'] * $conversion;


        } else {
            $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM srp_erp_itemledger where itemAutoID="' . $itemAutoID . '" ')->row_array();
            $details['qty'] = $stock['currentStock'] * $conversion;
            $details['qty_pulleddoc'] =   $details['qty'] - ($pulled_stock['Unapproved_stock'] * $conversion);
            $details['parkQty'] =  $pulled_stock['Unapproved_stock'] * $conversion;

        }
      return $details;
    }

    function update_quantity_sec_uom(){
        $documentID = $this->input->post('documentID');
        $detail_autoID = $this->input->post('detail_autoID');
        $secondaryUOMID = $this->input->post('secondaryUOMID');
        $sec_qty = $this->input->post('sec_qty');
        $data = [];
        $this->db->trans_start();

        if($documentID == 'ST'){
            $data = [
                'SUOMID' => $secondaryUOMID,
                'SUOMQty' => $sec_qty
            ];
            $this->db->where('stockTransferDetailsID', $detail_autoID);
            $this->db->update('srp_erp_stocktransferdetails', $data);
            
        }
        else if($documentID == 'JOB'){
            $data = [
                'secondaryUOMID' => $secondaryUOMID,
                'secondaryQty' => $sec_qty
            ];
            $this->db->where('jcMaterialConsumptionID', $detail_autoID);
            $this->db->update('srp_erp_mfq_jc_materialconsumption', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error while updating Secondary UOM Quantity');
        } else {
            $this->db->trans_commit();
            return array('s', 'Secondary UOM Quantity updated successfully',$sec_qty);
        }
    }

    function get_warehouse_details(){
        $warehouseID = $this->input->post('warehouseID');
        if(!empty($warehouseID)){

            $this->db->SELECT("*");
            $this->db->FROM('srp_erp_warehousemaster');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where_in('wareHouseAutoID', $warehouseID);
            $result = $this->db->get()->result_array();
            $result = array_column($result, 'wareHouseLocation');

            return $result;
        }else{
            return $result = '';
        }
    }


    public function calculate_average_purcahse_of_raw_material($begining_date,$current_date,$xvalues)
    {
        $companyID = current_companyID();
        $item = $this->input->post('item');
        $orderby = $this->input->post('orderby');
        $feilds = "";


        if (!empty($xvalues)) {
            foreach ($xvalues as $key => $val2) {
                if($orderby == 1){
                    $feilds .= "SUM(if(DATE_FORMAT(srp_erp_itemledger.documentDate,'%Y-%m-d%') = '$key',(( companyLocalAmount / companyLocalExchangeRate )/( transactionQTY / convertionRate )),0) ) as `" . $val2 . "`,";


                }elseif($orderby == 2){
                    $feilds .= "SUM(if((DATE_FORMAT(srp_erp_itemledger.documentDate,'%Y-%m-d%') > '$key'  && (DATE_FORMAT(srp_erp_itemledger.documentDate,'%Y-%m-d%')) <= DATE_ADD('$key', INTERVAL 1 week)),(( companyLocalAmount / companyLocalExchangeRate )/( transactionQTY / convertionRate )),0) ) as `" . $val2 . "`,";

                }else{
                    $feilds .= "SUM(if(DATE_FORMAT(srp_erp_itemledger.documentDate,'%Y-%m') = '$key',(( companyLocalAmount / companyLocalExchangeRate )/( transactionQTY / convertionRate )),0) ) as `" . $val2 . "`,";

                }


            }
        }

        $query = $this->db->query("SELECT 
            $feilds
            documentDate 
	    FROM `srp_erp_itemledger` 
            WHERE srp_erp_itemledger.companyID=$companyID  AND itemAutoID = $item  AND srp_erp_itemledger.documentID IN ( 'GRV', 'BSI', 'PV' )
            AND documentDate BETWEEN '$begining_date'	AND '$current_date' 
           ");

        $res = $query->result_array();

        //echo'<pre>';print_r($res);echo'</pre>';

        $data['details'] = $res;
        $data['order_by'] = $orderby;

        return $data;

    }


    function fetch_existing_batch_details(){

        $batchNumber = $this->input->post('batchNumber');
        $itemAutoID = $this->input->post('itemAutoID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $companyID = current_companyID();

        if($itemAutoID){

            $ex_batch_details = get_item_batch_details_on_warehouse($itemAutoID,$batchNumber,$wareHouseAutoID);

            if($ex_batch_details){
                return array('status'=>'exists','details'=>$ex_batch_details);
            }

            return array('status'=>'false');
        }

    }

    function load_inventory_catalogue_header(){ 

        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(requestedDate,\'' . $convertFormat . '\') AS requestedDate');
        $this->db->where('mrAutoID', $this->input->post('mrAutoID'));
        return $this->db->get('srp_erp_inventorycataloguemaster')->row_array();

    }

    function save_inventory_catalogue_detail_multiple(){

        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $mrDetailID = $this->input->post('mrDetailID');
        $mrAutoID = $this->input->post('mrAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $projectID = $this->input->post('projectID');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $transactionAmount = $this->input->post('salesPrice');
        //$a_segment = $this->input->post('a_segment');
        $comment = $this->input->post('comment');

        $this->db->select('*');
        $this->db->from('srp_erp_inventorycataloguemaster');
        $this->db->where('mrAutoID', $mrAutoID);
        $masterRecord = $this->db->get()->row_array();

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$mrDetailID) {
                $this->db->select('mrAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_inventorycataloguedetails');
                $this->db->where('mrAutoID', $mrAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Request Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            //$segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

        

            $data['mrAutoID'] = trim($this->input->post('mrAutoID') ?? '');
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['qtyRequested'] = 1;
            $data['comments'] = $comment[$key];
            $data['remarks'] = '';
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];
            //$data['segmentID'] = $masterRecord['segmentID'];
            //$data['segmentCode'] = $masterRecord['segmentCode'];
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            $data['totalValue'] = 1;
            $data['transactionAmount'] = $transactionAmount[$key];
            $data['companyLocalAmount'] = $transactionAmount[$key]/$masterRecord['companyLocalExchangeRate'];
            $data['companyReportingAmount'] = $transactionAmount[$key]/$masterRecord['companyReportingExchangeRate'];

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_inventorycataloguedetails', $data);

           
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item Request Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Request Detail :  Saved Successfully.');

        }

    }

    function delete_inventory_catalogue_item(){

        $id = $this->input->post('mrDetailID');

        $this->db->select('*');
        $this->db->from('srp_erp_inventorycataloguedetails');
        $this->db->where('mrDetailID', $id);
        $detail_arr = $this->db->get()->row_array();


        /** end update sub item master */
        $this->db->where('mrDetailID', $id);
        $result = $this->db->delete('srp_erp_inventorycataloguedetails');

        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }

    
    function fetch_template_data_MIC($mrAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        
        $this->db->select('*,DATE_FORMAT(requestedDate,\'' . $convertFormat . '\') AS requestedDate,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN 
        CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('mrAutoID', $mrAutoID);
        $this->db->from('srp_erp_inventorycataloguemaster');
        $data['master'] = $this->db->get()->row_array();
        
        
        $this->db->select('*,srp_erp_inventorycataloguedetails.comments as comments,'.$item_code_alias.'');
        $this->db->where('mrAutoID', $mrAutoID);
        $this->db->from('srp_erp_inventorycataloguedetails');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_inventorycataloguedetails.itemAutoID','left');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function inventory_catalogue_confirmation(){

        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $companyID = current_companyID();
        $currentuser = current_userID();
        $this->db->select('mrAutoID');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
        $this->db->from('srp_erp_inventorycataloguedetails');
        $result = $this->db->get()->row_array();
        if (empty($result)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return false;
        } else {
            $this->db->select('mrAutoID');
            $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_inventorycataloguemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->db->select('MRCode,documentID,DATE_FORMAT(requestedDate, "%Y") as invYear,DATE_FORMAT(requestedDate, "%m") as invMonth,requestedDate');
                $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
                $this->db->from('srp_erp_inventorycataloguemaster');
                $master_dt = $this->db->get()->row_array();

                $docDate = $master_dt['requestedDate'];
                $Comp = current_companyID();
                $companyFinanceYearID = $this->db->query("SELECT
                        period.companyFinanceYearID as companyFinanceYearID
                    FROM
                        srp_erp_companyfinanceperiod period
                    WHERE
                        period.companyID = $Comp
                    AND '$docDate' BETWEEN period.dateFrom
                    AND period.dateTo
                    AND period.isActive = 1")->row_array();

                if (empty($companyFinanceYearID['companyFinanceYearID'])) {
                    $companyFinanceYearID['companyFinanceYearID'] = NULL;
                }

                $this->load->library('sequence');
                if ($master_dt['MRCode'] == "0" || empty($master_dt['MRCode'])) {
                    $mrcode = $this->sequence->sequence_generator_fin($master_dt['documentID'], $companyFinanceYearID['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);

                    $validate_code = validate_code_duplication($mrcode, 'MRCode', trim($this->input->post('mrAutoID') ?? ''),'mrAutoID', 'srp_erp_inventorycataloguemaster');
                    if(!empty($validate_code)) {
                        $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                        return false;
                    }

                    
                    $pvCd = array(
                        'MRCode' => $mrcode
                    );
                    $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
                    $this->db->update('srp_erp_inventorycataloguemaster', $pvCd);
                } else {
                    $validate_code = validate_code_duplication($master_dt['MRCode'], 'MRCode', trim($this->input->post('mrAutoID') ?? ''),'mrAutoID', 'srp_erp_inventorycataloguemaster');
                    if(!empty($validate_code)) {
                        $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                        return false;
                    }
                }

                $this->load->library('Approvals');
                $this->db->select('mrAutoID, MRCode,requestedDate');
                $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
                $this->db->from('srp_erp_inventorycataloguemaster');
                $app_data = $this->db->get()->row_array();

                $autoApproval = get_document_auto_approval('MIC');
                if ($autoApproval == 0) {
                    $approvals_status = $this->approvals->auto_approve($app_data['mrAutoID'], 'srp_erp_inventorycataloguemaster', 'mrAutoID', 'MIC', $app_data['MRCode'], $app_data['requestedDate']);
                } elseif ($autoApproval == 1) {
                    $approvals_status = $this->approvals->CreateApproval('MIC', $app_data['mrAutoID'], $app_data['MRCode'], 'Inventory Catalogue', 'srp_erp_inventorycataloguemaster', 'mrAutoID', 0, $app_data['requestedDate']);
                } else {
                    $this->session->set_flashdata('e', 'Approval levels are not set for this document ');
                    return false;
                    exit;
                }

                if ($approvals_status == 1) {
                    $autoApproval = get_document_auto_approval('MIC');
                    if ($autoApproval == 0) {
                        $result = $this->save_material_request_approval(0, $app_data['mrAutoID'], 1, 'Auto Approved');
                        if ($result) {
                            $this->session->set_flashdata('s', 'Approvals Created Successfully ');
                            return true;
                        }
                    } else {
                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user']
                        );
                        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID') ?? ''));
                        $this->db->update('srp_erp_materialrequest', $data);
                        $this->session->set_flashdata('s', 'Approvals Created Successfully ');
                        return true;
                    }
                } else {
                    return false;
                }
            }
        }
    }


}
