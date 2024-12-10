<?php

class CompanyPolicy extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('CompanyPolicy_model');
        $this->load->helper('companypolicy_helper');
    }

    function fetch_company_policy()
    {
        $companyId = current_companyID();

        $data['detail'] = $this->db->query("SELECT `policymaster`.`companypolicymasterID` AS `companypolicymasterID`, `policymaster`.`code` AS `code`, `policymaster`.`companyPolicyDescription` AS `companyPolicyDescription`, `policymaster`.`fieldType` AS `fieldType`, `policydetails`.`value` AS `companyValue`, `policymaster`.`documentID` AS `documentID`,0 AS isCompanyLevel, `policymaster`.`moduleID` AS `moduleID` FROM `srp_erp_companypolicymaster` `policymaster` LEFT JOIN ( SELECT * FROM `srp_erp_companypolicy` WHERE companyID = '{$companyId}' ) `policydetails` ON `policymaster`.`companypolicymasterID` = `policydetails`.`companypolicymasterID` WHERE `policymaster`.`isCompanyLevel` = 0 GROUP BY companypolicymasterID UNION SELECT `policymaster`.`companypolicymasterID` AS `companypolicymasterID`, `policymaster`.`companyPolicyDescription` AS `companyPolicyDescription`,`policymaster`.`code` AS `code`, `policymaster`.`fieldType` AS `fieldType`, `policydetails`.`value` AS `companyValue`, `policymaster`.`documentID` AS `documentID`,1 AS isCompanyLevel, `policymaster`.`moduleID` AS `moduleID` FROM `srp_erp_companypolicymaster` `policymaster` LEFT JOIN ( SELECT srp_erp_companypolicy.companyPolicyAutoID, srp_erp_companypolicy.companypolicymasterID, srp_erp_companypolicy.companyID, srp_erp_companypolicy.documentID, srp_erp_companypolicy.`value` FROM `srp_erp_companypolicy` INNER JOIN srp_erp_companypolicymaster_value ON srp_erp_companypolicy.companypolicymasterID = srp_erp_companypolicymaster_value.companypolicymasterID WHERE srp_erp_companypolicy.companyID = '{$companyId}' ) `policydetails` ON `policymaster`.`companypolicymasterID` = `policydetails`.`companypolicymasterID` WHERE `policymaster`.`isCompanyLevel` = 1 AND `policydetails`.`companyID` = '{$companyId}' GROUP BY companypolicymasterID")->result_array();
        $data['moduleID'] = $this->db->query("SELECT `policymaster`.`moduleID` AS `moduleID` FROM `srp_erp_companypolicymaster` `policymaster` GROUP BY moduleID ORDER BY moduleID DESC")->result_array();
        
        echo $this->load->view('system/erp_company_policy_table', $data, true);

    }

    function master_policy_update()
    {
        $this->form_validation->set_rules('autoID', 'ID Is Missing', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CompanyPolicy_model->master_policy_update());
        }
    }

    function policy_detail_update()
    {
        $this->form_validation->set_rules('id', 'Id is missing.', 'trim|required');
        $this->form_validation->set_rules('value', 'Value is required.', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->CompanyPolicy_model->policy_detail_update());
        }
    }

    function get_document_policy()
    {
        $documentID = $this->input->post('documentID');
        $companyID = current_companyID();
        $getPolicy = $this->db->query("SELECT companypolicymasterID,value FROM srp_erp_companypolicy WHERE `companyID` = '{$companyID}' AND `documentID` = '{$documentID}'")->result_array();
        echo json_encode($getPolicy);
    }

    function policy()
    {
        $this->load->view('system/company/feed-policy');
    }

    function get_password_policy(){
        echo json_encode($this->CompanyPolicy_model->get_password_policy());
    }

    function save_password_complexity(){
        echo json_encode($this->CompanyPolicy_model->save_password_complexity());
    }

    function get_usergroupfor_policy(){
        $companyId = current_companyID();
        $polcyId=$this->input->post('polcyId');
        $data['detail'] = $this->db->query("SELECT
	srp_erp_usergroups.userGroupID,
	srp_erp_usergroups.description,
	rst.policyUserGroupID,
	rst.companypolicymasterID,
	rst.docID
FROM
	srp_erp_usergroups
	LEFT JOIN (
SELECT
	policyUserGroupID,
	companypolicymasterID,
	documentID,
	userGroupID,
	GROUP_CONCAT( documentID ) AS docID 
FROM
	srp_erp_documentpolicyusergroup 
WHERE
	companypolicymasterID = '$polcyId' 
	AND companyID = '$companyId' 
GROUP BY
	userGroupID 
	) rst ON srp_erp_usergroups.userGroupID=rst.userGroupID
	WHERE companyID='$companyId'
	AND isActive=1  
	ORDER BY srp_erp_usergroups.userGroupID")->result_array();
        echo $this->load->view('system/erp_assign_user_group_to_pv_rv_table', $data, true);
    }


    function saveusergrouptopolicy(){
        echo json_encode($this->CompanyPolicy_model->saveusergrouptopolicy());
    }
    function fetch_company_policy_modulewise()
    {
        $companyId = current_companyID();
        $moduleID = $this->input->post('moduleID');
        $data['detail'] = $this->db->query("SELECT `policymaster`.`companypolicymasterID` AS `companypolicymasterID`, `policymaster`.`code` AS `code`, `policymaster`.`companyPolicyDescription` AS `companyPolicyDescription`, `policymaster`.`fieldType` AS `fieldType`, `policydetails`.`value` AS `companyValue`, `policymaster`.`documentID` AS `documentID`,0 AS isCompanyLevel, `policymaster`.`moduleID` FROM `srp_erp_companypolicymaster` `policymaster` LEFT JOIN ( SELECT * FROM `srp_erp_companypolicy` WHERE companyID = '{$companyId}' ) `policydetails` ON `policymaster`.`companypolicymasterID` = `policydetails`.`companypolicymasterID` WHERE `policymaster`.`isCompanyLevel` = 0 AND policymaster.moduleID = '{$moduleID}'  GROUP BY companypolicymasterID UNION SELECT `policymaster`.`companypolicymasterID` AS `companypolicymasterID`, `policymaster`.`companyPolicyDescription` AS `companyPolicyDescription`,`policymaster`.`code` AS `code`, `policymaster`.`fieldType` AS `fieldType`, `policydetails`.`value` AS `companyValue`, `policymaster`.`documentID` AS `documentID`,1 AS isCompanyLevel, `policymaster`.`moduleID` FROM `srp_erp_companypolicymaster` `policymaster` LEFT JOIN ( SELECT srp_erp_companypolicy.companyPolicyAutoID, srp_erp_companypolicy.companypolicymasterID, srp_erp_companypolicy.companyID, srp_erp_companypolicy.documentID, srp_erp_companypolicy.`value` FROM `srp_erp_companypolicy` INNER JOIN srp_erp_companypolicymaster_value ON srp_erp_companypolicy.companypolicymasterID = srp_erp_companypolicymaster_value.companypolicymasterID WHERE srp_erp_companypolicy.companyID = '{$companyId}' ) `policydetails` ON `policymaster`.`companypolicymasterID` = `policydetails`.`companypolicymasterID` WHERE `policymaster`.`isCompanyLevel` = 1 AND `policydetails`.`companyID` = '{$companyId}' AND policymaster.moduleID = '{$moduleID}' GROUP BY companypolicymasterID")->result_array();
        $data['moduleID'] = [['moduleID' => $moduleID]];
        echo $this->load->view('system/erp_company_policy_table', $data, true);

    }
}