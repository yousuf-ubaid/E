<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*

============================================================

-- File Name : Pos_restaurant.php
-- Project Name : POS
-- Module Name : POS Restaurant model
-- Create date : 25 - October 2016
-- Description : SME POS System.

--REVISION HISTORY
--Date: 25 - Oct 2016 : comment started

============================================================

*/

class Pos_restaurant_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('pos_policy');
        $this->load->model('Pos_model');
        $this->load->model('Inventory_modal');
        $this->load->model('Pos_restaurant_model');
        $this->load->model('Pos_config_model');
        $this->load->model('Pos_restaurant_accounts');
        $this->load->helper('cookie');
        $this->load->helper('pos');
    }

    function item_search()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouseID = $this->common_data['ware_houseID'];
        $search_string = "%" . $_GET['q'] . "%";
        return $this->db->query("SELECT t1.itemAutoID, t1.itemSystemCode, t1.itemDescription, t1.currentStock,
                                 t2.companyLocalSellingPrice, defaultUnitOfMeasure, itemImage
                                 FROM srp_erp_warehouseitems t1
                                 JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                 WHERE (t1.itemSystemCode LIKE '" . $search_string . "' OR t1.itemDescription LIKE '" . $search_string . "')
                                 AND t2.companyID={$companyID} AND t1.wareHouseAutoID ={$wareHouseID} AND isActive=1")->result_array();

        /*return $this->db->query('SELECT mainCategoryID, subcategoryID, subSubCategoryID, revanueGLCode, itemSystemCode,
                                 costGLCode , assteGLCode, defaultUnitOfMeasure, itemDescription, itemAutoID, currentStock,
                                 companyLocalWacAmount, companyLocalSellingPrice
                                 FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "'.$search_string.'"
                                 OR itemDescription LIKE "'.$search_string.'") AND companyCode = "'.$companyCode.'"
                                 AND isActive="1"')->result_array();*/
    }

    function isHaveNotClosedSession_tabUsers()
    {
        $q = "SELECT `shiftID`, `counterID` FROM `srp_erp_pos_shiftdetails` WHERE `companyID` = '" . current_companyID() . "' AND `wareHouseID` = '" . current_warehouseID() . "' AND `isClosed` =0 AND `counterID`>0";
        $result = $this->db->query($q)->row_array();
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

        return $this->db->select('shiftID, counterID')->from('srp_erp_pos_shiftdetails')->where($where)->get()->row_array();
        // echo $this->db->last_query();
    }


    function create_tmp_session($shiftID)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_shiftdetails");
        $this->db->where("shiftID", $shiftID);
        $result = $this->db->get()->row_array();
        unset($result['shiftID']);
        $result['empID'] = current_userID();
        $result['counterID'] = null;
        $result['startingBalance_transaction'] = 0;
        $result['startingBalance_local'] = 0;
        $result['startingBalance_reporting'] = 0;
        $result['createdDateTime'] = format_date_mysql_datetime();
        $result['createdUserName'] = current_user();
        $result['createdPCID'] = current_pc();

        return $this->db->insert('srp_erp_pos_shiftdetails', $result);
    }

    function getInvoiceCode()
    {
        $query = $this->db->select('serialNo')->from('srp_erp_pos_invoice')->where('companyID', $this->common_data['company_data']['company_id'])
            ->order_by('invoiceID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastRefNo = $lastRefArray['serialNo'] ?? 0;
        $lastRefNo = ($lastRefNo == null) ? 1 : $lastRefArray['serialNo'] + 1;

        $this->load->library('sequence');
        $refCode = $this->sequence->sequence_generator('REF', $lastRefNo);

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

        /*$this->db->select('wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation')
            ->from('srp_erp_warehousemaster')
            ->where('wareHouseAutoID', $this->common_data['ware_houseID']);
        return $this->db->get()->row_array();*/
    }

    function invoice_create()
    {
        $currentShiftData = $this->isHaveNotClosedSession();

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
            $chequeAmount = $this->input->post('_chequeAmount');
            $cardAmount = $this->input->post('_cardAmount');
            $total_discVal = $this->input->post('discVal');
            $paidAmount = ($cashAmount + $chequeAmount + $cardAmount);
            $netTotVal = $this->input->post('netTotVal');
            $balanceAmount = ($netTotVal - $paidAmount);

            if ($netTotVal < $paidAmount) {
                $cashAmount = $netTotVal - ($chequeAmount + $cardAmount);
                $balanceAmount = 0;
            }

            /*Payment Details Calculation End*/

            //Get last reference no
            $invCodeDet = $this->getInvoiceCode();
            $lastRefNo = $invCodeDet['lastRefNo'];
            $refCode = $invCodeDet['refCode'];

            $localConversion = currency_conversion($com_currency, $com_currency, $netTotVal);
            $localConversionRate = $localConversion['conversion'];
            $transConversion = currency_conversion($tr_currency, $com_currency, $netTotVal);
            $tr_currDPlace = $transConversion['DecimalPlaces'];
            $transConversionRate = $transConversion['conversion'];
            $reportConversion = currency_conversion($rep_currency, $com_currency, $netTotVal);
            $reportConversionRate = $reportConversion['conversion'];

            /*echo '<pre>';print_r($tr_currency);echo '</pre>';
            die();*/

            $invArray = array(
                'documentSystemCode' => $refCode,
                'documentCode' => 'POS',
                'serialNo' => $lastRefNo,
                'customerID' => $customerID,
                'customerCode' => $customerCode,
                'invoiceDate' => $invoiceDate,
                'counterID' => $currentShiftData['counterID'],
                'shiftID' => $currentShiftData['shiftID'],


                'netTotal' => $netTotVal,
                'localNetTotal' => ($netTotVal * $localConversionRate),
                'reportingNetTotal' => ($netTotVal * $reportConversionRate),

                'paidAmount' => $paidAmount,
                'localPaidAmount' => ($paidAmount * $localConversionRate),
                'reportingPaidAmount' => ($paidAmount * $reportConversionRate),

                'balanceAmount' => $balanceAmount,
                'localBalanceAmount' => ($balanceAmount * $localConversionRate),
                'reportingBalanceAmount' => ($balanceAmount * $reportConversionRate),

                'cashAmount' => $cashAmount,
                'chequeAmount' => $chequeAmount,
                'cardAmount' => $cardAmount,

                'discountAmount' => $total_discVal,
                'localDiscountAmount' => ($total_discVal * $localConversionRate),
                'reportingDiscountAmount' => ($total_discVal * $reportConversionRate),


                'companyLocalCurrencyID' => '',
                'companyLocalCurrency' => $com_currency,
                'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
                'companyLocalExchangeRate' => $localConversionRate,

                'transactionCurrencyID' => '',
                'transactionCurrency' => $tr_currency,
                'transactionCurrencyDecimalPlaces' => $tr_currDPlace,
                'transactionExchangeRate' => $transConversionRate,

                'companyReportingCurrencyID' => '',
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

            );

            if ($customerID == 0) {
                $bankData = $this->db->query("SELECT receivableAutoID, receivableSystemGLCode, receivableGLAccount,
                                          receivableDescription, receivableType
                                          FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();
                $invArray['bankGLAutoID'] = $bankData['receivableAutoID'];
                $invArray['bankSystemGLCode'] = $bankData['receivableSystemGLCode'];
                $invArray['bankGLAccount'] = $bankData['receivableGLAccount'];
                $invArray['bankGLDescription'] = $bankData['receivableDescription'];
                $invArray['bankGLType'] = $bankData['receivableType'];
            } else {
                $cusData = $this->db->query("SELECT receivableAutoID, receivableSystemGLCode, receivableGLAccount,
                                         receivableDescription, receivableType
                                         FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

                $invArray['customerReceivableAutoID'] = $cusData['receivableAutoID'];
                $invArray['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
                $invArray['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
                $invArray['customerReceivableDescription'] = $cusData['receivableDescription'];
                $invArray['customerReceivableType'] = $cusData['receivableType'];
            }

            /*echo '<pre>';print_r($cusData);echo '</pre>';
            die();*/

            $this->db->trans_start();
            $this->db->insert('srp_erp_pos_invoice', $invArray);
            $invID = $this->db->insert_id();

            $i = 0;
            $dataInt = array();
            foreach ($item as $itemID) {
                $itemData = fetch_ware_house_item_data($itemID);
                $conversion = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
                $conversionRate = 1 / $conversion;
                $availableQTY = $itemData['wareHouseQty'];
                $qty = $itemQty[$i] * $conversionRate;

                /*echo 'conversion: '.$conversion;
                echo '<p>$itemQty[$i]: '.$itemQty[$i];
                echo '<p>conversionRate: '.$conversionRate;
                echo '<p>availableQTY: '.$availableQTY;
                echo '<p>QTY: '.$qty; die();*/

                /*if ($availableQTY >= $qty) {*/

                $itemTotal = $itemQty[$i] * $itemPrice[$i];
                $itemTotal = ($itemDis[$i] > 0) ? ($itemPrice[$i] * 0.01 * $itemDis[$i]) : $itemTotal;

                $dataInt[$i]['invoiceID'] = $invID;
                $dataInt[$i]['itemAutoID'] = $itemID;
                $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
                $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
                $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
                $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
                $dataInt[$i]['conversionRateUOM'] = $conversion;
                $dataInt[$i]['qty'] = $itemQty[$i];
                $dataInt[$i]['price'] = $itemPrice[$i];
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


                $dataInt[$i]['transactionAmount'] = ($itemTotal * $tr_currDPlace);;
                $dataInt[$i]['transactionExchangeRate'] = $transConversionRate;
                $dataInt[$i]['transactionCurrency'] = $tr_currency;
                $dataInt[$i]['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;

                $dataInt[$i]['companyLocalAmount'] = ($itemTotal * $localConversionRate);
                $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
                $dataInt[$i]['companyLocalCurrency'] = $com_currency;
                $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

                $dataInt[$i]['companyReportingAmount'] = ($itemTotal * $reportConversionRate);
                $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
                $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
                $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

                $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
                $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
                $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
                $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $dataInt[$i]['createdDateTime'] = current_date();


                $balanceQty = $availableQTY - $qty;

                /* echo '<p>conversion:'.$conversion.'</p><p>qty:'.$qty.'</p><p>itemQty:'.$itemQty[$i].'</p>';
                 echo '<p>AVlQty:'.$availableQTY.'</p>';
                 echo '<p>balanceQty:'.$balanceQty.'</p>';
                 die();*/
                $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
                $itemUpdateQty = array('currentStock' => $balanceQty);
                $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);


                $i++;
                //}
                /*else {
                    $this->db->trans_rollback();
                    return array('e', '[ '.$itemData['itemSystemCode'].' - '.$itemData['itemDescription'].' ]<p> is available only '.$availableQTY.' qty');
                    break;
                }*/
            }

            $this->db->insert_batch('srp_erp_pos_invoicedetail', $dataInt);


            $this->db->trans_complete();
            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                return array('e', 'Error in Invoice Create');
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Code : ' . $refCode . ' ', $invID, $refCode);
            }
        } else {
            return array('e', 'You have not a valid session.<p>Please login and try again.</p>');
        }
    }

    function invoice_hold()
    {

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
        $chequeAmount = $this->input->post('_chequeAmount');
        $cardAmount = $this->input->post('_cardAmount');
        $total_discVal = $this->input->post('discVal');
        $paidAmount = ($cashAmount + $chequeAmount + $cardAmount);
        $netTotVal = $this->input->post('netTotVal');
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

        /*echo $tr_currency.' // '.$com_repCurrency.' // '.$netTotVal;*/


        $invArray = array(
            'documentSystemCode' => $refCode,
            'serialNo' => $lastRefNo,
            'customerID' => $customerID,
            'customerCode' => $customerCode,
            'invoiceDate' => $invoiceDate,

            'netTotal' => $netTotVal,
            'localNetTotal' => ($netTotVal * $localConversionRate),
            'reportingNetTotal' => ($netTotVal * $reportConversionRate),

            'paidAmount' => $paidAmount,
            'localPaidAmount' => ($paidAmount * $localConversionRate),
            'reportingPaidAmount' => ($paidAmount * $reportConversionRate),

            'balanceAmount' => $balanceAmount,
            'localBalanceAmount' => ($balanceAmount * $localConversionRate),
            'reportingBalanceAmount' => ($balanceAmount * $reportConversionRate),

            'cashAmount' => $cashAmount,
            'chequeAmount' => $chequeAmount,
            'cardAmount' => $cardAmount,

            'discountAmount' => $total_discVal,
            'localDiscountAmount' => ($total_discVal * $localConversionRate),
            'reportingDiscountAmount' => ($total_discVal * $reportConversionRate),


            'companyLocalExchangeRate' => $localConversionRate,
            'companyLocalCurrency' => $com_currency,
            'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,

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
            'createdDateTime' => current_date()
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
            $itemTotal = $itemQty[$i] * $itemPrice[$i];
            $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal * 0.01 * $itemDis[$i]) : $itemTotal;

            $dataInt[$i]['invoiceID'] = $invID;
            $dataInt[$i]['itemAutoID'] = $itemID;
            $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
            $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
            $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
            $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
            $dataInt[$i]['conversionRateUOM'] = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
            $dataInt[$i]['qty'] = $itemQty[$i];
            $dataInt[$i]['price'] = $itemPrice[$i];
            $dataInt[$i]['discountPer'] = $itemDis[$i];

            $dataInt[$i]['itemFinanceCategory'] = $itemData['subcategoryID'];
            $dataInt[$i]['itemFinanceCategorySub'] = $itemData['subSubCategoryID'];
            $dataInt[$i]['financeCategory'] = $itemData['financeCategory'];
            $dataInt[$i]['itemCategory'] = $itemData['mainCategory'];

            $dataInt[$i]['transactionAmount'] = $itemTotal;
            $dataInt[$i]['transactionExchangeRate'] = '';
            $dataInt[$i]['transactionCurrency'] = $com_currency;
            $dataInt[$i]['transactionCurrencyDecimalPlaces'] = '';

            $dataInt[$i]['companyLocalAmount'] = ($itemTotal * $localConversionRate);
            $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
            $dataInt[$i]['companyLocalCurrency'] = $com_currency;
            $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

            $dataInt[$i]['companyReportingAmount'] = ($itemTotal * $reportConversionRate);
            $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
            $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
            $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

            $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
            $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
            $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
            $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $dataInt[$i]['createdDateTime'] = current_date();
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

        return $this->db->query("SELECT customerAutoID, customerSystemCode, customerName, customerCurrency, customerAddress1
                                 FROM srp_erp_customermaster WHERE companyID={$companyID} AND
                                 (customerName LIKE '%$key%' OR customerName LIKE '%$key%')
                                 UNION SELECT 1, 'CASH', 'Cash', '', ''")->result_array();
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
            return array('s', 'Card Details Updated');
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
                                 FROM srp_erp_pos_invoice t1 WHERE companyID={$companyID} AND t1.wareHouseAutoID={$wareHouse}")->result_array();
    }

    function invoice_search()
    {
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];
        $invoiceCode = $this->input->post('invoiceCode');

        $isExistInv = $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName,
                                  (SELECT sum(balanceAmount) FROM srp_erp_pos_invoice WHERE customerID = t1.customerID) AS cusBalance
                                  FROM srp_erp_pos_invoice t1 WHERE companyID={$companyID} AND t1.wareHouseAutoID={$wareHouse} AND
                                  t1.documentSystemCode='$invoiceCode'")->row_array();

        if ($isExistInv != null) {
            $invItems = $this->db->select('*')->from('srp_erp_pos_invoicedetail')->where('invoiceID', $isExistInv['invoiceID'])
                ->get()->result_array();
            return array(
                0 => 's',
                1 => $isExistInv,
                2 => $invItems
            );
        } else {
            return array('w', 'There is not a invoice in this number');
        }
    }

    function recall_hold_invoice()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouse = $this->common_data['ware_houseID'];
        return $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName
                                 FROM srp_erp_pos_invoicehold t1 WHERE companyID={$companyID} AND t1.wareHouseAutoID={$wareHouse}")->result_array();
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
                                     (SELECT itemImage FROM srp_erp_itemmaster WHERE itemAutoID=t1.itemAutoID) AS itemImage
                                     FROM srp_erp_pos_invoiceholddetail t1 WHERE  invoiceID={$holdID}")->result_array();

        return array($masterDet, $itemDet);
    }

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

    function get_giftCardTopUpCashCollection()
    {
        $counterInfo = get_counterData();
        $shiftID = $counterInfo['shiftID'];
        $q = "SELECT
                srp_erp_pos_cardtopup.glConfigMasterID,
                sum( srp_erp_pos_cardtopup.topUpAmount ) AS totalAmount,
                srp_erp_pos_cardtopup.shiftID 
            FROM
                srp_erp_pos_cardtopup 
            WHERE
                srp_erp_pos_cardtopup.shiftID = '" . $shiftID . "' 
                AND srp_erp_pos_cardtopup.glConfigMasterID =1";

        $totalAmount = $this->db->query($q)->row('totalAmount');
        $totalAmount = !empty($totalAmount) ? $totalAmount : 0;
        return $totalAmount;
    }

    function get_counterData($counterID)
    {
        return $this->db->select('counterID, counterCode, counterName')->from('srp_erp_pos_counters')
            ->where('counterID', $counterID)->where('isActive', 1)
            ->get()->row_array();
    }

    function load_wareHouseUsers($wareHouse)
    {
        return $this->db->select("autoID, userID, Ecode, CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) eName ")
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
        $com = $this->common_data['company_data']['company_id'];
        $where = "(Ename1 LIKE '%$keyword%' OR Ename2 LIKE '%$keyword%' OR Ename3 LIKE '%$keyword%' OR Ename4 LIKE '%$keyword%' OR ECode LIKE '%$keyword%') ";
        $where .= "AND t1.Erp_companyID='$com'";
        $where .= "AND NOT EXISTS( SELECT userID FROM srp_erp_warehouse_users WHERE userID=t1.EIdNo )";

        $this->db->select("EIdNo, ECode, CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) empName");
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
                   (SELECT wareHouseLocation FROM srp_erp_warehousemaster WHERE srp_erp_warehousemaster.wareHouseAutoID=t1.wareHouseID) AS wareHouse")
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

    function get_warehouseMenues($warehouseID)
    {
        $path = base_url();
        $this->db->select("category.autoID as autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice , menuMaster.isPack ");
        $this->db->from("srp_erp_pos_warehousemenumaster menu");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('category.isDeleted', 0);
        $this->db->where('category.isActive', 1);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('menu.warehouseID', $warehouseID);
        $this->db->order_by('menuMaster.sortOrder', 'asc');
        $this->db->order_by('menuMaster.menuMasterDescription', 'asc');
        $result = $this->db->get()->result_array();
        /*echo $this->db->last_query();
        exit;*/
        return $result;
    }

    function get_warehouseCategory($warehouseID)
    {
        $path = base_url();
        $this->db->select("warehouseCategory.autoID, category.menuCategoryID, category.menuCategoryDescription as description, concat('" . $path . "',category.image) as image, category.bgColor, category.masterLevelID, category.levelNo, category.showImageYN as showImageYN");
        $this->db->from("srp_erp_pos_warehousemenucategory warehouseCategory");
        $this->db->join("srp_erp_pos_menucategory category", "warehouseCategory.menuCategoryID = category.menuCategoryID", "INNER");
        $this->db->where('category.isActive', 1);
        $this->db->where('warehouseCategory.isDeleted', 0);
        $this->db->where('warehouseCategory.isActive', 1);
        $this->db->where('category.companyID', current_companyID());
        $this->db->where('category.isDeleted', 0);
        $this->db->where('warehouseCategory.warehouseID', $warehouseID);
        $this->db->order_by('category.sortOrder', 'asc');
        $this->db->order_by('category.menuCategoryDescription', 'asc');
        $result = $this->db->get()->result_array();

        /*echo $this->db->last_query();
        exit;*/

        return $result;
    }


    function get_warehouseSubCategory($warehouseID)
    {
        $path = base_url();
        $q = "SELECT
                  whmc2.autoID,
                    srp_erp_pos_menucategory.menuCategoryID,
                    srp_erp_pos_menucategory.menuCategoryDescription AS description,
                    concat('" . $path . "',srp_erp_pos_menucategory.image) as image,
                    srp_erp_pos_menucategory.bgColor,
                    srp_erp_pos_menucategory.masterLevelID,
                    srp_erp_pos_menucategory.levelNo,
                    srp_erp_pos_menucategory.showImageYN
                FROM
                    srp_erp_pos_menucategory
                    INNER JOIN srp_erp_pos_warehousemenucategory whmc2 ON whmc2.menuCategoryID = srp_erp_pos_menucategory.menuCategoryID
                WHERE
                    srp_erp_pos_menucategory.menuCategoryID IN (
                        SELECT
                            masterLevelID
                        FROM
                            srp_erp_pos_menucategory
                        INNER JOIN srp_erp_pos_warehousemenucategory whmc ON whmc.menuCategoryID = srp_erp_pos_menucategory.menuCategoryID
                        WHERE
                            srp_erp_pos_menucategory.masterLevelID IS NOT NULL  AND whmc.warehouseID = '" . $warehouseID . "' AND srp_erp_pos_menucategory.isDeleted = 0
                        GROUP BY
                            masterLevelID
                    ) AND srp_erp_pos_menucategory.companyID = '" . current_companyID() . "' AND whmc2.isDeleted = 0
                    ORDER BY `srp_erp_pos_menucategory`.`sortOrder` ASC, `srp_erp_pos_menucategory`.`menuCategoryDescription` ASC";
        /*echo $q;
        exit;*/
        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_warehouseMenu_specific($warehouseMenuID)
    {
        $path = base_url();
        $this->db->select("category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, menu.warehouseMenuCategoryID, menuMaster.menuCost, menuMaster.isPack , categoryMaster.revenueGLAutoID,menu.menuMasterID, category.menuCategoryID, menuMaster.TAXpercentage, menuMaster.taxMasterID, menuMaster.pricewithoutTax, menuMaster.totalServiceCharge,menuMaster.totalTaxAmount,menu.isTaxEnabled");
        $this->db->from("srp_erp_pos_warehousemenumaster menu");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_menucategory categoryMaster", "categoryMaster.menuCategoryID = menuMaster.menuCategoryID", "INNER");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('menu.warehouseMenuID', $warehouseMenuID);
        $result = $this->db->get()->row_array();
        /*echo $this->db->last_query();
        exit;*/
        return $result;
    }

    function get_srp_erp_pos_shiftdetails_employee()
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_shiftdetails");
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', current_companyID());
        $this->db->where('wareHouseID', current_warehouseID());
        $this->db->where('isClosed', 0);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function insert_srp_erp_pos_menusalesmaster($data)
    {
        $data['wareHouseAutoID'] = current_warehouseID();
        $data['id_store'] = current_warehouseID();
        $result = $this->db->insert('srp_erp_pos_menusalesmaster', $data);
        if ($result) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    function insert_srp_erp_pos_menusalesitems($data)
    {
        $data['id_store'] = current_warehouseID();
        $result = $this->db->insert('srp_erp_pos_menusalesitems', $data);
        if ($result) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    function get_srp_erp_pos_menusalesitems_invoiceID_forHoldBill($invoiceID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $path = base_url();
        $this->db->select("sales.menuSalesID, sales.menuSalesItemID, category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, sales.qty , sales.discountPer, sales.discountAmount, menuMaster.menuMasterID,sales.remarkes, menuMaster.pricewithoutTax, menuMaster.totalTaxAmount, menuMaster.totalServiceCharge, menu.isTaxEnabled , size.code as sizeCode, size.description as sizeDescription,sales.isSamplePrinted");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster menu", "menu.warehouseMenuID = sales.warehouseMenuID");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_menusize size", "size.menuSizeID = menuMaster.menuSizeID", "left");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('sales.menuSalesID', $invoiceID);
        $this->db->where('sales.id_store', $outletID);
        $result = $this->db->get()->result_array();

        return $result;
    }

    function get_srp_erp_pos_menusalesitems_invoiceID($invoiceID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $path = base_url();
        $this->db->select("sales.menuSalesID, sales.menuSalesItemID, category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, sales.qty , sales.discountPer, sales.discountAmount, menuMaster.menuMasterID,sales.remarkes, menuMaster.pricewithoutTax, menuMaster.totalTaxAmount, menuMaster.totalServiceCharge, menu.isTaxEnabled , size.code as sizeCode, size.description as sizeDescription,sales.isSamplePrinted");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster menu", "menu.warehouseMenuID = sales.warehouseMenuID");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_menusize size", "size.menuSizeID = menuMaster.menuSizeID", "left");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('sales.menuSalesID', $invoiceID);
        $this->db->where('sales.id_store', $outletID);
        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_srp_erp_pos_menusalesitems_specific($menuSalesItemID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }

        $this->db->select("master.*");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster warehouse", 'warehouse.warehouseMenuID = sales.warehouseMenuID', 'left');
        $this->db->join("srp_erp_pos_menumaster master", 'warehouse.menuMasterID = master.menuMasterID', 'left');
        $this->db->where('sales.menuSalesItemID', $menuSalesItemID);
        $this->db->where('sales.id_store', $outletID);
        $result = $this->db->get()->row_array();
        return $result;
    }


    function delete_menuSalesItem($id, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $this->db->where('menuSalesItemID', $id);
        $this->db->where('id_store', $outletID);
        return $this->db->delete('srp_erp_pos_menusalesitems');
    }

    function delete_srp_erp_pos_valuepackdetail_by_ItemID($menuSalesItemID)
    {
        $this->db->where('menuSalesItemID', $menuSalesItemID);
        return $this->db->delete('srp_erp_pos_valuepackdetail');
    }


    /** Delete Menu Sales */
    function delete_srp_erp_pos_menusalesitems_byMenuSalesID($menuSalesID)
    {
        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->where('id_store', current_warehouseID());
        $result = $this->db->delete('srp_erp_pos_menusalesitems');
        if ($result) {
            return array('error' => 0, 'message' => 'done');
        } else {
            return array('error' => 1, 'message' => 'Error deleting!, Please contact system support team');
        }
    }

    /** Delete Menu Sales */
    function delete_srp_erp_pos_menusalesmaster($menuSalesID)
    {
        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->where('wareHouseAutoID', current_warehouseID());
        $result = $this->db->update('srp_erp_pos_menusalesmaster', array(
            "isCancelled" => 1,
            "isVoid" => 1,
            "voidBy" => current_user(),
            "voidDatetime" => current_date(),
            "is_sync" => 0
        ));
        if ($result) {
            return array('error' => 0, 'message' => 'done');
        } else {
            return array('error' => 1, 'message' => 'Error cancelling!, Please contact system support team');
        }
    }

    /** Delete Menu Sales */
    function delete_srp_erp_pos_menusalesitemdetails_byMenuSalesID($menuSalesID)
    {
        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->where('id_store', current_warehouseID());
        $result = $this->db->delete('srp_erp_pos_menusalesitemdetails');
        if ($result) {
            return array('error' => 0, 'message' => 'done');
        } else {
            return array('error' => 1, 'message' => 'Error deleting!, Please contact system support team');
        }
    }

    function get_srp_erp_warehouse_users_WarehouseID()
    {
        $companyID = current_companyID();
        $userID = current_userID();

        $this->db->select("wareHouseID");
        $this->db->from("srp_erp_warehouse_users");
        $this->db->where('companyID', $companyID);
        $this->db->where('userID', $userID);
        $this->db->where('isActive', 1);
        $wareHouseID = $this->db->get()->row('wareHouseID');
        return $wareHouseID;
    }

    function update_srp_erp_pos_menusalesmaster($data, $id)
    {
        $this->db->where('menuSalesID', $id);
        $this->db->where('wareHouseAutoID', get_outletID());
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        return $result;
    }

    function get_srp_erp_pos_menusalesmaster($id, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $this->db->select("menuSales.*, cType.customerDescription,cusm.CustomerName as cusname,cusm.CustomerAddress1 as cusaddress,cusm.customerTelephone as custel,promo.customerName as promotn,IFNULL(empdetails.EmpShortCode,'-')  as waitername	");
        $this->db->from("srp_erp_pos_menusalesmaster menuSales");
        $this->db->join('srp_erp_customertypemaster cType', 'cType.customerTypeID = menuSales.customerTypeID', 'left'); /*customerTypeID*/
        $this->db->join('srp_erp_pos_customermaster cusm', 'cusm.posCustomerAutoID = menuSales.customerID', 'left'); /*customerTypeID*/
        $this->db->join('srp_erp_pos_customers promo', 'promo.customerID = menuSales.promotionID', 'left'); /*customerTypeID*/
        $this->db->join('srp_erp_pos_crewmembers crewmember', 'crewmember.crewMemberID = menuSales.waiterID', 'left');
        $this->db->join('srp_employeesdetails empdetails', 'empdetails.EIdNo = crewmember.EIdNo', 'left');
        $this->db->where('menuSales.menuSalesID', $id);
        $this->db->where('menuSales.wareHouseAutoID', $outletID);
        //$this->db->where('menuSales.wareHouseAutoID', current_warehouseID());
        $result = $this->db->get()->row_array();
        //var_dump($this->db->last_query());exit;
        return $result;
    }

    function get_srp_erp_pos_menusalesmaster_specific($id, $select = '*', $row = false)
    {
        $this->db->select($select);
        $this->db->from('srp_erp_pos_menusalesmaster');
        $this->db->where('menuSalesID', $id);
        if ($row) {
            return $this->db->get()->row($select);
        } else {
            return $this->db->get()->row_array();
        }
    }

    function load_posHoldReceipt()
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where('isHold', 0);
        $result = $this->db->get()->result_array();
        return $result;
    }

    function updateTotalCost($invoiceID)
    {
        $q = "UPDATE srp_erp_pos_menusalesmaster AS salesMaster, ( SELECT sum(detailTbl.menuCost) AS menuCostTmp FROM srp_erp_pos_menusalesitems AS detailTbl WHERE detailTbl.menuSalesID = " . $invoiceID . " AND detailTbl.id_store = " . current_warehouseID() . " ) tmp SET salesMaster.menuCost = tmp.menuCostTmp, salesMaster.is_sync=0 WHERE salesMaster.menuSalesID = '" . $invoiceID . "' AND salesMaster.wareHouseAutoID = '" . current_warehouseID() . "'";
        $result = $this->db->query($q);
        //echo $q.'<br/>';
        return $result;
    }

    function get_warehouseMenuItem($warehouseMenuID)
    {
        $path = base_url();
        $this->db->select("category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice , menuMaster.isPack , menuMaster.menuMasterID");
        $this->db->from("srp_erp_pos_warehousemenumaster menu");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('category.isDeleted', 0);
        $this->db->where('category.isActive', 1);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('menu.warehouseMenuID', $warehouseMenuID);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function get_pack_menuItem($packMenuID)
    {
        $path = base_url();
        $this->db->select("packItem.menuPackItemID as id, packItem.menuID as menuID, packItem.isRequired as isRequired, menuMaster.menuMasterDescription, packItem.menuCategoryID ,  concat('" . $path . "',menuMaster.menuImage) as menuImage, menuCategory.menuCategoryDescription, packItem.PackMenuID");
        $this->db->from("srp_erp_pos_menupackitem packItem");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = packItem.menuID", "left");
        $this->db->join("srp_erp_pos_menucategory menuCategory", "menuCategory.menuCategoryID = packItem.menuCategoryID", "left");
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('packItem.PackMenuID', $packMenuID);
        $this->db->order_by('packItem.isRequired', 'DESC');
        $this->db->order_by('menuMaster.menuCategoryID ', 'ASC');
        $this->db->order_by('menuMaster.menuMasterDescription ', 'ASC');

        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_srp_erp_pos_menupackitem_requiredItems($menuMasterID)
    {
        $this->db->select("menuPack.*,packGroup.packgroupdetailID");
        $this->db->from("srp_erp_pos_menupackitem menuPack");
        $this->db->join("srp_erp_pos_packgroupdetail packGroup", "packGroup.menuPackItemID = menuPack.menuPackItemID ", "LEFT");
        $this->db->where('menuPack.isRequired', 1);
        $this->db->where('packGroup.isActive', 1);
        $this->db->where('menuPack.PackMenuID', $menuMasterID);


        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_srp_erp_pos_menupackitem_optionalItems($menuMasterID)
    {
        $this->db->select("menuPack.*,packGroup.packgroupdetailID");
        $this->db->from("srp_erp_pos_menupackitem menuPack");
        $this->db->join("srp_erp_pos_packgroupdetail packGroup", "packGroup.menuPackItemID = menuPack.menuPackItemID ", "LEFT");
        $this->db->where('menuPack.isRequired', 0);
        $this->db->where('menuPack.PackMenuID', $menuMasterID);

        $result = $this->db->get()->result_array();
        return $result;
    }

    function bulk_insert_srp_erp_pos_valuepackdetail($data)
    {
        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_pos_valuepackdetail', $data);
        }
    }

    function get_currentCompanyDetail()
    {
        $this->db->select("*");
        $this->db->from("srp_erp_company");
        $this->db->where('company_id', current_companyID());
        $result = $this->db->get()->row_array();
        /*echo $this->db->last_query();
        echo '<br/>';
        echo '<br/>';*/
        return $result;
    }

    function get_report_customerTypeCount($date, $date2, $cashier = null, $Outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $q = "SELECT customertype.customerDescription,count(salesMaster.netTotal) as countTotal FROM srp_erp_pos_menusalesmaster AS salesMaster LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID WHERE salesMaster.isVoid = 0 AND  salesMaster.isHold = 0 AND salesMaster.companyID=" . current_companyID() . " AND DATE_FORMAT(salesMaster.menuSalesDate,'%Y-%m-%d')  BETWEEN '" . $date . "' AND '" . $date2 . "' " . $qString . $outletFilter . $outletsFilter . " GROUP BY customertype.customerDescription";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_customerTypeCount2($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                    customertype.customerDescription,
                    count(salesMaster.menuSalesID) AS countTotal,
                    sum(subTotal) as subTotal
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID
                WHERE
                    salesMaster.isVoid = 0 
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY 
                    customertype.customerDescription ORDER BY customertype.customerTypeID ";
        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_customerTypeCount2_new($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT						
                customertype.customerDescription,
                sum(payments.amount) AS subTotal,					
                count( salesMaster.menuSalesID ) AS countTotal
            FROM						
                srp_erp_pos_menusalesmaster AS salesMaster 
                LEFT JOIN  (
                        SELECT SUM( IFNULL(amount,0) ) as amount, menuSalesID, paymentConfigMasterID , wareHouseAutoID 
                        FROM srp_erp_pos_menusalespayments 
                        GROUP BY menuSalesID, wareHouseAutoID
                    ) as payments ON payments.menuSalesID = salesMaster.menuSalesID AND payments.wareHouseAutoID = salesMaster.wareHouseAutoID				
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID 
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID  
                WHERE
                    salesMaster.isVoid = 0 
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY 
                customertype.customerDescription 
            ORDER BY 
                customertype.customerTypeID, salesMaster.wareHouseAutoID ";
        // echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_startingBillNo($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                    customertype.customerDescription,
                    count(salesMaster.netTotal) AS countTotal,
                    sum(subTotal) as subTotal
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY 
                    customertype.customerDescription ORDER BY customertype.customerTypeID ";


        // echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_totalSalse($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }


        $q = "SELECT
                'Total Sales' AS Description,
                SUM(paidAmount) AS amount
            
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' "
            . $qString . $outletFilter .
            "AND createdUserID IN (1106, 1138, 12548)";


        // echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_javaAppDiscount($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                'Java App' AS customerName,
            SUM(netTotal) AS netTotal,
            SUM(payments.amount) AS lessAmount
            FROM
                srp_erp_pos_menusalespayments AS payments
                JOIN srp_erp_pos_menusalesmaster  AS salesMaster ON  salesMaster.menuSalesID = payments.menuSalesID
            WHERE
                payments.paymentConfigMasterID = 25 
            AND  salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID =  '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
             " . $qString . $outletFilter;


        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_salesReport_discount($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                'Discounts' AS customerName,
            SUM(netTotal) AS netTotal,
            SUM(salesMaster.discountAmount) AS lessAmount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID =  '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
            AND salesMaster.discountAmount>0 " . $qString . $outletFilter;


        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_lessAmount($date, $date2, $cashier = null, $Outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }


        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }

        $q = "SELECT
                    salesMaster.deliveryCommission,
                    customers.customerName,
                    SUM(netTotal) AS netTotal,
                    SUM( ( netTotal * ( salesMaster.deliveryCommission/100 ) ) ) AS lessAmount
                
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.deliveryPersonID
                JOIN srp_paymentmethodmaster payments ON  payments.PaymentMethodMasterID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND DATE_FORMAT(
                    salesMaster.menuSalesDate,
                    '%Y-%m-%d'
                ) 
                BETWEEN '" . $date . "'
                AND '" . $date2 . "'
                AND NOT ISNULL(
                    salesMaster.deliveryPersonID
                )
                AND salesMaster.deliveryPersonID <> 0 
                AND payments.PaymentDescription = 'Cash'
                " . $qString . "
                " . $outletFilter . "
                " . $outletsFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_lessAmount2($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }


        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                    salesMaster.deliveryCommission,
                    customers.customerName,
                    SUM(netTotal) AS netTotal,
                    SUM( deliveryCommissionAmount ) AS lessAmount
                
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.deliveryPersonID
                JOIN srp_paymentmethodmaster payments ON  payments.PaymentMethodMasterID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(
                    salesMaster.deliveryPersonID
                )
                AND salesMaster.deliveryPersonID <> 0 
                AND payments.PaymentDescription = 'Cash'
                " . $qString . "
                " . $outletFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_lessAmount_promotion($date, $date2, $cashier = null, $Outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }

        $q = "SELECT
                    salesMaster.promotionDiscount as deliveryCommission,
                    customers.customerName as customerName,
                    SUM(netTotal) AS netTotal,
                    SUM( ( netTotal * ( salesMaster.promotionDiscount/100 ) ) ) AS lessAmount
                
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.promotionID
                JOIN srp_paymentmethodmaster payments ON  payments.PaymentMethodMasterID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND DATE_FORMAT(
                    salesMaster.menuSalesDate,
                    '%Y-%m-%d'
                ) 
                BETWEEN '" . $date . "'
                AND '" . $date2 . "'
                AND NOT ISNULL(
                    salesMaster.promotionID
                )
                AND salesMaster.promotionID <> 0 
                AND payments.PaymentDescription = 'Cash'
                " . $qString . "
                " . $outletFilter . "
                " . $outletsFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_lessAmount_promotion2($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }


        $q = "SELECT
                    salesMaster.promotionDiscount AS deliveryCommission,
                    customers.customerName AS customerName,
                    SUM(IFNULL(grossTotal,0)) AS netTotal,
                    SUM(IFNULL(promotionDiscountAmount,0) ) as lessAmount
                
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.promotionID
                LEFT JOIN srp_paymentmethodmaster payments ON payments.PaymentMethodMasterID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(salesMaster.promotionID)
                AND salesMaster.promotionID <> 0
                " . $qString . "
                " . $outletFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";
        /*$q = "SELECT
                    salesMaster.promotionDiscount as deliveryCommission,
                    customers.customerName as customerName,
                    SUM(netTotal) AS netTotal,
                    SUM( ( netTotal * ( salesMaster.promotionDiscount/100 ) ) ) AS lessAmount

                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.promotionID
                JOIN srp_paymentmethodmaster payments ON  payments.PaymentMethodMasterID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(
                    salesMaster.promotionID
                )
                AND salesMaster.promotionID <> 0
                AND payments.PaymentDescription = 'Cash'
                " . $qString . "
                " . $outletFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";*/

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_paymentMethod($date, $data2, $cashier = null, $Outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ") ";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }

        $q = "SELECT payment.paymentDescription, SUM(salesMaster.netTotal) as NetTotal FROM srp_erp_pos_menusalesmaster AS salesMaster LEFT JOIN srp_erp_pos_paymentmethods as payment ON payment.paymentMethodsID= salesMaster.paymentMethod WHERE salesMaster.isVoid = 0 AND salesMaster.isHold = 0 AND salesMaster.companyID=" . current_companyID() . " AND DATE_FORMAT(salesMaster.menuSalesDate,'%Y-%m-%d')  BETWEEN '" . $date . "' AND '" . $data2 . "'  " . $qString . $outletFilter . $outletsFilter . "  GROUP BY salesMaster.paymentMethod";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_paymentMethod2($date, $data2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND payments.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                    configMaster.description as paymentDescription,
                    SUM(payments.amount) AS NetTotal,
                     count(payments.menuSalesPaymentID) as countTransaction
                FROM
                    srp_erp_pos_menusalespayments AS payments 
                 
                LEFT JOIN srp_erp_pos_menusalesmaster AS salesMaster ON payments.menuSalesID = salesMaster.menuSalesID AND payments.wareHouseAutoID = salesMaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID
                WHERE
                 salesMaster.isVoid = 0 AND 
                salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . "
                GROUP BY
                    payments.paymentConfigMasterID, payments.wareHouseAutoID
                ORDER BY payments.paymentConfigMasterID;";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_voidBills($date, $data2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                   'Voided Bills'  AS paymentDescription,
                    SUM(salesMaster.subTotal) AS NetTotal,
                     count(	salesMaster.menuSalesID) as countTransaction
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster 
                 
                WHERE
                   salesMaster.isVoid = 1 AND 
                salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter;
        $result = $this->db->query($q)->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_giftCardTopUp($date, $data2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND giftCard.createdUserID IN(" . $cashier . ") ";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND giftCard.outletID = '" . $outletID . "' ";
        }

        $q = "SELECT
                    configMaster.description as paymentDescription,
                    SUM(giftCard.topUpAmount) AS topUpTotal,
                     count(giftCard.cardTopUpID) as countTopUp
                FROM
                    srp_erp_pos_cardtopup AS giftCard
                 
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = giftCard.glConfigMasterID
                WHERE
                  giftCard.companyID = '" . current_companyID() . "'
                AND giftCard.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . " AND (giftCard.topUpAmount>0 OR giftCard.isRefund=1)
                GROUP BY
                    configMaster.autoID";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_itemizedSalesReport($dateFrom, $dateTo, $Outlets = null, $cashier = null, $orderTypes = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }

        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }

        $q = "SELECT
                    menuMaster.menuMasterID,
                    menuMaster.menuMasterDescription,
                    menuCategory.menuCategoryID,
                    menuCategory.menuCategoryDescription,
                    sum( salesItem.salesPriceAfterDiscount ) AS itemPriceTotal,
                    sum( salesItem.qty ) AS qty,
                    size.description AS menuSize 
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                    LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID AND salesMaster.wareHouseAutoID = salesItem.id_store
                    LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                    LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                    LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                    LEFT JOIN srp_erp_pos_menusize size ON size.menuSizeID = menuMaster.menuSizeID 
                WHERE
                    salesMaster.isVoid = 0 
                    AND salesMaster.isHold = 0 
                    AND salesMaster.companyID = " . current_companyID() . " 
                    AND salesMaster.createdDateTime BETWEEN '" . $dateFrom . "' 
                    AND '" . $dateTo . "' 
                    AND menuMaster.menuMasterID IS NOT NULL " . $outletFilter . $qString . $outletsFilter . $orderFilter . " 
                GROUP BY
                    menuMaster.menuMasterID, salesItem.id_store
                ORDER BY
                    menuCategory.menuCategoryID";


        $result = $this->db->query($q)->result_array();
        // echo $this->db->last_query();


        return $result;
    }


    function get_srp_erp_pos_menudetails_by_menuMasterID($menuMasterID)
    {
        $this->db->select("menu.*,item.costGLAutoID, assteGLAutoID as assetGLAutoID");
        $this->db->from("srp_erp_pos_menudetails menu");
        $this->db->join("srp_erp_itemmaster item", "item.itemAutoID = menu.itemAutoID", "LEFT");
        $this->db->where('menuMasterID', $menuMasterID);
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();exit;
        return $result;
    }

    function batch_insert_srp_erp_pos_menusalesitemdetails($data)
    {
        $this->db->insert_batch('srp_erp_pos_menusalesitemdetails', $data);
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
                    item.menuSalesID = " . $menusSalesID . " AND item.menuSalesID = " . current_warehouseID() . " 
                GROUP BY
                    revenueGLAutoID";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_packGroup_menuItem($packMenuID)
    {
        $path = base_url();
        $q = "SELECT
                    pd.packgroupdetailID AS id,
                    pd.menuID AS menuID,
                    mgm.IsRequired AS isRequired,
                    menuMaster.menuMasterDescription,
                    mgm.groupMasterID as menuCategoryID,
                    concat(
                        '" . $path . "',
                        menuMaster.menuImage
                    ) AS menuImage,
                    mgm.description as menuCategoryDescription,
                    pd.PackMenuID
                FROM
                    srp_erp_pos_packgroupdetail pd
                LEFT JOIN srp_erp_pos_menupackgroupmaster mgm ON mgm.groupMasterID = pd.groupMasterID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = pd.menuID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                LEFT JOIN srp_erp_pos_menupackitem mpi ON mpi.menuPackItemID = pd.menuPackItemID
                WHERE
                    pd.packMenuID = '" . $packMenuID . "' AND pd.isActive=1 ORDER BY pd.groupMasterID ";
        /*  $this->db->select("packItem.menuPackItemID as id, packItem.menuID as menuID, packItem.isRequired as isRequired, menuMaster.menuMasterDescription, packItem.menuCategoryID ,  concat('".$path."',menuMaster.menuImage) as menuImage, menuCategory.menuCategoryDescription, packItem.PackMenuID");
          $this->db->from("srp_erp_pos_menupackitem packItem");
          $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = packItem.menuID", "left");
          $this->db->join("srp_erp_pos_menucategory menuCategory", "menuCategory.menuCategoryID = packItem.menuCategoryID", "left");
          $this->db->where('menuMaster.isDeleted', 0);
          $this->db->where('packItem.PackMenuID', $packMenuID);
          $this->db->order_by('packItem.isRequired', 'DESC');
          $this->db->order_by('menuMaster.menuCategoryID ', 'ASC');
          $this->db->order_by('menuMaster.menuMasterDescription ', 'ASC');*/

        //$result = $this->db->get()->result_array();
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function update_srp_erp_pos_updaterestaurantTable($data, $id)
    {
        $data['is_sync'] = 0;
        $this->db->where('menuSalesID', $id);
        $this->db->where('wareHouseAutoID', current_warehouseID());
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        return $result;
    }

    function saveMenuSalesItemRemarkes($data, $menuSalesItemID, $menuSalesID)
    {
        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->where('menuSalesItemID', $menuSalesItemID);
        $result = $this->db->update('srp_erp_pos_menusalesitems', $data);
        return $result;
    }

    function get_add_on_list($menuSalesItemID)
    {
        $cmpid = current_companyID();
        $result = $this->db->query("SELECT * from srp_erp_pos_menumaster WHERE companyID = '$cmpid' AND isAddOn= 1 AND isDeleted=0 AND menuStatus=1 ;")->result_array();
        return $result;
    }

    function saveAddon()
    {
        $menuSalesID = $this->input->post('invoiceIDMenusales');
        if (!empty($this->input->post('menuItemRemarkes'))) {
            $menuSalesItemID = $this->input->post('itmID');
            $menuItemRemarkes = $this->input->post('menuItemRemarkes');

            if (!empty($menuItemRemarkes)) {
                $data['remarkes'] = $menuItemRemarkes;

                $this->db->where('menuSalesID', $menuSalesID);
                $this->db->where('menuSalesItemID', $menuSalesItemID);
                $this->db->where('id_store', current_warehouseID());
                $result = $this->db->update('srp_erp_pos_menusalesitems', $data);
            }
        }

        $menuSalesItemIDaddon = $this->input->post('menuSalesItemIDaddon');
        $results = $this->db->delete('srp_erp_pos_addon', array('menuSalesItemID' => $menuSalesItemIDaddon));
        if (!empty($this->input->post('addonCheck'))) {
            foreach ($this->input->post('addonCheck') as $val) {
                $this->db->set('menuSalesItemID', $this->input->post('menuSalesItemIDaddon'));
                $this->db->set('menuMasterID', $val);

                $result = $this->db->insert('srp_erp_pos_addon');
            }
        }
        if ($result) {
            return array('s', 'Add On Added Successfully', $menuSalesID);
        } else {
            return array('e', 'Error In Adding Add On');
        }
    }

    function updateQty()
    {
        $menuSalesItemID = $this->input->post('menuSalesItemID');
        $qty = $this->input->post('qty');
        $outletID = $this->input->post('outletID');
        if ($outletID > 0) {
            $outletID = get_outletID();
        }

        $data['qty'] = $qty;

        $this->db->where('menuSalesItemID', $menuSalesItemID);
        $this->db->where('id_store', get_outletID());
        $result = $this->db->update('srp_erp_pos_menusalesitems', $data);
        if ($result) {
            return array('s', 'QTY Updated Successfully');
        } else {
            return array('e', 'Error In Updating QTY');
        }
    }


    function get_customerInfo($customerID)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_customers");
        $this->db->where('customerID', $customerID);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function save_send_pos_email()
    {
        /**/
        $this->load->library('email_manual');
        $sen = 0;
        $comapnyemail = $this->pos_policy->isCompanyEmail();
        $companyid = current_companyID();

        $this->db->select("company_email");
        $this->db->from("srp_erp_company");
        $this->db->where('company_id', $companyid);
        $company_email = $this->db->get()->row_array();
        $compmail = $company_email['company_email'];

        $invoiceID = isPos_invoiceSessionExist();
        $invoiceID = $this->input->post('invoiceID');
        $invoice = $this->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $masters = $this->get_srp_erp_pos_menusalesmaster($invoiceID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $msg = $this->load->view('system/pos/email/restaurant-pos-dotmatric-email', $data, true);
        $emal = $this->input->post('emailAddress');

        $toEmail = $emal;
        $subject = 'Receipt';
        $from = (hstGeras == 1) ? 'noreply@redberylit.com' : 'noreply@redberylit.com';
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['wordwrap'] = TRUE;
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp.sendgrid.net';
        $config['smtp_user'] = 'apikey';
        $config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
        $config['smtp_crypto'] = 'tls';
        $config['smtp_port'] = '587';
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $this->load->library('email', $config);

        $this->email->from($from, EMAIL_SYS_NAME);
        $this->email->to($toEmail);
        if (!empty($compmail)) {
            $this->email->cc($compmail);
        }
        $this->email->subject($subject);
        $this->email->message($this->load->view('system/pos/email/restaurant-pos-dotmatric-email', $data, true));

        if ($sen == 0) {
            $sen = $sen++;
            $result = $this->email->send();
        }
        if (!$result) {
            echo json_encode(array("error" => 1, "message" => "Mailer Error"));
        } else {
            echo json_encode(array("error" => 0, "message" => "Mail sent"));
        }
    }

    function get_srp_erp_pos_menusalesitems_all()
    {
        $this->db->select("salesItem.*,warehouseMenu.menuMasterID,warehouseCategory.menuCategoryID");
        $this->db->from("srp_erp_pos_menusalesitems salesItem");
        $this->db->join("srp_erp_pos_warehousemenumaster warehouseMenu", "warehouseMenu.warehouseMenuID = salesItem.warehouseMenuID", "INNER");
        $this->db->join("srp_erp_pos_warehousemenucategory warehouseCategory", "warehouseCategory.autoID = salesItem.warehouseMenuCategoryID", "inner");

        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_srp_erp_pos_menusalesmaster_all()
    {
        $this->db->select('menuSales .*, cType.customerDescription');
        $this->db->from('srp_erp_pos_menusalesmaster menuSales');
        $this->db->join('srp_erp_customertypemaster cType', 'cType.customerTypeID = menuSales.customerTypeID', 'left'); /*customerTypeID*/
        $result = $this->db->get()->result_array();
        return $result;
    }

    function void_bill()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $isClosed = $this->db->query("SELECT srp_erp_pos_shiftdetails.isClosed FROM srp_erp_pos_menusalesmaster 
        join srp_erp_pos_shiftdetails on srp_erp_pos_shiftdetails.shiftID=srp_erp_pos_menusalesmaster.shiftID
        where menuSalesID=$invoiceID")->row('isClosed');
        if ($isClosed == "0") {
            $isAlreadyVoid = $this->db->query("SELECT isVoid FROM srp_erp_pos_menusalesmaster WHERE menuSalesID=$invoiceID")->row('isVoid');
            if ($isAlreadyVoid == 1) {
                return array('e', 'Already voided');
            } else {
                $outletID = get_outletID();
                $data['isVoid'] = 1;
                $data['voidBy'] = current_user();
                $data['voidDatetime'] = current_date();
                $data['is_sync'] = 0;
                $this->db->where('menuSalesID', $this->input->post('menuSalesID'));
                $this->db->where('wareHouseAutoID', $outletID);
                $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
                if ($result) {
                    return array('s', 'Record Updated successfully');
                }
            }
        } elseif ($isClosed == "1") {
            return array('e', 'Cannot void bill in a closed session');
        }
    }

    function un_void_bill()
    {
        $outletID = get_outletID();
        $data['isVoid'] = 0;
        $data['voidBy'] = null;
        $data['voidDatetime'] = null;
        $this->db->where('menuSalesID', $this->input->post('menuSalesID'));
        $this->db->where('wareHouseAutoID', $outletID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        if ($result) {
            return array('s', 'Record Updated successfully');
        }
    }

    function get_deliveryPersonReport($dateFrom, $dateTo, $customerID, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND srp_erp_pos_menusalesmaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND srp_erp_pos_menusalesmaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND srp_erp_pos_menusalesmaster.createdUserID IN(" . $cashier . ") ";
        }

        if ($customerID) {
            $customerID = join(',', $customerID);
            $companyID = current_companyID();
            $q = "SELECT
                        menuSalesID,
                        invoiceCode,
                        DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, srp_erp_pos_menusalesmaster.createdDateTime), ' %d-%m-%Y') AS billdate,
                        DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryTime, srp_erp_pos_menusalesmaster.createdDateTime), ' %h-%i %p') AS billtime,
                        netTotal,
                        deliveryCommission,
                        subTotal,
                        deliveryCommissionAmount,
                        netTotal * (deliveryCommission/100) AS CommissionAmount,
                    srp_erp_pos_customers.customerName
                    FROM
                        srp_erp_pos_menusalesmaster
                    JOIN srp_erp_pos_customers on srp_erp_pos_menusalesmaster.deliveryPersonID = srp_erp_pos_customers.customerID
                    LEFT JOIN srp_erp_pos_deliveryorders delivery ON srp_erp_pos_menusalesmaster.menuSalesID = delivery.menuSalesMasterID
                    WHERE
                        deliveryPersonID IN($customerID)
                        AND srp_erp_pos_menusalesmaster.companyID = $companyID
                    AND isHold = 0
                    AND isVoid = 0
                    AND DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, srp_erp_pos_menusalesmaster.createdDateTime), '%Y-%m-%d') BETWEEN '$dateFrom'   AND '$dateTo' " . $outletFilter . $qString . $outletsFilter;
            $result = $this->db->query($q)->result_array();

            // echo $this->db->last_query();

            return $result;
        } else {
        }
    }

    function get_discountReport($dateFrom, $dateTo, $customerID, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND srp_erp_pos_menusalesmaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND srp_erp_pos_menusalesmaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND srp_erp_pos_menusalesmaster.createdUserID IN(" . $cashier . ") ";
        }


        $generalDiscount = false;
        if (!empty($customerID)) {
            foreach ($customerID as $key => $discountID) {
                if ($discountID == -1) {
                    unset($customerID[$key]);
                    $generalDiscount = true;
                }
            }
        }


        $whereSQL = '';

        if (!empty($customerID)) {
            $customerID = join(',', $customerID);
            $whereSQL .= 'AND (';
            $whereSQL .= ' promotionID IN (' . $customerID . ') ';
            if ($generalDiscount) {
                $whereSQL .= ' OR discountPer>0 ';
            }
            $whereSQL .= ')';
        } else {
            if ($generalDiscount) {
                $whereSQL .= ' AND discountPer>0 ';
            } else {
                $whereSQL .= ' AND discountPer=-1 ';
            }
        }

        $companyID = current_companyID();
        $q = "SELECT
                    menuSalesID,
                    invoiceCode,
                    DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, srp_erp_pos_menusalesmaster.createdDateTime), '%d-%m-%Y' ) AS billdate,
                    DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryTime, srp_erp_pos_menusalesmaster.createdDateTime), '%h:%i %p' ) AS billtime,
                    subTotal + discountAmount + promotionDiscountAmount AS grossTotal,
                    subTotal AS netTotal,
                    discountPer AS generalDiscount,
                    discountAmount AS generalDiscountAmount,
                    promotionDiscount,
                    promotionDiscountAmount,
                    srp_erp_pos_customers.customerName  AS discountTypes,
                    srp_erp_pos_customers.customerName 
                FROM
                    srp_erp_pos_menusalesmaster
                    LEFT JOIN srp_erp_pos_customers ON srp_erp_pos_menusalesmaster.promotionID = srp_erp_pos_customers.customerID 
                    LEFT JOIN srp_erp_pos_deliveryorders delivery ON srp_erp_pos_menusalesmaster.menuSalesID = delivery.menuSalesMasterID
                WHERE
                    srp_erp_pos_menusalesmaster.companyID = $companyID 
                    AND isHold = 0 
                    AND isVoid = 0 
                    AND IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, srp_erp_pos_menusalesmaster.createdDateTime) BETWEEN " . "'" . $dateFrom . "'   AND '" . $dateTo . "' " . $outletFilter . $qString . $outletsFilter . $whereSQL . "
                    ORDER BY
	                    menuSalesID ASC";

        $result = $this->db->query($q)->result_array();

        //echo $this->db->last_query();

        return $result;
    }

    function get_paymentCollection($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND msm.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND msm.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND msm.createdUserID IN(" . $cashier . ") ";
        }

        $paymentList = $this->Pos_config_model->get_payment_list_for_reports($Outlets);
        $paymentListStringArray = array();
        $selectColumnArray = array();
        foreach ($paymentList as $item) {

            $tempStr = "sum( CASE WHEN paymentConfigMasterID = '" . $item['autoID'] . "' THEN amount ELSE 0 END ) " . str_replace(' ', '', $item['description']);
            $selectColumnTemp = "msp_tmp." . str_replace(' ', '', $item['description']);
            array_push($paymentListStringArray, $tempStr);
            array_push($selectColumnArray, $selectColumnTemp);
        }
        $commaSeperatedPaymentListString = implode(",", $paymentListStringArray);
        $selectColumnString = implode(",", $selectColumnArray);
        $companyID = current_companyID();
        $q = "SELECT
                    msm.wareHouseAutoID,
                    msm.menuSalesID,
                    msm.invoiceCode,
                    DATE_FORMAT( msm.createdDateTime, '%d-%m-%Y' ) AS billdate,
                    delivery.deliveryDate AS deliveryDate,
                    msp_tmp.paymentDate AS paymentDate,
                    msm.subTotal AS billAmount,
                    msm.isHold AS isHold,
                    IF ( msm.isHold = 0, 1, 0 ) AS DispatchedYN,
                    msp_tmp.amountPaid,
                    otype.customerDescription,
                    pcm_description AS paidType,
                    msp_tmp.paymentConfigMasterID,
                            msp_tmp.amountPaid,
                            msp_tmp.customerAutoID, 
                            $selectColumnString,
                            
                    concat( cm.customerName, \" - \", delivery.phoneNo ) AS customerInfo,
                    ( msm.subTotal - IFNULL( msp_tmp.amountPaid, 0 ) ) AS balance 
                FROM
                    srp_erp_pos_menusalesmaster msm
                    INNER JOIN (
                SELECT
                    paymentConfigMasterID,
                    sum(amount) as amountPaid,
                    menuSalesID,
                    msp.customerAutoID,
                    srp_erp_customermaster.customerName,
                    srp_erp_customermaster.customerTelephone,
                    GROUP_CONCAT( pcm.description ) AS pcm_description,
                    DATE_FORMAT( msp.createdDateTime, '%d-%m-%Y' ) AS paymentDate,
                     $commaSeperatedPaymentListString
                                        ,wareHouseAutoID
                FROM
                    srp_erp_pos_menusalespayments AS msp
                    LEFT JOIN srp_erp_pos_paymentglconfigmaster pcm ON pcm.autoID = msp.paymentConfigMasterID
                    LEFT JOIN srp_erp_customermaster ON msp.customerAutoID = srp_erp_customermaster.customerAutoID 
                WHERE
                    msp.createdDateTime BETWEEN '" . $dateFrom . "' 
                    AND '" . $dateTo . "' 
                GROUP BY
                    menuSalesID, wareHouseAutoID 
                    ) AS msp_tmp ON msp_tmp.menuSalesID = msm.menuSalesID
                    LEFT JOIN srp_erp_pos_deliveryorders delivery ON delivery.menuSalesMasterID = msm.menuSalesID
                    LEFT JOIN srp_erp_pos_customermaster cm ON cm.posCustomerAutoID = delivery.posCustomerAutoID
                    LEFT JOIN srp_erp_customertypemaster otype ON otype.customerTypeID = msm.customerTypeID 
                WHERE
                    msm.companyID = '" . $companyID . "' 
                    AND ( ( delivery.deliveryDate IS NOT NULL ) OR ( msm.isHold = 0 AND delivery.deliveryDate IS NULL ) ) 
                    AND msm.isVoid = 0 
                    " . $qString . "
                    " . $outletsFilter . "
                GROUP BY
                    msm.menuSalesID
                ORDER BY
                    msm.menuSalesID DESC";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_deliveryPerson($customerID)
    {
        $q = "SELECT
                    customerName
                FROM
                    srp_erp_pos_customers
                WHERE
                    customerID = $customerID
                     ";
        $result = $this->db->query($q)->row_array();

        //echo $this->db->last_query();

        return $result;
    }

    function batch_get_srp_erp_pos_menusalesmaster_all($limit = 10)
    {
        $this->db->select(' * ');
        $this->db->from('srp_erp_pos_menusalesmaster menuSales');
        $this->db->where('isHold', 0);
        $this->db->limit($limit);
        $result = $this->db->get()->result_array();

        return $result;
    }

    function batch_update_srp_erp_pos_menusalesmaster($data)
    {
        $this->db->update_batch('srp_erp_pos_menusalesmaster', $data, 'menuSalesID');
        $row = $this->db->affected_rows();
        return $row;
    }


    function get_srp_erp_pos_menusalesitems_byMenusalesID($menuSalesID)
    {
        $this->db->select(' * ');
        $this->db->from('srp_erp_pos_menusalesitems');
        $this->db->where('menuSalesID', $menuSalesID);
        $result = $this->db->get()->result_array();

        return $result;
    }


    function get_productMixPacks_sales($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $q = "SELECT valuePack.menuID,  menuMaster.menuMasterDescription, sum(valuePack.qty) AS qty, mSize.description AS menuSize   
            FROM srp_erp_pos_menusalesmaster salesMaster 
            LEFT JOIN  srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
            LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
            LEFT JOIN srp_erp_pos_valuepackdetail valuePack ON valuePack.menuSalesItemID = salesItem.menuSalesItemID
            LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = valuePack.menuID
            LEFT JOIN srp_erp_pos_menusize mSize ON mSize.menuSizeID = menuMaster.menuSizeID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND DATE_FORMAT(
                salesMaster.menuSalesDate,
                '%Y-%m-%d'
            ) BETWEEN '" . $dateFrom . "'
            AND '" . $dateTo . "'
            AND menuMaster.menuMasterID IS NOT NULL
            AND salesMaster.companyID = " . current_companyID() . "
            " . $outletFilter . "
            " . $outletsFilter . "
            " . $qString . "
            GROUP BY
                valuePack.menuID
            ORDER BY
                menuMaster.menuMasterDescription";
        $result = $this->db->query($q)->result_array();

        //echo '<div class="hide"> '.$q.'</div> ';

        return $result;
    }

    function productMix_menuItem($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $q = "SELECT menuMaster.menuMasterID, menuMaster.menuMasterDescription,  sum( salesItem.salesPriceNetTotal ) AS itemPriceTotal, sum(salesItem.qty) AS qty, mSize.description AS menuSize
            FROM
                srp_erp_pos_menusalesmaster salesMaster
            LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
            LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
            LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
            LEFT JOIN srp_erp_pos_menusize mSize ON mSize.menuSizeID = menuMaster.menuSizeID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = " . current_companyID() . "
            AND menuMaster.isPack = 0
            AND DATE_FORMAT(
                salesMaster.menuSalesDate,
                '%Y-%m-%d'
            ) BETWEEN '" . $dateFrom . "'
            AND '" . $dateTo . "'
            AND menuMaster.menuMasterID IS NOT NULL
            " . $outletFilter . "
            " . $outletsFilter . "
            " . $qString . "
            GROUP BY
                menuMaster.menuMasterID
            ORDER BY
                menuMaster.menuMasterDescription";

        $result = $this->db->query($q)->result_array();
        //echo '<span class="hide"> '.$q.'</span> ';

        return $result;
    }

    function get_franchiseReport($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $companyID = current_companyID();
        $q = "SELECT
            salesMaster.salesDay,
            salesMaster.menuSalesDate,
            Customer.customerDescription,
            SUM(
                IF (
                    (Customer.customerDescription = 'Eat-in' OR Customer.customerDescription = 'dine-in' ),
                    (salesMaster.subTotal ),
                    0
                )
            ) AS EatInTotal,
            SUM(
        
                IF (
                    (Customer.customerDescription = 'Eat-in' OR Customer.customerDescription = 'dine-in' ),
                    1,
                    0
                )
            ) AS EatInQty,
        
            SUM(
                IF (
                    (Customer.customerDescription = 'Take-away' || Customer.customerDescription = 'Direct sale'),
                    (salesMaster.subTotal ),
                    0
                )
            ) AS TakeAwayTotal,
            SUM(
        
                IF (
                    (Customer.customerDescription = 'Take-away' || Customer.customerDescription = 'Direct sale'),
                    1,
                    0
                )
            ) AS TakeAwayQty,
            SUM(
                IF (
                    Customer.customerDescription = 'Delivery Orders',
                    (salesMaster.subTotal ),
                    0
                )
            ) AS DeliveryOrdersTotal,
            SUM(
        
                IF (
                    Customer.customerDescription = 'Delivery Orders',
                    1,
                    0
                )
            ) AS DeliveryOrdersQty,
            sum(salesMaster.subTotal) AS NetTotal,
            sum(salesMaster.totalTaxAmount) AS totalTax,
            count(salesMaster.menuSalesID) AS netQty
        FROM
            srp_erp_pos_menusalesmaster AS salesMaster
        LEFT JOIN srp_erp_customertypemaster AS Customer ON Customer.customerTypeID = salesMaster.customerTypeID
        WHERE
            salesMaster.isVoid = 0
        AND salesMaster.isHold = 0
        AND salesMaster.companyID = '$companyID'
        AND DATE_FORMAT(
            salesMaster.menuSalesDate,
            '%Y-%m-%d'
        ) BETWEEN '$dateFrom'
        AND '$dateTo'
        " . $outletFilter . "
        " . $outletsFilter . "
        " . $qString . "
        GROUP BY
            salesMaster.menuSalesDate
        ORDER BY
            salesMaster.menuSalesDate ASC ";
        $result = $this->db->query($q)->result_array();

        //echo $this->db->last_query();

        return $result;
    }

    function updateCurrentMenuWAC()
    {
        $companyID = current_companyID();
        $outletID = get_outletID();
        $this->db->select("*");
        $this->db->from("srp_erp_company");
        $this->db->where("company_id", $companyID);
        $company = $this->db->get()->row_array();
        if (!empty($company) && $company['pos_isFinanceEnables'] == 1 && false) { // WAC UPDATE stopped by Hisham
            $this->db->select('*');
            $this->db->from('srp_erp_pos_wac_updatehistory');
            $this->db->where('companyID', $companyID);
            $this->db->where('updatedDate', current_date(false));
            $result = $this->db->get()->row_array();

            if (empty($result)) {
                /** insert to history */
                $data['companyID'] = $companyID;
                $data['updatedDate'] = current_date();
                $data['timestamp'] = format_date_mysql_datetime();
                $this->db->insert('srp_erp_pos_wac_updatehistory', $data);

                /** update WAC in menusales detail */
                $q = "UPDATE srp_erp_pos_menudetails AS tmpMenuDetails,
                     (
                        SELECT
                            menudetails.menuDetailID,
                            menudetails.menuDetailDescription,
                            menudetails.cost,
                            menudetails.actualInventoryCost,
                            menudetails.UOM AS uom_pos,
                            unitOfMeasure.UnitID AS uomID_pos,
                            itemmaster.defaultUnitOfMeasure AS uom,
                            itemmaster.defaultUnitOfMeasureID AS uomID,
                            ABS(
                                itemmaster.companyLocalWacAmount
                            ) AS companyLocalWacAmount,
                    
                        IF ( unitOfMeasure.UnitID = itemmaster.defaultUnitOfMeasureID,  1, ( SELECT conversion FROM srp_erp_unitsconversion WHERE masterUnitID = unitOfMeasure.UnitID AND subUnitID = itemmaster.defaultUnitOfMeasureID ) ) AS conversion,
                        menudetails.qty AS Qty,
                    
                    IF ( unitOfMeasure.UnitID = itemmaster.defaultUnitOfMeasureID,
                            menudetails.qty * ( ABS( itemmaster.companyLocalWacAmount ) ),
                         ( SELECT conversion FROM srp_erp_unitsconversion WHERE masterUnitID = unitOfMeasure.UnitID
                            AND subUnitID = itemmaster.defaultUnitOfMeasureID ) * menudetails.qty * ( ABS( itemmaster.companyLocalWacAmount ) ) 
                    ) 
                    AS newWAC
                    FROM
                        srp_erp_pos_menudetails menudetails
                    LEFT JOIN srp_erp_pos_menumaster menumaster ON menudetails.menuMasterID = menumaster.menuMasterID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = menudetails.itemAutoID
                    LEFT JOIN srp_erp_unit_of_measure unitOfMeasure ON unitOfMeasure.UnitShortCode = menudetails.UOM
                    WHERE
                        menumaster.companyID = '" . $companyID . "'
                    AND unitOfMeasure.companyID = '" . $companyID . "'
                    AND menudetails.isYield = 0
                    )
                     tmpOutput
                    SET tmpMenuDetails.actualInventoryCost = tmpMenuDetails.cost,
                     tmpMenuDetails.cost = tmpOutput.newWAC
                    WHERE tmpOutput.menuDetailID = tmpMenuDetails.menuDetailID  AND tmpOutput.newWAC != tmpMenuDetails.cost";
                $this->db->query($q);

                /** update the menu cost */
                $q2 = "UPDATE srp_erp_pos_menumaster AS menumasterTmp,
                         (
                            SELECT
                                menumaster.menuMasterID,
                                menumaster.menuCost AS menuCost,
                                SUM(menudetails.cost) AS menuCostSum
                            FROM
                                srp_erp_pos_menumaster AS menumaster
                            LEFT JOIN srp_erp_pos_menudetails AS menudetails ON menumaster.menuMasterID = menudetails.menuMasterID
                            WHERE
                                menudetails.cost IS NOT NULL
                            AND menumaster.companyID = '" . $companyID . "'
                            GROUP BY
                                menumaster.menuMasterID
                            HAVING
                                menuCost <> menuCostSum
                        ) subList
                        SET menumasterTmp.menuCost = subList.menuCostSum
                        WHERE
                            menumasterTmp.menuMasterID = subList.menuMasterID
                        AND menumasterTmp.companyID = '" . $companyID . "'
                        AND menumasterTmp.menuCost <> subList.menuCostSum";
                $this->db->query($q2);

                outlet_generateMissingItemsToOutlets($companyID, $outletID);

                return array('error' => 0, 'message' => 'WAC & Item Master for outlet  updated successfully', 'output' => $result);
            } else {
                return array('error' => 1, 'message' => 'WAC & Item Master already updated for this day');
            }
        } else {
            return array('error' => 1, 'message' => 'Finance not enabled for this company.');
        }
    }

    //    function update_menuSalesTax()
    //    {
    //        $invoiceID = isPos_invoiceSessionExist();
    //        $outletID = get_outletID();
    ////var_dump($invoiceID);exit;
    ////var_dump($this->input->post());exit;
    //
    //        if ($invoiceID) {
    //            $totalPaid = 0;
    //
    //            $isConfirmedDeliveryOrder = pos_isConfirmedDeliveryOrder($invoiceID);
    //
    //            $createdUserGroup = user_group();
    //            $createdPCID = current_pc();
    //            $createdUserID = current_userID();
    //            $createdUserName = current_user();
    //            $createdDateTime = format_date_mysql_datetime();
    //            $timestamp = format_date_mysql_datetime();
    //            $companyID = current_companyID();
    //            $companyCode = current_company_code();
    //
    //            $masterData = get_pos_invoice_id($invoiceID);
    //
    //
    //            $reference = $this->input->post('referenceUpdate');
    //            $customerAutoIDs = $this->input->post('customerAutoIDUpdate');
    //            $paymentTypes = $this->input->post('paymentTypesUpdate');
    //            $cardTotalAmount = $this->input->post('cardTotalAmountUpdate');
    //            $netTotalAmount = $this->input->post('netTotalAmountUpdate');
    //            $isDelivery = $this->input->post('isDeliveryUpdate');
    //            $isOnTimePayment = $this->input->post('isOnTimePaymentUpdate');
    //            $payableDeliveryAmount = $this->input->post('totalPayableAmountDelivery_idUpdate');
    //            $returnChange = $this->input->post('returned_changeUpdate');
    //            $grossTotal = $this->input->post('total_payable_amtUpdate');
    //
    //            if (!empty($paymentTypes)) {
    //                $i = 0;
    //               // print_r($paymentTypes);exit;
    //                foreach ($paymentTypes as $key => $amount) {
    //
    //                    if ($amount > 0) {
    //
    //
    //
    //                        $totalPaid += $amount;
    //                        $this->db->select('configDetail.GLCode,configMaster.autoID, configMaster.glAccountType');
    //                        $this->db->from('srp_erp_pos_paymentglconfigdetail configDetail');
    //                        $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configDetail.paymentConfigMasterID = configMaster.autoID', 'left');
    //                        $this->db->where('configDetail.ID', $key);
    //                        $r = $this->db->get()->row_array();
    //
    //                        if ($r['glAccountType'] == 1) {
    //
    //                            /** Cash Payment */
    //                            // print_r($isDelivery);
    //                            // print_r($isOnTimePayment);
    //                            // print_r('-------------');exit;
    //                            if ($isDelivery == 1 && $isOnTimePayment == 1) {
    //                                $cashPaidAmount = $payableDeliveryAmount - $cardTotalAmount;
    //                            } else {
    //                                $cashPaidAmount = $netTotalAmount - $cardTotalAmount;
    ////                                 print_r("netTotalAmount");
    ////                                 print_r($netTotalAmount);
    //// print_r("cardTotalAmount");
    ////                                 print_r($cardTotalAmount);
    //// print_r("cashPaidAmount");
    ////                                print_r($cashPaidAmount);exit;
    //                                if ($isConfirmedDeliveryOrder) {
    //                                    $advancePayment = get_paidAmount($invoiceID);
    //
    //                                    //$payable = $netTotalAmount - ($advancePayment + $cardTotalAmount); bug because of this.
    //                                    $payable = $grossTotal - ($advancePayment + $cardTotalAmount);
    //
    //
    //                                    if ($amount == $payable) {
    //                                        $cashPaidAmount = $amount;
    //                                        $returnChange = 0;
    //                                    } else if ($amount > $payable) {
    //                                        $cashPaidAmount = $payable;
    //                                        $returnChange = $amount - $payable;
    //
    //
    //                                    } else {
    //
    //                                        /** Advance payment */
    //                                        $cashPaidAmount = $amount;
    //                                        $returnChange = 0;
    //                                    }
    //                                }
    //                            }
    //
    //                            $amount = $cashPaidAmount;
    //
    //
    //                        }
    //
    //                        /** Credit Customer's GL Code should be picked from Customer */
    //                        $GLCode = null;
    //                        if ($r['autoID'] == 7) {
    //                            if (isset($customerAutoIDs[$key]) && $customerAutoIDs[$key]) {
    //                                $receivableAutoID = $this->db->select('receivableAutoID')
    //                                    ->from('srp_erp_customermaster')
    //                                    ->where('customerAutoID', $customerAutoIDs[$key])
    //                                    ->get()->row('receivableAutoID');
    //                                $GLCode = $receivableAutoID;
    //                            }
    //                        }
    //
    //                        $paymentData[$i]['menuSalesID'] = $invoiceID;
    //                        $paymentData[$i]['wareHouseAutoID'] = $outletID;
    //                        $paymentData[$i]['paymentConfigMasterID'] = $r['autoID'];
    //                        $paymentData[$i]['paymentConfigDetailID'] = $key;
    //                        $paymentData[$i]['GLCode'] = $r['autoID'] == 7 ? $GLCode : $r['GLCode'];
    //                        $paymentData[$i]['glAccountType'] = $r['glAccountType'];
    //                        $paymentData[$i]['amount'] = $amount;
    //                        $paymentData[$i]['reference'] = isset($reference[$key]) ? $reference[$key] : null;
    //                        $paymentData[$i]['customerAutoID'] = isset($customerAutoIDs[$key]) ? $customerAutoIDs[$key] : null;
    //
    //                        /*Common Data*/
    //                        $paymentData[$i]['createdUserGroup'] = $createdUserGroup;
    //                        $paymentData[$i]['createdPCID'] = $createdPCID;
    //                        $paymentData[$i]['createdUserID'] = $createdUserID;
    //                        $paymentData[$i]['createdUserName'] = $createdUserName;
    //                        $paymentData[$i]['createdDateTime'] = $createdDateTime;
    //                        $paymentData[$i]['timestamp'] = $timestamp;
    //
    //                        if ($r['autoID'] == 25) {
    //                            $data_JA['menuSalesID'] = $invoiceID;
    //                            $data_JA['outletID'] = $outletID;
    //                            $data_JA['appPIN'] = isset($reference[$key]) ? $reference[$key] : null;;
    //                            $data_JA['amount'] = $amount;
    //                            $data_JA['companyID'] = $companyID;
    //                            $data_JA['companyCode'] = $companyCode;
    //                            $data_JA['createdUserGroup'] = $createdUserGroup;
    //                            $data_JA['createdPCID'] = $createdPCID;
    //                            $data_JA['createdUserID'] = $createdUserID;
    //                            $data_JA['createdDateTime'] = $createdDateTime;
    //                            $data_JA['createdUserName'] = $createdUserName;
    //                            $data_JA['timestamp'] = $createdDateTime;
    //
    //                            $this->db->insert('srp_erp_pos_javaappredeemhistory', $data_JA);
    //                        }
    //
    //                        if ($r['autoID'] == 5) {
    //                            $barCode = isset($reference[$key]) ? $reference[$key] : null;
    //                            $cardInfo = get_giftCardInfo($barCode);
    //
    //                            $dta_GC['wareHouseAutoID'] = $outletID;
    //                            $dta_GC['cardMasterID'] = !empty($cardInfo) ? $cardInfo['cardMasterID'] : null;
    //                            $dta_GC['barCode'] = isset($reference[$key]) ? $reference[$key] : null;
    //                            $dta_GC['posCustomerAutoID'] = !empty($cardInfo) ? $cardInfo['posCustomerAutoID'] : null;
    //                            $dta_GC['topUpAmount'] = abs($amount) * -1;
    //                            $dta_GC['points'] = 0;
    //                            $dta_GC['glConfigMasterID'] = $r['autoID'];
    //                            $dta_GC['glConfigDetailID'] = $key;
    //                            $dta_GC['menuSalesID'] = $invoiceID;
    //                            $dta_GC['giftCardGLAutoID'] = $r['autoID'] == 7 ? null : $r['GLCode'];
    //                            $dta_GC['outletID'] = $outletID;
    //                            $dta_GC['reference'] = 'redeem barcode ' . $barCode;
    //                            $dta_GC['companyID'] = $companyID;
    //                            $dta_GC['companyCode'] = $companyCode;
    //                            $dta_GC['createdPCID'] = $createdPCID;
    //                            $dta_GC['createdUserID'] = $createdUserID;
    //                            $dta_GC['createdDateTime'] = $createdDateTime;
    //                            $dta_GC['createdUserName'] = $createdUserName;
    //                            $dta_GC['createdUserGroup'] = $createdUserGroup;
    //                            $dta_GC['timestamp'] = $createdDateTime;
    //
    //                            $this->db->insert('srp_erp_pos_cardtopup', $dta_GC);
    //                        }
    //
    //                        $i++;
    //                    }else{
    //                        if ($amount!=null && $amount==0) {
    //                            //echo $key;
    //                            $this->db->select('configDetail.GLCode,configMaster.autoID, configMaster.glAccountType');
    //                            $this->db->from('srp_erp_pos_paymentglconfigdetail configDetail');
    //                            $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configDetail.paymentConfigMasterID = configMaster.autoID', 'left');
    //                            $this->db->where('configDetail.ID', $key);
    //                            $rh = $this->db->get()->row_array();
    //
    //                            $GLCode = null;
    //                            if ($rh['autoID'] == 7) {
    //                                if (isset($customerAutoIDs[$key]) && $customerAutoIDs[$key]) {
    //                                    $receivableAutoID = $this->db->select('receivableAutoID')
    //                                        ->from('srp_erp_customermaster')
    //                                        ->where('customerAutoID', $customerAutoIDs[$key])
    //                                        ->get()->row('receivableAutoID');
    //                                    $GLCode = $receivableAutoID;
    //                                }
    //                            }
    //                            //echo $amount;exit;
    //                            $paymentData[$i]['menuSalesID'] = $invoiceID;
    //                            $paymentData[$i]['wareHouseAutoID'] = $outletID;
    //                            $paymentData[$i]['paymentConfigMasterID'] = $rh['autoID'];
    //                            $paymentData[$i]['paymentConfigDetailID'] = $key;
    //                            $paymentData[$i]['GLCode'] = $rh['autoID'] == 7 ? $GLCode : $rh['GLCode'];
    //                            $paymentData[$i]['glAccountType'] = $rh['glAccountType'];
    //                            $paymentData[$i]['amount'] = 0;
    //                            $paymentData[$i]['reference'] = isset($reference[$key]) ? $reference[$key] : null;
    //                            $paymentData[$i]['customerAutoID'] = isset($customerAutoIDs[$key]) ? $customerAutoIDs[$key] : null;
    //
    //                            /*Common Data*/
    //                            $paymentData[$i]['createdUserGroup'] = $createdUserGroup;
    //                            $paymentData[$i]['createdPCID'] = $createdPCID;
    //                            $paymentData[$i]['createdUserID'] = $createdUserID;
    //                            $paymentData[$i]['createdUserName'] = $createdUserName;
    //                            $paymentData[$i]['createdDateTime'] = $createdDateTime;
    //                            $paymentData[$i]['timestamp'] = $timestamp;
    //                        }
    //                    } // end if
    //                } //end foreach
    //
    //                if (isset($paymentData) && !empty($paymentData)) {
    //                    $this->db->delete('srp_erp_pos_menusalespayments', array('menuSalesID' => $invoiceID));
    //                    $this->db->insert_batch('srp_erp_pos_menusalespayments', $paymentData);
    //
    //                }
    //                if ($totalPaid > 0) {
    //                    $payable = $this->input->post('total_payable_amt');
    //                    //$balancePayable = $totalPaid - ($payable > 0 ? $payable : 0);
    //                    $this->db->update('srp_erp_pos_menusalesmaster', array('cashReceivedAmount' => $totalPaid, 'balanceAmount' => $returnChange, 'is_sync' => 0), array('menuSalesID' => $invoiceID));
    //                }
    //            }
    //
    //
    //            $data['status']=true;
    //            $data['invoice_id']=$invoiceID;
    //            return $data;
    //        } else {
    //            $data['status']=false;
    //            $data['invoice_id']=$invoiceID;
    //            return $data;
    //        }
    //    }

    function update_pos_payments()
    {
        $invoiceID = isPos_invoiceSessionExist();
        $outletID = get_outletID();


        if ($invoiceID) {
            $totalPaid = 0;

            $isConfirmedDeliveryOrder = pos_isConfirmedDeliveryOrder($invoiceID);

            $createdUserGroup = user_group();
            $createdPCID = current_pc();
            $createdUserID = current_userID();
            $createdUserName = current_user();
            $createdDateTime = format_date_mysql_datetime();
            $timestamp = format_date_mysql_datetime();
            $companyID = current_companyID();
            $companyCode = current_company_code();

            $masterData = get_pos_invoice_id($invoiceID);


            $reference = $this->input->post('reference');
            $customerAutoIDs = $this->input->post('customerAutoID');
            $paymentTypes = $this->input->post('paymentTypes');
            $cardTotalAmount = $this->input->post('cardTotalAmount');
            $netTotalAmount = $this->input->post('netTotalAmount'); //netTotal
            $isDelivery = $this->input->post('isDelivery'); //isDelivery
            $isOnTimePayment = $this->input->post('isOnTimePayment');
            $payableDeliveryAmount = $this->input->post('totalPayableAmountDelivery_id');
            $returnChange = $this->input->post('returned_change'); //balanceAmount
            $grossTotal = $this->input->post('total_payable_amt'); //cashReceivedAmount
            $promotional_discount = $this->input->post('promotional_discount');
            $paid = $this->input->post('paid');
            $isOwnDelivery = $this->input->post('isOwnDelivery', true);
            $returned_change_toDelivery = $this->input->post('returned_change_toDelivery', true);


            if (!empty($paymentTypes)) {
                $i = 0;
                //print_r($paymentTypes);exit;
                foreach ($paymentTypes as $key => $amount) {
                    if ($amount > 0) {

                        $totalPaid += $amount;
                        $this->db->select('configDetail.GLCode,configMaster.autoID, configMaster.glAccountType');
                        $this->db->from('srp_erp_pos_paymentglconfigdetail configDetail');
                        $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configDetail.paymentConfigMasterID = configMaster.autoID', 'left');
                        $this->db->where('configDetail.ID', $key);
                        $r = $this->db->get()->row_array();

                        if ($r['glAccountType'] == 1) {

                            /** Cash Payment */
                            if ($isDelivery == 1 && $isOnTimePayment == 1 && $payableDeliveryAmount == 0) {
                                $cashPaidAmount = $paid;
                            } else if ($isDelivery == 1 && $isOnTimePayment == 1 && $netTotalAmount != $paid) {
                                $cashPaidAmount = $payableDeliveryAmount - $paid;
                            } else if ($isDelivery == 1 && $isOnTimePayment == 1) {
                                $cashPaidAmount = $payableDeliveryAmount - $cardTotalAmount;
                            } else if ($isOwnDelivery == 1) {
                                //$cashPaidAmount = $payableDeliveryAmount - $cardTotalAmount;//verify.
                                $cashPaidAmount = $paid; //verify.
                                //var_dump($cardTotalAmount);
                            } else {

                                $cashPaidAmount = $netTotalAmount - $cardTotalAmount;
                                $advancePayment = get_paidAmount($invoiceID);
                                $payable = $netTotalAmount - ($advancePayment + $cardTotalAmount); //bug because of this.
                                //$payable = $grossTotal - ($advancePayment + $cardTotalAmount);
                                //$payable = $paid - ($advancePayment + $cardTotalAmount);//this is in-progress.
                                //                                var_dump('amount');
                                //                                var_dump($amount);
                                //                                var_dump('payable');
                                //                                var_dump($payable);exit;
                                if ($amount == $payable) {
                                    $cashPaidAmount = $amount;
                                    $returnChange = 0;
                                } else if ($amount > $payable) {
                                    $cashPaidAmount = $payable;
                                    $returnChange = $amount - $payable;
                                    if ($promotional_discount > 0) {
                                        $returnChange += $promotional_discount;
                                    }
                                } else {
                                    /** Advance payment */

                                    $cashPaidAmount = $amount;
                                    $returnChange = 0;
                                }
                            }
                            //                            exit;
                            $amount = $cashPaidAmount;
                        }


                        /** Credit Customer's GL Code should be picked from Customer */
                        $GLCode = null;
                        if ($r['autoID'] == 7) {
                            if (isset($customerAutoIDs[$key]) && $customerAutoIDs[$key]) {
                                $receivableAutoID = $this->db->select('receivableAutoID')
                                    ->from('srp_erp_customermaster')
                                    ->where('customerAutoID', $customerAutoIDs[$key])
                                    ->get()->row('receivableAutoID');
                                $GLCode = $receivableAutoID;
                            }
                        }

                        $paymentData[$i]['menuSalesID'] = $invoiceID;
                        $paymentData[$i]['wareHouseAutoID'] = $outletID;
                        $paymentData[$i]['paymentConfigMasterID'] = $r['autoID'];
                        $paymentData[$i]['paymentConfigDetailID'] = $key;
                        $paymentData[$i]['GLCode'] = $r['autoID'] == 7 ? $GLCode : $r['GLCode'];
                        $paymentData[$i]['glAccountType'] = $r['glAccountType'];
                        $paymentData[$i]['amount'] = $amount;
                        $paymentData[$i]['reference'] = isset($reference[$key]) ? $reference[$key] : null;
                        $paymentData[$i]['customerAutoID'] = isset($customerAutoIDs[$key]) ? $customerAutoIDs[$key] : null;

                        /*Common Data*/
                        $paymentData[$i]['createdUserGroup'] = $createdUserGroup;
                        $paymentData[$i]['createdPCID'] = $createdPCID;
                        $paymentData[$i]['createdUserID'] = $createdUserID;
                        $paymentData[$i]['createdUserName'] = $createdUserName;
                        $paymentData[$i]['createdDateTime'] = $createdDateTime;
                        $paymentData[$i]['timestamp'] = $timestamp;

                        if ($r['autoID'] == 25) {
                            $data_JA['menuSalesID'] = $invoiceID;
                            $data_JA['outletID'] = $outletID;
                            $data_JA['appPIN'] = isset($reference[$key]) ? $reference[$key] : null;;
                            $data_JA['amount'] = $amount;
                            $data_JA['companyID'] = $companyID;
                            $data_JA['companyCode'] = $companyCode;
                            $data_JA['createdUserGroup'] = $createdUserGroup;
                            $data_JA['createdPCID'] = $createdPCID;
                            $data_JA['createdUserID'] = $createdUserID;
                            $data_JA['createdDateTime'] = $createdDateTime;
                            $data_JA['createdUserName'] = $createdUserName;
                            $data_JA['timestamp'] = $createdDateTime;

                            $this->db->insert('srp_erp_pos_javaappredeemhistory', $data_JA);
                        }

                        if ($r['autoID'] == 5) {
                            $barCode = isset($reference[$key]) ? $reference[$key] : null;
                            $cardInfo = get_giftCardInfo($barCode);

                            $dta_GC['wareHouseAutoID'] = $outletID;
                            $dta_GC['cardMasterID'] = !empty($cardInfo) ? $cardInfo['cardMasterID'] : null;
                            $dta_GC['barCode'] = isset($reference[$key]) ? $reference[$key] : null;
                            $dta_GC['posCustomerAutoID'] = !empty($cardInfo) ? $cardInfo['posCustomerAutoID'] : null;
                            $dta_GC['topUpAmount'] = abs($amount) * -1;
                            $dta_GC['points'] = 0;
                            $dta_GC['glConfigMasterID'] = $r['autoID'];
                            $dta_GC['glConfigDetailID'] = $key;
                            $dta_GC['menuSalesID'] = $invoiceID;
                            $dta_GC['giftCardGLAutoID'] = $r['autoID'] == 7 ? null : $r['GLCode'];
                            $dta_GC['outletID'] = $outletID;
                            $dta_GC['reference'] = 'redeem barcode ' . $barCode;
                            $dta_GC['companyID'] = $companyID;
                            $dta_GC['companyCode'] = $companyCode;
                            $dta_GC['createdPCID'] = $createdPCID;
                            $dta_GC['createdUserID'] = $createdUserID;
                            $dta_GC['createdDateTime'] = $createdDateTime;
                            $dta_GC['createdUserName'] = $createdUserName;
                            $dta_GC['createdUserGroup'] = $createdUserGroup;
                            $dta_GC['timestamp'] = $createdDateTime;

                            $this->db->insert('srp_erp_pos_cardtopup', $dta_GC);
                        }

                        $i++;
                    } else {
                        if ($amount != null && $amount == 0) {
                            //echo $key;
                            $this->db->select('configDetail.GLCode,configMaster.autoID, configMaster.glAccountType');
                            $this->db->from('srp_erp_pos_paymentglconfigdetail configDetail');
                            $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configDetail.paymentConfigMasterID = configMaster.autoID', 'left');
                            $this->db->where('configDetail.ID', $key);
                            $rh = $this->db->get()->row_array();

                            $GLCode = null;
                            if ($rh['autoID'] == 7) {
                                if (isset($customerAutoIDs[$key]) && $customerAutoIDs[$key]) {
                                    $receivableAutoID = $this->db->select('receivableAutoID')
                                        ->from('srp_erp_customermaster')
                                        ->where('customerAutoID', $customerAutoIDs[$key])
                                        ->get()->row('receivableAutoID');
                                    $GLCode = $receivableAutoID;
                                }
                            }
                            //echo $amount;exit;
                            $paymentData[$i]['menuSalesID'] = $invoiceID;
                            $paymentData[$i]['wareHouseAutoID'] = $outletID;
                            $paymentData[$i]['paymentConfigMasterID'] = $rh['autoID'];
                            $paymentData[$i]['paymentConfigDetailID'] = $key;
                            $paymentData[$i]['GLCode'] = $rh['autoID'] == 7 ? $GLCode : $rh['GLCode'];
                            $paymentData[$i]['glAccountType'] = $rh['glAccountType'];
                            $paymentData[$i]['amount'] = 0;
                            $paymentData[$i]['reference'] = isset($reference[$key]) ? $reference[$key] : null;
                            $paymentData[$i]['customerAutoID'] = isset($customerAutoIDs[$key]) ? $customerAutoIDs[$key] : null;

                            /*Common Data*/
                            $paymentData[$i]['createdUserGroup'] = $createdUserGroup;
                            $paymentData[$i]['createdPCID'] = $createdPCID;
                            $paymentData[$i]['createdUserID'] = $createdUserID;
                            $paymentData[$i]['createdUserName'] = $createdUserName;
                            $paymentData[$i]['createdDateTime'] = $createdDateTime;
                            $paymentData[$i]['timestamp'] = $timestamp;
                        }
                    } // end if
                } //end foreach
                //print_r('exit');exit;
                if (isset($paymentData) && !empty($paymentData)) {
                    $this->db->insert_batch('srp_erp_pos_menusalespayments', $paymentData);
                }
                if ($totalPaid > 0) {
                    $payable = $this->input->post('total_payable_amt');
                    //$balancePayable = $totalPaid - ($payable > 0 ? $payable : 0);
                    $this->db->update('srp_erp_pos_menusalesmaster', array('cashReceivedAmount' => $totalPaid, 'balanceAmount' => $returnChange, 'is_sync' => 0), array('menuSalesID' => $invoiceID));
                }
            }

            $get_outletID = get_outletID();
            $current_companyID = current_companyID();
            $isOutletTaxEnabled = isOutletTaxEnabled($get_outletID, $current_companyID);

            if ($isOutletTaxEnabled == true) {
                $this->insert_outlet_tax($outletID, $grossTotal, $promotional_discount, $invoiceID);
            }

            return true;
        } else {
            return false;
        }
    }

    function insert_outlet_tax($outletID, $grossTotal, $promotional_discount, $invoiceID)
    {
        //insert outlet tax table
        $outlet_tax_list = $this->outlet_tax_list($outletID);
        $amount_with_discount = $grossTotal - $promotional_discount;
        $tax_amount_total = 0;
        foreach ($outlet_tax_list as $outlet_tax) {
            $taxPercentage = $outlet_tax->taxPercentage;
            $tax_amount = ($amount_with_discount / 100) * $taxPercentage;

            $amount_with_tax = $amount_with_discount + $tax_amount;
            $menusalesoutlettaxes = array(
                "wareHouseAutoID" => $outletID,
                "menuSalesID" => $invoiceID,
                "outletTaxID" => $outlet_tax->outletTaxID,
                "taxmasterID" => $outlet_tax->taxMasterID,
                "GLCode" => $outlet_tax->supplierGLAutoID,
                "taxPercentage" => $taxPercentage,
                "taxAmount" => $tax_amount
            );
            $this->db->insert('srp_erp_pos_menusalesoutlettaxes', $menusalesoutlettaxes);
            $tax_amount_total += $tax_amount;
        }

        //updating 'sub total' and 'balance amount' according to tax amounts.
        $row = $this->db->query("select * from srp_erp_pos_menusalesmaster where menuSalesID=$invoiceID")->row();
        $subTotal = $row->subTotal;

        $balanceAmount = $row->balanceAmount;
        //$subTotal += $tax_amount_total;
        if ($row->isCreditSales != 1) {
            if ($balanceAmount != 0) { //not zero if promotion discount applied.
                $balanceAmount -= $tax_amount_total;
            }
        }
        if ($row->isDelivery == 1 && $row->ownDeliveryAmount > 0) {
            $balanceAmount += $tax_amount_total;
        }
        $this->db->where('menuSalesID', $invoiceID);
        //$this->db->update('srp_erp_pos_menusalesmaster',array('subTotal'=>$subTotal,'balanceAmount'=>$balanceAmount));
        $this->db->update('srp_erp_pos_menusalesmaster', array('balanceAmount' => $balanceAmount));
    }

    function outlet_tax_list($outletID)
    {
        $query = $this->db->query("SELECT 
 srp_erp_pos_outlettaxmaster.taxPercentage,
 srp_erp_pos_outlettaxmaster.outletTaxID,
 srp_erp_pos_outlettaxmaster.taxMasterID,
 srp_erp_pos_outlettaxmaster.taxDescription,
 srp_erp_taxmaster.supplierGLAutoID
 FROM `srp_erp_pos_outlettaxmaster` 
JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID=srp_erp_pos_outlettaxmaster.taxMasterID
where srp_erp_pos_outlettaxmaster.warehouseAutoID=$outletID AND srp_erp_pos_outlettaxmaster.isDeleted=0");
        //print_r($this->db->last_query());
        return $query->result();
    }

    function calculate_outlet_tax($outletID, $grossTotal, $promotional_discount)
    {
        $outlet_tax_list = $this->outlet_tax_list($outletID);
        $amount_with_discount = $grossTotal - $promotional_discount;
        $tax_amount_total = 0;
        foreach ($outlet_tax_list as $outlet_tax) {
            $taxPercentage = $outlet_tax->taxPercentage;
            $tax_amount = ($amount_with_discount / 100) * $taxPercentage;
            $tax_amount_total += $tax_amount;
        }
        return $tax_amount_total;
    }

    function update_menuSalesTax($menuSalesID)
    {
        $outletId = get_outletID();
        $q = "INSERT INTO srp_erp_pos_menusalestaxes (
                wareHouseAutoID, menuSalesID,
                menuSalesItemID, menuID, menutaxID,
                taxmasterID, GLCode, taxPercentage, taxAmount,
                beforeDiscountTotalTaxAmount, menusalesDiscount,
                menusalesPromotionalDiscount, unitMenuTaxAmount,
                menusalesItemQty, companyID, companyCode,
                createdUserGroup, createdPCID, createdUserID,
                createdDateTime, createdUserName, modifiedPCID,
                modifiedUserID, modifiedDateTime, modifiedUserName,
                `timestamp`,
                vatType
                ) (SELECT
                    msm.wareHouseAutoID as wareHouseAutoID,
                    msm.menuSalesID as menuSalesID,
                    msi.menuSalesItemID as menuSalesItemID,
                    msi.menuID as menuID,
                    mt.menuTaxID as menutaxID,
                    mt.taxmasterID as taxmasterID,
                    if(tm.taxcategory=2,tm.outputVatGLAccountAutoID,tm.supplierGLAutoID) as GLAutoID,
                    mt.taxPercentage as taxPercentage,
                    IF(msi.discountPer>0,IF(msm.promotionDiscount>0, (msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100)) * ((100-msm.promotionDiscount)/100), msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100)),IF(msm.promotionDiscount>0, (msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100)) * ((100-msm.promotionDiscount)/100), msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100))) as taxAmount,	
                    msi.qty * mt.taxAmount as beforeDiscountTaxAmount,
                    msm.discountPer as menusalesDiscount,
                    msm.promotionDiscount as menusalesPromotionalDiscount,
                    mt.taxAmount as unitMenuTaxAmount,
                    msi.qty as menusalesItemQty,
                    msm.companyID as companyID,
                    msm.companyCode as companyCode,
                    msm.createdUserGroup as createdUserGroup,
                    msm.createdPCID as createdPCID,
                    msm.createdUserID as createdUserID,
                    CURRENT_TIMESTAMP() as createdDateTime,
                    msm.createdUserName as createdUserName,
                    null as modifiedPCID,
                    null as modifiedUserID,
                    null as modifiedDateTime,
                    null as modifiedUserName,
                    CURRENT_TIMESTAMP() as `timestamp`,
                    mt.vatType
                FROM
                    srp_erp_pos_menusalesmaster msm
                INNER JOIN srp_erp_pos_menusalesitems msi ON msi.menuSalesID = msm.menuSalesID AND msi.wareHouseAutoID = msm.wareHouseAutoID
                INNER JOIN srp_erp_pos_warehousemenumaster whm ON whm.menuMasterID = msi.menuID
                INNER JOIN srp_erp_pos_menutaxes mt ON mt.menuMasterID = msi.menuID
                INNER JOIN srp_erp_taxmaster	tm ON mt.taxmasterID = tm.taxMasterAutoID
                WHERE
                    msm.menuSalesID = '" . $menuSalesID . "' 
                    AND msm.wareHouseAutoID = '" . $outletId . "'
                    AND whm.warehouseID = msm.wareHouseAutoID 
                    AND whm.isDeleted = 0 AND whm.isActive = 1 
                    AND whm.isTaxEnabled=1 
                    
                    )";
        $r = $this->db->query($q);

        /** update total Tax */
        if ($r == true) {
            $q2 = "UPDATE srp_erp_pos_menusalesmaster SET totalTaxAmount = ( SELECT sum(taxAmount) FROM srp_erp_pos_menusalestaxes WHERE menuSalesID = '" . $menuSalesID . "' AND wareHouseAutoID='" . $outletId . "'  ) WHERE menuSalesID = '" . $menuSalesID . "'";
            $r2 = $this->db->query($q2);

            if ($r2) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    function update_menuSalesServiceCharge($menuSalesID)
    {
        $outletId = get_outletID();
        $q = "INSERT INTO srp_erp_pos_menusalesservicecharge (
                wareHouseAutoID, menuSalesID, menuSalesItemID, menuServiceChargeID, menuMasterID, serviceChargePercentage, serviceChargeAmount,
                GLAutoID, beforeDiscountTotalServiceCharge, menusalesDiscount,
                menusalesPromotionalDiscount, unitMenuServiceCharge, menusalesItemQty,
                companyID, companyCode, createdUserGroup, createdPCID, createdUserID,
                createdDateTime, createdUserName, modifiedPCID, modifiedUserID,
                modifiedDateTime, modifiedUserName, `timestamp`)
                (SELECT
                msm.wareHouseAutoID as wareHouseAutoID,
                msi.menuSalesID as menuSalesID,
                msi.menuSalesItemID as menuSalesItemID,
                msc.menuServiceChargeID AS menuServiceChargeID,
                msc.menuMasterID AS menuMasterID,
                msc.serviceChargePercentage AS serviceChargePercentage,
                IF(msi.discountPer > 0 , IF(msm.promotionDiscount>0, (msi.qty * (msc.serviceChargeAmount - (msc.serviceChargeAmount * msi.discountPer/100)) * ((100-msm.discountPer)/100)) * ((100-msm.promotionDiscount)/100), msi.qty * (msc.serviceChargeAmount - (msc.serviceChargeAmount * msi.discountPer/100)) * ((100-msm.discountPer)/100)) ,IF(msm.promotionDiscount>0, (msi.qty * msc.serviceChargeAmount * ((100-msm.discountPer)/100)) * ((100-msm.promotionDiscount)/100), msi.qty * msc.serviceChargeAmount * ((100-msm.discountPer)/100)))  as serviceChargeAmount,
                msc.GLAutoID AS GLAutoID,
                msi.qty * msc.serviceChargeAmount AS beforeDiscountTotalServiceCharge,
                msm.discountPer AS menusalesDiscount,
                msm.promotionDiscount AS menusalesPromotionalDiscount,
                msc.serviceChargeAmount AS unitMenuServiceCharge,
                msi.qty AS menusalesItemQty,
                msm.companyID AS companyID,
                msm.companyCode AS companyCode,
                msm.createdUserGroup AS createdUserGroup,
                msm.createdPCID AS createdPCID,
                msm.createdUserID AS createdUserID,
                CURRENT_TIMESTAMP () AS createdDateTime,
                msm.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP () AS `timestamp`
            FROM
                srp_erp_pos_menusalesmaster msm
            INNER JOIN srp_erp_pos_menusalesitems msi ON msi.menuSalesID = msm.menuSalesID AND msi.wareHouseAutoID = msm.wareHouseAutoID
            INNER JOIN srp_erp_pos_menuservicecharge msc ON msc.menuMasterID = msi.menuID
            WHERE
                msm.menuSalesID = '" . $menuSalesID . "'
                AND  msm.wareHouseAutoID = '" . $outletId . "'
            )";

        $r = $this->db->query($q);

        if ($r == true) {
            /**update total Service charge */
            $r2 = $this->db->query("UPDATE srp_erp_pos_menusalesmaster SET serviceCharge = ( SELECT sum(serviceChargeAmount) FROM srp_erp_pos_menusalesservicecharge WHERE menuSalesID = '" . $menuSalesID . "' AND wareHouseAutoID='" . $outletId . "'   )  , is_sync = 0 WHERE menuSalesID = '" . $menuSalesID . "'");


            if ($r2) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    function update_deliveryCommission($menuSalesID)
    {
        $outletId = get_outletID();
        $q1 = "SELECT IF (subTotal IS NULL, 0, subTotal) AS totalTmp, deliveryCommission FROM srp_erp_pos_menusalesmaster WHERE menuSalesID = '" . $menuSalesID . "' AND isDelivery =1 AND wareHouseAutoID='" . $outletId . "' ";
        $result = $this->db->query($q1)->row_array();
        if (!empty($result) && $result['totalTmp'] > 0) {
            $calculatedCommission = $result['totalTmp'] * ($result['deliveryCommission'] / 100);
            $q2 = "UPDATE srp_erp_pos_menusalesmaster SET deliveryCommissionAmount =  '" . $calculatedCommission . "' , is_sync = 0   WHERE menuSalesID = '" . $menuSalesID . "' AND wareHouseAutoID='" . $outletId . "' ";
            $r2 = $this->db->query($q2);
            if ($r2 == true) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
        //echo $this->db->last_query();
    }


    function get_report_salesReport_totalSales($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }


        $q = "SELECT
                'Total Sales' AS Description,
                SUM(paidAmount) AS amount
            
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' "
            . $qString . $outletFilter;


        // echo $q;

        $result = $this->db->query($q)->row_array();
        return $result;
    }


    function get_report_salesReport_totalTaxes($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }


        $q = "SELECT
                taxMaster.taxDescription AS Description,
                SUM(tax.taxAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalestaxes tax ON tax.menuSalesID = salesMaster.menuSalesID
            INNER JOIN srp_erp_taxmaster taxMaster ON taxMaster.taxMasterAutoID = tax.taxmasterID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' 
            " . $qString . $outletFilter . "
            GROUP BY tax.taxmasterID";
        //echo $q;


        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_salesReport_ServiceCharge($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }


        $q = "SELECT
                'Service Charge' AS Description,
                SUM(sc.serviceChargeAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalesservicecharge sc ON sc.menuSalesID = salesMaster.menuSalesID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " . $qString . $outletFilter;
        //echo $q;


        $result = $this->db->query($q)->row_array();
        return $result;
    }

    function updateSendToKitchen()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $data['isOrderPending'] = 1;
        $this->db->where('menuSalesID', $invoiceID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);

        if ($result) {

            $q = "UPDATE `srp_erp_pos_menusalesitems` SET `KOTAlarm` = 0, `isOrderPending` = 1 WHERE `menuSalesID` = '" . $invoiceID . "' AND ( `KOTAlarm` = -1 OR `isOrderPending` = -1)";
            $result = $this->db->query($q);


            $printSession = $this->session->userdata('accessToken');

            if (!empty($printSession)) {
                $auth = 1;
            } else {
                $auth = 0;
            }
            return array('error' => 0, 'code' => $invoiceID, 'message' => 'done', 'auth' => $auth);
        } else {
            return array('error' => 1, 'code' => 0, 'message' => 'KOT not updated', 'auth' => 0);
        }
    }


    function get_report_lessAmount_admin($date, $date2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }

        $q = "SELECT
                    salesMaster.deliveryCommission,
                    customers.customerName,
                    SUM(netTotal) AS netTotal,
                    SUM(deliveryCommissionAmount) AS lessAmount

                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.deliveryPersonID
                JOIN srp_paymentmethodmaster payments ON  payments.PaymentMethodMasterID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(
                    salesMaster.deliveryPersonID
                )
                AND salesMaster.deliveryPersonID <> 0
                AND payments.PaymentDescription = 'Cash'
                " . $qString . "
                " . $outletFilter . "
                " . $orderFilter . "                
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_lessAmount_promotion_admin($date, $date2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }

        $q = "SELECT
                    salesMaster.promotionDiscount AS deliveryCommission,
                    customers.customerName AS customerName,
                    SUM(grossTotal) AS netTotal,
                    SUM(IFNULL(promotionDiscountAmount,0) ) as lessAmount
              
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.promotionID
                LEFT JOIN srp_paymentmethodmaster payments ON payments.PaymentMethodMasterID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(salesMaster.promotionID)
                AND salesMaster.promotionID <> 0
                " . $qString . "
                " . $outletFilter . "
                " . $orderFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_discount_admin($date, $date2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $q = "SELECT
                'Java App' AS customerName,
            SUM(netTotal) AS netTotal,
            SUM(payments.amount) AS lessAmount
            FROM
                srp_erp_pos_menusalespayments AS payments
                JOIN srp_erp_pos_menusalesmaster  AS salesMaster ON  salesMaster.menuSalesID = payments.menuSalesID
                AND salesMaster.wareHouseAutoID = payments.wareHouseAutoID 
            WHERE
                payments.paymentConfigMasterID = 25
            AND  salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID =  '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
             " . $qString . $outletFilter . $orderFilter;


        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_discount_item_wise_admin($date, $date2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $q = "SELECT
                    salesMaster.promotionDiscount AS deliveryCommission,
                    'Item Wise Discount' AS customerName,
                    SUM( grossTotal ) AS netTotal,
                    SUM( IFNULL( salesitem.discountAmount, 0 ) ) AS lessAmount 
              
                FROM
                    srp_erp_pos_menusalesitems AS salesitem
                LEFT JOIN srp_erp_pos_menusalesmaster salesMaster ON salesMaster.menuSalesID = salesitem.menuSalesID
                          AND salesMaster.wareHouseAutoID = salesitem.wareHouseAutoID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                " . $qString . "
                " . $orderFilter . "
                " . $outletFilter . " ";

        //echo $q;


        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_javaAppDiscount_admin($date, $date2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $q = "SELECT
                'Discounts' AS customerName,
            SUM(netTotal) AS netTotal,
            SUM(salesMaster.discountAmount) AS lessAmount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID =  '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
            AND salesMaster.discountAmount>0 " . $qString . $outletFilter . $orderFilter;


        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_paymentMethod_admin($date, $data2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND payments.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $q = "SELECT
                    configMaster.description as paymentDescription,
                    SUM(payments.amount) AS NetTotal,
                    count(payments.menuSalesPaymentID) as countTransaction
                FROM
                    srp_erp_pos_menusalespayments AS payments
                LEFT JOIN srp_erp_pos_menusalesmaster AS salesMaster ON payments.menuSalesID = salesMaster.menuSalesID AND payments.wareHouseAutoID = salesMaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID
                WHERE
                  salesMaster.isVoid = 0 AND
                  salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . $orderFilter . "
                GROUP BY
                    payments.paymentConfigMasterID 
                    ORDER BY payments.paymentConfigMasterID;";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_customerTypeCount_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    customertype.customerDescription,
                    count(salesMaster.netTotal) AS countTotal,
                    sum(subTotal) as subTotal
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY
                    customertype.customerDescription ORDER BY customertype.customerTypeID ";


        // echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_customerTypeCount_2_admin($date, $date2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $q = "SELECT						
                customertype.customerDescription,
                sum(payments.amount) AS subTotal,					
                count( salesMaster.menuSalesID ) AS countTotal
            FROM						
                srp_erp_pos_menusalesmaster AS salesMaster 
                LEFT JOIN  (
                    SELECT SUM( IFNULL(amount,0) ) as amount, menuSalesID, paymentConfigMasterID, wareHouseAutoID 
                    FROM srp_erp_pos_menusalespayments 
                    GROUP BY menuSalesID, wareHouseAutoID
                ) as payments ON payments.menuSalesID = salesMaster.menuSalesID AND payments.wareHouseAutoID = salesMaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID 
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID  
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter . $orderFilter .
            "GROUP BY
                    customertype.customerDescription 
                    ORDER BY customertype.customerDescription, salesMaster.wareHouseAutoID ";


        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_salesReport_totalSales_admin($date, $date2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $q = "SELECT
                'Total Sales' AS Description,
                SUM(paidAmount) AS amount

            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' "
            . $qString . $outletFilter . $orderFilter;


        // echo $q;

        $result = $this->db->query($q)->row_array();
        return $result;
    }


    function get_report_salesReport_totalTaxes_admin($date, $date2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $q = "SELECT
                taxMaster.taxDescription AS Description,
                SUM(tax.taxAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalestaxes tax ON tax.menuSalesID = salesMaster.menuSalesID
            INNER JOIN srp_erp_taxmaster taxMaster ON taxMaster.taxMasterAutoID = tax.taxmasterID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
            " . $qString . $outletFilter . $orderFilter . "
            GROUP BY tax.taxmasterID";
        //echo $q;


        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_ServiceCharge_admin($date, $date2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }

        $q = "SELECT
                'Service Charge' AS Description,
                SUM(sc.serviceChargeAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalesservicecharge sc ON sc.menuSalesID = salesMaster.menuSalesID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " . $qString . $outletFilter . $orderFilter;
        //echo $q;


        $result = $this->db->query($q)->row_array();
        return $result;
    }

    function get_report_giftCardTopUp_admin($date, $data2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND giftCard.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND giftCard.outletID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $q = "SELECT
                    configMaster.description as paymentDescription,
                    SUM(giftCard.topUpAmount) AS topUpTotal,
                     count(giftCard.cardTopUpID) as countTopUp
                FROM
                    srp_erp_pos_cardtopup AS giftCard

                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = giftCard.glConfigMasterID
                WHERE
                  giftCard.companyID = '" . current_companyID() . "'
                AND giftCard.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . " AND (giftCard.topUpAmount>0 OR giftCard.isRefund=1)
                GROUP BY
                    configMaster.autoID";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_creditSales($date, $data2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND srp_erp_pos_menusalesmaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND srp_erp_pos_menusalesmaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND srp_erp_pos_menusalesmaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $q = "SELECT
                Sum( srp_erp_pos_menusalespayments.amount ) AS salesAmount,
                count(srp_erp_pos_menusalespayments.menuSalesPaymentID) as countCreditSales,
                srp_erp_pos_customermaster.CustomerName,
                srp_erp_pos_customermaster.CustomerAutoID 
            FROM
                srp_erp_pos_menusalespayments
                INNER JOIN srp_erp_pos_paymentglconfigmaster ON srp_erp_pos_paymentglconfigmaster.autoID = srp_erp_pos_menusalespayments.paymentConfigMasterID
                INNER JOIN srp_erp_pos_menusalesmaster ON srp_erp_pos_menusalespayments.menuSalesID = srp_erp_pos_menusalesmaster.menuSalesID AND srp_erp_pos_menusalespayments.wareHouseAutoID = srp_erp_pos_menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_pos_customermaster.CustomerAutoID 
                WHERE
                  srp_erp_pos_menusalesmaster.companyID = '" . current_companyID() . "'
                AND srp_erp_pos_menusalesmaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . $orderFilter . "  
                AND srp_erp_pos_paymentglconfigmaster.autoID = 7 
                AND  srp_erp_pos_menusalesmaster.isVoid = 0 
                AND srp_erp_pos_menusalesmaster.isHold = 0
                GROUP BY
                    srp_erp_pos_customermaster.CustomerAutoID
                ORDER BY srp_erp_pos_menusalespayments.paymentConfigMasterID;";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_report_voidBills_admin($date, $data2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $q = "SELECT
                   'Voided Bills'  AS paymentDescription,
                    SUM(salesMaster.subTotal) AS NetTotal,
                     count(	salesMaster.menuSalesID) as countTransaction
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster

                WHERE
                   salesMaster.isVoid = 1 AND
                salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . $orderFilter;
        $result = $this->db->query($q)->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_fullyDiscountBills_admin($date, $data2, $cashier = null, $outlets = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $q = "SELECT
                  count( salesMaster.menuSalesID ) AS fullyDiscountBills 
                FROM
                      srp_erp_pos_menusalesmaster AS salesMaster
                      LEFT JOIN srp_erp_pos_menusalespayments msp ON msp.menuSalesID = salesMaster.menuSalesID
                      AND msp.wareHouseAutoID = salesMaster.wareHouseAutoID
                WHERE
                  msp.menuSalesID IS NULL 
                  AND salesMaster.isVoid = 0 
                  AND salesMaster.isHold = 0
                  AND salesMaster.companyID = '" . current_companyID() . "'
                  AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . $orderFilter;
        //echo $q;
        $result = $this->db->query($q)->row_array();
        return $result;
    }

    function get_outlet_cashier()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashier2" class="form-control input-sm" multiple = "multiple"  required >';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashier2" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }
    }

    function get_outlet_waiter()
    {
        $warehouseUser = get_warehouse_user_details();
        if ($warehouseUser->superAdminYN == 1) {
            if ($this->input->post("warehouseAutoID")) {
                $warehouse = join(',', $this->input->post("warehouseAutoID"));
                $q = "SELECT
	Ename1 AS empName,
	srp_erp_pos_crewmembers.crewMemberID
FROM
	srp_erp_pos_menusalesmaster salesMaster	
	Join srp_erp_pos_crewmembers on srp_erp_pos_crewmembers.crewMemberID = salesMaster.waiterID
 	join srp_erp_pos_crewroles on srp_erp_pos_crewroles.crewRoleID = srp_erp_pos_crewmembers.crewRoleID
	join srp_employeesdetails on srp_employeesdetails.EIdNo=srp_erp_pos_crewmembers.EIdNo
WHERE
	salesMaster.companyID = " . current_companyID() . "
	and srp_erp_pos_crewroles.isWaiter=1
	AND salesMaster.warehouseAutoID IN ($warehouse) 
GROUP BY
	salesMaster.waiterID";

                $result = $this->db->query($q)->result_array();
                $html = '<select name = "waiter[]" id = "cashier2" class="form-control input-sm" multiple = "multiple"  required >';
                if ($result) {
                    foreach ($result as $val) {
                        $html .= '<option value = "' . $val['crewMemberID'] . '" > ' . $val['empName'] . ' </option > ';
                    }
                }
                $html .= '</select > ';
                return $html;
            } else {
                $html = '<select name = "waiter[]" id = "cashier2" class="form-control input-sm" multiple = "multiple"  required > ';
                $html .= '</select > ';
                return $html;
            }
        } else {
            $warehouse = get_outletID();
            $q = "SELECT
	Ename1 AS empName,
	srp_erp_pos_crewmembers.crewMemberID
FROM
	srp_erp_pos_menusalesmaster salesMaster	
	Join srp_erp_pos_crewmembers on srp_erp_pos_crewmembers.crewMemberID = salesMaster.waiterID
 	join srp_erp_pos_crewroles on srp_erp_pos_crewroles.crewRoleID = srp_erp_pos_crewmembers.crewRoleID
	join srp_employeesdetails on srp_employeesdetails.EIdNo=srp_erp_pos_crewmembers.EIdNo
WHERE
	salesMaster.companyID = " . current_companyID() . "
	and srp_erp_pos_crewroles.isWaiter=1
	AND salesMaster.warehouseAutoID IN ($warehouse) 
GROUP BY
	salesMaster.waiterID";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "waiter[]" id = "cashier2" class="form-control input-sm" multiple = "multiple"  required >';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['crewMemberID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        }
    }


    function get_outlet_cashier_itemized()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashieritemized" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashieritemized" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }
    }

    function get_outlet_cashier_Promotions()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashierpromotions" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashierpromotions" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }
    }


    function get_outlet_cashier_productmix()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashierproductmix" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashierproductmix" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }
    }

    function get_outlet_cashier_franchise()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashierfranchise" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashierfranchise" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }
    }

    function get_srp_erp_pos_paymentglconfigmaster($outlets = null)
    {
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND srp_erp_pos_paymentglconfigdetail.warehouseID IN(" . $outlets . ")";
        }

        /* $this->db->select("autoID,description,sortOrder");
         $this->db->from("srp_erp_pos_paymentglconfigmaster");
         $this->db->join("srp_erp_pos_paymentglconfigdetail", "srp_erp_pos_paymentglconfigdetail.paymentConfigMasterID = srp_erp_pos_paymentglconfigmaster.autoID", "inner");
         $this->db->order_by("sortOrder ASC");
         $result = $this->db->get()->result_array();
         return $result;*/

        $result = "SELECT
                            autoID,description,sortOrder
                        FROM
                            srp_erp_pos_paymentglconfigmaster
                            INNER JOIN srp_erp_pos_paymentglconfigdetail ON srp_erp_pos_paymentglconfigdetail.paymentConfigMasterID = srp_erp_pos_paymentglconfigmaster.autoID
                        WHERE
                             srp_erp_pos_paymentglconfigdetail.companyID = " . current_companyID() . "   " . $outletFilter . "
                        Group BY
                            srp_erp_pos_paymentglconfigdetail.paymentConfigMasterID
                        Order BY
                            sortOrder ASC ";

        //var_dump($result);exit;
        return $this->db->query($result)->result_array();
    }

    function get_report_salesDetailReport_with_waiters($date, $date2, $cashier = null, $outlets = null, $customers = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.waiterID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $customerFilter = '';
        if ($customers != null) {
            $customerFilter = " AND ((delivery.posCustomerAutoID IN(" . $customers . ") 
            OR salesMaster.customerID IN(" . $customers . "))
            OR payment.posCustomerAutoID IN (" . $customers . "))";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }

        $paymentList = $this->Pos_config_model->get_payment_list_for_reports($outlets);
        $paymentListStringArray = array();
        $selectColumnArray = array();
        foreach ($paymentList as $item) {
            $tempStr = "sum( CASE WHEN paymentConfigMasterID = '" . $item['autoID'] . "' THEN amount ELSE 0 END ) " . str_replace(' ', '', $item['description']);
            $selectColumnTemp = "payment." . str_replace(' ', '', $item['description']);
            array_push($paymentListStringArray, $tempStr);
            array_push($selectColumnArray, $selectColumnTemp);
        }
        $commaSeperatedPaymentListString = implode(",", $paymentListStringArray);
        $selectColumnString = implode(",", $selectColumnArray);

        $querySalesDetail = "SELECT
                            salesMaster.menuSalesID AS salesMasterMenuSalesID,
                            DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, salesMaster.createdDateTime), '%d-%m-%Y' ) AS salesMasterCreatedDate,
                            DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryTime, salesMaster.createdDateTime), '%h-%i %p' ) AS salesMasterCreatedTime,
                            wmaster.wareHouseDescription AS whouseName,
                            employee.EmpShortCode AS menuCreatedUser,
                            salesMaster.grossTotal,
                            salesMaster.grossAmount,
                            salesMaster.companyLocalCurrencyDecimalPlaces AS companyLocalDecimal,
                            invoiceCode,
                            salesMaster.discountPer,
                            salesMaster.discountAmount,
                            salesMaster.promotionDiscount,
                            salesMaster.deliveryCommission,
                            salesMaster.isOnTimeCommision,
                            salesMaster.isDelivery,
                            salesMaster.deliveryCommissionAmount,
                            salesMaster.subTotal AS billNetTotal,
                            salesMaster.promotionDiscount,
                            salesMaster.wareHouseAutoID as wareHouseAutoID,
                      
                            payment.paymentConfigMasterID,
                            payment.amount,
                            payment.customerAutoID, 
                            payment.posCustomerAutoID as posCustomerAutoID,
                            $selectColumnString,                            
                            payment.customerName,
                            payment.customerTelephone,
                            promotionTypeP.customerName AS PromotionalDiscountType,
                            promotionTypeD.customerName AS DeliveryCommissionType 
                        FROM
                            srp_erp_pos_menusalesmaster AS salesMaster
                            LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID
                            LEFT JOIN srp_erp_pos_crewmembers ON srp_erp_pos_crewmembers.crewMemberID = salesMaster.waiterID
                            LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = srp_erp_pos_crewmembers.EIdNo                            
                            LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID
                            LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID
                            LEFT JOIN srp_erp_pos_deliveryorders delivery ON salesMaster.menuSalesID = delivery.menuSalesMasterID
                            LEFT JOIN (
                                    SELECT
                                        paymentConfigMasterID,
                                        amount,
                                        menuSalesID,
                                        srp_erp_pos_menusalespayments.customerAutoID,
                                        srp_erp_customermaster.customerName,
                                        srp_erp_customermaster.customerTelephone,
                                        $commaSeperatedPaymentListString,                                        
                                        srp_erp_pos_menusalespayments.wareHouseAutoID as wareHouseAutoID,
                                        posCustomerAutoID 
                                    FROM
                                        srp_erp_pos_menusalespayments
                                       
                                        LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID 
                                        LEFT JOIN srp_erp_pos_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_pos_customermaster.customerAutoID 
                                    GROUP BY
                                        menuSalesID, wareHouseAutoID  
                            ) payment ON salesMaster.menuSalesID = payment.menuSalesID  AND payment.wareHouseAutoID = salesMaster.wareHouseAutoID
                        WHERE
                            salesMaster.isVoid = 0 
                            AND salesMaster.isHold = 0 
                            AND salesMaster.companyID = " . current_companyID() . " 
                            AND IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, salesMaster.createdDateTime) BETWEEN '" . $date . "' 
                            AND '" . $date2 . "' " . $qString . $outletFilter . $customerFilter . $orderFilter . " 
                        GROUP BY
                            salesMaster.menuSalesID, salesMaster.wareHouseAutoID ";

        //var_dump($querySalesDetail);exit;
        return $this->db->query($querySalesDetail)->result_array();
    }

    function get_report_salesDetailReport($date, $date2, $cashier = null, $outlets = null, $customers = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }
        $customerFilter = '';
        if ($customers != null) {
            $customerFilter = " AND ((delivery.posCustomerAutoID IN(" . $customers . ") 
            OR salesMaster.customerID IN(" . $customers . "))
            OR srp_erp_pos_customermaster.posCustomerAutoID IN (" . $customers . "))";
        }

        $paymentList = $this->Pos_config_model->get_payment_list_for_reports($outlets);
        $paymentListStringArray = array();
        $selectColumnArray = array();
        foreach ($paymentList as $item) {
            $tempStr = "sum( CASE WHEN paymentConfigMasterID = '" . $item['autoID'] . "' THEN amount ELSE 0 END ) " . str_replace(' ', '', $item['description']);
            $selectColumnTemp = "payment." . str_replace(' ', '', $item['description']);
            array_push($paymentListStringArray, $tempStr);
            array_push($selectColumnArray, $selectColumnTemp);
        }
        $commaSeperatedPaymentListString = implode(",", $paymentListStringArray);
        $selectColumnString = implode(",", $selectColumnArray);

        $querySalesDetail = "SELECT
	salesMaster.menuSalesID AS salesMasterMenuSalesID,
	DATE_FORMAT( IF ( delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, salesMaster.createdDateTime ), '%d-%m-%Y' ) AS salesMasterCreatedDate,
	DATE_FORMAT( IF ( delivery.deliveryOrderID IS NOT NULL, delivery.deliveryTime, salesMaster.createdDateTime ), '%h-%i %p' ) AS salesMasterCreatedTime,
	wmaster.wareHouseDescription AS whouseName,
	employee.EmpShortCode AS menuCreatedUser,
	salesMaster.grossTotal,
	salesMaster.grossAmount,
	salesMaster.companyLocalCurrencyDecimalPlaces AS companyLocalDecimal,
	invoiceCode,
	salesMaster.discountPer,
	salesMaster.discountAmount,
	salesMaster.promotionDiscount,
	salesMaster.deliveryCommission,
	salesMaster.isOnTimeCommision,
	salesMaster.isDelivery,
	salesMaster.deliveryCommissionAmount,
	salesMaster.subTotal AS billNetTotal,
	salesMaster.promotionDiscount,
	salesMaster.wareHouseAutoID AS wareHouseAutoID,
	srp_erp_customertypemaster.displayDescription AS orderType,
	payment.paymentConfigMasterID,
	payment.amount,
	payment.customerAutoID,
	srp_erp_pos_customermaster.posCustomerAutoID AS posCustomerAutoID,
	$selectColumnString,
	payment.customerName,
	payment.customerTelephone,
	promotionTypeP.customerName AS PromotionalDiscountType,
	promotionTypeD.customerName AS DeliveryCommissionType 
FROM
	srp_erp_pos_menusalesmaster AS salesMaster
	LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID
	LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID
	LEFT JOIN (select customerName,customerID from srp_erp_pos_customers) promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID
	LEFT JOIN (select customerName,customerID from srp_erp_pos_customers) promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID
	LEFT JOIN srp_erp_pos_deliveryorders delivery ON salesMaster.menuSalesID = delivery.menuSalesMasterID
	LEFT JOIN srp_erp_customertypemaster ON salesMaster.customerTypeID = srp_erp_customertypemaster.customerTypeID
	LEFT JOIN (
SELECT
	paymentConfigMasterID,
	amount,
	menuSalesID,
	srp_erp_pos_menusalespayments.customerAutoID,
	srp_erp_customermaster.customerName,
	srp_erp_customermaster.customerTelephone,
	$commaSeperatedPaymentListString,
	srp_erp_pos_menusalespayments.wareHouseAutoID AS wareHouseAutoID	
FROM
	srp_erp_pos_menusalespayments
	LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID
GROUP BY
	menuSalesID,
	wareHouseAutoID 
	) payment ON salesMaster.menuSalesID = payment.menuSalesID 
	AND payment.wareHouseAutoID = salesMaster.wareHouseAutoID	
LEFT JOIN	srp_erp_pos_customermaster ON payment.customerAutoID = srp_erp_pos_customermaster.customerAutoID
                        WHERE
                            salesMaster.isVoid = 0 
                            AND salesMaster.isHold = 0 
                            AND salesMaster.companyID = " . current_companyID() . " 
                            AND IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, salesMaster.createdDateTime) BETWEEN '" . $date . "' 
                            AND '" . $date2 . "' " . $qString . $outletFilter . $customerFilter . $orderFilter . " 
                        GROUP BY
                            salesMaster.menuSalesID, salesMaster.wareHouseAutoID ";

        //var_dump($querySalesDetail);exit;
        return $this->db->query($querySalesDetail)->result_array();
    }

    function update_pos_submitted_payments()
    {
        $invoiceID = isPos_invoiceSessionExist();
        $outletID = get_outletID();
        //var_dump($invoiceID);exit;
        //var_dump($this->input->post());exit;

        if ($invoiceID) {
            $totalPaid = 0;

            $isConfirmedDeliveryOrder = pos_isConfirmedDeliveryOrder($invoiceID);

            $createdUserGroup = user_group();
            $createdPCID = current_pc();
            $createdUserID = current_userID();
            $createdUserName = current_user();
            $createdDateTime = format_date_mysql_datetime();
            $timestamp = format_date_mysql_datetime();
            $companyID = current_companyID();
            $companyCode = current_company_code();

            $masterData = get_pos_invoice_id($invoiceID);


            $reference = $this->input->post('referenceUpdate');
            $customerAutoIDs = $this->input->post('customerAutoIDUpdate');
            $paymentTypes = $this->input->post('paymentTypesUpdate');
            $cardTotalAmount = $this->input->post('cardTotalAmountUpdate');
            $netTotalAmount = $this->input->post('netTotalAmountUpdate');
            $isDelivery = $this->input->post('isDeliveryUpdate');
            $isOnTimePayment = $this->input->post('isOnTimePaymentUpdate');
            $payableDeliveryAmount = $this->input->post('totalPayableAmountDelivery_idUpdate');
            $returnChange = $this->input->post('returned_changeUpdate');
            $grossTotal = $this->input->post('total_payable_amtUpdate');
            $promotional_discount = $this->input->post('promotional_discount');
            $paid = $this->input->post('paid');
            $isOwnDelivery = $this->input->post('isOwnDelivery', true);

            if (!empty($paymentTypes)) {
                $i = 0;
                foreach ($paymentTypes as $key => $amount) {
                    if ($amount > 0) {
                        $totalPaid += $amount;
                        $this->db->select('configDetail.GLCode,configMaster.autoID, configMaster.glAccountType');
                        $this->db->from('srp_erp_pos_paymentglconfigdetail configDetail');
                        $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configDetail.paymentConfigMasterID = configMaster.autoID', 'left');
                        $this->db->where('configDetail.ID', $key);
                        $r = $this->db->get()->row_array();

                        if ($r['glAccountType'] == 1) {
                            /** Cash Payment */
                            if ($isDelivery == 1 && $isOnTimePayment == 1 && $payableDeliveryAmount == 0) {
                                $cashPaidAmount = $paid;
                            } else if ($isDelivery == 1 && $isOnTimePayment == 1 && $netTotalAmount != $paid) {
                                $cashPaidAmount = $payableDeliveryAmount - $paid;
                            } else if ($isDelivery == 1 && $isOnTimePayment == 1) {
                                $cashPaidAmount = $payableDeliveryAmount - $cardTotalAmount;
                            } else if ($isOwnDelivery == 1) {
                                $cashPaidAmount = $payableDeliveryAmount - $cardTotalAmount; //verify.

                            } else {

                                $cashPaidAmount = $netTotalAmount - $cardTotalAmount;
                                //$advancePayment = get_paidAmount($invoiceID);
                                $advancePayment = 0;
                                $payable = $netTotalAmount - ($advancePayment + $cardTotalAmount); //bug because of this.
                                //$payable = $grossTotal - ($advancePayment + $cardTotalAmount);
                                //$payable = $paid - ($advancePayment + $cardTotalAmount);//this is in-progress.
                                //                                var_dump('amount');
                                //                                var_dump($amount);
                                //                                var_dump('payable');
                                // var_dump($payable);exit;
                                if ($amount == $payable) {

                                    $cashPaidAmount = $amount;
                                    $returnChange = 0;
                                } else if ($amount > $payable) {
                                    //var_dump('1');exit;
                                    $cashPaidAmount = $payable;
                                    $returnChange = $amount - $payable;
                                    if ($promotional_discount > 0) {
                                        $returnChange += $promotional_discount;
                                    }
                                } else {

                                    /** Advance payment */
                                    $cashPaidAmount = $amount;
                                    $returnChange = 0;
                                }
                            }

                            $amount = $cashPaidAmount;
                            //var_dump($amount);exit;
                        }

                        $paymentMethods= $r['autoID'];
                        /** Credit Customer's GL Code should be picked from Customer */
                        $GLCode = null;
                        if ($r['autoID'] == 7) {
                            if (isset($customerAutoIDs[$key]) && $customerAutoIDs[$key]) {
                                $receivableAutoID = $this->db->select('receivableAutoID')
                                    ->from('srp_erp_customermaster')
                                    ->where('customerAutoID', $customerAutoIDs[$key])
                                    ->get()->row('receivableAutoID');
                                $GLCode = $receivableAutoID;
                            }
                        }
                        

                        $paymentData[$i]['menuSalesID'] = $invoiceID;
                        $paymentData[$i]['wareHouseAutoID'] = $outletID;
                        $paymentData[$i]['paymentConfigMasterID'] = $r['autoID'];
                        $paymentData[$i]['paymentConfigDetailID'] = $key;
                        $paymentData[$i]['GLCode'] = $r['autoID'] == 7 ? $GLCode : $r['GLCode'];
                        $paymentData[$i]['glAccountType'] = $r['glAccountType'];
                        $paymentData[$i]['amount'] = $amount;
                        $paymentData[$i]['reference'] = isset($reference[$key]) ? $reference[$key] : null;
                        $paymentData[$i]['customerAutoID'] = isset($customerAutoIDs[$key]) ? $customerAutoIDs[$key] : null;

                        /*Common Data*/
                        $paymentData[$i]['createdUserGroup'] = $createdUserGroup;
                        $paymentData[$i]['createdPCID'] = $createdPCID;
                        $paymentData[$i]['createdUserID'] = $createdUserID;
                        $paymentData[$i]['createdUserName'] = $createdUserName;
                        $paymentData[$i]['createdDateTime'] = $createdDateTime;
                        $paymentData[$i]['timestamp'] = $timestamp;

                        if ($r['autoID'] == 25) {
                            $data_JA['menuSalesID'] = $invoiceID;
                            $data_JA['outletID'] = $outletID;
                            $data_JA['appPIN'] = isset($reference[$key]) ? $reference[$key] : null;;
                            $data_JA['amount'] = $amount;
                            $data_JA['companyID'] = $companyID;
                            $data_JA['companyCode'] = $companyCode;
                            $data_JA['createdUserGroup'] = $createdUserGroup;
                            $data_JA['createdPCID'] = $createdPCID;
                            $data_JA['createdUserID'] = $createdUserID;
                            $data_JA['createdDateTime'] = $createdDateTime;
                            $data_JA['createdUserName'] = $createdUserName;
                            $data_JA['timestamp'] = $createdDateTime;

                            $this->db->insert('srp_erp_pos_javaappredeemhistory', $data_JA);
                        }

                        if ($r['autoID'] == 5) {
                            $barCode = isset($reference[$key]) ? $reference[$key] : null;
                            $cardInfo = get_giftCardInfo($barCode);

                            $dta_GC['wareHouseAutoID'] = $outletID;
                            $dta_GC['cardMasterID'] = !empty($cardInfo) ? $cardInfo['cardMasterID'] : null;
                            $dta_GC['barCode'] = isset($reference[$key]) ? $reference[$key] : null;
                            $dta_GC['posCustomerAutoID'] = !empty($cardInfo) ? $cardInfo['posCustomerAutoID'] : null;
                            $dta_GC['topUpAmount'] = abs($amount) * -1;
                            $dta_GC['points'] = 0;
                            $dta_GC['glConfigMasterID'] = $r['autoID'];
                            $dta_GC['glConfigDetailID'] = $key;
                            $dta_GC['menuSalesID'] = $invoiceID;
                            $dta_GC['giftCardGLAutoID'] = $r['autoID'] == 7 ? null : $r['GLCode'];
                            $dta_GC['outletID'] = $outletID;
                            $dta_GC['reference'] = 'redeem barcode ' . $barCode;
                            $dta_GC['companyID'] = $companyID;
                            $dta_GC['companyCode'] = $companyCode;
                            $dta_GC['createdPCID'] = $createdPCID;
                            $dta_GC['createdUserID'] = $createdUserID;
                            $dta_GC['createdDateTime'] = $createdDateTime;
                            $dta_GC['createdUserName'] = $createdUserName;
                            $dta_GC['createdUserGroup'] = $createdUserGroup;
                            $dta_GC['timestamp'] = $createdDateTime;

                            $this->db->insert('srp_erp_pos_cardtopup', $dta_GC);
                        }

                        $i++;
                    } else {
                        if ($amount != null && $amount == 0) {
                            //echo $key;
                            $this->db->select('configDetail.GLCode,configMaster.autoID, configMaster.glAccountType');
                            $this->db->from('srp_erp_pos_paymentglconfigdetail configDetail');
                            $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configDetail.paymentConfigMasterID = configMaster.autoID', 'left');
                            $this->db->where('configDetail.ID', $key);
                            $rh = $this->db->get()->row_array();

                            $GLCode = null;
                            if ($rh['autoID'] == 7) {
                                if (isset($customerAutoIDs[$key]) && $customerAutoIDs[$key]) {
                                    $receivableAutoID = $this->db->select('receivableAutoID')
                                        ->from('srp_erp_customermaster')
                                        ->where('customerAutoID', $customerAutoIDs[$key])
                                        ->get()->row('receivableAutoID');
                                    $GLCode = $receivableAutoID;
                                }
                            }
                            //echo $amount;exit;
                            $paymentData[$i]['menuSalesID'] = $invoiceID;
                            $paymentData[$i]['wareHouseAutoID'] = $outletID;
                            $paymentData[$i]['paymentConfigMasterID'] = $rh['autoID'];
                            $paymentData[$i]['paymentConfigDetailID'] = $key;
                            $paymentData[$i]['GLCode'] = $rh['autoID'] == 7 ? $GLCode : $rh['GLCode'];
                            $paymentData[$i]['glAccountType'] = $rh['glAccountType'];
                            $paymentData[$i]['amount'] = 0;
                            $paymentData[$i]['reference'] = isset($reference[$key]) ? $reference[$key] : null;
                            $paymentData[$i]['customerAutoID'] = isset($customerAutoIDs[$key]) ? $customerAutoIDs[$key] : null;

                            /*Common Data*/
                            $paymentData[$i]['createdUserGroup'] = $createdUserGroup;
                            $paymentData[$i]['createdPCID'] = $createdPCID;
                            $paymentData[$i]['createdUserID'] = $createdUserID;
                            $paymentData[$i]['createdUserName'] = $createdUserName;
                            $paymentData[$i]['createdDateTime'] = $createdDateTime;
                            $paymentData[$i]['timestamp'] = $timestamp;
                        }
                    } // end if
                }
                //var_dump($paymentData);exit;
                if (isset($paymentData) && !empty($paymentData)) {
                    $this->db->delete('srp_erp_pos_menusalespayments', array('menuSalesID' => $invoiceID));
                    $this->db->insert_batch('srp_erp_pos_menusalespayments', $paymentData);
                }
                if ($totalPaid > 0) {
                    $payable = $this->input->post('total_payable_amt');
                    //$balancePayable = $totalPaid - ($payable > 0 ? $payable : 0);
                    if($paymentMethods == 1) {
                        $this->db->update('srp_erp_pos_menusalesmaster', array('cashReceivedAmount' => $totalPaid, 'cashAmount' => $totalPaid, 'balanceAmount' => $returnChange, 'is_sync' => 0), array('menuSalesID' => $invoiceID));
                    } else {
                        $this->db->update('srp_erp_pos_menusalesmaster', array('cashReceivedAmount' => $totalPaid, 'cashAmount' => 0, 'balanceAmount' => $returnChange, 'is_sync' => 0), array('menuSalesID' => $invoiceID));
                    }
                    
                }
            }


            $data['status'] = true;
            $data['invoice_id'] = $invoiceID;
            return $data;
        } else {
            $data['status'] = false;
            $data['invoice_id'] = $invoiceID;
            return $data;
        }
    }

    function get_void_detail_report_with_waiters($date, $date2, $cashier = null, $outlets = null, $customers = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.waiterID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }

        $customerFilter = '';
        if ($customers != null) {
            $customerFilter = " AND ((delivery.posCustomerAutoID IN(" . $customers . ") 
            OR salesMaster.customerID IN(" . $customers . "))
            OR payment.posCustomerAutoID IN (" . $customers . "))";
        }

        $paymentList = $this->Pos_config_model->get_payment_list_for_reports($outlets);
        $paymentListStringArray = array();
        $selectColumnArray = array();
        foreach ($paymentList as $item) {
            $tempStr = "sum( CASE WHEN paymentConfigMasterID = '" . $item['autoID'] . "' THEN amount ELSE 0 END ) " . str_replace(' ', '', $item['description']);
            $selectColumnTemp = "payment." . str_replace(' ', '', $item['description']);
            array_push($paymentListStringArray, $tempStr);
            array_push($selectColumnArray, $selectColumnTemp);
        }
        $commaSeperatedPaymentListString = implode(",", $paymentListStringArray);
        $selectColumnString = implode(",", $selectColumnArray);

        $querySalesDetail = "SELECT
                            salesMaster.menuSalesID AS salesMasterMenuSalesID,
                            DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, salesMaster.createdDateTime), '%d-%m-%Y' ) AS salesMasterCreatedDate,
                            DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryTime, salesMaster.createdDateTime), '%h-%i %p' ) AS salesMasterCreatedTime,
                            wmaster.wareHouseDescription AS whouseName,
                            employee.EmpShortCode AS menuCreatedUser,
                            salesMaster.grossTotal,
                            salesMaster.grossAmount,
                            salesMaster.companyLocalCurrencyDecimalPlaces AS companyLocalDecimal,
                            invoiceCode,
                            salesMaster.discountPer,
                            salesMaster.discountAmount,
                            salesMaster.promotionDiscount,
                            salesMaster.deliveryCommission,
                            salesMaster.isOnTimeCommision,
                            salesMaster.isDelivery,
                            salesMaster.deliveryCommissionAmount,
                            salesMaster.subTotal AS billNetTotal,
                            salesMaster.promotionDiscount,
                            salesMaster.wareHouseAutoID as wareHouseAutoID,
                      
                            payment.paymentConfigMasterID,
                            payment.amount,
                            payment.customerAutoID, 
                            payment.posCustomerAutoID as posCustomerAutoID,
                            $selectColumnString,                            
                            payment.customerName,
                            payment.customerTelephone,
                            promotionTypeP.customerName AS PromotionalDiscountType,
                            promotionTypeD.customerName AS DeliveryCommissionType 
                        FROM
                            srp_erp_pos_menusalesmaster AS salesMaster
                            LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID
                            LEFT JOIN srp_erp_pos_crewmembers ON srp_erp_pos_crewmembers.crewMemberID = salesMaster.waiterID
                            LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = srp_erp_pos_crewmembers.EIdNo
                            LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID
                            LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID
                            LEFT JOIN srp_erp_pos_deliveryorders delivery ON salesMaster.menuSalesID = delivery.menuSalesMasterID
                            LEFT JOIN (
                                    SELECT
                                        paymentConfigMasterID,
                                        amount,
                                        menuSalesID,
                                        srp_erp_pos_menusalespayments.customerAutoID,
                                        srp_erp_customermaster.customerName,
                                        srp_erp_customermaster.customerTelephone,
                                        $commaSeperatedPaymentListString,
                                        srp_erp_pos_menusalespayments.wareHouseAutoID as wareHouseAutoID,
                                        posCustomerAutoID 
                                    FROM
                                        srp_erp_pos_menusalespayments
                                       
                                        LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID
                                        LEFT JOIN srp_erp_pos_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_pos_customermaster.customerAutoID 
                                    GROUP BY
                                        menuSalesID, wareHouseAutoID  
                            ) payment ON salesMaster.menuSalesID = payment.menuSalesID  AND payment.wareHouseAutoID = salesMaster.wareHouseAutoID
                        WHERE
                            salesMaster.isVoid = 1 
                            AND salesMaster.isHold = 0 
                            AND salesMaster.companyID = " . current_companyID() . " 
                            AND IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, salesMaster.createdDateTime) BETWEEN '" . $date . "' 
                            AND '" . $date2 . "' " . $qString . $outletFilter . $customerFilter . $orderFilter . " 
                        GROUP BY
                            salesMaster.menuSalesID, salesMaster.wareHouseAutoID ";


        return $this->db->query($querySalesDetail)->result_array();
    }

    function get_void_detail_report($date, $date2, $cashier = null, $outlets = null, $customers = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $customerFilter = '';
        if ($customers != null) {
            $customerFilter = " AND ((delivery.posCustomerAutoID IN(" . $customers . ") 
            OR salesMaster.customerID IN(" . $customers . "))
            OR payment.posCustomerAutoID IN (" . $customers . "))";
        }
        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }

        $paymentList = $this->Pos_config_model->get_payment_list_for_reports($outlets);
        $paymentListStringArray = array();
        $selectColumnArray = array();
        foreach ($paymentList as $item) {
            $tempStr = "sum( CASE WHEN paymentConfigMasterID = '" . $item['autoID'] . "' THEN amount ELSE 0 END ) " . str_replace(' ', '', $item['description']);
            $selectColumnTemp = "payment." . str_replace(' ', '', $item['description']);
            array_push($paymentListStringArray, $tempStr);
            array_push($selectColumnArray, $selectColumnTemp);
        }
        $commaSeperatedPaymentListString = implode(",", $paymentListStringArray);
        $selectColumnString = implode(",", $selectColumnArray);

        $querySalesDetail = "SELECT
                            salesMaster.menuSalesID AS salesMasterMenuSalesID,
                            DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, salesMaster.createdDateTime), '%d-%m-%Y' ) AS salesMasterCreatedDate,
                            DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryTime, salesMaster.createdDateTime), '%h-%i %p' ) AS salesMasterCreatedTime,
                            wmaster.wareHouseDescription AS whouseName,
                            employee.EmpShortCode AS menuCreatedUser,
                            salesMaster.grossTotal,
                            salesMaster.grossAmount,
                            salesMaster.companyLocalCurrencyDecimalPlaces AS companyLocalDecimal,
                            invoiceCode,
                            salesMaster.discountPer,
                            salesMaster.discountAmount,
                            salesMaster.promotionDiscount,
                            salesMaster.deliveryCommission,
                            salesMaster.isOnTimeCommision,
                            salesMaster.isDelivery,
                            salesMaster.deliveryCommissionAmount,
                            salesMaster.subTotal AS billNetTotal,
                            salesMaster.promotionDiscount,
                            salesMaster.wareHouseAutoID as wareHouseAutoID,
                            srp_erp_customertypemaster.displayDescription as orderType,
                      
                            payment.paymentConfigMasterID,
                            payment.amount,
                            payment.customerAutoID, 
                            payment.posCustomerAutoID as posCustomerAutoID,
                            $selectColumnString,                            
                            payment.customerName,
                            payment.customerTelephone,
                            promotionTypeP.customerName AS PromotionalDiscountType,
                            promotionTypeD.customerName AS DeliveryCommissionType 
                        FROM
                            srp_erp_pos_menusalesmaster AS salesMaster
                            LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID
                            LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID
                            LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID
                            LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID
                            LEFT JOIN srp_erp_pos_deliveryorders delivery ON salesMaster.menuSalesID = delivery.menuSalesMasterID
                            LEFT JOIN srp_erp_customertypemaster ON salesMaster.customerTypeID = srp_erp_customertypemaster.customerTypeID  
                            LEFT JOIN (
                                    SELECT
                                        paymentConfigMasterID,
                                        amount,
                                        menuSalesID,
                                        srp_erp_pos_menusalespayments.customerAutoID,
                                        srp_erp_customermaster.customerName,
                                        srp_erp_customermaster.customerTelephone,
                                        $commaSeperatedPaymentListString,
                                        srp_erp_pos_menusalespayments.wareHouseAutoID as wareHouseAutoID,
                                        posCustomerAutoID 
                                    FROM
                                        srp_erp_pos_menusalespayments
                                       
                                        LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID
                                        LEFT JOIN (select * from srp_erp_pos_customermaster where customerAutoID is not null) srp_erp_pos_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_pos_customermaster.customerAutoID 
                                    GROUP BY
                                        menuSalesID, wareHouseAutoID  
                            ) payment ON salesMaster.menuSalesID = payment.menuSalesID  AND payment.wareHouseAutoID = salesMaster.wareHouseAutoID
                        WHERE
                            salesMaster.isVoid = 1 
                            AND salesMaster.isHold = 0 
                            AND salesMaster.companyID = " . current_companyID() . " 
                            AND IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, salesMaster.createdDateTime) BETWEEN '" . $date . "' 
                            AND '" . $date2 . "' " . $qString . $outletFilter . $customerFilter . $orderFilter . " 
                        GROUP BY
                            salesMaster.menuSalesID, salesMaster.wareHouseAutoID ";


        return $this->db->query($querySalesDetail)->result_array();
    }

    function get_report_employeePerformance($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $querySalesDetail = "SELECT
                            salesMaster.menuSalesID AS salesMasterMenuSalesID,
                            DATE_FORMAT( salesMaster.createdDateTime, '%d-%m-%Y' ) AS salesMasterCreatedDate,
                            DATE_FORMAT( salesMaster.createdDateTime, '%h-%i %p' ) AS salesMasterCreatedTime,
                            wmaster.wareHouseDescription AS whouseName,
                            employee.EmpShortCode AS menuCreatedUser,
                            salesMaster.grossTotal,
                            salesMaster.grossAmount,
                            salesMaster.companyLocalCurrencyDecimalPlaces AS companyLocalDecimal,
                            invoiceCode,
                            salesMaster.discountPer,
                            salesMaster.discountAmount,
                            salesMaster.promotionDiscount,
                            salesMaster.deliveryCommission,
                            salesMaster.deliveryCommissionAmount,
                            salesMaster.subTotal AS billNetTotal,
                            salesMaster.promotionDiscount,
                            salesMaster.wareHouseAutoID as wareHouseAutoID,
                      
                            payment.paymentConfigMasterID,
                            payment.amount,
                            payment.customerAutoID, 
                            payment.RCGC, 
                            payment.Cash, 
                            payment.CreditNote, 
                            payment.MasterCard, 
                            payment.VisaCard, 
                            payment.GiftCard, 
                            payment.AMEX, 
                            payment.CreditSales, 
                            payment.FriMi, 
                            payment.JavaApp, 
                            payment.AliPay, 
                            payment.customerName,
                            promotionTypeP.customerName AS PromotionalDiscountType,
                            promotionTypeD.customerName AS DeliveryCommissionType 
                        FROM
                            srp_erp_pos_menusalesmaster AS salesMaster
                            LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID
                            LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID
                            LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID
                            LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID
                            LEFT JOIN (
                                    SELECT
                                        paymentConfigMasterID,
                                        amount,
                                        menuSalesID,
                                        srp_erp_pos_menusalespayments.customerAutoID,
                                        srp_erp_customermaster.customerName,
                                        sum( CASE WHEN paymentConfigMasterID = '26' THEN amount ELSE 0 END ) RCGC,
                                        sum( CASE WHEN paymentConfigMasterID = '1' THEN amount ELSE 0 END ) Cash,
                                        sum( CASE WHEN paymentConfigMasterID = '2' THEN amount ELSE 0 END ) CreditNote,
                                        sum( CASE WHEN paymentConfigMasterID = '3' THEN amount ELSE 0 END ) MasterCard,
                                        sum( CASE WHEN paymentConfigMasterID = '4' THEN amount ELSE 0 END ) VisaCard,
                                        sum( CASE WHEN paymentConfigMasterID = '5' THEN amount ELSE 0 END ) GiftCard,
                                        sum( CASE WHEN paymentConfigMasterID = '6' THEN amount ELSE 0 END ) AMEX,
                                        sum( CASE WHEN paymentConfigMasterID = '7' THEN amount ELSE 0 END ) CreditSales,
                                        sum( CASE WHEN paymentConfigMasterID = '27' THEN amount ELSE 0 END ) FriMi,
                                        sum( CASE WHEN paymentConfigMasterID = '25' THEN amount ELSE 0 END ) JavaApp,
                                        sum( CASE WHEN paymentConfigMasterID = '28' THEN amount ELSE 0 END ) AliPay,
                                        wareHouseAutoID	 
                                    FROM
                                        srp_erp_pos_menusalespayments
                                       
                                        LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID 
                                    GROUP BY
                                        menuSalesID, wareHouseAutoID  
                            ) payment ON salesMaster.menuSalesID = payment.menuSalesID  AND payment.wareHouseAutoID = salesMaster.wareHouseAutoID
                        WHERE
                            salesMaster.isVoid = 0 
                            AND salesMaster.isHold = 0 
                            AND salesMaster.companyID = " . current_companyID() . " 
                            AND salesMaster.createdDateTime BETWEEN '" . $date . "' 
                            AND '" . $date2 . "' " . $qString . $outletFilter . " 
                        GROUP BY
                            salesMaster.menuSalesID, salesMaster.wareHouseAutoID ";


        return $this->db->query($querySalesDetail)->result_array();
    }

    function get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        return $this->get_srp_erp_pos_menusalesitems_drillDown($invoiceID, $outletID);
    }

    function get_srp_erp_pos_menusalesmaster_salesDetailReport($id, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $this->db->select("menuSales.*, cType.customerDescription, wmaster.wareHouseDescription,wmaster.warehouseAddress,wmaster.warehouseTel,cusm.CustomerName as cusname,cusm.CustomerAddress1 as cusaddress,cusm.customerTelephone as custel");
        $this->db->from("srp_erp_pos_menusalesmaster menuSales");
        $this->db->join('srp_erp_customertypemaster cType', 'cType.customerTypeID = menuSales.customerTypeID', 'left'); /*customerTypeID*/
        $this->db->join('srp_erp_pos_customermaster cusm', 'cusm.posCustomerAutoID = menuSales.customerID', 'left');
        $this->db->join('srp_erp_warehousemaster wmaster', 'wmaster.wareHouseAutoID = menuSales.wareHouseAutoID', 'left'); /*customerTypeID*/
        $this->db->where('menuSales.menuSalesID', $id);
        $this->db->where('menuSales.wareHouseAutoID', $outletID);
        //$this->db->where('menuSales.wareHouseAutoID', current_warehouseID());
        $result = $this->db->get()->row_array();
        return $result;
    }

    function get_pos_customer($customerID)
    {
        $r = $this->db->select('*')
            ->from('srp_erp_pos_customermaster')
            ->where('posCustomerAutoID', $customerID)
            ->get()->row_array();
        return $r;
    }

    function get_tableList($status = array())
    {

        $this->db->select('diningTableAutoID, diningTableDescription, noOfSeats, diningRoomMasterID');
        $this->db->from('srp_erp_pos_diningtables');
        if (!empty($status)) {
            foreach ($status as $val) {
                $this->db->or_where('status', $val);
            }
        }
        $this->db->where('companyID', current_companyID());
        $this->db->where('segmentID', get_outletID());
        $result = $this->db->get()->result_array();
        return $result;
    }

    function validate_tableOrder()
    {
        $menuSalesID = $this->input->post('menuSalesID');
        $tableID = $this->input->post('id');
        $this->db->select("*");
        $this->db->from("srp_erp_pos_diningtables");
        $this->db->where('diningTableAutoID', $tableID);
        $this->db->where('status', 1);
        $diningTableAutoID = $this->db->get()->row('diningTableAutoID');
        if ($diningTableAutoID) {
            return false;
        } else {
            return true;
        }

        /*        } else {
                    return false;
                }*/
    }

    function update_menuSalesMasterTableID()
    {
        $menuSalesID = $this->input->post('menuSalesID');
        $tableID = $this->input->post('id');
        $this->db->where('menuSalesID', $menuSalesID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', array('tableID' => $tableID));
        return $result;
    }

    function update_diningTableStatus()
    {
        /*
        $this->db->select("tableID");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where('isHold', 1);
        $this->db->where('isVoid', 0);
        $this->db->where('menuSalesID', $menuSalesID);
        $tableID = $this->db->get()->row('tableID');


        $result = false;
        if (!$tableID) {*/
        $tableID = $this->input->post('id');
        $this->db->where('diningTableAutoID', $tableID);
        $data['status'] = 1;
        $data['tmp_menuSalesID'] = $this->input->post('menuSalesID');
        $result = $this->db->update('srp_erp_pos_diningtables', $data);

        /* }*/

        return $result;
    }

    function get_diningTableUsed()
    {
        $this->db->select('msm.menuSalesID, dt.diningTableAutoID, concat(msm.invoiceCode,"<br/>",crew.crewLastName) as invoiceCode, dt.diningTableDescription as tableName, dt.status, dt.tmp_crewID');
        $this->db->from('srp_erp_pos_diningtables dt');
        $this->db->join('srp_erp_pos_menusalesmaster msm', 'msm.menuSalesID=dt.tmp_menuSalesID', 'left');
        $this->db->join('srp_erp_pos_crewmembers crew', 'crew.crewMemberID = dt.tmp_crewID', 'left');
        $this->db->where('dt.status', 1);
        $this->db->where('dt.companyID', current_companyID());
        $this->db->where('dt.segmentID', get_outletID());
        $result = $this->db->get()->result_array();
        return $result;
    }

    function update_diningTableReset($tableID)
    {
        $this->db->where('diningTableAutoID', $tableID);
        $result = $this->db->update('srp_erp_pos_diningtables', array('status' => 0, 'tmp_menuSalesID' => null, 'tmp_crewID' => null, 'tmp_numberOfPacks' => 0));
        return $result;
    }

    function get_srp_erp_pos_paymentglconfigmaster2($outlets = null)
    {
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND srp_erp_pos_paymentglconfigdetail.warehouseID IN(" . $outlets . ")";
        }

        /* $this->db->select("autoID,description,sortOrder");
         $this->db->from("srp_erp_pos_paymentglconfigmaster");
         $this->db->join("srp_erp_pos_paymentglconfigdetail", "srp_erp_pos_paymentglconfigdetail.paymentConfigMasterID = srp_erp_pos_paymentglconfigmaster.autoID", "inner");
         $this->db->order_by("sortOrder ASC");
         $result = $this->db->get()->result_array();
         return $result;*/

        $result = "SELECT
                            autoID,description,sortOrder
                        FROM
                            srp_erp_pos_paymentglconfigmaster
                            INNER JOIN srp_erp_pos_paymentglconfigdetail ON srp_erp_pos_paymentglconfigdetail.paymentConfigMasterID = srp_erp_pos_paymentglconfigmaster.autoID
                        WHERE
                             srp_erp_pos_paymentglconfigdetail.companyID = " . current_companyID() . "   " . $outletFilter . "
                        Group BY
                            srp_erp_pos_paymentglconfigdetail.paymentConfigMasterID
                        Order BY
                            sortOrder ASC ";


        return $this->db->query($result)->result_array();
    }

    function get_report_salesDetailReport2($date, $date2, $cashier = null, $outlets = null, $customers = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $customerFilter = '';
        if ($customers != null) {
            $customerFilter = " AND ((deliveryorders.posCustomerAutoID IN(" . $customers . ") 
            OR salesMaster.customerID IN(" . $customers . "))
            OR payment.posCustomerAutoID IN (" . $customers . "))";
        }

        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }

        //$querySalesDetail = "SELECT salesMaster.menuSalesID as salesMasterMenuSalesID,salesMaster.isDelivery,salesMaster.isHold,deliveryorders.isDispatched as isDispatched,DATE_FORMAT(salesMaster.createdDateTime, '%d-%m-%Y') AS salesMasterCreatedDate,DATE_FORMAT(salesMaster.createdDateTime, '%h-%i %p') AS salesMasterCreatedTime,wmaster.wareHouseDescription as whouseName,wmaster.wareHouseCode as wareHouseCode,employee.EmpShortCode as menuCreatedUser,salesMaster.grossTotal,salesMaster.grossAmount,salesMaster.companyLocalCurrencyDecimalPlaces as companyLocalDecimal,invoiceCode,salesMaster.discountPer,salesMaster.discountAmount,salesMaster.promotionDiscount,salesMaster.deliveryCommission,salesMaster.deliveryCommissionAmount,salesMaster.subTotal as billNetTotal,salesMaster.promotionDiscount,payment.*,promotionTypeP.customerName as PromotionalDiscountType,promotionTypeD.customerName as DeliveryCommissionType,salesMaster.isDelivery, salesMaster.isHold,COUNT(deliveryorders.menuSalesMasterID) AS isDelivery1,pos_cmaster.CustomerName AS DeliveryCustomerName,deliveryorders.posCustomerAutoID AS DeliveryCustomerID,CASE deliveryorders.isDispatched WHEN 0 THEN 'No' WHEN deliveryorders.isDispatched IS NULL THEN 'Yes' WHEN deliveryorders.isDispatched = '' THEN 'Yes' WHEN 1 THEN 'Yes' END AS deliveryordersDispatched FROM srp_erp_pos_menusalesmaster AS salesMaster LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID LEFT JOIN srp_erp_pos_deliveryorders deliveryorders ON deliveryorders.menuSalesMasterID = salesMaster.menuSalesID LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID LEFT JOIN srp_erp_pos_customermaster pos_cmaster ON pos_cmaster.posCustomerAutoID = deliveryorders.posCustomerAutoID LEFT JOIN (Select paymentConfigMasterID,amount,menuSalesID,srp_erp_pos_menusalespayments.customerAutoID,srp_erp_customermaster.customerName, sum(CASE WHEN paymentConfigMasterID = '1' THEN amount ELSE 0 END) Cash,sum(CASE WHEN paymentConfigMasterID = '2' THEN amount ELSE 0 END) CreditNote,sum(CASE WHEN paymentConfigMasterID = '3' THEN amount ELSE 0 END) MasterCard,sum(CASE WHEN paymentConfigMasterID = '4' THEN amount ELSE 0 END) VisaCard,sum(CASE WHEN paymentConfigMasterID = '5' THEN amount ELSE 0 END) GiftCard,sum(CASE WHEN paymentConfigMasterID = '6' THEN amount ELSE 0 END) AMEX,sum(CASE WHEN paymentConfigMasterID = '7' THEN amount ELSE 0 END) CreditSales,sum(CASE WHEN paymentConfigMasterID = '25' THEN amount ELSE 0 END) JavaApp FROM srp_erp_pos_menusalespayments LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID GROUP BY menuSalesID) payment ON salesMaster.menuSalesID = payment.menuSalesID WHERE salesMaster.isVoid = 0   AND salesMaster.companyID = " . current_companyID() . " AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'" . $qString . $outletFilter . " GROUP BY salesMaster.menuSalesID HAVING (isHold = 0 OR isDelivery1=1) ";
        $querySalesDetail = "SELECT
deliveryorders.deliveryOrderID,
salesMaster.menuSalesID as salesMasterMenuSalesID,
salesMaster.isDelivery,
salesMaster.isHold,
deliveryorders.isDispatched as isDispatched,
DATE_FORMAT(IF(deliveryorders.deliveryOrderID IS NOT NULL, deliveryorders.deliveryDate, salesMaster.createdDateTime), '%d-%m-%Y' ) AS salesMasterCreatedDate,
DATE_FORMAT(IF(deliveryorders.deliveryOrderID IS NOT NULL, deliveryorders.deliveryTime, salesMaster.createdDateTime), '%h-%i %p' ) AS salesMasterCreatedTime,
deliveryorders.deliveryDate as ddate,
deliveryorders.deliveryTime as dtime, 
salesMaster.createdDateTime k, 
IF(deliveryorders.deliveryOrderID IS NOT NULL, deliveryorders.deliveryDate, salesMaster.createdDateTime) as rptDate,
wmaster.wareHouseDescription as whouseName,
wmaster.wareHouseCode as wareHouseCode,
employee.EmpShortCode as menuCreatedUser,
salesMaster.grossTotal,
salesMaster.grossAmount,
salesMaster.companyLocalCurrencyDecimalPlaces as companyLocalDecimal,
invoiceCode,
salesMaster.discountPer,
salesMaster.discountAmount,
salesMaster.promotionDiscount,
salesMaster.deliveryCommission,
salesMaster.deliveryCommissionAmount,
salesMaster.subTotal as billNetTotal,
salesMaster.promotionDiscount,
payment.*,
promotionTypeP.customerName as PromotionalDiscountType,
promotionTypeD.customerName as DeliveryCommissionType,
salesMaster.isDelivery, 
salesMaster.isHold,
COUNT(deliveryorders.menuSalesMasterID) AS isDelivery1,
pos_cmaster.CustomerName AS DeliveryCustomerName,
deliveryorders.posCustomerAutoID AS DeliveryCustomerID,
CASE deliveryorders.isDispatched WHEN 0 THEN 'No' WHEN deliveryorders.isDispatched IS NULL THEN 'Yes' WHEN deliveryorders.isDispatched = '' THEN 'Yes' WHEN 1 THEN 'Yes' END AS deliveryordersDispatched 
FROM srp_erp_pos_menusalesmaster AS salesMaster 
LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID 
LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID 
LEFT JOIN srp_erp_pos_deliveryorders deliveryorders ON deliveryorders.menuSalesMasterID = salesMaster.menuSalesID 
LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID 
LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID 
LEFT JOIN srp_erp_pos_customermaster pos_cmaster ON pos_cmaster.posCustomerAutoID = deliveryorders.posCustomerAutoID 
LEFT JOIN 
    (Select paymentConfigMasterID,
    amount,
    menuSalesID,
    srp_erp_pos_menusalespayments.customerAutoID,
    srp_erp_pos_customermaster.posCustomerAutoID,
    srp_erp_customermaster.customerName,
    srp_erp_customermaster.customerTelephone, 
 sum( CASE WHEN paymentConfigMasterID = '26' THEN amount ELSE 0 END ) RCGC,
                                        sum( CASE WHEN paymentConfigMasterID = '1' THEN amount ELSE 0 END ) Cash,
                                        sum( CASE WHEN paymentConfigMasterID = '2' THEN amount ELSE 0 END ) CreditNote,
                                        sum( CASE WHEN paymentConfigMasterID = '3' THEN amount ELSE 0 END ) MasterCard,
                                        sum( CASE WHEN paymentConfigMasterID = '4' THEN amount ELSE 0 END ) VisaCard,
                                        sum( CASE WHEN paymentConfigMasterID = '5' THEN amount ELSE 0 END ) GiftCard,
                                        sum( CASE WHEN paymentConfigMasterID = '6' THEN amount ELSE 0 END ) AMEX,
                                        sum( CASE WHEN paymentConfigMasterID = '7' THEN amount ELSE 0 END ) CreditSales,
                                        sum( CASE WHEN paymentConfigMasterID = '27' THEN amount ELSE 0 END ) FriMi,
                                        sum( CASE WHEN paymentConfigMasterID = '25' THEN amount ELSE 0 END ) JavaApp,
                                        sum( CASE WHEN paymentConfigMasterID = '28' THEN amount ELSE 0 END ) AliPay,
                                        sum( CASE WHEN paymentConfigMasterID = '29' THEN amount ELSE 0 END ) Thawani,
                                        sum( CASE WHEN paymentConfigMasterID = '30' THEN amount ELSE 0 END ) eFloos,
                                        sum( CASE WHEN paymentConfigMasterID = '31' THEN amount ELSE 0 END ) Talabat,
                                        sum( CASE WHEN paymentConfigMasterID = '32' THEN amount ELSE 0 END ) RoundOff,
                                        sum( CASE WHEN paymentConfigMasterID = '33' THEN amount ELSE 0 END ) DFCC,
                                        sum( CASE WHEN paymentConfigMasterID = '34' THEN amount ELSE 0 END ) UberEats,
                                        sum( CASE WHEN paymentConfigMasterID = '35' THEN amount ELSE 0 END ) BankDeposit,
                                        sum( CASE WHEN paymentConfigMasterID = '38' THEN amount ELSE 0 END ) Coupon,
                                        sum( CASE WHEN paymentConfigMasterID = '39' THEN amount ELSE 0 END ) LBECoupon,
                                        sum( CASE WHEN paymentConfigMasterID = '36' THEN amount ELSE 0 END ) Akeed,
                                        sum( CASE WHEN paymentConfigMasterID = '37' THEN amount ELSE 0 END ) Daleel,
                                        sum( CASE WHEN paymentConfigMasterID = '40' THEN amount ELSE 0 END ) Voucher,
                                        sum( CASE WHEN paymentConfigMasterID = '42' THEN amount ELSE 0 END ) Loyalty,
                                        
                                        sum( CASE WHEN paymentConfigMasterID = '51' THEN amount ELSE 0 END ) NAPS,
                                        sum( CASE WHEN paymentConfigMasterID = '52' THEN amount ELSE 0 END ) QNBDebit,
                                        sum( CASE WHEN paymentConfigMasterID = '53' THEN amount ELSE 0 END ) GCCNET,
                                        sum( CASE WHEN paymentConfigMasterID = '46' THEN amount ELSE 0 END ) TMDone,
                                        sum( CASE WHEN paymentConfigMasterID = '54' THEN amount ELSE 0 END ) TMDoneCash,
                                        sum( CASE WHEN paymentConfigMasterID = '43' THEN amount ELSE 0 END ) TalabatCash 
    FROM srp_erp_pos_menusalespayments 
        LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID 
        LEFT JOIN srp_erp_pos_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_pos_customermaster.customerAutoID 
        GROUP BY menuSalesID) payment ON salesMaster.menuSalesID = payment.menuSalesID 
        WHERE salesMaster.isVoid = 0   
        AND salesMaster.companyID = " . current_companyID() . " " . $qString . $outletFilter . $customerFilter . $orderFilter . " 
        GROUP BY salesMaster.menuSalesID 
        HAVING (isHold = 0 OR deliveryOrderID is not null) 
        AND (rptDate BETWEEN '$date' AND '$date2') ";

        return $this->db->query($querySalesDetail)->result_array();
    }

    function get_srp_erp_pos_menusalesitems_drillDown($invoiceID, $outletID = 0)
    {
        $path = base_url();
        $this->db->select("sales.menuSalesID, sales.menuSalesItemID, category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, sales.menuSalesPrice as sellingPrice, sales.qty , sales.discountPer, sales.discountAmount, menuMaster.menuMasterID,sales.remarkes, sales.menuSalesPrice as pricewithoutTax, sales.totalMenuTaxAmount as totalTaxAmount, sales.totalMenuServiceCharge as totalServiceCharge,menu.isTaxEnabled , size.code as sizeCode, size.description as sizeDescription");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster menu", "menu.warehouseMenuID = sales.warehouseMenuID");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_menusize size", "size.menuSizeID = menuMaster.menuSizeID", "left");
        $this->db->where('sales.menuSalesID', $invoiceID);
        $this->db->where('sales.id_store', $outletID);
        $result = $this->db->get()->result_array();
        return $result;
    }

    function update_isSampleBillPrintFlag($invoiceID, $outletID)
    {
        if (!empty($invoiceID)) {
            $this->db->where('menuSalesID', $invoiceID);
            $this->db->where('id_store', $outletID);
            return $this->db->update('srp_erp_pos_menusalesitems', array('isSamplePrinted' => 1));
        } else {
            return false;
        }
    }

    function load_hold_refno()
    {
        $menuSalesID = $this->input->post('menuSalesID');
        $this->db->select("holdRemarks");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where('menuSalesID', $menuSalesID);
        $result = $this->db->get()->row_array();

        return $result;
    }

    function submitBOT()
    {
        $invoiceID = $this->input->post('id');
        $data['BOT'] = 1;
        $data['BOTCreatedUser'] = current_userID();
        $data['BOTCreatedDatetime'] = format_date_mysql_datetime();
        $this->db->where('menuSalesID', $invoiceID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        if ($result) {
            return array('error' => 0, 'e_type' => 's', 'message' => 'Successfully submitted to BOT.');
        } else {
            return array('error' => 1, 'e_type' => 'e', 'message' => 'error while submitting to BOT.');
        }
    }

    function clear_pos_tables($id)
    {
        $invoice = $this->db->select('*')
            ->from('srp_erp_pos_menusalesmaster')
            ->where('menuSalesID', $id)
            ->get()
            ->row_array();
        $tableID = $invoice['tableID'];

        if ($tableID) {
            $this->db->where('diningTableAutoID', $tableID);
            $result = $this->db->update('srp_erp_pos_diningtables', array('status' => 0, 'tmp_menuSalesID' => null, 'tmp_crewID' => null, 'tmp_numberOfPacks' => 0));
            return $result;
        } else {
            return false;
        }
    }

    function fetch_pos_customer_details()
    {
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyCode = $this->common_data['company_data']['company_code'];
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        //$data = $this->db->query('SELECT mainCategory,mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT( IFNULL(itemDescription,"empty"), " - ", IFNULL(itemSystemCode,"empty"), " - ", IFNULL(partNo,"empty")  , " - ", IFNULL(seconeryItemCode,"empty")) AS "Match" , isSubitemExist FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '") AND financeCategory != 3 AND companyCode = "' . $companyCode . '" AND isActive="1"')->result_array();
        $data = $this->db->query('SELECT posCustomerAutoID,CustomerName,CustomerAddress1,customerTelephone,CONCAT( customerTelephone, "- ",IFNULL(CustomerName,\'\')  ) AS cusdet,customerEmail,customerCountryCode,customerCountry,IFNULL(loyalitycard.barcode,0) as loyalityno FROM srp_erp_pos_customermaster
       	LEFT JOIN (SELECT barcode,customerID FROM srp_erp_pos_loyaltycard where customerType = 1) loyalitycard ON loyalitycard.customerID = srp_erp_pos_customermaster.posCustomerAutoID 
        WHERE (customerTelephone LIKE "' . $search_string . '" OR CustomerName LIKE "' . $search_string . '") AND srp_erp_pos_customermaster.companyID="' . $companyID . '"')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["cusdet"], 'data' => $val['customerTelephone'], 'posCustomerAutoID' => $val["posCustomerAutoID"], 'CustomerName' => $val['CustomerName'], 'CustomerAddress1' => $val['CustomerAddress1'], 'customerTelephone' => $val['customerTelephone'], 'customerEmail' => $val['customerEmail'], 'customerCountry' => $val['customerCountry'], 'customerCountryCode' => $val['customerCountryCode'], 'loyalityno' => $val['loyalityno']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }


    function get_pendingPaymentsReport($dateFrom, $Outlets = null, $Customers = null)
    {
        /*$outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }*/
        $dateFrom = '';
        $dateto = '';
        $tmpFromDate = $this->input->post('filterFrom');
        $tmptoDate = $this->input->post('filterto');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        if (isset($tmptoDate) && !empty($tmptoDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmptoDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateto = $date;
        } else {
            $dateto = date('Y-m-d');
        }


        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND srp_erp_pos_menusalesmaster.wareHouseAutoID IN(" . $Outlets . ")";
        }

        $customersFilter = '';
        $doFilter = '';
        if ($Customers != null) {
            $customersFilter = " AND mp.customerAutoID IN(" . $Customers . ")";
            $doFilter = " AND srp_erp_pos_deliveryorders.posCustomerAutoID  IN(" . $Customers . ")";
        }


        $companyID = current_companyID();
        $q = "SELECT
	srp_erp_pos_menusalesmaster.invoiceCode AS billNo,
	srp_erp_customermaster.CustomerName AS customerDetal,
	srp_erp_customermaster.customerTelephone AS customerTelephone,
 srp_erp_customerinvoicemaster.invoiceCode,
 srp_erp_customerinvoicemaster.invoiceAutoID AS invID,
 srp_erp_customerinvoicemaster.transactionAmount,
 srp_erp_customerinvoicemaster.invoiceDate as dat,
 SUM(receipt.receiptamnt) AS receiptamnts,
 receipt.invoiceAutoID
FROM
	srp_erp_pos_menusalesmaster
INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_pos_menusalesmaster.documentMasterAutoID = srp_erp_customerinvoicemaster.invoiceAutoID
INNER JOIN srp_erp_pos_shiftdetails ON srp_erp_pos_menusalesmaster.shiftID = srp_erp_pos_shiftdetails.shiftID
LEFT JOIN srp_erp_pos_menusalespayments mp on srp_erp_pos_menusalesmaster.menuSalesID = mp.menuSalesID
LEFT JOIN srp_erp_customermaster ON mp.customerAutoID = srp_erp_customermaster.customerAutoID

LEFT JOIN (
	SELECT
		srp_erp_customerreceiptdetail.receiptVoucherAutoId,
		SUM(srp_erp_customerreceiptdetail.transactionAmount) AS receiptamnt,
		srp_erp_customerreceiptdetail.invoiceAutoID
	FROM
		srp_erp_customerreceiptdetail
LEFT JOIN srp_erp_customerreceiptmaster on srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
	WHERE
		srp_erp_customerreceiptdetail.companyID = $companyID
		AND srp_erp_customerreceiptmaster.approvedYN = 1
	GROUP BY
		receiptVoucherAutoId
) receipt ON receipt.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID
WHERE
	isClosed = 1
AND isCreditSales = 1
AND isVoid = 0
AND isHold = 0
AND srp_erp_pos_menusalesmaster.companyID = $companyID
AND DATE_FORMAT(srp_erp_customerinvoicemaster.invoiceDate,'%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateto'

        " . $outletsFilter . "
        " . $customersFilter . "
        GROUP BY
srp_erp_customerinvoicemaster.invoiceAutoID 

	Union All

SELECT
	srp_erp_pos_menusalesmaster.invoiceCode AS billNo,
	srp_erp_pos_customermaster.CustomerName AS customerDetal,
	srp_erp_pos_customermaster.customerTelephone AS customerTelephone,
	'Delivery Order' AS invoiceCode,
	'-' AS invID,
	IFNULL(srp_erp_pos_menusalesmaster.subTotal,0) AS transactionAmount,
	srp_erp_pos_deliveryorders.deliveryDate as dat,
	IFNULL(menupayment.amnt,0) AS receiptamnts,
	'-' AS invoiceAutoID 
FROM
	srp_erp_pos_deliveryorders
	LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_deliveryorders.posCustomerAutoID = srp_erp_pos_customermaster.posCustomerAutoID
	LEFT JOIN srp_erp_pos_menusalesmaster ON srp_erp_pos_deliveryorders.menuSalesMasterID = srp_erp_pos_menusalesmaster.menuSalesID
	LEFT JOIN ( SELECT menuSalesID, sum( amount ) AS amnt FROM srp_erp_pos_menusalespayments GROUP BY menuSalesID ) menupayment ON menupayment.menuSalesID = srp_erp_pos_deliveryorders.menuSalesMasterID 
WHERE
	srp_erp_pos_deliveryorders.companyID = $companyID 
AND DATE_FORMAT(srp_erp_pos_deliveryorders.deliveryDate,'%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateto'

        " . $outletsFilter . "
        " . $doFilter . "

";
        $result = $this->db->query($q)->result_array();

        //echo $this->db->last_query();

        return $result;
    }

    function loadPendingPaymentDD()
    {
        $companyID = current_companyID();
        $invoiceID = $this->input->post('invoiceID');
        $q = "SELECT
	srp_erp_customerreceiptmaster.RVcode,
	srp_erp_customerreceiptmaster.RVdate,
	SUM(
		srp_erp_customerreceiptdetail.transactionAmount
	) AS receiptamnt,
	srp_erp_customerreceiptmaster.receiptVoucherAutoId
FROM
	`srp_erp_customerreceiptdetail`
LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
WHERE
	invoiceAutoID = $invoiceID
	AND approvedYN=1
AND srp_erp_customerreceiptdetail.companyID = $companyID
GROUP BY
	srp_erp_customerreceiptdetail.receiptVoucherAutoId ";
        $result = $this->db->query($q)->result_array();

        //echo $this->db->last_query();

        return $result;
    }

    function get_gift_card_details($date_to = null, $outlets = null, $customers = null)
    {
        $where_to_date = '';
        if ($date_to != null) {
            $where_to_date = "AND (srp_erp_pos_cardtopup.createdDateTime<='$date_to')";
        }
        $this->db->select("srp_erp_pos_giftcardmaster.barcode,
        srp_erp_warehousemaster.wareHouseDescription,
        srp_erp_pos_cardissue.createdUserName as issued_user,
        srp_erp_pos_customermaster.CustomerName,
        srp_erp_pos_customermaster.customerTelephone,
        IFNULL(srp_erp_pos_cardissue.cardIssueID,0) as cardIssueID,
        IFNULL((select sum(topUpAmount) from srp_erp_pos_cardtopup where topUpAmount<0 AND srp_erp_pos_cardtopup.cardMasterID=srp_erp_pos_giftcardmaster.cardMasterID $where_to_date),0) as redeemed_amount,
        IFNULL((select sum(topUpAmount) from srp_erp_pos_cardtopup where topUpAmount>0 AND srp_erp_pos_cardtopup.cardMasterID=srp_erp_pos_giftcardmaster.cardMasterID $where_to_date),0) as topup_amount,
        IFNULL(sum(srp_erp_pos_cardtopup.topUpAmount),0) as total_amount");
        $this->db->from("srp_erp_pos_giftcardmaster");
        $this->db->join("srp_erp_warehousemaster", "srp_erp_pos_giftcardmaster.outletID = srp_erp_warehousemaster.wareHouseAutoID", "left");
        $this->db->join("srp_erp_pos_cardissue", "srp_erp_pos_giftcardmaster.cardMasterID = srp_erp_pos_cardissue.cardMasterID", "left");
        $this->db->join("srp_erp_pos_cardtopup", "(srp_erp_pos_giftcardmaster.cardMasterID = srp_erp_pos_cardtopup.cardMasterID) $where_to_date", "left");
        $this->db->join("srp_erp_pos_customermaster", "srp_erp_pos_cardissue.posCustomerAutoID = srp_erp_pos_customermaster.posCustomerAutoID", "left");
        $this->db->where('srp_erp_warehousemaster.companyID', current_companyID());
        if ($outlets != null) {
            $this->db->where_in('srp_erp_warehousemaster.wareHouseAutoID', $outlets);
        }
        $this->db->group_by('srp_erp_pos_giftcardmaster.cardMasterID')->group_by('srp_erp_pos_giftcardmaster.outletID');
        $result = $this->db->get()->result_array();

        return $result;
    }

    function get_gift_card_topup_redeem_details($date_from = null, $date_to = null, $outlets = null, $customers = null)
    {
        $where_date = '';
        if ($date_to != null && $date_from != null) {
            $where_date = "AND (srp_erp_pos_cardtopup.createdDateTime>='$date_from') AND (srp_erp_pos_cardtopup.createdDateTime<='$date_to')";
        }
        $this->db->select("srp_erp_pos_giftcardmaster.barcode,
        srp_erp_warehousemaster.wareHouseDescription,
        srp_erp_pos_cardissue.createdUserName as issued_user,
        srp_erp_pos_customermaster.CustomerName,
        srp_erp_pos_customermaster.customerTelephone,
        srp_erp_pos_cardtopup.menuSalesID as invoice,
        srp_erp_pos_cardtopup.giftCardReceiptID as receipt,
        srp_erp_pos_cardtopup.outletID as outletID,
        IFNULL(srp_erp_pos_cardtopup.topUpAmount,0) as amount,isRefund");
        $this->db->from("srp_erp_pos_giftcardmaster");
        $this->db->join("srp_erp_pos_cardissue", "srp_erp_pos_giftcardmaster.cardMasterID = srp_erp_pos_cardissue.cardMasterID");
        $this->db->join("srp_erp_pos_cardtopup", "(srp_erp_pos_giftcardmaster.cardMasterID = srp_erp_pos_cardtopup.cardMasterID) $where_date");
        $this->db->join("srp_erp_warehousemaster", "srp_erp_pos_giftcardmaster.outletID = srp_erp_warehousemaster.wareHouseAutoID", "left");
        $this->db->join("srp_erp_pos_customermaster", "srp_erp_pos_cardissue.posCustomerAutoID = srp_erp_pos_customermaster.posCustomerAutoID", "left");
        if ($outlets != null) {
            $this->db->where_in('srp_erp_warehousemaster.wareHouseAutoID', $outlets);
        }
        if ($customers != null) {
            $this->db->where_in('srp_erp_pos_cardissue.posCustomerAutoID', $customers);
        }
        $this->db->where('srp_erp_warehousemaster.companyID', current_companyID());
        $result = $this->db->get()->result_array();
        //dd($this->db->last_query());
        return $result;
    }

    function get_item_usage_details($date_from = null, $date_to = null, $outlets = null)
    {
        /*$this->db->select("itemSystemCode,
        seconeryItemCode,
        itemDescription,
        srp_erp_pos_menusalesitemdetails.UOM as uom,
        sum(srp_erp_pos_menusalesitemdetails.qty) as usage_qty");
        $this->db->from("srp_erp_itemmaster");
        $this->db->join("srp_erp_pos_menusalesitemdetails", "srp_erp_itemmaster.itemAutoID = srp_erp_pos_menusalesitemdetails.itemAutoID");
        if ($outlets != null) {
            $this->db->where_in('srp_erp_pos_menusalesitemdetails.wareHouseAutoID', $outlets);
        }
        if ($date_from != null && $date_to != null) {
            $this->db->where("srp_erp_pos_menusalesitemdetails.createdDateTime>='$date_from' AND srp_erp_pos_menusalesitemdetails.createdDateTime<='$date_to'");
        }
        $this->db->group_by('srp_erp_pos_menusalesitemdetails.itemAutoID');
        $result = $this->db->get()->result_array();*/
        $companyid = current_companyID();

        $result = $this->db->query("SELECT `itemSystemCode`,`seconeryItemCode`,`itemDescription`,uommaster.UnitShortCode as  uom,
                                    	sum(( srp_erp_pos_menusalesitemdetails.qty * menusalesQty )/getUoMConvertion(srp_erp_pos_menusalesitemdetails.UOMID,srp_erp_itemmaster.defaultUnitOfMeasureID,srp_erp_pos_menusalesitemdetails.companyID)) AS usage_qty
                                        FROM `srp_erp_pos_menusalesitemdetails` LEFT JOIN srp_erp_itemmaster ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_pos_menusalesitemdetails`.`itemAutoID` 
	                                    LEFT JOIN srp_erp_unit_of_measure uommaster on uommaster.UnitID = srp_erp_itemmaster.defaultUnitOfMeasureID WHERE srp_erp_pos_menusalesitemdetails.companyID = {$companyid} 
                                        And	`srp_erp_pos_menusalesitemdetails`.`wareHouseAutoID` IN ($outlets) AND DATE(`srp_erp_pos_menusalesitemdetails`.`createdDateTime` ) BETWEEN  '{$date_from}'  And  '{$date_to}' 
	                                    GROUP BY `srp_erp_pos_menusalesitemdetails`.`itemAutoID`")->result_array();

        return $result;
    }

    function get_void_detail_report2($date, $date2, $cashier = null, $outlets = null, $customers = null, $orderTypes = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $customerFilter = '';
        if ($customers != null) {
            $customerFilter = " AND ((deliveryorders.posCustomerAutoID IN(" . $customers . ") 
            OR salesMaster.customerID IN(" . $customers . "))
            OR payment.posCustomerAutoID IN (" . $customers . "))";
        }

        $orderFilter = '';
        if ($orderTypes != null) {
            $orderFilter = " AND salesMaster.customerTypeID IN(" . $orderTypes . ")";
        }

        //$querySalesDetail = "SELECT salesMaster.menuSalesID as salesMasterMenuSalesID,salesMaster.isDelivery,salesMaster.isHold,deliveryorders.isDispatched as isDispatched,DATE_FORMAT(salesMaster.createdDateTime, '%d-%m-%Y') AS salesMasterCreatedDate,DATE_FORMAT(salesMaster.createdDateTime, '%h-%i %p') AS salesMasterCreatedTime,wmaster.wareHouseDescription as whouseName,wmaster.wareHouseCode as wareHouseCode,employee.EmpShortCode as menuCreatedUser,salesMaster.grossTotal,salesMaster.grossAmount,salesMaster.companyLocalCurrencyDecimalPlaces as companyLocalDecimal,invoiceCode,salesMaster.discountPer,salesMaster.discountAmount,salesMaster.promotionDiscount,salesMaster.deliveryCommission,salesMaster.deliveryCommissionAmount,salesMaster.subTotal as billNetTotal,salesMaster.promotionDiscount,payment.*,promotionTypeP.customerName as PromotionalDiscountType,promotionTypeD.customerName as DeliveryCommissionType,salesMaster.isDelivery, salesMaster.isHold,COUNT(deliveryorders.menuSalesMasterID) AS isDelivery1,pos_cmaster.CustomerName AS DeliveryCustomerName,deliveryorders.posCustomerAutoID AS DeliveryCustomerID,CASE deliveryorders.isDispatched WHEN 0 THEN 'No' WHEN deliveryorders.isDispatched IS NULL THEN 'Yes' WHEN deliveryorders.isDispatched = '' THEN 'Yes' WHEN 1 THEN 'Yes' END AS deliveryordersDispatched FROM srp_erp_pos_menusalesmaster AS salesMaster LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID LEFT JOIN srp_erp_pos_deliveryorders deliveryorders ON deliveryorders.menuSalesMasterID = salesMaster.menuSalesID LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID LEFT JOIN srp_erp_pos_customermaster pos_cmaster ON pos_cmaster.posCustomerAutoID = deliveryorders.posCustomerAutoID LEFT JOIN (Select paymentConfigMasterID,amount,menuSalesID,srp_erp_pos_menusalespayments.customerAutoID,srp_erp_customermaster.customerName, sum(CASE WHEN paymentConfigMasterID = '1' THEN amount ELSE 0 END) Cash,sum(CASE WHEN paymentConfigMasterID = '2' THEN amount ELSE 0 END) CreditNote,sum(CASE WHEN paymentConfigMasterID = '3' THEN amount ELSE 0 END) MasterCard,sum(CASE WHEN paymentConfigMasterID = '4' THEN amount ELSE 0 END) VisaCard,sum(CASE WHEN paymentConfigMasterID = '5' THEN amount ELSE 0 END) GiftCard,sum(CASE WHEN paymentConfigMasterID = '6' THEN amount ELSE 0 END) AMEX,sum(CASE WHEN paymentConfigMasterID = '7' THEN amount ELSE 0 END) CreditSales,sum(CASE WHEN paymentConfigMasterID = '25' THEN amount ELSE 0 END) JavaApp FROM srp_erp_pos_menusalespayments LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID GROUP BY menuSalesID) payment ON salesMaster.menuSalesID = payment.menuSalesID WHERE salesMaster.isVoid = 0   AND salesMaster.companyID = " . current_companyID() . " AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'" . $qString . $outletFilter . " GROUP BY salesMaster.menuSalesID HAVING (isHold = 0 OR isDelivery1=1) ";
        $querySalesDetail = "SELECT deliveryorders.deliveryOrderID,
salesMaster.menuSalesID as salesMasterMenuSalesID,
salesMaster.isDelivery,
salesMaster.isHold,
deliveryorders.isDispatched as isDispatched,
DATE_FORMAT(IF(deliveryorders.deliveryOrderID IS NOT NULL, deliveryorders.deliveryDate, salesMaster.createdDateTime), '%d-%m-%Y' ) AS salesMasterCreatedDate,
DATE_FORMAT(IF(deliveryorders.deliveryOrderID IS NOT NULL, deliveryorders.deliveryTime, salesMaster.createdDateTime), '%h-%i %p' ) AS salesMasterCreatedTime,
deliveryorders.deliveryDate as ddate,
deliveryorders.deliveryTime as dtime,
salesMaster.createdDateTime k,
IF(deliveryorders.deliveryOrderID IS NOT NULL, deliveryorders.deliveryDate, salesMaster.createdDateTime) as rptDate,
wmaster.wareHouseDescription as whouseName,
wmaster.wareHouseCode as wareHouseCode,
employee.EmpShortCode as menuCreatedUser,
salesMaster.grossTotal,
salesMaster.grossAmount,
salesMaster.companyLocalCurrencyDecimalPlaces as companyLocalDecimal,
invoiceCode,
salesMaster.discountPer,
salesMaster.discountAmount,
salesMaster.promotionDiscount,
salesMaster.deliveryCommission,
salesMaster.deliveryCommissionAmount,
salesMaster.subTotal as billNetTotal,
salesMaster.promotionDiscount,
payment.*,
promotionTypeP.customerName as PromotionalDiscountType,
promotionTypeD.customerName as DeliveryCommissionType,
salesMaster.isDelivery,
salesMaster.isHold,
COUNT(deliveryorders.menuSalesMasterID) AS isDelivery1,
pos_cmaster.CustomerName AS DeliveryCustomerName,
deliveryorders.posCustomerAutoID AS DeliveryCustomerID,
CASE deliveryorders.isDispatched WHEN 0 THEN 'No' WHEN deliveryorders.isDispatched IS NULL THEN 'Yes' WHEN deliveryorders.isDispatched = '' THEN 'Yes' WHEN 1 THEN 'Yes' END AS deliveryordersDispatched 
FROM srp_erp_pos_menusalesmaster AS salesMaster 
LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID 
LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID 
LEFT JOIN srp_erp_pos_deliveryorders deliveryorders ON deliveryorders.menuSalesMasterID = salesMaster.menuSalesID 
LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID 
LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID 
LEFT JOIN srp_erp_pos_customermaster pos_cmaster ON pos_cmaster.posCustomerAutoID = deliveryorders.posCustomerAutoID 
LEFT JOIN (Select paymentConfigMasterID,amount,menuSalesID,srp_erp_pos_menusalespayments.customerAutoID,srp_erp_pos_customermaster.posCustomerAutoID,srp_erp_customermaster.customerName,srp_erp_customermaster.customerTelephone, 

                                        sum( CASE WHEN paymentConfigMasterID = '26' THEN amount ELSE 0 END ) RCGC,
                                        sum( CASE WHEN paymentConfigMasterID = '1' THEN amount ELSE 0 END ) Cash,
                                        sum( CASE WHEN paymentConfigMasterID = '2' THEN amount ELSE 0 END ) CreditNote,
                                        sum( CASE WHEN paymentConfigMasterID = '3' THEN amount ELSE 0 END ) MasterCard,
                                        sum( CASE WHEN paymentConfigMasterID = '4' THEN amount ELSE 0 END ) VisaCard,
                                        sum( CASE WHEN paymentConfigMasterID = '5' THEN amount ELSE 0 END ) GiftCard,
                                        sum( CASE WHEN paymentConfigMasterID = '6' THEN amount ELSE 0 END ) AMEX,
                                        sum( CASE WHEN paymentConfigMasterID = '7' THEN amount ELSE 0 END ) CreditSales,
                                        sum( CASE WHEN paymentConfigMasterID = '27' THEN amount ELSE 0 END ) FriMi,
                                        sum( CASE WHEN paymentConfigMasterID = '25' THEN amount ELSE 0 END ) JavaApp,
                                        sum( CASE WHEN paymentConfigMasterID = '28' THEN amount ELSE 0 END ) AliPay,
                                        sum( CASE WHEN paymentConfigMasterID = '29' THEN amount ELSE 0 END ) Thawani,
                                        sum( CASE WHEN paymentConfigMasterID = '30' THEN amount ELSE 0 END ) eFloos,
                                        sum( CASE WHEN paymentConfigMasterID = '31' THEN amount ELSE 0 END ) Talabat,
                                        sum( CASE WHEN paymentConfigMasterID = '32' THEN amount ELSE 0 END ) RoundOff,
                                        sum( CASE WHEN paymentConfigMasterID = '33' THEN amount ELSE 0 END ) DFCC,
                                        sum( CASE WHEN paymentConfigMasterID = '34' THEN amount ELSE 0 END ) UberEats,
                                        sum( CASE WHEN paymentConfigMasterID = '35' THEN amount ELSE 0 END ) BankDeposit,
                                        sum( CASE WHEN paymentConfigMasterID = '38' THEN amount ELSE 0 END ) Coupon,
                                        sum( CASE WHEN paymentConfigMasterID = '39' THEN amount ELSE 0 END ) LBECoupon,
                                        sum( CASE WHEN paymentConfigMasterID = '36' THEN amount ELSE 0 END ) Akeed,
                                        sum( CASE WHEN paymentConfigMasterID = '37' THEN amount ELSE 0 END ) Daleel,
                                        sum( CASE WHEN paymentConfigMasterID = '40' THEN amount ELSE 0 END ) Voucher,
                                        sum( CASE WHEN paymentConfigMasterID = '42' THEN amount ELSE 0 END ) Loyalty,
                                        
                                        sum( CASE WHEN paymentConfigMasterID = '51' THEN amount ELSE 0 END ) NAPS,
                                        sum( CASE WHEN paymentConfigMasterID = '52' THEN amount ELSE 0 END ) QNBDebit,
                                        sum( CASE WHEN paymentConfigMasterID = '53' THEN amount ELSE 0 END ) GCCNET,
                                        sum( CASE WHEN paymentConfigMasterID = '46' THEN amount ELSE 0 END ) TMDone,
                                        sum( CASE WHEN paymentConfigMasterID = '54' THEN amount ELSE 0 END ) TMDoneCash,
                                        sum( CASE WHEN paymentConfigMasterID = '43' THEN amount ELSE 0 END ) TalabatCash
                                        
                                        FROM srp_erp_pos_menusalespayments LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID LEFT JOIN srp_erp_pos_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_pos_customermaster.customerAutoID GROUP BY menuSalesID) payment ON salesMaster.menuSalesID = payment.menuSalesID WHERE salesMaster.isVoid = 1 AND salesMaster.companyID = " . current_companyID() . " " . $qString . $outletFilter . $customerFilter . $orderFilter . " GROUP BY salesMaster.menuSalesID HAVING (isHold = 0 OR deliveryOrderID is not null) 
                                        AND (rptDate BETWEEN '$date' AND '$date2') ";

        //echo $querySalesDetail;

        return $this->db->query($querySalesDetail)->result_array();
    }


    function get_category_wise_profitability_report($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {
        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }

        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $q = "SELECT
                menuCategory.menuCategoryID,
                menuCategory.menuCategoryDescription,
                sum( salesItem.salesPriceAfterDiscount ) AS sales,
                sum(salesItem.menuCost * salesItem.qty) as cos,
                sum( salesItem.salesPriceAfterDiscount )-(sum(salesItem.menuCost  * salesItem.qty)) as gp
            FROM
                srp_erp_pos_menusalesmaster salesMaster
                LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID 
                AND salesMaster.wareHouseAutoID = salesItem.id_store
                LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
            WHERE
                salesMaster.isVoid = 0 
                    AND salesMaster.isHold = 0 
                    AND salesMaster.companyID = " . current_companyID() . " 
                    AND salesMaster.createdDateTime BETWEEN '" . $dateFrom . "' 
                    AND '" . $dateTo . "' 
                    AND menuMaster.menuMasterID IS NOT NULL " . $outletsFilter . " $qString 
            GROUP BY
                menuCategory.menuCategoryID #, salesItem.id_store 
            ORDER BY
                menuCategory.menuCategoryDescription";

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_item_wise_profitability_report($cat_id, $dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $cat_id = $this->input->post('cat_id');

        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $q = "SELECT
                menuCategory.menuCategoryID,
                menuCategory.menuCategoryDescription,
                sum( salesItem.salesPriceAfterDiscount ) AS sales,
                sum(salesMaster.menuCost) as cos,
                sum( salesItem.salesPriceAfterDiscount )-(sum(salesMaster.menuCost)) as gp
            FROM
                srp_erp_pos_menusalesmaster salesMaster
                LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID 
                AND salesMaster.wareHouseAutoID = salesItem.id_store
                LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
            WHERE
                salesMaster.isVoid = 0 
                    AND salesMaster.isHold = 0 
                    AND salesMaster.companyID = " . current_companyID() . " 
                    AND salesMaster.createdDateTime BETWEEN '" . $dateFrom . "' 
                    AND '" . $dateTo . "' 
                    AND menuMaster.menuMasterID IS NOT NULL " . $outletFilter . " $qString 
            GROUP BY
                menuCategory.menuCategoryID,
                salesItem.id_store 
            ORDER BY
                menuMaster.menuMasterID";

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_top_sales_items($from_date, $to_date, $outlet, $company_id)
    {
        $outlet_where = '';
        if (!empty($outlet)) {
            $outlet = join(',', $outlet);
            $outlet_where = "AND wareHouseAutoID IN ({$outlet})";
        }
        $ware_house = $this->db->query("SELECT wareHouseAutoID AS id, wareHouseCode, wareHouseDescription FROM srp_erp_warehousemaster 
                            WHERE companyID = {$company_id} AND isPosLocation = 1 AND isActive = 1 {$outlet_where}")->result_array();
        $master_menus = $this->db->query("SELECT menuCategoryID, menuCategoryDescription, pos_menu_tree(menuCategoryID) AS id_list
                              FROM srp_erp_pos_menucategory WHERE companyID = {$company_id} and topSalesRptYN = 1")->result_array();
        foreach ($ware_house as $key => $row) {
            foreach ($master_menus as $top_key => $row_menu) {
                if ($row_menu['id_list'] != "") {
                    $ware_house[$key]['items'][$row_menu['menuCategoryID']] =
                        $this->db->query("SELECT menu_mas.menuMasterDescription AS menu_des, rpt_tb.qty, rpt_tb.net FROM (
                            SELECT salse_det.menuID, SUM(salse_det.qty) AS qty, round(SUM(salse_det.salesPriceAfterDiscount) ,2) AS net
                            FROM srp_erp_pos_menusalesmaster AS sales_mas
                            JOIN srp_erp_pos_menusalesitems AS salse_det ON salse_det.menuSalesID = sales_mas.menuSalesID
                            WHERE sales_mas.companyID = {$company_id} AND sales_mas.createdDateTime BETWEEN '{$from_date}' AND  '{$to_date}'
                            AND isHold = 0 AND isVoid = 0 AND sales_mas.wareHouseAutoID = {$row['id']} AND salse_det.menuCategoryID IN ({$row_menu['id_list']})
                            GROUP BY sales_mas.wareHouseAutoID, salse_det.menuID ORDER BY qty DESC LIMIT 10
                        ) rpt_tb
                        JOIN srp_erp_pos_menumaster AS menu_mas ON menu_mas.menuMasterID = rpt_tb.menuID ")->result_array();
                }
            }
        }
        //echo '<pre>$ware_house - '; print_r($ware_house); echo '</pre>';        die();
        return [
            'master_menus' => $master_menus,
            'ware_house' => $ware_house
        ];
    }

    function get_top_sales_items_for_daybookemail($from_date, $to_date, $outlet, $company_id)
    {
        $outlet_where = '';
        if (!empty($outlet)) {
            $outlet = join(',', $outlet);
            $outlet_where = "AND wareHouseAutoID IN ({$outlet})";
        }
        $ware_house = $this->db->query("SELECT wareHouseAutoID AS id, wareHouseCode, wareHouseDescription FROM srp_erp_warehousemaster 
                            WHERE companyID = {$company_id} AND isPosLocation = 1 AND isActive = 1 {$outlet_where}")->result_array();
        $master_menus = $this->db->query("SELECT menuCategoryID, menuCategoryDescription, pos_menu_tree(menuCategoryID) AS id_list
                              FROM srp_erp_pos_menucategory WHERE companyID = {$company_id}")->result_array();


        foreach ($ware_house as $key => $row) {

            //                if ($row_menu['id_list'] != "") {
            //                    $ware_house[$key]['items'][$row_menu['menuCategoryID']] =
            //                        $this->db->query("SELECT menu_mas.menuMasterDescription AS menu_des, rpt_tb.qty, rpt_tb.net FROM (
            //                            SELECT salse_det.menuID, SUM(salse_det.qty) AS qty, round(SUM(salse_det.salesPriceAfterDiscount) ,2) AS net
            //                            FROM srp_erp_pos_menusalesmaster AS sales_mas
            //                            JOIN srp_erp_pos_menusalesitems AS salse_det ON salse_det.menuSalesID = sales_mas.menuSalesID
            //                            WHERE sales_mas.companyID = {$company_id} AND sales_mas.createdDateTime BETWEEN '{$from_date}' AND  '{$to_date}'
            //                            AND isHold = 0 AND isVoid = 0 AND sales_mas.wareHouseAutoID = {$row['id']} AND salse_det.menuCategoryID IN ({$row_menu['id_list']})
            //                            GROUP BY sales_mas.wareHouseAutoID, salse_det.menuID ORDER BY qty DESC LIMIT 10
            //                        ) rpt_tb
            //                        JOIN srp_erp_pos_menumaster AS menu_mas ON menu_mas.menuMasterID = rpt_tb.menuID ")->result_array();
            //                }
            $ware_house[$key]['items'] =
                $this->db->query("SELECT menu_mas.menuMasterDescription AS menu_des, rpt_tb.qty, rpt_tb.net FROM (
                            SELECT salse_det.menuID, SUM(salse_det.qty) AS qty, round(SUM(salse_det.salesPriceAfterDiscount) ,2) AS net
                            FROM srp_erp_pos_menusalesmaster AS sales_mas
                            JOIN srp_erp_pos_menusalesitems AS salse_det ON salse_det.menuSalesID = sales_mas.menuSalesID
                            WHERE sales_mas.companyID = {$company_id} AND sales_mas.createdDateTime BETWEEN '{$from_date}' AND  '{$to_date}'
                            AND isHold = 0 AND isVoid = 0 AND sales_mas.wareHouseAutoID = {$row['id']}
                            GROUP BY sales_mas.wareHouseAutoID, salse_det.menuID ORDER BY qty DESC LIMIT 20
                        ) rpt_tb
                        JOIN srp_erp_pos_menumaster AS menu_mas ON menu_mas.menuMasterID = rpt_tb.menuID ")->result_array();
        }

        //echo '<pre>$ware_house - '; print_r($ware_house); echo '</pre>';        die();
        return [
            'master_menus' => $master_menus,
            'ware_house' => $ware_house
        ];
    }

    function get_kot_countdown_report($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {
        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }

        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $q = "SELECT
	salesMaster.createdUserID AS createdUserID,
	DATE_FORMAT(IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, salesMaster.createdDateTime),'%d-%m-%Y %H:%i' ) AS datentime,
	invoiceCode AS billNo,
	Ename2 AS Cashier,
	KOTEndDateTime,
	KOTStartDateTime,
	DATE_FORMAT( salesMaster.KOTEndDateTime, \"%Y-%m-%d   %H : %i \" ) AS billEndTime,
	DATE_FORMAT( salesMaster.KOTStartDateTime, \"%Y-%m-%d   %H : %i \" ) AS billStartTime,
	DATE_FORMAT( salesMaster.KOTEndDateTime, \"%H : %i \" ) AS billEndTime1,
	DATE_FORMAT( salesMaster.KOTStartDateTime, \"%H : %i \" ) AS billStartTime2,
	TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(KOTEndDateTime,'%Y-%m-%d  %H:%i'), DATE_FORMAT(KOTStartDateTime,'%Y-%m-%d  %H:%i') ) ) / 60  AS timetaken,
	IFNULL( preparationTime, 0 ) AS defaultDuration,
	salesMaster.companyID,
	salesMaster.wareHouseAutoID 
	
FROM
	srp_erp_pos_menusalesmaster salesMaster
	LEFT JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID 
	LEFT JOIN srp_erp_pos_deliveryorders delivery ON salesMaster.menuSalesID = delivery.menuSalesMasterID
WHERE
	salesMaster.companyID = " . current_companyID() . " 
    AND IF(delivery.deliveryOrderID IS NOT NULL, delivery.deliveryDate, salesMaster.createdDateTime) BETWEEN '" . $dateFrom . "' 
	AND '" . $dateTo . "' 
	$outletsFilter
	$qString 
ORDER BY
	salesMaster.createdDateTime DESC";

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function update_wowfood_status($invoiceID, $status)
    {
        $update_row = array(
            "wowFoodStatus" => $status
        );
        $this->db->where('menuSalesID', $invoiceID);
        $this->db->update('srp_erp_pos_menusalesmaster', $update_row);
    }

    function get_outlet_cashier_voidbills()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier3[]" id = "cashier3" class="form-control input-sm" multiple = "multiple"  required >';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier3[]" id = "cashier3" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }
    }

    function get_outlet_waiter_voidbills()
    {
        $warehouseUser = get_warehouse_user_details();
        if ($warehouseUser->superAdminYN == 1) {
            if ($this->input->post("warehouseAutoID")) {
                $warehouse = join(',', $this->input->post("warehouseAutoID"));
                $q = "SELECT
	Ename1 AS empName,
	srp_erp_pos_crewmembers.crewMemberID
FROM
	srp_erp_pos_menusalesmaster salesMaster	
	Join srp_erp_pos_crewmembers on srp_erp_pos_crewmembers.crewMemberID = salesMaster.waiterID
 	join srp_erp_pos_crewroles on srp_erp_pos_crewroles.crewRoleID = srp_erp_pos_crewmembers.crewRoleID
	join srp_employeesdetails on srp_employeesdetails.EIdNo=srp_erp_pos_crewmembers.EIdNo
WHERE
	salesMaster.companyID = " . current_companyID() . "
	and srp_erp_pos_crewroles.isWaiter=1
	AND salesMaster.warehouseAutoID IN ($warehouse) 
GROUP BY
	salesMaster.waiterID";

                $result = $this->db->query($q)->result_array();
                $html = '<select name = "waiter[]" id = "cashier3" class="form-control input-sm" multiple = "multiple"  required >';
                if ($result) {
                    foreach ($result as $val) {
                        $html .= '<option value = "' . $val['crewMemberID'] . '" > ' . $val['empName'] . ' </option > ';
                    }
                }
                $html .= '</select > ';
                return $html;
            } else {
                $html = '<select name = "waiter[]" id = "cashier3" class="form-control input-sm" multiple = "multiple"  required > ';
                $html .= '</select > ';
                return $html;
            }
        } else {
            $warehouse = get_outletID();
            $q = "SELECT
	Ename1 AS empName,
	srp_erp_pos_crewmembers.crewMemberID
FROM
	srp_erp_pos_menusalesmaster salesMaster	
	Join srp_erp_pos_crewmembers on srp_erp_pos_crewmembers.crewMemberID = salesMaster.waiterID
 	join srp_erp_pos_crewroles on srp_erp_pos_crewroles.crewRoleID = srp_erp_pos_crewmembers.crewRoleID
	join srp_employeesdetails on srp_employeesdetails.EIdNo=srp_erp_pos_crewmembers.EIdNo
WHERE
	salesMaster.companyID = " . current_companyID() . "
	and srp_erp_pos_crewroles.isWaiter=1
	AND salesMaster.warehouseAutoID IN ($warehouse) 
GROUP BY
	salesMaster.waiterID";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "waiter[]" id = "cashier3" class="form-control input-sm" multiple = "multiple"  required >';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['crewMemberID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        }
    }

    function restaurant_shift_doubleEntry_fromReview($shiftID)
    {
        $this->db->trans_start();

        $outletID = get_outletID();
        $exceededItem = true;

        if ($exceededItem) {
            /** 0. ITEM EXCEEDED */
            $this->Pos_restaurant_accounts->update_itemExceededRecord_fromReview($shiftID, false);
        }
        /*item ledger insert feom review*/
        $this->Pos_restaurant_accounts->update_itemLedger_fromReview($shiftID, false);
        /** 1. REVENUE */
        $this->Pos_restaurant_accounts->update_generalLedger_fromReview($shiftID);
        $this->Pos_restaurant_accounts->update_bankLedger_fromReview($shiftID);
        $this->Pos_restaurant_accounts->update_exceededGL_generalLedger($shiftID);

        $this->Pos_restaurant_accounts->update_itemMasterNewStock_itemExceeded($shiftID);
        $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock_itemExceeded($shiftID);

        $CS = " SELECT *  FROM srp_erp_pos_menusalesmaster  WHERE isCreditSales = 1 AND wareHouseAutoID = '" . $outletID . "'  AND shiftID = '" . $shiftID . "'";
        $resultCS = $this->db->query($CS)->result_array();
        if (!empty($resultCS)) {
            foreach ($resultCS as $val) {
                $menuSalesID = $val['menuSalesID'];
                $this->db->select('invoiceAutoID');
                $this->db->from('srp_erp_customerinvoicemaster');
                $this->db->where('posMasterAutoID', $menuSalesID);
                $row = $this->db->get()->row_array();
                if (!empty($row['invoiceAutoID'])) {
                    if (isset($val['isVoid']) && $val['isVoid'] == 1) {
                        //for voided bills - delete invoice details
                        $update_data = array(
                            'isDeleted' => 1,
                            'deletedEmpID' => current_userID(),
                            'deletedDate' => current_date(),
                        );
                        $this->db->delete('srp_erp_customerinvoicedetails', array('invoiceAutoID' => $row['invoiceAutoID']));
                    } else {
                        //update confirm and update columns
                        $this->db->select('*');
                        $this->db->from('srp_erp_pos_shiftdetails');
                        $this->db->where('shiftID', $shiftID);
                        $shiftDetails = $this->db->get()->row_array();
                        $createdUserID = isset($shiftDetails['createdUserID']) ? $shiftDetails['createdUserID'] : '';
                        $createdUserName = isset($shiftDetails['createdUserName']) ? $shiftDetails['createdUserName'] : '';
                        $startTime = isset($shiftDetails['startTime']) ? $shiftDetails['startTime'] : '';

                        $update_data = array(
                            'confirmedYN' => 1,
                            'confirmedByEmpID' => $createdUserID,
                            'confirmedByName' => $createdUserName,
                            'confirmedDate' => $startTime,
                            'approvedYN' => 1,
                            'approvedDate' => $startTime,
                            'approvedbyEmpID' => $createdUserID,
                            'approvedbyEmpName' => $createdUserName
                        );
                        //Document Approved Table Entries
                        $this->Pos_restaurant_accounts->document_approved_entries_for_invoices($row['invoiceAutoID']);
                    }

                    $this->db->update('srp_erp_customerinvoicemaster', $update_data, array('posMasterAutoID' => $menuSalesID)); //update invoice master
                } else {
                    if ($row['isCreditSales'] == 1) {
                        $invSequenceCodeDet = $this->getInvoiceSequenceCode();
                        $lastINVNo = $invSequenceCodeDet['lastINVNo'];
                        $sequenceCode = $invSequenceCodeDet['sequenceCode'];
                        $this->db->select('customerAutoID');
                        $this->db->from('srp_erp_pos_menusalespayments');
                        $this->db->where('menuSalesID', $row['menuSalesID']);
                        $custmrs = $this->db->get()->row_array();
                        $customerID = $custmrs['customerAutoID'];
                        $cusData = $this->db->query("SELECT customerAutoID, customerSystemCode, customerName, receivableAutoID,
                                             receivableSystemGLCode, receivableGLAccount, receivableDescription, receivableType,
                                             customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces,customerAddress1,customerTelephone
                                             FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

                        $data_customer_invoice['invoiceType'] = 'Direct';
                        $data_customer_invoice['documentID'] = 'CINV';
                        $data_customer_invoice['posTypeID'] = 1;
                        $data_customer_invoice['referenceNo'] = $sequenceCode;
                        $data_customer_invoice['invoiceNarration'] = 'POS Credit Sales - ' . $sequenceCode;
                        $data_customer_invoice['posMasterAutoID'] = $row['menuSalesID'];
                        $data_customer_invoice['invoiceDate'] = $row['menuSalesDate'];
                        $data_customer_invoice['invoiceDueDate'] = $row['menuSalesDate'];
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
                        $data_customer_invoice['transactionAmount'] = 0;
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
                            $data_customer_invoice_detail['transactionAmount'] = round(0, $master['transactionCurrencyDecimalPlaces']);
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
                }
                /** 0. CUSTOMER INVOICE - Credit Sales Entries  */
                // $this->Pos_restaurant_accounts->pos_generate_invoices_from_review($shiftID, $menuSalesID);  // updated on bill submit

                if ($exceededItem) {
                    $query = " SELECT documentMasterAutoID  FROM srp_erp_pos_menusalesmaster  WHERE menuSalesID = '" . $menuSalesID . "'  ";
                    $docid = $this->db->query($query)->row_array();
                    $this->Pos_restaurant_accounts->update_itemExceededRecord_creditSales_menuSalesID($shiftID, $menuSalesID);

                    $this->Pos_restaurant_accounts->update_itemLedger_fromReview_creditsales($shiftID, $menuSalesID, $docid['documentMasterAutoID']);
                    $this->Pos_restaurant_accounts->update_generalLedger_fromReview_creditsales($shiftID, $menuSalesID, $docid['documentMasterAutoID']);
                }
            }

            $this->Pos_restaurant_accounts->update_exceededGL_generalLedger_credit_sales($shiftID); // outletID => JOIN, WHERE condition corrected
            $this->Pos_restaurant_accounts->update_itemMasterNewStock_credit_sales_Item_exceeded($shiftID);
            $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock_credit_sales_Item_exceeded($shiftID);
        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('w', 'Error while updating:  <br/><br/>' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Double Entries Updated');
        }
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
        $this->db->order_by("serialNo", "Desc");
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

            $this->db->select('*');
            $this->db->where('companyID', trim($companyID));
            $this->db->where('cardMasterID', trim($cardMasterID));
            $this->db->from('srp_erp_pos_loyaltytopup');
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                return array('e', 'Card Already Used.');
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
                $data['customerType'] = 1;
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
        $this->db->select('cardMasterID,barcode,srp_erp_pos_loyaltycard.customerID,srp_erp_pos_customermaster.customerName,srp_erp_pos_customermaster.customerTelephone');
        $this->db->where('cardMasterID', trim($cardMasterID));
        $this->db->join('srp_erp_pos_customermaster', 'srp_erp_pos_loyaltycard.customerID = srp_erp_pos_customermaster.posCustomerAutoID', 'left');
        $this->db->from('srp_erp_pos_loyaltycard');
        $results = $this->db->get()->row_array();

        return $results;
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

    function add_points()
    {
        $exchange_rate_amount = $this->input->post('exchange_rate_amount');
        $price_to_point = $this->input->post('price_to_point');
        $point_to_price = $this->input->post('point_to_price');
        $minimum_points = $this->input->post('minimum_points');
        $amount_val = $this->input->post('amount_val');
        $number_of_points = $this->input->post('number_of_points');
        $companyID = current_companyID();

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

    function save_customer_posres()
    {
        $customerCountry = $this->input->post('customerCountry');
        $q = "SELECT company_country FROM srp_erp_company WHERE company_id = '" . current_companyID() . "'";
        $company_country = $this->db->query($q)->row('company_country');
        $customercountryID = $this->db->query("SELECT countryID FROM `srp_erp_countrymaster` where CountryDes LIKE '{$customerCountry}'")->row('countryID');
        $data_poscus['customerTelephone'] = $this->input->post('customerTelephoneTmp');
        $data_poscus['CustomerName'] = $this->input->post('customerNameTmp');
        $data_poscus['CustomerAddress1'] = $this->input->post('customerAddressTmp');
        $data_poscus['customerEmail'] = $this->input->post('customerEmailTmp');
        $data_poscus['customerCountry'] = $customerCountry;
        $data_poscus['customerCountryCode'] = $this->input->post('customerCountryCode');
        $data_poscus['customerCountryId'] = $customercountryID;
        $data_poscus['isActive'] = 1;
        //$data_poscus['customerCountry'] = $company_country;
        $data_poscus['isFromERP'] = 0;
        $data_poscus['companyID'] = current_companyID();
        $data_poscus['createdUserGroup'] = $this->common_data['user_group'];
        $data_poscus['createdPCID'] = $this->common_data['current_pc'];
        $data_poscus['createdUserID'] = $this->common_data['current_userID'];
        $data_poscus['createdDateTime'] = $this->common_data['current_date'];
        $data_poscus['createdUserName'] = $this->common_data['current_user'];

        $insert_query = $this->db->insert('srp_erp_pos_customermaster', $data_poscus);
        $customerID = $this->db->insert_id();


        if ($insert_query) {
            return array('s', 'Customer Saved Successfully', $customerID);
        } else {
            return array('e', 'Customer Save Failed');
        }
    }

    function update_partial_payment()
    {
        $invoiceID = isPos_invoiceSessionExist();
        $outletID = get_outletID();
        $totalPaid = 0;
        $isConfirmedDeliveryOrder = pos_isConfirmedDeliveryOrder($invoiceID);
        $createdUserGroup = user_group();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserName = current_user();
        $createdDateTime = format_date_mysql_datetime();
        $timestamp = format_date_mysql_datetime();
        $companyID = current_companyID();
        $companyCode = current_company_code();
        $masterData = get_pos_invoice_id($invoiceID);
        $reference = $this->input->post('reference');
        $customerAutoIDs = $this->input->post('customerAutoID');
        $paymentTypes = $this->input->post('paymentTypes');
        $cardTotalAmount = $this->input->post('cardTotalAmount');
        $netTotalAmount = $this->input->post('netTotalAmount'); //netTotal
        $isDelivery = $this->input->post('isDelivery'); //isDelivery
        $isOnTimePayment = $this->input->post('isOnTimePayment');
        $payableDeliveryAmount = $this->input->post('totalPayableAmountDelivery_id');
        $returnChange = $this->input->post('returned_change'); //balanceAmount
        $grossTotal = $this->input->post('total_payable_amt'); //cashReceivedAmount
        $promotional_discount = $this->input->post('promotional_discount');
        $own_delivery_amount = $this->input->post('own_delivery_amount');
        $i = 0;
        $paymentData = array();
        $datac = '';
        foreach ($paymentTypes as $key => $amount) {
            if ($amount > 0) {
                $totalPaid += $amount;
                $this->db->select('configDetail.GLCode,configMaster.autoID, configMaster.glAccountType');
                $this->db->from('srp_erp_pos_paymentglconfigdetail configDetail');
                $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configDetail.paymentConfigMasterID = configMaster.autoID', 'left');
                $this->db->where('configDetail.ID', $key);
                $r = $this->db->get()->row_array();
                $advancePayment = get_paidAmount($invoiceID);
                //$payable = $netTotalAmount - ($advancePayment + $cardTotalAmount); bug because of this.
                $payable = $grossTotal - ($advancePayment + $cardTotalAmount);
                //                if ($amount == $payable) {
                //                    $cashPaidAmount = $amount;
                //                    $returnChange = 0;
                //                } else if ($amount > $payable) {
                //                    $cashPaidAmount = $payable;
                //                    $returnChange = $amount - $payable;
                //                } else {
                //                    /** Advance payment */
                //                    $cashPaidAmount = $amount;
                //                    $returnChange = 0;
                //                }
                //                $amount = $cashPaidAmount;
                /** Credit Customer's GL Code should be picked from Customer */
                $GLCode = null;

                $paymentData[$i]['menuSalesID'] = $invoiceID;
                $paymentData[$i]['wareHouseAutoID'] = $outletID;
                $paymentData[$i]['paymentConfigMasterID'] = $r['autoID'];
                $paymentData[$i]['paymentConfigDetailID'] = $key;
                $paymentData[$i]['GLCode'] = $r['autoID'] == 7 ? $GLCode : $r['GLCode'];
                $paymentData[$i]['glAccountType'] = $r['glAccountType'];
                $paymentData[$i]['amount'] = $amount;
                $paymentData[$i]['reference'] = isset($reference[$key]) ? $reference[$key] : null;
                $paymentData[$i]['customerAutoID'] = isset($customerAutoIDs[$key]) ? $customerAutoIDs[$key] : null;
                /*Common Data*/
                $paymentData[$i]['createdUserGroup'] = $createdUserGroup;
                $paymentData[$i]['createdPCID'] = $createdPCID;
                $paymentData[$i]['createdUserID'] = $createdUserID;
                $paymentData[$i]['createdUserName'] = $createdUserName;
                $paymentData[$i]['createdDateTime'] = $createdDateTime;
                $paymentData[$i]['timestamp'] = $timestamp;

                if ($r['autoID'] == 1) {

                    $this->db->select('IFNULL(cashAmount,0) as cashAmount');
                    $this->db->from('srp_erp_pos_menusalesmaster');
                    $this->db->where('menuSalesID', $invoiceID);
                    $this->db->where('wareHouseAutoID', $outletID);
                    $cshamnt = $this->db->get()->row_array();

                    $datac['cashAmount'] = $cshamnt['cashAmount'] + $amount;
                }
                $i++;
            }
        }

        if ($own_delivery_amount > 0) {
            $datac['ownDeliveryAmount'] = $own_delivery_amount;
        }


        if (!empty($datac)) {
            $this->db->where('menuSalesID', $invoiceID);
            $this->db->where('wareHouseAutoID', $outletID);
            $this->db->update('srp_erp_pos_menusalesmaster', $datac);
        }

        if (isset($paymentData) && !empty($paymentData)) {
            $this->db->insert_batch('srp_erp_pos_menusalespayments', $paymentData);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('error' => 1, 'message' => 'error, please contact your support team' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('error' => 0, 'message' => 'Partial payment submitted', 'invoiceID' => $invoiceID, 'outletID' => $outletID);
            }
        } else {
            return array('error' => 1, 'message' => 'Please enter a payment');
        }
    }

    function get_last_partial_payment($menusalesID)
    {
        return $this->db->query("SELECT * FROM `srp_erp_pos_menusalespayments` WHERE menuSalesID=$menusalesID ORDER BY menuSalesPaymentID DESC LIMIT 1")->row_array();
    }

    function is_pack($menuMasterID)
    {
        $row = $this->db->get_where('srp_erp_pos_menumaster', array('menuMasterID' => $menuMasterID))->row_array();
        return $row['isPack'];
    }

    function get_currency()
    {
        $com_id = current_companyID();
        return $currency = $this->db->query("select company_default_currency from srp_erp_company where company_id=$com_id")->row()->company_default_currency;
    }

    function checking_for_errors($invoiceID)
    {
        $errors = array();

        //----->This query checks whether there are any duplicate entries in the srp_erp_generalledger_review table.<--------------//
        $test_query1 = $this->db->query("SELECT
	count(*) as cnt,
-- documentSystemCode,
pos_menusalesID,
pos_menusalesItemID
FROM
	srp_erp_generalledger_review
WHERE
	documentCode = 'POSR' AND
	pos_menusalesID=$invoiceID
GROUP BY
	documentCode,
	documentSystemCode,
	pos_menusalesItemID,
pos_menusalesID,
GLAutoID

having cnt > 1

");
        if ($test_query1->num_rows() > 0) {
            array_push($errors, 1); //Issue type 1
        }

        //----->This query checks whether there is any double entry mismatch in the srp_erp_generalledger_review table.<--------------//
        $test_query2 = $this->db->query("SELECT
pos_menusalesID,
documentMasterAutoID,
	documentSystemCode,
	documentNarration,
	round( sum( companyLocalAmount ), 2 ) AS amt,
	documentDate,
	wareHouseAutoID ,
	documentMasterAutoID
FROM
	srp_erp_generalledger_review 
	Where
documentCode = 'POSR' AND
	pos_menusalesID=$invoiceID

GROUP BY
	pos_menusalesID,
	id_store 
HAVING
	amt!=0 

	");

        if ($test_query2->num_rows() > 0) {
            array_push($errors, 2); //Issue type 2
        }

        //----->This query checks whether the sum of the transaction tables tallies to the sum of the payment table.<--------------//
        $test_query3 = $this->db->query("select *, (amount+taxamount+SCamount+outlettaxamount) as payable, (PVamount - (amount+taxamount+SCamount+outlettaxamount)) as diff from (SELECT
  sales.menuSalesID, 
  sum(item.salesPriceNetTotal) amount,
	menuSalesDate
FROM
srp_erp_pos_menusalesmaster sales
    
left JOIN srp_erp_pos_menusalesitems item ON item.menusalesID=sales.menuSalesID and item.wareHouseAutoID=sales.wareHouseAutoID
where sales.menuSalesID = $invoiceID
and ishold=0 and isvoid=0


group by sales.menuSalesID) sales1 
left JOIN 
(SELECT
    sales.menuSalesID as taxmenusalesID, ifnull(sum(taxamount),0) taxamount
FROM
srp_erp_pos_menusalesmaster sales
    
LEFT JOIN srp_erp_pos_menusalestaxes tax ON sales.menuSalesID = tax.menuSalesID and tax.wareHouseAutoID=sales.wareHouseAutoID
WHERE
    sales.menuSalesID = $invoiceID
		
AND sales.isHold = 0
AND isvoid = 0

group by sales.menuSalesID) tax on sales1.menusalesID=tax.taxmenusalesID 
left JOIN 
(SELECT
    sales.menuSalesID scMenusalesID, ifnull(round(sum(sc.serviceChargeAmount),2),0) as SCamount
FROM
srp_erp_pos_menusalesmaster sales
    
left JOIN srp_erp_pos_menusalesservicecharge sc ON sales.menuSalesID = sc.menuSalesID and sc.wareHouseAutoID=sales.wareHouseAutoID
WHERE
    sales.menuSalesID = $invoiceID
AND sales.isHold = 0
AND isvoid = 0

group by sales.menuSalesID) serviceCharge on sales1.menuSalesID=serviceCharge.scMenusalesID
left join 
(SELECT
    sales.menuSalesID as outlettaxmenusalesID, ifnull(sum(taxamount),0) outlettaxamount
FROM
srp_erp_pos_menusalesmaster sales
LEFT JOIN srp_erp_pos_menusalesoutlettaxes outlettax ON sales.menuSalesID = outlettax.menuSalesID and outlettax.wareHouseAutoID=sales.wareHouseAutoID
WHERE
    sales.menuSalesID = $invoiceID
		
AND sales.isHold = 0
AND isvoid = 0

group by sales.menuSalesID) outlettax on sales1.menusalesID=outlettax.outlettaxmenusalesID

left JOIN 
(
SELECT
    sales.menuSalesID, ifnull(sum(amount),0)as  PVamount
FROM
srp_erp_pos_menusalesmaster sales
    
LEFT JOIN srp_erp_pos_menusalespayments pv ON sales.menuSalesID = pv.menuSalesID and pv.wareHouseAutoID=sales.wareHouseAutoID
WHERE
    sales.menuSalesID = $invoiceID
AND sales.isHold = 0
AND isvoid = 0

group by sales.menuSalesID) Payment on sales1.menuSalesID=Payment.menuSalesID 
having (diff != 0
and diff is not  null)

order by menuSalesDate

");

        if ($test_query3->num_rows() > 0) {
            array_push($errors, 3); //Issue type 3
        }
        $str = implode(", ", $errors);
        if ($str != "") {
            $update_status_record = array(
                "issueType" => $str
            );
            $this->db->where('menuSalesID', $invoiceID);
            $this->db->update('srp_erp_pos_menusalesmaster', $update_status_record);
        }
    }

    public function get_session_close_payment_details($shiftID, $array_paymentIDs)
    {
        $dataFill = array();
        foreach ($array_paymentIDs as $paymentID) {
            $dataSegment = array();
            $dataSegment['paymentName'] = $this->db->query("select * from srp_erp_pos_paymentglconfigmaster where autoID=$paymentID")->row()->description;
            $query = $this->db->query("SELECT * FROM `srp_erp_pos_menusalesmaster`
join srp_erp_pos_menusalespayments on srp_erp_pos_menusalesmaster.menuSalesID=srp_erp_pos_menusalespayments.menuSalesID
where srp_erp_pos_menusalesmaster.shiftID=$shiftID
and srp_erp_pos_menusalespayments.paymentConfigMasterID=$paymentID");
            if ($query->num_rows() > 0) {
                $dataSegment['paymentList'] = $query->result();
            } else {
                $dataSegment['paymentList'] = array();
            }
            array_push($dataFill, $dataSegment);
        }
        return $dataFill;
    }

    public function get_payment_references($menusalesID)
    {
        $query = $this->db->query("SELECT * FROM `srp_erp_pos_menusalespayments` WHERE menuSalesID=$menusalesID");
        $paymentRef = array();
        foreach ($query->result() as $item) {
            array_push($paymentRef, $item->reference);
        }
        $paymentRefString = implode(",", $paymentRef);
        return $paymentRefString;
    }

    public function getWaiters($warehouseID)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $query = $this->db->query("SELECT * FROM `srp_erp_pos_crewmembers` 
join srp_erp_pos_crewroles on srp_erp_pos_crewroles.crewRoleID = srp_erp_pos_crewmembers.crewRoleID
where srp_erp_pos_crewroles.isWaiter=1
and srp_erp_pos_crewroles.companyID=$companyID
and srp_erp_pos_crewmembers.wareHouseAutoID=$warehouseID");
        return $query->result_array();
    }

    public function dineInId()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $customerTypeID = $this->db->query("select customerTypeID from srp_erp_customertypemaster where company_id=$companyID and isDineIn=1")->row('customerTypeID');
        if ($customerTypeID == null) {
            $customerTypeID = $this->db->query("select customerTypeID from srp_erp_customertypemaster where company_id=$companyID and (customerDescription='Eat-in' || customerDescription='Dine-in' || customerDescription='Direct Sales')")->row('customerTypeID');
        }
        return $customerTypeID;
    }

    public function isPayButtonEnabled()
    {
        $companyID = current_companyID();
        $userID = current_userID();
        $wareHouseID = current_warehouseID();
        $query = $this->db->query("SELECT * FROM `srp_erp_warehouse_users` where wareHouseID=$wareHouseID and userID=$userID and companyID=$companyID and isActive=1");

        if ($query->num_rows() > 0) {

            $warehouseUserID = $query->row()->autoID;
            $query2 = $this->db->query("SELECT * FROM `srp_erp_pos_warehouseuser_button_access` where warehouseUserID=$warehouseUserID and buttonID=1 and companyID=$companyID"); //buttonID 1 is pay button.
            if ($query2->num_rows() > 0) {
                $isDisabled = $query2->row()->isDisabled;
                if ($isDisabled == 1) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1; //button not restricted for this user.
            }
        } else {
            return 0; //user not in warehouse users table
        }
    }

    public function isPowerButtonEnabled()
    {
        $companyID = current_companyID();
        $userID = current_userID();
        $wareHouseID = current_warehouseID();
        $query = $this->db->query("SELECT * FROM `srp_erp_warehouse_users` where wareHouseID=$wareHouseID and userID=$userID and companyID=$companyID and isActive=1");

        if ($query->num_rows() > 0) {

            $warehouseUserID = $query->row()->autoID;
            $query2 = $this->db->query("SELECT * FROM `srp_erp_pos_warehouseuser_button_access` where warehouseUserID=$warehouseUserID and buttonID=4 and companyID=$companyID"); //buttonID 4 is power button.
            if ($query2->num_rows() > 0) {
                $isDisabled = $query2->row()->isDisabled;
                if ($isDisabled == 1) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1; //button not restricted for this user.
            }
        } else {
            return 0; //user not in warehouse users table
        }
    }

    public function isOpenButtonEnabled()
    {
        $companyID = current_companyID();
        $userID = current_userID();
        $wareHouseID = current_warehouseID();
        $query = $this->db->query("SELECT * FROM `srp_erp_warehouse_users` where wareHouseID=$wareHouseID and userID=$userID and companyID=$companyID and isActive=1");

        if ($query->num_rows() > 0) {

            $warehouseUserID = $query->row()->autoID;
            $query2 = $this->db->query("SELECT * FROM `srp_erp_pos_warehouseuser_button_access` where warehouseUserID=$warehouseUserID and buttonID=5 and companyID=$companyID"); //buttonID 5 is Open button.
            if ($query2->num_rows() > 0) {
                $isDisabled = $query2->row()->isDisabled;
                if ($isDisabled == 1) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1; //button not restricted for this user.
            }
        } else {
            return 0; //user not in warehouse users table
        }
    }

    public function isPrintSampleButtonEnabled()
    {
        $companyID = current_companyID();
        $userID = current_userID();
        $wareHouseID = current_warehouseID();
        $query = $this->db->query("SELECT * FROM `srp_erp_warehouse_users` where wareHouseID=$wareHouseID and userID=$userID and companyID=$companyID and isActive=1");

        if ($query->num_rows() > 0) {

            $warehouseUserID = $query->row()->autoID;
            $query2 = $this->db->query("SELECT * FROM `srp_erp_pos_warehouseuser_button_access` where warehouseUserID=$warehouseUserID and buttonID=6 and companyID=$companyID"); //buttonID 6 is Print Sample button.
            if ($query2->num_rows() > 0) {
                $isDisabled = $query2->row()->isDisabled;
                if ($isDisabled == 1) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1; //button not restricted for this user.
            }
        } else {
            return 0; //user not in warehouse users table
        }
    }

    public function isHoldButtonEnabled()
    {
        $companyID = current_companyID();
        $userID = current_userID();
        $wareHouseID = current_warehouseID();
        $query = $this->db->query("SELECT * FROM `srp_erp_warehouse_users` where wareHouseID=$wareHouseID and userID=$userID and companyID=$companyID and isActive=1");

        if ($query->num_rows() > 0) {

            $warehouseUserID = $query->row()->autoID;
            $query2 = $this->db->query("SELECT * FROM `srp_erp_pos_warehouseuser_button_access` where warehouseUserID=$warehouseUserID and buttonID=7 and companyID=$companyID"); //buttonID 7 is Hold button.
            if ($query2->num_rows() > 0) {
                $isDisabled = $query2->row()->isDisabled;
                if ($isDisabled == 1) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1; //button not restricted for this user.
            }
        } else {
            return 0; //user not in warehouse users table
        }
    }

    public function isCancelButtonEnabled()
    {
        $companyID = current_companyID();
        $userID = current_userID();
        $wareHouseID = current_warehouseID();
        $query = $this->db->query("SELECT * FROM `srp_erp_warehouse_users` where wareHouseID=$wareHouseID and userID=$userID and companyID=$companyID and isActive=1");

        if ($query->num_rows() > 0) {

            $warehouseUserID = $query->row()->autoID;
            $query2 = $this->db->query("SELECT * FROM `srp_erp_pos_warehouseuser_button_access` where warehouseUserID=$warehouseUserID and buttonID=8 and companyID=$companyID"); //buttonID 8 is Cancel button.
            if ($query2->num_rows() > 0) {
                $isDisabled = $query2->row()->isDisabled;
                if ($isDisabled == 1) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1; //button not restricted for this user.
            }
        } else {
            return 0; //user not in warehouse users table
        }
    }

    public function isKitchenButtonEnabled()
    {
        $companyID = current_companyID();
        $userID = current_userID();
        $wareHouseID = current_warehouseID();
        $query = $this->db->query("SELECT * FROM `srp_erp_warehouse_users` where wareHouseID=$wareHouseID and userID=$userID and companyID=$companyID and isActive=1");

        if ($query->num_rows() > 0) {

            $warehouseUserID = $query->row()->autoID;
            $query2 = $this->db->query("SELECT * FROM `srp_erp_pos_warehouseuser_button_access` where warehouseUserID=$warehouseUserID and buttonID=9 and companyID=$companyID"); //buttonID 9 is Kitchen button.
            if ($query2->num_rows() > 0) {
                $isDisabled = $query2->row()->isDisabled;
                if ($isDisabled == 1) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1; //button not restricted for this user.
            }
        } else {
            return 0; //user not in warehouse users table
        }
    }

    public function isGiftCardButtonEnabled()
    {
        $companyID = current_companyID();
        $userID = current_userID();
        $wareHouseID = current_warehouseID();
        $query = $this->db->query("SELECT * FROM `srp_erp_warehouse_users` where wareHouseID=$wareHouseID and userID=$userID and companyID=$companyID and isActive=1");

        if ($query->num_rows() > 0) {

            $warehouseUserID = $query->row()->autoID;
            $query2 = $this->db->query("SELECT * FROM `srp_erp_pos_warehouseuser_button_access` where warehouseUserID=$warehouseUserID and buttonID=10 and companyID=$companyID"); //buttonID 10 is Gift Card button.
            if ($query2->num_rows() > 0) {
                $isDisabled = $query2->row()->isDisabled;
                if ($isDisabled == 1) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1; //button not restricted for this user.
            }
        } else {
            return 0; //user not in warehouse users table
        }
    }

    public function isClosedBillsButtonEnabled()
    {
        $companyID = current_companyID();
        $userID = current_userID();
        $wareHouseID = current_warehouseID();
        $query = $this->db->query("SELECT * FROM `srp_erp_warehouse_users` where wareHouseID=$wareHouseID and userID=$userID and companyID=$companyID and isActive=1");

        if ($query->num_rows() > 0) {

            $warehouseUserID = $query->row()->autoID;
            $query2 = $this->db->query("SELECT * FROM `srp_erp_pos_warehouseuser_button_access` where warehouseUserID=$warehouseUserID and buttonID=11 and companyID=$companyID"); //buttonID 11 is Closed Bills button.
            if ($query2->num_rows() > 0) {
                $isDisabled = $query2->row()->isDisabled;
                if ($isDisabled == 1) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1; //button not restricted for this user.
            }
        } else {
            return 0; //user not in warehouse users table
        }
    }

    public function isScreenLockButtonEnabled()
    {
        $companyID = current_companyID();
        $userID = current_userID();
        $wareHouseID = current_warehouseID();
        $query = $this->db->query("SELECT * FROM `srp_erp_warehouse_users` where wareHouseID=$wareHouseID and userID=$userID and companyID=$companyID and isActive=1");

        if ($query->num_rows() > 0) {

            $warehouseUserID = $query->row()->autoID;
            $query2 = $this->db->query("SELECT * FROM `srp_erp_pos_warehouseuser_button_access` where warehouseUserID=$warehouseUserID and buttonID=12 and companyID=$companyID"); //buttonID 12 is Screen Lock button.
            if ($query2->num_rows() > 0) {
                $isDisabled = $query2->row()->isDisabled;
                if ($isDisabled == 1) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1; //button not restricted for this user.
            }
        } else {
            return 0; //user not in warehouse users table
        }
    }

    public function is_items_exist($invoiceID)
    {
        $q = $this->db->query("select * from srp_erp_pos_menusalesitems where menuSalesID=$invoiceID");
        if ($q->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
