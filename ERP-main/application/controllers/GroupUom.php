<?php
class GroupUom extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Group_uom_model');
        $this->load->helpers('group_management');
    }

    function fetch_umo_data()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $this->datatables->select("UnitID,UnitShortCode,UnitDes,modifiedUserName");
        $this->datatables->from('srp_erp_group_unit_of_measure');
        $this->datatables->where('groupID', $companyID);
        $this->datatables->add_column('edit', '$1', 'load_group_uom_action(UnitID,UnitDes,UnitShortCode)');
        echo $this->datatables->generate();
    }

    function save_uom()
    {
        $this->form_validation->set_rules('UnitShortCode', 'Code', 'trim|required');
        $this->form_validation->set_rules('UnitDes', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        }
        else
        {
            echo json_encode($this->Group_uom_model->save_uom());
        }
    }

    function fetch_convertion_detail_table()
    {
        echo json_encode($this->Group_uom_model->fetch_convertion_detail_table());
    }

    function change_conversion()
    {
        $this->form_validation->set_rules('masterUnitID', 'Master Unit ID', 'trim|required');
        $this->form_validation->set_rules('subUnitID', 'Sub Unit ID', 'trim|required');
        $this->form_validation->set_rules('conversion', 'Conversion', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        }
        else
        {
            echo json_encode($this->Group_uom_model->change_conversion());
        }
    }

    function save_uom_conversion()
    {
        $this->form_validation->set_rules('masterUnitID', 'Master Unit ID', 'trim|required');
        $this->form_validation->set_rules('subUnitID', 'Sub Unit ID', 'trim|required');
        $this->form_validation->set_rules('conversion', 'Conversion', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        }
        else
        {
            echo json_encode($this->Group_uom_model->save_uom_conversion());
        }
    }

    function load_company()
    {
        $data['groupUOMMasterID'] = $this->input->post('groupUOMMasterID');
        $html = $this->load->view('system/GroupUOM/ajax/ajax-erp_load_company', $data, true);
        echo $html;
    }

    function load_comapny_uom()
    {
        $data['companyID'] = $this->input->post('companyID');
        $data['groupUOMMasterID'] = $this->input->post('groupUOMMasterID');
        $html = $this->load->view('system/GroupUOM/ajax/erp_load_company_uom', $data, true);
        echo $html;
    }

    function fetch_uom_Details()
    {
        $groupUOMMasterID = $this->input->post('groupUOMMasterID');

        $this->datatables->select('groupUOMDetailID,groupUOMMasterID,srp_erp_groupuomdetails.UOMMasterID,srp_erp_groupuomdetails.companyID,srp_erp_unit_of_measure.UnitShortCode as UnitShortCode,srp_erp_unit_of_measure.UnitDes as UnitDes,srp_erp_company.company_name as company_name');
        $this->datatables->from('srp_erp_groupuomdetails');
        $this->datatables->join('srp_erp_unit_of_measure', 'srp_erp_groupuomdetails.UOMMasterID = srp_erp_unit_of_measure.UnitID');
        $this->datatables->join('srp_erp_company', 'srp_erp_groupuomdetails.companyID = srp_erp_company.company_id');
        $this->datatables->where('srp_erp_groupuomdetails.groupUOMMasterID', $groupUOMMasterID);
        //$this->datatables->where('srp_erp_groupchartofaccountdetails.companyGroupID', $grpid);
        $this->datatables->add_column('edit', '<a onclick="delete_uom_link($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'groupUOMDetailID');
        echo $this->datatables->generate();
    }

    function delete_uom_link()
    {
        echo json_encode($this->Group_uom_model->delete_uom_link());
    }

    function save_uom_link()
    {

        $this->form_validation->set_rules('companyIDgrp[]', 'Company', 'trim|required');
        //$this->form_validation->set_rules('UOMMasterID[]', 'UOM', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('e', validation_errors()));
        }
        else
        {
            echo json_encode($this->Group_uom_model->save_uom_link());
        }
    }

    function load_all_companies_uom()
    {
        $company = array();
        $groupUOMMasterID = $this->input->post('groupUOMMasterID');
        $comp = customer_company_link($groupUOMMasterID);
        foreach ($comp as $val)
        {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        $data['groupUOMMasterID'] = $groupUOMMasterID;
        $html = $this->load->view('system/GroupUOM/ajax/erp_load_company_uom', $data, true);
        echo $html;
    }

    function load_uom_header()
    {
        echo json_encode($this->Group_uom_model->load_uom_header());
    }

    function load_all_companies_duplicate()
    {
        $company = array();
        $groupUomID = $this->input->post('groupUomID');
        // $masterAccountYN=$this->input->post('masterAccountYN');
        $data['extra'] = $this->Group_uom_model->fetch_uom_details();
        $comp = customer_company_link($groupUomID);
        foreach ($comp as $val)
        {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        // $data['masterAccountYN']=$masterAccountYN;
        $data['groupUomID'] = $groupUomID;
        $html = $this->load->view('system/GroupUOM/ajax/erp_load_company_uom_duplicate', $data, true);
        echo $html;
    }

    function save_uom_duplicate()
    {
        $this->form_validation->set_rules('checkedCompanies[]', 'Company', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('e', validation_errors()));
        }
        else
        {
            echo json_encode($this->Group_uom_model->save_uom_duplicate());
        }
    }
}
