<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');


class Template_paysheet extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        ini_set('max_execution_time', 500);
        ini_set('memory_limit', '2048M');
        $this->load->model('Template_paySheet_model');
        $this->load->model('Payment_voucher_model');
        $this->load->helper('template_paySheet');
        $this->load->helper('employee');
        $this->load->helpers('payable');
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);
        $this->lang->load('hrms_approvals', $primaryLanguage);
        $this->lang->load('profile', $primaryLanguage);

    }

    public function fetch_templates()
    {

        $isNonPayroll = $this->input->post('isNonPayroll');
        $this->datatables->select('templateID, templateDescription, documentCode, confirmedYN, isDefault, isNonPayroll', false)
            ->from('srp_erp_pay_template')
            ->add_column('defaultTemplate', '$1', 'confirm(confirmedYN)')
            ->add_column('status', '$1', 'template_status(templateID, isDefault, confirmedYN, isNonPayroll)')
            ->add_column('edit', '$1', 'load_paysheet_template_action(templateID, confirmedYN, templateDescription)')
            ->where('isNonPayroll', $isNonPayroll)
            ->where('companyID', current_companyID());
        echo $this->datatables->generate();

    }

    public function createTemplate()
    {
        echo json_encode($this->Template_paySheet_model->createTemplate());
    }

    public function cloneTemplate()
    {
        echo json_encode($this->Template_paySheet_model->cloneTemplate());
    }

    public function templateHeaderDetails()
    {
        echo json_encode($this->Template_paySheet_model->templateHeaderDetails());
    }

    public function templateDetails()
    {
        echo json_encode($this->Template_paySheet_model->templateDetails());
    }

    public function templateDetails_view()
    {
        $payrollMasterID = $this->input->post('hidden_payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $segmentID = $this->input->post('segmentID');
        $hideZeroColumn = $this->input->post('hideZeroColumn');

        $data['masterData'] = $this->Template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);

        $lastMonth = array(
            date('Y', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01'))),
            date('m', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01')))
        );

        $without_last_month = 'Y';
        if ($this->input->post('from_approval') == 'Y') {
            $def = ($isNonPayroll == 'Y') ? 'Y' : 'N';
            $templateID = ($data['masterData']['templateID'] != null) ? ($data['masterData']['templateID']) : getDefault_template($def);
            $data['isForReverse'] = ($this->input->post('isForReverse') == 'Y') ? 'Y' : 'N';
            $data['isFromPrint'] = 'Y';
            $without_last_month = 'N';
        } else {
            $templateID = $this->input->post('templateId');
            $data['isFromPrint'] = 'N';
        }

        $data['header_det'] = $this->Template_paySheet_model->templateDetails($templateID, $without_last_month);

    

        $data['currency_groups'] = $this->Template_paySheet_model->currencyWiseSum($payrollMasterID, $isNonPayroll, $segmentID);
        $data['isForReverse'] = null;

        // echo '<pre>';
        // print_r($data); exit;

        if ($hideZeroColumn == 'Y') {
            $data['paysheetData'] = $this->Template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, $segmentID, $templateID);
            echo $this->load->view('system/hrm/print/paySheet_print', $data, true);
        } else {
            $data['paysheetData'] = $this->Template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, $segmentID);
            // echo '<pre>'; print_r($data['paysheetData']); echo '</pre>';
            echo $this->load->view('system/hrm/print/paySheet_print_withZero', $data, true);
        }
    }

    public function statusChangePaysheetTemplate()
    {
        echo json_encode($this->Template_paySheet_model->statusChangePaysheetTemplate());
    }

    public function referBack()
    {
        $id = $this->input->post('referID');
        $companyID = current_companyID();
        $details = $this->Template_paySheet_model->templateStatus($id);
        $table = ($details->isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        /*$isInUse = $this->db->query("SELECT documentCode FROM {$table} WHERE companyID={$companyID}
                                     AND templateID={$id} AND confirmedYN=1")->row('documentCode');*/
        $isInUse = null;
        if (!empty($isInUse)) {
            echo json_encode(['e', 'You can not refer back this.<br/>This template is used for the payroll', $isInUse]);
        } else {
            echo json_encode($this->Template_paySheet_model->referBack());
        }
    }

    public function deleteTemplate()
    {
        echo json_encode($this->Template_paySheet_model->deleteTemplate());
    }

    public function templateFields()
    {
        echo json_encode($this->Template_paySheet_model->templateFields());
    }

    public function templateDetailsSave()
    {
        echo json_encode($this->Template_paySheet_model->templateDetailsSave());
    }

    public function templateCaptionUpdate()
    {
        echo json_encode($this->Template_paySheet_model->templateCaptionUpdate());
    }

    public function templateSortOrderUpdate()
    {
        echo json_encode($this->Template_paySheet_model->templateSortOrderUpdate());
    }

    public function loadTemplate()
    {
        $tempHeaderDet = $this->Template_paySheet_model->templateHeaderDetails();
        $tempDet = $this->Template_paySheet_model->templateDetails();

        echo json_encode(array('error' => 0, 'header' => $tempHeaderDet, 'details' => $tempDet));
    }

    public function loadPaySheetData()
    {

        $this->form_validation->set_rules('payYear', 'Year ', 'trim|required|numeric');
        $this->form_validation->set_rules('payMonth', 'Month', 'trim|required|numeric');
        $this->form_validation->set_rules('payNarration', 'Narration', 'trim|required');
        $this->form_validation->set_rules('processingDate', 'Processing Date', 'trim|required|date');
        $this->form_validation->set_rules('visibleDate', 'Payslip Date', 'trim|required|date');
        $this->form_validation->set_rules('selectedEmployees', 'Employee', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $payYear = $this->input->post('payYear');
            $payMonth = $this->input->post('payMonth');
            $visibleDate = $this->input->post('visibleDate');
            $date_format_policy = date_format_policy();
            $visibleDate = input_format_date($visibleDate, $date_format_policy);
            $payrollFirstDate = date('Y-m-d', strtotime($payYear . '-' . $payMonth . '-01'));

            if ($payrollFirstDate > $visibleDate) {
                die(json_encode(['e', 'Payslip date can not be leaser than ' . convert_date_format($payrollFirstDate)]));
            }


            $isNonPayroll = $this->input->post('isNonPayroll'); // Payroll or Non payroll
            $isAlreadyProcessed = null; //$this->template_paySheet_model->loadPaySheetData($isNonPayroll);

           

            if ($isAlreadyProcessed == null) {
                $payYear = $this->input->post('payYear');
                $payMonth = $this->input->post('payMonth');
                $sendWithExp = $this->input->post('sendWithExp'); 
                $status = $this->Template_paySheet_model->currentMonthPaysheetData_status($payYear, $payMonth, $isNonPayroll);

                //die();
                if ($status[0] == 's') {
                    if ($sendWithExp != 1) {
                        $expenseClaims = $this->pendingExpensesClaims();

                        //echo '<pre>'; print_r($expenseClaims); echo '</pre>';die();
                        if ($expenseClaims[1] !== 0) {
                            die(json_encode($expenseClaims));
                        }
                    }
                    echo json_encode($this->Template_paySheet_model->insertPaySheetDataBasedOnEmployee());
                    
                    /*if($resultEmployee[2] != "" || $resultEmployee[2] != NULL){
                        $payroll_employees = $this->Template_paySheet_model->save_payroll_employees_reporting_structure($resultEmployee[2]);
                        $att_details = $this->Template_paySheet_model->update_attendance_details($resultEmployee[2]);
                        echo json_encode(array('s', 'Added Successfuly...'));
                    }*/
                    
                } else {
                    echo json_encode($status);
                }

            } else {
                echo json_encode(array('w', 'Already Payroll processed on given month.'));
            }
        }
    }

    public function loadPaySheetData_period_base()
    {
        $this->form_validation->set_rules('p_group', 'Payroll Group', 'trim|required|numeric');
        $this->form_validation->set_rules('period_id', 'Period', 'trim|required|numeric');
        $this->form_validation->set_rules('payNarration', 'Narration', 'trim|required');
        $this->form_validation->set_rules('processingDate', 'Processing Date', 'trim|required|date');
        $this->form_validation->set_rules('visibleDate', 'Payslip Date', 'trim|required|date');
        $this->form_validation->set_rules('selectedEmployees', 'Employee', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $pay_group = $this->input->post('p_group');
        $period_id = $this->input->post('period_id');
        $visibleDate = $this->input->post('visibleDate');
        $date_format_policy = date_format_policy();
        $visibleDate = input_format_date($visibleDate, $date_format_policy);

        $period_arr = $this->db->query("SELECT dateFrom, dateTo FROM srp_erp_hrperiod WHERE id={$period_id}")->row_array();

        $payrollFirstDate = $period_arr['dateFrom'];

        if ($payrollFirstDate > $visibleDate) {
            die(json_encode(['e', 'Payslip date can not be leaser than ' . convert_date_format($payrollFirstDate)]));
        }

        $isNonPayroll = $this->input->post('isNonPayroll'); // Payroll or Non payroll
        $isAlreadyProcessed = null; //$this->template_paySheet_model->loadPaySheetData($isNonPayroll);

        if ($isAlreadyProcessed == null) {
            $payYear = $this->input->post('payYear');
            $payMonth = $this->input->post('payMonth');
            $sendWithExp = $this->input->post('sendWithExp');
            $status = $this->Template_paySheet_model->currentMonthPaysheetData_status($payYear, $payMonth, $isNonPayroll);

            if ($status[0] == 's') {
                if ($sendWithExp != 1) {
                    $expenseClaims = $this->pendingExpensesClaims();

                    if ($expenseClaims[1] !== 0) {
                        die(json_encode($expenseClaims));
                    }
                }
                echo json_encode($this->Template_paySheet_model->insertPaySheetDataBasedOnEmployee_period_base($period_arr));

            } else {
                echo json_encode($status);
            }

        } else {
            echo json_encode(array('w', 'Already Payroll processed on given month.'));
        }
    }

    function getVisibleDate()
    {
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $lastDate = date('Y-m-t', strtotime($year . '-' . $month . '-01'));
        $lastDate = convert_date_format($lastDate);

        echo json_encode($lastDate);
    }

    public function fetchPaySheetData()
    {
        $payrollMasterID = $this->input->post('hidden_payrollID');
        $this->form_validation->set_rules('hidden_payrollID', 'Payroll ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $payrollDet = $this->Template_paySheet_model->fetchPaySheetData($payrollMasterID);
            $currencyWiseSum = $this->Template_paySheet_model->currencyWiseSum($payrollMasterID);
            echo json_encode(array('s', $payrollDet, $currencyWiseSum));
        }
    }

    public function fetch_paySheets()
    {
        $companyID = current_companyID();
        $isGroupAccess = getPolicyValues('PAC', 'All');

        $this->datatables->select('payrollMasterID, documentCode, payrollYear, payrollMonth, narration, confirmedYN, approvedYN, templateID', false)
            ->from('srp_erp_payrollmaster')
            ->edit_column('payrollMonth', '$1', 'payrollMonthInName(payrollYear, payrollMonth)')
            ->add_column('confirm', '$1', 'confirm(confirmedYN)')
            ->add_column('approve', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SP",payrollMasterID)')
            ->add_column('action', '$1', 'paySheetAction(payrollMasterID, confirmedYN, approvedYN, payrollYear, payrollMonth, N, templateID)')
            ->where('companyID', $companyID);

        if ($isGroupAccess == 1) {
            $currentEmp = current_userID();
            $this->datatables->join("(SELECT payID FROM srp_erp_payrollgroupincharge AS inCharge
                                      JOIN(
                                            SELECT payrollMasterID AS payID, accessGroupID
                                            FROM srp_erp_payrollheaderdetails WHERE companyID={$companyID}
                                            AND accessGroupID IS NOT NULL
                                            GROUP BY payrollMasterID, accessGroupID
                                      ) AS headerDet ON inCharge.groupID=headerDet.accessGroupID
                                      WHERE companyID={$companyID} AND empID={$currentEmp}
                                      GROUP BY payID) AS accTB", 'srp_erp_payrollmaster.payrollMasterID = accTB.payID');

        }
        echo $this->datatables->generate();
    }

    public function fetch_paySheets_conformation()
    {
        $userID = $this->common_data["current_userID"];
        $status = trim($this->input->post('approvedYN') ?? '');
        $companyid = current_companyID();

        /*
        * rejected = 1
        * not rejected = 0
        * */

        if($status == 0)
        {
            $where = array(
                'approve.documentID' => 'SP',
                'ap.documentID' => 'SP',
                'ap.employeeID' => $userID,
                'approve.approvedYN' => $status,
            );
            $this->datatables->select('payrollMasterID, t1.documentCode AS documentCode, payrollYear, payrollMonth, narration, approve.approvedYN as approvedYN,
        documentApprovedID, approvalLevelID', true)
                ->from('srp_erp_payrollmaster AS t1')
                ->join('srp_erp_documentapproved AS approve', 'approve.documentSystemCode = t1.payrollMasterID AND approve.approvalLevelID = t1.currentLevelNo')
                ->join('srp_erp_approvalusers AS ap', 'ap.levelNo = t1.currentLevelNo')
                ->where($where)
                ->where('t1.companyID', current_companyID())
                ->where('ap.companyID', current_companyID())
                ->add_column('payrollMonth', '$1', 'payrollMonthInName(payrollYear, payrollMonth)')
                ->add_column('level', "<center>Level $1</center>", 'approvalLevelID')
                ->add_column('documentCode_str', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'code\')')
                ->add_column('edit', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'edit\')')
                ->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"SP", payrollMasterID)');
            echo $this->datatables->generate('json', 'ISO-8859-1');
        }else
        {
            $where = array(
                'approve.documentID' => 'SP',
                't1.companyID' => $companyid,
                'approve.approvedEmpID' => $userID,

            );
            $this->datatables->select('payrollMasterID, t1.documentCode AS documentCode, payrollYear, payrollMonth, narration, approve.approvedYN as approvedYN,
        documentApprovedID, approvalLevelID', true)
                ->from('srp_erp_payrollmaster AS t1')
                ->join('srp_erp_documentapproved AS approve', 'approve.documentSystemCode = t1.payrollMasterID')
                ->where($where)
                ->where('t1.companyID', current_companyID())
                ->group_by('t1.payrollMasterID')
                ->group_by('approve.approvalLevelID')
                ->add_column('payrollMonth', '$1', 'payrollMonthInName(payrollYear, payrollMonth)')
                ->add_column('level', "<center>Level $1</center>", 'approvalLevelID')
                ->add_column('documentCode_str', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'code\')')
                ->add_column('edit', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'edit\')')
                ->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"SP", payrollMasterID)');
            echo $this->datatables->generate('json', 'ISO-8859-1');
        }



    }

    public function fetch_non_paySheets_conformation()
    {
        $userID = $this->common_data["current_userID"];
        $status = trim($this->input->post('approvedYN') ?? '');
        $companyid = current_companyID();

        /*
        * rejected = 1
        * not rejected = 0
        * */
        if($status == 0)
        {
            $where = array(
                'approve.documentID' => 'SPN',
                'ap.documentID' => 'SPN',
                'ap.employeeID' => $userID,
                'approve.approvedYN' => $status,
            );

            $this->datatables->select('payrollMasterID, t1.documentCode AS documentCode, payrollYear, payrollMonth, narration, approve.approvedYN as approvedYN,
        documentApprovedID, approvalLevelID', true)
                ->from('srp_erp_non_payrollmaster AS t1')
                ->join('srp_erp_documentapproved AS approve', 'approve.documentSystemCode = t1.payrollMasterID AND approve.approvalLevelID = t1.currentLevelNo')
                ->join('srp_erp_approvalusers AS ap', 'ap.levelNo = t1.currentLevelNo')
                ->where($where)
                ->where('ap.companyID', current_companyID())
                ->where('t1.companyID', current_companyID())
                ->add_column('payrollMonth', '$1', 'payrollMonthInName(payrollYear, payrollMonth)')
                ->add_column('level', "<center>Level $1</center>", 'approvalLevelID')
                ->add_column('documentCode_str', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'code\')')
                ->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SPN", payrollMasterID)')
                ->add_column('edit', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'edit\')');
            echo $this->datatables->generate('json', 'ISO-8859-1');
        }else
        {
            $where = array(
                'approve.documentID' => 'SPN',
                'approve.approvedEmpID' => $userID,

            );

            $this->datatables->select('payrollMasterID, t1.documentCode AS documentCode, payrollYear, payrollMonth, narration, approve.approvedYN as approvedYN,
        documentApprovedID, approvalLevelID', true)
                ->from('srp_erp_non_payrollmaster AS t1')
                ->join('srp_erp_documentapproved AS approve', 'approve.documentSystemCode = t1.payrollMasterID')
                ->where($where)
                ->where('t1.companyID', current_companyID())
                ->group_by('t1.payrollMasterID')
                ->group_by('approve.approvalLevelID')
                ->add_column('payrollMonth', '$1', 'payrollMonthInName(payrollYear, payrollMonth)')
                ->add_column('level', "<center>Level $1</center>", 'approvalLevelID')
                ->add_column('documentCode_str', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'code\')')
                ->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SPN", payrollMasterID)')
                ->add_column('edit', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'edit\')');
            echo $this->datatables->generate('json', 'ISO-8859-1');
        }

    }

    public function getPayrollDetails()
    {
        $payrollID = $this->input->post('payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');

        echo json_encode($this->Template_paySheet_model->getPayrollDetails($payrollID, $isNonPayroll));
    }

    public function getPayrollApproveLevel()
    {
        $payrollID = $this->input->post('payrollID');
        echo json_encode($this->Template_paySheet_model->getPayrollApproveLevel($payrollID));
    }

    public function update_PaySheet()
    {
        $this->form_validation->set_rules('hidden_payrollID', 'Payroll ID', 'trim|required');
        $this->form_validation->set_rules('payNarration', 'Narration', 'trim|required');
        $this->form_validation->set_rules('templateId', 'Template', 'trim|required');
        $this->form_validation->set_rules('processingDate', 'Processing Date', 'trim|required|date');
        $this->form_validation->set_rules('visibleDate', 'Payslip Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $payrollID = trim($this->input->post('hidden_payrollID') ?? '');
            $isConfirm = $this->input->post('isConfirm');
            $isNonPayroll = $this->input->post('isNonPayroll');
            $visibleDate = $this->input->post('visibleDate');
            $date_format_policy = date_format_policy();
            $visibleDate = input_format_date($visibleDate, $date_format_policy);

            $payrollDet = $this->Template_paySheet_model->getPayrollDetails($payrollID, $isNonPayroll);

            $payrollMonth = date('Y - F', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));

            $payrollFirstDate = date('Y-m-d', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));


            if ($payrollFirstDate > $visibleDate) {
                die(json_encode(['e', 'Payslip date can not be leaser than ' . convert_date_format($payrollFirstDate)]));
            }

            if ($payrollDet['confirmedYN'] != 1) {

                $isFinanceYearMatch = array();
                if ($isConfirm == 1) {
                    $year = $payrollDet['payrollYear'];
                    $month = $payrollDet['payrollMonth'];
                    $isFinanceYearMatch = $this->Template_paySheet_model->check_financeYear($year, $month);

                    if ($isFinanceYearMatch[0] == 's') {
                        echo json_encode($this->Template_paySheet_model->update_PaySheet($payrollDet, $isFinanceYearMatch, $isNonPayroll));
                    } else {
                        echo json_encode($isFinanceYearMatch);
                    }
                } else {
                    $isFinanceYearMatch[0] = null;
                    $isFinanceYearMatch[1] = null;
                    $isFinanceYearMatch[2] = null;
                    echo json_encode($this->Template_paySheet_model->update_PaySheet($payrollDet, $isFinanceYearMatch, $isNonPayroll));
                }


            } else {
                echo json_encode(array('e', $payrollMonth . ' payroll is already confirmed you can not update this'));
            }
        }
    }

    public function referBackPayroll()
    {
        $this->form_validation->set_rules('referID', 'Payroll ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $payrollID = $this->input->post('referID');
            $isNonPayroll = $this->input->post('isNonPayroll');
            $payrollDet = $this->Template_paySheet_model->getPayrollDetails($payrollID, $isNonPayroll);
            $payrollMonth = date('Y - F', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));

            if ($payrollDet['approvedYN'] == 1) {
                echo json_encode(array('e', '[ ' . $payrollMonth . ' ] is already approved, you can not refer back this'));
            } else {

                $this->load->library('approvals');
                $docCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
                $status = $this->approvals->approve_delete($payrollID, $docCode);
                if ($status == 1) {
                    echo json_encode(array('s', $payrollMonth . ' Referred Back Successfully.', $status));
                } else {
                    echo json_encode(array('e', $payrollMonth . ' Error in refer back.', $status));
                }
            }
        }
    }

    public function payroll_delete()
    {
        echo json_encode($this->Template_paySheet_model->payroll_delete());
    }

    public function payroll_refresh()
    {
        echo json_encode($this->Template_paySheet_model->payroll_refresh());
    }

    public function payroll_bankTransfer()
    {
        $this->form_validation->set_rules('payrollID', 'Payroll ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $payrollMasterID = $this->input->post('payrollID');
            $isNonPayroll = $this->input->post('isNonPayroll');
            $getPayrollDetails = $this->Template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);


            if ($getPayrollDetails['confirmedYN'] != 1) {
                echo json_encode(array('e', 'This Payroll is not confirmed yet.'));
            } else if ($getPayrollDetails['isBankTransferProcessed'] == 1) {
                echo json_encode(array('e', 'Bank Transfer process already done.<P> Please refresh the page and load again '));
            } else {
                $isSuccess = $this->Template_paySheet_model->payroll_bankTransfer($payrollMasterID, $isNonPayroll);
                if ($isSuccess[0] == 's') {
                    echo json_encode(array('s', 'Processing'));
                    /*$data['bankTransferDet'] = $this->template_paySheet_model->payroll_bankTransferData($payrollMasterID);
                    $this->load->view('system\hrm\pay_sheetSalaryBankTransfer', $data);*/
                } else {
                    echo json_encode($isSuccess);
                }
            }

        }
    }

    public function load_bankTransferPage()
    {
        $payrollMasterID = $this->input->post('payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');

        $data['bankTransferDet'] = $this->Template_paySheet_model->payroll_bankTransferPendingData($payrollMasterID, $isNonPayroll);
        $data['currencySum'] = $this->Template_paySheet_model->payroll_bankTransferPendingData_currencyWiseSum($payrollMasterID, $isNonPayroll);
        $data['payrollID'] = $payrollMasterID;
        $data['isPending'] = (!empty($data['bankTransferDet'])) ? 'Y' : 'N';

        $this->load->view('system/hrm/pay_sheetSalaryBankTransfer', $data);
    }

    public function load_empWithoutBankPage()
    {
        $payrollMasterID = $this->input->post('payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $data['empWithoutBank'] = $this->Template_paySheet_model->payroll_empWithoutBank($payrollMasterID, $isNonPayroll, 0);
        $data['empWithoutBank_paid'] = $this->Template_paySheet_model->payroll_empWithoutBank($payrollMasterID, $isNonPayroll, 1);
        $data['payrollID'] = $payrollMasterID;
        $data['group_by_currency'] =   array_group_by($data['empWithoutBank_paid'], 'transactionCurrency');


        $data['isPending'] = (count($data['empWithoutBank']) == 0) ? 'N' : 'Y';

        /*echo '<pre>'; print_r($data['empWithoutBank']); echo '</pre>'; die();*/

        $this->load->view('system/hrm/pay_sheetSalaryEmpWithoutBank', $data);
    }

    public function new_bankTransfer()
    {
        $this->form_validation->set_rules('bnkPayrollID', 'Payroll ID', 'trim|required|numeric');
        $this->form_validation->set_rules('accountID', 'Bank', 'trim|required|numeric');
        $this->form_validation->set_rules('transDate', 'Transfer Date', 'trim|required|date');
        $this->form_validation->set_rules('transCheck[]', 'Employee', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Template_paySheet_model->new_bankTransfer());
        }
    }

    public function fetch_processedBankTransfer()
    {
        $payrollID = $this->input->get('id');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $tableName = ($isNonPayroll != 'Y') ? 'srp_erp_pay_banktransfermaster' : 'srp_erp_pay_non_banktransfermaster';
        $bankTransferType = getPolicyValues('PBT', 'All');

        $this->datatables->select('bankTransferID, payrollMasterID, documentCode, bankName, branchName, swiftCode, accountNo, confirmedYN,notificationYN', false)
            ->from($tableName)
            ->add_column('amountDetails', '$1', 'processed_bankTransferData_currencyWiseSum(payrollMasterID, bankTransferID,' . $isNonPayroll . ')')
            ->add_column('action', '$1', 'actionBankProcess(bankTransferID, confirmedYN, documentCode,' . $isNonPayroll . ', \''.$bankTransferType.'\',notificationYN)')
            ->where('payrollMasterID', $payrollID)
            ->where('companyID', current_companyID());
        echo $this->datatables->generate();
    }

    public function pay_sheetBankTransferDet_load()
    {
        $bankTransID = $this->input->post('bankTransID');
        $payrollMasterID = $this->input->post('payrollMasterID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $payrollDet = $this->Template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);
        $bTransOtherDet = $this->Template_paySheet_model->getTotalBankTransferAmount($bankTransID, $isNonPayroll);

        $data['bTransOtherDet'] = $bTransOtherDet['lo_currency'] . ' ' . number_format($bTransOtherDet['lo_amount'], $bTransOtherDet['lo_dPlace']);
        $data['masterData'] = $this->Template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);
        $data['payDate'] = date('Y - F', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));
        $data['bankTransferDet'] = $this->Template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);
        $data['currencySum'] = processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID, $isNonPayroll, 1);

        $data['bankTransID'] = $bankTransID;

        $this->load->view('system/hrm/pay_sheetBankTransferDet_load', $data);
    }

    public function pay_sheetBankTransferDet_delete()
    {
        echo json_encode($this->Template_paySheet_model->pay_sheetBankTransferDet_delete());
    }

    public function confirm_bankTransfer()
    {
        echo json_encode($this->Template_paySheet_model->confirm_bankTransfer());
    }

    public function bankTransfer_print()
    {
        $bankTransID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $data['masterData'] = $this->Template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);
        $payrollMasterID = $data['masterData']['payrollMasterID'];
        $data['bankTransferDet'] = $this->Template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);
        $data['bankTransID'] = $bankTransID;
        $data['currencySum'] = processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID, $isNonPayroll, 1);

        /*echo $this->load->view('system\hrm\print\bankTransferPrint', $data,true); die();*/
        $html = $this->load->view('system/hrm/print/bankTransferPrint', $data, true);
        $this->load->library('pdf');
        //$this->pdf->printed($html, 'A4', 1);
        //Print Footer Space Increased (SME-2563) - Margin bottom Set To 55
        $this->pdf->printed_bank_letter($html, 'A4', 1);

    }

    public function bankTransfer_excel_tab()
    {
        $this->load->library('excel');
        $bankTransID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);

        $masterData = $this->Template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);
        $payrollMasterID = $masterData['payrollMasterID'];
        $bankTransferDet = $this->Template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);
        $currencySum = processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID, $isNonPayroll, 1);

        $wizard = new PHPExcel_Helper_HTML;

        $lastBank = null;
        $lastCurrency = null;
        $lastGroup = null;

        $x = 0;
        $temArray = array_group_by($bankTransferDet, 'bankName', 'transactionCurrency');
        foreach ($temArray as $key => $transfers) {

            $this->excel->createSheet();
            $this->excel->setActiveSheetIndex($x);
            $tabName = substr($key, 0, 30);
            $this->excel->getActiveSheet()->setTitle($tabName);

            $width = 5;
            foreach (range('A', 'G') as $columnID) {
                $this->excel->getActiveSheet()->getStyle($columnID . '2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
                $this->excel->getActiveSheet()->getStyle($columnID . '2')->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
                $this->excel->getActiveSheet()->getColumnDimension($columnID)->setWidth($width);
            }

            $html = '<p style="margin-left: 2%;">' . $key . '</p>';
            $richText = $wizard->toRichTextObject($html);
            $this->excel->getActiveSheet()->setCellValue('A1', $richText);
            $this->excel->getActiveSheet()->setCellValue('A2', '#');
            $this->excel->getActiveSheet()->setCellValue('B2', 'EMP ID');
            $this->excel->getActiveSheet()->setCellValue('C2', 'Name');
            $this->excel->getActiveSheet()->setCellValue('D2', 'Swift Code');
            $this->excel->getActiveSheet()->setCellValue('E2', 'Account No');
            $this->excel->getActiveSheet()->setCellValue('F2', 'Currency');
            $this->excel->getActiveSheet()->setCellValue('G2', 'Amount');

            $z = 3;
            foreach ($transfers as $key => $transfer) {
                if ($z > 3) {
                    $this->excel->getActiveSheet()->setCellValue('A' . ($z + 1), $key);
                    $this->excel->getActiveSheet()->mergeCells('A' . ($z + 1) . ':G' . ($z + 1));
                } else {
                    $this->excel->getActiveSheet()->setCellValue('A' . $z, $key);
                    $this->excel->getActiveSheet()->mergeCells('A' . $z . ':G' . $z);
                }
                $y = 0;
                if ($z > 3) {
                    $y = $z + 2;
                } else {
                    $y = $z + 1;
                }
                $tot = 0;
                $i = 1;
                $decimal = "";
                foreach ($transfer as $data) {
                    $trCurrency = trim($data['transactionCurrency'] ?? '');
                    $this->excel->getActiveSheet()->setCellValue('A' . $y, $i++);
                    $this->excel->getActiveSheet()->setCellValue('B' . $y, $data['ECode']);
                    $this->excel->getActiveSheet()->setCellValue('C' . $y, $data['acc_holderName']);
                    $this->excel->getActiveSheet()->setCellValue('D' . $y, $data['swiftCode']);
                    $this->excel->getActiveSheet()->setCellValue('E' . $y, $data['accountNo']);
                    $this->excel->getActiveSheet()->setCellValue('F' . $y, $trCurrency);
                    $this->excel->getActiveSheet()->setCellValue('G' . $y, round($data['transactionAmount'], $data['transactionCurrencyDecimalPlaces']));

                    $this->excel->getActiveSheet()->getStyle('G' . $y)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    $totThis = number_format($data['transactionAmount'], $data['transactionCurrencyDecimalPlaces'], '.', '');
                    $decimal = $data['transactionCurrencyDecimalPlaces'];
                    $tot += $totThis;

                    $y++;
                    $z = $y;
                }

                $this->excel->getActiveSheet()->setCellValue('A' . ($z), 'Total');
                $this->excel->getActiveSheet()->setCellValue('G' . ($z), round($tot, $decimal));
                $this->excel->getActiveSheet()->mergeCells('A' . ($z) . ':F' . ($z));
                $this->excel->getActiveSheet()->getStyle('G' . $z)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
                $this->excel->getActiveSheet()->getStyle('A' . ($z))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->getStyle('G' . ($z))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }
            $x++;
        }

        $this->excel->removeSheetByIndex($x);

        $filename = 'BankTransferTab.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    public function bankTransfer_excel_single()
    {
        $this->load->library('excel');
        $bankTransID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $masterData = $this->Template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);
        $payrollMasterID = $masterData['payrollMasterID'];
        $bankTransferDet = $this->Template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);
        $currencySum = processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID, $isNonPayroll, 1);

        $wizard = new PHPExcel_Helper_HTML;
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle("Bank Transfer");
        $lastBank = null;
        $lastCurrency = null;
        $lastGroup = null;

        $temArray = array_group_by($bankTransferDet, 'bankName', 'transactionCurrency');
        $x = 2;
        foreach ($temArray as $key => $transfers) {
            foreach (range('A', 'G') as $columnID) {
                $this->excel->getActiveSheet()->getStyle($columnID . $x)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
                $this->excel->getActiveSheet()->getStyle($columnID . $x)->getFont()->setBold(true);
            }

            $this->excel->getActiveSheet()->setCellValue('A' . ($x - 1), $key);
            $this->excel->getActiveSheet()->setCellValue('A' . $x, '#');
            $this->excel->getActiveSheet()->setCellValue('B' . $x, 'EMP ID');
            $this->excel->getActiveSheet()->setCellValue('C' . $x, 'Name');
            $this->excel->getActiveSheet()->setCellValue('D' . $x, 'Swift Code');
            $this->excel->getActiveSheet()->setCellValue('E' . $x, 'Account No');
            $this->excel->getActiveSheet()->setCellValue('F' . $x, 'Currency');
            $this->excel->getActiveSheet()->setCellValue('G' . $x, 'Amount');

            $z = $x + 1;
            $w = 0;
            $multiple = 0;
            foreach ($transfers as $key => $transfer) {
                if (count($transfers) > 1) {
                    if ($w > 0) {
                        $this->excel->getActiveSheet()->setCellValue('A' . ($z + 1), $key);
                        $this->excel->getActiveSheet()->mergeCells('A' . ($z + 1) . ':G' . ($z + 1));
                    } else {
                        $this->excel->getActiveSheet()->setCellValue('A' . $z, $key);
                        $this->excel->getActiveSheet()->mergeCells('A' . $z . ':G' . $z);
                    }

                } else {
                    $this->excel->getActiveSheet()->setCellValue('A' . $z, $key);
                    $this->excel->getActiveSheet()->mergeCells('A' . $z . ':G' . $z);
                }
                $y = 0;
                if (count($transfers) > 1) {
                    if ($w > 0) {
                        $y = $z + 2;
                    } else {
                        $y = $z + 1;
                    }
                } else {
                    $y = $z + 1;
                }
                $tot = 0;
                $i = 1;
                $decimal = "";
                foreach ($transfer as $data) {
                    $trCurrency = trim($data['transactionCurrency'] ?? '');
                    $this->excel->getActiveSheet()->setCellValue('A' . $y, $i++);
                    $this->excel->getActiveSheet()->setCellValue('B' . $y, $data['ECode']);
                    $this->excel->getActiveSheet()->setCellValue('C' . $y, $data['acc_holderName']);
                    $this->excel->getActiveSheet()->setCellValue('D' . $y, $data['swiftCode']);
                    $this->excel->getActiveSheet()->setCellValue('E' . $y, $data['accountNo']);
                    $this->excel->getActiveSheet()->setCellValue('F' . $y, $trCurrency);
                    $this->excel->getActiveSheet()->setCellValue('G' . $y, round($data['transactionAmount'], $data['transactionCurrencyDecimalPlaces']));

                    $this->excel->getActiveSheet()->getStyle('G' . $y)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    $totThis = round($data['transactionAmount'], $data['transactionCurrencyDecimalPlaces']);
                    $decimal = $data['transactionCurrencyDecimalPlaces'];
                    $tot += $totThis;

                    $y++;
                    $z = $y;
                }

                $this->excel->getActiveSheet()->setCellValue('A' . ($z), 'Total');
                $this->excel->getActiveSheet()->setCellValue('G' . ($z), round($tot, $decimal));
                $this->excel->getActiveSheet()->mergeCells('A' . ($z) . ':F' . ($z));
                $this->excel->getActiveSheet()->getStyle('G' . $z)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
                $this->excel->getActiveSheet()->getStyle('A' . ($z))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->getStyle('G' . ($z))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $x = $z + 3;
                $w++;
            }
        }

        $filename = 'BankTransferSingle.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    public function bankTransferCoverLetter_print()
    {

        $bankTransID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $masterData = $this->Template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);
        $data['masterData'] = $masterData;

        $payrollMasterID = $masterData['payrollMasterID'];
        $payrollDet = $this->Template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);
        $bTransOtherDet = $this->Template_paySheet_model->getTotalBankTransferAmount($bankTransID, $isNonPayroll);

        $currencySum = processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID, $isNonPayroll, 1);
        $currencySumStr = '';
        foreach ($currencySum as $sumRow) {
            $currencySumStr .= $sumRow['transactionCurrency'] . ' ' . $sumRow['trAmount'] . '</br>';
        }
        $data['bTransOtherDet'] = $currencySumStr;


        $data['payDate'] = date('M, Y', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));
        $data['bankTransferDet'] = $this->Template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);


        //echo '<pre>'; print_r($data['bTransOtherDet']); echo '</pre>';die();
        //echo $this->load->view('system\hrm\print\bankTransferCoverLetterPrint', $data,true); die();

        $html = $this->load->view('system/hrm/print/bankTransferCoverLetterPrint', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', 1);

    }

    public function bank_slip_excel(){
        $bankTransID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $masterData = $this->Template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);

        $payrollMasterID = $masterData['payrollMasterID'];

        $bankTransferDet = $this->Template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);

        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Contract Expiry Report');

        $payrollDet = $this->Template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);
        $period = date('M, Y', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));
        $tot_records = count($bankTransferDet);

        $ex_data = []; $fileName = 'Bank SLIP - '.$period.'.xlsx';
        $ex_data[0] = [ 'eWindow SLIPS ' ];
        $ex_data[2] = [ 'Total No. of Records',  $tot_records];
        $ex_data[3] = [ 'Total Amount', 0 ];
        $ex_data[4] = [ '' ];

        $ex_data[5] = [
            'Customer Reference Number', 'Bank ID', 'Branch ID', 'Credit Account Number', 'Amount', 'Debit Account Number',
            'Value Date', 'Account Name', 'Description / Narration', 'Transaction Code'

        ];

        $dPlace = 2; $totalAmount = 0;
        $debitAcc = $masterData['accountNo']; $debitDate = date('d/m/Y', strtotime($masterData['transferDate']));
        foreach ($bankTransferDet as $key=>$row){
            $totalAmount += round($row['transactionAmount'], $dPlace);
            $ex_data[] = [
                ($key+1), $row['bankCode'], $row['branchCode'], $row['accountNo'],
                round($row['transactionAmount'], $dPlace),
                $debitAcc, $debitDate, $row['name_in_bank_slip'], 'Payroll', 23
            ];
        }

        $ex_data[3] = [ 'Total Amount', round($totalAmount, $dPlace)];

        $this->excel->getActiveSheet()->fromArray($ex_data, null, 'A1');
        $this->excel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A2:A3')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle('B4:D'.(6+$tot_records))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $this->excel->getActiveSheet()->getStyle('B4:D'.(6+$tot_records))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle('E4:E'.(6+$tot_records))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        $this->excel->getActiveSheet()->getStyle('B3')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->excel->getActiveSheet()->mergeCells('A1:J1');

        $this->excel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle('A5:J5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');

        ob_clean();
        ob_start();
        header('Content-Type: application/vnd.ms-excel;charset=utf-16');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        ob_clean();
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    public function bank_slip_text(){
        $bankTransID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $masterData = $this->Template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);

        $payrollMasterID = $masterData['payrollMasterID'];

        $bankTransferDet = $this->Template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);

        $dPlace = 2;
        $debitAcc = substr($masterData['accountNo'],0,12);
        $debitDate = date('d/m/Y', strtotime($masterData['transferDate']));
        $filename = 'Payroll '.current_companyName(true);
        $str = '';
        foreach ($bankTransferDet as $key=>$row){
            $acc = substr( $row['accountNo'], 0, 12);
            $empName = substr( $row['name_in_bank_slip'], 0, 20);
            $amount = number_format($row['transactionAmount'], $dPlace, '.', '');

            $str .= ($key+1).';'. $row['bankCode'].';'. $row['branchCode'].';'. $acc.';'. $amount.';'.
                    $debitAcc.';'. $debitDate.';'. $empName.';'. 'Payroll'.';'.'23;'. PHP_EOL;

        }

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $filename . ".txt");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $str;
    }

    public function bank_slip_text_slf(){
        $bankTransID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $masterData = $this->Template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);

        $payrollMasterID = $masterData['payrollMasterID'];

        $bankTransferDet = $this->Template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);

        $segemntID = isset($bankTransferDet[0]['segmentID']) ? $bankTransferDet[0]['segmentID'] : '';

        $masterData = $this->Template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll,$segemntID);

        $absentAsNoPay = getPolicyValues('HANOP','All');
        $attendanceCycleDay = getPolicyValues('HACDAY','All');

        if($absentAsNoPay == 1){
            $last_month = date('Y-m-d',strtotime('-1 month',strtotime($payDateMin)));

            if($attendanceCycleDay == 31){
                $attendanceMin = $payDateMin;
            } else {
                $attendanceMin = substr($last_month, 0, -2).($attendanceCycleDay + 1);
            }
            
            $attendanceMax = substr($payDateMin, 0, -2).($attendanceCycleDay);
        }else{
            $attendanceMax = $masterData['payrollLastDate'];
            $attendanceMin = date('Y-m-01',strtotime($attendanceMax));
        }

        echo '<pre>';
        print_r($attendanceMax); exit;
 

        $dPlace = 2;
        $debitAcc = substr($masterData['accountNo'],0,12);
        $debitDate = date('d/m/Y', strtotime($masterData['transferDate']));
        $filename = 'Payroll '.current_companyName(true);
        $str = '';
        foreach ($bankTransferDet as $key=>$row){
            $acc = substr( $row['accountNo'], 0, 12);
            $empName = substr( $row['name_in_bank_slip'], 0, 20);
            $amount = number_format($row['transactionAmount'], $dPlace, '.', '');

            $str .= 'EDR'.','.$row['Nid'].','. $acc.','.$row['ibancode'].','. $amount.';'.
                $debitAcc.';'. $debitDate.';'. $empName.';'. 'Payroll'.';'.'23;'. PHP_EOL;

            $str .= 'SCR'.';'. $row['bankCode'].';'. $row['branchCode'].';'. $acc.';'. $amount.';'.
                    $debitAcc.';'. $debitDate.';'. $empName.';'. 'Payroll'.';'.'23;'. PHP_EOL;

        }
        echo $str; exit;

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $filename . ".txt");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $str;
    }


    public function pay_slip()
    {
        $payrollID = $this->uri->segment(3);
        $empID = $this->uri->segment(4);
        $isNonPayroll = $this->uri->segment(5);

        $data['masterData'] = $this->Template_paySheet_model->getPayrollDetails($payrollID, $isNonPayroll);

        $documentCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
        $code = ($isNonPayroll != 'Y') ? 'PT' : 'NPT';

        $template = getPolicyValues($code, $documentCode); //$this->template_paySheet_model->getDefault_paySlipTemplate($isNonPayroll);

        if ($template == 'Envoy') {
            $data['details'] = $this->Template_paySheet_model->get_empPaySlipDet($empID, $payrollID, $isNonPayroll);
            $pageSize = 'A5';
            if (!empty($data['details'])) {
                $this->load->model('Employee_model');
                $html = $this->load->view('system/hrm/print/pay_slip_print_envoy', $data, true);
            } else {
                $html = 'No data';
            }

        }
        else if ($template == 'Aitken') {
        $data['details'] = $this->Template_paySheet_model->get_empPaySlipDet($empID, $payrollID, $isNonPayroll);
        $pageSize = 'A5';
        if (!empty($data['details'])) {
            $this->load->model('Employee_model');
            $html = $this->load->view('system/hrm/print/pay_slip_print_aitken', $data, true);
        } else {
            $html = 'No data';
        }

    } else if ($template == 0) {
            $data['details'] = $this->Template_paySheet_model->get_empPaySlipDet($empID, $payrollID, $isNonPayroll);
            $pageSize = 'A4';
            if (!empty($data['details'])) {
                $this->load->model('Employee_model');
                $data['leaveDet'] = false; // $this->Employee_model->get_emp_leaveDet_paySheetPrint($empID, $data['masterData']);
                $html = $this->load->view('system/hrm/print/paySlipPrint', $data, true);
            } else {
                $html = 'No data';
            }

        } else {

            $data['details'] = $this->Template_paySheet_model->fetchPaySheetData_employee($payrollID, $empID, $isNonPayroll);
            $pageSize = 'A5';

            if (!empty($data['details'])) {
                $data['header_det'] = $this->Template_paySheet_model->templateDetails($template);
                /*echo '$template:'.$template;
                echo '<pre>'; print_r($data['header_det']); echo '</pre>';die();*/
                $html = $this->load->view('system/hrm/print/paySlipPrint_template', $data, true);
            } else {
                $html = 'No data';
            }

        }
        //echo $html; die();
        $this->load->library('pdf');
        $this->pdf->printed($html, $pageSize, $data['masterData']['approvedYN']);

    }

    public function pay_slip_selected_employee()
    {
        $payrollMonth = trim($this->input->post('payrollMonth') ?? '');
        $segment = $this->input->post('segmentID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $empID = $this->input->post('empID');
        $companyID = current_companyID();

        $pYear = date('Y', strtotime($payrollMonth));
        $pMonth = date('m', strtotime($payrollMonth));

        $filter_payroll = $filter = "";

        if ($segment != '') {
            $segmentID = explode('|', $segment);
            $filter_payroll .= " AND segmentID={$segmentID[0]}";
        }

        $masterTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        $headerTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';


        $payroll_data = $this->db->query("SELECT payrollMaster.* FROM {$headerTB} AS t1
                                           JOIN (
                                              SELECT payrollMasterID, payrollYear, payrollMonth, narration
                                              FROM {$masterTB} WHERE companyID={$companyID}
                                              AND payrollYear={$pYear} AND payrollMonth={$pMonth} AND approvedYN=1
                                           ) AS payrollMaster ON payrollMaster.payrollMasterID = t1.payrollMasterID
                                           WHERE companyID={$companyID} {$filter_payroll} GROUP BY payrollMasterID")->result_array();

        $payrollID_arr = implode(',', array_column($payroll_data, 'payrollMasterID'));

        $documentCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
        $code = ($isNonPayroll != 'Y') ? 'PT' : 'NPT';

        $template = getPolicyValues($code, $documentCode); //$template = $this->template_paySheet_model->getDefault_paySlipTemplate($isNonPayroll);

        $data['payroll_data'] = $payroll_data;

        if ($template == 'Envoy') {
            $data['details'] = $this->get_empPaySlipDetSelectedEmp($payrollID_arr, $isNonPayroll, $empID);
            $data['empIDs'] = $empID;
            $data['leaveDet'] = false;

            $this->load->view('system/hrm/print/pay_slip_print_envoy_selected_employee', $data);
        }else if ($template == 'Aitken') {
            $data['details'] = $this->get_empPaySlipDetSelectedEmp($payrollID_arr, $isNonPayroll, $empID);
            $data['empIDs'] = $empID;
            $data['leaveDet'] = false;

            $this->load->view('system/hrm/print/pay_slip_print_aitken_selected_employee', $data);
        } else if ($template == 0) {
            $data['details'] = $this->get_empPaySlipDetSelectedEmp($payrollID_arr, $isNonPayroll, $empID);
            $data['empIDs'] = $empID;
            $data['leaveDet'] = false;

            $this->load->view('system/hrm/print/pay_slip_selected_employee', $data);
        } else {
            $data['details'] = $this->get_empPaySlipDetSelectedEmp_template($payrollID_arr, $isNonPayroll, $empID);

            if (!empty($data['details'])) {
                $data['header_det'] = $this->Template_paySheet_model->templateDetails($template);
                echo $this->load->view('system/hrm/print/paySlipPrint_template_all', $data, true);
            } else {
                echo 'No data';
            }
        }
    }

    public function get_empPaySlipDetSelectedEmp($payrollID_list, $isNonPayroll, $empID)
    {

        if ($isNonPayroll != 'Y') {
            $headerDetailTableName = 'srp_erp_payrollheaderdetails';
            $detailTableName = 'srp_erp_payrolldetail';
        } else {
            $headerDetailTableName = 'srp_erp_non_payrollheaderdetails';
            $detailTableName = 'srp_erp_non_payrolldetail';
        }

        $empList = implode(', ', $empID);
        $companyID = current_companyID();

        $headerDet = $this->db->query("SELECT Ename2 AS empName, Designation,EmpID, payrollMasterID, secondaryCode,
                                       IF(transactionCurrency = null , transactionCurrency, payCurrency) AS transactionCurrency,
                                       IF(transactionCurrencyDecimalPlaces = null, transactionCurrencyDecimalPlaces,
                                       (SELECT DecimalPlaces FROM srp_erp_currencymaster1 WHERE CurrencyCode = payCurrency )) AS dPlace
                                       FROM {$headerDetailTableName} WHERE payrollMasterID IN ({$payrollID_list}) AND
                                       EmpID IN({$empList}) AND  {$headerDetailTableName}.companyID={$companyID}
                                       GROUP BY {$headerDetailTableName}.EmpID")->result_array();

        //salary Declarations
        $salaryDec_A = $this->db->query("SELECT salaryDescription, detailType, sum(pay.transactionAmount) AS transactionAmount,cat.salaryCategoryID AS salaryCategoryID,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID, fromTB,pay.salCatID
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                        WHERE payrollMasterID IN ({$payrollID_list})
                                        AND (fromTB = 'SD' OR  fromTB = 'VP' OR fromTB = 'BP' OR fromTB = 'OT') AND detailType = 'A' AND transactionAmount != 0 AND pay.companyID={$companyID}
                                        AND pay.EmpID IN({$empList}) GROUP BY pay.salCatID,pay.EmpID ")->result_array();

        $salaryDec_D = $this->db->query("SELECT salaryDescription, detailType, sum(pay.transactionAmount) AS transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                        WHERE payrollMasterID IN ({$payrollID_list})
                                        AND (fromTB = 'SD' OR  fromTB = 'NO-PAY')  AND detailType = 'D'  AND transactionAmount != 0 AND pay.companyID={$companyID}
                                        AND pay.EmpID IN({$empList}) GROUP BY pay.salCatID,pay.EmpID ")->result_array();

        //Monthly Addition
        $monthAdd = $this->db->query("SELECT *, SUM(amnt) AS transactionAmount FROM (
                                        SELECT monthlyDeclaration AS description, detailType, pay.transactionAmount AS amnt, pay.EmpID, 
                                        CONCAT(IFNULL(salCatID,0),'_',monthlyDeclarationID) AS grField, pay.transactionCurrencyDecimalPlaces AS dPlace
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_pay_monthlyadditiondetail AS mAdd ON mAdd.monthlyAdditionDetailID = pay.detailTBID
                                        JOIN srp_erp_pay_monthlydeclarationstypes AS monDec ON monDec.monthlyDeclarationID = mAdd.declarationID
                                        WHERE payrollMasterID IN ({$payrollID_list}) AND pay.companyID={$companyID}
                                        AND fromTB = 'MA' AND pay.EmpID IN({$empList})
                                     ) t1 GROUP BY EmpID, grField")->result_array();



        //Monthly Deduction
        $monthDec = $this->db->query("SELECT *, SUM(amnt) AS transactionAmount FROM (
                                        SELECT monthlyDeclaration AS description, detailType, pay.transactionAmount AS amnt, pay.EmpID, 
                                        CONCAT(IFNULL(salCatID,0),'_',monthlyDeclarationID) AS grField, pay.transactionCurrencyDecimalPlaces AS dPlace                                    
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_pay_monthlydeductiondetail AS mDed ON mDed.monthlyDeductionDetailID = pay.detailTBID
                                        JOIN srp_erp_pay_monthlydeclarationstypes AS monDec ON monDec.monthlyDeclarationID = mDed.declarationID
                                        WHERE payrollMasterID IN ({$payrollID_list}) AND pay.companyID={$companyID}
                                        AND fromTB = 'MD' AND pay.EmpID IN({$empList})
                                      ) t1 GROUP BY EmpID, grField")->result_array();

        //SSO Payee
        $sso_payee = $this->db->query("SELECT grMaster.description, detailType, pay.transactionAmount, pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_paygroupmaster AS grMaster ON grMaster.payGroupID = pay.detailTBID
                                        LEFT JOIN (
                                            SELECT * FROM srp_erp_socialinsurancemaster WHERE companyID={$companyID}
                                        ) AS ssoMaster ON ssoMaster.socialInsuranceID = grMaster.socialInsuranceID
                                        WHERE payrollMasterID IN ({$payrollID_list}) AND (employerContribution = 0  OR employerContribution is null)
                                        AND fromTB = 'PAY_GROUP' AND pay.EmpID IN({$empList}) GROUP BY detailTBID,pay.EmpID ")->result_array();


        $employerContributions = [];
        $OT_data = [];
        $isNonPayroll = $this->input->post('isNonPayroll');
        /*Get only for Envoy template (Not for Non payroll) */
        if($isNonPayroll != 'Y'){
            $template = getPolicyValues('PT', 'SP');
            if ($template == 'Envoy') {
                $tempData = $this->db->query("SELECT template_tb.id, transactionAmount, pay.empID FROM srp_erp_sso_reporttemplatefields AS template_tb
                                        LEFT JOIN srp_erp_sso_reporttemplatedetails AS setup_tb ON setup_tb.reportID = template_tb.id  
                                        AND setup_tb.companyID={$companyID}
                                        LEFT JOIN srp_erp_payrolldetail AS pay ON setup_tb.reportValue=pay.detailTBID 
                                        AND pay.payrollMasterID IN ({$payrollID_list}) AND pay.empID IN ({$empList}) 
                                        WHERE template_tb.id IN (6, 7, 18) ")->result_array();

                $tempData = array_group_by($tempData, 'empID');
                foreach ($tempData as $tKey=>$tRow){
                    foreach($tRow as $fnRow){
                        $employerContributions[$tKey][$fnRow['id']] = $fnRow['transactionAmount'];
                    }
                }


                /**** Get Over time hours and minutes */
                $OT_temp = $this->db->query("SELECT payTb.empID, CONCAT(FLOOR(hourorDays/60),'h ',MOD(hourorDays,60),'m') AS otHour, salCatID
                                FROM srp_erp_payrolldetail payTb
                                JOIN (
                                    SELECT ID AS attRVID, hourorDays, otDet.empID 
                                    FROM srp_erp_pay_empattendancereview attTB
                                    JOIN srp_erp_generalotdetail otDet ON attTB.generalOTID = otDet.generalOTMasterID
	                                AND attTB.empID = otDet.empID AND otDet.salaryCategoryID = attTB.salaryCategoryID
                                    WHERE paymentOT != 0 AND attTB.companyID={$companyID} AND attTB.empID IN ({$empList}) AND hourorDays != 0
                                    GROUP BY ID
                                ) AS otTB ON otTB.empID=payTb.empID AND payTb.detailTBID=otTB.attRVID
                                WHERE payrollMasterID IN ({$payrollID_list}) AND fromTB='OT' GROUP BY
	                                                            payTb.salCatID,otTB.empID")->result_array();

                $OT_data = array_group_by($OT_temp, 'empID');
                /*if(!empty($OT_temp)){
                    foreach ($OT_temp as $oRow){
                        $OT_data[$oRow['empID']] = $oRow['otHour'];
                    }
                }*/

            }

            if ($template == 'Aitken') {
                $tempData = $this->db->query("SELECT template_tb.id, transactionAmount, pay.empID FROM srp_erp_sso_reporttemplatefields AS template_tb
                                        LEFT JOIN srp_erp_sso_reporttemplatedetails AS setup_tb ON setup_tb.reportID = template_tb.id
                                        AND setup_tb.companyID={$companyID}
                                        LEFT JOIN srp_erp_payrolldetail AS pay ON setup_tb.reportValue=pay.detailTBID
                                        AND pay.payrollMasterID IN ({$payrollID_list}) AND pay.empID IN ({$empList})
                                        WHERE template_tb.id IN (6, 7, 18) ")->result_array();

                $tempData = array_group_by($tempData, 'empID');
                foreach ($tempData as $tKey=>$tRow){
                    foreach($tRow as $fnRow){
                        $employerContributions[$tKey][$fnRow['id']] = $fnRow['transactionAmount'];
                    }
                }


                /**** Get Over time hours and minutes */
                $OT_temp = $this->db->query("SELECT payTb.empID, CONCAT(FLOOR(hourorDays/60),'h ',MOD(hourorDays,60),'m') AS otHour, salCatID
                                FROM srp_erp_payrolldetail payTb
                                JOIN (
                                    SELECT ID AS attRVID, hourorDays, otDet.empID
                                    FROM srp_erp_pay_empattendancereview attTB
                                    JOIN srp_erp_generalotdetail otDet ON attTB.generalOTID = otDet.generalOTMasterID
	                                AND attTB.empID = otDet.empID AND otDet.salaryCategoryID = attTB.salaryCategoryID
                                    WHERE paymentOT != 0 AND attTB.companyID={$companyID} AND attTB.empID IN ({$empList}) AND hourorDays != 0
                                    GROUP BY ID
                                ) AS otTB ON otTB.empID=payTb.empID AND payTb.detailTBID=otTB.attRVID
                                WHERE payrollMasterID IN ({$payrollID_list}) AND fromTB='OT' GROUP BY
	                                                            payTb.salCatID,otTB.empID")->result_array();

                $OT_data = array_group_by($OT_temp, 'empID');
                /*if(!empty($OT_temp)){
                    foreach ($OT_temp as $oRow){
                        $OT_data[$oRow['empID']] = $oRow['otHour'];
                    }
                }*/

            }
        }

        //Loan Deduction
        $loanDed = $this->db->query("SELECT installmentNo, loan.loanCode, loanDescription, detailType, pay.transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_pay_emploan_schedule AS loan_sch ON loan_sch.ID = pay.detailTBID
                                        JOIN srp_erp_pay_emploan AS loan ON loan.ID = loan_sch.loanID
                                        WHERE payrollMasterID IN ({$payrollID_list}) AND pay.companyID={$companyID}
                                        AND fromTB = 'LO' AND pay.EmpID IN({$empList}) GROUP BY pay.EmpID")->result_array();


        $loanIntPending = $this->db->query("SELECT loan.loanCode, loanDescription, count(l_sched.ID) AS pending_Int,
                                            sum(l_sched.transactionAmount) as trAmount,loan.empID
                                            FROM srp_erp_pay_emploan AS loan
                                            JOIN srp_erp_pay_emploan_schedule AS l_sched ON loan.ID = l_sched.loanID
                                            WHERE approvedYN = 1 AND isClosed != 1 AND isSetteled = 0 AND skipedInstallmentID = 0
                                            AND loan.EmpID IN({$empList}) GROUP BY loan.loanCode,loan.empID")->result_array();


        //Bank transfer
        $bankTransferDed = $this->db->query("SELECT bankName, accountNo, transactionCurrency, transactionAmount, salaryTransferPer,
                                             transactionCurrencyDecimalPlaces AS dPlace, swiftCode, empID
                                             FROM srp_erp_pay_banktransfer
                                             WHERE payrollMasterID IN ({$payrollID_list}) AND companyID={$companyID} AND empID IN({$empList})
                                             GROUP BY empID")->result_array();

        //Salary Paid by cash / cheque
        $salaryNonBankTransfer = $this->db->query("SELECT * FROM srp_erp_payroll_salarypayment_without_bank WHERE payrollMasterID IN ({$payrollID_list})
                                                   AND empID IN({$empList}) AND companyID={$companyID} GROUP BY empID")->result_array();;

        return array(
            'headerDet' => array_group_by($headerDet, 'EmpID'),
            'salaryDec_A' => array_group_by($salaryDec_A, 'EmpID'),
            'salaryDec_D' => array_group_by($salaryDec_D, 'EmpID'),
            'monthAdd' => array_group_by($monthAdd, 'EmpID'),
            'monthDec' => array_group_by($monthDec, 'EmpID'),
            'sso_payee' => array_group_by($sso_payee, 'EmpID'),
            'employerContributions' => $employerContributions,
            'loanDed' => array_group_by($loanDed, 'EmpID'),
            'loanIntPending' => array_group_by($loanIntPending, 'empID'),
            'bankTransferDed' => array_group_by($bankTransferDed, 'empID'),
            'salaryNonBankTransfer' => array_group_by($salaryNonBankTransfer, 'empID'),
            'OT_data' => $OT_data
        );

    }

    public function get_empPaySlipDetSelectedEmp_template($payrollID_list, $isNonPayroll, $empID)
    {

        if ($isNonPayroll != 'Y') {
            $headerDetailTableName = 'srp_erp_payrollheaderdetails';
            $detailTableName = 'srp_erp_payrolldetail';
            $payGroupDetailTableName = 'srp_erp_payrolldetailpaygroup';
        } else {
            $headerDetailTableName = 'srp_erp_non_payrollheaderdetails';
            $detailTableName = 'srp_erp_non_payrolldetail';
            $payGroupDetailTableName = 'srp_erp_non_payrolldetailpaygroup';
        }

        $empList = implode(', ', $empID);
        $companyID = current_companyID();

        $info = $this->db->query("SELECT empTB.*, empTB.transactionAmount AS netTrans , fromTB, calculationTB, detailType, salCatID, pay.detailTBID,
                                  sum(pay.transactionAmount) AS transactionAmount, pay.transactionCurrencyDecimalPlaces, seg.segmentCode AS emp_segmentCode
                                  FROM {$headerDetailTableName} AS empTB
                                  JOIN {$detailTableName} AS pay ON empTB.EmpID=pay.empID
                                  LEFT JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                  LEFT JOIN srp_erp_segment AS seg ON seg.segmentID = empTB.segmentID
                                  WHERE pay.payrollMasterID IN ({$payrollID_list}) AND empTB.payrollMasterID IN ({$payrollID_list}) AND pay.companyID = {$companyID}
                                  AND fromTB != 'PAY_GROUP' AND pay.empID IN ($empList)
                                  GROUP BY pay.empID, pay.salCatID, pay.calculationTB
                                  UNION
                                        SELECT empTB.*, empTB.transactionAmount AS netTrans, fromTB, calculationTB, detailType, salCatID, pay.detailTBID,
                                        pay.transactionAmount AS transactionAmount, pay.transactionCurrencyDecimalPlaces, seg.segmentCode AS emp_segmentCode
                                        FROM {$headerDetailTableName} AS empTB
                                        JOIN {$detailTableName} AS pay ON empTB.EmpID=pay.empID
                                        LEFT JOIN srp_erp_segment AS seg ON seg.segmentID = empTB.segmentID
                                        WHERE fromTB = 'PAY_GROUP' AND pay.payrollMasterID IN ({$payrollID_list}) AND empTB.payrollMasterID IN ({$payrollID_list})
                                        AND pay.empID IN ($empList)
                                  UNION
                                        SELECT pay2.*, pay2.transactionAmount AS netTrans, fromTB, fromTB AS calculationTB, detailType, '' AS salCatID,
                                        detailTBID, payGroup.transactionAmount, payGroup.transactionCurrencyDecimalPlaces, '' AS emp_segmentCode
                                        FROM {$payGroupDetailTableName}  AS payGroup
                                        JOIN {$headerDetailTableName} AS pay2 ON payGroup.empID=pay2.empID AND pay2.companyID={$companyID} AND
                                        pay2.payrollMasterID IN ({$payrollID_list})
                                        WHERE payGroup.companyID={$companyID} AND payGroup.payrollMasterID IN ({$payrollID_list})
                                        AND payGroup.empID IN ($empList)
                                  ORDER BY empID DESC")->result_array();

        if (isset($info)) {

            $dataArray = array();
            $i = 0;
            $j = 0;
            $ECode = '';

            foreach ($info as $row) {
                $tmpECode = $row['ECode'];

                if ($ECode != $tmpECode) {
                    $j = 0;
                    $i++;

                    switch ($row['Gender']) {
                        case '1':
                            $gender = 'Male';
                            break;

                        case '2':
                            $gender = 'Female';
                            break;

                        default :
                            $gender = '-';
                    }

                    //$dataArray[$i]['empDet'] = $row;
                    $dataArray[$i]['empDet'] = array(
                        'masterID' => $row['payrollMasterID'],
                        'E_ID' => $row['EmpID'],
                        'ECode' => $row['ECode'],
                        'Ename1' => $row['Ename1'],
                        'Ename2' => $row['Ename2'],
                        'Ename3' => $row['Ename3'],
                        'Ename4' => $row['Ename4'],
                        'EmpShortCode' => $row['EmpShortCode'],
                        'Designation' => $row['Designation'],
                        'Gender' => $gender,
                        'EcTel' => $row['Tel'],
                        'EcMobile' => $row['Mobile'],
                        'EDOJ' => $row['DOJ'],
                        'payCurrency' => $row['payCurrency'],
                        'nationality' => $row['nationality'],
                        'dPlaces' => $row['transactionCurrencyDecimalPlaces'],
                        'segmentID' => $row['emp_segmentCode']
                    );

                    $ECode = $row['ECode'];
                }


                if ($row['calculationTB'] == 'SD') {
                    $cat = $row['salCatID'];
                } else if ($row['fromTB'] == 'PAY_GROUP') {
                    $cat = 'G_' . $row['detailTBID'];
                } else {
                    $cat = $row['fromTB'];
                }

                $dataArray[$i]['empSalDec'][$j] = array(
                    'catID' => $cat,
                    'catType' => $row['detailType'],
                    'amount' => $row['transactionAmount'],
                );
                $j++;

            }
            return $dataArray;

        } else {
            return 'There is no record.';
        }

    }

    public function paySheet_print()
    {
        $payrollMasterID = $this->uri->segment(3);
        $templateID = $this->uri->segment(4);
        $isNonPayroll = $this->uri->segment(5);

        $data['masterData'] = $this->Template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);

        $lastMonth = array(
            date('Y', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01'))),
            date('m', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01')))
        );

        $data['header_det'] = $this->Template_paySheet_model->templateDetails($templateID);
        $data['currency_groups'] = $this->Template_paySheet_model->currencyWiseSum($payrollMasterID, $isNonPayroll);
        $data['paysheetData'] = $this->Template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, '');
        $data['isFromPrint'] = 'Y';
        $data['isForReverse'] = null;

        $html = $this->load->view('system/hrm/print/paySheet_print_withZero', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4-L', $data['masterData']['confirmedYN']);
    }

    public function paySheetPrint_segmentWise()
    {
        $payrollMasterID = $this->uri->segment(3);
        $templateID = $this->uri->segment(4);
        $isNonPayroll = $this->uri->segment(5);
        $segmentID = $this->input->post('segmentID');
        $hideZeroColumn = $this->input->post('hideZeroColumn');

        $data['masterData'] = $this->Template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll, $segmentID);
        $lastMonth = array(
            date('Y', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01'))),
            date('m', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01')))
        );
        $data['header_det'] = $this->Template_paySheet_model->templateDetails($templateID);
        $data['currency_groups'] = $this->Template_paySheet_model->currencyWiseSum($payrollMasterID, $isNonPayroll, $segmentID);
        $data['isFromPrint'] = 'Y';
        $data['isForReverse'] = null;

        if ($hideZeroColumn == 'Y') {
            $data['paysheetData'] = $this->Template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, $segmentID, $templateID);
            $html = $this->load->view('system/hrm/print/paySheet_print', $data, true);
        } else {
            $data['paysheetData'] = $this->Template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, $segmentID);
            $html = $this->load->view('system/hrm/print/paySheet_print_withZero', $data, true);
        }

        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4-L', $data['masterData']['approvedYN']);
    }

    public function paySheetPrint_segmentWise_csv()
    {
        $payrollMasterID = $this->uri->segment(3);
        $templateID = $this->uri->segment(4);
        $isNonPayroll = $this->uri->segment(5);
        $segmentID = $this->input->post('segmentID');
        $hideZeroColumn = $this->input->post('hideZeroColumn');

        $data['masterData'] = $this->Template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll, $segmentID);
        $lastMonth = array(
            date('Y', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01'))),
            date('m', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01')))
        );
        $data['header_det'] = $this->Template_paySheet_model->templateDetails($templateID);
        $data['currency_groups'] = $this->Template_paySheet_model->currencyWiseSum($payrollMasterID, $isNonPayroll, $segmentID);
        $data['isFromPrint'] = 'Y';
        $data['isForReverse'] = null;

        if ($hideZeroColumn == 'Y') {
            $data['paysheetData'] = $this->Template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, $segmentID, $templateID);
            $html = $this->load->view('system/hrm/print/paySheet_print', $data, true);
        } else {
            $data['paysheetData'] = $this->Template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, $segmentID);
            $html = $this->load->view('system/hrm/print/paySheet_print_edr', $data, true);
        }

        // $csv = $this->htmlTableToCsv($html);

        // // Output the CSV as a file download
        // header('Content-Type: text/csv');
        // header('Content-Disposition: attachment;filename="table.csv"');
        // echo $csv;
        // print_r($html); exit;
        
        // $this->load->library('pdf');
        // $this->pdf->printed($html, 'A4-L', $data['masterData']['approvedYN']);
    }

    function htmlTableToCsv($html) {
        // Load the HTML into DOMDocument
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        
        // Initialize a new CSV output
        $csvOutput = '';
        
        // Get all rows from the table
        $rows = $dom->getElementsByTagName('tr');
        
        foreach ($rows as $row) {
            // Get all cells in the row
            $cells = $row->getElementsByTagName('td');
            if ($cells->length == 0) { // No TD tags means this row may be a TH (header) row
                $cells = $row->getElementsByTagName('th');
            }
            
            $cellData = [];
            foreach ($cells as $cell) {
                // Add the cell data to the row array
                $cellData[] = '"' . trim($cell->textContent) . '"'; // Enclose in quotes and trim whitespace
            }
            
            // Join the cell data with commas and add to CSV output
            $csvOutput .= implode(',', $cellData) . "\n";
        }
        
        return $csvOutput;
    }


    public function save_empNonBankPay()
    {
        $empID = $this->input->post('hidden_empID');
        $payrollMasterID = $this->input->post('hidden_payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $payType = $this->input->post('payType');
        $empPayBank = $this->input->post('empPayBank');
        $chequeNo = $this->input->post('chequeNo');

        $this->form_validation->set_rules('hidden_payrollID', 'Payroll ID', 'trim|required|numeric');
        $this->form_validation->set_rules('hidden_empID', 'Employee ID', 'trim|required|numeric');
        $this->form_validation->set_rules('payType', 'Payment Type', 'trim|required');
        $this->form_validation->set_rules('paymentDate', 'Payment Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $isAlreadyPaid = $this->Template_paySheet_model->get_EmpNonBankTransferDet($empID, $payrollMasterID, $isNonPayroll);

            if ($isAlreadyPaid['isPaid'] != 1) {
                $byChq_errorMsg = '';
                if ($payType == 'By Cheque') {
                    if (trim($empPayBank) == '') {
                        $byChq_errorMsg = '<p>Bank field is required</p>';
                    }
                    if (trim($chequeNo) == '') {
                        $byChq_errorMsg .= '<p>Cheque No field is required</p>';
                    }
                }

                if ($byChq_errorMsg != '') {
                    echo json_encode(array('e', $byChq_errorMsg));
                } else {
                    echo json_encode($this->Template_paySheet_model->save_empNonBankPay());
                }
            } else {
                echo json_encode(array('e', 'Already salary paid for this employee'));
            }

        }
    }

    public function print_empNonBankPay()
    {
        $payrollMasterID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $data['masterData'] = $this->Template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);
        $data['printData'] = $this->Template_paySheet_model->payroll_empWithoutBank($payrollMasterID, $isNonPayroll, 1);

        /*echo '<pre>';print_r( $data['printData']);echo '</pre>';die();*/
        /*echo $this->load->view('system\hrm\print\empNonBankPay', $data,true);die();*/

        $html = $this->load->view('system/hrm/print/empNonBankPayPrint', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', $data['masterData']['confirmedYN']);
    }

    function getNotPayrollProcessedMonths()
    {
        $year = $this->input->post('year');
        $type = (!empty($this->input->post('payrollType'))) ? 'Y' : 'N';
        echo json_encode(payrollCalender($year, 2, $type));
    }

    function paysheetApproval()
    {
        $paysheetID = $this->input->post('hiddenPaysheetID');
        $paysheetCode = $this->input->post('hiddenPaysheetCode');
        $level_id = $this->input->post('level');
        $status = $this->input->post('status');
        $comments = $this->input->post('comments');
        $isNonPayroll = $this->input->post('isNonPayroll');

        $this->form_validation->set_rules('hiddenPaysheetID', 'Paysheet ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_rules('level', 'Level', 'trim|required');
        if ($this->input->post('status') == 2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $isConfirmed = $this->Template_paySheet_model->getPayrollDetails($paysheetID, $isNonPayroll);

            if ($isConfirmed['confirmedYN'] == 1) {
                $this->load->library('approvals');
                $docCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
                $approvals_status = $this->approvals->approve_document($paysheetID, $level_id, $status, $comments, $docCode);

                if ($approvals_status == 2) {
                    //  echo json_encode(array('s', 'Pay sheet [ ' . $paysheetCode . ' ] Approved', $approvals_status));
                    $msg = $this->lang->line('common_paysheet') . ' [ ' . $paysheetCode . ' ] ' . strtolower($this->lang->line('common_approved')) . '.';
                    echo json_encode(array('s', $msg, $approvals_status));
                } else if ($approvals_status == 1) {
                    echo json_encode($this->Template_paySheet_model->double_entries($paysheetID, $isNonPayroll));
                    //echo json_encode(array('s', 'Paysheet [ ' . $paysheetCode . ' ] Approved', $approvals_status));

                } else if ($approvals_status == 3) {
                    $Rejectprocess = $this->lang->line('hrms_payroll_approvals_reject_process_successfully_done');
                    echo json_encode(array('s', '[ ' . $paysheetCode . ' ]' . $Rejectprocess . ' .'));/*Approvals  Reject Process Successfully done*/
                } else if ($approvals_status == 5) {
                    $previouslevel = $this->lang->line('hrms_payroll_previous_level_approval_not_finished');

                    echo json_encode(array('w', '[ ' . $paysheetCode . ' ] ' . $previouslevel . '.'));/*Previous Level Approval Not Finished*/
                } else {
                    $errorinpaysheet = $this->lang->line('hrms_payroll_error_in_paysheet_approvals_of');

                    echo json_encode(array('e', ' [ ' . $paysheetCode . ' ]' . $errorinpaysheet . ' '));/*Error in Paysheet Approvals Of */
                }
            } else {
                $paysheet = $this->lang->line('common_paysheet');
                $notconfirmed = $this->lang->line('common_not_confirmed_yet');
                $refresh = $this->lang->line('common_please_refresh_and_try_again');
                echo json_encode(array('e', '' . $paysheet . ' [ ' . $paysheetCode . ' ] ' . $notconfirmed . '.</br>' . $refresh . '.'));/*Pay sheet*//*not confirmed yet*//*Please refresh and try again*/
            }
        }
    }

    function payrollAccountReview()
    {
        $payrollMasterID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $data['masterData'] = $this->Template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);
        $accReviewData_arr = $this->Template_paySheet_model->payrollAccountReview($payrollMasterID, $isNonPayroll);

        $data['accReviewData_arr'] = $accReviewData_arr[1];

        // print_r($data['accReviewData_arr'] ); exit;

        $html = $this->load->view('system/hrm/print/payroll-account-review', $data, true);

        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', 1);

    }

    function update_template_sortOrder()
    {
        $tempFieldID = $this->input->post('tempFieldID');
        $sortOrder = $this->input->post('sortOrder');

        $update = $this->db->update('srp_erp_pay_templatedetail', array('sortOrder' => $sortOrder), array('tempFieldID' => $tempFieldID));
        if ($update) {
            echo json_encode(array('error' => 0, 'message' => 'success'));
        } else {
            echo json_encode(array('error' => 0, 'message' => 'error'));
        }

    }

    function double_entries()
    {
        /*echo json_encode( $this->template_paySheet_model->double_entries($fb) );*/
    }

    function get_paySlip_report()
    {
        $payrollMonth = trim($this->input->post('payrollMonth') ?? '');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $segment = $this->input->post('segmentID');
        $empID = $this->input->post('empID');
        $filter_payroll = $filter = "";
        $Pleasesselectatleastoneemployeetoproceed = $this->lang->line('common_please_select_at_least_one_employee_to_proceed');
        if ($empID == '') {
            echo '<div class="col-md-12 bg-border" style="">
                   <div class="row">
                        <div class="col-md-12 xxcol-md-offset-2">
                            <div class="alert alert-warning" role="alert">
                                <p>' . $Pleasesselectatleastoneemployeetoproceed . '<!--Please select at least one employee to proceed--></p>
                            </div>
                        </div>
                    </div>
                   </div>';
            die();
        } else {
            $commaList = implode(', ', $empID);
            $filter .= " AND empID IN({$commaList})";
        }


        $companyID = current_companyID();
        $pYear = date('Y', strtotime($payrollMonth));
        $pMonth = date('m', strtotime($payrollMonth));

        if ($segment != '') {
            $segmentID = explode('|', $segment);
            $filter_payroll .= " AND segmentID={$segmentID[0]}";
        }

        $masterTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        $headerTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';
        $detailTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrolldetail' : 'srp_erp_non_payrolldetail';


        $payrollID_arr = $this->db->query("SELECT payrollMasterID FROM {$headerTB} AS t1
                                           JOIN (
                                              SELECT payrollMasterID AS payID FROM {$masterTB} WHERE companyID={$companyID}
                                              AND payrollYear={$pYear} AND payrollMonth={$pMonth} AND approvedYN=1
                                           ) AS payrollMaster ON payrollMaster.payID = t1.payrollMasterID
                                           WHERE companyID={$companyID} {$filter_payroll} GROUP BY payrollMasterID")->result_array();

        if (empty($payrollID_arr)) {
            echo '<div class="col-md-12 bg-border" style="">
                       <div class="row">
                            <div class="col-md-12 xxcol-md-offset-2">
                                <div class="alert alert-warning" role="alert">
                                    <p>Please select at least one employee to proceed</p>
                                </div>
                            </div>
                       </div>
                  </div>';
            die();
        }

        $payrollID_arr = implode(',', array_column($payrollID_arr, 'payrollMasterID'));

        $sql = "SELECT {$masterTB}.payrollMasterID, EIdNo, ECode, EmpDesignationId, Ename1, Ename2, Ename3, Ename4, EmpShortCode,
              srp_employeesdetails.segmentID, transactionCurrency, transactionCurrencyDecimalPlaces, SUM(IF(detailType = 'A', transactionAmount, 0)) AS addition,
              SUM(IF(detailType = 'D' || detailType = 'G', transactionAmount  , 0))  AS deduction , SUM(IF(detailType = 'A', transactionAmount, 0)) +
              SUM(IF(detailType = 'D' || detailType = 'G', transactionAmount, 0))  AS total, detailType, salCatID
              FROM {$masterTB}
              LEFT JOIN {$detailTB} ON {$masterTB}.payrollMasterID = {$detailTB}.payrollMasterID
              LEFT JOIN srp_employeesdetails ON empID = EidNo
              WHERE approvedYN = 1 AND {$masterTB}.payrollMasterID IN ({$payrollID_arr}) {$filter} AND {$masterTB}.companyID = '{$companyID}'
              AND NOT EXISTS (
                  SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                  JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID='{$companyID}'
                  WHERE {$detailTB}.detailTBID = groupMaster.payGroupID AND {$detailTB}.fromTB='PAY_GROUP'
                  AND groupMaster.companyID='{$companyID}' AND SSO_TB.employerContribution > 0
              ) and {$detailTB}.payrollMasterID IN ({$payrollID_arr}) AND
              {$detailTB}.empID=srp_employeesdetails.EIdNo
              GROUP BY EIdNo , transactionCurrency";


        $sql2 = "SELECT transactionCurrency as currency, transactionCurrencyDecimalPlaces,
               SUM(IF(detailType = 'A', transactionAmount, 0)) AS totaladdition,
               SUM(IF(detailType = 'D' || detailType = 'G', transactionAmount  , 0))  AS totaldeduction ,
               SUM(IF(detailType = 'A', transactionAmount, 0)) + SUM(IF(detailType = 'D' || detailType = 'G', transactionAmount, 0))  AS totalamount
               FROM {$masterTB}
               LEFT JOIN {$detailTB} ON {$masterTB}.payrollMasterID = {$detailTB}.payrollMasterID
               LEFT JOIN srp_employeesdetails ON empID = EidNo
               WHERE approvedYN = 1 AND {$masterTB}.payrollMasterID IN ({$payrollID_arr}) {$filter} AND {$masterTB}.companyID = '{$companyID}'
               AND NOT EXISTS (
                   SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                   JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID='{$companyID}'
                   WHERE {$detailTB}.detailTBID = groupMaster.payGroupID AND {$detailTB}.fromTB='PAY_GROUP' AND
                   groupMaster.companyID='{$companyID}' AND SSO_TB.employerContribution > 0
               ) and {$detailTB}.payrollMasterID IN ({$payrollID_arr})
               AND {$detailTB}.empID=srp_employeesdetails.EIdNo
              GROUP BY  transactionCurrency";

        $data['detail'] = $this->db->query($sql)->result_array();
        $data['currency'] = $this->db->query($sql2)->result_array();

        /*  $this->load->view('system\hrm\pay_sheetTemplateDetails_view', $data);*/
        echo $this->load->view('system/hrm/ajax/load-employee-pays-slip', $data, true);
    }

    function dropdown_payslipemployees()
    {

        $this->form_validation->set_rules('payrollMonth', 'Payroll Month', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $html = '<div class="col-md-12 bg-border" style="">
                    <div class="row">
                        <div class="col-md-12 xxcol-md-offset-2">
                            <div class="alert alert-warning" role="alert">
                                ' . validation_errors() . '
                            </div>
                        </div>
                    </div>
                   </div>';

            echo json_encode(['e', $html]);

        } else {
            $payrollMonth = trim($this->input->post('payrollMonth') ?? '');
            $segment = $this->input->post('segmentID');
            $isNonPayroll = $this->input->post('isNonPayroll');
            $companyID = current_companyID();

            $pYear = date('Y', strtotime($payrollMonth));
            $pMonth = date('m', strtotime($payrollMonth));

            if (empty($segment)) {
                $segmentFilter = '';
            } else {
                $seg = explode('|', $segment);
                $segmentFilter = 'AND segmentID=' . $seg[0];
            }

            $masterTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
            $headerTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';

            $str = '';
            $isGroupAccess = getPolicyValues('PAC', 'All');
            $data['isGroupAccess'] = $isGroupAccess;
            if ($isGroupAccess == 1) {
                $currentEmp = current_userID();
                $str = "JOIN (
                            SELECT groupID FROM srp_erp_payrollgroupincharge
                            WHERE companyID={$companyID} AND empID={$currentEmp}
                        ) AS accTb ON accTb.groupID = t1.accessGroupID";
            }

            echo "SELECT EmpID, ECode, Ename2 FROM {$headerTB} AS t1
                                        JOIN (
                                           SELECT payrollMasterID AS payID FROM {$masterTB} WHERE companyID={$companyID}
                                           AND payrollYear={$pYear} AND payrollMonth={$pMonth} AND approvedYN=1
                                        ) AS payrollMaster ON payrollMaster.payID = t1.payrollMasterID
                                        {$str}
                                        WHERE companyID={$companyID} {$segmentFilter}";
            exit;

            $empArr = $this->db->query("SELECT EmpID, ECode, Ename2 FROM {$headerTB} AS t1
                                        JOIN (
                                           SELECT payrollMasterID AS payID FROM {$masterTB} WHERE companyID={$companyID}
                                           AND payrollYear={$pYear} AND payrollMonth={$pMonth} AND approvedYN=1
                                        ) AS payrollMaster ON payrollMaster.payID = t1.payrollMasterID
                                        {$str}
                                        WHERE companyID={$companyID} {$segmentFilter}")->result_array();

            $html = '<select name="empID[]" id="empID" class="form-control" multiple="multiple"  required>';

            if ($empArr) {
                foreach ($empArr as $empID) {
                    $html .= '<option value="' . $empID['EmpID'] . '">' . $empID['ECode'] . '|' . $empID['Ename2'] . '</option>';
                }
            }
            $html .= '</select>';

            echo json_encode(['s', $html]);
        }
    }


    public function fetch_paySheets_nonPayroll()
    {
        $companyID = current_companyID();

        $this->datatables->select('payrollMasterID, documentCode, payrollYear, payrollMonth, narration, confirmedYN, approvedYN, templateID', false)
            ->from('srp_erp_non_payrollmaster')
            ->edit_column('payrollMonth', '$1', 'payrollMonthInName(payrollYear, payrollMonth)')
            ->add_column('confirm', '$1', 'confirm_approval(confirmedYN)')
            ->add_column('approve', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SPN",payrollMasterID)')
            ->add_column('action', '$1', 'load_non_payroll_action(payrollMasterID, confirmedYN, approvedYN, payrollYear, payrollMonth, Y, templateID)')
            ->where('companyID', $companyID);
        echo $this->datatables->generate();
    }

    function get_paySlip_profile()
    {
        $companyID = current_companyID();
        $payrollMonth = trim($this->input->post('payrollMonth') ?? '');
        $isNonPayroll = trim($this->input->post('isNonPayroll') ?? '');
        $empID = trim($this->input->post('empID') ?? '');
        $pYear = date('Y', strtotime($payrollMonth));
        $pMonth = date('m', strtotime($payrollMonth));

        $headerTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';
        $masterTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';


        $payrollData = $this->db->query("SELECT payrollMasterID, visibleDate FROM {$headerTB} AS t1
                                       JOIN (
                                          SELECT payrollMasterID AS payID, visibleDate FROM {$masterTB} WHERE companyID={$companyID}
                                          AND payrollYear={$pYear} AND payrollMonth={$pMonth} AND approvedYN=1
                                       ) AS payrollMaster ON payrollMaster.payID = t1.payrollMasterID
                                       WHERE companyID={$companyID} AND empID={$empID}  ")->row_array();

        $warning_msg = $this->lang->line('common_warning');
        $payroll_not_run = $this->lang->line('profile_payroll_nor_run_on_selected_month_for_you');
        if (empty($payrollData)) {
            $returnData = '<div class="col-sm-12"><div class="alert alert-warning">
                             <strong>' . $warning_msg . '<!--Warning-->!</strong> <br/>' . $payroll_not_run . '<!--Payroll Not run on selected month for you-->.
                           </div></div>';
            die($returnData);
        }

        if ($payrollData['visibleDate'] > date('Y-m-d')) {
            $returnData = '<div class="col-sm-12"><div class="alert alert-warning">
                             <strong>' . $warning_msg . '<!--Warning-->!</strong> <br/>' . $payroll_not_run . '<!--Payroll Not run on selected month for you-->.
                           </div></div>';
            die($returnData);
        }

        $this->response_payslip_pdf($payrollData['payrollMasterID']);
    }

    function profile_payslip_print(){
        //echo '<pre>'; print_r($_POST); echo '</pre>';        die();
        $payrollID = $this->input->post('payrollMasterID');
        $isFromProfile = $this->input->post('isFromProfile');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $empID = ($isFromProfile == 1)? current_userID(): $this->input->post('empID');

        return $this->response_payslip_pdf($payrollID, $empID, $isNonPayroll);
    }

    function response_payslip_pdf($payrollID = 0, $empID = 0, $isNonPayroll = 0)
    {

        if ($payrollID == 0) {
            $payrollID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payrollMasterID') ?? '');
        }

        if ($empID == 0) {
            $empID = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('empID') ?? '');
        }

        if($isNonPayroll == 0){
            $isNonPayroll = ($this->uri->segment(5)) ? $this->uri->segment(5) : trim($this->input->post('isNonPayroll') ?? '');
        }

        $ishtml = $this->input->post('html');
        $data['payrollMasterID'] = $payrollID;
        $data['empID'] = $empID;
        $data['isNonPayroll'] = $isNonPayroll;

        $data['masterData'] = $this->Template_paySheet_model->getPayrollDetails($payrollID, $isNonPayroll);

        $documentCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
        $code = ($isNonPayroll != 'Y') ? 'PT' : 'NPT';

        $template = getPolicyValues($code, $documentCode); //$this->template_paySheet_model->getDefault_paySlipTemplate($isNonPayroll);

        if ($template == 'Envoy') {
            $data['details'] = $this->Template_paySheet_model->get_empPaySlipDet($empID, $payrollID, $isNonPayroll);
            $pageSize = 'A5';
            if (!empty($data['details'])) {
                $this->load->model('Employee_model');
                $html = $this->load->view('system/hrm/print/pay_slip_print_envoy', $data, true);
            } else {
                $html = 'No data';
            }

        }else if ($template == 'Aitken') {
            $data['details'] = $this->Template_paySheet_model->get_empPaySlipDet($empID, $payrollID, $isNonPayroll);
            $pageSize = 'A5';
            if (!empty($data['details'])) {
                $this->load->model('Employee_model');
                $html = $this->load->view('system/hrm/print/pay_slip_print_aitken', $data, true);
            } else {
                $html = 'No data';
            }

        } else if ($template == 0) {
            $data['details'] = $this->Template_paySheet_model->get_empPaySlipDet($empID, $payrollID, $isNonPayroll);
            $pageSize = 'A4';
            if (!empty($data['details'])) {
                $this->load->model('Employee_model');
                $data['leaveDet'] = false; // $this->Employee_model->get_emp_leaveDet_paySheetPrint($empID, $data['masterData']);
                $html = $this->load->view('system/hrm/print/paySlipPrint', $data, true);
                if ($this->input->post('html')) {
                    echo $html;
                } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4', $data);
                }
            } else {
                $html = 'No data';
            }

        } else {

            $data['details'] = $this->Template_paySheet_model->fetchPaySheetData_employee($payrollID, $empID, $isNonPayroll);
            $pageSize = 'A5';

            if (!empty($data['details'])) {
                $data['header_det'] = $this->Template_paySheet_model->templateDetails($template);
                /*echo '$template:'.$template;
                echo '<pre>'; print_r($data['header_det']); echo '</pre>';die();*/
                $html = $this->load->view('system/hrm/print/paySlipPrint_template', $data, true);
            } else {
                $html = 'No data';
            }

        }
        // echo $html; die();
        if ($ishtml == 1) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $this->pdf->printed($html, $pageSize, $data['masterData']['approvedYN'],0);
        }

    }

    function payroll_dropDown()
    {
        $isNonPayroll = $this->input->post('isNonPayroll');

        echo json_encode(payrollMonth_dropDown($isNonPayroll));
    }

    function payroll_dropDown_with_visible_date()
    {
        $isNonPayroll = $this->input->post('isNonPayroll');

        echo json_encode(payrollMonth_dropDown_with_visible_date($isNonPayroll));
    }

    function getEmployeesDataTable()
    {
        $companyID = current_companyID();
        $segment = $this->input->post('segment');
        $currency = $this->input->post('currency');
        $payYear = $this->input->post('payYear');
        $payMonth = $this->input->post('payMonth');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $isVisaPartyType = $this->input->post('visaPartyType');
        $isVisaPartyID = $this->input->post('visaPartyID');
        $isVisaPartyTypeFilter = '';
        $isVisaPartyIDFilter = '';
        $effectiveDate = date('Y-m-t', strtotime($payYear . '-' . $payMonth . '-01'));
        $segmentFilter = '';
        $currencyFilter = '';

        $salaryDeclarationTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_pay_salarydeclartion' : 'srp_erp_pay_salarydeclartion';
        $headerDetailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollheaderdetails' : 'srp_erp_payrollheaderdetails';
        $payrollMaster = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';

        if(!empty($isVisaPartyType)){
            $isVisaPartyType_arr = explode(',',$isVisaPartyType);

            if(in_array('2',$isVisaPartyType_arr)){
                $isVisaPartyIDFilter = ' AND srp_employeesdetails.visaPartyID IN (' . $isVisaPartyID . ')';
            }

            $isVisaPartyTypeFilter = ' AND srp_employeesdetails.visaPartyType IN (' . $isVisaPartyType . ')';

        }


        if (!empty($segment)) {

            $segmentsArray = explode(',', $segment);
            $segmentIDs = array();
            
            foreach ($segmentsArray as $seg) {
                $parts = explode('|', $seg);
                if (isset($parts[0])) {
                    $segmentIDs[] = $parts[0];
                }
            }
    
            $segmentIDsString = implode(',', $segmentIDs);
            $segmentFilter = ' AND srp_erp_segment.segmentID IN (' . $segmentIDsString . ')';
        }


        // if (!empty($segment)) {
        //     $segmentFilter = ' AND srp_erp_segment.segmentID IN (' . $segment . ')';
        // }
        if (!empty($currency)) {
            $currencyFilter = ' AND srp_erp_currencymaster.currencyID IN (' . $currency . ')';
        }

        $where = 'srp_employeesdetails.isPayrollEmployee = 1 AND isDischargedStatus != 1 AND  EIdNo NOT IN (
                        SELECT  empID FROM ' . $payrollMaster . ' AS payMaster
                        JOIN ' . $headerDetailTB . ' AS payDet ON payDet.payrollMasterID = payMaster.payrollMasterID AND payDet.companyID=' . $companyID . '
                        WHERE payMaster.companyID = ' . $companyID . ' AND payrollYear=' . $payYear . ' AND payrollMonth=' . $payMonth . '
                  ) ' . $segmentFilter . ' ' . $currencyFilter . ' ' . $isVisaPartyTypeFilter . ' ' . $isVisaPartyIDFilter;

        $this->datatables->select('EIdNo, ECode, Ename2 AS empName, DesDescription, CurrencyCode, segmentCode');
        $this->datatables->from('srp_employeesdetails');
        $this->datatables->join(' (SELECT employeeNo FROM ' . $salaryDeclarationTB . ' WHERE companyID=' . $companyID . '
                                    AND payDate<="' . $effectiveDate . '" GROUP BY employeeNo) AS declarationTB',
            'declarationTB.employeeNo=srp_employeesdetails.EIdNo');
        $this->datatables->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $this->datatables->join('srp_erp_currencymaster', 'srp_employeesdetails.payCurrencyID = srp_erp_currencymaster.currencyID');
        $this->datatables->join('srp_erp_segment', 'srp_employeesdetails.segmentID = srp_erp_segment.segmentID AND companyID=' . $companyID);
        $this->datatables->join('(
                                    SELECT EIdNo AS empID, dischargedDate,
                                        IF( isDischarged != 1, 0,
                                        CASE
                                            WHEN \'' . $effectiveDate . '\' <= DATE_FORMAT(dischargedDate, \'%Y-%m-01\') THEN 0
                                            WHEN \'' . $effectiveDate . '\' > DATE_FORMAT(dischargedDate, \'%Y-%m-01\') THEN 1
                                        END
                                    )AS isDischargedStatus
                                    FROM srp_employeesdetails WHERE Erp_companyID =' . $companyID . '
                                ) AS dischargedStatusTB', ' ON dischargedStatusTB.empID = srp_employeesdetails.EIdNo', 'left');

        $isGroupAccess = getPolicyValues('PAC', 'All');
        if ($isGroupAccess == 1) {
            $currentEmp = current_userID();
            $this->datatables->join("(
                                        SELECT empTB.groupID, employeeID FROM srp_erp_payrollgroupemployees AS empTB
                                        JOIN srp_erp_payrollgroupincharge AS inCharge ON inCharge.groupID=empTB.groupID
                                        WHERE empTB.companyID={$companyID} AND inCharge.companyID={$companyID} AND empID={$currentEmp}
                                    ) AS accTb", 'accTb.employeeID=EIdNo');
        }
        $this->datatables->add_column('addBtn', '$1', 'addBtn()');
        $this->datatables->where('srp_employeesdetails.Erp_companyID', $companyID);
        $this->datatables->where('srp_employeesdetails.empConfirmedYN', 1);
        $this->datatables->where($where);

        echo $this->datatables->generate();
    }

    function getEmployeesDataTable_period_base()
    {
        $companyID = current_companyID();
        $segment = $this->input->post('segment');
        $currency = $this->input->post('currency');
        $payGroup = $this->input->post('payGroup');
        $period_drop = $this->input->post('period_drop');


        $payYear = $this->input->post('payYear');
        $payMonth = $this->input->post('payMonth');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $effectiveDate = date('Y-m-t', strtotime($payYear . '-' . $payMonth . '-01'));
        $segmentFilter = '';
        $currencyFilter = '';

        $salaryDeclarationTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_pay_salarydeclartion' : 'srp_erp_pay_salarydeclartion';
        $headerDetailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollheaderdetails' : 'srp_erp_payrollheaderdetails';
        $payrollMaster = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';

        if (!empty($segment)) {
            $segmentFilter = ' AND srp_erp_segment.segmentID IN (' . $segment . ')';
        }
        if (!empty($currency)) {
            $currencyFilter = ' AND srp_erp_currencymaster.currencyID IN (' . $currency . ')';
        }

        $where = 'srp_employeesdetails.isPayrollEmployee = 1 AND isDischargedStatus != 1 AND  EIdNo NOT IN (
                        SELECT  empID FROM ' . $payrollMaster . ' AS payMaster
                        JOIN ' . $headerDetailTB . ' AS payDet ON payDet.payrollMasterID = payMaster.payrollMasterID AND payDet.companyID=' . $companyID . '
                        WHERE payMaster.companyID = ' . $companyID . ' 
                  ) ' . $segmentFilter . ' ' . $currencyFilter; //AND payrollYear=' . $payYear . ' AND payrollMonth=' . $payMonth . '

        $this->datatables->select('EIdNo, ECode, Ename2 AS empName, DesDescription, CurrencyCode, segmentCode');
        $this->datatables->from('srp_employeesdetails');
        /*$this->datatables->join(' (SELECT employeeNo FROM ' . $salaryDeclarationTB . ' WHERE companyID=' . $companyID . '
                                    AND payDate<="' . $effectiveDate . '" GROUP BY employeeNo) AS declarationTB',
            'declarationTB.employeeNo=srp_employeesdetails.EIdNo');*/
        $this->datatables->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $this->datatables->join('srp_erp_currencymaster', 'srp_employeesdetails.payCurrencyID = srp_erp_currencymaster.currencyID');
        $this->datatables->join('srp_erp_segment', 'srp_employeesdetails.segmentID = srp_erp_segment.segmentID AND companyID=' . $companyID);
        $this->datatables->join('(
                                    SELECT EIdNo AS empID, dischargedDate,
                                        IF( isDischarged != 1, 0,
                                        CASE
                                            WHEN \'' . $effectiveDate . '\' <= DATE_FORMAT(dischargedDate, \'%Y-%m-01\') THEN 0
                                            WHEN \'' . $effectiveDate . '\' > DATE_FORMAT(dischargedDate, \'%Y-%m-01\') THEN 1
                                        END
                                    )AS isDischargedStatus
                                    FROM srp_employeesdetails WHERE Erp_companyID =' . $companyID . '
                                ) AS dischargedStatusTB', ' ON dischargedStatusTB.empID = srp_employeesdetails.EIdNo', 'left');

        $isGroupAccess = getPolicyValues('PAC', 'All');
        if ($isGroupAccess == 1) {
            $currentEmp = current_userID();
            $this->datatables->join("(
                                        SELECT empTB.groupID, employeeID FROM srp_erp_payrollgroupemployees AS empTB
                                        JOIN srp_erp_payrollgroupincharge AS inCharge ON inCharge.groupID=empTB.groupID
                                        WHERE empTB.companyID={$companyID} AND inCharge.companyID={$companyID} AND empID={$currentEmp}
                                    ) AS accTb", 'accTb.employeeID=EIdNo');
        }

        /*** Get employees who are assign to this payroll group ***/
        $this->datatables->join("(SELECT grEmp.employeeID, grEmp.groupID
                          FROM srp_erp_hrperiodgroup AS hrMas
                          JOIN srp_erp_hrperiodassign AS hrAss ON hrMas.hrGroupID = hrAss.hrGroupID
                          JOIN srp_erp_payrollgroupemployees AS grEmp ON grEmp.groupID = hrAss.accessGroupID
                          WHERE hrMas.hrGroupID = {$payGroup}
                        ) AS payGrpTb", 'payGrpTb.employeeID=srp_employeesdetails.EIdNo');

        $this->datatables->add_column('addBtn', '$1', 'addBtn()');
        $this->datatables->where('srp_employeesdetails.Erp_companyID', $companyID);
        $this->datatables->where('srp_employeesdetails.empConfirmedYN', 1);
        $this->datatables->where($where);

        echo $this->datatables->generate();
    }

    function delete_PayrollEmp()
    {
        $this->form_validation->set_rules('payrollID', 'Payroll ID', 'trim|required');
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Template_paySheet_model->delete_PayrollEmp());
        }
    }

    function update_payrollEmpComment()
    {
        echo json_encode($this->Template_paySheet_model->update_payrollEmpComment());
    }

    function payroll_reversing()
    {

    }

    function get_payScale_report()
    {
        $requestType = $this->uri->segment(3);
        $companyID = current_companyID();
        $segment = $this->input->post('segmentID');

        $category = $this->db->query("SELECT salaryCategoryType,salaryCategoryID,salaryDescription,deductionPercntage,companyContributionPercentage
                                      FROM srp_erp_pay_salarycategories 
                                      WHERE companyID='{$companyID}' AND isPayrollCategory=1 ORDER BY salaryCategoryType ASC")->result_array();
        $query = '';
        $asofDate = $this->input->post('asofDate');
        if ($category) {
            foreach ($category as $cat) {
                $salaryDescription = str_replace(' ', '', $cat['salaryDescription']);
                $salaryDescription = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription);

                $query .= "SUM(IF(catTB.salaryCategoryID = " . $cat['salaryCategoryID'] . " , transactionAmount, 0)) as " . $salaryDescription . ",";

                /*if ($cat['salaryCategoryType'] == 'D' || $cat['salaryCategoryType'] == 'DC') {
                    $query .= "SUM(IF(srp_erp_pay_salarydeclartion.salaryCategoryID = " . $cat['salaryCategoryID'] . ", transactionAmount * -1, 0))   as " . $salaryDescription . ",";
                }*/
            }
            $query .= "salDec.companyID";
        }

        if ($query == '') {
            $data['details'] = false;
            $data['currency'] = false;
        } else {
            $filter = '';
            if (!empty($segment)) {
                $commaList = implode(', ', $segment);
                $filter .= "AND srp_employeesdetails.segmentID IN($commaList) ";

                $str = '';
                $isGroupAccess = getPolicyValues('PAC', 'All');
                if ($isGroupAccess == 1) {
                    $currentEmp = current_userID();
                    $str = "JOIN (
                        SELECT groupID FROM srp_erp_payrollgroupincharge
                        WHERE companyID={$companyID} AND empID={$currentEmp}
                    ) AS accTb ON accTb.groupID = salDec.accessGroupID";
                }

                $data['details'] = $this->db->query("SELECT srp_erp_segment.description as segment,ECode,EmpSecondaryCode,EDOJ, Ename2,
                                                IF(isDischarged = 1, IF( dischargedDate < '{$asofDate}', 1, 0), 0) isDischarged2,
                                                DesDescription, payCurrency AS transactionCurrency, sal.*
                                                FROM srp_employeesdetails  
                                                LEFT JOIN srp_designation ON DesignationID = EmpDesignationId
                                                LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentID=srp_employeesdetails.segmentID
                                                AND srp_erp_segment.companyID = {$companyID}
                                                LEFT JOIN (
                                                    SELECT salDec.employeeNo, transactionCurrency, $query 
                                                    FROM srp_erp_pay_salarydeclartion AS salDec                                                      
                                                    JOIN srp_erp_pay_salarycategories AS catTB ON salDec.salaryCategoryID = catTB.salaryCategoryID
                                                    AND catTB.companyID = {$companyID}   
                                                    WHERE salDec.companyID={$companyID} AND effectiveDate < '{$asofDate}' GROUP BY employeeNo
                                                ) sal ON employeeNo = EidNo
                                                WHERE srp_employeesdetails.Erp_companyID = {$companyID} AND isPayrollEmployee= 1 AND empConfirmedYN = 1
                                                {$filter} GROUP BY employeeNo, payCurrency HAVING isDischarged2 = 0 ORDER BY ECode")->result_array();

                $data['currency'] = $this->db->query("SELECT transactionCurrency as currency, $query
                                                      FROM srp_erp_pay_salarydeclartion AS salDec
                                                      LEFT JOIN srp_erp_pay_salarycategories AS catTB ON salDec.salaryCategoryID = catTB.salaryCategoryID
                                                      AND catTB.companyID={$companyID}
                                                      LEFT JOIN srp_employeesdetails ON employeeNo = EidNo
                                                      LEFT JOIN srp_designation ON DesignationID = EmpDesignationId
                                                      LEFT JOIN srp_erp_segment on srp_erp_segment.segmentID=srp_employeesdetails.segmentID
                                                      AND srp_erp_segment.companyID=salDec.companyID
                                                      {$str}
                                                      WHERE salDec.companyID = '{$companyID}' AND effectiveDate < '{$asofDate}' AND isPayrollEmployee=1
                                                      AND NOT EXISTS (
                                                         SELECT EIdNo FROM (
                                                            SELECT EIdNo, IF( dischargedDate < '{$asofDate}', 1, 0) isDischarged2, dischargedDate
                                                            FROM srp_employeesdetails 
                                                            WHERE isDischarged = 1 AND Erp_companyID = {$companyID} {$filter}
                                                         ) t1 WHERE isDischarged2 = 1 AND t1.EIdNo = srp_employeesdetails. EIdNo
                                                      )
                                                      $filter GROUP BY transactionCurrency ")->result_array();


            } else {
                $data['details'] = false;
                $data['currency'] = false;
            }


        }
        $data['asofDate'] = $asofDate;
        $data['category'] = $category;
        $data['segment'] = $segment;

        if ($requestType == 'pdf') {
            $html = $this->load->view('system/hrm/ajax/load-employee-payscale-report_pdf.php', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        } else {
            echo $html = $this->load->view('system/hrm/ajax/load-employee-payscale-report.php', $data, true);
        }

    }

    function get_payScale_report_pdf()
    {
        $companyID = current_companyID();
        $segment = $this->input->post('segmentID');
        $category = $this->db->query("SELECT salaryCategoryType,salaryCategoryID,salaryDescription,deductionPercntage,companyContributionPercentage FROM srp_erp_pay_salarycategories WHERE companyID='{$companyID}' AND isPayrollCategory=1  order by salaryCategoryType ASC")->result_array();
        $query = '';
        $asofDate = $this->input->post('asofDate');
        if ($category) {
            foreach ($category as $cat) {
                $salaryDescription = str_replace(' ', '', $cat['salaryDescription']);
                $salaryDescription = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription);
                /*   if ($cat['salaryCategoryType'] == 'A') {*/
                $query .= "SUM(IF(srp_erp_pay_salarydeclartion.salaryCategoryID = " . $cat['salaryCategoryID'] . " , transactionAmount, 0)) as " . $salaryDescription . ",";
                /*  }*/
                /*  if ($cat['salaryCategoryType'] == 'D' || $cat['salaryCategoryType'] == 'DC') {
                      $query .= "SUM(IF(srp_erp_pay_salarydeclartion.salaryCategoryID = " . $cat['salaryCategoryID'] . ", transactionAmount * -1, 0))   as " . $salaryDescription . ",";
                  }*/
            }
            $query .= "srp_erp_pay_salarydeclartion.companyID";
        }

        if ($query == '') {
            $data['details'] = false;
            $data['currency'] = false;
        } else {
            $filter = '';
            if (!empty($segment)) {
                $commaList = implode(', ', $segment);
                $filter .= "AND srp_employeesdetails.segmentID IN($commaList)";

                $data['details'] = $this->db->query("SELECT srp_erp_segment.description as segment,ECode, Ename1, Ename2, Ename3, Ename4, employeeNo, srp_erp_pay_salarycategories.salaryCategoryType, DesDescription, transactionCurrency, $query  FROM srp_erp_pay_salarydeclartion LEFT JOIN srp_erp_pay_salarycategories ON srp_erp_pay_salarydeclartion.salaryCategoryID = srp_erp_pay_salarycategories.salaryCategoryID AND srp_erp_pay_salarydeclartion.companyID = srp_erp_pay_salarycategories.companyID LEFT JOIN srp_employeesdetails ON employeeNo = EidNo LEFT JOIN srp_designation ON DesignationID = EmpDesignationId LEFT JOIN srp_erp_segment on srp_erp_segment.segmentID=srp_employeesdetails.segmentID AND srp_erp_segment.companyID=srp_erp_pay_salarydeclartion.companyID WHERE srp_erp_pay_salarydeclartion.companyID = '{$companyID}' AND effectiveDate < '{$asofDate}' $filter GROUP BY employeeNo , transactionCurrency ")->result_array();
                $data['currency'] = $this->db->query("SELECT transactionCurrency as currency,$query  FROM srp_erp_pay_salarydeclartion LEFT JOIN srp_erp_pay_salarycategories ON srp_erp_pay_salarydeclartion.salaryCategoryID = srp_erp_pay_salarycategories.salaryCategoryID AND srp_erp_pay_salarydeclartion.companyID = srp_erp_pay_salarycategories.companyID LEFT JOIN srp_employeesdetails ON employeeNo = EidNo LEFT JOIN srp_designation ON DesignationID = EmpDesignationId LEFT JOIN srp_erp_segment on srp_erp_segment.segmentID=srp_employeesdetails.segmentID AND srp_erp_segment.companyID=srp_erp_pay_salarydeclartion.companyID WHERE srp_erp_pay_salarydeclartion.companyID = '{$companyID}' AND effectiveDate < '{$asofDate}' $filter GROUP BY transactionCurrency ")->result_array();
            } else {
                $data['details'] = false;
                $data['currency'] = false;
            }

        }
        $data['category'] = $category;
        $data['asofDate'] = $asofDate;
        $html = $this->load->view('system/hrm/ajax/load-employee-payscale-report_pdf.php', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    function pendingExpensesClaims()
    {
        $companyID = current_companyID();
        $payYear = $this->input->post('payYear');
        $payMonth = $this->input->post('payMonth');
        $empList = $this->input->post('selectedEmployees');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $payDateMax = date('Y-m-t', strtotime($payYear . '-' . $payMonth . '-01'));
        $expenseClaimcheck = getPolicyValues('ECDT','All');
        $str = '';
        $str_com = '';
        $count = 0;
        if ($isNonPayroll != 'Y') {
            if($expenseClaimcheck == 'NA'){
                
                $_pending = $this->db->query("SELECT * FROM (
                    SELECT claimDet.expenseClaimMasterAutoID AS masterID, expenseClaimCode, empName, empCurrency,
                    FORMAT(SUM(empCurrencyAmount), empCurrencyDecimalPlaces) AS empAmnt, DATE_FORMAT(expenseClaimDate,'%Y-%m-01') AS firstDate
                    FROM  srp_erp_expenseclaimmaster AS claimMaster
                    JOIN srp_erp_expenseclaimdetails AS claimDet ON claimDet.expenseClaimMasterAutoID=claimMaster.expenseClaimMasterAutoID
                    JOIN srp_erp_expenseclaimcategories AS expCat ON expCat.expenseClaimCategoriesAutoID = claimDet.expenseClaimCategoriesAutoID
                    AND expCat.companyID = {$companyID}
                    JOIN (
                        SELECT EIdNo AS EmpID, CONCAT(Ecode,'  -  ',Ename2) AS empName
                        FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                    )AS payHead ON payHead.EmpID = claimMaster.claimedByEmpID
                    WHERE approvedYN = 1  AND addedToSalary = 0 AND addedForPayment = 0
                    AND claimMaster.claimedByEmpID IN ({$empList})
                    GROUP BY claimDet.expenseClaimMasterAutoID
                ) AS dataMaster WHERE firstDate <= '{$payDateMax}' ")->result_array();
                
            }else{

                $_pending = $this->db->query("SELECT * FROM (
                    SELECT claimDet.expenseClaimMasterAutoID AS masterID, expenseClaimCode, empName, empCurrency,
                    FORMAT(SUM(empCurrencyAmount), empCurrencyDecimalPlaces) AS empAmnt, DATE_FORMAT(expenseClaimDate,'%Y-%m-01') AS firstDate
                    FROM srp_erp_expenseclaimmaster AS claimMaster
                    JOIN srp_erp_expenseclaimdetails AS claimDet ON claimDet.expenseClaimMasterAutoID = claimMaster.expenseClaimMasterAutoID
                    JOIN srp_erp_expenseclaimcategories AS expCat ON expCat.expenseClaimCategoriesAutoID = claimDet.expenseClaimCategoriesAutoID
                    AND expCat.companyID = {$companyID}
                    JOIN (
                        SELECT EIdNo AS EmpID, CONCAT(Ecode, '  -  ', Ename2) AS empName
                        FROM srp_employeesdetails WHERE Erp_companyID = {$companyID}
                    ) AS payHead ON payHead.EmpID = claimMaster.claimedByEmpID
                    WHERE approvedYN = 1 AND addedToSalary = 0 AND addedForPayment = 0
                    AND claimMaster.claimedByEmpID IN ({$empList})
                    AND DAY(expenseClaimDate) < {$expenseClaimcheck}
                    GROUP BY claimDet.expenseClaimMasterAutoID
                ) AS dataMaster WHERE firstDate <= '{$payDateMax}'")->result_array();

            }


            if (!empty($_pending)) {
                foreach ($_pending as $key => $row) {
                    $r_ID = $row['masterID'];
                    $str .= '<tr><td>' . ($key + 1) . '</td><td>' . $row['expenseClaimCode'] . '</td><td>' . $row['empName'] . '</td><td>' . $row['empCurrency'] . '</td>';
                    $str .= '<td><div align="right">' . $row['empAmnt'] . '</div></td>';
                    $str .= '<td style="text-align: center;">';
                    $str .= '<input type="checkbox" name="selectedExpenseClaim[]" class="expCls" value="' . $r_ID . '" onclick="checkTotalChecked(\'.expCls\', \'#allCheckBox\')">';
                    $str .= '</td>';
                    $str .= '</tr>';
                }
            }

            $count = count($_pending);

            $localCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
            $_pending = $this->db->query("SELECT * FROM (
                                          SELECT det.commissionAutoID AS masterID, det.commissionDetailID AS detailID, documentSystemCode, empName, payCurrency as empCurrency,
                                          FORMAt(SUM(commissionAmount/convEmp.conversion), DecimalPlaces) AS empAmnt, DATE_FORMAT(invoiceDate,'%Y-%m-01') AS firstDate
                                          FROM  srp_erp_invoice_commission_detail AS det
                                          JOIN srp_erp_invoice_commision AS mast ON det.commissionAutoID = mast.commissionAutoID
                                          JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = mast.invoiceID
                                          JOIN (
                                              SELECT EIdNo AS EmpID, CONCAT(Ecode,'  -  ',Ename2) AS empName, payCurrency, payCurrencyID
                                              FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                          )AS payHead ON payHead.EmpID = det.salesPersonEmpID
                                          JOIN srp_erp_currencymaster AS currencyMaster ON currencyMaster.currencyID = payHead.payCurrencyID
                                          JOIN srp_erp_companycurrencyconversion AS convEmp ON convEmp.masterCurrencyID = {$localCurrencyID}
                                            AND convEmp.companyID={$companyID} AND convEmp.subCurrencyID = payHead.payCurrencyID
                                          WHERE mast.approvedYN = 1  AND PVAutoID = 0 AND payrollID = 0
                                          AND det.salesPersonEmpID IN ({$empList})
                                          GROUP BY det.commissionAutoID, det.salesPersonEmpID
                                      ) AS dataMaster WHERE firstDate <= '{$payDateMax}' ")->result_array();


            if (!empty($_pending)) {
                foreach ($_pending as $key => $row) {
                    $r_ID = $row['masterID'];
                    $str_com .= '<tr><td>' . ($key + 1) . '</td><td>' . $row['documentSystemCode'] . '</td><td>' . $row['empName'] . '</td><td>' . $row['empCurrency'] . '</td>';
                    $str_com .= '<td><div align="right">' . $row['empAmnt'] . '</div></td>';
                    $str_com .= '<td style="text-align: center;">';
                    $str_com .= '<input type="checkbox" name="selectedExpenseClaim[]" class="commissionCls" value="' . $r_ID . '" onclick="checkTotalChecked(\'.commissionCls\', \'#allCheckBox2\')">';
                    $str_com .= '</td>';
                    $str_com .= '</tr>';
                }
            }

            $count1 = count($_pending);
        }

        $updateColumn = ($isNonPayroll == 'Y') ? 'nonPayrollID' : 'payrollID';
        $amountColumn = ($isNonPayroll == 'Y') ? 'noPaynonPayrollAmount' : 'noPayAmount';

        $_pending = $this->db->query("SELECT noPayData.*, CONCAT(Ecode,'  -  ',Ename2) AS empName, CurrencyCode,
                                      FORMAT(amountColumn, DecimalPlaces) AS empAmnt
                                      FROM (
                                          SELECT ID, reviewTB.empID, $amountColumn AS amountColumn, documentCode,
                                          DATE_FORMAT(attendanceDate, '%Y-%m-01') AS firstAttDate
                                          FROM srp_erp_pay_empattendancereview AS reviewTB
                                          JOIN srp_erp_leavemaster AS lMaster ON lMaster.leaveMasterID=reviewTB.leaveMasterID
                                          WHERE reviewTB.companyID='{$companyID}' AND lMaster.companyID='{$companyID}'
                                          AND {$updateColumn} = 0 AND reviewTB.empID IN ({$empList}) AND $amountColumn != 0
                                          AND $amountColumn IS NOT NULL
                                      ) AS noPayData
                                      JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = empID AND Erp_companyID='{$companyID}'
                                      JOIN srp_erp_currencymaster AS curMaster ON curMaster.currencyID=payCurrencyID
                                      WHERE firstAttDate <= '{$payDateMax}' ")->result_array();
        $str_no = '';
        if (!empty($_pending)) {
            foreach ($_pending as $key => $row) {
                $r_ID = $row['ID'];
                $str_no .= '<tr><td>' . ($key + 1) . '</td><td>' . $row['documentCode'] . '</td><td>' . $row['empName'] . '</td><td>' . $row['CurrencyCode'] . '</td>';
                $str_no .= '<td><div align="right">' . $row['empAmnt'] . '</div></td>';
                $str_no .= '<td style="text-align: center;">';
                $str_no .= '<input type="checkbox" name="selectedNoPay[]" class="noPayCls" value="' . $r_ID . '" onclick="checkTotalChecked(\'.noPayCls\', \'#allCheckBox1\')">';
                $str_no .= '</td>';
                $str_no .= '</tr>';
            }
        }

        return ['e', ($count + $count1 + count($_pending)), 'pendingExpenseClaims', 'expenseClaim' => $str, 'commission' => $str_com, 'noPay' => $str_no];
    }

    function ssoCal($payrollMasterID = 512, $payDateMin = '2017-09-01')
    {

        /*$am =  $this->db->query("");

          echo '<pre>'; print_r($am); echo '</pre>';die();
          echo $this->db->last_query();
          die();*/

        //|(|#43|+|#44|+|#45|+|#46|+|#49|+|#52|+|#57|+|!0|)||*|_0.07_
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserGroup = current_user_group();
        $createdUserName = current_employee();
        $createdDateTime = current_date();
        $salary_categories_arr = salary_categories(array('A', 'D'));

        $ssoData = $this->db->query("SELECT ssoTB.socialInsuranceID, formulaString, expenseGlAutoID, liabilityGlAutoID, masterTB.payGroupID, isSlabApplicable, SlabID
                                     FROM srp_erp_socialinsurancemaster AS ssoTB
                                     JOIN srp_erp_paygroupmaster AS masterTB ON masterTB.socialInsuranceID=ssoTB.socialInsuranceID AND masterTB.companyID={$companyID}
                                     JOIN srp_erp_paygroupformula AS formulaTB ON formulaTB.payGroupID=masterTB.payGroupID AND formulaTB.companyID={$companyID}
                                     JOIN (
                                        SELECT socialInsuranceMasterID AS ssoID FROM srp_erp_socialinsurancedetails WHERE companyID={$companyID}
                                        GROUP BY socialInsuranceMasterID
                                     ) AS ssoDetail ON ssoDetail.ssoID = ssoTB.socialInsuranceID
                                     WHERE ssoTB.companyID={$companyID} AND masterTB.payGroupID=173")->result_array();
        echo $this->db->last_query();
        echo '<pre>';
        print_r($ssoData);
        echo '</pre>';
        foreach ($ssoData as $key => $ssoRow) {

            $isSlabApplicable = trim($ssoRow['isSlabApplicable'] ?? '');
            $slabID = trim($ssoRow['SlabID'] ?? '');
            $SSO_ID = trim($ssoRow['socialInsuranceID'] ?? '');
            $payGroupID = trim($ssoRow['payGroupID'] ?? '');
            $formula = trim($ssoRow['formulaString'] ?? '');
            $expenseGL = trim($ssoRow['expenseGlAutoID'] ?? '');
            $liabilityGL = trim($ssoRow['liabilityGlAutoID'] ?? '');

            if (!empty($formula) && $formula != null) {
                $getBalancePay = ($isSlabApplicable == 1) ? 'N' : 'Y';
                $formulaBuilder = formulaBuilder_to_sql($ssoRow, $salary_categories_arr, $payDateMin, $payGroupID, $getBalancePay);

                $formulaDecode = $formulaBuilder['formulaDecode'];
                $select_str2 = $formulaBuilder['select_str2'];
                $whereInClause = $formulaBuilder['whereInClause'];

                $select_str2 = (trim($select_str2) == '') ? '' : $select_str2 . ',';


                if ($isSlabApplicable == 1) {
                    $slabData = $this->db->query("SELECT startRangeAmount strAmount, endRangeAmount endAmount, formulaString
                                                  FROM srp_erp_ssoslabmaster AS slabMaster
                                                  JOIN srp_erp_ssoslabdetails AS slabDet ON slabMaster.ssoSlabMasterID = slabDet.ssoSlabMasterID
                                                  AND slabDet.companyID={$companyID}
                                                  WHERE slabMaster.companyID={$companyID} AND slabMaster.ssoSlabMasterID={$slabID}")->result_array();


                    if (!empty($slabData)) {
                        foreach ($slabData as $keySlab => $slabRow) {
                            $formulaBuilder_slab = formulaBuilder_to_sql($slabRow, $salary_categories_arr, $payDateMin, $payGroupID);
                            $formulaDecode_slab = $formulaBuilder_slab['formulaDecode'];
                            $select_str_slab = $formulaBuilder_slab['select_str2'];
                            $whereInClause_slab = $formulaBuilder_slab['whereInClause'];

                            $strAmount = $slabRow['strAmount'];
                            $endAmount = $slabRow['endAmount'];

                            $this->db->query("INSERT INTO srp_erp_payrolldetail2 ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, GLCode, liabilityGL,
                                              transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces,
                                              companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                              companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER,
                                              companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdPCID, createdUserID, createdUserGroup, createdUserName,
                                              createdDateTime, segmentID, segmentCode)

                                              SELECT {$payrollMasterID}, {$payGroupID}, 'PAY_GROUP', 'PAY_GROUP', calculationTB.empID, 'G', '{$expenseGL}', $liabilityGL,
                                              round( (({$formulaDecode_slab}) * -1 ), trDPlace ) AS trAmount, trCuID, trCu, 1, trDPlace,
                                              round( (({$formulaDecode_slab}) / locCuER) * -1 , locCuDPlace ) AS localAmount, locCuID, locCu, locCuER, locCuDPlace,
                                              round( (({$formulaDecode_slab}) / repCuER) * -1 , repCuDPlace ) AS reportingAmount, repCuID, repCu, repCuER, repCuDPlace,
                                              {$companyID}, '{$companyCode}', '{$createdPCID}', '{$createdUserID}', '{$createdUserGroup}', '{$createdUserName}',
                                              '{$createdDateTime}', seg.segmentID, seg.segmentCode
                                              FROM (
                                                    SELECT payDet.empID, {$select_str_slab},
                                                    transactionCurrencyID AS trCuID, transactionCurrency AS trCu, transactionER AS trER, transactionCurrencyDecimalPlaces
                                                    AS trDPlace, companyLocalCurrencyID AS locCuID , companyLocalCurrency AS locCu, companyLocalER AS locCuER,
                                                    companyLocalCurrencyDecimalPlaces AS locCuDPlace, companyReportingCurrencyID AS repCuID, companyReportingCurrency AS repCu,
                                                    companyReportingER AS repCuER, companyReportingCurrencyDecimalPlaces AS repCuDPlace
                                                    FROM srp_erp_payrolldetail AS payDet
                                                    JOIN srp_erp_socialinsurancedetails AS ssoDet ON ssoDet.empID = payDet.empID AND ssoDet.companyID={$companyID}
                                                    AND socialInsuranceMasterID={$SSO_ID}
                                                    WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID}
                                                    {$whereInClause_slab}  GROUP BY payDet.empID, salCatID, detailType
                                              ) calculationTB
                                              JOIN (
                                                    SELECT EmpID, segmentID FROM srp_erp_payrollheaderdetails WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                                              ) AS empTB ON empTB.EmpID=calculationTB.empID
                                              JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID AND seg.companyID = {$companyID}
                                              WHERE calculationTB.empID IN (
                                                   SELECT empID FROM (
                                                       SELECT calculationTB.empID, round( ({$formulaDecode}), trDPlace) AS trAmount FROM (
                                                            SELECT payDet.empID, {$select_str2} transactionCurrencyDecimalPlaces AS trDPlace
                                                            FROM srp_erp_payrolldetail AS payDet
                                                            JOIN srp_erp_socialinsurancedetails AS ssoDet ON ssoDet.empID = payDet.empID AND ssoDet.companyID={$companyID}
                                                            AND socialInsuranceMasterID={$SSO_ID}
                                                            WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID}
                                                            {$whereInClause}  GROUP BY payDet.empID, salCatID, detailType
                                                       ) calculationTB GROUP BY empID
                                                   ) AS currentMonthAmountTB WHERE trAmount > {$strAmount} and trAmount <= {$endAmount}
                                              ) GROUP BY calculationTB.empID");

                        }
                    }

                } else {

                    $this->db->query("INSERT INTO srp_erp_payrolldetail2 ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, GLCode, liabilityGL,
                                   transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces,
                                   companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                   companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces,
                                   companyID, companyCode, createdPCID, createdUserID, createdUserGroup, createdUserName, createdDateTime, segmentID, segmentCode)

                                   SELECT {$payrollMasterID}, {$payGroupID}, 'PAY_GROUP', 'PAY_GROUP', calculationTB.empID, 'G', '{$expenseGL}', $liabilityGL,
                                   round((({$formulaDecode}) * -1 ), transactionCurrencyDecimalPlaces)AS transactionAmount, transactionCurrencyID, transactionCurrency,
                                   transactionER, transactionCurrencyDecimalPlaces,
                                   round( (({$formulaDecode}) / companyLocalER) * -1 , companyLocalCurrencyDecimalPlaces  )AS localAmount,
                                   companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                   round( (({$formulaDecode}) / companyReportingER) * -1 , companyReportingCurrencyDecimalPlaces  )AS reportingAmount,
                                   companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces,
                                   {$companyID}, '{$companyCode}', '{$createdPCID}', '{$createdUserID}', '{$createdUserGroup}', '{$createdUserName}', '{$createdDateTime}',
                                   seg.segmentID, seg.segmentCode
                                   FROM (
                                        SELECT payDet.empID, fromTB, detailType, salCatID, {$select_str2}
                                        transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces,
                                        companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                        companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces
                                        FROM srp_erp_payrolldetail AS payDet
                                        JOIN srp_erp_socialinsurancedetails AS ssoDet ON ssoDet.empID = payDet.empID AND ssoDet.companyID={$companyID}
                                        AND socialInsuranceMasterID={$SSO_ID}
                                        WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID}
                                        {$whereInClause}  GROUP BY payDet.empID, salCatID, detailType
                                   ) calculationTB
                                   JOIN (
                                        SELECT EmpID, segmentID FROM srp_erp_payrollheaderdetails WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                                   ) AS empTB ON empTB.EmpID=calculationTB.empID
                                   JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID AND seg.companyID = {$companyID}
                                   GROUP BY empID");
                }


            }
        }

    }

    function payeeCal()
    {
        //return $this->template_paySheet_model->payeeCal324(4934342, 'N');
        //return $this->template_paySheet_model->payGroup_temporary_calculation(492, 'N');
        $payrollMasterID = 512;
        $isNonPayroll = 'N';
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdUserID = current_userID();
        $salary_categories_arr = salary_categories(array('A', 'D'));
        $payGroup_arr = get_payGroup(1);


        $payGroups = $this->db->query("SELECT temp.payGroupID, formulaString, payGroupCategories
                                       FROM srp_erp_pay_templatefields AS temp
                                       JOIN srp_erp_paygroupformula AS formulaTB ON formulaTB.payGroupID=temp.payGroupID AND formulaTB.companyID={$companyID}
                                       WHERE temp.fieldType = 'G' AND temp.companyID = {$companyID} AND isCalculate =1
                                       and temp.payGroupID=172")->result_array();


        foreach ($payGroups as $key => $payRow) {

            $payGroupID = trim($payRow['payGroupID'] ?? '');
            $formula = trim($payRow['formulaString'] ?? '');

            if (!empty($formula) && $formula != null) {
                $formulaBuilder = payGroup_formulaBuilder_to_sql('decode', $payRow, $salary_categories_arr, $payGroup_arr, $payGroupID, null);

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


                $this->db->query("SELECT {$payrollMasterID}, {$payGroupID}, 'PAY_GROUP', calculationTB.empID, ' ',transactionCurrencyID, transactionCurrency,
                                  transactionCurrencyDecimalPlaces, round((" . $formulaDecode . "), transactionCurrencyDecimalPlaces) AS transactionAmount,
                                  segmentID, segmentCode, {$companyID}, '{$companyCode}', '{$createdUserID}'
                                  FROM (
                                        SELECT payDet.empID, fromTB, detailType, salCatID, " . $select_salCat_str . " " . $select_group_str . " " . $select_monthlyAD_str . "
                                        transactionCurrencyID, transactionCurrency, transactionCurrencyDecimalPlaces, srp_erp_segment.segmentID, srp_erp_segment.segmentCode
                                        FROM {$detailTB} AS payDet
                                        JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = payDet.empID AND empTB.Erp_companyID={$companyID}
                                        JOIN srp_erp_segment ON srp_erp_segment.segmentID = empTB.segmentID AND srp_erp_segment.companyID = {$companyID}
                                        WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID} {$whereIN}
                                        GROUP BY payDet.empID, salCatID, payDet.fromTB, detailTBID
                                  ) calculationTB
                                  GROUP BY empID ");

                echo $this->db->last_query();
            }
        }
    }

    function decode_formula_categories()
    {

        $payGroups = $this->db->query("SELECT formulaString, formulaID FROM srp_erp_paygroupformula AS formulaTB ")->result_array();


        foreach ($payGroups as $key => $payRow) {

            $formulaID = trim($payRow['formulaID'] ?? '');
            $formula = trim($payRow['formulaString'] ?? '');

            if (!empty($formula) && $formula != null) {

                $salaryCategories = '';
                $ssoCategories = '';
                $payGroupCategories = '';

                $formula = (is_array($payRow)) ? trim($payRow['formulaString'] ?? '') : $payRow;
                $operand_arr = operand_arr();


                $formula_arr = explode('|', $formula); // break the formula


                foreach ($formula_arr as $formula_row) {

                    if (trim($formula_row) != '') {
                        if (in_array($formula_row, $operand_arr)) { //validate is a operand

                        } else {

                            $elementType = $formula_row[0];

                            if ($elementType == '@') {
                                /*** SSO ***/
                                $SSO_Arr = explode('@', $formula_row);
                                $ssoCategories .= ($ssoCategories == '') ? $SSO_Arr[1] : ',' . $SSO_Arr[1];

                            } else if ($elementType == '#') {
                                /*** Salary category ***/
                                $catArr = explode('#', $formula_row);
                                $salaryCategories .= ($salaryCategories == '') ? $catArr[1] : ',' . $catArr[1];

                            } else if ($elementType == '~') {
                                /*** Pay Group ***/
                                $SSO_Arr = explode('~', $formula_row);
                                $payGroupCategories .= ($payGroupCategories == '') ? $SSO_Arr[1] : ',' . $SSO_Arr[1];

                            }

                        }
                    }

                }


                echo "<br/>formulaID: " . trim($formulaID);
                echo "<br/>salaryCategories: $salaryCategories";
                echo "<br/>ssoCategories: $ssoCategories";
                echo "<br/>payGroupCategories: $payGroupCategories";

                echo "<br/><br/><br/><br/><br/>";

                $dataUp = [
                    'salaryCategories' => (trim($salaryCategories) == '') ? null : $salaryCategories,
                    'ssoCategories' => (trim($ssoCategories) == '') ? null : $ssoCategories,
                    'payGroupCategories' => (trim($payGroupCategories) == '') ? null : $payGroupCategories
                ];

                $this->db->where('formulaID =' . $formulaID);
                $this->db->update('srp_erp_paygroupformula', $dataUp);

            }
        }
    }

    function get_localization_report()
    {
        $this->form_validation->set_rules('segmentID[]', 'Segment', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->Template_paySheet_model->get_localization();
            $data["type"] = "html";
            echo $html = $this->load->view('system/hrm/ajax/load-localization-report', $data, true);
        }
    }

    function get_localization_report_pdf()
    {
        $data["details"] = $this->Template_paySheet_model->get_localization();
        $data["type"] = "pdf";
        $html = $this->load->view('system/hrm/ajax/load-localization-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    function get_salary_trend_report()
    {
        $this->form_validation->set_rules('year[]', 'Year', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->Template_paySheet_model->get_salary_trend();
            $data["type"] = "html";
            $data["months"] = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dece');
            echo $html = $this->load->view('system/hrm/ajax/load-salary-trend-report', $data, true);
        }
    }

    function get_salary_trend_report_pdf()
    {
        $data["details"] = $this->Template_paySheet_model->get_salary_trend();
        $data["type"] = "pdf";
        $data["months"] = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dece');
        $html = $this->load->view('system/hrm/ajax/load-salary-trend-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    function payGroup_temporary_calculation()
    {
        $id = trim($this->uri->segment(3));

        if (empty($id)) {
            die('Payroll id is not valid');
        }

        $this->db->trans_start();

        $where = [
            'companyID' => current_companyID(),
            'payrollMasterID' => $id
        ];

        $this->db->delete('srp_erp_payrolldetailpaygroup', $where);

        $this->Template_paySheet_model->payGroup_temporary_calculation($id, 'N', '');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo 'Updated successfully.';
        } else {
            $this->db->trans_rollback();
            echo 'Error in process.';
        }
    }

    function dropdown_payslipemployees_his_report()
    {
        $segment = $this->input->post('segmentID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $companyID = current_companyID();
        //$seg = explode('|', $segment);
        if(empty($segment)){
            $html = '<select name="empID[]" id="empID" class="form-control" multiple="multiple"  required>';

            $html .= '</select>';

            echo json_encode(['s', $html]);
        }else{
            $empArr = $this->db->query("SELECT
	EIdNo,
	ECode,
	Ename2
FROM
	srp_employeesdetails
WHERE
	 segmentID IN (".join(',',$segment).") AND Erp_companyID=$companyID AND isSystemAdmin=0 AND isDischarged != 1")->result_array();

            $html = '<select name="empID[]" id="empID" class="form-control" multiple="multiple"  required>';

            if ($empArr) {
                foreach ($empArr as $empID) {
                    $html .= '<option value="' . $empID['EIdNo'] . '">' . $empID['ECode'] . '|' . $empID['Ename2'] . '</option>';
                }
            }
            $html .= '</select>';

            echo json_encode(['s', $html]);
        }

    }


    function get_leave_history_report()
    {
        $this->form_validation->set_rules('date_from', 'Date From', 'required');
        $this->form_validation->set_rules('date_to', 'Date to', 'required');
        $this->form_validation->set_rules('empID[]', 'Employee', 'required');
        $this->form_validation->set_rules('leaveTypeID[]', 'Leave Type', 'required');
        if ($this->form_validation->run() == FALSE) {
            $msg = '<div class="alert alert-warning" role="alert" style="margin-top: 15px;">' . validation_errors() . ' </div>';
            die($msg);
        }else {
            $data["details"] = $this->Template_paySheet_model->get_leave_history_report();
            $data["type"] = "html";
            echo $html = $this->load->view('system/hrm/report/load-employee-leave-history-report', $data, true);
        }
    }

    function get_leave_history_report_pdf()
    {
        $data["details"] = $this->Template_paySheet_model->get_leave_history_report();
        $data["type"] = "pdf";
        $html = $this->load->view('system/hrm/report/load-employee-leave-history-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function load_payment_voucher(){
        $bankTransferID=$this->input->post('bankTransferID');

        $data['extra'] = $this->Template_paySheet_model->load_payment_voucher($bankTransferID);
        $html = $this->load->view('system/hrm/ajax/ajax-erp_load_payment_vouchers', $data, true);
        echo $html;
    }

    function update_payslipVisibleDate(){
        $this->form_validation->set_rules('payrollID', 'ID', 'trim|required');
        $this->form_validation->set_rules('isNonPayroll', 'Payroll Type', 'trim|required');
        $this->form_validation->set_rules('visibleDate', 'Visible Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $payrollID = $this->input->post('payrollID');
        $visibleDate = $this->input->post('visibleDate');
        $isNonPayroll = $this->input->post('isNonPayroll');

        $masterData = $this->Template_paySheet_model->getPayrollDetails($payrollID, $isNonPayroll);
        $payYear = $masterData['payrollYear'];
        $payMonth = $masterData['payrollMonth'];
        $companyID = current_companyID();

        $date_format_policy = date_format_policy();
        $visibleDate = input_format_date($visibleDate, $date_format_policy);
        $payrollFirstDate = date('Y-m-d', strtotime($payYear . '-' . $payMonth . '-01'));

        if ($payrollFirstDate > $visibleDate) {
            die(json_encode(['e', 'Payslip date can not be leaser than ' . convert_date_format($payrollFirstDate)]));
        }

        $updateData = [
            'visibleDate' => $visibleDate,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_user(),
            'modifiedDateTime' => current_date()
        ];

        $tableName = ($isNonPayroll != 'Y')?'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';

        $this->db->trans_start();

        $this->db->where('payrollMasterID', $payrollID)->where('companyID', $companyID)->update($tableName, $updateData);

        $this->db->trans_complete();

        if( $this->db->trans_status() === true ){
            $this->db->trans_complete();
            echo json_encode(['s', 'Payslip visible date updated']);
        }
        else{
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in payslip visible date update.']);
        }

    }

    function WPS(){
        $company_id = current_companyID();
        $bankTransID = $this->uri->segment(3);
        $bank_tr = $this->db->query("SELECT trMas.payrollMasterID, swiftCode, accountNo, transferDate, bankShortCode, payrollYear, payrollMonth
                                     FROM srp_erp_pay_banktransfermaster AS trMas
                                     JOIN srp_erp_payrollmaster AS payMas ON payMas.payrollMasterID = trMas.payrollMasterID
                                     JOIN srp_erp_chartofaccounts AS chMas ON chMas.GLAutoID = trMas.accountID
                                     WHERE bankTransferID = {$bankTransID}")->row_array();
        $payroll_id = $bank_tr['payrollMasterID'];

        $defaultSSOValues = get_defaultSSOSetup();
        if(empty($defaultSSOValues)){
            die( json_encode(['e', 'Social Insurance id is not configured']) );
        }

        $sso_id = $defaultSSOValues['sso_employee'];
        $basic_cat = $this->db->query("SELECT salaryCategoryID FROM srp_erp_pay_salarycategories WHERE companyID = {$company_id} AND is_basic = 1")->result_array();

        if(empty($basic_cat)){
            die( json_encode(['e', 'Basic salary not configured.']) );
        }

        if($this->input->get('isValidate') == 'Y'){
            die( json_encode(['s', '']) );
        }

        $basic_cat = array_column($basic_cat, 'salaryCategoryID');
        $basic_cats = implode(',', $basic_cat);

        $details_rec = $this->db->query("SELECT docIDType, documentNo, EIdNo, bnkTr.acc_holderName, bnkTr.swiftCode, bnkTr.accountNo, bnkTr.transactionAmount, 
                            basic_pay, other_add, other_ded, sso_amount, bnkTr.salaryTransferPer, pay_hed.transactionCurrencyDecimalPlaces AS dPlace
                            FROM srp_employeesdetails AS empTb
                            JOIN srp_erp_payrollheaderdetails AS pay_hed ON pay_hed.EmpID = empTb.EIdNo AND pay_hed.payrollMasterID = {$payroll_id}
                            JOIN srp_erp_pay_banktransfer AS bnkTr ON bnkTr.empID = pay_hed.EmpID
                            AND bnkTr.bankTransferID = {$bankTransID} AND bnkTr.payrollMasterID = {$payroll_id}
                            LEFT JOIN (
                                SELECT empID, transactionAmount AS sso_amount FROM srp_erp_payrolldetail AS payDet
                                WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB = 'PAY_GROUP' AND payDet.detailTBID = {$sso_id}
                            ) AS sso_det ON sso_det.empID = empTb.EIdNo
                            LEFT JOIN (
                                SELECT empID, SUM(transactionAmount) AS basic_pay FROM srp_erp_payrolldetail AS payDet
                                WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB = 'SD' AND payDet.salCatID IN ({$basic_cats})
                                GROUP BY empID
                            ) AS basic_pay ON basic_pay.empID = empTb.EIdNo
                            LEFT JOIN (
                                SELECT empID, SUM(amount) AS other_add FROM (
                                    SELECT empID, SUM(transactionAmount) AS amount FROM srp_erp_payrolldetail AS payDet
                                    WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB != 'PAY_GROUP'
                                    AND (payDet.salCatID NOT IN ({$basic_cats}) OR payDet.salCatID IS NULL)  
                                    GROUP BY empID, salCatID                                    
                                ) AS temp1 WHERE amount > 0 GROUP BY empID 
                            ) AS other_add_tb ON other_add_tb.empID = empTb.EIdNo
                            LEFT JOIN (
                                SELECT empID, SUM(amount) AS other_ded FROM (
                                    SELECT empID, SUM(transactionAmount) AS amount FROM srp_erp_payrolldetail AS payDet
                                    WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB != 'PAY_GROUP'
                                    AND (payDet.salCatID NOT IN ({$basic_cats}) OR payDet.salCatID IS NULL)  
                                    GROUP BY empID, salCatID
                                ) AS temp2 WHERE amount < 0 GROUP BY empID 
                            ) AS other_ded_tb ON other_ded_tb.empID = empTb.EIdNo                             
                            LEFT JOIN (
                                SELECT IF( MIN(systemTypeID) = 2, 'C', 'P') AS docIDType, t1.*  FROM (
                                    SELECT frm.PersonID AS empID, documentNo, mas.systemTypeID, issuedBy, issuedByText
                                    FROM srp_documentdescriptionforms AS frm
                                    JOIN srp_documentdescriptionmaster AS mas ON mas.DocDesID = frm.DocDesID
                                    WHERE frm.Erp_companyID = {$company_id} AND PersonType = 'E' AND frm.isActive = 1 AND mas.systemTypeID IN (2,4)
                                ) AS t1 GROUP BY empID
                            ) AS doc_tb ON doc_tb.empID = empTb.EIdNo
                            WHERE Erp_companyID = {$company_id}")->result_array();

        $numberOfRecords = count($details_rec);

        $employerCR = $this->db->query("SELECT registration_no FROM srp_erp_company WHERE company_id = {$company_id}")->row('registration_no');


        $year = $bank_tr['payrollYear']; $month = str_pad($bank_tr['payrollMonth'], 2, '0', STR_PAD_LEFT );
        $payerBankShortCode = $bank_tr['bankShortCode'];
        $payerCR = $employerCR; $monthAdd = []; $monthDed = []; $days = date('t', strtotime("{$year}-$month-01"));
        $submissionDate = date('Ymd', strtotime("{$year}-$month-01")); $submissionNo = 1;
        $csv_data = [
            [
                'Employee CR NO', 'Payer NO', 'Payer Bank Short Name', 'Payer Account Number', 'Salary Year', 'Salary Month', 'Total Salaries',
                'Number of Records', 'Payment Type'
            ],
            [
                $employerCR, $payerCR, $payerBankShortCode, $bank_tr['accountNo'], $year, $month, '', $numberOfRecords, 'Salary'
            ],
            [
                'Employee ID Type', 'Employee ID', 'Reference Number', 'Employee Name', 'Employee BIC', 'Employee Account',
                'Salary Frequency', 'Number of Working days', 'Net Salary', 'Basic Salary', 'Extra hours', 'Extra Income',
                'Deductions', 'Social Security Deductions', 'Notes Comments'
            ]
        ];

        $totalSalary = 0;
        $details_rec = array_group_by($details_rec, 'EIdNo');

        /*Get other bank transfer or pending for transfer details of this payroll to check split salary*/
        $other_bank_transfer_det = $this->db->query("SELECT EIdNo, bnkTr.salaryTransferPer FROM srp_employeesdetails AS empTb
                                        JOIN srp_erp_payrollheaderdetails AS pay_hed ON pay_hed.EmpID = empTb.EIdNo AND pay_hed.payrollMasterID = {$payroll_id}
                                        JOIN srp_erp_pay_banktransfer AS bnkTr ON bnkTr.empID = pay_hed.EmpID AND bnkTr.payrollMasterID = {$payroll_id} 
                                        AND (bnkTr.bankTransferID <> {$bankTransID} OR bnkTr.bankTransferID IS NULL)
                                        WHERE Erp_companyID = {$company_id}")->result_array();
        $other_bank_transfer_det = array_group_by($other_bank_transfer_det, 'EIdNo');


        $i = 3;
        foreach ($details_rec as $empID=>$empData){
            if(count($empData) == 1 && !array_key_exists($empID, $other_bank_transfer_det)){
                $row = $empData[0];
                $dPlace = $row['dPlace'];

                $thisNet = round($row['basic_pay'], $dPlace);
                $thisNet += round($row['other_add'], $dPlace);
                $thisNet += round($row['other_ded'], $dPlace);
                $thisNet += round($row['sso_amount'], $dPlace);

                $empID = $row['EIdNo'];

                $csv_data[$i][1] = $row['docIDType'];
                $csv_data[$i][2] =  $row['documentNo'];
                $csv_data[$i][3] = 0;
                $csv_data[$i][4] = $row['acc_holderName'];
                $csv_data[$i][5] = $row['swiftCode'];
                $csv_data[$i][6] = $row['accountNo'];
                $csv_data[$i][7] = 'M';
                $csv_data[$i][8] = $days;

                $csv_data[$i][9] = number_format($thisNet, $dPlace, '.', '');
                $csv_data[$i][10] = number_format($row['basic_pay'], $dPlace, '.', '');
                $csv_data[$i][11] = 0;
                $csv_data[$i][12] = number_format($row['other_add'], $dPlace, '.', '');
                $csv_data[$i][13] = number_format(abs($row['other_ded']), $dPlace, '.', '');
                $csv_data[$i][14] = number_format(abs($row['sso_amount']), $dPlace, '.', '');


                $des = '';
                if(array_key_exists($empID, $monthAdd)){
                    $thisDescription = $monthAdd[$empID];
                    $thisDescription = implode(', ', array_column($thisDescription, 'description'));
                    $des = 'MA :'.$thisDescription;
                }

                if(array_key_exists($empID, $monthDed)){
                    $thisDescription = $monthDed[$empID];
                    $thisDescription = implode(', ', array_column($thisDescription, 'description'));
                    $des .= ' MD :'.$thisDescription;
                }
                $des = str_replace(',', ' ', $des);
                $des = (empty($des))? ' ': $des;
                $csv_data[$i][15] = $des;

                $totalSalary += number_format($thisNet, $dPlace, '.', '');

                $i++;
            }
            else{
                $sp_per_tot = 0;

                foreach ($empData as $empSplit){
                    $sp_per_tot += $empSplit['salaryTransferPer'];
                }

                if(array_key_exists($empID, $other_bank_transfer_det)){
                    foreach ($other_bank_transfer_det[$empID] as $other_trans_split){
                        $sp_per_tot += $other_trans_split['salaryTransferPer'];
                    }
                }

                foreach ($empData as $row){
                    $dPlace = $row['dPlace'];
                    $this_per = $row['salaryTransferPer'];

                    $basic = ($this_per/$sp_per_tot) * $row['basic_pay'];
                    $other_add = ($this_per/$sp_per_tot) * $row['other_add'];
                    $other_ded = ($this_per/$sp_per_tot) * $row['other_ded'];
                    $sso_amount = ($this_per/$sp_per_tot) * $row['sso_amount'];

                    $empID = $row['EIdNo'];

                    $csv_data[$i][1] = $row['docIDType'];
                    $csv_data[$i][2] = $row['documentNo'];
                    $csv_data[$i][3] = 0;
                    $csv_data[$i][4] = $row['acc_holderName'];
                    $csv_data[$i][5] = $row['swiftCode'];
                    $csv_data[$i][6] = $row['accountNo'];
                    $csv_data[$i][7] = 'M';
                    $csv_data[$i][8] = $days;

                    $csv_data[$i][9] = number_format($row['transactionAmount'], $dPlace, '.', '');
                    $csv_data[$i][10] = number_format($basic, $dPlace, '.', '');
                    $csv_data[$i][11] = 0;
                    $csv_data[$i][12] = number_format($other_add, $dPlace, '.', '');
                    $csv_data[$i][13] = number_format(abs($other_ded), $dPlace, '.', '');
                    $csv_data[$i][14] = number_format(abs($sso_amount), $dPlace, '.', '');


                    $des = '';
                    if(array_key_exists($empID, $monthAdd)){
                        $thisDescription = $monthAdd[$empID];
                        $thisDescription = implode(', ', array_column($thisDescription, 'description'));
                        $des = 'MA :'.$thisDescription;
                    }

                    if(array_key_exists($empID, $monthDed)){
                        $thisDescription = $monthDed[$empID];
                        $thisDescription = implode(', ', array_column($thisDescription, 'description'));
                        $des .= ' MD :'.$thisDescription;
                    }
                    $des = str_replace(',', ' ', $des);
                    $des = (empty($des))? ' ': $des;
                    $csv_data[$i][15] = $des;
                    $i++;
                    $totalSalary += round($row['transactionAmount'], $dPlace);
                }

            }

        }

        //Set the total salary to the array
        $csv_data[1][6] = number_format($totalSalary, $dPlace, '.', '');

        $fileName = "SIF_{$employerCR}_{$payerBankShortCode}_{$submissionDate}_{$submissionNo}.csv";

        ob_start();
        ob_clean();
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename={$fileName}");
        foreach ($csv_data as $key=>$row){

            foreach ($row as $key2=>$row2){
                $row2 = preg_replace('/[^a-zA-Z0-9.\s]/', '', strip_tags(html_entity_decode($row2)));

                echo $row2;
                if($key < 2){
                    $count = 9;
                    if($count != ($key2+1)){
                        echo ",";
                    }
                }
                elseif($key == 2){
                    $count = 14;
                    if($count != ($key2)){
                        echo ",";
                    }
                }
                else{
                    $count = 15;
                    if($count != ($key2)){
                        echo ",";
                    }
                }
            }

            echo "\r\n";
        }
    }

    function WPS2(){
        $company_id = current_companyID();
        $bankTransID = $this->uri->segment(3);
        $bank_tr = $this->db->query("SELECT trMas.payrollMasterID, swiftCode, accountNo, transferDate, bankShortCode, payrollYear, payrollMonth
                                     FROM srp_erp_pay_banktransfermaster AS trMas
                                     JOIN srp_erp_payrollmaster AS payMas ON payMas.payrollMasterID = trMas.payrollMasterID
                                     JOIN srp_erp_chartofaccounts AS chMas ON chMas.GLAutoID = trMas.accountID
                                     WHERE bankTransferID = {$bankTransID}")->row_array();
        $payroll_id = $bank_tr['payrollMasterID'];

        $defaultSSOValues = get_defaultSSOSetup();
        if(empty($defaultSSOValues)){
            die( json_encode(['e', 'Social Insurance id is not configured']) );
        }

        $sso_id = $defaultSSOValues['sso_employee'];
        $basic_cat = $this->db->query("SELECT salaryCategoryID FROM srp_erp_pay_salarycategories WHERE companyID = {$company_id} AND is_basic = 1")->result_array();

        if(empty($basic_cat)){
            die( json_encode(['e', 'Basic salary not configured.']) );
        }

        if($this->input->get('isValidate') == 'Y'){
            die( json_encode(['s', '']) );
        }

        $basic_cat = array_column($basic_cat, 'salaryCategoryID');
        $basic_cats = implode(',', $basic_cat);

        $sortOrder_policy = getPolicyValues('PSO', 'All');

        $sortOrder = 'pay_hed.empID DESC';
        switch ($sortOrder_policy){
            case 1: $sortOrder = 'pay_hed.ECode ASC'; break;
            case 2: $sortOrder = 'pay_hed.ECode DESC'; break;
        }

        $details_rec = $this->db->query("SELECT '' AS REFNO, 'C' AS docIDType, documentNo, NIC as nicNo, EIdNo, bnkTr.acc_holderName, bnkTr.swiftCode, bnkTr.accountNo, bnkTr.transactionAmount, 
                            basic_pay, other_add, other_ded, sso_amount, bnkTr.salaryTransferPer, pay_hed.transactionCurrencyDecimalPlaces AS dPlace
                            FROM srp_employeesdetails AS empTb
                            JOIN srp_erp_payrollheaderdetails AS pay_hed ON pay_hed.EmpID = empTb.EIdNo AND pay_hed.payrollMasterID = {$payroll_id}
                            JOIN srp_erp_pay_banktransfer AS bnkTr ON bnkTr.empID = pay_hed.EmpID
                            AND bnkTr.bankTransferID = {$bankTransID} AND bnkTr.payrollMasterID = {$payroll_id}
                            LEFT JOIN (
                                SELECT empID, transactionAmount AS sso_amount FROM srp_erp_payrolldetail AS payDet
                                WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB = 'PAY_GROUP' AND payDet.detailTBID = {$sso_id}
                            ) AS sso_det ON sso_det.empID = empTb.EIdNo
                            LEFT JOIN (
                                SELECT empID, SUM(transactionAmount) AS basic_pay FROM srp_erp_payrolldetail AS payDet
                                WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB = 'SD' AND payDet.salCatID IN ({$basic_cats})
                                GROUP BY empID
                            ) AS basic_pay ON basic_pay.empID = empTb.EIdNo
                            LEFT JOIN (
                                SELECT empID, SUM(amount) AS other_add FROM (
                                    SELECT empID, SUM(transactionAmount) AS amount FROM srp_erp_payrolldetail AS payDet
                                    WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB != 'PAY_GROUP'
                                    AND (payDet.salCatID NOT IN ({$basic_cats}) OR payDet.salCatID IS NULL)  
                                    GROUP BY empID, salCatID                                    
                                ) AS temp1 WHERE amount > 0 GROUP BY empID 
                            ) AS other_add_tb ON other_add_tb.empID = empTb.EIdNo
                            LEFT JOIN (
                                SELECT empID, SUM(amount) AS other_ded FROM (
                                    SELECT empID, SUM(transactionAmount) AS amount FROM srp_erp_payrolldetail AS payDet
                                    WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB != 'PAY_GROUP'
                                    AND (payDet.salCatID NOT IN ({$basic_cats}) OR payDet.salCatID IS NULL)  
                                    GROUP BY empID, salCatID
                                ) AS temp2 WHERE amount < 0 GROUP BY empID 
                            ) AS other_ded_tb ON other_ded_tb.empID = empTb.EIdNo
                            LEFT JOIN (
                                SELECT IF( MIN(systemTypeID) = 2, 'C', 'P') AS docIDType, t1.*  FROM (
                                    SELECT frm.PersonID AS empID, documentNo, mas.systemTypeID, issuedBy, issuedByText
                                    FROM srp_documentdescriptionforms AS frm
                                    JOIN srp_documentdescriptionmaster AS mas ON mas.DocDesID = frm.DocDesID
                                    WHERE frm.Erp_companyID = {$company_id} AND PersonType = 'E' AND frm.isActive = 1 AND mas.systemTypeID IN (2,4)
                                ) AS t1 GROUP BY empID
                            ) AS doc_tb ON doc_tb.empID = empTb.EIdNo
                            WHERE Erp_companyID = {$company_id}
                            ORDER BY {$sortOrder}")->result_array();


        $year = $bank_tr['payrollYear'];
        $month = str_pad($bank_tr['payrollMonth'], 2, '0', STR_PAD_LEFT );


        $csv_data = [
            [
                'REF No', 'Employee ID Type', 'Employee ID',  'Employee Name', 'Bank Name', 'Account No',
                'Salary Frequency', 'No. of Working days', 'Extra hours', 'Basic Pay', 'Extra Income',
                'Deductions', 'PASI', 'Net Pay', 'Salary Month'
            ]
        ];


        $totalSalary = 0;
        $details_rec = array_group_by($details_rec, 'EIdNo');

        /*Get other bank transfer or pending for transfer details of this payroll to check split salary*/
        $other_bank_transfer_det = $this->db->query("SELECT EIdNo, bnkTr.salaryTransferPer FROM srp_employeesdetails AS empTb
                                        JOIN srp_erp_payrollheaderdetails AS pay_hed ON pay_hed.EmpID = empTb.EIdNo AND pay_hed.payrollMasterID = {$payroll_id}
                                        JOIN srp_erp_pay_banktransfer AS bnkTr ON bnkTr.empID = pay_hed.EmpID AND bnkTr.payrollMasterID = {$payroll_id} 
                                        AND (bnkTr.bankTransferID <> {$bankTransID} OR bnkTr.bankTransferID IS NULL)
                                        WHERE Erp_companyID = {$company_id}")->result_array();
        $other_bank_transfer_det = array_group_by($other_bank_transfer_det, 'EIdNo');


        $i = 3;
        foreach ($details_rec as $empID=>$empData){
            if(count($empData) == 1 && !array_key_exists($empID, $other_bank_transfer_det)){
                $row = $empData[0];
                $dPlace = $row['dPlace'];

                $thisNet = round($row['basic_pay'], $dPlace);
                $thisNet += round($row['other_add'], $dPlace);
                $thisNet += round($row['other_ded'], $dPlace);
                $thisNet += round($row['sso_amount'], $dPlace);

                $csv_data[$i][1] = $row['REFNO'];
                $csv_data[$i][2] = $row['docIDType'];
                $csv_data[$i][3] = $row['nicNo'];
                $csv_data[$i][4] = $row['acc_holderName'];
                $csv_data[$i][5] = $row['swiftCode'];
                $csv_data[$i][6] = $row['accountNo'];
                $csv_data[$i][7] = 'M';
                $csv_data[$i][8] = 30;
                $csv_data[$i][9] = 0;
                $csv_data[$i][10] = number_format($row['basic_pay'], $dPlace, '.', '');
                $csv_data[$i][11] = number_format($row['other_add'], $dPlace, '.', '');
                $csv_data[$i][12] = number_format(abs($row['other_ded']), $dPlace, '.', '');
                $csv_data[$i][13] = number_format(abs($row['sso_amount']), $dPlace, '.', '');
                $csv_data[$i][14] = number_format($thisNet, $dPlace, '.', '');
                $csv_data[$i][15] = $month;

                $i++;
            }
            else{
                $sp_per_tot = 0;

                foreach ($empData as $empSplit){
                    $sp_per_tot += $empSplit['salaryTransferPer'];
                }

                if(array_key_exists($empID, $other_bank_transfer_det)){
                    foreach ($other_bank_transfer_det[$empID] as $other_trans_split){
                        $sp_per_tot += $other_trans_split['salaryTransferPer'];
                    }
                }

                foreach ($empData as $row){
                    $dPlace = $row['dPlace'];
                    $this_per = $row['salaryTransferPer'];

                    $basic = ($this_per/$sp_per_tot) * $row['basic_pay'];
                    $other_add = ($this_per/$sp_per_tot) * $row['other_add'];
                    $other_ded = ($this_per/$sp_per_tot) * $row['other_ded'];
                    $sso_amount = ($this_per/$sp_per_tot) * $row['sso_amount'];

                    $csv_data[$i][1] = $row['REFNO'];
                    $csv_data[$i][2] = $row['docIDType'];
                    $csv_data[$i][3] = $row['nicNo'];
                    $csv_data[$i][4] = $row['acc_holderName'];
                    $csv_data[$i][5] = $row['swiftCode'];
                    $csv_data[$i][6] = $row['accountNo'];
                    $csv_data[$i][7] = 'M';
                    $csv_data[$i][8] = 30;
                    $csv_data[$i][9] = 0;
                    $csv_data[$i][10] = number_format($basic, $dPlace, '.', '');
                    $csv_data[$i][11] = number_format($other_add, $dPlace, '.', '');
                    $csv_data[$i][12] = number_format(abs($other_ded), $dPlace, '.', '');
                    $csv_data[$i][13] = number_format(abs($sso_amount), $dPlace, '.', '');
                    $csv_data[$i][14] = number_format($row['transactionAmount'], $dPlace, '.', '');
                    $csv_data[$i][15] = $month;

                    $i++;
                    $totalSalary += round($row['transactionAmount'], $dPlace);
                }

            }

        }

        //echo '<pre>'; print_r($csv_data); echo '</pre>';        die();
        $fileName = "Bank Transfer {$year}-{$month}.csv";

        ob_start();
        ob_clean();
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename={$fileName}");
        foreach ($csv_data as $key=>$row){

            foreach ($row as $key2=>$row2){
                $row2 = preg_replace('/[^a-zA-Z0-9.\s]/', '', strip_tags(html_entity_decode($row2)));

                echo $row2;
                if($key == 2){
                    $count = 14;
                    if($count != ($key2)){
                        echo ",";
                    }
                }
                else{
                    $count = 15;
                    if($count != ($key2)){
                        echo ",";
                    }
                }
            }

            echo "\r\n";
        }
    }

    function WPS_MOL(){
        $company_id = current_companyID();
        $bankTransID = $this->uri->segment(3);
        $bank_tr = $this->db->query("SELECT trMas.payrollMasterID, swiftCode, accountNo, transferDate, bankShortCode, payrollYear, payrollMonth
                                     FROM srp_erp_pay_banktransfermaster AS trMas
                                     JOIN srp_erp_payrollmaster AS payMas ON payMas.payrollMasterID = trMas.payrollMasterID
                                     JOIN srp_erp_chartofaccounts AS chMas ON chMas.GLAutoID = trMas.accountID
                                     WHERE bankTransferID = {$bankTransID}")->row_array();
        $payroll_id = $bank_tr['payrollMasterID'];

        $defaultSSOValues = get_defaultSSOSetup();
        // if(empty($defaultSSOValues)){
        //     die( json_encode(['e', 'Social Insurance id is not configured']) );
        // }

        $sso_id = isset($defaultSSOValues['sso_employee'])  ? $defaultSSOValues['sso_employee'] : 0;
        $basic_cat = $this->db->query("SELECT salaryCategoryID FROM srp_erp_pay_salarycategories WHERE companyID = {$company_id} AND is_basic = 1")->result_array();

        if(empty($basic_cat)){
            die( json_encode(['e', 'Basic salary not configured.']) );
        }

        if($this->input->get('isValidate') == 'Y'){
            die( json_encode(['s', '']) );
        }

        $basic_cat = array_column($basic_cat, 'salaryCategoryID');
        $basic_cats = implode(',', $basic_cat);

        $sortOrder_policy = getPolicyValues('PSO', 'All');

        $sortOrder = 'pay_hed.empID DESC';
        switch ($sortOrder_policy){
            case 1: $sortOrder = 'pay_hed.ECode ASC'; break;
            case 2: $sortOrder = 'pay_hed.ECode DESC'; break;
        }

        $details_rec = $this->db->query("SELECT '' AS REFNO, 'C' AS docIDType, documentNo, NIC as nicNo, EIdNo, bnkTr.acc_holderName, bnkTr.swiftCode, bnkTr.accountNo, bnkTr.transactionAmount, 
                            basic_pay, other_add, other_ded, sso_amount, bnkTr.salaryTransferPer, pay_hed.transactionCurrencyDecimalPlaces AS dPlace
                            FROM srp_employeesdetails AS empTb
                            JOIN srp_erp_payrollheaderdetails AS pay_hed ON pay_hed.EmpID = empTb.EIdNo AND pay_hed.payrollMasterID = {$payroll_id}
                            JOIN srp_erp_pay_banktransfer AS bnkTr ON bnkTr.empID = pay_hed.EmpID
                            AND bnkTr.bankTransferID = {$bankTransID} AND bnkTr.payrollMasterID = {$payroll_id}
                            LEFT JOIN (
                                SELECT empID, transactionAmount AS sso_amount FROM srp_erp_payrolldetail AS payDet
                                WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB = 'PAY_GROUP' AND payDet.detailTBID = {$sso_id}
                            ) AS sso_det ON sso_det.empID = empTb.EIdNo
                            LEFT JOIN (
                                SELECT empID, SUM(transactionAmount) AS basic_pay FROM srp_erp_payrolldetail AS payDet
                                WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB = 'SD' AND payDet.salCatID IN ({$basic_cats})
                                GROUP BY empID
                            ) AS basic_pay ON basic_pay.empID = empTb.EIdNo
                            LEFT JOIN (
                                SELECT empID, SUM(amount) AS other_add FROM (
                                    SELECT empID, SUM(transactionAmount) AS amount FROM srp_erp_payrolldetail AS payDet
                                    WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB != 'PAY_GROUP'
                                    AND (payDet.salCatID NOT IN ({$basic_cats}) OR payDet.salCatID IS NULL)  
                                    GROUP BY empID, salCatID                                    
                                ) AS temp1 WHERE amount > 0 GROUP BY empID 
                            ) AS other_add_tb ON other_add_tb.empID = empTb.EIdNo
                            LEFT JOIN (
                                SELECT empID, SUM(amount) AS other_ded FROM (
                                    SELECT empID, SUM(transactionAmount) AS amount FROM srp_erp_payrolldetail AS payDet
                                    WHERE payDet.payrollMasterID = {$payroll_id} AND payDet.fromTB != 'PAY_GROUP'
                                    AND (payDet.salCatID NOT IN ({$basic_cats}) OR payDet.salCatID IS NULL)  
                                    GROUP BY empID, salCatID
                                ) AS temp2 WHERE amount < 0 GROUP BY empID 
                            ) AS other_ded_tb ON other_ded_tb.empID = empTb.EIdNo
                            LEFT JOIN (
                                SELECT IF( MIN(systemTypeID) = 2, 'C', 'P') AS docIDType, t1.*  FROM (
                                    SELECT frm.PersonID AS empID, documentNo, mas.systemTypeID, issuedBy, issuedByText
                                    FROM srp_documentdescriptionforms AS frm
                                    JOIN srp_documentdescriptionmaster AS mas ON mas.DocDesID = frm.DocDesID
                                    WHERE frm.Erp_companyID = {$company_id} AND PersonType = 'E' AND frm.isActive = 1 AND mas.DocDescription = 'MOL ID'
                                ) AS t1 GROUP BY empID
                            ) AS doc_tb ON doc_tb.empID = empTb.EIdNo
                            WHERE Erp_companyID = {$company_id}
                            ORDER BY {$sortOrder}")->result_array();

        //AND mas.systemTypeID IN (6,2,4)
        $year = $bank_tr['payrollYear'];
        $month = str_pad($bank_tr['payrollMonth'], 2, '0', STR_PAD_LEFT );


        $csv_data = [
            [
                'STAFFID', 'FIXEDSALARY', 'VARIABLESALARY',  'LEAVENO'
            ]
        ];


        $totalSalary = 0;
        $details_rec = array_group_by($details_rec, 'EIdNo');

        /*Get other bank transfer or pending for transfer details of this payroll to check split salary*/
        $other_bank_transfer_det = $this->db->query("SELECT EIdNo, bnkTr.salaryTransferPer FROM srp_employeesdetails AS empTb
                                        JOIN srp_erp_payrollheaderdetails AS pay_hed ON pay_hed.EmpID = empTb.EIdNo AND pay_hed.payrollMasterID = {$payroll_id}
                                        JOIN srp_erp_pay_banktransfer AS bnkTr ON bnkTr.empID = pay_hed.EmpID AND bnkTr.payrollMasterID = {$payroll_id} 
                                        AND (bnkTr.bankTransferID <> {$bankTransID} OR bnkTr.bankTransferID IS NULL)
                                        WHERE Erp_companyID = {$company_id}")->result_array();
        $other_bank_transfer_det = array_group_by($other_bank_transfer_det, 'EIdNo');


        $i = 3;
        foreach ($details_rec as $empID=>$empData){
            if(count($empData) == 1 && !array_key_exists($empID, $other_bank_transfer_det)){
                $row = $empData[0];
                $dPlace = $row['dPlace'];

                $thisNet = round($row['basic_pay'], $dPlace);
                $thisNet += round($row['other_add'], $dPlace);
                $thisNet += round($row['other_ded'], $dPlace);
                $thisNet += round($row['sso_amount'], $dPlace);

                $csv_data[$i][1] = $row['documentNo'];
                $csv_data[$i][2] = number_format($row['basic_pay'], $dPlace, '.', '');
                $csv_data[$i][3] = number_format($row['other_add'], $dPlace, '.', '');
                $csv_data[$i][4] = 0;
                // $csv_data[$i][5] = $row['swiftCode'];
                // $csv_data[$i][6] = $row['accountNo'];
                // $csv_data[$i][7] = 'M';
                // $csv_data[$i][8] = 30;
                // $csv_data[$i][9] = 0;
                // $csv_data[$i][10] = number_format($row['basic_pay'], $dPlace, '.', '');
                // $csv_data[$i][11] = number_format($row['other_add'], $dPlace, '.', '');
                // $csv_data[$i][12] = number_format(abs($row['other_ded']), $dPlace, '.', '');
                // $csv_data[$i][13] = number_format(abs($row['sso_amount']), $dPlace, '.', '');
                // $csv_data[$i][14] = number_format($thisNet, $dPlace, '.', '');
                // $csv_data[$i][15] = $month;

                $i++;
            }
            else{
                $sp_per_tot = 0;

                foreach ($empData as $empSplit){
                    $sp_per_tot += $empSplit['salaryTransferPer'];
                }

                if(array_key_exists($empID, $other_bank_transfer_det)){
                    foreach ($other_bank_transfer_det[$empID] as $other_trans_split){
                        $sp_per_tot += $other_trans_split['salaryTransferPer'];
                    }
                }

                foreach ($empData as $row){
                    $dPlace = $row['dPlace'];
                    $this_per = $row['salaryTransferPer'];

                    $basic = ($this_per/$sp_per_tot) * $row['basic_pay'];
                    $other_add = ($this_per/$sp_per_tot) * $row['other_add'];
                    $other_ded = ($this_per/$sp_per_tot) * $row['other_ded'];
                    $sso_amount = ($this_per/$sp_per_tot) * $row['sso_amount'];

                    $csv_data[$i][1] = $row['REFNO'];
                    $csv_data[$i][2] = $row['docIDType'];
                    $csv_data[$i][3] = $row['nicNo'];
                    $csv_data[$i][4] = $row['acc_holderName'];
                    $csv_data[$i][5] = $row['swiftCode'];
                    $csv_data[$i][6] = $row['accountNo'];
                    $csv_data[$i][7] = 'M';
                    $csv_data[$i][8] = 30;
                    $csv_data[$i][9] = 0;
                    $csv_data[$i][10] = number_format($basic, $dPlace, '.', '');
                    $csv_data[$i][11] = number_format($other_add, $dPlace, '.', '');
                    $csv_data[$i][12] = number_format(abs($other_ded), $dPlace, '.', '');
                    $csv_data[$i][13] = number_format(abs($sso_amount), $dPlace, '.', '');
                    $csv_data[$i][14] = number_format($row['transactionAmount'], $dPlace, '.', '');
                    $csv_data[$i][15] = $month;

                    $i++;
                    $totalSalary += round($row['transactionAmount'], $dPlace);
                }

            }

        }

        //echo '<pre>'; print_r($csv_data); echo '</pre>';        die();
        $fileName = "Bank Transfer {$year}-{$month}.csv";

        ob_start();
        ob_clean();
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename={$fileName}");
        foreach ($csv_data as $key=>$row){

            foreach ($row as $key2=>$row2){
                $row2 = preg_replace('/[^a-zA-Z0-9.\s]/', '', strip_tags(html_entity_decode($row2)));

                echo $row2;
                if($key == 2){
                    $count = 14;
                    if($count != ($key2)){
                        echo ",";
                    }
                }
                else{
                    $count = 15;
                    if($count != ($key2)){
                        echo ",";
                    }
                }
            }

            echo "\r\n";
        }
    }

    function get_paySlip_report_sponsership()
    {
        $payrollMonth = trim($this->input->post('payrollMonth') ?? '');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $segment = $this->input->post('segmentID');
        $empID = $this->input->post('empID');
        $locationID = $this->input->post('locationID');
        $sponsorID = $this->input->post('sponserID');
        $locationIDfilter = '';
        $sponsorfilter = '';
        if($locationID)
        {
            $locationIDfilter .=  ' ANd srp_employeesdetails.floorID = '.$locationID.' ';
        }
        if($sponsorID)
        {
            $sponsorfilter .=  'ANd sponser.sponsorID = '.$sponsorID.'';
        }


        $filter_payroll = $filter = "";
        $Pleasesselectatleastoneemployeetoproceed = $this->lang->line('common_please_select_at_least_one_employee_to_proceed');
        if ($empID == '') {
            echo '<div class="col-md-12 bg-border" style="">
                   <div class="row">
                        <div class="col-md-12 xxcol-md-offset-2">
                            <div class="alert alert-warning" role="alert">
                                <p>' . $Pleasesselectatleastoneemployeetoproceed . '<!--Please select at least one employee to proceed--></p>
                            </div>
                        </div>
                    </div>
                   </div>';
            die();
        } else {
            $commaList = implode(', ', $empID);
            $filter .= " AND empID IN({$commaList})";
        }


        $companyID = current_companyID();
        $pYear = date('Y', strtotime($payrollMonth));
        $pMonth = date('m', strtotime($payrollMonth));

        if ($segment != '') {
            $segmentID = explode('|', $segment);
            $filter_payroll .= " AND segmentID={$segmentID[0]}";
        }

        $masterTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        $headerTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';
        $detailTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrolldetail' : 'srp_erp_non_payrolldetail';


        $payrollID_arr = $this->db->query("SELECT payrollMasterID FROM {$headerTB} AS t1
                                           JOIN (
                                              SELECT payrollMasterID AS payID FROM {$masterTB} WHERE companyID={$companyID}
                                              AND payrollYear={$pYear} AND payrollMonth={$pMonth} AND approvedYN=1
                                           ) AS payrollMaster ON payrollMaster.payID = t1.payrollMasterID
                                           WHERE companyID={$companyID} {$filter_payroll} GROUP BY payrollMasterID")->result_array();

        if (empty($payrollID_arr)) {
            echo '<div class="col-md-12 bg-border" style="">
                       <div class="row">
                            <div class="col-md-12 xxcol-md-offset-2">
                                <div class="alert alert-warning" role="alert">
                                    <p>Please select at least one employee to proceed</p>
                                </div>
                            </div>
                       </div>
                  </div>';
            die();
        }

        $payrollID_arr = implode(',', array_column($payrollID_arr, 'payrollMasterID'));

        $categorysalary = $this->db->query("SELECT salaryCategoryType,salaryCategoryID,salaryDescription,deductionPercntage,companyContributionPercentage
                                      FROM srp_erp_pay_salarycategories 
                                      WHERE companyID='{$companyID}' AND isPayrollCategory=1 ORDER BY salaryCategoryType ASC")->result_array();

        $querycat = '';
        if ($categorysalary) {
            foreach ($categorysalary as $cat) {
                $salaryDescription = str_replace(' ', '', $cat['salaryDescription']);
                $salaryDescription = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription);

                $querycat .= "SUM(IF(srp_erp_payrolldetail.salCatID = " . $cat['salaryCategoryID'] . " , transactionAmount, 0)) as " . $salaryDescription . ",";

            }
        }


        $sql = "SELECT {$masterTB}.payrollMasterID, EIdNo, ECode, EmpDesignationId, Ename1, Ename2, Ename3, Ename4, EmpShortCode,
              srp_employeesdetails.segmentID, CONCAT('Currency : ',transactionCurrency,' : Sponsor Name : ',IFNULL(sponsorName,' - ' ),' : Location Name : ',IFNULL(floorDescription,' - ' ))  curr, transactionCurrencyDecimalPlaces,detailType, $querycat salCatID
              ,transactionCurrency,floorDescription,sponsorName
              FROM {$masterTB}
              LEFT JOIN {$detailTB} ON {$masterTB}.payrollMasterID = {$detailTB}.payrollMasterID
              LEFT JOIN srp_employeesdetails ON empID = EidNo
            LEFT JOIN srp_erp_pay_salarycategories on srp_erp_pay_salarycategories.salaryCategoryID = srp_erp_payrolldetail.salCatID
            LEFT JOIN srp_erp_sponsormaster sponser  on sponser.sponsorID = srp_employeesdetails.sponsorID
	            LEFT JOIN srp_erp_pay_floormaster location on location.floorID = srp_employeesdetails.floorID
              WHERE approvedYN = 1 AND {$masterTB}.payrollMasterID IN ({$payrollID_arr}) {$filter} AND {$masterTB}.companyID = '{$companyID}'
              AND NOT EXISTS (
                  SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                  JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID='{$companyID}'
                  WHERE {$detailTB}.detailTBID = groupMaster.payGroupID AND {$detailTB}.fromTB='PAY_GROUP'
                  AND groupMaster.companyID='{$companyID}' AND SSO_TB.employerContribution > 0
              ) and {$detailTB}.payrollMasterID IN ({$payrollID_arr}) AND
              {$detailTB}.empID=srp_employeesdetails.EIdNo $locationIDfilter $sponsorfilter
              GROUP BY EIdNo, srp_employeesdetails.floorID, sponser.sponsorID, curr";


        $sql2 = "SELECT CONCAT('Currency : ',transactionCurrency,' : Sponsor Name : ',IFNULL(sponsorName,' - ' ),' : Location Name : ',IFNULL(floorDescription,' - ' ))  currency, $querycat transactionCurrencyDecimalPlaces
            
               FROM {$masterTB}
               LEFT JOIN {$detailTB} ON {$masterTB}.payrollMasterID = {$detailTB}.payrollMasterID
               LEFT JOIN srp_employeesdetails ON empID = EidNo
               	LEFT JOIN srp_erp_pay_salarycategories on srp_erp_pay_salarycategories.salaryCategoryID = srp_erp_payrolldetail.salCatID
               	LEFT JOIN srp_erp_sponsormaster sponser  on sponser.sponsorID = srp_employeesdetails.sponsorID
	            LEFT JOIN srp_erp_pay_floormaster location on location.floorID = srp_employeesdetails.floorID
               WHERE approvedYN = 1 AND {$masterTB}.payrollMasterID IN ({$payrollID_arr}) {$filter} AND {$masterTB}.companyID = '{$companyID}'
               AND NOT EXISTS (
                   SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                   JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID='{$companyID}'
                   WHERE {$detailTB}.detailTBID = groupMaster.payGroupID AND {$detailTB}.fromTB='PAY_GROUP' AND
                   groupMaster.companyID='{$companyID}' AND SSO_TB.employerContribution > 0
               ) and {$detailTB}.payrollMasterID IN ({$payrollID_arr})
               AND {$detailTB}.empID=srp_employeesdetails.EIdNo $locationIDfilter $sponsorfilter
             GROUP BY currency, srp_employeesdetails.floorID, sponser.sponsorID";
       // DIE($sql2);

        $data['detail'] = $this->db->query($sql)->result_array();
        $data['currency'] = $this->db->query($sql2)->result_array();
        $data['categorysalary'] = $categorysalary;

        /*  $this->load->view('system\hrm\pay_sheetTemplateDetails_view', $data);*/
        echo $this->load->view('system/hrm/ajax/load-employee-sponserwise-report', $data, true);
    }

    function salary_breakup_report(){
        $company_id = current_companyID();
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $reqType = $this->input->post('reqType');

        $fromDate = $tempDate = date('Y-m-d', strtotime("{$fromDate}-01"));
        $toDate = date('Y-m-d', strtotime("{$toDate}-01"));

        if($fromDate > $toDate){
            $msg = '<div class="alert alert-danger">From date should be less than to date.</div>';
            die($msg);
        }


        $details = $this->db->query("SELECT SUM(transactionAmount) AS amount, CONCAT_WS('-', payDate, sponsorID, salCatID) AS am_key
                                        FROM srp_erp_payrolldetail AS det_tb
                                        JOIN (
                                            SELECT payrollMasterID, DATE( CONCAT_WS('-', payrollYear, payrollMonth,'01') ) AS payDate 
                                            FROM  srp_erp_payrollmaster WHERE companyID = {$company_id}
                                            HAVING payDate BETWEEN '{$fromDate}' AND '{$toDate}'
                                        )  AS  mas_tb ON mas_tb.payrollMasterID = det_tb.payrollMasterID
                                        JOIN srp_employeesdetails AS emp_tb ON emp_tb .EIdNo = det_tb.empID AND Erp_companyID = {$company_id}
                                        WHERE companyID = {$company_id}
                                        GROUP BY payDate, salCatID, emp_tb.sponsorID")->result_array();

        if(empty($details)){
            $msg = '<div class="alert alert-danger">No data found.</div>';
            die($msg);
        }

        $s_cats = $this->db->query("SELECT salaryCategoryID, salaryDescription FROM srp_erp_pay_salarycategories 
                                        WHERE companyID = {$company_id} #AND salaryCategoryID IN (1,2,5)")->result_array();

        $sponsor = $this->db->query("SELECT sponsorID, sponsorName FROM srp_erp_sponsormaster 
                                         WHERE companyID = {$company_id} #LIMIT 3")->result_array();

        $period_arr[] = $fromDate;
        while($tempDate < $toDate){
            $tempDate = date('Y-m-d', strtotime("{$tempDate} +1 month"));
            $period_arr[] = $tempDate;
        }

        $sum_det = $this->db->query("SELECT SUM(transactionAmount) AS amount, CONCAT_WS('-', payDate, salCatID) AS am_key
                                        FROM srp_erp_payrolldetail AS det_tb
                                        JOIN (
                                            SELECT payrollMasterID, DATE( CONCAT_WS('-', payrollYear, payrollMonth,'01') ) AS payDate 
                                            FROM  srp_erp_payrollmaster WHERE companyID = {$company_id}
                                            HAVING payDate BETWEEN '{$fromDate}' AND '{$toDate}'
                                        )  AS  mas_tb ON mas_tb.payrollMasterID = det_tb.payrollMasterID
                                        JOIN srp_employeesdetails AS emp_tb ON emp_tb .EIdNo = det_tb.empID AND Erp_companyID = {$company_id}
                                        WHERE companyID = {$company_id}
                                        GROUP BY payDate, salCatID")->result_array();

        $data['period_arr'] = $period_arr;
        $data['sponsor'] = $sponsor;
        $data['details'] = $details;
        $data['sum_det'] = $sum_det;
        $data['s_cats'] = $s_cats;

        if($reqType == 'v'){
            echo $this->load->view('system/hrm/report/ajax/salary-breakup-report-view', $data,true);
        }
        else{
            $data['fromDate'] = $fromDate;
            $data['toDate'] = $toDate;
            $this->salary_breakup_report_excel($data);
        }
    }

    function salary_breakup_report_excel($data){
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);

        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Cost Break up');

        $period_arr = $data['period_arr'];
        $dPlace = 3;

        $header = ['Contractor'];

        $full_tot = []; $summery_tot = [];
        foreach ($period_arr as $key=>$row){
            $full_tot[$row] = 0;
            $summery_tot[$row] = 0;
            $header[] = date('Y - M', strtotime($row));
        }
        $header[] = 'Total YTD';


        $details = $data['details'];
        $sponsor = $data['sponsor'];
        $s_cats = $data['s_cats'];

        $this->excel->getActiveSheet()->fromArray($header, null, 'A1');

        $count = count($header) - 1;
        $letter_arr = range('A', 'Z');
        $letter = $letter_arr[$count];

        $this->excel->getActiveSheet()->getStyle("A1:{$letter}1")->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle("A1:{$letter}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('778899');

        $amount_array = array_column($details, 'am_key');
        $det = [];

        foreach ($sponsor as $s_row){ // sponsor body
            $sponsor_id = $s_row['sponsorID'];
            $det[] = [$s_row['sponsorName']];

            $n = count($det)+1;
            $this->excel->getActiveSheet()->getStyle("A{$n}:{$letter}{$n}")->getFont()->setBold(true)->setSize(11)->setName('Calibri');
            $this->excel->getActiveSheet()->getStyle("A{$n}:{$letter}{$n}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('87CEFA');

            foreach ($s_cats as $cat){
                $n++;
                $cat_id = $cat['salaryCategoryID'];
                $temp = [];
                $temp[] = $cat['salaryDescription'];


                $tot_amount = 0;
                foreach ($period_arr as $key=>$row){
                    $search_key = "{$row}-{$sponsor_id}-{$cat_id}";
                    $am_key = array_search($search_key, $amount_array);
                    $amount = ($am_key !== false)? $details[$am_key]['amount'] : 0;
                    $tot_amount += $amount;

                    $full_tot[$row] = ($full_tot[$row] + $amount);
                    $temp[] = round($amount, $dPlace);
                }

                $temp[] = round($tot_amount, $dPlace);

                $det[] = $temp;
            }
        }

        $n++;
        $temp = [];
        $temp[] = '';
        $tot_amount = 0;
        foreach ($period_arr as $key=>$row){ // contractor total
            $temp[] = round($full_tot[$row], $dPlace);
        }
        $temp[] = round($tot_amount, $dPlace);
        $det[] = $temp;

        $this->excel->getActiveSheet()->getStyle("A{$n}:{$letter}{$n}")->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle("A{$n}:{$letter}{$n}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('778899');

        $det[] = ['', ''];
        $det[] = ['', ''];

        $header[0] = 'Summary Cost';
        $det[] = $header; // Summary cost header
        $sum_det = $data['sum_det'];
        $amount_array = array_column($sum_det, 'am_key');
        $n = $n+3;

        $this->excel->getActiveSheet()->getStyle("A{$n}:{$letter}{$n}")->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle("A{$n}:{$letter}{$n}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('778899');

        foreach ($s_cats as $cat){ // summary cost body
            $n++;
            $temp = [];
            $temp[] = $cat['salaryDescription'];

            $cat_id = $cat['salaryCategoryID'];
            $tot_amount = 0;
            foreach ($period_arr as $key=>$row){
                $search_key = "{$row}-{$cat_id}";
                $am_key = array_search($search_key, $amount_array);
                $amount = ($am_key !== false)? $sum_det[$am_key]['amount'] : 0;
                $tot_amount += $amount;

                $summery_tot[$row] = ($summery_tot[$row] + $amount);

                $temp[] = round($amount, $dPlace);
            }

            $temp[] = round($tot_amount, $dPlace);

            $det[] = $temp;
        }

        $temp = [];
        $temp[] = '';
        foreach ($period_arr as $key=>$row){ // summary cost total / footer
            $temp[] = round($summery_tot[$row], $dPlace);
        }
        $temp[] = round($summery_tot[$row], $dPlace);
        $det[] = $temp;

        $n++;
        $this->excel->getActiveSheet()->getStyle("A{$n}:{$letter}{$n}")->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle("A{$n}:{$letter}{$n}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('778899');

        $this->excel->getActiveSheet()->fromArray($det, null, 'A2');

        $format_decimal = '#,##0.000';
        $this->excel->getActiveSheet()->getStyle("B2:P{$n}")->getNumberFormat()->setFormatCode($format_decimal);

        $fromDate = date('Y-m', strtotime($data['fromDate']));
        $toDate = date('Y-m', strtotime($data['toDate']));
        $filename = "Salary breakup {$fromDate} to {$toDate}.xls";
        header('Content-Type: application/vnd.ms-excel;charset=utf-16');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }


    function get_onleave_history_report()
    {

        $this->form_validation->set_rules('current_date', 'As of Date', 'required');
        $this->form_validation->set_rules('empID_ol[]', 'Employee', 'required');
        $this->form_validation->set_rules('leaveTypeID_ol[]', 'Leave Type', 'required');
        if ($this->form_validation->run() == FALSE) {
            $msg = '<div class="alert alert-warning" role="alert" style="margin-top: 15px;">' . validation_errors() . ' </div>';
            die($msg);
        }else {
            $data["details"] = $this->Template_paySheet_model->get_onleave_history_report();
            $data["type"] = "html";
            echo $html = $this->load->view('system/hrm/report/load_employee_onleave_history_report', $data, true);
        }
    }

    function get_onleave_history_report_pdf()
    {
        $current_date = $this->input->post('current_date');
        $leaveTypeID = $this->input->post('leaveTypeID_ol');
        $segmentID = $this->input->post('segmentID_ol');
        $empID = $this->input->post('empID_ol');

        $data["details"] = $this->Template_paySheet_model->get_onleave_history_report();
        $data["type"] = "pdf";

        $html = $this->load->view('system/hrm/report/load_employee_onleave_history_report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function dropdown_payslipemployees_his_report_ol()
    {
        $segment = $this->input->post('segmentID');
        $companyID = current_companyID();

        $html = '<select name="empID_ol[]" id="empID_ol" class="form-control" multiple="multiple" required>';
        if(!empty($segment)){
            $segment = join(',',$segment);
            $empArr = $this->db->query("SELECT EIdNo, ECode, Ename2 FROM srp_employeesdetails
                        WHERE segmentID IN ({$segment}) AND Erp_companyID=$companyID AND isSystemAdmin=0 AND isDischarged != 1")->result_array();
            if ($empArr) {
                foreach ($empArr as $empID) {
                    $html .= '<option value="' . $empID['EIdNo'] . '">' . $empID['ECode'] . '|' . $empID['Ename2'] . '</option>';
                }
            }
        }

        $html .= '</select>';
        echo json_encode(['s', $html]);

    }

    function load_periods_drops(){
        $groupID = $this->input->post('groupID');
        $company_id = current_companyID();
        $convertFormat = convert_date_format_sql();

        $period = $this->db->query("SELECT id, CONCAT_WS(' | ', DATE_FORMAT(dateFrom,'{$convertFormat}'), DATE_FORMAT(dateTo,'{$convertFormat}')) AS datePr
                                    FROM srp_erp_hrperiodmaster AS prMas
                                    JOIN srp_erp_hrperiod AS prTB ON prMas.hrPeriodID=prTB.hrPeriodID
                                    WHERE prMas.isActive = 1 AND prMas.companyID = {$company_id} AND prTB.companyID = {$company_id}
                                    AND hrGroupID = {$groupID}")->result_array();

        /*$drop = '<select name="period_drop" id="period_drop" class="form-control select2" required>';
        foreach ($period as $item){
            $drop .= '<option value="' . $item['id'] . '">' . $item['dateFrom'] . '|' . $item['dateTo'] . '</option>';
        }
        $drop .= '</select>';*/

        echo json_encode(['s', 'drop'=>$period]);
    }

    function get_salary_process_report()
    {
        $groupBy = $this->input->post('groupBy');
        $this->form_validation->set_rules('payrollMonth', 'Payroll Month', 'required');
        $this->form_validation->set_rules('groupBy', 'Group By', 'required');
        if($groupBy == 1) {
            $this->form_validation->set_rules('segmentID[]', 'segment', 'required');
        } else if($groupBy == 2) {
            $this->form_validation->set_rules('departmentID[]', 'department', 'required');
        }
        if ($this->form_validation->run() == FALSE) {
            $msg = '<div class="alert alert-warning" role="alert" style="margin-top: 15px;">' . validation_errors() . ' </div>';
            die($msg);
        }else {
            $data['extra'] = $this->Template_paySheet_model->get_salary_process_report();
            $data['groupBy'] =  $this->input->post('groupBy');
            echo $this->load->view('system/hrm/report/ajax/load_salary_process_report', $data, true);
        }
    }
    /*  https://gearsjira.atlassian.net/browse/SME-2554*/
    public function save_empNonBankPay_new()
    {

        $empID = $this->input->post('hidden_empID');
        $payrollMasterID = $this->input->post('hidden_payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $payType = $this->input->post('payType');
        $empPayBank = $this->input->post('empPayBank');
        $chequeNo = $this->input->post('chequeNo');

        $this->form_validation->set_rules('hidden_payrollID', 'Payroll ID', 'trim|required|numeric');
        $this->form_validation->set_rules('hidden_empID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('payType', 'Payment Type', 'trim|required');
        $this->form_validation->set_rules('paymentDate', 'Payment Date', 'trim|required|date');
        $this->form_validation->set_rules('empPayBank', 'Bank', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $isAlreadyPaid = $this->Template_paySheet_model->get_EmpNonBankTransferDet_new($empID, $payrollMasterID, $isNonPayroll);
            if(empty($isAlreadyPaid))
            {
                $byChq_errorMsg = '';
                if ($payType == 'By Cheque') {
                    if (trim($empPayBank) == '') {
                        $byChq_errorMsg = '<p>Bank field is required</p>';
                    }
                    if (trim($chequeNo) == '') {
                        $byChq_errorMsg .= '<p>Cheque No field is required</p>';
                    }
                }

                if ($byChq_errorMsg != '') {
                    echo json_encode(array('e', $byChq_errorMsg));
                } else {
                    echo json_encode($this->Template_paySheet_model->save_empNonBankPay_new());
                }
            }else
            {
                $employees  = (join('<br>', array_column($isAlreadyPaid, 'empName')));
                echo json_encode(array('e', 'Already salary paid for these employees :<br>'.$employees));
            }


        }
    }

    function generatepaymentvoucher()
    {
        $this->form_validation->set_rules('empID', 'Employee', 'trim|required');
        $this->form_validation->set_rules('payrollID', 'Payroll ID', 'trim|required');
        $this->form_validation->set_rules('transactionAmount', 'Transaction Amount', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        }else {
            echo json_encode($this->Template_paySheet_model->generatepaymentvoucher());
        }
    }
    function send_payslipnotification()
    {
        $type = trim($this->input->post('type') ?? '');
        $this->form_validation->set_rules('isnonpayroll', 'Is Non Payroll', 'trim|required');
        $this->form_validation->set_rules('payrollID', 'Payroll ID', 'trim|required');
        if($type==1)
        {
            $this->form_validation->set_rules('bankTransID', 'Bank Transfer ID', 'trim|required');

        }else
        {
            $this->form_validation->set_rules('employeeID', 'Employee ID', 'trim|required');
        }



        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        }else {
            echo json_encode($this->Template_paySheet_model->send_payslipnotification());
        }
    }
    function fetch_bank()
    {
        $paytype = $this->input->post('paytype');
        $iscash = 0;
        if($paytype == 'By Cash')
        {
            $iscash = 1;
        }else {
            $iscash = 0;
        }
        $this->db->select("GLAutoID,bankName,bankBranch,bankSwiftCode,bankAccountNumber,subCategory,isCash");
        $this->db->from('srp_erp_chartofaccounts');
        $this->db->WHERE('controllAccountYN', 0);
        $this->db->WHERE('masterAccountYN', 0);
        $this->db->where('isBank', 1);
        $this->db->where('isActive', 1);
        $this->db->where('approvedYN', 1);
        $this->db->where('isCash', $iscash);
        $this->db->where('companyID', current_companyID());
        $bank = $this->db->get()->result_array();
        $bank_arr = array('' => 'Select Bank Account');
        if (isset($bank)) {
            foreach ($bank as $row) {
                $type = ($row['isCash'] == '1') ? ' | Cash' : ' | Bank';
                $bank_arr[trim($row['GLAutoID'] ?? '')] = trim($row['bankName'] ?? '') . ' | ' . trim($row['bankBranch'] ?? '') . ' | ' . trim($row['bankSwiftCode'] ?? '') . ' | ' . trim($row['bankAccountNumber'] ?? '') . ' | ' . trim($row['subCategory'] ?? '') . $type;
            }
            echo form_dropdown('empPayBank', $bank_arr, '', 'class="form-control select2 empPayBank" id="empPayBank"');
        }
    }
}



