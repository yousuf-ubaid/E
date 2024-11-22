<?php

class CompanyTemplate extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('CompanyTemplate_model');
        //$this->load->helper('CompanyPolicy_helper');
    }

    function fetch_template_configuration()
    {
        $data['detail'] = $this->db->query("SELECT srp_erp_templatemaster.*,srp_erp_templates.TempMasterID as detailTempMasterID FROM `srp_erp_templatemaster` LEFT JOIN srp_erp_templates ON srp_erp_templatemaster.TempMasterID =  srp_erp_templates.TempMasterID ORDER BY srp_erp_templatemaster.FormCatID")->result_array();
        echo $this->load->view('system/erp_template_configuration_table', $data, true);

    }

    function fetch_sub_template_configuration()
    {
        $data['detail'] = $this->db->query("SELECT
	srp_erp_templatemaster.*, srp_erp_companysubgrouptemplates.TempMasterID AS detailTempMasterID
FROM
	`srp_erp_templatemaster`
LEFT JOIN srp_erp_companysubgrouptemplates ON srp_erp_templatemaster.TempMasterID = srp_erp_companysubgrouptemplates.TempMasterID
LEFT JOIN srp_erp_navigationmenus ON srp_erp_companysubgrouptemplates.navigationMenuID = srp_erp_navigationmenus.navigationMenuID
WHERE
srp_erp_navigationmenus.isGroup=1
AND
srp_erp_navigationmenus.url != '#'
ORDER BY
	srp_erp_templatemaster.FormCatID")->result_array();
        echo $this->load->view('system/erp_sub_template_configuration_table', $data, true);

    }

    function saveTemplate(){
        echo json_encode($this->CompanyTemplate_model->saveTemplate());
    }

    function savesubTemplates(){
        echo json_encode($this->CompanyTemplate_model->savesubTemplates());
    }


}