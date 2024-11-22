<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CommissionScheme_model extends ERP_Model
{

    function save_commission_scheme_header()
    {
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];
        $docDate = $this->input->post('documentDate');
        $documentDate = input_format_date($docDate, $date_format_policy);
        $company_code = $this->common_data['company_data']['company_code'];
        $schemeID = $this->input->post('schemeID');
        $narration = $this->input->post('narration');
        $designation_array = $this->input->post('designation');
        $department = $this->input->post('department');
        $currency = $this->input->post('currency');
       
        $data['documentDate'] = $documentDate;
        $data['Narration'] = $narration;
        $data['departmentID'] = $department;
        $data['currencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        //$data['currencyID'] = $currency;

        $this->db->trans_start();
        if($schemeID){
            $itemdetails = $this->db->query("select * from srp_erp_commisionschemedetails WHERE companyID = {$companyID} AND schemeMasterID = {$schemeID}")->result_array();

            if(empty($itemdetails) && !empty($designation_array)){

                $this->db->where('schemeMasterID', $schemeID);
                $this->db->where('companyID', $companyID);
                $this->db->delete('srp_erp_commisionschemedesignations');

                $a=1;
                foreach ($designation_array as $designation)
                {
                    $details['companyID'] = $this->common_data['company_data']['company_id'];
                    $details['schemeMasterID'] = $schemeID;
                    $details['designationID'] = $designation;
                    $details['sortOrder'] = $a;
                    $details['modifiedPCID'] = $this->common_data['current_pc'];
                    $details['modifiedUserID'] = $this->common_data['current_userID'];
                    $details['modifiedUserName'] = $this->common_data['current_user'];
                    $details['modifiedDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_commisionschemedesignations', $details);
                     $a++;  
                }
            }

             $data['modifiedPCID'] = $this->common_data['current_pc'];
             $data['modifiedUserID'] = $this->common_data['current_userID'];
             $data['modifiedUserName'] = $this->common_data['current_user'];
             $data['modifiedDateTime'] = $this->common_data['current_date'];
             $this->db->where('schemeID', $schemeID);
             $this->db->update('srp_erp_commisionscheme', $data);
             $last_id = $schemeID;

        } else {
            $this->load->library('sequence');
            $data['documentCode'] = $this->sequence->sequence_generator('CS');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_commisionscheme', $data);
            $last_id = $this->db->insert_id();

            $a=1;
            foreach ($designation_array as $designation) {

                $details['schemeMasterID'] = $last_id;
                $details['designationID'] = $designation;
                $details['companyID'] = $this->common_data['company_data']['company_id'];
                $details['sortOrder'] = $a;
                $details['createdUserGroup'] = $this->common_data['user_group'];
                $details['createdPCID'] = $this->common_data['current_pc'];
                $details['createdUserID'] = $this->common_data['current_userID'];
                $details['createdDateTime'] = $this->common_data['current_date'];
                $details['createdUserName'] = $this->common_data['current_user'];

                $this->db->insert('srp_erp_commisionschemedesignations', $details);
                $a++;  
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Commission Scheme :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Commission Scheme :  Saved Successfully.',$last_id);
        }
    }

    function load_commission_scheme_header()
    {
        $schemeID = $this->input->post('schemeID');
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate');
        $this->db->from('srp_erp_commisionscheme');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_commisionscheme.currencyID = srp_erp_currencymaster.currencyID','left');

        $this->db->where('schemeID', trim($schemeID));
        $data['header'] = $this->db->get()->row_array();

        $designation_array = $this->db->query("SELECT * 
                            FROM `srp_erp_commisionschemedesignations` 
                            WHERE schemeMasterID = {$schemeID}")->result_array();

        /* $data['item'] = $this->db->query("SELECT DISTINCT(srp_erp_commisionschemedetails.itemAutoID) AS itemAutoID, itemSystemCode, itemDescription 
                            FROM `srp_erp_customeritemprices` 
                            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_customeritemprices.itemAutoID
                            WHERE cpsAutoID = {$cpsAutoID}")->result_array(); */
        $data['item'] = $this->db->query("SELECT DISTINCT(itemAutoID) AS itemAutoID
                            FROM `srp_erp_commisionschemedetails` 
                            WHERE schemeMasterID = {$schemeID}")->result_array(); 
                                           
        if(!empty($designation_array)){
            foreach ($designation_array as $val){
                $data['designation'][] = $val['designationID'];
            }
        }
        return $data;
    }

    function add_commission_scheme_item()
    {
        $companyID = current_companyID();
        $selectedItemsSync = $this->input->post('selectedItemsSync');
        $schemeID = $this->input->post('schemeID');
        $this->db->select('*');
        $this->db->where('companyID', $companyID);
        $this->db->where('schemeMasterID', trim($schemeID));
        $designation_array = $this->db->get('srp_erp_commisionschemedesignations')->result_array();
        
        $this->db->trans_start();
        foreach ($selectedItemsSync AS $itemAutoID)
        {
            $item_data = fetch_item_data($itemAutoID);
            foreach ($designation_array AS $designation)
            {
                $data['schemeMasterID'] = $schemeID;
                $data['itemAutoID'] = $itemAutoID;
                $data['schemeDesignationID'] = $designation['schemeDesignationID'];
                $data['designationID'] = $designation['designationID'];
                $data['commisionAmount'] = ' ';
                $data['companyID'] = $companyID;
                $data['timestamp'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_commisionschemedetails', $data);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 'e', 'message' => 'Item Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 's', 'message' => 'Item Detail : Saved Successfully.');
        }
    }
    function load_commission_scheme_details()
    {
        
        //$itemSearch = $this->input->post('itemSearch');
        $schemeID=$this->input->post('schemeID');
        $companyID = current_companyID();
        $this->db->select('description, count(description) AS catCount');
        $this->db->where('srp_erp_itemcategory.companyID', $companyID);
        $this->db->where('schemeMasterID',$schemeID );
        $this->db->join('srp_erp_itemmaster ItemMaster', 'srp_erp_itemcategory.itemCategoryID = ItemMaster.subcategoryID','left');
        $this->db->join('srp_erp_commisionschemedetails', 'srp_erp_commisionschemedetails.itemAutoID = ItemMaster.itemAutoID','left');
        $this->db->group_by('description');
        $this->db->order_by('itemCategoryID ASC');
        $data['category'] = $this->db->get('srp_erp_itemcategory')->result_array();
       
        $this->db->select('det.schemeDetailID,det.schemeMasterID,det.itemAutoID,det.schemeDesignationID,det.designationID,designation.sortOrder as sortOrder,
            det.commisionAmount, det.isActive as isActive,srp_erp_itemcategory.itemCategoryID,srp_erp_itemcategory.description,
            ItemMaster.itemSystemCode, ItemMaster.seconeryItemCode,ItemMaster.itemDescription,des.DesDescription as DesDescription ');
        $this->db->where('det.companyID', $companyID);
        $this->db->where('det.schemeMasterID',$schemeID );
        $this->db->join('srp_erp_itemmaster ItemMaster', 'det.itemAutoID = ItemMaster.itemAutoID','left');
        $this->db->join('srp_erp_itemcategory', 'srp_erp_itemcategory.itemCategoryID = ItemMaster.subcategoryID','left');
        $this->db->join('srp_designation des', 'det.designationID= des.DesignationID AND des.isDeleted=0 AND des.Erp_companyID=  \'' . $companyID . '\'','left');
        $this->db->join('srp_erp_commisionschemedesignations designation', 'det.schemeDesignationID= designation.schemeDesignationID AND  designation.companyID =   \'' . $companyID . '\'','left');
        $this->db->group_by('det.schemeDetailID');
        $this->db->order_by('itemCategoryID ASC,itemAutoID,sortOrder,designationID');
        $data['details'] = $this->db->get('srp_erp_commisionschemedetails AS det')->result_array();

        $this->db->select(' csdes.schemeDesignationID as schemeDesignationID,csdes.schemeMasterID as schemeMasterID,
            csdes.designationID as designationID, csdes.sortOrder as sortOrder, des.DesDescription as DesDescription ');
        $this->db->where('csdes.companyID', $companyID);
        $this->db->where('csdes.schemeMasterID',$schemeID );
        $this->db->join('srp_designation des', 'csdes.designationID= des.DesignationID AND des.isDeleted=0 AND des.Erp_companyID=  \'' . $companyID . '\'','left');
        $this->db->group_by('csdes.schemeDesignationID');
        $this->db->order_by('csdes.sortOrder ASC,designationID');
        $data['designation'] = $this->db->get('srp_erp_commisionschemedesignations as csdes')->result_array();

        return $data;
    }

    function update_commission_amount()
    {
        $schemeMasterID = $this->input->post('schemeMasterID');
        $designationID = $this->input->post('designationID');
        $schemeDesignationID = $this->input->post('schemeDesignationID');
        $schemeDetailID = $this->input->post('schemeDetailID');
        $itemAutoID = $this->input->post('itemAutoID');
        $commissoinAmount = $this->input->post('commissoinAmount');

       /*  $this->db->select('currentlWacAmount');
        $this->db->where('stockTransferDetailsID', $stockTransferDetailAutoID);
        $master = $this->db->get('srp_erp_stocktransferdetails_bulk')->row_array(); */
        $totalValue  = 0;
        $data['commisionAmount'] = $commissoinAmount;
        //if($commissoinAmount > 0) {
            $totalValue = $totalValue + $commissoinAmount;
       // } else {
            //$data['totalValue'] = 0;
        //}

        $this->db->where('schemeDetailID', $schemeDetailID);
        $this->db->update('srp_erp_commisionschemedetails', $data);
        $company_currency_decimal = get_company_currency_decimal();
        
        $det = $this->db->query("SELECT FORMAT(IFNULL(IFNULL(SUM(commisionAmount), 0)/ IFNULL(count(commisionAmount ),0),0),$company_currency_decimal) as avgPaymentPerProduct FROM srp_erp_commisionschemedetails 
        WHERE schemeMasterID = {$schemeMasterID} AND designationID = {$designationID}")->row('avgPaymentPerProduct');
        return $det;
    }

    function commission_scheme_confirmation()
    {
        $schemeID = trim($this->input->post('schemeID') ?? '');
        
        //$this->db->select('schemeID,documentDate');
        $this->db->select('*, DATE_FORMAT(documentDate, "%Y") as csYear,DATE_FORMAT(documentDate, "%m") as csMonth');
        $this->db->where('schemeID', $schemeID);
        $this->db->from('srp_erp_commisionscheme');
        $row = $this->db->get()->row_array();

        $this->db->select('schemeMasterID');
        $this->db->where('schemeMasterID', $schemeID);
        $this->db->from('srp_erp_commisionschemedetails');
        $itemdetails = $this->db->get()->result_array();

        if (empty($row)) {
            return array('w', 'There are no records to confirm this document!');
        } else if(empty($itemdetails)){
            return array('w', 'No items pulled for Commission Scheme!');
        } else {
            $this->load->library('approvals');
            /* $this->db->select('*');
            $this->db->where('schemeID', $schemeID);
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_commisionscheme');
            $row = $this->db->get()->row_array(); */
            if($row['confirmedYN']==1){
            //if (!empty($row)) {
                return array('w', 'Document already confirmed');
            } else {
                /* $this->db->select('*, DATE_FORMAT(documentDate, "%Y") as csYear,DATE_FORMAT(documentDate, "%m") as csMonth');
                $this->db->where('schemeID', $schemeID);
                $this->db->from('srp_erp_commisionscheme');
                $row = $this->db->get()->row_array(); */
               
                $this->db->trans_start();
                $approvals_status = $this->approvals->CreateApproval('CS', $row['schemeID'], $row['documentCode'], 'Commission Scheme', 'srp_erp_commisionscheme', 'schemeID',0 ,$row['documentDate']);
                if ($approvals_status == 1) {
                    $schemeID = trim($this->input->post('schemeID') ?? '');

                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']
                       
                    );
                    $this->db->where('schemeID', $schemeID);
                    $this->db->update('srp_erp_commisionscheme', $data);
                } 
                if ($approvals_status == 3) {
                    die( json_encode(['w', 'There are no users exist to perform approval for this document.']) );
                    
                }  
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    die( json_encode(['e', 'Commission Scheme : ' . $row['documentCode'] . ' confirmation failed ' . $this->db->_error_message()]) );
                } else {
                    $this->db->trans_commit();
                    die( json_encode(['s', 'Commisson Scheme : ' . $row['documentCode'] . ' confirmed successfully']) );
                }
            }
        }
    }

    function save_commission_scheme_approval()
    {
        $compID = $this->common_data['company_data']['company_id'];
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_id = trim($this->input->post('schemeID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'CS');

        if ($approvals_status == 1) {
            $details = $this->db->query("select * from srp_erp_commisionschemedetails 
            LEFT JOIN srp_erp_commisionscheme ON srp_erp_commisionschemedetails.schemeMasterID=srp_erp_commisionscheme.schemeID
            WHERE schemeMasterID = {$system_id} AND srp_erp_commisionschemedetails.companyID = $compID ")->result_array();
            foreach ($details as $val)
            {
                 $activeSchemedetailAvailable = $this->db->query("select schemeDetailID from srp_erp_commisionschemedetails 
                 LEFT JOIN srp_erp_commisionscheme ON srp_erp_commisionschemedetails.schemeMasterID=srp_erp_commisionscheme.schemeID
                        WHERE srp_erp_commisionschemedetails.companyID = {$compID} AND srp_erp_commisionschemedetails.itemAutoID = {$val['itemAutoID']} AND srp_erp_commisionschemedetails.designationID = {$val['designationID']} AND srp_erp_commisionscheme.departmentID={$val['departmentID']} AND isActive = 1")->row_array();

                    if($activeSchemedetailAvailable){
                        $deact['isActive'] = 0;
                        $deact['deactivatedDocumentID'] = $val['schemeDetailID'];
                        $this->db->where('schemeDetailID', $activeSchemedetailAvailable['schemeDetailID']);
                        $this->db->update('srp_erp_commisionschemedetails', $deact);
                    }

                    $act['isActive'] = 1;
                    $this->db->where('schemeDetailID', $val['schemeDetailID']);
                    $this->db->update('srp_erp_commisionschemedetails', $act);

            }

            /* $detail['isActive'] = 1;
            $this->db->where('cpsAutoID', $system_id);
            $this->db->update('srp_erp_customeritemprices', $detail); */

            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            $this->db->where('schemeID', $system_id);
            $this->db->update('srp_erp_commisionscheme', $data);
            $this->session->set_flashdata('s', 'Commission Scheme Approved Successfully.');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function update_sort_order()
    {
        $schemeDesignationID = $this->input->post('schemeDesignationID');
        $sortOrder = $this->input->post('sortOrder');
        $data['sortOrder'] = $sortOrder;
        $this->db->where('schemeDesignationID', $schemeDesignationID);
        $this->db->update('srp_erp_commisionschemedesignations', $data);
        
        $det = $this->db->query("SELECT sortOrder FROM srp_erp_commisionschemedesignations 
        WHERE schemeDesignationID = {$schemeDesignationID} ")->row('sortOrder');

        return $det;
    }

    function fetch_active_items_excel()
    {
        $data = array();
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
        $this->db->select('det.schemeDetailID,
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
        $details = $this->db->get()->result_array();

        $a = 1;
        foreach ($details as $row) {
            $data[] = array(
                'Num' => $a,
                'documentCode' => $row['DocumentCode'],
                'department' => $row['DepartmentDes'],
                'designation' => $row['DesDescription'],
                'itemCode' => $row['seconeryItemCode'],
                'itemDescription' => $row['itemDescription'],
                'Amount' => $row['commisionAmount']
            );
            $a++;
        }
        return $data;
    }

    function fetchEmployeeRelatedDesignation(){
        $companyID=$this->common_data['company_data']['company_id'];
        $commissionHierarchyID =$this->input->post('commissionHierarchyID');
        $employeeID =$this->input->post('employeeID');
        $union='';
        if($commissionHierarchyID) {
            $union = 'UNION SELECT ch.employeeID AS EmpID,ch.`designationID` AS `DesignationID`,`srp_designation`.`DesDescription` AS `DesDescription`,
            2 AS isMajor 
            FROM srp_erp_commission_hierachy  ch
            JOIN `srp_designation` ON `srp_designation`.`DesignationID` = ch.`designationID` AND `srp_designation`.`isDeleted` = 0 
            AND `srp_designation`.`Erp_companyID` = '.$companyID.'
            WHERE ch.isDeleted = 0 AND commissionHierarchyID = '.$commissionHierarchyID.' AND  ch.employeeID = '.$employeeID.' ';
        }
        $where ='AND `srp_employeedesignation`.`DesignationID` NOT IN (select ch.designationID from srp_erp_commission_hierachy ch where ch.isDeleted = 0 AND  employeeID ='.$employeeID.')';
        $result=$this->db->query("SELECT srp_employeedesignation.EmpID, `srp_employeedesignation`.`DesignationID` AS `DesignationID`,
            `srp_designation`.`DesDescription` AS `DesDescription`, srp_employeedesignation.isMajor as isMajor
            FROM `srp_employeedesignation`
            JOIN `srp_designation` ON `srp_designation`.`DesignationID` = `srp_employeedesignation`.`DesignationID` AND `srp_designation`.`isDeleted` = 0 
            AND `srp_designation`.`Erp_companyID` = $companyID
            WHERE `EmpID` = $employeeID $where $union")->result_array();
        return $result;
	}

    function fetchReportingEmployeeRelatedDesignation(){
        $companyID=$this->common_data['company_data']['company_id'];
        $commissionHierarchyID =$this->input->post('commissionHierarchyID');
        $employeeID =$this->input->post('employeeID');

        $union='';
        if($commissionHierarchyID) {
                $union = 'UNION SELECT ch.reportingEmployeeID AS EmpID,ch.`reportingDesignationID` AS `DesignationID`,
                `srp_designation`.`DesDescription` AS `DesDescription`, 2 AS isMajor 
                FROM srp_erp_commission_hierachy  ch
                JOIN `srp_designation` ON `srp_designation`.`DesignationID` = ch.`reportingDesignationID` AND `srp_designation`.`isDeleted` = 0 
                AND `srp_designation`.`Erp_companyID` = '.$companyID.'
                WHERE ch.isDeleted = 0 AND commissionHierarchyID = '.$commissionHierarchyID.' AND  ch.reportingEmployeeID = '.$employeeID.' ';
        }

        //$where = 'AND `srp_employeedesignation`.`DesignationID` NOT IN (select ch.reportingDesignationID from srp_erp_commission_hierachy ch where ch.isDeleted = 0 AND reportingEmployeeID ='.$employeeID.')';

        $result=$this->db->query("SELECT srp_employeedesignation.EmpID,
            `srp_employeedesignation`.`DesignationID` AS `DesignationID`,
            `srp_designation`.`DesDescription` AS `DesDescription`,
            srp_employeedesignation.isMajor as isMajor
            FROM `srp_employeedesignation`
            JOIN `srp_designation` ON `srp_designation`.`DesignationID` = `srp_employeedesignation`.`DesignationID` AND `srp_designation`.`isDeleted` = 0 
            AND `srp_designation`.`Erp_companyID` = $companyID
            WHERE `EmpID` = $employeeID  $union")->result_array();
        return $result;
    }

    function fetchSalespersonRelatedDesignation(){
        $companyID=$this->common_data['company_data']['company_id'];
        $employeeID =$this->input->post('employeeID');
        $invoiceDetailsAutoID =$this->input->post('invoiceDetailsAutoID');

        $union='';
        $where = '';
        if($invoiceDetailsAutoID) {
            $union = 'UNION SELECT invdet.salesPersonID AS EmpID, invdet.`designationID` AS `DesignationID`, `srp_designation`.`DesDescription` AS `DesDescription`,
                2 AS isMajor 
                FROM srp_erp_customerinvoicedetails  invdet
                JOIN `srp_designation` ON `srp_designation`.`DesignationID` = invdet.`designationID` AND `srp_designation`.`isDeleted` = 0 
                AND `srp_designation`.`Erp_companyID` = '.$companyID.' 
                WHERE `invoiceDetailsAutoID` = '.$invoiceDetailsAutoID.' AND  invdet.salesPersonID = '.$employeeID.' ';

            $where = 'AND `srp_employeedesignation`.`DesignationID` NOT IN (select invdet.designationID from srp_erp_customerinvoicedetails invdet where `invoiceDetailsAutoID` = '.$invoiceDetailsAutoID.' AND  invdet.salesPersonID = '.$employeeID. ')';
        }

        $result=$this->db->query("SELECT srp_employeedesignation.EmpID, `srp_employeedesignation`.`DesignationID` AS `DesignationID`,
            `srp_designation`.`DesDescription` AS `DesDescription`, srp_employeedesignation.isMajor as isMajor
            FROM `srp_employeedesignation`
            JOIN `srp_designation` ON `srp_designation`.`DesignationID` = `srp_employeedesignation`.`DesignationID` AND `srp_designation`.`isDeleted` = 0 
            AND `srp_designation`.`Erp_companyID` = $companyID
            WHERE `EmpID` = $employeeID  $where  $union")->result_array();
        return $result;
    }

    function saveCommissionHierarchy(){
        $this->db->trans_start();
        $commissionHierarchyID=trim($this->input->post('commissionHierarchyID') ?? '');
        $employeeID=$this->input->post('employeeID');
        $designationID=$this->input->post('designationID');
        $reportingEmployeeID=$this->input->post('reportingEmployeeID');
        $reportingDesignationID=$this->input->post('reportingDesignationID');

        $data['employeeID'] = $employeeID;
        $data['designationID'] = $designationID;
        $data['reportingEmployeeID'] = $reportingEmployeeID;
        $data['reportingDesignationID'] = $reportingDesignationID;

        if ($commissionHierarchyID) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('commissionHierarchyID', $commissionHierarchyID);
            $this->db->update('srp_erp_commission_hierachy', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Commission Hierarchy Updating Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Commission Hierarchy Updated Successfully.');
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_commission_hierachy', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Commission Hierarchy Save  Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Commission Hierarchy Saved Successfully.');
            }
        }
    }

    function getCommissionHierarchy(){
        $companyid = $this->common_data['company_data']['company_id'];
        $commissionHierarchyID = trim($this->input->post('commissionHierarchyID') ?? '');

        $this->db->select("*");
        $this->db->where('companyID',$companyid );
        $this->db->where('commissionHierarchyID',$commissionHierarchyID );
        $this->db->where('isDeleted',0 );
        $this->db->from('srp_erp_commission_hierachy ch');
        return $this->db->get()->row_array();
    }

    function delete_commission_hierarchy(){
        $commissionHierarchyID = trim($this->input->post('commissionHierarchyID') ?? '');
        $this->db->trans_start();
        $data = array(
            'isDeleted' => 1,
            'deletedEmpID' => current_userID(),
            'deletedDate' => current_date(),
        );
        $this->db->where('commissionHierarchyID', $commissionHierarchyID);
        $this->db->update('srp_erp_commission_hierachy', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting this record' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Deleted Successfully.');
        }

    }

    function get_commission_analysis_report(){
        $companyID = $this->common_data['company_data']['company_id'];
        $commissionAnalysisType = $this->input->post('commissionAnalysisType');
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invmaster.invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invmaster.invoiceDate <= '" . $datetoconvert . " 00:00:00')";
        }
        if($commissionAnalysisType)
        $this->db->select('item.itemAutoID, item.itemCategoryID, item.ItemCatDescription,item.seconeryItemCode');
        $this->db->where('comdet.companyID', $companyID);
        $this->db->join('srp_erp_invoice_commision com ', ' comdet.commissionAutoID = com.commissionAutoID AND com.approvedYN = 1 ', 'left');
        $this->db->join('(SELECT invdet.invoiceDetailsAutoID,invdet.itemAutoID AS itemAutoID, itemcat.itemCategoryID AS itemCategoryID, itemcat.description AS ItemCatDescription,
	            ItemMaster.seconeryItemCode AS seconeryItemCode FROM srp_erp_customerinvoicedetails invdet 
		        LEFT JOIN srp_erp_itemmaster ItemMaster ON invdet.itemAutoID = ItemMaster.itemAutoID 
		        LEFT JOIN srp_erp_itemcategory itemcat ON itemcat.itemCategoryID = ItemMaster.subcategoryID)item', 'item.invoiceDetailsAutoID = comdet.invoiceDetailID','left');
        $this->db->group_by('itemAutoID');
        $this->db->order_by('itemCategoryID ASC');
        $output['category'] = $this->db->get(' srp_erp_invoice_commission_detail comdet')->result_array();

        $items = '';
        $groupby = '';
        if($output['category']){
            foreach ($output['category'] as $key => $val) {
                $seconeryItemCode=$val['seconeryItemCode'];
                $itemAutoID = $val['itemAutoID'];

                $items .= " SUM(if(item.itemAutoID='$itemAutoID', item.requestedQty,0)) as  '$seconeryItemCode' ,";
            }
        }
        if($commissionAnalysisType == 1){
            $groupby = "GROUP BY  comdet.salesPersonEmpID";
        }elseif ($commissionAnalysisType == 2){
            $groupby = "GROUP BY  comdet.DesignationID ,comdet.salesPersonEmpID";
        }else{
            $groupby = "GROUP BY  comdet.salesPersonEmpID,comdet.DesignationID 	";
        }

        $output['icdetails']=$this->db->query("SELECT
            comdet.salesPersonEmpID AS empID,
            emp.Ename2 AS employee,
            $items
            comdet.DesignationID as DesignationID,
            desig.DesDescription as DesDescription,
            SUM(commissionAmount) AS commissionAmount 
            FROM
            srp_erp_invoice_commission_detail comdet
            JOIN srp_erp_invoice_commision com ON comdet.commissionAutoID = com.commissionAutoID AND com.approvedYN =1
            LEFT JOIN srp_employeesdetails emp ON emp.EIdNo =comdet.salesPersonEmpID AND emp.Erp_companyID = $companyID AND emp.empConfirmedYN = 1 AND emp.isDischarged = 0
	        LEFT JOIN srp_designation desig ON comdet.designationID = desig.DesignationID  AND desig.isDeleted = 0 AND desig.Erp_companyID = $companyID 
            LEFT JOIN srp_erp_customerinvoicedetails item ON item.invoiceDetailsAutoID = comdet.invoiceDetailID 
            LEFT JOIN srp_erp_customerinvoicemaster invmaster ON invmaster.invoiceAutoID = item.invoiceAutoID 

    WHERE
            comdet.companyID = $companyID  $date $groupby ")->result_array();
        return $output;
    }

    function update_new_commission_amount()
    {
        
        $commissionDetailID = $this->input->post('commissionDetailID');
        $commissoinAmount = $this->input->post('commissoinAmount');
              
        $data['commissionAmount'] = $commissoinAmount;
        $this->db->trans_start();
        $this->db->where('commissionDetailID', $commissionDetailID);
        $this->db->update('srp_erp_invoice_commission_detail', $data);
        //$company_currency_decimal = get_company_currency_decimal();
        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            return true;
        }else{
           return false;
        }
    }

}