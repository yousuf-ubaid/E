<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('exceed_double_entry')) { /*get po action list*/
    function exceed_double_entry($exceededMatchID)
    {
        $CI =& get_instance();
        $matchMaster = $CI->db->query("SELECT * FROM srp_erp_itemexceededmatch WHERE exceededMatchID = '" . $exceededMatchID . "'")->row_array();
        $matchDetail = $CI->db->query("SELECT * FROM srp_erp_itemexceededmatchdetails WHERE exceededMatchID = '" . $exceededMatchID . "'")->result_array();
        $CI->db->query("LOCK TABLES srp_erp_itemledger WRITE, srp_erp_itemmaster READ, srp_erp_itemmaster as itemmaster WRITE, srp_erp_warehouseitems WRITE, srp_erp_warehousemaster READ, srp_erp_chartofaccounts READ");
        foreach ($matchDetail as $val) {
            $item = fetch_item_data($val['itemAutoID']);
            $item_arr['itemAutoID'] = $val['itemAutoID'];
            $item_arr['currentStock'] = ($item['currentStock'] - ($val['matchedQty'] / $val['conversionRateUOM']));
//            $item_arr['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
//            $item_arr['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
            $qty = ($val['matchedQty'] / $val['conversionRateUOM']);
            $itemSystemCode = $val['itemAutoID'];
            $wareHouseAutoID = $val['warehouseAutoID'];
            $CI->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");

            $itemledger_arr['documentID'] = $matchMaster['documentID'];
            $itemledger_arr['documentCode'] = $matchMaster['documentID'];
            $itemledger_arr['documentAutoID'] = $exceededMatchID;
            $itemledger_arr['documentSystemCode'] = $matchMaster['documentSystemCode'];
            $itemledger_arr['documentDate'] = $matchMaster['documentDate'];
            //$itemledger_arr['referenceNumber'] = $details_arr['issueRefNo'];
            $itemledger_arr['companyFinanceYearID'] = $matchMaster['companyFinanceYearID'];
            $itemledger_arr['companyFinanceYear'] = $matchMaster['companyFinanceYear'];
            $itemledger_arr['FYBegin'] = $matchMaster['FYBegin'];
            $itemledger_arr['FYEnd'] = $matchMaster['FYEnd'];
            $itemledger_arr['FYPeriodDateFrom'] = $matchMaster['FYPeriodDateFrom'];
            $itemledger_arr['FYPeriodDateTo'] = $matchMaster['FYPeriodDateTo'];
            $itemledger_arr['wareHouseAutoID'] = $val['warehouseAutoID'];

            $CI->db->SELECT("wareHouseCode,wareHouseDescription,wareHouseLocation,wareHouseAutoID");
            $CI->db->FROM('srp_erp_warehousemaster');
            $CI->db->WHERE('wareHouseAutoID', $val['warehouseAutoID']);
            $location = $CI->db->get()->row_array();
            $itemledger_arr['wareHouseCode'] = $location['wareHouseCode'];
            $itemledger_arr['wareHouseLocation'] = $location['wareHouseLocation'];
            $itemledger_arr['wareHouseDescription'] = $location['wareHouseDescription'];
           /* $itemledger_arr['projectID'] = $details_arr['projectID'];
            $itemledger_arr['projectExchangeRate'] = $details_arr['projectExchangeRate'];*/
            $itemledger_arr['itemAutoID'] = $val['itemAutoID'];
            $itemledger_arr['itemSystemCode'] = $item['itemSystemCode'];
            $itemledger_arr['itemDescription'] = $item['itemDescription'];
            $itemledger_arr['defaultUOM'] = $val['defaultUOM'];
            $itemledger_arr['transactionUOM'] = $val['unitOfMeasure'];
            $itemledger_arr['transactionQTY'] = ($val['matchedQty'] * -1);
            $itemledger_arr['convertionRate'] = $val['conversionRateUOM'];
            $itemledger_arr['currentStock'] = $item_arr['currentStock'];
            $itemledger_arr['itemAutoID'] = $val['itemAutoID'];
            $itemledger_arr['itemSystemCode'] = $item['itemSystemCode'];
            $itemledger_arr['itemDescription'] = $item['itemDescription'];
            $itemledger_arr['defaultUOMID'] = $val['defaultUOMID'];
            $itemledger_arr['transactionUOMID'] = $val['unitOfMeasureID'];
            $itemledger_arr['transactionUOM'] = $val['unitOfMeasure'];
            $itemledger_arr['convertionRate'] = $val['conversionRateUOM'];
            $PLGLAutoID=fetch_gl_account_desc($val['costGLAutoID']);
            $itemledger_arr['PLGLAutoID'] = $val['costGLAutoID'];
            $itemledger_arr['PLSystemGLCode'] = $PLGLAutoID['systemAccountCode'];
            $itemledger_arr['PLGLCode'] = $PLGLAutoID['GLSecondaryCode'];
            $itemledger_arr['PLDescription'] = $PLGLAutoID['GLDescription'];
            $itemledger_arr['PLType'] = $PLGLAutoID['subCategory'];
            $BLGLAutoID=fetch_gl_account_desc($val['assetGLAutoID']);
            $itemledger_arr['BLGLAutoID'] = $val['assetGLAutoID'];
            $itemledger_arr['BLSystemGLCode'] = $BLGLAutoID['systemAccountCode'];
            $itemledger_arr['BLGLCode'] = $BLGLAutoID['GLSecondaryCode'];
            $itemledger_arr['BLDescription'] = $BLGLAutoID['GLDescription'];
            $itemledger_arr['BLType'] = $BLGLAutoID['subCategory'];
            $itemledger_arr['transactionCurrencyID'] = $matchMaster['companyLocalCurrencyID'];
            $itemledger_arr['transactionCurrency'] = $matchMaster['companyLocalCurrency'];
            $itemledger_arr['transactionExchangeRate'] = $matchMaster['companyLocalExchangeRate'];
            $itemledger_arr['transactionCurrencyDecimalPlaces'] = $matchMaster['companyLocalCurrencyDecimalPlaces'];
            $itemledger_arr['companyLocalCurrencyID'] = $matchMaster['companyLocalCurrencyID'];
            $itemledger_arr['companyLocalCurrency'] = $matchMaster['companyLocalCurrency'];
            $itemledger_arr['companyLocalExchangeRate'] = $matchMaster['companyLocalExchangeRate'];
            $itemledger_arr['companyLocalCurrencyDecimalPlaces'] = $matchMaster['companyLocalCurrencyDecimalPlaces'];
            $itemledger_arr['transactionAmount'] = (round(($val['totalValue'] / $matchMaster['companyLocalExchangeRate']), $itemledger_arr['companyLocalCurrencyDecimalPlaces']) * -1);
            $itemledger_arr['companyLocalAmount'] = (round(($val['totalValue'] / $matchMaster['companyLocalExchangeRate']), $itemledger_arr['companyLocalCurrencyDecimalPlaces']) * -1);
            $itemledger_arr['companyLocalWacAmount'] = round($item['companyLocalWacAmount'], $itemledger_arr['companyLocalCurrencyDecimalPlaces']);
            $itemledger_arr['companyReportingCurrencyID'] = $matchMaster['companyReportingCurrencyID'];
            $itemledger_arr['companyReportingCurrency'] = $matchMaster['companyReportingCurrency'];
            $itemledger_arr['companyReportingExchangeRate'] = $matchMaster['companyReportingExchangeRate'];
            $itemledger_arr['companyReportingCurrencyDecimalPlaces'] = $matchMaster['companyReportingCurrencyDecimalPlaces'];
            $itemledger_arr['companyReportingAmount'] = (round(($val['totalValue'] / $matchMaster['companyReportingExchangeRate']), $itemledger_arr['companyReportingCurrencyDecimalPlaces']) * -1);
            $itemledger_arr['companyReportingWacAmount'] = round(($itemledger_arr['companyLocalWacAmount'] / $itemledger_arr['companyReportingExchangeRate']), $itemledger_arr['companyReportingCurrencyDecimalPlaces']);
            $itemledger_arr['confirmedYN'] = 1;
            $itemledger_arr['confirmedByEmpID'] = current_userID();
            $itemledger_arr['confirmedByName'] = current_user();
            $itemledger_arr['confirmedDate'] = current_date(false);
            $itemledger_arr['approvedYN'] = 1;
            $itemledger_arr['approvedDate'] = current_date(false);
            $itemledger_arr['approvedbyEmpID'] = current_userID();
            $itemledger_arr['approvedbyEmpName'] = current_user();
            $itemledger_arr['segmentID'] = $val['segmentID'];
            $itemledger_arr['segmentCode'] = $val['segmentCode'];
            $itemledger_arr['companyID'] = current_companyID();
            $itemledger_arr['companyCode'] = current_companyCode();
            $itemledger_arr['createdUserGroup'] = current_user_group();
            $itemledger_arr['createdPCID'] = current_pc();
            $itemledger_arr['createdUserID'] = current_userID();
            $itemledger_arr['createdDateTime'] = current_date();
            $itemledger_arr['createdUserName'] = current_user();
            $itemledger_arr['modifiedPCID'] = current_pc();
            $itemledger_arr['modifiedUserID'] = current_userID();
            $itemledger_arr['modifiedDateTime'] = current_date();
            $itemledger_arr['modifiedUserName'] = current_user();

            if (!empty($item_arr)) {
                $CI->db->where('itemAutoID', $val['itemAutoID']);
                $CI->db->update('srp_erp_itemmaster as itemmaster', $item_arr);
            }

            if (!empty($itemledger_arr)) {
                $CI->db->insert('srp_erp_itemledger', $itemledger_arr);
            }
        }
        $CI->db->query("UNLOCK TABLES");


        $CI->load->model('Double_entry_model');
        $double_entry = $CI->Double_entry_model->fetch_double_entry_exceeded_match_data($exceededMatchID, 'EIM');
        for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
            $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['exceededMatchID'];
            $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
            $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['documentSystemCode'];
            $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['documentDate'];
            $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['documentDate'];
            $generalledger_arr[$i]['documentMonth'] = date("m",
                strtotime($double_entry['master_data']['documentDate']));
            $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['documentID'];
            $generalledger_arr[$i]['chequeNumber'] = '';
            $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
            $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
            $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
            $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
            $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
            $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
            $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
            $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
            $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
            $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
            $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
            $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
            $generalledger_arr[$i]['partyContractID'] = '';
            // $generalledger_arr[$i]['partyType']                                 = 'SUP';
            // $generalledger_arr[$i]['partyAutoID']                               = $double_entry['master_data']['supplierID'];
            // $generalledger_arr[$i]['partySystemCode']                           = $double_entry['master_data']['supplierSystemCode'];
            // $generalledger_arr[$i]['partyName']                                 = $double_entry['master_data']['supplierName'];
            // $generalledger_arr[$i]['partyCurrency']                             = $double_entry['master_data']['supplierCurrency'];
            // $generalledger_arr[$i]['partyExchangeRate']                         = $double_entry['master_data']['supplierCurrencyExchangeRate'];
            // $generalledger_arr[$i]['partyCurrencyDecimalPlaces']                = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
            $generalledger_arr[$i]['confirmedByEmpID'] =current_userID();
            $generalledger_arr[$i]['confirmedByName'] = current_user();
            $generalledger_arr[$i]['confirmedDate'] = current_date(false);
            $generalledger_arr[$i]['approvedDate'] = current_date(false);
            $generalledger_arr[$i]['approvedbyEmpID'] = current_userID();
            $generalledger_arr[$i]['approvedbyEmpName'] = current_user();
            $generalledger_arr[$i]['companyID'] = current_companyID();
            $generalledger_arr[$i]['companyCode'] = current_companyCode();
            $amount = $double_entry['gl_detail'][$i]['gl_dr'];
            if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
            }
            $generalledger_arr[$i]['transactionAmount'] = round($amount / $generalledger_arr[$i]['companyLocalExchangeRate'], $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
            $generalledger_arr[$i]['companyLocalAmount'] = round($amount / $generalledger_arr[$i]['companyLocalExchangeRate'], $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
            $generalledger_arr[$i]['companyReportingAmount'] = round($amount / $generalledger_arr[$i]['companyReportingExchangeRate'], $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
            //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']*$generalledger_arr[$i]['partyExchangeRate']),4);
            $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
            $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
            $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
            $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
            $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
            $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
            $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
            $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
            $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
            //$generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : NULL;
            $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
            $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
            $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
            $generalledger_arr[$i]['createdUserGroup'] = current_user_group();
            $generalledger_arr[$i]['createdPCID'] = current_pc();
            $generalledger_arr[$i]['createdUserID'] = current_userID();
            $generalledger_arr[$i]['createdDateTime'] = current_date();
            $generalledger_arr[$i]['createdUserName'] = current_user();
            $generalledger_arr[$i]['modifiedPCID'] = current_pc();
            $generalledger_arr[$i]['modifiedUserID'] = current_userID();
            $generalledger_arr[$i]['modifiedDateTime'] = current_date();
            $generalledger_arr[$i]['modifiedUserName'] = current_user();
        }

        if (!empty($generalledger_arr)) {
            $CI->db->query("LOCK TABLES srp_erp_generalledger WRITE");
            $CI->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            $CI->db->query("UNLOCK TABLES");
        }
    }
}
