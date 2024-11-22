<?php

class Approvel_user_model extends ERP_Model
{

    function save_approveluser()
    {
        $employeeid =  $this->input->post('employeeid');
        $employee =  trim($this->input->post('employee') ?? '');
        //$criteria =$this->input->post('criteria-chk');
        $documentid=$this->input->post('documentid');
        $approvalChecklistYN_val=$this->input->post('approvalChecklistYN_val');
        $companyID=current_companyID();
        $expenseClaimCategory=$this->input->post('expenseClaimCategory');
        $specialUserCheck=$this->input->post('specialUserCheck');

        $typeID = trim($this->input->post('type') ?? ''); /** added : almansoori chnges for personal application And for Trvale Request */
        $criteriaID = trim($this->input->post('criteria') ?? '') ? trim($this->input->post('criteria') ?? '') : 0;

        if($documentid=='EC'){

            if($specialUserCheck==1){
                $data['specificUser'] = $specialUserCheck;
            }else{
                $data['specificUser'] = '';
            }

            if (trim($this->input->post('expenseCategorycheck') ?? '')=='EC'){
                $data['typeID'] = $expenseClaimCategory;
            }
            else{
                $data['typeID'] = '';
            }
        }
        else{
            $data['specificUser'] = '';
        }

        if (!trim($this->input->post('approvalUserID') ?? '') && $this->input->post('levelno')!=0) {

            if($documentid == 'ATT'){
                $this->db->select('levelNo');
                $this->db->from('srp_erp_approvalusers');
                $this->db->where('levelNo', trim($this->input->post('levelno') ?? ''));
                $this->db->where('documentid', $documentid);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $detail = $this->db->get()->result_array();
                if (!empty($detail)) {
                    $this->session->set_flashdata('w',
                        'Approvel Level : ' . trim($this->input->post('levelno') ?? '') . ' already Exists for ' . $documentid . ' .');
    
                    return array('status' => FALSE);
                }
            }
            if($documentid == 'PAA'){ /** added : almansoori chnges for personal application */
                $this->db->select('levelNo');
                $this->db->from('srp_erp_approvalusers');
                $this->db->where('levelNo', trim($this->input->post('levelno') ?? ''));
                $this->db->where('documentid', trim($this->input->post('documentid') ?? ''));
                $this->db->where('typeID', trim($this->input->post('type') ?? ''));
                $this->db->where('employeeID', trim($this->input->post('employeeid') ?? ''));
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $order_detail = $this->db->get()->result_array();
    
                if (!empty($order_detail)) {
                    $this->session->set_flashdata('w',
                        'Approvel User : ' . trim($this->input->post('documentid') ?? '') . ' ' . trim($this->input->post('employee') ?? '') . '  already Exists.');
    
                    return array('status' => FALSE);
                }
            }else if($documentid == 'TRQ'){
                $this->db->select('levelNo');
                $this->db->from('srp_erp_approvalusers');
                $this->db->where('levelNo', trim($this->input->post('levelno') ?? ''));
                $this->db->where('documentid', trim($this->input->post('documentid') ?? ''));
                $this->db->where('typeID', trim($this->input->post('type') ?? ''));
                $this->db->where('employeeID', trim($this->input->post('employeeid') ?? ''));
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $order_detail = $this->db->get()->result_array();
    
                if (!empty($order_detail)) {
                    $this->session->set_flashdata('w',
                        'Approvel User : ' . trim($this->input->post('documentid') ?? '') . ' ' . trim($this->input->post('employee') ?? '') . '  already Exists.');
    
                    return array('status' => FALSE);
                }
            }
            else if($documentid == 'EC'){ 

                $this->db->select('levelNo');
                $this->db->from('srp_erp_approvalusers');
                $this->db->where('levelNo', trim($this->input->post('levelno') ?? ''));
                $this->db->where('documentID', trim($this->input->post('documentid') ?? ''));
                $this->db->where('typeID', trim($this->input->post('expenseClaimCategory') ?? ''));
                $this->db->where('employeeID', trim($this->input->post('employeeid') ?? ''));
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $this->db->where('specificUser', trim($this->input->post('specialUserCheck') ?? ''));
                $order_detail = $this->db->get()->result_array();
    
                if (!empty($order_detail)) {
                    $this->session->set_flashdata('w',
                        'Approvel User : ' . trim($this->input->post('documentid') ?? '') . ' ' . trim($this->input->post('employee') ?? '') . '  already Exists.');
    
                    return array('status' => FALSE);
                }
            }
            else{
                // $this->db->select('levelNo');
                // $this->db->from('srp_erp_approvalusers');
                // $this->db->where('levelNo', trim($this->input->post('levelno') ?? ''));
                // $this->db->where('documentid', trim($this->input->post('documentid') ?? ''));
                // $this->db->where('employeeID', trim($this->input->post('employeeid') ?? ''));
                // $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                // $order_detail = $this->db->get()->result_array();
                $approvalType = getApprovalTypesONDocumentCode(trim($this->input->post('documentid') ?? ''), $this->common_data['company_data']['company_id']);

                if($approvalType['approvalType'] == 6) {  //category and segment base

                    $this->db->select('levelNo');
                    $this->db->from('srp_erp_approvalusers');
                    $this->db->where('levelNo', trim($this->input->post('levelno') ?? ''));
                    $this->db->where('documentid', trim($this->input->post('documentid') ?? ''));
                    $this->db->where('employeeID', trim($this->input->post('employeeid') ?? ''));
                    $this->db->where('segmentID', trim($this->input->post('segmentid') ?? ''));
                    $this->db->where('typeID', trim($this->input->post('docCategoryType') ?? ''));
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $order_detail = $this->db->get()->result_array();
                }else if($approvalType['approvalType'] == 5){ //category base
                    $this->db->select('levelNo');
                    $this->db->from('srp_erp_approvalusers');
                    $this->db->where('levelNo', trim($this->input->post('levelno') ?? ''));
                    $this->db->where('documentid', trim($this->input->post('documentid') ?? ''));
                    $this->db->where('employeeID', trim($this->input->post('employeeid') ?? ''));
                   // $this->db->where('segmentID', trim($this->input->post('segmentid') ?? ''));
                    $this->db->where('typeID', trim($this->input->post('docCategoryType') ?? ''));
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $order_detail = $this->db->get()->result_array();
                }else if($approvalType['approvalType'] == 3 || $approvalType['approvalType'] == 4){ //segmet base , amount and segment base
                    $this->db->select('levelNo');
                    $this->db->from('srp_erp_approvalusers');
                    $this->db->where('levelNo', trim($this->input->post('levelno') ?? ''));
                    $this->db->where('documentid', trim($this->input->post('documentid') ?? ''));
                    $this->db->where('employeeID', trim($this->input->post('employeeid') ?? ''));
                    $this->db->where('segmentID', trim($this->input->post('segmentid') ?? ''));
                   // $this->db->where('typeID', trim($this->input->post('docCategoryType') ?? ''));
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $order_detail = $this->db->get()->result_array();
                }
                else{ //standard

                    $this->db->select('levelNo');
                    $this->db->from('srp_erp_approvalusers');
                    $this->db->where('levelNo', trim($this->input->post('levelno') ?? ''));
                    $this->db->where('documentid', trim($this->input->post('documentid') ?? ''));
                    $this->db->where('employeeID', trim($this->input->post('employeeid') ?? ''));
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $order_detail = $this->db->get()->result_array();

                }

                if (!empty($order_detail)) {
                    $this->session->set_flashdata('w',
                        'Approvel User : ' . trim($this->input->post('documentid') ?? '') . ' ' . trim($this->input->post('employee') ?? '') . '  already Exists.');

                    return array('status' => FALSE);
                }
            }

            $levelno = $this->input->post('levelno');

            if ($levelno != 1) {
                $levelno += -1;
                $documentid = $this->input->post('documentid');
                $employeeid = $this->input->post('employeeid');
                $companyID=current_companyID();
                if($documentid == 'PAA'){ /** added : almansoori chnges for personal application */
                    $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo = {$levelno} AND documentid = '{$documentid}' AND typeID = $typeID AND companyID=$companyID ")->row_array();
                }
                else if($documentid == 'TRQ'){
                    $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo = {$levelno} AND documentid = '{$documentid}' AND typeID = $typeID AND companyID=$companyID ")->row_array(); 
                }
                else{
                    $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo = {$levelno} AND documentid = '{$documentid}' AND companyID=$companyID ")->row_array();
                }
                if (empty($exist)) {
                    $this->session->set_flashdata('e',
                        'Approvel User : Level ' . $levelno . ' not available for ' . trim($this->input->post('documentid') ?? ''));

                    return array('status' => FALSE);
                }
            }
            $companyID=current_companyID();
            $documentid = $this->input->post('documentid');
            if($documentid == 'PAA'){ /** added : almansoori chnges for personal application */
                $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo = 0 AND documentid = '{$documentid}' AND typeID=$typeID AND companyID=$companyID ")->row_array();
            }
            else if($documentid == 'TRQ'){
                $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo = 0 AND documentid = '{$documentid}' AND typeID = $typeID AND companyID=$companyID ")->row_array(); 
            }else{
                $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo = 0 AND documentid = '{$documentid}' AND companyID=$companyID ")->row_array();
            }
            if (!empty($exist)) {
                $this->session->set_flashdata('e','This document is already assigned as No Approval. Please delete the record and create the approval levels again');

                return array('status' => FALSE);
            }

        }else{
            if($this->input->post('levelno')==0){
                $documentid = $this->input->post('documentid');
                if($documentid == 'PAA'){ /** added : almansoori chnges for personal application */
                    $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo >0 AND documentid = '{$documentid}' AND typeID=$typeID AND companyID=$companyID ")->row_array();
                }
                else if($documentid == 'TRQ'){
                    $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo >0 AND documentid = '{$documentid}' AND typeID = $typeID AND companyID=$companyID ")->row_array(); 
                }else{
                    $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo >0 AND documentid = '{$documentid}' AND companyID=$companyID ")->row_array();
                }
                if (!empty($exist)) {
                    $this->session->set_flashdata('e', 'This document is already assigned as No Approval. Please delete the record and create the approval levels again');

                    return array('status' => FALSE);
                }
            }
        }
        if($documentid == 'EC'){ 
            $this->db->select('levelNo');
            $this->db->from('srp_erp_approvalusers');
            $this->db->where('levelNo', trim($this->input->post('levelno') ?? ''));
            $this->db->where('documentID', trim($this->input->post('documentid') ?? ''));
            $this->db->where('typeID', trim($this->input->post('expenseClaimCategory') ?? ''));
            $this->db->where('employeeID', trim($this->input->post('employeeid') ?? ''));
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('specificUser', trim($this->input->post('specialUserCheck') ?? ''));
            $order_detail = $this->db->get()->result_array();

            if (!empty($order_detail)) {
                $this->session->set_flashdata('w',
                    'Approvel User : ' . trim($this->input->post('documentid') ?? '') . ' ' . trim($this->input->post('employee') ?? '') . '  already Exists.');

                return array('status' => FALSE);
            }
        }
        /** Amount based Approval policy */
        // if(trim($this->input->post('documentid') ?? '') == 'PO')
        // {
        //     $amountBasedApproval = getPolicyValues('ABA', 'All');
        //     if($amountBasedApproval == 1) {
        //         $data['fromAmount'] = $this->input->post('fromAmount');
        //         $data['toAmount'] = $this->input->post('toAmount');
        //     } else {
        //         $data['fromAmount'] = null;
        //         $data['toAmount'] = null;
        //     }
        // }

        $data['fromAmount'] = $this->input->post('fromAmount');
        $data['toAmount'] = $this->input->post('toAmount');
        $data['segmentID'] = $this->input->post('segmentid');

        if($documentid =='PRQ'){
            
            $data['criteriaID'] = $this->input->post('pr_single_source_val');
        }

        if($documentid =='GRV'){
            $grv_inspection_val =$this->input->post('grv_inspection_val');
            $data['criteriaID'] = $grv_inspection_val;

            if($grv_inspection_val==2){
                $exist_inspection_level = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE criteriaID = 2 AND documentid = '{$documentid}' AND companyID=$companyID ")->row_array();
                if (!empty($exist_inspection_level)) {
                    $this->session->set_flashdata('e','Inspection level already assigned');
    
                    return array('status' => FALSE);
                    exit;
                }
            }

        }

        if($this->input->post('levelno')!=0){
            $employee = explode('|', $this->input->post('employee'));
        }
        $document = explode('|', $this->input->post('document'));
        $data['levelNo'] = $this->input->post('levelno');
        $data['documentID'] = $this->input->post('documentid');

        /** added : almansoori chnges for personal application */
        if(trim($this->input->post('documentid') == 'PAA')){
            $data['typeID'] = $this->input->post('type');
            $data['criteriaID'] = $criteriaID;
        }

        if(trim($this->input->post('documentid') == 'TRQ')){
            $data['typeID'] = $this->input->post('type');
        }

        if(trim($this->input->post('documentid') ?? '') == 'PO' || trim($this->input->post('documentid') ?? '') == 'PRQ'){
            $data['typeID'] = $this->input->post('docCategoryType');
        }

        $data['groupID'] = $this->input->post('userGroupID');
        $data['document'] = $document[1];
        if($this->input->post('levelno')!=0) {
            if($employeeid == -1)
            {
                $data['employeeID'] = $employeeid;
                $data['employeeName'] = $employee[0];
            }else if($employeeid == -2){
                $data['employeeID'] = $employeeid;
                $data['employeeName'] = trim($this->input->post('employee') ?? '');
            }else if($employeeid == -3){
                $data['employeeID'] = $employeeid;
                $data['employeeName'] = trim($this->input->post('employee') ?? '');
            }else
            {
                $data['employeeID'] = $this->input->post('employeeid');
                $data['employeeName'] = $employee[1];
            }
        }

    
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        // if($criteria == 'on'){
        //     $data['criteriaID'] = 1; //checked
        // } else{
        //     $data['criteriaID'] = 0; //unchecked -defualt
        // }

        if( $approvalChecklistYN_val==1){
            $data['checkListYN'] = 1;
        }
        

        if (!$this->input->post('approvalUserID')) {
            $data['Status'] = 1;
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];            
            $result = $this->db->insert('srp_erp_approvalusers', $data);
            
            // To save Special User details
            // if($specialUserCheck==1){
            //     $lastID = $this->db->insert_id();
            //     $specialUsers=$this->input->post('specialUseremp');
            //     $specialUserData=[];
            //     foreach($specialUsers as $specialUser){
            //        $specialUserData=[
            //         'approvalUserID'=>$lastID,
            //         'empID'=>$specialUser,
            //         'createduserID'=>$this->common_data['current_userID'],
            //         'createdDateTime'=>$this->common_data['current_date']
            //        ];
            //        $this->db->insert('srp_erp_appoval_specific_users', $specialUserData);
            //     }
            // }

            if ($result) {
                $this->session->set_flashdata('s', 'Records Added Successfully');

                return TRUE;
            }
        } else {
            $this->db->where('approvalUserID', $this->input->post('approvalUserID'));
            $result = $this->db->update('srp_erp_approvalusers', $data);
            if ($result) {
                $this->session->set_flashdata('s', 'Records Updated Successfully');

                return TRUE;
            }
        }
    }

    function edit_approveluser()
    {
        $this->db->select('*');
        $this->db->where('approvalUserID', $this->input->post('id'));

        return $this->db->get('srp_erp_approvalusers')->row_array();
    }

    function delete_approveluser()
    {
        $id = $this->input->post('id');
        $companyID = $this->common_data['company_data']['company_id'];
        $row = $this->db->query("SELECT levelNo,documentID FROM srp_erp_approvalusers where approvalUserID={$id} AND companyID={$companyID}")->row_array();
        if($row['levelNo']>0){
            if ($row['levelNo'] != 1) {
                $row['levelNo'] += 1;
                $documentid = $row['documentID'];

                $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo = {$row['levelNo']} AND documentid = '{$documentid}' AND companyID={$companyID} ")->row_array();
                if ($exist) {
                    $this->session->set_flashdata('s',
                        'Unable to delete . Please delete Level No ' . $row['levelNo'] . ' - ' . $documentid . ' and continue');
                    return TRUE;
                }
            }
        }
        if($row['documentID']=='EC'){
            $this->db->where('approvalUserID', $this->input->post('id'));
            $result = $this->db->delete('srp_erp_appoval_specific_users');
        }
        $this->db->where('approvalUserID', $this->input->post('id'));
        $result = $this->db->delete('srp_erp_approvalusers');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');

            return TRUE;
        }
    }

    function fetch_emploee_using_group()
    {
        $this->db->select("EIdNo,ECode,Ename1,Ename2,Ename3,Ename4");
        $this->db->from('srp_erp_employeenavigation');
        $this->db->where('userGroupID', $this->input->post('id'));
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_employeenavigation.empID');
        $this->db->where('srp_employeesdetails.isDischarged', 0);

        return $this->db->get()->result_array();
    }

    function getApprovalTypeID()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $this->db->select("approvalType");
        $this->db->from('srp_erp_documentcodemaster');
        $this->db->where('companyID', $companyID);
        $this->db->where('documentID', $this->input->post('documentID'));

        return $this->db->get()->row_array();
      
    }

    function fetch_approval_user_modal()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $companies = $companyID;
        $groupCompanyID = $this->db->query(
            "SELECT companyGroupID 
             FROM srp_erp_companygroupdetails 
             WHERE srp_erp_companygroupdetails.companyID = $companyID"
        )->row('companyGroupID');

        if(!empty($groupCompanyID)){
            $companyList = $this->db->query(
                "SELECT companyID 
                    FROM srp_erp_companygroupdetails 
                    WHERE srp_erp_companygroupdetails.companyGroupID = $groupCompanyID"
            )->result_array();

            $companies = implode(',', array_column($companyList, 'companyID'));
        }

        //$companies = array_reverse($companies);
        /************************************************************************************************
         * No need to set a different logic to get leave approvals
         * So changed th case LA => LA1
         ************************************************************************************************/
        switch ($this->input->post('documentID')) {

            case 'LA';
                /*$convertFormat = convert_date_format_sql();
                $docSystemCode = $this->input->post('documentSystemCode');
                $data = $this->db->query("SELECT approvedbyEmpName,approvedYN,DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approvedDate,approvedbyEmpID,
                                          approvalComments,empID,documentCode,DATE_FORMAT(entryDate,\"" . $convertFormat . "\") AS entryDate,
                                          DATE_FORMAT(confirmedDate,\"" . $convertFormat . "\") AS confirmedDate,confirmedByName,currentLevelNo
                                          FROM `srp_erp_leavemaster` WHERE `leaveMasterID` ={$docSystemCode} ")->result_array();

                $data_arr['document_code'] = $data[0]['documentCode'];
                $data_arr['document_date'] = $data[0]['entryDate'];
                $data_arr['confirmed_date'] = $data[0]['confirmedDate'];
                $data_arr['conformed_by'] = $data[0]['confirmedByName'];

                $data_arr['approved'] = $data;*/
                /*$data_arr['approved'][0]['approvalLevelID'] = $data['currentLevelNo'];
                $data_arr['approved'][0]['Ename2'] = $data['approvedbyEmpName'];
                $data_arr['approved'][0]['approvedYN'] = $data['approvedYN'];
                $data_arr['approved'][0]['approveDate'] = $data['approvedDate'];
                $data_arr['approved'][0]['approvedComments'] = $data['approvalComments'];*/

                $companyID = current_companyID();
                $convertFormat = convert_date_format_sql();
                $documentID = $this->input->post('documentID');
                $documentSystemCode = $this->input->post('documentSystemCode');
                $data_arr = array();

                $requestForCancelYN = $this->db->query("SELECT requestForCancelYN FROM srp_erp_leavemaster t1 WHERE leaveMasterID = {$documentSystemCode}")->row('requestForCancelYN');
                $requestForCancelYNStr = ($requestForCancelYN == 1)? 'AND isCancel = 1': '';
                $data = $this->db->query("SELECT app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,approvalLevelID,approvedYN,approvedDate,approvedComments, documentCode,
                                  DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate,docConfirmedByEmpID,
                                  DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS docConfirmedDate,
                                  DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approveDate 
                                  FROM srp_erp_documentapproved app_tb
                                  JOIN srp_employeesdetails app_emp ON app_emp.EIdNo = app_tb.approvedEmpID
                                  WHERE documentID = '{$documentID}' AND documentSystemCode = '{$documentSystemCode}' AND companyID IN ({$companies}) 
                                  {$requestForCancelYNStr} ")->result_array();
                //echo $this->db->last_query();

                $data_arr['approved'] = $data;
                $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                $data_arr['conformed_by'] = $emp['Ename2'];

                return $data_arr;
                break;
            default:
                $convertFormat = convert_date_format_sql();
                $documentCode = $this->input->post('documentID');
                $documentSystemCode = $this->input->post('documentSystemCode');
                $data_arr = array();
                $this->db->select("IF(approvedYN = 1,`app_emp`.`EIdNo`,approvaluserntcon.EIdNo) as EIdNo,
                    IF(approvedYN = 1,`app_emp`.`ECode`,approvaluserntcon.ECode) as ECode,
                    IF(approvedYN = 1,`app_emp`.`Ename2`,approvaluserntcon.Ename2) as Ename2,
                    `approvalLevelID`,
                    `approvedYN`,
                    IF(approvedYN = 1,approvedDate,'-') as approvedDate,
                    IF(approvedYN = 1,approvedComments,'-') as approvedComments,
                    `documentCode`,
                    DATE_FORMAT( documentDate, \"%d-%m-%Y\" ) AS documentDate,
                    `docConfirmedByEmpID`,
                    DATE_FORMAT( docConfirmedDate, \"%d-%m-%Y\" ) AS docConfirmedDate,
                    IF(approvedYN = 1,DATE_FORMAT( approvedDate, \"%d-%m-%Y\" ) ,'-') as approveDate");
                $this->db->from('srp_erp_documentapproved');
                $this->db->where('srp_erp_documentapproved.documentID', $this->input->post('documentID'));
//                $this->db->where('appuser.documentID', $this->input->post('documentID'));
//                $this->db->where('appuser.companyID', $this->common_data['company_data']['company_id']);
                $this->db->where('documentSystemCode', $this->input->post('documentSystemCode'));
                $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = srp_erp_documentapproved.approvedEmpID','left');
                $this->db->join(" ( 
                                             SELECT * FROM srp_erp_approvalusers WHERE documentID = \"$documentCode\" AND companyID = \"$companyID\"
                                        )appuser", 'appuser.levelNo = srp_erp_documentapproved.approvalLevelID','left');

                $this->db->join('srp_employeesdetails approvaluserntcon', 'approvaluserntcon.EIdNo = appuser.employeeID','left');
                $this->db->where('srp_erp_documentapproved.companyID', $this->common_data['company_data']['company_id']);

                if($documentCode=='PV'){
                    $company_doc_approval_type = getApprovalTypesONDocumentCode('PV',$companyID);
                    
                    $segmentID = $this->db->query("SELECT segmentID FROM srp_erp_paymentvouchermaster where payVoucherAutoId = $documentSystemCode AND companyID = {$companyID}")->row_array();
                    
                  
                    $poLocalAmount = payment_voucher_total_value($documentSystemCode, 2, 0);

                    $poLocalAmount_max =$poLocalAmount+2;

                    if($company_doc_approval_type['approvalType']==1){

                    }else if($company_doc_approval_type['approvalType']==2){
                        
                        $this->db->where("((appuser.toAmount != 0 AND '{$poLocalAmount}' BETWEEN appuser.fromAmount AND appuser.toAmount) OR (appuser.toAmount = 0  AND '{$poLocalAmount}' BETWEEN appuser.fromAmount AND '{$poLocalAmount_max}'))");
                    
                    }else if($company_doc_approval_type['approvalType']==3){
                        $this->db->where('appuser.segmentID', $segmentID['segmentID']);
                    
                    }else if($company_doc_approval_type['approvalType']==4){
                        $this->db->where("((appuser.toAmount != 0 AND '{$poLocalAmount}' BETWEEN appuser.fromAmount AND appuser.toAmount) OR (appuser.toAmount = 0  AND '{$poLocalAmount}' BETWEEN appuser.fromAmount AND '{$poLocalAmount_max}'))");
                        $this->db->where('appuser.segmentID', $segmentID['segmentID']);
                    
                    }
                }

                $this->db->group_by('approvalLevelID');
                $data_arr['approved'] = $this->db->get()->result_array();
                $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
                $data_arr['conformed_by'] = $emp['Ename2'];

                return $data_arr;
        }
    }


    function fetch_all_approval_users_modal()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $documentID = $this->input->post('documentID');
        $systemID = $this->input->post('documentSystemCode');
        $singleSourcePR = getPolicyValues('SSPR', 'All');

        $companies = $companyID;
        $groupCompanyID = $this->db->query(
            "SELECT companyGroupID 
             FROM srp_erp_companygroupdetails 
             WHERE srp_erp_companygroupdetails.companyID = $companyID"
        )->row('companyGroupID');

        if(!empty($groupCompanyID)){
            $companyList = $this->db->query(
                "SELECT companyID 
                    FROM srp_erp_companygroupdetails 
                    WHERE srp_erp_companygroupdetails.companyGroupID = $groupCompanyID"
            )->result_array();

            $companies = implode(',', array_column($companyList, 'companyID'));
        }

        switch ($documentID) {

            case 'LA';
                $convertFormat = convert_date_format_sql();
                $companyApp = explode(',', $companies);

                $this->db->select('requestForCancelYN, coveringEmpID,empTB.leaveGroupID');
                $this->db->from('srp_erp_leavemaster');
                $this->db->join('srp_employeesdetails as empTB','srp_erp_leavemaster.empID = empTB.EIdNo');
                $this->db->where('leaveMasterID', $systemID);
                $this->db->where_in('companyID', $companyApp);
                $masterData = $this->db->get()->row_array();

                $this->db->select('*');
                $this->db->from('srp_erp_leave_covering_employee');
                $this->db->where('leaveapplicationID', $systemID);
                $covering_emp = $this->db->get()->result_array();

                if($covering_emp){
                    $coveringEmpID = $covering_emp;
                }else{
                    $coveringEmpID = [];
                }
                
                $requestForCancelYN = $masterData['requestForCancelYN'];

                
                $this->db->select("approvalLevelID,approvedYN,approvedDate,approvedComments,documentCode,docConfirmedByEmpID,
                                  DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate, '' AS Ename2,
                                  DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS docConfirmedDate,
                                  DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approveDate");
                $this->db->from('srp_erp_documentapproved');
                $this->db->where('srp_erp_documentapproved.documentID', $documentID);
                $this->db->where('documentSystemCode', $systemID);
                $this->db->where_in('srp_erp_documentapproved.companyID', array_reverse($companyApp));
                if($requestForCancelYN == 1){
                    $this->db->where('isCancel', 1);
                }
                
                $approved = $this->db->get()->result_array();
    
                $leaveApprovalWithGroup = getPolicyValues('LAG', 'All');

                if($leaveApprovalWithGroup == 1){
                    $setupData = getLeaveApprovalSetup('Y',null,$masterData['leaveGroupID']);
                }else{
                    $setupData = getLeaveApprovalSetup('Y');
                }
              
                $approvalSetup = $setupData['approvalSetup'];
                $approvalEmp_arr = $setupData['approvalEmp'];
                $managers = $this->db->query("SELECT * FROM (
                                                 SELECT repManager, repManagerName, currentLevelNo,HOD,HODName
                                                 FROM srp_erp_leavemaster AS empTB
                                                 LEFT JOIN (
                                            SELECT hod_id AS HOD,EmpID AS EmpNew,t3.Ename2 AS HODName
                
                                            FROM srp_empdepartments  AS dpt
                                            JOIN srp_departmentmaster AS departmentmaster  ON departmentmaster.DepartmentMasterID = dpt.DepartmentMasterID
                                            JOIN srp_employeesdetails AS t3 ON departmentmaster.hod_id=t3.EIdNo AND t3.Erp_companyID IN ({$companies})
                                            WHERE dpt.isPrimary = 1
                                            ) AS HodData ON empTB.empID = HodData.EmpNew

                                                 LEFT JOIN (
                                                     SELECT empID, managerID AS repManager, Ename2 AS repManagerName  FROM srp_erp_employeemanagers AS t1
                                                     JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo 
                                                     -- AND Erp_companyID={$companyID}  -- SME-3092
                                                     WHERE active = 1 
                                                     AND companyID IN ({$companies})
                                                 ) AS repoManagerTB ON empTB.empID = repoManagerTB.empID
                                                 WHERE companyID IN ({$companies}) AND leaveMasterID={$systemID}
                                             ) AS empData

                                             

                                             LEFT JOIN (
                                                  SELECT managerID AS topManager, Ename2 AS topManagerName, empID AS topEmpID
                                                  FROM srp_erp_employeemanagers AS t1
                                                  JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo AND Erp_companyID IN ({$companies})
                                                  WHERE companyID IN ({$companies}) AND active = 1
                                             ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID 
                                             
                                             ")->row_array();


                $approvalData = []; $k = 0;
                foreach($approved as $key=>$row){
                    $thisLevel = $row['approvalLevelID'];

                    $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $thisLevel);
                    $arr = array_map(function ($k) use ($approvalSetup) {
                        return $approvalSetup[$k];
                    }, $keys);

                    $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';

                    if($approvalType == 3){
                        $hrManagerID = (array_key_exists($thisLevel, $approvalEmp_arr)) ? $approvalEmp_arr[$thisLevel] : [];
                        $hrManagerID = array_column($hrManagerID, 'empID');

                        if(!empty($hrManagerID)){
                            foreach($hrManagerID as $hrManagerRow){
                                $hrEmpData = fetch_employeeNo($hrManagerRow);
                                $approved[$key]['Ename2'] = $hrEmpData['Ename2'];
                                $approvalData[] = $approved[$key];
                            }
                        }
                        else{
                            $approvalData[] = $approved[$key];
                        }
                    }
                    else if($approvalType == 4){ //need to change to hod
                        /*echo $approvalType.' <br/> cover :';
                        echo $coveringEmpID.' <br/>';*/
                        
                        if(count($coveringEmpID)>0){

                            foreach($coveringEmpID as $val){
                                $coveringEmpData = fetch_employeeNo($val['coveringID']);
                                $approved[$key]['Ename2'] = $coveringEmpData['Ename2'];
                                $approvalData[] = $approved[$key];
                            }
                        }
                        else{
                            $approvalData[] = $approved[$key];
                        }
                    }
                    else{
                        $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                        if( !empty($managers[$managerType]) ){
                            $approved[$key]['Ename2'] = $managers[$managerType.'Name'];
                        }
                        $approvalData[] = $approved[$key];

                    }
                }

                $data_arr['approved'] = $approvalData;
                $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                $empData = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                $data_arr['conformed_by'] = $empData['Ename2'];
                $data_arr['requestForCancelYN'] = $requestForCancelYN;


                return $data_arr;
                break;
            case 'PRQ';
                $convertFormat = convert_date_format_sql();
                $data_arr = array();

                $company_doc_approval_type = getApprovalTypesONDocumentCode('PRQ',$companyID);
                $approval_master_data_arr = $this->db->query("SELECT segmentID,itemCategoryID FROM srp_erp_purchaserequestmaster where purchaseRequestID = $systemID AND companyID = {$companyID}")->row_array();
                $segmentID =$approval_master_data_arr['segmentID'];
                $documentTotal = $this->db->query("SELECT srp_erp_purchaserequestmaster.purchaseRequestID AS purchaseRequestID, srp_erp_purchaserequestmaster.companyLocalExchangeRate, transactionCurrencyID, transactionCurrency,
                    ( det.transactionAmount - det.discountAmount)+ IFNULL( gentax.gentaxamount, 0 ) AS total_value 
                    FROM srp_erp_purchaserequestmaster
                        LEFT JOIN ( SELECT SUM( totalAmount ) AS transactionAmount, purchaseRequestID , discountAmount FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID ) det ON det.purchaseRequestID = srp_erp_purchaserequestmaster.purchaseRequestID
                        LEFT JOIN (
                                SELECT ifnull( SUM( amount ), 0 ) AS gentaxamount, documentMasterAutoID 
                                FROM srp_erp_taxledger 
                                WHERE documentID = 'PRQ' AND documentDetailAutoID IS NULL AND companyID = {$companyID} 
                                GROUP BY documentMasterAutoID 
                        ) gentax ON ( gentax.documentMasterAutoID = srp_erp_purchaserequestmaster.purchaseRequestID ) 
                    WHERE
                    srp_erp_purchaserequestmaster.purchaseRequestID = {$systemID} AND srp_erp_purchaserequestmaster.companyID = {$companyID}")->row_array();

                $poLocalAmount = $documentTotal['total_value'] /$documentTotal['companyLocalExchangeRate'];

                $amount_base_filters ='';
                $where_single_source ='';

                if($singleSourcePR==1){
                    $where_single_source = "AND ((prmaster.isSingleSourcePr= 1 AND ap.criteriaID =1) or (prmaster.isSingleSourcePr= 0 AND ap.criteriaID =0))";
                }

                $poLocalAmount_max =$poLocalAmount+2;
                if($company_doc_approval_type['approvalType']==1){

                }else if($company_doc_approval_type['approvalType']==2){
                    
                    $amount_base_filters =" AND ((ap.toAmount != 0 AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND ap.toAmount) OR (ap.toAmount = 0  AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND '{$poLocalAmount_max}'))";
                   
                }else if($company_doc_approval_type['approvalType']==3){

                    $amount_base_filters =" AND ap.segmentID ={$segmentID}";
                   
                }else if($company_doc_approval_type['approvalType']==4){
                    $amount_base_filters =" AND ((ap.toAmount != 0 AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND ap.toAmount) OR (ap.toAmount = 0  AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND '{$poLocalAmount_max}')) AND ap.segmentID ={$segmentID}";
                   
                }else if($company_doc_approval_type['approvalType']==5){
                   // $this->db->where("((ap.toAmount != 0 AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND ap.toAmount) OR (ap.toAmount = 0  AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND '{$poLocalAmount_max}'))");
                    $amount_base_filters =" AND ((ap.toAmount != 0 AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND ap.toAmount) OR (ap.toAmount = 0  AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND '{$poLocalAmount_max}')) AND ap.typeID ={$approval_master_data_arr['itemCategoryID']}";

                }
                /*Old Query*/
               /* $data_arr['approved'] = $this->db->query("SELECT
                            IF(ap.employeeID=-1,reporting.EIdNo,app_emp.EIdNo) as EIdNo,
                            IF(ap.employeeID=-1,reporting.ECode, app_emp.ECode) as ECode,
                            IF(ap.employeeID=-1,reporting.Ename2,`app_emp`.`Ename2`) as Ename2,


                                `approvalLevelID`,
                                srp_erp_documentapproved.approvedYN,
                                srp_erp_documentapproved.approvedDate,
                                `approvedComments`,
                                srp_erp_documentapproved.documentCode,
                                `docConfirmedByEmpID`,
                                DATE_FORMAT(srp_erp_documentapproved.documentDate,\"" . $convertFormat . "\") AS documentDate,
                                DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS docConfirmedDate,
                                DATE_FORMAT(srp_erp_documentapproved.approvedDate,\"" . $convertFormat . "\") AS approveDate
                            FROM
                                `srp_erp_documentapproved`
                                JOIN `srp_erp_approvalusers` `ap` ON `ap`.`levelNo` = `srp_erp_documentapproved`.`approvalLevelID`
                                LEFT JOIN `srp_employeesdetails` `app_emp` ON `app_emp`.`EIdNo` = `ap`.`employeeID`
                                    LEFT JOIN srp_erp_purchaserequestmaster prmaster on prmaster.purchaseRequestID = srp_erp_documentapproved.documentSystemCode

                                    LEFT JOIN (SELECT employee.EIdNo,empID FROM srp_erp_employeemanagers managerTb JOIN srp_employeesdetails employee ON managerTb.managerID=employee.EIdNo
                            where managerTb.active=1 ) employeemanager on employeemanager.empID = prmaster.requestedEmpID
                            LEFT JOIN srp_employeesdetails reporting on reporting.EIdNo = employeemanager.EIdNo

                            WHERE
                                `srp_erp_documentapproved`.`documentID` = '{$documentID}'
                                AND `ap`.`documentID` = '{$documentID}'
                                AND `documentSystemCode` = '{$systemID}'
                                AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
                                AND `ap`.`companyID` = '{$companyID}'")->result_array();*/

                $data_arr['approved'] = $this->db->query("SELECT
                                    IF
                                        ( ap.employeeID =- 1, reporting.EIdNo, app_emp.EIdNo ) AS EIdNo,
                                    IF
                                        ( ap.employeeID =- 1, reporting.ECode, app_emp.ECode ) AS ECode,
                                    IF
                                        ( ap.employeeID =- 1, reporting.Ename2, `app_emp`.`Ename2` ) AS Ename2,
                                        `levelNo` AS `approvalLevelID`,
                                        approved.approvedYN,
                                        srp_erp_documentapproved.approvedDate,
                                        `approvedComments`,
                                        DATE_FORMAT( srp_erp_documentapproved.approvedDate, \"%Y-%m-%d\" ) AS approveDate,
                                        approved.documentCode,
                                        approved.`docConfirmedByEmpID`,
                                        approved.documentDate  AS documentDate,
                                        approved.docConfirmedDate AS docConfirmedDate 
                                    FROM
                                        `srp_erp_approvalusers` `ap`
                                        LEFT JOIN `srp_employeesdetails` `app_emp` ON `app_emp`.`EIdNo` = `ap`.`employeeID`
                                            LEFT JOIN (
                                        SELECT
                                            approvedYN,
                                            approvalLevelID,
                                            documentCode,
                                            DATE_FORMAT( documentDate, '%d-%m-%Y' ) AS documentDate,
                                            DATE_FORMAT( docConfirmedDate, '%d-%m-%Y' ) AS docConfirmedDate,
                                            docConfirmedByEmpID AS docConfirmedByEmpID 
                                        FROM
                                            srp_erp_documentapproved 
                                        WHERE
                                            `documentID` = '{$documentID}'
                                            AND `companyID` = {$companyID} 
                                            AND `documentSystemCode` = '{$systemID}'
                                        ) approved ON `ap`.`levelNo` = `approved`.`approvalLevelID`
                                        LEFT JOIN srp_erp_purchaserequestmaster prmaster ON prmaster.purchaseRequestID = '{$systemID}'
                                        LEFT JOIN ( SELECT employee.EIdNo, empID FROM srp_erp_employeemanagers managerTb JOIN srp_employeesdetails employee ON managerTb.managerID = employee.EIdNo WHERE managerTb.active = 1 ) employeemanager ON employeemanager.empID = prmaster.requestedEmpID
                                        LEFT JOIN srp_employeesdetails reporting ON reporting.EIdNo = employeemanager.EIdNo 	
                                        LEFT JOIN srp_erp_documentapproved  ON `ap`.`levelNo` = `srp_erp_documentapproved`.`approvalLevelID` AND `srp_erp_documentapproved`.`companyID` = '{$companyID}' 
                                                                                AND `srp_erp_documentapproved`.`documentID` = '{$documentID}'  AND `documentSystemCode` = '{$systemID}' AND  
                                                                                (CASE WHEN( ap.employeeID = '-1' ) THEN srp_erp_documentapproved.approvedEmpID = reporting.EIdNo ELSE srp_erp_documentapproved.approvedEmpID = ap.employeeID END)
                                    WHERE
                                        `ap`.`documentID` = '{$documentID}' 
                                        AND `ap`.`companyID` = '{$companyID}' $amount_base_filters $where_single_source")->result_array();


                if(!empty($data_arr['approved'])){
                    $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                    $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                    $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                    $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                    //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
                    $data_arr['conformed_by'] = $emp['Ename2'];
                }
                return $data_arr;
                break;

                case 'WFH';
                    $convertFormat = convert_date_format_sql();
                    $data_arr = array();

                    $this->db->select("approvalLevelID,approvedYN,approvedDate,approvedComments,documentCode,docConfirmedByEmpID,
                        DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate, '' AS Ename2,
                        DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS docConfirmedDate,
                        DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approveDate,
                        ap.employeeID");
                    $this->db->from('srp_erp_documentapproved');
                    $this->db->join("srp_erp_approvalusers AS ap", "ap.levelNo = srp_erp_documentapproved.approvalLevelID AND ap.documentID = 'WFH' AND ap.companyID = '{$companyID}'");
                    $this->db->where('srp_erp_documentapproved.documentID', 'WFH');
                    $this->db->where('documentSystemCode', $systemID);
                    $this->db->where('srp_erp_documentapproved.companyID', $companyID);
                    $this->db->order_by('srp_erp_documentapproved.approvalLevelID');
                    $approved = $this->db->get()->result_array();

                    $managers = $this->db->query("SELECT * FROM (
                        SELECT repManager, repManagerName, currentLevelNo,HOD,HODName
                        FROM srp_erp_work_from_home AS empTB
                        JOIN srp_erp_documentapproved ON empTB.documentID = 'WFH'
                        LEFT JOIN (
                            SELECT hod_id AS HOD,EmpID AS EmpNew,t3.Ename2 AS HODName

                            FROM srp_empdepartments  AS dpt
                            JOIN srp_departmentmaster AS departmentmaster  ON departmentmaster.DepartmentMasterID = dpt.DepartmentMasterID
                            JOIN srp_employeesdetails AS t3 ON departmentmaster.hod_id=t3.EIdNo AND t3.Erp_companyID ='$companyID'
                            WHERE dpt.isPrimary = 1
                            ) AS HodData ON empTB.empID = HodData.EmpNew

                        LEFT JOIN (
                            SELECT empID, managerID AS repManager, Ename2 AS repManagerName  FROM srp_erp_employeemanagers AS t1
                            JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo 
                            WHERE active = 1 
                            AND t1.companyID ='$companyID'
                        ) AS repoManagerTB ON empTB.empID = repoManagerTB.empID
                        WHERE empTB.companyID ='$companyID' AND wfhID ={$systemID}
                        ) AS empData

                        LEFT JOIN (
                            SELECT managerID AS topManager, Ename2 AS topManagerName, empID AS topEmpID
                            FROM srp_erp_employeemanagers AS t1
                            JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo AND t2.Erp_companyID = '$companyID'
                            WHERE t1.companyID ='$companyID' AND active = 1
                        ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID 
            
                    ")->row_array();
                    
                    foreach($approved as $key => $approve_details){

                        $employeeID = $approve_details['employeeID'];

                        if($employeeID == -1){
                            $approved[$key]['Ename2'] =  $managers['repManagerName'];
                            $approved[$key]['levelUserID'] =  $managers['repManager'];
                        }elseif($employeeID == -2){
                            $approved[$key]['Ename2'] =  $managers['HODName'];
                            $approved[$key]['levelUserID'] =  $managers['HOD'];
                        }elseif($employeeID == -3){
                            $approved[$key]['Ename2'] =  $managers['topManagerName'];
                            $approved[$key]['levelUserID'] =  $managers['topManager'];
                        }else{
                            $employee_details = fetch_employeeNo($employeeID);
                            $approved[$key]['Ename2'] =  $employee_details['Ename2'];
                            $approved[$key]['levelUserID'] =  $employee_details['EIdNo'];
                        }
                    }
                    $data_arr['approved'] = $approved;
                    $data_arr['document_code'] = isset($data_arr['approved'][0]['documentCode']) ? $data_arr['approved'][0]['documentCode'] : '';
                    $data_arr['document_date'] = isset($data_arr['approved'][0]['documentDate']) ? $data_arr['approved'][0]['documentDate'] : '' ;
                    $data_arr['confirmed_date'] = isset($data_arr['approved'][0]['docConfirmedDate']) ? $data_arr['approved'][0]['docConfirmedDate'] : '';

                    $confirmedEmpID = isset($data_arr['approved'][0]['docConfirmedByEmpID']) ? $data_arr['approved'][0]['docConfirmedByEmpID'] : '';
                    $emp = fetch_employeeNo($confirmedEmpID);
                    //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
                    $data_arr['conformed_by'] = $emp['Ename2'];

                    return $data_arr;
                    break;
            
            case 'SAR':
                $convertFormat = convert_date_format_sql();
                $data_arr = array();

                $this->db->select("approvalLevelID,approvedYN,approvedDate,approvedComments,documentCode,docConfirmedByEmpID,
                    DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate, '' AS Ename2,
                    DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS docConfirmedDate,
                    DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approveDate,
                    ap.employeeID");
                $this->db->from('srp_erp_documentapproved');
                $this->db->join("srp_erp_approvalusers AS ap", "ap.levelNo = srp_erp_documentapproved.approvalLevelID AND ap.documentID = 'SAR' AND ap.companyID = '{$companyID}'");
                $this->db->where('srp_erp_documentapproved.documentID', 'SAR');
                $this->db->where('documentSystemCode', $systemID);
                $this->db->where('srp_erp_documentapproved.companyID', $companyID);
                $this->db->order_by('srp_erp_documentapproved.approvalLevelID');
                $approved = $this->db->get()->result_array();

                // $data_arr['approved'] = $this->db->query("SELECT IF(ap.employeeID=-1,reporting.EIdNo,app_emp.EIdNo) as EIdNo,
                //         IF(ap.employeeID=-1,reporting.ECode, app_emp.ECode) as ECode, IF(ap.employeeID=-1,reporting.Ename2,app_emp.Ename2) as Ename2,
                //         levelNo AS approvalLevelID, doc_app.approvedYN, doc_app.approvedDate, approvedComments, doc_app.documentCode, docConfirmedByEmpID, 
                //         DATE_FORMAT(doc_app.documentDate, '{$convertFormat}') AS documentDate, DATE_FORMAT(docConfirmedDate, '{$convertFormat}') AS docConfirmedDate,
                //         DATE_FORMAT(doc_app.approvedDate, '{$convertFormat}') AS approveDate
                //         FROM srp_erp_approvalusers AS ap
                //         LEFT JOIN srp_employeesdetails AS app_emp ON app_emp.EIdNo = ap.employeeID
                //         LEFT JOIN srp_erp_pay_salaryadvancerequest prmaster ON prmaster.masterID = '{$systemID}'
                //         LEFT JOIN (
                //             SELECT employee.EIdNo,empID FROM srp_erp_employeemanagers managerTb 
                //             JOIN srp_employeesdetails employee ON managerTb.managerID=employee.EIdNo
                //             WHERE managerTb.active = 1 
                //         ) AS employeemanager ON employeemanager.empID = prmaster.empID
                //         LEFT JOIN srp_employeesdetails reporting on reporting.EIdNo = employeemanager.EIdNo
                //         LEFT JOIN srp_erp_documentapproved AS doc_app ON ap.levelNo = doc_app.approvalLevelID AND doc_app.documentID = '{$documentID}' AND documentSystemCode = '{$systemID}' 
                //         AND doc_app.companyID = '{$companyID}' AND (CASE WHEN( ap.employeeID = '-1' ) THEN doc_app.approvedEmpID = reporting.EIdNo ELSE doc_app.approvedEmpID = ap.employeeID END)
                //         WHERE ap.documentID = '{$documentID}' AND ap.companyID = '{$companyID}'")->result_array();

                $managers = $this->db->query("SELECT * FROM (
                    SELECT repManager, repManagerName, currentLevelNo,HOD,HODName
                    FROM srp_erp_pay_salaryadvancerequest AS empTB
                    JOIN srp_erp_documentapproved ON empTB.documentID = 'SAR'
                    LEFT JOIN (
                        SELECT hod_id AS HOD,EmpID AS EmpNew,t3.Ename2 AS HODName

                        FROM srp_empdepartments  AS dpt
                        JOIN srp_departmentmaster AS departmentmaster  ON departmentmaster.DepartmentMasterID = dpt.DepartmentMasterID
                        JOIN srp_employeesdetails AS t3 ON departmentmaster.hod_id=t3.EIdNo AND t3.Erp_companyID ='$companyID'
                        WHERE dpt.isPrimary = 1
                        ) AS HodData ON empTB.empID = HodData.EmpNew

                    LEFT JOIN (
                        SELECT empID, managerID AS repManager, Ename2 AS repManagerName  FROM srp_erp_employeemanagers AS t1
                        JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo 
                        WHERE active = 1 
                        AND t1.companyID ='$companyID'
                    ) AS repoManagerTB ON empTB.empID = repoManagerTB.empID
                    WHERE empTB.companyID ='$companyID' AND masterID ={$systemID}
                    ) AS empData

                    LEFT JOIN (
                        SELECT managerID AS topManager, Ename2 AS topManagerName, empID AS topEmpID
                        FROM srp_erp_employeemanagers AS t1
                        JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo AND t2.Erp_companyID = '$companyID'
                        WHERE t1.companyID ='$companyID' AND active = 1
                    ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID 
        
                ")->row_array();

                $data_arr = array();

                foreach($approved as $key => $approve_details){

                    $employeeID = $approve_details['employeeID'];

                    if($employeeID == -1){
                        $approved[$key]['Ename2'] =  $managers['repManagerName'];
                        $approved[$key]['levelUserID'] =  $managers['repManager'];
                    }elseif($employeeID == -2){
                        $approved[$key]['Ename2'] =  $managers['HODName'];
                        $approved[$key]['levelUserID'] =  $managers['HOD'];
                    }elseif($employeeID == -3){
                        $approved[$key]['Ename2'] =  $managers['topManagerName'];
                        $approved[$key]['levelUserID'] =  $managers['topManager'];
                    }else{
                        $employee_details = fetch_employeeNo($employeeID);
                        $approved[$key]['Ename2'] =  $employee_details['Ename2'];
                        $approved[$key]['levelUserID'] =  $employee_details['EIdNo'];
                    }


                }
                $data_arr['approved'] = $approved;
                $data_arr['document_code'] = isset($data_arr['approved'][0]['documentCode']) ? $data_arr['approved'][0]['documentCode'] : '';
                $data_arr['document_date'] = isset($data_arr['approved'][0]['documentDate']) ? $data_arr['approved'][0]['documentDate'] : '' ;
                $data_arr['confirmed_date'] = isset($data_arr['approved'][0]['docConfirmedDate']) ? $data_arr['approved'][0]['docConfirmedDate'] : '';

                $confirmedEmpID = isset($data_arr['approved'][0]['docConfirmedByEmpID']) ? $data_arr['approved'][0]['docConfirmedByEmpID'] : '';
                $emp = fetch_employeeNo($confirmedEmpID);
                //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
                $data_arr['conformed_by'] = $emp['Ename2'];

                // if(!empty($data_arr['approved'])){
                //     $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                //     $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                //     $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                //     $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);

                //     $data_arr['conformed_by'] = $emp['Ename2'];
                // }
                return $data_arr;
                break;

            case 'PO':
                $where = "";
                $amountBasedApproval = getPolicyValues('ABA', 'All');
                //if($amountBasedApproval == 1) {
                    $documentTotal = $this->db->query("SELECT srp_erp_purchaseordermaster.purchaseOrderID AS purchaseOrderID, srp_erp_purchaseordermaster.companyLocalExchangeRate, transactionCurrencyID, transactionCurrency, ( det.transactionAmount -( generalDiscountPercentage / 100 )* det.transactionAmount )+ IFNULL( gentax.gentaxamount, 0 ) AS total_value 
                    FROM srp_erp_purchaseordermaster
                        LEFT JOIN ( SELECT SUM( totalAmount )+ ifnull( SUM( taxAmount ), 0 ) AS transactionAmount, purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID
                        LEFT JOIN (
                                SELECT ifnull( SUM( amount ), 0 ) AS gentaxamount, documentMasterAutoID 
                                FROM srp_erp_taxledger 
                                WHERE documentID = 'PO' AND documentDetailAutoID IS NULL AND companyID = {$companyID} 
                                GROUP BY documentMasterAutoID 
                        ) gentax ON ( gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID ) 
                    WHERE
                        srp_erp_purchaseordermaster.purchaseOrderID = {$systemID} AND srp_erp_purchaseordermaster.companyID = {$companyID}")->row_array();

//                    $defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
//                    $conversion = currency_conversionID($documentTotal['transactionCurrencyID'], $defaultCurrencyID,  $documentTotal['total_value']);

                    $poLocalAmount = $documentTotal['total_value'] / $documentTotal['companyLocalExchangeRate'];
                    //$where = "fromAmount <= " . $poLocalAmount . " AND toAmount >= " . $poLocalAmount . "";
                //}
                $company_doc_approval_type = getApprovalTypesONDocumentCode('PO',$companyID);
                $approval_type_data = $this->db->query("SELECT segmentID,itemCategoryID FROM srp_erp_purchaseordermaster where purchaseOrderID = $systemID AND companyID = {$companyID}")->row_array();
                

                $convertFormat = convert_date_format_sql();
                $data_arr = array();
                $this->db->select("app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,levelNo AS approvalLevelID, approved.approvedYN, approvedDate,approvedComments, approved.documentCode,approved.docConfirmedByEmpID,
                                  approved.documentDate as documentDate, approved.docConfirmedDate AS docConfirmedDate, DATE_FORMAT(approvedDate,\"" . $convertFormat . "\")
                                  AS approveDate");
                $this->db->from('srp_erp_approvalusers ap');
                $this->db->join('srp_erp_documentapproved', 'ap.levelNo = srp_erp_documentapproved.approvalLevelID AND
                                                                            srp_erp_documentapproved.approvedEmpID = ap.employeeID AND
                                                                            srp_erp_documentapproved.documentID = "' . $documentID . '" AND
                                                                            srp_erp_documentapproved.companyID = "' . $companyID . '" AND
                                                                            srp_erp_documentapproved.documentSystemCode = "' . $systemID . '"', 'LEFT');
                $this->db->join('(SELECT approvedYN, approvalLevelID, documentCode, DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate, 
                        DATE_FORMAT(docConfirmedDate,\'' . $convertFormat . '\') AS docConfirmedDate, docConfirmedByEmpID AS docConfirmedByEmpID FROM srp_erp_documentapproved WHERE `documentID` = "'. $documentID .'" AND `companyID` = "'. $companyID .'" AND `documentSystemCode` = "'. $systemID .'") approved', '`ap`.`levelNo` = `approved`.`approvalLevelID`', 'LEFT');
                $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = ap.employeeID');
                if(!empty($where)){
                    $this->db->where($where);
                }
                $poLocalAmount_max =$poLocalAmount+2;
                if($company_doc_approval_type['approvalType']==1){

                }else if($company_doc_approval_type['approvalType']==2){
                    
                    $this->db->where("((ap.toAmount != 0 AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND ap.toAmount) OR (ap.toAmount = 0  AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND '{$poLocalAmount_max}'))");
                   
                }else if($company_doc_approval_type['approvalType']==3){
                    $this->db->where('ap.segmentID', $approval_type_data['segmentID']);
                   
                }else if($company_doc_approval_type['approvalType']==4){
                    $this->db->where("((ap.toAmount != 0 AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND ap.toAmount) OR (ap.toAmount = 0  AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND '{$poLocalAmount_max}'))");
                    $this->db->where('ap.segmentID', $approval_type_data['segmentID']);
                   
                }else if($company_doc_approval_type['approvalType']==5){
                    $this->db->where("((ap.toAmount != 0 AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND ap.toAmount) OR (ap.toAmount = 0  AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND '{$poLocalAmount_max}'))");
                    $this->db->where('ap.typeID', $approval_type_data['itemCategoryID']);

                }

                $this->db->where('ap.documentID', $documentID);
                $this->db->where('ap.companyID', $companyID);
                $this->db->order_by('levelNo', 'ASC');
                $this->db->order_by('approvedYN', 'DESC');
                $data_arr['approved'] = $this->db->get()->result_array();

                if(!empty($data_arr['approved'])){
                    $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                    $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                    $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                    $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                    //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
                    $data_arr['conformed_by'] = $emp['Ename2'];
                }
//                echo '<pre>'; print_r($data_arr );
                return $data_arr;
            break;

            default:
                $convertFormat = convert_date_format_sql();
                $data_arr = array();
                $this->db->select("app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,levelNo AS approvalLevelID, approved.approvedYN, approvedDate,approvedComments, approved.documentCode,approved.docConfirmedByEmpID,
                                  approved.documentDate as documentDate, approved.docConfirmedDate AS docConfirmedDate, DATE_FORMAT(approvedDate,\"" . $convertFormat . "\")
                                  AS approveDate");
                $this->db->from('srp_erp_approvalusers ap');
                $this->db->join('srp_erp_documentapproved', 'ap.levelNo = srp_erp_documentapproved.approvalLevelID AND
                                                                            srp_erp_documentapproved.approvedEmpID = ap.employeeID AND
                                        ;                                    srp_erp_documentapproved.documentID = "' . $documentID . '" AND
                                                                            srp_erp_documentapproved.companyID = "' . $companyID . '" AND
                                                                            srp_erp_documentapproved.documentSystemCode = "' . $systemID . '"', 'LEFT');
                $this->db->join('(SELECT approvedYN, approvalLevelID, documentCode, DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate, 
                        DATE_FORMAT(docConfirmedDate,\'' . $convertFormat . '\') AS docConfirmedDate, docConfirmedByEmpID AS docConfirmedByEmpID FROM srp_erp_documentapproved WHERE `documentID` = "'. $documentID .'" AND `companyID` = "'. $companyID .'" AND `documentSystemCode` = "'. $systemID .'") approved', '`ap`.`levelNo` = `approved`.`approvalLevelID`', 'LEFT');
                $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = ap.employeeID');
                $this->db->where('ap.documentID', $documentID);

                if($documentID=='BSI'){
                    $company_doc_approval_type = getApprovalTypesONDocumentCode('BSI',$companyID);
                    $segmentID = $this->db->query("SELECT segmentID FROM srp_erp_paysupplierinvoicemaster where InvoiceAutoID = $systemID AND companyID = {$companyID}")->row_array();

                    $query_dt = "SELECT
                    srp_erp_paysupplierinvoicemaster.InvoiceAutoID,
                        `srp_erp_paysupplierinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
                        `srp_erp_paysupplierinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
                        `srp_erp_paysupplierinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
                        `srp_erp_paysupplierinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
                        `srp_erp_paysupplierinvoicemaster`.`supplierCurrencyExchangeRate` AS `supplierCurrencyExchangeRate`,
                        `srp_erp_paysupplierinvoicemaster`.`supplierCurrencyDecimalPlaces` AS `supplierCurrencyDecimalPlaces`,
                        `srp_erp_paysupplierinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,
                        (srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0) as discountAmnt,
                        (
                            (
                                (
                                    IFNULL(addondet.taxPercentage, 0) / 100
                                ) * (IFNULL(det.transactionAmount, 0)-((srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0)))
                            ) + IFNULL(det.transactionAmount, 0)-((srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0))
                        ) AS total_value
                    
                    FROM
                        `srp_erp_paysupplierinvoicemaster`
                    LEFT JOIN (
                        SELECT
                            SUM(transactionAmount) AS transactionAmount,
                            InvoiceAutoID
                        FROM
                            srp_erp_paysupplierinvoicedetail
                        GROUP BY
                            InvoiceAutoID
                    ) det ON (
                        `det`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                    )
                    LEFT JOIN (
                        SELECT
                            SUM(taxPercentage) AS taxPercentage,
                            InvoiceAutoID
                        FROM
                            srp_erp_paysupplierinvoicetaxdetails
                        GROUP BY
                            InvoiceAutoID
                    ) addondet ON (
                        `addondet`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                    )
                    WHERE
                        `companyID` = $companyID
                        AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $systemID ";
                    
                    $totalValue = $this->db->query($query_dt)->row_array();

                    $poLocalAmount = $totalValue['total_value'] /$totalValue['companyLocalExchangeRate'];

                    $poLocalAmount_max =$poLocalAmount+2;
                    if($company_doc_approval_type['approvalType']==1){

                    }else if($company_doc_approval_type['approvalType']==2){
                        
                        $this->db->where("((ap.toAmount != 0 AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND ap.toAmount) OR (ap.toAmount = 0  AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND '{$poLocalAmount_max}'))");
                    
                    }else if($company_doc_approval_type['approvalType']==3){
                        $this->db->where('ap.segmentID', $segmentID['segmentID']);
                    
                    }else if($company_doc_approval_type['approvalType']==4){
                        $this->db->where("((ap.toAmount != 0 AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND ap.toAmount) OR (ap.toAmount = 0  AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND '{$poLocalAmount_max}'))");
                        $this->db->where('ap.segmentID', $segmentID['segmentID']);
                    
                    }
                }

                if($documentID=='PV'){
                   
                    $company_doc_approval_type = getApprovalTypesONDocumentCode('PV',$companyID);
                    
                    $segmentID = $this->db->query("SELECT segmentID FROM srp_erp_paymentvouchermaster where payVoucherAutoId = $systemID AND companyID = {$companyID}")->row_array();
                    

                    $poLocalAmount = payment_voucher_total_value($systemID, 2, 0);
                    
                    $poLocalAmount_max =$poLocalAmount+2;
                    if($company_doc_approval_type['approvalType']==1){

                    }else if($company_doc_approval_type['approvalType']==2){
                        
                        $this->db->where("((ap.toAmount != 0 AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND ap.toAmount) OR (ap.toAmount = 0  AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND '{$poLocalAmount_max}'))");
                    
                    }else if($company_doc_approval_type['approvalType']==3){
                        $this->db->where('ap.segmentID', $segmentID['segmentID']);
                    
                    }else if($company_doc_approval_type['approvalType']==4){
                        $this->db->where("((ap.toAmount != 0 AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND ap.toAmount) OR (ap.toAmount = 0  AND '{$poLocalAmount}' BETWEEN ap.fromAmount AND '{$poLocalAmount_max}'))");
                        $this->db->where('ap.segmentID', $segmentID['segmentID']);
                    
                    }
                }
                
                $this->db->where('ap.companyID', $companyID);
                $this->db->order_by('levelNo', 'ASC');
                $this->db->order_by('approvedYN', 'DESC');
                $data_arr['approved'] = $this->db->get()->result_array();
               // print_r( $data_arr['approved']);exit;
                if(!empty($data_arr['approved'])){
                    $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                    $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                    $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                    $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                    //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
                    $data_arr['conformed_by'] = $emp['Ename2'];
                }
                return $data_arr;
        }
    }

    function fetch_reject_user_modal()
    {
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $data_arr = array();
        $documentSystemCode = $this->input->post('documentSystemCode');
        $documentID = $this->input->post('documentID');
        $isCancelled = null;

        if($documentID == 'LA'){
            $isCancelled = $this->db->query("SELECT requestForCancelYN FROM srp_erp_leavemaster WHERE companyID={$companyID}
                              AND leaveMasterID={$documentSystemCode}")->row('requestForCancelYN');
        }

        $this->db->select("app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,documentCode,comment,rejectedLevel,rejectByEmpID,systemID,
                           DATE_FORMAT(srp_erp_approvalreject.createdDateTime,\"" . $convertFormat . "\") AS referbackDate");
        $this->db->from('srp_erp_approvalreject');
        $this->db->where('srp_erp_approvalreject.documentID', $documentID);
        $this->db->where('systemID', $documentSystemCode);
        $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = srp_erp_approvalreject.rejectByEmpID');
        $this->db->where('srp_erp_approvalreject.companyID', $companyID);
        if($isCancelled == 1){
            $this->db->where('srp_erp_approvalreject.isFromCancel', 1);
        }
        if($this->input->post('is')){

        }
        $data_arr['rejected'] = $this->db->get()->result_array();
        if (!empty($data_arr['rejected'])) {
            $data_arr['document_code'] = $data_arr['rejected'][0]['documentCode'];
        }

        //$data_arr['referback_date'] = $data_arr['rejected'][0]['referbackDate'];
        return $data_arr;
    }

    function fetch_approval_referbackuser_user_modal()
    {
        $convertFormat = convert_date_format_sql();
        $data_arr = array();
        $this->db->select("app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,documentCode,comment,rejectedLevel,rejectByEmpID,systemID,
                          DATE_FORMAT(srp_erp_approvalreject.createdDateTime,\"" . $convertFormat . "\") AS referbackDate");
        $this->db->from('srp_erp_approvalreject');
        $this->db->where('srp_erp_approvalreject.documentID', $this->input->post('documentID'));
        $this->db->where('systemID', $this->input->post('documentSystemCode'));
        $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = srp_erp_approvalreject.rejectByEmpID');
        $this->db->where('srp_erp_approvalreject.companyID', $this->common_data['company_data']['company_id']);
        $data_arr['rejected'] = $this->db->get()->result_array();
        $data_arr['document_code'] = $data_arr['rejected'][0]['documentCode'];
        $data_arr['referback_date'] = $data_arr['rejected'][0]['referbackDate'];

        return $data_arr;
    }

    function fetch_approval_level()
    {
        $this->db->select("approvalLevel");
        $this->db->from('srp_erp_documentcodemaster');
        $this->db->where('documentID', $this->input->post('documentID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data_arr = $this->db->get()->row_array();

        return $data_arr;
    }

    function fetch_document_closed_users_modal()
    {
        $companyID = current_companyID();
        $data_arr = array();
        $closedDet = array();
        $systemID = $this->input->post('documentSystemCode');
        $documentID = $this->input->post('documentID');


        $convertFormat = convert_date_format_sql();
        $data_arr = array();
        $this->db->select("app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,levelNo AS approvalLevelID, approved.approvedYN, approvedDate,approvedComments, approved.documentCode,approved.docConfirmedByEmpID,
                                  approved.documentDate as documentDate, approved.docConfirmedDate AS docConfirmedDate, DATE_FORMAT(approvedDate,\"" . $convertFormat . "\")
                                  AS approveDate");
        $this->db->from('srp_erp_approvalusers ap');
        $this->db->join('srp_erp_documentapproved', 'ap.levelNo = srp_erp_documentapproved.approvalLevelID AND
                                                                            srp_erp_documentapproved.documentID = "' . $documentID . '" AND
                                                                            srp_erp_documentapproved.companyID = "' . $companyID . '" AND
                                                                            srp_erp_documentapproved.documentSystemCode = "' . $systemID . '"', 'LEFT');
        $this->db->join('(SELECT approvedYN, approvalLevelID, documentCode, DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate, approvedEmpID,
                        DATE_FORMAT(docConfirmedDate,\'' . $convertFormat . '\') AS docConfirmedDate, docConfirmedByEmpID AS docConfirmedByEmpID FROM srp_erp_documentapproved WHERE `documentID` = "'. $documentID .'" AND `companyID` = "'. $companyID .'" AND `documentSystemCode` = "'. $systemID .'") approved', '`ap`.`levelNo` = `approved`.`approvalLevelID`', 'LEFT');
        $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = approved.approvedEmpID');
        $this->db->where('ap.documentID', $documentID);
        $this->db->where('ap.companyID', $companyID);
        $this->db->order_by('levelNo', 'ASC');
        $this->db->order_by('approvedYN', 'DESC');
        $data_arr['approved'] = $this->db->get()->result_array();

        switch ($documentID) {
            case 'QUT';
            case 'SO';
            case 'CNT';
                $closedDet = $this->db->query("SELECT closedYN, DATE_FORMAT(closedDate,\"" . $convertFormat . "\") AS closedDate, closedReason, closedBy FROM srp_erp_contractmaster WHERE contractAutoID = $systemID")->row_array();
                break;
            case 'PO';
                $closedDet = $this->db->query("SELECT closedYN, DATE_FORMAT(closedDate,\"" . $convertFormat . "\") AS closedDate, closedReason, closedBy FROM srp_erp_purchaseordermaster WHERE purchaseOrderID = $systemID")->row_array();
                break;
            case 'PRQ';
                $closedDet = $this->db->query("SELECT closedYN, DATE_FORMAT(closedDate,\"" . $convertFormat . "\") AS closedDate, closedReason, closedBy FROM srp_erp_purchaserequestmaster WHERE purchaseRequestID = $systemID")->row_array();
                break;
            case 'MR';
                $closedDet = $this->db->query("SELECT closedYN, DATE_FORMAT(closedDate,\"" . $convertFormat . "\") AS closedDate, closedReason, closedBy FROM srp_erp_materialrequest WHERE mrAutoID = $systemID")->row_array();
                break;
        }

        if (!empty($data_arr['approved']))
        {
            $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
            $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
            $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
            $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
            $data_arr['conformed_by'] = $emp['Ename2'];
        }
        if(!empty($closedDet))
        {
            $data_arr['closedDate'] = $closedDet['closedDate'];
            $data_arr['closedReason'] = $closedDet['closedReason'];
            $data_arr['closedBy'] = $closedDet['closedBy'];
            $data_arr['closedYN'] = $closedDet['closedYN'];
        }
        return $data_arr;
    }

    // function getApprovalTypeID()
    // {
    //     $companyID = $this->common_data['company_data']['company_id'];

    //     $this->db->select("approvalType");
    //     $this->db->from('srp_erp_documentcodemaster');
    //     $this->db->where('companyID', $companyID);
    //     $this->db->where('documentID', $this->input->post('documentID'));

    //     return $this->db->get()->row_array();
      
    // }

    function getApprovalDocumentDetails()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $this->db->select("*");
        $this->db->from('srp_erp_documentcodemaster');
        $this->db->where('companyID', $companyID);
        $this->db->where('documentID', $this->input->post('documentID'));

        return $this->db->get()->row_array();
      
    }

    /** added : almansoori chnges for personal application */
    function all_types_drop()
    {
        $this->db->select("documentCategoryID,categoryDescription");
        $this->db->from('srp_erp_system_document_categories');
        $this->db->where('documentID', $this->input->post('documentID'));
        $data_arr = $this->db->get()->result_array();

        return $data_arr;
    }

    function save_special_user(){
        $approvalUserID = $this->input->post('specialApproavalID');
        $specialUser=$this->input->post('specialUseremp');
        $empID=$this->input->post('empApprove');

        $this->db->where('approvalUserID', $approvalUserID);
        $this->db->where('empID', $specialUser);
        $query = $this->db->get('srp_erp_appoval_specific_users')->row_array();

        if (!empty($query)) {
            $this->session->set_flashdata('e', 'Record already exists');
            return FALSE;
        }
        
        $this->db->trans_start();

        $specialUserData=[
        'approvalUserID'=>$approvalUserID,
        'empID'=>$specialUser,
        'createduserID'=>$empID,
        'createdDateTime'=>$this->common_data['current_date']
        ];

       $this->db->insert('srp_erp_appoval_specific_users', $specialUserData);
 
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Failed to add record');
            return FALSE;
        } else {
            $this->session->set_flashdata('s', 'Record Added Successfully');
            return TRUE;
        }
        
    }

    function delete_specialUser(){
        $specialUserID=$this->input->post('id');

        $this->db->where('id',$specialUserID);
        $result=$this->db->delete('srp_erp_appoval_specific_users');

        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');

            return TRUE;
        }
    }
}