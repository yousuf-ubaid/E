<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 *
 * -- =============================================
 * -- File Name : Delivery_order.php
 * -- Project Name : SME
 * -- Module Name : Sales & Marketing
 * -- Create date : 06 February 2019
 * -- Description : Delivery order management.
 *
 * --REVISION HISTORY
 *
 * -- =============================================
 **/

class Delivery_order extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helpers('buyback_helper');
        $this->load->model('dashboard_model');
        $this->load->model('Delivery_order_model');
        $this->load->model('Invoice_model');
    }

    function fetch_delivery_orders(){
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $date_from = $this->input->post('date_from');
        $date_from_convert = input_format_date($date_from, $date_format_policy);
        $date_to = $this->input->post('date_to');
        $date_to_convert = input_format_date($date_to, $date_format_policy);

        $company_id = current_companyID();
        $customer = $this->input->post('customer_code');
        $customer_filter = (!empty($customer))? " AND customerID IN ({$customer})": '';

        $date = (!empty($date_from) && !empty($date_to))? " AND ( DATE(DODate) BETWEEN '{$date_from_convert}' AND '{$date_to_convert}')" : '';

        $status = $this->input->post('status');
        $status_filter = "";
        if ($status != 'all') {
            $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            switch ($status){
                case 1:  $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";  break;
                case 2:  $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";  break;
                case 4:  $status_filter = " AND ((confirmedYN = 3 AND approvedYN != 1) or (confirmedYN = 2 AND approvedYN != 1))";  break;
            }
        }

        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND (( DOCode Like '%$search%' ESCAPE '!') OR ( DOType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%')  
                          OR (narration Like '%$sSearch%') OR (cusMas.customerName Like '%$sSearch%') OR (DODate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%') 
                          OR (referenceNo Like '%$sSearch%')) ";
        }

        $where = "ordMas.companyID = " . $company_id . $customer_filter . $date . $status_filter . $searches."";

        $str = '<b>Customer Name : </b> $2 <br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp; | &nbsp; Due Date : </b> $4 <br>  ';
        $str .= '<b>Type : </b> $5 <br> <b>Ref No : </b> $6 <br><b>Comments : </b> $1';

        $this->datatables->select('ordMas.DOAutoID as DOAutoID,ordMas.confirmedByEmpID as confirmedByEmp,DOCode,narration as narration,cusMas.customerName as cus_name,transactionCurrency as transactionCurrency, 
        transactionCurrencyDecimalPlaces,confirmedYN,approvedYN,status,ordMas.createdUserID as createdUser,DATE_FORMAT(DODate,\'' . $convertFormat . '\') AS DODate,
        DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate, DOType,isDeleted, referenceNo as referenceNo,
        (((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,
        ROUND((((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)), 2) as total_value_search');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,DOAutoID 
                                  FROM srp_erp_deliveryorderdetails GROUP BY DOAutoID) det', '(det.DOAutoID = ordMas.DOAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,DOAutoID FROM srp_erp_deliveryordertaxdetails  
                                   GROUP BY DOAutoID) addondet', '(addondet.DOAutoID = ordMas.DOAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster AS cusMas', 'cusMas.customerAutoID = ordMas.customerID', 'left');
        $this->datatables->from('srp_erp_deliveryorder AS ordMas');
        $this->datatables->add_column('invoice_detail', $str, 'trim_desc(narration),cus_name,DODate,invoiceDueDate,DOType,referenceNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"DO",DOAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"DO",DOAutoID)');
        $this->datatables->add_column('status', '$1', 'confirm_user_deliveredstatus("DO",DOAutoID,status, approvedYN, confirmedYN)');
        $this->datatables->add_column('edit', '$1', 'load_delivery_order_action(DOAutoID,confirmedYN,approvedYN,createdUser,DOCode,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function save_delivery_order_header()
    {
        $date_format_policy = date_format_policy();
        $docDate = $this->input->post('invoiceDate');
        $documentDate = input_format_date($docDate, $date_format_policy);

        $finance_year_periodYN = getPolicyValues('FPC', 'All');
        $this->form_validation->set_rules('invoiceType', 'Invoice Type', 'trim|required');
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('invoiceDate', 'Invoice Date', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer', 'trim|required');

        if($finance_year_periodYN == 1) {
            $this->form_validation->set_rules('finance_year', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('finance_period', 'Financial Period', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode( ['e', validation_errors()] ) );
        }


        if($finance_year_periodYN == 1) {
            $finance_arr = $this->input->post('finance_period');
            $financePeriod = fetchFinancePeriod($finance_arr);
            if ($documentDate >= $financePeriod['dateFrom'] && $documentDate <= $financePeriod['dateTo']) {
                echo json_encode($this->Delivery_order_model->save_delivery_order_header());
            } else {
                die( json_encode( ['e', 'Document Date not between Financial period !'] ) );
            }
        }
        else{
            echo json_encode($this->Delivery_order_model->save_delivery_order_header());
        }
    }

    function get_order_header_details()
    {
        $masterID = $this->input->post('orderAutoID');
        $masterData = $this->Delivery_order_model->get_order_header_details($masterID);

        $_POST['companyFinanceYearID'] = $masterData['companyFinanceYearID'];
        $financePeriods = $this->dashboard_model->fetch_finance_year_period();

        $this->load->model('Payable_modal');
        $_POST['customerAutoID'] = $masterData['customerID'];
        $customer_det = $this->Payable_modal->fetch_customer_currency_by_id();

        $data['masterData'] = $masterData;
        $data['financePeriods'] = $financePeriods;
        $data['customer_det'] = $customer_det;
        echo json_encode( $data );
    }

    function fetch_delivery_detail()
    {
        $masterID = $this->input->post('orderAutoID');
        $data['master'] = $this->Delivery_order_model->get_order_header_details($masterID);
        $data['invoiceAutoID'] = $masterID;
        $data['invoiceType'] = $data['master']['DOType'];
        $data['customerID'] = $data['master']['customerID'];
        $data['gl_code_arr'] = fetch_all_gl_codes();
        $data['gl_code_arr_income'] = fetch_all_gl_codes('PLI');
        $data['segment_arr'] = fetch_segment();
        $data['detail'] = $this->Invoice_model->fetch_detail();
        $data['openContractPolicy'] = getPolicyValues('OCE', 'All');
        $data['customer_con'] = $this->Invoice_model->fetch_customer_con($data['master']);
        //echo '<pre>'.$this->db->last_query().'</pre>';
        $data['tabID'] = $this->input->post('tab');
        $data['group_based_tax'] = existTaxPolicyDocumentWise('srp_erp_deliveryorder',trim($this->input->post('orderAutoID') ?? ''),'DO','DOAutoID');
        $this->load->view('system/delivery_order/delivery-order-detail.php', $data);
    }

    function fetch_direct_delivery_order_details(){
        $masterID = $this->input->post('masterID');
        echo json_encode($this->Delivery_order_model->fetch_direct_delivery_order_details($masterID));
    }

    function delete_delivery_order_master()
    {
        $masterID = trim($this->input->post('DOAutoID') ?? '');
        $document_status = document_status('DO', $masterID);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        $details = $this->db->get_where('srp_erp_deliveryorderdetails', ['DOAutoID'=> $masterID])->row_array();

        if (!empty($details)) {
            die( json_encode(['e', 'Please delete all detail records before delete this document.']) );
        }

        $documentCode = $this->db->get_where('srp_erp_deliveryorder', ['DOAutoID'=> $masterID])->row('DOCode');

        $this->db->trans_start();

        $length = strlen($documentCode);
        if($length > 1){
            $data = ['isDeleted' => 1, 'deletedEmpID' => current_userID(), 'deletedDate' => current_date()];
            $this->db->where('DOAutoID', $masterID);
            $this->db->update('srp_erp_deliveryorder', $data);
        }
        else{
            $this->db->where('DOAutoID', $masterID)->delete('srp_erp_deliveryordertaxdetails');
            $this->db->where('DOAutoID', $masterID)->delete('srp_erp_deliveryorderdetails');
            $this->db->where('DOAutoID', $masterID)->delete('srp_erp_deliveryorder');
        }

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Deleted successfully']);
        }else{
            echo json_encode(['e', 'Error in delete process.']);
        }
    }

    function add_direct_delivery_order_items(){
        //$projectExist = project_is_exist();
        $isBuyBackCompany = isBuyBack_company();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $quantityRequested = $this->input->post('quantityRequested');

        foreach ($searches as $key => $search) {


            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID[$key]);
            $service_itm = $this->db->get()->row_array();

            if($service_itm['mainCategory']!='Service'){
                $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            }
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
            /* if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            } */
            if ($isBuyBackCompany == 1) {
                $this->form_validation->set_rules("noOfItems[{$key}]", 'No Item', 'trim|required');
                $this->form_validation->set_rules("grossQty[{$key}]", 'Gross Qty', 'trim|required');
                $this->form_validation->set_rules("noOfUnits[{$key}]", 'Units', 'trim|required');
                $this->form_validation->set_rules("deduction[{$key}]", 'Deduction', 'trim|required');
            } else {
                $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            }
            if($quantityRequested[$key] == 0 && $quantityRequested[$key] != '')
            {
                echo json_encode(['e', 'Qty should be greater than 0.']);
                exit();
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $unique_msg = array_unique($msg);
            $validateMsg = array_map(function ($unique_msg) {
                return $a = $unique_msg . '</p>';
            }, array_filter($unique_msg));

            die( json_encode(['e', join('', $validateMsg)]));
        }

        $masterID = trim($this->input->post('invoiceAutoID') ?? '');

        $document_status = document_status('DO', $masterID);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        echo json_encode($this->Delivery_order_model->add_direct_delivery_order_items());
    }

    function fetch_direct_delivery_order_all_details()
    {
        $item_code_alias = "CONCAT(srp_erp_itemmaster.itemSystemCode, ' - ', srp_erp_itemmaster.seconeryItemCode) as itemSystemCode";
        $this->db->select("det.*,masTB.DOType,srp_erp_itemmaster.currentStock,srp_erp_itemmaster.mainCategory, $item_code_alias");
        $this->db->where('det.DOAutoID', trim($this->input->post('invoiceAutoID') ?? ''));
        $this->db->where('det.type', 'Item');
        $this->db->join('srp_erp_deliveryorder masTB', 'det.DOAutoID = masTB.DOAutoID');
        $this->db->join('srp_erp_itemmaster', 'det.itemAutoID = srp_erp_itemmaster.itemAutoID');
        $this->db->from('srp_erp_deliveryorderdetails det');
        $data = $this->db->get()->result_array();

        echo json_encode($data);
    }

    function fetch_delivery_order_detail(){
        $id = trim($this->input->post('order_det_id') ?? '');
        echo json_encode($this->Delivery_order_model->fetch_delivery_order_detail($id));
    }

    function update_delivery_order_item_detail()
    {
        //$projectExist = project_is_exist();
        $isBuyBackCompany = isBuyBack_company();
        $itemAutoID=$this->input->post('itemAutoID');
        $mainCategory = $this->db->get_where('srp_erp_itemmaster', ['itemAutoID'=>$itemAutoID])->row('mainCategory');
        $quantityRequested = $this->input->post('quantityRequested');


        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        if($mainCategory != 'Service') {
            $this->form_validation->set_rules("wareHouseAutoID", 'Warehouse', 'trim|required');
        }
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required');
        /* if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        } */
        if ($isBuyBackCompany == 1) {
            $this->form_validation->set_rules("noOfItems", 'No Item', 'trim|required');
            $this->form_validation->set_rules("grossQty", 'Gross Qty', 'trim|required');
            $this->form_validation->set_rules("noOfUnits", 'Units', 'trim|required');
            $this->form_validation->set_rules("deduction", 'Deduction', 'trim|required');
        } else {
            $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        }

        if($quantityRequested == 0 && $quantityRequested!= '')
        {
            echo json_encode(['e', 'Qty should be greater than 0.']);
            exit();
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }


        $masterID = trim($this->input->post('invoiceAutoID') ?? '');

        $document_status = document_status('DO', $masterID);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        echo json_encode($this->Delivery_order_model->update_delivery_order_item_detail($mainCategory));
    }

    function delete_order_item_direct(){
        $detail_id = $this->input->post('detail_id');
        $master_id = $this->db->get_where('srp_erp_deliveryorderdetails', ['DODetailsAutoID'=> $detail_id])->row('DOAutoID');

        $document_status = document_status('DO', $master_id);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        $this->db->trans_start();
        /** Update Contract Table*/
        $contractDet = $this->db->query("SELECT contractDetailsAutoID, deliveredQty FROM srp_erp_deliveryorderdetails WHERE DODetailsAutoID = {$detail_id}")->row_array();
        if(!empty($contractDet['contractDetailsAutoID'])) {
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
                        srp_erp_contractdetails.contractDetailsAutoID = {$contractDet['contractDetailsAutoID']}")->row_array();

            $balanceQty = $contractDet['deliveredQty'] + $balance['balance'];

            if($balanceQty <= 0) {
                $cont_data['invoicedYN'] = 1;
                $this->db->where('contractDetailsAutoID', $contractDet['contractDetailsAutoID']);
                $this->db->update('srp_erp_contractdetails', $cont_data);
            } else {
                $cont_data['invoicedYN'] = 0;
                $this->db->where('contractDetailsAutoID', $contractDet['contractDetailsAutoID']);
                $this->db->update('srp_erp_contractdetails', $cont_data);
            }
        }
        /** End Of Update Contract Table*/

        /** update sub item master */
        $dataTmp = [
            'isSold' => null, 'soldDocumentAutoID' => null, 'soldDocumentDetailID' => null, 'soldDocumentID' => null,
            'modifiedPCID' => current_pc(), 'modifiedUserID' => current_userID(), 'modifiedDatetime' => format_date_mysql_datetime(),
        ];

        $this->db->where('soldDocumentAutoID', $master_id)->where('soldDocumentDetailID', $detail_id)
            ->where('soldDocumentID', 'DO')->update('srp_erp_itemmaster_sub', $dataTmp);
        /** end update sub item master */

        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_deliveryorder', $master_id,'DO','DOAutoID');
        if($isGroupByTax == 1){ 
            $this->db->delete('srp_erp_taxledger', array('documentID' => 'DO','documentMasterAutoID' => $master_id,'documentDetailAutoID' => $detail_id));
        }

        $this->db->where('DODetailsAutoID', $detail_id);
        $this->db->delete('srp_erp_deliveryorderdetails');

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Item deleted successfully']);
        }else{
            echo json_encode(['e', 'Error in delete process.']);
        }
    }

    function save_order_tax_detail(){
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('masterID', 'Order ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $masterID =  $this->input->post('masterID');

        $document_status = document_status('DO', $masterID);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        $taxDetailAutoID = trim($this->input->post('taxDetailAutoID') ?? '');
        $text_type = $this->input->post('text_type');

        $this->db->select('*');
        $this->db->where('DOAutoID', $masterID);
        $this->db->where('taxMasterAutoID', $text_type);
        $tax_detail = $this->db->get('srp_erp_deliveryordertaxdetails')->row_array();
        if (!empty($tax_detail)) {
            die( json_encode(['e', 'Tax Detail added already ! ']) );
        }

        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $text_type);
        $master = $this->db->get('srp_erp_taxmaster')->row_array();


        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces,transactionCurrencyID,companyLocalCurrency, companyLocalExchangeRate,
                companyLocalCurrencyDecimalPlaces,companyReportingCurrency,companyReportingExchangeRate,companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('DOAutoID', $masterID);
        $inv_master = $this->db->get('srp_erp_deliveryorder')->row_array();
        $gldetails=fetch_gl_account_desc($master['supplierGLAutoID']);
        $dateTime = current_date();
        $data['DOAutoID']                        = $masterID;
        $data['taxMasterAutoID']                 = $text_type;
        $data['taxDescription']                  = $master['taxDescription'];
        $data['taxShortCode']                    = $master['taxShortCode'];
        $data['supplierAutoID']                  = $master['supplierAutoID'];
        $data['supplierSystemCode']              = $master['supplierSystemCode'];
        $data['supplierName']                    = $master['supplierName'];
        $data['supplierCurrencyID']              = $master['supplierCurrencyID'];
        $data['supplierCurrency']                = $master['supplierCurrency'];
        $data['supplierCurrencyDecimalPlaces']   = $master['supplierCurrencyDecimalPlaces'];
        $data['GLAutoID']                        = $master['supplierGLAutoID'];
        $data['systemGLCode']                    = $gldetails['systemAccountCode'];
        $data['GLCode']                          = $gldetails['GLSecondaryCode'];
        $data['GLDescription']                   = $gldetails['GLDescription'];
        $data['GLType']                          = $gldetails['subCategory'];
        $data['taxPercentage']                   = trim($this->input->post('percentage') ?? '');
        $data['transactionAmount']               = trim($this->input->post('amount') ?? '');
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

        $supplierCurrency      = currency_conversion($data['transactionCurrency'],$data['supplierCurrency']);
        $data['supplierCurrencyExchangeRate']    = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces']   = $supplierCurrency['DecimalPlaces'];

        $this->db->trans_start();

        if ($taxDetailAutoID) {
            $data['modifiedPCID']                    = $this->common_data['current_pc'];
            $data['modifiedUserID']                  = $this->common_data['current_userID'];
            $data['modifiedUserName']                = $this->common_data['current_user'];
            $data['modifiedDateTime']                = $dateTime;

            $this->db->where('taxDetailAutoID', $taxDetailAutoID);
            $this->db->update('srp_erp_deliveryordertaxdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                die( json_encode(['e', 'Tax Detail : ' . $data['GLDescription'] . ' update failed ' . $this->db->_error_message()]) );
            } else {
                $this->db->trans_commit();
                die( json_encode(['s', 'Tax Detail : ' . $data['GLDescription']. ' updated successfully.', 'last_id' => $taxDetailAutoID]) );
            }
        }
        else {
            $data['companyCode']        = $this->common_data['company_data']['company_code'];
            $data['companyID']          = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup']   = $this->common_data['user_group'];
            $data['createdPCID']        = $this->common_data['current_pc'];
            $data['createdUserID']      = $this->common_data['current_userID'];
            $data['createdUserName']    = $this->common_data['current_user'];
            $data['createdDateTime']    = $dateTime;
            $this->db->insert('srp_erp_deliveryordertaxdetails', $data);
            $last_id = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Tax Detail : ' . $data['GLDescription'] . ' saved failed ' . $this->db->_error_message()]);
            } else {
                $this->db->trans_commit();
                echo json_encode(['s', 'Tax Detail : ' . $data['GLDescription']. ' saved successfully.', 'last_id' => $last_id]);
            }
        }
    }

    function delete_tax_detail(){
        $taxDetailAutoID = trim($this->input->post('taxDetailAutoID') ?? '');
        $masterID = trim($this->input->post('masterID') ?? '');

        $document_status = document_status('DO', $masterID);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        $this->db->trans_start();

        $this->db->delete('srp_erp_deliveryordertaxdetails',array('taxDetailAutoID' => $taxDetailAutoID));

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in tax detail delete process']);
        } else {
            $this->db->trans_commit();
            echo json_encode(['s', 'Tax detail deleted successfully']);
        }

    }

    function customer_order_GLUpdate(){
        $this->form_validation->set_rules('PLGLAutoID', 'Cost GL Account', 'trim|required');
        if ($this->input->post('BLGLAutoID')) {
            $this->form_validation->set_rules('BLGLAutoID', 'Asset GL Account', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode( ['e', validation_errors()] ) );
        }

        $masterID = trim($this->input->post('masterID') ?? '');

        $document_status = document_status('DO', $masterID);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        $BLGLAutoID = $this->input->post('BLGLAutoID');
        $PLGLAutoID = $this->input->post('PLGLAutoID');

        $gl = fetch_gl_account_desc($PLGLAutoID);

        $data = [
            'expenseGLAutoID' => $PLGLAutoID,
            'expenseSystemGLCode' => $gl['systemAccountCode'],
            'expenseGLCode' => $gl['GLSecondaryCode'],
            'expenseGLDescription' => $gl['GLDescription'],
            'expenseGLType' => $gl['subCategory']
        ];

        if (isset($BLGLAutoID)) {
            $bl = fetch_gl_account_desc($BLGLAutoID);
            $data = array_merge($data, [
                    'revenueGLAutoID' => $BLGLAutoID,
                    'revenueGLCode' => $bl['systemAccountCode'],
                    'revenueSystemGLCode' => $bl['GLSecondaryCode'],
                    'revenueGLDescription' => $bl['GLSecondaryCode']
                ]
            );
        }

        $this->db->trans_start();

        if ($this->input->post('applyAll') == 1) {
            $this->db->where('DOAutoID', $masterID);
        } else {
            $this->db->where('DODetailsAutoID', trim($this->input->post('detailID') ?? ''));
        }
        $this->db->update('srp_erp_deliveryorderdetails ', $data);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'GL account successfully changed']);
        }else{
            echo json_encode(['e', 'Error in GL update process process ']);
        }
    }

    function update_all_item_details()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $quantityRequested = $this->input->post('quantityRequested');

        foreach ($searches as $key => $search) {

            $mainCategory = $this->db->get_where('srp_erp_itemmaster', ['itemAutoID'=>$itemAutoID[$key]])->row('mainCategory');

            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
            if($mainCategory != 'Service') {
                $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            }
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }

            if($quantityRequested[$key] == 0 && $quantityRequested[$key] != '')
            {
                echo json_encode(['e', 'Qty should be greater than 0.']);
                exit();
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $unique_msg = array_unique($msg);
            $validateMsg = array_map(function ($unique_msg) {
                return $a = $unique_msg . '</p>';
            }, array_filter($unique_msg));

            die( json_encode(['e', join('', $validateMsg)]));
        }

        $masterID = trim($this->input->post('invoiceAutoID') ?? '');

        $document_status = document_status('DO', $masterID);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        echo json_encode($this->Delivery_order_model->update_all_item_details());
    }

    function load_order_confirmation_view()
    {
        $orderID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('orderAutoID') ?? '');

        $this->load->library('NumberToWords');

        $response_type = $this->input->post('html');
// createdUserID
// confirmedByEmpID
        $data['html'] = $response_type;
        $data['approval'] = $this->input->post('approval');

        $data['extra'] = $this->Delivery_order_model->fetch_delivery_order_full_details($orderID);
        $data['signature'] = (!$response_type)? fetch_signature_level('DO'): '';
        $data['logo'] = ($response_type)? htmlImage: mPDFImage;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('DO', $orderID);

        $where = [ 'companyID' => current_companyID(), 'documentID' => 'DO' ];
        $printHeaderFooterYN = $this->db->get_where('srp_erp_documentcodemaster', $where)->row('printHeaderFooterYN'); /*Header*/
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $doc_code = $data['extra']['master']['DOCode'];
        $data['doc_code'] = str_replace('/','-',$doc_code);
        $data['isGroupByTax'] = existTaxPolicyDocumentWise('srp_erp_deliveryorder',trim($orderID),'DO','DOAutoID');
        if ($response_type) {
            $html = $this->load->view('system/delivery_order/delivery-order-print-view', $data, true);
            echo $html;
        } else {
            $this->load->library('pdf');
            $print_link = print_template_pdf('DO','system/delivery_order/delivery-order-print');
            $paper_size = print_template_paper_size('DO','A4');

            $view = $this->load->view($print_link, $data, true);
            $this->pdf->printed($view, $paper_size, 1, $printHeaderFooterYN);
        }
    }

    function order_confirmation()
    {
        $orderID = trim($this->input->post('orderAutoID') ?? '');
        $companyID= current_companyID();
        $detail_records = $this->db->get_where('srp_erp_deliveryorderdetails', ['DOAutoID'=> $orderID])->result_array();
        if (empty($detail_records)) {
            die( json_encode(['w', 'There are no records to confirm this document!']));
        }

        $document_status = document_status('DO', $orderID);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        $deliveryOrderDetail = $this->db->query("SELECT
        GROUP_CONCAT( itemAutoID ) AS itemAutoID 
        FROM
        srp_erp_deliveryorderdetails 
        WHERE
        companyID = $companyID
        AND DOAutoID = $orderID")->row('itemAutoID');
        if(!empty($deliveryOrderDetail)){ 
                $wacTransactionAmountValidation  = fetch_itemledger_transactionAmount_validation("$deliveryOrderDetail");
                if(!empty($wacTransactionAmountValidation)){ 
                    die( json_encode(['e', 'Below items are with negative wac amount',$wacTransactionAmountValidation]));

                }

        }

        

        $this->load->library('approvals');

        $this->db->select('documentID,DOCode,DATE_FORMAT(DODate, "%Y") as invYear,DATE_FORMAT(DODate, "%m") as invMonth,companyFinanceYearID, DODate ');
        $this->db->where('DOAutoID', $orderID);
        $this->db->from('srp_erp_deliveryorder');
        $master_dt = $this->db->get()->row_array();
        $this->load->library('sequence');

        $order_code = $master_dt['DOCode'];

        $this->db->trans_start();

        if(strlen($order_code) == 1){ /*Document code generation*/
            $location_wise_code_generation = getPolicyValues('LDG', 'All');

            if($location_wise_code_generation == 1) {
                $location_emp = trim($this->common_data['emplanglocationid']);
                if($location_emp != '') {
                    $order_code = $this->sequence->sequence_generator_location('DO', $master_dt['companyFinanceYearID'], $location_emp, $master_dt['invYear'], $master_dt['invMonth']);
                }
                else {
                    die( json_encode(['w', 'Location is not assigned for current employee']));
                }
            }
            else {
                $order_code = $this->sequence->sequence_generator_fin('DO', $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
            }
            $validate_code = validate_code_duplication($order_code, 'DOCode', $orderID,'DOAutoID', 'srp_erp_deliveryorder');
            if(!empty($validate_code)) {
                die( json_encode(['e', 'The document Code Already Exist.(' . $validate_code . ')']) );
            }

            $this->db->where('DOAutoID', $orderID)->update('srp_erp_deliveryorder', ['DOCode' => $order_code]);
        } else {
            $validate_code = validate_code_duplication($master_dt['DOCode'], 'DOCode', $orderID,'DOAutoID', 'srp_erp_deliveryorder');
            if(!empty($validate_code)) {
                die( json_encode(['e', 'The document Code Already Exist.(' . $validate_code . ')']) );
            }
        }

        
        /* reupdate partk Qty */
        
        $this->load->model('Receipt_voucher_model');
        $deliveryOrderDetails = $this->db->query("SELECT DODetailsAutoID,itemAutoID  ,wareHouseAutoID FROM srp_erp_deliveryorderdetails 
            WHERE companyID = $companyID AND DOAutoID = $orderID GROUP BY srp_erp_deliveryorderdetails.itemAutoID 
            ")->result_array();
        foreach($deliveryOrderDetails as $detail){
            $pulled_stock = $this->Receipt_voucher_model->fetch_pulled_document_qty($detail['itemAutoID'],$detail['wareHouseAutoID'], 'DO',$orderID);
            $data['parkQty'] =  $pulled_stock['Unapproved_stock'];

            $this->db->where('DODetailsAutoID', $detail['DODetailsAutoID']);
            $this->db->update('srp_erp_deliveryorderdetails', $data);
        }
        /* end reupdate partk Qty */

        $company_id = current_companyID();
        /* $item_low_qty = $this->db->query("SELECT ware_house.itemAutoID, ware_house.currentStock, SUM( detTB.deliveredQty / detTB.conversionRateUOM ) AS qty,
                             round(( IFNULL( ware_house.currentStock,0)- IFNULL(SUM( detTB.deliveredQty / detTB.conversionRateUOM ),0)  ),4)  AS stock, detTB.wareHouseAutoID
                              FROM srp_erp_deliveryorderdetails AS detTB 
                              LEFT JOIN ( SELECT SUM(transactionQTY/convertionRate) AS currentStock, wareHouseAutoID, itemAutoID 
                              FROM srp_erp_itemledger WHERE companyID = {$company_id} GROUP BY wareHouseAutoID, itemAutoID
                              ) AS ware_house ON ware_house.itemAutoID = detTB.itemAutoID AND detTB.wareHouseAutoID = ware_house.wareHouseAutoID
                              JOIN srp_erp_itemmaster itm_mas ON detTB.itemAutoID = itm_mas.itemAutoID
                              WHERE DOAutoID = {$orderID} AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' )
                              GROUP BY itemAutoID
                              HAVING stock < 0")->result_array(); */
        $item_low_qty = $this->db->query("SELECT 	round(( ( IFNULL( ware_house.currentStock, 0 )- IFNULL( SUM( detTB.parkQty / detTB.conversionRateUOM ), 0 )  ) -IFNULL(SUM( detTB.deliveredQty / detTB.conversionRateUOM ),0) ), 4 ) AS stock
                               FROM srp_erp_deliveryorderdetails AS detTB 
                               LEFT JOIN ( SELECT SUM(transactionQTY/convertionRate) AS currentStock, wareHouseAutoID, itemAutoID 
                               FROM srp_erp_itemledger WHERE companyID = {$company_id} GROUP BY wareHouseAutoID, itemAutoID
                               ) AS ware_house ON ware_house.itemAutoID = detTB.itemAutoID AND detTB.wareHouseAutoID = ware_house.wareHouseAutoID
                               JOIN srp_erp_itemmaster itm_mas ON detTB.itemAutoID = itm_mas.itemAutoID
                               WHERE DOAutoID = {$orderID} AND ( mainCategory != 'Service' AND mainCategory != 'Non Inventory' )
                               GROUP BY detTB.itemAutoID
                               HAVING stock < 0")->result_array();                      

        if (!empty($item_low_qty)) {
            die( json_encode(['e', 'Some Item quantities are not sufficient to confirm this transaction.', 'in-suf-items'=>$item_low_qty, 'in-suf-qty'=>'Y']));
        }

        $validation = $this->Delivery_order_model->on_delivery_order_confirmation($orderID);
        if($validation[0] == 'e'){
            die( json_encode($validation));
        }

        $autoApproval = get_document_auto_approval('DO');
        $order_date = $master_dt['DODate'];

        $approvals_status = null;
        if($autoApproval == 0){
            $auto_approve_status = $this->approvals->auto_approve($orderID, 'srp_erp_deliveryorder','DOAutoID', 'DO', $order_code, $order_date);
            /*if ($auto_approve_status == 1) {*/
                /*If delivery order auto approval successfully approved*/
            /*    return $this->Delivery_order_model->on_delivery_order_approval($orderID);
            }*/
            /*else{
                die( json_encode(['e', 'Error in auto approval process.']));
            }*/
        }elseif($autoApproval == 1){
            $approvals_status = $this->approvals->CreateApproval('DO', $orderID, $order_code, 'Invoice', 'srp_erp_deliveryorder', 'DOAutoID',0, $order_date);
        }else{
            die( json_encode(['e', 'Approval levels are not set for this document']));
        }

        if($approvals_status == 3){
            die( json_encode(['w', 'There are no users exist to perform approval for this document.']) );
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            die( json_encode(['e', 'Delivery order : ' . $order_code . ' confirmation failed ' . $this->db->_error_message()]) );
        } else {
            $this->db->trans_commit();

            reupdate_companylocalwac('srp_erp_deliveryorderdetails',$orderID,'DOAutoID','companyLocalWacAmount');
            die( json_encode(['s', 'Delivery order : ' . $order_code . ' confirmed successfully']) );
        }
    }

    function refer_back_delivery_order()
    {
        $masterID = $this->input->post('DOAutoID');

        $document_status = document_status('DO', $masterID, 1);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($masterID, 'DO');
        if ($status == 1) {
            $data['status'] = 0;
            $this->db->where('DOAutoID', $masterID);
            $this->db->update('srp_erp_deliveryorder', $data);
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }
    }

    function fetch_delivery_order_approval()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */

        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $current_user_id = current_userID();
        if($approvedYN == 0){
            $this->datatables->select('masTB.DOAutoID as DOAutoID,DOCode,narration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,DATE_FORMAT(DODate,\'' . $convertFormat . '\') AS DODate,
                (((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,transactionCurrencyDecimalPlaces,
                transactionCurrency,srp_erp_customermaster.customerName as customerName,masTB.referenceNo as referenceNo', false);
            $this->datatables->join('(SELECT SUM(deliveredTransactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,DOAutoID FROM srp_erp_deliveryorderdetails GROUP BY DOAutoID) det', '(det.DOAutoID = masTB.DOAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,DOAutoID FROM srp_erp_deliveryordertaxdetails  GROUP BY DOAutoID) addondet', '(addondet.DOAutoID = masTB.DOAutoID)', 'left');
            $this->datatables->from('srp_erp_deliveryorder masTB');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = masTB.customerID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = masTB.DOAutoID AND srp_erp_documentapproved.approvalLevelID = masTB.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = masTB.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'DO');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'DO');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('masTB.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $current_user_id);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('DOCode_str', '$1', 'approval_change_modal(DOCode,DOAutoID,documentApprovedID,approvalLevelID,approvedYN,DO,0)');
            $this->datatables->add_column('confirmed', "<div style='text-align: center'>Level $1</div>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"DO",DOAutoID)');
            $this->datatables->add_column('edit', '$1', 'delivery_order_approval_action(DOAutoID,approvalLevelID,approvedYN,documentApprovedID,DO)');
            echo $this->datatables->generate();
        }
        else {
            $this->datatables->select('masTB.DOAutoID as DOAutoID,DOCode,narration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,DATE_FORMAT(DODate,\'' . $convertFormat . '\') AS DODate,
                (((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,transactionCurrencyDecimalPlaces,
                transactionCurrency,srp_erp_customermaster.customerName as customerName,masTB.referenceNo as referenceNo', false);
            $this->datatables->join('(SELECT SUM(deliveredTransactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,DOAutoID FROM srp_erp_deliveryorderdetails GROUP BY DOAutoID) det', '(det.DOAutoID = masTB.DOAutoID)', 'left');
            $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,DOAutoID FROM srp_erp_deliveryordertaxdetails GROUP BY DOAutoID) addondet', '(addondet.DOAutoID = masTB.DOAutoID)', 'left');
            $this->datatables->from('srp_erp_deliveryorder masTB');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = masTB.customerID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = masTB.DOAutoID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'DO');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('masTB.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $current_user_id);
            $this->datatables->group_by('masTB.DOAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(DOCode,DOAutoID,documentApprovedID,approvalLevelID,approvedYN,DO,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('DOCode_str', '$1', 'approval_change_modal(DOCode,DOAutoID,documentApprovedID,approvalLevelID,approvedYN,DO,0)');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"DO",DOAutoID)');
            $this->datatables->add_column('edit', '$1', 'delivery_order_approval_action(DOAutoID,approvalLevelID,approvedYN,documentApprovedID,DO)');
            echo $this->datatables->generate();
        }

    }

    function approve_delivery_order()
    {
        $auto_id = trim($this->input->post('orderAutoID') ?? '');
        $level_id = trim($this->input->post('level') ?? '');
        $status = trim($this->input->post('status') ?? '');

        $this->form_validation->set_rules('orderAutoID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_rules('level', 'Level', 'trim|required');
        if ($status == 2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $approvedYN = checkApproved($auto_id, 'DO', $level_id);
        if ($approvedYN) {
            die( json_encode(['w', 'Document already approved', 1]) );
        }

        $document_status = $this->db->get_where('srp_erp_deliveryorder', ['DOAutoID'=>$auto_id])->row('confirmedYN');
        if ($document_status == 2) {
            die( json_encode(['w', 'Document already rejected', 1]) );
        }

        echo json_encode($this->Delivery_order_model->approve_delivery_order());
    }

    function delivery_order_account_review($orderID){
        $this->load->model('Double_entry_model');
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_delivery_order($orderID);

        $data['customer'] = 'Customer Name';
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', 1);
    }

    function save_con_base_items()
    {
        $ids = $this->input->post('DetailsID');
        foreach ($ids as $key => $id) {
            $num = ($key + 1);
            $this->form_validation->set_rules("DetailsID[{$key}]", "Line {$num} ID", 'trim|required');
            $this->form_validation->set_rules("amount[{$key}]", "Line {$num} Amount", 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", "Line {$num} WareHouse", 'trim|required');
            $this->form_validation->set_rules("qty[{$key}]", "Line {$num} QTY", 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        echo json_encode($this->Delivery_order_model->save_con_base_items());
    }
    function invoiceloademail()
    {
        echo json_encode($this->Delivery_order_model->loademail());
    }
    function load_mail_history(){
        $this->datatables->select('autoID,srp_erp_documentemailhistory.documentID,documentAutoID,sentByEmpID,toEmailAddress,sentDateTime,srp_employeesdetails.Ename2 as ename,srp_erp_contractmaster.contractCode')
            ->where('srp_erp_documentemailhistory.companyID', $this->common_data['company_data']['company_id'])
            ->where_in('srp_erp_documentemailhistory.documentID', array('DO'))
            ->where('srp_erp_documentemailhistory.documentAutoID', $this->input->post('DoAutoID'))
            ->join('srp_employeesdetails','srp_erp_documentemailhistory.sentByEmpID = srp_employeesdetails.EIdNo','left')
            ->join('srp_erp_contractmaster','srp_erp_contractmaster.contractAutoID = srp_erp_documentemailhistory.documentAutoID','left')
            ->from('srp_erp_documentemailhistory');
        echo $this->datatables->generate();
    }
    function send_do_email()
    {
        $this->form_validation->set_rules('email', 'email', 'trim|valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Delivery_order_model->send_do_email());
        }
    }
    function deliveryorder_collectionheader()
    {
        echo json_encode($this->Delivery_order_model->deliveryorder_collectionheader());
    }
    function update_deliveryorder_collectiondetails()
    {
        $this->form_validation->set_rules('DOAutoIddo', 'Delivery Order', 'trim|required');
        $status = trim($this->input->post('statusdo') ?? '');

        if ($status == 1 || $status == 2) {
            $this->form_validation->set_rules('driver_name', 'Driver Name', 'trim|required');
            $this->form_validation->set_rules('delivereddatedo', 'Delivery  Date', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Delivery_order_model->update_deliveryorder_collectiondetails());
        }
    }

    function fetch_DO_delivered_item_details()
    {
        echo json_encode($this->Delivery_order_model->fetch_DO_delivered_item_details());
    }

    function check_DO_matched()
    {
        echo json_encode($this->Delivery_order_model->check_DO_matched());
    }

    function generate_invoice_from_DO_header()
    {
        $date_format_policy = date_format_policy();
        $INV_Dates = $this->input->post('INV_Date');
        $INV_Date = input_format_date($INV_Dates, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');

        $this->form_validation->set_rules('INV_Date', 'Invoice Date', 'trim|required');
        $this->form_validation->set_rules('DOAutoID', 'DO AutoID', 'trim|required');
        $this->form_validation->set_rules('INV_Due_Date', 'Invoice Due Date', 'trim|required');

        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e',validation_errors()));
        } else {
            if ($financeyearperiodYN == 1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($INV_Date >= $financePeriod['dateFrom'] && $INV_Date <= $financePeriod['dateTo']) {
                    echo json_encode($this->Delivery_order_model->generate_invoice_from_DO_header());
                } else {
                    echo json_encode(array('e', 'Invoice Date is not between Financial period!'));
                }
            }else{
                echo json_encode($this->Delivery_order_model->generate_invoice_from_DO_header());
            }
        }
    }
    function load_order_confirmation_view_delivered()
    {
        $orderID = ($this->input->post('DOAutoID')?$this->input->post('DOAutoID'):$this->input->post('orderAutoID'));

        $this->load->library('NumberToWords');

        $response_type = $this->input->post('html');

        $data['html'] = $response_type;
        $data['approval'] = $this->input->post('approval');

        $data['extra'] = $this->Delivery_order_model->fetch_delivery_order_full_details($orderID);
        $data['signature'] = (!$response_type)? fetch_signature_level('DO'): '';
        $data['logo'] = ($response_type)? htmlImage: mPDFImage;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('DO', $orderID);

        $where = [ 'companyID' => current_companyID(), 'documentID' => 'DO' ];
        $conf_printHeaderFooterYN = $this->db->get_where('srp_erp_documentcodemaster', $where)->row('printHeaderFooterYN');
        $data['isGroupByTax'] = existTaxPolicyDocumentWise('srp_erp_deliveryorder',trim($orderID),'DO','DOAutoID');
        $printHeaderFooterYN = (!empty($conf_printHeaderFooterYN))? $conf_printHeaderFooterYN: 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $doc_code = $data['extra']['master']['DOCode'];
        $data['doc_code'] = str_replace('/','-',$doc_code);

        $html = $this->load->view('system/delivery_order/delivered-view', $data, true);
        echo $html;

    }

    function fetch_line_tax_and_vat()
    {
        echo json_encode($this->Delivery_order_model->fetch_line_tax_and_vat());
    }

    function load_line_tax_amount()
    {
        echo json_encode($this->Delivery_order_model->load_line_tax_amount());
    }

    function export_excel_do()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Delivery Order');
        $this->load->database();
        $data = $this->Delivery_order_model->fetch_do_excel();

        $header = ['#', 'Code', 'Document Date', 'Due Date', 'Customer Name', 'Type', 'Reference Number', 'Comment', 'Currency', 'Amount', 'Confirmed Status', 'Approved Status', 'Status'];
       
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->mergeCells("A1:E1");
        $this->excel->getActiveSheet()->mergeCells("A2:E2");

        $this->excel->getActiveSheet()->getStyle('A4:M4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Delivery Order List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:M4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:M4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $y=6;
        foreach ($data as $val) {
            $this->excel->getActiveSheet()->setCellValue('A' . $y, $val['Num']);
            $this->excel->getActiveSheet()->setCellValue('B' . $y, $val['code']);
            $this->excel->getActiveSheet()->setCellValue('C' . $y, $val['documentDate']);
            $this->excel->getActiveSheet()->setCellValue('D' . $y, $val['dueDate']);
            $this->excel->getActiveSheet()->setCellValue('E' . $y, $val['customerName']);
            $this->excel->getActiveSheet()->setCellValue('F' . $y, $val['type']);
            $this->excel->getActiveSheet()->setCellValue('G' . $y, $val['referenceNumber']);
            $this->excel->getActiveSheet()->setCellValue('H' . $y, $val['comment']);
            $this->excel->getActiveSheet()->setCellValue('I' . $y, $val['currency']);
            $this->excel->getActiveSheet()->setCellValue('J' . $y, $val['amount']);
            $format_decimal = ( $val['decimalPlace'] == 3)? '#,##0.000': '#,##0.00';
            $this->excel->getActiveSheet()->getStyle('J' . $y)->getNumberFormat()->setFormatCode($format_decimal);

            $this->excel->getActiveSheet()->setCellValue('K' . $y, $val['confirmed']);
            $this->excel->getActiveSheet()->setCellValue('L' . $y, $val['approved']);
            $this->excel->getActiveSheet()->setCellValue('M' . $y, $val['status']);
            $y++;
        }

        $filename = 'Delivery Order Details.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }
}