<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AttributeAssign extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Attribute_assign_modal');
        $this->load->helpers('attribute_assign');
    }

    function fetch_attribute_assign()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "companyID = " . $companyid . "";
        $this->datatables->select("companyAttributeID,isMandatory,isDeleted,srp_erp_systemattributemaster.attributeDescription as attributeDescription");
        $this->datatables->join('srp_erp_systemattributemaster ', 'srp_erp_companyattributeassign.systemAttributeID = srp_erp_systemattributemaster.systemAttributeID');
        $this->datatables->from('srp_erp_companyattributeassign');
        $this->datatables->where($where);
        $this->datatables->add_column('ismandatory', '$1', 'is_mandatory(isMandatory)');
        $this->datatables->add_column('edit', '<span class="pull-right"><a onclick="openAttributeAssignEdit($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> &nbsp;|&nbsp; <a onclick="delete_attribute($1);"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></span>', 'companyAttributeID');
        echo $this->datatables->generate();
    }

    function get_attributes(){
        $data = $this->Attribute_assign_modal->load_attributes();
        $html = $this->load->view('system/attribute_assign/ajax-erp_load_attribute_assign', $data, true);
        echo $html;
    }

    function save_assigned_attributes(){
        echo json_encode($this->Attribute_assign_modal->save_assigned_attributes());
    }

    function get_attributes_edit(){
        $data = $this->Attribute_assign_modal->load_attributes_edit();
        $html = $this->load->view('system/attribute_assign/ajax-erp_load_attribute_assign_edit', $data, true);
        echo $html;
    }

    function update_assigned_attributes(){
        echo json_encode($this->Attribute_assign_modal->update_assigned_attributes());
    }

    function delete_attribute(){
        echo json_encode($this->Attribute_assign_modal->delete_attribute());
    }


}