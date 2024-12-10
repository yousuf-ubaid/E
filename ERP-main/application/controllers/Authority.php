<?php

class Authority extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Authoritymaster_model');
    }

    function fetch_authority()
    {
        $supplier_filter = '';
        $currency_filter = '';
        $supplier = $this->input->post('supplierCode');
        //$category = $this->input->post('category');
        $currency = $this->input->post('currency');
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND taxAuthourityMasterID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join("' , '", $currency) . " )";
            $currency_filter = " AND currencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_taxauthorithymaster.companyID = " . $companyid . $supplier_filter . $currency_filter . "";
        $this->datatables->select('taxAuthourityMasterID,authoritySystemCode as authoritySystemCode,AuthorityName as AuthorityName,authoritySecondaryCode as authoritySecondaryCode,address as address,telephone as telephone,email as email,fax,currencyID')
            ->where($where)
            ->from('srp_erp_taxauthorithymaster');
        $this->datatables->add_column('authority_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$3<br><b>Address : </b> $2 &nbsp;&nbsp;.<br><b> Email </b> $5  &nbsp;&nbsp;&nbsp;<b>Telephone</b> $6', 'AuthorityName,address, authoritySecondaryCode, currencyID, email,telephone');
        // $this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="fetchPage(\'system/tax/erp_authority_master_new\',$1,\'Edit Authority\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_authority($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'taxAuthourityMasterID');
         $this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="fetchPage(\'system/tax/erp_authority_master_new\',$1,\'Edit Authority\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; </span>', 'taxAuthourityMasterID');
        //$this->datatables->add_column('edit', '$1', 'editsupplier(supplierAutoID)');
        echo $this->datatables->generate();
    }

    function save_authoritymaster()
    {
        if (!$this->input->post('taxAuthourityMasterID')) {
            $this->form_validation->set_rules('currencyID', 'Currency', 'trim|required');
        }
        $this->form_validation->set_rules('authoritySecondaryCode', 'Authority Code', 'trim|required');
        $this->form_validation->set_rules('AuthorityName', 'Authority Name', 'trim|required');
        $this->form_validation->set_rules('taxPayableGLAutoID', 'Liability Account', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Authoritymaster_model->save_supplier_master());
        }
    }

    function load_authority_header()
    {
        echo json_encode($this->Authoritymaster_model->load_authority_header());
    }

    function delete_authority()
    {
        echo json_encode($this->Authoritymaster_model->delete_authority());
    }

}
