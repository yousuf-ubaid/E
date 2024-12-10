<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Supplier extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Suppliermaster_model');
    }

    public function index()
    {
        $data['title'] = 'Supplier Master';
        $data['main_content'] = 'srp_mu_suppliermaster_view';
        $data['extra'] = NULL;
        $this->load->view('includes/template', $data);
    }


    function fetch_supplier()
    {
        $supplier_filter = '';
        $currency_filter = '';
        $category_filter = '';
        $deleted_filter = '';
        $supplier = $this->input->post('supplierCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
        $deleted = $this->input->post('deleted');
        if ($deleted == 1) {
            $deleted_filter = " AND deletedYN = " . $deleted;
        } else {
            $deleted_filter = " AND ( deletedYN IS NULL OR deletedYN = " . $deleted . ")";
        }
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierAutoID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join("' , '", $currency) . " )";
            $currency_filter = " AND supplierCurrencyID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join("' , '", $category) . " )";
            $category_filter = " AND srp_erp_suppliermaster.partyCategoryID IN " . $whereIN;
        }
        $policyCode='SUP';
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_suppliermaster.companyID = " . $companyid . $supplier_filter . $currency_filter . $category_filter. $deleted_filter . "";
        $this->datatables->select('srp_erp_suppliermaster.deletedYN as deletedYN,srp_erp_partycategories.categoryDescription as categoryDescription,supplierAutoID,supplierSystemCode,supplierName,secondaryCode,supplierName,supplierAddress1,supplierAddress2,supplierCountry,supplierTelephone,supplierEmail,supplierUrl,supplierFax,isActive,supplierCurrency,supplierEmail,supplierTelephone,supplierCurrencyID,cust.Amount as Amount,ROUND(cust.Amount, 2) as Amount_search,cust.partyCurrencyDecimalPlaces as partyCurrencyDecimalPlaces,masterConfirmedYN,masterApprovedYN')
            ->where($where)
            ->from('srp_erp_suppliermaster')
            ->join('srp_erp_partycategories', 'srp_erp_suppliermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID', 'left')
            ->join('(SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate)*-1 as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = "SUP" AND subLedgerType=2 GROUP BY partyAutoID) cust', 'cust.partyAutoID = srp_erp_suppliermaster.supplierAutoID', 'left');
        $this->datatables->add_column('supplier_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b> Email </b> $7  &nbsp;&nbsp;&nbsp;<b>Telephone</b> $8', 'supplierName,supplierAddress1, supplierAddress2, supplierCountry, secondaryCode, supplierCurrency, supplierEmail,supplierTelephone');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('supplierApprovalStatus', '$1', 'approvalStatus(isActive,masterConfirmedYN, masterApprovedYN,"ASM","SUP",supplierAutoID)');
        $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(Amount,partyCurrencyDecimalPlaces),supplierCurrency');
       // $this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="attachment_modal($1,\'Supplier\',\'SUP\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/supplier/erp_supplier_master_new\',$1,\'Edit Supplier\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_supplier($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'supplierAutoID');
        $this->datatables->add_column('edit', '$1', 'editsupplier(supplierAutoID, deletedYN,masterConfirmedYN, masterApprovedYN)');
        echo $this->datatables->generate();
    }

    function save_suppliermaster()
    {
        if (!$this->input->post('supplierAutoID')) {
            $this->form_validation->set_rules('supplierCurrency', 'supplier Currency', 'trim|required');
        }
        $this->form_validation->set_rules('suppliercode', 'Supplier Code', 'trim|required');
        $this->form_validation->set_rules('supplierName', 'supplier Name', 'trim|required');
        $this->form_validation->set_rules('suppliercountry', 'supplier country', 'trim|required');
        $this->form_validation->set_rules('nameOnCheque', 'Name On Cheque', 'trim|required');
        $this->form_validation->set_rules('liabilityAccount', 'liabilityAccount', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Suppliermaster_model->save_supplier_master());
        }
    }


    function edit_supplier()
    {
        if ($this->input->post('id') != "") {
            echo json_encode($this->Suppliermaster_model->get_supplier());
        } else {
            echo json_encode(FALSE);
        }
    }

    function load_supplier_header()
    {
        echo json_encode($this->Suppliermaster_model->load_supplier_header());
    }

    function delete_supplier()
    {
        echo json_encode($this->Suppliermaster_model->delete_supplier());
    }

    function fetch_supplier_category()
    {
        $this->datatables->select('partyCategoryID,partyType,categoryDescription')
            ->where('companyID', $this->common_data['company_data']['company_id'])
            ->where('partyType', 2)
            ->from('srp_erp_partycategories');
        $this->datatables->add_column('edit', '$1', 'editsuppliercategory(partyCategoryID)');
        echo $this->datatables->generate();
    }

    function saveCategory()
    {
        $this->form_validation->set_rules('categoryDescription', 'Category', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Suppliermaster_model->saveCategory());
        }
    }

    function getCategory()
    {
        echo json_encode($this->Suppliermaster_model->getCategory());
    }

    function delete_category()
    {
        echo json_encode($this->Suppliermaster_model->delete_category());
    }

    function save_supplierbank()
    {
        $this->form_validation->set_rules('bankName', 'Bank Name', 'trim|required');
        $this->form_validation->set_rules('currencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('accountName', 'Account Name', 'trim|required');
        $this->form_validation->set_rules('accountNumber', 'Account Number', 'trim|required');
        $this->form_validation->set_rules('swiftCode', 'Swift Code', 'trim|required');
        $this->form_validation->set_rules('ibanCode', 'IBAN Code', 'trim|required');
        $this->form_validation->set_rules('address', 'Bank Address', 'trim|required');
        $this->form_validation->set_rules('supplierAutoID', 'MasterID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Suppliermaster_model->save_bank_detail());
        }
    }

    function fetch_supplierbank()
    {
        $supplierAutoID=$this->input->post('supplierAutoID');
        $companyID = current_companyID();
        $this->datatables->select('supplierBankMasterID, supplierAutoID, accountName, accountNumber, swiftCode, IbanCode, bankName, srp_erp_supplierbankmaster.currencyID, srp_erp_currencymaster.CurrencyCode, bankAddress')
            ->from('srp_erp_supplierbankmaster')
            ->join('srp_erp_currencymaster', 'srp_erp_supplierbankmaster.CurrencyID = srp_erp_currencymaster.currencyID', 'left')
            ->where('companyID', $companyID)
            ->where('supplierAutoID',$supplierAutoID)
        ->add_column('edit', '<span class="pull-right"><a onclick="editBankDetails($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_supplierbank($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'supplierBankMasterID');
        echo $this->datatables->generate();
    }

    function delete_supplierbank()
    {
        echo json_encode($this->Suppliermaster_model->delete_supplierbank());
    }

    function edit_Bank_Details(){
        echo json_encode($this->Suppliermaster_model->edit_Bank_Details());
    }

    /* Function added  */
    function export_excel_supplier_master(){
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Supplier Master List');
        $this->load->database();
        $data = $this->Suppliermaster_model->export_excel_supplier_master();

        $header = ['#', 'Supplier Code', 'Supplier Name','Secondary Code','Address','Email','Telephone','URL','FAX','Tax Group ','VAT Identification No','Credit Period','Credit Limit','Name On Cheque','Liability Account','Currency','Balance'];
        $body = $data['suppliers'];

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Suppliers List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($body, null, 'A6');

        $filename = 'Supplier Master.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function sup_confirmation()
    {
        echo json_encode($this->Suppliermaster_model->sup_confirmation());
    }

    function check_supplier_confirmation()
    {
        echo json_encode($this->Suppliermaster_model->check_supplier_confirmation());
    }

    function approve_suppliermaster()
    {
        $companyID = current_companyID();
        $currentuser  = current_userID();
        $supplierAutoID = trim($this->input->post('supplierAutoID') ?? '');
        //$level_id = trim($this->input->post('level') ?? '');
        //$status = trim($this->input->post('status') ?? '');

        $this->form_validation->set_rules('supplierAutoID', 'Master ID', 'trim|required');
        //$this->form_validation->set_rules('status', 'Status', 'trim|required');
        // $this->form_validation->set_rules('level', 'Level', 'trim|required');
        /*if ($status == 2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }*/

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $result = $this->db->query("SELECT
                `approvalUserID`,
                `masterCurrentLevelNo` AS levelNo,
                srp_erp_approvalusers.companyCode AS `companyCode`,
                `srp_erp_approvalusers`.`documentID` AS `documentID`,
                `srp_erp_approvalusers`.`document` AS `document`,
                `employeeID`,
                `employeeName`, 
                srp_erp_suppliermaster.supplierAutoID AS supplierAutoID
            FROM
                `srp_erp_approvalusers`
                JOIN `srp_erp_documentcodes` ON `srp_erp_approvalusers`.`documentID` = `srp_erp_documentcodes`.`documentID` 
                LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_suppliermaster`.`masterCurrentLevelNo` 
            WHERE
                -- isApprovalDocument = 1 AND
                srp_erp_approvalusers.`companyID` =  $companyID 
                AND `srp_erp_approvalusers`.`documentID` = 'SUP' 
                AND `employeeID` =  $currentuser
                AND srp_erp_suppliermaster.supplierAutoID = $supplierAutoID " )->row_array();

        $level_id = $result['levelNo'];

        if(empty($result)){
            die( json_encode(['e', 'You are not authorized to perform this approval',2]) );
            //return array('e', 'You are not authorized to perform this approval');
        }else{
            $approvedYN = checkApproved($supplierAutoID, 'SUP', $level_id);
            if ($approvedYN) {
                die( json_encode(['w', 'Document already approved',2]) );
            }

            $document_status = $this->db->get_where('srp_erp_suppliermaster', ['supplierAutoID'=>$supplierAutoID])->row('masterConfirmedYN');
            if ($document_status == 2) {
                die( json_encode(['w', 'Document already rejected'],2) );
            }

            echo json_encode($this->Suppliermaster_model->approve_suppliermaster($level_id));
        }
    }

    function referback_supplier()
    {
        $masterID = $this->input->post('supplierAutoID');

        $document_status = document_status('SUP', $masterID, 1);
        //var_dump($document_status);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($masterID, 'SUP',false);
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }

        // echo json_encode($this->Item_model->referback_item());

    }


    /*function reversing_approval_supplier()
    {

    }*/

    function load_supplier_master_view(){
        //$supplierAutoID = trim($this->input->post('supplierAutoID') ?? '');
        //$data['extra'] = $this->Suppliermaster_model->load_supplier_header();
        $data['signature'] = '';
        $data['logo'] = mPDFImage;
        //echo '<pre>'; print_r($data); echo '</pre>';  exit;
        echo $this->load->view('system/supplier/erp_supplier_master_view', $data, true);

    }
  /* End  Function */  


  /** SMSD : create function fetch_supplier_master_approval*/
  function fetch_supplier_master_approval()
  {
      $supplier_filter = '';
      $currency_filter = '';
      $category_filter = '';
      $deleted_filter = '';
      $supplier = $this->input->post('supplierCode');
      $category = $this->input->post('category');
      $currency = $this->input->post('currency');

      if (!empty($supplier)) {
          $supplier = array($this->input->post('supplierCode'));
          $whereIN = "( " . join("' , '", $supplier) . " )";
          $supplier_filter = " AND supplierAutoID IN " . $whereIN;
      }
      if (!empty($currency)) {
          $currency = array($this->input->post('currency'));
          $whereIN = "( " . join("' , '", $currency) . " )";
          $currency_filter = " AND supplierCurrencyID IN " . $whereIN;
      }
      if (!empty($category)) {
          $category = array($this->input->post('category'));
          $whereIN = "( " . join("' , '", $category) . " )";
          $category_filter = " AND srp_erp_suppliermaster.partyCategoryID IN " . $whereIN;
      }

      $approvedYN = trim($this->input->post('approvedYN') ?? '');
      $convertFormat = convert_date_format_sql();
      $companyID = $this->common_data['company_data']['company_id'];
      $currentuserid = current_userID();

      $where1 = "srp_erp_suppliermaster.companyID = " . $companyID . $supplier_filter . $currency_filter . $category_filter. "";
      
      //$where2 = "(srp_erp_approvalusers.employeeID = ".$this->common_data['current_userID']." or srp_erp_approvalusers.employeeID=-1)";

      if(isset($approvedYN)){
          $this->datatables->select('srp_erp_suppliermaster.supplierAutoID as supplierAutoID,srp_erp_suppliermaster.isSrmGenerated as isSrmGenerated,srp_erp_suppliermaster.deletedYN as deletedYN,srp_erp_suppliermaster.isActive as isActive,supplierSystemCode,supplierName,nameOnCheque,supplierAddress1,supplierAddress2,secondaryCode,supplierCountry,supplierTelephone,supplierFax,supplierEmail,supplierCurrencyID,supplierCurrency,supplierCurrencyDecimalPlaces,masterConfirmedYN,masterApprovedYN,srp_erp_partycategories.categoryDescription as categoryDescription,srp_erp_documentapproved.approvedYN as approvedYN,srp_erp_documentapproved.documentApprovedID as documentApprovedID,srp_erp_documentapproved.approvalLevelID as approvalLevelID,masterCurrentLevelNo,DATE_FORMAT(srp_erp_suppliermaster.createdDateTime,\'' . $convertFormat . '\') AS createdDate,cust.Amount as Amount,ROUND(cust.Amount, 2) as Amount_search,cust.partyCurrencyDecimalPlaces as partyCurrencyDecimalPlaces');
          $this->datatables->from('srp_erp_suppliermaster');
          
          $this->datatables->join('(SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate)*-1 as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = "SUP" AND subLedgerType=2 GROUP BY partyAutoID) cust', 'cust.partyAutoID = srp_erp_suppliermaster.supplierAutoID', 'left');
          $this->datatables->join('srp_erp_partycategories', 'srp_erp_suppliermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID', 'left'); 
          $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_suppliermaster.supplierAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_suppliermaster.masterCurrentLevelNo AND srp_erp_documentapproved.documentID = "SUP"');
          $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_documentapproved.approvalLevelID AND srp_erp_approvalusers.documentID = "SUP" ');
          
          $this->datatables->where($where1);
          //$this->datatables->where($where2);
          //$this->datatables->where('srp_erp_suppliermaster.isActive', 1);
          //$this->datatables->where('srp_erp_suppliermaster.companyID', $companyID);
          $this->datatables->where('srp_erp_suppliermaster.masterConfirmedYN', 1);
          $this->datatables->where('srp_erp_suppliermaster.masterApprovedYN', $approvedYN);
          $this->datatables->where('srp_erp_documentapproved.documentID', 'SUP');
          $this->datatables->where('srp_erp_approvalusers.documentID', 'SUP');
          $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
          $this->datatables->where('srp_erp_approvalusers.employeeID' , trim($this->common_data['current_userID']));
          
          $this->datatables->add_column('supplier_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b> Email </b> $7  &nbsp;&nbsp;&nbsp;<b>Telephone</b> $8', 'supplierName,supplierAddress1, supplierAddress2, supplierCountry, secondaryCode, supplierCurrency, supplierEmail,supplierTelephone');
          $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
          $this->datatables->add_column('supplierApprovalStatus', '$1', 'approvalStatus(isActive,masterConfirmedYN,masterApprovedYN,"ASM","SUP",supplierAutoID)');
          $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(Amount,partyCurrencyDecimalPlaces),supplierCurrency');
          // $this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="attachment_modal($1,\'Supplier\',\'SUP\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/supplier/erp_supplier_master_new\',$1,\'Edit Supplier\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_supplier($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'supplierAutoID');
          $this->datatables->add_column('edit', '$1', 'editsupplier(supplierAutoID, deletedYN,masterConfirmedYN, masterApprovedYN, 1, 1,isSrmGenerated)');
          
          echo $this->datatables->generate();
      }
  }
/** SMSD : end*/

/**SMSD : create function reject_suppliermaster*/
  function reject_suppliermaster()
    {
        $companyID = current_companyID();
        $currentuser  = current_userID();
        $supplierAutoID = trim($this->input->post('supplierAutoID') ?? '');
        $comment = trim($this->input->post('comment') ?? '');
       
        $this->form_validation->set_rules('supplierAutoID', 'Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $result = $this->db->query("SELECT
                `masterCurrentLevelNo` AS levelNo,
                srp_erp_suppliermaster.supplierSystemCode AS supplierSystemCode
            FROM
                `srp_erp_approvalusers`
                 JOIN `srp_erp_suppliermaster` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_suppliermaster`.`masterCurrentLevelNo`
            WHERE
                srp_erp_approvalusers.`companyID` =  $companyID 
                AND `srp_erp_approvalusers`.`documentID` = 'SUP' 
                AND `employeeID` =  $currentuser
                AND srp_erp_suppliermaster.supplierAutoID = $supplierAutoID " )->row_array();

        $level_id = $result['levelNo'];

        if(empty($result)){
            die( json_encode(['e', 'You are not authorized to perform this approval',2]) );
        }
        else
        {
            $approvedYN = checkApproved($supplierAutoID, 'SUP', $level_id);
            if ($approvedYN) {
                die( json_encode(['w', 'Sorry! Can not Reject, Document already approved',2]) );
            }
            $document_status = $this->db->get_where('srp_erp_suppliermaster', ['supplierAutoID'=>$supplierAutoID])->row('masterApprovedYN');
            if ($document_status == 2) {
                die( json_encode(['w', 'Document already rejected',2]) );
            }
            else
            {
                $this->db->trans_start();

                $data1 = array(
                    'masterApprovedYN' => 2,
                    'modifiedPCID' => $this->common_data['current_pc'],
                    'modifiedUserID' => $this->common_data['current_userID'],
                    'modifiedUserName' => $this->common_data['current_user'],
                    'modifiedDateTime' => $this->common_data['current_date'],
                );
                $data2 = array(
                    'approvedYN' => 2,
                    //'approvedComments' => $comment,
                );
                $data3 = array(
                    'documentID' => "SUP",
                    'documentCode' => $result['supplierSystemCode'],
                    'systemID' => $supplierAutoID,
                    'comment' => $comment,
                    'table_name' => "srp_erp_suppliermaster",
                    'table_unique_field' => "supplierAutoID",
                    'rejectedLevel' => $level_id,
                    'rejectByEmpID' => $this->common_data['current_userID'],

                    'companyID' => $companyID,
                    'companyCode' => $this->common_data['company_data']['company_code'],
                    'createdUserGroup' => $this->common_data['user_group'],
                    'createdPCID' => $this->common_data['current_pc'],
                    'createdUserID' => $this->common_data['current_userID'],
                    'createdDateTime' => $this->common_data['current_date'],
                    'createdUserName' => $this->common_data['current_user'],
                    'timestamp' => current_date(true)
                );
                $this->db->where('supplierAutoID', $supplierAutoID)->update('srp_erp_suppliermaster', $data1);
                $this->db->where('documentSystemCode', $supplierAutoID)->update('srp_erp_documentapproved', $data2);
                $this->db->where('systemID', $supplierAutoID)->insert('srp_erp_approvalreject', $data3);

                $this->db->trans_complete();
                if ($this->db->trans_status() == true) {
                    echo json_encode(['s', 'Successfully Rejected', 2]);
                } else {
                    echo json_encode(['e', 'Faild to Reject the Supplier',3]);
                }

                //$this->session->set_flashdata('s', 'Successfully Rejected');
                //return json_encode(['s', 'Successfully Rejected',2]);
            }    
        }
    }
/**SMSD : end */

    function assign_approval_checklist_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $documentID = $this->input->post('documentID');
        $text = trim($this->input->post('Search') ?? '');
        $supplierAutoID = $this->input->post('supplierAutoID');
        $search_string = '';
        if (isset($text) && !empty($text)) 
        { 
            $search_string = "AND ((checklistDescription Like '%" . $text . "%'))";
        }

        $data['checklists'] = $this->db->query("SELECT * FROM srp_erp_document_approval_checklist where companyID = {$companyID}  AND documentID = '{$documentID}'   $search_string")->result_array();

        if(count($data['checklists'])>0){
            foreach($data['checklists'] as $key=>$val){
                $sub=$this->db->query("SELECT * FROM srp_erp_document_approval_checklistdeails where companyID = {$companyID}  AND checklistID = {$val['checklistID']} AND documentID='{$documentID}' AND documentMasterID={$supplierAutoID}")->row_array();

                if($sub){
                    $data['checklists'][$key]['isSelected']=1;
                }else{
                    $data['checklists'][$key]['isSelected']=0;
                }
            }
        }
        

        //$data['checklists'] = $this->db->query("SELECT * FROM srp_erp_checklistmaster where companyID = {$companyID}  AND isActive =1  $search_string")->result_array();

        $this->load->view('system/supplier/erp_approval_checklist_tb_view', $data);
    }

    function assignCheckListForSupplierApproval()
    {
        echo json_encode($this->Suppliermaster_model->assignCheckListForSupplierApproval());
    }

    function delete_approval_checklist()
    {
        echo json_encode($this->Suppliermaster_model->delete_approval_checklist());
    }

    function findCurrentLevelUserAccess()
    {
        echo json_encode($this->Suppliermaster_model->findCurrentLevelUserAccess());
    }
    
}
