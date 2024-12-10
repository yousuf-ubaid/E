<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Match_exceed extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->load->model('Matchexceed_modal');
        $this->load->helpers('exceedmatch');
    }


    function post_unmatched_inventory_exceed_item(){
        $companyID = current_companyID();
        $wareHouseID = $this->common_data['ware_houseID'];

        $itemexceedItemsWare = $this->db->query("SELECT
	itemAutoID,warehouseAutoID
FROM
	srp_erp_itemexceeded 
WHERE
	balanceQty > 0 
	AND companyID = $companyID 
GROUP BY
	itemAutoID,warehouseAutoID")->result_array();

        if(!empty($itemexceedItemsWare)){
           $itemAutoIDfrmexceed= array_column($itemexceedItemsWare, 'itemAutoID');
           $warehouseAutoIDfrmexceed= array_column($itemexceedItemsWare, 'warehouseAutoID');

            $legritems = $this->db->query("SELECT
	itemAutoID,
	IFNULL( SUM( transactionQTY / convertionRate ), 0 ) AS quntity 
FROM
	srp_erp_itemledger 
WHERE
	companyID = $companyID 
	AND wareHouseAutoID  IN (" . join(',', $warehouseAutoIDfrmexceed) . ") 
	AND itemAutoID  IN (" . join(',', $itemAutoIDfrmexceed) . ") 
GROUP BY
	itemAutoID,
	wareHouseAutoID 
HAVING
	quntity > 0")->result_array();


            $warehouse=array_unique($warehouseAutoIDfrmexceed);

            foreach ($warehouse as $ware){

                $items = $this->db->query("SELECT
	itemAutoID,
	IFNULL( SUM( transactionQTY / convertionRate ), 0 ) AS quntity 
FROM
	srp_erp_itemledger 
WHERE
	companyID = $companyID 
	AND wareHouseAutoID = $ware 
	AND itemAutoID  IN (" . join(',', $itemAutoIDfrmexceed) . ") 
GROUP BY
	itemAutoID,
	wareHouseAutoID 
HAVING
	quntity > 0")->result_array();

                if(!empty($items)){
                    $exceededMatchID = 0;
                    $financeYearDetails=get_financial_year($this->common_data['current_date']);
                    //$this->load->library('sequence');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $this->common_data['current_date'];
                    $exceededmatch['warehouseAutoID'] = $ware;
                    //$exceededmatch['orginDocumentID'] = $master ['documentID'];
                    //$exceededmatch['orginDocumentMasterID'] = $master ['grvAutoID'];
                    //$exceededmatch['orginDocumentSystemCode'] = $master ['grvPrimaryCode'];
                    $exceededmatch['companyFinanceYearID'] = $financeYearDetails['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $default_currency      = currency_conversionID($this->common_data['company_data']['company_default_currencyID'],$this->common_data['company_data']['company_default_currencyID']);

                    $exceededmatch['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $exceededmatch['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $exceededmatch['transactionExchangeRate'] = $default_currency['conversion'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                    $exceededmatch['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $exceededmatch['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $exceededmatch['companyLocalExchangeRate'] = $default_currency['conversion'];
                    $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                    $exceededmatch['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                    $exceededmatch['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                    $reporting_currency    = currency_conversionID($exceededmatch['companyLocalCurrencyID'],$exceededmatch['companyReportingCurrencyID']);
                    $exceededmatch['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

                    $FYBegin=$financeYearDetails['beginingDate'];
                    $FYEnd=$financeYearDetails['endingDate'];
                    $exceededmatch['companyFinanceYear'] = $FYBegin.' - '.$FYEnd;
                    $exceededmatch['FYBegin'] = $FYBegin;
                    $exceededmatch['FYEnd'] = $FYEnd;
                    $financePeriodDetails=get_financial_period_date_wise($this->common_data['current_date']);

                    $exceededmatch['FYPeriodDateFrom'] = $financePeriodDetails ['dateFrom'];
                    $exceededmatch['FYPeriodDateTo'] = $financePeriodDetails ['dateTo'];
                    $exceededmatch['companyFinancePeriodID'] = $financePeriodDetails['companyFinancePeriodID'];
                    $exceededmatch['createdUserGroup'] = $this->common_data['user_group'];
                    $exceededmatch['createdPCID'] = $this->common_data['current_pc'];
                    $exceededmatch['createdUserID'] = $this->common_data['current_userID'];
                    $exceededmatch['createdUserName'] = $this->common_data['current_user'];
                    $exceededmatch['createdDateTime'] = $this->common_data['current_date'];
                    $exceededmatch['documentSystemCode'] = $this->sequence->sequence_generator($exceededmatch['documentID']);
                    $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                    $exceededMatchID = $this->db->insert_id();





                    foreach ($items as $itemid) {
                        $receivedQty = $itemid['quntity'];
                        $receivedQtyConverted = $itemid['quntity'];
                        $companyID = current_companyID();
                        $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $ware . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
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
                                        //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                        $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                        $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                        $this->db->update('srp_erp_itemexceeded', $exeed);

                                        $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                        $exceededmatchdetail['exceededItemAutoID'] = $exceededItemAutoID['exceededItemAutoID'];
                                        $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                        $exceededmatchdetail['warehouseAutoID'] = $ware;
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
                                        $exceededmatchdetails['warehouseAutoID'] = $ware;
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

                    if (!empty($exceededitems)) {
                        exceed_double_entry($exceededMatchID);
                    }





                }
            }

        }

    }




}