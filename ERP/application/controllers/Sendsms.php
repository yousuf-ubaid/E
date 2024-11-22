<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sendsms extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function send_rpos_submit_sms()
    {

        $invoiceID = $this->input->post('invoiceID');
        $outletID = $this->input->post('outletID');
        $smsAllowed = getPolicyValues('SMS', 'All');
        if($smsAllowed==' ' || empty($smsAllowed) || $smsAllowed== null){
            $smsAllowed=0;
        }

        if($smsAllowed==1){

            $db2 = $this->load->database('db2', TRUE);
            $company_id = current_companyID();
            $smsDetails = $db2->query("SELECT * FROM sms_integration WHERE companyID = {$company_id}")->row_array();


            $mstr=$this->db->query("SELECT customerID,isCreditSales FROM srp_erp_pos_menusalesmaster WHERE menuSalesID = {$invoiceID} AND srp_erp_pos_menusalesmaster.companyID={$company_id}")->row_array();
            $billamnt=$this->db->query("SELECT ifnull(sum(amount),0) as billamnt FROM srp_erp_pos_menusalespayments WHERE menuSalesID = {$invoiceID} AND GLCode!=7 group by menuSalesID")->row_array();//AND wareHouseAutoID = $outletID

            if($mstr['customerID']>0){
                $csDetails = $this->db->query("SELECT menuSalesID,srp_erp_pos_menusalesmaster.invoiceCode,subTotal,srp_erp_pos_menusalesmaster.transactionCurrency,customerID,srp_erp_pos_customermaster.customerTelephone,DATE_FORMAT(srp_erp_pos_menusalesmaster.menuSalesDate ,'%d-%m-%Y') as menuSalesDate,DATE_FORMAT(srp_erp_pos_deliveryorders.deliveryDate ,'%d-%m-%Y') as deliveryDate,DATE_FORMAT(srp_erp_pos_deliveryorders.deliveryTime,'%h:%i %p') as deliveryTime,srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces,DATE_FORMAT(srp_erp_pos_menusalesmaster.createdDateTime,'%h:%i %p') as salestime FROM srp_erp_pos_menusalesmaster LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalesmaster.customerID=srp_erp_pos_customermaster.posCustomerAutoID LEFT JOIN srp_erp_pos_deliveryorders ON srp_erp_pos_deliveryorders.menuSalesMasterID=srp_erp_pos_menusalesmaster.menuSalesID WHERE menuSalesID = {$invoiceID} AND srp_erp_pos_menusalesmaster.companyID={$company_id}")->row_array();
            }elseif ($mstr['isCreditSales']==1){
                $csDetails = $this->db->query("SELECT srp_erp_pos_menusalesmaster.menuSalesID,srp_erp_pos_menusalesmaster.invoiceCode,subTotal,srp_erp_pos_menusalesmaster.transactionCurrency,srp_erp_pos_menusalespayments.customerAutoID as customerID,srp_erp_pos_customermaster.customerTelephone,DATE_FORMAT(srp_erp_pos_menusalesmaster.menuSalesDate ,'%d-%m-%Y') as menuSalesDate,DATE_FORMAT(srp_erp_pos_deliveryorders.deliveryTime,'%h:%i %p') as deliveryTime,DATE_FORMAT(srp_erp_pos_deliveryorders.deliveryDate ,'%d-%m-%Y') as deliveryDate,srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces,DATE_FORMAT(srp_erp_pos_menusalesmaster.createdDateTime,'%h:%i %p') as salestime FROM srp_erp_pos_menusalesmaster LEFT JOIN srp_erp_pos_menusalespayments ON srp_erp_pos_menusalespayments.menuSalesID=srp_erp_pos_menusalesmaster.menuSalesID AND srp_erp_pos_menusalespayments.wareHouseAutoID=srp_erp_pos_menusalesmaster.wareHouseAutoID  LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalespayments.customerAutoID=srp_erp_pos_customermaster.CustomerAutoID LEFT JOIN srp_erp_pos_deliveryorders ON srp_erp_pos_deliveryorders.menuSalesMasterID=srp_erp_pos_menusalesmaster.menuSalesID WHERE srp_erp_pos_menusalesmaster.menuSalesID = {$invoiceID} AND paymentConfigMasterID=7 AND srp_erp_pos_menusalesmaster.companyID={$company_id}")->row_array();

            }else{
                $csDetails['customerTelephone']='';
            }


            /*$csDetails = $this->db->query("SELECT menuSalesID,srp_erp_pos_menusalesmaster.serialNo,subTotal,srp_erp_pos_menusalesmaster.transactionCurrency,customerID,srp_erp_pos_customermaster.customerTelephone,srp_erp_pos_menusalesmaster.menuSalesDate,srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces FROM srp_erp_pos_menusalesmaster LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalesmaster.customerID=srp_erp_pos_customermaster.posCustomerAutoID WHERE menuSalesID = {$invoiceID} AND srp_erp_pos_menusalesmaster.companyID={$company_id}")->row_array();*/


            if(!empty($smsDetails)){
                if(!empty($csDetails['customerTelephone'])){
                    if($smsDetails['APIName']=='caravanAPI'){
                        $telno=$csDetails['customerTelephone'];
                        $apiUserName=$smsDetails['apiUserName'];
                        $apiPassword=$smsDetails['apiPassword'];
                        $apidciml=$csDetails['transactionCurrencyDecimalPlaces'];
                        $apiamnt=number_format($csDetails['subTotal']-$billamnt['billamnt'],$apidciml);
                        $apicurrncy=$csDetails['transactionCurrency'];
                        if(!empty($csDetails['deliveryDate'])){
                            $apiDate=$csDetails['deliveryDate'];
                            $salestime=$csDetails['deliveryTime'];
                        }else{
                            $apiDate=$csDetails['menuSalesDate'];
                            $salestime=$csDetails['salestime'];
                        }
                        $salestime = str_replace(' ','',$salestime);


                        $apiinvCode=trim($csDetails['invoiceCode'],'');
                        //$lnk='https://www.ismartsms.net/iBulkSMS/HttpWS/SMSDynamicAPI.aspx?UserId='.$apiUserName.'&Password='.$apiPassword.'&MobileNo=968'.$telno.'&Message=Thank%20You%20for%20choosing%20Caravans%20Catering,%20your%20order%20for%20'.$apiamnt.'%20'.$apicurrncy.'%20on%20'.$apiDate.'%20is%20submitted%20under%20'.$apiinvCode.'&Lang=0&FLashSMS=N';
                        $lnk='https://www.ismartsms.net/iBulkSMS/HttpWS/SMSDynamicAPI.aspx?UserId='.$apiUserName.'&Password='.$apiPassword.'&MobileNo=968'.$telno.'&Message=Thank%20You%20for%20choosing%20Caravans%20Catering.%20your%20order%20No.%20'.$apiinvCode.'%20for%20the%20'.$apiDate.'%20at%20'.$salestime.'%20is%20confirmed%20and%20the%20total%20outstanding%20is%20'.$apiamnt.'%20'.$apicurrncy.''.'&Lang=0&FLashSMS=N';
                         /*echo $lnk;exit;*/
                        $cURLConnection = curl_init();
                        curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, FALSE);
                        curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, FALSE);
                        //curl_setopt($cURLConnection, CURLOPT_URL, 'https://www.ismartsms.net/iBulkSMS/HttpWS/SMSDynamicAPI.aspx?UserId=caravansweb&Password=K$gkrb30&MobileNo=96892800281&Message=Thank%20You%20for%20purchasing%20with%20Caravans,%20your%20order%20for%20100.111%20OMR%20on%2011/02/2020%20is%20submitted%20under%20INV014950&Lang=0&FLashSMS=N');
                        curl_setopt($cURLConnection, CURLOPT_URL, $lnk);
                        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
                        $phoneList = curl_exec($cURLConnection);
                        curl_close($cURLConnection);

                        $jsonArrayResponse = json_decode($phoneList);
                        echo json_encode($jsonArrayResponse);
                    }


                }
            }
        }
    }

    function send_rpos_dispatched_sms()
    {

        $invoiceID = $this->input->post('menuSalesID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $smsAllowed = getPolicyValues('SMS', 'All');
        if($smsAllowed==' ' || empty($smsAllowed) || $smsAllowed== null){
            $smsAllowed=0;
        }

        if($smsAllowed==1){

            $db2 = $this->load->database('db2', TRUE);
            $company_id = current_companyID();
            $smsDetails = $db2->query("SELECT * FROM sms_integration WHERE companyID = {$company_id}")->row_array();

            $mstr=$this->db->query("SELECT customerID,isCreditSales FROM srp_erp_pos_menusalesmaster WHERE menuSalesID = {$invoiceID} AND srp_erp_pos_menusalesmaster.companyID={$company_id}")->row_array();

            if($mstr['customerID']>0){
                $csDetails = $this->db->query("SELECT menuSalesID,srp_erp_pos_menusalesmaster.invoiceCode,subTotal,srp_erp_pos_menusalesmaster.transactionCurrency,customerID,srp_erp_pos_customermaster.customerTelephone,DATE_FORMAT(srp_erp_pos_menusalesmaster.menuSalesDate ,'%d-%m-%Y') as menuSalesDate,srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces FROM srp_erp_pos_menusalesmaster LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalesmaster.customerID=srp_erp_pos_customermaster.posCustomerAutoID WHERE menuSalesID = {$invoiceID} AND srp_erp_pos_menusalesmaster.companyID={$company_id} AND srp_erp_pos_menusalesmaster.wareHouseAutoID={$wareHouseAutoID}")->row_array();
            }elseif ($mstr['isCreditSales']==1){
                $csDetails = $this->db->query("SELECT srp_erp_pos_menusalesmaster.menuSalesID,srp_erp_pos_menusalesmaster.invoiceCode,subTotal,srp_erp_pos_menusalesmaster.transactionCurrency,srp_erp_pos_menusalespayments.customerAutoID as customerID,srp_erp_pos_customermaster.customerTelephone,DATE_FORMAT(srp_erp_pos_menusalesmaster.menuSalesDate ,'%d-%m-%Y') as menuSalesDate,srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces FROM srp_erp_pos_menusalesmaster LEFT JOIN srp_erp_pos_menusalespayments ON srp_erp_pos_menusalespayments.menuSalesID=srp_erp_pos_menusalesmaster.menuSalesID AND srp_erp_pos_menusalespayments.wareHouseAutoID=srp_erp_pos_menusalesmaster.wareHouseAutoID  LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalespayments.customerAutoID=srp_erp_pos_customermaster.CustomerAutoID WHERE srp_erp_pos_menusalesmaster.menuSalesID = {$invoiceID} AND paymentConfigMasterID=7 AND srp_erp_pos_menusalesmaster.companyID={$company_id} AND srp_erp_pos_menusalesmaster.wareHouseAutoID={$wareHouseAutoID}")->row_array();

            }else{
                $csDetails['customerTelephone']='';
            }


            /*$csDetails = $this->db->query("SELECT menuSalesID,srp_erp_pos_menusalesmaster.serialNo,subTotal,srp_erp_pos_menusalesmaster.transactionCurrency,customerID,srp_erp_pos_customermaster.customerTelephone,srp_erp_pos_menusalesmaster.menuSalesDate,srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces FROM srp_erp_pos_menusalesmaster LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalesmaster.customerID=srp_erp_pos_customermaster.posCustomerAutoID WHERE menuSalesID = {$invoiceID} AND srp_erp_pos_menusalesmaster.companyID={$company_id} AND srp_erp_pos_menusalesmaster.wareHouseAutoID={$wareHouseAutoID}")->row_array();*/


            if(!empty($smsDetails)){
                if(!empty($csDetails['customerTelephone'])){
                    if($smsDetails['APIName']=='caravanAPI'){
                        $telno=$csDetails['customerTelephone'];
                        $apiUserName=$smsDetails['apiUserName'];
                        $apiPassword=$smsDetails['apiPassword'];
                        $apidciml=$csDetails['transactionCurrencyDecimalPlaces'];

                        $apiamnt=number_format($csDetails['subTotal'],$apidciml);
                        $apicurrncy=$csDetails['transactionCurrency'];
                        $apiDate=$csDetails['menuSalesDate'];
                        $apiinvCode=trim($csDetails['invoiceCode'],'');
                        $lnk='https://www.ismartsms.net/iBulkSMS/HttpWS/SMSDynamicAPI.aspx?UserId='.$apiUserName.'&Password='.$apiPassword.'&MobileNo=968'.$telno.'&Message=Your%20order%20%No%20'.$apiinvCode.'%20is%20ready%20for%20collection&Lang=0&FLashSMS=N';
                        // echo $lnk;exit;
                        $cURLConnection = curl_init();
                        curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, FALSE);
                        curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, FALSE);
                        //curl_setopt($cURLConnection, CURLOPT_URL, 'https://www.ismartsms.net/iBulkSMS/HttpWS/SMSDynamicAPI.aspx?UserId=caravansweb&Password=K$gkrb30&MobileNo=96892800281&Message=Thank%20You%20for%20purchasing%20with%20Caravans,%20your%20order%20for%20100.111%20OMR%20on%2011/02/2020%20is%20submitted%20under%20INV014950&Lang=0&FLashSMS=N');
                        curl_setopt($cURLConnection, CURLOPT_URL, $lnk);
                        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
                        $phoneList = curl_exec($cURLConnection);
                        curl_close($cURLConnection);

                        $jsonArrayResponse = json_decode($phoneList);
                        echo json_encode($jsonArrayResponse);
                    }


                }
            }
        }
    }


    function send_rpos_submit_sms_chk_cus()
    {

        $invoiceID = $this->input->post('invoiceID');
        $company_id = current_companyID();

        $mstr=$this->db->query("SELECT customerID,isCreditSales FROM srp_erp_pos_menusalesmaster WHERE menuSalesID = {$invoiceID} AND srp_erp_pos_menusalesmaster.companyID={$company_id}")->row_array();
        $billamnt=$this->db->query("SELECT ifnull(sum(amount),0) as billamnt FROM srp_erp_pos_menusalespayments WHERE menuSalesID = {$invoiceID} AND GLCode!=7 group by menuSalesID")->row_array();//AND wareHouseAutoID = $outletID
        if($mstr['customerID']>0){
            $csDetails = $this->db->query("SELECT menuSalesID,srp_erp_pos_menusalesmaster.invoiceCode,srp_erp_pos_menusalesmaster.serialNo,subTotal,srp_erp_pos_menusalesmaster.transactionCurrency,customerID,srp_erp_pos_customermaster.customerTelephone,DATE_FORMAT(srp_erp_pos_menusalesmaster.menuSalesDate ,'%d-%m-%Y') as menuSalesDate,DATE_FORMAT(srp_erp_pos_deliveryorders.deliveryDate ,'%d-%m-%Y') as deliveryDate,srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces,DATE_FORMAT(srp_erp_pos_menusalesmaster.createdDateTime,'%h:%i %p') as salestime,DATE_FORMAT(srp_erp_pos_deliveryorders.deliveryTime,'%h:%i %p') as deliveryTime FROM srp_erp_pos_menusalesmaster LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalesmaster.customerID=srp_erp_pos_customermaster.posCustomerAutoID LEFT JOIN srp_erp_pos_deliveryorders ON srp_erp_pos_deliveryorders.menuSalesMasterID=srp_erp_pos_menusalesmaster.menuSalesID WHERE menuSalesID = {$invoiceID} AND srp_erp_pos_menusalesmaster.companyID={$company_id}")->row_array();

            if(!empty($csDetails)){
                if(!empty($csDetails['customerTelephone'])){
                    $apidciml=$csDetails['transactionCurrencyDecimalPlaces'];
                    $apiamnt=number_format($csDetails['subTotal']-$billamnt['billamnt'],$apidciml);
                    $apicurrncy=$csDetails['transactionCurrency'];
                    if(!empty($csDetails['deliveryDate'])){
                        $apiDate=$csDetails['deliveryDate'];
                        $salestime=$csDetails['deliveryTime'];
                    }else{
                        $apiDate=$csDetails['menuSalesDate'];
                        $salestime=$csDetails['salestime'];
                    }

                    $apiinvCode=trim($csDetails['invoiceCode'],'');

                    $msg='Thank you for choosing Caravans Catering. Your order No. '.$apiinvCode.' for the '.$apiDate.' at '.$salestime.' is confirmed and the total outstanding is '.$apiamnt.' '.$apicurrncy.'';
                    echo json_encode(array(true,$msg));
                }
            }
        }else if ($mstr['isCreditSales']==1){
            $csDetails = $this->db->query("SELECT srp_erp_pos_menusalesmaster.menuSalesID,srp_erp_pos_menusalesmaster.invoiceCode,srp_erp_pos_menusalesmaster.serialNo,subTotal,srp_erp_pos_menusalesmaster.transactionCurrency,srp_erp_pos_menusalespayments.customerAutoID as customerID,srp_erp_pos_customermaster.customerTelephone,DATE_FORMAT(srp_erp_pos_menusalesmaster.menuSalesDate ,'%d-%m-%Y') as menuSalesDate,DATE_FORMAT(srp_erp_pos_deliveryorders.deliveryDate ,'%d-%m-%Y') as deliveryDate,srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces,DATE_FORMAT(srp_erp_pos_menusalesmaster.createdDateTime,'%h:%i %p') as salestime,DATE_FORMAT(srp_erp_pos_deliveryorders.deliveryTime,'%h:%i %p') as deliveryTime FROM srp_erp_pos_menusalesmaster LEFT JOIN srp_erp_pos_menusalespayments ON srp_erp_pos_menusalespayments.menuSalesID=srp_erp_pos_menusalesmaster.menuSalesID AND srp_erp_pos_menusalespayments.wareHouseAutoID=srp_erp_pos_menusalesmaster.wareHouseAutoID  LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalespayments.customerAutoID=srp_erp_pos_customermaster.CustomerAutoID LEFT JOIN srp_erp_pos_deliveryorders ON srp_erp_pos_deliveryorders.menuSalesMasterID=srp_erp_pos_menusalesmaster.menuSalesID WHERE srp_erp_pos_menusalesmaster.menuSalesID = {$invoiceID} AND paymentConfigMasterID=7 AND srp_erp_pos_menusalesmaster.companyID={$company_id}")->row_array();

            $apidciml=$csDetails['transactionCurrencyDecimalPlaces'];
            $apiamnt=number_format($csDetails['subTotal']-$billamnt['billamnt'],$apidciml);
            $apicurrncy=$csDetails['transactionCurrency'];
            if(!empty($csDetails['deliveryDate'])){
                $apiDate=$csDetails['deliveryDate'];
                $salestime=$csDetails['deliveryTime'];
            }else{
                $apiDate=$csDetails['menuSalesDate'];
                $salestime=$csDetails['salestime'];
            }

            $apiinvCode=trim($csDetails['invoiceCode'],'');

            $msg='Thank you for choosing Caravans Catering. Your order No. '.$apiinvCode.' for the '.$apiDate.' at '.$salestime.' is confirmed and the total outstanding is '.$apiamnt.' '.$apicurrncy.'';
            if(!empty($csDetails)){
                if(!empty($csDetails['customerTelephone'])){
                    echo json_encode(array(true,$msg));
                }
            }

        }else{
            echo json_encode(array(false,'false'));
        }
    }


    function send_rpos_dispatched_sms_chk_cus()
    {

        $invoiceID = $this->input->post('menuSalesID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $company_id = current_companyID();

        $mstr=$this->db->query("SELECT customerID,isCreditSales FROM srp_erp_pos_menusalesmaster WHERE menuSalesID = {$invoiceID} AND srp_erp_pos_menusalesmaster.companyID={$company_id}")->row_array();
        if($mstr['customerID']>0){
            $csDetails = $this->db->query("SELECT menuSalesID,srp_erp_pos_menusalesmaster.invoiceCode,srp_erp_pos_menusalesmaster.serialNo,subTotal,srp_erp_pos_menusalesmaster.transactionCurrency,customerID,srp_erp_pos_customermaster.customerTelephone,DATE_FORMAT(srp_erp_pos_menusalesmaster.menuSalesDate ,'%d-%m-%Y') as menuSalesDate,srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces,DATE_FORMAT(srp_erp_pos_menusalesmaster.createdDateTime,'%h:%i %p') as salestime FROM srp_erp_pos_menusalesmaster LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalesmaster.customerID=srp_erp_pos_customermaster.posCustomerAutoID WHERE menuSalesID = {$invoiceID} AND srp_erp_pos_menusalesmaster.companyID={$company_id} AND srp_erp_pos_menusalesmaster.wareHouseAutoID={$wareHouseAutoID}")->row_array();
            if(!empty($csDetails)){
                if(!empty($csDetails['customerTelephone'])){
                    $apidciml=$csDetails['transactionCurrencyDecimalPlaces'];
                    $apiamnt=number_format($csDetails['subTotal'],$apidciml);
                    $apicurrncy=$csDetails['transactionCurrency'];
                    $apiDate=$csDetails['menuSalesDate'];
                    $salestime=$csDetails['salestime'];
                    $apiinvCode=trim($csDetails['invoiceCode'],'');
                    $msg='Your order No '.$apiinvCode.' is ready for collection';
                    echo json_encode(array(true,$msg));
                }
            }
        }else if ($mstr['isCreditSales']==1){
            $csDetails = $this->db->query("SELECT srp_erp_pos_menusalesmaster.menuSalesID,srp_erp_pos_menusalesmaster.invoiceCode,srp_erp_pos_menusalesmaster.serialNo,subTotal,srp_erp_pos_menusalesmaster.transactionCurrency,srp_erp_pos_menusalespayments.customerAutoID as customerID,srp_erp_pos_customermaster.customerTelephone,DATE_FORMAT(srp_erp_pos_menusalesmaster.menuSalesDate ,'%d-%m-%Y') as menuSalesDate,srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces,DATE_FORMAT(srp_erp_pos_menusalesmaster.createdDateTime,'%h:%i %p') as salestime FROM srp_erp_pos_menusalesmaster LEFT JOIN srp_erp_pos_menusalespayments ON srp_erp_pos_menusalespayments.menuSalesID=srp_erp_pos_menusalesmaster.menuSalesID AND srp_erp_pos_menusalespayments.wareHouseAutoID=srp_erp_pos_menusalesmaster.wareHouseAutoID  LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalespayments.customerAutoID=srp_erp_pos_customermaster.CustomerAutoID WHERE srp_erp_pos_menusalesmaster.menuSalesID = {$invoiceID} AND paymentConfigMasterID=7 AND srp_erp_pos_menusalesmaster.companyID={$company_id} AND srp_erp_pos_menusalesmaster.wareHouseAutoID={$wareHouseAutoID}")->row_array();

            if(!empty($csDetails)){
                if(!empty($csDetails['customerTelephone'])){
                    $apidciml=$csDetails['transactionCurrencyDecimalPlaces'];
                    $apiamnt=number_format($csDetails['subTotal'],$apidciml);
                    $apicurrncy=$csDetails['transactionCurrency'];
                    $apiDate=$csDetails['menuSalesDate'];
                    $salestime=$csDetails['salestime'];
                    $apiinvCode=trim($csDetails['invoiceCode'],'');
                    $msg='Your order No '.$apiinvCode.' is ready for collection';

                    echo json_encode(array(true,$msg));
                }
            }

        }else{
            echo json_encode(array(false,'false'));
        }
    }



}