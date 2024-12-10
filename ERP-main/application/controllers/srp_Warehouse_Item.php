<?php
class srp_Warehouse_Item extends ERP_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('srp_warehouse_item_model');
    }

    public function index(){
        $data['title'] = 'Warehouse Item';
        $data['main_content'] = 'srp_wherehouseitem_view';
        $data['extra'] = NULL;
        $this->load->model('srp_warehouse_item_model');
        $this->load->view('includes/template', $data);
    }

    function load_warehouseitemtable(){
        $this->datatables->select('warehouseItemsID,warehouseSystemCode,wareHouseLocation,itemSystemCode,itemPrimaryCode,itemDescription,unitOfMeasure,stockQty')
            ->from('srp_erp_warehouseitems');
            //$this->datatables->join('srp_countrymaster', 'srp_erp_suppliermaster.supplierCountryID = srp_countrymaster.countryID', 'left')
            //->edit_column('action', '<span class="pull-right" onclick="openwarehouseitemmodel($1)"><a href="#" ><span class="glyphicon glyphicon-pencil" style="color:blue;"  rel="tooltip"></span></a></span>', 'warehouseItemsID');

        echo $this->datatables->generate();
    }

    function save_warehouseitem()
    {

        $this->form_validation->set_rules('warehouselocation', 'Warehouse Location', 'trim|required');
       // $this->form_validation->set_rules('itm', 'Select Item', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            //echo "hi";
            echo json_encode($this->srp_warehouse_item_model->save_warehouseitem());
        }
    }


}
