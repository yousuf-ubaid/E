<?php

/*
-- =============================================
-- File Name : Report.php
-- Project Name : SME ERP
-- Module Name : Report
-- Create date : 15 - September 2016
-- Description : This file contains all the report module generation function.

-- REVISION HISTORY
-- =============================================*/

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Report extends ERP_Controller
{
    public $format;

    function __construct()
    {
        parent::__construct();
        $this->format = convert_date_format_sql();
        $this->load->model('Report_model');
        $this->load->helper('report');
        $this->load->helper('group_management');
    }

    function get_item_filter()/*item ledger,valuation,counting*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/inventory/report/erp_item_filter', $data);
    }

    function get_finance_filter() /*Trial balance*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/finance/report/erp_finance_filter', $data);
    }
    function get_finance_filter_new() /*Trial balance*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/finance/report/erp_finance_filter_new', $data);
    }

    function get_procurement_filter() /*PO List*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/procurement/report/erp_procurement_filter', $data);
    }

    function get_accounts_payable_filter() /*Vendor Ledger,Vendor Statement,Vendor Aging Summary,Vendor Aging Detail*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/accounts_payable/report/erp_accounts_payable_filter', $data);
    }

    function get_accounts_receivable_filter() /*Customer Ledger,Customer Statement,Customer Aging Summary,Customer Aging Detail*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_filter', $data);
    }

    function get_report_by_id()
    {
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque+
        $companyID = $this->common_data['company_data']['company_id'];
        switch ($this->input->post('reportID')) {
            case "ITM_LG": /*item ledger*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $tempAssigned = $this->db->query("SELECT TemplateMasterID from srp_erp_printtemplates where documentID = 'ITM_LG' AND companyID = {$companyID}")->row_array();
                    if(empty($tempAssigned)){
                        $data["output"] = $this->Report_model->get_item_ledger_report();
                    }else{

                        $data["output"] = $this->Report_model->get_item_ledger_report_new();
                        $data["subSubCategories"] = $this->Report_model->get_item_ledger_report_subsubcategory();
                    }
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "html";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_LG', $this->input->post('fieldNameChk'));
                    $printlink = print_template_pdf('ITM_LG', 'system/inventory/report/erp_item_ledger_report');
                    $this->load->view($printlink, $data);
                }
                break;
            case "INV_VAL": /*item valuation*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Extra Column', 'trim|required');
               if($fieldNameChk) {
                   if(in_array("companyLocalWacAmount", $fieldNameChk) && in_array("companyReportingWacAmount", $fieldNameChk)){
                       $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                   } else if(!in_array("companyLocalWacAmount", $fieldNameChk) && !in_array("companyReportingWacAmount", $fieldNameChk)){
                       $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                   }
               }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $tempAssigned = $this->db->query("SELECT TemplateMasterID from srp_erp_printtemplates where documentID = 'INV_VAL' AND companyID = {$companyID}")->row_array();
                    if(empty($tempAssigned)){
                        $data["output"] = $this->Report_model->get_item_valuation_summary_report();
                        $data["TotalAssetValue"] = $this->Report_model->get_item_valuation_summary_total_asset();
                    }else{
                        $data["output"] = $this->Report_model->get_item_valuation_summary_report_new();
                        $data["TotalAssetValue"] = $this->Report_model->get_item_valuation_summary_total_asset_new();
                        $data["subSubCategories"] = $this->Report_model->get_item_ledger_report_subsubcategory();
                    }
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_VAL', $this->input->post('fieldNameChk'));
                    $printlink = print_template_pdf('INV_VAL', 'system/inventory/report/erp_item_valuation_summary_report');
                    $this->load->view($printlink, $data);
                }
                break;

            case "ITM_CNT": /*item counting*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["isSubItemExist"] = $this->input->post('isSubItemRequired');
                    $data["output"] = $this->Report_model->get_item_counting_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_CNT', $this->input->post('fieldNameChk'));
                    //                    $this->load->view('system/inventory/report/erp_item_counting_report', $data);
                    $printlink = print_template_pdf('ITM_CNT', 'system/inventory/report/erp_item_counting_report');
                    $this->load->view($printlink, $data);
                }
                break;
            case "ITM_FM": /*item fast moving*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report Type', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_fast_moving_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["customerfilter"] = $this->Report_model->get_customer();
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_FM', $this->input->post('fieldNameChk'));
                    $this->load->view('system/inventory/report/erp_item_fast_moving_report', $data);
                }
                break;
            case "FIN_TB": /*Trial Balance*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $finaceYearYN = getPolicyValues('HFY','All');
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_tb_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = ($finaceYearYN == 1 ? $this->input->post('to'):$this->input->post('from'));
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 1) {

                        if($finaceYearYN == 1)
                        {
                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        }else{
                            $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                            $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");

                        }

                        $this->load->view('system/finance/report/erp_finance_tb_month_wise_report', $data);
                    } else if ($this->input->post('rptType') == 3) {
                        $data["retain"] = $this->Report_model->get_finance_tb_retain();
                        $this->load->view('system/finance/report/erp_finance_tb_ytd_report', $data);
                    }
                }
                break;
            case "FIN_IS": /*Income statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                if (($this->input->post('rptType') == 1)||($this->input->post('rptType') == 4)||($this->input->post('rptType') == 7) ||($this->input->post('rptType') == 8)) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                $template = $this->input->post('templates');

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $gl_category_arr = array();
                    $gl_details = $this->Report_model->get_finance_income_statement_report();

                    foreach($gl_details as $gls){
                        $gl_category_arr[$gls['GLAutoID']] = $gls;
                    }

                    $data["output"] = $gl_details;//$this->Report_model->get_finance_income_statement_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    $startdate = $this->input->post("from");
                    $to = $this->input->post("to");

                    $date_format_policy = date_format_policy();
                    $format_startdate = null;
                    $format_to = null;

                    $format_startdate = input_format_date($startdate, $date_format_policy);
                    $format_to = input_format_date($to, $date_format_policy);

                    if ($this->input->post('rptType') == 1) {

                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        
                        if($template){
                            $custom_category_details = getCustomCategoryForReport($template,$gl_category_arr); 

                            $data['output_custom'] = $custom_category_details;
                            // print_r(json_encode($custom_category)); exit;
                            // print_r(json_encode($gl_category_arr['3355'])); exit;
                            // print_r($data['output']); exit;

                            $this->load->view('system/finance/report/erp_finance_income_statement_custom_month_wise_report', $data);
                        }else{
                            $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_report', $data);
                        }
                        
                    } else if ($this->input->post('rptType') == 3) {
                        $this->load->view('system/finance/report/erp_finance_income_statement_ytd_report', $data);
                    } else if ($this->input->post('rptType') == 5) {
                        $this->load->view('system/finance/report/erp_finance_income_statement_ytd_budget_report', $data);
                    } else if ($this->input->post('rptType') == 4) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_budget_report', $data);
                    } else if ($this->input->post('rptType') == 7) {

                        $d2 = new DateTime($format_startdate); // date to
                        $d1 = new DateTime($format_to);// date from
                        $diff = $d2->diff($d1);
                        $datecalculation = $diff->y;
                        if ($datecalculation > 0)
                        {
                            echo '
                <br>
                    <div class="alert alert-warning" role="alert">
                           Date From And Date To Range Should be within 1 Year for selected Report Type - YTD - LYD
                     </div>';
                        } else
                            {

                        $this->load->view('system/finance/report/erp_finance_income_statement_ytd_lyd_report', $data);
                        }
                    }
                    else if ($this->input->post('rptType') == 8) {
                        $d2 = new DateTime($format_startdate); // date to
                        $d1 = new DateTime($format_to);// date from
                        $diff = $d2->diff($d1);
                        $datecalculation = $diff->y;
                        if ($datecalculation > 0)
                        {
                            echo '
                <br>
                    <div class="alert alert-warning" role="alert">
                           Date From And Date To Range Should be within 1 Year for selected Report Type - Month wise LYD
                     </div>';
                        }else
                        {
                            $fromdateformatted = format_date($this->input->post("from"));
                            $todateformatted = format_date($this->input->post("to"));
                            $datefrom = strtotime($fromdateformatted.' -1 year');
                            $dateto = strtotime($todateformatted.' -1 year');
                            $newfromdate = date('Y-m-d', $datefrom);
                            $newtodate = date('Y-m-d', $dateto);
                            $data["monthslytd"] = get_month_list_from_date(format_date($newfromdate), format_date($newtodate), "Y-m", "1 month"); /*calculate months lytd*/

                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                           $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_budget_ytdltd_report', $data);
                        }

                    }
                    else if ($this->input->post('rptType') == 9) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $segments_arr = $this->input->post('segment');
                        $segments = implode(', ', $segments_arr);
                        
                        $data['rptType'] = $this->input->post('rptType');
                        $data['segment'] = $this->db->query("SELECT segmentID, segmentCode FROM srp_erp_segment WHERE segmentID IN ({$segments})")->result_array();
                        $this->load->view('system/finance/report/erp_finance_income_statement_segment_budget_report', $data);
                    }
                }
                break;
            case "FIN_BS": /*Balance sheet*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $finaceYearYN = getPolicyValues('HFY','All');
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_balance_sheet_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = ($finaceYearYN == 1 ? $this->input->post('to'):$this->input->post('from'));
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BS', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 1) {
                        $finaceYearYN = getPolicyValues('HFY','All');
                        if($finaceYearYN == 1)
                        {
                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        }else{

                            $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                            $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        }
                        $this->load->view('system/finance/report/erp_finance_balance_sheet_month_wise_report', $data);
                    } else if ($this->input->post('rptType') == 3) {
                        $this->load->view('system/finance/report/erp_finance_balance_sheet_ytd_report', $data);
                    }
                }
                break;
            case "FIN_GL": /*General Ledger */
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('glCodeTo[]', 'GL Code', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $educalTempAssigned = $this->db->query("SELECT srp_erp_printtemplates.TemplateMasterID FROM srp_erp_printtemplates INNER JOIN srp_erp_printtemplatemaster ON srp_erp_printtemplatemaster.TemplateMasterID = srp_erp_printtemplates.TemplateMasterID WHERE srp_erp_printtemplates.documentID = 'FIN_GL' AND srp_erp_printtemplates.companyID = {$companyID} AND srp_erp_printtemplatemaster.TempPageNameLink='system/finance/report/erp_finance_general_ledger_educal'")->row_array();
                    $donorTempAssigned =  $this->db->query("SELECT srp_erp_printtemplates.TemplateMasterID FROM srp_erp_printtemplates INNER JOIN srp_erp_printtemplatemaster ON srp_erp_printtemplatemaster.TemplateMasterID = srp_erp_printtemplates.TemplateMasterID WHERE srp_erp_printtemplates.documentID = 'FIN_GL' AND srp_erp_printtemplates.companyID = {$companyID} AND srp_erp_printtemplatemaster.TempPageNameLink='system/finance/report/erp_finance_general_ledger_report_don'")->row_array();
                    if(!empty($educalTempAssigned)){
                        $data["output"] = $this->Report_model->get_educal_general_ledger_report();
                    }elseif($donorTempAssigned){
                        $data["output"] = $this->Report_model->get_finance_general_ledger_report_donor();
                    }else{
                        $data["output"] = $this->Report_model->get_finance_general_ledger_report();
                    }
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_GL', $this->input->post('fieldNameChk'));

                    $printlink = print_template_pdf('FIN_GL', 'system/finance/report/erp_finance_general_ledger_report');
                    $this->load->view($printlink, $data);
                }
                break;
            case "FIN_BD": /*Budget*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_budget_report();

                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BD', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 5) {
                        $this->load->view('system/finance/report/erp_finance_ytd_budget_report', $data);
                    } else if ($this->input->post('rptType') == 4) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $this->load->view('system/finance/report/erp_finance_budget_month_wise_report', $data);
                    }
                }
                break;


            case "PROC_POL": /*PO List*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('status', 'Status', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_procurement_purchase_order_list_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["status"] = $this->input->post('status');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('PROC_POL', $this->input->post('fieldNameChk'));
                    $this->load->view('system/procurement/report/erp_procurement_purchase_order_list_report', $data);
                }
                break;
            case "INV_UBG": /*Unbilled GRV*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_inventory_unbilled_grv_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBG', $this->input->post('fieldNameChk'));
                    $this->load->view('system/inventory/report/erp_inventory_unbilled_grv_report', $data);
                }
                break;
            case "INV_UBI": /*Un billed invoice*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_inventory_un_billed_invoice_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBI', $this->input->post('fieldNameChk'));
                    //echo '<pre>'; print_r($data["fieldNameDetails"]); echo '</pre>';
                    $this->load->view('system/sales/un-billed-invoice-report', $data);
                }
                break;
            case "AP_VL": /*Vendor Ledger*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_report_postdatedcheques();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                        //$this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data);
                        $printlink = print_template_pdf('AP_VL', 'system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report_postdated');
                        $this->load->view($printlink, $data);
                    } else {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                        //$this->load->view('syste  m/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data);
                        $printlink = print_template_pdf('AP_VL', 'system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report');
                        $this->load->view($printlink, $data);
                    }
                }
                break;
            case "AP_VS": /*Vendor Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report_postdatedcheque();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report_postdated', $data);
                    } else {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report', $data);
                    }
                }
                break;
            case "AP_VAS": /*Vendor Aging Summary*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report_postdated($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report_postdated_cheque', $data);
                    } else {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report', $data);
                    }
                }
                break;
            case "AP_VAD": /*Vendor Aging Detail*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) { /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_detail_report_postdated($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_detail_report_postdated', $data);
                    } else {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_detail_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_detail_report', $data);
                    }
                }
                break;
            case "AR_CL": /*Customer Ledger*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_report_postdated_cheques();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                        $printlink = print_template_pdf('AR_CL', 'system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report_postdated_cheques');
                        $this->load->view($printlink, $data);
                    } else {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                        $printlink = print_template_pdf('AR_CL', 'system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report');
                        $this->load->view($printlink, $data);
                    }


                    //$this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report', $data);

                }
                break;
            case "AR_CS": /*Customer Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');

                $key = array_search("seNumber", $fieldNameChk); // get key of seNumber in an array and remove it
                
                if($key){
                    unset($fieldNameChk[$key]);
                }
                
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                // after currency validation get full array
                $fieldNameChk = $this->input->post('fieldNameChk');
                
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report_postdated();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report_postdated_cheques', $data);
                    } else {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report', $data);
                    }

                }
                break;
            case "AR_CAS": /*Customer Aging Summary*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');

                $key = array_search("seNumber", $fieldNameChk); // get key of seNumber in an array and remove it
                
                if($key){
                    unset($fieldNameChk[$key]);
                }

                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }

                // after currency validation get full array
                $fieldNameChk = $this->input->post('fieldNameChk');

                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_report_pdc($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report_pdc', $data);
                    } else {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report', $data);
                    }
                }
                break;
            case "AR_CAD": /*Customer Aging Detail*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');

                $key = array_search("seNumber", $fieldNameChk); // get key of seNumber in an array and remove it
                
                if($key){
                    unset($fieldNameChk[$key]);
                }

                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }

                $fieldNameChk = $this->input->post('fieldNameChk');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_report_postdated($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report_pdc', $data);
                    }else
                    {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report', $data);
                    }
                }
                break;
            case "INV_IIQ": /*Item Inquiry*/
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_inquiry_report();
                    $data["warehouse"] = load_location_drop();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_IIQ', $this->input->post('fieldNameChk'));
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_report', $data);
                }
                break;

            case "INV_IBSO": /*Item Inquiry*/
                //$fieldNameChk1 = $this->input->post("fieldNameChk1");
                $typeAs = $this->input->post('fieldNameChk1');
                $fieldNameChk = $this->input->post("fieldNameChk");
                $type = '';
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if($typeAs == 'itembelowstock')
                {
                    $type = 'Below Minimum Stock';
                }else if($typeAs== 'itembelowro')
                {
                    $type = 'Item Below ROL';
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_below_stock_ro_report($typeAs,$fieldNameChk);
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $fieldNameChk;
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_IBSO', $fieldNameChk);
                    $data["type_filter"] =$type;
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/item_below_stock', $data);
                }
                break;

            case "AR_CSR": /*Customer Statement Rebate*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report_postdated_rebate();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_outstanding_report_postdated_cheques', $data);
                    } else {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report_rebate();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_outstanding_report', $data);
                    }


                }
                break;
            case "INV_B_CNT": /*item counting*/
                    $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                    $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                    $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $error_message = validation_errors();
                        echo warning_message($error_message);
                    } else {
                        $data = array();
                        $data["isSubItemExist"] = $this->input->post('isSubItemRequired');
                        $data["output"] = $this->Report_model->get_item_batch_counting_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["warehouse"] = $this->Report_model->get_warehouse();
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_B_CNT', $this->input->post('fieldNameChk'));
                        //$this->load->view('system/inventory/report/erp_item_counting_report', $data);
                        $printlink = print_template_pdf('INV_B_CNT', 'system/inventory/report/erp_item_batch_counting_report');
                        $this->load->view($printlink, $data);
                    }
                    break;
            }
    }

 function get_report_by_id_new()
    {
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque+
        $companyID = $this->common_data['company_data']['company_id'];
        switch ($this->input->post('reportID')) {
            case "ITM_LG": /*item ledger*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $tempAssigned = $this->db->query("SELECT TemplateMasterID from srp_erp_printtemplates where documentID = 'ITM_LG' AND companyID = {$companyID}")->row_array();
                    if(empty($tempAssigned)){
                        $data["output"] = $this->Report_model->get_item_ledger_report();
                    }else{

                        $data["output"] = $this->Report_model->get_item_ledger_report_new();
                        $data["subSubCategories"] = $this->Report_model->get_item_ledger_report_subsubcategory();
                    }
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "html";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_LG', $this->input->post('fieldNameChk'));
                //$this->load->view('system/inventory/report/erp_item_ledger_report', $data);
                    $printlink = print_template_pdf('ITM_LG', 'system/inventory/report/erp_item_ledger_report');
                    $this->load->view($printlink, $data);
                }
                break;
            case "INV_VAL": /*item valuation*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Extra Column', 'trim|required');
               if($fieldNameChk) {
                   if(in_array("companyLocalWacAmount", $fieldNameChk) && in_array("companyReportingWacAmount", $fieldNameChk)){
                       $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                   } else if(!in_array("companyLocalWacAmount", $fieldNameChk) && !in_array("companyReportingWacAmount", $fieldNameChk)){
                       $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                   }

                      //$this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
               }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $tempAssigned = $this->db->query("SELECT TemplateMasterID from srp_erp_printtemplates where documentID = 'INV_VAL' AND companyID = {$companyID}")->row_array();
                    if(empty($tempAssigned)){
                        $data["output"] = $this->Report_model->get_item_valuation_summary_report();
                        $data["TotalAssetValue"] = $this->Report_model->get_item_valuation_summary_total_asset();
                    }else{
                        $data["output"] = $this->Report_model->get_item_valuation_summary_report_new();
                        $data["TotalAssetValue"] = $this->Report_model->get_item_valuation_summary_total_asset_new();
                        $data["subSubCategories"] = $this->Report_model->get_item_ledger_report_subsubcategory();
                    }
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_VAL', $this->input->post('fieldNameChk'));
                    //                    $this->load->view('system/inventory/report/erp_item_valuation_summary_report', $data);
                    $printlink = print_template_pdf('INV_VAL', 'system/inventory/report/erp_item_valuation_summary_report');
                    $this->load->view($printlink, $data);
                }
                break;

            case "ITM_CNT": /*item counting*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["isSubItemExist"] = $this->input->post('isSubItemRequired');
                    $data["output"] = $this->Report_model->get_item_counting_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_CNT', $this->input->post('fieldNameChk'));
                    //                    $this->load->view('system/inventory/report/erp_item_counting_report', $data);
                    $printlink = print_template_pdf('ITM_CNT', 'system/inventory/report/erp_item_counting_report');
                    $this->load->view($printlink, $data);
                }
                break;
            case "ITM_FM": /*item fast moving*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report Type', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_fast_moving_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["customerfilter"] = $this->Report_model->get_customer();
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_FM', $this->input->post('fieldNameChk'));
                    $this->load->view('system/inventory/report/erp_item_fast_moving_report', $data);
                }
                break;
            case "FIN_TB": /*Trial Balance*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $finaceYearYN = getPolicyValues('HFY','All');
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_tb_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = ($finaceYearYN == 1 ? $this->input->post('to'):$this->input->post('from'));
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 1) {

                        if($finaceYearYN == 1)
                        {
                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        }else{
                            $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                            $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");

                        }

                        $this->load->view('system/finance/report/erp_finance_tb_month_wise_report', $data);
                    } else if ($this->input->post('rptType') == 3) {
                        $data["retain"] = $this->Report_model->get_finance_tb_retain();
                        $this->load->view('system/finance/report/erp_finance_tb_ytd_report', $data);
                    }
                }
                break;
            case "FIN_IS": /*Income statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                if (($this->input->post('rptType') == 1)||($this->input->post('rptType') == 4)||($this->input->post('rptType') == 7) ||($this->input->post('rptType') == 8)) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                $template = $this->input->post('templates');
                $reporttemplate = $this->input->post('TemplateID');

               // echo  $template;
               // exit();

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $gl_category_arr = array();
                    $gl_details = $this->Report_model->get_finance_income_statement_report();

                    foreach($gl_details as $gls){
                        $gl_category_arr[$gls['GLAutoID']] = $gls;
                    }

                    $data["output"] = $gl_details;//$this->Report_model->get_finance_income_statement_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    $startdate = $this->input->post("from");
                    $to = $this->input->post("to");

                    $date_format_policy = date_format_policy();
                    $format_startdate = null;
                    $format_to = null;

                    $format_startdate = input_format_date($startdate, $date_format_policy);
                    $format_to = input_format_date($to, $date_format_policy);
                      if($reporttemplate=='0')
                      {
                    if ($this->input->post('rptType') == 1) {

                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        
                        if($template){
                            $custom_category_details = getCustomCategoryForReport($template,$gl_category_arr); 

                            $data['output_custom'] = $custom_category_details;

                            $this->load->view('system/finance/report/erp_finance_income_statement_custom_month_wise_report', $data);
                        }else{
                            $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_report', $data);
                        }
                        
                    } else if ($this->input->post('rptType') == 3) {
                        $this->load->view('system/finance/report/erp_finance_income_statement_ytd_report', $data);
                    } else if ($this->input->post('rptType') == 5) {
                        $this->load->view('system/finance/report/erp_finance_income_statement_ytd_budget_report', $data);
                    } else if ($this->input->post('rptType') == 4) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_budget_report', $data);
                    } else if ($this->input->post('rptType') == 7) {

                        $d2 = new DateTime($format_startdate); // date to
                        $d1 = new DateTime($format_to);// date from
                        $diff = $d2->diff($d1);
                        $datecalculation = $diff->y;
                        if ($datecalculation > 0)
                        {
                            echo '
                <br>
                    <div class="alert alert-warning" role="alert">
                           Date From And Date To Range Should be within 1 Year for selected Report Type - YTD - LYD
                     </div>';
                        } else
                            {

                        $this->load->view('system/finance/report/erp_finance_income_statement_ytd_lyd_report', $data);
                        }
                    }
                    else if ($this->input->post('rptType') == 8) {
                        $d2 = new DateTime($format_startdate); // date to
                        $d1 = new DateTime($format_to);// date from
                        $diff = $d2->diff($d1);
                        $datecalculation = $diff->y;
                        if ($datecalculation > 0)
                        {
                            echo '
                            <br>
                    <div class="alert alert-warning" role="alert">
                           Date From And Date To Range Should be within 1 Year for selected Report Type - Month wise LYD
                     </div>';
                        }else
                        {
                            $fromdateformatted = format_date($this->input->post("from"));
                            $todateformatted = format_date($this->input->post("to"));
                            $datefrom = strtotime($fromdateformatted.' -1 year');
                            $dateto = strtotime($todateformatted.' -1 year');
                            $newfromdate = date('Y-m-d', $datefrom);
                            $newtodate = date('Y-m-d', $dateto);
                            $data["monthslytd"] = get_month_list_from_date(format_date($newfromdate), format_date($newtodate), "Y-m", "1 month"); /*calculate months lytd*/

                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                           $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_budget_ytdltd_report', $data);
                        }

                    }
                    else if ($this->input->post('rptType') == 9) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $segments_arr = $this->input->post('segment');
                        $segments = implode(', ', $segments_arr);
                        
                        $data['rptType'] = $this->input->post('rptType');
                        $data['segment'] = $this->db->query("SELECT segmentID, segmentCode FROM srp_erp_segment WHERE segmentID IN ({$segments})")->result_array();
                        $this->load->view('system/finance/report/erp_finance_income_statement_segment_budget_report', $data);
                    }
                }
                else
                {
                    $data["inv_company"]=$companyID;
                    $data["TemplateId"]=$reporttemplate; 
                    $gl_details = $this->Report_model->get_finance_income_statement_report();
                    if ($this->input->post('rptType') == 1) {
                    
                    $data["output"]=$gl_details;
                    $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                    //$periodYear=date('Y');
                    //$tBody = load_template_fm_statement_report($periodYear, $reporttemplate);
                    //print_R($tBody);
                   $this->load->view('system/finance/report/erp_finance_income_statement_companytemplate_wise_report', $data);
                    }
                    else if ($this->input->post('rptType') == 3) {
                       
                        $this->load->view('system/finance/report/erp_finance_income_statement_template_ytd_report', $data);
                    }
                    else if ($this->input->post('rptType') == 4) {
                     
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $data["output"]=$gl_details;
                        $this->load->view('system/finance/report/erp_finance_income_statement_template_month_wise_budget_report', $data);
                    }
                    else if ($this->input->post('rptType') == 9) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $segments_arr = $this->input->post('segment');
                        $segments = implode(', ', $segments_arr);
                        
                        $data['rptType'] = $this->input->post('rptType');
                        $data['segment'] = $this->db->query("SELECT segmentID, segmentCode FROM srp_erp_segment WHERE segmentID IN ({$segments})")->result_array();
                        $this->load->view('system/finance/report/erp_finance_income_statement_template_segment_budget_report', $data);
                    }
                    else if ($this->input->post('rptType') == 5) {
                        $this->load->view('system/finance/report/erp_finance_income_statement_template_ytd_budget_report', $data);
                    }
                    else if ($this->input->post('rptType') == 7) {

                        $d2 = new DateTime($format_startdate); // date to
                        $d1 = new DateTime($format_to);// date from
                        $diff = $d2->diff($d1);
                        $datecalculation = $diff->y;
                        if ($datecalculation > 0)
                        {
                            echo '
                <br>
                    <div class="alert alert-warning" role="alert">
                           Date From And Date To Range Should be within 1 Year for selected Report Type - YTD - LYD
                     </div>';
                        } else
                            {

                        $this->load->view('system/finance/report/erp_finance_income_statement_template_ytd_lyd_report', $data);
                        }
                    }

                    else if ($this->input->post('rptType') == 8) {
                        $d2 = new DateTime($format_startdate); // date to
                        $d1 = new DateTime($format_to);// date from
                        $diff = $d2->diff($d1);
                        $datecalculation = $diff->y;
                        if ($datecalculation > 0)
                        {
                            echo '
                            <br>
                    <div class="alert alert-warning" role="alert">
                           Date From And Date To Range Should be within 1 Year for selected Report Type - Month wise LYD
                     </div>';
                        }else
                        {
                            $fromdateformatted = format_date($this->input->post("from"));
                            $todateformatted = format_date($this->input->post("to"));
                            $datefrom = strtotime($fromdateformatted.' -1 year');
                            $dateto = strtotime($todateformatted.' -1 year');
                            $newfromdate = date('Y-m-d', $datefrom);
                            $newtodate = date('Y-m-d', $dateto);
                            $data["monthslytd"] = get_month_list_from_date(format_date($newfromdate), format_date($newtodate), "Y-m", "1 month"); /*calculate months lytd*/

                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                           $this->load->view('system/finance/report/erp_finance_income_statement_template_month_wise_budget_ytdltd_report', $data);
                        }

                    }
                    


                }



                }
                break;
            case "FIN_BS": /*Balance sheet*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                $reporttemplate = $this->input->post('TemplateID');


                if($reporttemplate=='0')
                {
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $finaceYearYN = getPolicyValues('HFY','All');
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_balance_sheet_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = ($finaceYearYN == 1 ? $this->input->post('to'):$this->input->post('from'));
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BS', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 1) {
                        $finaceYearYN = getPolicyValues('HFY','All');
                        if($finaceYearYN == 1)
                        {
                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        }else{

                            $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                            $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        }
                        $this->load->view('system/finance/report/erp_finance_balance_sheet_month_wise_report', $data);
                    } else if ($this->input->post('rptType') == 3) {
                        $this->load->view('system/finance/report/erp_finance_balance_sheet_ytd_report', $data);
                    }
                    }
                     }
                     else{
                       // $data["reportname"]='FIN_BS';
                        $data["inv_company"]=$companyID;
                        $data["TemplateId"]=$reporttemplate; 
                        $fromdate =$this->input->post('from');
                        $data["fromdatenew"]=date("Y-m-d",strtotime($fromdate));
                        $year = date('Y',strtotime($data["fromdatenew"]));
                        $pyear=$year-1;
                        $monthl= date('m',strtotime($data["fromdatenew"]));
                        
                        $monthlp=$monthl+1;
                        $monthlp='0'.$monthlp;
                       
                        $data["todatenew"]=$pyear.'-'.$monthlp.'-'.'01';
                      
                        $data["type"] = "html";
                       
                        $finaceYearYN = getPolicyValues('HFY','All');
                        $data["from"] = ($finaceYearYN == 1 ? $this->input->post('to'):$this->input->post('from'));
                        $gl_details = $this->Report_model->get_finance_balance_sheet_report();
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["output"]=$gl_details;
                            $gl=0;
                        foreach($gl_details as $gls){
                            $gl_category_arr[$gls['GLAutoID']] = $gls;
                            $gl++;
                        }
                        $data["glcount"]=$gl;
                        //print_R($data["glcount"]);
                        //exit();
                        
                        if($finaceYearYN == 1)
                        {
                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        }else{

                            $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                            $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        }
                         
                        //$periodYear=date('Y');
                        //$tBody = load_template_fm_statement_report($periodYear, $reporttemplate);
                        //print_R($tBody);
                        if ($this->input->post('rptType') == 1) {
                        
                       $this->load->view('system/finance/report/erp_finance_balance_sheet_template_wise_report', $data);

                        }
                        else if ($this->input->post('rptType') == 3) {
                            $this->load->view('system/finance/report/erp_finance_balance_sheet_template_ytd_report', $data);
                        }

                     }
                break;
            case "FIN_GL": /*General Ledger */
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('glCodeTo[]', 'GL Code', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $educalTempAssigned = $this->db->query("SELECT srp_erp_printtemplates.TemplateMasterID FROM srp_erp_printtemplates INNER JOIN srp_erp_printtemplatemaster ON srp_erp_printtemplatemaster.TemplateMasterID = srp_erp_printtemplates.TemplateMasterID WHERE srp_erp_printtemplates.documentID = 'FIN_GL' AND srp_erp_printtemplates.companyID = {$companyID} AND srp_erp_printtemplatemaster.TempPageNameLink='system/finance/report/erp_finance_general_ledger_educal'")->row_array();
                    $donorTempAssigned =  $this->db->query("SELECT srp_erp_printtemplates.TemplateMasterID FROM srp_erp_printtemplates INNER JOIN srp_erp_printtemplatemaster ON srp_erp_printtemplatemaster.TemplateMasterID = srp_erp_printtemplates.TemplateMasterID WHERE srp_erp_printtemplates.documentID = 'FIN_GL' AND srp_erp_printtemplates.companyID = {$companyID} AND srp_erp_printtemplatemaster.TempPageNameLink='system/finance/report/erp_finance_general_ledger_report_don'")->row_array();
                    if(!empty($educalTempAssigned)){
                        $data["output"] = $this->Report_model->get_educal_general_ledger_report();
                    }elseif($donorTempAssigned){
                        $data["output"] = $this->Report_model->get_finance_general_ledger_report_donor();
                    }else{
                        $data["output"] = $this->Report_model->get_finance_general_ledger_report();
                    }
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_GL', $this->input->post('fieldNameChk'));
                    //$this->load->view('system/finance/report/erp_finance_general_ledger_report', $data);
                    //$this->load->view('system/finance/report/erp_finance_general_ledger_cd_report', $data);
                    $printlink = print_template_pdf('FIN_GL', 'system/finance/report/erp_finance_general_ledger_report');
                    $this->load->view($printlink, $data);
                }
                break;
            case "FIN_BD": /*Budget*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_budget_report();

                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BD', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 5) {
                        $this->load->view('system/finance/report/erp_finance_ytd_budget_report', $data);
                    } else if ($this->input->post('rptType') == 4) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $this->load->view('system/finance/report/erp_finance_budget_month_wise_report', $data);
                    }
                }
                break;


            case "PROC_POL": /*PO List*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('status', 'Status', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_procurement_purchase_order_list_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["status"] = $this->input->post('status');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('PROC_POL', $this->input->post('fieldNameChk'));
                    $this->load->view('system/procurement/report/erp_procurement_purchase_order_list_report', $data);
                }
                break;
            case "INV_UBG": /*Unbilled GRV*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_inventory_unbilled_grv_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBG', $this->input->post('fieldNameChk'));
                    $this->load->view('system/inventory/report/erp_inventory_unbilled_grv_report', $data);
                }
                break;
            case "INV_UBI": /*Un billed invoice*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_inventory_un_billed_invoice_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBI', $this->input->post('fieldNameChk'));
                    //echo '<pre>'; print_r($data["fieldNameDetails"]); echo '</pre>';
                    $this->load->view('system/sales/un-billed-invoice-report', $data);
                }
                break;
            case "AP_VL": /*Vendor Ledger*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_report_postdatedcheques();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                        //$this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data);
                        $printlink = print_template_pdf('AP_VL', 'system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report_postdated');
                        $this->load->view($printlink, $data);
                    } else {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                        //$this->load->view('syste  m/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data);
                        $printlink = print_template_pdf('AP_VL', 'system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report');
                        $this->load->view($printlink, $data);
                    }
                }
                break;
            case "AP_VS": /*Vendor Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report_postdatedcheque();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report_postdated', $data);
                    } else {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report', $data);
                    }
                }
                break;
            case "AP_VAS": /*Vendor Aging Summary*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report_postdated($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report_postdated_cheque', $data);
                    } else {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report', $data);
                    }
                }
                break;
            case "AP_VAD": /*Vendor Aging Detail*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) { /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_detail_report_postdated($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_detail_report_postdated', $data);
                    } else {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_detail_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_detail_report', $data);
                    }
                }
                break;
            case "AR_CL": /*Customer Ledger*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_report_postdated_cheques();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                        $printlink = print_template_pdf('AR_CL', 'system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report_postdated_cheques');
                        $this->load->view($printlink, $data);
                    } else {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                        $printlink = print_template_pdf('AR_CL', 'system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report');
                        $this->load->view($printlink, $data);
                    }


                    //$this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report', $data);

                }
                break;
            case "AR_CS": /*Customer Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report_postdated();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report_postdated_cheques', $data);
                    } else {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report', $data);
                    }


                }
                break;
            case "AR_CAS": /*Customer Aging Summary*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_report_pdc($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report_pdc', $data);
                    } else {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report', $data);
                    }
                }
                break;
            case "AR_CAD": /*Customer Aging Detail*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_report_postdated($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report_pdc', $data);
                    }else
                    {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report', $data);
                    }
                }
                break;
            case "INV_IIQ": /*Item Inquiry*/
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_inquiry_report();
                    $data["warehouse"] = load_location_drop();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_IIQ', $this->input->post('fieldNameChk'));
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_report', $data);
                }
                break;

            case "INV_IBSO": /*Item Inquiry*/
                //$fieldNameChk1 = $this->input->post("fieldNameChk1");
                $typeAs = $this->input->post('fieldNameChk1');
                $fieldNameChk = $this->input->post("fieldNameChk");
                $type = '';
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if($typeAs == 'itembelowstock')
                {
                    $type = 'Below Minimum Stock';
                }else if($typeAs== 'itembelowro')
                {
                    $type = 'Item Below ROL';
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_below_stock_ro_report($typeAs,$fieldNameChk);
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $fieldNameChk;
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_IBSO', $fieldNameChk);
                    $data["type_filter"] =$type;
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/item_below_stock', $data);
                }
                break;

            case "AR_CSR": /*Customer Statement Rebate*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report_postdated_rebate();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_outstanding_report_postdated_cheques', $data);
                    } else {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report_rebate();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_outstanding_report', $data);
                    }


                }
                break;
            case "INV_B_CNT": /*item counting*/
                    $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                    $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                    $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $error_message = validation_errors();
                        echo warning_message($error_message);
                    } else {
                        $data = array();
                        $data["isSubItemExist"] = $this->input->post('isSubItemRequired');
                        $data["output"] = $this->Report_model->get_item_batch_counting_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["warehouse"] = $this->Report_model->get_warehouse();
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_B_CNT', $this->input->post('fieldNameChk'));
                        //$this->load->view('system/inventory/report/erp_item_counting_report', $data);
                        $printlink = print_template_pdf('INV_B_CNT', 'system/inventory/report/erp_item_batch_counting_report');
                        $this->load->view($printlink, $data);
                    }
                    break;
            }
    }






    function get_group_report_by_id()
    {
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque+
        switch ($this->input->post('reportID')) {
            case "PROC_POL": /*PO List*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('status', 'Status', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("SUPP"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_procurement_purchase_order_list_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["status"] = $this->input->post('status');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('PROC_POL', $this->input->post('fieldNameChk'));
                        $this->load->view('system/procurement/report/erp_procurement_purchase_order_list_report', $data);
                    }
                }
                break;
            case "ITM_LG": /*item ledger*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("ITM","WH"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_item_ledger_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["warehouse"] = $this->Report_model->get_group_warehouse();
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_LG', $this->input->post('fieldNameChk'));
                        $this->load->view('system/inventory/report/erp_item_ledger_report', $data);
                    }
                }
                break;
            case "INV_VAL": /*item valuation*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("ITM","WH"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_item_valuation_summary_group_report();
                        $data["TotalAssetValue"] = $this->Report_model->get_item_valuation_summary_total_asset_group();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["warehouse"] = $this->Report_model->get_group_warehouse();
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_VAL', $this->input->post('fieldNameChk'));
                        $this->load->view('system/inventory/report/erp_item_valuation_summary_report', $data);
                    }
                }
                break;
            case "ITM_CNT": /*item counting*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("ITM","WH"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["isSubItemExist"] = $this->input->post('isSubItemRequired');
                        $data["output"] = $this->Report_model->get_item_counting_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["warehouse"] = $this->Report_model->get_group_warehouse();
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_CNT', $this->input->post('fieldNameChk'));
                        $this->load->view('system/inventory/report/erp_item_counting_report', $data);
                    }
                }
                break;
            case "INV_UBG": /*Unbilled GRV*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("SUPP"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_inventory_unbilled_grv_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBG', $this->input->post('fieldNameChk'));
                        $this->load->view('system/inventory/report/erp_inventory_unbilled_grv_report', $data);
                    }
                }
                break;
            case "AR_CL": /*Customer Ledger*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","CUST"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                       
                        $data = array();
                        if ($PostDatedChequeManagement == 1) {
                            $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_group_report_postdated_cheques();
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["to"] = $this->input->post('to');
                            $data["type"] = "html";
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                            $printlink = print_template_pdf('AR_CL', 'system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report_postdated_cheques');
                            $this->load->view($printlink, $data);
                        }else{

                            $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_group_report();
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["to"] = $this->input->post('to');
                            $data["type"] = "html";
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
    //                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report', $data);
                            $printlink = print_group_template_pdf('AR_CL', 'system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report');
                            $this->load->view($printlink, $data);
                        }
                    }
                }
                break;
            case "AR_CS": /*Customer Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","CUST"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        if ($PostDatedChequeManagement == 1) {
                            $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_group_report_postdated();
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["type"] = "html";
                            $data["template"] = "default";
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                            $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report_postdated_cheques', $data);
                        }else{

                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report', $data);
                        }
                    }
                }
                break;
            case "AP_VL": /*Vendor Ledger*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        if ($PostDatedChequeManagement == 1) {
                            $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_report_postdatedcheques_group();
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["to"] = $this->input->post('to');
                            $data["type"] = "html";
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                            //$this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data);
                            $printlink = print_template_pdf('AP_VL', 'system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report_postdated');
                            $this->load->view($printlink, $data);
                        }else{
                            $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_group_report();
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["to"] = $this->input->post('to');
                            $data["type"] = "html";
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
    //                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data);
                            $printlink = print_group_template_pdf('AP_VL', 'system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report');
                            $this->load->view($printlink, $data);
                        }
                    }
                }
                break;
            case "AP_VS": /*Vendor Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        if ($PostDatedChequeManagement == 1) {
                            $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report_postdatedcheque_group();
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["type"] = "html";
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                            $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report_postdated', $data);
                        }else{
                            $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_group_report();
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["type"] = "html";
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                            $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report', $data);
                        }
                        
                    }
                }
                break;
            case "FIN_TB": /*Trial Balance*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_group_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP","CUST","SEG"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_finance_tb_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                        if ($this->input->post('rptType') == 1) {
                            $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                            $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                            $this->load->view('system/finance/report/erp_finance_tb_month_wise_report', $data);
                        } else if ($this->input->post('rptType') == 3) {
                            $data["retain"] = $this->Report_model->get_finance_tb_group_retain();
                            $this->load->view('system/finance/report/erp_finance_tb_ytd_report', $data);
                        }
                    }
                }
                break;
            case "FIN_GL": /*General Ledger */
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('glCodeTo[]', 'GL Code', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP","CUST","SEG"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_finance_general_ledger_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_GL', $this->input->post('fieldNameChk'));
//                        $this->load->view('system/finance/report/erp_finance_general_ledger_report', $data);
                        $printlink = print_group_template_pdf('FIN_GL', 'system/finance/report/erp_finance_general_ledger_report');
                        $this->load->view($printlink, $data);
                    }
                }
                break;
            case "FIN_BS": /*Balance sheet*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_group_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP","CUST","SEG"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_finance_balance_sheet_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BS', $this->input->post('fieldNameChk'));
                        if ($this->input->post('rptType') == 1) {
                            $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                            $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                            $this->load->view('system/finance/report/erp_finance_balance_sheet_month_wise_report', $data);
                        } else if ($this->input->post('rptType') == 3) {
                            $this->load->view('system/finance/report/erp_finance_balance_sheet_ytd_report', $data);
                        }
                    }
                }
                break;
            case "FIN_IS": /*Income statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP","CUST","SEG"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data['segmentfilter'] = $this->Report_model->get_group_segment_fin();
                        $data["output"] = $this->Report_model->get_finance_income_statement_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                        if ($this->input->post('rptType') == 1) {
                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                            $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_report', $data);
                        } else if ($this->input->post('rptType') == 3) {
                            $this->load->view('system/finance/report/erp_finance_income_statement_ytd_report', $data);
                        } else if ($this->input->post('rptType') == 5) {
                            $this->load->view('system/finance/report/erp_finance_income_statement_ytd_budget_report', $data);
                        } else if ($this->input->post('rptType') == 4) {
                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                            $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_budget_report', $data);
                        }
                    }
                }
                break;
            case "AR_CAS": /*Customer Aging Summary*/
                
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    //$PostDatedChequeManagement=1;
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_group_report_pdc($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report_pdc', $data);
                    } else {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_group_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report', $data);
                    }
                }
                break;
            case "AR_CAD": /*Customer Aging Detail*/  
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    //$PostDatedChequeManagement  = 1;
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_group_report_postdated($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report_pdc', $data);
                    }else
                    {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_group_detail_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report', $data);
                    }
                }
                break;      
            case "AP_VAS": /*Vendor Aging Summary*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report_postdated_group($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report_postdated_cheque', $data);
                    } else {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report_group($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report', $data);
                    }
                }
                break;
            }
    }

    function get_financial_year()
    {
        if ($this->input->post("type") == 1) {
            echo json_encode($this->Report_model->get_financial_year());
        } else {
            echo json_encode($this->Report_model->get_group_financial_year());
        }
    }

    function get_report_drilldown()
    {
        $report = $this->input->post('reportID');
        switch ($this->input->post('reportID')) {
            case "FIN_TB";/*Trial balanacer*/
            case "FIN_IS";/*Income Statement*/
            case "FIN_BD";/*Budget*/
            case "FIN_BS";/*Balance sheet*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                $finaceYearYN = getPolicyValues('HFY','All');
                if($finaceYearYN!=1 && (($report!='FIN_TB')&&($report!='FIN_BS')))
                {
                    if (isset($to)) {
                        $fromTo = true;
                        $data["to"] = $this->input->post('to');
                    }
                }

                if (isset($segments)) {
                    $segment = true;
                }

                $financialBeginingDate = ($finaceYearYN == 1 ? get_financial_year(format_date($this->input->post("to"))) : get_financial_year(format_date($this->input->post("from"))));
                $data["output"] = $this->Report_model->get_finance_report_drilldown($fromTo, $segment, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";

                // echo '<pre>';
                // print_r($data["output"]); exit;

                $this->load->view('system/finance/report/erp_finance_drilldown_report_credit_debit', $data);
                break;

            case "AP_VAS";/*Vendor Aging Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                $data["output"] = $this->Report_model->get_accounts_payable_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/accounts_payable/report/erp_accounts_payable_drilldown_report', $data);
                break;
            case "AR_CAS";/*Customer Aging Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $customerAutoID = $this->input->post("customerTo");
                $customerAutoID=join(",",$customerAutoID);
                $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                $data["output"] = $this->Report_model->get_accounts_receivable_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["groupbycus"] = $this->input->post('groupbycus');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $data["template"] = "default";
                $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_drilldown_report', $data);
                break;
            case "INV_VAL";/*Item Valuation Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_item_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/inventory/report/erp_item_drilldown_report', $data);
                break;
            case "INV_IIQ";/*Item Inquiry*/
                if ($this->input->post('currency') == 'PO') {
                    $data["output"] = $this->Report_model->get_item_inquiry_po_report_drilldown();
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_po_drilldown_report', $data);
                } else {
                    $data["output"] = $this->Report_model->get_item_inquiry_all_doc_report_drilldown();
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_all_doc_drilldown_report', $data);
                }

                break;
        }
    }

    function get_report_drilldown_new()
    {
        $report = $this->input->post('reportID');
        switch ($this->input->post('reportID')) {
            case "FIN_TB";/*Trial balanacer*/
            case "FIN_IS";/*Income Statement*/
            case "FIN_BD";/*Budget*/
            case "FIN_BS";/*Balance sheet*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                $finaceYearYN = getPolicyValues('HFY','All');
                if($finaceYearYN!=1 && (($report!='FIN_TB')&&($report!='FIN_BS')))
                {
                    if (isset($to)) {
                        $fromTo = true;
                        $data["to"] = $this->input->post('to');
                    }
                }

                if (isset($segments)) {
                    $segment = true;
                }

                $financialBeginingDate = ($finaceYearYN == 1 ? get_financial_year(format_date($this->input->post("to"))) : get_financial_year(format_date($this->input->post("from"))));
                $data["output"] = $this->Report_model->get_finance_report_drilldown($fromTo, $segment, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/finance/report/erp_finance_drilldown_templatereport', $data);
                break;
            case "AP_VAS";/*Vendor Aging Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                $data["output"] = $this->Report_model->get_accounts_payable_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/accounts_payable/report/erp_accounts_payable_drilldown_report', $data);
                break;
            case "AR_CAS";/*Customer Aging Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $customerAutoID = $this->input->post("customerTo");
                $customerAutoID=join(",",$customerAutoID);
                $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                $data["output"] = $this->Report_model->get_accounts_receivable_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["groupbycus"] = $this->input->post('groupbycus');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $data["template"] = "default";
                $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_drilldown_report', $data);
                break;
            case "INV_VAL";/*Item Valuation Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_item_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/inventory/report/erp_item_drilldown_report', $data);
                break;
            case "INV_IIQ";/*Item Inquiry*/
                if ($this->input->post('currency') == 'PO') {
                    $data["output"] = $this->Report_model->get_item_inquiry_po_report_drilldown();
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_po_drilldown_report', $data);
                } else {
                    $data["output"] = $this->Report_model->get_item_inquiry_all_doc_report_drilldown();
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_all_doc_drilldown_report', $data);
                }

                break;
        }
    }







    function get_report_group_drilldown()
    {
        switch ($this->input->post('reportID')) {
            case "FIN_TB";/*Trial balanacer*/
            case "FIN_IS";/*Income Statement*/
            case "FIN_BS";/*Balance sheet*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                $data["output"] = $this->Report_model->get_finance_report_group_drilldown($fromTo, $segment, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/finance/report/erp_finance_drilldown_report', $data);
                break;
            case "INV_VAL";/*Item Valuation Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_item_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/inventory/report/erp_item_drilldown_report', $data);
                break;
            case "INV_IIQ";/*Item Inquiry*/
                if ($this->input->post('currency') == 'PO') {
                    $data["output"] = $this->Report_model->get_item_inquiry_po_report_drilldown();
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_po_drilldown_report', $data);
                } else {
                    $data["output"] = $this->Report_model->get_item_inquiry_all_doc_report_drilldown();
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_all_doc_drilldown_report', $data);
                }

                break;
        }
    }

    function get_report_drilldown_pdf()
    {
        switch ($this->input->post('reportID')) {
            case "FIN_TB";/*Trial balanacer*/
            case "FIN_IS";/*Income Statement*/
            case "FIN_BS";/*Balance sheet*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                $data["output"] = $this->Report_model->get_finance_report_drilldown($fromTo, $segment, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "pdf";
                $html = $this->load->view('system/finance/report/erp_finance_drilldown_report', $data, true);
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4-L');
                break;
            case "AP_VAS";/*Vendor Aging Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_accounts_payable_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "pdf";
                $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_drilldown_report', $data, true);
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4-L');
                break;
            case "AR_CAS";/*Customer Aging Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_accounts_receivable_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "pdf";
                $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_drilldown_report', $data, true);
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4-L');
                break;
            case "INV_VAL";/*Item Valuation Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_item_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "pdf";
                $html = $this->load->view('system/inventory/report/erp_item_drilldown_report', $data, true);
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4-L');
                break;
            case "INV_IIQ";/*Item Inquiry*/
                if ($this->input->post('currency') == 'PO') {
                    $data["output"] = $this->Report_model->get_item_inquiry_po_report_drilldown();
                    $data["type"] = "pdf";
                    $html = $this->load->view('system/inventory/report/erp_item_inquiry_po_drilldown_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                } else {
                    $data["output"] = $this->Report_model->get_item_inquiry_all_doc_report_drilldown();
                    $data["type"] = "pdf";
                    $html = $this->load->view('system/inventory/report/erp_item_inquiry_all_doc_drilldown_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }

                break;
        }
    }

    function get_group_report_drilldown_pdf()
    {
        switch ($this->input->post('reportID')) {
            case "FIN_TB";/*Trial balanacer*/
            case "FIN_IS";/*Income Statement*/
            case "FIN_BS";/*Balance sheet*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                $data["output"] = $this->Report_model->get_finance_report_group_drilldown($fromTo, $segment, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "pdf";
                $html = $this->load->view('system/finance/report/erp_finance_drilldown_report', $data, true);
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4-L');
                break;
        }
    }

    function get_report_by_id_pdf()
    {

        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque+
        $companyID = $this->common_data['company_data']['company_id'];

        switch ($this->input->post('reportID')) {
            case "AR_CS": /*customer_statement*/
                $fieldNameChk = array("transactionAmount");
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                //$this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if($PostDatedChequeManagement ==1)
                    {
                        $_POST["captionChk"] = array("Transaction Currency");
                        $_POST["fieldNameChk"] = array("transactionAmount");
                        //$data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report_postdated();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_receipt_statement_report_postdated();
                        $data["caption"] = array("Transaction Currency");
                        $data["fieldName"] = array("transactionAmount");
                        $data["from"] = convert_date_format($this->input->post('from'));
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        //$html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report_pdf_pdc', $data);
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_receipt_statement_report_pdf_pdc', $data);
                    }else
                    {
                        $_POST["captionChk"] = array("Transaction Currency");
                        $_POST["fieldNameChk"] = array("transactionAmount");
                        //$data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_receipt_statement_report();
                        $data["caption"] = array("Transaction Currency");
                        $data["fieldName"] = array("transactionAmount");
                        $data["from"] = convert_date_format($this->input->post('from'));
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        //$html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report_pdf', $data);
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_receipt_statement_report_pdf', $data);
                    }
                }
                break;

            case "AR_CL": /*customer ledger*/
                $fieldNameChk = array("transactionAmount");
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                //$this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if($PostDatedChequeManagement ==1)
                    {
                        $_POST["captionChk"] = array("Transaction Currency");
                        $_POST["fieldNameChk"] = array("transactionAmount");
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_report_postdated_cheques();
                        $data["caption"] = array("Transaction Currency");
                        $data["fieldName"] = array("transactionAmount");
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                        //$html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report_pdf', $data);
                        //$html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_cd_report_pdf', $data);
                        $printlink = print_template_pdf('AR_CL_pdf', 'system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report_pdf_pdc');
                        $html = $this->load->view($printlink, $data);
                    }else
                    {
                        $_POST["captionChk"] = array("Transaction Currency");
                        $_POST["fieldNameChk"] = array("transactionAmount");
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_report();
                        $data["caption"] = array("Transaction Currency");
                        $data["fieldName"] = array("transactionAmount");
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                        //$html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report_pdf', $data);
                        //$html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_cd_report_pdf', $data);
                        $printlink = print_template_pdf('AR_CL_pdf', 'system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report_pdf');
                        $html = $this->load->view($printlink, $data);
                    }

                    /*$this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4');*/
                }
                break;

            case "FIN_BS": /*Balance Sheet*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_balance_sheet_report();
                    $data["caption"] = $_POST["captionChk"];
                    $data["fieldName"] = $_POST["fieldNameChk"];
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BS', $_POST["fieldNameChk"]);
                    if ($this->input->post('rptType') == 1) {
                        $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                        $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_balance_sheet_month_wise_report', $data, true);
                    } else if ($this->input->post('rptType') == 3) {
                        $html = $this->load->view('system/finance/report/erp_finance_balance_sheet_ytd_report', $data, true);
                    }
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "FIN_TB": /*Trial Balance*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_tb_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 1) {
                        $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                        $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_tb_month_wise_report', $data, true);
                    } else if ($this->input->post('rptType') == 3) {
                        $data["retain"] = $this->Report_model->get_finance_tb_retain();
                        $html = $this->load->view('system/finance/report/erp_finance_tb_ytd_report', $data, true);
                    }
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "FIN_IS": /*Income statement*/
                $date_format_policy = date_format_policy();
                $format_startdate = null;
                $format_to = null;
                $startdate = $this->input->post("from");
                $to = $this->input->post("to");
                $format_startdate = input_format_date($startdate, $date_format_policy);
                $format_to = input_format_date($to, $date_format_policy);
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_income_statement_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 1) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_report', $data, true);
                    } else if ($this->input->post('rptType') == 3) {
                        $html = $this->load->view('system/finance/report/erp_finance_income_statement_ytd_report', $data, true);
                    } else if ($this->input->post('rptType') == 5) {
                        $html = $this->load->view('system/finance/report/erp_finance_income_statement_ytd_budget_report', $data, true);
                    } else if ($this->input->post('rptType') == 4) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_budget_report', $data, true);
                    } else if ($this->input->post('rptType') == 7) {
                        $html = $this->load->view('system/finance/report/erp_finance_income_statement_ytd_lyd_report', $data,true);
                    }
                    else if ($this->input->post('rptType') == 8) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $html =  $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_budget_ytdltd_report', $data,true);


                    }
                    else if ($this->input->post('rptType') == 9) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $segments_arr = $this->input->post('segment');
                        $segments = implode(', ', $segments_arr);
                        $data['rptType'] = $this->input->post('rptType');
                        $data['segment'] = $this->db->query("SELECT segmentID, segmentCode FROM srp_erp_segment WHERE segmentID IN ({$segments})")->result_array();
                        $html =  $this->load->view('system/finance/report/erp_finance_income_statement_segment_budget_report', $data,true);


                    }

                    //echo $html;
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "FIN_GL": /*General Ledger */
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $_POST["documentIDpdf"] = explode(',', $this->input->post('documentIDpdf'));
            
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('glCodeTo[]', 'GL Code', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $educalTempAssigned = $this->db->query("SELECT srp_erp_printtemplates.TemplateMasterID FROM srp_erp_printtemplates INNER JOIN srp_erp_printtemplatemaster ON srp_erp_printtemplatemaster.TemplateMasterID = srp_erp_printtemplates.TemplateMasterID WHERE srp_erp_printtemplates.documentID = 'FIN_GL' AND srp_erp_printtemplates.companyID = {$companyID} AND srp_erp_printtemplatemaster.TempPageNameLink='system/finance/report/erp_finance_general_ledger_educal'")->row_array();
                    $donorTempAssigned =  $this->db->query("SELECT srp_erp_printtemplates.TemplateMasterID FROM srp_erp_printtemplates INNER JOIN srp_erp_printtemplatemaster ON srp_erp_printtemplatemaster.TemplateMasterID = srp_erp_printtemplates.TemplateMasterID WHERE srp_erp_printtemplates.documentID = 'FIN_GL' AND srp_erp_printtemplates.companyID = {$companyID} AND srp_erp_printtemplatemaster.TempPageNameLink='system/finance/report/erp_finance_general_ledger_report_don'")->row_array();
                    
                    if(!empty($educalTempAssigned)){
                        $data["output"] = $this->Report_model->get_educal_general_ledger_report();
                    }elseif($donorTempAssigned){
                        $data["output"] = $this->Report_model->get_finance_general_ledger_report_donor();
                    }else{
                        $data["output"] = $this->Report_model->get_finance_general_ledger_report();
                    }
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_GL', $this->input->post('fieldNameChk'));
                    //$html = $this->load->view('system/finance/report/erp_finance_general_ledger_report', $data, true);
                    $printlink = print_template_pdf('FIN_GL', 'system/finance/report/erp_finance_general_ledger_report');
                    $html = $this->load->view($printlink, $data, true);
                    /*$this->load->library('pdftc');
                    $pdf = $this->pdftc->printed($html, 'A4-L');*/
                    /*$this->load->library('pdfdom');
                    $pdf = $this->pdfdom->printed($html, 'A4-L');*/
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;

            case "FIN_BD": /*Budget*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_budget_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BD', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 5) {
                        $html=$this->load->view('system/finance/report/erp_finance_ytd_budget_report', $data, true);
                    } else if ($this->input->post('rptType') == 4) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $html= $this->load->view('system/finance/report/erp_finance_budget_month_wise_report', $data, true);
                    }
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;

            case "PROC_POL": /*PO List*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('status', 'Status', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_procurement_purchase_order_list_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["status"] = $this->input->post('status');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('PROC_POL', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/procurement/report/erp_procurement_purchase_order_list_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "ITM_LG": /*item ledger*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_ledger_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_LG', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_ledger_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;

            case "INV_VAL": /*item valuation*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                /*if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }*/
                if($_POST["fieldNameChk"]) {
                    if(in_array("companyLocalWacAmount", $_POST["fieldNameChk"]) && in_array("companyReportingWacAmount", $_POST["fieldNameChk"])){
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else if(!in_array("companyLocalWacAmount", $_POST["fieldNameChk"]) && !in_array("companyReportingWacAmount", $_POST["fieldNameChk"])){
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    }
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_valuation_summary_report();
                    $data["TotalAssetValue"] = $this->Report_model->get_item_valuation_summary_total_asset();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_VAL', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_valuation_summary_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;

            case "ITM_CNT": /*item counting*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_counting_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_CNT', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_counting_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "ITM_FM": /*item fast moving*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report Type', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["output"] = $this->Report_model->get_item_fast_moving_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_FM', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_fast_moving_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "INV_UBG": /*Unbilled GRV*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_inventory_unbilled_grv_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBG', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_inventory_unbilled_grv_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "INV_IIQ": /*Item Inquiry*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_inquiry_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_CNT', $this->input->post('fieldNameChk'));

                    $data["warehouse"] = load_location_drop();
                    $data["type"] = "pdf";
                    $html = $this->load->view('system/inventory/report/erp_item_inquiry_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "AP_VL": /*Vendor Ledger*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if($PostDatedChequeManagement ==1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_report_postdatedcheques();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                        //$html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data, true);
                        $printlink = print_template_pdf('AP_VL', 'system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report_postdated');
                        $html = $this->load->view($printlink, $data, true);
                        //echo $html;
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }else
                    {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                        //$html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data, true);
                        $printlink = print_template_pdf('AP_VL', 'system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report');
                        $html = $this->load->view($printlink, $data, true);
                        //echo $html;
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }
                }
                break;
            case "AP_VS": /*Vendor Statement*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if($PostDatedChequeManagement ==1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report_postdatedcheque();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report_postdated', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }else
                    {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }
                }
                break;
            case "AP_VAS": /*Vendor Aging Summary*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if($PostDatedChequeManagement ==1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report_postdated($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                        $data["type"] = "pdf";
                        $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report_postdated_cheque', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }else
                    {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                        $data["type"] = "pdf";
                        $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }
                }
                break;
            case "AP_VAD": /*Vendor Aging Detail*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) { /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if($PostDatedChequeManagement ==1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_detail_report_postdated($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAD', $this->input->post('fieldNameChk'));
                        $data["type"] = "pdf";
                        $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_detail_report_postdated', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }else
                    {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_detail_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAD', $this->input->post('fieldNameChk'));
                        $data["type"] = "pdf";
                        $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_detail_report', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }
                }
                break;
            case "AR_CAS": /*Customer Aging Summary*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_report_pdc($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report_pdc', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }else
                    {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }
                }
                break;
            case "AR_CAD": /*Customer Aging Detail*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1)
                    {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_report_postdated($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "pdf";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report_pdc', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }else
                    {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["aging"] = $aging;
                        $data["type"] = "pdf";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }

                }
                break;
            case "INV_IBSO": /*Item Inquiry*/
                $type = '';
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $typeAs = $this->input->post('fieldNameChkexceltypecheck1');

                //$fieldNameChk = $this->input->post('fieldNameChkpdftypeas');
                $fieldName = explode(',' , $this->input->post('fieldNameChkpdf'));

                if($typeAs == 'itembelowstock')
                {
                    $type = 'Below Minimum Stock';
                }else if($typeAs == 'itembelowro')
                {
                    $type = 'Item Below RO';
                }

                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_below_stock_ro_report($typeAs,$fieldName);
                    $data["warehouse"] = load_location_drop();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["type_filter"] =$type;
                    $data["type"] = "pdf";
                    $html =  $this->load->view('system/inventory/report/item_below_stock', $data,true);
                    /*echo $html;
                    exit();*/
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4');
                }
                break;
            case "INV_UBI": /*Unbilled invoice*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_inventory_un_billed_invoice_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBI', $this->input->post('fieldNameChk'));
                    //echo '<pre>'; print_r($data["fieldNameDetails"]); echo '</pre>';
                    $html = $this->load->view('system/sales/un-billed-invoice-report', $data,true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "AR_CSR":
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                //$fieldNameChk = $this->input->post('fieldNameChkpdf');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChkpdf[]', 'Column', 'callback_check_valid_extra_column');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {

                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report_postdated_rebate();
                        $data["caption"] =   $this->input->post('captionChk');
                        $data["fieldName"] =$this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $this->input->post('fieldNameChk'));
                        $html=  $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_outstanding_report_postdated_cheques', $data,true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4');
                    } else {

                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report_rebate();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS',$this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_outstanding_report', $data,true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }


                }

                break;
        }
    }

    function get_group_report_by_id_pdf()
    {
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque+
        switch ($this->input->post('reportID')) {
            case "ITM_LG": /*item ledger*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_ledger_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_group_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_LG', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_ledger_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "INV_VAL": /*item valuation*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_valuation_summary_group_report();
                    $data["TotalAssetValue"] = $this->Report_model->get_item_valuation_summary_total_asset_group();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_group_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_VAL', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_valuation_summary_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "ITM_CNT": /*item counting*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_counting_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_group_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_CNT', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_counting_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "INV_UBG": /*Unbilled GRV*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_inventory_unbilled_grv_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBG', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_inventory_unbilled_grv_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;

            case "AR_CL": /*Customer Ledger*/
                $fieldNameChk = array("transactionAmount");
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                //$this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if($PostDatedChequeManagement ==1)
                    {
                        $_POST["captionChk"] = array("Transaction Currency");
                        $_POST["fieldNameChk"] = array("transactionAmount");
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_group_report_postdated_cheques();
                        $data["caption"] = array("Transaction Currency");
                        $data["fieldName"] = array("transactionAmount");
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                        $printlink = print_template_pdf('AR_CL_pdf', 'system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report_pdf_pdc');
                        $html = $this->load->view($printlink, $data);
                   
                    }else{
                        $_POST["captionChk"] = array("Transaction Currency");
                        $_POST["fieldNameChk"] = array("transactionAmount");
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_group_report();
                        $data["caption"] = array("Transaction Currency");
                        $data["fieldName"] = array("transactionAmount");
                        $data["from"] = convert_date_format($this->input->post('from'));
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                        $printlink = print_template_pdf('AR_CL_pdf', 'system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report_pdf');
                        $html = $this->load->view($printlink, $data);
                   
                    }
                }
                break;
            case "AR_CS": /*Customer Statement*/
                $fieldNameChk = array("transactionAmount");
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                //$this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if($PostDatedChequeManagement ==1)
                    {
                        $_POST["captionChk"] = array("Transaction Currency");
                        $_POST["fieldNameChk"] = array("transactionAmount");
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_group_report_postdated();
                        $data["caption"] = array("Transaction Currency");
                        $data["fieldName"] = array("transactionAmount");
                        $data["from"] = convert_date_format($this->input->post('from'));
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        //$html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report_pdf_pdc', $data);
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_receipt_statement_report_pdf_pdc', $data);
                    }else
                    {
                        $_POST["captionChk"] = array("Transaction Currency");
                        $_POST["fieldNameChk"] = array("transactionAmount");
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_group_report();
                        $data["caption"] = array("Transaction Currency");
                        $data["fieldName"] = array("transactionAmount");
                        $data["from"] = convert_date_format($this->input->post('from'));
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        //$html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report_pdf', $data);
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_receipt_statement_report_pdf', $data);
                        
                    }
                    /* $this->load->library('pdf');
                     $pdf = $this->pdf->printed($html, 'A4');*/
                }
                break;
            case "AP_VL": /*Vendor Ledger*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if($PostDatedChequeManagement ==1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_report_postdatedcheques_group();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                        //$html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data, true);
                        $printlink = print_template_pdf('AP_VL', 'system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report_postdated');
                        $html = $this->load->view($printlink, $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    
                    }else{
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }
                    
                }
                break;
            case "AP_VS": /*Vendor Statement*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if($PostDatedChequeManagement ==1) {
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report_postdatedcheque_group();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report_postdated', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }else{
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }
                }
                break;
            case "FIN_TB": /*Trial Balance*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_group_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_tb_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    $html = "";
                    if ($this->input->post('rptType') == 1) {
                        $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                        $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_tb_month_wise_report', $data, true);
                    } else if ($this->input->post('rptType') == 3) {
                        $data["retain"] = $this->Report_model->get_finance_tb_group_retain();
                        $html = $this->load->view('system/finance/report/erp_finance_tb_ytd_report', $data, true);
                    }
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "FIN_GL": /*General Ledger */
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('glCodeTo[]', 'GL Code', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_general_ledger_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_GL', $this->input->post('fieldNameChk'));
                    $printlink = print_template_pdf('FIN_GL','system/finance/report/erp_finance_general_ledger_report');
                    $html =$this->load->view($printlink, $data, true);
                    //$html = $this->load->view('system/finance/report/erp_finance_general_ledger_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "FIN_BS": /*Balance Sheet*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_group_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_balance_sheet_group_report();
                    $data["caption"] = $_POST["captionChk"];
                    $data["fieldName"] = $_POST["fieldNameChk"];
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BS', $_POST["fieldNameChk"]);
                    if ($this->input->post('rptType') == 1) {
                        $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                        $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_balance_sheet_month_wise_report', $data, true);
                    } else if ($this->input->post('rptType') == 3) {
                        $html = $this->load->view('system/finance/report/erp_finance_balance_sheet_ytd_report', $data, true);
                    }
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "AR_CAS": /*Customer Aging Summary*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }
                        }
                    }
                    $aging[] = "> " . ($through);
                    $PostDatedChequeManagement = 1;
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_group_report_pdc($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report_pdc', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }else
                    {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_group_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "pdf";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }
                }
                break;    
            case "AR_CAD": /*Customer Aging Detail*/    
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    $PostDatedChequeManagement = 1;
                    if ($PostDatedChequeManagement == 1)
                    {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_group_report_postdated($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "pdf";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report_pdc', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }else
                    {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_group_detail_report($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["aging"] = $aging;
                        $data["type"] = "pdf";
                        $data["template"] = "default";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report', $data, true);
                        $this->load->library('pdf');
                        $pdf = $this->pdf->printed($html, 'A4-L');
                    }

                }
                break;   
                case "AP_VAS": /*Vendor Aging Summary*/
                    $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                    $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                    $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                    $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                    $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                    $this->form_validation->set_rules('through', 'Through', 'trim|required');
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                    if ($this->form_validation->run() == FALSE) {
                        $error_message = validation_errors();
                        echo warning_message($error_message);
                    } else {
                        $data = array();
                        $aging = array();
                        $interval = $this->input->post("interval");
                        $through = $this->input->post("through");
                        $z = 1;
                        for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                            if ($z == 1) {
                                $aging[] = $z . "-" . $interval;
                            } else {
                                if (($i + $interval) > $through) {
                                    $aging[] = ($i + 1) . "-" . ($through);
                                    $i += $interval;
                                } else {
                                    $aging[] = ($i + 1) . "-" . ($i + $interval);
                                    $i += $interval;
                                }
    
                            }
                        }
                        $aging[] = "> " . ($through);
                        if($PostDatedChequeManagement ==1) {
                            $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report_postdated_group($aging);
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["aging"] = $aging;
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                            $data["type"] = "pdf";
                            $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report_postdated_cheque', $data, true);
                            $this->load->library('pdf');
                            $pdf = $this->pdf->printed($html, 'A4-L');
                        }else
                        {
                            $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report_group($aging);
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["aging"] = $aging;
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                            $data["type"] = "pdf";
                            $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report', $data, true);
                            $this->load->library('pdf');
                            $pdf = $this->pdf->printed($html, 'A4-L');
                        }
                    }
                break;     
        }
    }

    function check_valid_extra_column($fieldNameChk)
    {
        if (empty($fieldNameChk)) {
            $this->form_validation->set_message('check_valid_extra_column', 'Please select one currency');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function check_column_count_selected()
    {
        $this->form_validation->set_message('check_column_count_selected', 'please select one currency');
        return FALSE;
    }

    function check_valid_financial_year($date)
    {
        $output = get_financial_year(format_date($date));
        if (!$output) {
            $this->form_validation->set_message('check_valid_financial_year', 'Invalid Date Range Selected');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function check_valid_group_financial_year($date)
    {
        $output = get_group_financial_year(format_date($date));
        if (!$output) {
            $this->form_validation->set_message('check_valid_financial_year', 'Invalid Date Range Selected');
            return FALSE;
        } else {
            return TRUE;
        }
    }


    function check_compareDate()
    {
        $from = strtotime(convert_date_format($_POST['from']));
        $to = strtotime(convert_date_format($_POST['to']));

        if ($to >= $from) {
            return True;
        } else {
            $this->form_validation->set_message('check_compareDate', 'Invalid Date Range Selected');
            return False;
        }
    }

    function dashboardReportView()
    {
        $rptID = trim($this->input->post('RptID') ?? '');
        $currentDate = date("Y-m-d");
        $companyId = $this->common_data['company_data']['company_id'];
        switch ($rptID) {
            case "FIN_IS": /*Income statement*/
                $allSegments = $this->db->query("SELECT segmentID,status from srp_erp_segment where companyID = '{$companyId}'")->result_array();
                $new_array = array();
                if (!empty($allSegments)) {
                    foreach ($allSegments as $value) {
                        $new_array[] = $value['segmentID'];
                    }
                }
                $period = $this->input->post("year");
                $lastTwoYears = get_last_two_financial_year();
                if (!empty($lastTwoYears)) {
                    $beginingDate = $lastTwoYears[$period]["beginingDate"];
                    $endDate = $lastTwoYears[$period]["endingDate"];
                }
                $_POST['rptType'] = 3;
                $_POST['segment'] = $new_array;
                $_POST['from'] = $beginingDate;
                $_POST['to'] = $endDate;
                $data = array();
                $data["output"] = $this->Report_model->get_finance_income_statement_report();
                $data["caption"] = $this->input->post('captionChk');
                $data["fieldName"] = $this->input->post('fieldNameChk');
                $data["from"] = convert_date_format($beginingDate);
                $data["to"] = convert_date_format($endDate);
                $data["userDashboardID"] = $this->input->post('userDashboardID');
                $data["type"] = "html";
                $data["segmentfilter"] = $this->Report_model->get_segment();
                $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_IS', $this->input->post('fieldNameChk'));
                $this->load->view('system/finance/report/erp_finance_income_statement_ytd_report', $data);
                break;
            case "AP_VS": /*Vendor Statement*/
                $_POST['from'] = $currentDate;
                $data = array();
                $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report();
                $data["caption"] = $this->input->post('captionChk');
                $data["fieldName"] = $this->input->post('fieldNameChk');
                $data["from"] = current_format_date();
                $data["type"] = "html";
                $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report', $data);
                break;
            case "AR_CS": /*Customer Statement*/
                $_POST['from'] = $currentDate;
                $data = array();
                $customerAutoID = $this->input->post("customerTo");
                $customerAutoID=join(",",$customerAutoID);
                $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report();
                $data["caption"] = $this->input->post('captionChk');
                $data["fieldName"] = $this->input->post('fieldNameChk');
                $data["groupbycus"] = 1;
                $data["from"] = current_format_date();
                $data["type"] = "html";
                $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $this->input->post('fieldNameChk'));
                $data["template"] = "default";
                $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report', $data);
                break;
        }
    }

    function dashboardReportDrilldownView()
    {
        $rptID = trim($this->input->post('RptID') ?? '');
        $currentDate = date("Y-m-d");
        $companyId = $this->common_data['company_data']['company_id'];
        switch ($rptID) {
            case "FIN_IS";/*Income Statement*/
                $allSegments = $this->db->query("SELECT segmentID,status from srp_erp_segment where companyID = '{$companyId}' AND status = 1")->result_array();
                $new_array = array();
                if (!empty($allSegments)) {
                    foreach ($allSegments as $value) {
                        $new_array[] = $value['segmentID'];
                    }
                }
                $period = $this->input->post("year");

                $company_type = $this->session->userdata("companyType");

                if($company_type==1) {
                    $lastTwoYears = get_last_two_financial_year();
                }else
                {
                    $lastTwoYears = get_last_two_financial_year_group();
                }


                if (!empty($lastTwoYears)) {
                    $beginingDate = $lastTwoYears[$period]["beginingDate"];
                    $endDate = $lastTwoYears[$period]["endingDate"];
                }
                $fromTo = false;
                if (isset($endDate)) {
                    $fromTo = true;
                    $data["to"] = $endDate;
                }
                $_POST['segment'] = $new_array;
                $_POST['from'] = $beginingDate;
                $_POST['to'] = $endDate;
                $financialBeginingDate = get_financial_year($beginingDate);
                $data["output"] = $this->Report_model->get_finance_report_drilldown($fromTo, $new_array, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = $beginingDate;
                $data["type"] = 'html';
                $this->load->view('system/finance/report/erp_finance_drilldown_report', $data);
                break;
        }
    }


    function fetch_reportMaster()
    {
        $this->datatables->select('id, description, reportCode', false)
            ->from('srp_erp_sso_reportmaster')
            ->add_column('action', '$1', 'reportMaster_action(id, reportCode)');
        echo $this->datatables->generate();
    }

    function save_companyLevelReportDetails()
    {
        $reportType = $this->input->post('reportType');
        $fields_arr = get_ssoReportFields('C', $reportType);

        $this->form_validation->set_rules('masterID', 'Report master ID', 'required');
        $this->form_validation->set_rules('reportType', 'Report Type', 'required');
        foreach ($fields_arr as $field) {
            $this->form_validation->set_rules($field['inputName'], $field['description'], 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_companyLevelReportDetails());
        }

    }

    function employee_config_view()
    {
        $this->load->view('system/hrm/report/erp_report_employee_configuration');
    }

    function save_employeeLevelReportDetails()
    {
        $fields_arr = get_ssoReportFields('E');

        $this->form_validation->set_rules('masterID', 'Report master ID', 'required');
        /*foreach ($fields_arr as $field) {
            $this->form_validation->set_rules($field['inputName'] . '[]', $field['description'], 'required');
        }*/

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_employeeLevelReportDetails($fields_arr));
        }
    }

    function epf_reportGenerate()
    {

        $epfReportID = $this->uri->segment(3);
        $companyID = current_companyID();

        $report_companyData = get_ssoReportFields('C', 'EPF');

        $payrollData = $this->db->query("SELECT DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') ,'%Y%m') AS contPeriod, masterTB.submissionID,
                                         DATE_SUB(
                                            DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') ,'%Y-%m-%d'),  INTERVAL 1 MONTH
                                         ) AS lastPayrollDate,
                                         (
                                             SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                             JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                             WHERE fieldName='znCode' LIMIT 1
                                         ) AS znCode
                                         ,
                                         (
                                             SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                             JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                             WHERE fieldName='empNo' AND masterID=1 LIMIT 1
                                         ) AS employerNumber, payrollYear, payrollMonth
                                         FROM srp_erp_sso_epfreportmaster AS masterTB
                                         WHERE masterTB.companyID={$companyID} AND masterTB.id={$epfReportID}")->row_array();

        $fileName = $payrollData['znCode'] . '' . $payrollData['employerNumber'];
        $contPeriod = $payrollData['contPeriod'];
        $payrollYear = $payrollData['payrollYear'];
        $payrollMonth = $payrollData['payrollMonth'];
        $submissionID = $payrollData['submissionID'];
        $lastPayrollDate = new DateTime($payrollData['lastPayrollDate']);
        $lastPayrollYear = $lastPayrollDate->format('Y');
        $lastPayrollMonth = $lastPayrollDate->format('m');


        $empContributionID = null;
        $comContributionID = null;
        $totalEarningID = null;
        $otherColumn = '';


        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'empCont') {
                $comContributionID = $reportData['reportValue'];
            } else if ($reportData['inputName'] == 'memCont') {
                $empContributionID = $reportData['reportValue'];
            } else if ($reportData['inputName'] == 'totEarnings') {
                $totalEarningID = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }

        $otherColumn .= ', \'' . $contPeriod . '\' AS contPeriod';

        $data = $this->db->query("SELECT EIdNo, nic, employerCont, memberCont, REPLACE(FORMAT(ABS(employerCont + memberCont),2), ',', '')  AS toCount,
                                REPLACE(FORMAT(totEarnings,2), ',', '') AS totEarnings, IF(isExist=1, 'E', 'N') AS memStatus, {$submissionID} AS submissionID,
                                ocGrade, CAST(empConfigDet.memNumber AS SIGNED) AS orderColumn, empConfigDet.* {$otherColumn}
                                FROM srp_employeesdetails AS empTB
                                JOIN
                                (
                                    SELECT empID, REPLACE(FORMAT(sum(ABS(employerC)), 2), ',', '') as employerCont,
                                    REPLACE(FORMAT(sum(ABS(memberC)), 2), ',', '') as memberCont
                                    FROM
                                    (
                                        SELECT empID, if(detailTBID={$comContributionID}, transactionAmount, 0) AS employerC,
                                        if(detailTBID={$empContributionID}, transactionAmount, 0) AS memberC,
                                        transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                        FROM srp_erp_payrolldetail WHERE companyID={$companyID} AND fromTB='PAY_GROUP'
                                        AND detailTBID IN ({$empContributionID},{$comContributionID}) AND payrollMasterID IN (
                                            SELECT payMaster.payrollMasterID FROM srp_erp_payrollmaster AS payMaster
                                            JOIN srp_erp_payrollheaderdetails AS payHeader ON payHeader.payrollMasterID = payMaster.payrollMasterID
                                            WHERE payMaster.companyID={$companyID} AND payHeader.companyID={$companyID}
                                            AND payrollYear={$payrollYear} AND payrollMonth={$payrollMonth} AND approvedYN=1
                                        )
                                    ) AS TB1  GROUP BY empID
                                ) AS memberContTB ON memberContTB.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, MAX(initials) AS initials, MAX(memNumber) AS memNumber, MAX(lastName) AS lastName FROM
                                    (
                                        SELECT empID, reportFields.id, inputName, reportID, reportValue,
                                        IF(inputName='lastName',reportValue, '') AS lastName,
                                        IF(inputName='initials', reportValue, '') AS initials,
                                        IF(inputName='memNumber', reportValue, '') AS memNumber
                                        FROM srp_erp_sso_reporttemplatedetails AS reportDetails
                                        JOIN srp_erp_sso_reporttemplatefields AS reportFields ON reportFields.id = reportDetails.reportID
                                        WHERE reportDetails.companyID={$companyID} AND reportFields.isEmployeeLevel=1 AND masterID=1
                                    ) AS tb1 GROUP  BY empID
                                ) AS empConfigDet ON empConfigDet.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, ocGrade  FROM srp_erp_sso_epfreportdetails WHERE companyID={$companyID} AND epfReportID={$epfReportID}
                                )AS otherDetTB ON otherDetTB.empID=empTB.EIdNo
                                JOIN (
                                    SELECT empID, transactionAmount AS totEarnings FROM srp_erp_payrolldetailpaygroup WHERE companyID={$companyID}
                                    AND detailTBID={$totalEarningID} AND payrollMasterID IN (
                                        SELECT payMaster.payrollMasterID FROM srp_erp_payrollmaster AS payMaster
                                        JOIN srp_erp_payrollheaderdetails AS payHeader ON payHeader.payrollMasterID = payMaster.payrollMasterID
                                        WHERE payMaster.companyID={$companyID} AND payHeader.companyID={$companyID}
                                        AND payrollYear={$payrollYear} AND payrollMonth={$payrollMonth} AND approvedYN=1
                                    ) GROUP BY empID
                                )AS payGroup ON payGroup.empID=empTB.EIdNo
                                LEFT JOIN (
                                    SELECT empID, 1 AS isExist  FROM srp_erp_payrollmaster AS t1
                                    JOIN srp_erp_payrolldetail AS t2 ON t2.payrollMasterID=t1.payrollMasterID AND t2.companyID={$companyID}
                                    WHERE t1.companyID={$companyID} AND payrollYear={$lastPayrollYear} AND payrollMonth={$lastPayrollMonth}
                                    GROUP BY empID
                                ) AS isNewEmpTB ON isNewEmpTB.empID=empTB.EIdNo AND Erp_companyID={$companyID}
                                WHERE Erp_companyID={$companyID} ORDER BY orderColumn ASC")->result_array();

        if($this->uri->segment(4) == 'V'){
            $data['epf_data'] = $data;
            echo $this->load->view('system/hrm/report/ajax/epf-view', $data, true);
        }
        else{
            $fileName = $fileName . 'C';

            $this->generateDetFile($fileName, $data, 'EPF');
        }
    }

    function etf_reportGenerate()
    {

        $payrollMonth = $this->input->post('payrollMonth');
        $segment = $this->input->post('segment');
        $req_type = $this->input->post('req_type');
        $companyID = current_companyID();
        $report_companyData = get_ssoReportFields('C', 'ETF');

        $payYear = date('Y', strtotime($payrollMonth));
        $payMonth = date('m', strtotime($payrollMonth));

        $fileName = 'MEMTXT';
        $from2periodTo = date('Ym', strtotime($payrollMonth));
        $from2periodFrom = $from2periodTo;

        $etfContributionID = null;
        $otherColumn = '';

        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'etfContribution') {
                $etfContributionID = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }

        $otherColumn .= ', \'' . $from2periodTo . '\' AS from2periodTo, \'' . $from2periodFrom . '\' AS from2periodFrom';
        $joinSeg = "";


        if (!empty($segment)) {
            $whereIN = join(',', $segment);

            $joinSeg = "JOIN (
                            SELECT EmpID AS empIDSegTB FROM srp_erp_payrollmaster AS payMaster
                            JOIN srp_erp_payrollheaderdetails AS payHDet ON payMaster.payrollMasterID=payHDet.payrollMasterID AND payHDet.companyID={$companyID}
                            WHERE payMaster.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}
                            AND payHDet.segmentID IN ({$whereIN}) AND approvedYN=1
                       ) AS segmntFillterd ON segmntFillterd.empIDSegTB = empID";
        }


        $data = $this->db->query("SELECT EIdNo, nic, etfContribution, CAST(empConfigDet.memNumber AS SIGNED) AS orderColumn, 
                                employerC, empConfigDet.* {$otherColumn}
                                FROM srp_employeesdetails AS empTB
                                JOIN
                                (
                                    SELECT empID, (REPLACE(round(sum(ABS(transactionAmount)), 2), '.', ''))AS etfContribution,
                                    transactionAmount AS employerC, transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                    FROM srp_erp_payrolldetail {$joinSeg}
                                    WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$etfContributionID}
                                    AND payrollMasterID IN (
                                        SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                        AND payrollMonth={$payMonth} AND approvedYN=1
                                    )
                                    GROUP BY empID
                                ) AS memberContTB ON memberContTB.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, MAX(initials) AS initials, MAX(memNumber) AS memNumber, MAX(lastName) AS lastName FROM
                                    (
                                        SELECT empID, reportFields.id, inputName, reportID, reportValue,
                                        IF(inputName='lastName', UPPER(reportValue), '') AS lastName,
                                        IF(inputName='initials', reportValue, '') AS initials,
                                        IF(inputName='memNumber', reportValue, '') AS memNumber
                                        FROM srp_erp_sso_reporttemplatedetails AS reportDetails
                                        JOIN srp_erp_sso_reporttemplatefields AS reportFields ON reportFields.id = reportDetails.reportID
                                        WHERE reportDetails.companyID={$companyID} AND reportFields.isEmployeeLevel=1 AND masterID=2
                                    ) AS tb1 GROUP BY empID
                                ) AS empConfigDet ON empConfigDet.empID = empTB.EIdNo
                                WHERE Erp_companyID={$companyID} ORDER BY orderColumn ASC")->result_array();

        if (empty($data)) {
            die('There is no data');
        }

        if($req_type == 'View'){
            $data['etf_data'] = $data;
            echo $this->load->view('system/hrm/report/ajax/etf-view', $data, true);
        }
        else{
            $this->generateDetFile($fileName, $data, 'ETF');
        }
    }

    function etfHeaderRow($memberCount)
    {
        $payrollMonth = $this->input->post('payrollMonth');
        $segment = $this->input->post('segment');

        $payYear = date('Y', strtotime($payrollMonth));
        $payMonth = date('m', strtotime($payrollMonth));
        $from2periodTo = date('Ym', strtotime($payrollMonth));
        $from2periodFrom = $from2periodTo;

        $companyID = current_companyID();
        $report_companyData = get_ssoReportFields('C', 'ETF-H');

        $etfContributionTotalID = null;
        $otherColumn = '';
        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'etfContributionTotal') {
                $etfContributionTotalID = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }
        $otherColumn .= ', \'' . $from2periodTo . '\' AS from2periodTo, \'' . $from2periodFrom . '\' AS from2periodFrom';

        $headerData = ssoReport_shortOrder('ETF-H');

        $joinSeg = "";
        if (!empty($segment)) {
            $whereIN = join(',', $segment);

            $joinSeg = "JOIN (
                        SELECT EmpID AS empIDSegTB FROM srp_erp_payrollmaster AS payMaster
                        JOIN srp_erp_payrollheaderdetails AS payHDet ON payMaster.payrollMasterID=payHDet.payrollMasterID AND payHDet.companyID={$companyID}
                        WHERE payMaster.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}
                        AND payHDet.segmentID IN ({$whereIN}) AND approvedYN=1
                    ) AS segmntFillterd ON segmntFillterd.empIDSegTB = empID";
        }

        $data = $this->db->query("SELECT {$memberCount} AS totalMembers,
                                  (
                                      SELECT REPLACE((round(sum(ABS(transactionAmount)),2)), '.', '')
                                      FROM srp_erp_payrolldetail
                                      {$joinSeg}
                                      WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$etfContributionTotalID}
                                      AND payrollMasterID IN (
                                          SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                          AND payrollMonth={$payMonth} AND approvedYN=1
                                      )
                                  )AS etfContributionTotal {$otherColumn}
                                  ")->row_array();

        $textData = '';
        foreach ($headerData as $headerRow) {
            $thisRow = $headerRow['fieldName'];
            $length = $headerRow['strLength'];
            $value = trim($data[$thisRow]);
            $escapStr = ' ';


            switch ($headerRow['fieldName']) {
                case 'totalMembers':
                    $escapStr = '0';
                    break;

                case 'etfContributionTotal':
                    $escapStr = '0';
                    break;
                case 'nic':
                    $escapStr = '0';
                    break;
                default :
                    $escapStr = ' ';
            }


            if ($headerRow['isLeft_strPad'] == 1) {
                $textData .= str_pad($value, $length, $escapStr, STR_PAD_LEFT);
            } else if ($headerRow['fieldName'] == 'nic') {
                $textData .= str_pad($value, $length, $escapStr, STR_PAD_LEFT);
            } else {
                $textData .= str_pad($value, $length, $escapStr, STR_PAD_RIGHT);
            }
        }

        return $textData;
    }

    function generateDetFile($fileName, $data, $reportType)
    {
        $headerData = ssoReport_shortOrder($reportType);
        if ($data == 0) {
            echo '<p>The File appears to have no data.</p>';
        } else {
            $memberCount = 0;
            $textData = '';
            foreach ($data as $key => $row) {
                if ($reportType == 'EPF' && $row['toCount'] == '0.00') {
                    continue;
                }
                if ($reportType == 'ETF' && $row['etfContribution'] == '0.00') {
                    continue;
                }
                $memberCount++;
                foreach ($headerData as $headerRow) {
                    $thisRow = $headerRow['fieldName'];
                    $length = $headerRow['strLength'];
                    $value = trim($row[$thisRow]);
                    $escapStr = ' ';
                    if ($reportType == 'ETF') {
                        switch ($headerRow['fieldName']) {
                            //case 'nic':
                            case 'memNumber':
                            case 'etfContribution':
                                $escapStr = '0';
                            break;
                            default :
                                $escapStr = ' ';
                        }
                    }
                    if ($headerRow['isLeft_strPad'] == 1) {
                        $textData .= str_pad($value, $length, $escapStr, STR_PAD_LEFT);
                    } else if ($headerRow['fieldName'] == 'nic' && $reportType == 'ETF') {
                        $textData .= str_pad($value, $length, $escapStr, STR_PAD_LEFT).' ';
                    } else if ($headerRow['fieldName'] == 'empNo' && $reportType == 'ETF') {
                        $textData .= str_pad($value, $length, $escapStr, STR_PAD_LEFT);
                    } else {
                        $textData .= str_pad($value, $length, $escapStr, STR_PAD_RIGHT);
                    }
                }
                $textData .= PHP_EOL;
            }
            if ($reportType == 'ETF') {
                $textData .= $this->etfHeaderRow($memberCount);
            }
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $fileName . ".txt");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo trim($textData);
        }
    }

    function save_epfReportOtherConfig()
    {
        $this->form_validation->set_rules('masterID', 'Report master ID', 'required');
        $this->form_validation->set_rules('shortOrder[]', 'Short order', 'required');
        $this->form_validation->set_rules('strLength[]', 'Length', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_epfReportOtherConfig());
        }
    }

    function fetch_epfReport()
    {
        $this->datatables->select("id, master.documentCode, submissionID, comment, master.confirmedYN AS master_confirmedYN,
              DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') ,'%Y %M') AS payMonth", false)
            ->from('srp_erp_sso_epfreportmaster AS master')
            ->add_column('action', '$1', 'action_epfReport(id, master_confirmedYN)')
            ->where('master.companyID', current_companyID());
        echo $this->datatables->generate();
    }

    function save_epfReportMaster()
    {
        $this->form_validation->set_rules('payrollMonth', 'Payroll month ', 'required');
        $this->form_validation->set_rules('submissionID', 'Submission ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_epfReportMaster());
        }
    }

    function epf_reportData_view()
    {
        $epfMasterID = $this->input->post('epfMasterID');
        $data['master'] = $this->Report_model->epf_reportData($epfMasterID);
        $data['employees'] = $this->Report_model->get_epfReportEmployee();

        $this->load->view('system/hrm/report/erp_employee_epf_report_generate_view', $data);
    }

    function getEmployeesDataTable()
    {
        $companyID = current_companyID();
        $payrollYear = $this->input->post('payrollYear');
        $payrollMonth = $this->input->post('payrollMonth');
        $segmentID = $this->input->post('segmentID');

        /**Already exist employee list **/
        $whereNotIn_str = $this->employee_arr($payrollYear, $payrollMonth);


        $str_lastOCGrade = '(SELECT ocGrade FROM srp_erp_sso_epfreportdetails WHERE empID = EIdNo AND companyID=' . $companyID . ' ORDER BY id DESC LIMIT 1)';


        $this->datatables->select('EIdNo, ECode, Ename2 AS empName, DesDescription, srp_erp_segment.segmentCode AS segCode,
                                    IF(' . $str_lastOCGrade . ' IS NULL, \'\', ' . $str_lastOCGrade . ') AS last_ocGrade');
        $this->datatables->from('srp_employeesdetails');
        $this->datatables->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $this->datatables->join('srp_erp_segment', 'srp_employeesdetails.segmentID = srp_erp_segment.segmentID');
        $this->datatables->join('(SELECT EmpID AS payEmpID FROM srp_erp_payrollmaster AS payMaster
                                  JOIN srp_erp_payrollheaderdetails AS payHeader ON payHeader.payrollMasterID = payMaster.payrollMasterID
                                  WHERE payMaster.companyID=' . $companyID . ' AND payHeader.companyID=' . $companyID . ' AND
                                  payrollYear=' . $payrollYear . ' AND payrollMonth=' . $payrollMonth . ' AND approvedYN=1 ) AS payrollProcessedEmpTB',
            'srp_employeesdetails.EIdNo = payrollProcessedEmpTB.payEmpID');
        $this->datatables->add_column('addBtn', '$1', 'addBtn()');
        $this->datatables->where('srp_employeesdetails.Erp_companyID', $companyID);
        $this->datatables->where('srp_employeesdetails.isPayrollEmployee', 1);
        if ($whereNotIn_str != '') {
            $this->datatables->where('EIdNo NOT IN ' . $whereNotIn_str);
        }
        if (!empty($segmentID)) {
            $this->datatables->where('srp_employeesdetails.segmentID IN (' . $segmentID . ' )');
        }

        echo $this->datatables->generate();
    }

    function employee_arr($payrollYear, $payrollMonth)
    {
        $companyID = current_companyID();
        $empList = $this->db->query("SELECT empID FROM srp_erp_sso_epfreportmaster AS masterTB
                                     JOIN srp_erp_sso_epfreportdetails AS detailTB ON detailTB.epfReportID=masterTB.id AND detailTB.companyID=$companyID
                                     WHERE masterTB.companyID={$companyID} AND payrollYear={$payrollYear} AND payrollMonth={$payrollMonth}
                                     GROUP BY empID ")->result_array();
        $whereNotIn_str = '';
        if (!empty($empList)) {
            $whereNotIn_str = '( ';
            foreach ($empList as $key => $row) {
                $sepreter = ($key > 0) ? ',' : '';
                $whereNotIn_str .= $sepreter . '' . $row['empID'];
            }
            $whereNotIn_str .= ')';
        }

        return $whereNotIn_str;
    }

    function save_empEmployeeAsTemporary()
    {
        $this->form_validation->set_rules('masterID', 'Report ID ', 'required');
        $this->form_validation->set_rules('empHiddenID[]', 'Employee ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_empEmployeeAsTemporary());
        }
    }

    function epf_reportData()
    {
        $epfMasterID = $this->input->post('epfMasterID');
        echo json_encode($this->Report_model->epf_reportData($epfMasterID));
    }

    function get_epfReportEmployee()
    {
        $companyID = current_companyID();
        $con = "IFNULL(Ename2, '')";
        $epfReportID = $this->input->post('epfReportID');

        $where = array(
            'Erp_companyID' => $companyID,
            'companyID' => $companyID,
            'epfReportID' => $epfReportID
        );

        $this->datatables->select('empID, ECode, CONCAT(' . $con . ') AS empName, ocGrade');
        $this->datatables->from('srp_employeesdetails AS empTB');
        $this->datatables->join('srp_erp_sso_epfreportdetails AS reportTB', 'empTB.EIdNo = reportTB.empID');
        $this->datatables->add_column('addBtn', '$1', 'epfReportTextbox(empID,ocGrade)');
        $this->datatables->where($where);

        echo $this->datatables->generate();
    }

    function save_reportDetails()
    {
        $this->form_validation->set_rules('epfMasterID', 'Report ID', 'required');
        $this->form_validation->set_rules('ocGrade[]', 'OC Grade', 'required');
        $this->form_validation->set_rules('empID[]', 'Employee', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_reportDetails());
            die();
            /*$epfMasterID = $this->input->post('epfMasterID');
            $data = $this->Report_model->epf_reportData($epfMasterID);
            if($data['confirmedYN'] == 1){
                echo json_encode(array('e', 'This report is already confirmed.<br>You can not update this'));
            }
            else{
                echo json_encode($this->Report_model->save_reportDetails());
            }*/

        }
    }

    function delete_epfReportEmp()
    {
        $this->form_validation->set_rules('epfMasterID', 'Report ID', 'required');
        $this->form_validation->set_rules('id', 'Report detail ID', 'required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->delete_epfReportEmp());
        }
    }

    function delete_epfReportAllEmp()
    {
        $this->form_validation->set_rules('epfMasterID', 'Report ID', 'required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->delete_epfReportAllEmp());
        }
    }

    function delete_epfReport()
    {
        $this->form_validation->set_rules('deleteID', 'Report ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->delete_epfReport());
        }
    }

    function cFrom_reportGenerate()
    {
        $responseType = $this->uri->segment(3);
        $payrollMonth = $this->input->post('payrollMonth');
        $segment = $this->input->post('segment');

        $payYear = date('Y', strtotime($payrollMonth));
        $payMonth = date('m', strtotime($payrollMonth));
        $contPeriod = date('M - Y', strtotime($payrollMonth));
        $companyID = current_companyID();

        $report_companyData = get_ssoReportFields('C', 'EPF');


        $payrollData = $this->db->query("SELECT '{$contPeriod}' AS contPeriod,
                                         (
                                             SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                             JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                             WHERE fieldName='znCode' LIMIT 1
                                         ) AS znCode
                                         ,
                                         (
                                             SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                             JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                             WHERE fieldName='empNo' AND masterID=1 LIMIT 1
                                         ) AS employerNumber")->row_array();

        $empContributionID = null;
        $comContributionID = null;
        $totalEarningID = null;
        $otherColumn = '';


        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'empCont') {
                $comContributionID = $reportData['reportValue'];
            } else if ($reportData['inputName'] == 'memCont') {
                $empContributionID = $reportData['reportValue'];
            } else if ($reportData['inputName'] == 'totEarnings') {
                $totalEarningID = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }

        $joinSeg = "";
        if (!empty($segment)) {
            $whereIN = join(',', $segment);

            $joinSeg = "JOIN (
                            SELECT EmpID AS empIDSegTB FROM srp_erp_payrollmaster AS payMaster
                            JOIN srp_erp_payrollheaderdetails AS payHDet ON payMaster.payrollMasterID=payHDet.payrollMasterID AND payHDet.companyID={$companyID}
                            WHERE payMaster.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}
                            AND payHDet.segmentID IN ({$whereIN}) AND approvedYN=1
                       ) AS segmntFillterd ON segmntFillterd.empIDSegTB = empID";
        }

        $totalContribution = $this->db->query("SELECT sum(transactionAmount) AS totalContribution  FROM srp_erp_payrolldetail {$joinSeg}
                                        WHERE companyID={$companyID} AND fromTB='PAY_GROUP'
                                        AND detailTBID IN ({$empContributionID},{$comContributionID}) AND payrollMasterID
                                        IN (
                                            SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                            AND payrollMonth={$payMonth} AND approvedYN=1
                                        ) ")->row('totalContribution');


        $epfData = $this->db->query("SELECT EIdNo, nic, employerCont, memberCont, REPLACE(FORMAT(ABS(employerCont + memberCont),2), ',', '')  AS toCount,
                                REPLACE(FORMAT(totEarnings,2), ',', '') AS totEarnings,
                                CAST(empConfigDet.memNumber AS SIGNED) AS orderColumn, empConfigDet.* {$otherColumn}
                                FROM srp_employeesdetails AS empTB
                                JOIN
                                (
                                    SELECT empID, REPLACE(FORMAT(sum(ABS(employerC)), 2), ',', '') as employerCont,
                                    REPLACE(FORMAT(sum(ABS(memberC)), 2), ',', '') as memberCont
                                    FROM
                                    (
                                        SELECT empID, if(detailTBID={$comContributionID}, transactionAmount, 0) AS employerC,
                                        if(detailTBID={$empContributionID}, transactionAmount, 0) AS memberC,
                                        transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                        FROM srp_erp_payrolldetail {$joinSeg}
                                        WHERE companyID={$companyID} AND fromTB='PAY_GROUP'
                                        AND detailTBID IN ({$empContributionID},{$comContributionID}) AND payrollMasterID IN (
                                            SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                            AND payrollMonth={$payMonth} AND approvedYN=1
                                        )
                                    ) AS TB1  GROUP BY empID
                                ) AS memberContTB ON memberContTB.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, MAX(initials) AS initials, MAX(memNumber) AS memNumber, MAX(lastName) AS lastName FROM
                                    (
                                        SELECT empID, reportFields.id, inputName, reportID, reportValue,
                                        IF(inputName='lastName',reportValue, '') AS lastName,
                                        IF(inputName='initials', reportValue, '') AS initials,
                                        IF(inputName='memNumber', reportValue, '') AS memNumber
                                        FROM srp_erp_sso_reporttemplatedetails AS reportDetails
                                        JOIN srp_erp_sso_reporttemplatefields AS reportFields ON reportFields.id = reportDetails.reportID
                                        WHERE reportDetails.companyID={$companyID} AND reportFields.isEmployeeLevel=1 AND masterID=1
                                    ) AS tb1 GROUP  BY empID
                                ) AS empConfigDet ON empConfigDet.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, transactionAmount AS totEarnings FROM srp_erp_payrolldetailpaygroup WHERE companyID={$companyID}
                                    AND detailTBID={$totalEarningID} AND payrollMasterID IN (
                                        SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                        AND payrollMonth={$payMonth} AND approvedYN=1
                                    )
                                    GROUP BY empID
                                )AS payGroup ON payGroup.empID=empTB.EIdNo
                                WHERE Erp_companyID={$companyID} GROUP BY empTB.EIdNo ORDER BY orderColumn ASC")->result_array();


        $data['payrollData'] = $payrollData;
        $data['epfData'] = $epfData;
        $data['report_companyData'] = $report_companyData;
        $data['totalContribution'] = $totalContribution;
        $data['responseType'] = $responseType;

        if ($responseType == 'print') {
            $html = $this->load->view('system/hrm/report/cForm_view', $data, true);
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4', 1);
        } else {
            $this->load->view('system/hrm/report/cForm_view', $data);
        }

    }

    function rFrom_reportGenerate()
    {
        $responseType = $this->uri->segment(3);
        $payrollMonth = $this->input->post('payrollMonth');
        $segment = $this->input->post('segment');

        $payYear = date('Y', strtotime($payrollMonth));
        $payMonth = date('m', strtotime($payrollMonth));
        $contPeriod = date('M - Y', strtotime($payrollMonth));
        $companyID = current_companyID();


        $report_companyData = get_ssoReportFields('C', 'ETF');

        $payrollData = $this->db->query("SELECT '{$contPeriod}' AS contPeriod,
                                         (
                                             SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                             JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                             WHERE fieldName='empNo' AND masterID=2 LIMIT 1
                                         ) AS employerNumber ")->row_array();


        $etfContributionID = null;
        $otherColumn = '';

        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'etfContribution') {
                $etfContributionID = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }

        $joinSeg = "";
        if (!empty($segment)) {
            $whereIN = join(',', $segment);

            $joinSeg = "JOIN (
                            SELECT EmpID AS empIDSegTB FROM srp_erp_payrollmaster AS payMaster
                            JOIN srp_erp_payrollheaderdetails AS payHDet ON payMaster.payrollMasterID=payHDet.payrollMasterID AND payHDet.companyID={$companyID}
                            WHERE payMaster.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}
                            AND payHDet.segmentID IN ({$whereIN}) AND approvedYN=1
                       ) AS segmntFillterd ON segmntFillterd.empIDSegTB = empID";
        }

        $totalContribution = $this->db->query("SELECT sum(transactionAmount) AS totalContribution  FROM srp_erp_payrolldetail {$joinSeg}
                                        WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$etfContributionID}
                                        AND payrollMasterID IN (
                                            SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                            AND payrollMonth={$payMonth} AND approvedYN=1
                                        )")->row('totalContribution');

        $etfData = $this->db->query("SELECT EIdNo, nic, etfContribution, CAST(empConfigDet.memNumber AS SIGNED) AS orderColumn,
                                 empConfigDet.* {$otherColumn}
                                FROM srp_employeesdetails AS empTB
                                JOIN
                                (
                                    SELECT empID,
                                    round(sum(ABS(transactionAmount)), 2)AS etfContribution,
                                    transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                    FROM srp_erp_payrolldetail
                                    {$joinSeg}
                                    WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$etfContributionID}
                                    AND payrollMasterID IN (
                                        SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                        AND payrollMonth={$payMonth} AND approvedYN=1
                                    ) GROUP BY empID
                                ) AS memberContTB ON memberContTB.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, MAX(initials) AS initials, MAX(memNumber) AS memNumber, MAX(lastName) AS lastName FROM
                                    (
                                        SELECT empID, reportFields.id, inputName, reportID, reportValue,
                                        IF(inputName='lastName', UPPER(reportValue), '') AS lastName,
                                        IF(inputName='initials', reportValue, '') AS initials,
                                        IF(inputName='memNumber', reportValue, '') AS memNumber
                                        FROM srp_erp_sso_reporttemplatedetails AS reportDetails
                                        JOIN srp_erp_sso_reporttemplatefields AS reportFields ON reportFields.id = reportDetails.reportID
                                        WHERE reportDetails.companyID={$companyID} AND reportFields.isEmployeeLevel=1 AND masterID=2
                                    ) AS tb1 GROUP  BY empID
                                ) AS empConfigDet ON empConfigDet.empID = empTB.EIdNo
                                WHERE Erp_companyID={$companyID} ORDER BY orderColumn ASC")->result_array();

        $data['payrollData'] = $payrollData;
        $data['etfData'] = $etfData;
        $data['totalContribution'] = $totalContribution;
        $data['responseType'] = $responseType;

        if ($responseType == 'print') {
            $html = $this->load->view('system/hrm/report/rForm_view', $data, true);
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4', 1);
        } else {
            $this->load->view('system/hrm/report/rForm_view', $data);
        }
    }

    function etfReturn_reportGenerate()
    {

        $responseType = $this->uri->segment(3);
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $segment = $this->input->post('segment');
        $companyID = current_companyID();
        $fromDate = $fromDate . '-01';
        $toDate = $toDate . '-01';
        $fromDate_obj = DateTime::createFromFormat('Y-m-d', $fromDate);
        $fromDate = $fromDate_obj->format('Y-m-d');

        $toDate_Obj = DateTime::createFromFormat('Y-m-d', $toDate);
        $toDate = $toDate_Obj->format('Y-m-t');


        $report_companyData = get_ssoReportFields('C', 'ETF');

        $totEarningsID = $this->db->query("SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                           JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                           WHERE fieldName='totEarnings' AND masterID=1 ")->row('reportValue');

        $payrollMastersData = $this->db->query("SELECT * FROM (
                                                    SELECT payrollMasterID, MONTHNAME(STR_TO_DATE(payrollMonth, '%m')) AS payrollMonth,
                                                    STR_TO_DATE( CONCAT(payrollYear,'-', payrollMonth, '-01'), '%Y-%m-%d') AS payrollDate,
                                                    CONCAT(payrollYear, payrollMonth) AS payYearMonth
                                                    FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND approvedYN=1
                                                ) AS dateTB
                                                WHERE payrollDate BETWEEN '{$fromDate}' AND '{$toDate}' GROUP BY payrollDate")->result_array();


        $ifConditionETF_str = '';
        $maxETF_str = '';
        $ifConditionTotalEarnings_str = '';
        $maxTotalEarnings_str = '';
        $separator = '';
        $plus = '';
        $totalContribution_str = '( ';
        //echo '<pre>'; print_r($payrollMastersData); echo '</pre>';
        foreach ($payrollMastersData as $keyMonth => $payrollMonthDataRow) {
            $payYearMonth = $payrollMonthDataRow['payYearMonth'];
            $rowETFData = 'etfContribution_' . $payYearMonth;
            $rowTotalEarningsData = 'totalEarnings_' . $payYearMonth;

            $ifConditionETF_str .= $separator . ' IF( payYearMonthStr=' . $payYearMonth . ', etfContribution, 0 ) AS ' . $rowETFData;
            $ifConditionTotalEarnings_str .= $separator . ' IF( payYearMonthStr=' . $payYearMonth . ', totalEarnings, 0 ) AS ' . $rowTotalEarningsData;
            $maxETF_str .= $separator . ' MAX( ' . $rowETFData . ' ) AS ' . $rowETFData;
            $maxTotalEarnings_str .= $separator . ' MAX( ' . $rowTotalEarningsData . ' ) AS ' . $rowTotalEarningsData;
            $totalContribution_str .= $plus . '' . $rowETFData;
            $separator = ',';
            $plus = '+';
        }

        $totalContribution_str .= ' ) AS totalContribution';

        $payrollID_arr = $this->db->query("SELECT * FROM (
                                              SELECT payrollMasterID, STR_TO_DATE( CONCAT(payrollYear,'-', payrollMonth, '-01'), '%Y-%m-%d') AS payrollDate
                                              FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND approvedYN=1
                                           ) AS dateTB
                                           WHERE payrollDate BETWEEN '{$fromDate}' AND '{$toDate}' ORDER BY payrollDate")->result_array();
        if (empty($payrollID_arr)) {
            echo '<div class="col-sm-12">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $whereIN = implode(',', array_column($payrollID_arr, 'payrollMasterID'));

        $etfContributionID = null;
        $employerNo = null;
        $otherColumn = '';

        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'etfContribution') {
                $etfContributionID = $reportData['reportValue'];
            } else if ($reportData['inputName'] == 'empNo') {
                $employerNo = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }

        $whereIN_segment = "";
        if (!empty($segment)) {
            $whereIN_segment = 'AND segmentID IN (' . join(',', $segment) . ' )';
        }


        $etfData = $this->db->query("SELECT EIdNo, nic,  CAST(empConfigDet.memNumber AS SIGNED) AS orderColumn,
                                    empConfigDet.* {$otherColumn} , memberContTB.*, {$totalContribution_str}, totalEarningsTB.*
                                    FROM srp_employeesdetails AS empTB
                                    JOIN
                                    (
                                        SELECT empID, $maxETF_str FROM
                                        (
                                            SELECT empID, {$ifConditionETF_str} FROM
                                            (
                                                SELECT empID, payrollMasterID, payYearMonthStr,
                                                round(sum(ABS(transactionAmount)), 2)AS etfContribution, transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                                FROM srp_erp_payrolldetail
                                                JOIN (
                                                     SELECT payrollMasterID AS payMasterID, CONCAT(payrollYear,payrollMonth) AS payYearMonthStr
                                                     FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollMasterID IN ( {$whereIN} )
                                                ) AS payYearMonthStrTB ON payYearMonthStrTB.payMasterID = srp_erp_payrolldetail.payrollMasterID
                                                WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$etfContributionID} {$whereIN_segment}
                                                GROUP BY empID, payrollMasterID
                                            ) AS dataTB
                                        ) AS dataTB2 GROUP BY empID
                                    ) AS memberContTB ON memberContTB.empID = empTB.EIdNo
                                    JOIN
                                    (
                                        SELECT empID AS empIDTotalEarnings, $maxTotalEarnings_str FROM
                                        (
                                            SELECT empID, {$ifConditionTotalEarnings_str} FROM
                                            (
                                                SELECT empID, payrollMasterID, payYearMonthStr,
                                                round(sum(ABS(transactionAmount)), 2)AS totalEarnings, transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                                FROM srp_erp_payrolldetailpaygroup
                                                JOIN (
                                                     SELECT payrollMasterID AS payMasterID, CONCAT(payrollYear,payrollMonth) AS payYearMonthStr
                                                     FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollMasterID IN ( {$whereIN} )
                                                ) AS payYearMonthStrTB ON payYearMonthStrTB.payMasterID = srp_erp_payrolldetailpaygroup.payrollMasterID
                                                WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$totEarningsID} {$whereIN_segment}
                                                GROUP BY empID, payrollMasterID
                                            ) AS dataTB
                                        ) AS dataTB2 GROUP BY empID
                                    ) AS totalEarningsTB ON totalEarningsTB.empIDTotalEarnings = empTB.EIdNo
                                    JOIN (
                                        SELECT empID, MAX(initials) AS initials, MAX(memNumber) AS memNumber, MAX(lastName) AS lastName FROM
                                        (
                                            SELECT empID, reportFields.id, inputName, reportID, reportValue,
                                            IF(inputName='lastName', UPPER(reportValue), '') AS lastName,
                                            IF(inputName='initials', reportValue, '') AS initials,
                                            IF(inputName='memNumber', reportValue, '') AS memNumber
                                            FROM srp_erp_sso_reporttemplatedetails AS reportDetails
                                            JOIN srp_erp_sso_reporttemplatefields AS reportFields ON reportFields.id = reportDetails.reportID
                                            WHERE reportDetails.companyID={$companyID} AND reportFields.isEmployeeLevel=1 AND masterID=2
                                        ) AS tb1 GROUP  BY empID
                                    ) AS empConfigDet ON empConfigDet.empID = empTB.EIdNo
                                    WHERE Erp_companyID={$companyID} ORDER BY orderColumn ASC")->result_array();

        //echo $this->db->last_query();
        //die();

        if (empty($etfData)) {
            echo '<div class="col-sm-12">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $employerNo_arr = explode(' ', $employerNo);
        if (count($employerNo_arr) > 1) {
            $employerNo = $employerNo_arr[0] . '&nbsp;' . $employerNo_arr[1];
        }

        $data['payrollMastersData'] = $payrollMastersData;
        $data['employerNo'] = $employerNo;
        $data['etfData'] = $etfData;
        $data['report_companyData'] = $report_companyData;
        $data['responseType'] = $responseType;


        if ($responseType == 'print') {
            $period = $fromDate_obj->format('F') . ' TO ' . $toDate_Obj->format('F Y');
            $data['period'] = $period;
            $html = $this->load->view('system/hrm/report/etf_return_view', $data, true);
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4-L', 1);
        } else if ($responseType == 'excel') {
            $period = $fromDate_obj->format('F') . ' - ' . $toDate_Obj->format('F Y');
            $data['period'] = $period;
            $this->etfReturn_reportGenerate_excel($data);
        } else {
            $period = $fromDate_obj->format('F') . ' TO ' . $toDate_Obj->format('F Y');
            $data['period'] = $period;
            $this->load->view('system/hrm/report/etf_return_view', $data);
        }
    }

    function etfReturn_reportGenerate_excel($data)
    {
        $payrollMastersData = $data['payrollMastersData'];

        $header = '<table>';
        $header .= '<thead>';
        $header .= '<tr>';
        $header .= '<th rowspan="2">#</th>';
        $header .= '<th rowspan="2">MEMBERS NAME</th>';
        $header .= '<th rowspan="2">NIC No</th>';
        $header .= '<th rowspan="2">MEM S No</th>';
        $header .= '<th>TOTAL</th>';

        foreach ($payrollMastersData as $row) {
            $header .= '<th colspan="2">' . $row['payrollMonth'] . '</th>';
        }

        $header .= '</tr>';
        $header .= '<tr>';
        $header .= '<th> CONTRIB.</th>';

        foreach ($payrollMastersData as $rowData) {
            $header .= '<th>Earnings</th>';
            $header .= '<th>Contrib.</th>';
        }

        $header = array('#', 'MEMBERS NAME', 'NIC No');
        $this->load->library('excel');

        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Users list');

        // Header
        $this->excel->getActiveSheet()->fromArray($header, null, 'A1');

        $filename = 'ETF Return ' . $data['period'] . '.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');

    }

    function save_payeeSetUp()
    {
        $this->form_validation->set_rules('payeeID', 'Payee', 'trim|required');
        $this->form_validation->set_rules('cashBenefits', 'Cash Benefits', 'trim|required');
        $this->form_validation->set_rules('regNo', 'PAYE Registration No', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $this->db->trans_start();
            $payeeID = $this->input->post('payeeID');
            $cashBenefits = $this->input->post('cashBenefits');
            $regNo = $this->input->post('regNo');

            $where = array(
                'masterID' => 4,
                'companyID' => current_companyID()
            );

            $this->db->delete('srp_erp_sso_reporttemplatedetails', $where);

            $data['masterID'] = '4';
            $data['reportID'] = '1'; /*Payee*/
            $data['reportValue'] = $payeeID;
            $data['companyID'] = current_companyID();
            $data['companyCode'] = current_companyCode();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdUserGroup'] = current_user_group();
            $data['createdUserName'] = current_employee();
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_sso_reporttemplatedetails', $data);


            $data['reportID'] = '2'; /*Cash Benefits*/
            $data['reportValue'] = $cashBenefits;
            $this->db->insert('srp_erp_sso_reporttemplatedetails', $data);


            $data['reportID'] = '3'; /*PAYE Reg No*/
            $data['reportValue'] = $regNo;
            $this->db->insert('srp_erp_sso_reporttemplatedetails', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                echo json_encode(['s', 'Process successfully']);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in process']);
            }
        }
    }

    function income_tax_deduction_old()
    {
        $responseType = $this->uri->segment(3);
        $rpt_type = $this->input->post('rpt_type');
        $payrollMonth = $this->input->post('payrollMonth');
        $segment = $this->input->post('segment');

        $payYear = date('Y', strtotime($payrollMonth));
        $payMonth = date('m', strtotime($payrollMonth));

        $companyID = current_companyID();
        $reportConfig = get_defaultPayeeSetup();
        $payeeID = $reportConfig['payee']; // Actually pay group id
        $cashBenefitID = $reportConfig['payGroup']; // Actually pay group id


        $month_where = "AND payrollMonth={$payMonth}";
        $display_from = date('d/m/Y', strtotime($payrollMonth));
        if($rpt_type == 'Y'){
            $month_where = "AND payrollMonth BETWEEN 1 AND {$payMonth}";
            $display_from = date('01/01/Y', strtotime($payrollMonth));
        }


        $payrollID_arr = $this->db->query("SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} 
                                               AND payrollYear={$payYear} {$month_where} AND approvedYN=1")->result_array();

        if (empty($payrollID_arr)) {
            echo '<div class="" style="margin-top: 10px;">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $whereINPayroll = implode(',', array_column($payrollID_arr, 'payrollMasterID'));


        $whereIN_segment1 = "";
        $whereIN_segment2 = "";
        if (!empty($segment)) {
            $whereIN_segment1 = 'AND payrolldetail.segmentID IN (' . join(',', $segment) . ' )';
            $whereIN_segment2 = 'AND payrollGroup.segmentID IN (' . join(',', $segment) . ' )';
        }

        $payeeData = $this->db->query("SELECT NIC, ABS( FLOOR(SUM(payrolldetail.transactionAmount)) ) AS payee,
                                       ABS( FLOOR(SUM(payrollGroup.transactionAmount)) ) AS cashBenefit, Ename1 AS fullName
                                       FROM srp_erp_payrollmaster AS payrollmaster
                                       JOIN srp_erp_payrolldetail AS payrolldetail ON payrolldetail.payrollMasterID=payrollmaster.payrollMasterID
                                       AND payrolldetail.companyID={$companyID} AND payrolldetail.payrollMasterID IN ( {$whereINPayroll} ) {$whereIN_segment1}
                                       AND payrolldetail.detailTBID={$payeeID} AND payrolldetail.calculationTB='PAY_GROUP'
                                       JOIN srp_erp_payrolldetailpaygroup AS payrollGroup ON payrollGroup.payrollMasterID=payrollmaster.payrollMasterID
                                       AND payrollGroup.companyID={$companyID} AND payrollGroup.payrollMasterID IN ( {$whereINPayroll} ) {$whereIN_segment2}
                                       AND payrollGroup.detailTBID={$cashBenefitID} AND payrolldetail.empID=payrollGroup.empID
                                       JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=payrolldetail.empID AND Erp_companyID={$companyID}
                                       WHERE payrollmaster.companyID={$companyID} AND payrollmaster.payrollMasterID IN ( {$whereINPayroll} ) 
                                       GROUP BY payrolldetail.empID
                                       #LIMIT 10")->result_array();

        if (empty($payeeData)) {
            echo '<div class="" style="margin-top: 10px;">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $data['payeeData'] = $payeeData;
        $data['fromDate'] = $display_from;
        $data['toDate'] = date('t/m/Y', strtotime($payrollMonth));
        $this->load->library('NumberToWords');

        if ($responseType == 'print') {
            echo $this->load->view('system/hrm/report/income_tax_deduction_print', $data, true);
        } else if ($responseType == 'excel') {
            //$this->etfReturn_reportGenerate_excel($data);
        } else {
            $this->load->view('system/hrm/report/income_tax_deduction_view', $data);
        }
    }

    function income_tax_deduction()
    {
        $responseType = $this->uri->segment(3);
        $segment = $this->input->post('segment');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');

        $companyID = current_companyID();
        $reportConfig = get_defaultPayeeSetup();
        $payeeID = $reportConfig['payee']; // Actually pay group id
        $cashBenefitID = $reportConfig['payGroup']; // Actually pay group id

        $from_date = date('Y-m-d', strtotime("$from_date-01"));
        $to_date = date('Y-m-t', strtotime("$to_date-01"));

        if($from_date > $to_date){
            $str = '<div class="" style="margin-top: 10px;">
                       <div class="alert alert-danger"><strong>Warning!</strong> </br>To date must be greater than From date</div>
                    </div>';
            die($str);
        }


        $payrollID_arr = $this->db->query("SELECT payrollMasterID, STR_TO_DATE(CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') AS pay_date 
                                                FROM srp_erp_payrollmaster WHERE companyID = {$companyID} AND approvedYN=1
                                                HAVING pay_date BETWEEN '{$from_date}' AND '{$to_date}' ")->result_array();

        if (empty($payrollID_arr)) {
            echo '<div class="" style="margin-top: 10px;">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $whereINPayroll = implode(',', array_column($payrollID_arr, 'payrollMasterID'));


        $whereIN_segment1 = "";
        $whereIN_segment2 = "";
        if (!empty($segment)) {
            $whereIN_segment1 = 'AND payrolldetail.segmentID IN (' . join(',', $segment) . ' )';
            $whereIN_segment2 = 'AND payrollGroup.segmentID IN (' . join(',', $segment) . ' )';
        }

        $payeeData = $this->db->query("SELECT NIC, Ename1 AS fullName, designation_str, ABS( FLOOR(SUM(payrollGroup.transactionAmount)) ) AS cashBenefit,
                                       ABS( FLOOR(SUM(payrolldetail.transactionAmount)) ) AS payee, empTB.EPassportNO AS passportNo, empTB.payee_emp_type, ssoNo   
                                       FROM srp_erp_payrollmaster AS payrollmaster
                                       JOIN srp_erp_payrolldetail AS payrolldetail ON payrolldetail.payrollMasterID=payrollmaster.payrollMasterID
                                       AND payrolldetail.companyID={$companyID} AND payrolldetail.payrollMasterID IN ( {$whereINPayroll} ) {$whereIN_segment1}
                                       AND payrolldetail.detailTBID={$payeeID} AND payrolldetail.calculationTB='PAY_GROUP'
                                       JOIN srp_erp_payrolldetailpaygroup AS payrollGroup ON payrollGroup.payrollMasterID=payrollmaster.payrollMasterID
                                       AND payrollGroup.companyID={$companyID} AND payrollGroup.payrollMasterID IN ( {$whereINPayroll} ) {$whereIN_segment2}
                                       AND payrollGroup.detailTBID={$cashBenefitID} AND payrolldetail.empID=payrollGroup.empID
                                       JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=payrolldetail.empID AND Erp_companyID={$companyID}                                       
                                       LEFT JOIN (
                                            SELECT t1.empID, head_tb.Designation AS designation_str
                                            FROM (
                                                  SELECT  MAX(STR_TO_DATE(CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') ) AS pay_date, 
                                                  EmpID AS empID, pm.payrollMasterID FROM srp_erp_payrollmaster AS pm
                                                  JOIN srp_erp_payrollheaderdetails AS payrolldetail ON payrolldetail.payrollMasterID = pm.payrollMasterID 
                                                  AND payrolldetail.companyID = {$companyID} AND payrolldetail.payrollMasterID IN ( {$whereINPayroll} ) 
                                                  GROUP BY EmpID
                                            ) AS t1
                                            JOIN srp_erp_payrollheaderdetails AS head_tb ON t1.empID = head_tb.EmpID AND head_tb.companyID = {$companyID} 
                                            JOIN srp_erp_payrollmaster AS pm_tb ON pm_tb.payrollMasterID = head_tb.payrollMasterID
                                            AND pm_tb.companyID = {$companyID} AND pm_tb.payrollYear = SUBSTRING(pay_date, 1, 4) 
                                            AND pm_tb.payrollMonth = SUBSTRING(pay_date, 6, 2) 
                                       ) AS desg ON desg.empID = empTB.EIdNo
                                       WHERE payrollmaster.companyID={$companyID} AND payrollmaster.payrollMasterID IN ( {$whereINPayroll} ) 
                                       GROUP BY payrolldetail.empID #LIMIT 10")->result_array();

        if (empty($payeeData)) {
            echo '<div class="" style="margin-top: 10px;">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $data['payeeData'] = $payeeData;
        $data['fromDate'] = $from_date;
        $data['toDate'] = date('Y-m-d', strtotime($to_date));
        $this->load->library('NumberToWords');

        if ($responseType == 'print') {
            echo $this->load->view('system/hrm/report/income_tax_deduction_print', $data, true);
        } else if ($responseType == 'View') {
            $this->load->view('system/hrm/report/income_tax_deduction_table_view', $data);
        } else if ($responseType == 'excel') {
            $this->excel_income_tax_deduction($data);
        } else {
            $this->load->view('system/hrm/report/income_tax_deduction_view', $data);
        }
    }

    function excel_income_tax_deduction($data){
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);

        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('PAYEE');

        $header = [
            '#', 'Name of Employee with Initials', 'Designation', 'Employment From Date', 'Employment To Date', 'Cash Payment',
            'Non-Cash Benefits', 'Total Remuneration', 'Total Tax Exempt / Excluded Income', 'Tax deducted under Primary Employment',
            'Tax deducted under Secondary Employment', 'Total Tax Deducted', 'Employee NIC', 'Passport No.', 'TIN'
        ];
        $this->excel->getActiveSheet()->fromArray($header, null, 'A1');

        $det = [];
        $tot_deduction = 0; $pri_tot = 0; $sec = 0; $dPlace = 2;
        $n = 2;
        foreach ($data['payeeData'] as $key=>$row){

            $pri_am = ($row['payee_emp_type'] == 1)? round($row['payee'], $dPlace): round(0, $dPlace);
            $sec_am = ($row['payee_emp_type'] != 1)? round($row['payee'], $dPlace): round(0, $dPlace);
            $pri_tot += round($pri_am, $dPlace);
            $sec += round($sec_am, $dPlace);
            $tot_deduction += round($row['payee'], $dPlace);

            $det[] = [
                ($key+1), $row['fullName'], $row['designation_str'], $data['fromDate'], $data['toDate'],
                round($row['cashBenefit'], $dPlace), 0, round($row['cashBenefit'], $dPlace), 0, round($pri_am, $dPlace),
                round($sec_am, $dPlace), round($row['payee'], $dPlace), $row['NIC'], $row['passportNo'], $row['ssoNo']
            ];
            $n++;
        }

        $det[] = [
            '', '', '', '', '', '', '', '', '', '', '', round($tot_deduction, $dPlace)
        ];

        $this->excel->getActiveSheet()->fromArray($det, null, 'A2');

        $format_decimal = '#,##0.00';
        $this->excel->getActiveSheet()->getStyle("F2:F{$n}")->getNumberFormat()->setFormatCode($format_decimal);
        $this->excel->getActiveSheet()->getStyle("H2:H{$n}")->getNumberFormat()->setFormatCode($format_decimal);
        $this->excel->getActiveSheet()->getStyle("J2:J{$n}")->getNumberFormat()->setFormatCode($format_decimal);
        $this->excel->getActiveSheet()->getStyle("K2:K{$n}")->getNumberFormat()->setFormatCode($format_decimal);
        $n++;
        $this->excel->getActiveSheet()->getStyle("L2:L{$n}")->getNumberFormat()->setFormatCode($format_decimal);

        $fromDate = date('Y-m', strtotime($data['fromDate']));
        $toDate = date('Y-m', strtotime($data['toDate']));
        $filename = 'PAYEE-'.current_companyCode()." {$fromDate} to {$toDate}.xls";
        header('Content-Type: application/vnd.ms-excel;charset=utf-16');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function payee_registration()
    {
        $responseType = $this->uri->segment(3);
        $segment = $this->input->post('segment');
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');

        $fromDate = $fromDate . '-01';
        $toDate = $toDate . '-01';
        $fromDate_obj = DateTime::createFromFormat('Y-m-d', $fromDate);
        $fromDate = $fromDate_obj->format('Y-m-d');

        $toDate_Obj = DateTime::createFromFormat('Y-m-d', $toDate);
        $toDate = $toDate_Obj->format('Y-m-t');

        $companyID = current_companyID();
        $reportConfig = get_defaultPayeeSetup();
        $payeeID = $reportConfig['payee']; // Actually pay group id
        $cashBenefitID = $reportConfig['payGroup']; // Actually pay group id
        $data['regNo'] = $reportConfig['regNo']; // PAYE registration no.
        $data['fromDate'] = date('d-m-Y', strtotime($fromDate));
        $data['toDate'] = date('d-m-Y', strtotime($toDate));


        $payrollID_arr = $this->db->query("SELECT * FROM (
                                                SELECT payrollMasterID, MONTHNAME(STR_TO_DATE(payrollMonth, '%m')) AS payrollMonth,
                                                STR_TO_DATE( CONCAT(payrollYear,'-', payrollMonth, '-01'), '%Y-%m-%d') AS payrollDate,
                                                CONCAT(payrollYear, payrollMonth) AS payYearMonth
                                                FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND approvedYN=1
                                            ) AS dateTB
                                            WHERE payrollDate BETWEEN '{$fromDate}' AND '{$toDate}'")->result_array();

        if (empty($payrollID_arr)) {
            echo '<div class="" style="margin-top: 10px;">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $whereINPayroll = implode(',', array_column($payrollID_arr, 'payrollMasterID'));


        $whereIN_segment1 = "";
        $whereIN_segment2 = "";
        if (!empty($segment)) {
            $whereIN_segment1 = 'AND payrolldetail.segmentID IN (' . join(',', $segment) . ' )';
            $whereIN_segment2 = 'AND payrollGroup.segmentID IN (' . join(',', $segment) . ' )';
        }

        $payeeData = $this->db->query("SELECT NIC, ABS( FLOOR(SUM(payrolldetail.transactionAmount)) ) AS payee, Ename2 AS nameWithIn,
                                       ABS( FLOOR(SUM(payrollGroup.transactionAmount)) ) AS cashBenefit, DesDescription AS desgination
                                       FROM srp_erp_payrollmaster AS payrollmaster
                                       JOIN srp_erp_payrolldetail AS payrolldetail ON payrolldetail.payrollMasterID=payrollmaster.payrollMasterID
                                       AND payrolldetail.companyID={$companyID} AND payrolldetail.payrollMasterID IN ( {$whereINPayroll} ) {$whereIN_segment1}
                                       AND payrolldetail.detailTBID={$payeeID} AND payrolldetail.calculationTB='PAY_GROUP'
                                       JOIN srp_erp_payrolldetailpaygroup AS payrollGroup ON payrollGroup.payrollMasterID=payrollmaster.payrollMasterID
                                       AND payrollGroup.companyID={$companyID} AND payrollGroup.payrollMasterID IN ( {$whereINPayroll} ) {$whereIN_segment2}
                                       AND payrollGroup.detailTBID={$cashBenefitID} AND payrolldetail.empID=payrollGroup.empID
                                       JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=payrolldetail.empID AND empTB.Erp_companyID={$companyID}
                                       JOIN srp_designation ON srp_designation.DesignationID = empTB.EmpDesignationId AND srp_designation.Erp_companyID={$companyID}
                                       WHERE payrollmaster.companyID={$companyID} AND payrollmaster.payrollMasterID IN ( {$whereINPayroll} )
                                       GROUP BY payrolldetail.empID ORDER BY empTB.EmpSecondaryCode DESC")->result_array();

        if (empty($payeeData)) {
            echo '<div class="" style="margin-top: 10px;">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $data['payeeData'] = $payeeData;

        if ($responseType == 'print') {
            $html = $this->load->view('system/hrm/report/payee_registration_view', $data, true);
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4', 1);
        } else if ($responseType == 'excel') {
            //$this->etfReturn_reportGenerate_excel($data);
        } else {
            $this->load->view('system/hrm/report/payee_registration_view', $data);
        }
    }

    function save_salaryComparisonFormula()
    {
        $companyID = current_companyID();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserName = current_employee();
        $createdDateTime = current_date();

        $masterID = $this->input->post('payGroupID');
        $formulaStr = $this->input->post('formulaString');
        $salaryCategories = $this->input->post('salaryCategoryContainer');
        $salaryCategories = (trim($salaryCategories) == '') ? null : $salaryCategories;
        $ssoCategories = $this->input->post('SSOContainer');
        $ssoCategories = (trim($ssoCategories) == '') ? null : $ssoCategories;
        $payGroupCategories = $this->input->post('payGroupContainer');
        $payGroupCategories = (trim($payGroupCategories) == '') ? null : $payGroupCategories;


        $formulaID = $this->db->query("SELECT formulaID FROM srp_erp_salarycomparisonformula
                                     WHERE masterID={$masterID} AND companyID={$companyID}")->row('formulaID');


        $this->db->trans_start();

        $data = array(
            'formulaStr' => $formulaStr,
            'salaryCategories' => $salaryCategories,
            'ssoCategories' => $ssoCategories,
            'payGroupCategories' => $payGroupCategories
        );

        if (!empty($formulaID)) {
            $data['modifiedUserID'] = $createdPCID;
            $data['modifiedUserID'] = $createdUserID;
            $data['modifiedDateTime'] = $createdDateTime;
            $data['modifiedUserName'] = $createdUserName;

            $this->db->where(array('companyID' => $companyID, 'masterID' => $masterID))->update('srp_erp_salarycomparisonformula', $data);
        } else {
            $data['masterID'] = $masterID;
            $data['companyID'] = $companyID;
            $data['companyCode'] = current_companyCode();
            $data['createdUserGroup'] = current_user_group();
            $data['createdPCID'] = $createdPCID;
            $data['createdUserID'] = $createdUserID;
            $data['createdDateTime'] = $createdDateTime;
            $data['createdUserName'] = $createdUserName;
            $this->db->insert('srp_erp_salarycomparisonformula', $data);
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            echo json_encode(['s', 'Formula successfully updated']);
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in formula update process']);
        }
    }

    function salaryComparison_reportGenerate()
    {
        $this->load->helper('template_paySheet');
        $this->load->helper('employee');

        $companyID = current_companyID();
        $responseType = $this->uri->segment(3);
        $firstMonth = $this->input->post('firstMonth');
        $secondMonth = $this->input->post('secondMonth');
        $isNonPayroll = 'N';
        $salary_categories_arr = salary_categories(array('A', 'D'));
        $payGroup_arr = get_payGroup();

        $firstMonth_arr = $this->db->query("SELECT payrollMasterID FROM(
                                                SELECT payrollMasterID, DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') payrollDate
                                                FROM srp_erp_payrollmaster WHERE companyID={$companyID}
                                             ) AS payrollDateTB WHERE payrollDate='{$firstMonth}' ")->result_array();


        $secondMonth_arr = $this->db->query("SELECT payrollMasterID FROM(
                                                SELECT payrollMasterID, DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') payrollDate
                                                FROM srp_erp_payrollmaster WHERE companyID={$companyID}
                                             ) AS payrollDateTB WHERE payrollDate='{$secondMonth}' ")->result_array();


        $firstMonth_whereIN = implode(',', array_column($firstMonth_arr, 'payrollMasterID'));
        $secondMonth_whereIN = implode(',', array_column($secondMonth_arr, 'payrollMasterID'));
        $allMonth_arr = array_merge($firstMonth_arr, $secondMonth_arr);
        $allMonth_whereIN = implode(',', array_column($allMonth_arr, 'payrollMasterID'));


        $formula_arr = get_salaryComparison();


        $selectStr = '';
        foreach ($formula_arr as $key => $rowFormula) {
            $masterID = $rowFormula['masterID'];
            $formulaBuilder = payGroup_formulaBuilder_to_sql('decode', $rowFormula['formulaStr'], $salary_categories_arr, $payGroup_arr);

            $formulaDecode = $formulaBuilder['formulaDecode'];
            $select_monthlyAD_str = trim($formulaBuilder['select_monthlyAD_str'] ?? '');
            $select_salCat_str = trim($formulaBuilder['select_salaryCat_str'] ?? '');
            $select_group_str = trim($formulaBuilder['select_group_str'] ?? '');
            $whereInClause = trim($formulaBuilder['whereInClause'] ?? '');
            $where_MA_MD_Clause = $formulaBuilder['where_MA_MD_Clause'];
            $whereInClause_group = trim($formulaBuilder['whereInClause_group'] ?? '');


            $where_MA_MD_Clause_str = '';
            if (!empty($where_MA_MD_Clause)) {
                if (count($where_MA_MD_Clause) > 1) {
                    $where_MA_MD_Clause_str = ' calculationTB = \'' . $where_MA_MD_Clause[0] . '\' OR calculationTB = \'' . $where_MA_MD_Clause[1] . '\'';
                } else {
                    $where_MA_MD_Clause_str = ' calculationTB = \'' . $where_MA_MD_Clause[0] . '\'';
                }
            }


            if ($select_monthlyAD_str != '') {
                $select_monthlyAD_str .= ',';
            }

            if ($whereInClause != '' && $select_salCat_str != '') {
                $select_salCat_str .= ',';
                $whereInClause = 'salCatID IN (' . $whereInClause . ') AND calculationTB = \'SD\'';

            }

            if ($whereInClause_group != '' && $select_group_str != '') {
                $select_group_str .= ',';
                $whereInClause_group = 'detailTBID IN (' . $whereInClause_group . ') AND fromTB = \'PAY_GROUP\'';
            }


            if ($whereInClause != '' && $whereInClause_group != '') {
                $whereIN = $whereInClause . ' OR ' . $whereInClause_group;
            } else {
                $whereIN = $whereInClause . ' ' . $whereInClause_group;
            }

            if (trim($whereIN) == '') {
                $whereIN = (trim($where_MA_MD_Clause_str) == '') ? '' : 'AND (' . $where_MA_MD_Clause_str . ' )';
            } else {
                $MA_MD_Clause_str_join = (trim($where_MA_MD_Clause_str) == '') ? '' : ' OR ' . $where_MA_MD_Clause_str;
                $whereIN = 'AND (' . $whereIN . ' ' . $MA_MD_Clause_str_join . ')';
            }


            $detailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrolldetail' : 'srp_erp_payrolldetail';


            $description_str = trim(implode('_', explode(' ', $rowFormula['description'])));


            $selectStr .= " LEFT JOIN (
                               SELECT calculationTB.empID AS empNo, round((" . $formulaDecode . "), transactionCurrencyDecimalPlaces) AS fr_amount_{$masterID}
                               FROM (
                                     SELECT payDet.empID, fromTB, detailType, salCatID, " . $select_salCat_str . " " . $select_group_str . " " . $select_monthlyAD_str . "
                                     transactionCurrencyDecimalPlaces
                                     FROM {$detailTB} AS payDet
                                     JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = payDet.empID AND empTB.Erp_companyID={$companyID}
                                     WHERE payDet.companyID = {$companyID} AND payrollMasterID IN ({$firstMonth_whereIN}) {$whereIN}
                                     GROUP BY payDet.empID, salCatID, payDet.fromTB, detailTBID
                               ) calculationTB GROUP BY empID
                           ) AS fr_{$description_str}TB ON fr_{$description_str}TB.empNo=payHeader.EmpID ";

            $selectStr .= " LEFT JOIN (
                               SELECT calculationTB.empID AS empNo, round((" . $formulaDecode . "), transactionCurrencyDecimalPlaces) AS sn_amount_{$masterID}
                               FROM (
                                     SELECT payDet.empID, fromTB, detailType, salCatID, " . $select_salCat_str . " " . $select_group_str . " " . $select_monthlyAD_str . "
                                     transactionCurrencyDecimalPlaces
                                     FROM {$detailTB} AS payDet
                                     JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = payDet.empID AND empTB.Erp_companyID={$companyID}
                                     WHERE payDet.companyID = {$companyID} AND payrollMasterID IN ({$secondMonth_whereIN}) {$whereIN}
                                     GROUP BY payDet.empID, salCatID, payDet.fromTB, detailTBID
                               ) calculationTB GROUP BY empID
                           ) AS sn_{$description_str}TB ON sn_{$description_str}TB.empNo=payHeader.EmpID ";


        }

        $records = $this->db->query("SELECT Ecode, Ename2, transactionCurrencyDecimalPlaces AS dPlace, fr_amount_1, sn_amount_1, fr_amount_2, sn_amount_2, fr_amount_3,
                                     sn_amount_3, fr_amount_4, sn_amount_4, fr_amount_5, sn_amount_5, fr_amount_6, sn_amount_6, fr_amount_7, sn_amount_7
                                     FROM srp_erp_payrollmaster AS payMaster
                                     JOIN srp_erp_payrollheaderdetails AS payHeader ON payHeader.payrollMasterID = payMaster.payrollMasterID
                                     AND payHeader.companyID={$companyID}
                                     {$selectStr}
                                     WHERE payMaster.companyID={$companyID} AND payMaster.payrollMasterID IN ({$allMonth_whereIN})
                                     GROUP BY payHeader.EmpID")->result_array();

        $data['reportData'] = $records;
        $html = $this->load->view('system/hrm/report/salary-comparison-table-view', $data, true);

        if ($responseType == 'print') {
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4', 1);
        } else {
            echo $html;
        }
    }

    function salaryComparisonDet_reportGenerate()
    {
        $companyID = current_companyID();
        $responseType = $this->uri->segment(3);
        $firstMonth = $this->input->post('firstMonth');
        $secondMonth = $this->input->post('secondMonth');

        $firstMonth_arr = $this->db->query("SELECT payrollMasterID FROM(
                                                SELECT payrollMasterID, DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') payrollDate
                                                FROM srp_erp_payrollmaster WHERE companyID={$companyID}
                                             ) AS payrollDateTB WHERE payrollDate='{$firstMonth}' ")->result_array();


        $secondMonth_arr = $this->db->query("SELECT payrollMasterID FROM(
                                                SELECT payrollMasterID, DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') payrollDate
                                                FROM srp_erp_payrollmaster WHERE companyID={$companyID}
                                             ) AS payrollDateTB WHERE payrollDate='{$secondMonth}' ")->result_array();

        $sal_cat = $this->db->query("SELECT salaryCategoryID, salaryDescription FROM srp_erp_pay_salarycategories  
                                         WHERE companyID={$companyID} #AND salaryCategoryID IN (59,64,66)")->result_array();
        $sal_cat[] = [ 'salaryCategoryID'=> 'MA', 'salaryDescription'=> 'Monthly Addition' ];
        $sal_cat[] = [ 'salaryCategoryID'=> 'MD', 'salaryDescription'=> 'Monthly Deduction' ];

        $firstMonth_whereIN = implode(',', array_column($firstMonth_arr, 'payrollMasterID'));
        $secondMonth_whereIN = implode(',', array_column($secondMonth_arr, 'payrollMasterID'));
        $allMonth_arr = array_merge($firstMonth_arr, $secondMonth_arr);
        $allMonth_whereIN = implode(',', array_column($allMonth_arr, 'payrollMasterID'));


        $emp_list = $this->db->query("SELECT empTB.EIdNo AS empID, empTB.ECode AS empCode, empTB.Ename2 AS empName
                                        FROM srp_erp_payrollheaderdetails AS hed
                                        JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = hed.EmpID
                                        WHERE companyID = {$companyID} AND payrollMasterID IN ({$allMonth_whereIN}) 
                                        #AND empTB.EIdNo IN (208, 209, 210)
                                        GROUP BY EmpID ")->result_array();


        $first_det = $this->db->query("SELECT empID, salCatID AS salKey, SUM(transactionAmount) AS amount, calculationTB
                                        FROM srp_erp_payrolldetail WHERE companyID = {$companyID} AND payrollMasterID IN ({$firstMonth_whereIN})
                                        AND calculationTB = 'SD' GROUP BY empID, salCatID
                                        UNION ALL
                                        SELECT empID, calculationTB AS salKey, SUM(transactionAmount) AS amount, calculationTB
                                        FROM srp_erp_payrolldetail WHERE companyID = {$companyID} AND payrollMasterID IN ({$firstMonth_whereIN})
                                        AND calculationTB = 'MA' GROUP BY empID 
                                        UNION ALL
                                        SELECT empID, calculationTB AS salKey, SUM(transactionAmount) AS amount, calculationTB
                                        FROM srp_erp_payrolldetail WHERE companyID = {$companyID} AND payrollMasterID IN ({$firstMonth_whereIN})
                                        AND calculationTB = 'MD' GROUP BY empID ")->result_array();

        $sec_det = $this->db->query("SELECT empID, salCatID AS salKey, SUM(transactionAmount) AS amount, calculationTB
                                        FROM srp_erp_payrolldetail WHERE companyID = {$companyID} AND payrollMasterID IN ({$secondMonth_whereIN})
                                        AND calculationTB = 'SD' GROUP BY empID, salCatID
                                        UNION ALL
                                        SELECT empID, calculationTB AS salKey, SUM(transactionAmount) AS amount, calculationTB
                                        FROM srp_erp_payrolldetail WHERE companyID = {$companyID} AND payrollMasterID IN ({$secondMonth_whereIN})
                                        AND calculationTB = 'MA' GROUP BY empID 
                                        UNION ALL
                                        SELECT empID, calculationTB AS salKey, SUM(transactionAmount) AS amount, calculationTB
                                        FROM srp_erp_payrolldetail WHERE companyID = {$companyID} AND payrollMasterID IN ({$secondMonth_whereIN})
                                        AND calculationTB = 'MD' GROUP BY empID ")->result_array();

        $data['sal_cat'] = $sal_cat;
        $data['emp_list'] = $emp_list;
        $data['first_det'] = $first_det;
        $data['sec_det'] = $sec_det;
        $html = $this->load->view('system/hrm/report/salary-comparison-table-view2', $data, true);

        if ($responseType == 'print') {
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4-L', 1);
        } else {
            echo $html;
        }
    }

    function load_subcat()
    {
        echo json_encode($this->Report_model->load_subcat());
    }

    function load_subsubcat()
    {
        echo json_encode($this->Report_model->load_subsubcat());
    }

    function loadItems()
    {
        if ($this->input->post("type") == 1) {
            echo json_encode($this->Report_model->loadItems());
        } else {
            echo json_encode($this->Report_model->loadGroupItems());
        }
    }

    function get_collection_summery_report()
    {
        $currency = $this->input->post('currency');
        $financeyear = $this->input->post('financeyear');
        $this->db->select('beginingDate,endingDate');
        $this->db->where('companyFinanceYearID', $financeyear);
        $this->db->from('srp_erp_companyfinanceyear ');
        $financeyeardtl = $this->db->get()->row_array();
        $beginingDate = $financeyeardtl['beginingDate'];
        $endingDate = $financeyeardtl['endingDate'];

        $start = (new DateTime($beginingDate));
        $end = (new DateTime($endingDate));

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $datearr = [];
        foreach ($period as $dt) {
            $dat = $dt->format("Y-m");
            $text = $dt->format("Y-M");
            $datearr[$dat] = $text;
        }

        $this->db->select('max(beginingDate) as beginingDate,max(endingDate) as endingDate');
        $this->db->where('beginingDate < ', $beginingDate);
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_companyfinanceyear');
        $previuosyeardtl = $this->db->get()->row_array();

        $previousbegindate = $previuosyeardtl['beginingDate'];
        $previousenddate = $previuosyeardtl['endingDate'];

        //echo '<pre>';print_r($datearr); echo '</pre>'; die();

        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        $this->form_validation->set_rules('segment[]', 'Segment', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->Report_model->get_collection_summery_report($datearr, $previousbegindate, $previousenddate, $beginingDate, $endingDate);
            $data["header"] = $datearr;
            $data["previousbeginingdate"] = $previousbegindate;
            $data["previousenddate"] = $previousenddate;
            $data["type"] = "html";
            $data["currency"] = $currency;
            echo $html = $this->load->view('system/accounts_receivable/report/load-collection-summary-report', $data, true);
        }
    }

    function get_collection_details_drilldown_report()
    {
        $currency = $this->input->post('currency');

        $data["customers"] = $this->Report_model->customer_name();
        $data["details"] = $this->Report_model->get_revanue_details_drilldown_report();
        $data["type"] = "html";
        $data["currency"] = $currency;
        echo $html = $this->load->view('system/accounts_receivable/report/load-collection-summary-dd-report', $data, true);

    }

    function get_collection_summery_report_pdf()
    {
        $currency = $this->input->post('currency');
        $financeyear = $this->input->post('financeyear');
        $this->db->select('beginingDate,endingDate');
        $this->db->where('companyFinanceYearID', $financeyear);
        $this->db->from('srp_erp_companyfinanceyear ');
        $financeyeardtl = $this->db->get()->row_array();
        $beginingDate = $financeyeardtl['beginingDate'];
        $endingDate = $financeyeardtl['endingDate'];

        $start = (new DateTime($beginingDate));
        $end = (new DateTime($endingDate));

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $datearr = [];
        foreach ($period as $dt) {
            $dat = $dt->format("Y-m");
            $text = $dt->format("Y-M");
            $datearr[$dat] = $text;
        }

        $this->db->select('max(beginingDate) as beginingDate,max(endingDate) as endingDate');
        $this->db->where('beginingDate < ', $beginingDate);
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_companyfinanceyear');
        $previuosyeardtl = $this->db->get()->row_array();

        $previousbegindate = $previuosyeardtl['beginingDate'];
        $previousenddate = $previuosyeardtl['endingDate'];

        $data["details"] = $this->Report_model->get_collection_summery_report($datearr, $previousbegindate, $previousenddate, $beginingDate, $endingDate);
        $data["header"] = $datearr;
        $data["type"] = "pdf";
        $data["currency"] = $currency;
        $html = $this->load->view('system/accounts_receivable/report/load-collection-summary-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }


    function get_collection_previous_details_drilldown_report()
    {
        $currency = $this->input->post('currency');

        $data["customers"] = $this->Report_model->customer_name();
        $data["details"] = $this->Report_model->get_revanue_previous_details_drilldown_report();
        $data["type"] = "html";
        $data["currency"] = $currency;
        echo $html = $this->load->view('system/accounts_receivable/report/load-collection-summary-dd-report', $data, true);

    }

    function get_collection_detail_report()
    {
        $currency = $this->input->post('currency');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $customer = $this->input->post('customerID');
        $segment = $this->input->post('segment');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date To is  required
            </div>';
        } else {
            $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
            } else {
                $data["details"] = $this->Report_model->get_collection_detail_reports($currency, $customer, $segment);
                $data["type"] = "html";
                $data["currency"] = $currency;
                echo $html = $this->load->view('system/accounts_receivable/report/load-collection-details-report-html', $data, true);
            }
        }
    }


    function get_collection_detail_report_excel(){
        $currency = $this->input->post('currency');
        $customer = $this->input->post('customerID');
        $segment = $this->input->post('segment');

        $data["details"] = $this->Report_model->get_collection_detail_reports_export($currency, $customer, $segment);
        $data["type"] = "pdf";
        $data["currency"] = $currency;
   
        $base_arr = array();

        foreach($data["details"] as $details){
            $base_arr[] = array('customerSystemCode'=>$details['customerSystemCode'],
                                    'secondaryCode'=>$details['secondaryCode'],
                                    'customerName'=>$details['customerName'],
                                    'invoiceCode'=>$details['invoiceCode'],
                                    'invoiceDate'=>$details['invoiceDate'],
                                    'referenceNo'=>$details['referenceNo'],
                                    'RVcode'=>$details['RVcode'],
                                    'RVdate'=>$details['RVdate'],
                                    'bankName'=>$details['bankName'],
                                    'bankAccountNumber'=>$details['bankAccountNumber'],
                                    'segmentCode'=>$details['segmentCode'],
                                    'transactionCurrency'=>$details['transactionCurrency'],
                                    'transactionAmount'=>$details['transactionAmount'],
                                );
        }


        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Collection Report');

        $header = ['Customer Code', 'Secondary Code','Customer Name', 'Invoice Code', 'Invoice Date', 'Reference', 'RV Code', 'Document Date', 'Bank', 'Account', 'Segment', 'Currency', 'Amount'];
        
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($base_arr, null, 'A6');

        $filename = 'Collection Report.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        $writer = new Xlsx($this->excel);
        $writer->save('php://output');

    }


    function get_collection_detail_report_pdf()
    {
        $currency = $this->input->post('currency');
        $customer = $this->input->post('customerID');
        $segment = $this->input->post('segment');
        $type = $this->input->post('type');

        $data["details"] = $this->Report_model->get_collection_detail_reports_export($currency, $customer, $segment);
        $data["type"] = "pdf";
        $data["currency"] = $currency;

        $html = $this->load->view('system/accounts_receivable/report/load-collection-details-report', $data, true);

        if($type == 'excel'){
            $base_arr = array();

            foreach($data["details"] as $details){
                $base_arr[] = array('customerSystemCode'=>$details['customerSystemCode'],
                                        'secondaryCode'=>$details['secondaryCode'],
                                        'customerName'=>$details['customerName'],
                                        'invoiceCode'=>$details['invoiceCode'],
                                        'invoiceDate'=>$details['invoiceDate'],
                                        'referenceNo'=>$details['referenceNo'],
                                        'RVcode'=>$details['RVcode'],
                                        'RVdate'=>$details['RVdate'],
                                        'bankName'=>$details['bankName'],
                                        'bankAccountNumber'=>$details['bankAccountNumber'],
                                        'segmentCode'=>$details['segmentCode'],
                                        'transactionCurrency'=>$details['transactionCurrency'],
                                        'transactionAmount'=>$details['transactionAmount'],
                                    );
            }


            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);
            $this->excel->getActiveSheet()->setTitle('Collection Report');

            $header = ['Customer Code', 'Secondary Code','Customer Name', 'Invoice Code', 'Invoice Date', 'Reference', 'RV Code', 'Document Date', 'Bank', 'Account', 'Segment', 'Currency', 'Amount'];
            
            $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
            $this->excel->getActiveSheet()->fromArray($base_arr, null, 'A6');

            $filename = 'Collection Report.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
            header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache

            $writer = new Xlsx($this->excel);
            $writer->save('php://output');

        } else {

            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L');
        
        }

    }

    function group_customer_linked()
    {
        return $this->Report_model->group_customer_linked();
    }

    function group_supplier_linked()
    {
        return $this->Report_model->group_supplier_linked();
    }

    function group_chartofaccount_linked()
    {
        return $this->Report_model->group_chartofaccount_linked();
    }

    function group_segment_linked()
    {
        return $this->Report_model->group_segment_linked();
    }

    function group_item_linked()
    {
        return $this->Report_model->group_item_linked();
    }

    function group_warehouse_linked()
    {
        return $this->Report_model->group_warehouse_linked();
    }


    function group_unlink($report)
    {
        $errorHTML = "";
        if (in_array('ITM', $report)) {
            if ($this->group_item_linked()) {
                $errorHTML .= "<h4>Please link the following items</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_item_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }
        if (in_array('WH', $report)) {
            if ($this->group_warehouse_linked()) {
                $errorHTML .= "<h4>Please link the following Warehouse</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_warehouse_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        if (in_array('CUST', $report)) {
            if ($this->group_customer_linked()) {
                $errorHTML .= "<h4>Please link the following customer</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_customer_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        if (in_array('SUPP', $report)) {
            if ($this->group_supplier_linked()) {
                $errorHTML .= "<h4>Please link the following supplier</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_supplier_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        if (in_array('SEG', $report)) {
            if ($this->group_segment_linked()) {
                $errorHTML .= "<h4>Please link the following segment</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_segment_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        if (in_array('CA', $report)) {
            if ($this->group_chartofaccount_linked()) {
                $errorHTML .= "<h4>Please link the following chart of account</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_chartofaccount_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        return $errorHTML;
    }

    function get_customer_balance_report()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);

        if($this->input->post('grouptyp')==1){
            $customerID = $this->input->post('customerID');
        }else{
            $customerID = $this->input->post('customerIDgrp');
        }

        $companyID = current_companyID();

        //echo '<pre>';print_r($datearr); echo '</pre>'; die();


        if($this->input->post('grouptyp')==1){
            $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        }else{
            $this->form_validation->set_rules('customerIDgrp[]', 'Customer', 'required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            if($this->input->post('grouptyp')==1){
                $companyID = current_companyID();
                $company="= $companyID";
            }else{
                $companyID = $this->Report_model->get_group_company();
                $company="IN (" . join(',', $companyID) . ")";
            }

            $qry = "SELECT
                companyLocalCurrency,
                companyReportingCurrency
                FROM
                    `srp_erp_generalledger`
                WHERE
                srp_erp_generalledger.partyAutoID IN (" . join(',', $customerID) . ")
                    AND srp_erp_generalledger.companyID $company
                AND `subLedgerType` = '3'
                and documentDate<='$fromdt'
                group by partyAutoID,GLAutoID";
            $outputcrr = $this->db->query($qry)->row_array();

            $data["details"] = $this->Report_model->get_customer_balance_report($fromdt);
            $data["type"] = "html";
            $data["loccurr"] = $outputcrr['companyLocalCurrency'];
            $data["repcurr"] = $outputcrr['companyReportingCurrency'];
            $data["loccurrDec"] = fetch_currency_desimal($outputcrr['companyLocalCurrency']);
            $data["repcurrDec"] = fetch_currency_desimal($outputcrr['companyReportingCurrency']);
            $data["currency"] = $currency;
            echo $html = $this->load->view('system/accounts_receivable/report/load-customer-balance-report', $data, true);
        }
    }

    function get_customer_balance_report_pdf()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);
        if($this->input->post('grouptyp')==1){
            $customerID = $this->input->post('customerID');
        }else{
            $customerID = $this->input->post('customerIDgrp');
        }

        if($this->input->post('grouptyp')==1){
            $companyID = current_companyID();
            $company="= $companyID";
        }else{
            $companyID = $this->Report_model->get_group_company();
            $company="IN (" . join(',', $companyID) . ")";
        }
        $qry = "SELECT
  companyLocalCurrency,
  companyReportingCurrency
FROM
    `srp_erp_generalledger`
WHERE
srp_erp_generalledger.partyAutoID IN (" . join(',', $customerID) . ")
    AND srp_erp_generalledger.companyID $company
AND `subLedgerType` = '3'
and documentDate<='$fromdt'
group by partyAutoID,GLAutoID";
        $outputcrr = $this->db->query($qry)->row_array();


        $data["details"] = $this->Report_model->get_customer_balance_report($fromdt);
        $data["type"] = "pdf";
        $data["loccurr"] = $outputcrr['companyLocalCurrency'];
        $data["repcurr"] = $outputcrr['companyReportingCurrency'];
        $data["loccurrDec"] = fetch_currency_desimal($outputcrr['companyLocalCurrency']);
        $data["repcurrDec"] = fetch_currency_desimal($outputcrr['companyReportingCurrency']);
        $data["currency"] = $currency;
        $html = $this->load->view('system/accounts_receivable/report/load-customer-balance-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }


    function get_vendor_balance_report()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);

        if($this->input->post('grouptyp')==1){
            $supplierID = $this->input->post('supplierID');
        }else{
            $supplierID = $this->input->post('supplierIDgrp');
        }

        $companyID = current_companyID();


        //echo '<pre>';print_r($datearr); echo '</pre>'; die();
        if($this->input->post('grouptyp')==1){
            $this->form_validation->set_rules('supplierID[]', 'Supplier', 'required');
        }else{
            $this->form_validation->set_rules('supplierIDgrp[]', 'Supplier', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            if($this->input->post('grouptyp')==1){
                $companyID = current_companyID();
                $company="= $companyID";
            }else{
                $companyID = $this->Report_model->get_group_company();
                $company="IN (" . join(',', $companyID) . ")";
            }


            $qry = "SELECT
  companyLocalCurrency,
  companyReportingCurrency
FROM
    `srp_erp_generalledger`
WHERE
srp_erp_generalledger.partyAutoID IN (" . join(',', $supplierID) . ")
    AND srp_erp_generalledger.companyID $company
AND `subLedgerType` = '2'
and documentDate<='$fromdt'
group by partyAutoID,GLAutoID";
            $outputcrr = $this->db->query($qry)->row_array();

            $data["details"] = $this->Report_model->get_vendor_balance_report($fromdt);
            $data["type"] = "html";
            $data["loccurr"] = $outputcrr['companyLocalCurrency'];
            $data["repcurr"] = $outputcrr['companyReportingCurrency'];

            $data["loccurrDec"] = fetch_currency_desimal($outputcrr['companyLocalCurrency']);
            $data["repcurrDec"] = fetch_currency_desimal($outputcrr['companyReportingCurrency']);

            $data["currency"] = $currency;
            echo $html = $this->load->view('system/accounts_payable/report/load-vendor-balance-report', $data, true);
        }
    }

    function get_vendor_balance_report_pdf()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);

        if($this->input->post('grouptyp')==1){
            $supplierID = $this->input->post('supplierID');
        }else{
            $supplierID = $this->input->post('supplierIDgrp');
        }
        if($this->input->post('grouptyp')==1){
            $companyID = current_companyID();
            $company="= $companyID";
        }else{
            $companyID = $this->Report_model->get_group_company();
            $company="IN (" . join(',', $companyID) . ")";
        }

        $qry = "SELECT
  companyLocalCurrency,
  companyReportingCurrency
FROM
    `srp_erp_generalledger`
WHERE
srp_erp_generalledger.partyAutoID IN (" . join(',', $supplierID) . ")
    AND srp_erp_generalledger.companyID $company
AND `subLedgerType` = '2'
and documentDate<='$fromdt'
group by partyAutoID,GLAutoID";
        $outputcrr = $this->db->query($qry)->row_array();


        $data["details"] = $this->Report_model->get_vendor_balance_report($fromdt);
        $data["type"] = "pdf";
        $data["loccurr"] = $outputcrr['companyLocalCurrency'];
        $data["repcurr"] = $outputcrr['companyReportingCurrency'];

        $data["loccurrDec"] = fetch_currency_desimal($outputcrr['companyLocalCurrency']);
        $data["repcurrDec"] = fetch_currency_desimal($outputcrr['companyReportingCurrency']);
        $data["currency"] = $currency;
        $html = $this->load->view('system/accounts_payable/report/load-vendor-balance-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }
    function vendoragingsummary_pdc()
    {
        $to = $this->input->post('to');
        $segments = $this->input->post('segment');
        $fromTo = false;
        $segment = false;
        if (isset($to)) {
            $fromTo = true;
            $data["to"] = $this->input->post('to');
        }
        if (isset($segments)) {
            $segment = true;
        }
        $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
        $data["output"] = $this->Report_model->get_accounts_payable_report_drilldown_pdc($fromTo, $financialBeginingDate);
        $data["fieldName"] = $this->input->post('currency');
        $data["from"] = convert_date_format($this->input->post('from'));
        $data["type"] = "html";
        $this->load->view('system/accounts_payable/report/erp_accounts_payable_drilldown_report_pdc', $data);
    }

    function get_item_exceed_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $item = $this->input->post('item');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date To is  required
            </div>';
        } else {
            $this->form_validation->set_rules('item[]', 'Item', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
            } else {
                $data["details"] = $this->Report_model->get_item_exceed_reports($item);
                $data['RecordsCount'] = $this->Report_model->get_item_exceed_reports_recordCount($item);
                $data["type"] = "html";
                $data["viewZeroBalace"] = $this->input->post('viewZeroBalace');
                echo $html = $this->load->view('system/inventory/report/load-item-exceeded-report', $data, true);
            }
        }
    }

    function get_exceeded_details(){
        $item = $this->input->post('item');
        echo json_encode($this->Report_model->get_item_exceed_reports($item));
    }

    function get_item_exceed_summery_report()
    {
        $datefrom = $this->input->post('datefrom');
//        $dateto = $this->input->post('dateto');
        $item = $this->input->post('itemSum');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
              As of Date is required
            </div>';
        }/* else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
             Date To is  required
            </div>';
        }*/ else {
            $this->form_validation->set_rules('itemSum[]', 'Item', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
            } else {
                $data["details"] = $this->Report_model->get_item_exceed_summery_reports($item);
                $data["type"] = "html";
                $data["viewZeroBalace"] = $this->input->post('viewZeroBalace');
                echo $html = $this->load->view('system/inventory/report/load-item-exceeded-summery-report', $data, true);
            }
        }
    }

    function get_item_match_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date To is  required
            </div>';
        } else {
            $data["details"] = $this->Report_model->get_item_match_report();
            $data["type"] = "html";
            echo $html = $this->load->view('system/inventory/report/load-item-match-report', $data, true);
        }
    }

    function get_item_exceed_report_pdf()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $item = $this->input->post('item');

        $data["details"] = $this->Report_model->get_item_exceed_reports($item , 1);
        $data["type"] = "pdf";
        $data["viewZeroBalace"] =  $this->input->post('viewZeroBalace');
        $data['RecordsCount'] = $this->Report_model->get_item_exceed_reports_recordCount($item);
        $html =  $this->load->view('system/inventory/report/load-item-exceeded-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    function get_item_exceed_report_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Item Exceeded Details Report');
        $this->load->database();

        $item = $this->input->post('item');

        $header = ['#', 'Item','Item Description', 'Document Code', 'Document Date', 'Warehouse', 'UOM', 'ExceededQty', 'MatchedQty', 'BalanceQty', 'Unit Amount', 'Exceeded Amount', 'Matched Amount', 'Balance Amount'];
        $details = $this->Report_model->get_item_exceed_reports_excel($item , 1);

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Item Exceed Report'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A5:U4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A5:U4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($details, null, 'A6');

        $filename = 'Item Exceeded Details Report.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
//        ob_clean();
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function get_item_matching_report_pdf()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $item = $this->input->post('item');

        $data["details"] = $this->Report_model->get_item_match_report();
        $data["type"] = "pdf";

        $html =  $this->load->view('system/inventory/report/load-item-match-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    function get_item_exceed_summary_report_pdf()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $item = $this->input->post('itemSum');
        $data["details"] = $this->Report_model->get_item_exceed_summery_reports($item);
        $data["type"] = "pdf";

        $html =  $this->load->view('system/inventory/report/load-item-exceeded-summery-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    function get_item_match_detail_report()
    {
        $exceededMatchID = $this->input->post('exceededMatchID');

        $data["details"] = $this->Report_model->get_item_match_detail_report($exceededMatchID);
        $data["type"] = "html";
        echo $html = $this->load->view('system/inventory/report/load_item_match_detail_report', $data, true);

    }

    function get_item_matching_detail_report_pdf()
    {
        $exceededMatchID = $this->input->post('exceedmatchid');

        $data["details"] = $this->Report_model->get_item_match_detail_report($exceededMatchID);
        $data["type"] = "pdf";

        $html = $this->load->view('system/inventory/report/load_item_match_detail_report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');

    }

    function get_grade_wise_salary_cost_report()
    {
        $requestType = $this->uri->segment(3);
        $companyID = current_companyID();
        $gradeID = $this->input->post('gradeID');

        $category = $this->db->query("SELECT salaryCategoryType,salaryCategoryID,salaryDescription,deductionPercntage,companyContributionPercentage
                                      FROM srp_erp_pay_salarycategories 
                                      WHERE companyID='{$companyID}' AND isPayrollCategory=1 ORDER BY salaryCategoryType ASC")->result_array();
        $query = '';
        $sum_str = '';
        $as_of_date = date('Y-m-d');
        if ($category) {
            foreach ($category as $key=>$cat) {
                $salaryDescription = "cat_{$key}";
                $query .= "SUM(IF(catTB.salaryCategoryID = {$cat['salaryCategoryID']} , transactionAmount, 0)) AS {$salaryDescription},";
                $sum_str .= "SUM(IFNULL({$salaryDescription}, 0)) AS {$salaryDescription},";
            }
            $query .= "salDec.companyID";
        }

        if ($query == '') {
            $data['details'] = false;
            $data['currency'] = false;
        }
        else {
            $filter = '';
            if (!empty($gradeID)) {
                $commaList = implode(', ', $gradeID);
                $filter .= "AND empTB.gradeID IN($commaList) ";

                $str = '';
                $isGroupAccess = getPolicyValues('PAC', 'All');
                if ($isGroupAccess == 1) {
                    $currentEmp = current_userID();
                    $str = "JOIN (
                        SELECT groupID FROM srp_erp_payrollgroupincharge
                        WHERE companyID={$companyID} AND empID={$currentEmp}
                    ) AS accTb ON accTb.groupID = salDec.accessGroupID";
                }

                $data['details'] = $this->db->query("SELECT EidNo AS employeeNo, srp_erp_segment.description as segment,ECode, Ename2, gradeDescription,
                                                IF(isDischarged = 1, IF( dischargedDate < '{$as_of_date}', 1, 0), 0) isDischarged2,
                                                DesDescription, payCurrency, sal.*
                                                FROM srp_employeesdetails AS empTB 
                                                JOIN srp_erp_employeegrade AS em_grade ON empTB.gradeID = em_grade.gradeID
                                                LEFT JOIN srp_designation ON DesignationID = EmpDesignationId
                                                LEFT JOIN srp_erp_segment on srp_erp_segment.segmentID=empTB.segmentID
                                                AND srp_erp_segment.companyID = {$companyID}
                                                LEFT JOIN (
                                                    SELECT salDec.employeeNo AS empID, transactionCurrency, $query 
                                                    FROM srp_erp_pay_salarydeclartion AS salDec                                                      
                                                    JOIN srp_erp_pay_salarycategories AS catTB ON salDec.salaryCategoryID = catTB.salaryCategoryID
                                                    AND catTB.companyID = {$companyID}   
                                                    WHERE salDec.companyID={$companyID} AND effectiveDate < '{$as_of_date}' GROUP BY employeeNo
                                                ) sal ON empID = EidNo
                                                WHERE empTB.Erp_companyID = {$companyID} AND isPayrollEmployee= 1 AND empConfirmedYN = 1
                                                {$filter} GROUP BY employeeNo, payCurrency HAVING isDischarged2 = 0 ORDER BY ECode")->result_array();

                $data['currency'] = $this->db->query("SELECT {$sum_str}  payCurrency  FROM (SELECT EidNo AS employeeNo, payCurrency,
                                                IF(isDischarged = 1, IF( dischargedDate < '{$as_of_date}', 1, 0), 0) isDischarged2,
                                                sal.*
                                                FROM srp_employeesdetails AS empTB 
                                                JOIN srp_erp_employeegrade AS em_grade ON empTB.gradeID = em_grade.gradeID
                                                LEFT JOIN srp_designation ON DesignationID = EmpDesignationId
                                                LEFT JOIN srp_erp_segment on srp_erp_segment.segmentID=empTB.segmentID
                                                AND srp_erp_segment.companyID = {$companyID}
                                                LEFT JOIN (
                                                    SELECT salDec.employeeNo AS empID, transactionCurrency, $query 
                                                    FROM srp_erp_pay_salarydeclartion AS salDec                                                      
                                                    JOIN srp_erp_pay_salarycategories AS catTB ON salDec.salaryCategoryID = catTB.salaryCategoryID
                                                    AND catTB.companyID = {$companyID}   
                                                    WHERE salDec.companyID={$companyID} AND effectiveDate < '{$as_of_date}' GROUP BY employeeNo
                                                ) sal ON empID = EidNo
                                                WHERE empTB.Erp_companyID = {$companyID} AND isPayrollEmployee= 1 AND empConfirmedYN = 1
                                                {$filter} GROUP BY employeeNo, payCurrency  
                                                ) AS t1 WHERE isDischarged2 = 0 
                                                GROUP BY payCurrency ")->result_array();

            }
            else {
                $data['details'] = false;
                $data['currency'] = false;
            }


        }
        $data['category'] = $category;
        $data['grade'] = $gradeID;
        //echo '<pre>'; print_r($data['details']); echo '</pre>';
        if ($requestType == 'pdf') {
            $data['is_print'] = 'Y';
            $html = $this->load->view('system/hrm/ajax/grade-wise-salary-cost-ajax.php', $data, true);
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4-L');
        } else {
            $data['is_print'] = 'N';
            echo $html = $this->load->view('system/hrm/ajax/grade-wise-salary-cost-ajax.php', $data, true);
        }
    }

    function get_gratuity_salary_report(){
        $this->form_validation->set_rules('as_of_date', 'As Of', 'trim|required');
        $this->form_validation->set_rules('gratuityID[]', 'Gratuity Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $msg = '<div class="alert alert-warning" style="ma">'.validation_errors().'</div>';
            die( $msg );
        }

        $this->load->helper('employee');
        $requestType = $this->uri->segment(3);
        $companyID = current_companyID();
        $as_of_date_str = $as_of_date = $this->input->post('as_of_date');
        $date_format_policy = date_format_policy();
        $as_of_date = input_format_date($as_of_date, $date_format_policy);
        $firstDate = date('Y-m-01', strtotime($as_of_date));
        $convertFormat = convert_date_format_sql();
        $gratuity_arr = $this->input->post('gratuityID');
        $gratuity_list = join(',', $gratuity_arr);

        $previousMonth = $this->input->post('previousMonth');

        $gratuityMaster = $this->db->query("SELECT t1.gratuityID, gratuityDescription, formulaString, provisionGL                                 
                            FROM srp_erp_pay_gratuitymaster t1 JOIN srp_erp_pay_gratuityformula t2 ON t2.autoID = t1.gratuityID 
                            AND t1.gratuityID IN ({$gratuity_list}) AND t2.masterType='GRATUITY' ")->result_array();

        $gr_data = []; $currency = [];
        foreach ($gratuityMaster as $gr_mas_data) {
            $dPlace = 3;
            $gratuityID = $gr_mas_data['gratuityID'];
            $gratuitySlabData = $this->db->query("SELECT t1.id, slabTitle, formulaString, startYear, endYear FROM srp_erp_pay_gratuityslab t1                                    
                                    JOIN srp_erp_pay_gratuityformula t3 ON t3.autoID = t1.id AND t3.masterType='GRATUITY-SLAB'
                                    WHERE t1.gratuityMasterID='{$gratuityID}'")->result_array();

            $slab_wise = [];
            $details = [];
            $slabStr = '';
            $formula = '';
            $salCat2 = '';
            $salCat = '';
            $whereInClause = '';
            if (!empty($gratuitySlabData)) {
                $slabStr = 'round((CASE';
                foreach ($gratuitySlabData as $slabKey => $slabData) {
                    $gr_data[$gratuityID]['slab_det'][$slabData['id']] = $slabData['slabTitle'];
                    $endYear = $slabData['endYear'];
                    $result_slab = formulaBuilder_to_sql_simple_conversion($slabData['formulaString']);
                    $formula_slab = $result_slab['formulaDecode'];

                    $slabStr .= ' WHEN (totalWorkingDays/365) <= ' . $endYear . ' THEN ' . $formula_slab;
                    $slab_wise[$slabData['id']] = $endYear - 0.001;
                }
                $slabStr .= ' ELSE 0 END), ' . $dPlace . ') AS gratuityAmount';

                $result = formulaBuilder_to_sql_simple_conversion($gr_mas_data['formulaString']);

                $formula = $result['formulaDecode'];
                $salCat = $result['select_str'];
                $salCat2 = $result['select_str2'];
                $whereInClause = $result['whereInClause'];

               $details = $this->db->query("SELECT empID, totalWork, totFixPayment, joinDate, designation, payCurrencyID, ECode, Ename2, {$slabStr} FROM (
                                            SELECT empID, totalWorkingDays, CONCAT(age,'Y ',days,'D') AS totalWork, joinDate, designation,
                                            payCurrencyID, ECode, Ename2, {$formula} AS totFixPayment
                                            FROM (
                                                SELECT empID, DATE_FORMAT(EDOJ,'{$convertFormat}') AS joinDate, DesDescription AS designation, 
                                                payCurrencyID, ECode, Ename2, 
                                                IF (
                                                    isDischarged = 0 OR dischargedDate >= '{$as_of_date}', DATEDIFF( '{$as_of_date}', EDOJ ),
                                                    IF ( finalSettlementDoneYN = 0, DATEDIFF( dischargedDate, EDOJ ), 0 )
                                                ) AS totalWorkingDays,
                                                IF (
                                                    isDischarged = 0 OR dischargedDate >= '{$as_of_date}', TIMESTAMPDIFF( YEAR, empTB.EDOJ, '{$as_of_date}' ),
                                                    TIMESTAMPDIFF( YEAR, empTB.EDOJ, dischargedDate )
                                                ) AS age,
                                                IF (
                                                    isDischarged = 0 OR dischargedDate >= '{$as_of_date}',
                                                    IF(
                                                        TIMESTAMPDIFF( YEAR, empTB.EDOJ, '{$as_of_date}' ) = 0, DATEDIFF( '{$as_of_date}', empTB.EDOJ ),
                                                        FLOOR( TIMESTAMPDIFF( DAY, empTB.EDOJ, '{$as_of_date}' ) % 365 )
                                                    ),  
                                                    IF(
                                                        TIMESTAMPDIFF( YEAR, empTB.EDOJ, dischargedDate ) = 0, DATEDIFF( dischargedDate, empTB.EDOJ ),
                                                        FLOOR( TIMESTAMPDIFF( DAY, empTB.EDOJ, dischargedDate ) % 365 )
                                                    )
                                                ) AS days, {$salCat2}                                                                    
                                                FROM srp_employeesdetails AS empTB 
                                                LEFT JOIN (
                                                    SELECT DesignationID, DesDescription FROM srp_designation WHERE Erp_companyID = {$companyID} AND isDeleted = 0
                                                ) AS des_tb ON des_tb.DesignationID = empTB.EmpDesignationId 
                                                JOIN (
                                                    SELECT employeeNo AS empID, {$salCat} FROM srp_erp_pay_salarydeclartion salDec
                                                    JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = salDec.employeeNo
                                                    WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} 
                                                    AND effectiveDate <= '{$as_of_date}' AND amount IS NOT NULL 
                                                    AND salaryCategoryID IN ({$whereInClause}) GROUP BY empID, salaryCategoryID
                                                )  salDec ON salDec.empID = empTB.EIdNo
                                                WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} AND 
                                                ( isDischarged = 0 OR ( dischargedDate >= '{$firstDate}' ) OR (isDischarged = 1 AND empTB.finalSettlementDoneYN = 0 ) ) 
                                                GROUP BY empTB.EIdNo
                                            ) empSalary GROUP BY empSalary.empID ORDER BY ECode
                                      ) t1")->result_array();

                $empIDs = array_column($details, 'empID');
                $empIDsStr = implode(',', array_map('intval', $empIDs));

                if (!empty($empIDs)) {
                    $loanDetailsQuery = "SELECT l.empID, l.loanCode, l.ID AS loanID
                                        FROM srp_erp_pay_emploan l
                                        JOIN (
                                            SELECT DISTINCT s.loanID
                                            FROM srp_erp_pay_emploan_schedule s
                                            WHERE s.isSetteled = 0
                                        ) unsettledLoans ON l.ID = unsettledLoans.loanID
                                        WHERE l.empID IN ({$empIDsStr}) AND l.companyID = {$companyID}";

                    $loanDetails = $this->db->query($loanDetailsQuery)->result_array();
                    foreach ($details as &$detail) {
                        $detail['loanDetails'] = array_filter($loanDetails, function($loanDetail) use ($detail) {
                            return $loanDetail['empID'] == $detail['empID'];
                        });
                    }
                }
            
                $slab_wise_amount = [];
                foreach ($slab_wise as $slab_id => $end) {

                    $slabStr = 'round((CASE';
                    foreach ($gratuitySlabData as $slabKey => $slabData) {
                        $endYear = $slabData['endYear'];
                        $result_slab = formulaBuilder_to_sql_simple_conversion($slabData['formulaString']);
                        $formula_slab = $result_slab['formulaDecode'];

                        if (($endYear - 0.001) != $end) {
                            $formula_slab = 0;
                        }
                        $slabStr .= ' WHEN (totalWorkingDays/365) <= ' . $endYear . ' THEN ' . $formula_slab;

                    }
                    $slabStr .= ' ELSE 0 END), ' . $dPlace . ') AS gratuityAmount';

                    $this_data = $this->db->query("SELECT empID, {$slabStr} FROM (
                                            SELECT empID, IF((totalWorkingDays/365) > {$end}, (365*{$end}), totalWorkingDays) AS totalWorkingDays,   
                                            {$formula} AS totFixPayment  FROM (
                                                SELECT empID,  
                                                IF (
                                                    isDischarged = 0 OR dischargedDate >= '{$as_of_date}', DATEDIFF( '{$as_of_date}', EDOJ ),
                                                    IF ( finalSettlementDoneYN = 0, DATEDIFF( dischargedDate, EDOJ ), 0 )
                                                ) AS totalWorkingDays,
                                                {$salCat2}                                                                    
                                                FROM srp_employeesdetails empTB 
                                                JOIN (
                                                    SELECT employeeNo AS empID, {$salCat} FROM srp_erp_pay_salarydeclartion salDec
                                                    JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = salDec.employeeNo
                                                    WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} 
                                                    AND effectiveDate <= '{$as_of_date}' AND amount IS NOT NULL 
                                                    AND salaryCategoryID IN ({$whereInClause}) GROUP BY empID, salaryCategoryID
                                                )  salDec ON salDec.empID = empTB.EIdNo
                                                WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} AND
                                                ( isDischarged = 0 OR ( dischargedDate >= '{$firstDate}' ) OR (isDischarged = 1 AND empTB.finalSettlementDoneYN = 0 ) )
                                                GROUP BY empTB.EIdNo
                                            ) empSalary GROUP BY empSalary.empID
                                      ) t1")->result_array();

                    $slab_wise_amount[$slab_id] = array_group_by($this_data, 'empID');
                }
            }

            if (!empty($details)) {
                foreach ($details as $key => $row) {
                    $emp_id = $row['empID'];
                    if(!in_array($row['payCurrencyID'], $currency)){
                        $currency[] = $row['payCurrencyID'];
                    }
                    $sum = 0;
                    foreach ($gratuitySlabData as $slabKey => $slabData) {
                        $slab_id = $slabData['id'];
                        $amount = $slab_wise_amount[$slab_id][$emp_id][0]['gratuityAmount'];
                        $amount = ($amount > 0) ? $amount - $sum : 0;
                        $details[$key]['slab'][$slab_id] = $amount;
                        $sum += $amount;
                    }
                }
            }

            $gr_data[$gratuityID]['details'] = $details;

            // Previous Month details
            if ($previousMonth == 1) {

                $as_of_previous_date=new DateTime($as_of_date_str);
                $previous_month = $as_of_previous_date->modify('-1 month');
                $previous_month_str = $previous_month->format('Y-m-d');
                $firstDatePrevious = date('Y-m-01', strtotime($previous_month_str));

                $details_previous_month = $this->db->query("SELECT empID, totalWork, totFixPayment, joinDate, designation, payCurrencyID, ECode, Ename2, {$slabStr} FROM (
                                            SELECT empID, totalWorkingDays, CONCAT(age,'Y ',days,'D') AS totalWork, joinDate, designation,
                                            payCurrencyID, ECode, Ename2, {$formula} AS totFixPayment
                                            FROM (
                                                SELECT empID, DATE_FORMAT(EDOJ,'{$convertFormat}') AS joinDate, DesDescription AS designation, 
                                                payCurrencyID, ECode, Ename2, 
                                                IF (
                                                    isDischarged = 0 OR dischargedDate >= '{$previous_month_str}', DATEDIFF( '{$previous_month_str}', EDOJ ),
                                                    IF ( finalSettlementDoneYN = 0, DATEDIFF( dischargedDate, EDOJ ), 0 )
                                                ) AS totalWorkingDays,
                                                IF (
                                                    isDischarged = 0 OR dischargedDate >= '{$previous_month_str}', TIMESTAMPDIFF( YEAR, empTB.EDOJ, '{$previous_month_str}' ),
                                                    TIMESTAMPDIFF( YEAR, empTB.EDOJ, dischargedDate )
                                                ) AS age,
                                                IF (
                                                    isDischarged = 0 OR dischargedDate >= '{$previous_month_str}',
                                                    IF(
                                                        TIMESTAMPDIFF( YEAR, empTB.EDOJ, '{$previous_month_str}' ) = 0, DATEDIFF( '{$previous_month_str}', empTB.EDOJ ),
                                                        FLOOR( TIMESTAMPDIFF( DAY, empTB.EDOJ, '{$previous_month_str}' ) % 365 )
                                                    ),  
                                                    IF(
                                                        TIMESTAMPDIFF( YEAR, empTB.EDOJ, dischargedDate ) = 0, DATEDIFF( dischargedDate, empTB.EDOJ ),
                                                        FLOOR( TIMESTAMPDIFF( DAY, empTB.EDOJ, dischargedDate ) % 365 )
                                                    )
                                                ) AS days, {$salCat2}                                                                    
                                                FROM srp_employeesdetails AS empTB 
                                                LEFT JOIN (
                                                    SELECT DesignationID, DesDescription FROM srp_designation WHERE Erp_companyID = {$companyID} AND isDeleted = 0
                                                ) AS des_tb ON des_tb.DesignationID = empTB.EmpDesignationId 
                                                JOIN (
                                                    SELECT employeeNo AS empID, {$salCat} FROM srp_erp_pay_salarydeclartion salDec
                                                    JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = salDec.employeeNo
                                                    WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} 
                                                    AND effectiveDate <= '{$previous_month_str}' AND amount IS NOT NULL 
                                                    AND salaryCategoryID IN ({$whereInClause}) GROUP BY empID, salaryCategoryID
                                                )  salDec ON salDec.empID = empTB.EIdNo
                                                WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} AND 
                                                ( isDischarged = 0 OR ( dischargedDate >= '{$firstDatePrevious}' ) OR (isDischarged = 1 AND empTB.finalSettlementDoneYN = 0 ) ) 
                                                GROUP BY empTB.EIdNo
                                            ) empSalary GROUP BY empSalary.empID ORDER BY ECode
                                    ) t1")->result_array();

                    $slab_wise_amount_previous = [];
                    foreach ($slab_wise as $slab_id => $end) {

                    $slabStr = 'round((CASE';
                    foreach ($gratuitySlabData as $slabKey => $slabData) {
                        $endYear = $slabData['endYear'];
                        $result_slab = formulaBuilder_to_sql_simple_conversion($slabData['formulaString']);
                        $formula_slab = $result_slab['formulaDecode'];

                        if (($endYear - 0.001) != $end) {
                        $formula_slab = 0;
                        }
                        $slabStr .= ' WHEN (totalWorkingDays/365) <= ' . $endYear . ' THEN ' . $formula_slab;

                    }
                        $slabStr .= ' ELSE 0 END), ' . $dPlace . ') AS gratuityAmount';

                        $this_data = $this->db->query("SELECT empID, {$slabStr} FROM (
                                                            SELECT empID, IF((totalWorkingDays/365) > {$end}, (365*{$end}), totalWorkingDays) AS totalWorkingDays,   
                                                            {$formula} AS totFixPayment  FROM (
                                                                SELECT empID,  
                                                                IF (
                                                                    isDischarged = 0 OR dischargedDate >= '{$previous_month_str}', DATEDIFF( '{$previous_month_str}', EDOJ ),
                                                                    IF ( finalSettlementDoneYN = 0, DATEDIFF( dischargedDate, EDOJ ), 0 )
                                                                ) AS totalWorkingDays,
                                                                {$salCat2}                                                                    
                                                                FROM srp_employeesdetails empTB 
                                                                JOIN (
                                                                    SELECT employeeNo AS empID, {$salCat} FROM srp_erp_pay_salarydeclartion salDec
                                                                    JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = salDec.employeeNo
                                                                    WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} 
                                                                    AND effectiveDate <= '{$previous_month_str}' AND amount IS NOT NULL 
                                                                    AND salaryCategoryID IN ({$whereInClause}) GROUP BY empID, salaryCategoryID
                                                                )  salDec ON salDec.empID = empTB.EIdNo
                                                                WHERE empTB.Erp_companyID = {$companyID} AND empTB.gratuityID = {$gratuityID} AND
                                                                ( isDischarged = 0 OR ( dischargedDate >= '{$firstDatePrevious}' ) OR (isDischarged = 1 AND empTB.finalSettlementDoneYN = 0 ) )
                                                                GROUP BY empTB.EIdNo
                                                            ) empSalary GROUP BY empSalary.empID
                                                    ) t1")->result_array();

                    $slab_wise_amount_previous[$slab_id] = array_group_by($this_data, 'empID');
                    }

                    foreach ($details_previous_month as $key => $row) {
                        $emp_id = $row['empID'];
                        if (!in_array($row['payCurrencyID'], $currency)) {
                            $currency[] = $row['payCurrencyID'];
                        }
                        $sum = 0;
                        foreach ($gratuitySlabData as $slabKey => $slabData) {
                            $slab_id = $slabData['id'];
                            $amount = $slab_wise_amount_previous[$slab_id][$emp_id][0]['gratuityAmount'];
                            $amount = ($amount > 0) ? $amount - $sum : 0;
                            $details_previous_month[$key]['slab'][$slab_id] = $amount;

                            $sum += $amount;
                        }
                        $details_previous_month[$key]['previous_detail'] = 1;
                    }

                $gr_data[$gratuityID]['details_previous_month'] = $details_previous_month;
               

            }
            else{
                $gr_data[$gratuityID]['details_previous_month'] = 0;
            }
        }
        
        $loc_cur = $this->common_data['company_data']['company_default_currencyID'];
        $rpt_cur = $this->common_data['company_data']['company_reporting_currencyID'];


        $currency_det = [];
        foreach ($currency as $item){
            $reportCon = currency_conversionID($item, $rpt_cur, 0);
            $currency_det[$item]['rpt'] = $reportCon;

            $localCon = currency_conversionID($item, $loc_cur, 0);
            $currency_det[$item]['loc'] = $localCon;
        }


        $gratuityMaster = array_group_by($gratuityMaster, 'gratuityID');

        $data['as_of_date_str'] = $as_of_date_str;
        $data['currency_det'] = $currency_det;
        $data['gratuityMaster'] = $gratuityMaster;
        $data['gr_data'] = $gr_data;
        $data['loc_curr'] = $this->common_data['company_data']['company_default_currency'];
        $data['rpt_curr'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['rpt_dPlace'] = $this->db->get_where('srp_erp_currencymaster', ['currencyID' => $rpt_cur])->row('DecimalPlaces');
        $data['loc_dPlace'] = $this->db->get_where('srp_erp_currencymaster', ['currencyID' => $loc_cur])->row('DecimalPlaces');

        //if($requestType )
        echo $this->load->view('system/hrm/report/ajax/gratuity-salary-ajax.php', $data, true);
    }

    function loadLoan()
    {
        $convertFormat = convert_date_format_sql();
        $empID = $this->input->post('empID');
        $companyID = $this->common_data['company_data']['company_id'];
    
        $loanIDs = $this->db->query("SELECT DISTINCT loan.ID FROM srp_erp_pay_emploan AS loan
                    JOIN srp_employeesdetails AS emp ON emp.EIdNo = loan.empID
                    LEFT JOIN srp_erp_pay_emploan_schedule AS loan_sch ON loan_sch.loanID = loan.ID
                    WHERE loan.companyID = {$companyID}
                    AND emp.EIdNo = {$empID}
                    AND loan_sch.isSetteled = 0
                    ")->result_array();

        $loanIDArray = array_column($loanIDs, 'ID');
        $allResults = [];

        foreach ($loanIDArray as $loanID) {

            $query = $this->db->query("
                SELECT 
                    DATE_FORMAT(scheduleDate, '{$convertFormat}') AS scheduleDate1,
                    scheduleDate AS sh,
                    sch.loanCode,
                    amountPerInstallment AS amount,
                    sch.ID AS scheduleID,
                    sch.transactionCurrencyDecimalPlaces AS dPlace,
                    isSetteled,
                    skipedInstallmentID,
                    installmentNo,
                    skippedDescription
                FROM srp_erp_pay_emploan_schedule AS sch
                INNER JOIN srp_erp_pay_emploan AS loan ON loan.ID = sch.loanID
                WHERE 
                    loan.companyID = {$this->common_data['company_data']['company_id']}
                    AND loan.ID = {$loanID}
            ");

            $allResults = array_merge($allResults, $query->result_array());
        }

        echo json_encode($allResults);
    }
    

    function save_sso_setup(){
        $this->form_validation->set_rules('sso_employee', 'Employee', 'trim|required');
        $this->form_validation->set_rules('sso_employer', 'Employer', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $this->db->trans_start();
        $sso_employee = $this->input->post('sso_employee');
        $sso_employer = $this->input->post('sso_employer');

        $where = [ 'masterID' => 5, 'companyID' => current_companyID() ];

        $this->db->delete('srp_erp_sso_reporttemplatedetails', $where);

        $data['masterID'] = '5';
        $data['reportID'] = '1'; /*Employee */
        $data['reportValue'] = $sso_employee;
        $data['companyID'] = current_companyID();
        $data['companyCode'] = current_companyCode();
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdUserGroup'] = current_user_group();
        $data['createdUserName'] = current_employee();
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_sso_reporttemplatedetails', $data);


        $data['reportID'] = '2'; /*Employer */
        $data['reportValue'] = $sso_employer;
        $this->db->insert('srp_erp_sso_reporttemplatedetails', $data);


        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode(['s', 'SSO report configuration successfully updated']);
        } else {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in SSO report configuration process']);
        }
    }

    function get_social_insurance_report()
    {
        $this->form_validation->set_rules('payroll_period', 'Period', 'trim|required');
        $this->form_validation->set_rules('currencyID', 'Currency', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $requestType = $this->uri->segment(3);
        $companyID = current_companyID();
        $payroll_period = $this->input->post('payroll_period');
        $currencyID = $this->input->post('currencyID');

        $defaultSSOValues = get_defaultSSOSetup();
        if(empty($defaultSSOValues)){
            die( json_encode(['e', 'Social Insurance Report not configured']) );
        }
        $sso_employee_id = $defaultSSOValues['sso_employee'];
        $sso_employer_id = $defaultSSOValues['sso_employer'];

        $employee_per = $this->db->query("SELECT employeeContribution FROM srp_erp_paygroupmaster AS payGroup
                                JOIN srp_erp_socialinsurancemaster AS ssoTB
                                ON ssoTB.socialInsuranceID=payGroup.socialInsuranceID AND ssoTB.companyID={$companyID}
                                WHERE payGroup.companyID={$companyID} AND payGroupID = {$sso_employee_id}")->row('employeeContribution');

        $employer_per = $this->db->query("SELECT employerContribution FROM srp_erp_paygroupmaster AS payGroup
                                JOIN srp_erp_socialinsurancemaster AS ssoTB
                                ON ssoTB.socialInsuranceID=payGroup.socialInsuranceID AND ssoTB.companyID={$companyID}
                                WHERE payGroup.companyID={$companyID} AND payGroupID = {$sso_employer_id}")->row('employerContribution');

        $curr_data = $this->db->query("SELECT DecimalPlaces, CurrencyCode FROM srp_erp_currencymaster WHERE currencyID = {$currencyID}")->row_array();
        $dPlace = $curr_data['DecimalPlaces'];


        $year = date('Y', strtotime($payroll_period));
        $month = date('m', strtotime($payroll_period));

        $SSO_data = $this->db->query("SELECT ECode, Ename2 AS nameWithIn, ABS( SUM(det_tb_1.transactionAmount) ) AS employee_am,
                                    ABS( SUM(det_tb_2.transactionAmount) ) AS employer_am                                  
                                    FROM srp_erp_payrollmaster AS mas_tb
                                    LEFT JOIN (
                                        SELECT transactionAmount, empID, payrollMasterID FROM srp_erp_payrolldetail 
                                        WHERE detailTBID = {$sso_employee_id} AND calculationTB='PAY_GROUP' AND companyID = {$companyID} 
                                        AND transactionCurrencyID = {$currencyID}
                                    ) AS det_tb_1 ON det_tb_1.payrollMasterID = mas_tb.payrollMasterID                                       
                                    JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=det_tb_1.empID AND empTB.Erp_companyID = {$companyID}                                      
                                    LEFT JOIN (
                                        SELECT transactionAmount, empID, payrollMasterID FROM srp_erp_payrolldetail 
                                        WHERE detailTBID = {$sso_employer_id} AND calculationTB='PAY_GROUP' AND companyID = {$companyID} 
                                        AND transactionCurrencyID = {$currencyID}
                                    ) AS det_tb_2 ON det_tb_2.payrollMasterID = mas_tb.payrollMasterID  AND empTB.EIdNo = det_tb_2.empID                                      
                                    WHERE mas_tb.companyID={$companyID} AND mas_tb.payrollYear = {$year} AND mas_tb.payrollMonth = {$month}
                                    GROUP BY empTB.EIdNo ORDER BY empTB.ECode DESC")->result_array();

        $data['SSO_data'] = $SSO_data;
        $data['employee_per'] = $employee_per;
        $data['employer_per'] = $employer_per;
        $data['dPlace'] = $dPlace;

        if ($requestType == 'excel') {
            $data['file_name'] = date('Y - F', strtotime($payroll_period)).' - '.$curr_data['CurrencyCode'];
            return $this->excel_social_insurance_report($data);
        } else {
            $view = $this->load->view('system/hrm/ajax/social-insurance-report.php', $data, true);
            echo json_encode(['s', 'view'=>$view]);
        }
    }

    function excel_social_insurance_report($data){
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);

        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('SSO report');

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->mergeCells('A1:H1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:H4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('cee2f3');

        $header = [
            '#',
            $this->lang->line('common_emp_no'),
            $this->lang->line('common_employee_name'),
            $this->lang->line('common_employee').' %',
            $this->lang->line('common_employee'),
            $this->lang->line('common_employer').' %',
            $this->lang->line('common_employer'),
            $this->lang->line('common_total'),
        ];
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');

        $det = [];
        $employee_tot = 0; $employer_tot = 0; $total = 0;
        $dPlace = $data['dPlace']; $employee_per = $data['employee_per']; $employer_per = $data['employer_per'];
        $SSO_data = $data['SSO_data'];
        if(empty($SSO_data)){
            $det[] =  $this->lang->line('common_no_records_found');
        }
        else{
            $n = 5;
            foreach ($SSO_data as $key=>$row){
                $line_total = round($row['employee_am'], $dPlace ) + round($row['employer_am'], $dPlace );
                $total += $line_total;
                $employee_tot += round($row['employee_am'], $dPlace );
                $employer_tot += round($row['employer_am'], $dPlace );

                $det[] = [
                    ($key+1), $row['ECode'], $row['nameWithIn'], "$employee_per %", round($row['employee_am'], $dPlace ),
                    "$employer_per %", round($row['employer_am'], $dPlace ), round($line_total, $dPlace )
                ];
                $n++;
            }

            $det[] = [
                $this->lang->line('common_grand_total'), '', '', '', number_format($employee_tot, $dPlace ), '',
                number_format($employer_tot, $dPlace ), number_format($total, $dPlace )
            ];

            $format_decimal = ($dPlace == 3)? '#,##0.000': '#,##0.00';
            $this->excel->getActiveSheet()->mergeCells("A{$n}:D{$n}");
            $this->excel->getActiveSheet()->getStyle("A{$n}:H{$n}")->getFont()->setBold(true)->setSize(11)->setName('Calibri');
            $this->excel->getActiveSheet()->getStyle("E")->getNumberFormat()->setFormatCode($format_decimal);
            $this->excel->getActiveSheet()->getStyle("E{$n}")->getNumberFormat()->setFormatCode($format_decimal);
            $this->excel->getActiveSheet()->getStyle("D5:H{$n}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->getStyle("G5:H{$n}")->getNumberFormat()->setFormatCode($format_decimal);
        }

        $this->excel->getActiveSheet()->fromArray($det, null, 'A5');

        $filename = $data['file_name'].'.xls';
        header('Content-Type: application/vnd.ms-excel;charset=utf-16');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }
    function get_stock_aging_report_item()/*item ledger,valuation,counting*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/inventory/report/erp_stockcounting', $data);
    }
    function stock_aging_drilldown()
    {
        // Done stock_aging
        $fieldNameChk = $this->input->post('fieldNameChk');

        $date_format_policy = date_format_policy();
        $asofdate = $this->input->post('from');
        $data['asofdateconverted'] = input_format_date($asofdate, $date_format_policy);

        $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
        $this->form_validation->set_rules('warehouseid[]', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('fieldNameChk[]', 'Extra Column', 'trim|required');

        /*  if (count($fieldNameChk) > 1) {
            $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
        } else {
            $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
        }*/

        if($fieldNameChk) {
            if(in_array("companyLocalAmount", $fieldNameChk) && in_array("companyReportingAmount", $fieldNameChk)){
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
            } else if(!in_array("companyLocalAmount", $fieldNameChk) && !in_array("companyReportingAmount", $fieldNameChk)){
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
            }
        }
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
        $aging = array("0-30", "31-60", "61-90", "91-120", "121-365", "366-730", "731");
        $reversAging = array_reverse($aging);
        $agingcolumn = array("<=30", "31-60", "61-90", "91-120", "121-365", "366-730", "Over 730");
        $data["type"] = "html";
        $data["aging"] = $aging;
        $data["agingcolumn"] = $agingcolumn;
        $data["output"] = $this->Report_model->stock_aging_detail();
        //output stock deduct from bucket
            if($data["output"] )
            {
                foreach ($data["output"] as $key => $val)
                {
                    $gSubstractValue = 0;
                    $x=0;
                    foreach ($reversAging as $key2 => $revval)
                    {
                        $substractValue = 0;
                        $agingValue = $val['qtyaging'.$revval];

                        if($x == 0) {
                            $substractValue = (int)$agingValue - (int)$val['outputtoalqty'];
                        }else{
                            $substractValue = $agingValue - $gSubstractValue;
                        }

                        $gSubstractValue = abs($substractValue);
                        if($substractValue > 0){
                            $data["output"][$key]['qtyaging'.$revval] = $substractValue;
                            $data["output"][$key]['valueaging'.$revval] = ($substractValue * $val['WacAmount']);
                            break;
                        }else{
                            $data["output"][$key]['qtyaging'.$revval] = 0;
                            $data["output"][$key]['valueaging'.$revval] = 0;
                        }
                        $x++;
                    }
                }
            }
            $total = array();
            $total2 = array();

            foreach ($data["output"] as $key => $val)
            {
                foreach ($aging as $key2 => $valtotal)
                {
                    $total[$valtotal][] = $val['valueaging'.$valtotal];
                }
            }
            foreach ($aging as $key2 => $valtotal)
            {
                $total2[$valtotal] = array_sum($total[$valtotal]);
            }
            $data['grandtotal'] = $total2;


/*
            if($fieldName =='companyLocalAmount')
            {
                $data['rpttype'] = 1;

            }else
            {
                $data['rpttype'] = 2;
            }*/
            $data['fieldName'] = $this->input->post('fieldNameChk');

        $data["output_stock"] = $this->Report_model->stock_aging_detail_output();
        $data["warehouse"] = $this->Report_model->get_warehouse_stock_aging();
        $this->load->view('system/inventory/report/erp_stock_aging_detail',$data);
    }
    }

    function get_report_by_id_buyback()
    {
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque+
        $companyID = $this->common_data['company_data']['company_id'];
        switch ($this->input->post('reportID')) {
            case "AR_CS": /*Customer Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report_postdated_buyback();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["template"] = "buyback";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report_postdated_cheques', $data);
                    } else {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report_buyback();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["template"] = "buyback";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report', $data);
                    }


                }
                break;
            case "AR_CAS": /*Customer Aging Summary*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_report_pdc_buyback($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report_pdc', $data);
                    } else {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_report_buyback($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report', $data);
                    }
                }
                break;
            case "AR_CAD": /*Customer Aging Details*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_report_postdated_buyback($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["template"] = "buyback";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report_pdc', $data);
                    }else
                    {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_report_buyback($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["template"] = "buyback";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report', $data);
                    }
                }
                break;
        }
    }


    function get_report_drilldown_buyback()
    {
        switch ($this->input->post('reportID')) {
            case "AR_CAS";/*Customer Aging Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $customerAutoID = $this->input->post("customerTo");
                $customerAutoID=join(",",$customerAutoID);
                $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                $data["output"] = $this->Report_model->get_accounts_receivable_report_drilldown_buyback($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["groupbycus"] = $this->input->post('groupbycus');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $data["template"] = "buyback";
                $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_drilldown_report', $data);
                break;
        }
    }

    function get_group_report_by_id_buyback()
    {

        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque+
        $companyID = $this->common_data['company_data']['company_id'];
        switch ($this->input->post('reportID')) {
            case "AR_CS": /*Customer Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","CUST"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        //$PostDatedChequeManagement = 1;
                        $data = array();
                        if($PostDatedChequeManagement == 1){
                            $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_group_report_postdated_buyback();
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["type"] = "html";
                            $data["template"] = "buyback";
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                            $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report_postdated_cheques', $data);

                        }else{
                            $customerAutoID = $this->input->post("customerTo");
                            $customerAutoID=join(",",$customerAutoID);
                            $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                            $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                            $data["groupbycus"] = $this->input->post('groupbycus');
                            $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_group_report_buyback();
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["type"] = "html";
                            $data["template"] = "buyback";

                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                            $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report', $data);

                        }

                         }
                }
                break;
            case "AR_CAS": /*Customer Aging Summary*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                
                        $data = array();
                        $aging = array();
                        $interval = $this->input->post("interval");
                        $through = $this->input->post("through");
                        $z = 1;
                        for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                            if ($z == 1) {
                                $aging[] = $z . "-" . $interval;
                            } else {
                                if (($i + $interval) > $through) {
                                    $aging[] = ($i + 1) . "-" . ($through);
                                    $i += $interval;
                                } else {
                                    $aging[] = ($i + 1) . "-" . ($i + $interval);
                                    $i += $interval;
                                }

                            }
                        }
                        $aging[] = "> " . ($through);
                        //$PostDatedChequeManagement = 1;
                        if ($PostDatedChequeManagement == 1) {
                            $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_group_report_pdc_buyback($aging);
                            $data["caption"] = $this->input->post('captionChk');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["aging"] = $aging;
                            $data["type"] = "html";
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                            $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report_pdc', $data);
                        }else{
                            $customerAutoID = $this->input->post("customerTo");
                            $customerAutoID=join(",",$customerAutoID);
                            $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                            $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                            $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_group_report_buyback($aging);
                            $data["caption"] = $this->input->post('captionChk');
                            $data["groupbycus"] = $this->input->post('groupbycus');
                            $data["fieldName"] = $this->input->post('fieldNameChk');
                            $data["from"] = $this->input->post('from');
                            $data["aging"] = $aging;
                            $data["type"] = "html";
                            $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                            $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report', $data);
                        
                
                        }


                        }
                break;
            case "AR_CAD": /*Customer Aging Details*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);
                    if ($PostDatedChequeManagement == 1) {
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_group_report_postdated_buyback($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["template"] = "buyback";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report_pdc', $data);
                    }else
                    {
                        $customerAutoID = $this->input->post("customerTo");
                        $customerAutoID=join(",",$customerAutoID);
                        $data["customers"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, cus.customerSystemCode, cus.customerName FROM srp_erp_customermaster INNER JOIN  srp_erp_customermaster cus on srp_erp_customermaster.masterID =cus.customerAutoID WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) and srp_erp_customermaster.masterID is not null ")->result_array();
                        $data["customersall"] = $this->db->query("SELECT srp_erp_customermaster.	customerAutoID, srp_erp_customermaster.masterID, srp_erp_customermaster.customerSystemCode, srp_erp_customermaster.customerName FROM srp_erp_customermaster  WHERE srp_erp_customermaster.customerAutoID IN ($customerAutoID) ")->result_array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_group_report_buyback($aging);
                        $data["caption"] = $this->input->post('captionChk');
                        $data["groupbycus"] = $this->input->post('groupbycus');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["aging"] = $aging;
                        $data["type"] = "html";
                        $data["template"] = "buyback";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report', $data);
                    }
                }
                /******** Not  Converted To Group END  ********/
                break;
        }
    }


    function loadCustomer()
    {
        echo json_encode($this->Report_model->loadCustomer());
    }

    function fetch_customerDropdown()
    {
        $customer_arr = array();
        $partyCategoryID = $this->input->post("customerCategoryID");
        $activeStatus = $this->input->post("activeStatus");
        $tab = $this->input->post("tab");

        $status_filter = '';
        $partyCategoryID_join="";
        $countcat = is_array($partyCategoryID) ? sizeof($partyCategoryID) : 0;
        $companyID = current_companyID();
        $customercat = $this->db->query("SELECT COUNT( `partyCategoryID`) as partyCategorycount FROM `srp_erp_partycategories` WHERE `companyID` = '{$companyID}' AND `partyType` = 1")->row('partyCategorycount');
        if(!empty($partyCategoryID)){
            $partyCategoryID_join_filter =  join(",",$partyCategoryID);
            $partyCategoryID_join = "AND partyCategoryID IN ($partyCategoryID_join_filter)";
        }
        if (!empty($activeStatus)) {
            if($activeStatus==1){
                $status_filter = "AND isActive = 1 ";
            }elseif($activeStatus==2){
                $status_filter = "AND isActive = 0 ";
            }else{
                $status_filter = '';
            }
        }
        $companyID = current_companyID();
        $type = $this->input->post("type");

        if($type == 1){
            if($countcat == $customercat)
            {
                $partyCategoryID_join = '';
            }
            $customer= $this->db->query("SELECT `customerAutoID`, `customerName`, `customerSystemCode`, `customerCountry` 
                                              FROM `srp_erp_customermaster` WHERE `companyID` = $companyID AND `deletedYN` =0 $partyCategoryID_join $status_filter")->result_array();
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['customerAutoID'] ?? '')] = (trim($row['customerSystemCode'] ?? '') ? trim($row['customerSystemCode'] ?? '') . ' | ' : '') . trim($row['customerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
                }
            }
        }else{
            $companies = getallsubGroupCompanies(true);
            $masterGroupID=getParentgroupMasterID();

            $customer = $this->db->query("SELECT `groupCustomerAutoID`,`groupCustomerName`,`groupcustomerSystemCode`,`customerCountry` 
                FROM
                    `srp_erp_groupcustomermaster` 
                    INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
                WHERE
                    srp_erp_groupcustomermaster.companyGroupID =  $masterGroupID
                    AND srp_erp_groupcustomerdetails.companyID IN ( $companies )
                    GROUP BY groupCustomerAutoID")->result_array();
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['groupCustomerAutoID'] ?? '')] = (trim($row['groupcustomerSystemCode'] ?? '') ? trim($row['groupcustomerSystemCode'] ?? '') . ' | ' : '') . trim($row['groupCustomerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
                }
            }
        }
        if($tab==2){
            echo form_dropdown('s_customerID[]', $customer_arr, '', 'class="form-control" id="s_filter_customerID" multiple="" ');
        }else{
            echo form_dropdown('customerID[]', $customer_arr, '', 'class="form-control" id="filter_customerID" multiple="" ');
        }
    }


    function fetch_customerDropdown_rev() // revenue detail report,revenue summary report
    {
        $customer_arr = array();
        $partyCategoryID = $this->input->post("customerCategoryID");
        $partyCategoryID_join="";
        $activeStatus = $this->input->post("activeStatus");
        $status_filter = '';
        $selectall = $this->input->post("selectall");
        $selectdeselectall = $this->input->post("selectdeselectall");
        if (!empty($activeStatus)) {
            if($activeStatus==1){
                $status_filter = "AND isActive = 1 ";
            }elseif($activeStatus==2){
                $status_filter = "AND isActive = 0 ";
            }else{
                $status_filter = '';
            }
        }
        $countcat = sizeof($partyCategoryID);
        $companyID = current_companyID();

        if(empty($partyCategoryID)){
            $count_incomecatID = 0;
        }else{
            $count_incomecatID = count($partyCategoryID);
        }

        $exact_count = $this->db->select('count(partyCategoryID) as exact_count ')->from('srp_erp_partycategories')->where(['companyID' => $companyID, 'partyType' => 1])->get()->row('exact_count');

        if($count_incomecatID == $exact_count && (($selectall=='All' || empty($selectall)) || ($selectdeselectall=='DAll' || empty($selectdeselectall)))){
            $show_cust = 1;
        }else{
            if($count_incomecatID == 0){ 
                $show_cust = 1; 
            }else{
                $show_cust = 0; 
            }
            
        }
        //echo '<pre>';print_r($show_cust.' | '.$count_incomecatID.' | '.$exact_count);'</pre>';exit;
        $customercat = $this->db->query("SELECT COUNT( `partyCategoryID`) as partyCategorycount FROM `srp_erp_partycategories` WHERE `companyID` = '{$companyID}' AND `partyType` = 1")->row('partyCategorycount');
        if(!empty($partyCategoryID)){
            $partyCategoryID_join_filter =  join(",",$partyCategoryID);
            $partyCategoryID_join = "AND partyCategoryID IN ($partyCategoryID_join_filter)";
        }

        $companyID = current_companyID();
        $type = $this->input->post("type");

        if($type == 1){
            if($countcat == $customercat)
            {
                $partyCategoryID_join = '';
            }
            $customer= $this->db->query("SELECT `customerAutoID`, `customerName`, `customerSystemCode`, `customerCountry` 
                                              FROM `srp_erp_customermaster` WHERE `companyID` = $companyID AND `deletedYN` =0 $partyCategoryID_join $status_filter")->result_array();
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['customerAutoID'] ?? '')] = (trim($row['customerSystemCode'] ?? '') ? trim($row['customerSystemCode'] ?? '') . ' | ' : '') . trim($row['customerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
                }
                if($show_cust == 1){
                    #$customer_arr[0] = ('') . 'Sundry' . ('');
                    $customer_arr[-1] = ('') . 'POS Customers' . ('');
                    $customer_arr[-2] = ('') . 'Direct Receipt voucher' . ('');
                }

            }
        }else{
            $companies = getallsubGroupCompanies(true);
            $masterGroupID=getParentgroupMasterID();

            $customer = $this->db->query("SELECT `groupCustomerAutoID`,`groupCustomerName`,`groupcustomerSystemCode`,`customerCountry` 
                FROM
                    `srp_erp_groupcustomermaster` 
                    INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID
                WHERE
                    srp_erp_groupcustomermaster.companyGroupID =  $masterGroupID
                    AND srp_erp_groupcustomerdetails.companyID IN ( $companies )
                    GROUP BY groupCustomerAutoID")->result_array();
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['groupCustomerAutoID'] ?? '')] = (trim($row['groupcustomerSystemCode'] ?? '') ? trim($row['groupcustomerSystemCode'] ?? '') . ' | ' : '') . trim($row['groupCustomerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
                }
                #$customer_arr[0] = ('') . 'Sundry' . ('');
                $customer_arr[-1] = ('') . 'POS Customers' . ('');
                $customer_arr[-2] = ('') . 'Direct Receipt voucher' . ('');
            }
        }
        echo form_dropdown('customerID[]', $customer_arr, '', 'class="form-control" id="filter_customerID" multiple="" ');
    }

    function get_project_cost_report()
    {
        $project = $this->input->post('project');
        $companyID = current_companyID();
        $projectID_join = '';
        $this->form_validation->set_rules('project[]', 'Project', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            if(!empty($project))
            {
                $projectID = join(",",$project);
                $projectID_join=' AND srp_erp_projects.projectID IN('.$projectID.')';
            }
            $data['projectcost'] = $this->db->query("SELECT IFNULL( pmexp.amount, 0 ) AS actualcost,srp_erp_projects.projectID, projectName ,IFNULL(pm.amount,0) as amount,IFNULL(pmestrev.amt,0) as estimatedrevenue,	IFNULL( pmestrev.costamt, 0 ) AS estimatedcost FROM srp_erp_projects 
	                                                   LEFT JOIN (SELECT projectID, sum( transactionAmount / projectExchangeRate ) AS amount FROM `srp_erp_generalledger` 
                                                       WHERE ( GLType = 'PLI' ) GROUP BY projectID)pm on pm.projectID = srp_erp_projects.projectID
                                                        LEFT JOIN(SELECT IFNULL(SUM(totalTransCurrency),0) as amt,IFNULL( SUM( totalCostAmountTranCurrency ), 0 ) AS costamt, projectID FROM `srp_erp_boq_details` INNER JOIN srp_erp_boq_header  ON srp_erp_boq_header.headerID = `srp_erp_boq_details`.`headerID` 
                                                        GROUP BY projectID) pmestrev on pmestrev.ProjectID  = srp_erp_projects.projectID
                                                        LEFT JOIN (SELECT projectID,sum( transactionAmount / projectExchangeRate ) AS amount FROM `srp_erp_generalledger` WHERE ( GLType = 'PLE' ) 
                                                        GROUP BY projectID)pmexp on pmexp.projectID = srp_erp_projects.projectID
                                                    
                                                        WHERE srp_erp_projects.companyID = $companyID $projectID_join")->result_array();
            $this->load->view('system/pm/project_cost_report_view',$data);
        }
    }

    function export_excel_item_report()
    {
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('inventory', $primaryLanguage);
        $this->lang->load('common', $primaryLanguage);
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->load->database();
        switch ($this->input->post('reportID')) {
            case "ITM_LG": /*Item Counting*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkexcel'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkexcel'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    echo json_encode($this->Report_model->fetch_item_ledger_for_excel());
                }
                break;

            case 'INV_VAL' : /*Item Valuation Summary*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkexcel'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Extra Column', 'trim|required');

                if($_POST["fieldNameChk"]) {
                    if(in_array("companyLocalWacAmount", $_POST["fieldNameChk"]) && in_array("companyReportingWacAmount", $_POST["fieldNameChk"])){
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else if(!in_array("companyLocalWacAmount", $_POST["fieldNameChk"]) && !in_array("companyReportingWacAmount", $_POST["fieldNameChk"])){
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    }
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $isRep = false;
                    $isLoc = false;
                    $isBarcode = false;
                    $isPartNo = false;
                    $isSeconeryItemCode = false;

                    $fieldName = explode(',' , $this->input->post('fieldNameChkexcel'));
                    $header = array(
                        $this->lang->line('transaction_common_item_code'), // Item Code
                        $this->lang->line('transaction_common_item_description') // Item Description

                    );

                    if (isset($fieldName)) {
                        if (in_array("barcode", $fieldName)) {
                            $header[] = $this->lang->line('transaction_barcode') ; // barcode
                            $isBarcode = true;
                        }
                        if (in_array("seconeryItemCode", $fieldName)) {
                            $header[] = $this->lang->line('erp_item_master_secondary_code') ; // Secondary Code
                            $isSeconeryItemCode = true;
                        }
                        if (in_array("partNo", $fieldName)) {
                            $header[] = $this->lang->line('transaction_part_no') ; // part No
                            $isPartNo = true;
                        }
                        $header[] = $this->lang->line('transaction_on_hand') ; // On Hand
                        if (in_array("companyReportingWacAmount", $fieldName)) {
                            $header[] = $this->lang->line('transaction_avg_cost') . "(" . $this->common_data['company_data']['company_reporting_currency'] . ")"; // Avg Cost
                            $header[] = $this->lang->line('common_total_value') . "(" . $this->common_data['company_data']['company_reporting_currency'] . ")"; // Total Value
                            $isRep = true;
                        }
                        if (in_array("companyLocalWacAmount", $fieldName)) {
                            $header[] = $this->lang->line('transaction_avg_cost') . "(" . $this->common_data['company_data']['company_default_currency'] . ")"; // Avg Cost
                            $header[] = $this->lang->line('common_total_value') . "(" . $this->common_data['company_data']['company_default_currency'] . ")"; // Total Value
                            $isLoc = true;
                        }
                    }
                    $header[] = "% " . $this->lang->line('transaction_of_total'); // % of Total
                    $header[] = $this->lang->line('transaction_sales_price'); // Sales Price
                    $header[] = $this->lang->line('transaction_retail_value'); // Retail Price
                    $header[] = "% " . $this->lang->line('transaction_of_total_retail'); // % ot total retail
                    $header[] = "% " . $this->lang->line('transaction_margin'); // % Margin

                    $this->excel->getActiveSheet()->setTitle($this->lang->line('transaction_item_valuation_summary'));
                    $data = $this->Report_model->fetch_item_valuation_summary_for_excel($isRep, $isLoc, $isBarcode, $isPartNo, $isSeconeryItemCode);
                    $details = $data['details'];
                    $rows = $data['rowCount'];

                    $warehouse = $this->Report_model->get_warehouse();
                    $warehouseString = str_replace("<br>"," : ",join(" | ",$warehouse));
                    $this->excel->getActiveSheet()->setCellValue('A4', $this->lang->line('common_warehouse') . ' : ' . $warehouseString);
                    $this->excel->getActiveSheet()->mergeCells('A4:H4');
                    $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

                    $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('transaction_item_valuation_summary')], null, 'A2');
                    $this->excel->getActiveSheet()->fromArray([$this->common_data['company_data']['company_name']], null, 'A1');
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('common_as_of_date') . ' : ' . $this->input->post('from')], null, 'A3');

                    $this->excel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('A6:U6')->getFont()->setBold(true)->setSize(11)->setName('Calibri');

                    $this->excel->getActiveSheet()->getStyle('C7:M'.$rows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $this->excel->getActiveSheet()->fromArray($header, null, 'A6');
                    $this->excel->getActiveSheet()->fromArray($details, null, 'A7');

                    $filename = 'Item Valuation Summary.xls'; //save our workbook as this file name
                    header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
                    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
                    header('Cache-Control: max-age=0'); //no cache

                    $writer = new Xlsx($this->excel);
                    $writer->save('php://output');
                }
                break;

            case "ITM_CNT": /*Item Counting*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $isRep = false;
                    $isLoc = false;
                    $isBarcode = false;
                    $isSeconeryItemCode = false;
                    $isPartNo = false;

                    $fieldName = explode(',' , $this->input->post('fieldNameChkexcel'));
                    $header = array(
                        $this->lang->line('transaction_common_item_code'), $this->lang->line('transaction_common_item_description')
                    );
                    if (isset($fieldName)) {
                        if (in_array("barcode", $fieldName)) {
                            $header[] = $this->lang->line('transaction_barcode') ; // barcode
                            $isBarcode = true;
                        }
                        if (in_array("seconeryItemCode", $fieldName)) {
                            $header[] = $this->lang->line('erp_item_master_secondary_code') ; // Secondary Code
                            $isSeconeryItemCode = true;
                        }
                        if (in_array("partNo", $fieldName)) {
                            $header[] = $this->lang->line('transaction_part_no') ; // part No
                            $isPartNo = true;
                        }
                    }
                    $header[] = $this->lang->line('common_uom');
                    $header[] = $this->lang->line('common_Location');
                    $header[] = $this->lang->line('transaction_qty_in_hand');

                    if (isset($fieldName)) {
                        if (in_array("AssetValueLocal", $fieldName)) {
                            $header[] = $this->lang->line('transaction_avg_cost_in_local');
                            $header[] = $this->lang->line('transaction_asset_value_local');
                            $isLoc = true;
                        }
                        if (in_array("AssetValueRpt", $fieldName)) {
                            $header[] = $this->lang->line('transaction_avg_cost_rpt');
                            $header[] = $this->lang->line('transaction_asset_value_rpt');
                            $isRep = true;
                        }
                    }
                    $header[] = $this->lang->line('transaction_physical_qty');
                    $this->excel->getActiveSheet()->setTitle($this->lang->line('transaction_item_counting'));
                    $data = $this->Report_model->fetch_item_counting_for_excel($isRep, $isLoc,$isBarcode,$isPartNo , $isSeconeryItemCode);
                    $details = $data['details'];
                    $rows = $data['rowCount'];

                    $warehouse = $this->Report_model->get_warehouse();
                    $warehouseString = str_replace("<br>"," : ",join(" | ",$warehouse));
                    $this->excel->getActiveSheet()->setCellValue('A4', $this->lang->line('common_warehouse') . ' : ' . $warehouseString);
                    $this->excel->getActiveSheet()->mergeCells('A4:H4');
                    $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

                    $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('transaction_item_counting')], null, 'A2');
                    $this->excel->getActiveSheet()->fromArray([$this->common_data['company_data']['company_name']], null, 'A1');
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('common_as_of_date') . ' : ' . $this->input->post('from')], null, 'A3');
                    $this->excel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('A6:U6')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('E7:M'.$rows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $this->excel->getActiveSheet()->fromArray($header, null, 'A6');
                    $this->excel->getActiveSheet()->fromArray($details, null, 'A7');

                    $filename = 'Item Counting.xls'; //save our workbook as this file name
                    header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
                    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
                    header('Cache-Control: max-age=0'); //no cache

                    $writer = new Xlsx($this->excel);
                    $writer->save('php://output');
                }
                break;

            case "ITM_FM": /*item fast moving*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkexcel'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report Type', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $isRep = false;
                    $isLoc = false;
                    $isBarcode = false;
                    $isSeconeryItemCode = false;
                    $isPartNo = false;

                    $fieldName = explode(',' , $this->input->post('fieldNameChkexcel'));
                    $header = array(
                        $this->lang->line('transaction_common_item_code'),
                        $this->lang->line('transaction_item_name')

                    );
                    if (isset($fieldName)) {
                        if (in_array("barcode", $fieldName)) {
                            $header[] = $this->lang->line('transaction_barcode') ; // barcode
                            $isBarcode = true;
                        }
                        if (in_array("seconeryItemCode", $fieldName)) {
                            $header[] = $this->lang->line('erp_item_master_secondary_code') ; // Secondary Code
                            $isSeconeryItemCode = true;
                        }
                        if (in_array("partNo", $fieldName)) {
                            $header[] = $this->lang->line('transaction_part_no') ; // part No
                            $isPartNo = true;
                        }

                    }
                    $header[] = $this->lang->line('common_uom') ;
                    $header[] = $this->lang->line('common_qty') ;

                    if (isset($fieldName)) {
                        if (in_array("companyLocalAmount", $fieldName)) {
                            $header[] = $this->lang->line('transaction_total_sales') . "(" . $this->common_data['company_data']['company_default_currency'] . ")";
                            $isLoc = true;
                        }
                        if (in_array("companyReportingAmount", $fieldName)) {
                            $header[] = $this->lang->line('transaction_total_sales') . "(" . $this->common_data['company_data']['company_reporting_currency'] . ")";
                            $isRep = true;
                        }
                    }
                    $header[] = $this->lang->line('transaction_qty_in_hand');

                    $this->excel->getActiveSheet()->setTitle($this->lang->line('transaction_item_fast_moving'));
                    $data = $this->Report_model->fetch_item_fast_moving_for_excel($isRep, $isLoc,$isBarcode, $isSeconeryItemCode, $isPartNo);
                    $details = $data['details'];
                    $rows = $data['rowCount'];

                    $segmentFilter = $this->Report_model->get_segment();
                    $this->excel->getActiveSheet()->setCellValue('A4', $this->lang->line('common_segment') . ' : ' . join(" | ",$segmentFilter));
                    $this->excel->getActiveSheet()->mergeCells('A4:H4');
                    $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

                    $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('transaction_item_fast_moving')], null, 'A2');
                    $this->excel->getActiveSheet()->fromArray([$this->common_data['company_data']['company_name']], null, 'A1');
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('transaction_date_from') . ' : ' . $this->input->post('from') . ' - ' . $this->lang->line('transaction_date_to') . ' : ' . $this->input->post('to')], null, 'A3');

                    $this->excel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('A6:U6')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('E7:M'.$rows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $this->excel->getActiveSheet()->fromArray($header, null, 'A6');
                    $this->excel->getActiveSheet()->fromArray($details, null, 'A7');

                    $filename = 'Item Fast Moving.xls'; //save our workbook as this file name
                    header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
                    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
                    header('Cache-Control: max-age=0'); //no cache

                    $writer = new Xlsx($this->excel);
                    $writer->save('php://output');
                }
                break;

            case "INV_UBG": /*Un-Billed GRV*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkexcel'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkexcel'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $fieldName = explode(',' , $this->input->post('fieldNameChkexcel'));
                    $caption = explode(',' , $this->input->post('captionChkexcel'));

                    $header = array(
                        $this->lang->line('transaction_doc_number'),
                        $this->lang->line('common_reference_number'),
                        $this->lang->line('transaction_doc_date')
                    );
                    if (!empty($caption)) {
                        foreach ($caption as $val) {
                            $header[] = $val;
                            $headerSecond = array(
                                $this->lang->line('common_currency'),
                                $this->lang->line('transaction_grn_value'),
                                $this->lang->line('transaction_invoice_value'),
                                $this->lang->line('transaction_balance'),
                            );
                        }
                    }

                    $this->excel->getActiveSheet()->setTitle('Unbilled GRV');
                    $data = $this->Report_model->fetch_unbilled_GRV_for_excel($fieldName);
                    $details = $data['details'];
                    $rows = $data['rowCount'];

                    $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
                    $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('transaction_unbilled_grv')], null, 'A2');
                    $this->excel->getActiveSheet()->fromArray([$this->common_data['company_data']['company_name']], null, 'A1');
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('common_as_of_date') . ' : ' . $this->input->post('from')], null, 'A3');

                    $this->excel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $this->excel->getActiveSheet()->getStyle('A6:U7')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('A6:U7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $this->excel->getActiveSheet()->getStyle('E7:M'.$rows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $this->excel->getActiveSheet()->mergeCells('A6:A7');
                    $this->excel->getActiveSheet()->mergeCells('B6:B7');
                    $this->excel->getActiveSheet()->mergeCells('C6:C7');
                    $this->excel->getActiveSheet()->mergeCells('D6:G6');
                    $this->excel->getActiveSheet()->fromArray($header, null, 'A6');
                    $this->excel->getActiveSheet()->fromArray($headerSecond, null, 'D7');
                    $this->excel->getActiveSheet()->fromArray($details, null, 'A8');

                    $filename = 'Unbilled GRV.xls'; //save our workbook as this file name
                    header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
                    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
                    header('Cache-Control: max-age=0'); //no cache

                    $writer = new Xlsx($this->excel);
                    $writer->save('php://output');
                }
                break;

            case "INV_IIQ": /*item Inquiry*/
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $isBarcode = false;
                    $isSeconeryItemCode = false;
                    $isPartNo = false;
                    $fieldName = explode(',' , $this->input->post('fieldNameChkexcel'));
                    $warehouses = load_location_drop();

                    $header = array(
                        $this->lang->line('transaction_common_item_code'),
                        $this->lang->line('common_description'),
                        $this->lang->line('common_uom')
                    );

                    if (!empty($warehouses)) {
                        $header[] = $this->lang->line('transaction_qty_in_hand');
                        foreach ($warehouses as $val) {
                            $val["wareHouseDescription"] = str_replace("<br>","        ",$val["wareHouseDescription"]);
                            $val["wareHouseDescription"] = str_replace("&nbsp;","  ",$val["wareHouseDescription"]);
                            $headerSec[] = $val["wareHouseCode"] . " | " . $val["wareHouseDescription"];
                        }
                        $headerSec[] = $this->lang->line('common_total');
                    }

                    $headerSec[] = $this->lang->line('transaction_item_on_order');
                    $headerSec[] = $this->lang->line('transaction_commited');
                    $headerSec[] = $this->lang->line('transaction_in_un_approved_documnet');
                    $headerSec[] = $this->lang->line('transaction_net_stock');
                    $headerSec[] = $this->lang->line('transaction_min_stock');
                    $headerSec[] = $this->lang->line('transaction_item_below_stock');
                    $headerSec[] = $this->lang->line('erp_item_master_recorder_level');
                    if (isset($fieldName)) {
                        if (in_array("barcode", $fieldName)) {
                            $headerSec[] = $this->lang->line('transaction_barcode') ; // barcode
                            $isBarcode = true;
                        }
                        if (in_array("seconeryItemCode", $fieldName)) {
                            $headerSec[] = $this->lang->line('erp_item_master_secondary_code') ; // Secondary Code
                            $isSeconeryItemCode = true;
                        }
                        if (in_array("partNo", $fieldName)) {
                            $headerSec[] = $this->lang->line('transaction_part_no') ; // part No
                            $isPartNo = true;
                        }
                    }

                    $this->excel->getActiveSheet()->setTitle('Item Inquiry Report');
                    $data = $this->Report_model->fetch_item_inquiry_for_excel($isBarcode,$isSeconeryItemCode,$isPartNo );
                    $details = $data['details'];

                    $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

                    $num = count($warehouses) + 12;
                    $letter = '';
                    While ($num > 0) {
                        $numeric = ($num - 1) % 26;
                        $letter = chr(65 + $numeric) . $letter;
                        $num = intval(($num - 1) / 26);
                    }

                    $num2 = count($warehouses) + 4;
                    $colMerge = '';
                    While ($num2 > 0) {
                        $numeric = ($num2 - 1) % 26;
                        $colMerge = chr(65 + $numeric) . $colMerge;
                        $num2 = intval(($num2 - 1) / 26);
                    }


                    $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('transaction_item_inquiry_report')], null, 'A2');
                    $this->excel->getActiveSheet()->fromArray([$this->common_data['company_data']['company_name']], null, 'A1');
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('common_as_of_date') . ' : ' . current_date(false)], null, 'A3');
                    $this->excel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->getStyle('A6:' . $letter . '7')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('A6:' . $letter . '7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $this->excel->getActiveSheet()->mergeCells('A6:A7');
                    $this->excel->getActiveSheet()->mergeCells('B6:B7');
                    $this->excel->getActiveSheet()->mergeCells('C6:C7');
                    $this->excel->getActiveSheet()->mergeCells('D6:'. $colMerge . '6');

                    $this->excel->getActiveSheet()->fromArray($header, null, 'A6');
                    $this->excel->getActiveSheet()->fromArray($headerSec, null, 'D7');
                    $this->excel->getActiveSheet()->fromArray($details, null, 'A8');

                    $filename = 'Item Inquiry Report.xls'; //save our workbook as this file name
                    header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
                    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
                    header('Cache-Control: max-age=0'); //no cache

                    $writer = new Xlsx($this->excel);
                    $writer->save('php://output');
                }
                break;

            case 'INV_IBSO' : /*Below Min Stock / ROL*/
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $type = '';
                    $typeAs = $this->input->post('fieldNameChkexceltypecheck1');
                    $fieldName = explode(',' , $this->input->post('fieldNameChkexcel'));
                    $isBarcode = false;
                    $isPartNo = false;
                    $isSeconeryItemCode = false;

                    if($typeAs == 'itembelowstock')
                    {
                        $type = 'Below Minimum Stock';
                    }else if($typeAs == 'itembelowro')
                    {
                        $type = 'Item Below ROL';
                    }

                    $header = array(
                        $this->lang->line('transaction_common_item_code'),
                        $this->lang->line('common_description')
                    );
                    if (isset($fieldName)) {
                        if (in_array("barcode", $fieldName)) {
                            $header[] = $this->lang->line('transaction_barcode') ; // barcode
                            $isBarcode = true;
                        }
                        if (in_array("partNo", $fieldName)) {
                            $header[] = $this->lang->line('transaction_part_no') ; // part No
                            $isPartNo = true;
                        }
                        if (in_array("seconeryItemCode", $fieldName)) {
                            $header[] = $this->lang->line('erp_item_master_secondary_code') ; // Secondary Code
                            $isSeconeryItemCode = true;
                        }
                    }

                    $header[] = $this->lang->line('common_uom') ;
                    $header[] = $this->lang->line('common_total') ;
                    $header[] = $this->lang->line('transaction_min_stock') ;
                    $header[] = $this->lang->line('erp_item_master_recorder_level') ;
                    $header[] = $this->lang->line('transaction_item_on_order') ;
                    $header[] = $this->lang->line('transaction_commited') ;
                    $header[] = $this->lang->line('transaction_in_un_approved_documnet') ;

                    $this->excel->getActiveSheet()->setTitle('Below Min Stock ROL');
                    $data = $this->Report_model->fetch_item_ibmso_for_excel($typeAs,$fieldName,$isBarcode, $isPartNo, $isSeconeryItemCode);
                    $details = $data['details'];
                    $rows = $data['rowCount'];

                    $warehouse = $this->Report_model->get_warehouse();
                    $warehouseString = str_replace("<br>"," : ",join(" | ",$warehouse));
                    $this->excel->getActiveSheet()->setCellValue('A4', $this->lang->line('common_warehouse') . ' : ' . $warehouseString);
                    $this->excel->getActiveSheet()->mergeCells('A4:H4');
                    $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

                    $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->fromArray(['Below Min Stock / ROL'], null, 'A2');
                    $this->excel->getActiveSheet()->fromArray([$this->common_data['company_data']['company_name']], null, 'A1');
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('common_as_of_date') . ' : ' . current_date(false)], null, 'A3');
                    $this->excel->getActiveSheet()->fromArray([$this->lang->line('common_type') . ' : ' . $type], null, 'A4');
                    $this->excel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('A6:U6')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                    $this->excel->getActiveSheet()->getStyle('D7:J'.$rows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $this->excel->getActiveSheet()->fromArray($header, null, 'A6');
                    $this->excel->getActiveSheet()->fromArray($details, null, 'A7');

                    $filename = 'Below Min Stock / ROL.xls'; //save our workbook as this file name
                    header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
                    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
                    header('Cache-Control: max-age=0'); //no cache

                    $writer = new Xlsx($this->excel);
                    $writer->save('php://output');
                }
                break;

        }
    }

    function export_excel_stock_aging_report()
    {
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('inventory', $primaryLanguage);
        $this->lang->load('common', $primaryLanguage);
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->load->database();

       $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkexcel'));
        $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
        $this->form_validation->set_rules('warehouseid[]', 'Warehouse', 'trim|required');
        if($_POST["fieldNameChk"]) {
            if(in_array("companyLocalAmount", $_POST["fieldNameChk"]) && in_array("companyReportingAmount", $_POST["fieldNameChk"])){
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
            } else if(!in_array("companyLocalAmount", $_POST["fieldNameChk"]) && !in_array("companyReportingAmount", $_POST["fieldNameChk"])){
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
            }
        }
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
            $isRep = false;
            $isLoc = false;

            $fieldName = explode(',', $this->input->post('fieldNameChkexcel'));
            $header = array(
                $this->lang->line('transaction_common_item_code'), // Item Code
                $this->lang->line('transaction_common_item_description'), // Item Description
                $this->lang->line('common_uom'),
                $this->lang->line('common_qty'),
            );

            if (isset($fieldName)) {

                if (in_array("companyreportingcurrency", $fieldName)) {
                    $header[] = $this->common_data['company_data']['company_reporting_currency'];
                    $headerSec[] = $this->lang->line('inventory_wac');
                    $headerSec[] = $this->lang->line('common_total');
                    $isRep = true;
                }
                if (in_array("companyLocalAmount", $fieldName)) {
                    $header[] = $this->common_data['company_data']['company_default_currency'];
                    $headerSec[] = $this->lang->line('inventory_wac');
                    $headerSec[] = $this->lang->line('common_total');
                    $isLoc = true;
                }
            }
            $this->excel->getActiveSheet()->mergeCells('E6:F6');
            $aging = array("0-30", "31-60", "61-90", "91-120", "121-365", "366-730", "731");
            $agingcolumn = array("<=30", "31-60", "61-90", "91-120", "121-365", "366-730", "Over 730");


            $col = 6;
            $j = 'G';
            foreach($agingcolumn as $value) {
                $this->excel->getActiveSheet()->setCellValue([$col, 6], $value);
                $headerSec[] = $this->lang->line('common_qty');
                $headerSec[] = $this->lang->line('common_value');
                $col=$col+2;
                $i = $j++;
                $this->excel->getActiveSheet()->mergeCells($i.'6'.':'.$j.'6');
                $j++;
            }


            $this->excel->getActiveSheet()->setTitle('Stock Aging Report');
            $data = $this->Report_model->fetch_stock_aging_for_excel($isRep, $isLoc, $aging, $fieldName);
            $details = $data['details'];
            $rows = $data['rowCount'];

            $warehouse = $this->Report_model->get_warehouse();
            $warehouseString = str_replace("<br>"," : ",join(" | ",$warehouse));
            $this->excel->getActiveSheet()->setCellValue('A4', $this->lang->line('common_warehouse') . ' : ' . $warehouseString);
            $this->excel->getActiveSheet()->mergeCells('A4:AA4');
            $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
            $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->fromArray(['Stock Aging Report'], null, 'A2');
            $this->excel->getActiveSheet()->fromArray([$this->common_data['company_data']['company_name']], null, 'A1');
            $this->excel->getActiveSheet()->fromArray([$this->lang->line('common_as_of_date') . ' : ' . $this->input->post('from')], null, 'A3');
            $this->excel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
            $this->excel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->getStyle('A6:AA7')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
            $this->excel->getActiveSheet()->getStyle('A6:AA7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->getStyle('A7:AA7')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
            $this->excel->getActiveSheet()->getStyle('A7:AA7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $this->excel->getActiveSheet()->getStyle('D7:' . $j . $rows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->fromArray($header, null, 'A6');
            $this->excel->getActiveSheet()->fromArray($headerSec, null, 'E7');
            $this->excel->getActiveSheet()->fromArray($details, null, 'A8');

            $filename = 'Stock Aging Report.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
            header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache

            $writer = new Xlsx($this->excel);
            $writer->save('php://output');
        }

    }


    function get_collection_detail_report_group()
    {
    
        $currency = $this->input->post('currency');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $customer = $this->input->post('customerID');
        $segment = $this->input->post('segment');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date To is  required
            </div>';
        } else {
            
            $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
            } else {

                $errorHTML = $this->group_unlink(array("CUST","SEG"));
                if ($errorHTML) {
                    echo warning_message($errorHTML);
                }else { 
                    $data["details"] = $this->Report_model->get_collection_detail_report_group($currency, $customer, $segment);
                    $data["type"] = "html";
                    $data["currency"] = $currency;
                    echo $html = $this->load->view('system/accounts_receivable/report/load-collection-details-report', $data, true);
                }

            
            }
        }
    }

    function get_collection_detail_report_group_pdf()
    {
        $currency = $this->input->post('currency');
        $customer = $this->input->post('customerID');
        $segment = $this->input->post('segment');

        $data["details"] = $this->Report_model->get_collection_detail_report_group($currency, $customer, $segment);
        $data["type"] = "pdf";
        $data["currency"] = $currency;

        $html = $this->load->view('system/accounts_receivable/report/load-collection-details-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');

    }

    function get_customer_balance_report_group()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);

        $companyID = current_companyID();
        $this->form_validation->set_rules('customerIDgrp[]', 'Customer', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            
                $companyID = $this->Report_model->get_group_company();
                $company= join(',', $companyID);
                $customerGrp = $this->input->post('customerIDgrp');
                $customerID = join(',', $customerGrp);

                $groupcustomer = fetch_customerid_from_group("$customerID","$company");
                $groupcustomer = (join(',', array_column($groupcustomer,'customerMasterID')));
                //print_r($groupcustomer);exit;

            $qry = "SELECT
  companyLocalCurrency,
  companyReportingCurrency
FROM
    `srp_erp_generalledger`
WHERE
srp_erp_generalledger.partyAutoID IN ($groupcustomer)
    AND srp_erp_generalledger.companyID IN ($company)
AND `subLedgerType` = '3'
and documentDate<='$fromdt'
group by partyAutoID,GLAutoID";
            $outputcrr = $this->db->query($qry)->row_array();

            $data["details"] = $this->Report_model->get_customer_balance_report_group($fromdt);
            $data["type"] = "html";
            $data["loccurr"] = $outputcrr['companyLocalCurrency'];
            $data["repcurr"] = $outputcrr['companyReportingCurrency'];
            $data["loccurrDec"] = fetch_currency_desimal($outputcrr['companyLocalCurrency']);
            $data["repcurrDec"] = fetch_currency_desimal($outputcrr['companyReportingCurrency']);
            $data["currency"] = $currency;
            echo $html = $this->load->view('system/accounts_receivable/report/load-customer-balance-report', $data, true);
        }
    }


    function get_customer_balance_report_pdf_group()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);
        $companyID = $this->Report_model->get_group_company();
        $company= join(',', $companyID);
        $customerGrp = $this->input->post('customerIDgrp');
       
        $customerID = join(',', $customerGrp);

        $groupcustomer = fetch_customerid_from_group("$customerID","$company");
        $groupcustomer = (join(',', array_column($groupcustomer,'customerMasterID')));
       

        $qry = "SELECT
  companyLocalCurrency,
  companyReportingCurrency
FROM
    `srp_erp_generalledger`
WHERE
srp_erp_generalledger.partyAutoID IN ($groupcustomer)
    AND srp_erp_generalledger.companyID IN ($company)
AND `subLedgerType` = '3'
and documentDate<='$fromdt'
group by partyAutoID,GLAutoID";
        $outputcrr = $this->db->query($qry)->row_array();


        $data["details"] = $this->Report_model->get_customer_balance_report_group($fromdt);
        $data["type"] = "pdf";
        $data["loccurr"] = $outputcrr['companyLocalCurrency'];
        $data["repcurr"] = $outputcrr['companyReportingCurrency'];
        $data["loccurrDec"] = fetch_currency_desimal($outputcrr['companyLocalCurrency']);
        $data["repcurrDec"] = fetch_currency_desimal($outputcrr['companyReportingCurrency']);
        $data["currency"] = $currency;
        $html = $this->load->view('system/accounts_receivable/report/load-customer-balance-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }


    function get_vendor_balance_report_group()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);
       
         $this->form_validation->set_rules('supplierIDgrp[]', 'Supplier', 'required');
        

        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
      
             $companyID = $this->Report_model->get_group_company();
             $company= join(',', $companyID);
             $supplierIDgrp = $this->input->post('supplierIDgrp');
             $supplierID = join(',', $supplierIDgrp);
             $groupsupplier = fetch_supplierID_from_group("$supplierID","$company");
             $groupsupplier = (join(',', array_column($groupsupplier,'SupplierMasterID')));
        
        $qry = "SELECT
  companyLocalCurrency,
  companyReportingCurrency
FROM
    `srp_erp_generalledger`
WHERE
srp_erp_generalledger.partyAutoID IN ($groupsupplier)
    AND srp_erp_generalledger.companyID IN ($company)
AND `subLedgerType` = '2'
and documentDate<='$fromdt'
group by partyAutoID,GLAutoID";
            $outputcrr = $this->db->query($qry)->row_array();

            $data["details"] = $this->Report_model->get_vendor_balance_report_group($fromdt);
            $data["type"] = "html";
            $data["loccurr"] = $outputcrr['companyLocalCurrency'];
            $data["repcurr"] = $outputcrr['companyReportingCurrency'];

            $data["loccurrDec"] = fetch_currency_desimal($outputcrr['companyLocalCurrency']);
            $data["repcurrDec"] = fetch_currency_desimal($outputcrr['companyReportingCurrency']);

            $data["currency"] = $currency;
            echo $html = $this->load->view('system/accounts_payable/report/load-vendor-balance-report', $data, true);
        }
    }
    function get_vendor_balance_report_pdf_group()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);
        $companyID = $this->Report_model->get_group_company();
        $company= join(',', $companyID);
        $supplierIDgrp = $this->input->post('supplierIDgrp');
        $supplierID = join(',', $supplierIDgrp);
        $groupsupplier = fetch_supplierID_from_group("$supplierID","$company");
        $groupsupplier = (join(',', array_column($groupsupplier,'SupplierMasterID')));
       
        $qry = "SELECT
  companyLocalCurrency,
  companyReportingCurrency
FROM
    `srp_erp_generalledger`
WHERE
srp_erp_generalledger.partyAutoID IN ($groupsupplier)
    AND srp_erp_generalledger.companyID IN ($company)
AND `subLedgerType` = '2'
and documentDate<='$fromdt'
group by partyAutoID,GLAutoID";
        $outputcrr = $this->db->query($qry)->row_array();


        $data["details"] = $this->Report_model->get_vendor_balance_report_group($fromdt);
        $data["type"] = "pdf";
        $data["loccurr"] = $outputcrr['companyLocalCurrency'];
        $data["repcurr"] = $outputcrr['companyReportingCurrency'];

        $data["loccurrDec"] = fetch_currency_desimal($outputcrr['companyLocalCurrency']);
        $data["repcurrDec"] = fetch_currency_desimal($outputcrr['companyReportingCurrency']);
        $data["currency"] = $currency;
        $html = $this->load->view('system/accounts_payable/report/load-vendor-balance-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function fetch_supplierDropdown()
    {
        $supplier_arr = array();
        $partyCategoryID = $this->input->post("partyCategoryID");
        $activeStatus = $this->input->post("status_filter");
        $status_filter='';
        $partyCategoryID_join="";
        $countcat = sizeof($partyCategoryID);
        $companyID = current_companyID();
       $customercat = $this->db->query("SELECT COUNT(partyCategoryID) as partyCategorycount FROM srp_erp_partycategories WHERE companyID = '{$companyID}' AND partyType = 2")->row('partyCategorycount');
        if(!empty($partyCategoryID)){
            $partyCategoryID_join_filter =  join(",",$partyCategoryID);
            $partyCategoryID_join = "AND partyCategoryID IN ($partyCategoryID_join_filter)";
        }
        if (!empty($activeStatus)) {
            if($activeStatus==1){
                $status_filter = "AND isActive = 1 ";
            }elseif($activeStatus==2){
                $status_filter = "AND isActive = 0 ";
            }else{
                $status_filter = '';
            }
        }
        $companyID = current_companyID();
        if($countcat == $customercat)
        {
            $partyCategoryID_join = '';
        }
        $customer= $this->db->query("SELECT supplierAutoID,supplierName,supplierSystemCode,supplierCountry 
                                          FROM `srp_erp_suppliermaster` WHERE masterApprovedYN = 1 AND `companyID` = $companyID AND (deletedYN = 0 OR deletedYN IS NULL) $partyCategoryID_join $status_filter ")->result_array();
        if (isset($customer)) {
            foreach ($customer as $row) {
                $supplier_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierSystemCode'] ?? '') ? trim($row['supplierSystemCode'] ?? '') . ' | ' : '') . trim($row['supplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
            }
        }

        echo form_dropdown('supplierID[]', $supplier_arr, '', 'class="form-control" id="supplierID" multiple="" ');
    }

    function get_collection_summery_report_group()
    {
        $currency = $this->input->post('currency');
        $financeyear = $this->input->post('financeyear');
        $this->db->select('beginingDate,endingDate');
        $this->db->where('groupFinanceYearID', $financeyear);
        $this->db->from('srp_erp_groupfinanceyear ');
        $financeyeardtl = $this->db->get()->row_array();
        $beginingDate = $financeyeardtl['beginingDate'];
        $endingDate = $financeyeardtl['endingDate'];

        $start = (new DateTime($beginingDate));
        $end = (new DateTime($endingDate));

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $datearr = [];
        foreach ($period as $dt) {
            $dat = $dt->format("Y-m");
            $text = $dt->format("Y-M");
            $datearr[$dat] = $text;
        }
        $companyID = $this->Report_model->get_group_company();

        $previuosyeardtl = $this->db->query("SELECT max(beginingDate) as beginingDate,max(endingDate) as endingDate from srp_erp_groupfinanceyear where beginingDate < {$beginingDate} AND groupID = " . current_companyID() . "  ")->row_array();
                    
        $previousbegindate = $previuosyeardtl['beginingDate'];
        $previousenddate = $previuosyeardtl['endingDate'];

        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        $this->form_validation->set_rules('segment[]', 'Segment', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->Report_model->get_collection_summery_report_group($datearr, $previousbegindate, $previousenddate, $beginingDate, $endingDate);
            $data["header"] = $datearr;
            $data["previousbeginingdate"] = $previousbegindate;
            $data["previousenddate"] = $previousenddate;
            $data["type"] = "html";
            $data["currency"] = $currency;
            echo $html = $this->load->view('system/accounts_receivable/report/load-collection-summary-report', $data, true);
        }
    }

    function get_collection_summery_report_group_pdf()
    {
        $currency = $this->input->post('currency');
        $financeyear = $this->input->post('financeyear');
        $this->db->select('beginingDate,endingDate');
        $this->db->where('groupFinanceYearID', $financeyear);
        $this->db->from('srp_erp_groupfinanceyear ');
        $financeyeardtl = $this->db->get()->row_array();
        $beginingDate = $financeyeardtl['beginingDate'];
        $endingDate = $financeyeardtl['endingDate'];
        $start = (new DateTime($beginingDate));
        $end = (new DateTime($endingDate));

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $datearr = [];
        foreach ($period as $dt) {
            $dat = $dt->format("Y-m");
            $text = $dt->format("Y-M");
            $datearr[$dat] = $text;
        }

        $previuosyeardtl = $this->db->query("SELECT max(beginingDate) as beginingDate,max(endingDate) as endingDate from srp_erp_groupfinanceyear where beginingDate < {$beginingDate} AND groupID = " . current_companyID() . "  ")->row_array();
                
        $previousbegindate = $previuosyeardtl['beginingDate'];
        $previousenddate = $previuosyeardtl['endingDate'];

        $data["details"] = $this->Report_model->get_collection_summery_report_group($datearr, $previousbegindate, $previousenddate, $beginingDate, $endingDate);
        $data["header"] = $datearr;
        $data["type"] = "pdf";
        $data["currency"] = $currency;
        $html = $this->load->view('system/accounts_receivable/report/load-collection-summary-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }
    function loadSupplier()
    {
        echo json_encode($this->Report_model->loadSupplier());
    }
    function load_statusbased_customer()
    {
        $customer_arr = array();
        $activeStatus = $this->input->post("activeStatus");
        $document = $this->input->post("document");

        $status_filter = '';
        $companyID = current_companyID();
        if (!empty($activeStatus)) {
            if($activeStatus==1){
                $status_filter = "AND isActive = 1 ";
            }elseif($activeStatus==2){
                $status_filter = "AND isActive = 0 ";
            }else{
                $status_filter = '';
            }
        }
        $companyID = current_companyID();
        $type = $this->input->post("type");

        if($type == 1){
            
            $customer= $this->db->query("SELECT `customerAutoID`, `customerName`, `customerSystemCode`, `customerCountry` 
                                              FROM `srp_erp_customermaster` WHERE `companyID` = $companyID AND `deletedYN` =0  $status_filter")->result_array();
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['customerAutoID'] ?? '')] = (trim($row['customerSystemCode'] ?? '') ? trim($row['customerSystemCode'] ?? '') . ' | ' : '') . trim($row['customerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
                }
            }
        }
        if($document == 'PostDatedCheques'){
            //echo form_dropdown('customerAutoID[]', $customer_arr, '', 'class="orm-control customerAutoID" id="customerAutoID" multiple="multiple" ');
            echo form_dropdown('customerID[]', $customer_arr, '', 'class="form-control" id="customerID" multiple="multiple"'); 

        }else{
            echo form_dropdown('customerAutoID[]', $customer_arr, '', 'class="orm-control customerAutoID" id="customerAutoID" multiple="multiple" ');
        }
    }


    function save_report()
    {
        
        $this->form_validation->set_rules('description', 'Report Description', 'trim|required');
        $this->form_validation->set_rules('sortOrder', 'Sort Order', 'trim|required');  
        $this->form_validation->set_rules('system_type', 'System Type', 'trim|required');  

        if ($this->form_validation->run() == FALSE) {

            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));

        } else {
            echo json_encode($this->Report_model->save_report());
        }

    }


    function load_report()
    {
        $result = $this->Report_model->load_report();

        $this->db->select('id, sortOrder, structureMasterID, detail_description, detail_code, CONCAT(detail_code," ", "-"," ", detail_description) as combined_description');
        $this->db->from('srp_erp_reporting_structure_details');
        //$this->db->where('companyID', current_companyID());
        $descriptions = $this->db->get()->result_array();

        $data['report'] = $result;
        $data['descriptions'] = $descriptions;
        $this->load->view('system/load_ajax_report_structure_view', $data);
    }

    function update_report()
    {
        $this->form_validation->set_rules('description', 'Report Description', 'trim|required');
        $this->form_validation->set_rules('sortOrder', 'Sort Order', 'trim|required');  
        $this->form_validation->set_rules('system_type', 'System Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Report_model->update_report());
        }
    }

    function save_report_describe(){
        $this->form_validation->set_rules('describe_text', 'Description', 'trim|required');
        $this->form_validation->set_rules('code', 'Code', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Report_model->save_report_describe());
        }
    }

    function load_report_describe(){
       
        $id = $this->input->post('id');
        //$sortOrder = $this->input->post('sortOrder');

        $this->db->select('id, structureMasterID, detail_description, sortOrder');
        $this->db->where('id', $id);
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_reporting_structure_details');
        $result = $this->db->get()->row_array();

       // return $result;
        echo json_encode($result);

        // $html = '<table class="table table-hover">';
        // $x = 1;
        // foreach($result as $val){
        //     $html .= '<tr>';
        //     $html .= '<td>' . $x . '</td>';
        //     $html .= '<td>' . $val['detail_description'] . '</td>';
        //     $html .= '<td style="text-align: right;">
        //                     <a onclick="editCategory(' . $val['id'] . ')" ><span style="color:#ff3f3a" class="glyphicon glyphicon-edit "></span></a>
        //                     <a onclick="deleteCategory(' . $val['id'] . ')" ><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a>&nbsp;&nbsp;
        //                 </td>';
        //     $html .= '</tr>';

        //     $x++;
        // }
        // $html .= '</table>';
        
        // echo $html;
    
    }

    function description_delete(){
        $id = $this->input->post('id');
        echo json_encode($this->Report_model->description_delete($id));
    }


    function load_activity_code(){
        $this->load->view('system/activity_code');
    }

    function save_activity_code(){
        echo json_encode($this->Report_model->save_activity_code()); 
    }

    function activity_code_table()
    {
        $company = $this->input->post('company');

        $where = "company_id = " . $company . "";
        $this->datatables->select("*, id, is_active, activity_code");
        $this->datatables->from('srp_erp_activity_code_main');
        $this->datatables->where($where);
        $this->datatables->add_column('status', '$1', 'activity_code_status(is_active)');
        $this->datatables->add_column('edit', '$1', 'load_activity_code_action(id, activity_code)');
        echo $this->datatables->generate();
    }

    function load_activity_code_edit(){
        echo json_encode($this->Report_model->load_activity_code_edit()); 
    }

    function delete_activity_code(){
        $status=$this->db->delete('srp_erp_activity_code_main', array('id' => trim($this->input->post('id') ?? '')));
        if($status){
            echo json_encode(array('s', ' Deleted Successfully.'));
        }else {
            echo json_encode(array('e', ' Error in Deletion.'));
        }
    }

    function load_config_report()
    {
        $activityCode_AutoID = $this->input->post('activityCode_AutoID');
        $result = $this->Report_model->load_report();

        $this->db->select('id, sortOrder, structureMasterID, detail_description, detail_code, CONCAT(detail_code," ", "-"," ", detail_description) as combined_description');
        $this->db->from('srp_erp_reporting_structure_details');
        //$this->db->where('companyID', current_companyID());
        $descriptions = $this->db->get()->result_array();

        $this->db->select('id, main_id, rpt_struc_master_id, rpt_struc_detail_id, sort_order');
        $this->db->from('srp_erp_activity_code_sub');
        $this->db->where('main_id', $activityCode_AutoID);
        $config_details = $this->db->get()->result_array();

       // echo '<pre>';print_r($config_details);exit;

        $data['report'] = $result;
        $data['descriptions'] = $descriptions;
        $data['config_details'] = $config_details;
        $this->load->view('system/load_ajax_report_structure_config_view', $data);
    }

    function save_details(){
        echo json_encode($this->Report_model->save_details()); 
    }

    
    function getOtSummary(){

        $companyID=current_companyID();
        $fromDate=$this->input->post('fromDate');
        $toDate=$this->input->post('toDate');

        $query = $this->db->query("SELECT DISTINCT seg.description
        FROM srp_erp_pay_empattendancereview AS atd
        JOIN srp_employeesdetails AS empdet ON empdet.EIdNo = atd.empID
        JOIN srp_erp_segment AS seg ON empdet.segmentID = seg.segmentID
        WHERE atd.companyID = $companyID 
        AND atd.confirmedYN = 1
        AND DATE_FORMAT(atd.attendanceDate, '%Y-%m') BETWEEN '$fromDate' AND '$toDate'")->result_array();

        echo json_encode($query);
    }

    function getOtDetails(){

        $this->load->helper('report_helper'); 
        $companyID=current_companyID();
        $fromDate=$this->input->post('fromDate');
        $toDate=$this->input->post('toDate');

        $query = $this->db->query("SELECT seg.description,atd.empID,empdet.Ename2,empdet.ECode,empdet.EmpSecondaryCode, ROUND((SUM(atd.OTHours)/60),2) AS totalOTHours,SUM(atd.paymentOT) AS totalPaymentOT
        FROM 
        srp_erp_pay_empattendancereview AS atd
        JOIN 
        srp_employeesdetails AS empdet ON empdet.EIdNo = atd.empID
        JOIN 
        srp_erp_segment AS seg ON empdet.segmentID = seg.segmentID
        WHERE 
        atd.companyID = $companyID 
        AND atd.confirmedYN = 1
        AND atd.attendanceDate BETWEEN '$fromDate' AND '$toDate'
        GROUP BY 
        seg.description,atd.empID, empdet.EmpSecondaryCode;")->result_array();

        foreach ($query as &$row) {
            $row['totalOTHours'] = customRound($row['totalOTHours']);
        }

        echo json_encode($query);
    }

    function getOtDetailsDateWise(){

        $this->load->helper('report_helper'); 
        $companyID=current_companyID();
        $fromDate=$this->input->post('fromDate');
        $toDate=$this->input->post('toDate');

        $query = $this->db->query("SELECT atd.empID,empdet.Ename2,empdet.ECode,atd.attendanceDate,SUM(atd.paymentOT) AS totalPaymentOT, ROUND((SUM(atd.OTHours)/60),2) AS totalOTHours, ROUND((SUM(atd.NDaysOT)/60),2) AS totalNormalDayOT,ROUND((SUM(atd.weekendOTHours)/60),2) AS totalWeekendOT,ROUND((SUM(atd.holidayOTHours)/60),2) AS totalHolidayOT
        FROM 
		srp_erp_pay_empattendancereview AS atd
        JOIN 
		srp_employeesdetails AS empdet ON empdet.EIdNo = atd.empID
        JOIN 
		srp_erp_segment AS seg ON empdet.segmentID = seg.segmentID
        WHERE 
        atd.companyID = $companyID 
        AND atd.confirmedYN = 1
        AND atd.attendanceDate BETWEEN '$fromDate' AND '$toDate'
        GROUP BY 
		seg.description,atd.empID,empdet.EmpSecondaryCode,atd.attendanceDate;")->result_array();

        foreach ($query as &$row) {
            $row['totalNormalDayOT'] = customRound($row['totalNormalDayOT']);
            $row['totalWeekendOT'] = customRound($row['totalWeekendOT']);
            $row['totalHolidayOT'] = customRound($row['totalHolidayOT']);
        }

        echo json_encode($query);
    }
  
}