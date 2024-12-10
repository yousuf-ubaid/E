<?php
/**
 *
 * -- =============================================
 * -- File Name : Yield_preparation_model.php
 * -- Project Name : POS
 * -- Module Name : Yield preparation model
 * -- Create date : 25 October 2017
 * -- Description : database script related to yield prepartion.
 *
 * --REVISION HISTORY
 * --Date:
 * -- =============================================
 **/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class POS_Yield_preparation_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function load_yield_detail()
    {
        $companyID = current_companyID();
        $yieldID = $this->input->post('yieldID');
        $result = $this->db->query("SELECT
        yieldDetailID,
        yieldID,
        description,
        typeAutoId,
        qty,
        uom,
        cost,
        srp_erp_pos_menuyieldsdetails.itemAutoID,
        srp_erp_unit_of_measure.UnitDes AS UnitDes,
        CONCAT( srp_erp_itemmaster.itemDescription, ' - ', srp_erp_itemmaster.itemSystemCode ) AS itemDescription,
        getUoMConvertion ( srp_erp_pos_menuyieldsdetails.uom, defaultUnitOfMeasureID, $companyID ) AS conversionRateUOM,
        ( srp_erp_itemmaster.companyLocalWacAmount / getUoMConvertion ( srp_erp_pos_menuyieldsdetails.uom, defaultUnitOfMeasureID, $companyID ) ) AS companyLocalWacAmount,
        IFNULL(abs(( SUM( SUBSTRING( srp_erp_itemledger.transactionAmount, 1, 16 ) / srp_erp_itemledger.companyLocalExchangeRate ) ) / ( sum( srp_erp_itemledger.transactionQty / srp_erp_itemledger.convertionRate ) )),0) AS unitCost 
    FROM
        `srp_erp_pos_menuyieldsdetails`
        LEFT JOIN `srp_erp_unit_of_measure` ON `srp_erp_pos_menuyieldsdetails`.`uom` = `srp_erp_unit_of_measure`.`UnitID`
        LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_pos_menuyieldsdetails`.`itemAutoID` 
        LEFT JOIN srp_erp_itemledger ON srp_erp_itemledger.itemAutoID = srp_erp_itemmaster.itemAutoID
    WHERE
        `srp_erp_pos_menuyieldsdetails`.`companyID` = $companyID 
        AND `srp_erp_pos_menuyieldsdetails`.`yieldID` = $yieldID
        GROUP BY yieldDetailID")->result_array();
        return $result;
    }

    function load_yield_preparation_detail()
    {
        $result = $this->db->select('srp_erp_pos_menuyieldpreparationdetails.*,CONCAT(itemDescription," - ",itemSystemCode) as itemDescription,companyLocalWacAmount', false)
            ->from('srp_erp_pos_menuyieldpreparationdetails')
            ->join('srp_erp_unit_of_measure', 'srp_erp_pos_menuyieldpreparationdetails.uomID = srp_erp_unit_of_measure.UnitID', 'left')
            ->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID=srp_erp_pos_menuyieldpreparationdetails.itemAutoID', 'left')
            ->where('srp_erp_pos_menuyieldpreparationdetails.companyID', current_companyID())
            ->where('srp_erp_pos_menuyieldpreparationdetails.yieldPreparationID', $this->input->post('yieldPreparationID'))->get()->result_array();
        return $result;
    }


    function save_yieldPreparation()
    {
        $last_id = "";
        $this->db->trans_start();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $date_format_policy = date_format_policy();
        $documentDate = input_format_date(trim($this->input->post('documentDate') ?? ''), $date_format_policy);
        $yieldMasterID = explode("-", $this->input->post('yieldMasterID'));
        if ($this->input->post('status') == 1) {
            $item = array();
            $itemAutoID = $this->input->post('itemAutoID');
            if (!empty($itemAutoID)) {
                foreach ($itemAutoID as $key => $val) {
                    if ($this->input->post('totalQty')[$key] > 0) {
                        $sql = "SELECT * from srp_erp_itemledger
                                WHERE itemAutoID = (" . $val . ") AND wareHouseAutoID =" . $this->input->post('warehouseAutoID') . " AND companyID = " . current_companyID() ." GROUP BY srp_erp_itemledger.itemAutoID";
                        $check_exist = $this->db->query($sql)->result_array();
                        if ($check_exist) {
                            $sql = "SELECT defaultUnitOfMeasureID FROM `srp_erp_itemmaster` WHERE `companyID` = " . current_companyID() . " AND `itemAutoID` = " . $val;
                            $defaultUnitOfMeasureID = $this->db->query($sql)->row("defaultUnitOfMeasureID");

                            $sql = "SELECT conversion FROM `srp_erp_unitsconversion` WHERE `companyID` = " . current_companyID() . " AND `masterUnitID` = " . $defaultUnitOfMeasureID . " AND subUnitID = " . $this->input->post('yuomID')[$key];
                            $result = $this->db->query($sql)->row_array();

                            if ($this->input->post('totalQty')[$key]>0) {
                                $sqls = "SELECT (IFNULL(SUM(srp_erp_itemledger.transactionQTY/srp_erp_itemledger.convertionRate),0) - ({$this->input->post('totalQty')[$key]} / {$result["conversion"]})) as stock,itemAutoID from srp_erp_itemledger
                                WHERE itemAutoID = " . $val . " AND wareHouseAutoID =" . $this->input->post('warehouseAutoID') . " AND companyID = " . current_companyID() . " GROUP BY srp_erp_itemledger.itemAutoID HAVING  stock < 0";
                                $item_low_qty = $this->db->query($sqls)->row_array();

                                if (!empty($item_low_qty)) {
                                    $item[] = $val;
                                }
                            }
                        } else {
                            $item[] = $val;
                        }
                    }
                }
            }

            if (!empty($item)) {
                return array('w', 'Some Item quantities are not sufficient to save this transaction!', $item);
                exit;
            }
        }

        if (!$this->input->post('yieldPreparationID')) {
            $serialInfo = generateMFQ_SystemCode('srp_erp_pos_menuyieldpreparation', 'yieldPreparationID', 'companyID');
            $codes = $this->sequence->sequence_generator('YPRP', $serialInfo['serialNo']);
            $itemDetail = fetch_item_data($yieldMasterID[3]);
            $this->db->set('serialNo', $serialInfo['serialNo']);
            $this->db->set('documentSystemCode', $codes);
            $this->db->set('documentDate', $documentDate);
            $this->db->set('documentID', 'YPRP');
            $this->db->set('itemAutoID', $yieldMasterID[3]);
            $this->db->set('yieldMasterID', $yieldMasterID[0]);
            $this->db->set('assetGLAutoID', $itemDetail["assteGLAutoID"]);
            $this->db->set('narration', $this->input->post('narration'));
            $this->db->set('uomID', $this->input->post('uomID'));
            $this->db->set('qty', $this->input->post('qty'));
            $this->db->set('warehouseAutoID', $this->input->post('warehouseAutoID'));
            $this->db->set('companyID', current_companyID());

            if ($financeyearperiodYN == 1) {
                $financeYear = get_financial_from_to($this->input->post('financeyear'));
                $financePeriod = fetchFinancePeriod($this->input->post('financeyear_period'));

                $companyFinanceYearID = $this->input->post('financeyear');
                $companyFinanceYear = $financeYear["beginingDate"] . " - " . $financeYear["endingDate"];
                $FYBegin = $financeYear["beginingDate"];
                $FYEnd = $financeYear["endingDate"];
                $companyFinancePeriodID = $this->input->post('financeyear_period');
                $FYPeriodDateFrom = $financePeriod['dateFrom'];
                $FYPeriodDateTo = $financePeriod['dateTo'];
            } else {
                $financeYearDetails = get_financial_year($documentDate);
                if (empty($financeYearDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $FYBegin = $financeYearDetails['beginingDate'];
                    $FYEnd = $financeYearDetails['endingDate'];
                    $companyFinanceYear = $FYBegin . ' - ' . $FYEnd;
                    $companyFinanceYearID = $financeYearDetails['companyFinanceYearID'];
                }
                $financePeriodDetails = get_financial_period_date_wise($documentDate);

                if (empty($financePeriodDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $companyFinancePeriodID = $financePeriodDetails['companyFinancePeriodID'];
                    $FYPeriodDateFrom = $financePeriodDetails['dateFrom'];
                    $FYPeriodDateTo = $financePeriodDetails['dateTo'];
                }
            }

            $this->db->set('companyFinanceYearID', $companyFinanceYearID);
            $this->db->set('companyFinanceYear', $companyFinanceYear);
            $this->db->set('FYBegin', $FYBegin);
            $this->db->set('FYEnd', $FYEnd);
            $this->db->set('companyFinancePeriodID', $companyFinancePeriodID);
            $this->db->set('FYPeriodDateFrom', $FYPeriodDateFrom);
            $this->db->set('FYPeriodDateTo', $FYPeriodDateTo);

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
            if ($this->input->post('status') == 2) {
                $this->db->set('confirmedYN', 1);
                $this->db->set('confirmedUserID', current_userID());
                $this->db->set('confirmedUserName', current_user());
                $this->db->set('confirmedDateTime', current_date(false));
            }
            $result = $this->db->insert('srp_erp_pos_menuyieldpreparation');
            $last_id = $this->db->insert_id();
        } else {
            $itemDetail = fetch_item_data($yieldMasterID[3]);
            $last_id = $this->input->post('yieldPreparationID');
            $this->db->set('documentDate', $documentDate);
            $this->db->set('itemAutoID', $yieldMasterID[3]);
            $this->db->set('yieldMasterID', $yieldMasterID[0]);
            $this->db->set('assetGLAutoID', $itemDetail["assteGLAutoID"]);
            $this->db->set('narration', $this->input->post('narration'));
            $this->db->set('uomID', $this->input->post('uomID'));
            $this->db->set('qty', $this->input->post('qty'));
            $this->db->set('warehouseAutoID', $this->input->post('warehouseAutoID'));

            if ($financeyearperiodYN == 1) {
                $financeYear = get_financial_from_to($this->input->post('financeyear'));
                $financePeriod = fetchFinancePeriod($this->input->post('financeyear_period'));

                $companyFinanceYearID = $this->input->post('financeyear');
                $companyFinanceYear = $financeYear["beginingDate"] . " - " . $financeYear["endingDate"];
                $FYBegin = $financeYear["beginingDate"];
                $FYEnd = $financeYear["endingDate"];
                $companyFinancePeriodID = $this->input->post('financeyear_period');
                $FYPeriodDateFrom = $financePeriod['dateFrom'];
                $FYPeriodDateTo = $financePeriod['dateTo'];
            } else {
                $financeYearDetails = get_financial_year($documentDate);
                if (empty($financeYearDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $FYBegin = $financeYearDetails['beginingDate'];
                    $FYEnd = $financeYearDetails['endingDate'];
                    $companyFinanceYear = $FYBegin . ' - ' . $FYEnd;
                    $companyFinanceYearID = $financeYearDetails['companyFinanceYearID'];
                }
                $financePeriodDetails = get_financial_period_date_wise($documentDate);

                if (empty($financePeriodDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $companyFinancePeriodID = $financePeriodDetails['companyFinancePeriodID'];
                    $FYPeriodDateFrom = $financePeriodDetails['dateFrom'];
                    $FYPeriodDateTo = $financePeriodDetails['dateTo'];
                }
            }

            $this->db->set('companyFinanceYearID', $companyFinanceYearID);
            $this->db->set('companyFinanceYear', $companyFinanceYear);
            $this->db->set('FYBegin', $FYBegin);
            $this->db->set('FYEnd', $FYEnd);
            $this->db->set('companyFinancePeriodID', $companyFinancePeriodID);
            $this->db->set('FYPeriodDateFrom', $FYPeriodDateFrom);
            $this->db->set('FYPeriodDateTo', $FYPeriodDateTo);

          /*  $financeYear = get_financial_from_to($this->input->post('financeyear'));
            $financePeriod = fetchFinancePeriod($this->input->post('financeyear_period'));
            $this->db->set('companyFinanceYearID', $this->input->post('financeyear'));
            $this->db->set('companyFinanceYear', $financeYear["beginingDate"] . " - " . $financeYear["endingDate"]);
            $this->db->set('FYBegin', $financeYear["beginingDate"]);
            $this->db->set('FYEnd', $financeYear["endingDate"]);
            $this->db->set('companyFinancePeriodID', $this->input->post('financeyear_period'));
            $this->db->set('FYPeriodDateFrom', $financePeriod['dateFrom']);
            $this->db->set('FYPeriodDateTo', $financePeriod['dateTo']);*/

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
            if ($this->input->post('status') == 2) {
                $this->db->set('confirmedYN', 1);
                $this->db->set('confirmedUserID', current_userID());
                $this->db->set('confirmedUserName', current_user());
                $this->db->set('confirmedDateTime', current_date(false));
            }
            $this->db->where('yieldPreparationID', $last_id);
            $result = $this->db->update('srp_erp_pos_menuyieldpreparation');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Yield Preparation Saved Failed ' . $this->db->_error_message());
        } else {
            $yieldPreparationDetailID = $this->input->post('yieldPreparationDetailID');
            $itemAutoID = $this->input->post('itemAutoID');
            if (!empty($itemAutoID)) {
                foreach ($itemAutoID as $key => $val) {
                    if (!empty($yieldPreparationDetailID[$key])) {
                        $itemDetail = fetch_item_data($val);
                        $this->db->set('yieldPreparationID', $last_id);
                        $this->db->set('yieldMasterID', $yieldMasterID[0]);
                        $this->db->set('yieldDetailID', $this->input->post('yieldDetailID')[$key]);
                        $this->db->set('itemAutoID', $val);
                        $this->db->set('uomID', $this->input->post('yuomID')[$key]);
                        $this->db->set('qty', $this->input->post('yQty')[$key]);
                        $this->db->set('totalQty', $this->input->post('totalQty')[$key]);
                        $this->db->set('localWacAmount', $this->input->post('localWacAmount')[$key]);
                        $this->db->set('localWacAmountTotal', $this->input->post('localWacAmountTotal')[$key]);
                        $this->db->set('assetGLAutoID', $itemDetail["assteGLAutoID"]);

                        $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('modifiedUserID', current_userID());
                        $this->db->set('modifiedUserName', current_user());
                        $this->db->set('modifiedDateTime', current_date(true));
                        $this->db->where('yieldPreparationDetailID', $yieldPreparationDetailID[$key]);
                        $result = $this->db->update('srp_erp_pos_menuyieldpreparationdetails');
                    } else {
                        if (!empty($itemAutoID[$key])) {
                            $itemDetail = fetch_item_data($val);
                            $this->db->set('yieldPreparationID', $last_id);
                            $this->db->set('yieldMasterID', $yieldMasterID[0]);
                            $this->db->set('yieldDetailID', $this->input->post('yieldDetailID')[$key]);
                            $this->db->set('itemAutoID', $this->input->post('itemAutoID')[$key]);
                            $this->db->set('uomID', $this->input->post('yuomID')[$key]);
                            $this->db->set('qty', $this->input->post('yQty')[$key]);
                            $this->db->set('totalQty', $this->input->post('totalQty')[$key]);
                            $this->db->set('localWacAmount', $this->input->post('localWacAmount')[$key]);
                            $this->db->set('localWacAmountTotal', $this->input->post('localWacAmountTotal')[$key]);
                            $this->db->set('assetGLAutoID', $itemDetail["assteGLAutoID"]);
                            $this->db->set('companyID', current_companyID());
                            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('createdUserID', current_userID());
                            $this->db->set('createdUserName', current_user());
                            $this->db->set('createdDateTime', current_date(true));
                            $result = $this->db->insert('srp_erp_pos_menuyieldpreparationdetails');
                        }
                    }
                }
            }

            /*double entry */
            if ($this->input->post('status') == 1) {
                $double_entry = $this->double_entry_model->fetch_double_entry_yield_preparation($last_id, 'YPRP');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['yieldPreparationID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['documentSystemCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['documentDate'];
                    $generalledger_arr[$i]['documentType'] = null;
                    $generalledger_arr[$i]['documentYear'] = date("Y", strtotime($double_entry['master_data']['documentDate']));
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['documentDate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['narration'];
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedUserID'];
                    $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedUserName'];
                    $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                    $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                    if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    }
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                    $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                    $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                    $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                    $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                    $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                    $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                    $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                    $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                    $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                    $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                    $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                }

                if (!empty($generalledger_arr)) {
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                }

                $this->db->select('srp_erp_warehousemaster.*,srp_erp_pos_menuyieldpreparation.*,srp_erp_unit_of_measure.UnitShortCode');
                $this->db->where('yieldPreparationID', $last_id);
                $this->db->join('srp_erp_warehousemaster', "srp_erp_pos_menuyieldpreparation.wareHouseAutoID = srp_erp_warehousemaster.wareHouseAutoID", 'left');
                $this->db->join('srp_erp_unit_of_measure', "uomID = UnitID", 'left');
                $master = $this->db->get('srp_erp_pos_menuyieldpreparation')->row_array();

                $this->db->select('SUM(localWacAmountTotal) as localWacAmountTotal');
                $this->db->where('srp_erp_pos_menuyieldpreparation.yieldPreparationID', $last_id);
                $this->db->join('srp_erp_pos_menuyieldpreparationdetails', "srp_erp_pos_menuyieldpreparationdetails.yieldPreparationID = srp_erp_pos_menuyieldpreparation.yieldPreparationID", 'left');
                $detailSum = $this->db->get('srp_erp_pos_menuyieldpreparation')->row_array();

                $this->db->select('srp_erp_pos_menuyieldpreparationdetails.*,SUM(totalQty) as totalQty,SUM(localWacAmountTotal) as localWacAmountTotal,srp_erp_unit_of_measure.UnitShortCode');
                $this->db->join('srp_erp_unit_of_measure', "uomID = UnitID", 'left');
                $this->db->where('yieldPreparationID', $last_id);
                $this->db->group_by('itemAutoID');
                $detail = $this->db->get('srp_erp_pos_menuyieldpreparationdetails')->result_array();

                $item_arr = array();
                $itemledger_arr = array();
                if ($detail) {
                    for ($a = 0; $a < count($detail); $a++) {
                        $item = fetch_item_data($detail[$a]['itemAutoID']);
                        if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                            $conversionRate = $this->db->query("SELECT getUoMConvertion(" . $detail[$a]['uomID'] . ", " . $item["defaultUnitOfMeasureID"] . ", " . current_companyID() . ") as conversionRateUOM")->row_array();
                            $itemAutoID = $detail[$a]['itemAutoID'];
                            $qty = $detail[$a]['totalQty'] / $conversionRate['conversionRateUOM'];
                            $qty = ($qty * -1);
                            $wareHouseAutoID = $master['wareHouseAutoID'];
                            $itemExists = $this->db->query("SELECT * FROM srp_erp_warehouseitems WHERE wareHouseAutoID='{$wareHouseAutoID}' AND itemAutoID={$itemAutoID}")->row_array();
                            if ($itemExists) {
                                $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty}) WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                            } else {
                                $data_arr = array(
                                    'wareHouseAutoID' => $master['wareHouseAutoID'],
                                    'wareHouseLocation' => $master['wareHouseLocation'],
                                    'wareHouseDescription' => $master['wareHouseDescription'],
                                    'itemAutoID' => $item['itemAutoID'],
                                    'itemSystemCode' => $item['itemSystemCode'],
                                    'itemDescription' => $item['itemDescription'],
                                    'unitOfMeasureID' => $item['defaultUnitOfMeasureID'],
                                    'unitOfMeasure' => $item['defaultUnitOfMeasure'],
                                    'currentStock' => $qty,
                                    'companyID' => $this->common_data['company_data']['company_id'],
                                    'companyCode' => $this->common_data['company_data']['company_code'],
                                );

                                $this->db->insert('srp_erp_warehouseitems', $data_arr);
                            }
                            $item_arr[$a]['itemAutoID'] = $detail[$a]['itemAutoID'];
                            $item_arr[$a]['currentStock'] = ($item['currentStock'] + $qty);
                            /*$item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) + ($detail[$a]['localWacAmountTotal'] * -1)) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                            $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) + (($detail[$a]['localWacAmountTotal'] * -1) / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);*/
                            $itemledger_arr[$a]['documentID'] = $master['documentID'];
                            $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                            $itemledger_arr[$a]['documentAutoID'] = $master['yieldPreparationID'];
                            $itemledger_arr[$a]['documentSystemCode'] = $master['documentSystemCode'];
                            $itemledger_arr[$a]['documentDate'] = $master['documentDate'];
                            $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                            $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                            $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                            $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                            $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                            $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                            $itemledger_arr[$a]['wareHouseAutoID'] = $wareHouseAutoID;
                            $itemledger_arr[$a]['wareHouseCode'] = $master['wareHouseCode'];
                            $itemledger_arr[$a]['wareHouseLocation'] = $master['wareHouseLocation'];
                            $itemledger_arr[$a]['wareHouseDescription'] = $master['wareHouseDescription'];
                            $itemledger_arr[$a]['itemAutoID'] = $item['itemAutoID'];
                            $itemledger_arr[$a]['itemSystemCode'] = $item['itemSystemCode'];
                            $itemledger_arr[$a]['itemDescription'] = $item['itemDescription'];
                            $itemledger_arr[$a]['defaultUOMID'] = $item['defaultUnitOfMeasureID'];
                            $itemledger_arr[$a]['defaultUOM'] = $item['defaultUnitOfMeasure'];
                            $itemledger_arr[$a]['transactionUOM'] = $detail[$a]['UnitShortCode'];
                            $itemledger_arr[$a]['transactionUOMID'] = $detail[$a]['uomID'];
                            $itemledger_arr[$a]['transactionQTY'] = $detail[$a]['totalQty'] * -1;
                            $itemledger_arr[$a]['convertionRate'] = $conversionRate['conversionRateUOM'];
                            $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                            $itemledger_arr[$a]['PLGLAutoID'] = $item['costGLAutoID'];
                            $itemledger_arr[$a]['PLSystemGLCode'] = $item['costSystemGLCode'];
                            $itemledger_arr[$a]['PLGLCode'] = $item['costGLCode'];
                            $itemledger_arr[$a]['PLDescription'] = $item['costDescription'];
                            $itemledger_arr[$a]['PLType'] = $item['costType'];
                            $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                            $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                            $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                            $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                            $itemledger_arr[$a]['BLType'] = $item['assteType'];
                            $itemledger_arr[$a]['transactionAmount'] = $detail[$a]['localWacAmountTotal'] * -1;
                            $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                            $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                            $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                            $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                            $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                            $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                            $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyReportingWacAmount'] = $item['companyReportingWacAmount'] * -1;
                            $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                            $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedUserID'];
                            $itemledger_arr[$a]['confirmedByName'] = $master['confirmedUserName'];
                            $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDateTime'];
                            $itemledger_arr[$a]['companyID'] = $master['companyID'];
                            $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                            $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                            $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                            $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                            $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                            $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                            $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                            $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                            $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];
                        }
                    }
                    if (!empty($itemledger_arr)) {
                        $itemledger_arr = array_values($itemledger_arr);
                        $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                    }

                    if (!empty($item_arr)) {
                        $item_arr = array_values($item_arr);
                        $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                    }
                }

                $itemledger_arr = array();
                $item_arr = array();
                if ($master) {
                    $item = fetch_item_data($master['itemAutoID']);
                    if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                        $conversionRate = $this->db->query("SELECT getUoMConvertion(" . $master['uomID'] . ", " . $item["defaultUnitOfMeasureID"] . ", " . current_companyID() . ") as conversionRateUOM")->row_array();
                        $itemAutoID = $master['itemAutoID'];
                        $qty = $master['qty'] / $conversionRate['conversionRateUOM'];
                        $wareHouseAutoID = $master['wareHouseAutoID'];
                        $itemExists = $this->db->query("SELECT * FROM srp_erp_warehouseitems WHERE wareHouseAutoID='{$wareHouseAutoID}' AND itemAutoID={$itemAutoID}")->row_array();
                        if ($itemExists) {
                            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty}) WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                        } else {
                            $data_arr = array(
                                'wareHouseAutoID' => $master['wareHouseAutoID'],
                                'wareHouseLocation' => $master['wareHouseLocation'],
                                'wareHouseDescription' => $master['wareHouseDescription'],
                                'itemAutoID' => $item['itemAutoID'],
                                'itemSystemCode' => $item['itemSystemCode'],
                                'itemDescription' => $item['itemDescription'],
                                'unitOfMeasureID' => $item['defaultUnitOfMeasureID'],
                                'unitOfMeasure' => $item['defaultUnitOfMeasure'],
                                'currentStock' => $qty,
                                'companyID' => $this->common_data['company_data']['company_id'],
                                'companyCode' => $this->common_data['company_data']['company_code'],
                            );

                            $this->db->insert('srp_erp_warehouseitems', $data_arr);
                        }
                        $item_arr['itemAutoID'] = $itemAutoID;
                        $item_arr['currentStock'] = ($item['currentStock'] + $qty);
                        $item_arr['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) + $detailSum['localWacAmountTotal']) / $item_arr['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                        $item_arr['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) + ($detailSum['localWacAmountTotal'] / $master['companyReportingExchangeRate'])) / $item_arr['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
                        $itemledger_arr['documentID'] = $master['documentID'];
                        $itemledger_arr['documentCode'] = $master['documentID'];
                        $itemledger_arr['documentAutoID'] = $master['yieldPreparationID'];
                        $itemledger_arr['documentSystemCode'] = $master['documentSystemCode'];
                        $itemledger_arr['documentDate'] = $master['documentDate'];
                        $itemledger_arr['companyFinanceYearID'] = $master['companyFinanceYearID'];
                        $itemledger_arr['companyFinanceYear'] = $master['companyFinanceYear'];
                        $itemledger_arr['FYBegin'] = $master['FYBegin'];
                        $itemledger_arr['FYEnd'] = $master['FYEnd'];
                        $itemledger_arr['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                        $itemledger_arr['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                        $itemledger_arr['wareHouseAutoID'] = $wareHouseAutoID;
                        $itemledger_arr['wareHouseCode'] = $master['wareHouseCode'];
                        $itemledger_arr['wareHouseLocation'] = $master['wareHouseLocation'];
                        $itemledger_arr['wareHouseDescription'] = $master['wareHouseDescription'];
                        $itemledger_arr['itemAutoID'] = $item['itemAutoID'];
                        $itemledger_arr['itemSystemCode'] = $item['itemSystemCode'];
                        $itemledger_arr['itemDescription'] = $item['itemDescription'];
                        $itemledger_arr['defaultUOMID'] = $item['defaultUnitOfMeasureID'];
                        $itemledger_arr['defaultUOM'] = $item['defaultUnitOfMeasure'];
                        $itemledger_arr['transactionUOM'] = $master['UnitShortCode'];
                        $itemledger_arr['transactionUOMID'] = $master['uomID'];
                        $itemledger_arr['transactionQTY'] = $master['qty'];
                        $itemledger_arr['convertionRate'] = $conversionRate['conversionRateUOM'];
                        $itemledger_arr['currentStock'] = $item_arr['currentStock'];
                        $itemledger_arr['PLGLAutoID'] = $item['costGLAutoID'];
                        $itemledger_arr['PLSystemGLCode'] = $item['costSystemGLCode'];
                        $itemledger_arr['PLGLCode'] = $item['costGLCode'];
                        $itemledger_arr['PLDescription'] = $item['costDescription'];
                        $itemledger_arr['PLType'] = $item['costType'];
                        $itemledger_arr['BLGLAutoID'] = $item['assteGLAutoID'];
                        $itemledger_arr['BLSystemGLCode'] = $item['assteSystemGLCode'];
                        $itemledger_arr['BLGLCode'] = $item['assteGLCode'];
                        $itemledger_arr['BLDescription'] = $item['assteDescription'];
                        $itemledger_arr['BLType'] = $item['assteType'];
                        $itemledger_arr['transactionAmount'] = $detailSum['localWacAmountTotal'];
                        $itemledger_arr['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $itemledger_arr['transactionCurrency'] = $master['transactionCurrency'];
                        $itemledger_arr['transactionExchangeRate'] = $master['transactionExchangeRate'];
                        $itemledger_arr['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $itemledger_arr['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $itemledger_arr['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $itemledger_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $itemledger_arr['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr['companyLocalAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyLocalExchangeRate']), $itemledger_arr['companyLocalCurrencyDecimalPlaces']);
                        $itemledger_arr['companyLocalWacAmount'] = $item_arr['companyLocalWacAmount'];
                        $itemledger_arr['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $itemledger_arr['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $itemledger_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                        $itemledger_arr['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                        $itemledger_arr['companyReportingAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyReportingExchangeRate']), $itemledger_arr['companyReportingCurrencyDecimalPlaces']);
                        $itemledger_arr['companyReportingWacAmount'] = $item_arr['companyReportingWacAmount'];
                        $itemledger_arr['confirmedYN'] = $master['confirmedYN'];
                        $itemledger_arr['confirmedByEmpID'] = $master['confirmedUserID'];
                        $itemledger_arr['confirmedByName'] = $master['confirmedUserName'];
                        $itemledger_arr['confirmedDate'] = $master['confirmedDateTime'];
                        $itemledger_arr['companyID'] = $master['companyID'];
                        $itemledger_arr['createdUserGroup'] = $master['createdUserGroup'];
                        $itemledger_arr['createdPCID'] = $master['createdPCID'];
                        $itemledger_arr['createdUserID'] = $master['createdUserID'];
                        $itemledger_arr['createdDateTime'] = $master['createdDateTime'];
                        $itemledger_arr['createdUserName'] = $master['createdUserName'];
                        $itemledger_arr['modifiedPCID'] = $master['modifiedPCID'];
                        $itemledger_arr['modifiedUserID'] = $master['modifiedUserID'];
                        $itemledger_arr['modifiedDateTime'] = $master['modifiedDateTime'];
                        $itemledger_arr['modifiedUserName'] = $master['modifiedUserName'];

                        $this->db->update('srp_erp_itemmaster', $item_arr, "itemAutoID=$itemAutoID");
                        $this->db->insert('srp_erp_itemledger', $itemledger_arr);
                    }
                }
                /*Item exceed match for yield preparation*/
                $companyID = current_companyID();
                $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID =  '" . $master['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $master['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID = 0;

                if (!empty($exceededitems_master)) {
                    $this->load->library('sequence');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['documentDate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['yieldPreparationID'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['documentSystemCode'];
                    $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $exceededmatch['transactionCurrencyID'] = $master ['transactionCurrencyID'];
                    $exceededmatch['transactionCurrency'] = $master ['transactionCurrency'];
                    $exceededmatch['transactionExchangeRate'] = $master ['transactionExchangeRate'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['transactionCurrencyDecimalPlaces'];
                    $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                    $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                    $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                    $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                    $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                    $exceededmatch['FYBegin'] = $master ['FYBegin'];
                    $exceededmatch['FYEnd'] = $master ['FYEnd'];
                    $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                    $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                    $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];
                    $exceededmatch['createdUserGroup'] = $this->common_data['user_group'];
                    $exceededmatch['createdPCID'] = $this->common_data['current_pc'];
                    $exceededmatch['createdUserID'] = $this->common_data['current_userID'];
                    $exceededmatch['createdUserName'] = $this->common_data['current_user'];
                    $exceededmatch['createdDateTime'] = $this->common_data['current_date'];
                    $exceededmatch['documentSystemCode'] = $this->sequence->sequence_generator($exceededmatch['documentID']);
                    $exceedmaster=$this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                    $exceededMatchID = $this->db->insert_id();

                    if($exceedmaster){
                        $receivedQty = $master['qty'];
                        $item = fetch_item_data($master['itemAutoID']);
                        $uomDesc = $this->db->query("SELECT  UnitShortCode FROM srp_erp_unit_of_measure WHERE UnitID =  '" . $master['uomID'] . "' ")->row_array();
                        $conversion=conversionRateUOM($uomDesc['UnitShortCode'], $item['defaultUnitOfMeasure']);
                        $receivedQtyConverted = $master['qty'] / $conversion;

                        $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $master['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $master ['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
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
                                        $exceededmatchdetail['warehouseAutoID'] = $master['wareHouseAutoID'];
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
                                        $exceededmatchdetails['warehouseAutoID'] = $master['wareHouseAutoID'];
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

                            if (!empty($exceededitems_master)) {
                                exceed_double_entry($exceededMatchID);
                            }
                        }
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Yield Preparation Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Yield Preparation Saved Successfully.', $last_id);
            }
        }
    }

    function load_yieldPreparation()
    {
        $convertFormat = convert_date_format_sql();
        $result = $this->db->select('srp_erp_pos_menuyieldpreparation.*,CONCAT(yieldID,"-",yielduomID,"-",srp_erp_pos_menuyields.qty,"-",srp_erp_pos_menuyields.itemAutoID) as yieldMasterID,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') as documentDate,companyFinancePeriodID,companyFinanceYearID,warehouseAutoID', false)
            ->from('srp_erp_pos_menuyieldpreparation')
            ->join('srp_erp_pos_menuyields', 'srp_erp_pos_menuyieldpreparation.yieldMasterID = srp_erp_pos_menuyields.yieldID', 'left')
            ->where('srp_erp_pos_menuyieldpreparation.yieldPreparationID', $this->input->post('yieldPreparationID'))->get()->row_array();
        return $result;
    }

    function delete_yield_preparation()
    {
        $result = $this->db->query("SELECT * FROM srp_erp_pos_menuyieldpreparation WHERE yieldPreparationID = " . trim($this->input->post('yieldPreparationID') ?? ''))->row_array();
        if ($result["confirmedYN"]) {
            return array('e', 'You cant delete confirmed document');
        } else {
            $this->db->delete('srp_erp_pos_menuyieldpreparationdetails', array('yieldPreparationID' => trim($this->input->post('yieldPreparationID') ?? '')));
            $this->db->delete('srp_erp_pos_menuyieldpreparation', array('yieldPreparationID' => trim($this->input->post('yieldPreparationID') ?? '')));
            return array('s', 'Successfully deleted');
        }

    }

    function yield_preparation_print($yieldpreparationid)
    {

        $convertFormat = convert_date_format_sql();
        $this->db->select('srp_erp_pos_menuyieldpreparation.*,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') as documentDate,CONCAT(itemDescription," - ",itemSystemCode) as itemDescription,srp_erp_companyfinanceperiod.datefrom as datefrom,srp_erp_companyfinanceperiod.dateTo as dateTo,CONCAT(wareHouseCode," - ",wareHouseDescription," - ",wareHouseLocation) as warehouse,srp_erp_pos_menuyieldpreparation.qty as qty,srp_erp_pos_menuyieldpreparation.narration as narration,CONCAT(UnitShortCode," | ",UnitDes) as uom,CONCAT(CurrencyCode," | ",CurrencyName) as currency,DATE_FORMAT(confirmedDateTime,\'' . $convertFormat . '\') as confirmedDateTime,documentSystemCode');
        $this->db->join('srp_erp_pos_menuyields', 'srp_erp_pos_menuyieldpreparation.yieldMasterID = srp_erp_pos_menuyields.yieldID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID=srp_erp_pos_menuyieldpreparation.itemAutoID', 'left');
        $this->db->join('srp_erp_companyfinanceperiod', 'srp_erp_companyfinanceperiod.companyFinancePeriodID=srp_erp_pos_menuyieldpreparation.companyFinancePeriodID', 'left');
        $this->db->join('srp_erp_warehousemaster', 'srp_erp_warehousemaster.warehouseAutoID=srp_erp_pos_menuyieldpreparation.warehouseAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID=srp_erp_pos_menuyieldpreparation.uomID', 'left');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_pos_menuyieldpreparation.transactionCurrencyID', 'left');
        $this->db->where('srp_erp_pos_menuyieldpreparation.yieldPreparationID', $yieldpreparationid);
        $this->db->from('srp_erp_pos_menuyieldpreparation');
        $data['yield'] = $this->db->get()->row_array();

        $this->db->select('srp_erp_pos_menuyieldpreparationdetails.*,CONCAT(itemDescription," - ",itemSystemCode) as itemDescription,companyLocalWacAmount,CONCAT(uom.UnitShortCode," | ",uom.UnitDes) as unitofmeasure');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_pos_menuyieldpreparationdetails.uomID = srp_erp_unit_of_measure.UnitID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID=srp_erp_pos_menuyieldpreparationdetails.itemAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure uom', 'uom.UnitID=srp_erp_pos_menuyieldpreparationdetails.uomID', 'left');
        $this->db->where('srp_erp_pos_menuyieldpreparationdetails.companyID', current_companyID());
        $this->db->where('srp_erp_pos_menuyieldpreparationdetails.yieldPreparationID', $yieldpreparationid);
        $this->db->from('srp_erp_pos_menuyieldpreparationdetails');
        $data['yield_detail'] = $this->db->get()->result_array();

        return $data;
    }


}