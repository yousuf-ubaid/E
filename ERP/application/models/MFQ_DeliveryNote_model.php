<?php

class MFQ_DeliveryNote_model extends ERP_Model
{

    function save_delivery_note_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $companyID = current_companyID();
        $format_deliveryDate = input_format_date(trim($this->input->post('deliveryDate') ?? ''), $date_format_policy);
        $deliverNoteID = trim($this->input->post('deliverNoteID') ?? '');
        $jobID = $this->input->post('jobID');
        $jobError = array();
        $where = '';
        if ($deliverNoteID) {
            $where = " WHERE deliveryNoteID != " . $deliverNoteID;
        }
        foreach ($jobID AS $job) {
            $validateQty = $this->db->query("SELECT `workProcessID`, `documentCode`, job.qty, dnQty FROM `srp_erp_mfq_job` `job`
                LEFT JOIN `srp_erp_mfq_estimatedetail` `estd` ON `estd`.`estimateDetailID` = `job`.`estimateDetailID`
                LEFT JOIN `srp_erp_mfq_estimatemaster` `estm` ON `estd`.`estimateMasterID` = `estm`.`estimateMasterID` 
                LEFT JOIN (SELECT SUM(deliveredQty) as dnQty, jobID FROM srp_erp_mfq_deliverynotedetail $where GROUP BY jobID)delivered ON delivered.jobID = job.workProcessID
            WHERE `job`.`companyID` = {$companyID} AND `job`.`approvedYN` = 1 AND workProcessID = {$job}")->row_array();
            if ($validateQty['qty'] <= $validateQty['dnQty']) {
                $jobError[] = $validateQty;
            }
        }
        if (!empty($jobError)) {
            $jobCode = array_column($jobError, 'documentCode');
            $jobCode = implode(', ', $jobCode);
            return array('e', 'Selected Job Qty Exceeded' . $jobCode);
        }

        $data['mfqCustomerAutoID'] = trim($this->input->post('mfqCustomerAutoID') ?? '');
        $data['deliveryDate'] = $format_deliveryDate;
//        $data['jobID'] = trim($this->input->post('jobID') ?? '');
        $data['driverName'] = trim($this->input->post('driverName') ?? '');
        $data['vehicleNo'] = trim($this->input->post('vehicleNo') ?? '');
        $data['mobileNo'] = trim($this->input->post('mobileNo') ?? '');
        $data['mfqSegmentID'] = trim($this->input->post('mfqsegmentID') ?? '');
        $data['note'] = $this->input->post('invoiceNote');
        $data['comment'] = $this->input->post('comment');

        if ($deliverNoteID) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('deliverNoteID', $deliverNoteID);
            $this->db->update('srp_erp_mfq_deliverynote', $data);

            foreach ($jobID AS $job) {
                $existJob = $this->db->query("SELECT * FROM srp_erp_mfq_deliverynotedetail WHERE companyID = {$companyID} AND jobID = {$job} AND deliveryNoteID = {$deliverNoteID}")->row_array();

                if (empty($existJob)) {
                    $qty = $this->db->query("SELECT poNumber, qty, IFNULL( deliveredQty, 0 ) AS deliveredQty, (qty - IFNULL(deliveredQty, 0)) AS requestedDeliveredQty FROM srp_erp_mfq_job LEFT JOIN ( SELECT SUM(deliveredQty) AS deliveredQty, jobID FROM srp_erp_mfq_deliverynotedetail GROUP BY jobID ) delivered ON delivered.jobID = srp_erp_mfq_job.workProcessID LEFT JOIN srp_erp_mfq_estimatedetail estd ON estd.estimateDetailID = srp_erp_mfq_job.estimateDetailID LEFT JOIN srp_erp_mfq_estimatemaster estm ON estd.estimateMasterID = estm.estimateMasterID WHERE workProcessID = {$job} AND srp_erp_mfq_job.companyID = {$companyID}")->row_array();
                    //  $qty = $this->db->query("SELECT poNumber, qty, IFNULL( deliveredQty, 0 ) AS deliveredQty, (qty - IFNULL(deliveredQty, 0)) AS requestedDeliveredQty FROM srp_erp_mfq_job LEFT JOIN ( SELECT SUM(deliveredQty) AS deliveredQty, jobID FROM srp_erp_mfq_deliverynotedetail GROUP BY jobID ) delivered ON delivered.jobID = srp_erp_mfq_job.workProcessID LEFT JOIN srp_erp_mfq_estimatedetail estd ON estd.estimateDetailID = srp_erp_mfq_job.estimateDetailID LEFT JOIN srp_erp_mfq_estimatemaster estm ON estd.estimateMasterID = estm.estimateMasterID WHERE workProcessID = {$job} AND srp_erp_mfq_job.companyID = {$companyID}")->row_array();
               

                    $det['deliveryNoteID'] = $deliverNoteID;
                    $det['jobID'] = $job;
                    $det['requestedDeliveredQty'] = $qty['requestedDeliveredQty'];
                    $det['deliveredQty'] = $qty['requestedDeliveredQty'];
                    $det['poNumberDN'] = $qty['poNumber'];
                    $det['companyID'] = current_companyID();
                    $det['createdUserName'] = current_user();
                    $det['createdPCID'] = current_pc();
                    $det['createdUserID'] = current_userID();
                    $det['createdDateTime'] = current_date();

                    $this->db->insert('srp_erp_mfq_deliverynotedetail', $det);
                }
            }

            $job_arr = implode(', ', $jobID);
            $removeJob = $this->db->query("SELECT deliveryNoteDetailID FROM srp_erp_mfq_deliverynotedetail WHERE companyID = {$companyID} AND jobID NOT IN ({$job_arr}) AND deliveryNoteID = {$deliverNoteID}")->result_array();
            if (!empty($removeJob)) {
                foreach ($removeJob as $remove) {
                    $this->db->delete('srp_erp_mfq_deliverynotedetail', array('deliveryNoteDetailID' => $remove['deliveryNoteDetailID']));
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Delivery Note Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Delivery Note Updated Successfully.', $deliverNoteID);
            }
        } else {
            $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_deliverynote', 'deliverNoteID', 'companyID');
            $codes = $this->sequence->sequence_generator('MDN', $serialInfo['serialNo']);
            $data['serialNo'] = $serialInfo['serialNo'];
            $data['deliveryNoteCode'] = $codes;
            $data['documentID'] = 'MDN';
            $jobrefno = null;

            if ($jobID) {
                $jobfilter = join(",", $jobID);
                $ponumber = $this->db->query("select 
                                              linkjob.documentCode
                                              from 
                                              srp_erp_mfq_job jobmastertbl
                                              LEFT JOIN srp_erp_mfq_job linkjob on linkjob.workProcessID=jobmastertbl.linkedJobID
                                              where 
                                              jobmastertbl.companyID = $companyID 
                                              AND jobmastertbl.workProcessID IN ($jobfilter)")->result_array();

                $jobrefno = str_replace('|', PHP_EOL, join("|", array_unique(array_column($ponumber, 'documentCode'))));
            }
            $data['jobreferenceNo'] = $jobrefno;
            $data['companyID'] = $companyID;
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_mfq_deliverynote', $data);
            $last_id = $this->db->insert_id();

            foreach ($jobID AS $job) {
               // $qty = $this->db->query("SELECT srp_erp_mfq_job.poNumber, qty, IFNULL( deliveredQty, 0 ) AS deliveredQty, (qty - IFNULL(deliveredQty, 0)) AS requestedDeliveredQty FROM srp_erp_mfq_job LEFT JOIN ( SELECT SUM(deliveredQty) AS deliveredQty, jobID FROM srp_erp_mfq_deliverynotedetail GROUP BY jobID ) delivered ON delivered.jobID = srp_erp_mfq_job.workProcessID LEFT JOIN srp_erp_mfq_estimatedetail estd ON estd.estimateDetailID = srp_erp_mfq_job.estimateDetailID LEFT JOIN srp_erp_mfq_estimatemaster estm ON estd.estimateMasterID = estm.estimateMasterID WHERE workProcessID = {$job} AND srp_erp_mfq_job.companyID = {$companyID}")->row_array();
    
               $items = $this->db->where('workProcessID',$job)->from('srp_erp_mfq_joboutputitems')->get()->result_array();

                foreach($items as $item){

                    $det['deliveryNoteID'] = $last_id;
                    $det['jobID'] = $job;
                    $det['requestedDeliveredQty'] = $item['qty'];
                    $det['deliveredQty'] = $item['qty'];
                    $det['mfqItemID'] = $item['mfqItemID'];
                    $det['mfqItemName'] = $item['mfqItemDescription'];
                    $det['poNumberDN'] = '';
                    $det['companyID'] = current_companyID();
                    $det['createdUserName'] = current_user();
                    $det['createdPCID'] = current_pc();
                    $det['createdUserID'] = current_userID();
                    $det['createdDateTime'] = current_date();

                    $this->db->insert('srp_erp_mfq_deliverynotedetail', $det);
                }
                // $det['deliveryNoteID'] = $last_id;
                // $det['jobID'] = $job;
                // $det['requestedDeliveredQty'] = $qty['requestedDeliveredQty'];
                // $det['deliveredQty'] = $qty['requestedDeliveredQty'];
                // $det['poNumberDN'] = $qty['poNumber'];
                // $det['companyID'] = current_companyID();
                // $det['createdUserName'] = current_user();
                // $det['createdPCID'] = current_pc();
                // $det['createdUserID'] = current_userID();
                // $det['createdDateTime'] = current_date();

               // $this->db->insert('srp_erp_mfq_deliverynotedetail', $det);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Delivery Note Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Delivery Note Saved Successfully.', $last_id);

            }
        }
    }

    function load_delivery_note_header()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $this->db->select('*,DATE_FORMAT(deliveryDate,\'' . $convertFormat . '\') AS deliveryDate');
        $this->db->from('srp_erp_mfq_deliverynote');
        $this->db->where('deliverNoteID', trim($this->input->post('deliverNoteID') ?? ''));
        $data['header'] = $this->db->get()->row_array();

        $this->db->select('jobID');
        $this->db->from('srp_erp_mfq_deliverynotedetail');
        $this->db->where('deliveryNoteID', trim($this->input->post('deliverNoteID') ?? ''));
        $jobs = $this->db->get()->result_array();
        $data['jobs'] = $jobs;

        $this->db->select('jobID,linkedJobID');
        $this->db->from('srp_erp_mfq_deliverynotedetail dnDetailTbl');
        $this->db->join('srp_erp_mfq_job jobTbl','dnDetailTbl.jobID = jobTbl.workProcessID','left');
        $this->db->where('dnDetailTbl.companyID',$companyID);
        $this->db->where('deliveryNoteID', trim($this->input->post('deliverNoteID') ?? ''));
        $jobs_drop = $this->db->get()->result_array();
        $data['jobs_drop'] = $jobs_drop;

        $main_job_id = array_column( $data['jobs_drop'],'linkedJobID');
        $data['main_job_id']  = isset($main_job_id[0]) ? $main_job_id[0] : '';




        return $data;
    }

    function delivery_note_confirmation()
    {

        $this->db->trans_start();
        $deliverNoteID = trim($this->input->post('deliverNoteID') ?? '');
        $this->db->select('*');
        $this->db->where('deliverNoteID', $deliverNoteID);
        $this->db->from('srp_erp_mfq_deliverynote');
        $row = $this->db->get()->row_array();
        if (!empty($row['confirmedYN'] == 1)) {
            return array('w', 'Document already confirmed');
        } else {
            $companyID = current_companyID();
            $this->db->select('estimateCode');
            $this->db->from('srp_erp_mfq_deliverynotedetail');
            $this->db->join('srp_erp_mfq_job job', 'job.workProcessID = srp_erp_mfq_deliverynotedetail.jobID', 'left');
            $this->db->join('srp_erp_mfq_estimatedetail estd', 'estd.estimateDetailID = job.estimateDetailID', 'left');
            $this->db->join('srp_erp_mfq_estimatemaster estm', 'estd.estimateMasterID = estm.estimateMasterID', 'left');
            $this->db->where('srp_erp_mfq_deliverynotedetail.deliveryNoteID', $deliverNoteID);
            $this->db->where('estm.orderStatus', 1);
            $confirmedOrder = $this->db->get()->row_array();
            if (!empty($confirmedOrder)) {
                return array('e', 'Confirm All orders before confirming the document!');
            }

            $validate_code = validate_code_duplication($row['deliveryNoteCode'], 'deliveryNoteCode', $deliverNoteID, 'deliverNoteID', 'srp_erp_mfq_deliverynote');
            if (!empty($validate_code)) {
                return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
            }

            $mfqsegmentIDLinked = $this->db->query("select segmentID from srp_erp_mfq_segment WHERE companyID = $companyID AND mfqSegmentID = {$row['mfqSegmentID']}")->row('segmentID');
            if (($mfqsegmentIDLinked == '') || empty($mfqsegmentIDLinked)) {
                return array('e', 'Manufacturing segment not linked with ERP segment');
            }

            $itemQtyValidation = $this->db->query("SELECT
                                    deliveredQty,
                                    srp_erp_mfq_job.mfqItemID,
                                    IFNULL( current.currentStock, 0 ) AS currentStock,
                                    srp_erp_mfq_itemmaster.itemAutoID,
                                    srp_erp_mfq_itemmaster.itemDescription,
                                    srp_erp_mfq_itemmaster.itemSystemCode 
                                FROM
                                    srp_erp_mfq_deliverynotedetail
                                    JOIN srp_erp_mfq_job ON srp_erp_mfq_job.workProcessID = jobID
                                    LEFT JOIN srp_erp_mfq_warehousemaster ON srp_erp_mfq_warehousemaster.mfqWarehouseAutoID = srp_erp_mfq_job.mfqWarehouseAutoID
                                    LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID
                                    LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_mfq_itemmaster.itemAutoID
                                    LEFT JOIN ( SELECT SUM( transactionQTY / convertionRate ) AS currentStock, wareHouseAutoID, itemAutoID FROM srp_erp_itemledger WHERE companyID = {$companyID} GROUP BY wareHouseAutoID, itemAutoID ) current ON current.ItemAutoID = srp_erp_mfq_itemmaster.itemAutoID 
                                    AND current.wareHouseAutoID = srp_erp_mfq_warehousemaster.warehouseAutoID 
                                WHERE
                                    deliveryNoteID = $deliverNoteID AND srp_erp_itemmaster.mainCategory = 'Inventory' AND deliveredQty > IFNULL( current.currentStock, 0 )")->result_array();

            if (!empty($itemQtyValidation)) {
                return array('e', 'Item Quantity Insufficient!', $itemQtyValidation);
            }

            $this->db->select('srp_erp_mfq_job.*,deliveredQty, customerAutoID,customerSystemCode,customerName,seg.segmentID,seg.segmentCode,itm.*,wh.*, IFNULL(ledger.companyLocalAmount/qty, 0) AS localWacAmount, IFNULL( ledger.companyReportingAmount/qty, 0 ) AS reportingWacAmount');
            $this->db->where('deliveryNoteID', $deliverNoteID);
            $this->db->join("(SELECT srp_erp_segment.segmentCode,srp_erp_mfq_segment.segmentID,mfqSegmentID FROM srp_erp_mfq_segment LEFT JOIN srp_erp_segment ON srp_erp_mfq_segment.segmentID = srp_erp_segment.segmentID) seg", "srp_erp_mfq_job.mfqSegmentID=seg.mfqSegmentID", "left");
            $this->db->join("(SELECT srp_erp_customermaster.*,mfqCustomerAutoID FROM srp_erp_mfq_customermaster LEFT JOIN srp_erp_customermaster ON srp_erp_mfq_customermaster.CustomerAutoID = srp_erp_customermaster.CustomerAutoID) cust", "srp_erp_mfq_job.mfqCustomerAutoID=cust.mfqCustomerAutoID", "INNER");
            $this->db->join('(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,srp_erp_mfq_itemmaster.mfqItemID as itemID,srp_erp_itemmaster.itemAutoID,
                        srp_erp_itemmaster.itemSystemCode, srp_erp_itemmaster.itemDescription, srp_erp_itemmaster.defaultUnitOfMeasureID, srp_erp_itemmaster.defaultUnitOfMeasure, srp_erp_itemmaster.currentStock, srp_erp_itemmaster.mainCategory, srp_erp_itemmaster.costGLAutoID, srp_erp_itemmaster.costSystemGLCode, srp_erp_itemmaster.costGLCode, srp_erp_itemmaster.costDescription,
                        srp_erp_itemmaster.costType, srp_erp_itemmaster.assteGLAutoID, srp_erp_itemmaster.assteSystemGLCode, srp_erp_itemmaster.assteGLCode, srp_erp_itemmaster.assteDescription, srp_erp_itemmaster.assteType
                        FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_itemmaster.assteGLAutoID = srp_erp_chartofaccounts.GLAutoID) itm', 'itm.itemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
            $this->db->join("(SELECT srp_erp_warehousemaster.wareHouseAutoID,srp_erp_warehousemaster.wareHouseCode,srp_erp_warehousemaster.wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription,mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID) wh", "wh.mfqWarehouseAutoID=srp_erp_mfq_job.mfqWarehouseAutoID", "left");
            $this->db->join('(SELECT SUM(companyReportingAmount) AS companyReportingAmount, SUM(companyLocalAmount) AS companyLocalAmount, documentSystemCode, documentAutoID, itemAutoID FROM srp_erp_itemledger WHERE documentCode = "JOB" GROUP BY documentAutoID, itemAutoID) ledger', "ledger.documentAutoID = srp_erp_mfq_job.workProcessID AND ledger.itemAutoID = itm.itemAutoID", "left");
            $this->db->join('srp_erp_mfq_deliverynotedetail', "srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID", "inner");
            $master = $this->db->get('srp_erp_mfq_job')->result_array();

            $item_arr2 = array();
            $itemledger_arr2 = array();
            if (!empty($master)) {
                foreach ($master as $mas) {
                    if ($mas['mainCategory'] == 'Inventory') {
                        $itemAutoID = $mas['itemAutoID'];
                        $qty = $mas['qty'] / 1;
                        $wareHouseAutoID = $mas['wareHouseAutoID'];
                        $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                        $item_arr['itemAutoID'] = $mas['itemAutoID'];
                        $item_arr['currentStock'] = ($mas['currentStock'] - $qty);

                        $itemledger_arr['documentID'] = $mas['documentID'];
                        $itemledger_arr['documentCode'] = $mas['documentID'];
                        $itemledger_arr['documentAutoID'] = $mas['workProcessID'];
                        $itemledger_arr['documentSystemCode'] = $mas['documentCode'];
                        $itemledger_arr['documentDate'] = $mas['closedDate'];
                        $itemledger_arr['referenceNumber'] = null;
                        $itemledger_arr['companyFinanceYearID'] = $mas['companyFinanceYearID'];
                        $itemledger_arr['companyFinanceYear'] = $mas['companyFinanceYear'];
                        $itemledger_arr['FYBegin'] = $mas['FYBegin'];
                        $itemledger_arr['FYEnd'] = $mas['FYEnd'];
                        $itemledger_arr['FYPeriodDateFrom'] = $mas['FYPeriodDateFrom'];
                        $itemledger_arr['FYPeriodDateTo'] = $mas['FYPeriodDateTo'];
                        $itemledger_arr['wareHouseAutoID'] = $mas['wareHouseAutoID'];
                        $itemledger_arr['wareHouseCode'] = $mas['wareHouseCode'];
                        $itemledger_arr['wareHouseLocation'] = $mas['wareHouseLocation'];
                        $itemledger_arr['wareHouseDescription'] = $mas['wareHouseDescription'];
                        $itemledger_arr['itemAutoID'] = $mas['itemAutoID'];
                        $itemledger_arr['itemSystemCode'] = $mas['itemSystemCode'];
                        $itemledger_arr['itemDescription'] = $mas['itemDescription'];
                        $itemledger_arr['defaultUOMID'] = $mas['defaultUnitOfMeasureID'];
                        $itemledger_arr['defaultUOM'] = $mas['defaultUnitOfMeasure'];
                        $itemledger_arr['transactionUOM'] = $mas['defaultUnitOfMeasure'];
                        $itemledger_arr['transactionUOMID'] = $mas['defaultUnitOfMeasureID'];
                        $itemledger_arr['transactionQTY'] = $mas['qty'];
                        $itemledger_arr['convertionRate'] = 1;
                        $itemledger_arr['currentStock'] = $item_arr['currentStock'];
                        $itemledger_arr['PLGLAutoID'] = $mas['costGLAutoID'];
                        $itemledger_arr['PLSystemGLCode'] = $mas['costSystemGLCode'];
                        $itemledger_arr['PLGLCode'] = $mas['costGLCode'];
                        $itemledger_arr['PLDescription'] = $mas['costDescription'];
                        $itemledger_arr['PLType'] = $mas['costType'];
                        $itemledger_arr['BLGLAutoID'] = $mas['assteGLAutoID'];
                        $itemledger_arr['BLSystemGLCode'] = $mas['assteSystemGLCode'];
                        $itemledger_arr['BLGLCode'] = $mas['assteGLCode'];
                        $itemledger_arr['BLDescription'] = $mas['assteDescription'];
                        $itemledger_arr['BLType'] = $mas['assteType'];
                        $itemledger_arr['transactionAmount'] = $mas['localWacAmount'] * $mas['deliveredQty'];
                        $itemledger_arr['transactionCurrencyID'] = $mas['transactionCurrencyID'];
                        $itemledger_arr['transactionCurrency'] = $mas['transactionCurrency'];
                        $itemledger_arr['transactionExchangeRate'] = $mas['transactionExchangeRate'];
                        $itemledger_arr['transactionCurrencyDecimalPlaces'] = $mas['transactionCurrencyDecimalPlaces'];
                        $itemledger_arr['companyLocalCurrencyID'] = $mas['companyLocalCurrencyID'];
                        $itemledger_arr['companyLocalCurrency'] = $mas['companyLocalCurrency'];
                        $itemledger_arr['companyLocalExchangeRate'] = $mas['companyLocalExchangeRate'];
                        $itemledger_arr['companyLocalCurrencyDecimalPlaces'] = $mas['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr['companyLocalAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyLocalExchangeRate']), $itemledger_arr['companyLocalCurrencyDecimalPlaces']);
                        $itemledger_arr['companyLocalWacAmount'] = $mas['localWacAmount'];
                        $itemledger_arr['companyReportingCurrencyID'] = $mas['companyReportingCurrencyID'];
                        $itemledger_arr['companyReportingCurrency'] = $mas['companyReportingCurrency'];
                        $itemledger_arr['companyReportingExchangeRate'] = $mas['companyReportingExchangeRate'];
                        $itemledger_arr['companyReportingCurrencyDecimalPlaces'] = $mas['companyReportingCurrencyDecimalPlaces'];
                        $itemledger_arr['companyReportingAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyReportingExchangeRate']), $itemledger_arr['companyReportingCurrencyDecimalPlaces']);
                        $itemledger_arr['companyReportingWacAmount'] = $mas['reportingWacAmount'];
                        $itemledger_arr['partyCurrencyID'] = $mas['mfqCustomerCurrencyID'];
                        $itemledger_arr['partyCurrency'] = $mas['mfqCustomerCurrency'];
                        $itemledger_arr['partyCurrencyExchangeRate'] = 1;
                        $itemledger_arr['partyCurrencyDecimalPlaces'] = $mas['mfqCustomerCurrencyDecimalPlaces'];
                        $itemledger_arr['partyCurrencyAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['partyCurrencyExchangeRate']), $itemledger_arr['partyCurrencyDecimalPlaces']);
                        $itemledger_arr['confirmedYN'] = $mas['confirmedYN'];
                        $itemledger_arr['confirmedByEmpID'] = $mas['confirmedByEmpID'];
                        $itemledger_arr['confirmedByName'] = $mas['confirmedByName'];
                        $itemledger_arr['confirmedDate'] = $mas['confirmedDate'];
                        $itemledger_arr['segmentID'] = $mas['segmentID'];
                        $itemledger_arr['segmentCode'] = $mas['segmentCode'];
                        $itemledger_arr['companyID'] = $mas['companyID'];
                        $itemledger_arr['createdUserGroup'] = $mas['createdUserGroup'];
                        $itemledger_arr['createdPCID'] = $mas['createdPCID'];
                        $itemledger_arr['createdUserID'] = $mas['createdUserID'];
                        $itemledger_arr['createdDateTime'] = $mas['createdDateTime'];
                        $itemledger_arr['createdUserName'] = $mas['createdUserName'];
                        $itemledger_arr['modifiedPCID'] = $mas['modifiedPCID'];
                        $itemledger_arr['modifiedUserID'] = $mas['modifiedUserID'];
                        $itemledger_arr['modifiedDateTime'] = $mas['modifiedDateTime'];
                        $itemledger_arr['modifiedUserName'] = $mas['modifiedUserName'];

                        if (!empty($item_arr)) {
                            $item_arr2[] = $item_arr;
                        }
                        if (!empty($itemledger_arr)) {
                            $itemledger_arr2[] = $itemledger_arr;
                        }
                    }
                }
                if (!empty($item_arr2)) {
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr2, 'itemAutoID');
                }
                if (!empty($itemledger_arr2)) {
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr2);
                }
            }

            $this->db->set('confirmedYN', 1);
            $this->db->set('confirmedByEmpID', current_userID());
            $this->db->set('confirmedByName', current_user());
            $this->db->set('confirmedDate', current_date(false));
            $this->db->where('deliverNoteID', $deliverNoteID);
            $this->db->update('srp_erp_mfq_deliverynote');

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Delivery Note Confirmed Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Delivery Note: Confirmed Successfully');
            }
        }
    }

    function delivery_note_confirmation_test()
    {

        $this->db->trans_start();
        $deliverNoteID = trim($this->input->post('deliverNoteID') ?? '');
        $this->db->select('*');
        $this->db->where('deliverNoteID', $deliverNoteID);
        $this->db->from('srp_erp_mfq_deliverynote');
        $row = $this->db->get()->row_array();
        if (!empty($row['confirmedYN'] == 1)) {
            return array('w', 'Document already confirmed');
        } else {
            $companyID = current_companyID();
            $this->db->select('estimateCode');
            $this->db->from('srp_erp_mfq_deliverynotedetail');
            $this->db->join('srp_erp_mfq_job job', 'job.workProcessID = srp_erp_mfq_deliverynotedetail.jobID', 'left');
            $this->db->join('srp_erp_mfq_estimatedetail estd', 'estd.estimateDetailID = job.estimateDetailID', 'left');
            $this->db->join('srp_erp_mfq_estimatemaster estm', 'estd.estimateMasterID = estm.estimateMasterID', 'left');
            $this->db->where('srp_erp_mfq_deliverynotedetail.deliveryNoteID', $deliverNoteID);
            $this->db->where('estm.orderStatus', 1);
            $confirmedOrder = $this->db->get()->row_array();
            if (!empty($confirmedOrder)) {
                return array('e', 'Confirm All orders before confirming the document!');
            }

            $validate_code = validate_code_duplication($row['deliveryNoteCode'], 'deliveryNoteCode', $deliverNoteID, 'deliverNoteID', 'srp_erp_mfq_deliverynote');
            if (!empty($validate_code)) {
                return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
            }

            $mfqsegmentIDLinked = $this->db->query("select segmentID from srp_erp_mfq_segment WHERE companyID = $companyID AND mfqSegmentID = {$row['mfqSegmentID']}")->row('segmentID');
            if (($mfqsegmentIDLinked == '') || empty($mfqsegmentIDLinked)) {
                return array('e', 'Manufacturing segment not linked with ERP segment');
            }

            $this->db->select('srp_erp_mfq_job.*,customerAutoID,customerSystemCode,customerName,seg.segmentID,seg.segmentCode,itm.*,wh.*');
            $this->db->where('srp_erp_mfq_job.workProcessID', $row['jobID']);
            $this->db->join("(SELECT srp_erp_segment.segmentCode,srp_erp_mfq_segment.segmentID,mfqSegmentID FROM srp_erp_mfq_segment LEFT JOIN srp_erp_segment ON srp_erp_mfq_segment.segmentID = srp_erp_segment.segmentID) seg", "srp_erp_mfq_job.mfqSegmentID=seg.mfqSegmentID", "left");
            $this->db->join("(SELECT srp_erp_customermaster.*,mfqCustomerAutoID FROM srp_erp_mfq_customermaster LEFT JOIN srp_erp_customermaster ON srp_erp_mfq_customermaster.CustomerAutoID = srp_erp_customermaster.CustomerAutoID) cust", "srp_erp_mfq_job.mfqCustomerAutoID=cust.mfqCustomerAutoID", "INNER");
            $this->db->join('(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,srp_erp_mfq_itemmaster.mfqItemID as itemID,srp_erp_itemmaster.itemAutoID,
                        srp_erp_itemmaster.itemSystemCode,
                        srp_erp_itemmaster.itemDescription,
                        srp_erp_itemmaster.defaultUnitOfMeasureID,
                        srp_erp_itemmaster.defaultUnitOfMeasure,
                        srp_erp_itemmaster.currentStock,
                        srp_erp_itemmaster.mainCategory,
                        srp_erp_itemmaster.costGLAutoID,
                        srp_erp_itemmaster.costSystemGLCode,
                        srp_erp_itemmaster.costGLCode,
                        srp_erp_itemmaster.costDescription,
                        srp_erp_itemmaster.costType,
                        srp_erp_itemmaster.assteGLAutoID,
                        srp_erp_itemmaster.assteSystemGLCode,
                        srp_erp_itemmaster.assteGLCode,
                        srp_erp_itemmaster.assteDescription,
                        srp_erp_itemmaster.assteType, 
                        srp_erp_itemmaster.companyLocalWacAmount, 
                        srp_erp_itemmaster.companyReportingWacAmount 
                        FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_itemmaster.assteGLAutoID = srp_erp_chartofaccounts.GLAutoID) itm', 'itm.itemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
            $this->db->join("(SELECT srp_erp_warehousemaster.wareHouseAutoID,srp_erp_warehousemaster.wareHouseCode,srp_erp_warehousemaster.wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription,mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID) wh", "wh.mfqWarehouseAutoID=srp_erp_mfq_job.mfqWarehouseAutoID", "left");
            $master = $this->db->get('srp_erp_mfq_job')->row_array();

            if ($master['mainCategory'] == 'Inventory') {
                $itemAutoID = $master['itemAutoID'];
                $qty = $master['qty'] / 1;
                $wareHouseAutoID = $master['wareHouseAutoID'];
                $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                $item_arr['itemAutoID'] = $master['itemAutoID'];
                $item_arr['currentStock'] = ($master['currentStock'] - $qty);

                $itemledger_arr['documentID'] = $master['documentID'];
                $itemledger_arr['documentCode'] = $master['documentID'];
                $itemledger_arr['documentAutoID'] = $master['workProcessID'];
                $itemledger_arr['documentSystemCode'] = $master['documentCode'];
                $itemledger_arr['documentDate'] = $master['closedDate'];
                $itemledger_arr['referenceNumber'] = null;
                $itemledger_arr['companyFinanceYearID'] = $master['companyFinanceYearID'];
                $itemledger_arr['companyFinanceYear'] = $master['companyFinanceYear'];
                $itemledger_arr['FYBegin'] = $master['FYBegin'];
                $itemledger_arr['FYEnd'] = $master['FYEnd'];
                $itemledger_arr['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                $itemledger_arr['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                $itemledger_arr['wareHouseAutoID'] = $master['wareHouseAutoID'];
                $itemledger_arr['wareHouseCode'] = $master['wareHouseCode'];
                $itemledger_arr['wareHouseLocation'] = $master['wareHouseLocation'];
                $itemledger_arr['wareHouseDescription'] = $master['wareHouseDescription'];
                $itemledger_arr['itemAutoID'] = $master['itemAutoID'];
                $itemledger_arr['itemSystemCode'] = $master['itemSystemCode'];
                $itemledger_arr['itemDescription'] = $master['itemDescription'];
                $itemledger_arr['defaultUOMID'] = $master['defaultUnitOfMeasureID'];
                $itemledger_arr['defaultUOM'] = $master['defaultUnitOfMeasure'];
                $itemledger_arr['transactionUOM'] = $master['defaultUnitOfMeasure'];
                $itemledger_arr['transactionUOMID'] = $master['defaultUnitOfMeasureID'];
                $itemledger_arr['transactionQTY'] = $master['qty'];
                $itemledger_arr['convertionRate'] = 1;
                $itemledger_arr['currentStock'] = $item_arr['currentStock'];
                $itemledger_arr['PLGLAutoID'] = $master['costGLAutoID'];
                $itemledger_arr['PLSystemGLCode'] = $master['costSystemGLCode'];
                $itemledger_arr['PLGLCode'] = $master['costGLCode'];
                $itemledger_arr['PLDescription'] = $master['costDescription'];
                $itemledger_arr['PLType'] = $master['costType'];
                $itemledger_arr['BLGLAutoID'] = $master['assteGLAutoID'];
                $itemledger_arr['BLSystemGLCode'] = $master['assteSystemGLCode'];
                $itemledger_arr['BLGLCode'] = $master['assteGLCode'];
                $itemledger_arr['BLDescription'] = $master['assteDescription'];
                $itemledger_arr['BLType'] = $master['assteType'];
                $itemledger_arr['transactionAmount'] = $master['companyLocalWacAmount'] * $master['qty'];
                $itemledger_arr['transactionCurrencyID'] = $master['transactionCurrencyID'];
                $itemledger_arr['transactionCurrency'] = $master['transactionCurrency'];
                $itemledger_arr['transactionExchangeRate'] = $master['transactionExchangeRate'];
                $itemledger_arr['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                $itemledger_arr['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                $itemledger_arr['companyLocalCurrency'] = $master['companyLocalCurrency'];
                $itemledger_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $itemledger_arr['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                $itemledger_arr['companyLocalAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyLocalExchangeRate']), $itemledger_arr['companyLocalCurrencyDecimalPlaces']);
                $itemledger_arr['companyLocalWacAmount'] = $master['companyLocalWacAmount'];
                $itemledger_arr['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                $itemledger_arr['companyReportingCurrency'] = $master['companyReportingCurrency'];
                $itemledger_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $itemledger_arr['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                $itemledger_arr['companyReportingAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyReportingExchangeRate']), $itemledger_arr['companyReportingCurrencyDecimalPlaces']);
                $itemledger_arr['companyReportingWacAmount'] = $master['companyReportingWacAmount'];
                $itemledger_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                $itemledger_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                $itemledger_arr['partyCurrencyExchangeRate'] = 1;
                $itemledger_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                $itemledger_arr['partyCurrencyAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['partyCurrencyExchangeRate']), $itemledger_arr['partyCurrencyDecimalPlaces']);
                $itemledger_arr['confirmedYN'] = $master['confirmedYN'];
                $itemledger_arr['confirmedByEmpID'] = $master['confirmedByEmpID'];
                $itemledger_arr['confirmedByName'] = $master['confirmedByName'];
                $itemledger_arr['confirmedDate'] = $master['confirmedDate'];
                $itemledger_arr['segmentID'] = $master['segmentID'];
                $itemledger_arr['segmentCode'] = $master['segmentCode'];
                $itemledger_arr['companyID'] = $master['companyID'];
                $itemledger_arr['createdUserGroup'] = $master['createdUserGroup'];
                $itemledger_arr['createdPCID'] = $master['createdPCID'];
                $itemledger_arr['createdUserID'] = $master['createdUserID'];
                $itemledger_arr['createdDateTime'] = $master['createdDateTime'];
                $itemledger_arr['createdUserName'] = $master['createdUserName'];
                $itemledger_arr['modifiedPCID'] = $master['modifiedPCID'];
                $itemledger_arr['modifiedUserID'] = $master['modifiedUserID'];
                $itemledger_arr['modifiedDateTime'] = $master['modifiedDateTime'];
                $itemledger_arr['modifiedUserName'] = $master['modifiedUserName'];

                if (!empty($item_arr)) {
                    $item_arr2[] = $item_arr;
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr2, 'itemAutoID');
                }

                if (!empty($itemledger_arr)) {
                    $itemledger_arr2[] = $itemledger_arr;
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr2);
                }
            }

            $this->db->set('confirmedYN', 1);
            $this->db->set('confirmedByEmpID', current_userID());
            $this->db->set('confirmedByName', current_user());
            $this->db->set('confirmedDate', current_date(false));
            $this->db->where('deliverNoteID', $deliverNoteID);
            $this->db->update('srp_erp_mfq_deliverynote');

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Delivery Note Confirmed Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Delivery Note: Confirmed Successfully');
            }
        }
    }

    function referback_delivery_note()
    {
        $deliverNoteID = trim($this->input->post('deliverNoteID') ?? '');
        $dataUpdate = array(
            'confirmedYN' => 0,
            'confirmedByEmpID' => '',
            'confirmedByName' => '',
            'confirmedDate' => ''
        );
        $this->db->where('deliverNoteID', $deliverNoteID);
        $status = $this->db->update('srp_erp_mfq_deliverynote', $dataUpdate);
        if ($status) {
            return array('s', ' Referred Back Successfully.');
        } else {
            return array('e', ' Error in refer back.');
        }
    }

    function delete_delivery_note()
    {
        $deliverNoteID = trim($this->input->post('deliverNoteID') ?? '');

        $this->db->select('*');
        $this->db->from('srp_erp_mfq_deliverynotedetail');
        $this->db->where('companyID', current_companyID());
        $this->db->where('deliveryNoteID', $deliverNoteID);
        $details = $this->db->get()->result_array();
        if (!empty($details)) {
            return array('w', 'Delete All Details before deleting the Delivery Note!');
        } else {
            $this->db->delete('srp_erp_mfq_deliverynote', array('deliverNoteID' => $deliverNoteID));
            return array('s', 'Delivery Note Deleted Successfully');
        }
    }

    function deliveryNote_confirmation_details($deliverNoteID)
    {
        $companyID = current_companyID();
        $data = $this->db->query("SELECT
                    deliveryNoteDetailID AS deliveryNoteDetailID, estd.estimateMasterID AS estimateMasterID,
                    requestedDeliveredQty AS detailQty,
                    deliveredQty AS deliveredQty,
                    -- item.itemName AS itemName,
                    IFNULL(srp_erp_mfq_deliverynotedetail.mfqItemName, item.itemName) AS itemName,
                    srp_erp_mfq_deliverynotedetail.poNumberDN AS estmPoNumber,
                    masterjob.documentCode AS documentCode, estm.orderStatus AS orderStatus
                FROM
                    srp_erp_mfq_deliverynotedetail
                    LEFT JOIN srp_erp_mfq_job job ON job.workProcessID = srp_erp_mfq_deliverynotedetail.jobID
                    LEFT JOIN srp_erp_mfq_job masterjob ON masterjob.levelNo = 1 AND masterjob.workProcessID = job.linkedJobID
                    LEFT JOIN srp_erp_mfq_itemmaster item ON item.mfqItemID = job.mfqItemID
                    LEFT JOIN srp_erp_mfq_estimatedetail estd ON estd.estimateDetailID = job.estimateDetailID
                    LEFT JOIN srp_erp_mfq_estimatemaster estm ON estd.estimateMasterID = estm.estimateMasterID 
                WHERE
                    srp_erp_mfq_deliverynotedetail.companyID = {$companyID} 
                    AND srp_erp_mfq_deliverynotedetail.deliveryNoteID = {$deliverNoteID}")->result_array();

        return $data;
    }

    function save_delivery_note_details()
    {
        $this->db->trans_start();
        $deliverNoteID = trim($this->input->post('deliverNoteID') ?? '');
        $deliveryNoteDetailIDs = $this->input->post('deliveryNoteDetailID');
        $deliveredQty = $this->input->post('deliveredQty');

        foreach ($deliveryNoteDetailIDs as $key => $deliveryNoteDetailID) {
            $data['deliveredQty'] = $deliveredQty[$key];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('deliveryNoteDetailID', $deliveryNoteDetailID);
            $this->db->where('deliveryNoteID', $deliverNoteID);
            $this->db->where('companyID', current_companyID());
            $this->db->update('srp_erp_mfq_deliverynotedetail', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Delivery Note Details Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Delivery Note Details Updated Successfully.');
        }
    }

    function delete_delivery_note_detail()
    {
        $deliveryNoteDetailID = trim($this->input->post('deliveryNoteDetailID') ?? '');
        $this->db->delete('srp_erp_mfq_deliverynotedetail', array('deliveryNoteDetailID' => $deliveryNoteDetailID));
        return array('s', 'Delivery Note Detail Deleted Successfully');
    }

    function fetch_delivery_note_details()
    {
        $data = array();
        $where = '';
        $companyID = current_companyID();
        $mfqCustomerAutoID = $this->input->post('mfqCustomerAutoID');
        if (!empty($mfqCustomerAutoID)) {
            $where .= " AND srp_erp_mfq_deliverynote.mfqCustomerAutoID = {$mfqCustomerAutoID}";
        }
        $DepartmentID = $this->input->post('DepartmentID');
        if (!empty($DepartmentID)) {
            $where .= " AND srp_erp_mfq_deliverynote.mfqSegmentID = {$DepartmentID}";
        }
        $date_format_policy = date_format_policy();
        $DeliveryDateFrom = $this->input->post('DeliveryDateFrom');
        $DeliveryDateTo = $this->input->post('DeliveryDateTo');
        $date_from_convert = input_format_date($DeliveryDateFrom, $date_format_policy);
        $date_to_convert = input_format_date($DeliveryDateTo, $date_format_policy);
        if (!empty($DeliveryDateFrom) && !empty($DeliveryDateTo)) {
            $where .= " AND (DATE(deliveryDate) BETWEEN '{$date_from_convert}' AND '{$date_to_convert}')";
        }
        $convertFormat = convert_date_format_sql();

        $result = $this->db->query("SELECT
	srp_erp_mfq_deliverynote.deliverNoteID AS deliverNoteID,
	srp_erp_mfq_deliverynote.deliveryNoteCode AS deliveryNoteCode,
	DATE_FORMAT( deliveryDate, '{$convertFormat}' ) AS deliveryDate,
	srp_erp_mfq_customermaster.CustomerName AS CustomerName,
	srp_erp_mfq_deliverynote.confirmedYN AS confirmedYN,
	IFNULL( mfqsegment.segmentCode, '-' ) AS segment 
FROM srp_erp_mfq_deliverynote
	LEFT JOIN srp_erp_mfq_customermaster ON srp_erp_mfq_customermaster.mfqCustomerAutoID = srp_erp_mfq_deliverynote.mfqCustomerAutoID
	LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = srp_erp_mfq_deliverynote.mfqSegmentID 
WHERE
	srp_erp_mfq_deliverynote.companyID = {$companyID} {$where}
ORDER BY deliverNoteID DESC ")->result_array();

        if ($result) {
            $a = 1;
            foreach ($result AS $val) {
                $det['recordNo'] = $a;
                $det['deliveryNoteCode'] = $val['deliveryNoteCode'];
                $det['segment'] = $val['segment'];
                $det['CustomerName'] = $val['CustomerName'];
                $det['deliveryDate'] = $val['deliveryDate'];
                if ($val['confirmedYN'] == 1) {
                    $det['confirmedYN'] = 'Confirmed';
                } else {
                    $det['confirmedYN'] = 'Not Confirmed';
                }
                $a++;
                array_push($data, $det);
            }
        }

        return $data;
    }

    function save_est_order_status()
    {
        $this->db->trans_start();
        $estimateMasterID = $this->input->post('estimateMasterID');
        $status = $this->input->post('status');

        $data['orderStatus'] = $status;

        $this->db->where('estimateMasterID', $estimateMasterID);
        $this->db->update('srp_erp_mfq_estimatemaster', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Order Status Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Order Status Updated Successfully.');
        }
    }

    function upload_attachment_for_DeliveryNote()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentSystemCode', 'documentSystemCode', 'trim|required');
        $this->form_validation->set_rules('document_name', 'document_name', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {
            $companyID = $this->common_data['company_data']['company_id'];
            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_documentattachments')->result_array();
            $file_name = $this->input->post('documentID') . '_' . $this->input->post('documentSystemCode') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            /** call s3 library */
            $file = $_FILES['document_file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            if (empty($ext)) {
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'No extension found for the selected attachment'));
                exit();
            }
            $cc = current_companyCode();
            $folderPath = !empty($cc) ? $cc . '/' : '';
            if ($this->s3->upload($file['tmp_name'], $folderPath . $file_name . '.' . $ext)) {
                $s3Upload = true;
            } else {
                $s3Upload = false;
            }
            /** end of s3 integration */

            $data['documentID'] = trim($this->input->post('documentID') ?? '');
            $data['documentSystemCode'] = trim($this->input->post('documentSystemCode') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
            $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
            $data['fileType'] = trim($ext);
            $data['fileSize'] = trim($file["size"]);
            $data['timestamp'] = date('Y-m-d H:i:s');
            $data['companyID'] = $companyID;
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_documentattachments', $data);

            $this->db->select('srp_erp_customerinvoicemaster.invoiceAutoID AS erpInvoiceAutoID, srp_erp_mfq_customerinvoicemaster.invoiceAutoID AS mfqInvoiceAutoID');
            $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicemaster.mfqInvoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID', 'LEFT');
            $this->db->where('deliveryNoteID', trim($this->input->post('documentSystemCode') ?? ''));
            $this->db->where('srp_erp_mfq_customerinvoicemaster.confirmedYN', 1);
            $this->db->where('srp_erp_mfq_customerinvoicemaster.companyID', $companyID);
            $invoiceIDs = $this->db->get('srp_erp_mfq_customerinvoicemaster')->row_array();

            if ($invoiceIDs) {
                if ($invoiceIDs['erpInvoiceAutoID']) {
                    $data['documentID'] = 'CINV';
                    $data['documentSystemCode'] = $invoiceIDs['erpInvoiceAutoID'];
                    $data['documentSubID'] = $invoiceIDs['mfqInvoiceAutoID'];
                    $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                    $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
                    $data['fileType'] = trim($ext);
                    $data['fileSize'] = trim($file["size"]);
                    $data['timestamp'] = date('Y-m-d H:i:s');
                    $data['companyID'] = $companyID;
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_documentattachments', $data);
                }
                if ($invoiceIDs['mfqInvoiceAutoID']) {
                    $data['documentID'] = 'MCINV_DN';
                    $data['documentSystemCode'] = trim($invoiceIDs['mfqInvoiceAutoID'] ?? '');
                    $data['documentSubID'] = trim($this->input->post('documentSystemCode') ?? '');
                    $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                    $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
                    $data['fileType'] = trim($ext);
                    $data['fileSize'] = trim($file["size"]);
                    $data['timestamp'] = date('Y-m-d H:i:s');
                    $data['companyID'] = $companyID;
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_documentattachments', $data);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message(), 's3Upload' => $s3Upload);
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $file_name . ' uploaded.', 's3Upload' => $s3Upload);
            }
        }
    }

    function referback_delivery_note_with_validation()
    {
        $deliverNoteID = trim($this->input->post('deliverNoteID') ?? '');

        $q = $this->db->query("select * from srp_erp_mfq_customerinvoicemaster where deliveryNoteID=$deliverNoteID");

        if ($q->num_rows() > 0) {
            return array('e', 'Delivery note linked with a customer invoice.');
        } else {
            $dataUpdate = array(
                'confirmedYN' => 0,
                'confirmedByEmpID' => '',
                'confirmedByName' => '',
                'confirmedDate' => ''
            );
            $this->db->where('deliverNoteID', $deliverNoteID);
            $status = $this->db->update('srp_erp_mfq_deliverynote', $dataUpdate);
            if ($status) {
                return array('s', ' Referred Back Successfully.');
            } else {
                return array('e', ' Error in refer back.');
            }
        }

    }
}