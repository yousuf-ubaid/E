<?php
class CustomerGroup extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Customer_group_model');
        $this->load->helpers('group_management');
    }

    function fetch_customer()
    {
        $customer_filter = '';
        $category_filter = '';
        $currency_filter = '';
        $customer = $this->input->post('customerCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND groupCustomerAutoID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join("' , '", $category) . " )";
            $category_filter = " AND srp_erp_groupcustomermaster.partyCategoryID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join("' , '", $currency) . " )";
            $currency_filter = " AND customerCurrencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $this->db->select('companyGroupID');
        $this->db->where('companyID', $companyid);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();
        $grpid = current_companyID();

        $where = "srp_erp_groupcustomermaster.companygroupID = " . $grpid . $customer_filter . $category_filter . $currency_filter . "";
        $this->datatables->select('srp_erp_grouppartycategories.categoryDescription as categoryDescription,groupCustomerAutoID,groupcustomerSystemCode,secondaryCode,groupCustomerName,customerAddress1,customerAddress2,customerCountry,customerTelephone,customerEmail,customerUrl,customerFax,isActive,customerCurrency,customerEmail,customerTelephone,customerCurrencyID')
            ->where($where)
            ->from('srp_erp_groupcustomermaster')
            ->join('srp_erp_grouppartycategories', 'srp_erp_groupcustomermaster.partyCategoryID = srp_erp_grouppartycategories.partyCategoryID', 'left');
        /*->join('(SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate) as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = "CUS" AND subLedgerType=3 GROUP BY partyAutoID) cust', 'cust.partyAutoID = srp_erp_customermaster.customerAutoID','left');*/
        $this->datatables->add_column('customer_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>customer Currency : </b>$6 &nbsp;&nbsp;&nbsp;', 'groupCustomerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '$1', 'editcustomerGroup(groupCustomerAutoID)');
        $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', '0,customerCurrency');
        //$this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="attachment_modal($1,\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new\',$1,\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'customerAutoID');
        echo $this->datatables->generate();
    }

    function save_customer()
    {
        if (!$this->input->post('groupCustomerAutoID')) {
            $this->form_validation->set_rules('customerCurrency', 'customer Currency', 'trim|required');
        }
        $this->form_validation->set_rules('customercode', 'customer Code', 'trim|required');
        $this->form_validation->set_rules('customerName', 'customer Name', 'trim|required');
        $this->form_validation->set_rules('customercountry', 'customer country', 'trim|required');
        $this->form_validation->set_rules('receivableAccount', 'Receivable Account', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Customer_group_model->save_customer());
        }
    }

    function load_customer_header()
    {
        echo json_encode($this->Customer_group_model->load_customer_header());
    }

    function delete_customer()
    {
        echo json_encode($this->Customer_group_model->delete_customer());
    }

    function load_comapny_customers()
    {
        $data['companyID'] = $this->input->post('companyID');
        $html = $this->load->view('system/GroupMaster/ajax-erp_load_company_customers', $data, true);
        echo $html;
    }

    function save_customer_link()
    {

        $this->form_validation->set_rules('companyIDgrp[]', 'Company', 'trim|required');
        //$this->form_validation->set_rules('customerMasterID[]', 'Customer', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Customer_group_model->save_customer_link());
        }
    }

    function fetch_customer_link()
    {

        $groupCustomerMasterID = $this->input->post('groupCustomerMasterID');
        $where = "srp_erp_groupcustomerdetails.groupCustomerMasterID = " . $groupCustomerMasterID . "";
        $this->datatables->select('groupCustomerDetailID,groupCustomerMasterID,customerMasterID,srp_erp_groupcustomerdetails.companyID,companyGroupID,srp_erp_customermaster.customerSystemCode as customerSystemCode,srp_erp_customermaster.customerName as customerName,srp_erp_customermaster.customerAddress1 as customerAddress1,srp_erp_customermaster.customerAddress2 as customerAddress2,srp_erp_customermaster.customerTelephone as customerTelephone,srp_erp_customermaster.customerEmail as customerEmail,srp_erp_customermaster.customerCurrency as customerCurrency,srp_erp_customermaster.customerCountry as customerCountry,srp_erp_customermaster.secondaryCode as secondaryCode,srp_erp_company.company_name as company_name,srp_erp_chartofaccounts.GLDescription as GLDescription')
            ->where($where)
            ->from('srp_erp_groupcustomerdetails')
            ->join('srp_erp_customermaster', 'srp_erp_groupcustomerdetails.customerMasterID = srp_erp_customermaster.customerAutoID', 'left')
            ->join('srp_erp_company', 'srp_erp_groupcustomerdetails.companyID = srp_erp_company.company_id', 'left')
            ->join('srp_erp_chartofaccounts', 'srp_erp_customermaster.receivableAutoID = srp_erp_chartofaccounts.GLAutoID', 'left');
        $this->datatables->add_column('customer_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b>customer Currency : </b>$6 &nbsp;&nbsp;&nbsp;<b> Email </b> $7  <b>Telephone</b> $8', 'customerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone');
        //$this->datatables->add_column('edit', '$1', 'editcustomerGroup(groupCustomerAutoID)');
        $this->datatables->add_column('edit', '<a onclick="delete_customer_link($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'groupCustomerDetailID');
        echo $this->datatables->generate();
    }

    function delete_customer_link()
    {
        echo json_encode($this->Customer_group_model->delete_customer_link());
    }

    function load_company()
    {
        $data['groupCustomerMasterID'] = $this->input->post('groupCustomerMasterID');
        $html = $this->load->view('system/GroupMaster/ajax-erp_load_company', $data, true);
        echo $html;
    }

    function load_all_companies_customers()
    {
        $company = array();
        $groupCustomerAutoID = $this->input->post('groupCustomerAutoID');
        $comp = customer_company_link($groupCustomerAutoID);
        foreach ($comp as $val) {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        $data['groupCustomerAutoID'] = $groupCustomerAutoID;
        $html = $this->load->view('system/GroupMaster/ajax-erp_load_company_customers', $data, true);
        echo $html;
    }

    function load_all_companies_duplicate()
    {
        $company = array();
        $groupCustomerAutoID = $this->input->post('groupCustomerAutoID');
        $comp = customer_company_link($groupCustomerAutoID);
        foreach ($comp as $val) {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        $data['groupCustomerAutoID'] = $groupCustomerAutoID;
        $html = $this->load->view('system/GroupMaster/ajax-erp_load_company_customers_duplicate', $data, true);
        echo $html;
    }

    function save_customer_duplicate()
    {
        $this->form_validation->set_rules('checkedCompanies[]', 'Company', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Customer_group_model->save_customer_duplicate());
        }
    }

    function updategroppolicy()
    {
        echo json_encode($this->Customer_group_model->updategroppolicy());
    }
}
