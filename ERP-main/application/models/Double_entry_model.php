<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Double_entry_model extends CI_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function fetch_double_entry_grv_data($grvAutoID, $code = null)
    {
        $gl_array = array();
        $cr_total = 0;
        $grv_total = 0;
        $addon_total = 0;
        $addon_item = array();
        $m_arr = array();
        $expanss_item = array();
        $gl_array['gl_detail'] = array();

        $companyID = current_companyID();
        $UGRV_ID = $this->db->query("SELECT srp_erp_chartofaccounts.GLAutoID 
                    FROM srp_erp_chartofaccounts
                    JOIN srp_erp_companycontrolaccounts ON srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
                    WHERE controllAccountYN = 1 AND srp_erp_companycontrolaccounts.companyID = {$companyID} AND srp_erp_chartofaccounts.companyID = {$companyID} AND controlAccountType = 'UGRV'")->row_array();
        $UGRV = fetch_gl_account_desc($UGRV_ID['GLAutoID']);

        $this->db->select('srp_erp_grvmaster.*,srp_erp_suppliermaster.supplierName as suppliernamemaster');
        $this->db->where('grvAutoID', $grvAutoID);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_grvmaster.supplierID', 'left');
        $master = $this->db->get('srp_erp_grvmaster')->row_array();

        $this->db->select('grvDetailsID,itemFinanceCategory,PLGLAutoID,PLSystemGLCode,PLGLCode,PLDescription, PLType,BLGLAutoID, BLSystemGLCode ,BLGLCode,BLDescription, BLType,receivedTotalAmount,financeCategory,projectID,projectExchangeRate,project_categoryID,project_subCategoryID');
        $this->db->where('grvAutoID', $grvAutoID);
        $detail = $this->db->get('srp_erp_grvdetails')->result_array();
        for ($i = 0; $i < count($detail); $i++) {
            $grv_total += $detail[$i]['receivedTotalAmount'];
        }

        $this->db->select('id,GLAutoID,systemGLCode,GLCode,GLDescription,GLType,sum(total_amount) as total_amount,isChargeToExpense,impactFor, supplierName, supplierID,supplierSystemCode,projectID,projectExchangeRate');
        $this->db->group_by("GLAutoID");
        $this->db->group_by("supplierID");
        $this->db->where('grvAutoID', $grvAutoID);
        $addon_detail = $this->db->get('srp_erp_grv_addon')->result_array();
        if (!empty($addon_detail)) {
            for ($i = 0; $i < count($addon_detail); $i++) {
                $adon_data_arr = array();
                $supplier_arr = $this->fetch_supplier_data($addon_detail[$i]['supplierID']);
                if ($addon_detail[$i]['isChargeToExpense'] == 1) {
                    $data_arr['auto_id'] = $addon_detail[$i]['id'];
                    $data_arr['gl_auto_id'] = $addon_detail[$i]['GLAutoID'];
                    $data_arr['gl_code'] = $addon_detail[$i]['systemGLCode'];
                    $data_arr['secondary'] = $addon_detail[$i]['GLCode'];
                    $data_arr['gl_desc'] = $addon_detail[$i]['GLDescription'];
                    $data_arr['gl_type'] = $addon_detail[$i]['GLType'];
                    $data_arr['segment_id'] = $master['segmentID'];
                    $data_arr['projectID'] = null;
                    $data_arr['project_categoryID'] = null;
                    $data_arr['project_subCategoryID'] = null;
                    //$data_arr['projectID'] = $master['projectID'];
                    //$data_arr['projectExchangeRate'] = $master['projectExchangeRate'];
                    $data_arr['segment'] = $master['segmentCode'];
                    $data_arr['gl_dr'] = $addon_detail[$i]['total_amount'];
                    $data_arr['gl_cr'] = 0;
                    $data_arr['amount_type'] = 'dr';
                    $data_arr['isAddon'] = 0;
                    $data_arr['subLedgerType'] = 0;
                    $data_arr['subLedgerDesc'] = null;
                    $data_arr['partyContractID'] = null;
                    $data_arr['partyType'] = 'SUP';
                    $data_arr['partyAutoID'] = $addon_detail[$i]['supplierID'];
                    $data_arr['partySystemCode'] = $supplier_arr['supplierSystemCode'];
                    $data_arr['partyName'] = $supplier_arr['supplierName'];
                    $data_arr['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
                    $data_arr['transactionExchangeRate'] = null;
                    $data_arr['companyLocalExchangeRate'] = null;
                    $data_arr['companyReportingExchangeRate'] = null;
                    $data_arr['partyCurrency'] = $supplier_arr['supplierCurrency'];
                    $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $data_arr['partyCurrencyID']);
                    $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
                    $data_arr['partyCurrencyDecimalPlaces'] = $conversion_arr['DecimalPlaces'];
                    $data_arr['partyCurrencyAmount'] = ($addon_detail[$i]['total_amount'] / $conversion_arr['conversion']);
                    array_push($expanss_item, $data_arr);
                } elseif ($addon_detail[$i]['isChargeToExpense'] == 0 and $addon_detail[$i]['impactFor'] == 0) {
                    $addon_total += $addon_detail[$i]['total_amount'];
                } elseif ($addon_detail[$i]['isChargeToExpense'] == 0 and $addon_detail[$i]['impactFor'] != 0) {
                    $adon_data_arr['item_id'] = $addon_detail[$i]['impactFor'];
                    $adon_data_arr['total'] = $addon_detail[$i]['total_amount'];
                    array_push($addon_item, $adon_data_arr);
                }

                $data_arr['auto_id'] = 0;
                $data_arr['gl_auto_id'] = $UGRV_ID['GLAutoID'];
                $data_arr['gl_code'] = $UGRV['systemAccountCode'];
                $data_arr['secondary'] = $UGRV['GLSecondaryCode'];
                $data_arr['gl_desc'] = $UGRV['GLDescription'] . ' - ' . $addon_detail[$i]['supplierName'];
                $data_arr['gl_type'] = $UGRV['subCategory'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $addon_detail[$i]['total_amount'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['isAddon'] = 1;
                $data_arr['subLedgerType'] = 1;
                $data_arr['subLedgerDesc'] = 'UGRV';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'SUP';
                $data_arr['partyAutoID'] = $addon_detail[$i]['supplierID'];
                $data_arr['partySystemCode'] = $supplier_arr['supplierSystemCode'];
                $data_arr['partyName'] = $supplier_arr['supplierName'];
                $data_arr['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
                $data_arr['partyCurrency'] = $supplier_arr['supplierCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $data_arr['partyCurrencyID']);
                $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
                $data_arr['partyCurrencyDecimalPlaces'] = $conversion_arr['DecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = ($addon_detail[$i]['total_amount'] / $conversion_arr['conversion']);
                array_push($gl_array['gl_detail'], $data_arr);
            }
        }

        $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $master['supplierCurrencyID']); /*get currency conversion for seleted supplier's currency in grv header*/
        for ($i = 0; $i < count($detail); $i++) {
            $addon = ($detail[$i]['receivedTotalAmount'] / $grv_total) * $addon_total;
            $data_arr['auto_id'] = $detail[$i]['grvDetailsID'];
            if ($detail[$i]['financeCategory'] == 1 or $detail[$i]['financeCategory'] == 3) {
                $data_arr['gl_auto_id'] = $detail[$i]['BLGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['BLGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['BLDescription'];
                $data_arr['gl_type'] = $detail[$i]['BLType'];
            } else {
                $data_arr['gl_auto_id'] = $detail[$i]['PLGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['PLSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['PLGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['PLDescription'];
                $data_arr['gl_type'] = $detail[$i]['PLType'];
            }

            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['projectID'] = $detail[$i]['projectID'];
            $data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            $data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $data_arr['project_categoryID'] = isset($detail[$i]['project_categoryID']) ? $detail[$i]['project_categoryID'] : null;
            $data_arr['project_subCategoryID'] = isset($detail[$i]['project_subCategoryID']) ? $detail[$i]['project_subCategoryID'] : null;;
            $data_arr['gl_dr'] = ($detail[$i]['receivedTotalAmount'] + $addon);
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'SUP';
            $data_arr['partyAutoID'] = $master['supplierID'];
            $data_arr['partySystemCode'] = $master['supplierSystemCode'];
            $data_arr['partyName'] = $master['supplierName'];
            $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $master['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
            $data_arr['partyCurrencyAmount'] = (($detail[$i]['receivedTotalAmount'] + $addon) / $conversion_arr['conversion']);
            $data_arr['partyCurrencyDecimalPlaces'] = fetch_currency_desimal($data_arr['partyCurrency']);

            $cr_total += $detail[$i]['receivedTotalAmount'];
            for ($x = 0; $x < count($addon_item); $x++) {
                if ($addon_item[$x]['item_id'] == $detail[$i]['grvDetailsID']) {
                    $data_arr['gl_dr'] += $addon_item[$x]['total'];
                }
            }
            //array_push($gl_array['gl_detail'], $data_arr);
            array_push($m_arr, $data_arr);
        }
        //$m_arr = $this->array_group_sum_grv($m_arr);
        $m_arr = $this->array_group_sum_pm($m_arr);

        foreach ($m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        //$gl_array['gl_detail'] = $this->array_group_sum($gl_array['gl_detail']);
        foreach ($expanss_item as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $UGRV_ID['GLAutoID'];
        $data_arr['gl_code'] = $UGRV['systemAccountCode'];
        $data_arr['secondary'] = $UGRV['GLSecondaryCode'];
        $data_arr['gl_desc'] = $UGRV['GLDescription'] . ' - ' . $master['supplierName'];
        $data_arr['gl_type'] = $UGRV['subCategory'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];
        $data_arr['gl_dr'] = 0;
        $data_arr['gl_cr'] = $cr_total;
        $data_arr['amount_type'] = 'cr';
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 1;
        $data_arr['subLedgerDesc'] = 'UGRV';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = 'SUP';
        $data_arr['partyAutoID'] = $master['supplierID'];
        $data_arr['partySystemCode'] = $master['supplierSystemCode'];
        $data_arr['partyName'] = $master['supplierName'];
        $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
        $data_arr['partyCurrency'] = $master['supplierCurrency'];
        $data_arr['transactionExchangeRate'] = null;
        $data_arr['companyLocalExchangeRate'] = null;
        $data_arr['companyReportingExchangeRate'] = null;
        $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];;
        $data_arr['partyCurrencyAmount'] = ($cr_total / $conversion_arr['conversion']);
        $data_arr['partyCurrencyDecimalPlaces'] = fetch_currency_desimal($data_arr['partyCurrency']);
        array_push($gl_array['gl_detail'], $data_arr);
        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = $code;
        $gl_array['name'] = $code;
        $gl_array['primary_Code'] = $master['grvPrimaryCode'];
        $gl_array['date'] = $master['grvDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['suppliernamemaster'] = $master['suppliernamemaster'];
        return $gl_array;
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function fetch_double_entry_sa_data($stockAdjustmentAutoID, $code = null)
    {
        $gl_array = array();
        $cr_total = 0;
        $grv_total = 0;
        $addon_total = 0;
        $addon_item = array();
        $gl_array['gl_detail'] = array();
        $this->db->select('*');
        $this->db->where('stockAdjustmentAutoID', $stockAdjustmentAutoID);
        $master = $this->db->get('srp_erp_stockadjustmentmaster')->row_array();

        $this->db->select('stockAdjustmentDetailsAutoID,itemFinanceCategory,PLGLAutoID,PLSystemGLCode,PLGLCode,PLDescription,PLType,BLGLAutoID,BLSystemGLCode ,BLGLCode,BLDescription,BLType,totalValue,financeCategory,segmentCode,segmentID,projectID');
        $this->db->where('stockAdjustmentAutoID', $stockAdjustmentAutoID);
        $detail = $this->db->get('srp_erp_stockadjustmentdetails')->result_array();

        $cr_p_arr = array();
        $cr_m_arr = array();
        $dr_p_arr = array();
        $dr_m_arr = array();
        for ($i = 0; $i < count($detail); $i++) {
            if ($master["stockAdjustmentType"] == 'Inventory') {
                $assat_entry_arr['auto_id'] = $detail[$i]['stockAdjustmentDetailsAutoID'];
                $assat_entry_arr['gl_auto_id'] = $detail[$i]['BLGLAutoID'];
                $assat_entry_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
                $assat_entry_arr['secondary'] = $detail[$i]['BLGLCode'];
                $assat_entry_arr['gl_desc'] = $detail[$i]['BLDescription'];
                $assat_entry_arr['gl_type'] = $detail[$i]['BLType'];
                $assat_entry_arr['segment_id'] = $detail[$i]['segmentID'];
                $assat_entry_arr['segment'] = $detail[$i]['segmentCode'];
                $assat_entry_arr['projectID'] = $detail[$i]['projectID'];
                $assat_entry_arr['isAddon'] = 0;
                $assat_entry_arr['subLedgerType'] = 0;
                $assat_entry_arr['subLedgerDesc'] = null;
                $assat_entry_arr['partyContractID'] = null;
                $assat_entry_arr['partyType'] = null;
                $assat_entry_arr['partyAutoID'] = null;
                $assat_entry_arr['partySystemCode'] = null;
                $assat_entry_arr['partyName'] = null;
                $assat_entry_arr['partyCurrencyID'] = null;
                $assat_entry_arr['partyCurrency'] = null;
                $assat_entry_arr['transactionExchangeRate'] = null;
                $assat_entry_arr['companyLocalExchangeRate'] = null;
                $assat_entry_arr['companyReportingExchangeRate'] = null;
                $assat_entry_arr['partyExchangeRate'] = null;
                $assat_entry_arr['partyCurrencyAmount'] = null;
                $assat_entry_arr['partyCurrencyDecimalPlaces'] = null;
                $assat_entry_arr['amount_type'] = 'dr';
                if ($detail[$i]['totalValue'] >= 0) {
                    $assat_entry_arr['gl_dr'] = abs($detail[$i]['totalValue']);
                    $assat_entry_arr['gl_cr'] = 0;
                    array_push($dr_p_arr, $assat_entry_arr);
                } else {
                    $assat_entry_arr['gl_dr'] = 0;
                    $assat_entry_arr['gl_cr'] = abs($detail[$i]['totalValue']);
                    $assat_entry_arr['amount_type'] = 'cr';
                    array_push($cr_m_arr, $assat_entry_arr);
                }

                $cost_entry_arr['auto_id'] = $detail[$i]['stockAdjustmentDetailsAutoID'];
                $cost_entry_arr['gl_auto_id'] = $detail[$i]['PLGLAutoID'];
                $cost_entry_arr['gl_code'] = $detail[$i]['PLSystemGLCode'];
                $cost_entry_arr['secondary'] = $detail[$i]['PLGLCode'];
                $cost_entry_arr['gl_desc'] = $detail[$i]['PLDescription'];
                $cost_entry_arr['gl_type'] = $detail[$i]['PLType'];
                $cost_entry_arr['segment_id'] = $detail[$i]['segmentID'];
                $cost_entry_arr['segment'] = $detail[$i]['segmentCode'];
                $cost_entry_arr['isAddon'] = 0;
                $cost_entry_arr['subLedgerType'] = 0;
                $cost_entry_arr['subLedgerDesc'] = null;
                $cost_entry_arr['partyContractID'] = null;
                $cost_entry_arr['partyType'] = null;
                $cost_entry_arr['partyAutoID'] = null;
                $cost_entry_arr['partySystemCode'] = null;
                $cost_entry_arr['partyName'] = null;
                $cost_entry_arr['partyCurrencyID'] = null;
                $cost_entry_arr['partyCurrency'] = null;
                $cost_entry_arr['transactionExchangeRate'] = null;
                $cost_entry_arr['companyLocalExchangeRate'] = null;
                $cost_entry_arr['companyReportingExchangeRate'] = null;
                $cost_entry_arr['partyExchangeRate'] = null;
                $cost_entry_arr['partyCurrencyAmount'] = null;
                $cost_entry_arr['partyCurrencyDecimalPlaces'] = null;
                $cost_entry_arr['amount_type'] = 'cr';
                if ($detail[$i]['totalValue'] >= 0) {
                    $cost_entry_arr['gl_dr'] = 0;
                    $cost_entry_arr['gl_cr'] = abs($detail[$i]['totalValue']);
                    array_push($cr_p_arr, $cost_entry_arr);
                } else {
                    $cost_entry_arr['gl_dr'] = abs($detail[$i]['totalValue']);
                    $cost_entry_arr['gl_cr'] = 0;
                    $cost_entry_arr['amount_type'] = 'dr';
                    array_push($dr_m_arr, $cost_entry_arr);
                }
            }

            $cr_p_arr = $this->array_group_sum($cr_p_arr);
            $cr_m_arr = $this->array_group_sum($cr_m_arr);
            $dr_p_arr = $this->array_group_sum($dr_p_arr);
            $dr_m_arr = $this->array_group_sum($dr_m_arr);

            $gl_array['gl_detail'] = $cr_p_arr;
            foreach ($cr_m_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
            foreach ($dr_p_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
            foreach ($dr_m_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
        }

        $gl_array['currency'] = $master['companyLocalCurrency'];
        $gl_array['decimal_places'] = $master['companyLocalCurrencyDecimalPlaces'];
        $gl_array['code'] = 'SA';
        $gl_array['name'] = 'Stock Adjustment';
        $gl_array['primary_Code'] = $master['stockAdjustmentCode'];
        $gl_array['date'] = $master['stockAdjustmentDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;

        return $gl_array;
    }

    function fetch_double_entry_stock_return_data($stockReturnAutoID, $code = null)
    {
        $gl_array = array();
        $dr_total = 0;
        $gl_array['gl_detail'] = array();
        $this->db->select('srp_erp_stockreturnmaster.*,srp_erp_suppliermaster.supplierName as suppliernamemaster');
        $this->db->where('stockReturnAutoID', $stockReturnAutoID);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_stockreturnmaster.supplierID', 'left');
        $master = $this->db->get('srp_erp_stockreturnmaster')->row_array();

        $this->db->select('stockReturnAutoID,financeCategory,PLSystemGLCode,PLGLCode,PLDescription,BLGLAutoID, BLSystemGLCode,PLType ,BLGLCode,BLDescription,BLType,totalValue,segmentCode,segmentID,PLGLAutoID');
        $this->db->where('stockReturnAutoID', $stockReturnAutoID);
        $detail = $this->db->get('srp_erp_stockreturndetails')->result_array();
        for ($i = 0; $i < count($detail); $i++) {
            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = 0;
            if ($detail[$i]['financeCategory'] == 1 or $detail[$i]['financeCategory'] == 3) {
                $data_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['BLGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['BLDescription'];
                $data_arr['gl_type'] = $detail[$i]['BLType'];
                $data_arr['gl_auto_id'] = $detail[$i]['BLGLAutoID'];
            } else {
                $data_arr['gl_auto_id'] = $detail[$i]['PLGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['PLSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['PLGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['PLDescription'];
                $data_arr['gl_type'] = $detail[$i]['PLType'];
            }

            $data_arr['segment_id'] = $detail[$i]['segmentID'];
            $data_arr['segment'] = $detail[$i]['segmentCode'];
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = $detail[$i]['totalValue'];
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = null;
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = null;
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = null;
            $data_arr['partyCurrencyAmount'] = null;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'cr';

            $dr_total += $detail[$i]['totalValue'];
            array_push($gl_array['gl_detail'], $data_arr);
        }
        $gl_array['gl_detail'] = $this->array_group_sum($gl_array['gl_detail']);

        $item_tax_group_wise = $this->db->query("SELECT *  FROM(
            SELECT documentDetailAutoID as auto_id, taxGlAutoID as gl_auto_id, SUM(amount) as taxAmount, taxMasterID
                FROM `srp_erp_taxledger`
                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                where documentID = 'PR' AND documentMasterAutoID = $stockReturnAutoID GROUP BY taxMasterID) t1
            LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id")->result_array();

        for ($i = 0; $i < count($item_tax_group_wise); $i++) {
            if (!empty($item_tax_group_wise[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_tax_group_wise[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_tax_group_wise[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_tax_group_wise[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_tax_group_wise[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = null;
                $data_arr_tx_group['segment'] = null;
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = null;
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = '';
                $data_arr_tx_group['partySystemCode'] = '';
                $data_arr_tx_group['partyName'] = '';
                $data_arr_tx_group['partyCurrencyID'] = '';
                $data_arr_tx_group['partyCurrency'] = '';
                $data_arr_tx_group['transactionExchangeRate'] = null;
                $data_arr_tx_group['companyLocalExchangeRate'] = null;
                $data_arr_tx_group['companyReportingExchangeRate'] = null;
                $data_arr_tx_group['partyExchangeRate'] = '';
                $data_arr_tx_group['partyCurrencyAmount'] = '';
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = '';
                $data_arr_tx_group['amount_type'] = 'cr';
                $data_arr_tx_group['gl_dr'] = 0;
                $data_arr_tx_group['gl_cr'] = $item_tax_group_wise[$i]['taxAmount'];
                array_push($gl_array['gl_detail'], $data_arr_tx_group);

                $dr_total += $item_tax_group_wise[$i]['taxAmount'];

            }
        }


        $companyID = current_companyID();
        $UGRV_ID = $this->db->query("SELECT srp_erp_chartofaccounts.GLAutoID 
                    FROM srp_erp_chartofaccounts
                    JOIN srp_erp_companycontrolaccounts ON srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
                    WHERE controllAccountYN = 1 AND srp_erp_companycontrolaccounts.companyID = {$companyID} AND srp_erp_chartofaccounts.companyID = {$companyID} AND controlAccountType = 'UGRV'")->row_array();
        $UGRV = fetch_gl_account_desc($UGRV_ID['GLAutoID']);

        //    $UGRV_ID = $this->common_data['controlaccounts']['UGRV'];
        //    $UGRV = fetch_gl_account_desc($this->common_data['controlaccounts']['UGRV']);

        /*$data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $UGRV_ID;
        $data_arr['gl_code'] = $UGRV['systemAccountCode'];
        $data_arr['secondary'] = $UGRV['GLSecondaryCode'];
        $data_arr['gl_desc'] = $UGRV['GLDescription'];
        $data_arr['gl_type'] = $UGRV['subCategory'];
        $data_arr['segment_id'] = '-';
        $data_arr['segment'] = '-';
        $data_arr['gl_dr'] = $dr_total;
        $data_arr['gl_cr'] = 0;
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 1;
        $data_arr['subLedgerDesc'] = 'UGRV';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = null;
        $data_arr['partyAutoID'] = null;
        $data_arr['partySystemCode'] = null;
        $data_arr['partyName'] = null;
        $data_arr['partyCurrencyID'] = null;
        $data_arr['partyCurrency'] = null;
        $data_arr['transactionExchangeRate'] = null;
        $data_arr['companyLocalExchangeRate'] = null;
        $data_arr['companyReportingExchangeRate'] = null;
        $data_arr['partyExchangeRate'] = null;
        $data_arr['partyCurrencyAmount'] = null;
        $data_arr['partyCurrencyDecimalPlaces'] = null;
        $data_arr['amount_type'] = 'dr';
        array_push($gl_array['gl_detail'], $data_arr);*/

        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $master['supplierliabilityAutoID'];
        $data_arr['gl_code'] = $master['supplierliabilitySystemGLCode'];
        $data_arr['secondary'] = $master['supplierliabilityGLAccount'];
        $data_arr['gl_desc'] = $master['supplierliabilityDescription'];
        $data_arr['gl_type'] = $master['supplierliabilityType'];
        $data_arr['segment_id'] = '-';
        $data_arr['segment'] = '-';
        $data_arr['gl_dr'] = $dr_total;
        $data_arr['gl_cr'] = 0;
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 2;
        $data_arr['subLedgerDesc'] = 'AP';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = null;
        $data_arr['partyAutoID'] = null;
        $data_arr['partySystemCode'] = null;
        $data_arr['partyName'] = null;
        $data_arr['partyCurrencyID'] = null;
        $data_arr['partyCurrency'] = null;
        $data_arr['transactionExchangeRate'] = null;
        $data_arr['companyLocalExchangeRate'] = null;
        $data_arr['companyReportingExchangeRate'] = null;
        $data_arr['partyExchangeRate'] = null;
        $data_arr['partyCurrencyAmount'] = null;
        $data_arr['partyCurrencyDecimalPlaces'] = null;
        $data_arr['amount_type'] = 'dr';
        array_push($gl_array['gl_detail'], $data_arr);

        /*echo '<pre>';
        print_r($gl_array['gl_detail'] );
        echo '</pre>';
        exit;*/


        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'SR';
        $gl_array['name'] = 'Purchase Return';
        $gl_array['primary_Code'] = $master['stockReturnCode'];
        $gl_array['date'] = $master['returnDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['suppliernamemaster'] = $master['suppliernamemaster'];
        /*echo '<pre>';
        print_r($gl_array['gl_detail']);
        echo '</pre>';
        exit;*/
        return $gl_array;
    }

    function fetch_double_entry_material_issue_data($itemIssueAutoID, $code = null)
    {
        $gl_array = array();
        $cost_arr = array();
        $assat_arr = array();
        $gl_array['gl_detail'] = array();
        $this->db->select('*');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $master = $this->db->get('srp_erp_itemissuemaster')->row_array();

        $this->db->select('financeCategory,PLGLAutoID,PLSystemGLCode,PLGLCode,PLDescription,BLGLAutoID,BLSystemGLCode ,PLType ,BLGLCode,BLDescription,BLType,totalValue,segmentCode,segmentID,projectID,projectExchangeRate,project_categoryID,project_subCategoryID');
        $this->db->where('itemCategory !=', 'Non Inventory');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $detail = $this->db->get('srp_erp_itemissuedetails')->result_array();
        for ($i = 0; $i < count($detail); $i++) {
            $assa_data_arr['auto_id'] = 0;
            $assa_data_arr['gl_auto_id'] = $detail[$i]['BLGLAutoID'];
            $assa_data_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
            $assa_data_arr['secondary'] = $detail[$i]['BLGLCode'];
            $assa_data_arr['gl_desc'] = $detail[$i]['BLDescription'];
            $assa_data_arr['gl_type'] = $detail[$i]['BLType'];
            $assa_data_arr['segment_id'] = $detail[$i]['segmentID'];
            $assa_data_arr['segment'] = $detail[$i]['segmentCode'];
            $assa_data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            $assa_data_arr['project_categoryID'] = isset($detail[$i]['project_categoryID']) ? $detail[$i]['project_categoryID'] : null;
            $assa_data_arr['project_subCategoryID'] = isset($detail[$i]['project_subCategoryID']) ? $detail[$i]['project_subCategoryID'] : null;
            $assa_data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $assa_data_arr['gl_dr'] = 0;
            $assa_data_arr['gl_cr'] = $detail[$i]['totalValue'];
            $assa_data_arr['amount_type'] = 'cr';
            $assa_data_arr['isAddon'] = 0;
            $assa_data_arr['subLedgerType'] = 0;
            $assa_data_arr['subLedgerDesc'] = null;
            $assa_data_arr['partyContractID'] = null;
            $assa_data_arr['partyType'] = null;
            $assa_data_arr['partyAutoID'] = null;
            $assa_data_arr['partySystemCode'] = null;
            $assa_data_arr['partyName'] = null;
            $assa_data_arr['partyCurrencyID'] = null;
            $assa_data_arr['partyCurrency'] = null;
            $assa_data_arr['transactionExchangeRate'] = null;
            $assa_data_arr['companyLocalExchangeRate'] = null;
            $assa_data_arr['companyReportingExchangeRate'] = null;
            $assa_data_arr['partyExchangeRate'] = null;
            $assa_data_arr['partyCurrencyAmount'] = null;
            $assa_data_arr['partyCurrencyDecimalPlaces'] = null;
            array_push($assat_arr, $assa_data_arr);

            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $detail[$i]['PLGLAutoID'];
            $data_arr['gl_code'] = $detail[$i]['PLSystemGLCode'];
            $data_arr['secondary'] = $detail[$i]['PLGLCode'];
            $data_arr['gl_desc'] = $detail[$i]['PLDescription'];
            $data_arr['gl_type'] = $detail[$i]['PLType'];
            $data_arr['segment_id'] = $detail[$i]['segmentID'];
            $data_arr['segment'] = $detail[$i]['segmentCode'];
            $data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            $data_arr['project_categoryID'] = isset($detail[$i]['project_categoryID']) ? $detail[$i]['project_categoryID'] : null;
            $data_arr['project_subCategoryID'] = isset($detail[$i]['project_subCategoryID']) ? $detail[$i]['project_subCategoryID'] : null;
            $data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $data_arr['gl_dr'] = $detail[$i]['totalValue'];
            $data_arr['gl_cr'] = 0;
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = null;
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = null;
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = null;
            $data_arr['partyCurrencyAmount'] = null;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'dr';
            array_push($cost_arr, $data_arr);
        }

        $assat_arr = $this->array_group_sum_pm($assat_arr);
        $cost_arr = $this->array_group_sum_pm($cost_arr);

        $gl_array['gl_detail'] = $assat_arr;
        foreach ($cost_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        $gl_array['currency'] = $master['companyLocalCurrency'];
        $gl_array['decimal_places'] = $master['companyLocalCurrencyDecimalPlaces'];
        $gl_array['code'] = 'MI';
        $gl_array['name'] = 'Material Issue';
        $gl_array['primary_Code'] = $master['itemIssueCode'];
        $gl_array['date'] = $master['issueDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }

    function fetch_double_entry_stock_transfer_data($stockTransferAutoID, $code = null)
    {
        $gl_array = array();
        $cost_arr = array();
        $assat_arr = array();
        $gl_array['gl_detail'] = array();
        $this->db->select('*');
        $this->db->where('stockTransferAutoID', $stockTransferAutoID);
        $master = $this->db->get('srp_erp_stocktransfermaster')->row_array();

        $this->db->select('stockTransferDetailsID,financeCategory,PLSystemGLCode,PLGLCode,PLDescription,BLGLAutoID,BLSystemGLCode ,PLType ,BLGLCode,BLDescription,BLType,totalValue,segmentCode,segmentID,projectID,fromWarehouseType,toWarehouseType,fromWarehouseWIPGLAutoID,toWarehouseWIPGLAutoID,project_categoryID,project_subCategoryID');
        $this->db->where('itemCategory !=', 'Non Inventory');
        $this->db->where('stockTransferAutoID', $stockTransferAutoID);
        $detail = $this->db->get('srp_erp_stocktransferdetails')->result_array();

        for ($i = 0; $i < count($detail); $i++) {
            $assa_data_arr['auto_id'] = $detail[$i]['stockTransferDetailsID'];
            if ($detail[$i]['fromWarehouseType'] == 2) {
                $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory');
                $this->db->where('GLAutoID', $detail[$i]['fromWarehouseWIPGLAutoID']);
                $frmGlDetail = $this->db->get('srp_erp_chartofaccounts')->row_array();

                $assa_data_arr['gl_auto_id'] = $detail[$i]['fromWarehouseWIPGLAutoID'];
                $assa_data_arr['gl_code'] = $frmGlDetail['systemAccountCode'];
                $assa_data_arr['secondary'] = $frmGlDetail['GLSecondaryCode'];
                $assa_data_arr['gl_desc'] = $frmGlDetail['GLDescription'];
                $assa_data_arr['gl_type'] = $frmGlDetail['subCategory'];
            } else {
                $assa_data_arr['gl_auto_id'] = $detail[$i]['BLGLAutoID'];
                $assa_data_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
                $assa_data_arr['secondary'] = $detail[$i]['BLGLCode'];
                $assa_data_arr['gl_desc'] = $detail[$i]['BLDescription'];
                $assa_data_arr['gl_type'] = $detail[$i]['BLType'];
            }

            $assa_data_arr['segment_id'] = $detail[$i]['segmentID'];
            $assa_data_arr['segment'] = $detail[$i]['segmentCode'];
            $assa_data_arr['projectID'] = $detail[$i]['projectID'];
            $assa_data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
            $assa_data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
            $assa_data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $assa_data_arr['gl_dr'] = 0;
            $assa_data_arr['gl_cr'] = $detail[$i]['totalValue'];
            $assa_data_arr['amount_type'] = 'cr';
            $assa_data_arr['isAddon'] = 0;
            $assa_data_arr['subLedgerType'] = 0;
            $assa_data_arr['subLedgerDesc'] = null;
            $assa_data_arr['partyContractID'] = null;
            $assa_data_arr['partyType'] = null;
            $assa_data_arr['partyAutoID'] = null;
            $assa_data_arr['partySystemCode'] = null;
            $assa_data_arr['partyName'] = null;
            $assa_data_arr['partyCurrencyID'] = null;
            $assa_data_arr['partyCurrency'] = null;
            $assa_data_arr['transactionExchangeRate'] = null;
            $assa_data_arr['companyLocalExchangeRate'] = null;
            $assa_data_arr['companyReportingExchangeRate'] = null;
            $assa_data_arr['partyExchangeRate'] = null;
            $assa_data_arr['partyCurrencyAmount'] = null;
            $assa_data_arr['partyCurrencyDecimalPlaces'] = null;
            array_push($assat_arr, $assa_data_arr);

            $data_arr['auto_id'] = $detail[$i]['stockTransferDetailsID'];
            if ($detail[$i]['toWarehouseType'] == 2) {
                $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory');
                $this->db->where('GLAutoID', $detail[$i]['toWarehouseWIPGLAutoID']);
                $toGlDetail = $this->db->get('srp_erp_chartofaccounts')->row_array();

                $data_arr['gl_auto_id'] = $detail[$i]['toWarehouseWIPGLAutoID'];
                $data_arr['gl_code'] = $toGlDetail['systemAccountCode'];
                $data_arr['secondary'] = $toGlDetail['GLSecondaryCode'];
                $data_arr['gl_desc'] = $toGlDetail['GLDescription'];
                $data_arr['gl_type'] = $toGlDetail['subCategory'];
            } else {
                $data_arr['gl_auto_id'] = $detail[$i]['BLGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['BLGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['BLDescription'];
                $data_arr['gl_type'] = $detail[$i]['BLType'];
            }
            $data_arr['segment_id'] = $detail[$i]['segmentID'];
            $data_arr['segment'] = $detail[$i]['segmentCode'];
            $data_arr['gl_dr'] = $detail[$i]['totalValue'];
            $data_arr['projectID'] = null;
            $data_arr['project_categoryID'] = null;
            $data_arr['project_subCategoryID'] = null;
            $data_arr['projectExchangeRate'] = null;
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = null;
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = null;
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = null;
            $data_arr['partyCurrencyAmount'] = null;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            array_push($cost_arr, $data_arr);
        }

        $assat_arr = $this->array_group_sum_pm($assat_arr);
        $cost_arr = $this->array_group_sum_pm($cost_arr);

        $gl_array['gl_detail'] = $assat_arr;
        foreach ($cost_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        $gl_array['currency'] = $master['companyLocalCurrency'];
        $gl_array['decimal_places'] = $master['companyLocalCurrencyDecimalPlaces'];
        $gl_array['code'] = 'ST';
        $gl_array['name'] = 'Stock Transfer';
        $gl_array['primary_Code'] = $master['stockTransferCode'];
        $gl_array['date'] = $master['tranferDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }

    function fetch_double_entry_supplier_invoices_data($InvoiceAutoID, $code = null)
    {
        $gl_array = array();
        $gl_array['gl_detail'] = array();
        $cr_total = 0;
        $dr_total = 0;
        $this->db->select('srp_erp_paysupplierinvoicemaster.*,srp_erp_suppliermaster.vatIdNo as vatIdNo,srp_erp_suppliermaster.supplierName as suppliernamemaster');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID');
        $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $this->db->select('*,srp_erp_paysupplierinvoicedetail.systemGLCode as sglcd, IFNULL(taxAmount, 0) as taxAmount');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $detail = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

        $this->db->select('SUM(retensionValue) as retensionValue');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $totalRetensionValue = $this->db->get('srp_erp_paysupplierinvoicedetail')->row('retensionValue');

        $this->db->select('sum(transactionAmount + taxAmount) as transactionAmount');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $totalsum = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();

        $disciunt = ($master['generalDiscountPercentage'] / 100) * $totalsum['transactionAmount'];

        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate,supplierCurrencyDecimalPlaces,supplierCurrencyID,taxMasterAutoID');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $tax_detail = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();

        $isGropBasedTaxGL = $this->db->query("SELECT *  FROM (  SELECT
                                                                documentDetailAutoID AS auto_id,
                                                                taxGlAutoID AS gl_auto_id,
                                                                SUM( amount ) AS taxAmount,
                                                                taxMasterID,
                                                                rcmApplicableYN,
                                                                taxCategory,
                                                                srp_erp_taxledger.companyID
                                                                FROM
                                                                `srp_erp_taxledger`
                                                                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                                LEFT JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_taxledger.companyID
                                                                WHERE
                                                                documentID = 'BSI' 
                                                                AND documentMasterAutoID = $InvoiceAutoID
                                                                GROUP BY
                                                                taxGlAutoID,
                                                                taxMasterID 
                                                                ) t1
                                                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id
                                                GROUP BY
	                                            taxMasterID
                                                ")->result_array();


        $isGropBasedtransferGLAutoID = $this->db->query("SELECT * FROM (  SELECT
                                                                          documentDetailAutoID AS auto_id,
                                                                          transferGLAutoID AS gl_auto_id,
                                                                          SUM( amount ) AS taxAmount,
                                                                          taxMasterID,
                                                                           rcmApplicableYN
                                                                          FROM
                                                                          `srp_erp_taxledger`
                                                                          LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                                          WHERE
                                                                          documentID = 'BSI' 
                                                                          AND documentMasterAutoID = $InvoiceAutoID 
                                                                          AND srp_erp_taxledger.isClaimable = 1
                                                                          AND transferGLAutoID IS NOT NULL 
                                                                          GROUP BY
                                                                           taxGlAutoID,
                                                                          taxMasterID ) t1
                                                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id
                                                GROUP BY
	                                            taxMasterID
                                                ")->result_array();


        $outputVatTransfer = $this->db->query("SELECT *  FROM (  SELECT
                                                                documentDetailAutoID AS auto_id,
                                                                outputVatTransferGL AS gl_auto_id,
                                                                SUM( amount ) AS taxAmount,
                                                                taxMasterID,
                                                                rcmApplicableYN
                                                                FROM
                                                                `srp_erp_taxledger`
                                                                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                                WHERE
                                                                documentID = 'BSI' 
                                                                AND documentMasterAutoID = $InvoiceAutoID
                                                                AND taxCategory = 2 
                                                                GROUP BY
                                                               	taxGlAutoID,
		                                                        taxMasterID 
                                                                ) t1
                                                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id
                                                GROUP BY
	                                            taxMasterID
                                                ")->result_array();


        $outputVatAC = $this->db->query("SELECT *  FROM (  SELECT
                                                                documentDetailAutoID AS auto_id,
                                                                outputVatGL AS gl_auto_id,
                                                                SUM( amount ) AS taxAmount,
                                                                taxMasterID,
                                                                rcmApplicableYN
                                                                FROM
                                                                `srp_erp_taxledger`
                                                                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                                WHERE
                                                                documentID = 'BSI' 
                                                                AND documentMasterAutoID = $InvoiceAutoID
                                                                AND taxCategory = 2 
                                                                GROUP BY
                                                               	taxGlAutoID,
		                                                        taxMasterID 
                                                                ) t1
                                                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id
                                                 GROUP BY
	                                            taxMasterID
                                                ")->result_array();


        $vatAmount = array_filter($isGropBasedTaxGL, function ($a) {
            return $a['taxCategory'] == 2;
        });

        $vatAmountRCM = (array_column($vatAmount, 'taxAmount'));


        $isRCMApplicableoutputVatAC = array_column($outputVatAC, 'rcmApplicableYN');
        $isRCMApplicableoutputVatTransfer = array_column($outputVatTransfer, 'rcmApplicableYN');

        if ($master['invoiceType'] == 'GRV Base') {
            $taxttlitm = 0;
            $companyID = current_companyID();
            $UGRV_ID = $this->db->query("SELECT srp_erp_chartofaccounts.GLAutoID 
                    FROM srp_erp_chartofaccounts
                    JOIN srp_erp_companycontrolaccounts ON srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
                    WHERE controllAccountYN = 1 AND srp_erp_companycontrolaccounts.companyID = {$companyID} AND srp_erp_chartofaccounts.companyID = {$companyID} AND controlAccountType = 'UGRV'")->row_array();
            $UGRV = fetch_gl_account_desc($UGRV_ID['GLAutoID']);
            //            $UGRV_ID = $this->common_data['controlaccounts']['UGRV'];
            //            $UGRV = fetch_gl_account_desc($UGRV_ID);


            for ($i = 0; $i < count($detail); $i++) {
                $dr_total += ($detail[$i]['transactionAmount']);
                $cr_total += ($detail[$i]['transactionAmount'] + $detail[$i]['taxAmount']);
            }
            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $UGRV_ID['GLAutoID'];
            $data_arr['gl_code'] = $UGRV['systemAccountCode'];
            $data_arr['secondary'] = $UGRV['GLSecondaryCode'];
            $data_arr['gl_desc'] = $UGRV['GLDescription'];
            $data_arr['gl_type'] = $UGRV['subCategory'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = $dr_total;
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = 1;
            $data_arr['subLedgerDesc'] = 'UGRV';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'SUP';
            $data_arr['partyAutoID'] = $master['supplierID'];
            $data_arr['partySystemCode'] = $master['supplierCode'];
            $data_arr['partyName'] = $master['supplierName'];
            $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $master['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
            $data_arr['partyCurrencyAmount'] = 0;

            array_push($gl_array['gl_detail'], $data_arr);

        } else {
            $cr_p_arr = array();
            $cr_m_arr = array();
            $p_arr = array();
            $taxttlitm = 0;
            // echo '<pre>';print_r($detail); echo '</pre>'; die();
            for ($i = 0; $i < count($detail); $i++) {
                if ($detail[$i]['type'] == 'Item') {
                    $data_arr['auto_id'] = $detail[$i]['InvoiceDetailAutoID'];
                    $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                    $data_arr['gl_code'] = $detail[$i]['sglcd'];
                    $data_arr['secondary'] = $detail[$i]['GLCode'];
                    $data_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Item';
                    $data_arr['gl_type'] = $detail[$i]['GLType'];
                    $data_arr['segment_id'] = $detail[$i]['segmentID'];
                    $data_arr['segment'] = $detail[$i]['segmentCode'];
                    $data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
                    $data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
                    $data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
                    $data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
                    $data_arr['isAddon'] = 0;
                    $data_arr['taxMasterAutoID'] = null;
                    $data_arr['partyVatIdNo'] = null;
                    $data_arr['subLedgerType'] = 0;
                    $data_arr['subLedgerDesc'] = null;
                    $data_arr['partyContractID'] = null;
                    $data_arr['partyType'] = 'SUP';
                    $data_arr['partyAutoID'] = $master['supplierID'];
                    $data_arr['partySystemCode'] = $master['supplierCode'];
                    $data_arr['partyName'] = $master['supplierName'];
                    $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
                    $data_arr['partyCurrency'] = $master['supplierCurrency'];
                    $data_arr['transactionExchangeRate'] = 1;
                    $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                    $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                    $data_arr['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                    $data_arr['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
                    $data_arr['partyCurrencyAmount'] = 0;
                    $data_arr['amount_type'] = 'dr';
                    if ($disciunt > 0) {
                        $amount = $detail[$i]['transactionAmount'] - (($detail[$i]['transactionAmount'] + $detail[$i]['taxAmount']) / $totalsum['transactionAmount']) * $disciunt;
                    } else {

                        $amount = $detail[$i]['transactionAmount'];
                    }
                    if ($amount >= 0) {

                        $data_arr['gl_dr'] = $amount;
                        $data_arr['gl_cr'] = 0;
                        //array_push($cr_p_arr, $data_arr);
                    } else {
                        $data_arr['gl_dr'] = 0;
                        $data_arr['gl_cr'] = $amount;
                        $data_arr['amount_type'] = 'cr';
                        //array_push($cr_m_arr, $data_arr);
                    }

                    $taxttlitm += $amount;
                    //$cr_total += $detail[$i]['transactionAmount'];
                    array_push($p_arr, $data_arr);

                } else {
                    $data_arr['auto_id'] = $detail[$i]['InvoiceDetailAutoID'];
                    $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                    $data_arr['gl_code'] = $detail[$i]['sglcd'];
                    $data_arr['secondary'] = $detail[$i]['GLCode'];
                    $data_arr['gl_desc'] = $detail[$i]['GLDescription'];
                    $data_arr['gl_type'] = $detail[$i]['GLType'];
                    $data_arr['segment_id'] = $detail[$i]['segmentID'];
                    $data_arr['segment'] = $detail[$i]['segmentCode'];
                    $data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
                    $data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
                    $data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
                    $data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
                    $data_arr['isAddon'] = 0;
                    $data_arr['taxMasterAutoID'] = null;
                    $data_arr['partyVatIdNo'] = null;
                    $data_arr['subLedgerType'] = 0;
                    $data_arr['subLedgerDesc'] = null;
                    $data_arr['partyContractID'] = null;
                    $data_arr['partyType'] = 'SUP';
                    $data_arr['partyAutoID'] = $master['supplierID'];
                    $data_arr['partySystemCode'] = $master['supplierCode'];
                    $data_arr['partyName'] = $master['supplierName'];
                    $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
                    $data_arr['partyCurrency'] = $master['supplierCurrency'];
                    $data_arr['transactionExchangeRate'] = null;
                    $data_arr['companyLocalExchangeRate'] = null;
                    $data_arr['companyReportingExchangeRate'] = null;
                    $data_arr['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                    $data_arr['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
                    $data_arr['partyCurrencyAmount'] = 0;
                    $data_arr['amount_type'] = 'dr';
                    if ($disciunt > 0) {
                        $amount = $detail[$i]['transactionAmount'] - (($detail[$i]['transactionAmount'] + $detail[$i]['taxAmount']) / $totalsum['transactionAmount']) * $disciunt;
                    } else {
                        $amount = $detail[$i]['transactionAmount'];
                    }
                    if ($amount >= 0) {

                        $data_arr['gl_dr'] = $amount;
                        $data_arr['gl_cr'] = 0;
                        array_push($cr_p_arr, $data_arr);
                    } else {
                        $data_arr['gl_dr'] = 0;
                        $data_arr['gl_cr'] = $amount;

                        $data_arr['amount_type'] = 'cr';
                        array_push($cr_m_arr, $data_arr);
                    }

                    $cr_total += $amount;
                    // if ($isRCMApplicableoutputVatAC[0] == 1 && $master['invoiceType'] == 'StandardPO') {

                    //     $cr_total += $amount - $vatAmountRCM[0];
                    // } else {

                    //     $cr_total += $amount ;
                    // }
                }
            }

            $isRCMApplicableoutputVatAC = isset($isRCMApplicableoutputVatAC[0]) ? $isRCMApplicableoutputVatAC[0] : 0;

            if ($isRCMApplicableoutputVatAC == 1 && $master['invoiceType'] == 'StandardPO') {
                $cr_total = $cr_total - $vatAmountRCM[0];
            }

            $p_arr = $this->array_group_sum_tax_pm($p_arr);
            $gl_array['gl_detail'] = $p_arr;
            foreach ($cr_m_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
            foreach ($cr_p_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
        }


        for ($i = 0; $i < count($isGropBasedTaxGL); $i++) {
            if (!empty($isGropBasedTaxGL[$i]['taxMasterID'])) {

                $data_arr_tx_group['auto_id'] = $isGropBasedTaxGL[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $isGropBasedTaxGL[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $isGropBasedTaxGL[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $isGropBasedTaxGL[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $isGropBasedTaxGL[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $isGropBasedTaxGL[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = $master['segmentID'];
                $data_arr_tx_group['segment'] = $master['segmentCode'];
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $isGropBasedTaxGL[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'SUP';
                $data_arr_tx_group['partyAutoID'] = $master['supplierID'];
                $data_arr_tx_group['partySystemCode'] = $master['supplierCode'];
                $data_arr_tx_group['partyName'] = $master['supplierName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['supplierCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['supplierCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = null;
                $data_arr_tx_group['companyLocalExchangeRate'] = null;
                $data_arr_tx_group['companyReportingExchangeRate'] = null;
                $data_arr_tx_group['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = '';
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'dr';
                $data_arr_tx_group['gl_dr'] = $isGropBasedTaxGL[$i]['taxAmount'];
                $data_arr_tx_group['gl_cr'] = 0;
                array_push($gl_array['gl_detail'], $data_arr_tx_group);
                if ($master['invoiceType'] != 'GRV Base') {
                    $cr_total += $data_arr_tx_group['gl_dr'];
                }


            }
        }


        $inputvatAmount = array_filter($isGropBasedTaxGL, function ($a) {
            return $a['taxCategory'] == 2;
        });

        $inputvatAmountRCM = (array_column($inputvatAmount, 'taxAmount'));

        if(!empty( $isRCMApplicableoutputVatAC[0]) && $isRCMApplicableoutputVatAC[0] == 1 && ($master['invoiceType'] == 'StandardItem' || $master['invoiceType'] == 'StandardExpense' || $master['invoiceType'] == 'StandardPO')){

            $cr_total =  $cr_total - $inputvatAmountRCM[0];

        }

        for ($i = 0; $i < count($isGropBasedtransferGLAutoID); $i++) {
            if (!empty($isGropBasedtransferGLAutoID[$i]['taxMasterID'])) {

                $data_arr_tx_group['auto_id'] = $isGropBasedtransferGLAutoID[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $isGropBasedtransferGLAutoID[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $isGropBasedtransferGLAutoID[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $isGropBasedtransferGLAutoID[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $isGropBasedtransferGLAutoID[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $isGropBasedtransferGLAutoID[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = $master['segmentID'];
                $data_arr_tx_group['segment'] = $master['segmentCode'];
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $isGropBasedtransferGLAutoID[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'SUP';
                $data_arr_tx_group['partyAutoID'] = $master['supplierID'];
                $data_arr_tx_group['partySystemCode'] = $master['supplierCode'];
                $data_arr_tx_group['partyName'] = $master['supplierName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['supplierCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['supplierCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = null;
                $data_arr_tx_group['companyLocalExchangeRate'] = null;
                $data_arr_tx_group['companyReportingExchangeRate'] = null;
                $data_arr_tx_group['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = '';
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'cr';
                $data_arr_tx_group['gl_cr'] = $isGropBasedtransferGLAutoID[$i]['taxAmount'];
                $data_arr_tx_group['gl_dr'] = 0;
                array_push($gl_array['gl_detail'], $data_arr_tx_group);
                if ($master['invoiceType'] != 'GRV Base' ) {
                    $cr_total += $data_arr_tx_group['gl_cr'];
                }
            }
        }

        $tax_total = ($cr_total) + $taxttlitm;

        for ($i = 0; $i < count($tax_detail); $i++) {
            $data_arr['auto_id'] = $tax_detail[$i]['taxDetailAutoID'];
            $data_arr['gl_auto_id'] = $tax_detail[$i]['GLAutoID'];
            $data_arr['gl_code'] = $tax_detail[$i]['SystemGLCode'];
            $data_arr['secondary'] = $tax_detail[$i]['GLCode'];
            $data_arr['gl_desc'] = $tax_detail[$i]['GLDescription'] . ' - Tax';
            $data_arr['gl_type'] = $tax_detail[$i]['GLType'];
            $data_arr['segment_id'] = $tax_detail[$i]['segmentID'];
            $data_arr['segment'] = $tax_detail[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['projectID'] = null;
            $data_arr['project_categoryID'] = null;
            $data_arr['project_subCategoryID'] = null;
            $data_arr['projectExchangeRate'] = null;
            $data_arr['taxMasterAutoID'] = $tax_detail[$i]['taxMasterAutoID'];
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'AUT';
            $data_arr['partyAutoID'] = $master['supplierID'];
            $data_arr['partySystemCode'] = $master['supplierCode'];
            $data_arr['partyName'] = $master['supplierName'];
            $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $master['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $tax_detail[$i]['supplierCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $tax_detail[$i]['supplierCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'dr';
            $data_arr['gl_dr'] = (($tax_detail[$i]['taxPercentage'] / 100) * $tax_total);
            $data_arr['gl_cr'] = 0;
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total += $data_arr['gl_dr'];
        }


        if (!empty($outputVatTransfer) && $isRCMApplicableoutputVatTransfer[0] == 1 && ($master['invoiceType'] != 'StandardPO' && $master['invoiceType'] != 'StandardItem' && $master['invoiceType'] != 'StandardExpense')) {
            for ($i = 0; $i < count($outputVatTransfer); $i++) {

                $data_arr_tx_group['auto_id'] = $outputVatTransfer[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $outputVatTransfer[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $outputVatTransfer[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $outputVatTransfer[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $outputVatTransfer[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $outputVatTransfer[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = $master['segmentID'];
                $data_arr_tx_group['segment'] = $master['segmentCode'];
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $outputVatTransfer[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'SUP';
                $data_arr_tx_group['partyAutoID'] = $master['supplierID'];
                $data_arr_tx_group['partySystemCode'] = $master['supplierCode'];
                $data_arr_tx_group['partyName'] = $master['supplierName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['supplierCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['supplierCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = null;
                $data_arr_tx_group['companyLocalExchangeRate'] = null;
                $data_arr_tx_group['companyReportingExchangeRate'] = null;
                $data_arr_tx_group['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = '';
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'dr';
                $data_arr_tx_group['gl_cr'] = 0;
                $data_arr_tx_group['gl_dr'] = $outputVatTransfer[$i]['taxAmount'];
                array_push($gl_array['gl_detail'], $data_arr_tx_group);
                if ($master['invoiceType'] != 'GRV Base') {
                    $cr_total += $data_arr_tx_group['gl_cr'];
                }

            }
        }

        if (!empty($outputVatAC) && $isRCMApplicableoutputVatAC[0] == 1) {
            for ($i = 0; $i < count($outputVatAC); $i++) {

                $data_arr_tx_group['auto_id'] = $outputVatAC[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $outputVatAC[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $outputVatAC[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $outputVatAC[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $outputVatAC[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $outputVatAC[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = $master['segmentID'];
                $data_arr_tx_group['segment'] = $master['segmentCode'];
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $outputVatAC[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'SUP';
                $data_arr_tx_group['partyAutoID'] = $master['supplierID'];
                $data_arr_tx_group['partySystemCode'] = $master['supplierCode'];
                $data_arr_tx_group['partyName'] = $master['supplierName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['supplierCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['supplierCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = null;
                $data_arr_tx_group['companyLocalExchangeRate'] = null;
                $data_arr_tx_group['companyReportingExchangeRate'] = null;
                $data_arr_tx_group['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = '';
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'cr';
                $data_arr_tx_group['gl_cr'] = $outputVatAC[$i]['taxAmount'];
                $data_arr_tx_group['gl_dr'] = 0;
                array_push($gl_array['gl_detail'], $data_arr_tx_group);
                if ($master['invoiceType'] != 'GRV Base' && $master['invoiceType'] != 'StandardItem' && $master['invoiceType'] != 'StandardExpense') {
                    $cr_total += $data_arr_tx_group['gl_cr'];
                }
            }
        }

        if($totalRetensionValue > 0){

            $retensionGL = $master['retensionGL'];

            $GL_retension_record = $this->db->where('GLAutoID',$retensionGL)->from('srp_erp_chartofaccounts')->get()->row_array();

            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $GL_retension_record['GLAutoID'];
            $data_arr['gl_code'] = $GL_retension_record['systemAccountCode'];
            $data_arr['secondary'] = $GL_retension_record['GLSecondaryCode'];
            $data_arr['gl_desc'] = $GL_retension_record['GLDescription'];
            $data_arr['gl_type'] = $GL_retension_record['masterCategory'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = $totalRetensionValue;
            $data_arr['amount_type'] = 'cr';
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = 2;
            $data_arr['subLedgerDesc'] = 'AP';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'SUP';
            $data_arr['partyAutoID'] = $master['supplierID'];
            $data_arr['partySystemCode'] = $master['supplierCode'];
            $data_arr['partyName'] = $master['supplierName'];
            $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $master['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
            $data_arr['partyCurrencyAmount'] = 0;
            array_push($gl_array['gl_detail'], $data_arr);

        }




        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $master['supplierliabilityAutoID'];
        $data_arr['gl_code'] = $master['supplierliabilitySystemGLCode'];
        $data_arr['secondary'] = $master['supplierliabilityGLAccount'];
        $data_arr['gl_desc'] = $master['supplierliabilityDescription'];
        $data_arr['gl_type'] = $master['supplierliabilityType'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];

        if($cr_total < 0){
            $data_arr['gl_dr'] = $cr_total + $taxttlitm - $totalRetensionValue;
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
        }else{
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = $cr_total + $taxttlitm - $totalRetensionValue;
            $data_arr['amount_type'] = 'cr';
        }
       
        $data_arr['isAddon'] = 0;
        $data_arr['taxMasterAutoID'] = null;
        $data_arr['partyVatIdNo'] = null;
        $data_arr['subLedgerType'] = 2;
        $data_arr['subLedgerDesc'] = 'AP';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = 'SUP';
        $data_arr['partyAutoID'] = $master['supplierID'];
        $data_arr['partySystemCode'] = $master['supplierCode'];
        $data_arr['partyName'] = $master['supplierName'];
        $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
        $data_arr['partyCurrency'] = $master['supplierCurrency'];
        $data_arr['transactionExchangeRate'] = null;
        $data_arr['companyLocalExchangeRate'] = null;
        $data_arr['companyReportingExchangeRate'] = null;
        $data_arr['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
        $data_arr['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
        $data_arr['partyCurrencyAmount'] = 0;
        array_push($gl_array['gl_detail'], $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['approved_YN'] = $master['approvedYN'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'BSI';
        $gl_array['name'] = $master['invoiceType'] . ' BSI';
        $gl_array['primary_Code'] = $master['bookingInvCode'];
        $gl_array['date'] = $master['bookingDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['suppliernamemaster'] = $master['suppliernamemaster'];
        // echo '<pre>';print_r($gl_array); echo '</pre>'; die();
        return $gl_array;
    }

    function fetch_double_entry_debit_note_data($debitNoteMasterAutoID, $code = null)
    {
        $gl_array = array();
        $item_tax_arr = array();
        $dr_total = 0;
        $party_total = 0;
        $companyLocal_total = 0;
        $companyReporting_total = 0;
        $gl_array['gl_detail'] = array();
        $this->db->select('srp_erp_debitnotemaster.*,srp_erp_suppliermaster.supplierName as suppliernamemaster');
        $this->db->where('debitNoteMasterAutoID', $debitNoteMasterAutoID);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_debitnotemaster.supplierID');
        $master = $this->db->get('srp_erp_debitnotemaster')->row_array();

        $q = "SELECT
                        `debitNoteDetailsID`,
                        `GLAutoID`,
                        `SystemGLCode`,
                        `GLCode`,
                        `GLDescription`,
                        `GLType`,
                        SUM(transactionAmount) AS transactionAmount,
                        `segmentCode`,
                        `segmentID`,
                        `companyLocalExchangeRate`,
                        `companyReportingExchangeRate`,
                        `supplierCurrencyExchangeRate`,
                        `projectID`,
                        `projectExchangeRate`,
                         IFNULL(SUM(taxAmount),0) as taxAmount,InvoiceAutoID
       
                    FROM
                        `srp_erp_debitnotedetail`
                    WHERE
                        `debitNoteMasterAutoID` = '" . $debitNoteMasterAutoID . "'
                    GROUP BY GLAutoID, isFromInvoice, segmentID";
        $detail = $this->db->query($q)->result_array();
        //$this->db->select('debitNoteDetailsID,GLAutoID,SystemGLCode,GLCode,GLDescription,GLType,transactionAmount ,segmentCode,segmentID,companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate');
        //$this->db->where('debitNoteMasterAutoID', $debitNoteMasterAutoID);
        //$detail = $this->db->get('srp_erp_debitnotedetail')->result_array();

        /*print_r($detail);
        exit;*/

        $item_tax_group_wise = $this->db->query("SELECT *  FROM (  SELECT
                                                                documentDetailAutoID AS auto_id,
                                                                taxGlAutoID AS gl_auto_id,
                                                                SUM( amount ) AS taxAmount,
                                                                taxMasterID 
                                                                FROM
                                                                `srp_erp_taxledger`
                                                                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                                WHERE
                                                                documentID = 'DN' 
                                                                AND documentMasterAutoID = $debitNoteMasterAutoID
                                                                GROUP BY
                                                                taxMasterID 
                                                                ) t1
                                                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id")->result_array();


        $m_arr = array();
        $p_arr = array();
        for ($i = 0; $i < count($detail); $i++) {
            $assat_entry_arr['auto_id'] = $detail[$i]['debitNoteDetailsID'];
            $assat_entry_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
            $assat_entry_arr['gl_code'] = $detail[$i]['SystemGLCode'];
            $assat_entry_arr['secondary'] = $detail[$i]['GLCode'];
            $assat_entry_arr['gl_desc'] = $detail[$i]['GLDescription'];
            $assat_entry_arr['gl_type'] = $detail[$i]['GLType'];
            $assat_entry_arr['segment_id'] = $detail[$i]['segmentID'];
            $assat_entry_arr['segment'] = $detail[$i]['segmentCode'];
            $assat_entry_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            $assat_entry_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $assat_entry_arr['isAddon'] = 0;
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
            $assat_entry_arr['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $assat_entry_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $assat_entry_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $assat_entry_arr['partyCurrencyAmount'] = null;
            $assat_entry_arr['partyCurrencyDecimalPlaces'] = null;
            $assat_entry_arr['amount_type'] = 'cr';
            if ($detail[$i]['transactionAmount'] >= 0) {
                $assat_entry_arr['gl_dr'] = 0;

                $assat_entry_arr['gl_cr'] = $detail[$i]['transactionAmount'] - $detail[$i]['taxAmount'];


                array_push($p_arr, $assat_entry_arr);
            } else {
                $assat_entry_arr['gl_dr'] = $detail[$i]['transactionAmount'] - $detail[$i]['taxAmount'];
                $assat_entry_arr['gl_cr'] = 0;
                $assat_entry_arr['amount_type'] = 'dr';
                array_push($m_arr, $assat_entry_arr);
            }
           /* if ($detail[$i]['InvoiceAutoID'] != '') {
                $dr_total += $detail[$i]['transactionAmount'] - $detail[$i]['taxAmount'];

            } else {
                $dr_total += $detail[$i]['transactionAmount'];
            }


            $party_total += (($detail[$i]['transactionAmount'] + $detail[$i]['taxAmount']) / $detail[$i]['supplierCurrencyExchangeRate']);
            $companyLocal_total += (($detail[$i]['transactionAmount']+ $detail[$i]['taxAmount']) / $detail[$i]['companyLocalExchangeRate']);
            $companyReporting_total += (($detail[$i]['transactionAmount']+ $detail[$i]['taxAmount']) / $detail[$i]['companyReportingExchangeRate']);*/

            $dr_total += $detail[$i]['transactionAmount'] - $detail[$i]['taxAmount'];

            $party_total += ($detail[$i]['transactionAmount'] / $detail[$i]['supplierCurrencyExchangeRate']);
            $companyLocal_total += ($detail[$i]['transactionAmount'] / $detail[$i]['companyLocalExchangeRate']);
            $companyReporting_total += ($detail[$i]['transactionAmount'] / $detail[$i]['companyReportingExchangeRate']);
        }


        for ($i = 0; $i < count($item_tax_group_wise); $i++) {
            if (!empty($item_tax_group_wise[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_tax_group_wise[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_tax_group_wise[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_tax_group_wise[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_tax_group_wise[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = null;
                $data_arr_tx_group['segment'] = null;
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise[$i]['taxMasterID'];

                $data_arr_tx_group['partyVatIdNo'] = null;
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = '';
                $data_arr_tx_group['partySystemCode'] = '';
                $data_arr_tx_group['partyName'] = '';
                $data_arr_tx_group['partyCurrencyID'] = '';
                $data_arr_tx_group['partyCurrency'] = '';
                $data_arr_tx_group['transactionExchangeRate'] = 1;
                $data_arr_tx_group['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                $data_arr_tx_group['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr_tx_group['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = '';
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = '';
                $data_arr_tx_group['amount_type'] = 'cr';
                $data_arr_tx_group['gl_dr'] = 0;
                $data_arr_tx_group['gl_cr'] = $item_tax_group_wise[$i]['taxAmount'];
                array_push($item_tax_arr, $data_arr_tx_group);
                $dr_total += $data_arr_tx_group['gl_cr'];
            }
        }

        $gl_array['gl_detail'] = $p_arr;
        foreach ($m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_tax_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }


        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $master['supplierliabilityAutoID'];
        $data_arr['gl_code'] = $master['supplierliabilitySystemGLCode'];
        $data_arr['secondary'] = $master['supplierliabilityGLAccount'];
        $data_arr['gl_desc'] = $master['supplierliabilityDescription'];
        $data_arr['gl_type'] = $master['supplierliabilityType'];
        $data_arr['segment_id'] = '';
        $data_arr['segment'] = '';
        $data_arr['gl_dr'] = $dr_total;
        $data_arr['gl_cr'] = 0;
        $data_arr['amount_type'] = 'dr';
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 2;
        $data_arr['subLedgerDesc'] = 'AP';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = null;
        $data_arr['partyAutoID'] = null;
        $data_arr['partySystemCode'] = null;
        $data_arr['partyName'] = null;
        $data_arr['partyCurrencyID'] = null;
        $data_arr['partyCurrency'] = null;
        $data_arr['transactionExchangeRate'] = ($dr_total / $dr_total);
        $data_arr['partyExchangeRate'] = ($dr_total / $party_total);
        $data_arr['companyLocalExchangeRate'] = ($dr_total / $companyLocal_total);
        $data_arr['companyReportingExchangeRate'] = ($dr_total / $companyReporting_total);
        $data_arr['partyCurrencyAmount'] = 0;
        $data_arr['partyCurrencyDecimalPlaces'] = 0;
        array_push($gl_array['gl_detail'], $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['approved_YN'] = $master['approvedYN'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'DN';
        $gl_array['name'] = 'Debit Note';
        $gl_array['primary_Code'] = $master['debitNoteCode'];
        $gl_array['date'] = $master['debitNoteDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['suppliernamemaster'] = $master['suppliernamemaster'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }

    function fetch_double_entry_payment_voucher_data($payVoucherAutoId, $code = null)
    {

        $gl_array = array();
        $inv_total = 0;
        $cr_total = 0;
        $party_total = 0;
        $companyLocal_total = 0;
        $companyReporting_total = 0;
        $tax_total = 0;
        $dn_total = 0;
        $dn_party_total = 0;
        $dn_companyLocal_total = 0;
        $dn_companyReporting_total = 0;
        $sc_total = 0;
        $sc_party_total = 0;
        $sc_companyLocal_total = 0;
        $sc_companyReporting_total = 0;
        $gl_array['gl_detail'] = array();
        $this->db->select('*,srp_erp_paymentvouchermaster.companyID as companyID,srp_erp_paymentvouchermaster.companyCode as companyCode,srp_erp_suppliermaster.supplierName as suppliernamemaster');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID', 'left');
        $master = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate,supplierCurrencyDecimalPlaces,supplierCurrencyID,transactionExchangeRate,companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,taxMasterAutoID');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $tax_detail = $this->db->get('srp_erp_paymentvouchertaxdetails')->result_array();

        $this->db->select('payVoucherDetailAutoID,GLAutoID,SystemGLCode,GLCode,detailInvoiceType,GLDescription,GLType,transactionAmount,segmentCode,segmentID,type,companyLocalExchangeRate,companyReportingExchangeRate,partyExchangeRate,partyAmount,companyLocalAmount,companyReportingAmount,projectID,projectExchangeRate,project_categoryID,project_subCategoryID,description');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $detail = $this->db->get('srp_erp_paymentvoucherdetail')->result_array();

        $item_tax_group_wise = $this->db->query("SELECT *  FROM(
            SELECT documentDetailAutoID as auto_id, taxGlAutoID as gl_auto_id, SUM(amount) as taxAmount, taxMasterID
            FROM srp_erp_taxledger
            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
            LEFT JOIN srp_erp_paymentvoucherdetail ON srp_erp_paymentvoucherdetail.payVoucherDetailAutoID  = srp_erp_taxledger.documentDetailAutoID
            where documentID = 'PV' AND srp_erp_taxledger.isClaimable = 1 AND documentMasterAutoID = $payVoucherAutoId GROUP BY taxGlAutoID,
            taxMasterID ) t1
            LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id 	
            GROUP BY
	        GLAutoID")->result_array();



        $item_tax_group_wise_isVatReg = $this->db->query("SELECT *  FROM(
            SELECT documentDetailAutoID as auto_id, GLAutoID as gl_auto_id, SUM(amount) as taxAmount, taxMasterID
            FROM srp_erp_taxledger
            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
            LEFT JOIN srp_erp_paymentvoucherdetail ON srp_erp_paymentvoucherdetail.payVoucherDetailAutoID  = srp_erp_taxledger.documentDetailAutoID
            where documentID = 'PV' AND srp_erp_taxledger.isClaimable = 0 AND documentMasterAutoID = $payVoucherAutoId GROUP BY GLAutoID ) t1
            LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id 
            GROUP BY
              GLAutoID,
	        taxMasterID")->result_array();


        $APA = fetch_gl_account_desc($master["partyGLAutoID"]);

        $m_arr = array();
        $p_arr = array();
        $e_m_arr = array();
        $e_p_arr = array();
        $ex_p_arr = array();
        $ex_m_arr = array();
        $item_tax_arr = array();
        $debitNoteAmount = 0;
        $supplier_invtotal = 0;
        $customer_invtotal = 0;

        for ($i = 0; $i < count($detail); $i++) {
            if ($detail[$i]['type'] == 'Invoice') {
                $inv_total += $detail[$i]['transactionAmount'];
                $party_total += $detail[$i]['partyAmount'];
                $companyLocal_total += $detail[$i]['companyLocalAmount'];
                $companyReporting_total += $detail[$i]['companyReportingAmount'];
              
            }
            if ($detail[$i]['type'] == 'SC') {
                $sc_total += $detail[$i]['transactionAmount'];
                //$party_total += $detail[$i]['partyAmount'];
                $sc_party_total += $detail[$i]['partyAmount'];
                $sc_companyLocal_total += $detail[$i]['companyLocalAmount'];
                $sc_companyReporting_total += $detail[$i]['companyReportingAmount'];
            }
            if ($detail[$i]['type'] == 'debitnote') {
                $dn_total += $detail[$i]['transactionAmount'];
                $dn_party_total += $detail[$i]['partyAmount'];
                $dn_companyLocal_total += $detail[$i]['companyLocalAmount'];
                $dn_companyReporting_total += $detail[$i]['companyReportingAmount'];
            }

            if ($detail[$i]['type'] == 'INGL') {
                $cr_total += $detail[$i]['transactionAmount'] * -1;
            }elseif($detail[$i]['type'] == 'Invoice'){
                if($detail[$i]['detailInvoiceType'] == 'CUS'){
                    $cr_total += $detail[$i]['transactionAmount'] * -1;
                }else{
                    $cr_total += $detail[$i]['transactionAmount'];
                }
            }elseif($detail[$i]['type'] == 'debitnote'){
                $cr_total += $detail[$i]['transactionAmount'] * -1;
            }else{
                $cr_total += $detail[$i]['transactionAmount'];
            }
            
        }


        for ($i = 0; $i < count($detail); $i++) {
            if ($detail[$i]['type'] == 'Advance') {
                $data_arr['auto_id'] = 0;
                $data_arr['gl_auto_id'] = $APA['GLAutoID'];
                $data_arr['gl_code'] = $APA['systemAccountCode'];
                $data_arr['secondary'] = $APA['GLSecondaryCode'];
                $data_arr['gl_desc'] = $APA['GLDescription'] . ' - Advance';
                $data_arr['gl_type'] = $APA['subCategory'];
                $data_arr['segment_id'] = '-';
                $data_arr['segment'] = '-';
                $data_arr['projectID'] = null;
                $data_arr['project_categoryID'] = null;
                $data_arr['project_subCategoryID'] = null;
                $data_arr['projectExchangeRate'] = null;
                $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                $data_arr['gl_cr'] = 0;
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 2;
                $data_arr['subLedgerDesc'] = 'AP';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = $master['partyType'];
                $data_arr['partyAutoID'] = $master['partyID'];
                $data_arr['partySystemCode'] = $master['partyCode'];
                $data_arr['partyName'] = $master['partyName'];
                $data_arr['partyCurrencyID'] = $master['partyCurrencyID'];
                $data_arr['partyCurrency'] = $master['partyCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['partyExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                array_push($p_arr, $data_arr);
            } else if ($detail[$i]['type'] == 'Invoice') {

                if($detail[$i]['detailInvoiceType'] == 'CUS'){
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $detail[$i]['transactionAmount']; 
                    $data_arr['amount_type'] = 'cr';
                    $customer_invtotal += $detail[$i]['transactionAmount'];
                }else{
                    $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $data_arr['gl_cr'] = 0;
                    $data_arr['amount_type'] = 'dr';
                   // $cr_total -= $detail[$i]['transactionAmount'];
                    $supplier_invtotal += $detail[$i]['transactionAmount'];
                }


                $data_arr['auto_id'] = $detail[$i]['payVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Invoice';
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = null;
                $data_arr['project_categoryID'] = null;
                $data_arr['project_subCategoryID'] = null;
                $data_arr['projectExchangeRate'] = null;
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 2;
                $data_arr['subLedgerDesc'] = 'AP';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = $master['partyType'];
                $data_arr['partyAutoID'] = $master['partyID'];
                $data_arr['partySystemCode'] = $master['partyCode'];
                $data_arr['partyName'] = $master['partyName'];
                $data_arr['partyCurrencyID'] = $master['partyCurrencyID'];
                $data_arr['partyCurrency'] = $master['partyCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['partyExchangeRate'] = ($inv_total / $party_total);
                $data_arr['companyLocalExchangeRate'] = ($inv_total / $companyLocal_total);
                $data_arr['companyReportingExchangeRate'] = ($inv_total / $companyReporting_total);
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];

                array_push($p_arr, $data_arr);

            } else if ($detail[$i]['type'] == 'SC') {
                $data_arr['auto_id'] = $detail[$i]['payVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Sales Commission';
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['projectID'] = null;
                $data_arr['project_categoryID'] = null;
                $data_arr['project_subCategoryID'] = null;
                $data_arr['projectExchangeRate'] = null;
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                $data_arr['gl_cr'] = 0;
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 4;
                $data_arr['subLedgerDesc'] = 'AP';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = $master['partyType'];
                $data_arr['partyAutoID'] = $master['partyID'];
                $data_arr['partySystemCode'] = $master['partyCode'];
                $data_arr['partyName'] = $master['partyName'];
                $data_arr['partyCurrencyID'] = $master['partyCurrencyID'];
                $data_arr['partyCurrency'] = $master['partyCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['partyExchangeRate'] = ($sc_total / $sc_party_total);
                $data_arr['companyLocalExchangeRate'] = ($sc_total / $sc_companyLocal_total);
                $data_arr['companyReportingExchangeRate'] = ($sc_total / $sc_companyReporting_total);
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                array_push($p_arr, $data_arr);
            } else if ($detail[$i]['type'] == 'debitnote') {
                $assat_entry_arr['auto_id'] = $detail[$i]['payVoucherDetailAutoID'];
                $assat_entry_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $assat_entry_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $assat_entry_arr['secondary'] = $detail[$i]['GLCode'];
                $assat_entry_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Debit Note ';
                $assat_entry_arr['gl_type'] = $detail[$i]['GLType'];
                $assat_entry_arr['segment_id'] = $detail[$i]['segmentID'];
                $assat_entry_arr['segment'] = $detail[$i]['segmentCode'];
                $assat_entry_arr['isAddon'] = 0;
                $assat_entry_arr['projectID'] = null;
                $assat_entry_arr['project_categoryID'] = null;
                $assat_entry_arr['project_subCategoryID'] = null;
                $assat_entry_arr['projectExchangeRate'] = null;
                $assat_entry_arr['taxMasterAutoID'] = null;
                $assat_entry_arr['partyVatIdNo'] = null;
                $assat_entry_arr['subLedgerType'] = 2;
                $assat_entry_arr['subLedgerDesc'] = 'AP';
                $assat_entry_arr['partyContractID'] = null;
                $assat_entry_arr['partyType'] = $master['partyType'];
                $assat_entry_arr['partyAutoID'] = $master['partyID'];
                $assat_entry_arr['partySystemCode'] = $master['partyCode'];
                $assat_entry_arr['partyName'] = $master['partyName'];
                $assat_entry_arr['partyCurrencyID'] = $master['partyCurrencyID'];
                $assat_entry_arr['partyCurrency'] = $master['partyCurrency'];
                $assat_entry_arr['transactionExchangeRate'] = 1;
                /*$assat_entry_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $assat_entry_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $assat_entry_arr['partyExchangeRate'] = $master['partyExchangeRate'];*/
                $assat_entry_arr['companyLocalExchangeRate'] = ($dn_total / $dn_companyLocal_total);
                $assat_entry_arr['companyReportingExchangeRate'] = ($dn_total / $dn_companyReporting_total);
                $assat_entry_arr['partyExchangeRate'] = ($dn_total / $dn_party_total);

                $assat_entry_arr['partyCurrencyAmount'] = 0;
                $assat_entry_arr['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
                $assat_entry_arr['amount_type'] = 'dr';
          
                if (($detail[$i]['transactionAmount'] * -1) >= 0) {
                    $assat_entry_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $assat_entry_arr['gl_cr'] = 0;
                    array_push($e_p_arr, $assat_entry_arr);
                } else {
                    $assat_entry_arr['gl_dr'] = 0;
                    $assat_entry_arr['gl_cr'] = $detail[$i]['transactionAmount'];
                    $assat_entry_arr['amount_type'] = 'cr';
                    array_push($e_m_arr, $assat_entry_arr);
                }
        
                $debitNoteAmount += $detail[$i]['transactionAmount'];
                //$tax_total += $detail[$i]['transactionAmount'];

            } else if ($detail[$i]['type'] == 'SR') {
                $assat_entry_arr['auto_id'] = $detail[$i]['payVoucherDetailAutoID'];
                $assat_entry_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $assat_entry_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $assat_entry_arr['secondary'] = $detail[$i]['GLCode'];
                $assat_entry_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Sales Return ';
                $assat_entry_arr['gl_type'] = $detail[$i]['GLType'];
                $assat_entry_arr['segment_id'] = $detail[$i]['segmentID'];
                $assat_entry_arr['segment'] = $detail[$i]['segmentCode'];
                $assat_entry_arr['isAddon'] = 0;
                $assat_entry_arr['taxMasterAutoID'] = null;
                $assat_entry_arr['partyVatIdNo'] = null;
                $assat_entry_arr['subLedgerType'] = 2;
                $assat_entry_arr['projectID'] = null;
                $assat_entry_arr['project_categoryID'] = null;
                $assat_entry_arr['project_subCategoryID'] = null;
                $assat_entry_arr['projectExchangeRate'] = null;
                $assat_entry_arr['subLedgerDesc'] = 'AP';
                $assat_entry_arr['partyContractID'] = null;
                $assat_entry_arr['partyType'] = $master['partyType'];
                $assat_entry_arr['partyAutoID'] = $master['partyID'];
                $assat_entry_arr['partySystemCode'] = $master['partyCode'];
                $assat_entry_arr['partyName'] = $master['partyName'];
                $assat_entry_arr['partyCurrencyID'] = $master['partyCurrencyID'];
                $assat_entry_arr['partyCurrency'] = $master['partyCurrency'];
                $assat_entry_arr['transactionExchangeRate'] = 1;
                $assat_entry_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $assat_entry_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $assat_entry_arr['partyExchangeRate'] = $master['partyExchangeRate'];
                $assat_entry_arr['partyCurrencyAmount'] = 0;
                $assat_entry_arr['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
                $assat_entry_arr['amount_type'] = 'dr';
                if (($detail[$i]['transactionAmount'] * -1) >= 0) {
                    $assat_entry_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $assat_entry_arr['gl_cr'] = 0;
                    array_push($e_p_arr, $assat_entry_arr);
                } else {
                    $assat_entry_arr['gl_dr'] = 0;
                    $assat_entry_arr['gl_cr'] = $detail[$i]['transactionAmount'];
                    $assat_entry_arr['amount_type'] = 'cr';
                    array_push($e_m_arr, $assat_entry_arr);
                }
                $debitNoteAmount += $detail[$i]['transactionAmount'];
                //$tax_total += $detail[$i]['transactionAmount'];

            } else if ($detail[$i]['type'] == 'Item') {
                $data_arr['auto_id'] = $detail[$i]['payVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Item';
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
                $data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                $data_arr['gl_cr'] = 0;
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = $master['partyType'];
                $data_arr['partyAutoID'] = $master['partyID'];
                $data_arr['partySystemCode'] = $master['partyCode'];
                $data_arr['partyName'] = $master['partyName'];
                $data_arr['partyCurrencyID'] = $master['partyCurrencyID'];
                $data_arr['partyCurrency'] = $master['partyCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['partyExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                array_push($p_arr, $data_arr);
                $tax_total += $detail[$i]['transactionAmount'];
            } else if ($detail[$i]['type'] == 'PRQ') {
                $data_arr['auto_id'] = $detail[$i]['payVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Item';
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
                $data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                $data_arr['gl_cr'] = 0;
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = $master['partyType'];
                $data_arr['partyAutoID'] = $master['partyID'];
                $data_arr['partySystemCode'] = $master['partyCode'];
                $data_arr['partyName'] = $master['partyName'];
                $data_arr['partyCurrencyID'] = $master['partyCurrencyID'];
                $data_arr['partyCurrency'] = $master['partyCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['partyExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                array_push($p_arr, $data_arr);
                $tax_total += $detail[$i]['transactionAmount'];
            } else {

                if($detail[$i]['type'] == 'INGL'){
                    $detail[$i]['transactionAmount'] = $detail[$i]['transactionAmount'] * -1;
                }

             //   $supplier_invtotal += $detail[$i]['transactionAmount'];
                // $cr_total -= $detail[$i]['transactionAmount'];

                $assat_entry_arr['auto_id'] = $detail[$i]['payVoucherDetailAutoID'];
                $assat_entry_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $assat_entry_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $assat_entry_arr['secondary'] = $detail[$i]['GLCode'];
                $assat_entry_arr['gl_desc'] = $detail[$i]['GLDescription'];
                $assat_entry_arr['gl_type'] = $detail[$i]['GLType'];
                $assat_entry_arr['segment_id'] = $detail[$i]['segmentID'];
                $assat_entry_arr['segment'] = $detail[$i]['segmentCode'];
                $assat_entry_arr['projectID'] = $detail[$i]['projectID'];
                $assat_entry_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
                $assat_entry_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
                $assat_entry_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $assat_entry_arr['isAddon'] = 0;
                $assat_entry_arr['taxMasterAutoID'] = null;
                $assat_entry_arr['partyVatIdNo'] = null;
                $assat_entry_arr['subLedgerType'] = 0;
                $assat_entry_arr['subLedgerDesc'] = null;
                $assat_entry_arr['partyContractID'] = null;
                $assat_entry_arr['partyType'] = $master['partyType'];
                $assat_entry_arr['partyAutoID'] = $master['partyID'];
                $assat_entry_arr['partySystemCode'] = $master['partyCode'];
                $assat_entry_arr['partyName'] = $master['partyName'];
                $assat_entry_arr['partyCurrencyID'] = $master['partyCurrencyID'];
                $assat_entry_arr['partyCurrency'] = $master['partyCurrency'];
                $assat_entry_arr['transactionExchangeRate'] = 1;
                $assat_entry_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $assat_entry_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $assat_entry_arr['partyExchangeRate'] = $master['partyExchangeRate'];
                $assat_entry_arr['partyCurrencyAmount'] = 0;
                $assat_entry_arr['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
                $assat_entry_arr['amount_type'] = 'dr';
                $assat_entry_arr['gl_remarks'] = $detail[$i]['description'];
                if ($detail[$i]['transactionAmount'] >= 0) {
                    $assat_entry_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $assat_entry_arr['gl_cr'] = 0;
                    array_push($ex_p_arr, $assat_entry_arr);
                } else {
                    $assat_entry_arr['gl_dr'] = 0;
                    $assat_entry_arr['gl_cr'] = ($detail[$i]['transactionAmount'] * -1);
                    $assat_entry_arr['amount_type'] = 'cr';
                    array_push($ex_m_arr, $assat_entry_arr);
                }
                $tax_total += $detail[$i]['transactionAmount'];
            }
        }
    
    

        for ($i = 0; $i < count($item_tax_group_wise); $i++) {
            if (!empty($item_tax_group_wise[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_tax_group_wise[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_tax_group_wise[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_tax_group_wise[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_tax_group_wise[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = null;
                $data_arr_tx_group['segment'] = null;
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = '';
                $data_arr_tx_group['partySystemCode'] = '';
                $data_arr_tx_group['partyName'] = '';
                $data_arr_tx_group['partyCurrencyID'] = '';
                $data_arr_tx_group['partyCurrency'] = '';
                $data_arr_tx_group['transactionExchangeRate'] = 1;
                $data_arr_tx_group['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr_tx_group['companyReportingExchangeRate'] =  $master['companyReportingExchangeRate'];
                $data_arr_tx_group['partyExchangeRate'] =$master['partyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = 0;
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'dr';
                $data_arr_tx_group['gl_dr'] = $item_tax_group_wise[$i]['taxAmount'];
                $data_arr_tx_group['gl_cr'] = 0;
                array_push($item_tax_arr, $data_arr_tx_group);

                $cr_total += $data_arr_tx_group['gl_dr'];
            }
        }


        for ($i = 0; $i < count($item_tax_group_wise_isVatReg); $i++) {
            if (!empty($item_tax_group_wise_isVatReg[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_tax_group_wise_isVatReg[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise_isVatReg[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_tax_group_wise_isVatReg[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_tax_group_wise_isVatReg[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_tax_group_wise_isVatReg[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_tax_group_wise_isVatReg[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = null;
                $data_arr_tx_group['segment'] = null;
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise_isVatReg[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = '';
                $data_arr_tx_group['partySystemCode'] = '';
                $data_arr_tx_group['partyName'] = '';
                $data_arr_tx_group['partyCurrencyID'] = '';
                $data_arr_tx_group['partyCurrency'] = '';
                $data_arr_tx_group['transactionExchangeRate'] = 1;
                $data_arr_tx_group['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr_tx_group['companyReportingExchangeRate'] =  $master['companyReportingExchangeRate'];
                $data_arr_tx_group['partyExchangeRate'] =$master['partyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = 0;
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'dr';
                $data_arr_tx_group['gl_dr'] = $item_tax_group_wise_isVatReg[$i]['taxAmount'];
                $data_arr_tx_group['gl_cr'] = 0;
                array_push($item_tax_arr, $data_arr_tx_group);

                $cr_total += $data_arr_tx_group['gl_dr'];
            }
        }

        $p_arr = $this->array_group_sum_tax_pm($p_arr);
        $m_arr = $this->array_group_sum_tax_pm($m_arr);

        $e_p_arr = $this->array_group_sum_tax_pm($e_p_arr);
        $e_m_arr = $this->array_group_sum_tax_pm($e_m_arr);

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
        foreach ($ex_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($ex_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_tax_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        //$tax_total = $cr_total;
        for ($i = 0; $i < count($tax_detail); $i++) {
            $data_arr['auto_id'] = $tax_detail[$i]['taxDetailAutoID'];
            $data_arr['gl_auto_id'] = $tax_detail[$i]['GLAutoID'];
            $data_arr['gl_code'] = $tax_detail[$i]['SystemGLCode'];
            $data_arr['secondary'] = $tax_detail[$i]['GLCode'];
            $data_arr['gl_desc'] = $tax_detail[$i]['GLDescription'] . ' - Tax';
            $data_arr['gl_type'] = $tax_detail[$i]['GLType'];
            $data_arr['segment_id'] = $tax_detail[$i]['segmentID'];
            $data_arr['segment'] = $tax_detail[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = $tax_detail[$i]['taxMasterAutoID'];
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['projectID'] = null;
            $data_arr['project_categoryID'] = null;
            $data_arr['project_subCategoryID'] = null;
            $data_arr['projectExchangeRate'] = null;
            $data_arr['partyType'] = 'AUT';
            $data_arr['partyAutoID'] = $tax_detail[$i]['supplierAutoID'];
            $data_arr['partySystemCode'] = $tax_detail[$i]['supplierSystemCode'];
            $data_arr['partyName'] = $tax_detail[$i]['supplierName'];
            $data_arr['partyCurrencyID'] = $tax_detail[$i]['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $tax_detail[$i]['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = $tax_detail[$i]['transactionExchangeRate'];
            $data_arr['companyLocalExchangeRate'] = $tax_detail[$i]['companyLocalExchangeRate'];
            $data_arr['companyReportingExchangeRate'] = $tax_detail[$i]['companyReportingExchangeRate'];
            $data_arr['partyExchangeRate'] = $tax_detail[$i]['supplierCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $tax_detail[$i]['supplierCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'dr';
            $data_arr['gl_dr'] = (($tax_detail[$i]['taxPercentage'] / 100) * $tax_total);
            $data_arr['gl_cr'] = 0;
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total += $data_arr['gl_dr'];
        }

        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $master['bankGLAutoID'];
        $data_arr['gl_code'] = $master['bankSystemAccountCode'];
        $data_arr['secondary'] = $master['bankGLSecondaryCode'];
        $data_arr['gl_desc'] = $master['PVbank'];
        $data_arr['gl_type'] = $master['PVbankType'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];


        // print_r($debitNoteAmount); exit;
       
        // if($supplier_invtotal > 0 || $customer_invtotal > 0 ){
        //     $cr_total =  ($supplier_invtotal + ($debitNoteAmount * 2)) - $customer_invtotal;
        // } else{
        //     $cr_total =  $cr_total - ($debitNoteAmount * 2);
        // }
    

        // $cr_total =   $cr_total + $cr_total_ex;

        // print_r($cr_total); exit;
        // $cr_total =  $cr_total - ($debitNoteAmount * 2);
      
        // print_r($cr_total); exit;

        if ($cr_total <= 0) {
            $data_arr['gl_dr'] = ($cr_total  * -1);
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
         } else {
             $data_arr['gl_dr'] = 0;
             $data_arr['gl_cr'] =  $cr_total;
             $data_arr['amount_type'] = 'cr';
         }
        


        // $data_arr['gl_dr'] = 0;
        // $data_arr['gl_cr'] = $cr_total - ($debitNoteAmount * 2);
        // $data_arr['amount_type'] = 'cr';
        $data_arr['isAddon'] = 0;
        $data_arr['taxMasterAutoID'] = null;
        $data_arr['partyVatIdNo'] = null;
        $data_arr['subLedgerType'] = 0;
        $data_arr['subLedgerDesc'] = null;
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = $master['partyType'];
        $data_arr['partyAutoID'] = $master['partyID'];
        $data_arr['partySystemCode'] = $master['partyCode'];
        $data_arr['partyName'] = $master['partyName'];
        $data_arr['partyCurrencyID'] = $master['partyCurrencyID'];
        $data_arr['partyCurrency'] = $master['partyCurrency'];
        $data_arr['transactionExchangeRate'] = 1;
        $data_arr['partyExchangeRate'] = $master['partyExchangeRate'];
        $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data_arr['partyCurrencyAmount'] = 0;
        $data_arr['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
        array_push($gl_array['gl_detail'], $data_arr);

     
        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'PV';
        $gl_array['name'] = 'Payment Voucher';
        $gl_array['approved_YN'] = $master['approvedYN'];
        $gl_array['primary_Code'] = $master['PVcode'];
        $gl_array['date'] = $master['PVdate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['suppliernamemaster'] = $master['suppliernamemaster'];
      
        return $gl_array;
    }

    function fetch_double_entry_commission_payment_data($payVoucherAutoId, $code = null)
    {
        $this->db->select('pvd.payVoucherDetailAutoID,pvd.GLAutoID,pvd.SystemGLCode,pvd.GLCode,pvd.GLDescription,pvd.GLType,SUM(pvd.transactionAmount) as transactionAmount,pvd.segmentCode,pvd.segmentID,pvd.type,pvd.companyLocalExchangeRate,pvd.companyReportingExchangeRate,pvd.partyExchangeRate,SUM(pvd.partyAmount)  as partyAmount,SUM(pvd.companyLocalAmount) as companyLocalAmount,SUM(pvd.companyReportingAmount) as companyReportingAmount,pv.PVbankCode,pv.bankSystemAccountCode,pv.bankGLSecondaryCode,
        pv.PVbank,pv.PVbankType,pv.partyType,pv.partyID,pv.partyCode,pv.partyName,pv.partyCurrencyID,pv.partyCurrency,pv.partyExchangeRate,pv.partyCurrencyDecimalPlaces');
        $this->db->join('srp_erp_paymentvouchermaster as pv', "pvd.payVoucherAutoId = pv.payVoucherAutoId", "inner");
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $detail = $this->db->get('srp_erp_paymentvoucherdetail as pvd')->row_array();

        $gl_array = array();

        $data_arr['auto_id'] = $detail['payVoucherDetailAutoID'];
        $data_arr['gl_auto_id'] = $detail['GLAutoID'];
        $data_arr['gl_code'] = $detail['SystemGLCode'];
        $data_arr['secondary'] = $detail['GLCode'];
        $data_arr['gl_desc'] = $detail['GLDescription'] . ' - Sales Commission';
        $data_arr['gl_type'] = $detail['GLType'];
        $data_arr['segment_id'] = $detail['segmentID'];
        $data_arr['segment'] = $detail['segmentCode'];
        $data_arr['gl_dr'] = $detail['transactionAmount'];
        $data_arr['gl_cr'] = 0;
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 4;
        $data_arr['subLedgerDesc'] = 'AP';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = $detail['partyType'];
        $data_arr['partyAutoID'] = $detail['partyID'];
        $data_arr['partySystemCode'] = $detail['partyCode'];
        $data_arr['partyName'] = $detail['partyName'];
        $data_arr['partyCurrencyID'] = $detail['partyCurrencyID'];
        $data_arr['partyCurrency'] = $detail['partyCurrency'];
        $data_arr['transactionExchangeRate'] = 1;
        $data_arr['partyExchangeRate'] = ($detail["transactionAmount"] / $detail['partyAmount']);
        $data_arr['companyLocalExchangeRate'] = ($detail["transactionAmount"] / $detail['companyLocalAmount']);
        $data_arr['companyReportingExchangeRate'] = ($detail["transactionAmount"] / $detail['companyReportingAmount']);
        $data_arr['partyCurrencyAmount'] = 0;
        $data_arr['partyCurrencyDecimalPlaces'] = $detail['partyCurrencyDecimalPlaces'];
        $data_arr['amount_type'] = 'dr';
        array_push($gl_array['gl_detail'], $data_arr);

        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $detail['PVbankCode'];
        $data_arr['gl_code'] = $detail['bankSystemAccountCode'];
        $data_arr['secondary'] = $detail['bankGLSecondaryCode'];
        $data_arr['gl_desc'] = $detail['PVbank'];
        $data_arr['gl_type'] = $detail['PVbankType'];
        $data_arr['segment_id'] = $detail['segmentID'];
        $data_arr['segment'] = $detail['segmentCode'];
        $data_arr['gl_dr'] = 0;
        $data_arr['gl_cr'] = $detail["transactionAmount"];
        $data_arr['amount_type'] = 'cr';
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 0;
        $data_arr['subLedgerDesc'] = null;
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = $detail['partyType'];
        $data_arr['partyAutoID'] = $detail['partyID'];
        $data_arr['partySystemCode'] = $detail['partyCode'];
        $data_arr['partyName'] = $detail['partyName'];
        $data_arr['partyCurrencyID'] = $detail['partyCurrencyID'];
        $data_arr['partyCurrency'] = $detail['partyCurrency'];
        $data_arr['transactionExchangeRate'] = 1;
        $data_arr['partyExchangeRate'] = $detail['partyExchangeRate'];
        $data_arr['companyLocalExchangeRate'] = $detail['companyLocalExchangeRate'];
        $data_arr['companyReportingExchangeRate'] = $detail['companyReportingExchangeRate'];
        $data_arr['partyCurrencyAmount'] = 0;
        $data_arr['partyCurrencyDecimalPlaces'] = $detail['partyCurrencyDecimalPlaces'];
        array_push($gl_array['gl_detail'], $data_arr);
    }

    function fetch_double_entry_journal_entry_data($JVMasterAutoId, $code = null)
    {
        $gl_array = array();
        $gl_array['gl_detail'] = array();
        $this->db->select('*');
        $this->db->where('JVMasterAutoId', $JVMasterAutoId);
        $master = $this->db->get('srp_erp_jvmaster')->row_array();

        $this->db->select('JVDetailAutoID,GLAutoID,SystemGLCode,GLCode,GLDescription,GLType,debitAmount,creditAmount,segmentCode,segmentID, gl_type,description,projectID,project_categoryID,project_subCategoryID,projectExchangeRate');
        $this->db->where('JVMasterAutoId', $JVMasterAutoId);
        $detail = $this->db->get('srp_erp_jvdetail')->result_array();

        $m_arr = array();
        $p_arr = array();
        for ($i = 0; $i < count($detail); $i++) {
            $data_arr['auto_id'] = $detail[$i]['JVDetailAutoID'];
            $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
            $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
            $data_arr['description'] = $detail[$i]['description'];
            $data_arr['secondary'] = $detail[$i]['GLCode'];
            $data_arr['gl_desc'] = $detail[$i]['GLDescription'];
            $data_arr['gl_type'] = $detail[$i]['GLType'];
            $data_arr['segment_id'] = $detail[$i]['segmentID'];
            $data_arr['segment'] = $detail[$i]['segmentCode'];
            $data_arr['projectID'] = $detail[$i]['projectID'];
            $data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
            $data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
            $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = null;
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = null;
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = null;
            $data_arr['partyCurrencyAmount'] = null;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            if ($detail[$i]['gl_type'] == 'Cr') {
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $detail[$i]['creditAmount'];
                $data_arr['amount_type'] = 'cr';
                array_push($gl_array['gl_detail'], $data_arr);
            } else {
                $data_arr['gl_dr'] = $detail[$i]['debitAmount'];
                $data_arr['gl_cr'] = 0;
                $data_arr['amount_type'] = 'dr';
                array_push($gl_array['gl_detail'], $data_arr);
            }
        }

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'JV';
        $gl_array['name'] = 'Journal Entry';
        $gl_array['primary_Code'] = $master['JVcode'];
        $gl_array['date'] = $master['JVdate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }

    function fetch_double_entry_credit_note_data($creditNoteMasterAutoID, $code = null)
    {
        $gl_array = array();
        $cr_total = 0;
        $party_total = 0;
        $companyLocal_total = 0;
        $companyReporting_total = 0;
        $gl_array['gl_detail'] = array();
        $this->db->select('srp_erp_creditnotemaster.*,srp_erp_customermaster.customerName as customerNamemaster, srp_erp_customermaster.vatIdNo as vatIdNo');
        $this->db->where('creditNoteMasterAutoID', $creditNoteMasterAutoID);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_creditnotemaster.customerID', 'left');
        $master = $this->db->get('srp_erp_creditnotemaster')->row_array();

        $this->db->select('creditNoteDetailsID,GLAutoID,SystemGLCode,GLCode,GLDescription,GLType,transactionAmount, segmentCode,segmentID,,companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,projectID,projectExchangeRate, IFNULL(taxAmount, 0) as taxAmount');
        $this->db->where('creditNoteMasterAutoID', $creditNoteMasterAutoID);
        $detail = $this->db->get('srp_erp_creditnotedetail')->result_array();

        $item_tax_group_wise = $this->db->query("SELECT *  FROM(
            SELECT documentDetailAutoID as auto_id, taxGlAutoID as gl_auto_id, SUM(amount) as taxAmount, taxMasterID
                FROM `srp_erp_taxledger`
                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                where documentID = 'CN' AND documentMasterAutoID = $creditNoteMasterAutoID GROUP BY taxMasterID) t1
            LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id")->result_array();

        $m_arr = array();
        $p_arr = array();
        $item_tax_arr = array();
        for ($i = 0; $i < count($detail); $i++) {
            $data_arr['auto_id'] = $detail[$i]['creditNoteDetailsID'];
            $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
            $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
            $data_arr['secondary'] = $detail[$i]['GLCode'];
            $data_arr['gl_desc'] = $detail[$i]['GLDescription'];
            $data_arr['gl_type'] = $detail[$i]['GLType'];
            $data_arr['segment_id'] = $detail[$i]['segmentID'];
            $data_arr['segment'] = $detail[$i]['segmentCode'];
            $data_arr['projectID'] = $detail[$i]['projectID'];
            $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = null;
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = null;
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = 1;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data_arr['partyCurrencyAmount'] = null;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'dr';
            if ($detail[$i]['transactionAmount'] >= 0) {
                $data_arr['gl_dr'] = $detail[$i]['transactionAmount'] - $detail[$i]['taxAmount'];
                $data_arr['gl_cr'] = 0;
                array_push($p_arr, $data_arr);
            } else {
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $detail[$i]['transactionAmount'] - $detail[$i]['taxAmount'];
                $data_arr['amount_type'] = 'cr';
                array_push($m_arr, $data_arr);
            }
            $cr_total += $detail[$i]['transactionAmount'] - $detail[$i]['taxAmount'];
            $party_total += ($detail[$i]['transactionAmount'] / $detail[$i]['customerCurrencyExchangeRate']);
            $companyLocal_total += ($detail[$i]['transactionAmount'] / $detail[$i]['companyLocalExchangeRate']);
            $companyReporting_total += ($detail[$i]['transactionAmount'] / $detail[$i]['companyReportingExchangeRate']);
        }

        for ($i = 0; $i < count($item_tax_group_wise); $i++) {
            if (!empty($item_tax_group_wise[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_tax_group_wise[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_tax_group_wise[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_tax_group_wise[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_tax_group_wise[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = null;
                $data_arr_tx_group['segment'] = null;
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = '';
                $data_arr_tx_group['partySystemCode'] = '';
                $data_arr_tx_group['partyName'] = '';
                $data_arr_tx_group['partyCurrencyID'] = '';
                $data_arr_tx_group['partyCurrency'] = '';
                $data_arr_tx_group['transactionExchangeRate'] = 1;
                $data_arr_tx_group['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr_tx_group['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr_tx_group['partyExchangeRate'] =$master['customerCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = '';
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = '';
                $data_arr_tx_group['amount_type'] = 'dr';
                $data_arr_tx_group['gl_dr'] = $item_tax_group_wise[$i]['taxAmount'];
                $data_arr_tx_group['gl_cr'] = 0;
                array_push($item_tax_arr, $data_arr_tx_group);


                $cr_total += $data_arr_tx_group['gl_dr'];

            }
        }

        $gl_array['gl_detail'] = $p_arr;
        foreach ($m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_tax_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $master['customerReceivableAutoID'];
        $data_arr['gl_code'] = $master['customerReceivableSystemGLCode'];
        $data_arr['secondary'] = $master['customerReceivableGLAccount'];
        $data_arr['gl_desc'] = $master['customerReceivableDescription'];
        $data_arr['gl_type'] = $master['customerReceivableType'];
        $data_arr['segment_id'] = '';
        $data_arr['segment'] = '';
        $data_arr['gl_dr'] = 0;
        $data_arr['gl_cr'] = $cr_total;
        $data_arr['amount_type'] = 'cr';
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 3;
        $data_arr['subLedgerDesc'] = 'AR';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = null;
        $data_arr['partyAutoID'] = null;
        $data_arr['partySystemCode'] = null;
        $data_arr['partyName'] = null;
        $data_arr['partyCurrencyID'] = null;
        $data_arr['partyCurrency'] = null;
        $data_arr['transactionExchangeRate'] = ($cr_total / $cr_total);
        $data_arr['partyExchangeRate'] = ($cr_total / $party_total);
        $data_arr['companyLocalExchangeRate'] = ($cr_total / $companyLocal_total);
        $data_arr['companyReportingExchangeRate'] = ($cr_total / $companyReporting_total);
        $data_arr['partyCurrencyAmount'] = 0;
        $data_arr['partyCurrencyDecimalPlaces'] = 0;
        array_push($gl_array['gl_detail'], $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'CN';
        $gl_array['name'] = 'Credit Note';
        $gl_array['approved_YN'] = $master['approvedYN'];
        $gl_array['primary_Code'] = $master['creditNoteCode'];
        $gl_array['date'] = $master['creditNoteDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['customername'] = $master['customerNamemaster'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }

    function fetch_double_entry_receipt_voucher_data($receiptVoucherAutoId, $code = null)
    {
        $gl_array = array();
        $inv_total = 0;
        $dr_total = 0;
        $party_total = 0;
        $companyLocal_total = 0;
        $companyReporting_total = 0;
        $tax_total = 0;
        $cn_total = 0;
        $cn_party_total = 0;
        $cn_companyLocal_total = 0;
        $cn_companyReporting_total = 0;
        $gl_array['gl_detail'] = array();
        $this->db->select('srp_erp_customerreceiptmaster.*,srp_erp_customermaster.vatIdNo as vatIdNo,srp_erp_customermaster.customerName as customerNamemaster');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerreceiptmaster.customerID', 'left');
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

        $this->db->select('conversionRateUOM, IFNULL(taxAmount, 0) as taxAmount, receiptVoucherDetailAutoID,detailInvoiceType,GLAutoID,SystemGLCode,GLCode,GLDescription,GLType,transactionAmount ,segmentCode, segmentID,expenseGLAutoID,expenseSystemGLCode,expenseGLCode,expenseGLDescription,expenseGLType,assetGLAutoID,assetSystemGLCode,assetGLCode,assetGLDescription,assetGLType,transactionAmount,segmentCode ,segmentID,type,companyLocalWacAmount,requestedQty,itemCategory,companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,companyLocalAmount,companyReportingAmount,customerAmount,projectID,projectExchangeRate,ifnull(rebateAmount,0) as rebatamnt,project_categoryID,project_subCategoryID');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $detail = $this->db->get('srp_erp_customerreceiptdetail')->result_array();

        //check other than creditnote
        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type !=', 'creditnote');
        $except_creditnote = $this->db->get('srp_erp_customerreceiptdetail')->result_array();
   

        $r1 = "SELECT SUM(rebateAmount) as rebateAmounts,rebateGLAutoID,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.GLDescription,
                        srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
                        FROM
                            `srp_erp_customerreceiptdetail`
                            LEFT JOIN srp_erp_chartofaccounts ON srp_erp_customerreceiptdetail.rebateGLAutoID=srp_erp_chartofaccounts.GLAutoID
                        WHERE
                            `receiptVoucherAutoId` = $receiptVoucherAutoId
                            GROUP BY
                                rebateGLAutoID 
                                HAVING rebateAmounts>0";
        $rebetamnt = $this->db->query($r1)->result_array();

        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode ,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate, supplierCurrencyDecimalPlaces,supplierCurrencyID,transactionExchangeRate,companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,taxMasterAutoID');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $tax_detail = $this->db->get('srp_erp_customerreceipttaxdetails')->result_array();

        $item_tax_group_wise = $this->db->query("SELECT *  FROM(
                    SELECT documentDetailAutoID as auto_id, taxGlAutoID as gl_auto_id, outputVatTransferGL, SUM(amount) as taxAmount, taxMasterID, isAdvance
                    FROM `srp_erp_taxledger`
                    LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                    WHERE documentID = 'RV' AND documentMasterAutoID = $receiptVoucherAutoId GROUP BY taxMasterID, isAdvance
                ) t1
                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id")->result_array();

        $m_arr = array();
        $p_arr = array();
        $i_m_arr = array();
        $i_p_arr = array();
        $item_arr = array();
        $e_p_arr = array();
        $e_m_arr = array();
        $creditNoteAmount = 0;
        for ($i = 0; $i < count($detail); $i++) {
            if ($detail[$i]['type'] == 'Invoice') {
                $inv_total += $detail[$i]['transactionAmount'];
                $party_total += $detail[$i]['customerAmount'];
                $companyLocal_total += $detail[$i]['companyLocalAmount'];
                $companyReporting_total += $detail[$i]['companyReportingAmount'];
            }
            if ($detail[$i]['type'] == 'creditnote' || $detail[$i]['type'] == 'SLR') {
                $cn_total += $detail[$i]['transactionAmount'];
               // $dr_total += $detail[$i]['transactionAmount'];
                $cn_party_total += $detail[$i]['customerAmount'];
                $cn_companyLocal_total += $detail[$i]['companyLocalAmount'];
                $cn_companyReporting_total += $detail[$i]['companyReportingAmount'];
            }

            if($detail[$i]['detailInvoiceType'] == 'SUP'){
                $ex = $detail[$i]['transactionAmount']*-1;
            }else{
                $ex = $detail[$i]['transactionAmount'];
            }

            
      
            if($detail[$i]['type'] == 'EXGL'){
               $ex = $detail[$i]['transactionAmount'] * -1;
            }

            if ($detail[$i]['type'] == 'Advance') {
                $dr_total += $ex;
            } elseif($detail[$i]['type'] != 'creditnote') {
                $dr_total += $ex - $detail[$i]['taxAmount'];
            }
        }

    

        for ($i = 0; $i < count($detail); $i++) {
            if ($detail[$i]['type'] == 'Item' && $detail[$i]['itemCategory'] == 'Inventory') {
                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'];
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
                $data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                if ($detail[$i]['transactionAmount'] <= 0) {
                    $data_arr['gl_dr'] = $detail[$i]['transactionAmount'] - $detail[$i]['taxAmount'];
                    $data_arr['gl_cr'] = 0;
                    array_push($p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $detail[$i]['transactionAmount'] - $detail[$i]['taxAmount'];
                    $data_arr['amount_type'] = 'cr';
                    array_push($m_arr, $data_arr);
                }

                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['expenseGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['expenseSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['expenseGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['expenseGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['expenseGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                $data_arr['gl_dr'] = ((($detail[$i]['companyLocalWacAmount'] / $detail[$i]['conversionRateUOM']) / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                $data_arr['gl_cr'] = 0;
                array_push($item_arr, $data_arr);

                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['assetGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['assetSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['assetGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['assetGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['assetGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = ((($detail[$i]['companyLocalWacAmount'] / $detail[$i]['conversionRateUOM']) / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
                $tax_total += $detail[$i]['transactionAmount'];

            } elseif ($detail[$i]['type'] == 'Advance') {
                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['projectID'] = null;
                $data_arr['project_categoryID'] = null;
                $data_arr['project_subCategoryID'] = null;
                $data_arr['projectExchangeRate'] = null;
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Advance';
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 3;
                $data_arr['subLedgerDesc'] = 'AR';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                // $data_arr['transactionExchangeRate'] = ($dr_total/$dr_total);
                // $data_arr['partyExchangeRate'] = ($dr_total/$party_total);
                // $data_arr['companyLocalExchangeRate'] = ($dr_total/$companyLocal_total);
                // $data_arr['companyReportingExchangeRate'] = ($dr_total/$companyReporting_total);
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                if ($detail[$i]['transactionAmount'] <= 0) {
                    $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $data_arr['gl_cr'] = 0;
                    array_push($i_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $detail[$i]['transactionAmount'];
                    $data_arr['amount_type'] = 'cr';
                    array_push($i_m_arr, $data_arr);
                }
            } elseif ($detail[$i]['type'] == 'Invoice') {

                if($detail[$i]['detailInvoiceType'] == 'SUP'){
                    $detail[$i]['transactionAmount'] = $detail[$i]['transactionAmount'] * -1;
                }

                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['projectID'] = null;
                $data_arr['project_categoryID'] = null;
                $data_arr['project_subCategoryID'] = null;
                $data_arr['projectExchangeRate'] = null;
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Invoice';;
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 3;
                $data_arr['subLedgerDesc'] = 'AR';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = ($inv_total / $inv_total);
                $data_arr['partyExchangeRate'] = ($inv_total / $party_total);
                $data_arr['companyLocalExchangeRate'] = ($inv_total / $companyLocal_total);
                $data_arr['companyReportingExchangeRate'] = ($inv_total / $companyReporting_total);
                // $data_arr['transactionExchangeRate'] = 1;
                // $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                // $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                // $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                if ($detail[$i]['transactionAmount'] <= 0) {
                    $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $data_arr['gl_cr'] = 0;
                    array_push($p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $detail[$i]['transactionAmount'];
                    $data_arr['amount_type'] = 'cr';
                    array_push($m_arr, $data_arr);
                }

            } elseif ($detail[$i]['type'] == 'creditnote' || $detail[$i]['type'] == 'SLR') {
                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['projectID'] = null;
                $data_arr['project_categoryID'] = null;
                $data_arr['project_subCategoryID'] = null;
                $data_arr['projectExchangeRate'] = null;
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Credit Note';;
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 3;
                $data_arr['subLedgerDesc'] = 'AR';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['partyExchangeRate'] = ($cn_total / $cn_party_total);
                $data_arr['companyLocalExchangeRate'] = ($cn_total / $cn_companyLocal_total);
                $data_arr['companyReportingExchangeRate'] = ($cn_total / $cn_companyReporting_total);
                // $data_arr['transactionExchangeRate'] = 1;
                // $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                // $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                // $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';

                if (($detail[$i]['transactionAmount'] * -1) <= 0) {
                    $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $data_arr['gl_cr'] = 0;
                    array_push($e_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $detail[$i]['transactionAmount'];
                    $data_arr['amount_type'] = 'cr';
                    array_push($e_m_arr, $data_arr);
                }

                $creditNoteAmount += $detail[$i]['transactionAmount'];

            } else {

                if($detail[$i]['type'] == 'EXGL'){
                    $detail[$i]['transactionAmount'] = $detail[$i]['transactionAmount'] * -1;
                }

               
                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'];
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
                $data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                if ($detail[$i]['transactionAmount'] <= 0) {
                    $data_arr['gl_dr'] = (($detail[$i]['transactionAmount'] - $detail[$i]['taxAmount']) * -1);
                    $data_arr['gl_cr'] = 0;
                    array_push($i_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $detail[$i]['transactionAmount'] - $detail[$i]['taxAmount'];
                    $data_arr['amount_type'] = 'cr';
                    array_push($i_m_arr, $data_arr);
                }
                $tax_total += $detail[$i]['transactionAmount'];
            }
        }

   

        $p_arr = $this->array_group_sum_tax_pm($p_arr);
        $m_arr = $this->array_group_sum_tax_pm($m_arr);
        $item_arr = $this->array_group_sum_tax_pm($item_arr);

        $gl_array['gl_detail'] = $p_arr;
        foreach ($m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        


        foreach ($item_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($i_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($i_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

  
   
        for ($i = 0; $i < count($tax_detail); $i++) {
            $data_arr['auto_id'] = $tax_detail[$i]['taxDetailAutoID'];
            $data_arr['gl_auto_id'] = $tax_detail[$i]['GLAutoID'];
            $data_arr['gl_code'] = $tax_detail[$i]['SystemGLCode'];
            $data_arr['secondary'] = $tax_detail[$i]['GLCode'];
            $data_arr['gl_desc'] = $tax_detail[$i]['GLDescription'] . ' - Tax All';
            $data_arr['gl_type'] = $tax_detail[$i]['GLType'];
            $data_arr['segment_id'] = $tax_detail[$i]['segmentID'];
            $data_arr['segment'] = $tax_detail[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = $tax_detail[$i]['taxMasterAutoID'];
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['projectID'] = null;
            $data_arr['project_categoryID'] = null;
            $data_arr['project_subCategoryID'] = null;
            $data_arr['projectExchangeRate'] = null;
            $data_arr['partyType'] = 'AUT';
            $data_arr['partyAutoID'] = $tax_detail[$i]['supplierAutoID'];
            $data_arr['partySystemCode'] = $tax_detail[$i]['supplierSystemCode'];
            $data_arr['partyName'] = $tax_detail[$i]['supplierName'];
            $data_arr['partyCurrencyID'] = $tax_detail[$i]['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $tax_detail[$i]['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = $tax_detail[$i]['transactionExchangeRate'];
            $data_arr['companyLocalExchangeRate'] = $tax_detail[$i]['companyLocalExchangeRate'];
            $data_arr['companyReportingExchangeRate'] = $tax_detail[$i]['companyReportingExchangeRate'];
            $data_arr['partyExchangeRate'] = $tax_detail[$i]['supplierCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $tax_detail[$i]['supplierCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = (($tax_detail[$i]['taxPercentage'] / 100) * $tax_total);
            array_push($gl_array['gl_detail'], $data_arr);
            $dr_total += $data_arr['gl_cr'];
        }

        for ($i = 0; $i < count($item_tax_group_wise); $i++) {
            if (!empty($item_tax_group_wise[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_tax_group_wise[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_tax_group_wise[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_tax_group_wise[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_tax_group_wise[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = $master['segmentID'];
                $data_arr_tx_group['segment'] = $master['segmentCode'];
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';

                $data_arr_tx_group['partyAutoID'] = $master['customerID'];
                $data_arr_tx_group['partySystemCode'] = $master['customerSystemCode'];
                $data_arr_tx_group['partyName'] = $master['customerName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['customerCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = 1;
                $data_arr_tx_group['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr_tx_group['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr_tx_group['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = '';
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'cr';
                $data_arr_tx_group['gl_dr'] = 0;
                $data_arr_tx_group['gl_cr'] = $item_tax_group_wise[$i]['taxAmount'];
                array_push($gl_array['gl_detail'], $data_arr_tx_group);

                if($item_tax_group_wise[$i]['isAdvance'] == 0)
                {
                    $dr_total += $data_arr_tx_group['gl_cr'];
                } else {
                    $this->db->select('GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory');
                    $this->db->where('GLAutoID', $item_tax_group_wise[$i]['outputVatTransferGL']);
                    $transferGL_advance = $this->db->get('srp_erp_chartofaccounts')->row_array();

                    $data_arr_tx_group['auto_id'] = $item_tax_group_wise[$i]['auto_id'];
                    $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise[$i]['outputVatTransferGL'];
                    $data_arr_tx_group['gl_code'] = $transferGL_advance['systemAccountCode'];
                    $data_arr_tx_group['secondary'] = $transferGL_advance['GLSecondaryCode'];
                    $data_arr_tx_group['gl_desc'] = $transferGL_advance['GLDescription'];
                    $data_arr_tx_group['gl_type'] = $transferGL_advance['subCategory'];

                    $data_arr_tx_group['segment_id'] = $master['segmentID'];
                    $data_arr_tx_group['segment'] = $master['segmentCode'];
                    $data_arr_tx_group['isAddon'] = 0;
                    $data_arr_tx_group['projectID'] = null;
                    $data_arr_tx_group['project_categoryID'] = null;
                    $data_arr_tx_group['project_subCategoryID'] = null;
                    $data_arr_tx_group['projectExchangeRate'] = null;
                    $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise[$i]['taxMasterID'];
                    $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                    $data_arr_tx_group['subLedgerType'] = null;
                    $data_arr_tx_group['subLedgerDesc'] = null;
                    $data_arr_tx_group['partyContractID'] = null;
                    $data_arr_tx_group['partyType'] = 'AUT';

                    $data_arr_tx_group['partyAutoID'] = $master['customerID'];
                    $data_arr_tx_group['partySystemCode'] = $master['customerSystemCode'];
                    $data_arr_tx_group['partyName'] = $master['customerName'];
                    $data_arr_tx_group['partyCurrencyID'] = $master['customerCurrencyID'];
                    $data_arr_tx_group['partyCurrency'] = $master['customerCurrency'];
                    $data_arr_tx_group['transactionExchangeRate'] = 1;
                    $data_arr_tx_group['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                    $data_arr_tx_group['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                    $data_arr_tx_group['partyExchangeRate'] = $master['customerExchangeRate'];
                    $data_arr_tx_group['partyCurrencyAmount'] = '';
                    $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                    $data_arr_tx_group['amount_type'] = 'dr';
                    $data_arr_tx_group['gl_dr'] = $item_tax_group_wise[$i]['taxAmount'];
                    $data_arr_tx_group['gl_cr'] = 0;
                    array_push($gl_array['gl_detail'], $data_arr_tx_group);
                }
            }
        }

        for ($i = 0; $i < count($rebetamnt); $i++) {
            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $rebetamnt[$i]['rebateGLAutoID'];
            $data_arr['gl_code'] = $rebetamnt[$i]['systemAccountCode'];
            $data_arr['secondary'] = $rebetamnt[$i]['GLSecondaryCode'];
            $data_arr['gl_desc'] = $rebetamnt[$i]['GLDescription'] . ' - Rebate Amount';
            $data_arr['gl_type'] = $rebetamnt[$i]['subCategory'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = null;
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = null;
            $data_arr['partyCurrency'] = null;
            $data_arr['projectID'] = null;
            $data_arr['project_categoryID'] = null;
            $data_arr['project_subCategoryID'] = null;
            $data_arr['projectExchangeRate'] = null;
            $data_arr['transactionExchangeRate'] = 1;
            $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'dr';
            $data_arr['gl_dr'] = $rebetamnt[$i]['rebateAmounts'];
            $data_arr['gl_cr'] = 0;
            array_push($gl_array['gl_detail'], $data_arr);
            $dr_total -= $data_arr['gl_dr'];
        }
        
        // echo '<pre>'; print_r($dr_total); exit;
        $dr_final_total = $dr_total - $creditNoteAmount;

        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $master['RVbankCode'];
        $data_arr['gl_code'] = $master['bankSystemAccountCode'];
        $data_arr['secondary'] = $master['bankGLSecondaryCode'];
        $data_arr['gl_desc'] = $master['RVbank'];
        $data_arr['gl_type'] = $master['RVbankType'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];

        if($dr_total <= 0 && $dr_final_total > 0){
            $data_arr['gl_dr'] = 0;
            //$data_arr['gl_cr'] = $dr_total - ($creditNoteAmount * 2);
            $data_arr['gl_cr'] = $dr_final_total;
            $data_arr['amount_type'] = 'cr';
        }else{

            if($dr_final_total < 0){
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $dr_final_total * -1;
                $data_arr['amount_type'] = 'cr';
            }else{
                $data_arr['gl_dr'] = $dr_final_total;
                $data_arr['gl_cr'] = 0;
                $data_arr['amount_type'] = 'dr';
            }

        }
      
        $data_arr['isAddon'] = 0;
        $data_arr['taxMasterAutoID'] = null;
        $data_arr['partyVatIdNo'] = null;
        $data_arr['subLedgerType'] = 0;
        $data_arr['subLedgerDesc'] = null;
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = 'CUS';
        $data_arr['partyAutoID'] = $master['customerID'];
        $data_arr['partySystemCode'] = $master['customerSystemCode'];
        $data_arr['partyName'] = $master['customerName'];
        $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
        $data_arr['partyCurrency'] = $master['customerCurrency'];
        $data_arr['transactionExchangeRate'] = 1;
        $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
        $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        // $data_arr['transactionExchangeRate'] = ($dr_total/$dr_total);
        // $data_arr['partyExchangeRate'] = ($dr_total/$party_total);
        // $data_arr['companyLocalExchangeRate'] = ($dr_total/$companyLocal_total);
        // $data_arr['companyReportingExchangeRate'] = ($dr_total/$companyReporting_total);
        $data_arr['partyCurrencyAmount'] = 0;
        $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
        array_push($gl_array['gl_detail'], $data_arr);
        
    

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'RV';
        $gl_array['name'] = 'Receipt Voucher';
        $gl_array['primary_Code'] = $master['RVcode'];
        $gl_array['approved_YN'] = $master['approvedYN'];
        $gl_array['date'] = $master['RVdate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['customername'] = $master['customerNamemaster'];
        $gl_array['master_data'] = $master;
        if (!empty($master['customerNamemaster'])) {
            $gl_array['customername'] = $master['customerNamemaster'];
        } else {
            $gl_array['customername'] = $master['customerName'];
        }

        // echo '<pre>';
        // print_r($gl_array); exit;

        return $gl_array;
    }

   
    function fetch_double_entry_customer_invoice_data($invoiceAutoID, $code = null)
    {
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_customerinvoicemaster',$invoiceAutoID,'CINV','invoiceAutoID');
        $gl_array = array();
        $tax_total = 0;
        $cr_total = 0;
        $gl_array['gl_detail'] = array();

        $this->db->select('srp_erp_customerinvoicemaster.*,srp_erp_customermaster.vatIdNo as vatIdNo,srp_erp_customermaster.customerName as customerNamemaster');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where("`type` <> 'DO'");
        $detail = $this->db->get('srp_erp_customerinvoicedetails')->result_array();

        $this->db->select('SUM(transactionAmount) AS do_sum, det.*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where("`type` = 'DO'");
        $this->db->where(" `DODetailsID` is null");
        $detail_do = $this->db->get('srp_erp_customerinvoicedetails det')->row_array();

        if ($detail_do['do_sum'] != null) { //Single entry for Un billed Invoices - [Delivery Order]
            $detail_do['transactionAmount'] = $detail_do['do_sum'];
            $detail[] = $detail_do;
        }
        //DO itemwise SME-3138
        $this->db->select('*,"DO" AS type');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where(" srp_erp_customerinvoicedetails.`type` = 'DO'");
        $this->db->where(" `DODetailsID` is not null");
        $detail_do_itemwise = $this->db->get('srp_erp_customerinvoicedetails')->result_array();
        if($detail_do_itemwise){
            $detail=$detail_do_itemwise;
        }

        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode ,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate, supplierCurrencyDecimalPlaces,supplierCurrencyID,taxMasterAutoID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $tax_detail = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $extraCharge = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('isChargeToExpense', 1);
        $discountIsCharge = $this->db->get('srp_erp_customerinvoicediscountdetails')->result_array();

        $r1 = "SELECT srp_erp_customerinvoicedetails.*,SUM(taxAmount*requestedQty) as tottaxAmount
               FROM `srp_erp_customerinvoicedetails`
               WHERE `invoiceAutoID` = $invoiceAutoID GROUP BY InvoiceAutoID,taxMasterAutoID";
        $item_tax_detail = $this->db->query($r1)->result_array();

        $item_tax_group_wise = $this->db->query("SELECT *  FROM(
            SELECT documentDetailAutoID as auto_id, taxGlAutoID as gl_auto_id, SUM(amount) as taxAmount, taxMasterID
                FROM `srp_erp_taxledger`
                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                where documentID = 'CINV' AND documentMasterAutoID = $invoiceAutoID GROUP BY taxMasterID, taxGlAutoID) t1
            LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id")->result_array();

        $item_transfer_tax_group_wise = $this->db->query("SELECT *  FROM(
            SELECT documentDetailAutoID as auto_id, transferGLAutoID as gl_auto_id, SUM(amount) as taxAmount, taxMasterID
                FROM `srp_erp_taxledger`
                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                where documentID = 'CINV' AND documentMasterAutoID = $invoiceAutoID AND transferGLAutoID IS NOT NULL AND isAdvance = 0 GROUP BY taxMasterID, taxGlAutoID) t1
            LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id")->result_array();

       
        $qry = "SELECT
                        SUM(transactionAmount) as tottransactionAmount,SUM(totalAfterTax) as totalAfterTax
                    FROM
                        srp_erp_customerinvoicedetails
                    WHERE
                        invoiceAutoID = $invoiceAutoID";
        $sumdetail = $this->db->query($qry)->row_array();
        $tottransamnt = $sumdetail['tottransactionAmount'];

        $this->db->select('SUM(transactionAmount) as transactionAmount');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('isTaxApplicable', 1);
        $t_extraCharge = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->row_array();

        if ($t_extraCharge['transactionAmount'] == null) {
            $t_extraCharge['transactionAmount'] = 0;
        }

        $this->db->select('SUM(discountPercentage) as discountPercentage');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $discounttax = $this->db->get('srp_erp_customerinvoicediscountdetails')->row_array();

        if ($discounttax) {
            $discountamnttax = ($tottransamnt * $discounttax['discountPercentage']) / 100;
        }


        $this->db->select('SUM(discountPercentage) as discountPercentage');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('isChargeToExpense', 0);
        $discount = $this->db->get('srp_erp_customerinvoicediscountdetails')->row_array();

        if ($discount) {
            $discountamnt = ($tottransamnt * $discount['discountPercentage']) / 100;
        }

        $cr_p_arr = array();
        $cr_m_arr = array();
        $e_cr_p_arr = array();
        $e_cr_m_arr = array();
        $item_arr = array();
        $item_tax_arr = array();
        $retensionValue = 0;
        for ($i = 0; $i < count($detail); $i++) {
            //if ($detail[$i]['type'] == 'Item') {
            $retensionValue += $detail[$i]['retensionValue']; 

            if ($detail[$i]['type'] == 'Item' || $detail[$i]['type'] == 'Commission') {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['revenueGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['revenueSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['revenueGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['revenueGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['revenueGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
                $data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = null;
                $data_arr['amount_type'] = 'dr';
                //$amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                if ($discount) {
                    if (!empty($tottransamnt) || $tottransamnt != 0) {
                        $linedisc = ($detail[$i]['transactionAmount'] / $tottransamnt) * $discountamnt;
                    } else {
                        $linedisc = ($detail[$i]['transactionAmount']) * $discountamnt;
                    }
                    if($group_based_tax == 1) {
                        $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax'] - $detail[$i]['taxAmount']) - $linedisc;
                    } else {
                        $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']) - $linedisc;
                    }
                } else {
                    if($group_based_tax == 1) {
                        $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax'] - $detail[$i]['taxAmount']);
                    } else {
                        $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                    }
                }
                if ($amount <= 0) {
                    $data_arr['gl_dr'] = $amount;
                    $data_arr['gl_cr'] = 0;
                    array_push($cr_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $amount;
                    $data_arr['amount_type'] = 'cr';
                    array_push($cr_m_arr, $data_arr);
                }

            } else {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['revenueGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['revenueSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['revenueGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['revenueGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['revenueGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
                $data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                if ($detail[$i]['type'] == 'DO') {
                    $data_arr['subLedgerType'] = 5;
                    $data_arr['subLedgerDesc'] = 'UBI';
                } else {
                    $data_arr['subLedgerType'] = 0;
                    $data_arr['subLedgerDesc'] = null;
                }

                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                //$amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                if($detail[$i]['DODetailsID'] == null) {
                    if ($discount) {
                        $linedisc = ($detail[$i]['transactionAmount'] / $tottransamnt) * $discountamnt;
                        if($group_based_tax == 1) {
                            $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax'] - $detail[$i]['taxAmount']) - $linedisc;
                        } else {
                            $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']) - $linedisc;
                        }
                    } else {
                        if($group_based_tax == 1) {
                            $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax'] - $detail[$i]['taxAmount']);
                        } else {
                            $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                        }
                    }
                } else {
                    $amount = $detail[$i]['transactionAmount'];
                }
               
                if ($amount <= 0) {
                    $data_arr['gl_dr'] = ($amount) * -1;
                    $data_arr['gl_cr'] = 0;
                    array_push($e_cr_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $amount;
                    $data_arr['amount_type'] = 'cr';
                    array_push($e_cr_m_arr, $data_arr);
                }
            }
            $cr_total += $data_arr['gl_cr'] - $data_arr['gl_dr'];

            /*if ($detail[$i]['taxAmount'] != 0) {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['taxSupplierliabilityAutoID'];
                $data_arr['gl_code'] = $detail[$i]['taxSupplierliabilitySystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['taxSupplierliabilityGLAccount'];
                $data_arr['gl_desc'] = $detail[$i]['taxSupplierliabilityDescription'];
                $data_arr['gl_type'] = $detail[$i]['taxSupplierliabilityType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = null;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'AUT';
                $data_arr['partyAutoID'] = $detail[$i]['taxSupplierAutoID'];
                $data_arr['partySystemCode'] = $detail[$i]['taxSupplierSystemCode'];
                $data_arr['partyName'] = $detail[$i]['taxSupplierName'];
                $data_arr['partyCurrencyID'] = $detail[$i]['taxSupplierCurrencyID'];
                $data_arr['partyCurrency'] = $detail[$i]['taxSupplierCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $detail[$i]['taxSupplierCurrencyExchangeRate'];
                $data_arr['partyCurrencyAmount'] = $detail[$i]['taxSupplierliabilityType'];
                $data_arr['partyCurrencyDecimalPlaces'] = $detail[$i]['taxSupplierCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = ($detail[$i]['taxAmount'] * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
                $cr_total += $data_arr['gl_cr'];
            }*/

            //if ($detail[$i]['type'] == 'Item' && $detail[$i]['itemCategory'] == 'Inventory') {
            if (($detail[$i]['type'] == 'Item' || $detail[$i]['type'] == 'Commission')  && $detail[$i]['itemCategory'] == 'Inventory') {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['expenseGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['expenseSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['expenseGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['expenseGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['expenseGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
                $data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                $data_arr['gl_dr'] = ((($detail[$i]['companyLocalWacAmount'] / $detail[$i]['conversionRateUOM']) / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                $data_arr['gl_cr'] = 0;
                array_push($item_arr, $data_arr);
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['assetGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['assetSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['assetGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['assetGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['assetGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
                $data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = ((($detail[$i]['companyLocalWacAmount'] / $detail[$i]['conversionRateUOM']) / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
            }

            if($group_based_tax == 1) {
                $tax_total += ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax'] - $detail[$i]['taxAmount']);
            } else {
                $tax_total += ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
            }
        }

        for ($i = 0; $i < count($item_tax_detail); $i++) {
            if (!empty($item_tax_detail[$i]['taxMasterAutoID'])) {
                $data_arr_tx['auto_id'] = $item_tax_detail[$i]['invoiceDetailsAutoID'];
                $data_arr_tx['gl_auto_id'] = $item_tax_detail[$i]['taxSupplierliabilityAutoID'];
                $data_arr_tx['gl_code'] = $item_tax_detail[$i]['taxSupplierliabilitySystemGLCode'];
                $data_arr_tx['secondary'] = $item_tax_detail[$i]['taxSupplierliabilityGLAccount'];
                $data_arr_tx['gl_desc'] = $item_tax_detail[$i]['taxSupplierliabilityDescription'];
                $data_arr_tx['gl_type'] = $item_tax_detail[$i]['taxSupplierliabilityType'];
                $data_arr_tx['segment_id'] = $master['segmentID'];
                $data_arr_tx['segment'] = $master['segmentCode'];
                $data_arr_tx['isAddon'] = 0;
                $data_arr_tx['projectID'] = null;
                $data_arr_tx['project_categoryID'] = null;
                $data_arr_tx['project_subCategoryID'] = null;
                $data_arr_tx['projectExchangeRate'] = null;
                $data_arr_tx['taxMasterAutoID'] = $item_tax_detail[$i]['taxMasterAutoID'];
                $data_arr_tx['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx['subLedgerType'] = null;
                $data_arr_tx['subLedgerDesc'] = null;
                $data_arr_tx['partyContractID'] = null;
                $data_arr_tx['partyType'] = 'AUT';
                $data_arr_tx['partyAutoID'] = $item_tax_detail[$i]['taxSupplierAutoID'];
                $data_arr_tx['partySystemCode'] = $item_tax_detail[$i]['taxSupplierSystemCode'];
                $data_arr_tx['partyName'] = $item_tax_detail[$i]['taxSupplierName'];
                $data_arr_tx['partyCurrencyID'] = $item_tax_detail[$i]['taxSupplierCurrencyID'];
                $data_arr_tx['partyCurrency'] = $item_tax_detail[$i]['taxSupplierCurrency'];
                $data_arr_tx['transactionExchangeRate'] = null;
                $data_arr_tx['companyLocalExchangeRate'] = null;
                $data_arr_tx['companyReportingExchangeRate'] = null;
                $data_arr_tx['partyExchangeRate'] = $item_tax_detail[$i]['taxSupplierCurrencyExchangeRate'];
                $data_arr_tx['partyCurrencyAmount'] = $item_tax_detail[$i]['taxSupplierliabilityType'];
                $data_arr_tx['partyCurrencyDecimalPlaces'] = $item_tax_detail[$i]['taxSupplierCurrencyDecimalPlaces'];
                $data_arr_tx['amount_type'] = 'cr';
                $data_arr_tx['gl_dr'] = 0;
                $data_arr_tx['gl_cr'] = $item_tax_detail[$i]['tottaxAmount'];
                array_push($item_tax_arr, $data_arr_tx);
                $cr_total += $data_arr_tx['gl_cr'];
            }
        }
      
        for ($i = 0; $i < count($item_tax_group_wise); $i++) {
            if (!empty($item_tax_group_wise[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_tax_group_wise[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_tax_group_wise[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_tax_group_wise[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_tax_group_wise[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = $master['segmentID'];
                $data_arr_tx_group['segment'] = $master['segmentCode'];
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = $master['customerID'];
                $data_arr_tx_group['partySystemCode'] = $master['customerSystemCode'];
                $data_arr_tx_group['partyName'] = $master['customerName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['customerCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = null;
                $data_arr_tx_group['companyLocalExchangeRate'] = null;
                $data_arr_tx_group['companyReportingExchangeRate'] = null;
                $data_arr_tx_group['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = 0;
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'cr';
                $data_arr_tx_group['gl_dr'] = 0;
                $data_arr_tx_group['gl_cr'] = $item_tax_group_wise[$i]['taxAmount'];
                array_push($item_tax_arr, $data_arr_tx_group);

               // if ($master['invoiceType'] != 'DeliveryOrder') {
                    $cr_total += $data_arr_tx_group['gl_cr'];
               // }
            }
        }

        for ($i = 0; $i < count($item_transfer_tax_group_wise); $i++) {
            if (!empty($item_transfer_tax_group_wise[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_transfer_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_transfer_tax_group_wise[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_transfer_tax_group_wise[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_transfer_tax_group_wise[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_transfer_tax_group_wise[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_transfer_tax_group_wise[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = $master['segmentID'];
                $data_arr_tx_group['segment'] = $master['segmentCode'];
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_transfer_tax_group_wise[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = $master['customerID'];
                $data_arr_tx_group['partySystemCode'] = $master['customerSystemCode'];
                $data_arr_tx_group['partyName'] = $master['customerName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['customerCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = null;
                $data_arr_tx_group['companyLocalExchangeRate'] = null;
                $data_arr_tx_group['companyReportingExchangeRate'] = null;
                $data_arr_tx_group['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = 0;
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'Dr';
                $data_arr_tx_group['gl_cr'] = 0;
                $data_arr_tx_group['gl_dr'] = $item_transfer_tax_group_wise[$i]['taxAmount'];
                array_push($item_tax_arr, $data_arr_tx_group);

               // if ($master['invoiceType'] != 'DeliveryOrder') {
                    $cr_total += $data_arr_tx_group['gl_cr'];
             //   }
            }
        }

        for ($i = 0; $i < count($tax_detail); $i++) {
            $data_arr['auto_id'] = $tax_detail[$i]['taxDetailAutoID'];
            $data_arr['gl_auto_id'] = $tax_detail[$i]['GLAutoID'];
            $data_arr['gl_code'] = $tax_detail[$i]['SystemGLCode'];
            $data_arr['secondary'] = $tax_detail[$i]['GLCode'];
            $data_arr['gl_desc'] = $tax_detail[$i]['GLDescription'] . ' - Tax All';
            $data_arr['gl_type'] = $tax_detail[$i]['GLType'];
            $data_arr['segment_id'] = $tax_detail[$i]['segmentID'];
            $data_arr['projectID'] = null;
            $data_arr['project_categoryID'] = null;
            $data_arr['project_subCategoryID'] = null;
            $data_arr['project_subCategoryID'] = null;
            $data_arr['projectExchangeRate'] = null;
            $data_arr['segment'] = $tax_detail[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = $tax_detail[$i]['taxMasterAutoID'];
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'AUT';
            $data_arr['partyAutoID'] = $tax_detail[$i]['supplierAutoID'];
            $data_arr['partySystemCode'] = $tax_detail[$i]['supplierSystemCode'];
            $data_arr['partyName'] = $tax_detail[$i]['supplierName'];
            $data_arr['partyCurrencyID'] = $tax_detail[$i]['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $tax_detail[$i]['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $tax_detail[$i]['supplierCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $tax_detail[$i]['supplierCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            if ($discounttax) {
                $data_arr['gl_cr'] = (($tax_detail[$i]['taxPercentage'] / 100) * (($tax_total - $discountamnttax) + $t_extraCharge['transactionAmount']));
            } else {
                $data_arr['gl_cr'] = (($tax_detail[$i]['taxPercentage'] / 100) * ($tax_total + $t_extraCharge['transactionAmount']));
            }
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total += $data_arr['gl_cr'];
        }

        for ($i = 0; $i < count($extraCharge); $i++) {
            $data_arr['auto_id'] = $extraCharge[$i]['extraChargeDetailID'];
            $data_arr['gl_auto_id'] = $extraCharge[$i]['GLAutoID'];
            $data_arr['gl_code'] = $extraCharge[$i]['systemGLCode'];
            $data_arr['secondary'] = $extraCharge[$i]['GLCode'];
            $data_arr['gl_desc'] = $extraCharge[$i]['GLDescription'] . ' - Extra Charge';
            $data_arr['gl_type'] = $extraCharge[$i]['GLType'];
            $data_arr['segment_id'] = $extraCharge[$i]['segmentID'];
            $data_arr['segment'] = $extraCharge[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['projectID'] = null;
            $data_arr['project_categoryID'] = null;
            $data_arr['project_subCategoryID'] = null;
            $data_arr['projectExchangeRate'] = null;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $extraCharge[$i]['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = $extraCharge[$i]['transactionAmount'];
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total += $data_arr['gl_cr'];
        }

        for ($i = 0; $i < count($discountIsCharge); $i++) {
            $data_arr['auto_id'] = $discountIsCharge[$i]['discountDetailID'];
            $data_arr['gl_auto_id'] = $discountIsCharge[$i]['GLAutoID'];
            $data_arr['gl_code'] = $discountIsCharge[$i]['systemGLCode'];
            $data_arr['secondary'] = $discountIsCharge[$i]['GLCode'];
            $data_arr['gl_desc'] = $discountIsCharge[$i]['GLDescription'] . ' - Discount';
            $data_arr['gl_type'] = $discountIsCharge[$i]['GLType'];
            $data_arr['segment_id'] = $discountIsCharge[$i]['segmentID'];
            $data_arr['segment'] = $discountIsCharge[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['projectID'] = null;
            $data_arr['project_categoryID'] = null;
            $data_arr['project_subCategoryID'] = null;
            $data_arr['projectExchangeRate'] = null;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $discountIsCharge[$i]['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'dr';
            $data_arr['gl_dr'] = ($discountIsCharge[$i]['discountPercentage'] * $tottransamnt) / 100;
            $data_arr['gl_cr'] = 0;
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total -= ($discountIsCharge[$i]['discountPercentage'] * $tottransamnt) / 100;
        }


        /* $cr_p_arr = $this->array_group_sum_tax($cr_p_arr);
         $cr_m_arr = $this->array_group_sum_tax($cr_m_arr);
         $item_arr = $this->array_group_sum_tax($item_arr);*/
        $cr_p_arr = $this->array_group_sum_tax_pm($cr_p_arr);
        $cr_m_arr = $this->array_group_sum_tax_pm($cr_m_arr);
        $item_arr = $this->array_group_sum_tax_pm($item_arr);
        foreach ($cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        foreach ($item_tax_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
   
        if ($master['rebateAmount'] > 0) {
            if ($master['retentionPercentage'] > 0) {
                $retamnt = $cr_total * ($master['retentionPercentage'] / 100);
                $cr_total = $cr_total - $retamnt;
            }
            $cr_total = $cr_total - $master['rebateAmount'];

            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $master['customerReceivableAutoID'];
            $data_arr['gl_code'] = $master['customerReceivableSystemGLCode'];
            $data_arr['secondary'] = $master['customerReceivableGLAccount'];
            $data_arr['gl_desc'] = $master['customerReceivableDescription'];
            $data_arr['gl_type'] = $master['customerReceivableType'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = $cr_total;
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = 3;
            $data_arr['subLedgerDesc'] = 'AR';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            array_push($gl_array['gl_detail'], $data_arr);

            /** Rebate Account Entry */
            $this->db->select('GLAutoID, systemAccountCode AS SystemGLCode, GLSecondaryCode AS GLCode, GLDescription, subCategory AS GLType');
            $this->db->where('GLAutoID', $master['rebateGLAutoID']);
            $rebate_GL = $this->db->get('srp_erp_chartofaccounts')->row_array();

            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $rebate_GL['GLAutoID'];
            $data_arr['gl_code'] = $rebate_GL['SystemGLCode'];
            $data_arr['secondary'] = $rebate_GL['GLCode'];
            $data_arr['gl_desc'] = $rebate_GL['GLDescription'];
            $data_arr['gl_type'] = $rebate_GL['GLType'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = $master['rebateAmount'];
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            array_push($gl_array['gl_detail'], $data_arr);

        } else {

          
            if($master['retentionPercentage'] > 0){
        
                $retamnt = $retensionValue; //$cr_total * ($master['retentionPercentage'] / 100);
                
                $retensionGL = $master['retensionGL'];

                $GL_retension_record = $this->db->where('GLAutoID',$retensionGL)->from('srp_erp_chartofaccounts')->get()->row_array();

                $cr_ret_total = $retamnt;
                $data_arr['auto_id'] = 0;
                $data_arr['gl_auto_id'] = $GL_retension_record['GLAutoID'];
                $data_arr['gl_code'] = $GL_retension_record['systemAccountCode'];
                $data_arr['secondary'] = $GL_retension_record['GLSecondaryCode'];
                $data_arr['gl_desc'] = $GL_retension_record['GLDescription'];
                $data_arr['gl_type'] = $GL_retension_record['masterCategory'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['gl_dr'] = $cr_ret_total;
                $data_arr['gl_cr'] = 0;
                $data_arr['amount_type'] = 'dr';
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 3;
                $data_arr['subLedgerDesc'] = 'AR';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                array_push($gl_array['gl_detail'], $data_arr);


            }

           
            if ($master['retentionPercentage'] > 0) {
                // $retamnt = $cr_total * ($master['retentionPercentage'] / 100);
                $cr_total = $cr_total - $cr_ret_total;
            }
            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $master['customerReceivableAutoID'];
            $data_arr['gl_code'] = $master['customerReceivableSystemGLCode'];
            $data_arr['secondary'] = $master['customerReceivableGLAccount'];
            $data_arr['gl_desc'] = $master['customerReceivableDescription'];
            $data_arr['gl_type'] = $master['customerReceivableType'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = $cr_total;
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = 3;
            $data_arr['subLedgerDesc'] = 'AR';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            array_push($gl_array['gl_detail'], $data_arr);
        }

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['approved_yn'] = $master['approvedYN'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'CINV';
        $gl_array['name'] = 'Customer Invoice';
        $gl_array['primary_Code'] = $master['invoiceCode'];
        $gl_array['date'] = $master['invoiceDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['customername'] = $master['customerNamemaster'];

       

        return $gl_array;
    }


    function fetch_double_entry_mfq_customer_invoice_data($invoiceAutoID, $code = null)
    {
        $gl_array = array();
        $tax_total = 0;
        $cr_total = 0;
        $gl_array['gl_detail'] = array();
        $this->db->select('srp_erp_customerinvoicemaster.*,srp_erp_customermaster.vatIdNo as vatIdNo');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $detail = $this->db->get('srp_erp_customerinvoicedetails')->result_array();

        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode ,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate, supplierCurrencyDecimalPlaces,supplierCurrencyID,taxMasterAutoID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $tax_detail = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();

        $r1 = "SELECT 
                    srp_erp_customerinvoicedetails.*,SUM(taxAmount*requestedQty) as tottaxAmount
                FROM
                    `srp_erp_customerinvoicedetails`
                WHERE
                    `invoiceAutoID` = $invoiceAutoID
                GROUP BY
                    InvoiceAutoID,taxMasterAutoID";
        $item_tax_detail = $this->db->query($r1)->result_array();

        $item_tax_group_wise = $this->db->query("SELECT *  
                                        FROM(
                                            SELECT documentDetailAutoID as auto_id, taxGlAutoID as gl_auto_id, SUM(amount) as taxAmount, taxMasterID
                                            FROM `srp_erp_taxledger`
                                            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                            where documentID = 'CINV' AND documentMasterAutoID = $invoiceAutoID GROUP BY taxMasterID, taxGlAutoID
                                        ) t1
                                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id")->result_array();

        $item_transfer_tax_group_wise = $this->db->query("SELECT *  
                                                        FROM(
                                                            SELECT documentDetailAutoID as auto_id, transferGLAutoID as gl_auto_id, SUM(amount) as taxAmount, taxMasterID
                                                            FROM `srp_erp_taxledger`
                                                            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                                            where documentID = 'CINV' AND documentMasterAutoID = $invoiceAutoID AND transferGLAutoID IS NOT NULL AND isAdvance = 0 GROUP BY taxMasterID, taxGlAutoID
                                                        ) t1
                                                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id")->result_array();

        $cr_p_arr = array();
        $cr_m_arr = array();
        $e_cr_p_arr = array();
        $e_cr_m_arr = array();
        $item_arr = array();
        $item_tax_arr = array();
        for ($i = 0; $i < count($detail); $i++) {
            if ($detail[$i]['type'] == 'Item') {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['revenueGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['revenueSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['revenueGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['revenueGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['revenueGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = null;
                $data_arr['amount_type'] = 'dr';
                $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax'] - $detail[$i]['taxAmount']);
                if ($amount <= 0) {
                    $data_arr['gl_dr'] = $amount;
                    $data_arr['gl_cr'] = 0;
                    array_push($cr_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $amount;
                    $data_arr['amount_type'] = 'cr';
                    array_push($cr_m_arr, $data_arr);
                }
            } else {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['revenueGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['revenueSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['revenueGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['revenueGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['revenueGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax'] - $detail[$i]['taxAmount']);
                if ($amount <= 0) {
                    $data_arr['gl_dr'] = $amount;
                    $data_arr['gl_cr'] = 0;
                    array_push($e_cr_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $amount;
                    $data_arr['amount_type'] = 'cr';
                    array_push($e_cr_m_arr, $data_arr);
                }
            }
            $cr_total += $data_arr['gl_cr'];

            /*if ($detail[$i]['taxAmount'] != 0) {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['taxSupplierliabilityAutoID'];
                $data_arr['gl_code'] = $detail[$i]['taxSupplierliabilitySystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['taxSupplierliabilityGLAccount'];
                $data_arr['gl_desc'] = $detail[$i]['taxSupplierliabilityDescription'];
                $data_arr['gl_type'] = $detail[$i]['taxSupplierliabilityType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = null;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'AUT';
                $data_arr['partyAutoID'] = $detail[$i]['taxSupplierAutoID'];
                $data_arr['partySystemCode'] = $detail[$i]['taxSupplierSystemCode'];
                $data_arr['partyName'] = $detail[$i]['taxSupplierName'];
                $data_arr['partyCurrencyID'] = $detail[$i]['taxSupplierCurrencyID'];
                $data_arr['partyCurrency'] = $detail[$i]['taxSupplierCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $detail[$i]['taxSupplierCurrencyExchangeRate'];
                $data_arr['partyCurrencyAmount'] = $detail[$i]['taxSupplierliabilityType'];
                $data_arr['partyCurrencyDecimalPlaces'] = $detail[$i]['taxSupplierCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = ($detail[$i]['taxAmount'] * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
                $cr_total += $data_arr['gl_cr'];
            }*/

            if ($detail[$i]['type'] == 'Item' && ($detail[$i]['itemCategory'] == 'Inventory' || $detail[$i]['itemCategory'] == 'Service' || $detail[$i]['itemCategory'] == 'Non Inventory')) {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['expenseGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['expenseSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['expenseGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['expenseGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['expenseGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                $data_arr['gl_dr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                $data_arr['gl_cr'] = 0;
                array_push($item_arr, $data_arr);
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['assetGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['assetSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['assetGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['assetGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['assetGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
            }
            $tax_total += ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax'] - $detail[$i]['taxAmount']);
        }

        for ($i = 0; $i < count($item_tax_detail); $i++) {
            if (!empty($item_tax_detail[$i]['taxMasterAutoID'])) {
                $data_arr_tx['auto_id'] = $item_tax_detail[$i]['invoiceDetailsAutoID'];
                $data_arr_tx['gl_auto_id'] = $item_tax_detail[$i]['taxSupplierliabilityAutoID'];
                $data_arr_tx['gl_code'] = $item_tax_detail[$i]['taxSupplierliabilitySystemGLCode'];
                $data_arr_tx['secondary'] = $item_tax_detail[$i]['taxSupplierliabilityGLAccount'];
                $data_arr_tx['gl_desc'] = $item_tax_detail[$i]['taxSupplierliabilityDescription'];
                $data_arr_tx['gl_type'] = $item_tax_detail[$i]['taxSupplierliabilityType'];
                $data_arr_tx['segment_id'] = $master['segmentID'];
                $data_arr_tx['segment'] = $master['segmentCode'];
                $data_arr_tx['isAddon'] = 0;
                $data_arr_tx['taxMasterAutoID'] = $item_tax_detail[$i]['taxMasterAutoID'];
                $data_arr_tx['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx['subLedgerType'] = null;
                $data_arr_tx['subLedgerDesc'] = null;
                $data_arr_tx['partyContractID'] = null;
                $data_arr_tx['partyType'] = 'AUT';
                $data_arr_tx['partyAutoID'] = $item_tax_detail[$i]['taxSupplierAutoID'];
                $data_arr_tx['partySystemCode'] = $item_tax_detail[$i]['taxSupplierSystemCode'];
                $data_arr_tx['partyName'] = $item_tax_detail[$i]['taxSupplierName'];
                $data_arr_tx['partyCurrencyID'] = $item_tax_detail[$i]['taxSupplierCurrencyID'];
                $data_arr_tx['partyCurrency'] = $item_tax_detail[$i]['taxSupplierCurrency'];
                $data_arr_tx['transactionExchangeRate'] = null;
                $data_arr_tx['companyLocalExchangeRate'] = null;
                $data_arr_tx['companyReportingExchangeRate'] = null;
                $data_arr_tx['partyExchangeRate'] = $item_tax_detail[$i]['taxSupplierCurrencyExchangeRate'];
                $data_arr_tx['partyCurrencyAmount'] = $item_tax_detail[$i]['taxSupplierliabilityType'];
                $data_arr_tx['partyCurrencyDecimalPlaces'] = $item_tax_detail[$i]['taxSupplierCurrencyDecimalPlaces'];
                $data_arr_tx['amount_type'] = 'cr';
                $data_arr_tx['gl_dr'] = 0;
                $data_arr_tx['gl_cr'] = $item_tax_detail[$i]['tottaxAmount'];
                array_push($item_tax_arr, $data_arr_tx);
                $cr_total += $data_arr_tx['gl_cr'];
            }
        }

        for ($i = 0; $i < count($item_tax_group_wise); $i++) {
            if (!empty($item_tax_group_wise[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_tax_group_wise[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_tax_group_wise[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_tax_group_wise[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_tax_group_wise[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = $master['segmentID'];
                $data_arr_tx_group['segment'] = $master['segmentCode'];
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = $master['customerID'];
                $data_arr_tx_group['partySystemCode'] = $master['customerSystemCode'];
                $data_arr_tx_group['partyName'] = $master['customerName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['customerCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = null;
                $data_arr_tx_group['companyLocalExchangeRate'] = null;
                $data_arr_tx_group['companyReportingExchangeRate'] = null;
                $data_arr_tx_group['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = 0;
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'cr';
                $data_arr_tx_group['gl_dr'] = 0;
                $data_arr_tx_group['gl_cr'] = $item_tax_group_wise[$i]['taxAmount'];
                array_push($item_tax_arr, $data_arr_tx_group);

                if ($master['invoiceType'] != 'DeliveryOrder') {
                    $cr_total += $data_arr_tx_group['gl_cr'];
                }
            }
        }

        for ($i = 0; $i < count($item_transfer_tax_group_wise); $i++) {
            if (!empty($item_transfer_tax_group_wise[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_transfer_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_transfer_tax_group_wise[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_transfer_tax_group_wise[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_transfer_tax_group_wise[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_transfer_tax_group_wise[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_transfer_tax_group_wise[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = $master['segmentID'];
                $data_arr_tx_group['segment'] = $master['segmentCode'];
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_transfer_tax_group_wise[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = $master['customerID'];
                $data_arr_tx_group['partySystemCode'] = $master['customerSystemCode'];
                $data_arr_tx_group['partyName'] = $master['customerName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['customerCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = null;
                $data_arr_tx_group['companyLocalExchangeRate'] = null;
                $data_arr_tx_group['companyReportingExchangeRate'] = null;
                $data_arr_tx_group['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = 0;
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'Dr';
                $data_arr_tx_group['gl_cr'] = 0;
                $data_arr_tx_group['gl_dr'] = $item_transfer_tax_group_wise[$i]['taxAmount'];
                array_push($item_tax_arr, $data_arr_tx_group);

                if ($master['invoiceType'] != 'DeliveryOrder') {
                    $cr_total += $data_arr_tx_group['gl_cr'];
                }
            }
        }

        for ($i = 0; $i < count($tax_detail); $i++) {
            $data_arr['auto_id'] = $tax_detail[$i]['taxDetailAutoID'];
            $data_arr['gl_auto_id'] = $tax_detail[$i]['GLAutoID'];
            $data_arr['gl_code'] = $tax_detail[$i]['SystemGLCode'];
            $data_arr['secondary'] = $tax_detail[$i]['GLCode'];
            $data_arr['gl_desc'] = $tax_detail[$i]['GLDescription'] . ' - Tax All';
            $data_arr['gl_type'] = $tax_detail[$i]['GLType'];
            $data_arr['segment_id'] = $tax_detail[$i]['segmentID'];
            $data_arr['segment'] = $tax_detail[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = $tax_detail[$i]['taxMasterAutoID'];
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'AUT';
            $data_arr['partyAutoID'] = $tax_detail[$i]['supplierAutoID'];
            $data_arr['partySystemCode'] = $tax_detail[$i]['supplierSystemCode'];
            $data_arr['partyName'] = $tax_detail[$i]['supplierName'];
            $data_arr['partyCurrencyID'] = $tax_detail[$i]['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $tax_detail[$i]['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $tax_detail[$i]['supplierCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $tax_detail[$i]['supplierCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = (($tax_detail[$i]['taxPercentage'] / 100) * $tax_total);
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total += $data_arr['gl_cr'];
        }

        $cr_p_arr = $this->array_group_sum_tax($cr_p_arr);
        $cr_m_arr = $this->array_group_sum_tax($cr_m_arr);
        $item_arr = $this->array_group_sum_tax($item_arr);
        foreach ($cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        foreach ($item_tax_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }


        if ($master['rebateAmount'] > 0) {
            $cr_total = $cr_total - $master['rebateAmount'];

            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $master['customerReceivableAutoID'];
            $data_arr['gl_code'] = $master['customerReceivableSystemGLCode'];
            $data_arr['secondary'] = $master['customerReceivableGLAccount'];
            $data_arr['gl_desc'] = $master['customerReceivableDescription'];
            $data_arr['gl_type'] = $master['customerReceivableType'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = $cr_total;
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = 3;
            $data_arr['subLedgerDesc'] = 'AR';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            array_push($gl_array['gl_detail'], $data_arr);

            /** Rebate Account Entry */
            $this->db->select('GLAutoID, systemAccountCode AS SystemGLCode, GLSecondaryCode AS GLCode, GLDescription, subCategory AS GLType');
            $this->db->where('GLAutoID', $master['rebateGLAutoID']);
            $rebate_GL = $this->db->get('srp_erp_chartofaccounts')->row_array();

            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $rebate_GL['GLAutoID'];
            $data_arr['gl_code'] = $rebate_GL['SystemGLCode'];
            $data_arr['secondary'] = $rebate_GL['GLCode'];
            $data_arr['gl_desc'] = $rebate_GL['GLDescription'];
            $data_arr['gl_type'] = $rebate_GL['GLType'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = $master['rebateAmount'];
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = 3;
            $data_arr['subLedgerDesc'] = 'AR';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            array_push($gl_array['gl_detail'], $data_arr);

        } else {
            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $master['customerReceivableAutoID'];
            $data_arr['gl_code'] = $master['customerReceivableSystemGLCode'];
            $data_arr['secondary'] = $master['customerReceivableGLAccount'];
            $data_arr['gl_desc'] = $master['customerReceivableDescription'];
            $data_arr['gl_type'] = $master['customerReceivableType'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = $cr_total;
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = 3;
            $data_arr['subLedgerDesc'] = 'AR';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            array_push($gl_array['gl_detail'], $data_arr);
        }

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['approved_yn'] = $master['approvedYN'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'CINV';
        $gl_array['name'] = 'Customer Invoice';
        $gl_array['primary_Code'] = $master['invoiceCode'];
        $gl_array['date'] = $master['invoiceDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }


    function fetch_double_entry_asset_master($faId, $code = null)
    {
        $gl_array = array();
        $dr_total = 0;
        $cr_total = 0;
        $gl_array['gl_detail'] = array();

        $this->db->select('*');
        $this->db->where('faID', $faId);
        $detail = $this->db->get('srp_erp_fa_asset_master')->row_array();

        $array = array();

        if ($detail['assetType'] == 1) {

            for ($x = 1; $x <= 2; $x++) {
                if ($x == 1) { /*postGL*/
                    $GL = fetch_gl_account_desc($detail['postGLAutoID']);
                } else { /*CostGl*/
                    $GL = fetch_gl_account_desc($detail['costGLAutoID']);
                }

                $data_arr['auto_id'] = $detail['faID'];
                $data_arr['gl_auto_id'] = $detail['postGLAutoID'];
                $data_arr['gl_code'] = $GL['systemAccountCode'];
                $data_arr['gl_secondary'] = $GL['GLSecondaryCode'];
                $data_arr['gl_desc'] = $GL['GLDescription'];
                $data_arr['gl_type'] = $GL['subCategory'];
                $data_arr['segment_id'] = $detail['segmentID'];
                $data_arr['segment'] = $detail['segmentCode'];
                if ($x == 1) {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = number_format($detail['transactionAmount'], $detail['transactionCurrencyDecimalPlaces']);
                    $data_arr['amount_type'] = 'cr';
                    $cr_total += $detail['transactionAmount'];
                } else {
                    $data_arr['gl_dr'] = number_format($detail['transactionAmount'], $detail['transactionCurrencyDecimalPlaces']);
                    $data_arr['gl_cr'] = 0;
                    $data_arr['amount_type'] = 'dr';
                    $dr_total += $detail['transactionAmount'];
                }
                array_push($array, $data_arr);
            }
        }

        $gl_array['code'] = 'FA';
        $gl_array['name'] = 'Asset Master';
        $gl_array['currency'] = $detail['transactionCurrency'];
        $gl_array['decimal_places'] = $detail['transactionCurrencyDecimalPlaces'];
        $gl_array['primary_Code'] = $detail['faCode'];
        $gl_array['date'] = $detail['postDate'];
        $gl_array['master_data'] = $array;
        $gl_array['dr_total'] = $dr_total;
        $gl_array['cr_total'] = $cr_total;
        $gl_array['approved'] = $detail['approvedYN'];

        return $gl_array;
    }

    function fetch_double_entry_asset_depreciation_master($depMasterAutoID, $code = null)
    {
        $gl_array = array();
        $dr_total = 0;
        $cr_total = 0;
        /*$gl_array['gl_detail'] = array();*/

        $this->db->select('*');
        $this->db->where('depMasterAutoID', $depMasterAutoID);
        $master = $this->db->get('srp_erp_fa_depmaster')->row_array();

        $array = array();

        $detailsDr = $this->db->query("SELECT srp_erp_fa_depmaster.depCode, srp_erp_fa_depmaster.depDate, srp_erp_fa_depmaster.depMonthYear, srp_erp_fa_depmaster.FYBegin, srp_erp_fa_depmaster.FYEnd, srp_erp_fa_depmaster.FYPeriodDateFrom, srp_erp_fa_depmaster.FYPeriodDateTo, srp_erp_fa_assetdepreciationperiods.DepreciationPeriodsID, srp_erp_fa_assetdepreciationperiods.faFinanceCatID, srp_erp_fa_assetdepreciationperiods.faID, srp_erp_fa_assetdepreciationperiods.faMainCategory, srp_erp_fa_assetdepreciationperiods.faSubCategory, srp_erp_fa_assetdepreciationperiods.faCode, srp_erp_fa_assetdepreciationperiods.assetDescription, srp_erp_fa_assetdepreciationperiods.transactionCurrency, srp_erp_fa_assetdepreciationperiods.transactionExchangeRate, SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS transactionAmount, srp_erp_fa_assetdepreciationperiods.transactionCurrencyDecimalPlaces, srp_erp_fa_assetdepreciationperiods.companyLocalCurrency, srp_erp_fa_assetdepreciationperiods.companyLocalExchangeRate, SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS companyLocalAmount, srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces, srp_erp_fa_assetdepreciationperiods.companyReportingCurrency, srp_erp_fa_assetdepreciationperiods.companyReportingExchangeRate, SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS companyReportingAmount, srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyDecimalPlaces, srp_erp_fa_depmaster.documentID, srp_erp_fa_depmaster.serialNo, srp_erp_fa_depmaster.depMasterAutoID, srp_erp_fa_asset_master.ACCDEPGLCODE, srp_erp_fa_asset_master.DEPGLCODE, srp_erp_fa_asset_master.ACCDEPGLAutoID, srp_erp_fa_asset_master.DEPGLAutoID, srp_erp_fa_assetdepreciationperiods.segmentID, srp_erp_fa_assetdepreciationperiods.segmentCode, srp_erp_fa_depmaster.approvedbyEmpName, srp_erp_fa_depmaster.approvedbyEmpID, srp_erp_fa_depmaster.approvedDate, srp_erp_fa_depmaster.confirmedByEmpID, srp_erp_fa_depmaster.confirmedByName, srp_erp_fa_depmaster.confirmedDate FROM srp_erp_fa_assetdepreciationperiods INNER JOIN srp_erp_fa_depmaster ON srp_erp_fa_depmaster.depMasterAutoID = srp_erp_fa_assetdepreciationperiods.depMasterAutoID INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID WHERE srp_erp_fa_depmaster.depMasterAutoID = '{$depMasterAutoID}' GROUP BY DEPGLAutoID, srp_erp_fa_asset_master.segmentID")->result_array();

        foreach ($detailsDr as $key => $detail) {

            $GL = fetch_gl_account_desc($detail['DEPGLAutoID']);

            $data_arr['auto_id'] = $detail['faID'];
            $data_arr['gl_auto_id'] = $detail['DEPGLAutoID'];
            $data_arr['gl_code'] = $GL['systemAccountCode'];
            $data_arr['gl_secondary'] = $GL['GLSecondaryCode'];
            $data_arr['gl_desc'] = $GL['GLDescription'];
            $data_arr['gl_type'] = $GL['subCategory'];
            $data_arr['segment_id'] = $detail['segmentID'];
            $data_arr['segment'] = $detail['segmentCode'];

            $data_arr['gl_dr'] = number_format($detail['companyLocalAmount'], $detail['companyLocalCurrencyDecimalPlaces']);
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $dr_total += $detail['companyLocalAmount'];

            array_push($array, $data_arr);
        }

        /*Cr*/
        $detailsCr = $this->db->query("SELECT srp_erp_fa_depmaster.depCode, srp_erp_fa_depmaster.depDate, srp_erp_fa_depmaster.depMonthYear, srp_erp_fa_depmaster.FYBegin, srp_erp_fa_depmaster.FYEnd, srp_erp_fa_depmaster.FYPeriodDateFrom, srp_erp_fa_depmaster.FYPeriodDateTo, srp_erp_fa_assetdepreciationperiods.DepreciationPeriodsID, srp_erp_fa_assetdepreciationperiods.faFinanceCatID, srp_erp_fa_assetdepreciationperiods.faID, srp_erp_fa_assetdepreciationperiods.faMainCategory, srp_erp_fa_assetdepreciationperiods.faSubCategory, srp_erp_fa_assetdepreciationperiods.faCode, srp_erp_fa_assetdepreciationperiods.assetDescription, srp_erp_fa_assetdepreciationperiods.transactionCurrency, srp_erp_fa_assetdepreciationperiods.transactionExchangeRate, SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS transactionAmount, srp_erp_fa_assetdepreciationperiods.transactionCurrencyDecimalPlaces, srp_erp_fa_assetdepreciationperiods.companyLocalCurrency, srp_erp_fa_assetdepreciationperiods.companyLocalExchangeRate, SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS companyLocalAmount, srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces, srp_erp_fa_assetdepreciationperiods.companyReportingCurrency, srp_erp_fa_assetdepreciationperiods.companyReportingExchangeRate, SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS companyReportingAmount, srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyDecimalPlaces, srp_erp_fa_depmaster.documentID, srp_erp_fa_depmaster.serialNo, srp_erp_fa_depmaster.depMasterAutoID, srp_erp_fa_asset_master.ACCDEPGLCODE, srp_erp_fa_asset_master.DEPGLCODE, srp_erp_fa_asset_master.ACCDEPGLAutoID, srp_erp_fa_asset_master.DEPGLAutoID, srp_erp_fa_assetdepreciationperiods.segmentID, srp_erp_fa_assetdepreciationperiods.segmentCode, srp_erp_fa_depmaster.approvedbyEmpName, srp_erp_fa_depmaster.approvedbyEmpID, srp_erp_fa_depmaster.approvedDate, srp_erp_fa_depmaster.confirmedByEmpID, srp_erp_fa_depmaster.confirmedByName, srp_erp_fa_depmaster.confirmedDate FROM srp_erp_fa_assetdepreciationperiods INNER JOIN srp_erp_fa_depmaster ON srp_erp_fa_depmaster.depMasterAutoID = srp_erp_fa_assetdepreciationperiods.depMasterAutoID INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID WHERE srp_erp_fa_depmaster.depMasterAutoID = '{$depMasterAutoID}' GROUP BY ACCDEPGLAutoID")->result_array();

        foreach ($detailsCr as $key => $detail) {

            $GL = fetch_gl_account_desc($detail['ACCDEPGLAutoID']);

            $data_arr['auto_id'] = $detail['faID'];
            $data_arr['gl_auto_id'] = $detail['ACCDEPGLAutoID'];
            $data_arr['gl_code'] = $GL['systemAccountCode'];
            $data_arr['gl_secondary'] = $GL['GLSecondaryCode'];
            $data_arr['gl_desc'] = $GL['GLDescription'];
            $data_arr['gl_type'] = $GL['subCategory'];
            $data_arr['segment_id'] = $detail['segmentID'];
            $data_arr['segment'] = $detail['segmentCode'];

            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = number_format($detail['companyLocalAmount'], $detail['companyLocalCurrencyDecimalPlaces']);
            $data_arr['amount_type'] = 'cr';
            $cr_total += $detail['companyLocalAmount'];

            array_push($array, $data_arr);
        }

        $gl_array['code'] = 'FAD';
        $gl_array['name'] = 'Asset Depreciation';
        $gl_array['currency'] = $com_currency = $this->common_data['company_data']['company_default_currency'];
        $gl_array['decimal_places'] = $com_currency = $this->common_data['company_data']['company_default_currency'];
        $gl_array['primary_Code'] = $master['depCode'];
        $gl_array['date'] = format_date($master['depDate']);
        $gl_array['master_data'] = $array;
        $gl_array['dr_total'] = number_format($dr_total, $this->common_data['company_data']['company_default_decimal']);
        $gl_array['cr_total'] = number_format($cr_total, $this->common_data['company_data']['company_default_decimal']);
        $gl_array['approved'] = $master['approvedYN'];
        return $gl_array;
    }


    function fetch_double_entry_asset_disposal($assetdisposalMasterAutoID, $code = null)
    {
        $company_id = current_companyID();
        $gl_array = array();
        $dr_total = 0;
        $cr_total = 0;
        /*$gl_array['gl_detail'] = array();*/

        $this->db->select('*');
        $this->db->where('assetdisposalMasterAutoID', $assetdisposalMasterAutoID);
        $master = $this->db->get('srp_erp_fa_asset_disposalmaster')->row_array();

        $array = array();

        /*Disposal GL entries*/
        $disposalGLEntiries = $this->db->query("SELECT srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID, srp_erp_fa_asset_disposalmaster.disposalDocumentCode, srp_erp_fa_asset_disposalmaster.disposalDocumentDate, srp_erp_fa_asset_disposalmaster.segmentID, srp_erp_fa_asset_disposalmaster.narration, srp_erp_fa_asset_disposaldetail.segmentCode, srp_erp_fa_asset_disposaldetail.segmentID, srp_erp_fa_asset_disposaldetail.companyCode, srp_erp_fa_asset_disposaldetail.faID, srp_erp_fa_asset_disposaldetail.faCode, srp_erp_fa_asset_disposaldetail.assetDescription, srp_erp_fa_asset_disposaldetail.COSTGLAutoID, srp_erp_fa_asset_disposaldetail.ACCDEPGLAutoID, srp_erp_fa_asset_disposaldetail.DISPOGLAutoID, srp_erp_fa_asset_disposaldetail.transactionCurrency, srp_erp_fa_asset_disposaldetail.transactionCurrencyExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.transactionAmount) AS transactionAmount, srp_erp_fa_asset_disposaldetail.transactionCurrencyDecimalPlaces, srp_erp_fa_asset_disposaldetail.companyLocalCurrencyID, srp_erp_fa_asset_disposaldetail.companyLocalCurrency, srp_erp_fa_asset_disposaldetail.companyLocalExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.companyLocalAmount) AS companyLocalAmount, srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces, srp_erp_fa_asset_disposaldetail.companyReportingCurrencyID, srp_erp_fa_asset_disposaldetail.companyReportingCurrency, srp_erp_fa_asset_disposaldetail.companyReportingExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.companyReportingAmount) AS companyReportingAmount, srp_erp_fa_asset_disposaldetail.companyReportingDecimalPlaces, srp_erp_fa_asset_master.documentID, SUM(srp_erp_fa_asset_master.companyLocalAmount) AS assetCostLocal, SUM(srp_erp_fa_asset_master.transactionAmount) AS assetCostTransaction, SUM(srp_erp_fa_asset_master.companyReportingAmount) AS assetCostReporting, SUM(assetdepreciationperiods_tbl.accDepcompanyLocalAmount) AS accDepcompanyLocalAmount, SUM(assetdepreciationperiods_tbl.accDepcompanyReportingAmount) AS accDepcompanyReportingAmount, SUM(assetdepreciationperiods_tbl.accDeptransactionAmount) AS accDeptransactionAmount, srp_erp_fa_asset_disposalmaster.approvedbyEmpName, srp_erp_fa_asset_disposalmaster.approvedbyEmpID, srp_erp_fa_asset_disposalmaster.approvedDate, srp_erp_fa_asset_disposalmaster.confirmedDate, srp_erp_fa_asset_disposalmaster.confirmedByName, srp_erp_fa_asset_disposalmaster.confirmedByEmpID FROM srp_erp_fa_asset_disposalmaster INNER JOIN srp_erp_fa_asset_disposaldetail ON srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID LEFT JOIN ( SELECT SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS accDepcompanyLocalAmount, SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS accDepcompanyReportingAmount, SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS accDeptransactionAmount, srp_erp_fa_assetdepreciationperiods.depMasterAutoID, srp_erp_fa_assetdepreciationperiods.faMainCategory, srp_erp_fa_assetdepreciationperiods.faSubCategory, srp_erp_fa_assetdepreciationperiods.faID FROM srp_erp_fa_assetdepreciationperiods WHERE srp_erp_fa_assetdepreciationperiods.companyID = '{$company_id}' GROUP BY faID ) assetdepreciationperiods_tbl ON srp_erp_fa_asset_master.faID = assetdepreciationperiods_tbl.faID WHERE srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = '{$assetdisposalMasterAutoID}' GROUP BY srp_erp_fa_asset_disposaldetail.DISPOGLAutoID, srp_erp_fa_asset_master.segmentID")->result_array();

        foreach ($disposalGLEntiries as $key => $detail) {
            /*1. GL entries Asset Disposal GL Debit -- Asset Cost Amount*/
            $DISPOGLAutoID = fetch_gl_account_desc($detail['DISPOGLAutoID']);
            $data_arr['auto_id'] = $detail['assetdisposalMasterAutoID'];
            $data_arr['gl_auto_id'] = $detail['DISPOGLAutoID'];
            $data_arr['gl_code'] = $DISPOGLAutoID['systemAccountCode'];
            $data_arr['gl_secondary'] = $DISPOGLAutoID['GLSecondaryCode'];
            $data_arr['gl_desc'] = $DISPOGLAutoID['GLDescription'];
            $data_arr['gl_type'] = $DISPOGLAutoID['subCategory'];
            $data_arr['segment_id'] = $detail['segmentID'];
            $data_arr['segment'] = $detail['segmentCode'];

            $data_arr['gl_dr'] = number_format($detail['assetCostLocal'], $detail['companyLocalCurrencyDecimalPlaces']);
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $dr_total += $detail['assetCostLocal'];
            array_push($array, $data_arr);

            /*2 .GL entries Asset Disposal GL Credit -- Acc Dep Amount*/
            $data_arr['auto_id'] = $detail['assetdisposalMasterAutoID'];
            $data_arr['gl_auto_id'] = $detail['DISPOGLAutoID'];
            $data_arr['gl_code'] = $DISPOGLAutoID['systemAccountCode'];
            $data_arr['gl_secondary'] = $DISPOGLAutoID['GLSecondaryCode'];
            $data_arr['gl_desc'] = $DISPOGLAutoID['GLDescription'];
            $data_arr['gl_type'] = $DISPOGLAutoID['subCategory'];
            $data_arr['segment_id'] = $detail['segmentID'];
            $data_arr['segment'] = $detail['segmentCode'];

            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = number_format($detail['accDepcompanyLocalAmount'], $detail['companyLocalCurrencyDecimalPlaces']);
            $data_arr['amount_type'] = 'cr';
            $dr_total += $detail['accDepcompanyLocalAmount'];
            array_push($array, $data_arr);

            /*3 .GL entries Asset Disposal GL Credit -- Disposal Amount*/
            $data_arr['auto_id'] = $detail['assetdisposalMasterAutoID'];
            $data_arr['gl_auto_id'] = $detail['DISPOGLAutoID'];
            $data_arr['gl_code'] = $DISPOGLAutoID['systemAccountCode'];
            $data_arr['gl_secondary'] = $DISPOGLAutoID['GLSecondaryCode'];
            $data_arr['gl_desc'] = $DISPOGLAutoID['GLDescription'];
            $data_arr['gl_type'] = $DISPOGLAutoID['subCategory'];
            $data_arr['segment_id'] = $detail['segmentID'];
            $data_arr['segment'] = $detail['segmentCode'];

            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = number_format($detail['companyLocalAmount'], $detail['companyLocalCurrencyDecimalPlaces']);
            $data_arr['amount_type'] = 'cr';
            $dr_total += $detail['companyLocalAmount'];
            array_push($array, $data_arr);
        }

        /**/
        $datas = $this->db->query("SELECT srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID, srp_erp_fa_asset_disposalmaster.disposalDocumentCode, srp_erp_fa_asset_disposalmaster.disposalDocumentDate, srp_erp_fa_asset_disposalmaster.segmentID, srp_erp_fa_asset_disposalmaster.narration, srp_erp_fa_asset_disposaldetail.segmentCode, srp_erp_fa_asset_disposaldetail.segmentID, srp_erp_fa_asset_disposaldetail.companyCode, srp_erp_fa_asset_disposaldetail.faID, srp_erp_fa_asset_disposaldetail.faCode, srp_erp_fa_asset_disposaldetail.assetDescription, srp_erp_fa_asset_disposaldetail.COSTGLAutoID, srp_erp_fa_asset_disposaldetail.ACCDEPGLAutoID, srp_erp_fa_asset_disposaldetail.DISPOGLAutoID, srp_erp_fa_asset_disposaldetail.transactionCurrency, srp_erp_fa_asset_disposaldetail.transactionCurrencyExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.transactionAmount) AS transactionAmount, srp_erp_fa_asset_disposaldetail.transactionCurrencyDecimalPlaces, srp_erp_fa_asset_disposaldetail.companyLocalCurrencyID, srp_erp_fa_asset_disposaldetail.companyLocalCurrency, srp_erp_fa_asset_disposaldetail.companyLocalExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.companyLocalAmount) AS companyLocalAmount, srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces, srp_erp_fa_asset_disposaldetail.companyReportingCurrencyID, srp_erp_fa_asset_disposaldetail.companyReportingCurrency, srp_erp_fa_asset_disposaldetail.companyReportingExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.companyReportingAmount) AS companyReportingAmount, srp_erp_fa_asset_disposaldetail.companyReportingDecimalPlaces, srp_erp_fa_asset_master.documentID, SUM(srp_erp_fa_asset_master.companyLocalAmount) AS assetCostLocal, SUM(srp_erp_fa_asset_master.transactionAmount) AS assetCostTransaction, SUM(srp_erp_fa_asset_master.companyReportingAmount) AS assetCostReporting, SUM(assetdepreciationperiods_tbl.accDepcompanyLocalAmount) AS accDepcompanyLocalAmount, SUM(assetdepreciationperiods_tbl.accDepcompanyReportingAmount) AS accDepcompanyReportingAmount, SUM(assetdepreciationperiods_tbl.accDeptransactionAmount) AS accDeptransactionAmount, srp_erp_fa_asset_disposalmaster.approvedbyEmpName, srp_erp_fa_asset_disposalmaster.approvedbyEmpID, srp_erp_fa_asset_disposalmaster.approvedDate, srp_erp_fa_asset_disposalmaster.confirmedDate, srp_erp_fa_asset_disposalmaster.confirmedByName, srp_erp_fa_asset_disposalmaster.confirmedByEmpID FROM srp_erp_fa_asset_disposalmaster INNER JOIN srp_erp_fa_asset_disposaldetail ON srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID LEFT JOIN ( SELECT SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS accDepcompanyLocalAmount, SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS accDepcompanyReportingAmount, SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS accDeptransactionAmount, srp_erp_fa_assetdepreciationperiods.depMasterAutoID, srp_erp_fa_assetdepreciationperiods.faMainCategory, srp_erp_fa_assetdepreciationperiods.faSubCategory, srp_erp_fa_assetdepreciationperiods.faID FROM srp_erp_fa_assetdepreciationperiods WHERE srp_erp_fa_assetdepreciationperiods.companyID = '{$company_id}' GROUP BY faID ) assetdepreciationperiods_tbl ON srp_erp_fa_asset_master.faID = assetdepreciationperiods_tbl.faID WHERE srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = '{$assetdisposalMasterAutoID}' GROUP BY srp_erp_fa_asset_disposaldetail.COSTGLAutoID")->result_array();

        foreach ($datas as $key => $detail) {

            /*1. GL entries Cost GL - Cost Amount*/
            $COSTGLAutoID = fetch_gl_account_desc($detail['COSTGLAutoID']);

            $data_arr['auto_id'] = $detail['assetdisposalMasterAutoID'];
            $data_arr['gl_auto_id'] = $detail['COSTGLAutoID'];
            $data_arr['gl_code'] = $COSTGLAutoID['systemAccountCode'];
            $data_arr['gl_secondary'] = $COSTGLAutoID['GLSecondaryCode'];
            $data_arr['gl_desc'] = $COSTGLAutoID['GLDescription'];
            $data_arr['gl_type'] = $COSTGLAutoID['subCategory'];
            $data_arr['segment_id'] = $detail['segmentID'];
            $data_arr['segment'] = $detail['segmentCode'];

            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = number_format($detail['assetCostLocal'], $detail['companyLocalCurrencyDecimalPlaces']);
            $data_arr['amount_type'] = 'cr';
            $cr_total += $detail['assetCostLocal'];

            array_push($array, $data_arr);
        }

        /**/
        $items = $this->db->query("SELECT srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID, srp_erp_fa_asset_disposalmaster.disposalDocumentCode, srp_erp_fa_asset_disposalmaster.disposalDocumentDate, srp_erp_fa_asset_disposalmaster.segmentID, srp_erp_fa_asset_disposalmaster.narration, srp_erp_fa_asset_disposaldetail.segmentCode, srp_erp_fa_asset_disposaldetail.segmentID, srp_erp_fa_asset_disposaldetail.companyCode, srp_erp_fa_asset_disposaldetail.faID, srp_erp_fa_asset_disposaldetail.faCode, srp_erp_fa_asset_disposaldetail.assetDescription, srp_erp_fa_asset_disposaldetail.COSTGLAutoID, srp_erp_fa_asset_disposaldetail.ACCDEPGLAutoID, srp_erp_fa_asset_disposaldetail.DISPOGLAutoID, srp_erp_fa_asset_disposaldetail.transactionCurrency, srp_erp_fa_asset_disposaldetail.transactionCurrencyExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.transactionAmount) AS transactionAmount, srp_erp_fa_asset_disposaldetail.transactionCurrencyDecimalPlaces, srp_erp_fa_asset_disposaldetail.companyLocalCurrencyID, srp_erp_fa_asset_disposaldetail.companyLocalCurrency, srp_erp_fa_asset_disposaldetail.companyLocalExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.companyLocalAmount) AS companyLocalAmount, srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces, srp_erp_fa_asset_disposaldetail.companyReportingCurrencyID, srp_erp_fa_asset_disposaldetail.companyReportingCurrency, srp_erp_fa_asset_disposaldetail.companyReportingExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.companyReportingAmount) AS companyReportingAmount, srp_erp_fa_asset_disposaldetail.companyReportingDecimalPlaces, srp_erp_fa_asset_master.documentID, SUM(srp_erp_fa_asset_master.companyLocalAmount) AS assetCostLocal, SUM(srp_erp_fa_asset_master.transactionAmount) AS assetCostTransaction, SUM(srp_erp_fa_asset_master.companyReportingAmount) AS assetCostReporting, SUM(assetdepreciationperiods_tbl.accDepcompanyLocalAmount) AS accDepcompanyLocalAmount, SUM(assetdepreciationperiods_tbl.accDepcompanyReportingAmount) AS accDepcompanyReportingAmount, SUM(assetdepreciationperiods_tbl.accDeptransactionAmount) AS accDeptransactionAmount, srp_erp_fa_asset_disposalmaster.approvedbyEmpName, srp_erp_fa_asset_disposalmaster.approvedbyEmpID, srp_erp_fa_asset_disposalmaster.approvedDate, srp_erp_fa_asset_disposalmaster.confirmedDate, srp_erp_fa_asset_disposalmaster.confirmedByName, srp_erp_fa_asset_disposalmaster.confirmedByEmpID FROM srp_erp_fa_asset_disposalmaster INNER JOIN srp_erp_fa_asset_disposaldetail ON srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID LEFT JOIN ( SELECT SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS accDepcompanyLocalAmount, SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS accDepcompanyReportingAmount, SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS accDeptransactionAmount, srp_erp_fa_assetdepreciationperiods.depMasterAutoID, srp_erp_fa_assetdepreciationperiods.faMainCategory, srp_erp_fa_assetdepreciationperiods.faSubCategory, srp_erp_fa_assetdepreciationperiods.faID FROM srp_erp_fa_assetdepreciationperiods WHERE srp_erp_fa_assetdepreciationperiods.companyID = '{$company_id}' GROUP BY faID ) assetdepreciationperiods_tbl ON srp_erp_fa_asset_master.faID = assetdepreciationperiods_tbl.faID WHERE srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = '{$assetdisposalMasterAutoID}' GROUP BY srp_erp_fa_asset_disposaldetail.ACCDEPGLAutoID")->result_array();


        foreach ($datas as $key => $detail) {

            /*1. GL entries Cost GL - Cost Amount*/
            $ACCDEPGLAutoID = fetch_gl_account_desc($detail['ACCDEPGLAutoID']);

            $data_arr['auto_id'] = $detail['assetdisposalMasterAutoID'];
            $data_arr['gl_auto_id'] = $detail['ACCDEPGLAutoID'];
            $data_arr['gl_code'] = $ACCDEPGLAutoID['systemAccountCode'];
            $data_arr['gl_secondary'] = $ACCDEPGLAutoID['GLSecondaryCode'];
            $data_arr['gl_desc'] = $ACCDEPGLAutoID['GLDescription'];
            $data_arr['gl_type'] = $ACCDEPGLAutoID['subCategory'];
            $data_arr['segment_id'] = $detail['segmentID'];
            $data_arr['segment'] = $detail['segmentCode'];

            $data_arr['gl_dr'] = number_format($detail['accDepcompanyLocalAmount'], $detail['companyLocalCurrencyDecimalPlaces']);
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $cr_total += $detail['accDepcompanyLocalAmount'];

            array_push($array, $data_arr);
        }

        /**/
        $banks = $this->db->query("SELECT srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID, srp_erp_fa_asset_disposalmaster.disposalDocumentCode, srp_erp_fa_asset_disposalmaster.disposalDocumentDate, srp_erp_fa_asset_disposalmaster.segmentID, srp_erp_fa_asset_disposalmaster.narration, srp_erp_fa_asset_disposaldetail.segmentCode, srp_erp_fa_asset_disposaldetail.segmentID, srp_erp_fa_asset_disposaldetail.companyCode, srp_erp_fa_asset_disposaldetail.faID, srp_erp_fa_asset_disposaldetail.faCode, srp_erp_fa_asset_disposaldetail.assetDescription, srp_erp_fa_asset_disposaldetail.COSTGLAutoID, srp_erp_fa_asset_disposaldetail.ACCDEPGLAutoID, srp_erp_fa_asset_disposaldetail.DISPOGLAutoID, srp_erp_fa_asset_disposaldetail.transactionCurrency, srp_erp_fa_asset_disposaldetail.transactionCurrencyExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.transactionAmount) AS transactionAmount, srp_erp_fa_asset_disposaldetail.transactionCurrencyDecimalPlaces, srp_erp_fa_asset_disposaldetail.companyLocalCurrencyID, srp_erp_fa_asset_disposaldetail.companyLocalCurrency, srp_erp_fa_asset_disposaldetail.companyLocalExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.companyLocalAmount) AS companyLocalAmount, srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces, srp_erp_fa_asset_disposaldetail.companyReportingCurrencyID, srp_erp_fa_asset_disposaldetail.companyReportingCurrency, srp_erp_fa_asset_disposaldetail.companyReportingExchangeRate, SUM(srp_erp_fa_asset_disposaldetail.companyReportingAmount) AS companyReportingAmount, srp_erp_fa_asset_disposaldetail.companyReportingDecimalPlaces, srp_erp_fa_asset_master.documentID, SUM(srp_erp_fa_asset_master.companyLocalAmount) AS assetCostLocal, SUM(srp_erp_fa_asset_master.transactionAmount) AS assetCostTransaction, SUM(srp_erp_fa_asset_master.companyReportingAmount) AS assetCostReporting, SUM(assetdepreciationperiods_tbl.accDepcompanyLocalAmount) AS accDepcompanyLocalAmount, SUM(assetdepreciationperiods_tbl.accDepcompanyReportingAmount) AS accDepcompanyReportingAmount, SUM(assetdepreciationperiods_tbl.accDeptransactionAmount) AS accDeptransactionAmount, srp_erp_fa_asset_disposalmaster.approvedbyEmpName, srp_erp_fa_asset_disposalmaster.approvedbyEmpID, srp_erp_fa_asset_disposalmaster.approvedDate, srp_erp_fa_asset_disposalmaster.confirmedDate, srp_erp_fa_asset_disposalmaster.confirmedByName, srp_erp_fa_asset_disposalmaster.confirmedByEmpID FROM srp_erp_fa_asset_disposalmaster INNER JOIN srp_erp_fa_asset_disposaldetail ON srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID LEFT JOIN ( SELECT SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS accDepcompanyLocalAmount, SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS accDepcompanyReportingAmount, SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS accDeptransactionAmount, srp_erp_fa_assetdepreciationperiods.depMasterAutoID, srp_erp_fa_assetdepreciationperiods.faMainCategory, srp_erp_fa_assetdepreciationperiods.faSubCategory, srp_erp_fa_assetdepreciationperiods.faID FROM srp_erp_fa_assetdepreciationperiods WHERE srp_erp_fa_assetdepreciationperiods.companyID = '{$company_id}' GROUP BY faID ) assetdepreciationperiods_tbl ON srp_erp_fa_asset_master.faID = assetdepreciationperiods_tbl.faID WHERE srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = '{$assetdisposalMasterAutoID}'")->result_array();

        foreach ($banks as $key => $detail) {

            /*1. GL entries Cost GL - Cost Amount*/
            $bankAccGL = $this->db->query("SELECT * FROM srp_erp_chartofaccounts WHERE GLAutoID = ( SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'ADSP' AND companyID = '{$company_id}' )")->row_array();

            $data_arr['auto_id'] = $detail['assetdisposalMasterAutoID'];
            $data_arr['gl_auto_id'] = $bankAccGL['GLAutoID'];
            $data_arr['gl_code'] = $bankAccGL['systemAccountCode'];
            $data_arr['gl_secondary'] = $bankAccGL['GLSecondaryCode'];
            $data_arr['gl_desc'] = $bankAccGL['GLDescription'];
            $data_arr['gl_type'] = $bankAccGL['subCategory'];
            $data_arr['segment_id'] = $detail['segmentID'];
            $data_arr['segment'] = $detail['segmentCode'];

            $data_arr['gl_dr'] = number_format($detail['companyLocalAmount'], $detail['companyLocalCurrencyDecimalPlaces']);
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $cr_total += $detail['companyLocalAmount'];

            array_push($array, $data_arr);
        }

        $gl_array['code'] = 'FAD';
        $gl_array['name'] = 'Asset Depreciation';
        $gl_array['currency'] = $com_currency = $this->common_data['company_data']['company_default_currency'];;
        $gl_array['decimal_places'] = $com_currency = $this->common_data['company_data']['company_default_currency'];;
        $gl_array['primary_Code'] = $master['disposalDocumentCode'];
        $gl_array['date'] = format_date($master['disposalDocumentDate']);
        $gl_array['master_data'] = $array;
        $gl_array['dr_total'] = $dr_total;
        $gl_array['cr_total'] = $cr_total;
        $gl_array['approved'] = $master['approvedYN'];
        return $gl_array;
    }

    public function array_group_sum($data)
    {
        $groups = array();
        $key = 0;
        foreach ($data as $item) {
            $key = $item['gl_auto_id'] . $item['segment_id'];
            if (!array_key_exists($key, $groups)) {
                $groups[$key] = array(
                    'auto_id' => $item['auto_id'],
                    'gl_auto_id' => $item['gl_auto_id'],
                    'gl_code' => $item['gl_code'],
                    'secondary' => $item['secondary'],
                    'gl_desc' => $item['gl_desc'],
                    'gl_type' => $item['gl_type'],
                    'segment' => $item['segment'],
                    'segment_id' => $item['segment_id'],
                    'gl_dr' => $item['gl_dr'],
                    'gl_cr' => $item['gl_cr'],
                    'amount_type' => $item['amount_type'],
                    'isAddon' => $item['isAddon'],
                    'subLedgerType' => $item['subLedgerType'],
                    'subLedgerDesc' => $item['subLedgerDesc'],
                    'partyContractID' => $item['partyContractID'],
                    'partyType' => $item['partyType'],
                    'partyAutoID' => $item['partyAutoID'],
                    'partySystemCode' => $item['partySystemCode'],
                    'partyName' => $item['partyName'],
                    'partyCurrencyID' => $item['partyCurrencyID'],
                    'partyCurrency' => $item['partyCurrency'],
                    'transactionExchangeRate' => $item['transactionExchangeRate'],
                    'companyLocalExchangeRate' => $item['companyLocalExchangeRate'],
                    'companyReportingExchangeRate' => $item['companyReportingExchangeRate'],
                    'partyExchangeRate' => $item['partyExchangeRate'],
                    'partyCurrencyAmount' => $item['partyCurrencyAmount'],
                    'partyCurrencyDecimalPlaces' => $item['partyCurrencyDecimalPlaces'],
                );
            } else {
                $groups[$key]['gl_dr'] = $groups[$key]['gl_dr'] + $item['gl_dr'];
                $groups[$key]['gl_cr'] = $groups[$key]['gl_cr'] + $item['gl_cr'];
            }
            $key++;
        }
        $groups = array_values($groups);
        return $groups;
    }

    public function array_group_sum_tax($data)
    {
        $groups = array();
        $key = 0;
        foreach ($data as $item) {
            $key = $item['gl_auto_id'] . $item['segment_id'] . $item['gl_desc'];
            if (!array_key_exists($key, $groups)) {
                $groups[$key] = array(
                    'auto_id' => $item['auto_id'],
                    'gl_auto_id' => $item['gl_auto_id'],
                    'gl_code' => $item['gl_code'],
                    'secondary' => $item['secondary'],
                    'gl_desc' => $item['gl_desc'],
                    'gl_type' => $item['gl_type'],
                    'segment' => $item['segment'],
                    'segment_id' => $item['segment_id'],
                    'gl_dr' => $item['gl_dr'],
                    'gl_cr' => $item['gl_cr'],
                    'amount_type' => $item['amount_type'],
                    'isAddon' => $item['isAddon'],
                    'taxMasterAutoID' => $item['taxMasterAutoID'],
                    'partyVatIdNo' => $item['partyVatIdNo'],
                    'subLedgerType' => $item['subLedgerType'],
                    'subLedgerDesc' => $item['subLedgerDesc'],
                    'partyContractID' => $item['partyContractID'],
                    'partyType' => $item['partyType'],
                    'partyAutoID' => $item['partyAutoID'],
                    'partySystemCode' => $item['partySystemCode'],
                    'partyName' => $item['partyName'],
                    'partyCurrencyID' => $item['partyCurrencyID'],
                    'partyCurrency' => $item['partyCurrency'],
                    'transactionExchangeRate' => $item['transactionExchangeRate'],
                    'companyLocalExchangeRate' => $item['companyLocalExchangeRate'],
                    'companyReportingExchangeRate' => $item['companyReportingExchangeRate'],
                    'partyExchangeRate' => $item['partyExchangeRate'],
                    'partyCurrencyAmount' => $item['partyCurrencyAmount'],
                    'partyCurrencyDecimalPlaces' => $item['partyCurrencyDecimalPlaces'],
                );
            } else {
                $groups[$key]['gl_dr'] = $groups[$key]['gl_dr'] + $item['gl_dr'];
                $groups[$key]['gl_cr'] = $groups[$key]['gl_cr'] + $item['gl_cr'];
            }
            $key++;
        }
        $groups = array_values($groups);
        return $groups;
    }

    function fetch_double_entry_SC($salesCommisionID, $code = null)
    {
        $gl_array = array();
        $gl_array['gl_detail'] = array();
        //$liability_arr = array();
        //$expanse_arr = array();
        $this->db->select('*');
        $this->db->where('salesCommisionID', $salesCommisionID);
        $master = $this->db->get('srp_erp_salescommisionmaster')->row_array();

        $this->db->select('srp_erp_salescommisionperson.salesPersonID,liabilityAutoID,liabilitySystemGLCode,liabilityGLAccount, liabilityDescription, liabilityType, expenseAutoID,expenseSystemGLCode,expenseGLAccount, expenseDescription, expenseType,netCommision, netCommision as total_amount,segmentID,segmentCode,SalesPersonCode,SalesPersonName, srp_erp_salespersonmaster.salesPersonCurrency, srp_erp_salespersonmaster.salesPersonCurrencyID');
        $this->db->where('salesCommisionID', $salesCommisionID);
        $this->db->where('netCommision <>', 0);
        $this->db->from('srp_erp_salescommisionperson');
        $this->db->join('srp_erp_salespersonmaster', 'srp_erp_salespersonmaster.salesPersonID=srp_erp_salescommisionperson.salesPersonID');
        $detail_arr = $this->db->get()->result_array();


        if (!empty($detail_arr)) {
            foreach ($detail_arr as $val) {
                $data_arr['auto_id'] = $salesCommisionID;
                $data_arr['gl_auto_id'] = $val['expenseAutoID'];
                $data_arr['gl_code'] = $val['expenseSystemGLCode'];
                $data_arr['secondary'] = $val['expenseGLAccount'];
                $data_arr['gl_desc'] = $val['expenseDescription'] . ' - ' . $val['SalesPersonName'];
                $data_arr['gl_type'] = $val['expenseType'];
                $data_arr['segment_id'] = $val['segmentID'];
                $data_arr['segment'] = $val['segmentCode'];
                $data_arr['gl_dr'] = $val['total_amount'];;
                $data_arr['gl_cr'] = 0;
                $data_arr['amount_type'] = 'dr';
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = null;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'REP';
                $data_arr['partyAutoID'] = $val['salesPersonID'];
                $data_arr['partySystemCode'] = $val['SalesPersonCode'];
                $data_arr['partyName'] = $val['SalesPersonName'];
                $data_arr['partyCurrencyID'] = $val['salesPersonCurrencyID'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyCurrency'] = $val['salesPersonCurrency'];
                $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $data_arr['partyCurrencyID']);
                $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
                $data_arr['partyCurrencyDecimalPlaces'] = $conversion_arr['DecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = ($val['total_amount'] / $conversion_arr['conversion']);
                array_push($gl_array['gl_detail'], $data_arr);

                $data_arr['auto_id'] = $salesCommisionID;
                $data_arr['gl_auto_id'] = $val['liabilityAutoID'];
                $data_arr['gl_code'] = $val['liabilitySystemGLCode'];
                $data_arr['secondary'] = $val['liabilityGLAccount'];
                $data_arr['gl_desc'] = $val['liabilityDescription'] . ' - ' . $val['SalesPersonName'];
                $data_arr['gl_type'] = $val['liabilityType'];
                $data_arr['segment_id'] = $val['segmentID'];
                $data_arr['segment'] = $val['segmentCode'];
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $val['total_amount'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 4;
                $data_arr['subLedgerDesc'] = 'SC';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'REP';
                $data_arr['partyAutoID'] = $val['salesPersonID'];
                $data_arr['partySystemCode'] = $val['SalesPersonCode'];
                $data_arr['partyName'] = $val['SalesPersonName'];
                $data_arr['partyCurrencyID'] = $val['salesPersonCurrencyID'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyCurrency'] = $val['salesPersonCurrency'];
                $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $data_arr['partyCurrencyID']);
                $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
                $data_arr['partyCurrencyDecimalPlaces'] = $conversion_arr['DecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = ($val['total_amount'] / $conversion_arr['conversion']);
                array_push($gl_array['gl_detail'], $data_arr);
            }
        }

        //$liability_arr = $this->array_group_sum($liability_arr);
        //$expanse_arr   = $this->array_group_sum($expanse_arr);
        /*foreach ($expanse_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($liability_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }*/

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'SC';
        $gl_array['name'] = 'Sales Commission';
        $gl_array['primary_Code'] = $master['salesCommisionCode'];
        $gl_array['approved_yn'] = $master['approvedYN'];
        $gl_array['date'] = $master['asOfDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }


    /**
     * Created on 18-05-2017
     * @param $salesReturnAutoID
     * @return array
     */
    function fetch_double_entry_sales_return_data($salesReturnAutoID, $documentID) /*, $code = null*/
    {
        $this->db->select('srp_erp_salesreturnmaster.*,srp_erp_customermaster.customerName as customerNamemaster');
        $this->db->where('salesReturnAutoID', $salesReturnAutoID);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_salesreturnmaster.customerID', 'left');
        $master = $this->db->get('srp_erp_salesreturnmaster')->row_array();

        /** Cost Related GL entries */
        $GLEntry_cost = $this->db->query("SELECT
                                    detailTbl.segmentID as segmentID,
                                    detailTbl.segmentCode as segmentCode,
                                    'cr' as amountType,
                                    null as subLedgerType,
                                    null as subLedgerDesc,
                                    detailTbl.expenseGLAutoID  AS GLAutoID, 
                                    detailTbl.expenseSystemGLCode  AS SystemGLCode, 
                                    detailTbl.expenseGLCode AS GLSecondaryCode, 
                                    detailTbl.expenseGLDescription AS GLDescription,
                                    detailTbl.segmentCode AS segmentCode,
                                    detailTbl.expenseGLType AS GLType,
                                    ABS(SUM( ( detailTbl.currentWacAmount * detailTbl.return_Qty ) / ( 1 / masterTbl.companyLocalExchangeRate ) )) *-1 AS credit,
                                    0 as debit,
                                    ABS(SUM( ( detailTbl.currentWacAmount * detailTbl.return_Qty ) / ( 1 / masterTbl.companyLocalExchangeRate ) )) *-1 AS transactionAmount,
                                    masterTbl.transactionCurrencyDecimalPlaces as transactionDecimal,
                                    masterTbl.approvedYN as approvedYN,
                                    detailTbl.salesReturnAutoID as auto_id                              
                                FROM srp_erp_salesreturnmaster masterTbl
                                LEFT JOIN srp_erp_salesreturndetails detailTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID AND detailTbl.itemCategory = 'Inventory'
                                WHERE detailTbl.salesReturnAutoID = '{$salesReturnAutoID}'
                                GROUP BY detailTbl.expenseGLAutoID")->result_array();

        /** Inventory GL entries */
        $GLEntry_inventory = $this->db->query("SELECT
                                    detailTbl.segmentID as segmentID,
                                    detailTbl.segmentCode as segmentCode,
                                    'dr' as amountType,
                                    null as subLedgerType,
                                    null as subLedgerDesc,
                                    detailTbl.assetGLAutoID  AS GLAutoID, 
                                    detailTbl.assetSystemGLCode  AS SystemGLCode, 
                                    detailTbl.assetGLCode AS GLSecondaryCode, 
                                    detailTbl.assetGLDescription AS GLDescription,
                                    detailTbl.segmentCode AS segmentCode,
                                    detailTbl.assetGLType AS GLType,
                                    0 AS credit,
                                    ABS(SUM((detailTbl.currentWacAmount * detailTbl.return_Qty ) / (1/masterTbl.companyLocalExchangeRate))) as debit,
                                    ABS(SUM((detailTbl.currentWacAmount * detailTbl.return_Qty ) / (1/masterTbl.companyLocalExchangeRate)))  AS transactionAmount,
                                    masterTbl.transactionCurrencyDecimalPlaces as transactionDecimal,
                                    masterTbl.approvedYN as approvedYN,
                                detailTbl.salesReturnAutoID as auto_id
                                FROM srp_erp_salesreturnmaster masterTbl
                                LEFT JOIN srp_erp_salesreturndetails detailTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID AND detailTbl.itemCategory = 'Inventory'
                                WHERE detailTbl.salesReturnAutoID = '{$salesReturnAutoID}'
                                GROUP BY detailTbl.assetGLAutoID")->result_array();

        /** VAT GL entries */
        $GLEntry_VAT = $this->db->query("SELECT
                                            detailTbl.segmentID AS segmentID,
                                            detailTbl.segmentCode AS segmentCode,
                                            'dr' AS amountType,
                                            NULL AS subLedgerType,
                                            NULL AS subLedgerDesc,
                                            auto_id AS auto_id,
                                            gl_auto_id AS GLAutoID,
                                            systemAccountCode AS SystemGLCode,
                                            GLSecondaryCode AS GLSecondaryCode,
                                            GLDescription AS GLDescription,
                                            detailTbl.segmentCode AS segmentCode,
                                            subCategory AS GLType,
                                            0 AS credit,
                                            ABS( gl_taxAmount ) AS debit,
                                            ABS( gl_taxAmount ) AS transactionAmount,
                                            masterTbl.transactionCurrencyDecimalPlaces AS transactionDecimal,
                                            masterTbl.approvedYN AS approvedYN 
                                        FROM
                                            (
                                            SELECT
                                                documentDetailAutoID AS auto_id,
                                                taxGlAutoID AS gl_auto_id,
                                                SUM( amount ) AS gl_taxAmount,
                                                taxMasterID 
                                            FROM
                                                srp_erp_taxledger
                                                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                            WHERE
                                                documentID = 'SLR' 
                                                AND documentMasterAutoID = {$salesReturnAutoID} 
                                            GROUP BY
                                                taxMasterID 
                                            ) t1
                                            LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id
                                            LEFT JOIN srp_erp_salesreturndetails detailTbl ON t1.auto_id = detailTbl.salesReturnDetailsID
                                            LEFT JOIN srp_erp_salesreturnmaster masterTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID")->result_array();


        /** Revenue GL entries */
        $GLEntry_revenue = $this->db->query("SELECT
                                    detailTbl.segmentID as segmentID,
                                    detailTbl.segmentCode as segmentCode,
                                    'dr' as amountType,
                                    null as subLedgerType,
                                    null as subLedgerDesc,
                                    detailTbl.revenueGLAutoID  AS GLAutoID, 
                                    detailTbl.revenueSystemGLCode AS SystemGLCode, 
                                    detailTbl.revenueGLCode AS GLSecondaryCode, 
                                    detailTbl.revenueGLDescription AS GLDescription,
                                    detailTbl.segmentCode AS segmentCode,
                                    detailTbl.revenueGLType AS GLType,
                                    0 AS credit,
                                    ABS(SUM(((detailTbl.totalValue / 100) * (100 - IFNULL( discountPer, 0 ))))) AS debit,
                                    ABS(SUM(((detailTbl.totalValue / 100) * (100 - IFNULL( discountPer, 0 ))))) AS transactionAmount,
                                    masterTbl.transactionCurrencyDecimalPlaces as transactionDecimal,
                                    masterTbl.approvedYN as approvedYN,
                                detailTbl.salesReturnAutoID as auto_id
                                FROM srp_erp_salesreturnmaster masterTbl
                                LEFT JOIN srp_erp_salesreturndetails detailTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID
                                LEFT JOIN ( SELECT SUM( discountPercentage ) AS discountPer, salesReturnDetailsID FROM srp_erp_salesreturndiscountdetails WHERE isChargeToExpense = 0 GROUP BY salesReturnDetailsID ) discountTable ON discountTable.salesReturnDetailsID = detailTbl.salesReturnDetailsID
                                WHERE detailTbl.salesReturnAutoID = '{$salesReturnAutoID}'
                                GROUP BY detailTbl.revenueGLAutoID")->result_array();

        /** Receivable GL entries */
        $GLEntry_receivable = $this->db->query("SELECT
                                        detailTbl.segmentID as segmentID,
                                        detailTbl.segmentCode as segmentCode,
                                        'cr' as amountType,
                                        '3' as subLedgerType,
                                        'AR' as subLedgerDesc,
                                        masterTbl.customerReceivableAutoID  AS GLAutoID, 
                                        masterTbl.customerReceivableSystemGLCode AS SystemGLCode, 
                                        coa.GLSecondaryCode AS GLSecondaryCode, 
                                        masterTbl.customerReceivableDescription AS GLDescription,
                                        detailTbl.segmentCode AS segmentCode,
                                        coa.subCategory AS GLType,
                                        ABS(SUM(((((detailTbl.totalValue / 100) * (100 - IFNULL(discountPer, 0)))/100) * (100 + IFNULL(taxPer, 0))) - IFNULL(detailTbl.rebateAmount,0)) + IFNULL(taxAmount, 0)) * -1  AS credit,
                                        0 AS debit,
                                        ABS(SUM(((((detailTbl.totalValue / 100) * (100 - IFNULL(discountPer, 0)))/100) * (100 + IFNULL(taxPer, 0))) - IFNULL(detailTbl.rebateAmount,0)) + IFNULL(taxAmount, 0)) * -1  AS transactionAmount,
                                        masterTbl.transactionCurrencyDecimalPlaces as transactionDecimal,
                                        masterTbl.approvedYN as approvedYN,
                                    detailTbl.salesReturnAutoID as auto_id
                                    FROM srp_erp_salesreturnmaster masterTbl
                                    LEFT JOIN srp_erp_salesreturndetails detailTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID
                                    LEFT JOIN (SELECT SUM(taxPercentage) as taxPer, salesReturnDetailsID FROM srp_erp_salesreturntaxdetails GROUP BY salesReturnDetailsID)taxTable ON taxTable.salesReturnDetailsID = detailTbl.salesReturnDetailsID
                                    JOIN srp_erp_chartofaccounts coa ON coa.GLAutoID = masterTbl.customerReceivableAutoID
                                    LEFT JOIN ( SELECT SUM( discountPercentage ) AS discountPer, salesReturnDetailsID FROM srp_erp_salesreturndiscountdetails GROUP BY salesReturnDetailsID ) discountTable ON discountTable.salesReturnDetailsID = detailTbl.salesReturnDetailsID
                                    WHERE detailTbl.salesReturnAutoID = '{$salesReturnAutoID}' /*AND detailTbl.DOAutoID IS NULL*/ ")->result_array();

        /** Rebate GL entries */
        $RebateGLEntry_receivable = $this->db->query("SELECT
                                    detailTbl.segmentID as segmentID,
                                    detailTbl.segmentCode as segmentCode,
                                    'cr' as amountType,
                                    null as subLedgerType,
                                    null as subLedgerDesc,
                                    coa.GLAutoID AS GLAutoID, 
                                    coa.systemAccountCode AS SystemGLCode, 
                                    coa.GLSecondaryCode AS GLSecondaryCode, 
                                    coa.GLDescription AS GLDescription,
                                    detailTbl.segmentCode AS segmentCode,
                                    coa.subCategory AS GLType,
                                    ABS(SUM(detailTbl.rebateAmount)) * -1  AS credit,
                                    0 AS debit,
                                    ABS(SUM(detailTbl.rebateAmount)) * -1  AS transactionAmount,
                                    masterTbl.transactionCurrencyDecimalPlaces as transactionDecimal,
                                    masterTbl.approvedYN as approvedYN,
                                detailTbl.salesReturnAutoID as auto_id
                                FROM srp_erp_salesreturnmaster masterTbl
                                LEFT JOIN srp_erp_salesreturndetails detailTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID
                                JOIN srp_erp_chartofaccounts coa ON coa.GLAutoID = detailTbl.rebateGLAutoID
                                WHERE detailTbl.salesReturnAutoID = '{$salesReturnAutoID}' /*AND detailTbl.DOAutoID IS NULL*/ GROUP BY detailTbl.rebateGLAutoID")->result_array();

        /** Un billed invoice GL entry*/
        $companyID = current_companyID();
        $GLEntry_UBI = $this->db->query("SELECT
                                    detailTbl.segmentID as segmentID,
                                    detailTbl.segmentCode as segmentCode,
                                    'cr' as amountType,
                                    null as subLedgerType,
                                    null as subLedgerDesc,
                                    coa.GLAutoID AS GLAutoID, 
                                    coa.systemAccountCode AS SystemGLCode, 
                                    coa.GLSecondaryCode AS GLSecondaryCode, 
                                    coa.GLDescription AS GLDescription,
                                    detailTbl.segmentCode AS segmentCode,
                                    coa.subCategory AS GLType,
                                    ABS(SUM(detailTbl.totalValue)) * -1  AS credit,
                                    0 AS debit,
                                    ABS(SUM(detailTbl.totalValue)) * -1  AS transactionAmount,
                                    masterTbl.transactionCurrencyDecimalPlaces as transactionDecimal,
                                    masterTbl.approvedYN as approvedYN,
                                detailTbl.salesReturnAutoID as auto_id
                                FROM srp_erp_salesreturnmaster masterTbl
                                LEFT JOIN srp_erp_salesreturndetails detailTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID
                                JOIN (
                                      SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory, companyID 
                                      FROM srp_erp_chartofaccounts
                                      WHERE GLAutoID = (
                                          SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'UBI' AND companyID = {$companyID}
                                      ) AND companyID={$companyID} 
                                )  AS coa ON coa.companyID = masterTbl.companyID
                            WHERE detailTbl.salesReturnAutoID = '{$salesReturnAutoID}' AND detailTbl.invoiceAutoID IS NULL ")->result_array();

        /** General Tax Return GL Entry */
        $GLEntry_tax = $this->db->query("SELECT
                                    srp_erp_salesreturntaxdetails.segmentID AS segmentID,
                                    'dr' AS amountType,
                                    NULL AS subLedgerType,
                                    NULL AS subLedgerDesc,
                                    GLAutoID AS GLAutoID,
                                    SystemGLCode AS SystemGLCode,
                                    GLCode AS GLSecondaryCode,
                                    GLDescription AS GLDescription,
                                    srp_erp_salesreturntaxdetails.segmentCode AS segmentCode,
                                    GLType AS GLType,
                                    ((((totalValue / 100) * (100 - IFNULL(discountPer, 0))) / 100) * taxPercentage) AS debit,
                                    0 AS credit,
                                    ((((totalValue / 100) * (100 - IFNULL(discountPer, 0))) / 100) * taxPercentage) AS transactionAmount,
                                    srp_erp_salesreturnmaster.transactionCurrencyDecimalPlaces AS transactionDecimal,
                                    approvedYN AS approvedYN,
                                    srp_erp_salesreturnmaster.salesReturnAutoID AS auto_id
                                FROM
                                    srp_erp_salesreturntaxdetails
                                    LEFT JOIN srp_erp_salesreturndetails ON srp_erp_salesreturntaxdetails.salesReturnAutoID = srp_erp_salesreturndetails.salesReturnAutoID 
                                    AND srp_erp_salesreturntaxdetails.salesReturnDetailsID = srp_erp_salesreturndetails.salesReturnDetailsID
                                    LEFT JOIN (SELECT SUM(discountPercentage) AS discountPer, salesReturnDetailsID FROM srp_erp_salesreturndiscountdetails GROUP BY salesReturnDetailsID ) discountTable ON discountTable.salesReturnDetailsID = srp_erp_salesreturndetails.salesReturnDetailsID
                                    LEFT JOIN srp_erp_salesreturnmaster ON srp_erp_salesreturnmaster.salesReturnAutoID = srp_erp_salesreturntaxdetails.salesReturnAutoID 
                                WHERE
                                    srp_erp_salesreturntaxdetails.salesReturnAutoID = {$salesReturnAutoID} 
                                    AND srp_erp_salesreturntaxdetails.companyID = {$companyID}")->result_array();

        /** Invoice Discount GL Entry */
        $GLEntry_discount = $this->db->query("SELECT
                                    srp_erp_salesreturndiscountdetails.segmentID AS segmentID,
                                    'cr' AS amountType,
                                    NULL AS subLedgerType,
                                    NULL AS subLedgerDesc,
                                    GLAutoID AS GLAutoID,
                                    SystemGLCode AS SystemGLCode,
                                    GLCode AS GLSecondaryCode,
                                    GLDescription AS GLDescription,
                                    srp_erp_salesreturndiscountdetails.segmentCode AS segmentCode,
                                    GLType AS GLType,
                                    discountPercentage,totalValue,
                                    ((totalValue / 100) * (IFNULL(discountPercentage, 0))) * -1 AS credit,
                                    0 AS debit,
                                    ((totalValue / 100) * (IFNULL(discountPercentage, 0))) * -1 AS transactionAmount,
                                    srp_erp_salesreturnmaster.transactionCurrencyDecimalPlaces AS transactionDecimal,
                                    approvedYN AS approvedYN,
                                    srp_erp_salesreturnmaster.salesReturnAutoID AS auto_id 
                                FROM
                                    srp_erp_salesreturndiscountdetails
                                    LEFT JOIN srp_erp_salesreturndetails ON srp_erp_salesreturndiscountdetails.salesReturnAutoID = srp_erp_salesreturndetails.salesReturnAutoID 
                                    AND srp_erp_salesreturndiscountdetails.salesReturnDetailsID = srp_erp_salesreturndetails.salesReturnDetailsID
                                    LEFT JOIN srp_erp_salesreturnmaster ON srp_erp_salesreturnmaster.salesReturnAutoID = srp_erp_salesreturndiscountdetails.salesReturnAutoID 
                                WHERE
                                    srp_erp_salesreturndiscountdetails.salesReturnAutoID = {$salesReturnAutoID}
                                    AND isChargeToExpense = 1
                                    AND srp_erp_salesreturndiscountdetails.companyID = {$companyID} HAVING credit != 0")->result_array();

        if ($RebateGLEntry_receivable) {
            $GLEntries = array_merge($GLEntry_cost, $GLEntry_inventory, $GLEntry_revenue, $GLEntry_receivable, $RebateGLEntry_receivable/*, $GLEntry_UBI*/, $GLEntry_tax, $GLEntry_discount, $GLEntry_VAT);
        } else {
            $GLEntries = array_merge($GLEntry_cost, $GLEntry_inventory, $GLEntry_revenue, $GLEntry_receivable/*, $GLEntry_UBI*/, $GLEntry_tax, $GLEntry_discount, $GLEntry_VAT);
        }

        /*setup GL entries */
        $data['GLEntries'] = $GLEntries;

        /*setup master data */
        $data['name'] = 'Sales Return ';
        $data['code'] = 'SLR';
        $data['date'] = $master['returnDate'];
        $data['approved_yn'] = $master['approvedYN'];
        $data['currency'] = $master['transactionCurrency'];
        $data['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $data['primary_Code'] = $master['salesReturnCode'];
        $data['finance_year'] = $master['companyFinanceYear'];
        $data['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $data['approvedYN'] = $master['approvedYN'];

        $data['master_data'] = $master;
        $data['customername'] = $master['customerNamemaster'];

        return $data;


        /*

            $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode ,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate, supplierCurrencyDecimalPlaces,supplierCurrencyID');
            $this->db->where('invoiceAutoID', $salesReturnAutoID);
            $tax_detail = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();


            $cr_p_arr = array();
            $cr_m_arr = array();
            $e_cr_p_arr = array();
            $e_cr_m_arr = array();
            $item_arr = array();

            $item_tax_arr = array();

            for ($i = 0; $i < count($detail); $i++) {

                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['revenueGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['revenueSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['revenueGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['revenueGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['revenueGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = null;
                $data_arr['amount_type'] = 'dr';
                $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                if ($amount <= 0) {
                    $data_arr['gl_dr'] = $amount;
                    $data_arr['gl_cr'] = 0;
                    array_push($cr_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $amount;
                    $data_arr['amount_type'] = 'cr';
                    array_push($cr_m_arr, $data_arr);
                }

                $cr_total += $data_arr['gl_cr'];

                if ($detail[$i]['taxAmount'] != 0) {
                    $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                    $data_arr['gl_auto_id'] = $detail[$i]['taxSupplierliabilityAutoID'];
                    $data_arr['gl_code'] = $detail[$i]['taxSupplierliabilitySystemGLCode'];
                    $data_arr['secondary'] = $detail[$i]['taxSupplierliabilityGLAccount'];
                    $data_arr['gl_desc'] = $detail[$i]['taxSupplierliabilityDescription'];
                    $data_arr['gl_type'] = $detail[$i]['taxSupplierliabilityType'];
                    $data_arr['segment_id'] = $master['segmentID'];
                    $data_arr['segment'] = $master['segmentCode'];
                    $data_arr['isAddon'] = 0;
                    $data_arr['subLedgerType'] = 2;
                    $data_arr['subLedgerDesc'] = 'AP';
                    $data_arr['partyContractID'] = null;
                    $data_arr['partyType'] = 'SUP';
                    $data_arr['partyAutoID'] = $detail[$i]['taxSupplierAutoID'];
                    $data_arr['partySystemCode'] = $detail[$i]['taxSupplierSystemCode'];
                    $data_arr['partyName'] = $detail[$i]['taxSupplierName'];
                    $data_arr['partyCurrencyID'] = $detail[$i]['taxSupplierCurrencyID'];
                    $data_arr['partyCurrency'] = $detail[$i]['taxSupplierCurrency'];
                    $data_arr['transactionExchangeRate'] = null;
                    $data_arr['companyLocalExchangeRate'] = null;
                    $data_arr['companyReportingExchangeRate'] = null;
                    $data_arr['partyExchangeRate'] = $detail[$i]['taxSupplierCurrencyExchangeRate'];
                    $data_arr['partyCurrencyAmount'] = $detail[$i]['taxSupplierliabilityType'];
                    $data_arr['partyCurrencyDecimalPlaces'] = $detail[$i]['taxSupplierCurrencyDecimalPlaces'];
                    $data_arr['amount_type'] = 'cr';
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = ($detail[$i]['taxAmount'] * $detail[$i]['requestedQty']);
                    array_push($item_arr, $data_arr);
                    $cr_total += $data_arr['gl_cr'];
                }

                if ($detail[$i]['type'] == 'Item' && $detail[$i]['itemCategory'] == 'Inventory') {
                    $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                    $data_arr['gl_auto_id'] = $detail[$i]['expenseGLAutoID'];
                    $data_arr['gl_code'] = $detail[$i]['expenseSystemGLCode'];
                    $data_arr['secondary'] = $detail[$i]['expenseGLCode'];
                    $data_arr['gl_desc'] = $detail[$i]['expenseGLDescription'];
                    $data_arr['gl_type'] = $detail[$i]['expenseGLType'];
                    $data_arr['segment_id'] = $master['segmentID'];
                    $data_arr['segment'] = $master['segmentCode'];
                    $data_arr['isAddon'] = 0;
                    $data_arr['subLedgerType'] = 0;
                    $data_arr['subLedgerDesc'] = null;
                    $data_arr['partyContractID'] = null;
                    $data_arr['partyType'] = 'CUS';
                    $data_arr['partyAutoID'] = $master['customerID'];
                    $data_arr['partySystemCode'] = $master['customerSystemCode'];
                    $data_arr['partyName'] = $master['customerName'];
                    $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                    $data_arr['partyCurrency'] = $master['customerCurrency'];
                    $data_arr['transactionExchangeRate'] = null;
                    $data_arr['companyLocalExchangeRate'] = null;
                    $data_arr['companyReportingExchangeRate'] = null;
                    $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                    $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                    $data_arr['amount_type'] = 'dr';
                    $data_arr['gl_dr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                    $data_arr['gl_cr'] = 0;
                    array_push($item_arr, $data_arr);
                    $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                    $data_arr['gl_auto_id'] = $detail[$i]['assetGLAutoID'];
                    $data_arr['gl_code'] = $detail[$i]['assetSystemGLCode'];
                    $data_arr['secondary'] = $detail[$i]['assetGLCode'];
                    $data_arr['gl_desc'] = $detail[$i]['assetGLDescription'];
                    $data_arr['gl_type'] = $detail[$i]['assetGLType'];
                    $data_arr['segment_id'] = $master['segmentID'];
                    $data_arr['segment'] = $master['segmentCode'];
                    $data_arr['isAddon'] = 0;
                    $data_arr['subLedgerType'] = 0;
                    $data_arr['subLedgerDesc'] = null;
                    $data_arr['partyContractID'] = null;
                    $data_arr['partyType'] = 'CUS';
                    $data_arr['partyAutoID'] = $master['customerID'];
                    $data_arr['partySystemCode'] = $master['customerSystemCode'];
                    $data_arr['partyName'] = $master['customerName'];
                    $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                    $data_arr['partyCurrency'] = $master['customerCurrency'];
                    $data_arr['transactionExchangeRate'] = null;
                    $data_arr['companyLocalExchangeRate'] = null;
                    $data_arr['companyReportingExchangeRate'] = null;
                    $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                    $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                    $data_arr['amount_type'] = 'cr';
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                    array_push($item_arr, $data_arr);
                }
                $tax_total += ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
            }


            for ($i = 0; $i < count($tax_detail); $i++) {
                $data_arr['auto_id'] = $tax_detail[$i]['taxDetailAutoID'];
                $data_arr['gl_auto_id'] = $tax_detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $tax_detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $tax_detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $tax_detail[$i]['GLDescription'] . ' - Tax All';
                $data_arr['gl_type'] = $tax_detail[$i]['GLType'];
                $data_arr['segment_id'] = $tax_detail[$i]['segmentID'];
                $data_arr['segment'] = $tax_detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 2;
                $data_arr['subLedgerDesc'] = 'AP';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'SUP';
                $data_arr['partyAutoID'] = $tax_detail[$i]['supplierAutoID'];
                $data_arr['partySystemCode'] = $tax_detail[$i]['supplierSystemCode'];
                $data_arr['partyName'] = $tax_detail[$i]['supplierName'];
                $data_arr['partyCurrencyID'] = $tax_detail[$i]['supplierCurrencyID'];
                $data_arr['partyCurrency'] = $tax_detail[$i]['supplierCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $tax_detail[$i]['supplierCurrencyExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $tax_detail[$i]['supplierCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = (($tax_detail[$i]['taxPercentage'] / 100) * $tax_total);
                array_push($gl_array['gl_detail'], $data_arr);
                $cr_total += $data_arr['gl_cr'];
            }

            $cr_p_arr = $this->array_group_sum($cr_p_arr);
            $cr_m_arr = $this->array_group_sum($cr_m_arr);
            $item_arr = $this->array_group_sum($item_arr);


            foreach ($cr_m_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
            foreach ($cr_p_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
            foreach ($item_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
            foreach ($e_cr_m_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
            foreach ($e_cr_p_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }


            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $master['customerReceivableAutoID'];
            $data_arr['gl_code'] = $master['customerReceivableSystemGLCode'];
            $data_arr['secondary'] = $master['customerReceivableGLAccount'];
            $data_arr['gl_desc'] = $master['customerReceivableDescription'];
            $data_arr['gl_type'] = $master['customerReceivableType'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = $cr_total;
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 3;
            $data_arr['subLedgerDesc'] = 'AR';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            array_push($gl_array['gl_detail'], $data_arr);

            $gl_array['currency'] = $master['transactionCurrency'];
            $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
            $gl_array['code'] = 'SLR';
            $gl_array['name'] = 'Customer Invoice';
            $gl_array['primary_Code'] = $master['invoiceCode'];
            $gl_array['date'] = $master['invoiceDate'];
            $gl_array['finance_year'] = $master['companyFinanceYear'];
            $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
            $gl_array['master_data'] = $master;

            return $gl_array;*/
    }

    function fetch_double_entry_material_receipt_data($mrnAutoID, $code = null)
    {
        $gl_array = array();
        $cost_arr = array();
        $assat_arr = array();
        $gl_array['gl_detail'] = array();
        $this->db->select('*');
        $this->db->where('mrnAutoID', $mrnAutoID);
        $master = $this->db->get('srp_erp_materialreceiptmaster')->row_array();

        $this->db->select('financeCategory,PLGLAutoID,PLSystemGLCode,PLGLCode,PLDescription,BLGLAutoID,BLSystemGLCode ,PLType ,BLGLCode,BLDescription,BLType,totalValue,segmentCode,segmentID,projectID,projectExchangeRate,project_categoryID,project_subCategoryID');
        $this->db->where('itemCategory !=', 'Non Inventory');
        $this->db->where('mrnAutoID', $mrnAutoID);
        $detail = $this->db->get('srp_erp_materialreceiptdetails')->result_array();
        for ($i = 0; $i < count($detail); $i++) {
            $assa_data_arr['auto_id'] = 0;
            $assa_data_arr['gl_auto_id'] = $detail[$i]['BLGLAutoID'];
            $assa_data_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
            $assa_data_arr['secondary'] = $detail[$i]['BLGLCode'];
            $assa_data_arr['gl_desc'] = $detail[$i]['BLDescription'];
            $assa_data_arr['gl_type'] = $detail[$i]['BLType'];
            $assa_data_arr['segment_id'] = $detail[$i]['segmentID'];
            $assa_data_arr['segment'] = $detail[$i]['segmentCode'];
            $assa_data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            $assa_data_arr['project_categoryID'] = isset($detail[$i]['project_categoryID']) ? $detail[$i]['project_categoryID'] : null;
            $assa_data_arr['project_subCategoryID'] = isset($detail[$i]['project_subCategoryID']) ? $detail[$i]['project_subCategoryID'] : null;
            $assa_data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $assa_data_arr['gl_dr'] = $detail[$i]['totalValue'];
            $assa_data_arr['gl_cr'] = 0;
            $assa_data_arr['amount_type'] = 'dr';
            $assa_data_arr['isAddon'] = 0;
            $assa_data_arr['subLedgerType'] = 0;
            $assa_data_arr['subLedgerDesc'] = null;
            $assa_data_arr['partyContractID'] = null;
            $assa_data_arr['partyType'] = null;
            $assa_data_arr['partyAutoID'] = null;
            $assa_data_arr['partySystemCode'] = null;
            $assa_data_arr['partyName'] = null;
            $assa_data_arr['partyCurrencyID'] = null;
            $assa_data_arr['partyCurrency'] = null;
            $assa_data_arr['transactionExchangeRate'] = null;
            $assa_data_arr['companyLocalExchangeRate'] = null;
            $assa_data_arr['companyReportingExchangeRate'] = null;
            $assa_data_arr['partyExchangeRate'] = null;
            $assa_data_arr['partyCurrencyAmount'] = null;
            $assa_data_arr['partyCurrencyDecimalPlaces'] = null;
            array_push($assat_arr, $assa_data_arr);

            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $detail[$i]['PLGLAutoID'];
            $data_arr['gl_code'] = $detail[$i]['PLSystemGLCode'];
            $data_arr['secondary'] = $detail[$i]['PLGLCode'];
            $data_arr['gl_desc'] = $detail[$i]['PLDescription'];
            $data_arr['gl_type'] = $detail[$i]['PLType'];
            $data_arr['segment_id'] = $detail[$i]['segmentID'];
            $data_arr['segment'] = $detail[$i]['segmentCode'];
            $data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            $data_arr['project_categoryID'] = isset($detail[$i]['project_categoryID']) ? $detail[$i]['project_categoryID'] : null;
            $data_arr['project_subCategoryID'] = isset($detail[$i]['project_subCategoryID']) ? $detail[$i]['project_subCategoryID'] : null;
            $data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = $detail[$i]['totalValue'];
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = null;
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = null;
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = null;
            $data_arr['partyCurrencyAmount'] = null;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'cr';
            array_push($cost_arr, $data_arr);
        }

        $assat_arr = $this->array_group_sum_pm($assat_arr);
        $cost_arr = $this->array_group_sum_pm($cost_arr);

        $gl_array['gl_detail'] = $assat_arr;
        foreach ($cost_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        $gl_array['currency'] = $master['companyLocalCurrency'];
        $gl_array['decimal_places'] = $master['companyLocalCurrencyDecimalPlaces'];
        $gl_array['code'] = 'MRN';
        $gl_array['name'] = 'Material Receipt Note';
        $gl_array['primary_Code'] = $master['mrnCode'];
        $gl_array['date'] = $master['receivedDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }

    function fetch_double_entry_donor_collection($collectionAutoId, $code = null)
    {
        $gl_array = array();
        $inv_total = 0;
        $dr_total = 0;
        $party_total = 0;
        $companyLocal_total = 0;
        $companyReporting_total = 0;
        $tax_total = 0;
        $gl_array['gl_detail'] = array();
        $this->db->select('*');
        $this->db->where('collectionAutoId', $collectionAutoId);
        $master = $this->db->get('srp_erp_ngo_donorcollectionmaster')->row_array();

        $this->db->select('collectionDetailAutoID ,collectionAutoId ,projectID ,commitmentAutoID ,type ,itemAutoID ,itemSystemCode ,itemDescription ,itemCategory ,expenseGLAutoID ,expenseGLCode ,expenseSystemGLCode ,expenseGLDescription ,expenseGLType ,revenueGLAutoID ,revenueGLCode ,revenueSystemGLCode ,revenueGLDescription ,revenueGLType ,assetGLAutoID ,assetGLCode ,assetSystemGLCode ,assetGLDescription ,assetGLType ,wareHouseAutoID ,wareHouseCode ,wareHouseLocation ,wareHouseDescription ,defaultUOMID ,defaultUOM ,unitOfMeasureID ,unitOfMeasure ,conversionRateUOM ,itemQty ,description ,GLAutoID ,SystemGLCode ,GLCode ,GLDescription ,GLType ,commitmentExpiryDate ,unittransactionAmount ,transactionAmount ,companyLocalWacAmount ,unitcompanyLocalAmount ,companyLocalAmount ,companyLocalExchangeRate ,unitcompanyReportingAmount ,companyReportingAmount ,companyReportingExchangeRate ,unitDonoursAmount ,donorsAmount ,donorsExchangeRate ,companyID ,createdUserGroup ,createdPCID ,createdUserID ,createdDateTime ,createdUserName ,modifiedPCID ,modifiedUserID ,modifiedDateTime ,modifiedUserName ,timestamp ');
        $this->db->where('collectionAutoId', $collectionAutoId);
        $detail = $this->db->get('srp_erp_ngo_donorcollectiondetails')->result_array();

        /* $keys = array_keys(array_column($detail, 'type'), 1);
         $cashDetail = array_map(function ($k) use ($detail) {
           return $detail[$k];
         }, $keys);*/

        $creditGL = $this->db->query("SELECT srp_erp_ngo_donorcollectiondetails.*,srp_erp_ngo_projects.segmentID,segmentCode,sum(srp_erp_ngo_donorcollectiondetails.transactionAmount) as amount FROM `srp_erp_ngo_donorcollectiondetails` LEFT JOIN srp_erp_ngo_projects ON ngoProjectID = projectID LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentID = srp_erp_ngo_projects.segmentID WHERE collectionAutoId=$collectionAutoId GROUP BY GLAutoID, srp_erp_ngo_projects.segmentID ")->result_array();

        $this->db->select('*');
        $this->db->where('contactID', $master['donorsID']);
        $donorDetail = $this->db->get('srp_erp_ngo_donors')->row_array();

        $globalArray = array();
        /*creditGL*/
        if ($creditGL) {
            foreach ($creditGL as $credit) {
                $data_arr['auto_id'] = $credit['collectionAutoId'];
                $data_arr['gl_auto_id'] = $credit['GLAutoID'];
                $data_arr['gl_code'] = $credit['SystemGLCode'];
                $data_arr['secondary'] = $credit['GLCode'];
                $data_arr['gl_desc'] = $credit['GLDescription'];
                $data_arr['gl_type'] = $credit['GLType'];
                $data_arr['segment_id'] = $credit['segmentID'];
                $data_arr['segment'] = $credit['segmentCode'];
                $data_arr['projectID'] = $credit['projectID'];
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'DON';
                $data_arr['partyAutoID'] = $master['donorsID'];
                $data_arr['partySystemCode'] = NULL;
                $data_arr['partyName'] = $donorDetail['name'];
                $data_arr['partyCurrencyID'] = $master['donorCurrencyID'];
                $data_arr['partyCurrency'] = fetch_currency_code($master['donorCurrencyID']);
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['donorExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['donorCurrencyDecimalPlaces'];
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $credit['amount'];
                $data_arr['amount_type'] = 'cr';
                array_push($globalArray, $data_arr);
            }
        }

        /*item  GL*/
        $assetGL = $this->db->query("SELECT srp_erp_ngo_donorcollectiondetails.*,srp_erp_ngo_projects.segmentID,segmentCode,sum(srp_erp_ngo_donorcollectiondetails.transactionAmount) as amount FROM `srp_erp_ngo_donorcollectiondetails` LEFT JOIN srp_erp_ngo_projects ON ngoProjectID = projectID LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentID = srp_erp_ngo_projects.segmentID WHERE collectionAutoId=$collectionAutoId AND type=2 GROUP BY GLAutoID, srp_erp_ngo_projects.segmentID,assetGLAutoID ")->result_array();
        if ($assetGL) {
            foreach ($assetGL as $asset) {
                $data_arr['auto_id'] = $asset['collectionAutoId'];
                $data_arr['gl_auto_id'] = $asset['assetGLAutoID'];
                $data_arr['gl_code'] = $asset['assetGLCode'];
                $data_arr['secondary'] = $asset['assetSystemGLCode'];
                $data_arr['gl_desc'] = $asset['assetGLDescription'];
                $data_arr['gl_type'] = $asset['assetGLType'];
                $data_arr['segment_id'] = $asset['segmentID'];
                $data_arr['segment'] = $asset['segmentCode'];
                $data_arr['projectID'] = $asset['projectID'];
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'DON';
                $data_arr['partyAutoID'] = $master['donorsID'];
                $data_arr['partySystemCode'] = NULL;
                $data_arr['partyName'] = $donorDetail['name'];
                $data_arr['partyCurrencyID'] = $master['donorCurrencyID'];
                $data_arr['partyCurrency'] = fetch_currency_code($master['donorCurrencyID']);
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['donorExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['donorCurrencyDecimalPlaces'];
                $data_arr['gl_dr'] = $asset['amount'];
                $data_arr['gl_cr'] = 0;
                $data_arr['amount_type'] = 'dr';
                array_push($globalArray, $data_arr);
            }
        }

        /*cash bank GL*/
        $bankGL = $this->db->query("SELECT srp_erp_ngo_donorcollectiondetails.*,srp_erp_ngo_projects.segmentID,segmentCode,sum(srp_erp_ngo_donorcollectiondetails.transactionAmount) as amount FROM `srp_erp_ngo_donorcollectiondetails` LEFT JOIN srp_erp_ngo_projects ON ngoProjectID = projectID LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentID = srp_erp_ngo_projects.segmentID WHERE collectionAutoId=$collectionAutoId AND type=1 GROUP BY GLAutoID, srp_erp_ngo_projects.segmentID ")->result_array();
        if ($bankGL) {
            foreach ($bankGL as $bank) {
                $GL = $this->db->query("select * from srp_erp_chartofaccounts WHERE GLAutoID = {$master['bankGLAutoID']} ")->row_array();
                $data_arr['auto_id'] = $bank['collectionAutoId'];
                $data_arr['gl_auto_id'] = $master['bankGLAutoID'];
                $data_arr['gl_code'] = $master['bankSystemAccountCode'];
                $data_arr['secondary'] = $master['bankGLSecondaryCode'];
                $data_arr['gl_desc'] = $GL['GLDescription'];
                $data_arr['gl_type'] = $master['DCbankType'];
                $data_arr['segment_id'] = $bank['segmentID'];
                $data_arr['segment'] = $bank['segmentCode'];
                $data_arr['projectID'] = $bank['projectID'];
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'DON';
                $data_arr['partyAutoID'] = $master['donorsID'];
                $data_arr['partySystemCode'] = NULL;
                $data_arr['partyName'] = $donorDetail['name'];
                $data_arr['partyCurrencyID'] = $master['donorCurrencyID'];
                $data_arr['partyCurrency'] = fetch_currency_code($master['donorCurrencyID']);
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['donorExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['donorCurrencyDecimalPlaces'];
                $data_arr['gl_dr'] = $bank['amount'];
                $data_arr['gl_cr'] = 0;
                $data_arr['amount_type'] = 'dr';
                array_push($globalArray, $data_arr);
            }
        }


        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'DC';
        $gl_array['name'] = 'Donor Collection';
        $gl_array['primary_Code'] = $master['documentSystemCode'];
        $gl_array['date'] = $master['documentDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['donor'] = $donorDetail;
        $gl_array['gl_detail'] = $globalArray;


        return $gl_array;
    }


    function fetch_double_entry_payment_voucher_prvr($pvautoid)
    {
        $gl_array = array();
        $globle_array = array();
        //$pvautoid = $this->input->post('payVoucherAutoId');
        $this->db->select('*');
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->where('payVoucherAutoId', trim($pvautoid));
        $pvmaster = $this->db->get()->row_array();

        $this->db->select('sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->where('payVoucherAutoId', trim($pvautoid));
        $pvdetail = $this->db->get()->row_array();

        $pvdetailSupp = $this->db->query('SELECT
                sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount
            FROM srp_erp_paymentvoucherdetail
            WHERE srp_erp_paymentvoucherdetail.payVoucherAutoId = ' . $pvautoid . '
            AND (type = "Invoice" OR type = "Advance" OR type = "debitnote")')->row_array();
        $companyid = current_companyID();


        $date_format_policy = date_format_policy();
        $date = $this->input->post('reversalDate');
        $reversalDate = input_format_date($date, $date_format_policy);

        $str = $reversalDate;
        $dat = explode("-", $str);


        $companyFinanceYearID = $this->db->query('SELECT
                companyFinanceYearID
            FROM
                srp_erp_companyfinanceperiod
            WHERE
                companyID = ' . $companyid . '
            AND isActive = 1
            AND (
                "' . $reversalDate . '" BETWEEN dateFrom
                AND dateTo
            )')->row_array();
                    $invoiceGlDetails = $this->db->query('SELECT
                SUM(srp_erp_paymentvoucherdetail.transactionAmount) as Amount,SUM(srp_erp_paymentvoucherdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_paymentvoucherdetail.companyReportingAmount) as compReportingAmount,srp_erp_suppliermaster.liabilityAutoID,srp_erp_chartofaccounts.GLAutoID,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
            FROM srp_erp_paymentvouchermaster
            LEFT JOIN srp_erp_paymentvoucherdetail on srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId
            LEFT JOIN srp_erp_suppliermaster on srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID
            LEFT JOIN srp_erp_chartofaccounts on srp_erp_suppliermaster.liabilityAutoID = srp_erp_chartofaccounts.GLAutoID
            WHERE srp_erp_paymentvouchermaster.payVoucherAutoId = ' . $pvautoid . '
            AND (type = "Invoice" OR type = "Advance" OR type = "debitnote")')->row_array();


        $ItemGlDetails = $this->db->query('SELECT
                SUM(srp_erp_paymentvoucherdetail.transactionAmount) as Amount,SUM(srp_erp_paymentvoucherdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_paymentvoucherdetail.companyReportingAmount) as compReportingAmount,srp_erp_chartofaccounts.GLAutoID,addondet.taxPercentage as taxPercentage,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
            FROM srp_erp_paymentvouchermaster
            LEFT JOIN srp_erp_paymentvoucherdetail on srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId
            LEFT JOIN srp_erp_suppliermaster on srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID
            LEFT JOIN (
                SELECT
                    SUM(taxPercentage) AS taxPercentage,
                    payVoucherAutoId
                FROM
                    srp_erp_paymentvouchertaxdetails
                GROUP BY
                    payVoucherAutoId
            ) addondet ON (
                `addondet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId
            )
            LEFT JOIN srp_erp_companycontrolaccounts on srp_erp_companycontrolaccounts.controlAccountType = "PRVR"
            LEFT JOIN srp_erp_chartofaccounts on srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID
            WHERE srp_erp_paymentvouchermaster.payVoucherAutoId = ' . $pvautoid . '
            AND srp_erp_companycontrolaccounts.companyID=' . $companyid . '
            AND (type = "GL" OR type = "Item")')->row_array();


        $bankGlBankLeager = $this->db->query('SELECT
            SUM(srp_erp_paymentvoucherdetail.transactionAmount) as Amount,SUM(srp_erp_paymentvoucherdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_paymentvoucherdetail.companyReportingAmount) as compReportingAmount,addondet.taxPercentage as taxPercentage,srp_erp_chartofaccounts.GLAutoID,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
        FROM srp_erp_paymentvouchermaster
        LEFT JOIN srp_erp_paymentvoucherdetail on srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId
        LEFT JOIN (
            SELECT
                SUM(taxPercentage) AS taxPercentage,
                payVoucherAutoId
            FROM
                srp_erp_paymentvouchertaxdetails
            GROUP BY
                payVoucherAutoId
        ) addondet ON (
            `addondet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId
        )
        LEFT JOIN srp_erp_chartofaccounts on srp_erp_paymentvouchermaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID
        WHERE srp_erp_paymentvouchermaster.payVoucherAutoId = ' . $pvautoid . '')->row_array();

        if ($pvmaster['pvType'] == 'Supplier' || $pvmaster['pvType'] == 'SupplierAdvance' || $pvmaster['pvType'] == 'SupplierInvoice') {
            if (!empty($invoiceGlDetails)) {
                $invGLLia['GLAutoID'] = $invoiceGlDetails['GLAutoID'];
                $invGLLia['gl_code'] = $invoiceGlDetails['systemAccountCode'];
                $invGLLia['secondary'] = $invoiceGlDetails['GLSecondaryCode'];
                $invGLLia['gl_desc'] = $invoiceGlDetails['GLDescription'];
                $invGLLia['gl_type'] = $invoiceGlDetails['subCategory'];
                $invGLLia['segment'] = null;
                $invGLLia['amount_type'] = 'cr';
                $invGLLia['transactionCurrency'] = $pvmaster['transactionCurrency'];
                $invGLLia['transactionExchangeRate'] = $pvmaster['transactionExchangeRate'];
                $invGLLia['gl_cr'] = $invoiceGlDetails['Amount'] * -1;
                $invGLLia['gl_dr'] = 0;
                $invGLLia['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];
                $invGLLia['partyAutoID'] = $pvmaster['partyID'];
                $invGLLia['partyName'] = $pvmaster['partyName'];
                $invGLLia['subLedgerType'] = 2;
                $invGLLia['subLedgerDesc'] = 'AP';
                $invGLLia['approvedDate'] = $this->common_data['current_date'];
                $invGLLia['approvedbyEmpID'] = $this->common_data['current_userID'];
                $invGLLia['approvedbyEmpName'] = $this->common_data['current_user'];
                $invGLLia['companyCode'] = $this->common_data['company_data']['company_code'];
                $invGLLia['companyID'] = $this->common_data['company_data']['company_id'];

                array_push($globle_array, $invGLLia);
            }

            if (!empty($ItemGlDetails['GLAutoID'])) {
                $invGL['GLAutoID'] = $ItemGlDetails['GLAutoID'];
                $invGL['gl_code'] = $ItemGlDetails['systemAccountCode'];
                $invGL['secondary'] = $ItemGlDetails['GLSecondaryCode'];
                $invGL['gl_desc'] = $ItemGlDetails['GLDescription'];
                $invGL['gl_type'] = $ItemGlDetails['subCategory'];
                $invGL['amount_type'] = 'cr';
                $invGL['segment'] = null;
                $invGL['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                $invGL['transactionCurrency'] = $pvmaster['transactionCurrency'];
                $invGL['transactionExchangeRate'] = $pvmaster['transactionExchangeRate'];
                if ($ItemGlDetails['taxPercentage'] == null) {
                    $taxPercentage = 0;
                } else {
                    $taxPercentage = $ItemGlDetails['taxPercentage'];
                }
                $itemTaxamount = ($ItemGlDetails['Amount'] / 100) * $taxPercentage;
                $invGL['gl_cr'] = ($ItemGlDetails['Amount'] + $itemTaxamount) * -1;
                $invGL['gl_dr'] = 0;
                $invGL['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];
                $invGL['partyAutoID'] = $pvmaster['partyID'];
                $invGL['partyName'] = $pvmaster['partyName'];
                $invGL['approvedDate'] = $this->common_data['current_date'];
                $invGL['approvedbyEmpID'] = $this->common_data['current_userID'];
                $invGL['approvedbyEmpName'] = $this->common_data['current_user'];
                $invGL['companyCode'] = $this->common_data['company_data']['company_code'];
                $invGL['companyID'] = $this->common_data['company_data']['company_id'];
                array_push($globle_array, $invGL);
            }

            if (!empty($bankGlBankLeager['GLAutoID'])) {
                $invGLbnk['GLAutoID'] = $bankGlBankLeager['GLAutoID'];
                $invGLbnk['gl_code'] = $bankGlBankLeager['systemAccountCode'];
                $invGLbnk['secondary'] = $bankGlBankLeager['GLSecondaryCode'];
                $invGLbnk['gl_desc'] = $bankGlBankLeager['GLDescription'];
                $invGLbnk['gl_type'] = $bankGlBankLeager['subCategory'];
                $invGLbnk['amount_type'] = 'dr';
                $invGLbnk['segment'] = null;
                $invGLbnk['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                $invGLbnk['transactionCurrency'] = $pvmaster['transactionCurrency'];
                $invGLbnk['transactionExchangeRate'] = $pvmaster['transactionExchangeRate'];
                if ($ItemGlDetails['taxPercentage'] == null) {
                    $taxPercentageb = 0;
                } else {
                    $taxPercentageb = $bankGlBankLeager['taxPercentage'];
                }
                $bankTaxamount = ($ItemGlDetails['Amount'] / 100) * $taxPercentageb;
                $invGLbnk['gl_dr'] = $bankGlBankLeager['Amount'] + $bankTaxamount;
                $invGLbnk['gl_cr'] = 0;
                $invGLbnk['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];
                $invGLbnk['partyAutoID'] = $pvmaster['partyID'];
                $invGLbnk['partyName'] = $pvmaster['partyName'];
                $invGLbnk['approvedDate'] = $this->common_data['current_date'];
                $invGLbnk['approvedbyEmpID'] = $this->common_data['current_userID'];
                $invGLbnk['approvedbyEmpName'] = $this->common_data['current_user'];
                $invGLbnk['companyCode'] = $this->common_data['company_data']['company_code'];
                $invGLbnk['companyID'] = $this->common_data['company_data']['company_id'];
                array_push($globle_array, $invGLbnk);
            }
        } else {
            if (!empty($ItemGlDetails['GLAutoID'])) {
                $invGL['GLAutoID'] = $ItemGlDetails['GLAutoID'];
                $invGL['gl_code'] = $ItemGlDetails['systemAccountCode'];
                $invGL['secondary'] = $ItemGlDetails['GLSecondaryCode'];
                $invGL['gl_desc'] = $ItemGlDetails['GLDescription'];
                $invGL['gl_type'] = $ItemGlDetails['subCategory'];
                $invGL['amount_type'] = 'cr';
                $invGL['segment'] = null;
                $invGL['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                $invGL['transactionCurrency'] = $pvmaster['transactionCurrency'];
                $invGL['transactionExchangeRate'] = $pvmaster['transactionExchangeRate'];
                if (empty($ItemGlDetails['taxPercentage'])) {
                    $ItemGlDetails['taxPercentage'] = 0;
                }
                if ($ItemGlDetails['taxPercentage'] == null) {
                    $taxPercentage = 0;
                } else {
                    $taxPercentage = $ItemGlDetails['taxPercentage'];
                }
                $itemTaxamount = ($ItemGlDetails['Amount'] / 100) * $taxPercentage;
                $invGL['gl_cr'] = ($ItemGlDetails['Amount'] + $itemTaxamount) * -1;
                $invGL['gl_dr'] = 0;
                $invGL['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];
                $invGL['partyAutoID'] = $pvmaster['partyID'];
                $invGL['partyName'] = $pvmaster['partyName'];
                $invGL['approvedDate'] = $this->common_data['current_date'];
                $invGL['approvedbyEmpID'] = $this->common_data['current_userID'];
                $invGL['approvedbyEmpName'] = $this->common_data['current_user'];
                $invGL['companyCode'] = $this->common_data['company_data']['company_code'];
                $invGL['companyID'] = $this->common_data['company_data']['company_id'];
                array_push($globle_array, $invGL);
            }
            if (!empty($bankGlBankLeager['GLAutoID'])) {
                $invGLbnk['GLAutoID'] = $bankGlBankLeager['GLAutoID'];
                $invGLbnk['gl_code'] = $bankGlBankLeager['systemAccountCode'];
                $invGLbnk['secondary'] = $bankGlBankLeager['GLSecondaryCode'];
                $invGLbnk['gl_desc'] = $bankGlBankLeager['GLDescription'];
                $invGLbnk['gl_type'] = $bankGlBankLeager['subCategory'];
                $invGLbnk['segment'] = null;
                $invGLbnk['amount_type'] = 'dr';
                $invGLbnk['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                $invGLbnk['transactionCurrency'] = $pvmaster['transactionCurrency'];
                $invGLbnk['transactionExchangeRate'] = $pvmaster['transactionExchangeRate'];
                if ($ItemGlDetails['taxPercentage'] == null) {
                    $taxPercentageb = 0;
                } else {
                    $taxPercentageb = $bankGlBankLeager['taxPercentage'];
                }
                $bankTaxamount = ($ItemGlDetails['Amount'] / 100) * $taxPercentageb;
                $invGLbnk['gl_dr'] = $bankGlBankLeager['Amount'] + $bankTaxamount;
                $invGLbnk['gl_cr'] = 0;
                $invGLbnk['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];
                $invGLbnk['partyAutoID'] = $pvmaster['partyID'];
                $invGLbnk['partyName'] = $pvmaster['partyName'];
                $invGLbnk['approvedDate'] = $this->common_data['current_date'];
                $invGLbnk['approvedbyEmpID'] = $this->common_data['current_userID'];
                $invGLbnk['approvedbyEmpName'] = $this->common_data['current_user'];
                $invGLbnk['companyCode'] = $this->common_data['company_data']['company_code'];
                $invGLbnk['companyID'] = $this->common_data['company_data']['company_id'];
                array_push($globle_array, $invGLbnk);
            }
        }

        $gl_array['currency'] = $pvmaster['transactionCurrency'];
        $gl_array['decimal_places'] = $pvmaster['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'PV';
        $gl_array['name'] = 'Payment Voucher';
        $gl_array['primary_Code'] = $pvmaster['PVcode'];
        $gl_array['date'] = $pvmaster['PVdate'];
        $gl_array['finance_year'] = $pvmaster['companyFinanceYear'];
        $gl_array['finance_period'] = $pvmaster['FYPeriodDateFrom'] . ' - ' . $pvmaster['FYPeriodDateTo'];
        $gl_array['master_data'] = $pvmaster;
        $gl_array['gl_detail'] = $globle_array;

        //$grand_array["gl_detail"] = $gl_array;
        return $gl_array;
    }


    function fetch_double_entry_payment_voucher_rrvr($rvautoid)
    {
        $gl_array = array();
        $globle_array = array();
        //$pvautoid = $this->input->post('payVoucherAutoId');
        $this->db->select('*');
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->where('receiptVoucherAutoId', trim($rvautoid));
        $rvmaster = $this->db->get()->row_array();

        $this->db->select('sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->where('receiptVoucherAutoId', trim($rvautoid));
        $rvdetail = $this->db->get()->row_array();

        $rvdetailSupp = $this->db->query('SELECT
	sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount,sum(unittransactionAmount) as unittransactionAmount,sum(customerAmount) as customerAmount
FROM srp_erp_customerreceiptdetail
WHERE srp_erp_customerreceiptdetail.receiptVoucherAutoId = "' . $rvautoid . '"
AND (type = "Invoice" OR type = "Advance" OR type = "debitnote")')->row_array();


        $date_format_policy = date_format_policy();
        $date = $this->input->post('reversalDate');
        $reversalDate = input_format_date($date, $date_format_policy);

        $str = $reversalDate;
        $dat = explode("-", $str);
        $companyid = current_companyID();

        $companyFinanceYearID = $this->db->query('SELECT
    companyFinanceYearID,companyFinancePeriodID,dateFrom,dateTo
FROM
    srp_erp_companyfinanceperiod
WHERE
    companyID = ' . $companyid . '
AND isActive = 1
AND (
    "' . $reversalDate . '" BETWEEN dateFrom
    AND dateTo
)')->row_array();

        $invoiceGlDetails = $this->db->query('SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as Amount,SUM(srp_erp_customerreceiptdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_customerreceiptdetail.companyReportingAmount) as compReportingAmount,srp_erp_customermaster.receivableAutoID,srp_erp_customermaster.receivableSystemGLCode,srp_erp_customermaster.receivableGLAccount,srp_erp_customermaster.receivableDescription,srp_erp_customermaster.receivableType,srp_erp_chartofaccounts.GLAutoID,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
FROM srp_erp_customerreceiptmaster
LEFT JOIN srp_erp_customerreceiptdetail on srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
LEFT JOIN srp_erp_customermaster on srp_erp_customerreceiptmaster.customerID = srp_erp_customermaster.customerAutoID
LEFT JOIN srp_erp_chartofaccounts on srp_erp_customermaster.receivableAutoID = srp_erp_chartofaccounts.GLAutoID
WHERE srp_erp_customerreceiptmaster.receiptVoucherAutoId = "' . $rvautoid . '"
AND (type = "Invoice" OR type = "Advance" OR type = "debitnote")')->row_array();


        $ItemGlDetails = $this->db->query('SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as Amount,SUM(srp_erp_customerreceiptdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_customerreceiptdetail.companyReportingAmount) as compReportingAmount,srp_erp_chartofaccounts.GLAutoID,addondet.taxPercentage as taxPercentage,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
FROM srp_erp_customerreceiptmaster
LEFT JOIN srp_erp_customerreceiptdetail on srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
LEFT JOIN srp_erp_customermaster on srp_erp_customerreceiptmaster.customerID = srp_erp_customermaster.customerAutoID
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		receiptVoucherAutoId
	FROM
		srp_erp_customerreceipttaxdetails
	GROUP BY
		receiptVoucherAutoId
) addondet ON (
	`addondet`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId
)
LEFT JOIN srp_erp_companycontrolaccounts on srp_erp_companycontrolaccounts.controlAccountType = "RRVR"
LEFT JOIN srp_erp_chartofaccounts on srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID
WHERE srp_erp_customerreceiptmaster.receiptVoucherAutoId = "' . $rvautoid . '"
AND srp_erp_companycontrolaccounts.companyID=' . $companyid . '
AND (type = "GL" OR type = "Item")')->row_array();

        $bankGlBankLeager = $this->db->query('SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as Amount,SUM(srp_erp_customerreceiptdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_customerreceiptdetail.companyReportingAmount) as compReportingAmount,addondet.taxPercentage as taxPercentage,srp_erp_chartofaccounts.GLAutoID,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
FROM srp_erp_customerreceiptmaster
LEFT JOIN srp_erp_customerreceiptdetail on srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		receiptVoucherAutoId
	FROM
		srp_erp_customerreceipttaxdetails
	GROUP BY
		receiptVoucherAutoId
) addondet ON (
	`addondet`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId
)
LEFT JOIN srp_erp_chartofaccounts on srp_erp_customerreceiptmaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID
WHERE srp_erp_customerreceiptmaster.receiptVoucherAutoId = "' . $rvautoid . '"')->row_array();

        if ($rvmaster['RVType'] == 'Invoices' || $rvmaster['RVType'] == 'CustomerInvoices') {
            if (!empty($invoiceGlDetails)) {
                $invGLLia['GLAutoID'] = $invoiceGlDetails['GLAutoID'];
                $invGLLia['gl_code'] = $invoiceGlDetails['systemAccountCode'];
                $invGLLia['secondary'] = $invoiceGlDetails['GLSecondaryCode'];
                $invGLLia['gl_desc'] = $invoiceGlDetails['GLDescription'];
                $invGLLia['gl_type'] = $invoiceGlDetails['subCategory'];
                $invGLLia['segment'] = null;
                $invGLLia['amount_type'] = 'dr';
                $invGLLia['transactionCurrency'] = $rvmaster['transactionCurrency'];
                $invGLLia['transactionExchangeRate'] = $rvmaster['transactionExchangeRate'];
                $invGLLia['gl_cr'] = 0;
                $invGLLia['gl_dr'] = $invoiceGlDetails['Amount'];
                $invGLLia['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];
                $invGLLia['partyAutoID'] = $rvmaster['customerID'];
                $invGLLia['partyName'] = $rvmaster['customerName'];
                $invGLLia['subLedgerType'] = 1;
                $invGLLia['subLedgerDesc'] = 'AR';
                $invGLLia['approvedDate'] = $this->common_data['current_date'];
                $invGLLia['approvedbyEmpID'] = $this->common_data['current_userID'];
                $invGLLia['approvedbyEmpName'] = $this->common_data['current_user'];
                $invGLLia['companyCode'] = $this->common_data['company_data']['company_code'];
                $invGLLia['companyID'] = $this->common_data['company_data']['company_id'];

                array_push($globle_array, $invGLLia);
            }

            if (!empty($ItemGlDetails['GLAutoID'])) {
                $invGL['GLAutoID'] = $ItemGlDetails['GLAutoID'];
                $invGL['gl_code'] = $ItemGlDetails['systemAccountCode'];
                $invGL['secondary'] = $ItemGlDetails['GLSecondaryCode'];
                $invGL['gl_desc'] = $ItemGlDetails['GLDescription'];
                $invGL['gl_type'] = $ItemGlDetails['subCategory'];
                $invGL['amount_type'] = 'dr';
                $invGL['segment'] = null;
                $invGL['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                $invGL['transactionCurrency'] = $rvmaster['transactionCurrency'];
                $invGL['transactionExchangeRate'] = $rvmaster['transactionExchangeRate'];
                if ($ItemGlDetails['taxPercentage'] == null) {
                    $taxPercentage = 0;
                } else {
                    $taxPercentage = $ItemGlDetails['taxPercentage'];
                }
                $itemTaxamount = ($ItemGlDetails['Amount'] / 100) * $taxPercentage;
                $invGL['gl_cr'] = 0;
                $invGL['gl_dr'] = $ItemGlDetails['Amount'] + $itemTaxamount;
                $invGL['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];
                $invGL['partyAutoID'] = $rvmaster['customerID'];
                $invGL['partyName'] = $rvmaster['customerName'];
                $invGL['approvedDate'] = $this->common_data['current_date'];
                $invGL['approvedbyEmpID'] = $this->common_data['current_userID'];
                $invGL['approvedbyEmpName'] = $this->common_data['current_user'];
                $invGL['companyCode'] = $this->common_data['company_data']['company_code'];
                $invGL['companyID'] = $this->common_data['company_data']['company_id'];
                array_push($globle_array, $invGL);
            }

            if (!empty($bankGlBankLeager['GLAutoID'])) {
                $invGLbnk['GLAutoID'] = $bankGlBankLeager['GLAutoID'];
                $invGLbnk['gl_code'] = $bankGlBankLeager['systemAccountCode'];
                $invGLbnk['secondary'] = $bankGlBankLeager['GLSecondaryCode'];
                $invGLbnk['gl_desc'] = $bankGlBankLeager['GLDescription'];
                $invGLbnk['gl_type'] = $bankGlBankLeager['subCategory'];
                $invGLbnk['amount_type'] = 'cr';
                $invGLbnk['segment'] = null;
                $invGLbnk['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                $invGLbnk['transactionCurrency'] = $rvmaster['transactionCurrency'];
                $invGLbnk['transactionExchangeRate'] = $rvmaster['transactionExchangeRate'];
                if ($ItemGlDetails['taxPercentage'] == null) {
                    $taxPercentageb = 0;
                } else {
                    $taxPercentageb = $bankGlBankLeager['taxPercentage'];
                }
                $bankTaxamount = ($ItemGlDetails['Amount'] / 100) * $taxPercentageb;
                $invGLbnk['gl_dr'] = 0;
                $invGLbnk['gl_cr'] = ($bankGlBankLeager['Amount'] + $bankTaxamount) * -1;
                $invGLbnk['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];
                $invGLbnk['partyAutoID'] = $rvmaster['customerID'];
                $invGLbnk['partyName'] = $rvmaster['customerName'];
                $invGLbnk['approvedDate'] = $this->common_data['current_date'];
                $invGLbnk['approvedbyEmpID'] = $this->common_data['current_userID'];
                $invGLbnk['approvedbyEmpName'] = $this->common_data['current_user'];
                $invGLbnk['companyCode'] = $this->common_data['company_data']['company_code'];
                $invGLbnk['companyID'] = $this->common_data['company_data']['company_id'];
                array_push($globle_array, $invGLbnk);
            }
        } else {
            if (!empty($ItemGlDetails['GLAutoID'])) {
                $invGL['GLAutoID'] = $ItemGlDetails['GLAutoID'];
                $invGL['gl_code'] = $ItemGlDetails['systemAccountCode'];
                $invGL['secondary'] = $ItemGlDetails['GLSecondaryCode'];
                $invGL['gl_desc'] = $ItemGlDetails['GLDescription'];
                $invGL['gl_type'] = $ItemGlDetails['subCategory'];
                $invGL['amount_type'] = 'dr';
                $invGL['segment'] = null;
                $invGL['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                $invGL['transactionCurrency'] = $rvmaster['transactionCurrency'];
                $invGL['transactionExchangeRate'] = $rvmaster['transactionExchangeRate'];
                if ($ItemGlDetails['taxPercentage'] == null) {
                    $taxPercentage = 0;
                } else {
                    $taxPercentage = $ItemGlDetails['taxPercentage'];
                }
                $itemTaxamount = ($ItemGlDetails['Amount'] / 100) * $taxPercentage;
                $invGL['gl_cr'] = 0;
                $invGL['gl_dr'] = $ItemGlDetails['Amount'] + $itemTaxamount;
                $invGL['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];
                $invGL['partyAutoID'] = $rvmaster['customerID'];
                $invGL['partyName'] = $rvmaster['customerName'];
                $invGL['approvedDate'] = $this->common_data['current_date'];
                $invGL['approvedbyEmpID'] = $this->common_data['current_userID'];
                $invGL['approvedbyEmpName'] = $this->common_data['current_user'];
                $invGL['companyCode'] = $this->common_data['company_data']['company_code'];
                $invGL['companyID'] = $this->common_data['company_data']['company_id'];
                array_push($globle_array, $invGL);
            }
            if (!empty($bankGlBankLeager['GLAutoID'])) {
                $invGLbnk['GLAutoID'] = $bankGlBankLeager['GLAutoID'];
                $invGLbnk['gl_code'] = $bankGlBankLeager['systemAccountCode'];
                $invGLbnk['secondary'] = $bankGlBankLeager['GLSecondaryCode'];
                $invGLbnk['gl_desc'] = $bankGlBankLeager['GLDescription'];
                $invGLbnk['gl_type'] = $bankGlBankLeager['subCategory'];
                $invGLbnk['amount_type'] = 'cr';
                $invGLbnk['segment'] = null;
                $invGLbnk['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                $invGLbnk['transactionCurrency'] = $rvmaster['transactionCurrency'];
                $invGLbnk['transactionExchangeRate'] = $rvmaster['transactionExchangeRate'];
                if ($ItemGlDetails['taxPercentage'] == null) {
                    $taxPercentageb = 0;
                } else {
                    $taxPercentageb = $bankGlBankLeager['taxPercentage'];
                }
                $bankTaxamount = ($ItemGlDetails['Amount'] / 100) * $taxPercentageb;
                $invGLbnk['gl_dr'] = 0;
                $invGLbnk['gl_cr'] = ($bankGlBankLeager['Amount'] + $bankTaxamount) * -1;
                $invGLbnk['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];
                $invGLbnk['partyAutoID'] = $rvmaster['customerID'];
                $invGLbnk['partyName'] = $rvmaster['customerName'];
                $invGLbnk['approvedDate'] = $this->common_data['current_date'];
                $invGLbnk['approvedbyEmpID'] = $this->common_data['current_userID'];
                $invGLbnk['approvedbyEmpName'] = $this->common_data['current_user'];
                $invGLbnk['companyCode'] = $this->common_data['company_data']['company_code'];
                $invGLbnk['companyID'] = $this->common_data['company_data']['company_id'];
                array_push($globle_array, $invGLbnk);
            }
        }

        $gl_array['currency'] = $rvmaster['transactionCurrency'];
        $gl_array['decimal_places'] = $rvmaster['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'RV';
        $gl_array['name'] = 'Receipt Voucher';
        $gl_array['primary_Code'] = $rvmaster['RVcode'];
        $gl_array['date'] = $rvmaster['RVdate'];
        $gl_array['finance_year'] = $rvmaster['companyFinanceYear'];
        $gl_array['finance_period'] = $rvmaster['FYPeriodDateFrom'] . ' - ' . $rvmaster['FYPeriodDateTo'];
        $gl_array['master_data'] = $rvmaster;
        $gl_array['gl_detail'] = $globle_array;

        //$grand_array["gl_detail"] = $gl_array;
        return $gl_array;
    }

    function fetch_double_entry_yield_preparation($autoId, $code = null)
    {
        $gl_array = array();
        $gl_array['gl_detail'] = array();

        $this->db->select('*');
        $this->db->where('yieldPreparationID', $autoId);
        $this->db->join('srp_erp_chartofaccounts', "GLAutoID = assetGLAutoID", 'left');
        $master = $this->db->get('srp_erp_pos_menuyieldpreparation')->row_array();

        $this->db->select('SUM(localWacAmountTotal) as localWacAmountTotal');
        $this->db->where('srp_erp_pos_menuyieldpreparation.yieldPreparationID', $autoId);
        $this->db->join('srp_erp_pos_menuyieldpreparationdetails', "srp_erp_pos_menuyieldpreparationdetails.yieldPreparationID = srp_erp_pos_menuyieldpreparation.yieldPreparationID", 'left');
        $detailSum = $this->db->get('srp_erp_pos_menuyieldpreparation')->row_array();

        $this->db->select('srp_erp_chartofaccounts.*,SUM(localWacAmountTotal) as localWacAmountTotal,yieldPreparationID');
        $this->db->where('yieldPreparationID', $autoId);
        $this->db->join('srp_erp_chartofaccounts', "GLAutoID = assetGLAutoID", 'left');
        $this->db->group_by('assetGLAutoID');
        $detail = $this->db->get('srp_erp_pos_menuyieldpreparationdetails')->result_array();

        $globalArray = array();
        /*creditGL*/
        if ($detail) {
            foreach ($detail as $val) {
                $data_arr['auto_id'] = $val['yieldPreparationID'];
                $data_arr['gl_auto_id'] = $val['GLAutoID'];
                $data_arr['gl_code'] = $val['systemAccountCode'];
                $data_arr['secondary'] = $val['GLSecondaryCode'];
                $data_arr['gl_desc'] = $val['GLDescription'];
                $data_arr['gl_type'] = $val['subCategory'];
                $data_arr['segment_id'] = null;
                $data_arr['segment'] = null;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $val['localWacAmountTotal'];
                $data_arr['amount_type'] = 'cr';
                array_push($globalArray, $data_arr);
            }
        }

        /*debit GL*/
        if ($master) {
            $data_arr['auto_id'] = $master['yieldPreparationID'];
            $data_arr['gl_auto_id'] = $master['GLAutoID'];
            $data_arr['gl_code'] = $master['systemAccountCode'];
            $data_arr['secondary'] = $master['GLSecondaryCode'];
            $data_arr['gl_desc'] = $master['GLDescription'];
            $data_arr['gl_type'] = $master['subCategory'];
            $data_arr['segment_id'] = null;
            $data_arr['segment'] = null;
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['gl_dr'] = $detailSum['localWacAmountTotal'];
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            array_push($globalArray, $data_arr);
        }

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'YPRP';
        $gl_array['name'] = 'Yield Preparation';
        $gl_array['primary_Code'] = $master['documentSystemCode'];
        $gl_array['date'] = $master['documentDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['gl_detail'] = $globalArray;

        return $gl_array;
    }


    function fetch_double_entry_scnt_data($stockCountingAutoID, $code = null)
    {
        $gl_array = array();
        $cr_total = 0;
        $grv_total = 0;
        $addon_total = 0;
        $addon_item = array();
        $gl_array['gl_detail'] = array();
        $this->db->select('*');
        $this->db->where('stockCountingAutoID', $stockCountingAutoID);
        $master = $this->db->get('srp_erp_stockcountingmaster')->row_array();

        $this->db->select('stockCountingDetailsAutoID,itemFinanceCategory,PLGLAutoID,PLSystemGLCode,PLGLCode,PLDescription,PLType,BLGLAutoID,BLSystemGLCode ,BLGLCode,BLDescription,BLType,totalValue,financeCategory,segmentCode,segmentID,projectID');
        $this->db->where('stockCountingAutoID', $stockCountingAutoID);
        $detail = $this->db->get('srp_erp_stockcountingdetails')->result_array();

        $cr_p_arr = array();
        $cr_m_arr = array();
        $dr_p_arr = array();
        $dr_m_arr = array();
        for ($i = 0; $i < count($detail); $i++) {
            if ($master["stockCountingType"] == 'Inventory') {
                $assat_entry_arr['auto_id'] = $detail[$i]['stockCountingDetailsAutoID'];
                $assat_entry_arr['gl_auto_id'] = $detail[$i]['BLGLAutoID'];
                $assat_entry_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
                $assat_entry_arr['secondary'] = $detail[$i]['BLGLCode'];
                $assat_entry_arr['gl_desc'] = $detail[$i]['BLDescription'];
                $assat_entry_arr['gl_type'] = $detail[$i]['BLType'];
                $assat_entry_arr['segment_id'] = $detail[$i]['segmentID'];
                $assat_entry_arr['segment'] = $detail[$i]['segmentCode'];
                $assat_entry_arr['projectID'] = $detail[$i]['projectID'];
                $assat_entry_arr['isAddon'] = 0;
                $assat_entry_arr['subLedgerType'] = 0;
                $assat_entry_arr['subLedgerDesc'] = null;
                $assat_entry_arr['partyContractID'] = null;
                $assat_entry_arr['partyType'] = null;
                $assat_entry_arr['partyAutoID'] = null;
                $assat_entry_arr['partySystemCode'] = null;
                $assat_entry_arr['partyName'] = null;
                $assat_entry_arr['partyCurrencyID'] = null;
                $assat_entry_arr['partyCurrency'] = null;
                $assat_entry_arr['transactionExchangeRate'] = null;
                $assat_entry_arr['companyLocalExchangeRate'] = null;
                $assat_entry_arr['companyReportingExchangeRate'] = null;
                $assat_entry_arr['partyExchangeRate'] = null;
                $assat_entry_arr['partyCurrencyAmount'] = null;
                $assat_entry_arr['partyCurrencyDecimalPlaces'] = null;
                $assat_entry_arr['amount_type'] = 'dr';
                if ($detail[$i]['totalValue'] >= 0) {
                    $assat_entry_arr['gl_dr'] = abs($detail[$i]['totalValue']);
                    $assat_entry_arr['gl_cr'] = 0;
                    array_push($dr_p_arr, $assat_entry_arr);
                } else {
                    $assat_entry_arr['gl_dr'] = 0;
                    $assat_entry_arr['gl_cr'] = abs($detail[$i]['totalValue']);
                    $assat_entry_arr['amount_type'] = 'cr';
                    array_push($cr_m_arr, $assat_entry_arr);
                }

                $cost_entry_arr['auto_id'] = $detail[$i]['stockCountingDetailsAutoID'];
                $cost_entry_arr['gl_auto_id'] = $detail[$i]['PLGLAutoID'];
                $cost_entry_arr['gl_code'] = $detail[$i]['PLSystemGLCode'];
                $cost_entry_arr['secondary'] = $detail[$i]['PLGLCode'];
                $cost_entry_arr['gl_desc'] = $detail[$i]['PLDescription'];
                $cost_entry_arr['gl_type'] = $detail[$i]['PLType'];
                $cost_entry_arr['segment_id'] = $detail[$i]['segmentID'];
                $cost_entry_arr['segment'] = $detail[$i]['segmentCode'];
                $cost_entry_arr['isAddon'] = 0;
                $cost_entry_arr['subLedgerType'] = 0;
                $cost_entry_arr['subLedgerDesc'] = null;
                $cost_entry_arr['partyContractID'] = null;
                $cost_entry_arr['partyType'] = null;
                $cost_entry_arr['partyAutoID'] = null;
                $cost_entry_arr['partySystemCode'] = null;
                $cost_entry_arr['partyName'] = null;
                $cost_entry_arr['partyCurrencyID'] = null;
                $cost_entry_arr['partyCurrency'] = null;
                $cost_entry_arr['transactionExchangeRate'] = null;
                $cost_entry_arr['companyLocalExchangeRate'] = null;
                $cost_entry_arr['companyReportingExchangeRate'] = null;
                $cost_entry_arr['partyExchangeRate'] = null;
                $cost_entry_arr['partyCurrencyAmount'] = null;
                $cost_entry_arr['partyCurrencyDecimalPlaces'] = null;
                $cost_entry_arr['amount_type'] = 'cr';
                if ($detail[$i]['totalValue'] >= 0) {
                    $cost_entry_arr['gl_dr'] = 0;
                    $cost_entry_arr['gl_cr'] = abs($detail[$i]['totalValue']);
                    array_push($cr_p_arr, $cost_entry_arr);
                } else {
                    $cost_entry_arr['gl_dr'] = abs($detail[$i]['totalValue']);
                    $cost_entry_arr['gl_cr'] = 0;
                    $cost_entry_arr['amount_type'] = 'dr';
                    array_push($dr_m_arr, $cost_entry_arr);
                }
            }

            $cr_p_arr = $this->array_group_sum($cr_p_arr);
            $cr_m_arr = $this->array_group_sum($cr_m_arr);
            $dr_p_arr = $this->array_group_sum($dr_p_arr);
            $dr_m_arr = $this->array_group_sum($dr_m_arr);

            $gl_array['gl_detail'] = $cr_p_arr;
            foreach ($cr_m_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
            foreach ($dr_p_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
            foreach ($dr_m_arr as $key => $value) {
                array_push($gl_array['gl_detail'], $value);
            }
        }

        $gl_array['currency'] = $master['companyLocalCurrency'];
        $gl_array['decimal_places'] = $master['companyLocalCurrencyDecimalPlaces'];
        $gl_array['code'] = 'SCNT';
        $gl_array['name'] = 'Stock Counting';
        $gl_array['primary_Code'] = $master['stockCountingCode'];
        $gl_array['date'] = $master['stockCountingDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;

        return $gl_array;
    }


    function fetch_double_entry_exceeded_match_data($exceededMatchID, $code = null)
    {
        $gl_array = array();
        $cost_arr = array();
        $assat_arr = array();
        $gl_array['gl_detail'] = array();
        $this->db->select('*');
        $this->db->where('exceededMatchID', $exceededMatchID);
        $master = $this->db->get('srp_erp_itemexceededmatch')->row_array();

        //$this->db->select('financeCategory,PLGLAutoID,PLSystemGLCode,PLGLCode,PLDescription,BLGLAutoID,BLSystemGLCode ,PLType ,BLGLCode,BLDescription,BLType,totalValue,segmentCode,segmentID,projectID,projectExchangeRate');
        $this->db->select('*');
        //$this->db->where('itemCategory !=', 'Non Inventory');
        $this->db->where('exceededMatchID', $exceededMatchID);
        $detail = $this->db->get('srp_erp_itemexceededmatchdetails')->result_array();

        for ($i = 0; $i < count($detail); $i++) {
            //$PLGLAutoID=fetch_gl_account_desc($detail[$i]['costGLAutoID']);
            $PLGLAutoID = fetch_gl_account_desc($detail[$i]['exceededGLAutoID']);
            $BLGLAutoID = fetch_gl_account_desc($detail[$i]['assetGLAutoID']);
            $assa_data_arr['auto_id'] = 0;
            $assa_data_arr['gl_auto_id'] = $detail[$i]['assetGLAutoID'];
            $assa_data_arr['gl_code'] = $BLGLAutoID['systemAccountCode'];
            $assa_data_arr['secondary'] = $BLGLAutoID['GLSecondaryCode'];
            $assa_data_arr['gl_desc'] = $BLGLAutoID['GLDescription'];
            $assa_data_arr['gl_type'] = $BLGLAutoID['subCategory'];
            $assa_data_arr['segment_id'] = $detail[$i]['segmentID'];
            $assa_data_arr['segment'] = $detail[$i]['segmentCode'];
            //$assa_data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            //$assa_data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $assa_data_arr['gl_dr'] = 0;
            $assa_data_arr['gl_cr'] = $detail[$i]['totalValue'];
            $assa_data_arr['amount_type'] = 'cr';
            $assa_data_arr['isAddon'] = 0;
            $assa_data_arr['subLedgerType'] = 0;
            $assa_data_arr['subLedgerDesc'] = null;
            $assa_data_arr['partyContractID'] = null;
            $assa_data_arr['partyType'] = null;
            $assa_data_arr['partyAutoID'] = null;
            $assa_data_arr['partySystemCode'] = null;
            $assa_data_arr['partyName'] = null;
            $assa_data_arr['partyCurrencyID'] = null;
            $assa_data_arr['partyCurrency'] = null;
            $assa_data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'];
            $assa_data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $assa_data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $assa_data_arr['partyExchangeRate'] = null;
            $assa_data_arr['partyCurrencyAmount'] = null;
            $assa_data_arr['partyCurrencyDecimalPlaces'] = null;
            array_push($assat_arr, $assa_data_arr);

            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $detail[$i]['exceededGLAutoID'];
            $data_arr['gl_code'] = $PLGLAutoID['systemAccountCode'];
            $data_arr['secondary'] = $PLGLAutoID['GLSecondaryCode'];
            $data_arr['gl_desc'] = $PLGLAutoID['GLDescription'];
            $data_arr['gl_type'] = $PLGLAutoID['subCategory'];
            $data_arr['segment_id'] = $detail[$i]['segmentID'];
            $data_arr['segment'] = $detail[$i]['segmentCode'];
            //$data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            //$data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $data_arr['gl_dr'] = $detail[$i]['totalValue'];
            $data_arr['gl_cr'] = 0;
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = null;
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = null;
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = null;
            $data_arr['partyCurrencyAmount'] = null;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'dr';
            array_push($cost_arr, $data_arr);
        }

        $assat_arr = $this->array_group_sum($assat_arr);
        $cost_arr = $this->array_group_sum($cost_arr);

        $gl_array['gl_detail'] = $assat_arr;
        foreach ($cost_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        $gl_array['currency'] = $master['companyLocalCurrency'];
        $gl_array['decimal_places'] = $master['companyLocalCurrencyDecimalPlaces'];
        $gl_array['code'] = 'EIM';
        $gl_array['name'] = 'Exceeded Item Match';
        $gl_array['primary_Code'] = $master['exceededMatchID'];
        $gl_array['date'] = $master['documentDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }

    function fetch_double_entry_customer_invoice_buyback_data($invoiceAutoID, $code = null)
    {
        $gl_array = array();
        $tax_total = 0;
        $cr_total = 0;
        $discountamnt = 0;
        $companyID = current_companyID();
        $gl_array['gl_detail'] = array();
        $this->db->select('srp_erp_customerinvoicemaster_temp.*,srp_erp_customermaster.vatIdNo as vatIdNo');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster_temp.customerID', 'left');
        $master = $this->db->get('srp_erp_customerinvoicemaster_temp')->row_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $detail = $this->db->get('srp_erp_customerinvoicedetails_temp')->result_array();

        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode ,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate, supplierCurrencyDecimalPlaces,supplierCurrencyID,taxMasterAutoID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $tax_detail = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();
        $taxledger = $this->db->query("SELECT
	tax.taxDescription,
	tax.taxShortCode,
	srp_erp_taxledger.taxMasterID as taxMasterAutoID,
	SUM(srp_erp_taxledger.amount) AS amount,
	documentDetailAutoID,
	taxAuthorityAutoID,
	taxGlAutoID,
srp_erp_chartofaccounts.systemAccountCode as SystemGLCode,
srp_erp_chartofaccounts.GLSecondaryCode as GLCode,
srp_erp_chartofaccounts.GLDescription as GLDescription,
srp_erp_chartofaccounts.subCategory as GLType,
srp_erp_taxauthorithymaster.authoritySystemCode as supplierSystemCode,
srp_erp_taxauthorithymaster.AuthorityName as supplierName,
srp_erp_taxauthorithymaster.currencyID as supplierCurrencyID

FROM
	`srp_erp_taxledger`
LEFT JOIN srp_erp_taxmaster tax ON srp_erp_taxledger.taxMasterID = tax.taxMasterAutoID
LEFT JOIN srp_erp_chartofaccounts ON srp_erp_taxledger.taxGlAutoID = srp_erp_chartofaccounts.GLAutoID
LEFT JOIN srp_erp_taxauthorithymaster ON srp_erp_taxledger.taxAuthorityAutoID = srp_erp_taxauthorithymaster.taxAuthourityMasterID
WHERE
	documentMasterAutoID = $invoiceAutoID
AND documentID = 'HCINV'
AND srp_erp_taxledger.companyID = $companyID
GROUP BY
	srp_erp_taxledger.taxMasterID ")->result_array();

        $r1 = "SELECT srp_erp_customerinvoicedetails_temp.*,SUM(taxAmount*requestedQty) as tottaxAmount
FROM
	`srp_erp_customerinvoicedetails_temp`
WHERE
	`invoiceAutoID` = $invoiceAutoID
	GROUP BY
		InvoiceAutoID,taxMasterAutoID";
        $item_tax_detail = $this->db->query($r1)->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $extraCharge = $this->db->get('srp_erp_customerinvoiceextrachargedetails_temp')->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('isChargeToExpense', 1);
        $discountIsCharge = $this->db->get('srp_erp_customerinvoicediscountdetails_temp')->result_array();

        $qry = "SELECT
	SUM(transactionAmount) as tottransactionAmount,SUM(totalAfterTax) as totalAfterTax
FROM
	srp_erp_customerinvoicedetails_temp
WHERE
	invoiceAutoID = $invoiceAutoID";
        $sumdetail = $this->db->query($qry)->row_array();
        $tottransamnt = $sumdetail['tottransactionAmount'];

        $this->db->select('SUM(discountPercentage) as discountPercentage');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('isChargeToExpense', 0);
        $discount = $this->db->get('srp_erp_customerinvoicediscountdetails_temp')->row_array();

        if ($discount) {
            $discountamnt = ($tottransamnt * $discount['discountPercentage']) / 100;
        }

        $cr_p_arr = array();
        $cr_m_arr = array();
        $e_cr_p_arr = array();
        $e_cr_m_arr = array();
        $item_arr = array();
        $item_tax_arr = array();
        for ($i = 0; $i < count($detail); $i++) {
            if ($detail[$i]['type'] == 'Item') {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['revenueGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['revenueSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['revenueGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['revenueGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['revenueGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = null;
                $data_arr['amount_type'] = 'dr';
                if ($discount) {
                    $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                    $discamnt = ($detail[$i]['transactionAmount'] * $discountamnt) / $tottransamnt;
                    $amount = $amount - $discamnt;
                } else {
                    $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                }

                if ($amount <= 0) {
                    $data_arr['gl_dr'] = $amount;
                    $data_arr['gl_cr'] = 0;
                    array_push($cr_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $amount;
                    $data_arr['amount_type'] = 'cr';
                    array_push($cr_m_arr, $data_arr);
                }
            } else {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['revenueGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['revenueSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['revenueGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['revenueGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['revenueGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                if ($discount) {
                    $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                    $discamnt = ($detail[$i]['transactionAmount'] * $discountamnt) / $tottransamnt;
                    $amount = $amount - $discamnt;
                } else {
                    $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                }
                if ($amount <= 0) {
                    $data_arr['gl_dr'] = $amount;
                    $data_arr['gl_cr'] = 0;
                    array_push($e_cr_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $amount;
                    $data_arr['amount_type'] = 'cr';
                    array_push($e_cr_m_arr, $data_arr);
                }
            }
            $cr_total += $data_arr['gl_cr'];

            /*if ($detail[$i]['taxAmount'] != 0) {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['taxSupplierliabilityAutoID'];
                $data_arr['gl_code'] = $detail[$i]['taxSupplierliabilitySystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['taxSupplierliabilityGLAccount'];
                $data_arr['gl_desc'] = $detail[$i]['taxSupplierliabilityDescription'];
                $data_arr['gl_type'] = $detail[$i]['taxSupplierliabilityType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = null;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'AUT';
                $data_arr['partyAutoID'] = $detail[$i]['taxSupplierAutoID'];
                $data_arr['partySystemCode'] = $detail[$i]['taxSupplierSystemCode'];
                $data_arr['partyName'] = $detail[$i]['taxSupplierName'];
                $data_arr['partyCurrencyID'] = $detail[$i]['taxSupplierCurrencyID'];
                $data_arr['partyCurrency'] = $detail[$i]['taxSupplierCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $detail[$i]['taxSupplierCurrencyExchangeRate'];
                $data_arr['partyCurrencyAmount'] = $detail[$i]['taxSupplierliabilityType'];
                $data_arr['partyCurrencyDecimalPlaces'] = $detail[$i]['taxSupplierCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = ($detail[$i]['taxAmount'] * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
                $cr_total += $data_arr['gl_cr'];
            }*/

            if ($detail[$i]['type'] == 'Item' && $detail[$i]['itemCategory'] == 'Inventory') {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['expenseGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['expenseSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['expenseGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['expenseGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['expenseGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                $data_arr['gl_dr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                $data_arr['gl_cr'] = 0;
                array_push($item_arr, $data_arr);
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['assetGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['assetSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['assetGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['assetGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['assetGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
            }
            $tax_total += ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
        }

        /*for ($i = 0; $i < count($item_tax_detail); $i++) {
            if(!empty($item_tax_detail[$i]['taxMasterAutoID'])){
                $data_arr_tx['auto_id'] = $item_tax_detail[$i]['invoiceDetailsAutoID'];
                $data_arr_tx['gl_auto_id'] = $item_tax_detail[$i]['taxSupplierliabilityAutoID'];
                $data_arr_tx['gl_code'] = $item_tax_detail[$i]['taxSupplierliabilitySystemGLCode'];
                $data_arr_tx['secondary'] = $item_tax_detail[$i]['taxSupplierliabilityGLAccount'];
                $data_arr_tx['gl_desc'] = $item_tax_detail[$i]['taxSupplierliabilityDescription'];
                $data_arr_tx['gl_type'] = $item_tax_detail[$i]['taxSupplierliabilityType'];
                $data_arr_tx['segment_id'] = $master['segmentID'];
                $data_arr_tx['segment'] = $master['segmentCode'];
                $data_arr_tx['isAddon'] = 0;
                $data_arr_tx['taxMasterAutoID'] = $item_tax_detail[$i]['taxMasterAutoID'];
                $data_arr_tx['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx['subLedgerType'] = null;
                $data_arr_tx['subLedgerDesc'] = null;
                $data_arr_tx['partyContractID'] = null;
                $data_arr_tx['partyType'] = 'AUT';
                $data_arr_tx['partyAutoID'] = $item_tax_detail[$i]['taxSupplierAutoID'];
                $data_arr_tx['partySystemCode'] = $item_tax_detail[$i]['taxSupplierSystemCode'];
                $data_arr_tx['partyName'] = $item_tax_detail[$i]['taxSupplierName'];
                $data_arr_tx['partyCurrencyID'] = $item_tax_detail[$i]['taxSupplierCurrencyID'];
                $data_arr_tx['partyCurrency'] = $item_tax_detail[$i]['taxSupplierCurrency'];
                $data_arr_tx['transactionExchangeRate'] = null;
                $data_arr_tx['companyLocalExchangeRate'] = null;
                $data_arr_tx['companyReportingExchangeRate'] = null;
                $data_arr_tx['partyExchangeRate'] = $item_tax_detail[$i]['taxSupplierCurrencyExchangeRate'];
                $data_arr_tx['partyCurrencyAmount'] = $item_tax_detail[$i]['taxSupplierliabilityType'];
                $data_arr_tx['partyCurrencyDecimalPlaces'] = $item_tax_detail[$i]['taxSupplierCurrencyDecimalPlaces'];
                $data_arr_tx['amount_type'] = 'cr';
                $data_arr_tx['gl_dr'] = 0;
                $data_arr_tx['gl_cr'] = $item_tax_detail[$i]['tottaxAmount'];
                array_push($item_tax_arr, $data_arr_tx);
                $cr_total += $data_arr_tx['gl_cr'];
            }
        }*/


        for ($i = 0; $i < count($taxledger); $i++) {
            if (!empty($taxledger[$i]['taxMasterAutoID'])) {
                $data_arr['auto_id'] = $taxledger[$i]['documentDetailAutoID'];
                $data_arr['gl_auto_id'] = $taxledger[$i]['taxGlAutoID'];
                $data_arr['gl_code'] = $taxledger[$i]['SystemGLCode'];
                $data_arr['secondary'] = $taxledger[$i]['GLCode'];
                $data_arr['gl_desc'] = $taxledger[$i]['GLDescription'] . ' - Tax';
                $data_arr['gl_type'] = $taxledger[$i]['GLType'];
                $data_arr['segment_id'] = null;
                $data_arr['segment'] = null;
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = $taxledger[$i]['taxMasterAutoID'];
                $data_arr['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr['subLedgerType'] = null;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'AUT';
                $data_arr['partyAutoID'] = $taxledger[$i]['taxAuthorityAutoID'];
                $data_arr['partySystemCode'] = $taxledger[$i]['supplierSystemCode'];
                $data_arr['partyName'] = $taxledger[$i]['supplierName'];
                $data_arr['partyCurrencyID'] = $taxledger[$i]['supplierCurrencyID'];
                $data_arr['partyCurrency'] = null;
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = null;
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = null;
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $taxledger[$i]['amount'];
                array_push($gl_array['gl_detail'], $data_arr);
                $cr_total += $data_arr['gl_cr'];
            }
        }

        for ($i = 0; $i < count($extraCharge); $i++) {
            $data_arr['auto_id'] = $extraCharge[$i]['extraChargeDetailID'];
            $data_arr['gl_auto_id'] = $extraCharge[$i]['GLAutoID'];
            $data_arr['gl_code'] = $extraCharge[$i]['systemGLCode'];
            $data_arr['secondary'] = $extraCharge[$i]['GLCode'];
            $data_arr['gl_desc'] = $extraCharge[$i]['GLDescription'] . ' - Extra Charge';
            $data_arr['gl_type'] = $extraCharge[$i]['GLType'];
            $data_arr['segment_id'] = $extraCharge[$i]['segmentID'];
            $data_arr['segment'] = $extraCharge[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = $extraCharge[$i]['customerCurrencyID'];
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = null;
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = $extraCharge[$i]['transactionAmount'];
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total += $data_arr['gl_cr'];
        }

        for ($i = 0; $i < count($discountIsCharge); $i++) {
            $data_arr['auto_id'] = $discountIsCharge[$i]['discountDetailID'];
            $data_arr['gl_auto_id'] = $discountIsCharge[$i]['GLAutoID'];
            $data_arr['gl_code'] = $discountIsCharge[$i]['systemGLCode'];
            $data_arr['secondary'] = $discountIsCharge[$i]['GLCode'];
            $data_arr['gl_desc'] = $discountIsCharge[$i]['GLDescription'] . ' - Discount';
            $data_arr['gl_type'] = $discountIsCharge[$i]['GLType'];
            $data_arr['segment_id'] = $discountIsCharge[$i]['segmentID'];
            $data_arr['segment'] = $discountIsCharge[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = $discountIsCharge[$i]['customerCurrencyID'];
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = null;
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = ($discountIsCharge[$i]['discountPercentage'] * $tottransamnt) / 100;
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total -= ($discountIsCharge[$i]['discountPercentage'] * $tottransamnt) / 100;
        }

        $cr_p_arr = $this->array_group_sum_tax($cr_p_arr);
        $cr_m_arr = $this->array_group_sum_tax($cr_m_arr);
        $item_arr = $this->array_group_sum_tax($item_arr);
        foreach ($cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        foreach ($item_tax_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }


        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $master['customerReceivableAutoID'];
        $data_arr['gl_code'] = $master['customerReceivableSystemGLCode'];
        $data_arr['secondary'] = $master['customerReceivableGLAccount'];
        $data_arr['gl_desc'] = $master['customerReceivableDescription'];
        $data_arr['gl_type'] = $master['customerReceivableType'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];
        $data_arr['gl_dr'] = $cr_total;
        $data_arr['gl_cr'] = 0;
        $data_arr['amount_type'] = 'dr';
        $data_arr['isAddon'] = 0;
        $data_arr['taxMasterAutoID'] = null;
        $data_arr['partyVatIdNo'] = null;
        $data_arr['subLedgerType'] = 3;
        $data_arr['subLedgerDesc'] = 'AR';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = 'CUS';
        $data_arr['partyAutoID'] = $master['customerID'];
        $data_arr['partySystemCode'] = $master['customerSystemCode'];
        $data_arr['partyName'] = $master['customerName'];
        $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
        $data_arr['partyCurrency'] = $master['customerCurrency'];
        $data_arr['transactionExchangeRate'] = null;
        $data_arr['companyLocalExchangeRate'] = null;
        $data_arr['companyReportingExchangeRate'] = null;
        $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
        $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
        array_push($gl_array['gl_detail'], $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'CINV';
        $gl_array['name'] = 'Customer Invoice';
        $gl_array['primary_Code'] = $master['invoiceCode'];
        $gl_array['date'] = $master['invoiceDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }

    function fetch_double_entry_customer_invoice_temp_data($invoiceAutoID, $code = null)
    {
        $gl_array = array();
        $tax_total = 0;
        $cr_total = 0;
        $discountamnt = 0;

        $gl_array['gl_detail'] = array();
        $this->db->select('srp_erp_customerinvoicemaster.*,srp_erp_customermaster.vatIdNo as vatIdNo,srp_erp_customermaster.customerName as customerNamemaster');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
        $companyID = $master['companyID'];
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $detail = $this->db->get('srp_erp_customerinvoicedetails')->result_array();

        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode ,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate, supplierCurrencyDecimalPlaces,supplierCurrencyID,taxMasterAutoID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $tax_detail = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();
        $taxledger = $this->db->query("SELECT
	tax.taxDescription,
	tax.taxShortCode,
	srp_erp_taxledger.taxMasterID as taxMasterAutoID,
	SUM(srp_erp_taxledger.amount) AS amount,
	documentDetailAutoID,
	taxAuthorityAutoID,
	taxGlAutoID,
srp_erp_chartofaccounts.systemAccountCode as SystemGLCode,
srp_erp_chartofaccounts.GLSecondaryCode as GLCode,
srp_erp_chartofaccounts.GLDescription as GLDescription,
srp_erp_chartofaccounts.subCategory as GLType,
srp_erp_taxauthorithymaster.authoritySystemCode as supplierSystemCode,
srp_erp_taxauthorithymaster.AuthorityName as supplierName,
srp_erp_taxauthorithymaster.currencyID as supplierCurrencyID

FROM
	`srp_erp_taxledger`
LEFT JOIN srp_erp_taxmaster tax ON srp_erp_taxledger.taxMasterID = tax.taxMasterAutoID
LEFT JOIN srp_erp_chartofaccounts ON srp_erp_taxledger.taxGlAutoID = srp_erp_chartofaccounts.GLAutoID
LEFT JOIN srp_erp_taxauthorithymaster ON srp_erp_taxledger.taxAuthorityAutoID = srp_erp_taxauthorithymaster.taxAuthourityMasterID
WHERE
	documentMasterAutoID = $invoiceAutoID
AND documentID = 'CINV'
AND srp_erp_taxledger.companyID = $companyID
GROUP BY
	srp_erp_taxledger.taxMasterID ")->result_array();

        $r1 = "SELECT srp_erp_customerinvoicedetails.*,SUM(taxAmount*requestedQty) as tottaxAmount
FROM
	`srp_erp_customerinvoicedetails`
WHERE
	`invoiceAutoID` = $invoiceAutoID
	GROUP BY
		InvoiceAutoID,taxMasterAutoID";
        $item_tax_detail = $this->db->query($r1)->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $extraCharge = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('isChargeToExpense', 1);
        $discountIsCharge = $this->db->get('srp_erp_customerinvoicediscountdetails')->result_array();

        $qry = "SELECT
	SUM(transactionAmount) as tottransactionAmount,SUM(totalAfterTax) as totalAfterTax
FROM
	srp_erp_customerinvoicedetails
WHERE
	invoiceAutoID = $invoiceAutoID";
        $sumdetail = $this->db->query($qry)->row_array();
        $tottransamnt = $sumdetail['tottransactionAmount'];

        $this->db->select('SUM(discountPercentage) as discountPercentage');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('isChargeToExpense', 0);
        $discount = $this->db->get('srp_erp_customerinvoicediscountdetails')->row_array();

        if ($discount) {
            $discountamnt = ($tottransamnt * $discount['discountPercentage']) / 100;
        }

        $cr_p_arr = array();
        $cr_m_arr = array();
        $e_cr_p_arr = array();
        $e_cr_m_arr = array();
        $item_arr = array();
        $item_tax_arr = array();
        for ($i = 0; $i < count($detail); $i++) {
            if ($detail[$i]['type'] == 'Item') {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['revenueGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['revenueSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['revenueGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['revenueGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['revenueGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = null;
                $data_arr['amount_type'] = 'dr';
                if ($discount) {
                    $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                    $discamnt = ($detail[$i]['transactionAmount'] * $discountamnt) / $tottransamnt;
                    $amount = $amount - $discamnt;
                } else {
                    $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                }

                if ($amount <= 0) {
                    $data_arr['gl_dr'] = $amount;
                    $data_arr['gl_cr'] = 0;
                    array_push($cr_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $amount;
                    $data_arr['amount_type'] = 'cr';
                    array_push($cr_m_arr, $data_arr);
                }
            } else {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['revenueGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['revenueSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['revenueGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['revenueGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['revenueGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                if ($discount) {
                    $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                    $discamnt = ($detail[$i]['transactionAmount'] * $discountamnt) / $tottransamnt;
                    $amount = $amount - $discamnt;
                } else {
                    $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
                }
                if ($amount <= 0) {
                    $data_arr['gl_dr'] = $amount;
                    $data_arr['gl_cr'] = 0;
                    array_push($e_cr_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $amount;
                    $data_arr['amount_type'] = 'cr';
                    array_push($e_cr_m_arr, $data_arr);
                }
            }
            $cr_total += $data_arr['gl_cr'];

            /*if ($detail[$i]['taxAmount'] != 0) {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['taxSupplierliabilityAutoID'];
                $data_arr['gl_code'] = $detail[$i]['taxSupplierliabilitySystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['taxSupplierliabilityGLAccount'];
                $data_arr['gl_desc'] = $detail[$i]['taxSupplierliabilityDescription'];
                $data_arr['gl_type'] = $detail[$i]['taxSupplierliabilityType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = null;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'AUT';
                $data_arr['partyAutoID'] = $detail[$i]['taxSupplierAutoID'];
                $data_arr['partySystemCode'] = $detail[$i]['taxSupplierSystemCode'];
                $data_arr['partyName'] = $detail[$i]['taxSupplierName'];
                $data_arr['partyCurrencyID'] = $detail[$i]['taxSupplierCurrencyID'];
                $data_arr['partyCurrency'] = $detail[$i]['taxSupplierCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $detail[$i]['taxSupplierCurrencyExchangeRate'];
                $data_arr['partyCurrencyAmount'] = $detail[$i]['taxSupplierliabilityType'];
                $data_arr['partyCurrencyDecimalPlaces'] = $detail[$i]['taxSupplierCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = ($detail[$i]['taxAmount'] * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
                $cr_total += $data_arr['gl_cr'];
            }*/

            if ($detail[$i]['type'] == 'Item' && $detail[$i]['itemCategory'] == 'Inventory') {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['expenseGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['expenseSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['expenseGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['expenseGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['expenseGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                $data_arr['gl_dr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                $data_arr['gl_cr'] = 0;
                array_push($item_arr, $data_arr);
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['assetGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['assetSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['assetGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['assetGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['assetGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
            }
            $tax_total += ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
        }

        /*for ($i = 0; $i < count($item_tax_detail); $i++) {
            if(!empty($item_tax_detail[$i]['taxMasterAutoID'])){
                $data_arr_tx['auto_id'] = $item_tax_detail[$i]['invoiceDetailsAutoID'];
                $data_arr_tx['gl_auto_id'] = $item_tax_detail[$i]['taxSupplierliabilityAutoID'];
                $data_arr_tx['gl_code'] = $item_tax_detail[$i]['taxSupplierliabilitySystemGLCode'];
                $data_arr_tx['secondary'] = $item_tax_detail[$i]['taxSupplierliabilityGLAccount'];
                $data_arr_tx['gl_desc'] = $item_tax_detail[$i]['taxSupplierliabilityDescription'];
                $data_arr_tx['gl_type'] = $item_tax_detail[$i]['taxSupplierliabilityType'];
                $data_arr_tx['segment_id'] = $master['segmentID'];
                $data_arr_tx['segment'] = $master['segmentCode'];
                $data_arr_tx['isAddon'] = 0;
                $data_arr_tx['taxMasterAutoID'] = $item_tax_detail[$i]['taxMasterAutoID'];
                $data_arr_tx['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx['subLedgerType'] = null;
                $data_arr_tx['subLedgerDesc'] = null;
                $data_arr_tx['partyContractID'] = null;
                $data_arr_tx['partyType'] = 'AUT';
                $data_arr_tx['partyAutoID'] = $item_tax_detail[$i]['taxSupplierAutoID'];
                $data_arr_tx['partySystemCode'] = $item_tax_detail[$i]['taxSupplierSystemCode'];
                $data_arr_tx['partyName'] = $item_tax_detail[$i]['taxSupplierName'];
                $data_arr_tx['partyCurrencyID'] = $item_tax_detail[$i]['taxSupplierCurrencyID'];
                $data_arr_tx['partyCurrency'] = $item_tax_detail[$i]['taxSupplierCurrency'];
                $data_arr_tx['transactionExchangeRate'] = null;
                $data_arr_tx['companyLocalExchangeRate'] = null;
                $data_arr_tx['companyReportingExchangeRate'] = null;
                $data_arr_tx['partyExchangeRate'] = $item_tax_detail[$i]['taxSupplierCurrencyExchangeRate'];
                $data_arr_tx['partyCurrencyAmount'] = $item_tax_detail[$i]['taxSupplierliabilityType'];
                $data_arr_tx['partyCurrencyDecimalPlaces'] = $item_tax_detail[$i]['taxSupplierCurrencyDecimalPlaces'];
                $data_arr_tx['amount_type'] = 'cr';
                $data_arr_tx['gl_dr'] = 0;
                $data_arr_tx['gl_cr'] = $item_tax_detail[$i]['tottaxAmount'];
                array_push($item_tax_arr, $data_arr_tx);
                $cr_total += $data_arr_tx['gl_cr'];
            }
        }*/


        for ($i = 0; $i < count($taxledger); $i++) {
            if (!empty($taxledger[$i]['taxMasterAutoID'])) {
                $data_arr['auto_id'] = $taxledger[$i]['documentDetailAutoID'];
                $data_arr['gl_auto_id'] = $taxledger[$i]['taxGlAutoID'];
                $data_arr['gl_code'] = $taxledger[$i]['SystemGLCode'];
                $data_arr['secondary'] = $taxledger[$i]['GLCode'];
                $data_arr['gl_desc'] = $taxledger[$i]['GLDescription'] . ' - Tax';
                $data_arr['gl_type'] = $taxledger[$i]['GLType'];
                $data_arr['segment_id'] = null;
                $data_arr['segment'] = null;
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = $taxledger[$i]['taxMasterAutoID'];
                $data_arr['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr['subLedgerType'] = null;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'AUT';
                $data_arr['partyAutoID'] = $taxledger[$i]['taxAuthorityAutoID'];
                $data_arr['partySystemCode'] = $taxledger[$i]['supplierSystemCode'];
                $data_arr['partyName'] = $taxledger[$i]['supplierName'];
                $data_arr['partyCurrencyID'] = $taxledger[$i]['supplierCurrencyID'];
                $data_arr['partyCurrency'] = null;
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = 1;
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = null;
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $taxledger[$i]['amount'];
                array_push($gl_array['gl_detail'], $data_arr);
                $cr_total += $data_arr['gl_cr'];
            }
        }

        for ($i = 0; $i < count($extraCharge); $i++) {
            $data_arr['auto_id'] = $extraCharge[$i]['extraChargeDetailID'];
            $data_arr['gl_auto_id'] = $extraCharge[$i]['GLAutoID'];
            $data_arr['gl_code'] = $extraCharge[$i]['systemGLCode'];
            $data_arr['secondary'] = $extraCharge[$i]['GLCode'];
            $data_arr['gl_desc'] = $extraCharge[$i]['GLDescription'] . ' - Extra Charge';
            $data_arr['gl_type'] = $extraCharge[$i]['GLType'];
            $data_arr['segment_id'] = $extraCharge[$i]['segmentID'];
            $data_arr['segment'] = $extraCharge[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = $extraCharge[$i]['customerCurrencyID'];
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = 1;
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = $extraCharge[$i]['transactionAmount'];
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total += $data_arr['gl_cr'];
        }

        for ($i = 0; $i < count($discountIsCharge); $i++) {
            $data_arr['auto_id'] = $discountIsCharge[$i]['discountDetailID'];
            $data_arr['gl_auto_id'] = $discountIsCharge[$i]['GLAutoID'];
            $data_arr['gl_code'] = $discountIsCharge[$i]['systemGLCode'];
            $data_arr['secondary'] = $discountIsCharge[$i]['GLCode'];
            $data_arr['gl_desc'] = $discountIsCharge[$i]['GLDescription'] . ' - Discount';
            $data_arr['gl_type'] = $discountIsCharge[$i]['GLType'];
            $data_arr['segment_id'] = $discountIsCharge[$i]['segmentID'];
            $data_arr['segment'] = $discountIsCharge[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = $discountIsCharge[$i]['customerCurrencyID'];
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = 1;
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = ($discountIsCharge[$i]['discountPercentage'] * $tottransamnt) / 100;
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total -= ($discountIsCharge[$i]['discountPercentage'] * $tottransamnt) / 100;
        }

        $cr_p_arr = $this->array_group_sum_tax($cr_p_arr);
        $cr_m_arr = $this->array_group_sum_tax($cr_m_arr);
        $item_arr = $this->array_group_sum_tax($item_arr);
        foreach ($cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        foreach ($item_tax_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }


        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $master['customerReceivableAutoID'];
        $data_arr['gl_code'] = $master['customerReceivableSystemGLCode'];
        $data_arr['secondary'] = $master['customerReceivableGLAccount'];
        $data_arr['gl_desc'] = $master['customerReceivableDescription'];
        $data_arr['gl_type'] = $master['customerReceivableType'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];
        $data_arr['gl_dr'] = $cr_total;
        $data_arr['gl_cr'] = 0;
        $data_arr['amount_type'] = 'dr';
        $data_arr['isAddon'] = 0;
        $data_arr['taxMasterAutoID'] = null;
        $data_arr['partyVatIdNo'] = null;
        $data_arr['subLedgerType'] = 3;
        $data_arr['subLedgerDesc'] = 'AR';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = 'CUS';
        $data_arr['partyAutoID'] = $master['customerID'];
        $data_arr['partySystemCode'] = $master['customerSystemCode'];
        $data_arr['partyName'] = $master['customerName'];
        $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
        $data_arr['partyCurrency'] = $master['customerCurrency'];
        $data_arr['transactionExchangeRate'] = null;
        $data_arr['companyLocalExchangeRate'] = null;
        $data_arr['companyReportingExchangeRate'] = null;
        $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
        $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
        array_push($gl_array['gl_detail'], $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'CINV';
        $gl_array['name'] = 'Customer Invoice';
        $gl_array['primary_Code'] = $master['invoiceCode'];
        $gl_array['customername'] = $master['customerName'];
        $gl_array['date'] = $master['invoiceDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['customername'] = $master['customerNamemaster'];
        return $gl_array;
    }


    function fetch_double_entry_customer_invoice_data_insurance($invoiceAutoID, $code = null)
    {
        $gl_array = array();
        $tax_total = 0;
        $cr_total = 0;
        $gl_array['gl_detail'] = array();
        $this->db->select('srp_erp_customerinvoicemaster.*,srp_erp_customermaster.vatIdNo as vatIdNo');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $detail = $this->db->get('srp_erp_customerinvoicedetails')->result_array();


        $sumamnt = "SELECT 
                        sum(transactionAmount) as transactionAmount
                    FROM
                        `srp_erp_customerinvoicedetails`
                    WHERE
                        `invoiceAutoID` = $invoiceAutoID
                    GROUP BY
                            InvoiceAutoID";
        $sumdetail = $this->db->query($sumamnt)->row_array();

        $sumsup = "SELECT 
                        marginAmount as transactionAmount,marginGLAutoID,invoiceDetailsAutoID,segmentID,segmentCode,srp_erp_customerinvoicedetails.supplierAutoID as supplierAutoID,srp_erp_suppliermaster.supplierName as supplierName,srp_erp_suppliermaster.supplierSystemCode as supplierSystemCode,srp_erp_suppliermaster.supplierCurrencyID as supplierCurrencyID,srp_erp_suppliermaster.supplierCurrency as supplierCurrency,srp_erp_suppliermaster.supplierCurrencyDecimalPlaces as supplierCurrencyDecimalPlaces
                    FROM
                        `srp_erp_customerinvoicedetails`
                    Left JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_customerinvoicedetails.supplierAutoID
                    WHERE
                        `invoiceAutoID` = $invoiceAutoID";
        $sumsupdetail = $this->db->query($sumsup)->result_array();

        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode ,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate, supplierCurrencyDecimalPlaces,supplierCurrencyID,taxMasterAutoID');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $tax_detail = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();


        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $extraCharge = $this->db->get('srp_erp_customerinvoiceextrachargedetails')->result_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('isChargeToExpense', 1);
        $discountIsCharge = $this->db->get('srp_erp_customerinvoicediscountdetails')->result_array();

        $qry = "SELECT
	SUM(transactionAmount) as tottransactionAmount,SUM(totalAfterTax) as totalAfterTax,SUM(marginAmount) as marginAmount
FROM
	srp_erp_customerinvoicedetails
WHERE
	invoiceAutoID = $invoiceAutoID";
        $sumdetails = $this->db->query($qry)->row_array();
        $tottransamntmargin = $sumdetails['marginAmount'];
        $tottransamnt = $sumdetails['tottransactionAmount'];

        $this->db->select('SUM(discountPercentage) as discountPercentage');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $totdiscount = $this->db->get('srp_erp_customerinvoicediscountdetails')->row_array();
        $totdisc = 0;
        if ($totdiscount) {
            $totdisc = ($tottransamnt * $totdiscount['discountPercentage']) / 100;
        }

        $this->db->select('SUM(discountPercentage) as discountPercentage');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('isChargeToExpense', 0);
        $discount = $this->db->get('srp_erp_customerinvoicediscountdetails')->row_array();
        $discountamnt = 0;
        if ($discount) {
            $discountamnt = ($tottransamnt * $discount['discountPercentage']) / 100;
        }

        $cr_p_arr = array();
        $cr_m_arr = array();
        $e_cr_p_arr = array();
        $e_cr_m_arr = array();
        $item_arr = array();
        $item_tax_arr = 0;
        $tax_extracharge = 0;
        $cr_total = $sumdetail['transactionAmount'] - $discountamnt;
        $tax_total = $sumdetail['transactionAmount'];
        for ($x = 0; $x < count($sumsupdetail); $x++) {
            $gldetails = fetch_gl_account_desc($sumsupdetail[$x]['marginGLAutoID']);
            $data_arr['auto_id'] = $sumsupdetail[$x]['invoiceDetailsAutoID'];
            $data_arr['gl_auto_id'] = $sumsupdetail[$x]['marginGLAutoID'];
            $data_arr['gl_code'] = $gldetails['systemAccountCode'];
            $data_arr['secondary'] = $gldetails['GLSecondaryCode'];
            $data_arr['gl_desc'] = $gldetails['GLDescription'] . ' - Margin';
            $data_arr['gl_type'] = $gldetails['masterCategory'];
            $data_arr['segment_id'] = $sumsupdetail[$x]['segmentID'];
            $data_arr['segment'] = $sumsupdetail[$x]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'SUP';
            $data_arr['partyAutoID'] = $sumsupdetail[$x]['supplierAutoID'];
            $data_arr['partySystemCode'] = $sumsupdetail[$x]['supplierSystemCode'];
            $data_arr['partyName'] = $sumsupdetail[$x]['supplierName'];
            $data_arr['partyCurrencyID'] = $sumsupdetail[$x]['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $sumsupdetail[$x]['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = $sumsupdetail[$x]['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $sumsupdetail[$x]['supplierCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'cr';
            if ($discount['discountPercentage'] > 0) {
                $discamnt = ($sumsupdetail[$x]['transactionAmount'] * $discountamnt) / $tottransamntmargin;
                $amount = $discamnt - $sumsupdetail[$x]['transactionAmount'];
            } else {
                $amount = $sumsupdetail[$x]['transactionAmount'];
            }

            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = $amount;
            array_push($gl_array['gl_detail'], $data_arr);

            /*$data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = $sumsupdetail[$x]['transactionAmount'];
            array_push($gl_array['gl_detail'], $data_arr);*/
            //$cr_total += abs($data_arr['gl_cr']);
        }


        for ($i = 0; $i < count($extraCharge); $i++) {
            $data_arr['auto_id'] = $extraCharge[$i]['extraChargeDetailID'];
            $data_arr['gl_auto_id'] = $extraCharge[$i]['GLAutoID'];
            $data_arr['gl_code'] = $extraCharge[$i]['systemGLCode'];
            $data_arr['secondary'] = $extraCharge[$i]['GLCode'];
            $data_arr['gl_desc'] = $extraCharge[$i]['GLDescription'] . ' - Extra Charge';
            $data_arr['gl_type'] = $extraCharge[$i]['GLType'];
            $data_arr['segment_id'] = $extraCharge[$i]['segmentID'];
            $data_arr['segment'] = $extraCharge[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = $extraCharge[$i]['customerCurrencyID'];
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = 1;
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = $extraCharge[$i]['transactionAmount'];
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total += abs($data_arr['gl_cr']);
            if ($extraCharge[$i]['isTaxApplicable'] == 1) {
                $tax_extracharge += $extraCharge[$i]['transactionAmount'];
            }
        }

        for ($i = 0; $i < count($tax_detail); $i++) {
            $data_arr['auto_id'] = $tax_detail[$i]['taxDetailAutoID'];
            $data_arr['gl_auto_id'] = $tax_detail[$i]['GLAutoID'];
            $data_arr['gl_code'] = $tax_detail[$i]['SystemGLCode'];
            $data_arr['secondary'] = $tax_detail[$i]['GLCode'];
            $data_arr['gl_desc'] = $tax_detail[$i]['GLDescription'] . ' - Tax All';
            $data_arr['gl_type'] = $tax_detail[$i]['GLType'];
            $data_arr['segment_id'] = $tax_detail[$i]['segmentID'];
            $data_arr['segment'] = $tax_detail[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = $tax_detail[$i]['taxMasterAutoID'];
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'AUT';
            $data_arr['partyAutoID'] = $tax_detail[$i]['supplierAutoID'];
            $data_arr['partySystemCode'] = $tax_detail[$i]['supplierSystemCode'];
            $data_arr['partyName'] = $tax_detail[$i]['supplierName'];
            $data_arr['partyCurrencyID'] = $tax_detail[$i]['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $tax_detail[$i]['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $tax_detail[$i]['supplierCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $tax_detail[$i]['supplierCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = (($tax_detail[$i]['taxPercentage'] / 100) * (($tax_total + $tax_extracharge) - $totdisc));
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total += abs($data_arr['gl_cr']);
            $item_tax_arr += $data_arr['gl_cr'];
        }


        for ($i = 0; $i < count($discountIsCharge); $i++) {
            $data_arr['auto_id'] = $discountIsCharge[$i]['discountDetailID'];
            $data_arr['gl_auto_id'] = $discountIsCharge[$i]['GLAutoID'];
            $data_arr['gl_code'] = $discountIsCharge[$i]['systemGLCode'];
            $data_arr['secondary'] = $discountIsCharge[$i]['GLCode'];
            $data_arr['gl_desc'] = $discountIsCharge[$i]['GLDescription'] . ' - Discount';
            $data_arr['gl_type'] = $discountIsCharge[$i]['GLType'];
            $data_arr['segment_id'] = $discountIsCharge[$i]['segmentID'];
            $data_arr['segment'] = $discountIsCharge[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = $discountIsCharge[$i]['customerCurrencyID'];
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = 1;
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = ($discountIsCharge[$i]['discountPercentage'] * $tottransamnt) / 100;
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total -= ($discountIsCharge[$i]['discountPercentage'] * $tottransamnt) / 100;
        }

        $cr_p_arr = $this->array_group_sum_tax($cr_p_arr);
        $cr_m_arr = $this->array_group_sum_tax($cr_m_arr);
        $item_arr = $this->array_group_sum_tax($item_arr);
        foreach ($cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        /*foreach ($item_tax_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }*/

        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $master['customerReceivableAutoID'];
        $data_arr['gl_code'] = $master['customerReceivableSystemGLCode'];
        $data_arr['secondary'] = $master['customerReceivableGLAccount'];
        $data_arr['gl_desc'] = $master['customerReceivableDescription'];
        $data_arr['gl_type'] = $master['customerReceivableType'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];
        $data_arr['gl_dr'] = $cr_total;
        $data_arr['gl_cr'] = 0;
        $data_arr['amount_type'] = 'dr';
        $data_arr['isAddon'] = 0;
        $data_arr['taxMasterAutoID'] = null;
        $data_arr['partyVatIdNo'] = null;
        $data_arr['subLedgerType'] = 3;
        $data_arr['subLedgerDesc'] = 'AR';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = 'CUS';
        $data_arr['partyAutoID'] = $master['customerID'];
        $data_arr['partySystemCode'] = $master['customerSystemCode'];
        $data_arr['partyName'] = $master['customerName'];
        $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
        $data_arr['partyCurrency'] = $master['customerCurrency'];
        $data_arr['transactionExchangeRate'] = null;
        $data_arr['companyLocalExchangeRate'] = null;
        $data_arr['companyReportingExchangeRate'] = null;
        $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
        $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
        array_push($gl_array['gl_detail'], $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['approved_yn'] = $master['approvedYN'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'CINV';
        $gl_array['name'] = 'Customer Invoice';
        $gl_array['primary_Code'] = $master['invoiceCode'];
        $gl_array['date'] = $master['invoiceDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }


    function fetch_double_entry_supplier_invoices_insurance_data($InvoiceAutoID, $code = null)
    {
        $gl_array = array();
        $gl_array['gl_detail'] = array();
        $cr_total = 0;
        $dr_total = 0;
        $this->db->select('srp_erp_paysupplierinvoicemaster.*,srp_erp_suppliermaster.vatIdNo as vatIdNo,srp_erp_suppliermaster.supplierName as suppliernamemaster');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID');
        $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $this->db->select('*,srp_erp_paysupplierinvoicedetail.systemGLCode as sglcd');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $detail = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate,supplierCurrencyDecimalPlaces,supplierCurrencyID,taxMasterAutoID');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $tax_detail = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();
        $taxttlitm = 0;
        // echo '<pre>';print_r($detail); echo '</pre>'; die();
        for ($i = 0; $i < count($detail); $i++) {
            $cr_total += $detail[$i]['transactionAmount'];
        }


        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $master['supplierliabilityAutoID'];
        $data_arr['gl_code'] = $master['supplierliabilitySystemGLCode'];
        $data_arr['secondary'] = $master['supplierliabilityGLAccount'];
        $data_arr['gl_desc'] = $master['supplierliabilityDescription'];
        $data_arr['gl_type'] = $master['supplierliabilityType'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];
        $data_arr['gl_dr'] = 0;
        $data_arr['gl_cr'] = $cr_total;
        $data_arr['amount_type'] = 'cr';
        $data_arr['isAddon'] = 0;
        $data_arr['taxMasterAutoID'] = null;
        $data_arr['partyVatIdNo'] = null;
        $data_arr['subLedgerType'] = 2;
        $data_arr['subLedgerDesc'] = 'AP';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = 'SUP';
        $data_arr['partyAutoID'] = $master['supplierID'];
        $data_arr['partySystemCode'] = $master['supplierCode'];
        $data_arr['partyName'] = $master['supplierName'];
        $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
        $data_arr['partyCurrency'] = $master['supplierCurrency'];
        $data_arr['transactionExchangeRate'] = null;
        $data_arr['companyLocalExchangeRate'] = null;
        $data_arr['companyReportingExchangeRate'] = null;
        $data_arr['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
        $data_arr['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
        $data_arr['partyCurrencyAmount'] = 0;
        array_push($gl_array['gl_detail'], $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['approved_YN'] = $master['approvedYN'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'BSI';
        $gl_array['name'] = $master['invoiceType'] . ' BSI';
        $gl_array['primary_Code'] = $master['bookingInvCode'];
        $gl_array['date'] = $master['bookingDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['suppliernamemaster'] = $master['suppliernamemaster'];
        $gl_array['master_data'] = $master;
        //echo '<pre>';print_r($gl_array); echo '</pre>'; die();
        return $gl_array;
    }

    function fetch_double_entry_delivery_order($orderID)
    {
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_deliveryorder', trim($orderID), 'DO', 'DOAutoID');
        $this->db->select('srp_erp_deliveryorder.*,srp_erp_customermaster.vatIdNo as vatIdNo,srp_erp_customermaster.customerName as customer_name');
        $this->db->where('DOAutoID', $orderID);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_deliveryorder.customerID', 'left');
        $master = $this->db->get('srp_erp_deliveryorder')->row_array();

        $detail = $this->db->get_where('srp_erp_deliveryorderdetails', ['DOAutoID' => $orderID])->result_array();

        $gl_array = array();
        $tax_total = 0;
        $cr_total = 0;
        $gl_array['gl_detail'] = array();
        $cr_p_arr = array();
        $cr_m_arr = array();
        $e_cr_p_arr = array();
        $e_cr_m_arr = array();
        $item_arr = array();
        $item_tax_arr = array();

        for ($i = 0; $i < count($detail); $i++) {
            $data_arr['auto_id'] = $detail[$i]['DODetailsAutoID'];
            $data_arr['gl_auto_id'] = $detail[$i]['revenueGLAutoID'];
            $data_arr['gl_code'] = $detail[$i]['revenueSystemGLCode'];
            $data_arr['secondary'] = $detail[$i]['revenueGLCode'];
            $data_arr['gl_desc'] = $detail[$i]['revenueGLDescription'];
            $data_arr['gl_type'] = $detail[$i]['revenueGLType'];
            $data_arr['segment_id'] = $detail[$i]['segmentID'];
            $data_arr['segment'] = $detail[$i]['segmentCode'];
            $data_arr['projectID'] = (empty($detail[$i]['projectID'])) ? null : $detail[$i]['projectID'];
            $data_arr['projectExchangeRate'] = (empty($detail[$i]['projectExchangeRate'])) ? null : $detail[$i]['projectExchangeRate'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            $data_arr['partyCurrencyAmount'] = null;
            $data_arr['amount_type'] = 'dr';

            if ($isGroupByTax == 1) {
                $amount = ($detail[$i]['deliveredTransactionAmount'] - $detail[$i]['taxAmount']);
            } else {
                $amount = ($detail[$i]['deliveredTransactionAmount'] - ($detail[$i]['taxAmount'] * $detail[$i]['deliveredQty']));
            }

            if ($detail[$i]['type'] == 'Item') {
                if ($amount <= 0) {
                    $data_arr['gl_dr'] = $amount;
                    $data_arr['gl_cr'] = 0;
                    array_push($cr_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $amount;
                    $data_arr['amount_type'] = 'cr';
                    array_push($cr_m_arr, $data_arr);
                }
            } else {
                if ($amount <= 0) {
                    $data_arr['gl_dr'] = $amount;
                    $data_arr['gl_cr'] = 0;
                    array_push($e_cr_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $amount;
                    $data_arr['amount_type'] = 'cr';
                    array_push($e_cr_m_arr, $data_arr);
                }
            }
            $cr_total += $data_arr['gl_cr'];


            if ($detail[$i]['type'] == 'Item' && $detail[$i]['itemCategory'] == 'Inventory') {
                $data_arr['auto_id'] = $detail[$i]['DODetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['expenseGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['expenseSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['expenseGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['expenseGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['expenseGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                $data_arr['gl_dr'] = (ROUND((($detail[$i]['companyLocalWacAmount'] / $detail[$i]['conversionRateUOM']) / (1 / $master['companyLocalExchangeRate'])), $master['transactionCurrencyDecimalPlaces']) * $detail[$i]['deliveredQty']);
                $data_arr['gl_cr'] = 0;
                array_push($item_arr, $data_arr);

                $data_arr['auto_id'] = $detail[$i]['DODetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['assetGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['assetSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['assetGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['assetGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['assetGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = (ROUND((($detail[$i]['companyLocalWacAmount'] / $detail[$i]['conversionRateUOM']) / (1 / $master['companyLocalExchangeRate'])), $master['transactionCurrencyDecimalPlaces']) * $detail[$i]['deliveredQty']);
                array_push($item_arr, $data_arr);
            }
            $tax_total += ($detail[$i]['deliveredTransactionAmount'] - $detail[$i]['totalAfterTax']);
        }


        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode ,segmentID, supplierAutoID,supplierSystemCode,supplierName,
                           supplierCurrency,supplierCurrencyExchangeRate, supplierCurrencyDecimalPlaces,supplierCurrencyID,taxMasterAutoID');
        $tax_detail = $this->db->where('DOAutoID', $orderID)->get('srp_erp_deliveryordertaxdetails ')->result_array();


        $item_tax_detail = $this->db->query("SELECT *, SUM(taxAmount * deliveredQty) AS tottaxAmount FROM srp_erp_deliveryorderdetails
                                      WHERE DOAutoID = {$orderID} GROUP BY taxMasterAutoID")->result_array();

        $item_tax_group_wise = $this->db->query("SELECT * FROM
                                        (
                                            SELECT
                                                documentDetailAutoID AS auto_id, taxGlAutoID AS gl_auto_id, SUM(amount) AS taxAmount, taxMasterID 
                                            FROM srp_erp_taxledger
                                            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                            WHERE documentID = 'DO' AND documentMasterAutoID = {$orderID} 
                                            GROUP BY taxMasterID 
                                        ) t1
                                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id")->result_array();

        for ($i = 0; $i < count($item_tax_detail); $i++) {
            if (!empty($item_tax_detail[$i]['taxMasterAutoID'])) {
                $data_arr_tx['auto_id'] = $item_tax_detail[$i]['DODetailsAutoID'];
                $data_arr_tx['gl_auto_id'] = $item_tax_detail[$i]['taxSupplierliabilityAutoID'];
                $data_arr_tx['gl_code'] = $item_tax_detail[$i]['taxSupplierliabilitySystemGLCode'];
                $data_arr_tx['secondary'] = $item_tax_detail[$i]['taxSupplierliabilityGLAccount'];
                $data_arr_tx['gl_desc'] = $item_tax_detail[$i]['taxSupplierliabilityDescription'];
                $data_arr_tx['gl_type'] = $item_tax_detail[$i]['taxSupplierliabilityType'];
                $data_arr_tx['segment_id'] = $master['segmentID'];
                $data_arr_tx['segment'] = $master['segmentCode'];
                $data_arr_tx['isAddon'] = 0;
                $data_arr_tx['taxMasterAutoID'] = $item_tax_detail[$i]['taxMasterAutoID'];
                $data_arr_tx['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx['subLedgerType'] = null;
                $data_arr_tx['subLedgerDesc'] = null;
                $data_arr_tx['partyContractID'] = null;
                $data_arr_tx['partyType'] = 'AUT';
                $data_arr_tx['partyAutoID'] = $item_tax_detail[$i]['taxSupplierAutoID'];
                $data_arr_tx['partySystemCode'] = $item_tax_detail[$i]['taxSupplierSystemCode'];
                $data_arr_tx['partyName'] = $item_tax_detail[$i]['taxSupplierName'];
                $data_arr_tx['partyCurrencyID'] = $item_tax_detail[$i]['taxSupplierCurrencyID'];
                $data_arr_tx['partyCurrency'] = $item_tax_detail[$i]['taxSupplierCurrency'];
                $data_arr_tx['transactionExchangeRate'] = null;
                $data_arr_tx['companyLocalExchangeRate'] = null;
                $data_arr_tx['companyReportingExchangeRate'] = null;
                $data_arr_tx['partyExchangeRate'] = $item_tax_detail[$i]['taxSupplierCurrencyExchangeRate'];
                $data_arr_tx['partyCurrencyAmount'] = $item_tax_detail[$i]['taxSupplierliabilityType'];
                $data_arr_tx['partyCurrencyDecimalPlaces'] = $item_tax_detail[$i]['taxSupplierCurrencyDecimalPlaces'];
                $data_arr_tx['amount_type'] = 'cr';
                $data_arr_tx['gl_dr'] = 0;
                $data_arr_tx['gl_cr'] = $item_tax_detail[$i]['tottaxAmount'];
                array_push($item_tax_arr, $data_arr_tx);
                $cr_total += $data_arr_tx['gl_cr'];
            }
        }


        for ($i = 0; $i < count($tax_detail); $i++) {
            $data_arr['auto_id'] = $tax_detail[$i]['taxDetailAutoID'];
            $data_arr['gl_auto_id'] = $tax_detail[$i]['GLAutoID'];
            $data_arr['gl_code'] = $tax_detail[$i]['SystemGLCode'];
            $data_arr['secondary'] = $tax_detail[$i]['GLCode'];
            $data_arr['gl_desc'] = $tax_detail[$i]['GLDescription'] . ' - Tax All';
            $data_arr['gl_type'] = $tax_detail[$i]['GLType'];
            $data_arr['segment_id'] = $tax_detail[$i]['segmentID'];
            $data_arr['segment'] = $tax_detail[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = $tax_detail[$i]['taxMasterAutoID'];
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'AUT';
            $data_arr['partyAutoID'] = $tax_detail[$i]['supplierAutoID'];
            $data_arr['partySystemCode'] = $tax_detail[$i]['supplierSystemCode'];
            $data_arr['partyName'] = $tax_detail[$i]['supplierName'];
            $data_arr['partyCurrencyID'] = $tax_detail[$i]['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $tax_detail[$i]['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $tax_detail[$i]['supplierCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $tax_detail[$i]['supplierCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = (($tax_detail[$i]['taxPercentage'] / 100) * $tax_total);
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total += $data_arr['gl_cr'];
        }

        for ($i = 0; $i < count($item_tax_group_wise); $i++) {
            if (!empty($item_tax_group_wise[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_tax_group_wise[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_tax_group_wise[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_tax_group_wise[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_tax_group_wise[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = $master['segmentID'];
                $data_arr_tx_group['segment'] = $master['segmentCode'];
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = $master['vatIdNo'];
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = $master['customerID'];
                $data_arr_tx_group['partySystemCode'] = $master['customerSystemCode'];
                $data_arr_tx_group['partyName'] = $master['customerName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['customerCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = null;
                $data_arr_tx_group['companyLocalExchangeRate'] = null;
                $data_arr_tx_group['companyReportingExchangeRate'] = null;
                $data_arr_tx_group['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = '';
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'cr';
                $data_arr_tx_group['gl_dr'] = 0;
                $data_arr_tx_group['gl_cr'] = $item_tax_group_wise[$i]['taxAmount'];
                array_push($item_tax_arr, $data_arr_tx_group);
                $cr_total += $data_arr_tx_group['gl_cr'];
            }
        }


        $cr_p_arr = $this->array_group_sum_tax($cr_p_arr);
        $cr_m_arr = $this->array_group_sum_tax($cr_m_arr);
        $item_arr = $this->array_group_sum_tax($item_arr);
        foreach ($cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        foreach ($item_tax_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }


        /*Un billed entry*/
        $companyID = current_companyID();
        $un_billed_gl = $this->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory FROM srp_erp_chartofaccounts
                                          WHERE GLAutoID = (
                                              SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'UBI' AND companyID = {$companyID}
                                          ) AND companyID={$companyID} ")->row_array();
        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $un_billed_gl['GLAutoID'];
        $data_arr['gl_code'] = $un_billed_gl['systemAccountCode'];
        $data_arr['secondary'] = $un_billed_gl['GLSecondaryCode'];
        $data_arr['gl_desc'] = $un_billed_gl['GLDescription'];
        $data_arr['gl_type'] = $un_billed_gl['subCategory'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];
        $data_arr['gl_dr'] = $cr_total;
        $data_arr['gl_cr'] = 0;
        $data_arr['amount_type'] = 'dr';
        $data_arr['isAddon'] = 0;
        $data_arr['taxMasterAutoID'] = null;
        $data_arr['partyVatIdNo'] = null;
        $data_arr['subLedgerType'] = 5;
        $data_arr['subLedgerDesc'] = 'UBI';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = 'CUS';
        $data_arr['partyAutoID'] = $master['customerID'];
        $data_arr['partySystemCode'] = $master['customerSystemCode'];
        $data_arr['partyName'] = $master['customerName'];
        $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
        $data_arr['partyCurrency'] = $master['customerCurrency'];
        $data_arr['transactionExchangeRate'] = null;
        $data_arr['companyLocalExchangeRate'] = null;
        $data_arr['companyReportingExchangeRate'] = null;
        $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
        $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
        array_push($gl_array['gl_detail'], $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['approved_yn'] = $master['approvedYN'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'DO';
        $gl_array['name'] = 'Delivery Order';
        $gl_array['primary_Code'] = $master['DOCode'];
        $gl_array['date'] = $master['DODate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['customername'] = $master['customer_name'];
        return $gl_array;
    }

    /*Sales Return Buyback Double Entry */
    function fetch_double_entry_sales_return_buyback_data($salesReturnAutoID, $documentID) /*, $code = null*/
    {
        $this->db->select('srp_erp_salesreturnmaster.*,srp_erp_customermaster.customerName as customerNamemaster');
        $this->db->where('salesReturnAutoID', $salesReturnAutoID);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_salesreturnmaster.customerID', 'left');
        $master = $this->db->get('srp_erp_salesreturnmaster')->row_array();

        /** Cost Related GL entries */
        $GLEntry_cost = $this->db->query("SELECT
                                    detailTbl.segmentID as segmentID,
                                    detailTbl.segmentCode as segmentCode,
                                    'cr' as amountType,
                                    null as subLedgerType,
                                    null as subLedgerDesc,
                                    detailTbl.expenseGLAutoID  AS GLAutoID, 
                                    detailTbl.expenseSystemGLCode  AS SystemGLCode, 
                                    detailTbl.expenseGLCode AS GLSecondaryCode, 
                                    detailTbl.expenseGLDescription AS GLDescription,
                                    detailTbl.segmentCode AS segmentCode,
                                    detailTbl.expenseGLType AS GLType,
                                    ABS(SUM( ( detailTbl.currentWacAmount * detailTbl.return_Qty ) / ( 1 / masterTbl.companyLocalExchangeRate ) )) *-1 AS credit,
                                    0 as debit,
                                    ABS(SUM( ( detailTbl.currentWacAmount * detailTbl.return_Qty ) / ( 1 / masterTbl.companyLocalExchangeRate ) )) *-1 AS transactionAmount,
                                    masterTbl.transactionCurrencyDecimalPlaces as transactionDecimal,
                                    masterTbl.approvedYN as approvedYN,
                                    detailTbl.salesReturnAutoID as auto_id                              
                                FROM srp_erp_salesreturnmaster masterTbl
                                LEFT JOIN srp_erp_salesreturndetails detailTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID
                                WHERE detailTbl.salesReturnAutoID = '{$salesReturnAutoID}'
                                GROUP BY detailTbl.expenseGLAutoID")->result_array();

        /** Inventory GL entries */
        $GLEntry_inventory = $this->db->query("SELECT
                                    detailTbl.segmentID as segmentID,
                                    detailTbl.segmentCode as segmentCode,
                                    'dr' as amountType,
                                    null as subLedgerType,
                                    null as subLedgerDesc,
                                    detailTbl.assetGLAutoID  AS GLAutoID, 
                                    detailTbl.assetSystemGLCode  AS SystemGLCode, 
                                    detailTbl.assetGLCode AS GLSecondaryCode, 
                                    detailTbl.assetGLDescription AS GLDescription,
                                    detailTbl.segmentCode AS segmentCode,
                                    detailTbl.assetGLType AS GLType,
                                    0 AS credit,
                                    ABS(SUM( ( detailTbl.currentWacAmount * detailTbl.return_Qty ) / ( 1 / masterTbl.companyLocalExchangeRate ) )) as debit,
                                    ABS(SUM( ( detailTbl.currentWacAmount * detailTbl.return_Qty ) / ( 1 / masterTbl.companyLocalExchangeRate ) ))  AS transactionAmount,
                                    masterTbl.transactionCurrencyDecimalPlaces as transactionDecimal,
                                    masterTbl.approvedYN as approvedYN,
                                detailTbl.salesReturnAutoID as auto_id
                                FROM srp_erp_salesreturnmaster masterTbl
                                LEFT JOIN srp_erp_salesreturndetails detailTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID
                                WHERE detailTbl.salesReturnAutoID = '{$salesReturnAutoID}'
                                GROUP BY detailTbl.assetGLAutoID")->result_array();

        /** Revenue GL entries */
        $GLEntry_revenue = $this->db->query("SELECT
                                    detailTbl.segmentID as segmentID,
                                    detailTbl.segmentCode as segmentCode,
                                    'dr' as amountType,
                                    null as subLedgerType,
                                    null as subLedgerDesc,
                                    detailTbl.revenueGLAutoID  AS GLAutoID, 
                                    detailTbl.revenueSystemGLCode AS SystemGLCode, 
                                    detailTbl.revenueGLCode AS GLSecondaryCode, 
                                    detailTbl.revenueGLDescription AS GLDescription,
                                    detailTbl.segmentCode AS segmentCode,
                                    detailTbl.revenueGLType AS GLType,
                                    0 AS credit,
                                    ABS(SUM( detailTbl.return_Qty * ROUND(((IFNULL(taxAmount,0)) + detailTbl.salesPrice), masterTbl.transactionCurrencyDecimalPlaces)))  as debit,
                                    ABS(SUM( detailTbl.return_Qty * ROUND(((IFNULL(taxAmount,0)) + detailTbl.salesPrice), masterTbl.transactionCurrencyDecimalPlaces)))   AS transactionAmount,
                                    masterTbl.transactionCurrencyDecimalPlaces as transactionDecimal,
                                    masterTbl.approvedYN as approvedYN,
                                detailTbl.salesReturnAutoID as auto_id
                                FROM srp_erp_salesreturnmaster masterTbl
                                LEFT JOIN srp_erp_salesreturndetails detailTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID
                                 LEFT JOIN srp_erp_customerinvoicedetails invoiceDetail ON invoiceDetail.invoiceAutoID = detailTbl.invoiceAutoID AND `invoiceDetail`.`InvoiceDetailsAutoID` = `detailTbl`.`invoiceDetailID`
                                WHERE detailTbl.salesReturnAutoID = '{$salesReturnAutoID}'
                                GROUP BY detailTbl.revenueGLAutoID")->result_array();

        /** Receivable GL entries */
        $GLEntry_receivable = $this->db->query("SELECT
                                    detailTbl.segmentID as segmentID,
                                    detailTbl.segmentCode as segmentCode,
                                    'cr' as amountType,
                                    '3' as subLedgerType,
                                    'AR' as subLedgerDesc,
                                    masterTbl.customerReceivableAutoID  AS GLAutoID, 
                                    masterTbl.customerReceivableSystemGLCode AS SystemGLCode, 
                                    coa.GLSecondaryCode AS GLSecondaryCode, 
                                    masterTbl.customerReceivableDescription AS GLDescription,
                                    detailTbl.segmentCode AS segmentCode,
                                    coa.subCategory AS GLType,
                                    ABS(SUM(detailTbl.return_Qty * ROUND(((IFNULL(taxAmount,0)) + detailTbl.salesPrice),masterTbl.transactionCurrencyDecimalPlaces))) * -1  AS credit,
                                    0 AS debit,
                                    ABS(SUM(detailTbl.return_Qty * ROUND(((IFNULL(taxAmount,0)) + detailTbl.salesPrice),masterTbl.transactionCurrencyDecimalPlaces))) * -1  AS transactionAmount,
                                    masterTbl.transactionCurrencyDecimalPlaces as transactionDecimal,
                                    masterTbl.approvedYN as approvedYN,
                                detailTbl.salesReturnAutoID as auto_id
                                FROM srp_erp_salesreturnmaster masterTbl
                                LEFT JOIN srp_erp_salesreturndetails detailTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID
                                LEFT JOIN srp_erp_customerinvoicedetails invoiceDetail ON invoiceDetail.invoiceAutoID = detailTbl.invoiceAutoID AND `invoiceDetail`.`InvoiceDetailsAutoID` = `detailTbl`.`invoiceDetailID`
                                JOIN srp_erp_chartofaccounts coa ON coa.GLAutoID = masterTbl.customerReceivableAutoID
                                WHERE detailTbl.salesReturnAutoID = '{$salesReturnAutoID}' /*AND detailTbl.DOAutoID IS NULL*/ ")->result_array();

        /** Un billed invoice GL entry*/
        $companyID = current_companyID();
        $GLEntry_UBI = $this->db->query("SELECT
                                    detailTbl.segmentID as segmentID,
                                    detailTbl.segmentCode as segmentCode,
                                    'cr' as amountType,
                                    null as subLedgerType,
                                    null as subLedgerDesc,
                                    coa.GLAutoID AS GLAutoID, 
                                    coa.systemAccountCode AS SystemGLCode, 
                                    coa.GLSecondaryCode AS GLSecondaryCode, 
                                    coa.GLDescription AS GLDescription,
                                    detailTbl.segmentCode AS segmentCode,
                                    coa.subCategory AS GLType,
                                    ABS(SUM(   detailTbl.totalValue    ) ) * -1  AS credit,
                                    0 AS debit,
                                    ABS(SUM(   detailTbl.totalValue    ) ) * -1  AS transactionAmount,
                                    masterTbl.transactionCurrencyDecimalPlaces as transactionDecimal,
                                    masterTbl.approvedYN as approvedYN,
                                detailTbl.salesReturnAutoID as auto_id
                                FROM srp_erp_salesreturnmaster masterTbl
                                LEFT JOIN srp_erp_salesreturndetails detailTbl ON masterTbl.salesReturnAutoID = detailTbl.salesReturnAutoID
                                JOIN (
                                      SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory, companyID 
                                      FROM srp_erp_chartofaccounts
                                      WHERE GLAutoID = (
                                          SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'UBI' AND companyID = {$companyID}
                                      ) AND companyID={$companyID} 
                                )  AS coa ON coa.companyID = masterTbl.companyID
                                WHERE detailTbl.salesReturnAutoID = '{$salesReturnAutoID}' AND detailTbl.invoiceAutoID IS NULL ")->result_array();


        $GLEntries = array_merge($GLEntry_cost, $GLEntry_inventory, $GLEntry_revenue, $GLEntry_receivable/*, $GLEntry_UBI*/);

        /*setup GL entries */
        $data['GLEntries'] = $GLEntries;

        /*setup master data */
        $data['name'] = 'Sales Return ';
        $data['code'] = 'SLR';
        $data['date'] = $master['returnDate'];
        $data['approved_yn'] = $master['approvedYN'];
        $data['currency'] = $master['transactionCurrency'];
        $data['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $data['primary_Code'] = $master['salesReturnCode'];
        $data['finance_year'] = $master['companyFinanceYear'];
        $data['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $data['approvedYN'] = $master['approvedYN'];

        $data['master_data'] = $master;
        $data['customername'] = $master['customerNamemaster'];

        return $data;


        /*

        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode ,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate, supplierCurrencyDecimalPlaces,supplierCurrencyID');
        $this->db->where('invoiceAutoID', $salesReturnAutoID);
        $tax_detail = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();


        $cr_p_arr = array();
        $cr_m_arr = array();
        $e_cr_p_arr = array();
        $e_cr_m_arr = array();
        $item_arr = array();

        $item_tax_arr = array();

        for ($i = 0; $i < count($detail); $i++) {

            $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
            $data_arr['gl_auto_id'] = $detail[$i]['revenueGLAutoID'];
            $data_arr['gl_code'] = $detail[$i]['revenueSystemGLCode'];
            $data_arr['secondary'] = $detail[$i]['revenueGLCode'];
            $data_arr['gl_desc'] = $detail[$i]['revenueGLDescription'];
            $data_arr['gl_type'] = $detail[$i]['revenueGLType'];
            $data_arr['segment_id'] = $detail[$i]['segmentID'];
            $data_arr['segment'] = $detail[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            $data_arr['partyCurrencyAmount'] = null;
            $data_arr['amount_type'] = 'dr';
            $amount = ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
            if ($amount <= 0) {
                $data_arr['gl_dr'] = $amount;
                $data_arr['gl_cr'] = 0;
                array_push($cr_p_arr, $data_arr);
            } else {
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $amount;
                $data_arr['amount_type'] = 'cr';
                array_push($cr_m_arr, $data_arr);
            }

            $cr_total += $data_arr['gl_cr'];

            if ($detail[$i]['taxAmount'] != 0) {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['taxSupplierliabilityAutoID'];
                $data_arr['gl_code'] = $detail[$i]['taxSupplierliabilitySystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['taxSupplierliabilityGLAccount'];
                $data_arr['gl_desc'] = $detail[$i]['taxSupplierliabilityDescription'];
                $data_arr['gl_type'] = $detail[$i]['taxSupplierliabilityType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 2;
                $data_arr['subLedgerDesc'] = 'AP';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'SUP';
                $data_arr['partyAutoID'] = $detail[$i]['taxSupplierAutoID'];
                $data_arr['partySystemCode'] = $detail[$i]['taxSupplierSystemCode'];
                $data_arr['partyName'] = $detail[$i]['taxSupplierName'];
                $data_arr['partyCurrencyID'] = $detail[$i]['taxSupplierCurrencyID'];
                $data_arr['partyCurrency'] = $detail[$i]['taxSupplierCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $detail[$i]['taxSupplierCurrencyExchangeRate'];
                $data_arr['partyCurrencyAmount'] = $detail[$i]['taxSupplierliabilityType'];
                $data_arr['partyCurrencyDecimalPlaces'] = $detail[$i]['taxSupplierCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = ($detail[$i]['taxAmount'] * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
                $cr_total += $data_arr['gl_cr'];
            }

            if ($detail[$i]['type'] == 'Item' && $detail[$i]['itemCategory'] == 'Inventory') {
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['expenseGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['expenseSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['expenseGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['expenseGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['expenseGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                $data_arr['gl_dr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                $data_arr['gl_cr'] = 0;
                array_push($item_arr, $data_arr);
                $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['assetGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['assetSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['assetGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['assetGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['assetGLType'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
            }
            $tax_total += ($detail[$i]['transactionAmount'] - $detail[$i]['totalAfterTax']);
        }


        for ($i = 0; $i < count($tax_detail); $i++) {
            $data_arr['auto_id'] = $tax_detail[$i]['taxDetailAutoID'];
            $data_arr['gl_auto_id'] = $tax_detail[$i]['GLAutoID'];
            $data_arr['gl_code'] = $tax_detail[$i]['SystemGLCode'];
            $data_arr['secondary'] = $tax_detail[$i]['GLCode'];
            $data_arr['gl_desc'] = $tax_detail[$i]['GLDescription'] . ' - Tax All';
            $data_arr['gl_type'] = $tax_detail[$i]['GLType'];
            $data_arr['segment_id'] = $tax_detail[$i]['segmentID'];
            $data_arr['segment'] = $tax_detail[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 2;
            $data_arr['subLedgerDesc'] = 'AP';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'SUP';
            $data_arr['partyAutoID'] = $tax_detail[$i]['supplierAutoID'];
            $data_arr['partySystemCode'] = $tax_detail[$i]['supplierSystemCode'];
            $data_arr['partyName'] = $tax_detail[$i]['supplierName'];
            $data_arr['partyCurrencyID'] = $tax_detail[$i]['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $tax_detail[$i]['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $tax_detail[$i]['supplierCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $tax_detail[$i]['supplierCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = (($tax_detail[$i]['taxPercentage'] / 100) * $tax_total);
            array_push($gl_array['gl_detail'], $data_arr);
            $cr_total += $data_arr['gl_cr'];
        }

        $cr_p_arr = $this->array_group_sum($cr_p_arr);
        $cr_m_arr = $this->array_group_sum($cr_m_arr);
        $item_arr = $this->array_group_sum($item_arr);


        foreach ($cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }


        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $master['customerReceivableAutoID'];
        $data_arr['gl_code'] = $master['customerReceivableSystemGLCode'];
        $data_arr['secondary'] = $master['customerReceivableGLAccount'];
        $data_arr['gl_desc'] = $master['customerReceivableDescription'];
        $data_arr['gl_type'] = $master['customerReceivableType'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];
        $data_arr['gl_dr'] = $cr_total;
        $data_arr['gl_cr'] = 0;
        $data_arr['amount_type'] = 'dr';
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 3;
        $data_arr['subLedgerDesc'] = 'AR';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = 'CUS';
        $data_arr['partyAutoID'] = $master['customerID'];
        $data_arr['partySystemCode'] = $master['customerSystemCode'];
        $data_arr['partyName'] = $master['customerName'];
        $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
        $data_arr['partyCurrency'] = $master['customerCurrency'];
        $data_arr['transactionExchangeRate'] = null;
        $data_arr['companyLocalExchangeRate'] = null;
        $data_arr['companyReportingExchangeRate'] = null;
        $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
        $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
        array_push($gl_array['gl_detail'], $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'SLR';
        $gl_array['name'] = 'Customer Invoice';
        $gl_array['primary_Code'] = $master['invoiceCode'];
        $gl_array['date'] = $master['invoiceDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;

        return $gl_array;*/
    }

    /*Receipt Voucher SUOM Double Entry */
    function fetch_double_entry_receipt_voucher_suom_data($receiptVoucherAutoId, $code = null)
    {
        $gl_array = array();
        $inv_total = 0;
        $dr_total = 0;
        $party_total = 0;
        $companyLocal_total = 0;
        $companyReporting_total = 0;
        $tax_total = 0;
        $cn_total = 0;
        $cn_party_total = 0;
        $cn_companyLocal_total = 0;
        $cn_companyReporting_total = 0;
        $gl_array['gl_detail'] = array();
        $gl_array['gl_bank'] = array();
        $this->db->select('srp_erp_customerreceiptmaster.*,srp_erp_customermaster.vatIdNo as vatIdNo,srp_erp_customermaster.customerName as customerNamemaster');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerreceiptmaster.customerID', 'left');
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

        $this->db->select('receiptVoucherDetailAutoID,GLAutoID,SystemGLCode,GLCode,GLDescription,GLType,transactionAmount ,segmentCode, segmentID,expenseGLAutoID,expenseSystemGLCode,expenseGLCode,expenseGLDescription,expenseGLType,assetGLAutoID,assetSystemGLCode,assetGLCode,assetGLDescription,assetGLType,transactionAmount,segmentCode ,segmentID,type,companyLocalWacAmount,requestedQty,itemCategory,companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,companyLocalAmount,companyReportingAmount,customerAmount,projectID,projectExchangeRate');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $detail = $this->db->get('srp_erp_customerreceiptdetail')->result_array();

        $this->db->select('taxDetailAutoID,GLAutoID, SystemGLCode,GLCode,GLDescription,GLType,taxPercentage,segmentCode ,segmentID, supplierAutoID,supplierSystemCode,supplierName,supplierCurrency,supplierCurrencyExchangeRate, supplierCurrencyDecimalPlaces,supplierCurrencyID,transactionExchangeRate,companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,taxMasterAutoID');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $tax_detail = $this->db->get('srp_erp_customerreceipttaxdetails')->result_array();

        $this->db->select('receiptPaymentID,bankGLAutoID,amount,chequeNo,chequeDate, bank.systemAccountCode, bank.GLSecondaryCode, bank.bankName, bank.subCategory, memo, bank.bankCurrencyID, bank.bankCurrencyCode, bank.bankCurrencyDecimalPlaces');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->join('srp_erp_chartofaccounts bank', 'payment.bankGLAutoID = bank.GLAutoID', 'left');
        $bank_detail = $this->db->get('srp_erp_customerreceiptpayments payment')->result_array();


        $m_arr = array();
        $p_arr = array();
        $i_m_arr = array();
        $i_p_arr = array();
        $item_arr = array();
        $e_p_arr = array();
        $e_m_arr = array();
        $creditNoteAmount = 0;
        for ($i = 0; $i < count($detail); $i++) {
            if ($detail[$i]['type'] == 'Invoice') {
                $inv_total += $detail[$i]['transactionAmount'];
                $party_total += $detail[$i]['customerAmount'];
                $companyLocal_total += $detail[$i]['companyLocalAmount'];
                $companyReporting_total += $detail[$i]['companyReportingAmount'];
            }
            if ($detail[$i]['type'] == 'creditnote') {
                $cn_total += $detail[$i]['transactionAmount'];
                $cn_party_total += $detail[$i]['customerAmount'];
                $cn_companyLocal_total += $detail[$i]['companyLocalAmount'];
                $cn_companyReporting_total += $detail[$i]['companyReportingAmount'];
            }
            $dr_total += $detail[$i]['transactionAmount'];
        }

        for ($i = 0; $i < count($detail); $i++) {
            if ($detail[$i]['type'] == 'Item' && $detail[$i]['itemCategory'] == 'Inventory') {
                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'];
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                if ($detail[$i]['transactionAmount'] <= 0) {
                    $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $data_arr['gl_cr'] = 0;
                    array_push($p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $detail[$i]['transactionAmount'];
                    $data_arr['amount_type'] = 'cr';
                    array_push($m_arr, $data_arr);
                }

                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['expenseGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['expenseSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['expenseGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['expenseGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['expenseGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                $data_arr['gl_dr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                $data_arr['gl_cr'] = 0;
                array_push($item_arr, $data_arr);
                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['assetGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['assetSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['assetGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['assetGLDescription'];
                $data_arr['gl_type'] = $detail[$i]['assetGLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = (($detail[$i]['companyLocalWacAmount'] / (1 / $master['companyLocalExchangeRate'])) * $detail[$i]['requestedQty']);
                array_push($item_arr, $data_arr);
                $tax_total += $detail[$i]['transactionAmount'];
            } elseif ($detail[$i]['type'] == 'Advance') {
                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Advance';
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 3;
                $data_arr['subLedgerDesc'] = 'AR';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                // $data_arr['transactionExchangeRate'] = ($dr_total/$dr_total);
                // $data_arr['partyExchangeRate'] = ($dr_total/$party_total);
                // $data_arr['companyLocalExchangeRate'] = ($dr_total/$companyLocal_total);
                // $data_arr['companyReportingExchangeRate'] = ($dr_total/$companyReporting_total);
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                if ($detail[$i]['transactionAmount'] <= 0) {
                    $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $data_arr['gl_cr'] = 0;
                    array_push($i_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $detail[$i]['transactionAmount'];
                    $data_arr['amount_type'] = 'cr';
                    array_push($i_m_arr, $data_arr);
                }
            } elseif ($detail[$i]['type'] == 'Invoice') {
                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Invoice';;
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 3;
                $data_arr['subLedgerDesc'] = 'AR';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = ($inv_total / $inv_total);
                $data_arr['partyExchangeRate'] = ($inv_total / $party_total);
                $data_arr['companyLocalExchangeRate'] = ($inv_total / $companyLocal_total);
                $data_arr['companyReportingExchangeRate'] = ($inv_total / $companyReporting_total);
                // $data_arr['transactionExchangeRate'] = 1;
                // $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                // $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                // $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                if ($detail[$i]['transactionAmount'] <= 0) {
                    $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $data_arr['gl_cr'] = 0;
                    array_push($p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $detail[$i]['transactionAmount'];
                    $data_arr['amount_type'] = 'cr';
                    array_push($m_arr, $data_arr);
                }
            } elseif ($detail[$i]['type'] == 'creditnote') {
                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'] . ' - Credit Note';;
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 3;
                $data_arr['subLedgerDesc'] = 'AR';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['partyExchangeRate'] = ($cn_total / $cn_party_total);
                $data_arr['companyLocalExchangeRate'] = ($cn_total / $cn_companyLocal_total);
                $data_arr['companyReportingExchangeRate'] = ($cn_total / $cn_companyReporting_total);
                // $data_arr['transactionExchangeRate'] = 1;
                // $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                // $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                // $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                if (($detail[$i]['transactionAmount'] * -1) <= 0) {
                    $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $data_arr['gl_cr'] = 0;
                    array_push($e_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $detail[$i]['transactionAmount'];
                    $data_arr['amount_type'] = 'cr';
                    array_push($e_m_arr, $data_arr);
                }
                $creditNoteAmount += $detail[$i]['transactionAmount'];
            } else {
                $data_arr['auto_id'] = $detail[$i]['receiptVoucherDetailAutoID'];
                $data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['SystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['GLCode'];
                $data_arr['gl_desc'] = $detail[$i]['GLDescription'];
                $data_arr['gl_type'] = $detail[$i]['GLType'];
                $data_arr['segment_id'] = $detail[$i]['segmentID'];
                $data_arr['segment'] = $detail[$i]['segmentCode'];
                $data_arr['projectID'] = $detail[$i]['projectID'];
                $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
                $data_arr['isAddon'] = 0;
                $data_arr['taxMasterAutoID'] = null;
                $data_arr['partyVatIdNo'] = null;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'CUS';
                $data_arr['partyAutoID'] = $master['customerID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr['partyCurrency'] = $master['customerCurrency'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr['amount_type'] = 'dr';
                if ($detail[$i]['transactionAmount'] <= 0) {
                    $data_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                    $data_arr['gl_cr'] = 0;
                    array_push($i_p_arr, $data_arr);
                } else {
                    $data_arr['gl_dr'] = 0;
                    $data_arr['gl_cr'] = $detail[$i]['transactionAmount'];
                    $data_arr['amount_type'] = 'cr';
                    array_push($i_m_arr, $data_arr);
                }
                $tax_total += $detail[$i]['transactionAmount'];
            }
        }

        $p_arr = $this->array_group_sum_tax($p_arr);
        $m_arr = $this->array_group_sum_tax($m_arr);
        $item_arr = $this->array_group_sum_tax($item_arr);

        $gl_array['gl_detail'] = $p_arr;
        foreach ($m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($i_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($i_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        for ($i = 0; $i < count($tax_detail); $i++) {
            $data_arr['auto_id'] = $tax_detail[$i]['taxDetailAutoID'];
            $data_arr['gl_auto_id'] = $tax_detail[$i]['GLAutoID'];
            $data_arr['gl_code'] = $tax_detail[$i]['SystemGLCode'];
            $data_arr['secondary'] = $tax_detail[$i]['GLCode'];
            $data_arr['gl_desc'] = $tax_detail[$i]['GLDescription'] . ' - Tax All';
            $data_arr['gl_type'] = $tax_detail[$i]['GLType'];
            $data_arr['segment_id'] = $tax_detail[$i]['segmentID'];
            $data_arr['segment'] = $tax_detail[$i]['segmentCode'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = $tax_detail[$i]['taxMasterAutoID'];
            $data_arr['partyVatIdNo'] = $master['vatIdNo'];
            $data_arr['subLedgerType'] = null;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'AUT';
            $data_arr['partyAutoID'] = $tax_detail[$i]['supplierAutoID'];
            $data_arr['partySystemCode'] = $tax_detail[$i]['supplierSystemCode'];
            $data_arr['partyName'] = $tax_detail[$i]['supplierName'];
            $data_arr['partyCurrencyID'] = $tax_detail[$i]['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $tax_detail[$i]['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = $tax_detail[$i]['transactionExchangeRate'];
            $data_arr['companyLocalExchangeRate'] = $tax_detail[$i]['companyLocalExchangeRate'];
            $data_arr['companyReportingExchangeRate'] = $tax_detail[$i]['companyReportingExchangeRate'];
            $data_arr['partyExchangeRate'] = $tax_detail[$i]['supplierCurrencyExchangeRate'];
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $tax_detail[$i]['supplierCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'cr';
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = (($tax_detail[$i]['taxPercentage'] / 100) * $tax_total);
            array_push($gl_array['gl_detail'], $data_arr);
            $dr_total += $data_arr['gl_cr'];
        }

        for ($i = 0; $i < count($bank_detail); $i++) {
            $data_arr['auto_id'] = $bank_detail[$i]['receiptPaymentID'];
            $data_arr['gl_auto_id'] = $bank_detail[$i]['bankGLAutoID'];
            $data_arr['gl_code'] = $bank_detail[$i]['systemAccountCode'];
            $data_arr['secondary'] = $bank_detail[$i]['GLSecondaryCode'];
            $data_arr['gl_desc'] = $bank_detail[$i]['bankName'];
            $data_arr['gl_type'] = $bank_detail[$i]['subCategory'];

            $data_arr['chequeNo'] = $bank_detail[$i]['chequeNo'];
            $data_arr['chequeDate'] = $bank_detail[$i]['chequeDate'];
            $data_arr['memo'] = $bank_detail[$i]['memo'];
            $data_arr['bankCurrencyID'] = $bank_detail[$i]['bankCurrencyID'];
            $data_arr['bankCurrencyCode'] = $bank_detail[$i]['bankCurrencyCode'];
            $data_arr['bankCurrencyDecimalPlaces'] = $bank_detail[$i]['bankCurrencyDecimalPlaces'];

            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = $bank_detail[$i]['amount'];
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = 1;
            $data_arr['partyExchangeRate'] = $master['customerExchangeRate'];
            $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            // $data_arr['transactionExchangeRate'] = ($dr_total/$dr_total);
            // $data_arr['partyExchangeRate'] = ($dr_total/$party_total);
            // $data_arr['companyLocalExchangeRate'] = ($dr_total/$companyLocal_total);
            // $data_arr['companyReportingExchangeRate'] = ($dr_total/$companyReporting_total);
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            array_push($gl_array['gl_detail'], $data_arr);
            array_push($gl_array['gl_bank'], $data_arr);
        }

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'RV';
        $gl_array['name'] = 'Receipt Voucher';
        $gl_array['primary_Code'] = $master['RVcode'];
        $gl_array['approved_YN'] = $master['approvedYN'];
        $gl_array['date'] = $master['RVdate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['customername'] = $master['customerNamemaster'];
        $gl_array['master_data'] = $master;

        if (!empty($master['customerNamemaster'])) {
            $gl_array['customername'] = $master['customerNamemaster'];
        } else {
            $gl_array['customername'] = $master['customerName'];
        }

        return $gl_array;
    }


    function fetch_double_entry_customer_invoice_data_opr($invoiceAutoID, $code = null)
    {
        $gl_array = array();
        $tax_total = 0;
        $cr_total = 0;
        $gl_array['gl_detail'] = array();
        $this->db->select('srp_erp_customerinvoicemaster.*,srp_erp_customermaster.vatIdNo as vatIdNo,srp_erp_customermaster.customerName as customerNamemaster');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID', 'left');
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where("`type` <> 'DO'");
        $detail = $this->db->get('srp_erp_customerinvoicedetails')->result_array();


        $cr_p_arr = array();
        $cr_m_arr = array();
        $e_cr_p_arr = array();
        $e_cr_m_arr = array();
        $item_arr = array();
        $item_tax_arr = array();
        for ($i = 0; $i < count($detail); $i++) {
            $data_arr['auto_id'] = $detail[$i]['invoiceDetailsAutoID'];

            $data_arr['gl_auto_id'] = $master['customerReceivableAutoID'];
            $data_arr['gl_code'] = $master['customerReceivableSystemGLCode'];
            $data_arr['secondary'] = $master['customerReceivableGLAccount'];
            $data_arr['gl_desc'] = $master['customerReceivableDescription'];
            $data_arr['gl_type'] = $master['customerReceivableType'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['projectID'] = $detail[$i]['projectID'];
            $data_arr['projectExchangeRate'] = $detail[$i]['projectExchangeRate'];
            $data_arr['isAddon'] = 0;
            $data_arr['taxMasterAutoID'] = null;
            $data_arr['partyVatIdNo'] = null;
            $data_arr['subLedgerType'] = 3;
            $data_arr['subLedgerDesc'] = 'AR';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'CUS';
            $data_arr['partyAutoID'] = $master['customerID'];
            $data_arr['partySystemCode'] = $master['customerSystemCode'];
            $data_arr['partyName'] = $master['customerName'];
            $data_arr['partyCurrencyID'] = $master['customerCurrencyID'];
            $data_arr['partyCurrency'] = $master['customerCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data_arr['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            $data_arr['amount_type'] = 'dr';
            $amount = $detail[$i]['transactionAmount'];

            $data_arr['gl_dr'] = $amount;
            $data_arr['gl_cr'] = 0;
            array_push($e_cr_p_arr, $data_arr);

            /*if ($amount <= 0) {
                $data_arr['gl_dr'] = $amount;
                $data_arr['gl_cr'] = 0;
                array_push($e_cr_p_arr, $data_arr);
            } else {
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $amount;
                $data_arr['amount_type'] = 'cr';
                array_push($e_cr_m_arr, $data_arr);
            }*/
            $cr_total += $data_arr['gl_cr'];

        }


        $cr_p_arr = $this->array_group_sum_tax($cr_p_arr);
        $cr_m_arr = $this->array_group_sum_tax($cr_m_arr);
        $item_arr = $this->array_group_sum_tax($item_arr);
        foreach ($cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($item_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_cr_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        foreach ($item_tax_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }


        if ($master['retentionPercentage'] > 0) {
            $retamnt = $cr_total * ($master['retentionPercentage'] / 100);
            $cr_total = $cr_total - $retamnt;
        }


        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['approved_yn'] = $master['approvedYN'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'CINV';
        $gl_array['name'] = 'Customer Invoice';
        $gl_array['primary_Code'] = $master['invoiceCode'];
        $gl_array['date'] = $master['invoiceDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['customername'] = $master['customerNamemaster'];
        return $gl_array;
    }

    public function array_group_sum_tax_pm($data)
    {


        $groups = array();
        foreach ($data as $item) {
            $key = $item['gl_auto_id'] . $item['segment_id'] . $item['gl_desc'] . $item['projectID'] . $item['project_categoryID'] . $item['project_subCategoryID'];
            if (!array_key_exists($key, $groups)) {
                $groups[$key] = array(
                    'auto_id' => $item['auto_id'],
                    'gl_auto_id' => $item['gl_auto_id'],
                    'gl_code' => $item['gl_code'],
                    'secondary' => $item['secondary'],
                    'gl_desc' => $item['gl_desc'],
                    'gl_type' => $item['gl_type'],
                    'segment' => $item['segment'],
                    'segment_id' => $item['segment_id'],
                    'gl_dr' => $item['gl_dr'],
                    'gl_cr' => $item['gl_cr'],
                    'amount_type' => $item['amount_type'],
                    'isAddon' => $item['isAddon'],
                    'taxMasterAutoID' => $item['taxMasterAutoID'],
                    'partyVatIdNo' => $item['partyVatIdNo'],
                    'subLedgerType' => $item['subLedgerType'],
                    'subLedgerDesc' => $item['subLedgerDesc'],
                    'partyContractID' => $item['partyContractID'],
                    'partyType' => $item['partyType'],
                    'partyAutoID' => $item['partyAutoID'],
                    'partySystemCode' => $item['partySystemCode'],
                    'partyName' => $item['partyName'],
                    'partyCurrencyID' => $item['partyCurrencyID'],
                    'partyCurrency' => $item['partyCurrency'],
                    'transactionExchangeRate' => $item['transactionExchangeRate'],
                    'companyLocalExchangeRate' => $item['companyLocalExchangeRate'],
                    'companyReportingExchangeRate' => $item['companyReportingExchangeRate'],
                    'partyExchangeRate' => $item['partyExchangeRate'],
                    'partyCurrencyAmount' => $item['partyCurrencyAmount'],
                    'partyCurrencyDecimalPlaces' => $item['partyCurrencyDecimalPlaces'],
                    'projectID' => $item['projectID'],
                    'project_categoryID' => $item['project_categoryID'],
                    'project_subCategoryID' => $item['project_subCategoryID'],
                    'projectExchangeRate' => $item['projectExchangeRate'],

                );
            } else {
                $groups[$key]['gl_dr'] += $item['gl_dr'];
                $groups[$key]['gl_cr'] += $item['gl_cr'];
            }
        }
        $groups = array_values($groups);


        return $groups;
    }

    public function array_group_sum_pm($data)
    {
        $groups = array();
        $key = 0;
        foreach ($data as $item) {
            $key = $item['gl_auto_id'] . $item['segment_id'] . $item['projectID'] . $item['project_categoryID'] . $item['project_subCategoryID'];
            if (!array_key_exists($key, $groups)) {
                $groups[$key] = array(
                    'auto_id' => $item['auto_id'],
                    'gl_auto_id' => $item['gl_auto_id'],
                    'gl_code' => $item['gl_code'],
                    'secondary' => $item['secondary'],
                    'gl_desc' => $item['gl_desc'],
                    'gl_type' => $item['gl_type'],
                    'segment' => $item['segment'],
                    'segment_id' => $item['segment_id'],
                    'gl_dr' => $item['gl_dr'],
                    'gl_cr' => $item['gl_cr'],
                    'amount_type' => $item['amount_type'],
                    'isAddon' => $item['isAddon'],
                    'subLedgerType' => $item['subLedgerType'],
                    'subLedgerDesc' => $item['subLedgerDesc'],
                    'partyContractID' => $item['partyContractID'],
                    'partyType' => $item['partyType'],
                    'partyAutoID' => $item['partyAutoID'],
                    'partySystemCode' => $item['partySystemCode'],
                    'partyName' => $item['partyName'],
                    'partyCurrencyID' => $item['partyCurrencyID'],
                    'partyCurrency' => $item['partyCurrency'],
                    'transactionExchangeRate' => $item['transactionExchangeRate'],
                    'companyLocalExchangeRate' => $item['companyLocalExchangeRate'],
                    'companyReportingExchangeRate' => $item['companyReportingExchangeRate'],
                    'partyExchangeRate' => $item['partyExchangeRate'],
                    'partyCurrencyAmount' => $item['partyCurrencyAmount'],
                    'partyCurrencyDecimalPlaces' => $item['partyCurrencyDecimalPlaces'],
                    'projectID' => $item['projectID'],
                    'project_categoryID' => $item['project_categoryID'],
                    'project_subCategoryID' => $item['project_subCategoryID'],
                    'projectExchangeRate' => $item['projectExchangeRate'],
                );
            } else {
                $groups[$key]['gl_dr'] = $groups[$key]['gl_dr'] + $item['gl_dr'];
                $groups[$key]['gl_cr'] = $groups[$key]['gl_cr'] + $item['gl_cr'];
            }
            $key++;
        }
        $groups = array_values($groups);
        return $groups;
    }

    function fetch_double_entry_bulk_transfer_data($stockTransferAutoID, $code = null)
    {
        $gl_array = array();
        $cost_arr = array();
        $assat_arr = array();
        $gl_array['gl_detail'] = array();
        $this->db->select('*');
        $this->db->where('stockTransferAutoID', $stockTransferAutoID);
        $master = $this->db->get('srp_erp_stocktransfermaster_bulk')->row_array();

        $this->db->select('stockTransferDetailsID,financeCategory,PLSystemGLCode,PLGLCode,PLDescription,BLGLAutoID,PLGLAutoID,BLSystemGLCode ,PLType ,BLGLCode,BLDescription,BLType,totalValue,segmentCode,segmentID,projectID,fromWarehouseType,toWarehouseType,fromWarehouseWIPGLAutoID,toWarehouseWIPGLAutoID,project_categoryID,project_subCategoryID');
        $this->db->where('itemCategory !=', 'Non Inventory');
        $this->db->where('stockTransferAutoID', $stockTransferAutoID);
        $this->db->where('transfer_QTY > 0');
        $detail = $this->db->get('srp_erp_stocktransferdetails_bulk')->result_array();

        for ($i = 0; $i < count($detail); $i++) {
            $assa_data_arr['auto_id'] = $detail[$i]['stockTransferDetailsID'];
            if ($detail[$i]['fromWarehouseType'] == 2) {
                $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory');
                $this->db->where('GLAutoID', $detail[$i]['fromWarehouseWIPGLAutoID']);
                $frmGlDetail = $this->db->get('srp_erp_chartofaccounts')->row_array();

                $assa_data_arr['gl_auto_id'] = $detail[$i]['fromWarehouseWIPGLAutoID'];
                $assa_data_arr['gl_code'] = $frmGlDetail['systemAccountCode'];
                $assa_data_arr['secondary'] = $frmGlDetail['GLSecondaryCode'];
                $assa_data_arr['gl_desc'] = $frmGlDetail['GLDescription'];
                $assa_data_arr['gl_type'] = $frmGlDetail['subCategory'];
            } else {
                $assa_data_arr['gl_auto_id'] = $detail[$i]['BLGLAutoID'];
                $assa_data_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
                $assa_data_arr['secondary'] = $detail[$i]['BLGLCode'];
                $assa_data_arr['gl_desc'] = $detail[$i]['BLDescription'];
                $assa_data_arr['gl_type'] = $detail[$i]['BLType'];
            }

            $assa_data_arr['segment_id'] = $detail[$i]['segmentID'];
            $assa_data_arr['segment'] = $detail[$i]['segmentCode'];
            $assa_data_arr['projectID'] = $detail[$i]['projectID'];
            $assa_data_arr['project_categoryID'] = $detail[$i]['project_categoryID'];
            $assa_data_arr['project_subCategoryID'] = $detail[$i]['project_subCategoryID'];
            $assa_data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $assa_data_arr['gl_dr'] = 0;
            $assa_data_arr['gl_cr'] = $detail[$i]['totalValue'];
            $assa_data_arr['amount_type'] = 'cr';
            $assa_data_arr['isAddon'] = 0;
            $assa_data_arr['subLedgerType'] = 0;
            $assa_data_arr['subLedgerDesc'] = null;
            $assa_data_arr['partyContractID'] = null;
            $assa_data_arr['partyType'] = null;
            $assa_data_arr['partyAutoID'] = null;
            $assa_data_arr['partySystemCode'] = null;
            $assa_data_arr['partyName'] = null;
            $assa_data_arr['partyCurrencyID'] = null;
            $assa_data_arr['partyCurrency'] = null;
            $assa_data_arr['transactionExchangeRate'] = null;
            $assa_data_arr['companyLocalExchangeRate'] = null;
            $assa_data_arr['companyReportingExchangeRate'] = null;
            $assa_data_arr['partyExchangeRate'] = null;
            $assa_data_arr['partyCurrencyAmount'] = null;
            $assa_data_arr['partyCurrencyDecimalPlaces'] = null;
            array_push($assat_arr, $assa_data_arr);

            $data_arr['auto_id'] = $detail[$i]['stockTransferDetailsID'];
            if ($master['receiptType'] == 1) {
                $data_arr['gl_auto_id'] = $detail[$i]['PLGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['PLSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['PLGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['PLDescription'];
                $data_arr['gl_type'] = $detail[$i]['PLType'];
            } else {
                if ($detail[$i]['toWarehouseType'] == 2) {
                    $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory');
                    $this->db->where('GLAutoID', $detail[$i]['toWarehouseWIPGLAutoID']);
                    $toGlDetail = $this->db->get('srp_erp_chartofaccounts')->row_array();

                    $data_arr['gl_auto_id'] = $detail[$i]['toWarehouseWIPGLAutoID'];
                    $data_arr['gl_code'] = $toGlDetail['systemAccountCode'];
                    $data_arr['secondary'] = $toGlDetail['GLSecondaryCode'];
                    $data_arr['gl_desc'] = $toGlDetail['GLDescription'];
                    $data_arr['gl_type'] = $toGlDetail['subCategory'];
                } else {
                    $data_arr['gl_auto_id'] = $detail[$i]['BLGLAutoID'];
                    $data_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
                    $data_arr['secondary'] = $detail[$i]['BLGLCode'];
                    $data_arr['gl_desc'] = $detail[$i]['BLDescription'];
                    $data_arr['gl_type'] = $detail[$i]['BLType'];
                }
            }

            $data_arr['segment_id'] = $detail[$i]['segmentID'];
            $data_arr['segment'] = $detail[$i]['segmentCode'];
            $data_arr['gl_dr'] = $detail[$i]['totalValue'];
            $data_arr['projectID'] = null;
            $data_arr['project_categoryID'] = null;
            $data_arr['project_subCategoryID'] = null;
            $data_arr['projectExchangeRate'] = null;
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = null;
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = null;
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = null;
            $data_arr['partyCurrencyAmount'] = null;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            array_push($cost_arr, $data_arr);
        }

        $assat_arr = $this->array_group_sum_pm($assat_arr);
        $cost_arr = $this->array_group_sum_pm($cost_arr);

        $gl_array['gl_detail'] = $assat_arr;
        foreach ($cost_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        $gl_array['currency'] = $master['companyLocalCurrency'];
        $gl_array['decimal_places'] = $master['companyLocalCurrencyDecimalPlaces'];
        $gl_array['code'] = 'STB';
        $gl_array['name'] = 'Bulk Transfer';
        $gl_array['primary_Code'] = $master['stockTransferCode'];
        $gl_array['date'] = $master['tranferDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }


    function fetch_double_entry_grv_data_groupbased($grvAutoID, $code = null)
    {
        $gl_array = array();
        $cr_total = 0;
        $grv_total = 0;
        $addon_total = 0;
        $addon_tax = 0;
        $addon_tax_total = 0;
        $addon_tax_impact = 0;
        $addon_item = array();
        $m_arr = array();
        $expanss_item = array();
        $gl_array['gl_detail'] = array();

        $companyID = current_companyID();
        $UGRV_ID = $this->db->query("SELECT srp_erp_chartofaccounts.GLAutoID 
                    FROM srp_erp_chartofaccounts
                    JOIN srp_erp_companycontrolaccounts ON srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
                    WHERE controllAccountYN = 1 AND srp_erp_companycontrolaccounts.companyID = {$companyID} AND srp_erp_chartofaccounts.companyID = {$companyID} AND controlAccountType = 'UGRV'")->row_array();
        $UGRV = fetch_gl_account_desc($UGRV_ID['GLAutoID']);
//        $UGRV_ID = $this->common_data['controlaccounts']['UGRV'];
//        $UGRV = fetch_gl_account_desc($UGRV_ID);
        $this->db->select('srp_erp_grvmaster.*,srp_erp_suppliermaster.supplierName as suppliernamemaster');
        $this->db->where('grvAutoID', $grvAutoID);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_grvmaster.supplierID', 'left');
        $master = $this->db->get('srp_erp_grvmaster')->row_array();


        $vatRegActive = $this->db->query("SELECT 
        sum(amount) as taxLedgerAmount,
        srp_erp_company.vatRegisterYN,
        srp_erp_taxmaster.isClaimable,
        srp_erp_taxmaster.taxCategory,
        taxGlAutoID,
        srp_erp_grvmaster.supplierID,
        documentMasterAutoID,
        srp_erp_chartofaccounts.*,
        rcmApplicableYN
        FROM 
        srp_erp_taxledger
        LEFT JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_taxledger.companyID
        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
        LEFT JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_taxledger.documentMasterAutoID
        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxledger.taxGlAutoID
        WHERE 
        srp_erp_taxledger.documentID = 'GRV'
        AND srp_erp_taxledger.isClaimable = 1
        AND documentMasterAutoID = $grvAutoID
        GROUP BY
        taxMasterID")->result_array();


        $varRegNotActive = $this->db->query("SELECT
                                                      sum(amount) as taxLedgerAmount,
                                                      srp_erp_company.vatRegisterYN,
                                                      srp_erp_taxmaster.isClaimable,
                                                      srp_erp_taxmaster.taxCategory,srp_erp_grvdetails.BLGLAutoID as taxGlAutoID,
                                                      srp_erp_chartofaccounts.*,
                                                      srp_erp_grvdetails.grvDetailsID,
                                                      financeCategory,
                                                      BLGLAutoID,
                                                      BLSystemGLCode,
                                                      BLGLCode,
                                                      BLDescription,
                                                      BLType,
                                                      PLGLAutoID,
                                                      PLSystemGLCode,
                                                      PLGLCode,
                                                      PLDescription,
                                                      PLType,
                                                      projectID,
                                                      projectExchangeRate,
                                                      project_categoryID,
                                                      project_subCategoryID,
                                                      IF(srp_erp_grvdetails.financeCategory = 1 OR srp_erp_grvdetails.financeCategory = 3,BLGLAutoID,PLGLAutoID) as grvGLAutoID,
                                                      chartOfAccountsGRV.GLAutoID as grvGLAutoID,
                                                      chartOfAccountsGRV.systemAccountCode as grvsystemAccountCode,
                                                      chartOfAccountsGRV.GLSecondaryCode as grvGLSecondaryCode,
                                                      chartOfAccountsGRV.GLDescription as grvGLDescription,
                                                      chartOfAccountsGRV.subCategory as grvsubCategory 
                                                  FROM 
                                                      srp_erp_taxledger
                                                  LEFT JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_taxledger.companyID
                                                  LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                                  LEFT JOIN srp_erp_grvdetails ON srp_erp_grvdetails.grvDetailsID = srp_erp_taxledger.documentDetailAutoID
                                                  LEFT JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_taxledger.documentMasterAutoID
                                                  LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxledger.taxGlAutoID
                                                  LEFT JOIN (SELECT 
                                                                 IF(srp_erp_grvdetails.financeCategory = 1 OR srp_erp_grvdetails.financeCategory = 3,BLGLAutoID,PLGLAutoID) as grvGLAutoID,
                                                                    grvDetailsID	
                                                                 FROM 
                                                                 srp_erp_grvdetails) grvchartofaccounts on  grvchartofaccounts.grvDetailsID = srp_erp_grvdetails.grvDetailsID
                                                  LEFT JOIN srp_erp_chartofaccounts chartOfAccountsGRV ON chartOfAccountsGRV.GLAutoID = grvchartofaccounts.grvGLAutoID 
                                                  WHERE 
                                                      srp_erp_taxledger.documentID = 'GRV'
                                                      AND srp_erp_taxledger.isClaimable = 0 
                                                      AND documentMasterAutoID =  $grvAutoID
                                                  GROUP BY
                                                      grvGLAutoID")->result_array();


        $vaRCMApplicaple = $this->db->query("SELECT 
                                                        sum(amount) as taxLedgerAmount,
                                                        srp_erp_company.vatRegisterYN,
                                                        srp_erp_taxmaster.isClaimable,
                                                        srp_erp_taxmaster.taxCategory,
                                                        taxGlAutoID,
                                                        srp_erp_grvmaster.supplierID,
                                                        documentMasterAutoID,
                                                        srp_erp_chartofaccounts.*,
                                                        rcmApplicableYN
                                                    FROM 
                                                        srp_erp_taxledger
                                                    LEFT JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_taxledger.companyID
                                                    LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                                    LEFT JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_taxledger.documentMasterAutoID
                                                    LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxledger.outputVatTransferGL
                                                    WHERE 
                                                        srp_erp_taxledger.documentID = 'GRV'
                                                        AND  srp_erp_taxmaster.taxCategory = 2 
                                                        AND documentMasterAutoID = $grvAutoID
                                                    GROUP BY
                                                        taxMasterID")->result_array();


        $grvAddonTax = $this->db->query("SELECT
	                                         sum( amount ) AS taxLedgerAmount,
                                             srp_erp_company.vatRegisterYN,
                                             srp_erp_taxmaster.isClaimable,
                                             srp_erp_taxmaster.taxCategory,
                                             taxGlAutoID,
                                             srp_erp_grvmaster.supplierID,
                                             documentMasterAutoID,
                                             srp_erp_chartofaccounts.*,
                                             rcmApplicableYN ,
                                             srp_erp_taxledger.isClaimable,
                                             srp_erp_grv_addon.isChargeToExpense,
                                             srp_erp_grv_addon.impactFor,srp_erp_grv_addon.supplierID,
                                             grvAddon.GLAutoID as grvAddonGLAutoID,
                                             grvAddon.systemAccountCode as systemAccountCodeAddon,
                                             grvAddon.GLSecondaryCode as GLSecondaryCodeAddon,
                                             grvAddon.GLDescription as GLDescriptionAddon,
                                             grvAddon.subCategory as subCategoryAddon,
                                             srp_erp_grv_addon.impactFor as impactFor
                                             FROM
	                                         srp_erp_taxledger
	                                         LEFT JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_taxledger.companyID
	                                         LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
	                                         LEFT JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_taxledger.documentMasterAutoID
	                                         LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxledger.taxGlAutoID 
	                                         LEFT JOIN srp_erp_grv_addon ON srp_erp_grv_addon.id = srp_erp_taxledger.documentDetailAutoID
                                             LEFT JOIN srp_erp_chartofaccounts grvAddon ON grvAddon.GLAutoID = srp_erp_grv_addon.GLAutoID
                                             WHERE
	                                         srp_erp_taxledger.documentID = 'GRV-ADD' 	
	                                         AND documentMasterAutoID = '{$grvAutoID}' 
                                             GROUP BY
	                                         	taxMasterID,documentDetailAutoID")->result_array();


        if(!empty($grvAddonTax)){
            for ($i = 0; $i < count($grvAddonTax); $i++) {

                $adon_data_arr = array();
                $supplier_arr = $this->fetch_supplier_data($grvAddonTax[$i]['supplierID']);
                if($grvAddonTax[$i]['isClaimable'] == 1 ){

                    $data_arr['auto_id'] = $grvAddonTax[$i]['documentMasterAutoID'];
                    $data_arr['gl_auto_id'] = $grvAddonTax[$i]['taxGlAutoID'];
                    $data_arr['gl_code'] = $grvAddonTax[$i]['systemAccountCode'];
                    $data_arr['secondary'] = $grvAddonTax[$i]['GLSecondaryCode'];
                    $data_arr['gl_desc'] = $grvAddonTax[$i]['GLDescription'];
                    $data_arr['gl_type'] = $grvAddonTax[$i]['subCategory'];

                    $data_arr['segment_id'] = $master['segmentID'];
                    $data_arr['projectID'] = null;
                    $data_arr['project_categoryID'] = null;
                    $data_arr['project_subCategoryID'] = null;
                    //$data_arr['projectID'] = $master['projectID'];
                    //$data_arr['projectExchangeRate'] = $master['projectExchangeRate'];
                    $data_arr['segment'] = $master['segmentCode'];
                    $data_arr['gl_dr'] = $grvAddonTax[$i]['taxLedgerAmount'];
                    $data_arr['gl_cr'] = 0;
                    $data_arr['amount_type'] = 'dr';
                    $data_arr['isAddon'] = 0;
                    $data_arr['subLedgerType'] = 0;
                    $data_arr['subLedgerDesc'] = null;
                    $data_arr['partyContractID'] = null;
                    $data_arr['partyType'] = 'SUP';
                    $data_arr['partyAutoID'] = $grvAddonTax[$i]['supplierID'];
                    $data_arr['partySystemCode'] = $supplier_arr['supplierSystemCode'];
                    $data_arr['partyName'] = $supplier_arr['supplierName'];
                    $data_arr['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
                    $data_arr['transactionExchangeRate'] = null;
                    $data_arr['companyLocalExchangeRate'] = null;
                    $data_arr['companyReportingExchangeRate'] = null;
                    $data_arr['partyCurrency'] = $supplier_arr['supplierCurrency'];
                    $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $data_arr['partyCurrencyID']);
                    $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
                    $data_arr['partyCurrencyDecimalPlaces'] = $conversion_arr['DecimalPlaces'];
                    $data_arr['partyCurrencyAmount'] = ($grvAddonTax[$i]['taxLedgerAmount'] / $conversion_arr['conversion']);
                    array_push($gl_array['gl_detail'], $data_arr);
                    $addon_tax += $grvAddonTax[$i]['taxLedgerAmount'];

                }else if(($grvAddonTax[$i]['isChargeToExpense'] == 0 && $grvAddonTax[$i]['isClaimable'] == 0)){
                        if($grvAddonTax[$i]['impactFor'] == 0){
                            $addon_tax_impact += $grvAddonTax[$i]['taxLedgerAmount'];
                        }else{

                            $adon_data_arr['item_id'] = $grvAddonTax[$i]['impactFor'];
                            $adon_data_arr['total'] = $grvAddonTax[$i]['taxLedgerAmount'];
                            array_push($addon_item, $adon_data_arr);
                            $addon_tax += $grvAddonTax[$i]['taxLedgerAmount'];
                        }

                }else if($grvAddonTax[$i]['isChargeToExpense'] == 1 && $grvAddonTax[$i]['isClaimable'] == 0){

                    $data_arr['auto_id'] = $grvAddonTax[$i]['documentMasterAutoID'];
                    $data_arr['gl_auto_id'] = $grvAddonTax[$i]['grvAddonGLAutoID'];
                    $data_arr['gl_code'] = $grvAddonTax[$i]['systemAccountCodeAddon'];
                    $data_arr['secondary'] = $grvAddonTax[$i]['GLSecondaryCodeAddon'];
                    $data_arr['gl_desc'] = $grvAddonTax[$i]['GLDescriptionAddon'];
                    $data_arr['gl_type'] = $grvAddonTax[$i]['subCategoryAddon'];

                    $data_arr['segment_id'] = $master['segmentID'];
                    $data_arr['projectID'] = null;
                    $data_arr['project_categoryID'] = null;
                    $data_arr['project_subCategoryID'] = null;
                    //$data_arr['projectID'] = $master['projectID'];
                    //$data_arr['projectExchangeRate'] = $master['projectExchangeRate'];
                    $data_arr['segment'] = $master['segmentCode'];
                    $data_arr['gl_dr'] = $grvAddonTax[$i]['taxLedgerAmount'];
                    $data_arr['gl_cr'] = 0;
                    $data_arr['amount_type'] = 'dr';
                    $data_arr['isAddon'] = 0;
                    $data_arr['subLedgerType'] = 0;
                    $data_arr['subLedgerDesc'] = null;
                    $data_arr['partyContractID'] = null;
                    $data_arr['partyType'] = 'SUP';
                    $data_arr['partyAutoID'] = $grvAddonTax[$i]['supplierID'];
                    $data_arr['partySystemCode'] = $supplier_arr['supplierSystemCode'];
                    $data_arr['partyName'] = $supplier_arr['supplierName'];
                    $data_arr['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
                    $data_arr['transactionExchangeRate'] = null;
                    $data_arr['companyLocalExchangeRate'] = null;
                    $data_arr['companyReportingExchangeRate'] = null;
                    $data_arr['partyCurrency'] = $supplier_arr['supplierCurrency'];
                    $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $data_arr['partyCurrencyID']);
                    $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
                    $data_arr['partyCurrencyDecimalPlaces'] = $conversion_arr['DecimalPlaces'];
                    $data_arr['partyCurrencyAmount'] = ($grvAddonTax[$i]['taxLedgerAmount'] / $conversion_arr['conversion']);
                    array_push($gl_array['gl_detail'], $data_arr);
                    $addon_tax += $grvAddonTax[$i]['taxLedgerAmount'];
                }

            }
        }



        $vatAmount = array_filter($vatRegActive, function ($a) {
            return $a['taxCategory'] == 2;
        });

        $vatAmountRCM = (array_column($vatAmount, 'taxLedgerAmount'));
        $isRCMApplicable = array_column($vaRCMApplicaple, 'rcmApplicableYN');


        if (!empty($vatRegActive)) {
            for ($i = 0; $i < count($vatRegActive); $i++) {
                $supplier_arr = $this->fetch_supplier_data($vatRegActive[$i]['supplierID']);
                $data_arr['auto_id'] = $vatRegActive[$i]['documentMasterAutoID'];
                $data_arr['gl_auto_id'] = $vatRegActive[$i]['taxGlAutoID'];
                $data_arr['gl_code'] = $vatRegActive[$i]['systemAccountCode'];
                $data_arr['secondary'] = $vatRegActive[$i]['GLSecondaryCode'];
                $data_arr['gl_desc'] = $vatRegActive[$i]['GLDescription'];
                $data_arr['gl_type'] = $vatRegActive[$i]['subCategory'];


                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['projectID'] = null;
                $data_arr['project_categoryID'] = null;
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['gl_dr'] = $vatRegActive[$i]['taxLedgerAmount'];
                $data_arr['gl_cr'] = 0;
                $data_arr['amount_type'] = 'dr';
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'SUP';
                $data_arr['partyAutoID'] = $vatRegActive[$i]['supplierID'];
                $data_arr['partySystemCode'] = $supplier_arr['supplierSystemCode'];
                $data_arr['partyName'] = $supplier_arr['supplierName'];
                $data_arr['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyCurrency'] = $supplier_arr['supplierCurrency'];
                $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $data_arr['partyCurrencyID']);
                $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
                $data_arr['partyCurrencyDecimalPlaces'] = $conversion_arr['DecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = ($vatRegActive[$i]['taxLedgerAmount'] / $conversion_arr['conversion']);
                if ($vatRegActive[$i]['rcmApplicableYN'] == 1 && $vatRegActive[$i]['taxCategory'] == 2) {
                    $cr_total += $vatRegActive[$i]['taxLedgerAmount'] - $vatAmountRCM[0];
                } else {
                    $cr_total += $vatRegActive[$i]['taxLedgerAmount'];
                }

                array_push($gl_array['gl_detail'], $data_arr);
            }
        }


        if (!empty($vaRCMApplicaple) && ($isRCMApplicable[0] == 1)) {
            for ($i = 0; $i < count($vaRCMApplicaple); $i++) {
                $supplier_arr = $this->fetch_supplier_data($vaRCMApplicaple[$i]['supplierID']);
                $data_arr['auto_id'] = $vaRCMApplicaple[$i]['documentMasterAutoID'];
                $data_arr['gl_auto_id'] = $vaRCMApplicaple[$i]['GLAutoID'];
                $data_arr['gl_code'] = $vaRCMApplicaple[$i]['systemAccountCode'];
                $data_arr['secondary'] = $vaRCMApplicaple[$i]['GLSecondaryCode'];
                $data_arr['gl_desc'] = $vaRCMApplicaple[$i]['GLDescription'];
                $data_arr['gl_type'] = $vaRCMApplicaple[$i]['subCategory'];


                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['projectID'] = null;
                $data_arr['project_categoryID'] = null;
                $data_arr['project_subCategoryID'] = null;
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $vaRCMApplicaple[$i]['taxLedgerAmount'];
                $data_arr['amount_type'] = 'cr';
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'SUP';
                $data_arr['partyAutoID'] = $vaRCMApplicaple[$i]['supplierID'];
                $data_arr['partySystemCode'] = $supplier_arr['supplierSystemCode'];
                $data_arr['partyName'] = $supplier_arr['supplierName'];
                $data_arr['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $data_arr['partyCurrency'] = $supplier_arr['supplierCurrency'];
                $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $data_arr['partyCurrencyID']);
                $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
                $data_arr['partyCurrencyDecimalPlaces'] = $conversion_arr['DecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = ($vaRCMApplicaple[$i]['taxLedgerAmount'] / $conversion_arr['conversion']);


                array_push($gl_array['gl_detail'], $data_arr);
            }
        }


        $this->db->select('grvDetailsID,itemFinanceCategory,PLGLAutoID,PLSystemGLCode,PLGLCode,PLDescription, PLType,BLGLAutoID, BLSystemGLCode ,BLGLCode,BLDescription, BLType,receivedTotalAmount,financeCategory,projectID,projectExchangeRate,project_categoryID,project_subCategoryID');
        $this->db->where('grvAutoID', $grvAutoID);
        $detail = $this->db->get('srp_erp_grvdetails')->result_array();
        for ($i = 0; $i < count($detail); $i++) {
            $grv_total += $detail[$i]['receivedTotalAmount'];
        }

        $this->db->select('id,GLAutoID,systemGLCode,GLCode,GLDescription,GLType,sum(total_amount) as total_amount,isChargeToExpense,impactFor, supplierName, supplierID,supplierSystemCode,projectID,projectExchangeRate');
        $this->db->group_by("GLAutoID");
        $this->db->group_by("supplierID");
        $this->db->where('grvAutoID', $grvAutoID);
        $addon_detail = $this->db->get('srp_erp_grv_addon')->result_array();
        if (!empty($addon_detail)) {
            for ($i = 0; $i < count($addon_detail); $i++) {
                $adon_data_arr = array();
                $supplier_arr = $this->fetch_supplier_data($addon_detail[$i]['supplierID']);
                if ($addon_detail[$i]['isChargeToExpense'] == 1) {
                    $data_arr['auto_id'] = $addon_detail[$i]['id'];
                    $data_arr['gl_auto_id'] = $addon_detail[$i]['GLAutoID'];
                    $data_arr['gl_code'] = $addon_detail[$i]['systemGLCode'];
                    $data_arr['secondary'] = $addon_detail[$i]['GLCode'];
                    $data_arr['gl_desc'] = $addon_detail[$i]['GLDescription'];
                    $data_arr['gl_type'] = $addon_detail[$i]['GLType'];
                    $data_arr['segment_id'] = $master['segmentID'];
                    $data_arr['projectID'] = null;
                    $data_arr['project_categoryID'] = null;
                    $data_arr['project_subCategoryID'] = null;
                    //$data_arr['projectID'] = $master['projectID'];
                    //$data_arr['projectExchangeRate'] = $master['projectExchangeRate'];
                    $data_arr['segment'] = $master['segmentCode'];
                    $data_arr['gl_dr'] = $addon_detail[$i]['total_amount'];
                    $data_arr['gl_cr'] = 0;
                    $data_arr['amount_type'] = 'dr';
                    $data_arr['isAddon'] = 0;
                    $data_arr['subLedgerType'] = 0;
                    $data_arr['subLedgerDesc'] = null;
                    $data_arr['partyContractID'] = null;
                    $data_arr['partyType'] = 'SUP';
                    $data_arr['partyAutoID'] = $addon_detail[$i]['supplierID'];
                    $data_arr['partySystemCode'] = $supplier_arr['supplierSystemCode'];
                    $data_arr['partyName'] = $supplier_arr['supplierName'];
                    $data_arr['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
                    $data_arr['transactionExchangeRate'] = null;
                    $data_arr['companyLocalExchangeRate'] = null;
                    $data_arr['companyReportingExchangeRate'] = null;
                    $data_arr['partyCurrency'] = $supplier_arr['supplierCurrency'];
                    $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $data_arr['partyCurrencyID']);
                    $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
                    $data_arr['partyCurrencyDecimalPlaces'] = $conversion_arr['DecimalPlaces'];
                    $data_arr['partyCurrencyAmount'] = ($addon_detail[$i]['total_amount'] / $conversion_arr['conversion']);
                    array_push($expanss_item, $data_arr);
                } elseif ($addon_detail[$i]['isChargeToExpense'] == 0 and $addon_detail[$i]['impactFor'] == 0) {
                    $addon_total += $addon_detail[$i]['total_amount'];

                } elseif ($addon_detail[$i]['isChargeToExpense'] == 0 and $addon_detail[$i]['impactFor'] != 0) {
                    $adon_data_arr['item_id'] = $addon_detail[$i]['impactFor'];
                    $adon_data_arr['total'] = $addon_detail[$i]['total_amount'];
                    array_push($addon_item, $adon_data_arr);
                }

                $data_arr['auto_id'] = 0;
                $data_arr['gl_auto_id'] = $UGRV_ID['GLAutoID'];
                $data_arr['gl_code'] = $UGRV['systemAccountCode'];
                $data_arr['secondary'] = $UGRV['GLSecondaryCode'];
                $data_arr['gl_desc'] = $UGRV['GLDescription'] . ' - ' . $addon_detail[$i]['supplierName'];
                $data_arr['gl_type'] = $UGRV['subCategory'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = $addon_detail[$i]['total_amount']+($addon_tax + $addon_tax_impact);
                $data_arr['amount_type'] = 'cr';
                $data_arr['isAddon'] = 1;
                $data_arr['subLedgerType'] = 1;
                $data_arr['subLedgerDesc'] = 'UGRV';
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'SUP';
                $data_arr['partyAutoID'] = $addon_detail[$i]['supplierID'];
                $data_arr['partySystemCode'] = $supplier_arr['supplierSystemCode'];
                $data_arr['partyName'] = $supplier_arr['supplierName'];
                $data_arr['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
                $data_arr['partyCurrency'] = $supplier_arr['supplierCurrency'];
                $data_arr['transactionExchangeRate'] = null;
                $data_arr['companyLocalExchangeRate'] = null;
                $data_arr['companyReportingExchangeRate'] = null;
                $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $data_arr['partyCurrencyID']);
                $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
                $data_arr['partyCurrencyDecimalPlaces'] = $conversion_arr['DecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = ($addon_detail[$i]['total_amount'] / $conversion_arr['conversion']);
                array_push($gl_array['gl_detail'], $data_arr);
            }
        }

        $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $master['supplierCurrencyID']); /*get currency conversion for seleted supplier's currency in grv header*/
        for ($i = 0; $i < count($detail); $i++) {
            $addon = ($detail[$i]['receivedTotalAmount'] / $grv_total) * $addon_total;
            $addon_tax_total = ($detail[$i]['receivedTotalAmount'] / $grv_total) * $addon_tax_impact;

            $data_arr['auto_id'] = $detail[$i]['grvDetailsID'];
            if ($detail[$i]['financeCategory'] == 1 or $detail[$i]['financeCategory'] == 3) {
                $data_arr['gl_auto_id'] = $detail[$i]['BLGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['BLGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['BLDescription'];
                $data_arr['gl_type'] = $detail[$i]['BLType'];
            } else {
                $data_arr['gl_auto_id'] = $detail[$i]['PLGLAutoID'];
                $data_arr['gl_code'] = $detail[$i]['PLSystemGLCode'];
                $data_arr['secondary'] = $detail[$i]['PLGLCode'];
                $data_arr['gl_desc'] = $detail[$i]['PLDescription'];
                $data_arr['gl_type'] = $detail[$i]['PLType'];
            }

            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['projectID'] = $detail[$i]['projectID'];
            $data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            $data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $data_arr['project_categoryID'] = isset($detail[$i]['project_categoryID']) ? $detail[$i]['project_categoryID'] : null;
            $data_arr['project_subCategoryID'] = isset($detail[$i]['project_subCategoryID']) ? $detail[$i]['project_subCategoryID'] : null;;
            $data_arr['gl_dr'] = ($detail[$i]['receivedTotalAmount'] + ($addon + $addon_tax_total));
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'SUP';
            $data_arr['partyAutoID'] = $master['supplierID'];
            $data_arr['partySystemCode'] = $master['supplierSystemCode'];
            $data_arr['partyName'] = $master['supplierName'];
            $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $master['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
            $data_arr['partyCurrencyAmount'] = (($detail[$i]['receivedTotalAmount'] + $addon) / $conversion_arr['conversion']);
            $data_arr['partyCurrencyDecimalPlaces'] = fetch_currency_desimal($data_arr['partyCurrency']);

            $cr_total += $detail[$i]['receivedTotalAmount'];

            for ($x = 0; $x < count($addon_item); $x++) {
                if ($addon_item[$x]['item_id'] == $detail[$i]['grvDetailsID']) {
                    $data_arr['gl_dr'] += $addon_item[$x]['total'];
                }
            }
            //array_push($gl_array['gl_detail'], $data_arr);
            array_push($m_arr, $data_arr);
        }
        //$m_arr = $this->array_group_sum_grv($m_arr);
        $m_arr = $this->array_group_sum_pm($m_arr);

        foreach ($m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }

        //$gl_array['gl_detail'] = $this->array_group_sum($gl_array['gl_detail']);
        foreach ($expanss_item as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }


        for ($i = 0; $i < count($varRegNotActive); $i++) {

            $data_arr['auto_id'] = $varRegNotActive[$i]['grvDetailsID'];

            $data_arr['gl_auto_id'] = $varRegNotActive[$i]['grvGLAutoID'];
            $data_arr['gl_code'] = $varRegNotActive[$i]['grvsystemAccountCode'];
            $data_arr['secondary'] = $varRegNotActive[$i]['grvGLSecondaryCode'];
            $data_arr['gl_desc'] = $varRegNotActive[$i]['grvGLDescription'];
            $data_arr['gl_type'] = $varRegNotActive[$i]['grvsubCategory'];


            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['projectID'] = $varRegNotActive[$i]['projectID'];
            $data_arr['projectID'] = isset($varRegNotActive[$i]['projectID']) ? $varRegNotActive[$i]['projectID'] : null;
            $data_arr['projectExchangeRate'] = isset($varRegNotActive[$i]['projectExchangeRate']) ? $varRegNotActive[$i]['projectExchangeRate'] : null;
            $data_arr['project_categoryID'] = isset($varRegNotActive[$i]['project_categoryID']) ? $varRegNotActive[$i]['project_categoryID'] : null;
            $data_arr['project_subCategoryID'] = isset($varRegNotActive[$i]['project_subCategoryID']) ? $varRegNotActive[$i]['project_subCategoryID'] : null;;
            $data_arr['gl_dr'] = ($varRegNotActive[$i]['taxLedgerAmount']);
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'SUP';
            $data_arr['partyAutoID'] = $master['supplierID'];
            $data_arr['partySystemCode'] = $master['supplierSystemCode'];
            $data_arr['partyName'] = $master['supplierName'];
            $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $master['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
            $data_arr['partyCurrencyAmount'] = (($varRegNotActive[$i]['taxLedgerAmount']) / $conversion_arr['conversion']);
            $data_arr['partyCurrencyDecimalPlaces'] = fetch_currency_desimal($data_arr['partyCurrency']);

            $cr_total += $varRegNotActive[$i]['taxLedgerAmount'];
            array_push($gl_array['gl_detail'], $data_arr);
        }


        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $UGRV_ID['GLAutoID'];
        $data_arr['gl_code'] = $UGRV['systemAccountCode'];
        $data_arr['secondary'] = $UGRV['GLSecondaryCode'];
        $data_arr['gl_desc'] = $UGRV['GLDescription'] . ' - ' . $master['supplierName'];
        $data_arr['gl_type'] = $UGRV['subCategory'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];
        $data_arr['gl_dr'] = 0;
        $data_arr['gl_cr'] = $cr_total;
        $data_arr['amount_type'] = 'cr';
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 1;
        $data_arr['subLedgerDesc'] = 'UGRV';
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = 'SUP';
        $data_arr['partyAutoID'] = $master['supplierID'];
        $data_arr['partySystemCode'] = $master['supplierSystemCode'];
        $data_arr['partyName'] = $master['supplierName'];
        $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
        $data_arr['partyCurrency'] = $master['supplierCurrency'];
        $data_arr['transactionExchangeRate'] = null;
        $data_arr['companyLocalExchangeRate'] = null;
        $data_arr['companyReportingExchangeRate'] = null;
        $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];;
        $data_arr['partyCurrencyAmount'] = ($cr_total / $conversion_arr['conversion']);
        $data_arr['partyCurrencyDecimalPlaces'] = fetch_currency_desimal($data_arr['partyCurrency']);
        array_push($gl_array['gl_detail'], $data_arr);
        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = $code;
        $gl_array['name'] = $code;
        $gl_array['primary_Code'] = $master['grvPrimaryCode'];
        $gl_array['date'] = $master['grvDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['suppliernamemaster'] = $master['suppliernamemaster'];
        return $gl_array;
    }

    function fetch_double_entry_receipt_match_data($matchID, $code)
    {
        $gl_array = array();
        $gl_array['gl_detail'] = array();
        $this->db->select('srp_erp_rvadvancematch.*,srp_erp_customermaster.vatIdNo as vatIdNo,srp_erp_customermaster.customerName as customerNamemaster');
        $this->db->where('matchID', $matchID);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_rvadvancematch.customerID', 'left');
        $master = $this->db->get('srp_erp_rvadvancematch')->row_array();

        $gl_array = array();
        $gl_array['gl_detail'] = array();
        $item_tax_group_wise = $this->db->query("SELECT *  FROM(
                    SELECT documentDetailAutoID as auto_id, taxGlAutoID as gl_auto_id, outputVatTransferGLAccountAutoID, SUM(amount) as taxAmount, taxMasterID, isAdvance
                    FROM `srp_erp_taxledger`
                    LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                    WHERE documentID = 'RVM' AND documentMasterAutoID = {$matchID} GROUP BY taxMasterID
                ) t1
                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = t1.gl_auto_id")->result_array();

        for ($i = 0; $i < count($item_tax_group_wise); $i++) {
            if (!empty($item_tax_group_wise[$i]['taxMasterID'])) {
                $data_arr_tx_group['auto_id'] = $item_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise[$i]['gl_auto_id'];
                $data_arr_tx_group['gl_code'] = $item_tax_group_wise[$i]['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $item_tax_group_wise[$i]['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $item_tax_group_wise[$i]['GLDescription'];
                $data_arr_tx_group['gl_type'] = $item_tax_group_wise[$i]['subCategory'];

                $data_arr_tx_group['segment_id'] = null;
                $data_arr_tx_group['segment'] = null;
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = null;
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = $master['customerID'];
                $data_arr_tx_group['partySystemCode'] = $master['customerSystemCode'];
                $data_arr_tx_group['partyName'] = $master['customerName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['customerCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = $master['transactionExchangeRate'];
                $data_arr_tx_group['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr_tx_group['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr_tx_group['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = '';
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'dr';
                $data_arr_tx_group['gl_cr'] = 0;
                $data_arr_tx_group['gl_dr'] = $item_tax_group_wise[$i]['taxAmount'];
                array_push($gl_array['gl_detail'], $data_arr_tx_group);



                $this->db->select('GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory');
                $this->db->where('GLAutoID', $item_tax_group_wise[$i]['outputVatTransferGLAccountAutoID']);
                $transferGL_advance = $this->db->get('srp_erp_chartofaccounts')->row_array();

                $data_arr_tx_group['auto_id'] = $item_tax_group_wise[$i]['auto_id'];
                $data_arr_tx_group['gl_auto_id'] = $item_tax_group_wise[$i]['outputVatTransferGLAccountAutoID'];
                $data_arr_tx_group['gl_code'] = $transferGL_advance['systemAccountCode'];
                $data_arr_tx_group['secondary'] = $transferGL_advance['GLSecondaryCode'];
                $data_arr_tx_group['gl_desc'] = $transferGL_advance['GLDescription'];
                $data_arr_tx_group['gl_type'] = $transferGL_advance['subCategory'];

                $data_arr_tx_group['segment_id'] = null;
                $data_arr_tx_group['segment'] = null;
                $data_arr_tx_group['isAddon'] = 0;
                $data_arr_tx_group['projectID'] = null;
                $data_arr_tx_group['project_categoryID'] = null;
                $data_arr_tx_group['project_subCategoryID'] = null;
                $data_arr_tx_group['projectExchangeRate'] = null;
                $data_arr_tx_group['taxMasterAutoID'] = $item_tax_group_wise[$i]['taxMasterID'];
                $data_arr_tx_group['partyVatIdNo'] = null;
                $data_arr_tx_group['subLedgerType'] = null;
                $data_arr_tx_group['subLedgerDesc'] = null;
                $data_arr_tx_group['partyContractID'] = null;
                $data_arr_tx_group['partyType'] = 'AUT';
                $data_arr_tx_group['partyAutoID'] = $master['customerID'];
                $data_arr_tx_group['partySystemCode'] = $master['customerSystemCode'];
                $data_arr_tx_group['partyName'] = $master['customerName'];
                $data_arr_tx_group['partyCurrencyID'] = $master['customerCurrencyID'];
                $data_arr_tx_group['partyCurrency'] = $master['customerCurrency'];
                $data_arr_tx_group['transactionExchangeRate'] = $master['transactionExchangeRate'];
                $data_arr_tx_group['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr_tx_group['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr_tx_group['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data_arr_tx_group['partyCurrencyAmount'] = '';
                $data_arr_tx_group['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $data_arr_tx_group['amount_type'] = 'cr';
                $data_arr_tx_group['gl_dr'] = 0;
                $data_arr_tx_group['gl_cr'] = $item_tax_group_wise[$i]['taxAmount'];
                array_push($gl_array['gl_detail'], $data_arr_tx_group);


            }
        }

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'RVM';
        $gl_array['name'] = 'Receipt Match';
        $gl_array['primary_Code'] = $master['matchSystemCode'];
        $gl_array['approved_YN'] = $master['confirmedYN'];
        $gl_array['date'] = $master['matchDate'];
        $gl_array['customername'] = $master['customerNamemaster'];
        $gl_array['master_data'] = $master;
        if (!empty($master['customerNamemaster'])) {
            $gl_array['customername'] = $master['customerNamemaster'];
        } else {
            $gl_array['customername'] = $master['customerName'];
        }
        return $gl_array;
    }

    /**
     * Get double entry for logistic grv
     *
     * @param integer $grvAutoID
     * @param string|null $code
     * @return array
     */
    function fetch_double_entry_logistic_grv_data($grvAutoID, $code = null)
    {
        $gl_array = array();
        $gl_array['gl_detail'] = array();

        $companyID = current_companyID();
        $UGRV_ID = $this->db->query("SELECT srp_erp_chartofaccounts.GLAutoID 
                    FROM srp_erp_chartofaccounts
                    JOIN srp_erp_companycontrolaccounts ON srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
                    WHERE controllAccountYN = 1 AND srp_erp_companycontrolaccounts.companyID = {$companyID} AND srp_erp_chartofaccounts.companyID = {$companyID} AND controlAccountType = 'UGRV'")->row_array();
        $UGRV = fetch_gl_account_desc($UGRV_ID['GLAutoID']);

        $this->db->select('srp_erp_grvmaster.*,srp_erp_suppliermaster.supplierName as suppliernamemaster');
        $this->db->where('grvAutoID', $grvAutoID);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_grvmaster.supplierID', 'left');
        $master = $this->db->get('srp_erp_grvmaster')->row_array();

        $this->db->select('grvDetailsID,itemFinanceCategory,PLGLAutoID,PLSystemGLCode,PLGLCode,PLDescription, PLType,BLGLAutoID, BLSystemGLCode ,BLGLCode,BLDescription, BLType,receivedTotalAmount,financeCategory,projectID,projectExchangeRate,project_categoryID,project_subCategoryID,poLogisticID');
        $this->db->where('grvAutoID', $grvAutoID);
        $detail = $this->db->get('srp_erp_grvdetails')->result_array();

        $conversion_arr = currency_conversionID($master['transactionCurrencyID'], $master['supplierCurrencyID']); /*get currency conversion for seleted supplier's currency in grv header*/
        for ($i = 0; $i < count($detail); $i++) {
            $this->db->select('matchedAmount');
            $this->db->where('poLogisticID', $detail[$i]['poLogisticID']);
            $matchedAmount = $this->db->get('srp_erp_purchase_order_logistic')->row('matchedAmount');
            $actualAmount = $detail[$i]['receivedTotalAmount'];

            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $UGRV_ID['GLAutoID'];
            $data_arr['gl_code'] = $UGRV['systemAccountCode'];
            $data_arr['secondary'] = $UGRV['GLSecondaryCode'];
            $data_arr['gl_desc'] = $UGRV['GLDescription'] . ' - ' . $master['supplierName'];
            $data_arr['gl_type'] = $UGRV['subCategory'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = 0;
            $data_arr['gl_cr'] = $actualAmount;
            $data_arr['amount_type'] = 'cr';
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 1;
            $data_arr['subLedgerDesc'] = 'UGRV';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'SUP';
            $data_arr['partyAutoID'] = $master['supplierID'];
            $data_arr['partySystemCode'] = $master['supplierSystemCode'];
            $data_arr['partyName'] = $master['supplierName'];
            $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $master['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];;
            $data_arr['partyCurrencyAmount'] = ($actualAmount / $conversion_arr['conversion']);
            $data_arr['partyCurrencyDecimalPlaces'] = fetch_currency_desimal($data_arr['partyCurrency']);

            array_push($gl_array['gl_detail'], $data_arr);

            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $UGRV_ID['GLAutoID'];
            $data_arr['gl_code'] = $UGRV['systemAccountCode'];
            $data_arr['secondary'] = $UGRV['GLSecondaryCode'];
            $data_arr['gl_desc'] = $UGRV['GLDescription'] . ' - ' . $master['supplierName'];
            $data_arr['gl_type'] = $UGRV['subCategory'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['gl_dr'] = $matchedAmount;
            $data_arr['gl_cr'] = 0;
            $data_arr['amount_type'] = 'dr';
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 1;
            $data_arr['subLedgerDesc'] = 'UGRV';
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'SUP';
            $data_arr['partyAutoID'] = $master['supplierID'];
            $data_arr['partySystemCode'] = $master['supplierSystemCode'];
            $data_arr['partyName'] = $master['supplierName'];
            $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $master['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];;
            $data_arr['partyCurrencyAmount'] = ($matchedAmount / $conversion_arr['conversion']);
            $data_arr['partyCurrencyDecimalPlaces'] = fetch_currency_desimal($data_arr['partyCurrency']);

            array_push($gl_array['gl_detail'], $data_arr);


            $data_arr['auto_id'] = $detail[$i]['grvDetailsID'];
            $data_arr['gl_auto_id'] = $detail[$i]['PLGLAutoID'];
            $data_arr['gl_code'] = $detail[$i]['PLSystemGLCode'];
            $data_arr['secondary'] = $detail[$i]['PLGLCode'];
            $data_arr['gl_desc'] = $detail[$i]['PLDescription'];
            $data_arr['gl_type'] = $detail[$i]['PLType'];
            $data_arr['segment_id'] = $master['segmentID'];
            $data_arr['segment'] = $master['segmentCode'];
            $data_arr['projectID'] = $detail[$i]['projectID'];
            $data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            $data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $data_arr['project_categoryID'] = isset($detail[$i]['project_categoryID']) ? $detail[$i]['project_categoryID'] : null;
            $data_arr['project_subCategoryID'] = isset($detail[$i]['project_subCategoryID']) ? $detail[$i]['project_subCategoryID'] : null;;

            if($actualAmount > $matchedAmount){
                $data_arr['gl_dr'] = $actualAmount - $matchedAmount;
                $data_arr['gl_cr'] = 0;
                $data_arr['amount_type'] = 'dr';
            }
            else{
                $data_arr['gl_dr'] = 0;
                $data_arr['gl_cr'] = abs($actualAmount - $matchedAmount);
                $data_arr['amount_type'] = 'cr';
            }

            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'SUP';
            $data_arr['partyAutoID'] = $master['supplierID'];
            $data_arr['partySystemCode'] = $master['supplierSystemCode'];
            $data_arr['partyName'] = $master['supplierName'];
            $data_arr['partyCurrencyID'] = $master['supplierCurrencyID'];
            $data_arr['partyCurrency'] = $master['supplierCurrency'];
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = $conversion_arr['conversion'];
            $data_arr['partyCurrencyAmount'] = (($detail[$i]['receivedTotalAmount']) / $conversion_arr['conversion']);
            $data_arr['partyCurrencyDecimalPlaces'] = fetch_currency_desimal($data_arr['partyCurrency']);

            array_push($gl_array['gl_detail'], $data_arr);

        }

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = $code;
        $gl_array['name'] = $code;
        $gl_array['primary_Code'] = $master['grvPrimaryCode'];
        $gl_array['date'] = $master['grvDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['FYPeriodDateFrom'] . ' - ' . $master['FYPeriodDateTo'];
        $gl_array['master_data'] = $master;
        $gl_array['suppliernamemaster'] = $master['suppliernamemaster'];

        return $gl_array;
    }
}
