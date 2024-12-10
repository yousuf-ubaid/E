<?php

class Customer_group_model extends ERP_Model
{

    function save_customer()
    {
        $this->db->trans_start();

        $companyid = $this->common_data['company_data']['company_id'];
       /* $this->db->select('companyGroupID');
        $this->db->where('companyID', $companyid);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $companyid;

        $liability = fetch_gl_account_desc_cus_group(trim($this->input->post('receivableAccount') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $data['secondaryCode'] = trim($this->input->post('customercode') ?? '');
        $data['groupCustomerName'] = trim($this->input->post('customerName') ?? '');
        $data['customerCountry'] = trim($this->input->post('customercountry') ?? '');
        $data['customerTelephone'] = trim($this->input->post('customerTelephone') ?? '');
        $data['customerEmail'] = trim($this->input->post('customerEmail') ?? '');
        $data['customerUrl'] = trim($this->input->post('customerUrl') ?? '');
        $data['customerFax'] = trim($this->input->post('customerFax') ?? '');
        $data['customerAddress1'] = trim($this->input->post('customerAddress1') ?? '');
        $data['customerAddress2'] = trim($this->input->post('customerAddress2') ?? '');
        //$data['taxGroupID'] = trim($this->input->post('customertaxgroup') ?? '');
        $data['vatIdNo'] = trim($this->input->post('vatIdNo') ?? '');
        $data['partyCategoryID'] = trim($this->input->post('partyCategoryID') ?? '');
        $data['receivableAutoID'] = $this->input->post('receivableAccount');
        $data['receivableSystemGLCode'] = $liability['systemAccountCode'];
        $data['receivableGLAccount'] = $liability['GLSecondaryCode'];
        $data['receivableDescription'] = $liability['GLDescription'];
        $data['receivableType'] = $liability['subCategory'];
        $data['customerCreditPeriod'] = trim($this->input->post('customerCreditPeriod') ?? '');
        $data['customerCreditLimit'] = trim($this->input->post('customerCreditLimit') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('groupCustomerAutoID') ?? '')) {
            $this->db->where('groupCustomerAutoID', trim($this->input->post('groupCustomerAutoID') ?? ''));
            $this->db->update('srp_erp_groupcustomermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Customer Updating  Failed');
            } else {
                return array('s', 'Customer Updated Successfully', $this->input->post('groupCustomerAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $data['isActive'] = 1;
            $data['customerCurrencyID'] = trim($this->input->post('customerCurrency') ?? '');
            $data['customerCurrency'] = $currency_code[0];
            $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal($data['customerCurrency']);
            /*$data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];*/
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['companygroupID'] = $grpid;
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $number = $this->db->query("SELECT IFNULL(MAX(serialNo),0) as serialNo FROM srp_erp_groupcustomermaster")->row_array();
            $data['serialNo'] = $number["serialNo"]+1;
            $data['groupcustomerSystemCode'] = $this->sequence->sequence_generator_group(
                'CUS',
                0,
                $grpid,
                $this->common_data['company_data']['company_code'],
                $grpid
            );
            $this->db->insert('srp_erp_groupcustomermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Customer Save Failed', $last_id);
            } else {
                return array('s', 'Customer Saved Successfully');
            }
        }
    }


    function load_customer_header()
    {
        $this->db->select('*');
        //$this->db->join('srp_erp_groupcustomerdetails', 'srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID');
        $this->db->where('groupCustomerAutoID', $this->input->post('groupCustomerAutoID'));
        return $this->db->get('srp_erp_groupcustomermaster')->row_array();
    }


    function delete_customer()
    {
        $this->db->where('groupCustomerAutoID', $this->input->post('groupCustomerAutoID'));
        $result = $this->db->delete('srp_erp_groupcustomermaster');
        $this->session->set_flashdata('s', 'Record Deleted Successfully');
        return true;
    }


    function save_customer_link()
    {

        $companyid = $this->input->post('companyIDgrp');
        $customerMasterID = $this->input->post('customerMasterID');
        $com = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $com);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $com;

        $results= $this->db->delete('srp_erp_groupcustomerdetails', array('companyGroupID' => $grpid, 'groupCustomerMasterID' => $this->input->post('groupCustomerMasterID')));

        foreach($companyid as $key => $val){
            if(!empty($customerMasterID[$key])){
                $data['groupCustomerMasterID'] = trim($this->input->post('groupCustomerMasterID') ?? '');
                $data['customerMasterID'] = trim($customerMasterID[$key]);
                $data['companyID'] = trim($val);

                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                $data['companyGroupID'] = $grpid;
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

                $results = $this->db->insert('srp_erp_groupcustomerdetails', $data);
            }
            //$last_id = $this->db->insert_id();
        }

        if ($results) {
            return array('s', 'Customer Link Saved Successfully');
        } else {
            return array('e', 'Customer Link Save Failed');
        }
    }

    function delete_customer_link()
    {
        $this->db->where('groupCustomerDetailID', $this->input->post('groupCustomerDetailID'));
        $result = $this->db->delete('srp_erp_groupcustomerdetails');
        return array('s', 'Record Deleted Successfully');
    }

    function save_customer_duplicate(){
        $companyid = $this->input->post('checkedCompanies');
        $com = current_companyID();
        $grpid = $com;
        $masterGroupID=getParentgroupMasterID();
        $results='';
        $comparr=array();

        $policyCustomer = getPolicyValues('CM', 'All');
        $this->load->library('sequence');

        $customerSystemCode = null;
        $sequenceGenerated = false;

        foreach($companyid as $val)
        {
            $i=0;
            $this->db->select('groupCustomerDetailID');
            $this->db->where('groupCustomerMasterID', $this->input->post('customerAutoIDDuplicatehn'));
            $this->db->where('companyID', $val);
            $this->db->where('companyGroupID', $masterGroupID);
            $linkexsist = $this->db->get('srp_erp_groupcustomerdetails')->row_array();

            $this->db->select('*');
            $this->db->where('groupCustomerAutoID', $this->input->post('customerAutoIDDuplicatehn'));
            $CurrentCus = $this->db->get('srp_erp_groupcustomermaster')->row_array();

            $this->db->select('partyCategoryID');
            $this->db->where('groupPartyCategoryID', $CurrentCus['partyCategoryID']);
            $this->db->where('companyID', $val);
            $this->db->where('companyGroupID', $masterGroupID);
            $categorylinkexsist = $this->db->get('srp_erp_grouppartycategorydetails')->row_array();

            if (empty($categorylinkexsist))
            {
                $i++;
                $companyName = get_companyData($val);
                $this->db->select('categoryDescription');
                $this->db->where('partyCategoryID', $CurrentCus['partyCategoryID']);
                $partyDesc = $this->db->get('srp_erp_grouppartycategories')->row_array();
                array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Category not linked" ." (".$partyDesc['categoryDescription'].")" ));
            }

            $this->db->select('chartofAccountID');
            $this->db->where('groupChartofAccountMasterID', $CurrentCus['receivableAutoID']);
            $this->db->where('companyID', $val);
            $this->db->where('companyGroupID', $masterGroupID);
            $categoryCOAexsist = $this->db->get('srp_erp_groupchartofaccountdetails')->row_array();

            if (empty($categoryCOAexsist))
            {
                $i++;
                $companyName = get_companyData($val);
                $this->db->select('GLSecondaryCode');
                $this->db->where('GLAutoID', $CurrentCus['receivableAutoID']);
                $glDesc = $this->db->get('srp_erp_groupchartofaccounts')->row_array();
                array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Chart of Account not linked" ." (".$glDesc['GLSecondaryCode'].")" ));
            }

            $this->db->select('customerAutoID');
            $this->db->where('customerName', $CurrentCus['groupCustomerName']);
            $this->db->where('companyID', $val);
            $CurrentCOAexsist = $this->db->get('srp_erp_customermaster')->row_array();

            if (!empty($CurrentCOAexsist)) {
                $i++;
                $companyName = get_companyData($val);

                array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Customer name already exist" . " (" . $CurrentCus['groupCustomerName'] . ")"));
            }

            if($i==0)
            {
                if(empty($linkexsist))
                {
                    $data['isActive'] = 1;
                    $data['secondaryCode'] = $CurrentCus['secondaryCode'];
                    $data['customerName'] = $CurrentCus['groupCustomerName'];
                    $data['customerCountry'] = $CurrentCus['customerCountry'];
                    $data['customerTelephone'] = $CurrentCus['customerTelephone'];
                    $data['customerEmail'] = $CurrentCus['customerEmail'];
                    $data['customerUrl'] = $CurrentCus['customerUrl'];
                    $data['customerFax'] = $CurrentCus['customerFax'];
                    $data['customerAddress1'] = $CurrentCus['customerAddress1'];
                    $data['customerAddress2'] = $CurrentCus['customerAddress2'];
                    $data['taxGroupID'] = $CurrentCus['taxGroupID'];
                    $data['vatIdNo'] = $CurrentCus['vatIdNo'];
                    $data['partyCategoryID'] = $categorylinkexsist['partyCategoryID'];
                    $data['receivableAutoID'] = $categoryCOAexsist['chartofAccountID'];
                    $recglDet=fetch_gl_account_desc_cus_group_company($categoryCOAexsist['chartofAccountID'],$val);
                    $data['receivableSystemGLCode'] = $recglDet['systemAccountCode'];
                    $data['receivableGLAccount'] = $recglDet['GLSecondaryCode'];
                    $data['receivableDescription'] = $recglDet['GLDescription'];
                    $data['receivableType'] = $recglDet['subCategory'];
                    $data['customerCreditPeriod'] = $CurrentCus['customerCreditPeriod'];
                    $data['customerCreditLimit'] = $CurrentCus['customerCreditLimit'];
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];
                    $data['customerCurrencyID'] = $CurrentCus['customerCurrencyID'];
                    $data['customerCurrency'] = $CurrentCus['customerCurrency'];
                    $data['customerCurrencyDecimalPlaces'] = $CurrentCus['customerCurrencyDecimalPlaces'];
                    $data['companyID'] = $val;
                    $companyCode = get_companyData($val);
                    $data['companyCode'] = $companyCode['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    
                    if (1 == $policyCustomer && false === $sequenceGenerated)
                    {
                        $customerSystemCode = $this->sequence->sequence_generator_group('CUS', 0, $grpid, $this->common_data['company_data']['groupCode'], $grpid);
                        $sequenceGenerated = true;
                    }
                    
                    if (0 == $policyCustomer)
                    {
                        $customerSystemCode = $this->sequence->sequence_generator_group('CUS', 0, $val, $companyCode['company_code']);
                    }

                    $data['customerSystemCode'] = $customerSystemCode;
                    
                    $this->db->insert('srp_erp_customermaster', $data);
                    $last_id = $this->db->insert_id();


                    $dataLink['groupCustomerMasterID'] = trim($this->input->post('customerAutoIDDuplicatehn') ?? '');
                    $dataLink['customerMasterID'] = trim($last_id);
                    $dataLink['companyID'] = trim($val);
                    $dataLink['companyGroupID'] = $masterGroupID;

                    $dataLink['createdPCID'] = $this->common_data['current_pc'];
                    $dataLink['createdUserID'] = $this->common_data['current_userID'];
                    $dataLink['createdUserName'] = $this->common_data['current_user'];
                    $dataLink['createdDateTime'] = $this->common_data['current_date'];

                    $results = $this->db->insert('srp_erp_groupcustomerdetails', $dataLink);

                }
            }
            else
            {
                continue;
            }
        }

        if ($results) {
            return array('s', 'Customer Replicated Successfully',$comparr);
        } else {
            return array('e', 'Customer Replication not successful',$comparr);
        }

    }
    function updategroppolicy()
{
    $groupPolicyvalue = $this->input->post('policyValue');

    $groupPolicymasterID = $this->input->post('groupPolicymasterID');
    $companyid = current_companyID();
    $this->db->delete('srp_erp_grouppolicy', array('groupPolicymasterID' => $groupPolicymasterID));
    $data['groupPolicymasterID'] = $groupPolicymasterID;
    $data['groupID'] = $companyid;
    $data['code'] = 'CM';
    $data['documentID'] = 'All';
    $data['isYN'] = $groupPolicyvalue;
    $data['value'] = $groupPolicyvalue;
    $data['createdUserGroup'] = $this->common_data['user_group'];
    $data['createdPCID'] = $this->common_data['current_pc'];
    $data['createdUserID'] = $this->common_data['current_userID'];
    $data['modifiedUserName'] = $this->common_data['current_user'];
    $data['createdDateTime'] = $this->common_data['current_date'];
    $results = $this->db->insert('srp_erp_grouppolicy', $data);
    if ($results) {
        return array('s', 'Customer Master policy updated successfully');
    } else {
        return array('e', 'Customer Master policy updated failed');
    }

}

}