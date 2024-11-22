<?php

class GroupItemMaster extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Group_Item_model');
        $this->load->helpers('group_management');
    }

    function fetch_item()
    {
        $companyID=$this->common_data['company_data']['company_id'];
        $defaultdecimal =  $this->common_data['company_data']['company_default_decimal'];
        $showPurchasePrice = getPolicyValues('SPP', 'All');
        if($showPurchasePrice==' ' || $showPurchasePrice== null || empty($showPurchasePrice)){
            $showPurchasePrice = 0;
        }
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $this->datatables->select('itemAutoID,itemSystemCode,itemName,secondaryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,currentStock,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revenueDescription,costDescription,assetDescription,isActive,companyLocalWacAmount,srp_erp_groupitemcategory.description as SubCategoryDescription,CONCAT(currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount,CONCAT(itemSystemCode," - ",itemDescription) as description, isSubitemExist,companyLocalPurchasingPrice', false)
            ->from('srp_erp_groupitemmaster')
            ->join('srp_erp_groupitemcategory', 'srp_erp_groupitemmaster.subcategoryID = srp_erp_groupitemcategory.itemCategoryID');
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        $this->datatables->where('srp_erp_groupitemmaster.groupID', $companyID);
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('TotalWacAmount', '$1  $2', 'format_number(companyLocalWacAmount,2),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        /*$this->datatables->add_column('img', "<a onclick='change_img(\"$2\",\"$3/$1\")'><img class='img-thumbnail' src='$3/$1' style='width:120px;height: 80px;' ></a>", 'itemImage,itemAutoID,base_url("images/item/")');*/
        $this->datatables->add_column('edit', '$1', 'group_item_edit(itemAutoID,isActive,isSubitemExist)');
        if($showPurchasePrice == 1){
           $this->datatables->add_column('price', '<b>Prch Price: </b>$3 $1 <br><b>Sales Price: </b>$3 $2', 'format_number(companyLocalPurchasingPrice,'.$defaultdecimal.'),number_format(companyLocalSellingPrice,'.$defaultdecimal.'),companyLocalCurrency');
        }else{
            $this->datatables->add_column('price', '<b>Sales Price: </b>$2 $1', 'format_number(companyLocalSellingPrice,'.$defaultdecimal.'),companyLocalCurrency');
        }

        // $this->datatables->add_column('edit', '<spsn class="pull-right"><input type="checkbox" id="itemchkbox" name="itemchkbox" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked><br><br><a onclick="fetchPage(\'system/item/erp_item_new\',$1)"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_master($1)"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'itemAutoID');


        echo $this->datatables->generate();
    }

    function load_subcat()
    {
        echo json_encode($this->Group_Item_model->load_subcat());
    }

    function load_subsubcat()
    {
        echo json_encode($this->Group_Item_model->load_subsubcat());
    }

    function save_itemmaster()
    {
        if (!$this->input->post('itemAutoID')) {
            $this->form_validation->set_rules('mainCategoryID', 'Main category', 'trim|required');
            $this->form_validation->set_rules('defaultUnitOfMeasureID', 'Unit of messure', 'trim|required');
        }

        $this->form_validation->set_rules('seconeryItemCode', 'Seconery Item Code', 'trim|required');
        $this->form_validation->set_rules('itemName', 'Item Name', 'trim|required');
        $this->form_validation->set_rules('itemDescription', 'Item Full Name', 'trim|required');
        $this->form_validation->set_rules('subcategoryID', 'Sub category', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Group_Item_model->save_item_master());
        }
    }

    function load_item_header()
    {
        echo json_encode($this->Group_Item_model->load_item_header());
    }

    function fetch_item_Details(){
        $groupItemMasterID=$this->input->post('groupItemMasterID');

        $this->datatables->select('groupItemDetailID,groupItemMasterID,srp_erp_groupitemmasterdetails.ItemAutoID,srp_erp_groupitemmasterdetails.companyID,companyGroupID,srp_erp_itemmaster.itemSystemCode as itemSystemCode,srp_erp_itemmaster.itemDescription as description,srp_erp_company.company_name as company_name');
        $this->datatables->from('srp_erp_groupitemmasterdetails');
        $this->datatables->join('srp_erp_itemmaster', 'srp_erp_groupitemmasterdetails.ItemAutoID = srp_erp_itemmaster.itemAutoID');
        $this->datatables->join('srp_erp_company', 'srp_erp_groupitemmasterdetails.companyID = srp_erp_company.company_id');
        $this->datatables->where('srp_erp_groupitemmasterdetails.groupItemMasterID', $groupItemMasterID);
        $this->datatables->add_column('edit', '<a onclick="delete_item_link($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'groupItemDetailID');
        echo $this->datatables->generate();
    }

    function load_company()
    {
        $data['groupItemMasterID'] = $this->input->post('groupItemMasterID');
        $html = $this->load->view('system/GroupItemMaster/ajax/ajax-erp_load_item_company', $data, true);
        echo $html;
    }

    function load_item()
    {
        $data['companyID'] = $this->input->post('companyID');
        $data['groupItemMasterID'] = $this->input->post('groupItemMasterID');
        $html = $this->load->view('system/GroupItemMaster/ajax/erp_load_company_items', $data, true);
        echo $html;
    }

    function save_item_link()
    {

        $this->form_validation->set_rules('companyIDgrp[]', 'Company', 'trim|required');
        //$this->form_validation->set_rules('ItemAutoID[]', 'Item', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Group_Item_model->save_item_link());
        }
    }

    function delete_item_link(){
        echo json_encode($this->Group_Item_model->delete_item_link());
    }

    function load_all_companies_items(){
        $company=array();
        $groupItemMasterID=$this->input->post('groupItemMasterID');
        $comp = customer_company_link($groupItemMasterID);
        foreach($comp as $val){
            $company[]=$val['companyID'];
        }
        $data['companyID']=$company;
        $data['groupItemMasterID']=$groupItemMasterID;
        $html = $this->load->view('system/GroupItemMaster/ajax/erp_load_company_items', $data, true);
        echo $html;
    }

    function load_all_companies_duplicate(){
        $company=array();
        $groupItemAutoID=$this->input->post('groupItemAutoID');
        $comp = customer_company_link($groupItemAutoID);
        foreach($comp as $val){
            $company[]=$val['companyID'];
        }
        $data['companyID']=$company;
        $data['groupItemAutoID']=$groupItemAutoID;
        $html = $this->load->view('system/GroupItemMaster/ajax/erp_load_company_item_duplicate', $data, true);
        echo $html;
    }

    function save_item_duplicate()
    {
        $this->form_validation->set_rules('checkedCompanies[]', 'Company', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Group_Item_model->save_item_duplicate());
        }
    }
    function updategroppolicy()
    {
        echo json_encode($this->Group_Item_model->updategroppolicy());
    }





}
