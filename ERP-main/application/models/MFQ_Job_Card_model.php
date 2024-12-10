<?php

class MFQ_Job_Card_model extends ERP_Model
{
    function fetch_material()
    {
        $dataArr = array();
        $dataArr2 = array();
        $companyID = current_companyID();
        $companyLanguage = getPolicyValues('LNG', 'All');
        $packaging = ($this->uri->segment(3)) ? $this->uri->segment(3) : '';

        $search_string = "%" . $_GET['query'] . "%";
        $packaging_filter = '';

        if($packaging == 'PACKAGING'){
            $packaging_filter = ' AND srp_erp_mfq_itemmaster.packagingYN = 1 AND srp_erp_mfq_itemmaster.packagingYN IS NOT NULL';
        }
        

        $sql = 'SELECT mfqCategoryID,mfqSubcategoryID,secondaryItemCode,mfqSubSubCategoryID,itemSystemCode,costGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,mfqItemID as itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice, CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN "RM" WHEN 2 THEN "FG" WHEN 3 THEN "SF"
            END," - ",IFNULL(itemDescription,"")," (",IFNULL(itemSystemCode,""),"(",IFNULL(partNo,""),")") AS "Match",partNo,srp_erp_unit_of_measure.unitDes as uom FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR secondaryItemCode LIKE "' . $search_string . '" OR partNo LIKE "' . $search_string . '") AND srp_erp_mfq_itemmaster.companyID = "' . $companyID . '" AND isActive="1" '.$packaging_filter.'  LIMIT 20';
        
        $data = $this->db->query($sql)->result_array();
      
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'mfqItemID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'uom' => $val['uom'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'partNo' => $val['partNo']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_material_by_id()
    {
        $companyID = current_companyID();
        $mfqItemID = $this->input->post('mfqItemID');
        $sql = 'SELECT *,IFNULL(companyLocalWacAmount,0) as companyLocalWacAmountMod FROM srp_erp_mfq_itemmaster WHERE mfqItemID = ' . $mfqItemID . ' AND srp_erp_mfq_itemmaster.companyID = "' . $companyID . '" AND isActive=1';
        $data = $this->db->query($sql)->row_array();
        return $data;
    }

    function fetch_overhead()
    {
        $dataArr = array();
        $dataArr2 = array();
        $search_string = "%" . $_GET['query'] . "%";
        $companyID = current_companyID();
        $data = $this->db->query('SELECT srp_erp_mfq_overhead.*,CONCAT(IFNULL(description,""), " (" ,IFNULL(overHeadCode,""),")") AS "Match",IFNULL(srp_erp_mfq_segmenthours.hours,0) as hours  FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_mfq_segmenthours ON srp_erp_mfq_overhead.mfqSegmentID = srp_erp_mfq_segmenthours.mfqSegmentID WHERE srp_erp_mfq_overhead.companyID = ' . $companyID .' AND overHeadCategoryID = 1 AND  (overHeadCode LIKE "' . $search_string . '" OR description LIKE "' . $search_string . '")')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['overHeadCode'], 'overHeadID' => $val['overHeadID'], 'description' => $val['description'], 'segment' => $val['mfqSegmentID'], 'rate' => $val['rate'], 'hours' => $val['hours'], 'uom' => $val['unitOfMeasureID']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_machine()
    {
        $dataArr = array();
        $dataArr2 = array();
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT srp_erp_mfq_fa_asset_master.*,CONCAT(IFNULL(assetDescription,""), " (" ,IFNULL(faCode,""),")") AS "Match",IFNULL(srp_erp_mfq_segmenthours.hours,0) as hours,mfqSeg.mfqSegmentID FROM srp_erp_mfq_fa_asset_master LEFT JOIN srp_erp_mfq_category c1 ON mfq_faCatID = c1.itemCategoryID LEFT JOIN srp_erp_mfq_category c2 ON mfq_faSubCatID = c2.itemCategoryID LEFT JOIN srp_erp_mfq_category c3 ON mfq_faSubSubCatID = c3.itemCategoryID LEFT JOIN (SELECT segmentID,mfqSegmentID FROM srp_erp_mfq_segment WHERE companyID = ' . current_companyID() . ') mfqSeg ON mfqSeg.segmentID = srp_erp_mfq_fa_asset_master.segmentID LEFT JOIN srp_erp_mfq_segmenthours ON mfqSeg.mfqSegmentID = srp_erp_mfq_segmenthours.mfqSegmentID WHERE srp_erp_mfq_fa_asset_master.companyID = '. $companyID .' AND (faCode LIKE "' . $search_string . '" OR assetDescription LIKE "' . $search_string . '")')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['faCode'], 'mfq_faID' => $val['mfq_faID'], 'description' => $val['assetDescription'], 'segment' => $val['mfqSegmentID'], 'rate' => $val['unitRate'], 'hours' => $val['hours'], 'uom' => $val['unitOfmeasureID']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_labourTask()
    {
        $dataArr = array();
        $dataArr2 = array();
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT srp_erp_mfq_overhead.*,CONCAT(IFNULL(description,""), " (" ,IFNULL(overHeadCode,""),")") AS "Match",IFNULL(srp_erp_mfq_segmenthours.hours,0) as hours  FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_mfq_segmenthours ON srp_erp_mfq_overhead.mfqSegmentID = srp_erp_mfq_segmenthours.mfqSegmentID  WHERE srp_erp_mfq_overhead.companyID = '. $companyID .' AND overHeadCategoryID = 2 AND (overHeadCode LIKE "' . $search_string . '" OR description LIKE "' . $search_string . '")')->result_array();
        //echo $this->db->last_query();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['overHeadCode'], 'overHeadID' => $val['overHeadID'], 'description' => $val['description'], 'segment' => $val['mfqSegmentID'], 'rate' => $val['rate'], 'hours' => $val['hours'], 'uom' => $val['unitOfMeasureID']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_checklist(){
        $workProcessID = trim($this->input->post('workProcessID') ?? '');

        $job_details = $this->db->where('workProcessID',$workProcessID)->from('srp_erp_mfq_job')->get()->row_array();

        if($job_details){
            $mfq_itemID = $job_details['mfqItemID'];
            $checklist = $this->db->where('mfqItemautoID',$mfq_itemID)->where('workProcessID',$workProcessID)->from('srp_erp_mfq_itemmaster_checklist_values')->get()->result_array();

            return $checklist;
        }

    }

    function fetch_jobcard_material_consumption($isPackaging = null)
    {
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $jobCardID = trim($this->input->post('jobCardID') ?? '');
        $where = "";
        $where_packaging = " AND (srp_erp_mfq_jc_materialconsumption.isPackageYN != 1 OR srp_erp_mfq_jc_materialconsumption.isPackageYN IS NULL)";

        if (isset($_POST["jobCardID"])) {
            $where = "AND jobCardID = $jobCardID";
        }

        if($isPackaging){
            $where_packaging = " AND srp_erp_mfq_jc_materialconsumption.isPackageYN = 1";
        }

        $sql = "SELECT 	srp_erp_mfq_jc_materialconsumption.*,CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN 'RM' WHEN 2 THEN 'FG' WHEN 3 THEN 'SF'
            END,' - ',srp_erp_mfq_itemmaster.itemDescription) as itemDescription,srp_erp_unit_of_measure.unitDes as uom,IFNULL(partNo,'') as partNo,job.confirmedYN,srp_erp_mfq_itemmaster.itemType,job.linkedJobID,job.documentCode FROM srp_erp_mfq_jc_materialconsumption LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_jc_materialconsumption.mfqItemID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID LEFT JOIN (SELECT mfqItemID,confirmedYN,linkedJobID,documentCode FROM srp_erp_mfq_job WHERE linkedJobID = $workProcessID) job ON srp_erp_mfq_jc_materialconsumption.mfqItemID = job.mfqItemID WHERE workProcessID = $workProcessID $where $where_packaging";
        
        $data = $this->db->query($sql)->result_array();
        
        $this->db->where('workProcessID',$workProcessID);
        $job_detail =  $this->db->from('srp_erp_mfq_job')->get()->row_array();

        
        foreach($data as $key=>$value){
            if($job_detail['bomMasterID']){
                $charge_detail = $this->db->where('mfqItemID',$value['mfqItemID'])->where('bomMasterID',$job_detail['bomMasterID'])->from('srp_erp_mfq_bom_materialconsumption')->get()->row_array();
                $data[$key]['bomItemValue'] = $charge_detail['materialCost'];
                $data[$key]['bomItemQty'] = $charge_detail['qtyUsed'];
                $data[$key]['bomItemUnit'] = $charge_detail['unitCost'];
            }else{
                $data[$key]['bomItemValue'] = 0;
                $data[$key]['bomItemQty'] = 0;
                $data[$key]['bomItemUnit'] = 0;
            }
        }
       
        
        return $data;
    }

    function fetch_jobcard_labour_task()
    {
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $jobCardID = trim($this->input->post('jobCardID') ?? '');
        $where = "";
        if (isset($_POST["jobCardID"])) {
            $where = "AND jobCardID = $jobCardID";
        }
        $sql = "SELECT srp_erp_mfq_jc_labourtask.*,srp_erp_mfq_overhead.*,srp_erp_mfq_segment.description as segment,srp_erp_unit_of_measure.unitDes as uom FROM srp_erp_mfq_jc_labourtask LEFT JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_labourtask.labourTask LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_jc_labourtask.segmentID = srp_erp_mfq_segment.mfqSegmentID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = uomID WHERE workProcessID = $workProcessID $where";
        $data = $this->db->query($sql)->result_array();

        $this->db->where('workProcessID',$workProcessID);
        $job_detail =  $this->db->from('srp_erp_mfq_job')->get()->row_array();

        
        foreach($data as $key=>$value){
            if($job_detail['bomMasterID']){
                $charge_detail = $this->db->where('labourTask',$value['labourTask'])->where('bomMasterID',$job_detail['bomMasterID'])->from('srp_erp_mfq_bom_labourtask')->get()->row_array();
                $data[$key]['bomItemValue'] = $charge_detail['totalValue'];
                $data[$key]['bomItemQty'] = $charge_detail['totalHours'];
                $data[$key]['bomItemUnit'] = $charge_detail['hourlyRate'];
            }else{
                $data[$key]['bomItemValue'] = 0;
                $data[$key]['bomItemQty'] = 0;
                $data[$key]['bomItemUnit'] = 0;
            }
        }

        return $data;
    }

    function fetch_jobcard_overhead_cost($type = 1)
    {
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $jobCardID = trim($this->input->post('jobCardID') ?? '');
        $where = "";
        if (isset($_POST["jobCardID"])) {
            $where = "AND jobCardID = $jobCardID";
        }
        $sql = "SELECT srp_erp_mfq_jc_overhead.*,srp_erp_mfq_overhead.*,srp_erp_mfq_segment.description as segment,srp_erp_unit_of_measure.unitDes as uom FROM srp_erp_mfq_jc_overhead LEFT JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_overhead.overHeadID LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_jc_overhead.segmentID = srp_erp_mfq_segment.mfqSegmentID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = uomID WHERE workProcessID = $workProcessID AND srp_erp_mfq_overhead.typeID = '{$type}' $where";
        $data = $this->db->query($sql)->result_array();

        $this->db->where('workProcessID',$workProcessID);
        $job_detail =  $this->db->from('srp_erp_mfq_job')->get()->row_array();

        
        foreach($data as $key=>$value){
            if($job_detail['bomMasterID']){
                $charge_detail = $this->db->where('overheadID',$value['overHeadID'])->where('bomMasterID',$job_detail['bomMasterID'])->from('srp_erp_mfq_bom_overhead')->get()->row_array();
                $data[$key]['bomItemValue'] = $charge_detail['totalValue'];
                $data[$key]['bomItemQty'] = $charge_detail['totalHours'];
                $data[$key]['bomItemUnit'] = $charge_detail['hourlyRate'];
            }else{
                $data[$key]['bomItemValue'] = 0;
                $data[$key]['bomItemQty'] = 0;
                $data[$key]['bomItemUnit'] = 0;
            }
        }

        return $data;
    }

    function fetch_jobcard_thiredParty_cost()
    {
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $jobCardID = trim($this->input->post('jobCardID') ?? '');
        $where = "";
        if (isset($_POST["jobCardID"])) {
            $where = "AND jobCardID = $jobCardID";
        }
        $sql = "SELECT srp_erp_mfq_jc_overhead.*,srp_erp_mfq_overhead.*,srp_erp_mfq_segment.description as segment,srp_erp_unit_of_measure.unitDes as uom FROM srp_erp_mfq_jc_overhead LEFT JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_overhead.overHeadID LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_jc_overhead.segmentID = srp_erp_mfq_segment.mfqSegmentID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = uomID WHERE workProcessID = $workProcessID AND srp_erp_mfq_overhead.typeID = 2 $where";
        $data = $this->db->query($sql)->result_array();

        $this->db->where('workProcessID',$workProcessID);
        $job_detail =  $this->db->from('srp_erp_mfq_job')->get()->row_array();

        
        foreach($data as $key=>$value){
            if($job_detail['bomMasterID']){
                $charge_detail = $this->db->where('overheadID',$value['overHeadID'])->where('bomMasterID',$job_detail['bomMasterID'])->from('srp_erp_mfq_bom_overhead')->get()->row_array();
                $data[$key]['bomItemValue'] = $charge_detail['totalValue'];
                $data[$key]['bomItemQty'] = $charge_detail['totalHours'];
                $data[$key]['bomItemUnit'] = $charge_detail['hourlyRate'];
            }else{
                $data[$key]['bomItemValue'] = 0;
                $data[$key]['bomItemQty'] = 0;
                $data[$key]['bomItemUnit'] = 0;
            }
        }

        return $data;
    }

    function fetch_jobcard_machine_cost()
    {
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $jobCardID = trim($this->input->post('jobCardID') ?? '');
        $where = "";
        if (isset($_POST["jobCardID"])) {
            $where = "AND jobCardID = $jobCardID";
        }
        $sql = "SELECT srp_erp_mfq_jc_machine.*,srp_erp_mfq_fa_asset_master.*,srp_erp_mfq_segment.description as segment,srp_erp_unit_of_measure.unitDes as uom,srp_erp_mfq_jc_machine.segmentID as segment2 FROM srp_erp_mfq_jc_machine LEFT JOIN srp_erp_mfq_fa_asset_master ON srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_jc_machine.mfq_faID LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_jc_machine.segmentID = srp_erp_mfq_segment.mfqSegmentID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = uomID WHERE workProcessID = $workProcessID $where";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }

    function  save_workprocess_jobcard()
    {
        $save = false;
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_job');
        $this->db->where('linkedJobID', $this->input->post('workProcessID'));
        $outputJob = $this->db->get()->result_array();
        $jobCount = count($outputJob);

        $status = $this->input->post('status');
        $templateDetailID = $this->input->post('templateDetailID');
        

        //srp_erp_mfq_job_wise_stage
        if($status == 1){
            $stage_wise = $this->db->where('job_id',$this->input->post('workProcessID'))->where('templateDetailID',$templateDetailID)->where('stage_progress !=','100')->from('srp_erp_mfq_job_wise_stage')->get()->row_array();

            if($stage_wise){
                return array('e', 'Some stages still not Completed.');
            }
        }
        
        if (!empty($outputJob)) {
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_job');
            $this->db->where('linkedJobID', $this->input->post('workProcessID'));
            $this->db->where('closedYN', 1);
            $outputJob = $this->db->get()->result_array();
            $jobClosedCount = count($outputJob);
            if ($jobCount == $jobClosedCount) {
                $save = true;
            } else {
                $save = false;
            }
        } else {
            $save = true;
        }

        if ($save) {
            $last_id = "";
            $this->db->trans_start();
            if (!$this->input->post('jobCardID')) {
                $this->db->set('jobNo', $this->input->post('jobNo'));
                $this->db->set('bomID', $this->input->post('bomID'));
                $this->db->set('quotationRef', $this->input->post('quotationRef'));
                $this->db->set('description', $this->input->post('description'));
                $this->db->set('workProcessID', $this->input->post('workProcessID'));
                $this->db->set('workFlowID', $this->input->post('workFlowID'));
                $this->db->set('templateDetailID', $this->input->post('templateDetailID'));
                $this->db->set('companyID', current_companyID());
                $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserName', current_user());
                $this->db->set('createdDateTime', current_date(true));

                $result = $this->db->insert('srp_erp_mfq_jobcardmaster');
                $last_id = $this->db->insert_id();

                $this->db->set('unitPrice', $this->input->post('unitPrice'));
                $this->db->set('bomMasterID', $this->input->post('bomID'));
                $this->db->where('workProcessID', $this->input->post('workProcessID'));
                $result = $this->db->update('srp_erp_mfq_job');

            } else {
                $last_id = $this->input->post('jobCardID');
                $this->db->set('jobNo', $this->input->post('jobNo'));
                $this->db->set('bomID', $this->input->post('bomID'));
                $this->db->set('quotationRef', $this->input->post('quotationRef'));
                $this->db->set('description', $this->input->post('description'));
                $this->db->set('templateDetailID', $this->input->post('templateDetailID'));
                $this->db->set('companyID', current_companyID());
                $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                $this->db->set('modifiedUserID', current_userID());
                $this->db->set('modifiedUserName', current_user());
                $this->db->set('modifiedDateTime', current_date(true));
                $this->db->where('jobcardID', $this->input->post('jobCardID'));
                $result = $this->db->update('srp_erp_mfq_jobcardmaster');

                $this->db->set('unitPrice', $this->input->post('unitPrice'));
                $this->db->set('bomMasterID', $this->input->post('bomID'));
                $this->db->where('workProcessID', $this->input->post('workProcessID'));
                $result = $this->db->update('srp_erp_mfq_job');
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Job Card Saved Failed ' . $this->db->_error_message());

            } else {

                $jcMaterialConsumptionID = $this->input->post('jcMaterialConsumptionID');
                $mfqItemID = $this->input->post('mfqItemID');
                $batch_selected_arr = $this->input->post('batch');
                $estimated_qty_arr = $this->input->post('estimated_qty');

                if (!empty($mfqItemID)) {
                    foreach ($mfqItemID as $key => $val) {

                        $batch_selected = $batch_selected_arr[$key] ?? null;
                        $estimated_qty = $estimated_qty_arr[$key] ?? 0;
                        $materialCost = $this->input->post('materialCost')[$key] ?? 0;
                        $unitCost =  $this->input->post('unitCost')[$key] ?? 0;
                        
                        if($batch_selected){
                            $this->db->set('batchNumber', $batch_selected);
                            $this->db->set('qtyUsed', $estimated_qty);
                        }else{
                            $estimated_qty = $this->input->post('qtyUsed')[$key] ?? 0;
                            $this->db->set('qtyUsed', $estimated_qty);
                        }

                        if($materialCost == 0){
                            $materialCost = $unitCost * $estimated_qty;
                        }

                        //recerved quantity from batch
                        $res = reserved_quantity_from_item_batch($val,$estimated_qty,$batch_selected,$last_id);


                        if (!empty($jcMaterialConsumptionID[$key])) {
                            $this->db->set('jobCardID', $last_id);
                            $this->db->set('workProcessID', $this->input->post('workProcessID'));

                            $this->db->set('mfqItemID', $this->input->post('mfqItemID')[$key] ?? null);
                            $this->db->set('unitCost', $this->input->post('unitCost')[$key] ?? 0);
                            $this->db->set('materialCost', $materialCost);
                            $this->db->set('usageQty', $this->input->post('usageQty')[$key] ?? 0);
                            $this->db->set('markUp', $this->input->post('markUp')[$key] ?? 0);
                            $this->db->set('materialCharge', $this->input->post('materialCharge')[$key] ?? 0);
                            
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

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('jcMaterialConsumptionID', $jcMaterialConsumptionID[$key]);
                            $result = $this->db->update('srp_erp_mfq_jc_materialconsumption');
                        } else {
                            if (!empty($mfqItemID[$key])) {
                                $this->db->set('mfqItemID', $this->input->post('mfqItemID')[$key] ?? null);
                                $this->db->set('usageQty', $this->input->post('usageQty')[$key] ?? 0);
                                $this->db->set('unitCost', $this->input->post('unitCost')[$key] ?? 0);
                                $this->db->set('materialCost', $materialCost);
                                $this->db->set('markUp', $this->input->post('markUp')[$key] ?? 0);
                                $this->db->set('materialCharge', $this->input->post('materialCharge')[$key] ?? 0);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('workProcessID', $this->input->post('workProcessID'));
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
                                $result = $this->db->insert('srp_erp_mfq_jc_materialconsumption');
                            }
                        }
                    }
                }


                $jcLabourTaskID = $this->input->post('jcLabourTaskID');
                $labourTask = $this->input->post('labourTask');
                if (!empty($labourTask)) {
                    foreach ($labourTask as $key => $val) {
                        if (!empty($jcLabourTaskID[$key])) {
                            $this->db->set('jobCardID', $last_id);
                            $this->db->set('workProcessID', $this->input->post('workProcessID'));

                            $this->db->set('labourTask', $this->input->post('labourTask')[$key] ?? null);
                            $this->db->set('uomID', isset($this->input->post('la_uomID')[$key]) && $this->input->post('la_uomID')[$key] !== ""
                                ? $this->input->post('la_uomID')[$key]
                                : null);
                            $this->db->set('segmentID', $this->input->post('la_segmentID')[$key] ?? null);
                            $this->db->set('subsegmentID', $this->input->post('la_subsegmentID')[$key] ?? null);
                            $this->db->set('hourlyRate', $this->input->post('la_hourlyRate')[$key] ?? 0);
                            $this->db->set('totalHours', $this->input->post('la_totalHours')[$key] ?? 0);
                            $this->db->set('usageHours', $this->input->post('la_usageHours')[$key] ?? 0);
                            $this->db->set('totalValue', $this->input->post('la_totalValue')[$key] ?? 0);

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

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('jcLabourTaskID', $jcLabourTaskID[$key]);
                            $result = $this->db->update('srp_erp_mfq_jc_labourtask');
                        } else {
                            if (!empty($labourTask[$key])) {
                                $this->db->set('labourTask', $this->input->post('labourTask')[$key] ?? null);
                                $this->db->set('uomID', isset($this->input->post('la_uomID')[$key]) && $this->input->post('la_uomID')[$key] !== ""
                                    ? $this->input->post('la_uomID')[$key]
                                    : null);
                                $this->db->set('segmentID', $this->input->post('la_segmentID')[$key] ?? null);
                                $this->db->set('subsegmentID', $this->input->post('la_subsegmentID')[$key] ?? null);
                                $this->db->set('hourlyRate', $this->input->post('la_hourlyRate')[$key] ?? 0);
                                $this->db->set('totalHours', $this->input->post('la_totalHours')[$key] ?? 0);
                                $this->db->set('usageHours', $this->input->post('la_usageHours')[$key] ?? 0);
                                $this->db->set('totalValue', $this->input->post('la_totalValue')[$key] ?? 0);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('workProcessID', $this->input->post('workProcessID'));
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
                                $result = $this->db->insert('srp_erp_mfq_jc_labourtask');
                            }
                        }
                    }
                }

                $jcOverHeadID = $this->input->post('jcOverHeadID');
                $overHeadID = $this->input->post('overHeadID');
                if (!empty($overHeadID)) {
                    foreach ($overHeadID as $key => $val) {
                        if (!empty($jcOverHeadID[$key])) {
                            $this->db->set('jobCardID', $last_id);
                            $this->db->set('workProcessID', $this->input->post('workProcessID'));

                            $this->db->set('overHeadID', $this->input->post('overHeadID')[$key] ?? null);
                            $this->db->set('uomID', isset($this->input->post('oh_uomID')[$key]) && $this->input->post('oh_uomID')[$key] !== ""
                                ? $this->input->post('oh_uomID')[$key]
                                : null);
                            $this->db->set('segmentID', $this->input->post('oh_segmentID')[$key] ?? null);
                            $this->db->set('hourlyRate', $this->input->post('oh_hourlyRate')[$key] ?? 0);
                            $this->db->set('usageHours', $this->input->post('oh_usageHours')[$key] ?? 0);
                            $this->db->set('totalHours', $this->input->post('oh_totalHours')[$key] ?? 0);
                            $this->db->set('totalValue', $this->input->post('oh_totalValue')[$key] ?? 0);

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

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('jcOverHeadID', $jcOverHeadID[$key]);
                            $result = $this->db->update('srp_erp_mfq_jc_overhead');
                        } else {
                            if (!empty($overHeadID[$key])) {
                                $this->db->set('overHeadID', $this->input->post('overHeadID')[$key]);
                                $this->db->set('uomID', isset($this->input->post('oh_uomID')[$key]) && $this->input->post('oh_uomID')[$key] !== ""
                                    ? $this->input->post('oh_uomID')[$key]
                                    : null);
                                $this->db->set('segmentID', $this->input->post('oh_segmentID')[$key] ?? null);
                                $this->db->set('hourlyRate', $this->input->post('oh_hourlyRate')[$key] ?? 0);
                                $this->db->set('usageHours', $this->input->post('oh_usageHours')[$key] ?? 0);
                                $this->db->set('totalHours', $this->input->post('oh_totalHours')[$key] ?? 0);
                                $this->db->set('totalValue', $this->input->post('oh_totalValue')[$key] ?? 0);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('workProcessID', $this->input->post('workProcessID'));
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
                                $result = $this->db->insert('srp_erp_mfq_jc_overhead');
                            }
                        }
                    }
                }

                $jcMachineID = $this->input->post('jcMachineID');
                $mfq_faID = $this->input->post('mfq_faID');
                if (!empty($mfq_faID)) {
                    foreach ($mfq_faID as $key => $val) {
                        if (!empty($jcMachineID[$key])) {
                            $this->db->set('jobCardID', $last_id);
                            $this->db->set('workProcessID', $this->input->post('workProcessID'));

                            $this->db->set('mfq_faID', $this->input->post('mfq_faID')[$key] ?? null);
                            $this->db->set('uomID', isset($this->input->post('mc_uomID')[$key]) && $this->input->post('mc_uomID')[$key] !== ""
                                ? $this->input->post('mc_uomID')[$key]
                                : null);
                            $this->db->set('segmentID', $this->input->post('mc_segmentID')[$key] ?? null);
                            $this->db->set('hourlyRate', $this->input->post('mc_hourlyRate')[$key] ?? 0);
                            $this->db->set('totalHours', $this->input->post('mc_totalHours')[$key] ?? 0);
                            $this->db->set('usageHours', $this->input->post('mc_usageHours')[$key] ?? 0);
                            $this->db->set('totalValue', $this->input->post('mc_totalValue')[$key] ?? 0);

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

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('jcMachineID', $jcMachineID[$key]);
                            $result = $this->db->update('srp_erp_mfq_jc_machine');
                        } else {
                            if (!empty($mfq_faID[$key])) {
                                $this->db->set('mfq_faID', $this->input->post('mfq_faID')[$key] ?? null);
                                $this->db->set('uomID', isset($this->input->post('mc_uomID')[$key]) && $this->input->post('mc_uomID')[$key] !== ""
                                    ? $this->input->post('mc_uomID')[$key]
                                    : null);
                                $this->db->set('segmentID', $this->input->post('mc_segmentID')[$key] ?? null);
                                $this->db->set('hourlyRate', $this->input->post('mc_hourlyRate')[$key] ?? 0);
                                $this->db->set('totalHours', $this->input->post('mc_totalHours')[$key] ?? 0);
                                $this->db->set('usageHours', $this->input->post('mc_usageHours')[$key] ?? 0);
                                $this->db->set('totalValue', $this->input->post('mc_totalValue')[$key] ?? 0);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('workProcessID', $this->input->post('workProcessID'));
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
                                $result = $this->db->insert('srp_erp_mfq_jc_machine');
                            }
                        }
                    }
                }

                /**Third Party Services Start**/
                $jcOverHeadIDthirdparty = $this->input->post('jcthirdpartyservice');
                $overHeadID = $this->input->post('tpsID');

                if (!empty($overHeadID)) {

                    foreach ($overHeadID as $key => $val) {
                        if (!empty($jcOverHeadIDthirdparty[$key])) {
                            $this->db->set('jobCardID', $last_id);
                            $this->db->set('workProcessID', $this->input->post('workProcessID'));
                            $this->db->set('overHeadID', $this->input->post('tpsID')[$key] ?? null);
                            $this->db->set('uomID', isset($this->input->post('tps_uomID')[$key]) && $this->input->post('tps_uomID')[$key] !== ""
                                ? $this->input->post('tps_uomID')[$key]
                                : null);
                            $this->db->set('hourlyRate', $this->input->post('tps_hourlyRate')[$key] ?? 0);
                            $this->db->set('totalHours', $this->input->post('tps_totalHours')[$key] ?? 0);
                            $this->db->set('totalValue', $this->input->post('tps_totalValue')[$key] ?? 0);
                            $this->db->set('usageHours', $this->input->post('tps_usageHours')[$key] ?? 0);
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

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('jcOverHeadID', $jcOverHeadIDthirdparty[$key]);
                            $result = $this->db->update('srp_erp_mfq_jc_overhead');
                        } else {

                            if (!empty($overHeadID[$key])) {
                                $this->db->set('overHeadID', $this->input->post('tpsID')[$key] ?? null);
                                $this->db->set('uomID', isset($this->input->post('tps_uomID')[$key]) && $this->input->post('tps_uomID')[$key] !== ""
                                    ? $this->input->post('tps_uomID')[$key]
                                    : null);
                                $this->db->set('hourlyRate', $this->input->post('tps_hourlyRate')[$key] ?? 0);
                                $this->db->set('totalHours', $this->input->post('tps_totalHours')[$key] ?? 0);
                                $this->db->set('totalValue', $this->input->post('tps_totalValue')[$key] ?? 0);
                                $this->db->set('usageHours', $this->input->post('tps_usageHours')[$key] ?? 0);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('workProcessID', $this->input->post('workProcessID'));
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
                                $result = $this->db->insert('srp_erp_mfq_jc_overhead');

                            }
                        }
                    }
                }
                /**Third Party Services End**/


                if ($this->input->post('status') == 1) {
                    $this->db->set('status', $this->input->post('status'));
                    $this->db->where('workFlowID', $this->input->post('workFlowID'));
                    $this->db->where('jobID', $this->input->post('workProcessID'));
                    $this->db->where('templateDetailID', $this->input->post('templateDetailID'));
                    $result = $this->db->update('srp_erp_mfq_workflowstatus');

                    /* Generate Next Job Card */
                    $this->db->where('jobID', $this->input->post('workProcessID'));
                    $this->db->where('status', 0);
                    $this->db->order_by('workProcessFlowID', 'asc');
                    $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');
                
                    if($templateDetailID) {
                        $this->db->set('jobNo', $this->input->post('jobNo'));
                        $this->db->set('bomID', $this->input->post('bomID'));
                        $this->db->set('quotationRef', $this->input->post('quotationRef'));
                        $this->db->set('description', $this->input->post('description'));
                        $this->db->set('workProcessID', $this->input->post('workProcessID'));
                        $this->db->set('workFlowID', $this->input->post('workFlowID'));
                        $this->db->set('templateDetailID', $templateDetailID);
                        $this->db->set('companyID', current_companyID());
                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('createdUserID', current_userID());
                        $this->db->set('createdUserName', current_user());
                        $this->db->set('createdDateTime', current_date(true));
                        $this->db->insert('srp_erp_mfq_jobcardmaster');
                        $newJobCardID = $this->db->insert_id();
    
                        $workProcessID = $this->input->post('workProcessID');
                        $materialConsumption = $this->db->query("SELECT * FROM srp_erp_mfq_jc_materialconsumption WHERE workProcessID = {$workProcessID} AND jobCardID = {$last_id}")->result_array();
                        if(!empty($materialConsumption)) {
                            foreach($materialConsumption as $mcp) {
                                $this->db->set('mfqItemID', $mcp['mfqItemID']);
                                $this->db->set('qtyUsed', $mcp['qtyUsed']);
                                $this->db->set('usageQty', $mcp['usageQty']);
                                $this->db->set('unitCost', $mcp['unitCost']);
                                $this->db->set('materialCost', $mcp['materialCost']);
                                $this->db->set('markUp', $mcp['markUp']);
                                $this->db->set('materialCharge', $mcp['materialCharge']);
                                $this->db->set('jobCardID', $newJobCardID);
                                $this->db->set('workProcessID', $workProcessID);
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
                                $result = $this->db->insert('srp_erp_mfq_jc_materialconsumption');
                            }
                        }
    
                        $labourTaskDet = $this->db->query("SELECT * FROM srp_erp_mfq_jc_labourtask WHERE workProcessID = {$workProcessID} AND jobCardID = {$last_id}")->result_array();
                        if(!empty($labourTaskDet)) {
                            foreach($labourTaskDet as $ltd) {
                                $this->db->set('labourTask', $ltd['labourTask']);
                                $this->db->set('uomID', $ltd['uomID']);
                                $this->db->set('segmentID', $ltd['segmentID']);
                                $this->db->set('subsegmentID', $ltd['subsegmentID']);
                                $this->db->set('hourlyRate', $ltd['hourlyRate']);
                                $this->db->set('totalHours', $ltd['totalHours']);
                                $this->db->set('usageHours', $ltd['usageHours']);
                                $this->db->set('totalValue', $ltd['totalValue']);
                                $this->db->set('jobCardID', $newJobCardID);
                                $this->db->set('workProcessID', $workProcessID);
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
                                $result = $this->db->insert('srp_erp_mfq_jc_labourtask');
                            }
                        }
    
                        $overheadDet = $this->db->query("SELECT * FROM srp_erp_mfq_jc_overhead WHERE workProcessID = {$workProcessID} AND jobCardID = {$last_id}")->result_array();
                        if(!empty($overheadDet)) {
                            foreach($overheadDet as $ohd) {
                                $this->db->set('overHeadID', $ohd['overHeadID']);
                                $this->db->set('uomID', $ohd['uomID']);
                                $this->db->set('segmentID', $ohd['segmentID']);
                                $this->db->set('hourlyRate', $ohd['hourlyRate']);
                                $this->db->set('totalHours', $ohd['totalHours']);
                                $this->db->set('usageHours', $ohd['usageHours']);
                                $this->db->set('totalValue', $ohd['totalValue']);
                                $this->db->set('jobCardID', $newJobCardID);
                                $this->db->set('workProcessID', $workProcessID);
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
                                $result = $this->db->insert('srp_erp_mfq_jc_overhead');
                            }
                        }
    
                        $machineDet = $this->db->query("SELECT * FROM srp_erp_mfq_jc_machine WHERE workProcessID = {$workProcessID} AND jobCardID = {$last_id}")->result_array();
                        if(!empty($machineDet)) {
                            foreach($machineDet as $macD) {
                                $this->db->set('mfq_faID', $macD['mfq_faID']);
                                $this->db->set('uomID', $macD['uomID']);
                                $this->db->set('segmentID', $macD['segmentID']);
                                $this->db->set('hourlyRate', $macD['hourlyRate']);
                                $this->db->set('totalHours', $macD['totalHours']);
                                $this->db->set('usageHours', $macD['usageHours']);
                                $this->db->set('totalValue', $macD['totalValue']);
                                $this->db->set('jobCardID', $newJobCardID);
                                $this->db->set('workProcessID', $workProcessID);
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
                                $result = $this->db->insert('srp_erp_mfq_jc_machine');
                            }
                        }
                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Job Card Saved Failed ' . $this->db->_error_message());

                } else {
                    $this->db->trans_commit();
                    return array('s', 'Job Card Saved Successfully.', $last_id);
                }
            }
        } else {
            return array('w', 'There are pending related job cards to be closed');
        }
    }

    function delete_materialConsumption()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('jcMaterialConsumptionID');
        $this->db->from('srp_erp_mfq_jc_materialconsumption');
        $this->db->where('jobCardID', $masterID);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $result = $this->db->delete('srp_erp_mfq_jc_materialconsumption', array('jcMaterialConsumptionID' => $this->input->post('jcMaterialConsumptionID')), 1);
        $result2 = $this->db->delete('srp_erp_mfq_jc_usage', array('jobCardID' => $masterID,'jobDetailID' => $this->input->post('jcMaterialConsumptionID')), 1);
        $this->db->query("UPDATE srp_erp_itemmaster_sub AS updt_subItem INNER JOIN (
            SELECT subItemAutoID,isSold,soldDocumentID,soldDocumentAutoID,soldDocumentDetailID 
            FROM srp_erp_itemmaster_sub 
            WHERE soldDocumentID = 'JOB'  AND soldDocumentDetailID = {$this->input->post('jcMaterialConsumptionID')}) as sel_subItem
            SET updt_subItem.isSold = NULL, updt_subItem.soldDocumentID = NULL, updt_subItem.soldDocumentAutoID = NULL, updt_subItem.soldDocumentDetailID = NULL
            WHERE updt_subItem.soldDocumentID = 'JOB' AND updt_subItem.soldDocumentDetailID = {$this->input->post('jcMaterialConsumptionID')}");

        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function delete_labour_task()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('jcLabourTaskID');
        $this->db->from('srp_erp_mfq_jc_labourtask');
        $this->db->where('jobCardID', $masterID);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $result = $this->db->delete('srp_erp_mfq_jc_labourtask', array('jcLabourTaskID' => $this->input->post('jcLabourTaskID')), 1);
        $result2 = $this->db->delete('srp_erp_mfq_jc_usage', array('jobCardID' => $masterID,'jobDetailID' => $this->input->post('jcLabourTaskID')), 1);

        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function delete_overhead_cost()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('jcOverHeadID');
        $this->db->from('srp_erp_mfq_jc_overhead');
        $this->db->join('srp_erp_mfq_overhead','srp_erp_mfq_overhead.overHeadID  = srp_erp_mfq_jc_overhead.overHeadID','left');
        $this->db->where('jobCardID', $masterID);
        $this->db->where('typeID', 1);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $result = $this->db->delete('srp_erp_mfq_jc_overhead', array('jcOverHeadID' => $this->input->post('jcOverHeadID')), 1);
        $result2 = $this->db->delete('srp_erp_mfq_jc_usage', array('jobCardID' => $masterID,'jobDetailID' => $this->input->post('jcOverHeadID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function delete_machine_cost()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('jcMachineID');
        $this->db->from('srp_erp_mfq_jc_machine');
        $this->db->where('jobCardID', $masterID);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $result = $this->db->delete('srp_erp_mfq_jc_machine', array('jcMachineID' => $this->input->post('jcMachineID')), 1);
        $result2 = $this->db->delete('srp_erp_mfq_jc_usage', array('jobCardID' => $masterID,'jobDetailID' => $this->input->post('jcMachineID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function load_data_from_bom()
    {

        $data = array();
        $bomID = $this->input->post("id");
        $qty = $this->input->post("qty");
        $jobID = $this->input->post("jobID");

        $companyID = current_companyID();
        $isProcessBased = $this->db->query("SELECT 
                                            IF(mfqItemID = 0 || mfqItemID IS NULL,1,2) as isprocessbased
                                            FROM
                                            srp_erp_mfq_billofmaterial
                                            where 
                                            bomMasterID = $bomID ")->row('isprocessbased');
        if($isProcessBased == 1){ 
            $qty = 1;
        }

        $this->db->select("srp_erp_mfq_bom_materialconsumption.*,(qtyUsed * $qty) as qtyUsed,(qtyUsed * $qty) * unitCost  as materialCost,(((qtyUsed * $qty) * unitCost * markUp)/100)+((qtyUsed * $qty) * unitCost) as materialCharge,srp_erp_mfq_itemmaster.itemType,job.confirmedYN,job.linkedJobID,job.documentCode,CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN 'RM' WHEN 2 THEN 'FG' WHEN 3 THEN 'SF'
                                END,' - ',srp_erp_mfq_itemmaster.itemDescription) as itemDescription,partNo,UnitDes,isPackageYN");
        $this->db->from('srp_erp_mfq_bom_materialconsumption');
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_bom_materialconsumption.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'inner');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID', 'inner');
        $this->db->join("(SELECT mfqItemID,confirmedYN,linkedJobID,documentCode FROM srp_erp_mfq_job WHERE linkedJobID = $jobID) job", 'srp_erp_mfq_bom_materialconsumption.mfqItemID = job.mfqItemID', 'left');
        $this->db->where('srp_erp_mfq_bom_materialconsumption.bomMasterID', $bomID);
        $this->db->where('srp_erp_mfq_bom_materialconsumption.companyID', $companyID);
        $this->db->where('(srp_erp_mfq_bom_materialconsumption.isPackageYN IS NULL OR isPackageYN != 1)', null);
        $result = $this->db->get()->result_array();
        $data["materialConsumption"] = $result;

        $this->db->select("srp_erp_mfq_bom_materialconsumption.*,(qtyUsed * $qty) as qtyUsed,(qtyUsed * $qty) * unitCost  as materialCost,(((qtyUsed * $qty) * unitCost * markUp)/100)+((qtyUsed * $qty) * unitCost) as materialCharge,srp_erp_mfq_itemmaster.itemType,job.confirmedYN,job.linkedJobID,job.documentCode,CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN 'RM' WHEN 2 THEN 'FG' WHEN 3 THEN 'SF'
        END,' - ',srp_erp_mfq_itemmaster.itemDescription) as itemDescription,partNo,UnitDes,isPackageYN");
        $this->db->from('srp_erp_mfq_bom_materialconsumption');
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_bom_materialconsumption.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'inner');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID', 'inner');
        $this->db->join("(SELECT mfqItemID,confirmedYN,linkedJobID,documentCode FROM srp_erp_mfq_job WHERE linkedJobID = $jobID) job", 'srp_erp_mfq_bom_materialconsumption.mfqItemID = job.mfqItemID', 'left');
        $this->db->where('bomMasterID', $bomID);
        $this->db->where('srp_erp_mfq_bom_materialconsumption.companyID', $companyID);
        $this->db->where('isPackageYN', 1);
        $result = $this->db->get()->result_array();
        $data["packaging"] = $result;

        $this->db->select("*,(totalHours * $qty) as totalHours,(totalHours * $qty) * hourlyRate as totalValue");
        $this->db->from('srp_erp_mfq_bom_labourtask');
        $this->db->join('srp_erp_mfq_overhead', 'srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_labourtask.labourTask', 'inner');
        $this->db->where('bomMasterID', $bomID);
        $this->db->where('srp_erp_mfq_bom_labourtask.companyID', $companyID);
        $result = $this->db->get()->result_array();
        $data["labourTask"] = $result;

        $this->db->select("*,(totalHours * $qty) as totalHours,(totalHours * $qty) * hourlyRate as totalValue");
        $this->db->from('srp_erp_mfq_bom_overhead');
        $this->db->join('srp_erp_mfq_overhead', 'srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_overhead.overheadID', 'inner');
        $this->db->where('bomMasterID', $bomID);
        $this->db->where('srp_erp_mfq_bom_overhead.companyID', $companyID);
        $this->db->where('srp_erp_mfq_overhead.typeID', 1);
        $result = $this->db->get()->result_array();
        $data["overheadCost"] = $result;

        $this->db->select("*,(totalHours * $qty) as totalHours,(totalHours * $qty) * hourlyRate as totalValue");
        $this->db->from('srp_erp_mfq_bom_overhead');
        $this->db->join('srp_erp_mfq_overhead', 'srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_overhead.overheadID', 'inner');
        $this->db->where('bomMasterID', $bomID);
        $this->db->where('srp_erp_mfq_bom_overhead.companyID', $companyID);
        $this->db->where('srp_erp_mfq_overhead.typeID', 2);
        $result = $this->db->get()->result_array();
        $data["thirdparty"] = $result;

        $this->db->select("*,(totalHours * $qty) as totalHours,(totalHours * $qty) * hourlyRate as totalValue,srp_erp_mfq_bom_machine.segmentID as segment");
        $this->db->from('srp_erp_mfq_bom_machine');
        $this->db->join('srp_erp_mfq_fa_asset_master', 'srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_bom_machine.mfq_faID', 'inner');
        $this->db->where('bomMasterID', $bomID);
        $this->db->where('srp_erp_mfq_bom_machine.companyID', $companyID);
        $result = $this->db->get()->result_array();
        $data["machineCost"] = $result;

        return $data;

    }

    function fetch_finish_goods()
    {
         
        $dataArr = array();
        $dataArr2 = array();
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $sql = 'SELECT mfqCategoryID,mfqSubcategoryID,secondaryItemCode,mfqSubSubCategoryID,itemSystemCode,costGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,	srp_erp_mfq_itemmaster.mfqItemID AS itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN "RM" WHEN 2 THEN "FG" WHEN 3 THEN "SF"
END," - ",IFNULL(itemDescription,""), " (" ,IFNULL(itemSystemCode,""),")") AS "Match",partNo,srp_erp_unit_of_measure.unitDes as uom FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID LEFT JOIN srp_erp_mfq_workflowtemplateitems on  srp_erp_mfq_workflowtemplateitems.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR secondaryItemCode LIKE "' . $search_string . '") AND srp_erp_mfq_itemmaster.companyID = "' . $companyID . '" AND (srp_erp_mfq_itemmaster.itemType = 2 OR srp_erp_mfq_itemmaster.itemType = 3)  AND isActive="1"  LIMIT 20';
        $data = $this->db->query($sql)->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'mfqItemID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'uom' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'partNo' => $val['partNo']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_goods()
    {
         
        $dataArr = array();
        $dataArr2 = array();
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $sql = 'SELECT mfqCategoryID,mfqSubcategoryID,srp_erp_mfq_itemmaster.itemAutoID as primaryAutoID,secondaryItemCode,mfqSubSubCategoryID,itemSystemCode,costGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,	srp_erp_mfq_itemmaster.mfqItemID AS itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN "RM" WHEN 2 THEN "FG" WHEN 3 THEN "SF"
        END," - ",IFNULL(itemDescription,""), " (" ,IFNULL(itemSystemCode,""),")") AS "Match",partNo,srp_erp_unit_of_measure.unitDes as uom FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID LEFT JOIN srp_erp_mfq_workflowtemplateitems on  srp_erp_mfq_workflowtemplateitems.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR secondaryItemCode LIKE "' . $search_string . '") AND srp_erp_mfq_itemmaster.companyID = "' . $companyID . '"  AND isActive="1"  LIMIT 20';
        $data = $this->db->query($sql)->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'mfqItemID' => $val['itemAutoID'] ,'itemAutoID' => $val['primaryAutoID'], 'currentStock' => $val['currentStock'], 'uom' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'partNo' => $val['partNo']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_finish_goods_jobcard()
    {
        $workprocessID =  $_GET['workProcessID'];
        $dataArr = array();
        $dataArr2 = array();
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $sql = 'SELECT mfqCategoryID,mfqSubcategoryID,secondaryItemCode,mfqSubSubCategoryID,itemSystemCode,costGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,	srp_erp_mfq_itemmaster.mfqItemID AS itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN "RM" WHEN 2 THEN "FG" WHEN 3 THEN "SF"
END," - ",IFNULL(itemDescription,""), " (" ,IFNULL(itemSystemCode,""),")") AS "Match",partNo,srp_erp_unit_of_measure.unitDes as uom FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID LEFT JOIN srp_erp_mfq_workflowtemplateitems on  srp_erp_mfq_workflowtemplateitems.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR secondaryItemCode LIKE "' . $search_string . '") AND srp_erp_mfq_itemmaster.companyID = "' . $companyID . '" AND (srp_erp_mfq_itemmaster.itemType = 2 OR srp_erp_mfq_itemmaster.itemType = 3)  AND isActive="1" AND srp_erp_mfq_workflowtemplateitems.workFlowTemplateID = '.$workprocessID.' LIMIT 20';
        $data = $this->db->query($sql)->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'mfqItemID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'uom' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'partNo' => $val['partNo']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }
    function fetch_job_detail()
    {
        $data = array();
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $jobCardID = trim($this->input->post('jobCardID') ?? '');
        $companyID = current_companyID();
        $where = "";
        if (isset($_POST["jobCardID"])) {
            $where = "AND jobCardID = $jobCardID";
        }

        $convertFormat = convert_date_format_sql();
        $data['jobCard'] = $this->db->query("SELECT DATE_FORMAT(JCstartDate, '{$convertFormat}') AS JCstartDate FROM srp_erp_mfq_jobcardmaster WHERE jobcardID = {$jobCardID} AND JCstartDate IS NOT NULL")->row_array();

        $sql = "SELECT 	srp_erp_mfq_jc_materialconsumption.*,CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN 'RM' WHEN 2 THEN 'FG' WHEN 3 THEN 'SF'
                END,' - ',srp_erp_mfq_itemmaster.itemDescription) as itemDescription,srp_erp_unit_of_measure.unitDes as uom,srp_erp_mfq_itemmaster.defaultUnitOfMeasureID as uomID,IFNULL(srp_erp_mfq_itemmaster.partNo,'') as partNo,srp_erp_mfq_itemmaster.currentstock as currentstock,job.confirmedYN,srp_erp_mfq_itemmaster.itemType,job.linkedJobID,job.documentCode,srp_erp_mfq_itemmaster.itemAutoID,srp_erp_itemmaster.isSubitemExist,srp_erp_mfq_warehousemaster.warehouseAutoID,srp_erp_itemmaster.subItemapplicableon  FROM srp_erp_mfq_jc_materialconsumption LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_jc_materialconsumption.mfqItemID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID LEFT JOIN (SELECT mfqItemID,confirmedYN,linkedJobID,documentCode FROM srp_erp_mfq_job WHERE linkedJobID = $workProcessID) job ON srp_erp_mfq_jc_materialconsumption.mfqItemID = job.mfqItemID LEFT JOIN	srp_erp_mfq_job on srp_erp_mfq_jc_materialconsumption.workProcessID = srp_erp_mfq_job.workProcessID LEFT JOIN srp_erp_mfq_warehousemaster on srp_erp_mfq_job.mfqWarehouseAutoID = srp_erp_mfq_warehousemaster.mfqWarehouseAutoID WHERE srp_erp_mfq_jc_materialconsumption.workProcessID = $workProcessID AND ( srp_erp_mfq_jc_materialconsumption.isPackageYN != 1 OR srp_erp_mfq_jc_materialconsumption.isPackageYN IS NULL ) AND srp_erp_mfq_jc_materialconsumption.companyID={$companyID}  $where";
        $data["material"] = $this->db->query($sql)->result_array();

        foreach($data["material"]  as $key => $material){

            $uomID = $material['uomID'];

            $uomDetails = $this->db->query("
                SELECT subunit.subUnitID,umeasure.UnitShortCode,subunit.conversion
                FROM `srp_erp_unitsconversion` as subunit
                LEFT JOIN srp_erp_unit_of_measure as umeasure ON subunit.subUnitID = umeasure.UnitID
                WHERE subunit.masterUnitID = {$uomID} AND subunit.companyID = {$companyID}
            ")->result_array();

            $uom_arr = array();

            $data["material"][$key]['uom_details'] = $uomDetails;

        }


        $sql = "SELECT 	srp_erp_mfq_jc_materialconsumption.*,CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN 'RM' WHEN 2 THEN 'FG' WHEN 3 THEN 'SF'
        END,' - ',srp_erp_mfq_itemmaster.itemDescription) as itemDescription,srp_erp_unit_of_measure.unitDes as uom,IFNULL(srp_erp_mfq_itemmaster.partNo,'') as partNo, srp_erp_mfq_itemmaster.currentstock as currentstock,job.confirmedYN,srp_erp_mfq_itemmaster.itemType,job.linkedJobID,job.documentCode,srp_erp_mfq_itemmaster.itemAutoID,srp_erp_itemmaster.isSubitemExist,srp_erp_mfq_warehousemaster.warehouseAutoID,srp_erp_itemmaster.subItemapplicableon  FROM srp_erp_mfq_jc_materialconsumption LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_jc_materialconsumption.mfqItemID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID LEFT JOIN (SELECT mfqItemID,confirmedYN,linkedJobID,documentCode FROM srp_erp_mfq_job WHERE linkedJobID = $workProcessID) job ON srp_erp_mfq_jc_materialconsumption.mfqItemID = job.mfqItemID LEFT JOIN	srp_erp_mfq_job on srp_erp_mfq_jc_materialconsumption.workProcessID = srp_erp_mfq_job.workProcessID LEFT JOIN srp_erp_mfq_warehousemaster on srp_erp_mfq_job.mfqWarehouseAutoID = srp_erp_mfq_warehousemaster.mfqWarehouseAutoID WHERE srp_erp_mfq_jc_materialconsumption.workProcessID = $workProcessID AND srp_erp_mfq_jc_materialconsumption.isPackageYN = 1 AND srp_erp_mfq_jc_materialconsumption.companyID={$companyID} $where";
        $data["packaging"] = $this->db->query($sql)->result_array();

        $sql = "SELECT srp_erp_mfq_jc_labourtask.*,srp_erp_mfq_overhead.*,srp_erp_mfq_segment.description as segment,srp_erp_unit_of_measure.unitDes as uom FROM srp_erp_mfq_jc_labourtask LEFT JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_labourtask.labourTask LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_jc_labourtask.segmentID = srp_erp_mfq_segment.mfqSegmentID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = uomID WHERE workProcessID = $workProcessID AND srp_erp_mfq_jc_labourtask.companyID={$companyID} $where";
        $data["labourTask"] = $this->db->query($sql)->result_array();

        $sql = "SELECT srp_erp_mfq_jc_overhead.*,srp_erp_mfq_overhead.*,srp_erp_mfq_segment.description as segment,srp_erp_unit_of_measure.unitDes as uom FROM srp_erp_mfq_jc_overhead LEFT JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_overhead.overHeadID LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_jc_overhead.segmentID = srp_erp_mfq_segment.mfqSegmentID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = uomID WHERE workProcessID = $workProcessID $where AND typeID = 1 AND srp_erp_mfq_jc_overhead.companyID={$companyID} ";
        $data["overhead"] = $this->db->query($sql)->result_array();

        $sql = "SELECT srp_erp_mfq_jc_machine.*,srp_erp_mfq_fa_asset_master.*,srp_erp_mfq_segment.description as segment,srp_erp_unit_of_measure.unitDes as uom,srp_erp_mfq_jc_machine.segmentID as segment2 FROM srp_erp_mfq_jc_machine LEFT JOIN srp_erp_mfq_fa_asset_master ON srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_jc_machine.mfq_faID LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_jc_machine.segmentID = srp_erp_mfq_segment.mfqSegmentID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = uomID WHERE workProcessID = $workProcessID AND srp_erp_mfq_jc_machine.companyID={$companyID} $where";
        $data["machine"] = $this->db->query($sql)->result_array();

        $sql = "SELECT srp_erp_mfq_jc_overhead.*,srp_erp_mfq_overhead.*,srp_erp_mfq_segment.description as segment,srp_erp_unit_of_measure.unitDes as uom FROM srp_erp_mfq_jc_overhead LEFT JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_overhead.overHeadID LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_jc_overhead.segmentID = srp_erp_mfq_segment.mfqSegmentID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = uomID WHERE workProcessID = $workProcessID $where AND typeID = 2 AND srp_erp_mfq_jc_overhead.companyID={$companyID} ";
        $data["thirdparty"] = $this->db->query($sql)->result_array();

        return $data;
    }

    function get_unit_of_measure_conversion(){

        $subUnitID = trim($this->input->post('subUnitID') ?? '');
        $defaultUomID = trim($this->input->post('defaultUomID') ?? '');
        $companyID = current_companyID();

        $uomDetails = $this->db->query("
            SELECT subunit.conversion
            FROM `srp_erp_unitsconversion` as subunit
            WHERE subunit.subUnitID = {$subUnitID} AND subunit.masterUnitID = {$defaultUomID} AND subunit.companyID = {$companyID}
        ")->row_array();

        return isset($uomDetails['conversion']) ? $uomDetails['conversion'] : 1;
    }

    function fetch_po_unit_cost()
    {
        $sql = "SELECT * FROM srp_erp_mfq_itemmaster WHERE mfqItemID =" . $this->input->post("mfqItemID");
        $item = $this->db->query($sql)->row_array();
        $result = "";
        //$gearsDB = $this->load->database('gearserp', true);
        if ($item["itemAutoID"]) {
            /*$sql = "SELECT podet2.unitCost as companyLocalWacAmount FROM erp_purchaseordermaster INNER JOIN (SELECT MAX(erp_purchaseorderdetails.purchaseOrderMasterID) as purchaseOrderMasterID FROM erp_purchaseorderdetails INNER JOIN erp_purchaseordermaster ON erp_purchaseorderdetails.purchaseOrderMasterID = erp_purchaseordermaster.purchaseOrderID WHERE erp_purchaseordermaster.approved=-1 AND erp_purchaseorderdetails.companyID='HEMT' AND documentID='PO' AND itemCode =" . $item["itemAutoID"] . ") podet ON erp_purchaseordermaster.purchaseOrderID = podet.purchaseOrderMasterID INNER JOIN (SELECT * FROM erp_purchaseorderdetails WHERE erp_purchaseorderdetails.companyID='HEMT' AND itemCode =" . $item["itemAutoID"] . ") podet2 ON erp_purchaseordermaster.purchaseOrderID = podet2.purchaseOrderMasterID";
            $result = $gearsDB->query($sql)->row_array();*/
            $sql = "SELECT podet2.unitAmount as companyLocalWacAmount FROM srp_erp_purchaseordermaster INNER JOIN (SELECT MAX(srp_erp_purchaseorderdetails.purchaseOrderID) as purchaseOrderMasterID FROM srp_erp_purchaseorderdetails INNER JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseorderdetails.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID WHERE srp_erp_purchaseordermaster.approvedYN=1 AND srp_erp_purchaseorderdetails.companyID=".current_companyID()." AND itemAutoID =" . $item["itemAutoID"] . ") podet ON srp_erp_purchaseordermaster.purchaseOrderID = podet.purchaseOrderMasterID INNER JOIN (SELECT * FROM srp_erp_purchaseorderdetails WHERE srp_erp_purchaseorderdetails.companyID=".current_companyID()." AND itemAutoID =" . $item["itemAutoID"] . ") podet2 ON srp_erp_purchaseordermaster.purchaseOrderID = podet2.purchaseOrderID";
            $result = $this->db->query($sql)->row_array();

        } else {
            $result = array("companyLocalWacAmount" => 0);
        }
        return $result;

    }
    function delete_thirdparty_cost()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('jcOverHeadID');
        $this->db->from('srp_erp_mfq_jc_overhead');
        $this->db->join('srp_erp_mfq_overhead','srp_erp_mfq_overhead.overHeadID  = srp_erp_mfq_jc_overhead.overHeadID','left');
        $this->db->where('jobCardID', $masterID);
        $this->db->where('typeID', 2);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $result = $this->db->delete('srp_erp_mfq_jc_overhead', array('jcOverHeadID' => $this->input->post('jcOverHeadID')), 1);
        $result2 = $this->db->delete('srp_erp_mfq_jc_usage', array('jobCardID' => $masterID,'jobDetailID' => $this->input->post('jcOverHeadID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }
    function  save_workprocess_jobcard_process_based()
    {
        $workprocess_ID = trim($this->input->post('workProcessID') ?? '');
        $status = $this->input->post('status');
        $save = false;
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_job');
        $this->db->where('linkedJobID', $this->input->post('workProcessID'));
        $outputJob = $this->db->get()->result_array();
        $jobCount = count($outputJob);
        if (!empty($outputJob)) {
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_job');
            $this->db->where('linkedJobID', $this->input->post('workProcessID'));
            $this->db->where('closedYN', 1);
            $outputJob = $this->db->get()->result_array();
            $jobClosedCount = count($outputJob);
            if ($jobCount == $jobClosedCount) {
                $save = true;
            } else {
                $save = false;
            }
        } else {
            $save = true;
        }

        /* if($status == 1){
            $get_items = $this->db->query("SELECT materialconsumption.mfqItemID, materialconsumption.ItemAutoID , ifnull(usageQty,0) - ifnull(qty,0) as balanceQty
                                           FROM ( SELECT `srp_erp_mfq_jc_materialconsumption`.`mfqItemID`, SUM( srp_erp_mfq_jc_materialconsumption.usageQty ) as usageQty, `srp_erp_mfq_itemmaster`.`itemAutoID`, `srp_erp_itemmaster`.`subItemapplicableon` 
                                                FROM `srp_erp_mfq_jc_materialconsumption`
                                                LEFT JOIN `srp_erp_mfq_itemmaster` ON `srp_erp_mfq_jc_materialconsumption`.`mfqItemID` = `srp_erp_mfq_itemmaster`.`mfqItemID`
                                                LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_mfq_itemmaster`.`itemAutoID` = `srp_erp_itemmaster`.`itemAutoID` 
                                                WHERE `workProcessID` = '{$workprocess_ID}' AND srp_erp_itemmaster.isSubitemExist = 1 
                                            GROUP BY
                                            srp_erp_mfq_itemmaster.itemAutoID 
                                            ) materialconsumption
                                        LEFT JOIN ( SELECT `itemAutoID` as itemautoIDSub, count( subItemAutoID ) AS qty FROM `srp_erp_itemmaster_sub` WHERE soldDocumentID = 'JOB' AND `soldDocumentAutoID` = '{$workprocess_ID}' GROUP BY srp_erp_itemmaster_sub.itemAutoID ) itemmastersub ON materialconsumption.itemautoID = itemmastersub.itemautoIDSub
                                    having balanceQty>0")->result_array();

            if(!empty($get_items)){
                return array('e', 'Sub Items are not configured');
            }
        } */

        if ($save) {
            $date_format_policy = date_format_policy();
            $includeCurrentDateDaily = $this->input->post('includeCurrentDate');
            $jobCardStartDate = $this->input->post('jobCardStartDate');
            $JCstartDate = input_format_date($jobCardStartDate, $date_format_policy);

            $last_id = "";
            $this->db->trans_start();
            if (!$this->input->post('jobCardID')) {
                $this->db->set('jobNo', $this->input->post('jobNo'));
                $this->db->set('bomID', $this->input->post('bomID'));
                $this->db->set('JCstartDate', $JCstartDate);
                $this->db->set('includeCurrentDate', $includeCurrentDateDaily);
                $this->db->set('quotationRef', $this->input->post('quotationRef'));
                $this->db->set('description', $this->input->post('description'));
                $this->db->set('workProcessID', $this->input->post('workProcessID'));
                $this->db->set('workFlowID', $this->input->post('workFlowID'));
                $this->db->set('templateDetailID', $this->input->post('templateDetailID'));
                $this->db->set('companyID', current_companyID());
                $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserName', current_user());
                $this->db->set('createdDateTime', current_date(true));

                $result = $this->db->insert('srp_erp_mfq_jobcardmaster');
                $last_id = $this->db->insert_id();

                $this->db->set('unitPrice', $this->input->post('unitPrice'));
                $this->db->where('workProcessID', $this->input->post('workProcessID'));
                $result = $this->db->update('srp_erp_mfq_job');

            } else {
                $last_id = $this->input->post('jobCardID');
                $this->db->set('jobNo', $this->input->post('jobNo'));
                $this->db->set('bomID', $this->input->post('bomID'));
                $this->db->set('JCstartDate', $JCstartDate);
                $this->db->set('includeCurrentDate', $includeCurrentDateDaily);
                $this->db->set('quotationRef', $this->input->post('quotationRef'));
                $this->db->set('description', $this->input->post('description'));
                $this->db->set('templateDetailID', $this->input->post('templateDetailID'));
                $this->db->set('companyID', current_companyID());
                $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                $this->db->set('modifiedUserID', current_userID());
                $this->db->set('modifiedUserName', current_user());
                $this->db->set('modifiedDateTime', current_date(true));
                $this->db->where('jobcardID', $this->input->post('jobCardID'));
                $result = $this->db->update('srp_erp_mfq_jobcardmaster');

                $this->db->set('unitPrice', $this->input->post('unitPrice'));
                $this->db->where('workProcessID', $this->input->post('workProcessID'));
                $result = $this->db->update('srp_erp_mfq_job');
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Job Card Saved Failed ' . $this->db->_error_message());

            } else {

                $jcMaterialConsumptionID = $this->input->post('jcMaterialConsumptionID');
                $mfqItemID = $this->input->post('mfqItemID');
                if (!empty($mfqItemID)) {
                    foreach ($mfqItemID as $key => $val) 
                    {
                        $dailyUpdate = $this->input->post('Daily_material')[$key];
                        if (!empty($jcMaterialConsumptionID[$key])) {
                            $this->db->set('jobCardID', $last_id);
                            $this->db->set('workProcessID', $this->input->post('workProcessID'));

                            $this->db->set('mfqItemID', $this->input->post('mfqItemID')[$key]);
                            $this->db->set('qtyUsed', $this->input->post('qtyUsed')[$key]);
                            $this->db->set('unitCost', $this->input->post('unitCost')[$key]);
                            $this->db->set('materialCost', $this->input->post('materialCost')[$key]);
                            $this->db->set('usageQty', $this->input->post('usageQty')[$key]);
                            $this->db->set('markUp', $this->input->post('markUp')[$key]);
                            $this->db->set('materialCharge', $this->input->post('materialCharge')[$key]);
                            $this->db->set('dayComputation', $dailyUpdate);

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

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('jcMaterialConsumptionID', $jcMaterialConsumptionID[$key]);
                            $result = $this->db->update('srp_erp_mfq_jc_materialconsumption');

                            if($status == 1 && $dailyUpdate == 1) 
                            {
                                if($this->input->post('bomID')) {
                                    $bomID = $this->input->post('bomID');
                                    $mfqItemIDs = $this->input->post('mfqItemID')[$key];
                                    $qtyUsed = $this->db->query("SELECT qtyUsed FROM srp_erp_mfq_bom_materialconsumption
                                    WHERE bomMasterID =  {$bomID} AND mfqItemID = {$mfqItemIDs}")->row('qtyUsed');

                                    if(($qtyUsed) && $qtyUsed > 0) {
                                        $unitCost = $this->input->post('unitCost')[$key];
                                        //$now = time();
                                        $startDate = strtotime($this->input->post('jobCardStartDate'));
                                        $now = strtotime(current_date(false));
                                        $datediff = $now - $startDate;
                                        $days = round($datediff / (60 * 60 * 24));
                                        $includeCurrentDate = $this->input->post('includeCurrentDate');
                                        if($includeCurrentDate == 2) {
                                            $days += 1;
                                        }
                                        $usageQty = ($qtyUsed * $days) + $this->input->post('usageQty')[$key];
                                        $materialCost = $usageQty * $unitCost;
        
                                        $this->db->set('jobID', $this->input->post('workProcessID'));
                                        $this->db->set('jobDetailID', $jcMaterialConsumptionID[$key]);
                                        $this->db->set('jobCardID', $last_id);
                                        $this->db->set('typeMasterAutoID', $this->input->post('mfqItemID')[$key]);
                                        $this->db->set('usageAmount', ($qtyUsed * $days));
                                        $this->db->set('linkedDocumentID', 'FORMUNLA');
                                        $this->db->set('linkedDocumentAutoID', null);
                                        $this->db->set('companyID', current_companyID());
                                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                        $this->db->set('createdUserID', current_userID());
                                        $this->db->set('createdUserName', current_user());
                                        $this->db->set('createdDateTime', current_date(true));
                                        $this->db->set('typeID', 1);
                                        $this->db->insert('srp_erp_mfq_jc_usage');
        
                                        $this->db->query("UPDATE srp_erp_mfq_jc_materialconsumption 
                                                            SET 
                                                                usageQty = {$usageQty},
                                                                materialCost = materialCost + $materialCost,
                                                                materialCharge = (materialCost)+((materialCost)*(markUp/100))
                                                            WHERE
                                                                jcMaterialConsumptionID= {$jcMaterialConsumptionID[$key]}"
                                                        );
                                    }
                                }
                            }
                        } else {
                            if (!empty($mfqItemID[$key])) {
                                $this->db->set('mfqItemID', $this->input->post('mfqItemID')[$key]);
                                $this->db->set('qtyUsed', $this->input->post('qtyUsed')[$key]);
                                $this->db->set('usageQty', $this->input->post('usageQty')[$key]);
                                $this->db->set('unitCost', $this->input->post('unitCost')[$key]);
                                $this->db->set('materialCost', $this->input->post('materialCost')[$key]);
                                $this->db->set('markUp', $this->input->post('markUp')[$key]);
                                $this->db->set('materialCharge', $this->input->post('materialCharge')[$key]);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('dayComputation', $dailyUpdate);
                                $this->db->set('workProcessID', $this->input->post('workProcessID'));
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
                                $result = $this->db->insert('srp_erp_mfq_jc_materialconsumption');
                                $materialConID = $this->db->insert_id();

                                if($status == 1 && $dailyUpdate == 1) 
                                {
                                    if($this->input->post('bomID')) {
                                        $bomID = $this->input->post('bomID');
                                        $mfqItemIDs = $this->input->post('mfqItemID')[$key];
                                        $qtyUsed = $this->db->query("SELECT qtyUsed FROM srp_erp_mfq_bom_materialconsumption
                                        WHERE bomMasterID =  {$bomID} AND mfqItemID = {$mfqItemIDs}")->row('qtyUsed');

                                        if(($qtyUsed) && $qtyUsed > 0) {
                                            $unitCost = $this->input->post('unitCost')[$key];
                                            //$now = time();
                                            $startDate = strtotime($this->input->post('jobCardStartDate'));
                                            $now = strtotime(current_date(false));
                                            $datediff = $now - $startDate;
                                            $days = round($datediff / (60 * 60 * 24));
                                            $includeCurrentDate = $this->input->post('includeCurrentDate');
                                            if($includeCurrentDate == 2) {
                                                $days += 1;
                                            }
                                            $usageQty = ($qtyUsed * $days) + $this->input->post('usageQty')[$key];
                                            $materialCost = $usageQty * $unitCost;
            
                                            $this->db->set('jobID', $this->input->post('workProcessID'));
                                            $this->db->set('jobDetailID', $materialConID);
                                            $this->db->set('jobCardID', $last_id);
                                            $this->db->set('typeMasterAutoID', $this->input->post('mfqItemID')[$key]);
                                            $this->db->set('usageAmount', $qtyUsed * $days);
                                            $this->db->set('linkedDocumentID', 'FORMUNLA');
                                            $this->db->set('linkedDocumentAutoID', null);
                                            $this->db->set('companyID', current_companyID());
                                            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                            $this->db->set('createdUserID', current_userID());
                                            $this->db->set('createdUserName', current_user());
                                            $this->db->set('createdDateTime', current_date(true));
                                            $this->db->set('typeID', 1);
                                            $this->db->insert('srp_erp_mfq_jc_usage');
            
                                            $this->db->query("UPDATE srp_erp_mfq_jc_materialconsumption 
                                                                SET 
                                                                    usageQty = {$usageQty},
                                                                    materialCost = materialCost + $materialCost,
                                                                    materialCharge = (materialCost)+((materialCost)*(markUp/100))
                                                                WHERE
                                                                    jcMaterialConsumptionID= {$materialConID}"
                                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }


                $jcLabourTaskID = $this->input->post('jcLabourTaskID');
                $labourTask = $this->input->post('labourTask');
                if (!empty($labourTask)) {
                    foreach ($labourTask as $key => $val) 
                    {
                        $dailyUpdate = $this->input->post('Daily_labour')[$key];
                        if (!empty($jcLabourTaskID[$key])) {
                            $this->db->set('jobCardID', $last_id);
                            $this->db->set('workProcessID', $this->input->post('workProcessID'));

                            $this->db->set('labourTask', $this->input->post('labourTask')[$key]);
                            /*$this->db->set('activityCode', $this->input->post('la_activityCode')[$key]);*/
                            $this->db->set('uomID', $this->input->post('la_uomID')[$key] == "" ? NULL : $this->input->post('la_uomID')[$key]);
                            $this->db->set('segmentID', $this->input->post('la_segmentID')[$key]);
                            $this->db->set('subsegmentID', $this->input->post('la_subsegmentID')[$key]);
                            $this->db->set('hourlyRate', $this->input->post('la_hourlyRate')[$key]);
                            $this->db->set('totalHours', $this->input->post('la_totalHours')[$key]);
                            $this->db->set('usageHours', $this->input->post('la_usageHours')[$key]);
                            $this->db->set('totalValue', $this->input->post('la_totalValue')[$key]);
                            $this->db->set('dayComputation', $dailyUpdate);

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

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('jcLabourTaskID', $jcLabourTaskID[$key]);
                            $result = $this->db->update('srp_erp_mfq_jc_labourtask');

                            if($status == 1 && $dailyUpdate == 1) 
                            {
                                if($this->input->post('bomID')) {
                                    $bomID = $this->input->post('bomID');
                                    $labourTasks = $this->input->post('labourTask')[$key];
                                    $totalHours = $this->db->query("SELECT totalHours FROM srp_erp_mfq_bom_labourtask
                                    WHERE bomMasterID =  {$bomID} AND labourTask = {$labourTasks}")->row('totalHours');

                                    if(($totalHours) && $totalHours > 0) {
                                        $hourlyRate = $this->input->post('la_hourlyRate')[$key];
                                        //$now = time();
                                        $startDate = strtotime($this->input->post('jobCardStartDate'));
                                        $now = strtotime(current_date(false));
                                        $datediff = $now - $startDate;
                                        $days = round($datediff / (60 * 60 * 24));
                                        $includeCurrentDate = $this->input->post('includeCurrentDate');
                                        if($includeCurrentDate == 2) {
                                            $days += 1;
                                        }
                                        $usageHours = ($totalHours * $days) + $this->input->post('la_usageHours')[$key];
                                        $totalValue = $usageHours * $hourlyRate;
        
                                        $this->db->set('jobID', $this->input->post('workProcessID'));
                                        $this->db->set('jobDetailID', $jcLabourTaskID[$key]);
                                        $this->db->set('jobCardID', $last_id);
                                        $this->db->set('typeMasterAutoID', $this->input->post('labourTask')[$key]);
                                        $this->db->set('usageAmount', $totalHours * $days);
                                        $this->db->set('linkedDocumentID', 'FORMUNLA');
                                        $this->db->set('linkedDocumentAutoID', null);
                                        $this->db->set('companyID', current_companyID());
                                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                        $this->db->set('createdUserID', current_userID());
                                        $this->db->set('createdUserName', current_user());
                                        $this->db->set('createdDateTime', current_date(true));
                                        $this->db->set('typeID', 2);
                                        $this->db->insert('srp_erp_mfq_jc_usage');
        
                                        $this->db->query("UPDATE srp_erp_mfq_jc_labourtask 
                                                                SET usageHours = {$usageHours}, totalValue = totalValue + $totalValue
                                                                WHERE jcLabourTaskID = {$jcLabourTaskID[$key]}"
                                                            );
                                    }
                                }
                            }
                        } else {
                            if (!empty($labourTask[$key])) {
                                $this->db->set('labourTask', $this->input->post('labourTask')[$key]);
                               /* $this->db->set('activityCode', $this->input->post('la_activityCode')[$key]);*/
                                $this->db->set('uomID', $this->input->post('la_uomID')[$key] == "" ? NULL : $this->input->post('la_uomID')[$key]);
                                $this->db->set('segmentID', $this->input->post('la_segmentID')[$key]);
                                $this->db->set('subsegmentID', $this->input->post('la_subsegmentID')[$key]);
                                $this->db->set('hourlyRate', $this->input->post('la_hourlyRate')[$key]);
                                $this->db->set('totalHours', $this->input->post('la_totalHours')[$key]);
                                $this->db->set('usageHours', $this->input->post('la_usageHours')[$key]);
                                $this->db->set('totalValue', $this->input->post('la_totalValue')[$key]);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('dayComputation', $dailyUpdate);
                                $this->db->set('workProcessID', $this->input->post('workProcessID'));
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
                                $result = $this->db->insert('srp_erp_mfq_jc_labourtask');
                                $labourTaID = $this->db->insert_id();

                                if($status == 1 && $dailyUpdate == 1) 
                                {
                                    if($this->input->post('bomID')) {
                                        $bomID = $this->input->post('bomID');
                                        $labourTasks = $this->input->post('labourTask')[$key];
                                        $totalHours = $this->db->query("SELECT totalHours FROM srp_erp_mfq_bom_labourtask
                                        WHERE bomMasterID = {$bomID} AND labourTask = {$labourTasks}")->row('totalHours');
    
                                        if(($totalHours) && $totalHours > 0) {
                                            $hourlyRate = $this->input->post('la_hourlyRate')[$key];
                                            //$now = time();
                                            $startDate = strtotime($this->input->post('jobCardStartDate'));
                                            $now = strtotime(current_date(false));
                                            $datediff = $now - $startDate;
                                            $days = round($datediff / (60 * 60 * 24));
                                            $includeCurrentDate = $this->input->post('includeCurrentDate');
                                            if($includeCurrentDate == 2) {
                                                $days += 1;
                                            }
                                            $usageHours = ($totalHours * $days) + $this->input->post('la_usageHours')[$key];
                                            $totalValue = $usageHours * $hourlyRate;
            
                                            $this->db->set('jobID', $this->input->post('workProcessID'));
                                            $this->db->set('jobDetailID', $labourTaID);
                                            $this->db->set('jobCardID', $last_id);
                                            $this->db->set('typeMasterAutoID', $this->input->post('labourTask')[$key]);
                                            $this->db->set('usageAmount', $totalHours * $days);
                                            $this->db->set('linkedDocumentID', 'FORMUNLA');
                                            $this->db->set('linkedDocumentAutoID', null);
                                            $this->db->set('companyID', current_companyID());
                                            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                            $this->db->set('createdUserID', current_userID());
                                            $this->db->set('createdUserName', current_user());
                                            $this->db->set('createdDateTime', current_date(true));
                                            $this->db->set('typeID', 2);
                                            $this->db->insert('srp_erp_mfq_jc_usage');
            
                                            $this->db->query("UPDATE srp_erp_mfq_jc_labourtask 
                                                                SET usageHours = {$usageHours}, totalValue = totalValue + $totalValue
                                                                WHERE jcLabourTaskID = {$labourTaID}"
                                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $jcOverHeadID = $this->input->post('jcOverHeadID');
                $overHeadID = $this->input->post('overHeadID');
                if (!empty($overHeadID)) {
                    foreach ($overHeadID as $key => $val) 
                    {
                        $dailyUpdate = $this->input->post('Daily_overhead')[$key];
                        if (!empty($jcOverHeadID[$key])) {
                            $this->db->set('jobCardID', $last_id);
                            $this->db->set('workProcessID', $this->input->post('workProcessID'));

                            $this->db->set('overHeadID', $this->input->post('overHeadID')[$key]);
                            /*$this->db->set('activityCode', $this->input->post('oh_activityCode')[$key]);*/
                            $this->db->set('uomID', $this->input->post('oh_uomID')[$key] == "" ? NULL : $this->input->post('oh_uomID')[$key]);
                            $this->db->set('segmentID', $this->input->post('oh_segmentID')[$key]);
                            $this->db->set('hourlyRate', $this->input->post('oh_hourlyRate')[$key]);
                            $this->db->set('usageHours', $this->input->post('oh_usageHours')[$key]);
                            $this->db->set('totalHours', $this->input->post('oh_totalHours')[$key]);
                            $this->db->set('totalValue', $this->input->post('oh_totalValue')[$key]);
                            $this->db->set('dayComputation', $dailyUpdate);

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

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('jcOverHeadID', $jcOverHeadID[$key]);
                            $result = $this->db->update('srp_erp_mfq_jc_overhead');

                            if($status == 1 && $dailyUpdate == 1) 
                            {
                                if($this->input->post('bomID')) {
                                    $bomID = $this->input->post('bomID');
                                    $overheadIDs = $this->input->post('overHeadID')[$key];
                                    $totalHours = $this->db->query("SELECT totalHours FROM srp_erp_mfq_bom_overhead
                                    WHERE bomMasterID = {$bomID} AND overheadID = {$overheadIDs}")->row('totalHours');

                                    if(($totalHours) && $totalHours > 0) {
                                        $hourlyRate = $this->input->post('oh_hourlyRate')[$key];
                                        //$now = time();
                                        $startDate = strtotime($this->input->post('jobCardStartDate'));
                                        $now = strtotime(current_date(false));
                                        $datediff = $now - $startDate;
                                        $days = round($datediff / (60 * 60 * 24));
                                        $includeCurrentDate = $this->input->post('includeCurrentDate');
                                        if($includeCurrentDate == 2) {
                                            $days += 1;
                                        }
                                        $usageHours = ($totalHours * $days) + $this->input->post('oh_usageHours')[$key];
                                        $totalValue = $usageHours * $hourlyRate;
        
                                        $this->db->set('jobID', $this->input->post('workProcessID'));
                                        $this->db->set('jobDetailID', $jcOverHeadID[$key]);
                                        $this->db->set('jobCardID', $last_id);
                                        $this->db->set('typeMasterAutoID', $this->input->post('overHeadID')[$key]);
                                        $this->db->set('usageAmount', $totalHours * $days);
                                        $this->db->set('linkedDocumentID', 'FORMUNLA');
                                        $this->db->set('linkedDocumentAutoID', null);
                                        $this->db->set('companyID', current_companyID());
                                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                        $this->db->set('createdUserID', current_userID());
                                        $this->db->set('createdUserName', current_user());
                                        $this->db->set('createdDateTime', current_date(true));
                                        $this->db->set('typeID', 3);
                                        $this->db->insert('srp_erp_mfq_jc_usage');
        
                                        $this->db->query("UPDATE srp_erp_mfq_jc_overhead 
                                                            SET usageHours = {$usageHours}, totalValue = totalValue + $totalValue
                                                            WHERE jcOverHeadID = {$jcOverHeadID[$key]}"
                                                        );
                                    }
                                }
                            }
                        } else {
                            if (!empty($overHeadID[$key])) {
                                $this->db->set('overHeadID', $this->input->post('overHeadID')[$key]);
                                /*$this->db->set('activityCode', $this->input->post('oh_activityCode')[$key]);*/
                                $this->db->set('uomID', $this->input->post('oh_uomID')[$key] == "" ? NULL : $this->input->post('oh_uomID')[$key]);
                                $this->db->set('segmentID', $this->input->post('oh_segmentID')[$key]);
                                $this->db->set('hourlyRate', $this->input->post('oh_hourlyRate')[$key]);
                                $this->db->set('totalHours', $this->input->post('oh_totalHours')[$key]);
                                $this->db->set('usageHours', $this->input->post('oh_usageHours')[$key]);
                                $this->db->set('totalValue', $this->input->post('oh_totalValue')[$key]);
                                $this->db->set('dayComputation', $dailyUpdate);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('workProcessID', $this->input->post('workProcessID'));
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
                                $result = $this->db->insert('srp_erp_mfq_jc_overhead');
                                $overheID = $this->db->insert_id();

                                if($status == 1 && $dailyUpdate == 1) 
                                {
                                    if($this->input->post('bomID')) {
                                        $bomID = $this->input->post('bomID');
                                        $overheadIDs = $this->input->post('overHeadID')[$key];
                                        $totalHours = $this->db->query("SELECT totalHours FROM srp_erp_mfq_bom_overhead
                                        WHERE bomMasterID = {$bomID} AND overheadID = {$overheadIDs}")->row('totalHours');
    
                                        if(($totalHours) && $totalHours > 0) {
                                            $hourlyRate = $this->input->post('oh_hourlyRate')[$key];
                                            //$now = time();
                                            $startDate = strtotime($this->input->post('jobCardStartDate'));
                                            $now = strtotime(current_date(false));
                                            $datediff = $now - $startDate;
                                            $days = round($datediff / (60 * 60 * 24));
                                            $includeCurrentDate = $this->input->post('includeCurrentDate');
                                            if($includeCurrentDate == 2) {
                                                $days += 1;
                                            }
                                            $usageHours = ($totalHours * $days) + $this->input->post('oh_usageHours')[$key];
                                            $totalValue = $usageHours * $hourlyRate;
        
                                            $this->db->set('jobID', $this->input->post('workProcessID'));
                                            $this->db->set('jobDetailID', $overheID);
                                            $this->db->set('jobCardID', $last_id);
                                            $this->db->set('typeMasterAutoID', $this->input->post('overHeadID')[$key]);
                                            $this->db->set('usageAmount', $totalHours * $days);
                                            $this->db->set('linkedDocumentID', 'FORMUNLA');
                                            $this->db->set('linkedDocumentAutoID', null);
                                            $this->db->set('companyID', current_companyID());
                                            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                            $this->db->set('createdUserID', current_userID());
                                            $this->db->set('createdUserName', current_user());
                                            $this->db->set('createdDateTime', current_date(true));
                                            $this->db->set('typeID', 3);
                                            $this->db->insert('srp_erp_mfq_jc_usage');
        
                                            $this->db->query("UPDATE srp_erp_mfq_jc_overhead 
                                                                SET usageHours = {$usageHours}, totalValue = totalValue + $totalValue
                                                                WHERE jcOverHeadID = {$overheID}"
                                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $jcMachineID = $this->input->post('jcMachineID');
                $mfq_faID = $this->input->post('mfq_faID');
                if (!empty($mfq_faID)) {
                    foreach ($mfq_faID as $key => $val) 
                    {
                        $dailyUpdate = $this->input->post('Daily_machine')[$key];
                        if (!empty($jcMachineID[$key])) {
                            $this->db->set('jobCardID', $last_id);
                            $this->db->set('workProcessID', $this->input->post('workProcessID'));

                            $this->db->set('mfq_faID', $this->input->post('mfq_faID')[$key]);
                            /*$this->db->set('activityCode', $this->input->post('mc_activityCode')[$key]);*/
                            $this->db->set('uomID', $this->input->post('mc_uomID')[$key] == "" ? NULL : $this->input->post('mc_uomID')[$key]);
                            $this->db->set('segmentID', $this->input->post('mc_segmentID')[$key]);
                            $this->db->set('hourlyRate', $this->input->post('mc_hourlyRate')[$key]);
                            $this->db->set('totalHours', $this->input->post('mc_totalHours')[$key]);
                            $this->db->set('usageHours', $this->input->post('mc_usageHours')[$key] == "" ? 0 : $this->input->post('mc_usageHours')[$key]);
                            $this->db->set('totalValue', $this->input->post('mc_totalValue')[$key]);
                            $this->db->set('dayComputation', $dailyUpdate);

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

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('jcMachineID', $jcMachineID[$key]);
                            $result = $this->db->update('srp_erp_mfq_jc_machine');

                            if($status == 1 && $dailyUpdate == 1) 
                            {
                                if($this->input->post('bomID')) {
                                    $bomID = $this->input->post('bomID');
                                    $mfq_faIDs = $this->input->post('mfq_faID')[$key];
                                    $totalHours = $this->db->query("SELECT totalHours FROM srp_erp_mfq_bom_machine
                                    WHERE bomMasterID = {$bomID} AND mfq_faID = {$mfq_faIDs}")->row('totalHours');

                                    if(($totalHours) && $totalHours > 0) {
                                        $hourlyRate = $this->input->post('mc_hourlyRate')[$key];
                                        //$now = time();
                                        $startDate = strtotime($this->input->post('jobCardStartDate'));
                                        $now = strtotime(current_date(false));
                                        $datediff = $now - $startDate;
                                        $days = round($datediff / (60 * 60 * 24));
                                        $includeCurrentDate = $this->input->post('includeCurrentDate');
                                        if($includeCurrentDate == 2) {
                                            $days += 1;
                                        }
                                        $usageHours = ($totalHours * $days) + (float)$this->input->post('mc_usageHours')[$key];;
                                        $totalValue = $usageHours * $hourlyRate;
        
                                        $this->db->set('jobID', $this->input->post('workProcessID'));
                                        $this->db->set('jobDetailID', $jcMachineID[$key]);
                                        $this->db->set('jobCardID', $last_id);
                                        $this->db->set('typeMasterAutoID', $this->input->post('mfq_faID')[$key]);
                                        $this->db->set('usageAmount', $totalHours * $days);
                                        $this->db->set('linkedDocumentID', 'FORMUNLA');
                                        $this->db->set('linkedDocumentAutoID', null);
                                        $this->db->set('companyID', current_companyID());
                                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                        $this->db->set('createdUserID', current_userID());
                                        $this->db->set('createdUserName', current_user());
                                        $this->db->set('createdDateTime', current_date(true));
                                        $this->db->set('typeID', 4);
                                        $this->db->insert('srp_erp_mfq_jc_usage');
        
                                        $this->db->query("UPDATE srp_erp_mfq_jc_machine 
                                                            SET usageHours = {$usageHours}, totalValue = totalValue + $totalValue
                                                            WHERE jcMachineID = {$jcMachineID[$key]}"
                                                        );
                                    }
                                }
                            }
                        } else {
                            if (!empty($mfq_faID[$key])) {
                                $this->db->set('mfq_faID', $this->input->post('mfq_faID')[$key]);
                                /*$this->db->set('activityCode', $this->input->post('mc_activityCode')[$key]);*/
                                $this->db->set('uomID', $this->input->post('mc_uomID')[$key] == "" ? NULL : $this->input->post('mc_uomID')[$key]);
                                $this->db->set('segmentID', $this->input->post('mc_segmentID')[$key]);
                                $this->db->set('hourlyRate', $this->input->post('mc_hourlyRate')[$key]);
                                $this->db->set('totalHours', $this->input->post('mc_totalHours')[$key]);
                                $this->db->set('usageHours', $this->input->post('mc_usageHours')[$key] == "" ? 0 : $this->input->post('mc_usageHours')[$key]);
                                $this->db->set('totalValue', $this->input->post('mc_totalValue')[$key]);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('dayComputation', $dailyUpdate);
                                $this->db->set('workProcessID', $this->input->post('workProcessID'));
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
                                $result = $this->db->insert('srp_erp_mfq_jc_machine');
                                $machinDeID = $this->db->insert_id();

                                if($status == 1 && $dailyUpdate == 1) 
                                {
                                    if($this->input->post('bomID')) {
                                        $bomID = $this->input->post('bomID');
                                        $mfq_faIDs = $this->input->post('mfq_faID')[$key];
                                        $totalHours = $this->db->query("SELECT totalHours FROM srp_erp_mfq_bom_machine
                                        WHERE bomMasterID = {$bomID} AND mfq_faID = {$mfq_faIDs}")->row('totalHours');
    
                                        if(($totalHours) && $totalHours > 0) {
                                            $hourlyRate = $this->input->post('mc_hourlyRate')[$key];
                                            //$now = time();
                                            $startDate = strtotime($this->input->post('jobCardStartDate'));
                                            $now = strtotime(current_date(false));
                                            $datediff = $now - $startDate;
                                            $days = round($datediff / (60 * 60 * 24));
                                            $includeCurrentDate = $this->input->post('includeCurrentDate');
                                            if($includeCurrentDate == 2) {
                                                $days += 1;
                                            }
                                            $usageHours = ($totalHours * $days) + (float)$this->input->post('mc_usageHours')[$key];
                                            $totalValue = $usageHours * $hourlyRate;
        
                                            $this->db->set('jobID', $this->input->post('workProcessID'));
                                            $this->db->set('jobDetailID', $machinDeID);
                                            $this->db->set('jobCardID', $last_id);
                                            $this->db->set('typeMasterAutoID', $this->input->post('mfq_faID')[$key]);
                                            $this->db->set('usageAmount', $totalHours * $days);
                                            $this->db->set('linkedDocumentID', 'FORMUNLA');
                                            $this->db->set('linkedDocumentAutoID', null);
                                            $this->db->set('companyID', current_companyID());
                                            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                            $this->db->set('createdUserID', current_userID());
                                            $this->db->set('createdUserName', current_user());
                                            $this->db->set('createdDateTime', current_date(true));
                                            $this->db->set('typeID', 4);
                                            $this->db->insert('srp_erp_mfq_jc_usage');
        
                                            $this->db->query("UPDATE srp_erp_mfq_jc_machine 
                                                                SET usageHours = {$usageHours}, totalValue = totalValue + $totalValue
                                                                WHERE jcMachineID = {$machinDeID}"
                                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                /**Third Party Services Start**/
                $jcOverHeadIDthirdparty = $this->input->post('jcthirdpartyservice');
                $overHeadID = $this->input->post('tpsID');
                if (!empty($overHeadID)) 
                {
                    foreach ($overHeadID as $key => $val)
                    {
                        $dailyUpdate = $this->input->post('Daily_thirdParty')[$key];
                        if (!empty($jcOverHeadIDthirdparty[$key])) {
                            $this->db->set('jobCardID', $last_id);
                            $this->db->set('workProcessID', $this->input->post('workProcessID'));

                            $this->db->set('overHeadID', $this->input->post('tpsID')[$key]);
                            /*$this->db->set('activityCode', $this->input->post('oh_activityCode')[$key]);*/
                            $this->db->set('uomID', $this->input->post('tps_uomID')[$key] == "" ? NULL : $this->input->post('tps_uomID')[$key]);
                            $this->db->set('hourlyRate', $this->input->post('tps_hourlyRate')[$key]);
                            $this->db->set('totalHours', $this->input->post('tps_totalHours')[$key]);
                            $this->db->set('totalValue', $this->input->post('tps_totalValue')[$key]);
                            $this->db->set('usageHours', $this->input->post('tps_usageHours')[$key]);
                            $this->db->set('dayComputation', $dailyUpdate);
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

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('jcOverHeadID', $jcOverHeadIDthirdparty[$key]);
                            $result = $this->db->update('srp_erp_mfq_jc_overhead');

                            if($status == 1 && $dailyUpdate == 1) 
                            {
                                if($this->input->post('bomID')) {
                                    $bomID = $this->input->post('bomID');
                                    $tpsIDs = $this->input->post('tpsID')[$key];
                                    $totalHours = $this->db->query("SELECT totalHours FROM srp_erp_mfq_bom_overhead
                                    WHERE bomMasterID = {$bomID} AND overheadID = {$tpsIDs}")->row('totalHours');

                                    if(($totalHours) && $totalHours > 0) {
                                        $hourlyRate = $this->input->post('tps_hourlyRate')[$key];
                                        //$now = time();
                                        $startDate = strtotime($this->input->post('jobCardStartDate'));
                                        $now = strtotime(current_date(false));
                                        $datediff = $now - $startDate;
                                        $days = round($datediff / (60 * 60 * 24));
                                        $includeCurrentDate = $this->input->post('includeCurrentDate');
                                        if($includeCurrentDate == 2) {
                                            $days += 1;
                                        }
                                        $usageHours = ($totalHours * $days) + $this->input->post('tps_usageHours')[$key];
                                        $totalValue = $usageHours * $hourlyRate;
        
                                        $this->db->set('jobID', $this->input->post('workProcessID'));
                                        $this->db->set('jobDetailID', $jcOverHeadIDthirdparty[$key]);
                                        $this->db->set('jobCardID', $last_id);
                                        $this->db->set('typeMasterAutoID', $this->input->post('tpsID')[$key]);
                                        $this->db->set('usageAmount', $totalHours * $days);
                                        $this->db->set('linkedDocumentID', 'FORMUNLA');
                                        $this->db->set('linkedDocumentAutoID', null);
                                        $this->db->set('companyID', current_companyID());
                                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                        $this->db->set('createdUserID', current_userID());
                                        $this->db->set('createdUserName', current_user());
                                        $this->db->set('createdDateTime', current_date(true));
                                        $this->db->set('typeID', 5);
                                        $this->db->insert('srp_erp_mfq_jc_usage');
        
                                        $this->db->query("UPDATE srp_erp_mfq_jc_overhead 
                                                            SET usageHours = {$usageHours}, totalValue = totalValue + $totalValue
                                                            WHERE jcOverHeadID = {$jcOverHeadIDthirdparty[$key]}"
                                                        );
                                    }
                                }
                            }
                        } else {
                            if (!empty($overHeadID[$key])) {
                                $this->db->set('overHeadID', $this->input->post('tpsID')[$key]);
                                /*$this->db->set('activityCode', $this->input->post('oh_activityCode')[$key]);*/
                                $this->db->set('uomID', $this->input->post('tps_uomID')[$key] == "" ? NULL : $this->input->post('tps_uomID')[$key]);
                                $this->db->set('hourlyRate', $this->input->post('tps_hourlyRate')[$key]);
                                $this->db->set('totalHours', $this->input->post('tps_totalHours')[$key]);
                                $this->db->set('totalValue', $this->input->post('tps_totalValue')[$key]);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('usageHours', $this->input->post('tps_usageHours')[$key]);
                                $this->db->set('dayComputation', $dailyUpdate);
                                $this->db->set('workProcessID', $this->input->post('workProcessID'));
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
                                $result = $this->db->insert('srp_erp_mfq_jc_overhead');
                                $thirdPaID = $this->db->insert_id();

                                if($status == 1 && $dailyUpdate == 1) 
                                {
                                    if($this->input->post('bomID')) {
                                        $bomID = $this->input->post('bomID');
                                        $tpsIDs = $this->input->post('tpsID')[$key];
                                        $totalHours = $this->db->query("SELECT totalHours FROM srp_erp_mfq_bom_overhead
                                        WHERE bomMasterID = {$bomID} AND overheadID = {$tpsIDs}")->row('totalHours');
    
                                        if(($totalHours) && $totalHours > 0) {
                                            $hourlyRate = $this->input->post('tps_hourlyRate')[$key];
                                            //$now = time();
                                            $startDate = strtotime($this->input->post('jobCardStartDate'));
                                            $now = strtotime(current_date(false));
                                            $datediff = $now - $startDate;
                                            $days = round($datediff / (60 * 60 * 24));
                                            $includeCurrentDate = $this->input->post('includeCurrentDate');
                                            if($includeCurrentDate == 2) {
                                                $days += 1;
                                            }
                                            $usageHours = ($totalHours * $days) + $this->input->post('tps_usageHours')[$key];
                                            $totalValue = $usageHours * $hourlyRate;
            
                                            $this->db->set('jobID', $this->input->post('workProcessID'));
                                            $this->db->set('jobDetailID', $thirdPaID);
                                            $this->db->set('jobCardID', $last_id);
                                            $this->db->set('typeMasterAutoID', $this->input->post('tpsID')[$key]);
                                            $this->db->set('usageAmount', $totalHours * $days);
                                            $this->db->set('linkedDocumentID', 'FORMUNLA');
                                            $this->db->set('linkedDocumentAutoID', null);
                                            $this->db->set('companyID', current_companyID());
                                            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                            $this->db->set('createdUserID', current_userID());
                                            $this->db->set('createdUserName', current_user());
                                            $this->db->set('createdDateTime', current_date(true));
                                            $this->db->set('typeID', 5);
                                            $this->db->insert('srp_erp_mfq_jc_usage');
            
                                            $this->db->query("UPDATE srp_erp_mfq_jc_overhead 
                                                                SET usageHours = {$usageHours}, totalValue = totalValue + $totalValue
                                                                WHERE jcOverHeadID = {$thirdPaID}"
                                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                /**Third Party Services End**/


                if ($this->input->post('status') == 1) {
                    $this->db->set('status', $this->input->post('status'));
                    $this->db->where('workFlowID', $this->input->post('workFlowID'));
                    $this->db->where('jobID', $this->input->post('workProcessID'));
                    $this->db->where('templateDetailID', $this->input->post('templateDetailID'));
                    $result = $this->db->update('srp_erp_mfq_workflowstatus');
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Job Card Saved Failed ' . $this->db->_error_message());

                } else {
                    $this->db->trans_commit();
                    return array('s', 'Job Card Saved Successfully.', $last_id);
                }
            }
        } else {
            return array('w', 'There are pending related job cards to be closed');
        }
    }

    function calculateDailyComputation_overhead()
    {
        $usageHours = 0;
        $jcOverHeadID = $this->input->post('jcOverHeadID');
        $bomOverheadID = $this->input->post('bomOverheadID');
        $value = $this->input->post('value');
        //$now = time();
        $startDate = strtotime($this->input->post('startDate'));
        $now = strtotime(current_date(false));
        $datediff = $now - $startDate;
        $days = round($datediff / (60 * 60 * 24));
        $includeCurrentDate = $this->input->post('includeCurrentDate');
        if($includeCurrentDate == 2) {
            $days += 1;
        }

        $this->db->select('srp_erp_mfq_jc_overhead.*, status');
        $this->db->from('srp_erp_mfq_jc_overhead');
        $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jobcardmaster.jobcardID = srp_erp_mfq_jc_overhead.jobCardID', 'LEFT');
        $this->db->join('srp_erp_mfq_workflowstatus', 'srp_erp_mfq_workflowstatus.templateDetailID = srp_erp_mfq_jobcardmaster.templateDetailID AND srp_erp_mfq_jobcardmaster.workProcessID = srp_erp_mfq_workflowstatus.jobID', 'LEFT');
        $this->db->where('jcOverHeadID', $jcOverHeadID);
        $master = $this->db->get()->row_array();

        if($master) {
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_bom_overhead');
            $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jobcardmaster.bomID = srp_erp_mfq_bom_overhead.bomMasterID');
            $this->db->where('workProcessID', $master['workProcessID']);
            $this->db->where('jobcardID', $master['jobCardID']);
            $this->db->where('overheadID', $master['overHeadID']);
            $bomMaster = $this->db->get()->row('totalHours');

            $usageHours = (int)$master['usageHours'];
            if($master['status'] == 1) {
                return 0;
            }
            if($value == 1) {
                $usageHours += ($bomMaster * $days);
            }
        } else {
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_bom_overhead');
            $this->db->where('bomOverheadID', $bomOverheadID);
            $bomMaster = $this->db->get()->row_array();
            if($value == 1) {
                $usageHours = ($bomMaster['totalHours'] * $days);
            }
        }
        return $usageHours;
    }

    function calculateDailyComputation_machine()
    {
        $usageHours = 0;
        $jcMachineID = $this->input->post('jcMachineID');
        $bomMachineID = $this->input->post('bomMachineID');
        $value = $this->input->post('value');
        //$now = time();
        $startDate = strtotime($this->input->post('startDate'));
        $now = strtotime(current_date(false));
        $datediff = $now - $startDate;
        $days = round($datediff / (60 * 60 * 24));
        $includeCurrentDate = $this->input->post('includeCurrentDate');
        if($includeCurrentDate == 2) {
            $days += 1;
        }

        $this->db->select('srp_erp_mfq_jc_machine.*, status');
        $this->db->from('srp_erp_mfq_jc_machine');
        $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jobcardmaster.jobcardID = srp_erp_mfq_jc_machine.jobCardID', 'LEFT');
        $this->db->join('srp_erp_mfq_workflowstatus', 'srp_erp_mfq_workflowstatus.templateDetailID = srp_erp_mfq_jobcardmaster.templateDetailID AND srp_erp_mfq_jobcardmaster.workProcessID = srp_erp_mfq_workflowstatus.jobID', 'LEFT');
        $this->db->where('jcMachineID', $jcMachineID);
        $master = $this->db->get()->row_array();

        if($master) {
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_bom_machine');
            $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jobcardmaster.bomID = srp_erp_mfq_bom_machine.bomMasterID');
            $this->db->where('workProcessID', $master['workProcessID']);
            $this->db->where('jobcardID', $master['jobCardID']);
            $this->db->where('mfq_faID', $master['mfq_faID']);
            $bomMaster = $this->db->get()->row('totalHours');

            $usageHours = (int)$master['usageHours'];
            if($master['status'] == 1) {
                return $usageHours;
            }
            if($value == 1) {
                $usageHours += ($bomMaster * $days);
            }
        } else {
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_bom_machine');
            $this->db->where('bomMachineID', $bomMachineID);
            $bomMaster = $this->db->get()->row_array();
            if($value == 1) {
                $usageHours = ($bomMaster['totalHours'] * $days);
            }
        }
        return $usageHours;
    }

    function calculateDailyComputation_labourTask()
    {
        $usageHours = 0;
        $jcLabourTaskID = $this->input->post('jcLabourTaskID');
        $bomLabourTaskID = $this->input->post('bomLabourTaskID');
        $value = $this->input->post('value');
        //$now = time();
        $startDate = strtotime($this->input->post('startDate'));
        $now = strtotime(current_date(false));
        $datediff = $now - $startDate;
        $days = round($datediff / (60 * 60 * 24));
        $includeCurrentDate = $this->input->post('includeCurrentDate');
        if($includeCurrentDate == 2) {
            $days += 1;
        }

        $this->db->select('srp_erp_mfq_jc_labourtask.*, status');
        $this->db->from('srp_erp_mfq_jc_labourtask');
        $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jobcardmaster.jobcardID = srp_erp_mfq_jc_labourtask.jobCardID', 'LEFT');
        $this->db->join('srp_erp_mfq_workflowstatus', 'srp_erp_mfq_workflowstatus.templateDetailID = srp_erp_mfq_jobcardmaster.templateDetailID AND srp_erp_mfq_jobcardmaster.workProcessID = srp_erp_mfq_workflowstatus.jobID', 'LEFT');
        $this->db->where('jcLabourTaskID', $jcLabourTaskID);
        $master = $this->db->get()->row_array();

        if($master) {
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_bom_labourtask');
            $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jobcardmaster.bomID = srp_erp_mfq_bom_labourtask.bomMasterID');
            $this->db->where('workProcessID', $master['workProcessID']);
            $this->db->where('jobcardID', $master['jobCardID']);
            $this->db->where('labourTask', $master['labourTask']);
            $bomMaster = $this->db->get()->row('totalHours');

            $usageHours = (int)$master['usageHours'];
            if($master['status'] == 1) {
                return $usageHours;
            }
            if($value == 1) {
                $usageHours += ($bomMaster * $days);
            }
        } else {
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_bom_labourtask');
            $this->db->where('bomLabourTaskID', $bomLabourTaskID);
            $bomMaster = $this->db->get()->row_array();
            if($value == 1) {
                $usageHours = (($bomMaster['totalHours']) * $days);
            }
        }
        return $usageHours;
    }

    function calculateDailyComputation_materialCharge()
    {
        $usageHours = 0;
        $includeCurrentDate = $this->input->post('includeCurrentDate');
        $jcMaterialConsumptionID = $this->input->post('jcMaterialConsumptionID');
        $bomMaterialConsumptionID = $this->input->post('bomMaterialConsumptionID');
        $value = $this->input->post('value');
//        $now = time();
        $startDate = strtotime($this->input->post('startDate'));
        $now = strtotime(current_date(false));
        $datediff = $now - $startDate;

        $days = round($datediff / (60 * 60 * 24));
        if($includeCurrentDate == 2) {
            $days += 1;
        }


        $this->db->select('srp_erp_mfq_jc_materialconsumption.*, status');
        $this->db->from('srp_erp_mfq_jc_materialconsumption');
        $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jobcardmaster.jobcardID = srp_erp_mfq_jc_materialconsumption.jobCardID', 'LEFT');
        $this->db->join('srp_erp_mfq_workflowstatus', 'srp_erp_mfq_workflowstatus.templateDetailID = srp_erp_mfq_jobcardmaster.templateDetailID AND srp_erp_mfq_jobcardmaster.workProcessID = srp_erp_mfq_workflowstatus.jobID', 'LEFT');
        $this->db->where('jcMaterialConsumptionID', $jcMaterialConsumptionID);
        $master = $this->db->get()->row_array();

        if($master) {
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_bom_materialconsumption');
            $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jobcardmaster.bomID = srp_erp_mfq_bom_materialconsumption.bomMasterID');
            $this->db->where('workProcessID', $master['workProcessID']);
            $this->db->where('jobcardID', $master['jobCardID']);
            $this->db->where('mfqItemID', $master['mfqItemID']);
            $bomMaster = $this->db->get()->row('qtyUsed');
            
            $usageHours = (int)$master['usageQty'];
            if($master['status'] == 1) {
                return $usageHours;
            }
            if($value == 1) {
                if($bomMaster) {
                    $usageHours += ($bomMaster * $days);
                }
            }
        } else {
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_bom_materialconsumption');
            $this->db->where('bomMaterialConsumptionID', $bomMaterialConsumptionID);
            $bomMaster = $this->db->get()->row_array();
            if($value == 1) {
                $usageHours = (($bomMaster['qtyUsed']) * $days);
            }
        }
        return $usageHours;
    }

    function fetch_bom_process_based()
    {
        $workFlowTemplateID = $this->input->post('workFlowTemplateID');
        $this->db->select("bomMasterID");
        $this->db->from('srp_erp_mfq_billofmaterial');
        if(!empty($workFlowTemplateID)) {
            $this->db->where('mfqProcessID', $workFlowTemplateID);
        }
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $bom = $this->db->get()->row('bomMasterID');

        return (int)$bom;
    }

    function get_inventory_item_batch(){
        $item = $this->input->post('item');
        $qty = $this->input->post('qty');

        $this->db->where('mfqItemID',$item);
        $mfq_item_detail = $this->db->from('srp_erp_mfq_itemmaster')->get()->row_array();

        if($mfq_item_detail){

            $itemAutoID = $mfq_item_detail['itemAutoID'];

            // if(empty($itemAutoID)){
            //     return array('e','Item is not linked with ERP to Assign any batches');
            // }

            $this->db->where('itemMasterID',$itemAutoID);
            $itemBatches = $this->db->from('srp_erp_inventory_itembatch')->get()->result_array();

            // if(count($itemBatches) > 0){
            //     return array('e','Item is not containing any batches');
            // }

            return $itemBatches;
        }

        // if($qty <= 0){
        //     return array('e','Estimated Quantity is Required');
        // }




    }

    function save_late_overhead_cost_job(){
        $jcOverHeadID = $this->input->post('jcOverHeadID');
                $overHeadID = $this->input->post('overHeadID');
                if ($overHeadID) {
                   
                    $this->db->set('jobCardID', $this->input->post('jobCardNo'));
                    $this->db->set('overHeadID', $this->input->post('overHeadID'));
                    /*$this->db->set('activityCode', $this->input->post('oh_activityCode')[$key]);*/
                    $this->db->set('uomID', $this->input->post('uomID') == "" ? NULL : $this->input->post('uomID'));
                    $this->db->set('segmentID', $this->input->post('segmentID'));
                    $this->db->set('hourlyRate', $this->input->post('oh_hourRate'));
                    $this->db->set('totalHours', $this->input->post('oh_totalHours'));
                    $this->db->set('usageHours', $this->input->post('oh_usageHours'));
                    $this->db->set('totalValue', $this->input->post('oh_totalValue'));
                    
                    $this->db->set('workProcessID', $this->input->post('jobNo'));
                    
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
                    $this->db->set('isLateCost', 1);
                    $result = $this->db->insert('srp_erp_mfq_jc_overhead');

                            
                }

                $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('e', 'Saved Failed ');
            } else {
                $this->session->set_flashdata('s', 'Saved Successfully.');
                $this->db->trans_commit();
                return array('s' ,'Saved Successfully.');
            }
    }
}