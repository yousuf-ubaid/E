<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Grv_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function save_grv_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $gvDte = $this->input->post('grvDate');
        $grvDate = input_format_date($gvDte, $date_format_policy);
        $deveedDte = $this->input->post('deliveredDate');
        $deliveredDate = input_format_date($deveedDte, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');

        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $delivery_location = explode('|', trim($this->input->post('delivery_location') ?? ''));
        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplierID') ?? ''));

        if($financeyearperiodYN==1) {
            $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        }else{
            $financeYearDetails=get_financial_year($grvDate);
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
            $financePeriodDetails=get_financial_period_date_wise($grvDate);

            if(empty($financePeriodDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
        
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $data['grvType'] = trim($this->input->post('grvType') ?? '');
        $data['documentID'] = trim($this->input->post('documentID') ?? '');
        $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');
        $data['supplierID'] = trim($this->input->post('supplierID') ?? '');

        $narration = ($this->input->post('narration'));
        $data['grvNarration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        $data['grvDate'] = $grvDate;
        $data['deliveredDate'] = $deliveredDate;
        $data['grvDocRefNo'] = trim($this->input->post('referenceno') ?? '');
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
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['wareHouseAutoID'] = trim($this->input->post('location') ?? '');
        $data['wareHouseCode'] = trim($delivery_location[0] ?? '');
        $data['wareHouseLocation'] = trim($delivery_location[1] ?? '');
        $data['wareHouseDescription'] = trim($delivery_location[2] ?? '');
        $data['isGroupBasedTax'] =  getPolicyValues('GBT', 'All');
        $data['isconsignment'] = trim($this->input->post('isconsignment') ?? 0);
        $data['consignmentWarehouseID'] = trim($this->input->post('conID') ?? '');

        $warehouseAutoID = trim($this->input->post('location') ?? '');
        $companyID = current_companyID();
        $mfqWarehouseAutoID = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster WHERE warehouseAutoID = {$warehouseAutoID} AND companyID = {$companyID}")->row('mfqWarehouseAutoID');

        if($mfqWarehouseAutoID) {
            $data['jobID'] = trim($this->input->post('jobID') ?? '');
            $data['jobNo'] = trim($this->input->post('jobNumber') ?? '');
        } else {
            $data['jobID'] = null;
            $data['jobNo'] = null;
        }

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

        if (trim($this->input->post('grvAutoID') ?? '')) {
            $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
            $this->db->update('srp_erp_grvmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', $data['documentID'] . ' for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', $data['documentID'] . ' for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('grvAutoID'), 'mfqWarehouseAutoID' => $mfqWarehouseAutoID);
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_grvmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', $data['documentID'] . ' for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', $data['documentID'] . ' for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id, 'mfqWarehouseAutoID' => $mfqWarehouseAutoID);
            }
        }
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function delete_grv()
    {
        $masterID = trim($this->input->post('grv_auto_id') ?? '');
        $this->db->select('*');
        $this->db->from('srp_erp_grvdetails');
        $this->db->where('grvAutoID', trim($this->input->post('grv_auto_id') ?? ''));
        $datas = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_grv_addon');
        $this->db->where('grvAutoID', trim($this->input->post('grv_auto_id') ?? ''));
        $dataa = $this->db->get()->row_array();

        if (!empty($datas) || !empty($dataa)) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {

            /* Added */
            $documentCode = $this->db->get_where('srp_erp_grvmaster', ['grvAutoID'=> $masterID])->row('grvPrimaryCode');
            $this->db->trans_start();

            $length = strlen($documentCode);
            if($length > 1){
                $data = array(
                    'jobID' => null,
                    'jobNo' => null,
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('grvAutoID', $masterID);
                $this->db->update('srp_erp_grvmaster', $data);
            }
            else{
                $this->db->where('grvAutoID', $masterID)->delete('srp_erp_grv_addon');
                $this->db->where('grvAutoID', $masterID)->delete('srp_erp_grvdetails');
                $this->db->where('grvAutoID', $masterID)->delete('srp_erp_grvmaster');
            }

            $this->db->trans_complete();
            if($this->db->trans_status() == true){
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }else{
                $this->session->set_flashdata('e', 'Error in delete process.');

                return false;
            }
            /* End  */
        }
    }

    function delete_grv_detail()
    {
        $grvDetailID = trim($this->input->post('grvDetailsID') ?? '');
        $companyID = current_companyID();

        $this->db->select('purchaseOrderDetailsID, poLogisticID');
        $this->db->where('grvDetailsID', $grvDetailID);
        $Detail = $this->db->get('srp_erp_grvdetails')->row_array();

        if ($Detail && $Detail['poLogisticID'] == null) {

            $grvMasterAutoID = $this->db->query("select 
                                             grvAutoID 
                                             from 
                                             srp_erp_grvdetails 
                                             where 
                                             companyID = $companyID 
                                             AND grvDetailsID = $grvDetailID ")->row('grvAutoID');


            $isTaxGroupPolicyEnable = existTaxPolicyDocumentWise('srp_erp_grvmaster',$grvMasterAutoID,'GRV','grvAutoID');

            if($isTaxGroupPolicyEnable == 1){
                $this->db->delete('srp_erp_taxledger', array('documentID' => 'GRV','documentMasterAutoID'=>$grvMasterAutoID,'documentDetailAutoID'=>$grvDetailID));
            }

            $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentDetailID' => $grvDetailID, 'receivedDocumentID' => 'GRV'));

            $query = fetch_po_detail_status($Detail['purchaseOrderDetailsID']);

            if ($query) {
                if ($query['receivedQty'] > 0 || $query['bsireceivedQty'] > 0) {
                    $po_data['GRVSelectedYN'] = 1;
                } else {
                    $po_data['GRVSelectedYN'] = 0;
                }

                if ($query['requestedQty'] <= (floatval($query['receivedQty']) + floatval($query['bsireceivedQty']))) {
                    $po_data['goodsRecievedYN'] = 1;
                } else {
                    $po_data['goodsRecievedYN'] = 0;
                }
            } else {
                $po_data['GRVSelectedYN'] = 0;
                $po_data['goodsRecievedYN'] = 0;
            }

            $this->db->where('purchaseOrderDetailsID', $Detail['purchaseOrderDetailsID']);
            $this->db->update('srp_erp_purchaseorderdetails', $po_data);
        }

        $this->db->delete('srp_erp_grvdetails', array('grvDetailsID' => $grvDetailID));
        $this->db->delete('srp_erp_grv_addon', array('impactFor' => $grvDetailID));


        return true;
    }

    function load_grv_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('srp_erp_grvmaster.*,DATE_FORMAT(grvDate,\'' . $convertFormat . '\') AS grvDate,DATE_FORMAT(deliveredDate,\'' . $convertFormat . '\') AS deliveredDate, srp_erp_mfq_warehousemaster.mfqWarehouseAutoID AS mfqWarehouseAutoID,grvType');
        $this->db->from('srp_erp_grvmaster');
        $this->db->join('srp_erp_mfq_warehousemaster', 'srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_grvmaster.wareHouseAutoID', 'LEFT');
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        return $this->db->get()->row_array();
    }

    function fetch_detail()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        $master = $this->db->get()->row_array();

        if($master && $master['grvType'] === 'LOG'){
            return $this->getGrvLogisticDetail(trim($this->input->post('grvAutoID') ?? ''));
        }

        $secondaryCode = getPolicyValues('SSC', 'All');
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }

        $this->db->select('srp_erp_grvdetails.*, srp_erp_activity_code_main.activity_code as activityCodeName, CONCAT_WS(\' - \',IF(LENGTH(srp_erp_grvdetails.itemDescription),srp_erp_grvdetails.itemDescription,NULL),IF(LENGTH(srp_erp_grvdetails.`comment`),srp_erp_grvdetails.`comment`,NULL))as itemdes,srp_erp_itemmaster.isSubitemExist,CONCAT_WS(
	\' - Part No : \',
IF
	( LENGTH( srp_erp_grvdetails.`comment` ), `srp_erp_grvdetails`.`comment`, NULL ),

IF
	( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
	) AS Itemdescriptionpartno,IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS Description,IFNULL( srp_erp_grvdetails.taxAmount, 0) AS taxAmount,,'.$item_code .'');
        $this->db->from('srp_erp_grvdetails');
        $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_grvdetails.activityCodeID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_grvdetails.itemAutoID', 'left');
        $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_grvdetails.taxCalculationformulaID', 'left');
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        return $this->db->get()->result_array();
    }

    /**
     * Get grv logistic detail
     *
     * @param integer $grvAutoID
     * @return array
     */
    public function getGrvLogisticDetail($grvAutoID)
    {
        $this->db->select('srp_erp_grvdetails.*,srp_erp_purchase_order_logistic.*');
        $this->db->from('srp_erp_grvdetails');
        $this->db->join('srp_erp_purchase_order_logistic', 'srp_erp_purchase_order_logistic.poLogisticID = srp_erp_grvdetails.poLogisticID', 'inner');
        $this->db->where('grvAutoID', $grvAutoID);
        return $this->db->get()->result_array();
    }

    function fetch_grv_detail()
    {
        $purchaseOrderID = trim($this->input->post('purchaseOrderID') ?? '');
        $grvDetailsID = trim($this->input->post('grvDetailsID') ?? '');

        $data = $this->db->query("SELECT
                                 `srp_erp_grvdetails`.*,
                                 `srp_erp_itemmaster`.`seconeryItemCode` AS `seconeryItemCode`,
                                 ((TRIM( ROUND( IFNULL( srp_erp_purchaseorderdetails.requestedQty, 0 ), 4 ) ) + 0 	) )	-  ((TRIM( ROUND( IFNULL( supdetail.supqty, 0 ), 4 ) ) + 0 	) + TRIM( ROUND( IFNULL( grvdetail.grvqty, 0 ), 4 ) ) + 0 ) AS qtybalance, 
                                 IFNULL(srp_erp_taxcalculationformulamaster.Description,'-') as Description,
                                 IFNULL(srp_erp_grvdetails.taxCalculationformulaID,0) as taxtype,
                                 IFNULL(srp_erp_grvdetails.taxAmount,0) as taxAmountLedger
                    
                                 FROM
                                 `srp_erp_grvdetails`
                                 LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_grvdetails`.`itemAutoID`
                                 LEFT JOIN srp_erp_purchaseorderdetails ON srp_erp_grvdetails.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID
                                 LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_grvdetails.taxCalculationformulaID
                                 LEFT JOIN ( SELECT purchaseOrderDetailsID, SUM( requestedQty ) AS supqty FROM srp_erp_paysupplierinvoicedetail GROUP BY purchaseOrderDetailsID ) supdetail ON `supdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
                                 LEFT JOIN ( SELECT purchaseOrderDetailsID, SUM( receivedQty ) AS grvqty FROM srp_erp_grvdetails where grvDetailsID !=  '{$grvDetailsID}'  GROUP BY purchaseOrderDetailsID ) grvdetail ON `grvdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID` 
                                 WHERE
                                `grvDetailsID` = '{$grvDetailsID}'")->row_array();

        return $data;
    }

    function fetch_supplier_po($master)
    {
        $convertFormat = convert_date_format_sql();
        $supplierID = $master['supplierID'];
        $currencyID = $master['transactionCurrencyID'];
        $segmentID = $master['segmentID'];
        $date = format_date($master['grvDate']);
        return $this->db->query("SELECT srp_erp_purchaseordermaster.purchaseOrderID,srp_erp_purchaseordermaster.purchaseOrderCode,DATE_FORMAT(srp_erp_purchaseordermaster.documentDate, '$convertFormat') AS documentDate FROM srp_erp_purchaseorderdetails INNER JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseorderdetails.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID LEFT JOIN srp_erp_grvdetails ON srp_erp_grvdetails.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID LEFT JOIN (
SELECT
srp_erp_purchaseorderdetails.`purchaseOrderID`,
	SUM(`srp_erp_purchaseorderdetails`.requestedQty - TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM (ROUND(IFNULL( supdetail.supqty, 0 ) + IFNULL( grvdetail.grvqty, 0 ), 2 )))))) AS receivedQty
FROM
	`srp_erp_purchaseorderdetails`
LEFT JOIN (
	SELECT
		purchaseOrderDetailsID,
		SUM(requestedQty) AS supqty
	FROM
		srp_erp_paysupplierinvoicedetail
	GROUP BY
		purchaseOrderMastertID,
		itemAutoID
) supdetail ON `supdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
LEFT JOIN (
	SELECT
		purchaseOrderDetailsID,
		SUM(receivedQty) AS grvqty
	FROM
		srp_erp_grvdetails
	GROUP BY
		purchaseOrderMastertID,
		itemAutoID
) grvdetail ON `grvdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_purchaseorderdetails`.`itemAutoID`
AND (
	`goodsRecievedYN` = 0
	OR `goodsRecievedYN` IS NULL
)
GROUP BY
	srp_erp_purchaseorderdetails.`purchaseOrderID`
) qtybal ON srp_erp_purchaseordermaster.purchaseOrderID = qtybal.purchaseOrderID WHERE  `supplierID` = '{$supplierID}' AND `documentDate` <= '{$date}' AND `segmentID` = '{$segmentID}' AND `transactionCurrencyID` = '{$currencyID}' AND `confirmedYN` = 1 AND `closedYN` = 0 AND `approvedYN` = 1 AND qtybal.receivedQty>0  GROUP BY srp_erp_purchaseordermaster.purchaseOrderCode")->result_array();

}

    function save_po_base_items()
    {
        $noofitems = $this->input->post('noofitems');
        $grossqty = $this->input->post('grossqty');
        $buckets = $this->input->post('buckets');
        $bucketweightID = $this->input->post('bucketweightID');
        $bucketweight = $this->input->post('bucketweight');
        $taxCalculationMasterID = $this->input->post('taxCalculationMasterID');

        $this->db->trans_start();
        $items_arr = array();
        $this->db->select('srp_erp_purchaseorderdetails.*,ifnull(sum(srp_erp_grvdetails.receivedQty),0) AS receivedQty,ifnull(sum(srp_erp_paysupplierinvoicedetail.requestedQty),0) AS bsireceivedQty,srp_erp_purchaseordermaster.purchaseOrderCode,srp_erp_purchaseordermaster.transactionAmount');
        $this->db->from('srp_erp_purchaseorderdetails');
        $this->db->where_in('srp_erp_purchaseorderdetails.purchaseOrderDetailsID', $this->input->post('DetailsID'));
        $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_purchaseorderdetails.purchaseOrderID');
        $this->db->join('srp_erp_grvdetails', 'srp_erp_grvdetails.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
        $this->db->join('srp_erp_paysupplierinvoicedetail', 'srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
        $this->db->group_by("purchaseOrderDetailsID");
        $query = $this->db->get()->result_array();

        $purchaseOrderIDs = \array_column($query, 'purchaseOrderID');
        $this->db->select('*');
        $this->db->from('srp_erp_grv_addon');
        $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_grv_addon.poAutoID');
        $this->db->where_in('poAutoID', $purchaseOrderIDs);
        $this->db->where('srp_erp_grv_addon.logisticConfirmedYN', 0);
        $grvAddon = $this->db->get()->result_array();

        if (false === empty($grvAddon))
        {
            $this->session->set_flashdata('w', 'There are pending purchase orders awaiting logistic confirmation.');
            return ['status' => false];
        }

        $this->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription, jobID');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        $master = $this->db->get()->row_array();

        if (true === empty($master))
        {
            $this->session->set_flashdata('w', 'Grv not found');
            return ['status' => false];
        }

        $qty = $this->input->post('qty');
        $amount = $this->input->post('amount');
        for ($i = 0; $i < count($query); $i++) {

            $receivedQty = $qty[$i] ?? 0;

            if ($receivedQty == 0){
                continue;
            }

            $this->db->select('purchaseOrderMastertID');
            $this->db->from('srp_erp_grvdetails');
            $this->db->where('purchaseOrderMastertID', $query[$i]['purchaseOrderID']);
            $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
            $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
            $order_detail = $this->db->get()->result_array();
            $item_data = fetch_item_data($query[$i]['itemAutoID']);

            if (true === empty($item_data))
            {
                $this->session->set_flashdata('w', 'Item not found');
                return ['status' => false];
            }

            if (!empty($order_detail) && $item_data['mainCategory'] == "Inventory") {
                $this->session->set_flashdata('w', 'PO Details added already.');
                return ['status' => false];
            }

            if ($item_data['mainCategory'] == 'Inventory' || $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
                $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
                if (empty($warehouseitems)) {
                    $item_id = array_search($query[$i]['itemSystemCode'], array_column($items_arr, 'itemSystemCode'));
                    if ((string)$item_id == '') {
                        $items_arr[$i]['wareHouseAutoID'] = $master['wareHouseAutoID'];
                        $items_arr[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                        $items_arr[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                        $items_arr[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
                        $items_arr[$i]['barCodeNo']= $item_data['barcode'];
                        $items_arr[$i]['salesPrice']= $item_data['companyLocalSellingPrice'];
                        $items_arr[$i]['ActiveYN']= $item_data['isActive'];
                        $items_arr[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                        $items_arr[$i]['itemDescription'] = $query[$i]['itemDescription'];
                        $items_arr[$i]['unitOfMeasureID'] = $query[$i]['defaultUOMID'];
                        $items_arr[$i]['unitOfMeasure'] = $query[$i]['defaultUOM'];
                        $items_arr[$i]['currentStock'] = 0;
                        $items_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                        $items_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                    }
                }
            }

            $this->db->select('GLAutoID');
            $this->db->where('controlAccountType', 'ACA');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
            $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);

            $potaxamnt=($query[$i]['taxAmount']+$query[$i]['generalTaxAmount'])/$query[$i]['requestedQty'];
            $item_data = fetch_item_data($query[$i]['itemAutoID']);
            $data[$i]['purchaseOrderMastertID'] = $query[$i]['purchaseOrderID'];
            $data[$i]['purchaseOrderCode'] = $query[$i]['purchaseOrderCode'];
            $data[$i]['purchaseOrderDetailsID'] = $query[$i]['purchaseOrderDetailsID'];
            $data[$i]['grvAutoID'] = trim($this->input->post('grvAutoID') ?? '');
            $data[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
            $data[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
            $data[$i]['itemDescription'] = $query[$i]['itemDescription'];
            $data[$i]['defaultUOM'] = $query[$i]['defaultUOM'];
            $data[$i]['defaultUOMID'] = $query[$i]['defaultUOMID'];
            $data[$i]['unitOfMeasure'] = $query[$i]['unitOfMeasure'];
            $data[$i]['unitOfMeasureID'] = $query[$i]['unitOfMeasureID'];
            $data[$i]['conversionRateUOM'] = $query[$i]['conversionRateUOM'];
            $data[$i]['requestedQty'] = $query[$i]['requestedQty'];

            $data[$i]['unitAmount'] = $query[$i]['unitAmount'];
            $data[$i]['transactionAmount'] = $query[$i]['transactionAmount'];
            $data[$i]['totalAmount'] = $query[$i]['totalAmount'];

            if(existTaxPolicyDocumentWise('srp_erp_grvmaster',trim($this->input->post('grvAutoID') ?? ''),'GRV','grvAutoID')== 1 && $taxCalculationMasterID[$i]!=0){
                $data[$i]['requestedAmount'] = $query[$i]['unitAmount']-($query[$i]['generalDiscountAmount']/$query[$i]['requestedQty']);
            }else{
                $data[$i]['requestedAmount'] = $query[$i]['unitAmount']-($query[$i]['generalDiscountAmount']/$query[$i]['requestedQty'])+$potaxamnt;
            }

            $data[$i]['comment'] = $query[$i]['comment'];
            $data[$i]['receivedQty'] = $qty[$i] ?? null;
            $data[$i]['noOfItems'] = $noofitems[$i] ?? null;
            $data[$i]['grossQty'] = $grossqty[$i] ?? null;
            $data[$i]['noOfUnits'] = $buckets[$i] ?? null;
            $data[$i]['deduction'] = $bucketweight[$i] ?? null;
            $data[$i]['bucketWeightID'] = $bucketweightID[$i] ?? null;
            $data[$i]['receivedAmount'] = $amount[$i];
            $data[$i]['receivedTotalAmount'] = ($data[$i]['receivedQty'] * $data[$i]['receivedAmount']);
            $data[$i]['fullTotalAmount'] = ($data[$i]['receivedQty'] * $data[$i]['receivedAmount']);
            $data[$i]['financeCategory'] = $item_data['financeCategory'];
            $data[$i]['itemCategory'] = trim($item_data['mainCategory'] ?? '');
            if ($data[$i]['itemCategory'] == 'Inventory') {

                if(!empty($master['jobID'])) {
                    $companyID = current_companyID();
                    $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                                    FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
                                                        WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();
                    if ($glDetails) {
                        $data[$i]['BLGLAutoID'] = $glDetails['WIPGLAutoID'];
                        $data[$i]['BLSystemGLCode'] = $glDetails['systemAccountCode'];
                        $data[$i]['BLGLCode'] = $glDetails['GLSecondaryCode'];
                        $data[$i]['BLDescription'] = $glDetails['GLDescription'];
                        $data[$i]['BLType'] = $glDetails['subCategory'];
                    } else {
                        $data[$i]['BLGLAutoID'] = $item_data['assteGLAutoID'];
                        $data[$i]['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                        $data[$i]['BLGLCode'] = $item_data['assteGLCode'];
                        $data[$i]['BLDescription'] = $item_data['assteDescription'];
                        $data[$i]['BLType'] = $item_data['assteType'];
                    }
                } else {
                    $data[$i]['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data[$i]['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data[$i]['BLGLCode'] = $item_data['assteGLCode'];
                    $data[$i]['BLDescription'] = $item_data['assteDescription'];
                    $data[$i]['BLType'] = $item_data['assteType'];
                }
                $data[$i]['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data[$i]['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data[$i]['PLGLCode'] = $item_data['costGLCode'];
                $data[$i]['PLDescription'] = $item_data['costDescription'];
                $data[$i]['PLType'] = $item_data['costType'];


            } elseif ($data[$i]['itemCategory'] == 'Fixed Assets') {
                $data[$i]['PLGLAutoID'] = NULL;
                $data[$i]['PLSystemGLCode'] = NULL;
                $data[$i]['PLGLCode'] = NULL;
                $data[$i]['PLDescription'] = NULL;
                $data[$i]['PLType'] = NULL;

                $data[$i]['BLGLAutoID'] = $ACA_ID['GLAutoID'];
                $data[$i]['BLSystemGLCode'] = $ACA['systemAccountCode'];
                $data[$i]['BLGLCode'] = $ACA['GLSecondaryCode'];
                $data[$i]['BLDescription'] = $ACA['GLDescription'];
                $data[$i]['BLType'] = $ACA['subCategory'];
            } else {
                if(!empty($master['jobID'])) {
                    $companyID = current_companyID();
                    $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                                        FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
                                                            WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();

                    if ($glDetails) {
                        $data[$i]['PLGLAutoID'] = $glDetails['WIPGLAutoID'];
                        $data[$i]['PLSystemGLCode'] = $glDetails['systemAccountCode'];
                        $data[$i]['PLGLCode'] = $glDetails['GLSecondaryCode'];
                        $data[$i]['PLDescription'] = $glDetails['GLDescription'];
                        $data[$i]['PLType'] = $glDetails['subCategory'];
                    } else {
                        $data[$i]['PLGLAutoID'] = $item_data['costGLAutoID'];
                        $data[$i]['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                        $data[$i]['PLGLCode'] = $item_data['costGLCode'];
                        $data[$i]['PLDescription'] = $item_data['costDescription'];
                        $data[$i]['PLType'] = $item_data['costType'];
                    }
                } else {
                    $data[$i]['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data[$i]['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data[$i]['PLGLCode'] = $item_data['costGLCode'];
                    $data[$i]['PLDescription'] = $item_data['costDescription'];
                    $data[$i]['PLType'] = $item_data['costType'];
                }

                $data[$i]['BLGLAutoID'] = '';
                $data[$i]['BLSystemGLCode'] = '';
                $data[$i]['BLGLCode'] = '';
                $data[$i]['BLDescription'] = '';
                $data[$i]['BLType'] = '';
            }

            $data[$i]['addonAmount'] = 0;
            $data[$i]['addonTotalAmount'] = 0;
            $data[$i]['comment'] = $query[$i]['comment'];
            $data[$i]['remarks'] = $query[$i]['remarks'];
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

            $po_data[$i]['purchaseOrderDetailsID'] = $query[$i]['purchaseOrderDetailsID'];
            $po_data[$i]['GRVSelectedYN'] = 1;
            if ($query[$i]['requestedQty'] <= (floatval($qty[$i]) + floatval($query[$i]['receivedQty'])+ floatval($query[$i]['bsireceivedQty']))) {
                $po_data[$i]['goodsRecievedYN'] = 1;
            } else {
                $po_data[$i]['goodsRecievedYN'] = 0;
            }

        }

        if (!empty($items_arr)) {
            $items_arr = array_values($items_arr);
            $this->db->insert_batch('srp_erp_warehouseitems', $items_arr);
        }

        if (!empty($data)) {

            foreach($data as $val)
            {
                $unitAmount = $val['unitAmount'];
                $poTotalAmount = $val['transactionAmount'];
                $receivedQty = $val['receivedQty'];
                $totalDetailAmount = $val['totalAmount'];

                unset($val['unitAmount']);
                unset($val['transactionAmount']);
                unset($val['totalAmount']);

                $this->db->insert('srp_erp_grvdetails', $val);
                $grvDetailId = $this->db->insert_id();

                $poAddons = $this->geAddonByPurchaseOrderId($val['purchaseOrderMastertID']);

                if (false === empty($poAddons))
                {
                    foreach($poAddons as $addon)
                    {
                        $val['grvDetailId'] = $grvDetailId;
                        $val['paidBy'] = $addon['paidBy'];
                        $val['addonCatagory'] = $addon['addonCatagory'];
                        $val['narrations'] = $addon['narrations'];
                        $val['referenceNo'] = $addon['referenceNo'];
                        $val['projectID'] = $addon['projectID'];
                        $val['projectExchangeRate'] = $addon['projectExchangeRate'];
                        $val['id'] = null;
                        $val['addonAmount'] = $this->caculatePoAddonAmount(
                            (float)$receivedQty,
                            (float)$unitAmount,
                            (float)$poTotalAmount,
                            (float)$totalDetailAmount,
                            (int)$val['purchaseOrderDetailsID'],
                            (int)$addon['impactFor'],
                            (float)$addon['bookingCurrencyAmount']
                        );

                        $this->savePoAddon($val);

                    }
                }
            }

           /** sub item add */
            $grvAutoID = trim($this->input->post('grvAutoID') ?? '');
            $output = $this->db->query("SELECT * FROM srp_erp_grvdetails INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_grvdetails.itemAutoID AND isSubitemExist = 1 WHERE grvAutoID = '" . $grvAutoID . "'")->result_array();
            if (!empty($output)) {
                foreach ($output as $item) {
                    if ($item['isSubitemExist'] == 1) {
                        $qty = $item['receivedQty'];
                        $subData['uom'] = $data[0]['unitOfMeasure'];
                        $subData['uomID'] = $data[0]['unitOfMeasureID'];
                        $subData['grv_detailID'] = $item['grvDetailsID'];
                        $this->add_sub_itemMaster_tmpTbl($qty, $item['itemAutoID'], $grvAutoID, $item['grvDetailsID'], 'GRV', $item['itemSystemCode'], $subData);
                    }
                }
            }

            /** End sub item add */

            $this->db->update_batch('srp_erp_purchaseorderdetails', $po_data, 'purchaseOrderDetailsID');
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Good Received note : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
            $this->session->set_flashdata('s', 'Good Received note : ' . count($query) . ' Item Details Saved Successfully.');
            $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false, 'data' => 'PO Details added already.');
        }
    }

    function save_grv_detail()
    {
        $grvDetailsID = $this->input->post('grvDetailsID');
        $tax_type = $this->input->post('tax_type');
        $advanceCostCapturing = getPolicyValues('ACC', 'All');

        if (!trim($grvDetailsID)) {
            $this->db->select('srp_erp_grvdetails.*');
            $this->db->from('srp_erp_grvdetails');
            $this->db->where('srp_erp_grvdetails.grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
            $this->db->where('srp_erp_grvdetails.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $order_detail = $this->db->get()->row_array();

            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $serviceitm= $this->db->get()->row_array();

            if(!empty($serviceitm) && $serviceitm['mainCategory']=="Inventory") {
                if (!empty($order_detail)) {
                    $this->session->set_flashdata('w', 'GRV Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                    return array('status' => false);
                }
            }
        }

        $this->db->trans_start();
        $this->db->select('GLAutoID');
        $this->db->where('controlAccountType', 'ACA');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
        $projectID = trim($this->input->post('projectID') ?? '');
        $projectExist = project_is_exist();
        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));

        if(empty($item_data)) {
            $this->session->set_flashdata('w', 'Item not found');
            return ['status' => false];
        }

        if($this->input->post('activityCode')){
            $activityCodeID = trim($this->input->post('activityCode') ?? '');
        }

        $this->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,transactionCurrencyID, jobID');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        $master = $this->db->get()->row_array();
        $data['grvAutoID'] = trim($this->input->post('grvAutoID') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['itemFinanceCategory'] = $item_data['subcategoryID'];
        $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
        $data['financeCategory'] = $item_data['financeCategory'];
        $data['itemCategory'] = $item_data['mainCategory'];
        if ($data['itemCategory'] == 'Inventory') {
            $data['PLGLAutoID'] = $item_data['costGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['PLGLCode'] = $item_data['costGLCode'];
            $data['PLDescription'] = $item_data['costDescription'];
            $data['PLType'] = $item_data['costType'];

            if(!empty($master['jobID'])) {
                $companyID = current_companyID();
                $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                                FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
                                                    WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();

                if ($glDetails) {
                    $data['BLGLAutoID'] = $glDetails['WIPGLAutoID'];
                    $data['BLSystemGLCode'] = $glDetails['systemAccountCode'];
                    $data['BLGLCode'] = $glDetails['GLSecondaryCode'];
                    $data['BLDescription'] = $glDetails['GLDescription'];
                    $data['BLType'] = $glDetails['subCategory'];
                } else {
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                }
            } else {
                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            }
        } elseif ($data['itemCategory'] == 'Fixed Assets') {
            $data['PLGLAutoID'] = NULL;
            $data['PLSystemGLCode'] = NULL;
            $data['PLGLCode'] = NULL;
            $data['PLDescription'] = NULL;
            $data['PLType'] = NULL;

            $data['BLGLAutoID'] = $ACA_ID['GLAutoID'];
            $data['BLSystemGLCode'] = $ACA['systemAccountCode'];
            $data['BLGLCode'] = $ACA['GLSecondaryCode'];
            $data['BLDescription'] = $ACA['GLDescription'];
            $data['BLType'] = $ACA['subCategory'];
        } else {
            if(!empty($master['jobID'])) {
                $companyID = current_companyID();
                $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                                    FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
                                                        WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();

                if ($glDetails) {
                    $data['PLGLAutoID'] = $glDetails['WIPGLAutoID'];
                    $data['PLSystemGLCode'] = $glDetails['systemAccountCode'];
                    $data['PLGLCode'] = $glDetails['GLSecondaryCode'];
                    $data['PLDescription'] = $glDetails['GLDescription'];
                    $data['PLType'] = $glDetails['subCategory'];
                } else {
                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data['PLGLCode'] = $item_data['costGLCode'];
                    $data['PLDescription'] = $item_data['costDescription'];
                    $data['PLType'] = $item_data['costType'];
                }
            } else {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
            }

            $data['BLGLAutoID'] = '';
            $data['BLSystemGLCode'] = '';
            $data['BLGLCode'] = '';
            $data['BLDescription'] = '';
            $data['BLType'] = '';
        }
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');
        }
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['receivedQty'] = trim($this->input->post('quantityRequested') ?? '');
        $data['receivedAmount'] = trim($this->input->post('estimatedAmount') ?? '');
        $data['receivedTotalAmount'] = ($this->input->post('quantityRequested') * $this->input->post('estimatedAmount'));
        $data['fullTotalAmount'] = ($this->input->post('quantityRequested') * $this->input->post('estimatedAmount'));
        $data['addonAmount'] = 0;
        $data['addonTotalAmount'] = 0;
        if($advanceCostCapturing == 1){
            $data['activityCodeID'] = $activityCodeID;
        }
        $data['comment'] = trim($this->input->post('comment') ?? '');
        $data['batchNumber'] = trim($this->input->post('batchNumber') ?? '');
        $data['batchExpireDate'] = trim($this->input->post('expireDate') ?? '');
        $data['remarks'] = trim($this->input->post('remarks') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if ($data['itemCategory'] == 'Inventory' or $data['itemCategory'] == 'Non Inventory') {
            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $master['wareHouseAutoID'],
                    'wareHouseLocation' => $master['wareHouseLocation'],
                    'wareHouseDescription' => $master['wareHouseDescription'],
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
        }

        if (trim($grvDetailsID)) {
            $this->db->where('grvDetailsID', trim($grvDetailsID));
            $this->db->update('srp_erp_grvdetails', $data);

            $purchaseOrderDetailsID = $this->db->query("SELECT purchaseOrderDetailsID FROM `srp_erp_grvdetails` where grvDetailsID  = $grvDetailsID ")->row('purchaseOrderDetailsID');

            $grvAutoID = trim($this->input->post('grvAutoID') ?? '');

            $this->db->select('srp_erp_purchaseorderdetails.*,srp_erp_purchaseordermaster.transactionAmount');
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_purchaseorderdetails.purchaseOrderID');
            $this->db->where('srp_erp_purchaseorderdetails.purchaseOrderDetailsID', $purchaseOrderDetailsID);
            $poDetail = $this->db->get()->row_array();

            $unitAmount = $poDetail['unitAmount'] ?? 0;
            $poTotalAmount = $poDetail['transactionAmount'] ?? 0;
            $totalDetailAmount = $poDetail['totalAmount'] ?? 0;
            $receivedQty = $data['receivedQty'];

            $this->deleteAddonByGrvDetailId($grvDetailsID);
            $poAddons = $this->geAddonByPurchaseOrderId($poDetail['purchaseOrderID'] ?? '');

            if (false === empty($poAddons))
            {
                foreach($poAddons as $addon)
                {
                    $addonDetail['grvDetailId'] = $grvDetailsID;
                    $addonDetail['paidBy'] = $addon['paidBy'];
                    $addonDetail['addonCatagory'] = $addon['addonCatagory'];
                    $addonDetail['narrations'] = $addon['narrations'];
                    $addonDetail['referenceNo'] = $addon['referenceNo'];
                    $addonDetail['projectID'] = $addon['projectID'];
                    $addonDetail['projectExchangeRate'] = $addon['projectExchangeRate'];
                    $addonDetail['id'] = null;
                    $addonDetail['grvAutoID'] = $grvAutoID;
                    $addonDetail['addonAmount'] = $this->caculatePoAddonAmount(
                        (float)$receivedQty,
                        (float)$unitAmount,
                        (float)$poTotalAmount,
                        (float)$totalDetailAmount,
                        (int)$purchaseOrderDetailsID,
                        (int)$addon['impactFor'],
                        (float)$addon['bookingCurrencyAmount']
                    );

                    $this->savePoAddon($addonDetail);
                }
            }

            $grvDocType = $this->db->query("SELECT grvType FROM `srp_erp_grvmaster` where grvAutoID = $grvAutoID")->row("grvType");
            $grvPurchaseOrderID = trim($this->input->post('grvPurchaseOrderID') ?? '');
            $group_based_tax = existTaxPolicyDocumentWise('srp_erp_grvmaster',trim($this->input->post('grvAutoID') ?? ''),'GRV','grvAutoID');
            if($group_based_tax == 1){
                if($grvDocType == 'PO Base'){
                    $companyID = current_companyID();
                    $grvTax = $this->db->query("SELECT
                                            srp_erp_purchaseorderdetails.taxCalculationformulaID,grvAutoID,
                                           ((srp_erp_grvdetails.receivedQty * unitAmount)+(srp_erp_grvdetails.receivedQty* IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)	))  as totalAmount,
		                                   (srp_erp_grvdetails.receivedQty * IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)) as discountAmount,
                                            grvDetailsID
                                       FROM
                                       `srp_erp_grvdetails` 
                                       LEFT JOIN srp_erp_purchaseorderdetails ON srp_erp_purchaseorderdetails.purchaseOrderDetailsID = srp_erp_grvdetails.purchaseOrderDetailsID

                                        WHERE
                                        srp_erp_grvdetails.companyID = $companyID 
                                        AND grvAutoID = $grvAutoID")->result_array();


                    $isRcmDocument = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID',$grvPurchaseOrderID);

                    if (existTaxPolicyDocumentWise('srp_erp_grvmaster', trim($this->input->post('grvAutoID') ?? ''), 'GRV', 'grvAutoID') == 1) {
                        if (!empty($grvTax)) {
                            foreach ($grvTax as $val) {
                                if (($val['taxCalculationformulaID'] != 0) && ($val['taxCalculationformulaID'] != '')) {
                                    tax_calculation_vat(null, null,$val['taxCalculationformulaID'], 'grvAutoID', trim($this->input->post('grvAutoID') ?? ''), $val['totalAmount'], 'GRV', $val['grvDetailsID'], $val['discountAmount'], 1,$isRcmDocument);
                                }
                            }
                        }
                    }
                }else {
                     tax_calculation_vat(null,null,$tax_type,'grvAutoID',trim($this->input->post('grvAutoID') ?? ''),$data['receivedTotalAmount'],'GRV',$grvDetailsID,0,1);
                    }
            }

            /** update sub item master */

            $this->db->select('srp_erp_grvdetails.*,srp_erp_grvmaster.wareHouseAutoID');
            $this->db->from('srp_erp_grvdetails');
            $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID', 'left');
            $this->db->where('srp_erp_grvdetails.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $detail = $this->db->get()->row_array();


            $subData['uom'] = $data['unitOfMeasure'];
            $subData['uomID'] = $data['unitOfMeasureID'];
            $subData['grvDetailsID'] = $grvDetailsID;
            $subData['wareHouseAutoID'] = $detail['wareHouseAutoID'] ?? '';

            if ($item_data['isSubitemExist'] == 1) {
                $this->edit_sub_itemMaster_tmpTbl($this->input->post('quantityRequested'), $item_data['itemAutoID'], $data['grvAutoID'], $grvDetailsID, 'GRV', $data['itemSystemCode'], $subData);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GRV Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {   

                $podetail = fetch_po_detail_status($purchaseOrderDetailsID);

                if($podetail['receivedQty']>0 || $podetail['bsireceivedQty']>0){
                    $po_data['GRVSelectedYN'] = 1;
                }else{
                    $po_data['GRVSelectedYN'] = 0;
                }
               
                if ($podetail['requestedQty'] <= (floatval($podetail['receivedQty'])+ floatval($podetail['bsireceivedQty']))) {
                    $po_data['goodsRecievedYN'] = 1;
                } else {
                    $po_data['goodsRecievedYN'] = 0;
                }
                
                $this->db->where('purchaseOrderDetailsID', $purchaseOrderDetailsID);
                $this->db->update('srp_erp_purchaseorderdetails', $po_data);

                $this->session->set_flashdata('s', 'GRV Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('grvDetailsID'));
            }
        } else {
            $data['requestedQty'] = 0.00;
            $data['requestedAmount'] = 0.00;
            $data['purchaseOrderMastertID'] = trim($this->input->post('purchaseOrderID') ?? '');
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_grvdetails', $data);
            $last_id = $this->db->insert_id();


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GRV Detail : ' . $data['itemSystemCode'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'GRV Detail : ' . $data['itemSystemCode'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_grv_inspection_qty()
    {
        $grvDetailsID = $this->input->post('grvDetailsID');
        $tax_type = $this->input->post('tax_type');

        if (!trim($grvDetailsID)) {
            $this->db->select('srp_erp_grvdetails.*');
            $this->db->from('srp_erp_grvdetails');
            $this->db->where('srp_erp_grvdetails.grvAutoID', trim($this->input->post('grvAutoID_in') ?? ''));
            $this->db->where('srp_erp_grvdetails.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $order_detail = $this->db->get()->row_array();

            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $serviceitm= $this->db->get()->row_array();

            if($serviceitm['mainCategory']=="Inventory") {
                if (!empty($order_detail)) {
                    $this->session->set_flashdata('w', 'GRV Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                    return array('status' => false);
                }
            }
        }

        $this->db->trans_start();
        //$ACA_ID = $this->common_data['controlaccounts']['ACA'];
        $this->db->select('GLAutoID');
        $this->db->where('controlAccountType', 'ACA');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
        $projectID = trim($this->input->post('projectID') ?? '');
        $projectExist = project_is_exist();
        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));

        $this->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,transactionCurrencyID, jobID');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID_in') ?? ''));
        $master = $this->db->get()->row_array();
        $data['grvAutoID'] = trim($this->input->post('grvAutoID_in') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['itemFinanceCategory'] = $item_data['subcategoryID'];
        $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
        $data['financeCategory'] = $item_data['financeCategory'];
        $data['itemCategory'] = $item_data['mainCategory'];
        if ($data['itemCategory'] == 'Inventory') {
            $data['PLGLAutoID'] = $item_data['costGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['PLGLCode'] = $item_data['costGLCode'];
            $data['PLDescription'] = $item_data['costDescription'];
            $data['PLType'] = $item_data['costType'];

            if(!empty($master['jobID'])) {
                $companyID = current_companyID();
                $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                                FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
                                                    WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();

                if ($glDetails) {
                    $data['BLGLAutoID'] = $glDetails['WIPGLAutoID'];
                    $data['BLSystemGLCode'] = $glDetails['systemAccountCode'];
                    $data['BLGLCode'] = $glDetails['GLSecondaryCode'];
                    $data['BLDescription'] = $glDetails['GLDescription'];
                    $data['BLType'] = $glDetails['subCategory'];
                } else {
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                }
            } else {
                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            }
        } elseif ($data['itemCategory'] == 'Fixed Assets') {
            $data['PLGLAutoID'] = NULL;
            $data['PLSystemGLCode'] = NULL;
            $data['PLGLCode'] = NULL;
            $data['PLDescription'] = NULL;
            $data['PLType'] = NULL;

            $data['BLGLAutoID'] = $ACA_ID['GLAutoID'];
            $data['BLSystemGLCode'] = $ACA['systemAccountCode'];
            $data['BLGLCode'] = $ACA['GLSecondaryCode'];
            $data['BLDescription'] = $ACA['GLDescription'];
            $data['BLType'] = $ACA['subCategory'];
        } else {
            if(!empty($master['jobID'])) {
                $companyID = current_companyID();
                $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                                    FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
                                                        WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();

                if ($glDetails) {
                    $data['PLGLAutoID'] = $glDetails['WIPGLAutoID'];
                    $data['PLSystemGLCode'] = $glDetails['systemAccountCode'];
                    $data['PLGLCode'] = $glDetails['GLSecondaryCode'];
                    $data['PLDescription'] = $glDetails['GLDescription'];
                    $data['PLType'] = $glDetails['subCategory'];
                } else {
                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data['PLGLCode'] = $item_data['costGLCode'];
                    $data['PLDescription'] = $item_data['costDescription'];
                    $data['PLType'] = $item_data['costType'];
                }
            } else {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
            }

            $data['BLGLAutoID'] = '';
            $data['BLSystemGLCode'] = '';
            $data['BLGLCode'] = '';
            $data['BLDescription'] = '';
            $data['BLType'] = '';
        }
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] =$this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');
        }
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['receivedQty'] = trim($this->input->post('quantityRequested') ?? '');
        $data['receivedAmount'] = trim($this->input->post('estimatedAmount') ?? '');
        $data['receivedTotalAmount'] = ($this->input->post('quantityRequested') * $this->input->post('estimatedAmount'));
        $data['fullTotalAmount'] = ($this->input->post('quantityRequested') * $this->input->post('estimatedAmount'));
        $data['addonAmount'] = 0;
        $data['addonTotalAmount'] = 0;
        $data['comment'] = trim($this->input->post('comment') ?? '');
        $data['batchNumber'] = trim($this->input->post('batchNumber') ?? '');
        $data['batchExpireDate'] = trim($this->input->post('expireDate') ?? '');
        $data['remarks'] = trim($this->input->post('remarks') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if ($data['itemCategory'] == 'Inventory' or $data['itemCategory'] == 'Non Inventory') {
            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $master['wareHouseAutoID'],
                    'wareHouseLocation' => $master['wareHouseLocation'],
                    'wareHouseDescription' => $master['wareHouseDescription'],
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
        }

        if (trim($grvDetailsID)) {
            $this->db->where('grvDetailsID', trim($grvDetailsID));
            $this->db->update('srp_erp_grvdetails', $data);

            $grvAutoID = trim($this->input->post('grvAutoID_in') ?? '');

            $grvDocType = $this->db->query("SELECT grvType FROM `srp_erp_grvmaster` where grvAutoID = $grvAutoID")->row("grvType");
            $grvPurchaseOrderID = trim($this->input->post('grvPurchaseOrderID') ?? '');
            $group_based_tax = existTaxPolicyDocumentWise('srp_erp_grvmaster',trim($this->input->post('grvAutoID_in') ?? ''),'GRV','grvAutoID');
            if($group_based_tax == 1){
                if($grvDocType == 'PO Base'){
                    $companyID = current_companyID();
                    $grvTax = $this->db->query("SELECT
                                            srp_erp_purchaseorderdetails.taxCalculationformulaID,grvAutoID,
                                           ((srp_erp_grvdetails.receivedQty * unitAmount)+(srp_erp_grvdetails.receivedQty* IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)	))  as totalAmount,
		                                   (srp_erp_grvdetails.receivedQty * IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)) as discountAmount,
                                            grvDetailsID
                                       FROM
                                       `srp_erp_grvdetails` 
                                       LEFT JOIN srp_erp_purchaseorderdetails ON srp_erp_purchaseorderdetails.purchaseOrderDetailsID = srp_erp_grvdetails.purchaseOrderDetailsID

                                        WHERE
                                        srp_erp_grvdetails.companyID = $companyID 
                                        AND grvAutoID = $grvAutoID")->result_array();


                    $isRcmDocument = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID',$grvPurchaseOrderID);

                    if (existTaxPolicyDocumentWise('srp_erp_grvmaster', trim($this->input->post('grvAutoID_in') ?? ''), 'GRV', 'grvAutoID') == 1) {
                        if (!empty($grvTax)) {
                            foreach ($grvTax as $val) {
                                if (($val['taxCalculationformulaID'] != 0) && ($val['taxCalculationformulaID'] != '')) {
                                    tax_calculation_vat(null, null,$val['taxCalculationformulaID'], 'grvAutoID', trim($this->input->post('grvAutoID_in') ?? ''), $val['totalAmount'], 'GRV', $val['grvDetailsID'], $val['discountAmount'], 1,$isRcmDocument);
                                }
                            }
                        }
                    }
                }else {
                     tax_calculation_vat(null,null,$tax_type,'grvAutoID',trim($this->input->post('grvAutoID_in') ?? ''),$data['receivedTotalAmount'],'GRV',$grvDetailsID,0,1);
                    }
            }

            /** update sub item master */

            $this->db->select('srp_erp_grvdetails.*,srp_erp_grvmaster.wareHouseAutoID');
            $this->db->from('srp_erp_grvdetails');
            $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID', 'left');
            $this->db->where('srp_erp_grvdetails.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $detail = $this->db->get()->row_array();


            $purchaseOrderDetailsID = $this->db->query("SELECT purchaseOrderDetailsID FROM `srp_erp_grvdetails` where grvDetailsID  = $grvDetailsID ")->row('purchaseOrderDetailsID');

            $subData['uom'] = $data['unitOfMeasure'];
            $subData['uomID'] = $data['unitOfMeasureID'];
            $subData['grvDetailsID'] = $grvDetailsID;
            $subData['wareHouseAutoID'] = $detail['wareHouseAutoID'] ?? '';

            if ($item_data['isSubitemExist'] == 1) {
                $this->edit_sub_itemMaster_tmpTbl($this->input->post('quantityRequested'), $item_data['itemAutoID'], $data['grvAutoID'], $grvDetailsID, 'GRV', $data['itemSystemCode'], $subData);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GRV Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {   

                $podetail = fetch_po_detail_status($purchaseOrderDetailsID);


                if($podetail['receivedQty']>0 || $podetail['bsireceivedQty']>0){
                    $po_data['GRVSelectedYN'] = 1;
                }else{
                    $po_data['GRVSelectedYN'] = 0;
                }
               
                if ($podetail['requestedQty'] <= (floatval($podetail['receivedQty'])+ floatval($podetail['bsireceivedQty']))) {
                    $po_data['goodsRecievedYN'] = 1;
                } else {
                    $po_data['goodsRecievedYN'] = 0;
                }
                
                $this->db->where('purchaseOrderDetailsID', $purchaseOrderDetailsID);
                $this->db->update('srp_erp_purchaseorderdetails', $po_data);


                $data_inspection['grvMasterID'] = trim($this->input->post('grvAutoID_in') ?? '');
                $data_inspection['grvDetailID'] = trim($this->input->post('grvDetailsID') ?? '');
                $data_inspection['oldQty'] = trim($this->input->post('qty_unchanged') ?? '');
                $data_inspection['newQty'] = trim($this->input->post('quantityRequested') ?? '');
                $data_inspection['comment'] = $this->input->post('inspection_comment');

                $data_inspection['createdDateTime'] = $this->common_data['current_date'];
                $data_inspection['companyID'] = $this->common_data['company_data']['company_id'];
                $data_inspection['createdUserGroup'] = $this->common_data['user_group'];
                $data_inspection['createdPCID'] = $this->common_data['current_pc'];
                $data_inspection['createdUserID'] = $this->common_data['current_userID'];
                $data_inspection['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_grv_inspection', $data_inspection);


                $this->session->set_flashdata('s', 'GRV Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('grvDetailsID'));
            }
        } else {
            $data['requestedQty'] = 0.00;
            $data['requestedAmount'] = 0.00;
            $data['purchaseOrderMastertID'] = trim($this->input->post('purchaseOrderID') ?? '');
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_grvdetails', $data);
            $last_id = $this->db->insert_id();


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GRV Detail : ' . $data['itemSystemCode'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {

                $data_inspection['grvMasterID'] = trim($this->input->post('grvAutoID_in') ?? '');
                $data_inspection['grvDetailID'] = trim($this->input->post('grvDetailsID') ?? '');
                $data_inspection['oldQty'] = trim($this->input->post('qty_unchanged') ?? '');
                $data_inspection['newQty'] = trim($this->input->post('quantityRequested') ?? '');
                $data_inspection['comment'] = $this->input->post('inspection_comment');

                $data_inspection['createdDateTime'] = $this->common_data['current_date'];
                $data_inspection['companyID'] = $this->common_data['company_data']['company_id'];
                $data_inspection['createdUserGroup'] = $this->common_data['user_group'];
                $data_inspection['createdPCID'] = $this->common_data['current_pc'];
                $data_inspection['createdUserID'] = $this->common_data['current_userID'];
                $data_inspection['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_grv_inspection', $data_inspection);

                $this->session->set_flashdata('s', 'GRV Detail : ' . $data['itemSystemCode'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }

    }

    function save_grv_st_bulk_detail()
    {
        //print_r($this->input->post('itemAutoID'));exit;
        $projectExist = project_is_exist();
        $grvDetailsID = $this->input->post('grvDetailsID');
        $grvAutoID = $this->input->post('grvAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $projectID = $this->input->post('projectID');
        $discount = $this->input->post('discount');
        if($this->input->post('activityCode')){
            $activityCodeID = $this->input->post('activityCode');
        }
        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        $comment = $this->input->post('comment');
        $taxType = $this->input->post('text_type');
        $expireDate=$this->input->post('expireDate');
        $batchNumber=$this->input->post('batchNumber');
        $isFoc = $this->input->post('isFoc');
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_grvmaster',$grvAutoID,'GRV','grvAutoID');

        $this->db->trans_start();
        //$ACA_ID = $this->common_data['controlaccounts']['ACA'];
        $this->db->select('GLAutoID');
        $this->db->where('controlAccountType', 'ACA');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);

        $this->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,transactionCurrencyID, jobID');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        $master = $this->db->get()->row_array();
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $isFocRecord = isset($isFoc[$key])? 1:null;
            if ($grvAutoID) {
                $this->db->select('mainCategory');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemAutoID', $itemAutoID);
                $category = $this->db->get()->row_array();
                if ($category['mainCategory'] == 'Inventory') {
                    $this->db->select('grvAutoID,itemDescription,itemSystemCode,isFoc');
                    $this->db->from('srp_erp_grvdetails');
                    $this->db->where('grvAutoID', $grvAutoID);
                    $this->db->where('itemAutoID', $itemAutoID);
                }
            }
            $item_data = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);
            if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $item_data['itemAutoID']);
                $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                if (empty($warehouseitems)) {
                    $data_arr = array(
                        'wareHouseAutoID' => $master['wareHouseAutoID'],
                        'wareHouseLocation' => $master['wareHouseLocation'],
                        'wareHouseDescription' => $master['wareHouseDescription'],
                        'itemAutoID' => $item_data['itemAutoID'],
                        'barCodeNo' => $item_data['barcode'],
                        'salesPrice' => $item_data['companyLocalSellingPrice'],
                        'ActiveYN' => $item_data['isActive'],
                        'itemSystemCode' => $item_data['itemSystemCode'],
                        'itemDescription' => $item_data['itemDescription'],
                        'unitOfMeasureID' => $item_data['defaultUnitOfMeasureID'],
                        'unitOfMeasure' => $item_data['defaultUnitOfMeasure'],
                        'currentStock' => 0,
                        'companyID' => $this->common_data['company_data']['company_id'],
                        'companyCode' => $this->common_data['company_data']['company_code'],
                    );

                    $this->db->insert('srp_erp_warehouseitems', $data_arr);
                }
            }

            $data['grvAutoID'] = $grvAutoID;
            $data['itemAutoID'] = $item_data['itemAutoID'];
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            if ($data['itemCategory'] == 'Inventory') {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                if(!empty($master['jobID'])) {
                    $companyID = current_companyID();
                    $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                            FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
	                        WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();

                    if($glDetails) {
                        $data['BLGLAutoID'] = $glDetails['WIPGLAutoID'];
                        $data['BLSystemGLCode'] = $glDetails['systemAccountCode'];
                        $data['BLGLCode'] = $glDetails['GLSecondaryCode'];
                        $data['BLDescription'] = $glDetails['GLDescription'];
                        $data['BLType'] = $glDetails['subCategory'];
                    } else {
                        $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                        $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                        $data['BLGLCode'] = $item_data['assteGLCode'];
                        $data['BLDescription'] = $item_data['assteDescription'];
                        $data['BLType'] = $item_data['assteType'];
                    }
                } else {
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                }
            } elseif ($data['itemCategory'] == 'Fixed Assets') {
                $data['PLGLAutoID'] = NULL;
                $data['PLSystemGLCode'] = NULL;
                $data['PLGLCode'] = NULL;
                $data['PLDescription'] = NULL;
                $data['PLType'] = NULL;

                $data['BLGLAutoID'] = $ACA_ID['GLAutoID'];
                $data['BLSystemGLCode'] = $ACA['systemAccountCode'];
                $data['BLGLCode'] = $ACA['GLSecondaryCode'];
                $data['BLDescription'] = $ACA['GLDescription'];
                $data['BLType'] = $ACA['subCategory'];
            } else {
                if(!empty($master['jobID'])) {
                    $companyID = current_companyID();
                    $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                        FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
	                    WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();

                    if($glDetails) {
                        $data['PLGLAutoID'] = $glDetails['WIPGLAutoID'];
                        $data['PLSystemGLCode'] = $glDetails['systemAccountCode'];
                        $data['PLGLCode'] = $glDetails['GLSecondaryCode'];
                        $data['PLDescription'] = $glDetails['GLDescription'];
                        $data['PLType'] = $glDetails['subCategory'];
                    } else {
                        $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                        $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                        $data['PLGLCode'] = $item_data['costGLCode'];
                        $data['PLDescription'] = $item_data['costDescription'];
                        $data['PLType'] = $item_data['costType'];
                    }
                } else {
                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data['PLGLCode'] = $item_data['costGLCode'];
                    $data['PLDescription'] = $item_data['costDescription'];
                    $data['PLType'] = $item_data['costType'];
                }

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
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
            $data['receivedQty'] = trim($quantityRequested[$key]);
            $data['receivedAmount'] = trim($estimatedAmount[$key]);
            $data['isFoc'] = (!isset($isFoc[$key])) ? 0 : 1;
            $data['receivedTotalAmount'] = ($data['receivedQty'] * $data['receivedAmount']);
            $data['fullTotalAmount'] = ($data['receivedQty'] * $data['receivedAmount']);
            $data['addonAmount'] = 0;
            $data['addonTotalAmount'] = 0;
            if($advanceCostCapturing == 1){
                $data['activityCodeID'] = $activityCodeID[$key];
            }
            $data['comment'] = $comment[$key];
            $data['remarks'] = null;
            $data['requestedQty'] = 0.00;
            $data['requestedAmount'] = 0.00;
            if (isset($taxType[$key])) {
                $data['taxCalculationformulaID'] = $taxType[$key];
            }
            if (isset($expireDate[$key])) {
                $data['batchExpireDate'] = $expireDate[$key];
            }
            if (isset($batchNumber[$key])) {
                $data['batchNumber'] =$batchNumber[$key];
            }

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
            $this->db->insert('srp_erp_grvdetails', $data);
            $last_id = $this->db->insert_id();

            if($group_based_tax == 1 && !empty($taxType[$key])){
                tax_calculation_vat(null,null,$taxType[$key],'grvAutoID',$grvAutoID,($data['receivedQty'] * $data['receivedAmount']),'GRV',$last_id,0,1);
            }

            if ($item_data['isSubitemExist'] == 1) {

                $qty = 0;
                if (!empty($itemAutoIDs)) {
                    foreach ($itemAutoIDs as $key => $itemAutoIDTmp) {
                        if ($itemAutoIDTmp == $itemAutoID) {
                            $qty = $quantityRequested[$key];
                        }
                    }
                }

                $subData['uom'] = $data['unitOfMeasure'];
                $subData['uomID'] = $data['unitOfMeasureID'];
                $subData['grv_detailID'] = $last_id;
                $subData['warehouseAutoID'] = $master['wareHouseAutoID'];
                $this->add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $grvAutoID, $last_id, 'GRV', $item_data['itemSystemCode'], $subData);

            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'GRV Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'GRV Details :  Saved Successfully.');
        }
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
                $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/GRV/' . $grv_detailID . '/' . $i;
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

    function getQty_subItemMaster_tmpTbl($itemAutoID, $masterID, $detailID)
    {
        $this->db->select('*');
        $this->db->where('itemAutoID', $itemAutoID);
        $this->db->where('receivedDocumentAutoID', $masterID);
        $this->db->where('receivedDocumentDetailID', $detailID);
        $this->db->from('srp_erp_itemmaster_subtemp');
        $r = $this->db->get()->result_array();
        return $r;
    }

    function delete_sub_itemMaster_existing($itemAutoID, $masterID, $detailID)
    {
        $this->db->where('receivedDocumentAutoID', $masterID);
        $this->db->where('receivedDocumentDetailID', $detailID);
        $result = $this->db->delete('srp_erp_itemmaster_subtemp');
        return $result;

    }

    function edit_sub_itemMaster_tmpTbl($qty, $itemAutoID, $masterID, $detailID, $code = 'GRV', $itemCode = null, $data = array())
    {

        $uom = isset($data['uom']) && !empty($data['uom']) ? $data['uom'] : null;
        $uomID = isset($data['uomID']) && !empty($data['uomID']) ? $data['uomID'] : null;
        $grvDetailsID = isset($data['grvDetailsID']) && !empty($data['grvDetailsID']) ? $data['grvDetailsID'] : null;
        $wareHouseAutoID = isset($data['wareHouseAutoID']) && !empty($data['wareHouseAutoID']) ? $data['wareHouseAutoID'] : null;

        $result = $this->getQty_subItemMaster_tmpTbl($itemAutoID, $masterID, $detailID);
        $count_subItemMaster = 0;
        if (!empty($result)) {
            $count_subItemMaster = count($result);
        }
        if ($count_subItemMaster != $qty) {

            /** delete existing set */
            $this->delete_sub_itemMaster_existing($itemAutoID, $masterID, $detailID);

            /** Add new set */

            $data_subItemMaster = array();
            if ($qty > 0) {
                $x = 0;
                for ($i = 1; $i <= $qty; $i++) {
                    $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                    $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                    $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/GRV/' . $grvDetailsID . '/' . $i;
                    $data_subItemMaster[$x]['uom'] = $uom;
                    $data_subItemMaster[$x]['uomID'] = $uomID;
                    $data_subItemMaster[$x]['wareHouseAutoID'] = $wareHouseAutoID;
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
        } else if ($count_subItemMaster == 0) {
            $data_subItemMaster = array();
            if ($qty > 0) {
                $x = 0;
                for ($i = 1; $i <= $qty; $i++) {
                    $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                    $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                    $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/' . $i;
                    $data_subItemMaster[$x]['uom'] = $uom;
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


    }

    function batch_insert_srp_erp_itemmaster_subtemp($data)
    {
        $this->db->insert_batch('srp_erp_itemmaster_subtemp', $data);
    }

    function fetch_template_data($grvAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
        }

        $this->db->select('documentID,grvAutoID,createdUserName,grvType,DATE_FORMAT(deliveredDate,\'' . $convertFormat . '\') AS deliveredDate,grvPrimaryCode,transactionCurrencyDecimalPlaces,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,grvDocRefNo,transactionCurrency,DATE_FORMAT(grvDate,\'' . $convertFormat . '\') AS grvDate ,wareHouseLocation,grvNarration,confirmedYN,confirmedByName,confirmedDate,supplierAddress,supplierName,supplierTelephone,supplierFax,supplierEmail,approvedbyEmpID,approvedbyEmpName,approvedYN,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate,supplierSystemCode,supplierID,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn, jobNo, jobID');
        $this->db->where('grvAutoID', $grvAutoID);
        $this->db->from('srp_erp_grvmaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('supplierSystemCode,supplierName,supplierAddress1,supplierTelephone,supplierFax,supplierEmail');
        $this->db->where('supplierAutoID', $data['master']['supplierID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();


        $this->db->select('srp_erp_grvdetails.*,srp_erp_activity_code_main.activity_code as activityCodeName,srp_erp_itemmaster.partNo,if(grvType="PO Base",concat(srp_erp_grvdetails.purchaseOrderCode," - ",'.$item_code.'),'.$item_code.') as itemSystemCode,CONCAT_WS(\' - \',IF(LENGTH(srp_erp_grvdetails.itemDescription),srp_erp_grvdetails.itemDescription,NULL),IF(LENGTH(srp_erp_grvdetails.`comment`),srp_erp_grvdetails.`comment`,NULL))as itemdes,CONCAT_WS(
                \' - Part No : \',
            IF
                ( LENGTH( srp_erp_grvdetails.`comment` ), `srp_erp_grvdetails`.`comment`, NULL ),
            IF
                ( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
	        ) AS Itemdescriptionpartno,srp_erp_taxcalculationformulamaster.Description,rcmApplicableYN,srp_erp_warehousemaster.wareHouseCode as wareHouseCode, srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription,srp_erp_purchaseorderdetails.taxAmount as purchaseTax,srp_erp_purchaseorderdetails.requestedQty as purchaseQty'); 
        $this->db->where('srp_erp_grvdetails.grvAutoID', $grvAutoID);
        $this->db->from('srp_erp_grvdetails');
        $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_grvdetails.activityCodeID', 'left');
        $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_grvdetails.itemAutoID', 'left');
        $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID =srp_erp_grvdetails.taxCalculationformulaID', 'left');
        $this->db->join('srp_erp_purchaseordermaster','srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_grvdetails.purchaseOrderMastertID','left');
        $this->db->join('srp_erp_purchaseorderdetails','srp_erp_grvdetails.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID','left');
        $this->db->join('srp_erp_warehousemaster', 'srp_erp_warehousemaster.wareHouseAutoID = srp_erp_grvmaster.consignmentWarehouseID', 'left');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('addonCatagory,bookingCurrencyAmount,bookingCurrencyDecimalPlaces,bookingCurrency,referenceNo ,supplierName, total_amount , supplierID,srp_erp_taxcalculationformulamaster.Description,taxAmount,id,
        grvAutoID');
        $this->db->where('grvAutoID', $grvAutoID);
        $this->db->from('srp_erp_grv_addon');
        $this->db->join('srp_erp_taxcalculationformulamaster','srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_grv_addon.taxCalculationformulaID','left');
        $data['addon'] = $this->db->get()->result_array();

        $data['rcmApplicableYnpolicy'] = '';
        if($data['detail']){
            $rcmApplicableYn= array_column($data['detail'],'rcmApplicableYN');
            $data['rcmApplicableYnpolicy'] =$rcmApplicableYn[0];
        }

        return $data;
    }

    function fetch_po_detail_table()
    {
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        
        
        $data['hideCost'] = getPolicyValues('HCG', 'All');
        $purchaseOrderID=$this->input->post('purchaseOrderID');
        $data['detail']=$this->db->query("SELECT
	`srp_erp_purchaseorderdetails`.*, 
    IFNULL(srp_erp_taxcalculationformulamaster.Description,'-') as Description,
    IFNULL(srp_erp_taxcalculationformulamaster.taxCalculationformulaID,0) as taxCalculationformulaID,
    (TRIM( ROUND( requestedQty, 4 ) ) + 0 ) - ((TRIM( ROUND( IFNULL( supdetail.supqty, 0 ), 4 ) ) + 0 ) + TRIM( ROUND( IFNULL( grvdetail.grvqty, 0 ), 4 ) ) + 0 ) as qtybalance,
	CONCAT_WS(
		' - Part No : ',
	IF (
		LENGTH(
			srp_erp_purchaseorderdetails.`itemDescription`
		),
		`srp_erp_purchaseorderdetails`.`itemDescription`,
		NULL
	),
IF (
	LENGTH(srp_erp_itemmaster.partNo),
	`srp_erp_itemmaster`.`partNo`,
	NULL
)
	) AS Itemdescriptionpartno,$item_code
FROM
	`srp_erp_purchaseorderdetails`
LEFT JOIN (
	SELECT
		purchaseOrderDetailsID,
		SUM(requestedQty) AS supqty
	FROM
		srp_erp_paysupplierinvoicedetail
	WHERE
		purchaseOrderMastertID = '$purchaseOrderID'
	GROUP BY
    purchaseOrderDetailsID
) supdetail ON `supdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
LEFT JOIN (
	SELECT
		purchaseOrderDetailsID,
		SUM(receivedQty) AS grvqty
	FROM
		srp_erp_grvdetails
	WHERE
		purchaseOrderMastertID = '$purchaseOrderID'
	GROUP BY
	purchaseOrderDetailsID
) grvdetail ON `grvdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_purchaseorderdetails`.`itemAutoID`
LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_purchaseorderdetails.taxCalculationformulaID
WHERE
	`purchaseOrderID` = '$purchaseOrderID' AND srp_erp_purchaseorderdetails.`isClosedYN` = 0
AND (
	`goodsRecievedYN` = 0
	OR `goodsRecievedYN` IS NULL
)
GROUP BY
	`purchaseOrderDetailsID`")->result_array();

        $companyID = current_companyID();
        $data['policy_po_cost_change'] = policy_allow_to_change_po_cost_in_grv();
        $this->db->SELECT("weightAutoID,bucketWeight");
        $this->db->FROM('srp_erp_buyback_bucketweight');
        $this->db->WHERE('companyID', $companyID);
        $data['bucketweightdrop'] =  $this->db->get()->result_array();
        return $data;
    }

    function grv_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $itemBatchPolicy = getPolicyValues('IB', 'All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $system_code = trim($this->input->post('grvAutoID') ?? '');
        $companyID = current_companyID();
        $currentuser  = current_userID();

        $this->db->select('grvPrimaryCode,documentID,DATE_FORMAT(grvDate, "%Y") as invYear,DATE_FORMAT(grvDate, "%m") as invMonth,companyFinanceYearID');
        $this->db->where('grvAutoID', trim($system_code));
        $this->db->from('srp_erp_grvmaster');
        $master_dt = $this->db->get()->row_array();

        $isProductReference_completed = 0;

        if('GRV' === $master_dt['documentID']){
            $isProductReference_completed = isMandatory_completed_document($system_code, 'GRV');
        }

        $this->db->select('grvAutoID');
        $this->db->where('grvAutoID', $system_code);
        $this->db->from('srp_erp_grvdetails');
        $record = $this->db->get()->result_array();

        //update grv checklist
        $checklists = $this->db->where('documentID','GRV')->from('srp_erp_document_approval_checklist')->get()->result_array();

        foreach($checklists as $key => $value){

            $data = array();

            $ex_record = $this->db->where('documentMasterID',$this->input->post('grvAutoID'))->where('checklistID', $value['checklistID'])->from('srp_erp_document_approval_checklistdeails')->get()->row_array();

            if($ex_record){
                continue;
            }

            $data['checklistID'] = $value['checklistID'];
            $data['checklistDescription'] = $value['checklistDescription'];
            $data['Value'] = 0;
            $data['documentMasterID'] = $this->input->post('grvAutoID');

            $this->db->insert('srp_erp_document_approval_checklistdeails',$data);

        }

        if (empty($record)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        }
        else {
            if ($isProductReference_completed == 0) {

                if($itemBatchPolicy==1){
                    $this->db->select('*');
                    $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
                    $this->db->from('srp_erp_grvdetails');
                    $grvdetails_results = $this->db->get()->result_array();
                }

                $this->load->library('sequence');
                if ($master_dt['grvPrimaryCode'] == "0" || empty($master_dt['grvPrimaryCode'])) {
                    if($locationwisecodegenerate == 1)
                    {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location =='')) {
                            return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                        }else
                        {
                            if($locationemployee!='')
                            {
                                $codegeratorgrv = $this->sequence->sequence_generator_location($master_dt['documentID'],$master_dt['companyFinanceYearID'],$locationemployee,$master_dt['invYear'],$master_dt['invMonth']);
                            }else
                            {
                                return array('error' => 2, 'message' => 'Location is not assigned for current employee');
                            }
                        }
                    }else
                    {
                        $codegeratorgrv = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }

                    $validate_code = validate_code_duplication($codegeratorgrv, 'grvPrimaryCode', $system_code,'grvAutoID', 'srp_erp_grvmaster');
                    if(!empty($validate_code)) {
                        return array('error' => 1, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                    }

                    $pvCd = array(
                        'grvPrimaryCode' => $codegeratorgrv
                    );
                    $this->db->where('grvAutoID', trim($system_code));
                    $this->db->update('srp_erp_grvmaster', $pvCd);
                } else {
                    $validate_code = validate_code_duplication($master_dt['grvPrimaryCode'], 'grvPrimaryCode', $system_code,'grvAutoID', 'srp_erp_grvmaster');
                    if(!empty($validate_code)) {
                        return array('error' => 1, 'message' => 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                }

                $this->load->library('Approvals');
                $this->db->select('grvAutoID, grvPrimaryCode, wareHouseLocation,grvDate,isconsignment,grvType');
                $this->db->where('grvAutoID', $system_code);
                $this->db->from('srp_erp_grvmaster');
                $grv_data = $this->db->get()->row_array();

                $autoApproval= get_document_auto_approval($master_dt['documentID']);

                $documentName = 'Good Received Note';
                if ('SRN' === $master_dt['documentID']){
                    $documentName = 'Service Received Note';
                }

                if($autoApproval==0){
                    $approvals_status = $this->approvals->auto_approve(
                        $grv_data['grvAutoID'],
                        'srp_erp_grvmaster',
                        'grvAutoID',
                        $master_dt['documentID'],
                        $grv_data['grvPrimaryCode'],
                        $grv_data['grvDate']
                    );
                }elseif($autoApproval==1){
                    $approvals_status = $this->approvals->CreateApproval(
                        $master_dt['documentID'],
                        $grv_data['grvAutoID'],
                        $grv_data['grvPrimaryCode'],
                        $documentName,
                        'srp_erp_grvmaster',
                        'grvAutoID',
                        0,
                        $grv_data['grvDate']
                    );
                }else{
                    return array('error' => 1, 'message' => 'Approval levels are not set for this document.');
                }

                if ($approvals_status == 1) {
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']
                    );
                    $this->db->where('grvAutoID', $system_code);
                    $this->db->update('srp_erp_grvmaster', $data);

                    if($grv_data['grvType'] !== 'LOG')
                    {
                        $this->db->select_sum('total_amount');
                        $this->db->where('impactFor', 0);
                        $this->db->where('isChargeToExpense', 0);
                        $this->db->where('grvAutoID', $system_code);
                        $addon_total_amount = $this->db->get('srp_erp_grv_addon')->row('total_amount');

                        $this->db->select('grvDetailsID,receivedAmount,receivedQty,itemAutoID,receivedTotalAmount,addonTotalAmount');
                        $this->db->where('grvAutoID', $system_code);
                        $grvdetails = $this->db->get('srp_erp_grvdetails')->result_array();
                        if (!empty($grvdetails)) {
                            $grv_full_total = 0;
                            foreach ($grvdetails as $num => $values) {
                                $grv_full_total += $values['receivedTotalAmount'];
                            }
                            for ($i = 0; $i < count($grvdetails); $i++) {
                                $data_recode = calculation_addon($grv_full_total, $addon_total_amount, $grvdetails[$i]['receivedAmount'], $grvdetails[$i]['receivedQty']);
                                $addon_item_all = $this->fetch_addon_item($system_code, $grvdetails[$i]['grvDetailsID']);
                                $addon_item = ($addon_item_all / $grvdetails[$i]['receivedQty']);
                                $grv_details[$i]['grvDetailsID'] = $grvdetails[$i]['grvDetailsID'];
                                $grv_details[$i]['addonAmount'] = round(($addon_item + $data_recode['unit']), 3);
                                $grv_details[$i]['addonTotalAmount'] = round(($addon_item_all + $data_recode['full']), 3);
                                $grv_details[$i]['fullTotalAmount'] = round($addon_item_all + $data_recode['item_total'], 3);
                            }
                            $this->db->update_batch('srp_erp_grvdetails', $grv_details, 'grvDetailsID');
                        }
                    }

                    $autoApproval= get_document_auto_approval($master_dt['documentID']);
                    if($autoApproval==0) {
                        if($grv_data['grvType'] !== 'LOG'){
                            $result = $this->save_grv_approval(
                                0,
                                $grv_data['grvAutoID'],
                                1,
                                'Auto Approved'
                            );
                        }else{
                            $result = $this->save_logistic_grv_approval(
                                0,
                                $grv_data['grvAutoID'],
                                1,
                                'Auto Approved'
                            );
                        }

                        if($result){
                            if( $itemBatchPolicy==1){
                                $this->hit_item_batch($grvdetails_results);
                            }
                            return array('error' => 0, 'message' => 'Document confirmed successfully ');
                        }
                    }else{
                        if( $itemBatchPolicy==1){
                            $this->hit_item_batch($grvdetails_results);
                        }
                        return array('error' => 0, 'message' => 'Document confirmed successfully ');
                    }
                } else if ($approvals_status == 3) {
                    return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document.');
                } else {
                    return array('error' => 1, 'message' => 'some went wrong!');
                }
            } else {
                return array('error' => 1, 'message' => 'Please complete you sub item configuration, fill all the mandatory fields!.');
            }
        }
    }

    function hit_item_batch($array){
        foreach($array AS $val){

                $this->db->select('*');
                $this->db->where('grvAutoID', $val['grvAutoID']);
                $this->db->from('srp_erp_grvmaster');
                $master_dt = $this->db->get()->row_array();

                $this->db->select("*");
                $this->db->where('itemMasterID',  $val['itemAutoID']);
                $this->db->where('batchNumber', $val['batchNumber']);
                $this->db->where('wareHouseAutoID', $master_dt['wareHouseAutoID']);
                $this->db->where('companyId',$this->common_data['company_data']['company_id']);
                $batchitems = $this->db->get('srp_erp_inventory_itembatch')->row_array();

                if (empty($batchitems)) {
                    $CI =& get_instance();
                    $CI->db->select("*");
                    $CI->db->from('srp_erp_inventory_itembatch');
                    $results_data_batch = count($CI->db->get()->result_array());
        
                    $data_batch['batchNumber']=$val['batchNumber'];
                    $data_batch['batchCode']="BIL".(100001+$results_data_batch);
                    $data_batch['qtr']=$val['receivedQty'];
                    $data_batch['batchExpireDate']=$val['batchExpireDate'];
                    $data_batch['itemMasterID']=$val['itemAutoID'];
                    $data_batch['companyId']=$this->common_data['company_data']['company_id'];
                    $data_batch['createdUserID']=$this->common_data['current_userID'];
                    $data_batch['createdDateTime']=$this->common_data['current_date'];
                    $data_batch['grvDetailID']= isset($val['stockAdjustmentDetailsAutoID']) ? $val['stockAdjustmentDetailsAutoID'] : '';
                    $data_batch['wareHouseAutoID']=  $master_dt['wareHouseAutoID'];
                    $data_batch['status']=2;
        
                    $this->db->insert('srp_erp_inventory_itembatch', $data_batch);

                }else{
                    $data_batch['qtr'] = $batchitems['qtr']+$val['receivedQty'];
                    
                    $this->db->where('itemMasterID',  $val['itemAutoID']);
                    $this->db->where('batchNumber', $val['batchNumber']);
                    $this->db->where('wareHouseAutoID', $master_dt['wareHouseAutoID']);
                    $this->db->where('companyId',$this->common_data['company_data']['company_id']);
                    $this->db->update('srp_erp_inventory_itembatch', $data_batch);

                }
        }
    }

    function fetch_addon_item($grvAutoID, $grvDetailsID)
    {
        $this->db->select_sum('total_amount');
        $this->db->where('grvAutoID', $grvAutoID);
        $this->db->where('impactFor', $grvDetailsID);
        return $this->db->get('srp_erp_grv_addon')->row('total_amount');
    }

    function save_addon()
    {
        $this->db->trans_start();
        $booking_code = explode('|', trim($this->input->post('booking_code') ?? ''));
        $taxtype = trim($this->input->post('taxtype') ?? '');

        $projectExist = project_is_exist();

        $grvAutoID = null !== $this->input->post('grvAutoID') ? trim($this->input->post('grvAutoID') ?? ''): null;
        $poAutoID = null !== $this->input->post('poAutoID') ? trim($this->input->post('poAutoID') ?? ''): null;

        if ($grvAutoID)
        {
            $table = 'srp_erp_grvmaster';
            $condition = 'grvAutoID';
            $value = $grvAutoID;
            $select = 'transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces';
        } 
        elseif ($poAutoID)
        {
            $table = 'srp_erp_purchaseordermaster';
            $condition = 'purchaseOrderID';
            $value = $poAutoID;
            $select = 'logisticConfirmedYN, transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces';
        }
        
        if (isset($table, $condition, $value, $select))
        {
            $this->db->select($select);
            $this->db->from($table);
            $this->db->where($condition, $value);
            $master = $this->db->get()->row_array();
        
            if (
                $poAutoID
                && $master
                && $master['logisticConfirmedYN'] == 1
            )
            {
                $this->session->set_flashdata('w', 'Document is already confirmed');
                return ['status' => false];
            }
        }

        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplier') ?? ''));
        $data['grvAutoID'] = $grvAutoID;
        $data['poAutoID'] = $poAutoID;
        $data['supplierID'] = trim($this->input->post('supplier') ?? '');
        if ($projectExist == 1) {
            $projectCurrency = project_currency($this->input->post('projectID'));
            $projectCurrencyExchangerate = currency_conversionID($this->input->post('bookingCurrencyID'), $projectCurrency);
            $data['projectID'] = trim($this->input->post('projectID') ?? '');
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['supplierSystemCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data['supplierliabilityType'] = $supplier_arr['liabilityType'];
        $data['isChargeToExpense'] = trim($this->input->post('isChargeToExpense') ?? '');
        if ($data['isChargeToExpense'] == 1) {
            $gl_code = explode('|', $this->input->post('glcode_dec'));
            $data['GLAutoID'] = trim($this->input->post('GLAutoID') ?? '');
            $data['systemGLCode'] = trim($gl_code[0] ?? '');
            $data['GLCode'] = trim($gl_code[1] ?? '');
            $data['GLDescription'] = trim($gl_code[2] ?? '');
            $data['GLType'] = trim($gl_code[3] ?? '');
        }
        $data['bookingCurrencyID'] = trim($this->input->post('bookingCurrencyID') ?? '');
        $data['bookingCurrency'] = trim($booking_code[0] ?? '');
        $data['bookingCurrencyExchangeRate'] = 1;
        $data['bookingCurrencyAmount'] = trim($this->input->post('total_amount') ?? '');
        $data['bookingCurrencyDecimalPlaces'] = fetch_currency_desimal($data['bookingCurrency']);
        $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
        $data['transactionCurrency'] = $master['transactionCurrency'];
        $transaction_currency = currency_conversionID($data['bookingCurrencyID'], $data['transactionCurrencyID']);
        $data['transactionExchangeRate'] = $transaction_currency['conversion'];
        $data['transactionCurrencyDecimalPlaces'] = $transaction_currency['DecimalPlaces'];
        $data['total_amount'] = round(($data['bookingCurrencyAmount'] / $data['transactionExchangeRate']), $data['transactionCurrencyDecimalPlaces']);
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data['bookingCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['bookingCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $supplier_currency = currency_conversionID($data['bookingCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplier_currency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplier_currency['DecimalPlaces'];
        $data['supplierCurrencyAmount'] = round(($data['bookingCurrencyAmount'] / $data['supplierCurrencyExchangeRate']), $data['supplierCurrencyDecimalPlaces']);
        $data['impactFor'] = trim($this->input->post('impactFor') ?? '');
        $data['paidBy'] = trim($this->input->post('paid_by') ?? '');
        $data['addonCatagory'] = trim($this->input->post('addonCatagory') ?? '');
        $data['narrations'] = trim($this->input->post('narrations') ?? '');
        $data['referenceNo'] = trim($this->input->post('referencenos') ?? '');
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['modifiedUserName'] = $this->common_data['current_user'];

        if (trim($this->input->post('id') ?? '')) {
            $this->db->where('id', trim($this->input->post('id') ?? ''));
            $this->db->update('srp_erp_grv_addon', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GRV Addon  : ' . $data['addonCatagory'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                if($taxtype){
                    tax_calculation_vat(null,null,$taxtype,'id',$data['grvAutoID'] ,$data['bookingCurrencyAmount'],'GRV-ADD',trim($this->input->post('id') ?? ''),0,0);
                }

                $this->session->set_flashdata('s', 'GRV Addon  : ' . $data['addonCatagory'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('supplierCodeSystem'));
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_grv_addon', $data);
            $last_id = $this->db->insert_id();

            if($taxtype){
                tax_calculation_vat(null,null,$taxtype,'id',$data['grvAutoID'] ,$data['bookingCurrencyAmount'],'GRV-ADD',$last_id,0,0);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GRV Addon : ' . $data['addonCatagory'] . ' Save Failed ');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'GRV Addon : ' . $data['addonCatagory'] . 'Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    } 

    function fetch_addons()
    {
        $this->db->select('masterTBL.*,detailTBL.description'); 
        $this->db->from('srp_erp_grv_addon masterTBL');
        $this->db->Join('srp_erp_taxcalculationformulamaster detailTBL','detailTBL.taxCalculationformulaID = masterTBL.taxCalculationformulaID','Left');
        
        if(null !== $this->input->post('grvAutoID'))
        {
            $this->db->where('masterTBL.grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        }

        if(null !== $this->input->post('poAutoID'))
        {
            $this->db->where('masterTBL.poAutoID', trim($this->input->post('poAutoID') ?? ''));
        }
        
        return $this->db->get()->result_array();
    }

    /**
     * Save logistic grv approval
     *
     * @param $autoappLevel
     * @param $system_idAP
     * @param $statusAP
     * @param $commentsAP
     * @return boolean
     */
    public function save_logistic_grv_approval($autoappLevel=1, $system_idAP=0, $statusAP=0, $commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        $companyID = current_companyID();

        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('grvAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['grvAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $this->db->select('*');
        $this->db->where('grvAutoID', $system_code);
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_grvmaster');
        $master = $this->db->get()->row_array();

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, $master['documentID']);
        }

        if ($approvals_status == 1) {
            $this->db->select('SUM(fullTotalAmount) as fullTotalAmount');
            $this->db->where('grvAutoID', $system_code);
            $this->db->from('srp_erp_grvdetails');
            $grvdetail = $this->db->get()->row_array();

            $company_loc_tot = $grvdetail['fullTotalAmount'] / $master['companyLocalExchangeRate'];
            $company_rpt_tot = $grvdetail['fullTotalAmount'] / $master['companyReportingExchangeRate'];
            $supplier_cr_tot = $grvdetail['fullTotalAmount'] / $master['supplierCurrencyExchangeRate'];
            $transaction_tot = $grvdetail['fullTotalAmount'];

            $match_supplierinvoice_arr['grvAutoID'] = $system_code;
            $match_supplierinvoice_arr['addonID'] = 0;
            $match_supplierinvoice_arr['supplierID'] = $master['supplierID'];
            $match_supplierinvoice_arr['bookingAmount'] = $transaction_tot;
            $match_supplierinvoice_arr['bookingCurrencyID'] = $master['transactionCurrencyID'];
            $match_supplierinvoice_arr['bookingCurrency'] = $master['transactionCurrency'];
            $match_supplierinvoice_arr['bookingCurrencyExchangeRate'] = $master['transactionExchangeRate'];
            $match_supplierinvoice_arr['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
            $match_supplierinvoice_arr['companyLocalCurrency'] = $master['companyLocalCurrency'];
            $match_supplierinvoice_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $match_supplierinvoice_arr['companyLocalAmount'] = round(($transaction_tot / $match_supplierinvoice_arr['companyLocalExchangeRate']), 3);
            $match_supplierinvoice_arr['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
            $match_supplierinvoice_arr['companyReportingCurrency'] = $master['companyReportingCurrency'];
            $match_supplierinvoice_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $match_supplierinvoice_arr['companyReportingAmount'] = round(($transaction_tot / $match_supplierinvoice_arr['companyReportingExchangeRate']), 3);
            $match_supplierinvoice_arr['supplierCurrencyID'] = $master['supplierCurrencyID'];
            $match_supplierinvoice_arr['supplierCurrency'] = $master['supplierCurrency'];
            $match_supplierinvoice_arr['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $match_supplierinvoice_arr['supplierCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
            $match_supplierinvoice_arr['supplierCurrencyAmount'] = round(($transaction_tot / $match_supplierinvoice_arr['supplierCurrencyExchangeRate']), $match_supplierinvoice_arr['supplierCurrencyDecimalPlaces']);
            $match_supplierinvoice_arr['isAddon'] = 0;
            $match_supplierinvoice_arr['segmentID'] = $master['segmentID'];
            $match_supplierinvoice_arr['companyID'] = $companyID;

            if (!empty($match_supplierinvoice_arr)) {
                $this->db->insert('srp_erp_match_supplierinvoice', $match_supplierinvoice_arr);
            }

            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_logistic_grv_data($system_code, $master['documentID']);

            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['grvAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['grvPrimaryCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['grvDate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['grvType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['grvDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['grvDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['grvNarration'];
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
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
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
                $generalledger_arr = array_values($generalledger_arr);
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }

            $data['companyLocalAmount'] = round($company_loc_tot, $master['companyLocalCurrencyDecimalPlaces']);
            $data['companyReportingAmount'] = round($company_rpt_tot, $master['companyReportingCurrencyDecimalPlaces']);
            $data['supplierCurrencyAmount'] = round($supplier_cr_tot, $master['supplierCurrencyDecimalPlaces']);
            $data['transactionAmount'] = round($transaction_tot, $master['transactionCurrencyDecimalPlaces']);

            $this->db->where('grvAutoID', $system_code);
            $this->db->update('srp_erp_grvmaster', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return true;
            } else {

                $this->db->trans_commit();
                return true;
            }

        }

    }

    function save_grv_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        $companyID = current_companyID();
        $isconsignment = null;
        $consignmentWareHouseID = null;

        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('grvAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['grvAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        $company_id = $this->common_data['company_data']['company_id'];
        $transaction_tot = 0;
        $company_rpt_tot = 0;
        $supplier_cr_tot = 0;
        $company_loc_tot = 0;

        $this->db->select('*');
        $this->db->where('grvAutoID', $system_code);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_grvmaster');
        $master = $this->db->get()->row_array();

        if(!$master){
            return false;
        }

        $maxLevel = $this->approvals->maxlevel($master['documentID']);

        $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, $master['documentID']);
        }
        if ($approvals_status == 1) {

            if($master){
                $isconsignment = $master['isconsignment'];
                $consignmentWareHouseID = $master['consignmentWarehouseID'];
            }
            
            $vatRegisterYN = $this->db->query("SELECT vatRegisterYN FROM `srp_erp_company` where company_id = $companyID")->row('vatRegisterYN');

            $item_arr = array();
            $itemledger_arr = array();
            $po_arr = array();
            $wareHouseAutoID = $master['wareHouseAutoID'];
            $company_loc = 0;
            $company_rpt = 0;
            $supplier_cr = 0;
            $grvdetail_tot = 0;
            $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_grvmaster',$system_code,$master['documentID'],'grvAutoID');
            
            $this->db->select('*,IFNULL(taxAmount,0) as grvtaxAmount, 0 as taxLedgerAmount');
            $this->db->where('grvAutoID', $system_code);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->from('srp_erp_grvdetails');
            $grvdetails = $this->db->get()->result_array();

            if($isGroupByTax == 1){ 
                $grvdetails = $this->db->query("SELECT
                                                srp_erp_grvdetails.*,
                                                IFNULL( taxAmount, 0 ) AS grvtaxAmount,
                                                IFNULL(grvitemtaxamount.taxLedgerAmount,0)as taxLedgerAmount
                FROM
                `srp_erp_grvdetails` 
                LEFT JOIN (  SELECT
                             sum( amount ) AS taxLedgerAmount,
                             srp_erp_grvdetails.itemAutoID 
                             FROM
                             srp_erp_taxledger
                             LEFT JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_taxledger.companyID
                             LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                             LEFT JOIN srp_erp_grvdetails ON srp_erp_grvdetails.grvDetailsID = srp_erp_taxledger.documentDetailAutoID 
                             WHERE
                             srp_erp_taxledger.documentID = '" . $master['documentID'] . "' 
                            --  AND  srp_erp_taxledger.isClaimable = 0 
                             AND documentMasterAutoID = $system_code 
                             GROUP BY
                             srp_erp_grvdetails.itemAutoID) grvitemtaxamount ON grvitemtaxamount.ItemAutoID = srp_erp_grvdetails.itemAutoID
                             WHERE
                            `grvAutoID` = $system_code 
                            AND `companyID` =$companyID ")->result_array();
            }

            for ($i = 0; $i < count($grvdetails); $i++) {
                if($vatRegisterYN != 1 && $isGroupByTax == 1){
                    $company_loc = (($grvdetails[$i]['fullTotalAmount']+$grvdetails[$i]['taxLedgerAmount']) / $master['companyLocalExchangeRate']);
                    $company_rpt = (($grvdetails[$i]['fullTotalAmount']+$grvdetails[$i]['taxLedgerAmount']) / $master['companyReportingExchangeRate']);
                    $supplier_cr = (($grvdetails[$i]['fullTotalAmount']+$grvdetails[$i]['taxLedgerAmount']) / $master['supplierCurrencyExchangeRate']);
                    $transaction_tot += ($grvdetails[$i]['fullTotalAmount']+$grvdetails[$i]['taxLedgerAmount']);
                    $company_trans = ($grvdetails[$i]['fullTotalAmount']+$grvdetails[$i]['taxLedgerAmount']);
             
                }else { 
                    $company_loc = ($grvdetails[$i]['fullTotalAmount'] / $master['companyLocalExchangeRate']);
                    $company_rpt = ($grvdetails[$i]['fullTotalAmount'] / $master['companyReportingExchangeRate']);
                    $supplier_cr = ($grvdetails[$i]['fullTotalAmount'] / $master['supplierCurrencyExchangeRate']);
                    $transaction_tot += $grvdetails[$i]['fullTotalAmount'];
                    $company_trans = $grvdetails[$i]['fullTotalAmount'];
                }

                $company_loc_tot += $company_loc;
                $company_rpt_tot += $company_rpt;
                $supplier_cr_tot += $supplier_cr;
                
                if($isGroupByTax == 1){
                    $grvdetail_tot += ($grvdetails[$i]['receivedTotalAmount']+$grvdetails[$i]['grvtaxAmount']);
                }else{
                    $grvdetail_tot += $grvdetails[$i]['receivedTotalAmount'];
                }

                $po_arr[$i] = $grvdetails[$i]['purchaseOrderMastertID'];
                $item = fetch_item_data($grvdetails[$i]['itemAutoID']);
                $this->db->select('GLAutoID');
                $this->db->where('controlAccountType', 'ACA');
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();

                $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);

                if ($grvdetails[$i]['itemCategory'] == 'Inventory' or $grvdetails[$i]['itemCategory'] == 'Non Inventory' or $grvdetails[$i]['itemCategory'] =='Service') {
                    $itemAutoID = $grvdetails[$i]['itemAutoID'];
                    $grv_qty = $grvdetails[$i]['receivedQty'] / $grvdetails[$i]['conversionRateUOM'];

                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$grv_qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                    
                    if($isconsignment == 1){
                        $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$grv_qty})  WHERE wareHouseAutoID='{$consignmentWareHouseID}' and itemAutoID='{$itemAutoID}'");
                    }

                    $itemledgerCurrentStock = fetch_itemledger_currentstock($grvdetails[$i]['itemAutoID']);
                    $itemledgerTransactionAmountLocalWac = fetch_itemledger_transactionAmount($grvdetails[$i]['itemAutoID'], 'companyLocalExchangeRate');
                    $itemledgerTransactionAmountReportingWac = fetch_itemledger_transactionAmount($grvdetails[$i]['itemAutoID'],'companyReportingExchangeRate');
                   
                    $item_arr[$i]['itemAutoID'] = $grvdetails[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = ($itemledgerCurrentStock + $grv_qty);
                    $item_arr[$i]['companyLocalWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountLocalWac) + $company_loc) / $item_arr[$i]['currentStock']),wacDecimalPlaces);
                    $item_arr[$i]['companyReportingWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountReportingWac) + $company_rpt) / $item_arr[$i]['currentStock']),wacDecimalPlaces);
                     
                    $itemledger_arr[$i]['documentID'] = $master['documentID'];
                    $itemledger_arr[$i]['documentCode'] = $master['documentID'];
                    $itemledger_arr[$i]['documentAutoID'] = $master['grvAutoID'];
                    $itemledger_arr[$i]['documentSystemCode'] = $master['grvPrimaryCode'];
                    $itemledger_arr[$i]['documentDate'] = $master['grvDate'];
                    $itemledger_arr[$i]['referenceNumber'] = $master['grvDocRefNo'];
                    $itemledger_arr[$i]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                    $itemledger_arr[$i]['companyFinanceYear'] = $master['companyFinanceYear'];
                    $itemledger_arr[$i]['FYBegin'] = $master['FYBegin'];
                    $itemledger_arr[$i]['FYEnd'] = $master['FYEnd'];
                    $itemledger_arr[$i]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                    $itemledger_arr[$i]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                    $itemledger_arr[$i]['wareHouseAutoID'] = $master['wareHouseAutoID'];
                    $itemledger_arr[$i]['wareHouseCode'] = $master['wareHouseCode'];
                    $itemledger_arr[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                    $itemledger_arr[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                    $itemledger_arr[$i]['itemAutoID'] = $grvdetails[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $grvdetails[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $grvdetails[$i]['itemDescription'];
                    $itemledger_arr[$i]['defaultUOMID'] = $grvdetails[$i]['defaultUOMID'];
                    $itemledger_arr[$i]['defaultUOM'] = $grvdetails[$i]['defaultUOM'];
                    $itemledger_arr[$i]['transactionUOMID'] = $grvdetails[$i]['unitOfMeasureID'];
                    $itemledger_arr[$i]['transactionUOM'] = $grvdetails[$i]['unitOfMeasure'];
                    $itemledger_arr[$i]['transactionQTY'] = $grvdetails[$i]['receivedQty'];
                    $itemledger_arr[$i]['convertionRate'] = $grvdetails[$i]['conversionRateUOM'];
                    $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr[$i]['PLGLAutoID'] = $grvdetails[$i]['PLGLAutoID'];
                    $itemledger_arr[$i]['PLSystemGLCode'] = $grvdetails[$i]['PLSystemGLCode'];
                    $itemledger_arr[$i]['PLGLCode'] = $grvdetails[$i]['PLGLCode'];
                    $itemledger_arr[$i]['PLDescription'] = $grvdetails[$i]['PLDescription'];
                    $itemledger_arr[$i]['PLType'] = $grvdetails[$i]['PLType'];
                    $itemledger_arr[$i]['BLGLAutoID'] = $grvdetails[$i]['BLGLAutoID'];
                    $itemledger_arr[$i]['BLSystemGLCode'] = $grvdetails[$i]['BLSystemGLCode'];
                    $itemledger_arr[$i]['BLGLCode'] = $grvdetails[$i]['BLGLCode'];
                    $itemledger_arr[$i]['BLDescription'] = $grvdetails[$i]['BLDescription'];
                    $itemledger_arr[$i]['BLType'] = $grvdetails[$i]['BLType'];
                    $itemledger_arr[$i]['batchNumber'] = $grvdetails[$i]['batchNumber'];
                    $itemledger_arr[$i]['batchExpireDate'] = $grvdetails[$i]['batchExpireDate'];
                    $itemledger_arr[$i]['transactionAmount'] = round($company_trans, $master['transactionCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                    $itemledger_arr[$i]['transactionCurrency'] = $master['transactionCurrency'];
                    $itemledger_arr[$i]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                    $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                    $itemledger_arr[$i]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                    $itemledger_arr[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                    $itemledger_arr[$i]['companyLocalAmount'] = round($company_loc, $master['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['companyLocalWacAmount'] = $item_arr[$i]['companyLocalWacAmount'];
                    $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                    $itemledger_arr[$i]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                    $itemledger_arr[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                    $itemledger_arr[$i]['companyReportingAmount'] = round($company_rpt, $master['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['companyReportingWacAmount'] = $item_arr[$i]['companyReportingWacAmount'];
                    $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['partyCurrencyID'] = $master['supplierCurrencyID'];
                    $itemledger_arr[$i]['partyCurrency'] = $master['supplierCurrency'];
                    $itemledger_arr[$i]['partyCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                    $itemledger_arr[$i]['partyCurrencyAmount'] = round($supplier_cr, $master['supplierCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['confirmedYN'] = $master['confirmedYN'];
                    $itemledger_arr[$i]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                    $itemledger_arr[$i]['confirmedByName'] = $master['confirmedByName'];
                    $itemledger_arr[$i]['confirmedDate'] = $master['confirmedDate'];
                    $itemledger_arr[$i]['approvedYN'] = $master['approvedYN'];
                    $itemledger_arr[$i]['approvedDate'] = $master['approvedDate'];
                    $itemledger_arr[$i]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                    $itemledger_arr[$i]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                    $itemledger_arr[$i]['segmentID'] = $master['segmentID'];
                    $itemledger_arr[$i]['segmentCode'] = $master['segmentCode'];
                    $itemledger_arr[$i]['companyID'] = $master['companyID'];
                    $itemledger_arr[$i]['companyCode'] = $master['companyCode'];
                    $itemledger_arr[$i]['createdUserGroup'] = $master['createdUserGroup'];
                    $itemledger_arr[$i]['createdPCID'] = $master['createdPCID'];
                    $itemledger_arr[$i]['createdUserID'] = $master['createdUserID'];
                    $itemledger_arr[$i]['createdDateTime'] = $master['createdDateTime'];
                    $itemledger_arr[$i]['createdUserName'] = $master['createdUserName'];
                    $itemledger_arr[$i]['modifiedPCID'] = $master['modifiedPCID'];
                    $itemledger_arr[$i]['modifiedUserID'] = $master['modifiedUserID'];
                    $itemledger_arr[$i]['modifiedDateTime'] = $master['modifiedDateTime'];
                    $itemledger_arr[$i]['modifiedUserName'] = $master['modifiedUserName'];

                    if($isconsignment == 1){
                        $consignemnt_arr = array();
                        $consignemnt_arr = $itemledger_arr[$i];
                        
                      
                        $this->db->select('*');
                        $this->db->where('wareHouseAutoID', $consignmentWareHouseID );
                        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                        $conWarehouseDetail = $this->db->get('srp_erp_warehousemaster')->row_array();

                        $consignemnt_arr['wareHouseAutoID'] =  $consignmentWareHouseID;
                        $consignemnt_arr['wareHouseCode'] = $conWarehouseDetail['wareHouseCode'];
                        $consignemnt_arr['wareHouseLocation'] = $conWarehouseDetail['wareHouseLocation'];
                        $consignemnt_arr['wareHouseDescription'] = $conWarehouseDetail['wareHouseDescription'];
                        $consignemnt_arr['transactionQTY'] = $itemledger_arr[$i]['transactionQTY'] * -1;
                        $consignemnt_arr['transactionAmount'] = 0;
                        $consignemnt_arr['companyLocalAmount'] = 0;
                        $consignemnt_arr['companyReportingAmount'] = 0;
                        $consignemnt_arr['partyCurrencyAmount'] = 0;
                        
                        $this->db->insert('srp_erp_itemledger', $consignemnt_arr);

                    }


                } elseif ($grvdetails[$i]['itemCategory'] == 'Fixed Assets') {
                    $this->load->library('sequence');
                    $assat_data = array();
                    $assat_amount = (($grvdetails[$i]['fullTotalAmount']/$grvdetails[$i]['receivedQty']) / $grvdetails[$i]['conversionRateUOM']);
                    for ($a = 0; $a < ($grvdetails[$i]['receivedQty'] / $grvdetails[$i]['conversionRateUOM']); $a++) {
                        $assat_data[$a]['documentID'] = 'FA';
                        $assat_data[$a]['assetDescription'] = $item['itemDescription'];

                        //Add group to Assets on po base grv

                        if($grvdetails[$i]['purchaseOrderDetailsID']!=0){

                            $this->db->select('*');
                            $this->db->where('purchaseOrderDetailsID', $grvdetails[$i]['purchaseOrderDetailsID']);
                            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                            $po_details_fa = $this->db->get('srp_erp_purchaseorderdetails')->row_array();

                            if($po_details_fa){
                                $assat_data[$a]['groupTO'] = $po_details_fa['groupAssetsID'];
                                $assat_data[$a]['replacementAssetsID'] = $po_details_fa['replacementAssetsID'];
                            }

                        }
                         // end Add group to Assets on po base grv
                        $assat_data[$a]['docOriginSystemCode'] = $system_code;
                        $assat_data[$a]['docOriginDetailID'] = $grvdetails[$i]['grvDetailsID'];
                        $assat_data[$a]['batchNumber'] = $grvdetails[$i]['batchNumber'];
                        $assat_data[$a]['batchExpireDate'] = $grvdetails[$i]['expireDate'];
                        $assat_data[$a]['docOrigin'] = $master['documentID'];
                        $assat_data[$a]['dateAQ'] = $master['grvDate'];
                        $assat_data[$a]['grvAutoID'] = $system_code;
                        $assat_data[$a]['isFromGRV'] = 1;
                        $assat_data[$a]['comments'] = trim($this->input->post('comments') ?? '');
                        $assat_data[$a]['faCatID'] = $item['subcategoryID'];
                        $assat_data[$a]['faSubCatID'] = $item['subSubCategoryID'];
                        $assat_data[$a]['faSubCatID2'] = null;
                        $assat_data[$a]['assetType'] = 1;
                        $assat_data[$a]['transactionAmount'] = $assat_amount;
                        $assat_data[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $assat_data[$a]['transactionCurrency'] = $master['transactionCurrency'];
                        $assat_data[$a]['transactionCurrencyExchangeRate'] = $master['transactionExchangeRate'];
                        $assat_data[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $assat_data[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $assat_data[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $assat_data[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $assat_data[$a]['companyLocalAmount'] = round($assat_amount/$master['companyLocalExchangeRate'], $assat_data[$a]['transactionCurrencyDecimalPlaces']);
                        $assat_data[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                        $assat_data[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $assat_data[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $assat_data[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                        $assat_data[$a]['companyReportingAmount'] = round($assat_amount/$master['companyReportingExchangeRate'], $assat_data[$a]['companyLocalCurrencyDecimalPlaces']);
                        $assat_data[$a]['companyReportingDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                        $assat_data[$a]['supplierID'] = $master['supplierID'];
                        $assat_data[$a]['segmentID'] = $master['segmentID'];
                        $assat_data[$a]['segmentCode'] = $master['segmentCode'];
                        $assat_data[$a]['companyID'] = $master['companyID'];
                        $assat_data[$a]['companyCode'] = $master['companyCode'];
                        $assat_data[$a]['createdUserGroup'] = $master['createdUserGroup'];
                        $assat_data[$a]['createdPCID'] = $master['createdPCID'];
                        $assat_data[$a]['createdUserID'] = $master['createdUserID'];
                        $assat_data[$a]['createdDateTime'] = $master['createdDateTime'];
                        $assat_data[$a]['createdUserName'] = $master['createdUserName'];
                        $assat_data[$a]['modifiedPCID'] = $master['modifiedPCID'];
                        $assat_data[$a]['modifiedUserID'] = $master['modifiedUserID'];
                        $assat_data[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                        $assat_data[$a]['modifiedUserName'] = $master['modifiedUserName'];
                        $assat_data[$a]['costGLAutoID'] = $item['faCostGLAutoID'];
                        $assat_data[$a]['ACCDEPGLAutoID'] = $item['faACCDEPGLAutoID'];
                        $assat_data[$a]['DEPGLAutoID'] = $item['faDEPGLAutoID'];
                        $assat_data[$a]['DISPOGLAutoID'] = $item['faDISPOGLAutoID'];
                        $assat_data[$a]['isPostToGL'] = 1;
                        $assat_data[$a]['postGLAutoID'] = $ACA_ID['GLAutoID'];
                        $assat_data[$a]['postGLCode'] = $ACA['systemAccountCode'];
                        $assat_data[$a]['postGLCodeDes'] = $ACA['GLDescription'];
                        $assat_data[$a]['faCode'] = $this->sequence->sequence_generator("FA");
                    }

                    if (!empty($assat_data)) {
                        $assat_data = array_values($assat_data);
                        $this->db->insert_batch('srp_erp_fa_asset_master', $assat_data);
                    }
                }
            }

            if (!empty($item_arr)) {
                $item_arr = array_values($item_arr);
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }

            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_grv_data($system_code, $master['documentID']);

           
            $isGroupByPolicyExists =  (existTaxPolicyDocumentWise('srp_erp_grvmaster',$system_code,$master['documentID'],'grvAutoID')!=''?existTaxPolicyDocumentWise('srp_erp_grvmaster',$system_code,'GRV','grvAutoID'):0);
            if($isGroupByPolicyExists == 1){ 
                $double_entry =  $this->Double_entry_model->fetch_double_entry_grv_data_groupbased($system_code,$master['documentID']);
            }
          
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['grvAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['grvPrimaryCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['grvDate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['grvType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['grvDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['grvDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['grvNarration'];
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
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
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
                $generalledger_arr = array_values($generalledger_arr);
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }

            /** update Manufacturing Job Qty Based on Job Number */
            if($master['jobID']) {
                $this->load->model('Inventory_modal');
                $this->Inventory_modal->updateJobQty($master['jobID'], $system_code, $master['documentID']);
            }
            /**End Of update Manufacturing Job Qty Based on Job Number */

            $this->db->select('id,supplierID,(bookingCurrencyAmount + IFNULL(taxAmount,0)) as bookingCurrencyAmount,bookingCurrency,bookingCurrencyID,supplierCurrencyID ,bookingCurrencyExchangeRate, supplierCurrency ,supplierCurrencyExchangeRate,supplierCurrencyDecimalPlaces,companyLocalCurrency,companyLocalExchangeRate,companyReportingCurrency,companyReportingExchangeRate,(total_amount + IFNULL(taxAmount,0)) as total_amount,companyLocalCurrencyID,companyReportingCurrencyID');
            $this->db->from('srp_erp_grv_addon');
            $this->db->where('grvAutoID', $system_code);
            //$this->db->where('isChargeToExpense', 0);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $grv_addon_arr = $this->db->get()->result_array();
            $match_supplierinvoice_arr = array();
            for ($x = 0; $x < count($grv_addon_arr); $x++) {
                $match_supplierinvoice_arr[$x]['grvAutoID'] = $system_code;
                $match_supplierinvoice_arr[$x]['addonID'] = $grv_addon_arr[$x]['id'];
                $match_supplierinvoice_arr[$x]['supplierID'] = $grv_addon_arr[$x]['supplierID'];
                $match_supplierinvoice_arr[$x]['bookingAmount'] = $grv_addon_arr[$x]['bookingCurrencyAmount'];
                $match_supplierinvoice_arr[$x]['addonID'] = $grv_addon_arr[$x]['id'];
                $match_supplierinvoice_arr[$x]['bookingCurrencyID'] = $grv_addon_arr[$x]['bookingCurrencyID'];
                $match_supplierinvoice_arr[$x]['bookingCurrency'] = $grv_addon_arr[$x]['bookingCurrency'];
                $match_supplierinvoice_arr[$x]['bookingCurrencyExchangeRate'] = $grv_addon_arr[$x]['bookingCurrencyExchangeRate'];
                $match_supplierinvoice_arr[$x]['companyLocalCurrencyID'] = $grv_addon_arr[$x]['companyLocalCurrencyID'];
                $match_supplierinvoice_arr[$x]['companyLocalCurrency'] = $grv_addon_arr[$x]['companyLocalCurrency'];
                $match_supplierinvoice_arr[$x]['companyLocalExchangeRate'] = $grv_addon_arr[$x]['companyLocalExchangeRate'];
                $match_supplierinvoice_arr[$x]['companyLocalAmount'] = round(($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyLocalExchangeRate']), 3);
                $match_supplierinvoice_arr[$x]['companyReportingCurrencyID'] = $grv_addon_arr[$x]['companyReportingCurrencyID'];
                $match_supplierinvoice_arr[$x]['companyReportingCurrency'] = $grv_addon_arr[$x]['companyReportingCurrency'];
                $match_supplierinvoice_arr[$x]['companyReportingExchangeRate'] = $grv_addon_arr[$x]['companyReportingExchangeRate'];
                $match_supplierinvoice_arr[$x]['companyReportingAmount'] = round(($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyReportingExchangeRate']), 3);
                $match_supplierinvoice_arr[$x]['supplierCurrencyID'] = $grv_addon_arr[$x]['supplierCurrencyID'];
                $match_supplierinvoice_arr[$x]['supplierCurrency'] = $grv_addon_arr[$x]['supplierCurrency'];
                $match_supplierinvoice_arr[$x]['supplierCurrencyExchangeRate'] = $grv_addon_arr[$x]['supplierCurrencyExchangeRate'];
                $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces'] = $grv_addon_arr[$x]['supplierCurrencyDecimalPlaces'];
                $match_supplierinvoice_arr[$x]['supplierCurrencyAmount'] = round(($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['supplierCurrencyExchangeRate']), $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces']);
                $match_supplierinvoice_arr[$x]['isAddon'] = 1;
                $match_supplierinvoice_arr[$x]['segmentID'] = $master['segmentID'];
                $match_supplierinvoice_arr[$x]['companyID'] = $this->common_data['company_data']['company_id'];

                $transaction_tot += $grv_addon_arr[$x]['total_amount'];
                $company_loc_tot += ($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyLocalExchangeRate']);
                $company_rpt_tot += ($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyReportingExchangeRate']);
                $supplier_cr_tot += ($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['supplierCurrencyExchangeRate']);
            }
            $x++;
            $match_supplierinvoice_arr[$x]['grvAutoID'] = $system_code;
            $match_supplierinvoice_arr[$x]['addonID'] =0;
            $match_supplierinvoice_arr[$x]['supplierID'] = $master['supplierID'];
            $match_supplierinvoice_arr[$x]['bookingAmount'] = $grvdetail_tot;
            $match_supplierinvoice_arr[$x]['bookingCurrencyID'] = $master['transactionCurrencyID'];
            $match_supplierinvoice_arr[$x]['bookingCurrency'] = $master['transactionCurrency'];
            $match_supplierinvoice_arr[$x]['bookingCurrencyExchangeRate'] = $master['transactionExchangeRate'];
            $match_supplierinvoice_arr[$x]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
            $match_supplierinvoice_arr[$x]['companyLocalCurrency'] = $master['companyLocalCurrency'];
            $match_supplierinvoice_arr[$x]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $match_supplierinvoice_arr[$x]['companyLocalAmount'] = round(($match_supplierinvoice_arr[$x]['bookingAmount'] / $match_supplierinvoice_arr[$x]['companyLocalExchangeRate']), 3);
            $match_supplierinvoice_arr[$x]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
            $match_supplierinvoice_arr[$x]['companyReportingCurrency'] = $master['companyReportingCurrency'];
            $match_supplierinvoice_arr[$x]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $match_supplierinvoice_arr[$x]['companyReportingAmount'] = round(($match_supplierinvoice_arr[$x]['bookingAmount'] / $match_supplierinvoice_arr[$x]['companyReportingExchangeRate']), 3);
            $match_supplierinvoice_arr[$x]['supplierCurrencyID'] = $master['supplierCurrencyID'];
            $match_supplierinvoice_arr[$x]['supplierCurrency'] = $master['supplierCurrency'];
            $match_supplierinvoice_arr[$x]['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
            $match_supplierinvoice_arr[$x]['supplierCurrencyAmount'] = round(($match_supplierinvoice_arr[$x]['bookingAmount'] / $match_supplierinvoice_arr[$x]['supplierCurrencyExchangeRate']), $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces']);
            $match_supplierinvoice_arr[$x]['isAddon'] = 0;
            $match_supplierinvoice_arr[$x]['segmentID'] = $master['segmentID'];
            $match_supplierinvoice_arr[$x]['companyID'] = $this->common_data['company_data']['company_id'];

            if (!empty($match_supplierinvoice_arr)) {
                $this->db->insert_batch('srp_erp_match_supplierinvoice', $match_supplierinvoice_arr);
            }

            $data['companyLocalAmount'] = round($company_loc_tot, $master['companyLocalCurrencyDecimalPlaces']);
            $data['companyReportingAmount'] = round($company_rpt_tot, $master['companyReportingCurrencyDecimalPlaces']);
            $data['supplierCurrencyAmount'] = round($supplier_cr_tot, $master['supplierCurrencyDecimalPlaces']);
            $data['transactionAmount'] = round($transaction_tot, $master['transactionCurrencyDecimalPlaces']);

            $this->db->where('grvAutoID', $system_code);
            $this->db->update('srp_erp_grvmaster', $data);

            $this->db->query("UPDATE srp_erp_purchaseordermaster prd
                                    JOIN (
                                        SELECT
                                            purchaseOrderID AS pid,
                                            (
                                                CASE
                                                WHEN balance = 0 THEN
                                                    '2'
                                                WHEN balance = requestedtqy THEN
                                                    '0'
                                                ELSE
                                                    '1'
                                                END
                                            ) AS sts
                                        FROM
                                            (
                                                SELECT
                                        t2.purchaseOrderID,
                                    sum(requestedtqy) as requestedtqy ,
                                        sum(balance) AS balance
                                    FROM
                                        (
                                    SELECT
                                                po.purchaseOrderDetailsID,
                                                purchaseOrderID,
                                                po.itemAutoID,
                                                ifnull((po.requestedQty),0) AS requestedtqy,
                                                (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0)) AS receivedqty,
                                            IF (
                                                (
                                                    (po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0))
                                                ) < 0,
                                                0,
                                                (
                                                    (po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0))
                                                )
                                            ) AS balance
                                            FROM
                                                srp_erp_purchaseorderdetails po
                                            LEFT JOIN (
                                                SELECT
                                                    purchaseOrderMastertID,
                                                    ifnull(sum(requestedQty),0) AS receivedQty,
                                                    itemAutoID,
                                                    purchaseOrderDetailsID
                                                FROM
                                                    srp_erp_paysupplierinvoicedetail
                                            left join srp_erp_paysupplierinvoicemaster sinm on srp_erp_paysupplierinvoicedetail.InvoiceAutoID=sinm.InvoiceAutoID
                                                    where sinm.approvedYN=1
                                                GROUP BY
                                                srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID
                                            ) gd ON po.purchaseOrderDetailsID=gd.purchaseOrderDetailsID

                                                    LEFT JOIN (
                                                SELECT
                                                    purchaseOrderMastertID,
                                                    ifnull(sum(receivedQty),0) AS receivedQty,
                                                    itemAutoID,
                                                    purchaseOrderDetailsID
                                                FROM
                                                    srp_erp_grvdetails
                                            left join srp_erp_grvmaster grvm on srp_erp_grvdetails.grvAutoID=grvm.grvAutoID
                                                    where grvm.grvType='PO Base' and grvm.approvedYN=1
                                                GROUP BY
                                                srp_erp_grvdetails.purchaseOrderDetailsID
                                            ) grd ON po.purchaseOrderDetailsID=grd.purchaseOrderDetailsID

                                        ) t2 group by t2.purchaseOrderID
                                            ) z
                                    ) tt ON prd.purchaseOrderID = tt.pid
                                    SET prd.isReceived = tt.sts
                                    where  prd.companyID = $company_id AND prd.purchaseOrderID=tt.pid");

            /** update sub item master : shafry */
            if ($isFinalLevel) {
                $masterID = $this->input->post('grvAutoID');
                $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                if (!empty($result)) {
                    $i = 0;
                    foreach ($result as $item) {
                        unset($result[$i]['subItemAutoID']);
                        $i++;
                    }

                    $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $masterID));

                }
            }
            $itemAutoIDarry = array();
            foreach ($grvdetails as $value) {
                array_push($itemAutoIDarry, $value['itemAutoID']);
            }
            $companyID = current_companyID();
            $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID= '" . $master ['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
            $exceededMatchID = 0;
            if (!empty($exceededitems_master)) {
                $this->load->library('sequence');
                $exceededmatch['documentID'] = "EIM";
                $exceededmatch['documentDate'] = $master ['grvDate'];
                $exceededmatch['orginDocumentID'] = $master ['documentID'];
                $exceededmatch['orginDocumentMasterID'] = $master ['grvAutoID'];
                $exceededmatch['orginDocumentSystemCode'] = $master ['grvPrimaryCode'];
                $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                $exceededmatch['companyID'] = current_companyID();
                $exceededmatch['transactionCurrencyID'] = $master ['transactionCurrencyID'];
                $exceededmatch['transactionCurrency'] = $master ['transactionCurrency'];
                $exceededmatch['transactionExchangeRate'] = $master ['transactionExchangeRate'];
                $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['transactionCurrencyDecimalPlaces'];
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

            foreach ($grvdetails as $itemid) {
                $receivedQty = $itemid['receivedQty'];
                $receivedQtyConverted = $itemid['receivedQty'] / $itemid['conversionRateUOM'];
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
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {

            $this->db->select('*');
            $this->db->where('grvAutoID', $system_code);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->from('srp_erp_grvmaster');
            $grv_all= $this->db->get()->row_array();

            //send grv in srm module
            if($grv_all){
                if(isset($grv_all['isSrmGenerated']) && $grv_all['isSrmGenerated'] == 1){
                    send_approve_po_srm_portal($grv_all['srmGrvAutoID'],2);  // 1-po ,2-grv
                }
            }

            $this->db->trans_commit();
            return true;
        }
    }

    function save_grv_approval_buyback()
    {

        $this->db->trans_start();
        $this->load->library('Approvals');
        $system_code = trim($this->input->post('grvAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $company_id = $this->common_data['company_data']['company_id'];
        $transaction_tot = 0;
        $company_rpt_tot = 0;
        $supplier_cr_tot = 0;
        $company_loc_tot = 0;

        $maxLevel = $this->approvals->maxlevel('GRV');

        $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'GRV');

        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->where('grvAutoID', $system_code);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->from('srp_erp_grvmaster');
            $master = $this->db->get()->row_array();

            $this->db->select('*');
            $this->db->where('grvAutoID', $system_code);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->from('srp_erp_grvdetails');
            $grvdetails = $this->db->get()->result_array();

            $item_arr = array();
            $itemledger_arr = array();
            $po_arr = array();
            $wareHouseAutoID = $master['wareHouseAutoID'];
            $company_loc = 0;
            $company_rpt = 0;
            $supplier_cr = 0;
            $grvdetail_tot = 0;

            for ($i = 0; $i < count($grvdetails); $i++) {
                $company_loc = ($grvdetails[$i]['fullTotalAmount'] / $master['companyLocalExchangeRate']);
                $company_rpt = ($grvdetails[$i]['fullTotalAmount'] / $master['companyReportingExchangeRate']);
                $supplier_cr = ($grvdetails[$i]['fullTotalAmount'] / $master['supplierCurrencyExchangeRate']);

                $transaction_tot += $grvdetails[$i]['fullTotalAmount'];
                $company_loc_tot += $company_loc;
                $company_rpt_tot += $company_rpt;
                $supplier_cr_tot += $supplier_cr;
                $grvdetail_tot += $grvdetails[$i]['receivedTotalAmount'];

                $po_arr[$i] = $grvdetails[$i]['purchaseOrderMastertID'];
                $item = fetch_item_data($grvdetails[$i]['itemAutoID']);
                //$ACA_ID = $this->common_data['controlaccounts']['ACA'];
                $this->db->select('GLAutoID');
                $this->db->where('controlAccountType', 'ACA');
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
                $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
                if ($grvdetails[$i]['itemCategory'] == 'Inventory' or $grvdetails[$i]['itemCategory'] == 'Non Inventory' or $grvdetails[$i]['itemCategory'] =='Service') {
                    $itemAutoID = $grvdetails[$i]['itemAutoID'];
                    $grv_qty = $grvdetails[$i]['receivedQty'] / $grvdetails[$i]['conversionRateUOM'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$grv_qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                    $item_arr[$i]['itemAutoID'] = $grvdetails[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = ($item['currentStock'] + $grv_qty);
                    $item_arr[$i]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) + $company_loc) / $item_arr[$i]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                    $item_arr[$i]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) + $company_rpt) / $item_arr[$i]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['documentID'] = $master['documentID'];
                    $itemledger_arr[$i]['documentCode'] = $master['documentID'];
                    $itemledger_arr[$i]['documentAutoID'] = $master['grvAutoID'];
                    $itemledger_arr[$i]['documentSystemCode'] = $master['grvPrimaryCode'];
                    $itemledger_arr[$i]['documentDate'] = $master['grvDate'];
                    $itemledger_arr[$i]['referenceNumber'] = $master['grvDocRefNo'];
                    $itemledger_arr[$i]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                    $itemledger_arr[$i]['companyFinanceYear'] = $master['companyFinanceYear'];
                    $itemledger_arr[$i]['FYBegin'] = $master['FYBegin'];
                    $itemledger_arr[$i]['FYEnd'] = $master['FYEnd'];
                    $itemledger_arr[$i]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                    $itemledger_arr[$i]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                    $itemledger_arr[$i]['wareHouseAutoID'] = $master['wareHouseAutoID'];
                    $itemledger_arr[$i]['wareHouseCode'] = $master['wareHouseCode'];
                    $itemledger_arr[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                    $itemledger_arr[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                    $itemledger_arr[$i]['itemAutoID'] = $grvdetails[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $grvdetails[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $grvdetails[$i]['itemDescription'];
                    $itemledger_arr[$i]['defaultUOMID'] = $grvdetails[$i]['defaultUOMID'];
                    $itemledger_arr[$i]['defaultUOM'] = $grvdetails[$i]['defaultUOM'];
                    $itemledger_arr[$i]['transactionUOMID'] = $grvdetails[$i]['unitOfMeasureID'];
                    $itemledger_arr[$i]['transactionUOM'] = $grvdetails[$i]['unitOfMeasure'];
                    $itemledger_arr[$i]['transactionQTY'] = $grvdetails[$i]['receivedQty'];
                    $itemledger_arr[$i]['convertionRate'] = $grvdetails[$i]['conversionRateUOM'];
                    $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr[$i]['PLGLAutoID'] = $grvdetails[$i]['PLGLAutoID'];
                    $itemledger_arr[$i]['PLSystemGLCode'] = $grvdetails[$i]['PLSystemGLCode'];
                    $itemledger_arr[$i]['PLGLCode'] = $grvdetails[$i]['PLGLCode'];
                    $itemledger_arr[$i]['PLDescription'] = $grvdetails[$i]['PLDescription'];
                    $itemledger_arr[$i]['PLType'] = $grvdetails[$i]['PLType'];
                    $itemledger_arr[$i]['BLGLAutoID'] = $grvdetails[$i]['BLGLAutoID'];
                    $itemledger_arr[$i]['BLSystemGLCode'] = $grvdetails[$i]['BLSystemGLCode'];
                    $itemledger_arr[$i]['BLGLCode'] = $grvdetails[$i]['BLGLCode'];
                    $itemledger_arr[$i]['BLDescription'] = $grvdetails[$i]['BLDescription'];
                    $itemledger_arr[$i]['BLType'] = $grvdetails[$i]['BLType'];
                    $itemledger_arr[$i]['batchNumber'] = $grvdetails[$i]['batchNumber'];
                    $itemledger_arr[$i]['batchExpireDate'] = $grvdetails[$i]['expireDate'];
                    $itemledger_arr[$i]['transactionAmount'] = round($grvdetails[$i]['fullTotalAmount'], $master['transactionCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                    $itemledger_arr[$i]['transactionCurrency'] = $master['transactionCurrency'];
                    $itemledger_arr[$i]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                    $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                    $itemledger_arr[$i]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                    $itemledger_arr[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                    $itemledger_arr[$i]['companyLocalAmount'] = round($company_loc, $master['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['companyLocalWacAmount'] = $item_arr[$i]['companyLocalWacAmount'];
                    $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                    $itemledger_arr[$i]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                    $itemledger_arr[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                    $itemledger_arr[$i]['companyReportingAmount'] = round($company_rpt, $master['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['companyReportingWacAmount'] = $item_arr[$i]['companyReportingWacAmount'];
                    $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['partyCurrencyID'] = $master['supplierCurrencyID'];
                    $itemledger_arr[$i]['partyCurrency'] = $master['supplierCurrency'];
                    $itemledger_arr[$i]['partyCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                    $itemledger_arr[$i]['partyCurrencyAmount'] = round($supplier_cr, $master['supplierCurrencyDecimalPlaces']);
                    $itemledger_arr[$i]['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['confirmedYN'] = $master['confirmedYN'];
                    $itemledger_arr[$i]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                    $itemledger_arr[$i]['confirmedByName'] = $master['confirmedByName'];
                    $itemledger_arr[$i]['confirmedDate'] = $master['confirmedDate'];
                    $itemledger_arr[$i]['approvedYN'] = $master['approvedYN'];
                    $itemledger_arr[$i]['approvedDate'] = $master['approvedDate'];
                    $itemledger_arr[$i]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                    $itemledger_arr[$i]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                    $itemledger_arr[$i]['segmentID'] = $master['segmentID'];
                    $itemledger_arr[$i]['segmentCode'] = $master['segmentCode'];
                    $itemledger_arr[$i]['companyID'] = $master['companyID'];
                    $itemledger_arr[$i]['companyCode'] = $master['companyCode'];
                    $itemledger_arr[$i]['createdUserGroup'] = $master['createdUserGroup'];
                    $itemledger_arr[$i]['createdPCID'] = $master['createdPCID'];
                    $itemledger_arr[$i]['createdUserID'] = $master['createdUserID'];
                    $itemledger_arr[$i]['createdDateTime'] = $master['createdDateTime'];
                    $itemledger_arr[$i]['createdUserName'] = $master['createdUserName'];
                    $itemledger_arr[$i]['modifiedPCID'] = $master['modifiedPCID'];
                    $itemledger_arr[$i]['modifiedUserID'] = $master['modifiedUserID'];
                    $itemledger_arr[$i]['modifiedDateTime'] = $master['modifiedDateTime'];
                    $itemledger_arr[$i]['modifiedUserName'] = $master['modifiedUserName'];
                } elseif ($grvdetails[$i]['itemCategory'] == 'Fixed Assets') {
                    $this->load->library('sequence');
                    $assat_data = array();
                    $assat_amount = ($grvdetails[$i]['fullTotalAmount'] / ($grvdetails[$i]['receivedQty'] / $grvdetails[$i]['conversionRateUOM']));
                    for ($a = 0; $a < ($grvdetails[$i]['receivedQty'] / $grvdetails[$i]['conversionRateUOM']); $a++) {
                        $assat_data[$a]['documentID'] = 'FA';
                        $assat_data[$a]['assetDescription'] = $item['itemDescription'];
                        // $assat_data[$a]['MANUFACTURE']                         = trim($this->input->post('MANUFACTURE') ?? '');
                        $assat_data[$a]['docOriginSystemCode'] = $system_code;
                        $assat_data[$a]['docOriginDetailID'] = $grvdetails[$i]['grvDetailsID'];
                        $assat_data[$a]['docOrigin'] = 'GRV';
                        $assat_data[$a]['dateAQ'] = $master['grvDate'];
                        $assat_data[$a]['grvAutoID'] = $system_code;
                        $assat_data[$a]['isFromGRV'] = 1;
                        $assat_data[$a]['comments'] = trim($this->input->post('comments') ?? '');
                        $assat_data[$a]['faCatID'] = $item['subcategoryID'];
                        $assat_data[$a]['faSubCatID'] = $item['subSubCategoryID'];
                        $assat_data[$a]['faSubCatID2'] = null;
                        $assat_data[$a]['assetType'] = 1;
                        $assat_data[$a]['transactionAmount'] = $assat_amount;
                        $assat_data[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $assat_data[$a]['transactionCurrency'] = $master['transactionCurrency'];
                        $assat_data[$a]['transactionCurrencyExchangeRate'] = $master['transactionExchangeRate'];
                        $assat_data[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $assat_data[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $assat_data[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $assat_data[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $assat_data[$a]['companyLocalAmount'] = round($assat_amount, $assat_data[$a]['transactionCurrencyDecimalPlaces']);
                        $assat_data[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                        $assat_data[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $assat_data[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $assat_data[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                        $assat_data[$a]['companyReportingAmount'] = round($assat_amount, $assat_data[$a]['companyLocalCurrencyDecimalPlaces']);
                        $assat_data[$a]['companyReportingDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                        $assat_data[$a]['supplierID'] = $master['supplierID'];
                        $assat_data[$a]['segmentID'] = $master['segmentID'];
                        $assat_data[$a]['segmentCode'] = $master['segmentCode'];
                        $assat_data[$a]['companyID'] = $master['companyID'];
                        $assat_data[$a]['companyCode'] = $master['companyCode'];
                        $assat_data[$a]['createdUserGroup'] = $master['createdUserGroup'];
                        $assat_data[$a]['createdPCID'] = $master['createdPCID'];
                        $assat_data[$a]['createdUserID'] = $master['createdUserID'];
                        $assat_data[$a]['createdDateTime'] = $master['createdDateTime'];
                        $assat_data[$a]['createdUserName'] = $master['createdUserName'];
                        $assat_data[$a]['modifiedPCID'] = $master['modifiedPCID'];
                        $assat_data[$a]['modifiedUserID'] = $master['modifiedUserID'];
                        $assat_data[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                        $assat_data[$a]['modifiedUserName'] = $master['modifiedUserName'];
                        $assat_data[$a]['costGLAutoID'] = $item['faCostGLAutoID'];
                        $assat_data[$a]['ACCDEPGLAutoID'] = $item['faACCDEPGLAutoID'];
                        $assat_data[$a]['DEPGLAutoID'] = $item['faDEPGLAutoID'];
                        $assat_data[$a]['DISPOGLAutoID'] = $item['faDISPOGLAutoID'];
                        $assat_data[$a]['isPostToGL'] = 1;
                        $assat_data[$a]['postGLAutoID'] = $ACA_ID['GLAutoID'];
                        $assat_data[$a]['postGLCode'] = $ACA['systemAccountCode'];
                        $assat_data[$a]['postGLCodeDes'] = $ACA['GLDescription'];
                        $assat_data[$a]['faCode'] = $this->sequence->sequence_generator("FA");
                    }

                    if (!empty($assat_data)) {
                        $assat_data = array_values($assat_data);
                        $this->db->insert_batch('srp_erp_fa_asset_master', $assat_data);
                    }
                }
            }

            if (!empty($item_arr)) {
                $item_arr = array_values($item_arr);
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }

            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_grv_data($system_code, 'GRV');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['grvAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['grvPrimaryCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['grvDate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['grvType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['grvDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['grvDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['grvNarration'];
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
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
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
                $generalledger_arr = array_values($generalledger_arr);
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }

            $this->db->select('supplierID,bookingCurrencyAmount,bookingCurrency,bookingCurrencyID,supplierCurrencyID ,bookingCurrencyExchangeRate, supplierCurrency ,supplierCurrencyExchangeRate,supplierCurrencyDecimalPlaces,companyLocalCurrency,companyLocalExchangeRate,companyReportingCurrency,companyReportingExchangeRate,total_amount,companyLocalCurrencyID,companyReportingCurrencyID');
            $this->db->from('srp_erp_grv_addon');
            $this->db->where('grvAutoID', $system_code);
            //$this->db->where('isChargeToExpense', 0);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $grv_addon_arr = $this->db->get()->result_array();
            $match_supplierinvoice_arr = array();
            for ($x = 0; $x < count($grv_addon_arr); $x++) {
                $match_supplierinvoice_arr[$x]['grvAutoID'] = $system_code;
                $match_supplierinvoice_arr[$x]['supplierID'] = $grv_addon_arr[$x]['supplierID'];
                $match_supplierinvoice_arr[$x]['bookingAmount'] = $grv_addon_arr[$x]['bookingCurrencyAmount'];
                $match_supplierinvoice_arr[$x]['bookingCurrencyID'] = $grv_addon_arr[$x]['bookingCurrencyID'];
                $match_supplierinvoice_arr[$x]['bookingCurrency'] = $grv_addon_arr[$x]['bookingCurrency'];
                $match_supplierinvoice_arr[$x]['bookingCurrencyExchangeRate'] = $grv_addon_arr[$x]['bookingCurrencyExchangeRate'];
                $match_supplierinvoice_arr[$x]['companyLocalCurrencyID'] = $grv_addon_arr[$x]['companyLocalCurrencyID'];
                $match_supplierinvoice_arr[$x]['companyLocalCurrency'] = $grv_addon_arr[$x]['companyLocalCurrency'];
                $match_supplierinvoice_arr[$x]['companyLocalExchangeRate'] = $grv_addon_arr[$x]['companyLocalExchangeRate'];
                $match_supplierinvoice_arr[$x]['companyLocalAmount'] = round(($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyLocalExchangeRate']), 3);
                $match_supplierinvoice_arr[$x]['companyReportingCurrencyID'] = $grv_addon_arr[$x]['companyReportingCurrencyID'];
                $match_supplierinvoice_arr[$x]['companyReportingCurrency'] = $grv_addon_arr[$x]['companyReportingCurrency'];
                $match_supplierinvoice_arr[$x]['companyReportingExchangeRate'] = $grv_addon_arr[$x]['companyReportingExchangeRate'];
                $match_supplierinvoice_arr[$x]['companyReportingAmount'] = round(($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyReportingExchangeRate']), 3);
                $match_supplierinvoice_arr[$x]['supplierCurrencyID'] = $grv_addon_arr[$x]['supplierCurrencyID'];
                $match_supplierinvoice_arr[$x]['supplierCurrency'] = $grv_addon_arr[$x]['supplierCurrency'];
                $match_supplierinvoice_arr[$x]['supplierCurrencyExchangeRate'] = $grv_addon_arr[$x]['supplierCurrencyExchangeRate'];
                $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces'] = $grv_addon_arr[$x]['supplierCurrencyDecimalPlaces'];
                $match_supplierinvoice_arr[$x]['supplierCurrencyAmount'] = round(($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['supplierCurrencyExchangeRate']), $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces']);
                $match_supplierinvoice_arr[$x]['isAddon'] = 1;
                $match_supplierinvoice_arr[$x]['segmentID'] = $master['segmentID'];
                $match_supplierinvoice_arr[$x]['companyID'] = $this->common_data['company_data']['company_id'];

                $transaction_tot += $grv_addon_arr[$x]['total_amount'];
                $company_loc_tot += ($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyLocalExchangeRate']);
                $company_rpt_tot += ($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['companyReportingExchangeRate']);
                $supplier_cr_tot += ($grv_addon_arr[$x]['total_amount'] / $grv_addon_arr[$x]['supplierCurrencyExchangeRate']);
            }
            $x++;
            $match_supplierinvoice_arr[$x]['grvAutoID'] = $system_code;
            $match_supplierinvoice_arr[$x]['supplierID'] = $master['supplierID'];
            $match_supplierinvoice_arr[$x]['bookingAmount'] = $grvdetail_tot;
            $match_supplierinvoice_arr[$x]['bookingCurrencyID'] = $master['transactionCurrencyID'];
            $match_supplierinvoice_arr[$x]['bookingCurrency'] = $master['transactionCurrency'];
            $match_supplierinvoice_arr[$x]['bookingCurrencyExchangeRate'] = $master['transactionExchangeRate'];
            $match_supplierinvoice_arr[$x]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
            $match_supplierinvoice_arr[$x]['companyLocalCurrency'] = $master['companyLocalCurrency'];
            $match_supplierinvoice_arr[$x]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $match_supplierinvoice_arr[$x]['companyLocalAmount'] = round(($match_supplierinvoice_arr[$x]['bookingAmount'] / $match_supplierinvoice_arr[$x]['companyLocalExchangeRate']), 3);
            $match_supplierinvoice_arr[$x]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
            $match_supplierinvoice_arr[$x]['companyReportingCurrency'] = $master['companyReportingCurrency'];
            $match_supplierinvoice_arr[$x]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $match_supplierinvoice_arr[$x]['companyReportingAmount'] = round(($match_supplierinvoice_arr[$x]['bookingAmount'] / $match_supplierinvoice_arr[$x]['companyReportingExchangeRate']), 3);
            $match_supplierinvoice_arr[$x]['supplierCurrencyID'] = $master['supplierCurrencyID'];
            $match_supplierinvoice_arr[$x]['supplierCurrency'] = $master['supplierCurrency'];
            $match_supplierinvoice_arr[$x]['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
            $match_supplierinvoice_arr[$x]['supplierCurrencyAmount'] = round(($match_supplierinvoice_arr[$x]['bookingAmount'] / $match_supplierinvoice_arr[$x]['supplierCurrencyExchangeRate']), $match_supplierinvoice_arr[$x]['supplierCurrencyDecimalPlaces']);
            $match_supplierinvoice_arr[$x]['isAddon'] = 0;
            $match_supplierinvoice_arr[$x]['segmentID'] = $master['segmentID'];
            $match_supplierinvoice_arr[$x]['companyID'] = $this->common_data['company_data']['company_id'];

            if (!empty($match_supplierinvoice_arr)) {
                $this->db->insert_batch('srp_erp_match_supplierinvoice', $match_supplierinvoice_arr);
            }

            $data['companyLocalAmount'] = round($company_loc_tot, $master['companyLocalCurrencyDecimalPlaces']);
            $data['companyReportingAmount'] = round($company_rpt_tot, $master['companyReportingCurrencyDecimalPlaces']);
            $data['supplierCurrencyAmount'] = round($supplier_cr_tot, $master['supplierCurrencyDecimalPlaces']);
            $data['transactionAmount'] = round($transaction_tot, $master['transactionCurrencyDecimalPlaces']);

            $this->db->where('grvAutoID', $system_code);
            $this->db->update('srp_erp_grvmaster', $data);

            $this->db->query("UPDATE srp_erp_purchaseordermaster prd
JOIN (
SELECT
    purchaseOrderID AS pid,
    (
        CASE
        WHEN balance = 0 THEN
            '2'
        WHEN balance = requestedtqy THEN
            '0'
        ELSE
            '1'
        END
    ) AS sts
FROM
    (
        SELECT
t2.purchaseOrderID,
sum(requestedtqy) as requestedtqy ,
sum(balance) AS balance
FROM
(
SELECT
        po.purchaseOrderDetailsID,
        purchaseOrderID,
        po.itemAutoID,
        ifnull((po.requestedQty),0) AS requestedtqy,
        ifnull(gd.receivedQty,0) AS receivedqty,
    IF (
        (
            (po.requestedQty) - ifnull(gd.receivedQty,0)
        ) < 0,
        0,
        (
            (po.requestedQty) - ifnull(gd.receivedQty,0)
        )
    ) AS balance
    FROM
        srp_erp_purchaseorderdetails po
    LEFT JOIN (
        SELECT
            purchaseOrderMastertID,
            ifnull(sum(receivedQty),0) AS receivedQty,
            itemAutoID,
            purchaseOrderDetailsID
        FROM
            srp_erp_grvdetails
    left join srp_erp_grvmaster gm on srp_erp_grvdetails.grvAutoID=gm.grvAutoID
            where gm.grvType='PO Base' and gm.approvedYN=1
        GROUP BY
          srp_erp_grvdetails.purchaseOrderDetailsID
    ) gd ON po.purchaseOrderDetailsID=gd.purchaseOrderDetailsID

) t2 group by t2.purchaseOrderID
    ) z
) tt ON prd.purchaseOrderID = tt.pid
SET prd.isReceived = tt.sts
where  prd.companyID = {$company_id} AND prd.purchaseOrderID=tt.pid");

            /** update sub item master : shafry */
            if ($isFinalLevel) {
                $masterID = $this->input->post('grvAutoID');
                $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                if (!empty($result)) {
                    $i = 0;
                    foreach ($result as $item) {
                        unset($result[$i]['subItemAutoID']);
                        $i++;
                    }

                    $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $masterID));

                }
            }
            $itemAutoIDarry = array();
            foreach ($grvdetails as $value) {
                array_push($itemAutoIDarry, $value['itemAutoID']);
            }
            $companyID = current_companyID();
            $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID= '" . $master ['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
            $exceededMatchID = 0;
            if (!empty($exceededitems_master)) {
                $this->load->library('sequence');
                $exceededmatch['documentID'] = "EIM";
                $exceededmatch['documentDate'] = $master ['grvDate'];
                $exceededmatch['orginDocumentID'] = $master ['documentID'];
                $exceededmatch['orginDocumentMasterID'] = $master ['grvAutoID'];
                $exceededmatch['orginDocumentSystemCode'] = $master ['grvPrimaryCode'];
                $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                $exceededmatch['companyID'] = current_companyID();
                $exceededmatch['transactionCurrencyID'] = $master ['transactionCurrencyID'];
                $exceededmatch['transactionCurrency'] = $master ['transactionCurrency'];
                $exceededmatch['transactionExchangeRate'] = $master ['transactionExchangeRate'];
                $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['transactionCurrencyDecimalPlaces'];
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

            foreach ($grvdetails as $itemid) {
                $receivedQty = $itemid['receivedQty'];
                $receivedQtyConverted = $itemid['receivedQty'] / $itemid['conversionRateUOM'];
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
                                $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                $this->db->update('srp_erp_itemexceeded', $exeed);

                                $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                $exceededmatchdetail['warehouseAutoID'] = $master['wareHouseAutoID'];
                                $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                $exceededmatchdetail['itemCost'] = $itemCost['companyLocalWacAmount'];
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
                                $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                $exceededmatchdetails['itemCost'] = $itemCost['companyLocalWacAmount'];
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
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function save_addonmaster()
    {
        $val = $this->input->post('addonmasteredit');
        if (empty($val)) {
            $this->db->set('companyID', $this->common_data['company_data']['company_id']);
            $this->db->set('description', $this->input->post('description'));
            $this->db->set('itemAutoID', $this->input->post('itemAutoID'));
            $result = $this->db->insert('srp_erp_addon_category');
            if ($result) {
                $this->session->set_flashdata('s', 'Add On Master Added Successfully');
                return true;
            }
        } else {
            $data['description'] = ((($this->input->post('description') != "")) ? $this->input->post('description') : NULL);
            $data['itemAutoID'] = ((($this->input->post('itemAutoID') != "")) ? $this->input->post('itemAutoID') : NULL);
            $this->db->where('category_id', $this->input->post('addonmasteredit'));
            $result = $this->db->update('srp_erp_addon_category', $data);
            if ($result) {
                $this->session->set_flashdata('s', 'Records Updated Successfully');
                return true;
            }
        }
    }

    function get_addonmaster()
    {
        $this->db->select('srp_erp_addon_category.description,srp_erp_addon_category.category_id,srp_erp_itemmaster.itemDescription,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.seconeryItemCode,srp_erp_addon_category.itemAutoID');
        $this->db->where('category_id', $this->input->post('id'));
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_addon_category.itemAutoID','LEFT');
        return $this->db->get('srp_erp_addon_category')->row_array();
    }

    function delete_addonmaster()
    {
        $this->db->where('category_id', $this->input->post('id'));
        $result = $this->db->delete('srp_erp_addon_category');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }

    function get_addon_details()
    {
        $this->db->select('*');
        $this->db->where('id', $this->input->post('id'));
        return $this->db->get('srp_erp_grv_addon')->row_array();
    }

    function get_addon_details_projectBase()
    {
        $this->db->select('*');
        $this->db->where('id', $this->input->post('id'));
        $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grv_addon.grvAutoID', 'LEFT');
        return $this->db->get('srp_erp_grv_addon')->row_array();
    }

    function delete_addondetails()
    {
        $companyID = current_companyID();
        $addonID = trim($this->input->post('id') ?? '');
        $grvAddon = $this->db->query("SELECT grvAutoID, poAutoId FROM `srp_erp_grv_addon` where companyID = $companyID ANd id = $addonID")->row_array();
        
        if (false === empty($grvAddon))
        {
            if (null !== $grvAddon['poAutoId'])
            {
                $purchaseOrder = $this->getPurchaseOrderById((int)$grvAddon['poAutoId']);
                if (false !== $purchaseOrder && 1 == $purchaseOrder['logisticConfirmedYN'])
                {
                    $this->session->set_flashdata('w', 'Document is already confirmed');
                    return true;
                }
            }

            $isTaxGroupPolicyEnable = existTaxPolicyDocumentWise('srp_erp_grvmaster',$grvAddon['grvAutoID'],'GRV','grvAutoID');
            if ($isTaxGroupPolicyEnable == 1)
            {
                $this->db->delete('srp_erp_taxledger', array('documentID' => 'GRV-ADD','documentMasterAutoID' => $grvAddon['grvAutoID'],'documentDetailAutoID'=>$addonID));
            }
        }

        $this->db->where('id', $addonID);
        $result = $this->db->delete('srp_erp_grv_addon');

        if ($result) {
            $this->session->set_flashdata('s', 'Record Deleted Successfully');
            return true;
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

    function load_itemMasterSub_tmp($drv_detailID, $documentID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(srp_erp_itemmaster_subtemp.expiryDate,\'' . $convertFormat . '\') AS expiryDate');
        $this->db->where('receivedDocumentDetailID', $drv_detailID);
        $this->db->where('receivedDocumentID', $documentID);
        $r = $this->db->get('srp_erp_itemmaster_subtemp')->result_array();
        return $r;
    }

    function update_batch_srp_erp_itemmaster_subtemp($data)
    {
        $this->db->update_batch('srp_erp_itemmaster_subtemp', $data, 'subItemAutoID');
    }

    function load_itemMasterSub_approval($grvAutoID, $receivedDocumentID)
    {


        switch ($receivedDocumentID) {

            case "GRV":
                $grv = $this->db->query("SELECT  approvedYN FROM srp_erp_grvmaster WHERE grvAutoID = '" . $grvAutoID . "' ")->row_array();
                if (!empty($grv) && $grv['approvedYN'] == 1) {
                    //$tbl = 'srp_erp_itemmaster_sub';
                    $sql = "SELECT
	im.isSubitemExist,
	grvMaster.grvAutoID AS grvMasterID,
	grvDetail.grvDetailsID AS grvDetailID,
	grvDetail.itemSystemCode,
	grvDetail.itemDescription,
	itemMaster.subItemAutoID,
	itemMaster.description,
	itemMaster.subItemSerialNo,
	itemMaster.subItemCode,
	itemMaster.productReferenceNo,
	itemMaster.uom
FROM
	srp_erp_itemmaster_sub itemMaster
INNER JOIN srp_erp_grvmaster grvMaster ON itemMaster.receivedDocumentAutoID = grvMaster.grvAutoID
INNER JOIN srp_erp_grvdetails grvDetail ON itemMaster.receivedDocumentAutoID = grvDetail.grvAutoID
INNER JOIN srp_erp_itemmaster im ON im.itemAutoID = grvDetail.itemAutoID
WHERE
	itemMaster.receivedDocumentAutoID = '" . $grvAutoID . "' AND itemMaster.receivedDocumentID='GRV'";
                } else {
                    //$tbl = 'srp_erp_itemmaster_subtemp';
                    $sql = "SELECT
	im.isSubitemExist,
	grvMaster.grvAutoID AS grvMasterID,
	grvDetail.grvDetailsID AS grvDetailID,
	grvDetail.itemSystemCode,
	grvDetail.itemDescription,
	itemMaster.subItemAutoID,
	itemMaster.description,
	itemMaster.subItemSerialNo,
	itemMaster.subItemCode,
	itemMaster.productReferenceNo,
	itemMaster.uom
FROM
	srp_erp_itemmaster_subtemp itemMaster
INNER JOIN srp_erp_grvmaster grvMaster ON itemMaster.receivedDocumentAutoID = grvMaster.grvAutoID
INNER JOIN srp_erp_grvdetails grvDetail ON itemMaster.receivedDocumentAutoID = grvDetail.grvAutoID
INNER JOIN srp_erp_itemmaster im ON im.itemAutoID = grvDetail.itemAutoID
WHERE
	itemMaster.receivedDocumentAutoID = '" . $grvAutoID . "' AND itemMaster.receivedDocumentID='GRV'";
                }
                break;
            case "PV":
                $grv = $this->db->query("SELECT  approvedYN FROM srp_erp_paymentvouchermaster WHERE payVoucherAutoId = '" . $grvAutoID . "' ")->row_array();
                if (!empty($grv) && $grv['approvedYN'] == 1) {
                    //$tbl = 'srp_erp_itemmaster_sub';
                    $sql = "SELECT
	im.isSubitemExist,
	grvMaster.grvAutoID AS grvMasterID,
	grvDetail.grvDetailsID AS grvDetailID,
	grvDetail.itemSystemCode,
	grvDetail.itemDescription,
	itemMaster.subItemAutoID,
	itemMaster.description,
	itemMaster.subItemSerialNo,
	itemMaster.subItemCode,
	itemMaster.productReferenceNo,
	itemMaster.uom
FROM
	srp_erp_itemmaster_sub itemMaster
INNER JOIN srp_erp_grvmaster grvMaster ON itemMaster.receivedDocumentAutoID = grvMaster.grvAutoID
INNER JOIN srp_erp_grvdetails grvDetail ON itemMaster.receivedDocumentAutoID = grvDetail.grvAutoID
INNER JOIN srp_erp_itemmaster im ON im.itemAutoID = grvDetail.itemAutoID
WHERE
	itemMaster.receivedDocumentAutoID = '" . $grvAutoID . "' AND itemMaster.receivedDocumentID='PV'";
                } else {
                    //$tbl = 'srp_erp_itemmaster_subtemp';
                    $sql = "SELECT
	im.isSubitemExist,
	grvMaster.grvAutoID AS grvMasterID,
	grvDetail.grvDetailsID AS grvDetailID,
	grvDetail.itemSystemCode,
	grvDetail.itemDescription,
	itemMaster.subItemAutoID,
	itemMaster.description,
	itemMaster.subItemSerialNo,
	itemMaster.subItemCode,
	itemMaster.productReferenceNo,
	itemMaster.uom
FROM
	srp_erp_itemmaster_subtemp itemMaster
INNER JOIN srp_erp_grvmaster grvMaster ON itemMaster.receivedDocumentAutoID = grvMaster.grvAutoID
INNER JOIN srp_erp_grvdetails grvDetail ON itemMaster.receivedDocumentAutoID = grvDetail.grvAutoID
INNER JOIN srp_erp_itemmaster im ON im.itemAutoID = grvDetail.itemAutoID
WHERE
	itemMaster.receivedDocumentAutoID = '" . $grvAutoID . "' AND itemMaster.receivedDocumentID='PV'";
                }

                break;

            default:
                echo 'Please configure Line No.' . __LINE__ . ' in ' . __FILE__;
                exit;
        }


        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    function re_open_grv()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        $this->db->update('srp_erp_grvmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'GRV');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();
    }

    function saveSubItemMasterTmpDynamic()
    {
        $subItemAutoID = $this->input->post('subItemAutoID');
        $attrinutes = fetch_company_assigned_attributes();
        $variable = '';
        $primaryQty = 0;
        foreach ($attrinutes as $valu) {
            $variable[$valu['columnName']] = $this->input->post($valu['columnName']);
        }

        foreach ($subItemAutoID as $key => $val) {
            foreach ($attrinutes as $valu) {
                if ($valu['attributeType'] == 2) {
                    $policyDate = date_format_policy();
                    $date = $variable[$valu['columnName']][$key];
                    if (!empty($date)) {
                        $finalDate = input_format_date($date, $policyDate);
                    } else {
                        $finalDate = null;
                    }
                    $data[$valu['columnName']] = $finalDate;
                } else {
                    $data[$valu['columnName']] = $variable[$valu['columnName']][$key];
                }

                if($valu['columnName'] == 'QtyPrimaryUOM') {
                    $primaryQty += $variable[$valu['columnName']][$key];
                }

            }
            $this->db->where('subItemAutoID', $val);
            $result = $this->db->update('srp_erp_itemmaster_subtemp', $data);
            //print_r($data);
        }

        if ($result) {
            return array('s', 'Successfully Updated', $primaryQty);
        }

    }

    function delete_grv_buyback()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_grvdetails');
        $this->db->where('grvAutoID', trim($this->input->post('grv_auto_id') ?? ''));
        $datas = $this->db->get()->row_array();
        if (!empty($datas)) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('grvAutoID', trim($this->input->post('grv_auto_id') ?? ''));
            $this->db->update('srp_erp_grvmaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }


    }

    function re_open_grv_buyback()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        $this->db->update('srp_erp_grvmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function save_grv_header_buyback()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $gvDte = $this->input->post('grvDate');
        $grvDate = input_format_date($gvDte, $date_format_policy);
        $deveedDte = $this->input->post('deliveredDate');
        $deliveredDate = input_format_date($deveedDte, $date_format_policy);

        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $delivery_location = explode('|', trim($this->input->post('delivery_location') ?? ''));
        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplierID') ?? ''));
        //$period                 = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
        $FYBegin = input_format_date($year[0], $date_format_policy);
        $FYEnd = input_format_date($year[1], $date_format_policy);
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $data['grvType'] = trim($this->input->post('grvType') ?? '');
        $data['documentID'] = 'GRV';
        $data['contactPersonName'] = trim($this->input->post('contactPersonName') ?? '');
        $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber') ?? '');
        $data['supplierID'] = trim($this->input->post('supplierID') ?? '');
        $data['grvNarration'] = trim_desc($this->input->post('narration'));
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        /*$data['FYPeriodDateFrom']                   = trim($period[0] ?? '');
        $data['FYPeriodDateTo']                     = trim($period[1] ?? '');*/
        $data['grvDate'] = $grvDate;
        $data['deliveredDate'] = $deliveredDate;
        $data['grvDocRefNo'] = trim($this->input->post('referenceno') ?? '');
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
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['wareHouseAutoID'] = trim($this->input->post('location') ?? '');
        $data['wareHouseCode'] = trim($delivery_location[0] ?? '');
        $data['wareHouseLocation'] = trim($delivery_location[1] ?? '');
        $data['wareHouseDescription'] = trim($delivery_location[2] ?? '');
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

        if (trim($this->input->post('grvAutoID') ?? '')) {
            $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
            $this->db->update('srp_erp_grvmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GRV for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'GRV for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('grvAutoID'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['grvPrimaryCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_grvmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GRV for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'GRV for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_detail_buyback()
    {
        $this->db->select('srp_erp_grvdetails.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_grvdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_grvdetails.itemAutoID', 'left');
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        return $this->db->get()->result_array();
    }

    function save_grv_st_bulk_detail_buyback()
    {
        $projectExist = project_is_exist();
        $grvDetailsID = $this->input->post('grvDetailsID');
        $grvAutoID = $this->input->post('grvAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $projectID = $this->input->post('projectID');
        $discount = $this->input->post('discount');
        $comment = $this->input->post('comment');

        $noOfItems = $this->input->post('noOfItems');
        $grossQty = $this->input->post('grossQty');
        $noOfUnits = $this->input->post('noOfUnits');
        $deduction = $this->input->post('deduction');
        $deductionvalue = $this->input->post('deductionvalue');

        $this->db->trans_start();
        //$ACA_ID = $this->common_data['controlaccounts']['ACA'];
        $this->db->select('GLAutoID');
        $this->db->where('controlAccountType', 'ACA');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);

        $this->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,transactionCurrencyID');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        $master = $this->db->get()->row_array();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            if (!$grvAutoID) {
                $this->db->select('grvAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_grvdetails');
                $this->db->where('grvAutoID', $grvAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();

                $this->db->select('mainCategory');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemAutoID', $itemAutoID);
                $serviceitm= $this->db->get()->row_array();
                if($serviceitm['mainCategory']=="Inventory") {
                    if (!empty($order_detail)) {
                        return array('e', 'GRV Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                    }
                }
            }
            $item_data = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);
            if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $item_data['itemAutoID']);
                $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                if (empty($warehouseitems)) {
                    $data_arr = array(
                        'wareHouseAutoID' => $master['wareHouseAutoID'],
                        'wareHouseLocation' => $master['wareHouseLocation'],
                        'wareHouseDescription' => $master['wareHouseDescription'],
                        'itemAutoID' => $item_data['itemAutoID'],
                        'barCodeNo' => $item_data['barcode'],
                        'salesPrice' => $item_data['companyLocalSellingPrice'],
                        'ActiveYN' => $item_data['isActive'],
                        'itemSystemCode' => $item_data['itemSystemCode'],
                        'itemDescription' => $item_data['itemDescription'],
                        'unitOfMeasureID' => $item_data['defaultUnitOfMeasureID'],
                        'unitOfMeasure' => $item_data['defaultUnitOfMeasure'],
                        'currentStock' => 0,
                        'companyID' => $this->common_data['company_data']['company_id'],
                        'companyCode' => $this->common_data['company_data']['company_code'],
                    );

                    $this->db->insert('srp_erp_warehouseitems', $data_arr);
                }
            }

            $data['grvAutoID'] = $grvAutoID;
            $data['itemAutoID'] = $item_data['itemAutoID'];
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            if ($data['itemCategory'] == 'Inventory') {
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
            } elseif ($data['itemCategory'] == 'Fixed Assets') {
                $data['PLGLAutoID'] = NULL;
                $data['PLSystemGLCode'] = NULL;
                $data['PLGLCode'] = NULL;
                $data['PLDescription'] = NULL;
                $data['PLType'] = NULL;

                $data['BLGLAutoID'] = $ACA_ID['GLAutoID'];
                $data['BLSystemGLCode'] = $ACA['systemAccountCode'];
                $data['BLGLCode'] = $ACA['GLSecondaryCode'];
                $data['BLDescription'] = $ACA['GLDescription'];
                $data['BLType'] = $ACA['subCategory'];
            } else {
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
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];

            $data['noOfItems'] = $noOfItems[$key] ?? null;
            $data['grossQty'] = $grossQty[$key] ?? null;
            $data['noOfUnits'] = $noOfUnits[$key] ?? null;
            $data['deduction'] = $deductionvalue[$key] ?? null;
            $data['bucketWeightID'] = $deduction[$key] ?? null;


            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['receivedQty'] = trim($quantityRequested[$key]);
            $data['receivedAmount'] = trim($estimatedAmount[$key]);
            $data['receivedTotalAmount'] = ($data['receivedQty'] * $data['receivedAmount']);
            $data['fullTotalAmount'] = ($data['receivedQty'] * $data['receivedAmount']);
            $data['addonAmount'] = 0;
            $data['addonTotalAmount'] = 0;
            $data['comment'] = $comment[$key];
            $data['remarks'] = null;
            $data['requestedQty'] = 0.00;
            $data['requestedAmount'] = 0.00;
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
            $this->db->insert('srp_erp_grvdetails', $data);
            $last_id = $this->db->insert_id();

            if ($item_data['isSubitemExist'] == 1) {

                $qty = 0;
                if (!empty($itemAutoIDs)) {
                    foreach ($itemAutoIDs as $key => $itemAutoIDTmp) {
                        if ($itemAutoIDTmp == $itemAutoID) {
                            $qty = $quantityRequested[$key];
                        }
                    }
                }

                $subData['uom'] = $data['unitOfMeasure'];
                $subData['uomID'] = $data['unitOfMeasureID'];
                $subData['grv_detailID'] = $last_id;
                $subData['warehouseAutoID'] = $master['wareHouseAutoID'];
                $this->add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $grvAutoID, $last_id, 'GRV', $item_data['itemSystemCode'], $subData);


            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'GRV Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'GRV Details :  Saved Successfully.');
        }
    }

    function save_grv_detail_buyback()
    {
        $grvDetailsID = $this->input->post('grvDetailsID');

        if (!trim($grvDetailsID)) {
            $this->db->select('srp_erp_grvdetails.*');
            $this->db->from('srp_erp_grvdetails');
            $this->db->where('srp_erp_grvdetails.grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
            $this->db->where('srp_erp_grvdetails.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $order_detail = $this->db->get()->row_array();

            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $serviceitm= $this->db->get()->row_array();
            if($serviceitm['mainCategory']=="Inventory") {
                if (!empty($order_detail)) {
                    $this->session->set_flashdata('w', 'GRV Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                    return array('status' => false);
                }
            }
        }

        $this->db->trans_start();
        //$ACA_ID = $this->common_data['controlaccounts']['ACA'];
        $this->db->select('GLAutoID');
        $this->db->where('controlAccountType', 'ACA');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
        $projectID = trim($this->input->post('projectID') ?? '');
        $projectExist = project_is_exist();
        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));

        $this->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,transactionCurrencyID');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
        $master = $this->db->get()->row_array();
        $data['grvAutoID'] = trim($this->input->post('grvAutoID') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['itemFinanceCategory'] = $item_data['subcategoryID'];
        $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
        $data['financeCategory'] = $item_data['financeCategory'];
        $data['itemCategory'] = $item_data['mainCategory'];
        if ($data['itemCategory'] == 'Inventory') {
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
        } elseif ($data['itemCategory'] == 'Fixed Assets') {
            $data['PLGLAutoID'] = NULL;
            $data['PLSystemGLCode'] = NULL;
            $data['PLGLCode'] = NULL;
            $data['PLDescription'] = NULL;
            $data['PLType'] = NULL;

            $data['BLGLAutoID'] = $ACA_ID['GLAutoID'];
            $data['BLSystemGLCode'] = $ACA['systemAccountCode'];
            $data['BLGLCode'] = $ACA['GLSecondaryCode'];
            $data['BLDescription'] = $ACA['GLDescription'];
            $data['BLType'] = $ACA['subCategory'];
        } else {
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
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['receivedQty'] = trim($this->input->post('quantityRequested') ?? '');
        $data['noOfItems'] = trim($this->input->post('noOfItems') ?? '');
        $data['grossQty'] = trim($this->input->post('grossQty') ?? '');
        $data['noOfUnits'] = trim($this->input->post('noOfUnits') ?? '');
        $data['deduction'] = trim($this->input->post('deductionvalue') ?? '');
        $data['bucketWeightID'] = trim($this->input->post('deductionedit') ?? '');
        $data['receivedAmount'] = trim($this->input->post('estimatedAmount') ?? '');
        $data['receivedTotalAmount'] = ($this->input->post('quantityRequested') * $this->input->post('estimatedAmount'));
        $data['fullTotalAmount'] = ($this->input->post('quantityRequested') * $this->input->post('estimatedAmount'));
        $data['addonAmount'] = 0;
        $data['addonTotalAmount'] = 0;
        $data['comment'] = trim($this->input->post('comment') ?? '');
        $data['remarks'] = trim($this->input->post('remarks') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if ($data['itemCategory'] == 'Inventory' or $data['itemCategory'] == 'Non Inventory') {
            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $master['wareHouseAutoID'],
                    'wareHouseLocation' => $master['wareHouseLocation'],
                    'wareHouseDescription' => $master['wareHouseDescription'],
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
        }

        if (trim($grvDetailsID)) {
            $this->db->where('grvDetailsID', trim($grvDetailsID));
            $this->db->update('srp_erp_grvdetails', $data);

            /** update sub item master */

            $this->db->select('srp_erp_grvdetails.*,srp_erp_grvmaster.wareHouseAutoID');
            $this->db->from('srp_erp_grvdetails');
            $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID', 'left');
            $this->db->where('srp_erp_grvdetails.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $detail = $this->db->get()->row_array();


            $subData['uom'] = $data['unitOfMeasure'];
            $subData['uomID'] = $data['unitOfMeasureID'];
            $subData['grvDetailsID'] = $grvDetailsID;
            $subData['wareHouseAutoID'] = $detail['wareHouseAutoID'];

            if ($item_data['isSubitemExist'] == 1) {
                $this->edit_sub_itemMaster_tmpTbl($this->input->post('quantityRequested'), $item_data['itemAutoID'], $data['grvAutoID'], $grvDetailsID, 'GRV', $data['itemSystemCode'], $subData);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GRV Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'GRV Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('grvDetailsID'));
            }
        } else {
            $data['requestedQty'] = 0.00;
            $data['requestedAmount'] = 0.00;
            $data['purchaseOrderMastertID'] = trim($this->input->post('purchaseOrderID') ?? '');
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_grvdetails', $data);
            $last_id = $this->db->insert_id();


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GRV Detail : ' . $data['itemSystemCode'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'GRV Detail : ' . $data['itemSystemCode'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_line_tax_and_vat(){ 
        $companyID = current_companyID();
        $GrvAutoID = trim($this->input->post('grvAutoID') ?? '');
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_grvmaster',$GrvAutoID,'GRV','grvAutoID');
        if($isGroupByTax == 1){
            $data['tax_drop'] = fetch_line_wise_itemTaxFormulaID($itemAutoID,'taxMasterAutoID','taxDescription', 2);
            $selected_itemTax =   array_column($data['tax_drop'], 'assignedItemTaxFormula');
            $data['selected_itemTax'] =   $selected_itemTax[0];
        }
        return $data;
    }


    function load_line_tax_amount_grv(){
        $grvAutoID=$this->input->post('grvAutoID');
        $applicableAmnt=$this->input->post('applicableAmnt');
        $taxCalculationformulaID=$this->input->post('taxtype');
        $grvDetailsID= ($this->input->post('grvDetailsID')!=''?$this->input->post('grvDetailsID'):null);
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $disount = ((trim($this->input->post('discount') ?? '')!='')?trim($this->input->post('discount') ?? ''):0);
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_grvmaster',$grvAutoID,'GRV','grvAutoID');
        $bookingCurrencyID = trim($this->input->post('bookingCurrencyID') ?? '');
        $dPlaces= '';
        if($bookingCurrencyID){
            $dPlaces = fetch_currency_desimal_by_id($bookingCurrencyID);
        }

        if($isGroupByTax == 1){
            $amnt=0;
            $return = fetch_line_wise_itemTaxcalculation($taxCalculationformulaID,$applicableAmnt,$disount,'GRV',$grvAutoID,$grvDetailsID);
            if($return['error'] == 1) {
                $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
                $amnt = 0;
            } else {
                $amnt = $return['amount'];
            }
        }
        if($dPlaces){
            return number_format($amnt,$dPlaces);
        }else{
            return $amnt;
        }
    }

    function fetch_inspection_approval_level($documentID){
        $companyID = current_companyID();
        $userID =$this->common_data['current_userID'];
        $this->db->select('*');
        $this->db->from('srp_erp_approvalusers');
        $this->db->where('documentID', $documentID);
        $this->db->where('companyID', $companyID);
        $this->db->where('criteriaID', 2);
        $detail = $this->db->get()->row_array();

        if($detail){
            $this->db->select('*');
            $this->db->from('srp_erp_approvalusers');
            $this->db->where('documentID', $documentID);
            $this->db->where('levelNo', $detail['levelNo']);
            $this->db->where('companyID', $companyID);
            $this->db->where('employeeID', $userID);
            $this->db->where('criteriaID', 2);
            $inspection_access_level = $this->db->get()->row_array();

            if($inspection_access_level){
                return true;
            }else{
                return false;
            }

        }else{
            return false;
        }
    }

    /**
     * Get addon by purchase order id
     *
     * @param integer $poId
     * @return array
     */
    public function geAddonByPurchaseOrderId($poId)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_grv_addon');
        $this->db->where('poAutoId', $poId);

        return $this->db->get()->result_array();
    }

    /**
     * Get purchase order by Id
     *
     * @param integer $poId
     * @return array
     */
    public function getPurchaseOrderById($poId)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_purchaseordermaster');
        $this->db->where('purchaseOrderID', $poId);
        $this->db->where('isDeleted', 0);

        return $this->db->get()->row_array();
    }

    /**
     * Get addon details by grv detail id
     *
     * @param integer $grvDetailId
     * @return void
     */
    public function deleteAddonByGrvDetailId($grvDetailId)
    {
        $this->db->delete('srp_erp_grv_addon', ['grvDetailsID' => $grvDetailId]);
    }

    /**
     * Save po addon into grv addon
     *
     * @param array $input
     * @return array
     */
    function savePoAddon($input)
    {
        $this->db->trans_start();

        $taxtype = null;
        $projectExist = project_is_exist();
        $grvAutoID = $input['grvAutoID'];
        
        $this->db->select('transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces,supplierID');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where('grvAutoID', $grvAutoID);
        $master = $this->db->get()->row_array();

        $supplier_arr = $this->fetch_supplier_data($master['supplierID']);
        $data['grvAutoID'] = $grvAutoID;
        $data['grvDetailsID'] = $input['grvDetailId'];
        $data['supplierID'] = $master['supplierID'];
        if ($projectExist == 1) {
            
            $data['projectID'] = $input['projectID'];
            $data['projectExchangeRate'] = $input['projectExchangeRate'];
        }
        $data['supplierSystemCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data['supplierliabilityType'] = $supplier_arr['liabilityType'];
        $data['isChargeToExpense'] = 0;  
        $data['bookingCurrencyID'] = $master['transactionCurrencyID'];
        $data['bookingCurrency'] = $master['transactionCurrency'];
        $data['bookingCurrencyExchangeRate'] = 1;
        $data['bookingCurrencyAmount'] = $input['addonAmount'];
        $data['bookingCurrencyDecimalPlaces'] = fetch_currency_desimal($data['bookingCurrency']);
        $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
        $data['transactionCurrency'] = $master['transactionCurrency'];
        $transaction_currency = currency_conversionID($data['bookingCurrencyID'], $data['transactionCurrencyID']);
        $data['transactionExchangeRate'] = $transaction_currency['conversion'];
        $data['transactionCurrencyDecimalPlaces'] = $transaction_currency['DecimalPlaces'];
        $data['total_amount'] = round(($data['bookingCurrencyAmount'] / $data['transactionExchangeRate']), $data['transactionCurrencyDecimalPlaces']);
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data['bookingCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['bookingCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $supplier_currency = currency_conversionID($data['bookingCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplier_currency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplier_currency['DecimalPlaces'];
        $data['supplierCurrencyAmount'] = round(($data['bookingCurrencyAmount'] / $data['supplierCurrencyExchangeRate']), $data['supplierCurrencyDecimalPlaces']);
        $data['impactFor'] = 0;
        $data['paidBy'] = $input['paidBy'];
        $data['addonCatagory'] = $input['addonCatagory'];
        $data['narrations'] = $input['narrations'];
        $data['referenceNo'] = $input['referenceNo'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['id'] = $input['id'];

        if ($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('srp_erp_grv_addon', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) { 
                $this->db->trans_rollback();
            } else {
                if($taxtype){
                    tax_calculation_vat(null,null,$taxtype,'id',$data['grvAutoID'] ,$data['bookingCurrencyAmount'],'GRV-ADD',trim($this->input->post('id') ?? ''),0,0);
                }
                $this->db->trans_commit();
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_grv_addon', $data);
            $last_id = $this->db->insert_id();

            if($taxtype){
                tax_calculation_vat(null,null,$taxtype,'id',$data['grvAutoID'] ,$data['bookingCurrencyAmount'],'GRV-ADD',$last_id,0,0);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /**
     * Calculate the po addon amount
     *
     * @param float $receivedQty
     * @param float $unitAmount
     * @param float $poTotalAmount
     * @param float $totalDetailAmount
     * @param integer $purchaseOrderDetailsID
     * @param integer $impactFor
     * @param float $amount
     * @return float
     */
    function caculatePoAddonAmount(
        $receivedQty,
        $unitAmount,
        $poTotalAmount,
        $totalDetailAmount,
        $purchaseOrderDetailsID,
        $impactFor,
        $amount
    )
    {
        $addonAmount = 0;

        if (0 == $impactFor)
        {
            $addonAmount = (($receivedQty * $unitAmount) / $poTotalAmount) * $amount;
        }

        if ($purchaseOrderDetailsID == $impactFor)
        {
            $addonAmount = (($receivedQty * $unitAmount) / $totalDetailAmount) * $amount;
        }

        return $addonAmount;
    }

    function fetch_checklist(){

        $grvAutoID = $this->input->post('grvAutoID');

        $checklists = $this->db->where('documentMasterID',$grvAutoID)->from('srp_erp_document_approval_checklistdeails')->get()->result_array();

        return $checklists;

    }

    function update_checklist_response(){
        $id = trim($this->input->post('id') ?? '');
        $value = trim($this->input->post('value') ?? '');

        $data = array();
        $data['Value'] = $value;

        $this->db->where('id',$id)->update('srp_erp_document_approval_checklistdeails',$data);
        return array('s', 'Updated Successfully');

    }

    /**
     * Get logistic po
     *
     * @param array $master
     * @return array
     */
    function fetchLogisticPo(array $master)
    {
        $convertFormat = convert_date_format_sql();
        $supplierID = $master['supplierID'];
        $currencyID = $master['transactionCurrencyID'];
        $segmentID = $master['segmentID'];
        $date = format_date($master['grvDate']);
        $sql = "SELECT 
    srp_erp_purchaseordermaster.purchaseOrderID,
    srp_erp_purchaseordermaster.purchaseOrderCode,
    DATE_FORMAT(srp_erp_purchaseordermaster.documentDate,
            '$convertFormat') AS documentDate
FROM
    srp_erp_purchase_order_logistic
        INNER JOIN
    srp_erp_purchaseordermaster ON srp_erp_purchase_order_logistic.poMasterID = srp_erp_purchaseordermaster.purchaseOrderID
WHERE NOT EXISTS (SELECT *
                   FROM   srp_erp_grvdetails
                   WHERE  srp_erp_grvdetails.poLogisticID = srp_erp_purchase_order_logistic.poLogisticID)
                   AND 
    `supplierID` = '{$supplierID}'
        AND `documentDate` <= '{$date}'
        AND `segmentID` = '{$segmentID}'
        AND `transactionCurrencyID` = '{$currencyID}'
        AND `confirmedYN` = 1
        AND `closedYN` = 0
        AND `approvedYN` = 1
        AND `purchaseOrderType` = 'LOG'
GROUP BY srp_erp_purchaseordermaster.purchaseOrderCode";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get logistic po detail
     *
     * @param integer $purchaseOrderID
     * @return array
     */
    function fetchLogisticPoDetail($purchaseOrderID)
    {
        $sql = "SELECT 
   srp_erp_purchase_order_logistic.*,srp_erp_grv_addon.addonCatagory
FROM
    srp_erp_purchase_order_logistic
        INNER JOIN
    srp_erp_purchaseordermaster ON srp_erp_purchase_order_logistic.poMasterID = srp_erp_purchaseordermaster.purchaseOrderID
INNER JOIN
    srp_erp_grv_addon ON srp_erp_purchase_order_logistic.addonDetailID = srp_erp_grv_addon.id
WHERE NOT EXISTS (SELECT *
                   FROM   srp_erp_grvdetails
                   WHERE  srp_erp_grvdetails.poLogisticID = srp_erp_purchase_order_logistic.poLogisticID)
        AND `poMasterID` = " . $purchaseOrderID ;
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get logistic po grv addons
     *
     * @param integer $logisticId
     * @return array
     */
    public function getLogisticPoAddonByID($logisticId)
    {
        $this->db->select('
        srp_erp_purchase_order_logistic.*,
        srp_erp_purchaseordermaster.purchaseOrderCode,
        srp_erp_itemmaster.assteGLAutoID,
        srp_erp_itemmaster.assteSystemGLCode,
        srp_erp_itemmaster.assteGLCode,
        srp_erp_itemmaster.assteDescription,
        srp_erp_itemmaster.assteType,
        srp_erp_itemmaster.costGLAutoID,
        srp_erp_itemmaster.costSystemGLCode,
        srp_erp_itemmaster.costGLCode,
        srp_erp_itemmaster.costDescription,
        srp_erp_itemmaster.costType,
        srp_erp_itemmaster.itemAutoID,
        srp_erp_itemmaster.itemSystemCode,
        srp_erp_itemmaster.itemDescription,
        srp_erp_itemmaster.mainCategory,
        srp_erp_itemmaster.financeCategory,
        ');
        $this->db->join('srp_erp_grv_addon', 'srp_erp_grv_addon.id = srp_erp_purchase_order_logistic.addonDetailID', 'inner');
        $this->db->join('srp_erp_addon_category', 'srp_erp_addon_category.description = srp_erp_grv_addon.addonCatagory', 'inner');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_addon_category.itemAutoID', 'inner');
        $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_grv_addon.poAutoID', 'inner');
        $this->db->where('poLogisticID', $logisticId);
        return $this->db->get('srp_erp_purchase_order_logistic')->row_array();
    }

    /**
     * Save logistic po item
     *
     * @param integer $grvID
     * @param array $detailsIDs
     * @return boolean
     */
    public function saveLogisticPoBaseItems($grvID, $detailsIDs)
    {
        foreach ($detailsIDs as $detailId) {
            $detail = $this->getLogisticPoAddonByID($detailId);
            if (!$detail) {
                return false;
            }

            $data = [
                'grvAutoID' => $grvID,
                'purchaseOrderMastertID' => $detail['poMasterID'],
                'purchaseOrderCode' => $detail['purchaseOrderCode'],
                'poLogisticID' => $detailId,
                'logistic_different_amount' => $detail['matchedAmount'],
                'itemAutoID' => $detail['itemAutoID'],
                'itemSystemCode' => $detail['itemSystemCode'],
                'itemDescription' => $detail['itemDescription'],
                'itemCategory' => $detail['mainCategory'],
                'financeCategory' => $detail['financeCategory'],
                'PLGLAutoID' => $detail['costGLAutoID'],
                'PLSystemGLCode' => $detail['costSystemGLCode'],
                'PLGLCode' => $detail['costGLCode'],
                'PLDescription' => $detail['costDescription'],
                'PLType' => $detail['costType'],
                'BLGLAutoID' => $detail['assteGLAutoID'],
                'BLSystemGLCode' => $detail['assteSystemGLCode'],
                'BLGLCode' => $detail['assteGLCode'],
                'BLDescription' => $detail['assteDescription'],
                'BLType' => $detail['assteType'],
                'receivedAmount' => $detail['actualLogisticAmount'],
                'receivedTotalAmount' => $detail['actualLogisticAmount'],
                'fullTotalAmount' => $detail['actualLogisticAmount'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'companyID' => $this->common_data['company_data']['company_id'],
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => $this->common_data['current_date'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdDateTime' => $this->common_data['current_date'],
            ];

            $this->db->insert('srp_erp_grvdetails', $data);
            return true;
        }
    }

    /**
     * Get grv header
     *
     * @param integer $grvAutoID
     * @return array|null
     */
    function getGrvById($grvAutoID)
    {
        $this->db->from('srp_erp_grvmaster');
        $this->db->where('grvAutoID', $grvAutoID);
        return $this->db->get()->row_array();
    }
}