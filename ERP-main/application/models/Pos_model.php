<?php

/** ================================
 * -- File Name : Pos_model.php
 * -- Project Name : SME
 * -- Module Name : Point of sale General
 * -- Create date : 19-09-2016
 * -- Description : model for POS general
 *
 * --REVISION HISTORY
 * Date: 25-05-2017 : worked on the bank ledger entry .
 *
 *
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');


class Pos_model extends ERP_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function item_search($barcode = false)
    {
        $this->load->model('Dashboard_model');
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouseID = $this->common_data['ware_houseID'];

        if ($barcode) {
            $search_string = $_GET['q'];
            $result = $this->db->query("SELECT t2.partNo,t2.itemAutoID,t2.seconeryItemCode, t2.itemSystemCode as itemSystemCode, t2.itemDescription, IFNULL(t1.currentStock,0) as currentStock,
                                 t2.companyLocalSellingPrice, defaultUnitOfMeasure, defaultUnitOfMeasureID as unitOfMeasureID, itemImage, barcode
                                 FROM srp_erp_warehouseitems t1
                                 JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                 WHERE (t2.partNo = '" . $search_string . "' OR t2.barcode =  '" . $search_string . "' OR t2.itemSystemCode =  '" . $search_string . "' OR t2.seconeryItemCode LIKE  '" . $search_string . "' )
                                 AND t2.companyID={$companyID} AND wareHouseAutoID ={$wareHouseID} AND isActive=1 AND allowedtoSellYN=1")->row_array();
            
            if (!empty($result)) {
                $_POST['masterUnitID'] = $result['unitOfMeasureID'];
                if (!empty($result['uom_output'])) {
                    $result['uom_output'] = $this->Dashboard_model->fetch_related_uom_id();
                }
            }

            //checking whether is there any promotion active for this item on today.
            $result['isPromotionApplicable'] = 0; //initially set to zero.
            $itemAutoID = $result['itemAutoID'];

            $warehousePromotion = $this->db->query("select * from srp_erp_pos_promotionwarehouses where companyID=$companyID and wareHouseID=$wareHouseID and isActive=1");
            if ($warehousePromotion->num_rows() > 0) {
                $promoID = $warehousePromotion->row()->promotionID;
                $activePromotion = $this->db->query("select * from srp_erp_pos_customers where customerID=$promoID");
                if ($activePromotion->num_rows() > 0) {
                    $applyToAllItem = $activePromotion->row()->applyToAllItem;
                    if ($applyToAllItem) {
                        $dateFrom = date('Y-m-d', strtotime($activePromotion->row()->dateFrom));
                        $dateTo = date('Y-m-d', strtotime($activePromotion->row()->dateTo));
                        $today = date('Y-m-d');
                        if (($today >= $dateFrom) && ($today <= $dateTo)) {
                            $result['isPromotionApplicable'] = 1;
                            $result['discountPercentage'] = $activePromotion->row()->commissionPercentage;
                        } else {
                            $result['isPromotionApplicable'] = 0;
                        }
                    } else {
                        $promoID = $activePromotion->row()->customerID;
                        $promotionapplicableitems = $this->db->query("select * from srp_erp_pos_promotionapplicableitems where itemAutoID=$itemAutoID and isActive=1 and companyID=$companyID and promotionID=$promoID");
                        if ($promotionapplicableitems->num_rows() > 0) {

                            $dateFrom = date('Y-m-d', strtotime($activePromotion->row()->dateFrom));
                            $dateTo = date('Y-m-d', strtotime($activePromotion->row()->dateTo));
                            $today = date('Y-m-d');
                            if (($today >= $dateFrom) && ($today <= $dateTo)) {
                                $discountPercentage = $promotionapplicableitems->row()->discountPercentage;
                                $result['isPromotionApplicable'] = 1;
                                $result['discountPercentage'] = $discountPercentage;
                            } else {
                                $result['isPromotionApplicable'] = 0;
                            }
                        }
                    }
                }
            }
        } else {
            $search_string = "%" . $_GET['q'] . "%";
            $customer = $this->input->get('customer');
            $result = $this->db->query("SELECT t2.partNo,t2.itemAutoID,t2.seconeryItemCode , t2.itemSystemCode, t2.itemDescription, IFNULL(t1.currentStock,0) as currentStock,
                                 t2.companyLocalSellingPrice, defaultUnitOfMeasure, defaultUnitOfMeasureID as unitOfMeasureID, barcode, itemImage,  IFNULL(j2.rSalesPrice,IFNULL(j1.rSalesPrice,t2.companyLocalSellingPrice)) AS companyLocalSellingPrice
                                 FROM srp_erp_warehouseitems t1
                                JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                LEFT JOIN (
                                    SELECT itemMasterID,wareHouseAutoID,salesPrice,rSalesPrice FROM srp_erp_item_master_pricing AS pt
                                    WHERE pt.isActive = 1 AND pt.wareHouseAutoID ={$wareHouseID} AND pt.pricingType = 'Direct'
                                 ) AS j1
                                 ON t1.itemAutoID = j1.itemMasterID AND t1.wareHouseAutoID = j1.wareHouseAutoID
                                LEFT JOIN (
                                SELECT itemMasterID,wareHouseAutoID,salesPrice,rSalesPrice FROM srp_erp_item_master_pricing AS pt
                                WHERE pt.isActive = 1 AND pt.pricingType = 'Selected' AND pt.customer ='{$customer}'
                                ) AS j2
                                ON t1.itemAutoID = j2.itemMasterID
                                WHERE (t2.partNo LIKE '" . $search_string . "' OR t1.itemSystemCode LIKE '" . $search_string . "' OR t1.itemDescription LIKE '" . $search_string . "' OR t2.seconeryItemCode LIKE  '" . $search_string . "' OR t2.barcode LIKE '" . $search_string . "')
                                 AND t2.companyID={$companyID} AND t1.wareHouseAutoID ={$wareHouseID} AND t2.isActive=1 AND t2.allowedtoSellYN=1 limit 10")->result_array();

            if (!empty($result)) {
                //$_POST['masterUnitID'] = $result['unitOfMeasureID'];
                if (!empty($result['uom_output'])) {
                    $result['uom_output'] = $this->Dashboard_model->fetch_related_uom_id();
                }
            }
        }
        if (!empty($itemAutoID)) {
            $assignedTaxFormula = $this->db->query("SELECT
	                                              IFNULL( taxFormulaID, 0 ) AS taxFormulaID 
                                                  FROM
	                                              `srp_erp_itemtaxformula` 
                                                  WHERE
	                                              ItemAutoID = {$itemAutoID} 
                                                  AND taxType = 1
                                                  ORDER BY
	                                              itemTaxformulaID DESC 
	                                              LIMIT 1")->row('taxFormulaID');

            if (!empty($assignedTaxFormula)) {
                $amount = fetch_line_wise_itemTaxcalculation_gpos($assignedTaxFormula, ($result['companyLocalSellingPrice']), 0, 'GPOS', null);
            } else {
                $amount = 0;
            }

            $result['default_taxAmount'] = $amount;
        }

        return $result;
    }

    function item_outlet_pricing_search($barcode = false)
    {
        $this->load->model('Dashboard_model');
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouseID = $this->common_data['ware_houseID'];
        $customerCode = trim($this->input->get('customer'));

        if ($barcode) {
            $search_string = $_GET['q'];
            $result = $this->db->query("SELECT t2.partNo,t2.itemAutoID,t2.seconeryItemCode, t2.itemSystemCode as itemSystemCode, t2.itemDescription, IFNULL(t1.currentStock,0) as currentStock,
                                 t2.companyLocalSellingPrice, defaultUnitOfMeasure, defaultUnitOfMeasureID as unitOfMeasureID, itemImage, barcode
                                 FROM srp_erp_warehouseitems t1
                                 JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                 WHERE (t2.partNo = '" . $search_string . "' OR t2.barcode =  '" . $search_string . "' OR t2.itemSystemCode =  '" . $search_string . "' OR t2.seconeryItemCode LIKE  '" . $search_string . "' )
                                 AND t2.companyID={$companyID} AND wareHouseAutoID ={$wareHouseID} AND isActive=1 AND allowedtoSellYN=1")->row_array();
            
           

            if (!empty($result)) {
                $_POST['masterUnitID'] = $result['unitOfMeasureID'];
                if (!empty($result['unitOfMeasureID'])) {
                    $uomList = $this->get_item_added_pricing_for_uom($result['itemAutoID'],$result['unitOfMeasureID']);

                    $isin_default = 0;
                    foreach($uomList as $uom){
                        if($uom['uomMasterID'] == $result['unitOfMeasureID']){
                            $isin_default = 1;
                        }
                    }

                    //if default unit of measure is not altered in pricing
                    if($isin_default == 0){
                        $uomList[] = $this->get_item_default_measure_details($result['itemAutoID'],$result['unitOfMeasureID']);
                    }

                    $result['uom_output'] = json_encode($uomList);

                }
            }

            //checking whether is there any promotion active for this item on today.
            $result['isPromotionApplicable'] = 0; //initially set to zero.
            $itemAutoID = $result['itemAutoID'];
            $original_pricing = $result['companyLocalSellingPrice'];
            

            $warehousePromotion = $this->db->query("select * from srp_erp_pos_promotionwarehouses where companyID=$companyID and wareHouseID=$wareHouseID and isActive=1");
            $warehouseOutletPromotion = $this->db->query("select * from srp_erp_item_master_pricing where companyID=$companyID and wareHouseAutoID=$wareHouseID and itemMasterID=$itemAutoID AND isActive=1")->result_array();
            $warehouseCustomerPromotion = $this->db->query("select * from srp_erp_item_master_pricing where companyID=$companyID and customer='$customerCode' and itemMasterID=$itemAutoID AND isActive=1")->result_array();
           


            if ($warehousePromotion->num_rows() > 0) {
                $promoID = $warehousePromotion->row()->promotionID;
                $activePromotion = $this->db->query("select * from srp_erp_pos_customers where customerID=$promoID");

                if ($activePromotion->num_rows() > 0) {
                    $applyToAllItem = $activePromotion->row()->applyToAllItem;
                    if ($applyToAllItem) {
                        $dateFrom = date('Y-m-d', strtotime($activePromotion->row()->dateFrom));
                        $dateTo = date('Y-m-d', strtotime($activePromotion->row()->dateTo));
                        $today = date('Y-m-d');
                        if (($today >= $dateFrom) && ($today <= $dateTo)) {
                            $result['isPromotionApplicable'] = 1;
                            $result['discountPercentage'] = $activePromotion->row()->commissionPercentage;
                        } else {
                            $result['isPromotionApplicable'] = 0;
                        }
                    } else {
                        $promoID = $activePromotion->row()->customerID;
                        $promotionapplicableitems = $this->db->query("select * from srp_erp_pos_promotionapplicableitems where itemAutoID=$itemAutoID and isActive=1 and companyID=$companyID and promotionID=$promoID");
                        if ($promotionapplicableitems->num_rows() > 0) {

                            $dateFrom = date('Y-m-d', strtotime($activePromotion->row()->dateFrom));
                            $dateTo = date('Y-m-d', strtotime($activePromotion->row()->dateTo));
                            $today = date('Y-m-d');
                            if (($today >= $dateFrom) && ($today <= $dateTo)) {
                                $discountPercentage = $promotionapplicableitems->row()->discountPercentage;
                                $result['isPromotionApplicable'] = 1;
                                $result['discountPercentage'] = $discountPercentage;
                            } else {
                                $result['isPromotionApplicable'] = 0;
                            }
                        }
                    }
                }
            }

            $added_promotions = array();
            $result['discountPercentage'] = (isset($result['discountPercentage'])) ? $result['discountPercentage'] : 0;

            if((count($warehouseOutletPromotion) > 0) && count($warehouseCustomerPromotion) == 0 ){

                foreach($warehouseOutletPromotion as $promotion){

                    $isDefault = $promotion['isDefault'];
                    $pricingType = $promotion['pricingType'];
                   

                    if(!in_array($promotion['pricingAutoID'],$added_promotions)){

                        if ($isDefault) {
                            $result['companyLocalSellingPrice'] = $promotion['salesPrice'];
                            $result['isPromotionApplicable'] = 1;
                            $result['discountPercentage'] = $result['discountPercentage'] + $promotion['discount'];
                            $added_promotions[] = $promotion['pricingAutoID'];
                        } else {
                            // $result['isPromotionApplicable'] = 0;
                            $result['companyLocalSellingPrice'] = $promotion['salesPrice'];
                            $result['discountPromotionNotDefault'] = $promotion['discount'];

                        }
                    }
                   

                }

            }



            if(count($warehouseCustomerPromotion) > 0){

                foreach($warehouseCustomerPromotion as $promotioncus){

                    $isDefault = $promotioncus['isDefault'];
                    $pricingType = $promotioncus['pricingType'];
                   

                    if(!in_array($promotioncus['pricingAutoID'],$added_promotions)){

                        if ($isDefault) {
                            $result['companyLocalSellingPrice'] = $promotioncus['salesPrice'];
                            $result['isPromotionApplicable'] = 1;
                            $result['discountPercentage'] = $result['discountPercentage'] + $promotioncus['discount'];
                            $added_promotions[] = $promotioncus['pricingAutoID'];
                        } else{
                            //$result['isPromotionApplicable'] = 0;
                            $result['companyLocalSellingPrice'] = $promotioncus['salesPrice'];
                            $result['discountPromotionNotDefault'] = $result['discountPromotionNotDefault'] + $promotioncus['discount'];
                        }
                    }
                   

                }

            }

            if(isset($result['discountPromotionNotDefault'])){
                $result['discountPromotionNotDefault'] = $result['discountPromotionNotDefault'] + $result['discountPercentage'];
            }
            
            
        } else {

            $search_string = "%" . $_GET['q'] . "%";
            $result = $this->db->query("SELECT t2.partNo,t2.itemAutoID,t2.seconeryItemCode , t2.itemSystemCode, t2.itemDescription, IFNULL(t1.currentStock,0) as currentStock,
                                 t2.companyLocalSellingPrice, defaultUnitOfMeasure, defaultUnitOfMeasureID as unitOfMeasureID, barcode, itemImage
                                 FROM srp_erp_warehouseitems t1
                                 JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                 WHERE (t2.partNo LIKE '" . $search_string . "' OR t1.itemSystemCode LIKE '" . $search_string . "' OR t1.itemDescription LIKE '" . $search_string . "' OR t2.seconeryItemCode LIKE  '" . $search_string . "' OR t2.barcode LIKE '" . $search_string . "')
                                 AND t2.companyID={$companyID} AND wareHouseAutoID ={$wareHouseID} AND isActive=1 AND allowedtoSellYN=1 limit 10")->result_array();

            if (!empty($result)) {
                //$_POST['masterUnitID'] = $result['unitOfMeasureID'];
                if (!empty($result['uom_output'])) {
                    $result['uom_output'] = $this->Dashboard_model->fetch_related_uom_id();
                }
            }
        }

        if (!empty($itemAutoID)) {
            $assignedTaxFormula = $this->db->query("SELECT
	                                              IFNULL( taxFormulaID, 0 ) AS taxFormulaID 
                                                  FROM
	                                              `srp_erp_itemtaxformula` 
                                                  WHERE
	                                              ItemAutoID = {$itemAutoID} 
                                                  AND taxType = 1
                                                  ORDER BY
	                                              itemTaxformulaID DESC 
	                                              LIMIT 1")->row('taxFormulaID');

            if (!empty($assignedTaxFormula)) {
                $amount = fetch_line_wise_itemTaxcalculation_gpos($assignedTaxFormula, ($result['companyLocalSellingPrice']), 0, 'GPOS', null);
            } else {
                $amount = 0;
            }

            $result['default_taxAmount'] = $amount;
        }

        return $result;
    }


    function shift_create()
    {
        $employeeID = current_userID();
        $wareHouseID = current_warehouseID();
        $counterID = $this->input->post('counterID');
        $startingBalance = $this->input->post('startingBalance');
        $startingBalance = str_replace(',', '', $startingBalance);

        $isAvailableSession = $this->db->select('counterID')->from('srp_erp_pos_shiftdetails')
            ->where('wareHouseID', $wareHouseID)->where('empID', $employeeID)
            ->where('isClosed', 0)->get()->row('counterID');

        if (empty($isAvailableSession)) {

            $this->db->select('srp_erp_pos_shiftdetails.*, srp_employeesdetails.Ename2')->from('srp_erp_pos_shiftdetails')
                ->where('wareHouseID', $wareHouseID)->where('counterID', $counterID);
            $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_pos_shiftdetails.empID', 'LEFT');
            $this->db->where('isClosed', 0);

            $isExist = $this->db->get()->row_array();
            if (empty($isExist)) {
                $com_currency = $this->common_data['company_data']['company_default_currency'];
                $tr_currency = $this->common_data['company_data']['company_default_currency']; /*Transaction currency is Company currency */
                $rep_currency = $this->common_data['company_data']['company_reporting_currency'];

                $localConversion = currency_conversion($tr_currency, $com_currency, $startingBalance);
                $com_currDPlace = $localConversion['DecimalPlaces'];
                $localConversionRate = $localConversion['conversion'];

                $transConversion = currency_conversion($tr_currency, $tr_currency, $startingBalance);
                $tr_currDPlace = $transConversion['DecimalPlaces'];
                $transConversionRate = $transConversion['conversion'];

                $reportConversion = currency_conversion($tr_currency, $rep_currency, $startingBalance);
                $rep_currDPlace = $reportConversion['DecimalPlaces'];
                $reportConversionRate = $reportConversion['conversion'];

                $data = array(
                    'wareHouseID' => $wareHouseID,
                    'empID' => $employeeID,
                    'counterID' => $counterID,
                    'startTime' => current_date(),

                    'startingBalance_transaction' => $startingBalance,
                    'startingBalance_local' => round(($startingBalance / $localConversionRate), $com_currDPlace),
                    'startingBalance_reporting' => round(($startingBalance / $reportConversionRate), $rep_currDPlace),


                    'transactionCurrency' => $tr_currency,
                    'transactionCurrencyDecimalPlaces' => $tr_currDPlace,
                    'transactionExchangeRate' => $transConversionRate,

                    'companyLocalCurrency' => $com_currency,
                    'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
                    'companyLocalExchangeRate' => $localConversionRate,

                    'companyReportingCurrency' => $rep_currency,
                    'companyReportingCurrencyDecimalPlaces' => $rep_currDPlace,
                    'companyReportingExchangeRate' => $reportConversionRate,

                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                    'createdPCID' => $this->common_data['current_pc'],
                    'createdUserID' => $this->common_data['current_userID'],
                    'createdUserName' => $this->common_data['current_user'],
                    'createdUserGroup' => $this->common_data['user_group'],
                    'createdDateTime' => current_date(),
                    'id_store' => current_warehouseID()
                );

                $this->db->insert('srp_erp_pos_shiftdetails', $data);
                if ($this->db->affected_rows() > 0) {
                    return array('s', 'Shift Created with counter'); /*, $this->promotionDetail() : removed */
                } else {
                    return array('e', 'Error In Shift Creation');
                }
            } else {
                $counterCode = $this->db->select('counterCode')->from('srp_erp_pos_counters')
                    ->where('counterID', $counterID)->get()->row('counterCode');
                return array('e', 'Already a shift is going on with counter [ ' . $counterCode . ' ] ' . $isExist['Ename2']);
            }
        } else {
            $counterCode = $this->db->select('counterCode')->from('srp_erp_pos_counters')
                ->where('counterID', $isAvailableSession)->get()->row('counterCode');
            return array('e', 'You have a unclosed session in counter [ ' . $counterCode . ' ]');
        }
    }

    function shift_close($shiftID = 0)
    {

        $endBalance = $this->input->post('startingBalance');
        $endBalance = str_replace(',', '', $endBalance);

        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $rep_currency = $this->common_data['company_data']['company_reporting_currency'];

        $localConversion = currency_conversion($com_currency, $com_currency, $endBalance);
        $localConversionRate = $localConversion['conversion'];

        $reportConversion = currency_conversion($com_currency, $rep_currency, $endBalance);
        $reportConversionRate = $reportConversion['conversion'];
        $cashSales = $this->input->post('cashSales');
        $cardCollection = $this->input->post('cardCollection');
        $closingCashBalance = $this->input->post('closingCashBalance');
        $different_transaction = $endBalance - $closingCashBalance;

        $data = array(
            'endTime' => current_date(),
            'isClosed' => 1,

            'endingBalance_transaction' => $endBalance,
            'endingBalance_local' => round(($endBalance / $localConversionRate), $localConversion['DecimalPlaces']),
            'endingBalance_reporting' => round(($endBalance / $reportConversionRate), $reportConversion['DecimalPlaces']),

            'cashSales' => $cashSales,
            'cashSales_local' => round(($cashSales / $localConversionRate), $localConversion['DecimalPlaces']),
            'cashSales_reporting' => round(($cashSales / $reportConversionRate), $reportConversion['DecimalPlaces']),

            'giftCardTopUp' => $cardCollection,
            'giftCardTopUp_local' => round(($cardCollection / $localConversionRate), $localConversion['DecimalPlaces']),
            'giftCardTopUp_reporting' => round(($cardCollection / $reportConversionRate), $reportConversion['DecimalPlaces']),

            'closingCashBalance_transaction' => $closingCashBalance,
            'closingCashBalance_local' => round(($closingCashBalance / $localConversionRate), $localConversion['DecimalPlaces']),
            'closingCashBalance_reporting' => round(($closingCashBalance / $reportConversionRate), $reportConversion['DecimalPlaces']),

            'different_transaction' => $different_transaction,
            'different_local' => round(($different_transaction / $localConversionRate), $localConversion['DecimalPlaces']),
            'different_local_reporting' => round(($different_transaction / $reportConversionRate), $reportConversion['DecimalPlaces']),


            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => current_date(),
            'id_store' => current_warehouseID(),
            'is_sync' => 0
        );

        $this->db->where('shiftID', $shiftID)->where('wareHouseID', current_warehouseID())->update('srp_erp_pos_shiftdetails', $data);
        $result = $this->db->affected_rows();
        /*echo $this->db->last_query();
        echo 'result: '.$result;*/
        return $result;
    }

    function isHaveNotClosedSession()
    {
        $where = array(
            'empID' => current_userID(),
            'companyID' => current_companyID(),
            'wareHouseID' => current_warehouseID(),
            'isClosed' => 0,
        );

        $result = $this->db->select('shiftID, counterID')->from('srp_erp_pos_shiftdetails')->where($where)->get()->row_array();
        return $result;
    }

    function getInvoiceCode()
    {
        $query = $this->db->select('serialNo')->from('srp_erp_pos_invoice')->where('companyID', $this->common_data['company_data']['company_id'])
            ->order_by('invoiceID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastRefNo = $lastRefArray['serialNo'];
        $lastRefNo = ($lastRefNo == null) ? 1 : $lastRefArray['serialNo'] + 1;

        $this->load->library('sequence');
        $refCode = $this->sequence->sequence_generator('POS', $lastRefNo);

        return array('refCode' => $refCode, 'lastRefNo' => $lastRefNo);
    }

    function getInvoiceHoldCode()
    {
        $query = $this->db->select('serialNo')->from('srp_erp_pos_invoicehold')->where('companyID', $this->common_data['company_data']['company_id'])
            ->order_by('invoiceID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastRefNo = $lastRefArray['serialNo'];
        $lastRefNo = ($lastRefNo == null) ? 1 : $lastRefArray['serialNo'] + 1;

        $this->load->library('sequence');
        $refCode = $this->sequence->sequence_generator('REF-H', $lastRefNo);

        return array('refCode' => $refCode, 'lastRefNo' => $lastRefNo);
    }

    function get_wareHouse()
    {
        $this->db->select('wHouse.wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation, segmentID, segmentCode')
            ->from('srp_erp_warehousemaster wHouse')
            ->join('srp_erp_pos_segmentconfig conf', 'conf.wareHouseAutoID=wHouse.wareHouseAutoID', 'left')
            ->where('wHouse.wareHouseAutoID', $this->common_data['ware_houseID']);
        return $this->db->get()->row_array();
    }

    function invoice_create()
    {
        $totVal = $this->input->post('totVal');
        $payConData = posPaymentConfig_data();

        if (empty($payConData)) {
            return array('e', 'Payment GL configuration is not configured');
            exit;
        }

        $currentShiftData = $this->isHaveNotClosedSession();

        $financeYear = $this->db->select('companyFinanceYearID, beginingDate, endingDate')->from('srp_erp_companyfinanceyear')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();


        $financePeriod = $this->db->select('companyFinancePeriodID, dateFrom, dateTo')->from('srp_erp_companyfinanceperiod')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();

        if (empty($financeYear)) {
            return array('e', 'Please setup the current financial year');
            exit;
        }

        if (empty($financePeriod)) {
            return array('e', 'Please setup the current financial period');
            exit;
        }

        if (!empty($currentShiftData)) {

            $com_currency = $this->common_data['company_data']['company_default_currency'];
            $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
            $rep_currency = $this->common_data['company_data']['company_reporting_currency'];
            $rep_currDPlace = $this->common_data['company_data']['company_reporting_decimal'];
            $wareHouseData = $this->get_wareHouse();


            $customerID = $this->input->post('customerID');
            $customerCode = $this->input->post('customerCode');
            $tr_currency = $this->input->post('_trCurrency');
            $item = $this->input->post('itemID[]');
            $itemUOM = $this->input->post('itemUOM[]');
            $itemQty = $this->input->post('itemQty[]');
            $itemPrice = $this->input->post('itemPrice[]');
            $itemDis = $this->input->post('itemDis[]');
            $invoiceDate = format_date(date('Y-m-d'));

            /*Payment Details Calculation Start*/
            $cashAmount = $this->input->post('_cashAmount');
            $creditSalesAmount = $this->input->post('_creditSalesAmount');
            $chequeAmount = $this->input->post('_chequeAmount');
            $cardAmount = $this->input->post('_cardAmount');
            $referenceNO = $this->input->post('_referenceNO');
            $cardNumber = $this->input->post('_cardNumber');
            $bank = $this->input->post('_bank');
            $creditNoteAmount = str_replace(',', '', $this->input->post('_creditNoteAmount'));
            $creditNote_invID = $this->input->post('creditNote-invID');
            $total_discVal = $this->input->post('discVal');
            $paidAmount = ($cashAmount + $chequeAmount + $cardAmount + $creditNoteAmount);
            $netTotVal = $this->input->post('netTotVal');
            $totVal = $this->input->post('totVal');
            $balanceAmount = ($netTotVal - $paidAmount);

            $chequeNO = $this->input->post('_chequeNO');
            $chequeCashDate = $this->input->post('_chequeCashDate');


            if ($netTotVal < $paidAmount) {
                $cashAmount = $netTotVal - ($chequeAmount + $cardAmount + $creditNoteAmount);
                $balanceAmount = 0;
            }
            $isCreditSales = 0;
            if ($creditSalesAmount > 0) {
                $isCreditSales = 1;
                $balanceAmount = 0;
            }

            /*Payment Details Calculation End*/

            //Get last reference no
            $invCodeDet = $this->getInvoiceCode();
            $lastRefNo = $invCodeDet['lastRefNo'];
            $refCode = $invCodeDet['refCode'];

            $WarehouseID = current_warehouseID();

            $querys = $this->db->select('wareHouseCode')->from('srp_erp_warehousemaster')->where('wareHouseAutoID', $WarehouseID)->get();
            $WarehouseCode = $querys->row_array();

            $invSequenceCodeDet = $this->getInvoiceSequenceCode();
            $lastINVNo = $invSequenceCodeDet['lastINVNo'];
            $sequenceCode = $invSequenceCodeDet['sequenceCode'];
            /*********************************************************************************************
             * Always transaction is going with transaction currency [ Transaction Currency => OMR ]
             * If we want to know the reporting amount [ Reporting Currency => USD ]
             * So the currency_conversion functions 1st parameter will be the USD [what we looking for ]
             * And the 2nd parameter will be the OMR [what we already got]
             *
             * Ex :
             *    Transaction currency => OMR   => $trCurrency
             *    Transaction Amount => 1000/-  => $trAmount
             *    Reporting Currency => USD     => $reCurrency
             *
             *    $conversionData  = currency_conversion($trCurrency, $reCurrency, $trAmount);
             *    $conversionRate  = $conversionData['conversion'];
             *    $decimalPlace    = $conversionData['DecimalPlaces'];
             *    $reportingAmount = round( ($trAmount / $conversionRate) , $decimalPlace );
             **********************************************************************************************/

            $localConversion = currency_conversion($tr_currency, $com_currency, $netTotVal);
            $localConversionRate = $localConversion['conversion'];
            $transConversion = currency_conversion($tr_currency, $tr_currency, $netTotVal);
            $tr_currDPlace = $transConversion['DecimalPlaces'];
            $transConversionRate = $transConversion['conversion'];
            $reportConversion = currency_conversion($tr_currency, $rep_currency, $netTotVal);
            $reportConversionRate = $reportConversion['conversion'];

            $isStockCheck = 0;

            $invArray = array(
                'documentSystemCode' => $refCode,
                'documentCode' => 'POS',
                'serialNo' => $lastRefNo,
                'invoiceSequenceNo' => $lastINVNo,
                'invoiceCode' => $sequenceCode,
                'financialYearID' => $financeYear['companyFinanceYearID'],
                'financialPeriodID' => $financePeriod['companyFinancePeriodID'],
                'FYBegin' => $financeYear['beginingDate'],
                'FYEnd' => $financeYear['endingDate'],
                'FYPeriodDateFrom' => $financePeriod['dateFrom'],
                'FYPeriodDateTo' => $financePeriod['dateTo'],
                'customerID' => $customerID,
                'customerCode' => $customerCode,
                'invoiceDate' => $invoiceDate,
                'counterID' => $currentShiftData['counterID'],
                'shiftID' => $currentShiftData['shiftID'],
                'subTotal' => $totVal,
                'netTotal' => $netTotVal,
                'paidAmount' => $paidAmount,
                'balanceAmount' => $balanceAmount,
                'cashAmount' => $cashAmount,
                'chequeAmount' => $chequeAmount,
                'cardAmount' => $cardAmount,
                'discountAmount' => $total_discVal,
                'creditNoteID' => $creditNote_invID,
                'creditNoteAmount' => $creditNoteAmount,
                'creditSalesAmount' => $creditSalesAmount,
                'isCreditSales' => $isCreditSales,
                'chequeNo' => $chequeNO,
                'chequeDate' => $chequeCashDate,
                'companyLocalCurrencyID' => $localConversion['currencyID'],
                'companyLocalCurrency' => $com_currency,
                'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
                'companyLocalExchangeRate' => $localConversionRate,
                'transactionCurrencyID' => $localConversion['trCurrencyID'],
                'transactionCurrency' => $tr_currency,
                'transactionCurrencyDecimalPlaces' => $tr_currDPlace,
                'transactionExchangeRate' => $transConversionRate,
                'companyReportingCurrencyID' => $reportConversion['currencyID'],
                'companyReportingCurrency' => $rep_currency,
                'companyReportingCurrencyDecimalPlaces' => $rep_currDPlace,
                'companyReportingExchangeRate' => $reportConversionRate,
                'wareHouseAutoID' => $wareHouseData['wareHouseAutoID'],
                'wareHouseCode' => $wareHouseData['wareHouseCode'],
                'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
                'wareHouseDescription' => $wareHouseData['wareHouseDescription'],
                'segmentID' => $wareHouseData['segmentID'],
                'segmentCode' => $wareHouseData['segmentCode'],
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date(),
            );

            if (isset($bank)) {
                $invArray['cardRefNo'] = $referenceNO;
                $invArray['cardBank'] = $bank;
                $invArray['cardNumber'] = $cardNumber;
            }

            if ($customerID == 0) {
                $bankData = $this->db->query("SELECT receivableAutoID, receivableSystemGLCode, receivableGLAccount,
                                          receivableDescription, receivableType
                                          FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();
                $invArray['bankGLAutoID'] = $bankData['receivableAutoID'];
                $invArray['bankSystemGLCode'] = $bankData['receivableSystemGLCode'];
                $invArray['bankGLAccount'] = $bankData['receivableGLAccount'];
                $invArray['bankGLDescription'] = $bankData['receivableDescription'];
                $invArray['bankGLType'] = $bankData['receivableType'];

                /*************** item ledger party currency ***********/
                $partyData = array(
                    'cusID' => 0,
                    'sysCode' => 'CASH',
                    'cusName' => 'CASH',
                    'partyCurID' => $localConversion['trCurrencyID'],
                    'partyCurrency' => $tr_currency,
                    'partyDPlaces' => $tr_currDPlace,
                    'partyER' => $transConversionRate,
                );
            } else {

                $cusData = $this->db->query("SELECT customerAutoID, customerSystemCode, customerName, receivableAutoID,
                                             receivableSystemGLCode, receivableGLAccount, receivableDescription, receivableType,
                                             customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces,customerAddress1,customerTelephone
                                             FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

                $partyData = currency_conversion($tr_currency, $cusData['customerCurrency']);

                $invArray['customerCurrencyID'] = $cusData['customerCurrencyID'];
                $invArray['customerCurrency'] = $cusData['customerCurrency'];
                $invArray['customerCurrencyExchangeRate'] = $partyData['conversion'];
                $invArray['customerCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];

                $invArray['customerReceivableAutoID'] = $cusData['receivableAutoID'];
                $invArray['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
                $invArray['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
                $invArray['customerReceivableDescription'] = $cusData['receivableDescription'];
                $invArray['customerReceivableType'] = $cusData['receivableType'];

                /*************** item ledger party currency ***********/

                $partyData = array(
                    'cusID' => $cusData['customerAutoID'],
                    'sysCode' => $cusData['customerSystemCode'],
                    'cusName' => $cusData['customerName'],
                    'partyCurID' => $cusData['customerCurrencyID'],
                    'partyCurrency' => $cusData['customerCurrency'],
                    'partyDPlaces' => $cusData['customerCurrencyDecimalPlaces'],
                    'partyER' => $partyData['conversion'],
                    'partyGL' => $cusData,
                );
            }

            /*Load wac library*/
            $this->load->library('Wac');
            $this->load->library('sequence');

            $this->db->trans_start();
            $this->db->insert('srp_erp_pos_invoice', $invArray);
            $invID = $this->db->insert_id();

            if ($creditSalesAmount != 0) {
                $data_customer_invoice['invoiceType'] = 'Direct';
                $data_customer_invoice['documentID'] = 'CINV';
                $data_customer_invoice['posTypeID'] = 1;
                $data_customer_invoice['referenceNo'] = $sequenceCode;
                $data_customer_invoice['invoiceNarration'] = 'POS Credit Sales - ' . $sequenceCode;
                $data_customer_invoice['posMasterAutoID'] = $invID;
                $data_customer_invoice['invoiceDate'] = current_date();
                $data_customer_invoice['invoiceDueDate'] = current_date();
                $data_customer_invoice['customerInvoiceDate'] = current_date();
                $data_customer_invoice['invoiceCode'] = $this->sequence->sequence_generator($data_customer_invoice['documentID']);
                $customerInvoiceCode = $data_customer_invoice['invoiceCode'];
                $data_customer_invoice['companyFinanceYearID'] = $this->common_data['company_data']['companyFinanceYearID'];
                $financialYear = get_financial_from_to($this->common_data['company_data']['companyFinanceYearID']);
                $data_customer_invoice['companyFinanceYear'] = trim($financialYear['beginingDate'] ?? '') . ' - ' . trim($financialYear['endingDate'] ?? '');
                $data_customer_invoice['FYBegin'] = trim($financialYear['beginingDate'] ?? '');
                $data_customer_invoice['FYEnd'] = trim($financialYear['endingDate'] ?? '');
                $data_customer_invoice['FYPeriodDateFrom'] = trim($this->common_data['company_data']['FYPeriodDateFrom']);
                $data_customer_invoice['FYPeriodDateTo'] = trim($this->common_data['company_data']['FYPeriodDateTo']);
                $data_customer_invoice['companyFinancePeriodID'] = $this->common_data['company_data']['companyFinancePeriodID'];
                $data_customer_invoice['customerID'] = $customerID;
                $data_customer_invoice['customerSystemCode'] = $cusData['customerSystemCode'];
                $data_customer_invoice['customerName'] = $cusData['customerName'];
                $data_customer_invoice['customerAddress'] = $cusData['customerAddress1'];
                $data_customer_invoice['customerTelephone'] = $cusData['customerTelephone'];
                $data_customer_invoice['customerFax'] = $cusData['customerTelephone'];
                $data_customer_invoice['customerEmail'] = $cusData['customerTelephone'];
                $data_customer_invoice['customerReceivableAutoID'] = $cusData['receivableAutoID'];
                $data_customer_invoice['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
                $data_customer_invoice['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
                $data_customer_invoice['customerReceivableDescription'] = $cusData['receivableDescription'];
                $data_customer_invoice['customerReceivableType'] = $cusData['receivableType'];
                $data_customer_invoice['customerCurrency'] = $cusData['customerCurrency'];
                $data_customer_invoice['customerCurrencyID'] = $cusData['customerCurrencyID'];
                $data_customer_invoice['customerCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];

                $data_customer_invoice['confirmedYN'] = 1;
                $data_customer_invoice['confirmedByEmpID'] = current_userID();
                $data_customer_invoice['confirmedByName'] = current_user();
                $data_customer_invoice['confirmedDate'] = current_date();
                $data_customer_invoice['approvedYN'] = 1;
                $data_customer_invoice['approvedDate'] = current_date();
                $data_customer_invoice['currentLevelNo'] = 1;
                $data_customer_invoice['approvedbyEmpID'] = current_userID();
                $data_customer_invoice['approvedbyEmpName'] = current_user();

                $data_customer_invoice['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data_customer_invoice['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $data_customer_invoice['transactionExchangeRate'] = 1;
                $data_customer_invoice['transactionAmount'] = $totVal;
                $data_customer_invoice['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_customer_invoice['transactionCurrencyID']);
                $data_customer_invoice['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data_customer_invoice['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $default_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyLocalCurrencyID']);
                $data_customer_invoice['companyLocalExchangeRate'] = $default_currency['conversion'];
                $data_customer_invoice['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                $data_customer_invoice['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                $data_customer_invoice['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                $reporting_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyReportingCurrencyID']);
                $data_customer_invoice['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                $data_customer_invoice['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                $customer_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['customerCurrencyID']);
                $data_customer_invoice['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
                $data_customer_invoice['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
                $data_customer_invoice['companyCode'] = $this->common_data['company_data']['company_code'];
                $data_customer_invoice['companyID'] = $this->common_data['company_data']['company_id'];
                $data_customer_invoice['createdUserGroup'] = $this->common_data['user_group'];
                $data_customer_invoice['createdPCID'] = $this->common_data['current_pc'];
                $data_customer_invoice['createdUserID'] = $this->common_data['current_userID'];
                $data_customer_invoice['createdUserName'] = $this->common_data['current_user'];
                $data_customer_invoice['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerinvoicemaster', $data_customer_invoice);
                $customerInvoiceMasterID = $this->db->insert_id();

                if ($customerInvoiceMasterID) {
                    $doc_approved['departmentID'] = "CINV";
                    $doc_approved['documentID'] = "CINV";
                    $doc_approved['documentCode'] = $data_customer_invoice['invoiceCode'];
                    $doc_approved['documentSystemCode'] = $customerInvoiceMasterID;
                    $doc_approved['documentDate'] = current_date();
                    $doc_approved['approvalLevelID'] = 1;
                    $doc_approved['docConfirmedDate'] = current_date();
                    $doc_approved['docConfirmedByEmpID'] = current_userID();
                    $doc_approved['table_name'] = 'srp_erp_customerinvoicemaster';
                    $doc_approved['table_unique_field_name'] = 'invoiceAutoID';
                    $doc_approved['approvedEmpID'] = current_userID();
                    $doc_approved['approvedYN'] = 1;
                    $doc_approved['approvedComments'] = 'Approved from POS';
                    $doc_approved['approvedPC'] = current_pc();
                    $doc_approved['approvedDate'] = current_date();
                    $doc_approved['companyID'] = current_companyID();
                    $doc_approved['companyCode'] = current_company_code();
                    $this->db->insert('srp_erp_documentapproved', $doc_approved);

                    $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,segmentID,segmentCode,transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces');
                    $this->db->from('srp_erp_customerinvoicemaster');
                    $this->db->where('invoiceAutoID', $customerInvoiceMasterID);
                    $master = $this->db->get()->row_array();

                    $data_customer_invoice_detail['invoiceAutoID'] = $customerInvoiceMasterID;
                    $data_customer_invoice_detail['type'] = 'GL';
                    $data_customer_invoice_detail['description'] = 'POS Sales - ' . $sequenceCode;
                    $data_customer_invoice_detail['transactionAmount'] = round($totVal, $master['transactionCurrencyDecimalPlaces']);
                    $companyLocalAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyLocalExchangeRate'];
                    $data_customer_invoice_detail['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                    $companyReportingAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyReportingExchangeRate'];
                    $data_customer_invoice_detail['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                    $customerAmount = $data_customer_invoice_detail['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                    $data_customer_invoice_detail['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                    $data_customer_invoice_detail['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data_customer_invoice_detail['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_customer_invoice_detail['createdUserGroup'] = $this->common_data['user_group'];
                    $data_customer_invoice_detail['createdPCID'] = $this->common_data['current_pc'];
                    $data_customer_invoice_detail['createdUserID'] = $this->common_data['current_userID'];
                    $data_customer_invoice_detail['createdUserName'] = $this->common_data['current_user'];
                    $data_customer_invoice_detail['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_customerinvoicedetails', $data_customer_invoice_detail);
                }
            }

            $i = 0;
            $item_ledger_arr = array();
            $dataInt = array();
            foreach ($item as $itemID) {
                $itemData = fetch_ware_house_item_data($itemID);
                $conversion = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
                $conversion = ($conversion == 0) ? 1 : $conversion;
                $conversionRate = 1 / $conversion;
                $availableQTY = $itemData['wareHouseQty'];
                $qty = $itemQty[$i] * $conversionRate;

                if ($availableQTY < $qty && $isStockCheck == 1) {
                    $this->db->trans_rollback();
                    return array('e', '[ ' . $itemData['itemSystemCode'] . ' - ' . $itemData['itemDescription'] . ' ]<p> is available only ' . $availableQTY . ' qty');
                    break;
                }

                $price = str_replace(',', '', $itemPrice[$i]);
                $itemTotal = $itemQty[$i] * $price;
                $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal - ($itemTotal * 0.01 * $itemDis[$i])) : $itemTotal;
                $itemTotal = round($itemTotal, $tr_currDPlace);

                $dataInt[$i]['invoiceID'] = $invID;
                $dataInt[$i]['itemAutoID'] = $itemID;
                $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
                $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
                $dataInt[$i]['defaultUOMID'] = $itemData['defaultUnitOfMeasureID'];
                $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
                $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
                $dataInt[$i]['UOMID'] = $itemData['defaultUnitOfMeasureID'];
                $dataInt[$i]['conversionRateUOM'] = $conversion;
                $dataInt[$i]['qty'] = $itemQty[$i];
                $dataInt[$i]['price'] = $price;
                $dataInt[$i]['discountPer'] = $itemDis[$i];
                $dataInt[$i]['wacAmount'] = $itemData['companyLocalWacAmount'];

                $dataInt[$i]['itemFinanceCategory'] = $itemData['subcategoryID'];
                $dataInt[$i]['itemFinanceCategorySub'] = $itemData['subSubCategoryID'];
                $dataInt[$i]['financeCategory'] = $itemData['financeCategory'];
                $dataInt[$i]['itemCategory'] = $itemData['mainCategory'];

                $dataInt[$i]['expenseGLAutoID'] = $itemData['costGLAutoID'];
                $dataInt[$i]['expenseGLCode'] = $itemData['costGLCode'];
                $dataInt[$i]['expenseSystemGLCode'] = $itemData['costSystemGLCode'];
                $dataInt[$i]['expenseGLDescription'] = $itemData['costDescription'];
                $dataInt[$i]['expenseGLType'] = $itemData['costType'];

                $dataInt[$i]['revenueGLAutoID'] = $itemData['revanueGLAutoID'];
                $dataInt[$i]['revenueGLCode'] = $itemData['revanueGLCode'];
                $dataInt[$i]['revenueSystemGLCode'] = $itemData['revanueSystemGLCode'];
                $dataInt[$i]['revenueGLDescription'] = $itemData['revanueDescription'];
                $dataInt[$i]['revenueGLType'] = $itemData['revanueType'];

                $dataInt[$i]['assetGLAutoID'] = $itemData['assteGLAutoID'];
                $dataInt[$i]['assetGLCode'] = $itemData['assteGLCode'];
                $dataInt[$i]['assetSystemGLCode'] = $itemData['assteSystemGLCode'];
                $dataInt[$i]['assetGLDescription'] = $itemData['assteDescription'];
                $dataInt[$i]['assetGLType'] = $itemData['assteType'];


                $dataInt[$i]['transactionAmount'] = $itemTotal;
                $dataInt[$i]['transactionExchangeRate'] = $transConversionRate;
                $dataInt[$i]['transactionCurrency'] = $tr_currency;
                $dataInt[$i]['transactionCurrencyID'] = $localConversion['trCurrencyID'];
                $dataInt[$i]['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;
                $dataInt[$i]['companyLocalAmount'] = round(($itemTotal / $localConversionRate), $com_currDPlace);

                $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
                $dataInt[$i]['companyLocalCurrency'] = $com_currency;
                $dataInt[$i]['companyLocalCurrencyID'] = $localConversion['currencyID'];
                $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

                $dataInt[$i]['companyReportingAmount'] = round(($itemTotal / $reportConversionRate), $rep_currDPlace);
                $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
                $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
                $dataInt[$i]['companyReportingCurrencyID'] = $reportConversion['currencyID'];
                $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

                $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
                $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
                $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
                $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $dataInt[$i]['createdDateTime'] = current_date();

                /*$balanceQty = $availableQTY - $qty;
                $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
                $itemUpdateQty = array('currentStock' => $balanceQty);
                $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);*/

                $wacData = $this->wac->wac_calculation(1, $itemID, $qty, '', $this->common_data['ware_houseID']);

                if ($creditSalesAmount > 0) {
                    $item_ledger_arr[$i] = $this->item_ledger_customerInvoice($financeYear, $financePeriod, $customerInvoiceCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData, null, $customerInvoiceMasterID);
                } else {
                    $item_ledger_arr[$i] = $this->item_ledger($financeYear, $financePeriod, $refCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData);
                }

                $i++;
            }

            //echo '<pre>';print_r($dataInt);echo '</pre>'; exit;

            $this->db->insert_batch('srp_erp_pos_invoicedetail', $dataInt);
            $isInvoiced = $this->input->post('isInvoiced');
            if (!empty($isInvoiced)) {
                $holdinv['isInvoiced'] = 1;
                $this->db->where('invoiceID', $isInvoiced);
                $this->db->update('srp_erp_pos_invoicehold', $holdinv);
            }
            $this->db->insert_batch('srp_erp_itemledger', $item_ledger_arr);
            if ($creditSalesAmount != 0) {
                $this->double_entry($invID, $partyData, $creditSalesAmount, $customerInvoiceMasterID);
            } else {
                $this->double_entry($invID, $partyData, $creditSalesAmount);
            }
            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                return array('e', 'Error in Invoice Create');
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Code : ' . $sequenceCode . ' ', $invID, $refCode);
            }

            $this->db->trans_complete();
        } else {
            return array('e', 'You have not a valid session.<p>Please login and try again.</p>');
        }
    }

    function get_current_period(){

        $financeYear = $this->db->select('companyFinanceYearID, beginingDate, endingDate')->from('srp_erp_companyfinanceyear')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();


        $financePeriod = $this->db->select('companyFinancePeriodID, dateFrom, dateTo')->from('srp_erp_companyfinanceperiod')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();


        return array('year' => $financeYear,'period'=>$financePeriod);

    }

    function item_ledger($financeYear, $financePeriod, $refCode, $itemData, $repoWac, $wacData, $wareHouseData, $partyData, $isReturn = null)
    {
        //$tranQty = $itemData['qty'] * ( 1 / $itemData['conversionRateUOM'] );
       
        $tranQty_um = $itemData['qty'];

        $tranQty = $tranQty_um * ( 1 / $itemData['conversionRateUOM'] );
        
        $ledger_arr = array();
        $ledger_arr['documentID'] = ($isReturn == null) ? 'POS' : 'RET';
        $ledger_arr['documentCode'] = ($isReturn == null) ? 'POS' : 'RET';
        $ledger_arr['documentAutoID'] = $itemData['invoiceID'];
        $ledger_arr['documentSystemCode'] = $refCode;
        $ledger_arr['documentDate'] = date('Y-m-d');
        $ledger_arr['referenceNumber'] = $refCode;
        $ledger_arr['companyFinanceYearID'] = $financeYear['companyFinanceYearID'];
        $ledger_arr['companyFinanceYear'] = $financeYear['beginingDate'] . ' - ' . $financeYear['endingDate'];
        $ledger_arr['FYBegin'] = $financeYear['beginingDate'];
        $ledger_arr['FYEnd'] = $financeYear['endingDate'];
        $ledger_arr['FYPeriodDateFrom'] = $financePeriod['dateFrom'];
        $ledger_arr['FYPeriodDateTo'] = $financePeriod['dateTo'];
        $ledger_arr['wareHouseAutoID'] = $wareHouseData['wareHouseAutoID'];
        $ledger_arr['wareHouseCode'] = $wareHouseData['wareHouseCode'];
        $ledger_arr['wareHouseLocation'] = $wareHouseData['wareHouseLocation'];
        $ledger_arr['wareHouseDescription'] = $wareHouseData['wareHouseDescription'];
        $ledger_arr['itemAutoID'] = $itemData['itemAutoID'];
        $ledger_arr['itemSystemCode'] = $itemData['itemSystemCode'];
        $ledger_arr['itemDescription'] = $itemData['itemDescription'];
        $ledger_arr['defaultUOMID'] = $itemData['defaultUOMID'];
        $ledger_arr['defaultUOM'] = $itemData['defaultUOM'];
        $ledger_arr['transactionUOM'] = $itemData['unitOfMeasure'];
        $ledger_arr['transactionUOMID'] = $itemData['UOMID'];

        if ($isReturn == null) {
            $ledger_arr['transactionQTY'] = ($tranQty_um * -1);
        } else {
            $ledger_arr['transactionQTY'] = $tranQty_um;
        }

        $ledger_arr['convertionRate'] = $itemData['conversionRateUOM'];
        $ledger_arr['currentStock'] = $wacData[2];
        $ledger_arr['companyLocalWacAmount'] = $itemData['wacAmount'];
        $ledger_arr['companyReportingWacAmount'] = $repoWac;


        $ledger_arr['PLGLAutoID'] = $itemData['expenseGLAutoID'];
        $ledger_arr['PLSystemGLCode'] = $itemData['expenseSystemGLCode'];
        $ledger_arr['PLGLCode'] = $itemData['expenseGLCode'];
        $ledger_arr['PLDescription'] = $itemData['expenseGLDescription'];
        $ledger_arr['PLType'] = $itemData['expenseGLType'];


        $ledger_arr['BLGLAutoID'] = $itemData['assetGLAutoID'];
        $ledger_arr['BLSystemGLCode'] = $itemData['assetSystemGLCode'];
        $ledger_arr['BLGLCode'] = $itemData['assetGLCode'];
        $ledger_arr['BLDescription'] = $itemData['assetGLDescription'];
        $ledger_arr['BLType'] = $itemData['assetGLType'];


        $ledger_arr['transactionCurrencyDecimalPlaces'] = $itemData['transactionCurrencyDecimalPlaces'];
        if ($isReturn == null) {
            $wacMin = $itemData['wacAmount'] * -1;
            $ledger_arr['transactionAmount'] = round(($wacMin * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
        } else {
            $ledger_arr['transactionAmount'] = round(($itemData['wacAmount'] * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
        }

        $itemDiscount = $itemData['discountPer'];
        $ledger_arr['salesPrice'] = ($itemDiscount > 0) ? ($itemData['price'] - ($itemData['price'] * $itemDiscount * 0.01)) : $itemData['price'];
        $ledger_arr['transactionCurrencyID'] = $itemData['transactionCurrencyID'];
        $ledger_arr['transactionCurrency'] = $itemData['transactionCurrency'];
        $ledger_arr['transactionExchangeRate'] = $itemData['transactionExchangeRate'];

        $ledger_arr['companyLocalCurrencyID'] = $itemData['companyLocalCurrencyID'];
        $ledger_arr['companyLocalCurrency'] = $itemData['companyLocalCurrency'];
        $ledger_arr['companyLocalExchangeRate'] = $itemData['companyLocalExchangeRate'];
        $ledger_arr['companyLocalCurrencyDecimalPlaces'] = $itemData['companyLocalCurrencyDecimalPlaces'];
        $ledger_arr['companyLocalAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyLocalExchangeRate']), $itemData['companyLocalCurrencyDecimalPlaces']);

        $ledger_arr['companyReportingCurrencyID'] = $itemData['companyReportingCurrencyID'];
        $ledger_arr['companyReportingCurrency'] = $itemData['companyReportingCurrency'];
        $ledger_arr['companyReportingExchangeRate'] = $itemData['companyReportingExchangeRate'];
        $ledger_arr['companyReportingCurrencyDecimalPlaces'] = $itemData['companyReportingCurrencyDecimalPlaces'];
        $ledger_arr['companyReportingAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyReportingExchangeRate']),
            $itemData['companyReportingCurrencyDecimalPlaces']
        );


        $ledger_arr['partyCurrency'] = $partyData['partyCurrency'];
        $ledger_arr['partyCurrencyExchangeRate'] = $partyData['partyER'];
        $ledger_arr['partyCurrencyDecimalPlaces'] = $partyData['partyDPlaces'];
        $ledger_arr['partyCurrencyAmount'] = round(($ledger_arr['transactionAmount'] / $partyData['partyER']), $partyData['partyDPlaces']);


        $ledger_arr['confirmedYN'] = 1;
        $ledger_arr['confirmedByEmpID'] = $itemData['createdUserID'];
        $ledger_arr['confirmedByName'] = $itemData['createdUserName'];
        $ledger_arr['confirmedDate'] = $itemData['createdDateTime'];
        $ledger_arr['approvedYN'] = 1;
        $ledger_arr['approvedbyEmpID'] = $itemData['createdUserID'];
        $ledger_arr['approvedbyEmpName'] = $itemData['createdUserName'];
        $ledger_arr['approvedDate'] = $itemData['createdDateTime'];
        $ledger_arr['segmentID'] = $wareHouseData['segmentID'];
        $ledger_arr['segmentCode'] = $wareHouseData['segmentCode'];
        $ledger_arr['companyID'] = $itemData['companyID'];
        $ledger_arr['companyCode'] = $itemData['companyCode'];
        $ledger_arr['createdUserGroup'] = $itemData['createdUserGroup'];
        $ledger_arr['createdPCID'] = $itemData['createdPCID'];
        $ledger_arr['createdUserID'] = $itemData['createdUserID'];
        $ledger_arr['createdDateTime'] = $itemData['createdDateTime'];
        $ledger_arr['createdUserName'] = $itemData['createdUserName'];

        return $ledger_arr;
    }

    function get_reserved_batch_list($itemAutoID,$ref_id){

        $company_id = current_companyID();

        $results = $this->db->query("SELECT srp_erp_inventory_itembatch_reserved.id,srp_erp_inventory_itembatch_reserved.batchNumber,srp_erp_inventory_itembatch_reserved.reserved_qty,srp_erp_inventory_itembatch.batchExpireDate
            FROM srp_erp_inventory_itembatch_reserved
            LEFT JOIN srp_erp_inventory_itembatch 
            ON srp_erp_inventory_itembatch_reserved.batchNumber = srp_erp_inventory_itembatch.batchNumber
            AND srp_erp_inventory_itembatch_reserved.itemMasterID = srp_erp_inventory_itembatch.itemMasterID
            WHERE srp_erp_inventory_itembatch_reserved.invoice_num = '$ref_id' 
            AND srp_erp_inventory_itembatch_reserved.itemMasterID = '$itemAutoID' 
            AND srp_erp_inventory_itembatch_reserved.status = 0 
            AND srp_erp_inventory_itembatch_reserved.companyId = '$company_id' ")->result_array();

        return $results;

    }

    function item_ledger_batch($financeYear, $financePeriod, $refCode, $itemData, $repoWac, $wacData, $wareHouseData, $partyData, $isReturn = null)
    {
       
        $item_ledger_arr = array();
        $reserved_batch_list = array();

        //Reserved quantity and batch flow both are on
        $reserved_batch_list = $this->get_reserved_batch_list($itemData['itemAutoID'],$refCode);

        foreach($reserved_batch_list as $batch_details){
            
            if(isset($batch_details['reserved_qty'])){
                $tranQty_um = $batch_details['reserved_qty'] * $itemData['conversionRateUOM'];
            }else{
                $tranQty_um = $itemData['qty'];
            }

            $tranQty = $tranQty_um * ( 1 / $itemData['conversionRateUOM'] );
            //$tranQty = $itemData['qty'];
            
            $ledger_arr = array();
            $ledger_arr['documentID'] = ($isReturn == null) ? 'POS' : 'RET';
            $ledger_arr['documentCode'] = ($isReturn == null) ? 'POS' : 'RET';
            $ledger_arr['documentAutoID'] = $itemData['invoiceID'];
            $ledger_arr['documentSystemCode'] = $refCode;
            $ledger_arr['documentDate'] = date('Y-m-d');
            $ledger_arr['referenceNumber'] = $refCode;
            $ledger_arr['companyFinanceYearID'] = $financeYear['companyFinanceYearID'];
            $ledger_arr['companyFinanceYear'] = $financeYear['beginingDate'] . ' - ' . $financeYear['endingDate'];
            $ledger_arr['FYBegin'] = $financeYear['beginingDate'];
            $ledger_arr['FYEnd'] = $financeYear['endingDate'];
            $ledger_arr['FYPeriodDateFrom'] = $financePeriod['dateFrom'];
            $ledger_arr['FYPeriodDateTo'] = $financePeriod['dateTo'];
            $ledger_arr['wareHouseAutoID'] = $wareHouseData['wareHouseAutoID'];
            $ledger_arr['wareHouseCode'] = $wareHouseData['wareHouseCode'];
            $ledger_arr['wareHouseLocation'] = $wareHouseData['wareHouseLocation'];
            $ledger_arr['wareHouseDescription'] = $wareHouseData['wareHouseDescription'];
            $ledger_arr['itemAutoID'] = $itemData['itemAutoID'];
            $ledger_arr['itemSystemCode'] = $itemData['itemSystemCode'];
            $ledger_arr['itemDescription'] = $itemData['itemDescription'];
            $ledger_arr['defaultUOMID'] = $itemData['defaultUOMID'];
            $ledger_arr['defaultUOM'] = $itemData['defaultUOM'];
            $ledger_arr['transactionUOM'] = $itemData['unitOfMeasure'];
            $ledger_arr['transactionUOMID'] = $itemData['UOMID'];

            if ($isReturn == null) {
                $ledger_arr['transactionQTY'] = ($tranQty_um * -1);
            } else {
                $ledger_arr['transactionQTY'] = $tranQty_um;
            }

            $ledger_arr['convertionRate'] = $itemData['conversionRateUOM'];
            $ledger_arr['currentStock'] = $wacData[2];
            $ledger_arr['companyLocalWacAmount'] = $itemData['wacAmount'];
            $ledger_arr['companyReportingWacAmount'] = $repoWac;


            $ledger_arr['PLGLAutoID'] = $itemData['expenseGLAutoID'];
            $ledger_arr['PLSystemGLCode'] = $itemData['expenseSystemGLCode'];
            $ledger_arr['PLGLCode'] = $itemData['expenseGLCode'];
            $ledger_arr['PLDescription'] = $itemData['expenseGLDescription'];
            $ledger_arr['PLType'] = $itemData['expenseGLType'];

            //batch adding
            $ledger_arr['batchNumber'] = isset($batch_details['batchNumber']) ? $batch_details['batchNumber'] : null;
            $ledger_arr['batchExpireDate'] = isset($batch_details['batchExpireDate']) ? $batch_details['batchExpireDate'] : null;

            $ledger_arr['BLGLAutoID'] = $itemData['assetGLAutoID'];
            $ledger_arr['BLSystemGLCode'] = $itemData['assetSystemGLCode'];
            $ledger_arr['BLGLCode'] = $itemData['assetGLCode'];
            $ledger_arr['BLDescription'] = $itemData['assetGLDescription'];
            $ledger_arr['BLType'] = $itemData['assetGLType'];


            $ledger_arr['transactionCurrencyDecimalPlaces'] = $itemData['transactionCurrencyDecimalPlaces'];
            if ($isReturn == null) {
                $wacMin = $itemData['wacAmount'] * -1;
                $ledger_arr['transactionAmount'] = round(($wacMin * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
            } else {
                $ledger_arr['transactionAmount'] = round(($itemData['wacAmount'] * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
            }

            $itemDiscount = $itemData['discountPer'];
            $ledger_arr['salesPrice'] = ($itemDiscount > 0) ? ($itemData['price'] - ($itemData['price'] * $itemDiscount * 0.01)) : $itemData['price'];
            $ledger_arr['transactionCurrencyID'] = $itemData['transactionCurrencyID'];
            $ledger_arr['transactionCurrency'] = $itemData['transactionCurrency'];
            $ledger_arr['transactionExchangeRate'] = $itemData['transactionExchangeRate'];

            $ledger_arr['companyLocalCurrencyID'] = $itemData['companyLocalCurrencyID'];
            $ledger_arr['companyLocalCurrency'] = $itemData['companyLocalCurrency'];
            $ledger_arr['companyLocalExchangeRate'] = $itemData['companyLocalExchangeRate'];
            $ledger_arr['companyLocalCurrencyDecimalPlaces'] = $itemData['companyLocalCurrencyDecimalPlaces'];
            $ledger_arr['companyLocalAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyLocalExchangeRate']), $itemData['companyLocalCurrencyDecimalPlaces']);

            $ledger_arr['companyReportingCurrencyID'] = $itemData['companyReportingCurrencyID'];
            $ledger_arr['companyReportingCurrency'] = $itemData['companyReportingCurrency'];
            $ledger_arr['companyReportingExchangeRate'] = $itemData['companyReportingExchangeRate'];
            $ledger_arr['companyReportingCurrencyDecimalPlaces'] = $itemData['companyReportingCurrencyDecimalPlaces'];
            $ledger_arr['companyReportingAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyReportingExchangeRate']),
                $itemData['companyReportingCurrencyDecimalPlaces']
            );


            $ledger_arr['partyCurrency'] = $partyData['partyCurrency'];
            $ledger_arr['partyCurrencyExchangeRate'] = $partyData['partyER'];
            $ledger_arr['partyCurrencyDecimalPlaces'] = $partyData['partyDPlaces'];
            $ledger_arr['partyCurrencyAmount'] = round(($ledger_arr['transactionAmount'] / $partyData['partyER']), $partyData['partyDPlaces']);


            $ledger_arr['confirmedYN'] = 1;
            $ledger_arr['confirmedByEmpID'] = $itemData['createdUserID'];
            $ledger_arr['confirmedByName'] = $itemData['createdUserName'];
            $ledger_arr['confirmedDate'] = $itemData['createdDateTime'];
            $ledger_arr['approvedYN'] = 1;
            $ledger_arr['approvedbyEmpID'] = $itemData['createdUserID'];
            $ledger_arr['approvedbyEmpName'] = $itemData['createdUserName'];
            $ledger_arr['approvedDate'] = $itemData['createdDateTime'];
            $ledger_arr['segmentID'] = $wareHouseData['segmentID'];
            $ledger_arr['segmentCode'] = $wareHouseData['segmentCode'];
            $ledger_arr['companyID'] = $itemData['companyID'];
            $ledger_arr['companyCode'] = $itemData['companyCode'];
            $ledger_arr['createdUserGroup'] = $itemData['createdUserGroup'];
            $ledger_arr['createdPCID'] = $itemData['createdPCID'];
            $ledger_arr['createdUserID'] = $itemData['createdUserID'];
            $ledger_arr['createdDateTime'] = $itemData['createdDateTime'];
            $ledger_arr['createdUserName'] = $itemData['createdUserName'];

            try{

                update_batch_reserved_quantity($itemData['itemAutoID'],$ledger_arr['batchNumber'],$refCode,1);

            }catch(\Exception $e){

            }


            $item_ledger_arr[] = $ledger_arr;
        }

        return $item_ledger_arr;
    }

    function item_ledger_customerInvoice($financeYear, $financePeriod, $refCode, $itemData, $repoWac, $wacData, $wareHouseData, $partyData, $isReturn, $customerInvoiceMasterID)
    {
        //$tranQty = $itemData['qty'] * ( 1 / $itemData['conversionRateUOM'] );
        
        $tranQty_um = $itemData['qty'];
        $tranQty = $tranQty_um * ( 1 / $itemData['conversionRateUOM'] );

        $ledger_arr = array();
        $ledger_arr['documentID'] = ($isReturn == null) ? 'CINV' : 'RET';
        $ledger_arr['documentCode'] = ($isReturn == null) ? 'CINV' : 'RET';
        $ledger_arr['documentAutoID'] = $customerInvoiceMasterID;
        $ledger_arr['documentSystemCode'] = $refCode;
        $ledger_arr['documentDate'] = date('Y-m-d');
        $ledger_arr['referenceNumber'] = $refCode;
        $ledger_arr['companyFinanceYearID'] = $financeYear['companyFinanceYearID'];
        $ledger_arr['companyFinanceYear'] = $financeYear['beginingDate'] . ' - ' . $financeYear['endingDate'];
        $ledger_arr['FYBegin'] = $financeYear['beginingDate'];
        $ledger_arr['FYEnd'] = $financeYear['endingDate'];
        $ledger_arr['FYPeriodDateFrom'] = $financePeriod['dateFrom'];
        $ledger_arr['FYPeriodDateTo'] = $financePeriod['dateTo'];
        $ledger_arr['wareHouseAutoID'] = $wareHouseData['wareHouseAutoID'];
        $ledger_arr['wareHouseCode'] = $wareHouseData['wareHouseCode'];
        $ledger_arr['wareHouseLocation'] = $wareHouseData['wareHouseLocation'];
        $ledger_arr['wareHouseDescription'] = $wareHouseData['wareHouseDescription'];
        $ledger_arr['itemAutoID'] = $itemData['itemAutoID'];
        $ledger_arr['itemSystemCode'] = $itemData['itemSystemCode'];
        $ledger_arr['itemDescription'] = $itemData['itemDescription'];
        $ledger_arr['defaultUOMID'] = $itemData['defaultUOMID'];
        $ledger_arr['defaultUOM'] = $itemData['defaultUOM'];
        $ledger_arr['transactionUOM'] = $itemData['unitOfMeasure'];
        $ledger_arr['transactionUOMID'] = $itemData['UOMID'];

        if ($isReturn == null) {
            $ledger_arr['transactionQTY'] = ($tranQty_um * -1);
        } else {
            $ledger_arr['transactionQTY'] = $tranQty_um;
        }

        $ledger_arr['convertionRate'] = $itemData['conversionRateUOM'];
        $ledger_arr['currentStock'] = $wacData[2];
        $ledger_arr['companyLocalWacAmount'] = $itemData['wacAmount'];
        $ledger_arr['companyReportingWacAmount'] = $repoWac;


        $ledger_arr['PLGLAutoID'] = $itemData['expenseGLAutoID'];
        $ledger_arr['PLSystemGLCode'] = $itemData['expenseSystemGLCode'];
        $ledger_arr['PLGLCode'] = $itemData['expenseGLCode'];
        $ledger_arr['PLDescription'] = $itemData['expenseGLDescription'];
        $ledger_arr['PLType'] = $itemData['expenseGLType'];


        $ledger_arr['BLGLAutoID'] = $itemData['assetGLAutoID'];
        $ledger_arr['BLSystemGLCode'] = $itemData['assetSystemGLCode'];
        $ledger_arr['BLGLCode'] = $itemData['assetGLCode'];
        $ledger_arr['BLDescription'] = $itemData['assetGLDescription'];
        $ledger_arr['BLType'] = $itemData['assetGLType'];


        $ledger_arr['transactionCurrencyDecimalPlaces'] = $itemData['transactionCurrencyDecimalPlaces'];
        if ($isReturn == null) {
            $wacMin = $itemData['wacAmount'] * -1;
            $ledger_arr['transactionAmount'] = round(($wacMin * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
        } else {
            $ledger_arr['transactionAmount'] = round(($itemData['wacAmount'] * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
        }

        $itemDiscount = $itemData['discountPer'];
        $ledger_arr['salesPrice'] = ($itemDiscount > 0) ? ($itemData['price'] - ($itemData['price'] * $itemDiscount * 0.01)) : $itemData['price'];
        $ledger_arr['transactionCurrencyID'] = $itemData['transactionCurrencyID'];
        $ledger_arr['transactionCurrency'] = $itemData['transactionCurrency'];
        $ledger_arr['transactionExchangeRate'] = $itemData['transactionExchangeRate'];

        $ledger_arr['companyLocalCurrencyID'] = $itemData['companyLocalCurrencyID'];
        $ledger_arr['companyLocalCurrency'] = $itemData['companyLocalCurrency'];
        $ledger_arr['companyLocalExchangeRate'] = $itemData['companyLocalExchangeRate'];
        $ledger_arr['companyLocalCurrencyDecimalPlaces'] = $itemData['companyLocalCurrencyDecimalPlaces'];
        $ledger_arr['companyLocalAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyLocalExchangeRate']), $itemData['companyLocalCurrencyDecimalPlaces']);

        $ledger_arr['companyReportingCurrencyID'] = $itemData['companyReportingCurrencyID'];
        $ledger_arr['companyReportingCurrency'] = $itemData['companyReportingCurrency'];
        $ledger_arr['companyReportingExchangeRate'] = $itemData['companyReportingExchangeRate'];
        $ledger_arr['companyReportingCurrencyDecimalPlaces'] = $itemData['companyReportingCurrencyDecimalPlaces'];
        $ledger_arr['companyReportingAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyReportingExchangeRate']),
            $itemData['companyReportingCurrencyDecimalPlaces']
        );


        $ledger_arr['partyCurrency'] = $partyData['partyCurrency'];
        $ledger_arr['partyCurrencyExchangeRate'] = $partyData['partyER'];
        $ledger_arr['partyCurrencyDecimalPlaces'] = $partyData['partyDPlaces'];
        $ledger_arr['partyCurrencyAmount'] = round(($ledger_arr['transactionAmount'] / $partyData['partyER']), $partyData['partyDPlaces']);


        $ledger_arr['confirmedYN'] = 1;
        $ledger_arr['confirmedByEmpID'] = $itemData['createdUserID'];
        $ledger_arr['confirmedByName'] = $itemData['createdUserName'];
        $ledger_arr['confirmedDate'] = $itemData['createdDateTime'];
        $ledger_arr['approvedYN'] = 1;
        $ledger_arr['approvedbyEmpID'] = $itemData['createdUserID'];
        $ledger_arr['approvedbyEmpName'] = $itemData['createdUserName'];
        $ledger_arr['approvedDate'] = $itemData['createdDateTime'];
        $ledger_arr['segmentID'] = $wareHouseData['segmentID'];
        $ledger_arr['segmentCode'] = $wareHouseData['segmentCode'];
        $ledger_arr['companyID'] = $itemData['companyID'];
        $ledger_arr['companyCode'] = $itemData['companyCode'];
        $ledger_arr['createdUserGroup'] = $itemData['createdUserGroup'];
        $ledger_arr['createdPCID'] = $itemData['createdPCID'];
        $ledger_arr['createdUserID'] = $itemData['createdUserID'];
        $ledger_arr['createdDateTime'] = $itemData['createdDateTime'];
        $ledger_arr['createdUserName'] = $itemData['createdUserName'];

        return $ledger_arr;
    }

    function item_ledger_customerInvoice_batch($financeYear, $financePeriod, $refCode, $itemData, $repoWac, $wacData, $wareHouseData, $partyData, $isReturn, $customerInvoiceMasterID,$ref_sequence_code)
    {
        $item_ledger_arr = array();
        $reserved_batch_list = array();

        //Reserved quantity and batch flow both are on
        $reserved_batch_list = $this->get_reserved_batch_list($itemData['itemAutoID'],$ref_sequence_code);

        foreach($reserved_batch_list as $batch_details){

            if(isset($batch_details['reserved_qty'])){
                $tranQty_um = $batch_details['reserved_qty'] * $itemData['conversionRateUOM'];
            }else{
                $tranQty_um = $itemData['qty'];
            }

            $tranQty = $tranQty_um * ( 1 / $itemData['conversionRateUOM'] );

            $ledger_arr = array();
            $ledger_arr['documentID'] = ($isReturn == null) ? 'CINV' : 'RET';
            $ledger_arr['documentCode'] = ($isReturn == null) ? 'CINV' : 'RET';
            $ledger_arr['documentAutoID'] = $customerInvoiceMasterID;
            $ledger_arr['documentSystemCode'] = $refCode;
            $ledger_arr['documentDate'] = date('Y-m-d');
            $ledger_arr['referenceNumber'] = $refCode;
            $ledger_arr['companyFinanceYearID'] = $financeYear['companyFinanceYearID'];
            $ledger_arr['companyFinanceYear'] = $financeYear['beginingDate'] . ' - ' . $financeYear['endingDate'];
            $ledger_arr['FYBegin'] = $financeYear['beginingDate'];
            $ledger_arr['FYEnd'] = $financeYear['endingDate'];
            $ledger_arr['FYPeriodDateFrom'] = $financePeriod['dateFrom'];
            $ledger_arr['FYPeriodDateTo'] = $financePeriod['dateTo'];
            $ledger_arr['wareHouseAutoID'] = $wareHouseData['wareHouseAutoID'];
            $ledger_arr['wareHouseCode'] = $wareHouseData['wareHouseCode'];
            $ledger_arr['wareHouseLocation'] = $wareHouseData['wareHouseLocation'];
            $ledger_arr['wareHouseDescription'] = $wareHouseData['wareHouseDescription'];
            $ledger_arr['itemAutoID'] = $itemData['itemAutoID'];
            $ledger_arr['itemSystemCode'] = $itemData['itemSystemCode'];
            $ledger_arr['itemDescription'] = $itemData['itemDescription'];
            $ledger_arr['defaultUOMID'] = $itemData['defaultUOMID'];
            $ledger_arr['defaultUOM'] = $itemData['defaultUOM'];
            $ledger_arr['transactionUOM'] = $itemData['unitOfMeasure'];
            $ledger_arr['transactionUOMID'] = $itemData['UOMID'];

            if ($isReturn == null) {
                $ledger_arr['transactionQTY'] = ($tranQty_um * -1);
            } else {
                $ledger_arr['transactionQTY'] = $tranQty_um;
            }

            $ledger_arr['convertionRate'] = $itemData['conversionRateUOM'];
            $ledger_arr['currentStock'] = $wacData[2];
            $ledger_arr['companyLocalWacAmount'] = $itemData['wacAmount'];
            $ledger_arr['companyReportingWacAmount'] = $repoWac;


            $ledger_arr['PLGLAutoID'] = $itemData['expenseGLAutoID'];
            $ledger_arr['PLSystemGLCode'] = $itemData['expenseSystemGLCode'];
            $ledger_arr['PLGLCode'] = $itemData['expenseGLCode'];
            $ledger_arr['PLDescription'] = $itemData['expenseGLDescription'];
            $ledger_arr['PLType'] = $itemData['expenseGLType'];

            //batch adding
            $ledger_arr['batchNumber'] = isset($batch_details['batchNumber']) ? $batch_details['batchNumber'] : null;
            $ledger_arr['batchExpireDate'] = isset($batch_details['batchExpireDate']) ? $batch_details['batchExpireDate'] : null;


            $ledger_arr['BLGLAutoID'] = $itemData['assetGLAutoID'];
            $ledger_arr['BLSystemGLCode'] = $itemData['assetSystemGLCode'];
            $ledger_arr['BLGLCode'] = $itemData['assetGLCode'];
            $ledger_arr['BLDescription'] = $itemData['assetGLDescription'];
            $ledger_arr['BLType'] = $itemData['assetGLType'];


            $ledger_arr['transactionCurrencyDecimalPlaces'] = $itemData['transactionCurrencyDecimalPlaces'];
            if ($isReturn == null) {
                $wacMin = $itemData['wacAmount'] * -1;
                $ledger_arr['transactionAmount'] = round(($wacMin * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
            } else {
                $ledger_arr['transactionAmount'] = round(($itemData['wacAmount'] * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
            }

            $itemDiscount = $itemData['discountPer'];
            $ledger_arr['salesPrice'] = ($itemDiscount > 0) ? ($itemData['price'] - ($itemData['price'] * $itemDiscount * 0.01)) : $itemData['price'];
            $ledger_arr['transactionCurrencyID'] = $itemData['transactionCurrencyID'];
            $ledger_arr['transactionCurrency'] = $itemData['transactionCurrency'];
            $ledger_arr['transactionExchangeRate'] = $itemData['transactionExchangeRate'];

            $ledger_arr['companyLocalCurrencyID'] = $itemData['companyLocalCurrencyID'];
            $ledger_arr['companyLocalCurrency'] = $itemData['companyLocalCurrency'];
            $ledger_arr['companyLocalExchangeRate'] = $itemData['companyLocalExchangeRate'];
            $ledger_arr['companyLocalCurrencyDecimalPlaces'] = $itemData['companyLocalCurrencyDecimalPlaces'];
            $ledger_arr['companyLocalAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyLocalExchangeRate']), $itemData['companyLocalCurrencyDecimalPlaces']);

            $ledger_arr['companyReportingCurrencyID'] = $itemData['companyReportingCurrencyID'];
            $ledger_arr['companyReportingCurrency'] = $itemData['companyReportingCurrency'];
            $ledger_arr['companyReportingExchangeRate'] = $itemData['companyReportingExchangeRate'];
            $ledger_arr['companyReportingCurrencyDecimalPlaces'] = $itemData['companyReportingCurrencyDecimalPlaces'];
            $ledger_arr['companyReportingAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyReportingExchangeRate']),
                $itemData['companyReportingCurrencyDecimalPlaces']
            );


            $ledger_arr['partyCurrency'] = $partyData['partyCurrency'];
            $ledger_arr['partyCurrencyExchangeRate'] = $partyData['partyER'];
            $ledger_arr['partyCurrencyDecimalPlaces'] = $partyData['partyDPlaces'];
            $ledger_arr['partyCurrencyAmount'] = round(($ledger_arr['transactionAmount'] / $partyData['partyER']), $partyData['partyDPlaces']);


            $ledger_arr['confirmedYN'] = 1;
            $ledger_arr['confirmedByEmpID'] = $itemData['createdUserID'];
            $ledger_arr['confirmedByName'] = $itemData['createdUserName'];
            $ledger_arr['confirmedDate'] = $itemData['createdDateTime'];
            $ledger_arr['approvedYN'] = 1;
            $ledger_arr['approvedbyEmpID'] = $itemData['createdUserID'];
            $ledger_arr['approvedbyEmpName'] = $itemData['createdUserName'];
            $ledger_arr['approvedDate'] = $itemData['createdDateTime'];
            $ledger_arr['segmentID'] = $wareHouseData['segmentID'];
            $ledger_arr['segmentCode'] = $wareHouseData['segmentCode'];
            $ledger_arr['companyID'] = $itemData['companyID'];
            $ledger_arr['companyCode'] = $itemData['companyCode'];
            $ledger_arr['createdUserGroup'] = $itemData['createdUserGroup'];
            $ledger_arr['createdPCID'] = $itemData['createdPCID'];
            $ledger_arr['createdUserID'] = $itemData['createdUserID'];
            $ledger_arr['createdDateTime'] = $itemData['createdDateTime'];
            $ledger_arr['createdUserName'] = $itemData['createdUserName'];

            //update and confirmed batch utilization
            try{

                update_batch_reserved_quantity($itemData['itemAutoID'],$ledger_arr['batchNumber'],$ref_sequence_code,1);

            }catch(\Exception $e){

            }

            $item_ledger_arr[] = $ledger_arr;
        }
        
        return $item_ledger_arr;
    }



    function double_entry($invID, $partyData, $creditSalesAmount, $customerInvoiceMasterID = null,$wareHousAutoID = null)
    {
        if($wareHousAutoID){
            $wareHouseID = $wareHousAutoID;
        }else{
            $wareHouseID = $this->common_data['ware_houseID'];
        }
       
        $partyID = $partyData['cusID'];
        $partyName = $partyData['cusName'];
        $partySysCode = $partyData['sysCode'];
        $partyCurrencyID = 0;
        $partyCurrency = $partyData['partyCurrency'];
        $partyER = $partyData['partyER'];
        $partyDP = $partyData['partyDPlaces'];

        if ($creditSalesAmount > 0) {
            $documentid = 'CINV';
            $documentMasterAutoID = $customerInvoiceMasterID;
            $exceedjoinID = $customerInvoiceMasterID;
            $exceedjoin = 'cinvm.invoiceAutoID';
        } else {
            $documentid = 'POS';
            $documentMasterAutoID = 'inv.invoiceID';
            $exceedjoinID = $invID;
            $exceedjoin = 'inv.invoiceID';
        }

        /************** EXPENSE GL DEBIT *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID,wareHouseAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID,
                         systemGLCode, GLCode, GLDescription,
                         GLType, amount_type, transactionAmount, transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces,
                         companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency,  companyLocalExchangeRate, companyLocalCurrencyDecimalPlaces,
                         companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,
                         partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate, partyCurrencyAmount, partyCurrencyDecimalPlaces,
                         confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode,
                         createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName)
                         SELECT '{$documentid}', $documentMasterAutoID as invoiceID,$wareHouseID as wareid, documentSystemCode, invoiceDate, DATE_FORMAT(invoiceDate,'%Y'), DATE_FORMAT(invoiceDate,'%m'),
                         det.expenseGLAutoID, det.expenseSystemGLCode, det.expenseGLCode, det.expenseGLDescription, det.expenseGLType, 'dr',
                         ROUND( sum(wacAmount * qty), det.transactionCurrencyDecimalPlaces ), det.transactionCurrencyID, det.transactionCurrency, det.transactionExchangeRate,
                         det.transactionCurrencyDecimalPlaces, ROUND(sum( (wacAmount * qty) / det.companyLocalExchangeRate), det.companyLocalCurrencyDecimalPlaces),
                         det.companyLocalCurrencyID, det.companyLocalCurrency, det.companyLocalExchangeRate, det.companyLocalCurrencyDecimalPlaces,
                         ROUND( sum( (wacAmount * qty) / det.companyReportingExchangeRate), det.companyReportingCurrencyDecimalPlaces), det.companyReportingCurrencyID,
                         det.companyReportingCurrency, det.companyReportingExchangeRate, det.companyReportingCurrencyDecimalPlaces,
                         {$partyID}, '{$partySysCode}', '{$partyName}', {$partyCurrencyID}, '{$partyCurrency}',  {$partyER} , ROUND( sum( (wacAmount * qty) / {$partyER}),
                         {$partyDP}),  {$partyDP}, inv.createdUserID, inv.createdUserName, inv.createdDateTime, inv.createdDateTime, inv.createdUserID, inv.createdUserName,
                         inv.segmentID, inv.segmentCode, inv.companyID, inv.companyCode, inv.createdUserGroup, inv.createdPCID, inv.createdUserID, inv.createdDateTime,
                         inv.createdUserName
                         FROM srp_erp_pos_invoicedetail det JOIN srp_erp_pos_invoice inv ON inv.invoiceID = det.invoiceID
                         WHERE det.invoiceID ={$invID} AND financeCategory=1 GROUP BY expenseGLAutoID");

     

        /************** ASSET GL CREDIT *************/
        //        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID,wareHouseAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID,
        //                         systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount, transactionCurrencyID, transactionCurrency, transactionExchangeRate,
        //                         transactionCurrencyDecimalPlaces, companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate,
        //                         companyLocalCurrencyDecimalPlaces,  companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingExchangeRate,
        //                         companyReportingCurrencyDecimalPlaces, partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate,
        //                         partyCurrencyAmount, partyCurrencyDecimalPlaces, confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName,
        //                         segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName)
        //                         SELECT '{$documentid}', $documentMasterAutoID as invoiceID,$wareHouseID as wareid, documentSystemCode, inv.invoiceDate, DATE_FORMAT(inv.invoiceDate,'%Y'), DATE_FORMAT(inv.invoiceDate,'%m'),
        //                         det.assetGLAutoID, det.assetSystemGLCode, det.assetGLCode, det.assetGLDescription, det.assetGLType, 'cr',
        //                         ROUND( IFNULL(sum((((wacAmount * qty))-IFNULL(exceed.exccedTransAmnt,0)) *- 1),0), det.transactionCurrencyDecimalPlaces ) as transamnt,det.transactionCurrencyID, det.transactionCurrency,
        //                         det.transactionExchangeRate, det.transactionCurrencyDecimalPlaces, ROUND( sum(((((wacAmount * qty)/ det.companyLocalExchangeRate))-IFNULL(exceed.exccedLocAmnt,0)) *- 1), det.companyLocalCurrencyDecimalPlaces ), det.companyLocalCurrencyID, det.companyLocalCurrency, det.companyLocalExchangeRate,
        //                         det.companyLocalCurrencyDecimalPlaces, ROUND( sum( ((((wacAmount * qty)/ det.companyReportingExchangeRate))-IFNULL(exceed.exccedRepAmnt,0)) *- 1 ), det.companyReportingCurrencyDecimalPlaces ), det.companyReportingCurrencyID, det.companyReportingCurrency, det.companyReportingExchangeRate,
        //                         det.companyReportingCurrencyDecimalPlaces, {$partyID}, '{$partySysCode}', '{$partyName}', {$partyCurrencyID}, '{$partyCurrency}',  {$partyER} ,
        //                         ROUND( sum( (wacAmount * qty *-1) / {$partyER}), {$partyDP}),  {$partyDP},
        //                         inv.createdUserID, inv.createdUserName, inv.createdDateTime, inv.createdDateTime, inv.createdUserID, inv.createdUserName, inv.segmentID,
        //                         inv.segmentCode, inv.companyID, inv.companyCode, inv.createdUserGroup, inv.createdPCID, inv.createdUserID, inv.createdDateTime, inv.createdUserName
        //                         FROM srp_erp_pos_invoicedetail det JOIN srp_erp_pos_invoice inv ON inv.invoiceID = det.invoiceID
        //                         LEFT JOIN srp_erp_customerinvoicemaster cinvm ON cinvm.posMasterAutoID = inv.invoiceID
        //                          LEFT JOIN ( SELECT documentAutoID,itemAutoID, SUM(transactionAmount) AS exccedTransAmnt, SUM(companyLocalAmount) AS exccedLocAmnt, SUM(companyReportingAmount) AS exccedRepAmnt FROM srp_erp_itemexceeded WHERE documentAutoID = {$exceedjoinID} AND (documentCode = 'POS' OR documentCode = 'CINV') GROUP BY documentAutoID ) exceed ON exceed.documentAutoID = $exceedjoin and exceed.itemAutoID = det.itemAutoID
        //                         WHERE det.invoiceID ={$invID} AND financeCategory=1 GROUP BY assetGLAutoID HAVING transamnt<0");

        //Asset GL General ledger record new query.
        if ($customerInvoiceMasterID != null) {
            //this is a credit sale thus need to pass customer invoice id to the query.
            $joinAndWhere = "JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID=srp_erp_itemledger.documentAutoID
                                JOIN srp_erp_pos_invoice ON srp_erp_pos_invoice.invoiceID=srp_erp_customerinvoicemaster.posMasterAutoID
                                JOIN (SELECT * FROM srp_erp_pos_invoicedetail WHERE srp_erp_pos_invoicedetail.invoiceID = $invID GROUP BY srp_erp_pos_invoicedetail.assetGLAutoID) srp_erp_pos_invoicedetail
                                WHERE 
                                srp_erp_itemledger.documentCode='CINV' and srp_erp_itemledger.documentAutoID=$customerInvoiceMasterID";
            $documentCode = 'CINV';
            $documentAutoID = $customerInvoiceMasterID;
        } else {
            //pass pos_invoice id to the query.
            $joinAndWhere = "JOIN srp_erp_pos_invoice ON srp_erp_pos_invoice.invoiceID = srp_erp_itemledger.documentAutoID
                            JOIN (SELECT * FROM srp_erp_pos_invoicedetail WHERE srp_erp_pos_invoicedetail.invoiceID = $invID GROUP BY srp_erp_pos_invoicedetail.assetGLAutoID) srp_erp_pos_invoicedetail
                            WHERE
                            srp_erp_itemledger.documentCode = 'POS' 
                            AND srp_erp_itemledger.documentAutoID = $invID";
            $documentCode = 'POS';
            $documentAutoID = 'srp_erp_pos_invoice.invoiceID';
        }
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID,wareHouseAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID,
                         systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount, transactionCurrencyID, transactionCurrency, transactionExchangeRate,
                         transactionCurrencyDecimalPlaces, companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate,
                         companyLocalCurrencyDecimalPlaces,  companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingExchangeRate,
                         companyReportingCurrencyDecimalPlaces, partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate,
                         partyCurrencyAmount, partyCurrencyDecimalPlaces, confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName,
                         segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName)
                         SELECT
                            '{$documentCode}',
                            $documentAutoID,
                            srp_erp_pos_invoice.wareHouseAutoID,
                            srp_erp_pos_invoice.documentSystemCode,
                            srp_erp_pos_invoice.invoiceDate,
                            DATE_FORMAT( srp_erp_pos_invoice.invoiceDate, '%Y' ),
                            DATE_FORMAT( srp_erp_pos_invoice.invoiceDate, '%m' ),
                            srp_erp_pos_invoicedetail.assetGLAutoID,
                            srp_erp_pos_invoicedetail.assetSystemGLCode,
                            srp_erp_pos_invoicedetail.assetGLCode,
                            srp_erp_pos_invoicedetail.assetGLDescription,
                            srp_erp_pos_invoicedetail.assetGLType,
                            'cr',
                            sum( srp_erp_itemledger.transactionAmount ) AS transsactionamount,
                            srp_erp_itemledger.transactionCurrencyID,
                            srp_erp_itemledger.transactionCurrency,
                            srp_erp_itemledger.transactionExchangeRate,
                            srp_erp_itemledger.transactionCurrencyDecimalPlaces,
                            sum( srp_erp_itemledger.companyLocalAmount ) AS companyLocalAmount,
                            srp_erp_pos_invoicedetail.companyLocalCurrencyID,
                            srp_erp_pos_invoicedetail.companyLocalCurrency,
                            srp_erp_pos_invoicedetail.companyLocalExchangeRate,
                            srp_erp_pos_invoicedetail.companyLocalCurrencyDecimalPlaces,
                            sum( srp_erp_itemledger.companyReportingAmount ) AS companyReportingAmount,
                            srp_erp_pos_invoicedetail.companyReportingCurrencyID,
                            srp_erp_pos_invoicedetail.companyReportingCurrency,
                            srp_erp_pos_invoicedetail.companyReportingExchangeRate,
                            srp_erp_pos_invoicedetail.companyReportingCurrencyDecimalPlaces,
                            {$partyID},
                            '{$partySysCode}', 
                            '{$partyName}', 
                            {$partyCurrencyID}, 
                            '{$partyCurrency}',  
                            {$partyER} ,
                            ROUND( sum( (srp_erp_pos_invoicedetail.wacAmount * srp_erp_pos_invoicedetail.qty *-1) / {$partyER})), 
                            {$partyDP}, 
                            srp_erp_itemledger.confirmedByEmpID,
                            srp_erp_itemledger.confirmedByName,
                            srp_erp_itemledger.confirmedDate,
                            srp_erp_itemledger.approvedDate,
                            srp_erp_itemledger.approvedbyEmpID,
                            srp_erp_itemledger.approvedbyEmpName,
                            srp_erp_pos_invoice.segmentID,
                            srp_erp_pos_invoice.segmentCode,
                            srp_erp_pos_invoice.companyID,
                            srp_erp_pos_invoice.companyCode,
                            srp_erp_pos_invoice.createdUserGroup,
                            srp_erp_pos_invoice.createdPCID,
                            srp_erp_pos_invoice.createdUserID,
                            srp_erp_pos_invoice.createdDateTime,
                            srp_erp_pos_invoice.createdUserName 
                        FROM
                            srp_erp_itemledger
                            $joinAndWhere
                        GROUP BY
                            BLGLAutoID");

     
        //var_dump($this->db->last_query());
        /************** Revenue GL CREDIT *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID,wareHouseAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID,
                          systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount, transactionCurrencyID, transactionCurrency, transactionExchangeRate,
                          transactionCurrencyDecimalPlaces, companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate,
                          companyLocalCurrencyDecimalPlaces,  companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingExchangeRate,
                          companyReportingCurrencyDecimalPlaces, partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate,
                          partyCurrencyAmount, partyCurrencyDecimalPlaces, confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName,
                          segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName)
                         SELECT '{$documentid}', $documentMasterAutoID as invoiceID,$wareHouseID as wareid, documentSystemCode, invoiceDate, DATE_FORMAT(invoiceDate,'%Y'), DATE_FORMAT(invoiceDate,'%m'),
                         det.revenueGLAutoID, det.revenueSystemGLCode, det.revenueGLCode, det.revenueGLDescription, det.revenueGLType, 'cr',
                         ROUND( sum(det.transactionAmount *-1), det.transactionCurrencyDecimalPlaces ), det.transactionCurrencyID, det.transactionCurrency,
                         det.transactionExchangeRate, det.transactionCurrencyDecimalPlaces, ROUND( sum(det.companyLocalAmount *-1), det.companyLocalCurrencyDecimalPlaces),
                         det.companyLocalCurrencyID, det.companyLocalCurrency, det.companyLocalExchangeRate, det.companyLocalCurrencyDecimalPlaces,
                         ROUND( sum(det.companyReportingAmount *-1), det.companyReportingCurrencyDecimalPlaces), det.companyReportingCurrencyID, det.companyReportingCurrency,
                         det.companyReportingExchangeRate, det.companyReportingCurrencyDecimalPlaces, {$partyID}, '{$partySysCode}', '{$partyName}', {$partyCurrencyID},
                         '{$partyCurrency}',  {$partyER} , ROUND( sum(det.companyLocalAmount /-1) / {$partyER}, {$partyDP}),  {$partyDP},
                         inv.createdUserID, inv.createdUserName, inv.createdDateTime, inv.createdDateTime, inv.createdUserID, inv.createdUserName, inv.segmentID,
                         inv.segmentCode, inv.companyID, inv.companyCode, inv.createdUserGroup, inv.createdPCID, inv.createdUserID, inv.createdDateTime, inv.createdUserName
                         FROM srp_erp_pos_invoicedetail det JOIN srp_erp_pos_invoice inv ON inv.invoiceID = det.invoiceID
                         WHERE det.invoiceID ={$invID} GROUP BY revenueGLAutoID");
          
        //        $query = $this->db->query("select * from srp_erp_itemexceeded where documentAutoID=$invID");
        //        foreach ($query->result() as $exceededItem) {
        //            $companyID = $this->common_data['company_data']['company_id'];
        //            $exceedglid = $this->db->query("SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE companyID =$companyID AND controlAccountType = 'IEXC'")->row_array();
        //            $exceedGlAutoID = $exceedglid['GLAutoID'];
        //            $exceedGlDesc = fetch_gl_account_desc($exceedGlAutoID);
        //            $invoice = $this->db->query("select * from srp_erp_pos_invoice where invoiceID=$invID")->row();
        //            $itemExceedGL = array(
        //                "wareHouseAutoID" => $wareHouseID,
        //                "documentCode" => $documentid,
        //                "documentMasterAutoID" => $invID,
        //                "documentSystemCode" => $invoice->documentSystemCode,
        //                "documentNarration" => "POS Sales - Exceeded",
        //                "documentDate" => current_date(),
        //                "documentYear" => date("Y", strtotime(current_date())),
        //                "documentMonth" => date("m", strtotime(current_date())),
        //                "projectExchangeRate" => "",
        //                "GLAutoID" => $exceedGlAutoID,
        //                "systemGLCode" => $exceedGlDesc['systemAccountCode'],
        //                "GLCode" => $exceedGlDesc['GLSecondaryCode'],
        //                "GLDescription" => $exceedGlDesc['GLDescription'],
        //                "GLType" => $exceedGlDesc['subCategory'],
        //                "amount_type" => "cr",
        //                "isFromItem" => "",
        //                "transactionCurrencyID" => $invoice->transactionCurrencyID,
        //                "transactionCurrency" => $invoice->transactionCurrency,
        //                "transactionExchangeRate" => $invoice->transactionExchangeRate,
        //                "transactionAmount" => ROUND((-$exceededItem->transactionAmount), $invoice->transactionCurrencyDecimalPlaces),
        //                "transactionCurrencyDecimalPlaces" => $invoice->transactionCurrencyDecimalPlaces,
        //                "companyLocalCurrencyID" => $invoice->companyLocalCurrencyID,
        //                "companyLocalCurrency" => $invoice->companyLocalCurrency,
        //                "companyLocalExchangeRate" => $invoice->companyLocalExchangeRate,
        //                "companyLocalAmount" => $exceededItem->companyLocalAmount,
        //                "companyLocalCurrencyDecimalPlaces" => $invoice->companyLocalCurrencyDecimalPlaces,
        //                "companyReportingCurrencyID" => $invoice->companyReportingCurrencyID,
        //                "companyReportingCurrency" => $invoice->companyReportingCurrency,
        //                "companyReportingExchangeRate" => $invoice->companyReportingExchangeRate,
        //                "companyReportingAmount" => $exceededItem->companyReportingAmount,
        //                "companyReportingCurrencyDecimalPlaces" => $invoice->companyReportingCurrencyDecimalPlaces,
        //                "partyAutoID" => $partyID,
        //                "partySystemCode" => $partySysCode,
        //                "partyName" => $partyName,
        //                "partyCurrencyID" => $partyCurrencyID,
        //                "partyCurrency" => $partyCurrency,
        //                "partyExchangeRate" => $partyER,
        //                "partyCurrencyAmount" => "",
        //                "partyCurrencyDecimalPlaces" => "",
        //                "is_sync" => "",
        //                "id_store" => "",
        //                "isAddon" => "",
        //                "confirmedByEmpID" => "",
        //                "confirmedByName" => "",
        //                "confirmedDate" => "",
        //                "approvedDate" => "",
        //                "approvedbyEmpID" => "",
        //                "approvedbyEmpName" => "",
        //                "segmentID" => $invoice->segmentID,
        //                "segmentCode" => $invoice->segmentCode,
        //                "companyID" => $invoice->companyID,
        //                "companyCode" => $invoice->companyCode,
        //                "createdUserGroup" => $this->common_data['user_group'],
        //                "createdPCID" => $this->common_data['current_pc'],
        //                "createdUserID" => $this->common_data['current_userID'],
        //                "createdDateTime" => current_date(),
        //                "createdUserName" => $this->common_data['current_user']
        //            );
        //            $this->db->insert('srp_erp_generalledger', $itemExceedGL);
        //        }


        /************** Group Based Tax Start *************/

        $this->db->query("INSERT INTO srp_erp_generalledger (
                                   documentCode, 
                                   documentMasterAutoID,
                                   wareHouseAutoID,
                                   documentSystemCode, 
                                   documentDate, 
                                   documentYear, 
                                   documentMonth,
                                   GLAutoID,
                                   systemGLCode, 
                                   GLCode, 
                                   GLDescription, 
                                   GLType, 
                                   amount_type, 
                                   transactionAmount, 
                                   transactionCurrencyID, 
                                   transactionCurrency, 
                                   transactionExchangeRate,
                                   transactionCurrencyDecimalPlaces, 
                                   companyLocalAmount, 
                                   companyLocalCurrencyID, 
                                   companyLocalCurrency, 
                                   companyLocalExchangeRate,
                                   companyLocalCurrencyDecimalPlaces,  
                                   companyReportingAmount, 
                                   companyReportingCurrencyID, 
                                   companyReportingCurrency, 
                                   companyReportingExchangeRate,
                                   companyReportingCurrencyDecimalPlaces, 
                                   partyAutoID, 
                                   partySystemCode, 
                                   partyName, 
                                   partyCurrencyID, 
                                   partyCurrency, 
                                   partyExchangeRate,
                                   partyCurrencyAmount, 
                                   partyCurrencyDecimalPlaces, 
                                   segmentID, 
                                   segmentCode, 
                                   companyID, 
                                   companyCode, 
                                   createdUserGroup, 
                                   createdPCID, 
                                   createdUserID, 
                                   createdDateTime, 
                                   createdUserName )
                                   SELECT
                                    '{$documentid}', 
                                    $documentMasterAutoID as invoiceID,
                                    inv.wareHouseAutoID,
                                    documentSystemCode,
                                    inv.invoiceDate AS documentDate,
                                    DATE_FORMAT( inv.invoiceDate, '%Y' ) AS documentYear,
                                    DATE_FORMAT( inv.invoiceDate, '%m' ) AS documentMonth,
                                    gl_auto_id AS GLAutoID,
                                    chartOfAC.systemAccountCode AS systemGLCode,
                                    chartOfAC.GLSecondaryCode AS GLCode,
                                    chartOfAC.GLDescription AS GLDescription,
                                    chartOfAC.subCategory AS GLType,
                                    'cr' AS amount_type,
                                    (taxledger.taxAmount)*-1 AS transactionamount,
                                    inv.transactionCurrencyID,
                                    inv.transactionCurrency,
                                    inv.transactionExchangeRate,
                                    inv.transactionCurrencyDecimalPlaces,
                                    ( taxledger.taxAmount / inv.companyLocalExchangeRate )*-1 AS companyLocalAmount,
                                    inv.companyLocalCurrencyID,
                                    inv.companyLocalCurrency,
                                    inv.companyLocalExchangeRate,
                                    inv.companyLocalCurrencyDecimalPlaces,
                                    ( taxledger.taxAmount / inv.companyReportingExchangeRate )*-1 AS companyReportingAmount,
                                    inv.companyReportingCurrencyID,
                                    inv.companyReportingCurrency,
                                    inv.companyReportingExchangeRate,
                                    inv.companyReportingCurrencyDecimalPlaces,                                    
                                     {$partyID},
                                    '{$partySysCode}',
                                    '{$partyName}',
                                     {$partyCurrencyID},
                                    '{$partyCurrency}',
                                     {$partyER},
                                     ROUND((((taxledger.taxAmount)*-1)/{$partyER}),{$partyDP}),
                                     {$partyDP},
	                                inv.segmentID,
	                                inv.segmentCode,
	                                inv.companyID,
	                                inv.companyCode,
	                                inv.createdUserGroup,
	                                inv.createdPCID,
	                                inv.createdUserID,
	                                inv.createdDateTime,
	                                inv.createdUserName 
                                    FROM (
                                            SELECT
		                                    documentDetailAutoID AS auto_id,
		                                    documentMasterAutoID AS documentMasterAutoID,
		                                    taxGlAutoID AS gl_auto_id,
		                                    SUM( amount ) AS taxAmount,
		                                    taxMasterID 
	                                        FROM
		                                    `srp_erp_taxledger`
		                                    LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
	                                        WHERE
		                                    documentID = 'GPOS' 
		                                    AND documentMasterAutoID = {$invID} 
	                                        GROUP BY
		                                    taxMasterID,
		                                    taxGlAutoID ) taxledger
	                                        LEFT JOIN srp_erp_chartofaccounts chartOfAC ON chartOfAC.GLAutoID = taxledger.gl_auto_id
	                                        LEFT JOIN srp_erp_pos_invoicedetail posInvoiceDetail ON posInvoiceDetail.invoiceDetailsID = taxledger.auto_id
	                                        LEFT JOIN srp_erp_pos_invoice inv ON inv.invoiceID = posInvoiceDetail.invoiceID");
        /************** Group Based Tax End *************/


        /************** BANK / CUSTOMER GL DEBIT *************/
        $data = $this->db->query("SELECT documentCode, invoiceID, documentSystemCode,invoiceCode, invoiceDate, customerID, DATE_FORMAT(invoiceDate,'%Y') e_year,
                                  DATE_FORMAT(invoiceDate,'%m') e_month, 'cr' amountType, cashAmount, chequeAmount, cardAmount, creditNoteAmount, cardBank, creditSalesAmount,
                                  chequeNo, netTotal, transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces,
                                  ROUND((netTotal / companyLocalExchangeRate), companyLocalCurrencyDecimalPlaces) localAmount, companyLocalCurrencyID, companyLocalCurrency,
                                  companyLocalExchangeRate, companyLocalCurrencyDecimalPlaces,
                                  ROUND((netTotal / companyReportingExchangeRate), companyReportingCurrencyDecimalPlaces) reportAmount, companyReportingCurrencyID,
                                  companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces, createdUserGroup, createdPCID,
                                  createdUserID, createdUserName, createdDateTime, segmentID, segmentCode, companyID, companyCode
                                  FROM srp_erp_pos_invoice inv
                                  WHERE invoiceID ={$invID}")->row();

        //print_r($data); exit;

        $cusinvoice = $this->db->query("SELECT invoiceCode,invoiceAutoID
                                  FROM srp_erp_customerinvoicemaster
                                  WHERE posMasterAutoID ={$invID} AND posTypeID=1")->row_array();
                                  
        $cash = $data->cashAmount;
        $cheque = $data->chequeAmount;
        $card = $data->cardAmount;
        $creditNote = $data->creditNoteAmount;

        $baninvoicePayments = $this->db->query("SELECT
                                    PaymentID,
                                    invoiceID,
                                    paymentConfigMasterID,
                                    paymentConfigDetailID,
                                    glAccountType,
                                    GLCode,
                                    SUM(amount) as amount,
                                    reference,
                                    customerAutoID
                                FROM
                                    srp_erp_pos_invoicepayments
                                WHERE
                                    invoiceID = $invID
                                GROUP BY GLCode")->result_array();
        if (!empty($cusinvoice)) {
            $docsyscode = $cusinvoice['invoiceCode'];
            $docsysid = $cusinvoice['invoiceAutoID'];
        } else {
            $docsyscode = $data->documentSystemCode;
            $docsysid = $data->invoiceID;
        }

        $GL_Data = array(
            'documentMasterAutoID' => $docsysid,
            'documentCode' => $documentid,
            'wareHouseAutoID' => $wareHouseID,
            'documentSystemCode' => $docsyscode,
            'documentDate' => $data->invoiceDate,
            'documentYear' => $data->e_year,
            'documentMonth' => $data->e_month,
            'amount_type' => 'dr',

            'transactionCurrencyID' => $data->transactionCurrencyID,
            'transactionCurrency' => $data->transactionCurrency,
            'transactionCurrencyDecimalPlaces' => $data->transactionCurrencyDecimalPlaces,
            'transactionExchangeRate' => $data->transactionExchangeRate,

            'companyLocalCurrencyID' => $data->companyLocalCurrencyID,
            'companyLocalCurrency' => $data->companyLocalCurrency,
            'companyLocalCurrencyDecimalPlaces' => $data->companyLocalCurrencyDecimalPlaces,
            'companyLocalExchangeRate' => $data->companyLocalExchangeRate,

            'companyReportingCurrencyID' => $data->companyReportingCurrencyID,
            'companyReportingCurrency' => $data->companyReportingCurrency,
            'companyReportingCurrencyDecimalPlaces' => $data->companyReportingCurrencyDecimalPlaces,
            'companyReportingExchangeRate' => $data->companyReportingExchangeRate,

            'confirmedDate' => $data->createdDateTime,
            'confirmedByEmpID' => $data->createdUserID,
            'confirmedByName' => $data->createdUserName,

            'approvedDate' => $data->createdDateTime,
            'approvedbyEmpID' => $data->createdUserID,
            'approvedbyEmpName' => $data->createdUserName,

            'partyAutoID' => $partyID,
            'partySystemCode' => $partySysCode,
            'partyName' => $partyName,
            'partyCurrencyID' => $partyCurrencyID,
            'partyCurrency' => $partyCurrency,
            'partyExchangeRate' => $partyER,
            'partyCurrencyDecimalPlaces' => $partyDP,


            'segmentID' => $data->segmentID,
            'segmentCode' => $data->segmentCode,
            'companyID' => $data->companyID,
            'companyCode' => $data->companyCode,
            'createdPCID' => $data->createdPCID,
            'createdUserID' => $data->createdUserID,
            'createdUserName' => $data->createdUserName,
            'createdUserGroup' => $data->createdUserGroup,
            'createdDateTime' => $data->createdDateTime,
        );


        $payConData = posPaymentConfig_data();


        $bankLedger_Data = array(
            'documentMasterAutoID' => $data->invoiceID,
            'documentSystemCode' => $docsyscode,
            'documentDate' => $data->invoiceDate,

            'transactionType' => 1,
            'documentType' => 'POS',
            'remainIn' => null,
            'memo' => null,
            'clearedYN' => null,
            'clearedDate' => null,
            'clearedAmount' => null,
            'clearedBy' => null,
            'bankRecMonthID' => null,
            'thirdPartyName' => null,
            'thirdPartyInfo' => null,

            'transactionCurrencyID' => $data->transactionCurrencyID,
            'transactionCurrency' => $data->transactionCurrency,
            'transactionCurrencyDecimalPlaces' => $data->transactionCurrencyDecimalPlaces,
            'transactionExchangeRate' => $data->transactionExchangeRate,

            'partyType' => 'CUS',
            'partyAutoID' => $partyID,
            'partyCode' => $partySysCode,
            'partyName' => $partyName,
            'partyCurrencyID' => $partyData['partyCurID'],
            'partyCurrency' => $partyData['partyCurrency'],
            'partyCurrencyExchangeRate' => $partyData['partyER'],
            'partyCurrencyDecimalPlaces' => $partyData['partyDPlaces'],


            'segmentID' => $data->segmentID,
            'segmentCode' => $data->segmentCode,
            'companyID' => $data->companyID,
            'companyCode' => $data->companyCode,
            'createdPCID' => $data->createdPCID,
            'createdUserID' => $data->createdUserID,
            'createdUserName' => $data->createdUserName,
            'createdDateTime' => $data->createdDateTime,
            'timeStamp' => $data->createdDateTime
        );


        $this->load->model('Pos_config_model');
        $localER = $data->companyLocalExchangeRate;
        $localDP = $data->companyLocalCurrencyDecimalPlaces;
        $repoER = $data->companyReportingExchangeRate;
        $repoDP = $data->companyReportingCurrencyDecimalPlaces;

        /*if ($cash != 0 && $cash != null) {
            $cashAmount = $data->cashAmount;
            $cashGLID = $this->Pos_config_model->load_posGL(1); //srp_erp_pos_paymentglconfigmaster => unDepositFund autoID is (1)

            $cashGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                ->from('srp_erp_chartofaccounts')->where('GLAutoID', $cashGLID)->get()->row();

            $cashData = $GL_Data;
            $cashData['GLAutoID'] = $cashGL->GLAutoID;
            $cashData['systemGLCode'] = $cashGL->systemAccountCode;
            $cashData['GLCode'] = $cashGL->GLSecondaryCode;
            $cashData['GLDescription'] = $cashGL->GLDescription;
            $cashData['GLType'] = $cashGL->subCategory;
            $cashData['transactionAmount'] = $cashAmount;
            $cashData['companyLocalAmount'] = round(($cashAmount / $localER), $localDP);
            $cashData['companyReportingAmount'] = round(($cashAmount / $repoER), $repoDP);
            $cashData['partyCurrencyAmount'] = round(($cashAmount / $partyER), $partyDP);

            $this->db->insert('srp_erp_generalledger', $cashData);


            //BANK LEDGER IMPACT - CASH   created on 25-05-2017
            $ledgerInfo = loadPOS_BankLedgerInfo('cash');
            $cashData_bankLedger = $bankLedger_Data;
            $cashData_bankLedger['partyCurrencyAmount'] = round(($cashAmount / $partyER), $partyDP);;
            $cashData_bankLedger['transactionAmount'] = $cashAmount;

            $cashData_bankLedger['modeofPayment'] = 1;
            $cashData_bankLedger['chequeNo'] = null;
            $cashData_bankLedger['chequeDate'] = null;
            $cashData_bankLedger['bankName'] = $ledgerInfo['bankName'];
            $cashData_bankLedger['bankGLAutoID'] = $ledgerInfo['GLAutoID'];
            $cashData_bankLedger['bankSystemAccountCode'] = $ledgerInfo['systemAccountCode']; // systemAccountCode.chartofaccount
            $cashData_bankLedger['bankGLSecondaryCode'] = $ledgerInfo['GLSecondaryCode']; // GLSecondaryCode.chartofaccount
            $cashData_bankLedger['bankCurrencyID'] = $ledgerInfo['bankCurrencyID'];
            $cashData_bankLedger['bankCurrencyDecimalPlaces'] = $ledgerInfo['bankCurrencyDecimalPlaces'];

            $conversion = currency_conversionID($data->transactionCurrencyID, $ledgerInfo['bankCurrencyID']);

            $cashData_bankLedger['bankCurrencyID'] = $conversion['currencyID'];
            $cashData_bankLedger['bankCurrency'] = $conversion['CurrencyCode'];
            $cashData_bankLedger['bankCurrencyExchangeRate'] = $conversion['conversion'];
            $cashData_bankLedger['bankCurrencyAmount'] = $cashAmount / $conversion['conversion'];

            $this->db->insert('srp_erp_bankledger', $cashData_bankLedger);

        }*/

        /*if ($cheque != 0 && $cheque != null) {
            $chequeAmount = $data->chequeAmount;
            $chequeGLID = $this->Pos_config_model->load_posGL(1); //srp_erp_pos_paymentglconfigmaster => unDepositFund autoID is (1)

            $chequeBnkGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                ->from('srp_erp_chartofaccounts')->where('GLAutoID', $chequeGLID)->get()->row();

            $chequeData = $GL_Data;
            $chequeData['chequeNumber'] = $data->chequeNo;
            $chequeData['GLAutoID'] = $chequeBnkGL->GLAutoID;
            $chequeData['systemGLCode'] = $chequeBnkGL->systemAccountCode;
            $chequeData['GLCode'] = $chequeBnkGL->GLSecondaryCode;
            $chequeData['GLDescription'] = $chequeBnkGL->GLDescription;
            $chequeData['GLType'] = $chequeBnkGL->subCategory;
            $chequeData['transactionAmount'] = $chequeAmount;
            $chequeData['companyLocalAmount'] = round(($chequeAmount / $localER), $localDP);
            $chequeData['companyReportingAmount'] = round(($chequeAmount / $repoER), $repoDP);
            $chequeData['partyCurrencyAmount'] = round(($chequeAmount / $partyER), $partyDP);
            $this->db->insert('srp_erp_generalledger', $chequeData);



            $ledgerInfo = loadPOS_BankLedgerInfo('cash'); // un-deposited fund
            $cashData_bankLedger = $bankLedger_Data;
            $cashData_bankLedger['partyCurrencyAmount'] = round(($chequeAmount / $partyER), $partyDP);
            $cashData_bankLedger['transactionAmount'] = $chequeAmount;
            $cashData_bankLedger['modeofPayment'] = 2;

            $cashData_bankLedger['chequeNo'] = $this->input->post('_chequeNO');
            $tmpDate = $this->input->post('_chequeCashDate');
            $cashData_bankLedger['chequeDate'] = format_date_mysql_datetime($tmpDate);
            $cashData_bankLedger['bankName'] = $ledgerInfo['bankName'];
            $cashData_bankLedger['bankGLAutoID'] = $ledgerInfo['GLAutoID'];
            $cashData_bankLedger['bankSystemAccountCode'] = $ledgerInfo['systemAccountCode']; // systemAccountCode.chartofaccount
            $cashData_bankLedger['bankGLSecondaryCode'] = $ledgerInfo['GLSecondaryCode']; // GLSecondaryCode.chartofaccount
            $cashData_bankLedger['bankCurrencyID'] = $ledgerInfo['bankCurrencyID'];
            $cashData_bankLedger['bankCurrencyDecimalPlaces'] = $ledgerInfo['bankCurrencyDecimalPlaces'];

            $conversion = currency_conversionID($data->transactionCurrencyID, $ledgerInfo['bankCurrencyID']);

            $cashData_bankLedger['bankCurrencyID'] = $conversion['currencyID'];
            $cashData_bankLedger['bankCurrency'] = $conversion['CurrencyCode'];
            $cashData_bankLedger['bankCurrencyExchangeRate'] = $conversion['conversion'];
            $cashData_bankLedger['bankCurrencyAmount'] = $chequeAmount / $conversion['conversion'];

            $this->db->insert('srp_erp_bankledger', $cashData_bankLedger);
        }*/

        foreach ($baninvoicePayments as $bankDE) {
            /*print_r($baninvoicePayments);
            exit;*/
            if (($bankDE['paymentConfigMasterID'] != 2) && ($bankDE['paymentConfigMasterID'] != 7) && ($bankDE['paymentConfigMasterID'] != 25) && ($bankDE['paymentConfigMasterID'] != 26)) {
                /*echo $bankDE['paymentConfigMasterID'].'<br> -- ';
                echo $bankDE['PaymentID'];
                continue;*/

                /** Note : please change this to vias & master cards because it has different GL codes */
                $cardAmount = $data->cardAmount;
                $cardBankGLID = $data->cardBank;
                $bankGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                    ->from('srp_erp_chartofaccounts')->where('GLAutoID', $bankDE['GLCode'])->get()->row();


                $cardData = $GL_Data;
                $cardData['GLAutoID'] = $bankGL->GLAutoID;
                $cardData['systemGLCode'] = $bankGL->systemAccountCode;
                $cardData['GLCode'] = $bankGL->GLSecondaryCode;
                $cardData['GLDescription'] = $bankGL->GLDescription;
                $cardData['GLType'] = $bankGL->subCategory;
                $cardData['transactionAmount'] = $bankDE['amount'];
                $cardData['companyLocalAmount'] = round(($bankDE['amount'] / $localER), $localDP);
                $cardData['companyReportingAmount'] = round(($bankDE['amount'] / $repoER), $repoDP);
                $cardData['partyCurrencyAmount'] = round(($bankDE['amount'] / $partyER), $partyDP);

                $this->db->insert('srp_erp_generalledger', $cardData);


                /** BANK LEDGER IMPACT - Master OR Visa   created on 25-05-2017 */
                $GLAutoID = $this->input->post('_bank');
                $this->db->select("*");
                $this->db->from("srp_erp_chartofaccounts");
                $this->db->where("GLAutoID", $bankDE['GLCode']);
                $chartOfAccountTmp = $this->db->get()->row();

                $cashData_bankLedger = $bankLedger_Data;
                $cashData_bankLedger['partyCurrencyAmount'] = round(($bankDE['amount'] / $partyER), $partyDP);
                $cashData_bankLedger['transactionAmount'] = $bankDE['amount'];
                $cashData_bankLedger['modeofPayment'] = 1;
                $cashData_bankLedger['chequeNo'] = null;
                $cashData_bankLedger['chequeDate'] = null;
                $cashData_bankLedger['bankName'] = $chartOfAccountTmp->bankName;
                $cashData_bankLedger['bankGLAutoID'] = $chartOfAccountTmp->GLAutoID;
                $cashData_bankLedger['bankSystemAccountCode'] = $chartOfAccountTmp->systemAccountCode;
                $cashData_bankLedger['bankGLSecondaryCode'] = $chartOfAccountTmp->GLSecondaryCode;
                $cashData_bankLedger['bankCurrencyID'] = $chartOfAccountTmp->bankCurrencyID;
                $cashData_bankLedger['bankCurrencyDecimalPlaces'] = $chartOfAccountTmp->bankCurrencyDecimalPlaces;

                $conversion = currency_conversionID($data->transactionCurrencyID, $chartOfAccountTmp->bankCurrencyID);

                $cashData_bankLedger['bankCurrencyID'] = $conversion['currencyID'];
                $cashData_bankLedger['bankCurrency'] = $conversion['CurrencyCode'];
                $cashData_bankLedger['bankCurrencyExchangeRate'] = $conversion['conversion'];
                $cashData_bankLedger['bankCurrencyAmount'] = $conversion['conversion'] > 0 ? $bankDE['amount'] / $conversion['conversion'] : $bankDE['amount'];

                $this->db->insert('srp_erp_bankledger', $cashData_bankLedger);
            }
        }


        if ($creditNote != 0 && $creditNote != null) {

            $creditAmount = $data->creditNoteAmount;
            $creditNoteGLID = $this->Pos_config_model->load_posGL(2); //srp_erp_pos_paymentglconfigmaster => creditNote autoID is (2)
            $creditNoteGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                ->from('srp_erp_chartofaccounts')
                ->join('srp_erp_pos_paymentglconfigdetail', 'srp_erp_pos_paymentglconfigdetail.GLCode=srp_erp_chartofaccounts.GLAutoID')
                ->where('GLAutoID', $creditNoteGLID)->get()->row();

            $creditNoteData = $GL_Data;
            $creditNoteData['GLAutoID'] = $creditNoteGL->GLAutoID;
            $creditNoteData['systemGLCode'] = $creditNoteGL->systemAccountCode;
            $creditNoteData['GLCode'] = $creditNoteGL->GLSecondaryCode;
            $creditNoteData['GLDescription'] = $creditNoteGL->GLDescription;
            $creditNoteData['GLType'] = $creditNoteGL->subCategory;
            $creditNoteData['transactionAmount'] = $creditAmount;
            $creditNoteData['companyLocalAmount'] = round(($creditAmount / $localER), $localDP);
            $creditNoteData['companyReportingAmount'] = round(($creditAmount / $repoER), $repoDP);
            $creditNoteData['partyCurrencyAmount'] = round(($creditAmount / $partyER), $partyDP);


            $this->db->insert('srp_erp_generalledger', $creditNoteData);


            /**
             * BANK LEDGER IMPACT - Credit Note created on 25-05-2017
             * This function is commented on 2018-12-23  No need enter bank ledger for credit memos / credit Note instructed by Hisham
             */
            $ledgerInfo = loadPOS_BankLedgerInfo('creditNote');
            $cashData_bankLedger = $bankLedger_Data;
            $cashData_bankLedger['partyCurrencyAmount'] = round(($creditAmount / $partyER), $partyDP);
            $cashData_bankLedger['transactionAmount'] = $creditAmount;
            $cashData_bankLedger['modeofPayment'] = null;
            $cashData_bankLedger['chequeNo'] = null;
            $cashData_bankLedger['chequeDate'] = null;
            $cashData_bankLedger['bankName'] = $ledgerInfo['bankName'];
            $cashData_bankLedger['bankGLAutoID'] = $ledgerInfo['GLAutoID'];
            $cashData_bankLedger['bankSystemAccountCode'] = $ledgerInfo['systemAccountCode']; // systemAccountCode.chartofaccount
            $cashData_bankLedger['bankGLSecondaryCode'] = $ledgerInfo['GLSecondaryCode']; // GLSecondaryCode.chartofaccount
            $cashData_bankLedger['bankCurrencyID'] = $ledgerInfo['bankCurrencyID'];
            $cashData_bankLedger['bankCurrencyDecimalPlaces'] = $ledgerInfo['bankCurrencyDecimalPlaces'];

            $conversion = currency_conversionID($data->transactionCurrencyID, $ledgerInfo['bankCurrencyID']);

            $cashData_bankLedger['bankCurrencyID'] = $conversion['currencyID'];
            $cashData_bankLedger['bankCurrency'] = $conversion['CurrencyCode'];
            $cashData_bankLedger['bankCurrencyExchangeRate'] = $conversion['conversion'];
            //$cashData_bankLedger['bankCurrencyAmount'] = $creditAmount / $conversion['conversion'];

            //$this->db->insert('srp_erp_bankledger', $cashData_bankLedger);
        }

       

        if ($data->creditSalesAmount > 0 && $data->customerID != 0) {
            $partyGL = $partyData['partyGL'];
            $customerData = $GL_Data;
            $customerData['GLAutoID'] = $partyGL['receivableAutoID'];
            $customerData['systemGLCode'] = $partyGL['receivableSystemGLCode'];
            $customerData['GLCode'] = $partyGL['receivableGLAccount'];
            $customerData['GLDescription'] = $partyGL['receivableDescription'];
            $customerData['GLType'] = $partyGL['receivableType'];
            $customerData['transactionAmount'] = $data->creditSalesAmount;
            $customerData['companyLocalAmount'] = round(($data->creditSalesAmount / $localER), $localDP);
            $customerData['companyReportingAmount'] = round(($data->creditSalesAmount / $repoER), $repoDP);
            $customerData['partyCurrencyAmount'] = round(($data->creditSalesAmount / $partyER), $partyDP);
            $customerData['subLedgerType'] = 3;
            $customerData['subLedgerDesc'] = 'AR';

            $this->db->insert('srp_erp_generalledger', $customerData);
        }

        return $GL_Data;
    }

    function invoice_hold()
    {

        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $com_currency_id = $this->common_data['company_data']['company_default_currencyID'];
        $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
        $rep_currency = $this->common_data['company_data']['company_reporting_currency'];
        $rep_currDPlace = $this->common_data['company_data']['company_reporting_decimal'];
        $wareHouseData = $this->get_wareHouse();
        $customerID = $this->input->post('customerID');
        $customerCode = $this->input->post('customerCode');
        $tr_currency = $this->input->post('_trCurrency');
        $item = $this->input->post('itemID[]');
        $itemUOM = $this->input->post('itemUOM[]');
        $itemQty = $this->input->post('itemQty[]');
        $itemPrice = $this->input->post('itemPrice[]');
        $itemDis = $this->input->post('itemDis[]');
        $itemDisAmount = $this->input->post('itemDisAmount[]');
        $invoiceDate = format_date(date('Y-m-d'));
        $holdDateTime = $this->input->post('clientDateTime');

        /*Payment Details Calculation Start*/
        $cashAmount = $this->input->post('_cashAmount');
        $chequeAmount = $this->input->post('_chequeAmount');
        $cardAmount = $this->input->post('_cardAmount');
        $total_discVal = $this->input->post('gen_disc_amount_hide'); //$this->input->post('discVal');
        $paidAmount = ($cashAmount + $chequeAmount + $cardAmount);
        $netTotVal = $this->input->post('netTotVal');
        $netTotVal = $this->input->post('netTotVal');
        $taxFormula = $this->input->post('taxFormula');
        $taxamountCal = $this->input->post('taxamountCal');


        $balanceAmount = ($netTotVal - $paidAmount);

        if ($netTotVal < $paidAmount) {
            $cashAmount = $netTotVal - ($chequeAmount + $cardAmount);
            $balanceAmount = 0;
        }

        /*Payment Details Calculation End*/

        //Get last reference no
        $invCodeDet = $this->getInvoiceHoldCode();
        $lastRefNo = $invCodeDet['lastRefNo'];
        $refCode = $invCodeDet['refCode'];

        $localConversion = currency_conversion($tr_currency, $com_currency, $netTotVal);
        $localConversionRate = $localConversion['conversion'];
        $reportConversion = currency_conversion($tr_currency, $rep_currency, $netTotVal);
        $reportConversionRate = $reportConversion['conversion'];


        $invArray = array(
            'documentSystemCode' => $refCode,
            'serialNo' => $lastRefNo,
            'customerID' => $customerID,
            'customerCode' => $customerCode,
            'invoiceDate' => $invoiceDate,

            'netTotal' => number_format($netTotVal, $com_currDPlace),
            'localNetTotal' => ($netTotVal / $localConversionRate),
            'reportingNetTotal' => ($netTotVal / $reportConversionRate),

            'paidAmount' => $paidAmount,
            'localPaidAmount' => ($paidAmount / $localConversionRate),
            'reportingPaidAmount' => ($paidAmount / $reportConversionRate),

            'balanceAmount' => $balanceAmount,
            'localBalanceAmount' => ($balanceAmount / $localConversionRate),
            'reportingBalanceAmount' => ($balanceAmount / $reportConversionRate),

            'cashAmount' => $cashAmount,
            'chequeAmount' => $chequeAmount,
            'cardAmount' => $cardAmount,

            'discountAmount' => $total_discVal,
            'localDiscountAmount' => ($total_discVal / $localConversionRate),
            'reportingDiscountAmount' => ($total_discVal / $reportConversionRate),


            'companyLocalExchangeRate' => $localConversionRate,
            'companyLocalCurrency' => $com_currency,
            'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
            'companyLocalCurrencyID' => $com_currency_id,

            'transactionExchangeRate' => $localConversionRate,
            'transactionCurrencyID' => $com_currency_id,
            'transactionCurrency' => $com_currency,
            'transactionCurrencyDecimalPlaces' => $com_currDPlace,

            'companyReportingExchangeRate' => $reportConversionRate,
            'companyReportingCurrency' => $rep_currency,
            'companyReportingCurrencyDecimalPlaces' => $rep_currDPlace,

            'wareHouseAutoID' => $wareHouseData['wareHouseAutoID'],
            'wareHouseCode' => $wareHouseData['wareHouseCode'],
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'wareHouseDescription' => $wareHouseData['wareHouseDescription'],

            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdUserGroup' => $this->common_data['user_group'],
            'createdDateTime' => $holdDateTime
        );
        //echo '<pre>'.print_r($invArray).'</pre>';die();


        $this->db->trans_start();
        $this->db->insert('srp_erp_pos_invoicehold', $invArray);
        $invID = $this->db->insert_id();

        $i = 0;
        $dataInt = array();
        foreach ($item as $itemID) {
            $itemData = fetch_ware_house_item_data($itemID);

            /*echo '<pre>'.print_r($itemData).'</pre>';*/
            $price = str_replace(',', '', $itemPrice[$i]);
            $itemTotal = $itemQty[$i] * $price;
            $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal - ($itemTotal * 0.01 * $itemDis[$i])) : $itemTotal;

            $dataInt[$i]['invoiceID'] = $invID;
            $dataInt[$i]['itemAutoID'] = $itemID;
            $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
            $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
            $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
            $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
            $dataInt[$i]['conversionRateUOM'] = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
            $dataInt[$i]['qty'] = $itemQty[$i];
            $dataInt[$i]['price'] = $price;
            $dataInt[$i]['discountPer'] = $itemDis[$i];
            $itemDisAmount_this = $itemDisAmount[$i];
            $dataInt[$i]['discountAmount'] = (!empty($itemDisAmount_this)) ? ($itemDisAmount_this / $itemQty[$i]) : 0;

            $dataInt[$i]['itemFinanceCategory'] = $itemData['subcategoryID'];
            $dataInt[$i]['itemFinanceCategorySub'] = $itemData['subSubCategoryID'];
            $dataInt[$i]['financeCategory'] = $itemData['financeCategory'];
            $dataInt[$i]['itemCategory'] = $itemData['mainCategory'];

            $dataInt[$i]['transactionAmount'] = $itemTotal;
            $dataInt[$i]['transactionExchangeRate'] = '';
            $dataInt[$i]['transactionCurrency'] = $com_currency;
            $dataInt[$i]['transactionCurrencyDecimalPlaces'] = '';

            $dataInt[$i]['companyLocalAmount'] = round(($itemTotal / $localConversionRate), $com_currDPlace);
            $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
            $dataInt[$i]['companyLocalCurrency'] = $com_currency;
            $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

            $dataInt[$i]['taxCalculationformulaID'] = $taxFormula[$i];
            $dataInt[$i]['taxAmount'] = $taxamountCal[$i];

            $dataInt[$i]['companyReportingAmount'] = round(($itemTotal / $reportConversionRate), $rep_currDPlace);
            $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
            $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
            $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

            $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
            $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
            $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
            $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $dataInt[$i]['createdDateTime'] = $holdDateTime;
            $i++;
        }

        $this->db->insert_batch('srp_erp_pos_invoiceholddetail', $dataInt);


        $this->db->trans_complete();
        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            return array('e', 'Error in Hold Invoice');
        } else {
            $this->db->trans_commit();
            return array('s', 'Hold Invoice Code : ' . $refCode . ' ', $invID, $refCode);
        }
    }

    function customer_search()
    {
        $key = $this->input->post('key');
        $companyID = $this->common_data['company_data']['company_id'];

        return $this->db->query("SELECT customerAutoID, customerSystemCode,secondaryCode, customerName, customerCurrency, customerTelephone,IF(loyalitycard.cardMasterID!='',1,0) as iscardexist,barcode as loyalitycardno
                                 FROM srp_erp_customermaster
                                  LEFT JOIN srp_erp_pos_loyaltycard loyalitycard on loyalitycard.customerID = srp_erp_customermaster.customerAutoID AND customerType = 0
                                  WHERE srp_erp_customermaster.companyID={$companyID} AND
                                 (customerName LIKE '%$key%' OR customerTelephone LIKE '%$key%' OR secondaryCode LIKE '%$key%')
                                 UNION SELECT 0, 'CASH', 'Cash', '','','','2','' ")->result_array();
    }

    function item_batch_search(){

        $key = $this->input->post('key');
        $invoice_no = $this->input->post('invoice_no');
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouseID = $this->common_data['ware_houseID'];
        $date = date('Y-m-d',strtotime(current_date()));

        $itemdetail = get_inventory_item_master_details($key); 
       
        if($itemdetail){

            $itemAutoID = $itemdetail['itemAutoID'];

            $results = $this->db->query("SELECT *
                FROM srp_erp_inventory_itembatch
                WHERE srp_erp_inventory_itembatch.companyId={$companyID} 
                AND srp_erp_inventory_itembatch.wareHouseAutoID={$wareHouseID}
                AND srp_erp_inventory_itembatch.itemMasterID={$itemAutoID} 
                AND srp_erp_inventory_itembatch.batchExpireDate >= '{$date}'
                ORDER BY srp_erp_inventory_itembatch.batchExpireDate ASC
            ")->result_array();

            foreach($results as $record_key=>$record){

                $batchNumber = trim($record['batchNumber'] ?? '');

                $re_exists = $this->db->select('*')
                    ->from('srp_erp_inventory_itembatch_reserved')
                    ->where('invoice_num', $invoice_no)
                    ->where('barcode', $key)
                    ->where('batchNumber', $batchNumber)
                    ->where('status', 0)
                    ->get()->row_array();
               
                if($re_exists){
                    $results[$record_key]['selected'] = 1;
                    $results[$record_key]['reserved_qty'] = $re_exists['reserved_qty'];
                }else{
                    $results[$record_key]['selected'] = 0;
                    $results[$record_key]['reserved_qty'] = '';
                }

                $results[$record_key]['defaultUnitOfMeasure'] = $itemdetail['defaultUnitOfMeasure'];

            }

            return $results;
        }
       
        return array();

    }

    function invoice_cardDetail()
    {
        $invID = $this->input->post('invID');
        $referenceNO = $this->input->post('referenceNO');
        $cardNumber = $this->input->post('cardNumber');
        $bank = $this->input->post('bank');

        $upData = array(
            'cardNumber' => $cardNumber,
            'cardRefNo' => $referenceNO,
            'cardBank' => $bank
        );

        $this->db->where('invoiceID', $invID)->update('srp_erp_pos_invoice', $upData);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Card Details Updated', $invID);
        } else {
            return array('e', 'Error In Card Details Updated');
        }
    }

    function recall_invoice()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouse = $this->common_data['ware_houseID'];
        return $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName
                                 FROM srp_erp_pos_invoice t1 WHERE companyID={$companyID} AND wareHouseAutoID={$wareHouse}")->result_array();
    }

    function creditNote_search()
    {
        $key = $this->input->post('key');
        $companyID = current_companyID();
        return $this->db->select('salesReturnID, documentSystemCode, salesReturnDate, netTotal')
            ->from('srp_erp_pos_salesreturn t1')
            ->where("(documentSystemCode LIKE '%" . $key . "%'  OR  netTotal LIKE '%" . $key . "%')
                        AND companyID={$companyID}
                        AND NOT EXISTS (
                            SELECT * FROM srp_erp_pos_invoice WHERE creditNoteID = t1.salesReturnID
                        )
                    ")->get()->result_array();
    }

    function get_returnCode($creditNoteID)
    {
        return $this->db->select('documentSystemCode')->from('srp_erp_pos_salesreturn')
            ->where('salesReturnID', $creditNoteID)->get()->row('documentSystemCode');
    }

    function invoice_search($invoiceID = null)
    {
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];

        /** when we open it from receipt, we do not need outlet filter */
        $receipt = $this->input->post('receipt');
        if ($receipt == 1) {
            $where = array('companyID' => $companyID);
        } else {

            $where = array('companyID' => $companyID, 'wareHouseAutoID' => $wareHouse);
        }

        if ($invoiceID != null) {
            $where['t1.invoiceID'] = $invoiceID;
        } else {
            $invoiceCode = $this->input->post('invoiceCode');
            $where['t1.invoiceCode'] = $invoiceCode;
        }


        $isExistInv = $this->db->select("t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName,
                                  (SELECT sum(balanceAmount) FROM srp_erp_pos_invoice WHERE customerID = t1.customerID) AS cusBalance,
                                  (SELECT EmpShortCode FROM srp_employeesdetails WHERE EIdNo = t1.createdUserID) AS repName")
            ->from("srp_erp_pos_invoice t1")
            ->where($where)->get()->row_array();

        if ($isExistInv != null) {
            $invoiceID = $isExistInv['invoiceID'];


            $invItems = $this->db->select('invoiceDetail.*,amount,taxPercentage,documentDetailAutoID, (SELECT itemImage FROM srp_erp_itemmaster WHERE itemAutoID=invoiceDetail.itemAutoID) itemImage,( SELECT seconeryItemCode FROM srp_erp_itemmaster WHERE itemAutoID = invoiceDetail.itemAutoID ) seconeryItemCode')
                ->select(" (invoiceDetail.qty - (SELECT  IFNULL(SUM(qty),0) FROM srp_erp_pos_salesreturndetails WHERE itemAutoID = invoiceDetail.itemAutoID
                                        AND invoiceDetail.invoiceID = {$invoiceID} AND invoiceDetailID = invoiceDetail.invoiceDetailsID ) ) balanceQty")
                ->from('srp_erp_pos_invoicedetail invoiceDetail')->join('(SELECT
                    amount,
                    srp_erp_taxledger.taxPercentage,
                    documentDetailAutoID 
                FROM
                    srp_erp_taxledger
                    LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                WHERE
                    documentID = \'GPOS\' 
                    AND taxCategory = 2 
                GROUP BY
                    documentID,
                    documentDetailAutoID)generalPos', 'generalPos.documentDetailAutoID = invoiceDetail.invoiceDetailsID', 'left')
                ->join('srp_erp_pos_invoice', 'srp_erp_pos_invoice.invoiceID=invoiceDetail.invoiceID', 'left')
                ->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID=invoiceDetail.taxCalculationformulaID', 'left')
                ->where('invoiceDetail.invoiceID', $invoiceID)->get()->result_array();

            $credit_reference = $this->db->query("select referenceNo from srp_erp_customerinvoicemaster where posMasterAutoID=$invoiceID")->row('referenceNo');
            $isExistInv['referenceNo'] = $credit_reference;
            $invCodeDet = $this->getReturnCode();
            return array(
                0 => 's',
                1 => $isExistInv,
                2 => $invItems,
                3 => $invCodeDet['refCode']
            );
        } else {
            return array('w', 'There is not a invoice in this number');
        }
    }

    function invoice_return()
    {

        $financeYear = $this->db->select('companyFinanceYearID, beginingDate, endingDate')->from('srp_erp_companyfinanceyear')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();


        $financePeriod = $this->db->select('companyFinancePeriodID, dateFrom, dateTo')->from('srp_erp_companyfinanceperiod')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();

        if (empty($financeYear)) {
            return array('e', 'Please setup the current financial year');
            exit;
        }

        if (empty($financePeriod)) {
            return array('e', 'Please setup the current financial period');
            exit;
        }

        $currentShiftData = $this->isHaveNotClosedSession();

        if (!empty($currentShiftData)) {

            $com_currency = $this->common_data['company_data']['company_default_currency'];
            $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
            $rep_currency = $this->common_data['company_data']['company_reporting_currency'];
            $rep_currDPlace = $this->common_data['company_data']['company_reporting_decimal'];
            $wareHouseData = $this->get_wareHouse();

            $returnType = 0;
            $returnMode = $this->input->post('returnMode');
            /***************************************************
             *  $returnMode  =>  exchange/ creditNote =>  1    *
             *  $returnMode  =>  Refund               =>  2    *
             *  $returnMode  =>  credit-to-customer   =>  3    *
             ***************************************************/

            switch ($returnMode) {
                case 'exchange':
                    $returnType = 1;
                    break;

                case 'Refund':
                    $returnType = 2;
                    break;

                case 'credit-to-customer':
                    $returnType = 3;
                    break;
            }

            $return_invID = $this->input->post('return-invoiceID');
            $customerID = $this->input->post('return-customerID');
            $customerCode = $this->input->post('return-cusCode');
            $tr_currency = $this->common_data['company_data']['company_default_currency'];
            $invoiceDetailsID = $this->input->post('invoiceDetailsID[]');
            $item = $this->input->post('itemID[]');
            $itemUOM = $this->input->post('itemUOM[]');
            $return_QTY = $this->input->post('return_QTY[]');
            $itemPrice = $this->input->post('itemPrice[]');
            $itemDis = $this->input->post('itemDis[]');
            $invoiceDate = format_date($this->input->post('return-date'));
            $generalDisc = $this->input->post('generaldiscreturn');
            /*Payment Details Calculation Start*/
            $refundable_hidden = $this->input->post('return-refundable-hidden');
            $refund = str_replace(',', '', $this->input->post('return-refund'));
            $total_discVal = str_replace(',', '', $this->input->post('return-discTotal'));
            $netTotVal = str_replace(',', '', $this->input->post('return-credit-total'));
            $subTotalAmount = str_replace(',', '', $this->input->post('return-subTotalAmount'));
            $promoDiscountAmount = $this->input->post('promoDiscountAmount');
            $promoDiscount = $this->input->post('promo-discount');
            $promoDiscountID = $this->input->post('promotionDiscountID');
            $paymentType = $this->input->post('paymentType');

            //Set to cash
            $paymentType = ($paymentType) ? $paymentType : 1;


            //Get last reference no
            $invCodeDet = $this->getReturnCode();

            //get payment config detail
            //Set to exchange as credit note type
            if($returnType == '1'){
                $paymentType = 2;
                $paymentTypeDetail = get_posconfig_details($paymentType,$wareHouseData['wareHouseAutoID']);
            }else{
                $paymentTypeDetail = get_posconfig_details($paymentType,$wareHouseData['wareHouseAutoID'],'id');
            }
            

            if(empty($paymentTypeDetail)){
                return array('e','No credit note configuration has found');
            }

            $lastRefNo = $invCodeDet['lastRefNo'];
            $refCode = $invCodeDet['refCode'];

            $localConversion = currency_conversion($tr_currency, $com_currency, $netTotVal);
            $localConversionRate = $localConversion['conversion'];
            $transConversion = currency_conversion($tr_currency, $tr_currency, $netTotVal);
            $tr_currDPlace = $transConversion['DecimalPlaces'];
            $transConversionRate = $transConversion['conversion'];
            $reportConversion = currency_conversion($tr_currency, $rep_currency, $netTotVal);
            $reportConversionRate = $reportConversion['conversion'];

            $segmentDetails = $this->db->query("select segmentID,segmentCode from srp_erp_pos_invoice where invoiceID=$return_invID")->row();

            $isGroupBasedTax = $this->db->query("select isGroupBasedTax from srp_erp_pos_invoice where invoiceID=$return_invID")->row('isGroupBasedTax');


            $returnArray = array(
                'invoiceID' => $return_invID,
                'documentSystemCode' => $refCode,
                'documentCode' => 'RET',
                'serialNo' => $lastRefNo,
                'financialYearID' => $financeYear['companyFinanceYearID'],
                'financialPeriodID' => $financePeriod['companyFinancePeriodID'],
                'FYBegin' => $financeYear['beginingDate'],
                'FYEnd' => $financeYear['endingDate'],
                'FYPeriodDateFrom' => $financePeriod['dateFrom'],
                'FYPeriodDateTo' => $financePeriod['dateTo'],
                'customerID' => $customerID,
                'customerCode' => $customerCode,
                'salesReturnDate' => $invoiceDate,
                'counterID' => $currentShiftData['counterID'],
                'shiftID' => $currentShiftData['shiftID'],

                'subTotal' => $subTotalAmount,
                'netTotal' => $netTotVal,
                'refundAmount' => $refund,
                'discountAmount' => $total_discVal,
                'generalDiscountPercentage' => $generalDisc,
                'generalDiscountAmount' => ($subTotalAmount * $generalDisc) / 100,
                'promotionID' => $promoDiscountID,
                'promotiondiscount' => $promoDiscount,
                'promotiondiscountAmount' => $promoDiscountAmount,
                'returnMode' => $returnType,
                'segmentID' => $segmentDetails->segmentID,
                'segmentCode' => $segmentDetails->segmentCode,
                'companyLocalCurrencyID' => $localConversion['currencyID'],
                'companyLocalCurrency' => $com_currency,
                'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
                'companyLocalExchangeRate' => $localConversionRate,

                'transactionCurrencyID' => $localConversion['trCurrencyID'],
                'transactionCurrency' => $tr_currency,
                'transactionCurrencyDecimalPlaces' => $tr_currDPlace,
                'transactionExchangeRate' => $transConversionRate,

                'companyReportingCurrencyID' => $reportConversion['currencyID'],
                'companyReportingCurrency' => $rep_currency,
                'companyReportingCurrencyDecimalPlaces' => $rep_currDPlace,
                'companyReportingExchangeRate' => $reportConversionRate,


                'wareHouseAutoID' => $wareHouseData['wareHouseAutoID'],
                'wareHouseCode' => $wareHouseData['wareHouseCode'],
                'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
                'wareHouseDescription' => $wareHouseData['wareHouseDescription'],

                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date(),
                'isGroupBasedTax' => $isGroupBasedTax
            );

            

            if ($customerID == 0) {
                // $bankData = $this->db->query("SELECT receivableAutoID, receivableSystemGLCode, receivableGLAccount,
                //                           receivableDescription, receivableType
                //                           FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();
               // $cashGLID = $this->Pos_config_model->load_posGL(1); //srp_erp_pos_paymentglconfigmaster => unDepositFund autoID is (1)
                
                

                $cashGLID = $this->Pos_config_model->load_posGL($paymentTypeDetail['paymentConfigMasterID']);

               
              
                $cashGL = $this->db->select('*')
                    ->from('srp_erp_chartofaccounts')->where('GLAutoID', $cashGLID)->get()->row();

                if($paymentTypeDetail['paymentConfigMasterID'] != 1){
                    $returnArray['bankGLAutoID'] = $cashGL->GLAutoID;
                    $returnArray['bankSystemGLCode'] = $cashGL->systemAccountCode;
                    $returnArray['bankGLDescription'] = $cashGL->GLDescription;
                    $returnArray['bankGLAccount'] = $cashGL->systemAccountCode;
                    $returnArray['bankCurrencyID'] = $cashGL->bankCurrencyID;
                    // $returnArray['bankCurrency'] =$cashGL->bankCurrencyCode;
                    $conversion = currency_conversionID($returnArray['bankCurrencyID'], $returnArray['companyLocalCurrencyID']);
                    $returnArray['bankCurrencyExchangeRate'] = $conversion['conversion'];
                    $returnArray['bankCurrency'] = $conversion['CurrencyCode'];
                    $returnArray['bankCurrencyAmount'] = $returnArray['refundAmount'] / $returnArray['bankCurrencyExchangeRate'];
                }
               

                /*************** item ledger party currency ***********/
                $partyData = array(
                    'cusID' => 0,
                    'sysCode' => 'CASH',
                    'cusName' => 'CASH',
                    'partyCurID' => '',
                    'partyCurrency' => $tr_currency,
                    'partyDPlaces' => $tr_currDPlace,
                    'paymentType' => $paymentTypeDetail['paymentConfigMasterID'],
                    'partyER' => $transConversionRate,
                );

               

            } else {
                $cusData = $this->db->query("SELECT receivableAutoID, receivableSystemGLCode, receivableGLAccount,
                                             receivableDescription, receivableType,customerCurrency,customerSystemCode,customerAutoID,customerName,customerCurrencyID,customerCurrency,customerCurrencyDecimalPlaces
                                             FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

                $partyData = currency_conversion($tr_currency, $cusData['customerCurrency']);

                $returnArray['customerReceivableAutoID'] = $cusData['receivableAutoID'];
                $returnArray['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
                $returnArray['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
                $returnArray['customerReceivableDescription'] = $cusData['receivableDescription'];
                $returnArray['customerReceivableType'] = $cusData['receivableType'];

                /*************** item ledger party currency ***********/

                $partyData = array(
                    'cusID' => $cusData['customerAutoID'],
                    'sysCode' => $cusData['customerSystemCode'],
                    'cusName' => $cusData['customerName'],
                    'partyCurID' => $cusData['customerCurrencyID'],
                    'partyCurrency' => $cusData['customerCurrency'],
                    'partyDPlaces' => $cusData['customerCurrencyDecimalPlaces'],
                    'partyER' => $partyData['conversion'],
                    'partyGL' => $cusData,
                );
            }

            $this->db->trans_start();
            $this->db->insert('srp_erp_pos_salesreturn', $returnArray);
            $salesReturnID = $this->db->insert_id();

            /*Load wac library*/
            $this->load->library('Wac');

            $itemReturn_ledger_arr = array();
            $i = 0;
            $dataInt = array();


            foreach ($return_QTY as $r_qty) {
                $itemID = $item[$i];
                $itemData = fetch_ware_house_item_data($itemID);
                $conversion = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
                $conversionRate = 1 / $conversion;
                $qty = $r_qty * $conversionRate;
                $returnData = $this->get_invReturnBalanceQty($return_invID, $invoiceDetailsID[$i], $itemID);
                $balanceQty = $returnData->balanceQty;
                $invWac = $returnData->wacAmount;

                $invoiceDetails = $this->db->query("select (taxAmount/qty) as taxPerItem,taxAmount,taxCalculationformulaID from srp_erp_pos_invoicedetail where invoiceID=$return_invID and itemAutoID=$itemID")->row();
                //var_dump($this->db->last_query());exit;
                $taxAmount = $invoiceDetails->taxPerItem * $qty;
                $taxCalculationformulaID = $invoiceDetails->taxCalculationformulaID;
                
               // print_r($invWac); exit;

                if ($qty > 0) {
                    if ($balanceQty >= $qty) {

                        $price = str_replace(',', '', $itemPrice[$i]);
                        $itemTotal = $r_qty * $price;
                        $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal - ($itemTotal * 0.01 * $itemDis[$i])) : $itemTotal;
                        if($generalDisc > 0) {
                            $generalDiscountAmount_ret = ($itemTotal * $generalDisc) / 100;
                            $itemTotal = $itemTotal - $generalDiscountAmount_ret;
                        }

                        $dataInt[$i]['salesReturnID'] = $salesReturnID;
                        $dataInt[$i]['invoiceID'] = $return_invID;
                        $dataInt[$i]['invoiceDetailID'] = $invoiceDetailsID[$i];
                        $dataInt[$i]['itemAutoID'] = $itemID;
                        $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
                        $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
                        $dataInt[$i]['defaultUOMID'] = $itemData['defaultUnitOfMeasureID'];
                        $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
                        $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
                        $dataInt[$i]['UOMID'] = $itemData['defaultUnitOfMeasureID'];
                        $dataInt[$i]['conversionRateUOM'] = $conversion;
                        $dataInt[$i]['qty'] = $qty;
                        $dataInt[$i]['price'] = $price;
                        $dataInt[$i]['discountPer'] = $itemDis[$i];
                        $dataInt[$i]['generalDiscountPercentage'] = $generalDisc;
                        $dataInt[$i]['generalDiscountAmount'] = (($itemTotal + $taxAmount) * $generalDisc) / 100;

                        $dataInt[$i]['promotiondiscount'] = $promoDiscount;
                        $dataInt[$i]['promotiondiscountAmount'] = ($itemTotal * $promoDiscount) / 100;

                        $dataInt[$i]['wacAmount'] = $itemData['companyLocalWacAmount'];

                        $dataInt[$i]['itemFinanceCategory'] = $itemData['subcategoryID'];
                        $dataInt[$i]['itemFinanceCategorySub'] = $itemData['subSubCategoryID'];
                        $dataInt[$i]['financeCategory'] = $itemData['financeCategory'];
                        $dataInt[$i]['itemCategory'] = $itemData['mainCategory'];

                        $dataInt[$i]['expenseGLAutoID'] = $itemData['costGLAutoID'];
                        $dataInt[$i]['expenseGLCode'] = $itemData['costGLCode'];
                        $dataInt[$i]['expenseSystemGLCode'] = $itemData['costSystemGLCode'];
                        $dataInt[$i]['expenseGLDescription'] = $itemData['costDescription'];
                        $dataInt[$i]['expenseGLType'] = $itemData['costType'];

                        $dataInt[$i]['revenueGLAutoID'] = $itemData['revanueGLAutoID'];
                        $dataInt[$i]['revenueGLCode'] = $itemData['revanueGLCode'];
                        $dataInt[$i]['revenueSystemGLCode'] = $itemData['revanueSystemGLCode'];
                        $dataInt[$i]['revenueGLDescription'] = $itemData['revanueDescription'];
                        $dataInt[$i]['revenueGLType'] = $itemData['revanueType'];

                        $dataInt[$i]['assetGLAutoID'] = $itemData['assteGLAutoID'];
                        $dataInt[$i]['assetGLCode'] = $itemData['assteGLCode'];
                        $dataInt[$i]['assetSystemGLCode'] = $itemData['assteSystemGLCode'];
                        $dataInt[$i]['assetGLDescription'] = $itemData['assteDescription'];
                        $dataInt[$i]['assetGLType'] = $itemData['assteType'];

                        $dataInt[$i]['transactionAmount'] = $itemTotal;
                        $dataInt[$i]['transactionExchangeRate'] = $transConversionRate;
                        $dataInt[$i]['transactionCurrency'] = $tr_currency;
                        $dataInt[$i]['transactionCurrencyID'] = $localConversion['trCurrencyID'];
                        $dataInt[$i]['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;

                        $dataInt[$i]['companyLocalAmount'] = number_format(($itemTotal / $localConversionRate), $com_currDPlace);
                        $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
                        $dataInt[$i]['companyLocalCurrency'] = $com_currency;
                        $dataInt[$i]['companyLocalCurrencyID'] = $localConversion['currencyID'];
                        $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

                        $dataInt[$i]['companyReportingAmount'] = number_format(($itemTotal / $reportConversionRate), $rep_currDPlace);
                        $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
                        $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
                        $dataInt[$i]['companyReportingCurrencyID'] = $reportConversion['currencyID'];
                        $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

                        $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
                        $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $dataInt[$i]['createdDateTime'] = current_date();

                        if ($isGroupBasedTax == 1) {
                            $dataInt[$i]['taxAmount'] = number_format($taxAmount, $com_currDPlace);
                            $dataInt[$i]['taxCalculationformulaID'] = $taxCalculationformulaID;
                        }

                        /*$newQty = $availableQTY + $qty;

                        $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
                        $itemUpdateQty = array('currentStock' => $newQty);
                        $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);*/


                        $wacData = $this->wac->wac_calculation(0, $itemID, $qty, $invWac, $this->common_data['ware_houseID']);
                        $itemReturn_ledger_arr[$i] = $this->item_ledger($financeYear, $financePeriod, $refCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData, 1);

                        $i++;
                    } else {
                        $this->db->trans_rollback();
                        return array('e', '[ ' . $itemData['itemSystemCode'] . ' - ' . $itemData['itemDescription'] . ' ]<p> maximum return quantity is : ' . $balanceQty);
                        break;
                    }
                }
            }

            //var_dump($dataInt);exit;
            $this->db->insert_batch('srp_erp_pos_salesreturndetails', $dataInt);

            $salesReturnAutoID = $this->input->post('salesReturnAutoID');
            $companyID = $this->common_data['company_data']['company_id'];


            $invoiceDetailsIDsQry = $this->db->query("select * from srp_erp_pos_salesreturndetails where invoiceID=$return_invID");
            $invoiceDetailsIDsArray = array();
            $returnDetailsIDsArray = array();
            foreach ($invoiceDetailsIDsQry->result() as $item) {
                array_push($invoiceDetailsIDsArray, $item->invoiceDetailID);
                array_push($returnDetailsIDsArray, $item->salesReturnDetailID);
            }
            $invoiceDetailsIDsInSalesReturnDetails = implode(", ", $invoiceDetailsIDsArray);
            $returnDetailsIDsInSalesReturnDetails = implode(", ", $returnDetailsIDsArray);


            $ledgerDet = $this->db->query("SELECT
                srp_erp_pos_salesreturndetails.salesReturnID,
                srp_erp_pos_salesreturndetails.salesReturnDetailID,
                srp_erp_pos_salesreturndetails.qty as returnQty,
                srp_erp_pos_invoicedetail.qty as soldQty,
                taxledger.amount,
                IF( srp_erp_taxmaster.taxCategory = 2, ( SELECT vatRegisterYN FROM `srp_erp_company` WHERE company_id = $companyID ), srp_erp_taxmaster.isClaimable ) AS isClaimable,
                    customerCountryID,
                    vatEligible,
                    srp_erp_pos_invoice.customerID,	
                IF( taxCategory = 2, outputVatGLAccountAutoID, taxGlAutoID ) AS outputVatGLAccountAutoID,
                    outputVatTransferGLAccountAutoID,
                    srp_erp_pos_invoicedetail.transactionAmount,
                    taxledger.*
                FROM
                    srp_erp_pos_salesreturndetails
                    JOIN srp_erp_pos_salesreturn ON srp_erp_pos_salesreturn.salesReturnID = srp_erp_pos_salesreturndetails.salesReturnID
                    JOIN srp_erp_pos_invoicedetail ON srp_erp_pos_salesreturndetails.invoiceDetailID = srp_erp_pos_invoicedetail.invoiceDetailsID
                    JOIN ( SELECT * FROM srp_erp_taxledger WHERE documentID = 'GPOS' 
                    AND documentDetailAutoID IN ( $invoiceDetailsIDsInSalesReturnDetails ) 
                    ) taxledger ON taxledger.documentDetailAutoID = srp_erp_pos_invoicedetail.invoiceDetailsID 
                    JOIN srp_erp_pos_invoice ON srp_erp_pos_salesreturndetails.invoiceID = srp_erp_pos_invoice.invoiceID
                    LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_pos_invoice.customerID
                    JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = taxledger.taxFormulaDetailID
                    JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID 
                WHERE
	            srp_erp_pos_salesreturndetails.salesReturnDetailID IN ( $returnDetailsIDsInSalesReturnDetails )")->result_array();
            //var_dump($this->db->last_query());exit;
            if (!empty($ledgerDet)) {
                $taxAmount = 0;
                foreach ($ledgerDet as $val) {
                    $taxPerUnit = $val['amount'] / $val['soldQty'];
                    $dataleg['documentID'] = 'RET';
                    $dataleg['documentMasterAutoID'] = $val['salesReturnID'];
                    $dataleg['documentDetailAutoID'] = $val['salesReturnDetailID'];
                    $dataleg['taxDetailAutoID'] = null;
                    $dataleg['taxPercentage'] = 0;
                    $dataleg['ismanuallychanged'] = 0;
                    $dataleg['isClaimable'] = $val['isClaimable'];
                    $dataleg['taxFormulaMasterID'] = $val['taxFormulaMasterID'];
                    $dataleg['taxFormulaDetailID'] = $val['taxFormulaDetailID'];
                    $dataleg['taxMasterID'] = $val['taxMasterID'];
                    $taxCalculateAmount = $taxPerUnit * $val['returnQty'];
                    $dataleg['amount'] = $taxCalculateAmount;
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
                    $this->db->insert('srp_erp_taxledger', $dataleg);
                    //$taxAmount += ($val['amount'] / $val['transactionAmount']) * $taxCalculateAmount;
                }
                //                    $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                //                    $data_detailTBL['taxAmount'] = $taxAmount;
                //                    $this->db->where('salesReturnID', $salesRetDet->salesReturnID);
                //$this->db->update('srp_erp_pos_salesreturndetails', $data_detailTBL);
            }


            $this->db->insert_batch('srp_erp_itemledger', $itemReturn_ledger_arr);
            $this->double_entry_itemReturn($salesReturnID, $partyData);


            $this->db->trans_complete();
            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                return array('e', 'Error in return process');
            } else {
                $this->db->trans_commit();
                /*$invoiceCode = $this->db->query("SELECT invoiceCode FROM srp_erp_pos_invoice WHERE invoiceID={$return_invID}")->row_array();*/

                return array('s', 'Return Note : ' . $refCode . ' ', $salesReturnID, $refCode);
            }
        } else {
            return array('e', 'You have not a valid session.<p>Please login and try again.</p>');
        }
    }

    function double_entry_itemReturn($salesReturnID, $partyData)
    {

        /*" . $partyData['cusID'] . " partyID, '" . $partyData['cusName'] . "' partyName, '" . $partyData['sysCode'] . "' partyCode, '" . $partyData['partyCurrency'] . "' partyCur, " . $partyData['partyDPlaces'] . " partyDPlace, " . $partyData['partyER'] . " partyER,*/
        $partyID = $partyData['cusID'];
        $partyName = $partyData['cusName'];
        $partySysCode = $partyData['sysCode'];
        $partyCurrencyID = 0;
        $partyCurrency = $partyData['partyCurrency'];
        $partyER = $partyData['partyER'];
        $partyDP = $partyData['partyDPlaces'];
        $paymentType = $partyData['paymentType'];

        /************** EXPENSE GL DEBIT *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID, systemGLCode, GLCode, GLDescription,
                     GLType, amount_type, transactionAmount, transactionCurrency,transactionCurrencyID, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalAmount, companyLocalCurrency,companyLocalCurrencyID,  companyLocalExchangeRate,
                     companyLocalCurrencyDecimalPlaces,  companyReportingAmount, companyReportingCurrency,companyReportingCurrencyID, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,
                     partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate, partyCurrencyAmount, partyCurrencyDecimalPlaces,
                     confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID, createdUserID,
                     createdDateTime, createdUserName)
                     SELECT documentCode, ret.salesReturnID, documentSystemCode, salesReturnDate, DATE_FORMAT(salesReturnDate,'%Y'), DATE_FORMAT(salesReturnDate,'%m'),
                     det.expenseGLAutoID, det.expenseSystemGLCode, det.expenseGLCode, det.expenseGLDescription, det.expenseGLType, 'cr',
                     ROUND( sum(wacAmount * qty *-1), det.transactionCurrencyDecimalPlaces ), det.transactionCurrency ,det.transactionCurrencyID , det.transactionExchangeRate, det.transactionCurrencyDecimalPlaces,
                     ROUND( sum( (wacAmount * qty *-1) / det.companyLocalExchangeRate), det.companyLocalCurrencyDecimalPlaces), det.companyLocalCurrency,det.companyLocalCurrencyID, det.companyLocalExchangeRate, det.companyLocalCurrencyDecimalPlaces,
                     ROUND( sum( (wacAmount * qty *-1) / det.companyReportingExchangeRate), det.companyReportingCurrencyDecimalPlaces), det.companyReportingCurrency,det.companyReportingCurrencyID, det.companyReportingExchangeRate, det.companyReportingCurrencyDecimalPlaces,
                     {$partyID}, '" . $partySysCode . "', '" . $partyName . "', {$partyCurrencyID}, '" . $partyCurrency . "',  {$partyER} , ROUND( sum( (wacAmount * qty *-1) / {$partyER}), {$partyDP}),  {$partyDP},
                     ret.createdUserID, ret.createdUserName, ret.createdDateTime, ret.createdDateTime, ret.createdUserID, ret.createdUserName, ret.segmentID, ret.segmentCode, ret.companyID, ret.companyCode, ret.createdUserGroup, ret.createdPCID,
                     ret.createdUserID, ret.createdDateTime, ret.createdUserName
                     FROM srp_erp_pos_salesreturndetails det JOIN srp_erp_pos_salesreturn ret ON ret.salesReturnID = det.salesReturnID
                     WHERE det.salesReturnID ={$salesReturnID} GROUP BY expenseGLAutoID");


        /************** ASSET GL CREDIT *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID, systemGLCode, GLCode, GLDescription,
                     GLType, amount_type, transactionAmount, transactionCurrency,transactionCurrencyID, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalAmount, companyLocalCurrency,companyLocalCurrencyID,  companyLocalExchangeRate,
                     companyLocalCurrencyDecimalPlaces,  companyReportingAmount, companyReportingCurrency,companyReportingCurrencyID, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,
                     partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate, partyCurrencyAmount, partyCurrencyDecimalPlaces,
                     confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID,
                     createdUserID, createdDateTime, createdUserName)
                     SELECT documentCode, ret.salesReturnID, documentSystemCode, salesReturnDate, DATE_FORMAT(salesReturnDate,'%Y'), DATE_FORMAT(salesReturnDate,'%m'),
                     det.assetGLAutoID, det.assetSystemGLCode, det.assetGLCode, det.assetGLDescription, det.assetGLType, 'dr',
                     ROUND( sum(wacAmount * qty), det.transactionCurrencyDecimalPlaces ), det.transactionCurrency,det.transactionCurrencyID, det.transactionExchangeRate, det.transactionCurrencyDecimalPlaces,
                     ROUND( sum( (wacAmount * qty) / det.companyLocalExchangeRate), det.companyLocalCurrencyDecimalPlaces), det.companyLocalCurrency, det.companyLocalCurrencyID, det.companyLocalExchangeRate, det.companyLocalCurrencyDecimalPlaces,
                     ROUND( sum( (wacAmount * qty) / det.companyReportingExchangeRate), det.companyReportingCurrencyDecimalPlaces), det.companyReportingCurrency,det.companyReportingCurrencyID, det.companyReportingExchangeRate, det.companyReportingCurrencyDecimalPlaces,
                     {$partyID}, '" . $partySysCode . "', '" . $partyName . "', {$partyCurrencyID}, '" . $partyCurrency . "',  {$partyER} , ROUND( sum( (wacAmount * qty) / {$partyER}), {$partyDP}),  {$partyDP},
                     ret.createdUserID, ret.createdUserName, ret.createdDateTime, ret.createdDateTime, ret.createdUserID, ret.createdUserName, ret.segmentID, ret.segmentCode, ret.companyID, ret.companyCode, ret.createdUserGroup, ret.createdPCID,
                     ret.createdUserID, ret.createdDateTime, ret.createdUserName
                     FROM srp_erp_pos_salesreturndetails det JOIN srp_erp_pos_salesreturn ret ON ret.salesReturnID = det.salesReturnID
                     WHERE det.salesReturnID ={$salesReturnID} GROUP BY assetGLAutoID");


        /************** Revenue GL CREDIT *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID, systemGLCode, GLCode, GLDescription,
                     GLType, amount_type, transactionAmount, transactionCurrency,transactionCurrencyID, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalAmount, companyLocalCurrency,companyLocalCurrencyID,  companyLocalExchangeRate,
                     companyLocalCurrencyDecimalPlaces,  companyReportingAmount, companyReportingCurrency,companyReportingCurrencyID, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,
                     partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate, partyCurrencyAmount, partyCurrencyDecimalPlaces,
                     confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID,
                     createdUserID, createdDateTime, createdUserName)
                     SELECT documentCode, ret.salesReturnID, documentSystemCode, salesReturnDate, DATE_FORMAT(salesReturnDate,'%Y'), DATE_FORMAT(salesReturnDate,'%m'),
                     det.revenueGLAutoID, det.revenueSystemGLCode, det.revenueGLCode, det.revenueGLDescription, det.revenueGLType, 'dr',
                     ROUND( sum(det.transactionAmount), det.transactionCurrencyDecimalPlaces ), det.transactionCurrency,det.transactionCurrencyID, det.transactionExchangeRate, det.transactionCurrencyDecimalPlaces,
                     ROUND( sum(det.companyLocalAmount), det.companyLocalCurrencyDecimalPlaces), det.companyLocalCurrency,det.companyLocalCurrencyID, det.companyLocalExchangeRate, det.companyLocalCurrencyDecimalPlaces,
                     ROUND( sum(det.companyReportingAmount), det.companyReportingCurrencyDecimalPlaces), det.companyReportingCurrency,det.companyReportingCurrencyID, det.companyReportingExchangeRate, det.companyReportingCurrencyDecimalPlaces,
                     {$partyID}, '" . $partySysCode . "', '" . $partyName . "', {$partyCurrencyID}, '" . $partyCurrency . "',  {$partyER} , ROUND( sum(det.companyLocalAmount) / {$partyER}, {$partyDP}),  {$partyDP},
                     ret.createdUserID, ret.createdUserName, ret.createdDateTime, ret.createdDateTime, ret.createdUserID, ret.createdUserName, ret.segmentID, ret.segmentCode, ret.companyID, ret.companyCode, ret.createdUserGroup, ret.createdPCID,
                     ret.createdUserID, ret.createdDateTime, ret.createdUserName
                     FROM srp_erp_pos_salesreturndetails det JOIN srp_erp_pos_salesreturn ret ON ret.salesReturnID = det.salesReturnID
                     WHERE det.salesReturnID ={$salesReturnID} GROUP BY revenueGLAutoID");


        /************** BANK / CUSTOMER GL DEBIT *************/
        $data = $this->db->query("SELECT documentCode, salesReturnID, documentSystemCode, salesReturnDate, customerID, DATE_FORMAT(salesReturnDate,'%Y') e_year, DATE_FORMAT(salesReturnDate,'%m') e_month, 'cr' amountType,
                             returnMode, refundAmount, netTotal, transactionCurrency ,transactionCurrencyID, transactionExchangeRate, transactionCurrencyDecimalPlaces,
                             ROUND(sum(netTotal / companyLocalExchangeRate), companyLocalCurrencyDecimalPlaces) localAmount, companyLocalCurrency,companyLocalCurrencyID, companyLocalExchangeRate, companyLocalCurrencyDecimalPlaces,
                             ROUND(sum(netTotal / companyReportingExchangeRate), companyReportingCurrencyDecimalPlaces) reportAmount, companyReportingCurrency,companyReportingCurrencyID, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,
                             createdUserGroup, createdPCID, createdUserID, createdUserName, createdDateTime, segmentID, segmentCode, companyID, companyCode
                             FROM srp_erp_pos_salesreturn
                             WHERE salesReturnID ={$salesReturnID}")->row();

        $invoiceID = $this->db->query("select invoiceID from srp_erp_pos_salesreturn where salesReturnID=$salesReturnID")->row('invoiceID');
        /************** Group Based Tax Start *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (
                                   documentCode, 
                                   documentMasterAutoID ,
                                   documentSystemCode, 
                                   documentDate, 
                                   documentYear, 
                                   documentMonth,
                                   GLAutoID,
                                   systemGLCode, 
                                   GLCode, 
                                   GLDescription, 
                                   GLType, 
                                   amount_type, 
                                   transactionAmount, 
                                   transactionCurrencyID, 
                                   transactionCurrency, 
                                   transactionExchangeRate,
                                   transactionCurrencyDecimalPlaces, 
                                   companyLocalAmount, 
                                   companyLocalCurrencyID, 
                                   companyLocalCurrency, 
                                   companyLocalExchangeRate,
                                   companyLocalCurrencyDecimalPlaces,  
                                   companyReportingAmount, 
                                   companyReportingCurrencyID, 
                                   companyReportingCurrency, 
                                   companyReportingExchangeRate,
                                   companyReportingCurrencyDecimalPlaces, 
                                   partyAutoID, 
                                   partySystemCode, 
                                   partyName, 
                                   partyCurrencyID, 
                                   partyCurrency, 
                                   partyExchangeRate,
                                   partyCurrencyAmount, 
                                   partyCurrencyDecimalPlaces, 
                                   segmentID, 
                                   segmentCode, 
                                   companyID, 
                                   companyCode, 
                                   createdUserGroup, 
                                   createdPCID, 
                                   createdUserID, 
                                   createdDateTime, 
                                   createdUserName )
                                   SELECT
                                    srp_erp_pos_salesreturn.documentCode, 
                                    srp_erp_pos_salesreturn.salesReturnID as invoiceID,
                                    srp_erp_pos_salesreturn.documentSystemCode,
                                    srp_erp_pos_salesreturn.salesReturnDate AS documentDate,
                                    DATE_FORMAT( srp_erp_pos_salesreturn.salesReturnDate, '%Y' ) AS documentYear,
                                    DATE_FORMAT( srp_erp_pos_salesreturn.salesReturnDate, '%m' ) AS documentMonth,
                                    gl_auto_id AS GLAutoID,
                                    chartOfAC.systemAccountCode AS systemGLCode,
                                    chartOfAC.GLSecondaryCode AS GLCode,
                                    chartOfAC.GLDescription AS GLDescription,
                                    chartOfAC.subCategory AS GLType,
                                    'dr' AS amount_type,
                                    (taxledger.taxAmount) AS transactionamount,
                                    srp_erp_pos_salesreturn.transactionCurrencyID,
                                    srp_erp_pos_salesreturn.transactionCurrency,
                                    srp_erp_pos_salesreturn.transactionExchangeRate,
                                    srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces,
                                    ( taxledger.taxAmount / srp_erp_pos_salesreturn.companyLocalExchangeRate ) AS companyLocalAmount,
                                    srp_erp_pos_salesreturn.companyLocalCurrencyID,
                                    srp_erp_pos_salesreturn.companyLocalCurrency,
                                    srp_erp_pos_salesreturn.companyLocalExchangeRate,
                                    srp_erp_pos_salesreturn.companyLocalCurrencyDecimalPlaces,
                                    ( taxledger.taxAmount / srp_erp_pos_salesreturn.companyReportingExchangeRate ) AS companyReportingAmount,
                                    srp_erp_pos_salesreturn.companyReportingCurrencyID,
                                    srp_erp_pos_salesreturn.companyReportingCurrency,
                                    srp_erp_pos_salesreturn.companyReportingExchangeRate,
                                    srp_erp_pos_salesreturn.companyReportingCurrencyDecimalPlaces,                                    
                                     $partyID ,
                                    '{$partySysCode}',
                                    '{$partyName}',
                                     $partyCurrencyID,
                                    '{$partyCurrency}',                                    
                                     $partyER,
                                     ROUND( taxledger.taxAmount / {$partyER}, {$partyDP}),
                                     $partyDP,
	                                srp_erp_pos_salesreturn.segmentID,
	                                srp_erp_pos_salesreturn.segmentCode,
	                                srp_erp_pos_salesreturn.companyID,
	                                srp_erp_pos_salesreturn.companyCode,
	                                srp_erp_pos_salesreturn.createdUserGroup,
	                                srp_erp_pos_salesreturn.createdPCID,
	                                srp_erp_pos_salesreturn.createdUserID,
	                                srp_erp_pos_salesreturn.createdDateTime,
	                                srp_erp_pos_salesreturn.createdUserName 
                                    FROM (
                                            SELECT
		                                    documentDetailAutoID AS auto_id,
		                                    documentMasterAutoID AS documentMasterAutoID,
		                                    taxGlAutoID AS gl_auto_id,
		                                    SUM( amount ) AS taxAmount,
		                                    taxMasterID 
	                                        FROM
		                                    `srp_erp_taxledger`
		                                    LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
	                                        WHERE
		                                    documentID = 'RET' 
		                                    AND documentMasterAutoID = {$salesReturnID} 
	                                        GROUP BY
		                                    taxMasterID,
		                                    taxGlAutoID ) taxledger
	                                        LEFT JOIN srp_erp_chartofaccounts chartOfAC ON chartOfAC.GLAutoID = taxledger.gl_auto_id
	                                        LEFT JOIN srp_erp_pos_invoicedetail posInvoiceDetail ON posInvoiceDetail.invoiceDetailsID = taxledger.auto_id
	                                        LEFT JOIN srp_erp_pos_invoice inv ON inv.invoiceID = posInvoiceDetail.invoiceID
	                                        LEFT JOIN srp_erp_pos_salesreturn ON srp_erp_pos_salesreturn.salesReturnID = {$salesReturnID} 
	                                        ");
        /************** Group Based Tax End *************/
        //var_dump($this->db->last_query());exit;

        //Bank ledger
        $this->db->query("INSERT INTO srp_erp_bankledger (wareHouseAutoID,
                                                            documentDate,
                                                            transactionType,
                                                            partyType,
                                                            transactionCurrencyID,
                                                            transactionCurrency,
                                                            transactionExchangeRate,
                                                            transactionAmount,
                                                            transactionCurrencyDecimalPlaces,
                                                            bankCurrencyID,
                                                            bankCurrency,
                                                            bankCurrencyExchangeRate,
                                                            bankCurrencyAmount,
                                                            bankCurrencyDecimalPlaces,
                                                            bankGLAutoID,
                                                            bankSystemAccountCode,
                                                            bankGLSecondaryCode,
                                                            createdPCID,
                                                            companyID,
                                                            companyCode,
                                                            segmentID,
                                                            segmentCode,
                                                            partyAutoID, 
                                                            partyCode,
                                                            partyName, 
                                                            partyCurrencyID, 
                                                            partyCurrency, 
                                                            partyCurrencyAmount, 
                                                            partyCurrencyDecimalPlaces, 
                                                            createdUserID,
                                                            createdDateTime,
                                                            createdUserName,
                                                            modifiedPCID,
                                                            modifiedUserID,
                                                            modifiedDateTime,
                                                            modifiedUserName,
                                                            bankName,	
                                                            documentType,
                                                            documentMasterAutoID,	
                                                            documentSystemCode
                                                        )
                                                    SELECT
                                                    wareHouseAutoID,
                                                    salesReturnDate,
                                                    2 as transactionType,
                                                    'CUS' AS partyType,
                                                    transactionCurrencyID,
                                                    transactionCurrency,
                                                    transactionExchangeRate,
                                                    refundAmount,
                                                    transactionCurrencyDecimalPlaces,
                                                    srp_erp_chartofaccounts.bankCurrencyID,
                                                    bankCurrency,
                                                    bankCurrencyExchangeRate,
                                                    bankCurrencyAmount,
                                                    srp_erp_chartofaccounts.bankCurrencyDecimalPlaces,
                                                    bankGLAutoID,
                                                    bankSystemGLCode,
                                                    srp_erp_chartofaccounts.GLSecondaryCode,
                                                    srp_erp_pos_salesreturn.createdPCID,
                                                    srp_erp_pos_salesreturn.companyID,
                                                    srp_erp_pos_salesreturn.companyCode,
                                                    segmentID,
                                                    segmentCode,
                                                    $partyID,
                                                    '{$partySysCode}',
                                                    '{$partyName}',
                                                    $partyCurrencyID,
                                                    '{$partyCurrency}',
                                                    ROUND( refundAmount / {$partyER}, {$partyDP}),
                                                    $partyDP,
                                                    srp_erp_pos_salesreturn.createdUserID,
                                                    srp_erp_pos_salesreturn.createdDateTime,
                                                    srp_erp_pos_salesreturn.createdUserName,
                                                    srp_erp_pos_salesreturn.modifiedPCID,
                                                    srp_erp_pos_salesreturn.modifiedUserID,
                                                    srp_erp_pos_salesreturn.modifiedDateTime,
                                                    srp_erp_pos_salesreturn.modifiedUserName,
                                                    bankGLDescription,
                                                    'RET' AS documentType,
                                                    salesReturnID,	
                                                    documentSystemCode
                                                FROM
                                                    srp_erp_pos_salesreturn 
                                                    JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID=srp_erp_pos_salesreturn.bankGLAutoID
                                                WHERE
                                                    salesReturnID =$salesReturnID");


        $returnMode = $data->returnMode;


        $insertArray = array(
            'documentMasterAutoID' => $data->salesReturnID,
            'documentCode' => 'RET',
            'documentSystemCode' => $data->documentSystemCode,
            'documentDate' => $data->salesReturnDate,
            'documentYear' => $data->e_year,
            'documentMonth' => $data->e_month,


            'transactionCurrencyID' => $data->transactionCurrencyID,
            'transactionCurrency' => $data->transactionCurrency,
            'transactionCurrencyDecimalPlaces' => $data->transactionCurrencyDecimalPlaces,
            'transactionExchangeRate' => $data->transactionExchangeRate,


            'companyLocalCurrencyID' => $data->companyLocalCurrencyID,
            'companyLocalCurrency' => $data->companyLocalCurrency,
            'companyLocalCurrencyDecimalPlaces' => $data->companyLocalCurrencyDecimalPlaces,
            'companyLocalExchangeRate' => $data->companyLocalExchangeRate,


            'companyReportingCurrencyID' => $data->companyReportingCurrencyID,
            'companyReportingCurrency' => $data->companyReportingCurrency,
            'companyReportingCurrencyDecimalPlaces' => $data->companyReportingCurrencyDecimalPlaces,
            'companyReportingExchangeRate' => $data->companyReportingExchangeRate,


            'confirmedDate' => $data->createdDateTime,
            'confirmedByEmpID' => $data->createdUserID,
            'confirmedByName' => $data->createdUserName,

            'approvedDate' => $data->createdDateTime,
            'approvedbyEmpID' => $data->createdUserID,
            'approvedbyEmpName' => $data->createdUserName,

            'partyAutoID' => $partyID,
            'partySystemCode' => $partySysCode,
            'partyName' => $partyName,
            'partyCurrencyID' => $partyCurrencyID,
            'partyCurrency' => $partyCurrency,
            'partyExchangeRate' => $partyER,
            'partyCurrencyDecimalPlaces' => $partyDP,


            'segmentID' => $data->segmentID,
            'segmentCode' => $data->segmentCode,
            'companyID' => $data->companyID,
            'companyCode' => $data->companyCode,
            'createdPCID' => $data->createdPCID,
            'createdUserID' => $data->createdUserID,
            'createdUserName' => $data->createdUserName,
            'createdUserGroup' => $data->createdUserGroup,
            'createdDateTime' => $data->createdDateTime,
        );

        $this->load->model('Pos_config_model');
        $localER = $data->companyLocalExchangeRate;
        $localDP = $data->companyLocalCurrencyDecimalPlaces;
        $repoER = $data->companyReportingExchangeRate;
        $repoDP = $data->companyReportingCurrencyDecimalPlaces;

        $warehouseID = $this->db->query("SELECT warehouseAutoID FROM srp_erp_pos_salesreturn WHERE salesReturnID = $salesReturnID")->row('warehouseAutoID');
        if ($returnMode == 1) { //exchange => return note
            $creditAmount = $data->netTotal;
            $creditNoteGLID = $this->Pos_config_model->load_posGL(2, $warehouseID); //srp_erp_pos_paymentglconfigmaster => creditNote autoID is (2)
            $creditNoteGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                ->from('srp_erp_chartofaccounts')
                ->join('srp_erp_pos_paymentglconfigdetail', 'srp_erp_pos_paymentglconfigdetail.GLCode=srp_erp_chartofaccounts.GLAutoID')
                ->where('GLAutoID', $creditNoteGLID)->get()->row();

            $creditNoteData = $insertArray;
            $creditNoteData['amount_type'] = 'cr';
            $creditNoteData['GLAutoID'] = $creditNoteGL->GLAutoID;
            $creditNoteData['systemGLCode'] = $creditNoteGL->systemAccountCode;
            $creditNoteData['GLCode'] = $creditNoteGL->GLSecondaryCode;
            $creditNoteData['GLDescription'] = $creditNoteGL->GLDescription;
            $creditNoteData['GLType'] = $creditNoteGL->subCategory;
            $creditNoteData['transactionAmount'] = $creditAmount * -1;
            $creditNoteData['companyLocalAmount'] = round(($creditAmount / $localER) * -1, $localDP);
            $creditNoteData['companyReportingAmount'] = round(($creditAmount / $repoER) * -1, $repoDP);
            $creditNoteData['partyCurrencyAmount'] = round(($creditAmount / $partyER) * -1, $partyDP);

            $this->db->insert('srp_erp_generalledger', $creditNoteData);
        }

        if ($returnMode == 2) { //Refund
            $refundAmount = $data->refundAmount;
            //$cashGLID = $this->Pos_config_model->load_posGL(1, $warehouseID); //srp_erp_pos_paymentglconfigmaster => unDepositFund autoID is (1)


            $cashGLID = $this->Pos_config_model->load_posGL($paymentType, $warehouseID);

            $cashGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                ->from('srp_erp_chartofaccounts')->where('GLAutoID', $cashGLID)->get()->row();

            $cashData = $insertArray;
            $cashData['amount_type'] = 'cr';
            $cashData['GLAutoID'] = $cashGL->GLAutoID;
            $cashData['systemGLCode'] = $cashGL->systemAccountCode;
            $cashData['GLCode'] = $cashGL->GLSecondaryCode;
            $cashData['GLDescription'] = $cashGL->GLDescription;
            $cashData['GLType'] = $cashGL->subCategory;
            $cashData['transactionAmount'] = $refundAmount * -1;
            $cashData['companyLocalAmount'] = round(($refundAmount / $localER) * -1, $localDP);
            $cashData['companyReportingAmount'] = round(($refundAmount / $repoER) * -1, $repoDP);
            $cashData['partyCurrencyAmount'] = round(($refundAmount / $partyER) * -1, $partyDP);

            $this->db->insert('srp_erp_generalledger', $cashData);
        }

        if ($returnMode == 3) { //credit-to-customer

            $partyGL = $partyData['partyGL'];
            $customerData = $insertArray;
            $customerData['amount_type'] = 'cr';
            $customerData['GLAutoID'] = $partyGL['receivableAutoID'];
            $customerData['systemGLCode'] = $partyGL['receivableSystemGLCode'];
            $customerData['GLCode'] = $partyGL['receivableGLAccount'];
            $customerData['GLDescription'] = $partyGL['receivableDescription'];
            $customerData['GLType'] = $partyGL['receivableType'];
            $customerData['transactionAmount'] = $data->netTotal * -1;
            $customerData['companyLocalAmount'] = round(($data->netTotal / $localER) * -1, $localDP);
            $customerData['companyReportingAmount'] = round(($data->netTotal / $repoER) * -1, $repoDP);
            $customerData['partyCurrencyAmount'] = round(($data->netTotal / $partyER) * -1, $partyDP);
            $customerData['subLedgerType'] = 3;
            $customerData['subLedgerDesc'] = 'AR';

            $this->db->insert('srp_erp_generalledger', $customerData);
        }
    }

    function get_invReturnBalanceQty($invNo, $invDetID, $itemID)
    {
        return $this->db->query("SELECT wacAmount, ( t1.qty -
                                  (SELECT  IFNULL(SUM(qty),0) FROM srp_erp_pos_salesreturndetails WHERE itemAutoID={$itemID} AND invoiceID={$invNo} AND
                                  invoiceDetailID={$invDetID} )
                                 ) balanceQty
                                 FROM srp_erp_pos_invoicedetail t1 WHERE  invoiceDetailsID={$invDetID}")->row();
    }

    function getReturnCode()
    {
        $query = $this->db->select('serialNo')->from('srp_erp_pos_salesreturn')->where('companyID', $this->common_data['company_data']['company_id'])
            ->order_by('salesReturnID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastRefNo = $lastRefArray['serialNo'];
        $lastRefNo = ($lastRefNo == null) ? 1 : $lastRefArray['serialNo'] + 1;

        $this->load->library('sequence');
        $refCode = $this->sequence->sequence_generator('RET', $lastRefNo);

        return array('refCode' => $refCode, 'lastRefNo' => $lastRefNo);
    }

    function recall_hold_invoice()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouse = $this->common_data['ware_houseID'];
        $curruser = $this->common_data['current_userID'];
        $recall_search = $this->input->post('recall_search');
        if ($recall_search) {
            $where = 'AND documentSystemCode="' . $recall_search . '"';
        } else {
            $where = '';
        }

        $wareHouseAdminYN = $this->db->query("SELECT wareHouseAdminYN FROM srp_erp_warehouse_users WHERE companyID=$companyID AND wareHouseID={$wareHouse} AND userID={$curruser} ")->row_array();
        $superAdminYN = $this->db->query("SELECT superAdminYN FROM srp_erp_warehouse_users WHERE companyID=$companyID AND userID={$curruser} AND superAdminYN=1 ")->row_array();


        if ($wareHouseAdminYN['wareHouseAdminYN'] == 1) {
            $result = $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName
                                 FROM srp_erp_pos_invoicehold t1 WHERE companyID={$companyID} AND wareHouseAutoID={$wareHouse} AND isInvoiced = 0 $where ")->result_array();
        } elseif ($superAdminYN['superAdminYN'] == 1) {
            $result = $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName
                                 FROM srp_erp_pos_invoicehold t1 WHERE companyID={$companyID} AND isInvoiced = 0 $where ")->result_array();
        } else {
            $result = $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName
                                 FROM srp_erp_pos_invoicehold t1 WHERE companyID={$companyID} AND wareHouseAutoID={$wareHouse} AND isInvoiced = 0 $where ")->result_array();
        }
        return $result;
        //echo $this->db->last_query();
    }

    function load_holdInv()
    {
        $wareHouse = $this->common_data['ware_houseID'];
        $holdID = $this->input->post('holdID');
        $masterDet = $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                       (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName
                                       FROM srp_erp_pos_invoicehold t1 WHERE invoiceID={$holdID}")->row_array();

        $itemDet = $this->db->query("SELECT t1.*, (SELECT currentStock FROM srp_erp_warehouseitems WHERE  wareHouseAutoID={$wareHouse}
                                     AND itemAutoID=t1.itemAutoID) AS currentStk,
                                     (SELECT itemImage FROM srp_erp_itemmaster WHERE itemAutoID=t1.itemAutoID) AS itemImage,
                                     (SELECT barcode FROM srp_erp_itemmaster WHERE itemAutoID=t1.itemAutoID) AS barcode
                                     FROM srp_erp_pos_invoiceholddetail t1 WHERE  invoiceID={$holdID}")->result_array();

        return array($masterDet, $itemDet);
    }

    function invReturn_details($returnID)
    {
        $companyID = current_companyID();
        //$wareHouse = $this->common_data['ware_houseID'];

        //$where = array('companyID' => $companyID, 'wareHouseAutoID' => $wareHouse, 't1.salesReturnID' => $returnID);
        $where = array('companyID' => $companyID, 't1.salesReturnID' => $returnID);

        $isExistInv = $this->db->select("t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName,
                                  (SELECT invoiceCode FROM srp_erp_pos_invoice WHERE  invoiceID=t1.invoiceID) AS invCode,
                                  (SELECT EmpShortCode FROM srp_employeesdetails WHERE EIdNo = t1.createdUserID) AS repName")
            ->from("srp_erp_pos_salesreturn t1")
            ->where($where)->get()->row_array();


        if ($isExistInv != null) {
            $invItems = $this->db->query("SELECT * FROM `srp_erp_pos_salesreturndetails`
                JOIN srp_erp_taxledger ON srp_erp_taxledger.documentDetailAutoID=srp_erp_pos_salesreturndetails.salesReturnID
                WHERE `salesReturnID` = '{$returnID}' AND documentID = 'RET'
                GROUP BY invoiceDetailID")->result_array();

            return array(
                0 => 's',
                1 => $isExistInv,
                2 => $invItems
            );
        } else {
            return array('w', 'There is not a return in this number');
        }
    }

    /*Counter*/
    function new_counter()
    {
        $wareHouseID = $this->input->post('wareHouseID');
        $counterCode = $this->input->post('counterCode');
        $counterName = $this->input->post('counterName');

        $data = array(
            'counterCode' => $counterCode,
            'counterName' => $counterName,
            'wareHouseID' => $wareHouseID,
            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdUserGroup' => $this->common_data['user_group'],
            'createdDateTime' => current_date()
        );

        $this->db->insert('srp_erp_pos_counters', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Counter Created Successfully.');
        } else {
            return array('e', 'Error In Counter Created');
        }
    }

    function update_counterDetails()
    {
        $counterID = $this->input->post('updateID');
        $wareHouseID = $this->input->post('wareHouseID');
        $counterCode = $this->input->post('counterCode');
        $counterName = $this->input->post('counterName');

        $upData = array(
            'counterCode' => $counterCode,
            'counterName' => $counterName,
            'wareHouseID' => $wareHouseID,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date']
        );
        $this->db->where('counterID', $counterID)->update('srp_erp_pos_counters', $upData);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Counter Updated Successfully.');
        } else {
            return array('e', 'Error In Counter Updated');
        }
    }

    function delete_counterDetails()
    {
        $counterID = $this->input->post('counterID');
        $upData = array(
            'isActive' => 0,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date']
        );
        $this->db->where('counterID', $counterID)->update('srp_erp_pos_counters', $upData);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Counter Delete Successfully.');
        } else {
            return array('e', 'Error In Counter Delete');
        }
    }

    function load_wareHouseCounters($wareHouse)
    {
        $result = $this->db->select('counterID, counterCode, counterName')->from('srp_erp_pos_counters')
            ->where('wareHouseID', $wareHouse)->where('isActive', 1)
            ->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_counterData($counterID)
    {
        return $this->db->select('counterID, counterCode, counterName')->from('srp_erp_pos_counters')
            ->where('counterID', $counterID)->where('isActive', 1)
            ->get()->row_array();
    }

    function load_wareHouseUsers($wareHouse)
    {

        // CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) eName
        return $this->db->select("autoID, userID, Ecode, Ename2 AS eName ")
            ->from('srp_erp_warehouse_users t1')
            ->join('srp_employeesdetails t2', 't1.userID=t2.EidNo')
            ->where('t1.wareHouseID', $wareHouse)
            ->where('t1.isActive', 1)
            ->where('NOT EXISTS( SELECT userID FROM srp_erp_warehouse_users WHERE userID=t2.EIdNo
                         AND (counterID IS NOT NULL OR counterID != 0))')->get()->result_array();
    }

    function emp_search()
    {
        $keyword = $this->input->get('q');
        $companyID = $this->common_data['company_data']['company_id'];
        $where = "(Ename1 LIKE '%$keyword%' OR Ename2 LIKE '%$keyword%' OR Ename3 LIKE '%$keyword%' OR Ename4 LIKE '%$keyword%' OR ECode LIKE '%$keyword%')  ";
        $where .= "AND t1.Erp_companyID='$companyID'";
        $where .= " AND EIdNo NOT IN(
                      SELECT userID FROM srp_erp_warehouse_users AS userTB
                      JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=userTB.userID AND Erp_companyID='$companyID'
                      WHERE userTB.isActive = 1 GROUP BY userID AND companyID='$companyID'
                   )";

        //CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) empName
        $this->db->select("EIdNo, ECode, Ename2 AS empName");
        $this->db->from('srp_employeesdetails t1');
        $this->db->where($where);
        $query = $this->db->get();

        return $query->result();
    }

    function add_ware_house_user()
    {
        $employeeID = $this->input->post('employeeID');
        $employeeCode = $this->input->post('employeeCode');
        $wareHouseID = $this->input->post('wareHouseID');

        $isExist = $this->db->select("autoID,
                   (SELECT wareHouseLocation FROM srp_erp_warehousemaster WHERE wareHouseAutoID=t1.wareHouseID) AS wareHouse")
            ->from('srp_erp_warehouse_users t1')
            ->where('userID', $employeeID)->where('isActive', 1)->get()->row_array();

        if (empty($isExist)) {
            $data = array(
                'userID' => $employeeID,
                'wareHouseID' => $wareHouseID,
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date(),
            );

            $this->db->insert('srp_erp_warehouse_users', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Employee [ ' . $employeeCode . ' ] successfully added to ware house');
            } else {
                return array('e', 'Error In Counter Created');
            }
        } else {
            return array('e', '[ ' . $employeeCode . ' ] is already added to ' . $isExist['wareHouse'] . ' location');
        }
    }

    function update_ware_house_user()
    {
        $autoID = $this->input->post('updateID');
        $employeeID = $this->input->post('employeeID');
        $employeeCode = $this->input->post('employeeCode');
        $wareHouseID = $this->input->post('wareHouseID');

        $isExist = $this->db->select("autoID,
                   (SELECT wareHouseLocation FROM srp_erp_warehousemaster WHERE wareHouseAutoID=t1.wareHouseID) AS wareHouse")
            ->from('srp_erp_warehouse_users t1')
            ->where('userID=' . $employeeID . ' AND autoID!=' . $autoID . ' AND isActive=1')->get()->row_array();

        if (empty($isExist)) {
            $upData = array(
                'userID' => $employeeID,
                'wareHouseID' => $wareHouseID,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => $this->common_data['current_date']
            );

            $this->db->where('autoID', $autoID)->update('srp_erp_warehouse_users', $upData);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Updated Successfully.');
            } else {
                return array('e', 'Error In Update Process');
            }
        } else {
            return array('e', '[ ' . $employeeCode . ' ] is already added to ' . $isExist['wareHouse'] . ' location');
        }
    }

    function delete_ware_house_user()
    {
        $autoID = $this->input->post('autoID');
        $upData = array(
            'isActive' => 0,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date']
        );

        $this->db->where('autoID', $autoID)->update('srp_erp_warehouse_users', $upData);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Deleted Successfully.');
        } else {
            return array('e', 'Error In Deleted Process.');
        }
    }

    function currencyDenominations($currencyCode)
    {
        return $this->db->select('amount, value, caption, isNote')->from('srp_erp_currencydenomination')
            ->where('currencyCode', $currencyCode)->order_by('amount', 'DESC')->get()->result_array();
    }

    /*Promotion setups*/
    function new_promotion()
    {
        $promoType = $this->input->post('promoType');
        $warehouses = $this->input->post('warehouses[]');
        $range = $this->input->post('range[]');
        $discountPer = $this->input->post('discountPer[]');
        $couponAmount = $this->input->post('couponAmount[]');
        $getFreeQty = $this->input->post('getFreeQty[]');
        $buyQty = $this->input->post('buyQty[]');
        $isApplicableForAllItem = $this->input->post('isApplicableForAllItem');
        $isApplicableForAllWarehouse = $this->input->post('isApplicableForAllWarehouse');
        $promotionDescr = $this->input->post('promotionDescr');
        $fromDate = $this->input->post('fromDate');
        $endDate = $this->input->post('endDate');

        $data = array(
            'promotionTypeID' => $promoType,
            'description' => $promotionDescr,
            'dateFrom' => $fromDate,
            'dateTo' => $endDate,
            'isActive' => 0,
            'isApplicableForAllItem' => $isApplicableForAllItem,
            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            //'createdUserGroup' => $this->common_data['user_group'],
            'createdDateTime' => current_date()
        );

        $this->db->trans_start();
        $this->db->insert('srp_erp_pos_promotionsetupmaster', $data);
        $promotionID = $this->db->insert_id();


        /*$proWarehouses = array();
        foreach( $warehouses as $key => $house ){
            $proWarehouses[$key]['promotionID'] = $promotionID;
            $proWarehouses[$key]['wareHouseID'] = $house;
            $proWarehouses[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $proWarehouses[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $proWarehouses[$key]['createdPCID'] = $this->common_data['current_pc'];
            $proWarehouses[$key]['createdUserID'] = $this->common_data['current_userID'];
            $proWarehouses[$key]['createdUserName'] = $this->common_data['current_user'];
            $proWarehouses[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $proWarehouses[$key]['createdDateTime'] = current_date();
        }
        $this->db->insert_batch('srp_erp_pos_promotionwarehouses', $proWarehouses);


        $promoDet = array();
        foreach($range as $key => $row){

            if($promoType == 1){
                $promoDet[$key]['startRangeAmount'] = $row;
                $promoDet[$key]['discountPrc'] = $discountPer[$key];
            }
            elseif($promoType == 2){
                $promoDet[$key]['startRangeAmount'] = $row;
                $promoDet[$key]['coupenAmount'] = $couponAmount[$key];
            }
            elseif($promoType == 3){
                $promoDet[$key]['buyQty'] = $buyQty[$key];
                $promoDet[$key]['getFreeQty'] = $getFreeQty[$key];
            }

            $promoDet[$key]['promotionID'] = $promotionID;
            $promoDet[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $promoDet[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $promoDet[$key]['createdPCID'] = $this->common_data['current_pc'];
            $promoDet[$key]['createdUserID'] = $this->common_data['current_userID'];
            $promoDet[$key]['createdUserName'] = $this->common_data['current_user'];
            $promoDet[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $promoDet[$key]['createdDateTime'] = current_date();
        }
        $this->db->insert_batch('srp_erp_pos_promotionsetupdetail', $promoDet);*/

        $this->insert_promotion_warehouses($promotionID);
        $this->insert_promotion_details($promotionID);

        $this->db->trans_complete();
        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            return array('e', 'Error In Promotion Create Process.');
        } else {
            $this->db->trans_commit();
            return array('s', 'Successfully.', $promotionID);
        }
    }

    function update_promotion()
    {
        $updateID = $this->input->post('updateID');
        $promoType = $this->input->post('promoType');
        $isApplicableForAllItem = $this->input->post('isApplicableForAllItem');
        $promotionDescr = $this->input->post('promotionDescr');
        $fromDate = $this->input->post('fromDate');
        $endDate = $this->input->post('endDate');

        $data = array(
            'promotionTypeID' => $promoType,
            'description' => $promotionDescr,
            'dateFrom' => $fromDate,
            'dateTo' => $endDate,
            'isActive' => 0,
            'isApplicableForAllItem' => $isApplicableForAllItem,

            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date()
        );

        $this->db->trans_start();
        $this->db->where('promotionID', $updateID)->update('srp_erp_pos_promotionsetupmaster', $data);

        $this->db->where('promotionID', $updateID)->delete('srp_erp_pos_promotionwarehouses');
        $this->db->where('promotionID', $updateID)->delete('srp_erp_pos_promotionsetupdetail');


        $this->insert_promotion_warehouses($updateID);
        $this->insert_promotion_details($updateID);

        $this->db->trans_complete();
        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            return array('e', 'Error is Promotion update');
        } else {
            $this->db->trans_commit();
            return array('s', 'Successfully Updated', $updateID);
        }
    }

    function insert_promotion_warehouses($promotionID)
    {
        $warehouses = $this->input->post('warehouses[]');
        $proWarehouses = array();

        foreach ($warehouses as $key => $house) {
            $proWarehouses[$key]['promotionID'] = $promotionID;
            $proWarehouses[$key]['wareHouseID'] = $house;
            $proWarehouses[$key]['companyID'] = current_companyID();
            $proWarehouses[$key]['companyCode'] = current_companyCode();
            $proWarehouses[$key]['createdPCID'] = current_pc();
            $proWarehouses[$key]['createdUserID'] = current_userID();
            $proWarehouses[$key]['createdUserName'] = current_employee();
            $proWarehouses[$key]['createdUserGroup'] = current_user_group();
            $proWarehouses[$key]['createdDateTime'] = current_date();
        }
        $this->db->insert_batch('srp_erp_pos_promotionwarehouses', $proWarehouses);
    }

    function insert_promotion_details($promotionID)
    {
        $promoType = $this->input->post('promoType');
        $range = $this->input->post('range[]');
        $discountPer = $this->input->post('discountPer[]');
        $couponAmount = $this->input->post('couponAmount[]');
        $getFreeQty = $this->input->post('getFreeQty[]');
        $buyQty = $this->input->post('buyQty[]');
        $promoDet = array();

        foreach ($range as $key => $row) {

            if ($promoType == 1) {
                $promoDet[$key]['startRangeAmount'] = $row;
                $promoDet[$key]['discountPrc'] = $discountPer[$key];
            } elseif ($promoType == 2) {
                $promoDet[$key]['startRangeAmount'] = $row;
                $promoDet[$key]['coupenAmount'] = $couponAmount[$key];
            } elseif ($promoType == 3) {
                $promoDet[$key]['buyQty'] = $buyQty[$key];
                $promoDet[$key]['getFreeQty'] = $getFreeQty[$key];
            }

            $promoDet[$key]['promotionID'] = $promotionID;
            $promoDet[$key]['companyID'] = current_companyID();
            $promoDet[$key]['companyCode'] = current_companyCode();
            $promoDet[$key]['createdPCID'] = current_pc();
            $promoDet[$key]['createdUserID'] = current_userID();
            $promoDet[$key]['createdUserName'] = current_employee();
            $promoDet[$key]['createdUserGroup'] = current_user_group();
            $promoDet[$key]['createdDateTime'] = current_date();
        }

        $this->db->insert_batch('srp_erp_pos_promotionsetupdetail', $promoDet);
        return $promoDet;
    }

    function delete_promotion()
    {
        $promoID = $this->input->post('promoID');

        $this->db->trans_start();

        $this->db->where('promotionID', $promoID)->delete('srp_erp_pos_promotionsetupmaster');
        $this->db->where('promotionID', $promoID)->delete('srp_erp_pos_promotionwarehouses');
        $this->db->where('promotionID', $promoID)->delete('srp_erp_pos_promotionsetupdetail');

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('e', 'Error in delete process');
        } else {
            $this->db->trans_commit();
            return array('s', 'Successfully deleted');
        }
    }

    function get_promotionMasterDet($promo_ID)
    {
        $master = $this->db->select('promotionTypeID, description, dateFrom, dateTo, isApplicableForAllItem')
            ->from('srp_erp_pos_promotionsetupmaster')->where('promotionID', $promo_ID)->get()->row_array();

        $warehouses = $this->db->select('house.wareHouseAutoID AS wareHID')
            ->from('srp_erp_pos_promotionwarehouses proWare')
            ->join('srp_erp_warehousemaster house', 'house.wareHouseAutoID=proWare.wareHouseID')
            ->where('promotionID', $promo_ID)->get()->result_array();
        return array(
            'master' => $master,
            'warehouses' => $warehouses
        );
    }

    function get_promotionDet($promo_ID)
    {
        return $this->db->select('startRangeAmount, discountPrc, coupenAmount, buyQty, getFreeQty')
            ->from('srp_erp_pos_promotionsetupdetail')->where('promotionID', $promo_ID)
            ->get()->result_array();
    }

    function load_applicableItems($promo_ID)
    {
        $companyID = current_companyID();
        return $this->db->query("SELECT t1.itemAutoID, itemSystemCode, itemDescription, t1.promotionID
                                 FROM srp_erp_pos_promotionapplicableitems t1
                                 JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                 WHERE t2.companyID={$companyID} AND isActive=1 AND t1.promotionID={$promo_ID}")->result_array();
    }

    function save_promotionItems()
    {
        $promotionID = $this->input->post('promoID');
        $selectedItems = $this->input->post('selectedItems[]');
        $data = array();

        $this->db->trans_start();
        $this->db->where('promotionID', $promotionID)->delete('srp_erp_pos_promotionapplicableitems');

        foreach ($selectedItems as $key => $item) {
            $data[$key]['promotionID'] = $promotionID;
            $data[$key]['itemAutoID'] = $item;
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['companyCode'] = current_companyCode();
            $data[$key]['createdPCID'] = current_pc();
            $data[$key]['createdUserID'] = current_userID();
            $data[$key]['createdUserName'] = current_employee();
            $data[$key]['createdUserGroup'] = current_user_group();
            $data[$key]['createdDateTime'] = current_date();
        }

        $this->db->insert_batch('srp_erp_pos_promotionapplicableitems', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('e', 'Error in applicable items saving');
        } else {
            $this->db->trans_commit();
            return array('s', 'Applicable items saved successfully');
        }
    }

    function promotionDetail()
    {
        $currentDate = date_format(date_create(current_date()), 'Y-m-d');
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];
        $couponArray = array();
        $freeIssueArray = array();

        $coupon = $this->db->query("SELECT promo.promotionID, description, isApplicableForAllItem
                                    FROM srp_erp_pos_promotionsetupmaster promo
                                    JOIN srp_erp_pos_promotionwarehouses house ON promo.promotionID = house.promotionID
                                    WHERE promo.dateFrom <= '$currentDate' AND promo.dateTo >= '$currentDate'
                                    AND promotionTypeID = 2 AND house.wareHouseID = {$wareHouse}
                                    AND promo.companyID = {$companyID}")->result_array();

        $freeIssue = $this->db->query("SELECT promo.promotionID, description, isApplicableForAllItem
                                    FROM srp_erp_pos_promotionsetupmaster promo
                                    JOIN srp_erp_pos_promotionwarehouses house ON promo.promotionID = house.promotionID
                                    WHERE promo.dateFrom <= '$currentDate' AND promo.dateTo >= '$currentDate'
                                    AND promotionTypeID = 3 AND house.wareHouseID = {$wareHouse}
                                    AND promo.companyID = {$companyID}")->result_array();

        foreach ($coupon as $key => $pro) {
            $promoID = $pro['promotionID'];
            $couponArray[$key] = array(
                'master' => $pro,
                'promoSetup' => $this->get_promotionDet($promoID),
                'promoItems' => $this->load_applicableItems($promoID),
            );
        }

        foreach ($freeIssue as $key => $pro) {
            $promoID = $pro['promotionID'];
            $isApplicableForAllItems = $pro['isApplicableForAllItem'];

            $freeIssueArray[$key] = array(
                'master' => $pro,
                'promoSetup' => $this->get_promotionDet($promoID),
                'promoItems' => (($isApplicableForAllItems == 0) ? $this->load_applicableItems($promoID) : null),
            );
        }

        return array(
            'coupon' => $couponArray,
            'freeIssue' => $freeIssueArray
        );
    }

    /*End of Promotion setups*/

    function get_pos_shift()
    {
        $shiftID = $this->db->select('shiftID')->from('srp_erp_pos_shiftdetails')
            ->where('wareHouseID', current_warehouseID())->where('empID', current_userID())
            ->where('isClosed', 0)->get()->row('shiftID');


        return $shiftID;
    }

    function get_GL_Entries_items($menusSalesID)
    {

        $q = "SELECT
                    SUM(item.salesPriceNetTotal) AS totalGL,
                    chartOfAccount.systemAccountCode,
                    chartOfAccount.GLSecondaryCode,
                    chartOfAccount.GLDescription,
                    chartOfAccount.subCategory,
                    item.*
                FROM
                    srp_erp_pos_menusalesitems item
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
                WHERE
                    item.menuSalesID = " . $menusSalesID . " AND 
                GROUP BY
                    revenueGLAutoID";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_posr_sales($shiftID)
    {
        $q = "SELECT
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                CURDATE() AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales' AS documentNarration,
                '' AS chequeNumber,
                item.revenueGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                sum(item.menuSalesPrice) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                -- AS companyLocalExchangeRate,
                -- AS companyLocalAmount,
                -- AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                -- AS companyReportingExchangeRate,
                -- AS companyReportingAmount,
                -- AS companyReportingCurrencyDecimalPlaces,
                -- AS confirmedByEmpID,
                -- AS confirmedByName,
                -- AS confirmedDate,
                -- AS approvedDate,
                -- AS approvedbyEmpID,
                -- AS approvedbyEmpName,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                SUM(item.salesPriceNetTotal) AS totalGL,
                chartOfAccount.systemAccountCode,
                chartOfAccount.GLSecondaryCode,
                chartOfAccount.GLDescription,
                chartOfAccount.subCategory,
                menusalesmaster.shiftID
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            GROUP BY
                revenueGLAutoID";
        $q = "SELECT
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                CURDATE() AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales' AS documentNarration,
                '' AS chequeNumber,
                item.revenueGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                sum(item.menuSalesPrice) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_default_currencyID ,menusalesmaster.companyID ) AS companyLocalExchangeRate,
                sum(item.menuSalesPrice)/(getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_default_currencyID ,menusalesmaster.companyID ))  AS companyLocalAmount,
                getDecimalPlaces(company.company_default_currencyID) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_reporting_currencyID ,menusalesmaster.companyID) AS companyReportingExchangeRate,
                sum(item.menuSalesPrice)/(getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_reporting_currencyID ,menusalesmaster.companyID )) AS companyReportingAmount,
                getDecimalPlaces(company.company_reporting_currencyID) AS companyReportingCurrencyDecimalPlaces,
                 
                
                -- AS confirmedByEmpID,
                -- AS confirmedByName,
                -- AS confirmedDate,
                -- AS approvedDate,
                -- AS approvedbyEmpID,
                -- AS approvedbyEmpName,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                --'' AS createdUserGroup,
                --'' AS createdPCID,
                --'' AS createdUserID,
                --NOW() AS createdDateTime,
                --'' AS createdUserName,
                --'' AS modifiedPCID,
                --'' AS modifiedUserID,
                --NULL AS modifiedDateTime,
                --'' AS modifiedUserName,
                --NOW() AS `timestamp`,
                SUM(item.salesPriceNetTotal) AS totalGL,
                chartOfAccount.systemAccountCode,
                chartOfAccount.GLSecondaryCode,
                chartOfAccount.GLDescription,
                chartOfAccount.subCategory,
                menusalesmaster.shiftID
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            WHERE
                menusalesmaster.shiftID =  '" . $shiftID . "'
            GROUP BY
                revenueGLAutoID";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function insert_batch_srp_erp_generalledger($data)
    {
        $result = $this->db->insert_batch('srp_erp_generalledger', $data);
        return $result;
    }

    function update_usergroup_isactive()
    {

        $data['isActive'] = ($this->input->post('chkedvalue'));

        $this->db->where('userGroupMasterID', $this->input->post('userGroupMasterID'));
        $result = $this->db->update('srp_erp_pos_auth_usergroupmaster', $data);
        if ($result) {
            return array('error' => 0, 'message' => 'successfully updated', 'result' => $result);
        }
    }

    function save_userGroup()
    {
        $description = $this->input->post('description');
        $posType = $this->input->post('posType');
        $companyID = current_companyID();
        $q = "SELECT
                    description
                FROM
                    srp_erp_pos_auth_usergroupmaster
                WHERE
                   description = '" . $description . "' AND posType = $posType  AND companyID = $companyID ";
        $result = $this->db->query($q)->row_array();


        if ($result) {
            return array('e', 'User Group Exist');
        } else {
            $data = array(
                'description' => $description,
                'posType' => $posType,
                'companyID' => $this->common_data['company_data']['company_id'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_pos_auth_usergroupmaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'User Group Created Successfully.');
            } else {
                return array('e', 'Error In User Group Creation');
            }
        }
    }

    function update_userGroup()
    {
        $userGroupMasterID = $this->input->post('userGroupMasterID');
        $description = $this->input->post('description');
        $companyID = current_companyID();
        $q = "SELECT
                    description
                FROM
                    srp_erp_pos_auth_usergroupmaster
                WHERE
                   description = '" . $description . "' AND userGroupMasterID != $userGroupMasterID AND companyID = $companyID ";
        $result = $this->db->query($q)->row_array();


        if ($result) {
            return array('e', 'User Group Exist');
        } else {
            $data = array(
                'description' => $description,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => $this->common_data['current_date']
            );
            $this->db->where('userGroupMasterID', $userGroupMasterID)->update('srp_erp_pos_auth_usergroupmaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'User Group Updated Successfully.');
            } else {
                return array('e', 'Error In Updating User Group');
            }
        }
    }

    function save_usergroup_users()
    {
        $empID = $this->input->post('empID');
        $userGroupMasterID = $this->input->post('userGroupMasterID');
        $companyID = current_companyID();

        $data['pos_userGroupMasterID'] = null;
        $this->db->where('Erp_companyID', $companyID);
        $this->db->where('pos_userGroupMasterID', $userGroupMasterID);
        $this->db->update('srp_employeesdetails', $data);
        $result = true;
        if ($empID) {
            foreach ($empID as $val) {
                $datas['pos_userGroupMasterID'] = $userGroupMasterID;
                $this->db->where('EIdNo', $val);
                $result = $this->db->update('srp_employeesdetails', $datas);
            }
        }

        if ($result) {
            return array('s', 'Employees successfully added to User Group.');
        } else {
            return array('e', 'Error In adding Employees to User Group.');
        }
    }

    function fetch_assigned_users()
    {
        $result = $this->db->select('*')->from('srp_employeesdetails')->where("pos_userGroupMasterID", $this->input->post("userGroupMasterID"))->where("Erp_companyID", current_companyID())->get()->result_array();
        return $result;
    }

    function getInvoiceSequenceCode()
    {
        $WarehouseID = current_warehouseID();

        $querys = $this->db->select('wareHouseCode')->from('srp_erp_warehousemaster')->where('wareHouseAutoID', $WarehouseID)->get();
        $WarehouseCode = $querys->row_array();
        $code = $WarehouseCode['wareHouseCode'] ?? '';

        $query = $this->db->select('invoiceSequenceNo')->from('srp_erp_pos_invoice')->where('companyID', $this->common_data['company_data']['company_id'])->where('wareHouseAutoID', $WarehouseID)
            ->order_by('invoiceID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastINVNo = $lastRefArray['invoiceSequenceNo'] ?? '';
        $lastINVNo = ($lastINVNo == null) ? 1 : $lastRefArray['invoiceSequenceNo'] + 1;
        $companyID = current_companyID();
        $queryscomp = $this->db->select('company_code')->from('srp_erp_company')->where('company_id', $companyID)->get();
        $compCode = $queryscomp->row_array();
        $company_code = $compCode['company_code'];

        $sequenceCode['sequenceCode'] = ($company_code . '/' . $code . str_pad($lastINVNo, 6, '0', STR_PAD_LEFT));
        $sequenceCode['lastINVNo'] = $lastINVNo;
        return $sequenceCode;
    }


    function submit_pos_payments()
    {
        //exit;
        $totalPayment = $this->input->post('paid');
        $totVal = $this->input->post('totVal');
        //$discVal = $this->input->post('discVal');
        $netTotalAmount = $this->input->post('total_payable_amt');
        $customerID = $this->input->post('customerID');
        $cardTotalAmount = $this->input->post('cardTotalAmount');
        $CreditSalesAmnt = $this->input->post('CreditSalesAmnt');
        $pos_itemwisediscount = $this->input->post('itemDisAmount');
        $itemwisediscountamount = 0;
        $discVal = 0;

        if ($pos_itemwisediscount != 0) {
            foreach ($pos_itemwisediscount as $totval) {
                $discVal += $totval;
            }
        }


        $pos_pay = $this->input->post('paymentTypes');

        if (!empty($this->input->post('paymentTypes[26]'))) {
            $CreditSalesAmnt = $this->input->post('paymentTypes[26]');
        }

        $CreditNoteAmnt = $this->input->post('paymentTypes[2]');
        $creditNote_invID = $this->input->post('creditNote-invID');
        $cash[1] = $this->input->post('paymentTypes[1]');
        $CreditNote[2] = $this->input->post('paymentTypes[2]');
        $MasterCard[3] = $this->input->post('paymentTypes[3]');
        $VisaCard[4] = $this->input->post('paymentTypes[4]');
        $AMEX[6] = $this->input->post('paymentTypes[6]');
        $CreditSales[7] = $this->input->post('paymentTypes[7]');
        $loyaltypaymnt = $this->input->post('paymentTypes[42]');

        $item = $this->input->post('itemID[]');
        $itemUOM = $this->input->post('itemUOM[]');
        $itemQty = $this->input->post('itemQty[]');
        $itemPrice = $this->input->post('itemPrice[]');
        $itemDis = $this->input->post('itemDis[]');
        $taxFormula = $this->input->post('taxFormula[]');

        //        $invCodeDet = $this->getInvoiceCode();
        //        $lastRefNo = $invCodeDet['lastRefNo'];
        //        $refCode = $invCodeDet['refCode'];
        //
        //        $invSequenceCodeDet = $this->getInvoiceSequenceCode();
        //        $lastINVNo = $invSequenceCodeDet['lastINVNo'];
        //        $sequenceCode = $invSequenceCodeDet['sequenceCode'];

        $financeYear = $this->db->select('companyFinanceYearID, beginingDate, endingDate')->from('srp_erp_companyfinanceyear')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();


        $financePeriod = $this->db->select('companyFinancePeriodID, dateFrom, dateTo')->from('srp_erp_companyfinanceperiod')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();

        $invoiceDate = format_date(date('Y-m-d'));
        $currentShiftData = $this->isHaveNotClosedSession();

        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $tr_currency = $this->common_data['company_data']['company_default_currency'];
        $localConversion = currency_conversion($tr_currency, $com_currency, $netTotalAmount);
        $localConversionRate = $localConversion['conversion'];
        $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
        $rep_currency = $this->common_data['company_data']['company_reporting_currency'];
        $rep_currDPlace = $this->common_data['company_data']['company_reporting_decimal'];

        $transConversion = currency_conversion($tr_currency, $tr_currency, $netTotalAmount);
        $tr_currDPlace = $transConversion['DecimalPlaces'];
        $transConversionRate = $transConversion['conversion'];
        $reportConversion = currency_conversion($tr_currency, $rep_currency, $netTotalAmount);
        $reportConversionRate = $reportConversion['conversion'];
        $wareHouseData = $this->get_wareHouse();

        $isStockCheck = 0;
        $isGroupBasedPolicyYN = getPolicyValues('GBT', 'All');
        $isItemBatchPolicy = getPolicyValues('IB', 'All');

        $invArray = array(
            //            'documentSystemCode' => $refCode,
            'isGroupBasedTax' => $isGroupBasedPolicyYN,
            'documentCode' => 'POS',
            //            'serialNo' => $lastRefNo,
            //            'invoiceSequenceNo' => $lastINVNo,
            //            'invoiceCode' => $sequenceCode,
            'financialYearID' => $financeYear['companyFinanceYearID'],
            'financialPeriodID' => $financePeriod['companyFinancePeriodID'],
            'FYBegin' => $financeYear['beginingDate'],
            'FYEnd' => $financeYear['endingDate'],
            'FYPeriodDateFrom' => $financePeriod['dateFrom'],
            'FYPeriodDateTo' => $financePeriod['dateTo'],
            'customerID' => $customerID,
            /*'customerCode' => $customerCode,*/
            'invoiceDate' => $invoiceDate,
            'counterID' => $currentShiftData['counterID'],
            'shiftID' => $currentShiftData['shiftID'],
            'generalDiscountPercentage' => $this->input->post('gen_disc_percentage'),
            'generalDiscountAmount' => $this->input->post('gen_disc_amount_hide'),
            'subTotal' => $totVal,
            'netTotal' => $netTotalAmount,
            'paidAmount' => $totalPayment,
            'balanceAmount' => ($netTotalAmount - $totalPayment),
            'cashAmount' => $totalPayment - $cardTotalAmount,
            /*'chequeAmount' => $chequeAmount,*/
            'cardAmount' => $cardTotalAmount,
            'discountAmount' => $discVal,
            'creditNoteID' => $creditNote_invID,
            'creditNoteAmount' => $CreditNoteAmnt,
            'creditSalesAmount' => $CreditSalesAmnt,
            'isCreditSales' => $this->input->post('isCreditSale'),


            'memberID' => $this->input->post('memberidhn'),
            'memberName' => $this->input->post('membernamehn'),
            'memberContactNo' => $this->input->post('contactnumberhn'),
            'memberEmail' => $this->input->post('mailaddresshn'),
            /*'chequeNo' => $chequeNO,
            'chequeDate' => $chequeCashDate,*/
            'companyLocalCurrencyID' => $localConversion['currencyID'],
            'companyLocalCurrency' => $com_currency,
            'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
            'companyLocalExchangeRate' => $localConversionRate,
            'transactionCurrencyID' => $localConversion['trCurrencyID'],
            'transactionCurrency' => $tr_currency,
            'transactionCurrencyDecimalPlaces' => $tr_currDPlace,
            'transactionExchangeRate' => $transConversionRate,
            'companyReportingCurrencyID' => $reportConversion['currencyID'],
            'companyReportingCurrency' => $rep_currency,
            'companyReportingCurrencyDecimalPlaces' => $rep_currDPlace,
            'companyReportingExchangeRate' => $reportConversionRate,
            'wareHouseAutoID' => $wareHouseData['wareHouseAutoID'],
            'wareHouseCode' => $wareHouseData['wareHouseCode'],
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'wareHouseDescription' => $wareHouseData['wareHouseDescription'],
            'segmentID' => $wareHouseData['segmentID'],
            'segmentCode' => $wareHouseData['segmentCode'],
            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdUserGroup' => $this->common_data['user_group'],
            'createdDateTime' => current_date(),
        );

        //==============START: PROMOTION DISCOUNT =======================
        $wastage = false;
        $wastage_glID = '';
        $promotionID = $this->input->post('promotionID');
        if ($promotionID) {
            $r = $this->Pos_restaurant_model->get_customerInfo($promotionID);
            if (!empty($r)) {
                $promotionDiscount = $r['commissionPercentage'];
                if ($r['customerTypeMasterID'] == 3) {
                    $wastage = true;
                    $wastage_glID = $r['expenseGLAutoID'];
                }
            }
            $invArray['isPromotion'] = 1;
            $invArray['promotionID'] = $promotionID;
            $invArray['promotionDiscount'] = $promotionDiscount;
            if ($invArray['promotionDiscount'] > 0) {
                $invArray['promotionDiscountAmount'] = ($invArray['promotionDiscount'] / 100) * $this->input->post('netTot_after_g_disc');
            }
        } else {
            $invArray['isPromotion'] = 0;
            $invArray['promotionID'] = 0;
            $invArray['promotionDiscount'] = 0;
            $invArray['promotionDiscountAmount'] = 0;
        }
        //==============END: PROMOTION DISCOUNT =======================

        if ($CreditNoteAmnt > 0 || $CreditSalesAmnt > 0) {
            $invArray['cardAmount'] = 0;
        }
        $invArray['cardRefNo'] = 0;
        $invArray['cardBank'] = 0;
        $invArray['cardNumber'] = 0;

        if (!empty($this->input->post('memberidhn'))) {
            $invArray['creditSalesAmount'] = $totalPayment - $cardTotalAmount;
            $invArray['isCreditSales'] = 1;
            $invArray['cashAmount'] = 0;
        }

        if ($customerID == 0) {
            $bankData = $this->db->query("SELECT receivableAutoID, receivableSystemGLCode, receivableGLAccount,
                                          receivableDescription, receivableType
                                          FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();
            $invArray['bankGLAutoID'] = $bankData['receivableAutoID'];
            $invArray['bankSystemGLCode'] = $bankData['receivableSystemGLCode'];
            $invArray['bankGLAccount'] = $bankData['receivableGLAccount'];
            $invArray['bankGLDescription'] = $bankData['receivableDescription'];
            $invArray['bankGLType'] = $bankData['receivableType'];

            /*************** item ledger party currency ***********/
            $partyData = array(
                'cusID' => 0,
                'sysCode' => 'CASH',
                'cusName' => 'CASH',
                'partyCurID' => $localConversion['trCurrencyID'],
                'partyCurrency' => $tr_currency,
                'partyDPlaces' => $tr_currDPlace,
                'partyER' => $transConversionRate,
            );

        } else {

            $cusData = $this->db->query("SELECT customerAutoID, customerSystemCode, customerName, receivableAutoID,
                                             receivableSystemGLCode, receivableGLAccount, receivableDescription, receivableType,
                                             customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces,customerAddress1,customerTelephone
                                             FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

            $partyData = currency_conversion($tr_currency, $cusData['customerCurrency']);

            $invArray['customerCurrencyID'] = $cusData['customerCurrencyID'];
            $invArray['customerCurrency'] = $cusData['customerCurrency'];
            $invArray['customerCurrencyExchangeRate'] = $partyData['conversion'];
            $invArray['customerCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];

            $invArray['customerReceivableAutoID'] = $cusData['receivableAutoID'];
            $invArray['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
            $invArray['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
            $invArray['customerReceivableDescription'] = $cusData['receivableDescription'];
            $invArray['customerReceivableType'] = $cusData['receivableType'];

            /*************** item ledger party currency ***********/

            $partyData = array(
                'cusID' => $cusData['customerAutoID'],
                'sysCode' => $cusData['customerSystemCode'],
                'cusName' => $cusData['customerName'],
                'partyCurID' => $cusData['customerCurrencyID'],
                'partyCurrency' => $cusData['customerCurrency'],
                'partyDPlaces' => $cusData['customerCurrencyDecimalPlaces'],
                'partyER' => $partyData['conversion'],
                'partyGL' => $cusData,
            );
        }

        $this->db->trans_start();

        //        $this->db->query("LOCK TABLES srp_erp_pos_invoice WRITE,srp_erp_documentcodemaster WRITE,srp_erp_warehouse_users as users WRITE,
        //        srp_erp_warehousemaster as w_master WRITE,srp_erp_warehousemaster WRITE,srp_erp_company WRITE;");

        $invCodeDet = $this->getInvoiceCode();
        $lastRefNo = $invCodeDet['lastRefNo'];
        $refCode = $invCodeDet['refCode'];

        $invSequenceCodeDet = $this->getInvoiceSequenceCode();
        $lastINVNo = $invSequenceCodeDet['lastINVNo'];
        $sequenceCode = $invSequenceCodeDet['sequenceCode'];


        $invArray['documentSystemCode'] = $refCode;
        $invArray['serialNo'] = $lastRefNo;
        $invArray['invoiceSequenceNo'] = $lastINVNo;
        $invArray['invoiceCode'] = $sequenceCode;

        $this->db->insert('srp_erp_pos_invoice', $invArray);
        $invID = $this->db->insert_id();
    
        if ($customerID == 0) {
        } else {
            $companyID = current_companyID();
            $loyaltycard = $this->db->query("SELECT cardMasterID,barcode FROM srp_erp_pos_loyaltycard WHERE companyID={$companyID} AND customerID=$customerID AND isActive=1")->row_array();
            $points = $this->db->query("SELECT poinforPuchaseAmount,purchaseRewardPoint,pointSetupID,currencyID,amount,loyaltyPoints,priceToPointsEarned FROM srp_erp_loyaltypointsetup WHERE companyID={$companyID} AND isActive=1")->row_array();

            if (!empty($loyaltycard)) {

                if (!empty($points) && ($netTotalAmount >= $points['priceToPointsEarned'])) {

                    if (!empty($loyaltypaymnt) && $loyaltypaymnt > 0) {
                        $totpts = ($points['purchaseRewardPoint'] / $points['poinforPuchaseAmount']) * ($netTotalAmount - $loyaltypaymnt);
                        $topUpAmount = ($netTotalAmount - $loyaltypaymnt);

                        $ptsm['cardMasterID'] = $loyaltycard['cardMasterID'];
                        $ptsm['barCode'] = $loyaltycard['barcode'];
                        $ptsm['posCustomerAutoID'] = $customerID;
                        $ptsm['topUpAmount'] = 0;
                        $ptsm['transationType'] = 1;
                        $ptsm['points'] = ($loyaltypaymnt / $points['amount']) * -1; //$points['amount'] is exchange rate of 1 point.
                        $ptsm['pointSetupID'] = $points['pointSetupID'];
                        $ptsm['invoiceID'] = $invID;
                        $ptsm['companyID'] = $companyID;
                        $ptsm['createdPCID'] = $this->common_data['current_pc'];
                        $ptsm['createdUserID'] = $this->common_data['current_userID'];
                        $ptsm['createdUserName'] = $this->common_data['current_user'];
                        $ptsm['createdUserGroup'] = $this->common_data['user_group'];
                        $ptsm['createdDateTime'] = current_date();

                        $this->db->insert('srp_erp_pos_loyaltytopup', $ptsm);
                    } else {
                        $totpts = ($points['purchaseRewardPoint'] / $points['poinforPuchaseAmount']) * $netTotalAmount;
                        $topUpAmount = $netTotalAmount;
                    }
                    if ($totpts > 0) {
                        $pts['cardMasterID'] = $loyaltycard['cardMasterID'];
                        $pts['barCode'] = $loyaltycard['barcode'];
                        $pts['posCustomerAutoID'] = $customerID;
                        $pts['topUpAmount'] = $topUpAmount;
                        $pts['points'] = $totpts;
                        $pts['pointSetupID'] = $points['pointSetupID'];
                        $pts['invoiceID'] = $invID;
                        $pts['companyID'] = $companyID;
                        $pts['createdPCID'] = $this->common_data['current_pc'];
                        $pts['createdUserID'] = $this->common_data['current_userID'];
                        $pts['createdUserName'] = $this->common_data['current_user'];
                        $pts['createdUserGroup'] = $this->common_data['user_group'];
                        $pts['createdDateTime'] = current_date();

                        $this->db->insert('srp_erp_pos_loyaltytopup', $pts);
                    }
                }
            }
        }


        $paymentTypes = $this->input->post('paymentTypes');
        foreach ($paymentTypes as $key => $paymentType) {
            if ($paymentType > 0) {
                $wareHouseID = $this->common_data['ware_houseID'];
                $paymentglconfigmaster = $this->db->query("SELECT * FROM srp_erp_pos_paymentglconfigmaster WHERE autoID={$key}")->row_array();
                $paymentglconfigdetail = $this->db->query("SELECT * FROM srp_erp_pos_paymentglconfigdetail WHERE paymentConfigMasterID={$key} AND warehouseID={$wareHouseID}")->row_array();
                if ($key == 7 || $key == 26) {
                    $cusid = $customerID;
                } else {
                    $cusid = 0;
                }

                if ($key == 1) {
                    $paymentType = $netTotalAmount - ($totalPayment - $invArray['cashAmount']);
                }

                if ($paymentType > 0) {

                    $invPaymentARR = array(
                        'invoiceID' => $invID,
                        'paymentConfigMasterID' => $key,
                        'paymentConfigDetailID' => $paymentglconfigdetail['ID'],
                        'glAccountType' => $paymentglconfigmaster['glAccountType'],
                        'GLCode' => $paymentglconfigdetail['GLCode'],
                        'amount' => $paymentType,
                        'reference' => $this->input->post('reference[' . $key . ']'),
                        'customerAutoID' => $cusid,
                        'createdPCID' => $this->common_data['current_pc'],
                        'createdUserID' => $this->common_data['current_userID'],
                        'createdUserName' => $this->common_data['current_user'],
                        'createdUserGroup' => $this->common_data['user_group'],
                        'createdDateTime' => current_date(),
                    );
                    $this->db->insert('srp_erp_pos_invoicepayments', $invPaymentARR);
                }
            }
        }

        /*Load wac library*/
        $this->load->library('Wac');
        $this->load->library('sequence');


        if ($CreditSalesAmnt != 0) {
            $cusData = $this->db->query("SELECT customerAutoID, customerSystemCode, customerName, receivableAutoID,
                                             receivableSystemGLCode, receivableGLAccount, receivableDescription, receivableType,
                                             customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces,customerAddress1,customerTelephone
                                             FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

            $data_customer_invoice['invoiceType'] = 'Direct';
            $data_customer_invoice['documentID'] = 'CINV';
            $data_customer_invoice['posTypeID'] = 1;
            $data_customer_invoice['referenceNo'] = $sequenceCode;
            $creditSaleReference = $this->input->post('reference[7]');

            if ($creditSaleReference != '') {
                $data_customer_invoice['referenceNo'] .= ' - ' . $creditSaleReference;
            }
            if (!empty($this->input->post('memberidhn'))) {
                $memberid = $this->input->post('memberidhn');
                $membername = $this->input->post('membernamehn');
                $data_customer_invoice['invoiceNarration'] = 'POS Credit Sales - ' . $sequenceCode . '-' . $memberid . '-' . $membername;
            } else {
                $data_customer_invoice['invoiceNarration'] = 'POS Credit Sales - ' . $sequenceCode;
            }
            $data_customer_invoice['posMasterAutoID'] = $invID;
            $data_customer_invoice['invoiceDate'] = current_date();
            $data_customer_invoice['invoiceDueDate'] = current_date();
            $data_customer_invoice['customerInvoiceDate'] = current_date();
            $data_customer_invoice['invoiceCode'] = $this->sequence->sequence_generator($data_customer_invoice['documentID']);
            $data_customer_invoice['companyFinanceYearID'] = $this->common_data['company_data']['companyFinanceYearID'];
            $financialYear = get_financial_from_to($this->common_data['company_data']['companyFinanceYearID']);
            $data_customer_invoice['companyFinanceYear'] = trim($financialYear['beginingDate'] ?? '') . ' - ' . trim($financialYear['endingDate'] ?? '');
            $data_customer_invoice['FYBegin'] = trim($financialYear['beginingDate'] ?? '');
            $data_customer_invoice['FYEnd'] = trim($financialYear['endingDate'] ?? '');
            $data_customer_invoice['FYPeriodDateFrom'] = trim($this->common_data['company_data']['FYPeriodDateFrom']);
            $data_customer_invoice['FYPeriodDateTo'] = trim($this->common_data['company_data']['FYPeriodDateTo']);
            $data_customer_invoice['companyFinancePeriodID'] = $this->common_data['company_data']['companyFinancePeriodID'];
            $data_customer_invoice['customerID'] = $customerID;
            $data_customer_invoice['customerSystemCode'] = $cusData['customerSystemCode'];
            $data_customer_invoice['customerName'] = $cusData['customerName'];
            $data_customer_invoice['customerAddress'] = $cusData['customerAddress1'];
            $data_customer_invoice['customerTelephone'] = $cusData['customerTelephone'];
            $data_customer_invoice['customerFax'] = $cusData['customerTelephone'];
            $data_customer_invoice['customerEmail'] = $cusData['customerTelephone'];
            $data_customer_invoice['customerReceivableAutoID'] = $cusData['receivableAutoID'];
            $data_customer_invoice['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
            $data_customer_invoice['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
            $data_customer_invoice['customerReceivableDescription'] = $cusData['receivableDescription'];
            $data_customer_invoice['customerReceivableType'] = $cusData['receivableType'];
            $data_customer_invoice['customerCurrency'] = $cusData['customerCurrency'];
            $data_customer_invoice['customerCurrencyID'] = $cusData['customerCurrencyID'];
            $data_customer_invoice['customerCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];

            $data_customer_invoice['confirmedYN'] = 1;
            $data_customer_invoice['confirmedByEmpID'] = current_userID();
            $data_customer_invoice['confirmedByName'] = current_user();
            $data_customer_invoice['confirmedDate'] = current_date();
            $data_customer_invoice['approvedYN'] = 1;
            $data_customer_invoice['approvedDate'] = current_date();
            $data_customer_invoice['currentLevelNo'] = 1;
            $data_customer_invoice['approvedbyEmpID'] = current_userID();
            $data_customer_invoice['approvedbyEmpName'] = current_user();

            $data_customer_invoice['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data_customer_invoice['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data_customer_invoice['transactionExchangeRate'] = 1;
            $data_customer_invoice['transactionAmount'] = $netTotalAmount;
            $data_customer_invoice['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_customer_invoice['transactionCurrencyID']);
            $data_customer_invoice['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data_customer_invoice['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $default_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyLocalCurrencyID']);
            $data_customer_invoice['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data_customer_invoice['companyLocalAmount'] = $data_customer_invoice['transactionAmount'] / $data_customer_invoice['companyLocalExchangeRate'];
            $data_customer_invoice['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data_customer_invoice['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $data_customer_invoice['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyReportingCurrencyID']);
            $data_customer_invoice['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data_customer_invoice['companyReportingAmount'] = $data_customer_invoice['transactionAmount'] / $data_customer_invoice['companyReportingExchangeRate'];
            $data_customer_invoice['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $customer_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['customerCurrencyID']);
            $data_customer_invoice['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
            $data_customer_invoice['customerCurrencyAmount'] = $data_customer_invoice['transactionAmount'] / $data_customer_invoice['customerCurrencyExchangeRate'];
            $data_customer_invoice['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
            $data_customer_invoice['companyCode'] = $this->common_data['company_data']['company_code'];
            $data_customer_invoice['companyID'] = $this->common_data['company_data']['company_id'];
            $data_customer_invoice['createdUserGroup'] = $this->common_data['user_group'];
            $data_customer_invoice['createdPCID'] = $this->common_data['current_pc'];
            $data_customer_invoice['createdUserID'] = $this->common_data['current_userID'];
            $data_customer_invoice['createdUserName'] = $this->common_data['current_user'];
            $data_customer_invoice['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_customerinvoicemaster', $data_customer_invoice);
            $customerInvoiceMasterID = $this->db->insert_id();
            $this->db->select('documentID,invoiceCode,DATE_FORMAT(invoiceDate, "%Y") as invYear,DATE_FORMAT(invoiceDate, "%m") as invMonth,companyFinanceYearID,invoiceType');
            $this->db->where('invoiceAutoID', $customerInvoiceMasterID);
            $this->db->from('srp_erp_customerinvoicemaster');
            $master_dt = $this->db->get()->row_array();
            $data_customer_invoice['invoiceCode'] = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
            $invcod = array(
                'invoiceCode' => $data_customer_invoice['invoiceCode'],
            );

            $this->db->where('invoiceAutoID', $customerInvoiceMasterID);
            $this->db->update('srp_erp_customerinvoicemaster', $invcod);
            $customerInvoiceCode = $data_customer_invoice['invoiceCode'];

            if ($customerInvoiceMasterID) {
                $data_cusinv['documentSystemCode'] = $data_customer_invoice['invoiceCode'];
                $this->db->where('invoiceID', $invID);
                $this->db->update('srp_erp_pos_invoice', $data_cusinv);

                $doc_approved['departmentID'] = "CINV";
                $doc_approved['documentID'] = "CINV";
                $doc_approved['documentCode'] = $data_customer_invoice['invoiceCode'];
                $doc_approved['documentSystemCode'] = $customerInvoiceMasterID;
                $doc_approved['documentDate'] = current_date();
                $doc_approved['approvalLevelID'] = 1;
                $doc_approved['docConfirmedDate'] = current_date();
                $doc_approved['docConfirmedByEmpID'] = current_userID();
                $doc_approved['table_name'] = 'srp_erp_customerinvoicemaster';
                $doc_approved['table_unique_field_name'] = 'invoiceAutoID';
                $doc_approved['approvedEmpID'] = current_userID();
                $doc_approved['approvedYN'] = 1;
                $doc_approved['approvedComments'] = 'Approved from POS';
                $doc_approved['approvedPC'] = current_pc();
                $doc_approved['approvedDate'] = current_date();
                $doc_approved['companyID'] = current_companyID();
                $doc_approved['companyCode'] = current_company_code();
                $this->db->insert('srp_erp_documentapproved', $doc_approved);

                $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,segmentID,segmentCode,transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces');
                $this->db->from('srp_erp_customerinvoicemaster');
                $this->db->where('invoiceAutoID', $customerInvoiceMasterID);
                $master = $this->db->get()->row_array();

                $data_customer_invoice_detail['invoiceAutoID'] = $customerInvoiceMasterID;
                $data_customer_invoice_detail['type'] = 'GL';
                if (!empty($this->input->post('memberidhn'))) {
                    $memberid = $this->input->post('memberidhn');
                    $membername = $this->input->post('membernamehn');
                    $data_customer_invoice_detail['description'] = 'POS Sales - ' . $sequenceCode . '-' . $memberid . '-' . $membername;
                } else {
                    $data_customer_invoice_detail['description'] = 'POS Sales - ' . $sequenceCode;
                }
                $data_customer_invoice_detail['transactionAmount'] = round($netTotalAmount, $master['transactionCurrencyDecimalPlaces']);
                $companyLocalAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyLocalExchangeRate'];
                $data_customer_invoice_detail['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $companyReportingAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyReportingExchangeRate'];
                $data_customer_invoice_detail['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $customerAmount = $data_customer_invoice_detail['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                $data_customer_invoice_detail['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                $data_customer_invoice_detail['companyCode'] = $this->common_data['company_data']['company_code'];
                $data_customer_invoice_detail['companyID'] = $this->common_data['company_data']['company_id'];
                $data_customer_invoice_detail['createdUserGroup'] = $this->common_data['user_group'];
                $data_customer_invoice_detail['createdPCID'] = $this->common_data['current_pc'];
                $data_customer_invoice_detail['createdUserID'] = $this->common_data['current_userID'];
                $data_customer_invoice_detail['createdUserName'] = $this->common_data['current_user'];
                $data_customer_invoice_detail['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerinvoicedetails', $data_customer_invoice_detail);
            }
        }

        $i = 0;
        $item_ledger_arr = array();
        $dataInt = array();
        //var_dump($item);exit;
        $itemList = fetch_warehouse_items_data($item);
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouseID = $this->common_data['ware_houseID'];
        $exceedglid = $this->db->query("SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE companyID =$companyID AND controlAccountType = 'IEXC'")->row_array();
        $exceedGlAutoID = $exceedglid['GLAutoID'];
        $exceedGlDesc = fetch_gl_account_desc($exceedGlAutoID);
        $warehousePromotion = $this->db->query("select * from srp_erp_pos_promotionwarehouses where companyID=$companyID and wareHouseID=$wareHouseID and isActive=1");
        $isMinusAllowed = getPolicyValues('MQT', 'All');

    

        foreach ($itemList as $itemData) {

            $itemMainCategory = $itemData['mainCategoryID'];

            //$itemData = fetch_ware_house_item_data($itemID);
            /*  $calculatedWacReportingAmount = $this->db->query("SELECT
                itemAutoID,
                ( (SUM( transactionAmount / companyLocalExchangeRate)) / (sum( transactionQty / convertionRate) )  )	as wacAmount,
                ( (SUM( transactionAmount / companyReportingExchangeRate)) / (sum( transactionQty / convertionRate) )  )	as wacReportingAmount
                        FROM
                            srp_erp_itemledger
                        WHERE
                            itemAutoID = $itemID
                        GROUP BY
                            itemAutoID")->row()->wacReportingAmount; */
                        // $itemData['companyReportingWacAmount']=$calculatedWacReportingAmount;
                        //var_dump($itemData['itemAutoID']);exit;

            $itemID = $itemData['itemAutoID'];
            $conversion = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
            $conversion = ($conversion == 0) ? 1 : $conversion;
            $conversionRate = 1 / $conversion;
            $availableQTY = $itemData['wareHouseQty'];
            $qty = $itemQty[$i] * $conversionRate;

            if ($availableQTY < $qty && $isStockCheck == 1) {
                $this->db->trans_rollback();
                return array('e', '[ ' . $itemData['itemSystemCode'] . ' - ' . $itemData['itemDescription'] . ' ]<p> is available only ' . $availableQTY . ' qty');
                break;
            }

            $price = str_replace(',', '', $itemPrice[$i]);
            $itemTotal = $itemQty[$i] * $price;
            $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal - ($itemTotal * 0.01 * $itemDis[$i])) : $itemTotal;
            $itemTotal = round($itemTotal, $tr_currDPlace);

            $dataInt[$i]['invoiceID'] = $invID;
            $dataInt[$i]['itemAutoID'] = $itemID;
            $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
            $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
            $dataInt[$i]['defaultUOMID'] = $itemData['defaultUnitOfMeasureID'];
            $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
            $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
            $dataInt[$i]['UOMID'] = $itemData['defaultUnitOfMeasureID'];
            $dataInt[$i]['conversionRateUOM'] = $conversion;
            $dataInt[$i]['qty'] = $itemQty[$i];
            $dataInt[$i]['price'] = $price;
            $dataInt[$i]['discountPer'] = $itemDis[$i];
            if ($itemDis[$i] > 0) {
                $discountAmount = ($price * 0.01 * $itemDis[$i]);
            } else {
                $discountAmount = 0;
            }
            $dataInt[$i]['discountAmount'] = $discountAmount;


            $gen_disc_percentage = $this->input->post('gen_disc_percentage');
            if ($gen_disc_percentage > 0) {
                $gen_discountAmount = ($price - $discountAmount) * 0.01 * $gen_disc_percentage * $itemQty[$i];
            } else {
                $gen_discountAmount = 0;
            }

            $dataInt[$i]['generalDiscountPercentage'] = $gen_disc_percentage;
            $dataInt[$i]['generalDiscountAmount'] = $gen_discountAmount;
            $dataInt[$i]['wacAmount'] = $itemData['companyLocalWacAmount'];
            /*
              $calculatedWacAmount = $this->db->query("SELECT
                    itemAutoID,

                    ( (SUM( transactionAmount / companyLocalExchangeRate)) / (sum( transactionqty / convertionRate) )  )	as wacAmount,
                    ( (SUM( transactionAmount / companyReportingExchangeRate)) / (sum( transactionqty / convertionRate) )  )	as wacReportingAmount
                FROM
                    srp_erp_itemledger
                WHERE
                    itemAutoID = $itemID
                GROUP BY
                    itemAutoID")->row()->wacAmount; */
                            //$dataInt[$i]['wacAmount'] = $calculatedWacAmount;//$itemData['companyLocalWacAmount']; <- previous value.

            $dataInt[$i]['itemFinanceCategory'] = $itemData['subcategoryID'];
            $dataInt[$i]['itemFinanceCategorySub'] = $itemData['subSubCategoryID'];
            $dataInt[$i]['financeCategory'] = $itemData['financeCategory'];
            $dataInt[$i]['itemCategory'] = $itemData['mainCategory'];

            $dataInt[$i]['expenseGLAutoID'] = $itemData['costGLAutoID'];
            $dataInt[$i]['expenseGLCode'] = $itemData['costGLCode'];
            $dataInt[$i]['expenseSystemGLCode'] = $itemData['costSystemGLCode'];
            $dataInt[$i]['expenseGLDescription'] = $itemData['costDescription'];
            $dataInt[$i]['expenseGLType'] = $itemData['costType'];

            $dataInt[$i]['revenueGLAutoID'] = $itemData['revanueGLAutoID'];
            $dataInt[$i]['revenueGLCode'] = $itemData['revanueGLCode'];
            $dataInt[$i]['revenueSystemGLCode'] = $itemData['revanueSystemGLCode'];
            $dataInt[$i]['revenueGLDescription'] = $itemData['revanueDescription'];
            $dataInt[$i]['revenueGLType'] = $itemData['revanueType'];

            $dataInt[$i]['assetGLAutoID'] = $itemData['assteGLAutoID'];
            $dataInt[$i]['assetGLCode'] = $itemData['assteGLCode'];
            $dataInt[$i]['assetSystemGLCode'] = $itemData['assteSystemGLCode'];
            $dataInt[$i]['assetGLDescription'] = $itemData['assteDescription'];
            $dataInt[$i]['assetGLType'] = $itemData['assteType'];


            $dataInt[$i]['transactionAmountBeforeDiscount'] = $itemTotal;
            $itemTotal = $itemTotal - $gen_discountAmount;
            $promotiondiscountAmount = 0;
            if (isset($promotionDiscount)) {
                $promotiondiscountAmount = $itemTotal * ($promotionDiscount / 100);
                $dataInt[$i]['promotiondiscount'] = $promotionDiscount;
                $dataInt[$i]['promotiondiscountAmount'] = $promotiondiscountAmount;
            } else {
                $dataInt[$i]['promotiondiscount'] = 0;
                $dataInt[$i]['promotiondiscountAmount'] = 0;
            }
            $dataInt[$i]['transactionAmount'] = $itemTotal - $promotiondiscountAmount;
            $dataInt[$i]['transactionExchangeRate'] = $transConversionRate;
            $dataInt[$i]['transactionCurrency'] = $tr_currency;
            $dataInt[$i]['transactionCurrencyID'] = $localConversion['trCurrencyID'];
            $dataInt[$i]['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;
            $dataInt[$i]['companyLocalAmount'] = round(($itemTotal / $localConversionRate), $com_currDPlace);

            $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
            $dataInt[$i]['companyLocalCurrency'] = $com_currency;
            $dataInt[$i]['companyLocalCurrencyID'] = $localConversion['currencyID'];
            $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

            $dataInt[$i]['companyReportingAmount'] = round(($itemTotal / $reportConversionRate), $rep_currDPlace);
            $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
            $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
            $dataInt[$i]['companyReportingCurrencyID'] = $reportConversion['currencyID'];
            $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;
            $dataInt[$i]['taxCalculationformulaID'] = $taxFormula[$i];
            $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
            $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
            $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
            $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $dataInt[$i]['createdDateTime'] = current_date();


            if ($warehousePromotion->num_rows() > 0) {
                $dataInt[$i]['promoID'] = $warehousePromotion->row()->promotionID;
            }

            //this section previously commented
            //            $balanceQty = $availableQTY - $qty;
            //            $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
            //            $itemUpdateQty = array('currentStock' => $balanceQty);
            //            $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);
            //=========================================

            $exceedYN = true;

            if ($isMinusAllowed == ' ' || empty($isMinusAllowed) || $isMinusAllowed == null) {
                $isMinusAllowed = 0;
            }

            $exccededVar = 0;
            if ($isMinusAllowed != 1 && $exceedYN && ($itemData['mainCategory'] != 'Service' && $itemData['mainCategory'] != 'Non Inventory')) {
                //check if item exceeded
                $wareHouseID = $this->common_data['ware_houseID'];
                $excceditems = $this->db->query("SELECT
                    t1.itemAutoID,
                    t1.itemSystemCode,
                    t1.itemDescription,
                    ROUND(IFNULL(SUM(t1.transactionQTY/t1.convertionRate),0),5) AS currentStock
                FROM
                    srp_erp_itemledger t1
                JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                WHERE
                    t2.companyID = '{$companyID}'
                AND wareHouseAutoID = '{$wareHouseID}'
                AND t1.itemAutoID = '{$itemID}'
                AND isActive = 1
                GROUP BY
                    t1.itemAutoID")->row_array();

                $exceededQty = $excceditems['currentStock'] - $itemQty[$i];
                if ($excceditems['currentStock'] < 0) {
                    $exceededQty = $itemQty[$i] * -1;
                }


                //var_dump($exceededQty);exit;
                if ($exceededQty < 0) {

                    $exccededVar = 1;

                    if ($CreditSalesAmnt != 0) {
                        $exceedtbl['documentCode'] = 'CINV';
                        $exceedtbl['documentAutoID'] = $customerInvoiceMasterID;
                        $exceedtbl['documentSystemCode'] = $data_customer_invoice['invoiceCode'];
                    } else {
                        $exceedtbl['documentCode'] = 'POS';
                        $exceedtbl['documentAutoID'] = $invID;
                        $exceedtbl['documentSystemCode'] = $refCode;
                    }
                    $exceedtbl['itemAutoID'] = $itemID;
                    $exceedtbl['warehouseAutoID'] = $wareHouseID;
                    $exceedtbl['exceededGLAutoID'] = $exceedGlAutoID;
                    $exceedtbl['assetGLAutoID'] = $itemData['assteGLAutoID'];
                    $exceedtbl['costGLAutoID'] = $itemData['costGLAutoID'];
                    $exceedtbl['exceededQty'] = abs($exceededQty);
                    $exceedtbl['updatedQty'] = 0;
                    $exceedtbl['balanceQty'] = abs($exceededQty);
                    $exceedtbl['isFromCreditSales'] = $this->input->post('isCreditSale');
                    $exceedtbl['unitCost'] = $itemData['companyLocalWacAmount'];
                    $exceedtbl['transactionAmount'] = $itemData['companyLocalWacAmount'] * abs($exceededQty);
                    $exceedtbl['companyLocalAmount'] = $exceedtbl['transactionAmount'] / $localConversionRate;
                    $exceedtbl['companyReportingAmount'] = $exceedtbl['transactionAmount'] / $reportConversionRate;
                    $exceedtbl['defaultUOMID'] = $itemData['defaultUnitOfMeasureID'];
                    $exceedtbl['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
                    $exceedtbl['unitOfMeasureID'] = $itemData['defaultUnitOfMeasureID'];
                    $exceedtbl['unitOfMeasure'] = $itemUOM[$i];
                    $exceedtbl['conversionRateUOM'] = $conversion;
                    $exceedtbl['documentDate'] = current_date();
                    $exceedtbl['companyID'] = $companyID;
                    $exceedtbl['segmentID'] = $wareHouseData['segmentID'];
                    $exceedtbl['segmentCode'] = $wareHouseData['segmentCode'];
                    $exceedtbl['createdUserGroup'] = $this->common_data['user_group'];
                    $exceedtbl['createdPCID'] = $this->common_data['current_pc'];
                    $exceedtbl['createdUserID'] = $this->common_data['current_userID'];
                    $exceedtbl['createdDateTime'] = current_date();
                    $exceedtbl['createdUserName'] = $this->common_data['current_user'];

                    $this->db->insert('srp_erp_itemexceeded', $exceedtbl);

                    //this section is new
                    $balanceQty = $availableQTY - $qty;
                    $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
                    $itemUpdateQty = array('currentStock' => $balanceQty);
                    $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);
                    //=========================================


                    $exceedGL['wareHouseAutoID'] = $wareHouseID;

                    if ($CreditSalesAmnt != 0) {
                        $exceedGL['documentCode'] = 'CINV';
                        $exceedGL['documentMasterAutoID'] = $customerInvoiceMasterID;
                        $exceedGL['documentSystemCode'] = $data_customer_invoice['invoiceCode'];
                    } else {
                        $exceedGL['documentCode'] = 'POS';
                        $exceedGL['documentMasterAutoID'] = $invID;
                        $exceedGL['documentSystemCode'] = $refCode; //$sequenceCode;
                    }

                    $exceedGL['documentDate'] = current_date();
                    $exceedGL['documentYear'] = date("Y", strtotime(current_date()));
                    $exceedGL['documentMonth'] = date("m", strtotime(current_date()));
                    $exceedGL['documentNarration'] = 'GPOS Exceeded';
                    $exceedGL['GLAutoID'] = $exceedGlAutoID;
                    $exceedGL['systemGLCode'] = $exceedGlDesc['systemAccountCode'];
                    $exceedGL['GLCode'] = $exceedGlDesc['GLSecondaryCode'];
                    $exceedGL['GLDescription'] = $exceedGlDesc['GLDescription'];
                    $exceedGL['GLType'] = $exceedGlDesc['subCategory'];
                    $exceedGL['amount_type'] = 'cr';
                    $exceedGL['transactionCurrencyID'] = $localConversion['trCurrencyID'];
                    $exceedGL['transactionCurrency'] = $tr_currency;
                    $exceedGL['transactionExchangeRate'] = $transConversionRate;
                    $exceedGL['transactionAmount'] = $exceedtbl['transactionAmount'] * -1;
                    $exceedGL['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;
                    $exceedGL['companyLocalCurrencyID'] = $localConversion['currencyID'];
                    $exceedGL['companyLocalCurrency'] = $com_currency;
                    $exceedGL['companyLocalExchangeRate'] = $localConversionRate;
                    $exceedGL['companyLocalAmount'] = ($exceedGL['transactionAmount'] / $localConversionRate);
                    $exceedGL['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

                    $exceedGL['companyReportingCurrencyID'] = $reportConversion['currencyID'];
                    $exceedGL['companyReportingCurrency'] = $rep_currency;
                    $exceedGL['companyReportingExchangeRate'] = $reportConversionRate;
                    $exceedGL['companyReportingAmount'] = ($exceedGL['transactionAmount'] / $reportConversionRate);
                    $exceedGL['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

                    if ($customerID == 0) {
                        $exceedGL['partyAutoID'] = 0;
                        $exceedGL['partySystemCode'] = 'CASH';
                        $exceedGL['partyName'] = 'CASH';
                        $exceedGL['partyCurrencyID'] = 0;
                        $exceedGL['partyCurrency'] = $tr_currency;
                        $exceedGL['partyExchangeRate'] = $transConversionRate;
                        $exceedGL['partyCurrencyAmount'] = $exceedGL['transactionAmount'];
                        $exceedGL['partyCurrencyDecimalPlaces'] = $tr_currDPlace;
                    } else {
                        $partyDatacon = currency_conversion($tr_currency, $cusData['customerCurrency']);
                        $exceedGL['partyAutoID'] = $cusData['customerAutoID'];
                        $exceedGL['partySystemCode'] = $cusData['customerSystemCode'];
                        $exceedGL['partyName'] = $cusData['customerName'];
                        $exceedGL['partyCurrencyID'] = $cusData['customerCurrencyID'];
                        $exceedGL['partyCurrency'] = $cusData['customerCurrency'];
                        $exceedGL['partyExchangeRate'] = $partyDatacon['conversion'];
                        $exceedGL['partyCurrencyAmount'] = ($exceedGL['transactionAmount'] / $partyDatacon['conversion']) * -1;
                        $exceedGL['partyCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];
                    }
                    $exceedGL['confirmedByEmpID'] = $this->common_data['current_userID'];
                    $exceedGL['confirmedByName'] = $this->common_data['current_user'];
                    $exceedGL['confirmedDate'] = current_date();
                    $exceedGL['approvedDate'] = current_date();
                    $exceedGL['approvedbyEmpID'] = $this->common_data['current_userID'];
                    $exceedGL['approvedbyEmpName'] = $this->common_data['current_user'];
                    $exceedGL['segmentID'] = $wareHouseData['segmentID'];
                    $exceedGL['segmentCode'] = $wareHouseData['segmentCode'];
                    $exceedGL['companyID'] = current_companyID();
                    $exceedGL['companyCode'] = $this->common_data['company_data']['company_code'];
                    $exceedGL['createdUserGroup'] = $this->common_data['user_group'];
                    $exceedGL['createdPCID'] = $this->common_data['current_pc'];
                    $exceedGL['createdUserID'] = $this->common_data['current_userID'];
                    $exceedGL['createdDateTime'] = current_date();
                    $exceedGL['createdUserName'] = $this->common_data['current_user'];

                    $this->db->insert('srp_erp_generalledger', $exceedGL);
                }
            }


            $notitemleg = 0;
            if ($exccededVar == 1) {
                $notExceeddINVqty = $itemQty[$i] - abs($exceededQty);
                
                if ($excceditems['currentStock'] < 0) {
                    $notitemleg = 1;
                }
                if ($notExceeddINVqty > 0 && $notitemleg == 0) {
                    
                    $price = str_replace(',', '', $itemPrice[$i]);
                    $itemTotal = $notExceeddINVqty * $price;
                    $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal - ($itemTotal * 0.01 * $itemDis[$i])) : $itemTotal;
                    $itemTotal = round($itemTotal, $tr_currDPlace);

                    $dataIntExcced[$i]['invoiceID'] = $invID;
                    $dataIntExcced[$i]['itemAutoID'] = $itemID;
                    $dataIntExcced[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
                    $dataIntExcced[$i]['itemDescription'] = $itemData['itemDescription'];
                    $dataIntExcced[$i]['defaultUOMID'] = $itemData['defaultUnitOfMeasureID'];
                    $dataIntExcced[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
                    $dataIntExcced[$i]['unitOfMeasure'] = $itemUOM[$i];
                    $dataIntExcced[$i]['UOMID'] = $itemData['defaultUnitOfMeasureID'];
                    $dataIntExcced[$i]['conversionRateUOM'] = $conversion;
                    $dataIntExcced[$i]['qty'] = $notExceeddINVqty;
                    $dataIntExcced[$i]['price'] = $price;
                    $dataIntExcced[$i]['discountPer'] = $itemDis[$i];
                    if ($itemDis[$i] > 0) {
                        $discountAmount = ($price * 0.01 * $itemDis[$i]);
                    } else {
                        $discountAmount = 0;
                    }
                    $dataIntExcced[$i]['discountAmount'] = $discountAmount;


                    $gen_disc_percentage = $this->input->post('gen_disc_percentage');
                    if ($gen_disc_percentage > 0) {
                        $gen_discountAmount = ($price - $discountAmount) * 0.01 * $gen_disc_percentage * $notExceeddINVqty;
                    } else {
                        $gen_discountAmount = 0;
                    }

                    $dataIntExcced[$i]['generalDiscountPercentage'] = $gen_disc_percentage;
                    $dataIntExcced[$i]['generalDiscountAmount'] = $gen_discountAmount;

                    //                    $calculatedWacAmount = $this->db->query("SELECT
                    //	itemAutoID,
                    //    ( (SUM( transactionAmount / companyLocalExchangeRate)) / (sum( transactionqty / convertionRate) )  )	as wacAmount,
                    //	( (SUM( transactionAmount / companyReportingExchangeRate)) / (sum( transactionqty / convertionRate) )  )	as wacReportingAmount
                    //FROM
                    //	srp_erp_itemledger
                    //WHERE
                    //	itemAutoID = $itemID
                    //GROUP BY
                    //	itemAutoID")->row()->wacAmount;

                    //$dataIntExcced[$i]['wacAmount'] = $calculatedWacAmount;//$itemData['companyLocalWacAmount']; <- previous value.
                    $dataIntExcced[$i]['wacAmount'] = $itemData['companyLocalWacAmount'];
                    $dataIntExcced[$i]['itemFinanceCategory'] = $itemData['subcategoryID'];
                    $dataIntExcced[$i]['itemFinanceCategorySub'] = $itemData['subSubCategoryID'];
                    $dataIntExcced[$i]['financeCategory'] = $itemData['financeCategory'];
                    $dataIntExcced[$i]['itemCategory'] = $itemData['mainCategory'];

                    $dataIntExcced[$i]['expenseGLAutoID'] = $itemData['costGLAutoID'];
                    $dataIntExcced[$i]['expenseGLCode'] = $itemData['costGLCode'];
                    $dataIntExcced[$i]['expenseSystemGLCode'] = $itemData['costSystemGLCode'];
                    $dataIntExcced[$i]['expenseGLDescription'] = $itemData['costDescription'];
                    $dataIntExcced[$i]['expenseGLType'] = $itemData['costType'];

                    $dataIntExcced[$i]['revenueGLAutoID'] = $itemData['revanueGLAutoID'];
                    $dataIntExcced[$i]['revenueGLCode'] = $itemData['revanueGLCode'];
                    $dataIntExcced[$i]['revenueSystemGLCode'] = $itemData['revanueSystemGLCode'];
                    $dataIntExcced[$i]['revenueGLDescription'] = $itemData['revanueDescription'];
                    $dataIntExcced[$i]['revenueGLType'] = $itemData['revanueType'];

                    $dataIntExcced[$i]['assetGLAutoID'] = $itemData['assteGLAutoID'];
                    $dataIntExcced[$i]['assetGLCode'] = $itemData['assteGLCode'];
                    $dataIntExcced[$i]['assetSystemGLCode'] = $itemData['assteSystemGLCode'];
                    $dataIntExcced[$i]['assetGLDescription'] = $itemData['assteDescription'];
                    $dataIntExcced[$i]['assetGLType'] = $itemData['assteType'];


                    $dataIntExcced[$i]['transactionAmountBeforeDiscount'] = $itemTotal;
                    $itemTotal = $itemTotal - $gen_discountAmount;
                    $dataIntExcced[$i]['transactionAmount'] = $itemTotal;
                    $dataIntExcced[$i]['transactionExchangeRate'] = $transConversionRate;
                    $dataIntExcced[$i]['transactionCurrency'] = $tr_currency;
                    $dataIntExcced[$i]['transactionCurrencyID'] = $localConversion['trCurrencyID'];
                    $dataIntExcced[$i]['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;
                    $dataIntExcced[$i]['companyLocalAmount'] = round(($itemTotal / $localConversionRate), $com_currDPlace);

                    $dataIntExcced[$i]['companyLocalExchangeRate'] = $localConversionRate;
                    $dataIntExcced[$i]['companyLocalCurrency'] = $com_currency;
                    $dataIntExcced[$i]['companyLocalCurrencyID'] = $localConversion['currencyID'];
                    $dataIntExcced[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

                    $dataIntExcced[$i]['companyReportingAmount'] = round(($itemTotal / $reportConversionRate), $rep_currDPlace);
                    $dataIntExcced[$i]['companyReportingExchangeRate'] = $reportConversionRate;
                    $dataIntExcced[$i]['companyReportingCurrency'] = $rep_currency;
                    $dataIntExcced[$i]['companyReportingCurrencyID'] = $reportConversion['currencyID'];
                    $dataIntExcced[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;


                    $dataIntExcced[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataIntExcced[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $dataIntExcced[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $dataIntExcced[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $dataIntExcced[$i]['createdUserName'] = $this->common_data['current_user'];
                    $dataIntExcced[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $dataIntExcced[$i]['createdDateTime'] = current_date();
                    $wacData = $this->wac->wac_calculation(1, $itemID, $notExceeddINVqty, '', $this->common_data['ware_houseID']);
                    
                    if ($CreditSalesAmnt > 0) {

                        $item_ledger_arr[$i] = $this->item_ledger_customerInvoice($financeYear, $financePeriod, $customerInvoiceCode, $dataIntExcced[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData, null, $customerInvoiceMasterID);
                        $this->db->insert('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.
                    } else {

                        $item_ledger_arr[$i] = $this->item_ledger($financeYear, $financePeriod, $sequenceCode, $dataIntExcced[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData);
                        $this->db->insert('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.
                    }
                }
                
            } else {
                $wacData = $this->wac->wac_calculation(1, $itemID, $qty, '', $this->common_data['ware_houseID']);
                $isItemBatchPolicy = getPolicyValues('IB', 'All');

                if ($CreditSalesAmnt > 0) {
                    //only for item inventory category batch is enable

                    if($isItemBatchPolicy == 1 && $itemMainCategory == 1){
                        $item_ledger_arr[$i] = $this->item_ledger_customerInvoice_batch($financeYear, $financePeriod, $customerInvoiceCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData, null, $customerInvoiceMasterID,$sequenceCode);
                        $this->db->insert_batch('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.
                    }else{
                        $item_ledger_arr[$i] = $this->item_ledger_customerInvoice($financeYear, $financePeriod, $customerInvoiceCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData, null, $customerInvoiceMasterID);
                        $this->db->insert('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.
                    }
                   
                } else {
                    //only for item inventory category batch is enable

                    if($isItemBatchPolicy == 1 && $itemMainCategory == 1){
                        $item_ledger_arr[$i] = $this->item_ledger_batch($financeYear, $financePeriod, $sequenceCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData);
                        $this->db->insert_batch('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.
                    }else{
                        $item_ledger_arr[$i] = $this->item_ledger($financeYear, $financePeriod, $sequenceCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData);
                        $this->db->insert('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.
                    }

                }
            }


            $i++;
        }

        //echo '<pre>';print_r($dataInt);echo '</pre>'; exit;
        $this->db->insert_batch('srp_erp_pos_invoicedetail', $dataInt);

        if ($isGroupBasedPolicyYN == 1) {
            $companyID = current_companyID();
            $posInvoiceTax = $this->db->query("SELECT
	                                           taxCalculationFormulaID,
	                                           invoiceID,
	                                           (qty * price) as totaltaxApplicableAmt,
                                               invoiceDetailsID,
		                                       (qty * discountAmount) as discountamt,
                                               invoiceDetailsID
                                               FROM
	                                           srp_erp_pos_invoicedetail 
                                               WHERE
                                                companyID  = {$companyID} 
                                                AND invoiceID = {$invID}")->result_array();

            if (!empty($posInvoiceTax)) {
                foreach ($posInvoiceTax as $taxVal) {
                    if ($taxVal['taxCalculationFormulaID'] > 0) {
                        tax_calculation_vat(null, null, $taxVal['taxCalculationFormulaID'], 'invoiceID', $taxVal['invoiceID'], $taxVal['totaltaxApplicableAmt'], 'GPOS', $taxVal['invoiceDetailsID'], $taxVal['discountamt'], 1);
                    }
                }
            }
        }


        $isInvoiced = $this->input->post('isInvoiced');
        if (!empty($isInvoiced)) {
            $holdinv['isInvoiced'] = 1;
            $this->db->where('invoiceID', $isInvoiced);
            $this->db->update('srp_erp_pos_invoicehold', $holdinv);
        }
        //var_dump($item_ledger_arr);exit;
        //        if (!empty($item_ledger_arr)) {
        //            $this->db->insert_batch('srp_erp_itemledger', $item_ledger_arr);
        //        }

        //item exeed transaction goes here
        //        $companyID = $this->common_data['company_data']['company_id'];
        //        $wareHouseID = $this->common_data['ware_houseID'];
        //        $query = $this->db->query("select * from srp_erp_pos_invoicedetail where invoiceID=$invID GROUP BY itemAutoID");
        //        if ($query->num_rows() > 0) {
        //            foreach ($query->result() as $item) {
        //                $itemAutoID = $item->itemAutoID;
        //                $exeeded_item_query = $this->db->query("SELECT
        //convertionRate,
        //	transactiontbl.itemAutoID,
        //	ifnull( transactionQty, 0 ) as transactionQty,
        //	ifnull( currentStock, 0 ) as currentStock,
        //	(
        //	ifnull( IF(currentStock<0, 0, currentStock), 0 ) - ifnull( transactionQty, 0 )) AS bal
        //FROM
        //	( SELECT itemAutoID, sum( Qty / conversionRateUOM ) AS transactionQty FROM srp_erp_pos_invoicedetail WHERE invoiceID = $invID AND itemAutoID=$itemAutoID GROUP BY itemAutoID ) transactiontbl
        //	LEFT JOIN (
        //SELECT
        //	itemAutoID,
        //	sum( transactionQty / convertionRate ) AS currentStock,
        //	convertionRate
        //FROM
        //	srp_erp_itemledger
        //WHERE
        //	companyID = $companyID
        //	AND wareHouseAutoID = $wareHouseID
        //GROUP BY
        //	itemAutoID
        //	) ledgerTable ON transactiontbl.itemAutoID = ledgerTable.itemAutoID
        //	having bal<0");
        //                var_dump($exeeded_item_query->row()->transactionQty);var_dump($exeeded_item_query->row()->convertionRate);exit;
        //                if ($exeeded_item_query->num_rows() > 0) {
        //                    $exeeded_item_row = $exeeded_item_query->row();
        //                    $invoice = $this->db->query("select * from srp_erp_pos_invoice where invoiceID=$invID")->row();
        //                    $invoicedetail = $this->db->query("select * from srp_erp_pos_invoicedetail where invoiceID=$invID and itemAutoID=$itemAutoID GROUP BY itemAutoID")->row();
        //                    $exeedGLAutoID = $this->db->query("SELECT GLAutoID FROM `srp_erp_companycontrolaccounts` where controlAccountType='IEXC' and companyID=$companyID")->row()->GLAutoID;
        //
        //                    if ($CreditSalesAmnt != 0) {
        //                        $documentCode = 'CINV';
        //                        $documentAutoID = $this->db->query("select * from srp_erp_customerinvoicemaster where posMasterAutoID=$invID")->row()->invoiceAutoID;
        //                    } else {
        //                        $documentCode = 'POS';
        //                        $documentAutoID = $invID;
        //                    }
        //
        //                    $transactionAmount = ($exeeded_item_row->bal)*($invoicedetail->wacAmount);
        //                    $insert_exeeded_item = array(
        //                        "documentCode" => $documentCode,
        //                        "documentAutoID" => $documentAutoID,
        //                        "documentSystemCode" => $invoice->documentSystemCode,
        //                        "itemAutoID" => $exeeded_item_row->itemAutoID,
        //                        "warehouseAutoID" => $invoice->wareHouseAutoID,
        //                        "exceededGLAutoID" => $exeedGLAutoID,
        //                        "assetGLAutoID" => $invoicedetail->assetGLAutoID,
        //                        "costGLAutoID" => $invoicedetail->expenseGLAutoID,
        //                        "exceededQty" => $exeeded_item_row->bal,
        //                        "updatedQty" => 0,
        //                        "balanceQty" => $exeeded_item_row->bal,
        //                        "isFromCreditSales" => 0,
        //                        "unitCost" => $invoicedetail->wacAmount,
        //                        "transactionAmount" => $invoicedetail->transactionAmount,
        //                        "companyLocalAmount" => $invoicedetail->companyLocalAmount,
        //                        "companyReportingAmount" => $invoicedetail->companyReportingAmount,
        //                        "defaultUOMID" => $invoicedetail->defaultUOMID,
        //                        "defaultUOM" => $invoicedetail->defaultUOM,
        //                        "unitOfMeasureID" => $invoicedetail->UOMID,
        //                        "unitOfMeasure" => $invoicedetail->unitOfMeasure,
        //                        "conversionRateUOM" => $invoicedetail->conversionRateUOM,
        //                        "documentDate" => $invoice->createdDateTime,
        //                        "companyID" => $invoice->companyID,
        //                        "segmentID" => $invoice->segmentID,
        //                        "segmentCode" => $invoice->segmentCode,
        //                        "createdUserGroup" => $this->common_data['user_group'],
        //                        "createdPCID" => $this->common_data['current_pc'],
        //                        "createdUserID" => $this->common_data['current_userID'],
        //                        "createdDateTime" => current_date(),
        //                        "createdUserName" => $this->common_data['current_user'],
        //                        "timestamp" => format_date_mysql_datetime()
        //                    );
        //                    $this->db->insert('srp_erp_itemexceeded', $insert_exeeded_item);
        //                }
        //            }
        //        }

        //leger transactions goes here
        if ($CreditSalesAmnt > 0) {
            $this->double_entry($invID, $partyData, $CreditSalesAmnt, $customerInvoiceMasterID);
        } else {
            $this->double_entry($invID, $partyData, $CreditSalesAmnt);
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            //            $this->db->query("UNLOCK TABLES;");
            $invCodeQ = $this->db->query("select invoiceCode from srp_erp_pos_invoice where invoiceCode='$sequenceCode'");
            if ($invCodeQ->num_rows() > 0) {
                return array('d', 'duplicate');
            } else {
                return array('e', 'Error in Invoice Create');
            }
        } else {
            $this->db->trans_commit();
            return array('s', 'Invoice Code : ' . $sequenceCode . ' ', $invID, $refCode, $sequenceCode);
        }
    }

    function creditNote_load()
    {
        $key = $this->input->post('key');
        $companyID = current_companyID();
        return $this->db->select('salesReturnID, documentSystemCode, salesReturnDate, netTotal')
            ->from('srp_erp_pos_salesreturn t1')
            ->where('returnMode', 1)
            ->where(" companyID={$companyID}
                        AND NOT EXISTS (
                            SELECT * FROM srp_erp_pos_invoice WHERE creditNoteID = t1.salesReturnID
                        )
                     order by salesReturnDate desc")->get()->result_array();
    }

    function save_customer()
    {
        $companyID = current_companyID();
        $customerTelephoneTmp = trim($this->input->post('customerTelephone') ?? '');
        $customerTelephone = $this->db->query("SELECT customerAutoID FROM srp_erp_customermaster 
                                                    where companyID = $companyID AND customerTelephone LIKE '{$customerTelephoneTmp}'")->row('customerAutoID');

        if (!empty($customerTelephone)) {
            $this->session->set_flashdata('e', 'Customer telephone number already exist!');
            $this->db->trans_rollback();
            return array('status' => false);
        }

        $this->db->trans_start();
        $isactive = 1;

        $companyData = get_companyInfo();
        $controlAccount = get_companyControlAccounts('ARA'); /*Account Receivable Control Account*/

        //$liability = fetch_gl_account_desc(trim($this->input->post('receivableAccount') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $country = explode('|', trim($this->input->post('country') ?? ''));
        $data['isActive'] = $isactive;
        $data['secondaryCode'] = trim($this->input->post('customercode') ?? '');
        $data['customerName'] = trim($this->input->post('customerName') ?? '');
        $data['customerCountry'] = $companyData['company_country']; //$country[0];
        $data['customerTelephone'] = trim($this->input->post('customerTelephone') ?? '');
        $data['customerUrl'] = ''; //trim($this->input->post('customerUrl') ?? '');
        $data['customerUrl'] = ''; //trim($this->input->post('customerUrl') ?? '');
        $data['customerFax'] = ''; //trim($this->input->post('customerFax') ?? '');
        $data['customerAddress1'] = trim($this->input->post('customerAddress1') ?? '');
        $data['customerAddress2'] = ''; //trim($this->input->post('customerAddress2') ?? '');
        $data['taxGroupID'] = ''; //trim($this->input->post('customertaxgroup') ?? '');
        $data['partyCategoryID'] = trim($this->input->post('partyCategoryID') ?? '');
        $data['receivableAutoID'] = $controlAccount['GLAutoID']; // $liability['GLAutoID'];
        $data['receivableSystemGLCode'] = $controlAccount['systemAccountCode'];
        $data['receivableGLAccount'] = $controlAccount['GLSecondaryCode'];
        $data['receivableDescription'] = $controlAccount['GLDescription'];
        $data['receivableType'] = $controlAccount['subCategory'];
        $data['customerCreditPeriod'] = ''; //trim($this->input->post('customerCreditPeriod') ?? '');
        $data['customerCreditLimit'] = ''; //trim($this->input->post('customerCreditLimit') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->load->library('sequence');
        $data['customerCurrencyID'] = trim($this->input->post('customerCurrency') ?? '');
        $data['customerCurrency'] = $currency_code[0];
        $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal($data['customerCurrency']);
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['customerSystemCode'] = $this->sequence->sequence_generator('CUS');
        $this->db->insert('srp_erp_customermaster', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Customer : ' . $data['customerName'] . ' Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Customer : ' . $data['customerName'] . ' Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id, 'customerName' => $data['customerName']);
        }
    }

    function sync_customer_invoice()
    {
        $companyID = current_companyID();
        $cusData = $this->db->query("SELECT * FROM `srp_erp_customerinvoicemaster_sync` WHERE companyID=$companyID AND isUpdated!=1")->result_array();
        if (!empty($cusData)) {
            foreach ($cusData as $mastersync) {
                $syncMaster['wareHouseAutoID'] = $mastersync['wareHouseAutoID'];
                $syncMaster['invoiceType'] = $mastersync['invoiceType'];
                $syncMaster['documentID'] = $mastersync['documentID'];
                $syncMaster['rrvrID'] = $mastersync['rrvrID'];
                $syncMaster['posTypeID'] = $mastersync['posTypeID'];
                $syncMaster['posMasterAutoID'] = $mastersync['posMasterAutoID'];
                $syncMaster['invoiceDate'] = $mastersync['invoiceDate'];
                $syncMaster['invoiceDueDate'] = $mastersync['invoiceDueDate'];
                $syncMaster['customerInvoiceDate'] = $mastersync['customerInvoiceDate'];
                $syncMaster['referenceNo'] = $mastersync['referenceNo'];
                $syncMaster['invoiceNarration'] = $mastersync['invoiceNarration'];
                $syncMaster['invoiceNote'] = $mastersync['invoiceNote'];
                $syncMaster['salesPersonID'] = $mastersync['salesPersonID'];
                $syncMaster['SalesPersonCode'] = $mastersync['SalesPersonCode'];
                $syncMaster['bankGLAutoID'] = $mastersync['bankGLAutoID'];
                $syncMaster['bankSystemAccountCode'] = $mastersync['bankSystemAccountCode'];
                $syncMaster['bankGLSecondaryCode'] = $mastersync['bankGLSecondaryCode'];
                $syncMaster['bankCurrencyID'] = $mastersync['bankCurrencyID'];
                $syncMaster['bankCurrency'] = $mastersync['bankCurrency'];
                $syncMaster['invoicebank'] = $mastersync['invoicebank'];
                $syncMaster['invoicebankBranch'] = $mastersync['invoicebankBranch'];
                $syncMaster['invoicebankSwiftCode'] = $mastersync['invoicebankSwiftCode'];
                $syncMaster['invoicebankAccount'] = $mastersync['invoicebankAccount'];
                $syncMaster['invoicebankType'] = $mastersync['invoicebankType'];
                $syncMaster['companyFinanceYearID'] = $mastersync['companyFinanceYearID'];
                $syncMaster['companyFinanceYear'] = $mastersync['companyFinanceYear'];
                $syncMaster['FYBegin'] = $mastersync['FYBegin'];
                $syncMaster['FYEnd'] = $mastersync['FYEnd'];
                $syncMaster['FYPeriodDateFrom'] = $mastersync['FYPeriodDateFrom'];
                $syncMaster['FYPeriodDateTo'] = $mastersync['FYPeriodDateTo'];
                $syncMaster['companyFinancePeriodID'] = $mastersync['companyFinancePeriodID'];
                $syncMaster['contactPersonName'] = $mastersync['contactPersonName'];
                $syncMaster['contactPersonNumber'] = $mastersync['contactPersonNumber'];
                $syncMaster['customerID'] = $mastersync['customerID'];
                $syncMaster['customerSystemCode'] = $mastersync['customerSystemCode'];
                $syncMaster['customerName'] = $mastersync['customerName'];
                $syncMaster['customerAddress'] = $mastersync['customerAddress'];
                $syncMaster['customerTelephone'] = $mastersync['customerTelephone'];
                $syncMaster['customerFax'] = $mastersync['customerFax'];
                $syncMaster['customerEmail'] = $mastersync['customerEmail'];
                $syncMaster['customerReceivableAutoID'] = $mastersync['customerReceivableAutoID'];
                $syncMaster['customerReceivableSystemGLCode'] = $mastersync['customerReceivableSystemGLCode'];
                $syncMaster['customerReceivableGLAccount'] = $mastersync['customerReceivableGLAccount'];
                $syncMaster['customerReceivableDescription'] = $mastersync['customerReceivableDescription'];
                $syncMaster['customerReceivableType'] = $mastersync['customerReceivableType'];
                $syncMaster['deliveryNoteSystemCode'] = $mastersync['deliveryNoteSystemCode'];
                $syncMaster['isPrintDN'] = $mastersync['isPrintDN'];
                $syncMaster['transactionCurrencyID'] = $mastersync['transactionCurrencyID'];
                $syncMaster['transactionCurrency'] = $mastersync['transactionCurrency'];
                $syncMaster['transactionExchangeRate'] = $mastersync['transactionExchangeRate'];
                $syncMaster['transactionAmount'] = $mastersync['transactionAmount'];
                $syncMaster['transactionCurrencyDecimalPlaces'] = $mastersync['transactionCurrencyDecimalPlaces'];
                $syncMaster['companyLocalCurrencyID'] = $mastersync['companyLocalCurrencyID'];
                $syncMaster['companyLocalCurrency'] = $mastersync['companyLocalCurrency'];
                $syncMaster['companyLocalExchangeRate'] = $mastersync['companyLocalExchangeRate'];
                $syncMaster['companyLocalAmount'] = $mastersync['companyLocalAmount'];
                $syncMaster['companyLocalCurrencyDecimalPlaces'] = $mastersync['companyLocalCurrencyDecimalPlaces'];
                $syncMaster['companyReportingCurrencyID'] = $mastersync['companyReportingCurrencyID'];
                $syncMaster['companyReportingCurrency'] = $mastersync['companyReportingCurrency'];
                $syncMaster['companyReportingExchangeRate'] = $mastersync['companyReportingExchangeRate'];
                $syncMaster['companyReportingAmount'] = $mastersync['companyReportingAmount'];
                $syncMaster['companyReportingCurrencyDecimalPlaces'] = $mastersync['companyReportingCurrencyDecimalPlaces'];
                $syncMaster['customerCurrencyID'] = $mastersync['customerCurrencyID'];
                $syncMaster['customerCurrency'] = $mastersync['customerCurrency'];
                $syncMaster['customerCurrencyExchangeRate'] = $mastersync['customerCurrencyExchangeRate'];
                $syncMaster['customerCurrencyAmount'] = $mastersync['customerCurrencyAmount'];
                $syncMaster['customerCurrencyDecimalPlaces'] = $mastersync['customerCurrencyDecimalPlaces'];
                $syncMaster['receiptInvoiceYN'] = $mastersync['receiptInvoiceYN'];
                $syncMaster['tempInvoiceID'] = $mastersync['tempInvoiceID'];
                $syncMaster['receiptTotalAmount'] = $mastersync['receiptTotalAmount'];
                $syncMaster['creditNoteTotalAmount'] = $mastersync['creditNoteTotalAmount'];
                $syncMaster['advanceMatchedTotal'] = $mastersync['advanceMatchedTotal'];
                $syncMaster['isDeleted'] = $mastersync['isDeleted'];
                $syncMaster['deletedEmpID'] = $mastersync['deletedEmpID'];
                $syncMaster['deletedDate'] = $mastersync['deletedDate'];
                $syncMaster['showTaxSummaryYN'] = $mastersync['showTaxSummaryYN'];
                $syncMaster['confirmedYN'] = $mastersync['confirmedYN'];
                $syncMaster['confirmedByEmpID'] = $mastersync['confirmedByEmpID'];
                $syncMaster['confirmedByName'] = $mastersync['confirmedByName'];
                $syncMaster['confirmedDate'] = $mastersync['confirmedDate'];
                $syncMaster['approvedYN'] = $mastersync['approvedYN'];
                $syncMaster['approvedDate'] = $mastersync['approvedDate'];
                $syncMaster['currentLevelNo'] = $mastersync['currentLevelNo'];
                $syncMaster['approvedbyEmpID'] = $mastersync['approvedbyEmpID'];
                $syncMaster['approvedbyEmpName'] = $mastersync['approvedbyEmpName'];
                $syncMaster['segmentID'] = $mastersync['segmentID'];
                $syncMaster['segmentCode'] = $mastersync['segmentCode'];
                $syncMaster['companyID'] = $mastersync['companyID'];
                $syncMaster['companyCode'] = $mastersync['companyCode'];
                $syncMaster['createdUserGroup'] = $this->common_data['user_group'];
                $syncMaster['createdPCID'] = $this->common_data['current_pc'];
                $syncMaster['createdUserID'] = $this->common_data['current_userID'];
                $syncMaster['createdUserName'] = $this->common_data['current_user'];
                $syncMaster['createdDateTime'] = $this->common_data['current_date'];
                $syncMaster['timestamp'] = $mastersync['timestamp'];
                $syncMaster['is_sync'] = $mastersync['is_sync'];
                $syncMaster['id_store'] = $mastersync['id_store'];
                $this->load->library('sequence');
                $invYear = date("Y", strtotime($mastersync['invoiceDate']));
                $invMonth = date("m", strtotime($mastersync['invoiceDate']));

                $syncMaster['invoiceCode'] = $this->sequence->sequence_generator_fin($mastersync['documentID'], $mastersync['companyFinanceYearID'], $invYear, $invMonth);

                $invmaster = $this->db->insert('srp_erp_customerinvoicemaster', $syncMaster);
                $last_idm = $this->db->insert_id();
                if ($invmaster) {
                    $updateinvsync = array(
                        'relatedMasterAutoID' => $last_idm,
                        'isUpdated' => 1,
                    );
                    $this->db->where('invoiceAutoID', trim($mastersync['invoiceAutoID'] ?? ''));
                    $this->db->update('srp_erp_customerinvoicemaster_sync', $updateinvsync);

                    $syncinvid = $mastersync['invoiceAutoID'];
                    $syncdocapproved = $this->db->query("SELECT * FROM `srp_erp_documentapproved_sync` WHERE companyID=$companyID AND isUpdated!=1 AND documentSystemCode=$syncinvid AND documentID='CINV' ")->result_array();

                    foreach ($syncdocapproved as $socappr) {
                        $syncdocapp['wareHouseAutoID'] = $socappr['wareHouseAutoID'];
                        $syncdocapp['departmentID'] = $socappr['departmentID'];
                        $syncdocapp['documentID'] = $socappr['documentID'];
                        $syncdocapp['documentSystemCode'] = $last_idm;
                        $syncdocapp['documentCode'] = $syncMaster['invoiceCode'];
                        $syncdocapp['isCancel'] = $socappr['isCancel'];
                        $syncdocapp['documentDate'] = $socappr['documentDate'];
                        $syncdocapp['approvalLevelID'] = $socappr['approvalLevelID'];
                        $syncdocapp['isReverseApplicableYN'] = $socappr['isReverseApplicableYN'];
                        $syncdocapp['roleID'] = $socappr['roleID'];
                        $syncdocapp['approvalGroupID'] = $socappr['approvalGroupID'];
                        $syncdocapp['roleLevelOrder'] = $socappr['roleLevelOrder'];
                        $syncdocapp['docConfirmedDate'] = $socappr['docConfirmedDate'];
                        $syncdocapp['docConfirmedByEmpID'] = $socappr['docConfirmedByEmpID'];
                        $syncdocapp['table_name'] = 'srp_erp_customerinvoicemaster';
                        $syncdocapp['table_unique_field_name'] = $socappr['table_unique_field_name'];
                        $syncdocapp['approvedEmpID'] = $socappr['approvedEmpID'];
                        $syncdocapp['approvedYN'] = $socappr['approvedYN'];
                        $syncdocapp['approvedDate'] = $socappr['approvedDate'];
                        $syncdocapp['approvedComments'] = $socappr['approvedComments'];
                        $syncdocapp['approvedPC'] = $socappr['approvedPC'];
                        $syncdocapp['companyID'] = $socappr['companyID'];
                        $syncdocapp['companyCode'] = $socappr['companyCode'];
                        $syncdocapp['timeStamp'] = $socappr['timeStamp'];
                        $syncdocapp['is_sync'] = $socappr['is_sync'];
                        $syncdocapp['id_store'] = $socappr['id_store'];

                        $invdocappmaster = $this->db->insert('srp_erp_documentapproved', $syncdocapp);
                        $last_dam = $this->db->insert_id();
                        if ($invdocappmaster) {
                            $updateinvsyncdocapp = array(
                                'relatedMasterAutoID' => $last_dam,
                                'isUpdated' => 1,
                            );
                            $this->db->where('documentApprovedID', trim($socappr['documentApprovedID'] ?? ''));
                            $this->db->update('srp_erp_documentapproved_sync', $updateinvsyncdocapp);
                        }
                    }

                    $syncgenaralleg = $this->db->query("SELECT * FROM `srp_erp_generalledger_sync` WHERE companyID=$companyID AND isUpdated!=1 AND documentMasterAutoID=$syncinvid AND documentCode='CINV' ")->result_array();

                    foreach ($syncgenaralleg as $ledgr) {
                        $syncgenledg['wareHouseAutoID'] = $ledgr['wareHouseAutoID'];
                        $syncgenledg['documentCode'] = $ledgr['documentCode'];
                        $syncgenledg['documentMasterAutoID'] = $last_idm;
                        $syncgenledg['documentDetailAutoID'] = $ledgr['documentDetailAutoID'];
                        $syncgenledg['documentSystemCode'] = $syncMaster['invoiceCode'];
                        $syncgenledg['documentType'] = $ledgr['documentType'];
                        $syncgenledg['documentDate'] = $ledgr['documentDate'];
                        $syncgenledg['documentYear'] = $ledgr['documentYear'];
                        $syncgenledg['documentMonth'] = $ledgr['documentMonth'];
                        $syncgenledg['projectID'] = $ledgr['projectID'];
                        $syncgenledg['projectExchangeRate'] = $ledgr['projectExchangeRate'];
                        $syncgenledg['documentNarration'] = $ledgr['documentNarration'];
                        $syncgenledg['chequeNumber'] = $ledgr['chequeNumber'];
                        $syncgenledg['GLAutoID'] = $ledgr['GLAutoID'];
                        $syncgenledg['systemGLCode'] = $ledgr['systemGLCode'];
                        $syncgenledg['GLCode'] = $ledgr['GLCode'];
                        $syncgenledg['GLDescription'] = $ledgr['GLDescription'];
                        $syncgenledg['GLType'] = $ledgr['GLType'];
                        $syncgenledg['amount_type'] = $ledgr['amount_type'];
                        $syncgenledg['isFromItem'] = $ledgr['isFromItem'];
                        $syncgenledg['transactionCurrencyID'] = $ledgr['transactionCurrencyID'];
                        $syncgenledg['transactionCurrency'] = $ledgr['transactionCurrency'];
                        $syncgenledg['transactionExchangeRate'] = $ledgr['transactionExchangeRate'];
                        $syncgenledg['transactionAmount'] = $ledgr['transactionAmount'];
                        $syncgenledg['transactionCurrencyDecimalPlaces'] = $ledgr['transactionCurrencyDecimalPlaces'];
                        $syncgenledg['companyLocalCurrencyID'] = $ledgr['companyLocalCurrencyID'];
                        $syncgenledg['companyLocalCurrency'] = $ledgr['companyLocalCurrency'];
                        $syncgenledg['companyLocalExchangeRate'] = $ledgr['companyLocalExchangeRate'];
                        $syncgenledg['companyLocalAmount'] = $ledgr['companyLocalAmount'];
                        $syncgenledg['companyLocalCurrencyDecimalPlaces'] = $ledgr['companyLocalCurrencyDecimalPlaces'];
                        $syncgenledg['companyReportingCurrencyID'] = $ledgr['companyReportingCurrencyID'];
                        $syncgenledg['companyReportingCurrency'] = $ledgr['companyReportingCurrency'];
                        $syncgenledg['companyReportingExchangeRate'] = $ledgr['companyReportingExchangeRate'];
                        $syncgenledg['companyReportingAmount'] = $ledgr['companyReportingAmount'];
                        $syncgenledg['companyReportingCurrencyDecimalPlaces'] = $ledgr['companyReportingCurrencyDecimalPlaces'];
                        $syncgenledg['partyContractID'] = $ledgr['partyContractID'];
                        $syncgenledg['partyType'] = $ledgr['partyType'];
                        $syncgenledg['partyAutoID'] = $ledgr['partyAutoID'];
                        $syncgenledg['partySystemCode'] = $ledgr['partySystemCode'];
                        $syncgenledg['partyName'] = $ledgr['partyName'];
                        $syncgenledg['partyCurrencyID'] = $ledgr['partyCurrencyID'];
                        $syncgenledg['partyCurrency'] = $ledgr['partyCurrency'];
                        $syncgenledg['partyExchangeRate'] = $ledgr['partyExchangeRate'];
                        $syncgenledg['partyCurrencyAmount'] = $ledgr['partyCurrencyAmount'];
                        $syncgenledg['partyCurrencyDecimalPlaces'] = $ledgr['partyCurrencyDecimalPlaces'];
                        $syncgenledg['subLedgerType'] = $ledgr['subLedgerType'];
                        $syncgenledg['subLedgerDesc'] = $ledgr['subLedgerDesc'];
                        $syncgenledg['taxMasterAutoID'] = $ledgr['taxMasterAutoID'];
                        $syncgenledg['partyVatIdNo'] = $ledgr['partyVatIdNo'];
                        $syncgenledg['is_sync'] = $ledgr['is_sync'];
                        $syncgenledg['id_store'] = $ledgr['id_store'];
                        $syncgenledg['isAddon'] = $ledgr['isAddon'];
                        $syncgenledg['confirmedByEmpID'] = $ledgr['confirmedByEmpID'];
                        $syncgenledg['confirmedByName'] = $ledgr['confirmedByName'];
                        $syncgenledg['confirmedDate'] = $ledgr['confirmedDate'];
                        $syncgenledg['approvedDate'] = $ledgr['approvedDate'];
                        $syncgenledg['approvedbyEmpID'] = $ledgr['approvedbyEmpID'];
                        $syncgenledg['approvedbyEmpName'] = $ledgr['approvedbyEmpName'];
                        $syncgenledg['segmentID'] = $ledgr['segmentID'];
                        $syncgenledg['segmentCode'] = $ledgr['segmentCode'];
                        $syncgenledg['companyID'] = $ledgr['companyID'];
                        $syncgenledg['companyCode'] = $ledgr['companyCode'];
                        $syncgenledg['OtherFeesID'] = $ledgr['OtherFeesID'];
                        $syncgenledg['createdUserGroup'] = $this->common_data['user_group'];
                        $syncgenledg['createdPCID'] = $this->common_data['current_pc'];
                        $syncgenledg['createdUserID'] = $this->common_data['current_userID'];
                        $syncgenledg['createdUserName'] = $this->common_data['current_user'];
                        $syncgenledg['createdDateTime'] = $this->common_data['current_date'];

                        $invgenleg = $this->db->insert('srp_erp_generalledger', $syncgenledg);
                        $last_genleg = $this->db->insert_id();
                        if ($invgenleg) {
                            $updateinvsyncgenleg = array(
                                'relatedMasterAutoID' => $last_genleg,
                                'isUpdated' => 1,
                            );
                            $this->db->where('generalLedgerAutoID', trim($ledgr['generalLedgerAutoID'] ?? ''));
                            $this->db->update('srp_erp_generalledger_sync', $updateinvsyncgenleg);
                        }
                    }

                    $syncitemleg = $this->db->query("SELECT * FROM `srp_erp_itemledger_sync` WHERE companyID=$companyID AND isUpdated!=1 AND documentAutoID=$syncinvid AND documentCode='CINV' ")->result_array();

                    foreach ($syncitemleg as $itmledgr) {
                        $syncgenitmledg['documentID'] = $itmledgr['documentID'];
                        $syncgenitmledg['documentAutoID'] = $last_idm;
                        $syncgenitmledg['documentCode'] = $itmledgr['documentCode'];
                        $syncgenitmledg['documentSystemCode'] = $syncMaster['invoiceCode'];
                        $syncgenitmledg['documentDate'] = $itmledgr['documentDate'];
                        $syncgenitmledg['referenceNumber'] = $itmledgr['referenceNumber'];
                        $syncgenitmledg['companyFinanceYearID'] = $itmledgr['companyFinanceYearID'];
                        $syncgenitmledg['companyFinanceYear'] = $itmledgr['companyFinanceYear'];
                        $syncgenitmledg['FYBegin'] = $itmledgr['FYBegin'];
                        $syncgenitmledg['FYEnd'] = $itmledgr['FYEnd'];
                        $syncgenitmledg['FYPeriodDateFrom'] = $itmledgr['FYPeriodDateFrom'];
                        $syncgenitmledg['FYPeriodDateTo'] = $itmledgr['FYPeriodDateTo'];
                        $syncgenitmledg['wareHouseAutoID'] = $itmledgr['wareHouseAutoID'];
                        $syncgenitmledg['wareHouseCode'] = $itmledgr['wareHouseCode'];
                        $syncgenitmledg['wareHouseLocation'] = $itmledgr['wareHouseLocation'];
                        $syncgenitmledg['wareHouseDescription'] = $itmledgr['wareHouseDescription'];
                        $syncgenitmledg['projectID'] = $itmledgr['projectID'];
                        $syncgenitmledg['projectExchangeRate'] = $itmledgr['projectExchangeRate'];
                        $syncgenitmledg['itemAutoID'] = $itmledgr['itemAutoID'];
                        $syncgenitmledg['itemSystemCode'] = $itmledgr['itemSystemCode'];
                        $syncgenitmledg['ItemSecondaryCode'] = $itmledgr['ItemSecondaryCode'];
                        $syncgenitmledg['itemDescription'] = $itmledgr['itemDescription'];
                        $syncgenitmledg['defaultUOMID'] = $itmledgr['defaultUOMID'];
                        $syncgenitmledg['defaultUOM'] = $itmledgr['defaultUOM'];
                        $syncgenitmledg['transactionUOMID'] = $itmledgr['transactionUOMID'];
                        $syncgenitmledg['transactionUOM'] = $itmledgr['transactionUOM'];
                        $syncgenitmledg['transactionQTY'] = $itmledgr['transactionQTY'];
                        $syncgenitmledg['convertionRate'] = $itmledgr['convertionRate'];
                        $syncgenitmledg['currentStock'] = $itmledgr['currentStock'];
                        $syncgenitmledg['PLGLAutoID'] = $itmledgr['PLGLAutoID'];
                        $syncgenitmledg['PLSystemGLCode'] = $itmledgr['PLSystemGLCode'];
                        $syncgenitmledg['PLGLCode'] = $itmledgr['PLGLCode'];
                        $syncgenitmledg['PLDescription'] = $itmledgr['PLDescription'];
                        $syncgenitmledg['PLType'] = $itmledgr['PLType'];
                        $syncgenitmledg['BLGLAutoID'] = $itmledgr['BLGLAutoID'];
                        $syncgenitmledg['BLSystemGLCode'] = $itmledgr['BLSystemGLCode'];
                        $syncgenitmledg['BLGLCode'] = $itmledgr['BLGLCode'];
                        $syncgenitmledg['BLDescription'] = $itmledgr['BLDescription'];
                        $syncgenitmledg['BLType'] = $itmledgr['BLType'];
                        $syncgenitmledg['transactionCurrencyID'] = $itmledgr['transactionCurrencyID'];
                        $syncgenitmledg['transactionCurrency'] = $itmledgr['transactionCurrency'];
                        $syncgenitmledg['transactionExchangeRate'] = $itmledgr['transactionExchangeRate'];
                        $syncgenitmledg['transactionAmount'] = $itmledgr['transactionAmount'];
                        $syncgenitmledg['transactionCurrencyDecimalPlaces'] = $itmledgr['transactionCurrencyDecimalPlaces'];
                        $syncgenitmledg['companyLocalCurrencyID'] = $itmledgr['companyLocalCurrencyID'];
                        $syncgenitmledg['companyLocalCurrency'] = $itmledgr['companyLocalCurrency'];
                        $syncgenitmledg['companyLocalExchangeRate'] = $itmledgr['companyLocalExchangeRate'];
                        $syncgenitmledg['companyLocalAmount'] = $itmledgr['companyLocalAmount'];
                        $syncgenitmledg['companyLocalWacAmount'] = $itmledgr['companyLocalWacAmount'];
                        $syncgenitmledg['companyLocalCurrencyDecimalPlaces'] = $itmledgr['companyLocalCurrencyDecimalPlaces'];
                        $syncgenitmledg['companyReportingCurrencyID'] = $itmledgr['companyReportingCurrencyID'];
                        $syncgenitmledg['companyReportingCurrency'] = $itmledgr['companyReportingCurrency'];
                        $syncgenitmledg['companyReportingExchangeRate'] = $itmledgr['companyReportingExchangeRate'];
                        $syncgenitmledg['companyReportingAmount'] = $itmledgr['companyReportingAmount'];
                        $syncgenitmledg['companyReportingWacAmount'] = $itmledgr['companyReportingWacAmount'];
                        $syncgenitmledg['companyReportingCurrencyDecimalPlaces'] = $itmledgr['companyReportingCurrencyDecimalPlaces'];
                        $syncgenitmledg['partyCurrencyID'] = $itmledgr['partyCurrencyID'];
                        $syncgenitmledg['partyCurrency'] = $itmledgr['partyCurrency'];
                        $syncgenitmledg['partyCurrencyExchangeRate'] = $itmledgr['partyCurrencyExchangeRate'];
                        $syncgenitmledg['partyCurrencyAmount'] = $itmledgr['partyCurrencyAmount'];
                        $syncgenitmledg['partyCurrencyDecimalPlaces'] = $itmledgr['partyCurrencyDecimalPlaces'];
                        $syncgenitmledg['salesPrice'] = $itmledgr['salesPrice'];
                        $syncgenitmledg['confirmedYN'] = $itmledgr['confirmedYN'];
                        $syncgenitmledg['confirmedByEmpID'] = $itmledgr['confirmedByEmpID'];
                        $syncgenitmledg['confirmedByName'] = $itmledgr['confirmedByName'];
                        $syncgenitmledg['confirmedDate'] = $itmledgr['confirmedDate'];
                        $syncgenitmledg['approvedYN'] = $itmledgr['approvedYN'];
                        $syncgenitmledg['approvedDate'] = $itmledgr['approvedDate'];
                        $syncgenitmledg['approvedbyEmpID'] = $itmledgr['approvedbyEmpID'];
                        $syncgenitmledg['approvedbyEmpName'] = $itmledgr['approvedbyEmpName'];
                        $syncgenitmledg['segmentID'] = $itmledgr['segmentID'];
                        $syncgenitmledg['segmentCode'] = $itmledgr['segmentCode'];
                        $syncgenitmledg['companyID'] = $itmledgr['companyID'];
                        $syncgenitmledg['companyCode'] = $itmledgr['companyCode'];
                        $syncgenitmledg['narration'] = $itmledgr['narration'];
                        $syncgenitmledg['expenseGLAutoID'] = $itmledgr['expenseGLAutoID'];
                        $syncgenitmledg['expenseGLCode'] = $itmledgr['expenseGLCode'];
                        $syncgenitmledg['expenseSystemGLCode'] = $itmledgr['expenseSystemGLCode'];
                        $syncgenitmledg['expenseGLDescription'] = $itmledgr['expenseGLDescription'];
                        $syncgenitmledg['expenseGLType'] = $itmledgr['expenseGLType'];
                        $syncgenitmledg['revenueGLAutoID'] = $itmledgr['revenueGLAutoID'];
                        $syncgenitmledg['revenueGLCode'] = $itmledgr['revenueGLCode'];
                        $syncgenitmledg['revenueSystemGLCode'] = $itmledgr['revenueSystemGLCode'];
                        $syncgenitmledg['revenueGLDescription'] = $itmledgr['revenueGLDescription'];
                        $syncgenitmledg['revenueGLType'] = $itmledgr['revenueGLType'];
                        $syncgenitmledg['assetGLAutoID'] = $itmledgr['assetGLAutoID'];
                        $syncgenitmledg['assetGLCode'] = $itmledgr['assetGLCode'];
                        $syncgenitmledg['assetSystemGLCode'] = $itmledgr['assetSystemGLCode'];
                        $syncgenitmledg['assetGLDescription'] = $itmledgr['assetGLDescription'];
                        $syncgenitmledg['assetGLType'] = $itmledgr['assetGLType'];
                        $syncgenitmledg['is_sync'] = $itmledgr['is_sync'];
                        $syncgenitmledg['id_store'] = $itmledgr['id_store'];
                        $syncgenitmledg['createdUserGroup'] = $this->common_data['user_group'];
                        $syncgenitmledg['createdPCID'] = $this->common_data['current_pc'];
                        $syncgenitmledg['createdUserID'] = $this->common_data['current_userID'];
                        $syncgenitmledg['createdUserName'] = $this->common_data['current_user'];
                        $syncgenitmledg['createdDateTime'] = $this->common_data['current_date'];


                        $invitmleg = $this->db->insert('srp_erp_itemledger', $syncgenitmledg);
                        $last_itmleg = $this->db->insert_id();
                        if ($invitmleg) {
                            $updateinvsyncitmleg = array(
                                'relatedMasterAutoID' => $last_itmleg,
                                'isUpdated' => 1,
                            );
                            $this->db->where('itemLedgerAutoID', trim($itmledgr['itemLedgerAutoID'] ?? ''));
                            $this->db->update('srp_erp_itemledger_sync', $updateinvsyncitmleg);
                        }
                    }

                    $syncInvID = $mastersync['invoiceAutoID'];
                    $cusDetailData = $this->db->query("SELECT * FROM `srp_erp_customerinvoicedetails_sync` WHERE companyID=$companyID AND isUpdated!=1 AND invoiceAutoID=$syncInvID ")->result_array();
                    foreach ($cusDetailData as $isdd) {
                        $syncDetail['invoiceAutoID'] = $last_idm;
                        $syncDetail['tempinvoiceDetailID'] = $isdd['tempinvoiceDetailID'];
                        $syncDetail['type'] = $isdd['type'];
                        $syncDetail['contractAutoID'] = $isdd['contractAutoID'];
                        $syncDetail['contractDetailsAutoID'] = $isdd['contractDetailsAutoID'];
                        $syncDetail['contractCode'] = $isdd['contractCode'];
                        $syncDetail['projectID'] = $isdd['projectID'];
                        $syncDetail['projectExchangeRate'] = $isdd['projectExchangeRate'];
                        $syncDetail['itemAutoID'] = $isdd['itemAutoID'];
                        $syncDetail['itemSystemCode'] = $isdd['itemSystemCode'];
                        $syncDetail['itemDescription'] = $isdd['itemDescription'];
                        $syncDetail['itemCategory'] = $isdd['itemCategory'];
                        $syncDetail['expenseGLAutoID'] = $isdd['expenseGLAutoID'];
                        $syncDetail['expenseSystemGLCode'] = $isdd['expenseSystemGLCode'];
                        $syncDetail['expenseGLCode'] = $isdd['expenseGLCode'];
                        $syncDetail['expenseGLDescription'] = $isdd['expenseGLDescription'];
                        $syncDetail['expenseGLType'] = $isdd['expenseGLType'];
                        $syncDetail['revenueGLAutoID'] = $isdd['revenueGLAutoID'];
                        $syncDetail['revenueGLCode'] = $isdd['revenueGLCode'];
                        $syncDetail['revenueSystemGLCode'] = $isdd['revenueSystemGLCode'];
                        $syncDetail['revenueGLDescription'] = $isdd['revenueGLDescription'];
                        $syncDetail['revenueGLType'] = $isdd['revenueGLType'];
                        $syncDetail['assetGLAutoID'] = $isdd['assetGLAutoID'];
                        $syncDetail['assetGLCode'] = $isdd['assetGLCode'];
                        $syncDetail['assetSystemGLCode'] = $isdd['assetSystemGLCode'];
                        $syncDetail['assetGLDescription'] = $isdd['assetGLDescription'];
                        $syncDetail['taxMasterAutoID'] = $isdd['taxMasterAutoID'];
                        $syncDetail['taxPercentage'] = $isdd['taxPercentage'];
                        $syncDetail['assetGLType'] = $isdd['assetGLType'];
                        $syncDetail['wareHouseAutoID'] = $isdd['wareHouseAutoID'];
                        $syncDetail['wareHouseCode'] = $isdd['wareHouseCode'];
                        $syncDetail['wareHouseLocation'] = $isdd['wareHouseLocation'];
                        $syncDetail['wareHouseDescription'] = $isdd['wareHouseDescription'];
                        $syncDetail['defaultUOMID'] = $isdd['defaultUOMID'];
                        $syncDetail['defaultUOM'] = $isdd['defaultUOM'];
                        $syncDetail['unitOfMeasureID'] = $isdd['unitOfMeasureID'];
                        $syncDetail['unitOfMeasure'] = $isdd['unitOfMeasure'];
                        $syncDetail['conversionRateUOM'] = $isdd['conversionRateUOM'];
                        $syncDetail['contractQty'] = $isdd['contractQty'];
                        $syncDetail['contractAmount'] = $isdd['contractAmount'];
                        $syncDetail['requestedQty'] = $isdd['requestedQty'];
                        $syncDetail['noOfItems'] = $isdd['noOfItems'];
                        $syncDetail['grossQty'] = $isdd['grossQty'];
                        $syncDetail['noOfUnits'] = $isdd['noOfUnits'];
                        $syncDetail['deduction'] = $isdd['deduction'];
                        $syncDetail['comment'] = $isdd['comment'];
                        $syncDetail['remarks'] = $isdd['remarks'];
                        $syncDetail['description'] = $isdd['description'];
                        $syncDetail['companyLocalWacAmount'] = $isdd['companyLocalWacAmount'];
                        $syncDetail['unittransactionAmount'] = $isdd['unittransactionAmount'];
                        $syncDetail['transactionAmount'] = $isdd['transactionAmount'];
                        $syncDetail['companyLocalAmount'] = $isdd['companyLocalAmount'];
                        $syncDetail['companyReportingAmount'] = $isdd['companyReportingAmount'];
                        $syncDetail['customerAmount'] = $isdd['customerAmount'];
                        $syncDetail['segmentID'] = $isdd['segmentID'];
                        $syncDetail['segmentCode'] = $isdd['segmentCode'];
                        $syncDetail['companyID'] = $isdd['companyID'];
                        $syncDetail['companyCode'] = $isdd['companyCode'];
                        $syncDetail['discountPercentage'] = $isdd['discountPercentage'];
                        $syncDetail['discountAmount'] = $isdd['discountAmount'];
                        $syncDetail['taxDescription'] = $isdd['taxDescription'];
                        $syncDetail['taxAmount'] = $isdd['taxAmount'];
                        $syncDetail['totalAfterTax'] = $isdd['totalAfterTax'];
                        $syncDetail['taxShortCode'] = $isdd['taxShortCode'];
                        $syncDetail['taxSupplierAutoID'] = $isdd['taxSupplierAutoID'];
                        $syncDetail['taxSupplierSystemCode'] = $isdd['taxSupplierSystemCode'];
                        $syncDetail['taxSupplierName'] = $isdd['taxSupplierName'];
                        $syncDetail['taxSupplierliabilityAutoID'] = $isdd['taxSupplierliabilityAutoID'];
                        $syncDetail['taxSupplierliabilitySystemGLCode'] = $isdd['taxSupplierliabilitySystemGLCode'];
                        $syncDetail['taxSupplierliabilityGLAccount'] = $isdd['taxSupplierliabilityGLAccount'];
                        $syncDetail['taxSupplierliabilityDescription'] = $isdd['taxSupplierliabilityDescription'];
                        $syncDetail['taxSupplierliabilityType'] = $isdd['taxSupplierliabilityType'];
                        $syncDetail['taxSupplierCurrencyID'] = $isdd['taxSupplierCurrencyID'];
                        $syncDetail['taxSupplierCurrency'] = $isdd['taxSupplierCurrency'];
                        $syncDetail['taxSupplierCurrencyExchangeRate'] = $isdd['taxSupplierCurrencyExchangeRate'];
                        $syncDetail['taxSupplierCurrencyAmount'] = $isdd['taxSupplierCurrencyAmount'];
                        $syncDetail['taxSupplierCurrencyDecimalPlaces'] = $isdd['taxSupplierCurrencyDecimalPlaces'];
                        $syncDetail['createdUserGroup'] = $this->common_data['user_group'];
                        $syncDetail['createdPCID'] = $this->common_data['current_pc'];
                        $syncDetail['createdUserID'] = $this->common_data['current_userID'];
                        $syncDetail['createdUserName'] = $this->common_data['current_user'];
                        $syncDetail['createdDateTime'] = $this->common_data['current_date'];
                        $syncDetail['is_sync'] = $isdd['is_sync'];
                        $syncDetail['id_store'] = $isdd['id_store'];

                        $invdetail = $this->db->insert('srp_erp_customerinvoicedetails', $syncDetail);
                        $last_idd = $this->db->insert_id();
                        if ($invdetail) {
                            $updateinvdsync = array(
                                'relatedMasterAutoID' => $last_idd,
                                'isUpdated' => 1,
                            );
                            $this->db->where('invoiceDetailsAutoID', trim($isdd['invoiceDetailsAutoID'] ?? ''));
                            $this->db->update('srp_erp_customerinvoicedetails_sync', $updateinvdsync);
                        }
                    }
                }
            }
            if ($invmaster) {
                return array('s', 'Synced Successfully');
            } else {
                return array('e', 'Syncing Failed');
            }
        } else {
            return array('e', 'No Records Found');
        }
    }

    function void_gpos()
    {
        $invoiceID = $this->input->post('invoiceID');
        $company_id = current_companyID();
        $master = $this->db->query("SELECT * FROM srp_erp_pos_invoice WHERE invoiceID={$invoiceID}")->row_array();
        $documentSystemCode = $master['documentSystemCode'];
        if ($master['isVoid']==0) {
            $voidinv['isVoid'] = 1;
            $voidinv['voidBy'] = $this->common_data['current_userID'];
            $voidinv['voidDatetime'] = $this->common_data['current_date'];
            $this->db->where('invoiceID', $invoiceID);
            $result = $this->db->update('srp_erp_pos_invoice', $voidinv);
            if ($result) {
                if ($master['isCreditSales'] == 1) {
                    $cusInovice = $this->db->query("SELECT invoiceAutoID FROM srp_erp_customerinvoicemaster WHERE posTypeID=1 AND posMasterAutoID={$invoiceID}")->row_array();
                    $invoiceAutoID = $cusInovice['invoiceAutoID'];
                    $this->db->delete('srp_erp_customerinvoicedetails', array('invoiceAutoID' => $invoiceAutoID, 'companyID' => $company_id));

                    $cusinv['isDeleted'] = 1;
                    $cusinv['confirmedYN'] = 0;
                    $cusinv['confirmedByEmpID'] = NULL;
                    $cusinv['confirmedByName'] = NULL;
                    $cusinv['confirmedDate'] = NULL;
                    $cusinv['approvedYN'] = 0;
                    $cusinv['approvedDate'] = NULL;
                    $cusinv['approvedbyEmpID'] = NULL;
                    $cusinv['approvedbyEmpName'] = NULL;
                    $cusinv['deletedEmpID'] = $this->common_data['current_userID'];
                    $cusinv['deletedDate'] = $this->common_data['current_date'];
                    $this->db->where('invoiceAutoID', $invoiceAutoID);
                    $this->db->update('srp_erp_customerinvoicemaster', $cusinv);

                    $this->db->delete('srp_erp_itemledger', array('documentAutoID' => $invoiceAutoID, 'documentID' => "CINV", 'companyID' => $company_id));
                    $this->db->delete('srp_erp_generalledger', array('documentMasterAutoID' => $invoiceAutoID, 'documentCode' => "CINV", 'companyID' => $company_id));
                    $this->db->delete('srp_erp_documentapproved', array('documentSystemCode' => "$invoiceAutoID", 'companyID' => $company_id, 'documentID' => 'CINV'));
                    //$this->db->delete('srp_erp_bankledger', array('documentMasterAutoID' => $invoiceAutoID,'documentType' => "CINV",'companyID'=>$company_id));
                } else {
                    $this->db->delete('srp_erp_itemledger', array('documentAutoID' => $invoiceID, 'documentID' => "POS", 'companyID' => $company_id));
                    $this->db->delete('srp_erp_generalledger', array('documentMasterAutoID' => $invoiceID, 'documentCode' => "POS", 'companyID' => $company_id));
                    $this->db->delete('srp_erp_bankledger', array('documentMasterAutoID' => $invoiceID, 'documentSystemCode' => "$documentSystemCode", 'companyID' => $company_id));
                }
                update_warehouse_items();
                update_item_master();
                return array('s', 'Invoice Void Successfully');
            } else {
                return array('e', 'Void Failed');
            }
        }else{
            return array('e', 'Already voided.');
        }
    }

    function get_payment_methods_by_invoice_id($invoiceID)
    {

        $sql = "SELECT
                    srp_erp_pos_invoicepayments.invoiceID,
                    srp_erp_pos_invoicepayments.paymentConfigMasterID,
                    srp_erp_pos_paymentglconfigmaster.description,
                    IF(srp_erp_pos_paymentglconfigmaster.description =  'Cash',posinvoice.cashAmount ,srp_erp_pos_invoicepayments.amount ) as amount,
                    srp_erp_pos_invoicepayments.reference
                FROM
                    srp_erp_pos_invoicepayments
                    INNER JOIN srp_erp_pos_paymentglconfigmaster ON srp_erp_pos_invoicepayments.paymentConfigMasterID = srp_erp_pos_paymentglconfigmaster.autoID 
                    LEFT JOIN srp_erp_pos_invoice posinvoice on 	posinvoice.invoiceID = srp_erp_pos_invoicepayments.invoiceID
                WHERE
                    srp_erp_pos_invoicepayments.invoiceID = " . $invoiceID;

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    function delete_UserGroup()
    {
        $userGroupMasterID = $this->input->post('userGroupMasterID');
        $posType = $this->input->post('posType');
        $result = $this->db->where('userGroupMasterID', $userGroupMasterID)->delete('srp_erp_pos_auth_usergroupmaster');
        if ($result) {
            if ($posType == 2) {
                $datas['pos_userGroupMasterID_gpos'] = null;
                $this->db->where('pos_userGroupMasterID_gpos', $userGroupMasterID);
                $this->db->where('Erp_companyID', current_companyID());
                $this->db->update('srp_employeesdetails', $datas);
            } else {
                $datas['pos_userGroupMasterID'] = null;
                $this->db->where('pos_userGroupMasterID', $userGroupMasterID);
                $this->db->where('Erp_companyID', current_companyID());
                $this->db->update('srp_employeesdetails', $datas);
            }


            return array('s', 'User Group Deleted Successfully');
        } else {
            return array('e', 'User Group Deletion Failed');
        }
    }

    function delete_Aut_process()
    {
        $processMasterID = $this->input->post('processMasterID');
        $posType = $this->input->post('posType');

        $this->db->where('companyID', current_companyID());
        $this->db->where('processMasterID', $processMasterID);
        $this->db->where('posType', $posType);
        $result = $this->db->delete('srp_erp_pos_auth_processassign');
        if ($result) {
            $this->db->where('companyID', current_companyID());
            $this->db->where('processMasterID', $processMasterID);
            $this->db->delete('srp_erp_pos_auth_usergroupdetail');
            $this->db->where('posType', $posType);
            return array('s', 'Authentication Process Deleted Successfully');
        } else {
            return array('e', 'Authentication Process Deletion Failed');
        }
    }

    function addProcess()
    {
        $this->db->select('processMasterID');
        $this->db->from('srp_erp_pos_auth_processassign');
        $this->db->where('companyID', current_companyID());
        $this->db->where('posType', 2);
        $r = $this->db->get()->result_array();

        $existRec = array_column($r, 'processMasterID');
        $process = $this->input->post("processMasterID");
        foreach ($process as $val) {
            if (!in_array($val, $existRec)) {
                $data['processMasterID'] = $val;
                $data['posType'] = 2;
                $data['companyID'] = current_companyID();
                $data['createdUserGroup'] = user_group();
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdDateTime'] = format_date_mysql_datetime();
                $data['createdUserName'] = current_user();
                $data['timestamp'] = format_date_mysql_datetime();
                $insert_query = $this->db->insert('srp_erp_pos_auth_processassign', $data);
            }
        }
        return array('error' => 0, 'message' => 'Successfully assigned');
    }

    function add_user_group()
    {
        $this->db->select('userGroupMasterID');
        $this->db->from('srp_erp_pos_auth_usergroupdetail');
        $this->db->where('companyID', current_companyID());
        $this->db->where('processMasterID', $this->input->post('processMasterID'));
        $this->db->where('wareHouseID', $this->input->post('wareHouseID'));
        $this->db->where('posType', 2);
        $r = $this->db->get()->result_array();

        $existRec = array_column($r, 'userGroupMasterID');
        $userGroupMasterID = $this->input->post("userGroupMasterID");
        $processMasterID = $this->input->post("processMasterID");
        foreach ($userGroupMasterID as $val) {
            if (!in_array($val, $existRec)) {
                $data['userGroupMasterID'] = $val;
                $data['processMasterID'] = $processMasterID;
                $data['posType'] = 2;
                $data['wareHouseID'] = $this->input->post('wareHouseID');
                $data['companyID'] = current_companyID();
                $data['createdUserGroup'] = user_group();
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdDateTime'] = format_date_mysql_datetime();
                $data['createdUserName'] = current_user();
                $data['timestamp'] = format_date_mysql_datetime();
                $insert_query = $this->db->insert('srp_erp_pos_auth_usergroupdetail', $data);
            }
        }
        return array('error' => 0, 'message' => 'Successfully assigned');
    }


    function save_usergroup_users_gpos()
    {
        $empID = $this->input->post('empID');
        $userGroupMasterID = $this->input->post('userGroupMasterID');
        $companyID = current_companyID();

        $data['pos_userGroupMasterID_gpos'] = null;
        $this->db->where('Erp_companyID', $companyID);
        $this->db->where('pos_userGroupMasterID_gpos', $userGroupMasterID);
        $this->db->update('srp_employeesdetails', $data);
        $result = true;
        if ($empID) {
            foreach ($empID as $val) {
                $datas['pos_userGroupMasterID_gpos'] = $userGroupMasterID;
                $this->db->where('EIdNo', $val);
                $result = $this->db->update('srp_employeesdetails', $datas);
            }
        }

        if ($result) {
            return array('s', 'Employees successfully added to User Group.');
        } else {
            return array('e', 'Error In adding Employees to User Group.');
        }
    }

    function fetch_assigned_users_gpos()
    {
        $result = $this->db->select('*')->from('srp_employeesdetails')->where("pos_userGroupMasterID_gpos", $this->input->post("userGroupMasterID"))->where("Erp_companyID", current_companyID())->get()->result_array();
        return $result;
    }

    function update_superadmin_warehouse()
    {
        $companyID = current_companyID();
        $autoID = $this->input->post('autoID');
        $valu = $this->input->post('valu');

        if ($valu == 1) {
            $data['superAdminYN'] = 0;
            $this->db->where('companyID', $companyID);
            $rslt = $this->db->update('srp_erp_warehouse_users', $data);
            if ($rslt) {
                $datas['superAdminYN'] = 1;
                $this->db->where('autoID', $autoID);
                $rslts = $this->db->update('srp_erp_warehouse_users', $datas);

                if ($rslts) {
                    return array('s', 'Super Admin Updated successfully.');
                }
            }
        } else {
            $data['superAdminYN'] = 0;
            $this->db->where('autoID', $autoID);
            $rslts = $this->db->update('srp_erp_warehouse_users', $data);

            if ($rslts) {
                return array('s', 'Super Admin Removed successfully.');
            }
        }
    }

    function update_warehouse_admin()
    {
        $companyID = current_companyID();
        $autoID = $this->input->post('autoID');
        $valu = $this->input->post('valu');
        $WHID = $this->input->post('WHID');

        if ($valu == 1) {
            $datas['superAdminYN'] = 0;
            $datas['wareHouseAdminYN'] = 1;
            $this->db->where('autoID', $autoID);
            $rslts = $this->db->update('srp_erp_warehouse_users', $datas);

            if ($rslts) {
                return array('s', 'Warehouse Admin Updated successfully.');
            }
        } else {
            $datas['wareHouseAdminYN'] = 0;
            $this->db->where('autoID', $autoID);
            $rslts = $this->db->update('srp_erp_warehouse_users', $datas);

            if ($rslts) {
                return array('s', 'Warehouse Admin Removed successfully.');
            }
        }
    }

    function delete_gpos_hold_bills()
    {
        $companyID = current_companyID();
        $invoiceID = $this->input->post('invoiceID');

        $this->db->where('invoiceID', $invoiceID);
        $result = $this->db->delete('srp_erp_pos_invoicehold');
        if ($result) {
            $this->db->where('invoiceID', $invoiceID);
            $this->db->where('companyID', $companyID);
            $results = $this->db->delete('srp_erp_pos_invoiceholddetail');
            if ($results) {
                return array('s', 'Invoice Deleted Successfully .');
            }
        }
    }

    function load_barcode_loyalty()
    {
        $companyID = current_companyID();
        $companyCod = current_companyCode();

        $this->db->select('serialNo');
        $this->db->from('srp_erp_pos_loyaltycard');
        $this->db->where('companyID', current_companyID());
        $this->db->order_by("serialNo", "Desc");
        $this->db->limit(1);
        $qry = $this->db->get()->row_array();
        $code = '';
        if (!empty($qry)) {
            $serialNo = $qry['serialNo'] + 1;
            $code = $companyCod . str_pad($serialNo, 6, '0', STR_PAD_LEFT);
        } else {
            $code = $companyCod . str_pad(1, 6, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    function save_loyalty_card()
    {
        $barcode = $this->input->post('barcode');
        $customerID = $this->input->post('customerID');
        $cardMasterID = $this->input->post('cardMasterID');
        $companyID = current_companyID();

        $this->db->select('serialNo');
        $this->db->from('srp_erp_pos_loyaltycard');
        $this->db->where('companyID', $companyID);
        $this->db->order_by("serialNo", "desc");
        $this->db->limit(1);
        $qry = $this->db->get()->row_array();
        if (!empty($qry)) {
            $serialNo = $qry['serialNo'] + 1;
        } else {
            $serialNo = 1;
        }

        if (trim($cardMasterID)) {
            $this->db->select('cardMasterID');
            $this->db->where('customerID', trim($customerID));
            $this->db->where('companyID', trim($companyID));
            $this->db->where('cardMasterID !=', trim($cardMasterID));
            $this->db->from('srp_erp_pos_loyaltycard');
            $cusxsist = $this->db->get()->row_array();
            if (!empty($cusxsist)) {
                return array('e', 'Customer already exist');
                exit();
            }

            $this->db->select('cardMasterID');
            $this->db->where('barcode', trim($barcode));
            $this->db->where('companyID', trim($companyID));
            $this->db->where('cardMasterID !=', trim($cardMasterID));
            $this->db->from('srp_erp_pos_loyaltycard');
            $codexsist = $this->db->get()->row_array();
            if (!empty($codexsist)) {
                return array('e', 'Barcode already exist');
                exit();
            } else {
                $data['barcode'] = $barcode;
                $data['customerID'] = $customerID;

                $data['modifiedPCID'] = current_pc();
                $data['modifiedUserID'] = current_userID();
                $data['modifiedDateTime'] = format_date_mysql_datetime();
                $data['modifiedUserName'] = current_user();
                $this->db->where('cardMasterID', trim($cardMasterID));
                $update_query = $this->db->update('srp_erp_pos_loyaltycard', $data);
                if ($update_query) {
                    return array('s', 'Loyalty Card Updated Successfully');
                } else {
                    return array('e', 'Loyalty Card update Failed');
                }
            }
        } else {

            $this->db->select('cardMasterID');
            $this->db->where('customerID', trim($customerID));
            $this->db->where('companyID', trim($companyID));
            $this->db->from('srp_erp_pos_loyaltycard');
            $cusxsist = $this->db->get()->row_array();

            if (!empty($cusxsist)) {
                return array('e', 'Customer already exist');
                exit();
            }

            $this->db->select('cardMasterID');
            $this->db->where('barcode', trim($barcode));
            $this->db->where('companyID', trim($companyID));
            $this->db->from('srp_erp_pos_loyaltycard');
            $codexsist = $this->db->get()->row_array();
            if (!empty($codexsist)) {
                return array('e', 'Barcode already exist');
                exit();
            } else {
                $data['barcode'] = $barcode;
                $data['serialNo'] = $serialNo;
                $data['customerID'] = $customerID;
                $data['companyID'] = $companyID;
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdDateTime'] = format_date_mysql_datetime();
                $data['createdUserName'] = current_user();
                $data['timestamp'] = format_date_mysql_datetime();
                $insert_query = $this->db->insert('srp_erp_pos_loyaltycard', $data);
                if ($insert_query) {
                    return array('s', 'Loyalty Card Added Successfully');
                } else {
                    return array('e', 'Loyalty Card Insert Failed');
                }
            }
        }
    }

    function edit_loyalty()
    {
        $cardMasterID = $this->input->post('cardMasterID');
        $this->db->select('cardMasterID,barcode,srp_erp_pos_loyaltycard.customerID,srp_erp_customermaster.customerName,srp_erp_customermaster.customerTelephone');
        $this->db->where('cardMasterID', trim($cardMasterID));
        $this->db->join('srp_erp_customermaster', 'srp_erp_pos_loyaltycard.customerID = srp_erp_customermaster.customerAutoID', 'left');
        $this->db->from('srp_erp_pos_loyaltycard');
        $results = $this->db->get()->row_array();

        return $results;
    }

    function add_points()
    {
        $exchange_rate_amount = $this->input->post('exchange_rate_amount');
        $price_to_point = $this->input->post('price_to_point');
        $point_to_price = $this->input->post('point_to_price');
        $minimum_points = $this->input->post('minimum_points');
        $amount_val = $this->input->post('amount_val');
        $number_of_points = $this->input->post('number_of_points');
        $companyID = current_companyID();

        //$pointsexist = $this->db->query("SELECT pointSetupID FROM srp_erp_loyaltypointsetup WHERE companyID={$companyID} AND loyaltyPoints=$points")->row_array();

        /*  if (!empty($pointsexist)) {
              return array('e', 'Points already exist');
              exit;
          }*/

        $data['loyaltyPoints'] = 1;
        $data['amount'] = $exchange_rate_amount;
        $data['priceToPointsEarned'] = $price_to_point;
        $data['pointsToPriceRedeemed'] = $point_to_price;
        $data['minimumPointstoRedeem'] = $minimum_points;
        $data['currencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyID'] = current_companyID();
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdDateTime'] = format_date_mysql_datetime();
        $data['createdUserName'] = current_user();
        $data['poinforPuchaseAmount'] = $amount_val;
        $data['purchaseRewardPoint'] = $number_of_points;

        $insert_query = $this->db->insert('srp_erp_loyaltypointsetup', $data);

        if ($insert_query) {
            return array('s', 'Point setup Added Successfully');
        } else {
            return array('e', 'Point setup Insert Failed');
        }
    }

    function update_point_active()
    {
        $pointSetupID = $this->input->post('pointSetupID');
        $chkedvalue = $this->input->post('chkedvalue');

        $dataA['isActive'] = 0;

        $this->db->where('companyID', trim(current_companyID()));
        $update_query = $this->db->update('srp_erp_loyaltypointsetup', $dataA);
        if ($update_query) {
            $data['isActive'] = $chkedvalue;

            $this->db->where('pointSetupID', trim($pointSetupID));
            $this->db->update('srp_erp_loyaltypointsetup', $data);
            return array('s', 'Activated Successfully');
        } else {
            return array('e', 'Activation Failed');
        }
    }

    function delete_loyalty_card()
    {
        $cardMasterID = $this->input->post('cardMasterID');

        $data['isActive'] = 0;
        $this->db->where('cardMasterID', trim($cardMasterID));
        $update_query = $this->db->update('srp_erp_pos_loyaltycard', $data);
        if ($update_query) {
            return array('s', 'Deleted Successfully');
        } else {
            return array('e', 'Deletion Failed');
        }
    }

    function get_loyalty_card_details($date_to, $customers, $actvstatus)
    {
        $where_to_date = '';
        if ($date_to != null) {
            $where_to_date = "AND (srp_erp_pos_loyaltytopup.createdDateTime<='$date_to' OR srp_erp_pos_loyaltytopup.createdDateTime is null)";
        }
        $customerFilter = '';
        if (!empty($customers) || $customers != '') {
            $customerFilter .= ' AND `srp_erp_pos_loyaltycard`.`customerID` IN ( ' . $customers . ' )';
        }
        $acvstatus = "";
        if ($actvstatus == 'ALL') {
            $acvstatus = "";
        } else {
            $acvstatus = "AND srp_erp_pos_loyaltycard.isActive='$actvstatus'";
        }


        $companyID = current_companyID();
        $result = $this->db->query("SELECT
	cardMasterID,
	cardTopUpID,
	barcode,
	sum( redeemed_amount ) AS redeemed_amount,
	sum( topUpAmount ) AS topUpAmount,
	customerTelephone,
	customerName,
	isActive 
FROM
	(
	SELECT
		`srp_erp_pos_loyaltytopup`.`cardMasterID`,
		`srp_erp_pos_loyaltycard`.`customerID`,
		`cardTopUpID`,
		`srp_erp_pos_loyaltycard`.`barcode` AS `barcode`,
	IF
		( transationType = 1, points, 0 ) AS redeemed_amount,
	IF
		( transationType = 0, points, 0 ) AS topUpAmount,
		IF(customerType=0,`srp_erp_customermaster`.`customerTelephone`,srp_erp_pos_customermaster.customerTelephone) AS `customerTelephone`,
	IF(customerType=0,`srp_erp_customermaster`.`customerName`,srp_erp_pos_customermaster.customerName) AS `customerName`,
		`srp_erp_pos_loyaltycard`.`isActive` AS `isActive` 
	FROM
		`srp_erp_pos_loyaltycard`
		LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_pos_loyaltycard`.`customerID`
		AND customerType = 0
		LEFT JOIN `srp_erp_pos_customermaster` ON `srp_erp_pos_customermaster`.`posCustomerAutoID` = `srp_erp_pos_loyaltycard`.`customerID` AND customerType = 1
		LEFT JOIN `srp_erp_pos_loyaltytopup` ON ( `srp_erp_pos_loyaltycard`.`cardMasterID` = srp_erp_pos_loyaltytopup.cardMasterID ) 
		LEFT JOIN `srp_erp_pos_menusalesmaster` ON `srp_erp_pos_menusalesmaster`.`menuSalesID` = `srp_erp_pos_loyaltytopup`.`invoiceID`and customerType=1
	  LEFT JOIN `srp_erp_pos_invoice` ON `srp_erp_pos_invoice`.`invoiceID` = `srp_erp_pos_loyaltytopup`.`invoiceID` and customerType=0 
	WHERE
		`srp_erp_pos_loyaltycard`.`companyID` = '$companyID' 
		 AND (`srp_erp_pos_menusalesmaster`.`isVoid` != 1 or `srp_erp_pos_invoice`.`isVoid` != 1)		
		$where_to_date
		$acvstatus
		$customerFilter
	) AS te 
GROUP BY
	customerID")->result_array();


        return $result;
    }


    function pos_loyalty_card_topup_redeem_report($date_from = null, $date_to = null, $customers = null)
    {
        $where_date = '';
        if ($date_to != null && $date_from != null) {
            $where_date = "AND (srp_erp_pos_loyaltytopup.createdDateTime>='$date_from') AND (srp_erp_pos_loyaltytopup.createdDateTime<='$date_to')";
        }

        $customerFilter = '';
        if (!empty($customers) || $customers != '') {
            $customerFilter .= ' AND `srp_erp_pos_loyaltycard`.`customerID` IN ( ' . $customers . ' )';
        }
        $companyID = current_companyID();

        $result = $this->db->query("SELECT
	cardMasterID,
	cardTopUpID,
	barcode,
	redeemed_amount  AS redeemed_amount,
	topUpAmount  AS topUpAmount,
	customerTelephone,
	customerName,
	isActive 
FROM
	(
	SELECT
		`srp_erp_pos_loyaltytopup`.`cardMasterID`,
		`cardTopUpID`,
		`srp_erp_pos_loyaltycard`.`barcode` AS `barcode`,
	IF
		( transationType = 1, points, 0 ) AS redeemed_amount,
	IF
		( transationType = 0, points, 0 ) AS topUpAmount,
IF(customerType=0,`srp_erp_customermaster`.`customerTelephone`,srp_erp_pos_customermaster.customerTelephone) AS `customerTelephone`,
	IF(customerType=0,`srp_erp_customermaster`.`customerName`,srp_erp_pos_customermaster.customerName) AS `customerName`,
		`srp_erp_pos_loyaltycard`.`isActive` AS `isActive` 
	FROM
		`srp_erp_pos_loyaltytopup`
		LEFT JOIN `srp_erp_pos_loyaltycard` ON ( `srp_erp_pos_loyaltycard`.`cardMasterID` = srp_erp_pos_loyaltytopup.cardMasterID ) 
		LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_pos_loyaltycard`.`customerID`
		AND customerType = 0
		LEFT JOIN `srp_erp_pos_customermaster` ON `srp_erp_pos_customermaster`.`posCustomerAutoID` = `srp_erp_pos_loyaltycard`.`customerID` 	 AND customerType = 1
	WHERE
		`srp_erp_pos_loyaltytopup`.`companyID` = '$companyID' 
		$where_date
		$customerFilter
	) AS te 
")->result_array();

        return $result;
    }


    function updateDiscountConfig()
    {
        $capAmount = $this->input->post('capAmount');
        $capPercentage = $this->input->post('capPercentage');
        $processAssignID = $this->input->post('processAssignID');
        if (empty($capAmount)) {
            $capAmount = 0;
        }

        if (empty($capPercentage)) {
            $capPercentage = 0;
        }

        $data['capPercentage'] = $capPercentage;
        $data['capAmount'] = $capAmount;
        $this->db->where('processAssignID', trim($processAssignID));
        $update_query = $this->db->update('srp_erp_pos_auth_processassign', $data);

        if ($update_query) {
            return array('s', 'Successfully Added');
        } else {
            return array('e', 'Failed');
        }
    }

    function assign_capamnt_percentage()
    {
        $processAssignID = $this->input->post('processAssignID');
        $this->db->select('ifnull(capPercentage,0) as capPercentage,ifnull(capAmount,0) as capAmount');
        $this->db->where('processAssignID', trim($processAssignID));
        $this->db->from('srp_erp_pos_auth_processassign');
        $result = $this->db->get()->row_array();

        return $result;
    }

    function update_card_active()
    {
        $cardSetupID = $this->input->post('cardSetupID');
        $chkedvalue = $this->input->post('chkedvalue');
        $data['isActive'] = $chkedvalue;
        $this->db->where('cardMasterID', trim($cardSetupID));
        $res = $this->db->update('srp_erp_pos_loyaltycard', $data);
        if ($res) {
            if ($chkedvalue == 1) {
                return array('s', 'Activated Successfully');
            } else {
                return array('s', 'Deactivated Successfully');
            }
        }
    }

    function save_customers_loyality_card()
    {
        $companyID = current_companyID();
        $selectedItem = $this->input->post('selectedItemsSync[]');
        $data = [];
        $companyCod = current_companyCode();
        foreach ($selectedItem as $vals) {
            $qry2 = $this->db->query("SELECT `serialNo` FROM `srp_erp_pos_loyaltycard` WHERE `companyID` = $companyID ORDER BY `serialNo` DESC LIMIT 1")->row_array();
            $data['serialNo'] = (!empty($qry2) ? $qry2['serialNo'] + 1 : 1);
            $data['barcode'] = (!empty($qry2) ? $companyCod . str_pad($data['serialNo'], 6, '0', STR_PAD_LEFT) : $companyCod . str_pad(1, 6, '0', STR_PAD_LEFT));
            $data['customerID'] = $vals;
            $data['customerType'] = 1;
            $data['companyID'] = $companyID;
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdDateTime'] = format_date_mysql_datetime();
            $data['createdUserName'] = current_user();
            $data['timestamp'] = format_date_mysql_datetime();
            $this->db->insert('srp_erp_pos_loyaltycard', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', 'Loyalty Card Added  Failed!');
            return array('status' => false);
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Loyalty Card Added Successfully !');
            return array('status' => true);
        }
    }

    function generateloyalitycard()
    {
        $companyCod = current_companyCode();
        $customerID = $this->input->post('customerAutoID');
        $companyID = current_companyID();
        $this->db->select('serialNo');
        $this->db->from('srp_erp_pos_loyaltycard');
        $this->db->where('companyID', $companyID);
        $this->db->order_by("serialNo", "desc");
        $this->db->limit(1);
        $qry = $this->db->get()->row_array();
        if (!empty($qry)) {
            $serialNo = $qry['serialNo'] + 1;
        } else {
            $serialNo = 1;
        }
        $data['barcode'] = (!empty($qry) ? $companyCod . str_pad($serialNo, 6, '0', STR_PAD_LEFT) : $companyCod . str_pad(1, 6, '0', STR_PAD_LEFT));;
        $data['serialNo'] = $serialNo;
        $data['customerID'] = $customerID;
        $data['companyID'] = $companyID;
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdDateTime'] = format_date_mysql_datetime();
        $data['createdUserName'] = current_user();
        $data['timestamp'] = format_date_mysql_datetime();
        $insert_query = $this->db->insert('srp_erp_pos_loyaltycard', $data);
        if ($insert_query) {
            return array('s', 'Loyalty Card Added Successfully', $data['barcode']);
        } else {
            return array('e', 'Loyalty Card Insert Failed');
        }
    }

    function save_customers_loyality_card_general()
    {
        $companyID = current_companyID();
        $selectedItem = $this->input->post('selectedItemsSync[]');
        $data = [];
        $companyCod = current_companyCode();
        foreach ($selectedItem as $vals) {
            $qry2 = $this->db->query("SELECT `serialNo` FROM `srp_erp_pos_loyaltycard` WHERE `companyID` = $companyID ORDER BY `serialNo` DESC LIMIT 1")->row_array();
            $data['serialNo'] = (!empty($qry2) ? $qry2['serialNo'] + 1 : 1);
            $data['barcode'] = (!empty($qry2) ? $companyCod . str_pad($data['serialNo'], 6, '0', STR_PAD_LEFT) : $companyCod . str_pad(1, 6, '0', STR_PAD_LEFT));
            $data['customerID'] = $vals;
            $data['customerType'] = 0;
            $data['companyID'] = $companyID;
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdDateTime'] = format_date_mysql_datetime();
            $data['createdUserName'] = current_user();
            $data['timestamp'] = format_date_mysql_datetime();
            $this->db->insert('srp_erp_pos_loyaltycard', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', 'Loyalty Card Added  Failed!');
            return array('status' => false);
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Loyalty Card Added Successfully !');
            return array('status' => true);
        }
    }

    function save_customer_loylity()
    {
        $companyID = current_companyID();
        $customerTelephoneTmp = trim($this->input->post('customerTelephoneTmp') ?? '');
        $customerTelephone = $this->db->query("SELECT customerAutoID FROM srp_erp_customermaster 
                                                    where companyID = $companyID AND customerTelephone LIKE '{$customerTelephoneTmp}'")->row('customerAutoID');

        if (!empty($customerTelephone)) {
            return array('e', 'Customer telephone number already exist!');
            exit();
        }

        $this->db->trans_start();
        $isactive = 1;

        $companyData = get_companyInfo();
        $controlAccount = get_companyControlAccounts('ARA'); /*Account Receivable Control Account*/

        //$liability = fetch_gl_account_desc(trim($this->input->post('receivableAccount') ?? ''));
        $currency_code = $this->common_data['company_data']['company_default_currency'];
        $currency_ID = $this->common_data['company_data']['company_default_currencyID'];
        $country = $this->input->post('customerCountry');
        $data['isActive'] = $isactive;
        $data['customerName'] = trim($this->input->post('customerNameTmp') ?? '');
        $data['customerCountry'] = $country; //$country[0];
        $data['customerTelephone'] = trim($this->input->post('customerTelephoneTmp') ?? '');
        $data['customerEmail'] = trim($this->input->post('customerEmailTmp') ?? '');
        $data['customerAddress1'] = $this->input->post('customerAddressTmp');

        $data['receivableAutoID'] = $controlAccount['GLAutoID']; // $liability['GLAutoID'];
        $data['receivableSystemGLCode'] = $controlAccount['systemAccountCode'];
        $data['receivableGLAccount'] = $controlAccount['GLSecondaryCode'];
        $data['receivableDescription'] = $controlAccount['GLDescription'];
        $data['receivableType'] = $controlAccount['subCategory'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->load->library('sequence');
        $data['customerCurrencyID'] = $currency_ID;
        $data['customerCurrency'] = $currency_code;
        $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal($data['customerCurrency']);
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['customerSystemCode'] = $this->sequence->sequence_generator('CUS');
        $this->db->insert('srp_erp_customermaster', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Customer : ' . $data['customerName'] . ' Save Failed ' . $this->db->_error_message());
        } else {
            $this->session->set_flashdata('s', 'Customer : ' . $data['customerName'] . ' Saved Successfully.');
            $this->db->trans_commit();
            return array('s', 'Customer : ' . $data['customerName'] . ' Saved Successfully.', $last_id);
        }
    }

    function get_loyalty_setup_details()
    {
        $query = $this->db->query("select * from srp_erp_loyaltypointsetup where isActive=1");
        if ($query->num_rows() > 0) {
            $data['status'] = 'success';
            $data['loyalty_setup'] = $query->row_array();
        } else {
            $data['status'] = 'failed';
            $data['loyalty_setup'] = null;
        }
        return $data;
    }

    function taxLedgerRecordsForPosRestaurant($shiftID)
    {

        //if credit sale 0
        $this->db->query("insert into srp_erp_taxledger (
                                            vatTypeID,
                                            taxPercentage,
                                            documentID,
                                            documentMasterAutoID,
                                            taxDetailAutoID,
                                            taxFormulaMasterID,
                                            taxFormulaDetailID,
                                            taxMasterID,
                                            amount,
                                            formula,
                                            taxGlAutoID,
                                            companyID,
                                            createdUserGroup,
                                            createdPCID,
                                            createdUserID,
                                            createdDateTime,
                                            createdUserName,
                                            `timestamp`
                                            )
                                            (SELECT 
                                            srp_erp_pos_menusalestaxes.vatType,
                                            srp_erp_pos_menusalestaxes.taxPercentage as taxPercentage,
                                            'POSR' as documentID,
                                            $shiftID AS documentMasterAutoID,
                                            null as taxDetailAutoID,
                                            null as taxFormulaMasterID,
                                            null as taxFormulaDetailID,
                                            srp_erp_pos_menusalestaxes.taxmasterID as taxMasterID,
                                            SUM(srp_erp_pos_menusalestaxes.taxAmount) as amount,
                                            null as formula,
                                            srp_erp_pos_menusalestaxes.GLCode,
                                            srp_erp_pos_menusalestaxes.companyID as companyID,
                                            srp_erp_pos_menusalesmaster.createdUserGroup AS createdUserGroup,
                                            srp_erp_pos_menusalesmaster.createdPCID AS createdPCID,
                                            srp_erp_pos_menusalesmaster.createdUserID AS createdUserID,
                                            NOW( ) AS createdDateTime,
                                            srp_erp_pos_menusalesmaster.createdUserName AS createdUserName,
                                            CURRENT_TIMESTAMP ( ) AS `timestamp`
                                            FROM srp_erp_pos_menusalestaxes 
                                            join srp_erp_pos_menusalesmaster on srp_erp_pos_menusalesmaster.menuSalesID=srp_erp_pos_menusalestaxes.menuSalesID
                                            join srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID=srp_erp_pos_menusalestaxes.taxmasterID
                                            where srp_erp_taxmaster.taxCategory=2
                                            and srp_erp_pos_menusalesmaster.shiftID=$shiftID
                                            and srp_erp_pos_menusalesmaster.isCreditSales=0
                                            group by srp_erp_pos_menusalestaxes.taxPercentage,srp_erp_pos_menusalestaxes.vatType)");

        //when credit sale = 1
        $this->db->query("insert into srp_erp_taxledger (
                                vatTypeID,
                                taxPercentage,
                                documentID,
                                documentMasterAutoID,
                                taxDetailAutoID,
                                taxFormulaMasterID,
                                taxFormulaDetailID,
                                taxMasterID,
                                amount,
                                formula,
                                taxGlAutoID,
                                companyID,
                                createdUserGroup,
                                createdPCID,
                                createdUserID,
                                createdDateTime,
                                createdUserName,
                                `timestamp`
                                )
                                (SELECT 
                                srp_erp_pos_menusalestaxes.vatType,
                                srp_erp_pos_menusalestaxes.taxPercentage as taxPercentage,
                                'POSR' as documentID,
                                srp_erp_pos_menusalesmaster.menuSalesID,
                                null as taxDetailAutoID,
                                null as taxFormulaMasterID,
                                null as taxFormulaDetailID,
                                srp_erp_pos_menusalestaxes.taxmasterID as taxMasterID,
                                srp_erp_pos_menusalestaxes.taxAmount as amount,
                                null as formula,
                                srp_erp_pos_menusalestaxes.GLCode,
                                srp_erp_pos_menusalestaxes.companyID as companyID,
                                srp_erp_pos_menusalesmaster.createdUserGroup AS createdUserGroup,
                                srp_erp_pos_menusalesmaster.createdPCID AS createdPCID,
                                srp_erp_pos_menusalesmaster.createdUserID AS createdUserID,
                                NOW( ) AS createdDateTime,
                                srp_erp_pos_menusalesmaster.createdUserName AS createdUserName,
                                CURRENT_TIMESTAMP ( ) AS `timestamp`
                                FROM srp_erp_pos_menusalestaxes 
                                join srp_erp_pos_menusalesmaster on srp_erp_pos_menusalesmaster.menuSalesID=srp_erp_pos_menusalestaxes.menuSalesID
                                join srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID=srp_erp_pos_menusalestaxes.taxmasterID
                                where srp_erp_taxmaster.taxCategory=2
                                and srp_erp_pos_menusalesmaster.shiftID=$shiftID
                                and srp_erp_pos_menusalesmaster.isCreditSales=1
                                group by srp_erp_pos_menusalesmaster.documentSystemCode,srp_erp_pos_menusalesmaster.documentMasterAutoID,srp_erp_pos_menusalestaxes.vatType)
        ");
    }

    function sales_details_report_v2_total()
    {
        $company_id = current_companyID();
        $customers = $this->input->post('customers');
        $warehouses = $this->input->post('warehouses');
        $cashiers = $this->input->post('cashiers');
        $fromdate = trim(str_replace('/', '-', $this->input->post('fromdate')));
        $todate = trim(str_replace('/', '-', $this->input->post('todate')));

        $where = '';
        if($customers != ''){
            $where .= " AND customerID IN ( " . $customers . ")";
        }
        if(!empty($warehouses)){
            $where .= " AND wareHouseAutoID IN ( " . $warehouses . ")";
        }
        if(!empty($cashiers)){
            $where .= " AND invoice.createdUserID IN ( " . $cashiers . ")";
        }

        if (isset($fromdate) && !empty($fromdate)) {
            $fromdate = date('Y-m-d H:i:s', strtotime($fromdate));
        } else {
            $fromdate = date('Y-m-d 00:00:00');
        }

        if (!empty($todate)) {
            $todate = date('Y-m-d H:i:s', strtotime($todate));
        } else {
            $todate = date('Y-m-d 23:59:59');
        }
        
        $qry = $this->db->query("SELECT 
                    ROUND(SUM(invoice_det.subTotal), invoice.transactionCurrencyDecimalPlaces) as subTotalTot,
                    SUM(ROUND(invoice.netTotal, invoice.transactionCurrencyDecimalPlaces)) as netTotalTot,
                    SUM(ROUND(invoice.paidAmount, invoice.transactionCurrencyDecimalPlaces)) as paidAmountTot,
                    SUM(ROUND(invoice.balanceAmount, invoice.transactionCurrencyDecimalPlaces)) as balanceAmountTot,
                    SUM(ROUND(invoice.discountAmount, invoice.transactionCurrencyDecimalPlaces)) as discountAmountTot,
                    SUM(ROUND(invoice.generalDiscountAmount, invoice.transactionCurrencyDecimalPlaces)) as generalDiscountAmountTot,
                    SUM(ROUND(invoice.promotiondiscountAmount, invoice.transactionCurrencyDecimalPlaces)) as promotiondiscountAmountTot,
                    SUM(IFNULL( rtn.totalreturn, 0 )) as totalreturncolTot,
                    SUM(invoice.discountAmount) + SUM(invoice.generalDiscountAmount) + SUM(invoice.promotiondiscountAmount) as discountTotalAmount,
                    ROUND(SUM(IFNULL(amount,0)),invoice.transactionCurrencyDecimalPlaces) as vatTotalTot,
                    ROUND(SUM(IFNULL(Otheramount,0)),invoice.transactionCurrencyDecimalPlaces) as OtherTotalTot
                FROM
                    srp_erp_pos_invoice AS invoice
                    LEFT JOIN ( SELECT SUM(netTotal) AS totalreturn, invoiceID FROM srp_erp_pos_salesreturn WHERE companyID = $company_id GROUP BY invoiceID ) rtn ON invoice.invoiceID = rtn.invoiceID
                    LEFT JOIN ( SELECT (sum(qty * price)) AS subTotal, invoiceID FROM srp_erp_pos_invoicedetail WHERE companyID = $company_id GROUP BY invoiceID ) invoice_det ON invoice.invoiceID = invoice_det.invoiceID 
                    LEFT JOIN ( SELECT 
                        IFNULL( sum(amount), 0 ) AS amount,
                        documentMasterAutoID 
                    FROM
                        srp_erp_taxledger tax_led
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = tax_led.taxMasterID 
                    WHERE
                        documentID = 'GPOS' 
                        AND taxCategory = 2 
                        AND tax_led.companyID = $company_id 
                    GROUP BY
                    documentMasterAutoID) AS tax_det ON  tax_det.documentMasterAutoID = invoice.invoiceID
                    LEFT JOIN ( SELECT 
                        IFNULL( sum(amount), 0 ) AS Otheramount,
                        documentMasterAutoID 
                    FROM
                        srp_erp_taxledger tax_led
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = tax_led.taxMasterID 
                    WHERE
                        documentID = 'GPOS' 
                        AND taxCategory = 1 
                        AND tax_led.companyID = $company_id 
                    GROUP BY
                    documentMasterAutoID) AS tax_det_other ON  tax_det_other.documentMasterAutoID = invoice.invoiceID
                WHERE
                    invoice.createdDateTime >= '$fromdate'
                    AND invoice.createdDateTime <= '$todate' 
                    AND invoice.isVoid = 0 
                    AND invoice.companyID = $company_id 
                    $where
        ")->row_array();

       return $qry;
    }

    function sales_details_report_v2_excel()
    {
        $company_id = current_companyID();
        $customers = $this->input->post('customerAutoID');
        $warehouses = $this->input->post('outletID_f');
        $cashiers = $this->input->post('cashier');
        $fromdate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $todate = trim(str_replace('/', '-', $this->input->post('filterTo')));

        $where = '';
        if($customers != ''){
            $where .= " AND customerID IN ( " . $customers . ")";
        }
        if(!empty($warehouses)){
            $warehouses = implode(',', $warehouses);
            $where .= " AND wareHouseAutoID IN ( " . $warehouses . ")";
        }
        if(!empty($cashiers)){
            $cashiers = implode(',', $cashiers);
            $where .= " AND invoice.createdUserID IN ( " . $cashiers . ")";
        }

        if (isset($fromdate) && !empty($fromdate)) {
            $fromdate = date('Y-m-d H:i:s', strtotime($fromdate));
        } else {
            $fromdate = date('Y-m-d 00:00:00');
        }

        if (!empty($todate)) {
            $todate = date('Y-m-d H:i:s', strtotime($todate));
        } else {
            $todate = date('Y-m-d 23:59:59');
        }
        
        $qry = $this->db->query("SELECT
                                        invoice.invoiceID as invoiceID,
                                        invoice.documentSystemCode as documentSystemCode,
                                        invoice.createdDateTime as createdDateTime,
                                        invoice.invoiceCode as invoiceCode,
                                        invoice.wareHouseLocation as wareHouseLocation,
                                        ROUND((invoice_det.subTotal), invoice.transactionCurrencyDecimalPlaces) as subTotal,
                                        ROUND(invoice.netTotal, invoice.transactionCurrencyDecimalPlaces) as netTotal,
                                        ROUND(invoice.paidAmount, invoice.transactionCurrencyDecimalPlaces) as paidAmount,
                                        ROUND(invoice.balanceAmount, invoice.transactionCurrencyDecimalPlaces) as balanceAmount,
                                        ROUND(invoice.discountAmount, invoice.transactionCurrencyDecimalPlaces) as discountAmount,
                                        ROUND(invoice.generalDiscountAmount, invoice.transactionCurrencyDecimalPlaces) as generalDiscountAmount,
                                        ROUND(invoice.promotiondiscountAmount, invoice.transactionCurrencyDecimalPlaces) as promotiondiscountAmount,
                                        IFNULL( srp_erp_customermaster.customerName, 'Cash') AS customernam,
                                        srp_erp_customermaster.customerTelephone as customerTelephone,
                                        IFNULL( rtn.totalreturn, 0 ) AS totalreturn,
                                        invoice.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
                                        IFNULL(ROUND(amount,invoice.transactionCurrencyDecimalPlaces),0) as amount,
                                        IFNULL(ROUND(Otheramount,invoice.transactionCurrencyDecimalPlaces),0) as Otheramount
                                FROM
                                    srp_erp_pos_invoice AS invoice
                                    LEFT JOIN srp_erp_customermaster ON invoice.customerID = srp_erp_customermaster.customerAutoID
                                    LEFT JOIN ( SELECT SUM(netTotal) AS totalreturn, invoiceID FROM srp_erp_pos_salesreturn WHERE companyID = $company_id GROUP BY invoiceID ) rtn ON invoice.invoiceID = rtn.invoiceID 
                                    LEFT JOIN ( SELECT (sum(qty * price)) AS subTotal, invoiceID FROM srp_erp_pos_invoicedetail WHERE companyID = $company_id GROUP BY invoiceID ) invoice_det ON invoice.invoiceID = invoice_det.invoiceID 
                                    LEFT JOIN (
                                                SELECT
                                                    IFNULL( sum(amount), 0 ) AS amount,
                                                    documentMasterAutoID 
                                                FROM
                                                    srp_erp_taxledger tax_led
                                                    LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = tax_led.taxMasterID 
                                                WHERE
                                                    documentID = 'GPOS' 
                                                    AND taxCategory = 2 
                                                    AND tax_led.companyID = $company_id 
                                                GROUP BY
                                                    documentMasterAutoID 
                                                ) AS tax_det ON tax_det.documentMasterAutoID = invoice.invoiceID 
                                    LEFT JOIN (
                                                SELECT
                                                    IFNULL( sum(amount), 0 ) AS Otheramount,
                                                    documentMasterAutoID 
                                                FROM
                                                    srp_erp_taxledger tax_led
                                                    LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = tax_led.taxMasterID 
                                                WHERE
                                                    documentID = 'GPOS' 
                                                    AND taxCategory = 1 
                                                    AND tax_led.companyID = $company_id 
                                                GROUP BY
                                                    documentMasterAutoID 
                                                ) AS tax_det_other ON tax_det_other.documentMasterAutoID = invoice.invoiceID 
                                WHERE
                                    invoice.createdDateTime >= '$fromdate'
                                    AND invoice.createdDateTime <= '$todate' 
                                    AND invoice.isVoid = 0 
                                    AND invoice.companyID = $company_id 
                                    $where
        ")->result_array();

        $details = array();
        if (!empty($qry)) {
            $a = 1;
            $tot_subTotal = 0;
            $tot_discount = 0;
            $tot_vat = 0;
            $tot_netTotal = 0;
            $tot_paidAmount = 0;
            $tot_balanceAmount = 0;
            $tot_totalreturncol = 0;
            $tot_OtherTax =0;
            foreach ($qry as $val) {
                $details[] = array( 
                    'Num' => $a,
                    'date' => $val['createdDateTime'],
                    'billID' => $val['invoiceCode'],
                    'Outlet' => $val['wareHouseLocation'],
                    'Customer' => $val['customernam'],
                    'ContactNo' => $val['customerTelephone'],
                    'GrossTotal' => $val['subTotal'],
                    'TotalDiscount' => ($val['discountAmount'] + $val['generalDiscountAmount'] + $val['promotiondiscountAmount']),
                    'VAT' => ($val['amount']),
                    'OtherTax' => ($val['Otheramount']),
                    'NetTotal' => number_format($val['netTotal'], $val['transactionCurrencyDecimalPlaces']),
                    'paidAmount' => $val['paidAmount'],
                    'Balance' => $val['balanceAmount'],
                    'Return' => $val['totalreturn']
                );
                $tot_subTotal += $val['subTotal'];
                $tot_discount += ($val['discountAmount'] + $val['generalDiscountAmount'] + $val['promotiondiscountAmount']);
                $tot_vat += ($val['amount']);
                $tot_OtherTax += ($val['Otheramount']);
                $tot_netTotal += $val['netTotal'];
                $tot_paidAmount += $val['paidAmount'];
                $tot_balanceAmount += $val['balanceAmount'];
                $tot_totalreturncol += $val['totalreturn'];
                $decimalPlace = $val['transactionCurrencyDecimalPlaces'];
                $a++;
            }

            $totalQry = $this->db->query("SELECT 
                                        ROUND(SUM(invoice_det.subTotal), invoice.transactionCurrencyDecimalPlaces) as subTotalTot,
                                        SUM(ROUND(invoice.netTotal, invoice.transactionCurrencyDecimalPlaces)) as netTotalTot,
                                        SUM(ROUND(invoice.paidAmount, invoice.transactionCurrencyDecimalPlaces)) as paidAmountTot,
                                        SUM(ROUND(invoice.balanceAmount, invoice.transactionCurrencyDecimalPlaces)) as balanceAmountTot,
                                        SUM(ROUND(invoice.discountAmount, invoice.transactionCurrencyDecimalPlaces)) as discountAmountTot,
                                        SUM(ROUND(invoice.generalDiscountAmount, invoice.transactionCurrencyDecimalPlaces)) as generalDiscountAmountTot,
                                        SUM(ROUND(invoice.promotiondiscountAmount, invoice.transactionCurrencyDecimalPlaces)) as promotiondiscountAmountTot,
                                        SUM(IFNULL( rtn.totalreturn, 0 )) as totalreturncolTot,
                                        SUM(invoice.discountAmount) + SUM(invoice.generalDiscountAmount) + SUM(invoice.promotiondiscountAmount) as discountTotalAmount,
                                        ROUND(SUM(IFNULL(amount,0)),invoice.transactionCurrencyDecimalPlaces) as vatTotalTot,
                                        ROUND(SUM(IFNULL(Otheramount,0)),invoice.transactionCurrencyDecimalPlaces) as OtherTotalTot
                                    FROM
                                        srp_erp_pos_invoice AS invoice
                                        LEFT JOIN ( SELECT SUM(netTotal) AS totalreturn, invoiceID FROM srp_erp_pos_salesreturn WHERE companyID = $company_id GROUP BY invoiceID ) rtn ON invoice.invoiceID = rtn.invoiceID 
                                        LEFT JOIN ( SELECT (sum(qty * price)) AS subTotal, invoiceID FROM srp_erp_pos_invoicedetail WHERE companyID = $company_id GROUP BY invoiceID ) invoice_det ON invoice.invoiceID = invoice_det.invoiceID 
                                        LEFT JOIN ( SELECT 
                                                    IFNULL( sum(amount), 0 ) AS amount,
                                                    documentMasterAutoID 
                                                    FROM
                                                        srp_erp_taxledger tax_led
                                                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = tax_led.taxMasterID 
                                                    WHERE
                                                        documentID = 'GPOS' 
                                                        AND taxCategory = 2 
                                                        AND tax_led.companyID = $company_id 
                                                    GROUP BY
                                                    documentMasterAutoID) AS tax_det ON  tax_det.documentMasterAutoID = invoice.invoiceID
                                        LEFT JOIN ( SELECT 
                                                        IFNULL( sum(amount), 0 ) AS Otheramount,
                                                        documentMasterAutoID 
                                                    FROM
                                                        srp_erp_taxledger tax_led
                                                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = tax_led.taxMasterID 
                                                    WHERE
                                                        documentID = 'GPOS' 
                                                        AND taxCategory = 1 
                                                        AND tax_led.companyID = $company_id 
                                                    GROUP BY
                                                    documentMasterAutoID) AS tax_det_other ON  tax_det_other.documentMasterAutoID = invoice.invoiceID
                                    WHERE
                                        invoice.createdDateTime >= '$fromdate'
                                        AND invoice.createdDateTime <= '$todate' 
                                        AND invoice.isVoid = 0 
                                        AND invoice.companyID = $company_id 
                                        $where
            ")->row_array();

            $total_arr = array(
                'Num' => 'Total ',
                'date' => '',
                'billID' => '',
                'Outlet' => '',
                'Customer' => '',
                'ContactNo' => '',
                'GrossTotal' => $totalQry['subTotalTot'],
                'TotalDiscount' => $totalQry['discountTotalAmount'],
                'VatTotal' => $totalQry['vatTotalTot'],
                'OtherTax' => $totalQry['OtherTotalTot'],
                'NetTotal' => $totalQry['netTotalTot'],
                'paidAmount' => $totalQry['paidAmountTot'],
                'Balance' => $totalQry['balanceAmountTot'],
                'Return' => $totalQry['totalreturncolTot']
            );
            array_push($details, $total_arr);
        }
        return $details;
    }

    function sales_refund_details_report_v2_total()
    {
        $customers = $this->input->post('customers');
        $warehouses = $this->input->post('warehouses');
        $cashiers = $this->input->post('cashiers');
        $fromdate = trim(str_replace('/', '-', $this->input->post('fromdate')));
        $todate = trim(str_replace('/', '-', $this->input->post('todate')));

        $where = '';
        if($customers != ''){
            $where .= " AND srp_erp_pos_salesreturn.customerID IN ( " . $customers . ")";
        }
        if(!empty($warehouses)){
            $where .= " AND srp_erp_pos_salesreturn.wareHouseAutoID IN ( " . $warehouses . ")";
        }
        if(!empty($cashiers)){
            $where .= " AND srp_erp_pos_salesreturn.createdUserID IN ( " . $cashiers . ")";
        }

        if (isset($fromdate) && !empty($fromdate)) {
            $fromdate = date('Y-m-d H:i:s', strtotime($fromdate));
        } else {
            $fromdate = date('Y-m-d 00:00:00');
        }

        if (!empty($todate)) {
            $todate = date('Y-m-d H:i:s', strtotime($todate));
        } else {
            $todate = date('Y-m-d 23:59:59');
        }
        
        $qry = $this->db->query("SELECT
                                    srp_erp_pos_salesreturn.salesReturnID,
                                    SUM(ROUND(srp_erp_pos_salesreturn.netTotal, srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces)) as netTotalTot,
                                    SUM(ROUND(CASE
                                        WHEN srp_erp_pos_salesreturn.returnMode = 2 THEN (srp_erp_pos_salesreturn.netTotal)
                                        ELSE 0
                                    END, srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces)) as refundTot,
                                    SUM(ROUND(CASE
                                        WHEN srp_erp_pos_salesreturn.returnMode = 1 THEN (srp_erp_pos_salesreturn.netTotal)
                                        ELSE 0
                                    END, srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces)) as exchangeTot
                                FROM srp_erp_pos_salesreturn                
                                    LEFT JOIN srp_erp_pos_invoice AS invoice ON invoice.invoiceID = srp_erp_pos_salesreturn.invoiceID
                                    LEFT JOIN srp_erp_customermaster ON invoice.customerID = srp_erp_customermaster.customerAutoID
                                    LEFT JOIN ( SELECT SUM( netTotal ) AS totalreturn, invoiceID FROM srp_erp_pos_salesreturn WHERE companyID = '" . current_companyID() . "' GROUP BY invoiceID )  rtn ON invoice.invoiceID=rtn.invoiceID
                                WHERE
                                    srp_erp_pos_salesreturn.createdDateTime >= '$fromdate'
                                    AND srp_erp_pos_salesreturn.createdDateTime <= '$todate' 
                                    AND isVoid=0 AND
                                    srp_erp_pos_salesreturn.companyID = '" . current_companyID() . "'
                                    $where")->row_array();

       return $qry;
    }

    function refund_sales_details_report_v2_excel()
    {
        $customers = $this->input->post('customerAutoID');
        $warehouses = $this->input->post('outletID_f');
        $cashiers = $this->input->post('cashier');
        $fromdate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $todate = trim(str_replace('/', '-', $this->input->post('filterTo')));
        
        $where = '';
        if($customers != ''){
            $where .= " AND srp_erp_pos_salesreturn.customerID IN ( " . $customers . ")";
        }

        if(!empty($warehouses)){
            $warehouses = implode(',', $warehouses);
            $where .= " AND srp_erp_pos_salesreturn.wareHouseAutoID IN ( " . $warehouses . ")";
        }
        if(!empty($cashiers)){
            $cashiers = implode(',', $cashiers);
            $where .= " AND srp_erp_pos_salesreturn.createdUserID IN ( " . $cashiers . ")";
        }

        if (isset($fromdate) && !empty($fromdate)) {
            $fromdate = date('Y-m-d H:i:s', strtotime($fromdate));
        } else {
            $fromdate = date('Y-m-d 00:00:00');
        }

        if (!empty($todate)) {
            $todate = date('Y-m-d H:i:s', strtotime($todate));
        } else {
            $todate = date('Y-m-d 23:59:59');
        }
        
        $qry = $this->db->query("SELECT
                                    srp_erp_pos_salesreturn.salesReturnID as salesReturnID,
                                    srp_erp_pos_salesreturn.documentSystemCode as documentSystemCode,
                                    srp_erp_pos_salesreturn.createdDateTime,
                                    ROUND(srp_erp_pos_salesreturn.netTotal, srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces) as netTotal,
                                    ROUND((CASE
                                        WHEN srp_erp_pos_salesreturn.returnMode = 2 THEN (srp_erp_pos_salesreturn.netTotal)
                                        ELSE 0
                                    END), srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces) as refund,
                                    ROUND((CASE
                                        WHEN srp_erp_pos_salesreturn.returnMode = 1 THEN (srp_erp_pos_salesreturn.netTotal)
                                        ELSE 0
                                    END), srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces) as exchange,
                                    srp_erp_pos_salesreturn.returnMode,
                                    srp_erp_pos_salesreturn.wareHouseLocation,
                                    invoice.invoiceID,
                                    srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces
                                FROM srp_erp_pos_salesreturn                
                                    LEFT JOIN srp_erp_pos_invoice AS invoice ON invoice.invoiceID = srp_erp_pos_salesreturn.invoiceID
                                    LEFT JOIN srp_erp_customermaster ON invoice.customerID = srp_erp_customermaster.customerAutoID
                                    LEFT JOIN ( SELECT SUM( netTotal ) AS totalreturn, invoiceID FROM srp_erp_pos_salesreturn WHERE companyID = '" . current_companyID() . "' GROUP BY invoiceID )  rtn ON invoice.invoiceID=rtn.invoiceID
                                WHERE
                                    srp_erp_pos_salesreturn.createdDateTime >= '$fromdate'
                                    AND srp_erp_pos_salesreturn.createdDateTime <= '$todate' 
                                    AND isVoid=0 AND
                                    srp_erp_pos_salesreturn.companyID = '" . current_companyID() . "'
                                    $where"
        )->result_array();

        $details['records'] = array();
        $a = 1;
        if (!empty($qry)) {
            $tot_refund = 0;
            $tot_exchange = 0;
            $tot_netTotal = 0;
            foreach ($qry as $val) {
                $details['records'][] = array( 
                    'Num' => $a,
                    'date' => $val['createdDateTime'],
                    'DocumentCode' => $val['documentSystemCode'],
                    'Outlet' => $val['wareHouseLocation'],
                    'RefundAmount' => $val['refund'],
                    'ExchangeAmount' => $val['exchange'],
                    'Total' => $val['netTotal'],
                );
                $tot_refund += $val['refund'];
                $tot_exchange += $val['exchange'];
                $tot_netTotal += $val['netTotal'];
                $decimalPlace = $val['transactionCurrencyDecimalPlaces'];
                $a++;
            }

            $total_arr = array(
                'Num' => 'Total ',
                'date' => '',
                'DocumentCode' => '',
                'Outlet' => '',
                'RefundAmount' => $tot_refund,
                'ExchangeAmount' => $tot_exchange,
                'Total' => $tot_netTotal
            );
            array_push($details['records'], $total_arr);
        }
        $details['count'] = $a;
        return $details;
    }

    function total_cash_collected($excel = 0)
    {
        $company_id = current_companyID();
        $customers = $this->input->post('customers');
        if(!empty($customers)) {
            $customers = implode(',', $customers);
        }
        $warehouses = $this->input->post('warehouses');
        $cashiers = $this->input->post('cashiers');
        $fromdate = trim(str_replace('/', '-', $this->input->post('fromdate')));
        $todate = trim(str_replace('/', '-', $this->input->post('todate')));

        if($excel == 1) {
            $warehouses = $this->input->post('outletID_f');
            $cashiers = $this->input->post('cashier');
            $fromdate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
            $todate = trim(str_replace('/', '-', $this->input->post('filterTo')));
            $customers = $this->input->post('customerAutoID');
        }
        
        $where = '';
        $where2 = '';
        if($customers != ''){
            $where .= " AND customerID IN ( " . $customers . ")";
            $where2 .= " AND srp_erp_pos_salesreturn.customerID IN ( " . $customers . ")";
        }

        if(!empty($warehouses)){
            $warehouses = implode(',', $warehouses);
            $where .= " AND wareHouseAutoID IN(" . $warehouses . ")";
            $where2 .= " AND srp_erp_pos_salesreturn.wareHouseAutoID IN(" . $warehouses . ")";
        }
        if(!empty($cashiers)){
            $cashiers = implode(',', $cashiers);
            $where .= "AND invoice.createdUserID IN(" . $cashiers . ") ";
            $where2 .= "AND srp_erp_pos_salesreturn.createdUserID IN(" . $cashiers . ") ";
        }

        if (isset($fromdate) && !empty($fromdate)) {
            $fromdate = date('Y-m-d H:i:s', strtotime($fromdate));
        } else {
            $fromdate = date('Y-m-d 00:00:00');
        }

        if (!empty($todate)) {
            $todate = date('Y-m-d H:i:s', strtotime($todate));
        } else {
            $todate = date('Y-m-d 23:59:59');
        }

        $q = "SELECT
                configMaster.description AS paymentDescription,
                SUM( payments.amount ) AS NetTotal,
                count( payments.invoiceID ) AS countTransaction,
                invoice.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces
            FROM
                srp_erp_pos_invoicepayments AS payments
            LEFT JOIN srp_erp_pos_invoice AS invoice ON payments.invoiceID = invoice.invoiceID
            LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID 
            WHERE
                invoice.companyID = $company_id
                AND invoice.createdDateTime BETWEEN '$fromdate' AND '$todate'
                $where
                AND configMaster.autoID = 1
                AND invoice.isVoid=0
            GROUP BY
                payments.paymentConfigMasterID ";
        $result = $this->db->query($q)->row_array();

        $qry = $this->db->query("SELECT
                                    srp_erp_pos_salesreturn.salesReturnID,
                                    IFNULL(ROUND(SUM(srp_erp_pos_salesreturn.netTotal), srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces), 0) as refundAmount
                                FROM srp_erp_pos_salesreturn                
                                    LEFT JOIN srp_erp_pos_invoice AS invoice ON invoice.invoiceID = srp_erp_pos_salesreturn.invoiceID
                                    LEFT JOIN srp_erp_customermaster ON invoice.customerID = srp_erp_customermaster.customerAutoID
                                    LEFT JOIN ( SELECT SUM( netTotal ) AS totalreturn, invoiceID FROM srp_erp_pos_salesreturn WHERE companyID = $company_id GROUP BY invoiceID )  rtn ON invoice.invoiceID=rtn.invoiceID
                                WHERE
                                    srp_erp_pos_salesreturn.createdDateTime >= '$fromdate'
                                    AND srp_erp_pos_salesreturn.createdDateTime <= '$todate' 
                                    AND isVoid = 0 AND srp_erp_pos_salesreturn.returnMode = 2
                                    AND srp_erp_pos_salesreturn.companyID = $company_id $where2"
            )->row_array();

        return number_format($result['NetTotal'] - $qry['refundAmount'], $result['transactionCurrencyDecimalPlaces'], '.', ',');
    }

    function get_item_master_image(){

          $CI = &get_instance();
          $CI->load->library('s3');

          $itemAutoID = $this->input->post('itemAutoID');
          $itemImage = $this->input->post('itemImage');

              $path = 'uploads/itemMaster/' . $itemImage;
              $companyid = current_companyID();
              
              $itemimageexist = $CI->db->query("SELECT itemImage FROM `srp_erp_itemmaster` where companyID = '{$companyid}'  AND itemAutoID = '{$itemAutoID}' AND itemImage != \"no-image.png\"")->row_array();
              if(!empty($itemimageexist))
              {
                  $img_item = $CI->s3->createPresignedRequest($path, '+1 hour');
                  $generatedHTML = "<center><img class='img-thumbnail-2' alt='No image 1' src='$img_item' style='width:100%;max-height: 350px;'><center>";
              } else {
                  $img_item = $CI->s3->createPresignedRequest('images/item/no-image.png', '+1 hour');
                  $generatedHTML = "<center><img class='img-thumbnail-2' alt='No image 2' src='$img_item' style='width:100%;max-height: 350px;'><center>";
              }
  
          return $generatedHTML;  
    }
    function item_reserved_qty(){

        $type = $this->input->post('type');

        if($type == 'add' || $type == 'plus'){
            $response_add = $this->item_add_reserved_qty();
        }elseif($type == 'delete'){
            $response_add = $this->item_remove_reserved_qty();
        }elseif($type == 'change_batch'){
            $response_add = $this->item_manually_reserved_qty();
        }

        echo json_encode($response_add);
        exit;
    }

    function item_remove_reserved_qty(){

        $barcode = $this->input->post('barcode');
        $requested_qty = $this->input->post('qty');
        $invoice_no = $this->input->post('invoice_no');
        $batch_number = $this->input->post('batch_number');
        $type = $this->input->post('type');
        $wareHouseID = $this->common_data['ware_houseID'];
        $init_batch = array();
        $company_id = current_companyID();

        //check item batch process open
        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy != 1){
            return json_encode(array('status'=>'s'));
        }

        //get reserved details for particular invoice
        $reserved_record = get_pos_reserved_item_record($barcode,$invoice_no,'all');

        foreach($reserved_record as $reserved){

            $batchNumber = $reserved['batchNumber'];
            $reserved_qty = $reserved['reserved_qty'];
            $reserved_id = $reserved['id'];
            $itemMasterID = $reserved['itemMasterID'];

            $itemQuantity = $this->db->where('itemAutoID',$itemMasterID)->get('srp_erp_itemmaster')->row('currentStock');

            if($batchNumber){

                $batch_details = get_pos_itembatch_from_batch_number($batchNumber,$itemMasterID);

                $itemBadgeAdjustedStock = $batch_details['qtr'] + $reserved_qty;
                $itemAdjustedQuantity = $itemQuantity + $reserved_qty;

                //reduce actual batch quntity from batch and the item current quentity
                $batch_calc = $this->reduce_actual_quentities_from_db($itemMasterID,$batchNumber,$itemAdjustedQuantity,$itemBadgeAdjustedStock);

            }

            //remove record
            $data['status'] = 3; // released from deletion
            $res = $this->db->where('id',$reserved_id)->update('srp_erp_inventory_itembatch_reserved',$data);

        }

        return True;

    }

    //Functions for batch processing
    function item_add_reserved_qty(){

        $barcode = $this->input->post('barcode');
        $requested_qty = $this->input->post('qty');
        $invoice_no = $this->input->post('invoice_no');
        $batch_number = $this->input->post('batch_number');
        $type = $this->input->post('type');
        $wareHouseID = $this->common_data['ware_houseID'];
        $init_batch = array();
        $company_id = current_companyID();
        $selection_type = 'auto';

        //check item batch process open
        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy != 1){
            return json_encode(array('status'=>'s'));
        }

        if($type == 'plus'){
            $res = $this->released_existing_reserved_batches($barcode,$invoice_no);
        }

        //get item master details
        $itemDetails = get_inventory_item_master_details($barcode);

        //check badges with the quintity
        $batch_list = get_pos_itembatch_for_expire_order($wareHouseID,$itemDetails['itemAutoID']);

        //check requested quantity exceeds
        $total_num_items = get_pos_itembatch_total_qunatity($wareHouseID,$itemDetails['itemAutoID']);

       

        if($itemDetails['mainCategoryID'] == '1'){

            if($itemBatchPolicy == 1 && count($batch_list) == 0 ){
                $ex = array('status'=>'e','message'=>"Item $barcode does not exist in a batch.");
                echo json_encode($ex); 
                exit;
            }

            if($total_num_items < $requested_qty){
                $ex = array('status'=>'e','message'=>"Item $barcode does not have enough quantity to proceed.");
                echo json_encode($ex); 
                exit;
            }

           
        }else{

            // bypass this for non inventory items
            $ex = array('status'=>'s');
            echo json_encode($ex); 
            exit;
    

        }
        
        
        if(empty($batch_number)){
            $batch_details = $this->pick_batch_for_assign($batch_list,$requested_qty,$barcode,$invoice_no);
        }



        if($itemDetails){
            // no item for this invoice
            // record reserved
            $reserve_list_res = $this->add_item_for_recerved_list($barcode,$requested_qty,$invoice_no,$batch_details,$selection_type);

            // deducted from current quentity
            // if(isset($reserve_list_res['status']) && $reserve_list_res['status'] == 'e'){
            //     $ex = array('status'=>'e','message'=>$reserve_list_res['message']);
            //     echo json_encode($ex); 
            //     exit;
            // }

        }

        $ex = array('status'=>'s');
        echo json_encode($ex); 
        exit;

    }

    function add_item_for_recerved_list($barcode,$qty,$invoice_no,$batch_details,$selection_type='auto'){

        echo '<pre>';

        $data = array();
        $company_id = current_companyID();

        foreach($batch_details as $batch){

            $this->db->trans_start();

            $batch_number = $batch['batchNumber'];
            $batch_qty = $batch['qtr'];
            $batch_reserved_qty = $batch['reserved_qty'];
    
            //get reserved details for particular invoice
            $reserved_record = get_pos_reserved_item_record_for_batch($barcode,$invoice_no,$batch_number);
    
            //get item master details
            $itemDetails = get_inventory_item_master_details($barcode);
    
            $itemAutoId = $itemDetails['itemAutoID'];
            
            $itemCurrentStock = $itemDetails['currentStock'];
            $itemAdjustedStock = $itemDetails['currentStock'] - $qty;

        
    
            $itemBadgeAdjustedStock = $batch_qty - $batch_reserved_qty;
    
            if($reserved_record){
                //batch record exists
                $ex = array('status'=>'e','message'=>"Item already added to the invoice");
                return $ex; 
                
            }else{
    
                //batch record
                $data['document_type'] = 'POS_GENERAL';
                $data['batchNumber'] = $batch_number;
                $data['reserved_qty'] = $batch_reserved_qty;
                $data['batch_current_qty'] = $batch_qty;
                $data['barcode'] = $barcode;
                $data['itemMasterID'] = $itemAutoId;
                $data['companyId'] = $company_id;
                $data['invoice_num'] = $invoice_no;
                $data['selection_type'] = $selection_type;
                $data['status'] = 0;
                $data['reserved_datetime'] = $this->common_data['current_date'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['createdUserID'] = $this->common_data['current_userID'];
    
                $this->db->insert('srp_erp_inventory_itembatch_reserved', $data);
    
                //reduce actual batch quntity from batch and the item current quentity
                $batch_calc = $this->reduce_actual_quentities_from_db($itemAutoId,$batch_number,$itemAdjustedStock,$itemBadgeAdjustedStock);
    
                $last_id = $this->db->insert_id();
    
                $this->db->trans_complete();
    
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    // return array('e', 'Customer : ' . $data['customerName'] . ' Save Failed ' . $this->db->_error_message());
                } else {
                    $this->session->set_flashdata('s', 'Item Added Successfully.');
                    $this->db->trans_commit();
                    // return array('s', 'Customer : ' . $data['customerName'] . ' Saved Successfully.', $last_id);
                }
    
            }

        }
        
       
        
    }

    function reduce_actual_quentities_from_db($itemAutoId,$batch_number,$itemAdjustedStock,$itemBadgeAdjustedStock){

        $item = array();
        $batch = array();
        $wareHouseID = $this->common_data['ware_houseID'];
        $wareHouseQuantity = $this->db->where('itemAutoID',$itemAutoId)->where('wareHouseAutoID',$wareHouseID)->get('srp_erp_warehouseitems')->row('currentStock');;
        $itemQuantity = $this->db->where('itemAutoID',$itemAutoId)->get('srp_erp_itemmaster')->row('currentStock');

        $wareHouseQuantityDiffernce = $itemQuantity - $itemAdjustedStock;
        $wareHouseQuantityAdjusted = $wareHouseQuantity - $wareHouseQuantityDiffernce;


        /*
            when doing confirmation system will reduce quantities from the srp_erp_itemmaster / srp_erp_warehouseitems
        */

        //reduce from item master
        $item['currentStock'] = $itemAdjustedStock;
        //$this->db->where('itemAutoID',$itemAutoId)->update('srp_erp_itemmaster',$item);

        //revise mfq item master
        //$this->db->where('itemAutoID',$itemAutoId)->update('srp_erp_mfq_itemmaster',$item);

        //revise mfq item master
        $item['currentStock'] = $wareHouseQuantityAdjusted;
        //$this->db->where('itemAutoID',$itemAutoId)->where('wareHouseAutoID',$wareHouseID)->update('srp_erp_warehouseitems',$item);

        //reduce from the batch
        $batch['qtr'] = $itemBadgeAdjustedStock;
        $this->db->where('batchNumber',$batch_number)->where('itemMasterID',$itemAutoId)->update('srp_erp_inventory_itembatch',$batch);

    }

    function pick_batch_for_assign($batch_list,$qty,$barcode,$invoice_no){

        $init_batch = array_shift($batch_list);
        $selected_batch = array();
        $requested_qty = $qty;


        while($requested_qty > 0){

            $reduce_qty = $init_batch['qtr'];
            $early_requested_qty = $requested_qty;
            $went_minus = 0;

            $requested_qty = $requested_qty - $reduce_qty;

            if($early_requested_qty <= $init_batch['qtr']){
                $init_batch['reserved_qty'] = $early_requested_qty;
            }else{
                if($requested_qty > 0){
                    $init_batch['reserved_qty'] = $init_batch['qtr'];
                }else{
                    $init_batch['reserved_qty'] = $early_requested_qty;
                    $went_minus = 1;
                }
                
            }

            // if($went_minus == 1){
            //     $requested_qty = abs($requested_qty);
            // }
            

            $selected_batch[] = $init_batch;

            $init_batch = array_shift($batch_list);
                
            

        }
        
        return $selected_batch;

    }


    function released_existing_reserved_batches($barcode,$invoice_no){

        //get reserved details for particular invoice
        $reserved_record = get_pos_reserved_item_record($barcode,$invoice_no,'all');

        foreach($reserved_record as $reserved){
  
              $batchNumber = $reserved['batchNumber'];
              $reserved_qty = $reserved['reserved_qty'];
              $reserved_id = $reserved['id'];
              $itemMasterID = $reserved['itemMasterID'];

              $itemQuantity = $this->db->where('itemAutoID',$itemMasterID)->get('srp_erp_itemmaster')->row('currentStock');

              if($batchNumber){
  
                  $batch_details = get_pos_itembatch_from_batch_number($batchNumber,$itemMasterID);
  
                  $itemBadgeAdjustedStock = $batch_details['qtr'] + $reserved_qty;
                  $itemAdjustedQuantity = $itemQuantity + $reserved_qty;
  
                  //reduce actual batch quntity from batch and the item current quentity
                  $batch_calc = $this->reduce_actual_quentities_from_db($itemMasterID,$batchNumber,$itemAdjustedQuantity,$itemBadgeAdjustedStock);
  
              }
              
              //remove record
              $this->db->where('id', $reserved_id);
              $results = $this->db->delete('srp_erp_inventory_itembatch_reserved');
             
          }

    }

    function item_manually_reserved_qty(){

        $barcode = $this->input->post('barcode');
        $requested_qty = (int)$this->input->post('qty');
        $invoice_no = $this->input->post('invoice_no');
        $selected_batches = $this->input->post('batch_number');
        $type = $this->input->post('type');
        $wareHouseID = $this->common_data['ware_houseID'];
        $init_batch = array();
        $company_id = current_companyID();
        $batch_details_arr = array();
        $filled = 0;
        $actual_qty = $requested_qty;

        //Released existing batches
        $res = $this->released_existing_reserved_batches($barcode,$invoice_no);
        $item_details = get_inventory_item_master_details($barcode);
        
        foreach($selected_batches as $batchNumber){

            if($filled == 0){

                $batch_details = get_pos_itembatch_from_batch_number($batchNumber,$item_details['itemAutoID']);

                if($batch_details){ 

                    $batch_qty = (int)$batch_details['qtr'];
                
                    if($batch_qty >= $requested_qty){
                        $batch_details['reserved_qty'] = $requested_qty;
                        $filled = 1;
                    }else{
                        $requested_qty = $requested_qty - $batch_qty;
                        $batch_details['reserved_qty'] = $batch_qty;
                    }

                }

            }

            $batch_details_arr[] = $batch_details;

        }

       $res = $this->add_item_for_recerved_list($barcode,$actual_qty,$invoice_no,$batch_details_arr,'manual');

       return array('status'=>'s','message'=>'Successfully assign item from the batch');

    }

    function item_warehouse_search(){

        $barcode = $this->input->post('key');

        $result = load_item_from_different_warehouses($barcode);
    
        return $result;
        
    }

    function get_item_added_pricing_for_uom($itemAutoID,$defaultUOM = null){

        $company_id = current_companyID();

        $results = $this->db->query("SELECT um.UnitID,
            um.UnitShortCode,
            um.UnitDes,
            uc.conversion,
            IFNULL(sub1.salesPrice,	IFNULL(NULLIF(sub2.companyLocalWacAmount,0),IFNULL(sub2.companyLocalSellingPrice,0))) AS salesPrice,
	        IFNULL(sub1.rSalesPrice,IFNULL(NULLIF(sub2.companyLocalWacAmount,0),IFNULL(sub2.companyLocalSellingPrice,0))) AS rSalesPrice,
            sub1.uomMasterID
            FROM 
            srp_erp_unitsconversion AS uc
            LEFT JOIN srp_erp_unit_of_measure AS um ON uc.subUnitID = um.UnitID
            INNER JOIN (
                SELECT salesPrice, rSalesPrice, uomMasterID
                FROM srp_erp_item_master_pricing AS imp
                WHERE imp.pricingType = 'Price' AND companyID = '{$company_id}' AND itemMasterID = '{$itemAutoID}' AND isActive = 1
            ) AS sub1 ON uc.subUnitID = sub1.uomMasterID
            LEFT JOIN (
                SELECT companyLocalWacAmount,companyLocalSellingPrice,itemAutoID
                FROM srp_erp_itemmaster AS im 
                WHERE im.itemAutoID = '{$itemAutoID}'
            ) AS sub2 ON sub2.itemAutoID = '{$itemAutoID}'
            WHERE uc.masterUnitID = '{$defaultUOM}' ")->result_array();

        return $results;

    }

    function get_item_default_measure_details($itemAutoID,$defaultUOM = null){
        
        $company_id = current_companyID();

        $results = $this->db->query("SELECT
                    im.defaultUnitOfMeasureID     AS UnitID,
                    um.UnitShortCode,
                    um.UnitDes,
                    1                           AS conversion,
                    im.companyLocalSellingPrice AS salesPrice,
                    im.companyLocalSellingPrice AS rSalesPrice,
                    um.UnitID AS uomMasterID
                FROM srp_erp_itemmaster AS im
                    LEFT JOIN srp_erp_unit_of_measure AS um
                    ON im.defaultUnitOfMeasureID = um.UnitID
                WHERE im.itemAutoID = '{$itemAutoID}' ")->row_array();

        return $results;

    }


    //Sync sales data to 3rd party
    //Calling from cron
    function sync_sales_data($company_id){

        $batch_number = '';
        $counter_id = '';
        $list_of_invoices = array();

        //Pull invoices according to the time
        $invoices = $this->pull_pos_invoices($company_id);

        //loop invoice to get items
        $pro_sales_arr = array();
        foreach($invoices as $invoice){

            $invoiceID = $invoice['invoiceID'];
            $list_of_invoices[] = $invoiceID;

            $pro_sales_arr_temp = array();

            //pull items for the invoice
            $items = load_item_from_invoice($invoiceID,$company_id);
            $itemTaxAmount = 0;
            
            $item_arr = array();
            foreach($items as $item){
                $itemTaxAmount += $item['taxAmount'];

                $temp = array();
                $temp['ItemDesc'] = $item['itemDescription'];
                $temp['ItemAmt'] = $item['price'];
                $temp['ItemDiscoumtAmt'] = $item['discountAmount'];

                $item_arr[] = $temp;
            }
            
            //prosales headers
            $pro_sales_arr_temp['PropertyCode'] = 'CCB1';
            $pro_sales_arr_temp['POSInterfaceCode'] = 'CCB1-PS-21-00000056';
            $pro_sales_arr_temp['ReceiptDate'] = date('d/m/Y',strtotime($invoice['createdDateTime']));
            $pro_sales_arr_temp['ReceiptTime'] = date('H:i:s',strtotime($invoice['createdDateTime']));
            $pro_sales_arr_temp['ReceiptNo'] = $invoice['invoiceCode'];
            $pro_sales_arr_temp['SalesCurrency'] = $invoice['transactionCurrency'];
            $pro_sales_arr_temp['NoOfItems'] = count($items);
            $pro_sales_arr_temp['TotalSalesAmtB4Tax'] = $invoice['netTotal'] - $itemTaxAmount ;
            $pro_sales_arr_temp['TotalSalesAmtAfterTax'] = $invoice['netTotal'] ;
            $pro_sales_arr_temp['SalesTaxRate'] = 0.00;
            $pro_sales_arr_temp['ServiceChargeAmt'] = 0.00;
            $pro_sales_arr_temp['PaymentAmt'] = $invoice['paidAmount'];
            $pro_sales_arr_temp['PaymentCurrency'] = $invoice['transactionCurrency'];
            $pro_sales_arr_temp['PaymentMethod'] = 'Cash';
            $pro_sales_arr_temp['SalesType'] = 'Sales';
            $pro_sales_arr_temp['Items'] = $item_arr;
           
            $pro_sales_arr[] = $pro_sales_arr_temp;

           
        }

        $pro_sales_arr['invoice_list'] = $list_of_invoices;

        return $pro_sales_arr;

    }

    //Pull invoices
    function pull_pos_invoices($company_id){

        $this->db->select("*");
        $this->db->where('i.companyID',$company_id);
        $this->db->where('i.isSync',0);
        $this->db->where('i.wareHouseAutoID',2);
        $this->db->from('srp_erp_pos_invoice as i');
        // $this->db->join('srp_erp_pos_counters as pc','i.counterID = pc.counterID');
        $this->db->limit(10);
        $invoices = $this->db->get()->result_array();

        return $invoices;

    }


    //Add update sync data badge details
    function sync_data_batch_details($data){

        $data['created_date'] = date('Y-m-d H:i:s');
        $data['send_status'] = 1;
        $this->db->insert('srp_erp_pos_salesdata_sync',$data);

    }

    function update_pos_invoice($isSync,$invoice_list,$batchCode = null){
        
        $data = array();

        try{
            
            $data['isSync'] = $isSync;
            if($batchCode){
                $data['syncBatchCode'] = $batchCode;
            }

            $this->db->where_in('invoiceID',$invoice_list);
            $this->db->update('srp_erp_pos_invoice',$data);

            return TRUE;
        
        }catch(Exception $e){

        }
        


    }

    function item_release_cron(){
        print_r('setup2'); exit;
    }

    function make_the_adjusment_batch(){

        $company_id = current_companyID();
        $base_arr = array();

        $this->db->where('companyID',$company_id);
        $this->db->where('processedStatus',0);
        $itemList = $this->db->from('srp_erp_itemmaster')->get()->result_array();

        foreach($itemList as $item){

            $itemCurrentStock = $item['currentStock'];
            $itemAutoID = $item['itemAutoID'];
            $batch_qtr = 0;
            $data_item_update = array();
            $data_log = array();

            $this->db->where('companyID',$company_id);
            $this->db->where('itemMasterID',$itemAutoID);
            $batchList = $this->db->from('srp_erp_inventory_itembatch')->get()->result_array();

            foreach($batchList as $batch){
                $batch_qtr += $batch['qtr'];
            }


            if($itemCurrentStock != $batch_qtr){
                $base_arr[] = array('itemAutoID' => $item['itemAutoID'],'correctQty' => $itemCurrentStock,'batchQtr' => $batch_qtr);

                 //add to table
                $data_log['itemAutoID'] = $itemAutoID;
                $data_log['currentStock'] = $itemCurrentStock;
                $data_log['batchQty'] = $batch_qtr;
                $data_log['adjusment'] = $itemCurrentStock - $batch_qtr;

                $this->db->insert('srp_erp_inventory_batch_adjusment',$data_log);

            }

            $data_item_update['processedStatus'] = 1;

            $this->db->where('itemAutoID',$itemAutoID)->update('srp_erp_itemmaster',$data_item_update);

        }

        return true;
        

    }

    function make_the_adjusment(){

        $company_id = current_companyID();
        $base_arr = array();

        $this->db->where('status',0);
        $itemList = $this->db->from('srp_erp_inventory_batch_adjusment')->get()->result_array();

        foreach($itemList as $item){

            $adjusment_id = $item['id'];
            $adjusment = $item['adjusment'];
            $itemAutoID = $item['itemAutoID'];
            $data_batch_adjusment = array();
            $data_adjusment = array();

            if($adjusment < 0){

                $temp_adjusment = abs($adjusment);

                $this->db->where('companyID',$company_id);
                $this->db->where('itemMasterID',$itemAutoID);
                $this->db->where('qtr >',$temp_adjusment);
                $batch = $this->db->from('srp_erp_inventory_itembatch')->get()->row_array();

            }else{

                $this->db->where('companyID',$company_id);
                $this->db->where('itemMasterID',$itemAutoID);
                $batch = $this->db->from('srp_erp_inventory_itembatch')->get()->row_array();
            }
            

            if($batch){

                $batch_id = $batch['id'];
                $data_batch_adjusment['qtr'] = $batch['qtr'] + $adjusment;
                
                $this->db->where('id',$batch_id)->update('srp_erp_inventory_itembatch',$data_batch_adjusment);


                $data_adjusment['status'] = 1;
                $data_adjusment['comment'] = $batch_id;

            }else{
                $data_adjusment['status'] = 2;
            }


            $this->db->where('id',$adjusment_id)->update('srp_erp_inventory_batch_adjusment',$data_adjusment);


        }

    }

    function make_the_adjusment_step2(){

        $company_id = current_companyID();
        $base_arr = array();

        $this->db->where('status',2);
        $itemList = $this->db->from('srp_erp_inventory_batch_adjusment')->get()->result_array();

        foreach($itemList as $item){

            $adjusment_id = $item['id'];
            $adjusment = $item['adjusment'];
            $itemAutoID = $item['itemAutoID'];
            $data_batch_adjusment = array();
            $data_adjusment = array();

            if($adjusment < 0){

                $temp_adjusment = abs($adjusment);

                $this->db->where('companyID',$company_id);
                $this->db->where('itemMasterID',$itemAutoID);
                $this->db->where('qtr >=',$temp_adjusment);
                $batch = $this->db->from('srp_erp_inventory_itembatch')->get()->row_array();

            }else{

                $this->db->where('companyID',$company_id);
                $this->db->where('itemMasterID',$itemAutoID);
                $batch = $this->db->from('srp_erp_inventory_itembatch')->get()->row_array();
            }
            

            if($batch){

                $batch_id = $batch['id'];
                $data_batch_adjusment['qtr'] = $batch['qtr'] + $adjusment;
                
                $this->db->where('id',$batch_id)->update('srp_erp_inventory_itembatch',$data_batch_adjusment);


                $data_adjusment['status'] = 11;
                $data_adjusment['comment'] = $batch_id;

            }else{
                $data_adjusment['status'] = 2;
            }


            $this->db->where('id',$adjusment_id)->update('srp_erp_inventory_batch_adjusment',$data_adjusment);

        }

    }

    function make_the_adjusment_step3(){

        $company_id = current_companyID();
        $base_arr = array();

        $this->db->where('status',2);
        $itemList = $this->db->from('srp_erp_inventory_batch_adjusment')->get()->result_array();

        foreach($itemList as $item){

            $adjusment_id = $item['id'];
            $adjusment = $item['adjusment'];
            $itemAutoID = $item['itemAutoID'];
            $currentStock = $item['currentStock'];
            $data_batch_adjusment = array();
            $data_adjusment = array();

            if($currentStock == 0){

                $this->db->where('companyID',$company_id);
                $this->db->where('itemMasterID',$itemAutoID);
                $batchList = $this->db->from('srp_erp_inventory_itembatch')->get()->result_array();

                foreach($batchList as $batch){

                    $batch_id = $batch['id'];
                    $data_batch_adjusment['qtr'] = 0;
                
                    $this->db->where('id',$batch_id)->update('srp_erp_inventory_itembatch',$data_batch_adjusment);

                    $data_adjusment['status'] = 11;
                    $data_adjusment['comment'] = $batch_id;
                }

            }else{
                $data_adjusment['status'] = 3;
            }
            

            $this->db->where('id',$adjusment_id)->update('srp_erp_inventory_batch_adjusment',$data_adjusment);

        }

    }

    function get_currency_details($currencyCode){

        $this->db->where('CurrencyCode',$currencyCode);
        $currency = $this->db->from('srp_erp_currencymaster')->get()->row_array();

        return $currency;
        
    }

    function get_wareHouse_details($wareHouseID){

        $this->db->where('wHouse.wareHouseAutoID',$wareHouseID);

        $wareHouse = $this->db->select('wHouse.* , conf.segmentID, conf.segmentCode')->from('srp_erp_warehousemaster as wHouse')->join('srp_erp_pos_segmentconfig conf', 'conf.wareHouseAutoID=wHouse.wareHouseAutoID', 'left')->get()->row_array();

        return $wareHouse;

    }

    function set_processed_invoice_list($company_id){

        $this->db->where('companyID',$company_id);
        $this->db->where('processed',0);
        $this->db->where('invoice_status',1);
        $batchList = $this->db->from('srp_erp_pos_tab_invoice')->get()->result_array();

        $this->load->library('sequence');

        $wareHouseID = 0;
        try {

            foreach($batchList as $invoice){
                $data = array();
    
                $invoiceCode = $invoice['invoiceCode'];
                $inv_tab_id = $invoice['invoiceID'];
    
                //item List
                $this->db->where('companyID',$company_id);
                $this->db->where('invoiceCode',$invoiceCode);
                $this->db->where('processed',0);
                $invoice_item_list = $this->db->from('srp_erp_pos_tab_invoicedetail')->get()->result_array();
    
                $ref_code_arr = $this->getInvoiceCode();
                $refCode = $ref_code_arr['refCode'];//'DEMO/POS000040';//////$this->sequence->sequence_generator('POS', $lastRefNo);
                $refSequence = $ref_code_arr['lastRefNo'];
                //refCode] => DEMO/POS000040
                //[lastRefNo] => 40
    
                $current_year_details = $this->get_current_period();
                $isGroup = getPolicyValues('GBT', 'All');
                $com_code = $this->common_data['company_data']['company_code'];

                //print_r($invoice_item_list); exit;
            
    
                $data['documentSystemCode'] = $refCode;
                $data['documentCode'] = 'POS';
                $data['serialNo'] = $refSequence;
                $data['invoiceSequenceNo'] = $refSequence;
                $data['invoiceCode'] = $invoiceCode;
                $data['financialYearID'] = $current_year_details['year']['companyFinanceYearID'];
                $data['FYBegin'] = $current_year_details['year']['beginingDate'];
                $data['FYEnd'] = $current_year_details['year']['endingDate'];
                $data['financialPeriodID'] = $current_year_details['period']['companyFinancePeriodID'];
                $data['FYPeriodDateFrom'] = $current_year_details['period']['dateFrom'];
                $data['FYPeriodDateTo'] = $current_year_details['period']['dateTo'];
                $data['isGroupBasedTax'] = $isGroup;
                $data['customerID'] = $invoice['customerID'];

                // $invoice['customerID'] = null;
    
                if($invoice['customerID']){
    
                    $customer_details = getCustomerMaster($invoice['customerID']);
                   

                    if($customer_details){
                        $data['customerCode'] = $customer_details['customerSystemCode'];
                        $data['customerReceivableAutoID'] = $customer_details['receivableAutoID'];
                        $data['customerReceivableSystemGLCode'] = $customer_details['receivableSystemGLCode'];
                        $data['customerReceivableGLAccount'] = $customer_details['receivableGLAccount'];
                        $data['customerReceivableDescription'] = $customer_details['receivableDescription'];
                        $data['customerReceivableType'] = $customer_details['receivableType'];

                    }
                   
                }
    
                $data['counterID'] = $invoice['counterID'];
                $data['shiftID'] = $invoice['shiftID'];
                $data['invoiceDate'] = $invoice['invoiceDate'];
                $data['subTotal'] = $invoice['subTotal'];
                $data['discountPer'] = $invoice['discountPer'];
                $data['discountAmount'] = $invoice['discountAmount'];
                $data['generalDiscountPercentage'] = $invoice['generalDiscountPercentage'];
                $data['generalDiscountAmount'] = $invoice['generalDiscountAmount'];
                $data['netTotal'] = $startingBalance = $invoice['netTotal'];
                $data['paidAmount'] = $invoice['paidAmount'];
                $data['balanceAmount'] = $invoice['balanceAmount'];
                $data['cashAmount'] = $invoice['cashAmount'];
                $data['chequeAmount'] = $invoice['chequeAmount'];
                $data['cardAmount'] = $invoice['cardAmount'];
                $data['cardRefNo'] = $invoice['cardRefNo'];
                $data['creditNoteID'] = $invoice['creditNoteID'];
                $data['creditNoteAmount'] = $invoice['creditNoteAmount'];
                $data['giftCardID'] = $invoice['giftCardID'];
                $data['giftCardAmount'] = $invoice['giftCardAmount'];
                $data['isCreditSales'] = $invoice['isCreditSales'];
                $data['creditSalesAmount'] = $invoice['creditSalesAmount'];
                $data['wareHouseAutoID'] = $invoice['wareHouseAutoID'];

                if($invoice['wareHouseAutoID']){
                    $warehouse_details = wareHouseDetails($data['wareHouseAutoID']);
    
                    $data['wareHouseCode'] = $warehouse_details['wareHouseCode'];
                    $data['wareHouseLocation'] = $warehouse_details['wareHouseLocation'];
                    $data['wareHouseDescription'] = $warehouse_details['wareHouseDescription'];
                }
    
                $data['transactionCurrencyID'] = $invoice['transactionCurrencyID'];
                $data['transactionCurrency'] =  $tr_currency = $invoice['transactionCurrency'];
                $data['transactionExchangeRate'] = 1;
                $data['transactionCurrencyDecimalPlaces'] = $invoice['transactionCurrencyDecimalPlaces'];
    
                $com_currency = $this->common_data['company_data']['company_default_currency'];
                $rep_currency = $this->common_data['company_data']['company_reporting_currency'];
    
                $localConversion = currency_conversion($tr_currency, $com_currency, $startingBalance);
                $com_currDPlace = $localConversion['DecimalPlaces'];
                $localConversionRate = $localConversion['conversion'];
    
                $transConversion = currency_conversion($tr_currency, $tr_currency, $startingBalance);
                $tr_currDPlace = $transConversion['DecimalPlaces'];
                $transConversionRate = $transConversion['conversion'];
    
                $reportConversion = currency_conversion($tr_currency, $rep_currency, $startingBalance);
                $rep_currDPlace = $reportConversion['DecimalPlaces'];
                $reportConversionRate = $reportConversion['conversion'];
    
                $data['companyLocalCurrencyID'] = $localConversion['currencyID'];
                $data['companyLocalCurrency'] = $localConversion['CurrencyCode'];
                $data['companyLocalExchangeRate'] = $localConversion['conversion'];
                $data['companyLocalCurrencyDecimalPlaces'] = $localConversion['DecimalPlaces'];
    
                $data['companyReportingCurrencyID'] = $reportConversion['currencyID'];
                $data['companyReportingCurrency'] = $reportConversion['CurrencyCode'];
                $data['companyReportingExchangeRate'] = $reportConversion['conversion'];
                $data['companyReportingCurrencyDecimalPlaces'] = $reportConversion['DecimalPlaces'];

            
                $data['companyID'] = $invoice['companyID'];
                $data['companyCode'] = $invoice['companyCode'];
                $data['createdUserID'] = $invoice['createdUserID'];
                $data['segmentCode'] = 'GEN';
                $data['segmentID'] = 162;
                $data['createdDateTime'] = $this->common_data['current_date'];
    
                $inv = $this->db->insert('srp_erp_pos_invoice',$data);
                $last_id = $data['invID'] = $this->db->insert_id();
                // $last_id = $data['invID']  = 280;
         

                if($data['creditSalesAmount'] != 0){
                    $res = $this->generateCustomerInvoice($data,$last_id,$invoice['wareHouseAutoID']);
                }

                //update payment config details
                $paymentTypes = $this->get_paymentType_arr($data);

                $res_paymentTypes = $this->add_payment_config($paymentTypes,$data);

                
                if($last_id){

                    $item_added_arr = [];
                    $item_added_detail_arr = [];
    
                    foreach($invoice_item_list as $item){
    
                        $item_arr = array();
                        $invoiceDetailsID = $item['invoiceDetailsID'];
    
                        $item_details_master = get_itemmaster_details($item['itemSystemCode']);
                        $item_details_warehouse = get_warehouseitem_details($item_details_master['itemAutoID'],$invoice['wareHouseAutoID']);
                        $item_added_arr[] = $item_details_master['itemAutoID'];
    
                        $item_arr['invoiceID'] = $last_id;
                        $item_arr['itemAutoID'] = $item_details_master['itemAutoID'];
                        $item_arr['itemSystemCode'] = $item_details_master['itemSystemCode'];
                        $item_arr['itemDescription'] = $item_details_master['itemDescription'];
                        $item_arr['itemCategory'] = $item_details_master['mainCategory'];
                        $item_arr['financeCategory'] = $item_details_master['financeCategory'];
                        $item_arr['defaultUOMID'] = $item_details_master['defaultUnitOfMeasureID'];
                        $item_arr['defaultUOM'] = $item_details_master['defaultUnitOfMeasure'];
                        $item_arr['UOMID'] = $item_details_master['defaultUnitOfMeasureID'];
                        $item_arr['unitOfMeasure'] = $item_details_master['defaultUnitOfMeasure'];
                        $item_arr['conversionRateUOM'] = 1;
                        $item_arr['expenseGLAutoID'] = $item_details_master['costGLAutoID'];
                        $item_arr['expenseGLCode'] = $item_details_master['costGLCode'];
                        $item_arr['expenseSystemGLCode'] = $item_details_master['costSystemGLCode'];
                        $item_arr['expenseGLDescription'] = $item_details_master['costDescription'];
                        $item_arr['expenseGLType'] = $item_details_master['costType'];
    
                        $item_arr['revenueGLAutoID'] = $item_details_master['revanueGLAutoID'];
                        $item_arr['revenueGLCode'] = $item_details_master['revanueGLCode'];
                        $item_arr['revenueSystemGLCode'] = $item_details_master['revanueSystemGLCode'];
                        $item_arr['revenueGLDescription'] = $item_details_master['revanueDescription'];
                        $item_arr['revenueGLType'] = $item_details_master['revanueType'];
    
                        $item_arr['assetGLAutoID'] = $item_details_master['assteGLAutoID'];
                        $item_arr['assetGLCode'] = $item_details_master['assteGLCode'];
                        $item_arr['assetSystemGLCode'] = $item_details_master['assteSystemGLCode'];
                        $item_arr['assetGLDescription'] = $item_details_master['assteDescription'];
                        $item_arr['assetGLType'] = $item_details_master['assteType'];
     
                        $item_arr['qty'] = $item['qty'];
                        $item_arr['price'] = $item['price'];

                        if($item['price'] == 0){
                            $item_arr['isFoc'] = 1;
                        }
                        
                        $item_arr['itemConditionType'] = $item['itemConditionType'];
                        $item_arr['discountPer'] = $item['discountPer'];
                        $item_arr['discountAmount'] = $item['discountAmount']/$item['qty'];
                        $item_arr['generalDiscountPercentage'] = $item['generalDiscountPercentage'];
                        $item_arr['generalDiscountAmount'] = $item['generalDiscountAmount'];
    
                        $item_arr['taxCalculationformulaID'] = $item['taxCalculationformulaID'];
                        $item_arr['taxAmount'] = $item['taxAmount'];
                        $item_arr['wacAmount'] = $item['wacAmount'];
    
                        $amount_with_discount = $startingBalanceVal = ((($item['qty'] * $item['price']) + $item['taxAmount']) - $item['discountAmount']);
                        $amount_with = ((($item['qty'] * $item['price']) + $item['taxAmount']));
    
                        $item_arr['transactionCurrencyID'] = $invoice['transactionCurrencyID'];
                        $item_arr['transactionCurrency'] = $invoice['transactionCurrency'];
                        $item_arr['transactionAmountBeforeDiscount'] = $amount_with_discount;
                        $item_arr['transactionAmount'] = $amount_with_discount;
                        $item_arr['transactionExchangeRate'] = 1;
                        $item_arr['transactionCurrencyDecimalPlaces'] = 1;
    
                        $localConversion = currency_conversion($tr_currency, $com_currency, $startingBalanceVal);
                        $com_currDPlace = $localConversion['DecimalPlaces'];
                        $localConversionRate = $localConversion['conversion'];
    
                        $transConversion = currency_conversion($tr_currency, $tr_currency, $startingBalanceVal);
                        $tr_currDPlace = $transConversion['DecimalPlaces'];
                        $transConversionRate = $transConversion['conversion'];
    
                        $reportConversion = currency_conversion($tr_currency, $rep_currency, $startingBalanceVal);
                        $rep_currDPlace = $reportConversion['DecimalPlaces'];
                        $reportConversionRate = $reportConversion['conversion'];
    
                        $item_arr['companyLocalAmount'] = $localConversion['convertedAmount'];
                        $item_arr['companyLocalCurrencyID'] = $localConversion['currencyID'];
                        $item_arr['companyLocalCurrency'] = $localConversion['CurrencyCode'];
                        $item_arr['companyLocalExchangeRate'] = $localConversion['conversion'];
                        $item_arr['companyLocalCurrencyDecimalPlaces'] = $localConversion['DecimalPlaces'];
                        
                        $item_arr['companyReportingAmount'] = $localConversion['convertedAmount'];
                        $item_arr['companyReportingCurrencyID'] = $reportConversion['currencyID'];
                        $item_arr['companyReportingCurrency'] = $reportConversion['CurrencyCode'];
                        $item_arr['companyReportingExchangeRate'] = $reportConversion['conversion'];
                        $item_arr['companyReportingCurrencyDecimalPlaces'] = $reportConversion['DecimalPlaces'];
                        
                        $item_arr['companyID'] = $invoice['companyID'];
                        $item_arr['companyCode'] = $invoice['companyCode'];
                        
                        $inv = $this->db->insert('srp_erp_pos_invoicedetail',$item_arr);
                        $last_detail_id =  $this->db->insert_id();

                        $item_added_detail_arr[$item_arr['itemAutoID']] = $item_arr;

                        //update 
                        $detail_inv = array();
                        $detail_inv['processed'] = 1;
                        $inv = $this->db->where('invoiceDetailsID',$invoiceDetailsID)->update('srp_erp_pos_tab_invoicedetail',$detail_inv);
            
                    }
                }

                //update tax amounts
                $res_tax = $this->update_taxamount($last_id);

                //update item tax ledger
                $res  = $this->item_quantity_update($item_added_arr,$item_added_detail_arr,$invoice['wareHouseAutoID'],$invoiceCode);
                //exit;

                $data_inv = array();
    
                $data_inv['processed'] = 1;
                $data_inv['processed_date'] = $this->common_data['current_date'];
    
                $inv = $this->db->where('invoiceID',$inv_tab_id)->update('srp_erp_pos_tab_invoice',$data_inv);
                
            }

            echo "Successfully Completed";

        } catch (\Throwable $th) {
            throw $th;
        }
        

    }

    function generateCustomerInvoice($data,$invID,$wareHousAutoID){
        
        $customerID = $data['customerID'];
        $CreditSalesAmnt = $netTotalAmount = $data['creditSalesAmount'];
        $data_customer_invoice = array();
        $sequenceCode  = $data['invoiceSequenceNo'];
     
        
        if ($CreditSalesAmnt != 0) {
            $cusData = $this->db->query("SELECT customerAutoID, customerSystemCode, customerName, receivableAutoID,
                                             receivableSystemGLCode, receivableGLAccount, receivableDescription, receivableType,
                                             customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces,customerAddress1,customerTelephone
                                             FROM srp_erp_customermaster WHERE customerAutoID = {$customerID}")->row_array();
            

            $data_customer_invoice['invoiceType'] = 'Direct';
            $data_customer_invoice['documentID'] = 'CINV';
            $data_customer_invoice['posTypeID'] = 1;
            $data_customer_invoice['referenceNo'] = $sequenceCode;
            $creditSaleReference = $data['documentSystemCode'];
            if ($creditSaleReference != '') {
                $data_customer_invoice['referenceNo'] .= ' - ' . $creditSaleReference;
            }
            $data_customer_invoice['invoiceNarration'] = 'POS Credit Sales - ' . $sequenceCode. ' - '.$data['invoiceCode'];
            $data_customer_invoice['posMasterAutoID'] = $sequenceCode;
            $data_customer_invoice['invoiceDate'] = current_date();
            $data_customer_invoice['invoiceDueDate'] = current_date();
            $data_customer_invoice['customerInvoiceDate'] = current_date();
            $data_customer_invoice['invoiceCode'] = $this->sequence->sequence_generator($data_customer_invoice['documentID']);
            $data_customer_invoice['companyFinanceYearID'] = $this->common_data['company_data']['companyFinanceYearID'];
            $financialYear = get_financial_from_to($this->common_data['company_data']['companyFinanceYearID']);
            $data_customer_invoice['companyFinanceYear'] = trim($financialYear['beginingDate'] ?? '') . ' - ' . trim($financialYear['endingDate'] ?? '');
            $data_customer_invoice['FYBegin'] = trim($financialYear['beginingDate'] ?? '');
            $data_customer_invoice['FYEnd'] = trim($financialYear['endingDate'] ?? '');
            $data_customer_invoice['FYPeriodDateFrom'] = trim($this->common_data['company_data']['FYPeriodDateFrom']);
            $data_customer_invoice['FYPeriodDateTo'] = trim($this->common_data['company_data']['FYPeriodDateTo']);
            $data_customer_invoice['companyFinancePeriodID'] = $this->common_data['company_data']['companyFinancePeriodID'];
            $data_customer_invoice['customerID'] = $customerID;
            $data_customer_invoice['customerSystemCode'] = $cusData['customerSystemCode'];
            $data_customer_invoice['customerName'] = $cusData['customerName'];
            $data_customer_invoice['customerAddress'] = $cusData['customerAddress1'];
            $data_customer_invoice['customerTelephone'] = $cusData['customerTelephone'];
            $data_customer_invoice['customerFax'] = $cusData['customerTelephone'];
            $data_customer_invoice['customerEmail'] = $cusData['customerTelephone'];
            $data_customer_invoice['customerReceivableAutoID'] = $cusData['receivableAutoID'];
            $data_customer_invoice['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
            $data_customer_invoice['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
            $data_customer_invoice['customerReceivableDescription'] = $cusData['receivableDescription'];
            $data_customer_invoice['customerReceivableType'] = $cusData['receivableType'];
            $data_customer_invoice['customerCurrency'] = $cusData['customerCurrency'];
            $data_customer_invoice['customerCurrencyID'] = $cusData['customerCurrencyID'];
            $data_customer_invoice['customerCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];

            $data_customer_invoice['confirmedYN'] = 1;
            $data_customer_invoice['confirmedByEmpID'] = current_userID();
            $data_customer_invoice['confirmedByName'] = current_user();
            $data_customer_invoice['confirmedDate'] = current_date();
            $data_customer_invoice['approvedYN'] = 1;
            $data_customer_invoice['approvedDate'] = current_date();
            $data_customer_invoice['currentLevelNo'] = 1;
            $data_customer_invoice['approvedbyEmpID'] = current_userID();
            $data_customer_invoice['approvedbyEmpName'] = current_user();

            $data_customer_invoice['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data_customer_invoice['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data_customer_invoice['transactionExchangeRate'] = 1;
            $data_customer_invoice['transactionAmount'] = $netTotalAmount;
            $data_customer_invoice['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_customer_invoice['transactionCurrencyID']);
            $data_customer_invoice['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data_customer_invoice['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $default_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyLocalCurrencyID']);
            $data_customer_invoice['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data_customer_invoice['companyLocalAmount'] = $data_customer_invoice['transactionAmount'] / $data_customer_invoice['companyLocalExchangeRate'];
            $data_customer_invoice['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data_customer_invoice['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $data_customer_invoice['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyReportingCurrencyID']);
            $data_customer_invoice['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data_customer_invoice['companyReportingAmount'] = $data_customer_invoice['transactionAmount'] / $data_customer_invoice['companyReportingExchangeRate'];
            $data_customer_invoice['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $customer_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['customerCurrencyID']);
            $data_customer_invoice['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
            $data_customer_invoice['customerCurrencyAmount'] = $data_customer_invoice['transactionAmount'] / $data_customer_invoice['customerCurrencyExchangeRate'];
            $data_customer_invoice['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
            $data_customer_invoice['companyCode'] = $this->common_data['company_data']['company_code'];
            $data_customer_invoice['companyID'] = $this->common_data['company_data']['company_id'];
            $data_customer_invoice['createdUserGroup'] = $this->common_data['user_group'];
            $data_customer_invoice['createdPCID'] = $this->common_data['current_pc'];
            $data_customer_invoice['createdUserID'] = $this->common_data['current_userID'];
            $data_customer_invoice['createdUserName'] = $this->common_data['current_user'];
            $data_customer_invoice['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_customerinvoicemaster', $data_customer_invoice);
            $customerInvoiceMasterID = $this->db->insert_id();

            $this->db->select('documentID,invoiceCode,DATE_FORMAT(invoiceDate, "%Y") as invYear,DATE_FORMAT(invoiceDate, "%m") as invMonth,companyFinanceYearID,invoiceType');
            $this->db->where('invoiceAutoID', $customerInvoiceMasterID);
            $this->db->from('srp_erp_customerinvoicemaster');
            $master_dt = $this->db->get()->row_array();

            $data_customer_invoice['invoiceCode'] = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
            $invcod = array(
                'invoiceCode' => $data_customer_invoice['invoiceCode'],
            );
            $this->db->where('invoiceAutoID', $customerInvoiceMasterID);
            $this->db->update('srp_erp_customerinvoicemaster', $invcod);
            $customerInvoiceCode = $data_customer_invoice['invoiceCode'];

            $doc_approved = array();

            if ($customerInvoiceMasterID) {

                $data_cusinv['documentSystemCode'] = $data_customer_invoice['invoiceCode'];
                $this->db->where('invoiceID', $invID);
                $this->db->update('srp_erp_pos_invoice', $data_cusinv);

                $doc_approved['departmentID'] = "CINV";
                $doc_approved['documentID'] = "CINV";
                $doc_approved['documentCode'] = $data_customer_invoice['invoiceCode'];
                $doc_approved['documentSystemCode'] = $customerInvoiceMasterID;
                $doc_approved['documentDate'] = current_date();
                $doc_approved['approvalLevelID'] = 1;
                $doc_approved['docConfirmedDate'] = current_date();
                $doc_approved['docConfirmedByEmpID'] = current_userID();
                $doc_approved['table_name'] = 'srp_erp_customerinvoicemaster';
                $doc_approved['table_unique_field_name'] = 'invoiceAutoID';
                $doc_approved['approvedEmpID'] = current_userID();
                $doc_approved['approvedYN'] = 1;
                $doc_approved['approvedComments'] = 'Approved from POS Tab';
                $doc_approved['approvedPC'] = current_pc();
                $doc_approved['approvedDate'] = current_date();
                $doc_approved['companyID'] = current_companyID();
                $doc_approved['companyCode'] = current_company_code();
                $this->db->insert('srp_erp_documentapproved', $doc_approved);

                $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,segmentID,segmentCode,transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces');
                $this->db->from('srp_erp_customerinvoicemaster');
                $this->db->where('invoiceAutoID', $customerInvoiceMasterID);
                $master = $this->db->get()->row_array();

                $data_customer_invoice_detail['invoiceAutoID'] = $customerInvoiceMasterID;
                $data_customer_invoice_detail['type'] = 'GL';
                $data_customer_invoice_detail['description'] = 'POS Credit Sales - ' . $sequenceCode. ' - '.$data['invoiceCode'];

                $data_customer_invoice_detail['transactionAmount'] = round($netTotalAmount, $master['transactionCurrencyDecimalPlaces']);
                $companyLocalAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyLocalExchangeRate'];
                $data_customer_invoice_detail['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $companyReportingAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyReportingExchangeRate'];
                $data_customer_invoice_detail['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $customerAmount = $data_customer_invoice_detail['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                $data_customer_invoice_detail['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                $data_customer_invoice_detail['companyCode'] = $this->common_data['company_data']['company_code'];
                $data_customer_invoice_detail['companyID'] = $this->common_data['company_data']['company_id'];
                $data_customer_invoice_detail['createdUserGroup'] = $this->common_data['user_group'];
                $data_customer_invoice_detail['createdPCID'] = $this->common_data['current_pc'];
                $data_customer_invoice_detail['createdUserID'] = $this->common_data['current_userID'];
                $data_customer_invoice_detail['createdUserName'] = $this->common_data['current_user'];
                $data_customer_invoice_detail['createdDateTime'] = $this->common_data['current_date'];

                $res = $this->db->insert('srp_erp_customerinvoicedetails', $data_customer_invoice_detail);
            }

            $partyData = array(
                'cusID' => $cusData['customerAutoID'],
                'sysCode' => $cusData['customerSystemCode'],
                'cusName' => $cusData['customerName'],
                'partyCurID' => $cusData['customerCurrencyID'],
                'partyCurrency' => $cusData['customerCurrency'],
                'partyDPlaces' => $cusData['customerCurrencyDecimalPlaces'],
                'partyER' => 1,
                'partyGL' => $cusData,
            );

            $double_entry = $this->double_entry($invID, $partyData, $CreditSalesAmnt, $customerInvoiceMasterID,$wareHousAutoID);

            return True;

        }

    }

    function add_payment_config($paymentTypes,$data){

        $wareHouseID = $data['wareHouseAutoID'];
        $customerID = $data['customerID'];
        $refSystemCode = $data['documentSystemCode'];
        $invID = $data['invID'];

        foreach ($paymentTypes as $paymentType) {

            $key = $paymentType['type'];
            $amount = $paymentType['amount'];
            
            $paymentglconfigmaster = $this->db->query("SELECT * FROM srp_erp_pos_paymentglconfigmaster WHERE autoID={$key}")->row_array();
            $paymentglconfigdetail = $this->db->query("SELECT * FROM srp_erp_pos_paymentglconfigdetail WHERE paymentConfigMasterID={$key} AND warehouseID={$wareHouseID}")->row_array();
            
            if ($key == 7 || $key == 26) {
                $cusid = $customerID;
            } else {
                $cusid = 0;
            }

            $invPaymentARR = array(
                'invoiceID' => $invID,
                'paymentConfigMasterID' => $key,
                'paymentConfigDetailID' => $paymentglconfigdetail['ID'],
                'glAccountType' => $paymentglconfigmaster['glAccountType'],
                'GLCode' => $paymentglconfigdetail['GLCode'],
                'amount' => $amount,
                'reference' => $refSystemCode,
                'customerAutoID' => $cusid,
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => 'System',
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date(),
            );

            $this->db->insert('srp_erp_pos_invoicepayments', $invPaymentARR);
                
        }

    }

    function get_paymentType_arr($data){

        $payment_arr = array();

        if($data['cashAmount'] > 0){
            $payment_arr[] = array('type'=>1,'amount'=> $data['cashAmount']);
        }
        if($data['cardAmount'] > 0 && ($data['cardRefNo'] == 'visa' || $data['cardRefNo'] == 'Visa')){
            $payment_arr[] = array('type'=> 55,'amount'=> $data['cardAmount']);
        }
        if($data['cardAmount'] > 0 && ($data['cardRefNo'] == 'master' || $data['cardRefNo'] == 'Master')){
            $payment_arr[] = array('type'=> 3,'amount'=> $data['cardAmount']);
        }
        if($data['cardAmount'] > 0 && ($data['cardRefNo'] == 'amex' || $data['cardRefNo'] == 'Amex')){
            $payment_arr[] = array('type'=>6 ,'amount'=> $data['cardAmount']);
        }
        if($data['creditSalesAmount'] > 0){
            $payment_arr[] = array('type'=>7,'amount'=> $data['creditSalesAmount']);
        }
        if($data['creditNoteAmount'] > 0){
            $payment_arr[] = array('type'=>2,'amount'=> $data['creditNoteAmount']);
        }

        return $payment_arr;
    }

    function update_taxamount($invID){

        $isGroupBasedPolicyYN = getPolicyValues('GBT', 'All');

   

        if ($isGroupBasedPolicyYN == 1) {
            $companyID = current_companyID();
            $posInvoiceTax = $this->db->query("SELECT
	                                           taxCalculationFormulaID,
	                                           invoiceID,
	                                           price as totaltaxApplicableAmt,
                                               invoiceDetailsID,
		                                       discountAmount as discountamt,
                                               invoiceDetailsID,
                                               taxAmount,
                                               qty,
                                               IFNULL(discountAmount,0)
                                               transactionCurrencyDecimalPlaces
                                               FROM
	                                           srp_erp_pos_invoicedetail 
                                               WHERE
                                                companyID  = {$companyID} 
                                                AND invoiceID = {$invID}")->result_array();
         

            if (!empty($posInvoiceTax)) {
                foreach ($posInvoiceTax as $taxVal) {
                    
                    $taxCalculationFormulaID = 12;//$taxVal['taxCalculationFormulaID'];

                    $query=  $this->db->query("
                            SELECT a.Description as itemName,b.formulaDetailID,b.formula,b.taxMasterAutoID,c.supplierGLAutoID,a.taxType as itemTaxType,a.taxCalculationFormulaID as itemSystemCode,ROUND(SUM(b.taxPercentage),2) as itemTaxPercentage
                            FROM srp_erp_taxcalculationformulamaster a
                            INNER JOIN srp_erp_taxcalculationformuladetails as b ON a.taxCalculationFormulaID = b.taxCalculationFormulaID
                            LEFT JOIN srp_erp_taxmaster as c ON b.taxMasterAutoID = c.taxMasterAutoID
                            WHERE a.companyID = '{$companyID}' AND a.taxCalculationFormulaID = '{$taxCalculationFormulaID }'
                            GROUP BY a.taxCalculationFormulaID 
                        ");
        
                    $taxdetails = $query->row_array();
                    $total_price = (($taxVal['totaltaxApplicableAmt'] * $taxVal['qty']) - $taxVal['discountAmount']);

                    if($taxdetails){
                        $tax_amount = round(((($total_price * $taxdetails['itemTaxPercentage'])/100)),2);
                    }else{
                        $tax_amount = 0;
                    }


                    $data_tax = array();
                    $data_tax['documentID'] = 'GPOS';
                    $data_tax['documentMasterAutoID'] = $taxVal['invoiceID'];
                    $data_tax['documentDetailAutoID'] = $taxVal['invoiceDetailsID'];
                    $data_tax['taxPercentage'] = $taxdetails['itemTaxPercentage'];
                    $data_tax['taxFormulaMasterID'] = $taxCalculationFormulaID;
                    $data_tax['taxFormulaDetailID'] = $taxdetails['formulaDetailID'];
                    $data_tax['taxMasterID'] = $taxdetails['taxMasterAutoID'];
                    $data_tax['amount'] = $taxVal['taxAmount'];
                    $data_tax['taxGlAutoID'] = $taxdetails['supplierGLAutoID'];
                    $data_tax['formula'] = $taxdetails['formula'];
                    $data_tax['isClaimable'] = 1;
                    
                    $data_tax['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data_tax['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_tax['createdUserGroup'] = $this->common_data['user_group'];
                    $data_tax['createdPCID'] = $this->common_data['current_pc'];
                    $data_tax['createdUserID'] = $this->common_data['current_userID'];
                    $data_tax['createdUserName'] = $this->common_data['current_user'];
                    $data_tax['createdDateTime'] = $this->common_data['current_date'];

                    // 
                    $res = $this->db->insert('srp_erp_taxledger',$data_tax);

                }
            }

            return TRUE;
        }
    }

    function item_quantity_update($item,$itemDetail,$wareHouseGET = null,$invoiceCode = null){

        $this->load->library('Wac');
        $this->load->library('sequence');

        

        $i = 0;
        $item_ledger_arr = array();
        $wareHouseID = $wareHouseGET;
        $dataInt = array();
        $itemList = fetch_warehouse_items_data($item,$wareHouseID);
        $companyID = $this->common_data['company_data']['company_id'];
       
  
        $exceedglid = $this->db->query("SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE companyID =$companyID AND controlAccountType = 'IEXC'")->row_array();
        $exceedGlAutoID = $exceedglid['GLAutoID'];
        $exceedGlDesc = fetch_gl_account_desc($exceedGlAutoID);
        $warehousePromotion = $this->db->query("select * from srp_erp_pos_promotionwarehouses where companyID=$companyID and wareHouseID=$wareHouseID and isActive=1");
        $isMinusAllowed = getPolicyValues('MQT', 'All');

        $dataInt = array();

        foreach ($itemList as $i => $itemData) {

            $itemMainCategory = $itemData['mainCategoryID'];
            $itemID = $itemData['itemAutoID'];
            $item_detail = $itemDetail[$itemID];

            $conversion = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
            $conversion = ($conversion == 0) ? 1 : $conversion;
            $conversionRate = 1 / $conversion;
            $availableQTY = $itemData['wareHouseQty'];
            $qty = $item_detail['qty'] * $conversionRate;

            if ($availableQTY < $qty && $isStockCheck == 1) {
                $this->db->trans_rollback();
                return array('e', '[ ' . $itemData['itemSystemCode'] . ' - ' . $itemData['itemDescription'] . ' ]<p> is available only ' . $availableQTY . ' qty');
                break;
            }

            $tr_currency = $item_detail['transactionCurrency'];

            $price = $item_detail['price'];
            $itemTotal = $qty * $price;
            $itemDiscount = $item_detail['discountAmount'];
            $itemTotal = ($itemDiscount > 0) ? ($itemTotal - $itemDiscount) : $itemTotal;

            $transConversion = currency_conversion($tr_currency, $tr_currency, $itemTotal);
            $tr_currDPlace = $transConversion['DecimalPlaces'];
            $transConversionRate = $transConversion['conversion'];
            $itemTotal = round($itemTotal, $tr_currDPlace);
    
            $wareHouseData = $this->get_wareHouse_details($wareHouseGET);

            if(empty($wareHouseData)){
                print_r('exit-'.$wareHouseID); exit;
                return True;
            }

            $dataInt[$i]['invoiceID'] = $item_detail['invoiceID'];
            $dataInt[$i]['itemAutoID'] = $item_detail['itemAutoID'];
            $dataInt[$i]['itemSystemCode'] = $item_detail['itemSystemCode'];
            $dataInt[$i]['itemDescription'] = $item_detail['itemDescription'];
            $dataInt[$i]['defaultUOMID'] = $item_detail['defaultUnitOfMeasureID'];
            $dataInt[$i]['defaultUOM'] = $item_detail['defaultUnitOfMeasure'];
            $dataInt[$i]['unitOfMeasure'] = $item_detail['unitOfMeasure'];
            $dataInt[$i]['UOMID'] = $item_detail['defaultUOMID'];
            $dataInt[$i]['conversionRateUOM'] = $conversion;
            $dataInt[$i]['qty'] = $qty;
            $dataInt[$i]['price'] = $price;
            $dataInt[$i]['discountPer'] = $item_detail['discountPer'];
            $dataInt[$i]['discountAmount'] = $discountAmount;

            $dataInt[$i]['generalDiscountPercentage'] = 0;
            $dataInt[$i]['generalDiscountAmount'] = 0;
            $dataInt[$i]['wacAmount'] = $item_detail['wacAmount'];

            // $dataInt[$i]['itemFinanceCategory'] = $itemData['subcategoryID'];
            // $dataInt[$i]['itemFinanceCategorySub'] = $itemData['subSubCategoryID'];
            // $dataInt[$i]['financeCategory'] = $itemData['financeCategory'];
            // $dataInt[$i]['itemCategory'] = $itemData['mainCategory'];

            // print_r($dataInt[$i]); exit;
 

          

            $dataInt[$i]['expenseGLAutoID'] = $itemData['costGLAutoID'];
            $dataInt[$i]['expenseGLCode'] = $itemData['costGLCode'];
            $dataInt[$i]['expenseSystemGLCode'] = $itemData['costSystemGLCode'];
            $dataInt[$i]['expenseGLDescription'] = $itemData['costDescription'];
            $dataInt[$i]['expenseGLType'] = $itemData['costType'];

            $dataInt[$i]['revenueGLAutoID'] = $itemData['revanueGLAutoID'];
            $dataInt[$i]['revenueGLCode'] = $itemData['revanueGLCode'];
            $dataInt[$i]['revenueSystemGLCode'] = $itemData['revanueSystemGLCode'];
            $dataInt[$i]['revenueGLDescription'] = $itemData['revanueDescription'];
            $dataInt[$i]['revenueGLType'] = $itemData['revanueType'];

            $dataInt[$i]['assetGLAutoID'] = $itemData['assteGLAutoID'];
            $dataInt[$i]['assetGLCode'] = $itemData['assteGLCode'];
            $dataInt[$i]['assetSystemGLCode'] = $itemData['assteSystemGLCode'];
            $dataInt[$i]['assetGLDescription'] = $itemData['assteDescription'];
            $dataInt[$i]['assetGLType'] = $itemData['assteType'];


            $dataInt[$i]['transactionAmountBeforeDiscount'] = $itemTotal;
            $itemTotal = $itemTotal - $gen_discountAmount;
            $promotiondiscountAmount = 0;
            if (isset($promotionDiscount)) {
                $promotiondiscountAmount = $itemTotal * ($promotionDiscount / 100);
                $dataInt[$i]['promotiondiscount'] = $promotionDiscount;
                $dataInt[$i]['promotiondiscountAmount'] = $promotiondiscountAmount;
            } else {
                $dataInt[$i]['promotiondiscount'] = 0;
                $dataInt[$i]['promotiondiscountAmount'] = 0;
            }
            $dataInt[$i]['transactionAmount'] = $itemTotal - $promotiondiscountAmount;
            $dataInt[$i]['transactionExchangeRate'] = $transConversionRate;
            $dataInt[$i]['transactionCurrency'] = $tr_currency;
            $dataInt[$i]['transactionCurrencyID'] = $localConversion['trCurrencyID'];
            $dataInt[$i]['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;
            $dataInt[$i]['companyLocalAmount'] = round(($itemTotal / $localConversionRate), $com_currDPlace);

            $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
            $dataInt[$i]['companyLocalCurrency'] = $com_currency;
            $dataInt[$i]['companyLocalCurrencyID'] = $localConversion['currencyID'];
            $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

            $dataInt[$i]['companyReportingAmount'] = round(($itemTotal / $reportConversionRate), $rep_currDPlace);
            $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
            $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
            $dataInt[$i]['companyReportingCurrencyID'] = $reportConversion['currencyID'];
            $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;
            $dataInt[$i]['taxCalculationformulaID'] = $taxFormula[$i];
            $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
            $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
            $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
            $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $dataInt[$i]['createdDateTime'] = current_date();


            if ($warehousePromotion->num_rows() > 0) {
                $dataInt[$i]['promoID'] = $warehousePromotion->row()->promotionID;
            }

            //this section previously commented
            //            $balanceQty = $availableQTY - $qty;
            //            $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
            //            $itemUpdateQty = array('currentStock' => $balanceQty);
            //            $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);
            //=========================================

            $exceedYN = true;

            if ($isMinusAllowed == ' ' || empty($isMinusAllowed) || $isMinusAllowed == null) {
                $isMinusAllowed = 0;
            }

            $exccededVar = 0;
            if ($isMinusAllowed != 1 && $exceedYN && ($itemData['mainCategory'] != 'Service' && $itemData['mainCategory'] != 'Non Inventory')) {
                //check if item exceeded
                $wareHouseID = $this->common_data['ware_houseID'];
                $excceditems = $this->db->query("SELECT
                    t1.itemAutoID,
                    t1.itemSystemCode,
                    t1.itemDescription,
                    ROUND(IFNULL(SUM(t1.transactionQTY/t1.convertionRate),0),5) AS currentStock
                FROM
                    srp_erp_itemledger t1
                JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                WHERE
                    t2.companyID = '{$companyID}'
                AND wareHouseAutoID = '{$wareHouseID}'
                AND t1.itemAutoID = '{$itemID}'
                AND isActive = 1
                GROUP BY
                    t1.itemAutoID")->row_array();

                $exceededQty = $excceditems['currentStock'] - $itemQty[$i];
                if ($excceditems['currentStock'] < 0) {
                    $exceededQty = $itemQty[$i] * -1;
                }


                //var_dump($exceededQty);exit;
                if ($exceededQty < 0) {

                    $exccededVar = 1;

                    if ($CreditSalesAmnt != 0) {
                        $exceedtbl['documentCode'] = 'CINV';
                        $exceedtbl['documentAutoID'] = $customerInvoiceMasterID;
                        $exceedtbl['documentSystemCode'] = $data_customer_invoice['invoiceCode'];
                    } else {
                        $exceedtbl['documentCode'] = 'POS';
                        $exceedtbl['documentAutoID'] = $invID;
                        $exceedtbl['documentSystemCode'] = $refCode;
                    }
                    $exceedtbl['itemAutoID'] = $itemID;
                    $exceedtbl['warehouseAutoID'] = $wareHouseID;
                    $exceedtbl['exceededGLAutoID'] = $exceedGlAutoID;
                    $exceedtbl['assetGLAutoID'] = $itemData['assteGLAutoID'];
                    $exceedtbl['costGLAutoID'] = $itemData['costGLAutoID'];
                    $exceedtbl['exceededQty'] = abs($exceededQty);
                    $exceedtbl['updatedQty'] = 0;
                    $exceedtbl['balanceQty'] = abs($exceededQty);
                    $exceedtbl['isFromCreditSales'] = $this->input->post('isCreditSale');
                    $exceedtbl['unitCost'] = $itemData['companyLocalWacAmount'];
                    $exceedtbl['transactionAmount'] = $itemData['companyLocalWacAmount'] * abs($exceededQty);
                    $exceedtbl['companyLocalAmount'] = $exceedtbl['transactionAmount'] / $localConversionRate;
                    $exceedtbl['companyReportingAmount'] = $exceedtbl['transactionAmount'] / $reportConversionRate;
                    $exceedtbl['defaultUOMID'] = $itemData['defaultUnitOfMeasureID'];
                    $exceedtbl['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
                    $exceedtbl['unitOfMeasureID'] = $itemData['defaultUnitOfMeasureID'];
                    $exceedtbl['unitOfMeasure'] = $itemUOM[$i];
                    $exceedtbl['conversionRateUOM'] = $conversion;
                    $exceedtbl['documentDate'] = current_date();
                    $exceedtbl['companyID'] = $companyID;
                    $exceedtbl['segmentID'] = $wareHouseData['segmentID'];
                    $exceedtbl['segmentCode'] = $wareHouseData['segmentCode'];
                    $exceedtbl['createdUserGroup'] = $this->common_data['user_group'];
                    $exceedtbl['createdPCID'] = $this->common_data['current_pc'];
                    $exceedtbl['createdUserID'] = $this->common_data['current_userID'];
                    $exceedtbl['createdDateTime'] = current_date();
                    $exceedtbl['createdUserName'] = $this->common_data['current_user'];

                    $this->db->insert('srp_erp_itemexceeded', $exceedtbl);

                    //this section is new
                    $balanceQty = $availableQTY - $qty;
                    $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
                    $itemUpdateQty = array('currentStock' => $balanceQty);
                    $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);
                    //=========================================


                    $exceedGL['wareHouseAutoID'] = $wareHouseID;

                    if ($CreditSalesAmnt != 0) {
                        $exceedGL['documentCode'] = 'CINV';
                        $exceedGL['documentMasterAutoID'] = $customerInvoiceMasterID;
                        $exceedGL['documentSystemCode'] = $data_customer_invoice['invoiceCode'];
                    } else {
                        $exceedGL['documentCode'] = 'POS';
                        $exceedGL['documentMasterAutoID'] = $invID;
                        $exceedGL['documentSystemCode'] = $refCode; //$sequenceCode;
                    }

                    $exceedGL['documentDate'] = current_date();
                    $exceedGL['documentYear'] = date("Y", strtotime(current_date()));
                    $exceedGL['documentMonth'] = date("m", strtotime(current_date()));
                    $exceedGL['documentNarration'] = 'GPOS Exceeded';
                    $exceedGL['GLAutoID'] = $exceedGlAutoID;
                    $exceedGL['systemGLCode'] = $exceedGlDesc['systemAccountCode'];
                    $exceedGL['GLCode'] = $exceedGlDesc['GLSecondaryCode'];
                    $exceedGL['GLDescription'] = $exceedGlDesc['GLDescription'];
                    $exceedGL['GLType'] = $exceedGlDesc['subCategory'];
                    $exceedGL['amount_type'] = 'cr';
                    $exceedGL['transactionCurrencyID'] = $localConversion['trCurrencyID'];
                    $exceedGL['transactionCurrency'] = $tr_currency;
                    $exceedGL['transactionExchangeRate'] = $transConversionRate;
                    $exceedGL['transactionAmount'] = $exceedtbl['transactionAmount'] * -1;
                    $exceedGL['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;
                    $exceedGL['companyLocalCurrencyID'] = $localConversion['currencyID'];
                    $exceedGL['companyLocalCurrency'] = $com_currency;
                    $exceedGL['companyLocalExchangeRate'] = $localConversionRate;
                    $exceedGL['companyLocalAmount'] = ($exceedGL['transactionAmount'] / $localConversionRate);
                    $exceedGL['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

                    $exceedGL['companyReportingCurrencyID'] = $reportConversion['currencyID'];
                    $exceedGL['companyReportingCurrency'] = $rep_currency;
                    $exceedGL['companyReportingExchangeRate'] = $reportConversionRate;
                    $exceedGL['companyReportingAmount'] = ($exceedGL['transactionAmount'] / $reportConversionRate);
                    $exceedGL['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

                    if ($customerID == 0) {
                        $exceedGL['partyAutoID'] = 0;
                        $exceedGL['partySystemCode'] = 'CASH';
                        $exceedGL['partyName'] = 'CASH';
                        $exceedGL['partyCurrencyID'] = 0;
                        $exceedGL['partyCurrency'] = $tr_currency;
                        $exceedGL['partyExchangeRate'] = $transConversionRate;
                        $exceedGL['partyCurrencyAmount'] = $exceedGL['transactionAmount'];
                        $exceedGL['partyCurrencyDecimalPlaces'] = $tr_currDPlace;
                    } else {
                        $partyDatacon = currency_conversion($tr_currency, $cusData['customerCurrency']);
                        $exceedGL['partyAutoID'] = $cusData['customerAutoID'];
                        $exceedGL['partySystemCode'] = $cusData['customerSystemCode'];
                        $exceedGL['partyName'] = $cusData['customerName'];
                        $exceedGL['partyCurrencyID'] = $cusData['customerCurrencyID'];
                        $exceedGL['partyCurrency'] = $cusData['customerCurrency'];
                        $exceedGL['partyExchangeRate'] = $partyDatacon['conversion'];
                        $exceedGL['partyCurrencyAmount'] = ($exceedGL['transactionAmount'] / $partyDatacon['conversion']) * -1;
                        $exceedGL['partyCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];
                    }
                    $exceedGL['confirmedByEmpID'] = $this->common_data['current_userID'];
                    $exceedGL['confirmedByName'] = $this->common_data['current_user'];
                    $exceedGL['confirmedDate'] = current_date();
                    $exceedGL['approvedDate'] = current_date();
                    $exceedGL['approvedbyEmpID'] = $this->common_data['current_userID'];
                    $exceedGL['approvedbyEmpName'] = $this->common_data['current_user'];
                    $exceedGL['segmentID'] = $wareHouseData['segmentID'];
                    $exceedGL['segmentCode'] = $wareHouseData['segmentCode'];
                    $exceedGL['companyID'] = current_companyID();
                    $exceedGL['companyCode'] = $this->common_data['company_data']['company_code'];
                    $exceedGL['createdUserGroup'] = $this->common_data['user_group'];
                    $exceedGL['createdPCID'] = $this->common_data['current_pc'];
                    $exceedGL['createdUserID'] = $this->common_data['current_userID'];
                    $exceedGL['createdDateTime'] = current_date();
                    $exceedGL['createdUserName'] = $this->common_data['current_user'];

                    $this->db->insert('srp_erp_generalledger', $exceedGL);
                }
            }


            $notitemleg = 0;
            if ($exccededVar == 1) {
                $notExceeddINVqty = $itemQty[$i] - abs($exceededQty);
                
                if ($excceditems['currentStock'] < 0) {
                    $notitemleg = 1;
                }
                if ($notExceeddINVqty > 0 && $notitemleg == 0) {
                    
                    $price = str_replace(',', '', $itemPrice[$i]);
                    $itemTotal = $notExceeddINVqty * $price;
                    $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal - ($itemTotal * 0.01 * $itemDis[$i])) : $itemTotal;
                    $itemTotal = round($itemTotal, $tr_currDPlace);

                    $dataIntExcced[$i]['invoiceID'] = $invID;
                    $dataIntExcced[$i]['itemAutoID'] = $itemID;
                    $dataIntExcced[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
                    $dataIntExcced[$i]['itemDescription'] = $itemData['itemDescription'];
                    $dataIntExcced[$i]['defaultUOMID'] = $itemData['defaultUnitOfMeasureID'];
                    $dataIntExcced[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
                    $dataIntExcced[$i]['unitOfMeasure'] = $itemUOM[$i];
                    $dataIntExcced[$i]['UOMID'] = $itemData['defaultUnitOfMeasureID'];
                    $dataIntExcced[$i]['conversionRateUOM'] = $conversion;
                    $dataIntExcced[$i]['qty'] = $notExceeddINVqty;
                    $dataIntExcced[$i]['price'] = $price;
                    $dataIntExcced[$i]['discountPer'] = $itemDis[$i];
                    if ($itemDis[$i] > 0) {
                        $discountAmount = ($price * 0.01 * $itemDis[$i]);
                    } else {
                        $discountAmount = 0;
                    }
                    $dataIntExcced[$i]['discountAmount'] = $discountAmount;


                    $gen_disc_percentage = $this->input->post('gen_disc_percentage');
                    if ($gen_disc_percentage > 0) {
                        $gen_discountAmount = ($price - $discountAmount) * 0.01 * $gen_disc_percentage * $notExceeddINVqty;
                    } else {
                        $gen_discountAmount = 0;
                    }

                    $dataIntExcced[$i]['generalDiscountPercentage'] = $gen_disc_percentage;
                    $dataIntExcced[$i]['generalDiscountAmount'] = $gen_discountAmount;

                    //                    $calculatedWacAmount = $this->db->query("SELECT
                    //	itemAutoID,
                    //    ( (SUM( transactionAmount / companyLocalExchangeRate)) / (sum( transactionqty / convertionRate) )  )	as wacAmount,
                    //	( (SUM( transactionAmount / companyReportingExchangeRate)) / (sum( transactionqty / convertionRate) )  )	as wacReportingAmount
                    //FROM
                    //	srp_erp_itemledger
                    //WHERE
                    //	itemAutoID = $itemID
                    //GROUP BY
                    //	itemAutoID")->row()->wacAmount;

                    //$dataIntExcced[$i]['wacAmount'] = $calculatedWacAmount;//$itemData['companyLocalWacAmount']; <- previous value.
                    $dataIntExcced[$i]['wacAmount'] = $itemData['companyLocalWacAmount'];
                    $dataIntExcced[$i]['itemFinanceCategory'] = $itemData['subcategoryID'];
                    $dataIntExcced[$i]['itemFinanceCategorySub'] = $itemData['subSubCategoryID'];
                    $dataIntExcced[$i]['financeCategory'] = $itemData['financeCategory'];
                    $dataIntExcced[$i]['itemCategory'] = $itemData['mainCategory'];

                    $dataIntExcced[$i]['expenseGLAutoID'] = $itemData['costGLAutoID'];
                    $dataIntExcced[$i]['expenseGLCode'] = $itemData['costGLCode'];
                    $dataIntExcced[$i]['expenseSystemGLCode'] = $itemData['costSystemGLCode'];
                    $dataIntExcced[$i]['expenseGLDescription'] = $itemData['costDescription'];
                    $dataIntExcced[$i]['expenseGLType'] = $itemData['costType'];

                    $dataIntExcced[$i]['revenueGLAutoID'] = $itemData['revanueGLAutoID'];
                    $dataIntExcced[$i]['revenueGLCode'] = $itemData['revanueGLCode'];
                    $dataIntExcced[$i]['revenueSystemGLCode'] = $itemData['revanueSystemGLCode'];
                    $dataIntExcced[$i]['revenueGLDescription'] = $itemData['revanueDescription'];
                    $dataIntExcced[$i]['revenueGLType'] = $itemData['revanueType'];

                    $dataIntExcced[$i]['assetGLAutoID'] = $itemData['assteGLAutoID'];
                    $dataIntExcced[$i]['assetGLCode'] = $itemData['assteGLCode'];
                    $dataIntExcced[$i]['assetSystemGLCode'] = $itemData['assteSystemGLCode'];
                    $dataIntExcced[$i]['assetGLDescription'] = $itemData['assteDescription'];
                    $dataIntExcced[$i]['assetGLType'] = $itemData['assteType'];


                    $dataIntExcced[$i]['transactionAmountBeforeDiscount'] = $itemTotal;
                    $itemTotal = $itemTotal - $gen_discountAmount;
                    $dataIntExcced[$i]['transactionAmount'] = $itemTotal;
                    $dataIntExcced[$i]['transactionExchangeRate'] = $transConversionRate;
                    $dataIntExcced[$i]['transactionCurrency'] = $tr_currency;
                    $dataIntExcced[$i]['transactionCurrencyID'] = $localConversion['trCurrencyID'];
                    $dataIntExcced[$i]['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;
                    $dataIntExcced[$i]['companyLocalAmount'] = round(($itemTotal / $localConversionRate), $com_currDPlace);

                    $dataIntExcced[$i]['companyLocalExchangeRate'] = $localConversionRate;
                    $dataIntExcced[$i]['companyLocalCurrency'] = $com_currency;
                    $dataIntExcced[$i]['companyLocalCurrencyID'] = $localConversion['currencyID'];
                    $dataIntExcced[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

                    $dataIntExcced[$i]['companyReportingAmount'] = round(($itemTotal / $reportConversionRate), $rep_currDPlace);
                    $dataIntExcced[$i]['companyReportingExchangeRate'] = $reportConversionRate;
                    $dataIntExcced[$i]['companyReportingCurrency'] = $rep_currency;
                    $dataIntExcced[$i]['companyReportingCurrencyID'] = $reportConversion['currencyID'];
                    $dataIntExcced[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;


                    $dataIntExcced[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataIntExcced[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $dataIntExcced[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $dataIntExcced[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $dataIntExcced[$i]['createdUserName'] = $this->common_data['current_user'];
                    $dataIntExcced[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $dataIntExcced[$i]['createdDateTime'] = current_date();
                    $wacData = $this->wac->wac_calculation(1, $itemID, $notExceeddINVqty, '', $this->common_data['ware_houseID']);
                    
                    if ($CreditSalesAmnt > 0) {

                        $item_ledger_arr[$i] = $this->item_ledger_customerInvoice($financeYear, $financePeriod, $customerInvoiceCode, $dataIntExcced[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData, null, $customerInvoiceMasterID);
                        $this->db->insert('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.
                    } else {

                        $item_ledger_arr[$i] = $this->item_ledger($financeYear, $financePeriod, $sequenceCode, $dataIntExcced[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData);
                        $this->db->insert('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.
                    }
                }
                
            } else {
                $wacData = $this->wac->wac_calculation(1, $itemID, $qty, '', $this->common_data['ware_houseID']);
                $isItemBatchPolicy = getPolicyValues('IB', 'All');


                if ($CreditSalesAmnt > 0) {
                    //only for item inventory category batch is enable

                    if($isItemBatchPolicy == 1 && $itemMainCategory == 1){
                        $item_ledger_arr[$i] = $this->item_ledger_customerInvoice_batch($financeYear, $financePeriod, $customerInvoiceCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData, null, $customerInvoiceMasterID,$sequenceCode);
                        $this->db->insert_batch('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.
                    }else{
                        $item_ledger_arr[$i] = $this->item_ledger_customerInvoice($financeYear, $financePeriod, $customerInvoiceCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData, null, $customerInvoiceMasterID);
                        $this->db->insert('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.
                    }
                   
                } else {
                    //only for item inventory category batch is enable
                    

                    if($isItemBatchPolicy == 1 && $itemMainCategory == 1){
                        $item_ledger_arr[$i] = $this->item_ledger_batch($financeYear, $financePeriod, $invoiceCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData);
                        $this->db->insert_batch('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.
                    }else{
                        
                        $item_ledger_arr[$i] = $this->item_ledger($financeYear, $financePeriod, $invoiceCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData);
                        $this->db->insert('srp_erp_itemledger', $item_ledger_arr[$i]); //this is new. testing.

                    }

                }
            }


            $i++;
        }


    }


    function get_exchange_selected_items(){

        $itemID = $this->input->post('itemID');
        $itemName = $this->input->post('itemName');
        $itemQty = $this->input->post('itemQty');
        $itemUOM = $this->input->post('itemUOM');
        $itemPrice = $this->input->post('itemPrice');
        $itemDis = $this->input->post('itemDis');
        $itemDisAmount = $this->input->post('itemDisAmount');
        $taxPerUnit = $this->input->post('taxPerUnit');

        $wareHouseID = $this->common_data['ware_houseID'];
        // $itemDisAmount = $this->input->post('itemDisAmount');
        $base_array = array();

        foreach($itemID as $key => $item){
            $result = $this->db->query("SELECT t2.partNo,t2.itemAutoID,t2.seconeryItemCode, t2.itemSystemCode as itemSystemCode, t2.itemDescription, IFNULL(t1.currentStock,0) as currentStock,
                t2.companyLocalSellingPrice, defaultUnitOfMeasure, defaultUnitOfMeasureID as unitOfMeasureID, itemImage, barcode
                FROM srp_erp_warehouseitems t1
                JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                WHERE t2.itemAutoID = {$item}" )->row_array();

            $uomList = $this->get_item_default_measure_details($item,$result['unitOfMeasureID']);

            $result['uom_output'] =  $uomList;
            $result['requested_qty'] = '-'.$itemQty[$key];
            $result['requested_dis'] = $itemDis[$key];
            $result['requested_disAmount'] = $itemDisAmount[$key];
            $result['requested_price'] = '-'.($itemPrice[$key] + $taxPerUnit[$key]);
            $result['requested_tax'] = $taxPerUnit[$key];

            $base_arr[] = $result;
        }
       
        return $base_arr;

    }



}
