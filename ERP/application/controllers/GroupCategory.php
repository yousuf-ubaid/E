<?php
class GroupCategory extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Group_category_model');
        $this->load->helpers('group_management');
    }

    function fetch_customer_category()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $this->datatables->select('partyCategoryID,partyType,categoryDescription')
            ->where('groupID', $companyID)
            ->where('partyType', 1)
            ->from('srp_erp_grouppartycategories');
        $this->datatables->add_column('edit', '$1', 'editgroupcategory(partyCategoryID)');
        echo $this->datatables->generate();
    }

    function saveCategory()
    {
        if (empty($this->input->post('categoryDescription')))
        {
            echo json_encode(['e', 'Enter Category']);
        }
        else
        {
            echo json_encode($this->Group_category_model->saveCategory());
        }
    }

    function getCategory()
    {
        echo json_encode($this->Group_category_model->getCategory());
    }

    function delete_category()
    {
        echo json_encode($this->Group_category_model->delete_category());
    }

    function fetch_supplier_category()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $this->datatables->select('partyCategoryID,partyType,categoryDescription')
            ->where('groupID', $companyID)
            ->where('partyType', 2)
            ->from('srp_erp_grouppartycategories');
        $this->datatables->add_column('edit', '$1', 'editsuppliergroupcategory(partyCategoryID)');
        echo $this->datatables->generate();
    }

    function saveSupplierCategory()
    {
        $this->form_validation->set_rules('categoryDescription', 'Category', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        }
        else
        {
            echo json_encode($this->Group_category_model->saveSupplierCategory());
        }
    }

    function getSupplierCategory()
    {
        echo json_encode($this->Group_category_model->getSupplierCategory());
    }

    function load_all_companies_customer_categories()
    {
        $company = array();
        $groupCustomerCategoryID = $this->input->post('groupCustomerCategoryID');
        $comp = customer_company_link($groupCustomerCategoryID);
        foreach ($comp as $val)
        {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        $data['groupCustomerCategoryID'] = $groupCustomerCategoryID;
        $html = $this->load->view('system/GroupCategory/ajax/erp_load_company_customercategory', $data, true);
        echo $html;
    }

    function load_category_header()
    {
        echo json_encode($this->Group_category_model->load_category_header());
    }

    function save_customer_category_link()
    {

        $this->form_validation->set_rules('companyIDgrp[]', 'Company', 'trim|required');
        //$this->form_validation->set_rules('partyCategoryID[]', 'party Category', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('e', validation_errors()));
        }
        else
        {
            echo json_encode($this->Group_category_model->save_customer_category_link());
        }
    }

    function load_all_companies_supplier_categories()
    {
        $company = array();
        $groupSupplierCategoryID = $this->input->post('groupSupplierCategoryID');
        $comp = customer_company_link($groupSupplierCategoryID);
        foreach ($comp as $val)
        {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        $data['groupSupplierCategoryID'] = $groupSupplierCategoryID;
        $html = $this->load->view('system/GroupCategory/ajax/erp_load_company_suppliercategory', $data, true);
        echo $html;
    }

    function save_supplier_category_link()
    {

        $this->form_validation->set_rules('companyIDgrp[]', 'Company', 'trim|required');
        //$this->form_validation->set_rules('partyCategoryID[]', 'party Category', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('e', validation_errors()));
        }
        else
        {
            echo json_encode($this->Group_category_model->save_supplier_category_link());
        }
    }
    function load_all_customer_companies_duplicate()
    {
        $company = array();
        $groupCustomerCategoryID = $this->input->post('groupCustomerCategoryID');
        // $masterAccountYN=$this->input->post('masterAccountYN');
        $data['extra'] = $this->Group_category_model->fetch_customer_category_details();
        $comp = customer_company_link($groupCustomerCategoryID);
        foreach ($comp as $val)
        {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        // $data['masterAccountYN']=$masterAccountYN;
        $data['groupCustomerCategoryID'] = $groupCustomerCategoryID;
        $html = $this->load->view('system/GroupCategory/ajax/erp_load_company_customercategory_duplicate', $data, true);
        echo $html;
    }

    function save_customer_category_duplicate()
    {
        $this->form_validation->set_rules('checkedCompanies[]', 'Company', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('e', validation_errors()));
        }
        else
        {
            echo json_encode($this->Group_category_model->save_customer_category_duplicate());
        }
    }

    function load_all_supplier_companies_duplicate()
    {
        $company = array();
        $groupSupplierCategoryID = $this->input->post('groupSupplierCategoryID');
        // $masterAccountYN=$this->input->post('masterAccountYN');
        $data['extra'] = $this->Group_category_model->fetch_supplier_category_details();
        $comp = customer_company_link($groupSupplierCategoryID);
        foreach ($comp as $val)
        {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        // $data['masterAccountYN']=$masterAccountYN;
        $data['groupSupplierCategoryID'] = $groupSupplierCategoryID;
        $html = $this->load->view('system/GroupCategory/ajax/erp_load_company_suppliercategory_duplicate', $data, true);
        echo $html;
    }

    function save_supplier_category_duplicate()
    {
        $this->form_validation->set_rules('checkedCompanies[]', 'Company', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('e', validation_errors()));
        }
        else
        {
            echo json_encode($this->Group_category_model->save_supplier_category_duplicate());
        }
    }
}
