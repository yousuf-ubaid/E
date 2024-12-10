<?php

class Srp_warehouseMaster extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Srp_warehousemasterModel');
        $this->load->helper('pos');
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
        $this->datatables->select('wareHouseAutoID,companyCode,wareHouseCode,wareHouseDescription,wareHouseLocation,isActive,isDefault')
            ->where('companyID', $this->common_data['company_data']['company_id'])
            ->from('srp_erp_warehousemaster')
            //$this->datatables->join('srp_countrymaster', 'srp_erp_suppliermaster.supplierCountryID = srp_countrymaster.countryID', 'left')
            ->edit_column('status', '$1', 'load_warehouse_status(isActive)')
            ->edit_column('action', '<span class="" onclick="openWarehouseitems($1)"><a href="#" ><span title="Add Warehouse Items" class="glyphicon glyphicon-th"  rel="tooltip"></span></a></span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="" onclick="openBinlocation($1)"><a href="#" ><span title="Add Bin Location" class="glyphicon glyphicon-align-justify"  rel="tooltip"></span></a></span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="" onclick="openwarehousemastermodel($1)"><a href="#" ><span title="Edit" class="glyphicon glyphicon-pencil" rel="tooltip"></span></a></span>', 'wareHouseAutoID')
            ->edit_column('default', '$1', 'loadDefaultWarehousechkbx(wareHouseAutoID,isDefault)');
        echo $this->datatables->generate();
    }

    function save_warehousemaster()
    {
        $glcodeid = $this->input->post('glcodeid');
        $ismanufacturingHN = $this->input->post('ismanufacturingHN');

        $this->form_validation->set_rules('warehousedescription', 'Warehouse Description', 'trim|required');
        $this->form_validation->set_rules('warehouselocation', 'Warehouse Location', 'trim|required');
        if ($ismanufacturingHN == 2) {
            if(empty($glcodeid))
            {
                $this->form_validation->set_rules('manufacturingglcode', 'WIP Gl code', 'trim|required');

            }


        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Srp_warehousemasterModel->save_warehousemaster());
        }
    }

    function edit_warehouse()
    {
        $id = $this->input->post('id');
        if ($id != "") {
            echo json_encode($this->Srp_warehousemasterModel->get_warehouse());
        } else {
            echo json_encode(FALSE);
        }
    }

    function setDefaultWarehouse()
    {
        echo json_encode($this->Srp_warehousemasterModel->setDefaultWarehouse());
    }

    function load_bin_location_table()
    {
        $this->datatables->select('binLocationID,warehouseAutoID,companyID,Description')
            ->where('companyID', $this->common_data['company_data']['company_id'])
            ->where('wareHouseAutoID', $this->input->post('wareHouseAutoID'))
            ->from('srp_erp_warehousebinlocation')
            ->edit_column('action', '<span class="" onclick="edit_bin_location_modal($1,\'$2\')"><a href="#" ><span title="Edit" class="glyphicon glyphicon-pencil" rel="tooltip"></span></a></span> &nbsp; | &nbsp; <span class="" onclick="delete_bin_location($1)"><a href="#" ><span title="Delete" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" rel="tooltip"></span></a></span>', 'binLocationID,Description')
            ->edit_column('default', '$1', 'loadDefaultWarehousechkbx(wareHouseAutoID,isDefault)');
        echo $this->datatables->generate();
    }

    function save_bin_location(){
        $this->form_validation->set_rules("Description", 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            //$this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srp_warehousemasterModel->save_bin_location());
        }
    }

    function delete_bin_location(){
        echo json_encode($this->Srp_warehousemasterModel->delete_bin_location());
    }

    function fetch_items()
    {
        $this->db->SELECT("itemAutoID");
        $this->db->FROM('srp_erp_warehouseitems');
        $this->db->where("wareHouseAutoID", $this->input->post('wareHouseAutoID'));
        $this->db->where("companyID", current_companyID());
        $result = $this->db->get()->result_array();
        $itemAutoID= array();
        foreach($result as $row){
            $itemAutoID[] = $row['itemAutoID'];
        }

        $this->datatables->select('itemAutoID,itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,currentStock,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,companyLocalWacAmount,subcat.description as SubCategoryDescription,subsubcat.description as SubSubCategoryDescription,CONCAT(currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount,CONCAT(itemSystemCode," - ",itemDescription) as description, isSubitemExist', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory subcat', 'srp_erp_itemmaster.subcategoryID = subcat.itemCategoryID')
            ->join('srp_erp_itemcategory subsubcat', 'srp_erp_itemmaster.subSubCategoryID = subsubcat.itemCategoryID','left');
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }
        if (!empty($itemAutoID)) {
            $this->db->where_not_in('itemAutoID', $itemAutoID);
        }

        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->where('srp_erp_itemmaster.isActive', 1);
        $this->datatables->where('srp_erp_itemmaster.deletedYN', 0);
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('TotalWacAmount', '$1  $2', 'format_number(companyLocalWacAmount,2),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        //$this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'itemAutoID');
        $this->datatables->add_column('edit', '<div class="" align="center"> <button class="btn btn-primary btn-xs" onclick="addTempTB(this)" style="font-size:10px"> + Add </button> </div>', 'itemAutoID');


        // $this->datatables->add_column('edit', '<spsn class="pull-right"><input type="checkbox" id="itemchkbox" name="itemchkbox" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked><br><br><a onclick="fetchPage(\'system/item/erp_item_new\',$1)"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_master($1)"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'itemAutoID');


        echo $this->datatables->generate();
    }

    function saveAssignedItems(){
       // exit;
        echo json_encode($this->Srp_warehousemasterModel->saveAssignedItems());
    }

    function fetch_assigned_items(){
        $this->datatables->select('warehouseItemsAutoID,wareHouseAutoID,wareHouseLocation,wareHouseDescription,itemAutoID,itemSystemCode,itemDescription,unitOfMeasureID,unitOfMeasure,currentStock', false)
            ->from('srp_erp_warehouseitems');

        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->where('wareHouseAutoID', $this->input->post('wareHouseAutoID'));
        
        //$this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'itemAutoID');
        echo $this->datatables->generate();
    }

}
