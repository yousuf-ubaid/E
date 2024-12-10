<?php

class Suppliermaster_model extends ERP_Model
{

    function save_supplier_master()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $ApprovalforSupplierMaster= getPolicyValues('ASM', 'All');
        $supplierAutoID= trim($this->input->post('supplierAutoID') ?? '');
        $isactive = 0;
        if (!empty($this->input->post('isActive'))) {
            $isactive = 1;
        }

        $suppliercode = trim($this->input->post('suppliercode') ?? '');
        $inter_company = $this->input->post('inter_company');

        $this->db->select('supplierAutoID');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('interCompanyID', $inter_company);
        $this->db->where('interCompanyYN',1);
        $query = $this->db->get();
        $isCompany = $query->result();

        if($isCompany){
            return array('e', 'This company already inter connected');
        }

        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $liability = fetch_gl_account_desc(trim($this->input->post('liabilityAccount') ?? ''));
        $data['isActive'] = $isactive;
        $data['secondaryCode'] = trim($this->input->post('suppliercode') ?? '');
        $data['supplierName'] = trim($this->input->post('supplierName') ?? '');

        $suppliercountry = trim($this->input->post('suppliercountry') ?? '');
        $suppliercountryID = $this->db->query("SELECT CountryID FROM srp_erp_countrymaster WHERE CountryDes = '{$suppliercountry}'")->row('CountryID');
        $data['suppliercountryID'] = trim($suppliercountryID);
        $data['supplierCountry'] = trim($this->input->post('suppliercountry') ?? '');
        $data['supplierLocationID'] = trim($this->input->post('supplierLocationID') ?? '');
        $data['supplierTelephone'] = trim($this->input->post('supplierTelephone') ?? '');
        $data['supplierEmail'] = trim($this->input->post('supplierEmail') ?? '');
        $data['supplierUrl'] = trim($this->input->post('supplierUrl') ?? '');
        $data['supplierFax'] = trim($this->input->post('supplierFax') ?? '');
        $data['taxGroupID'] = trim($this->input->post('suppliertaxgroup') ?? '');
        $data['vatIdNo'] = trim($this->input->post('vatIdNo') ?? '');
        $data['supplierAddress1'] = trim($this->input->post('supplierAddress1') ?? '');
        $data['supplierAddress2'] = trim($this->input->post('supplierAddress2') ?? '');
        $data['partyCategoryID'] = trim($this->input->post('partyCategoryID') ?? '');
        $data['nameOnCheque'] = trim($this->input->post('nameOnCheque') ?? '');
        $data['paymentTerms'] = trim($this->input->post('paymentTerms') ?? '');
        $data['vatEligible'] = trim($this->input->post('vatEligible') ?? '');
        $data['vatNumber'] = trim($this->input->post('vatNumber') ?? '');
        $data['vatPercentage'] = trim($this->input->post('vatPercentage') ?? '');
        $data['customerID'] = trim($this->input->post('customerID') ?? '');

        $data['liabilityAutoID'] = $liability['GLAutoID'];
        $data['liabilitySystemGLCode'] = $liability['systemAccountCode'];
        $data['liabilityGLAccount'] = $liability['GLSecondaryCode'];
        $data['liabilityDescription'] = $liability['GLDescription'];
        $data['liabilityType'] = $liability['subCategory'];

        $data['supplierCreditPeriod'] = trim($this->input->post('supplierCreditPeriod') ?? '');
        $data['supplierCreditLimit'] = trim($this->input->post('supplierCreditLimit') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        
        if($inter_company){
            $data['interCompanyID'] =  $inter_company ;
            $data['interCompanyYN'] = 1;
        }
        else{
            $data['interCompanyID'] =  null ;
            $data['interCompanyYN'] = 0;
        }

        if (trim($this->input->post('supplierAutoID') ?? '')) {

            $isExistSupCode = $this->db->query("SELECT
                                                COUNT(supplierAutoID) as existsCount  
                                                FROM
                                                `srp_erp_suppliermaster`
                                                where 
                                                companyID  = $companyID 
                                                AND supplierAutoID != {$this->input->post('supplierAutoID')}
                                                AND secondaryCode = '{$suppliercode}'")->row('existsCount');
                
            if(!empty($isExistSupCode)){ 
               return array('e', 'Supplier code is already exist.');
               die();

            }

            $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID') ?? ''));
            $this->db->update('srp_erp_suppliermaster', $data);

            if(($ApprovalforSupplierMaster==0 || $ApprovalforSupplierMaster == NULL )){

                    $this->load->library('Approvals');
                    $this->db->select('supplierAutoID, supplierSystemCode,modifiedDateTime');
                    $this->db->where('supplierAutoID', $supplierAutoID );
                    $this->db->from('srp_erp_suppliermaster');
                    $grv_data = $this->db->get()->row_array();
                    $approvals_status = $this->approvals->auto_approve($supplierAutoID, 'srp_erp_suppliermaster','supplierAutoID', 'SUP',$grv_data['supplierSystemCode'],$this->common_data['current_date']);

                    if ($approvals_status==1) {
                        //return array('s', 'Document confirmed Successfully');
                    }else if($approvals_status ==3){
                        return array('w', 'There are no users exist to perform approval for this document.');
                        $this->db->trans_rollback();
                    } else {
                        return array('e', 'Document confirmation failed');
                        $this->db->trans_rollback();
                    }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Supplier : ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
            } else {
                return array('s', 'Supplier : ' . $data['supplierName'] . ' Updated Successfully.',$this->input->post('supplierAutoID'));
                $this->db->trans_commit();
            }
        } else {

            $isExistSupCode = $this->db->query("SELECT
            COUNT(supplierAutoID) as existsCount  
            FROM
            `srp_erp_suppliermaster`
            where 
            companyID  = $companyID 
            AND secondaryCode = '{$suppliercode}'")->row('existsCount');

            if(!empty($isExistSupCode)){ 
                return array('e', 'Supplier code is already exist.');
                die();
            }

            $this->load->library('sequence');
            $data['supplierCurrencyID'] = trim($this->input->post('supplierCurrency') ?? '');
            $data['supplierCurrency'] = $currency_code[0];
            $data['supplierCurrencyDecimalPlaces'] = fetch_currency_desimal($data['supplierCurrency']);
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['supplierSystemCode'] = $this->sequence->sequence_generator('SUP');
            $this->db->insert('srp_erp_suppliermaster', $data);
            $last_id = $this->db->insert_id();

            if(( $ApprovalforSupplierMaster==0 || $ApprovalforSupplierMaster== NULL )){

                    $this->load->library('Approvals');
                    $approvals_status = $this->approvals->auto_approve($last_id, 'srp_erp_suppliermaster','supplierAutoID', 'SUP',$data['supplierSystemCode'] ,$this->common_data['current_date']);

                    if ($approvals_status==1) {
                        //return array('s', 'Document confirmed Successfully');
                    }else if($approvals_status ==3){
                        return array('w', 'There are no users exist to perform approval for this document.');
                        $this->db->trans_rollback();
                    } else {
                        return array('e', 'Document confirmation failed');
                        $this->db->trans_rollback();
                    }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Supplier : ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
            
            } else {
                return array('s', 'Supplier : ' . $data['supplierName'] . ' Updated Successfully.',$last_id);
                $this->db->trans_commit();
        
            }
        }
    }

    function load_supplier_header()
    {
        $this->db->select('supplierFax,partyCategoryID,customerID,supplierTelephone,supplierAutoID,paymentTerms,supplierSystemCode,supplierName,supplierAddress1,supplierAddress2,supplierEmail,supplierUrl,liabilityAutoID,secondaryCode,supplierCurrency,supplierCreditPeriod,supplierCreditLimit,isActive,liabilityGLAccount,supplierCountry,supplierCurrencyID,taxGroupID,vatIdNo,nameOnCheque, vatEligible, vatNumber, vatPercentage, supplierLocationID,isSystemGenerated,interCompanyYN,interCompanyID');
        $this->db->where('supplierAutoID', $this->input->post('supplierAutoID'));
        return $this->db->get('srp_erp_suppliermaster')->row_array();
    }

    function get_supplier()
    {
        $this->db->select('*');
        $this->db->where('supplierAutoID', $this->input->post('id'));
        return $this->db->get('srp_erp_suppliermaster')->row_array();
    }


    function delete_supplier()
    {
        $supplierAutoID = $this->input->post('supplierAutoID');
        $data['deletedYN'] = 1;
        $data['isActive'] = 0;
        $data['deleteByEmpID'] = $this->common_data['current_userID'];
        $data['deletedDatetime'] = $this->common_data['current_date'];

        $this->db->where('supplierAutoID', $supplierAutoID);
        $this->db->update('srp_erp_suppliermaster', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            return true;
        }

       /* $this->db->where('supplierAutoID', $this->input->post('supplierAutoID'));
        $result = $this->db->delete('srp_erp_suppliermaster');
        $this->session->set_flashdata('s', 'Record Deleted Successfully');
        return true;*/
    }

    function saveCategory()
    {
        if (empty($this->input->post('partyCategoryID'))) {

            $this->db->select('partyCategoryID');
            $this->db->where('categoryDescription', $this->input->post('categoryDescription'));
            $this->db->where('partyType', 2);
            $this->db->where('companyID', current_companyID());
            $category = $this->db->get('srp_erp_partycategories')->row_array();
            if (empty($category)) {
                $this->db->set('categoryDescription', $this->input->post('categoryDescription'));
                $this->db->set('partyType', 2);
                $this->db->set('companyID', current_companyID());
                $this->db->set('companyCode', current_companyCode());
                $this->db->set('createdUserGroup', current_user_group());
                $this->db->set('createdPCID', current_pc());
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserName', current_user());
                $this->db->set('createdDateTime', $this->common_data['current_date']);
                $result = $this->db->insert('srp_erp_partycategories');

                if ($result) {
                    return array('s', 'Record added successfully');
                } else {
                    return array('e', 'Error in adding Record');
                }
            } else {
                return array('e', 'Category Already Exist');
            }
        } else {
            $this->db->select('partyCategoryID');
            $this->db->where('categoryDescription', $this->input->post('categoryDescription'));
            $this->db->where('partyType', 2);
            $category = $this->db->get('srp_erp_partycategories')->row_array();
            if (empty($category)) {
                $data['categoryDescription'] = $this->input->post('categoryDescription');
                $data['modifiedPCID'] = current_pc();
                $data['modifiedUserID'] = current_userID();
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['modifiedUserName'] = current_user();

                $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
                $result = $this->db->update('srp_erp_partycategories', $data);


                if ($result) {
                    return array('s', 'Record Updated successfully');
                } else {
                    return array('e', 'Error in Updating Record');
                }
            } else {
                return array('e', 'Category Already Exist');
            }
        }

    }

    function getCategory()
    {
        $this->db->select('*');
        $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
        return $this->db->get('srp_erp_partycategories')->row_array();
    }

    function delete_category()
    {
        $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
        $result = $this->db->delete('srp_erp_partycategories');
        if ($result) {
            return array('s', 'Record Deleted successfully');
        }
    }

    function save_bank_detail()
    {
        $this->db->trans_start();
        $supplierBankMasterID= $this->input->post('supplierBankMasterID');
        $data['bankName'] = $this->input->post('bankName');
        $data['currencyID'] = $this->input->post('currencyID');
        $data['accountName'] = $this->input->post('accountName');
        $data['accountNumber'] = $this->input->post('accountNumber');
        $data['swiftCode'] = $this->input->post('swiftCode');
        $data['ibanCode'] = $this->input->post('ibanCode');
        $data['bankAddress'] = $this->input->post('address');
        $data['supplierAutoID'] = $this->input->post('supplierAutoID');
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        if(empty($supplierBankMasterID)) {
            $this->db->insert('srp_erp_supplierbankmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }else{
            $this->db->where('supplierBankMasterID', $supplierBankMasterID);
            $this->db->update('srp_erp_supplierbankmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $last_id = $this->db->insert_id();
                $this->session->set_flashdata('s', 'Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }



    }

    function delete_supplierbank()
    {
        $this->db->where('supplierBankMasterID', $this->input->post('supplierBankMasterID'));
        $result = $this->db->delete('srp_erp_supplierbankmaster');
        $this->session->set_flashdata('s', 'Record Deleted Successfully');
        return true;
    }

    function edit_Bank_Details(){
        $this->db->select('*');
        $this->db->where('supplierBankMasterID', $this->input->post('supplierBankMasterID'));
        return $this->db->get('srp_erp_supplierbankmaster')->row_array();
    }
    /* Function added */
    function export_excel_supplier_master(){
        $supplier_filter = '';
        $currency_filter = '';
        $category_filter = '';
        //$deleted_filter = '';
        
        $supplier = $this->input->post('supplierCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
        /*$deleted = $this->input->post('deleted');
       
        if ($deleted == 1) {
            $deleted_filter = " AND deletedYN = " . $deleted;
        } else {
            $deleted_filter = " AND ( deletedYN IS NULL OR deletedYN = " . $deleted . ")";
        }*/
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierCode'));

            $whereIN = "( " . join(" , ", $supplier[0]) . " )";
            $supplier_filter = " AND supplierAutoID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join(" , ", $currency[0]) . " )";
            $currency_filter = " AND supplierCurrencyID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join(" , ", $category[0]) . " )";
            $category_filter = " AND srp_erp_suppliermaster.partyCategoryID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_suppliermaster.companyID = " . $companyid . $supplier_filter . $currency_filter . $category_filter. "";
        $result = $this->db->query("SELECT supplierSystemCode,supplierName,secondaryCode,concat(`supplierAddress1`,supplierAddress2, supplierCountry) AS address, supplierEmail,supplierTelephone,supplierUrl,supplierFax,srp_erp_partycategories.categoryDescription,tax.Description AS Description, vatIdNo, supplierCreditPeriod, supplierCreditLimit, nameOnCheque,CONCAT_WS('|',liabilitySystemGLCode,liabilityGLAccount,liabilityDescription,liabilityType) AS liabilityAccount,supplierCurrency,`cust`.`Amount` AS `Amount`, `cust`.`partyCurrencyDecimalPlaces` AS `partyCurrencyDecimalPlaces`, srp_erp_suppliermaster.deletedYN as deletedYN
            FROM
            `srp_erp_suppliermaster`
            LEFT JOIN srp_erp_partycategories ON srp_erp_suppliermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID
            LEFT JOIN ( SELECT taxGroupID, Description FROM srp_erp_taxgroup WHERE taxType = 2 AND companyID = $companyid GROUP BY taxGroupID ) tax ON tax.taxGroupID = srp_erp_suppliermaster.taxGroupID
            LEFT JOIN (
            SELECT
                sum( srp_erp_generalledger.transactionAmount / srp_erp_generalledger.partyExchangeRate ) *- 1 AS Amount, partyAutoID, partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = 'SUP' 
                AND subLedgerType = 2 GROUP BY partyAutoID ) cust ON `cust`.`partyAutoID` = `srp_erp_suppliermaster`.`supplierAutoID` 
            WHERE $where
            AND  ( deletedYN IS NULL OR `deletedYN` = 0  )
            ORDER BY `supplierAutoID` DESC 
        ")->result_array();

        $data = array();
        $a = 1;
        foreach ($result as $row)
        {
            $data[] = array(
                'Num' => $a,
                'supplierSystemCode' => $row['supplierSystemCode'],
                'supplierName' => $row['supplierName'],
                'secondaryCode' => $row['secondaryCode'],
                'address' => $row['address'],
                'supplierEmail' => $row['supplierEmail'],
                'supplierTelephone' => $row['supplierTelephone'],
                'supplierUrl' => $row['supplierUrl'],
                'supplierFax' => $row['supplierFax'],
                'Description' => $row['Description'],
                'vatIdNo' => $row['vatIdNo'],  
                'supplierCreditPeriod' => $row['supplierCreditPeriod'],  
                'supplierCreditLimit' => $row['supplierCreditLimit'],  
                'nameOnCheque' => $row['nameOnCheque'],
                'liabilityAccount' => $row['liabilityAccount'],
                'supplierCurrency' => $row['supplierCurrency'],    
                'amt' =>  format_number($row['Amount'],$row['partyCurrencyDecimalPlaces']),

            );
            $a++;
        }

        return ['suppliers' => $data];
    }

    function check_supplier_confirmation()
    {
        $companyID = current_companyID();
        $supplierAutoID = $this->input->post('supplierAutoID');
        $result = $this->db->query("SELECT * FROM srp_erp_suppliermaster 
                                            WHERE	companyID = {$companyID} AND supplierAutoID = {$supplierAutoID} AND masterConfirmedYN = 1 ")->row_array();
        if (empty($result)) {
            //return array('e', 'No Records Found');
            return $result;
        } else {
            if($result['masterApprovedYN']==1 || $result['masterApprovedYN']==2/**SMSD */){
                return array('s', 'Fully Approved Supplier',2);
            }else{
                return array('s', 'Confirmed Supplier');
            }
        }
    }

    function sup_confirmation()
    {
        $companyID = current_companyID();
        $currentuser = current_userID();
        $system_id = trim($this->input->post('supplierAutoID') ?? '');

        $this->db->select('supplierAutoID');
        $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID') ?? ''));
        $this->db->where('masterConfirmedYN', 1);
        $this->db->from('srp_erp_suppliermaster');
        $Confirmed = $this->db->get()->row_array();
        if (!empty($Confirmed)) {
            return array('w', 'Supplier already confirmed');
        }

        $this->load->library('Approvals');
        $this->db->select('supplierAutoID, supplierSystemCode,createdDateTime');
        $this->db->where('supplierAutoID', $system_id);
        $this->db->from('srp_erp_suppliermaster');
        $grv_data = $this->db->get()->row_array();

        $autoApproval= get_document_auto_approval('SUP');

        if($autoApproval==0){
            $approvals_status = $this->approvals->auto_approve($grv_data['supplierAutoID'], 'srp_erp_suppliermaster','supplierAutoID', 'SUP',$grv_data['supplierSystemCode'],$this->common_data['current_date']);
        }elseif($autoApproval==1){
            $approvals_status = $this->approvals->CreateApproval('SUP', $grv_data['supplierAutoID'], $grv_data['supplierSystemCode'], 'Supplier', 'srp_erp_suppliermaster', 'supplierAutoID',0,$this->common_data['current_date']);
        }else{
            return array('e', 'Approval levels are not set for this document');
        }
        if ($approvals_status==1) {

//                $autoApproval= get_document_auto_approval('INV');
//                if($autoApproval==0) {
            //$result = $this->save_dn_approval(0, $system_id, 1, 'Auto Approved');
            //if($result){
//                        return array('s', 'Document confirmed Successfully');
            // }
//                }else{
//                    $data = array(
//                        'confirmedYN' => 1,
//                        'confirmedDate' => $this->common_data['current_date'],
//                        'confirmedByEmpID' => $this->common_data['current_userID'],
//                        'confirmedByName' => $this->common_data['current_user']
//                    );
//                    $this->db->where('itemAutoID', $system_id);
//                    $result = $this->db->update('srp_erp_itemmaster', $data);
//                    if ($result) {
//                        return array('s', 'Document confirmed Successfully');
//                    }
            //               }

            return array('s', 'Document confirmed Successfully');
        }else if($approvals_status==3){
            return array('w', 'There are no users exist to perform approval for this document.');
        } else {
            return array('e', 'Document confirmation failed');
        }
    }


    function approve_suppliermaster($level_id){
        $this->db->trans_start();
        $supplierAutoID = trim($this->input->post('supplierAutoID') ?? '');
        //$level_id = trim($this->input->post('level') ?? '');
        //$status = trim($this->input->post('status') ?? '');
        //$comments = trim($this->input->post('comments') ?? '');
        $status = 1;
        $comments ='';

        $this->load->library('approvals');
        $approvals_status = $this->approvals->approve_document($supplierAutoID, $level_id, $status, $comments, 'SUP');

//        if ($approvals_status == 1) {
//
//            $do_details = $this->db->query("SELECT status FROM srp_erp_deliveryorder WHERE DOAutoID = {$orderID} ")->row('status');
//            if( $do_details!= 2){
//                $cont_data['status'] = 2;
//                $this->db->where('DOAutoID', $orderID);
//                $this->db->update('srp_erp_deliveryorder', $cont_data);
//            }
//
//            $master = $this->db->get_where('srp_erp_deliveryorder', ['DOAutoID'=> $orderID])->row_array();
//
//        }

        ///for srm created

       

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in Supplier Master approval process.');
        } else {
            $this->db->trans_commit();

            $this->db->select('*');
            $this->db->where('supplierAutoID', $supplierAutoID);
            $this->db->from('srp_erp_suppliermaster');
            $current = $this->db->get()->row_array();
            
            if($current){
    
                $this->db->select('*');
                $this->db->where('systemSupplierID', $supplierAutoID);
                $this->db->from('srp_erp_srm_vendor_company_requests');
                $company_request = $this->db->get()->row_array();
    
                if($company_request){
                    switch ($approvals_status){
                        case 1:
                            
                            $data['approveYN']=1;
                            $this->db->where('companyReqID', $company_request['companyReqID']);
                            $this->db->update('srp_erp_srm_vendor_company_requests', $data);
                            //return ['s', 'Supplier fully approved.',2];

                            // $this->db->select('*');
                            // $this->db->where('supplierAutoID', $supplierAutoID );
                            // $new_sup_master = $this->db->get('srp_erp_suppliermaster')->row_array();
        
                            $master_n = [
                                "reqMasterID"=> $company_request['portalReqID'],
                                "vendorErpID"=> $company_request['erpSupplierID'],
                                "vendorErpMasterID"=> $supplierAutoID,
                                "supplierDetails"=>$current,
                                "systemComment"=>$company_request['systemComment']
                            ];

                            $token = getLoginToken();
                            
                            $token_array=json_decode($token);

                           
            
                            if($token_array){
            
                                if($token_array->success==true){
                                
                                    $res= srmCommonApiCall($master_n,null,$token_array->data->token,'/Api_ecommerce/send_company_request_confirmation');

                                    
            
                                    $res_array=json_decode($res);

                                    if($res_array->status==true){
                                        $data_detail1['submitErpIDYN'] = 1;
                    
                                        $this->db->where('companyReqID', $company_request['companyReqID']);
                                        $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail1);

                                        $companyid_supplier=current_companyID();

                                        $where = "itemmaster.companyID = $companyid_supplier AND (itemmaster.financeCategory = 1 OR itemmaster.financeCategory = 2)";
                                        $this->db->select('itemmaster.*,`category`.`Description` as catDescription');
                                        $this->db->from('srp_erp_itemmaster itemmaster');
                                        $this->db->join('srp_erp_itemcategory category', 'itemmaster.subcategoryID= category.itemCategoryID', 'left');
                                        //$this->db->join('srp_erp_srm_supplieritems supplierItem', 'supplierItem.itemAutoID = itemmaster.itemAutoID AND  supplierItem.supplierAutoID=' . $supplierID, 'left');
                                        //$this->db->join('(SELECT * FROM srp_erp_srm_supplieritems WHERE `supplierAutoID` = ' . $last_id . '  ) AS supplierItem', '`supplierItem`.`itemAutoID` = `itemmaster`.`itemAutoID`', 'left');
                                        $this->db->where($where);
                                
                                        $result_sup_items = $this->db->get()->result_array();
                    
                                        if(!empty($result_sup_items)){
                                            foreach($result_sup_items as $val){
                    
                                                $this->db->select('*');
                                                $this->db->where('itemAutoID', $val['itemAutoID']);
                                                $this->db->where('supplierAutoID', $company_request['erpSupplierID'] );
                                                $output12 = $this->db->get('srp_erp_srm_supplieritems')->row_array();
                    
                                                if (empty($output12)) {
                                                   
                                                    $data_sup_tems['supplierAutoID'] = $company_request['erpSupplierID'];
                                                    $data_sup_tems['itemAutoID'] =$val['itemAutoID'];
                                                    $data_sup_tems['companyID'] = current_companyID();
                                                    $data_sup_tems['createdPCID'] = $this->common_data['current_pc'];
                                                    $data_sup_tems['createdUserID'] = $this->common_data['current_userID'];
                                                    $data_sup_tems['createdUserName'] = $this->common_data['current_user'];
                                                    $data_sup_tems['createdDateTime'] = $this->common_data['current_date'];
                                                    $data_sup_tems['createdUserGroup'] = $this->common_data['user_group'];
                                                    $data_sup_tems['timestamp'] = format_date_mysql_datetime();
                                                    $this->db->insert('srp_erp_srm_supplieritems', $data_sup_tems);
                    
                                                }
                    
                                            }
                                        }
        
                                       // $this->send_company_approve_email_supplier($company_request['erpSupplierID'],1);
                    
                                    }
                                }
                            }
                        break;
                        
                    }
                }
    
            }

            switch ($approvals_status){
                case 1: return ['s', 'Supplier fully approved.',2]; break;
                case 2: return ['s', 'Supplier order level - '.$level_id.' successfully approved']; break;
                case 3: return ['s', 'Supplier successfully rejected.']; break;
                case 5: return ['w', 'Previous Level Approval Not Finished']; break;
                default : return ['e', 'Error in item approvals process'];
            }
        }
    }

    /*function reversing_approval_item(){
        $supplierAutoID        = trim($this->input->post('supplierAutoID') ?? '');
        $date           = trim('Y-m-d');
        $document_id    = "INV";
        $document_code  = trim($this->input->post('document_code') ?? '');
        $comments       = '';
        $company_id     = $this->common_data['company_data']['company_id'];


        $this->db->select('documentID, table_name, table_unique_field_name');
        $this->db->from('srp_erp_documentapproved');
        $this->db->where('documentApprovedID', $auto_id);
        $approved_data = $this->db->get()->row_array();
        if (empty($approved_data)) {
            $this->session->set_flashdata('w', 'Document Already Referred back!');
            return array('status' => false);
        }
    }*/
    /* End  Function */

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'SUP');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();

    }

    function assignCheckListForSupplierApproval()
    {
        $assignCheckListSync = $this->input->post('assignCheckListSyncApproval');
        $supplierAutoID=$this->input->post('supplierAutoID');

        if (!empty($assignCheckListSync)) {
            foreach ($assignCheckListSync as $key => $assignCheckList) {

                $this->db->select('*');
                $this->db->where('documentID', 'SUP');
                $this->db->where('documentMasterID', $supplierAutoID);
                $this->db->where('companyID', current_companyID());
                $this->db->where('checklistID', $assignCheckList);
                $this->db->from('srp_erp_document_approval_checklistdeails ');
                $sup= $this->db->get()->row_array();

                if($sup){
                    //
                }else{
                    $data['checklistID'] = $assignCheckList;
                    $data['Value'] = 1;
                    $data['documentMasterID'] = $supplierAutoID;
                    $data['companyID'] = current_companyID();
                    $data['documentID'] = 'SUP';
    
                    $this->db->insert('srp_erp_document_approval_checklistdeails', $data);
                }
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'CheckList Assigned Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'CheckList Assigned Successfully');
        }
    }

    function delete_approval_checklist()
    {
        $this->db->where('checklistID', $this->input->post('checklistID'));
        $this->db->where('documentMasterID', $this->input->post('supplierAutoID'));
        $this->db->where('documentID', 'SUP');
        $this->db->where('companyID', current_companyID());
        $result = $this->db->delete('srp_erp_document_approval_checklistdeails');
        if ($result) {
            return array('s', 'Record Deleted successfully');
        }else{
            return array('e', 'Try again');
        }
    }
    
    function findCurrentLevelUserAccess(){

        $this->db->trans_start();
        $supplierAutoID=$this->input->post('supplierAutoID');
        //print_r("ddd");exit;
        $com = current_companyID();
        
        $this->db->select('srp_erp_approvalusers.checkListYN');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->join('srp_erp_approvalusers','srp_erp_approvalusers.levelNo = srp_erp_suppliermaster.masterCurrentLevelNo','left');
        $this->db->where('srp_erp_approvalusers.documentID', 'SUP');
        $this->db->where('srp_erp_suppliermaster.supplierAutoID', $supplierAutoID);
        $this->db->where('srp_erp_suppliermaster.companyID', current_companyID());
        $this->db->where('srp_erp_approvalusers.companyID', current_companyID());
        $this->db->where('srp_erp_approvalusers.employeeID', current_userID());

        return $this->db->get()->row_array();

        //var_dump($this->db->last_query());exit;

    }

    

}