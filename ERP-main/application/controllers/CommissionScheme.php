<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

/** ================================
 * -- File Name : CommissionScheme.php
 * -- Project Name : Gs_SME
 * -- Module Name : Commission Scheme
 * -- Create date : 24 - January 2019
 * -- Description : HRMS Commission scheme management
 */

class CommissionScheme extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('CommissionScheme_model');
        $this->load->helper('employee');

        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('hrms_approvals', $primaryLanguage);
        $this->lang->load('common', $primaryLanguage);
    }

    function save_scheme(){
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $companyID = current_companyID();
        $description = trim($this->input->post('description') ?? '');
        $isExists = $this->db->get_where('srp_erp_pay_commissionscheme', ['companyID'=>$companyID, 'description' => $description])->row('id');
        if(!empty($isExists)){
            die(json_encode(['e', 'This commission scheme is already exist']));
        }

        $dateTime = current_date(); $pc = current_pc(); $userID = current_userID(); $userGroup = current_user_group();
        $insert_data = [
            'description' => $description, 'companyID' => $companyID, 'createdUserGroup' => $userGroup, 'createdPCID' => $pc,
            'createdUserID' => $userID, 'createdDateTime' => $dateTime, 'timestamp' => $dateTime
        ];

        $this->db->trans_start();
        $this->db->insert('srp_erp_pay_commissionscheme', $insert_data);
        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Commission scheme added successfully.']);
        }else{
            echo json_encode(['e', 'Error in process.']);
        }
    }

    public function fetch_commission_schemes(){
        $companyID = current_companyID();
        $details = '<div align="right" >';
        $details .= '<span class="glyphicon glyphicon-pencil" onclick="edit_commission(this)" style="color:#3c8dbc;"></span>&nbsp;&nbsp; |  &nbsp;&nbsp;';
        $details .= '<span class="glyphicon glyphicon-trash" onclick="delete_commission($1)" style="color:#d15b47;"></span>';
        $details .= '</div>';

        $this->datatables->select('id, description', false)
            ->from('srp_erp_pay_commissionscheme sch')
            ->where('sch.companyID', $companyID)
            ->add_column('action', $details, 'id');
        echo $this->datatables->generate();
    }

    function edit_scheme(){
        $this->form_validation->set_rules('autoID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['s', validation_errors()]));
        }

        $companyID = current_companyID();
        $autoID = $this->input->post('autoID');
        $description = trim($this->input->post('description') ?? '');
        $dateTime = current_date(); $pc = current_pc(); $userID = current_userID();

        $isExists = $this->db->query("SELECT * FROM srp_erp_pay_commissionscheme WHERE companyID ={$companyID} 
                            AND description = '{$description}' AND id <> {$autoID}")->row('id');
        if(!empty($isExists)){
            die(json_encode(['e', 'This commission scheme is already exist']));
        }

        $update_data = [
            'description' => $description, 'modifiedPCID' => $pc, 'modifiedUserID' => $userID,
            'modifiedDateTime' => $dateTime, 'timestamp' => $dateTime
        ];
        $this->db->trans_start();
        $this->db->where(['id'=>$autoID, 'companyID'=>$companyID]);
        $this->db->update('srp_erp_pay_commissionscheme', $update_data);
        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Commission scheme updated successfully.']);
        }else{
            echo json_encode(['e', 'Error in process.']);
        }
    }

    function delete_scheme(){
        $this->form_validation->set_rules('id', 'Master ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['s', validation_errors()]));
        }

        $companyID = current_companyID();
        $autoID = $this->input->post('id');

        $where = ['Erp_companyID'=>$companyID, 'commissionSchemeID'=>$autoID];
        $isExists = $this->db->get_where('srp_employeesdetails', $where)->row('commissionSchemeID');
        if(!empty($isExists)){
            die(json_encode(['e', 'This commission scheme is in use']));
        }

        $this->db->trans_start();

        $this->db->where(['id'=>$autoID, 'companyID'=>$companyID]);
        $this->db->delete('srp_erp_pay_commissionscheme');

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Commission scheme deleted successfully.']);
        }else{
            echo json_encode(['e', 'Error in process.']);
        }
    }

    function fetch_commission_scheme(){

        $department_filter = '';
        $status_filter='';
       
        $department = $this->input->post('departmentFilter');
        $department_filter = (!empty($department))? " AND srp_erp_commisionscheme.departmentID  IN ({$department})": '';

        $status = $this->input->post('statusFilter');
        if ($status != 'all') {
            $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            switch ($status){
                case 1:  $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";  break;
                case 2:  $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";  break;
                case 4:  $status_filter = " AND ((confirmedYN = 3 AND approvedYN != 1) or (confirmedYN = 2 AND approvedYN != 1))";  break;
            }
        }
        $companyID=$this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $where = "srp_erp_commisionscheme.companyID = " . $companyID . $department_filter . $status_filter .  "";
        $this->datatables->select('schemeID as schemeID,documentCode,DepartmentDes as department,departmentID,DATE_FORMAT(documentDate,\'' . $convertFormat . '\')AS documentDate,srp_erp_commisionscheme.currencyID AS currencyID,Narration,confirmedYN,approvedYN,isDeleted,CurrencyCode,srp_erp_commisionscheme.createdUserID as createdUserID')
            ->where($where)
            ->from('srp_erp_commisionscheme')
            ->join('srp_departmentmaster', 'srp_erp_commisionscheme.departmentID = srp_departmentmaster.DepartmentMasterID AND srp_departmentmaster.Erp_companyID = \'' . $companyID . '\'', 'left')
            ->join('srp_erp_currencymaster', 'srp_erp_commisionscheme.currencyID = srp_erp_currencymaster.currencyID ', 'left');

        $this->datatables->add_column('confirmed', '$1', 'confirm(confirmedYN)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN, "CS", schemeID)');
        $this->datatables->add_column('edit', '$1', 'commission_scheme_action(schemeID,confirmedYN,approvedYN,createdUserID,documentCode,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        
        echo $this->datatables->generate();
    }

    function delete_commission_scheme()
    {
       
        $companyID = current_companyID();
        $masterID = trim($this->input->post('schemeID') ?? '');
        $document_status = document_status('CS', $masterID);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }
        
        $this->db->select('*');
        $this->db->where('srp_erp_commisionschemedetails.companyID', $companyID);
        $this->db->where('schemeMasterID',$masterID );
        $itemdetails = $this->db->get('srp_erp_commisionschemedetails')->result_array();
        if($itemdetails){
            die( json_encode(['e', 'Item pulled for Commission Scheme!']));
        }
        
        $this->db->trans_start();
        $data = ['isDeleted' => 1, 'deletedEmpID' => current_userID(), 'deletedDate' => current_date()];
        $this->db->where('schemeID', $masterID);
        $this->db->update('srp_erp_commisionscheme', $data);
       
        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Deleted successfully']);
        }else{
            echo json_encode(['e', 'Error in delete process.']);
        }
    }

    function save_commission_scheme_header()
    {
        $this->form_validation->set_rules("documentDate", 'Document Date', 'required');
        $this->form_validation->set_rules("designation[]", 'Designation', 'required');
        $this->form_validation->set_rules("department", 'Department', 'required');
        $this->form_validation->set_rules("currency", 'Currency', 'required');
        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->CommissionScheme_model->save_commission_scheme_header());
        }
    }
    function load_commission_scheme_header()
    {
        echo json_encode($this->CommissionScheme_model->load_commission_scheme_header());
    }
    /* function load_commission_scheme_detailss()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $schemeID= trim($this->input->post('schemeID') ?? '');
        $data['header'] = $this->db->query("SELECT * FROM `srp_erp_commisionschemedesignations` 
                        LEFT JOIN srp_designation ON srp_erp_commisionschemedesignations.designationID= srp_designation.DesignationID
	                    WHERE srp_erp_commisionschemedesignations.companyID = {$companyID} AND schemeMasterID = {$schemeID} 
                        GROUP BY schemeDesignationID")->result_array();
        $this->load->view('system/sales/load_commission_scheme_detailsView.php', $data);
    } */

    function fetch_item() {
        $schemeID = trim($this->input->post('schemeID') ?? '');
        $this->datatables->select('srp_erp_itemmaster.itemAutoID AS itemAutoID,itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,srp_erp_itemcategory.description as SubCategoryDescription', false);
        $this->datatables->from('srp_erp_itemmaster');
        $this->datatables->join('srp_erp_itemcategory', 'srp_erp_itemmaster.subcategoryID = srp_erp_itemcategory.itemCategoryID');
        if(!empty($schemeID)) {
            $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_commisionschemedetails WHERE srp_erp_commisionschemedetails.itemAutoID = srp_erp_itemmaster.itemAutoID AND schemeMasterID =' . $schemeID . ' )');
        }
        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'itemAutoID');
        echo $this->datatables->generate();
    }
       
    function add_commission_scheme_item()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 'e', 'message' => validation_errors()));
        } else {
            echo json_encode($this->CommissionScheme_model->add_commission_scheme_item());
        }
        
    }

    function fetch_commissionScheme_details()
    {
        $data['extra'] = $this->CommissionScheme_model->load_commission_scheme_details();
        $this->load->view('system/sales/load_commission_scheme_detailsView.php', $data);
      
    }

    function update_commission_amount()
    {
        $this->form_validation->set_rules('schemeMasterID', 'schemeMasterID', 'required');
        $this->form_validation->set_rules('designationID', 'designationID', 'required');
        $this->form_validation->set_rules('schemeDesignationID', 'schemeDesignationID', 'required');
        $this->form_validation->set_rules('schemeDetailID', 'schemeDetailID', 'required');
        $this->form_validation->set_rules('itemAutoID', 'itemAutoID', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->CommissionScheme_model->update_commission_amount());
        }
    }

    function commission_scheme_confirmation()
    {
        echo json_encode($this->CommissionScheme_model->commission_scheme_confirmation());
    }

    function customer_commission_scheme_approval()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN= $this->input->post('approvedYN');
        if($approvedYN == 0)
        {
            $this->datatables->select(" documentApprovedID,`approvedYN`, approvalLevelID,schemeID,`confirmedYN`,`documentDate`,
                                        `department`,`narration`, `t`.`documentCode`, `t`.`confirmedByName`, t.currentLevelNo  ");
            $this->datatables->from("(SELECT documentApprovedID,`cs`.`approvedYN`,approvalLevelID,`DepartmentDes` AS `department`,
            `confirmedYN`, cs.schemeID as schemeID ,`cs`.`narration`,`cs`.`documentDate`,cs.documentCode, cs.confirmedByName,cs.currentLevelNo  FROM srp_erp_commisionscheme cs 
            LEFT JOIN srp_departmentmaster dep  ON `cs`.`departmentID` = `dep`.`DepartmentMasterID` AND Erp_companyID = {$companyID}
            LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = cs.schemeID AND approvalLevelID = currentLevelNo 
            LEFT JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = cs.currentLevelNo 
            WHERE srp_erp_documentapproved.documentID = 'CS' AND srp_erp_approvalusers.documentID = 'CS' AND employeeID = '{$this->common_data['current_userID']}' AND cs.approvedYN={$approvedYN} AND cs.confirmedYN = 1 AND cs.companyID={$companyID} ORDER BY schemeID DESC )t");
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"CS",schemeID)');
            $this->datatables->add_column('edit', '$1','approval_edit(schemeID,approvalLevelID,approvedYN,documentApprovedID,"CS")');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select("documentApprovedID, approvedYN,approvalLevelID,schemeID,confirmedYN,documentDate,department,narration,`t`.`documentCode`, `t`.`confirmedByName`, t.currentLevelNo  ");
            $this->datatables->from("(SELECT documentApprovedID,cs.approvedYN,approvalLevelID,DepartmentDes AS department,confirmedYN,
            cs.schemeID,cs.narration,cs.documentDate,`cs`.`documentCode`, `cs`.`confirmedByName`, cs.currentLevelNo  FROM srp_erp_commisionscheme cs 
            LEFT JOIN srp_departmentmaster dep  ON cs.departmentID = dep.DepartmentMasterID AND Erp_companyID = {$companyID}
            LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = cs.schemeID  
            WHERE srp_erp_documentapproved.documentID = 'CS' AND cs.confirmedYN = 1 AND cs.approvedYN = 1 AND srp_erp_documentapproved.approvedEmpID = '{$this->common_data['current_userID']}'  AND cs.companyID={$companyID} 
            GROUP BY cs.schemeID,srp_erp_documentapproved.approvalLevelID 
            ORDER BY schemeID DESC )t");
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"CS",schemeID)');
            $this->datatables->add_column('edit', '$1','approval_edit(schemeID,approvalLevelID,approvedYN,documentApprovedID,"CS")');
            echo $this->datatables->generate();
        }


    }

    function load_commission_scheme_confirmation()
    {
        $data['type'] = $this->input->post('html');
        $schemeID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('schemeID') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];

        $convertFormat = convert_date_format_sql();
        $data['extra'] = $this->CommissionScheme_model->load_commission_scheme_details();
        $data['master'] = $this->db->query("select cs.schemeID, DATE_FORMAT(cs.documentDate,'$convertFormat') AS documentDate, 
        cs.narration,cs.confirmedYN,cs.confirmedByEmpID,cs.confirmedByName,cs.confirmedDate,cs.approvedYN,cs.approvedDate,
        cs.approvedbyEmpName,`DepartmentDes` AS `department`
        FROM srp_erp_commisionscheme cs 
        LEFT JOIN srp_departmentmaster dep ON `cs`.`departmentID` = `dep`.`DepartmentMasterID` AND Erp_companyID = $companyID 
        WHERE schemeID = $schemeID AND companyID =$companyID ")->row_array();

        $html = $this->load->view('system/sales/commission_scheme_print', $data, TRUE);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function save_commission_scheme_approval()
    {
        $system_code = trim($this->input->post('schemeID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
     
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'CS', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('schemeID');
                $this->db->where('schemeID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_commisionscheme');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('schemeID', 'Scheme ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->CommissionScheme_model->save_commission_scheme_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('schemeID');
            $this->db->where('schemeID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_commisionscheme');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'CS', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('schemeID', 'Commission Scheme ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->CommissionScheme_model->save_commission_scheme_approval());
                    }
                }
            }
        }
    }

    function delete_sc_item(){
        
        $schemeID = trim($this->input->post('schemeID') ?? '');
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
   
        $this->db->trans_start();
        if($schemeID != null &&  $itemAutoID != null ){
            $this->db->where('schemeMasterID', $schemeID);
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->delete('srp_erp_commisionschemedetails');
        }
        $this->db->select('*');
        $this->db->where('schemeMasterID', trim($schemeID));
        $this->db->from('srp_erp_commisionschemedetails');
        $itemDetails = $this->db->get()->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Deleted successfully',$itemDetails]);
        }else{
            echo json_encode(['e', 'Error in delete process.']);
        }
    }


    function referback_commission_scheme()
    {
        $schemeID = trim($this->input->post('schemeID') ?? '');
        $this->load->library('Approvals');
        $status = $this->approvals->approve_delete($schemeID, 'CS');
        if ($status != 1) {
            echo json_encode(array('e', ' Error in refer back.', $status));
        } else {
            $dataUpdate = array(
                'confirmedYN' => 0,
                'confirmedByEmpID' => '',
                'confirmedByName' => '',
                'confirmedDate' => '',
            );

            $this->db->where('schemeID', $schemeID);
            $this->db->update('srp_erp_commisionscheme', $dataUpdate);

            echo json_encode(array('s', ' Re Opened Successfully.', $status));
          //  echo json_encode(array('s', 'Customer Price  Referred Back Successfully.'));
        }
    }


    function re_open_cs()
    {
        $schemeID =trim($this->input->post('schemeID') ?? '');
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('schemeID', $schemeID );
        $this->db->update('srp_erp_commisionscheme', $data);
        //$this->session->set_flashdata('s', 'Re Opened Successfully.');
        echo json_encode(array('s', ' Re Opened Back Successfully.'));
    }

    function update_sort_order()
    {
        $this->form_validation->set_rules('schemeDesignationID', 'schemeDesignationID', 'required');
        $this->form_validation->set_rules('sortOrder', 'Sort Order', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->CommissionScheme_model->update_sort_order());
        }
    }

    function fetch_active_items_cs()
    {
        $department = $this->input->post('departmentFilter');
        $department_filter = (!empty($department))? " AND master.departmentID  IN ({$department})": '';
        $designation = $this->input->post('designationFilter');
        $designation_filter = (!empty($designation))? " AND det.designationID  IN ({$designation})": '';

        $status_filter='';
        $status = $this->input->post('statusFilter');
        if ($status != 'all') {
            $status_filter = " AND ( master.confirmedYN = 1 AND master.approvedYN = 1)";
            switch ($status){
                case 1:  $status_filter = " AND ( master.confirmedYN = 0 AND master.approvedYN = 0)";  break;
                case 2:  $status_filter = " AND ( master.confirmedYN = 1 AND master.approvedYN = 0)";  break;
                case 4:  $status_filter = " AND ((master.confirmedYN = 3 AND master.approvedYN != 1) or (master.confirmedYN = 2 AND master.approvedYN != 1))";  break;
            }
        }

        $companyID=$this->common_data['company_data']['company_id'];
        $where = "det.isActive = 1 AND det.companyID = " . $companyID . $department_filter .$status_filter. $designation_filter . "";
        $transactionCurrencyDecimalPlaces = get_company_currency_decimal();
        $this->datatables->select('det.schemeDetailID,
            master.DocumentCode as DocumentCode,
            dep.DepartmentDes as DepartmentDes,
            des.DesDescription as DesDescription,
            itemmaster.itemAutoID as itemAutoID,
            itemmaster.seconeryItemCode AS seconeryItemCode,
            itemmaster.itemDescription AS itemDescription,
            itemmaster.partNo AS partNo,
            itemmaster.comments AS comments,
            det.commisionAmount as commisionAmount')
            ->where($where)
            ->from('srp_erp_commisionschemedetails as det ')
            ->join('srp_erp_itemmaster itemmaster ', 'itemmaster.itemAutoID = det.itemAutoID AND itemmaster.companyID = \'' . $companyID . '\'', 'left')
            ->join('srp_erp_commisionscheme master', ' master.schemeID = det.schemeMasterID ', 'left')
            ->join('srp_departmentmaster dep', 'dep.DepartmentMasterID = master.departmentID AND dep.Erp_companyID = \'' . $companyID . '\' AND dep.isActive = 1 ', 'left')
            ->join('srp_designation des ', 'des.DesignationID = det.designationID AND des.isDeleted=0 AND des.Erp_companyID=  \'' . $companyID . '\' ', 'left');
        $this->datatables->add_column('itemdetails', " $1 - $2", 'seconeryItemCode,itemDescription');
        $this->datatables->add_column('amount', '<div class="pull-right"> $1 </div>', 'format_number(commisionAmount,' .$transactionCurrencyDecimalPlaces.  ')');
        
        echo $this->datatables->generate();
    }

    function fetch_active_items_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Commission Scheme');
        $this->load->database();
        $data = $this->CommissionScheme_model->fetch_active_items_excel();

        $header = ['#', 'Document Code','Department','Designation','Item Code','Item Description', 'Amount'];
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A4:G4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Active Commissions Item Wise'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($data, null, 'A6');
        $filename = 'Commission Scheme.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function fetch_commission_hierarchy()
    {
        $status_filter='';
        $status = $this->input->post('statusFilter');
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "ch.isDeleted=0 AND ch.companyID = " . $companyid . $status_filter . "";
        
        $this->datatables->select("ch.commissionHierarchyID as commissionHierarchyID,emp.Ename2 as Ename2 ,emp.EmpSecondaryCode as EmpSecondaryCode ,
        repEmp.Ename2 as repEname2,repEmp.EmpSecondaryCode as repEmpSecondaryCode, ch.isDeleted as isDeleted,
        IF(ISNULL(emp.EmpSecondaryCode) ,emp.Ename2,CONCAT(emp.Ename2,' - ' ,emp.EmpSecondaryCode)) as employee,
        desig.DesDescription as DesDescription,
        IF(ISNULL(repEmp.EmpSecondaryCode) ,repEmp.Ename2,CONCAT(repEmp.Ename2,' - ' ,repEmp.EmpSecondaryCode)) as reportingEmployee,
        repDesig.DesDescription as reportingDesDescription");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_commission_hierachy ch');
        $this->datatables->join('srp_employeesdetails emp', 'emp.EIdNo = ch.employeeID', 'left');
        $this->datatables->join('srp_designation desig', 'desig.DesignationID = ch.designationID AND desig.isDeleted=0 AND desig.Erp_companyID =\'' . $companyid . '\'', 'left');
        $this->datatables->join('srp_designation repDesig', 'repDesig.DesignationID = ch.reportingDesignationID AND repDesig.isDeleted=0 AND repDesig.Erp_companyID =\'' . $companyid . '\'', 'left');
        $this->datatables->join('srp_employeesdetails repEmp', 'repEmp.EIdNo = ch.reportingEmployeeID', 'left');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('edit', '$1', 'load_ch_action(commissionHierarchyID,isDeleted)');
        echo $this->datatables->generate();
    }
    function fetchEmployeeRelatedDesignation()
    {
        echo json_encode($this->CommissionScheme_model->fetchEmployeeRelatedDesignation());
    }
    function fetchReportingEmployeeRelatedDesignation()
    {
        echo json_encode($this->CommissionScheme_model->fetchReportingEmployeeRelatedDesignation());
    }
    function fetchSalespersonRelatedDesignation()
    {
        echo json_encode($this->CommissionScheme_model->fetchSalespersonRelatedDesignation());
    }
    function saveCommissionHierarchy(){
        $this->form_validation->set_rules('employeeID', 'Employee', 'trim|required');
        $this->form_validation->set_rules('designationID', 'Designation', 'trim|required');
        //$this->form_validation->set_rules('reportingEmployeeID', 'Reporting Employee', 'trim|required');
        //$this->form_validation->set_rules('reportingDesignationID', 'Reporting Designation', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode( array('e', validation_errors()));
        } else {
            echo json_encode($this->CommissionScheme_model->saveCommissionHierarchy());
        }

    }

    function getCommissionHierarchy(){
        echo json_encode($this->CommissionScheme_model->getCommissionHierarchy());
    }

    function delete_commission_hierarchy(){
        echo json_encode($this->CommissionScheme_model->delete_commission_hierarchy());
    }

    function get_commission_analysis_report()
    {
        $commissionAnalysisType = $this->input->post('commissionAnalysisType');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');

        $this->form_validation->set_rules('commissionAnalysisType', 'Type', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {

            $data["details"] = $this->CommissionScheme_model->get_commission_analysis_report();
            $data["type"] = "html";
            //$data['datefrom'] = $datefrom;
           // $data['dateto'] = $dateto;
            $data["commissionAnalysisType"] = $commissionAnalysisType;
            echo $html = $this->load->view('system/sales/ajax/load-commission-analysis-report', $data, true);
        }
    }

    function update_new_commission_amount()
    {
        $this->form_validation->set_rules('commissionDetailID', 'commissionDetailID', 'required');
        $this->form_validation->set_rules('commissoinAmount', 'commissoinAmount', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->CommissionScheme_model->update_new_commission_amount());
        }
    }
}