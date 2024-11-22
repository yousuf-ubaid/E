<?php
class Group_warehouseMaster extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Group_warehousemasterModel');
        $this->load->helpers('group_management');
    }

    public function index()
    {
        $data['title'] = 'Warehouse Master';
        $data['main_content'] = 'srp_warehousemaster_view';
        $data['extra'] = NULL;
        $this->load->model('Srp_warehousemasterModel');
        $this->load->view('includes/template', $data);
    }

    function load_warehousemastertable()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();

        $this->datatables->select('wareHouseAutoID,wareHouseCode,wareHouseDescription,wareHouseLocation')
            ->where('groupID', $companyID)
            ->from('srp_erp_groupwarehousemaster')
            //$this->datatables->join('srp_countrymaster', 'srp_erp_suppliermaster.supplierCountryID = srp_countrymaster.countryID', 'left')
            ->edit_column('action', '<a onclick="load_duplicate_warehouse($1)"><span title="Replicate" rel="tooltip" class="glyphicon glyphicon-duplicate"></span></a>&nbsp;|&nbsp;<a onclick="link_group_warehouse($1)"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link" ></span></a>&nbsp;|&nbsp;<a href="#" onclick="openwarehousemastermodel($1)"><span title="Edit" class="glyphicon glyphicon-pencil"  rel="tooltip"></span></a>', 'wareHouseAutoID');

        echo $this->datatables->generate();
    }

    function save_warehousemaster()
    {
        $this->form_validation->set_rules('warehousedescription', 'Warehouse Description', 'trim|required');
        $this->form_validation->set_rules('warehouselocation', 'Warehouse Location', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        }
        else
        {
            echo json_encode($this->Group_warehousemasterModel->save_warehousemaster());
        }
    }

    function edit_warehouse()
    {
        if ($this->input->post('id') != "")
        {
            echo json_encode($this->Group_warehousemasterModel->get_warehouse());
        }
        else
        {
            echo json_encode(FALSE);
        }
    }

    function load_company()
    {
        $data['wareHouseAutoID'] = $this->input->post('wareHouseAutoID');
        $html = $this->load->view('system/GroupWarehouse/ajax/ajax-erp_load_company', $data, true);
        echo $html;
    }

    function load_Warehouse()
    {
        $data['companyID'] = $this->input->post('companyID');
        $data['groupwareHouseAutoID'] = $this->input->post('groupwareHouseAutoID');
        $html = $this->load->view('system/GroupWarehouse/ajax/erp_load_company_warehouses', $data, true);
        echo $html;
    }

    function fetch_warehouse_Details()
    {
        $groupWarehouseMasterID = $this->input->post('groupWarehouseMasterID');

        $this->datatables->select('groupWarehouseDetailID,groupWarehouseMasterID,srp_erp_groupwarehousedetails.warehosueMasterID,srp_erp_groupwarehousedetails.companyID,srp_erp_warehousemaster.wareHouseCode as wareHouseCode,srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription,srp_erp_company.company_name as company_name');
        $this->datatables->from('srp_erp_groupwarehousedetails');
        $this->datatables->join('srp_erp_warehousemaster', 'srp_erp_groupwarehousedetails.warehosueMasterID = srp_erp_warehousemaster.wareHouseAutoID');
        $this->datatables->join('srp_erp_company', 'srp_erp_groupwarehousedetails.companyID = srp_erp_company.company_id');
        $this->datatables->where('srp_erp_groupwarehousedetails.groupWarehouseMasterID', $groupWarehouseMasterID);
        //$this->datatables->where('srp_erp_groupchartofaccountdetails.companyGroupID', $grpid);
        $this->datatables->add_column('edit', '<a onclick="delete_warehouse_link($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'groupWarehouseDetailID');
        echo $this->datatables->generate();
    }

    function delete_warehouse_link()
    {
        echo json_encode($this->Group_warehousemasterModel->delete_warehouse_link());
    }

    function save_warehouse_link()
    {

        $this->form_validation->set_rules('companyIDgrp[]', 'Company', 'trim|required');
        //$this->form_validation->set_rules('warehosueMasterID[]', 'Warehouse', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('e', validation_errors()));
        }
        else
        {
            echo json_encode($this->Group_warehousemasterModel->save_warehouse_link());
        }
    }

    function load_all_companies_warehouses()
    {
        $company = array();
        $groupwareHouseAutoID = $this->input->post('groupwareHouseAutoID');
        $comp = customer_company_link($groupwareHouseAutoID);
        foreach ($comp as $val)
        {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        $data['groupwareHouseAutoID'] = $groupwareHouseAutoID;
        $html = $this->load->view('system/GroupWarehouse/ajax/erp_load_company_warehouses', $data, true);
        echo $html;
    }

    function load_warehouse_header()
    {
        echo json_encode($this->Group_warehousemasterModel->load_warehouse_header());
    }

    function load_all_companies_duplicate()
    {
        $company = array();
        $warehouseID = $this->input->post('warehouseID');
        // $masterAccountYN=$this->input->post('masterAccountYN');
        $data['extra'] = $this->Group_warehousemasterModel->fetch_warehouse_details();
        $comp = customer_company_link($warehouseID);
        foreach ($comp as $val)
        {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        // $data['masterAccountYN']=$masterAccountYN;
        $data['warehouseID'] = $warehouseID;
        $html = $this->load->view('system/GroupWarehouse/ajax/erp_load_company_warehouses_duplicate', $data, true);
        echo $html;
    }

    function save_warehouse_duplicate()
    {
        $this->form_validation->set_rules('checkedCompanies[]', 'Company', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('e', validation_errors()));
        }
        else
        {
            echo json_encode($this->Group_warehousemasterModel->save_warehouse_duplicate());
        }
    }
}
