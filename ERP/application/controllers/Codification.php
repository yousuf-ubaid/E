<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Codification extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Codification_modal');
        $this->load->helpers('codification');
    }

    function load_item_codification_table(){
        $companyid = $this->common_data['company_data']['company_id'];

        $where = "companyID = " . $companyid ."  ";
        $this->datatables->select('attributeID,valueType,masterID,attributeDescription ');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_itemcodificationattributes');
        $this->datatables->add_column('codificatn_action', '$1', 'codifictn_master_action(attributeID,masterID)');
        $this->datatables->add_column('valtyp', '$1', 'codifictn_get_val_type(valueType)');
        echo $this->datatables->generate();
    }

    function save_attribute(){
        $this->form_validation->set_rules('attributeDescription', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Codification_modal->save_attribute());
        }
    }

    function save_attribute_sub(){
        $this->form_validation->set_rules('attributeDescription', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Codification_modal->save_attribute_sub());
        }
    }

    function load_codification_detail_table(){
        $companyid = $this->common_data['company_data']['company_id'];
        $attributeID = $this->input->post('attributeID');

        $where = "companyID = " . $companyid ." AND attributeID = " . $attributeID ." ";
        $this->datatables->select('attributeDetailID,attributeID,comment,masterID,detailDescription ');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_itemcodificationattributedetails');
        $this->datatables->add_column('codificatn_action', '$1', 'codifictn_dtl_action(attributeDetailID,attributeID,masterID)');
        $this->datatables->add_column('mastrdtl', '$1', 'mastr_dtl_val(masterID)');
        echo $this->datatables->generate();
    }

    function load_assignto_drop(){
        echo json_encode($this->Codification_modal->load_assignto_drop());
    }

    function save_attribute_detail(){
        $this->form_validation->set_rules('detailDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('comment', 'Comment', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Codification_modal->save_attribute_detail());
        }
    }

    function load_codification_setup_table(){
        $companyid = $this->common_data['company_data']['company_id'];

        $where = "companyID = " . $companyid ."  ";
        $this->datatables->select('codificationSetupID,noOfElement,description,confirmedYN');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_itemcodificationsetup');
        $this->datatables->add_column('setup_action', '$1', 'codifictn_setup_action(codificationSetupID,confirmedYN)');
        echo $this->datatables->generate();
    }

    function save_setup(){
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('noOfElement', 'No of elements', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Codification_modal->save_setup());
        }
    }

    function load_setup_detail(){
        $data["setupDetail"] = $this->Codification_modal->load_setup_detail();
        $this->load->view('system/item/codification_setup_detail', $data);
    }

    function update_setup_details(){
        echo json_encode($this->Codification_modal->update_setup_details());
    }

    function confirmSetup(){
        echo json_encode($this->Codification_modal->confirmSetup());
    }

    function load_subcat()
    {
        echo json_encode($this->Codification_modal->load_subcat());
    }

    function load_asn_cat_table(){
        $companyid = $this->common_data['company_data']['company_id'];
        $codificationSetupID = $this->input->post('codificationSetupID');

        $where = "sub.companyID = " . $companyid ." AND sub.codificationSetupID = " . $codificationSetupID ." ";
        $this->datatables->select('sub.itemCategoryID as itemCategoryID,sub.codificationSetupID,sub.description as subcategory,mastr.description as mstrcategory,codsetup.description as codsetup');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_itemcategory sub');
        $this->datatables->join('srp_erp_itemcategory mastr', 'mastr.itemCategoryID = sub.masterID', 'left');
        $this->datatables->join('srp_erp_itemcodificationsetup codsetup', 'codsetup.codificationSetupID = sub.codificationSetupID', 'left');
        $this->datatables->add_column('setup_asn_action', '$1', 'codifictn_asn_setup_action(itemCategoryID)');
        echo $this->datatables->generate();
    }

    function save_assigned_setup_detail(){
        $this->form_validation->set_rules('mainCategoryID', 'Main Category', 'trim|required');
        $this->form_validation->set_rules('subcategoryID', 'Sub Category', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Codification_modal->save_assigned_setup_detail());
        }
    }

    function load_codification_tmplat(){
        $data["codesetup"] = $this->Codification_modal->load_codification_tmplat();
        $data["attributeDetailID"] = $this->input->post('attributeDetailID');
        $data["itemAutoID"] = $this->input->post('itemAutoID');

        $this->load->view('system/item/codification_setup_load', $data);
    }


    function save_itemmaster()
    {
        $maincategory = $this->db->query("SELECT itemCategoryID,categoryTypeID FROM srp_erp_itemcategory WHERE itemCategoryID ={$this->input->post('mainCategoryID')}")->row_array();
        $secondaryUOM = getPolicyValues('SUOM', 'All');
        $barcode=$this->input->post('barcode');
        if (!$this->input->post('itemAutoID')) {
            $this->form_validation->set_rules('mainCategoryID', 'Main category', 'trim|required');
            $this->form_validation->set_rules('defaultUnitOfMeasureID', 'Unit of messure', 'trim|required');
            if(!empty($barcode)){
                $barcdexist = $this->db->query("SELECT barcode FROM srp_erp_itemmaster WHERE barcode ='$barcode' AND deletedYN = 0")->row_array();
                if(!empty($barcdexist)){
                    echo json_encode(array('e', 'Barcode already exist'));
                    Exit;
                }
            }

        }else{
            if(!empty($barcode)) {
                $barcdexist = $this->db->query("SELECT barcode FROM srp_erp_itemmaster WHERE barcode ='$barcode' AND itemAutoID !={$this->input->post('itemAutoID')} AND deletedYN = 0")->row_array();
                if (!empty($barcdexist)) {
                    echo json_encode(array('e', 'Barcode already exist'));
                    Exit;
                }
            }
        }
        if ($maincategory['categoryTypeID'] == 3) {
            $this->form_validation->set_rules('COSTGLCODEdes', 'Cost Account', 'trim|required');
            $this->form_validation->set_rules('ACCDEPGLCODEdes', 'Acc Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DEPGLCODEdes', 'Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DISPOGLCODEdes', 'Disposal GL Code', 'trim|required');
        }
        if ($maincategory['categoryTypeID'] == 1) {
            $this->form_validation->set_rules('assteGLAutoID', 'Asset GL Code', 'trim|required');
            $this->form_validation->set_rules('revanueGLAutoID', 'Revenue GL Code', 'trim|required');
            $this->form_validation->set_rules('costGLAutoID', 'Cost GL Code', 'trim|required');
            $this->form_validation->set_rules('stockadjust', 'Stock Adjustment GL Code', 'trim|required');
        }
        if ($maincategory['categoryTypeID'] == 2) {
            //$this->form_validation->set_rules('revanueGLAutoID', 'Revanue GL Code', 'trim|required');
            $this->form_validation->set_rules('costGLAutoID', 'Cost GL Code', 'trim|required');
        }
        if($secondaryUOM==1){
            //$this->form_validation->set_rules('secondaryUOMID', 'Secondary Unit of Measure', 'trim|required');
        }
        $this->form_validation->set_rules('seconeryItemCode', 'Seconery Item Code', 'trim|required');
        $this->form_validation->set_rules('itemName', 'Item Name', 'trim|required');
        $this->form_validation->set_rules('itemDescription', 'Item Full Name', 'trim|required');
        $this->form_validation->set_rules('subcategoryID', 'Sub category', 'trim|required');
        /*        $this->form_validation->set_rules('maximunQty', 'Maximun Qty', 'trim|required');
                $this->form_validation->set_rules('minimumQty', 'Minimum Qty', 'trim|required');
                $this->form_validation->set_rules('reorderPoint', 'Reorder Point', 'trim|required');*/
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));

        } else {
            echo json_encode($this->Codification_modal->save_item_master());
        }
    }

    function item_codification_table_body(){
        $data["codificatntbl"] = $this->Codification_modal->item_codification_table_body();
        $this->load->view('system/item/codification_table_body', $data);
    }

    function editAttributeDetail(){
        echo json_encode($this->Codification_modal->editAttributeDetail());
    }

    function load_sub_codes(){
        $data["subcodeqry"] = $this->Codification_modal->load_sub_codes();
        echo json_encode($data["subcodeqry"]);

        //$this->load->view('system/item/codification_setup_load', $data);
    }

    function load_codification_edit_drp(){
        echo json_encode($this->Codification_modal->load_codification_edit_drp());
    }

}