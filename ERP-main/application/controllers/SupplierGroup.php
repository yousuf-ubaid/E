<?php

class SupplierGroup extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Suppliermastergroup_model');
        $this->load->helpers('group_management');
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
        $category_filter = '';
        $currency_filter = '';
        $supplier = $this->input->post('supplierCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND groupSupplierAutoID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join("' , '", $category) . " )";
            $category_filter = " AND srp_erp_groupsuppliermaster.partyCategoryID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join("' , '", $currency) . " )";
            $currency_filter = " AND supplierCurrencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $companyid);
        $grp= $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid=$companyid;

        $where = "srp_erp_groupsuppliermaster.companygroupID = " . $grpid . $supplier_filter . $category_filter . $currency_filter . "";
        $this->datatables->select('srp_erp_grouppartycategories.categoryDescription as categoryDescription,groupSupplierAutoID,groupSupplierSystemCode,groupSupplierName,secondaryCode,supplierAddress1,supplierAddress2,supplierCountry,supplierTelephone,supplierEmail,supplierUrl,supplierFax,isActive,supplierCurrency,supplierCurrencyID')
            ->where($where)
            ->from('srp_erp_groupsuppliermaster')
            ->join('srp_erp_grouppartycategories', 'srp_erp_groupsuppliermaster.partyCategoryID = srp_erp_grouppartycategories.partyCategoryID', 'left');
            /*->join('(SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate)*-1 as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = "SUP" AND subLedgerType=2 GROUP BY partyAutoID) cust', 'cust.partyAutoID = srp_erp_suppliermaster.supplierAutoID', 'left');*/
        $this->datatables->add_column('supplier_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b> Email </b> $7  &nbsp;&nbsp;&nbsp;<b>Telephone</b> $8', 'groupSupplierName,supplierAddress1, supplierAddress2, supplierCountry, secondaryCode, supplierCurrency, supplierEmail,supplierTelephone');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', '0,supplierCurrency');
        $this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="load_duplicate_supplier($1)"><span title="" rel="tooltip" class="glyphicon glyphicon-duplicate" data-original-title="Replicate"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="openLinkModal($1)"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/GroupMaster/erp_supplier_group_master_new\',$1,\'Edit Supplier\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span>', 'groupSupplierAutoID');
        echo $this->datatables->generate();
    }

    function save_suppliermaster()
    {
        if (!$this->input->post('groupSupplierAutoID')) {
            $this->form_validation->set_rules('supplierCurrency', 'supplier Currency', 'trim|required');
        }
        $this->form_validation->set_rules('suppliercode', 'Supplier Code', 'trim|required');
        $this->form_validation->set_rules('supplierName', 'supplier Name', 'trim|required');
        $this->form_validation->set_rules('suppliercountry', 'supplier country', 'trim|required');
        $this->form_validation->set_rules('liabilityAccount', 'liabilityAccount', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Suppliermastergroup_model->save_supplier_master());
        }
    }


    function edit_supplier()
    {
        if ($this->input->post('id') != "") {
            echo json_encode($this->Suppliermastergroup_model->get_supplier());
        } else {
            echo json_encode(FALSE);
        }
    }

    function load_supplier_header()
    {
        echo json_encode($this->Suppliermastergroup_model->load_supplier_header());
    }

    function delete_supplier()
    {
        echo json_encode($this->Suppliermastergroup_model->delete_supplier());
    }

    function fetch_supplier_link()
    {

        $groupSupplierMasterID = $this->input->post('groupSupplierMasterID');
        $where = "srp_erp_groupsupplierdetails.groupSupplierMasterID = " . $groupSupplierMasterID ."";
        $this->datatables->select('groupSupplierDetailID,groupSupplierMasterID,SupplierMasterID,srp_erp_groupsupplierdetails.companyID,companyGroupID,srp_erp_suppliermaster.supplierSystemCode as supplierSystemCode,srp_erp_suppliermaster.supplierName as supplierName,srp_erp_suppliermaster.supplierAddress1 as supplierAddress1,srp_erp_suppliermaster.supplierAddress2 as supplierAddress2,srp_erp_suppliermaster.supplierTelephone as supplierTelephone,srp_erp_suppliermaster.supplierEmail as supplierEmail,srp_erp_suppliermaster.supplierCurrency as supplierCurrency,srp_erp_suppliermaster.supplierCountry as supplierCountry,srp_erp_suppliermaster.secondaryCode as secondaryCode,srp_erp_company.company_name as company_name,srp_erp_chartofaccounts.GLDescription as GLDescription')
            ->where($where)
            ->from('srp_erp_groupsupplierdetails')
            ->join('srp_erp_suppliermaster', 'srp_erp_groupsupplierdetails.SupplierMasterID = srp_erp_suppliermaster.supplierAutoID','left')
            ->join('srp_erp_company', 'srp_erp_groupsupplierdetails.companyID = srp_erp_company.company_id','left')
            ->join('srp_erp_chartofaccounts', 'srp_erp_suppliermaster.liabilityAutoID = srp_erp_chartofaccounts.GLAutoID','left');
        $this->datatables->add_column('supplier_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b>supplier Currency : </b>$6 &nbsp;&nbsp;&nbsp;<b> Email </b> $7  <b>Telephone</b> $8','supplierName,supplierAddress1, supplierAddress2, supplierCountry, secondaryCode, supplierCurrency, supplierEmail,supplierTelephone');
        //$this->datatables->add_column('edit', '$1', 'editcustomerGroup(groupCustomerAutoID)');
        $this->datatables->add_column('edit', '<a onclick="delete_supplier_link($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'groupSupplierDetailID');
        echo $this->datatables->generate();
    }

    function load_company()
    {
        $data['groupSupplierMasterID'] = $this->input->post('groupSupplierMasterID');
        $html = $this->load->view('system/GroupMaster/ajax-erp_load_supplier_company', $data, true);
        echo $html;
    }

    function load_comapny_suppliers()
    {
        $data['companyID'] = $this->input->post('companyID');
        $html = $this->load->view('system/GroupMaster/ajax-erp_load_company_suppliers', $data, true);
        echo $html;
    }

    function save_supplier_link()
    {

        $this->form_validation->set_rules('companyIDgrp[]', 'Company', 'trim|required');
        //$this->form_validation->set_rules('SupplierMasterID[]', 'Supplier', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Suppliermastergroup_model->save_supplier_link());
        }
    }

    function delete_supplier_link(){
        echo json_encode($this->Suppliermastergroup_model->delete_supplier_link());
    }

    function load_all_companies_supliers(){
        $company=array();
        $groupSupplierMasterID=$this->input->post('groupSupplierMasterID');
        $comp = customer_company_link($groupSupplierMasterID);
        foreach($comp as $val){
            $company[]=$val['companyID'];
        }
        $data['companyID']=$company;
        $data['groupSupplierMasterID']=$groupSupplierMasterID;
        $html = $this->load->view('system/GroupMaster/ajax-erp_load_company_suppliers', $data, true);
        echo $html;
    }

    function load_supplier_heading()
    {
        echo json_encode($this->Suppliermastergroup_model->load_supplier_heading());
    }

    function load_all_companies_duplicate(){
        $company=array();
        $groupSupplierAutoID=$this->input->post('groupSupplierAutoID');
        $comp = customer_company_link($groupSupplierAutoID);
        foreach($comp as $val){
            $company[]=$val['companyID'];
        }
        $data['companyID']=$company;
        $data['groupSupplierAutoID']=$groupSupplierAutoID;
        $html = $this->load->view('system/GroupMaster/ajax_erp_load_company_suppliers_duplicate', $data, true);
        echo $html;
    }

    function save_supplier_duplicate()
    {
        $this->form_validation->set_rules('checkedCompanies[]', 'Company', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Suppliermastergroup_model->save_supplier_duplicate());
        }
    }
    function updategroppolicy()
    {
        echo json_encode($this->Suppliermastergroup_model->updategroppolicy());
    }

}
