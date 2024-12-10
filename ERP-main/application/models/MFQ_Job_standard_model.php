<?php

class MFQ_Job_standard_model extends ERP_Model
{

    function save_standard_job_header()
    {
        $date_format_policy = date_format_policy();
        $companyid = current_companyID();
        $productiondate = $this->input->post('productiondate');
        $expirydate = $this->input->post('expirydate');
        $batchno = $this->input->post('batchno');
        $narration = $this->input->post('narration');
        $company_code = $this->common_data['company_data']['company_code'];
        $mfqWarehouseAutoID = $this->input->post('mfqWarehouseAutoID');
        $mfqSegmentID = $this->input->post('mfqSegmentID');
        $mfqBomID = $this->input->post('mfqBomID');

        $format_productiondate = null;
        if (isset($productiondate) && !empty($productiondate)) {
            $format_productiondate = input_format_date($productiondate, $date_format_policy);
        }
        $format_expiryDate = null;
        if (isset($expirydate) && !empty($expirydate)) {
            $format_expiryDate = input_format_date($expirydate, $date_format_policy);
        }

        $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM `srp_erp_mfq_standardjob` WHERE companyID={$companyid}")->row_array();
        $data['serialNo'] = $serial['serialNo'];
        $data['documentSystemCode'] = ($company_code . '/' . 'STJOB' . str_pad($data['serialNo'], 6, '0', STR_PAD_LEFT));

        $data['documentDate'] = $format_productiondate;
        $data['expiryDate'] = $format_expiryDate;
        $data['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalExchangeRate'] = 1;
        $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['batchNumber'] = $batchno;
        $data['narration'] = $narration;
        $data['warehouseID'] = $mfqWarehouseAutoID;
        $data['bomID'] = $mfqBomID;
        $data['segmentID'] = $mfqSegmentID;
        $data['companyID'] = $companyid;

        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $this->db->insert('srp_erp_mfq_standardjob', $data);
        $last_id = $this->db->insert_id();

        if($mfqBomID){
            //BOM selected
            $res = $this->save_bom_details($last_id,$mfqBomID);

        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Standard Job Saved failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Standard Job Saved successfully.', $last_id);

        }
    }

    function save_bom_details($job_card_id = 1,$bom_id = 1){

        //get manufature bill of material consumptions
        $bill_of_consumptions = fetch_bom_materialconsumptions($bom_id);
        $bill_of_labour = fetch_bom_billOfLabour($bom_id);
        $bill_of_overhead = fetch_bom_billOverHead($bom_id);

        $bill_of_itemautoid_arr = array();
        $unit_of_messureID = array();
        $unit_of_messure = array();
        $qunty_arr = array();
        $unit_cost_arr = array();
        $search_arr = array();

        $labour_auto_arr = array();
        $search_lablour_arr = array();
        $UnitOfMeasureIDLabour_arr = array();
        $UOMLabour_arr = array();
        $glautoidlabour_arr = array();
        $unitrate_arr = array();
        $usagehours_arr = array();
        $totalhours_arr = array();

        $searchoverhead_arr = array();
        $overheadautoid_arr = array();
        $glautoidoverhead_arr = array();
        $UOMoverhead_arr = array();
        $UnitOfMeasureIDoverhead_arr = array();
        $unitrateoverhead_arr = array();
        $usagehoursoverhead_arr = array();
        $totalhoursoverhead_arr = array();


        $total_input = 0;

        foreach($bill_of_consumptions as $bill_con_values){
            $bill_of_itemautoid_arr[] = $bill_con_values['itemAutoID'];
            $unit_of_messureID[] = $bill_con_values['defaultUnitOfMeasureID'];
            $unit_of_messure[] = $bill_con_values['defaultUnitOfMeasure'];
            $qunty_arr[] = $bill_con_values['qtyUsed'];
            $unit_cost_arr[] = $bill_con_values['companyLocalWacAmount'];
            $total_cost_value = $bill_con_values['qtyUsed'] * $bill_con_values['companyLocalWacAmount'];
            $total_input += $total_cost_value; 
            $total_cost_arr[] = $total_cost_value;
            $search_arr[] = $bill_con_values['barcode'].' | '.$bill_con_values['secondaryItemCode'].' | '.$bill_con_values['itemDescription'];
        }

        foreach($bill_of_labour as $bill_labour_values){
          
            $labour_auto_arr[] = $bill_labour_values['labourTask'];
            $search_lablour_arr[] = $bill_labour_values['description'].' ('.$bill_labour_values['overHeadCode'].')';
            $UnitOfMeasureIDLabour_arr[] = $bill_labour_values['unitOfMeasureID'];
            $UOMLabour_arr[] = $bill_labour_values['UnitShortCode'];
            $glautoidlabour_arr[] = $bill_labour_values['financeGLAutoID'];
            $unitrate_arr[] = $bill_labour_values['hourlyRate'];
            $usagehours_arr[] = $bill_labour_values['totalHours'];
            $totalhours_arr[] = $bill_labour_values['totalValue'];

            $total_input += $bill_labour_values['totalValue']; 
           
        }

        foreach($bill_of_overhead as $bill_overhead_values){

            $searchoverhead_arr[] = $bill_overhead_values['description'].' ('.$bill_overhead_values['overHeadCode'].')';
            $overheadautoid_arr[] = $bill_overhead_values['overheadID'];
            $glautoidoverhead_arr[] = $bill_overhead_values['financeGLAutoID'];
            $UOMoverhead_arr[] = $bill_overhead_values['UnitShortCode'];
            $UnitOfMeasureIDoverhead_arr[] = $bill_overhead_values['unitOfMeasureID'];
            $unitrateoverhead_arr[] = $bill_overhead_values['hourlyRate'];
            $usagehoursoverhead_arr[] = $bill_overhead_values['totalHours'];
            $totalhoursoverhead_arr[] = $bill_overhead_values['totalValue'];
            
            $total_input += $bill_overhead_values['totalValue']; 
        }

        $bill_of_base_arr = array('standardjobcardAutoid'=>$job_card_id,'itemautoid'=>$bill_of_itemautoid_arr,'defaultUnitOfMeasureID'=>$unit_of_messureID,'defaultUnitOfMeasure'=>$unit_of_messure,
            'unitcost'=>$unit_cost_arr,'Qty'=>$qunty_arr,'totalcost'=>$total_cost_arr,'search'=>$search_arr,'totalinput'=>$total_input,'labourautoid'=>$labour_auto_arr,
            'searchlabour'=>$search_lablour_arr,'UnitOfMeasureIDLabour'=>$UnitOfMeasureIDLabour_arr,'unitrate'=>$unitrate_arr,'usagehours'=>$usagehours_arr,'totalhours'=>$totalhours_arr,
            'UOMLabour'=>$UOMLabour_arr,'glautoidlabour'=>$glautoidlabour_arr,'searchoverhead'=>$searchoverhead_arr,'overheadautoid'=>$overheadautoid_arr,'glautoidoverhead'=>$glautoidoverhead_arr,
            'UOMoverhead'=>$UOMoverhead_arr,'UnitOfMeasureIDoverhead'=>$UnitOfMeasureIDoverhead_arr,'unitrateoverhead'=>$unitrateoverhead_arr,'usagehoursoverhead'=>$usagehoursoverhead_arr,
            'totalhoursoverhead'=>$totalhoursoverhead_arr);

        $res = $this->save_mfq_sd_job_input(true,$bill_of_base_arr);

        return TRUE;

    }

    function fetch_mfq_standard_item()
    {
        $dataArr = array();
        $dataArr2 = array();
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";


        $sql = 'SELECT
            srp_erp_mfq_itemmaster.itemAutoID,
            srp_erp_mfq_itemmaster.secondaryItemCode,
            srp_erp_mfq_itemmaster.itemDescription,
            srp_erp_mfq_itemmaster.defaultUnitOfMeasureID,
            CONCAT( IFNULL(srp_erp_mfq_itemmaster.itemSystemCode, "" ), " | ",IFNULL(srp_erp_mfq_itemmaster.secondaryItemCode, "" ), " | ", IFNULL(srp_erp_mfq_itemmaster.itemDescription, "" ) ) AS `Match`,
            IFNULL(srp_erp_itemmaster.companyLocalWacAmount,0)  as wacamount,
            srp_erp_unit_of_measure.UnitDes,
            srp_erp_itemmaster.secondaryUOMID,
            suom.UnitDes AS suomDes
        FROM
            srp_erp_mfq_itemmaster
            LEFT JOIN srp_erp_itemmaster on srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID
            LEFT JOIN srp_erp_unit_of_measure on srp_erp_unit_of_measure.UnitID = srp_erp_itemmaster.defaultUnitOfMeasureID
            LEFT JOIN srp_erp_unit_of_measure suom ON suom.UnitID = srp_erp_itemmaster.secondaryUOMID 
        WHERE
            srp_erp_mfq_itemmaster.companyID = "' . $companyID . '"
            AND srp_erp_mfq_itemmaster.itemAutoID IS NOT NULL
            AND (srp_erp_mfq_itemmaster.itemType = "1" or srp_erp_mfq_itemmaster.itemType = "3")
            AND srp_erp_mfq_itemmaster.isActive = "1"
            AND(srp_erp_mfq_itemmaster.secondaryItemCode LIKE "' . $search_string . '" OR srp_erp_mfq_itemmaster.itemSystemCode LIKE "' . $search_string . '" OR srp_erp_mfq_itemmaster.itemDescription LIKE "' . $search_string . '" OR srp_erp_mfq_itemmaster.itemDescription LIKE "' . $search_string . '")
            ';


        $data = $this->db->query($sql)->result_array();

        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'itemAutoID' => $val['itemAutoID'], 'secondaryItemCode' => $val['secondaryItemCode'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'wacamount' => $val['wacamount'], 'uomdescription' => $val['UnitDes'], 'suomDes' => $val['suomDes'], 'secondaryUOMID' => $val['secondaryUOMID']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function save_mfq_sd_job_input($mat_consumptions = null,$mat_arr = [])
    {
        $this->db->trans_start();

        if($mat_consumptions){

            $standardjobcardAutoid = $mat_arr['standardjobcardAutoid'];;
            $itemautoid = $mat_arr['itemautoid'];
            $UnitOfMeasureID = $mat_arr['defaultUnitOfMeasureID'];
            $Qty = $mat_arr['Qty'];
            $search = $mat_arr['search'];
            $unitcost = $mat_arr['unitcost'];
            $totalcost = $mat_arr['totalcost'];
            $uom = $mat_arr['defaultUnitOfMeasure'];
            $grandtotalinput = $mat_arr['totalinput'];

            $labourautoid =  $mat_arr['labourautoid'];
            $searchlabour =  $mat_arr['searchlabour'];
            $UnitOfMeasureIDLabour =  $mat_arr['UnitOfMeasureIDLabour'];
            $unitrate =  $mat_arr['unitrate'];
            $usagehours =  $mat_arr['usagehours'];
            $totalhours =  $mat_arr['totalhours'];
            $UOMLabour = $mat_arr['UOMLabour']; 
            $glautoidlabour =  $mat_arr['glautoidlabour'];

            $searchoverhead = $mat_arr['searchoverhead'];
            $overheadautoid = $mat_arr['overheadautoid'];
            $glautoidoverhead = $mat_arr['glautoidoverhead'];
            $UOMoverhead = $mat_arr['UOMoverhead'];
            $UnitOfMeasureIDoverhead = $mat_arr['UnitOfMeasureIDoverhead'];
            $unitrateoverhead = $mat_arr['unitrateoverhead'];
            $usagehoursoverhead = $mat_arr['usagehoursoverhead'];
            $totalhoursoverhead = $mat_arr['totalhoursoverhead'];

            $itemautoidoutput = array();

        }else{
            $standardjobcardAutoid = $this->input->post('standardjobcardAutoid');
            $itemautoid = $this->input->post('itemautoid');
            $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
            $Qty = $this->input->post('Qty');
            $search = $this->input->post('search');
            $unitcost = $this->input->post('unitcost');
            $totalcost = $this->input->post('totalcost');
            $uom = $this->input->post('UOM');
            $grandtotalinput =  $this->input->post('totalinput');

            $labourautoid = $this->input->post('labourautoid');
            $searchlabour = $this->input->post('searchlabour');
            $UnitOfMeasureIDLabour = $this->input->post('UnitOfMeasureIDLabour');
            $unitrate = $this->input->post('unitrate');
            $usagehours = $this->input->post('usagehours');
            $totalhours = $this->input->post('totalhours');
            $UOMLabour = $this->input->post('UOMLabour');
            $glautoidlabour = $this->input->post('glautoidlabour');

            $searchoverhead = $this->input->post('searchoverhead');
            $overheadautoid = $this->input->post('overheadautoid');
            $glautoidoverhead = $this->input->post('glautoidoverhead');
            $UOMoverhead = $this->input->post('UOMoverhead');
            $UnitOfMeasureIDoverhead = $this->input->post('UnitOfMeasureIDoverhead');
            $unitrateoverhead = $this->input->post('unitrateoverhead');
            $usagehoursoverhead = $this->input->post('usagehoursoverhead');
            $totalhoursoverhead = $this->input->post('totalhoursoverhead');

            $itemautoidoutput = $this->input->post('itemautoidoutput');
        }

   
        
        $companyID = current_companyID();
        
        $expiryDate = $this->input->post('expiryDate');
        $date_format_policy = date_format_policy();

       // $standardjobcardAutoid = $this->input->post('standardjobcardAutoid');
        
        $UnitOfMeasureIDoutput = $this->input->post('UnitOfMeasureIDoutput');
        $Qtyoutput = $this->input->post('Qtyoutput');
        $searchoutput = $this->input->post('searchoutput');
        $unitcostoutput = $this->input->post('unitcostoutput');
        $totalcostoutput = $this->input->post('totalcostoutput');
        $assetglautoidoutput = $this->input->post('assetglautoid');
        $uomoutput = $this->input->post('UOMoutput');
        $companyID = current_companyID();
        $warehouseoutput = $this->input->post('warehouse');
        $percentageouttput = $this->input->post('percentageoutput');
        $percentageouttput_out = $this->input->post('percentageoutput');
        $warehouseitemtype = $this->input->post('warehouseitemtype');

        $SecondaryUnitOfMeasureID = $this->input->post('SecondaryUnitOfMeasureID');
        $SecondaryQty = $this->input->post('SecondaryQty');
        $suom = $this->input->post('SUOM');
        $suomoutput = $this->input->post('SUOMoutput');
        $secondaryQtyoutput = $this->input->post('SecondaryQtyoutput');
        $seconaryUnitOfMeasureIDoutput = $this->input->post('SeconaryUnitOfMeasureIDoutput');

        $masteroutput = $this->db->query("SELECT warehousemaster.wareHouseAutoID,warehousemaster.wareHouseLocation,warehousemaster.wareHouseDescription FROM `srp_erp_mfq_standardjob_items` sjitemmaster
        LEFT JOIN srp_erp_warehousemaster warehousemaster on sjitemmaster.warehouseAutoID = warehousemaster.wareHouseAutoID where sjitemmaster.companyID = $companyID And type = 2 And jobAutoID = $standardjobcardAutoid ")->result_array();
        $master = $this->db->query("select warehousemaster.wareHouseAutoID,warehousemaster.wareHouseLocation,warehousemaster.wareHouseDescription from srp_erp_mfq_standardjob jobmaster LEFT JOIN srp_erp_mfq_warehousemaster mfqwarehousemaster on mfqwarehousemaster.mfqWarehouseAutoID = jobmaster.warehouseID
        LEFT join srp_erp_warehousemaster warehousemaster on warehousemaster.wareHouseAutoID = mfqwarehousemaster.warehouseAutoID where  jobmaster.companyID = $companyID And jobAutoID = $standardjobcardAutoid")->row_array();

        $mfqstandardjob = $this->db->query("SELECT * FROM `srp_erp_mfq_standardjob` WHERE companyID = $companyID AND jobAutoID = $standardjobcardAutoid")->row_array();

        foreach ($itemautoidoutput as $key => $searchitem) {
            if($searchitem > 0)
            {
                $itemmaster = $this->db->query("SELECT mainCategory,itemSystemCode,itemDescription from srp_erp_itemmaster where companyID = $companyID AND itemAutoID = '{$searchitem}' ")->row_array();
                if ($itemmaster['mainCategory'] != 'Service' && empty($warehouseoutput[$key])) {
                    return array('e', 'Warehouse cannot be empty for selected item <br>['. $itemmaster['itemSystemCode'] . ' - ' . $itemmaster['itemDescription'] .']');
                    exit();
                }
            }

        }

        foreach ($itemautoidoutput as $key => $searchitem1) {
            $expiary = input_format_date($expiryDate[$key], $date_format_policy);

            if($searchitem1 > 0)
            {
                if($this->input->post('expiryDate')[$key]!='')
                {
                if ($expiary < $mfqstandardjob['documentDate']) {
                    return array('e', 'Expiry Date cannot be less than Document Date');
                    exit();
                }
            }
            }

        }

    

            $this->db->delete('srp_erp_mfq_standardjob_items', array('jobAutoID' => $standardjobcardAutoid, 'type' => 2));
            foreach ($itemautoidoutput as $key => $val) {
                if (!empty($itemautoidoutput[$key]) || $itemautoidoutput[$key] != '') {
                    $item_data = fetch_item_data($val);
                    if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                        foreach ($masteroutput as $val1)
                        {

                            $this->db->select('itemAutoID');
                            $this->db->where('itemAutoID', $item_data['itemAutoID']);
                            $this->db->where('wareHouseAutoID', $val1['wareHouseAutoID']);
                            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                            if (empty($warehouseitems)) {
                                $data_arr = array(
                                    'wareHouseAutoID' => $val1['wareHouseAutoID'],
                                    'wareHouseLocation' => $val1['wareHouseLocation'],
                                    'wareHouseDescription' => $val1['wareHouseDescription'],
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

                    }
                    $itemdescriptionoutput = explode('|', $searchoutput[$key]);
                    if(!isset($itemdescriptionoutput[2])){
                        return array('e', 'Item Description for Finish Goods is Invalid!');
                        exit();
                    }
                    if ($warehouseitemtype == 2) {
                        $dataoutput[$key]['glAutoID'] = $assetglautoidoutput[$key];
                    } else {
                        $warehouseexist = $this->db->query("SELECT srp_erp_warehousemaster.wareHouseAutoID,srp_erp_warehousemaster.WIPGLAutoID FROM `srp_erp_warehousemaster` WHERE `srp_erp_warehousemaster`.`companyID` = '13'
	                    AND `srp_erp_warehousemaster`.`wareHouseAutoID` IS NOT NULL  AND `warehouseType` = 2 AND wareHouseAutoID = '{$warehouseoutput[$key]}'")->row_array();
                        if (!empty($warehouseexist) || $warehouseexist != '') {

                            $dataoutput[$key]['glAutoID'] = $warehouseexist['WIPGLAutoID'];
                        } else {
                            $dataoutput[$key]['glAutoID'] = $assetglautoidoutput[$key];
                        }
                    }

                    if((!empty($Qtyoutput[$key]) && !empty($percentageouttput[$key])) || (!empty($grandtotalinput) && !empty($percentageouttput[$key])))
                    {
                        $unicost = ($grandtotalinput / 100 * $percentageouttput[$key]) /$Qtyoutput[$key] ;
                        $totalcostnew = ($grandtotalinput / 100) * $percentageouttput[$key];
                    }else
                    {
                        $unicost = 0;
                        $totalcostnew = 0;
                    }




                    $dataoutput[$key]['jobAutoID'] = $standardjobcardAutoid;
                    $dataoutput[$key]['type'] = 2;
                    $dataoutput[$key]['costAllocationPrc'] = $percentageouttput_out[$key];
                    $dataoutput[$key]['warehouseAutoID'] = $warehouseoutput[$key];
                    $dataoutput[$key]['mfqItemType'] = $warehouseitemtype[$key];
                    $dataoutput[$key]['itemAutoID'] = $itemautoidoutput[$key];
                    if(isset($itemdescriptionoutput[2])){
                        $dataoutput[$key]['description'] = $itemdescriptionoutput[2];
                    }
                   // $dataoutput[$key]['description'] = $itemdescriptionoutput[2];
                    $dataoutput[$key]['uomID'] = $UnitOfMeasureIDoutput[$key];
                    $dataoutput[$key]['unitOfMeasure'] = $uomoutput[$key];
                    if(!empty($expiryDate[$key]))
                    {
                        $dataoutput[$key]['expiryDate'] =  input_format_date($expiryDate[$key], $date_format_policy);
                    }else
                    {
                        $dataoutput[$key]['expiryDate'] = null;
                    }
                    $dataoutput[$key]['qty'] = $Qtyoutput[$key];
                    $dataoutput[$key]['unitCost'] = $unitcostoutput[$key];
                    $dataoutput[$key]['totalCost'] = $totalcostoutput[$key];
                    $dataoutput[$key]['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $dataoutput[$key]['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $dataoutput[$key]['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($dataoutput[$key]['transactionCurrencyID']);
                    $dataoutput[$key]['transactionExchangeRate'] = 1;
                    $dataoutput[$key]['transactionAmount'] = $totalcostoutput[$key];/*round($totalcostoutput[$key], $dataoutput[$key]['transactionCurrencyDecimalPlaces']);*/
                    $dataoutput[$key]['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $dataoutput[$key]['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $dataoutput[$key]['companyLocalExchangeRate'] = 1;
                    $dataoutput[$key]['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
                    $companyLocalAmount = $dataoutput[$key]['transactionAmount'] / $dataoutput[$key]['companyLocalExchangeRate'];
                    $dataoutput[$key]['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $dataoutput[$key]['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $default_currency = currency_conversionID($dataoutput[$key]['transactionCurrencyID'], $dataoutput[$key]['companyLocalCurrencyID']);
                    $dataoutput[$key]['companyLocalExchangeRate'] = $default_currency['conversion'];
                    $dataoutput[$key]['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                    $dataoutput[$key]['companyLocalAmount'] = $companyLocalAmount;/*round($companyLocalAmount, $dataoutput[$key]['companyLocalCurrencyDecimalPlaces']);*/
                    $dataoutput[$key]['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                    $dataoutput[$key]['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                    $reporting_currency = currency_conversionID($dataoutput[$key]['transactionCurrencyID'], $dataoutput[$key]['companyReportingCurrencyID']);
                    $dataoutput[$key]['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $dataoutput[$key]['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                    $companyReportingAmount = $dataoutput[$key]['transactionAmount'] / $dataoutput[$key]['companyReportingExchangeRate'];
                    $dataoutput[$key]['companyReportingAmount'] =$companyReportingAmount;/* round($companyReportingAmount, $dataoutput[$key]['companyReportingCurrencyDecimalPlaces']);*/
                    $dataoutput[$key]['createdUserID'] = $this->common_data['current_userID'];
                    $dataoutput[$key]['createdPCID'] = $this->common_data['current_pc'];
                    $dataoutput[$key]['createdDateTime'] = $this->common_data['current_date'];
                    $dataoutput[$key]['createdUserName'] = $this->common_data['current_user'];
                    $dataoutput[$key]['companyID'] = $companyID;

                    $dataoutput[$key]['suomID'] = $seconaryUnitOfMeasureIDoutput[$key];
                    $dataoutput[$key]['suom'] = $suomoutput [$key];
                    $dataoutput[$key]['suomQty'] = $secondaryQtyoutput[$key];

                    $this->db->insert('srp_erp_mfq_standardjob_items', $dataoutput[$key]);
                }

            }


            $this->db->delete('srp_erp_mfq_standardjob_items', array('jobAutoID' => $standardjobcardAutoid, 'type' => 1));
            foreach ($itemautoid as $key => $val) {
                if (!empty($itemautoid[$key]) || $itemautoid[$key] != '') {

                    $item_data = fetch_item_data($val);

              

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
                    $inputdata = $this->db->query("select srp_erp_warehousemaster.WIPGLAutoID as glautoid from srp_erp_mfq_standardjob LEFT JOIN srp_erp_mfq_warehousemaster on srp_erp_mfq_warehousemaster.mfqWarehouseAutoID = srp_erp_mfq_standardjob.warehouseID
                    LEFT JOIN srp_erp_warehousemaster on srp_erp_warehousemaster.wareHouseAutoID =  srp_erp_mfq_warehousemaster.warehouseAutoID WHERE srp_erp_mfq_standardjob.companyID  = $companyID AND srp_erp_mfq_standardjob.jobAutoID = $standardjobcardAutoid")->row_array();
                    $itemdescription = explode('|', $search[$key]);
                    if(!isset($itemdescription[2])){
                        return array('e', 'Item Description for Raw Material is Invalid!');
                    }
                    $data[$key]['jobAutoID'] = $standardjobcardAutoid;
                    $data[$key]['type'] = 1;
                    $data[$key]['glAutoID'] = $inputdata['glautoid'];
                    $data[$key]['itemAutoID'] = $itemautoid[$key];
                    if(isset($itemdescription[2])){
                        $data[$key]['description'] = $itemdescription[2];
                    }
                    //$data[$key]['description'] = $itemdescription[2];
                    $data[$key]['uomID'] = $UnitOfMeasureID[$key];
                    $data[$key]['unitOfMeasure'] = $uom[$key];
                    $data[$key]['qty'] = $Qty[$key];
                    $data[$key]['unitCost'] = $unitcost[$key];
                    $data[$key]['totalCost'] = $totalcost[$key];
                    $data[$key]['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $data[$key]['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $data[$key]['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data[$key]['transactionCurrencyID']);
                    $data[$key]['transactionExchangeRate'] = 1;
                    $data[$key]['transactionAmount'] = round($totalcost[$key], $data[$key]['transactionCurrencyDecimalPlaces']);
                    //$data[$key]['transactionAmount'] = $totalcost[$key];
                    $data[$key]['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $data[$key]['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $data[$key]['companyLocalExchangeRate'] = 1;
                    $data[$key]['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
                    $companyLocalAmount = (float)$data[$key]['transactionAmount'] / $data[$key]['companyLocalExchangeRate'];
                    $data[$key]['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $data[$key]['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $default_currency = currency_conversionID($data[$key]['transactionCurrencyID'], $data[$key]['companyLocalCurrencyID']);
                    $data[$key]['companyLocalExchangeRate'] = $default_currency['conversion'];
                    $data[$key]['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                    $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $data[$key]['companyLocalCurrencyDecimalPlaces']);
                    //$data[$key]['companyLocalAmount'] = $companyLocalAmount;
                    $data[$key]['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                    $data[$key]['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                    $reporting_currency = currency_conversionID($data[$key]['transactionCurrencyID'], $data[$key]['companyReportingCurrencyID']);
                    $data[$key]['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $data[$key]['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                    $companyReportingAmount = (float)$data[$key]['transactionAmount'] / $data[$key]['companyReportingExchangeRate'];
                    $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $data[$key]['companyReportingCurrencyDecimalPlaces']);
                    $data[$key]['companyReportingAmount'] = $companyReportingAmount;
                    $data[$key]['createdUserID'] = $this->common_data['current_userID'];
                    $data[$key]['createdPCID'] = $this->common_data['current_pc'];
                    $data[$key]['createdUserName'] = $this->common_data['current_user'];
                    $data[$key]['createdDateTime'] = $this->common_data['current_date'];
                    $data[$key]['companyID'] = $companyID;
                    $data[$key]['suomID'] = $SecondaryUnitOfMeasureID[$key] ?? null;
                    $data[$key]['suom'] = $suom[$key] ?? null;
                    $data[$key]['suomQty'] = $SecondaryQty[$key] ?? 0;

                    $this->db->insert('srp_erp_mfq_standardjob_items', $data[$key]);
                }

            }

            $this->db->delete('srp_erp_mfq_standardjob_labourtask', array('jobAutoID' => $standardjobcardAutoid));
            foreach ($labourautoid as $key => $val) {
                if (!empty($labourautoid[$key]) || $labourautoid[$key] != '') {

                    $datalabour[$key]['jobAutoID'] = $standardjobcardAutoid;
                    $datalabour[$key]['labourTaskID'] = $labourautoid[$key];
                    $datalabour[$key]['description'] = $searchlabour[$key];
                    $datalabour[$key]['glAutoID'] = $glautoidlabour[$key];
                    $datalabour[$key]['uomID'] = $UnitOfMeasureIDLabour[$key];
                    $datalabour[$key]['unitOfMeasure'] = $UOMLabour[$key];
                    $datalabour[$key]['hourlyRate'] = $unitrate[$key];
                    $datalabour[$key]['totalHours'] = $usagehours[$key];
                    $datalabour[$key]['totalValue'] = $totalhours[$key];
                    $datalabour[$key]['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $datalabour[$key]['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $datalabour[$key]['transactionExchangeRate'] = 1;
                    $datalabour[$key]['transactionAmount'] = $totalhours[$key];
                    $datalabour[$key]['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($datalabour[$key]['transactionCurrencyID']);

                    $datalabour[$key]['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $datalabour[$key]['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $datalabour[$key]['companyLocalExchangeRate'] = 1;
                    $datalabour[$key]['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
                    $companyLocalAmount = $datalabour[$key]['transactionAmount'] / $datalabour[$key]['companyLocalExchangeRate'] = 1;
                    $datalabour[$key]['companyLocalAmount'] = round($companyLocalAmount, $datalabour[$key]['companyLocalCurrencyDecimalPlaces']);


                    $datalabour[$key]['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                    $datalabour[$key]['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                    $reporting_currency = currency_conversionID($datalabour[$key]['companyLocalCurrencyID'], $datalabour[$key]['companyReportingCurrencyID']);
                    $datalabour[$key]['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $datalabour[$key]['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

                    $companyReportingAmount = $datalabour[$key]['transactionAmount'] / $datalabour[$key]['companyReportingExchangeRate'];
                    $datalabour[$key]['companyReportingAmount'] = round($companyReportingAmount, $datalabour[$key]['companyReportingCurrencyDecimalPlaces']);


                    $datalabour[$key]['createdUserID'] = $this->common_data['current_userID'];
                    $datalabour[$key]['createdPCID'] = $this->common_data['current_pc'];
                    $datalabour[$key]['createdDateTime'] = $this->common_data['current_date'];
                    $datalabour[$key]['createdUserName'] = $this->common_data['current_user'];
                    $datalabour[$key]['companyID'] = $companyID;
                    $this->db->insert('srp_erp_mfq_standardjob_labourtask', $datalabour[$key]);
                }

            }

            $this->db->delete('srp_erp_mfq_standardjob_overhead', array('jobAutoID' => $standardjobcardAutoid));
            foreach ($overheadautoid as $key => $val) {
                if (!empty($overheadautoid[$key]) || $overheadautoid[$key] != '') {

                    $dataoverhead[$key]['jobAutoID'] = $standardjobcardAutoid;
                    $dataoverhead[$key]['overHeadID'] = $overheadautoid[$key];
                    $dataoverhead[$key]['Description'] = $searchoverhead[$key];

                    $dataoverhead[$key]['uomID'] = $UnitOfMeasureIDoverhead[$key];
                    $dataoverhead[$key]['unitOfMeasure'] = $UOMoverhead[$key];
                    $dataoverhead[$key]['hourlyRate'] = $unitrateoverhead[$key];
                    $dataoverhead[$key]['totalHours'] = $usagehoursoverhead[$key];
                    $dataoverhead[$key]['totalValue'] = $totalhoursoverhead[$key];
                    $dataoverhead[$key]['glAutoID'] = $glautoidoverhead[$key];

                    $dataoverhead[$key]['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $dataoverhead[$key]['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $dataoverhead[$key]['transactionExchangeRate'] = 1;
                    $dataoverhead[$key]['transactionAmount'] = $totalhoursoverhead[$key];
                    $dataoverhead[$key]['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($dataoverhead[$key]['transactionCurrencyID']);

                    $dataoverhead[$key]['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $dataoverhead[$key]['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $dataoverhead[$key]['companyLocalExchangeRate'] = 1;
                    $dataoverhead[$key]['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
                    $companyLocalAmount = $dataoverhead[$key]['transactionAmount'] / $dataoverhead[$key]['companyLocalExchangeRate'] = 1;
                    $dataoverhead[$key]['companyLocalAmount'] = round($companyLocalAmount, $dataoverhead[$key]['companyLocalCurrencyDecimalPlaces']);


                    $dataoverhead[$key]['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                    $dataoverhead[$key]['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                    $reporting_currency = currency_conversionID($dataoverhead[$key]['companyLocalCurrencyID'], $dataoverhead[$key]['companyReportingCurrencyID']);
                    $dataoverhead[$key]['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $dataoverhead[$key]['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

                    $companyReportingAmount = $dataoverhead[$key]['transactionAmount'] / $dataoverhead[$key]['companyReportingExchangeRate'];
                    $dataoverhead[$key]['companyReportingAmount'] = round($companyReportingAmount, $dataoverhead[$key]['companyReportingCurrencyDecimalPlaces']);


                    $dataoverhead[$key]['createdUserID'] = $this->common_data['current_userID'];
                    $dataoverhead[$key]['createdPCID'] = $this->common_data['current_pc'];
                    $dataoverhead[$key]['createdDateTime'] = $this->common_data['current_date'];
                    $dataoverhead[$key]['createdUserName'] = $this->common_data['current_user'];
                    $dataoverhead[$key]['companyID'] = $companyID;
                    $this->db->insert('srp_erp_mfq_standardjob_overhead', $dataoverhead[$key]);
                }

            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Standard Job  Detail :  Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Standard Job  Detail :  Saved Successfully.');
            }




    }

    function load_mfq_standard_job_details()
    {
        $StandardJobCardID = $this->input->post('StandardJobcard');
        $companyid = current_companyID();
        $data = $this->db->query("SELECT
	srp_erp_mfq_standardjob_items.*,
	CONCAT(
	IFNULL( srp_erp_mfq_itemmaster.itemSystemCode, \"\" ),
	\" | \",
	IFNULL( srp_erp_mfq_itemmaster.secondaryItemCode, \"\" ),
	\" | \",
	IFNULL( srp_erp_mfq_itemmaster.itemDescription, \"\" )
	) AS `Match`,
	IFNULL( srp_erp_mfq_standardjob_items.suom, \"\" ) AS suom,
	IFNULL( srp_erp_mfq_standardjob_items.suomQty, 0 ) AS suomQty
FROM
	`srp_erp_mfq_standardjob_items`
	LEFT JOIN srp_erp_mfq_itemmaster on srp_erp_mfq_itemmaster.itemAutoID = srp_erp_mfq_standardjob_items.itemAutoID
WHERE
	`jobAutoID` = $StandardJobCardID
	AND srp_erp_mfq_standardjob_items.companyID = $companyid AND srp_erp_mfq_standardjob_items.type = 1 ")->result_array();
        return $data;

    }

    function fetch_mfq_standard_labourtask()
    {
        $dataArr = array();
        $dataArr2 = array();
        $companyid = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT srp_erp_mfq_overhead.*,CONCAT(IFNULL(description,""), " (" ,IFNULL(overHeadCode,""),")") AS "Match",IFNULL(srp_erp_mfq_segmenthours.hours,0) as hours,
        srp_erp_unit_of_measure.UnitDes FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_unit_of_measure on srp_erp_unit_of_measure.UnitID = srp_erp_mfq_overhead.unitOfMeasureID LEFT JOIN srp_erp_mfq_segmenthours ON srp_erp_mfq_overhead.mfqSegmentID = srp_erp_mfq_segmenthours.mfqSegmentID  WHERE srp_erp_mfq_overhead.companyID = '.$companyid.'  AND overHeadCategoryID = 2 AND (overHeadCode LIKE "' . $search_string . '" OR description LIKE "' . $search_string . '")')->result_array();
        //echo $this->db->last_query();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['overHeadCode'], 'overHeadID' => $val['overHeadID'], 'description' => $val['description'], 'segment' => $val['mfqSegmentID'], 'rate' => $val['rate'], 'hours' => $val['hours'], 'uom' => $val['unitOfMeasureID'], 'UnitDes' => $val['UnitDes'], 'financeGLAutoID' => $val['financeGLAutoID']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function load_mfq_standard_job_labour()
    {
        $StandardJobCardID = $this->input->post('StandardJobcard');
        $companyid = current_companyID();
        $data = $this->db->query("select
            *
            from
            srp_erp_mfq_standardjob_labourtask
            where
            companyID = $companyid
            And jobAutoID = $StandardJobCardID")->result_array();
        return $data;

    }

    function fetch_mfq_standard_overhead()
    {
        $dataArr = array();
        $dataArr2 = array();
        $companyid = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT srp_erp_mfq_overhead.*,CONCAT(IFNULL(description,""), " (" ,IFNULL(overHeadCode,""),")") AS "Match",IFNULL(srp_erp_mfq_segmenthours.hours,0) as hours,srp_erp_unit_of_measure.UnitDes  FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_mfq_segmenthours ON srp_erp_mfq_overhead.mfqSegmentID = srp_erp_mfq_segmenthours.mfqSegmentID LEFT JOIN srp_erp_unit_of_measure on srp_erp_unit_of_measure.UnitID = srp_erp_mfq_overhead.unitOfMeasureID WHERE srp_erp_mfq_overhead.companyID = '.$companyid.' AND overHeadCategoryID = 1 AND  (overHeadCode LIKE "' . $search_string . '" OR description LIKE "' . $search_string . '")')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['overHeadCode'], 'overHeadID' => $val['overHeadID'], 'description' => $val['description'], 'segment' => $val['mfqSegmentID'], 'rate' => $val['rate'], 'hours' => $val['hours'], 'uom' => $val['unitOfMeasureID'], 'UnitDes' => $val['UnitDes'], 'financeGLAutoID' => $val['financeGLAutoID']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function load_mfq_standard_job_overhead()
    {
        $StandardJobCardID = $this->input->post('StandardJobcard');
        $companyid = current_companyID();
        $data = $this->db->query("SELECT
                *
                FROM
                `srp_erp_mfq_standardjob_overhead`
                WHERE
                companyID = $companyid
                and jobAutoID = $StandardJobCardID")->result_array();
        return $data;

    }

    function fetch_mfq_standard_item_output()
    {
        $dataArr = array();
        $dataArr2 = array();
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";


        $sql = 'SELECT
	srp_erp_mfq_itemmaster.itemAutoID,
	srp_erp_mfq_itemmaster.secondaryItemCode,
	srp_erp_mfq_itemmaster.itemDescription,
	srp_erp_mfq_itemmaster.defaultUnitOfMeasureID,
	CONCAT( IFNULL(srp_erp_mfq_itemmaster.itemSystemCode, "" ), " | ",IFNULL(srp_erp_mfq_itemmaster.secondaryItemCode, "" ), " | ", IFNULL(srp_erp_mfq_itemmaster.itemDescription, "" ) ) AS `Match`,
	IFNULL(srp_erp_itemmaster.companyLocalWacAmount,0)  as wacamount,
	srp_erp_unit_of_measure.UnitDes,
	srp_erp_itemmaster.assteGLAutoID,
	srp_erp_mfq_itemmaster.itemType as warehouseitemtype,
    srp_erp_itemmaster.secondaryUOMID,
	suom.UnitDes AS suomDes,
		CASE
	  WHEN srp_erp_itemmaster.mainCategory = "Service" or "Non Inventory" THEN
	  srp_erp_itemmaster.costGLAutoID
	WHEN srp_erp_itemmaster.mainCategory = "Inventory" THEN
	srp_erp_itemmaster.assteGLAutoID
	END assteGLAutoID
FROM
	srp_erp_mfq_itemmaster
	LEFT JOIN srp_erp_itemmaster on srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID
	LEFT JOIN srp_erp_unit_of_measure on srp_erp_unit_of_measure.UnitID = srp_erp_itemmaster.defaultUnitOfMeasureID
	LEFT JOIN srp_erp_unit_of_measure suom ON suom.UnitID = srp_erp_itemmaster.secondaryUOMID 
WHERE
    srp_erp_mfq_itemmaster.companyID = "' . $companyID . '"
    AND srp_erp_mfq_itemmaster.itemAutoID IS NOT NULL
	AND (srp_erp_mfq_itemmaster.itemType = "2" or srp_erp_mfq_itemmaster.itemType = "3")
	AND srp_erp_mfq_itemmaster.isActive = "1"
	AND(srp_erp_mfq_itemmaster.secondaryItemCode LIKE "' . $search_string . '" OR srp_erp_mfq_itemmaster.itemSystemCode LIKE "' . $search_string . '"  OR srp_erp_mfq_itemmaster.itemDescription LIKE "' . $search_string . '")
	';


        $data = $this->db->query($sql)->result_array();

        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'itemAutoID' => $val['itemAutoID'], 'secondaryItemCode' => $val['secondaryItemCode'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'wacamount' => $val['wacamount'], 'uomdescription' => $val['UnitDes'], 'GLAutoID' => $val['assteGLAutoID'], 'warehouseitemtype' => $val['warehouseitemtype'],'suomDes' => $val['suomDes'],'secondaryUOMID' => $val['secondaryUOMID']);
            }
        }
        //echo $this->db->last_query();
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function save_mfq_sd_job_output()
    {
        $this->db->trans_start();
        $standardjobcardAutoid = $this->input->post('standardjobcardAutoid');
        $itemautoid = $this->input->post('itemautoidoutput');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureIDoutput');
        $Qty = $this->input->post('Qtyoutput');
        $search = $this->input->post('searchoutput');
        $unitcost = $this->input->post('unitcostoutput');
        $totalcost = $this->input->post('totalcostoutput');
        $assetglautoid = $this->input->post('assetglautoid');
        $uom = $this->input->post('UOMoutput');
        $companyID = current_companyID();


        $this->db->delete('srp_erp_mfq_standardjob_items', array('jobAutoID' => $standardjobcardAutoid, 'type' => 2));
        foreach ($itemautoid as $key => $val) {
            if (!empty($itemautoid[$key]) || $itemautoid[$key] != '') {
                $itemdescription = explode('|', $search[$key]);
                $data[$key]['jobAutoID'] = $standardjobcardAutoid;
                $data[$key]['type'] = 2;
                $data[$key]['glAutoID'] = $assetglautoid[$key];
                $data[$key]['itemAutoID'] = $itemautoid[$key];
                $data[$key]['description'] = $itemdescription[2];
                $data[$key]['uomID'] = $UnitOfMeasureID[$key];
                $data[$key]['unitOfMeasure'] = $uom[$key];
                $data[$key]['qty'] = $Qty[$key];
                $data[$key]['unitCost'] = $unitcost[$key];
                $data[$key]['totalCost'] = $totalcost[$key];
                $data[$key]['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data[$key]['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $data[$key]['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data[$key]['transactionCurrencyID']);
                $data[$key]['transactionExchangeRate'] = 1;
                $data[$key]['transactionAmount'] = round($totalcost[$key], $data[$key]['transactionCurrencyDecimalPlaces']);
                $data[$key]['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data[$key]['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $data[$key]['companyLocalExchangeRate'] = 1;
                $data[$key]['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
                $companyLocalAmount = (float)$data[$key]['transactionAmount'] / $data[$key]['companyLocalExchangeRate'];
                $data[$key]['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data[$key]['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $default_currency = currency_conversionID($data[$key]['transactionCurrencyID'], $data[$key]['companyLocalCurrencyID']);
                $data[$key]['companyLocalExchangeRate'] = $default_currency['conversion'];
                $data[$key]['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $data[$key]['companyLocalCurrencyDecimalPlaces']);
                $data[$key]['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                $data[$key]['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                $reporting_currency = currency_conversionID($data[$key]['transactionCurrencyID'], $data[$key]['companyReportingCurrencyID']);
                $data[$key]['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                $data[$key]['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                $companyReportingAmount = (float)$data[$key]['transactionAmount'] / $data[$key]['companyReportingExchangeRate'];
                $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $data[$key]['companyReportingCurrencyDecimalPlaces']);
                $data[$key]['createdUserID'] = $this->common_data['current_userID'];
                $data[$key]['createdPCID'] = $this->common_data['current_pc'];
                $data[$key]['createdDateTime'] = $this->common_data['current_date'];
                $data[$key]['createdUserName'] = $this->common_data['current_user'];
                $data[$key]['companyID'] = $companyID;
                $this->db->insert('srp_erp_mfq_standardjob_items', $data[$key]);
            }

        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Standard Job Output  Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Standard Job Output  Detail :  Saved Successfully.');
        }
    }

    function load_mfq_standard_job_details_output()
    {
        $StandardJobCardID = $this->input->post('StandardJobcard');
        $convertFormat = convert_date_format_sql();
        $companyid = current_companyID();
        $data = $this->db->query("SELECT
	srp_erp_mfq_standardjob_items.*,
	CONCAT(
	IFNULL( srp_erp_mfq_itemmaster.itemSystemCode, \"\" ),
	\" | \",
	IFNULL( srp_erp_mfq_itemmaster.secondaryItemCode, \"\" ),
	\" | \",
	IFNULL( srp_erp_mfq_itemmaster.itemDescription, \"\" )
	) AS `Match`,
	DATE_FORMAT(expiryDate,'%Y-%m-%d')AS expiryDate,
	srp_erp_mfq_standardjob_items.warehouseAutoID as manufacturingwarehouse,
	IFNULL( srp_erp_mfq_standardjob_items.suom, \"\" ) AS suom,
	IFNULL( srp_erp_mfq_standardjob_items.suomQty, 0 ) AS suomQty
FROM
	`srp_erp_mfq_standardjob_items`
	LEFT JOIN srp_erp_mfq_itemmaster on srp_erp_mfq_itemmaster.itemAutoID = srp_erp_mfq_standardjob_items.itemAutoID
WHERE
	`jobAutoID` = $StandardJobCardID
	AND srp_erp_mfq_standardjob_items.companyID = $companyid AND srp_erp_mfq_standardjob_items.type = '2' ")->result_array();


        /*echo $this->db->last_query();*/
        return $data;

    }

    function standardjobcard_confirmation()
    {
        $companyID = current_companyID();
        $currentuser = current_userID();
        $jobautoid = $this->input->post('standardjobcard');
        $results = $this->db->query("select jobAutoID from srp_erp_mfq_standardjob_items where companyID = $companyID And jobAutoID = $jobautoid AND type = 1 GROUP BY jobAutoID UNION select jobAutoID from srp_erp_mfq_standardjob_labourtask where
   companyID = $companyID And jobAutoID = $jobautoid GROUP BY jobAutoID UNION select jobAutoID from  srp_erp_mfq_standardjob_overhead where companyID = $companyID And jobAutoID = $jobautoid GROUP BY jobAutoID")->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('jobAutoID');
            $this->db->where('jobAutoID', $jobautoid);
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_mfq_standardjob');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                return array('error' => 2, 'message' => 'Document already confirmed');
            } else {
                $masterID = trim($this->input->post('salesReturnAutoID') ?? '');
                $this->load->library('approvals');
                $this->db->select('jobAutoID,documentSystemCode,documentDate');
                $this->db->where('jobAutoID', $jobautoid);
                $this->db->from('srp_erp_mfq_standardjob');
                $app_data = $this->db->get()->row_array();

                $validate_code = validate_code_duplication($app_data['documentSystemCode'], 'documentSystemCode', $jobautoid,'jobAutoID', 'srp_erp_mfq_standardjob');
                if(!empty($validate_code)) {
                    $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    return array(false, 'error');
                }

                /** item Master Sub check */
                /*$documentDetailID = trim($this->input->post('stockReturnAutoID') ?? '');
                $validate = $this->validate_itemMasterSub($documentDetailID, 'SLR');*/
                /** end of item master sub */

                /*if ($validate) {*/
                /*} else {
                    return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                }*/
                $autoApproval = get_document_auto_approval('STJOB');
                if ($autoApproval == 0) {
                    $approvals_status = $this->approvals->auto_approve($app_data['jobAutoID'], 'srp_erp_mfq_standardjob', 'jobAutoID', 'STJOB', $app_data['documentSystemCode'], $app_data['documentDate']);
                } elseif ($autoApproval == 1) {
                    $approvals_status = $this->approvals->CreateApproval('STJOB', $app_data['jobAutoID'], $app_data['documentSystemCode'], 'Standard Job Card', 'srp_erp_mfq_standardjob', 'jobAutoID', 0, $app_data['documentDate']);
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
                    $this->db->where('jobAutoID', $jobautoid);
                    $this->db->update('srp_erp_mfq_standardjob', $data);

                    $autoApproval = get_document_auto_approval('STJOB');
                    if ($autoApproval == 0) {
                        // $result = $this->save_sales_return_approval(0, $app_data['salesReturnAutoID'], 1, 'Auto Approved');
                        /* if($result){
                             return array('error' => 0, 'message' => 'document successfully confirmed');
                         }*/
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

    function save_jobstandard_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('jobAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $companyid = current_companyID();

        $inputgeneralledger = array();
        $outputgeneralledger = array();
        $labourgeneralledger = array();
        $overheadgeneralledger = array();

        $item_arr = array();
        $itemledger_arr = array();

        $item_arroutput = array();
        $itemledger_arroutput = array();


        $outputgeneralarray = $this->db->query("SELECT 'STJOB' AS documentCode,job.jobAutoID AS documentMasterAutoID,job.documentSystemCode AS documentSystemCode,job.documentDate AS documentDate,YEAR ( job.documentDate ) AS documentYear,MONTH ( job.documentDate ) AS documentMonth,job.narration AS documentNarration,items.GLAutoID AS glautoID,account.systemAccountCode AS systemGLCode,account.GLSecondaryCode AS GLCode,account.GLDescription AS GLDescription,account.subcategory AS GLType,
'dr' AS amount_type,items.transactionCurrencyID AS transactionCurrencyID,items.transactionCurrency AS transactionCurrency,items.transactionExchangeRate AS transactionExchangeRate,sum( items.transactionAmount ) AS transactionAmount,items.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,items.companyLocalCurrencyID AS companyLocalCurrencyID,items.companyLocalCurrency AS companyLocalCurrency,items.companyLocalExchangeRate AS companyLocalExchangeRate,sum( items.companyLocalAmount ) AS companyLocalAmount,items.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces,items.companyreportingcurrencyID AS companyReportingCurrencyID,items.companyreportingcurrency AS companyReportingCurrency,items.companyreportingExchangeRate AS companyReportingExchangeRate,
sum( items.companyReportingAmount ) AS companyReportingAmount,items.companyReportingCurrencyDecimalPlaces AS companyReportingCurrencyDecimalPlaces,job.confirmedByEmpID,job.confirmedByName,job.confirmedDate,job.approvedDate,job.approvedbyEmpID,job.approvedbyEmpName,segment.segmentID,segment.segmentCode,job.companyID,company_code AS companyCode,job.createdUserGroup,job.createdPCID,job.createdUserID,job.createdDateTime,job.createdUserName
FROM srp_erp_mfq_standardjob_items items LEFT JOIN srp_erp_mfq_standardjob job ON items.jobAutoID = job.jobAutoID LEFT JOIN srp_erp_chartofaccounts account ON account.glautoID = items.glautoID LEFT JOIN srp_erp_currencymaster currency ON currency.currencyID = transactionCurrency LEFT JOIN srp_erp_mfq_segment mgqsegment ON mgqsegment.mfqSegmentID = job.segmentID LEFT JOIN srp_erp_mfq_segment segment ON segment.segmentID = mgqsegment.segmentID LEFT JOIN srp_erp_company company ON company.company_id = job.companyID
WHERE job.companyID = $companyid AND job.jobAutoID = $system_code  AND items.type = 2 GROUP BY items.glAutoID")->result_array();

        $inputgeneralarray = $this->db->query("SELECT 'STJOB' AS documentCode, job.jobAutoID AS documentMasterAutoID, job.documentSystemCode AS documentSystemCode, job.documentDate AS documentDate, YEAR ( job.documentDate ) AS documentYear, MONTH ( job.documentDate ) AS documentMonth, job.narration AS documentNarration, items.GLAutoID AS glautoID, account.systemAccountCode AS systemGLCode, account.GLSecondaryCode AS GLCode, account.GLDescription AS GLDescription, account.subcategory AS GLType, 'cr' AS amount_type, items.transactionCurrencyID AS transactionCurrencyID, items.transactionCurrency AS transactionCurrency, items.transactionExchangeRate AS transactionExchangeRate, sum( items.transactionAmount ) AS transactionAmount, items.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces, items.companyLocalCurrencyID AS companyLocalCurrencyID, items.companyLocalCurrency AS companyLocalCurrency, items.companyLocalExchangeRate AS companyLocalExchangeRate,sum( items.companyLocalAmount ) AS companyLocalAmount,items.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces,items.companyreportingcurrencyID AS companyReportingCurrencyID,items.companyreportingcurrency AS companyReportingCurrency,items.companyreportingExchangeRate AS companyReportingExchangeRate,sum( items.companyReportingAmount ) AS companyReportingAmount,items.companyReportingCurrencyDecimalPlaces AS companyReportingCurrencyDecimalPlaces,job.confirmedByEmpID,job.confirmedByName,job.confirmedDate,job.approvedDate,job.approvedbyEmpID,job.approvedbyEmpName,segment.segmentID,segment.segmentCode,job.companyID,company_code AS companyCode,
job.createdUserGroup,job.createdPCID,job.createdUserID,job.createdDateTime,job.createdUserName FROM srp_erp_mfq_standardjob_items items LEFT JOIN srp_erp_mfq_standardjob job ON items.jobAutoID = job.jobAutoID LEFT JOIN srp_erp_chartofaccounts account ON account.glautoID = items.glautoID LEFT JOIN srp_erp_currencymaster currency ON currency.currencyID = transactionCurrency LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = job.segmentID LEFT JOIN srp_erp_segment segment ON segment.segmentID = mfqsegment.segmentID LEFT JOIN srp_erp_company company ON company.company_id = job.companyID
WHERE job.companyID = $companyid AND job.jobAutoID = $system_code  AND items.type = 1 GROUP BY items.glAutoID")->result_array();

        $labourgeneralledgerarray = $this->db->query("SELECT 'STJOB' AS documentCode, job.jobAutoID AS documentMasterAutoID, job.documentSystemCode AS documentSystemCode, job.documentDate AS documentDate, YEAR ( job.documentDate ) AS documentYear, MONTH ( job.documentDate ) AS documentMonth, job.narration AS documentNarration, labour.GLAutoID AS glautoID, account.systemAccountCode AS systemGLCode, account.GLSecondaryCode AS GLCode, account.GLDescription AS GLDescription, account.subcategory AS GLType, 'cr' AS amount_type, labour.transactionCurrencyID AS transactionCurrencyID, labour.transactionCurrency AS transactionCurrency, labour.transactionExchangeRate AS transactionExchangeRate, sum( labour.transactionAmount ) AS transactionAmount, labour.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces, labour.companyLocalCurrencyID AS companyLocalCurrencyID, labour.companyLocalCurrency AS companyLocalCurrency, labour.companyLocalExchangeRate AS companyLocalExchangeRate, sum( labour.companyLocalAmount ) AS companyLocalAmount, labour.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces, labour.companyreportingcurrencyID AS companyReportingCurrencyID,labour.companyreportingcurrency AS companyReportingCurrency,labour.companyreportingExchangeRate AS companyReportingExchangeRate,sum( labour.companyReportingAmount ) AS companyReportingAmount,labour.companyReportingCurrencyDecimalPlaces AS companyReportingCurrencyDecimalPlaces,job.confirmedByEmpID,job.confirmedByName,job.confirmedDate,job.approvedDate,job.approvedbyEmpID,job.approvedbyEmpName,segment.segmentID,segment.segmentCode,job.companyID,company_code AS companyCode,job.createdUserGroup,job.createdPCID,job.createdUserID,job.createdDateTime,job.createdUserName
FROM srp_erp_mfq_standardjob_labourtask labour LEFT JOIN srp_erp_mfq_standardjob job ON labour.jobAutoID = job.jobAutoID LEFT JOIN srp_erp_chartofaccounts account ON account.glautoID = labour.glautoID LEFT JOIN srp_erp_currencymaster currency ON currency.currencyID = transactionCurrency LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = job.segmentID LEFT JOIN srp_erp_segment segment ON segment.segmentID = mfqsegment.segmentID LEFT JOIN srp_erp_company company ON company.company_id = job.companyID
WHERE job.companyID = $companyid  AND job.jobAutoID = $system_code GROUP BY labour.glAutoID")->result_array();

        $overheadgeneralledgerarray = $this->db->query("SELECT 'STJOB' AS documentCode, job.jobAutoID AS documentMasterAutoID, job.documentSystemCode AS documentSystemCode, job.documentDate AS documentDate, YEAR ( job.documentDate ) AS documentYear, MONTH ( job.documentDate ) AS documentMonth, job.narration AS documentNarration, overhead.GLAutoID AS glautoID, account.systemAccountCode AS systemGLCode, account.GLSecondaryCode AS GLCode, account.GLDescription AS GLDescription, account.subcategory AS GLType, 'cr' AS amount_type, overhead.transactionCurrencyID AS transactionCurrencyID, overhead.transactionCurrency AS transactionCurrency, overhead.transactionExchangeRate AS transactionExchangeRate, sum( overhead.transactionAmount ) AS transactionAmount, overhead.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
overhead.companyLocalCurrencyID AS companyLocalCurrencyID,overhead.companyLocalCurrency AS companyLocalCurrency,overhead.companyLocalExchangeRate AS companyLocalExchangeRate,sum( overhead.companyLocalAmount ) AS companyLocalAmount,overhead.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces,overhead.companyreportingcurrencyID AS companyReportingCurrencyID,overhead.companyreportingcurrency AS companyReportingCurrency,overhead.companyreportingExchangeRate AS companyReportingExchangeRate,sum( overhead.companyReportingAmount ) AS companyReportingAmount,overhead.companyReportingCurrencyDecimalPlaces AS companyReportingCurrencyDecimalPlaces,job.confirmedByEmpID,job.confirmedByName,job.confirmedDate,job.approvedDate,job.approvedbyEmpID,job.approvedbyEmpName,segment.segmentID,segment.segmentCode,job.companyID,
company_code AS companyCode,job.createdUserGroup,job.createdPCID,job.createdUserID,job.createdDateTime,job.createdUserName FROM srp_erp_mfq_standardjob_overhead overhead LEFT JOIN srp_erp_mfq_standardjob job ON overhead.jobAutoID = job.jobAutoID LEFT JOIN srp_erp_chartofaccounts account ON account.glautoID = overhead.glautoID LEFT JOIN srp_erp_currencymaster currency ON currency.currencyID = transactionCurrency
LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = job.segmentID LEFT JOIN srp_erp_segment segment ON segment.segmentID = mfqsegment.segmentID LEFT JOIN srp_erp_company company ON company.company_id = job.companyID
WHERE job.companyID = $companyid AND job.jobAutoID = $system_code GROUP BY overhead.glAutoID")->result_array();

        $mastertbldetails = $this->db->query("select documentDate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces from srp_erp_mfq_standardjob where  companyID = $companyid AND jobAutoID = $system_code  ")->row_array();

        $finaceper = $this->db->query("SELECT srp_erp_companyfinanceperiod.*,CONCAT( dateFrom, ' - ', dateTo ) AS companyFinanceYear,beginingDate,endingDate FROM srp_erp_companyfinanceperiod LEFT JOIn srp_erp_companyfinanceyear on srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_companyfinanceperiod.companyFinanceYearID
WHERE srp_erp_companyfinanceperiod.isActive = 1 AND srp_erp_companyfinanceperiod.companyID = $companyid AND '{$mastertbldetails['documentDate']}' BETWEEN dateFrom AND dateTo ")->row_array();

        $details_arr = $this->db->query("SELECT
	standardjobmaster.approvedDate,
	standardjobmaster.approvedbyEmpName,
	standardjobmaster.approvedbyEmpID,
	standardjobmaster.confirmedDate,
	standardjobmaster.confirmedByName,
	standardjobmaster.confirmedByEmpID,
	mastertbl.glAutoID AS glAutoID,
	itemmaster.costGLAutoID,
	mastertbl.uomID,
	warehouse.warehouseAutoID AS warehouseAutoID,
	mastertbl.itemAutoID,
	standardjobmaster.jobAutoID AS jobAutoID,
	standardjobmaster.documentSystemCode AS documentSystemCode,
	standardjobmaster.documentDate AS documentDate,
	chart.GLSecondaryCode AS PLType,
	chart.GLDescription AS PLDescription,
	chart.GLSecondaryCode AS PLGLCode,
	chart.systemAccountCode AS PLSystemGLCode,
	chartbl.systemAccountCode AS BLSystemGLCode,
	chartbl.GLSecondaryCode AS BLGLCode,
	chartbl.GLDescription AS BLDescription,
	chartbl.GLSecondaryCode AS BLType,
	COALESCE ( SUM( mastertbl.qty ), 0 ) AS qtyUpdatedIssued,
	COALESCE ( SUM( mastertbl.totalCost ), 0 ) AS UpdatedTotalValue,
	warehouse.wareHouseCode AS wareHouseCode,
	warehouse.wareHouseLocation AS wareHouseLocation,
	warehouse.wareHouseDescription AS wareHouseDescription,
	itemmaster.itemSystemCode AS itemSystemCode,
	itemmaster.itemDescription AS itemDescription,
	unit.UnitShortCode AS UnitShortCode,
	mastertbl.transactionCurrencyID AS transactionCurrencyID,
	currencytransaction.CurrencyCode AS transactionCurrency,
	mastertbl.transactionExchangeRate AS transactionExchangeRate,
	mastertbl.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
	mastertbl.companyLocalCurrencyID AS companyLocalCurrencyID,
	currencycompany.CurrencyCode AS companyLocalCurrency,
	mastertbl.companyLocalExchangeRate AS companyLocalExchangeRate,
	mastertbl.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces,
	itemmaster.companyLocalWacAmount AS currentlWacAmount,
	mastertbl.companyReportingCurrencyID AS companyReportingCurrencyID,
	currencycompanyreporting.CurrencyCode AS companyReportingCurrency,
	mastertbl.companyReportingExchangeRate AS companyReportingExchangeRate,
	mastertbl.companyReportingCurrencyDecimalPlaces AS companyReportingCurrencyDecimalPlaces,
	segment.segmentCode AS segmentCode,
	itemmaster.mainCategory AS itemCategory,
	segment.segmentID AS segmentID
FROM
	srp_erp_mfq_standardjob_items mastertbl
	LEFT JOIN srp_erp_chartofaccounts chartbl ON chartbl.GLAutoID = mastertbl.glAutoID
	LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = mastertbl.itemAutoID
	LEFT JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = itemmaster.costGLAutoID
	LEFT JOIN srp_erp_mfq_standardjob standardjobmaster ON standardjobmaster.jobAutoID = mastertbl.jobAutoID
	LEFT JOIN srp_erp_mfq_warehousemaster mfqwarehouse ON mfqwarehouse.mfqWarehouseAutoID = standardjobmaster.warehouseID
	LEFT JOIN srp_erp_warehousemaster warehouse ON warehouse.wareHouseAutoID = mfqwarehouse.warehouseAutoID
	LEFT JOIN srp_erp_unit_of_measure unit ON unit.UnitID = mastertbl.uomID
	LEFT JOIN srp_erp_currencymaster currencytransaction ON currencytransaction.currencyID = mastertbl.transactionCurrencyID
	LEFT JOIN srp_erp_currencymaster currencycompany ON currencycompany.currencyID = mastertbl.companyLocalCurrencyID
	LEFT JOIN srp_erp_currencymaster currencycompanyreporting ON currencycompanyreporting.currencyID = mastertbl.companyReportingCurrencyID
	LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = standardjobmaster.segmentID
	LEFT JOIN srp_erp_segment segment ON segment.segmentID = mfqsegment.segmentID
WHERE
	mastertbl.companyID = $companyid
	AND mastertbl.jobAutoID = $system_code
	AND type = 1
GROUP BY
	`mastertbl`.`itemAutoID`")->result_array();

        $details_arroutput = $this->db->query("SELECT
	standardjobmaster.approvedDate,
	standardjobmaster.approvedbyEmpName,
	standardjobmaster.approvedbyEmpID,
	standardjobmaster.confirmedDate,
	standardjobmaster.confirmedByName,
	standardjobmaster.confirmedByEmpID,
	mastertbl.glAutoID AS glAutoID,
	itemmaster.costGLAutoID,
	mastertbl.uomID,
	warehouse.warehouseAutoID AS warehouseAutoID,
	mastertbl.itemAutoID,
	standardjobmaster.jobAutoID AS jobAutoID,
	standardjobmaster.documentSystemCode AS documentSystemCode,
	standardjobmaster.documentDate AS documentDate,
	chart.GLSecondaryCode AS PLType,
	chart.GLDescription AS PLDescription,
	chart.GLSecondaryCode AS PLGLCode,
	chart.systemAccountCode AS PLSystemGLCode,
	chartbl.systemAccountCode AS BLSystemGLCode,
	chartbl.GLSecondaryCode AS BLGLCode,
	chartbl.GLDescription AS BLDescription,
	chartbl.GLSecondaryCode AS BLType,
	COALESCE ( SUM( mastertbl.qty ), 0 ) AS qtyUpdatedIssued,
	COALESCE ( SUM( mastertbl.totalCost ), 0 ) AS UpdatedTotalValue,
	warehouse.wareHouseCode AS wareHouseCode,
	warehouse.wareHouseLocation AS wareHouseLocation,
	warehouse.wareHouseDescription AS wareHouseDescription,
	itemmaster.itemSystemCode AS itemSystemCode,
	itemmaster.itemDescription AS itemDescription,
	unit.UnitShortCode AS UnitShortCode,
	mastertbl.transactionCurrencyID AS transactionCurrencyID,
	currencytransaction.CurrencyCode AS transactionCurrency,
	mastertbl.transactionExchangeRate AS transactionExchangeRate,
	mastertbl.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
	mastertbl.companyLocalCurrencyID AS companyLocalCurrencyID,
	currencycompany.CurrencyCode AS companyLocalCurrency,
	mastertbl.companyLocalExchangeRate AS companyLocalExchangeRate,
	mastertbl.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces,
	itemmaster.companyLocalWacAmount AS currentlWacAmount,
	mastertbl.companyReportingCurrencyID AS companyReportingCurrencyID,
	currencycompanyreporting.CurrencyCode AS companyReportingCurrency,
	mastertbl.companyReportingExchangeRate AS companyReportingExchangeRate,
	mastertbl.companyReportingCurrencyDecimalPlaces AS companyReportingCurrencyDecimalPlaces,
	segment.segmentCode AS segmentCode,
	itemmaster.mainCategory AS itemCategory,
	segment.segmentID AS segmentID
FROM
	srp_erp_mfq_standardjob_items mastertbl
	LEFT JOIN srp_erp_chartofaccounts chartbl ON chartbl.GLAutoID = mastertbl.glAutoID
	LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = mastertbl.itemAutoID
	LEFT JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = itemmaster.costGLAutoID
	LEFT JOIN srp_erp_mfq_standardjob standardjobmaster ON standardjobmaster.jobAutoID = mastertbl.jobAutoID
	LEFT JOIN srp_erp_warehousemaster warehouse ON warehouse.wareHouseAutoID = mastertbl.wareHouseAutoID
	LEFT JOIN srp_erp_unit_of_measure unit ON unit.UnitID = mastertbl.uomID
	LEFT JOIN srp_erp_currencymaster currencytransaction ON currencytransaction.currencyID = mastertbl.transactionCurrencyID
	LEFT JOIN srp_erp_currencymaster currencycompany ON currencycompany.currencyID = mastertbl.companyLocalCurrencyID
	LEFT JOIN srp_erp_currencymaster currencycompanyreporting ON currencycompanyreporting.currencyID = mastertbl.companyReportingCurrencyID
	LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = standardjobmaster.segmentID
	LEFT JOIN srp_erp_segment segment ON segment.segmentID = mfqsegment.segmentID
WHERE
	mastertbl.companyID = $companyid
	AND mastertbl.jobAutoID = $system_code
	AND type = 2
GROUP BY
	`mastertbl`.`itemAutoID`")->result_array();


        //general ledgers entries  start
        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'STJOB');
        if ($approvals_status == 1) {
            for ($i = 0; $i < count($inputgeneralarray); $i++) { //input general ledgers entry
                $inputgeneralledger[$i]['documentCode'] = $inputgeneralarray[$i]['documentCode'];
                $inputgeneralledger[$i]['documentMasterAutoID'] = $inputgeneralarray[$i]['documentMasterAutoID'];
                $inputgeneralledger[$i]['documentSystemCode'] = $inputgeneralarray[$i]['documentSystemCode'];
                $inputgeneralledger[$i]['documentDate'] = $inputgeneralarray[$i]['documentDate'];
                $inputgeneralledger[$i]['documentYear'] = $inputgeneralarray[$i]['documentYear'];
                $inputgeneralledger[$i]['documentMonth'] = $inputgeneralarray[$i]['documentMonth'];
                $inputgeneralledger[$i]['documentNarration'] = $inputgeneralarray[$i]['documentNarration'];
                $inputgeneralledger[$i]['chequeNumber'] = '';
                $inputgeneralledger[$i]['transactionCurrencyID'] = $inputgeneralarray[$i]['transactionCurrencyID'];
                $inputgeneralledger[$i]['transactionCurrency'] = $inputgeneralarray[$i]['transactionCurrency'];
                $inputgeneralledger[$i]['transactionExchangeRate'] = $inputgeneralarray[$i]['transactionExchangeRate'];
                $inputgeneralledger[$i]['transactionCurrencyDecimalPlaces'] = $inputgeneralarray[$i]['transactionCurrencyDecimalPlaces'];
                $inputgeneralledger[$i]['companyLocalCurrencyID'] = $inputgeneralarray[$i]['companyLocalCurrencyID'];
                $inputgeneralledger[$i]['companyLocalCurrency'] = $inputgeneralarray[$i]['companyLocalCurrency'];
                $inputgeneralledger[$i]['companyLocalExchangeRate'] = $inputgeneralarray[$i]['companyLocalExchangeRate'];
                $inputgeneralledger[$i]['companyLocalCurrencyDecimalPlaces'] = $inputgeneralarray[$i]['companyLocalCurrencyDecimalPlaces'];
                $inputgeneralledger[$i]['companyReportingCurrencyID'] = $inputgeneralarray[$i]['companyReportingCurrencyID'];
                $inputgeneralledger[$i]['companyReportingCurrency'] = $inputgeneralarray[$i]['companyReportingCurrency'];
                $inputgeneralledger[$i]['companyReportingExchangeRate'] = $inputgeneralarray[$i]['companyReportingExchangeRate'];
                $inputgeneralledger[$i]['companyReportingCurrencyDecimalPlaces'] = $inputgeneralarray[$i]['companyReportingCurrencyDecimalPlaces'];
                $inputgeneralledger[$i]['confirmedByEmpID'] = $inputgeneralarray[$i]['confirmedByEmpID'];
                $inputgeneralledger[$i]['confirmedByName'] = $inputgeneralarray[$i]['confirmedByName'];
                $inputgeneralledger[$i]['confirmedDate'] = $inputgeneralarray[$i]['confirmedDate'];
                $inputgeneralledger[$i]['approvedDate'] = $inputgeneralarray[$i]['approvedDate'];
                $inputgeneralledger[$i]['approvedbyEmpID'] = $inputgeneralarray[$i]['approvedbyEmpID'];
                $inputgeneralledger[$i]['approvedbyEmpName'] = $inputgeneralarray[$i]['approvedbyEmpName'];
                $inputgeneralledger[$i]['companyID'] = $inputgeneralarray[$i]['companyID'];
                $inputgeneralledger[$i]['companyCode'] = '';
                $inputgeneralledger[$i]['transactionAmount'] = ($inputgeneralarray[$i]['transactionAmount'] * -1);
                $inputgeneralledger[$i]['companyLocalAmount'] = ($inputgeneralarray[$i]['companyLocalAmount'] * -1);
                $inputgeneralledger[$i]['companyReportingAmount'] = ($inputgeneralarray[$i]['companyReportingAmount'] * -1);
                $inputgeneralledger[$i]['amount_type'] = $inputgeneralarray[$i]['amount_type'];
                $inputgeneralledger[$i]['GLAutoID'] = $inputgeneralarray[$i]['glautoID'];
                $inputgeneralledger[$i]['systemGLCode'] = $inputgeneralarray[$i]['systemGLCode'];
                $inputgeneralledger[$i]['GLCode'] = $inputgeneralarray[$i]['GLCode'];
                $inputgeneralledger[$i]['GLDescription'] = $inputgeneralarray[$i]['GLDescription'];
                $inputgeneralledger[$i]['GLType'] = $inputgeneralarray[$i]['GLType'];
                $inputgeneralledger[$i]['segmentID'] = $inputgeneralarray[$i]['segmentID'];
                $inputgeneralledger[$i]['segmentCode'] = $inputgeneralarray[$i]['segmentCode'];
                $inputgeneralledger[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $inputgeneralledger[$i]['createdPCID'] = $this->common_data['current_pc'];
                $inputgeneralledger[$i]['createdUserID'] = $this->common_data['current_userID'];
                $inputgeneralledger[$i]['createdDateTime'] = $this->common_data['current_date'];
                $inputgeneralledger[$i]['createdUserName'] = $this->common_data['current_user'];
                $inputgeneralledger[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $inputgeneralledger[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $inputgeneralledger[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $inputgeneralledger[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }
            for ($i = 0; $i < count($outputgeneralarray); $i++) { //Output general ledgers entry
                $outputgeneralledger[$i]['documentCode'] = $outputgeneralarray[$i]['documentCode'];
                $outputgeneralledger[$i]['documentMasterAutoID'] = $outputgeneralarray[$i]['documentMasterAutoID'];
                $outputgeneralledger[$i]['documentSystemCode'] = $outputgeneralarray[$i]['documentSystemCode'];
                $outputgeneralledger[$i]['documentDate'] = $outputgeneralarray[$i]['documentDate'];
                $outputgeneralledger[$i]['documentYear'] = $outputgeneralarray[$i]['documentYear'];
                $outputgeneralledger[$i]['documentMonth'] = $outputgeneralarray[$i]['documentMonth'];
                $outputgeneralledger[$i]['documentNarration'] = $outputgeneralarray[$i]['documentNarration'];
                $outputgeneralledger[$i]['chequeNumber'] = '';
                $outputgeneralledger[$i]['transactionCurrencyID'] = $outputgeneralarray[$i]['transactionCurrencyID'];
                $outputgeneralledger[$i]['transactionCurrency'] = $outputgeneralarray[$i]['transactionCurrency'];
                $outputgeneralledger[$i]['transactionExchangeRate'] = $outputgeneralarray[$i]['transactionExchangeRate'];
                $outputgeneralledger[$i]['transactionCurrencyDecimalPlaces'] = $outputgeneralarray[$i]['transactionCurrencyDecimalPlaces'];
                $outputgeneralledger[$i]['companyLocalCurrencyID'] = $outputgeneralarray[$i]['companyLocalCurrencyID'];
                $outputgeneralledger[$i]['companyLocalCurrency'] = $outputgeneralarray[$i]['companyLocalCurrency'];
                $outputgeneralledger[$i]['companyLocalExchangeRate'] = $outputgeneralarray[$i]['companyLocalExchangeRate'];
                $outputgeneralledger[$i]['companyLocalCurrencyDecimalPlaces'] = $outputgeneralarray[$i]['companyLocalCurrencyDecimalPlaces'];
                $outputgeneralledger[$i]['companyReportingCurrencyID'] = $outputgeneralarray[$i]['companyReportingCurrencyID'];
                $outputgeneralledger[$i]['companyReportingCurrency'] = $outputgeneralarray[$i]['companyReportingCurrency'];
                $outputgeneralledger[$i]['companyReportingExchangeRate'] = $outputgeneralarray[$i]['companyReportingExchangeRate'];
                $outputgeneralledger[$i]['companyReportingCurrencyDecimalPlaces'] = $outputgeneralarray[$i]['companyReportingCurrencyDecimalPlaces'];
                $outputgeneralledger[$i]['confirmedByEmpID'] = $outputgeneralarray[$i]['confirmedByEmpID'];
                $outputgeneralledger[$i]['confirmedByName'] = $outputgeneralarray[$i]['confirmedByName'];
                $outputgeneralledger[$i]['confirmedDate'] = $outputgeneralarray[$i]['confirmedDate'];
                $outputgeneralledger[$i]['approvedDate'] = $outputgeneralarray[$i]['approvedDate'];
                $outputgeneralledger[$i]['approvedbyEmpID'] = $outputgeneralarray[$i]['approvedbyEmpID'];
                $outputgeneralledger[$i]['approvedbyEmpName'] = $outputgeneralarray[$i]['approvedbyEmpName'];
                $outputgeneralledger[$i]['companyID'] = $outputgeneralarray[$i]['companyID'];
                $outputgeneralledger[$i]['companyCode'] = '';
                $outputgeneralledger[$i]['transactionAmount'] = $outputgeneralarray[$i]['transactionAmount'];
                $outputgeneralledger[$i]['companyLocalAmount'] = $outputgeneralarray[$i]['companyLocalAmount'];
                $outputgeneralledger[$i]['companyReportingAmount'] = $outputgeneralarray[$i]['companyReportingAmount'];
                $outputgeneralledger[$i]['amount_type'] = $outputgeneralarray[$i]['amount_type'];
                $outputgeneralledger[$i]['GLAutoID'] = $outputgeneralarray[$i]['glautoID'];
                $outputgeneralledger[$i]['systemGLCode'] = $outputgeneralarray[$i]['systemGLCode'];
                $outputgeneralledger[$i]['GLCode'] = $outputgeneralarray[$i]['GLCode'];
                $outputgeneralledger[$i]['GLDescription'] = $outputgeneralarray[$i]['GLDescription'];
                $outputgeneralledger[$i]['GLType'] = $outputgeneralarray[$i]['GLType'];
                $outputgeneralledger[$i]['segmentID'] = $outputgeneralarray[$i]['segmentID'];
                $outputgeneralledger[$i]['segmentCode'] = $outputgeneralarray[$i]['segmentCode'];
                $outputgeneralledger[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $outputgeneralledger[$i]['createdPCID'] = $this->common_data['current_pc'];
                $outputgeneralledger[$i]['createdUserID'] = $this->common_data['current_userID'];
                $outputgeneralledger[$i]['createdDateTime'] = $this->common_data['current_date'];
                $outputgeneralledger[$i]['createdUserName'] = $this->common_data['current_user'];
                $outputgeneralledger[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $outputgeneralledger[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $outputgeneralledger[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $outputgeneralledger[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }

            for ($i = 0; $i < count($labourgeneralledgerarray); $i++) { //labour general ledgers entry
                $labourgeneralledger[$i]['documentCode'] = $labourgeneralledgerarray[$i]['documentCode'];
                $labourgeneralledger[$i]['documentMasterAutoID'] = $labourgeneralledgerarray[$i]['documentMasterAutoID'];
                $labourgeneralledger[$i]['documentSystemCode'] = $labourgeneralledgerarray[$i]['documentSystemCode'];
                $labourgeneralledger[$i]['documentDate'] = $labourgeneralledgerarray[$i]['documentDate'];
                $labourgeneralledger[$i]['documentYear'] = $labourgeneralledgerarray[$i]['documentYear'];
                $labourgeneralledger[$i]['documentMonth'] = $labourgeneralledgerarray[$i]['documentMonth'];
                $labourgeneralledger[$i]['documentNarration'] = $labourgeneralledgerarray[$i]['documentNarration'];
                $labourgeneralledger[$i]['chequeNumber'] = '';
                $labourgeneralledger[$i]['transactionCurrencyID'] = $labourgeneralledgerarray[$i]['transactionCurrencyID'];
                $labourgeneralledger[$i]['transactionCurrency'] = $labourgeneralledgerarray[$i]['transactionCurrency'];
                $labourgeneralledger[$i]['transactionExchangeRate'] = $labourgeneralledgerarray[$i]['transactionExchangeRate'];
                $labourgeneralledger[$i]['transactionCurrencyDecimalPlaces'] = $labourgeneralledgerarray[$i]['transactionCurrencyDecimalPlaces'];
                $labourgeneralledger[$i]['companyLocalCurrencyID'] = $labourgeneralledgerarray[$i]['companyLocalCurrencyID'];
                $labourgeneralledger[$i]['companyLocalCurrency'] = $labourgeneralledgerarray[$i]['companyLocalCurrency'];
                $labourgeneralledger[$i]['companyLocalExchangeRate'] = $labourgeneralledgerarray[$i]['companyLocalExchangeRate'];
                $labourgeneralledger[$i]['companyLocalCurrencyDecimalPlaces'] = $labourgeneralledgerarray[$i]['companyLocalCurrencyDecimalPlaces'];
                $labourgeneralledger[$i]['companyReportingCurrencyID'] = $labourgeneralledgerarray[$i]['companyReportingCurrencyID'];
                $labourgeneralledger[$i]['companyReportingCurrency'] = $labourgeneralledgerarray[$i]['companyReportingCurrency'];
                $labourgeneralledger[$i]['companyReportingExchangeRate'] = $labourgeneralledgerarray[$i]['companyReportingExchangeRate'];
                $labourgeneralledger[$i]['companyReportingCurrencyDecimalPlaces'] = $labourgeneralledgerarray[$i]['companyReportingCurrencyDecimalPlaces'];
                $labourgeneralledger[$i]['confirmedByEmpID'] = $labourgeneralledgerarray[$i]['confirmedByEmpID'];
                $labourgeneralledger[$i]['confirmedByName'] = $labourgeneralledgerarray[$i]['confirmedByName'];
                $labourgeneralledger[$i]['confirmedDate'] = $labourgeneralledgerarray[$i]['confirmedDate'];
                $labourgeneralledger[$i]['approvedDate'] = $labourgeneralledgerarray[$i]['approvedDate'];
                $labourgeneralledger[$i]['approvedbyEmpID'] = $labourgeneralledgerarray[$i]['approvedbyEmpID'];
                $labourgeneralledger[$i]['approvedbyEmpName'] = $labourgeneralledgerarray[$i]['approvedbyEmpName'];
                $labourgeneralledger[$i]['companyID'] = $labourgeneralledgerarray[$i]['companyID'];
                $labourgeneralledger[$i]['companyCode'] = '';
                $labourgeneralledger[$i]['transactionAmount'] = ($labourgeneralledgerarray[$i]['transactionAmount'] * -1);
                $labourgeneralledger[$i]['companyLocalAmount'] = ($labourgeneralledgerarray[$i]['companyLocalAmount'] * -1);
                $labourgeneralledger[$i]['companyReportingAmount'] = ($labourgeneralledgerarray[$i]['companyReportingAmount'] * -1);
                $labourgeneralledger[$i]['amount_type'] = $labourgeneralledgerarray[$i]['amount_type'];
                $labourgeneralledger[$i]['GLAutoID'] = $labourgeneralledgerarray[$i]['glautoID'];
                $labourgeneralledger[$i]['systemGLCode'] = $labourgeneralledgerarray[$i]['systemGLCode'];
                $labourgeneralledger[$i]['GLCode'] = $labourgeneralledgerarray[$i]['GLCode'];
                $labourgeneralledger[$i]['GLDescription'] = $labourgeneralledgerarray[$i]['GLDescription'];
                $labourgeneralledger[$i]['GLType'] = $labourgeneralledgerarray[$i]['GLType'];
                $labourgeneralledger[$i]['segmentID'] = $labourgeneralledgerarray[$i]['segmentID'];
                $labourgeneralledger[$i]['segmentCode'] = $labourgeneralledgerarray[$i]['segmentCode'];
                $labourgeneralledger[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $labourgeneralledger[$i]['createdPCID'] = $this->common_data['current_pc'];
                $labourgeneralledger[$i]['createdUserID'] = $this->common_data['current_userID'];
                $labourgeneralledger[$i]['createdDateTime'] = $this->common_data['current_date'];
                $labourgeneralledger[$i]['createdUserName'] = $this->common_data['current_user'];
                $labourgeneralledger[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $labourgeneralledger[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $labourgeneralledger[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $labourgeneralledger[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }

            for ($i = 0; $i < count($overheadgeneralledgerarray); $i++) { //overhead general ledgers entry
                $overheadgeneralledger[$i]['documentCode'] = $overheadgeneralledgerarray[$i]['documentCode'];
                $overheadgeneralledger[$i]['documentMasterAutoID'] = $overheadgeneralledgerarray[$i]['documentMasterAutoID'];
                $overheadgeneralledger[$i]['documentSystemCode'] = $overheadgeneralledgerarray[$i]['documentSystemCode'];
                $overheadgeneralledger[$i]['documentDate'] = $overheadgeneralledgerarray[$i]['documentDate'];
                $overheadgeneralledger[$i]['documentYear'] = $overheadgeneralledgerarray[$i]['documentYear'];
                $overheadgeneralledger[$i]['documentMonth'] = $overheadgeneralledgerarray[$i]['documentMonth'];
                $overheadgeneralledger[$i]['documentNarration'] = $overheadgeneralledgerarray[$i]['documentNarration'];
                $overheadgeneralledger[$i]['chequeNumber'] = '';
                $overheadgeneralledger[$i]['transactionCurrencyID'] = $overheadgeneralledgerarray[$i]['transactionCurrencyID'];
                $overheadgeneralledger[$i]['transactionCurrency'] = $overheadgeneralledgerarray[$i]['transactionCurrency'];
                $overheadgeneralledger[$i]['transactionExchangeRate'] = $overheadgeneralledgerarray[$i]['transactionExchangeRate'];
                $overheadgeneralledger[$i]['transactionCurrencyDecimalPlaces'] = $overheadgeneralledgerarray[$i]['transactionCurrencyDecimalPlaces'];
                $overheadgeneralledger[$i]['companyLocalCurrencyID'] = $overheadgeneralledgerarray[$i]['companyLocalCurrencyID'];
                $overheadgeneralledger[$i]['companyLocalCurrency'] = $overheadgeneralledgerarray[$i]['companyLocalCurrency'];
                $overheadgeneralledger[$i]['companyLocalExchangeRate'] = $overheadgeneralledgerarray[$i]['companyLocalExchangeRate'];
                $overheadgeneralledger[$i]['companyLocalCurrencyDecimalPlaces'] = $overheadgeneralledgerarray[$i]['companyLocalCurrencyDecimalPlaces'];
                $overheadgeneralledger[$i]['companyReportingCurrencyID'] = $overheadgeneralledgerarray[$i]['companyReportingCurrencyID'];
                $overheadgeneralledger[$i]['companyReportingCurrency'] = $overheadgeneralledgerarray[$i]['companyReportingCurrency'];
                $overheadgeneralledger[$i]['companyReportingExchangeRate'] = $overheadgeneralledgerarray[$i]['companyReportingExchangeRate'];
                $overheadgeneralledger[$i]['companyReportingCurrencyDecimalPlaces'] = $overheadgeneralledgerarray[$i]['companyReportingCurrencyDecimalPlaces'];
                $overheadgeneralledger[$i]['confirmedByEmpID'] = $overheadgeneralledgerarray[$i]['confirmedByEmpID'];
                $overheadgeneralledger[$i]['confirmedByName'] = $overheadgeneralledgerarray[$i]['confirmedByName'];
                $overheadgeneralledger[$i]['confirmedDate'] = $overheadgeneralledgerarray[$i]['confirmedDate'];
                $overheadgeneralledger[$i]['approvedDate'] = $overheadgeneralledgerarray[$i]['approvedDate'];
                $overheadgeneralledger[$i]['approvedbyEmpID'] = $overheadgeneralledgerarray[$i]['approvedbyEmpID'];
                $overheadgeneralledger[$i]['approvedbyEmpName'] = $overheadgeneralledgerarray[$i]['approvedbyEmpName'];
                $overheadgeneralledger[$i]['companyID'] = $overheadgeneralledgerarray[$i]['companyID'];
                $overheadgeneralledger[$i]['companyCode'] = '';
                $overheadgeneralledger[$i]['transactionAmount'] = ($overheadgeneralledgerarray[$i]['transactionAmount'] * -1);
                $overheadgeneralledger[$i]['companyLocalAmount'] = ($overheadgeneralledgerarray[$i]['companyLocalAmount'] * -1);
                $overheadgeneralledger[$i]['companyReportingAmount'] = ($overheadgeneralledgerarray[$i]['companyReportingAmount'] * -1);
                $overheadgeneralledger[$i]['amount_type'] = $overheadgeneralledgerarray[$i]['amount_type'];
                $overheadgeneralledger[$i]['GLAutoID'] = $overheadgeneralledgerarray[$i]['glautoID'];
                $overheadgeneralledger[$i]['systemGLCode'] = $overheadgeneralledgerarray[$i]['systemGLCode'];
                $overheadgeneralledger[$i]['GLCode'] = $overheadgeneralledgerarray[$i]['GLCode'];
                $overheadgeneralledger[$i]['GLDescription'] = $overheadgeneralledgerarray[$i]['GLDescription'];
                $overheadgeneralledger[$i]['GLType'] = $overheadgeneralledgerarray[$i]['GLType'];
                $overheadgeneralledger[$i]['segmentID'] = $overheadgeneralledgerarray[$i]['segmentID'];
                $overheadgeneralledger[$i]['segmentCode'] = $overheadgeneralledgerarray[$i]['segmentCode'];
                $overheadgeneralledger[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $overheadgeneralledger[$i]['createdPCID'] = $this->common_data['current_pc'];
                $overheadgeneralledger[$i]['createdUserID'] = $this->common_data['current_userID'];
                $overheadgeneralledger[$i]['createdDateTime'] = $this->common_data['current_date'];
                $overheadgeneralledger[$i]['createdUserName'] = $this->common_data['current_user'];
                $overheadgeneralledger[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $overheadgeneralledger[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $overheadgeneralledger[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $overheadgeneralledger[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }/* end generalledger entries*/


            for ($i = 0; $i < count($details_arr); $i++) { //input itemlledger entries
                if ($details_arr[$i]['itemCategory'] == 'Inventory' or $details_arr[$i]['itemCategory'] == 'Non Inventory' or $details_arr[$i]['itemCategory'] =='Service') {

                    $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                    $qty = ($details_arr[$i]['qtyUpdatedIssued'] / 1);
                    $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = ($item['currentStock'] - ($details_arr[$i]['qtyUpdatedIssued'] / 1));
                    $item_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $item_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];

                    $itemSystemCode = $details_arr[$i]['itemAutoID'];
                    $location = $details_arr[$i]['wareHouseLocation'];
                    $wareHouseAutoID = $details_arr[$i]['warehouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");

                    $itemledger_arr[$i]['documentID'] = 'STJOB';
                    $itemledger_arr[$i]['documentCode'] = 'STJOB';
                    $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['jobAutoID'];
                    $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['documentSystemCode'];
                    $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['documentDate'];
                    $itemledger_arr[$i]['referenceNumber'] = '';
                    $itemledger_arr[$i]['companyFinanceYearID'] = $finaceper['companyFinanceYearID'];
                    $itemledger_arr[$i]['companyFinanceYear'] = $finaceper['companyFinanceYearID'];
                    $itemledger_arr[$i]['FYBegin'] = $finaceper['dateFrom'];
                    $itemledger_arr[$i]['FYEnd'] = $finaceper['dateTo'];
                    $itemledger_arr[$i]['FYPeriodDateFrom'] = $finaceper['beginingDate'];
                    $itemledger_arr[$i]['FYPeriodDateTo'] = $finaceper['endingDate'];
                    $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['warehouseAutoID'];
                    $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                    $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                    $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                    /*    $itemledger_arr[$i]['projectID'] = $details_arr[$i]['projectID'];
                        $itemledger_arr[$i]['projectExchangeRate'] = $details_arr[$i]['projectExchangeRate'];*/
                    $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['UnitShortCode'];
                    $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['UnitShortCode'];
                    $itemledger_arr[$i]['transactionQTY'] = ($details_arr[$i]['qtyUpdatedIssued'] * -1);
                    $itemledger_arr[$i]['convertionRate'] = 1;
                    $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['uomID'];
                    $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['uomID'];
                    $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['UnitShortCode'];
                    $itemledger_arr[$i]['convertionRate'] = 1;
                    $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['costGLAutoID'];
                    $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];

                    $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['glAutoID'];
                    $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];

                    $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['transactionCurrencyID'];
                    $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['transactionCurrency'];
                    $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['transactionExchangeRate'];
                    $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['transactionCurrencyDecimalPlaces'];
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

                    $itemledger_arr[$i]['confirmedYN'] = 1;
                    $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr[$i]['approvedYN'] = 1;
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
            for ($i = 0; $i < count($details_arroutput); $i++) { //output itemlledger entries
                if ($details_arroutput[$i]['itemCategory'] == 'Inventory' or $details_arroutput[$i]['itemCategory'] == 'Non Inventory') {
                    $item = fetch_item_data($details_arroutput[$i]['itemAutoID']);
                    $item_arroutput[$i]['itemAutoID'] = $details_arroutput[$i]['itemAutoID'];
                    $item_arroutput[$i]['currentStock'] = ($item['currentStock'] + ($details_arroutput[$i]['qtyUpdatedIssued'] / 1));

                    $item_arroutput[$i]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) + $details_arroutput[$i]['UpdatedTotalValue']) / ($item_arroutput[$i]['currentStock'])), $mastertbldetails['companyLocalCurrencyDecimalPlaces']);
                    $item_arroutput[$i]['companyReportingWacAmount'] = round((((($item['currentStock'] * $item['companyLocalWacAmount'])/$details_arroutput[$i]['companyReportingExchangeRate']) + $details_arroutput[$i]['UpdatedTotalValue']) /($item_arroutput[$i]['currentStock'])), $mastertbldetails['companyReportingCurrencyDecimalPlaces']);

                    $qty = ($details_arroutput[$i]['qtyUpdatedIssued'] / 1);
                    $itemSystemCode = $details_arroutput[$i]['itemAutoID'];
                    $location = $details_arroutput[$i]['wareHouseLocation'];
                    $wareHouseAutoID = $details_arroutput[$i]['warehouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");

                    $itemledger_arroutput[$i]['documentID'] = 'STJOB';
                    $itemledger_arroutput[$i]['documentCode'] = 'STJOB';
                    $itemledger_arroutput[$i]['documentAutoID'] = $details_arroutput[$i]['jobAutoID'];
                    $itemledger_arroutput[$i]['documentSystemCode'] = $details_arroutput[$i]['documentSystemCode'];
                    $itemledger_arroutput[$i]['documentDate'] = $details_arroutput[$i]['documentDate'];
                    $itemledger_arroutput[$i]['referenceNumber'] = '';
                    $itemledger_arroutput[$i]['companyFinanceYearID'] = $finaceper['companyFinanceYearID'];
                    $itemledger_arroutput[$i]['companyFinanceYear'] = $finaceper['companyFinanceYearID'];
                    $itemledger_arroutput[$i]['FYBegin'] = $finaceper['dateFrom'];
                    $itemledger_arroutput[$i]['FYEnd'] = $finaceper['dateTo'];
                    $itemledger_arroutput[$i]['FYPeriodDateFrom'] = $finaceper['beginingDate'];
                    $itemledger_arroutput[$i]['FYPeriodDateTo'] = $finaceper['endingDate'];
                    $itemledger_arroutput[$i]['wareHouseAutoID'] = $details_arroutput[$i]['warehouseAutoID'];
                    $itemledger_arroutput[$i]['wareHouseCode'] = $details_arroutput[$i]['wareHouseCode'];
                    $itemledger_arroutput[$i]['wareHouseLocation'] = $details_arroutput[$i]['wareHouseLocation'];
                    $itemledger_arroutput[$i]['wareHouseDescription'] = $details_arroutput[$i]['wareHouseDescription'];
                    /*    $itemledger_arr[$i]['projectID'] = $details_arroutput[$i]['projectID'];
                        $itemledger_arr[$i]['projectExchangeRate'] = $details_arr[$i]['projectExchangeRate'];*/
                    $itemledger_arroutput[$i]['itemAutoID'] = $details_arroutput[$i]['itemAutoID'];
                    $itemledger_arroutput[$i]['itemSystemCode'] = $details_arroutput[$i]['itemSystemCode'];
                    $itemledger_arroutput[$i]['itemDescription'] = $details_arroutput[$i]['itemDescription'];
                    $itemledger_arroutput[$i]['defaultUOM'] = $details_arroutput[$i]['UnitShortCode'];
                    $itemledger_arroutput[$i]['transactionUOM'] = $details_arroutput[$i]['UnitShortCode'];
                    $itemledger_arroutput[$i]['transactionQTY'] = ($details_arroutput[$i]['qtyUpdatedIssued'] * 1);
                    $itemledger_arroutput[$i]['convertionRate'] = 1;
                    $itemledger_arroutput[$i]['currentStock'] = $item_arroutput[$i]['currentStock'];
                    $itemledger_arroutput[$i]['itemAutoID'] = $details_arroutput[$i]['itemAutoID'];
                    $itemledger_arroutput[$i]['itemSystemCode'] = $details_arroutput[$i]['itemSystemCode'];
                    $itemledger_arroutput[$i]['itemDescription'] = $details_arroutput[$i]['itemDescription'];
                    $itemledger_arroutput[$i]['defaultUOMID'] = $details_arroutput[$i]['uomID'];
                    $itemledger_arroutput[$i]['transactionUOMID'] = $details_arroutput[$i]['uomID'];
                    $itemledger_arroutput[$i]['transactionUOM'] = $details_arroutput[$i]['UnitShortCode'];
                    $itemledger_arroutput[$i]['convertionRate'] = 1;
                    $itemledger_arroutput[$i]['PLGLAutoID'] = $details_arroutput[$i]['costGLAutoID'];
                    $itemledger_arroutput[$i]['PLSystemGLCode'] = $details_arroutput[$i]['PLSystemGLCode'];
                    $itemledger_arroutput[$i]['PLGLCode'] = $details_arroutput[$i]['PLGLCode'];
                    $itemledger_arroutput[$i]['PLDescription'] = $details_arroutput[$i]['PLDescription'];
                    $itemledger_arroutput[$i]['PLType'] = $details_arroutput[$i]['PLType'];
                    $itemledger_arroutput[$i]['BLGLAutoID'] = $details_arroutput[$i]['glAutoID'];
                    $itemledger_arroutput[$i]['BLSystemGLCode'] = $details_arroutput[$i]['BLSystemGLCode'];
                    $itemledger_arroutput[$i]['BLGLCode'] = $details_arroutput[$i]['BLGLCode'];
                    $itemledger_arroutput[$i]['BLDescription'] = $details_arroutput[$i]['BLDescription'];
                    $itemledger_arroutput[$i]['BLType'] = $details_arroutput[$i]['BLType'];
                    $itemledger_arroutput[$i]['transactionCurrencyID'] = $details_arroutput[$i]['transactionCurrencyID'];
                    $itemledger_arroutput[$i]['transactionCurrency'] = $details_arroutput[$i]['transactionCurrency'];
                    $itemledger_arroutput[$i]['transactionExchangeRate'] = $details_arroutput[$i]['transactionExchangeRate'];
                    $itemledger_arroutput[$i]['transactionCurrencyDecimalPlaces'] = $details_arroutput[$i]['transactionCurrencyDecimalPlaces'];
                    $itemledger_arroutput[$i]['transactionAmount'] = (round($details_arroutput[$i]['UpdatedTotalValue'], $itemledger_arroutput[$i]['transactionCurrencyDecimalPlaces']) * 1);
                    $itemledger_arroutput[$i]['companyLocalCurrencyID'] = $details_arroutput[$i]['companyLocalCurrencyID'];
                    $itemledger_arroutput[$i]['companyLocalCurrency'] = $details_arroutput[$i]['companyLocalCurrency'];
                    $itemledger_arroutput[$i]['companyLocalExchangeRate'] = $details_arroutput[$i]['companyLocalExchangeRate'];
                    $itemledger_arroutput[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arroutput[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arroutput[$i]['companyLocalAmount'] = (round(($details_arroutput[$i]['UpdatedTotalValue'] / $details_arroutput[$i]['companyLocalExchangeRate']), $itemledger_arroutput[$i]['companyLocalCurrencyDecimalPlaces']) * 1);
                    $itemledger_arroutput[$i]['companyLocalWacAmount'] = round($details_arroutput[$i]['currentlWacAmount'], $itemledger_arroutput[$i]['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arroutput[$i]['companyReportingCurrencyID'] = $details_arroutput[$i]['companyReportingCurrencyID'];
                    $itemledger_arroutput[$i]['companyReportingCurrency'] = $details_arroutput[$i]['companyReportingCurrency'];
                    $itemledger_arroutput[$i]['companyReportingExchangeRate'] = $details_arroutput[$i]['companyReportingExchangeRate'];
                    $itemledger_arroutput[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arroutput[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arroutput[$i]['companyReportingAmount'] = (round(($details_arroutput[$i]['UpdatedTotalValue'] / $details_arroutput[$i]['companyReportingExchangeRate']), $itemledger_arroutput[$i]['companyReportingCurrencyDecimalPlaces']) * 1);
                    $itemledger_arroutput[$i]['companyReportingWacAmount'] = round(($itemledger_arroutput[$i]['companyLocalWacAmount'] / $itemledger_arroutput[$i]['companyReportingExchangeRate']), $itemledger_arroutput[$i]['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arroutput[$i]['confirmedYN'] = 1;
                    $itemledger_arroutput[$i]['confirmedByEmpID'] = $details_arroutput[$i]['confirmedByEmpID'];
                    $itemledger_arroutput[$i]['confirmedByName'] = $details_arroutput[$i]['confirmedDate'];
                    $itemledger_arroutput[$i]['confirmedDate'] = $details_arroutput[$i]['confirmedDate'];
                    $itemledger_arroutput[$i]['approvedYN'] = 1;
                    $itemledger_arroutput[$i]['approvedDate'] = $details_arroutput[$i]['approvedDate'];
                    $itemledger_arroutput[$i]['approvedbyEmpID'] = $details_arroutput[$i]['approvedbyEmpID'];
                    $itemledger_arroutput[$i]['approvedbyEmpName'] = $details_arroutput[$i]['approvedbyEmpName'];
                    $itemledger_arroutput[$i]['segmentID'] = $details_arroutput[$i]['segmentID'];
                    $itemledger_arroutput[$i]['segmentCode'] = $details_arroutput[$i]['segmentCode'];
                    $itemledger_arroutput[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                    $itemledger_arroutput[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $itemledger_arroutput[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $itemledger_arroutput[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $itemledger_arroutput[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $itemledger_arroutput[$i]['createdDateTime'] = $this->common_data['current_date'];
                    $itemledger_arroutput[$i]['createdUserName'] = $this->common_data['current_user'];
                    $itemledger_arroutput[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                    $itemledger_arroutput[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                    $itemledger_arroutput[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                    $itemledger_arroutput[$i]['modifiedUserName'] = $this->common_data['current_user'];
                }
            }

            if (!empty($inputgeneralledger)) {
                $this->db->insert_batch('srp_erp_generalledger', $inputgeneralledger);
            }
            if (!empty($outputgeneralledger)) {
                $this->db->insert_batch('srp_erp_generalledger', $outputgeneralledger);
            }
            if (!empty($labourgeneralledger)) {
                $this->db->insert_batch('srp_erp_generalledger', $labourgeneralledger);
            }
            if (!empty($overheadgeneralledger)) {
                $this->db->insert_batch('srp_erp_generalledger', $overheadgeneralledger);
            }

            if (!empty($item_arr)) {
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }
            if (!empty($item_arroutput)) {
                $this->db->update_batch('srp_erp_itemmaster', $item_arroutput, 'itemAutoID');
            }

            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            if (!empty($itemledger_arroutput)) {
                $itemledger_arroutput = array_values($itemledger_arroutput);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arroutput);
            }

            $data['approvedYN'] = $status;
            $data['completionPercenatage'] = '100';
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            $this->db->where('jobAutoID', $system_code);
            $this->db->update('srp_erp_mfq_standardjob', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $this->db->trans_commit();
            return array('s', 'Standard Job Card Approved Successfully');
        }

    }

    function warehousefinishgoods()
    {
        $companyid = current_companyID();
        $type = $this->input->post('itemtype');
        if ($type == 2) {
            $data = $this->db->query("SELECT srp_erp_warehousemaster.wareHouseAutoID,`srp_erp_warehousemaster`.`wareHouseDescription` FROM `srp_erp_warehousemaster` WHERE `srp_erp_warehousemaster`.`companyID` = '$companyid' AND `srp_erp_warehousemaster`.`wareHouseAutoID` IS NOT NULL AND `warehouseType` = 1")->result_array();
        } else if ($type == 3) {
            $data = $this->db->query("SELECT srp_erp_warehousemaster.wareHouseAutoID,`srp_erp_warehousemaster`.`wareHouseDescription` FROM `srp_erp_warehousemaster` WHERE `srp_erp_warehousemaster`.`companyID` = '$companyid' AND `srp_erp_warehousemaster`.`wareHouseAutoID` IS NOT NULL")->result_array();
        }
        return $data;

    }

    function fetch_double_entry_standardjobcard($system_code, $code = null)

    {

        $gl_array = array();
        $inv_total = 0;
        $cr_total = 0;
        $party_total = 0;
        $companyLocal_total = 0;
        $companyReporting_total = 0;
        $tax_total = 0;
        $companyid = current_companyID();
        $m_arr = array();
        $p_arr = array();
        $e_p_arr_labour = array();
        $e_m_arr_labour = array();
        $e_p_arr_overhead = array();
        $e_m_arr_overhead= array();
        $e_p_arr_output= array();
        $e_m_arr_output= array();

        $e_m_arr = array();
        $e_p_arr = array();

        $debitNoteAmount = 0;

        /*  $gl_array['gl_detail'] = array();


        /*  $this->db->select('bookingMasterID,bookingDetailsID,srp_erp_ioubookingdetails.glAutoID as GLAutoID,chart.systemAccountCode as SystemGLCode,chart.GLSecondaryCode as GLCode,chart.GLDescription as GLDescription,chart.subCategory as GLType,srp_erp_ioubookingdetails.transactionAmount,srp_erp_ioubookingdetails.segmentID as segmentID,segment.segmentCode as segmentCode,companyLocalExchangeRate,companyReportingExchangeRate,empCurrencyExchangeRate as partyExchangeRate,srp_erp_ioubookingdetails.description as ioudescription');
          $this->db->where('bookingMasterID', $ioubookingmasterid);
          $this->db->join('srp_erp_chartofaccounts chart', 'chart.GLAutoID = srp_erp_ioubookingdetails.GLAutoID', 'left');
          $this->db->join('srp_erp_segment segment', 'segment.segmentID = srp_erp_ioubookingdetails.segmentID', 'left');
          $detail = $this->db->get('srp_erp_ioubookingdetails')->result_array();*/

        $master = $this->db->query("select srp_erp_mfq_standardjob.*,srp_erp_currencymaster.CurrencyCode as CurrencyCode  from  srp_erp_mfq_standardjob LEFT JOIN srp_erp_currencymaster on srp_erp_currencymaster.currencyID = srp_erp_mfq_standardjob.transactionCurrencyID where companyID = $companyid And jobAutoID = $system_code")->row_array();

        $labour = $this->db->query("SELECT 'STJOB' as documentCode,labour.jobLabourTaskID as auto_id,job.jobAutoID as documentMasterAutoID,job.documentSystemCode as documentSystemCode,job.documentDate as documentDate,year(job.documentDate) as documentYear,month(job.documentDate) as documentMonth,
job.narration as documentNarration,labour.GLAutoID as glautoID,account.systemAccountCode as systemGLCode,account.GLSecondaryCode as GLCode,account.GLDescription as GLDescription,account.subcategory as GLType,'cr' as amount_type,labour.transactionCurrencyID as transactionCurrencyID,labour.transactionCurrency as transactionCurrency,labour.transactionExchangeRate as transactionExchangeRate,
sum(labour.transactionAmount) as transactionAmount,labour.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,labour.companyLocalCurrencyID as companyLocalCurrencyID,labour.companyLocalCurrency as companyLocalCurrency,
labour.companyLocalExchangeRate as companyLocalExchangeRate,sum(labour.companyLocalAmount) as companyLocalAmount,labour.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,labour.companyreportingcurrencyID as companyReportingCurrencyID,labour.companyreportingcurrency as companyReportingCurrency,
labour.companyreportingExchangeRate as companyReportingExchangeRate,sum(labour.companyReportingAmount) as companyReportingAmount,labour.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces,job.confirmedByEmpID,job.confirmedByName,
job.confirmedDate,job.approvedDate,job.approvedbyEmpID,job.approvedbyEmpName,segment.segmentID,segment.segmentCode,job.companyID ,company_code as companyCode,job.createdUserGroup,job.createdPCID,job.createdUserID,job.createdDateTime,
job.createdUserName FROM srp_erp_mfq_standardjob_labourtask labour   left JOIN srp_erp_mfq_standardjob job ON labour.jobAutoID = job.jobAutoID left JOIN srp_erp_chartofaccounts account on account.glautoID=labour.glautoID Left JOIN srp_erp_currencymaster currency on currency.currencyID=transactionCurrency
LEFT JOIN srp_erp_mfq_segment segmentmfq on segmentmfq.mfqSegmentID = job.segmentID LEFT JOIN srp_erp_segment segment ON segment.segmentID = segmentmfq.segmentID  left JOIN srp_erp_company company on company.company_id=job.companyID where job.companyID = $companyid  AND job.jobAutoID=$system_code group by labour.glAutoID")->result_array();

        $rawmaterial = $this->db->query("SELECT 'STJOB' AS documentCode,items.jobItemID as auto_id,job.jobAutoID AS documentMasterAutoID,job.documentSystemCode AS documentSystemCode,job.documentDate AS documentDate,YEAR ( job.documentDate ) AS documentYear,
MONTH ( job.documentDate ) AS documentMonth,job.narration AS documentNarration,items.GLAutoID AS glautoID,account.systemAccountCode AS systemGLCode,account.GLSecondaryCode AS GLCode,account.GLDescription AS GLDescription,account.subcategory AS GLType,
'cr' AS amount_type,items.transactionCurrencyID AS transactionCurrencyID,items.transactionCurrency AS transactionCurrency,items.transactionExchangeRate AS transactionExchangeRate,sum( items.transactionAmount ) AS transactionAmount,items.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,items.companyLocalCurrencyID AS companyLocalCurrencyID,items.companyLocalCurrency AS companyLocalCurrency,
items.companyLocalExchangeRate AS companyLocalExchangeRate,sum( items.companyLocalAmount ) AS companyLocalAmount,items.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces,items.companyreportingcurrencyID AS companyReportingCurrencyID,items.companyreportingcurrency AS companyReportingCurrency,
items.companyreportingExchangeRate AS companyReportingExchangeRate,sum( items.companyReportingAmount ) AS companyReportingAmount,items.companyReportingCurrencyDecimalPlaces AS companyReportingCurrencyDecimalPlaces,job.confirmedByEmpID,
job.confirmedByName,job.confirmedDate,job.approvedDate,job.approvedbyEmpID,job.approvedbyEmpName,job.segmentID,segment.segmentCode,job.companyID,company_code AS companyCode,job.createdUserGroup,job.createdPCID,job.createdUserID,
job.createdDateTime,job.createdUserName FROM srp_erp_mfq_standardjob_items items LEFT JOIN srp_erp_mfq_standardjob job ON items.jobAutoID = job.jobAutoID LEFT JOIN srp_erp_chartofaccounts account ON account.glautoID = items.glautoID
LEFT JOIN srp_erp_currencymaster currency ON currency.currencyID = transactionCurrency LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = job.segmentID LEFT JOIN srp_erp_segment segment ON segment.segmentID = mfqsegment.segmentID
LEFT JOIN srp_erp_company company ON company.company_id = job.companyID WHERE job.companyID = $companyid AND job.jobAutoID = $system_code AND items.type = 1 GROUP BY items.glAutoID")->result_array();

        $overhead = $this->db->query("SELECT 'STJOB' AS documentCode,overhead.overHeadID as auto_id,job.jobAutoID AS documentMasterAutoID,job.documentSystemCode AS documentSystemCode,job.documentDate AS documentDate,
YEAR ( job.documentDate ) AS documentYear,MONTH ( job.documentDate ) AS documentMonth,job.narration AS documentNarration,overhead.GLAutoID AS glautoID,account.systemAccountCode AS systemGLCode,account.GLSecondaryCode AS GLCode,
account.GLDescription AS GLDescription,account.subcategory AS GLType,'cr' AS amount_type,overhead.transactionCurrencyID AS transactionCurrencyID,overhead.transactionCurrency AS transactionCurrency,overhead.transactionExchangeRate AS transactionExchangeRate,
sum( overhead.transactionAmount ) AS transactionAmount,overhead.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,overhead.companyLocalCurrencyID AS companyLocalCurrencyID,overhead.companyLocalCurrency AS companyLocalCurrency,
overhead.companyLocalExchangeRate AS companyLocalExchangeRate,sum( overhead.companyLocalAmount ) AS companyLocalAmount,overhead.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces,overhead.companyreportingcurrencyID AS companyReportingCurrencyID,
overhead.companyreportingcurrency AS companyReportingCurrency,overhead.companyreportingExchangeRate AS companyReportingExchangeRate,sum( overhead.companyReportingAmount ) AS companyReportingAmount,
overhead.companyReportingCurrencyDecimalPlaces AS companyReportingCurrencyDecimalPlaces,job.confirmedByEmpID,job.confirmedByName,job.confirmedDate,job.approvedDate,job.approvedbyEmpID,job.approvedbyEmpName,segment.segmentID,
segment.segmentCode,job.companyID,company_code AS companyCode,job.createdUserGroup,job.createdPCID,job.createdUserID,job.createdDateTime,job.createdUserName FROM srp_erp_mfq_standardjob_overhead overhead LEFT JOIN srp_erp_mfq_standardjob job ON overhead.jobAutoID = job.jobAutoID LEFT JOIN srp_erp_chartofaccounts account ON account.glautoID = overhead.glautoID
LEFT JOIN srp_erp_currencymaster currency ON currency.currencyID = transactionCurrency LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = job.segmentID LEFT JOIN srp_erp_segment segment ON segment.segmentID = mfqsegment.segmentID
LEFT JOIN srp_erp_company company ON company.company_id = job.companyID  WHERE job.companyID = $companyid AND job.jobAutoID = $system_code GROUP BY overhead.glAutoID")->result_array();

        $output = $this->db->query("SELECT 'STJOB' AS documentCode,items.jobItemID as auto_id,job.jobAutoID AS documentMasterAutoID,job.documentSystemCode AS documentSystemCode,job.documentDate AS documentDate,YEAR ( job.documentDate ) AS documentYear,MONTH ( job.documentDate ) AS documentMonth,job.narration AS documentNarration,items.GLAutoID AS glautoID,account.systemAccountCode AS systemGLCode,account.GLSecondaryCode AS GLCode,account.GLDescription AS GLDescription,account.subcategory AS GLType,
'dr' AS amount_type,items.transactionCurrencyID AS transactionCurrencyID,items.transactionCurrency AS transactionCurrency,items.transactionExchangeRate AS transactionExchangeRate,sum( items.transactionAmount ) AS transactionAmount,items.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,items.companyLocalCurrencyID AS companyLocalCurrencyID,items.companyLocalCurrency AS companyLocalCurrency,items.companyLocalExchangeRate AS companyLocalExchangeRate,sum( items.companyLocalAmount ) AS companyLocalAmount,items.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces,items.companyreportingcurrencyID AS companyReportingCurrencyID,items.companyreportingcurrency AS companyReportingCurrency,items.companyreportingExchangeRate AS companyReportingExchangeRate,
sum( items.companyReportingAmount ) AS companyReportingAmount,items.companyReportingCurrencyDecimalPlaces AS companyReportingCurrencyDecimalPlaces,job.confirmedByEmpID,job.confirmedByName,job.confirmedDate,job.approvedDate,job.approvedbyEmpID,job.approvedbyEmpName,segment.segmentID,segment.segmentCode,job.companyID,company_code AS companyCode,job.createdUserGroup,job.createdPCID,job.createdUserID,job.createdDateTime,job.createdUserName
FROM srp_erp_mfq_standardjob_items items LEFT JOIN srp_erp_mfq_standardjob job ON items.jobAutoID = job.jobAutoID LEFT JOIN srp_erp_chartofaccounts account ON account.glautoID = items.glautoID LEFT JOIN srp_erp_currencymaster currency ON currency.currencyID = transactionCurrency LEFT JOIN srp_erp_mfq_segment mgqsegment ON mgqsegment.mfqSegmentID = job.segmentID LEFT JOIN srp_erp_mfq_segment segment ON segment.segmentID = mgqsegment.segmentID LEFT JOIN srp_erp_company company ON company.company_id = job.companyID
WHERE job.companyID = $companyid AND job.jobAutoID = $system_code  AND items.type = 2 GROUP BY items.glAutoID")->result_array();



            for ($i = 0; $i < count($labour); $i++) {
                $assat_entry_arr['auto_id'] = $labour[$i]['auto_id'];
                $assat_entry_arr['gl_auto_id'] = $labour[$i]['glautoID'];
                $assat_entry_arr['gl_code'] = $labour[$i]['systemGLCode'];
                $assat_entry_arr['secondary'] = $labour[$i]['GLCode'];
                $assat_entry_arr['gl_desc'] = $labour[$i]['GLDescription'];
                $assat_entry_arr['gl_type'] = $labour[$i]['GLType'];
                $assat_entry_arr['segment_id'] = $labour[$i]['segmentID'];
                $assat_entry_arr['segment'] = $labour[$i]['segmentCode'];
                $assat_entry_arr['projectID'] = null;
                $assat_entry_arr['projectExchangeRate'] = null;
                $assat_entry_arr['isAddon'] = 0;
                $assat_entry_arr['taxMasterAutoID'] = null;
                $assat_entry_arr['partyVatIdNo'] = null;
                $assat_entry_arr['subLedgerType'] = 0;
                $assat_entry_arr['subLedgerDesc'] = null;
                $assat_entry_arr['partyContractID'] = null;
                $assat_entry_arr['partyType'] = null;
                $assat_entry_arr['partyAutoID'] = null;
                $assat_entry_arr['partySystemCode'] = null;
                $assat_entry_arr['partyName'] = null;
                $assat_entry_arr['partyCurrencyID'] = null;
                $assat_entry_arr['partyCurrency'] = null;
                $assat_entry_arr['transactionExchangeRate'] = 1;
                $assat_entry_arr['companyLocalExchangeRate'] = null;
                $assat_entry_arr['companyReportingExchangeRate'] = null;
                $assat_entry_arr['partyExchangeRate'] = null;
                $assat_entry_arr['partyCurrencyAmount'] = null;
                $assat_entry_arr['partyCurrencyDecimalPlaces'] = 2;
                $assat_entry_arr['amount_type'] = 'cr';
                if ($labour[$i]['transactionAmount'] >= 0) {
                    $assat_entry_arr['gl_dr'] = 0;
                    $assat_entry_arr['gl_cr'] = $labour[$i]['transactionAmount'];
                    array_push($e_p_arr, $assat_entry_arr);
                } else {
                    $assat_entry_arr['gl_dr'] = $labour[$i]['transactionAmount'];
                    $assat_entry_arr['gl_cr'] = 0;
                    $assat_entry_arr['amount_type'] = 'cr';
                    array_push($e_m_arr, $assat_entry_arr);
                }

            }


            for ($i = 0; $i < count($rawmaterial); $i++) {
                $assat_entry_arr_raw_material['auto_id'] = $rawmaterial[$i]['auto_id'];
                $assat_entry_arr_raw_material['gl_auto_id'] = $rawmaterial[$i]['glautoID'];
                $assat_entry_arr_raw_material['gl_code'] = $rawmaterial[$i]['systemGLCode'];
                $assat_entry_arr_raw_material['secondary'] = $rawmaterial[$i]['GLCode'];
                $assat_entry_arr_raw_material['gl_desc'] = $rawmaterial[$i]['GLDescription'];
                $assat_entry_arr_raw_material['gl_type'] = $rawmaterial[$i]['GLType'];
                $assat_entry_arr_raw_material['segment_id'] = $rawmaterial[$i]['segmentID'];
                $assat_entry_arr_raw_material['segment'] = $rawmaterial[$i]['segmentCode'];
                $assat_entry_arr_raw_material['projectID'] = null;
                $assat_entry_arr_raw_material['projectExchangeRate'] = null;
                $assat_entry_arr_raw_material['isAddon'] = 0;
                $assat_entry_arr_raw_material['taxMasterAutoID'] = null;
                $assat_entry_arr_raw_material['partyVatIdNo'] = null;
                $assat_entry_arr_raw_material['subLedgerType'] = 0;
                $assat_entry_arr_raw_material['subLedgerDesc'] = null;
                $assat_entry_arr_raw_material['partyContractID'] = null;
                $assat_entry_arr_raw_material['partyType'] = null;
                $assat_entry_arr_raw_material['partyAutoID'] = null;
                $assat_entry_arr_raw_material['partySystemCode'] = null;
                $assat_entry_arr_raw_material['partyName'] = null;
                $assat_entry_arr_raw_material['partyCurrencyID'] = null;
                $assat_entry_arr_raw_material['partyCurrency'] = null;
                $assat_entry_arr_raw_material['transactionExchangeRate'] = 1;
                $assat_entry_arr_raw_material['companyLocalExchangeRate'] = null;
                $assat_entry_arr_raw_material['companyReportingExchangeRate'] = null;
                $assat_entry_arr_raw_material['partyExchangeRate'] = null;
                $assat_entry_arr_raw_material['partyCurrencyAmount'] = null;
                $assat_entry_arr_raw_material['partyCurrencyDecimalPlaces'] = 2;
                $assat_entry_arr_raw_material['amount_type'] = 'cr';
                if ($rawmaterial[$i]['transactionAmount'] >= 0) {
                    $assat_entry_arr_raw_material['gl_dr'] = 0;
                    $assat_entry_arr_raw_material['gl_cr'] = $rawmaterial[$i]['transactionAmount'];
                    array_push($e_p_arr, $assat_entry_arr_raw_material);
                } else {
                    $assat_entry_arr_raw_material['gl_dr'] = $rawmaterial[$i]['transactionAmount'];
                    $assat_entry_arr_raw_material['gl_cr'] = 0;
                    $assat_entry_arr_raw_material['amount_type'] = 'cr';
                    array_push($e_m_arr, $assat_entry_arr_raw_material);
                }

            }


           for ($i = 0; $i < count($overhead); $i++) {
                $assat_entry_arr_overhead['auto_id'] = $overhead[$i]['auto_id'];
                $assat_entry_arr_overhead['gl_auto_id'] = $overhead[$i]['glautoID'];
                $assat_entry_arr_overhead['gl_code'] = $overhead[$i]['systemGLCode'];
                $assat_entry_arr_overhead['secondary'] = $overhead[$i]['GLCode'];
                $assat_entry_arr_overhead['gl_desc'] = $overhead[$i]['GLDescription'];
                $assat_entry_arr_overhead['gl_type'] = $overhead[$i]['GLType'];
                $assat_entry_arr_overhead['segment_id'] = $overhead[$i]['segmentID'];
                $assat_entry_arr_overhead['segment'] = $overhead[$i]['segmentCode'];
                $assat_entry_arr_overhead['projectID'] = null;
                $assat_entry_arr_overhead['projectExchangeRate'] = null;
                $assat_entry_arr_overhead['isAddon'] = 0;
                $assat_entry_arr_overhead['taxMasterAutoID'] = null;
                $assat_entry_arr_overhead['partyVatIdNo'] = null;
                $assat_entry_arr_overhead['subLedgerType'] = 0;
                $assat_entry_arr_overhead['subLedgerDesc'] = null;
                $assat_entry_arr_overhead['partyContractID'] = null;
                $assat_entry_arr_overhead['partyType'] = null;
                $assat_entry_arr_overhead['partyAutoID'] = null;
                $assat_entry_arr_overhead['partySystemCode'] = null;
                $assat_entry_arr_overhead['partyName'] = null;
                $assat_entry_arr_overhead['partyCurrencyID'] = null;
                $assat_entry_arr_overhead['partyCurrency'] = null;
                $assat_entry_arr_overhead['transactionExchangeRate'] = 1;
                $assat_entry_arr_overhead['companyLocalExchangeRate'] = null;
                $assat_entry_arr_overhead['companyReportingExchangeRate'] = null;
                $assat_entry_arr_overhead['partyExchangeRate'] = null;
                $assat_entry_arr_overhead['partyCurrencyAmount'] = null;
                $assat_entry_arr_overhead['partyCurrencyDecimalPlaces'] = 2;
                $assat_entry_arr_overhead['amount_type'] = 'cr';
                if ($labour[$i]['transactionAmount'] >= 0) {
                    $assat_entry_arr_overhead['gl_dr'] = 0;
                    $assat_entry_arr_overhead['gl_cr'] = $overhead[$i]['transactionAmount'];
                    array_push($e_p_arr, $assat_entry_arr_overhead);
                } else {
                    $assat_entry_arr_overhead['gl_dr'] = $overhead[$i]['transactionAmount'];
                    $assat_entry_arr_overhead['gl_cr'] = 0;
                    $assat_entry_arr_overhead['amount_type'] = 'cr';
                    array_push($e_m_arr, $assat_entry_arr_overhead);
                }

            }


            for ($i = 0; $i < count($output); $i++) {
                $assat_entry_arr_output['auto_id'] = $output[$i]['auto_id'];
                $assat_entry_arr_output['gl_auto_id'] = $output[$i]['glautoID'];
                $assat_entry_arr_output['gl_code'] = $output[$i]['systemGLCode'];
                $assat_entry_arr_output['secondary'] = $output[$i]['GLCode'];
                $assat_entry_arr_output['gl_desc'] = $output[$i]['GLDescription'];
                $assat_entry_arr_output['gl_type'] = $output[$i]['GLType'];
                $assat_entry_arr_output['segment_id'] = $output[$i]['segmentID'];
                $assat_entry_arr_output['segment'] = $output[$i]['segmentCode'];
                $assat_entry_arr_output['projectID'] = null;
                $assat_entry_arr_output['projectExchangeRate'] = null;
                $assat_entry_arr_output['isAddon'] = 0;
                $assat_entry_arr_output['taxMasterAutoID'] = null;
                $assat_entry_arr_output['partyVatIdNo'] = null;
                $assat_entry_arr_output['subLedgerType'] = 0;
                $assat_entry_arr_output['subLedgerDesc'] = null;
                $assat_entry_arr_output['partyContractID'] = null;
                $assat_entry_arr_output['partyType'] = null;
                $assat_entry_arr_output['partyAutoID'] = null;
                $assat_entry_arr_output['partySystemCode'] = null;
                $assat_entry_arr_output['partyName'] = null;
                $assat_entry_arr_output['partyCurrencyID'] = null;
                $assat_entry_arr_output['partyCurrency'] = null;
                $assat_entry_arr_output['transactionExchangeRate'] = 1;
                $assat_entry_arr_output['companyLocalExchangeRate'] = null;
                $assat_entry_arr_output['companyReportingExchangeRate'] = null;
                $assat_entry_arr_output['partyExchangeRate'] = null;
                $assat_entry_arr_output['partyCurrencyAmount'] = null;
                $assat_entry_arr_output['partyCurrencyDecimalPlaces'] = 2;
                $assat_entry_arr_output['amount_type'] = 'dr';
                if ($output[$i]['transactionAmount'] >= 0) {
                    $assat_entry_arr_output['gl_dr'] = $output[$i]['transactionAmount'];
                    $assat_entry_arr_output['gl_cr'] = 0;
                    array_push($e_p_arr, $assat_entry_arr_output);
                } else {
                    $assat_entry_arr_output['gl_dr'] = 0;
                    $assat_entry_arr_output['gl_cr'] = $output[$i]['transactionAmount'];
                    $assat_entry_arr_output['amount_type'] = 'dr';
                    array_push($e_m_arr, $assat_entry_arr_output);
                }

            }

        $gl_array['gl_detail'] = $p_arr;

        foreach ($m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        $gl_array['currency'] = $master['CurrencyCode'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'STJOB';
        $gl_array['name'] = 'Standard Job Card';
        $gl_array['approved_YN'] = $master['approvedYN'];
        $gl_array['primary_Code'] = $master['documentSystemCode'];
        $gl_array['date'] = $master['documentDate'];
        $gl_array['finance_year'] = '-';
        $gl_array['finance_period'] = '-';
        $gl_array['master_data'] = $master;
        return $gl_array;
    }

    function save_sd_machine()
    {
        $this->db->trans_start();
        $mfq_faID = $this->input->post('mfq_faID');
        $jobAutoID = $this->input->post('jobcardid');
        $assetDescription = $this->input->post('assetDescription');
        /*  echo '<pre>';print_r($assetDescription); echo '</pre>'; die();*/


        $hoursSpent = $this->input->post('hoursSpent');
        $hoursSpentminutes = $this->input->post('hoursSpentminutes');
        $companyid = current_companyID();
        $this->db->delete('srp_erp_mfq_standardjob_machine', array('jobAutoID' => $jobAutoID));
        foreach ($mfq_faID as $key => $val) {
            if (!empty($mfq_faID[$key]) || $mfq_faID[$key] != '') {
                $startTime = trim($this->input->post('startTime')[$key]);
                $endTime = trim($this->input->post('endTime')[$key]);
                $format_startdate = null;
                if (isset($startTime) && !empty($startTime)) {
                    $dteStart = new DateTime($startTime);
                    $format_startdate = $dteStart->format('Y-m-d H:i:s');
                }
                $format_enddate = null;
                if (isset($endTime) && !empty($endTime)) {
                    $dueStart = new DateTime($endTime);
                    $format_enddate = $dueStart->format('Y-m-d H:i:s');
                }
                $data[$key]['jobAutoID'] = $jobAutoID;
                $data[$key]['mfq_faID'] = $val;
                $data[$key]['Description'] = $assetDescription[$key];
                $data[$key]['startDateTime'] = $format_startdate;
                $data[$key]['endDateTime'] = $format_enddate;
                $data[$key]['hoursSpent'] = $hoursSpentminutes[$key];
                $data[$key]['companyID'] = $companyid;
                $data[$key]['createdPCID'] = $this->common_data['current_pc'];
                $data[$key]['createdUserID'] = $this->common_data['current_userID'];
                $data[$key]['createdDateTime'] = $this->common_data['current_date'];
                $data[$key]['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_mfq_standardjob_machine', $data[$key]);

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Standard Job Machine  Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Standard Job Machine  Detail :  Saved Successfully.');
        }
    }


    function fetch_machine()
    {
        $dataArr = array();
        $dataArr2 = array();
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT mfq_faID,srp_erp_mfq_fa_asset_master.unitRate,faCode,assetDescription,partNumber,c1.description as faCat,c2.description as faSubCat,unit.UnitDes,c3.description as faSubSubCat,srp_erp_mfq_fa_asset_master.unitOfmeasureID,CONCAT(IFNULL(assetDescription,""), " (" ,IFNULL(faCode,""),")") AS "Match" FROM srp_erp_mfq_fa_asset_master LEFT JOIN srp_erp_mfq_category c1 ON mfq_faCatID = c1.itemCategoryID LEFT JOIN srp_erp_mfq_category c2 ON mfq_faSubCatID = c2.itemCategoryID LEFt join srp_erp_unit_of_measure unit on unit.UnitID = srp_erp_mfq_fa_asset_master.unitOfmeasureID LEFT JOIN srp_erp_mfq_category c3 ON mfq_faSubSubCatID = c3.itemCategoryID WHERE (faCode LIKE "' . $search_string . '" OR assetDescription LIKE "' . $search_string . '")')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['faCode'], 'mfq_faID' => $val['mfq_faID'], 'assetDescription' => $val['assetDescription'], 'partNumber' => $val['partNumber'], 'faCat' => $val['faCat'], 'faSubCat' => $val['faSubCat'], 'faSubSubCat' => $val['faSubSubCat'], 'UnitDes' => $val['UnitDes'], 'unitOfmeasureID' => $val['unitOfmeasureID'], 'unitRate' => $val['unitRate']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;


    }

    function load_mfq_standard_job_machine()
    {
        $convertFormat = convert_date_format_sql();
        $StandardJobCardID = $this->input->post('StandardJobcard');
        $companyid = current_companyID();
        $data = $this->db->query("SELECT
srp_erp_mfq_standardjob_machine.*,
CONCAT( IFNULL( assetDescription, \"\" ), \" (\", IFNULL( faCode, \"\" ), \")\" ) AS \"Match\"
,DATE_FORMAT(startDateTime,'" . $convertFormat . " %h:%i %p') AS startTime,DATE_FORMAT(endDateTime,'" . $convertFormat . " %h:%i %p') AS endTime
FROM
`srp_erp_mfq_standardjob_machine`
LEFT JOIN srp_erp_mfq_fa_asset_master on srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_standardjob_machine.mfq_faID
WHERE
srp_erp_mfq_standardjob_machine.companyID = $companyid
and jobAutoID = $StandardJobCardID")->result_array();
        return $data;
    }

    function save_mfq_crew()
    {
        $this->db->trans_start();

        $crewID = $this->input->post('crewID');
        $jobAutoID = $this->input->post('jobcardid');
        $hoursSpent = $this->input->post('hoursSpent');
        $hoursSpentminutes = $this->input->post('hoursSpentminutes');
        $designation = $this->input->post('designation');
        $eidno = $this->input->post('EIdNo');
        $companyid = current_companyID();
        $this->db->delete('srp_erp_mfq_standardjob_crew', array('jobAutoID' => $jobAutoID));
        foreach ($crewID as $key => $val) {
            if (!empty($crewID[$key]) || $crewID[$key] != '') {
                $startTime = trim($this->input->post('startTime')[$key]);
                $endTime = trim($this->input->post('endTime')[$key]);
                $format_startdate = null;
                if (isset($startTime) && !empty($startTime)) {
                    $dteStart = new DateTime($startTime);
                    $format_startdate = $dteStart->format('Y-m-d H:i:s');
                }
                $format_enddate = null;
                if (isset($endTime) && !empty($endTime)) {
                    $dueStart = new DateTime($endTime);
                    $format_enddate = $dueStart->format('Y-m-d H:i:s');
                }
                $data[$key]['jobAutoID'] = $jobAutoID;
                $data[$key]['crewID'] = $val;
                $data[$key]['empID'] = $eidno[$key];
                $data[$key]['Description'] = $designation[$key];
                $data[$key]['startDateTime'] = $format_startdate;
                $data[$key]['endDateTime'] = $format_enddate;
                $data[$key]['hoursSpent'] = $hoursSpentminutes[$key];
                $data[$key]['companyID'] = $companyid;
                $data[$key]['createdPCID'] = $this->common_data['current_pc'];
                $data[$key]['createdUserID'] = $this->common_data['current_userID'];
                $data[$key]['createdDateTime'] = $this->common_data['current_date'];
                $data[$key]['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_mfq_standardjob_crew', $data[$key]);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Standard Job Crew  Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Standard Job Crew  Detail :  Saved Successfully.');
        }
    }

    function fetch_crew_details()
    {
        $convertFormat = convert_date_format_sql();
        $StandardJobCardID = $this->input->post('StandardJobcard');
        $companyid = current_companyID();

        $data = $this->db->query("SELECT
	srp_erp_mfq_standardjob_crew.* ,
	srp_erp_mfq_crews.Ename1,
        DATE_FORMAT(startDateTime,'" . $convertFormat . " %h:%i %p') AS startTime,DATE_FORMAT(endDateTime,'" . $convertFormat . " %h:%i %p') AS endTime
FROM
	`srp_erp_mfq_standardjob_crew`
	LEFT JOIN srp_erp_mfq_crews on srp_erp_mfq_crews.crewID = srp_erp_mfq_standardjob_crew.crewID
	where
	companyID = $companyid
    AND jobAutoID = $StandardJobCardID")->result_array();
        return $data;
    }

    function delete_rawmaterial()
    {
        $jobAutoID = $this->input->post('jobAutoID');
        $jobItemID = $this->input->post('jobItemID');

        $result = $this->db->delete('srp_erp_mfq_standardjob_items', array('jobItemID' => $jobItemID, 'jobAutoID' => $jobAutoID, 'type' => 1));
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!');
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }

    }

    function delete_labourtask()
    {
        $jobLabourTaskID = $this->input->post('jobLabourTaskID');
        $jobAutoID = $this->input->post('jobAutoID');

        $result = $this->db->delete('srp_erp_mfq_standardjob_labourtask', array('jobLabourTaskID' => $jobLabourTaskID, 'jobAutoID' => $jobAutoID));
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!');
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function delete_OverHead()
    {
        $jobOverHeadID = $this->input->post('jobOverHeadID');
        $jobAutoID = $this->input->post('jobAutoID');
        $result = $this->db->delete('srp_erp_mfq_standardjob_overhead', array('jobOverHeadID' => $jobOverHeadID, 'jobAutoID' => $jobAutoID));
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!');
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }

    }

    function delete_finishgoods()
    {
        $jobAutoID = $this->input->post('jobAutoID');
        $jobItemID = $this->input->post('jobItemID');

        $result = $this->db->delete('srp_erp_mfq_standardjob_items', array('jobItemID' => $jobItemID, 'jobAutoID' => $jobAutoID, 'type' => 2));
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!');
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function delete_crew()
    {
        $jobAutoID = $this->input->post('jobAutoID');
        $jobCrewID = $this->input->post('jobCrewID');

        $result = $this->db->delete('srp_erp_mfq_standardjob_crew', array('jobCrewID' => $jobCrewID, 'jobAutoID' => $jobAutoID));
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!');
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function delete_machine()
    {
        $jobAutoID = $this->input->post('jobAutoID');
        $jobMachineID = $this->input->post('jobMachineID');

        $result = $this->db->delete('srp_erp_mfq_standardjob_machine', array('jobMachineID' => $jobMachineID, 'jobAutoID' => $jobAutoID));
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!');
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function save_standardjobcard_progress_value()
    {
        $jobautoid = $this->input->post('jobAutoID');
        $progressvalue = $this->input->post('progressvalue');

        $data['completionPercenatage'] = $progressvalue;
        $this->db->where('jobAutoID', $jobautoid);
        $this->db->update('srp_erp_mfq_standardjob', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $this->db->trans_commit();
            return array('s', 'Standard Job Card Progress Updated Successfully');
        }
    }
    function update_sj_header_details()
    {
        $value = $this->input->post('value'); // document date column in standard job card master tbl
        $masterid = $this->input->post('pk'); // Primary key of standard job card master tbl
        $companyID = current_companyID();

        $data = [
            'documentDate' => $value,
        ];
        $this->db->where(['companyID'=> $companyID, 'jobAutoID'=>$masterid]);
        $this->db->update('srp_erp_mfq_standardjob',$data);
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
         $this->db->trans_commit();
         return array('s', 'Updated successfully.');
        } else {
          $this->db->trans_rollback();
            return array('e', 'Error In update process');
            }


    }
    function update_sj_header_details_batchno()
    {
        $value = $this->input->post('value'); // batchno in standard job card master tbl
        $masterid = $this->input->post('pk'); // Primary key of standard job card master tbl
        $companyID = current_companyID();

        $data = [
            'batchNumber' => $value,
        ];
        $this->db->where(['companyID'=> $companyID, 'jobAutoID'=>$masterid]);
        $this->db->update('srp_erp_mfq_standardjob',$data);
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Updated successfully.');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error In update process');
        }
    }
    function update_sj_header_details_narration()
    {
        $value = $this->input->post('value'); // narration in standard job card master tbl
        $masterid = $this->input->post('pk'); // Primary key of standard job card master tbl
        $companyID = current_companyID();

        $data = [
            'narration' => $value,
        ];
        $this->db->where(['companyID'=> $companyID, 'jobAutoID'=>$masterid]);
        $this->db->update('srp_erp_mfq_standardjob',$data);
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Updated successfully.');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error In update process');
        }
    }
}
