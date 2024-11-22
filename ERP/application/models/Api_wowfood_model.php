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

class Api_wowfood_model extends ERP_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get_company_currency_details($company_id){
        $query=$this->db->query("SELECT
	company_default_currencyID,
	company_default_currency,
	company_default_decimal,
	company_reporting_currencyID,
	company_reporting_currency,
	company_reporting_decimal
FROM
	srp_erp_company 
WHERE
	company_id = $company_id");
        return $query->row_array();
    }

    function insert_srp_erp_pos_menusalesmaster($data)
    {

        $data['id_store'] = current_warehouseID();
        $result = $this->db->insert('srp_erp_pos_menusalesmaster', $data);
        if ($result) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    function get_srp_erp_pos_shiftdetails_employee($config,$wareHouseID)
    {
        $this->load->database($config, FALSE, TRUE);
        $this->db->select("*");
        $this->db->from("srp_erp_pos_shiftdetails");
        $this->db->where('isClosed', 0);
        $this->db->where('wareHouseID', $wareHouseID);
        $query = $this->db->get();

        if($query->num_rows()>0){
            $result = $query->row_array();
        }else{
            $result=null;
        }
        return $result;
    }

    function currency_conversionID($trans_currencyID, $againce_currencyID, $amount, $company_id)
    {
        /*********************************************************************************************
         * Always transaction is going with transaction currency [ Transaction Currency => OMR ]
         * If we want to know the reporting amount [ Reporting Currency => USD ]
         * So the currency_conversion functions 1st parameter will be the USD [what we looking for ]
         * And the 2nd parameter will be the OMR [what we already got]
         *
         * Ex :
         *    Transaction currency  =>  OMR     => $trCurrency  OR  $trans_currencyID
         *    Transaction Amount    =>  1000/-  => $trAmount    OR  $amount
         *    Reporting Currency    =>  USD     => $reCurrency  OR  $againce_currencyID
         *
         *    $conversionData  = currency_conversion($trCurrency, $reCurrency, $trAmount);
         *    $conversionRate  = $conversionData['conversion'];
         *    $decimalPlace    = $conversionData['DecimalPlaces'];
         *    $reportingAmount = round( ($trAmount / $conversionRate) , $decimalPlace );
         **********************************************************************************************/
        $data = array();
        $CI =& get_instance();
        if ($trans_currencyID == $againce_currencyID) {
            $CI->db->select('currencyID,CurrencyCode,DecimalPlaces,CurrencyName');
            $CI->db->from('srp_erp_companycurrencyassign');
            $CI->db->where('currencyID', $trans_currencyID);
            $CI->db->where('companyID', $company_id);
            $data_arr = $CI->db->get()->row_array();
            $data['currencyID'] = $data_arr['currencyID'];
            $data['conversion'] = 1;
            $data['CurrencyCode'] = $data_arr['CurrencyCode'];
            $data['CurrencyName'] = $data_arr['CurrencyName'];
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['convertedAmount'] = $amount * 1;
        } else {
            $CI->db->select('srp_erp_currencymaster.currencyID,conversion,CurrencyCode,CurrencyName,DecimalPlaces');
            $CI->db->from('srp_erp_companycurrencyconversion');
            $CI->db->where('srp_erp_companycurrencyconversion.masterCurrencyID', $trans_currencyID);
            $CI->db->where('srp_erp_companycurrencyconversion.subCurrencyID', $againce_currencyID);
            $CI->db->where('srp_erp_companycurrencyconversion.companyID', $company_id);
            $CI->db->join('srp_erp_currencymaster',
                'srp_erp_currencymaster.currencyID = srp_erp_companycurrencyconversion.subCurrencyID');
            $data_arr = $CI->db->get()->row_array();
            $data['currencyID'] = $data_arr['currencyID'];
            $data['conversion'] = round($data_arr['conversion'], 9);
            $data['CurrencyCode'] = $data_arr['CurrencyCode'];
            $data['CurrencyName'] = $data_arr['CurrencyName'];
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['convertedAmount'] = $amount * $data_arr['conversion'];
        }

        return $data;
    }
    function currency_conversion($trans_currency, $againce_currency, $amount, $company_id)
    {
        /*********************************************************************************************
         * Always transaction is going with transaction currency [ Transaction Currency => OMR ]
         * If we want to know the reporting amount [ Reporting Currency => USD ]
         * So the currency_conversion functions 1st parameter will be the USD [ what we looking for ]
         * And the 2nd parameter will be the OMR [what we already got]
         *
         * Ex :
         *    Transaction currency  =>  OMR     => $trCurrency  OR  $trans_currency
         *    Transaction Amount    =>  1000/-  => $trAmount    OR  $amount
         *    Reporting Currency    =>  USD     => $reCurrency  OR  $againce_currency
         *
         *    $conversionData  = currency_conversion($trCurrency, $reCurrency, $trAmount);
         *    $conversionRate  = $conversionData['conversion'];
         *    $decimalPlace    = $conversionData['DecimalPlaces'];
         *    $reportingAmount = round( ($trAmount / $conversionRate) , $decimalPlace );
         **********************************************************************************************/
        $data = array();
        $CI =& get_instance();
        if ($trans_currency == $againce_currency) {
            $CI->db->select('currencyID,CurrencyCode,DecimalPlaces,CurrencyName');
            $CI->db->from('srp_erp_companycurrencyassign');
            $CI->db->where('CurrencyCode', $trans_currency);
            $CI->db->where('companyID', $company_id);
            $data_arr = $CI->db->get()->row_array();

            /** Transaction Currency  **/
            $data['trCurrencyID'] = $data_arr['currencyID'];

            /** Conversion currency detail  **/
            $data['currencyID'] = $data_arr['currencyID'];
            $data['conversion'] = 1;
            $data['CurrencyCode'] = $data_arr['CurrencyCode'];
            $data['CurrencyName'] = $data_arr['CurrencyName'];
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['convertedAmount'] = $amount * 1;
        } else {
            $CI->db->select('srp_erp_currencymaster.currencyID,srp_erp_companycurrencyconversion.masterCurrencyID,conversion,CurrencyCode,CurrencyName,DecimalPlaces');
            $CI->db->from('srp_erp_companycurrencyconversion');
            $CI->db->where('srp_erp_companycurrencyconversion.masterCurrencyCode', $trans_currency);
            $CI->db->where('srp_erp_companycurrencyconversion.subCurrencyCode', $againce_currency);
            $CI->db->where('srp_erp_companycurrencyconversion.companyID', $company_id);
            $CI->db->join('srp_erp_currencymaster',
                'srp_erp_currencymaster.currencyID = srp_erp_companycurrencyconversion.subCurrencyID');
            $data_arr = $CI->db->get()->row_array();

            /** Transaction Currency  **/
            $data['trCurrencyID'] = $data_arr['masterCurrencyID'];

            /** Conversion currency detail  **/
            $data['currencyID'] = $data_arr['currencyID'];
            $data['conversion'] = round($data_arr['conversion'], 9);
            $data['CurrencyCode'] = $data_arr['CurrencyCode'];
            $data['CurrencyName'] = $data_arr['CurrencyName'];
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['convertedAmount'] = $amount * $data_arr['conversion'];
        }
        return $data;
    }

    function generate_pos_invoice_code($warehouse_id,$comapany_id,$user_id)
    {
        //$outletInfo = $this->get_outletInfo($user_id,$comapany_id);
        $outletInfo = $this->fetch_warehouse($warehouse_id);
        $CI =& get_instance();
        $CI->db->select("invoiceSequenceNo");
        $CI->db->from('srp_erp_pos_menusalesmaster');
        $CI->db->where('companyID', $comapany_id);
        $CI->db->where('wareHouseAutoID', $warehouse_id);
        $CI->db->order_by('invoiceSequenceNo', 'desc');
        $invoiceSequenceNo = $CI->db->get()->row('invoiceSequenceNo');

        if ($invoiceSequenceNo) {
            $serialNo = $invoiceSequenceNo + 1;
        } else {
            $serialNo = 1;
        }
        return $outletInfo['wareHouseCode'] . str_pad($serialNo, 6, "0", STR_PAD_LEFT);
    }

    function generate_pos_invoice_no($outletID,$companyID)
    {
        $CI =& get_instance();
        $CI->db->select("invoiceSequenceNo");
        $CI->db->from('srp_erp_pos_menusalesmaster');
        $CI->db->where('companyID', $companyID);
        $CI->db->where('wareHouseAutoID', $outletID);
        $CI->db->order_by('invoiceSequenceNo', 'desc');
        $invoiceSequenceNo = $CI->db->get()->row('invoiceSequenceNo');
        if ($invoiceSequenceNo) {
            $serialNo = $invoiceSequenceNo + 1;
        } else {
            $serialNo = 1;
        }
        return $serialNo;
    }

    function get_outletInfo($user_id,$company_id)
    {
        $CI =& get_instance();
        $CI->db->select("srp_erp_warehousemaster.*, srp_erp_warehouse_users.counterID");
        $CI->db->from('srp_erp_warehouse_users');
        $CI->db->join('srp_erp_warehousemaster', ' srp_erp_warehousemaster.wareHouseAutoID =srp_erp_warehouse_users.wareHouseID ', 'left');
        $CI->db->where('srp_erp_warehouse_users.userID', $user_id);
        $CI->db->where('srp_erp_warehouse_users.companyID', $company_id);
        $CI->db->where('srp_erp_warehousemaster.isPosLocation', 1);
        $CI->db->where('srp_erp_warehousemaster.isActive', 1);
        $CI->db->where('srp_erp_warehouse_users.isActive', 1);
        $result = $CI->db->get()->row_array();
        return $result;

    }
     function getCurrencyID_byCurrencyCode($currencyCodee)
    {
        $CI =& get_instance();
        $CI->db->select('currencyID');
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->where('CurrencyCode', $currencyCodee);
        $result = $CI->db->get()->row_array();
        return $result['currencyID'];
    }

    function insert_srp_erp_pos_menusalesitems($data)
    {

        $result = $this->db->insert('srp_erp_pos_menusalesitems', $data);
        if ($result) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    function get_company_id_by_restaurant_id($restaurantID){
        $CI =& get_instance();
        $CI->db->select("companyID");
        $CI->db->from('restaurantmaster');
        $CI->db->where('restaurantID', $restaurantID);
        $companyID = $CI->db->get()->row('companyID');
        return $companyID;
    }

    function get_company_details($company_id){
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_company');
        $CI->db->where('company_id', $company_id);
        return $CI->db->get()->row_array();
    }

//    function generate_pos_invoice_code()
//    {
//        $outletID = get_outletID();
//        $companyID = current_companyID();
//        $outletInfo = get_outletInfo();
//
//        $CI =& get_instance();
//        $CI->db->select("invoiceSequenceNo");
//        $CI->db->from('srp_erp_pos_menusalesmaster');
//        $CI->db->where('companyID', $companyID);
//        $CI->db->where('wareHouseAutoID', $outletID);
//        $CI->db->order_by('invoiceSequenceNo', 'desc');
//        $invoiceSequenceNo = $CI->db->get()->row('invoiceSequenceNo');
//
//
//        if ($invoiceSequenceNo) {
//            $serialNo = $invoiceSequenceNo + 1;
//        } else {
//            $serialNo = 1;
//        }
//        return $outletInfo['wareHouseCode'] . str_pad($serialNo, 6, "0", STR_PAD_LEFT);
//    }

    function get_order_details($config,$menuSalesID){
        //required libraries
        $this->load->library('s3');

        //loading db with specific restaurant db
        $this->load->database($config, FALSE, TRUE);
        $query = $this->db->query("SELECT
	srp_erp_pos_menusalesitems.menuSalesItemID,
	srp_erp_pos_menusalesitems.wareHouseAutoID,
	srp_erp_pos_menusalesitems.warehouseMenuID,	
	srp_erp_pos_menumaster.menuMasterID,
	srp_erp_pos_menumaster.menuMasterDescription,
	srp_erp_pos_menumaster.menuImage,
	srp_erp_pos_menusalesitems.transactionAmount as sellingPrice,
	poscustomer.CustomerName,
	poscustomer.customerTelephone,
	poscustomer.customerEmail,
	srp_erp_pos_menusalesmaster.wowFoodStatus
		
FROM
	srp_erp_pos_menusalesitems
	JOIN srp_erp_pos_menumaster ON srp_erp_pos_menumaster.menuMasterID = srp_erp_pos_menusalesitems.menuID
	JOIN srp_erp_pos_menusalesmaster on srp_erp_pos_menusalesmaster.menuSalesID = srp_erp_pos_menusalesitems.menuSalesID
	JOIN srp_erp_pos_customermaster poscustomer on poscustomer.posCustomerAutoID= srp_erp_pos_menusalesmaster.customerID
WHERE
	srp_erp_pos_menusalesitems.menuSalesID = $menuSalesID");
        $menu_list = array();
        foreach ($query->result() as $row) {
            $menu = array();
            $menu['menuMasterID'] = $row->menuMasterID;
            $menu['menuMasterDescription'] = $row->menuMasterDescription;
            $menu['menuImage'] = "http://".$_SERVER['HTTP_HOST'].base_url($row->menuImage);
            $menu['sellingPrice'] = $row->sellingPrice;
            $menu['warehouseMenuID'] = $row->warehouseMenuID;
            $menu['CustomerName'] = $row->CustomerName;
            $menu['customerTelephone'] = $row->customerTelephone;
            $menu['customerEmail'] = $row->customerEmail;
            $menu['wowFoodStatus'] = $row->wowFoodStatus;
          //  $menu['warehouseID'] = $row->warehouseID;
            array_push($menu_list, $menu);
        }



        return $menu_list;
    }

    function  is_wowfood_enabled($company_id,$outlet_id,$pos_policy_master_id){
        $query=$this->db->get_where('srp_erp_pos_policydetail',array('posPolicyMasterID'=>$pos_policy_master_id,'outletID'=>$outlet_id,'companyID'=>$company_id));
        if($query->num_rows()>0){
            return true;
        }else{
            return false;
        }
    }
    function fetch_warehouse($warehouse_id)
    { 

    $CI =& get_instance();
    $data =  $CI->db->query("select wareHouseCode from srp_erp_warehousemaster where wareHouseAutoID = $warehouse_id")->row_array();
    return $data;

    }

}
