<?php

class MFQ_BillOfMaterial_model extends ERP_Model
{
    function fetch_related_uom_id()
    {
        $this->db->select('defaultUnitOfMeasureID,defaultUnitOfMeasure,itemDescription,companyLocalWacAmount,companyLocalCurrencyID');
        $this->db->from('srp_erp_mfq_itemmaster');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        return $this->db->get()->row_array();
    }

    function fetch_related_uom_fn(){

        $this->db->select('srp_erp_unit_of_measure.UnitID,UnitShortCode,UnitDes,conversion');
         $this->db->from('srp_erp_unitsconversion');
         $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_unitsconversion.subUnitID');
         $this->db->where('masterUnitID',$this->input->post('masterUnitID'));
         $this->db->where('srp_erp_unitsconversion.companyID',$this->common_data['company_data']['company_id']);
         return $this->db->get()->result_array();

    }

    function load_unitprice_exchangerate()
    {
        $localwacAmount = trim($this->input->post('LocalWacAmount') ?? '');
        $localCurrency = trim($this->input->post('companyLocalCurrencyID') ?? '');
        $transactionCurrency = trim($this->input->post('transactionCurrency') ?? '');
        $conversion = currency_conversionID($localCurrency, $transactionCurrency);
        $unitprice = round(($localwacAmount / $conversion['conversion']));
        return array('status' => true, 'amount' => $unitprice);
    }

    function insert_BoM()
    {
        $post = $this->input->post();
        unset($post['bomMasterID']);
        $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_billofmaterial', 'bomMasterID', 'companyID');
        //print_r($serialInfo);
        $codes = $this->sequence->sequence_generator('BOM', $serialInfo['serialNo'] + 1);
        //var_dump($codes);

        //exit;
        $datetime = format_date_mysql_datetime();
        $post['documentDate'] = format_date_mysql_datetime($post['documentDate']);
        $post['serialNo'] = $serialInfo['serialNo'];
        $post['documentCode'] = $codes;
        $post['companyID'] = current_companyID();
        $post['createdPCID'] = current_pc();
        $post['createdUserID'] = current_userID();
        $post['createdDateTime'] = $datetime;
        $post['createdUserName'] = current_user();
        $post['timestamp'] = $datetime;


        $result = $this->db->insert('srp_erp_mfq_billofmaterial', $post);
        $masterID = $this->db->insert_id();
        if ($result) {
            return array('error' => 0, 'message' => 'Record successfully Added', 'code' => 1, 'masterID' => $masterID);
        } else {
            return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
        }

    }


    function update_BoM()
    {
        $post = $this->input->post();
        $masterID = $this->input->post('bomMasterID');
        unset($post['bomMasterID']);

        $datetime = format_date_mysql_datetime();
        $post['description'] = $this->input->post('description');
        $post['industryTypeID'] = $this->input->post('industryTypeID');
        $post['documentDate'] = $this->input->post('documentDate');

        $post['modifiedUserID'] = current_userID();
        $post['modifiedUserName'] = current_user();
        $post['modifiedDateTime'] = $datetime;
        $post['modifiedPCID'] = current_pc();


        $this->db->where('bomMasterID', $this->input->post('bomMasterID'));
        $result = $this->db->update('srp_erp_mfq_billofmaterial', $post);
        if ($result) {
            return array('error' => 0, 'message' => 'document successfully updated', 'code' => 2, 'masterID' => $masterID);
        } else {
            return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
        }
    }

    function get_srp_erp_mfq_billofmaterial($bomMasterID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('srp_erp_mfq_billofmaterial.*, DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,UnitDes,
        IF((mfqItemID = 0 || mfqItemID is NULL ) ,2,1) as bomType');
        $this->db->from('srp_erp_mfq_billofmaterial');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_mfq_billofmaterial.uomID', 'left');
        $this->db->where('bomMasterID', $bomMasterID);
        $result = $this->db->get()->row_array();
        /*if (!$result) {
            $result['documentDate'] = date('d-m-Y',strtotime($result['documentDate']));
        }*/

        return $result;
    }

    function load_mfq_billOfMaterial_detail($bomMasterID)
    {
        $result=array();
        $result["material"] = $this->load_bom_material_consumption($bomMasterID);
        $result["packaging"] = $this->load_bom_material_consumption($bomMasterID,1);
        $result["labour"] = $this->fetch_bom_labour_task();
        $result["overhead"] = $this->fetch_bom_overhead_cost();
        $result["third_party_service"] = $this->fetch_bom_third_party_service_cost();
        $result["machine"] = $this->fetch_bom_machine_cost();
        return $result;
    }


    function add_edit_BillOfMaterial()
    {
        try {
            $bomMasterID = $this->input->post('bomMasterID');
            $masterID = "";
            if (!$bomMasterID) {
                /** Insert */
                $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_billofmaterial', 'bomMasterID', 'companyID');
                $codes = $this->sequence->sequence_generator('BOM', $serialInfo['serialNo']);
                $datetime = format_date_mysql_datetime();

                $data['mfqProcessID'] = $this->input->post('process');
                $data['mfqItemID'] = $this->input->post('product');
                $data['industryTypeID'] = $this->input->post('industryTypeID');
                $data['uomID'] = $this->input->post('uomID');
                $data['Qty'] = $this->input->post('Qty');
                $data['description'] = $this->input->post('bomDescription');
                $data['documentDate'] = format_date_mysql_datetime($this->input->post('documentDate'));
                $data['serialNo'] = $serialInfo['serialNo'];
                $data['documentCode'] = $codes;
                $data['productImage'] = $this->input->post('productImage');
                $data['companyID'] = current_companyID();
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdDateTime'] = $datetime;
                $data['createdUserName'] = current_user();
                $data['timestamp'] = $datetime;

                if ($this->input->post('status') == 2) {
                    $data['confirmedYN'] = 1;
                    $data['confirmedUserID'] = current_userID();
                }
                $result = $this->db->insert('srp_erp_mfq_billofmaterial', $data);
                $masterID = $this->db->insert_id();

            } else {
                /** Update */
                $masterID = $this->input->post('bomMasterID');
                $datetime = format_date_mysql_datetime();
                $data['mfqProcessID'] = $this->input->post('process');
                $data['mfqItemID'] = trim($this->input->post('product') ?? '');
                $data['Qty'] = trim($this->input->post('Qty') ?? '');
                $data['uomID'] = $this->input->post('uomID');
                $data['industryTypeID'] = $this->input->post('industryTypeID');
                $data['description'] = $this->input->post('bomDescription');
                $data['documentDate'] = format_date_mysql_datetime($this->input->post('documentDate'));
                $data['productImage'] = $this->input->post('productImage');
                $data['modifiedUserID'] = current_userID();
                $data['modifiedUserName'] = current_user();
                $data['modifiedDateTime'] = $datetime;
                $data['modifiedPCID'] = current_pc();

                if ($this->input->post('status') == 2) {
                    $data['confirmedYN'] = 1;
                    $data['confirmedUserID'] = current_userID();
                }

                $this->db->where('bomMasterID', $this->input->post('bomMasterID'));
                $result = $this->db->update('srp_erp_mfq_billofmaterial', $data);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('error' => 1, 'message' => 'Error while updating');

            } else {

                $grandTotal = 0;
                /** Material Consumption */
                $bomMaterialConsumptionID = $this->input->post('bomMaterialConsumptionID');
                $mfqItemID = $this->input->post('mfqItemID');
                $search_mc = $this->input->post('search_mc');
                $Qty = (int)$this->input->post('Qty');


                if (!empty($search_mc)) {
                    foreach ($search_mc as $key => $val) {
                        if($search_mc[$key] != '')
                        {
                            if(empty($mfqItemID[$key])) {
                                $codes = generateMFQ_SystemCode('srp_erp_mfq_itemmaster', 'mfqItemID', 'companyID');
                                $datetime = format_date_mysql_datetime();
                                $post['serialNo'] = $codes['serialNo'];
                                $post['itemSystemCode'] = $codes['systemCode'];
                                $post['itemDescription'] = $this->input->post('search_mc')[$key] ?? null;
                                $post['itemType'] = 3;
                                $post['mainCategoryID'] = '';
                                $post['subcategoryID'] = '';
                                $post['subSubCategoryID'] = '';
                                $post['financeCategory'] = null;
                                $post['companyID'] = current_companyID();
                                $post['companyCode'] = current_companyCode();
                                $post['createdUserID'] = current_userID();
                                $post['createdDateTime'] = $datetime;
                                $post['createdPCID'] = current_pc();
                                $post['timestamp'] = $datetime;
                                $post['isFromERP'] = 0;
                                $post['isActive'] = 1;
                                $post['categoryType'] = 1;
                                $post['mfqSubCategoryID'] = '';
                                $post['defaultUnitOfMeasure'] = "";
                                $post['unbilledServicesGLAutoID'] = null;

                                $result = $this->db->insert('srp_erp_mfq_itemmaster', $post);
                                $mfqItemID[$key] = $this->db->insert_id();

                                if ($result) {
                                    $this->session->set_flashdata('w', 'Raw Material Created!');
                                }
                            }

                            //get manufacture item
                            $itemDetail = $this->db->where('mfqItemID',$mfqItemID[$key])->from('srp_erp_mfq_itemmaster')->get()->row_array();

                            if (!empty($bomMaterialConsumptionID[$key])) {
                                $materialCost = ((float)$this->input->post('qtyUsed')[$key]) * ((float)$this->input->post('unitCost')[$key]);
                                $materialCharge = $materialCost + (((float)$this->input->post('markUp')[$key] * $materialCost) / 100);
                                $grandTotal += $materialCharge;
                                $this->db->set('mfqItemID', $mfqItemID[$key]);
                                $this->db->set('qtyUsed', ((float)$this->input->post('qtyUsed')[$key]/$Qty));
                                $this->db->set('unitCost', ($this->input->post('unitCost')[$key]) ?? 0);
                                $this->db->set('costingType', $this->input->post('costingType')[$key] ?? null);
                                $this->db->set('materialCost', ($materialCost/$Qty));
                                $this->db->set('markUp', $this->input->post('markUp')[$key] ?? 0);
                                $this->db->set('materialCharge', ($materialCharge/$Qty));
                                $this->db->set('isPackageYN', isset($itemDetail['packagingYN']) ? $itemDetail['packagingYN'] : 0);
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
                                $this->db->where('bomMaterialConsumptionID', $bomMaterialConsumptionID[$key]);
                                $this->db->update('srp_erp_mfq_bom_materialconsumption');

                            } else {
                                if (!empty($mfqItemID[$key])) {
                                    $materialCost = ($this->input->post('qtyUsed')[$key] ?? 0) * ($this->input->post('unitCost')[$key] ?? 0);
                                    $materialCharge = $materialCost + (((float)$this->input->post('markUp')[$key] * $materialCost) / 100);
                                    $grandTotal += (float)$materialCharge;
                                    $this->db->set('bomMasterID', $masterID);
                                    $this->db->set('mfqItemID', $mfqItemID[$key]);
                                    $this->db->set('qtyUsed', ((float)$this->input->post('qtyUsed')[$key]/$Qty));
                                    $this->db->set('unitCost', $this->input->post('unitCost')[$key] ?? 0);
                                    $this->db->set('costingType', $this->input->post('costingType')[$key] ?? 0);
                                    $this->db->set('materialCost', ($materialCost/$Qty));
                                    $this->db->set('isPackageYN', isset($itemDetail['packagingYN']) ? $itemDetail['packagingYN'] : 0);
                                    $this->db->set('markUp', $this->input->post('markUp')[$key]);
                                    $this->db->set('materialCharge', ($materialCharge/$Qty));
                                    $this->db->set('modifiedPCID', current_pc());
                                    $this->db->set('modifiedUserID', current_userID());
                                    $this->db->set('modifiedDateTime', format_date_mysql_datetime());
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

                                    $this->db->insert('srp_erp_mfq_bom_materialconsumption');
                                }
                            }
                        }
                    }
                }


                /** Labour Task */
                $bomLabourTaskID = $this->input->post('bomLabourTaskID');
                $labourTask = $this->input->post('labourTask');
                if (!empty($labourTask)) {
                    foreach ($labourTask as $key => $val) {

                        if (!empty($bomLabourTaskID[$key])) {

                            $grandTotal += (float)$this->input->post('la_totalValue')[$key];
                            $this->db->set('bomMasterID', $masterID);

                            $this->db->set('labourTask', $this->input->post('labourTask')[$key] ?? null);
                            $this->db->set('activityCode', $this->input->post('la_activityCode')[$key] ?? null);
                            $this->db->set('uomID', $this->input->post('la_uomID')[$key] ?? null);
                            $this->db->set('segmentID', $this->input->post('la_segmentID')[$key] ?? null);
                            $this->db->set('subsegmentID', $this->input->post('la_subsegmentID')[$key] ?? null);
                            $this->db->set('hourlyRate', $this->input->post('la_hourlyRate')[$key] ?? 0);
                            $this->db->set('totalHours', ((float)$this->input->post('la_totalHours')[$key]/$Qty));
                            $this->db->set('totalValue', ((float)$this->input->post('la_totalValue')[$key]/$Qty));

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
                            $this->db->where('bomLabourTaskID', $bomLabourTaskID[$key]);
                            $result = $this->db->update('srp_erp_mfq_bom_labourtask');

                        } else {
                            if (!empty($labourTask[$key])) {
                                $grandTotal += (float)$this->input->post('la_totalValue')[$key];
                                $this->db->set('labourTask', $this->input->post('labourTask')[$key] ?? null);
                                $this->db->set('activityCode', $this->input->post('la_activityCode')[$key] ?? null);
                                $this->db->set('uomID', $this->input->post('la_uomID')[$key] ?? null);
                                $this->db->set('segmentID', $this->input->post('la_segmentID')[$key] ?? null);
                                $this->db->set('subsegmentID', $this->input->post('la_subsegmentID')[$key] ?? null);
                                $this->db->set('hourlyRate', $this->input->post('la_hourlyRate')[$key] ?? 0);
                                $this->db->set('totalHours', ((float)$this->input->post('la_totalHours')[$key]/$Qty));
                                $this->db->set('totalValue', ((float)$this->input->post('la_totalValue')[$key]/$Qty));
                                $this->db->set('bomMasterID', $masterID);
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
                                $this->db->insert('srp_erp_mfq_bom_labourtask');
                            }
                        }
                    }
                }

                /** Overhead Cost */
                $bomOverheadID = $this->input->post('bomOverheadID');
                $overheadID = $this->input->post('overheadID');

                if (!empty($overheadID)) {
                    foreach ($overheadID as $key => $val) {
                        if (!empty($bomOverheadID[$key])) {
                            $grandTotal += (float)$this->input->post('oh_totalValue')[$key];
                            $this->db->set('bomMasterID', $masterID);

                            $this->db->set('overheadID', $this->input->post('overheadID')[$key] ?? null);
                            $this->db->set('activityCode', $this->input->post('oh_activityCode')[$key] ?? null);
                            $this->db->set('uomID', $this->input->post('oh_uomID')[$key] ?? null);
                            $this->db->set('segmentID', $this->input->post('oh_segmentID')[$key] ?? null);
                            $this->db->set('hourlyRate', $this->input->post('oh_hourlyRate')[$key] ?? 0);
                            $this->db->set('totalHours', ((float)$this->input->post('oh_totalHours')[$key]/$Qty));
                            $this->db->set('totalValue', ((float)$this->input->post('oh_totalValue')[$key]/$Qty));

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
                            $this->db->where('bomOverheadID', $bomOverheadID[$key]);
                            $result = $this->db->update('srp_erp_mfq_bom_overhead');

                        } else {
                            if (!empty($overheadID[$key])) {
                                $grandTotal += (float)$this->input->post('oh_totalValue')[$key];
                                $this->db->set('overheadID', $this->input->post('overheadID')[$key] ?? null);
                                $this->db->set('activityCode', $this->input->post('oh_activityCode')[$key] ?? null);
                                $this->db->set('uomID', $this->input->post('oh_uomID')[$key] ?? null);
                                $this->db->set('segmentID', $this->input->post('oh_segmentID')[$key] ?? null);
                                $this->db->set('hourlyRate', $this->input->post('oh_hourlyRate')[$key] ?? 0);
                                $this->db->set('totalHours', ((float)$this->input->post('oh_totalHours')[$key]/$Qty));
                                $this->db->set('totalValue', ((float)$this->input->post('oh_totalValue')[$key]/$Qty));
                                $this->db->set('bomMasterID', $masterID);
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
                                $this->db->insert('srp_erp_mfq_bom_overhead');
                            }
                        }
                    }
                }

                /** Third Party Service Cost */
                $bomtpsID = $this->input->post('bomtpsID');
                $tpsID = $this->input->post('tpsID');
                $tps_search = $this->input->post('tps_search');

                if (!empty($tps_search)) {
                    foreach ($tps_search as $key => $val) {
                        if ($tps_search[$key] != '')
                        {
                            if(empty($tpsID[$key])) {
                                $ohid="TPS";
                                $companycode = current_companyCode();
                                $lastoverHeadID = $this->db->query("SELECT COUNT(overHeadID) as overHeadID FROM srp_erp_mfq_overhead WHERE overHeadCategoryID = 1 AND typeID = 2")->row_array();
                                if(!empty($lastoverHeadID)){
                                    $new_overHeadID = $lastoverHeadID['overHeadID'] + 1;
                                }else{
                                    $new_overHeadID = 1;
                                }
                                $overCode=str_pad($new_overHeadID, 6, '0', STR_PAD_LEFT);
                                $overHeadCode=$companycode.'/'.$ohid.$overCode;
                                $this->db->set('typeID', 2);
                                $this->db->set('description', $this->input->post('tps_search')[$key] ?? null);
                                $this->db->set('overHeadCode', $overHeadCode );
                                $this->db->set('unitOfMeasureID', $this->input->post('tps_uomID')[$key] ?? null);
                                $this->db->set('companyID', current_companyID());
                                $this->db->set('overHeadCategoryID', 1);
                                $result = $this->db->insert('srp_erp_mfq_overhead');
                                $tpsID[$key] = $this->db->insert_id();

                                if ($result) {
                                    $this->session->set_flashdata('w', 'Third Party Service Created!');
                                }
                            }

                            $manufacturing_Flow = getPolicyValues('MANFL', 'All');
                            if (!empty($bomtpsID[$key])) {
                                $grandTotal += (float)$this->input->post('tps_totalValue')[$key];
                                $this->db->set('bomMasterID', $masterID);

                                $this->db->set('overheadID', $tpsID[$key]);
                                if($manufacturing_Flow == 'Micoda'){
                                    $this->db->set('activityCode', $this->input->post('tps_activityCode')[$key] ?? null);
                                }
                                $this->db->set('uomID', $this->input->post('tps_uomID')[$key] ?? null);
                                $this->db->set('hourlyRate', $this->input->post('tps_hourlyRate')[$key] ?? 0);
                                $this->db->set('totalHours', ((float)$this->input->post('tps_totalHours')[$key]/$Qty));
                                $this->db->set('totalValue', ((float)$this->input->post('tps_totalValue')[$key]/$Qty));

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
                                $this->db->where('bomOverheadID', $bomtpsID[$key]);
                                $result = $this->db->update('srp_erp_mfq_bom_overhead');

                            } else {
                                if (!empty($tpsID[$key])) {
                                    $grandTotal += (float)$this->input->post('tps_totalValue')[$key];
                                    $this->db->set('overheadID', $tpsID[$key]);
                                    if($manufacturing_Flow == 'Micoda'){
                                        $this->db->set('activityCode', $this->input->post('tps_activityCode')[$key] ?? null);
                                    }
                                    $this->db->set('uomID', $this->input->post('tps_uomID')[$key] ?? null);
                                    $this->db->set('hourlyRate', $this->input->post('tps_hourlyRate')[$key] ?? 0);
                                    $this->db->set('totalHours', ((float)$this->input->post('tps_totalHours')[$key]/$Qty));
                                    $this->db->set('totalValue', ((float)$this->input->post('tps_totalValue')[$key]/$Qty));
                                    $this->db->set('bomMasterID', $masterID);
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
                                    $this->db->insert('srp_erp_mfq_bom_overhead');
                                }
                            }
                        }
                    }
                }


                /** Machine Cost */
                $bomMachineID = $this->input->post('bomMachineID');
                $mfq_faID = $this->input->post('mfq_faID');

                if (!empty($mfq_faID)) {
                    foreach ($mfq_faID as $key => $val) {
                        if (!empty($bomMachineID[$key])) {
                            $grandTotal += (float)$this->input->post('mc_totalValue')[$key];
                            $this->db->set('bomMasterID', $masterID);

                            $this->db->set('mfq_faID', $this->input->post('mfq_faID')[$key] ?? null);
                            $this->db->set('activityCode', $this->input->post('mc_activityCode')[$key] ?? null);
                            $this->db->set('uomID', $this->input->post('mc_uomID')[$key] ?? null);
                            $this->db->set('segmentID', $this->input->post('mc_segmentID')[$key] ?? null);
                            $this->db->set('hourlyRate', $this->input->post('mc_hourlyRate')[$key] ?? 0);
                            $this->db->set('totalHours', $this->input->post('mc_totalHours')[$key] ?? 0);
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
                            $this->db->where('bomMachineID', $bomMachineID[$key]);
                            $result = $this->db->update('srp_erp_mfq_bom_machine');

                        } else {
                            if (!empty($mfq_faID[$key])) {
                                $grandTotal += (float)$this->input->post('mc_totalValue')[$key];
                                $this->db->set('mfq_faID', $this->input->post('mfq_faID')[$key] ?? null);
                                $this->db->set('activityCode', $this->input->post('mc_activityCode')[$key] ?? null);
                                $this->db->set('uomID', $this->input->post('mc_uomID')[$key] ?? null);
                                $this->db->set('segmentID', $this->input->post('mc_segmentID')[$key] ?? null);
                                $this->db->set('hourlyRate', $this->input->post('mc_hourlyRate')[$key] ?? 0);
                                $this->db->set('totalHours', $this->input->post('mc_totalHours')[$key] ?? 0);
                                $this->db->set('totalValue', $this->input->post('mc_totalValue')[$key] ?? 0);
                                $this->db->set('bomMasterID', $masterID);
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
                                $this->db->insert('srp_erp_mfq_bom_machine');
                            }
                        }
                    }
                }

                $estimateMasterID = "";
                if (isset($_POST["estimateDetailID"])) {
                    $this->db->select('*');
                    $this->db->from('srp_erp_mfq_estimatedetail');
                    $this->db->join('srp_erp_mfq_estimatemaster','srp_erp_mfq_estimatedetail.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID','inner');
                    $this->db->where('estimateDetailID', $this->input->post('estimateDetailID'));
                    $result = $this->db->get()->row_array();

                    $estimateMasterID = $result["estimateMasterID"] ?? '';

                    $dataEst['estimatedCost'] = $grandTotal;
                    $dataEst['sellingPrice'] = ((($grandTotal * ($result["expectedQty"] ?? 0)) * ($result["margin"] ?? 0))/100) + ($grandTotal * ($result["expectedQty"] ?? 0));
                    $dataEst['discountedPrice'] = (($dataEst['sellingPrice'] ?? 0) - ((($dataEst['sellingPrice'] ?? 0) * ($result["margin"] ?? 0)) / 100));
                    $this->db->where('estimateDetailID', $this->input->post('estimateDetailID'));
                    $this->db->update('srp_erp_mfq_estimatedetail', $dataEst);

                    $this->db->select('SUM(sellingPrice) as sellingPrice,SUM(estimatedCost) as estimatedCost');
                    $this->db->from('srp_erp_mfq_estimatedetail');
                    $this->db->where('estimateMasterID', $estimateMasterID);
                    $sellingPrice = $this->db->get()->row_array();

                    $dataM['totalSellingPrice'] = ((($sellingPrice["sellingPrice"] ?? 0) * ($result["totMargin"] ?? 0)) / 100) + ($sellingPrice["sellingPrice"] ?? 0);
                    $dataM['totDiscountPrice'] = $dataM['totalSellingPrice'] - (($dataM['totalSellingPrice']  * ($result["totDiscount"] ?? 0))/100);
                    $dataM['totalCost'] = $sellingPrice["estimatedCost"];
                    $this->db->where('estimateMasterID', $estimateMasterID);
                    $this->db->update('srp_erp_mfq_estimatemaster', $dataM);
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('error' => 1, 'message' => 'Error while updating');

                } else {
                    $this->db->trans_commit();
                    if (isset($_POST["estimateDetailID"])) {
                        return array('error' => 0, 'message' => 'Document successfully updated', 'masterID' => $masterID,'estimateMasterID' => $estimateMasterID);
                    }else{
                        return array('error' => 0, 'message' => 'Document successfully updated', 'masterID' => $masterID);
                    }

                }
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return array('error' => 1, 'message' => 'Error while updating');
        }
    }

    function load_bom_material_consumption($bomMasterID,$packaging = null)
    {
        $companyID = current_companyID();

        $this->db->select('mc.bomMaterialConsumptionID, mc.bomMasterID, mc.mfqItemID,  mc.qtyUsed, mc.unitCost, mc.materialCost, mc.markUp, mc.materialCharge, CONCAT(CASE itemmaster.itemType WHEN 1 THEN "RM" WHEN 2 THEN "FG" WHEN 3 THEN "SF"
            END," - ",itemmaster.itemDescription," (",itemSystemCode,")") as itemName,itemmaster.defaultUnitOfMeasure,itemmaster.defaultUnitOfMeasureID, IFNULL(itemmaster.partNo, 0) AS partNo,mc.costingType');
        $this->db->from('srp_erp_mfq_bom_materialconsumption mc');
        $this->db->join('srp_erp_mfq_itemmaster itemmaster', 'itemmaster.mfqItemID = mc.mfqItemID', 'INNER');
        $this->db->where('bomMasterID', $bomMasterID);
        $this->db->where('mc.companyID', $companyID);

        if($packaging == 1){
            $this->db->where('mc.isPackageYN', 1);
        }else{
            $this->db->where('(mc.isPackageYN IS NULL OR  mc.isPackageYN != 1)', null);
        }

        $result = $this->db->get()->result_array();

        foreach($result as $key => $material){

            $uomID = $material['defaultUnitOfMeasureID'] ?? 0;

            $uomDetails = $this->db->query("
                SELECT subunit.subUnitID,umeasure.UnitShortCode,subunit.conversion
                FROM `srp_erp_unitsconversion` as subunit
                LEFT JOIN srp_erp_unit_of_measure as umeasure ON subunit.subUnitID = umeasure.UnitID
                WHERE subunit.masterUnitID = {$uomID} AND subunit.companyID = {$companyID}
            ")->result_array();

            $uom_arr = array();

            $result[$key]['uom_details'] = $uomDetails;

        }

        return $result;
    }

    function delete_materialConsumption($id)
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('bomMaterialConsumptionID');
        $this->db->from('srp_erp_mfq_bom_materialconsumption');
        $this->db->where('bomMasterID', $masterID);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $result = $this->db->delete('srp_erp_mfq_bom_materialconsumption', array('bomMaterialConsumptionID' => $id), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    /** Job Card */
    function fetch_labourTask()
    {
        $dataArr = array();
        $dataArr2 = array();
        $search_string = "%" . $_GET['query'] . "%";
        $companyID = current_companyID();
        $data = $this->db->query('SELECT srp_erp_mfq_overhead.*,CONCAT(IFNULL(description,""), " (" ,IFNULL(overHeadCode,""),")") AS "Match",srp_erp_mfq_segmenthours.hours FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_mfq_segmenthours ON srp_erp_mfq_overhead.mfqSegmentID = srp_erp_mfq_segmenthours.mfqSegmentID  WHERE overHeadCategoryID = 2 AND (overHeadCode LIKE "' . $search_string . '" OR description LIKE "' . $search_string . '") AND srp_erp_mfq_overhead.companyID="' . $companyID . '" ')->result_array();
        //echo $this->db->last_query();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['overHeadCode'], 'overHeadID' => $val['overHeadID'], 'description' => $val['description'],'segment' => $val['mfqSegmentID'],'rate' => $val['rate'],'hours' => $val['hours'],'uom' => $val['unitOfMeasureID']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_overhead()
    {
        $dataArr = array();
        $dataArr2 = array();
        $search_string = "%" . $_GET['query'] . "%";
        $companyID = current_companyID();
        $data = $this->db->query('SELECT srp_erp_mfq_overhead.*,CONCAT(IFNULL(description,""), " (" ,IFNULL(overHeadCode,""),")") AS "Match",srp_erp_mfq_segmenthours.hours FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_mfq_segmenthours ON srp_erp_mfq_overhead.mfqSegmentID = srp_erp_mfq_segmenthours.mfqSegmentID WHERE overHeadCategoryID = 1 AND  (overHeadCode LIKE "' . $search_string . '" OR description LIKE "' . $search_string . '") AND srp_erp_mfq_overhead.companyID="' . $companyID . '" AND srp_erp_mfq_overhead.typeID = 1 ')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['overHeadCode'], 'overHeadID' => $val['overHeadID'], 'description' => $val['description'],'segment' => $val['mfqSegmentID'],'rate' => $val['rate'],'hours' => $val['hours'],'uom' => $val['unitOfMeasureID']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_machine()
    {
        $dataArr = array();
        $dataArr2 = array();
        $search_string = "%" . $_GET['query'] . "%";
        $companyID = current_companyID();
        $data = $this->db->query('SELECT srp_erp_mfq_fa_asset_master.*,CONCAT(IFNULL(assetDescription,""), " (" ,IFNULL(faCode,""),")") AS "Match",srp_erp_mfq_segmenthours.hours,mfqSeg.mfqSegmentID FROM srp_erp_mfq_fa_asset_master LEFT JOIN srp_erp_mfq_category c1 ON mfq_faCatID = c1.itemCategoryID LEFT JOIN srp_erp_mfq_category c2 ON mfq_faSubCatID = c2.itemCategoryID LEFT JOIN srp_erp_mfq_category c3 ON mfq_faSubSubCatID = c3.itemCategoryID LEFT JOIN (SELECT segmentID,mfqSegmentID FROM srp_erp_mfq_segment WHERE companyID = '.current_companyID().') mfqSeg ON mfqSeg.segmentID = srp_erp_mfq_fa_asset_master.segmentID LEFT JOIN srp_erp_mfq_segmenthours ON mfqSeg.mfqSegmentID = srp_erp_mfq_segmenthours.mfqSegmentID WHERE (faCode LIKE "' . $search_string . '" OR assetDescription LIKE "' . $search_string . '") AND srp_erp_mfq_fa_asset_master.companyID='.current_companyID().' ')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['faCode'], 'mfq_faID' => $val['mfq_faID'], 'description' => $val['assetDescription'],'segment' => $val['mfqSegmentID'],'rate' => $val['unitRate'],'hours' => $val['hours'],'uom' => $val['unitOfmeasureID']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_bom_labour_task()
    {
        $companyID = current_companyID();
        $bomMasterID = trim($this->input->post('bomMasterID') ?? '');
        $sql = "SELECT * FROM srp_erp_mfq_bom_labourtask LEFT JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_labourtask.labourTask WHERE  bomMasterID = '$bomMasterID' AND srp_erp_mfq_bom_labourtask.companyID ={$companyID}";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }

    function delete_labour_task()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('bomLabourTaskID');
        $this->db->from('srp_erp_mfq_bom_labourtask');
        $this->db->where('bomMasterID', $masterID);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $result = $this->db->delete('srp_erp_mfq_bom_labourtask', array('bomLabourTaskID' => $this->input->post('bomLabourTaskID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function fetch_bom_overhead_cost()
    {
        $companyID = current_companyID();
        $bomMasterID = trim($this->input->post('bomMasterID') ?? '');
        $data = $this->db->query("SELECT * FROM srp_erp_mfq_bom_overhead LEFT JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_overhead.overHeadID WHERE bomMasterID = '$bomMasterID'  AND typeID = 1 AND srp_erp_mfq_bom_overhead.companyID={$companyID}")->result_array();
        return $data;
    }

    function fetch_bom_third_party_service_cost()
    {
        $companyID = current_companyID();
        $bomMasterID = trim($this->input->post('bomMasterID') ?? '');
        $data = $this->db->query("SELECT * FROM srp_erp_mfq_bom_overhead LEFT JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_overhead.overHeadID WHERE bomMasterID = '$bomMasterID'  AND typeID = 2 AND srp_erp_mfq_bom_overhead.companyID={$companyID}")->result_array();
        return $data;
    }

    function fetch_bom_machine_cost()
    {
        $companyID = current_companyID();
        $bomMasterID = trim($this->input->post('bomMasterID') ?? '');
        $data = $this->db->query("SELECT *,srp_erp_mfq_bom_machine.segmentID as segment FROM srp_erp_mfq_bom_machine LEFT JOIN srp_erp_mfq_fa_asset_master ON srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_bom_machine.mfq_faID WHERE bomMasterID = '$bomMasterID' AND srp_erp_mfq_bom_machine.companyID={$companyID}")->result_array();
        return $data;
    }

    function delete_overhead_cost()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_bom_overhead');
        $this->db->join('srp_erp_mfq_overhead', 'srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_overhead.overheadID');
        $this->db->where('bomMasterID', $masterID);
        $this->db->where('srp_erp_mfq_overhead.typeID', 1);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;
        //echo $this->db->last_query();

        $result = $this->db->delete('srp_erp_mfq_bom_overhead', array('bomOverheadID' => $this->input->post('bomOverheadID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function delete_machine_cost()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_bom_machine');
        $this->db->where('bomMasterID', $masterID);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;
        //echo $this->db->last_query();

        $result = $this->db->delete('srp_erp_mfq_bom_machine', array('bomMachineID' => $this->input->post('bomMachineID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function deleteBOM()
    {
        $masterID = $this->input->post('bomMasterID');
        $this->db->trans_start();
        $result = $this->db->delete('srp_erp_mfq_billofmaterial', array('bomMasterID' => $masterID), 1);
        if ($result) {
            $this->db->delete('srp_erp_mfq_bom_materialconsumption', array('bomMasterID' => $masterID));
            $this->db->delete('srp_erp_mfq_bom_labourtask', array('bomMasterID' => $masterID));
            $this->db->delete('srp_erp_mfq_bom_overhead', array('bomMasterID' => $masterID));
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
            } else {
                $this->db->trans_commit();
                return array('error' => 0, 'message' => 'Record deleted successfully');
            }
        }
    }

    function load_segment_hours()
    {
        $masterID = $this->input->post('segmentID');
        $this->db->select('hours');
        $this->db->from('srp_erp_mfq_segmenthours');
        $this->db->where('mfqSegmentID', $masterID);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function fetch_third_party_service()
    {
        $dataArr = array();
        $dataArr2 = array();
        $search_string = "%" . $_GET['query'] . "%";
        $companyID = current_companyID();
        $data = $this->db->query('SELECT srp_erp_mfq_overhead.*,CONCAT(IFNULL(description,""), " (" ,IFNULL(overHeadCode,""),")") AS "Match",srp_erp_mfq_segmenthours.hours FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_mfq_segmenthours ON srp_erp_mfq_overhead.mfqSegmentID = srp_erp_mfq_segmenthours.mfqSegmentID WHERE overHeadCategoryID = 1 AND  (overHeadCode LIKE "' . $search_string . '" OR description LIKE "' . $search_string . '") AND srp_erp_mfq_overhead.companyID="' . $companyID . '" AND srp_erp_mfq_overhead.typeID = 2 ')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['overHeadCode'], 'tpsID' => $val['overHeadID'], 'description' => $val['description'],'segment' => $val['mfqSegmentID'],'rate' => $val['rate'],'hours' => $val['hours'],'uom' => $val['unitOfMeasureID']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function delete_third_party_service_cost()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_bom_overhead');
        $this->db->join('srp_erp_mfq_overhead', 'srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_overhead.overheadID');
        $this->db->where('bomMasterID', $masterID);
        $this->db->where('srp_erp_mfq_overhead.typeID', 2);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $result = $this->db->delete('srp_erp_mfq_bom_overhead', array('bomOverheadID' => $this->input->post('bomOverheadID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }
}