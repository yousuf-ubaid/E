<?php
/*
-- =============================================
-- File Name : Reversing_modal.php
-- Project Name : SME ERP
-- Module Name : Email


-- REVISION HISTORY
-- =============================================*/
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Reversing_modal extends ERP_Model{

    function __contruct()
    {
        parent::__contruct();
    }

    function reversing_approval_document(){
        $auto_id        = trim($this->input->post('auto_id') ?? '');
        $date           = trim('Y-m-d');
        $document_id    = trim($this->input->post('document_id') ?? '');
        $document_code  = trim($this->input->post('document_code') ?? '');
        $comments       = trim($this->input->post('comments') ?? '');
        $company_id     = $this->common_data['company_data']['company_id'];

        if($document_code == 'P') {
            $categoryCreated = $this->db->query("SELECT * FROM srp_erp_boq_details WHERE tendertype = 1 AND headerID = {$document_id}")->row_array();
            if($categoryCreated) {
                $this->session->set_flashdata('w', 'This document can not be reversed as Post-tender details are created.');
                return array('status' => false);
                exit;
            } else {
                $costingCreated = $this->db->query("SELECT * FROM srp_erp_boq_details WHERE tendertype = 1 AND headerID = {$document_id}")->row_array();
                if($costingCreated) {
                    $this->session->set_flashdata('w', 'This document can not be reversed as Post-tender costs are created.');
                    return array('status' => false);
                    exit;
                }
            }
        }

        if($document_code == 'FAD'){
            $isDisposalExist = $this->db->query("SELECT
                                                  COUNT(DepreciationPeriodsID) as isExists	
                                                  FROM
                                                  srp_erp_fa_assetdepreciationperiods
                                                  where
                                                  depMasterAutoID = {$document_id} and
                                                  faID in
                                                  (select faID from srp_erp_fa_asset_disposaldetail where companyID = {$company_id} )")->row('isExists');
            if($isDisposalExist > 0){ 
                $this->session->set_flashdata('w', 'This document can not be reversed as disposal are recorded');
                return array('status' => false);
                exit;
            }else{
                $data = array(
                    'confirmedYN' => 0,
                    'approvedYN' => 0,
                );
                $this->db->where('depMasterAutoID', $document_id);
                $this->db->update('srp_erp_fa_depmaster', $data);
            } 
        }

        if($document_code == 'SC'){
            $isSalesComm_exist = $this->db->query("SELECT
                                                   COUNT( payVoucherDetailAutoID ) AS isExists 
                                                   FROM srp_erp_paymentvoucherdetail 
                                                   WHERE salesCommissionID = {$document_id} AND companyID = {$company_id}")->row('isExists'); 
            if($isSalesComm_exist > 0){
                $this->session->set_flashdata('w', 'This document can not be reversed as payments are already recorded');
                return array('status' => false);
                exit;
            }
                                    
        }

        if($document_code == 'RJV'){
            $check_RJV_exist = $this->db->query("SELECT
                                                   COUNT( JVDetailAutoID ) AS isExists 
                                                   FROM srp_erp_jvdetail 
                                                   WHERE recurringjvMasterAutoId = {$document_id} AND companyID = {$company_id}")->row('isExists'); 

            if($check_RJV_exist > 0){
                $this->session->set_flashdata('w', 'This document can not be reversed as Journal Voucher are already recorded');
                return array('status' => false);
                exit;
            }
        }

        if($document_code == 'ADSP'){
            $data_disposalMstr = [
                'confirmedYN' => 0,
                'approvedYN' => 0
            ];

            $this->db->where('assetdisposalMasterAutoID', $document_id);
            $this->db->update('srp_erp_fa_asset_disposalmaster', $data_disposalMstr);

            $data_assetMstr = [
                'disposed' => 0,
                'disposedDate' => null,
                'assetdisposalMasterAutoID' => null,
                'reasonDisposed' => null
            ];

            $this->db->where(['assetdisposalMasterAutoID' => $document_id,'companyID' => $company_id]);
            $this->db->update('srp_erp_fa_asset_master', $data_assetMstr);
        }
       
        if($document_code=='GRV' || $document_code=='ST' || $document_code=='MRN') {
            if($document_code == 'GRV') {
                $table = 'srp_erp_grvmaster'; $feildName = 'grvAutoID';
            } else if($document_code == 'MRN') {
                $table = 'srp_erp_materialreceiptmaster'; $feildName = 'mrnAutoID';
            } else {
                $table = 'srp_erp_stocktransfermaster'; $feildName = 'stockTransferAutoID';
            }
            $this->db->select('workProcessID, srp_erp_mfq_job.documentCode as documentCode');
            $this->db->from($table);
            $this->db->join('srp_erp_mfq_job', 'srp_erp_mfq_job.workProcessID = '.$table.'.jobID');
            $this->db->where($feildName, $document_id);
            $this->db->where('srp_erp_mfq_job.closedYN', 1);
            $job_closed = $this->db->get()->row_array();
            if (!empty($job_closed)) {
                $this->session->set_flashdata('w', 'Linked Job(' . $job_closed['documentCode'] . ') Already closed!');
                return array('status' => false);
            }
        }

        if($document_code == 'HCINV') {
            $dayclosed = $this->db->query("SELECT isDayClosed FROM srp_erp_customerinvoicemaster_temp WHERE companyID = {$company_id} AND invoiceAutoID = {$document_id}")->row_array();
            if ($dayclosed['isDayClosed'] == 1) {
                $this->session->set_flashdata('w', 'This document can not be reversed as day close is done.');
                return array('status' => false);
                exit;
            }
        }
        if($document_code == 'MR') {
            $materialIssued = $this->db->query("SELECT itemIssueAutoID FROM srp_erp_itemissuedetails WHERE companyID = {$company_id} AND mrAutoID = {$document_id}")->row_array();
            if (!empty($materialIssued)) {
                $this->session->set_flashdata('w', 'This document can not be reversed as it is already issued.');
                return array('status' => false);
                exit;
            }
        }
        if($document_code == 'DO') {
            $deliveryOrder_pulled = $this->db->query("SELECT
                        srp_erp_customerinvoicemaster.invoiceCode, DOMasterID 
                        FROM
                            `srp_erp_customerinvoicedetails` 
                            LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.InvoiceAutoID
                        WHERE
                            srp_erp_customerinvoicedetails.companyID = {$company_id}
                            AND type = 'DO'
                            AND DOMasterID = {$document_id}")->row_array();
            if (!empty($deliveryOrder_pulled)) {
                $this->session->set_flashdata('w', 'This document is pulled in invoice ' . $deliveryOrder_pulled['invoiceCode'] . '.');
                return array('status' => false);
                exit;
            }
        }

        if($document_code=='PV'){
            $companyID=current_companyID();
            $rrvrID = $this->db->query("SELECT payVoucherAutoId FROM `srp_erp_paymentvouchermaster` WHERE `companyID` = '$companyID' AND `payVoucherAutoId` = '$document_id' AND rrvrID !='' ")->row_array();
            $bankTransferID = $this->db->query("SELECT payVoucherAutoId FROM `srp_erp_paymentvouchermaster` WHERE `companyID` = '$companyID' AND `payVoucherAutoId` = '$document_id' AND bankTransferID !='' ")->row_array();

            if(!empty($rrvrID)){
                $this->session->set_flashdata('w', 'This document can not be reversed it has been generated from receipt reversal  ');
                return array('status' => false);
                exit;
            }

         /*    if(!empty($bankTransferID)){
                $this->session->set_flashdata('w', 'This document can not be reversed it has been generated from bank transfer');
                return array('status' => false);
                exit;
            }  */
        }

        if($document_code=='FA'){
            $this->db->select('DepreciationPeriodsID');
            $this->db->from('srp_erp_fa_assetdepreciationperiods');
            $this->db->where('faID', $document_id);
            $this->db->where('companyID', current_companyID());
            $result=$this->db->get()->row_array();

            if(!empty($result)){
                $this->session->set_flashdata('w', 'This asset cannot be reversed. It has been pulled for depreciation.');
                return array('status' => false);
                exit;
            }
        }

        if($document_code=='BBDPN' || $document_code=='BBGRN' || $document_code=='BBDR')
        {
            $this->db->select('documentID, table_name, table_unique_field_name');
            $this->db->from('srp_erp_documentapproved');
            $this->db->where('documentApprovedID', $auto_id);
            $docDetails = $this->db->get()->row_array();

            $this->db->select('farmID, batchMasterID');
            $this->db->from($docDetails['table_name']);
            $this->db->where($docDetails['table_unique_field_name'], $document_id);
            $farm = $this->db->get()->row_array();

            $this->db->select('isclosed');
            $this->db->from('srp_erp_buyback_batch');
            $this->db->where('batchMasterID', $farm['batchMasterID']);
            $batchClosed = $this->db->get()->row_array();

            if($batchClosed['isclosed'] == 1){
                $this->session->set_flashdata('w', 'This Document cannot be reversed. The batch is already closed.');
                return array('status' => false);
                exit;
            }
        }

        if($document_code == 'BBPV' || $document_code == 'BBRV' || $document_code == 'BBSV')
        {
            $this->db->select('pvd.BatchID, pvd.lossedBatchID');
            $this->db->from('srp_erp_buyback_paymentvoucherdetail pvd');
            $this->db->where('pvMasterAutoID', $document_id);
            $batch = $this->db->get()->result_array();

            foreach ($batch as $val)
            {
                $this->db->select('isclosed');
                $this->db->from('srp_erp_buyback_batch');
                $this->db->where('batchMasterID', $val['BatchID']);
                $batchClosed = $this->db->get()->row_array();

                if($batchClosed['isclosed'] == 1){
                    $this->session->set_flashdata('w', 'This Document cannot be reversed. The batch is already closed.');
                    return array('status' => false);
                    exit;
                } else{
                    $this->db->select('isclosed');
                    $this->db->from('srp_erp_buyback_batch');
                    $this->db->where('batchMasterID', $val['lossedBatchID']);
                    $LossbatchClosed = $this->db->get()->row_array();

                    if($LossbatchClosed['isclosed'] == 1){
                        $this->session->set_flashdata('w', 'This Document cannot be reversed. The batch is already closed.');
                        return array('status' => false);
                        exit;
                    }
                }
            }
        }
        
        if($document_code=='PV' || $document_code=='BSI' || $document_code=='GRV')
        {
            $isexistinassetmaster = $this->db->query("SELECT faID FROM `srp_erp_fa_asset_master` where companyID = '$company_id' AND docOrigin = '$document_code' AND docOriginSystemCode = '$document_id'")->result_array();
            if(!empty($isexistinassetmaster))
            {
                $this->session->set_flashdata('w', 'Document Reversal Has Failed.Asset Has Been Generated');
                return array('status' => false);
                exit;
            }
        }

        $this->db->select('documentID, table_name, table_unique_field_name');
        $this->db->from('srp_erp_documentapproved');
        $this->db->where('documentApprovedID', $auto_id);
        $approved_data = $this->db->get()->row_array();
        if (empty($approved_data)) {
            $this->session->set_flashdata('w', 'Document Already Referred back!');
            return array('status' => false);
        }
        $document_status = $this->current_document_status($document_code,$document_id);

        if ($document_status['status']=='A') {
            //$this->session->set_flashdata('w', ' Update Failed ');
            return $document_status;
            exit();
        }

        if($document_code=='GRV' || $document_code=='PV' || $document_code=='BSI' || $document_code=='ST' || $document_code=='MRN')
        {
            switch ($document_code) {
                case 'GRV' :
                    $item_details = $this->db->query("SELECT srp_erp_grvdetails.grvAutoID AS documentAutoID, 'GRV' AS documentID, grvDetailsID AS documentDetailID,srp_erp_grvdetails.itemAutoID as itemAutoID, (receivedQty/conversionRateUOM) AS Qty,(receivedAmount * receivedQty) AS QtyValue, wareHouseAutoID 
                                    FROM srp_erp_grvdetails 
                                    LEFT JOIN srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID
                                    LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_grvdetails.itemAutoID
                                    WHERE srp_erp_grvmaster.grvAutoID = {$document_id} AND srp_erp_grvmaster.companyID = {$company_id} AND srp_erp_itemmaster.mainCategory='Inventory'")->result_array();
                    break;
                case 'BSI' :
                    $item_details = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID AS documentAutoID, 'BSI' AS documentID, InvoiceDetailAutoID AS documentDetailID, srp_erp_paysupplierinvoicedetail.itemAutoID AS itemAutoID, (requestedQty/conversionRateUOMID) AS Qty,( srp_erp_paysupplierinvoicedetail.transactionAmount / conversionRateUOMID ) AS QtyValue , srp_erp_paysupplierinvoicedetail.wareHouseAutoID 
                                    FROM srp_erp_paysupplierinvoicedetail
                                    LEFT JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicedetail.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID 
                                    LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID
                                    WHERE srp_erp_paysupplierinvoicemaster.companyID = {$company_id} AND	srp_erp_paysupplierinvoicemaster.InvoiceAutoID = {$document_id} AND type = 'item' AND srp_erp_itemmaster.mainCategory='Inventory'")->result_array();
                                   
                                   
                    break;
                case 'PV' :
                    $item_details = $this->db->query("SELECT srp_erp_paymentvoucherdetail.payVoucherAutoId AS documentAutoID, 'PV' AS documentID, payVoucherDetailAutoID AS documentDetailID, srp_erp_paymentvoucherdetail.itemAutoID AS itemAutoID, ( requestedQty / conversionRateUOM ) AS Qty, srp_erp_paymentvoucherdetail.transactionAmount AS QtyValue, wareHouseAutoID 
                                    FROM srp_erp_paymentvoucherdetail
                                    LEFT JOIN srp_erp_paymentvouchermaster ON srp_erp_paymentvoucherdetail.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId 
                                    LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID
                                    WHERE srp_erp_paymentvouchermaster.companyID = {$company_id} AND srp_erp_paymentvouchermaster.payVoucherAutoId = {$document_id} AND type = 'item'  AND srp_erp_itemmaster.mainCategory='Inventory' AND srp_erp_paymentvoucherdetail.itemAutoID IS NOT NULL")->result_array();
                     break;               
                case 'ST' :
                    $item_details = $this->db->query("SELECT srp_erp_stocktransfermaster.stockTransferAutoID AS documentAutoID, 'ST' AS documentID,	stockTransferDetailsID AS documentDetailID,	srp_erp_stocktransferdetails.itemAutoID AS itemAutoID,	( transfer_QTY / conversionRateUOM ) AS Qty, ( transfer_QTY * currentlWacAmount ) AS QtyValue, srp_erp_stocktransfermaster.to_wareHouseAutoID AS wareHouseAutoID 
                                    FROM srp_erp_stocktransferdetails
                                    LEFT JOIN srp_erp_stocktransfermaster ON srp_erp_stocktransferdetails.stockTransferAutoID = srp_erp_stocktransfermaster.stockTransferAutoID 
                                    LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_stocktransferdetails.itemAutoID
                                    WHERE srp_erp_stocktransfermaster.companyID = {$company_id} AND srp_erp_stocktransfermaster.stockTransferAutoID = {$document_id} AND srp_erp_itemmaster.mainCategory='Inventory'")->result_array();
                                    
                    break;
                case 'MRN' :
                    $item_details = $this->db->query("SELECT srp_erp_materialreceiptmaster.mrnAutoID AS documentAutoID, 'ST' AS documentID,	mrnDetailID AS documentDetailID, srp_erp_materialreceiptdetails.itemAutoID AS itemAutoID, (qtyReceived/conversionRateUOM) AS Qty, ( qtyReceived * unitCost ) AS QtyValue, srp_erp_materialreceiptmaster.wareHouseAutoID 
                                    FROM srp_erp_materialreceiptdetails
                                    LEFT JOIN srp_erp_materialreceiptmaster ON srp_erp_materialreceiptdetails.mrnAutoID = srp_erp_materialreceiptmaster.mrnAutoID 
                                    LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_materialreceiptdetails.itemAutoID
                                    WHERE srp_erp_materialreceiptmaster.companyID = {$company_id} AND srp_erp_materialreceiptmaster.mrnAutoID = {$document_id} AND srp_erp_itemmaster.mainCategory='Inventory'")->result_array();
                    break;
            }
            if(!empty($item_details)){
                $itemExceeded = array();
                $matchDetails = array();
                foreach ($item_details AS $val) {
                    $currentStock_arr = $this->db->query("SELECT SUM(transactionQTY/convertionRate) as currentStock FROM `srp_erp_itemledger` where wareHouseAutoID = {$val['wareHouseAutoID']} AND itemAutoID = {$val['itemAutoID']}")->row_array();
                    $currentStockValue_arr = $this->db->query("SELECT SUM(transactionAmount/convertionRate) as currentValue FROM `srp_erp_itemledger` where wareHouseAutoID = {$val['wareHouseAutoID']} AND itemAutoID = {$val['itemAutoID']}")->row_array();
                    $exceedMatch = $this->db->query("SELECT srp_erp_itemexceededmatch.exceededMatchID, SUM(matchedQty) AS matchedQty, srp_erp_itemexceededmatchdetails.exceededItemAutoID FROM `srp_erp_itemexceededmatch` LEFT JOIN srp_erp_itemexceededmatchdetails ON srp_erp_itemexceededmatch.exceededMatchID = srp_erp_itemexceededmatchdetails.exceededMatchID 
                                                WHERE companyID = {$company_id} AND orginDocumentMasterID = {$val['documentAutoID']} AND orginDocumentID = '{$document_code}' AND itemAutoID = {$val['itemAutoID']} AND isDeleted = 0
                                                GROUP BY srp_erp_itemexceededmatch.exceededMatchID")->row_array();

                    if(!empty($exceedMatch['matchQty'])) {
                        $val['Qty'] = $val['Qty'] - $exceedMatch['matchQty'];
                    } else if($val['QtyValue'] > $currentStockValue_arr['currentValue']){
                        array_push($itemExceeded, $val['QtyValue']);

                    } else if ($currentStock_arr['currentStock'] == 0 && $currentStockValue_arr['currentValue'] != 0) {
                        array_push($itemExceeded, $val['itemAutoID']);

                    } else if ($currentStockValue_arr['currentValue'] == 0 && $currentStock_arr['currentStock'] != 0) {
                        array_push($itemExceeded, $val['itemAutoID']);
                    } 
                    else {
                        if(!empty($exceedMatch)) {
                            array_push($matchDetails , $exceedMatch);
                        }
                    }
                }

                if(!empty($itemExceeded)) {
                    $this->session->set_flashdata('w', 'Document Reversal Has Failed. Item Stock Exceeded');
                    return array('status' => false);
                    exit;
                } else {
                    if(!empty($matchDetails)) {
                        foreach ($matchDetails AS $det) {
                            $data = array(
                                'isDeleted' => 1,
                                'deletedEmpID' => current_userID(),
                                'deletedDate' => $this->common_data['current_date']
                            );
                            $this->db->where('exceededMatchID', $det['exceededMatchID']);
                            $this->db->update('srp_erp_itemexceededmatch', $data);

                            $stock = $this->db->query("SELECT exceededItemAutoID, updatedQty, exceededQty, balanceQty,conversionRateUOM FROM `srp_erp_itemexceeded` WHERE exceededItemAutoID = {$det['exceededItemAutoID']}")->row_array();
                            $updatedQty = $stock['updatedQty'] - ($det['matchedQty'] / $stock['conversionRateUOM']);
                            $balanceQty = $stock['balanceQty'] + ($det['matchedQty'] / $stock['conversionRateUOM']);

                            $exceedDet = array(
                                'updatedQty' => $updatedQty,
                                'balanceQty' => $balanceQty,
                                'modifiedPCID' => $this->common_data['current_pc'],
                                'modifiedUserID' => $this->common_data['current_userID'],
                                'modifiedUserName' => $this->common_data['current_user'],
                                'modifiedDateTime' => $this->common_data['current_date'],
                            );
                            $this->db->where('exceededItemAutoID', $det['exceededItemAutoID']);
                            $this->db->update('srp_erp_itemexceeded', $exceedDet);
                        }
                    }
                }
            }
        }

        $this->db->trans_start();

        if($document_code=='GRV' || $document_code=='ST' || $document_code=='MRN') {
            if($document_code=='ST') {
                $this->db->select('srp_erp_stocktransferdetails.itemAutoID as itemAutoID, jobCardID, jobDetailID, srp_erp_stocktransfermaster.jobID, typeMasterAutoID, usageID, srp_erp_stocktransferdetails.stockTransferAutoID AS documentAutoID, transfer_QTY AS Qty, (totalValue/srp_erp_stocktransfermaster.companyLocalExchangeRate) AS totalValue');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->join('srp_erp_stocktransfermaster', 'srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID');
                $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.itemAutoID = srp_erp_stocktransferdetails.ItemAutoID');
                $this->db->join('srp_erp_mfq_jc_usage', "srp_erp_mfq_jc_usage.linkedDocumentAutoID = srp_erp_stocktransferdetails.stockTransferAutoID AND srp_erp_mfq_jc_usage.linkedDocumentID = 'ST' AND srp_erp_mfq_jc_usage.typeMasterAutoID = srp_erp_mfq_itemmaster.mfqItemID");
                $this->db->where('srp_erp_stocktransferdetails.stockTransferAutoID', $document_id);
                $this->db->where('srp_erp_stocktransfermaster.companyID', $company_id);
                $details = $this->db->get()->result_array();
            } else if($document_code=='MRN') {
                $this->db->select('srp_erp_materialreceiptdetails.itemAutoID AS itemAutoID, jobCardID, jobDetailID, srp_erp_materialreceiptmaster.jobID, typeMasterAutoID, usageID, srp_erp_materialreceiptdetails.mrnAutoID AS documentAutoID, qtyReceived AS Qty, (totalValue / srp_erp_materialreceiptmaster.companyLocalExchangeRate) AS totalValue');
                $this->db->from('srp_erp_materialreceiptdetails');
                $this->db->join('srp_erp_materialreceiptmaster', 'srp_erp_materialreceiptmaster.mrnAutoID = srp_erp_materialreceiptdetails.mrnAutoID');
                $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.itemAutoID = srp_erp_materialreceiptdetails.ItemAutoID');
                $this->db->join('srp_erp_mfq_jc_usage', "srp_erp_mfq_jc_usage.linkedDocumentAutoID = srp_erp_materialreceiptmaster.mrnAutoID AND srp_erp_mfq_jc_usage.linkedDocumentID = 'MRN' AND srp_erp_mfq_jc_usage.typeMasterAutoID = srp_erp_mfq_itemmaster.mfqItemID");
                $this->db->where('srp_erp_materialreceiptdetails.mrnAutoID', $document_id);
                $this->db->where('srp_erp_materialreceiptmaster.companyID', $company_id);
                $details = $this->db->get()->result_array();
            } else {
                $this->db->select('srp_erp_grvdetails.itemAutoID as itemAutoID, jobCardID, jobDetailID, srp_erp_grvmaster.jobID, typeMasterAutoID, usageID, srp_erp_grvdetails.grvAutoID AS documentAutoID, (receivedQty) AS Qty, (fullTotalAmount/srp_erp_grvmaster.companyLocalExchangeRate) AS totalValue');
                $this->db->from('srp_erp_grvdetails');
                $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID');
                $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.itemAutoID = srp_erp_grvdetails.ItemAutoID');
                $this->db->join('srp_erp_mfq_jc_usage', "srp_erp_mfq_jc_usage.linkedDocumentAutoID = srp_erp_grvdetails.grvAutoID AND srp_erp_mfq_jc_usage.linkedDocumentID = 'GRV' AND srp_erp_mfq_jc_usage.typeMasterAutoID = srp_erp_mfq_itemmaster.mfqItemID");
                $this->db->where('srp_erp_grvdetails.grvAutoID', $document_id);
                $this->db->where('srp_erp_grvmaster.companyID', $company_id);
                $details = $this->db->get()->result_array();
            }

            if(!empty($details))
            {
                foreach ($details as $det) {
                    $result = $this->db->query("UPDATE srp_erp_mfq_jc_materialconsumption SET 
                            usageQty = usageQty - {$det['Qty']},
                            materialCost = materialCost - {$det['totalValue']},
                            unitCost = IFNULL((materialCost/usageQty ), 0),
                            materialCharge = (materialCost)+((materialCost)*(markUp/100))
                        WHERE jobCardID= {$det['jobCardID']} AND companyID= {$company_id} AND workProcessID= {$det['jobID']} AND mfqItemID= {$det['typeMasterAutoID']}");
                    
                    $this->db->delete('srp_erp_mfq_jc_usage', array('usageID' => $det['usageID'], 'companyID'=>$company_id));
                }
            }
        }

        $ledger_qty = 0;
        $this->db->select('itemAutoID,transactionQTY,convertionRate,wareHouseAutoID,companyLocalAmount,documentID');
        $this->db->from('srp_erp_itemledger');
        $this->db->where('documentAutoID', $document_id);
        $this->db->where('documentID', $document_code);
        $this->db->where('companyID', $company_id);
        $item_ledger_data = $this->db->get()->result_array();

        if (!empty($item_ledger_data)) {
            foreach ($item_ledger_data as $value) {
                $ledger_qty = ($value['transactionQTY']/$value['convertionRate']);
                //if (trim($value['documentID'] ?? '')=='CINV' or trim($value['documentID'] ?? '')=='RV' or trim($value['documentID'] ?? '')=='MI') {
                    //$this->reversing_wac_calculation($value['itemAutoID'],$ledger_qty,$value['companyLocalAmount'],$value['wareHouseAutoID'],0);
                //}else{
                    $this->reversing_wac_calculation($value['itemAutoID'],$ledger_qty,$value['companyLocalAmount'],$value['wareHouseAutoID'],1);
                //}
            }
        }
        IF($document_code ==  'CINV' ){
            $invoiceType= $this->db->query("SELECT invoiceType from srp_erp_customerinvoicemaster 
                WHERE companyID = {$company_id} AND invoiceAutoID = {$document_id} ")->row('invoiceType');
            if($invoiceType == 'Commission'){
                $invoiceCommisionDetails = $this->db->query("SELECT commissionAutoID,confirmedYN from 
                srp_erp_invoice_commision WHERE companyID = {$company_id} AND invoiceID = {$document_id} AND isDeleted=0 
                  ")->row_array();
                if($invoiceCommisionDetails){
                    if($invoiceCommisionDetails['confirmedYN']==1){
                        $this->db->delete('srp_erp_documentapproved', array('documentSystemCode' => $invoiceCommisionDetails['commissionAutoID'],'documentID' => 'IC','companyID'=>$company_id));
                    }
                    $this->db->where('commissionAutoID', $invoiceCommisionDetails['commissionAutoID']);
                    $this->db->update('srp_erp_invoice_commision', array('confirmedYN' => 0,'isDeleted' => 1,'deletedEmpID' => $this->common_data['current_userID'],'deletedDate' => current_date(),'confirmedByEmpID' => null,'confirmedByName' => null,'confirmedDate' => null,'currentLevelNo' => 1,'approvedYN' => 0,'approvedByEmpID' => null,'approvedbyEmpName' => null,'approvedDate ' => null ));
                     
                    $this->db->delete('srp_erp_invoice_commission_detail', array('commissionAutoID' => $invoiceCommisionDetails['commissionAutoID'],'companyID'=>$company_id));
    
                }
                
            }
        }

        if($document_code == 'P') {
            $pretenderConfirmedYN = array(
                'pretenderConfirmedYN' => 0
            );
            $this->db->where('headerID', $document_id);
            $this->db->update('srp_erp_boq_header', $pretenderConfirmedYN);
        }
        $this->db->delete('srp_erp_itemledger', array('documentAutoID' => $document_id,'documentID' => $document_code,'companyID'=>$company_id));
        $this->db->delete('srp_erp_generalledger', array('documentMasterAutoID' => $document_id,'documentCode' => $document_code,'companyID'=>$company_id));
        $this->db->where($approved_data['table_unique_field_name'], $document_id);
        if($document_code ==  'SUP' ||$document_code == 'INV'){
            $this->db->update($approved_data['table_name'], array('masterConfirmedYN' => 0,'masterApprovedYN' => 0,'masterConfirmedByEmpID' => null,'MasterApprovedbyEmpID' => null,'masterConfirmedByName' => null,'masterapprovedbyEmpName' => null,'masterConfirmedDate' => null,'masterApprovedDate' => null,'masterCurrentLevelNo' => 1));
        }else{
            
            $this->db->update($approved_data['table_name'], array('confirmedYN' => 0,'approvedYN' => 0,'confirmedByEmpID' => null,'approvedbyEmpID' => null,'confirmedByName' => null,'approvedbyEmpName' => null,'confirmedDate' => null,'approvedDate' => null,'currentLevelNo' => 1));
        }
        if($document_code == 'DO') {
            $deliverystatus = array(
                'status' => 0
            );
            $this->db->where('DOAutoID', $document_id);
            $this->db->update('srp_erp_deliveryorder', $deliverystatus);
        }
        if ($document_code == 'BSI' || $document_code == 'GRV') {
            if($document_code == 'BSI') {
                $where = " AND srp_erp_paysupplierinvoicedetail.InvoiceAutoID != {$document_id}";
                $where1 = '';
            } else if($document_code == 'GRV') {
                $where1 = " AND srp_erp_grvdetails.grvAutoID != {$document_id}";
                $where = '';
            }
            $this->db->query("UPDATE srp_erp_purchaseordermaster prd
                    JOIN (
                        SELECT purchaseOrderID AS pid, (CASE WHEN balance = 0 THEN '2' WHEN balance = requestedtqy THEN '0' ELSE '1' END) AS sts
                        FROM (
                            SELECT t2.purchaseOrderID, sum(requestedtqy) as requestedtqy, sum(balance) AS balance
                            FROM (
                                SELECT po.purchaseOrderDetailsID, purchaseOrderID, po.itemAutoID, ifnull((po.requestedQty),0) AS requestedtqy, (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0)) AS receivedqty, IF (((po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0))) < 0, 0, ((po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0)))) AS balance
                                    FROM srp_erp_purchaseorderdetails po
                                    LEFT JOIN (
                                        SELECT
                                            purchaseOrderMastertID,
                                            ifnull(sum(requestedQty),0) AS receivedQty,
                                            itemAutoID,
                                            purchaseOrderDetailsID
                                        FROM srp_erp_paysupplierinvoicedetail
                                        LEFT JOIN srp_erp_paysupplierinvoicemaster sinm on srp_erp_paysupplierinvoicedetail.InvoiceAutoID=sinm.InvoiceAutoID
                                        WHERE
                                            sinm.approvedYN = 1 {$where}
                                        GROUP BY
                                            srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID
                                    ) gd ON po.purchaseOrderDetailsID=gd.purchaseOrderDetailsID

                                    LEFT JOIN (
                                        SELECT purchaseOrderMastertID, IFNULL(sum(receivedQty),0) AS receivedQty, itemAutoID, purchaseOrderDetailsID
                                        FROM srp_erp_grvdetails
                                        LEFT JOIN srp_erp_grvmaster grvm on srp_erp_grvdetails.grvAutoID=grvm.grvAutoID
                                        WHERE grvm.grvType='PO Base' AND grvm.approvedYN = 1 {$where1}
                                        GROUP BY 
                                            srp_erp_grvdetails.purchaseOrderDetailsID
                                    ) grd ON po.purchaseOrderDetailsID=grd.purchaseOrderDetailsID
                                ) t2 group by t2.purchaseOrderID
                        ) z
                    ) tt ON prd.purchaseOrderID = tt.pid
                    SET prd.isReceived = tt.sts
                    where  prd.companyID = $company_id AND prd.purchaseOrderID=tt.pid");
        }

        //update item_master_sub table
        $this->db->query("UPDATE srp_erp_itemmaster_sub AS updt_subItem INNER JOIN (
                            SELECT subItemAutoID,isSold,soldDocumentID,soldDocumentAutoID,soldDocumentDetailID 
                            FROM srp_erp_itemmaster_sub 
                            WHERE soldDocumentID = '{$document_code}'  AND soldDocumentAutoID = {$document_id} AND companyID = {$company_id}) as sel_subItem
                            SET updt_subItem.isSold = NULL, updt_subItem.soldDocumentID = NULL, updt_subItem.soldDocumentAutoID = NULL, updt_subItem.soldDocumentDetailID = NULL
                          WHERE updt_subItem.soldDocumentID = '{$document_code}' AND updt_subItem.soldDocumentAutoID = {$document_id} AND updt_subItem.companyID = {$company_id}");

        // delete sub items
        $this->db->delete('srp_erp_itemmaster_sub', array('receivedDocumentID' => $document_code,'receivedDocumentAutoID' => $document_id,'companyID'=>$company_id));

        //$this->db->update($approved_data['table_name'], array('confirmedYN' => 0,'approvedYN' => 0,'confirmedByEmpID' => null,'approvedbyEmpID' => null,'confirmedByName' => null,'approvedbyEmpName' => null,'confirmedDate' => null,'approvedDate' => null,'currentLevelNo' => 1));
        $this->db->delete('srp_erp_documentapproved', array('documentSystemCode' => $document_id,'documentID' => $document_code,'companyID'=>$company_id));

        $this->db->delete('srp_erp_bankledger', array('documentMasterAutoID' => $document_id,'documentType' => $document_code,'companyID'=>$company_id));
        if ($document_code=='GRV') {
            $this->db->delete('srp_erp_match_supplierinvoice',array('grvAutoID'=>$document_id,'companyID'=>$company_id));
        }

        /* delete buyback BBDPN, BBGRN, BBDR from srp_erp_buyback_itemledger*/
        if ($document_code=='BBDPN' || $document_code == 'BBGRN' || $document_code == 'BBDR') {
            $this->db->delete('srp_erp_buyback_itemledger',array('documentAutoID'=>$document_id,'documentCode' => $document_code,'companyID'=>$company_id));
        }
        /*buyback batch close reversing*/
        if ($document_code=='BBBC') {
            $this->db->where($approved_data['table_unique_field_name'], $document_id);
            $this->db->update($approved_data['table_name'], array('isclosed' => 0, 'closedByEmpID' => null,'closedDate' => null,'closingComment' => null));
        }
        if($document_code=='ATS'){
            $this->db->delete('srp_erp_pay_empattendancereview', array('generalOTID' => $document_id,'companyID'=>$company_id));
        }
        $data_reversing['documentMasterAutoID'] = $document_id;
        $data_reversing['documentID']           = $document_code;
        $data_reversing['reversedDate']         = current_date();
        $data_reversing['reversedEmpID']        = $this->common_data['current_userID'];
        $data_reversing['reversedEmployee']     = $this->common_data['current_user'];
        $data_reversing['comments']             = $comments;
        $data_reversing['companyID']            = $company_id;
        $this->db->insert('srp_erp_documentapprovedreversing', $data_reversing);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', ' Update Failed ');
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s',' Updated Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function reversing_wac_calculation($itemAutoID,$defoult_qty,$total_value=0,$wareHouseID=0,$is_minimum=0){
        $CI =& get_instance();
        $com_currency   = $CI->common_data['company_data']['company_default_currency'];
        $com_currDPlace = $CI->common_data['company_data']['company_default_decimal'];
        $rep_currency   = $CI->common_data['company_data']['company_reporting_currency'];
        $rep_currDPlace = $CI->common_data['company_data']['company_reporting_decimal'];

        $item_current_data = $CI->db->select('itemSystemCode,currentStock,defaultUnitOfMeasure,companyLocalWacAmount as current_wac')->from('srp_erp_itemmaster')->where('itemAutoID', $itemAutoID)->get()->row();

        if ($is_minimum == 1) {
            $defoult_qty    *= -1;
            $total_value    *= -1;
            $document_total = $total_value;// * $defoult_qty;
        } else {
            $document_total = $total_value; //* $defoult_qty;//$item_current_data->current_wac * $defoult_qty;
        }

        $newQty = $item_current_data->currentStock + $defoult_qty;
        $currentTot = $item_current_data->current_wac * $item_current_data->currentStock;
        $newTot = $currentTot + $document_total;
        $newWac = round(($newTot / ($newQty != 0) ? $newQty : 1), wacDecimalPlaces);
        $reportConversion = currency_conversion($com_currency,$rep_currency,$newWac);
        $reportConversionRate = ($reportConversion['conversion'] != 0) ? $reportConversion['conversion'] : 1;
        $repWac = round(($newWac / $reportConversionRate), $rep_currDPlace);

        $data = array('currentStock'=>$newQty,'companyLocalWacAmount'=>$newWac,'companyReportingWacAmount'=>$repWac);
        $where = array('itemAutoID' => $itemAutoID,'companyID' => current_companyID());
        $CI->db->where($where)->update('srp_erp_itemmaster', $data);

        if (isset($wareHouseID)) {
            $CI->db->query("UPDATE srp_erp_warehouseitems SET currentStock=(currentStock+{$defoult_qty}) WHERE itemAutoID={$itemAutoID} AND wareHouseAutoID={$wareHouseID}");
        }
        return true;
    }

    function current_document_status($document_code,$document_id){
        $document = array();
        $rev_data_arr = array();
        $companyID  = current_companyID();
        if ($document_code=='PO') {
            $this->db->select('srp_erp_grvmaster.grvAutoID as auto_id, srp_erp_grvmaster.grvPrimaryCode as system_code');
            $this->db->group_by("srp_erp_grvmaster.grvAutoID");
            $this->db->from('srp_erp_grvdetails');
            $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID');
            $this->db->where('purchaseOrderMastertID', $document_id);
            $rev_data_arr1 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr1);

            $this->db->select('srp_erp_paymentvouchermaster.payVoucherAutoId as auto_id,PVcode as system_code');
            $this->db->group_by("srp_erp_paymentvouchermaster.payVoucherAutoId");
            $this->db->from('srp_erp_paymentvoucherdetail');
            $this->db->join('srp_erp_paymentvouchermaster','srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId');
            $this->db->where('type','Advance');
            $this->db->where('purchaseOrderID', $document_id);
            $rev_data_arr2 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr2);

        }elseif ($document_code=='GRV') {
            $this->db->select('srp_erp_paysupplierinvoicemaster.InvoiceAutoID as auto_id,bookingInvCode as system_code');
            $this->db->group_by("srp_erp_paysupplierinvoicemaster.InvoiceAutoID");
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_paysupplierinvoicemaster','srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID');
            $this->db->where('grvAutoID', $document_id);
            $rev_data_arr1 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr1);

            $this->db->select('srp_erp_stockreturnmaster.stockReturnAutoID as auto_id,stockReturnCode as system_code');
            $this->db->group_by("srp_erp_stockreturnmaster.stockReturnAutoID");
            $this->db->from('srp_erp_stockreturndetails');
            $this->db->join('srp_erp_stockreturnmaster','srp_erp_stockreturnmaster.stockReturnAutoID = srp_erp_stockreturndetails.stockReturnAutoID');
            $this->db->where('type','GRV');
            $this->db->where('grvAutoID', $document_id);
            $rev_data_arr2 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr2);

        }elseif ($document_code=='BSI') {
            $this->db->select('srp_erp_paymentvouchermaster.payVoucherAutoId as auto_id, srp_erp_paymentvouchermaster.PVcode as system_code');
            $this->db->group_by("srp_erp_paymentvouchermaster.payVoucherAutoId");
            $this->db->from('srp_erp_paymentvoucherdetail');
            $this->db->join('srp_erp_paymentvouchermaster', 'srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId');
            $this->db->where('InvoiceAutoID', $document_id);
            $rev_data_arr1 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr1);

            $this->db->select('srp_erp_stockreturnmaster.stockReturnAutoID as auto_id,stockReturnCode as system_code');
            $this->db->group_by("srp_erp_stockreturnmaster.stockReturnAutoID");
            $this->db->from('srp_erp_stockreturndetails');
            $this->db->join('srp_erp_stockreturnmaster','srp_erp_stockreturnmaster.stockReturnAutoID = srp_erp_stockreturndetails.stockReturnAutoID');
            $this->db->where('type','BSI');
            $this->db->where('grvAutoID', $document_id);
            $rev_data_arr2 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr2);

            $this->db->select('srp_erp_debitnotemaster.debitNoteMasterAutoID as auto_id, srp_erp_debitnotemaster.debitNoteCode as system_code');
            $this->db->group_by("srp_erp_debitnotemaster.debitNoteMasterAutoID");
            $this->db->from('srp_erp_debitnotedetail');
            $this->db->join('srp_erp_debitnotemaster', 'srp_erp_debitnotemaster.debitNoteMasterAutoID = srp_erp_debitnotedetail.debitNoteMasterAutoID');
            $this->db->where('InvoiceAutoID', $document_id);
            $rev_data_arr2 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr2);

        }elseif ($document_code=='PV') {
            $this->db->select('bankLedgerAutoID as auto_id, srp_erp_bankrecmaster.bankRecPrimaryCode as system_code');
            $this->db->group_by("bankLedgerAutoID");
            $this->db->from('srp_erp_bankledger');
            $this->db->where('documentType','PV');
            $this->db->where('clearedYN',1);
            $this->db->join('srp_erp_bankrecmaster','srp_erp_bankrecmaster.bankRecAutoID=srp_erp_bankledger.bankRecMonthID');
            $this->db->where('documentMasterAutoID', $document_id);
            $rev_data_arr1 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr1);

            $rev_data_arr2= $this->db->query("SELECT srp_erp_pvadvancematch.matchID AS `auto_id`,matchSystemCode AS `system_code` 
                                                  FROM `srp_erp_pvadvancematch` LEFT JOIN srp_erp_pvadvancematchdetails ON srp_erp_pvadvancematch.matchID = srp_erp_pvadvancematchdetails.matchID 
                                                  WHERE srp_erp_pvadvancematchdetails.companyID = $companyID AND payVoucherAutoId = $document_id GROUP BY srp_erp_pvadvancematch.matchID")->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr2);


        }elseif ($document_code=='BT') {
            $this->db->select('bankLedgerAutoID as auto_id, srp_erp_bankrecmaster.bankRecPrimaryCode as system_code');
            $this->db->group_by("bankLedgerAutoID");
            $this->db->from('srp_erp_bankledger');
            $this->db->where('documentType','BT');
            $this->db->where('clearedYN',1);
            $this->db->join('srp_erp_bankrecmaster','srp_erp_bankrecmaster.bankRecAutoID=srp_erp_bankledger.bankRecMonthID');
            $this->db->where('documentMasterAutoID', $document_id);
            $rev_data_arr = $this->db->get()->result_array();

        }elseif ($document_code=='RV') {
            $this->db->select('bankLedgerAutoID as auto_id, srp_erp_bankrecmaster.bankRecPrimaryCode as system_code');
            $this->db->group_by("bankLedgerAutoID");
            $this->db->from('srp_erp_bankledger');
            $this->db->where('documentType','RV');
            $this->db->where('clearedYN',1);
            $this->db->join('srp_erp_bankrecmaster','srp_erp_bankrecmaster.bankRecAutoID =srp_erp_bankledger.bankRecMonthID');
            $this->db->where('documentMasterAutoID', $document_id);
            $rev_data_arr1 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr1);

            $this->db->select('srp_erp_rvadvancematch.matchID AS `auto_id`, matchSystemCode AS `system_code`');
            $this->db->from('srp_erp_rvadvancematchdetails');
            $this->db->join('srp_erp_rvadvancematch','srp_erp_rvadvancematch.matchID = srp_erp_rvadvancematchdetails.matchID');
            $this->db->where('srp_erp_rvadvancematchdetails.companyID',$companyID);
            $this->db->where('receiptVoucherAutoId',$document_id);
            $this->db->group_by("srp_erp_rvadvancematch.matchID");
            $rev_data_arr2 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr2);


        }elseif ($document_code=='CINV') {


            $this->db->select('srp_erp_customerreceiptmaster.receiptVoucherAutoId as auto_id, srp_erp_customerreceiptmaster.RVcode as system_code');
            $this->db->group_by("srp_erp_customerreceiptmaster.receiptVoucherAutoId");
            $this->db->from('srp_erp_customerreceiptdetail');
            $this->db->join('srp_erp_customerreceiptmaster','srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId');
            $this->db->where('invoiceAutoID', $document_id);
            $rev_data_arr1 = $this->db->get()->result_array();
            $rev_data_arr = array_merge($rev_data_arr,$rev_data_arr1);

            $this->db->select('srp_erp_creditnotemaster.creditNoteMasterAutoID as auto_id, srp_erp_creditnotemaster.creditNoteCode as system_code');
            $this->db->group_by("srp_erp_creditnotemaster.creditNoteMasterAutoID");
            $this->db->from('srp_erp_creditnotedetail');
            $this->db->join('srp_erp_creditnotemaster', 'srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID');
            $this->db->where('invoiceAutoID', $document_id);
            $rev_data_arr2 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr2);


            $this->db->select('srp_erp_rvadvancematch.matchID AS auto_id, matchSystemCode AS system_code');
            $this->db->group_by("srp_erp_rvadvancematch.matchID");
            $this->db->from('srp_erp_rvadvancematchdetails');
            $this->db->join('srp_erp_rvadvancematch', 'srp_erp_rvadvancematch.matchID = srp_erp_rvadvancematchdetails.matchID');
            $this->db->where('srp_erp_rvadvancematchdetails.companyID',$companyID);
            $this->db->where('invoiceAutoID', $document_id);
            $rev_data_arr3 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr3);
            
            $invoiceType= $this->db->query("SELECT invoiceType from srp_erp_customerinvoicemaster 
                WHERE companyID = {$companyID} AND invoiceAutoID = {$document_id} ")->row('invoiceType');
            if($invoiceType == 'Commission'){
                $this->db->select('srp_erp_invoice_commision.commissionAutoID AS auto_id, documentSystemCode AS system_code');
                $this->db->group_by("srp_erp_invoice_commision.commissionAutoID");
                $this->db->from('srp_erp_invoice_commision');
                $this->db->join('srp_erp_invoice_commission_detail', 'srp_erp_invoice_commision.commissionAutoID = srp_erp_invoice_commission_detail.commissionAutoID','left');
                $this->db->where('srp_erp_invoice_commision.companyID',$companyID);
                $this->db->where('invoiceID', $document_id);
                $this->db->where('isDeleted', 0);
                //$this->db->where('approvedYN !=', 0);
                $this->db->where('payrollID >', 0);
                $rev_data_arr4 = $this->db->get()->result_array();
                $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr4);
            }  
            
        }else if ($document_code=='PRQ')
        {
            $this->db->select('srp_erp_purchaseordermaster.purchaseOrderID as auto_id,IFNULL(purchaseOrderCode,0) as system_code');
            $this->db->group_by("srp_erp_purchaseordermaster.purchaseOrderID");
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->join('srp_erp_purchaseordermaster','srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_purchaseorderdetails.purchaseOrderID');
            $this->db->where('prMasterID', $document_id);
            $rev_data_arr1 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr1);

            $this->db->select('srp_erp_paymentvouchermaster.payVoucherAutoId as auto_id,IFNULL(srp_erp_paymentvouchermaster.PVcode,0)  as system_code');
            $this->db->group_by("srp_erp_paymentvouchermaster.payVoucherAutoId");
            $this->db->from('srp_erp_paymentvoucherdetail');
            $this->db->join('srp_erp_paymentvouchermaster','srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId');
            $this->db->where('prMasterID', $document_id);
            $rev_data_arr2 = $this->db->get()->result_array();
            $rev_data_arr =  array_merge($rev_data_arr,$rev_data_arr2);

        }elseif ($document_code=='JV') {
            $this->db->select('bankLedgerAutoID as auto_id, srp_erp_bankrecmaster.bankRecPrimaryCode as system_code');
            $this->db->group_by("bankLedgerAutoID");
            $this->db->from('srp_erp_bankledger');
            $this->db->where('documentType','JV');
            $this->db->where('clearedYN',1);
            $this->db->join('srp_erp_bankrecmaster','srp_erp_bankrecmaster.bankRecAutoID =srp_erp_bankledger.bankRecMonthID');
            $this->db->where('documentMasterAutoID', $document_id);
            $rev_data_arr = $this->db->get()->result_array();
        }elseif ($document_code=='SO' || $document_code=='QUT' || $document_code=='CNT') {

            $compID = current_companyID();
            $qry = ("SELECT srp_erp_deliveryorderdetails.`DOAutoID` AS `auto_id`, `DOCode` AS system_code 
                            FROM srp_erp_deliveryorderdetails
                                JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
                            WHERE srp_erp_deliveryorderdetails.contractAutoID = $document_id AND srp_erp_deliveryorderdetails.companyID = $compID 
                            GROUP BY srp_erp_deliveryorder.DOAutoID UNION
                            SELECT srp_erp_customerinvoicemaster.invoiceAutoID AS auto_id, invoiceCode AS system_code
                            FROM srp_erp_customerinvoicedetails
                                LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicedetails.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                            WHERE srp_erp_customerinvoicemaster.contractAutoID = $document_id 
                            GROUP BY srp_erp_customerinvoicemaster.invoiceAutoID");
            $rev_data_arr = $this->db->query($qry)->result_array();

        }else if($document_code=='BRC')
        {
            $companyID = current_companyID();
            $bankrecmaster = $this->db->query("SELECT bankGLAutoID,DATE(bankRecAsOf) as date FROM  srp_erp_bankrecmaster where companyID = $companyID AND bankRecAutoID = $document_id ")->row_array();

                $qry = ("SELECT bankRecAutoID AS auto_id,bankRecPrimaryCode AS system_code FROM srp_erp_bankrecmaster WHERE companyID = $companyID AND bankRecAutoID != $document_id AND bankGLAutoID in 
                        (select bankGLAutoID from  srp_erp_bankrecmaster where bankGLAutoID={$bankrecmaster['bankGLAutoID']} AND DATE(bankRecAsOf) > DATE('{$bankrecmaster['date']}'))");
                $rev_data_arr = $this->db->query($qry)->result_array();
        }else if($document_code=='SLR')
        {
            $rev_data_arr =  $this->db->query("select
                                               srp_erp_customerreceiptmaster.`receiptVoucherAutoId` AS `auto_id`,
                                               srp_erp_customerreceiptmaster.RVcode as system_code
                                               from 
                                               srp_erp_customerreceiptdetail
                                               LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
                                               where 
                                               srp_erp_customerreceiptmaster.companyID = $companyID 
                                               AND type ='SLR'
                                               AND creditNoteAutoID = $document_id
                                               GROUP BY 
                                               srp_erp_customerreceiptmaster.`receiptVoucherAutoId` ")->result_array();
        }

        if (empty($rev_data_arr)) {

            $data['status'] = 'B';

        }else {
            $data['status'] = 'A';
            $data['data'] = $rev_data_arr;
        }
        return $data;
/*        return array('status' => 'A','data' =>$rev_data_arr);*/
    }

    function reversing_approval_HRDocument(){
        $auto_id        = trim($this->input->post('auto_id') ?? '');
        $document_id    = trim($this->input->post('document_id') ?? '');
        $document_code  = trim($this->input->post('document_code') ?? '');
        $comments       = trim($this->input->post('comments') ?? '');
        $companyID      = current_companyID();

        $document_code = $this->input->post('document_code');
        //$HR_documentCodes = array('SP', 'SD', 'NSP', 'BTL');
        $HR_documentCodes = array('SP', 'SD', 'SPN');
        if(in_array($document_code, $HR_documentCodes)) {
            $this->db->select('documentID, table_name, table_unique_field_name');
            $this->db->from('srp_erp_documentapproved');
            $this->db->where('documentApprovedID', $auto_id);
            $this->db->where('companyID', $companyID);
            $masterData = $this->db->get()->row_array();
            if (empty($masterData)) {
                $this->session->set_flashdata('w', ' Update Failed');
                return array('status' => false);
            }

            if ($document_code == 'SP' OR $document_code == 'SPN') {
                $transferMaster = ($document_code == 'SPN') ? 'srp_erp_pay_non_banktransfermaster' : 'srp_erp_pay_banktransfermaster';
                $transferDetail = ($document_code == 'SPN') ? 'srp_erp_pay_non_banktransfer' : 'srp_erp_pay_banktransfer';
                $payrollMaster = ($document_code == 'SPN') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';
                $wthOutBankTransfer = ($document_code == 'SPN') ? 'srp_erp_non_payroll_salarypayment_without_bank' : 'srp_erp_payroll_salarypayment_without_bank';

                /*Is payment voucher generated for this payroll`s / non payroll`s bank transfer*/
                $pv_codes = $this->db->query("SELECT PVcode FROM {$transferMaster} trnMas
                              JOIN srp_erp_paymentvouchermaster voMas ON voMas.bankTransferID = trnMas.bankTransferID
                              WHERE trnMas.payrollMasterID = {$document_id} AND voMas.approvedYN=1
                              UNION ALL 
	                          SELECT PVcode FROM {$wthOutBankTransfer} empwithoutbank
	                          JOIN srp_erp_paymentvouchermaster voMas  ON voMas.payVoucherAutoId = empwithoutbank.payVoucherAutoId 
                              WHERE empwithoutbank.payrollMasterID = {$document_id} AND voMas.approvedYN=1")->result_array();

                if(!empty($pv_codes)){
                    $msg = 'Following Payment vouchers generated for this payroll`s/ non payroll`s bank transfer already approved<br/><b> &nbsp; - ';
                    $msg .= implode('<br/> &nbsp; - ', array_column($pv_codes, 'PVcode'));
                    $msg .= '</b><br/>You can not reverse this payroll.';

                    $this->session->set_flashdata('e', $msg);
                    return array('status' => false);
                }

                $this->db->trans_start();
                $pv_arr = $this->db->query("SELECT voMas.payVoucherAutoId FROM {$transferMaster} trnMas
                                            JOIN srp_erp_paymentvouchermaster voMas ON voMas.bankTransferID = trnMas.bankTransferID
                                            WHERE trnMas.payrollMasterID = {$document_id} AND isDeleted = 0")->result_array();

                $emp_withoutbank_pv = $this->db->query("SELECT voMas.payVoucherAutoId FROM {$wthOutBankTransfer} empwithoutbank
                                                        JOIN srp_erp_paymentvouchermaster voMas  ON voMas.payVoucherAutoId = empwithoutbank.payVoucherAutoId 
                                                        WHERE empwithoutbank.payrollMasterID = {$document_id} AND isDeleted = 0")->result_array();

                if(!empty($pv_arr)){
                    $pv_arr = array_column($pv_arr, 'payVoucherAutoId');


                    $this->db->where_in('documentSystemCode', $pv_arr)->where('documentID', 'PV')
                        ->where('companyID', $companyID)->delete('srp_erp_documentapproved');

                    $this->db->where_in('payVoucherAutoId', $pv_arr)->where('companyID', $companyID)
                        ->delete('srp_erp_paymentvoucherdetail');

                    $up_data = [
                        'isDeleted' => 1, 'deletedEmpID' => current_userID(), 'deletedDate' => current_date(),
                        'confirmedYN' => 0, 'confirmedByEmpID'=> null, 'confirmedByName' => null, 'confirmedDate'=> null,
                        'payrollMasterID'=> $document_id,'bankTransferID'=>null
                    ];
                    $this->db->where_in('payVoucherAutoId', $pv_arr)->where('companyID', $companyID)
                        ->update('srp_erp_paymentvouchermaster', $up_data);

                    
                    $this->db->query('DELETE  FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId IN ('.implode(',', $pv_arr).')');
                    
                    

                }
                if(!empty($emp_withoutbank_pv)){ //Delete employee`s with out bank account details PaymentVoucher
                    $pv_arr_withoutbank = array_column($emp_withoutbank_pv, 'payVoucherAutoId');
                   
                    $this->db->where_in('documentSystemCode', $pv_arr_withoutbank)->where('documentID', 'PV')
                        ->where('companyID', $companyID)->delete('srp_erp_documentapproved');

                    $this->db->where_in('payVoucherAutoId', $pv_arr_withoutbank)->where('companyID', $companyID)
                        ->delete('srp_erp_paymentvoucherdetail');

                    $up_data = [
                        'isDeleted' => 1, 'deletedEmpID' => current_userID(), 'deletedDate' => current_date(),
                        'confirmedYN' => 0, 'confirmedByEmpID'=> null, 'confirmedByName' => null, 'confirmedDate'=> null,
                        'emppayrollMasterID'=> $document_id,'bankTransferID'=>null
                    ];
                    $this->db->where_in('payVoucherAutoId', $pv_arr_withoutbank)->where('companyID', $companyID)
                        ->update('srp_erp_paymentvouchermaster', $up_data);

                   
                    $this->db->query('DELETE  FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId IN ('.implode(',', $pv_arr_withoutbank).')');
                   
                    
                }


                $where = ['companyID'=> $companyID, 'payrollMasterID' => $document_id];
                $this->db->delete($transferMaster, $where);
                $this->db->delete($transferDetail, $where);
                $this->db->delete($wthOutBankTransfer, $where); //Delete employee`s with out bank account details

                $this->db->delete('srp_erp_generalledger', ['documentMasterAutoID' => $document_id, 'documentCode' => $document_code, 'companyID' => $companyID]);

                $data = array(
                    'isBankTransferProcessed' => 0,
                    'confirmedYN' => 0,
                    'approvedYN' => 0,
                    'confirmedByEmpID' => null,
                    'approvedbyEmpID' => null,
                    'confirmedByName' => null,
                    'approvedbyEmpName' => null,
                    'confirmedDate' => null,
                    'approvedDate' => null,
                    'currentLevelNo' => 1
                );

                $this->db->where($where)->update($payrollMaster, $data);

                $this->processComplete();

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', ' Update Failed ');
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', ' Updated Successfully.');
                  

                    $this->db->trans_commit();
                    return array('status' => true);
                }
            }
            elseif ($document_code == 'SD') {
                /** verify salary declaration for payroll / Non Payroll**/
                $salaryDecType = $this->db->query("SELECT isPayrollCategory FROM srp_erp_salarydeclarationmaster
                                                   WHERE salarydeclarationMasterID={$document_id} AND companyID={$companyID}")->row('isPayrollCategory');

                $payrollMaster = ($salaryDecType == 2)? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';
                $payrollDet = ($salaryDecType == 2)? 'srp_erp_non_payrolldetail' : 'srp_erp_payrolldetail';
                $salaryDeclaration = ($salaryDecType == 2)? 'srp_erp_non_pay_salarydeclartion' : 'srp_erp_pay_salarydeclartion';


                /** Check there are payroll processed with this salary declaration **/
                $processedPayroll = $this->db->query("SELECT payrollMaster.documentCode AS system_code
                                                      FROM {$salaryDeclaration} AS declarationDet
                                                      JOIN {$payrollDet} AS payrollDet ON payrollDet.detailTBID = declarationDet.id
                                                      AND payrollDet.companyID={$companyID} AND sdMasterID={$document_id} AND fromTB='SD'
                                                      JOIN {$payrollMaster} AS payrollMaster ON payrollMaster.payrollMasterID = payrollDet.payrollMasterID
                                                      WHERE declarationDet.companyID={$companyID} GROUP BY payrollMaster.payrollMasterID")->result_array();

                $finalSettlement = [];
                if($salaryDecType != 2){ /*Only for payroll salary declaration*/
                    $finalSettlement = $this->db->query("SELECT fsMaster.documentCode AS system_code
                                                      FROM {$salaryDeclaration} AS declarationDet
                                                      JOIN srp_erp_pay_finalsettlementmoredetails AS fsMorDet ON fsMorDet.otherDetailID = declarationDet.id
                                                      AND fsMorDet.companyID={$companyID} AND entryType = 'FA'
                                                      JOIN srp_erp_pay_finalsettlementmaster AS fsMaster ON fsMaster.masterID = fsMorDet.fsMasterID
                                                      WHERE declarationDet.companyID={$companyID} AND declarationDet.sdMasterID={$document_id} GROUP BY fsMaster.masterID")->result_array();
                }


                if( empty($processedPayroll) && empty($finalSettlement)){
                    $this->db->trans_start();

                    $this->db->delete($salaryDeclaration, ['companyID'=>$companyID, 'sdMasterID'=>$document_id] );

                    $data = array(
                        'confirmedYN' => 0,
                        'approvedYN' => 0,
                        'confirmedByEmpID' => null,
                        'approvedbyEmpID' => null,
                        'confirmedByName' => null,
                        'approvedbyEmpName' => null,
                        'confirmedDate' => null,
                        'approvedDate' => null,
                        'currentLevelNo' => 1
                    );

                    $this->db->where(['companyID'=>$companyID, 'salarydeclarationMasterID'=>$document_id])->update('srp_erp_salarydeclarationmaster', $data);

                    $this->processComplete();

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->session->set_flashdata('e', ' Update Failed ');
                        $this->db->trans_rollback();
                        return array('status' => false);
                    } else {
                        $this->session->set_flashdata('s', ' Updated Successfully.');
                        $this->db->trans_commit();
                        return array('status' => true);
                    }
                }
                else{

                    $msg = '';
                    if( !empty($processedPayroll) ){
                        $msg = 'Following <b>payrolls</b> are processed for this salary declaration<br/>';
                        $msg .= implode('<br/>', array_column($processedPayroll, 'system_code'));
                    }

                    if( !empty($finalSettlement) ){
                        $msg .= ( $msg != '' )? '<br/><br/>': '';
                        $msg .= 'Following <b>final settlement</b> are processed for this salary declaration<br/>';
                        $msg .= implode('<br/>', array_column($finalSettlement, 'system_code'));
                    }

                    $this->session->set_flashdata('e', $msg);
                    return array('status' => false);
                }

            }

        }
        else{
            $this->session->set_flashdata('w', ' In valid document code.');
            return array('status' => false);
        }


    }

    function processComplete(){

        $document_id    = trim($this->input->post('document_id') ?? '');
        $document_code  = trim($this->input->post('document_code') ?? '');
        $comments       = trim($this->input->post('comments') ?? '');
        $companyID      = current_companyID();

        $this->db->delete('srp_erp_documentapproved', array('documentSystemCode' => $document_id, 'documentID' => $document_code, 'companyID' => $companyID));

        $data_reversing['documentMasterAutoID'] = $document_id;
        $data_reversing['documentID']           = $document_code;
        $data_reversing['reversedDate']         = date('Y-m-d');
        $data_reversing['reversedEmpID']        = current_userID();
        $data_reversing['reversedEmployee']     = current_employee();
        $data_reversing['comments']             = $comments;
        $data_reversing['companyID']            = $companyID;

        $this->db->insert('srp_erp_documentapprovedreversing', $data_reversing);

    }
    
    function get_adjusted_values($document_code){

        $companyID      = current_companyID();
        $accepted_gl_types = ['PLI','BSA'];
        $selected = array();
        $base_arr = array();

        $base_arr['document_code'] = $document_code;

        $this->db->select('*');
        $this->db->from('srp_erp_generalledger');
        $this->db->where('documentSystemCode', $document_code);
        $this->db->where_in('GLType', $accepted_gl_types);
        $gl_records = $this->db->get()->result_array();

        foreach($gl_records as $key => $value){

            $bypassed_gl_code_arr = ['IN0001','ADSP0001'];
            // $bypassed_gl_code = 'ADSP0001';
          

            if(!in_array($value['GLCode'],$bypassed_gl_code_arr) && $value['splitYN'] != 1){
                $selected[] = $value;
            }

            $base_arr['document_date'] = $value['documentDate'];
        }

        $base_arr['selected'] = $selected;

        return $base_arr;

    }

    function set_gl_split_amount(){

        $gl_arr    = $this->input->post('gl_arr');
        $credit_adjust    = $this->input->post('credit_adjust');
        $debit_adjust    = $this->input->post('debit_adjust');
        $credit_amount    = $this->input->post('credit_amount');
        $debit_amount    = $this->input->post('debit_amount');
        $final_amount_credit    = $this->input->post('final_amount_credit');
        $final_amount_debit    = $this->input->post('final_amount_debit');
        $segment    = $this->input->post('segment');
        $cr_gl_code    = $this->input->post('cr_gl_code');
        $dr_gl_code    = $this->input->post('dr_gl_code');
        $split_autoid = $this->input->post('split_autoid');

        $response = $this->add_document_split_amount();

        //clear existing ledger records
        if($split_autoid){
            $response = $this->clear_split_detail();
        }


        foreach($gl_arr as $value){

            $cr_dr = $value['name'];
            $gl_id = $value['gl'];

            if($cr_dr == 'cr'){
                $gl_res = $this->edit_existing_amount($gl_id,$final_amount_credit);
                $res = $this->add_gl_record_for_split($gl_id,$cr_gl_code,$segment,$cr_dr,$credit_adjust);
            }else{
                $gl_res = $this->edit_existing_amount($gl_id,$final_amount_debit);
                $res = $this->add_gl_record_for_split($gl_id,$dr_gl_code,$segment,$cr_dr,$debit_adjust);
            }

        }

        $this->session->set_flashdata('s', 'Spli amount successfully updated');
        return array('status' => True);
       

    }

    function add_document_split_amount(){

        $credit_adjust    = $this->input->post('credit_adjust');
        $debit_adjust    = $this->input->post('debit_adjust');
        $credit_amount    = $this->input->post('credit_amount');
        $debit_amount    = $this->input->post('debit_amount');
        $final_amount_credit    = $this->input->post('final_amount_credit');
        $final_amount_debit    = $this->input->post('final_amount_debit');
        $segment    = $this->input->post('segment');
        $cr_gl_code    = $this->input->post('cr_gl_code');
        $dr_gl_code    = $this->input->post('dr_gl_code');
        $split_autoid    = $this->input->post('split_autoid');
        $document_code    = $this->input->post('document_code');
        $document_date    = $this->input->post('document_date');
        $dr_gl_code    = $this->input->post('dr_gl_code');
        $dr_gl_code    = $this->input->post('dr_gl_code');
        $split_autoid = $this->input->post('split_autoid');

        $companyID = current_companyID();

        $data = array();

        $data['document_code'] = 'POS';
        $data['document_id'] = $document_code;
        $data['date'] = $document_date;
        $data['segment_id'] = $segment;
        $data['credit_gl_autoid'] = $cr_gl_code;
        $data['debit_gl_autoid'] = $dr_gl_code;
        $data['credit_amount'] = $credit_amount;
        $data['credit_adjusment'] = $credit_adjust;
        $data['credit_final_amount'] = $final_amount_credit;
        $data['debit_amount'] = $debit_amount;
        $data['debit_adjusment'] = $debit_adjust;
        $data['debit_final_amount'] = $final_amount_debit;
        $data['split_date'] = date('Y-m-d H:i:s');
        $data['company_id'] = $companyID;

        if($split_autoid){
            $this->db->where('id',$split_autoid)->update('srp_erp_reversal_documentsplit',$data);
        }else{

            $this->db->insert('srp_erp_reversal_documentsplit',$data);

        }

    }  
    
    function add_gl_record_for_split($gl_autoid,$gl_code,$segment_id,$amount_type,$amount){

        $gl_details = fetch_gl_account_desc($gl_code);
        $companyID = current_companyID();
        $ledger_details = reversing_get_gl_record($gl_autoid);
        $segment_details = get_segemnt_by_id($segment_id);

        $data = $ledger_details;

        unset($data['generalLedgerAutoID']);
        $data['GLAutoID'] = $gl_details['GLAutoID'];
        $data['systemGLCode'] = $gl_details['systemAccountCode'];
        $data['GLCode'] = $gl_details['GLSecondaryCode'];
        $data['GLDescription'] = $gl_details['GLDescription'];
        $data['GLType'] = $gl_details['subCategory'];
        $data['amount_type'] = $amount_type;

        $data['transactionAmount'] = $amount;
        $data['companyLocalAmount'] = $ledger_details['companyLocalExchangeRate'] * $amount;
        $data['companyReportingAmount'] = $ledger_details['companyReportingExchangeRate'] * $amount;
        $data['partyCurrencyAmount'] = $ledger_details['partyExchangeRate'] * $amount;

        $data['segmentID'] = $segment_details['segmentID'];
        $data['segmentCode'] = $segment_details['segmentCode'];
        $data['splitYN'] = 1;

        // Add record
        $res = $this->db->insert('srp_erp_generalledger',$data);

    }

    function edit_existing_amount($gl_autoid,$amount){

        $ledger_details = reversing_get_gl_record($gl_autoid);

        if($ledger_details){

            $ledger_details['transactionAmount'] = $amount;
            $ledger_details['companyLocalAmount'] = $ledger_details['companyLocalExchangeRate'] * $amount;
            $ledger_details['companyReportingAmount'] = $ledger_details['companyReportingExchangeRate'] * $amount;
            $ledger_details['partyCurrencyAmount'] = $ledger_details['partyExchangeRate'] * $amount;

            $res = $this->db->where('generalLedgerAutoID',$gl_autoid)->update('srp_erp_generalledger',$ledger_details);
        }

    }

    function clear_split_detail(){
        $document_code    = $this->input->post('document_code');
        $split_autoid = $this->input->post('split_autoid');

        if($split_autoid){

            $res = $this->db->where('documentSystemCode',$document_code)->where('splitYN',1)->delete('srp_erp_generalledger');

        }

    }

}
