<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Chart_of_acconts_new extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->load->model('Chart_of_acconts_model');
        $this->load->model('Chart_of_acconts_model_new');
    }

    function load_master_ofAccount()
    {
        $accountType = $this->input->post('accountTYpe');
        $companyID = $this->common_data['company_data']['company_id'];
        $header = false;
        if (!empty($accountType)) {
            $commaList = implode(', ', $accountType);
            $header = $this->db->query("select Type,accountCategoryTypeID,CategoryTypeDescription from srp_erp_accountcategorytypes WHERE accountCategoryTypeID IN({$commaList})  order by sortOrder asc")->result_array();
        }
/*        $details = $this->db->query("SELECT srp_erp_chartofaccounts.*,companyReportingAmount,companyReportingCurrencyDecimalPlaces FROM srp_erp_chartofaccounts LEFT JOIN (SELECT SUM(companyReportingAmount) AS companyReportingAmount,GLAutoID,companyReportingCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE companyID={$companyID} GROUP BY srp_erp_generalledger.GLAutoID) gl ON (gl.GLAutoID =srp_erp_chartofaccounts.GLAutoID) WHERE srp_erp_chartofaccounts.companyID = {$companyID} GROUP BY srp_erp_chartofaccounts.GLAutoID")->result_array();*/

        $details = $this->db->query("SELECT ca.GLAutoID,ca.levelNo,ca.GLDescription,ca.masterAutoID,ca.systemAccountCode,ca.GLSecondaryCode FROM srp_erp_chartofaccounts ca WHERE ca.companyID = {$companyID} AND ca.levelNo IS NOT NULL GROUP BY ca.GLAutoID")->result_array();

        $data['header'] = $header;
        $data['details'] = $details;

        $html = $this->load->view('system/chart_of_accounts/ajax/ajax-load-srp-erp-chart-of-account_new', $data, true);
        echo $html;


    }

    function fetch_chart_of_acconts()
    {
        //$this->load->library('Datatables-2');
        $this->datatables->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory,masterAccount,approvedYN,CategoryTypeDescription', false);
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->where('masterAccountYN', 1);
        if ($this->input->post('master_account')) {
            $this->datatables->where('masterAccount', $this->input->post('master_account'));
        }
        if ($this->input->post('category')) {
            $this->datatables->where('masterCategory', $this->input->post('category'));
        }
        if ($this->input->post('control_account')) {
            $this->datatables->where('subCategory', $this->input->post('control_account'));
        }
        $this->datatables->from('srp_erp_chartofaccounts');
        $this->datatables->add_column('subCategorys', '$1', 'fetch_coa_type(subCategory)');
        $this->datatables->add_column('confirmed', '$1', 'confirm(approvedYN)');
        $this->datatables->add_column('balance', '$1', '<spsn class="pull-right">00</span>');
        $this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="edit_chart_of_accont($1)"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_chart_of_accont($1)"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'GLAutoID');
        echo $this->datatables->generate();
    }

    function save_chart_of_accont()
    {
        if($this->input->post('controlAccountUpdate')==0) {  /*if its 0 edit | not control account */

        if (!$this->input->post('GLAutoID')) {
            $this->form_validation->set_rules('masterAccountYN', 'Master Account', 'trim|required');
            $this->form_validation->set_rules('isBank', 'Is Bank', 'trim|required');
        }
        if ($this->input->post('isBank') && $this->input->post('isCash') !=1) {
            $this->form_validation->set_rules('accountCategoryTypeID', 'Account Type', 'trim|required');
            $this->form_validation->set_rules('GLSecondaryCode', 'Account Code', 'trim|required');
            $this->form_validation->set_rules('GLDescription', 'Account Description', 'trim|required');
            $this->form_validation->set_rules('masterAccountYN', 'Is Master Account', 'trim|required');

            $this->form_validation->set_rules('bankName', 'Bank Name', 'trim|required');
            $this->form_validation->set_rules('bank_branch', 'Bank Branch', 'trim|required');
            $this->form_validation->set_rules('bank_swift_code', 'Bank Swift Code', 'trim|required');
            $this->form_validation->set_rules('bankCurrencyCode', 'Bank Currency', 'trim|required');
            $this->form_validation->set_rules('bankAccountNumber', 'Bank Account Number', 'trim|required');
            $this->form_validation->set_rules('bankCheckNumber', 'Check Number', 'trim|required');
        }else{
            $this->form_validation->set_rules('accountCategoryTypeID', 'Account Type', 'trim|required');
            $this->form_validation->set_rules('GLSecondaryCode', 'Account Code', 'trim|required');
            $this->form_validation->set_rules('GLDescription', 'Account Description', 'trim|required');
            $this->form_validation->set_rules('masterAccountYN', 'Is Master Account', 'trim|required');
        }

        if ($this->input->post('masterAccountYN')) {
            $this->form_validation->set_rules('accountCategoryTypeID', 'Account Type', 'trim|required');
        } else {
            $this->form_validation->set_rules('masterAccount', 'Master Account', 'trim|required');

        }

        $this->form_validation->set_rules('masterAccountYN', 'Master Account YN', 'trim|required');

        $this->form_validation->set_rules('accountCategoryTypeID', 'Account Type', 'trim|required');
        }
        /*update validate control account*/
        $this->form_validation->set_rules('GLSecondaryCode', 'Secondary Code', 'trim|required');

        $this->form_validation->set_rules('GLDescription', 'GL Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if($this->input->post('controlAccountUpdate')==0) {
                if ($this->input->post('GLAutoID')) {
                    $GLAutoID = trim($this->input->post('GLAutoID') ?? '');
                    $accountCategoryTypeID = trim($this->input->post('accountCategoryTypeID') ?? '');
                    $masterAccountYN = trim($this->input->post('masterAccountYN') ?? '');
                    $validate = $this->db->query("select masterAccountYN,accountCategoryTypeID from srp_erp_chartofaccounts WHERE GLAutoID={$GLAutoID}")->row_array();
                    $recExistMaster = $this->db->query("SELECT * FROM srp_erp_chartofaccounts WHERE masterAutoID={$GLAutoID}")->row_array();
                    if (!empty($recExistMaster) && $masterAccountYN == 0) {
                        $this->session->set_flashdata('e', 'Ledger : You cannot change master account to sub account');

                        echo json_encode(array('status' => false));
                        exit;
                    } else {
                        if ($masterAccountYN != $validate['masterAccountYN'] || $accountCategoryTypeID != $validate['accountCategoryTypeID']) {
                            $valid = $this->db->query("SELECT revanueGLAutoID, costGLAutoID, assteGLAutoID, faCostGLAutoID, faACCDEPGLAutoID, faDEPGLAutoID, faDISPOGLAutoID FROM srp_erp_itemmaster where {$GLAutoID} IN( revanueGLAutoID, costGLAutoID, assteGLAutoID, faCostGLAutoID, faACCDEPGLAutoID, faDEPGLAutoID, faDISPOGLAutoID)")->row_array();
                            if (!empty($valid)) {
                                $this->session->set_flashdata('e', 'Ledger : Unable to assigned for master account. ');
                                echo json_encode(array('status' => false));
                                exit;
                            }



                            $valid2 = $this->db->query("SELECT  * FROM `srp_erp_generalledger` WHERE `GLAutoID` = {$GLAutoID} ")->row_array();
                            if (!empty($valid2)) {
                                $this->session->set_flashdata('e', 'Ledger : unable to change. Records exist in general ledger . ');
                                echo json_encode(array('status' => false));
                                exit;
                            }
                            $valid3 = $this->db->query("SELECT costGLAutoID,ACCDEPGLAutoID,DEPGLAutoID,DISPOGLAutoID,postGLAutoID FROM `srp_erp_fa_asset_master` WHERE {$GLAutoID} IN( costGLAutoID,ACCDEPGLAutoID,DEPGLAutoID,DISPOGLAutoID,postGLAutoID) ")->row_array();
                            if (!empty($valid3)) {
                                $this->session->set_flashdata('e', 'Ledger : unable to change. Records exist in fa asset master . ');
                                echo json_encode(array('status' => false));
                                exit;
                            }
                            $valid4 = $this->db->query("SELECT revenueGL, costGL, assetGL, faCostGLAutoID, faACCDEPGLAutoID, faDEPGLAutoID, faDISPOGLAutoID FROM srp_erp_itemcategory WHERE {$GLAutoID} IN( revenueGL, costGL, assetGL, faCostGLAutoID, faACCDEPGLAutoID, faDEPGLAutoID, faDISPOGLAutoID ) ")->row_array();
                            if (!empty($valid4)) {
                                $this->session->set_flashdata('e', 'Ledger : unable to change. Records exist in Item Category . ');
                                echo json_encode(array('status' => false));
                                exit;
                            }
                        }
                    }


                 $subAccountExist= $this->db->query("SELECT * FROM `srp_erp_chartofaccounts` WHERE masterAutoID = (SELECT GLAutoID FROM `srp_erp_chartofaccounts` WHERE `GLAutoID` = '{$GLAutoID}' AND `masterAccountYN` = '1') limit 1")->row_array();
                    if(!empty($subAccountExist) && $accountCategoryTypeID != $subAccountExist['accountCategoryTypeID'] ){
                      $this->session->set_flashdata('e', 'Ledger : Sub Account exist for this master account ');
                      echo json_encode(array('status' => false));
                      exit;
                    }
                    $GLSecondaryCode = $this->input->post('GLSecondaryCode');
                    $companyID = current_companyID();
                    if ($GLSecondaryCode != '') {
                        $exit = $this->db->query("SELECT * FROM `srp_erp_chartofaccounts` WHERE companyID = {$companyID}  AND GLSecondaryCode ='{$GLSecondaryCode}' AND `GLAutoID` != '{$GLAutoID}'  ")->row_array();
                        if (!empty($exit)) {
                            $this->session->set_flashdata('e', 'GL secondary code already exist');
                            echo json_encode(FALSE);
                            exit;
                        }

                    }
                } else {
                    $GLSecondaryCode = $this->input->post('GLSecondaryCode');
                    $companyID = current_companyID();
                    if ($GLSecondaryCode != '') {
                        $exit = $this->db->query("SELECT * FROM `srp_erp_chartofaccounts` WHERE companyID = {$companyID}  AND GLSecondaryCode ='{$GLSecondaryCode}' ")->row_array();
                        if (!empty($exit)) {
                            $this->session->set_flashdata('e', 'GL secondary code is already exist');
                            echo json_encode(FALSE);
                            exit;
                        }

                    }
                }
            }

            echo json_encode($this->Chart_of_acconts_model_new->save_chart_of_accont());
        }
    }

    function fetch_chart_of_acconts_approval()
    {
        $this->datatables->select('GLAutoID,approvalLevelID,srp_erp_chartofaccounts.approvedYN as approvedYN,documentApprovedID,systemAccountCode,GLDescription');
        $this->datatables->from('srp_erp_chartofaccounts');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_chartofaccounts.GLAutoID');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'GL');
        $this->datatables->where('approvedEmpID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
        $this->datatables->where('srp_erp_chartofaccounts.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('systemAccountCode', '$1', 'approval_change_modal(systemAccountCode,GLAutoID,documentApprovedID,approvalLevelID,approvedYN,GL)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'confirm(approvedYN)');
        $this->datatables->add_column('edit', '$1', 'RV_action_approval(GLAutoID,approvalLevelID,approvedYN,documentApprovedID)');
        echo $this->datatables->generate();
    }

    function load_chart_of_accont_header()
    {
        echo json_encode($this->Chart_of_acconts_model_new->load_chart_of_accont_header());
    }

    function fetch_master_account()
    {
        echo json_encode($this->Chart_of_acconts_model_new->fetch_master_account());
    }

    function delete_chart_of_accont()
    {
        echo json_encode($this->Chart_of_acconts_model_new->delete_chart_of_accont());
    }

    function fetch_cheque_number()
    {
        echo json_encode($this->Chart_of_acconts_model_new->fetch_cheque_number());
    }

    function fetch_chart_of_account_drilldown()
    {
        $GLAutoID = $this->input->post('GLAutoID');

        $outRecord = '';

        $depMaster = $this->db->query("SELECT * FROM `srp_erp_chartofaccounts` WHERE `masterAutoID` = '{$GLAutoID}'")->result_array();

        $outRecord .= '<table class="table" style="background-color: #f0f3f5;">';

        if (!empty($depMaster)) {
            foreach ($depMaster as $val) {
                $outRecord .= '<tr>';
                $outRecord .= '<td width="35%" style="text-indent: 7%;">' . $val['GLDescription'] . '</td>';
                $outRecord .= '<td width="11.5%">' . $val['systemAccountCode'] . '</td>';
                $outRecord .= '<td width="10.5%">' . $val['GLSecondaryCode'] . '</td>';
                $outRecord .= '<td width="10.5%">' . $val['CategoryTypeDescription'] . '</td>';
                $outRecord .= '<td width="10.5%">' . '<spsn class="pull-right">00</span>' . '</td>';
                $outRecord .= '<td width="6%">' . confirm($val['approvedYN']) . '</td>';
                $outRecord .= '<td width="10%">' . '<spsn class="pull-right"><a onclick="edit_chart_of_accont(' . $val['GLAutoID'] . ')"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_chart_of_accont(' . $val['GLAutoID'] . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>' . '</td>';
                $outRecord .= '</tr>';
            }
        }
        $outRecord .= '</table>';
        echo $outRecord;
    }

    /* Function added */
    function export_excel_chartofaccounts_master_new(){
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Chart Of Acccount Master List');
        $this->load->database();
        $data = $this->Chart_of_acconts_model_new->export_excel_chartofaccounts_master_new();

        $header = ['#', 'System Code', 'Secondary Code','Category Type','Sub Category','Control Account YN','Is Bank','Bank Name','Bank Branch','Bank Short Code','Bank Swift Code','Balance'];
        $body = $data['chartofaccounts_new'];

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Chart Of Account List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($body, null, 'A6');

        $filename = 'Chart Of Acount Master.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }
        /* End  Function */
}
